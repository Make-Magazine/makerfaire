<?php
/**
 * Ability: gk-gravityview/view-field-move
 *
 * Atomically moves a field slot between (or within) field areas.
 * Supports ref-relative placement (`to.before_slot` / `to.after_slot`)
 * and symbolic `position` ('start' | 'end' | integer). Mirrors the
 * legacy POST `/views/{id}/fields/_move` route on InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-field-move/run`
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
				'name'                => 'gk-gravityview/view-field-move',
				'label'               => __( 'Move View Field', 'gk-gravityview' ),
				'description'         => __( 'Atomically move a field slot between (or within) field areas. Placement precedence: `to.before_slot` > `to.after_slot` > `position` (`start` | `end` | integer). Out-of-range or negative numeric positions are clamped to "append".', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-fields',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
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
								'area'        => [ 'type' => 'string' ],
								'before_slot' => [ 'type' => 'string' ],
								'after_slot'  => [ 'type' => 'string' ],
								'position'    => [ 'description' => __( '"start" | "end" | integer index.', 'gk-gravityview' ) ],
							],
							'required'   => [ 'area' ],
						],
						'dry_run' => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch' => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'id', 'from', 'to' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'  => [ 'type' => 'integer' ],
						'from'     => [ 'type' => 'object' ],
						'to'       => [ 'type' => 'object' ],
						'position' => [
							'type'        => 'integer',
							'description' => __( 'Concrete 0-based final index in to.area after resolving symbolic input values (start, end, negative) and clamping out-of-range integers.', 'gk-gravityview' ),
						],
						'version'  => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'from', 'to', 'position', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$body = [
						'from' => is_array( $input['from'] ?? null ) ? $input['from'] : [],
						'to'   => is_array( $input['to'] ?? null ) ? $input['to'] : [],
					];
					$request->set_body( wp_json_encode( $body ) ?: '{}' );
					$request->set_header( 'content-type', 'application/json' );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'move_field', $request );
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
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_modify_view_child_slot( (int) $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-field-move' ),
					],
				],
            ]
		);
	}
);
