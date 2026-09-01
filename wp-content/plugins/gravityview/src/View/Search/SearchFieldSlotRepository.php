<?php
/**
 * Search-field-slot repository — CRUD inside a search_bar widget's
 * `search_fields_section`.
 *
 * @package     GravityKit\GravityView\View\Search
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View\Search;

use GravityKit\GravityView\View\Concurrency\ViewLock;
use GravityKit\GravityView\View\Concurrency\ViewPreconditionChecker;
use GravityKit\GravityView\View\Concurrency\ViewVersionComputer;
use GravityKit\GravityView\Renderer\Grid;
use GravityKit\GravityView\Search\SearchFieldResolver;
use GravityKit\GravityView\View\Contracts\BatchableSlotRepository;
use GravityKit\GravityView\View\SearchFieldIdentity;
use GravityKit\GravityView\View\SlotRepositoryTrait;
use WP_Error;

/**
 * Manages search fields nested inside a search_bar widget. Search fields
 * use 5-piece identity `(view_id, widget_area, widget_slot, position,
 * search_slot)` rather than the (area, slot) used by SlotRepository.
 *
 * Storage: `_gravityview_directory_widgets[widget_area][widget_slot]`
 * is the search_bar widget record. Inside that record,
 * `search_fields_section[position][search_slot]` is the search-field
 * map (canonical modern shape; legacy `search_fields` JSON migrates to
 * this on first write through the inspector surface).
 *
 * @since 3.0.0
 */
final class SearchFieldSlotRepository implements BatchableSlotRepository {
	use SlotRepositoryTrait;

	private const WIDGETS_META_KEY = '_gravityview_directory_widgets';

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
	 * Atomically apply search-field add or patch items.
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

				$widgets = get_post_meta( $view_id, self::WIDGETS_META_KEY, true );
				if ( ! is_array( $widgets ) ) {
					$widgets = [];
				}

				$plans    = [];
				$failures = [];

				foreach ( array_values( $items ) as $index => $item ) {
					if ( ! is_array( $item ) ) {
						$err = $this->invalid_input( __( 'Batch item must be an object.', 'gk-gravityview' ) );
					} elseif ( $this->is_patch_item( $item ) ) {
						$err = $this->validate_patch_item( $view_id, $item, $widgets );
					} else {
						$err = $this->validate_add_item( $view_id, $item, $widgets );
					}

					if ( is_wp_error( $err ) ) {
						$failures[] = $this->batch_failure( $index, $err );
						continue;
					}

					$plan = $this->is_patch_item( $item )
						? $this->plan_patch_item( $view_id, $item, $widgets, $index )
						: $this->plan_add_item( $view_id, $item, $widgets, $index );

					if ( is_wp_error( $plan ) ) {
						$failures[] = $this->batch_failure( $index, $plan );
						continue;
					}

					$widgets = $plan['widgets'];
					$plans[] = $plan['result'];
				}

				if ( ! empty( $failures ) ) {
					return $this->batch_validation_failed( $failures, count( $items ) );
				}

