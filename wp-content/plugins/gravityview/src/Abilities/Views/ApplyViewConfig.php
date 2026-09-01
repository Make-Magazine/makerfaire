<?php
/**
 * Ability: gk-gravityview/view-config-apply
 *
 * Bulk-applies a config tree (template_id, template_ids, template_settings,
 * search_criteria, fields, widgets) to a View in one transactional call.
 * Each area present in `fields` / `widgets` replaces the existing area
 * (`mode=replace`, default) or appends to it (`mode=merge`). Mirrors the
 * legacy `POST /views/{id}/config/_apply` route on InspectorRoute.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-config-apply/run`
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
				'name'                => 'gk-gravityview/view-config-apply',
				'label'               => __( 'Apply View Config', 'gk-gravityview' ),
				'description'         => __( 'Bulk-apply a config tree (template_id, template_ids, template_settings, search_criteria, fields, widgets) to a View in one transactional call. In `mode=replace` (default) each area present replaces the existing area; in `mode=merge` items append. Returns an `applied` summary plus optional `warnings` when sanitization dropped any setting values.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'                => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'template_id'       => [
							'type'        => 'string',
							'description' => __( 'Layout template id (e.g. `default_table`, `gravityview-layout-builder`). Discover valid ids via gk-gravityview/layouts-list.', 'gk-gravityview' ),
						],
						'template_ids'      => [
							'type'                 => 'object',
							'description'          => __( 'Per-zone layout overrides keyed by zone (`directory`, `single`, `edit`). Same template ids as `list-layouts`.', 'gk-gravityview' ),
							'additionalProperties' => true,
						],
						'template_settings' => [
							'type'                 => 'object',
							'description'          => __( 'Map of setting key → value. Discover valid keys + types via gk-gravityview/template-settings-schema-get.', 'gk-gravityview' ),
							'additionalProperties' => true,
						],
						'search_criteria'   => [
							'type'                 => 'object',
							'description'          => __( 'Search-criteria overrides merged into template_settings.', 'gk-gravityview' ),
							'additionalProperties' => true,
						],
						'fields'            => [
							'type'                 => 'object',
							'description'          => __( 'Field tree keyed by area id (e.g. `directory_table-columns`). Each area value may be either an array of slot objects OR a keyed map of `{slot_uid: slot_object}` — the latter is the shape `view-config-get` returns, so the round-trip works without reshaping. Discover the per-View area shape via gk-gravityview/view-areas-get, and the per-slot setting schema via gk-gravityview/view-field-schemas-get.', 'gk-gravityview' ),
							'additionalProperties' => true,
						],
						'widgets'           => [
							'type'                 => 'object',
							'description'          => __( 'Widget tree keyed by zone id (e.g. `header_top`). Each zone value may be either an array of widget objects OR a keyed map of `{slot_uid: widget_object}` — same dual-shape contract as `fields`. Discover valid zones via gk-gravityview/widget-zones-list and widget ids via gk-gravityview/widgets-list.', 'gk-gravityview' ),
							'additionalProperties' => true,
						],
						'joins'             => [
							'type'        => 'array',
							'description' => __( 'Optional. Multiple Forms join definitions, legacy 4-tuple shape: `[base_form_id, base_form_field_id, join_form_id, join_form_field_id]`. Persisted by the Multiple Forms add-on via the gk/gravityview/rest/view-config/apply/after action; silently ignored when the add-on is inactive. Discover valid join keys via gk-multiple-forms/list-joinable-fields.', 'gk-gravityview' ),
							'items'       => [
								'type'     => 'array',
								'minItems' => 4,
								'maxItems' => 4,
							],
						],
						'mode'              => [
							'type'        => 'string',
							'enum'        => [ 'replace', 'merge' ],
							'description' => __( '`replace` (default) replaces each present area; `merge` appends entries.', 'gk-gravityview' ),
						],
						'dry_run'           => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
						'ifMatch'           => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read. The server returns 412 if it disagrees.', 'gk-gravityview' ),
						],
					],
					'required'             => [ 'id' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'  => [ 'type' => 'integer' ],
						'version'  => [ 'type' => 'string' ],
						'applied'  => [
							'type'       => 'object',
							'properties' => [
								'fields'  => [
									'type'        => 'array',
									'description' => __( 'Slot records persisted across all fields areas, in apply order.', 'gk-gravityview' ),
									'items'       => [
										'type'       => 'object',
										'properties' => [
											'area'     => [ 'type' => 'string' ],
											'slot'     => [ 'type' => 'string' ],
											'field_id' => [ 'type' => 'string' ],
										],
									],
								],
								'widgets' => [
									'type'        => 'array',
									'description' => __( 'Slot records persisted across all widget areas, in apply order.', 'gk-gravityview' ),
									'items'       => [
										'type'       => 'object',
										'properties' => [
											'area'     => [ 'type' => 'string' ],
											'slot'     => [ 'type' => 'string' ],
											'field_id' => [ 'type' => 'string' ],
										],
									],
								],
								'mode'    => [ 'type' => 'string' ],
							],
						],
						'warnings' => [
							'type'        => 'array',
							'description' => __( 'Present only when sanitization dropped any setting values.', 'gk-gravityview' ),
						],
					],
					'required'   => [ 'view_id', 'version', 'applied' ],
				],
				'execute_callback'    => static function ( $input ) {
					// apply_config reads everything except `id` from the JSON body
					// via $request->get_json_params(), so we serialize the remaining
					// input as the body and only set `id` as a URL param.
					$view_id_for_check = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id_for_check ) ) {
						return $view_id_for_check;
					}
					$is_dry = (bool) ( $input['dry_run'] ?? false );
					$request = new \WP_REST_Request( 'POST', '' );
					$request->set_param( 'id', (int) ( $input['id'] ?? 0 ) );
					if ( ! empty( $input['ifMatch'] ) ) {
						$request->add_header( 'If-Match', (string) $input['ifMatch'] );
					}
					$body = $input;
					unset( $body['ifMatch'], $body['id'] );

					// Accept the keyed-map shape view-config-get returns
					// (`{area: {uid: slot}}`) alongside the legacy array
					// shape (`{area: [slot, slot, ...]}`). For keyed-map
					// items, stamp the key as `slot` so InspectorRoute keeps
					// slot UIDs stable across read → write round-trips. For
					// every slot regardless of shape, alias the legacy
					// storage key `id` (returned by view-config-get) to the
					// apply input's required `field_id` — the read surface
					// returns raw storage, the write surface validates
					// against the documented input contract.
					$alias_field_id = static function ( $slot ) {
						if ( ! is_array( $slot ) ) {
							return $slot;
						}
						if ( ! isset( $slot['field_id'] ) && isset( $slot['id'] ) ) {
							$slot['field_id'] = $slot['id'];
						}
						return $slot;
					};
					$normalize_tree = static function ( $tree, $apply_alias = true ) use ( $alias_field_id ) {
						if ( ! is_array( $tree ) ) {
							return $tree;
						}
						$normalized = [];
						foreach ( $tree as $area_key => $area_value ) {
							if ( ! is_array( $area_value ) ) {
								$normalized[ $area_key ] = $area_value;
								continue;
							}
							// Detect associative (keyed-map) shape via at least
							// one non-integer key, indicating slot_uid keys.
							$is_keyed = false;
							foreach ( $area_value as $k => $_v ) {
								if ( ! is_int( $k ) ) {
									$is_keyed = true;
									break;
								}
							}
							if ( ! $is_keyed ) {
								$indexed = [];
								foreach ( $area_value as $slot ) {
									$indexed[] = $apply_alias ? $alias_field_id( $slot ) : $slot;
								}
								$normalized[ $area_key ] = $indexed;
								continue;
							}
							$indexed = [];
							foreach ( $area_value as $slot_uid => $slot ) {
								if ( ! is_array( $slot ) ) {
									continue;
								}
								if ( ! isset( $slot['slot'] ) ) {
									$slot['slot'] = (string) $slot_uid;
								}
								$indexed[] = $apply_alias ? $alias_field_id( $slot ) : $slot;
							}
							$normalized[ $area_key ] = $indexed;
						}
						return $normalized;
					};
					if ( isset( $body['fields'] ) ) {
						$body['fields'] = $normalize_tree( $body['fields'], true );
					}
					if ( isset( $body['widgets'] ) ) {
						$body['widgets'] = $normalize_tree( $body['widgets'], false );
					}
					if ( ! empty( $body ) ) {
						$request->set_body( wp_json_encode( $body ) ?: '{}' );
						$request->set_header( 'content-type', 'application/json' );
					}
					$route    = new \GravityKit\GravityView\REST\InspectorRoute();
					$response = \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $route, $request ) {
							return $route->invoke_safely( 'apply_config', $request );
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
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_edit_view( (int) $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-config-apply' ),
					],
				],
            ]
		);
	}
);
