<?php
/**
 * Ability: gk-gravityview/views-scan
 *
 * Expensive View config scan. Use after narrowing with views-list when an
 * agent needs filters that require inspecting serialized View configuration.
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
				'name'                => 'gk-gravityview/views-scan',
				'label'               => __( 'Scan Views', 'gk-gravityview' ),
				'description'         => __( 'Scan View configuration for expensive filters such as field type, missing fields, widget type, add-on settings, template availability, health, or config text. Use views-list first to narrow candidates.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'status'             => \GravityKit\GravityView\Abilities\Bootstrap::status_filter_schema(),
						'form_id'            => [ 'type' => 'integer' ],
						'template'           => [ 'type' => 'string' ],
						'ids'                => [
							'type'  => 'array',
							'items' => [ 'type' => 'integer' ],
						],
						'exclude_ids'        => [
							'type'  => 'array',
							'items' => [ 'type' => 'integer' ],
						],
						'field_type'         => [ 'type' => 'string' ],
						'field_id'           => [ 'type' => 'string' ],
						'field_label'        => [ 'type' => 'string' ],
						'field_missing'      => [ 'type' => 'boolean' ],
						'widget_type'        => [ 'type' => 'string' ],
						'addon_settings'     => [
							'type' => 'string',
							'enum' => [ 'maps', 'datatables', 'multiple-forms', 'joins' ],
						],
						'template_available' => [ 'type' => 'boolean' ],
						'health'             => [
							'type' => 'string',
							'enum' => [ 'ok', 'template_missing', 'template_addon_inactive', 'form_missing', 'field_missing' ],
						],
						'config_search'      => [ 'type' => 'string' ],
						'include'            => [
							'type'  => 'array',
							'items' => [
								'type' => 'string',
								'enum' => [ 'field_count', 'widget_count', 'search_field_count', 'entry_count_estimate', 'description', 'addon_settings_summary', 'form_title', 'form_modified', 'form_active', 'field_types', 'field_ids', 'missing_field_refs', 'template_available', 'health', 'search_summary' ],
							],
						],
						'cursor'             => [ 'type' => 'string' ],
						'per_page'           => [
							'type'        => 'integer',
							'minimum'     => 1,
							'maximum'     => 25,
							'description' => __( 'Page size, 1-25. Default 20. Scan is server-side filtered for performance; use gk-gravityview/views-list for higher per_page when you only need top-level metadata.', 'gk-gravityview' ),
						],
						'candidate_cap'      => [
							'type'    => 'integer',
							'minimum' => 1,
							'maximum' => 500,
						],
					],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'views'          => [ 'type' => 'array' ],
						'next_cursor'    => [
							'oneOf' => [
								[ 'type' => 'string' ],
								[ 'type' => 'null' ],
							],
						],
						'scan_truncated' => [ 'type' => 'boolean' ],
						'scanned_count'  => [ 'type' => 'integer' ],
						'candidate_cap'  => [ 'type' => 'integer' ],
					],
					'required'   => [ 'views', 'next_cursor', 'scan_truncated', 'scanned_count', 'candidate_cap' ],
				],
				'execute_callback'    => static function ( $input ) {
					$input         = is_array( $input ) ? $input : [];
					$per_page      = isset( $input['per_page'] ) ? max( 1, min( 25, (int) $input['per_page'] ) ) : 20;
					$candidate_cap = isset( $input['candidate_cap'] ) ? max( 1, min( 500, (int) $input['candidate_cap'] ) ) : 500;
					$offset        = gv_abilities_views_scan_decode_cursor( (string) ( $input['cursor'] ?? '' ) );
					$include_keys  = \GravityKit\GravityView\Foundation\Abilities\Support\Projection::normalize_include(
						$input['include'] ?? [],
						[ 'field_count', 'widget_count', 'search_field_count', 'entry_count_estimate', 'description', 'addon_settings_summary', 'form_title', 'form_modified', 'form_active', 'field_types', 'field_ids', 'missing_field_refs', 'template_available', 'health', 'search_summary' ]
					);
					$include_keys  = array_values( array_unique( array_merge( $include_keys, [ 'template_available', 'health' ] ) ) );

					$args = gv_abilities_views_scan_candidate_args( $input, $candidate_cap + 1, $offset );
					$args = (array) apply_filters( 'gk/gravityview/rest/views/scan/query-args', $args, $input );

					$query = new \WP_Query( $args );
					/** @var \WP_Post[] $candidates */
					$candidates = $query->posts;
					$truncated  = count( $candidates ) > $candidate_cap;
					if ( $truncated ) {
						array_pop( $candidates );
					}

					update_meta_cache( 'post', wp_list_pluck( $candidates, 'ID' ) );

					$templates  = gv_abilities_registered_templates();
					$forms      = gv_abilities_forms_by_id();
					$views      = [];
					$scanned    = 0;
					$view_perms = new \GravityKit\GravityView\Permissions\Permissions();

					foreach ( $candidates as $post ) {
						++$scanned;
						if ( ! $view_perms->can_edit_view( (int) $post->ID ) ) {
							continue;
						}
						if ( ! gv_abilities_views_scan_matches( $post, $input, $templates, $forms ) ) {
							continue;
						}

						$views[] = gv_abilities_views_scan_row( $post, $include_keys, $templates, $forms );
						if ( count( $views ) >= $per_page ) {
							break;
						}
					}

					$next_offset = $offset + $scanned;
					$next_cursor = ( $truncated || $scanned < count( $candidates ) ) ? gv_abilities_views_scan_encode_cursor( $next_offset ) : null;

					return [
						'views'          => $views,
						'next_cursor'    => $next_cursor,
						'scan_truncated' => $truncated,
						'scanned_count'  => $scanned,
						'candidate_cap'  => $candidate_cap,
					];
				},
				'permission_callback' => static function () {
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_access_discovery();
				},
				'meta'                => [
					'show_in_rest' => true,
					'expensive'    => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
						'expensive'   => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/views-scan' ),
					],
				],
            ]
		);
	}
);

