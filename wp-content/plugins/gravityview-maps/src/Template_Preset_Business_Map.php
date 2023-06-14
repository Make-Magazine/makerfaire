<?php

namespace GravityKit\GravityMaps;

/**
 * Defines default (list) template for the Business Map View
 */
class Template_Preset_Business_Map extends Template_Map_Default {
	function __construct() {
		/**
		 * @global Loader $gravityview_maps
		 */
		global $gravityview_maps;

		$id = 'preset_business_map';

		$settings = array(
			'slug'          => 'map',
			'type'          => 'preset',
			'label'         => __( 'Business Map Listing', 'gk-gravitymaps' ),
			'description'   => __( 'Display business profiles pinned in a map.', 'gk-gravitymaps' ),
			'logo'          => plugins_url( 'src/presets/business-map/logo-business-map.png', $gravityview_maps->plugin_file ),
			'preview'       => 'http://demo.gravityview.co/blog/view/business-map/',
			'preset_form'   => $gravityview_maps->dir . 'includes/presets/business-map/form-business-map.xml',
			'preset_fields' => $gravityview_maps->dir . 'includes/presets/business-map/fields-business-map.xml'
		);

		parent::__construct( $id, $settings );
	}
}
