<?php
/**
 * Repository for grid-row operations within a View.
 *
 * @package     GravityKit\GravityView\View\Grid
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View\Grid;

use GravityKit\GravityView\View\Concurrency\ViewLock;
use GravityKit\GravityView\View\Concurrency\ViewPreconditionChecker;
use GravityKit\GravityView\View\Concurrency\ViewVersionComputer;
use GravityKit\GravityView\Renderer\Grid;
use GravityKit\GravityView\View\Contracts\BatchableSlotRepository;
use GravityKit\GravityView\View\RowSlotIdentity;
use GravityKit\GravityView\View\SlotRepositoryTrait;
use WP_Error;

/**
 * Owns row-level operations on the Layout Builder grid. Rows are
 * implicitly defined by area-keys: each storage key has the shape
 * `<zone>_<...>::<row_uid>::<column>` (fields surface) or
 * `<zone>_<row_uid>_<column>_<area>` (widgets surface). Rows in a zone
 * are rendered in iteration order, so "moving" a row means re-keying
 * the tree so the row's area-keys appear earlier or later.
 *
 * Identity: `(view_id, surface, zone, row_uid)`.
 *
 * @since 3.0.0
 */
final class GridRowRepository implements BatchableSlotRepository {
	use SlotRepositoryTrait;

	private const FIELDS_META_KEY             = '_gravityview_directory_fields';
	private const WIDGETS_META_KEY            = '_gravityview_directory_widgets';
	private const DIRECTORY_TEMPLATE_META_KEY = '_gravityview_directory_template';
	private const SINGLE_TEMPLATE_META_KEY    = '_gravityview_single_template';
	private const WIDGET_ZONES                = [ 'header', 'footer' ];

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param ViewLock|null                $lock         View-lock service.
	 * @param ViewPreconditionChecker|null $precondition Precondition checker.
	 * @param ViewVersionComputer|null     $version      Version computer.
	 */
	public function __construct(
		?ViewLock $lock = null,
		?ViewPreconditionChecker $precondition = null,
		?ViewVersionComputer $version = null
	) {
		$this->init_slot_repository_services( $lock, $precondition, $version );
	}

