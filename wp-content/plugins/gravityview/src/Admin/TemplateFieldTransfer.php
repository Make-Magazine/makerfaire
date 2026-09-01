<?php
/**
 * Transfers a View's field configuration between templates.
 *
 * @package GravityKit\GravityView\Admin
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin;

use GravityKit\GravityView\Preset\LayoutBuilder;
use GravityKit\GravityView\Renderer\Grid;

/**
 * Maps a `_gravityview_directory_fields`-shaped configuration from one template's zones to another's.
 *
 * Used by the View editor when switching layouts, so configured fields move into
 * the new layout instead of being discarded. Templates without a dedicated mapping
 * fall back to their first defined area; when a target defines no areas at all
 * (e.g. it is not registered), the source zones are returned untouched and will
 * only render again once the template is available.
 *
 * Extensions can adjust the result via the
 * `gk/gravityview/admin-views/template-switch/fields` filter.
 *
 * @since 3.0.0
 */
final class TemplateFieldTransfer {
	/**
	 * The contexts this service migrates. Other contexts (e.g. `edit`) pass through untouched.
	 *
	 * @since 3.0.0
	 *
	 * @var string[]
	 */
	private const CONTEXTS = [ 'directory', 'single' ];

	/**
	 * Memoized registered templates, looked up once per instance.
	 *
	 * @since 3.0.0
	 *
	 * @var array|null
	 */
	private ?array $templates = null;

	/**
	 * Transfers a field configuration from one template to another.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $fields        The configuration, keyed by `<context>_<areaid>`.
	 * @param string $from_template The source template ID (e.g. `default_list`).
	 * @param string $to_template   The target template ID (e.g. `gravityview-layout-builder`).
	 *
	 * @return array The configuration keyed for the target template's zones.
	 */
	public function transfer( array $fields, string $from_template, string $to_template ): array {
		if ( ! $fields || $from_template === $to_template ) {
			return $fields;
		}

		$from_slug = $this->get_template_slug( $from_template );
		$to_slug   = $this->get_template_slug( $to_template );

		if ( '' !== $from_slug && $from_slug === $to_slug ) {
			// Same layout family (e.g. Table and DataTables): zones are identical.
			// Two unresolved templates both yield '' but are NOT the same family;
			// they fall through to the first-area fallback so fields are kept.
			return $fields;
		}

		$result = [];

		foreach ( self::CONTEXTS as $context ) {
			$zones = $this->get_context_zones( $fields, $context, $from_template );

			if ( ! $zones ) {
				continue;
			}

			$result += $this->transfer_context( $zones, $context, $from_slug, $to_slug, $to_template );
		}

		foreach ( $fields as $key => $zone_fields ) {
			$context = explode( '_', (string) $key, 2 )[0];

			if ( ! in_array( $context, self::CONTEXTS, true ) ) {
				$result[ $key ] = $zone_fields;
			}
		}

		/**
		 * Modifies the migrated field configuration after a View template switch.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $result        The migrated configuration, keyed for the target template.
		 * @param array  $fields        The original configuration.
		 * @param string $from_template The source template ID.
		 * @param string $to_template   The target template ID.
		 */
		return (array) apply_filters( 'gk/gravityview/admin-views/template-switch/fields', $result, $fields, $from_template, $to_template );
	}

	/**
	 * Returns the configured, non-empty zones for a context in natural reading order.
	 *
	 * Zones are ordered by the source template's area definitions (rows, then
	 * columns, then areas). Configured zones the template does not define keep
	 * their configuration order at the end.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $fields      The full configuration.
	 * @param string $context     The context (`directory` or `single`).
	 * @param string $template_id The source template ID.
	 *
	 * @return array The ordered zones, keyed by `<context>_<areaid>`.
	 */
	private function get_context_zones( array $fields, string $context, string $template_id ): array {
		$configured = [];

		foreach ( $fields as $key => $zone_fields ) {
			if ( 0 !== strpos( (string) $key, $context . '_' ) || ! is_array( $zone_fields ) ) {
				continue;
			}

			// Tolerate corrupt entries: every field must itself be a configuration array.
			$zone_fields = array_filter( $zone_fields, 'is_array' );

			if ( $zone_fields ) {
				$configured[ $key ] = $zone_fields;
			}
		}

		if ( ! $configured ) {
			return [];
		}

		$ordered = [];

		foreach ( $this->get_zone_keys_in_natural_order( $template_id, $context, $fields ) as $key ) {
			if ( isset( $configured[ $key ] ) ) {
				$ordered[ $key ] = $configured[ $key ];
				unset( $configured[ $key ] );
			}
		}

		return $ordered + $configured;
	}

