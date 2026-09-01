<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Licenses;

use Exception;
use GravityKit\GravityImport\Foundation\Core;
use GravityKit\GravityImport\Foundation\Exceptions\LockAcquisitionException;
use GravityKit\GravityImport\Foundation\Helpers\Arr;
use GravityKit\GravityImport\Foundation\Helpers\WP;
use GravityKit\GravityImport\Foundation\Logger\Framework as LoggerFramework;
use GravityKit\GravityImport\Foundation\Encryption\Encryption;
use GravityKit\GravityImport\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityImport\Foundation\Licenses\Integrity\PackageVerifier;
use GravityKit\GravityImport\Foundation\Licenses\WP\WPUpgraderSkin;
use GravityKit\GravityImport\Foundation\Notices\ServerNoticeHandler;
use GravityKit\GravityImport\Foundation\WP\AdminMenu;
use Plugin_Upgrader;
use stdClass;

class ProductManager {
	const STORE_API_VERSION = 3;

	const PRODUCTS_DATA_CACHE_ID = Framework::ID . '/products/' . Core::VERSION;

	const PRODUCTS_DATA_CACHE_EXPIRATION = 43200; // 12 hours in seconds.

	/**
	 * Transient containing the last successfully reconciled catalog notice hash.
	 *
	 * @since TBD
	 */
	const CATALOG_NOTICES_HASH_CACHE_ID = Framework::ID . '/product-notices/' . Core::VERSION;

	/**
	 * Duration in seconds for the force-refresh lock window.
	 *
	 * Concurrent force-refresh requests within this window will share the same fetch operation.
	 *
	 * @since 1.7.0
	 */
	const FORCE_REFRESH_LOCK_WINDOW = 5;

	/**
	 * Maximum number of polling attempts when waiting for a concurrent fetch.
	 *
	 * Uses exponential backoff: 100ms, 200ms, 400ms, 800ms, then 1s (capped).
	 * Total wait time: ~10.5 seconds with 13 attempts.
	 *
	 * @since 1.7.0
	 */
	const FETCH_WAIT_MAX_ATTEMPTS = 13;

	/**
	 * Initial delay in microseconds for exponential backoff polling.
	 *
	 * Doubled on each attempt until FETCH_WAIT_POLL_MAX is reached.
	 *
	 * @since 1.7.0
	 */
	const FETCH_WAIT_POLL_INITIAL = 100000; // 100 milliseconds.

	/**
	 * Maximum delay in microseconds between polling attempts (exponential backoff cap).
	 *
	 * @since 1.7.0
	 */
	const FETCH_WAIT_POLL_MAX = 1000000; // 1 second.

	/**
	 * Lock timeout in seconds for product fetch operations.
	 *
	 * @since 1.7.0
	 */
	const FETCH_LOCK_TIMEOUT = 15;

	/**
	 * Brief delay in microseconds to allow other processes to complete their lock acquisition.
	 *
	 * @since 1.7.0
	 */
	const FETCH_LOCK_RACE_DELAY = 10000; // 10 milliseconds.

	/**
	 * Wait duration in microseconds after losing a lock race before checking cache.
	 *
	 * @since 1.7.0
	 */
	const FETCH_LOCK_LOST_RACE_WAIT = 500000; // 0.5 seconds.

	/**
	 * {@ProductManager} class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var ProductManager|null;
	 */
	private static $_instance = null;

	/**
	 * Catalog notice payload hashes synced during this request, keyed by blog ID.
	 *
	 * @since TBD
	 *
	 * @var array<int,string>
	 */
	private $catalog_notice_sync_hashes = [];

