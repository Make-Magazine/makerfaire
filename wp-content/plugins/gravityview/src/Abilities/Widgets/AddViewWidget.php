<?php
/**
 * Ability: gk-gravityview/view-widget-add
 *
 * Creates a widget slot inside one of the View's widget areas (header /
 * footer / search) with a server-generated slot UID. Mirrors the legacy
 * POST `/views/{id}/widgets/{area}/_slots` route on InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-widget-add/run`
 * HTTP method:   POST (write)
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'gv_abilities_view_widget_add_handle_batch' ) ) {
	/**
	 * Batch handler shim for the view_widget_add ability.
	 *
	 * @since 3.0.0
	 *
	 * @param array $input Ability input payload.
	 *
	 * @return array|\WP_Error
	 */
	function gv_abilities_view_widget_add_handle_batch( array $input ) {
		return \GravityKit\GravityView\Abilities\Support\BatchHelpers::handle_repository_batch(
			$input,
			new \GravityKit\GravityView\View\Widgets\WidgetSlotRepository()
		);
	}
}

add_action(
    'gk/foundation/abilities/register/before',
    static function () {
		GravityKitFoundation::abilities()->register(
            [
				'name'                => 'gk-gravityview/view-widget-add',
				'label'               => __( 'Add View Widget', 'gk-gravityview' ),
				'description'         => __( 'Create a widget slot inside one of the View\'s widget areas (header / footer / search) with a server-generated slot UID. For a search bar with fields, prefer gv_search_bar_add — it creates the search_bar widget AND its search fields in one call. Body accepts the widget id `widget_id` (or its aliases `field_id` / `type`), optional label, and any number of widget-specific settings (e.g. `search_layout`, `search_fields_section`, custom_content `content`). Settings are sanitized through the same schema-aware pipeline as patch-view-widget. Supports atomic batch mode: pass `batch[]` (up to 50 items, each with `area` + `widget_id` (or `field_id`) + optional `label`/`settings`) plus a top-level `version` token to create multiple slots in one transactional call. Singleton fields and `batch[]` are mutually exclusive.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-widgets',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'        => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'area'      => [
							'type'        => 'string',
							'description' => __( 'Widget area key (header / footer / search zone).', 'gk-gravityview' ),
						],
						'widget_id' => [
							'type'        => 'string',
							'description' => __( 'Widget id to instantiate.', 'gk-gravityview' ),
						],
						'field_id'  => [
							'type'        => 'string',
							'description' => __( 'Alias for `widget_id` — mirrors the add-view-field payload shape.', 'gk-gravityview' ),
						],
						'label'     => [ 'type' => 'string' ],
						'dry_run'   => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch'   => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
						'version'   => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. Required when using `batch`.', 'gk-gravityview' ),
						],
						'batch'     => [
							'type'        => 'array',
							'minItems'    => 1,
							'maxItems'    => 50,
							'description' => __( 'Apply N similar operations atomically. All items are validated against the starting state before any persist. If ANY item fails validation, NONE persist and 400 is returned with per-item failures. Mutually exclusive with the singleton fields on this ability.', 'gk-gravityview' ),
							'items'       => [
								'type'                 => 'object',
								'additionalProperties' => false,
								'properties'           => [
									'area'      => [ 'type' => 'string' ],
									'widget_id' => [ 'type' => 'string' ],
									'field_id'  => [ 'type' => 'string' ],
									'label'     => [ 'type' => 'string' ],
									'settings'  => [
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
						[ 'required' => [ 'id', 'area' ] ],
						[ 'required' => [ 'id', 'batch', 'version' ] ],
					],
				],
				'output_schema'       => \GravityKit\GravityView\Abilities\Support\BatchHelpers::output_schema(
                    [
						'type'       => 'object',
						'properties' => [
							'view_id'  => [ 'type' => 'integer' ],
							'area'     => [ 'type' => 'string' ],
							'slot'     => [ 'type' => 'string' ],
							'values'   => [ 'type' => 'object' ],
							'version'  => [ 'type' => 'string' ],
							'warnings' => [ 'type' => 'array' ],
						],
						'required'   => [ 'view_id', 'area', 'slot', 'values', 'version' ],
                    ]
                ),
				'execute_callback'    => static function ( $input ) {
					$input = is_array( $input ) ? $input : [];
					if ( \GravityKit\GravityView\Foundation\Abilities\Support\BatchContract::is_batch_input( $input ) ) {
						return gv_abilities_view_widget_add_handle_batch( $input );
					}

					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$request->set_param( 'area', (string) ( $input['area'] ?? '' ) );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$body = $input;
					unset( $body['ifMatch'], $body['id'], $body['area'] );
					// unwrap-widget-marker
					// rest-adapter / external callers wrap the legacy flat
					// body under `widget` for a clearer ability input
					// shape; the underlying handler reads flat keys, so unwrap
					// when the body is exactly `{ widget: { ... } }`.
					if ( 1 === count( $body ) && isset( $body['widget'] ) && is_array( $body['widget'] ) ) {
						$body = $body['widget'];
					}
					if ( ! empty( $body ) ) {
						$request->set_body( wp_json_encode( $body ) ?: '{}' );
						$request->set_header( 'content-type', 'application/json' );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'create_widget_slot', $request );
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
						'idempotent'  => false,
						'batch'       => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-widget-add' ),
					],
				],
            ]
		);
	}
);
