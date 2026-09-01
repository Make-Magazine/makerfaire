<?php
/**
 * Ability: gk-gravityview/view-create
 *
 * Creates a draft View, optionally seeding template / template_settings /
 * search_criteria / fields / widgets in one shot. Returns the full
 * `/config` envelope (plus `view_id`, `created`, `admin_url`) so AI
 * clients never need a follow-up GET. Mirrors the legacy `POST /views`
 * route on InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-create/run`
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
				'name'                => 'gk-gravityview/view-create',
				'label'               => __( 'Create View', 'gk-gravityview' ),
				'description'         => __( 'Create a draft View, optionally seeding template, template_settings, search_criteria, fields, and widgets in one shot. Returns the full config envelope (plus view_id, created, admin_url) so callers never need a follow-up GET.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'title'             => [
							'type'        => 'string',
							'description' => __( 'View title (post_title).', 'gk-gravityview' ),
						],
						'form_id'           => [
							'type'        => 'integer',
							'description' => __( 'Gravity Forms form id to bind the View to.', 'gk-gravityview' ),
						],
						'template_id'       => [
							'type'        => 'string',
							'description' => __( 'Directory-zone template id. Defaults to `gravityview-layout-builder`.', 'gk-gravityview' ),
						],
						'template_ids'      => [
							'type'        => 'object',
							'description' => __( 'Optional per-zone template overrides (`single`, `edit`).', 'gk-gravityview' ),
						],
						'status'            => [
							'type'        => 'string',
							'enum'        => [ 'draft', 'publish', 'pending', 'private' ],
							'description' => __( 'Initial post status. Defaults to `draft`.', 'gk-gravityview' ),
						],
						'template_settings' => [ 'type' => 'object' ],
						'search_criteria'   => [ 'type' => 'object' ],
						'fields'            => [ 'type' => 'object' ],
						'widgets'           => [ 'type' => 'object' ],
						'mode'              => [
							'type'        => 'string',
							'enum'        => [ 'replace', 'merge' ],
							'description' => __( 'Seed apply mode when fields / widgets are supplied.', 'gk-gravityview' ),
						],
						'ifMatch'           => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
						'dry_run'           => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
					],
					'required'             => [ 'title', 'form_id' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'           => [ 'type' => 'integer' ],
						'created'           => [ 'type' => 'boolean' ],
						'admin_url'         => [ 'type' => 'string' ],
						'version'           => [ 'type' => 'string' ],
						'template_id'       => [ 'type' => 'string' ],
						'template_ids'      => [ 'type' => 'object' ],
						'form_id'           => [ 'type' => 'integer' ],
						'areas'             => [ 'type' => 'object' ],
						'fields'            => [ 'type' => 'object' ],
						'widgets'           => [ 'type' => 'object' ],
						'template_settings' => [ 'type' => 'object' ],
						'search_criteria'   => [ 'type' => 'object' ],
						'applied'           => [
							'type'        => 'object',
							'description' => __( 'Present when seed fields / widgets were applied.', 'gk-gravityview' ),
						],
						'warnings'          => [
							'type'        => 'array',
							'description' => __( 'Present when sanitization dropped any setting values during seeding.', 'gk-gravityview' ),
						],
						'dry_run'           => [ 'type' => 'boolean' ],
						'would_create'      => [ 'type' => 'boolean' ],
					],
					'required'   => [ 'view_id', 'created', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$is_dry = (bool) ( $input['dry_run'] ?? false );
					if ( $is_dry ) {
						return \GravityKit\GravityView\Foundation\Abilities\Support\DryRun::with_dry_run(
							true,
							static function () use ( $input ) {
								return \GravityKit\GravityView\Foundation\Abilities\Support\DryRun::with_post_write_guard(
									static function () {
										return null;
									},
									[
										'view_id'      => 0,
										'created'      => false,
										'version'      => '',
										'title'        => (string) ( $input['title'] ?? '' ),
										'form_id'      => (int) ( $input['form_id'] ?? 0 ),
										'template_id'  => (string) ( $input['template_id'] ?? 'gravityview-layout-builder' ),
										'would_create' => true,
									]
								);
							}
						);
					}

					// create_view reads scalar params via get_param(); when a seed
					// (fields/widgets/template_settings/search_criteria) is present it
					// chains into apply_config, which reads get_json_params(). Put the
					// payload on the JSON body so both see it — get_param() reads JSON
					// body params too, so a set_param-only request leaves apply_config
					// with an empty payload (400).
					$request = new \WP_REST_Request( 'POST', '' );
					$body    = [];
					foreach ( [ 'title', 'form_id', 'template_id', 'template_ids', 'status', 'template_settings', 'search_criteria', 'fields', 'widgets', 'mode' ] as $key ) {
						if ( array_key_exists( $key, (array) $input ) ) {
							$body[ $key ] = $input[ $key ];
						}
					}
					$encoded = wp_json_encode( $body );
					$request->set_body( false !== $encoded ? $encoded : '{}' );
					$request->set_header( 'content-type', 'application/json' );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
						// invoke_safely() wraps the delegate so any uncaught
						// Throwable becomes a normalised gv_rest_handler_exception
						// WP_Error instead of bubbling to PHP's fatal handler.
						$route    = new \GravityKit\GravityView\REST\InspectorRoute();
						$response = $route->invoke_safely( 'create_view', $request );
					if ( is_wp_error( $response ) ) {
						return $response;
					}
						return $response->get_data();
				},
				'permission_callback' => static function ( $input ) {
					// Baseline + status-conditional publish cap unified in the
					// permission service. Returns WP_Error directly on deny so
					// the abilities framework surfaces the correct error code +
					// status (gv_rest_forbidden / gv_rest_forbidden_publish)
					// instead of a generic 403.
					$status = (string) ( $input['status'] ?? 'draft' );
					return ( new \GravityKit\GravityView\Permissions\Permissions() )
						->can_create_view_with_status( $status );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-create' ),
					],
				],
            ]
		);
	}
);
