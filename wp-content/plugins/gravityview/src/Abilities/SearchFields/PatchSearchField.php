<?php
/**
 * Ability: gk-gravityview/search-field-patch
 *
 * Patch settings on an existing search field slot. Only keys present
 * in `settings` are modified; the rest is preserved. Mirrors the legacy
 * PATCH `/views/{id}/search-fields/{search_slot}` route on
 * InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/search-field-patch/run`
 * HTTP method:   POST (write)
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'gv_abilities_search_field_patch_handle_batch' ) ) {
	/**
	 * Batch handler shim for the search_field_patch ability.
	 *
	 * @since 3.0.0
	 *
	 * @param array $input Ability input payload.
	 *
	 * @return array|\WP_Error
	 */
	function gv_abilities_search_field_patch_handle_batch( array $input ) {
		return \GravityKit\GravityView\Abilities\Support\BatchHelpers::handle_repository_batch(
			$input,
			new \GravityKit\GravityView\View\Search\SearchFieldSlotRepository()
		);
	}
}

add_action(
    'gk/foundation/abilities/register/before',
    static function () {
		GravityKitFoundation::abilities()->register(
            [
				'name'                => 'gk-gravityview/search-field-patch',
				'label'               => __( 'Patch Search Field', 'gk-gravityview' ),
				'description'         => __( 'Patch settings on an existing search field slot inside a `search_bar` widget. Only keys present in `settings` are modified; the rest of the slot is preserved. Setting a key to `null` removes it. The `input` slug is validated against the per-field allow-list for the slot\'s current `id` so typed patches can\'t silently break the rendered search bar. Supports atomic batch mode: pass `batch[]` (up to 50 items, each with `widget_area` + `widget_slot` + `position` + `search_slot` + `settings`) plus a top-level `version` token to patch multiple search-field slots in one transactional call. Singleton fields and `batch[]` are mutually exclusive. Locate the target search_bar widget (its widget_area + widget_slot) and the search_slot/position uids in the widgets tree from gv_view_config_get — there is no auto-resolution to a single bar, and search_slot uids are server-minted, so re-read them from gv_view_config_get after any structural change.', 'gk-gravityview' ),
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
						'settings'    => [
							'type'                 => 'object',
							'description'          => __( 'Non-empty object of setting key → value (null removes a key).', 'gk-gravityview' ),
							'additionalProperties' => true,
						],
						'dry_run'     => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch'     => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
						'version'     => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. Required when using `batch`.', 'gk-gravityview' ),
						],
						'batch'       => [
							'type'        => 'array',
							'minItems'    => 1,
							'maxItems'    => 50,
							'description' => __( 'Apply N similar operations atomically. All items are validated against the starting state before any persist. If ANY item fails validation, NONE persist and 400 is returned with per-item failures. Mutually exclusive with the singleton fields on this ability.', 'gk-gravityview' ),
							'items'       => [
								'type'                 => 'object',
								'additionalProperties' => false,
								'properties'           => [
									'widget_area' => [ 'type' => 'string' ],
									'widget_slot' => [ 'type' => 'string' ],
									'position'    => [ 'type' => 'string' ],
									'search_slot' => [ 'type' => 'string' ],
									'settings'    => [
										'type' => 'object',
										'additionalProperties' => true,
									],
								],
							],
						],
					],
					'required'             => [ 'id' ],
					'additionalProperties' => true,
					// Disjoint required[] sets enforce mutual exclusion at the schema
					// layer — WP core's validator doesn't support JSON Schema `not`.
					'oneOf'                => [
						[ 'required' => [ 'id', 'widget_area', 'widget_slot', 'position', 'search_slot', 'settings' ] ],
						[ 'required' => [ 'id', 'batch', 'version' ] ],
					],
				],
				'output_schema'       => \GravityKit\GravityView\Abilities\Support\BatchHelpers::output_schema(
                    [
						'type'       => 'object',
						'properties' => [
							'view_id'     => [ 'type' => 'integer' ],
							'widget_area' => [ 'type' => 'string' ],
							'widget_slot' => [ 'type' => 'string' ],
							'position'    => [ 'type' => 'string' ],
							'search_slot' => [ 'type' => 'string' ],
							'values'      => [ 'type' => 'object' ],
							'version'     => [ 'type' => 'string' ],
						],
						'required'   => [ 'view_id', 'widget_area', 'widget_slot', 'position', 'search_slot', 'values', 'version' ],
                    ]
                ),
				'execute_callback'    => static function ( $input ) {
					$input = is_array( $input ) ? $input : [];
					if ( \GravityKit\GravityView\Foundation\Abilities\Support\BatchContract::is_batch_input( $input ) ) {
						return gv_abilities_search_field_patch_handle_batch( $input );
					}

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
							return $route->invoke_safely( 'patch_search_field_slot', $request );
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
						'destructive' => false,
						'idempotent'  => true,
						'batch'       => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/search-field-patch' ),
					],
				],
            ]
		);
	}
);
