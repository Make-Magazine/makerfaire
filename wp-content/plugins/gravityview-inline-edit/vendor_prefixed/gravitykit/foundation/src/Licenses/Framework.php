<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by __root__ on 02-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Licenses;

use Exception;
use GravityKit\GravityEdit\Foundation\Core as FoundationCore;
use GravityKit\GravityEdit\Foundation\WP\AdminMenu;
use GravityKit\GravityEdit\Foundation\Translations\Framework as TranslationsFramework;
use GravityKit\GravityEdit\Foundation\Logger\Framework as LoggerFramework;
use GravityKit\GravityEdit\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityEdit\Foundation\Licenses\WP\PluginsPage;
use GravityKit\GravityEdit\Foundation\Licenses\WP\UpdatesPage;

class Framework {
	const ID = 'gk_licenses';

	const AJAX_ROUTER = 'licenses';

	/**
	 * @since 1.0.0
	 *
	 * @var Framework Class instance.
	 */
	private static $_instance;

	/**
	 * @since 1.0.3
	 *
	 * @var LicenseManager Class instance.
	 */
	private $_license_manager;

	/**
	 * @since 1.0.3
	 *
	 * @var ProductManager Class instance.
	 */
	private $_product_manager;

	/**
	 * @since 1.0.0
	 *
	 * @var array User permissions to manage licenses/products.
	 */
	private $_permissions;

