<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\AccountConnection;

use Exception;
use GravityKit\GravityImport\Foundation\Helpers\Core as CoreHelpers;
use WP_Error;

/**
 * Wires the connection kernel into WordPress: AJAX routes and the hidden
 * return-leg admin page. Domain-blind — consumers register their own routes on
 * the shared router and shape presentation through the kernel's seam filters.
 * Hooks-only at init so the per-request cost stays at zero.
 *
 * @since 1.26.0
 */
final class Framework {
	/**
	 * AJAX router grouping this module's routes.
	 *
	 * @since 1.26.0
	 */
	const AJAX_ROUTER = 'account_connection';

	/**
	 * Hidden admin page slug handling the OAuth return leg.
	 *
	 * @since 1.26.0
	 */
	const RETURN_PAGE = 'gk_foundation_connect_return';

	/**
	 * Class instance.
	 *
	 * @since 1.26.0
	 *
	 * @var Framework|null
	 */
	private static $_instance = null;

	/**
	 * Connection client.
	 *
	 * @since 1.26.0
	 *
	 * @var ConnectClient|null
	 */
	private $_client = null;

	/**
	 * Framework constructor.
	 *
	 * @since 1.26.0
	 */
	private function __construct() {
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.26.0
	 *
	 * @return Framework
	 */
	public static function get_instance(): Framework {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Registers the module's hooks. Hooks-only: no disk/parse-heavy work.
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public function init() {
		if ( did_action( 'gk/foundation/connection/initialized' ) ) {
			return;
		}

		add_filter( 'gk/foundation/ajax/' . self::AJAX_ROUTER . '/routes', [ $this, 'configure_ajax_routes' ] );

		add_action( 'admin_menu', [ $this, 'register_return_page' ] );
		add_action( 'network_admin_menu', [ $this, 'register_return_page' ] );

		// The return leg must exchange and redirect BEFORE the admin header is sent, so it runs on
		// admin_init rather than in the page-content callback (where wp_safe_redirect is too late).
		add_action( 'admin_init', [ $this, 'maybe_process_return' ] );

		// Reserve the network-connection marker path so no real subsite can be created at it and collide with
		// the network connection's synthetic URL.
		if ( is_multisite() ) {
			add_action( 'wp_validate_site_data', [ $this, 'reserve_network_marker_path' ], 10, 2 );
		}

		/**
		 * Fires when the account-connection module has finished initializing.
		 *
		 * @since 1.26.0
		 *
		 * @param array $context Reserved for future data. Always an array so bundled prefixed
		 *                       Foundation copies never share a class instance across this boundary.
		 */
		do_action( 'gk/foundation/connection/initialized', [] );
	}

	/**
	 * Configures Ajax routes handled by this class.
	 *
	 * @since 1.26.0
	 *
	 * @param array $routes Ajax route to class method map.
	 *
	 * @return array
	 */
	public function configure_ajax_routes( array $routes ) {
		return array_merge(
			$routes,
			[
				'connect_begin'        => [ $this, 'ajax_connect_begin' ],
				'get_connection_state' => [ $this, 'ajax_get_connection_state' ],
				'disconnect'           => [ $this, 'ajax_disconnect' ],
			]
		);
	}

	/**
	 * Ajax request to begin the connection handshake.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{authorize_url:string}
	 */
	public function ajax_connect_begin( array $payload ) {
		$this->require_capability( 'begin' );

		// begin() overwrites the per-blog pending slot, so a caller who cannot manage a network connection must
		// not clobber a super admin's in-flight network handshake. Only a genuinely live pending blocks — an
		// expired, missing, or future-dated record is free to replace (mirrors exchange()'s validity check).
		$pending      = get_option( ConnectClient::PENDING_OPTION );
		$created_at   = is_array( $pending ) ? (int) ( $pending['created_at'] ?? 0 ) : 0;
		$now          = time();
		$pending_live = $created_at > 0 && $created_at <= $now && ( $now - $created_at ) < ConnectClient::PENDING_TTL;

		if ( $pending_live
			&& TokenStore::SCOPE_NETWORK === ( $pending['scope'] ?? '' )
			&& ! current_user_can( 'manage_network_options' )
		) {
			throw new Exception( __( 'A network-wide connection is already in progress.', 'gk-foundation' ) );
		}

		$scope = $this->resolve_connection_scope( $payload );

		if ( TokenStore::SCOPE_NETWORK === $scope && ! is_main_site() ) {
			throw new Exception( __( 'A network-wide connection must be started from the main site.', 'gk-foundation' ) );
		}

		// A real subsite sitting on the marker path would make the network connection's synthetic URL point at a
		// live site, so refuse the handshake until it is removed rather than advertise a colliding URL.
		if ( TokenStore::SCOPE_NETWORK === $scope && $this->marker_path_taken() ) {
			throw new Exception( __( 'A subsite already occupies the path reserved for the network-wide connection. Remove or rename that site before connecting network-wide.', 'gk-foundation' ) );
		}

		// Symmetric guard: a subsite whose own home URL equals the network connection's synthetic marker URL would
		// derive the same storage owner_key as the network connection, so refuse its site handshake until renamed.
		if ( TokenStore::SCOPE_SITE === $scope && is_multisite() && ConnectClient::connection_url( TokenStore::SCOPE_SITE ) === ConnectClient::connection_url( TokenStore::SCOPE_NETWORK ) ) {
			throw new Exception( __( 'This site occupies the path reserved for the network-wide connection. Rename it before connecting.', 'gk-foundation' ) );
		}

		// Refuse a second HEALTHY connection in the target scope's slot (the endpoint must guard even though the
		// UI hides Connect when connected). A broken connection — key lost after a salt rotation, revoked, or
		// untrusted — stays reconnectable: Reconnect calls begin() directly without wiping first, so it must
		// replace the dead connection rather than hit "already connected".
		$existing = TokenStore::SCOPE_NETWORK === $scope ? TokenStore::get_network() : TokenStore::get_site();

		if ( is_array( $existing ) && ! empty( $existing['connection_id'] ) && ! $this->connection_is_broken( $existing ) ) {
			throw new Exception( __( 'This site is already connected to a GravityKit.com account.', 'gk-foundation' ) );
		}

		/**
		 * Filters the scopes a new connection handshake requests. Consumers append their domains.
		 *
		 * @since 1.26.0
		 *
		 * @param array<string> $scopes Requested scopes. Default: none.
		 */
		$scopes = (array) apply_filters( 'gk/foundation/connection/requested-scopes', [] );

		$result = $this->scoped_client( $scope )->begin( $this->return_url( $scope ), $scopes, $scope );

		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message() );
		}

		return [ 'authorize_url' => $result['authorize_url'] ];
	}

