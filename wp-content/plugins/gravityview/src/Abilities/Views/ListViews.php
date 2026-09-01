<?php
/**
 * Ability: gk-gravityview/views-list
 *
 * Enumerates GravityView posts using query-friendly filters only. Expensive
 * config-inspection filters live in gk-gravityview/views-scan.
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
				'name'                => 'gk-gravityview/views-list',
				'label'               => __( 'List Views', 'gk-gravityview' ),
				'description'         => __( 'Enumerate existing GravityView Views with inexpensive filters. Use views-scan for config-inspection filters such as field type, widget type, health, or add-on settings.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'status'               => \GravityKit\GravityView\Abilities\Bootstrap::status_filter_schema(),
						'form_id'              => [
							'type'        => 'integer',
							'description' => __( 'Filter to Views connected to this Gravity Forms form id.', 'gk-gravityview' ),
						],
						'match_joined'         => [
							'type'        => 'boolean',
							'description' => __( 'When true and form_id is set, add-ons may also match joined forms via the list query filter.', 'gk-gravityview' ),
						],
						'template'             => [
							'type'        => 'string',
							'description' => __( 'Filter by directory layout/template id, e.g. default_table, gravityview-layout-builder, or map_default.', 'gk-gravityview' ),
						],
						'author'               => [
							'type'        => 'integer',
							'description' => __( 'Filter by View post author id.', 'gk-gravityview' ),
						],
						'date_after'           => [ 'type' => 'string' ],
						'date_before'          => [ 'type' => 'string' ],
						'modified_after'       => [ 'type' => 'string' ],
						'modified_before'      => [ 'type' => 'string' ],
						'form_modified_after'  => [ 'type' => 'string' ],
						'form_modified_before' => [ 'type' => 'string' ],
						'form_active'          => [ 'type' => 'boolean' ],
						'form_has_entries'     => [ 'type' => 'boolean' ],
						'ids'                  => [
							'type'  => 'array',
							'items' => [ 'type' => 'integer' ],
						],
						'exclude_ids'          => [
							'type'  => 'array',
							'items' => [ 'type' => 'integer' ],
						],
						'search'               => [
							'type'        => 'string',
							'description' => __( 'Search post title/content according to search_in.', 'gk-gravityview' ),
						],
						'search_in'            => [
							'type'        => 'string',
							'enum'        => [ 'title', 'content', 'both' ],
							'description' => __( 'Search target. Default both.', 'gk-gravityview' ),
						],
						'include'              => [
							'type'  => 'array',
							'items' => [
								'type' => 'string',
								'enum' => [ 'field_count', 'widget_count', 'search_field_count', 'entry_count_estimate', 'description', 'addon_settings_summary', 'form_title', 'form_modified', 'form_active' ],
							],
						],
						'per_page'             => [
							'type'        => 'integer',
							'minimum'     => 1,
							'maximum'     => 100,
							'description' => __( 'Page size, 1-100. Default 20.', 'gk-gravityview' ),
						],
						'page'                 => [
							'type'        => 'integer',
							'minimum'     => 1,
							'description' => __( '1-based page number. Default 1.', 'gk-gravityview' ),
						],
						'orderby'              => [
							'type'        => 'string',
							'enum'        => [ 'modified', 'date', 'title', 'id', 'author' ],
							'description' => __( 'Sort key. Default modified.', 'gk-gravityview' ),
						],
						'order'                => [
							'type'        => 'string',
							'enum'        => [ 'asc', 'desc' ],
							'description' => __( 'Sort direction. Default desc.', 'gk-gravityview' ),
						],
					],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'views'       => [ 'type' => 'array' ],
						'total'       => [ 'type' => 'integer' ],
						'total_pages' => [ 'type' => 'integer' ],
						'per_page'    => [ 'type' => 'integer' ],
						'page'        => [ 'type' => 'integer' ],
					],
					'required'   => [ 'views', 'total', 'total_pages', 'per_page', 'page' ],
				],
				'execute_callback'    => static function ( $input ) {
					$input        = is_array( $input ) ? $input : [];
					$per_page     = isset( $input['per_page'] ) ? max( 1, min( 100, (int) $input['per_page'] ) ) : 20;
					$page         = isset( $input['page'] ) ? max( 1, (int) $input['page'] ) : 1;
					$orderby      = in_array( $input['orderby'] ?? '', [ 'modified', 'date', 'title', 'id', 'author' ], true ) ? $input['orderby'] : 'modified';
					$order        = ( strtoupper( (string) ( $input['order'] ?? '' ) ) === 'ASC' ) ? 'ASC' : 'DESC';
					$include_keys = \GravityKit\GravityView\Foundation\Abilities\Support\Projection::normalize_include(
						$input['include'] ?? [],
						[ 'field_count', 'widget_count', 'search_field_count', 'entry_count_estimate', 'description', 'addon_settings_summary', 'form_title', 'form_modified', 'form_active' ]
					);

					$args = [
						'post_type'           => 'gravityview',
						'post_status'         => gv_abilities_list_views_statuses( $input['status'] ?? null ),
						'posts_per_page'      => $per_page,
						'paged'               => $page,
						'orderby'             => 'id' === $orderby ? 'ID' : $orderby,
						'order'               => $order,
						'no_found_rows'       => false,
						'ignore_sticky_posts' => true,
						'perm'                => 'editable',
					];

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

					$form_filtered_ids = gv_abilities_list_views_form_ids_for_filters( $input );
					if ( is_array( $form_filtered_ids ) ) {
						if ( empty( $form_filtered_ids ) ) {
							$args['post__in'] = [ 0 ];
						} else {
							$meta_query[] = [
								'key'     => '_gravityview_form_id',
								'value'   => array_values( array_map( 'intval', $form_filtered_ids ) ),
								'compare' => 'IN',
							];
						}
					}

					if ( ! empty( $meta_query ) ) {
						$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					}

					if ( isset( $input['author'] ) && (int) $input['author'] > 0 ) {
						$args['author'] = (int) $input['author'];
					}

					$date_query = gv_abilities_list_views_date_query( $input );
					if ( ! empty( $date_query ) ) {
						$args['date_query'] = $date_query;
					}

					if ( ! empty( $input['ids'] ) && is_array( $input['ids'] ) ) {
						$ids = array_values( array_filter( array_map( 'intval', $input['ids'] ) ) );
						if ( isset( $args['post__in'] ) ) {
							$ids = array_values( array_intersect( $args['post__in'], $ids ) );
						}
						$args['post__in'] = empty( $ids ) ? [ 0 ] : $ids;
					}

					if ( ! empty( $input['exclude_ids'] ) && is_array( $input['exclude_ids'] ) ) {
						$args['post__not_in'] = array_values( array_filter( array_map( 'intval', $input['exclude_ids'] ) ) );
					}

					$args = (array) apply_filters( 'gk/gravityview/rest/views/list/query-args', $args, $input );

					if ( isset( $input['search'] ) && '' !== (string) $input['search'] ) {
						$args['s'] = (string) $input['search'];
						$search_in = (string) ( $input['search_in'] ?? 'both' );
						if ( 'title' === $search_in ) {
							$args['search_columns'] = [ 'post_title' ];
						} elseif ( 'content' === $search_in ) {
							$args['search_columns'] = [ 'post_content' ];
						}
					}

					$query     = new \WP_Query( $args );
					$templates = gv_abilities_registered_templates();
					$form_meta = gv_abilities_forms_by_id();
					$views     = [];

					/** @var \WP_Post[] $candidates */
					$candidates = $query->posts;
					update_meta_cache( 'post', wp_list_pluck( $candidates, 'ID' ) );

					$view_perms = new \GravityKit\GravityView\Permissions\Permissions();
					foreach ( $candidates as $post ) {
						if ( ! $view_perms->can_edit_view( (int) $post->ID ) ) {
							continue;
						}

						$views[] = gv_abilities_view_summary_row( $post, $include_keys, $templates, $form_meta );
					}

					$total       = (int) $query->found_posts;
					$total_pages = (int) ceil( $total / $per_page );

					return [
						'views'       => $views,
						'total'       => $total,
						'total_pages' => max( 1, $total_pages ),
						'per_page'    => $per_page,
						'page'        => $page,
					];
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/views-list' ),
					],
				],
            ]
		);
	}
);

