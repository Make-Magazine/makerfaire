<?php
/**
 * Manages Grid displays.
 *
 * @package GravityKit\GravityView\Renderer
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Renderer;

/**
 * Manages Grid displays.
 *
 * @since 2.31.0
 */
final class Grid {
	/**
	 * A prefix to use for the area key.
	 *
	 * @since 2.31.0
	 *
	 * @var string
	 */
	private static string $area_prefix = '';

	/**
	 * Internal counter to avoid UID clashes.
	 *
	 * @since 2.31.0
	 *
	 * @var int
	 */
	private static int $counter = 0;

	/**
	 * Returns the row configuration based on a type.
	 *
	 * @since 2.31.0
	 *
	 * @param string      $type         The type.
	 * @param string|null $id           The row ID. WIll be generated if not provided.
	 * @param bool        $keep_area_id Whether to keep the existing area IDs.
	 *
	 * @return array The row configuration.
	 */
	public static function get_row_by_type( string $type, ?string $id = null, bool $keep_area_id = false ): array {
		$rows = self::get_row_types();
		$row  = $rows[ $type ] ?? [];

		$id ??= self::uid();

		if ( $keep_area_id ) {
			return $row;
		}

		foreach ( $row as $col => $areas ) {
			foreach ( $areas as $i => $area ) {
				$row[ $col ][ $i ]['areaid'] = implode( '::', [ $row[ $col ][ $i ]['areaid'], $type, $id ] );
			}
		}

		return $row;
	}

	/**
	 * Returns unique ID.
	 *
	 * @since 2.42
	 *
	 * @return string
	 */
	public static function uid(): string {
		return substr( md5( ++self::$counter . microtime( true ) ), 0, 13 );
	}

	/**
	 * Calculates and returns the row configurations based on a collection and the zone.
	 *
	 * @since 2.31.0
	 *
	 * @param \GV\Collection $collection The collection.
	 * @param string         $zone       The zone.
	 *
	 * @return array The row configurations.
	 */
	public static function get_rows_from_collection( \GV\Collection $collection, string $zone ): array {
		$rows = [];
		if ( ! $collection instanceof \GV\Collection_Position_Aware ) {
			return $rows;
		}

		foreach ( $collection->by_position( $zone . '*' )->all() as $element ) {
			$parts = explode( '::', explode( '_', $element->position, 2 )[1] ?? '', 3 );

			$area = $parts[0] ?? '';
			$type = $parts[1] ?? ( in_array( $area, [ 'left', 'right' ], true ) ? '50/50' : '100' );
			$id   = $parts[2] ?? $type;

			$rows[ $id ] ??= self::get_row_by_type( $type, $id, ! ( $parts[1] ?? false ) );
		}

		return array_values( $rows );
	}

	/**
	 * Prefixes any area's for methods called within the callback.
	 *
	 * @param string        $prefix   The prefix.
	 * @param callable|null $callback The callback
	 *
	 * @return array
	 */
	public static function prefixed( string $prefix, callable $callback ): array {
		self::$area_prefix = $prefix;

		try {
			$result = $callback();
		} finally {
			self::$area_prefix = '';
		}

		return $result;
	}

	/**
	 * Extracts the row UID from a row configuration.
	 *
	 * @since 2.54.0
	 *
	 * @param array $row The row configuration (columns => areas).
	 *
	 * @return string The row UID, or empty string if not found.
	 */
	public static function extract_row_uid( array $row ): string {
		$first_col = reset( $row );

		if ( ! is_array( $first_col ) ) {
			return '';
		}

		$first_area = reset( $first_col );

		if ( ! is_array( $first_area ) ) {
			return '';
		}

		$parts = explode( '::', $first_area['areaid'] ?? '' );

		return $parts[2] ?? '';
	}

	/**
	 * Returns a single registered row type's template, or null when unknown.
	 * Singular sibling of get_row_types().
	 *
	 * @since 3.0.0
	 *
	 * @param string $type Row type id (e.g. "100", "50/50").
	 *
	 * @return array|null
	 */
	public static function get_row_type( string $type ): ?array {
		$types = self::get_row_types();

		return isset( $types[ $type ] ) && is_array( $types[ $type ] ) ? $types[ $type ] : null;
	}

	/**
	 * Build the ordered list of storage area-keys a row materialises into —
	 * `{prefix}{-?}{areaid}::{type}::{row_uid}`, one per column. The prefix joins
	 * with `-` for non-empty values (Layout Builder's
	 * `gravityview-layout-builder-top` shape); an empty prefix (widget surfaces)
	 * yields the bare areaid so a caller's `{zone}_{areaid}` key reads
	 * `header_top`, not `header_-top`.
	 *
	 * @since 3.0.0
	 *
	 * @param string $prefix       Template/area prefix ('' for widget surfaces).
	 * @param array  $row_template Row template (column key => areas).
	 * @param string $type         Row type (e.g. "50/50").
	 * @param string $row_uid      Row UID.
	 *
	 * @return string[]
	 */
	public static function area_keys_for_row( string $prefix, array $row_template, string $type, string $row_uid ): array {
		$out       = [];
		$separator = '' === $prefix ? '' : '-';

		foreach ( $row_template as $areas ) {
			foreach ( (array) $areas as $area ) {
				if ( ! is_array( $area ) || empty( $area['areaid'] ) ) {
					continue;
				}
				$out[] = $prefix . $separator . $area['areaid'] . '::' . $type . '::' . $row_uid;
			}
		}

		return $out;
	}

