<?php
/**
 * Ability: gk-gravityview/grid-row-patch
 *
 * Re-keys every field in a grid row from `::{old_type}::{row_uid}` to
 * `::{new_type}::{row_uid}`. When the new type has fewer columns,
 * surplus fields collapse into the first column of the new row so
 * nothing silently disappears. Mirrors the legacy PATCH
 * `/views/{id}/grid/_rows/{row_uid}` route on InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/grid-row-patch/run`
 * HTTP method:   POST (write)
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'gv_abilities_grid_row_patch_handle_batch' ) ) {
	/**
	 * Batch handler shim for the grid_row_patch ability.
	 *
	 * @since 3.0.0
	 *
	 * @param array $input Ability input payload.
	 *
	 * @return array|\WP_Error
	 */
	function gv_abilities_grid_row_patch_handle_batch( array $input ) {
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
				'name'                => 'gk-gravityview/grid-row-patch',
				'label'               => __( 'Patch Grid Row', 'gk-gravityview' ),
				'description'         => __( 'Change the row type of an existing grid row. Re-keys every entry whose key carries `::{row_uid}` to the new row type. When the new type has fewer columns, surplus fields collapse into the first column so nothing is silently lost. Supports atomic batch mode: pass `batch[]` (up to 50 items, each with `row_uid` + `type` + optional `surface`) plus a top-level `version` token to patch multiple grid rows in one transactional call. Singleton fields and `batch[]` are mutually exclusive.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-grid',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'row_uid' => [
							'type'        => 'string',
							'description' => __( 'Existing row uid.', 'gk-gravityview' ),
						],
						'type'    => [
							'type'        => 'string',
							'description' => __( 'New grid row type id.', 'gk-gravityview' ),
						],
						'surface' => [
							'type'        => 'string',
							'enum'        => [ 'fields', 'widgets' ],
							'description' => __( 'Which tree the row lives in. Defaults to `fields`.', 'gk-gravityview' ),
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
									'row_uid' => [ 'type' => 'string' ],
									'type'    => [ 'type' => 'string' ],
									'surface' => [
										'type' => 'string',
										'enum' => [ 'fields', 'widgets' ],
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
						[ 'required' => [ 'id', 'row_uid', 'type' ] ],
						[ 'required' => [ 'id', 'batch', 'version' ] ],
					],
				],
				'output_schema'       => \GravityKit\GravityView\Abilities\Support\BatchHelpers::output_schema(
                    [
						'type'       => 'object',
						'properties' => [
							'view_id' => [ 'type' => 'integer' ],
							'surface' => [ 'type' => 'string' ],
							'row_uid' => [ 'type' => 'string' ],
							'type'    => [ 'type' => 'string' ],
							'touched' => [
								'type'        => 'integer',
								'description' => __( 'How many area keys were re-keyed.', 'gk-gravityview' ),
							],
							'version' => [ 'type' => 'string' ],
						],
						'required'   => [ 'view_id', 'surface', 'row_uid', 'type', 'touched', 'version' ],
                    ]
                ),
				'execute_callback'    => static function ( $input ) {
					$input = is_array( $input ) ? $input : [];
					if ( \GravityKit\GravityView\Foundation\Abilities\Support\BatchContract::is_batch_input( $input ) ) {
						return gv_abilities_grid_row_patch_handle_batch( $input );
					}

					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$request->set_param( 'row_uid', (string) ( $input['row_uid'] ?? '' ) );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$body = $input;
					unset( $body['ifMatch'], $body['id'], $body['row_uid'] );
					if ( ! empty( $body ) ) {
						$request->set_body( wp_json_encode( $body ) ?: '{}' );
						$request->set_header( 'content-type', 'application/json' );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'patch_grid_row', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/grid-row-patch' ),
					],
				],
            ]
		);
	}
);
