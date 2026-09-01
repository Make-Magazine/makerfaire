<?php
/**
 * Repository for field slots within a View.
 *
 * @package     GravityKit\GravityView\View\Fields
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View\Fields;

use GravityKit\GravityView\View\AreaSlotIdentity;
use GravityKit\GravityView\View\Concurrency\ViewLock;
use GravityKit\GravityView\View\Concurrency\ViewPreconditionChecker;
use GravityKit\GravityView\View\Concurrency\ViewVersionComputer;
use GravityKit\GravityView\View\Contracts\BatchableSlotRepository;
use GravityKit\GravityView\View\MoveSlotTarget;
use GravityKit\GravityView\View\SlotRepositoryTrait;
use WP_Error;

/**
 * CRUD for field slots — `(view_id, area, slot)` identity.
 *
 * @since 3.0.0
 */
final class FieldSlotRepository implements BatchableSlotRepository {
	use SlotRepositoryTrait;

	private const META_KEY = '_gravityview_directory_fields';

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
	 * Find one field slot.
	 *
	 * @since 3.0.0
	 *
	 * @param AreaSlotIdentity $identity Field slot identity.
	 * @return array<string,mixed>|null
	 */
	public function find( AreaSlotIdentity $identity ): ?array {
		$tree = $this->load_tree( $identity->view_id() );
		$area = $identity->area();
		$slot = $identity->slot();

		return isset( $tree[ $area ][ $slot ] ) && is_array( $tree[ $area ][ $slot ] )
			? $tree[ $area ][ $slot ]
			: null;
	}

	/**
	 * Add a field slot.
	 *
	 * @since 3.0.0
	 *
	 * @param int         $view_id  View post id.
	 * @param string      $area     Field area key.
	 * @param array       $values   Slot values.
	 * @param string|null $if_match Optimistic-concurrency token.
	 * @return array<string,mixed>|WP_Error
	 */
	public function add( int $view_id, string $area, array $values, ?string $if_match ) {
		if ( '' === $area ) {
			return $this->invalid_input( __( 'Field area is required.', 'gk-gravityview' ) );
		}

		return $this->with_lock(
			$view_id,
			function () use ( $view_id, $area, $values, $if_match ) {
				$pre = $this->check_precondition( $view_id, $if_match );
				if ( is_wp_error( $pre ) ) {
					return $pre;
				}

				$tree = $this->load_tree( $view_id );
				if ( ! isset( $tree[ $area ] ) || ! is_array( $tree[ $area ] ) ) {
					$tree[ $area ] = [];
				}

				$slot                   = $this->mint_slot_id( $tree );
				$tree[ $area ][ $slot ] = $this->sanitize_settings( $values );

				update_post_meta( $view_id, self::META_KEY, $tree );

				$version = $this->bump_version( $view_id );
				if ( '' === $version ) {
					return $this->version_bump_failed();
				}

				return [
					'view_id' => $view_id,
					'area'    => $area,
					'slot'    => $slot,
					'values'  => $tree[ $area ][ $slot ],
					'version' => $version,
				];
			}
		);
	}

	/**
	 * Patch a field slot.
	 *
	 * @since 3.0.0
	 *
	 * @param AreaSlotIdentity $identity Field slot identity.
	 * @param array            $settings Settings patch.
	 * @param string|null      $if_match Optimistic-concurrency token.
	 * @return array<string,mixed>|WP_Error
	 */
	public function patch( AreaSlotIdentity $identity, array $settings, ?string $if_match ) {
		$view_id = $identity->view_id();

		return $this->with_lock(
			$view_id,
			function () use ( $identity, $settings, $if_match, $view_id ) {
				$pre = $this->check_precondition( $view_id, $if_match );
				if ( is_wp_error( $pre ) ) {
					return $pre;
				}

				$tree = $this->load_tree( $view_id );
				$area = $identity->area();
				$slot = $identity->slot();

				if ( ! isset( $tree[ $area ][ $slot ] ) || ! is_array( $tree[ $area ][ $slot ] ) ) {
					return $this->slot_not_found( $identity );
				}

				foreach ( $this->sanitize_settings( $settings ) as $key => $value ) {
					if ( null === $value ) {
						unset( $tree[ $area ][ $slot ][ $key ] );
					} else {
						$tree[ $area ][ $slot ][ $key ] = $value;
					}
				}

				update_post_meta( $view_id, self::META_KEY, $tree );

				$version = $this->bump_version( $view_id );
				if ( '' === $version ) {
					return $this->version_bump_failed();
				}

				return [
					'view_id' => $view_id,
					'area'    => $area,
					'slot'    => $slot,
					'values'  => $tree[ $area ][ $slot ],
					'version' => $version,
				];
			}
		);
	}

