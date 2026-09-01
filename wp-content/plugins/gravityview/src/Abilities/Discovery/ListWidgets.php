<?php
/**
 * Ability: gk-gravityview/widgets-list
 *
 * Enumerates every registered GravityView widget (page_info,
 * page_links, search_bar, custom_content, and any add-on contributed
 * widgets) with id / label / description / subtitle / icon / class.
 * Use to discover valid widget ids BEFORE placing a widget in a
 * View's header or footer zone.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/widgets-list/run`
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
				'name'                => 'gk-gravityview/widgets-list',
				'label'               => __( 'List Widgets', 'gk-gravityview' ),
				'description'         => __( 'Enumerate every registered GravityView widget (page_info, page_links, search_bar, custom_content, and any add-on contributed widgets) with id / label / description / subtitle / icon / class. Use to discover valid widget ids before placing a widget in a View\'s header or footer zone.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'widgets' => [
							'type'  => 'array',
							'items' => [
								'type'       => 'object',
								'properties' => [
									'id'          => [
										'type'        => 'string',
										'description' => __( 'Widget id (use as widget type when placing a widget).', 'gk-gravityview' ),
									],
									'label'       => [ 'type' => 'string' ],
									'description' => [ 'type' => 'string' ],
									'subtitle'    => [ 'type' => 'string' ],
									'icon'        => [ 'type' => 'string' ],
									'class'       => [ 'type' => 'string' ],
								],
								'required'   => [ 'id', 'label' ],
							],
						],
					],
					'required'   => [ 'widgets' ],
				],
				'execute_callback'    => static function () {
					$request  = new \WP_REST_Request( 'GET', '' );
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_widgets', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/widgets-list' ),
					],
				],
            ]
		);
	}
);
