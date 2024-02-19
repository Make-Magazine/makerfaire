<?php

/**
 * The class responsible for customizing the `Maps` ACF Relationship field.
 * 
 * @since    1.0.0
 */

use Acf_Mapifypro\Model\Mapify_Map_Location;

/**
 * class Acf_Mapifypro_Maps_Relationship_Fields
 * 
 * @since    1.0.0
 */
class Acf_Mapifypro_Maps_Relationship_Fields {

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
		$this->acf_field_key = 'mapify_acf_field_604d81bbc44d5';
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
		$map_location = new Mapify_Map_Location( $post_id );
		return $map_location->get_map_ids();
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

		$map_ids      = $_POST['acf'][ $this->acf_field_key ];
		$map_location = new Mapify_Map_Location( $post_id );
		$map_location->set_map_ids( $map_ids );
	}

}