	/**
	 * Move a grid row to a new position within its zone.
	 *
	 * @since 3.0.0
	 *
	 * @param RowSlotIdentity $row          Source row identity.
	 * @param int             $new_position 0-based target index in the zone's row order.
	 * @param string|null     $if_match     Optimistic-concurrency token.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function move( RowSlotIdentity $row, int $new_position, ?string $if_match ) {
		$view_id = $row->view_id();
		$surface = $row->surface();
		$zone    = $row->zone();
		$row_uid = $row->row_uid();

		if ( $new_position < 0 ) {
			return new WP_Error(
				'gv_rest_invalid_input',
				__( 'new_position must be 0 or higher.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		return $this->with_lock(
			$view_id,
			function () use ( $view_id, $surface, $zone, $row_uid, $new_position, $if_match ) {
				$pre = $this->check_precondition( $view_id, $if_match );
				if ( is_wp_error( $pre ) ) {
					return $pre;
				}

				$meta_key = 'widgets' === $surface ? self::WIDGETS_META_KEY : self::FIELDS_META_KEY;
				$tree     = get_post_meta( $view_id, $meta_key, true );
				if ( ! is_array( $tree ) ) {
					$tree = [];
				}

				$ordered = $this->reorder_zone_rows( $tree, $zone, $row_uid, $new_position );
				if ( is_wp_error( $ordered ) ) {
					return $ordered;
				}

				update_post_meta( $view_id, $meta_key, $ordered );

				$bumped = $this->bump_version( $view_id );
				if ( '' === $bumped ) {
					return $this->version_bump_failed( __( 'The row was moved but the View couldn\'t be marked updated.', 'gk-gravityview' ) );
				}

				return [
					'view_id'  => $view_id,
					'surface'  => $surface,
					'zone'     => $zone,
					'row_uid'  => $row_uid,
					'position' => $new_position,
					'version'  => $bumped,
				];
			}
		);
	}

	/**
	 * Atomically apply grid-row add or patch items.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id       View post id.
	 * @param array  $items         Per-item operations.
	 * @param string $version_token Caller-provided View version.
	 * @param bool   $dry_run       Whether to validate and plan only.
	 * @return array|WP_Error
	 */
	public function batch_apply( int $view_id, array $items, string $version_token, bool $dry_run ) {
		$count = count( $items );
		if ( \GravityKit\GravityView\Abilities\Support\BatchHelpers::MIN_BATCH_SIZE > $count
			|| $count > \GravityKit\GravityView\Abilities\Support\BatchHelpers::MAX_BATCH_SIZE ) {
			return new WP_Error(
				'gk_batch_invalid_size',
				sprintf(
					/* translators: 1: minimum batch size, 2: maximum batch size */
					__( 'Batch must contain between %1$d and %2$d items.', 'gk-gravityview' ),
					\GravityKit\GravityView\Abilities\Support\BatchHelpers::MIN_BATCH_SIZE,
					\GravityKit\GravityView\Abilities\Support\BatchHelpers::MAX_BATCH_SIZE
				),
				[
					'status' => 400,
					'count'  => $count,
				]
			);
		}

		return $this->with_lock(
			$view_id,
			function () use ( $view_id, $items, $version_token, $dry_run ) {
				$current_version = $this->compute_version( $view_id );
				if ( $this->normalize_version_token( $version_token ) !== $current_version ) {
					return $this->batch_version_mismatch( $current_version, $version_token );
				}

				$trees    = [
					'fields'  => $this->load_surface_tree( $view_id, 'fields' ),
					'widgets' => $this->load_surface_tree( $view_id, 'widgets' ),
				];
				$touched  = [];
				$plans    = [];
				$failures = [];

				foreach ( array_values( $items ) as $index => $item ) {
					if ( ! is_array( $item ) ) {
						$err = $this->invalid_input( __( 'Batch item must be an object.', 'gk-gravityview' ) );
					} elseif ( $this->is_patch_item( $item ) ) {
						$err = $this->validate_patch_item( $view_id, $item, $trees );
					} else {
						$err = $this->validate_add_item( $view_id, $item );
					}

					if ( is_wp_error( $err ) ) {
						$failures[] = $this->batch_failure( $index, $err );
						continue;
					}

					$plan = $this->is_patch_item( $item )
						? $this->plan_patch_item( $view_id, $item, $trees, $index )
						: $this->plan_add_item( $view_id, $item, $trees, $index );

					if ( is_wp_error( $plan ) ) {
						$failures[] = $this->batch_failure( $index, $plan );
						continue;
					}

					$trees                       = $plan['trees'];
					$touched[ $plan['surface'] ] = true;
					$plans[]                     = $plan['result'];
				}

				if ( ! empty( $failures ) ) {
					return $this->batch_validation_failed( $failures, count( $items ) );
				}

				$version = $current_version;
				if ( ! $dry_run ) {
					// Snapshots support cross-meta rollback when widget persist
					// fails after fields persist succeeded. persist_meta()
					// distinguishes DB failure from a no-op (stored value
					// already matched); a no-op leaves the underlying meta
					// untouched and returns false (not WP_Error).
					$snapshots = [];
					$wrote_any = false;

					if ( ! empty( $touched['fields'] ) ) {
						$snapshots['fields'] = get_post_meta( $view_id, self::FIELDS_META_KEY, true );
						$persist             = $this->persist_meta( $view_id, self::FIELDS_META_KEY, $trees['fields'] );
						if ( is_wp_error( $persist ) ) {
							return $persist;
						}
						if ( true === $persist ) {
							$wrote_any = true;
						}
					}
					if ( ! empty( $touched['widgets'] ) ) {
						$snapshots['widgets'] = get_post_meta( $view_id, self::WIDGETS_META_KEY, true );
						$persist              = $this->persist_meta( $view_id, self::WIDGETS_META_KEY, $trees['widgets'] );
						if ( is_wp_error( $persist ) ) {
							if ( $wrote_any && array_key_exists( 'fields', $snapshots ) ) {
								update_post_meta( $view_id, self::FIELDS_META_KEY, $snapshots['fields'] );
							}
							return $persist;
						}
						if ( true === $persist ) {
							$wrote_any = true;
						}
					}

					if ( $wrote_any ) {
						$version = $this->bump_version( $view_id );
						if ( '' === $version ) {
							if ( array_key_exists( 'widgets', $snapshots ) ) {
								update_post_meta( $view_id, self::WIDGETS_META_KEY, $snapshots['widgets'] );
							}
							if ( array_key_exists( 'fields', $snapshots ) ) {
								update_post_meta( $view_id, self::FIELDS_META_KEY, $snapshots['fields'] );
							}
							return $this->version_bump_failed();
						}
					}
				}

				return $this->batch_result_rows( $view_id, $plans, $version );
			}
		);
	}

