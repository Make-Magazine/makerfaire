<?php
/**
 * Ability: gk-gravityview/view-config-get
 *
 * Returns the full editable config tree for a View — template_id (singular,
 * directory zone), template_ids (all zones), form_id, area inventory, field
 * tree, widget tree, template settings, and search criteria. Mirrors the
 * legacy `GET /views/{id}/config` route on InspectorRoute and is the
 * canonical "describe-a-View" call for the inspector + AI clients.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-config-get/run`
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
				'name'                => 'gk-gravityview/view-config-get',
				'label'               => __( 'Get View Config', 'gk-gravityview' ),
				'description'         => __( 'Return the full editable config tree for a View — template_id (directory zone), template_ids (per zone), form_id, area inventory, field tree, widget tree, template settings, and search criteria. Canonical "describe-a-View" call for the inspector and AI clients.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'include' => [
							'type'        => 'array',
							'items'       => [
								'type' => 'string',
								'enum' => [ 'template_id', 'template_ids', 'form_id', 'joins', 'areas', 'fields', 'widgets', 'template_settings', 'search_criteria', 'version' ],
							],
							'description' => __( 'Optional projection — limit the response to these top-level keys. `view_id` is always returned. Unset returns the full tree (default). `joins` is populated only when an add-on (e.g. Multiple Forms) hooks the gk/gravityview/rest/view-config/get filter to inject it.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'id' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'           => [ 'type' => 'integer' ],
						'version'           => [ 'type' => 'string' ],
						'template_id'       => [
							'type'        => 'string',
							'description' => __( 'Directory-zone template id (backward-compat shorthand). Omitted if `include` filtered it out.', 'gk-gravityview' ),
						],
						'template_ids'      => [
							'type'        => 'object',
							'description' => __( 'Per-zone template ids (directory / single / edit). Omitted if `include` filtered it out.', 'gk-gravityview' ),
						],
						'form_id'           => [ 'type' => 'integer' ],
						'joins'             => [
							'type'        => 'array',
							'description' => __( 'Optional. Multiple Forms join definitions (legacy 4-tuple shape: `[base_form_id, base_field_id, join_form_id, join_field_id]`). Present only when the Multiple Forms add-on injects them via the gk/gravityview/rest/view-config/get filter.', 'gk-gravityview' ),
						],
						'areas'             => [ 'type' => 'object' ],
						'fields'            => [ 'type' => 'object' ],
						'widgets'           => [ 'type' => 'object' ],
						'template_settings' => [ 'type' => 'object' ],
						'search_criteria'   => [ 'type' => 'object' ],
					],
					'required'   => [ 'view_id' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$request = new \WP_REST_Request( 'GET', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_config', $request );
					if ( is_wp_error( $response ) ) {
						return $response;
					}
					$data = $response->get_data();

					// Apply projection. `view_id` always survives; everything
					// else is opt-in when `include` is set. Unknown keys in
					// `include` are silently ignored — the input_schema enum
					// already rejected anything off-list.
					if ( isset( $input['include'] ) && is_array( $input['include'] ) && ! empty( $input['include'] ) ) {
						$keep            = array_fill_keys( $input['include'], true );
						$keep['view_id'] = true;
						$data            = array_intersect_key( $data, $keep );
					}

					return $data;
				},
				'permission_callback' => static function ( $input ) {
					$view_id = (int) ( $input['id'] ?? 0 );
					if ( $view_id <= 0 ) {
						return false;
					}
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_read_view( (int) $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-config-get' ),
					],
				],
            ]
		);
	}
);
