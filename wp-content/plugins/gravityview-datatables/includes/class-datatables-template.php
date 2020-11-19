<?php
/**
 * GravityView Extension -- DataTables -- Template
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    GravityView <hello@gravityview.co>
 * @link      http://gravityview.co
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 1.0.4
 */



/**
 * GravityView_Default_Template_Table class.
 * Defines Table(DataTables) template
 */

if( class_exists( 'GravityView_Template' ) ) {

class GravityView_DataTables_Template extends GravityView_Template {

	function __construct( $id = 'datatables_table', $settings = array(), $field_options = array(), $areas = array() ) {

		if( empty( $settings ) ) {
			$settings = array(
				'slug' => 'table-dt',
				'type' => 'custom',
				'label' =>  __( 'DataTables Table', 'gv-datatables' ),
				'description' => __('Display items in a dynamic table powered by DataTables.', 'gv-datatables'),
				'logo' => plugins_url('assets/images/logo-datatables.png', GV_DT_FILE ),

				/**
				 * @filter `gravityview_datatables_style_src` Override the GravityView datatables.css CSS file and provide your own
				 * @param string $css_source URL to the datatables.css file
				 */
				'css_source' => apply_filters( 'gravityview_datatables_style_src', plugins_url( 'assets/css/datatables.css', GV_DT_FILE ) ),
			);
		}

		/**
		 * @see  GravityView_Admin_Views::get_default_field_options() for Generic Field Options
		 * @var array
		 */
		$field_options = array(
			'show_as_link' => array(
				'type' => 'checkbox',
				'label' => __( 'Link to single entry', 'gv-datatables' ),
				'value' => false,
				'context' => 'directory'
			),
		);

		$areas = array(
			array(
				'1-1' => array(
					array(
						'areaid' => 'table-columns',
						'title' => __('Visible Table Columns', 'gv-datatables' ) ,
						'subtitle' => ''
					)
				)
			)
		);


		parent::__construct( $id, $settings, $field_options, $areas );

	}

}
new GravityView_DataTables_Template;

} // if class_exists
