<?php

/**
 * The class responsible for customizing the `Front-End Search Radius Options` ACF field.
 * 
 * @since    1.0.0
 */

use Acf_Mapifypro\Model\Mapify_Meta_Field;

/**
 * class Acf_Mapifypro_Search_Radius_Options
 * 
 * @since    1.0.0
 */
class Acf_Mapifypro_Search_Radius_Options {

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
		$this->acf_field_key      = 'mapify_acf_field_60644059bf1b7';
		$this->mapify_data_prefix = '_map_search_radius_options_-_value_';
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
		$field_value       = array();
		$mapify_meta_value = Mapify_Meta_Field::get_repeater_meta( $post_id, $this->mapify_data_prefix );
		$sub_field         = $field['sub_fields'][0]['key'];

		foreach ( $mapify_meta_value as $key => $value ) {
			$field_value[ $key ][ $sub_field ] = $value;
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
		$field_value      = $_POST['acf'][ $this->acf_field_key ];
		$simplified_value = array();

		// the value must be an array
		if ( is_array( $field_value ) ) {
			/**
			 * Simplifying data value before processing
			 * I mean from something like this: { $data[row-x][field_xxx], $data[row-y][field_yyy] }
			 * to this: { $data[0], $data[1] }
			 */
			foreach ( $field_value as $parent => $childs ) {
				if ( ! is_array( $childs ) ) continue;	
	
				foreach ( $childs as $child_key => $child_value ) {
					$simplified_value[] = $child_value;
				}
			}
		}

		// update mapify meta value
		Mapify_Meta_Field::set_repeater_meta( $post_id, $simplified_value, $this->mapify_data_prefix );
	}

}