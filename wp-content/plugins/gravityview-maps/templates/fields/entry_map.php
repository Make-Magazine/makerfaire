<?php
/**
* Field Entry Map template
*
* @package   GravityView_Maps
* @license   GPL2+
* @author    Katz Web Services, Inc.
* @link      http://gravityview.co
* @copyright Copyright 2015, Katz Web Services, Inc.
*
* @since 1.0.0
*/


$Data = new GravityView_Maps_Data( GravityView_View::getInstance() );

if( $Data->get_markers() ) {
	/**
	 * @action `gravityview_map_render_div` Render the Map
	 * @param array $entry Gravity Forms entry object {@since 1.2}
	 */
	do_action( 'gravityview_map_render_div', GravityView_View::getInstance()->getCurrentEntry() );
}