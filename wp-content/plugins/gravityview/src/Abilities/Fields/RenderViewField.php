<?php
/**
 * Ability: gk-gravityview/view-field-render
 *
 * Renders a single field slot's cell HTML against a representative
 * entry. Accepts optional `settings` overrides (merged through the
 * schema-aware sanitization pipeline) and `staged_slot` for previewing
 * brand-new slots before the apply that would persist them. Mirrors the
 * legacy GET/POST `/views/{id}/fields/{area}/{slot}/_render` route on
 * InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-field-render/run`
 * HTTP method:   GET (readonly)
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
				'name'                => 'gk-gravityview/view-field-render',
				'label'               => __( 'Render View Field', 'gk-gravityview' ),
				'description'         => __( 'Render a single field slot\'s cell HTML against a representative entry. Pass `settings` to preview override values without persisting them, or `staged_slot` (`{ field_id, label?, ...settings }`) to preview a brand-new slot before the apply that would persist it. Both paths run the same schema-aware sanitization as the persist endpoints, so the preview can never honor a setting the apply path would silently strip.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-fields',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'          => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'area'        => [
							'type'        => 'string',
							'description' => __( 'Field area key.', 'gk-gravityview' ),
						],
						'slot'        => [
							'type'        => 'string',
							'description' => __( 'Slot UID to render (may be a placeholder when `staged_slot` is supplied).', 'gk-gravityview' ),
						],
						'settings'    => [
							'type'                 => 'object',
							'description'          => __( 'Optional setting overrides merged on top of the stored (or staged) slot record.', 'gk-gravityview' ),
							'additionalProperties' => true,
						],
						'staged_slot' => [
							'type'                 => 'object',
							'description'          => __( 'Optional `{ field_id, label?, ...settings }` payload describing an unsaved slot — used when the slot UID does not yet exist on the View.', 'gk-gravityview' ),
							'additionalProperties' => true,
						],
					],
					'required'   => [ 'id', 'area', 'slot' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id' => [ 'type' => 'integer' ],
						'area'    => [ 'type' => 'string' ],
						'slot'    => [ 'type' => 'string' ],
						'html'    => [ 'type' => 'string' ],
						'version' => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'area', 'slot', 'html', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					// Synthesize a POST so the legacy handler's
					// `get_json_params()` reads our `settings`/`staged_slot`
					// payload. The ability itself stays readonly — nothing
					// is mutated, the View is not bumped.
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$request->set_param( 'area', (string) ( $input['area'] ?? '' ) );
					$request->set_param( 'slot', (string) ( $input['slot'] ?? '' ) );
					$body = [];
					if ( isset( $input['settings'] ) && is_array( $input['settings'] ) ) {
						$body['settings'] = $input['settings'];
					}
					if ( isset( $input['staged_slot'] ) && is_array( $input['staged_slot'] ) ) {
						$body['staged_slot'] = $input['staged_slot'];
					}
					if ( ! empty( $body ) ) {
						$request->set_body( wp_json_encode( $body ) ?: '{}' );
						$request->set_header( 'content-type', 'application/json' );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'render_field', $request );
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
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_modify_view_child_slot( (int) $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-field-render' ),
					],
				],
            ]
		);
	}
);
