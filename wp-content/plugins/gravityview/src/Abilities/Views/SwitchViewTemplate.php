<?php
/**
 * Ability: gk-gravityview/view-template-switch
 *
 * Switches the layout template for one of the View's render zones
 * (`directory`, `single`, or `edit`). With the default `discard` policy
 * the matching zone's field + widget placements are cleared (matching the
 * legacy editor behavior); `preserve` (alias `keep`) leaves them intact so
 * fields configured in areas the new template lacks won't render until
 * moved or removed. Mirrors the legacy `PATCH /views/{id}/template` route
 * on InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-template-switch/run`
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
				'name'                => 'gk-gravityview/view-template-switch',
				'label'               => __( 'Switch View Template', 'gk-gravityview' ),
				'description'         => __( 'Switch the layout template for one of the View\'s render zones (directory, single, or edit). With the default `discard` policy the matching zone\'s field + widget placements are cleared; `preserve` (alias `keep`) leaves them intact so fields in areas the new template lacks won\'t render until moved or removed.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'          => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'template_id' => [
							'type'        => 'string',
							'description' => __( 'Template id to switch to. Use GET gk-gravityview/layouts-list to discover valid ids.', 'gk-gravityview' ),
						],
						'zone'        => [
							'type'        => 'string',
							'enum'        => [ 'directory', 'single', 'edit' ],
							'description' => __( 'Zone to switch. Defaults to `directory`.', 'gk-gravityview' ),
						],
						'policy'      => [
							'type'        => 'string',
							'enum'        => [ 'discard', 'preserve', 'keep' ],
							'description' => __( 'What to do with existing field / widget placements. Defaults to `discard`.', 'gk-gravityview' ),
						],
						'dry_run'     => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch'     => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id', 'template_id' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'           => [ 'type' => 'integer' ],
						'version'           => [ 'type' => 'string' ],
						'template_id'       => [ 'type' => 'string' ],
						'template_ids'      => [ 'type' => 'object' ],
						'form_id'           => [ 'type' => 'integer' ],
						'areas'             => [ 'type' => 'object' ],
						'fields'            => [ 'type' => 'object' ],
						'widgets'           => [ 'type' => 'object' ],
						'template_settings' => [ 'type' => 'object' ],
						'search_criteria'   => [ 'type' => 'object' ],
					],
					'required'   => [ 'view_id', 'version', 'template_id', 'template_ids', 'form_id', 'areas', 'fields', 'widgets', 'template_settings', 'search_criteria' ],
				],
				'execute_callback'    => static function ( $input ) {
					// patch_template reads `template_id`, `zone`, and `policy` from
					// the JSON body (with a fallback to URL params), so we send them
					// in a JSON body for consistency.
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
						$encoded = wp_json_encode( $body );
						$request->set_body( false === $encoded ? '{}' : $encoded );
						$request->set_header( 'content-type', 'application/json' );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'patch_template', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-template-switch' ),
					],
				],
            ]
		);
	}
);
