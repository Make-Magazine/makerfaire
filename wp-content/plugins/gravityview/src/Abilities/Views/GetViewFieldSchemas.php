<?php
/**
 * Ability: gk-gravityview/view-field-schemas-get
 *
 * Returns settings schemas for fields configured on the View.
 *
 *   - With NO `area` + `slot`: returns a `{area}/{slot}` → schema map
 *     for EVERY configured field slot (bulk mode — the inspector uses
 *     this to render any selected field without a follow-up round trip).
 *   - With BOTH `area` + `slot`: returns the schema for just THAT slot,
 *     under the same `{area}/{slot}` → schema shape (one-key map).
 *
 * Consolidates the legacy split between `get-view-field-schemas` (bulk)
 * and `get-view-field-schema` (single) — the only difference was the
 * filter, and the previous plural-s naming was a footgun.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-field-schemas-get/run`
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
				'name'                => 'gk-gravityview/view-field-schemas-get',
				'label'               => __( 'Get View Field Schemas', 'gk-gravityview' ),
				'description'         => __( 'Return field settings schemas for the View. Without `area` + `slot`, returns a `{area}/{slot}` → schema map for every configured slot (bulk). With both `area` + `slot`, returns the schema for just that one slot. One endpoint, two modes — caller picks via the optional filter.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'   => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'area' => [
							'type'        => 'string',
							'description' => __( 'Optional: with `slot`, narrow result to this single slot.', 'gk-gravityview' ),
						],
						'slot' => [
							'type'        => 'string',
							'description' => __( 'Optional: with `area`, narrow result to this single slot.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'id' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id' => [ 'type' => 'integer' ],
						'schemas' => [
							'type'        => 'object',
							'description' => __( 'Map of `{area_key}/{slot_uid}` to per-slot settings schema. One entry when `area`+`slot` were filtered, every configured slot otherwise.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'view_id', 'schemas' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}

					$area = isset( $input['area'] ) ? (string) $input['area'] : '';
					$slot = isset( $input['slot'] ) ? (string) $input['slot'] : '';

					$request = new \WP_REST_Request( 'GET', '' );
					$request->set_param( 'id', (int) $view_id_for_check );

					$route = new \GravityKit\GravityView\REST\InspectorRoute();

					// Filtered mode — `area` + `slot` both required if either set.
					// Delegates to the single-slot legacy handler so output
					// shape matches the bulk path's per-slot entry exactly.
					if ( '' !== $area && '' !== $slot ) {
						$request->set_param( 'area', $area );
						$request->set_param( 'slot', $slot );
						$response = $route->invoke_safely( 'get_field_settings_schema_one', $request );
						if ( is_wp_error( $response ) ) {
							return $response;
						}
						$single = $response->get_data();
						return [
							'view_id' => (int) $view_id_for_check,
							'schemas' => [ ( $area . '/' . $slot ) => ( $single['schema'] ?? $single ) ],
						];
					}

					// Bulk mode — every configured slot.
					$response = $route->invoke_safely( 'get_field_settings_schema_bulk', $request );
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
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-field-schemas-get' ),
					],
				],
            ]
		);
	}
);