	/**
	 * Rebuild the tree with the target row repositioned within its zone.
	 *
	 * Groups area-keys by row_uid (each row's keys appear together in the
	 * source tree). Walks the zone's groups, pops the target, inserts at
	 * the new index. Keys outside the target zone are kept in their
	 * original positions.
	 *
	 * @since 3.0.0
	 *
	 * @param array<string,array<string,mixed>> $tree         The full tree (fields or widgets meta).
	 * @param string                            $zone         Target zone (e.g., directory).
	 * @param string                            $row_uid      Row to move.
	 * @param int                               $new_position 0-based target index.
	 *
	 * @return array<string,array<string,mixed>>|WP_Error
	 */
	private function reorder_zone_rows( array $tree, string $zone, string $row_uid, int $new_position ) {
		// Build a per-zone ordered list of "row groups". Each group is a list
		// of (key, slots) pairs sharing the same row_uid. Keys outside the
		// target zone go into an "other" bucket preserved in their original
		// positions.
		// $zone_groups: list of arrays {uid, pairs} where pairs is a list
		// of (key, slots) tuples sharing the same row_uid. $other_pairs:
		// list of (position_marker, key, slots) tuples for keys outside
		// the target zone — preserved in their original positions.
		$zone_groups = [];
		$current_uid = null;
		$other_pairs = [];
		$counter     = 0;

		foreach ( $tree as $key => $slots ) {
			$key_zone = $this->extract_zone_from_key( (string) $key );
			if ( $key_zone !== $zone ) {
				$other_pairs[] = [ $counter, $key, $slots ];
				++$counter;
				continue;
			}
			$key_row_uid = $this->extract_row_uid_from_key( (string) $key );
			if ( null === $key_row_uid ) {
				// Zone key without a recognisable row_uid — pre-grid storage.
				// Keep at its position via the "other" channel; treat as one
				// row group on its own anchored at counter.
				$other_pairs[] = [ $counter, $key, $slots ];
				++$counter;
				continue;
			}
			if ( $key_row_uid !== $current_uid ) {
				$zone_groups[] = [
					'uid'   => $key_row_uid,
					'pairs' => [],
				];
				$current_uid   = $key_row_uid;
			}
			$zone_groups[ count( $zone_groups ) - 1 ]['pairs'][] = [ $key, $slots ];
			++$counter;
		}

		// Find the target row.
		$target_index = null;
		foreach ( $zone_groups as $idx => $group ) {
			if ( $group['uid'] === $row_uid ) {
				$target_index = $idx;
				break;
			}
		}
		if ( null === $target_index ) {
			return new WP_Error(
				'gv_rest_grid_row_not_found',
				__( 'The grid row couldn\'t be found in the specified zone.', 'gk-gravityview' ),
				[ 'status' => 404 ]
			);
		}

		// Reorder zone_groups.
		$target_group = $zone_groups[ $target_index ];
		array_splice( $zone_groups, $target_index, 1 );
		$insert_at = max( 0, min( $new_position, count( $zone_groups ) ) );
		array_splice( $zone_groups, $insert_at, 0, [ $target_group ] );

		// Rebuild the tree. Out-of-zone keys keep their original position
		// (interleaved via the position_marker counter); in-zone keys emit
		// in the new group order.
		$rebuilt        = [];
		$zone_keys_flat = [];
		foreach ( $zone_groups as $group ) {
			foreach ( $group['pairs'] as $pair ) {
				$zone_keys_flat[] = $pair;
			}
		}
		$zone_idx = 0;

		// Walk by counter: at each counter value, either emit the next
		// other-pair or the next zone-pair (depending on whether the
		// original key at that counter was in or out of zone).
		for ( $i = 0; $i < $counter; $i++ ) {
			$is_other = false;
			foreach ( $other_pairs as $op ) {
				if ( $op[0] === $i ) {
					$is_other          = true;
					$rebuilt[ $op[1] ] = $op[2];
					break;
				}
			}
			if ( ! $is_other && $zone_idx < count( $zone_keys_flat ) ) {
				[ $k, $v ]     = $zone_keys_flat[ $zone_idx ];
				$rebuilt[ $k ] = $v;
				++$zone_idx;
			}
		}

		return $rebuilt;
	}