if ( ! function_exists( 'gv_abilities_list_views_statuses' ) ) {
	/**
	 * Normalize status input without passing literal `any` to WP_Query.
	 *
	 * @param mixed $raw Raw status input.
	 * @return string|string[]
	 */
	function gv_abilities_list_views_statuses( $raw ) {
		if ( null === $raw || '' === $raw ) {
			return [ 'publish', 'draft', 'pending', 'private' ];
		}

		if ( 'any' === $raw ) {
			$statuses = array_values(
				array_diff(
					array_unique(
						array_merge(
							[ 'publish' ],
							get_post_stati( [ 'show_in_admin_status_list' => true ], 'names' )
						)
					),
					[ 'trash', 'auto-draft' ]
				)
			);

			return empty( $statuses ) ? [ 'publish', 'draft', 'pending', 'private' ] : $statuses;
		}

		if ( is_array( $raw ) ) {
			$status = array_values( array_filter( array_map( 'sanitize_key', $raw ) ) );
			return empty( $status ) ? [ 'publish' ] : $status;
		}

		return sanitize_key( (string) $raw );
	}
}

if ( ! function_exists( 'gv_abilities_list_views_date_query' ) ) {
	/**
	 * Build WP_Query date_query clauses.
	 *
	 * @param array $input Ability input.
	 * @return array<int,array<string,mixed>>
	 */
	function gv_abilities_list_views_date_query( array $input ): array {
		$date_query = [];

		$pairs = [
			'date_after'      => [
				'column' => 'post_date',
				'after'  => true,
			],
			'date_before'     => [
				'column' => 'post_date',
				'after'  => false,
			],
			'modified_after'  => [
				'column' => 'post_modified',
				'after'  => true,
			],
			'modified_before' => [
				'column' => 'post_modified',
				'after'  => false,
			],
		];

		foreach ( $pairs as $key => $args ) {
			if ( empty( $input[ $key ] ) ) {
				continue;
			}

			$date_query[] = [
				'column'                            => $args['column'],
				$args['after'] ? 'after' : 'before' => (string) $input[ $key ],
				'inclusive'                         => true,
			];
		}

		return $date_query;
	}
}

