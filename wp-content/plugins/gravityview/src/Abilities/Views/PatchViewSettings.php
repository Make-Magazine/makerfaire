<?php
/**
 * Ability: gk-gravityview/view-settings-patch
 *
 * Partial-merge update for the View's template_settings meta — the flat
 * key/value store used for view-level options (page size, sort, lightbox,
 * etc.) plus namespaced silo settings (e.g. `datatables`). Mirrors the
 * legacy `PATCH /views/{id}/template-settings` route on InspectorRoute,
 * which reads the request body as a flat `{key: value}` map and merges it
 * directly into the existing settings.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-settings-patch/run`
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
				'name'                => 'gk-gravityview/view-settings-patch',
				'label'               => __( 'Patch View Template Settings', 'gk-gravityview' ),
				'description'         => __( 'Partial-merge update for the View\'s template_settings meta — the flat key/value store used for view-level options (page size, sort, lightbox, etc.) plus namespaced silo settings. Pass the new key/value pairs as a `template_settings` object; absent keys are left untouched.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'                => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'template_settings' => [
							'type'        => 'object',
							'description' => __( 'Flat key/value map (or namespaced silo objects) to merge into template_settings.', 'gk-gravityview' ),
						],
						'dry_run'           => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch'           => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id', 'template_settings' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'           => [ 'type' => 'integer' ],
						'template_settings' => [ 'type' => 'object' ],
						'version'           => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'template_settings', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					// patch_template_settings reads the JSON body as a flat
					// `{key: value}` map and merges it directly, so we forward
					// the `template_settings` object as the entire body.
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$body = (array) ( $input['template_settings'] ?? [] );
					// unwrap-template_settings-marker
					// rest-adapter / external callers wrap the legacy flat
					// body under `template_settings` for a clearer ability input
					// shape; the underlying handler reads flat keys, so unwrap
					// when the body is exactly `{ template_settings: { ... } }`.
					if ( 1 === count( $body ) && isset( $body['template_settings'] ) && is_array( $body['template_settings'] ) ) {
						$body = $body['template_settings'];
					}
					if ( ! empty( $body ) ) {
						$request->set_body( wp_json_encode( $body ) ?: '{}' );
						$request->set_header( 'content-type', 'application/json' );
					}
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'patch_template_settings', $request );
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
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_edit_view( (int) $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-settings-patch' ),
					],
				],
            ]
		);
	}
);
