<?php
/**
 * Ability: gk-gravityview/layouts-list
 *
 * Migrates the legacy `GET /wp-json/gravityview/v1/layouts` route to
 * the WordPress Abilities API. Lists every installed GravityView
 * layout engine (Layout Builder, Default Table, List, DataTables,
 * Map, …) with id / label / description / logo / has_grid.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/layouts-list/run`
 * HTTP method:   GET (readonly)
 *
 * Excludes:
 *   - legacy `preset_*` content presets (no longer first-class)
 *   - `*_placeholder` ids registered by deactivated add-ons
 *   - `default_table_edit` (auto-selected by Edit Entry, not a user choice)
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
				'name'                => 'gk-gravityview/layouts-list',
				'label'               => __( 'List Layouts', 'gk-gravityview' ),
				'description'         => __( 'List the installed GravityView layout engines (Layout Builder, DIY, Table, List, DataTables, Map, …) with id / label / description / logo / has_grid. Use to discover valid template_id values before creating or switching a View. `has_grid: true` means the layout drives placement via grid rows; otherwise the layout exposes static areas.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-discovery',
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'layouts' => [
							'type'  => 'array',
							'items' => [
								'type'       => 'object',
								'properties' => [
									'id'          => [
										'type'        => 'string',
										'description' => __( 'Template id (use as template_id when creating/switching a View).', 'gk-gravityview' ),
									],
									'label'       => [ 'type' => 'string' ],
									'description' => [ 'type' => 'string' ],
									'logo'        => [ 'type' => 'string' ],
									'has_grid'    => [
										'type'        => 'boolean',
										'description' => __( 'When true, this layout drives placement via Layout Builder grid rows. Discovery flow: enumerate valid row types via gk-gravityview/grid-row-types-list, then create one via gk-gravityview/grid-row-add before adding fields. When false, the layout exposes a fixed set of named static areas — list them via gk-gravityview/view-areas-get after creating the View.', 'gk-gravityview' ),
									],
								],
								'required'   => [ 'id', 'label', 'has_grid' ],
							],
						],
					],
					'required'   => [ 'layouts' ],
				],
				'execute_callback'    => static function () {
					// Legacy template-registration hook used by every GravityView
					// layout to declare itself. Documented in the layout-registration
					// reference; reproduced here for the layouts-list ability surface.
					$templates = (array) apply_filters( 'gravityview_register_directory_template', [] );

					/**
					 * Filters the list of templates considered "grid-aware" — those
					 * whose layouts use grid rows + areas rather than static area
					 * placement. Used by has_grid in the response shape.
					 *
					 * @since 3.0.0
					 *
					 * @param array<int,string> $grid_templates List of template ids.
					 *                                          Defaults to ['gravityview-layout-builder'].
					 */
					$grid_templates = (array) apply_filters( 'gk/gravityview/rest/layouts/list/grid-aware-templates', [ 'gravityview-layout-builder' ] );
					$out            = [];
					foreach ( $templates as $template_id => $settings ) {
						if ( ! is_array( $settings ) ) {
							continue;
						}
						$tid = (string) $template_id;

						if ( 0 === strpos( $tid, 'preset_' ) ) {
							continue;
						}
						if ( false !== strpos( $tid, '_placeholder' ) ) {
							continue;
						}
						if ( 'default_table_edit' === $tid ) {
							continue;
						}

						$out[] = [
							'id'          => $tid,
							'label'       => (string) ( $settings['label'] ?? $tid ),
							'description' => (string) ( $settings['description'] ?? '' ),
							'logo'        => (string) ( $settings['logo'] ?? '' ),
							'has_grid'    => in_array( $tid, $grid_templates, true ),
						];
					}

					return [ 'layouts' => $out ];
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/layouts-list' ),
					],
				],
            ]
		);
	}
);
