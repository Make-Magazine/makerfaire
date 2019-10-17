<?php
/**
 * GravityView Maps Extension - Settings class
 * Adds a general setting to the GravityView settings screen
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */

class GravityView_Maps_Settings extends GravityView_Maps_Component {


	function load() {

		add_action( 'gravityview/settings/extension/sections', array( $this, 'register_settings' ), 10, 1 );

	}


	/**
	 * Add GravityView Maps settings
	 */
	function register_settings( $sections ) {

		$settings = array();

		$settings[] = array(
			'name'        => 'googlemaps-api-key',
			'type'      => 'text',
			'default_value'   => defined( 'GRAVITYVIEW_GOOGLEMAPS_KEY' ) ? GRAVITYVIEW_GOOGLEMAPS_KEY : '',
			'class'    => 'regular-text',
			'label'     => __( 'Google Maps API Key', 'gravityview-maps' ),
			'description'  => '<a href="http://docs.gravityview.co/article/306-signing-up-for-a-google-maps-api-key">' . sprintf( esc_html__( 'How to get a %s', 'gravityview-maps' ), __( 'Google Maps API Key', 'gravityview-maps' ) ) . '</a>',
		);

		$settings[] = array(
			'name'        => 'googlemapsbusiness-api-clientid',
			'type'      => 'text',
			'default_value'   => defined( 'GRAVITYVIEW_GOOGLEBUSINESSMAPS_CLIENTID' ) ? GRAVITYVIEW_GOOGLEBUSINESSMAPS_CLIENTID : '',
			'class'    => 'regular-text',
			'label'     => __( 'Google Maps API for Work Client ID', 'gravityview-maps' ),
			'tooltip'  => sprintf( __( 'Read more about %sGoogle Maps API for Work%s  and learn how to obtain your key.', 'gravityview-maps' ), '<a href="https://developers.google.com/maps/documentation/business/">', '</a>' ) ,
		);

		$settings[] = array(
			'name'        => 'googlemapsbusiness-api-key',
			'type'      => 'text',
			'default_value'   => defined( 'GRAVITYVIEW_GOOGLEBUSINESSMAPS_KEY' ) ? GRAVITYVIEW_GOOGLEBUSINESSMAPS_KEY : '',
			'class'    => 'regular-text',
			'label'     => __( 'Google Maps API for Work Key', 'gravityview-maps' ),
			'tooltip'  => sprintf( __( 'Read more about %sGoogle Maps API for Work%s  and learn how to obtain your key.', 'gravityview-maps' ), '<a href="https://developers.google.com/maps/documentation/business/">', '</a>' ) ,
		);

		$settings[] = array(
			'name'        => 'bingmaps-api-key',
			'type'      => 'text',
			'default_value'   => defined( 'GRAVITYVIEW_BING_KEY' ) ? GRAVITYVIEW_BING_KEY : '',
			'class'    => 'regular-text',
			'label'     => __( 'Bing Maps Locations API Key', 'gravityview-maps' ),
			'tooltip'  => '',
			'description'  => '<a href="http://docs.gravityview.co/article/307-signing-up-for-a-bing-maps-api-key">' . sprintf( esc_html__( 'How to get a %s', 'gravityview-maps' ), __( 'Bing Maps Locations API Key', 'gravityview-maps' ) ) . '</a>',
		);

		$settings[] = array(
			'name'        => 'mapquest-api-key',
			'type'      => 'text',
			'default_value'   => defined( 'GRAVITYVIEW_MAPQUEST_KEY' ) ? GRAVITYVIEW_MAPQUEST_KEY : '',
			'class'    => 'regular-text',
			'label'     => __( 'MapQuest Geocoding API Key', 'gravityview-maps' ),
			'description'  => '<a href="http://docs.gravityview.co/article/305-signing-up-for-a-mapquest-geocoding-api-key">' . sprintf( esc_html__( 'How to get a %s', 'gravityview-maps' ), __( 'MapQuest Geocoding API Key', 'gravityview-maps' ) ) . '</a>',
		);

		// register section
		$sections[] = array(
			'title' => __( 'GravityView Maps Extension Settings', 'gravityview-maps' ),
			'description' => wpautop( sprintf( esc_html__('GravityView will attempt to convert addresses into longitude and latitude values. This process is called geocoding, and is required to display entries on a map. To ensure entries are geocoded, sign up for one or more of the free services below. %sLearn more about GravityView Maps geocoding.%s', 'gravityview-maps' ) , '<a href="http://docs.gravityview.co/article/304-setting-up-geocoding-services">', '</a>' ) ),
			'fields' => $settings,
		);

		return $sections;
	}

}