	/**
	 * Determine whether an item is a patch operation.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Batch item.
	 * @return bool
	 */
	private function is_patch_item( array $item ): bool {
		return array_key_exists( 'row_uid', $item );
	}

	/**
	 * Validate a grid-row add item.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View post id.
	 * @param array $item    Batch item.
	 * @return true|WP_Error
	 */
	private function validate_add_item( int $view_id, array $item ) {
		$surface = $this->resolve_grid_surface( $view_id, $this->surface_from_item( $item ) );
		if ( is_wp_error( $surface ) ) {
			return $surface;
		}

		$type = isset( $item['type'] ) && '' !== (string) $item['type'] ? (string) $item['type'] : '100';
		if ( null === Grid::get_row_type( $type ) ) {
			return new WP_Error(
				'gv_rest_invalid_grid_type',
				sprintf(
					/* translators: %s: requested row type */
					__( 'Unknown grid row type "%s".', 'gk-gravityview' ),
					$type
				),
				[ 'status' => 400 ]
			);
		}

		if ( isset( $item['zones'] ) && ! is_array( $item['zones'] ) ) {
			return $this->invalid_input( __( 'zones must be an array.', 'gk-gravityview' ) );
		}

		return true;
	}

	/**
	 * Validate a grid-row patch item.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View post id.
	 * @param array $item    Batch item.
	 * @param array $trees   Planned surface trees.
	 * @return true|WP_Error
	 */
	private function validate_patch_item( int $view_id, array $item, array $trees ) {
		$surface_id = $this->surface_from_item( $item );
		$surface    = $this->resolve_grid_surface( $view_id, $surface_id );
		if ( is_wp_error( $surface ) ) {
			return $surface;
		}

		$row_uid = isset( $item['row_uid'] ) ? (string) $item['row_uid'] : '';
		$type    = isset( $item['type'] ) ? (string) $item['type'] : '';
		if ( '' === $row_uid || '' === $type ) {
			return $this->invalid_input( __( 'row_uid and type are required.', 'gk-gravityview' ) );
		}

		if ( null === Grid::get_row_type( $type ) ) {
			return new WP_Error(
				'gv_rest_invalid_grid_type',
				sprintf(
					/* translators: %s: requested row type */
					__( 'Unknown grid row type "%s".', 'gk-gravityview' ),
					$type
				),
				[ 'status' => 400 ]
			);
		}

		$tree    = isset( $trees[ $surface_id ] ) && is_array( $trees[ $surface_id ] ) ? $trees[ $surface_id ] : [];
		$touched = $this->count_row_touches( $tree, $row_uid );
		if ( 0 === $touched ) {
			return new WP_Error(
				'gv_rest_grid_row_not_found',
				__( 'Row UID not found in the View configuration.', 'gk-gravityview' ),
				[ 'status' => 404 ]
			);
		}

		return true;
	}

