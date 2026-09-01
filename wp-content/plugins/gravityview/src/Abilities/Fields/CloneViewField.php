<?php
/**
 * Ability: gk-gravityview/view-field-clone
 *
 * Duplicates an existing field slot — the customer's "Ctrl-D" gesture
 * in page builders (Elementor, Bricks, etc.). Returns the new server-
 * generated slot UID plus the deep-copied settings. The source slot
 * remains untouched.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-field-clone/run`
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
				'name'                => 'gk-gravityview/view-field-clone',
				'label'               => __( 'Clone View Field', 'gk-gravityview' ),
				'description'         => __( 'Duplicate an existing field slot. The source area is resolved automatically from the UID; the new slot inherits every setting via a deep copy and lands immediately after the source by default. `to.area`, `to.before_slot`, `to.after_slot`, and `to.position` let the caller place the clone explicitly (precedence: before > after > position). Use this for page-builder "duplicate" gestures so callers don\'t have to re-emit the full settings payload.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-fields',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'uid'     => [
							'type'        => 'string',
							'description' => __( 'Source slot UID. The ability resolves which area holds it automatically.', 'gk-gravityview' ),
						],
						'to'      => [
							'type'        => 'object',
							'description' => __( 'Optional destination. `area` defaults to the source area. Position resolution precedence: before_slot > after_slot > position.', 'gk-gravityview' ),
							'properties'  => [
								'area'        => [ 'type' => 'string' ],
								'before_slot' => [ 'type' => 'string' ],
								'after_slot'  => [ 'type' => 'string' ],
								'position'    => [
									'type'        => [ 'integer', 'string' ],
									'description' => __( 'Zero-based integer index, or "start" / "end".', 'gk-gravityview' ),
								],
							],
						],
						'dry_run' => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch' => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id', 'uid' ],
					'additionalProperties' => true,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id' => [ 'type' => 'integer' ],
						'area'    => [ 'type' => 'string' ],
						'slot'    => [ 'type' => 'string' ],
						'source'  => [ 'type' => 'object' ],
						'values'  => [ 'type' => 'object' ],
						'version' => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'area', 'slot', 'values', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$body = $input;
					unset( $body['id'], $body['dry_run'], $body['ifMatch'] );
					$request->set_body( wp_json_encode( $body ) ?: '{}' );
					$request->set_header( 'content-type', 'application/json' );

					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'clone_field', $request );
						}
					);
					if ( is_wp_error( $response ) ) {
						return $response;
					}
					return \GravityKit\GravityView\Abilities\Bootstrap::mark_dry_run( $response->get_data(), $is_dry );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-field-clone' ),
					],
				],
            ]
		);
	}
);
