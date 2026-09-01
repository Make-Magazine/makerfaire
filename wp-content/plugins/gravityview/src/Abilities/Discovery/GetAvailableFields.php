<?php
/**
 * Ability: gk-gravityview/available-fields-get
 *
 * Lists every field a View can place into a field slot — both
 * GravityView meta-fields (entry_link, custom_content, edit_link,
 * etc., context-filtered) AND the Gravity Forms fields from the
 * View's backing form. Use to discover valid `field_id` values
 * before adding a field to a row/area.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/available-fields-get/run`
 * HTTP method:   GET
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
				'name'                => 'gk-gravityview/available-fields-get',
				'label'               => __( 'List Available Fields', 'gk-gravityview' ),
				'description'         => __( 'List every field a View can place into a field slot — both GravityView meta-fields (entry_link, custom_content, edit_link, etc., context-filtered) and the Gravity Forms fields from the View\'s backing form. Use to discover valid field_id values before adding a field to a row/area.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'   => [
							'type'        => 'integer',
							'description' => __( 'View id whose available fields should be listed.', 'gk-gravityview' ),
						],
						'zone' => [
							'type'        => 'string',
							'enum'        => [ 'directory', 'single', 'edit' ],
							'description' => __( 'Zone to filter GV meta-fields against (directory / single / edit). Defaults to directory when omitted.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'id' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'     => [ 'type' => 'integer' ],
						'form_id'     => [ 'type' => 'integer' ],
						'context'     => [
							'type'        => 'string',
							'description' => __( 'Resolved context for the requested zone (e.g. "multiple" for directory, "single", "edit").', 'gk-gravityview' ),
						],
						'form_fields' => [
							'type'  => 'array',
							'items' => [
								'type'       => 'object',
								'properties' => [
									'id'         => [
										'type'        => 'string',
										'description' => __( 'GF field id (use as field_id when adding to a slot).', 'gk-gravityview' ),
									],
									'label'      => [ 'type' => 'string' ],
									'input_type' => [ 'type' => 'string' ],
									'type'       => [ 'type' => 'string' ],
								],
								'required'   => [ 'id', 'label' ],
							],
						],
						'gv_fields'   => [
							'type'  => 'array',
							'items' => [
								'type'       => 'object',
								'properties' => [
									'id'    => [
										'type'        => 'string',
										'description' => __( 'GV meta-field slug (use as field_id when adding to a slot).', 'gk-gravityview' ),
									],
									'label' => [ 'type' => 'string' ],
									'group' => [ 'type' => 'string' ],
									'icon'  => [ 'type' => 'string' ],
								],
								'required'   => [ 'id', 'label' ],
							],
						],
					],
					'required'   => [ 'view_id', 'form_id', 'context', 'form_fields', 'gv_fields' ],
				],
				'execute_callback'    => static function ( $input ) {
					$request = new \WP_REST_Request( 'GET', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					if ( isset( $input['zone'] ) && is_string( $input['zone'] ) && '' !== $input['zone'] ) {
						$request->set_param( 'zone', $input['zone'] );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_available_fields', $request );
					if ( is_wp_error( $response ) ) {
						return $response;
					}
					return $response->get_data();
				},
				'permission_callback' => static function ( $input ) {
					$vid = (int) ( $input['id'] ?? 0 );
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_access_view_discovery( $vid );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/available-fields-get' ),
					],
				],
            ]
		);
	}
);
