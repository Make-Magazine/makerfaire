<?php
/**
 * Ability: gk-gravityview/field-type-schema-get
 *
 * Returns the configuration schema for a GravityView field, widget,
 * or search-field type independent of any specific slot or View.
 * Lets agents discover what settings a type supports BEFORE placing
 * it, so they can build a complete configuration in one pass.
 *
 * Dispatch is context-aware — several type slugs (`created_by`,
 * `custom`/`custom_content`) exist on multiple surfaces. Pass
 * `context=search` to prefer the search-field resolver; otherwise
 * widgets resolve first, then fields.
 *
 * The response `kind` field tells the caller which surface the
 * schema came from (`field` / `widget` / `search_field`).
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/field-type-schema-get/run`
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
				'name'                => 'gk-gravityview/field-type-schema-get',
				'label'               => __( 'Get Field Type Schema', 'gk-gravityview' ),
				'description'         => __( 'Return the configuration schema for a GravityView field, widget, or search-field type independent of any specific slot or View. Lets agents discover what settings a type supports before placing it. Dispatch is context-aware (context=search prefers the search-field resolver). The response kind field tells the caller which surface the schema came from (field / widget / search_field).', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'field_type'  => [
							'type'        => 'string',
							'description' => __( 'Field/widget/search-field type slug to resolve (e.g. "text", "page_links", "search_all").', 'gk-gravityview' ),
						],
						'template_id' => [
							'type'        => 'string',
							'description' => __( 'Template id for layout-specific settings. Defaults to "default_list".', 'gk-gravityview' ),
						],
						'context'     => [
							'type'        => 'string',
							'description' => __( 'One of "multiple", "single", "edit", "search". Defaults to "multiple". Use "search" to prefer the search-field resolver for overlapping slugs.', 'gk-gravityview' ),
						],
						'input_type'  => [
							'type'        => 'string',
							'description' => __( 'Optional GF input_type override (rarely needed; used internally for slot-specific schema resolution).', 'gk-gravityview' ),
						],
						'form_id'     => [
							'type'        => 'integer',
							'description' => __( 'Optional Gravity Forms form id; lets search-field resolution narrow input-type sets to the specific GF field.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'field_type' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'field_type'  => [ 'type' => 'string' ],
						'kind'        => [
							'type'        => 'string',
							'enum'        => [ 'field', 'widget', 'search_field' ],
							'description' => __( 'Which surface the schema was resolved from.', 'gk-gravityview' ),
						],
						'template_id' => [ 'type' => 'string' ],
						'context'     => [ 'type' => 'string' ],
						'schema'      => [
							'type'        => 'array',
							'description' => __( 'Ordered list of setting definitions for this type. Each entry carries slug / type / label / desc / value / group, plus options when the setting is a select/radio/checkbox.', 'gk-gravityview' ),
							'items'       => [ 'type' => 'object' ],
						],
					],
					'required'   => [ 'field_type', 'kind', 'schema' ],
				],
				'execute_callback'    => static function ( $input ) {
					$request = new \WP_REST_Request( 'GET', '' );
					$request->set_param( 'field_type', (string) ( $input['field_type'] ?? '' ) );
					if ( isset( $input['template_id'] ) ) {
						$request->set_param( 'template_id', (string) $input['template_id'] );
					}
					if ( isset( $input['context'] ) ) {
						$request->set_param( 'context', (string) $input['context'] );
					}
					if ( isset( $input['input_type'] ) ) {
						$request->set_param( 'input_type', (string) $input['input_type'] );
					}
					if ( isset( $input['form_id'] ) ) {
						$request->set_param( 'form_id', (int) $input['form_id'] );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_field_type_schema', $request );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/field-type-schema-get' ),
					],
				],
            ]
		);
	}
);