if ( ! function_exists( 'gv_abilities_list_views_form_ids_for_filters' ) ) {
	/**
	 * Resolve Gravity Forms predicates to matching form ids.
	 *
	 * @param array $input Ability input.
	 * @return int[]|null Null when no form predicate was supplied.
	 */
	function gv_abilities_list_views_form_ids_for_filters( array $input ): ?array {
		$uses_filter = array_key_exists( 'form_modified_after', $input )
			|| array_key_exists( 'form_modified_before', $input )
			|| array_key_exists( 'form_active', $input )
			|| array_key_exists( 'form_has_entries', $input );

		if ( ! $uses_filter || ! class_exists( 'GFAPI' ) ) {
			return null;
		}

		$forms = array_merge( \GFAPI::get_forms( true ), \GFAPI::get_forms( false ) );
		$ids   = [];

		foreach ( $forms as $form ) {
			$form_id = (int) ( $form['id'] ?? 0 );
			if ( $form_id <= 0 ) {
				continue;
			}

			if ( array_key_exists( 'form_active', $input ) && ! empty( $form['is_active'] ) !== (bool) $input['form_active'] ) {
				continue;
			}

			$modified = (string) ( $form['date_updated'] ?? $form['date_created'] ?? '' );
			if ( ! empty( $input['form_modified_after'] ) && '' !== $modified && strtotime( $modified ) < strtotime( (string) $input['form_modified_after'] ) ) {
				continue;
			}
			if ( ! empty( $input['form_modified_before'] ) && '' !== $modified && strtotime( $modified ) > strtotime( (string) $input['form_modified_before'] ) ) {
				continue;
			}

			if ( array_key_exists( 'form_has_entries', $input ) ) {
				$has_entries = \GFAPI::count_entries( $form_id ) > 0;
				if ( (bool) $input['form_has_entries'] !== $has_entries ) {
					continue;
				}
			}

			$ids[] = $form_id;
		}

		return array_values( array_unique( $ids ) );
	}
}