	/**
	 * Atomically apply field-slot add or patch items.
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

				$tree     = $this->load_tree( $view_id );
				$plans    = [];
				$failures = [];

				foreach ( array_values( $items ) as $index => $item ) {
					if ( ! is_array( $item ) ) {
						$err = $this->invalid_input( __( 'Batch item must be an object.', 'gk-gravityview' ) );
					} elseif ( $this->is_patch_item( $item ) ) {
						$err = $this->validate_patch_item( $view_id, $item, $tree );
					} else {
						$err = $this->validate_add_item( $item );
					}

					if ( is_wp_error( $err ) ) {
						$failures[] = $this->batch_failure( $index, $err );
						continue;
					}

					$plan = $this->is_patch_item( $item )
						? $this->plan_patch_item( $item, $tree, $index )
						: $this->plan_add_item( $item, $tree, $index );

					if ( is_wp_error( $plan ) ) {
						$failures[] = $this->batch_failure( $index, $plan );
						continue;
					}

					$tree    = $plan['tree'];
					$plans[] = $plan['result'];
				}

				if ( ! empty( $failures ) ) {
					return $this->batch_validation_failed( $failures, count( $items ) );
				}

				$version = $current_version;
				if ( ! $dry_run ) {
					$persist = $this->persist_meta( $view_id, self::META_KEY, $tree );
					if ( is_wp_error( $persist ) ) {
						return $persist;
					}
					if ( true === $persist ) {
						$version = $this->bump_version( $view_id );
						if ( '' === $version ) {
							return $this->version_bump_failed();
						}
					}
				}

				return $this->batch_result_rows( $view_id, $tree, $plans, $version );
			}
		);
	}

	/**
	 * Remove a field slot.
	 *
	 * @since 3.0.0
	 *
	 * @param AreaSlotIdentity $identity Field slot identity.
	 * @param string|null      $if_match Optimistic-concurrency token.
	 * @return array<string,mixed>|WP_Error
	 */
	public function remove( AreaSlotIdentity $identity, ?string $if_match ) {
		$view_id = $identity->view_id();

		return $this->with_lock(
			$view_id,
			function () use ( $identity, $if_match, $view_id ) {
				$pre = $this->check_precondition( $view_id, $if_match );
				if ( is_wp_error( $pre ) ) {
					return $pre;
				}

				$tree = $this->load_tree( $view_id );
				$area = $identity->area();
				$slot = $identity->slot();

				if ( ! isset( $tree[ $area ][ $slot ] ) ) {
					return $this->slot_not_found( $identity );
				}

				unset( $tree[ $area ][ $slot ] );
				if ( empty( $tree[ $area ] ) ) {
					unset( $tree[ $area ] );
				}

				update_post_meta( $view_id, self::META_KEY, $tree );

				$version = $this->bump_version( $view_id );
				if ( '' === $version ) {
					return $this->version_bump_failed();
				}

				return [
					'view_id' => $view_id,
					'area'    => $area,
					'slot'    => $slot,
					'version' => $version,
				];
			}
		);
	}