if ( ! function_exists( 'gv_abilities_views_scan_candidate_args' ) ) {
	/**
	 * Build the cheap candidate query for views-scan.
	 *
	 * @param array $input Ability input.
	 * @param int   $limit Candidate query limit.
	 * @param int   $offset Candidate offset.
	 * @return array<string,mixed>
	 */
	function gv_abilities_views_scan_candidate_args( array $input, int $limit, int $offset ): array {
		$args = [
			'post_type'           => 'gravityview',
			'post_status'         => gv_abilities_list_views_statuses( $input['status'] ?? null ),
			'posts_per_page'      => $limit,
			'offset'              => $offset,
			'orderby'             => 'ID',
			'order'               => 'ASC',
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'perm'                => 'editable',
		];

		if ( ! empty( $input['ids'] ) && is_array( $input['ids'] ) ) {
			$args['post__in'] = array_values( array_filter( array_map( 'intval', $input['ids'] ) ) );
		}
		if ( ! empty( $input['exclude_ids'] ) && is_array( $input['exclude_ids'] ) ) {
			$args['post__not_in'] = array_values( array_filter( array_map( 'intval', $input['exclude_ids'] ) ) );
		}

		$meta_query = [];
		if ( isset( $input['form_id'] ) && (int) $input['form_id'] > 0 ) {
			$meta_query[] = [
				'key'     => '_gravityview_form_id',
				'value'   => (int) $input['form_id'],
				'compare' => '=',
			];
		}
		if ( isset( $input['template'] ) && '' !== (string) $input['template'] ) {
			$meta_query[] = [
				'key'     => '_gravityview_directory_template',
				'value'   => sanitize_key( (string) $input['template'] ),
				'compare' => '=',
			];
		}
		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		return $args;
	}
}

