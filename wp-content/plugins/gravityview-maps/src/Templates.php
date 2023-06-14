<?php

namespace GravityKit\GravityMaps;

use GravityView_View;

/**
 * Handles templates logic
 *
 * @since 1.0.0
 */
class Templates extends Component {
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
		add_filter( 'gravityview/template/fields_template_paths', array( $this, 'add_template_path' ) );

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
		$file_paths[133] = plugin_dir_path( $this->loader->path ) . 'templates';

		return $file_paths;
	}

	/**
	 * Register the Maps View template type to GV core
	 *
	 * @return void
	 */
	function register_map_template() {
		new Template_Map_Default;
		new Template_Preset_Business_Map;
	}

	/**
	 * Add the Map Settings to the GravityView_View runtime instance
	 *
	 * @param $view_id
	 */
	public function set_map_settings( $view_id ) {
		$map_view_settings       = Admin::get_map_settings( $view_id, false );
		$atts                    = GravityView_View::getInstance()->getAtts();
		$this->map_view_settings = wp_parse_args( $map_view_settings, $atts );
		GravityView_View::getInstance()->setAtts( $this->map_view_settings );
	}

	/**
	 * Render the map layout parts according to the Map Settings
	 *
	 * @param GravityView_View $instance The GravityView_View instance
	 */
	public function render_layout( $instance ) {
		// Don't show the map widget if we're doing "Hide data until search"
		if ( $instance->isHideUntilSearched() && ! ( function_exists( 'gravityview' ) && gravityview()->request->is_search() ) ) {
			return;
		}

		if ( ! empty( $this->map_view_settings['map_canvas_sticky'] ) ) {
			$instance->gv_maps_sticky_class = 'gv-map-sticky-container';
		}

		// before or after entries
		$zone = str_replace( 'gravityview_map_body_', '', current_filter() );

		// map position layout (top, right, left or bottom)
		$pos = $this->map_view_settings['map_canvas_position'];

		// render template
		$instance->render( 'map-part', $pos . '-' . $zone, false );
		$instance->setTemplatePartSlug( 'map' );
	}

	/**
	 * Using the View Wrapper ID filters create the outermost wrapper for the Map View as an array.
	 *
	 * @link https://docs.gravitykit.com/article/867-modifying-the-view-container-div
	 *
	 * @since 2.0
	 *
	 * @return array
	 */
	public static function get_container_wrapper_pieces(): array {
		static $counter = 0;

		$view = \GV\View::by_id( gravityview_get_view_id() );
		if ( ! $view ) {
			return [];
		}

		$context = \GV\Template_Context::from_template( [ 'view' => $view ] );

		$counter++;
		$context->view->set_anchor_id( $counter );

		/**
		 * @filter `gravityview/view/wrapper_container` Modify the wrapper container.
		 * @since  2.15
		 *
		 * @param string   $wrapper_container Wrapper container HTML markup
		 * @param string   $anchor_id         (optional) Unique anchor ID to identify the view.
		 * @param \GV\View $view              The View.
		 */
		$wrapper_container = apply_filters(
			'gravityview/view/wrapper_container',
			'<div id="' . esc_attr( $context->view->get_anchor_id() ) . '">{content}</div>',
			$context->view->get_anchor_id(),
			$context->view
		);

		if ( empty( $wrapper_container ) ) {
			return [];
		}

		return explode( '{content}', $wrapper_container );
	}

	/**
	 * Based on the container wrapper pieces logic return the opening wrapper HTML tag.
	 *
	 * @uses static::get_container_wrapper_pieces()
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public static function get_container_wrapper_open(): string {
		$pieces = static::get_container_wrapper_pieces();

		if ( empty( $pieces ) ){
			return '';
		}

		return $pieces[0];
	}

	/**
	 * Based on the container wrapper pieces logic return the closing wrapper HTML tag.
	 *
	 * @uses static::get_container_wrapper_pieces()
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public static function get_container_wrapper_close(): string {
		$pieces = static::get_container_wrapper_pieces();

		if ( empty( $pieces ) ){
			return '';
		}

		return $pieces[1];
	}
}
