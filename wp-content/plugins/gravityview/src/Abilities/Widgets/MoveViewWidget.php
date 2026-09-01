<?php
/**
 * Ability: gk-gravityview/view-widget-move
 *
 * Moves a widget slot within the same area (reorder) or across areas
 * (header ↔ footer). Slot UID is preserved across the move so settings persist.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-widget-move/run`
 * HTTP method:   POST (write)
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

defined( 'ABSPATH' ) || exit;

add_action(
    'gk/foundation/abilities/register/before',
    static function () {
		GravityKitFoundation::abilities()->register(
            [
				'name'                => 'gk-gravityview/view-widget-move',
				'label'               => __( 'Move View Widget', 'gk-gravityview' ),
				'description'         => __( 'Move a widget slot within its area (reposition) or across areas (header ↔ footer). The slot UID is preserved across the move so settings persist. Body accepts to_area + exactly ONE of before_slot, after_slot, position, or symbolic "start"/"end".', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-widgets',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'          => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'from_area'   => [
							'type'        => 'string',
							'description' => __( 'Source widget area key.', 'gk-gravityview' ),
						],
						'from_slot'   => [
							'type'        => 'string',
							'description' => __( 'Source slot UID within the source area.', 'gk-gravityview' ),
						],
						'to_area'     => [
							'type'        => 'string',
							'description' => __( 'Destination area key. May be the same as from_area to reposition within the area.', 'gk-gravityview' ),
						],
						'before_slot' => [
							'type'        => 'string',
							'description' => __( 'Optional. Slot UID in to_area to insert the moved widget immediately before. Mutually exclusive with after_slot / position.', 'gk-gravityview' ),
						],
						'after_slot'  => [
							'type'        => 'string',
							'description' => __( 'Optional. Slot UID in to_area to insert the moved widget immediately after. Mutually exclusive with before_slot / position.', 'gk-gravityview' ),
						],
						'position'    => [
							'description' => __( 'Optional. Numeric (0-based index) or symbolic "start"/"end" target position in to_area. Mutually exclusive with before_slot / after_slot.', 'gk-gravityview' ),
							'oneOf'       => [
								[
									'type'    => 'integer',
									'minimum' => 0,
								],
								[
									'type' => 'string',
									'enum' => [ 'start', 'end' ],
								],
							],
						],
						'ifMatch'     => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. Server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
						'dry_run'     => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
					],
					'required'             => [ 'id', 'from_area', 'from_slot', 'to_area' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id' => [ 'type' => 'integer' ],
						'from'    => [
							'type'       => 'object',
							'properties' => [
								'area' => [ 'type' => 'string' ],
								'slot' => [ 'type' => 'string' ],
							],
							'required'   => [ 'area', 'slot' ],
						],
						'to'      => [
							'type'       => 'object',
							'properties' => [
								'area' => [ 'type' => 'string' ],
								'slot' => [ 'type' => 'string' ],
							],
							'required'   => [ 'area', 'slot' ],
						],
						'version' => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'from', 'to', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					// Delegates to the new WidgetSlotRepository::move (Phase 4e)
					// — InspectorRoute never had a move_widget handler so the
					// previous shim would have 500'd.
					$view_id = (int) ( $input['id'] ?? 0 );
					$from    = \GravityKit\GravityView\View\AreaSlotIdentity::from_array(
						[
							'view_id' => $view_id,
							'area'    => $input['from_area'] ?? '',
							'slot'    => $input['from_slot'] ?? '',
						]
					);
					if ( is_wp_error( $from ) ) {
						return $from;
					}

					$target = \GravityKit\GravityView\View\MoveSlotTarget::from_array(
						[
							'to_area'     => (string) ( $input['to_area'] ?? '' ),
							'before_slot' => $input['before_slot'] ?? null,
							'after_slot'  => $input['after_slot'] ?? null,
							'position'    => $input['position'] ?? null,
						]
					);
					if ( is_wp_error( $target ) ) {
						return $target;
					}

					$is_dry = (bool) ( $input['dry_run'] ?? false );
					$repo   = new \GravityKit\GravityView\View\Widgets\WidgetSlotRepository();
					return \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $repo, $from, $target, $input ) {
							return $repo->move( $from, $target, isset( $input['ifMatch'] ) ? (string) $input['ifMatch'] : null );
						}
					);
				},
				'permission_callback' => static function ( $input ) {
					$view_id = (int) ( $input['id'] ?? 0 );
					if ( $view_id <= 0 ) {
						return false;
					}
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_modify_view_child_slot( $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-widget-move' ),
					],
				],
            ]
		);
	}
);