if ( ! function_exists( 'gv_abilities_views_scan_matches' ) ) {
	/**
	 * Test one candidate against expensive scan filters.
	 *
	 * @param \WP_Post $post      Candidate View.
	 * @param array    $input     Ability input.
	 * @param array    $templates Registered templates.
	 * @param array    $forms     Forms keyed by id.
	 * @return bool
	 */
	function gv_abilities_views_scan_matches( $post, array $input, array $templates, array $forms ): bool {
		$view_id     = (int) $post->ID;
		$form_id     = (int) get_post_meta( $view_id, '_gravityview_form_id', true );
		$template_id = (string) get_post_meta( $view_id, '_gravityview_directory_template', true );
		$template_id = '' === $template_id ? 'default_list' : $template_id;
		$fields      = get_post_meta( $view_id, '_gravityview_directory_fields', true );
		$widgets     = get_post_meta( $view_id, '_gravityview_directory_widgets', true );
		$settings    = get_post_meta( $view_id, '_gravityview_template_settings', true );
		$fields      = is_array( $fields ) ? $fields : [];
		$widgets     = is_array( $widgets ) ? $widgets : [];
		$settings    = is_array( $settings ) ? $settings : [];
		$field_info  = gv_abilities_views_scan_field_info( $fields, $forms[ $form_id ] ?? [] );
		$health      = gv_abilities_views_scan_health( $form_id, $template_id, $templates, $field_info );
		$summary     = gv_abilities_addon_settings_summary( $view_id, $template_id, $fields, $widgets );

		if ( isset( $input['field_type'] ) && '' !== (string) $input['field_type'] && ! in_array( (string) $input['field_type'], $field_info['types'], true ) ) {
			return false;
		}
		if ( isset( $input['field_id'] ) && '' !== (string) $input['field_id'] && ! in_array( (string) $input['field_id'], $field_info['ids'], true ) ) {
			return false;
		}
		if ( isset( $input['field_label'] ) && '' !== (string) $input['field_label'] && ! gv_abilities_case_contains_any( $field_info['labels'], (string) $input['field_label'] ) ) {
			return false;
		}
		if ( array_key_exists( 'field_missing', $input ) && ! empty( $field_info['missing'] ) !== (bool) $input['field_missing'] ) {
			return false;
		}
		if ( isset( $input['widget_type'] ) && '' !== (string) $input['widget_type'] && ! in_array( (string) $input['widget_type'], gv_abilities_views_scan_widget_types( $widgets ), true ) ) {
			return false;
		}
		if ( isset( $input['addon_settings'] ) && '' !== (string) $input['addon_settings'] ) {
			$key = 'multiple-forms' === $input['addon_settings'] ? 'multiple_forms' : (string) $input['addon_settings'];
			if ( empty( $summary[ $key ] ) ) {
				return false;
			}
		}
		if ( array_key_exists( 'template_available', $input ) && array_key_exists( $template_id, $templates ) !== (bool) $input['template_available'] ) {
			return false;
		}
		if ( isset( $input['health'] ) && '' !== (string) $input['health'] && (string) $input['health'] !== $health ) {
			return false;
		}
		if ( isset( $input['config_search'] ) && '' !== (string) $input['config_search'] ) {
			$json = strtolower( (string) wp_json_encode( [ $fields, $widgets, $settings ] ) );
			if ( false === strpos( $json, strtolower( (string) $input['config_search'] ) ) ) {
				return false;
			}
		}

		return true;
	}
}

if ( ! function_exists( 'gv_abilities_views_scan_row' ) ) {
	/**
	 * Build one views-scan row.
	 *
	 * @param \WP_Post $post      View post.
	 * @param string[] $include_keys   Include keys.
	 * @param array    $templates Registered templates.
	 * @param array    $forms     Forms keyed by id.
	 * @return array<string,mixed>
	 */
	function gv_abilities_views_scan_row( $post, array $include_keys, array $templates, array $forms ): array {
		$row         = gv_abilities_view_summary_row( $post, $include_keys, $templates, $forms );
		$view_id     = (int) $post->ID;
		$form_id     = (int) $row['form_id'];
		$template_id = (string) $row['template_id'];
		$fields      = get_post_meta( $view_id, '_gravityview_directory_fields', true );
		$widgets     = get_post_meta( $view_id, '_gravityview_directory_widgets', true );
		$fields      = is_array( $fields ) ? $fields : [];
		$widgets     = is_array( $widgets ) ? $widgets : [];
		$field_info  = gv_abilities_views_scan_field_info( $fields, $forms[ $form_id ] ?? [] );

		$row['template_available'] = array_key_exists( $template_id, $templates );
		$row['health']             = gv_abilities_views_scan_health( $form_id, $template_id, $templates, $field_info );

		if ( in_array( 'field_types', $include_keys, true ) ) {
			$row['field_types'] = $field_info['types'];
		}
		if ( in_array( 'field_ids', $include_keys, true ) ) {
			$row['field_ids'] = $field_info['ids'];
		}
		if ( in_array( 'missing_field_refs', $include_keys, true ) ) {
			$row['missing_field_refs'] = $field_info['missing'];
		}
		if ( in_array( 'search_summary', $include_keys, true ) ) {
			$row['search_summary'] = [
				'search_field_count' => gv_abilities_count_search_fields( $widgets ),
			];
		}

		return $row;
	}
}

