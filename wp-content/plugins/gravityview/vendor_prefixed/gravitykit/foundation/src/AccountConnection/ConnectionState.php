<?php

namespace GravityKit\GravityView\Foundation\AccountConnection;

/**
 * Read-only view of the current account connection for consumer UIs.
 *
 * @since 1.26.0
 */
final class ConnectionState {
	/**
	 * Class instance.
	 *
	 * @since 1.26.0
	 *
	 * @var ConnectionState|null
	 */
	private static $_instance = null;

	/**
	 * Connection store.
	 *
	 * @since 1.26.0
	 *
	 * @var TokenStore
	 */
	private $store;

	/**
	 * @since 1.26.0
	 *
	 * @param TokenStore|null $store Connection store. Defaults to a new TokenStore.
	 */
	public function __construct(?TokenStore $store = null ) {
		$this->store = $store ?? new TokenStore();
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.26.0
	 *
	 * @return ConnectionState
	 */
	public static function get_instance(): ConnectionState {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Whether the current connection warrants surfacing to the user as needing attention. Only states a
	 * consumer can explain in its UI count: revoked, untrusted, escalated-unreachable, and decrypt-failed.
	 * Disconnected and needs-reconfirm (URL-drift surface + freeze: Slice 4.6) are excluded.
	 *
	 * @since 1.26.0
	 *
	 * @return bool
	 */
	public function needs_attention(): bool {
		$connection = $this->store->get();

		if ( null === $connection ) {
			return false;
		}

		if ( ! empty( $connection['decrypt_failed'] ) ) {
			return true;
		}

		$surfaced = [ ConnectionHealth::REVOKED, ConnectionHealth::UNTRUSTED, ConnectionHealth::UNREACHABLE ];

		return in_array( ConnectionHealth::effective( $connection )['state'], $surfaced, true );
	}

	/**
	 * Returns the connection state shaped for frontend consumption.
	 *
	 * @since 1.26.0
	 *
	 * @return array{connected:bool, account_email:string, needs_reconfirmation:bool, decrypt_failed:bool, connection_health:string, scopes?:list<string>}
	 */
	public function for_frontend(): array {
		$connection = $this->store->get();

		if ( null === $connection ) {
			return [
				'connected'            => false,
				'account_email'        => '',
				'needs_reconfirmation' => false,
				'decrypt_failed'       => false,
				'connection_health'    => ConnectionHealth::OK,
			];
		}

		// A user who cannot act on this connection's scope does not see it at all: a subsite manages only its
		// own connection, never the network one. Report it as disconnected so the network owner's email and the
		// connection's very existence are not disclosed to a subsite admin (e.g. via a spoofed AJAX referer).
		if ( ! $this->user_can_manage_scope( $connection ) ) {
			return [
				'connected'            => false,
				'account_email'        => '',
				'needs_reconfirmation' => false,
				'decrypt_failed'       => false,
				'connection_health'    => ConnectionHealth::OK,
			];
		}

		if ( ! empty( $connection['decrypt_failed'] ) ) {
			// The connection key itself cannot be decrypted (salt rotation): the decrypt_failed flag is the
			// authoritative "reconnect to restore" signal, so do not surface a possibly-stale poll health.
			return [
				'connected'            => true,
				'account_email'        => '',
				'needs_reconfirmation' => false,
				'decrypt_failed'       => true,
				'connection_health'    => ConnectionHealth::OK,
			];
		}

		return [
			'connected'            => true,
			'account_email'        => (string) ( $connection['account_email'] ?? '' ),
			'needs_reconfirmation' => false,
			'decrypt_failed'       => false,
			'connection_health'    => ConnectionHealth::effective( $connection )['state'],
			'scopes'               => array_values( array_map( 'strval', (array) ( $connection['scopes'] ?? [] ) ) ),
		];
	}

	/**
	 * Whether the current user may act on the given connection's scope. A network-scoped connection requires
	 * manage_network_options; site-scoped connections and single-site installs impose no extra requirement.
	 *
	 * @since 1.26.0
	 *
	 * @param array $connection Stored connection.
	 *
	 * @return bool
	 */
	private function user_can_manage_scope( array $connection ): bool {
		if ( ! is_multisite() || TokenStore::SCOPE_NETWORK !== ( $connection['scope'] ?? '' ) ) {
			return true;
		}

		return current_user_can( 'manage_network_options' );
	}
}
