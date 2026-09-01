<?php
/**
 * Ability: gk-gravityview/search-input-types-list
 *
 * Returns the canonical set of search-field input type slugs
 * (`input_text`, `date`, `select`, `multiselect`, `radio`,
 * `checkbox`, `date_range`, `number_range`, `submit`, `hidden`, …)
 * plus anything add-ons have registered via
 * `gravityview/search/input_labels`. Use to validate user-supplied
 * `field.input` values BEFORE issuing a search-field write.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/search-input-types-list/run`
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
				'name'                => 'gk-gravityview/search-input-types-list',
				'label'               => __( 'List Search Input Types', 'gk-gravityview' ),
				'description'         => __( 'Return the GLOBAL catalogue of search-field input type slugs (input_text, date, select, multiselect, radio, checkbox, date_range, number_range, submit, hidden, …) plus anything add-ons registered. NOTE: this is the full set, not what a specific field accepts — each field type narrows it (e.g. a text field allows only input_text; a Drop Down allows select/radio/link). The server enforces the per-field allow-list on write and returns the valid slugs in the error if you pick a disallowed one.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'input_types' => [
							'type'        => 'array',
							'items'       => [ 'type' => 'string' ],
							'description' => __( 'Valid search input type slugs.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'input_types' ],
				],
				'execute_callback'    => static function () {
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = $route->invoke_safely( 'get_search_field_input_types' );
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/search-input-types-list' ),
					],
				],
            ]
		);
	}
);
