<?php
/**
 * Ability: gk-gravityview/grid-row-types-list
 *
 * Enumerates the registered Layout Builder grid row types. Sourced
 * from `\GravityKit\GravityView\Renderer\Grid::get_row_types()` so any add-on that registers
 * custom row layouts (3-col 25/25/50, etc.) surfaces automatically.
 * Each row type describes the area positions (`columns`) the row
 * materialises into. Use to discover valid `row_type` values before
 * calling `gk-gravityview/grid-row-add`.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/grid-row-types-list/run`
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
				'name'                => 'gk-gravityview/grid-row-types-list',
				'label'               => __( 'List Grid Row Types', 'gk-gravityview' ),
				'description'         => __( 'Enumerate the registered Layout Builder grid row types. Each row type describes the area positions (columns) the row materialises into. Use to discover valid row_type values before creating a grid row.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'row_types' => [
							'type'  => 'array',
							'items' => [
								'type'       => 'object',
								'properties' => [
									'id'      => [
										'type'        => 'string',
										'description' => __( 'Row type id (use as row_type when creating a grid row).', 'gk-gravityview' ),
									],
									'columns' => [
										'type'  => 'array',
										'items' => [
											'type'       => 'object',
											'properties' => [
												'col'    => [ 'type' => 'string' ],
												'areaid' => [ 'type' => 'string' ],
												'label'  => [ 'type' => 'string' ],
											],
											'required'   => [ 'col', 'areaid' ],
										],
									],
								],
								'required'   => [ 'id', 'columns' ],
							],
						],
					],
					'required'   => [ 'row_types' ],
				],
				'execute_callback'    => static function () {
					$request  = new \WP_REST_Request( 'GET', '' );
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_grid_row_types', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/grid-row-types-list' ),
					],
				],
            ]
		);
	}
);
