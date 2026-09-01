<?php
/**
 * Ability: gk-gravityview/search-field-add
 *
 * Creates a new search field slot inside a `search_bar` widget at the
 * given keyed position. Mirrors the legacy POST
 * `/views/{id}/search-fields` route on InspectorRoute. Auto-migrates
 * legacy `search_fields` storage to the modern `search_fields_section`
 * keyed-by-position shape on first write.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/search-field-add/run`
 * HTTP method:   POST (write)
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'gv_abilities_search_field_add_handle_batch' ) ) {
	/**
	 * Batch handler shim for the search_field_add ability.
	 *
	 * @since 3.0.0
	 *
	 * @param array $input Ability input payload.
	 *
	 * @return array|\WP_Error
	 */
	function gv_abilities_search_field_add_handle_batch( array $input ) {
		return \GravityKit\GravityView\Abilities\Support\BatchHelpers::handle_repository_batch(
			$input,
			new \GravityKit\GravityView\View\Search\SearchFieldSlotRepository()
		);
	}
}

add_action(
    'gk/foundation/abilities/register/before',
    static function () {
		GravityKitFoundation::abilities()->register(
            [
				'name'                => 'gk-gravityview/search-field-add',
				'label'               => __( 'Add Search Field', 'gk-gravityview' ),
				'description'         => __( 'Advanced, slot-level Search Bar editing. For the common task — add a search bar to a View and populate it with fields — use gv_search_bar_add instead (one call, no slot bookkeeping). Use this only for surgical control when you already have the target widget_area + widget_slot + position from gv_view_config_get. Creates a new search field slot inside a `search_bar` widget at the given keyed position (e.g. `search-general_top::100::ROW_UID`). The `field` object must carry at least an `id` (a Gravity Forms field id, or a virtual search id like `search_all`, `submit`, `search_mode`). The `input` slug is validated against the per-field allow-list so typos can\'t silently produce a broken Search Bar. Supports atomic batch mode: pass `batch[]` (up to 50 items, each with `widget_area` + `widget_slot` + `position` + `field` + optional `slot`) plus a top-level `version` token to create multiple search-field slots in one transactional call. Singleton fields and `batch[]` are mutually exclusive. Locate the target search_bar widget (its widget_area + widget_slot) and any existing search_slot/position uids in the widgets tree from gv_view_config_get — there is no auto-resolution to a single bar, search_slot uids are server-minted (read them back from gv_view_config_get), and a legacy search bar exposes modern slots only after its first write through this API.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-search-fields',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'          => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'widget_area' => [
							'type'        => 'string',
							'description' => __( 'Widget area key (e.g. `header_top`).', 'gk-gravityview' ),
						],
						'widget_slot' => [
							'type'        => 'string',
							'description' => __( 'Widget slot uid for the targeted `search_bar` widget.', 'gk-gravityview' ),
						],
						'position'    => [
							'type'        => 'string',
							'description' => __( 'Keyed position inside the search bar (e.g. `search-general_top::100::ROW_UID`).', 'gk-gravityview' ),
						],
						'field'       => [
							'type'                 => 'object',
							'description'          => __( 'Search field spec. Must include `id`. Optional `input` / `label` / per-setting overrides.', 'gk-gravityview' ),
							'properties'           => [
								'id'    => [ 'type' => 'string' ],
								'input' => [ 'type' => 'string' ],
								'label' => [ 'type' => 'string' ],
							],
							'required'             => [ 'id' ],
							'additionalProperties' => true,
						],
						'slot'        => [
							'type'        => 'string',
							'description' => __( 'Optional caller-provided search slot uid. Server generates one when omitted.', 'gk-gravityview' ),
						],
						'dry_run'     => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch'     => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
						'version'     => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. Required when using `batch`.', 'gk-gravityview' ),
						],
						'batch'       => [
							'type'        => 'array',
							'minItems'    => 1,
							'maxItems'    => 50,
							'description' => __( 'Apply N similar operations atomically. All items are validated against the starting state before any persist. If ANY item fails validation, NONE persist and 400 is returned with per-item failures. Mutually exclusive with the singleton fields on this ability.', 'gk-gravityview' ),
							'items'       => [
								'type'                 => 'object',
								'additionalProperties' => false,
								'properties'           => [
									'widget_area' => [ 'type' => 'string' ],
									'widget_slot' => [ 'type' => 'string' ],
									'position'    => [ 'type' => 'string' ],
									'field'       => [
										'type'       => 'object',
										'properties' => [
											'id'    => [ 'type' => 'string' ],
											'input' => [ 'type' => 'string' ],
											'label' => [ 'type' => 'string' ],
										],
										'additionalProperties' => true,
									],
									'slot'        => [ 'type' => 'string' ],
								],
							],
						],
					],
					'required'             => [ 'id' ],
					'additionalProperties' => true,
					// Disjoint required[] sets enforce mutual exclusion at the schema
					// layer — WP core's validator doesn't support JSON Schema `not`.
					'oneOf'                => [
						[ 'required' => [ 'id', 'widget_area', 'widget_slot', 'position', 'field' ] ],
						[ 'required' => [ 'id', 'batch', 'version' ] ],
					],
				],
				'output_schema'       => \GravityKit\GravityView\Abilities\Support\BatchHelpers::output_schema(
                    [
						'type'       => 'object',
						'properties' => [
							'view_id'     => [ 'type' => 'integer' ],
							'widget_area' => [ 'type' => 'string' ],
							'widget_slot' => [ 'type' => 'string' ],
							'position'    => [ 'type' => 'string' ],
							'search_slot' => [ 'type' => 'string' ],
							'field'       => [ 'type' => 'object' ],
							'version'     => [ 'type' => 'string' ],
						],
						'required'   => [ 'view_id', 'widget_area', 'widget_slot', 'position', 'search_slot', 'field', 'version' ],
                    ]
                ),
				'execute_callback'    => static function ( $input ) {
					$input = is_array( $input ) ? $input : [];
					if ( \GravityKit\GravityView\Foundation\Abilities\Support\BatchContract::is_batch_input( $input ) ) {
						return gv_abilities_search_field_add_handle_batch( $input );
					}

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
					unset( $body['ifMatch'], $body['id'] );
					if ( ! empty( $body ) ) {
						$encoded_body = wp_json_encode( $body );
						$request->set_body( is_string( $encoded_body ) ? $encoded_body : '{}' );
						$request->set_header( 'content-type', 'application/json' );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'create_search_field_slot', $request );
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
						'idempotent'  => false,
						'batch'       => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/search-field-add' ),
					],
				],
            ]
		);
	}
);
