<?php

/**
 * Widget to display page links
 *
 * @extends GravityView_Widget
 */
class GravityView_Maps_View_Widget extends GravityView_Widget {

	protected $show_on_single = false;

	function __construct() {

		$this->widget_description = __( 'Display the visible entries in a map', 'gravityview-maps' );

		$default_values = array( 'header' => 1, 'footer' => 1 );

		$settings = array();

		parent::__construct( __( 'Multiple Entries Map', 'gravityview-maps' ) , 'map', $default_values, $settings );

	}

	/**
	 * Trigger the `gravityview_map_render_div` action to render the map widget container
	 *
	 * Don't render the map if using DataTables layout
	 *
	 * @see GravityView_Maps_Render_Map::render_map_div
	 *
	 * @param $widget_args
	 * @param string $content
	 * @param string $context
	 *
	 * @return void
	 */
	public function render_frontend( $widget_args, $content = '', $context = '') {
		global $post;

		if( $post && 'datatables_table' === gravityview_get_template_id( $post->ID ) ) {
			do_action( 'gravityview_log_error', 'Map not shown: the map widget does not currently work with the DataTables layout' );
			return;
		}

		do_action( 'gravityview_map_render_div' );
	}

}