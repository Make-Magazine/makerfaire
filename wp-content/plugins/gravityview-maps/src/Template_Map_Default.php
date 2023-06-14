<?php

namespace GravityKit\GravityMaps;

use GravityView_Template;

/**
 * Defines default (list) template for the Map View
 */
class Template_Map_Default extends GravityView_Template {
	function __construct( $id = 'map', $settings = array(), $field_options = array(), $areas = array() ) {
		/**
		 * @global Loader $gravityview_maps
		 */
		global $gravityview_maps;

		$map_settings = array(
			'slug'        => 'map',
			'type'        => 'custom',
			'label'       => __( 'Map (default)', 'gk-gravitymaps' ),
			'description' => __( 'Display entries on a map.', 'gk-gravitymaps' ),
			'logo'        => plugins_url( 'src/presets/default-map/default-map.png', $gravityview_maps->plugin_file ),
			'css_source'  => plugins_url( 'templates/css/map-view.css', $gravityview_maps->plugin_file ),
		);

		$settings = wp_parse_args( $settings, $map_settings );

		$field_options = array(
			'show_as_link' => array(
				'type'    => 'checkbox',
				'label'   => __( 'Link to single entry', 'gk-gravitymaps' ),
				'value'   => false,
				'context' => 'directory'
			),
		);

		$areas['directory'] = array(
			array(
				'1-3' => array(
					array(
						'areaid'   => 'map-image',
						'title'    => __( 'Image', 'gk-gravitymaps' ),
						'subtitle' => ''
					),
				),
			),
			array(
				'1-3' => array(
					array(
						'areaid'   => 'map-title',
						'title'    => __( 'Listing Title', 'gk-gravitymaps' ),
						'subtitle' => ''
					),
				),
			),
			array(
				'1-3' => array(
					array(
						'areaid'   => 'map-details',
						'title'    => __( 'Details', 'gk-gravitymaps' ),
						'subtitle' => ''
					),
				),
			),
			array(
				'1-1' => array(
					array(
						'areaid'   => 'map-middle',
						'title'    => __( 'Middle row', 'gk-gravitymaps' ),
						'subtitle' => '',
					),
				),
			),
			array(
				'1-1' => array(
					array(
						'areaid'   => 'map-footer',
						'title'    => __( 'Footer', 'gk-gravitymaps' ),
						'subtitle' => '',
					),
				),
			),
		);

		$areas['single'] = array(
			array(
				'1-1' => array(
					array(
						'areaid'   => 'map-title',
						'title'    => __( 'Title', 'gk-gravitymaps' ),
						'subtitle' => ''
					),
					array(
						'areaid'   => 'map-subtitle',
						'title'    => __( 'Subheading', 'gk-gravitymaps' ),
						'subtitle' => 'Data placed here will be bold.'
					),
				),
				'1-3' => array(
					array(
						'areaid'   => 'map-image',
						'title'    => __( 'Image', 'gk-gravitymaps' ),
						'subtitle' => 'Leave empty to remove.'
					)
				),
				'2-3' => array(
					array(
						'areaid'   => 'map-description',
						'title'    => __( 'Other Fields', 'gk-gravitymaps' ),
						'subtitle' => 'Below the subheading, a good place for description and other data.'
					)
				)
			),
			array(
				'1-2' => array(
					array(
						'areaid'   => 'map-footer-left',
						'title'    => __( 'Footer Left', 'gk-gravitymaps' ),
						'subtitle' => ''
					)
				),
				'2-2' => array(
					array(
						'areaid'   => 'map-footer-right',
						'title'    => __( 'Footer Right', 'gk-gravitymaps' ),
						'subtitle' => ''
					)
				)
			)
		);

		parent::__construct( $id, $settings, $field_options, $areas );
	}
}
