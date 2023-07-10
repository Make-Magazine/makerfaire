<?php

namespace GravityKit\GravityMaps;


use GravityKit\GravityMaps\Foundation\Helpers\Arr;

class Markers {

	/**
	 * Local storage of the cached Marker Values.
	 *
	 * @since 2.0
	 *
	 * @var array
	 */
	protected static $items = [];

	/**
	 * Gets the cached value for a given marker.
	 *
	 * @since 2.0
	 *
	 * @param int|string $entry_id
	 *
	 * @return false|object
	 */
	public static function get( $entry_id ) {
		if ( ! isset( static::$items[ (int) $entry_id ] ) ) {
			return false;
		}

		return static::$items[ (int) $entry_id ];
	}

	/**
	 * Primes the cache for all the Map Markers for a given view to be queried at once.
	 *
	 * @since 2.0
	 *
	 * @param array $entries
	 * @param array $fields
	 *
	 */
	public static function prime_cache( array $entries, array $fields ) {
		$entries_ids = wp_list_pluck( $entries, 'id' );
		$entries_ids = array_filter( $entries_ids, 'is_numeric' );

		$entries_ids = array_filter( $entries_ids, static function ( $entry_id ) {
			return false === Markers::get( $entry_id );
		} );

		$meta_keys = [];
		$field_values = [];

		foreach( $fields as $field ) {
			$id = Arr::get( (array) $field, 'id' );
			if ( empty( $id ) ) {
				continue;
			}

			if ( $field instanceof \GF_Field_Address ) {
				$meta_keys[] = Form_Fields::get_meta_key( 'lat', $id );
				$meta_keys[] = Form_Fields::get_meta_key( 'long', $id );
			} elseif ( $field instanceof \GF_Field ) {
				$field_values[] = $id;
			}
		}

		if ( ! empty( $meta_keys ) ) {
			$values = gform_get_meta_values_for_entries( $entries_ids, $meta_keys );

			foreach ( $values as $value ) {
				static::$items[ (int) $value->entry_id ] = $value;
			}
		}

		if ( ! empty( $field_values ) ) {
			foreach ( $entries as $entry ) {
				foreach ( $field_values as $field_id ) {
					if ( ! isset( $entry[ $field_id ] ) ) {
						continue;
					}

					$values[] = $entry[ $field_id ];
				}

				if ( ! empty( $values ) ) {
					static::$items[ (int) $entry['id'] ] = $values;
				}
			}
		}
	}

	/**
	 * Filters the markers to only keep valid ones.
	 *
	 * @since 2.2
	 *
	 * @param mixed $marker
	 *
	 * @return bool
	 */
	public static function filter_valid_marker_data( $marker ): bool {
		if ( empty( $marker ) ) {
			return false;
		}

		if ( empty( $marker['lat'] ) ) {
			return false;
		}

		if ( empty( $marker['long'] ) ) {
			return false;
		}

		return true;
	}
}