	/**
	 * Resolves the connection scope from a client-supplied hint, gated by capability.
	 *
	 * The `network` hint is honored only for a multisite user who can `manage_network_options`; every other
	 * case downgrades to `site`. The hint is never an escalation vector — capability is the authority.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @return string 'network' or 'site'.
	 */
	public function resolve_connection_scope( array $payload ): string {
		$requested = (string) ( $payload['scope'] ?? '' );

		if ( TokenStore::SCOPE_NETWORK !== $requested ) {
			return TokenStore::SCOPE_SITE;
		}

		if ( ! is_multisite() || ! current_user_can( 'manage_network_options' ) ) {
			return TokenStore::SCOPE_SITE;
		}

		return TokenStore::SCOPE_NETWORK;
	}

	/**
	 * Whether a stored connection can no longer sync — its key was lost after a salt rotation, or its effective
	 * health is revoked or untrusted — and so may be replaced by a fresh Reconnect handshake.
	 *
	 * @since 1.26.0
	 *
	 * @param array $connection Stored connection.
	 *
	 * @return bool
	 */
	private function connection_is_broken( array $connection ): bool {
		if ( ! empty( $connection['decrypt_failed'] ) ) {
			return true;
		}

		$state = ConnectionHealth::effective( $connection )['state'];

		return ConnectionHealth::REVOKED === $state || ConnectionHealth::UNTRUSTED === $state;
	}

