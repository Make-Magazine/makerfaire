<?php

namespace GravityKit\GravityView\Foundation\Licenses;

use GravityKit\GravityView\Foundation\Logger\Framework as LoggerFramework;
use GravityKit\GravityView\Foundation\Helpers\Arr;
use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use Exception;
use ReflectionClass;

class EDD {
	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var EDD|null
	 */
	private static $_instance = null;

	/**
	 * Whether product update data has already been checked in this request.
	 *
	 * @since 1.19.0
	 *
	 * @var bool
	 */
	private static $checked_product_updates = false;

	/**
	 * Returns class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return EDD
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
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_for_product_updates' ], 999 );
		add_filter( 'plugins_api', [ $this, 'display_product_information' ], 999, 3 );
		add_action( 'admin_init', [ $this, 'disable_legacy_edd_updater' ], 999 );
	}

	/**
	 * Disables EDD updater that's included with GravityKit products.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function disable_legacy_edd_updater() {
		global $wp_filter;

		$filters_to_remove = [ 'pre_set_site_transient_update_plugins', 'plugins_api', 'after_plugin_row', 'admin_init' ];

		$legacy_edd_check = function () {
			try {
				$reflection = new ReflectionClass( get_class( $this ) );

				if ( ! $reflection->hasProperty( 'api_url' ) ) {
					return false;
				}

				$api_url_property = $reflection->getProperty( 'api_url' );
			} catch ( Exception $e ) {
				return false;
			}

			// $this is not an instance of EDD, but a bound class (see $remove_filter below).
			// @phpstan-ignore-next-line
			$api_url = $api_url_property->isStatic() ? $this::$api_url : $this->api_url;

			return preg_match( '/gravity(view|kit)\.com?/', $api_url );
		};

		$remove_filter = function ( $filter ) use ( $wp_filter, $legacy_edd_check ) {
			if ( empty( $wp_filter[ $filter ]->callbacks ) ) {
				return;
			}

			foreach ( $wp_filter[ $filter ]->callbacks as &$callback ) {
				foreach ( $callback as $key => &$hook ) {
					if ( ! is_array( $hook['function'] ) || ! is_object( $hook['function'][0] ) ) {
						continue;
					}

					// EDD_SL_Plugin_Updater->api_url is a private property, so we need a way to access it.
					$is_legacy_edd = $legacy_edd_check->bindTo( $hook['function'][0], get_class( $hook['function'][0] ) );

					if ( ! $is_legacy_edd() ) {
						continue;
					}

					unset( $callback[ $key ] );
				}
			}
		};

		foreach ( array_keys( $wp_filter ) as $filter ) {
			foreach ( $filters_to_remove as $filter_to_remove ) {
				// Older EDD_SL_Plugin_Updater class uses 'after_plugin_row_{plugin_file}' filter, so we can't just check for 'after_plugin_row'.
				if ( strpos( (string) $filter, $filter_to_remove ) !== false ) {
					$remove_filter( $filter );
				}
			}
		}
	}

	/**
	 * Checks for product updates and modifies the 'update_plugins' transient.
	 *
	 * @since 1.0.0
	 * @since 1.30.0 Up-to-date products are added to the transient's `no_update` collection so WP shows the auto-update UI.
	 *
	 * @param object $transient_data Transient data.
	 * @param bool   $skip_cache     (optional) Whether to skip cache when getting products data. Default: false.
	 *
	 * @return object
	 */
	public function check_for_product_updates( $transient_data, $skip_cache = false ) {
		if ( ! is_object( $transient_data ) || empty( $transient_data->checked ) ) {
			return $transient_data;
		}

		$force_check = ! self::$checked_product_updates
			&& ! $skip_cache
			&& Arr::get( $_GET, 'force-check', false ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			&& is_admin()
			&& ! wp_doing_ajax()
			&& ! wp_doing_cron()
			&& current_user_can( 'update_plugins' );

		if ( $force_check ) {
			LicenseManager::get_instance()->recheck_all_licenses_without_update_plugins_refresh( true );

			$skip_cache = true;
		}

		try {
			$products_data = ProductManager::get_instance()->get_products_data(
				[
					'skip_request_cache' => true,
					'skip_remote_cache'  => $skip_cache,
				]
			);
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( "Can't get products data when checking for updated versions: " . $e->getMessage() );

			return $transient_data;
		}

		foreach ( $products_data as $product ) {
			// Only hidden third-party products (e.g. Gravity Forms, Gravity PDF) are excluded — those
			// are tracked solely for dependency management and updated by their own publisher. Non-hidden
			// third-party products are distributed and managed by GravityKit, so they DO receive updates.
			if ( ! $product['installed'] || ( $product['third_party'] && $product['hidden'] ) || empty( $product['path'] ) ) {
				continue;
			}

			// Supersession is handled by ProductManager::resolve_channel_supersession() during normalization.
			// By this point, product['channel'] is already cleared if supersession occurred.

			// Resolve the effective version from the active channel (defaults to 'stable').
			$active_key        = $product['channel'] ?: 'stable';
			$active_channel    = $product['channels'][ $active_key ] ?? [];
			$effective_version = $active_channel['version'] ?? $product['server_version'];

			$channel_names        = array_keys( $product['channels'] ?? [] );
			$installed_normalized = ChannelManager::strip_build_suffix( $product['installed_version'], $channel_names );

			$has_update = ! empty( $effective_version )
				&& CoreHelpers::version_compare( $installed_normalized, $effective_version, '<' );

			// Cross-channel switch: installed channel build doesn't match the active channel's version
			// (e.g. beta→alpha, or the next commit-hash build landing on the same channel).
			$is_channel_switch = $product['channel'] && $product['channel_tracked']
				&& ! empty( $effective_version )
				&& ! ChannelManager::is_same_channel_build( $product['installed_version'], $effective_version );

			// User switched to stable (manually or via supersession): force update to stable version.
			//
			// Deliberately NOT channel_tracked, and deliberately NOT is_channel_build(). This arm runs
			// for products with NO active channel, where `channels` is server-supplied data. A response
			// that echoes the installed version back as some channel's version would otherwise satisfy
			// is_channel_build(), mint gk_stable_recovery, and have the verifier waive rollback
			// protection for whatever `server_version` claims -- a downgrade with no local intent
			// behind it. Provenance for this arm must be locally recorded: a real pre-release suffix,
			// or channel_exit written by this site when its channel was withdrawn.
			$is_stable_switch = ! $product['channel']
				&& ( ChannelManager::is_prerelease_version( $product['installed_version'], $channel_names )
					|| ! empty( $product['channel_exit'] ) )
				&& $product['server_version']
				&& $product['installed_version'] !== $product['server_version'];

			// A product lives in exactly one of `response`/`no_update`; WP treats membership in either as
			// "supports updates" and hides the auto-update UI otherwise (see WP_Plugins_List_Table).
			if ( $has_update || $is_channel_switch || $is_stable_switch ) {
				$transient_data->response[ $product['path'] ] = $this->format_product_data( $product, (bool) $is_stable_switch, (bool) $is_channel_switch ); // @phpstan-ignore property.notFound (WP update transient object has a `response` property.)

				unset( $transient_data->no_update[ $product['path'] ] ); // @phpstan-ignore property.notFound
			} else {
				unset( $transient_data->response[ $product['path'] ] ); // @phpstan-ignore property.notFound

				$no_update_data = $this->format_product_data( $product );

				// WP only checks membership here; drop `sections` (changelog/description HTML) so the
				// stored transient isn't bloated by every installed product. w.org `no_update` entries
				// carry no `sections` either.
				unset( $no_update_data->sections );

				$transient_data->no_update[ $product['path'] ] = $no_update_data; // @phpstan-ignore property.notFound (WP update transient object has a `no_update` property.)
			}
		}

		self::$checked_product_updates = true;

		return $transient_data;
	}

	/**
	 * Returns a product object formatted according to what WP expects in order to display changelog/store plugin update data.
	 *
	 * @since 1.0.0
	 *
	 * @see   ProductManager::get_products_data()
	 * @see   plugins_api()
	 *
	 * @param array $product             Product data.
	 * @param bool  $is_stable_recovery  (optional) Whether this entry is a deliberate move back to
	 *                                   stable, which authorizes an apparent downgrade.
	 * @param bool  $is_channel_update   (optional) Whether this entry is an in-channel move, which may
	 *                                   legitimately go backward (a reverted build, a lower track).
	 *
	 * @return object
	 */
	public function format_product_data( $product, bool $is_stable_recovery = false, bool $is_channel_update = false ) {
		$licenses_data = LicenseManager::get_instance()->get_all_licenses_data();

		$download_link = self::pick_download_link( $product, $licenses_data );

		// Resolve version-specific fields from the active channel (defaults to 'stable').
		$active_key     = $product['channel'] ?: 'stable';
		$active_channel = $product['channels'][ $active_key ] ?? [];

		$version   = $active_channel['version'] ?? $product['server_version'];
		$changelog = $active_channel['changelog'] ?? $product['sections']['changelog'];
		$link      = $active_channel['link'] ?? $product['link'];

		$formatted_data = [
			'plugin'                 => $product['path'],
			'name'                   => $product['name'],
			'id'                     => $product['id'],
			'slug'                   => $product['slug'],
			'gk_product_text_domain' => $product['text_domain'],
			// Tells PackageVerifier this download is a deliberate return to stable, decided by this
			// class. Without it, rollback protection blocks the very update just offered, because an
			// automatic update sets no channel-switch flag.
			'gk_stable_recovery'     => $is_stable_recovery,
			// Set when this class decided an in-channel move is warranted; the verifier honours it
			// only after corroborating against the site's own stored channel preference.
			'gk_channel_update'      => $is_channel_update,
			'version'                => $version,
			'new_version'            => $version,
			'url'                    => $link,
			'homepage'               => $link,
			'icons'                  => [
				'1x' => $product['icons']['1x'],
				'2x' => $product['icons']['2x'],
			],
			'banners'                => [
				'low'  => $product['banners']['low'],
				'high' => $product['banners']['high'],
			],
			'sections'               => [
				'description' => $product['sections']['description'],
				'changelog'   => $changelog,
			],
			'requires'               => Arr::get( $product, 'system_requirements.wp.version' ),
			'tested'                 => Arr::get( $product, 'system_requirements.wp.tested' ),
			'requires_php'           => Arr::get( $product, 'system_requirements.php.version' ),
		];

		// Use channel-specific download URL.
		$channel_download = $active_channel['download'] ?? '';

		if ( $channel_download ) {
			$download_link = $channel_download;
		}

		if ( $download_link && ( $product['free'] || ! empty( $product['licenses'] ) ) ) {
			$formatted_data['package']       = $download_link;
			$formatted_data['download_link'] = $download_link;
		}

		return (object) $formatted_data;
	}

	/**
	 * Picks the best download URL for a product.
	 *
	 * Prefers license-scoped URLs from `/licenses/check` responses — those
	 * tokens carry an `lh` claim, so downloads through them land in the Store's
	 * `wp_gk_download_log` with customer attribution. Falls back to the
	 * catalog URL only when no license is in scope at all (the genuine
	 * first-time free-install case on a fresh WP).
	 *
	 * Lookup order:
	 * 1. The product's primary license (`$product['licenses'][0]`) — handles
	 *    paid products and free products that opted into license association.
	 * 2. Any other license the caller holds that exposes the product —
	 *    free products are served under every license in the Store's
	 *    `/licenses/check` response for exactly this purpose.
	 * 3. `$product['download_link']` (from the `/products` catalog).
	 *
	 * @since 1.16.1
	 *
	 * @param array $product       Normalized product record.
	 * @param array $licenses_data License map from `LicenseManager::get_licenses_data()`.
	 *
	 * @return string|null Download URL or null when nothing resolvable exists.
	 */
	public static function pick_download_link( array $product, array $licenses_data ) {
		$product_id = (int) ( $product['id'] ?? 0 );
		$primary    = Arr::get( $product, 'licenses.0' );

		if ( $primary ) {
			$scoped = Arr::get( $licenses_data, "{$primary}.products.{$product_id}.download" );

			if ( $scoped ) {
				return $scoped;
			}
		}

		foreach ( $licenses_data as $license_row ) {
			$candidate = Arr::get( $license_row, "products.{$product_id}.download" );

			if ( $candidate ) {
				return $candidate;
			}
		}

		if ( ! empty( $product['free'] ) && ! empty( $product['download_link'] ) ) {
			return $product['download_link'];
		}

		return null;
	}

	/**
	 * Returns product information for display on the Plugins page.
	 * This short-circuits the WordPress.org API request by returning product information from the EDD API that we store in the database.
	 *
	 * @since 1.0.0
	 *
	 * @param false|object|array $result Product information.
	 * @param string             $action Plugin Installation API action.
	 * @param object             $args   Request arguments.
	 *
	 * @return false|object|array
	 */
	public function display_product_information( $result, $action, $args ) {
		try {
			$products = ProductManager::get_instance()->get_products_data();
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->error( "Can't get products data when displaying the changelog: " . $e->getMessage() );

			return $result;
		}

		if ( 'plugin_information' !== $action || empty( $args->slug ) ) {
			return $result;
		}

		$product = Arr::first(
			$products,
			function ( $product ) use ( $args ) {
				return ! ( $product['third_party'] ?? '' ) && $product['slug'] === $args->slug;
			}
		);

		if ( ! $product ) {
			return $result;
		}

		return $this->format_product_data( $product );
	}
}
