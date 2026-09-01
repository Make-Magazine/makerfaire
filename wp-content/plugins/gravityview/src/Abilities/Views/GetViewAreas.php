<?php
/**
 * Ability: gk-gravityview/view-areas-get
 *
 * Returns the area / zone inventory for a View — every render zone the
 * active layout exposes (directory, single, edit) with the keys that
 * fields and widgets attach to. Mirrors the legacy `GET /views/{id}/areas`
 * route on InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-areas-get/run`
 * HTTP method:   GET (readonly)
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
				'name'                => 'gk-gravityview/view-areas-get',
				'label'               => __( 'Get View Areas', 'gk-gravityview' ),
				'description'         => __( 'Return the area / zone inventory for a View — every render zone the active layout exposes (directory, single, edit) with the keys fields and widgets attach to.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id' => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'id' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'     => [ 'type' => 'integer' ],
						'template_id' => [ 'type' => 'string' ],
						'zones'       => [
							'type'        => 'object',
							'description' => __( 'Map of zone key → area inventory.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'view_id', 'template_id', 'zones' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$request = new \WP_REST_Request( 'GET', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_areas', $request );
					if ( is_wp_error( $response ) ) {
						return $response;
					}
					return $response->get_data();
				},
				'permission_callback' => static function ( $input ) {
					$view_id = (int) ( $input['id'] ?? 0 );
					if ( $view_id <= 0 ) {
						return false;
					}
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_edit_view( (int) $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-areas-get' ),
					],
				],
            ]
		);
	}
);
