<?php
/**
 * Ability: gk-gravityview/forms-list
 *
 * Enumerates every Gravity Forms form available on the site with
 * id / title / field count. Use to discover valid `form_id` values
 * when creating a View (a View is always backed by exactly one
 * Gravity Forms form). Returns an empty list when Gravity Forms
 * isn't active.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/forms-list/run`
 * HTTP method:   GET
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
				'name'                => 'gk-gravityview/forms-list',
				'label'               => __( 'List Forms', 'gk-gravityview' ),
				'description'         => __( 'Enumerate every Gravity Forms form available on the site with id / title / field count. Use to discover valid form_id values when creating a View. Returns an empty list when Gravity Forms isn\'t active.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'forms' => [
							'type'  => 'array',
							'items' => [
								'type'       => 'object',
								'properties' => [
									'id'     => [
										'type'        => 'integer',
										'description' => __( 'Gravity Forms form id (use as form_id when creating a View).', 'gk-gravityview' ),
									],
									'title'  => [ 'type' => 'string' ],
									'fields' => [
										'type'        => 'integer',
										'description' => __( 'Number of fields on the form.', 'gk-gravityview' ),
									],
								],
								'required'   => [ 'id', 'title', 'fields' ],
							],
						],
					],
					'required'   => [ 'forms' ],
				],
				'execute_callback'    => static function () {
					$request  = new \WP_REST_Request( 'GET', '' );
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_forms', $request );
					if ( is_wp_error( $response ) ) {
						return $response;
					}
					return $response->get_data();
				},
				'permission_callback' => static function () {
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_access_discovery();
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/forms-list' ),
					],
				],
            ]
		);
	}
);