	/**
	 * Whether a real subsite already occupies the reserved network-connection marker path on this network.
	 *
	 * @since 1.26.0
	 *
	 * @return bool
	 */
	private function marker_path_taken(): bool {
		if ( ! is_multisite() ) {
			return false;
		}

		$network = get_network();

		if ( ! $network ) {
			return false;
		}

		$marker_path = trailingslashit( $network->path ) . trim( ConnectClient::NETWORK_URL_MARKER, '/' ) . '/';

		$sites = get_sites(
			[
				'domain'     => $network->domain,
				'path'       => $marker_path,
				'network_id' => (int) $network->id,
				'number'     => 1,
				'fields'     => 'ids',
			]
		);

		return ! empty( $sites );
	}

	/**
	 * Rejects creating or updating a subsite whose last path segment is the reserved marker.
	 *
	 * @since 1.26.0
	 *
	 * @param WP_Error $errors Accumulating site-data validation errors.
	 * @param array    $data   Site data being validated.
	 *
	 * @return void
	 */
	public function reserve_network_marker_path( WP_Error $errors, array $data ): void {
		// Filter only empty-string segments so a legitimate "0" path segment is preserved.
		$segments = array_values(
			array_filter(
				explode( '/', (string) ( $data['path'] ?? '' ) ),
				static function ( $segment ) {
					return '' !== $segment;
				}
			)
		);
		$last     = end( $segments );

		if ( ! is_string( $last ) || trim( ConnectClient::NETWORK_URL_MARKER, '/' ) !== $last ) {
			return;
		}

		$errors->add(
			'gk_reserved_network_path',
			esc_html__( 'This site path is reserved by GravityKit and cannot be used.', 'gk-foundation' )
		);
	}

	/**
	 * Ajax request to read the current connection state.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function ajax_get_connection_state( array $payload ) {
		$this->require_capability( 'state' );

		$scope = $this->resolve_connection_scope( $payload );

		return ( new ConnectionState( new TokenStore( $scope ) ) )->for_frontend();
	}

	/**
	 * Lists the active account connections the current user may manage — one entry per scope.
	 *
	 * The public entry point for products that manage per-connection account storage: enumerate the
	 * connections, then pass each returned `scope` to {@see GravityKitFoundation::account_storage()}
	 * calls (`[ 'scope' => $scope ]`). On multisite this can be BOTH the network connection and the
	 * (main) site connection; on single-site it is the one site connection. Scopes the current user
	 * cannot manage (a subsite admin against a network-scoped connection) are omitted, matching the
	 * connection chip. A `decrypt_failed` connection is listed but cannot serve storage (its key is
	 * unusable until reconnect); `storage` reflects whether the storage permission was granted.
	 *
	 * @since 1.26.0
	 *
	 * @return array<int,array{scope:string, account_email:string, home_url:string, connection_health:string, decrypt_failed:bool, storage:bool}>
	 */
	public function connections(): array {
		$scopes = is_multisite()
			? [ TokenStore::SCOPE_NETWORK, TokenStore::SCOPE_SITE ]
			: [ TokenStore::SCOPE_SITE ];

		$connections = [];

		foreach ( $scopes as $scope ) {
			$store = new TokenStore( $scope );
			$state = ( new ConnectionState( $store ) )->for_frontend();

			if ( empty( $state['connected'] ) ) {
				continue;
			}

			$raw = $store->get();

			$connections[] = [
				'scope'             => $scope,
				'account_email'     => (string) $state['account_email'],
				'home_url'          => is_array( $raw ) ? (string) ( $raw['home_url'] ?? '' ) : '',
				'connection_health' => (string) $state['connection_health'],
				'decrypt_failed'    => ! empty( $state['decrypt_failed'] ),
				'storage'           => in_array( 'storage', (array) ( $state['scopes'] ?? [] ), true ),
			];
		}

		return $connections;
	}

	/**
	 * Ajax request to disconnect.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{disconnected:bool, revoked_remotely:bool, remote_confirmed:bool, remote_failures:int}
	 */
	public function ajax_disconnect( array $payload ) {
		$this->require_capability( 'disconnect' );

		$scope = $this->resolve_connection_scope( $payload );

		// Disconnect revokes and wipes only the payload-resolved connection scope.
		return $this->scoped_client( $scope )->disconnect();
	}

