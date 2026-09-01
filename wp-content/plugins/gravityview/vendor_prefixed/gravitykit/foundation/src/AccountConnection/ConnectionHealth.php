<?php

namespace GravityKit\GravityView\Foundation\AccountConnection;

use WP_Error;

/**
 * Classifies the outcome of a signed poll to the account server and persists a per-connection health
 * record so the "Manage Your Kit" badge and page notice can reflect connection trust.
 *
 * Health is deliberately status-like and stored in PLAINTEXT (a sibling option keyed by the connection
 * fingerprint), never inside the encrypted token payload: it must remain readable when the encrypted
 * blob cannot be decrypted (the exact salt-rotation case a connection is meant to recover from).
 *
 * @since 1.26.0
 */
final class ConnectionHealth {
	/**
	 * A verified 2xx poll. Trust intact.
	 */
	const OK = 'ok';

	/**
	 * The server could not be reached (transport error/timeout). Local state keeps working; escalates to a
	 * warning only after repeated failures over a sustained window.
	 */
	const UNREACHABLE = 'unreachable';

	/**
	 * The owner disconnected this site on the account. Trust broken: deactivate now, reconnect to restore.
	 */
	const REVOKED = 'revoked';

	/**
	 * The response could not be verified (signature/decrypt/freshness failure). Trust broken: freeze the
	 * last-verified state and apply nothing from the response.
	 */
	const UNTRUSTED = 'untrusted';

	/**
	 * The site URL drifted from the one the connection was authorized for. Changes are frozen pending a
	 * one-click reconfirm.
	 */
	const NEEDS_RECONFIRM = 'needs_reconfirm';

	/**
	 * WP_Error codes the server returns (via finish_signed()) that map to REVOKED. Kept in sync with the
	 * gk-connect server (Slice 4.6). A revoked connection also surfaces as HTTP 401/403.
	 */
	const SERVER_CODES_REVOKED = [ 'connection_revoked', 'connection_not_found', 'unknown_connection' ];

	/**
	 * WP_Error codes the server returns that map to NEEDS_RECONFIRM (the authorized URL no longer matches).
	 */
	const SERVER_CODES_NEEDS_RECONFIRM = [ 'url_mismatch', 'needs_reconfirmation', 'site_url_drift' ];

	/**
	 * Client-side WP_Error codes emitted by ConnectClient::attempt_signed() for an unverifiable response.
	 */
	const CLIENT_CODES_UNTRUSTED = [ 'gk_connection_bad_signature', 'gk_connection_bad_response' ];

	/**
	 * Verified server rejections of OUR request authentication. The site can no longer sign
	 * acceptable requests — a trust break, not an operational error.
	 *
	 * @since 1.26.0
	 *
	 * @var string[]
	 */
	const SERVER_CODES_UNTRUSTED = [ 'invalid_signature' ];

	/**
	 * Client-side WP_Error codes that mean the request never completed a verified round-trip (transport
	 * failure or an unreconcilable clock). Transient — local state keeps working; retried on the next poll.
	 */
	const CLIENT_CODES_UNREACHABLE = [ 'gk_connection_offline', 'gk_connection_clock_skew' ];

	/**
	 * States that keep the connection trustworthy (local state keeps working). Everything else breaks trust.
	 *
	 * @since 1.26.0
	 *
	 * @var array<string>
	 */
	const AMBER_STATES = [ self::UNREACHABLE, self::NEEDS_RECONFIRM ];

	/**
	 * Plaintext option holding the per-connection health record. Sibling to the connection option and
	 * deliberately unencrypted so health survives a connection-blob decryption failure.
	 */
	const OPTION = 'gk_connection_health';

	/**
	 * Consecutive failed polls before an unreachable server escalates from a transient blip to a warning.
	 */
	const UNREACHABLE_FAIL_THRESHOLD = 3;

	/**
	 * Minimum age (seconds) since the last verified poll before an unreachable server surfaces a warning.
	 * A literal (not DAY_IN_SECONDS) so the class const resolves outside a booted WordPress (unit tests).
	 */
	const UNREACHABLE_MIN_AGE_SECONDS = 86400;

