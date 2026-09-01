<?php
/**
 * Ability: gk-gravityview/preview-stage-create
 *
 * Stages an in-flight `fields` / `widgets` / `template_settings` tree
 * in a short-lived transient so the preview renderer can show unsaved
 * changes. Mirrors the legacy POST `/views/{id}/preview/_stage` route
 * on InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/preview-stage-create/run`
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
				'name'                => 'gk-gravityview/preview-stage-create',
				'label'               => __( 'Create Preview Stage', 'gk-gravityview' ),
				'description'         => __( 'Stage an in-flight `fields` / `widgets` / `template_settings` tree in a short-lived transient (5 minutes) so the preview renderer can show unsaved changes. Returns a `stage_key` to pass to the preview endpoint. Any of the three trees can be omitted — the renderer falls back to the saved meta for missing trees.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-preview',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'                => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'fields'            => [
							'type'        => 'object',
							'description' => __( 'In-flight fields tree, or omit/null to use the saved value.', 'gk-gravityview' ),
						],
						'widgets'           => [
							'type'        => 'object',
							'description' => __( 'In-flight widgets tree, or omit/null to use the saved value.', 'gk-gravityview' ),
						],
						'template_settings' => [
							'type'        => 'object',
							'description' => __( 'In-flight template settings, or omit/null to use the saved value.', 'gk-gravityview' ),
						],
						'ifMatch'           => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id' ],
					'additionalProperties' => true,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'stage_key'  => [
							'type'        => 'string',
							'description' => __( '32-char hex token to pass to the preview endpoint.', 'gk-gravityview' ),
						],
						'view_id'    => [ 'type' => 'integer' ],
						'expires_at' => [
							'type'        => 'string',
							'description' => __( 'ISO-8601 UTC expiry timestamp.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'stage_key', 'view_id', 'expires_at' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
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
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'create_preview_stage', $request );
					if ( is_wp_error( $response ) ) {
						return $response;
					}
					return $response->get_data();
				},
				'permission_callback' => static function ( $input ) {
					$view_id = (int) ( $input['id'] ?? 0 );
					if ( $view_id <= 0 ) {
						return false;
					}
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_edit_view( (int) $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/preview-stage-create' ),
					],
				],
            ]
		);
	}
);
