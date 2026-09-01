<?php
/**
 * Ability: gk-gravityview/template-settings-schema-get
 *
 * Returns the View-level settings schema for a given template
 * (layout) — the settings that show on the "Settings" tab inside
 * the View editor. Each entry carries slug / type / label / desc /
 * value / group (plus options when the setting is a select). Slugs
 * for namespaced silo sources (e.g. Maps) appear as dotted paths
 * (`maps.zoom_level`) so writes route to the correct meta key.
 * Use to discover valid `template_settings` keys before patching.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/template-settings-schema-get/run`
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
				'name'                => 'gk-gravityview/template-settings-schema-get',
				'label'               => __( 'Get Template Settings Schema', 'gk-gravityview' ),
				'description'         => __( 'Return the View-level settings schema for a given template (layout). Each entry carries slug / type / label / desc / value / group (plus options when the setting is a select). Slugs for namespaced silo sources appear as dotted paths (e.g. maps.zoom_level) so writes route to the correct meta key. Use to discover valid template_settings keys before patching.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'template_id' => [
							'type'        => 'string',
							'description' => __( 'Template (layout) id from gk-gravityview/layouts-list (e.g. "default_list", "default_table", "gravityview-layout-builder").', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'template_id' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'template_id' => [ 'type' => 'string' ],
						'schema'      => [
							'type'        => 'array',
							'description' => __( 'Ordered list of setting definitions. Each entry carries slug / type / label / desc / value / group, plus options when the setting is a select/radio/checkbox.', 'gk-gravityview' ),
							'items'       => [
								'type'       => 'object',
								'properties' => [
									'slug'    => [ 'type' => 'string' ],
									'type'    => [ 'type' => 'string' ],
									'label'   => [ 'type' => 'string' ],
									'desc'    => [ 'type' => 'string' ],
									'value'   => [],
									'group'   => [ 'type' => 'string' ],
									'options' => [ 'type' => [ 'object', 'array' ] ],
								],
								'required'   => [ 'slug', 'type' ],
							],
						],
					],
					'required'   => [ 'template_id', 'schema' ],
				],
				'execute_callback'    => static function ( $input ) {
					$request = new \WP_REST_Request( 'GET', '' );
					$request->set_param( 'template_id', (string) ( $input['template_id'] ?? '' ) );
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_template_settings_schema', $request );
					if ( is_wp_error( $response ) ) {
						return $response;
					}
					return $response->get_data();
				},
				'permission_callback' => static function () {
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_access_discovery();
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/template-settings-schema-get' ),
					],
				],
            ]
		);
	}
);