	/**
	 * Classifies a signed-channel result into one of the five health states.
	 *
	 * A non-WP_Error result is a verified 2xx success (see ConnectClient::finish_signed()), so it is OK.
	 * A WP_Error is mapped by its code: transport/clock → UNREACHABLE, unverifiable → UNTRUSTED, and an
	 * explicit revoked / URL-drift code from the server → REVOKED / NEEDS_RECONFIRM.
	 *
	 * Every other authenticated server rejection (an operational error like "at the activation limit" or
	 * "insufficient permission") maps to OK: the response completed a full verified round-trip, so the
	 * connection itself is healthy — only the operation failed. Classifying those as REVOKED would break
	 * trust on an ordinary error. Genuine revocation is surfaced by the server's explicit code (Slice 4.6).
	 *
	 * @since 1.26.0
	 *
	 * @param array|WP_Error $result Result from ConnectClient::signed_request().
	 *
	 * @return string One of the state constants.
	 */
	public static function classify( $result ): string {
		if ( ! is_wp_error( $result ) ) {
			return self::OK;
		}

		$code = $result->get_error_code();

		if ( in_array( $code, self::CLIENT_CODES_UNREACHABLE, true ) ) {
			return self::UNREACHABLE;
		}

		if ( in_array( $code, self::CLIENT_CODES_UNTRUSTED, true ) || in_array( $code, self::SERVER_CODES_UNTRUSTED, true ) ) {
			return self::UNTRUSTED;
		}

		if ( in_array( $code, self::SERVER_CODES_NEEDS_RECONFIRM, true ) ) {
			return self::NEEDS_RECONFIRM;
		}

		if ( in_array( $code, self::SERVER_CODES_REVOKED, true ) ) {
			return self::REVOKED;
		}

		// A verified 401 means the server no longer recognizes this connection: revoked, even without an
		// explicit code. 403 is left to the explicit codes above — it also covers live-connection scope errors.
		$data   = $result->get_error_data();
		$status = is_array( $data ) ? (int) ( $data['status'] ?? 0 ) : 0;

		if ( 401 === $status ) {
			return self::REVOKED;
		}

		// Any other authenticated, fully-verified rejection is an operational error: the connection is healthy.
		return self::OK;
	}

	/**
	 * Whether a state keeps the connection trustworthy (local state keeps working).
	 *
	 * @since 1.26.0
	 *
	 * @param string $state A state constant.
	 *
	 * @return bool
	 */
	public static function is_trustworthy( string $state ): bool {
		return self::OK === $state || in_array( $state, self::AMBER_STATES, true );
	}

	/**
	 * Records the outcome of one signed poll into the per-connection health record.
	 *
	 * OK clears the failure streak and stamps the last-verified time. UNREACHABLE increments the streak
	 * (stamping the first failure) but does not touch last-verified, so the "sustained failure" window can
	 * be measured. Trust-breaking states freeze immediately. A fingerprint change (reconnect/account
	 * switch) resets the record so a prior connection's failures never haunt a fresh one.
	 *
	 * @since 1.26.0
	 *
	 * @param array  $connection Stored connection (needs `fingerprint` and `scope`).
	 * @param string $state      A state constant from classify().
	 *
	 * @return void
	 */
	public static function record( array $connection, string $state ): void {
		$fingerprint = (string) ( $connection['fingerprint'] ?? '' );

		if ( '' === $fingerprint ) {
			return;
		}

		$is_network = TokenStore::SCOPE_NETWORK === ( $connection['scope'] ?? '' );
		$stored     = self::read( $is_network );
		$record     = $stored;

		if ( ( $record['fingerprint'] ?? '' ) !== $fingerprint ) {
			$record = [
				'fingerprint'      => $fingerprint,
				'state'            => self::OK,
				'fail_count'       => 0,
				'first_failed_at'  => 0,
				'last_verified_at' => 0,
			];
		}

		$now   = time();
		$prior = (string) ( $record['state'] ?? self::OK );

		if ( self::OK === $state ) {
			$record['state']            = self::OK;
			$record['fail_count']       = 0;
			$record['first_failed_at']  = 0;
			$record['last_verified_at'] = $now;
		} elseif ( self::UNREACHABLE === $state ) {
			// Unreachable only progresses from a healthy/unreachable state; it never downgrades a
			// trust-broken or reconfirm-frozen state — only a verified OK clears those.
			if ( self::OK === $prior || self::UNREACHABLE === $prior ) {
				$record['state'] = self::UNREACHABLE;
			}

			$record['fail_count'] = (int) ( $record['fail_count'] ?? 0 ) + 1;

			if ( empty( $record['first_failed_at'] ) ) {
				$record['first_failed_at'] = $now;
			}
		} else {
			$record['state'] = $state;
		}

		if ( $record !== $stored ) {
			self::write( $is_network, $record );
		}
	}