				$version = $current_version;
				if ( ! $dry_run ) {
					$persist = $this->persist_meta( $view_id, self::WIDGETS_META_KEY, $widgets );
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

				return $this->batch_result_rows( $view_id, $widgets, $plans, $version );
			}
		);
	}

	/**
	 * Move a search field within or across position buckets in the same
	 * search_bar widget.
	 *
	 * @since 3.0.0
	 *
	 * @param SearchFieldIdentity $from           Source identity.
	 * @param string              $to_position    Destination position bucket.
	 * @param string|null         $to_search_slot Optional anchor inside to_position;
	 *                                            if set, the moved field is inserted
	 *                                            BEFORE this search_slot. If null,
	 *                                            the field appends.
	 * @param string|null         $if_match       Optimistic-concurrency token.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function move( SearchFieldIdentity $from, string $to_position, ?string $to_search_slot, ?string $if_match ) {
		$view_id = $from->view_id();

		if ( '' === $to_position ) {
			return new WP_Error(
				'gv_rest_invalid_input',
				__( 'to_position is required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		return $this->with_lock(
			$view_id,
			function () use ( $from, $to_position, $to_search_slot, $if_match, $view_id ) {
			$pre = $this->check_precondition( $view_id, $if_match );
			if ( is_wp_error( $pre ) ) {
				return $pre;
			}

			$widgets = get_post_meta( $view_id, self::WIDGETS_META_KEY, true );
			if ( ! is_array( $widgets ) ) {
				$widgets = [];
			}

			$widget_area = $from->widget_area();
			$widget_slot = $from->widget_slot();

			if ( ! isset( $widgets[ $widget_area ][ $widget_slot ] ) ) {
				return new WP_Error(
					'gv_rest_widget_not_found',
					__( 'The search_bar widget couldn\'t be found at the given location.', 'gk-gravityview' ),
					[ 'status' => 404 ]
				);
			}

			$widget = (array) $widgets[ $widget_area ][ $widget_slot ];
			if ( 'search_bar' !== ( $widget['id'] ?? '' ) ) {
				return new WP_Error(
					'gv_rest_not_search_bar',
					__( 'The targeted widget is not a search_bar.', 'gk-gravityview' ),
					[ 'status' => 400 ]
				);
			}

			$section = isset( $widget['search_fields_section'] ) && is_array( $widget['search_fields_section'] )
				? $widget['search_fields_section']
				: [];

			$from_position    = $from->position();
			$from_search_slot = $from->search_slot();

			if ( ! isset( $section[ $from_position ][ $from_search_slot ] ) ) {
				return new WP_Error(
					'gv_rest_search_field_not_found',
					__( 'The search field couldn\'t be found at the given position.', 'gk-gravityview' ),
					[ 'status' => 404 ]
				);
			}

			$field = $section[ $from_position ][ $from_search_slot ];

			// Remove from source bucket.
			unset( $section[ $from_position ][ $from_search_slot ] );
			if ( empty( $section[ $from_position ] ) ) {
				unset( $section[ $from_position ] );
			}

			// Ensure destination bucket exists.
			if ( ! isset( $section[ $to_position ] ) || ! is_array( $section[ $to_position ] ) ) {
				$section[ $to_position ] = [];
			}

			// Insert into destination — before the anchor if provided, else append.
			if ( null !== $to_search_slot && isset( $section[ $to_position ][ $to_search_slot ] ) ) {
				$rebuilt = [];
				foreach ( $section[ $to_position ] as $key => $value ) {
					if ( $key === $to_search_slot ) {
						$rebuilt[ $from_search_slot ] = $field;
					}
					$rebuilt[ $key ] = $value;
				}
				$section[ $to_position ] = $rebuilt;
			} else {
				$section[ $to_position ][ $from_search_slot ] = $field;
			}

			$widget['search_fields_section']         = $section;
			$widgets[ $widget_area ][ $widget_slot ] = $widget;

			update_post_meta( $view_id, self::WIDGETS_META_KEY, $widgets );

			$bumped = $this->bump_version( $view_id );
			if ( '' === $bumped ) {
				return $this->version_bump_failed( __( 'The search field was moved but the View couldn\'t be marked updated.', 'gk-gravityview' ) );
			}

			return [
				'view_id'     => $view_id,
				'widget_area' => $widget_area,
				'widget_slot' => $widget_slot,
				'position'    => $to_position,
				'search_slot' => $from_search_slot,
				'version'     => $bumped,
			];
			}
		);
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
		return array_key_exists( 'search_slot', $item )
			|| ( array_key_exists( 'settings', $item ) && ! array_key_exists( 'field', $item ) );
	}

	/**
	 * Validate a search-field add item.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View post id.
	 * @param array $item    Batch item.
	 * @param array $widgets Planned widget tree.
	 * @return true|WP_Error
	 */
	private function validate_add_item( int $view_id, array $item, array $widgets ) {
		$target = $this->validate_search_target( $item, $widgets );
		if ( is_wp_error( $target ) ) {
			return $target;
		}

		$position = isset( $item['position'] ) ? (string) $item['position'] : '';
		$field    = isset( $item['field'] ) && is_array( $item['field'] ) ? $item['field'] : [];

		if ( '' === $position ) {
			return new WP_Error(
				'gv_rest_missing_position',
				__( 'position is required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		if ( empty( $field ) || empty( $field['id'] ) ) {
			return new WP_Error(
				'gv_rest_invalid_field',
				__( 'field must be an object with at least an id.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		// Resolve + form-scope the field and capture its effective (qualified)
		// identity — the same gate gv_search_bar_add and the singleton endpoint
		// apply, so a batch can't bind an unresolvable / foreign-form field.
		$effective_id = $this->resolve_searchable_field_id( $view_id, $field );
		if ( is_wp_error( $effective_id ) ) {
			return $effective_id;
		}

		$input_error = $this->validate_search_input( $view_id, $effective_id, $field );
		if ( is_wp_error( $input_error ) ) {
			return $input_error;
		}

		$slot = isset( $item['slot'] ) ? (string) $item['slot'] : '';
		if ( '' !== $slot ) {
			$section = $this->search_section_for_target( $item, $widgets );
			if ( isset( $section[ $position ][ $slot ] ) ) {
				return new WP_Error(
					'gv_rest_search_slot_exists',
					__( 'Search slot already exists at the given position.', 'gk-gravityview' ),
					[ 'status' => 400 ]
				);
			}
		}

		return true;
	}

	/**
	 * Validate a search-field patch item.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View post id.
	 * @param array $item    Batch item.
	 * @param array $widgets Planned widget tree.
	 * @return true|WP_Error
	 */
	private function validate_patch_item( int $view_id, array $item, array $widgets ) {
		$target = $this->validate_search_target( $item, $widgets );
		if ( is_wp_error( $target ) ) {
			return $target;
		}

		$position    = isset( $item['position'] ) ? (string) $item['position'] : '';
		$search_slot = isset( $item['search_slot'] ) ? (string) $item['search_slot'] : '';
		$settings    = isset( $item['settings'] ) && is_array( $item['settings'] ) ? $item['settings'] : null;

		if ( '' === $position || '' === $search_slot ) {
			return $this->invalid_input( __( 'position and search_slot are required.', 'gk-gravityview' ) );
		}

		if ( empty( $settings ) ) {
			return new WP_Error(
				'gv_rest_empty_settings',
				__( 'settings must be a non-empty object.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		$section = $this->search_section_for_target( $item, $widgets );
		if ( ! isset( $section[ $position ][ $search_slot ] ) || ! is_array( $section[ $position ][ $search_slot ] ) ) {
			return new WP_Error(
				'gv_rest_search_slot_not_found',
				__( 'Search slot not found at the given position.', 'gk-gravityview' ),
				[ 'status' => 404 ]
			);
		}

		$existing = (array) $section[ $position ][ $search_slot ];
		// Narrow against the slot's CANONICAL stored identity (qualified
		// {form_id}::id for GF / joined fields) so a joined-form slot is validated
		// against its real form, not the View's primary form.
		$canonical_id = $this->canonical_stored_field_id( $existing );
		$input_error  = $this->validate_search_input( $view_id, $canonical_id, $settings );
		if ( is_wp_error( $input_error ) ) {
			return $input_error;
		}

		return true;
	}

	/**
	 * Plan a search-field add item.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View post id.
	 * @param array $item    Batch item.
	 * @param array $widgets Planned widget tree.
	 * @param int   $index   Batch item index.
	 * @return array|WP_Error
	 */
	private function plan_add_item( int $view_id, array $item, array $widgets, int $index ) {
		$widget_area = (string) $item['widget_area'];
		$widget_slot = (string) $item['widget_slot'];
		$position    = (string) $item['position'];
		$search_slot = isset( $item['slot'] ) && '' !== (string) $item['slot']
			? (string) $item['slot']
			: $this->mint_search_slot_id( $widgets, $widget_area, $widget_slot );

		$widget  = (array) $widgets[ $widget_area ][ $widget_slot ];
		$section = isset( $widget['search_fields_section'] ) && is_array( $widget['search_fields_section'] )
			? $widget['search_fields_section']
			: [];

		if ( ! isset( $section[ $position ] ) || ! is_array( $section[ $position ] ) ) {
			$section[ $position ] = [];
		}

		$section[ $position ][ $search_slot ] = $this->normalize_search_field_payload(
			(array) $item['field'],
			$view_id
		);

		$widget['search_fields_section']         = $section;
		unset( $widget['search_fields'] );
		$widgets[ $widget_area ][ $widget_slot ] = $widget;

		return [
			'widgets' => $widgets,
			'result'  => [
				'index'       => $index,
				'widget_area' => $widget_area,
				'widget_slot' => $widget_slot,
				'position'    => $position,
				'search_slot' => $search_slot,
			],
		];
	}

	/**
	 * Plan a search-field patch item.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View post id.
	 * @param array $item    Batch item.
	 * @param array $widgets Planned widget tree.
	 * @param int   $index   Batch item index.
	 * @return array|WP_Error
	 */
	private function plan_patch_item( int $view_id, array $item, array $widgets, int $index ) {
		$widget_area = (string) $item['widget_area'];
		$widget_slot = (string) $item['widget_slot'];
		$position    = (string) $item['position'];
		$search_slot = (string) $item['search_slot'];
		$widget      = (array) $widgets[ $widget_area ][ $widget_slot ];
		$section     = isset( $widget['search_fields_section'] ) && is_array( $widget['search_fields_section'] )
			? $widget['search_fields_section']
			: [];
		$existing    = (array) $section[ $position ][ $search_slot ];

		foreach ( (array) $item['settings'] as $key => $value ) {
			$key = sanitize_key( $key );
			if ( '' === $key ) {
				continue;
			}
			// Route values through the shared sanitizer so search-field settings
			// get the same XSS / control-char strip Field + Widget settings do.
			// SettingValueSanitizer preserves null as a delete-this-key sentinel.
			$value = \GravityKit\GravityView\View\Sanitization\SettingValueSanitizer::sanitize( $value );
			if ( null === $value ) {
				unset( $existing[ $key ] );
			} else {
				$existing[ $key ] = $value;
			}
		}

		$section[ $position ][ $search_slot ] = $this->normalize_search_field_payload( $existing, $view_id );

		$widget['search_fields_section']         = $section;
		unset( $widget['search_fields'] );
		$widgets[ $widget_area ][ $widget_slot ] = $widget;

		return [
			'widgets' => $widgets,
			'result'  => [
				'index'       => $index,
				'widget_area' => $widget_area,
				'widget_slot' => $widget_slot,
				'position'    => $position,
				'search_slot' => $search_slot,
			],
		];
	}

	/**
	 * Validate the target search_bar widget.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item    Batch item.
	 * @param array $widgets Planned widget tree.
	 * @return true|WP_Error
	 */
	private function validate_search_target( array $item, array $widgets ) {
		$widget_area = isset( $item['widget_area'] ) ? (string) $item['widget_area'] : '';
		$widget_slot = isset( $item['widget_slot'] ) ? (string) $item['widget_slot'] : '';

		if ( '' === $widget_area || '' === $widget_slot ) {
			return new WP_Error(
				'gv_rest_missing_widget_target',
				__( 'widget_area and widget_slot are required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		if ( ! isset( $widgets[ $widget_area ][ $widget_slot ] ) ) {
			return new WP_Error(
				'gv_rest_widget_not_found',
				__( 'Widget slot not found at the given area.', 'gk-gravityview' ),
				[ 'status' => 404 ]
			);
		}

		$widget = (array) $widgets[ $widget_area ][ $widget_slot ];
		if ( 'search_bar' !== ( $widget['id'] ?? '' ) ) {
			return new WP_Error(
				'gv_rest_not_search_bar',
				__( 'Targeted widget is not a search_bar.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		return true;
	}

	/**
	 * Get the search_fields_section for an item target.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item    Batch item.
	 * @param array $widgets Planned widget tree.
	 * @return array
	 */
	private function search_section_for_target( array $item, array $widgets ): array {
		$widget_area = (string) $item['widget_area'];
		$widget_slot = (string) $item['widget_slot'];
		$widget      = isset( $widgets[ $widget_area ][ $widget_slot ] ) && is_array( $widgets[ $widget_area ][ $widget_slot ] )
			? $widgets[ $widget_area ][ $widget_slot ]
			: [];

		return isset( $widget['search_fields_section'] ) && is_array( $widget['search_fields_section'] )
			? $widget['search_fields_section']
			: [];
	}

	/**
	 * Validate a search input slug when present.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id  View post id.
	 * @param string $field_id Search field id.
	 * @param array  $values   Incoming field or settings values.
	 * @return null|WP_Error
	 */
	private function validate_search_input( int $view_id, string $field_id, array $values ) {
		$input = '';
		if ( isset( $values['input'] ) && '' !== (string) $values['input'] ) {
			$input = (string) $values['input'];
		} elseif ( isset( $values['input_type'] ) && '' !== (string) $values['input_type'] ) {
			$input = (string) $values['input_type'];
		}

		if ( '' === $input ) {
			return null;
		}

		$allowed = $this->valid_search_input_types_for_field( $view_id, $field_id );
		if ( in_array( $input, $allowed, true ) ) {
			return null;
		}

		return new WP_Error(
			'gv_rest_invalid_search_input',
			sprintf(
				/* translators: 1: rejected input slug, 2: field id, 3: comma-separated list of valid slugs */
				__( 'Search input "%1$s" is not allowed for field "%2$s". Allowed for this field: %3$s.', 'gk-gravityview' ),
				$input,
				$field_id,
				implode( ', ', $allowed )
			),
			[ 'status' => 400 ]
		);
	}

	/**
	 * Resolve valid input types for a search field id.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id  View post id.
	 * @param string $field_id Search field id.
	 * @return array
	 */
	/**
	 * Resolve + form-scope a caller-supplied field, returning its effective
	 * (qualified) identity or a WP_Error. Delegates to the shared InspectorRoute
	 * gate so the batch path enforces the same authorization as the singleton
	 * endpoints and gv_search_bar_add.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View post id.
	 * @param array $field   Caller-supplied search-field payload.
	 * @return string|WP_Error
	 */
	private function resolve_searchable_field_id( int $view_id, array $field ) {
		return ( new SearchFieldResolver( $view_id ) )->resolve_searchable_id( $field );
	}

	/**
	 * Canonical (qualified) identity of an already-stored slot, for input
	 * narrowing on patch.
	 *
	 * @since 3.0.0
	 *
	 * @param array $stored Stored search-field slot.
	 * @return string
	 */
	private function canonical_stored_field_id( array $stored ): string {
		return SearchFieldResolver::effective_id( $stored );
	}

	/**
	 * Valid input types for a search field id.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id  View post id.
	 * @param string $field_id Search field id.
	 * @return string[]
	 */
	private function valid_search_input_types_for_field( int $view_id, string $field_id ): array {
		return ( new SearchFieldResolver( $view_id ) )->valid_input_types( $field_id );
	}

	/**
	 * Normalize a search field payload before persistence — canonicalises core
	 * keys while PRESERVING unknown add-on per-field settings, so the batch path
	 * never silently drops them.
	 *
	 * @since 3.0.0
	 *
	 * @param array $field   Field settings.
	 * @param int   $view_id View post id.
	 * @return array
	 */
	private function normalize_search_field_payload( array $field, int $view_id ): array {
		return ( new SearchFieldResolver( $view_id ) )->normalize( $field );
	}

	/**
	 * Mint a unique search slot UID using the canonical grid generator.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $widgets     Planned widget tree.
	 * @param string $widget_area Widget area.
	 * @param string $widget_slot Widget slot.
	 * @return string
	 */
	private function mint_search_slot_id( array $widgets, string $widget_area, string $widget_slot ): string {
		do {
			$slot = Grid::uid();
		} while ( $this->search_slot_exists( $widgets, $widget_area, $widget_slot, $slot ) );

		return $slot;
	}

	/**
	 * Check whether a search slot exists in the target widget.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $widgets     Planned widget tree.
	 * @param string $widget_area Widget area.
	 * @param string $widget_slot Widget slot.
	 * @param string $slot        Search slot.
	 * @return bool
	 */
	private function search_slot_exists( array $widgets, string $widget_area, string $widget_slot, string $slot ): bool {
		if ( ! isset( $widgets[ $widget_area ][ $widget_slot ] ) || ! is_array( $widgets[ $widget_area ][ $widget_slot ] ) ) {
			return false;
		}

		$section = isset( $widgets[ $widget_area ][ $widget_slot ]['search_fields_section'] ) && is_array( $widgets[ $widget_area ][ $widget_slot ]['search_fields_section'] )
			? $widgets[ $widget_area ][ $widget_slot ]['search_fields_section']
			: [];

		foreach ( $section as $row ) {
			if ( is_array( $row ) && array_key_exists( $slot, $row ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Build result-envelope rows from planned items.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id View post id.
	 * @param array  $widgets Final widget tree.
	 * @param array  $plans   Planned item metadata.
	 * @param string $version Shared response version.
	 * @return array
	 */
	private function batch_result_rows( int $view_id, array $widgets, array $plans, string $version ): array {
		$rows = [];

		foreach ( $plans as $plan ) {
			$widget_area = (string) $plan['widget_area'];
			$widget_slot = (string) $plan['widget_slot'];
			$position    = (string) $plan['position'];
			$search_slot = (string) $plan['search_slot'];
			$field       = isset( $widgets[ $widget_area ][ $widget_slot ]['search_fields_section'][ $position ][ $search_slot ] )
				&& is_array( $widgets[ $widget_area ][ $widget_slot ]['search_fields_section'][ $position ][ $search_slot ] )
				? $widgets[ $widget_area ][ $widget_slot ]['search_fields_section'][ $position ][ $search_slot ]
				: [];

			$rows[] = [
				'index'  => (int) $plan['index'],
				'ok'     => true,
				'result' => [
					'view_id'     => $view_id,
					'widget_area' => $widget_area,
					'widget_slot' => $widget_slot,
					'position'    => $position,
					'search_slot' => $search_slot,
					'slot_id'     => $search_slot,
					'field'       => $field,
					'values'      => $field,
					'version'     => $version,
				],
			];
		}

		return $rows;
	}

}