	/**
	 * Returns a template's zone keys in natural reading order.
	 *
	 * Column order within a row is reversed for RTL locales. This deliberately
	 * uses the locale of the person editing (`is_rtl()` in the admin request):
	 * the editor shows the zones in that direction, so "natural order" matches
	 * what they see while configuring.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template_id The template ID.
	 * @param string $context     The context.
	 * @param array  $fields      The configuration (needed to resolve dynamic areas like Layout Builder rows).
	 *
	 * @return string[] The zone keys (`<context>_<areaid>`).
	 */
	private function get_zone_keys_in_natural_order( string $template_id, string $context, array $fields ): array {
		/** This filter is documented in src/Admin/AdminViews.php */
		$rows = apply_filters( 'gravityview_template_active_areas', [], $template_id, $context );

		/** This filter is documented in src/Admin/AdminViews.php */
		$rows = (array) apply_filters( 'gk/gravityview/admin-views/view/template/active-areas', $rows, $template_id, $context, $fields );

		$keys = [];

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$columns = is_rtl() ? array_reverse( $row, true ) : $row;

			foreach ( $columns as $areas ) {
				foreach ( (array) $areas as $area ) {
					if ( isset( $area['areaid'] ) ) {
						$keys[] = $context . '_' . $area['areaid'];
					}
				}
			}
		}