	/**
	 * Registers the hidden return-leg admin page.
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public function register_return_page() {
		// Parent slug '' hides the page from every menu while keeping admin.php?page=... reachable. `read` is
		// routing plumbing only (the injected connect permission is a Foundation key, not a WP capability); it
		// stays no stricter than the connect-capability gate in render_return_page(), which is the real authority.
		add_submenu_page(
			'',
			'',
			'',
			'read',
			self::RETURN_PAGE,
			[ $this, 'render_return_page' ]
		);
	}

	/**
	 * Handles the return leg on admin_init: exchanges the code and redirects to Manage Your Kit
	 * before any admin output, so wp_safe_redirect is not defeated by already-sent headers.
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public function maybe_process_return() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! is_admin() || self::RETURN_PAGE !== ( $_GET['page'] ?? '' ) ) {
			return;
		}

		if ( ! $this->current_user_can( 'return' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'gk-foundation' ), '', [ 'response' => 403 ] );
		}

		// The return leg can be completed by a different user than the initiator (a re-login mints a new
		// session), so re-check the network capability the scope was gated on at begin() before touching a
		// network-wide pending record — completing OR cancelling. Otherwise a site-only admin could finalize
		// it, or delete a super admin's in-flight network handshake by hitting the return URL with no code.
		$pending = get_option( ConnectClient::PENDING_OPTION );

		if ( is_array( $pending ) && TokenStore::SCOPE_NETWORK === ( $pending['scope'] ?? '' ) && ! current_user_can( 'manage_network_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to complete a network-wide connection.', 'gk-foundation' ), '', [ 'response' => 403 ] );
		}

		// The OAuth state (validated via hash_equals + single-use) is the CSRF guard here; a WP nonce
		// cannot survive the re-login this return leg can trigger. The genuine denial redirect carries
		// state inside its redirect_uri, so state is present on the cancel path too.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$code      = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
		$state     = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '';
		$cancelled = 'denied' === ( $_GET['gk_connect'] ?? '' ) || '' === $code;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// Cancelled on the consent screen: surface a neutral "cancelled" notice instead of running a
		// doomed exchange. Consume the pending only when the state proves ownership — a forged denial
		// callback (attacker URL opened by an admin) must not delete a legitimate in-flight handshake.
		if ( $cancelled ) {
			$this->client()->delete_pending_if_owned( $state );

			wp_safe_redirect( add_query_arg( 'gk_connect', 'denied', $this->redirect_url( '', 'denied' ) ) );

			exit;
		}

		$result = $this->client()->exchange( $code, $state );

		$scope = is_wp_error( $result ) ? '' : (string) ( $result['connection']['scope'] ?? '' );

		$status = $this->connect_status_from_result( $result );

		$redirect = add_query_arg( 'gk_connect', $status, $this->redirect_url( $scope, $status ) );

		wp_safe_redirect( $redirect );

		exit;
	}

	/**
	 * Fallback content for the hidden return page. The exchange + redirect happens on admin_init
	 * (see maybe_process_return), so this only renders if that redirect did not occur.
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public function render_return_page() {
		if ( ! $this->current_user_can( 'return' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'gk-foundation' ), '', [ 'response' => 403 ] );
		}

		echo '<div class="wrap"><p>' . esc_html__( 'Completing your connection…', 'gk-foundation' ) . '</p></div>';
	}

	/**
	 * Maps an exchange result to a `gk_connect` status.
	 *
	 * @since 1.26.0
	 *
	 * @param array|\WP_Error $result Exchange result.
	 *
	 * @return string
	 */
	private function connect_status_from_result( $result ): string {
		if ( ! is_wp_error( $result ) ) {
			// A requested cross-account storage disposition (transfer/destroy) that did not apply must
			// not be reported as a clean success: the connection stands, but the previously stored data
			// was left on the original account (and stays eligible for the retention janitor). Surface a
			// distinct status so the return leg warns instead of claiming everything moved.
			$disposition = is_array( $result ) && is_array( $result['data']['storage_disposition'] ?? null )
				? $result['data']['storage_disposition']
				: null;

			if ( null !== $disposition
				&& 'none' !== (string) ( $disposition['requested'] ?? 'none' )
				&& empty( $disposition['applied'] )
			) {
				return 'success_storage_incomplete';
			}

			return 'success';
		}

		// A consumed or absent pending record means the return leg was opened twice, not a failure.
		if ( 'gk_connection_no_pending' === $result->get_error_code() ) {
			return 'done';
		}

		return (string) $result->get_error_code();
	}

