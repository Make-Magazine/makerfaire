<?php
/**
 * Ability: gk-gravityview/view-widget-remove
 *
 * Removes a widget slot from one of the View's widget areas. Mirrors
 * the legacy DELETE `/views/{id}/widgets/{area}/{slot}` route on
 * InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-widget-remove/run`
 * HTTP method:   DELETE (destructive + idempotent)
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
				'name'                => 'gk-gravityview/view-widget-remove',
				'label'               => __( 'Remove View Widget', 'gk-gravityview' ),
				'description'         => __( 'Remove a widget slot from one of the View\'s widget areas. Idempotent: repeat calls after the first one return a 404, but the View state is unchanged.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-widgets',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'area'    => [
							'type'        => 'string',
							'description' => __( 'Widget area key.', 'gk-gravityview' ),
						],
						'slot'    => [
							'type'        => 'string',
							'description' => __( 'Slot UID to remove.', 'gk-gravityview' ),
						],
						'dry_run' => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch' => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'id', 'area', 'slot' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id' => [ 'type' => 'integer' ],
						'area'    => [ 'type' => 'string' ],
						'slot'    => [ 'type' => 'string' ],
						'version' => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'area', 'slot', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'DELETE', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$request->set_param( 'area', (string) ( $input['area'] ?? '' ) );
					$request->set_param( 'slot', (string) ( $input['slot'] ?? '' ) );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'delete_widget', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-widget-remove' ),
					],
				],
            ]
		);
	}
);
