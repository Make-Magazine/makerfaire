<?php
/**
 * Ability: gk-gravityview/grid-row-remove
 *
 * Removes every area entry whose key references the row_uid. Mirrors
 * the legacy DELETE `/views/{id}/grid/_rows/{row_uid}` route on
 * InspectorRoute. Idempotent: a missing row uid returns 404 from the
 * legacy handler.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/grid-row-remove/run`
 * HTTP method:   DELETE (destructive, idempotent)
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
				'name'                => 'gk-gravityview/grid-row-remove',
				'label'               => __( 'Remove Grid Row', 'gk-gravityview' ),
				'description'         => __( 'Remove every area entry whose key references the given row uid. Operates on either the `fields` or `widgets` surface.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-grid',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'row_uid' => [
							'type'        => 'string',
							'description' => __( 'Row uid to delete.', 'gk-gravityview' ),
						],
						'surface' => [
							'type'        => 'string',
							'enum'        => [ 'fields', 'widgets' ],
							'description' => __( 'Which tree to delete from. Defaults to `fields`.', 'gk-gravityview' ),
						],
						'dry_run' => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch' => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id', 'row_uid' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'       => [ 'type' => 'integer' ],
						'surface'       => [ 'type' => 'string' ],
						'row_uid'       => [ 'type' => 'string' ],
						'removed_areas' => [ 'type' => 'integer' ],
						'version'       => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'surface', 'row_uid', 'removed_areas', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$request->set_param( 'row_uid', (string) ( $input['row_uid'] ?? '' ) );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$body = $input;
					unset( $body['ifMatch'], $body['id'], $body['row_uid'] );
					if ( ! empty( $body ) ) {
						$request->set_body( wp_json_encode( $body ) ?: '{}' );
						$request->set_header( 'content-type', 'application/json' );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'delete_grid_row', $request );
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
						'destructive' => true,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/grid-row-remove' ),
					],
				],
            ]
		);
	}
);
