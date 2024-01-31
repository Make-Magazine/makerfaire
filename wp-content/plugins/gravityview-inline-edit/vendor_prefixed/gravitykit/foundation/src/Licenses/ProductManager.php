<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Licenses;

use Exception;
use GravityKit\GravityEdit\Foundation\Core;
use GravityKit\GravityEdit\Foundation\Helpers\Arr;
use GravityKit\GravityEdit\Foundation\Logger\Framework as LoggerFramework;
use GravityKit\GravityEdit\Foundation\Encryption\Encryption;
use GravityKit\GravityEdit\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityEdit\Foundation\Licenses\WP\WPUpgraderSkin;
use Plugin_Upgrader;

class ProductManager {
	const EDD_PRODUCTS_API_ENDPOINT = 'https://www.gravitykit.com/edd-api/products/';

	const EDD_PRODUCTS_API_VERSION = 3;

	const EDD_PRODUCTS_API_KEY = 'e4c7321c4dcf342c9cb078e27bf4ba97'; // Public key.

	const EDD_PRODUCTS_API_TOKEN = 'e031fd350b03bc223b10f04d8b5dde42'; // Public token.

	const PRODUCTS_DATA_CACHE_ID = Framework::ID . '/products/' . Core::VERSION;

	const PRODUCTS_DATA_CACHE_EXPIRATION = 86400; // 24 hours in seconds.

	/**
	 * @since 1.0.0
	 *
	 * @var ProductManager Class instance.
	 */
	private static $_instance;

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

		add_action( 'gk/foundation/ajax/after', [ $this, 'on_ajax_completion' ], 10, 3 );

