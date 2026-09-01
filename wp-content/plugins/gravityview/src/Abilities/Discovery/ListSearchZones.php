<?php
/**
 * Ability: gk-gravityview/search-zones-list
 *
 * Returns the Search Bar internal zone keys a search_bar widget
 * exposes for search-field placement. Defaults to
 * `search-general` / `search-advanced`; add-ons that introduce
 * additional search-bar surfaces register their zone keys via the
 * `gk/gravityview/rest/search-zones` filter and they appear here
 * automatically.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/search-zones-list/run`
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
				'name'                => 'gk-gravityview/search-zones-list',
				'label'               => __( 'List Search Zones', 'gk-gravityview' ),
				'description'         => __( 'Return the Search Bar internal zone keys a search_bar widget exposes for search-field placement. Defaults to search-general / search-advanced; add-ons may register additional zones via the gk/gravityview/rest/search-zones filter.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'search_zones' => [
							'type'        => 'array',
							'items'       => [ 'type' => 'string' ],
							'description' => __( 'Search Bar zone keys (e.g. "search-general", "search-advanced").', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'search_zones' ],
				],
				'execute_callback'    => static function () {
					$request  = new \WP_REST_Request( 'GET', '' );
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_search_zones', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/search-zones-list' ),
					],
				],
            ]
		);
	}
);