if ( ! function_exists( 'gv_abilities_registered_templates' ) ) {
	/**
	 * Registered GravityView templates keyed by id.
	 *
	 * @return array<string,array>
	 */
	function gv_abilities_registered_templates(): array {
		static $templates = null;
		if ( null !== $templates ) {
			return $templates;
		}

		$templates = [];
		foreach ( (array) apply_filters( 'gravityview_register_directory_template', [] ) as $id => $settings ) {
			$templates[ (string) $id ] = is_array( $settings ) ? $settings : [];
		}

		return $templates;
	}
}

if ( ! function_exists( 'gv_abilities_forms_by_id' ) ) {
	/**
	 * Gravity Forms forms keyed by id.
	 *
	 * @return array<int,array>
	 */
	function gv_abilities_forms_by_id(): array {
		static $forms = null;
		if ( null !== $forms ) {
			return $forms;
		}

		$forms = [];
		if ( class_exists( 'GFAPI' ) ) {
			foreach ( array_merge( \GFAPI::get_forms( true ), \GFAPI::get_forms( false ) ) as $form ) {
				$forms[ (int) ( $form['id'] ?? 0 ) ] = $form;
			}
		}

		return $forms;
	}
}

if ( ! function_exists( 'gv_abilities_view_summary_row' ) ) {
	/**
	 * Build one views-list row.
	 *
	 * @param \WP_Post $post      View post.
	 * @param string[] $include_keys   Include keys.
	 * @param array    $templates Registered templates.
	 * @param array    $forms     Forms keyed by id.
	 * @return array<string,mixed>
	 */
	function gv_abilities_view_summary_row( $post, array $include_keys, array $templates, array $forms ): array {
		$view_id     = (int) $post->ID;
		$form_id     = (int) get_post_meta( $view_id, '_gravityview_form_id', true );
		$template_id = (string) get_post_meta( $view_id, '_gravityview_directory_template', true );
		$template_id = '' === $template_id ? 'default_list' : $template_id;
		$form        = $forms[ $form_id ] ?? [];

		$row = [
			'view_id'        => $view_id,
			'title'          => (string) $post->post_title,
			'status'         => (string) $post->post_status,
			'author'         => (int) $post->post_author,
			'form_id'        => $form_id,
			'template_id'    => $template_id,
			'template_label' => (string) ( $templates[ $template_id ]['label'] ?? $template_id ),
			'modified'       => (string) $post->post_modified,
			'created'        => (string) $post->post_date,
			'edit_url'       => admin_url( 'post.php?post=' . $view_id . '&action=edit' ),
			'permalink'      => (string) get_permalink( $view_id ),
		];

		if ( in_array( 'description', $include_keys, true ) ) {
			$row['description'] = (string) $post->post_content;
		}
		if ( in_array( 'form_title', $include_keys, true ) ) {
			$row['form_title'] = (string) ( $form['title'] ?? '' );
		}
		if ( in_array( 'form_modified', $include_keys, true ) ) {
			$row['form_modified'] = (string) ( $form['date_updated'] ?? $form['date_created'] ?? '' );
		}
		if ( in_array( 'form_active', $include_keys, true ) ) {
			$row['form_active'] = ! empty( $form['is_active'] );
		}
		if ( in_array( 'entry_count_estimate', $include_keys, true ) ) {
			$row['entry_count_estimate'] = class_exists( 'GFAPI' ) && $form_id > 0 ? (int) \GFAPI::count_entries( $form_id ) : 0;
		}

		if ( in_array( 'field_count', $include_keys, true ) || in_array( 'widget_count', $include_keys, true ) || in_array( 'search_field_count', $include_keys, true ) || in_array( 'addon_settings_summary', $include_keys, true ) ) {
			$fields  = get_post_meta( $view_id, '_gravityview_directory_fields', true );
			$widgets = get_post_meta( $view_id, '_gravityview_directory_widgets', true );
			$fields  = is_array( $fields ) ? $fields : [];
			$widgets = is_array( $widgets ) ? $widgets : [];

			if ( in_array( 'field_count', $include_keys, true ) ) {
				$row['field_count'] = gv_abilities_count_nested_slots( $fields );
			}
			if ( in_array( 'widget_count', $include_keys, true ) ) {
				$row['widget_count'] = gv_abilities_count_nested_slots( $widgets );
			}
			if ( in_array( 'search_field_count', $include_keys, true ) ) {
				$row['search_field_count'] = gv_abilities_count_search_fields( $widgets );
			}
			if ( in_array( 'addon_settings_summary', $include_keys, true ) ) {
				$row['addon_settings_summary'] = gv_abilities_addon_settings_summary( $view_id, $template_id, $fields, $widgets );
			}
		}

		return $row;
	}
}

