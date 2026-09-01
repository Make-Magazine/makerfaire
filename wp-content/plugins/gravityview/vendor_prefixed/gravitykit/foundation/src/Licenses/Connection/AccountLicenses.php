<?php

namespace GravityKit\GravityView\Foundation\Licenses\Connection;

use Exception;
use Throwable;
use GravityKit\GravityView\Foundation\AccountConnection\ConnectClient;
use GravityKit\GravityView\Foundation\AccountConnection\ConnectionState;
use GravityKit\GravityView\Foundation\AccountConnection\Framework as ConnectionFramework;
use GravityKit\GravityView\Foundation\AccountConnection\TokenStore;
use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityView\Foundation\Helpers\WP;
use GravityKit\GravityView\Foundation\Licenses\Framework as LicensesFramework;
use GravityKit\GravityView\Foundation\Licenses\LicenseManager;
use GravityKit\GravityView\Foundation\Logger\Framework as LoggerFramework;

/**
 * The license domain's use of the account connection.
 *
 * The connection kernel (src/AccountConnection) is domain-blind: it establishes the connection and
 * exposes the signed channel plus generic events and seam filters. This class is the licenses
 * consumer — it pulls and mutates account licenses over the channel, caches the catalog, ingests
 * granted licenses into local state, and supplies the Manage Your Kit presentation the kernel
 * deliberately knows nothing about.
 *
 * @since 1.26.0
 */
final class AccountLicenses {
	/**
	 * Network transient that de-dupes the once-per-window network-connection recheck across all subsites.
	 *
	 * @since 1.26.0
	 *
	 * @var string
	 */
	const NETWORK_RECHECK_GUARD = 'gk_connection_network_recheck';

	/**
	 * How long (seconds) the network-connection recheck stays de-duped: 6 hours. A literal, not
	 * HOUR_IN_SECONDS, so the class constant resolves without WordPress loaded (test-safety).
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	const NETWORK_RECHECK_INTERVAL = 21600;

	/**
	 * Class instance.
	 *
	 * @since 1.26.0
	 *
	 * @var AccountLicenses|null
	 */
	private static $_instance = null;

	/**
	 * Foundation licenses permission key gating connection management.
	 *
	 * @since 1.26.0
	 *
	 * @var string
	 */
	private $capability;

