<?php
/**
 * Gravity Forms Geolocation field group class.
 *
 * @package gravityforms-geolocation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register geolocation fields group.
 */
class GFGEO_Fields_Group extends GF_Field {

	/**
	 * Field group type.
	 *
	 * @var string
	 */
	public $type = 'gfgeo_group_field';

	/**
	 * Button fields and group.
	 *
	 * @param [type] $field_groups [description].
	 */
	public function add_button( $field_groups ) {

		// Geolocation fields group.
		$field_groups[] = array(
			'name'   => 'gfgeo_geolocation_fields',
			'label'  => __( 'Geolocation Fields', 'gfgeo' ),
			'fields' => apply_filters( 'gfgeo_field_buttons', array(), $field_groups ),
		);

		return $field_groups;
	}
}
GF_Fields::register( new GFGEO_Fields_Group() );
