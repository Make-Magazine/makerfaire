<?php

/**
 * The class responsible for customizing the Map Location `Links` ACF field.
 * 
 * @since    1.0.0
 */

use Acf_Mapifypro\Model\Mapify_Meta_Field;

/**
 * class Acf_Mapifypro_Map_Location_Links
 * 
 * @since    1.0.0
 */
class Acf_Mapifypro_Map_Location_Links {

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
		$this->acf_field_key      = 'mapify_acf_field_606ad1187c879';
		$this->link_url_prefix    = '_map_location_links_-_url_';
		$this->link_text_prefix   = '_map_location_links_-_text_';
		$this->link_target_prefix = '_map_location_links_-_target_';
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
		$data_url    = Mapify_Meta_Field::get_repeater_meta( $post_id, $this->link_url_prefix );
		$data_text   = Mapify_Meta_Field::get_repeater_meta( $post_id, $this->link_text_prefix );
		$data_target = Mapify_Meta_Field::get_repeater_meta( $post_id, $this->link_target_prefix );

		// get field key
		$key_url     = $field['sub_fields'][0]['key'];
		$key_text    = $field['sub_fields'][1]['key'];
		$key_target  = $field['sub_fields'][2]['key'];
		
		foreach ( $data_url as $key => $value ) {
			$field_value[ $key ][ $key_url ]    = $data_url[ $key ];
			$field_value[ $key ][ $key_text ]   = $data_text[ $key ];
			$field_value[ $key ][ $key_target ] = $data_target[ $key ];
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

		// save the repeater data
		if ( ! is_array( $field_value ) ) {
			$values_url    = array();
			$values_text   = array();
			$values_target = array();
		} else {
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
				$values_url    = $simplified_values[0];
				$values_text   = $simplified_values[1];
				$values_target = $simplified_values[2];				
			}
		}

		// save to post meta
		Mapify_Meta_Field::set_repeater_meta( $post_id, $values_url, $this->link_url_prefix );	
		Mapify_Meta_Field::set_repeater_meta( $post_id, $values_text, $this->link_text_prefix );	
		Mapify_Meta_Field::set_repeater_meta( $post_id, $values_target, $this->link_target_prefix );	
	}

}