		$this->update_manage_your_kit_submenu_badge_count();

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
		return array_merge( $routes, [
			'install_product'    => [ $this, 'ajax_install_product' ],
			'update_product'     => [ $this, 'ajax_update_product' ],
			'delete_product'     => [ $this, 'ajax_delete_product' ],
			'activate_product'   => [ $this, 'ajax_activate_product' ],
			'deactivate_product' => [ $this, 'ajax_deactivate_product' ],
			'get_products'       => [ $this, 'ajax_get_products_data' ],
		] );
	}

	/**
	 * Executes various tasks upon the completion of an Ajax request.
	 *
	 * @since 1.2.0
	 *
	 * @param string $router  Ajax router that handled the request.
	 * @param string $route   Ajax route that was requested.
	 * @param array  $payload Ajax request payload.
	 *
	 * @return void
	 */
	public function on_ajax_completion( $router, $route, array $payload ): void {
		if ( Framework::AJAX_ROUTER !== $router ) {
			return;
		}

		if ( in_array( $route, [ 'install_product', 'activate_product', 'update_product' ] ) && isset( $payload['pause_after_completion'] ) ) {
			sleep( (int) $payload['pause_after_completion'] );
		}
	}

	/**
	 * Ajax request wrapper for the install_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload
	 *
	 * @throws Exception
	 *
	 * @return array{products: array, activation_error: null|string}
	 */
	public function ajax_install_product( array $payload ): array {
		$payload = wp_parse_args( $payload, [
			'text_domain' => null,
			'activate'    => false,
		] );

		if ( ! Framework::get_instance()->current_user_can( 'install_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-gravityedit' ) );
		}

		$product = Arr::get( $this->get_products_data(), $payload['text_domain'] );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		$this->install_product( $product );

		$product = Arr::get( $this->get_products_data( [ 'skip_request_cache' => true ] ), $product['text_domain'] );

		$activation_error = null;

		$backend_foundation_version = Core::VERSION;

		if ( ! $product['active'] && $payload['activate'] ) {
			try {
				$this->activate_product( $product );

				// Check if the installed product comes with a newer version of the Foundation, which will be loaded if another Ajax request is made.
				$product_foundation_version = Core::get_instance()->get_plugin_foundation_version( $product['plugin_file'] );

				$backend_foundation_version = version_compare(
					Core::VERSION,
					$product_foundation_version ?? 0,
					'<'
				) ? $product_foundation_version : Core::VERSION;
			} catch ( Exception $e ) {
				$activation_error = $e->getMessage();
			}
		}

		return [
			'products'                 => $this->ajax_get_products_data(),
			'activation_error'         => $activation_error,
			'backendFoundationVersion' => $backend_foundation_version,
		];
	}

	/**
	 * Installs a product.
	 *
	 * @since 1.0.0
	 *
	 * @param array $product
	 * @param bool  $activate
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function install_product( array $product ) {
		if ( ! file_exists( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' ) ) {
			throw new Exception( esc_html__( 'Unable to load core WordPress files required to install the product.', 'gk-gravityedit' ) );
		}

		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		$product_id            = $product['id'];
		$product_download_link = $product['download_link'];

		$license_manager = LicenseManager::get_instance();

		if ( ! $product_download_link ) {
			$licenses_data = $license_manager->get_licenses_data();

			foreach ( $licenses_data as $key => $license_data ) {
				if ( $license_manager->is_expired_license( $license_data['expiry'] ) || empty( $license_data['products'] ) || ! isset( $license_data['products'][ $product_id ] ) ) {
					continue;
				}

				try {
					$license = $license_manager->check_license( $key );
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
			throw new Exception( esc_html__( 'Unable to locate product download link.', 'gk-gravityedit' ) );
		}

		$installer = new Plugin_Upgrader( new WPUpgraderSkin() );

		try {
			$installer->install( $product_download_link, [ 'overwrite_package' => true ] );
		} catch ( Exception $e ) {
			$error = join( ' ', [
				esc_html__( 'Installation failed.', 'gk-gravityedit' ),
				$e->getMessage()
			] );

			throw new Exception( $error );
		}

		if ( ! $installer->result ) {
			throw new Exception( esc_html__( 'Installation failed.', 'gk-gravityedit' ) );
		}
	}

	/**
	 * Ajax request wrapper for the update_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload
	 *
	 * @throws Exception
	 *
	 * @return array{products: array, activation_error: null|string}
	 */
	public function ajax_update_product( array $payload ): array {
		$payload = wp_parse_args( $payload, [
			'text_domain' => null,
		] );

		if ( ! Framework::get_instance()->current_user_can( 'update_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-gravityedit' ) );
		}

		$product = Arr::get( $this->get_products_data(), $payload['text_domain'] );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		$this->update_product( $product );

		$backend_foundation_version = Core::VERSION;

		$activation_error = null;

		try {
			$this->activate_product( $product );

			// Check if the updated product comes with a newer version of the Foundation, which will be loaded if another Ajax request is made.
			$product_foundation_version = Core::get_instance()->get_plugin_foundation_version( $product['plugin_file'], true );

			$backend_foundation_version = version_compare(
				Core::VERSION,
				$product_foundation_version,
				'<'
			) ? $product_foundation_version : Core::VERSION;
		} catch ( Exception $e ) {
			$activation_error = $e->getMessage();
		}

		return [
			'products'                 => $this->ajax_get_products_data(),
			'activation_error'         => $activation_error,
			'backendFoundationVersion' => $backend_foundation_version,
		];
	}

	/**
	 * Updates a product.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Made the $product parameter an array of product data.
	 *
	 * @param array $product
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function update_product( array $product ) {
		if ( ! file_exists( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' ) ) {
			throw new Exception( esc_html__( 'Unable to load core WordPress files required to install the product.', 'gk-gravityedit' ) );
		}

		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		// This is an edge case when for some reason the update_plugins transient is not set or the product is not marked as needing an update.
		$update_plugins_transient_filter = function () {
			return EDD::get_instance()->check_for_product_updates( new \stdClass() );
		};

		// Tampering with the user-agent header (e.g., done by the WordPress Classifieds Plugin) breaks the update process.
		$lock_user_agent_header = function ( $args, $url ) {
			if ( strpos( $url, 'gravitykit.com' ) !== false ) {
				$args['user-agent'] = 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url();
			}

			return $args;
		};

		$updater = new Plugin_Upgrader( new WPUpgraderSkin() );

		try {
			add_filter( 'pre_site_transient_update_plugins', $update_plugins_transient_filter );
			add_filter( 'http_request_args', $lock_user_agent_header, 100, 2 );

			$updater->upgrade( $product['path'] );

			remove_filter( 'pre_site_transient_update_plugins', $update_plugins_transient_filter );
			remove_filter( 'http_request_args', $lock_user_agent_header, 100 );
		} catch ( Exception $e ) {
			$error = join( ' ', [
				esc_html__( 'Update failed.', 'gk-gravityedit' ),
				$updater->strings[ $e->getMessage() ] ?? $e->getMessage(),
			] );

			throw new Exception( $error );
		}

		if ( ! $updater->result ) {
			throw new Exception( esc_html__( 'Installation failed.', 'gk-gravityedit' ) );
		}
	}

	/**
	 * Ajax request wrapper for the delete_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload
	 *
	 * @throws Exception
	 *
	 * @return array{products: array}
	 */
	public function ajax_delete_product( array $payload ): array {
		$payload = wp_parse_args( $payload, [
			'text_domain' => null,
		] );

		if ( ! Framework::get_instance()->current_user_can( 'delete_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-gravityedit' ) );
		}

		$product = Arr::get( $this->get_products_data(), $payload['text_domain'] );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		if ( $product['active'] ) {
			throw new Exception(
				esc_html__( "Product must be deactivation before it can be deleted.", 'gk-gravityedit' )
			);
		}

		$this->delete_product( $product );

		return [
			'products' => $this->ajax_get_products_data()
		];
	}

	/**
	 * Deletes a product.
	 *
	 * @since 1.2.0
	 *
	 * @param array $product
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
			throw new Exception( esc_html__( 'Could not delete the product due to missing filesystem credentials.', 'gk-gravityedit' ) );
		}
	}

	/**
	 * Ajax request wrapper for the activate_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload
	 *
	 * @throws Exception
	 *
	 * @return array{products: array}
	 */
	public function ajax_activate_product( array $payload ): array {
		$payload = wp_parse_args( $payload, [
			'text_domain' => null,
		] );

		if ( ! Framework::get_instance()->current_user_can( 'activate_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-gravityedit' ) );
		}

		$product = Arr::get( $this->get_products_data(), $payload['text_domain'] ) ?? CoreHelpers::get_installed_plugin_by_text_domain( $payload['text_domain'] );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		$this->activate_product( $product );

		// Check if the activated product comes with a newer version of the Foundation, which will be loaded if another Ajax request is made.
		$product_foundation_version = Core::get_instance()->get_plugin_foundation_version( $product['plugin_file'] );

		$backend_foundation_version = version_compare(
			Core::VERSION,
			$product_foundation_version ?? 0,
			'<'
		) ? $product_foundation_version : Core::VERSION;

		return [
			'products'                 => $this->ajax_get_products_data(),
			'backendFoundationVersion' => $backend_foundation_version,
		];
	}

	/**
	 * Activates a product.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Made the $product parameter an array of product data.
	 *
	 * @param array $product
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function activate_product( array $product ) {
		if ( $this->is_product_active_in_current_context( $product['path'] ) ) {
			throw new Exception( esc_html__( 'Product is already active.', 'gk-gravityedit' ) );
		}

		$result = activate_plugin( $product['path'], false, CoreHelpers::is_network_admin() );

		if ( is_wp_error( $result ) ) {
			throw new Exception(
				strtr(
					esc_html_x( 'Could not activate the product. [error]', 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
					[ '[error]' => $result->get_error_message() ]
				)
			);
		}
	}

	/**
	 * Ajax request wrapper for the deactivate_product() method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload
	 *
	 * @throws Exception
	 *
	 * @return array{products: array}
	 */
	public function ajax_deactivate_product( array $payload ): array {
		$payload = wp_parse_args( $payload, [
			'text_domain' => null,
		] );

		if ( ! Framework::get_instance()->current_user_can( 'deactivate_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-gravityedit' ) );
		}

		$product = Arr::get( $this->get_products_data(), $payload['text_domain'] );

		if ( ! $product ) {
			throw new Exception(
				strtr(
					esc_html_x( "Product with '[text_domain]' text domain not found.", 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
					[ '[text_domain]' => $payload['text_domain'] ]
				)
			);
		}

		$this->deactivate_product( $product );

		return [
			'products'                 => $this->ajax_get_products_data(),
			'backendFoundationVersion' => Core::get_instance()->get_latest_foundation_version_from_registered_plugins( $product['text_domain'] ),
		];
	}

	/**
	 * Deactivates a product.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Made the $product parameter an array of product data.
	 *
	 * @param array $product
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function deactivate_product( array $product ) {
		if ( ! $this->is_product_active_in_current_context( $product['path'] ) ) {
			throw new Exception( esc_html__( 'Product in not active.', 'gk-gravityedit' ) );
		}

		deactivate_plugins( $product['path'], false, CoreHelpers::is_network_admin() );

		if ( $this->is_product_active_in_current_context( $product['path'] ) ) {
			throw new Exception( esc_html__( 'Could not deactivate the product.', 'gk-gravityedit' ) );
		}
	}

	/**
	 * Returns a list of all GravityKit products from the API grouped by category (e.g., plugins, extensions, etc.).
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Result is no longer grouped by category and is now keyed by product ID.
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function get_remote_products() {
		try {
			$response = Helpers::query_api(
				self::EDD_PRODUCTS_API_ENDPOINT,
				[
					'key'         => self::EDD_PRODUCTS_API_KEY,
					'token'       => self::EDD_PRODUCTS_API_TOKEN,
					'api_version' => self::EDD_PRODUCTS_API_VERSION,
					'bust_cache'  => time(),
				]
			);
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}

		$products = Arr::get( $response, 'products', [] );

		if ( empty( $response ) || empty( $products ) ) {
			throw new Exception( esc_html__( 'Invalid product information received from the API.', 'gk-gravityedit' ) );
		}

		$normalized_products = [];

		foreach ( $products as $product ) {
			$product_id = Arr::get( $product, 'info.id' );
			$icons      = unserialize( Arr::get( $product, 'readme.icons', '' ) );
			$banners    = unserialize( Arr::get( $product, 'readme.banners', '' ) );
			$sections   = unserialize( Arr::get( $product, 'readme.sections', '' ) );

			if ( ! Arr::get( $product, 'info.category_slug' ) || 'bundles' === Arr::get( $product, 'info.category_slug' ) ) {
				continue;
			}

			$product_schema = $this->get_product_schema();

			$normalized_products[ $product_id ] = $this->normalize_product_data( [
				'id'                 => $product_id,
				'slug'               => Arr::get( $product, 'info.slug', $product_schema['slug'] ),
				'category_name'      => Arr::get( $product, 'info.category_name', $product_schema['category_name'] ),
				'category_slug'      => Arr::get( $product, 'info.category_slug', $product_schema['category_slug'] ),
				'category_order'     => Arr::get( $product, 'info.category_order', $product_schema['category_order'] ),
				'text_domain'        => Arr::get( $product, 'info.text_domain', $product_schema['text_domain'] ),
				'text_domain_legacy' => Arr::get( $product, 'info.text_domain_legacy', $product_schema['text_domain_legacy'] ),
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
					'1x' => esc_url_raw( $product['icons']['1x'] ?? $product_schema['icons']['1x'] ),
					'2x' => esc_url_raw( $product['icons']['2x'] ?? $product_schema['icons']['2x'] ),
				],
				'banners'            => [
					'low'  => esc_url_raw( $product['banners']['low'] ?? $product_schema['banners']['low'] ),
					'high' => esc_url_raw( $product['banners']['high'] ?? $product_schema['banners']['low'] ),
				],
				'sections'           => [
					'description' => Arr::get( $sections, 'description', $product_schema['sections']['description'] ),
					'changelog'   => $this->truncate_product_changelog(
						Arr::get( $sections, 'changelog', $product_schema['sections']['changelog'] ),
						esc_url_raw( $product['info']['link'] ?? $product_schema['link'] )
					),
				],
				'server_version'     => Arr::get( $product, 'licensing.version', $product_schema['server_version'] ),
				'modified_date'      => Arr::get( $product, 'info.modified_date', $product_schema['modified_date'] ),
				'docs'               => esc_url_raw( $product['info']['docs_url'] ?? $product_schema['docs'] ),
				'dependencies'       => Arr::get( $product, 'dependencies', $product_schema['dependencies'] ),
			] );
		}

		return $normalized_products;
	}

	/**
	 * Truncates the product changelog to display only the specified number of most recent entries.
	 *
	 * @since 1.0.11
	 *
	 * @param string $changelog
	 * @param string $product_url
	 * @param int    $max_changelog_entries  (optional) Number of entries to display. Default: 3.
	 * @param bool   $link_to_full_changelog (optional) Display a link to the full changelog on GravityKit's website. Default: true.
	 *
	 * @return string
	 */
	public function truncate_product_changelog( $changelog, $product_url, $max_changelog_entries = 3, $link_to_full_changelog = true ) {
		$changelog_pattern = '~(<p><strong>\d+.*?on.*?(?=<p><strong>\d+.*?on|$))~s';

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
				esc_html__( 'View full changelog', 'gk-gravityedit' )
			);
		}

		foreach ( $truncated_changelog as $changelog_entry ) {
			$modified_changelog_entry = $changelog_entry;
			$modified_changelog_entry = preg_replace( '~<p><strong>(\d+.*?on.*?)</strong></p>~s', '<h4>$1</h4>', $modified_changelog_entry, 1 );
			$modified_changelog_entry = preg_replace( '~<a~s', '<a class="gk-link"', $modified_changelog_entry );
			$changelog                .= $modified_changelog_entry;
		}

		return $changelog;
	}

	/**
	 * Ajax request wrapper for the {@see get_products_data()} method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function ajax_get_products_data( array $payload = [] ) {
		if ( ! Framework::get_instance()->current_user_can( 'view_products' ) ) {
			throw new Exception( esc_html__( 'You do not have a permission to perform this action.', 'gk-gravityedit' ) );
		}

		$payload = wp_parse_args( $payload, [
			'skip_remote_cache'  => false,
			'skip_request_cache' => true,
		] );

		$products = $this->get_products_data( $payload );

		$excluded_properties = [
			'path',
			'plugin_file',
			'dependencies',
			'text_domain_legacy',
			'modified_date',
			'icons',
			'banners'
		];

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

			// Encrypt license keys.
			$product['licenses'] = array_map( function ( $key ) {
				return Encryption::get_instance()->encrypt( $key, false, Core::get_request_unique_string() );
			}, $product['licenses'] );
		}

		return array_values( $products );
	}

	/**
	 * Returns a list of all GravityKit products with associated installation/activation/licensing data.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Result is now keyed by product's text domain.
	 *
	 * @param array $args (optional) Additional arguments. Default: ['skip_cache_remote' => false, 'skip_request_cache' => false, 'key_by' => 'text_domain'].
	 *
	 * @return array
	 */
	public function get_products_data( array $args = [] ) {
		static $_cached_products_data;

		$args = wp_parse_args( $args, [
			'skip_remote_cache'  => false, // If true, products will be fetched from the API even if they are cached locally.
			'skip_request_cache' => false, // If true, products data will be updated with the most recent changes during the same request.
			'key_by'             => 'text_domain',
		] );

		if ( ! $args['skip_remote_cache'] && ! $args['skip_request_cache'] && $_cached_products_data ) {
			return 'text_domain' === $args['key_by'] ? $_cached_products_data : $this->key_products_by_property( $_cached_products_data, $args['key_by'] );
		}

		$products = ! $args['skip_remote_cache'] ? ( get_site_transient( self::PRODUCTS_DATA_CACHE_ID ) ?: null ) : null;

		if ( $products && ! is_array( $products ) ) { // Backward compatibility for serialized data (used in earlier Foundation versions).) {
			$products = json_decode( $products, true );
			$products = is_array( $products ) ? $products : null;
		}

		if ( is_null( $products ) ) {
			$products = [];

			try {
				$products = $this->get_remote_products();
			} catch ( Exception $e ) {
				LoggerFramework::get_instance()->error( 'Unable to get products from the API. ' . $e->getMessage() );
			}

			set_site_transient( self::PRODUCTS_DATA_CACHE_ID, json_encode( $products ), self::PRODUCTS_DATA_CACHE_EXPIRATION );
		}

		if ( empty( $products ) ) {
			$_cached_products_data = [];

			return $_cached_products_data;
		}

		$product_license_map = LicenseManager::get_instance()->get_product_license_map();

		$normalized_products = [];

		$products_history = ProductHistoryManager::get_instance()->get_products_history();

		// Supplement API response with additional data that can change between or during requests (e.g., activation status, etc.).
		foreach ( $products as $product ) {
			$installed_product = CoreHelpers::get_installed_plugin_by_text_domain( implode( '|', [ $product['text_domain'], $product['text_domain_legacy'] ] ) );

			/**
			 * @filter `gk/foundation/settings/{$product_slug}/settings-url` Sets link to the product settings page.
			 *
			 * @since  1.0.3
			 *
			 * @param string $settings_url URL to the product settings page.
			 */
			$product_settings_url = apply_filters( "gk/foundation/settings/{$product['slug']}/settings-url", '' );

			$normalized_product = array_merge( $product, [
				'id'                => $product['id'],
				'text_domain'       => $installed_product['text_domain'] ?? $product['text_domain'],
				'installed'         => ! is_null( $installed_product ),
				'installed_version' => $installed_product['version'] ?? $product['installed_version'],
				'active'            => $installed_product['active'] ?? $product['active'],
				'update_available'  => $installed_product && version_compare( $installed_product['version'], $product['server_version'], '<' ),
				'path'              => $installed_product['path'] ?? $product['path'],
				'plugin_file'       => $installed_product['plugin_file'] ?? $product['plugin_file'],
				'network_activated' => $installed_product['network_activated'] ?? $product['network_activated'],
				'licenses'          => $product_license_map[ $product['id'] ] ?? $product['licenses'],
				'settings'          => esc_url_raw( $product_settings_url ),
				'has_git_folder'    => $installed_product && file_exists( dirname( $installed_product['plugin_file'] ) . '/.git' ),
				'history'           => $products_history[ $product['text_domain'] ] ?? [],
			] );

			$normalized_products[ $normalized_product['text_domain'] ] = $normalized_product;
		}

		/**
		 * @filter `gk/foundation/products/data` Modifies products data object.
		 *
		 * @since  1.0.3
		 *
		 * @param array $normalized_products Products data.
		 * @param array $args                Additional arguments passed to the get_products_data() method.
		 */
		$normalized_products = apply_filters( 'gk/foundation/products/data', $normalized_products, $args );

		$product_dependency_checker = new ProductDependencyChecker( $normalized_products );

		foreach ( $normalized_products as &$product ) {
			$product['required_by'] = $product_dependency_checker->is_a_dependency_of_any_product( $product['text_domain'], true ) ?: [];

			$product_versions_to_check = [];

			// We need to check both installed and server versions for dependencies, for these can be different (e.g, an updated version may have new dependencies).
			if ( $product['installed'] && $product['installed_version'] ) {
				$product_versions_to_check[] = $product['installed_version'];
			}

			if ( ( ! $product['installed'] || $product['update_available'] ) && $product['server_version'] ) {
				$product_versions_to_check[] = $product['server_version'];
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
					} catch ( Exception $e ) {
						// No need to do anything here since dependencies can't be satisfied.
					}
				}
			}
		}

		if ( ! $args['skip_request_cache'] ) {
			$_cached_products_data = $normalized_products;
		}

		return 'text_domain' === $args['key_by'] ? $normalized_products : $this->key_products_by_property( $normalized_products, $args['key_by'] );
	}

	/**
	 * Keys products object by the specified product property.
	 *
	 * @since 1.2.0
	 *
	 * @param array  $products
	 * @param string $key_by
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

		return $keyed_products;
	}

	/**
	 * Checks if plugin is activated in the current context (network or site).
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_product_active_in_current_context( $plugin_path ) {
		return CoreHelpers::is_network_admin() ? is_plugin_active_for_network( $plugin_path ) : is_plugin_active( $plugin_path );
	}

	/**
	 * Optionally updates the Manage Your Kit submenu badge count if any of the products have newer versions available.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function update_manage_your_kit_submenu_badge_count() {
		if ( ! Framework::get_instance()->current_user_can( 'install_products' ) ) {
			return;
		}
		try {
			$products_data = $this->get_products_data();
		} catch ( Exception $e ) {
			LoggerFramework::get_instance()->warning( 'Unable to get products when adding a badge count for products with updates.' );

			return;
		}

		$update_count = 0;

		foreach ( $products_data as $product ) {
			if ( $product['third_party'] || $product['hidden'] ) {
				continue;
			}

			if ( $product['update_available'] ) {
				$update_count++;
			}
		}

		if ( ! $update_count ) {
			return;
		}

		add_filter( 'gk/foundation/admin-menu/submenu/' . Framework::ID . '/counter', function ( $count ) use ( $update_count ) {
			return (int) $count + $update_count;
		} );
	}

	/**
	 * Returns product data schema used in the UI and elsewhere.
	 *
	 * @since 1.2.0
	 *
	 * @return array
	 */
	public function get_product_schema() {
		return [
			// EDD API properties.
			'id'                   => null,        // Integer. Product ID: $product['info']['id'].
			'slug'                 => '',          // String. Product slug: $product['info']['slug'].
			'category_name'        => '',          // String. Product category name: $product['info']['category_name'].
			'category_slug'        => '',          // String. Product category slug: $product['info']['category_slug'].
			'category_order'       => '',         // String. Product category slug: $product['info']['category_order'].
			'text_domain'          => '',          // String. Product text domain: $product['info']['text_domain'].
			'text_domain_legacy'   => '',          // String. Product legacy text domain(s) separated by a pipe: $product['info']['text_domain_legacy'].
			'hidden'               => false,       // Boolean. Whether the product should be hidden from the UI: $product['info']['hidden'].
			'free'                 => false,       // Boolean. Whether the product is free: $product['info']['free'].
			'third_party'          => false,       // Boolean. Whether is not a GravityKit product: $product['info']['third_party'].
			'server_version'       => '',          // String. Latest available product version: $product['licensing']['version'].
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
						'system' => [],            // array{'PHP': array{'name': string, 'version': string}, 'WordPress': array{'name': string, 'version': string}}
						'plugin' => [],            // array{'product_text_domain': array{'name': string, 'text_domain': string, 'version': string}}
					]
				]
			],
			// Custom properties.
			'licenses'             => [],          // Array. License keys associated with the product.
			'active'               => false,       // Boolean. Whether the product is active.
			'installed'            => false,       // Boolean. Whether the product is installed.
			'installed_version'    => '',          // String. Installed product version.
			'update_available'     => false,       // Boolean. Whether an update is available for the product.
			'path'                 => '',          // String. Product path.
			'plugin_file'          => '',          // String. Product plugin file.
			'network_activated'    => false,       // Boolean. Whether the product is network activated.
			'settings'             => '',          // String. Product settings URL.
			'has_git_folder'       => false,       // Boolean. Whether the product is installed from a Git repo.
			'checked_dependencies' => [],          // Array. Version-specific product dependencies check results. See ProductManager::get_products_data() for structure.
			'required_by'          => [],          // Array. Products that depend on this product. See ProductDependencyChecker::is_a_dependency_of_any_product() for structure.
			'history'              => [],          // Array. Product history. See ProductHistoryTracker class for structure.
		];
	}

	/**
	 * Normalizes product data by merging it with the product schema.
	 *
	 * @since 1.2.0
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

		return $normalized_data;
	}
}
