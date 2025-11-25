<?php

/**
 * Interface for interacting with GravityWiz.com
 *
 * This class is responsible for:
 *
 * * Validating licenses
 * * Updating GP & Perks
 * * Installing Perks
 * * Pulling Perk WP Plugin Info
 * * Registering Perks
 * * Deregistering Perks
 * * Getting Announcements
 *
 * @version 2.0
 */
require_once( dirname( __FILE__ ) . '/traits/trait-perk-license.php' );
require_once( dirname( __FILE__ ) . '/traits/trait-connect-license.php' );
require_once( dirname( __FILE__ ) . '/traits/trait-shop-license.php' );
require_once( dirname( __FILE__ ) . '/traits/trait-gcgs-license.php' );
require_once( dirname( __FILE__ ) . '/traits/trait-wiz-bundle-license.php' );
require_once( dirname( __FILE__ ) . '/traits/trait-wiz-bundle-upgrade.php' );

class GWAPI {
    use GWAPI_Perk_License;
    use GWAPI_Connect_License;
    use GWAPI_Shop_License;
    use GWAPI_GCGS_License;
    use GWAPI_Wiz_Bundle_License;
    use GWAPI_Wiz_Bundle_Upgrade;

	// Product type constants
	const PRODUCT_TYPE_PERK = 'perk';
	const PRODUCT_TYPE_CONNECT = 'connect';
	const PRODUCT_TYPE_SHOP = 'shop';
	const PRODUCT_TYPE_FREE = 'free';
	const PRODUCT_TYPE_WIZ_BUNDLE = 'wiz-bundle';

	// Product type configuration
	public static $product_config = [
		self::PRODUCT_TYPE_PERK => [
			'categories' => ['perk'],
			'item_name' => 'Gravity Perks',
		],
		self::PRODUCT_TYPE_CONNECT => [
			'categories' => ['connection'],
			'item_name' => 'Gravity Connect',
		],
		self::PRODUCT_TYPE_SHOP => [
			'categories' => ['gravity-shop'],
			'item_name' => 'GS Product Configurator',
		],
		self::PRODUCT_TYPE_FREE => [
			'categories' => ['free-plugin'],
			'item_name' => 'Free Plugins',
			'has_license' => false,
		],
		self::PRODUCT_TYPE_WIZ_BUNDLE => [
			'categories' => ['wiz-bundle'],
			'item_name' => 'Wiz Bundle',
			'is_bundle' => true,
			'registration_type' => 'plugin',
			'bundle_priority' => 1,
		],
	];

	private $gcgs_upgrade_successful = false;
	private $should_activate_gcgs = false;

	private $_product_update_data = array(
		'loaded'    => false,
		'response'  => array(),
		'no_update' => array(),
	);

	/**
	 * @var string The slug of the plugin sending the request.
	 */
	public $slug;

	/**
	 * @var array Runtime cache as a backup to transients in case serialization fails. Prevents duplicate requests.
	 */
	protected $runtime_cache = array();

	const TRANSIENT_EXPIRATION = 43200; //60 * 60 * 12 = 12 hours

	function __construct( $args ) {

		/**
		* @var $plugin_file
		*/
		extract( wp_parse_args( $args ) );

		$this->slug = basename( $plugin_file, '.php' );

		$this->hook();

	}

	private function request( $args ) {

		/**
		* @var $action
		* @var $api_params
		* @var $callback
		* @var $method
		* @var $cache
		* @var $flush
		* @var $transient
		* @var $cache_expiration
		* @var $output
		*/
		extract( wp_parse_args( $args, array(
			'action'           => '',
			'api_params'       => array(),
			'callback'         => null,
			'method'           => 'GET',
			'cache'            => true,
			'flush'            => false,
			'transient'        => null,
			'cache_expiration' => self::TRANSIENT_EXPIRATION,
			'output'           => ARRAY_A,
		) ) );

		if ( ! $transient ) {
			$transient = 'spellbook_gwapi_' . $action;

			if ( ! empty( $api_params ) ) {
				$transient .= '_' . md5( wp_json_encode( $api_params ) );
			}
		}

		// Suffix transient with the current Gravity Perks version in case behavior changes between versions.
		$transient = $transient . '_' . SPELLBOOK_VERSION;

		if ( $cache && ! $flush ) {
			if ( isset( $this->runtime_cache[ $transient ] ) ) {
				return $this->runtime_cache[ $transient ];
			}

			$cached = get_site_transient( $transient );

			if ( $cached !== false ) {
				return $cached;
			}
		}

		$request_url = esc_url_raw( GWAPI_URL );

		$api_params = self::get_api_args( array_merge( array(
			'edd_action' => $action,
		), $api_params ) );

		/* This filter is automatically removed after running */
		add_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );

		switch ( strtoupper( $method ) ) {
			case 'POST':
				$request_args = self::get_request_args( array( 'body' => urlencode_deep( $api_params ) ) );
				$response     = wp_remote_post( $request_url, $request_args );
				break;

			case 'GET':
			default:
				$request_args = self::get_request_args();
				$request_url  = add_query_arg( $api_params, $request_url );
				$response     = wp_remote_get( $request_url, $request_args );
				break;
		}

		GravityPerks::log( print_r( compact( 'request_url', 'request_args', 'response' ), true ) );

		if ( is_wp_error( $response ) ) {
			if ( $cache ) {
				set_site_transient( $transient, null, $cache_expiration );
				$this->runtime_cache[ $transient ] = null;
			}

			return false;
		}

		if ( $output === 'code' ) {
			return wp_remote_retrieve_response_code( $response );
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response      = json_decode( $response_body, $output === ARRAY_A );

		/**
		* We check that the response is not an array as an empty array evaluates as false when it is a valid response
		* in this situation.
		*/
		if ( ! $response && ! is_array( $response ) ) {
			if ( $cache ) {
				set_site_transient( $transient, null, $cache_expiration );
				$this->runtime_cache[ $transient ] = null;
			}

			return false;
		}

		if ( is_callable( $callback ) ) {
			$response = call_user_func( $callback, $response );
		}

		if ( $cache ) {
			set_site_transient( $transient, $response, $cache_expiration );
			$this->runtime_cache[ $transient ] = $response;
		}

		return $response;

	}