	private function __construct() {
		$permissions = [
			// Licenses
			'view_licenses'       =>
				( ! is_super_admin() && current_user_can( 'gk_foundation_view_licenses' ) ) ||
				( ! is_multisite() && current_user_can( 'manage_options' ) ) ||
				( is_multisite() && current_user_can( 'manage_network_options' ) && CoreHelpers::is_network_admin() ),
			'manage_licenses'     =>
				( ! is_super_admin() && current_user_can( 'gk_foundation_manage_licenses' ) ) ||
				( ! is_multisite() && current_user_can( 'manage_options' ) ) ||
				( is_multisite() && current_user_can( 'manage_network_options' ) && CoreHelpers::is_network_admin() ),
			// Products
			'view_products'       =>
				( ! is_super_admin() && current_user_can( 'gk_foundation_view_products' ) ) ||
				( ! is_multisite() && current_user_can( 'install_plugins' ) ) ||
				( is_multisite() && ( current_user_can( 'activate_plugins' ) || current_user_can( 'manage_network_plugins' ) ) ),
			'install_products'    =>
				( ! is_super_admin() && current_user_can( 'gk_foundation_install_products' ) ) ||
				( ! is_multisite() && current_user_can( 'install_plugins' ) ) ||
				( is_multisite() && current_user_can( 'manage_network_plugins' ) && CoreHelpers::is_network_admin() ),
			'update_products'     =>
				( ! is_super_admin() && current_user_can( 'gk_foundation_update_products' ) ) ||
				( ! is_multisite() && current_user_can( 'update_plugins' ) ) ||
				( is_multisite() && current_user_can( 'manage_network_plugins' ) && CoreHelpers::is_network_admin() ),
			'activate_products'   =>
				( ! is_super_admin() && current_user_can( 'gk_foundation_activate_products' ) ) ||
				( ! is_multisite() && current_user_can( 'activate_plugins' ) ) ||
				( is_multisite() && ( current_user_can( 'activate_plugins' ) || current_user_can( 'manage_network_plugins' ) ) ),
			'delete_products'     =>
				( ! is_super_admin() && current_user_can( 'gk_foundation_delete_products' ) ) ||
				( ! is_multisite() && current_user_can( 'delete_plugins' ) ) ||
				( is_multisite() && current_user_can( 'manage_network_plugins' ) && CoreHelpers::is_network_admin() ),
			'deactivate_products' =>
				( ! is_super_admin() && current_user_can( 'gk_foundation_deactivate_products' ) ) ||
				( ! is_multisite() && current_user_can( 'install_plugins' ) ) ||
				( is_multisite() && ( current_user_can( 'activate_plugins' ) || current_user_can( 'manage_network_plugins' ) ) ),
		];

		/**
		 * @filter `gk/foundation/licenses/permissions` Modifies permissions to access Licenses functionality.
		 *
		 * @since  1.0.0
		 *
		 * @param array $permissions Permissions.
		 */
		$this->_permissions = apply_filters( 'gk/foundation/licenses/permissions', $permissions );
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Framework
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initializes the License framework.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		if ( did_action( 'gk/foundation/licenses/initialized' ) ) {
			return;
		}

		if ( ! $this->current_user_can( 'view_licenses' ) && ! $this->current_user_can( 'view_products' ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		add_filter( 'gk/foundation/ajax/' . self::AJAX_ROUTER . '/routes', [ $this, 'configure_ajax_routes' ] );

		$this->_product_manager = ProductManager::get_instance();
		$this->_license_manager = LicenseManager::get_instance();

		$this->_product_manager->init();
		$this->_license_manager->init();

		EDD::get_instance()->init();
		ProductHistoryManager::get_instance()->init();
		PluginsPage::get_instance()->init();
		UpdatesPage::get_instance()->init();

		$this->add_gk_submenu_item();

		/**
		 * @action `gk/foundation/licenses/initialized` Fires when the class has finished initializing.
		 *
		 * @since  1.0.0
		 *
		 * @param $this
		 */
		do_action( 'gk/foundation/licenses/initialized', $this );
	}

	/**
	 * Configures Ajax routes handled by this class.
	 *
	 * @since 1.0.0
	 *
	 * @see   FoundationCore::process_ajax_request()
	 *
	 * @param array $routes Ajax route to class method map.
	 *
	 * @return array
	 */
	public function configure_ajax_routes( array $routes ) {
		return array_merge( $routes, [
			'get_app_data' => [ $this, 'ajax_get_app_data' ],
		] );
	}

	/**
	 * Ajax request to get products and/or licenses data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $payload
	 *
	 * @throws Exception
	 *
	 * @return array{products:array,licenses:array}
	 */
	public function ajax_get_app_data( array $payload ) {
		$payload = wp_parse_args( $payload, [
			'skip_cache' => false,
		] );

		$response = [];

		if ( ! $this->current_user_can( 'view_products' ) && ! $this->current_user_can( 'view_licenses' ) ) {
			throw new Exception( esc_html__( 'You do not have permission to view this page.', 'gk-gravityedit' ) );
		}

		// When skipping cache, we need to first refresh licenses and then products since the products data depends on the licenses' data.
		if ( $this->current_user_can( 'view_licenses' ) ) {
			$licenses_payload = $payload;

			$response['licenses'] = [
				'licenses' => $this->_license_manager->ajax_get_licenses_data( $licenses_payload ),
				'meta'     => [
					'is_decryptable' => $this->_license_manager->is_decryptable,
				],
			];
		}

		if ( $this->current_user_can( 'view_products' ) ) {
			$products_payload = array_merge( $payload, [ 'skip_remote_cache' => $payload['skip_cache'] ] );

			unset( $products_payload['skip_cache'] );

			try {
				$response['products'] = $this->_product_manager->ajax_get_products_data( $products_payload );
			} catch ( Exception $e ) {
				throw new Exception( $e->getMessage() );
			}
		}

		return $response;
	}

	/**
	 * Returns framework title used in admin menu and the UI.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_framework_title() {
		if ( ! $this->current_user_can( 'view_licenses' ) && ! $this->current_user_can( 'view_products' ) ) {
			return '';
		}

		return esc_html__( 'Manage Your Kit', 'gk-gravityedit' );
	}

	/**
	 * Adds Licenses submenu to the GravityKit top-level admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_gk_submenu_item() {
		AdminMenu::add_submenu_item( [
			'page_title'         => $this->get_framework_title(),
			'menu_title'         => $this->get_framework_title(),
			'capability'         => 'manage_options',
			'id'                 => self::ID,
			'callback'           => function () {
				// Settings data will be injected into #wpbody by gk-setting.js (see /UI/Settings/src/main-prod.js)
			},
			'order'              => 1,
			'hide_admin_notices' => true,
		], 'top' );
	}

	/**
	 * Enqueues UI assets.
	 *
	 * @since 1.0.0
	 *
	 * @param string $page Current page.
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function enqueue_assets( $page ) {
		if ( strpos( $page, self::ID ) === false ) {
			return;
		}

		$script = 'licenses.js';
		$style  = 'licenses.css';

		if ( ! file_exists( CoreHelpers::get_assets_path( $script ) ) ||
		     ! file_exists( CoreHelpers::get_assets_path( $style ) )
		) {
			LoggerFramework::get_instance()->warning( 'UI assets not found.' );

			return;
		}

		wp_enqueue_script(
			self::ID,
			CoreHelpers::get_assets_url( $script ),
			[ 'wp-i18n' ],
			filemtime( CoreHelpers::get_assets_path( $script ) )
		);

		$script_data = array_merge(
			[
				'appTitle'                  => $this->get_framework_title(),
				'isNetworkAdmin'            => CoreHelpers::is_network_admin(),
				'permissions'               => $this->_permissions,
				'frontendFoundationVersion' => FoundationCore::VERSION,
				'backendFoundationVersion'  => FoundationCore::VERSION,
				'pluginsPageUrl'            => CoreHelpers::is_network_admin() ? network_admin_url( 'plugins.php' ) : admin_url( 'plugins.php' ),
				'languageDirection'         => is_rtl() ? 'rtl' : 'ltr',
			],
			FoundationCore::ajax_router()->get_ajax_params( self::AJAX_ROUTER )
		);

		$app_data = [
			'products' => [],
			'licenses' => [],
		];

		try {
			$app_data = $this->ajax_get_app_data( [] );
		} catch ( Exception $e ) {
			// No need to handle the error here.
		}

		$script_data = array_merge( $script_data, $app_data );

		wp_localize_script(
			self::ID,
			'gkLicenses',
			[ 'data' => $script_data ]
		);

		wp_enqueue_style(
			self::ID,
			CoreHelpers::get_assets_url( $style ),
			[],
			filemtime( CoreHelpers::get_assets_path( $style ) )
		);

		// WP's forms.css interferes with our styles.
		wp_deregister_style( 'forms' );
		wp_register_style( 'forms', false );

		// Load UI translations using the text domain of the product that instantiated Foundation.
		$foundation_information = FoundationCore::get_instance()->get_foundation_information();
		TranslationsFramework::get_instance()->load_frontend_translations( $foundation_information['source_plugin']['TextDomain'], '', 'gk-foundation' );
	}

	/**
	 * Checks if the current user has a certain permission.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function current_user_can( $permission ) {
		if ( CoreHelpers::is_cli() ) {
			return true;
		}

		return $this->_permissions[ $permission ] ?? false;
	}

	/**
	 * Returns Product Manager class instance.
	 *
	 * @since 1.0.3
	 *
	 * @return ProductManager
	 */
	function product_manager() {
		return $this->_product_manager;
	}

	/**
	 * Returns License Manager class instance.
	 *
	 * @since 1.0.3
	 *
	 * @return LicenseManager
	 */
	function license_manager() {
		return $this->_license_manager;
	}

	/**
	 * Returns link to product search in the licensing page.
	 *
	 * @since 1.0.5
	 *
	 * @param string $product_id Product ID (EDD download ID).
	 *
	 * @return string
	 */
	function get_link_to_product_search( $product_id ) {
		$admin_page = 'admin.php?page=' . self::ID;

		$admin_url = CoreHelpers::is_network_admin() ? network_admin_url( $admin_page ) : admin_url( $admin_page );

		return add_query_arg(
			[
				'filter' => 'custom',
				'search' => 'id:' . $product_id,
			],
			$admin_url
		);
	}
}