	/**
	 * Move a field slot.
	 *
	 * @since 3.0.0
	 *
	 * @param AreaSlotIdentity $from     Source field slot.
	 * @param MoveSlotTarget   $target   Move target.
	 * @param string|null      $if_match Optimistic-concurrency token.
	 * @return array<string,mixed>|WP_Error
	 */
	public function move( AreaSlotIdentity $from, MoveSlotTarget $target, ?string $if_match ) {
		$view_id = $from->view_id();

		return $this->with_lock(
			$view_id,
			function () use ( $from, $target, $if_match, $view_id ) {
				$pre = $this->check_precondition( $view_id, $if_match );
				if ( is_wp_error( $pre ) ) {
					return $pre;
				}

				$tree      = $this->load_tree( $view_id );
				$from_area = $from->area();
				$slot      = $from->slot();

				if ( ! isset( $tree[ $from_area ][ $slot ] ) ) {
					return $this->slot_not_found( $from );
				}

				$field = $tree[ $from_area ][ $slot ];
				unset( $tree[ $from_area ][ $slot ] );
				if ( empty( $tree[ $from_area ] ) ) {
					unset( $tree[ $from_area ] );
				}

				$to_area = $target->to_area();
				if ( ! isset( $tree[ $to_area ] ) || ! is_array( $tree[ $to_area ] ) ) {
					$tree[ $to_area ] = [];
				}

				$moved = $this->insert_slot( $tree[ $to_area ], $slot, $field, $target );
				if ( is_wp_error( $moved ) ) {
					return $moved;
				}
				$tree[ $to_area ] = $moved;

				update_post_meta( $view_id, self::META_KEY, $tree );

				$version = $this->bump_version( $view_id );
				if ( '' === $version ) {
					return $this->version_bump_failed();
				}

				return [
					'view_id' => $view_id,
					'from'    => [
						'area' => $from_area,
						'slot' => $slot,
					],
					'to'      => [
						'area' => $to_area,
						'slot' => $slot,
					],
					'version' => $version,
				];
			}
		);
	}

	/**
	 * Load the field tree.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View post id.
	 * @return array<string,array<string,mixed>>
	 */
	private function load_tree( int $view_id ): array {
		$tree = get_post_meta( $view_id, self::META_KEY, true );

		return is_array( $tree ) ? $tree : [];
	}

	/**
	 * Sanitize a settings payload before persistence.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings Raw settings.
	 * @return array
	 */
	private function sanitize_settings( array $settings ): array {
		return \GravityKit\GravityView\View\Sanitization\SettingValueSanitizer::sanitize_settings( $settings );
	}

	/**
	 * Mint a new field-slot UID.
	 *
	 * Returns a UUID v4 — matches InspectorRoute::next_slot_uid() so slot IDs
	 * are uniform across REST and repository write paths and across singleton
	 * and batch calls.
	 *
	 * @since 3.0.0
	 *
	 * @param array $collection Current field tree.
	 * @return string
	 */
	private function mint_slot_id( array $collection ): string {
		do {
			$slot = wp_generate_uuid4();
		} while ( $this->slot_exists_in_tree( $collection, $slot ) );

		return $slot;
	}

	/**
	 * Check whether a slot id exists anywhere in the tree.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $tree Field tree.
	 * @param string $slot Slot id.
	 * @return bool
	 */
	private function slot_exists_in_tree( array $tree, string $slot ): bool {
		foreach ( $tree as $area_slots ) {
			if ( is_array( $area_slots ) && array_key_exists( $slot, $area_slots ) ) {
				return true;
			}
		}

		return false;
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
		return array_key_exists( 'slot', $item )
			|| ( array_key_exists( 'settings', $item ) && ! array_key_exists( 'field_id', $item ) );
	}

