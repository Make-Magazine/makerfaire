<?php
/**
 * Ability: gk-gravityview/view-partial-render
 *
 * Renders a subtree of a View — a zone, a Layout Builder row, or an
 * explicit list of (area, slot) pairs — so page builders can re-paint
 * only the dirty section after a token / field edit. Delegates to the
 * existing `view-field-render` rendering pipeline per slot so the
 * markup contract stays identical.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-partial-render/run`
 * HTTP method:   POST (read-shaped — no persisted mutation)
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
				'name'                => 'gk-gravityview/view-partial-render',
				'label'               => __( 'Render View Partial', 'gk-gravityview' ),
				'description'         => __( 'Render a subtree of the View (a single zone, a Layout Builder row, or an explicit list of slot references) so a page-builder client can re-paint only the dirty subtree after a token / field edit. The rendered HTML matches the per-layout wrapper contract `view-field-render` uses.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-preview',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'    => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'scope' => [
							'type'        => 'object',
							'description' => sprintf(
								/* translators: %1$s, %2$s, %3$s are JSON shape examples for the scope parameter; do not translate the JSON itself. */
								__( 'Subtree to render. Shapes: %1$s / %2$s / %3$s.', 'gk-gravityview' ),
								'{ type: "zone", area: "<area-key>" }',
								'{ type: "row", row_uid: "<uid>" }',
								'{ type: "slots", slots: [ { area, slot }, ... ] }'
							),
						],
					],
					'required'             => [ 'id', 'scope' ],
					'additionalProperties' => true,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'  => [ 'type' => 'integer' ],
						'scope'    => [ 'type' => 'object' ],
						'rendered' => [ 'type' => 'array' ],
						'version'  => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'rendered', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$request->set_param( 'scope', $input['scope'] ?? [] );

					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'render_partial', $request );
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
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_render_field( $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						// Semantically read-only (no state mutation), but POST-shaped
						// so the scope payload can be sent in a JSON body — the
						// abilities API gates GET-only on readonly:true so we use
						// readonly:false to allow POST.
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-partial-render' ),
					],
				],
            ]
		);
	}
);
