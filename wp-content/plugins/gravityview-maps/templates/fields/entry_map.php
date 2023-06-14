<?php
/**
 * Field Entry Map template
 *
 * @since     1.0.0
 * @license   GPL2+
 * @author    GravityView <hello@gravityview.co>
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @package   GravityView_Maps
 */

$data = \GravityKit\GravityMaps\Data::get_instance( GravityView_View::getInstance() );

if( $markers = $data::get_markers() ) {
	/**
	 * @action `gravityview_map_render_div` Render the Map
	 *
	 * @param array $entry Gravity Forms entry object {@since 1.2}
	 */
	do_action( 'gravityview_map_render_div', GravityView_View::getInstance()->getCurrentEntry() );
}