if ( ! function_exists( 'gv_abilities_count_nested_slots' ) ) {
	/**
	 * Count slots in an area tree.
	 *
	 * @param array $tree Area tree.
	 * @return int
	 */
	function gv_abilities_count_nested_slots( array $tree ): int {
		$count = 0;
		foreach ( $tree as $slots ) {
			if ( is_array( $slots ) ) {
				$count += count( $slots );
			}
		}
		return $count;
	}
}

if ( ! function_exists( 'gv_abilities_count_search_fields' ) ) {
	/**
	 * Count nested search fields inside search bar widgets.
	 *
	 * @param array $widgets Widget tree.
	 * @return int
	 */
	function gv_abilities_count_search_fields( array $widgets ): int {
		$count = 0;
		foreach ( $widgets as $area ) {
			if ( ! is_array( $area ) ) {
				continue;
			}
			foreach ( $area as $widget ) {
				if ( ! is_array( $widget ) || 'search_bar' !== ( $widget['id'] ?? '' ) ) {
					continue;
				}
				foreach ( (array) ( $widget['search_fields_section'] ?? [] ) as $position ) {
					$count += is_array( $position ) ? count( $position ) : 0;
				}
			}
		}
		return $count;
	}
}

if ( ! function_exists( 'gv_abilities_addon_settings_summary' ) ) {
	/**
	 * Build a coarse add-on settings summary.
	 *
	 * @param int    $view_id     View id.
	 * @param string $template_id Template id.
	 * @param array  $fields      Field tree.
	 * @param array  $widgets     Widget tree.
	 * @return array<string,bool>
	 */
	function gv_abilities_addon_settings_summary( int $view_id, string $template_id, array $fields, array $widgets ): array {
		$settings = get_post_meta( $view_id, '_gravityview_template_settings', true );
		$settings = is_array( $settings ) ? $settings : [];
		$json     = strtolower( (string) wp_json_encode( [ $template_id, $settings, $fields, $widgets ] ) );

			return [
				'maps'           => false !== strpos( $json, 'map' ),
				'datatables'     => false !== strpos( $json, 'datatable' ),
				'multiple_forms' => false !== strpos( $json, 'join' ),
				'joins'          => false !== strpos( $json, 'join' ),
			];
	}
}
