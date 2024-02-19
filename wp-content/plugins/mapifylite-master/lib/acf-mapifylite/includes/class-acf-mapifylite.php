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
 * @package    Acf_Mapifylite
 * @subpackage Acf_Mapifylite/includes
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
 * @package    Acf_Mapifylite
 * @subpackage Acf_Mapifylite/includes
 * @author     Haris Ainur Rozak <harisrozak@gmail.com>
 */
class Acf_Mapifylite {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Acf_Mapifylite_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'ACF_MAPIFYLITE_VERSION' ) ) {
			$this->version = ACF_MAPIFYLITE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'acf-mapifylite';

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
	 * - Acf_Mapifylite_Loader. Orchestrates the hooks of the plugin.
	 * - Acf_Mapifylite_i18n. Defines internationalization functionality.
	 * - Acf_Mapifylite_Admin. Defines all hooks for the admin area.
	 * - Acf_Mapifylite_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acf-mapifylite-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acf-mapifylite-i18n.php';
		
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-mapifylite-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-acf-mapifylite-public.php';
		
		/**
		 * The model class responsible for maptiles-uploader process
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/model/class-maptiles-uploader.php';
		
		/**
		 * The model class that responsible for mapify-map data
		 * Displayed as "Map Settings" on mapifyFree
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/model/class-mapify-map.php';
		
		/**
		 * The model class that responsible for mapify-map-location data
		 * Displayed as "Map Locations" on mapifyFree
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/model/class-mapify-map-location.php';
		
		/**
		 * Responsible for the ACF library field's uploader
		 * This script based on Fegallery plugin by Haris
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acf-mapifylite-fegallery.php';

		/**
		 * Load ACF fields settings
		 * Both fields-groups will be loaded on a different post types, which is `map` and `map-location`
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/mapify-acf-fields-map.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/mapify-acf-fields-map-location.php';

		$this->loader = new Acf_Mapifylite_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Acf_Mapifylite_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Acf_Mapifylite_i18n();

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

		$plugin_admin = new Acf_Mapifylite_Admin( $this->get_plugin_name(), $this->get_version() );

		// register acf fields
		$this->loader->add_action( 'acf/include_field_types', $plugin_admin, 'include_fields' ); // ACF v5

		// register admin ajax osm_maptiles_check_status
		$this->loader->add_action( 'wp_ajax_osm_maptiles_check_status', $plugin_admin, 'maptiles_check_status' ); /* for logged in user */
		$this->loader->add_action( 'wp_ajax_nopriv_osm_maptiles_check_status', $plugin_admin, 'maptiles_check_status' ); /* for non-logged in user */
		
		// register admin ajax osm_maptiles_create_job
		$this->loader->add_action( 'wp_ajax_osm_maptiles_create_job', $plugin_admin, 'maptiles_create_job' ); /* for logged in user */
		$this->loader->add_action( 'wp_ajax_nopriv_osm_maptiles_create_job', $plugin_admin, 'maptiles_create_job' ); /* for non-logged in user */

		// register admin ajax osm_maptiles_check_job_status
		$this->loader->add_action( 'wp_ajax_osm_maptiles_check_job_status', $plugin_admin, 'maptiles_check_job_status' ); /* for logged in user */
		$this->loader->add_action( 'wp_ajax_nopriv_osm_maptiles_check_job_status', $plugin_admin, 'maptiles_check_job_status' ); /* for non-logged in user */

		// register admin ajax osm_maptiles_download_tiles
		$this->loader->add_action( 'wp_ajax_osm_maptiles_download_tiles', $plugin_admin, 'maptiles_download_tiles' ); /* for logged in user */
		$this->loader->add_action( 'wp_ajax_nopriv_osm_maptiles_download_tiles', $plugin_admin, 'maptiles_download_tiles' ); /* for non-logged in user */

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Acf_Mapifylite_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

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
	 * @return    Acf_Mapifylite_Loader    Orchestrates the hooks of the plugin.
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