	/**
	 * Plan a grid-row add item.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View post id.
	 * @param array $item    Batch item.
	 * @param array $trees   Planned surface trees.
	 * @param int   $index   Batch item index.
	 * @return array|WP_Error
	 */
	private function plan_add_item( int $view_id, array $item, array $trees, int $index ) {
		$surface = $this->resolve_grid_surface( $view_id, $this->surface_from_item( $item ) );
		if ( is_wp_error( $surface ) ) {
			return $surface;
		}

		$type         = isset( $item['type'] ) && '' !== (string) $item['type'] ? (string) $item['type'] : '100';
		$row_template = Grid::get_row_type( $type );
		if ( null === $row_template ) {
			return new WP_Error( 'gv_rest_invalid_grid_type', __( 'Unknown grid row type.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$zones   = isset( $item['zones'] ) && is_array( $item['zones'] ) && ! empty( $item['zones'] ) ? $item['zones'] : $surface['default_zones'];
		$row_uid = Grid::uid();
		$tree    = isset( $trees[ $surface['id'] ] ) && is_array( $trees[ $surface['id'] ] ) ? $trees[ $surface['id'] ] : [];
		$created = [];
		$skipped = [];

		foreach ( $zones as $zone ) {
			if ( ! is_string( $zone ) || '' === $zone ) {
				continue;
			}
			if ( ! in_array( $zone, $surface['valid_zones'], true ) ) {
				$skipped[ $zone ] = 'invalid-zone-for-surface';
				continue;
			}
			$prefix = $this->grid_prefix_for_surface( $view_id, $surface['id'], $zone );
			if ( null === $prefix ) {
				$skipped[ $zone ] = 'zone-template-not-grid-aware';
				continue;
			}

			$zone_areaids     = Grid::area_keys_for_row( $prefix, $row_template, $type, $row_uid );
			$created[ $zone ] = [];
			foreach ( $zone_areaids as $areaid ) {
				$key = $zone . '_' . $areaid;
				if ( ! isset( $tree[ $key ] ) ) {
					$tree[ $key ] = [];
				}
				$created[ $zone ][] = $areaid;
			}
		}

		if ( empty( $created ) ) {
			return new WP_Error(
				'gv_rest_grid_unsupported',
				__( 'None of the requested zones support grid rows on this surface.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		$trees[ $surface['id'] ] = $tree;

		return [
			'trees'   => $trees,
			'surface' => $surface['id'],
			'result'  => [
				'index'     => $index,
				'operation' => 'add',
				'surface'   => $surface['id'],
				'row_uid'   => $row_uid,
				'type'      => $type,
				'area_keys' => $this->area_keys_from_created( $created ),
				'created'   => $created,
				'skipped'   => $skipped,
			],
		];
	}

	/**
	 * Plan a grid-row patch item.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View post id.
	 * @param array $item    Batch item.
	 * @param array $trees   Planned surface trees.
	 * @param int   $index   Batch item index.
	 * @return array|WP_Error
	 */
	private function plan_patch_item( int $view_id, array $item, array $trees, int $index ) {
		$surface = $this->resolve_grid_surface( $view_id, $this->surface_from_item( $item ) );
		if ( is_wp_error( $surface ) ) {
			return $surface;
		}

		$new_type     = (string) $item['type'];
		$row_uid      = (string) $item['row_uid'];
		$row_template = Grid::get_row_type( $new_type );
		$tree         = isset( $trees[ $surface['id'] ] ) && is_array( $trees[ $surface['id'] ] ) ? $trees[ $surface['id'] ] : [];
		$next         = [];
		$touched      = 0;

		if ( null === $row_template ) {
			return new WP_Error( 'gv_rest_invalid_grid_type', __( 'Unknown grid row type.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		foreach ( $tree as $key => $slots ) {
			if ( ! $this->grid_key_belongs_to_row( (string) $key, $row_uid ) ) {
				$next[ $key ] = $slots;
				continue;
			}
			++$touched;
			$zone   = explode( '_', (string) $key, 2 )[0] ?? '';
			$prefix = $this->grid_prefix_for_surface( $view_id, $surface['id'], $zone );
			if ( null === $prefix ) {
				$next[ $key ] = $slots;
				continue;
			}
			$zone_areaids = Grid::area_keys_for_row( $prefix, $row_template, $new_type, $row_uid );
			if ( empty( $zone_areaids ) ) {
				$next[ $key ] = $slots;
				continue;
			}

			$col_index     = Grid::column_index_from_key( (string) $key, $prefix );
			$target_idx    = min( $col_index, count( $zone_areaids ) - 1 );
			$target_areaid = $zone_areaids[ $target_idx ];
			$target_key    = $zone . '_' . $target_areaid;

			if ( ! isset( $next[ $target_key ] ) ) {
				$next[ $target_key ] = [];
			}
			foreach ( (array) $slots as $slot_uid => $slot ) {
				$next[ $target_key ][ $slot_uid ] = $slot;
			}
		}

		if ( 0 === $touched ) {
			return new WP_Error(
				'gv_rest_grid_row_not_found',
				__( 'Row UID not found in the View configuration.', 'gk-gravityview' ),
				[ 'status' => 404 ]
			);
		}

		$trees[ $surface['id'] ] = $next;

		return [
			'trees'   => $trees,
			'surface' => $surface['id'],
			'result'  => [
				'index'     => $index,
				'operation' => 'patch',
				'surface'   => $surface['id'],
				'row_uid'   => $row_uid,
				'type'      => $new_type,
				'touched'   => $touched,
			],
		];
	}

	/**
	 * Resolve the surface id from an item.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Batch item.
	 * @return string
	 */
	private function surface_from_item( array $item ): string {
		$surface = isset( $item['surface'] ) ? (string) $item['surface'] : 'fields';
		return '' === $surface ? 'fields' : $surface;
	}

	/**
	 * Resolve a grid surface spec.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id    View post id.
	 * @param string $surface_id Surface id.
	 * @return array|WP_Error
	 */
	private function resolve_grid_surface( int $view_id, string $surface_id ) {
		switch ( $surface_id ) {
			case 'fields':
				return [
					'id'            => 'fields',
					'valid_zones'   => [ 'directory', 'single' ],
					'default_zones' => [ 'directory', 'single' ],
				];
			case 'widgets':
				return [
					'id'            => 'widgets',
					'valid_zones'   => self::WIDGET_ZONES,
					'default_zones' => self::WIDGET_ZONES,
				];
		}

		return new WP_Error(
			'gv_rest_invalid_surface',
			sprintf(
				/* translators: %s: requested surface id */
				__( 'Unknown grid surface "%s". Use one of: fields, widgets.', 'gk-gravityview' ),
				$surface_id
			),
			[ 'status' => 400 ]
		);
	}

	/**
	 * Load a grid surface tree.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id View post id.
	 * @param string $surface Surface id.
	 * @return array
	 */
	private function load_surface_tree( int $view_id, string $surface ): array {
		$meta_key = 'widgets' === $surface ? self::WIDGETS_META_KEY : self::FIELDS_META_KEY;
		$tree     = get_post_meta( $view_id, $meta_key, true );

		return is_array( $tree ) ? $tree : [];
	}

	/**
	 * Resolve the grid area prefix for a surface and zone.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id View post id.
	 * @param string $surface Surface id.
	 * @param string $zone    Zone id.
	 * @return string|null
	 */
	private function grid_prefix_for_surface( int $view_id, string $surface, string $zone ): ?string {
		if ( 'widgets' === $surface ) {
			return '';
		}

		$template_id = $this->resolve_template_id( $view_id, $zone );
		$templates   = apply_filters(
			'gk/gravityview/rest/grid-aware-templates',
			[ 'gravityview-layout-builder' ]
		);

		return in_array( $template_id, (array) $templates, true ) ? $template_id : null;
	}

	/**
	 * Resolve a template id for a zone.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id View post id.
	 * @param string $zone    Zone id.
	 * @return string
	 */
	private function resolve_template_id( int $view_id, string $zone ): string {
		$meta_key = 'single' === $zone ? self::SINGLE_TEMPLATE_META_KEY : self::DIRECTORY_TEMPLATE_META_KEY;
		$value    = (string) get_post_meta( $view_id, $meta_key, true );

		if ( '' === $value && 'directory' !== $zone ) {
			$value = (string) get_post_meta( $view_id, self::DIRECTORY_TEMPLATE_META_KEY, true );
		}

		return $value;
	}

	/**
	 * Exact-segment match on the row-UID portion of an area-key.
	 *
	 * Substring match (`strpos($key, '::' . $row_uid)`) over-touches when one
	 * row_uid is a prefix of another (e.g. `row-a` matches `row-ab`). Compare
	 * the final `::`-separated segment instead.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key     Stored area-key.
	 * @param string $row_uid Row UID to test.
	 * @return bool
	 */
	private function grid_key_belongs_to_row( string $key, string $row_uid ): bool {
		$parts = explode( '::', $key );
		$last  = (string) end( $parts );

		return $last === $row_uid;
	}

	/**
	 * Count how many area keys reference a row uid.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $tree    Surface tree.
	 * @param string $row_uid Row uid.
	 * @return int
	 */
	private function count_row_touches( array $tree, string $row_uid ): int {
		$touched = 0;
		foreach ( array_keys( $tree ) as $key ) {
			if ( $this->grid_key_belongs_to_row( (string) $key, $row_uid ) ) {
				++$touched;
			}
		}

		return $touched;
	}

	/**
	 * Build fully qualified area keys from created zone map.
	 *
	 * @since 3.0.0
	 *
	 * @param array $created Created zone map.
	 * @return array
	 */
	private function area_keys_from_created( array $created ): array {
		$area_keys = [];
		foreach ( $created as $zone => $areaids ) {
			foreach ( (array) $areaids as $areaid ) {
				$area_keys[] = $zone . '_' . $areaid;
			}
		}

		return $area_keys;
	}

	/**
	 * Build result-envelope rows from planned items.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id View post id.
	 * @param array  $plans   Planned item metadata.
	 * @param string $version Shared response version.
	 * @return array
	 */
	private function batch_result_rows( int $view_id, array $plans, string $version ): array {
		$rows = [];

		foreach ( $plans as $plan ) {
			$result = [
				'view_id' => $view_id,
				'surface' => (string) $plan['surface'],
				'row_uid' => (string) $plan['row_uid'],
				'slot_id' => (string) $plan['row_uid'],
				'type'    => (string) $plan['type'],
				'version' => $version,
			];

			if ( 'add' === (string) $plan['operation'] ) {
				$result['area_keys'] = (array) $plan['area_keys'];
				$result['created']   = (array) $plan['created'];
				$result['skipped']   = (array) $plan['skipped'];
			} else {
				$result['touched'] = (int) $plan['touched'];
			}

			$rows[] = [
				'index'  => (int) $plan['index'],
				'ok'     => true,
				'result' => $result,
			];
		}

		return $rows;
	}

	/**
	 * Extract the zone prefix from a tree key. Keys look like
	 * `<zone>_<remainder>` — the zone is the part before the FIRST
	 * underscore.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key Tree area-key.
	 *
	 * @return string Zone prefix (empty if not present).
	 */
	private function extract_zone_from_key( string $key ): string {
		$pos = strpos( $key, '_' );
		return false === $pos ? $key : substr( $key, 0, $pos );
	}

	/**
	 * Extract the row_uid from a tree key. Grid keys encode row identity as the
	 * TRAILING `::` segment: `{zone}_{prefix}{-?}{areaid}::{type}::{row_uid}`
	 * (fields and Layout Builder widget surfaces). Legacy non-`::` widget keys
	 * use `<zone>_<row_uid>_<column>_<area>`. Returns null if neither matches.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key Tree area-key.
	 *
	 * @return string|null Row UID, or null.
	 */
	private function extract_row_uid_from_key( string $key ): ?string {
		// row_uid is the last `::` segment — consistent with how keys are built
		// (grid_areaids_for_row) and matched (grid_key_belongs_to_row). A middle
		// `::(x)::` capture returns the TYPE instead, so moves never matched the
		// real row_uid and 404'd.
		if ( false !== strpos( $key, '::' ) ) {
			$parts = explode( '::', $key );
			$last  = (string) end( $parts );
			return '' !== $last ? $last : null;
		}
		// Legacy underscore form `<zone>_<row_uid>_<column>_<area>` (no `::`):
		// row_uid is the second underscore-separated segment.
		$parts = explode( '_', $key );
		if ( count( $parts ) >= 2 && '' !== $parts[1] ) {
			return $parts[1];
		}
		return null;
	}
}
