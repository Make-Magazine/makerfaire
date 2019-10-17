<?php
/**
 * Register and Launch templates logic
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */
class GravityView_Maps_Templates extends GravityView_Maps_Component {

	/**
	 * Holds the view map settings
	 *
	 * @var array
	 *
	 */
	protected $map_view_settings;

	function load() {

		// Register the Maps View template type to GV core
		add_action( 'init', array( $this, 'register_map_template' ), 20 );

		// add template path to check for field
		add_filter( 'gravityview_template_paths', array( $this, 'add_template_path' ) );

		// Add map settings to runtime GravityView_View object
		add_action( 'gravityview_before', array( $this, 'set_map_settings' ), 10, 1 );

		// Render the layout
		add_action( 'gravityview_map_body_before', array( $this, 'render_layout' ), 10, 1 );
		add_action( 'gravityview_map_body_after', array( $this, 'render_layout' ), 10, 1 );

	}


	/**
	 * Include this extension templates path
	 *
	 * @param array $file_paths List of template paths ordered
	 */
	function add_template_path( $file_paths ) {

		// Index 100 is the default GravityView template path.
		$file_paths[ 133 ] = plugin_dir_path( $this->loader->_path ) .'templates';

		return $file_paths;
	}


	/**
	 * Register the Maps View template type to GV core
	 *
	 * @return void
	 */
	function register_map_template() {
		include_once $this->loader->includes_dir . 'class-gravityview-maps-template-map-default.php';
		include_once $this->loader->includes_dir . 'class-gravityview-maps-template-preset-business-map.php';

		new GravityView_Maps_Template_Map_Default;
		new GravityView_Maps_Template_Preset_Business_Map;
	}

	/**
	 * Add the Map Settings to the GravityView_View runtime instance
	 *
	 * @param $view_id
	 */
	public function set_map_settings( $view_id ) {
		$map_view_settings = GravityView_Maps_Admin::get_map_settings( $view_id, false );
		$atts = GravityView_View::getInstance()->getAtts();
		$this->map_view_settings = wp_parse_args( $map_view_settings, $atts );
		GravityView_View::getInstance()->setAtts( $this->map_view_settings );
	}

	/**
	 * Render the map layout parts according to the Map Settings
	 *
	 * @param GravityView_View $instance The GravityView_View instance
	 */
	public function render_layout( $instance ) {

		if( !empty( $this->map_view_settings['map_canvas_sticky'] ) ) {
			$instance->gv_maps_sticky_class = 'gv-map-sticky-container';
		}

		// before or after entries
		$zone = str_replace( 'gravityview_map_body_', '' , current_filter() );

		// map position layout (top, right, left or bottom)
		$pos = $this->map_view_settings['map_canvas_position'];

		// render template
		$instance->render( 'map-part', $pos .'-'. $zone, false );
		$instance->setTemplatePartSlug('map');
	}

}

