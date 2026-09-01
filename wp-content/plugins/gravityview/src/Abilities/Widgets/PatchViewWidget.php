<?php
/**
 * Ability: gk-gravityview/view-widget-patch
 *
 * Patches an existing widget slot's settings — every top-level key in
 * the `settings` payload is merged into the stored record through the
 * sanitization pipeline. Mirrors the legacy PATCH
 * `/views/{id}/widgets/{area}/{slot}` route on InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-widget-patch/run`
 * HTTP method:   POST (write)
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'gv_abilities_view_widget_patch_handle_batch' ) ) {
	/**
	 * Batch handler shim for the view_widget_patch ability.
	 *
	 * @since 3.0.0
	 *
	 * @param array $input Ability input payload.
	 *
	 * @return array|\WP_Error
	 */
	function gv_abilities_view_widget_patch_handle_batch( array $input ) {
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
				'name'                => 'gk-gravityview/view-widget-patch',
				'label'               => __( 'Patch View Widget', 'gk-gravityview' ),
				'description'         => __( 'Patch an existing widget slot — each key/value in `settings` is merged into the stored slot record through the sanitization pipeline. Pass `null` for a setting to remove it. `conditional_logic` is validated separately and may report a warning if the supplied rules are rejected. Supports atomic batch mode: pass `batch[]` (up to 50 items, each with `area` + `slot` + `settings`) plus a top-level `version` token to patch multiple slots in one transactional call. Singleton fields and `batch[]` are mutually exclusive.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-widgets',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'       => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'area'     => [
							'type'        => 'string',
							'description' => __( 'Widget area key.', 'gk-gravityview' ),
						],
						'slot'     => [
							'type'        => 'string',
							'description' => __( 'Slot UID inside the area.', 'gk-gravityview' ),
						],
						'settings' => [
							'type'                 => 'object',
							'description'          => __( 'Map of setting key → value. Each entry is sanitized.', 'gk-gravityview' ),
							'additionalProperties' => true,
						],
						'dry_run'  => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch'  => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
						'version'  => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. Required when using `batch`.', 'gk-gravityview' ),
						],
						'batch'    => [
							'type'        => 'array',
							'minItems'    => 1,
							'maxItems'    => 50,
							'description' => __( 'Apply N similar operations atomically. All items are validated against the starting state before any persist. If ANY item fails validation, NONE persist and 400 is returned with per-item failures. Mutually exclusive with the singleton fields on this ability.', 'gk-gravityview' ),
							'items'       => [
								'type'                 => 'object',
								'additionalProperties' => false,
								'properties'           => [
									'area'     => [ 'type' => 'string' ],
									'slot'     => [ 'type' => 'string' ],
									'settings' => [
										'type' => 'object',
										'additionalProperties' => true,
									],
								],
							],
						],
					],
					'required'   => [ 'id' ],
					// Disjoint required[] sets enforce mutual exclusion at the schema
					// layer — WP core's validator doesn't support JSON Schema `not`.
					'oneOf'      => [
						[ 'required' => [ 'id', 'area', 'slot', 'settings' ] ],
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
						return gv_abilities_view_widget_patch_handle_batch( $input );
					}

					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$request->set_param( 'area', (string) ( $input['area'] ?? '' ) );
					$request->set_param( 'slot', (string) ( $input['slot'] ?? '' ) );
					// Legacy patch_widget reads each top-level body key as a
					// setting. Flatten `settings` into the body.
					$body = isset( $input['settings'] ) && is_array( $input['settings'] ) ? $input['settings'] : [];
					$request->set_body( wp_json_encode( $body ) ?: '{}' );
					$request->set_header( 'content-type', 'application/json' );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'patch_widget', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-widget-patch' ),
					],
				],
            ]
		);
	}
);