	/**
	 * Validate a field add item.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Batch item.
	 * @return true|WP_Error
	 */
	private function validate_add_item( array $item ) {
		if ( empty( $item['area'] ) || ! is_string( $item['area'] ) ) {
			return $this->invalid_input( __( 'Field area is required.', 'gk-gravityview' ) );
		}

		if ( empty( $item['field_id'] ) || ! is_string( $item['field_id'] ) ) {
			return new WP_Error(
				'gv_rest_missing_field_id',
				__( 'field_id is required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		if ( isset( $item['settings'] ) && ! is_array( $item['settings'] ) ) {
			return $this->invalid_input( __( 'settings must be an object.', 'gk-gravityview' ) );
		}

		return true;
	}

	/**
	 * Validate a field patch item against the planned tree.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Batch item.
	 * @param array $tree Planned field tree.
	 * @return true|WP_Error
	 */
	private function validate_patch_item( int $view_id, array $item, array $tree ) {
		$area = isset( $item['area'] ) ? (string) $item['area'] : '';
		$slot = isset( $item['slot'] ) ? (string) $item['slot'] : '';

		if ( '' === $area || '' === $slot ) {
			return $this->invalid_input( __( 'area and slot are required.', 'gk-gravityview' ) );
		}

		if ( ! isset( $item['settings'] ) || ! is_array( $item['settings'] ) ) {
			return $this->invalid_input( __( 'settings must be an object.', 'gk-gravityview' ) );
		}

		if ( ! isset( $tree[ $area ][ $slot ] ) || ! is_array( $tree[ $area ][ $slot ] ) ) {
			return $this->slot_not_found( new AreaSlotIdentity( $view_id, $area, $slot ) );
		}

		return true;
	}

	/**
	 * Plan a field add item.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item  Batch item.
	 * @param array $tree  Planned field tree.
	 * @param int   $index Batch item index.
	 * @return array|WP_Error
	 */
	private function plan_add_item( array $item, array $tree, int $index ) {
		$area = (string) $item['area'];
		if ( ! isset( $tree[ $area ] ) || ! is_array( $tree[ $area ] ) ) {
			$tree[ $area ] = [];
		}

		$slot   = $this->mint_slot_id( $tree );
		$values = $this->field_add_values( $item );

		$tree[ $area ][ $slot ] = $this->sanitize_settings( $values );

		return [
			'tree'   => $tree,
			'result' => [
				'index' => $index,
				'area'  => $area,
				'slot'  => $slot,
			],
		];
	}

	/**
	 * Plan a field patch item.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item  Batch item.
	 * @param array $tree  Planned field tree.
	 * @param int   $index Batch item index.
	 * @return array|WP_Error
	 */
	private function plan_patch_item( array $item, array $tree, int $index ) {
		$area = (string) $item['area'];
		$slot = (string) $item['slot'];

		foreach ( $this->sanitize_settings( (array) $item['settings'] ) as $key => $value ) {
			if ( null === $value ) {
				unset( $tree[ $area ][ $slot ][ $key ] );
			} else {
				$tree[ $area ][ $slot ][ $key ] = $value;
			}
		}

		return [
			'tree'   => $tree,
			'result' => [
				'index' => $index,
				'area'  => $area,
				'slot'  => $slot,
			],
		];
	}

	/**
	 * Build persisted values for an add item.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Batch item.
	 * @return array
	 */
	private function field_add_values( array $item ): array {
		$values = [
			'id' => (string) $item['field_id'],
		];

		if ( isset( $item['label'] ) ) {
			$values['label'] = (string) $item['label'];
		}

		if ( isset( $item['settings'] ) && is_array( $item['settings'] ) ) {
			$values = array_merge( $values, $item['settings'] );
		}

		return $values;
	}

	/**
	 * Build result-envelope rows from planned items.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id View post id.
	 * @param array  $tree    Final field tree.
	 * @param array  $plans   Planned item metadata.
	 * @param string $version Shared response version.
	 * @return array
	 */
	private function batch_result_rows( int $view_id, array $tree, array $plans, string $version ): array {
		$rows = [];

		foreach ( $plans as $plan ) {
			$area   = (string) $plan['area'];
			$slot   = (string) $plan['slot'];
			$rows[] = [
				'index'  => (int) $plan['index'],
				'ok'     => true,
				'result' => [
					'view_id' => $view_id,
					'area'    => $area,
					'slot'    => $slot,
					'slot_id' => $slot,
					'values'  => isset( $tree[ $area ][ $slot ] ) && is_array( $tree[ $area ][ $slot ] ) ? $tree[ $area ][ $slot ] : [],
					'version' => $version,
				],
			];
		}

		return $rows;
	}

	/**
	 * Insert a slot into an ordered area collection.
	 *
	 * @since 3.0.0
	 *
	 * @param array          $area_slots Area collection.
	 * @param string         $slot       Slot id.
	 * @param mixed          $value      Slot value.
	 * @param MoveSlotTarget $target     Move target.
	 * @return array|WP_Error
	 */
	private function insert_slot( array $area_slots, string $slot, $value, MoveSlotTarget $target ) {
		$position = $target->position();

		if ( null !== $target->before_slot() ) {
			if ( ! array_key_exists( $target->before_slot(), $area_slots ) ) {
				return $this->anchor_not_found( $target->before_slot() );
			}

			return $this->insert_before_key( $area_slots, $target->before_slot(), $slot, $value );
		}

		if ( null !== $target->after_slot() ) {
			if ( ! array_key_exists( $target->after_slot(), $area_slots ) ) {
				return $this->anchor_not_found( $target->after_slot() );
			}

			return $this->insert_after_key( $area_slots, $target->after_slot(), $slot, $value );
		}

		if ( MoveSlotTarget::POSITION_START === $position ) {
			return [ $slot => $value ] + $area_slots;
		}

		if ( is_int( $position ) ) {
			$position = max( 0, min( $position, count( $area_slots ) ) );

			return array_slice( $area_slots, 0, $position, true )
				+ [ $slot => $value ]
				+ array_slice( $area_slots, $position, null, true );
		}

		$area_slots[ $slot ] = $value;

		return $area_slots;
	}

	/**
	 * Insert before a given key.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $items      Items.
	 * @param string $before_key Anchor key.
	 * @param string $key        New key.
	 * @param mixed  $value      New value.
	 * @return array
	 */
	private function insert_before_key( array $items, string $before_key, string $key, $value ): array {
		$rebuilt = [];
		foreach ( $items as $item_key => $item_value ) {
			if ( $item_key === $before_key ) {
				$rebuilt[ $key ] = $value;
			}
			$rebuilt[ $item_key ] = $item_value;
		}

		return $rebuilt;
	}

	/**
	 * Insert after a given key.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $items     Items.
	 * @param string $after_key Anchor key.
	 * @param string $key       New key.
	 * @param mixed  $value     New value.
	 * @return array
	 */
	private function insert_after_key( array $items, string $after_key, string $key, $value ): array {
		$rebuilt = [];
		foreach ( $items as $item_key => $item_value ) {
			$rebuilt[ $item_key ] = $item_value;
			if ( $item_key === $after_key ) {
				$rebuilt[ $key ] = $value;
			}
		}

		return $rebuilt;
	}

	/**
	 * Build a not-found error for a missing slot.
	 *
	 * @since 3.0.0
	 *
	 * @param AreaSlotIdentity $identity Slot identity.
	 * @return WP_Error
	 */
	private function slot_not_found( AreaSlotIdentity $identity ): WP_Error {
		return new WP_Error(
			'gv_rest_field_slot_not_found',
			__( 'Field slot not found.', 'gk-gravityview' ),
			[
				'status'  => 404,
				'view_id' => $identity->view_id(),
				'area'    => $identity->area(),
				'slot'    => $identity->slot(),
			]
		);
	}

	/**
	 * Build a not-found error for a missing move anchor.
	 *
	 * @since 3.0.0
	 *
	 * @param string|null $slot Slot id.
	 * @return WP_Error
	 */
	private function anchor_not_found( ?string $slot ): WP_Error {
		return new WP_Error(
			'gv_rest_field_anchor_not_found',
			__( 'The requested field anchor slot could not be found.', 'gk-gravityview' ),
			[
				'status' => 404,
				'slot'   => $slot,
			]
		);
	}

}
