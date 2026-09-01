<?php
/**
 * Ability: gk-gravityview/grid-row-add
 *
 * Mints a new grid row UID on the chosen surface (`fields` or
 * `widgets`) and writes empty area entries for every zone that supports
 * grid rows. Mirrors the legacy POST `/views/{id}/grid/_rows` route on
 * InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/grid-row-add/run`
 * HTTP method:   POST (write)
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'gv_abilities_grid_row_add_handle_batch' ) ) {
	/**
	 * Batch handler shim for the grid_row_add ability.
	 *
	 * @since 3.0.0
	 *
	 * @param array $input Ability input payload.
	 *
	 * @return array|\WP_Error
	 */
	function gv_abilities_grid_row_add_handle_batch( array $input ) {
		return \GravityKit\GravityView\Abilities\Support\BatchHelpers::handle_repository_batch(
			$input,
			new \GravityKit\GravityView\View\Grid\GridRowRepository()
		);
	}
}

add_action(
    'gk/foundation/abilities/register/before',
    static function () {
		GravityKitFoundation::abilities()->register(
            [
				'name'                => 'gk-gravityview/grid-row-add',
				'label'               => __( 'Add Grid Row', 'gk-gravityview' ),
				'description'         => __( 'Mint a new grid row UID on the chosen surface (`fields` or `widgets`) and write empty area entries for every grid-aware zone. Returns the new row uid and the per-zone areaids that were created. Errors if no zone on the surface supports grid rows — switch a zone to `gravityview-layout-builder` first. Supports atomic batch mode: pass `batch[]` (up to 50 items, each with optional `surface` / `type` / `zones`) plus a top-level `version` token to mint multiple grid rows in one transactional call. Singleton fields and `batch[]` are mutually exclusive.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-grid',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'surface' => [
							'type'        => 'string',
							'enum'        => [ 'fields', 'widgets' ],
							'description' => __( 'Which tree to write into. Defaults to `fields`.', 'gk-gravityview' ),
						],
						'type'    => [
							'type'        => 'string',
							'description' => __( 'Grid row type id (e.g. `100`, `50/50`). Defaults to `100`. Use GET /grid/row-types for the live list.', 'gk-gravityview' ),
						],
						'zones'   => [
							'type'        => 'array',
							'items'       => [ 'type' => 'string' ],
							'description' => __( 'Zones to write into. Defaults to the surface\'s zones.', 'gk-gravityview' ),
						],
						'row_uid' => [
							'type'        => 'string',
							'description' => __( 'Reserved. Currently server-generated.', 'gk-gravityview' ),
						],
						'dry_run' => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch' => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
						'version' => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. Required when using `batch`.', 'gk-gravityview' ),
						],
						'batch'   => [
							'type'        => 'array',
							'minItems'    => 1,
							'maxItems'    => 50,
							'description' => __( 'Apply N similar operations atomically. All items are validated against the starting state before any persist. If ANY item fails validation, NONE persist and 400 is returned with per-item failures. Mutually exclusive with the singleton fields on this ability.', 'gk-gravityview' ),
							'items'       => [
								'type'                 => 'object',
								'additionalProperties' => false,
								'properties'           => [
									'surface' => [
										'type' => 'string',
										'enum' => [ 'fields', 'widgets' ],
									],
									'type'    => [ 'type' => 'string' ],
									'zones'   => [
										'type'  => 'array',
										'items' => [ 'type' => 'string' ],
									],
								],
							],
						],
					],
					'required'             => [ 'id' ],
					'additionalProperties' => true,
					// AddGridRow's singleton mode has no required distinguishing
					// field (surface/type/zones are all optional), so a strict
					// disjoint oneOf isn't expressible. The runtime
					// BatchContract::is_batch_input dispatch enforces the
					// mutual exclusion via the gk_batch_mixed_mode error code.
				],
				'output_schema'       => \GravityKit\GravityView\Abilities\Support\BatchHelpers::output_schema(
                    [
						'type'       => 'object',
						'properties' => [
							'view_id' => [ 'type' => 'integer' ],
							'surface' => [ 'type' => 'string' ],
							'row_uid' => [ 'type' => 'string' ],
							'type'    => [ 'type' => 'string' ],
							'created' => [
								'type'        => 'object',
								'description' => __( 'zone => array of areaid strings just created.', 'gk-gravityview' ),
							],
							'skipped' => [
								'type'        => 'object',
								'description' => __( 'zone => reason string for zones that were not written.', 'gk-gravityview' ),
							],
							'version' => [ 'type' => 'string' ],
						],
						'required'   => [ 'view_id', 'surface', 'row_uid', 'version' ],
                    ]
                ),
				'execute_callback'    => static function ( $input ) {
					$input = is_array( $input ) ? $input : [];
					if ( \GravityKit\GravityView\Foundation\Abilities\Support\BatchContract::is_batch_input( $input ) ) {
						return gv_abilities_grid_row_add_handle_batch( $input );
					}

					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$body = $input;
					unset( $body['ifMatch'], $body['id'] );
					if ( ! empty( $body ) ) {
						$request->set_body( wp_json_encode( $body ) ?: '{}' );
						$request->set_header( 'content-type', 'application/json' );
					}
					// `create_grid_row` reads type/surface/zones via `$request->get_param()`,
					// which on WP_REST_Request reads from JSON body as well as query params.
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'create_grid_row', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/grid-row-add' ),
					],
				],
            ]
		);
	}
);
