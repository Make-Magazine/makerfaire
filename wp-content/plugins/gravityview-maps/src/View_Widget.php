<?php

namespace GravityKit\GravityMaps;

use GV\Widget;

/**
 * Widget to display page links
 */
class View_Widget extends Widget {
	public $icon = 'dashicons-location-alt';

	protected $show_on_single = false;

	function __construct() {
		$this->widget_description = __( 'Display the visible entries in a map', 'gk-gravitymaps' );

		$default_values = array( 'header' => 1, 'footer' => 1 );

		$settings = array();

		parent::__construct( __( 'Multiple Entries Map', 'gk-gravitymaps' ), 'map', $default_values, $settings );
	}

	/**
	 * Trigger the `gravityview_map_render_div` action to render the map widget container
	 *
	 * Don't render the map if using DataTables layout
	 *
	 * @see Render_Map::render_map_div
	 *
	 * @param                             $widget_args
	 * @param string                      $content
	 * @param string|\GV\Template_Context $context
	 *
	 * @return void
	 */
	public function render_frontend( $widget_args, $content = '', $context = '' ) {
		global $post;

		if ( $post && 'datatables_table' === gravityview_get_template_id( $post->ID ) ) {
			do_action( 'gravityview_log_error', 'Map not shown: the map widget does not currently work with the DataTables layout' );

			return;
		}

		/**
		 * @since 1.6.2 added $context parameter
		 *
		 * @param array                       $entry   Entry being rendered. Not used here.
		 * @param string|\GV\Template_Context $context Current context, if set. Otherwise, empty string.
		 */
		do_action( 'gravityview_map_render_div', null, $context );
	}
}
