<?php
/**
 * Ability: gk-gravityview/view-clone
 *
 * Clone an existing View into a new draft. Copies the form binding,
 * every per-zone template, every template / search-criteria / silo'd
 * setting bucket, the entire field tree, and the entire widget tree
 * (including search-bar internal `search_fields_section` shape) so
 * the duplicate is a working View on first save — same shape as the
 * original, fresh post id + version counter.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-clone/run`
 * HTTP method:   POST
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
				'name'                => 'gk-gravityview/view-clone',
				'label'               => __( 'Clone View', 'gk-gravityview' ),
				'description'         => __( 'Clone an existing View into a new draft. Copies form binding + every per-zone template + template/search/silo settings + entire field tree + entire widget tree (including modern search_fields_section). The duplicate has a fresh post id and version counter; status defaults to `draft`.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'Source View post id.', 'gk-gravityview' ),
						],
						'title'   => [
							'type'        => 'string',
							'description' => __( 'Optional title for the duplicate. Defaults to "{original} (copy)".', 'gk-gravityview' ),
						],
						'dry_run' => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
					],
					'required'             => [ 'id' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'     => [
							'type'        => 'integer',
							'description' => __( 'New View post id.', 'gk-gravityview' ),
						],
						'source_id'   => [
							'type'        => 'integer',
							'description' => __( 'Source View post id.', 'gk-gravityview' ),
						],
						'title'       => [ 'type' => 'string' ],
						'admin_url'   => [ 'type' => 'string' ],
						'cloned'      => [ 'type' => 'boolean' ],
						'dry_run'     => [ 'type' => 'boolean' ],
						'would_apply' => [ 'type' => 'boolean' ],
					],
					'required'   => [ 'view_id', 'source_id', 'cloned' ],
				],
				'execute_callback'    => static function ( $input ) {
					$source_id = (int) ( $input['id'] ?? 0 );
					$source    = $source_id > 0 ? get_post( $source_id ) : null;
					if ( ! $source || 'gravityview' !== get_post_type( $source ) ) {
						return new \WP_Error( 'gv_rest_view_not_found', __( 'Source View not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
					}

					$title = isset( $input['title'] ) && '' !== (string) $input['title']
						? (string) $input['title']
						: sprintf(
							/* translators: %s: original View title */
							__( '%s (copy)', 'gk-gravityview' ),
							$source->post_title
						);

					$is_dry = (bool) ( $input['dry_run'] ?? false );
					if ( $is_dry ) {
						return \GravityKit\GravityView\Foundation\Abilities\Support\DryRun::with_dry_run(
							true,
							static function () use ( $source_id, $title ) {
								return \GravityKit\GravityView\Foundation\Abilities\Support\DryRun::with_post_write_guard(
									static function () {
										return null;
									},
									[
										'view_id'   => 0,
										'source_id' => $source_id,
										'title'     => $title,
										'admin_url' => '',
										'cloned'    => false,
									]
								);
							}
						);
					}

					$new_id = wp_insert_post(
						[
							'post_type'    => 'gravityview',
							'post_status'  => 'draft',
							'post_title'   => $title,
							'post_content' => $source->post_content,
							'post_author'  => get_current_user_id(),
						],
						true
					);
					if ( is_wp_error( $new_id ) ) {
						return $new_id;
					}

					// Copy every gravityview_* + GravityView meta key so the
					// duplicate gets the form binding, all per-zone templates,
					// every template_settings silo (Maps, DataTables, etc.),
					// the field tree, and the widget tree. We list the
					// canonical keys explicitly — copying ALL post meta would
					// drag along Yoast, ACF, and other unrelated keys.
					$meta_keys = [
						'_gravityview_form_id',
						'_gravityview_directory_template',
						'_gravityview_single_template',
						'_gravityview_template_settings',
						'_gravityview_directory_fields',
						'_gravityview_directory_widgets',
						'_gravityview_datatables_settings',
						'_gravityview_maps_settings',
					];
					foreach ( $meta_keys as $key ) {
						$value = get_post_meta( $source_id, $key, true );
						if ( '' !== $value && null !== $value && [] !== $value ) {
							update_post_meta( $new_id, $key, $value );
						}
					}

					/**
					 * Fires after a View has been cloned. Use this to
					 * copy add-on-owned post meta the core cloner
					 * doesn't know about.
					 *
					 * @since 3.0.0
					 *
					 * @param int $new_id    New View post id.
					 * @param int $source_id Source View post id.
					 */
					do_action( 'gk/gravityview/rest/view/cloned', $new_id, $source_id );

					return [
						'view_id'   => (int) $new_id,
						'source_id' => $source_id,
						'title'     => $title,
						'admin_url' => admin_url( 'post.php?post=' . (int) $new_id . '&action=edit' ),
						'cloned'    => true,
					];
				},
				'permission_callback' => static function ( $input ) {
					$source_id = (int) ( $input['id'] ?? 0 );
					if ( $source_id <= 0 ) {
						return false;
					}
					// AND semantics (source-edit + target-create) centralized in
					// Permissions::can_duplicate_view. Return WP_Error
					// directly so the specific deny reason (read-source vs
					// create-target) surfaces in the response.
					return ( new \GravityKit\GravityView\Permissions\Permissions() )
						->can_duplicate_view( (int) $source_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-clone' ),
					],
				],
            ]
		);
	}
);
