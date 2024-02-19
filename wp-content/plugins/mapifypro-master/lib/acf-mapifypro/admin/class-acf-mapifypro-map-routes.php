<?php

/**
 * The class responsible for customizing the Map `Routes` ACF field.
 * 
 * @since    1.0.0
 */

use Acf_Mapifypro\Model\Mapify_Map;

/**
 * class Acf_Mapifypro_Map_Routes
 * 
 * @since    1.0.0
 */
class Acf_Mapifypro_Map_Routes {

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
		$this->acf_field_key = 'mapify_acf_field_62317cf84bac5';
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
		// get route ids
		$mapify_map  = new Mapify_Map( $post_id );
		$field_value = $mapify_map->get_route_ids();

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
		$field_value = $_POST['acf'][ $this->acf_field_key ];
		$field_value = is_array( $field_value ) ? $field_value : array();
				
		// set map locations
		$mapify_map = new Mapify_Map( $post_id );
		$mapify_map->set_route_ids( $field_value );
	}

}