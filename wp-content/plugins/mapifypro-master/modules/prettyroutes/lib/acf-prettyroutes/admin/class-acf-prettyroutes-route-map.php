<?php

/**
 * The class responsible for customizing the Map Location `Links` ACF field.
 * 
 * @since    1.1.0
 */

/**
 * class Acf_Prettyroutes_Route_Map
 * 
 * @since    1.1.0
 */
class Acf_Prettyroutes_Route_Map {

	/**
	 * ACF field key
	 * 
	 * @since    1.1.0
	 * @var      string
	 */
	public $acf_field_key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.1.0
	 */
	public function __construct() {
		$this->acf_field_key = 'prettyroutes_acf_field_60ade50d7a35d';
	}

	/**
	 * ACF filter acf/load_value
	 * 
	 * @since    1.1.0
	 * @param    mixed    $field_value    The field value.
	 * @param    int      $post_id        The post ID where the value is saved.
	 * @param    array    $field          The field array containing all settings.
	 * @return   mixed
	 */
	public function acf_load_value( $field_value, $post_id, $field ) {
		if ( is_array( $field_value ) && isset( $field_value[0] ) ) {
			$field_value = $field_value[0];
		}
		
		return explode( ',', $field_value );
	}

	/**
	 * ACF action acf/save_post
	 * This hook's priority has been set to 5 (< 10) to run before ACF saves the value.
	 * 
	 * @since    1.1.0
	 * @param    int      $post_id    The post ID where the value is saved.
	 */
	public function acf_update_value( $post_id ) {
		if ( ! isset( $_POST['acf'][ $this->acf_field_key ] ) ) return;

		// get field value
		$field_value = $_POST['acf'][ $this->acf_field_key ];

		// override the data to save
		if ( is_array( $field_value ) ) {
			unset( $_POST['acf'][ $this->acf_field_key ] );
			update_post_meta( $post_id, '_route_map', implode( ',', $field_value ) );
		}
	}

}