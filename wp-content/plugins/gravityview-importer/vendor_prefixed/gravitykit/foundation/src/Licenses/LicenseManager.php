<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Licenses;

use Exception;
use GravityKit\GravityImport\Foundation\AccountConnection\ConnectionHealth;
use GravityKit\GravityImport\Foundation\AccountConnection\TokenStore;
use GravityKit\GravityImport\Foundation\Core;
use GravityKit\GravityImport\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityImport\Foundation\Helpers\WP;
use GravityKit\GravityImport\Foundation\Logger\Framework as LoggerFramework;
use GravityKit\GravityImport\Foundation\Settings\Framework as SettingsFramework;
use GravityKit\GravityImport\Foundation\Encryption\Encryption;
use GravityKit\GravityImport\Foundation\Helpers\Arr;
use GravityKit\GravityImport\Foundation\Notices\ServerNoticeHandler;
use GFForms;
use GFFormsModel;
use GravityKit\GravityImport\Foundation\WP\AdminMenu;

class LicenseManager {
	const STORE_API_ENDPOINT = 'https://store.gravitykit.com';

	const STORE_API_VERSION = 3;

	/**
	 * The newest license model this copy implements (see transition_model_to_vN methods).
	 *
	 * @since 1.25.0
	 */
	const LICENSE_MODEL_VERSION = 2;

	const HARDCODED_LICENSE_CONSTANTS = [ 'GRAVITYVIEW_LICENSE_KEY', 'GRAVITYKIT_LICENSES' ];

	const SCOPE_NETWORK = 'network';

	const SCOPE_SITE = 'site';

	const SITE_LICENSES_MIRROR_ID = 'gk_licenses_site_mirror';

	/**
	 * {@LicenseManager} class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var LicenseManager|null
	 */
	private static $_instance = null;

	/**
	 * Cached licenses data object.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Renamed to $licenses_data.
	 *
	 * @var array|null
	 */
	public $licenses_data = null;

	/**
	 * Whether license data exists but can't be decrypted.
	 *
	 * @since 1.2.0
	 *
	 * @var bool
	 */
	public $is_decryptable = true;

	/**
	 * Blog ID the cached licenses data was read for (multisite; null on single site).
	 *
	 * @since 1.25.0
	 *
	 * @var int|null
	 */
	private $licenses_data_blog_id = null;

	/**
	 * Where each cached license key was read from: 'network' (shared) or 'site' (this site only).
	 * Used as a fail-safe when saving entries that lack a `scope` (e.g., written by an older Foundation copy).
	 *
	 * @since 1.25.0
	 *
	 * @var array
	 */
	private $license_storage_scopes = [];

	/**
	 * Whether a system-initiated flow (hardcoded licenses, legacy migration) is running.
	 * System flows express install-wide intent and bypass the per-user network-scope capability check.
	 *
	 * @since 1.25.0
	 *
	 * @var bool
	 */
	private $is_system_operation = false;

	/**
	 * Request-scoped cache of the per-blog site licenses mirror.
	 *
	 * @since 1.25.0
	 *
	 * @var array|null
	 */
	private $site_licenses_mirror_cache = null;

	/**
	 * Whether license rechecks should rebuild the WordPress plugin update transient.
	 *
	 * @since 1.19.0
	 *
	 * @var bool
	 */
	private $refresh_update_plugins_transient_after_license_recheck = true;

	/**
	 * Whether this request's recheck already asked the store about network-scope keys.
	 *
	 * @since 1.25.0
	 *
	 * @var bool
	 */
	private $network_recheck_performed = false;

	/**
	 * Request-scoped cache of model-version marker reads.
	 *
	 * @since 1.25.0
	 *
	 * @var array
	 */
	private $model_version_markers = [];

	/**
	 * Returns class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return LicenseManager
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initializes the class.
	 *
	 * @since 1.0.0
	 * @since TBD Reconcile license notices after connection cleanup.
	 *
	 * @return void
	 */
	public function init() {
		static $initialized;

		if ( $initialized ) {
			return;
		}

		// Registered before the synchronous recheck below: its cadence chain can fire the background
		// catalog refresh, whose ingest event this listener must catch.
		add_action( 'gk/foundation/connection/licenses/received', [ $this, 'ingest_received_licenses' ] );

		// Run after AccountLicenses' priority-10 empty ingest so rows without account IDs cannot
		// be re-synced after cleanup. Notices remain blog-scoped by design: other subsites can retain
		// license notices until their next reconciliation or until uninstall hides the product.
		add_action( 'gk/foundation/connection/disconnected', [ $this, 'reconcile_disconnected_server_notices' ], 20 );

		if ( ! wp_doing_ajax() ) {
			$this->migrate_legacy_licenses();

			$this->process_hardcoded_licenses();

			$this->recheck_all_licenses();

			$this->maybe_poll_model_version_enforcement();
		}

		add_filter( 'gk/foundation/ajax/' . Framework::AJAX_ROUTER . '/routes', [ $this, 'configure_ajax_routes' ] );

		if ( is_multisite() ) {
			add_action(
				'wp_delete_site',
				function ( $old_site ) {
					$this->remove_blog_from_site_licenses_mirror( (int) $old_site->blog_id );
				}
			);
		}

		// The badge triggers a products-data lookup that can call the license server, and a failed
		// call translates its error message; doing that here (during `plugins_loaded`, before
		// `after_setup_theme`) trips WordPress 6.7's just-in-time translation notice. The badge is
		// not consumed until `admin_menu`.
		if ( did_action( 'init' ) ) {
			$this->update_manage_your_kit_submenu_badge_count();
		} else {
			add_action( 'init', [ $this, 'update_manage_your_kit_submenu_badge_count' ] );
		}

		$initialized = true;
	}

	/**
	 * Configures Ajax routes handled by this class.
	 *
	 * @since 1.0.0
	 *
	 * @see   Core::process_ajax_request()
	 *
	 * @param array $routes Ajax route to class method map.
	 *
	 * @return array
	 */
	public function configure_ajax_routes( array $routes ) {
		return array_merge(
			$routes,
			[
				'get_licenses'       => [ $this, 'ajax_get_licenses_data' ],
				'activate_license'   => [ $this, 'ajax_activate_license' ],
				'reactivate_license' => [ $this, 'ajax_reactivate_license' ],
				'deactivate_license' => [ $this, 'ajax_deactivate_license' ],
			]
		);
	}

	/**
	 * Ajax request wrapper for the get_licenses_data() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function ajax_get_licenses_data( array $payload ) {
		if ( ! Framework::get_instance()->current_user_can( 'view_licenses' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$payload = wp_parse_args(
			$payload,
			[
				'skip_cache' => false,
			]
		);

		$this->migrate_legacy_licenses( $payload['skip_cache'] );

		$this->process_hardcoded_licenses();

		$this->recheck_all_licenses( $payload['skip_cache'] );

		// Network admin manages network-scoped licenses only. Context comes from the request (the JS sends
		// the render-time value); the fallback uses the native check for page renders and never the referer,
		// so an AJAX call without the flag can't leak site rows into a network-admin response.
		$is_network_admin = isset( $payload['is_network_admin'] )
			? (bool) $payload['is_network_admin']
			: ( ! wp_doing_ajax() && CoreHelpers::is_network_admin() );

		// A network-scoped row carries the network licenser's holder identity, key, and signed download
		// URLs. Only a user who can manage the network may receive it — an authoritative server-side
		// capability, never the client-sent is_network_admin flag. A subsite admin gets their own site
		// rows only; products still read as network-licensed via ProductManager's separate boolean.
		$can_see_network_licenses = ! is_multisite() || current_user_can( 'manage_network_options' );

		$licenses      = $this->get_licenses_data();
		$licenses_data = [];

		foreach ( $licenses as $key => $license ) {
			$scope = $this->license_storage_scopes[ $key ] ?? self::SCOPE_NETWORK;

			if ( ! $can_see_network_licenses && self::SCOPE_NETWORK === $scope ) {
				continue;
			}

			// A site-scoped activation from any single site must never surface on the network admin. Classify
			// by the physical storage slot (authoritative), matching how save_licenses_data() routes rows;
			// the cascade runs network->site only.
			if ( $is_network_admin && is_multisite() && self::SCOPE_NETWORK !== $scope ) {
				continue;
			}

			$license                          = $this->modify_license_data_for_frontend_output( $license );
			$licenses_data[ $license['key'] ] = $license;
		}

		return $licenses_data;
	}

	/**
	 * Retrieves license data from the database.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_licenses_data() {
		if ( is_array( $this->licenses_data ) && ( ! is_multisite() || get_current_blog_id() === $this->licenses_data_blog_id ) ) {
			return $this->licenses_data;
		}

		$this->is_decryptable = true;

		$network_licenses = $this->read_license_storage( self::SCOPE_NETWORK );

		if ( ! is_multisite() ) {
			$this->licenses_data = $network_licenses;

			return $this->licenses_data;
		}

		$network_licenses = $this->add_missing_license_scopes( $network_licenses );
		$site_licenses    = $this->read_license_storage( self::SCOPE_SITE );

		$this->license_storage_scopes = [];

		foreach ( array_keys( $site_licenses ) as $key ) {
			$this->license_storage_scopes[ $key ] = self::SCOPE_SITE;
		}

		foreach ( array_keys( $network_licenses ) as $key ) {
			$this->license_storage_scopes[ $key ] = self::SCOPE_NETWORK;
		}

		// A key present in both storages resolves to the network entry, which covers the whole network.
		// The union operator (unlike array_merge) never renumbers integer-like license keys.
		$this->licenses_data         = $network_licenses + $site_licenses;
		$this->licenses_data_blog_id = get_current_blog_id();

		return $this->licenses_data;
	}

	/**
	 * Returns the network-wide supersession set: account license IDs activated network-wide.
	 *
	 * A network-wide activation supersedes any site activation of the same license, so a site connection
	 * must not offer or toggle these IDs. `readable` is false when a non-empty network slot cannot be
	 * decrypted, so callers fail closed rather than allow a site toggle that should be blocked.
	 *
	 * @since 1.26.0
	 *
	 * @return array{ids:int[], readable:bool}
	 */
	public function get_network_superseded_license_ids() {
		if ( ! is_multisite() ) {
			return [
				'ids'      => [],
				'readable' => true,
			];
		}

		$raw = get_site_option( Framework::ID );

		if ( empty( $raw ) ) {
			return [
				'ids'      => [],
				'readable' => true,
			];
		}

		$rows = json_decode( Encryption::get_instance()->decrypt( $raw ) ?: '', true );

		if ( ! is_array( $rows ) ) {
			return [
				'ids'      => [],
				'readable' => false,
			];
		}

		$ids = [];

		foreach ( $rows as $row ) {
			// Only an active network license supersedes; an expired/inactive row must not block a site toggle.
			if ( ! is_array( $row ) || ! $this->is_license_active_for_site( $row ) ) {
				continue;
			}

			$id = (int) ( $row['connection_license_id'] ?? 0 );

			// A connection-sourced network row must carry its account id; without it the supersession set
			// is incomplete, so fail closed rather than let a site re-toggle the same underlying license.
			if ( 'connection' === ( $row['source'] ?? '' ) && $id <= 0 ) {
				return [
					'ids'      => [],
					'readable' => false,
				];
			}

			if ( $id > 0 ) {
				$ids[] = $id;
			}
		}

		return [
			'ids'      => $ids,
			'readable' => true,
		];
	}

	/**
	 * Reads and decrypts licenses from one storage location.
	 *
	 * @since 1.25.0
	 *
	 * @param string $scope Storage to read: 'network' (shared network option) or 'site' (current site's option).
	 *
	 * @return array
	 */
	private function read_license_storage( $scope ) {
		$licenses_data = self::SCOPE_SITE === $scope ? get_option( Framework::ID ) : get_site_option( Framework::ID );

		if ( empty( $licenses_data ) ) {
			return [];
		}

		$licenses_data = json_decode( Encryption::get_instance()->decrypt( $licenses_data ) ?: '', true );

		if ( ! is_array( $licenses_data ) ) {
			$this->is_decryptable = false;

			return [];
		}

		return $licenses_data;
	}