	/**
	 * Derive the 0-based column index from a stored area-key
	 * (`{zone}_{prefix}{-?}{areaid}::{type}::{row_uid}`) by mapping its areaid to
	 * a column position. Returns 0 (first column) when the key carries no
	 * positional information — a safe fallback. Position derives from the areaid
	 * name, not the row-type registry, since changing a row's TYPE never renames
	 * area positions (works for `50/50`/`100` and legacy `1-2`/`2-3` tokens).
	 *
	 * @since 3.0.0
	 *
	 * @param string $key    Stored area-key.
	 * @param string $prefix Grid prefix for the surface + zone.
	 *
	 * @return int
	 */
	public static function column_index_from_key( string $key, string $prefix ): int {
		$without_zone = explode( '_', $key, 2 )[1] ?? $key;
		$head         = explode( '::', $without_zone, 2 )[0];
		$areaid       = ltrim( substr( $head, strlen( $prefix ) ), '-' );

		$ordering = [
			'top'    => 0,
			'left'   => 0,
			'first'  => 0,
			'middle' => 1,
			'second' => 1,
			'right'  => 2,
			'third'  => 2,
			'fourth' => 3,
		];

		return $ordering[ $areaid ] ?? 0;
	}

	/**
	 * Returns all registered row types.
	 *
	 * @since 2.31.0
	 *
	 * @return array The row types with their configuration.
	 */
	public static function get_row_types(): array {
		$types = [
			'100'         => [
				'1-1' => [
					[
						'areaid'   => 'top',
						'title'    => \__( 'Top', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
			],
			'50/50'       => [
				'1-2 left'  => [
					[
						'areaid'   => 'left',
						'title'    => \__( 'Left', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-2 right' => [
					[
						'areaid'   => 'right',
						'title'    => \__( 'Right', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
			],
			'33/66'       => [
				'1-3 left'  => [
					[
						'areaid'   => 'left',
						'title'    => \__( 'Left', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'2-3 right' => [
					[
						'areaid'   => 'right',
						'title'    => \__( 'Right', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
			],
			'66/33'       => [
				'2-3 left'  => [
					[
						'areaid'   => 'left',
						'title'    => \__( 'Left', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-3 right' => [
					[
						'areaid'   => 'right',
						'title'    => \__( 'Right', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
			],
			'33/33/33'    => [
				'1-3 left'   => [
					[
						'areaid'   => 'left',
						'title'    => \__( 'Left', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-3 middle' => [
					[
						'areaid'   => 'middle',
						'title'    => \__( 'Middle', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-3 right'  => [
					[
						'areaid'   => 'right',
						'title'    => \__( 'Right', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
			],
			'50/25/25'    => [
				'1-2 left'   => [
					[
						'areaid'   => 'left',
						'title'    => \__( 'Left', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-4 middle' => [
					[
						'areaid'   => 'middle',
						'title'    => \__( 'Middle', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-4 right'  => [
					[
						'areaid'   => 'right',
						'title'    => \__( 'Right', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
			],
			'25/25/50'    => [
				'1-4 left'   => [
					[
						'areaid'   => 'left',
						'title'    => \__( 'Left', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-4 middle' => [
					[
						'areaid'   => 'middle',
						'title'    => \__( 'Middle', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-2 right'  => [
					[
						'areaid'   => 'right',
						'title'    => \__( 'Right', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
			],
			'25/50/25'    => [
				'1-4 left'   => [
					[
						'areaid'   => 'left',
						'title'    => \__( 'Left', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-2 middle' => [
					[
						'areaid'   => 'middle',
						'title'    => \__( 'Middle', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-4 right'  => [
					[
						'areaid'   => 'right',
						'title'    => \__( 'Right', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
			],
			'25/25/25/25' => [
				'1-4 first'  => [
					[
						'areaid'   => 'first',
						'title'    => \__( 'First', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-4 second' => [
					[
						'areaid'   => 'second',
						'title'    => \__( 'Second', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-4 third'  => [
					[
						'areaid'   => 'third',
						'title'    => \__( 'Third', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
				'1-4 fourth' => [
					[
						'areaid'   => 'fourth',
						'title'    => \__( 'Fourth', 'gk-gravityview' ),
						'subtitle' => '',
					],
				],
			],
		];

		array_walk_recursive(
			$types,
			static function ( &$value, $key ) {
				if ( 'areaid' === $key ) {
					$value = ( self::$area_prefix ? self::$area_prefix . '-' : '' ) . $value;
				}
			}
		);

		return $types;
	}
}