	private function hook() {

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ), 99999 );
		add_filter( 'plugins_api', array( $this, 'products_plugins_api_filter' ), 100, 3 );
		add_filter( 'http_request_host_is_external', array( $this, 'allow_gwiz_external_redirects' ), 15, 3 );
		add_filter( 'upgrader_package_options', array( $this, 'upgrader_package_options_filter' ) );
		add_filter( 'upgrader_post_install', array( $this, 'gpgs_to_gcgs_upgrader_post_install' ), 10, 3 );
		add_action( 'upgrader_process_complete', array( $this, 'gpgs_to_gcgs_upgrader_process_complete' ), 10, 2 );

		add_action( 'init', array( $this, 'disable_gc_gwapi' ), 99999 );
		add_action( 'init', array( $this, 'disable_gspc_gwapi' ), 99999 );

	}

	/**
	 * Disables the Gravity Connect GWAPI class as Spellbook now handles updating Gravity Connect products.
	 *
	 * @return void
	 */
	public function disable_gc_gwapi() {
		if ( ! class_exists( 'Gravity_Connect\Licensing\GWAPI' ) ) {
			return;
		}

		$gc_gwapi = Gravity_Connect\Licensing\GWAPI::get_instance();

		remove_filter( 'pre_set_site_transient_update_plugins', [ $gc_gwapi, 'pre_set_site_transient_update_plugins_filter' ], 99999 );
		remove_filter( 'plugins_api', [ $gc_gwapi, 'products_plugins_api_filter' ], 100, 3 );
		remove_filter( 'http_request_host_is_external', [ $gc_gwapi, 'allow_gwiz_external_redirects' ], 15, 3 );
		remove_filter( 'upgrader_package_options', [ $gc_gwapi, 'upgrader_package_options_filter' ], 9 );
	}

	/**
	 * Disables the GS Product Configurator GWAPI class as Spellbook now handles updating Gravity Shop products.
	 *
	 * @return void
	 */
	public function disable_gspc_gwapi() {
		if ( ! function_exists( 'gs_product_configurator' ) ) {
			return;
		}

		if ( ! isset( gs_product_configurator()->gwapi_lite ) ) {
			return;
		}

		$gspc_gwapi = gs_product_configurator()->gwapi_lite;

		remove_filter( 'pre_set_site_transient_update_plugins', [ $gspc_gwapi, 'pre_set_site_transient_update_plugins_filter' ], 99999 );
		remove_filter( 'plugins_api', [ $gspc_gwapi, 'products_plugins_api_filter' ], 100, 3 );
		remove_filter( 'http_request_host_is_external', [ $gspc_gwapi, 'allow_gwiz_external_redirects' ], 15, 3 );
		remove_filter( 'upgrader_package_options', [ $gspc_gwapi, 'upgrader_package_options_filter' ], 9 );
	}

	public function gpgs_to_gcgs_upgrader_post_install( $response, $hook_extra, $result ) {
		if ( rgar( $hook_extra, 'plugin' ) !== 'gp-google-sheets/gp-google-sheets.php' ) {
			return $response;
		}

		if ( is_plugin_active( 'gp-google-sheets/gp-google-sheets.php' ) ) {
			// Unhook Action Scheduler during this request to prevent errors.
			if ( class_exists( 'ActionScheduler_QueueRunner' ) ) {
				remove_action( 'shutdown', array( ActionScheduler_QueueRunner::instance(), 'maybe_dispatch_async_request' ) );
			}

			deactivate_plugins( 'gp-google-sheets/gp-google-sheets.php' );

			/*
			 * I don't know the technical reasoning behind this, but if we try activating GCGS here, it doesn't
			 * end up getting activated. To work around this, we set a property and do it later during
			 * upgrader_process_complete.
			 */
			$this->should_activate_gcgs = true;
		}

		$this->gcgs_upgrade_successful = true;

		return $response;
	}

	public function gpgs_to_gcgs_upgrader_process_complete( $upgrader, $hook_extra ) {
		if ( rgar( $hook_extra, 'action' ) !== 'update' || rgar( $hook_extra, 'type' ) !== 'plugin' ) {
			return;
		}

		if ( ! is_array( rgar( $hook_extra, 'plugins' ) ) || ! in_array( 'gp-google-sheets/gp-google-sheets.php', $hook_extra['plugins'], true ) ) {
			return;
		}

		if ( ! $this->gcgs_upgrade_successful ) {
			return;
		}

		$upgrader->skin->feedback( __( '<strong>Important Note!</strong> GP Google Sheets has been converted to GC Google Sheets', 'spellbook' ) );

		if ( $this->should_activate_gcgs ) {
			activate_plugin( 'gc-google-sheets/gc-google-sheets.php' );
			$upgrader->skin->feedback( __( 'GC Google Sheets has been activated.', 'spellbook' ) );
		}
	}

	public function upgrader_package_options_filter( $options ) {

		if ( ! isset( $options['package'] ) || ! is_string( $options['package'] ) ) {
			return $options;
		}

		if ( strpos( $options['package'], GW_DOMAIN ) === false ) {
			return $options;
		}

		// Get product info either from hook_extra/plugin or product_id in URL
		$plugin_file = rgars( $options, 'hook_extra/plugin' );
		$product = null;

		if ( empty( $plugin_file ) ) {
			$parsed_url = parse_url( $options['package'] );
			parse_str( $parsed_url['query'], $query_params );

			if ( isset( $query_params['product_id'] ) ) {
				$product = $this->get_product_by_id( $query_params['product_id'] );

				if ( $product ) {
					$plugin_file = $product->plugin_file;
				}
			}
		} else {
			$products = $this->get_products();
			$product = isset( $products[ $plugin_file ] ) ? $products[ $plugin_file ] : null;
		}

		// If we don't have the plugin_file or product, bail.
		if ( empty( $plugin_file ) || ! $product ) {
			return $options;
		}

		$product_version = $this->get_local_product_version( $plugin_file );

		// Check if this is a free plugin by looking at its categories
		if (
			( ! empty( $product->categories ) && in_array( 'free-plugin', $product->categories ) )
			|| $product->slug === 'spellbook'
		) {
			$options['package'] = $this->prepare_free_plugin_package_url(
				$options['package'],
				$product_version
			);
		} else {
			$options['package'] = $this->prepare_licensed_plugin_package_url(
				$options['package'],
				$plugin_file,
				$product_version
			);
		}

		/*
		 * If we're installing a new perk, flush the license info. We know we're installing a new perk if
		 * $abort_if_destination_exists is true.
		 */
		if ( rgar( $options, 'abort_if_destination_exists' ) ) {
			$this->flush_license_info_by_plugin_file( $plugin_file );
		}

		return $options;

	}

	public function allow_gwiz_external_redirects( $allow, $host, $url ) {

		if ( $host === GW_DOMAIN ) {
			return true;
		}

		return $allow;

	}

	/**
	* Get all available products from the store.
	*
	* Note: The API supports fetching all product types in a single request.
	* We always fetch all categories for better caching efficiency.
	*
	* @param bool $flush Whether to flush the cache
	* @return array|false Array of products or false on failure
	*/
	public function get_products( $flush = false ) {

		$products = $this->request( array(
			'action'   => 'get_products',
			'output'   => OBJECT,
			'cache'    => true,
			'api_params' => [
				'download_categories' => array_merge( array_reduce(self::$product_config, function($carry, $config) {
					return array_merge($carry, $config['categories']);
				}, []), [ 'gravity-perks', 'spellbook' ] ),
			],
			'callback' => array( $this, 'process_get_products' ),
			'flush'    => $flush,
		) );

		return is_array( $products ) ? $products : [];

	}

	/**
	* Gets a single product. Used mostly for changelog, etc.
	*
	* @param int $product_id The product ID
	* @return stdClass|false Array of the product or false on failure
	*/
	public function get_product( $product_id ) {

		$product = $this->request( array(
			'action'   => 'gw_get_product',
			'output'   => OBJECT,
			'cache'    => true,
			'api_params' => [
				'product_id' => $product_id,
			],
			'callback' => array( $this, 'process_get_product' ),
		) );

		return is_object( $product ) ? $product : false;

	}

	/**
	 * Flush cache for products.
	 */
	public function flush_products() {
		delete_site_transient( 'gwapi_get_products_' . SPELLBOOK_VERSION );
	}

	public function process_get_products( $response ) {

		$products = array();

		if ( ! is_object( $response ) || empty( $response ) ) {
			return false;
		}

		// Do a deep maybe_unserialize
		$response = map_deep( $response, 'maybe_unserialize' );

		foreach ( $response as $plugin_file => $plugin ) {
			$plugin->download_link = $plugin->package;

			// If GC Google Sheets is not installed, convert GCGS to be GPGS to provide an upgrade path.
			if (
				$plugin_file === 'gc-google-sheets/gc-google-sheets.php'
				// class_exists() is not reliable here due to varying contexts.
				&& ! GWPerk::is_installed( 'gc-google-sheets/gc-google-sheets.php' )
			) {
				$plugin_file = 'gp-google-sheets/gp-google-sheets.php';
				$plugin->slug = 'gp-google-sheets';
				$plugin->plugin = 'gp-google-sheets';
				$plugin->plugin_file = 'gp-google-sheets/gp-google-sheets.php';
			}

			$plugin->type = $this->determine_product_type( $plugin->categories );

			$products[ $plugin_file ] = $plugin;

		}

		return ! empty( $products ) ? $products : false;

	}

	public function process_get_product( $plugin ) {

		if ( ! is_object( $plugin ) || empty( $plugin ) ) {
			return false;
		}

		// Do a deep maybe_unserialize
		$plugin = map_deep( $plugin, 'maybe_unserialize' );

		if ( property_exists( $plugin, 'sections' ) ) {
			if ( isset( $plugin->sections['changelog'] ) ) {
				$plugin->sections['changelog'] = GWPerks::format_changelog( $plugin->sections['changelog'], $plugin );
			}
		}

		if ( isset( $plugin->legacy_changelog ) ) {
			$plugin->legacy_changelog = GWPerks::format_changelog( $plugin->legacy_changelog, $plugin );
		}

		$plugin->download_link = $plugin->package;
		$plugin->type = $this->determine_product_type( $plugin->categories );

		return $plugin;

	}

	/**
	 * Determine the product type based on its categories
	 *
	 * @param array $categories Product categories
	 * @return string Product type (connect, shop, free, or perk)
	 */
	private function determine_product_type( $categories ) {
		if ( in_array( 'connection', $categories ) ) {
			return 'connect';
		}

		if ( in_array( 'gravity-shop', $categories ) ) {
			return 'shop';
		}

		if ( in_array( 'free-plugin', $categories ) ) {
			return 'free';
		}

		return 'perk';
	}

	/**
	 * Finds a product by ID.
	 *
	 * @param string $product_id The product ID to search for.
	 * @return array|false The product data if found, false otherwise.
	 */
	public function get_product_by_id( $product_id ) {
		$products = $this->get_products();

		if ( ! is_array( $products ) ) {
			return false;
		}

		foreach ( $products as $product ) {
			if ( isset( $product->ID ) && $product->ID == $product_id ) {
				return $product;
			}
		}

		return false;
	}

	/**
	 * Finds a product by plugin file.
	 *
	 * @param string $plugin_file The plugin file (e.g. gp-populate-anything/gp-populate-anything.php) to search for.
	 * @return array|false The product data if found, false otherwise.
	 */
	public function get_product_by_plugin_file( $plugin_file ) {
		$products = $this->get_products();

		if ( ! is_array( $products ) ) {
			return false;
		}

		foreach ( $products as $product ) {
			if ( isset( $product->plugin_file ) && $product->plugin_file == $plugin_file ) {
				return $product;
			}
		}

		return false;
	}


	/**
	* Get Spellbook Announcements from GWAPI v8
	*/
	public function get_spellbook_announcements( $flush = false ) {

		return $this->request( array(
			'action' => 'spellbook_announcements',
			'output' => ARRAY_A,
			'cache' => true,
			'flush' => $flush,
		) );

	}

	/**
	* This is the function that lets WordPress know if there is an update available
	* for one of our products.
	*
	* @param mixed $_transient_data
	*/
	public function pre_set_site_transient_update_plugins_filter( $_transient_data ) {
		// Force check on initial request, but not subsequent requests through this filter.
		static $force_check = null;

		if ( $force_check === null ) {
			$force_check = rgget( 'force-check' ) == 1;
		}

		GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() start. Retrieves download package for individual product auto-updates.' );

		// Check for license upgrades using cached data (no forced API call)
		// This allows automatic migration during update checks
		if ( $this->get_perk_license_key() ) {
			$this->get_license_data( 'perk', $force_check );
		}
		if ( $this->get_connect_license_key() ) {
			$this->get_license_data( 'connect', $force_check );
		}

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new stdClass;
		}

		if ( empty( $_transient_data->response ) ) {
			$_transient_data->response = array();
		}

		// check if our run-time cache is populated, save a little hassle of having to loop through this over and over
		if ( $this->_product_update_data['loaded'] && ! $force_check ) {
			$_transient_data->response  = array_merge( (array) $_transient_data->response, $this->_product_update_data['response'] );

			if ( ! isset( $_transient_data->no_update ) ) {
				$_transient_data->no_update = array();
			}

			$_transient_data->no_update = array_merge( (array) $_transient_data->no_update, $this->_product_update_data['no_update'] );

			GravityPerks::log_debug( 'Cached update data available.' );
			GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() end. Returning cached update data.' );

			return $_transient_data;
		}

		GravityPerks::log_debug( 'Retrieving product data.' );

		$remote_products     = $this->get_products( $force_check );
		$product_update_data = array();

		GravityPerks::log_debug( print_r( $remote_products, true ) );

		if ( ! is_array( $remote_products ) ) {
			GravityPerks::log_debug( 'Failed to retrieve remote product data.' );

			return $_transient_data;
		}

		foreach ( $remote_products as $remote_product_file => $remote_product ) {
			$local_product_version = $this->get_local_product_version( $remote_product_file );

			/* Handle legacy versions if available */
			if ( $this->should_use_legacy_version( $remote_product, $local_product_version ) ) {
				GravityPerks::log_debug( "Local product version: {$local_product_version}" );

				// Modify the remote product and swap the legacy version in for the new version
				$remote_product->new_version = $remote_product->legacy_version;
				$remote_product->version     = $remote_product->legacy_version;

				if ( isset( $remote_product->sections['legacy_changelog'] ) ) {
					$remote_product->sections['changelog'] = $remote_product->sections['legacy_changelog'];
				}
			}

			/*
			 * Unset needed keys from the product update data. Keys like changelog will be fetched using the
			 * `plugins_api` filter.
			 */
			$keys_to_remove = array(
				'sections',
				'download_link',
				'categories',
				'documentation',
				'changelog',
				'legacy_version',
				'legacy_changelog',
				'legacy_version_requirement',
				'version',
				'author',
				'last_updated',
			);

			foreach ( $keys_to_remove as $key ) {
				if ( isset( $remote_product->$key ) ) {
					unset( $remote_product->$key );
				}
			}

			// Change the 'homepage' key to 'url' to match the format of the WP.org API response.
			if (
				isset( $remote_product->homepage )
				&& ! isset( $remote_product->url )
			) {
				$remote_product->url = $remote_product->homepage;
				unset( $remote_product->homepage );
			}

			if ( $local_product_version ) {
				GravityPerks::log_debug( 'Product update found. Adding to local product update data.' . print_r( $remote_product, true ) );

				if ( version_compare( $local_product_version, $remote_product->new_version, '<' ) ) {
					$this->_product_update_data['response'][ $remote_product_file ] = $remote_product;
				} else {
					$this->_product_update_data['no_update'][ $remote_product_file ] = $remote_product;
				}
			}
		}

		$_transient_data->response  = array_merge( (array) $_transient_data->response, $this->_product_update_data['response'] );

		if ( ! isset( $_transient_data->no_update ) ) {
			$_transient_data->no_update = array();
		}

		// https://make.wordpress.org/core/2020/07/30/recommended-usage-of-the-updates-api-to-support-the-auto-updates-ui-for-plugins-and-themes-in-wordpress-5-5/
		$_transient_data->no_update = array_merge( (array) $_transient_data->no_update, $this->_product_update_data['no_update'] );

		$this->_product_update_data['loaded'] = true;

		GravityPerks::log_debug( 'pre_set_site_transient_update_plugins_filter() end. Returning update data.' . print_r( $_transient_data, true ) );

		$force_check = false;

		return $_transient_data;
	}

	/**
	 * Check if the product has a legacy version and meets the requirements
	 */
	public function should_use_legacy_version( $remote_product, $local_product_version ) {
		if ( ! is_object( $remote_product ) || empty( $remote_product ) ) {
			return false;
		}

		$has_legacy_version             = property_exists( $remote_product, 'legacy_version' ) && $remote_product->legacy_version;
		$has_legacy_version_requirement = property_exists( $remote_product, 'legacy_version_requirement' ) && $remote_product->legacy_version_requirement;

		if ( $has_legacy_version_requirement ) {
			preg_match( '/^([<>]=?)(.*)$/', $remote_product->legacy_version_requirement, $legacy_version_requirement_matches );
		}

		return $local_product_version
			&& $has_legacy_version
			&& $has_legacy_version_requirement
			&& ! empty( $legacy_version_requirement_matches )
			&& version_compare( $local_product_version, $legacy_version_requirement_matches[2], $legacy_version_requirement_matches[1] );
	}

	/**
	* Provides download package when installing and information on the "View version x.x details" page.
	*
	* @uses api_request()
	*
	* @param mixed $_data
	* @param string $_action
	* @param object $_args
	*
	* @return object $_data
	*/
	public function products_plugins_api_filter( $_data, $_action = '', $_args = null ) {

		GravityPerks::log_debug( 'products_plugins_api_filter() start. Retrieves download package and plugin info.' );

		$plugin_file = isset( $_args->slug ) ? $_args->slug : gwget( 'plugin' );
		if ( strpos( $plugin_file, '/' ) === false ) {
			$plugin_file = sprintf( '%1$s/%1$s.php', $plugin_file );
		}

		if ( $_action != 'plugin_information' || ! $plugin_file ) {
			return $_data;
		}

		GravityPerks::log_debug( 'Yes! This is a Gravity Wiz product.' );

		$remote_products = $this->get_products();

		if ( ! $remote_products ) {
			GravityPerks::log_debug( 'Rats! There was an error with the GW API response' );

			return $_data;
		}

		$remote_product = rgar( $remote_products, $plugin_file );
		$product_id = isset( $remote_product->ID ) ? $remote_product->ID : false;

		if ( ! $product_id ) {
			return $_data;
		}

		// Send API request to get the individual product data.
		$product = $this->get_product( $product_id );

		if ( $this->should_use_legacy_version( $product, $this->get_local_product_version( $plugin_file ) ) ) {
			if ( isset( $product->legacy_changelog ) ) {
				$product->sections['changelog'] = $product->legacy_changelog;
			}

			$product->version = $product->legacy_version;
		}


		GravityPerks::log_debug( 'Ok! Everything looks good. Let\'s build the response needed for WordPress.' );

		// don't allow other plugins to override the $request this function returns, several plugins use the 'plugins_api'
		// filter incorrectly and return a hard 'false' rather than returning the $_data object when they do not need to modify
		// the request which results in our customized $request being overwritten (WPMU Dev Dashboard v3.3.2 is one example)
		remove_all_filters( 'plugins_api' );

		// remove all the filters causes an infinite loop so add one dummy function so the loop can break itself
		add_filter( 'plugins_api', array( new GP_Late_Static_Binding(), 'GWAPI_dummy_func' ) );

		return $product;

	}

	/**
	 * @param mixed $product_type
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function validate_product_type( $product_type ) {
		if ( empty( $product_type ) ) {
			throw new InvalidArgumentException('Product type cannot be empty.');
		}

		if ( ! isset( self::$product_config[ $product_type ] ) ) {
			throw new InvalidArgumentException('Invalid product type.');
		}
	}

	public function get_product_name( $product_type ) {
		$this->validate_product_type( $product_type );

		return self::$product_config[ $product_type ]['item_name'];
	}

	/**
	 * Get all bundle product types, sorted by priority (highest first).
	 *
	 * @return array Array of bundle product type keys
	 */
	public function get_bundle_types() {
		$bundles = [];
		foreach (self::$product_config as $type => $config) {
			if (!empty($config['is_bundle'])) {
				$bundles[$type] = $config['bundle_priority'] ?? 0;
			}
		}
		arsort($bundles); // Sort by priority (highest first)
		return array_keys($bundles);
	}

	/**
	 * Check if a product type is a bundle.
	 *
	 * @param string $product_type Product type to check
	 * @return bool True if it's a bundle, false otherwise
	 */
	public function is_bundle_type($product_type) {
		return !empty(self::$product_config[$product_type]['is_bundle']);
	}

	/**
	 * Get the registration type for a product type.
	 *
	 * @param string $product_type Product type
	 * @return string Registration type ('plugin' or 'category')
	 */
	public function get_registration_type($product_type) {
		return self::$product_config[$product_type]['registration_type'] ?? 'category';
	}

	public function get_local_product_version( $plugin_file ) {
		$installed_plugins = GWPerks::get_plugins();

		return isset( $installed_plugins[ $plugin_file ] ) ? $installed_plugins[ $plugin_file ]['Version'] : false;
	}

	/**
	 * Get license data for a specific product type.
	 *
	 * @param string $product_type Product type to get license data for
	 * @param bool $flush Whether to flush the cache
	 * @return array|false License data array or false on failure
	 */
	public function get_license_data( $product_type, $flush = false ) {
		if (empty($product_type)) {
			throw new InvalidArgumentException('Product type cannot be empty.');
		}

		if ( ! isset( self::$product_config[ $product_type ] ) ) {
			throw new InvalidArgumentException('Invalid product type.');
		}

		$license_key = $this->get_license_key( $product_type );

		if ( ! $license_key ) {
			return false;
		}

		$item_name   = self::$product_config[ $product_type ]['item_name'];

		return $this->request( array(
			'action'     => 'check_license',
			'method'     => 'POST',
			'transient'  => 'gwapi_license_data_' . $product_type,
			'flush'      => $flush,
			'cache'      => true,
			'callback'   => function( $response ) use ( $product_type ) {
				return $this->process_license_data( $response, $product_type );
			},
			'api_params' => array(
				'license'      => $license_key,
				'item_name' => urlencode( $item_name ),
			),
		) );
	}

	/**
	 * Process license data response.
	 * Response format:
	 * {
	 *   "success": true|false,
	 *   "license": "valid|invalid|expired",
	 *   "item_id": 123,
	 *   "item_name": "Product Name",
	 *   "checksum": "abc123..."
	 * }
	 *
	 * @param array $response License API response
	 * @param string|null $checking_type The product type being checked (for upgrade detection)
	 * @return array Processed response with 'valid' key added
	 */
	public function process_license_data( $response, $checking_type = null ) {
		$has_valid_license = false;

		// Get product type from item name
		$product_type = false;
		foreach (self::$product_config as $type => $config) {
			if (urldecode($config['item_name']) === urldecode($response['item_name'])) {
				$product_type = $type;
				break;
			}
		}

		if ( is_array( $response ) ) {
			// Check for bundle upgrade (uses shared method)
			if ( $checking_type ) {
				$bundle_license_data = $this->check_and_handle_wiz_bundle_upgrade( $response, $checking_type );
				if ( $bundle_license_data ) {
					// Log the automatic upgrade
					GravityPerks::log_debug( "Automatically upgraded {$checking_type} license to Wiz Bundle" );

					// Return the bundle license data
					$response = $bundle_license_data;
					$product_type = 'wiz-bundle';
					$has_valid_license = $response['license'] === 'valid';
				}
			}

			// Existing inactive/site_inactive handling (only if not already migrated)
			if ( ! $has_valid_license && in_array( $response['license'], array( 'inactive', 'site_inactive' ) ) && $product_type ) {
				$license = $this->get_license_key($product_type);
				$has_valid_license = $this->activate_license( $product_type, $license );
				$response['license'] = $has_valid_license ? 'valid' : $response['license'];
			} else if ( ! $has_valid_license ) {
				$has_valid_license = $response['license'] == 'valid';
			}
		}

		$response['valid'] = $has_valid_license;

		return $response;
	}

	public function has_valid_license( $flush = false, $product_type = null ) {

		if ( ! $product_type ) {
			return false;
		}

		$license_data = $this->get_license_data( $product_type, $flush );

		return isset( $license_data['valid'] ) && $license_data['valid'];

	}

	public function get_api_status() {

		return $this->request( array(
			'action' => 'get_api_status',
			'cache'  => false,
			'output' => 'code',
		) );

	}

	public function log_http_request_args( $args ) {
		remove_filter( 'http_request_args', array( $this, 'log_http_request_args' ) );
		GravityPerks::log( print_r( compact( 'args' ), true ) );

		return $args;
	}

	public function activate_license( $product_type, $license ) {

		$response = $this->request( array(
			'action'     => 'activate_license',
			'api_params' => array(
				'license'   => $license,
				'item_name' => urlencode( $this->get_product_name( $product_type ) ),
			),
			'cache'      => false,
			'method'     => 'POST',
		) );

		return rgar( $response, 'license' ) === 'valid';

	}

	/**
	 * Deactivate a license for a specific product type.
	 *
	 * @param string $product_type Product type to deactivate license for
	 * @return bool Whether the deactivation was successful
	 */
	public function deactivate_license($product_type) {
		$this->validate_product_type($product_type);

		$license = $this->get_license_key($product_type);
		if (!$license) {
			return false;
		}

		$this->request([
			'action' => 'deactivate_license',
			'api_params' => [
				'license' => $license,
				'item_name' => urlencode($this->get_product_name($product_type)),
			],
			'cache' => false,
			'method' => 'POST',
		]);

		// Clear the license key no matter the response
		switch ($product_type) {
			case self::PRODUCT_TYPE_PERK:
				$this->remove_perk_license_key();
				break;
			case self::PRODUCT_TYPE_CONNECT:
				$this->remove_connect_license_key();
				break;
			case self::PRODUCT_TYPE_SHOP:
				$this->remove_shop_license_key();
				break;
			case self::PRODUCT_TYPE_WIZ_BUNDLE:
				$this->remove_wiz_bundle_license_key();
				break;
		}
		return true;
	}

	/**
	 * Register a product with the current license.
	 *
	 * @param string $product_id The ID of the product to register
	 * @param string $product_type The type of product (perk, connect, shop)
	 * @return bool Whether the registration was successful
	 */
	public function register_product( $product_id, $product_type ) {
		if (empty($product_type)) {
			throw new InvalidArgumentException('Product type cannot be empty.');
		}

		$response = $this->request( array(
			'action'     => 'register_product',
			'api_params' => array(
				'license' => $this->get_license_key($product_type),
				'product_id' => $product_id,
				'product_type' => $product_type,
			),
			'cache'      => false,
			'method'     => 'POST',
		) );

		return rgar( $response, 'success' );
	}

	/**
	 * Deregister a product from the current license.
	 *
	 * @param string $product_id The ID of the product to deregister
	 * @param string $product_type The type of product (perk, connect, shop)
	 * @return bool Whether the deregistration was successful
	 */
	public function deregister_product( $product_id, $product_type ) {
		if (empty($product_type)) {
			throw new InvalidArgumentException('Product type cannot be empty.');
		}

		$response = $this->request( array(
			'action'     => 'deregister_product',
			'api_params' => array(
				'license' => $this->get_license_key($product_type),
				'product_id' => $product_id,
				'product_type' => $product_type,
			),
			'cache'      => false,
			'method'     => 'POST',
		) );

		return rgar( $response, 'success' );
	}

	public static function get_api_args( $args = array() ) {
		return wp_parse_args( $args, array(
			'url'     => self::get_site_url(),
			'timeout' => 15,
		) );
	}

	public static function get_request_args( $args = array() ) {
		$default_args = array(
			'user-agent' => 'Gravity Perks ' . GWPerks::get_version(),
			'timeout'    => 15,
			'sslverify'  => (bool) apply_filters( 'edd_sl_api_request_verify_ssl', true ),
		);

		if ( defined( 'GW_BASIC_AUTH_USERNAME' ) && defined( 'GW_BASIC_AUTH_PASSWORD' ) ) {
			$default_args['headers'] = array(
				'Authorization' => 'Basic ' . base64_encode( GW_BASIC_AUTH_USERNAME . ':' . GW_BASIC_AUTH_PASSWORD ),
			);
		}

		return wp_parse_args( $args, $default_args );
	}

	public static function get_site_url() {
		return site_url( '', 'http' );
	}

	/**
	 * Get product type from plugin file path.
	 *
	 * @param string $plugin_file Plugin file path relative to plugins directory.
	 * @return string|false Product type constant or false if not recognized.
	 */
	public function get_product_type_from_file($plugin_file) {
		if (empty($plugin_file)) {
			return false;
		}

		// Special case for GCGS - check if we have a valid GP license first
		if (
			$plugin_file === 'gc-google-sheets/gc-google-sheets.php' ||
			$plugin_file === 'gp-google-sheets/gp-google-sheets.php'
		) {
			if ($this->has_gcgs_gp_license()) {
				return self::PRODUCT_TYPE_PERK;
			}

			return self::PRODUCT_TYPE_CONNECT;
		}

		if (preg_match('/^(gw|gp-)/', $plugin_file)) {
			return self::PRODUCT_TYPE_PERK;
		}

		if (strpos($plugin_file, 'gc-') === 0) {
			return self::PRODUCT_TYPE_CONNECT;
		}

		if (strpos($plugin_file, 'gs-') === 0) {
			return self::PRODUCT_TYPE_SHOP;
		}

		return false;
	}

	/**
	 * Get the license key based on product type.
	 *
	 * @param string $product_type Product type to get license for
	 * @return string|false The license key or false if not found
	 */
	public function get_license_key($product_type) {
		if (empty($product_type)) {
			throw new InvalidArgumentException('Product type cannot be empty.');
		}

		switch ($product_type) {
			case self::PRODUCT_TYPE_PERK:
				return $this->get_perk_license_key();
			case self::PRODUCT_TYPE_CONNECT:
				return $this->get_connect_license_key();
			case self::PRODUCT_TYPE_SHOP:
				return $this->get_shop_license_key();
			case self::PRODUCT_TYPE_WIZ_BUNDLE:
				return $this->get_wiz_bundle_license_key();
			case self::PRODUCT_TYPE_FREE:
				return null; // Free plugins do not have a license key
			default:
				throw new InvalidArgumentException('Invalid product type: ' . $product_type);
		}
	}

	/**
	 * Sets the license key for a specific product type.
	 *
	 * @param string $product_type Product type to set license for
	 * @param string $license_key License key to set
	 *
	 * @return bool True if the license key was set successfully, false otherwise
	 */
	public function set_license_key($product_type, $license_key) {
		if (empty($product_type)) {
			throw new InvalidArgumentException('Product type cannot be empty.');
		}

		switch ($product_type) {
			case self::PRODUCT_TYPE_PERK:
				return $this->set_perk_license_key($license_key);
			case self::PRODUCT_TYPE_CONNECT:
				return $this->set_connect_license_key($license_key);
			case self::PRODUCT_TYPE_SHOP:
				return $this->set_shop_license_key($license_key);
			case self::PRODUCT_TYPE_WIZ_BUNDLE:
				return $this->set_wiz_bundle_license_key($license_key);
			default:
				throw new InvalidArgumentException('Invalid product type: ' . $product_type);
		}
	}

	/**
	 * Removes the license key for a specific product type.
	 *
	 * @param string $product_type Product type to set license for
	 *
	 * @return bool True if the license key was removed successfully, false otherwise
	 */
	public function remove_license_key($product_type) {
		if (empty($product_type)) {
			throw new InvalidArgumentException('Product type cannot be empty.');
		}

		switch ($product_type) {
			case self::PRODUCT_TYPE_PERK:
				return $this->remove_perk_license_key();
			case self::PRODUCT_TYPE_CONNECT:
				return $this->remove_connect_license_key();
			case self::PRODUCT_TYPE_SHOP:
				return $this->remove_shop_license_key();
			case self::PRODUCT_TYPE_WIZ_BUNDLE:
				return $this->remove_wiz_bundle_license_key();
			default:
				throw new InvalidArgumentException('Invalid product type: ' . $product_type);
		}
	}

	/**
	 * Get the license key for a specific plugin file.
	 *
	 * @param string $plugin_file Plugin file path relative to plugins directory.
	 * @return string|false The license key or false if not found
	 */
	public function get_license_key_by_plugin_file($plugin_file) {
		$product_type = $this->get_product_type_from_file($plugin_file);
		if (!$product_type) {
			return false;
		}

		// If bundle key is present and type is connect or perk, use bundle key.
		if (in_array($product_type, [self::PRODUCT_TYPE_PERK, self::PRODUCT_TYPE_CONNECT])) {
			$bundle_key = $this->get_wiz_bundle_license_key();
			if ($bundle_key) {
				return $bundle_key;
			}
		}

		return $this->get_license_key($product_type);
	}

	/**
	 * Get the license data for a specific plugin file.
	 *
	 * @param string $plugin_file Plugin file path relative to plugins directory.
	 * @return array|false The license data or false if not found
	 */
	public function get_license_data_by_plugin_file($plugin_file) {
		$product_type = $this->get_product_type_from_file($plugin_file);
		if (!$product_type) {
			return false;
		}

		// If bundle key is present and type is connect or perk, use bundle key.
		if (in_array($product_type, [self::PRODUCT_TYPE_PERK, self::PRODUCT_TYPE_CONNECT])) {
			$bundle_license = $this->get_license_data(self::PRODUCT_TYPE_WIZ_BUNDLE);
			if ($bundle_license) {
				return $bundle_license;
			}
		}

		return $this->get_license_data($product_type);
	}

	/**
	 * Get any valid license data from any product type.
	 * Checks bundle licenses first, then individual licenses.
	 *
	 * @return array|false License data array or false if no valid license found
	 */
	private function get_any_valid_license_data() {
		// Check bundle licenses first (by priority)
		$bundle_types = $this->get_bundle_types();
		foreach ($bundle_types as $type) {
			$license_data = $this->get_license_data($type);
			if ($license_data && $license_data['valid']) {
				$license_data['product_type'] = $type;
				return $license_data;
			}
		}

		// Fall back to individual licenses
		foreach ([self::PRODUCT_TYPE_PERK, self::PRODUCT_TYPE_CONNECT, self::PRODUCT_TYPE_SHOP] as $type) {
			$license_data = $this->get_license_data($type);
			if ($license_data && $license_data['valid']) {
				$license_data['product_type'] = $type;
				return $license_data;
			}
		}
		return false;
	}

	/**
	 * Prepare package URL for free plugins.
	 *
	 * @param string $package_url Original package URL
	 * @param string $product_version Product version
	 * @return string Modified package URL
	 */
	private function prepare_free_plugin_package_url($package_url, $product_version) {
		// Always apply base replacements first
		$replacements = array(
			'%URL%' => rawurlencode(GWAPI::get_site_url()),
			'%PRODUCT_VERSION%' => $product_version
		);

		$package_url = str_replace(array_keys($replacements), array_values($replacements), $package_url);

		// Try to get any valid license first
		$license_data = $this->get_any_valid_license_data();

		if (!empty( $license_data['ID'] )) {
			$replacements = array(
				'%LICENSE_ID%' => rawurlencode($license_data['ID']),
				'%LICENSE_HASH%' => rawurlencode(md5($this->get_license_key($license_data['product_type'])))
			);

			return str_replace(array_keys($replacements), array_values($replacements), $package_url);
		}

		// No valid license, try email registration
		$email = get_option('gwp_spellbook_email');

		if ($email) {
			$package_url = remove_query_arg(['license_id', 'license_hash'], $package_url);
			return add_query_arg('email', $email, $package_url);
		}

		return $package_url;
	}

	/**
	 * Prepare package URL for licensed plugins.
	 * Uses bundle-first logic to get the best available license.
	 *
	 * @param string $package_url Original package URL
	 * @param string $plugin_file Plugin file path
	 * @param string $product_version Product version
	 * @return string Modified package URL
	 */
	private function prepare_licensed_plugin_package_url($package_url, $plugin_file, $product_version) {
		$license = $this->get_license_data_by_plugin_file($plugin_file);

		$replacements = array(
			'%URL%' => rawurlencode(GWAPI::get_site_url()),
			'%LICENSE_ID%' => rawurlencode(isset($license['ID']) ? $license['ID'] : ''),
			'%LICENSE_HASH%' => rawurlencode(md5($this->get_license_key_by_plugin_file($plugin_file))),
			'%PRODUCT_VERSION%' => $product_version
		);

		return str_replace(array_keys($replacements), array_values($replacements), $package_url);
	}

	/**
	 * Flushes the license info for a specific plugin file.
	 *
	 * @param string $plugin_file Plugin file path relative to plugins directory.
	 * @return void
	 */
	public function flush_license_info_by_plugin_file($plugin_file) {
		$product_type = $this->get_product_type_from_file($plugin_file);
		if (!$product_type) {
			return;
		}

		switch ($product_type) {
			case self::PRODUCT_TYPE_PERK:
				$this->flush_perk_license_info();
				break;
			case self::PRODUCT_TYPE_CONNECT:
				$this->flush_connect_license_info();
				break;
			case self::PRODUCT_TYPE_SHOP:
				$this->flush_shop_license_info();
				break;
			case self::PRODUCT_TYPE_WIZ_BUNDLE:
				$this->flush_wiz_bundle_license_info();
				break;
			default:
				throw new InvalidArgumentException('Invalid product type: ' . $product_type);
		}
	}

	/**
	 * Checks if a product is installed as a legacy free plugin. For instance, we have `gw-all-fields-template.php` in
	 * the Snippet Library and it can be installed as a single-file plugin.
	 *
	 * We need to detect this and show that it's installed, but pop a message telling them that they need to delete it
	 * and that they won't be able to update until doing so.
	 *
	 * @return bool
	 */
	public function is_legacy_free_plugin( $product ) {
		if ( ! in_array( 'free-plugin', $product->categories ) ) {
			return false;
		}

		// Get the plugin basename from $product->plugin_file.
		$plugin_basename = basename( $product->plugin_file, '.php' );

		/*
		 * Now check for variations to see if the plugin is installed as a single-file plugin or even in a folder, but
		 * with gw- prefix.
		 */
		$gw_plugin_basename = preg_replace( '/^gf-/', 'gw-', $plugin_basename );

		// If the plugin doesn't start with gf-, it was likely never prefixed with gw-.
		if ( $gw_plugin_basename === $plugin_basename ) {
			return false;
		}

		$variations = [
			$gw_plugin_basename . '.php',
			$gw_plugin_basename . '/' . $gw_plugin_basename . '.php',
		];

		foreach ( $variations as $variation ) {
			if ( GWPerk::is_installed( $variation ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Helper function to determine if a product can be auto-updated, and if not an error code why.
	 *
	 * @param string $plugin_file Plugin file path relative to plugins directory.
	 *
	 * @return true|null|array{
	 *     code: string,
	 *     suite_name: string,
	 *     plugin_name: string,
	 *     product: object,
	 *     type: string,
	 *     license_data: array,
	 * } Error code if the product cannot be auto-updated, or true if it can be, null
	 *   if the product is not found.
	 */
	public function can_auto_update($plugin_file) {
		$product = $this->get_product_by_plugin_file( $plugin_file );

		if ( ! $product ) {
			return null;
		}

		if ( $product->type === 'free' || $product->slug === 'spellbook' ) {
			// Make sure we have any valid license or email.
			$email = get_option('gwp_spellbook_email');
			$license_data = $this->get_any_valid_license_data();

			$has_email_or_license = ! empty( $email ) || ! empty( $license_data );

			if ( ! $has_email_or_license ) {
				return array(
					'code' => 'free_plugin_missing_email_or_license',
				);
			}

			return true;
		}

		$license_data = $this->get_license_data_by_plugin_file( $plugin_file );
		if ( ! $license_data ) {
			return null;
		}

		switch ( $product->type ) {
			case 'perk':
				$suite_name = 'Gravity Perks';
				/** @var int[] IDs of perks */
				$registered_products = $license_data['registered_perks'] ?? [];
				$registered_products_limit = $license_data['perk_limit'] ?? 0;

				$product_type = 'perk';
				break;

			case 'connect':
				$suite_name = 'Gravity Connect';
				/** @var int[] IDs of connections */
				$registered_products = $license_data['registered_connections'] ?? [];
				$registered_products_limit = $license_data['connection_limit'] ?? 0;

				$product_type = 'connection';
				break;

			case 'shop':
				$suite_name = 'Gravity Shop';
				$registered_products = null;
				$registered_products_limit = null;

				$product_type = 'plugin';
				break;

			default:
				return null;
		}

		$base_info = array(
			'suite_name' => $suite_name,
			'plugin_name' => $product->name,
			'type' => $product_type,
			'product' => $product,
			'license_data' => $license_data,
		);

		if ( $license_data['license'] === 'invalid' ) {
			return array_merge( $base_info, array(
				'code' => 'invalid_or_missing_license',
			) );
		}

		// If the product is not registered, show a message to register the plugin (not suite) in Spellbook.
		$is_registered = $registered_products_limit === 0 || $registered_products === null || in_array( $product->ID, $registered_products );

		if ( ! $is_registered ) {
			return array_merge( $base_info, array(
				'code' => 'unregistered_product',
			) );
		}

		if ( $license_data['license'] === 'expired' ) {
			return array_merge( $base_info, array(
				'code' => 'expired_license',
			) );
		}

		return true;
	}
}
