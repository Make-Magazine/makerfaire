<?php
/**
 * Components loader.
 *
 * @package   GravityView_Ratings_Reviews
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 0.1.0
 */

defined( 'ABSPATH' ) || exit;

class GravityView_Ratings_Reviews_Loader extends GravityView_Extension {

	/**
	 * Name of the plugin, used to fetch updates from GravityView.co
	 *
	 * @see GravityView_Extension::settings()
	 *
	 * @var string
	 */
	protected $_title = 'Ratings & Reviews';

	/**
	 * Minimum version of GravityView required to use the extension
	 *
	 * @see GravityView_Extension::is_extension_supported()
	 *
	 * @var string
	 */
	protected $_min_gravityview_version = '2.0';

	/**
	 * @var int The Download ID on gravityview.co
	 */
	protected $_item_id = 34;

	/**
	 * Translation textdomain passed to GravityView_Extension to handle loading language files
	 *
	 * @see GravityView_Extension::load_plugin_textdomain()
	 *
	 * @var string
	 */
	protected $_text_domain = 'gravityview-ratings-reviews';

	/**
	 * Components of this extension.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	protected $components = array(
		'post-bridge',
		'fields',
		'meta-box',
		'meta-box-edit-review',
		'recalculate-ratings',
		'review',
		'form-fields',
		'sorting',
	);

	/**
	 * @var string Version of the plugin
	 */
	public $_version;

	/**
	 * @var string Main plugin `__FILE__` path
	 */
	protected $_path;

	/**
	 * @var string Path to the plugin directory
	 */
	public $dir;

	/**
	 * @var string Path to the /includes/ directory
	 */
	public $includes_dir;

	/**
	 * @var string Path to the /templates/ directory
	 */
	public $templates_dir;

	/**
	 * Component instances.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	public $component_instances = array();

	/**
	 * Constructor.
	 *
	 * Set properties and load components.
	 *
	 * @since 0.1.0
	 *
	 * @param string $plugin_file
	 * @param string $plugin_version
	 *
	 * @return void
	 */
	public function __construct( $plugin_file, $plugin_version ) {

		// Properties for GravityView_Extension.
		$this->_version = $plugin_version;
		$this->_path    = $plugin_file;

		parent::__construct();
	}

	/**
	 * Called by parent's constructor if extension is supported.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function add_hooks() {
		$this->set_properties();
		$this->load_components();
	}

	/**
	 * Set properties of this extension that will be useful for components.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function set_properties() {
		// Directories.
		$this->dir           = trailingslashit( plugin_dir_path( $this->_path ) );
		$this->includes_dir  = trailingslashit( $this->dir . 'includes' );
		$this->templates_dir = trailingslashit( $this->dir . 'templates' );

		// URLs.
		$this->url     = trailingslashit( plugin_dir_url( $this->_path ) );
		$this->js_url  = trailingslashit( $this->url . 'assets/js' );
		$this->css_url = trailingslashit( $this->url . 'assets/css' );
	}

	/**
	 * Loads components.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function load_components() {
		// Loads the abstract component before loading each component.
		require_once $this->includes_dir . 'class-component.php';
		require_once $this->includes_dir . 'class-helper.php';
		require_once $this->includes_dir . 'fields/rate_entry.php';
		require_once $this->includes_dir . 'fields/stars.php';
		require_once $this->includes_dir . 'fields/votes.php';


		// Loads each known components of this extension.
		foreach ( $this->components as $component ) {
			$filename  = $this->includes_dir . 'class-' . $component . '.php';
			$classname = 'GravityView_Ratings_Reviews_' . str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $component ) ) );

			// Loads component and pass extension's instance so that component can
			// talk each other.
			require_once $filename;
			$this->component_instances[ $component ] = new $classname( $this );
			$this->component_instances[ $component ]->load();
		}
	}

	/**
	 *
	 * @since 1.2
	 *
	 * @param string $file_name
	 * @param bool|true $load
	 * @param bool|true $require_once
	 *
	 * @return null|string
	 */
	public function locate_template( $file_name = '', $load = false, $require_once = true ) {

		if( ! defined('GRAVITYVIEW_DIR') ) {
			if( file_exists( $this->templates_dir . $file_name ) ) {

				if( $load ) {
					if( $require_once ) {
						require_once $this->templates_dir . $file_name;
					} else {
						require $this->templates_dir . $file_name;
					}
				}

				return $this->templates_dir . $file_name;
			}
		}

		if( !class_exists( 'GravityView_View' ) ) {
			include_once( GRAVITYVIEW_DIR .'includes/class-template.php' );
		}

		return GravityView_View::getInstance()->locate_template( $file_name, $load, $require_once );
	}

	/**
	 * Returns the plugin title.
	 * @since $ver$
	 * @return string
	 */
	public function getTitle() {
		return $this->_title ? $this->_title : '';
	}
}
