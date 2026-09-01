<?php
/**
 * Ability: gk-gravityview/widget-zones-list
 *
 * Returns the canonical widget zone keys a View exposes for widget
 * placement (`header`, `footer`). Use to discover valid `zone`
 * values before placing a widget. Constant in core; this endpoint
 * exists so agents have a single discovery surface rather than
 * hard-coding the list.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/widget-zones-list/run`
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
				'name'                => 'gk-gravityview/widget-zones-list',
				'label'               => __( 'List Widget Zones', 'gk-gravityview' ),
				'description'         => __( 'Return the widget zone families a View exposes (header, footer). NOTE: these are families, not directly-placeable area keys — widget placement (gv_view_widget_add / gv_view_config_apply) needs a meta-zone like "header_top" or "footer_bottom". For a search bar, gv_search_bar_add picks the zone for you (pass zone="header"/"footer").', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'widget_zones' => [
							'type'        => 'array',
							'items'       => [ 'type' => 'string' ],
							'description' => __( 'Widget zone keys (e.g. "header", "footer").', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'widget_zones' ],
				],
				'execute_callback'    => static function () {
					$request  = new \WP_REST_Request( 'GET', '' );
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_widget_zones', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/widget-zones-list' ),
					],
				],
            ]
		);
	}
);
