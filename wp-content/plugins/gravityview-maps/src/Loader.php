<?php

namespace GravityKit\GravityMaps;

/**
 * Components loader
 *
 * @since     0.1.0
 */
class Loader {
	/**
	 * Components of this extension.
	 *
	 * @since 0.1.0
	 *
	 * @var array
	 */
	protected $components = array(
		'Admin',
		'Settings',
		'Form_Fields',
		'Templates',
		'Widgets',
		'Fields',
		'Cache_Markers',
		'Geocoding',
		'Render_Map',
		'Available_Icons',
		'GF_Entry_Geocoding',
		'Custom_Map_Icons',
		'Search_Filter',
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
		$this->path           = $plugin_file;
		$this->plugin_file    = $plugin_file;
		$this->plugin_version = $plugin_version;

		$this->set_properties();

		add_action( 'init', array( $this, 'load_components' ) );
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
		$this->includes_dir  = trailingslashit( $this->dir . 'src' );
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
	public function load_components() {
		foreach ( $this->components as $component ) {
			$class = __NAMESPACE__ . '\\' . $component;

			$this->component_instances[ $component ] = new $class( $this );
			$this->component_instances[ $component ]->load();
		}
	}
}