	/**
	 * Returns class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return ProductManager
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
	 *
	 * @return void
	 */
	public function init() {
		static $initialized;

		if ( $initialized ) {
			return;
		}

		add_filter( 'gk/foundation/ajax/' . Framework::AJAX_ROUTER . '/routes', [ $this, 'configure_ajax_routes' ] );

		add_action( 'gk/foundation/plugin-activated', [ $this, 'cleanup_products_data' ] );

		add_action( 'wp_loaded', [ $this, 'ensure_update_plugins_transient' ], PHP_INT_MAX );

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
	 * @param array $routes Ajax action to class method map.
	 *
	 * @return array
	 */
	public function configure_ajax_routes( array $routes ) {
		return array_merge(
			$routes,
			[
				'install_product'    => [ $this, 'ajax_install_product' ],
				'update_product'     => [ $this, 'ajax_update_product' ],
				'delete_product'     => [ $this, 'ajax_delete_product' ],
				'activate_product'   => [ $this, 'ajax_activate_product' ],
				'deactivate_product' => [ $this, 'ajax_deactivate_product' ],
				'get_products'       => [ $this, 'ajax_get_products_data' ],
				'switch_channel'     => [ $this, 'ajax_switch_channel' ],
				'switch_to_stable'   => [ $this, 'ajax_switch_to_stable' ],
			]
		);
	}

	/**
	 * Ensures the update_plugins transient exists before the admin menu renders.
	 *
	 * Core builds the admin menu, including the Plugins update badge via
	 * wp_get_update_data() in wp-admin/menu.php, before admin_init. Core's
	 * _maybe_update_plugins() would rebuild a cold transient on admin_init, but
	 * after a Foundation install/update the upgrader deletes the transient and the
	 * next menu badge would show stale/0 without this pre-menu rebuild.
	 *
	 * This runs on wp_loaded at PHP_INT_MAX: after init-registered plugin updaters
	 * (e.g., EDD_SL updaters from Gravity Forms add-on init at priority 15) and
	 * Foundation's own EDD product-update injector are attached, but before core
	 * builds the menu badge.
	 *
	 * @since 1.13.0
	 */
	public function ensure_update_plugins_transient(): void {
		if ( wp_doing_ajax() || wp_doing_cron() || ! is_admin() ) {
			return;
		}

		$current = get_site_transient( 'update_plugins' );

		if ( $current && ! empty( $current->last_checked ) ) { // @phpstan-ignore property.notFound
			return;
		}

		wp_update_plugins();
	}

	/**
	 * Cleans up stale files in upgrade-temp-backup/plugins/ before an install or update.
	 *
	 * A previous interrupted upgrade can leave a directory or symlink in the temp backup
	 * folder that prevents future upgrades. WordPress tries to delete it but fails on
	 * symlinks. This method handles both cases and throws a clear error if cleanup fails.
	 *
	 * @since 1.13.0
	 *
	 * @param array $product Product data with 'slug' key.
	 *
	 * @throws Exception When the stale backup cannot be removed.
	 */
	private function cleanup_upgrade_temp_backup( array $product ): void {
		$slug = basename( $product['slug'] ?? '' );

		if ( ! $slug || '.' === $slug || '..' === $slug ) {
			return;
		}

		// WP core hardcodes this path in WP_Upgrader::move_to_temp_backup_dir().
		$backup_path = WP_CONTENT_DIR . '/upgrade-temp-backup/plugins/' . $slug;

		if ( ! is_link( $backup_path ) && ! is_dir( $backup_path ) ) {
			return;
		}

		if ( is_link( $backup_path ) ) {
			@unlink( $backup_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink, WordPress.PHP.NoSilencedErrors.Discouraged -- Symlinks require native unlink().
		} else {
			global $wp_filesystem;

			if ( ! $wp_filesystem ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';

				if ( ! WP_Filesystem() ) {
					return;
				}
			}

			$wp_filesystem->delete( $backup_path, true );
		}

		if ( file_exists( $backup_path ) ) {
			throw new Exception(
				strtr(
					esc_html_x(
						'A previous update left behind temporary files at [path]. Please delete this folder and try again.',
						'Placeholders inside [] are not to be translated.',
						'gk-foundation'
					),
					[ '[path]' => esc_html( $backup_path ) ]
				)
			);
		}
	}

	/**
	 * Returns the first product found based on an Ajax router request payload.
	 *
	 * @since $ver$
	 * @since 2.7.2 Renamed from get_first_project_by_payload() & added $products_data_args parameter.
	 *
	 * @param array $payload            The payload.
	 * @param array $products_data_args Optional. Arguments to pass to get_products_data().
	 *
	 * @return array|null The product object.
	 */
	private function get_first_product_by_payload( array $payload, array $products_data_args = [] ): ?array {
		$text_domains = array_filter( explode( '|', $payload['text_domain'] ?? '' ) );

		if ( ! $text_domains ) {
			return null;
		}

		$product = Arr::first(
			$this->get_products_data( $products_data_args ),
			static function ( array $product ) use ( $text_domains ) {
				return (bool) array_intersect( $text_domains, $product['text_domains'] );
			}
		);

		return $product;
	}

	/**
	 * Ajax request wrapper for the install_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products: array, activation_error: null|string}
	 */
	public function ajax_install_product( array $payload ): array {
		$payload = wp_parse_args(
			$payload,
			[
				'text_domain'                 => null,
				'activate'                    => false,
				'frontend_foundation_version' => 0,
			]
		);

		if ( ! Framework::get_instance()->current_user_can( 'install_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$product = $this->get_first_product_by_payload( $payload );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		$this->install_product( $product );

		// Re-fetch product after install to get updated installation status.
		$product = $this->get_first_product_by_payload( $payload, [ 'skip_request_cache' => true ] );

		$activation_error = null;

		$backend_foundation_version = Core::VERSION;

		if ( ! $product['active'] && $payload['activate'] ) {
			try {
				$this->activate_product( $product );

				// Check if the installed product comes with a newer version of the Foundation, which will be loaded if another Ajax request is made.
				$product_foundation_version = Core::get_instance()->get_plugin_foundation_version( $product['plugin_file'] );

				$backend_foundation_version = CoreHelpers::version_compare(
					Core::VERSION,
					$product_foundation_version ?? '0',
					'<'
				) ? $product_foundation_version : Core::VERSION;
			} catch ( Exception $e ) {
				$activation_error = $e->getMessage();
			}
		}

		return [
			'products'         => $this->ajax_get_products_data(),
			'activation_error' => $activation_error,
            'ui_action'        => [
                'reload' => CoreHelpers::version_compare( $backend_foundation_version, $payload['frontend_foundation_version'], '<>' ) || $product['has_admin_menu'],
            ],
		];
	}

	/**
	 * Installs a product.
	 *
	 * On success, `$product` is refreshed in place so its `path`, `plugin_file`,
	 * `installed`, `installed_version`, `active`, and related fields reflect the
	 * just-installed plugin rather than the pre-install catalog snapshot.
	 * Without this refresh, immediate downstream calls such as
	 * {@see self::activate_product()} would receive an empty `path` and fail in
	 * `activate_plugin()` with a "valid header" error.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $product               Product data. Updated in place after install.
	 * @param string|null $download_url_override Explicit download URL to use, bypassing license-scoped resolution. Used by callers that have already chosen a non-default URL (e.g., a channel switch picking a beta build).
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function install_product( array &$product, ?string $download_url_override = null ) {
		if ( ! file_exists( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' ) ) {
			throw new Exception( esc_html__( 'Unable to load core WordPress files required to install the product.', 'gk-foundation' ) );
		}

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$product_id      = $product['id'];
		$license_manager = LicenseManager::get_instance();
		$licenses_data   = $license_manager->get_all_licenses_data();

		// Prefer the license-scoped URL from licenses_data — it carries the `lh`
		// attribution claim that the Store's download log uses. For free products
		// this resolves to any license the caller holds (the Store lists free
		// products under every license in /licenses/check); for paid products
		// this resolves to the purchasing license. Falls back to the catalog
		// URL from $product['download_link'] for free-only callers with no
		// licenses at all.
		$product_download_link = null !== $download_url_override
			? $download_url_override
			: EDD::pick_download_link( $product, $licenses_data );

		// Paid product without any cached entry: walk licenses with a live
		// re-check in case licenses_data is stale.
		if ( ! $product_download_link ) {
			foreach ( $licenses_data as $key => $license_data ) {
				if ( $license_manager->is_expired_license( $license_data['expiry'] ) || empty( $license_data['products'] ) || ! isset( $license_data['products'][ $product_id ] ) ) {
					continue;
				}

				try {
					$license = $license_manager->check_license( $key, $license_data['url'] ?? null );
				} catch ( Exception $e ) {
					LoggerFramework::get_instance()->warning( "Unable to verify license key {$key} when installing product ID {$product_id}: " . $e->getMessage() );

					continue;
				}

				if ( empty( $license['products'][ $product_id ]['download'] ) ) {
					continue;
				}

				$product_download_link = $license['products'][ $product_id ]['download'];

				break;
			}
		}

		if ( ! $product_download_link ) {
			throw new Exception( esc_html__( 'Unable to locate product download link.', 'gk-foundation' ) );
		}

		$this->cleanup_upgrade_temp_backup( $product );

		$installer = new Plugin_Upgrader( new WPUpgraderSkin() );

		try {
			$previous_expected_product_id         = PackageVerifier::$expected_product_id;
			PackageVerifier::$expected_product_id = (int) $product_id;

			$installer->install( $product_download_link, [ 'overwrite_package' => true ] );
		} catch ( Exception $e ) {
			$error = join(
				' ',
				[
					esc_html__( 'Installation failed.', 'gk-foundation' ),
					$e->getMessage(),
				]
			);

			throw new Exception( $error );
		} finally {
			PackageVerifier::$expected_product_id = $previous_expected_product_id ?? null;
		}

		if ( ! $installer->result ) {
			throw new Exception( esc_html__( 'Installation failed.', 'gk-foundation' ) );
		}

		foreach ( $this->get_products_data( [ 'skip_request_cache' => true ] ) as $candidate ) {
			if ( $candidate['text_domain'] === $product['text_domain'] ) {
				$product = $candidate;

				break;
			}
		}
	}

	/**
	 * Ajax request wrapper for the update_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products: array, activation_error: null|string}
	 */
	public function ajax_update_product( array $payload ): array {
		$payload = wp_parse_args(
			$payload,
			[
				'text_domain'                 => null,
				'frontend_foundation_version' => 0,
			]
		);

		if ( ! Framework::get_instance()->current_user_can( 'update_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$product = $this->get_first_product_by_payload( $payload );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		// Downloads are license-gated server-side; fail with a clear message instead of a broken package fetch.
		if ( isset( $product['licenses'] ) && ! $this->is_product_update_authorized( $product ) ) {
			throw new Exception( esc_html__( 'An active license is required to update this product.', 'gk-foundation' ) );
		}

		$this->update_product( $product );

		$backend_foundation_version = Core::VERSION;

		$activation_error = null;

		// After an update, the product is deactivated and needs to be reactivated. Activate it if it was active before the update.
		if ( $product['active'] ) {
			try {
				$this->activate_product( $product );

				// Check if the updated product comes with a newer version of the Foundation, which will be loaded if another Ajax request is made.
				$product_foundation_version = Core::get_instance()->get_plugin_foundation_version( $product['plugin_file'], true );

				$backend_foundation_version = CoreHelpers::version_compare(
					Core::VERSION,
					$product_foundation_version,
					'<'
				) ? $product_foundation_version : Core::VERSION;
			} catch ( Exception $e ) {
				$activation_error = $e->getMessage();
			}
		}

        return [
            'products'         => $this->ajax_get_products_data(),
            'activation_error' => $activation_error,
            'ui_action'        => [
                'reload' => CoreHelpers::version_compare( $backend_foundation_version, $payload['frontend_foundation_version'], '<>' ) || ( $product['has_admin_menu'] && $product['active'] && ! $activation_error ),
            ],
        ];
	}

	/**
	 * Switches a product to a pre-release channel and upgrades to the channel version.
	 *
	 * @since 1.13.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products: array, activation_error: null|string}
	 */
	public function ajax_switch_channel( array $payload ): array {
		$payload = wp_parse_args(
			$payload,
			[
				'text_domain'                 => null,
				'channel'                     => null,
				'frontend_foundation_version' => 0,
			]
		);

		if ( ! Framework::get_instance()->current_user_can( 'update_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$product = $this->get_first_product_by_payload( $payload );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		if ( 'stable' === $payload['channel'] ) {
			throw new Exception( esc_html__( 'Use the stable channel switch to return to stable.', 'gk-foundation' ) );
		}

		// Block transition if the current channel restricts it.
		// Only enforce when a prerelease is actually installed — a stale channel value
		// (e.g., from a previous install) should not restrict transitions.
		$installed_version = $product['installed_version'] ?? '';
		$is_prerelease     = ! empty( $installed_version ) && preg_match( '/-(alpha|beta|rc|nightly|dev)/i', $installed_version );
		$active_key        = $product['channel'] ?: 'stable';
		$active_channel    = $product['channels'][ $active_key ] ?? [];

		if ( $is_prerelease && isset( $active_channel['allowed_transitions'] ) && ! in_array( $payload['channel'], $active_channel['allowed_transitions'], true ) ) {
			throw new Exception( esc_html__( 'Switching to this channel is not allowed from the current channel.', 'gk-foundation' ) );
		}

		$channel_data = $product['channels'][ $payload['channel'] ] ?? null;

		if ( ! $channel_data || empty( $channel_data['version'] ) || empty( $channel_data['download'] ) ) {
			throw new Exception( esc_html__( 'No version available for this channel.', 'gk-foundation' ) );
		}

		// Set channel before triggering the upgrade.
		ChannelManager::get_instance()->set_channel( $product['text_domain'], $payload['channel'] );

		// Clear cached products data so the next fetch picks up the channel change.
		WP::delete_transient( self::PRODUCTS_DATA_CACHE_ID );

		$was_installed = $product['installed'];
		$was_active    = $product['active'];

		if ( $was_installed ) {
			PackageVerifier::$is_channel_switch       = true;
			PackageVerifier::$active_channel_override = $payload['channel'];

			try {
				$this->update_product(
					$product,
					[
						'download_url'   => $channel_data['download'],
						'version'        => $channel_data['version'],
						'signature'      => $channel_data['signature'] ?? '',
						'signing_key_id' => $channel_data['signing_key_id'] ?? '',
						'sha256'         => $channel_data['sha256'] ?? '',
						'filename'       => $channel_data['filename'] ?? '',
					]
				);
			} finally {
				PackageVerifier::$is_channel_switch       = false;
				PackageVerifier::$active_channel_override = null;
			}
		} else {
			$this->install_product( $product, $channel_data['download'] );
		}

		// Re-fetch product after install/update to get updated path, installed status, etc.
		$product = $this->get_first_product_by_payload( $payload, [ 'skip_request_cache' => true ] );

		$activation_error           = null;
		$backend_foundation_version = Core::VERSION;

		if ( $product['active'] || $was_active || ! $was_installed ) {
			try {
				$this->activate_product( $product );

				$product_foundation_version = Core::get_instance()->get_plugin_foundation_version( $product['plugin_file'], true );

				$backend_foundation_version = CoreHelpers::version_compare(
					Core::VERSION,
					$product_foundation_version ?? '0',
					'<'
				) ? $product_foundation_version : Core::VERSION;
			} catch ( Exception $e ) {
				$activation_error = $e->getMessage();
			}
		}

		return [
			'products'         => $this->ajax_get_products_data(),
			'activation_error' => $activation_error,
			'ui_action'        => [
				'reload' => CoreHelpers::version_compare( $backend_foundation_version, $payload['frontend_foundation_version'], '<>' ) || ( $product['has_admin_menu'] && ! $activation_error ),
			],
		];
	}

	/**
	 * Switches a product to the stable channel and downgrades to the stable version.
	 *
	 * @since 1.13.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products: array, activation_error: null|string}
	 */
	public function ajax_switch_to_stable( array $payload ): array {
		$payload = wp_parse_args(
			$payload,
			[
				'text_domain'                 => null,
				'frontend_foundation_version' => 0,
			]
		);

		if ( ! Framework::get_instance()->current_user_can( 'update_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$product = $this->get_first_product_by_payload( $payload );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		// Block transition if the current channel restricts it.
		// Only enforce when a prerelease is actually installed.
		$installed_version = $product['installed_version'] ?? '';
		$is_prerelease     = ! empty( $installed_version ) && preg_match( '/-(alpha|beta|rc|nightly|dev)/i', $installed_version );
		$active_key        = $product['channel'] ?: 'stable';
		$active_channel    = $product['channels'][ $active_key ] ?? [];

		if ( $is_prerelease && isset( $active_channel['allowed_transitions'] ) && ! in_array( 'stable', $active_channel['allowed_transitions'], true ) ) {
			throw new Exception( esc_html__( 'Switching to stable is not allowed from the current channel.', 'gk-foundation' ) );
		}

		// Clear cached products data.
		WP::delete_transient( self::PRODUCTS_DATA_CACHE_ID );

		// Resolve stable download URL — bypasses get_products_data() which may return stale cached channel data.
		$stable_channel  = $product['channels']['stable'] ?? [];
		$stable_version  = $stable_channel['version'] ?? $product['server_version'];
		$stable_download = $stable_channel['download'] ?? '';

		if ( ! $stable_download ) {
			$licenses_data   = LicenseManager::get_instance()->get_all_licenses_data();
			$stable_download = EDD::pick_download_link( $product, $licenses_data );
		}

		$override = [];

		if ( $stable_download && $stable_version ) {
			$override = [
				'download_url'   => $stable_download,
				'version'        => $stable_version,
				'signature'      => $stable_channel['signature'] ?? $product['signature'] ?? '',
				'signing_key_id' => $stable_channel['signing_key_id'] ?? $product['signing_key_id'] ?? '',
				'sha256'         => $stable_channel['sha256'] ?? $product['sha256'] ?? '',
				'filename'       => $stable_channel['filename'] ?? $product['filename'] ?? '',
			];
		}

		// Perform the upgrade/downgrade (same mechanism as update_product).
		PackageVerifier::$is_channel_switch       = true;
		PackageVerifier::$active_channel_override = 'stable';

		try {
			$this->update_product( $product, $override );
		} finally {
			PackageVerifier::$is_channel_switch       = false;
			PackageVerifier::$active_channel_override = null;
		}

		// Only clear the channel after a successful update. If the update fails,
		// the channel preference is preserved so the user stays on their current track.
		ChannelManager::get_instance()->clear_channel( $product['text_domain'] );

		$was_active = $product['active'];

		// Re-fetch product after update to get updated path and status.
		$product = $this->get_first_product_by_payload( $payload, [ 'skip_request_cache' => true ] );

		$activation_error           = null;
		$backend_foundation_version = Core::VERSION;

		if ( $was_active ) {
			try {
				$this->activate_product( $product );

				$product_foundation_version = Core::get_instance()->get_plugin_foundation_version( $product['plugin_file'], true );

				$backend_foundation_version = CoreHelpers::version_compare(
					Core::VERSION,
					$product_foundation_version ?? '0',
					'<'
				) ? $product_foundation_version : Core::VERSION;
			} catch ( Exception $e ) {
				$activation_error = $e->getMessage();
			}
		}

		return [
			'products'         => $this->ajax_get_products_data(),
			'activation_error' => $activation_error,
			'ui_action'        => [
				'reload' => CoreHelpers::version_compare( $backend_foundation_version, $payload['frontend_foundation_version'], '<>' ) || ( $product['has_admin_menu'] && ! $activation_error ),
			],
		];
	}

	/**
	 * Updates a product.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Made the $product parameter an array of product data.
	 * @since 1.13.0   Added optional $override parameter for direct download URL/version.
	 *
	 * @param array $product  Product data.
	 * @param array $override {
	 *     Optional. When provided, bypasses the products-data cache and builds
	 *     the update transient directly from these values. Used by channel-switch
	 *     flows where the cache may be stale.
	 *
	 *     @type string $download_url Download URL for the package.
	 *     @type string $version      Target version string.
	 * }
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function update_product( array $product, array $override = [] ) {
		if ( ! file_exists( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' ) ) {
			throw new Exception( esc_html__( 'Unable to load core WordPress files required to install the product.', 'gk-foundation' ) );
		}

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		if ( ! empty( $override['download_url'] ) && ! empty( $override['version'] ) ) {
			// Build the transient directly — bypasses get_products_data() which may return stale cached channel data.
			$update_plugins_transient_filter = function ( $transient_data ) use ( $product, $override ) {
				if ( ! $transient_data ) {
					$transient_data = new stdClass();
				}

				if ( empty( $transient_data->checked ) ) {
					$transient_data->checked = [];
				}

				$transient_data->checked[ $product['path'] ] = $product['installed_version'];

				$transient_data->response[ $product['path'] ] = (object) [
					'plugin'      => $product['path'],
					'slug'        => $product['slug'],
					'new_version' => $override['version'],
					'package'     => $override['download_url'],
				];

				return $transient_data;
			};
		} else {
			// Fallback: resolve from the full products-data pipeline.
			$update_plugins_transient_filter = function ( $transient_data ) use ( $product ) {
				if ( ! $transient_data ) {
					$transient_data          = new stdClass();
					$transient_data->checked = [ $product['path'] => $product['installed_version'] ];
				}

				return EDD::get_instance()->check_for_product_updates( $transient_data );
			};
		}

		// Tampering with the user-agent header (e.g., done by the WordPress Classifieds Plugin) breaks the update process.
		$lock_user_agent_header = function ( $args, $url ) {
			if ( strpos( $url, 'gravitykit.com' ) !== false ) {
				$args['user-agent'] = 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url();
			}

			return $args;
		};

		$this->cleanup_upgrade_temp_backup( $product );

		$updater = new Plugin_Upgrader( new WPUpgraderSkin() );

		try {
			add_filter( 'pre_site_transient_update_plugins', $update_plugins_transient_filter );
			add_filter( 'http_request_args', $lock_user_agent_header, 100, 2 );

			$updater->upgrade( $product['path'] );

			remove_filter( 'pre_site_transient_update_plugins', $update_plugins_transient_filter );
			remove_filter( 'http_request_args', $lock_user_agent_header, 100 );
		} catch ( Exception $e ) {
			$error = join(
				' ',
				[
					esc_html__( 'Update failed.', 'gk-foundation' ),
					$updater->strings[ $e->getMessage() ] ?? $e->getMessage(),
				]
			);

			throw new Exception( $error );
		}

		if ( ! $updater->result ) {
			throw new Exception( esc_html__( 'Installation failed.', 'gk-foundation' ) );
		}
	}

	/**
	 * Ajax request wrapper for the delete_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products: array}
	 */
	public function ajax_delete_product( array $payload ): array {
		$payload = wp_parse_args(
			$payload,
			[
				'text_domain' => null,
			]
		);

		if ( ! Framework::get_instance()->current_user_can( 'delete_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$product = $this->get_first_product_by_payload( $payload );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		if ( $product['active'] ) {
			throw new Exception(
				esc_html__( 'Product must be deactivated before it can be deleted.', 'gk-foundation' )
			);
		}

		$this->delete_product( $product );

		return [
			'products' => $this->ajax_get_products_data(),
		];
	}

	/**
	 * Deletes a product.
	 *
	 * @since 1.2.0
	 *
	 * @param array $product Product data.
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function delete_product( array $product ) {
		$clear_cache_after_delete = function ( $plugin_path ) use ( $product ) {
			if ( $plugin_path !== $product['path'] ) {
				return;
			}

			wp_cache_delete( 'plugins', 'plugins' );
		};

		add_action( 'delete_plugin', $clear_cache_after_delete );

		$result = delete_plugins( [ $product['path'] ] );

		remove_action( 'delete_plugin', $clear_cache_after_delete );

		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message() );
		}

		if ( is_null( $result ) ) {
			throw new Exception( esc_html__( 'Could not delete the product due to missing filesystem credentials.', 'gk-foundation' ) );
		}
	}

	/**
	 * Ajax request wrapper for the activate_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products: array}
	 */
	public function ajax_activate_product( array $payload ): array {
		$payload = wp_parse_args(
			$payload,
			[
				'text_domain'                 => null,
				'frontend_foundation_version' => 0,
			]
		);

		if ( ! Framework::get_instance()->current_user_can( 'activate_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$product = $this->get_first_product_by_payload( $payload ) ?? CoreHelpers::get_installed_plugin_by_text_domain( $payload['text_domain'] );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		// Network-wide activation requires a license that covers the whole network; single sites can still activate
		// individually. Coverage is computed live from license data — cached product payloads may predate the flag.
		if ( CoreHelpers::is_network_admin() && isset( $product['licenses'] ) && empty( $product['free'] ) && empty( $product['third_party'] ) && ! $this->has_network_license( $product['licenses'] ) ) {
			throw new Exception( esc_html__( 'A network license is required to activate this product for all sites on the network.', 'gk-foundation' ) );
		}

		$this->activate_product( $product );

		// Check if the activated product comes with a newer version of the Foundation, which will be loaded if another Ajax request is made.
		$product_foundation_version = Core::get_instance()->get_plugin_foundation_version( $product['plugin_file'] );

		$backend_foundation_version = CoreHelpers::version_compare(
			Core::VERSION,
			$product_foundation_version ?? '0',
			'<'
		) ? $product_foundation_version : Core::VERSION;

        return [
            'products'  => $this->ajax_get_products_data(),
            'ui_action' => [
                'reload' => CoreHelpers::version_compare( $backend_foundation_version, $payload['frontend_foundation_version'], '<>' ) || $product['has_admin_menu'],
            ],
        ];
	}

	/**
	 * Activates a product.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Made the $product parameter an array of product data.
	 *
	 * @param array $product Product data.
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function activate_product( array $product ) {
		if ( $this->is_product_active_in_current_context( $product['path'] ) ) {
			throw new Exception( esc_html__( 'Product is already active.', 'gk-foundation' ) );
		}

		$result = activate_plugin( $product['path'], '', CoreHelpers::is_network_admin() );

		if ( is_wp_error( $result ) ) {
			throw new Exception(
				strtr(
					esc_html_x( 'Could not activate the product. [error]', 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
					[ '[error]' => $result->get_error_message() ]
				)
			);
		}
	}

	/**
	 * Returns whether any of the given license keys covers the entire network.
	 *
	 * @since 1.25.0
	 *
	 * @param array $license_keys License keys.
	 *
	 * @return bool
	 */
	private function has_network_license( array $license_keys ): bool {
		$license_manager = LicenseManager::get_instance();
		$licenses_data   = $license_manager->get_licenses_data();

		foreach ( $license_keys as $license_key ) {
			if ( isset( $licenses_data[ $license_key ] ) && $license_manager->is_license_active_for_network( $licenses_data[ $license_key ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns whether any of the given license keys is active for the current site.
	 *
	 * @since 1.25.0
	 *
	 * @param array $license_keys License keys.
	 *
	 * @return bool
	 */
	private function has_site_license( array $license_keys ): bool {
		$license_manager = LicenseManager::get_instance();
		$licenses_data   = $license_manager->get_licenses_data();

		foreach ( $license_keys as $license_key ) {
			if ( isset( $licenses_data[ $license_key ] ) && $license_manager->is_license_active_for_site( $licenses_data[ $license_key ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns whether the current context holds a license that authorizes updating the product.
	 *
	 * Computed live from license data — cached product payloads may predate the licensed flags.
	 *
	 * @since 1.25.0
	 *
	 * @param array $product Product data.
	 *
	 * @return bool
	 */
	public function is_product_update_authorized( array $product ): bool {
		if ( ! empty( $product['free'] ) ) {
			return true;
		}

		$license_keys = $product['licenses'] ?? [];

		return CoreHelpers::is_network_admin() ? $this->has_network_license( $license_keys ) : $this->has_site_license( $license_keys );
	}

	/**
	 * Ajax request wrapper for the deactivate_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array{products: array}
	 */
	public function ajax_deactivate_product( array $payload ): array {
		$payload = wp_parse_args(
			$payload,
			[
				'text_domain'                 => null,
				'frontend_foundation_version' => 0,
			]
		);

		if ( ! Framework::get_instance()->current_user_can( 'deactivate_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$product = $this->get_first_product_by_payload( $payload );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-foundation' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		$this->deactivate_product( $product );

		$backend_foundation_version = Core::get_instance()->get_latest_foundation_version_from_registered_plugins( $product['text_domain'] );

        return [
            'products'  => $this->ajax_get_products_data(),
            'ui_action' => [
                'reload'   => CoreHelpers::version_compare( $backend_foundation_version, $payload['frontend_foundation_version'], '<>' ) || $product['has_admin_menu'],
                'redirect' => ! $backend_foundation_version ? [
                    'url'            => CoreHelpers::is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' ),
                    'loader_title'   => esc_html__( 'Redirecting to the Plugins page…', 'gk-foundation' ),
                    'loader_message' => esc_html__( 'Manage Your Kit functionality is no longer available.', 'gk-foundation' ),
                ] : false,
            ],
        ];
	}

	/**
	 * Deactivates a product.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Made the $product parameter an array of product data.
	 *
	 * @param array $product Product data.
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function deactivate_product( array $product ) {
		if ( ! $this->is_product_active_in_current_context( $product['path'] ) ) {
			throw new Exception( esc_html__( 'Product in not active.', 'gk-foundation' ) );
		}

		deactivate_plugins( $product['path'], false, CoreHelpers::is_network_admin() );

		if ( $this->is_product_active_in_current_context( $product['path'] ) ) { // @phpstan-ignore if.alwaysTrue (Safety check: deactivate_plugins() may silently fail.)
			throw new Exception( esc_html__( 'Could not deactivate the product.', 'gk-foundation' ) );
		}
	}

	/**
	 * Returns a list of all GravityKit products from the API grouped by category (e.g., plugins, extensions, etc.).
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Result is no longer grouped by category and is now keyed by product ID.
	 * @since 1.7.0 Added request locking to prevent concurrent API calls.
	 *
	 * @param bool $force_refresh (optional) Whether to bypass cache and fetch fresh data. Default: false.
	 *
	 * @throws LockAcquisitionException When unable to acquire lock for product fetch operation.
	 * @throws Exception
	 *
	 * @return array
	 */
	public function get_remote_products( bool $force_refresh = false ): array {
		// Failed fetches are memoized for the request: successes are cached in a transient, but a
		// failure would otherwise retry the license server on every consumer call in the request.
		static $failed_fetch = null;

		if ( $failed_fetch instanceof Exception && ! $force_refresh ) {
			throw $failed_fetch;
		}

		if ( $force_refresh ) {
			$failed_fetch = null;
		}

		$cache_key = self::PRODUCTS_DATA_CACHE_ID;
		$lock_key  = $this->get_fetch_lock_key( $force_refresh );

		// Try to wait for any concurrent fetch to complete.
		if ( ! $force_refresh ) {
			$cached_result = $this->wait_for_concurrent_fetch( $lock_key, $cache_key );
			if ( false !== $cached_result ) {
				return $cached_result;
			}
		}

		// Acquire lock or handle race condition.
		$lock_token = $this->acquire_fetch_lock( $lock_key );

		// Delete cache after acquiring lock to prevent race condition.
		if ( $force_refresh && false !== $lock_token ) {
			WP::delete_transient( $cache_key );
		}

		// If we lost the lock race, check if cache is now available.
		if ( false === $lock_token ) {
			$cached_result = $this->get_cached_products( $cache_key );
			if ( false !== $cached_result ) {
				return $cached_result;
			}

			// Lock not acquired and no cache available - do not proceed to fetch.
			throw LockAcquisitionException::failed(
				$lock_key,
				esc_html__( 'Unable to fetch product data at this time. Please try again.', 'gk-foundation' ),
				[
					'cache_key' => $cache_key,
					'reason'    => 'Failed to acquire distributed lock for concurrent fetch prevention',
				]
			);
		}

		try {
			$normalized_products = $this->fetch_and_normalize_products();
		} catch ( Exception $e ) {
			$failed_fetch = $e;

			$this->release_fetch_lock( $lock_key, $lock_token );
			throw $e;
		}

		// Release lock now that we're done.
		$this->release_fetch_lock( $lock_key, $lock_token );

		return $normalized_products;
	}

	/**
	 * Generates the lock key for product fetching operations.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $force_refresh Whether this is a force refresh operation.
	 *
	 * @return string
	 */
	private function get_fetch_lock_key( bool $force_refresh ): string {
		$lock_key = self::PRODUCTS_DATA_CACHE_ID . '/fetch_lock';

		// For force refresh, add timestamp to ensure fresh fetch while allowing concurrent requests to share it.
		if ( $force_refresh ) {
			$lock_key .= '_' . floor( time() / self::FORCE_REFRESH_LOCK_WINDOW );
		}

		return $lock_key;
	}

	/**
	 * Waits for a concurrent fetch operation to complete and returns cached result if available.
	 *
	 * @since 1.7.0
	 *
	 * @param string $lock_key  The lock key to monitor.
	 * @param string $cache_key The cache key to check for results.
	 *
	 * @return array|false Array of products if found in cache, false otherwise.
	 */
	private function wait_for_concurrent_fetch( string $lock_key, string $cache_key ) {
		$initial_lock_value = WP::get_transient( $lock_key );

		if ( false === $initial_lock_value ) {
			return false;
		}

		// Another process is fetching. Poll for the cached result using exponential backoff.
		$attempt    = 0;
		$poll_delay = self::FETCH_WAIT_POLL_INITIAL;

		while ( $attempt < self::FETCH_WAIT_MAX_ATTEMPTS ) {
			// Exponential backoff: start at 100ms, double each time, cap at 1s.
			usleep( $poll_delay );

			$poll_delay = min( $poll_delay * 2, self::FETCH_WAIT_POLL_MAX );

			// Check if the fetch completed and data is now cached.
			$cached_result = $this->get_cached_products( $cache_key );
			if ( false !== $cached_result ) {
				LoggerFramework::get_instance()->debug( 'Using cached products from concurrent request' );
				return $cached_result;
			}

			// Check if lock was released (other process finished or failed).
			if ( false === WP::get_transient( $lock_key ) ) {
				return false;
			}

			++$attempt;
		}

		// If we timed out and lock still exists, attempt to release it.
		// release_fetch_lock will verify ownership before releasing.
		if ( false !== WP::get_transient( $lock_key ) ) {
			LoggerFramework::get_instance()->warning( 'Stale fetch lock detected after timeout, attempting to release' );
			$this->release_fetch_lock( $lock_key, $initial_lock_value );
		}

		return false;
	}

	/**
	 * Retrieves cached products data if valid.
	 *
	 * @since 1.7.0
	 *
	 * @param string $cache_key The cache key to check.
	 *
	 * @return array|false Array of products if found and valid, false otherwise.
	 */
	private function get_cached_products( string $cache_key ) {
		$cached_products = WP::get_transient( $cache_key );

		if ( ! $cached_products ) {
			return false;
		}

		$decoded = json_decode( $cached_products, true );

		if ( empty( $decoded['raw'] ) || ! is_array( $decoded['raw'] ) ) {
			return false;
		}

		return $decoded['raw'];
	}

	/**
	 * Attempts to acquire a lock for product fetching.
	 *
	 * @since 1.7.0
	 *
	 * @param string $lock_key The lock key to acquire.
	 *
	 * @return string|false The unique lock token if acquired, false if another process has the lock.
	 */
	private function acquire_fetch_lock( string $lock_key ) {
		// Attempt to acquire lock using a unique value to detect race conditions.
		$our_lock_value = uniqid( 'gk_fetch_', true );
		WP::set_transient( $lock_key, $our_lock_value, self::FETCH_LOCK_TIMEOUT );

		// Verify we actually got the lock (handles race condition).
		usleep( self::FETCH_LOCK_RACE_DELAY );

		if ( WP::get_transient( $lock_key ) === $our_lock_value ) {
			return $our_lock_value;
		}

		// Another process won the race. Wait briefly to give it time to cache results.
		usleep( self::FETCH_LOCK_LOST_RACE_WAIT );

		return false;
	}

	/**
	 * Safely releases a lock by verifying ownership before deletion.
	 *
	 * Only deletes the lock if the current lock value matches our token,
	 * preventing accidental deletion of locks acquired by other processes.
	 *
	 * @since 1.7.0
	 *
	 * @param string $lock_key   The lock key to release.
	 * @param string $lock_token The unique token that was returned when the lock was acquired.
	 *
	 * @return void
	 */
	private function release_fetch_lock( string $lock_key, string $lock_token ): void {
		$current_lock_value = WP::get_transient( $lock_key );

		// Only delete the lock if we still own it.
		if ( $current_lock_value !== $lock_token ) {
			return;
		}

		WP::delete_transient( $lock_key );
	}

	/**
	 * Safely unserializes a PHP-serialized array field from the remote EDD API response.
	 *
	 * The EDD API returns `readme.sections`, `readme.banners`, and `readme.icons` as
	 * PHP-serialized strings embedded inside its JSON envelope. Because those bytes
	 * originate from a network call that is theoretically MitM-reachable, they must be
	 * treated as attacker-controllable. `allowed_classes => false` prevents PHP object
	 * injection — any class in the payload decodes to `__PHP_Incomplete_Class` instead
	 * of instantiating, which kills the `__destruct`/`__wakeup` gadget chain. The
	 * `is_array()` check rejects malformed payloads that would otherwise surface as
	 * `false`/scalar down-stream in the normalization logic.
	 *
	 * @since 1.15.0
	 *
	 * @param mixed $raw Value from the API response. Expected: string. Anything else → [].
	 *
	 * @return array Decoded array, or [] on any failure (non-string, empty, malformed, non-array).
	 */
	private function safe_unserialize_array( $raw ): array {
		if ( ! is_string( $raw ) || '' === $raw ) {
			return [];
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize,WordPress.PHP.NoSilencedErrors.Discouraged -- allowed_classes=>false blocks object-injection gadgets; the @ suppresses E_NOTICE on malformed payloads that the is_array() guard below handles as the actual error path.
		$decoded = @unserialize( $raw, [ 'allowed_classes' => false ] );

		return is_array( $decoded ) ? $decoded : [];
	}

	/**
	 * Fetches products from the API and normalizes them.
	 *
	 * @since 1.7.0
	 * @since TBD Preserve server-driven product notices from the catalog response.
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	private function fetch_and_normalize_products(): array {
		$response = Helpers::query_api(
			LicenseManager::store_url() . '/products',
			[
				'api_version' => self::STORE_API_VERSION,
				'bust_cache'  => time(),
			]
		);

		$products = Arr::get( $response, 'products', [] );

		if ( empty( $response ) || empty( $products ) ) {
			throw new Exception( esc_html__( 'Invalid product information received from the API.', 'gk-foundation' ) );
		}

		$normalized_products = [];

		foreach ( $products as $product ) {
			$product_id = Arr::get( $product, 'info.id' );
			$sections   = $this->safe_unserialize_array( Arr::get( $product, 'readme.sections', '' ) );
			$banners    = $this->safe_unserialize_array( Arr::get( $product, 'readme.banners', '' ) );
			$icons      = $this->safe_unserialize_array( Arr::get( $product, 'readme.icons', '' ) );

			if ( ! Arr::get( $product, 'info.category_slug' ) || 'bundles' === Arr::get( $product, 'info.category_slug' ) ) {
				continue;
			}

			$product_schema = $this->get_product_schema();

			$normalized_products[ $product_id ] = $this->normalize_product_data(
				[
					'id'                 => $product_id,
					'slug'               => Arr::get( $product, 'info.slug', $product_schema['slug'] ),
					'category_name'      => Arr::get( $product, 'info.category_name', $product_schema['category_name'] ),
					'category_slug'      => Arr::get( $product, 'info.category_slug', $product_schema['category_slug'] ),
					'category_order'     => Arr::get( $product, 'info.category_order', $product_schema['category_order'] ),
					'text_domain'        => Arr::get( $product, 'info.text_domain', $product_schema['text_domain'] ),
					'text_domain_legacy' => Arr::get( $product, 'info.text_domain_legacy', $product_schema['text_domain_legacy'] ),
					'has_admin_menu'     => Arr::get( $product, 'info.has_admin_menu', $product_schema['has_admin_menu'] ),
					'hidden'             => Arr::get( $product, 'info.hidden', $product_schema['hidden'] ),
					'free'               => Arr::get( $product, 'info.free', $product_schema['free'] ),
					'third_party'        => Arr::get( $product, 'info.third_party', $product_schema['third_party'] ),
					'coming_soon'        => Arr::get( $product, 'info.coming_soon', $product_schema['coming_soon'] ),
					'name'               => Arr::get( $product, 'info.title', $product_schema['name'] ),
					'excerpt'            => Arr::get( $product, 'info.excerpt', $product_schema['excerpt'] ),
					'buy_link'           => esc_url_raw( $product['info']['buy_url'] ?? $product_schema['buy_link'] ),
					'link'               => esc_url_raw( $product['info']['link'] ?? $product_schema['link'] ),
					'download_link'      => esc_url_raw( $product['info']['download_link'] ?? $product_schema['download_link'] ),
					'icon'               => esc_url_raw( $product['info']['icon'] ?? $product_schema['icon'] ),
					'icons'              => [
						'1x' => esc_url_raw( $icons['1x'] ?? $product_schema['icons']['1x'] ),
						'2x' => esc_url_raw( $icons['2x'] ?? $product_schema['icons']['2x'] ),
					],
					'banners'            => [
						'low'  => esc_url_raw( $banners['low'] ?? $product_schema['banners']['low'] ),
						'high' => esc_url_raw( $banners['high'] ?? $product_schema['banners']['low'] ),
					],
					'sections'           => [
						'description' => wp_kses_post( Arr::get( $sections, 'description', $product_schema['sections']['description'] ) ),
						'changelog'   => wp_kses_post(
							$this->truncate_product_changelog(
								Arr::get( $sections, 'changelog', $product_schema['sections']['changelog'] ),
								esc_url_raw( $product['info']['link'] ?? $product_schema['link'] )
							)
						),
					],
					'server_version'     => Arr::get( $product, 'licensing.version', $product_schema['server_version'] ),
					'modified_date'      => Arr::get( $product, 'info.modified_date', $product_schema['modified_date'] ),
					'docs'               => esc_url_raw( $product['info']['docs_url'] ?? $product_schema['docs'] ),
					'dependencies'       => Arr::get( $product, 'dependencies', $product_schema['dependencies'] ),
					'update_notices'     => Arr::get( $product, 'update_notices', $product_schema['update_notices'] ),
					'product_notices'    => Arr::get( $product, 'product_notices', $product_schema['product_notices'] ),
					'signature'          => Arr::get( $product, 'integrity.signature', '' ),
					'signing_key_id'     => Arr::get( $product, 'integrity.signing_key_id', '' ),
					'sha256'             => Arr::get( $product, 'integrity.sha256', '' ),
					'filename'           => Arr::get( $product, 'integrity.filename', '' ),
				]
			);
		}

		return $normalized_products;
	}

	/**
	 * Truncates the product changelog to display only the specified number of most recent entries.
	 *
	 * @since 1.0.11
	 *
	 * @param string $changelog              Product changelog.
	 * @param string $product_url            Product URL.
	 * @param int    $max_changelog_entries  (optional) Number of entries to display. Default: 3.
	 * @param bool   $link_to_full_changelog (optional) Display a link to the full changelog on GravityKit's website. Default: true.
	 *
	 * @return string
	 */
	public function truncate_product_changelog( $changelog, $product_url, $max_changelog_entries = 3, $link_to_full_changelog = true ) {
		// Match version headers in both formats: <p><strong>X.Y.Z on Date</strong></p> and <h4>X.Y.Z on Date</h4>.
		$changelog_pattern = '~((?:<p><strong>|<h4>)\d+.*?on.*?(?=(?:<p><strong>|<h4>)\d+.*?on|$))~s';

		preg_match_all( $changelog_pattern, $changelog, $parsed_changelog );

		if ( empty( $parsed_changelog[0] ) ) {
			return $changelog;
		}

		$changelog = '';

		$truncated_changelog = array_slice( $parsed_changelog[0], 0, $max_changelog_entries );

		if ( count( $parsed_changelog[0] ) > count( $truncated_changelog ) && $link_to_full_changelog ) {
			$truncated_changelog[] = sprintf(
				'<p><a href="%s#changelog" target="_blank">%s</a></strong></p><br><br><br>', // 3 line breaks are required for this line to be displayed correctly above the fixed modal window footer.
				$product_url,
				esc_html__( 'View full changelog', 'gk-foundation' )
			);
		}

		foreach ( $truncated_changelog as $changelog_entry ) {
			$modified_changelog_entry = $changelog_entry;
			$modified_changelog_entry = preg_replace( '~<p><strong>(\d+.*?on.*?)</strong></p>~s', '<h4>$1</h4>', $modified_changelog_entry, 1 );
			$modified_changelog_entry = preg_replace( '~<a~s', '<a class="gk-link"', $modified_changelog_entry );
			$changelog               .= $modified_changelog_entry;
		}

		return $changelog;
	}

	/**
	 * Ajax request wrapper for the {@see get_products_data()} method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload Ajax request payload.
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function ajax_get_products_data( array $payload = [] ) {
		if ( ! Framework::get_instance()->current_user_can( 'view_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-foundation' ) );
		}

		$payload = wp_parse_args(
			$payload,
			[
				'skip_remote_cache'  => false,
				'skip_request_cache' => true,
			]
		);

		$products = $this->get_products_data( $payload );

		// Rebuild the WordPress update transient so the Plugins page reflects current update state.
		delete_site_transient( 'update_plugins' );
		wp_update_plugins();

		$excluded_properties = [
			'path',
			'plugin_file',
			'dependencies',
			'text_domain_legacy',
			'text_domains',
			'modified_date',
			'icons',
			'banners',
		];

		// A channel download URL sourced from a network-scoped (or another site's) license is that
		// licenser's signed entitlement. A subsite admin keeps only URLs backed by the site's own
		// licenses — they cannot install or update network products anyway (see Framework caps).
		$can_see_network_downloads = ! is_multisite() || current_user_can( 'manage_network_options' );

		$own_downloads = [];

		if ( ! $can_see_network_downloads ) {
			foreach ( LicenseManager::get_instance()->get_site_scoped_licenses_data() as $license ) {
				foreach ( $license['products'] ?? [] as $product_id => $product_data ) {
					if ( ! empty( $product_data['download'] ) ) {
						$own_downloads[ $product_id ]['stable'] = (string) $product_data['download'];
					}

					foreach ( $product_data['channels'] ?? [] as $channel_name => $channel_data ) {
						if ( ! empty( $channel_data['download'] ) ) {
							$own_downloads[ $product_id ][ $channel_name ] = (string) $channel_data['download'];
						}
					}
				}
			}
		}

		foreach ( $products as $key => &$product ) {
			// Unset properties that are not needed in the UI.
			foreach ( $excluded_properties as $property ) {
				if ( isset( $product[ $property ] ) ) {
					unset( $product[ $property ] );
				}
			}

			// Hide products that are not meant to be displayed in the UI.
			if ( $product['hidden'] ) {
				unset( $products[ $key ] );

				continue;
			}

			if ( ! $can_see_network_downloads ) {
				foreach ( $product['channels'] ?? [] as $channel_name => $channel_data ) {
					if ( empty( $channel_data['download'] ) ) {
						continue;
					}

					// The resolved channel URL may be a network (or another site's) licenser's signed
					// entitlement — channels are filled network-first. Replace it with the site's OWN license
					// URL for this channel; else the public free-product link; else blank it.
					if ( isset( $own_downloads[ $product['id'] ][ $channel_name ] ) ) {
						$product['channels'][ $channel_name ]['download'] = $own_downloads[ $product['id'] ][ $channel_name ];
					} elseif ( ! empty( $product['free'] ) && ! empty( $product['download_link'] ) ) {
						$product['channels'][ $channel_name ]['download'] = (string) $product['download_link'];
					} else {
						$product['channels'][ $channel_name ]['download'] = '';
					}
				}
			}

			// Nonce derived from the key: same key → identical ciphertext this request (stable identifier),
			// different keys → different nonces (a shared nonce reuses the XSalsa20 keystream across keys).
			$product['licenses'] = array_map(
				function ( $key ) {
					$nonce = hash_hmac( 'sha256', $key, Core::get_request_unique_string(), true );

					return Encryption::get_instance()->encrypt( $key, false, $nonce );
				},
				$product['licenses']
			);
		}

		return array_values( $products );
	}

	/**
	 * Returns the last-known-good product data from the cache transient, or null when unavailable.
	 *
	 * Lets a fresh fetch that can't complete — lock contention or a slow/unreachable Store — fall back
	 * to previously cached data so admin pages keep rendering (with slightly stale update info) instead
	 * of showing an empty product list.
	 *
	 * @since 1.22.0
	 *
	 * @return array|null The cached product data structure, or null when no usable cache exists.
	 */
	private function get_stale_cached_products(): ?array {
		$stale = WP::get_transient( self::PRODUCTS_DATA_CACHE_ID );

		if ( $stale && ! is_array( $stale ) ) {
			$stale = json_decode( $stale, true );
		}

		return is_array( $stale ) && ! empty( $stale['raw'] ) ? $stale : null;
	}

	/**
	 * Returns a list of all GravityKit products with associated installation/activation/licensing data.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Result is now keyed by product's text domain.
	 * @since TBD Reconcile notices from valid, complete product catalogs.
	 *
	 * @param array $args (optional) Additional arguments. Default: ['skip_cache_remote' => false, 'skip_request_cache' => false, 'key_by' => 'text_domain'].
	 *
	 * @return array
	 */
	public function get_products_data( array $args = [] ) {
		static $_cached_products_data_by_blog = [];

		// Licenses (and therefore product data) differ per site on multisite; don't reuse another site's cache after switch_to_blog().
		$_cache_blog_id        = is_multisite() ? get_current_blog_id() : 0;
		$_cached_products_data = $_cached_products_data_by_blog[ $_cache_blog_id ] ?? null;

		$args = wp_parse_args(
			$args,
			[
				'skip_remote_cache'  => false, // If true, products will be fetched from the API even if they are cached locally.
				'skip_request_cache' => false, // If true, products data will be updated with the most recent changes during the same request.
				'key_by'             => 'text_domain',
			]
		);

		if ( ! $args['skip_remote_cache'] && ! $args['skip_request_cache'] && $_cached_products_data ) {
			return 'text_domain' === $args['key_by'] ? $_cached_products_data : $this->key_products_by_property( $_cached_products_data, $args['key_by'] );
		}

		$products = ! $args['skip_remote_cache'] ? ( WP::get_transient( self::PRODUCTS_DATA_CACHE_ID ) ?: null ) : null;

		if ( $products && ! is_array( $products ) ) { // Backward compatibility for serialized data (used in earlier Foundation versions).
			$products = json_decode( $products, true );
			$products = is_array( $products ) ? $products : null;
		}

		$catalog_notice_sync_is_valid = ! is_null( $products );

		if ( is_null( $products ) ) {
			$products = [
				'raw'                    => [],
				'normalized'             => [],
				'installed_plugins_hash' => null,
				'licenses_hash'          => null,
			];

			try {
				$products['raw']              = $this->get_remote_products( $args['skip_remote_cache'] );
				$catalog_notice_sync_is_valid = ! empty( $products['raw'] );
			} catch ( LockAcquisitionException $e ) {
				// Another instance is fetching. Fall back to the last-known-good cache and continue to
				// normalization so installation status, licenses, and dependencies are still current.
				LoggerFramework::get_instance()->warning(
					'Product fetch skipped due to lock contention: ' . $e->getMessage(),
					$e->get_data()
				);

				$stale_products = $this->get_stale_cached_products();

				if ( $stale_products ) {
					$products = $stale_products;
				}
			} catch ( Exception $e ) {
				// Actual API/fetch error (e.g. the Store timed out or is unreachable). Fall back to the
				// last-known-good cache so admin pages still render with (slightly stale) update info
				// instead of an empty product list, and a slow Store does not re-fetch on every request.
				LoggerFramework::get_instance()->error( 'Unable to get products from the API: ' . $e->getMessage() );

				$stale_products = $this->get_stale_cached_products();

				if ( $stale_products ) {
					$products = $stale_products;
				}
			}

			// Only cache if we have data (either fresh or from previous cache).
			if ( ! empty( $products['raw'] ) ) {
				// A stale fallback contains only the last complete, validated catalog. Re-caching it
				// is safe; the validity flag above prevents this failed request from reconciling notices.
				WP::set_transient(
					self::PRODUCTS_DATA_CACHE_ID,
					wp_json_encode( $products ),
					self::PRODUCTS_DATA_CACHE_EXPIRATION
				);
			}
		}

		if ( empty( $products['raw'] ) ) {
			$_cached_products_data_by_blog[ $_cache_blog_id ] = [];

			return [];
		}

		// Only a complete, previously validated non-empty catalog may reconcile the catalog source.
		// Error/empty fallbacks deliberately leave the last-known notice state untouched.
		if ( $catalog_notice_sync_is_valid ) {
			$this->sync_catalog_server_notices( $products['raw'] );
		}

		$installed_plugins_hash = md5( wp_json_encode( CoreHelpers::get_installed_plugins( $args['skip_request_cache'] ) ) ?: '' );
		$licenses_hash          = md5( wp_json_encode( LicenseManager::get_instance()->get_all_licenses_data() ) ?: '' );

		// If the installed plugins haven't changed since the last request, return the cached products data to prevent re-validating dependencies, etc.
		if ( $installed_plugins_hash === $products['installed_plugins_hash'] && $licenses_hash === $products['licenses_hash'] ) {
			$_cached_products_data                            = $products['normalized'];
			$_cached_products_data_by_blog[ $_cache_blog_id ] = $_cached_products_data;

			return 'text_domain' === $args['key_by'] ? $_cached_products_data : $this->key_products_by_property( $_cached_products_data, $args['key_by'] );
		} else {
			$products['installed_plugins_hash'] = $installed_plugins_hash;
			$products['licenses_hash']          = $licenses_hash;
		}

		$product_license_map = LicenseManager::get_instance()->get_all_product_license_map();

		$products_history = ProductHistoryManager::get_instance()->get_products_history();

		$products['normalized'] = [];

		$licenses_data   = LicenseManager::get_instance()->get_all_licenses_data();
		$channel_manager = ChannelManager::get_instance();

		// Display licensing is judged against the CURRENT SITE's own licenses, unlike update/download
		// resolution, which sees every license on the install.
		$site_licenses_data = LicenseManager::get_instance()->get_licenses_data();

		// Supplement API response with additional data that can change between or during requests (e.g., activation status, etc.).
		foreach ( $products['raw'] as $product ) {
			if ( ! isset( $product['text_domain'] ) ) {
				LoggerFramework::get_instance()->warning( "Unable to get text domain for {$product['name']}: " . wp_json_encode( $product ) );

				continue;
			}

			$installed_product = CoreHelpers::get_installed_plugin_by_text_domain( $product['text_domains'] );

			/**
			 * Sets link to the product settings page.
			 *
			 * @filter `gk/foundation/settings/{$product_slug}/settings-url`
			 *
			 * @since  1.0.3
			 *
			 * @param string $settings_url URL to the product settings page.
			 */
			$product_settings_url = apply_filters( "gk/foundation/settings/{$product['slug']}/settings-url", '' );

			$normalized_product = array_merge(
				$product,
				[
					'id'                => $product['id'],
					'text_domain'       => $installed_product['text_domain'] ?? $product['text_domain'],
					'installed'         => ! is_null( $installed_product ),
					'installed_version' => $installed_product['version'] ?? $product['installed_version'],
					'active'            => $installed_product['active'] ?? $product['active'],
					'custom_build'      => ! is_null( $installed_product ) && ChannelManager::is_custom_build_version( $installed_product['version'] ?? '', array_keys( $product['channels'] ?? [] ) ),
					'update_available'  => ! is_null( $installed_product ) && CoreHelpers::version_compare( ChannelManager::strip_build_suffix( $installed_product['version'] ?? '', array_keys( $product['channels'] ?? [] ) ), $product['server_version'], '<' ),
					'path'              => $installed_product['path'] ?? $product['path'],
					'plugin_file'       => $installed_product['plugin_file'] ?? $product['plugin_file'],
					'network_activated' => $installed_product['network_activated'] ?? $product['network_activated'],
					'licenses'          => $product_license_map[ $product['id'] ] ?? $product['licenses'],
					'settings'          => esc_url_raw( $product_settings_url ),
					'has_git_folder'    => $installed_product && file_exists( dirname( $installed_product['plugin_file'] ) . '/.git' ),
					'history'           => $products_history[ $product['text_domain'] ] ?? [],
				]
			);

			// Whether any of the product's licenses is actually active for the current site (not merely saved),
			// and whether any covers the entire network (gates network-wide activation).
			$normalized_product['licensed']         = false;
			$normalized_product['network_licensed'] = false;

			foreach ( $normalized_product['licenses'] as $license_key ) {
				if ( ! isset( $site_licenses_data[ $license_key ] ) ) {
					continue;
				}

				$license = $site_licenses_data[ $license_key ];

				if ( LicenseManager::get_instance()->is_license_active_for_site( $license ) ) {
					$normalized_product['licensed'] = true;
				}

				if ( LicenseManager::get_instance()->is_license_active_for_network( $license ) ) {
					$normalized_product['network_licensed'] = true;
				}

				if ( $normalized_product['licensed'] && $normalized_product['network_licensed'] ) {
					break;
				}
			}

			// Collect available channels from license data.
			$product_id          = $normalized_product['id'];
			$stable_download_url = '';

			$product_url = $normalized_product['link'];

			foreach ( $licenses_data as $license_data ) {
				$product_license_data = $license_data['products'][ $product_id ] ?? [];

				foreach ( $product_license_data['channels'] ?? [] as $channel_name => $channel_data ) {
					if ( ! empty( $channel_data['version'] ) ) {
						$normalized_product['channels'][ $channel_name ] = $this->build_channel_entry( $channel_data, $product_url );
					}
				}

				// Capture stable download URL from this license.
				if ( empty( $stable_download_url ) ) {
					$stable_download_url = $product_license_data['download'] ?? '';
				}

				// Capture integrity data from the license response.
				$product_integrity = $product_license_data['integrity'] ?? [];

				if ( ! empty( $product_integrity['signature'] ) ) {
					$normalized_product['signature']      = $product_integrity['signature'];
					$normalized_product['signing_key_id'] = $product_integrity['signing_key_id'] ?? '';
					$normalized_product['sha256']         = $product_integrity['sha256'] ?? '';
					$normalized_product['filename']       = $product_integrity['filename'] ?? '';
				}
			}

			$normalized_product['channel'] = $channel_manager->get_channel( $normalized_product['text_domain'] );

			// Auto-reset channel if it's no longer available from the server.
			$channel_manager->maybe_reset_channel( $normalized_product['text_domain'], $normalized_product );
			$normalized_product['channel'] = $channel_manager->get_channel( $normalized_product['text_domain'] );

			// Build channels.stable from root-level data if the server didn't provide one.
			if ( ! isset( $normalized_product['channels']['stable'] ) ) {
				$normalized_product['channels']['stable'] = $this->build_channel_entry(
					[
						'version'        => $normalized_product['server_version'],
						'changelog'      => $normalized_product['sections']['changelog'],
						'link'           => $normalized_product['link'],
						'docs'           => $normalized_product['docs'],
						'signature'      => $normalized_product['signature'] ?? '',
						'signing_key_id' => $normalized_product['signing_key_id'] ?? '',
						'sha256'         => $normalized_product['sha256'] ?? '',
						'filename'       => $normalized_product['filename'] ?? '',
					]
				);
			}

			// Populate stable download URL from license data or free product fallback.
			// Only set if the server didn't already provide a download URL for the stable channel.
			if ( $stable_download_url && empty( $normalized_product['channels']['stable']['download'] ) ) {
				$normalized_product['channels']['stable']['download'] = $stable_download_url;
			}

			if ( $normalized_product['free'] && $normalized_product['download_link'] && empty( $normalized_product['channels']['stable']['download'] ) ) {
				$normalized_product['channels']['stable']['download'] = $normalized_product['download_link'];
			}

			// Inject channel-specific dependencies into the product's versioned dependencies array.
			foreach ( $normalized_product['channels'] as $channel_data ) {
				if ( ! empty( $channel_data['dependencies'] ) && ! empty( $channel_data['version'] ) ) {
					$normalized_product['dependencies'][ $channel_data['version'] ] = $channel_data['dependencies'];
				}
			}

			// Supersession: remove pre-release channels whose superseded_by pattern matches the target.
			// A superseded channel no longer represents a distinct release track — the stable update takes over.
			$superseded_channels = [];

			foreach ( array_keys( $normalized_product['channels'] ) as $channel_name ) {
				$channel_name = (string) $channel_name;

				if ( 'stable' === $channel_name ) {
					continue;
				}

				if ( ! $this->resolve_channel_supersession( $normalized_product, $channel_name ) ) {
					continue;
				}

				$superseded_channels[] = $channel_name;

				// If the user is on this superseded channel, clear the preference.
				if ( $normalized_product['channel'] === $channel_name ) {
					$channel_manager->clear_channel( $normalized_product['text_domain'] );
					$normalized_product['channel'] = false;
				}
			}

			foreach ( $superseded_channels as $channel_name ) {
				unset( $normalized_product['channels'][ $channel_name ] );
			}

			// If a prerelease is installed but the channel is gone (revoked, disabled, or superseded),
			// mark an update as available so the user can switch back to stable.
			if ( ! $normalized_product['update_available']
				&& $normalized_product['installed']
				&& ! $normalized_product['channel']
				&& ChannelManager::is_prerelease_version( $normalized_product['installed_version'], array_keys( $normalized_product['channels'] ?? [] ) )
				&& $normalized_product['server_version']
			) {
				$normalized_product['update_available'] = true;
			}

			// Override product-level link and docs with active channel values.
			$active_channel_name = $normalized_product['channel'];
			$active_channel_data = $active_channel_name ? ( $normalized_product['channels'][ $active_channel_name ] ?? [] ) : [];

			if ( ! empty( $active_channel_data['link'] ) ) {
				$normalized_product['link'] = $active_channel_data['link'];
			}

			if ( ! empty( $active_channel_data['docs'] ) ) {
				$normalized_product['docs'] = $active_channel_data['docs'];
			}

			if ( ! empty( $active_channel_data['excerpt'] ) ) {
				$normalized_product['excerpt'] = $active_channel_data['excerpt'];
			}

			$products['normalized'][ $normalized_product['text_domain'] ] = $normalized_product;
		}

		/**
		 * Modifies products data object.
		 *
		 * @filter `gk/foundation/products/data`
		 *
		 * @since  1.0.3
		 *
		 * @param array $products Products data.
		 * @param array $args     Additional arguments passed to the get_products_data() method.
		 */
		$products['normalized'] = apply_filters( 'gk/foundation/products/data', $products['normalized'], $args );

		$product_dependency_checker = new ProductDependencyChecker( $products['normalized'] );

		foreach ( $products['normalized'] as &$product ) {
			$product['required_by'] = $product_dependency_checker->is_a_dependency_of_any_product( $product['text_domain'], true ) ?: [];

			$product_versions_to_check = [];

			// We need to check both installed and server versions for dependencies, for these can be different (e.g, an updated version may have new dependencies).
			if ( $product['installed'] && $product['installed_version'] ) {
				$product_versions_to_check[] = $product['installed_version'];
			}

			if ( ( ! $product['installed'] || $product['update_available'] ) && $product['server_version'] ) {
				$product_versions_to_check[] = $product['server_version'];
			}

			// Also check non-active channel versions so the UI can show dependency status for "Try Beta" etc.
			foreach ( $product['channels'] as $channel_name => $channel_data ) {
				if ( empty( $channel_data['version'] ) || 'stable' === $channel_name ) {
					continue;
				}

				if ( ! in_array( $channel_data['version'], $product_versions_to_check, true ) ) {
					$product_versions_to_check[] = $channel_data['version'];
				}
			}

			foreach ( $product_versions_to_check as $version ) {
				$result = $product_dependency_checker->check_dependencies( $product['text_domain'], $version );

				$product['checked_dependencies'][ $version ] = $result;

				if ( $result['status'] ) {
					continue;
				}

				// If the product only has unmet plugin dependencies, get the sequence of actions required to resolve them.
				if ( empty( $result['unmet']['system'] ) ) {
					try {
						$product['checked_dependencies'][ $version ]['resolution_sequence'] = $product_dependency_checker->get_product_dependency_resolution_sequence( $product['text_domain'], $version );
					} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
						// No need to do anything here since dependencies can't be satisfied.
					}
				}
			}
		}

		$_cached_products_data                            = $products['normalized'];
		$_cached_products_data_by_blog[ $_cache_blog_id ] = $_cached_products_data;

		WP::set_transient(
			self::PRODUCTS_DATA_CACHE_ID,
			wp_json_encode( $products ),
			self::PRODUCTS_DATA_CACHE_EXPIRATION
		);

		return 'text_domain' === $args['key_by'] ? $products['normalized'] : $this->key_products_by_property( $products['normalized'], $args['key_by'] );
	}

	/**
	 * Builds a normalized channel entry from raw channel data.
	 *
	 * @since 1.13.0
	 *
	 * @param array  $data        Raw channel data with optional keys: version, download, changelog, opt_in_notice, opt_out_notice, dependencies, link, docs.
	 * @param string $product_url Product URL used for the "View full changelog" link.
	 *
	 * @return array Normalized channel entry.
	 */
	private function build_channel_entry( array $data, string $product_url = '' ): array {
		$changelog = $data['changelog'] ?? '';

		if ( $changelog && $product_url ) {
			$changelog = $this->truncate_product_changelog( $changelog, $product_url );
		}

		return [
			'version'             => $data['version'] ?? '',
			'label'               => $data['label'] ?? '',
			'download'            => $data['download'] ?? '',
			'changelog'           => $changelog,
			'excerpt'             => $data['excerpt'] ?? '',
			'opt_in_notice'       => $data['opt_in_notice'] ?? null,
			'opt_out_notice'      => $data['opt_out_notice'] ?? null,
			'dependencies'        => $data['dependencies'] ?? null,
			'link'                => $data['link'] ?? '',
			'docs'                => $data['docs'] ?? '',
			'allowed_transitions' => $data['allowed_transitions'] ?? null,
			'superseded_by'       => $data['superseded_by'] ?? null,
			'signature'           => $data['signature'] ?? '',
			'signing_key_id'      => $data['signing_key_id'] ?? '',
			'sha256'              => $data['sha256'] ?? '',
			'filename'            => $data['filename'] ?? '',
		];
	}

	/**
	 * Checks whether a specific channel is superseded by a target channel.
	 *
	 * Each channel may define a `superseded_by` object with `channel` and `version_match` keys
	 * (e.g., `['channel' => 'stable', 'version_match' => '^3\.']`). When the target channel's
	 * version matches the regex, the channel is considered superseded — the UI hides it and
	 * the stable update takes precedence.
	 *
	 * @since 1.13.0
	 *
	 * @param array  $product      Normalized product data with 'channels' and 'server_version'.
	 * @param string $channel_name Channel name to check for supersession.
	 *
	 * @return string|null Target channel name if superseded, null otherwise.
	 */
	private function resolve_channel_supersession( array $product, string $channel_name ): ?string {
		$channel_data  = $product['channels'][ $channel_name ] ?? [];
		$superseded_by = $channel_data['superseded_by'] ?? null;

		if ( ! $superseded_by || ! is_array( $superseded_by ) ) {
			return null;
		}

		$target_channel = $superseded_by['channel'] ?? '';
		$version_regex  = $superseded_by['version_match'] ?? '';

		if ( ! $target_channel || ! $version_regex ) {
			return null;
		}

		// Resolve target version: root-level server_version for stable, channel version otherwise.
		$target_version = 'stable' === $target_channel
			? ( $product['server_version'] ?? '' )
			: ( $product['channels'][ $target_channel ]['version'] ?? '' );

		if ( empty( $target_version ) ) {
			return null;
		}

		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Intentional: malformed user-provided regex patterns should fail silently.
		$regex_match = @preg_match( '/' . $version_regex . '/', $target_version );

		if ( ! $regex_match ) {
			return null;
		}

		return $target_channel;
	}

	/**
	 * Keys products object by the specified product property.
	 *
	 * @since 1.2.0
	 *
	 * @param array  $products Products data.
	 * @param string $key_by   Property to key the return object by.
	 *
	 * @return array
	 */
	public function key_products_by_property( $products, $key_by ) {
		$keyed_products = [];

		foreach ( $products as $product ) {
			if ( array_key_exists( $key_by, $product ) && '' !== $product[ $key_by ] && ! is_array( $product[ $key_by ] ) ) {
				$keyed_products[ $product[ $key_by ] ] = $product;
			}
		}

		unset( $keyed_products[''] );

		return $keyed_products;
	}

	/**
	 * Checks if plugin is activated in the current context (network or site).
	 *
	 * @since 1.0.0
	 *
	 * @param string $plugin_path Plugin path.
	 *
	 * @return bool
	 */
	public function is_product_active_in_current_context( $plugin_path ) {
		return CoreHelpers::is_network_admin() ? is_plugin_active_for_network( $plugin_path ) : is_plugin_active( $plugin_path );
	}

	/**
	 * Returns installed GravityKit-managed product plugin paths.
	 *
	 * @since 1.21.0
	 *
	 * @throws Exception When product data cannot be retrieved.
	 *
	 * @return array
	 */
	public function get_managed_product_paths(): array {
		$products_data = $this->get_products_data();
		$product_paths = [];

		foreach ( $products_data as $product ) {
			if ( empty( $product['path'] ) || ! $product['installed'] ) {
				continue;
			}

			if ( $product['third_party'] && $product['hidden'] ) {
				continue;
			}

			$product_paths[] = $product['path'];
		}

		return array_values( array_unique( $product_paths ) );
	}

	/**
	 * Returns product plugin paths counted in the Manage Your Kit update badge.
	 *
	 * @since 1.21.0
	 *
	 * @throws Exception When product data cannot be retrieved.
	 *
	 * @return array
	 */
	public function get_product_paths_with_available_update(): array {
		$products_data = $this->get_products_data();
		$product_paths = [];

		foreach ( $products_data as $product ) {
			if ( empty( $product['path'] ) ) {
				continue;
			}

			// Exclude only hidden third-party products (updated by their own publisher); GravityKit-managed
			// non-hidden third-party products count, matching EDD::check_for_product_updates().
			if ( $product['third_party'] && $product['hidden'] ) {
				continue;
			}

			$channel_versions = array_keys( $product['channels'] ?? [] );

			if ( $product['update_available'] ) {
				// Suppress stable updates when actively running a prerelease channel.
				if ( $product['channel'] && ChannelManager::is_prerelease_version( $product['installed_version'], $channel_versions ) ) {
					continue;
				}

				$product_paths[] = $product['path'];

				continue;
			}

			if ( ! $product['installed'] || ! $product['channel'] ) {
				continue;
			}

			if ( ! ChannelManager::is_prerelease_version( $product['installed_version'], $channel_versions ) ) {
				continue;
			}

			$channel_data = $product['channels'][ $product['channel'] ] ?? [];

			// Channel update: prerelease installed and the channel's version differs.
			// Uses !== instead of version_compare because ANY version mismatch is a valid update:
			// - Cross-channel switch (beta.2 installed, switched to alpha channel serving alpha.1).
			// - Channel rollback (server reverts beta.3 to beta.2 due to a bad release).
			// Both require the user to install the channel's version regardless of direction.
			if ( ! empty( $channel_data['version'] ) && $product['installed_version'] !== $channel_data['version'] ) {
				$product_paths[] = $product['path'];
			}
		}

		return array_values( array_unique( $product_paths ) );
	}

	/**
	 * Optionally updates the Manage Your Kit submenu badge count if any of the products have newer versions available.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function update_manage_your_kit_submenu_badge_count() {
		if ( ! AdminMenu::should_initialize() ) {
			return;
		}

		if ( ! Framework::get_instance()->current_user_can( 'install_products' ) ) {
			return;
		}

		try {
			$update_paths  = $this->get_product_paths_with_available_update();
			$products_data = $this->get_products_data();
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->warning( 'Unable to get products when adding a badge count for products with updates.' );

			return;
		}

		$is_network_admin = CoreHelpers::is_network_admin();
		$update_count     = 0;

		// The badge is a summons: count only updates the viewer can install, for products running in this
		// context. Active unlicensed products are counted by the unlicensed badge instead (the sets stay disjoint).
		foreach ( $products_data as $product ) {
			if ( empty( $product['path'] ) || ! in_array( $product['path'], $update_paths, true ) ) {
				continue;
			}

			$active_in_context = $is_network_admin
				? ! empty( $product['network_activated'] )
				: ( ! empty( $product['active'] ) && empty( $product['network_activated'] ) );

			if ( ! $active_in_context || ! $this->is_product_update_authorized( $product ) ) {
				continue;
			}

			++$update_count;
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
	 * Returns product data schema used in the UI and elsewhere.
	 *
	 * @since 1.2.0
	 * @since TBD Added the product_notices schema field.
	 *
	 * @return array
	 */
	public function get_product_schema() {
		return [
			// phpcs:disable Squiz.PHP.CommentedOutCode.Found
			// EDD API properties.
			'id'                   => null,        // Integer. Product ID: $product['info']['id'].
			'slug'                 => '',          // String. Product slug: $product['info']['slug'].
			'category_name'        => '',          // String. Product category name: $product['info']['category_name'].
			'category_slug'        => '',          // String. Product category slug: $product['info']['category_slug'].
			'category_order'       => '',          // String. Product category slug: $product['info']['category_order'].
			'text_domain'          => '',          // String. Product text domain: $product['info']['text_domain'].
			'text_domain_legacy'   => '',          // String. Product legacy text domain(s) separated by a pipe: $product['info']['text_domain_legacy'].
			'text_domains'         => [],          // Array. Combined text domains (current + legacy) for lookup/matching.
			'has_admin_menu'       => false,       // Boolean. Whether the product has an admin menu: $product['info']['has_admin_menu'].
			'hidden'               => false,       // Boolean. Whether the product should be hidden from the UI: $product['info']['hidden'].
			'free'                 => false,       // Boolean. Whether the product is free: $product['info']['free'].
			'third_party'          => false,       // Boolean. Whether is not a GravityKit product: $product['info']['third_party'].
			'server_version'       => '',          // String. Latest available product version (flattened from active channel at normalization time): $product['licensing']['version'].
			'coming_soon'          => false,       // Boolean. Whether the product is coming soon: $product['info']['coming_soon'].
			'name'                 => '',          // String. Product name: $product['info']['title'].
			'excerpt'              => '',          // String. Product excerpt: $product['info']['excerpt'].
			'buy_link'             => '',          // String. Product buy link: $product['info']['buy_url'].
			'link'                 => '',          // String. Product information link: $product['info']['link'].
			'download_link'        => '',          // String. Product download link (for free product): $product['info']['download_link'].
			'icon'                 => '',          // String. Product icon: $product['info']['icon'].
			'icons'                => [            // Array. Product icons (JSON-encoded) that are displayed in the Plugins page when showing the changelog: $product['readme']['icons'].
				'1x' => '',
				'2x' => '',
			],
			'banners'              => [            // Array. Product banners (JSON-encoded) that are displayed in the Plugins page when showing the changelog: $product['readme']['banners'].
				'low'  => '',
				'high' => '',
			],
			'sections'             => [            // Array. Product changelog and description (JSON-encoded) hat are displayed in the Plugins page when showing the changelog: $product['readme']['sections'].
				'description' => '',
				'changelog'   => '',
			],
			'modified_date'        => '',          // String. Product modified date: $product['info']['modified_date'].
			'docs'                 => '',          // String. Product docs link: $product['info']['docs_url'].
			'dependencies'         => [            // Array. Product dependencies: $product['dependencies'].
				[
					'0.0.1' => [
						'system' => [],            // array{'PHP': array{'name': string, 'version': string}, 'WordPress': array{'name': string, 'version': string}}.
						'plugin' => [],            // array{'text_domain': array{'name': string, 'text_domain': string, 'author': string, 'version': string}}.
					],
				],
			],
			// Custom properties.
			'licenses'             => [],          // Array. License keys associated with the product.
			'active'               => false,       // Boolean. Whether the product is active.
			'installed'            => false,       // Boolean. Whether the product is installed.
			'installed_version'    => '',          // String. Installed product version.
			'custom_build'         => false,       // Boolean. Whether the installed version is a custom/dev build (hash or custom-labelled suffix, not a recognised pre-release).
			'update_available'     => false,       // Boolean. Whether an update is available for the product.
			'path'                 => '',          // String. Product path.
			'plugin_file'          => '',          // String. Product plugin file.
			'network_activated'    => false,       // Boolean. Whether the product is network activated.
			'settings'             => '',          // String. Product settings URL.
			'has_git_folder'       => false,       // Boolean. Whether the product is installed from a Git repo.
			'checked_dependencies' => [],          // Array. Version-specific product dependencies check results. See ProductManager::get_products_data() for structure.
			'required_by'          => [],          // Array. Products that depend on this product. See ProductDependencyChecker::is_a_dependency_of_any_product() for structure.
			'history'              => [],          // Array. Product history. See ProductHistoryTracker class for structure.
			'update_notices'       => [],          // Array. Version-keyed update notices. Each: ['title' => '', 'message' => ''].
			'product_notices'      => [],          // Array. Server-driven notices keyed by notice ID.
			'channel'              => false,       // String|false. User's active channel choice ('beta', 'alpha', etc., or false for stable).
			'channels'             => [],          // Array. Available channels keyed by name. Each channel: ['version' => '', 'download' => '', 'changelog' => '', 'opt_in_notice' => null, 'opt_out_notice' => null, 'dependencies' => null, 'link' => '', 'docs' => ''].
			'signature'            => '',          // String. Hex-encoded Ed25519 signature for the stable release ZIP.
			'signing_key_id'       => '',          // String. Key ID used to create the signature (e.g., 'gk-sign-v1').
			'sha256'               => '',          // String. Hex-encoded SHA-256 hash of the stable release ZIP.
			'filename'             => '',          // String. Build filename used when signing (e.g., 'gravityview-2.30.0-abc1234.zip').
		]; // phpcs:enable Squiz.PHP.CommentedOutCode.Found
	}

	/**
	 * Normalizes product data by merging it with the product schema.
	 *
	 * @since 1.2.0
	 * @since TBD Preserve dynamic product_notices definitions.
	 *
	 * @param array $product Product data.
	 *
	 * @return array
	 */
	public function normalize_product_data( $product ) {
		$schema   = Arr::dot( $this->get_product_schema() );
		$_product = Arr::dot( $product );

		$matched_keys = array_intersect_key( $_product, $schema );

		$normalized_data = array_merge( $schema, $matched_keys );
		$normalized_data = Arr::undot( $normalized_data );

		// Arrays with dynamic elements can't be easily normalized due to the strict comparison of the above method, so they have to be set manually.
		$normalized_data['dependencies']         = $product['dependencies'] ?? $this->get_product_schema()['dependencies'];
		$normalized_data['checked_dependencies'] = $product['checked_dependencies'] ?? $this->get_product_schema()['checked_dependencies'];
		$normalized_data['required_by']          = $product['required_by'] ?? $this->get_product_schema()['required_by'];
		$normalized_data['licenses']             = $product['licenses'] ?? $this->get_product_schema()['licenses'];
		$normalized_data['channels']             = $product['channels'] ?? $this->get_product_schema()['channels'];
		$normalized_data['update_notices']       = $product['update_notices'] ?? $this->get_product_schema()['update_notices'];
		$normalized_data['product_notices']      = $product['product_notices'] ?? $this->get_product_schema()['product_notices'];

		// Combine current and legacy text domains to match products that may have changed their text domain.
		$normalized_data['text_domains'] = array_values(
			array_unique(
				array_filter(
					array_merge(
						[ $normalized_data['text_domain'] ],
						array_filter( explode( '|', $normalized_data['text_domain_legacy'] ?? '' ) )
					)
				)
			)
		);

		return $normalized_data;
	}

	/**
	 * Syncs server notices when a complete product catalog changes.
	 *
	 * @since TBD
	 *
	 * @param array $products Complete normalized product catalog.
	 *
	 * @return void
	 */
	private function sync_catalog_server_notices( array $products ): void {
		$notice_products = [];

		foreach ( $products as $product ) {
			if ( ! is_array( $product ) ) {
				continue;
			}

			$text_domain = $product['text_domain'] ?? '';

			if ( ! is_scalar( $text_domain ) || '' === (string) $text_domain ) {
				continue;
			}

			$text_domain = (string) $text_domain;

			$notice_products[ $text_domain ] = [
				'id'                 => is_numeric( $product['id'] ?? null ) ? (int) $product['id'] : 0,
				'text_domain'        => $text_domain,
				'text_domains'       => is_array( $product['text_domains'] ?? null ) ? $product['text_domains'] : [],
				'text_domain_legacy' => is_scalar( $product['text_domain_legacy'] ?? null ) ? (string) $product['text_domain_legacy'] : '',
				'product_notices'    => is_array( $product['product_notices'] ?? null ) ? $product['product_notices'] : [],
			];
		}

		if ( empty( $notice_products ) ) {
			return;
		}

		ksort( $notice_products, SORT_STRING );

		$payload_hash = md5( wp_json_encode( $notice_products ) ?: '' );
		$blog_id      = is_multisite() ? get_current_blog_id() : 0;

		if ( ( $this->catalog_notice_sync_hashes[ $blog_id ] ?? '' ) === $payload_hash ) {
			return;
		}

		if ( get_transient( self::CATALOG_NOTICES_HASH_CACHE_ID ) === $payload_hash ) {
			$this->catalog_notice_sync_hashes[ $blog_id ] = $payload_hash;

			return;
		}

		if ( ! ServerNoticeHandler::sync( $notice_products, Core::notices(), ServerNoticeHandler::SOURCE_CATALOG ) ) {
			// A held source/write lock leaves the persisted hash unchanged, so the next request retries.
			return;
		}

		$this->catalog_notice_sync_hashes[ $blog_id ] = $payload_hash;
		set_transient( self::CATALOG_NOTICES_HASH_CACHE_ID, $payload_hash, self::PRODUCTS_DATA_CACHE_EXPIRATION );
	}

	/**
	 * Removes older products data from the database. This method is called when activating a product.
	 *
	 * @interal TODO: Move this to a separate class that will handle cleanup tasks.
	 *
	 * @since 1.2.11
	 *
	 * @return void
	 */
	public function cleanup_products_data() {
		global $wpdb;

		$products_data_cache_key       = self::PRODUCTS_DATA_CACHE_ID;
		$older_products_data_cache_key = substr( $products_data_cache_key, 0, strrpos( $products_data_cache_key, '/' ) ?: strlen( $products_data_cache_key ) );

		$wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_name != %s",
                $older_products_data_cache_key . '%',
                $products_data_cache_key
            )
        );
	}
}
