<?php
/**
 * Ability: gk-gravityview/merge-tag-data-get
 *
 * Returns the merge-tag dataset (form fields + standard merge tags) for one or
 * more Gravity Forms forms. The View editor's Gravity Forms merge-tag UI calls
 * this whenever the Data Source form (or the set of joined forms from the
 * Multiple Forms add-on) changes, so the merge-tag dropdowns can populate
 * immediately — without requiring the View to be saved first.
 *
 * When multiple `form_ids` are supplied, the primary form's id / title drive
 * the response shape and fields from every additional form are appended to the
 * primary's field list. Field-level data comes from `gravityview_get_form()`;
 * merge tags come from `GFCommon::get_merge_tags()`.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/merge-tag-data-get/run`
 * HTTP method:   GET (readonly ability — the Abilities API enforces GET for these)
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
				'name'                => 'gk-gravityview/merge-tag-data-get',
				'label'               => __( 'Get Merge Tag Data', 'gk-gravityview' ),
				'description'         => __( 'Return the merge-tag dataset (form fields plus the standard merge-tag catalogue) for one or more Gravity Forms forms. Use to populate the View editor\'s merge-tag dropdowns as soon as a Data Source form (or joined forms) is selected, without requiring the View to be saved first.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'input_schema'        => [
					'type'                 => 'object',
					'additionalProperties' => false,
					'properties'           => [
						'form_ids' => [
							'type'        => 'array',
							'description' => __( 'One or more Gravity Forms form ids. The first id is treated as the primary form (its id/title drive the response); fields from any additional ids are appended for joined-form Views.', 'gk-gravityview' ),
							'items'       => [
								'type'    => 'integer',
								'minimum' => 1,
							],
							'minItems'    => 1,
							'maxItems'    => \GravityKit\GravityView\REST\InspectorRoute::MAX_MERGE_TAG_FORM_IDS,
							'uniqueItems' => true,
						],
					],
					'required'             => [ 'form_ids' ],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'form'       => [
							'oneOf' => [
								[ 'type' => 'null' ],
								[
									'type'       => 'object',
									'properties' => [
										'id'     => [ 'type' => 'integer' ],
										'title'  => [ 'type' => 'string' ],
										'fields' => [ 'type' => 'array' ],
									],
									'required'   => [ 'id', 'title', 'fields' ],
								],
							],
						],
						'merge_tags' => [
							'type'        => 'object',
							'description' => __( 'Merge-tag groups keyed by category, as returned by GFCommon::get_merge_tags(). Each value carries a `label` and a `tags` array of `{ tag, label }` entries.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'form', 'merge_tags' ],
				],
				'execute_callback'    => static function ( $input ) {
					$request = new \WP_REST_Request( 'GET', '' );
					$request->set_param( 'form_ids', $input['form_ids'] ?? [] );

					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_merge_tag_data', $request );

					if ( is_wp_error( $response ) ) {
						return $response;
					}

					return $response->get_data();
				},
				'permission_callback' => static function () {
					// Same gate as `gk-gravityview/forms-list`: anyone who can author
					// a View can already enumerate the connected forms' fields via
					// the field picker, so reading the merge-tag projection of that
					// same data adds no surface.
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_access_discovery();
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/merge-tag-data-get' ),
					],
				],
			]
		);
	}
);