if ( ! function_exists( 'gv_abilities_views_scan_field_info' ) ) {
	/**
	 * Extract field ids, labels, types, and missing refs.
	 *
	 * @param array $fields Field tree.
	 * @param array $form   Gravity Forms form.
	 * @return array<string,array>
	 */
	function gv_abilities_views_scan_field_info( array $fields, array $form ): array {
		$form_fields = [];
		foreach ( (array) ( $form['fields'] ?? [] ) as $field ) {
			if ( $field instanceof \GF_Field ) {
				$id                 = (string) $field->id;
				$form_fields[ $id ] = [
					'type'  => (string) $field->type,
					'label' => (string) $field->label,
				];
				continue;
			}
			if ( ! is_array( $field ) ) {
				continue;
			}
			$id                 = (string) ( $field['id'] ?? '' );
			$form_fields[ $id ] = [
				'type'  => (string) ( $field['type'] ?? '' ),
				'label' => (string) ( $field['label'] ?? '' ),
			];
		}

		$ids     = [];
		$types   = [];
		$labels  = [];
		$missing = [];

		foreach ( $fields as $area => $slots ) {
			if ( ! is_array( $slots ) ) {
				continue;
			}
			foreach ( $slots as $slot => $record ) {
				if ( ! is_array( $record ) ) {
					continue;
				}
				$field_id = (string) ( $record['field_id'] ?? $record['id'] ?? '' );
				if ( '' === $field_id ) {
					continue;
				}

				$ids[] = $field_id;
				if ( isset( $form_fields[ $field_id ] ) ) {
					$types[]  = $form_fields[ $field_id ]['type'];
					$labels[] = $form_fields[ $field_id ]['label'];
				} else {
					$labels[] = (string) ( $record['label'] ?? '' );
					if ( is_numeric( $field_id ) ) {
						$missing[] = [
							'area'     => (string) $area,
							'slot'     => (string) $slot,
							'field_id' => $field_id,
						];
					}
				}
			}
		}

		return [
			'ids'     => array_values( array_unique( array_filter( $ids ) ) ),
			'types'   => array_values( array_unique( array_filter( $types ) ) ),
			'labels'  => array_values( array_filter( $labels ) ),
			'missing' => $missing,
		];
	}
}

if ( ! function_exists( 'gv_abilities_views_scan_widget_types' ) ) {
	/**
	 * Extract widget ids from a widget tree.
	 *
	 * @param array $widgets Widget tree.
	 * @return string[]
	 */
	function gv_abilities_views_scan_widget_types( array $widgets ): array {
		$types = [];
		foreach ( $widgets as $area ) {
			if ( ! is_array( $area ) ) {
				continue;
			}
			foreach ( $area as $widget ) {
				if ( is_array( $widget ) && ! empty( $widget['id'] ) ) {
					$types[] = (string) $widget['id'];
				}
			}
		}
		return array_values( array_unique( $types ) );
	}
}

if ( ! function_exists( 'gv_abilities_views_scan_health' ) ) {
	/**
	 * Compute a single health value.
	 *
	 * @param int    $form_id     Form id.
	 * @param string $template_id Template id.
	 * @param array  $templates   Registered templates.
	 * @param array  $field_info  Field info.
	 * @return string
	 */
	function gv_abilities_views_scan_health( int $form_id, string $template_id, array $templates, array $field_info ): string {
		if ( ! array_key_exists( $template_id, $templates ) ) {
			return 'template_missing';
		}
		if ( $form_id > 0 && class_exists( 'GFAPI' ) && ! \GFAPI::get_form( $form_id ) ) {
			return 'form_missing';
		}
		if ( ! empty( $field_info['missing'] ) ) {
			return 'field_missing';
		}
		return 'ok';
	}
}

if ( ! function_exists( 'gv_abilities_case_contains_any' ) ) {
	/**
	 * Case-insensitive substring match against a list.
	 *
	 * @param string[] $values Values.
	 * @param string   $needle Needle.
	 * @return bool
	 */
	function gv_abilities_case_contains_any( array $values, string $needle ): bool {
		$needle = strtolower( $needle );
		foreach ( $values as $value ) {
			if ( false !== strpos( strtolower( (string) $value ), $needle ) ) {
				return true;
			}
		}
		return false;
	}
}

if ( ! function_exists( 'gv_abilities_views_scan_encode_cursor' ) ) {
	/**
	 * Encode a scan cursor.
	 *
	 * @param int $offset Offset.
	 * @return string
	 */
	function gv_abilities_views_scan_encode_cursor( int $offset ): string {
		// Opaque pagination cursor — not obfuscation.
		return base64_encode( (string) max( 0, $offset ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}
}

if ( ! function_exists( 'gv_abilities_views_scan_decode_cursor' ) ) {
	/**
	 * Decode a scan cursor.
	 *
	 * @param string $cursor Cursor.
	 * @return int
	 */
	function gv_abilities_views_scan_decode_cursor( string $cursor ): int {
		if ( '' === $cursor ) {
			return 0;
		}

		$decoded = base64_decode( $cursor, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		return false === $decoded ? 0 : max( 0, (int) $decoded );
	}
}
