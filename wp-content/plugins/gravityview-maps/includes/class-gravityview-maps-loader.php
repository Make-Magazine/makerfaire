<?php
/**
 * Components loader.
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 0.1.0
 */
class GravityView_Maps_Loader extends GravityView_Extension {

	protected $_min_gravityview_version = '1.7.4';

	/**
	 * Components of this extension.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	protected $components = array(
		'admin',
		'settings',
		'form-fields',
		'templates',
		'widgets',
		'fields',
		'cache-markers',
		'geocoding',
		'render-map',
		'available-icons'
	);

	/**
	 * Component instances.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	public $component_instances = array();

	/**
	 * @var string Full path to base plugin __FILE__
	 */
	public $_path = null;

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

		// Properties from GravityView_Extension.
		$this->_title       = 'Maps';
		$this->_version     = $plugin_version;
		$this->_text_domain = 'gravityview-maps';
		$this->_path        = $plugin_file;
		$this->_item_id         = 27;
		$this->_min_php_version = '5.3';

		$this->plugin_file    = $plugin_file;
		$this->plugin_version = $plugin_version;

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
		$this->dir           = trailingslashit( plugin_dir_path( $this->plugin_file ) );
		$this->includes_dir  = trailingslashit( $this->dir . 'includes' );
		$this->templates_dir = trailingslashit( $this->dir . 'templates' );

		// URLs.
		$this->url     = trailingslashit( plugin_dir_url( $this->plugin_file ) );
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

		/** @define "$includes_dir" "./" */
		$includes_dir = $this->includes_dir;

		// Loads the abstract component before loading each component.
		require_once $includes_dir . 'class-gravityview-maps-component.php';

		// Loads helpers & common functions
		require_once $includes_dir . 'class-gravityview-maps-data.php';
		require_once $includes_dir . 'class-gravityview-maps-icon.php';
		require_once $includes_dir . 'class-gravityview-maps-marker.php';
		require_once $includes_dir . 'class-gravityview-maps-infowindow.php';

		// Loads each known components of this extension.
		foreach ( $this->components as $component ) {
			$filename  = $includes_dir . 'class-gravityview-maps-' . $component . '.php';
			$classname = 'GravityView_Maps_' . str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $component ) ) );

			// Loads component and pass extension's instance so that component can
			// talk each other.
			require_once $filename;
			$this->component_instances[ $component ] = new $classname( $this );
			$this->component_instances[ $component ]->load();
		}
	}
}
