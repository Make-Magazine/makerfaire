<?php

namespace GravityKit\GravityMaps;

use \GFFormsModel;
use GF_Query;

/**
 * Class Search_GF_Query_Bounds_Condition
 *
 * @since 2.0
 *
 */
class Search_GF_Query_Bounds_Condition extends \GF_Query_Condition {
	/**
	 * The mode we handle fields.
	 *
	 * @since 2.2
	 *
	 * @var string
	 */
	protected $mode = 'internal';

	/**
	 * Sets the fields that need to be used based on the GF forms array of IDs provided.
	 *
	 * @since 2.0
	 *
	 * @var array[]
	 */
	protected $fields;

	/**
	 * Sets the bounds that will be used for creating the SQL.
	 *
	 * @since 2.0
	 *
	 * @var array[]
	 */
	protected $bounds;

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
	public function set_fields( $fields, string $type = 'internal' ): self {
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
	 * Sets the Bounds for this particular conditional.
	 *
	 * @since 2.0
	 *
	 * @param array $bounds
	 *
	 * @return $this
	 */
	public function set_bounds( array $bounds ) {
		$this->bounds = $bounds;
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

		if ( ! isset( $this->bounds, $this->fields ) ) {
			return null;
		}

		$t          = $query->_alias( null );
		$meta_table = GFFormsModel::get_entry_meta_table_name();

		$sql = [];

		$min_lat = 90;
        $max_lat = -90;
        $min_lng = 180;
        $max_lng = -180;

		/*
		 * Reference: https://stackoverflow.com/questions/4834772/get-all-records-from-mysql-database-that-are-within-google-maps-getbounds
		 */

		foreach ( $this->fields as $field ) {
			$lat_meta_value  = $wpdb->prepare( "(SELECT meta_value FROM $meta_table AS rtm WHERE rtm.`entry_id` = $t.`id` AND meta_key = %s LIMIT 1)", $field['lat'] );
			$long_meta_value = $wpdb->prepare( "(SELECT meta_value FROM $meta_table AS rgm WHERE rgm.`entry_id` = $t.`id` AND meta_key = %s LIMIT 1)", $field['long'] );

			$bounds_conditional = '
				((CASE WHEN %1$s < %2$s
				        THEN %5$s BETWEEN %1$s AND %2$s
				        ELSE %5$s BETWEEN %2$s AND %1$s
				END)
				AND
				(CASE WHEN %3$s < %4$s
				        THEN %6$s BETWEEN %3$s AND %4$s
				        ELSE %6$s BETWEEN %4$s AND %3$s
				END))
			';

			$bounds_query = sprintf( $bounds_conditional, $this->bounds['max_lat'], $this->bounds['min_lat'], $this->bounds['min_lng'], $this->bounds['max_lng'], $lat_meta_value, $long_meta_value );
			$sql[]    = $bounds_query;
		}

		return implode( ' ' . static::_OR . ' ', $sql );
	}
}