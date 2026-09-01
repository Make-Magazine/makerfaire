<?php
/**
 * Ability: gk-gravityview/field-preset-apply
 *
 * Materialise a registered field preset into one of a View's areas in
 * a single call. Each preset is a list of slot definitions; this
 * ability iterates them and creates one slot per definition via the
 * legacy InspectorRoute add-field path. Honours the `dry_run` flag —
 * runs the full validation pipeline against every slot without
 * persisting any. Honours the preset's `applies_to` compatibility
 * constraints (template_ids / areas / zones).
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/field-preset-apply/run`
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
				'name'                => 'gk-gravityview/field-preset-apply',
				'label'               => __( 'Apply Field Preset', 'gk-gravityview' ),
				'description'         => __( 'Materialise a registered field preset (from list-field-presets) into one of a View\'s areas. Each preset is an array of slot definitions; the ability creates one slot per definition through the same sanitization pipeline as add-view-field. Returns the created slot UIDs in target order. Honours `dry_run` for preflight validation.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-fields',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'        => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'preset_id' => [
							'type'        => 'string',
							'description' => __( 'Preset id from list-field-presets.', 'gk-gravityview' ),
						],
						'area'      => [
							'type'        => 'string',
							'description' => __( 'Field area key to materialise the preset into. Must match the preset\'s `applies_to.areas` if set.', 'gk-gravityview' ),
						],
						'ifMatch'   => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
						'dry_run'   => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
					],
					'required'             => [ 'id', 'preset_id', 'area' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'   => [ 'type' => 'integer' ],
						'preset_id' => [ 'type' => 'string' ],
						'area'      => [ 'type' => 'string' ],
						'created'   => [
							'type'        => 'array',
							'description' => __( 'Slot UIDs created by the preset, in target order. Empty when dry_run is true (no slots are actually persisted).', 'gk-gravityview' ),
							'items'       => [ 'type' => 'string' ],
						],
						'count'     => [
							'type'        => 'integer',
							'description' => __( 'Number of slots that would be created (or were created).', 'gk-gravityview' ),
						],
						'version'   => [
							'type'        => 'string',
							'description' => __( 'View version after the preset application. Same as the input ifMatch on dry-run.', 'gk-gravityview' ),
						],
						'warnings'  => [ 'type' => 'array' ],
						'dry_run'   => [ 'type' => 'boolean' ],
					],
					'required'   => [ 'view_id', 'preset_id', 'area', 'count', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id ) ) {
						return $view_id;
					}

					$preset_id = (string) ( $input['preset_id'] ?? '' );
					$area      = (string) ( $input['area'] ?? '' );
					$is_dry    = (bool) ( $input['dry_run'] ?? false );

					$presets = \GravityKit\GravityView\Abilities\Bootstrap::field_presets();
					if ( ! isset( $presets[ $preset_id ] ) ) {
						return new \WP_Error(
							'gv_rest_preset_not_found',
							sprintf(
								/* translators: %s: preset id supplied by the caller */
								__( 'Field preset "%s" is not registered. Use list-field-presets to discover available presets.', 'gk-gravityview' ),
								$preset_id
							),
							[ 'status' => 404 ]
						);
					}
					$preset = $presets[ $preset_id ];

					$compat = \GravityKit\GravityView\Abilities\Bootstrap::preset_compatibility_error( $preset, $view_id, $area );
					if ( is_wp_error( $compat ) ) {
						return $compat;
					}

					$fields = $preset['fields'];
					if ( ! is_array( $fields ) || empty( $fields ) ) {
						return new \WP_Error(
							'gv_rest_preset_empty',
							__( 'Field preset has no field definitions.', 'gk-gravityview' ),
							[ 'status' => 400 ]
						);
					}

					$created  = [];
					$warnings = [];
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();

					$result = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $view_id, $area, $fields, $input, &$created, &$warnings ) {
							$last_version = '';
							foreach ( $fields as $i => $field_def ) {
								if ( ! is_array( $field_def ) || empty( $field_def['field_id'] ) ) {
									$warnings[] = [
										'index'  => $i,
										'reason' => __( 'Skipped: missing field_id.', 'gk-gravityview' ),
									];
									continue;
								}

								$request = new \WP_REST_Request( 'POST', '' );
								$request->set_param( 'id', $view_id );
								$request->set_param( 'area', $area );
								// Only the first slot honours the caller-supplied
								// `ifMatch`; subsequent slots have to chain off the
								// bumped version returned by the previous write.
								// Bare versions get wrapped in ETag quotes because
								// `check_precondition` compares against a quoted
								// `"<version>"` form.
								$if_match = '';
								if ( '' === $last_version && ! empty( $input['ifMatch'] ) ) {
									$if_match = (string) $input['ifMatch'];
								} elseif ( '' !== $last_version ) {
									$if_match = '"' . $last_version . '"';
								}
								if ( '' !== $if_match ) {
									$request->add_header( 'If-Match', $if_match );
								}
								$request->set_body( wp_json_encode( $field_def ) ?: '{}' );
								$request->set_header( 'content-type', 'application/json' );

								$response = $route->invoke_safely( 'create_field_slot', $request );
								if ( is_wp_error( $response ) ) {
									return $response;
								}
								$data = $response->get_data();
								if ( isset( $data['slot'] ) ) {
									$created[] = (string) $data['slot'];
								}
								if ( isset( $data['version'] ) ) {
									$last_version = (string) $data['version'];
								}
								if ( ! empty( $data['warnings'] ) && is_array( $data['warnings'] ) ) {
									foreach ( $data['warnings'] as $w ) {
										$warnings[] = $w;
									}
								}
							}
							return $last_version;
						}
					);

					if ( is_wp_error( $result ) ) {
						return $result;
					}

					// Dry-run echoes the caller's ifMatch; real-write returns the bumped
					// bare token. Strip ETag quotes so the two wire shapes match — a
					// caller sending `If-Match: "ABC"` would otherwise see `"ABC"` in
					// the dry-run version and `ABC` in the real-write version.
					$dry_version = trim( (string) ( $input['ifMatch'] ?? '' ), '"' );

					$response = [
						'view_id'   => $view_id,
						'preset_id' => $preset_id,
						'area'      => $area,
						'created'   => $created,
						'count'     => $is_dry ? count( $fields ) : count( $created ),
						'version'   => $is_dry ? $dry_version : (string) $result,
					];
					if ( ! empty( $warnings ) ) {
						$response['warnings'] = $warnings;
					}

					return \GravityKit\GravityView\Abilities\Bootstrap::mark_dry_run( $response, $is_dry );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/field-preset-apply' ),
					],
				],
            ]
		);
	}
);
