<?php

/**
 * GravityView_Default_Template_List class.
 * Defines List (default) template
 */
class GravityView_Maps_Template_Map_Default extends GravityView_Template {


	function __construct( $id = 'map', $settings = array(), $field_options = array(), $areas = array() ) {

		/**
		 * @global GravityView_Maps_Loader $gravityview_maps
		 */
		global $gravityview_maps;

		$map_settings = array(
			'slug' => 'map',
			'type' => 'custom',
			'label' =>  __( 'Map (default)', 'gravityview-maps' ),
			'description' => __( 'Display entries on a map.', 'gravityview-maps' ),
			'logo' => plugins_url( 'includes/presets/default-map/default-map.png', $gravityview_maps->plugin_file ),
			'css_source' => plugins_url( 'templates/css/map-view.css', $gravityview_maps->plugin_file ),
		);

		$settings = wp_parse_args( $settings, $map_settings );

		$field_options = array(
			'show_as_link' => array(
				'type' => 'checkbox',
				'label' => __( 'Link to single entry', 'gravityview-maps' ),
				'value' => false,
				'context' => 'directory'
			),
		);

		$areas['directory'] = array(
			array(
				'1-3' => array(
					array(
						'areaid' => 'map-image',
						'title' => __( 'Image', 'gravityview-maps' ) ,
						'subtitle' => ''
					),
				),
			),
			array(
				'1-3' => array(
					array(
						'areaid' => 'map-title',
						'title' => __( 'Listing Title', 'gravityview-maps' ),
						'subtitle' => ''
					),
				),
			),
			array(
				'1-3' => array(
					array(
						'areaid' => 'map-details',
						'title' => __( 'Details', 'gravityview-maps' ),
						'subtitle' => ''
					),
				),
			),
			array(
				'1-1' => array(
					array(
						'areaid' => 'map-middle',
						'title' => __( 'Middle row', 'gravityview-maps' ),
						'subtitle' => '',
					),
				),
			),
			array(
				'1-1' => array(
					array(
						'areaid' => 'map-footer',
						'title' => __( 'Footer', 'gravityview-maps' ),
						'subtitle' => '',
					),
				),
			),
		);

		$areas['single'] = array(
			array(
				'1-1' => array(
					array(
						'areaid' => 'map-title',
						'title' => __( 'Title', 'gravityview-maps' ) ,
						'subtitle' => ''
					),
					array(
						'areaid' => 'map-subtitle',
						'title' => __( 'Subheading', 'gravityview-maps' ) ,
						'subtitle' => 'Data placed here will be bold.'
					),
				),
				'1-3' => array(
					array(
						'areaid' => 'map-image',
						'title' => __( 'Image', 'gravityview-maps' ) ,
						'subtitle' => 'Leave empty to remove.'
					)
				),
				'2-3' => array(
					array(
						'areaid' => 'map-description',
						'title' => __( 'Other Fields', 'gravityview-maps' ) ,
						'subtitle' => 'Below the subheading, a good place for description and other data.'
					)
				)
			),
			array(
				'1-2' => array(
					array(
						'areaid' => 'map-footer-left',
						'title' => __( 'Footer Left', 'gravityview-maps' ) ,
						'subtitle' => ''
					)
				),
				'2-2' => array(
					array(
						'areaid' => 'map-footer-right',
						'title' => __( 'Footer Right', 'gravityview-maps' ) ,
						'subtitle' => ''
					)
				)
			)
		);


		parent::__construct( $id, $settings, $field_options, $areas );

	}

}