<?php
/**
 * Ability: gk-gravityview/grid-row-clone
 *
 * Duplicates a Layout Builder grid row — every area key whose suffix is
 * `::{row_uid}`, plus every slot inside, with brand-new UIDs.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/grid-row-clone/run`
 * HTTP method:   POST (write)
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
				'name'                => 'gk-gravityview/grid-row-clone',
				'label'               => __( 'Clone Grid Row', 'gk-gravityview' ),
				'description'         => __( 'Duplicate a Layout Builder row. Generates a new row_uid + fresh slot UIDs for every contained field; inserts the cloned row immediately after the source in the field tree (so it renders next to the source). Use this for page-builder row-duplicate gestures (Elementor Ctrl-D on a row, Bricks duplicate, etc.).', 'gk-gravityview' ),
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
							'description' => __( 'Source row UID. The ability finds every area key suffixed `::{row_uid}` and clones them as a single block.', 'gk-gravityview' ),
						],
						'dry_run' => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch' => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id', 'row_uid' ],
					'additionalProperties' => true,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'   => [ 'type' => 'integer' ],
						'row_uid'   => [ 'type' => 'string' ],
						'type'      => [ 'type' => 'string' ],
						'area_keys' => [ 'type' => 'array' ],
						'source'    => [ 'type' => 'object' ],
						'version'   => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'row_uid', 'type', 'area_keys', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
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
					unset( $body['id'], $body['dry_run'], $body['ifMatch'] );
					$request->set_body( wp_json_encode( $body ) ?: '{}' );
					$request->set_header( 'content-type', 'application/json' );

					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'clone_grid_row', $request );
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
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_modify_view_child_slot( $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/grid-row-clone' ),
					],
				],
            ]
		);
	}
);