	/**
	 * Returns the effective health for the frontend: the recorded state, except a not-yet-sustained
	 * unreachable server reads as OK (a single failed poll must not alarm the user while local state works).
	 *
	 * @since 1.26.0
	 *
	 * @param array $connection Stored connection (needs `fingerprint` and `scope`).
	 *
	 * @return array{state:string, fail_count:int, first_failed_at:int, last_verified_at:int}
	 */
	public static function effective( array $connection ): array {
		$defaults = [
			'state'            => self::OK,
			'fail_count'       => 0,
			'first_failed_at'  => 0,
			'last_verified_at' => 0,
		];

		$fingerprint = (string) ( $connection['fingerprint'] ?? '' );
		$is_network  = TokenStore::SCOPE_NETWORK === ( $connection['scope'] ?? '' );
		$record      = array_merge( $defaults, self::read( $is_network ) );

		if ( '' === $fingerprint || ( $record['fingerprint'] ?? '' ) !== $fingerprint ) {
			return $defaults;
		}

		$state = (string) $record['state'];

		if ( self::UNREACHABLE === $state ) {
			// Measure the outage from the last verified poll, or — when the connection has never verified
			// one (fresh reconnect straight into an outage) — from the first failure, so it still escalates.
			$window_start = (int) $record['last_verified_at'] > 0
				? (int) $record['last_verified_at']
				: (int) $record['first_failed_at'];

			$sustained = (int) $record['fail_count'] >= self::UNREACHABLE_FAIL_THRESHOLD
				&& $window_start > 0
				&& ( time() - $window_start ) >= self::UNREACHABLE_MIN_AGE_SECONDS;

			if ( ! $sustained ) {
				$state = self::OK;
			}
		}

		return [
			'state'            => $state,
			'fail_count'       => (int) $record['fail_count'],
			'first_failed_at'  => (int) $record['first_failed_at'],
			'last_verified_at' => (int) $record['last_verified_at'],
		];
	}

	/**
	 * Reads the raw health record from the scope-appropriate option.
	 *
	 * @since 1.26.0
	 *
	 * @param bool $is_network Whether the connection is network-scoped.
	 *
	 * @return array
	 */
	private static function read( bool $is_network ): array {
		$stored = $is_network ? get_site_option( self::OPTION, [] ) : get_option( self::OPTION, [] );

		return is_array( $stored ) ? $stored : [];
	}

	/**
	 * Writes the raw health record to the scope-appropriate option.
	 *
	 * @since 1.26.0
	 *
	 * @param bool  $is_network Whether the connection is network-scoped.
	 * @param array $record     The health record to persist.
	 *
	 * @return void
	 */
	private static function write( bool $is_network, array $record ): void {
		if ( $is_network ) {
			update_site_option( self::OPTION, $record );

			return;
		}

		update_option( self::OPTION, $record, false );
	}

	/**
	 * Deletes the disconnected connection's scope-specific health record so it does not outlive the connection.
	 *
	 * @since 1.26.0
	 *
	 * @param array $connection Stored connection whose health record to drop (its `scope` selects the slot).
	 *
	 * @return void
	 */
	public static function forget( array $connection ): void {
		// Scope-targeted, matching read()/write(): forget only the disconnected connection's health record, so
		// disconnecting a site connection never clears the surviving network connection's frozen state (or vice
		// versa), dropping its warning/write-freeze until the next poll.
		$is_network = TokenStore::SCOPE_NETWORK === ( $connection['scope'] ?? '' );

		if ( $is_network ) {
			delete_site_option( self::OPTION );

			return;
		}

		delete_option( self::OPTION );
	}
}