	/**
	 * Returns the post-handshake redirect URL for the given connection scope.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope  Connection scope: 'network' or 'site'.
	 * @param string $status gk_connect status stamped onto the redirect.
	 *
	 * @return string
	 */
	private function redirect_url( string $scope, string $status ): string {
		$is_network = TokenStore::SCOPE_NETWORK === $scope || CoreHelpers::is_network_admin();
		$default    = $is_network && is_multisite() ? network_admin_url() : admin_url();

		/**
		 * Filters where the connection return leg redirects after completing or cancelling.
		 *
		 * @since 1.26.0
		 *
		 * @param string $url     Redirect URL. Default: the scope-appropriate admin dashboard.
		 * @param array  $context { @type string $scope Connection scope. @type string $status gk_connect status. }
		 */
		return (string) apply_filters(
			'gk/foundation/connection/return-redirect',
			$default,
			[
				'scope'  => $scope,
				'status' => $status,
			]
		);
	}

	/**
	 * Returns the absolute URL of the return-leg admin page.
	 *
	 * @since 1.26.0
	 *
	 * @param string|null $scope Force the return URL to a scope ('network'/'site'), or null to derive it from
	 *                           context. begin() passes the already-resolved scope so a stripped referer cannot
	 *                           misroute a network handshake's return leg to a subsite.
	 *
	 * @return string
	 */
	private function return_url( ?string $scope = null ): string {
		$admin_page = 'admin.php?page=' . self::RETURN_PAGE;
		$is_network = null !== $scope ? TokenStore::SCOPE_NETWORK === $scope : CoreHelpers::is_network_admin();

		return $is_network ? network_admin_url( $admin_page ) : admin_url( $admin_page );
	}

	/**
	 * Returns the lazily-built connection client.
	 *
	 * @since 1.26.0
	 *
	 * @return ConnectClient
	 */
	private function client(): ConnectClient {
		if ( null === $this->_client ) {
			$this->_client = new ConnectClient( new TokenStore() );
		}

		return $this->_client;
	}

	/**
	 * Returns a client forced to one payload-resolved connection scope.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope Payload-resolved connection scope.
	 *
	 * @return ConnectClient
	 */
	private function scoped_client( string $scope ): ConnectClient {
		return new ConnectClient( new TokenStore( $scope ) );
	}

	/**
	 * Checks whether the current user may manage the account connection.
	 *
	 * @since 1.26.0
	 *
	 * @param string $action Kernel action being authorized.
	 *
	 * @return bool
	 */
	private function current_user_can( string $action ): bool {
		/**
		 * Filters whether the current user may manage the account connection.
		 *
		 * Consumers route this through their own permission system; the kernel default is the
		 * WordPress admin capability.
		 *
		 * @since 1.26.0
		 *
		 * @param bool  $can     Whether the current user may manage the connection.
		 * @param array $context { @type string $action Kernel action being authorized. }
		 */
		return (bool) apply_filters( 'gk/foundation/connection/user-can-manage', current_user_can( 'manage_options' ), [ 'action' => $action ] );
	}

	/**
	 * Throws when the current user lacks the required capability.
	 *
	 * @since 1.26.0
	 *
	 * @param string $action Kernel action being authorized.
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	private function require_capability( string $action ) {
		if ( ! $this->current_user_can( $action ) ) {
			throw new Exception( __( 'You do not have permission to perform this action.', 'gk-foundation' ) );
		}
	}
}