	/**
	 * Adds `scope`, `url` and `legacy` to network licenses that were saved before per-site licensing
	 * existed, and re-applies reached model transitions in memory.
	 *
	 * @since 1.25.0
	 *
	 * @param array $network_licenses Decrypted licenses from the network storage.
	 *
	 * @return array
	 */
	private function add_missing_license_scopes( array $network_licenses ) {
		$added = false;

		foreach ( $network_licenses as $key => $license ) {
			$effective = $this->effective_model_version( $key, $license );

			// Re-apply reached transitions in memory; transitions are pure shape-mutations, so an
			// opportunistic persist by the stamp write-back below converges to the same bytes.
			$this->apply_reached_transitions( $key, $network_licenses[ $key ] );

			if ( isset( $license['scope'] ) ) {
				continue;
			}

			$network_licenses[ $key ]['scope'] = self::SCOPE_NETWORK;
			$network_licenses[ $key ]['url']   = network_home_url();

			if ( $effective < 2 ) {
				$network_licenses[ $key ]['legacy'] = true;
			}

			$added = true;
		}

		// Saving is best-effort: an old Foundation copy removes the fields again on its next save, so
		// write at most every few minutes to avoid constant database churn. The returned data always has the fields.
		if ( $added && ! WP::get_site_transient( Framework::ID . '/scope-stamp-throttle' ) ) {
			WP::set_site_transient( Framework::ID . '/scope-stamp-throttle', current_time( 'timestamp' ), 5 * MINUTE_IN_SECONDS ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp

			$this->write_license_storage( self::SCOPE_NETWORK, $network_licenses );
		}

		return $network_licenses;
	}

	/**
	 * Polls the store for model-version enforcement on a dedicated schedule.
	 *
	 * The shared recheck budget can be consumed by older bundled Foundation copies that ignore the
	 * floor field; this poll uses its own transient (unknown to older copies), so the first eligible
	 * request served by a copy carrying this logic after the poll is due ATTEMPTS the climb (store
	 * or marker failures retry on the next cycle). It is quiet unless the store has actually
	 * published a floor above some entry's version, and skips requests where this copy's own
	 * recheck already asked the store.
	 *
	 * At model v2 every pending entry is network-scope by construction; a future transition that can
	 * apply to site-scoped entries requires scope-grouped polling (mirroring the recheck's per-scope
	 * buckets) before it ships.
	 *
	 * @since 1.25.0
	 *
	 * @return void
	 */
	private function maybe_poll_model_version_enforcement() {
		if ( ! is_multisite() ) {
			return;
		}

		// This request's recheck already asked the store about every network key; the poll exists
		// only for requests where an older copy consumed that budget.
		if ( $this->network_recheck_performed ) {
			return;
		}

		$poll_id = Framework::ID . '/model-version-poll';

		if ( WP::get_site_transient( $poll_id ) ) {
			return;
		}

		// Set BEFORE any store call so a slow or failing store cannot be hammered (normally at most one call per interval).
		WP::set_site_transient( $poll_id, current_time( 'timestamp' ), DAY_IN_SECONDS ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp

		// Termination is driven by the floor the store actually published, never this copy's own
		// capability — a never-raised floor means zero poll traffic, and a staggered rollout goes
		// quiet as soon as every entry reaches the published floor.
		$target = min( (int) get_site_option( 'gk_licenses_last_seen_model_floor', 0 ), self::LICENSE_MODEL_VERSION );

		if ( $target < 2 ) {
			return;
		}

		$licenses_data = $this->get_licenses_data();
		$pending_keys  = [];

		foreach ( $licenses_data as $key => $license ) {
			// The connection channel owns connection-sourced rows; this poll checks against the plain URL
			// and would misreport marker-URL (network connection) activations as inactive.
			if ( 'connection' === ( $license['source'] ?? '' ) ) {
				continue;
			}

			if ( $this->effective_model_version( $key, $license ) < $target ) {
				$pending_keys[] = $key;
			}
		}

		if ( empty( $pending_keys ) ) {
			return;
		}

		try {
			$license_check_result = $this->check_licenses( $pending_keys, $this->resolve_license_url( self::SCOPE_NETWORK ), self::SCOPE_NETWORK );
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( "Model-version enforcement poll failed. {$e->getMessage()}." );

			return;
		}

		$revalidated_licenses = [];

		foreach ( $license_check_result as $key => $license ) {
			$revalidated_licenses[ $key ] = $this->prepare_rechecked_license_for_storage( $license, $licenses_data[ $key ] ?? [] );

			$this->maybe_advance_model_version( $key, $license, $licenses_data[ $key ] ?? [], $revalidated_licenses[ $key ] );
		}

		if ( ! empty( $revalidated_licenses ) ) {
			$this->finalize_license_revalidation( $revalidated_licenses + $licenses_data, $revalidated_licenses );
		}
	}

	/**
	 * Returns the network option name recording that a license was evaluated at a model version.
	 *
	 * @since 1.25.0
	 *
	 * @param string $license_key The license key.
	 * @param int    $version     The model version.
	 *
	 * @return string
	 */
	private function model_version_reached_marker( $license_key, $version ) {
		return 'gk_licenses_model_version_reached_' . hash( 'sha256', (string) $license_key ) . '_v' . (int) $version;
	}

	/**
	 * Checks whether a license was already evaluated at a model version. Request-memoized.
	 *
	 * A marker means the version was EVALUATED for the key — not that a mutation was applied
	 * (a transition whose predicate refuses settles vacuously). Transitions therefore never
	 * assume a lower version's mutation is materialized on the entry.
	 *
	 * @since 1.25.0
	 *
	 * @param string $license_key The license key.
	 * @param int    $version     The model version.
	 *
	 * @return bool
	 */
	private function is_model_version_reached( $license_key, $version ) {
		$marker = $this->model_version_reached_marker( $license_key, $version );

		if ( ! array_key_exists( $marker, $this->model_version_markers ) ) {
			$this->model_version_markers[ $marker ] = (bool) get_site_option( $marker );
		}

		return $this->model_version_markers[ $marker ];
	}

	/**
	 * Durably records that a license was evaluated at a model version. Idempotent per (key, version).
	 *
	 * @since 1.25.0
	 *
	 * @param string $license_key The license key.
	 * @param int    $version     The model version.
	 *
	 * @return bool Whether the marker is persisted.
	 */
	private function mark_model_version_reached( $license_key, $version ) {
		$marker = $this->model_version_reached_marker( $license_key, $version );

		$persisted = (bool) get_site_option( $marker ) || update_site_option( $marker, current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp

		if ( $persisted ) {
			$this->model_version_markers[ $marker ] = true;
		}

		return $persisted;
	}

	/**
	 * Derives the model version governing a license: a baseline from the entry shape, pushed up by
	 * consecutive reached markers. Never stored in the entry (old copies strip entry fields).
	 *
	 * @since 1.25.0
	 *
	 * @param string $license_key The license key.
	 * @param array  $entry       The stored license entry.
	 *
	 * @return int
	 */
	private function effective_model_version( $license_key, array $entry ) {
		// At runtime add_missing_license_scopes() stamps `scope` onto every entry, so the surviving
		// v1 discriminator is the legacy flag (display truthiness; the v2 transition itself mutates
		// only on a literal true — non-canonical values keep their grace and settle vacuously).
		$version = ( isset( $entry['scope'] ) && empty( $entry['legacy'] ) ) ? 2 : 1;

		while ( $version < self::LICENSE_MODEL_VERSION && $this->is_model_version_reached( $license_key, $version + 1 ) ) {
			++$version;
		}

		return $version;
	}

	/**
	 * Transitions the license model from v1 to v2: ends the pre-scoping network-wide grace.
	 *
	 * Transitions MUST be pure, replayable shape-mutations of fields old copies preserve; store-
	 * sourced data is never transition output (it rides the recheck save path).
	 *
	 * @since 1.25.0
	 *
	 * @param string $license_key The license key.
	 * @param array  $entry       The entry to mutate.
	 *
	 * @return bool Whether the transition applied.
	 */
	private function transition_model_to_v2( $license_key, array &$entry ) {
		if ( true !== ( $entry['legacy'] ?? null ) ) {
			return false;
		}

		unset( $entry['legacy'] );

		return true;
	}

	/**
	 * Climbs a license from one model version to another, marker-before-mutate at every step.
	 *
	 * @since 1.25.0
	 *
	 * @param string $license_key The license key.
	 * @param array  $entry       The entry to advance; modified in place.
	 * @param int    $from        Current effective version.
	 * @param int    $to          Target version (already capped at LICENSE_MODEL_VERSION).
	 *
	 * @return void
	 */
	private function apply_model_transitions( $license_key, array &$entry, $from, $to ) {
		for ( $v = $from + 1; $v <= $to; $v++ ) {
			$method = "transition_model_to_v{$v}";

			// Defense-in-depth only: $to is capped at LICENSE_MODEL_VERSION, so a missing method
			// here means a broken release, not an older copy.
			if ( ! method_exists( $this, $method ) ) {
				return;
			}

			$probe   = $entry;
			$applied = $this->$method( $license_key, $probe );

			// The marker records that v{$v} was EVALUATED — written for the vacuous (predicate
			// refused, nothing mutated) case too, or a settled key would stay below the floor and
			// keep the poll firing forever.
			if ( ! $this->mark_model_version_reached( $license_key, $v ) ) {
				return;
			}

			if ( $applied ) {
				$entry = $probe;

				LoggerFramework::get_instance()->info( 'License ending in ' . substr( (string) $license_key, -4 ) . " advanced to model v{$v} (store floor)." );
			}
		}
	}

	/**
	 * Re-applies already-reached transitions to an entry in memory (read path; writes no markers).
	 *
	 * @since 1.25.0
	 *
	 * @param string $license_key The license key.
	 * @param array  $entry       The entry; modified in place.
	 *
	 * @return void
	 */
	private function apply_reached_transitions( $license_key, array &$entry ) {
		$effective = $this->effective_model_version( $license_key, $entry );

		for ( $v = 2; $v <= $effective; $v++ ) {
			$method = "transition_model_to_v{$v}";

			if ( method_exists( $this, $method ) ) {
				$this->$method( $license_key, $entry );
			}
		}
	}

	/**
	 * Remembers the highest enforcement floor the store has published. Monotonic.
	 *
	 * @since 1.25.0
	 *
	 * @param int $floor The floor from a successful check response.
	 *
	 * @return void
	 */
	private function remember_model_floor( $floor ) {
		if ( $floor > (int) get_site_option( 'gk_licenses_last_seen_model_floor', 0 ) ) {
			update_site_option( 'gk_licenses_last_seen_model_floor', (int) $floor );
		}
	}

	/**
	 * Advances a rechecked entry toward the store's enforcement floor.
	 *
	 * Fail-open: only a literal-true success carrying an integer floor ≥ 1 advances anything, and
	 * the climb is capped at this copy's own capability.
	 *
	 * @since 1.25.0
	 *
	 * @param string $license_key      The license key.
	 * @param array  $fresh_license    Rechecked license data (still carries `_raw`).
	 * @param array  $existing_license Stored license entry.
	 * @param array  $prepared_license Entry prepared for storage; modified in place.
	 *
	 * @return void
	 */
	private function maybe_advance_model_version( $license_key, array $fresh_license, array $existing_license, array &$prepared_license ) {
		if ( true !== ( $fresh_license['_raw']['success'] ?? null ) ) {
			return;
		}

		$floor = $fresh_license['_raw']['enforced_model_version'] ?? null;

		if ( ! is_int( $floor ) || $floor < 1 ) {
			return;
		}

		$this->remember_model_floor( $floor );

		$from = $this->effective_model_version( $license_key, $existing_license );
		$to   = min( $floor, self::LICENSE_MODEL_VERSION );

		if ( $from >= $to ) {
			return;
		}

		$this->apply_model_transitions( $license_key, $prepared_license, $from, $to );
	}

	/**
	 * Encrypts and saves licenses to one storage location.
	 *
	 * @since 1.25.0
	 *
	 * @param string $scope         Storage to write: 'network' or 'site'.
	 * @param array  $licenses_data Entries to store.
	 *
	 * @return bool
	 */
	private function write_license_storage( $scope, array $licenses_data ) {
		try {
			$encrypted = Encryption::get_instance()->encrypt( wp_json_encode( $licenses_data ) ?: '' );
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( 'Failed to encrypt licenses data: ' . $e->getMessage() );

			return false;
		}

		if ( self::SCOPE_SITE === $scope ) {
			return update_option( Framework::ID, $encrypted );
		}

		return update_site_option( Framework::ID, $encrypted );
	}

	/**
	 * Saves license data in the database.
	 *
	 * @since 1.0.0
	 *
	 * @param array $licenses_data Licenses data.
	 *
	 * @return bool
	 */
	public function save_licenses_data( array $licenses_data ) {
		// Build the sort key 1:1 with the entries so a missing `expiry` can never shorten the array and fatal array_multisort().
		$expiry_dates = array_map(
			static function ( $license ) {
				return is_array( $license ) ? ( $license['expiry'] ?? null ) : null;
			},
			$licenses_data
		);

		array_multisort( $licenses_data, SORT_ASC, $expiry_dates );

		if ( ! is_multisite() ) {
			$this->licenses_data = $licenses_data;

			return $this->write_license_storage( self::SCOPE_NETWORK, $licenses_data );
		}

		$network_licenses_to_save = [];
		$site_licenses_to_save    = [];

		foreach ( $licenses_data as $key => $license ) {
			// Entries without a scope (e.g., rewritten by an older Foundation copy) stay where they were read from.
			$scope = $license['scope'] ?? ( $this->license_storage_scopes[ $key ] ?? self::SCOPE_NETWORK );

			if ( self::SCOPE_SITE === $scope ) {
				$site_licenses_to_save[ $key ]        = $license;
				$this->license_storage_scopes[ $key ] = self::SCOPE_SITE;
			} else {
				$network_licenses_to_save[ $key ]     = $license;
				$this->license_storage_scopes[ $key ] = self::SCOPE_NETWORK;
			}
		}

		$this->licenses_data         = $licenses_data;
		$this->licenses_data_blog_id = get_current_blog_id();

		$network_saved = $this->write_license_storage( self::SCOPE_NETWORK, $network_licenses_to_save );
		$site_saved    = $this->write_license_storage( self::SCOPE_SITE, $site_licenses_to_save );

		$this->update_site_licenses_mirror( $site_licenses_to_save );

		return $network_saved || $site_saved;
	}

	/**
	 * Ingests licenses delivered by an account connection.
	 *
	 * Fired on `gk/foundation/connection/licenses/received` with an array payload. Existing keys are
	 * adopted in place (scope/url preserved, no re-activation); new keys are written with the payload
	 * scope; hardcoded-constant licenses are immutable and skipped.
	 *
	 * @since 1.26.0
	 * @since TBD Sync server notices delivered by the connection.
	 *
	 * @param array $payload {
	 *     Received-licenses payload.
	 *
	 *     @type array  $licenses      Received license entries, keyed by license key or a list.
	 *     @type string $scope         Connection scope: 'network' or 'site'.
	 *     @type string $account_email Connected account email.
	 *     @type bool   $complete      Whether the licenses are the complete granted set.
	 * }
	 *
	 * @return void
	 */
	public function ingest_received_licenses( $payload ) {
		if ( ! is_array( $payload ) ) {
			return;
		}

		$received = ( isset( $payload['licenses'] ) && is_array( $payload['licenses'] ) ) ? $payload['licenses'] : [];
		$complete = ! empty( $payload['complete'] );

		if ( empty( $received ) && ! $complete ) {
			return;
		}

		// Fail closed: only an explicit network scope writes network-wide. A malformed/missing scope on this
		// public hook must never silently widen an ingest to the whole network.
		$scope = self::SCOPE_NETWORK === ( $payload['scope'] ?? '' ) ? self::SCOPE_NETWORK : self::SCOPE_SITE;

		$changed          = $this->apply_received_licenses( $received, $scope );
		$removed_licenses = [];

		// A complete payload is the connection's full granted set: connection-sourced rows in this
		// scope the account no longer grants are stale (access removed on the account) and drop here.
		if ( $complete ) {
			$changed = $this->prune_absent_connection_licenses( $received, $scope, $removed_licenses ) || $changed;
		}

		// Notice storage can drift independently of license rows, so always reconcile a usable pull.
		// Include every surviving license so one license cannot remove a notice another still delivers.
		$this->sync_server_notices(
			array_replace( $this->get_licenses_data(), $received ),
			ServerNoticeHandler::SOURCE_LICENSE
		);

		if ( ! empty( $removed_licenses ) ) {
			$this->reconcile_removed_server_notices( $removed_licenses, $this->get_licenses_data() );
		}

		if ( ! $changed ) {
			return;
		}

		$this->flush_update_plugins_transients();
	}

	/**
	 * Drops connection-sourced rows in a scope whose account license id is absent from a complete grant set.
	 *
	 * @since 1.26.0
	 * @since TBD Added removed-license collection.
	 *
	 * @param array  $received        Received license entries (the connection's complete granted set).
	 * @param string $scope           Connection scope being ingested.
	 * @param array  $removed_licenses Removed license entries.
	 *
	 * @return bool Whether anything was removed.
	 */
	private function prune_absent_connection_licenses( array $received, $scope, array &$removed_licenses = [] ) {
		$granted_ids = [];

		foreach ( $received as $entry ) {
			if ( is_array( $entry ) && isset( $entry['id'] ) && is_numeric( $entry['id'] ) ) {
				$granted_ids[ (int) $entry['id'] ] = true;
			}
		}

		$licenses_data = $this->get_licenses_data();
		$changed       = false;

		foreach ( $licenses_data as $license_key => $license ) {
			if ( ! is_array( $license ) || 'connection' !== ( $license['source'] ?? '' ) || ! empty( $license['hardcoded'] ) ) {
				continue;
			}

			if ( ( $license['scope'] ?? self::SCOPE_SITE ) !== $scope ) {
				continue;
			}

			// Rows without an account id cannot be matched against the grant set; leave them alone.
			if ( ! isset( $license['connection_license_id'] ) || isset( $granted_ids[ (int) $license['connection_license_id'] ] ) ) {
				continue;
			}

			$removed_licenses[ $license_key ] = $license;

			unset( $licenses_data[ $license_key ] );

			$changed = true;
		}

		if ( $changed ) {
			$this->save_licenses_data( $licenses_data );
		}

		return $changed;
	}

	/**
	 * Merges received licenses into storage and persists them.
	 *
	 * @since 1.26.0
	 *
	 * @param array  $received Received license entries.
	 * @param string $scope    Connection scope for new entries.
	 *
	 * @return bool Whether anything changed.
	 */
	private function apply_received_licenses( array $received, $scope ) {
		$licenses_data = $this->get_licenses_data();
		$changed       = false;

		foreach ( $received as $key => $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}

			$entry_key   = $entry['key'] ?? '';
			$license_key = ( is_string( $key ) && '' !== $key ) ? $key : ( is_scalar( $entry_key ) ? (string) $entry_key : '' );

			if ( '' === $license_key ) {
				continue;
			}

			$existing = $licenses_data[ $license_key ] ?? null;

			// Hardcoded-constant licenses are immutable; never mutate or overwrite them.
			if ( is_array( $existing ) && ! empty( $existing['hardcoded'] ) ) {
				continue;
			}

			// A site ingest must never alter a network-scoped row (network supersedes site). Leave the
			// network license untouched; its site copy is superseded by the union on save.
			if ( self::SCOPE_SITE === $scope && self::SCOPE_NETWORK === ( $this->license_storage_scopes[ $license_key ] ?? '' ) ) {
				continue;
			}

			// Normalize the raw store payload into the internal shape; skip incomplete entries so one bad server entry never aborts ingest or WSODs.
			$normalized = $this->normalize_store_license_entry( $license_key, $entry );

			if ( null === $normalized ) {
				continue;
			}

			// Preserve the server's on-this-site activation state when present (not part of the store shape).
			// Accept it only as an array so a malformed scalar never reaches consumers expecting a shape.
			if ( isset( $entry['activation'] ) && is_array( $entry['activation'] ) ) {
				$normalized['activation'] = $entry['activation'];
			}

			// Preserve the account license id (distinct from product ids) so the frontend can route this
			// license's activate/deactivate back through the signed channel and release the account grant.
			if ( isset( $entry['id'] ) && is_numeric( $entry['id'] ) ) {
				$normalized['connection_license_id'] = (int) $entry['id'];
			}

			$entry = $normalized;

			// The connection is a data source, not the model-version authority: strip any received `legacy` so
			// the existing value survives the adopt merge and new entries defer to effective_model_version().
			unset( $entry['legacy'] );

			if ( is_array( $existing ) ) {
				$entry = array_merge( $existing, $entry );

				$existing_scope = $existing['scope'] ?? $scope;

				// A network ingest promotes a site-scoped key to network-wide (network supersedes site).
				// Otherwise adopt in place: keep the local scope/url and do not re-activate.
				if ( is_multisite() && self::SCOPE_NETWORK === $scope && self::SCOPE_SITE === $existing_scope ) {
					$entry['scope'] = self::SCOPE_NETWORK;
					$entry['url']   = $this->resolve_license_url( self::SCOPE_NETWORK, $license_key );
				} else {
					$entry['scope'] = $existing_scope;
					$entry['url']   = $existing['url'] ?? $this->resolve_license_url( $entry['scope'], $license_key );
				}
			} else {
				$entry['scope'] = $scope;
				$entry['url']   = $this->resolve_license_url( $scope, $license_key );
			}

			$entry['key']    = $license_key;
			$entry['source'] = 'connection';

			// Never let the server forge hardcoded provenance onto a connection-sourced license.
			unset( $entry['hardcoded'] );

			$licenses_data[ $license_key ] = $entry;
			$changed                       = true;
		}

		if ( ! $changed ) {
			return false;
		}

		$this->save_licenses_data( $licenses_data );

		return true;
	}

	/**
	 * Removes connection-sourced licenses from local storage by their account license id.
	 *
	 * Called when licenses are deactivated over the signed channel: the account grant is released and
	 * the license returns to the "available" catalog, so the now-inactive local row must be dropped
	 * (the empty re-pull cannot refresh its stale on-this-site activation state).
	 *
	 * @since 1.26.0
	 * @since TBD Reconcile notices supplied by removed licenses.
	 *
	 * @param array $account_license_ids Account license ids that were deactivated.
	 *
	 * @return void
	 */
	public function forget_connection_licenses( array $account_license_ids ) {
		$ids = array_filter( array_map( 'intval', $account_license_ids ) );

		if ( empty( $ids ) ) {
			return;
		}

		$ids           = array_flip( $ids );
		$licenses_data = $this->get_licenses_data();
		$changed       = false;
		$removed       = [];

		foreach ( $licenses_data as $license_key => $license ) {
			if ( ! is_array( $license ) || 'connection' !== ( $license['source'] ?? '' ) ) {
				continue;
			}

			if ( ! isset( $license['connection_license_id'] ) || ! isset( $ids[ (int) $license['connection_license_id'] ] ) ) {
				continue;
			}

			$removed[ $license_key ] = $license;

			unset( $licenses_data[ $license_key ] );

			$changed = true;
		}

		if ( ! $changed ) {
			return;
		}

		$this->flush_update_plugins_transients();

		$this->save_licenses_data( $licenses_data );

		$this->reconcile_removed_server_notices( $removed, $licenses_data );
	}

	/**
	 * Removes connection rows and reconciles their notices on explicit disconnect.
	 *
	 * Connection rows without an account license ID are intentionally not pruned by ordinary
	 * complete pulls, but their notice contributions must still end with the connection.
	 *
	 * @since TBD
	 *
	 * @param array $context Disconnected-connection context.
	 *
	 * @return void
	 */
	public function reconcile_disconnected_server_notices( $context ): void {
		if ( ! is_array( $context ) ) {
			return;
		}

		$scope         = self::SCOPE_NETWORK === ( $context['scope'] ?? '' ) ? self::SCOPE_NETWORK : self::SCOPE_SITE;
		$licenses_data = $this->get_licenses_data();
		$remaining     = $licenses_data;
		$disconnected  = [];

		foreach ( $licenses_data as $license_key => $license ) {
			if ( ! is_array( $license ) || 'connection' !== ( $license['source'] ?? '' ) ) {
				continue;
			}

			if ( ( $license['scope'] ?? self::SCOPE_SITE ) !== $scope ) {
				continue;
			}

			$disconnected[ $license_key ] = $license;

			unset( $remaining[ $license_key ] );
		}

		if ( empty( $disconnected ) ) {
			return;
		}

		$this->save_licenses_data( $remaining );
		$this->flush_update_plugins_transients();
		$this->reconcile_removed_server_notices( $disconnected, $remaining );
	}

	/**
	 * Mirrors the current blog's site-scope licenses into per-blog network options.
	 *
	 * The `update_plugins` transient is network-wide and can be rebuilt from any blog's cron, so
	 * update/download data must be readable outside the owning blog's context. Each blog
	 * writes ONLY its own row (plus an id index), so concurrent blog crons cannot clobber each other.
	 *
	 * @since 1.25.0
	 *
	 * @param array $site_licenses_to_save The current site's own license entries.
	 *
	 * @return void
	 */
	private function update_site_licenses_mirror( array $site_licenses_to_save ) {
		if ( ! is_multisite() ) {
			return;
		}

		$this->site_licenses_mirror_cache = null;

		$blog_id  = get_current_blog_id();
		$mirrored = $site_licenses_to_save;

		// Minimize mirrored data: the mirror only needs what update/download resolution reads.
		foreach ( $mirrored as $key => $license ) {
			unset( $mirrored[ $key ]['name'], $mirrored[ $key ]['email'] );
		}

		$index = get_site_option( self::SITE_LICENSES_MIRROR_ID );
		$index = is_array( $index ) ? $index : [];

		if ( ! $mirrored ) {
			delete_site_option( self::SITE_LICENSES_MIRROR_ID . '_' . $blog_id );

			if ( in_array( $blog_id, $index, true ) ) {
				update_site_option( self::SITE_LICENSES_MIRROR_ID, array_values( array_diff( $index, [ $blog_id ] ) ) );
			}

			return;
		}

		try {
			$encrypted = Encryption::get_instance()->encrypt( wp_json_encode( $mirrored ) ?: '' );
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( 'Failed to encrypt the site licenses mirror: ' . $e->getMessage() );

			return;
		}

		update_site_option( self::SITE_LICENSES_MIRROR_ID . '_' . $blog_id, $encrypted );

		if ( ! in_array( $blog_id, $index, true ) ) {
			$index[] = $blog_id;

			update_site_option( self::SITE_LICENSES_MIRROR_ID, $index );
		}
	}

	/**
	 * Reads the per-blog mirror rows of site-scope licenses.
	 *
	 * @since 1.25.0
	 *
	 * @return array Entries keyed by blog ID.
	 */
	private function read_site_licenses_mirror() {
		if ( ! is_multisite() ) {
			return [];
		}

		if ( is_array( $this->site_licenses_mirror_cache ) ) {
			return $this->site_licenses_mirror_cache;
		}

		$index = get_site_option( self::SITE_LICENSES_MIRROR_ID );

		if ( empty( $index ) || ! is_array( $index ) ) {
			return [];
		}

		$mirror = [];

		foreach ( $index as $blog_id ) {
			$row = get_site_option( self::SITE_LICENSES_MIRROR_ID . '_' . (int) $blog_id );

			if ( empty( $row ) ) {
				continue;
			}

			$row = json_decode( Encryption::get_instance()->decrypt( $row ) ?: '', true );

			if ( is_array( $row ) && $row ) {
				$mirror[ (int) $blog_id ] = $row;
			}
		}

		$this->site_licenses_mirror_cache = $mirror;

		return $mirror;
	}

	/**
	 * Removes a deleted blog's entries from the site licenses mirror.
	 *
	 * @since 1.25.0
	 *
	 * @param int $blog_id Deleted blog ID.
	 *
	 * @return void
	 */
	public function remove_blog_from_site_licenses_mirror( $blog_id ) {
		delete_site_option( self::SITE_LICENSES_MIRROR_ID . '_' . $blog_id );

		$index = get_site_option( self::SITE_LICENSES_MIRROR_ID );

		if ( is_array( $index ) && in_array( $blog_id, $index, true ) ) {
			update_site_option( self::SITE_LICENSES_MIRROR_ID, array_values( array_diff( $index, [ $blog_id ] ) ) );
		}
	}

	/**
	 * Returns every license entry on the install: the current context's own plus, on multisite,
	 * other blogs' site-scope licenses (from the mirror).
	 *
	 * For install-level consumers (plugin updates, download resolution, support identification).
	 * Per-subsite management reads {@see get_licenses_data()} instead.
	 *
	 * @since 1.25.0
	 *
	 * @return array
	 */
	public function get_all_licenses_data() {
		$licenses = $this->get_licenses_data();

		if ( ! is_multisite() ) {
			return $licenses;
		}

		$current_blog_id = get_current_blog_id();

		foreach ( $this->read_site_licenses_mirror() as $blog_id => $blog_licenses ) {
			if ( (int) $blog_id === $current_blog_id || ! is_array( $blog_licenses ) ) {
				continue;
			}

			foreach ( $blog_licenses as $key => $license ) {
				if ( isset( $licenses[ $key ] ) || ! is_array( $license ) ) {
					continue;
				}

				// A dormant blog never rewrites its mirror row; skip rows whose expiry has since lapsed.
				if ( ! empty( $license['expiry'] ) && $this->is_expired_license( $license['expiry'] ) ) {
					continue;
				}

				$licenses[ $key ] = $license;
			}
		}

		return $licenses;
	}

	/**
	 * Returns the current site's own site-scoped license entries.
	 *
	 * On multisite, network-scoped rows belong to the network licenser and are excluded; on a single
	 * site every license is the site's own.
	 *
	 * @since 1.26.0
	 *
	 * @return array
	 */
	public function get_site_scoped_licenses_data() {
		$licenses = $this->get_licenses_data();

		if ( ! is_multisite() ) {
			return $licenses;
		}

		$site_licenses = [];

		foreach ( $licenses as $key => $license ) {
			// Classify by the physical storage slot (authoritative); an unknown scope counts as network.
			if ( self::SCOPE_SITE === ( $this->license_storage_scopes[ $key ] ?? self::SCOPE_NETWORK ) ) {
				$site_licenses[ $key ] = $license;
			}
		}

		return $site_licenses;
	}

	/**
	 * Returns an object keyed by product ID and associated licenses.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key_by (optional) Key (product ID or text domain) to use for the returned array.
	 *                       Choices: 'id' or 'text_domain'. Default: 'id'.
	 *
	 * @return array
	 */
	public function get_product_license_map( $key_by = 'id' ) {
		return $this->build_product_license_map( $this->get_licenses_data(), $key_by );
	}

	/**
	 * Returns an object keyed by product ID and associated licenses, built from every license on the
	 * install ({@see get_all_licenses_data()}).
	 *
	 * @since 1.25.0
	 *
	 * @param string $key_by (optional) Key (product ID or text domain) to use for the returned array.
	 *                       Choices: 'id' or 'text_domain'. Default: 'id'.
	 *
	 * @return array
	 */
	public function get_all_product_license_map( $key_by = 'id' ) {
		return $this->build_product_license_map( $this->get_all_licenses_data(), $key_by );
	}

	/**
	 * Builds a product-to-license-keys map from license entries.
	 *
	 * @since 1.25.0
	 *
	 * @param array  $licenses_data License entries.
	 * @param string $key_by        Key (product ID or text domain) to use for the returned array.
	 *
	 * @return array
	 */
	private function build_product_license_map( array $licenses_data, $key_by = 'id' ) {
		$product_license_map = [];

		foreach ( $licenses_data as $license_key => $license_data ) {
			if ( empty( $license_data['products'] ) ) {
				continue;
			}

			foreach ( $license_data['products'] as $product_id => $product_data ) {
				switch ( $key_by ) {
					case 'id':
						$key = $product_id;
						break;
					default:
						$key = $product_data['text_domain'];
						break;
				}

				if ( empty( $product_license_map[ $key ] ) ) {
					$product_license_map[ $key ] = [];
				}

				$product_license_map[ $key ][] = $license_key;
			}
		}

		return $product_license_map;
	}

	/**
	 * Returns license status message based on the EDD status code.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status EDD status code.
	 *
	 * @return mixed
	 */
	public function get_license_key_status_message( $status ) {
		$statuses = [
			'site_inactive'       => esc_html__( 'The license key is valid, but it has not been activated for this site.', 'gk-foundation' ),
			'inactive'            => esc_html__( 'The license key is valid, but it has not been activated for this site.', 'gk-foundation' ),
			'no_activations_left' => esc_html__( 'This license has reached its activation limit.', 'gk-foundation' ),
			'deactivated'         => esc_html__( 'This license has been deactivated.', 'gk-foundation' ),
			'valid'               => esc_html__( 'This license key is valid and active.', 'gk-foundation' ),
			'invalid'             => esc_html__( 'This license key is invalid.', 'gk-foundation' ),
			'missing'             => esc_html__( 'This license key is invalid.', 'gk-foundation' ),
			'disabled'            => esc_html__( 'This license key has been disabled.', 'gk-foundation' ),
			'revoked'             => esc_html__( 'This license key has been revoked.', 'gk-foundation' ),
			'expired'             => esc_html__( 'This license key has expired.', 'gk-foundation' ),
		];

		if ( empty( $statuses[ $status ] ) ) {
			LoggerFramework::get_instance()->warning( 'Unknown license status: ' . $status );

			return esc_html__( 'License status could not be determined.', 'gk-foundation' );
		}

		return $statuses[ $status ];
	}

	/**
	 * Returns a short license status label for display.
	 *
	 * @since 1.19.0
	 *
	 * @param string $status EDD status code.
	 *
	 * @return string
	 */
	private function get_license_key_status_label( $status ) {
		$statuses = [
			'site_inactive'       => esc_html__( 'Not Activated for This Site', 'gk-foundation' ),
			'inactive'            => esc_html__( 'Not Activated', 'gk-foundation' ),
			'no_activations_left' => esc_html__( 'Limit Reached', 'gk-foundation' ),
			'deactivated'         => esc_html__( 'Deactivated', 'gk-foundation' ),
			'valid'               => esc_html__( 'Active', 'gk-foundation' ),
			'invalid'             => esc_html__( 'Invalid', 'gk-foundation' ),
			'missing'             => esc_html__( 'Invalid', 'gk-foundation' ),
			'disabled'            => esc_html__( 'Disabled', 'gk-foundation' ),
			'revoked'             => esc_html__( 'Revoked', 'gk-foundation' ),
			'expired'             => esc_html__( 'Expired', 'gk-foundation' ),
		];

		return $statuses[ $status ] ?? esc_html__( 'Unknown', 'gk-foundation' );
	}

	/**
	 * Returns the URL the store should record/check for a license scope.
	 *
	 * @since 1.25.0
	 *
	 * @param string $scope       License scope. Accepts 'network' or 'site'.
	 * @param string $license_key (optional) License key the URL is being resolved for. Default: empty.
	 *
	 * @return string
	 */
	public function resolve_license_url( $scope, $license_key = '' ) {
		$url = self::SCOPE_NETWORK === $scope && is_multisite() ? network_home_url() : home_url();

		/**
		 * Modifies the URL sent to the store for a license scope.
		 *
		 * @filter `gk/foundation/licenses/activation-url`
		 *
		 * @since 1.25.0
		 *
		 * @param string $url         The resolved URL. Default: network_home_url() for network scope, home_url() otherwise.
		 * @param string $scope       The license scope. Accepts 'network' or 'site'.
		 * @param string $license_key The license key the URL is being resolved for; empty when not key-specific.
		 */
		return apply_filters( 'gk/foundation/licenses/activation-url', $url, $scope, $license_key );
	}

	/**
	 * Whether a URL's host is a bare public IP or localhost — an address a real site is essentially never
	 * activated under, and the fingerprint of a Host-derived WP_HOME serving the raw origin. Private and
	 * reserved IP ranges (intranet, loopback) are intentionally excluded.
	 *
	 * @since 1.26.0
	 *
	 * @param string $url URL to inspect.
	 *
	 * @return bool
	 */
	private function is_bare_origin_host( $url ) {
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );

		if ( '' === $host ) {
			return false;
		}

		if ( 'localhost' === $host ) {
			return true;
		}

		// wp_parse_url() returns IPv6 hosts bracketed ("[2600:1f14::1]"); filter_var() needs them stripped.
		$host = trim( $host, '[]' );

		return false !== filter_var( $host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
	}

	/**
	 * Normalizes and validates the scope for a new license activation.
	 *
	 * @since 1.25.0
	 *
	 * @param string|null $scope Requested scope ('network' or 'site'), or null for the legacy default.
	 *
	 * @return string
	 */
	private function normalize_activation_scope( $scope ) {
		if ( ! is_multisite() ) {
			$scope = self::SCOPE_SITE;
		} elseif ( ! in_array( $scope, [ self::SCOPE_NETWORK, self::SCOPE_SITE ], true ) ) {
			// Programmatic callers without an explicit scope (hardcoded licenses, legacy migration) keep the pre-scoping network-wide behavior.
			$scope = self::SCOPE_NETWORK;
		}

		return $scope;
	}

	/**
	 * Returns the store/license API base URL: the GK_STORE_URL override, or the default. The override
	 * lets a tester point license checks and the product catalog at a staging or local store.
	 *
	 * @since 1.26.0
	 *
	 * @return string
	 */
	public static function store_url(): string {
		return defined( 'GK_STORE_URL' ) ? untrailingslashit( (string) GK_STORE_URL ) : self::STORE_API_ENDPOINT;
	}

	/**
	 * Calls the Store API and normalizes the license response.
	 *
	 * @since 1.15.0
	 *
	 * @param string       $path    API endpoint path (e.g., '/licenses/check').
	 * @param string|array $license License key or array of license keys.
	 * @param array        $extra   Additional payload fields (e.g., site_data, a scope-resolved url).
	 *
	 * @throws Exception
	 *
	 * @return array Normalized response data.
	 */
	private function call_store_api( string $path, $license, array $extra = [] ) {
		$multiple_licenses = is_array( $license );

		$payload = array_merge(
            [
				'url'         => is_multisite() ? network_home_url() : home_url(),
				'api_version' => self::STORE_API_VERSION,
				'license'     => $license,
				'environment' => CoreHelpers::get_environment_type(),
			],
            $extra
        );

		try {
			$response = Helpers::query_api(
				self::store_url() . $path,
				$payload
			);
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}

		// Response can be a multidimensional array when checking multiple licenses.
		$response = $multiple_licenses ? $response : [ $license => $response ];

		// When checking multiple licenses (i.e., an array of keys) but there is only 1 key in the array, the response is an associative array that needs to be converted to a multidimensional array keyed by the license key.
		if ( $multiple_licenses && 1 === count( $license ) ) {
			$response = [ $license[0] => $response ];
		}

		$normalized_response_data = [];

		$license_keys = $multiple_licenses ? $license : [ $license ];

		foreach ( (array) $response as $license_key => $data ) {
			$normalized_license_data = $this->normalize_store_license_entry( (string) $license_key, (array) $data );

			if ( null === $normalized_license_data ) {
				throw new Exception( esc_html__( 'License data received from the API is incomplete.', 'gk-foundation' ) );
			}

			if ( ! in_array( $license_key, $license_keys, true ) ) {
				LoggerFramework::get_instance()->warning( "EDD API returned unknown license key in response: {$license_key}" );

				continue;
			}

			if ( $multiple_licenses ) {
				$normalized_response_data[ $license_key ] = $normalized_license_data;
			} else {
				$normalized_response_data = $normalized_license_data;
			}
		}

		return $normalized_response_data;
	}

	/**
	 * Normalizes a raw store `/licenses/check` entry into Foundation's internal license shape.
	 *
	 * @since 1.26.0
	 *
	 * @param string $license_key License key.
	 * @param array  $data        Raw store entry.
	 *
	 * @return array|null Normalized entry, or null when the raw entry is incomplete.
	 */
	private function normalize_store_license_entry( string $license_key, array $data ) {
		if ( ! isset( $data['success'] ) || ! isset( $data['license'] ) || ! isset( $data['checksum'] ) ) {
			return null;
		}

		// A signature-valid but malformed server payload must never fatal ingest: require the identifying
		// fields to be scalar before use so a stray array can't trigger a TypeError or an illegal-offset error.
		if ( ! is_scalar( $data['license'] ) || ! is_scalar( $data['checksum'] ) ) {
			return null;
		}

		// `expires` reaches strtotime(), which throws a TypeError on a non-scalar in PHP 8; coerce anything
		// non-scalar to null so a malformed `expires` (e.g. an array) is skipped rather than fatal.
		$expires = $data['expires'] ?? null;

		if ( null !== $expires && ! is_scalar( $expires ) ) {
			$expires = null;
		}

		if ( ! $data['success'] && empty( $expires ) ) {
			$expiry = null;
		} else {
			$expiry = ! empty( $expires ) ? strtotime( (string) $expires, current_time( 'timestamp' ) ) : null; // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp
			$expiry = $expiry ?: $expires;
		}

		$normalized_license_data = [
			'name'             => $data['customer_name'] ?? null,
			'email'            => $data['customer_email'] ?? null,
			'license_name'     => $data['license_name'] ?? null,
			'status'           => $data['license'],
			'expiry'           => $expiry,
			'key'              => $license_key,
			'products'         => [],
			'license_limit'    => $data['license_limit'] ?? null,
			'site_count'       => $data['site_count'] ?? null,
			'activations_left' => $data['activations_left'] ?? null,
			'_raw'             => $data,
		];

		if ( ! empty( $data['products'] ) && is_array( $data['products'] ) ) {
			foreach ( $data['products'] as $product ) {
				if ( ! is_array( $product ) ) {
					continue;
				}

				$product_id = $product['id'] ?? null;
				$files      = $product['files'] ?? null;
				$file       = ( is_array( $files ) && isset( $files[0] ) && is_array( $files[0] ) ) ? ( $files[0]['file'] ?? null ) : null;

				if ( ! is_scalar( $product_id ) || (int) $product_id <= 0 ) {
					continue;
				}

				if ( empty( $file ) || ! is_scalar( $file ) || empty( $product['text_domain'] ) || ! is_scalar( $product['text_domain'] ) ) {
					continue;
				}

				$product_id = (int) $product_id;

				$normalized_license_data['products'][ $product_id ] = [
					'id'                 => $product_id,
					'text_domain'        => $product['text_domain'],
					'text_domains'       => is_array( $product['text_domains'] ?? null ) ? $product['text_domains'] : [],
					'text_domain_legacy' => is_scalar( $product['text_domain_legacy'] ?? null ) ? (string) $product['text_domain_legacy'] : '',
					'download'           => $file,
					'channels'           => is_array( $product['channels'] ?? null ) ? $product['channels'] : [],
					'product_notices'    => is_array( $product['product_notices'] ?? null ) ? $product['product_notices'] : [],
					'integrity'          => is_array( $product['integrity'] ?? null ) ? $product['integrity'] : [],
				];
			}
		}

		return $normalized_license_data;
	}

	/**
	 * Builds the base request payload sent with every license check — direct (manual key) or
	 * account-connected — so the store receives the site's telemetry identically on both channels.
	 * Add a field here once and both paths carry it.
	 *
	 * @since TBD
	 *
	 * @param array $extra Channel-specific fields (e.g. url, scope) merged over the base payload.
	 *
	 * @return array
	 */
	public function get_license_check_payload( array $extra = [] ) {
		return array_merge( [ 'site_data' => $this->get_site_data() ], $extra );
	}

	/**
	 * Checks license key for validity.
	 *
	 * @since 1.0.0
	 * @since 1.25.0 Added the $url parameter.
	 *
	 * @param string      $license_key License key.
	 * @param string|null $url         (optional) URL to check the license against. Default: the store API default for this install.
	 *
	 * @throws Exception
	 *
	 * @return array License data.
	 */
	public function check_license( $license_key, $url = null ) {
		$extra = $this->get_license_check_payload( $url ? [ 'url' => $url ] : [] );

		try {
			return $this->call_store_api( '/licenses/check', $license_key, $extra );
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}
	}

	/**
	 * Checks multiples license keys for validity.
	 *
	 * @since 1.0.0
	 * @since 1.25.0 Added the $url and $scope parameters.
	 *
	 * @param array       $license_keys License keys.
	 * @param string|null $url          (optional) URL to check the licenses against. Default: the store API default for this install.
	 * @param string|null $scope        (optional) License scope the check runs for ('network' or 'site'); sent to the store as telemetry.
	 *
	 * @throws Exception
	 *
	 * @return array Licenses data.
	 */
	public function check_licenses( array $license_keys, $url = null, $scope = null ) {
		$extra = [];

		if ( $url ) {
			$extra['url'] = $url;
		}

		if ( in_array( $scope, [ self::SCOPE_NETWORK, self::SCOPE_SITE ], true ) ) {
			$extra['scope'] = $scope;
		}

		$extra = $this->get_license_check_payload( $extra );

		try {
			return $this->call_store_api( '/licenses/check', $license_keys, $extra );
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}
	}

	/**
	 * Ajax request wrapper for the activate_license() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products:array,licenses:array}
	 */
	public function ajax_activate_license( array $payload ) {
		if ( ! Framework::get_instance()->current_user_can( 'manage_licenses' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		if ( empty( $payload['key'] ) ) {
			throw new Exception( esc_html__( 'Missing license key.', 'gk-foundation' ) );
		}

		$scope = $this->get_activation_scope_from_request( $payload );

		$this->activate_license( $payload['key'], $scope );

		return Framework::get_instance()->ajax_get_app_data( [ 'is_network_admin' => self::SCOPE_NETWORK === $scope ] );
	}

	/**
	 * Resolves and authorizes the activation scope for a UI (Ajax) request.
	 *
	 * Network scope requires the `manage_network_options` capability; the request-supplied value is
	 * a hint, never an escalation path.
	 *
	 * @since 1.25.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception When network scope is requested without the required capability.
	 *
	 * @return string
	 */
	private function get_activation_scope_from_request( array $payload ) {
		if ( ! is_multisite() ) {
			return self::SCOPE_SITE;
		}

		$requested = $payload['scope'] ?? null;

		if ( self::SCOPE_NETWORK === $requested ) {
			if ( ! current_user_can( 'manage_network_options' ) ) {
				throw new Exception( esc_html__( 'You do not have a permission to activate a license for the entire network.', 'gk-foundation' ) );
			}

			return self::SCOPE_NETWORK;
		}

		if ( self::SCOPE_SITE === $requested ) {
			return self::SCOPE_SITE;
		}

		// Older UI bundles don't send a scope; preserve the legacy network-wide behavior when the user could choose it anyway.
		if ( current_user_can( 'manage_network_options' ) && CoreHelpers::is_network_admin() ) {
			return self::SCOPE_NETWORK;
		}

		return self::SCOPE_SITE;
	}

	/**
	 * Ajax request wrapper that re-activates an already-saved license for the current context.
	 *
	 * Used when a saved license is no longer activated for this site (e.g., it was deactivated from
	 * the account page, or the site's URL changed). Force-activates the site's current URL in place;
	 * it never releases any activation (releasing the saved URL would let a database clone deactivate a
	 * live site — see b74a08c7). On failure the saved license is left untouched.
	 *
	 * @since 1.25.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products:array,licenses:array}
	 */
	public function ajax_reactivate_license( array $payload ) {
		if ( ! Framework::get_instance()->current_user_can( 'manage_licenses' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		if ( empty( $payload['key'] ) ) {
			throw new Exception( esc_html__( 'Missing license key.', 'gk-foundation' ) );
		}

		$licenses_data = $this->get_licenses_data();

		$license_key = Encryption::get_instance()->decrypt( $payload['key'] );

		if ( empty( $licenses_data[ $license_key ] ) ) {
			throw new Exception( esc_html__( 'The license key is invalid.', 'gk-foundation' ) );
		}

		$license = $licenses_data[ $license_key ];
		$scope   = $license['scope'] ?? ( $this->license_storage_scopes[ $license_key ] ?? self::SCOPE_NETWORK );

		// A network-scope license covers every subsite; only a network admin may re-activate it.
		if ( is_multisite() && self::SCOPE_NETWORK === $scope && ! current_user_can( 'manage_network_options' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to activate a license for the entire network.', 'gk-foundation' ) );
		}

		// Force-activate the current URL in place, without deleting the saved entry first. A failed or
		// interrupted store call therefore leaves the stored license untouched, so the key is never lost.
		// Activates only the current URL — never releases another activation the license holds, which a
		// database clone (carrying the original's saved URL) could otherwise exploit to deactivate a live site.
		$this->activate_license( $license_key, $scope, true );

		return Framework::get_instance()->ajax_get_app_data( [ 'is_network_admin' => self::SCOPE_NETWORK === $scope ] );
	}

	/**
	 * Appends "what to do next" guidance to activation-limit error messages.
	 *
	 * @since 1.25.0
	 *
	 * @param string $message Error message from the store or the local status map.
	 *
	 * @return string
	 */
	private function maybe_add_activation_limit_guidance( $message ) {
		if ( false === stripos( $message, 'activation limit' ) && false === stripos( $message, 'no_activations_left' ) ) {
			return $message;
		}

		if ( CoreHelpers::is_cli() ) {
			return $message . ' ' . strtr(
				__( 'You can manage your activated sites at [account-url] or contact support at [support-url].', 'gk-foundation' ),
				[
					'[account-url]' => 'https://www.gravitykit.com/account/',
					'[support-url]' => 'https://www.gravitykit.com/support/',
				]
			);
		}

		return $message . ' ' . strtr(
			/* translators: placeholders in [brackets] are replaced with link markup and must not be translated. */
			esc_html__( 'You can manage your activated sites from your [account-link]GravityKit account[/account-link] or [support-link]contact support[/support-link].', 'gk-foundation' ),
			[
				'[account-link]'  => '<a class="underline" href="https://www.gravitykit.com/account/" target="_blank" rel="noopener noreferrer">',
				'[/account-link]' => '</a>',
				'[support-link]'  => '<a class="underline" href="https://www.gravitykit.com/support/" target="_blank" rel="noopener noreferrer">',
				'[/support-link]' => '</a>',
			]
		);
	}

	/**
	 * Activates license.
	 *
	 * @since 1.0.0
	 * @since 1.25.0 Added the $scope parameter (per-site vs network-wide activation on multisite).
	 * @since 1.26.0 Added the $force parameter (re-activation of an already-saved key).
	 *
	 * @param string      $license_key License key.
	 * @param string|null $scope       (optional) Activation scope. Accepts 'network' or 'site'. Default: 'network' on multisite, 'site' otherwise.
	 * @param bool        $force       (optional) Re-activate a key already stored for this site, bypassing the "already activated" guard. Default: false.
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function activate_license( $license_key, $scope = null, $force = false ) {
		if ( ! Framework::get_instance()->current_user_can( 'manage_licenses' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$scope = $this->normalize_activation_scope( $scope );

		// Network-wide activation is a network-admin decision; system flows (hardcoded/migration) express install-wide intent.
		if (
			is_multisite()
			&& self::SCOPE_NETWORK === $scope
			&& ! $this->is_system_operation
			&& ! CoreHelpers::is_cli()
			&& ! current_user_can( 'manage_network_options' )
		) {
			throw new Exception( esc_html__( 'You do not have a permission to activate a license for the entire network.', 'gk-foundation' ) );
		}

		$licenses_data = $this->get_licenses_data();

		// Re-activation (see ajax_reactivate_license) intentionally targets an already-saved key, so it skips
		// the duplicate guard below; every other caller still rejects re-activating a stored license.
		if ( ! $force && isset( $licenses_data[ $license_key ] ) ) {
			$stored_in = $this->license_storage_scopes[ $license_key ] ?? self::SCOPE_NETWORK;

			if ( is_multisite() && self::SCOPE_SITE === $scope && self::SCOPE_NETWORK === $stored_in ) {
				throw new Exception( esc_html__( 'This license is already active for the entire network and is managed by the network administrator.', 'gk-foundation' ) );
			}

			// A network activation supersedes an existing site activation of the same key (promotion);
			// any other duplicate (same scope) is rejected.
			if ( ! ( is_multisite() && self::SCOPE_NETWORK === $scope && self::SCOPE_SITE === $stored_in ) ) {
				throw new Exception( esc_html__( 'This license is already activated.', 'gk-foundation' ) );
			}
		}

		$url = $this->resolve_license_url( $scope, $license_key );

		// An interactive activation whose URL is a bare public IP or localhost almost always means WP_HOME/
		// WP_SITEURL follow the request Host (e.g. Bitnami images) and the admin reached wp-admin over the raw
		// origin — activating would claim that address and burn a slot. System flows (hardcoded/migration/CLI)
		// and non-production/intranet installs are left alone.
		if (
			! $this->is_system_operation
			&& ! CoreHelpers::is_cli()
			&& CoreHelpers::is_production_environment()
			&& $this->is_bare_origin_host( $url )
		) {
			throw new Exception(
				strtr(
					esc_html__( 'This site currently reports its address as [url]. Set WP_HOME and WP_SITEURL to your domain, then activate again, or contact support if this address is correct.', 'gk-foundation' ),
					[ '[url]' => esc_url( $url ) ]
				)
			);
		}

		try {
			$response = $this->call_store_api( '/licenses/' . $license_key . '/activate', $license_key, [ 'url' => $url ] );

			if ( ! $response['_raw']['success'] ) {
				throw new Exception( $this->get_license_key_status_message( $response['_raw']['error'] ) );
			}
		} catch ( Exception $e ) {
			throw new Exception( $this->maybe_add_activation_limit_guidance( $e->getMessage() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		unset( $response['_raw'] );

		$response['scope']  = $scope;
		$response['url']    = $url;
		$response['source'] = 'manual';

		// Promotion of an account (connection) license: keep its account id and provenance so the
		// supersession guard and chooser still recognize it once it moves to network scope.
		$existing = $licenses_data[ $license_key ] ?? null;

		if ( is_array( $existing ) && ! empty( $existing['connection_license_id'] ) ) {
			$response['connection_license_id'] = $existing['connection_license_id'];
			$response['source']                = $existing['source'] ?? $response['source'];
		}

		// Preserve hardcoded provenance across a re-activation ($force) so the row is not silently
		// converted to a manual license until the next hardcoded-processing pass re-stamps it.
		if ( is_array( $existing ) && ! empty( $existing['hardcoded'] ) ) {
			$response['hardcoded'] = true;
		}

		$licenses_data[ $license_key ] = $response;

		$this->save_licenses_data( $licenses_data );

		$this->flush_update_plugins_transients();

		return $response;
	}

	/**
	 * Deletes the plugin-update transients affected by a license change.
	 *
	 * On multisite the authoritative update state is the network-wide site transient regardless of
	 * the current admin context.
	 *
	 * @since 1.25.0
	 *
	 * @return void
	 */
	private function flush_update_plugins_transients() {
		// WP core stores plugin-update state as a site transient on single site and multisite alike.
		delete_site_transient( 'update_plugins' );
	}

	/**
	 * Ajax request wrapper for the deactivate_license() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products:array,licenses:array}
	 */
	public function ajax_deactivate_license( array $payload ) {
		$payload = wp_parse_args(
			$payload,
			[
				'key'           => false,
				'force_removal' => false,
			]
		);

		// Todo: remove once EDD returns more relevant information.
		$payload['force_removal'] = true;

		if ( ! Framework::get_instance()->current_user_can( 'manage_licenses' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		if ( ! $payload['key'] ) {
			throw new Exception( esc_html__( 'Missing license key.', 'gk-foundation' ) );
		}

		$licenses_data = $this->get_licenses_data();

		$license_key = Encryption::get_instance()->decrypt( $payload['key'] );

		if ( empty( $licenses_data[ $license_key ] ) ) {
			throw new Exception( esc_html__( 'The license key is invalid.', 'gk-foundation' ) );
		}

		// A network-scope license covers every subsite; only a network admin may remove it.
		$scope = $licenses_data[ $license_key ]['scope'] ?? ( $this->license_storage_scopes[ $license_key ] ?? self::SCOPE_NETWORK );

		if ( is_multisite() && self::SCOPE_NETWORK === $scope && ! current_user_can( 'manage_network_options' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to deactivate a license that is active for the entire network.', 'gk-foundation' ) );
		}

		$this->deactivate_license( $license_key, (bool) $payload['force_removal'] );

		return Framework::get_instance()->ajax_get_app_data( [ 'is_network_admin' => self::SCOPE_NETWORK === $scope ] );
	}

	/**
	 * Deactivates license.
	 *
	 * @since 1.0.0
	 * @since 1.0.7 Added $force_removal parameter.
	 * @since 1.26.0 Reject connection-sourced licenses on manual deactivation paths.
	 *
	 * @param string $license_key   License key.
	 * @param bool   $force_removal (optional) Forces removal of license from the local licenses object even if deactivation request fails. Default: false.
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function deactivate_license( $license_key, $force_removal = false ) {
		$licenses_data = $this->get_licenses_data();

		$license = $licenses_data[ $license_key ] ?? [];

		// A network-scope license covers every subsite; only a network admin (or a system flow) may remove it.
		$scope = $license['scope'] ?? ( $this->license_storage_scopes[ $license_key ] ?? self::SCOPE_NETWORK );

		if (
			is_multisite()
			&& self::SCOPE_NETWORK === $scope
			&& ! $this->is_system_operation
			&& ! CoreHelpers::is_cli()
			&& ! current_user_can( 'manage_network_options' )
		) {
			throw new Exception( esc_html__( 'You do not have a permission to deactivate a license that is active for the entire network.', 'gk-foundation' ) );
		}

		$manual_connection_row = 'connection' === ( $license['source'] ?? '' ) && ! $this->is_system_operation && ! CoreHelpers::is_cli();

		if ( $manual_connection_row && ! $this->scope_has_live_connection( $scope ) ) {
			// Orphaned row: the explicit disconnect already released the server-side activation, so a
			// store "site inactive" failure must not strand the local row.
			$force_removal = true;
		} elseif ( $manual_connection_row ) {
			throw new Exception( esc_html__( "This license is managed by your GravityKit account and can't be deactivated here.", 'gk-foundation' ) );
		}

		// Deactivate this site's own scope-resolved URL, the same one activation sends. Using the saved
		// URL instead would let a database clone (which carries the original's saved URL) release the
		// original's live activation.
		$url = $this->resolve_license_url( $scope, $license_key );

		try {
			$response = $this->call_store_api( '/licenses/' . $license_key . '/deactivate', $license_key, [ 'url' => $url ] );

			if ( ! $force_removal && ! Arr::get( $response, '_raw.success' ) ) {
				// Unsuccessful deactivation can happen when the license has expired, in which case we should treat it as a "success" and remove from our list.
				// If the license hasn't expired, then there is a problem deactivating it, and we should throw an exception.
				if ( ! Arr::get( $response, 'expiry' ) || ! $this->is_expired_license( Arr::get( $response, 'expiry' ) ) ) {
					throw new Exception( esc_html__( 'Failed to deactivate license.', 'gk-foundation' ) );
				}
			}
		} catch ( Exception $e ) {
			if ( ! $force_removal ) {
				throw new Exception( $e->getMessage() );
			}
		}

		unset( $licenses_data[ $license_key ] );

		$this->flush_update_plugins_transients();

		$this->save_licenses_data( $licenses_data );

		// Clear any server notices only this license contributed, mirroring the connection-removal paths;
		// otherwise a deactivated license's notice lingers until the next full sync.
		if ( ! empty( $license ) ) {
			$this->reconcile_removed_server_notices( [ $license_key => $license ], $licenses_data );
		}
	}

	/**
	 * Whether the given scope still has a live account connection.
	 *
	 * A missing connection or one the server reported revoked is dead, so its leftover
	 * connection-sourced rows must be manually removable. Decrypt-failed and offline
	 * connections stay live: they are restorable and keep the account-managed guard.
	 *
	 * @since 1.26.0
	 *
	 * @param string $scope License storage scope ('network' or 'site').
	 *
	 * @return bool
	 */
	private function scope_has_live_connection( $scope ) {
		$connection = self::SCOPE_NETWORK === $scope ? TokenStore::get_network() : TokenStore::get_site();

		if ( null === $connection ) {
			return false;
		}

		if ( ! empty( $connection['decrypt_failed'] ) ) {
			return true;
		}

		return ConnectionHealth::REVOKED !== ConnectionHealth::effective( $connection )['state'];
	}

	/**
	 * Adds additional data to the license object for use in the frontend.
	 * - Encrypts license key;
	 * - Formats expiration date or message if license is expired; and
	 * - Optionally hides personal information.
	 *
	 * @since 1.0.0
	 *
	 * @param array $license License data.
	 *
	 * @return array
	 */
	public function modify_license_data_for_frontend_output( $license ) {
		$expiry  = ! empty( $license['expiry'] ) ? $license['expiry'] : 'invalid';
		$expired = false;

		// Raw sortable expiry: the UNIX timestamp for a dated license, or 0 for a lifetime/undated one
		// (which sorts as "never expires"). The frontend uses this to order rows, since the displayed
		// `expiry` is a locale-formatted string that cannot be compared chronologically.
		$expiry_timestamp = is_numeric( $expiry ) ? (int) $expiry : 0;

		if ( preg_match( '/[^a-z]/i', $expiry ) ) {
			$expired = $this->is_expired_license( $expiry );

			$expiry = $expired
				? human_time_diff( $expiry, current_time( 'timestamp' ) ) . ' ' . esc_html_x( 'ago', 'Indicates "time ago"', 'gk-foundation' ) // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp
				: date_i18n( get_option( 'date_format' ), $expiry );
		}

		$status = $this->get_license_status_for_frontend( $license, $expiry, $expired );

		try {
			// Nonce derived from the key: same key → identical ciphertext this request (stable identifier
			// reused during the request), different keys → different nonces (avoids XSalsa20 keystream reuse).
			$key_nonce     = hash_hmac( 'sha256', $license['key'], Core::get_request_unique_string(), true );
			$encrypted_key = Encryption::get_instance()->encrypt( $license['key'], false, $key_nonce );
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( 'Failed to encrypt license key: ' . $e->getMessage() );

			$encrypted_key = 'key_encryption_failed';
		}

		/**
		 * Hides the license holder's name/email.
		 *
		 * @filter `gk/foundation/licenses/hide-personal-information`
		 *
		 * @since  1.2.0
		 *
		 * @param bool $hide_personal_information Default: false.
		 */
		$hide_personal_information = apply_filters( 'gk/foundation/licenses/hide-personal-information', false );

		if ( $hide_personal_information ) {
			$license['name']  = '✽✽✽';
			$license['email'] = $license['name'];
		}

		$status_label   = $this->get_license_key_status_label( $status );
		$status_message = $this->get_license_key_status_message( $status );

		// Legacy licenses (activated before per-site licensing existed) keep working network-wide;
		// they read as a plain active network license, with the history in the tooltip.
		if ( ! $expired && ! empty( $license['legacy'] ) && in_array( $status, [ 'valid', 'inactive', 'site_inactive' ], true ) ) {
			$status_label   = esc_html__( 'Active', 'gk-foundation' );
			$status_message = esc_html__( 'This license was activated before per-site licensing was introduced and remains active for the entire network.', 'gk-foundation' );
		}

		// Never ship the raw store payload to the browser: it carries customer PII and signed product
		// download URLs. It is kept at rest for validity checks but stripped at the frontend boundary.
		unset( $license['_raw'] );

		return array_merge(
			$license,
			[
				'expiry'           => $expiry,
				'expiry_timestamp' => $expiry_timestamp,
				'expired'          => $expired,
				'key'              => $encrypted_key,
				'masked_key'       => $this->mask_license_key( $license['key'] ),
				'status'           => $status,
				'status_label'     => $status_label,
				'status_message'   => $status_message,
			]
		);
	}

	/**
	 * Returns the status code the frontend should display for a license.
	 *
	 * @since 1.19.0
	 *
	 * @param array  $license License data.
	 * @param string $expiry  Formatted or symbolic expiry value.
	 * @param bool   $expired Whether the license is expired.
	 *
	 * @return string
	 */
	private function get_license_status_for_frontend( array $license, $expiry, bool $expired ) {
		if ( $expired ) {
			return 'expired';
		}

		$status = $license['status'] ?? $license['license'] ?? null;

		if ( is_string( $status ) && '' !== $status ) {
			return strtolower( $status );
		}

		return 'invalid' === $expiry ? 'invalid' : 'valid';
	}

	/**
	 * Masks part of the license key
	 *
	 * @since 1.0.0
	 *
	 * @param string $license_key License key.
	 *
	 * @return string
	 */
	public function mask_license_key( $license_key ) {
		$length        = strlen( $license_key );
		$visible_count = (int) round( $length / 8 );
		$hidden_count  = $length - ( $visible_count * 4 );

		return sprintf(
			'%s%s%s',
			substr( $license_key, 0, $visible_count ),
			str_repeat( '✽', $hidden_count ),
			substr( $license_key, ( $visible_count * -1 ), $visible_count )
		);
	}

	/**
	 * Saves new or removes existing hardcoded licenses from the license data.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function process_hardcoded_licenses() {
		$this->is_system_operation = true;

		try {
			$this->do_process_hardcoded_licenses();
		} finally {
			$this->is_system_operation = false;
		}
	}

	/**
	 * Runs the hardcoded-licenses processing flow.
	 *
	 * @since 1.25.0
	 *
	 * @return void
	 */
	private function do_process_hardcoded_licenses() {
		$hardcoded_license_keys = [];

		foreach ( self::HARDCODED_LICENSE_CONSTANTS as $constant ) {
			if ( ! defined( $constant ) ) {
				continue;
			}

			if ( is_array( constant( $constant ) ) ) {
				$hardcoded_license_keys = array_merge( $hardcoded_license_keys, constant( $constant ) );
			} else {
				$hardcoded_license_keys[] = constant( $constant );
			}
		}

		$licenses_data = $this->get_licenses_data();

		// Remove any licenses that are no longer hardcoded.
		$removed_hardcoded = [];

		foreach ( $licenses_data as $key => $license ) {
			if ( ! empty( $license['hardcoded'] ) && ! in_array( $key, $hardcoded_license_keys, true ) ) {
				$removed_hardcoded[ $key ] = $license;

				unset( $licenses_data[ $key ] );
			}
		}

		if ( $removed_hardcoded ) {
			$this->save_licenses_data( $licenses_data );

			$this->reconcile_removed_server_notices( $removed_hardcoded, $licenses_data );
		}

		if ( empty( $hardcoded_license_keys ) ) {
			return;
		}

		// Add any new hardcoded licenses.
		$license_keys_to_check = array_values( array_diff( $hardcoded_license_keys, array_keys( $licenses_data ) ) );

		if ( empty( $license_keys_to_check ) ) {
			return;
		}

		$cache_id      = Framework::ID . '/hardcoded-licenses-check';
		$check_timeout = defined( 'GRAVITYKIT_HARDCODED_LICENSES_CHECK_TIMEOUT' ) ? GRAVITYKIT_HARDCODED_LICENSES_CHECK_TIMEOUT : 5 * MINUTE_IN_SECONDS;
		$last_check    = WP::get_site_transient( $cache_id );

		if ( $last_check ) {
			return;
		}

		WP::set_site_transient( $cache_id, current_time( 'timestamp' ), $check_timeout ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp

		LoggerFramework::get_instance()->notice( "Checking hardcoded licenses and pausing for {$check_timeout} seconds." );

		try {
			$checked_licenses = $this->check_licenses( $license_keys_to_check );
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( "Failed to check hardcoded licenses. {$e->getMessage()}." );

			return;
		}

		foreach ( $checked_licenses as $key => $license ) {
			if ( ! Arr::get( $license, '_raw.success' ) ) {
				LoggerFramework::get_instance()->warning( "Hardcoded license {$key} is invalid." );

				continue;
			}

			if ( 'inactive' === Arr::get( $license, '_raw.license' ) ) {
				try {
					$this->activate_license( Arr::get( $license, 'key' ) );
				} catch ( Exception $e ) {
					LoggerFramework::get_instance()->warning( "Unable to activate hardcoded license {$key}:" . $e->getMessage() );

					continue;
				}
			}

			unset( $license['_raw'] );

			$license['hardcoded'] = true;
			$license['source']    = 'hardcoded';

			// Hardcoded licenses are install-wide; set the scope so they are not mistaken for legacy pre-update licenses.
			$license['scope'] = $this->normalize_activation_scope( null );
			$license['url']   = $this->resolve_license_url( $license['scope'] );

			$licenses_data[ $key ] = $license;
		}

		$this->save_licenses_data( $licenses_data );
	}

	/**
	 * Migrates licenses for products that do not have Foundation integrated.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force_migration Whether to force migration even if it was done before.
	 *
	 * @return void
	 */
	public function migrate_legacy_licenses( $force_migration = false ) {
		$this->is_system_operation = true;

		try {
			$this->do_migrate_legacy_licenses( $force_migration );
		} finally {
			$this->is_system_operation = false;
		}
	}

	/**
	 * Runs the legacy-licenses migration flow.
	 *
	 * @since 1.25.0
	 *
	 * @param bool $force_migration Whether to force migration even if it was done before.
	 *
	 * @return void
	 */
	private function do_migrate_legacy_licenses( $force_migration = false ) {
		$logger = LoggerFramework::get_instance();

		$migration_status_id = Framework::ID . '/legacy-licenses-migrated';

		// Legacy license keys live in per-blog options, so on multisite each blog migrates its own.
		// The pre-scoping network-wide flag only proves the MAIN site ran; honor it there for backward compatibility.
		if ( is_multisite() ) {
			$save_migration_status_in_db = function () use ( $migration_status_id ) {
				update_option( $migration_status_id, current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp
			};

			$already_migrated = get_option( $migration_status_id ) || ( is_main_site() && get_site_option( $migration_status_id ) );
		} else {
			$save_migration_status_in_db = function () use ( $migration_status_id ) {
				update_site_option( $migration_status_id, current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp
			};

			$already_migrated = (bool) get_site_option( $migration_status_id );
		}

		if ( $already_migrated && ! $force_migration ) {
			return;
		}

		$licenses_data = $this->get_licenses_data();

		$license_keys_to_migrate = [];

		$db_options = [
			'gravityformsaddon_gravityview-importer_settings',
			'gravityformsaddon_gravityview_app_settings',
			'gravityformsaddon_gravityview-inline-edit_settings',
			'gravityformsaddon_gravitycharts_settings',
			'gravityformsaddon_gk-gravityactions_settings',
			'gravityformsaddon_gravityview-calendar_settings',
			'gravityformsaddon_gravityexport_settings',
			'gravityformsaddon_gravityview-entry-revisions_settings',
		];

		foreach ( $db_options as $option ) {
			$license = Arr::get( get_option( $option, [] ), 'license_key' );

			$option = str_replace( [ 'gravityformsaddon_', '_settings' ], '', $option );

			if ( $license ) {
				$license_keys_to_migrate[ $license ] = $option;
			} else {
				$logger->warning( "Legacy license not found for {$option}." );
			}
		}

		if ( empty( $license_keys_to_migrate ) ) {
			$save_migration_status_in_db();

			$logger->info( 'Did not find any legacy licenses to migrate.' );

			return;
		}

		try {
			$checked_licenses = $this->check_licenses( array_keys( $license_keys_to_migrate ) );
		} catch ( Exception $e ) {
			$logger->error( "Failed to check legacy licenses. {$e->getMessage()}." );

			return;
		}

		foreach ( $checked_licenses as $key => $license ) {
			if ( ! $license['_raw']['success'] ) {
				$logger->warning( "Legacy license {$key} is invalid." );

				continue;
			}

			try {
				$license = $this->activate_license( $key );
			} catch ( Exception $e ) {
				$logger->error( "Failed to activate legacy license {$key}. {$e->getMessage()}." );

				continue;
			}

			$logger->info( "Migrated legacy license for {$license_keys_to_migrate[$key]}." );

			$licenses_data[ $key ] = $license;
		}

		$save_migration_status_in_db();

		$this->save_licenses_data( $licenses_data );
	}

	/**
	 * Rechecks all licenses and updates the database.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $skip_cache Whether to skip returning products from cache.
	 *
	 * @return void
	 */
	public function recheck_all_licenses( $skip_cache = false ) {
		$cache_id = Framework::ID . '/licenses';

		$licenses_data = $this->get_licenses_data();

		// Network-scope keys revalidate once per network; site-scope keys revalidate per blog against their own URL.
		$keys_by_scope = [
			self::SCOPE_NETWORK => [],
			self::SCOPE_SITE    => [],
		];

		$has_connection_keys = [
			self::SCOPE_NETWORK => false,
			self::SCOPE_SITE    => false,
		];

		if ( empty( $licenses_data ) ) {
			// A zero-grant connection has no local rows yet; open its cadence gate so the
			// `rechecked` listeners can ingest the first account-side grant.
			foreach ( array_keys( $has_connection_keys ) as $scope ) {
				$has_connection_keys[ $scope ] = $this->scope_has_live_connection( $scope );
			}

			if ( ! array_filter( $has_connection_keys ) ) {
				return;
			}
		}

		foreach ( $licenses_data as $key => $license ) {
			$scope = $license['scope'] ?? ( $this->license_storage_scopes[ $key ] ?? self::SCOPE_NETWORK );
			$scope = is_multisite() && self::SCOPE_SITE === $scope ? self::SCOPE_SITE : self::SCOPE_NETWORK;

			// The connection channel owns connection-sourced license state: this direct recheck runs against
			// the plain URL and would misreport marker-URL (network connection) activations as inactive.
			if ( 'connection' === ( $license['source'] ?? '' ) ) {
				$has_connection_keys[ $scope ] = true;

				continue;
			}

			$keys_by_scope[ $scope ][] = $key;
		}

		$scopes_to_check = [];

		// Connection-sourced keys are excluded from the store check but still open their scope's cadence
		// gate, so the `rechecked` listeners keep firing on a connection-only install.
		if ( ( ! empty( $keys_by_scope[ self::SCOPE_NETWORK ] ) || $has_connection_keys[ self::SCOPE_NETWORK ] ) && ( $skip_cache || ! WP::get_site_transient( $cache_id ) ) ) {
			$scopes_to_check[] = self::SCOPE_NETWORK;

			// Only an actual store check consumes the model-version poll's budget.
			$this->network_recheck_performed = ! empty( $keys_by_scope[ self::SCOPE_NETWORK ] );

			WP::set_site_transient( $cache_id, current_time( 'timestamp' ), 12 * HOUR_IN_SECONDS ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp
		}

		if ( ( ! empty( $keys_by_scope[ self::SCOPE_SITE ] ) || $has_connection_keys[ self::SCOPE_SITE ] ) && ( $skip_cache || ! get_transient( $cache_id ) ) ) {
			$scopes_to_check[] = self::SCOPE_SITE;

			set_transient( $cache_id, current_time( 'timestamp' ), 12 * HOUR_IN_SECONDS ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp
		}

		if ( empty( $scopes_to_check ) ) {
			return;
		}

		$revalidated_licenses = [];

		foreach ( $scopes_to_check as $scope ) {
			if ( empty( $keys_by_scope[ $scope ] ) ) {
				continue;
			}

			try {
				$license_check_result = $this->check_licenses( $keys_by_scope[ $scope ], $this->resolve_license_url( $scope ), $scope );

				foreach ( $license_check_result as $key => $license ) {
					$is_valid = ! empty( $license['_raw']['success'] );

					if ( ! $is_valid ) {
						LoggerFramework::get_instance()->warning( "License {$key} is invalid." );
					}

					$revalidated_licenses[ $key ] = $this->prepare_rechecked_license_for_storage( $license, $licenses_data[ $key ] ?? [] );

					$this->maybe_advance_model_version( $key, $license, $licenses_data[ $key ] ?? [], $revalidated_licenses[ $key ] );
				}
			} catch ( Exception $e ) {
				LoggerFramework::get_instance()->error( "Failed to revalidate all licenses. {$e->getMessage()}." );
			}
		}

		if ( ! empty( $revalidated_licenses ) ) {
			// Union so keys skipped by the other scope's throttle keep their stored entries (and keys never renumber).
			$this->finalize_license_revalidation( $revalidated_licenses + $licenses_data, $revalidated_licenses );
		}

		/**
		 * Fires after a license recheck's cadence gate opens and the recheck runs, so listeners can
		 * piggyback the ~12h throttle for their own periodic refresh instead of scheduling a cron.
		 *
		 * @since 1.26.0
		 */
		do_action( 'gk/foundation/licenses/rechecked' );
	}

	/**
	 * Persists revalidated licenses and runs the post-save reconciliation.
	 *
	 * @since 1.25.0
	 * @since TBD Reconcile notices across the full surviving license set.
	 *
	 * @param array $licenses_data        Full set of entries to store.
	 * @param array $revalidated_licenses The freshly rechecked subset.
	 *
	 * @return void
	 */
	private function finalize_license_revalidation( array $licenses_data, array $revalidated_licenses ) {
		$this->save_licenses_data( $licenses_data );

		if ( $this->refresh_update_plugins_transient_after_license_recheck ) {
			// Rebuild the update_plugins transient with fresh product/channel data.
			// EDD::check_for_product_updates() hooks into pre_set_site_transient_update_plugins,
			// so re-setting the transient triggers our hook to inject channel updates.
			// Without this, the Plugins badge shows stale counts until the next WP update check.
			set_site_transient( 'update_plugins', get_site_transient( 'update_plugins' ) );
		}

		// Reconcile the full surviving set so a license skipped by this recheck's cadence cannot
		// lose a notice that it still supplies.
		$this->sync_server_notices( $licenses_data, ServerNoticeHandler::SOURCE_LICENSE );
	}

	/**
	 * Prepares remotely rechecked license data for local storage.
	 *
	 * @since 1.19.0
	 *
	 * @param array $license          Rechecked license data.
	 * @param array $existing_license Existing locally stored license data.
	 *
	 * @return array
	 */
	private function prepare_rechecked_license_for_storage( array $license, array $existing_license ) {
		$is_valid = ! empty( $license['_raw']['success'] );

		unset( $license['_raw'] );

		if ( ! empty( $existing_license['hardcoded'] ) ) {
			$license['hardcoded'] = true;
		}

		// The store response does not include these local fields; without copying them over, a site license
		// would move back into the shared network storage on every recheck and its provenance would vanish.
		foreach ( [ 'scope', 'url', 'legacy', 'source', 'connection_license_id' ] as $scope_field ) {
			if ( isset( $existing_license[ $scope_field ] ) && ! isset( $license[ $scope_field ] ) ) {
				$license[ $scope_field ] = $existing_license[ $scope_field ];
			}
		}

		// A background recheck (WP-Cron/WP-CLI) sends an ambient home_url() it cannot trust. `site_inactive`
		// is the ONLY status a wrong URL fabricates (license fine, but that URL isn't among its activations),
		// so background may not use it to downgrade a stored `valid`. Every other status is URL-independent and
		// still applies — including `inactive`, which means zero activations anywhere (a real full deactivation).
		if (
			( CoreHelpers::is_cli() || wp_doing_cron() )
			&& 'site_inactive' === ( $license['status'] ?? null )
			&& 'valid' === ( $existing_license['status'] ?? null )
		) {
			$license['status'] = $existing_license['status'];
		}

		if ( ! $is_valid ) {
			foreach ( [ 'name', 'email', 'license_name', 'license_limit', 'site_count', 'activations_left', 'expiry' ] as $field ) {
				if ( ( ! isset( $license[ $field ] ) || '' === $license[ $field ] ) && array_key_exists( $field, $existing_license ) ) {
					$license[ $field ] = $existing_license[ $field ];
				}
			}

			if ( empty( $license['status'] ) ) {
				$license['status'] = 'invalid';
			}

			$license['products'] = [];
		}

		return $license;
	}

	/**
	 * Rechecks all licenses without rebuilding the WordPress plugin update transient.
	 *
	 * @since 1.19.0
	 *
	 * @param bool $skip_cache Whether to skip returning products from cache.
	 *
	 * @return void
	 */
	public function recheck_all_licenses_without_update_plugins_refresh( $skip_cache = false ) {
		$previous = $this->refresh_update_plugins_transient_after_license_recheck;

		$this->refresh_update_plugins_transient_after_license_recheck = false;

		try {
			$this->recheck_all_licenses( $skip_cache );
		} finally {
			$this->refresh_update_plugins_transient_after_license_recheck = $previous;
		}
	}

	/**
	 * Syncs server-driven product notices from license data.
	 *
	 * Merges products across licenses (keyed by text_domain) and delegates
	 * to ServerNoticeHandler for reconciliation with stored notices.
	 *
	 * @since 1.13.0
	 * @since TBD Added deterministic notice merging and the $source parameter.
	 *
	 * @param array  $licenses License data with products.
	 * @param string $source  Notice source.
	 */
	private function sync_server_notices( array $licenses, string $source = ServerNoticeHandler::SOURCE_LICENSE ): void {
		$products = $this->collect_server_notice_products( $licenses );

		if ( ! empty( $products ) ) {
			ServerNoticeHandler::sync( $products, Core::notices(), $source );
		}
	}

	/**
	 * Collects products and merges server notices across licenses.
	 *
	 * License keys and notice keys are sorted so the same inputs always select the same
	 * definition when multiple licenses deliver an identical product and notice key.
	 *
	 * @since TBD
	 *
	 * @param array $licenses License data with products.
	 *
	 * @return array Products keyed by text domain.
	 */
	private function collect_server_notice_products( array $licenses ): array {
		$products = [];

		ksort( $licenses, SORT_STRING );

		foreach ( $licenses as $license ) {
			if ( ! is_array( $license ) ) {
				continue;
			}

			foreach ( $license['products'] ?? [] as $product ) {
				if ( ! is_array( $product ) ) {
					continue;
				}

				$text_domain = $product['text_domain'] ?? '';

				if ( ! is_scalar( $text_domain ) || '' === (string) $text_domain ) {
					continue;
				}

				$text_domain = (string) $text_domain;

				if ( ! isset( $products[ $text_domain ] ) ) {
					$products[ $text_domain ] = [
						'id'                 => is_numeric( $product['id'] ?? null ) ? (int) $product['id'] : 0,
						'text_domain'        => $text_domain,
						'text_domains'       => is_array( $product['text_domains'] ?? null ) ? $product['text_domains'] : [],
						'text_domain_legacy' => is_scalar( $product['text_domain_legacy'] ?? null ) ? (string) $product['text_domain_legacy'] : '',
						'product_notices'    => [],
					];
				}

				$notices = is_array( $product['product_notices'] ?? null ) ? $product['product_notices'] : [];

				ksort( $notices, SORT_STRING );

				foreach ( $notices as $notice_key => $notice ) {
					$products[ $text_domain ]['product_notices'][ $notice_key ] = $notice;
				}
			}
		}

		ksort( $products, SORT_STRING );

		return $products;
	}

	/**
	 * Reconciles notice contributions for products affected by removed licenses.
	 *
	 * @since TBD
	 *
	 * @param array $removed_licenses   Removed license entries.
	 * @param array $remaining_licenses Remaining license entries.
	 *
	 * @return void
	 */
	private function reconcile_removed_server_notices( array $removed_licenses, array $remaining_licenses ): void {
		$affected  = $this->collect_server_notice_products( $removed_licenses );
		$remaining = $this->collect_server_notice_products( $remaining_licenses );

		if ( empty( $affected ) ) {
			return;
		}

		foreach ( $affected as $text_domain => &$product ) {
			if ( isset( $remaining[ $text_domain ] ) ) {
				$product = $remaining[ $text_domain ];
				continue;
			}

			$product['product_notices'] = [];
		}

		unset( $product );

		ServerNoticeHandler::sync( $affected, Core::notices(), ServerNoticeHandler::SOURCE_LICENSE );
	}

	/**
	 * Retrieves site data (plugin versions, integrations, etc.) to be sent along with the license check.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_site_data() {
		global $wpdb;

		$data = [];

		$theme_data = wp_get_theme();
		$theme      = $theme_data->get( 'Name' ) . ' ' . $theme_data->get( 'Version' );

		$data['php_version']   = PHP_VERSION;
		$data['wp_version']    = get_bloginfo( 'version' );
		$data['mysql_version'] = $wpdb->db_version();

		if ( defined( 'GV_PLUGIN_VERSION' ) ) {
			$data['gv_version'] = GV_PLUGIN_VERSION;
		}

		if ( class_exists( 'GFForms' ) ) {
			$data['gf_version'] = GFForms::$version;
		}

		if ( isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
			$data['server'] = $_SERVER['SERVER_SOFTWARE'];
		}

		$data['multisite'] = is_multisite();
		$data['theme']     = $theme;
		$data['url']       = is_multisite() ? network_home_url() : home_url();

		if ( is_multisite() ) {
			$data['multisite_blog_count'] = (int) get_blog_count();
		}
		$data['beta'] = SettingsFramework::get_instance()->get_plugin_setting( Core::ID, 'beta' );

		// GravityView view data.
		$gravityview_posts = wp_count_posts( 'gravityview', 'readable' );

		$data['view_count']  = null;
		$data['view_first']  = null;
		$data['view_latest'] = null;

		if ( ! empty( $gravityview_posts->publish ) ) {
			$data['view_count'] = $gravityview_posts->publish;

			$first = get_posts(
                [
					'numberposts' => 1,
					'post_type'   => 'gravityview',
					'post_status' => 'publish',
					'order'       => 'ASC',
				]
            );

			$latest = get_posts(
                [
					'numberposts' => 1,
					'post_type'   => 'gravityview',
					'post_status' => 'publish',
					'order'       => 'DESC',
				]
            );

			$first = array_shift( $first );

			if ( $first ) {
				$data['view_first'] = $first->post_date;
			}

			$latest = array_pop( $latest );

			if ( $latest ) {
				$data['view_latest'] = $latest->post_date;
			}
		}

		// Gravity Forms form data.
		if ( class_exists( 'GFFormsModel' ) ) {
			$form_data = GFFormsModel::get_form_count();

			$data['forms_total']    = $form_data['total'];
			$data['forms_active']   = $form_data['active'];
			$data['forms_inactive'] = $form_data['inactive'];
			$data['forms_trash']    = $form_data['trash'];
		}

		$plugins = CoreHelpers::get_installed_plugins();
		foreach ( $plugins as &$plugin ) {
			$plugin = Arr::only( $plugin, [ 'name', 'version', 'active', 'network_activated', 'text_domain' ] );
			$plugin = array_filter( $plugin ); // Don't include active/network activated if false.
		}

		$data['plugins'] = $plugins;
		$data['locale']  = get_locale();

		return $data;
	}

	/**
	 * Optionally updates the Manage Your Kit submenu badge count if any of the products are unlicensed.
	 *
	 * @since 1.2.0
	 *
	 * @return void
	 */
	public function update_manage_your_kit_submenu_badge_count() {
		if ( ! AdminMenu::should_initialize() ) {
			return;
		}

		if ( ! Framework::get_instance()->current_user_can( 'manage_licenses' ) ) {
			return;
		}

		try {
			$products_data = ProductManager::get_instance()->get_products_data();
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->warning( 'Unable to get products when adding a badge count for unlicensed products.' );

			return;
		}

		$update_count     = 0;
		$is_network_admin = CoreHelpers::is_network_admin();

		// The nag is context-owned: subsites count their own site-activated products; network-activated ones are the network admin's.
		foreach ( $products_data as $product ) {
			if ( $product['third_party'] || $product['hidden'] || $product['free'] || ! $product['installed'] ) {
				continue;
			}

			if ( $is_network_admin ) {
				if ( ! empty( $product['network_activated'] ) && empty( $product['network_licensed'] ) ) {
					++$update_count;
				}

				continue;
			}

			if ( ! empty( $product['active'] ) && empty( $product['network_activated'] ) && empty( $product['licensed'] ) ) {
				++$update_count;
			}
		}

		if ( ! $update_count ) {
			return;
		}

		add_filter(
			'gk/foundation/admin-menu/submenu/' . Framework::ID . '/counter',
			function ( $count ) use ( $update_count ) {
				return (int) $count + $update_count;
			}
		);
	}

	/**
	 * Returns whether a saved license is actually active for the current site.
	 *
	 * True for a license the store reports as valid, a legacy (pre-per-site-licensing) license,
	 * or a hardcoded one — false for keys that are merely saved (e.g., "Not Activated for This Site").
	 *
	 * @since 1.25.0
	 *
	 * @param array $license License entry.
	 *
	 * @return bool
	 */
	public function is_license_active_for_site( array $license ) {
		if ( ! empty( $license['expiry'] ) && $this->is_expired_license( $license['expiry'] ) ) {
			return false;
		}

		if ( ! empty( $license['legacy'] ) || ! empty( $license['hardcoded'] ) ) {
			return true;
		}

		return 'valid' === strtolower( (string) ( $license['status'] ?? '' ) );
	}

	/**
	 * Returns whether a saved license is active for the entire network.
	 *
	 * True for active network-scope, legacy (pre-per-site-licensing), and hardcoded licenses.
	 *
	 * @since 1.25.0
	 *
	 * @param array $license License entry.
	 *
	 * @return bool
	 */
	public function is_license_active_for_network( array $license ) {
		if ( ! $this->is_license_active_for_site( $license ) ) {
			return false;
		}

		return self::SCOPE_NETWORK === ( $license['scope'] ?? '' ) || ! empty( $license['legacy'] ) || ! empty( $license['hardcoded'] );
	}

	/**
	 * Determines if the license has expired.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $expiry Unix time or 'lifetime'.
	 *
	 * @return bool
	 */
	public function is_expired_license( $expiry ) {
		if ( 'lifetime' === $expiry ) {
			return false;
		}

		return $expiry < current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp
	}
}
