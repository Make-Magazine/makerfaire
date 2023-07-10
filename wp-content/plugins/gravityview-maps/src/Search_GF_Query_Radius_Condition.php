<?php

namespace GravityKit\GravityMaps;

use \GFFormsModel;
use GF_Query;

/**
 * Class Search_GF_Query_Radius_Condition
 *
 * @since 2.0
 *
 */
class Search_GF_Query_Radius_Condition extends \GF_Query_Condition {
	/**
	 * Which Longitude will be used to run the query.
	 *
	 * @since 2.0
	 *
	 * @var float
	 */
	protected $longitude;

	/**
	 * Which Latitude will be used to run the query.
	 *
	 * @since 2.0
	 *
	 * @var float
	 */
	protected $latitude;

	/**
	 * Radius we will run this conditional with.
	 *
	 * @since 2.0
	 *
	 * @var float
	 */
	protected $radius;

	/**
	 * Sets the fields that need to be used based on the GF forms array of IDs provided.
	 *
	 * @since 2.0
	 *
	 * @var array[]
	 */
	protected $fields;

	/**
	 * Sets which fields we will use for the SQL for this particular conditional.
	 *
	 * @since 2.0
	 *
	 * @param int[]|string[] $fields Field IDS that will be turned to array of data.
	 * @param string         $type   From which type of field we are looking for.
	 *
	 * @return $this
	 */
	public function set_fields( $fields, $type = 'internal' ) {
		$callbacks = Form_Fields::get_geolocation_fields_meta_key_callback( $type );
		// Don't set fields when there are no callbacks.
		if ( ! $callbacks ) {
			return $this;
		}

		$this->mode = $type;

		$this->fields = array_map( static function ( $id ) use ( $callbacks ) {
			return [
				'id'   => $id,
				'lat'  => $callbacks['lat']( $id ),
				'long' => $callbacks['long']( $id ),
			];
		}, (array) $fields );

		return $this;
	}

	/**
	 * Sets the Radius for this particular conditional.
	 *
	 * @since 2.0
	 *
	 * @param float $radius
	 *
	 * @return $this
	 */
	public function set_radius( $radius ) {
		$this->radius = (float) $radius;

		return $this;
	}

	/**
	 * Sets the Latitude for this particular conditional.
	 *
	 * @since 2.0
	 *
	 * @param float $latitude
	 *
	 * @return $this
	 */
	public function set_latitude( $latitude ) {
		$this->latitude = (float) $latitude;

		return $this;
	}

	/**
	 * Sets the Longitude for this particular conditional.
	 *
	 * @since 2.0
	 *
	 * @param float $longitude
	 *
	 * @return $this
	 */
	public function set_longitude( $longitude ) {
		$this->longitude = (float) $longitude;

		return $this;
	}

	/**
	 * Generate the SQL based on the params set for this particular type of conditional.
	 *
	 * @param GF_Query The query.
	 *
	 * @return string The SQL this condition generates.
	 */
	public function sql( $query ) {
		global $wpdb;

		if ( ! isset( $this->longitude, $this->latitude, $this->radius, $this->fields ) ) {
			return null;
		}

		$t          = $query->_alias( null );
		$meta_table = GFFormsModel::get_entry_meta_table_name();

		$sql = [];

		foreach ( $this->fields as $field ) {
			$lat_meta_value  = $wpdb->prepare( "(SELECT meta_value FROM $meta_table AS rtm WHERE rtm.`entry_id` = $t.`id` AND meta_key = %s LIMIT 1)", $field['lat'] );
			$long_meta_value = $wpdb->prepare( "(SELECT meta_value FROM $meta_table AS rgm WHERE rgm.`entry_id` = $t.`id` AND meta_key = %s LIMIT 1)", $field['long'] );

			$haversine = "6371 * ACOS(COS(RADIANS(%%s)) * COS(RADIANS(%s)) * COS(RADIANS(%s) - RADIANS(%%s)) + SIN(RADIANS(%%s)) * SIN(RADIANS(%s)))";

			$distance = sprintf( $haversine, $lat_meta_value, $long_meta_value, $lat_meta_value );

			$distance = $wpdb->prepare( $distance, $this->latitude, $this->longitude, $this->latitude );
			$sql[]    = $wpdb->prepare( "(SELECT {$distance} BETWEEN 0 AND %s)", $this->radius );
		}

		return implode( ' ' . static::_OR . ' ', $sql );
	}
}