<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\AccountConnection;

use GravityKit\GravityEdit\Foundation\Encryption\Encryption;
use GravityKit\GravityEdit\Foundation\Helpers\Core as CoreHelpers;

/**
 * Persists the account connection at rest, encrypting the site's Ed25519 secret
 * key and mirroring the network/site scope split used by consumer modules' storage.
 *
 * @since 1.26.0
 */
final class TokenStore {
	/**
	 * Option name holding the connection payload.
	 *
	 * @since 1.26.0
	 */
	public const OPTION = 'gk_connection';

	/**
	 * Stored payload schema version.
	 *
	 * @since 1.26.0
	 */
	public const SCHEMA_VERSION = 1;

	/**
	 * Network-wide storage scope (super admin connection).
	 *
	 * @since 1.26.0
	 */
	public const SCOPE_NETWORK = 'network';

	/**
	 * Per-site storage scope.
	 *
	 * @since 1.26.0
	 */
	public const SCOPE_SITE = 'site';

	/**
	 * Fixed read/wipe scope, or null to resolve it from the current admin context. Network admin resolves to
	 * the network connection; every other context to the current blog's own site connection.
	 *
	 * @since 1.26.0
	 *
	 * @var string|null
	 */
	private $scope;

	/**
	 * @since 1.26.0
	 *
	 * @param string|null $scope Force reads/wipes to this scope ('network' or 'site'), or null for context.
	 */
	public function __construct( ?string $scope = null ) {
		$this->scope = ( self::SCOPE_NETWORK === $scope || self::SCOPE_SITE === $scope ) ? $scope : null;
	}

	/**
	 * Reads the network connection, regardless of the current admin context.
	 *
	 * @since 1.26.0
	 *
	 * @return array|null
	 */
	public static function get_network(): ?array {
		return ( new self( self::SCOPE_NETWORK ) )->get();
	}

	/**
	 * Reads the current blog's own site connection, regardless of the current admin context.
	 *
	 * @since 1.26.0
	 *
	 * @return array|null
	 */
	public static function get_site(): ?array {
		return ( new self( self::SCOPE_SITE ) )->get();
	}

	/**
	 * Returns the scope this store reads and wipes.
	 *
	 * @since 1.26.0
	 *
	 * @return string
	 */
	public function scope(): string {
		return $this->effective_scope();
	}

	/**
	 * The scope this instance reads and wipes: the forced scope, or — for a context store — the network
	 * connection in network admin and the current blog's own site connection everywhere else. Uses the
	 * AJAX-aware network-admin check, since core is_network_admin() is always false during admin-ajax and
	 * would misroute every connection AJAX to the site scope. Single-site collapses both option scopes onto
	 * one option, so the resolved value is immaterial there.
	 *
	 * @since 1.26.0
	 *
	 * @return string
	 */
	private function effective_scope(): string {
		if ( null !== $this->scope ) {
			return $this->scope;
		}

		return ( is_multisite() && CoreHelpers::is_network_admin() ) ? self::SCOPE_NETWORK : self::SCOPE_SITE;
	}

	/**
	 * Encrypts and stores a connection.
	 *
	 * @since 1.26.0
	 *
	 * @param array $connection Connection data. Requires `connection_id` and `private_key`.
	 *
	 * @return bool
	 */
	public function save( array $connection ): bool {
		$connection_id = (string) ( $connection['connection_id'] ?? '' );
		$private_key   = (string) ( $connection['private_key'] ?? '' );

		if ( '' === $connection_id || '' === $private_key ) {
			return false;
		}

		$encrypted = Encryption::get_instance()->encrypt( $private_key );

		if ( false === $encrypted ) {
			return false;
		}

		$scope = $this->normalize_scope( $connection['scope'] ?? '' );

		$stored = $connection;
		unset( $stored['private_key'] );

		$stored['private_key_enc'] = $encrypted;
		$stored['fingerprint']     = hash( 'sha256', $connection_id );
		$stored['schema_version']  = self::SCHEMA_VERSION;
		$stored['scope']           = $scope;

		return $this->write( $scope, $stored );
	}

	/**
	 * Returns the stored connection with the private key decrypted, `null` when
	 * absent, or a decrypt-failure marker (with fingerprint) when the ciphertext
	 * cannot be decrypted.
	 *
	 * @since 1.26.0
	 *
	 * @return array|null
	 */
	public function get(): ?array {
		$stored = $this->read();

		if ( ! is_array( $stored ) || empty( $stored ) ) {
			return null;
		}

		$encrypted = (string) ( $stored['private_key_enc'] ?? '' );
		$decrypted = '' === $encrypted ? null : Encryption::get_instance()->decrypt( $encrypted );

		if ( null === $decrypted ) {
			return [
				'decrypt_failed' => true,
				'fingerprint'    => $stored['fingerprint'] ?? null,
				'scope'          => $stored['scope'] ?? null,
				'connection_id'  => $stored['connection_id'] ?? null,
			];
		}

		$connection = $stored;
		unset( $connection['private_key_enc'] );

		$connection['private_key'] = $decrypted;

		return $connection;
	}

	/**
	 * Deletes the connection this store resolves to (the forced scope, or the context scope).
	 *
	 * @since 1.26.0
	 *
	 * @return bool
	 */
	public function wipe(): bool {
		// Scope-targeted: disconnect only the connection this store resolves to, so a subsite disconnect never
		// deletes the network connection (and vice versa), and a shadowed connection is never wiped un-revoked.
		return self::SCOPE_NETWORK === $this->effective_scope()
			? delete_site_option( self::OPTION )
			: delete_option( self::OPTION );
	}

	/**
	 * Returns the plaintext connection-id fingerprint, or `null` when absent.
	 *
	 * @since 1.26.0
	 *
	 * @return string|null
	 */
	public function fingerprint(): ?string {
		$stored = $this->read();

		if ( ! is_array( $stored ) ) {
			return null;
		}

		$fingerprint = $stored['fingerprint'] ?? null;

		return is_string( $fingerprint ) ? $fingerprint : null;
	}

	/**
	 * Writes the payload to the option matching the scope. On single-site both
	 * scopes collapse to the same option, matching consumer modules' storage.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope   Storage scope.
	 * @param array  $payload Payload to persist.
	 *
	 * @return bool
	 */
	private function write( string $scope, array $payload ): bool {
		if ( self::SCOPE_SITE === $scope ) {
			return update_option( self::OPTION, $payload, false );
		}

		return update_site_option( self::OPTION, $payload );
	}

	/**
	 * Reads the raw stored payload from the option matching this store's effective scope.
	 *
	 * @since 1.26.0
	 *
	 * @return array|null
	 */
	private function read(): ?array {
		$stored = self::SCOPE_NETWORK === $this->effective_scope()
			? get_site_option( self::OPTION )
			: get_option( self::OPTION );

		return is_array( $stored ) && ! empty( $stored ) ? $stored : null;
	}

	/**
	 * Normalizes an arbitrary scope value to a known scope, defaulting to site. The default must fail closed:
	 * a missing/typo'd scope must never silently widen a connection to network-wide (sitemeta) storage where
	 * every subsite would treat it as network-scoped. Only an explicit network scope resolves to network.
	 *
	 * @since 1.26.0
	 *
	 * @param mixed $scope Raw scope value.
	 *
	 * @return string
	 */
	private function normalize_scope( $scope ): string {
		return self::SCOPE_NETWORK === $scope ? self::SCOPE_NETWORK : self::SCOPE_SITE;
	}
}
