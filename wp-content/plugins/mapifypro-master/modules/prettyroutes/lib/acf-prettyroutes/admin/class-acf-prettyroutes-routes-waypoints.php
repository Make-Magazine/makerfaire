<?php

/**
 * The class responsible for customizing the Routes's `Waypoints` ACF field.
 * 
 * @since    1.0.0
 */

use Acf_Prettyroutes\Model\Prettyroutes_Meta_Field;

/**
 * class Acf_Prettyroutes_Routes_Waypoints
 * 
 * @since    1.0.0
 */
class Acf_Prettyroutes_Routes_Waypoints {

	/**
	 * ACF field key
	 * 
	 * @since    1.0.0
	 * @var      string
	 */
	public $acf_field_key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->acf_field_key          = 'prettyroutes_acf_field_60a7763dc0835';
		$this->pin_enabled_prefix     = '_route_waypoints_-_pin_enabled_';
		$this->tooltip_enabled_prefix = '_route_waypoints_-_tooltip_enabled_';
		$this->tooltip_close_prefix   = '_route_waypoints_-_tooltip_close_';
		$this->tooltip_content_prefix = '_route_waypoints_-_tooltip_content_';
		$this->pin_prefix             = '_route_waypoints_-_pin_';
	}

	/**
	 * ACF filter acf/load_value
	 * 
	 * @since    1.0.0
	 * @param    mixed    $field_value    The field value.
	 * @param    int      $post_id        The post ID where the value is saved.
	 * @param    array    $field          The field array containing all settings.
	 * @return   mixed
	 */
	public function acf_load_value( $field_value, $post_id, $field ) {
		$field_value = array();
		
		// get meta data
		$pin_enabled     = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $this->pin_enabled_prefix );
		$tooltip_enabled = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $this->tooltip_enabled_prefix );
		$tooltip_close   = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $this->tooltip_close_prefix );
		$tooltip_content = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $this->tooltip_content_prefix );
		$pin             = Prettyroutes_Meta_Field::get_repeater_meta( $post_id, $this->pin_prefix );

		// get field key
		$key_pin_enabled     = $field['sub_fields'][0]['key'];
		$key_tooltip_enabled = $field['sub_fields'][1]['key'];
		$key_tooltip_close   = $field['sub_fields'][2]['key'];
		$key_tooltip_content = $field['sub_fields'][3]['key'];
		$key_pin             = $field['sub_fields'][4]['key'];
		
		foreach ( $pin_enabled as $key => $value ) {
			$field_value[ $key ][ $key_pin_enabled ]     = $pin_enabled[ $key ];
			$field_value[ $key ][ $key_tooltip_enabled ] = $tooltip_enabled[ $key ];
			$field_value[ $key ][ $key_tooltip_close ]   = $tooltip_close[ $key ];
			$field_value[ $key ][ $key_tooltip_content ] = $tooltip_content[ $key ];
			$field_value[ $key ][ $key_pin ]             = $pin[ $key ];
		}

		return $field_value;
	}

	/**
	 * ACF action acf/save_post
	 * This hook's priority has been set to 5 (< 10) to run before ACF saves the value.
	 * 
	 * @since    1.0.0
	 * @param    int      $post_id    The post ID where the value is saved.
	 */
	public function acf_update_value( $post_id ) {
		if ( ! isset( $_POST['acf'][ $this->acf_field_key ] ) ) return;

		// get field value
		$field_value       = $_POST['acf'][ $this->acf_field_key ];
		$simplified_values = array();

		// the value must be an array
		if ( ! is_array( $field_value ) ) return;
		
		/**
		 * Simplifying data value before processing
		 * I mean from something like this: { $data[row-x][field_xxx], $data[row-y][field_yyy] }
		 * to this: { $data[0], $data[1] }
		 */
		foreach ( $field_value as $parent => $childs ) {
			if ( ! is_array( $childs ) ) continue;	
			$index = 0;

			foreach ( $childs as $child_key => $child_value ) {
				$simplified_values[ $index ][] = $child_value;
				$index++;
			}
		}

		// update mapify meta value
		if ( ! empty( $simplified_values ) ) {
			$pin_enabled     = $simplified_values[0];
			$tooltip_enabled = $simplified_values[1];
			$tooltip_close   = $simplified_values[2];
			$tooltip_content = $simplified_values[3];
			$pin             = $simplified_values[4];

			// save to post meta
			Prettyroutes_Meta_Field::set_repeater_meta( $post_id, $pin_enabled, $this->pin_enabled_prefix );
			Prettyroutes_Meta_Field::set_repeater_meta( $post_id, $tooltip_enabled, $this->tooltip_enabled_prefix );
			Prettyroutes_Meta_Field::set_repeater_meta( $post_id, $tooltip_close, $this->tooltip_close_prefix );
			Prettyroutes_Meta_Field::set_repeater_meta( $post_id, $tooltip_content, $this->tooltip_content_prefix );
			Prettyroutes_Meta_Field::set_repeater_meta( $post_id, $pin, $this->pin_prefix );
		}
	}

}