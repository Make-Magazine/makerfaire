<?php

/**
 * GravityView_Default_Template_List class.
 * Defines List (default) template
 */

class GravityView_Maps_Template_Preset_Business_Map extends GravityView_Maps_Template_Map_Default {

	function __construct() {

		/**
		 * @global GravityView_Maps_Loader $gravityview_maps
		 */
		global $gravityview_maps;

		$id = 'preset_business_map';

		$settings = array(
			'slug' => 'map',
			'type' => 'preset',
			'label' =>  __( 'Business Map Listing', 'gravityview-maps' ),
			'description' => __( 'Display business profiles pinned in a map.', 'gravityview-maps'),
			'logo' => plugins_url( 'includes/presets/business-map/logo-business-map.png', $gravityview_maps->plugin_file ),
			'preview' => 'http://demo.gravityview.co/blog/view/business-map/',
			'preset_form' => $gravityview_maps->dir . 'includes/presets/business-map/form-business-map.xml',
			'preset_fields' => $gravityview_maps->dir . 'includes/presets/business-map/fields-business-map.xml'
		);

		parent::__construct( $id, $settings );

	}

}
