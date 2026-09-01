<?php
/**
 * Ability: gk-gravityview/preview-stage-delete
 *
 * Invalidates a preview stage transient. Called by the React inspector
 * on Save (the saved state is the new truth) and on Discard (changes
 * were thrown away). Mirrors the legacy DELETE
 * `/views/{id}/preview/_stage` route on InspectorRoute. Ownership is
 * verified against the transient's stored `user_id` / `view_id` so a
 * different user or a different View can't clear an unrelated stage.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/preview-stage-delete/run`
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
				'name'                => 'gk-gravityview/preview-stage-delete',
				'label'               => __( 'Delete Preview Stage', 'gk-gravityview' ),
				'description'         => __( 'Invalidate a preview stage transient. Always returns 200 — a missing transient is indistinguishable from "already cleared". Ownership is verified before deletion.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-preview',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'        => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'stage_key' => [
							'type'        => 'string',
							'description' => __( '32-char hex token returned by create-preview-stage.', 'gk-gravityview' ),
						],
						'ifMatch'   => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id', 'stage_key' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id' => [ 'type' => 'integer' ],
						'cleared' => [ 'type' => 'boolean' ],
					],
					'required'   => [ 'view_id', 'cleared' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					// Legacy handler reads stage_key from the JSON body, not the URL.
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$body = $input;
					unset( $body['ifMatch'], $body['id'] );
					if ( ! empty( $body ) ) {
						$encoded = wp_json_encode( $body );
						$request->set_body( false === $encoded ? '{}' : $encoded );
						$request->set_header( 'content-type', 'application/json' );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'delete_preview_stage', $request );
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
						'destructive' => true,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/preview-stage-delete' ),
					],
				],
            ]
		);
	}
);
