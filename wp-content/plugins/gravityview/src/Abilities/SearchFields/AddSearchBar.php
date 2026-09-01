<?php
/**
 * Ability: gk-gravityview/search-bar-add
 *
 * The one-call "add a search bar with these fields" entry point. Creates (or
 * reuses) a single search_bar widget on a View and adds the requested search
 * fields to it — hiding the 5-piece slot identity (widget_area + widget_slot +
 * position + server-minted search_slot + field) that the low-level
 * search-field-add ability exposes. Idempotent by field id.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/search-bar-add/run`
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
				'name'                => 'gk-gravityview/search-bar-add',
				'label'               => __( 'Add Search Bar', 'gk-gravityview' ),
				'description'         => __( 'Add a search bar to a View and populate it with search fields in ONE call — the simplest way to make a View searchable. Pass `id` (the View) and `fields`, an array of `{ field_id, input? }` objects; `field_id` is a Gravity Forms field id or a virtual id (`search_all`, `submit`, `search_mode`), and `input` is the control type (`input_text` default, `select`, `multiselect`, `radio`, `checkbox`, `date`, `date_range`). Optional `zone` ("header" default, or "footer") picks where a new bar goes. If the View already has a search bar it adds the fields to that one instead of creating a second; re-running is idempotent (a field already present is updated in place, not duplicated). Each field item may also carry per-field settings: only_loggedin (true to show that search field only to logged-in users) with only_loggedin_cap (a capability such as "manage_options"), and label to override its caption. Returns the widget_area + widget_slot + each field\'s search_slot for later patching. Prefer this over gv_view_widget_add + gv_search_field_add unless you need surgical slot-level control.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-search-fields',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'          => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'zone'        => [
							'type'        => 'string',
							'enum'        => [ 'header', 'footer' ],
							'description' => __( 'Where to create a new search bar: "header" (default) or "footer". Ignored when the View already has a bar.', 'gk-gravityview' ),
						],
						'area'        => [
							'type'        => 'string',
							'description' => __( 'Advanced: explicit widget area key (e.g. "header_top"). Overrides `zone`. Pair with `widget_slot` to target a specific bar on multi-bar Views.', 'gk-gravityview' ),
						],
						'widget_slot' => [
							'type'        => 'string',
							'description' => __( 'Advanced: target a specific existing search_bar slot uid (requires `area`).', 'gk-gravityview' ),
						],
						'position'    => [
							'type'        => 'string',
							'description' => __( 'Advanced: search position bucket new fields go into (default "search-general_top").', 'gk-gravityview' ),
						],
						'fields'      => [
							'type'        => 'array',
							'minItems'    => 1,
							'description' => __( 'Search fields to add. Each item needs `field_id`; `input` is optional (server picks a sensible default per field type).', 'gk-gravityview' ),
							'items'       => [
								'type'                 => 'object',
								'additionalProperties' => true,
								'properties'           => [
									'field_id' => [
										'type'        => [ 'string', 'integer' ],
										'description' => __( 'GF field id or virtual id (search_all, submit, search_mode).', 'gk-gravityview' ),
									],
									'input'             => [
										'type'        => 'string',
										'description' => __( 'Input control: input_text, select, multiselect, radio, checkbox, date, date_range. Omit for the per-field default.', 'gk-gravityview' ),
									],
									'label'             => [ 'type' => 'string' ],
									'only_loggedin'     => [
										'type'        => [ 'boolean', 'string' ],
										'description' => __( 'true (or "1") to show this search field only to logged-in users.', 'gk-gravityview' ),
									],
									'only_loggedin_cap' => [
										'type'        => 'string',
										'description' => __( 'Capability required to see the field when only_loggedin is set (e.g. "read", "manage_options").', 'gk-gravityview' ),
									],
								],
								'required'             => [ 'field_id' ],
							],
						],
						'ifMatch'     => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
						'dry_run'     => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
					],
					'required'             => [ 'id', 'fields' ],
					'additionalProperties' => true,
				],
				'output_schema'       => [
					'type'                 => 'object',
					'properties'           => [
						'view_id'        => [ 'type' => 'integer' ],
						'widget_area'    => [ 'type' => 'string' ],
						'widget_slot'    => [ 'type' => 'string' ],
						'created_widget' => [ 'type' => 'boolean' ],
						'fields'         => [ 'type' => 'array' ],
						'version'        => [ 'type' => 'string' ],
						'dry_run'        => [ 'type' => 'boolean' ],
					],
					'required'             => [ 'view_id', 'widget_area', 'widget_slot', 'fields', 'version' ],
					'additionalProperties' => true,
				],
				'execute_callback'    => static function ( $input ) {
					$input = is_array( $input ) ? $input : [];

					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}

					$is_dry  = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}

					$body = $input;
					unset( $body['ifMatch'], $body['id'], $body['dry_run'] );
					$request->set_body( wp_json_encode( $body ) ?: '{}' );
					$request->set_header( 'content-type', 'application/json' );

					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'add_search_bar', $request );
						}
					);
					if ( is_wp_error( $response ) ) {
						return $response;
					}
					return \GravityKit\GravityView\Abilities\Bootstrap::mark_dry_run( $response->get_data(), $is_dry );
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
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/search-bar-add' ),
					],
				],
			]
		);
	}
);
