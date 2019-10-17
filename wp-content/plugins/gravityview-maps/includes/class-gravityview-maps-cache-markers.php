<?php
/**
 * GravityView Maps Extension - Cache Markers position (Lat / Long)
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */

class GravityView_Maps_Cache_Markers extends GravityView_Maps_Component {

	/**
	 * @since 1.0.4-beta
	 * @var array
	 */
	var $cached_meta = array( 'lat', 'long' );

	function load() {
		// Flush the cache if needed
		add_action( 'gform_after_update_entry', array( $this, 'flush_cache' ), 10, 2 );

	}

	/**
	 * Get the cached position for a given address field ID of a specified entry ID
	 *
	 * @param $entry_id string GF entry ID
	 * @param $field_id string GF field ID
	 *
	 * @return array
	 */
	public function get_cache_position( $entry_id, $field_id ) {

		$position = array();

		foreach( $this->cached_meta as $k => $key ) {

			$meta = gform_get_meta( $entry_id, self::get_meta_key( $key, $field_id ) );

			if( !empty( $meta ) ) {
				$position[] = $meta;
			} else {
				break;
			}

		}

		$position = array_filter( $position );

		return empty( $position ) ? false : $position;
	}

	/**
	 * Cache the Lat / Long associated to a given address field ID of a given entry ID
	 *
	 * @param $entry_id string GF entry ID
	 * @param $field_id string GF field ID
	 * @param $position array Contains the Latitude and Longitude
	 */
	public function set_cache_position( $entry_id, $field_id, $position ) {

		if ( empty( $position[0] ) || empty( $position[1] ) ) {
			return;
		}

		foreach( $this->cached_meta as $k => $key ) {
			gform_update_meta( $entry_id, self::get_meta_key( $key, $field_id ) , $position[ $k ] );
		}

	}

	/**
	 * In case entry is updated, delete the cached position
	 * todo: before deleting all the positions meta check if the address changed.
	 *
	 * @param array $form
	 * @param int $entry_id
	 */
	public function flush_cache( $form, $entry_id ) {

		$fields = GFAPI::get_fields_by_type( $form, array( 'address' ) );

		/** @var GF_Field $field */
		foreach ( $fields as $field ) {

			foreach ( $this->cached_meta as $key ) {
				gform_delete_meta( $entry_id, self::get_meta_key( $key, $field->id ) );
			}
		}

	}

	/**
	 * Get the meta key for the stored data.
	 *
	 * @param string $key Name of data stored
	 * @param $field_id
	 *
	 * @return string Meta key used to store data using Gravity Forms Entry Meta
	 */
	private static function get_meta_key( $key, $field_id ) {
		return sanitize_title( 'gvmaps_' . $key . '_' . $field_id );
	}

}