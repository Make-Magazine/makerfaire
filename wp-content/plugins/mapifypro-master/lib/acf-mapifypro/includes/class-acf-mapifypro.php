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
 * @package    Acf_Mapifypro
 * @subpackage Acf_Mapifypro/includes
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
 * @package    Acf_Mapifypro
 * @subpackage Acf_Mapifypro/includes
 * @author     Haris Ainur Rozak <harisrozak@gmail.com>
 */
class Acf_Mapifypro {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Acf_Mapifypro_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'ACF_MAPIFYPRO_VERSION' ) ) {
			$this->version = ACF_MAPIFYPRO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'acf-mapifypro';

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
	 * - Acf_Mapifypro_Loader. Orchestrates the hooks of the plugin.
	 * - Acf_Mapifypro_i18n. Defines internationalization functionality.
	 * - Acf_Mapifypro_Admin. Defines all hooks for the admin area.
	 * - Acf_Mapifypro_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acf-mapifypro-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acf-mapifypro-i18n.php';
		
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-mapifypro-admin.php';
				
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-acf-mapifypro-public.php';
		
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
		 * The model class that responsible for map route data
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/model/class-mapify-map-route.php';
		
		/**
		 * The model class that responsible for holding mapify meta-field operations
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/model/class-mapify-meta-field.php';
		
		/**
		 * The model class that responsible for mapify-map-drawer data
		 * Displayed as "Map Areas" on admin sidebar
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/model/class-mapify-map-drawer.php';

		/**
		 * The model class that contains ACF API helper static functions 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/model/class-mapify-acf-api-helper.php';
		
		/**
		 * Responsible for the ACF library field's uploader
		 * This script based on Fegallery plugin by Haris
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acf-mapifypro-fegallery.php';

		/**
		 * The class responsible for customizing the `Front-End Search Radius Options` ACF field.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-mapifypro-search-radius-options.php';
		
		/**
		 * The class responsible for `Maps Tags` addition field below `Maps` ACF Relationship field.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-mapifypro-maps-tags.php';
		
		/**
		 * The class responsible for customizing the Map Location `Links` ACF field.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-mapifypro-map-location-links.php';
		
		/**
		 * The class responsible for customizing the Map `Routes` ACF field.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-mapifypro-map-routes.php';
		
		/**
		 * The class responsible for customizing the `Maps` ACF Relationship field.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-mapifypro-maps-relationship-field.php';

		/**
		 * The class responsible for creating and customizing MapifyPro settings page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-mapifypro-settings-page.php';
		
		/**
		 * The class responsible for customizing the `Multi Map` options page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-acf-mapifypro-multi-map.php';
		
		/**
		 * Load ACF fields settings
		 * Both fields-groups will be loaded on a different post types, which is `map` and `map-location`
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/mapify-acf-fields-map.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/mapify-acf-fields-map-location.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/mapify-acf-fields-mapifypro-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/mapify-acf-fields-location-tags.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/mapify-acf-fields-multi-map.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/mapify-acf-fields-map-drawer.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/acf-fields-settings/mapify-acf-fields-post.php';

		$this->loader = new Acf_Mapifypro_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Acf_Mapifypro_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Acf_Mapifypro_i18n();

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

		$plugin_admin = new Acf_Mapifypro_Admin( $this->get_plugin_name(), $this->get_version() );

		// register the stylesheets for the admin area
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

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
		
		// remove maptiles downloaded tiles on delete post
		$this->loader->add_action( 'before_delete_post', $plugin_admin, 'maptiles_delete_downloaded_tiles', 10, 2 );

		// customize `Front-End Search Radius Options` ACF field
		$search_radius_options = new Acf_Mapifypro_Search_Radius_Options();
		$this->loader->add_filter( 'acf/load_value/key=' . $search_radius_options->acf_field_key, $search_radius_options, 'acf_load_value', 10, 3 );
		$this->loader->add_action( 'acf/save_post', $search_radius_options, 'acf_update_value', 5 );

		// the class responsible for `Maps Tags` addition field below `Maps` ACF Relationship field
		$maps_tags = new Acf_Mapifypro_Maps_Tags( $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $maps_tags, 'enqueue_scripts' );
		$this->loader->add_action( 'acf/render_field/name=mapify_location_maps', $maps_tags, 'render_after_maps_field' );
		$this->loader->add_action( 'wp_ajax_acf_mapifypro_get_maps_tags', $maps_tags, 'ajax_get_maps_tags' );
		$this->loader->add_action( 'wp_ajax_nopriv_acf_mapifypro_get_maps_tags', $maps_tags, 'ajax_get_maps_tags' );

		// customizing the Map Location `Links` ACF field
		$map_location_links = new Acf_Mapifypro_Map_Location_Links();
		$this->loader->add_filter( 'acf/load_value/key=' . $map_location_links->acf_field_key, $map_location_links, 'acf_load_value', 10, 3 );
		$this->loader->add_action( 'acf/save_post', $map_location_links, 'acf_update_value', 5 );
		
		// customizing the Map `Routes` ACF field
		$map_routes = new Acf_Mapifypro_Map_Routes();
		$this->loader->add_filter( 'acf/load_value/key=' . $map_routes->acf_field_key, $map_routes, 'acf_load_value', 10, 3 );
		$this->loader->add_action( 'acf/save_post', $map_routes, 'acf_update_value', 5 );
		
		// customizing the `Maps` ACF Relationship field
		$maps_relationship_field = new Acf_Mapifypro_Maps_Relationship_Fields();
		$this->loader->add_filter( 'acf/load_value/key=' . $maps_relationship_field->acf_field_key, $maps_relationship_field, 'acf_load_value', 10, 3 );
		$this->loader->add_action( 'acf/save_post', $maps_relationship_field, 'acf_update_value', 5 );
		
		// creating and customizing MapifyPro settings page
		$setings_page = new Acf_Mapifypro_Settings_Page();
		$this->loader->add_action( 'init', $setings_page, 'init' );
		$this->loader->add_filter( 'acf/load_value/key=' . $setings_page->acf_field_key, $setings_page, 'acf_load_value', 10, 3 );
		$this->loader->add_action( 'acf/save_post', $setings_page, 'acf_update_value', 5 );

		// customizing the `Multi Map` options page.
		$multi_map = new Acf_Mapifypro_Multi_Map();
		$this->loader->add_action( 'acf/input/admin_head', $multi_map, 'acf_admin_head', 11 );
		$this->loader->add_action( 'admin_enqueue_scripts', $multi_map, 'enqueue_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Acf_Mapifypro_Public( $this->get_plugin_name(), $this->get_version() );

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
	 * @return    Acf_Mapifypro_Loader    Orchestrates the hooks of the plugin.
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
