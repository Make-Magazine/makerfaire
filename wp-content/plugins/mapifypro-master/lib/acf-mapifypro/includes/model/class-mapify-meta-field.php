<?php

/**
 * The model class that responsible for holding mapify meta-field operations
 * 
 * @since    1.0.0
 */

namespace Acf_Mapifypro\Model;

/**
 * Class Mapify_Meta_Field
 * 
 * @since    1.0.0
 */
class Mapify_Meta_Field {

	/**
	 * Get mapify repeater meta value form database
	 * 
	 * @since    1.0.0
	 * @param    int      $post_id    WordPress custom post type post_id
	 * @return   array                Data values from database
	 * @static
	 */
	public static function get_repeater_meta( $post_id, $prefix ) {
		global $wpdb;

		$values = $wpdb->get_col(
			$wpdb->prepare( "
					SELECT meta_value
					FROM $wpdb->postmeta
					WHERE post_id = %d 
					AND meta_key LIKE %s
					ORDER BY meta_key ASC
				",
				$post_id,
				$wpdb->esc_like( $prefix ) . "%"
			)
		);

		// array of image url
		return $values ? $values : array();
	}

	/**
	 * Set mapify repeater meta value to database
	 * 
	 * @since    1.0.0
	 * @param    array     $post_id        WordPress custom post type post_id
	 * @param    array     $field_value    Data values to be saved to database
	 * @param    string    $prefix         Meta key prefix
	 * @static
	 */
	public static function set_repeater_meta( $post_id, $field_value, $prefix ) {
		if ( ! is_array( $field_value ) ) return;

		$mapify_meta_value = self::get_repeater_meta( $post_id, $prefix );
		
		// first, delete any unused record from database
		foreach ( $mapify_meta_value as $key => $value ) {
			if ( isset( $field_value[ $key ] ) ) continue;
			delete_post_meta( $post_id, $prefix . $key );
		}

		// then update (or insert) the remain data
		foreach ( $field_value as $key => $value ) {
			update_post_meta( $post_id, $prefix . $key, $value );
		}
	}

}