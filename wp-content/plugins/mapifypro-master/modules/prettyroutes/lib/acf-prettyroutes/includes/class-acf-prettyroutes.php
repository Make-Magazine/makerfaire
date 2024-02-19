<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://mapifypro.com/
 * @since      1.0.0
 *
 * @package    Acf_Prettyroutes
 * @subpackage Acf_Prettyroutes/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Acf_Prettyroutes
 * @subpackage Acf_Prettyroutes/includes
 * @author     Haris Ainur Rozak <https://support.mapifypro.com/>
 */
class Acf_Prettyroutes {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Acf_Prettyroutes_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ACF_PRETTYROUTES_VERSION' ) ) {
			$this->version = ACF_PRETTYROUTES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'acf-prettyroutes';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Acf_Prettyroutes_Loader. Orchestrates the hooks of the plugin.
	 * - Acf_Prettyroutes_i18n. Defines internationalization functionality.
	 * - Acf_Prettyroutes_Admin. Defines all hooks for the admin area.
	 * - Acf_Prettyroutes_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acf-prettyroutes-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acf-prettyroutes-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-prettyroutes-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-acf-prettyroutes-public.php';

		/**
		 * The model class that responsible for holding PrettyRoutes meta-field operations
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/model/class-prettyroutes-meta-field.php';

		/**
		 * The class responsible for customizing the Routes's `Waypoints` ACF field.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-prettyroutes-routes-waypoints.php';

		/**
		 * The class responsible for customizing the `Route Map` ACF field.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-prettyroutes-route-map.php';

		/**
		 * Load ACF fields settings
		 * Both fields-groups will be loaded on a different post types, which is `route` and `route_map`
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/prettyroutes-acf-route-options.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/prettyroutes-acf-route-map-options.php';

		$this->loader = new Acf_Prettyroutes_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Acf_Prettyroutes_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Acf_Prettyroutes_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Acf_Prettyroutes_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// register acf fields
		$this->loader->add_action( 'acf/include_field_types', $plugin_admin, 'include_fields' ); // ACF v5

		// customizing the Routes's `Waypoints` ACF field.
		$routes_waypoints = new Acf_Prettyroutes_Routes_Waypoints();
		$this->loader->add_filter( 'acf/load_value/key=' . $routes_waypoints->acf_field_key, $routes_waypoints, 'acf_load_value', 10, 3 );
		$this->loader->add_action( 'acf/save_post', $routes_waypoints, 'acf_update_value', 5 );

		// customizing the `Route Map` ACF field
		$route_map = new Acf_Prettyroutes_Route_Map();
		$this->loader->add_filter( 'acf/load_value/key=' . $route_map->acf_field_key, $route_map, 'acf_load_value', 10, 3 );
		$this->loader->add_action( 'acf/save_post', $route_map, 'acf_update_value', 5 );

		// Handle ajax action: create_connected_route
		$this->loader->add_action( 'wp_ajax_create_connected_route', $plugin_admin, 'ajax_create_connected_route' );
		$this->loader->add_action( 'wp_ajax_nopriv_create_connected_route', $plugin_admin, 'ajax_create_connected_route' );
		
		// Handle action: create_connected_route_on_next_reload
		$this->loader->add_action( 'current_screen', $plugin_admin, 'create_connected_route_on_next_reload' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Acf_Prettyroutes_Public( $this->get_plugin_name(), $this->get_version() );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Acf_Prettyroutes_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