	/**
	 * AccountLicenses constructor.
	 *
	 * @since 1.26.0
	 */
	private function __construct() {
		/**
		 * Filters the capability required to manage the account connection.
		 *
		 * @since 1.26.0
		 *
		 * @param string $capability Foundation licenses permission key. Default: 'manage_licenses'.
		 */
		$this->capability = (string) apply_filters( 'gk/foundation/connection/capability', 'manage_licenses' );
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.26.0
	 *
	 * @return AccountLicenses
	 */
	public static function get_instance(): AccountLicenses {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Registers the license consumer's hooks. Hooks-only: no disk/parse-heavy work.
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public function init() {
		if ( did_action( 'gk/foundation/licenses/connection/initialized' ) ) {
			return;
		}

		add_filter( 'gk/foundation/ajax/' . ConnectionFramework::AJAX_ROUTER . '/routes', [ $this, 'configure_ajax_routes' ] );

		// Kernel seams: the connection module stays license-blind; this side supplies the domain.
		add_filter( 'gk/foundation/connection/requested-scopes', [ $this, 'add_licenses_scope' ] );
		add_filter( 'gk/foundation/connection/user-can-manage', [ $this, 'user_can_manage' ], 10, 2 );
		add_filter( 'gk/foundation/connection/return-redirect', [ $this, 'return_redirect' ], 10, 2 );
		add_filter( 'gk/foundation/connection/error-messages', [ $this, 'error_messages' ] );

		add_action( 'gk/foundation/connection/established', [ $this, 'on_connection_established' ] );
		add_action( 'gk/foundation/connection/disconnected', [ $this, 'on_connection_disconnected' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ], 20 );

		// Reflect a connection needing attention in the Manage Your Kit menu badge (WP-update-bubble style),
		// alongside the unlicensed/updates counts already contributed to the same submenu counter.
		add_filter( 'gk/foundation/admin-menu/submenu/' . LicensesFramework::ID . '/counter', [ $this, 'add_connection_badge_count' ] );

		// Piggyback the license recheck's ~12h cadence to refresh the catalog — no separate cron.
		// Attached during init so the recheck fired synchronously inside LicenseManager::init() is
		// caught too (pre-split, the listener attached after that fire and the page-load cadence
		// never refreshed the catalog). Cost: one signed pull per scope per cadence window, gated by
		// the same 12h recheck transient plus the network de-dupe guard.
		add_action( 'gk/foundation/licenses/rechecked', [ $this, 'refresh_catalog_on_recheck' ] );

		/**
		 * Fires when the licenses account-connection consumer has finished initializing.
		 *
		 * @since 1.26.0
		 *
		 * @param array $context Reserved for future data.
		 */
		do_action( 'gk/foundation/licenses/connection/initialized', [] );
	}

	/**
	 * Adds the licenses scope to a connection handshake.
	 *
	 * @since 1.26.0
	 *
	 * @param mixed $scopes Requested scopes (filter input; normalized to an array).
	 *
	 * @return array
	 */
	public function add_licenses_scope( $scopes ) {
		$scopes   = is_array( $scopes ) ? $scopes : [];
		$scopes[] = 'licenses';

		return array_values( array_unique( $scopes ) );
	}

	/**
	 * Routes the kernel's connection-management capability check through the Foundation licenses permissions.
	 *
	 * @since 1.26.0
	 *
	 * @param bool  $can     Kernel default (manage_options).
	 * @param array $context Check context with the kernel action.
	 *
	 * @return bool
	 */
	public function user_can_manage( $can, $context = [] ) {
		return LicensesFramework::get_instance()->current_user_can( $this->capability );
	}

	/**
	 * Sends the connection return leg to the Manage Your Kit page.
	 *
	 * @since 1.26.0
	 *
	 * @param string $url     Kernel default redirect URL.
	 * @param mixed  $context Redirect context with the connection scope (filter input).
	 *
	 * @return string
	 */
	public function return_redirect( $url, $context ) {
		$admin_page = 'admin.php?page=' . LicensesFramework::ID;
		$scope      = is_array( $context ) ? (string) ( $context['scope'] ?? '' ) : '';
		$is_network = TokenStore::SCOPE_NETWORK === $scope || CoreHelpers::is_network_admin();

		return $is_network && is_multisite() ? network_admin_url( $admin_page ) : admin_url( $admin_page );
	}

	/**
	 * Supplies the license-flavored copy for the kernel's neutral connection errors.
	 *
	 * @since 1.26.0
	 *
	 * @param mixed $messages Error messages keyed by WP_Error code (filter input; normalized to an array).
	 *
	 * @return array
	 */
	public function error_messages( $messages ) {
		$messages = is_array( $messages ) ? $messages : [];

		return array_merge(
			$messages,
			[
				'gk_connection_encrypt_failed' => __( "This site couldn't secure the connection key. Please try again, or enter a license key manually below.", 'gk-gravityview' ),
				'gk_connection_pending_failed' => __( "This site couldn't save the connection request. Please try again, or enter a license key manually below.", 'gk-gravityview' ),
				'gk_connection_offline'        => __( "We couldn't reach GravityKit.com to start the connection. Check that your site can make outbound requests, or enter a license key manually below.", 'gk-gravityview' ),
				/* translators: [status] is replaced with the HTTP status (e.g. "HTTP 403") and must not be translated. */
				'gk_connection_blocked'        => __( 'GravityKit.com returned an unexpected response ([status]) when starting the connection — a firewall or security rule may be blocking it. Try again, or enter a license key manually below.', 'gk-gravityview' ),
			]
		);
	}

	/**
	 * Warms the catalog and ingests the granted licenses when a connection is established.
	 *
	 * Preserves the pre-split exchange order: cache-only warm first, then the ingest. The token
	 * exchange deliberately returns 200 with empty licenses when the payload build fails
	 * server-side, so an empty exchange set is never prune-authoritative.
	 *
	 * @since 1.26.0
	 *
	 * @param array $context Established-connection context from the kernel.
	 *
	 * @return void
	 */
	public function on_connection_established( $context ) {
		if ( ! is_array( $context ) ) {
			return;
		}

		$scope    = TokenStore::SCOPE_NETWORK === ( $context['scope'] ?? '' ) ? TokenStore::SCOPE_NETWORK : TokenStore::SCOPE_SITE;
		$data     = is_array( $context['data'] ?? null ) ? $context['data'] : [];
		$licenses = is_array( $data['licenses'] ?? null ) ? $data['licenses'] : [];

		// Warm the catalog so the first Licenses page load needs no network pull. Best-effort: a
		// catalog failure must never fail an otherwise-good connection.
		$this->list_licenses( new ConnectClient( new TokenStore( $scope ) ) );

		/**
		 * Fires when granted account licenses were received over the connection.
		 *
		 * @since 1.26.0
		 *
		 * @param array $payload {
		 *     @type array  $licenses      Licenses returned by the server.
		 *     @type string $scope         Connection scope: 'network' or 'site'.
		 *     @type string $account_email Connected account email.
		 *     @type bool   $complete      Whether $licenses is the connection's full granted set
		 *                                 (enables pruning of no-longer-granted local rows).
		 * }
		 */
		do_action(
			'gk/foundation/connection/licenses/received',
			[
				'licenses'      => $licenses,
				'scope'         => $scope,
				'account_email' => (string) ( $context['account_email'] ?? '' ),
				'complete'      => ! empty( $licenses ),
			]
		);
	}

	/**
	 * Prunes this scope's connection licenses and purges its catalog on explicit disconnect.
	 *
	 * @since 1.26.0
	 *
	 * @param array $context Disconnected-connection context from the kernel.
	 *
	 * @return void
	 */
	public function on_connection_disconnected( $context ) {
		if ( ! is_array( $context ) ) {
			return;
		}

		$scope = TokenStore::SCOPE_NETWORK === ( $context['scope'] ?? '' ) ? TokenStore::SCOPE_NETWORK : TokenStore::SCOPE_SITE;

		// An explicit disconnect ends this scope's connection licenses (the server deactivates them
		// on revoke), so prune the local rows via the same authoritative empty-complete payload a
		// pull uses. Fires even for a decrypt-failed connection: its rows are equally dead.
		/** This mirrors the ingest fired on establishment; see that action's documentation. */
		do_action(
			'gk/foundation/connection/licenses/received',
			[
				'licenses'      => [],
				'scope'         => $scope,
				'account_email' => '',
				'complete'      => true,
			]
		);

		( new CatalogCache( $scope ) )->purge();
	}

	/**
	 * Configures the license Ajax routes on the connection router.
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
				'list_licenses'              => [ $this, 'ajax_list_licenses' ],
				'activate_account_license'   => [ $this, 'ajax_activate_account_license' ],
				'deactivate_account_license' => [ $this, 'ajax_deactivate_account_license' ],
			]
		);
	}

	/**
	 * Ajax request to pull the account catalog and this connection's licenses.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{permission:string, read_only:bool, available:array, app_data:array}
	 */
	public function ajax_list_licenses( array $payload ) {
		// The Licenses page renders at view_licenses, so a view-only user must still be able to read the catalog.
		if ( ! LicensesFramework::get_instance()->current_user_can( 'view_licenses' ) ) {
			throw new Exception( esc_html__( 'You do not have permission to perform this action.', 'gk-gravityview' ) );
		}

		$scope  = ConnectionFramework::get_instance()->resolve_connection_scope( $payload );
		$client = $this->scoped_client( $scope );

		$pulled = $this->list_licenses( $client );

		if ( is_wp_error( $pulled ) ) {
			throw new Exception( esc_html( $pulled->get_error_message() ) );
		}

		$this->sync_local_from( $pulled, $scope );

		return $this->pull_response( $pulled, $scope );
	}

	/**
	 * Ajax request to activate account licenses by ID.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{permission:string, read_only:bool, available:array, app_data:array, results:array, stale?:bool}
	 */
	public function ajax_activate_account_license( array $payload ) {
		$this->require_capability();

		$scope       = ConnectionFramework::get_instance()->resolve_connection_scope( $payload );
		$license_ids = $this->license_ids_from_payload( $payload );

		$this->guard_network_superseded( $scope, $license_ids );

		$result = $this->scoped_client( $scope )->signed_request( 'POST', '/licenses/activate', [ 'license_ids' => $license_ids ] );

		if ( is_wp_error( $result ) ) {
			throw new Exception( esc_html( $result->get_error_message() ) );
		}

		return $this->write_response( $result, $scope );
	}

	/**
	 * Ajax request to deactivate account licenses by ID.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{permission:string, read_only:bool, available:array, app_data:array, results:array, stale?:bool}
	 */
	public function ajax_deactivate_account_license( array $payload ) {
		$this->require_capability();

		$scope       = ConnectionFramework::get_instance()->resolve_connection_scope( $payload );
		$license_ids = $this->license_ids_from_payload( $payload );

		$this->guard_network_superseded( $scope, $license_ids );

		$result = $this->scoped_client( $scope )->signed_request( 'POST', '/licenses/deactivate', [ 'license_ids' => $license_ids ] );

		if ( is_wp_error( $result ) ) {
			throw new Exception( esc_html( $result->get_error_message() ) );
		}

		// Drop the now-inactive local rows before the response's app_data is built, so a deactivated
		// license leaves the list and returns to the "available" catalog rather than lingering as stale.
		LicenseManager::get_instance()->forget_connection_licenses( $this->succeeded_license_ids( $result ) );

		return $this->write_response( $result, $scope );
	}

	/**
	 * Pulls the account catalog and this connection's licenses over the signed channel.
	 *
	 * @since 1.26.0
	 *
	 * @param ConnectClient $client Scoped connection client.
	 *
	 * @return array{permission?:string, read_only?:bool, licenses?:array, available?:array, account?:array}|\WP_Error
	 */
	public function list_licenses( ConnectClient $client ) {
		// Capture the fingerprint of the connection this pull authenticates with, so the cached catalog is
		// stamped with the producer connection even if the stored connection changes before store() runs.
		$store       = $client->store();
		$fingerprint = $store->fingerprint();

		// Send the site's telemetry with the pull through the same payload builder a manual license check
		// uses, so both channels stay in sync. POST (not GET) so the request legitimately carries the body
		// — a GET body is unreliable through CDNs and can be stripped, breaking the signature. Without this,
		// the store has no telemetry for account-connected sites and cannot match version-targeted notices.
		$result = $client->signed_request(
			'POST',
			'/licenses',
			LicenseManager::get_instance()->get_license_check_payload()
		);

		// Cache every verified success so the Licenses page renders without a network pull (the list for an
		// actionable permission, permission-only for view); a failure leaves the last-good catalog intact.
		if ( ! is_wp_error( $result ) ) {
			( new CatalogCache( $store->scope() ) )->store( $result, $fingerprint );
		}

		return $result;
	}

	/**
	 * Fires the licenses ingest hook with a pulled payload so LicenseManager merges the granted
	 * licenses into local state. Fires nothing without a usable connection.
	 *
	 * @since 1.26.0
	 *
	 * @param array  $pulled Decoded payload from a licenses pull or write.
	 * @param string $scope  Storage scope of the connection the payload came from.
	 *
	 * @return void
	 */
	public function sync_local_from( array $pulled, string $scope ): void {
		// Fail closed: an unrecognized scope must read the per-site slot, never fall back to the
		// context-derived store (which resolves to network inside network admin).
		$scope = TokenStore::SCOPE_NETWORK === $scope ? TokenStore::SCOPE_NETWORK : TokenStore::SCOPE_SITE;

		$connection = ( new TokenStore( $scope ) )->get();

		if ( ! is_array( $connection ) || ! empty( $connection['decrypt_failed'] ) || empty( $connection['connection_id'] ) ) {
			return;
		}

		// Fail closed: only an explicit network scope routes the ingest to network-wide storage.
		$ingest_scope = TokenStore::SCOPE_NETWORK === ( $connection['scope'] ?? '' ) ? TokenStore::SCOPE_NETWORK : TokenStore::SCOPE_SITE;

		/** This mirrors the ingest fired on establishment; see that action's documentation. */
		do_action(
			'gk/foundation/connection/licenses/received',
			[
				'licenses'      => is_array( $pulled['licenses'] ?? null ) ? $pulled['licenses'] : [],
				'scope'         => $ingest_scope,
				'account_email' => (string) ( $pulled['account']['email'] ?? '' ),
				// Pull and write responses always carry the full granted set — even an empty one is
				// authoritative (the owner removed every grant), so stale local rows can prune.
				'complete'      => true,
			]
		);
	}

	/**
	 * Builds the response for a verified write, treating the follow-up catalog pull as best-effort.
	 *
	 * The write response is already server-verified, so its licenses/results are ingested first and the
	 * call always reports success. A failed follow-up catalog re-pull only marks the response `stale` (so
	 * the UI can refresh); it never throws — the remote slot was already mutated, so a verified write must
	 * not be reported as failed.
	 *
	 * @since 1.26.0
	 *
	 * @param array  $write_result Result of an activate/deactivate write with a `results` map.
	 * @param string $scope        Payload-resolved connection scope.
	 *
	 * @return array{permission:string, read_only:bool, available:array, app_data:array, results:array, stale?:bool}
	 */
	private function write_response( array $write_result, string $scope ): array {
		// Ingest the verified write response before the best-effort re-pull so local state is current
		// even if the follow-up catalog pull fails.
		$this->sync_local_from( $write_result, $scope );

		$results = is_array( $write_result['results'] ?? null ) ? $write_result['results'] : [];

		$pulled = $this->list_licenses( $this->scoped_client( $scope ) );

		if ( is_wp_error( $pulled ) ) {
			$cached = ( new CatalogCache( $scope ) )->get();

			$response            = $this->pull_response( is_array( $cached ) ? $cached : $write_result, $scope );
			$response['results'] = $results;
			$response['stale']   = true;

			return $response;
		}

		$this->sync_local_from( $pulled, $scope );

		$response            = $this->pull_response( $pulled, $scope );
		$response['results'] = $results;

		return $response;
	}

	/**
	 * Shapes a pulled catalog payload for the UI, attaching refreshed local app data.
	 *
	 * @since 1.26.0
	 *
	 * @param array  $pulled Decoded catalog payload.
	 * @param string $scope  Payload-resolved connection scope.
	 *
	 * @return array{permission:string, read_only:bool, available:array, app_data:array}
	 */
	private function pull_response( array $pulled, string $scope ): array {
		$app_data               = LicensesFramework::get_instance()->ajax_get_app_data( [ 'is_network_admin' => TokenStore::SCOPE_NETWORK === $scope ] );
		$app_data['connection'] = ( new ConnectionState( new TokenStore( $scope ) ) )->for_frontend();

		return [
			'permission' => (string) ( $pulled['permission'] ?? '' ),
			'read_only'  => (bool) ( $pulled['read_only'] ?? false ),
			'available'  => is_array( $pulled['available'] ?? null ) ? $pulled['available'] : [],
			'app_data'   => $app_data,
		];
	}

	/**
	 * Extracts and validates the license IDs from an AJAX payload.
	 *
	 * @since 1.26.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array<int>
	 */
	private function license_ids_from_payload( array $payload ): array {
		$raw = $payload['license_ids'] ?? null;

		if ( ! is_array( $raw ) || empty( $raw ) ) {
			throw new Exception( esc_html__( 'A list of license IDs is required.', 'gk-gravityview' ) );
		}

		$license_ids = [];

		foreach ( $raw as $id ) {
			$id = (int) $id;

			if ( $id > 0 ) {
				$license_ids[] = $id;
			}
		}

		if ( empty( $license_ids ) ) {
			throw new Exception( esc_html__( 'A list of license IDs is required.', 'gk-gravityview' ) );
		}

		return array_values( array_unique( $license_ids ) );
	}

	/**
	 * Blocks a site connection from toggling a license that is already activated network-wide.
	 *
	 * A network-wide activation supersedes any site activation of the same license; only the network
	 * admin manages those. Network-scoped requests are unaffected.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope       Resolved connection scope.
	 * @param int[]  $license_ids Requested account license IDs.
	 *
	 * @throws Exception When a requested license is activated network-wide.
	 *
	 * @return void
	 */
	private function guard_network_superseded( string $scope, array $license_ids ) {
		if ( LicenseManager::SCOPE_SITE !== $scope ) {
			return;
		}

		$superseded = LicenseManager::get_instance()->get_network_superseded_license_ids();

		// Fail closed: an unreadable network slot must block a site toggle rather than risk a dual activation.
		if ( ! $superseded['readable'] ) {
			throw new Exception( esc_html__( 'Network-wide license data is temporarily unavailable, so this license can\'t be changed here. Reconnect the network connection and try again.', 'gk-gravityview' ) );
		}

		if ( array_intersect( $license_ids, $superseded['ids'] ) ) {
			throw new Exception( esc_html__( 'This license is activated network-wide and can only be changed by the network administrator.', 'gk-gravityview' ) );
		}
	}

	/**
	 * Extracts the account license ids a write succeeded for from its per-id `results` map.
	 *
	 * @since 1.26.0
	 *
	 * @param array $result Decoded write payload with a `results` map keyed by id.
	 *
	 * @return array<int>
	 */
	private function succeeded_license_ids( array $result ): array {
		$results = is_array( $result['results'] ?? null ) ? $result['results'] : [];

		$ids = [];

		foreach ( $results as $id => $outcome ) {
			if ( is_array( $outcome ) && ! empty( $outcome['success'] ) ) {
				$ids[] = (int) $id;
			}
		}

		return $ids;
	}

	/**
	 * Refreshes the cached catalog when the license recheck's cadence fires, so the connected
	 * account's catalog stays current without a per-page-load pull or a dedicated cron.
	 *
	 * Best-effort: list_licenses() caches the verified success (the list for an actionable permission,
	 * permission-only for view), so a failed refresh leaves the last-good catalog untouched.
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public function refresh_catalog_on_recheck() {
		// Each blog rechecks its own site connection.
		$this->recheck_connection( TokenStore::SCOPE_SITE );

		// The network connection is rechecked once per window across the whole network — from whichever blog
		// fires this hook (the main site can be a rarely-visited shell), de-duped by a short network transient so
		// it is not pulled once per subsite nor driven by every subsite's outbound HTTP. Its catalog caches to
		// the network-scoped slot (sitemeta), so it never overwrites a subsite's own per-blog catalog.
		if ( is_multisite() && ! WP::get_site_transient( self::NETWORK_RECHECK_GUARD ) ) {
			// WP::set_site_transient (not core set_site_transient) writes straight to sitemeta, bypassing the
			// object cache that opcode-cached installs mis-handle.
			WP::set_site_transient( self::NETWORK_RECHECK_GUARD, 1, self::NETWORK_RECHECK_INTERVAL );

			$this->recheck_connection( TokenStore::SCOPE_NETWORK );
		}
	}

	/**
	 * Runs a best-effort signed catalog pull for the connection in one storage scope, refreshing its health
	 * and cached catalog. A missing, decrypt-failed, or incomplete connection is a no-op.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope Storage scope to recheck ('network' or 'site').
	 *
	 * @return void
	 */
	private function recheck_connection( string $scope ): void {
		$store      = new TokenStore( $scope );
		$connection = $store->get();

		if ( ! is_array( $connection ) || ! empty( $connection['decrypt_failed'] ) || empty( $connection['connection_id'] ) ) {
			return;
		}

		// Best-effort background refresh: malformed stored key material can throw while signing, and a recheck
		// must never surface a fatal on an unrelated admin page load.
		try {
			$pulled = $this->list_licenses( new ConnectClient( $store ) );

			if ( is_wp_error( $pulled ) ) {
				return;
			}

			// Route the verified pull through the same ingest the pull paths use, so the background
			// refresh also repairs local connection rows (grants added or removed account-side).
			$this->sync_local_from( $pulled, $scope );
		} catch ( Throwable $e ) {
			LoggerFramework::get_instance()->warning( 'Account connection recheck failed: ' . $e->getMessage() );
		}
	}

	/**
	 * Bumps the Manage Your Kit submenu badge by one when the current connection needs attention, so a
	 * broken or attention-state connection surfaces the same way as an unlicensed product or a pending update.
	 *
	 * @since 1.26.0
	 *
	 * @param int $count Current submenu badge count.
	 *
	 * @return int
	 */
	public function add_connection_badge_count( $count ) {
		// A subsite admin must not be badged for a network-scoped connection they cannot act on (the same
		// gate the catalog uses) — otherwise the bubble never clears for them.
		if ( ! LicensesFramework::get_instance()->current_user_can( $this->capability ) || ! $this->user_can_access_connection_scope() ) {
			return (int) $count;
		}

		return (int) $count + ( ConnectionState::get_instance()->needs_attention() ? 1 : 0 );
	}

	/**
	 * Injects the connection state and cached catalog into the Manage Your Kit UI as
	 * `gkLicenses.data.connection` and `gkLicenses.data.connectionCatalog`.
	 *
	 * @since 1.26.0
	 *
	 * @param string $page Current admin page.
	 *
	 * @return void
	 */
	public function enqueue_assets( $page ) {
		if ( false === strpos( (string) $page, LicensesFramework::ID ) ) {
			return;
		}

		if ( ! wp_script_is( LicensesFramework::ID, 'enqueued' ) ) {
			return;
		}

		$state       = ConnectionState::get_instance()->for_frontend();
		$connection  = wp_json_encode( $state ) ?: '{}';
		$catalog     = wp_json_encode( $this->catalog_for_frontend( $state ) ) ?: 'null';
		$account_url = wp_json_encode( ConnectClient::account_url() ) ?: '""';

		wp_add_inline_script(
			LicensesFramework::ID,
			'window.gkLicenses = window.gkLicenses || {}; window.gkLicenses.data = window.gkLicenses.data || {};'
				. ' window.gkLicenses.data.connection = ' . $connection . ';'
				. ' window.gkLicenses.data.connectionCatalog = ' . $catalog . ';'
				. ' window.gkLicenses.data.accountUrl = ' . $account_url . ';',
			'after'
		);
	}

	/**
	 * Builds the cached catalog payload for the page, or `null` when none should reach it.
	 *
	 * A cached entry carries the connection's permission (+ read-only flag) for any level so the guidance
	 * renders on first paint; the activatable `available` list is non-empty only for self_service.
	 * Unconnected/decrypt-failed sites and scope-gated subsite admins get `null`. Carries the server `now`
	 * alongside `fetched_at` so the client can compute staleness.
	 *
	 * @since 1.26.0
	 *
	 * @param array $state Connection state from ConnectionState::for_frontend().
	 *
	 * @return array|null
	 */
	private function catalog_for_frontend( array $state ): ?array {
		if ( empty( $state['connected'] ) || ! empty( $state['decrypt_failed'] ) ) {
			return null;
		}

		// A network-scoped catalog must not be disclosed to a subsite admin lacking manage_network_options.
		if ( ! $this->user_can_access_connection_scope() ) {
			return null;
		}

		$catalog = ( new CatalogCache() )->get();

		if ( null === $catalog ) {
			return null;
		}

		return [
			'permission' => (string) ( $catalog['permission'] ?? '' ),
			'read_only'  => (bool) ( $catalog['read_only'] ?? false ),
			'available'  => is_array( $catalog['available'] ?? null ) ? $catalog['available'] : [],
			'fetched_at' => (int) ( $catalog['fetched_at'] ?? 0 ),
			'now'        => time(),
		];
	}

	/**
	 * Whether the current user may view the context-resolved connection given its scope.
	 *
	 * A network-scoped connection shares one account key across every subsite, so rendering its state or catalog
	 * requires `manage_network_options`. Site-scoped connections and single-site installs impose no requirement
	 * beyond the caller's own capability check.
	 *
	 * @since 1.26.0
	 *
	 * @return bool
	 */
	private function user_can_access_connection_scope(): bool {
		if ( ! is_multisite() ) {
			return true;
		}

		$connection = ( new TokenStore() )->get();
		$scope      = is_array( $connection ) ? (string) ( $connection['scope'] ?? '' ) : '';

		if ( TokenStore::SCOPE_NETWORK !== $scope ) {
			return true;
		}

		return current_user_can( 'manage_network_options' );
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
	 * Throws when the current user lacks the required capability.
	 *
	 * @since 1.26.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	private function require_capability() {
		if ( ! LicensesFramework::get_instance()->current_user_can( $this->capability ) ) {
			throw new Exception( esc_html__( 'You do not have permission to perform this action.', 'gk-gravityview' ) );
		}
	}
}