		return $keys;
	}

	/**
	 * Transfers a single context's zones to the target template.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $zones       The ordered source zones.
	 * @param string $context     The context.
	 * @param string $from_slug   The source template slug.
	 * @param string $to_slug     The target template slug.
	 * @param string $to_template The target template ID.
	 *
	 * @return array The zones keyed for the target template.
	 */
	private function transfer_context( array $zones, string $context, string $from_slug, string $to_slug, string $to_template ): array {
		if ( LayoutBuilder::ID === $to_slug ) {
			return $this->to_layout_builder( $zones, $context, $from_slug );
		}

		if ( 'table' === $to_slug ) {
			return [ $context . '_table-columns' => $this->flatten( $zones ) ];
		}

		if ( 'list' === $to_slug ) {
			return $this->to_list( $zones, $context );
		}

		return $this->to_first_area( $zones, $context, $to_template );
	}

	/**
	 * Flattens zones into a single field list, preserving unique IDs and order.
	 *
	 * @since 3.0.0
	 *
	 * @param array $zones The zones.
	 *
	 * @return array The flattened fields.
	 */
	private function flatten( array $zones ): array {
		$flat = [];

		foreach ( $zones as $zone_fields ) {
			$flat = array_replace( $flat, $zone_fields );
		}

		return $flat;
	}

	/**
	 * Transfers zones to the Layout Builder template.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $zones     The ordered source zones.
	 * @param string $context   The context.
	 * @param string $from_slug The source template slug.
	 *
	 * @return array The Layout Builder zones.
	 */
	private function to_layout_builder( array $zones, string $context, string $from_slug ): array {
		if ( 'list' === $from_slug ) {
			return $this->list_to_layout_builder( $zones, $context );
		}

		if ( 'table' === $from_slug ) {
			$zones = [ $context . '_table-columns' => $this->flatten( $zones ) ];
		}

		$result = [];

		foreach ( $zones as $zone_fields ) {
			$areas = $this->create_row_areas( '100' );

			$result[ $context . '_' . $areas['top'] ] = $zone_fields;
		}

		return $result;
	}

	/**
	 * Recreates the List layout's zones as Layout Builder rows.
	 *
	 * Title and subtitle each get a full-width row, image and description share
	 * a 33/66 row, and the footers share a 50/50 row. Rows without fields are skipped.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $zones   The ordered source zones.
	 * @param string $context The context.
	 *
	 * @return array The Layout Builder zones.
	 */
	private function list_to_layout_builder( array $zones, string $context ): array {
		$layout = [
			[
				'type'  => '100',
				'areas' => [ 'top' => 'list-title' ],
			],
			[
				'type'  => '100',
				'areas' => [ 'top' => 'list-subtitle' ],
			],
			[
				'type'  => '33/66',
				'areas' => [
					'left'  => 'list-image',
					'right' => 'list-description',
				],
			],
			[
				'type'  => '50/50',
				'areas' => [
					'left'  => 'list-footer-left',
					'right' => 'list-footer-right',
				],
			],
		];

		$result = [];

		foreach ( $layout as $row ) {
			$zone_keys = [];

			foreach ( $row['areas'] as $position => $list_area ) {
				$zone_key = $context . '_' . $list_area;

				if ( ! empty( $zones[ $zone_key ] ) ) {
					$zone_keys[ $position ] = $zone_key;
				}
			}

			if ( ! $zone_keys ) {
				continue;
			}

			$areas = $this->create_row_areas( $row['type'] );

			foreach ( $zone_keys as $position => $zone_key ) {
				$result[ $context . '_' . $areas[ $position ] ] = $zones[ $zone_key ];

				unset( $zones[ $zone_key ] );
			}
		}

		// Zones that are not part of the standard List layout each get their own row.
		foreach ( $zones as $zone_fields ) {
			$areas = $this->create_row_areas( '100' );

			$result[ $context . '_' . $areas['top'] ] = $zone_fields;
		}

		return $result;
	}

	/**
	 * Transfers zones to the List template.
	 *
	 * The first field becomes the Listing Title; the rest go to Other Fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $zones   The ordered source zones.
	 * @param string $context The context.
	 *
	 * @return array The List zones.
	 */
	private function to_list( array $zones, string $context ): array {
		$flat = $this->flatten( $zones );

		if ( ! $flat ) {
			return [];
		}

		$first_key = array_key_first( $flat );
		$result    = [ $context . '_list-title' => [ $first_key => $flat[ $first_key ] ] ];

		unset( $flat[ $first_key ] );

		if ( $flat ) {
			$result[ $context . '_list-description' ] = $flat;
		}

		return $result;
	}

	/**
	 * Transfers zones into the first area of a target template.
	 *
	 * Fallback for templates without a dedicated mapping, so fields are never dropped.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $zones       The ordered source zones.
	 * @param string $context     The context.
	 * @param string $to_template The target template ID.
	 *
	 * @return array The target zones, or the source zones when the target defines no areas.
	 */
	private function to_first_area( array $zones, string $context, string $to_template ): array {
		$keys = $this->get_zone_keys_in_natural_order( $to_template, $context, [] );

		if ( ! $keys ) {
			return $zones;
		}

		return [ $keys[0] => $this->flatten( $zones ) ];
	}

	/**
	 * Creates a fresh Layout Builder grid row and maps base area names to full area IDs.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type The row type (e.g. `100`, `50/50`, `33/66`).
	 *
	 * @return array Map of base area name (`top`, `left`, ...) to full area ID.
	 */
	private function create_row_areas( string $type ): array {
		$row = Grid::prefixed(
			LayoutBuilder::ID,
			static fn() => Grid::get_row_by_type( $type ),
		);

		$areas = [];

		foreach ( $row as $columns ) {
			foreach ( $columns as $area ) {
				$area_id = (string) ( $area['areaid'] ?? '' );

				if ( '' === $area_id ) {
					continue;
				}

				$base = substr( explode( '::', $area_id )[0], strlen( LayoutBuilder::ID ) + 1 );

				$areas[ $base ] = $area_id;
			}
		}

		return $areas;
	}

	/**
	 * Resolves the slug for a template ID.
	 *
	 * Registered templates use their declared slug. For unregistered templates
	 * (e.g. a deactivated layout plugin) the slug is only derived from the ID
	 * for the known first-party prefixes; anything else is treated as an
	 * unknown family rather than guessed from the ID, so an exotic ID like
	 * `client_table` is never misrouted into Table zones.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template_id The template ID.
	 *
	 * @return string The slug (e.g. `table` for both `default_table` and `datatables_table`), or empty string when unknown.
	 */
	private function get_template_slug( string $template_id ): string {
		if ( null === $this->templates ) {
			$this->templates = function_exists( 'gravityview_get_registered_templates' ) ? (array) \gravityview_get_registered_templates() : [];
		}

		$slug = (string) ( $this->templates[ $template_id ]['slug'] ?? '' );

		if ( '' === $slug && preg_match( '/^(?:default|preset|datatables)_(.+)$/', $template_id, $matches ) ) {
			$slug = $matches[1];
		}

		return $slug;
	}
}
