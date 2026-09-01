<?php
/**
 * Ability: gk-gravityview/search-field-remove
 *
 * Remove a search field slot from a `search_bar` widget. Mirrors the
 * legacy DELETE `/views/{id}/search-fields/{search_slot}` route on
 * InspectorRoute. Idempotent: a missing slot returns 404 from the
 * legacy handler.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/search-field-remove/run`
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
				'name'                => 'gk-gravityview/search-field-remove',
				'label'               => __( 'Remove Search Field', 'gk-gravityview' ),
				'description'         => __( 'Remove a search field slot from a `search_bar` widget at the given keyed position. Empties out the position bucket when no slots remain (excluding `area_settings`). Locate the target search_bar widget (its widget_area + widget_slot) and the search_slot/position uids in the widgets tree from gv_view_config_get — there is no auto-resolution to a single bar, and uids are server-minted, so re-read them from gv_view_config_get.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-search-fields',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'          => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'widget_area' => [ 'type' => 'string' ],
						'widget_slot' => [ 'type' => 'string' ],
						'position'    => [ 'type' => 'string' ],
						'search_slot' => [
							'type'        => 'string',
							'description' => __( 'Search slot uid inside the position.', 'gk-gravityview' ),
						],
						'dry_run'     => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch'     => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id', 'widget_area', 'widget_slot', 'position', 'search_slot' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'     => [ 'type' => 'integer' ],
						'widget_area' => [ 'type' => 'string' ],
						'widget_slot' => [ 'type' => 'string' ],
						'position'    => [ 'type' => 'string' ],
						'search_slot' => [ 'type' => 'string' ],
						'deleted'     => [ 'type' => 'boolean' ],
						'version'     => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$request->set_param( 'search_slot', (string) ( $input['search_slot'] ?? '' ) );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$body = $input;
					unset( $body['ifMatch'], $body['id'], $body['search_slot'] );
					if ( ! empty( $body ) ) {
						$encoded_body = wp_json_encode( $body );
						$request->set_body( is_string( $encoded_body ) ? $encoded_body : '{}' );
						$request->set_header( 'content-type', 'application/json' );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'delete_search_field_slot', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/search-field-remove' ),
					],
				],
            ]
		);
	}
);
