<?php
/**
 * Ability: gk-gravityview/view-field-get
 *
 * One-shot accessor returning a slot's persisted config, its settings
 * schema, and rendered HTML — the trifecta a page builder's controls
 * panel needs to render. Collapses three separate round-trips into one.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-field-get/run`
 * HTTP method:   GET (read)
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
				'name'                => 'gk-gravityview/view-field-get',
				'label'               => __( 'Get View Field', 'gk-gravityview' ),
				'description'         => __( 'Return a single slot\'s persisted config, its full settings schema, and the rendered field HTML in one call. Resolves the source area automatically from the UID. Designed so page-builder controls panels can populate their entire UI from a single round-trip instead of fetching config, schema, and render separately.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-fields',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'  => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'uid' => [
							'type'        => 'string',
							'description' => __( 'Slot UID. The ability resolves which area holds it automatically.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id', 'uid' ],
					'additionalProperties' => true,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'       => [ 'type' => 'integer' ],
						'uid'           => [ 'type' => 'string' ],
						'area'          => [ 'type' => 'string' ],
						'config'        => [ 'type' => 'object' ],
						'schema'        => [ 'type' => 'object' ],
						'rendered_html' => [ 'type' => 'string' ],
						'version'       => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'uid', 'area', 'config', 'schema', 'rendered_html', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$request = new \WP_REST_Request( 'GET', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$request->set_param( 'uid', (string) ( $input['uid'] ?? '' ) );

					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_field', $request );
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
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_render_field( $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-field-get' ),
					],
				],
            ]
		);
	}
);
