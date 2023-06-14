<?php

namespace GravityKit\GravityMaps;

use Exception;
use GFFormsModel;
use GFAPI;

/**
 * Cache for position markers (lat/long)
 *
 * @since 1.0.0
 */
class Cache_Markers extends Component {
	/**
	 * @since 1.0.4-beta
	 * @var array
	 */
	var $cached_meta = array( 'lat', 'long' );

	/**
	 * @since 1.6
	 * @var string
	 */
	var $error_meta = 'error';

	function load() {
		// TODO: Add "delete cache" bulk menu to Gravity Forms Entries screen

		// Flush the cache if needed. Runs in both GF and GV Edit Entry screens.
		add_action( 'gform_after_update_entry', array( $this, 'flush_cache' ), 10, 3 );
	}

	/**
	 * Get the cached position for a given address field ID of a specified entry ID
	 *
	 * @param $entry_id string GF entry ID
	 * @param $field_id string GF field ID
	 *
	 * @return array|false Returns false if cache should not be used
	 * @todo In 2.0: Hydrate all entries in a View all at once with gform_get_meta_values_for_entries
	 *
	 */
	public function get_cache_position( $entry_id, $field_id ) {
		foreach ( $this->cached_meta as $k => $key ) {
			$cached_meta_keys[] = self::get_meta_key( $key, $field_id );
		}

		// First try the method that might be primed to avoid too many queries.
		$values = Markers::get( $entry_id );

		if ( ! $values ) {
			$values = gform_get_meta_values_for_entries( array( $entry_id ), $cached_meta_keys );
			$values = reset( $values );
		}

		$position = array();
		foreach ( $cached_meta_keys as $cached_meta_key ) {
			$position[] = $values->{$cached_meta_key};
		}

		$position = array_filter( $position );

		return empty( $position ) ? false : $position;
	}

	/**
	 * Gets the error for geocoding an entry field, if exists
	 *
	 * @since 1.6
	 *
	 * @param int $entry_id ID of entry being geocoded
	 * @param int $field_id ID of entry field being geocoded
	 *
	 * @return bool|string False if not exists, string of error message if set
	 */
	public function get_cache_error( $entry_id, $field_id ) {
		return gform_get_meta( $entry_id, self::get_meta_key( $this->error_meta, $field_id ) );
	}

	/**
	 * Sets the error for geocoding an entry field
	 *
	 * @since 1.6
	 *
	 * @param int              $entry_id      GF entry ID
	 * @param int              $field_id      GF field ID
	 * @param string|Exception $error_message Error object from geocoding provider, or error message string
	 * @param int|null         $form_id       ID of the entry's form
	 *
	 * @return void
	 */
	public function set_cache_error( $entry_id, $field_id, $error, $form_id = null ) {
		$error_message = $error;

		if ( $error instanceof Exception ) {
			$error_message = $error->getCode() . ': ' . $error->getMessage();
		}

		gform_update_meta( $entry_id, self::get_meta_key( $this->error_meta, $field_id ), $error_message, $form_id );

		GFFormsModel::add_note( $entry_id, 0, sprintf( 'GravityView %s', esc_html( 'Maps', 'gravityview-maps' ) ), $error_message, 'gravityview-maps' );
	}

	/**
	 * Cache the Lat / Long associated to a given address field ID of a given entry ID
	 *
	 * @param int      $entry_id GF entry ID
	 * @param int      $field_id GF field ID
	 * @param array    $position Contains the Latitude and Longitude, in that order. For example: [ 39.5500507, -105.7820674 ]
	 * @param int|null $form_id  ID of the entry's form
	 *
	 * @return void
	 */
	public function set_cache_position( $entry_id, $field_id, $position, $form_id = null ) {
		if ( empty( $position[0] ) || empty( $position[1] ) ) {
			do_action( 'gravityview_log_error', __METHOD__ . ': Not caching position for Entry #' . $entry_id . ' because $position key not set.', $position );

			return;
		}

		foreach ( $this->cached_meta as $k => $key ) {
			gform_update_meta( $entry_id, self::get_meta_key( $key, $field_id ), $position[ $k ], $form_id );
		}
	}

	/**
	 * Delete the Lat / Long associated to a given address field ID of a given entry ID
	 *
	 * @since 1.7
	 *
	 * @param int      $entry_id GF entry ID
	 * @param int      $field_id GF field ID
	 * @param int|null $form_id  ID of the entry's form
	 *
	 * @return void
	 */
	public function delete_cache_position( $entry_id, $field_id, $form_id = null ) {
		foreach ( $this->cached_meta as $k => $key ) {
			gform_delete_meta( $entry_id, self::get_meta_key( $key, $field_id ) );
		}

		gform_delete_meta( $entry_id, self::get_meta_key( $this->error_meta, $field_id ) );
	}

	/**
	 * In case entry is updated, delete the cached position
	 *
	 * @since 1.6 Added $original_entry parameter
	 *
	 * @param array $form
	 * @param int   $entry_id
	 * @param array $original_entry
	 *
	 * @return void
	 */
	public function flush_cache( $form, $entry_id, $original_entry = array() ) {
		$entry = GFAPI::get_entry( $entry_id );

		$_flush_cache = function ( $field, $field_input_id ) use ( $original_entry, $entry, $entry_id ) {

			$original_values = $field->get_value_export( $original_entry, $field_input_id );
			$current_values  = $field->get_value_export( $entry, $field_input_id );

			if ( $original_values === $current_values ) {
				return;
			}

			$this->delete_cache_position( $entry_id, $field['id'] ); // cache isn't stored for specific field input but rather the field itself
		};

		/** @var GF_Field_Address $field */
		foreach ( $form['fields'] as $field ) {
			if ( ! empty( $field['inputs'] ) ) {
				foreach ( $field['inputs'] as $field_input ) {
					$_flush_cache( $field, $field_input['id'] );
				}
			} else {
				$_flush_cache( $field, $field['id'] );
			}
		}
	}

	/**
	 * Get the meta key for the stored data.
	 *
	 * @param string $key Name of data stored
	 * @param        $field_id
	 *
	 * @return string Meta key used to store data using Gravity Forms Entry Meta
	 */
	private static function get_meta_key( $key, $field_id ) {
		return sanitize_title( 'gvmaps_' . $key . '_' . $field_id );
	}
}
