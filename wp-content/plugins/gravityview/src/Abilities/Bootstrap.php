<?php
/**
 * Abilities API bootstrap for GravityView.
 *
 * Registers all GravityView abilities under the `gk-gravityview/`
 * namespace via the GravityKit Foundation Abilities service.
 *
 * Naming convention: `gk-gravityview/{object}-{verb}` —
 * `gk-gravityview/layouts-list`, `gk-gravityview/view-config-apply`,
 * `gk-gravityview/view-field-add`, etc.
 *
 * Categories (declared via the `gk/foundation/abilities/products` filter):
 *   - `gk-gravityview`               — product category (parent)
 *   - `gk-gravityview-discovery`     — readonly list/get endpoints
 *   - `gk-gravityview-views`         — view-level CRUD + bulk apply
 *   - `gk-gravityview-fields`        — field-slot CRUD + render
 *   - `gk-gravityview-widgets`       — widget-slot CRUD
 *   - `gk-gravityview-search-fields` — search field CRUD
 *   - `gk-gravityview-grid`          — Layout Builder row CRUD
 *   - `gk-gravityview-preview`       — preview-staging transient
 *
 * Each ability is a single file under `src/Abilities/{Category}/`.
 * Bootstrap loads them all on plugin init when the Abilities API is
 * available; degrades to no-op when WordPress doesn't ship it.
 *
 * Migration plan: every existing `register_rest_route()` in
 * `InspectorRoute.php` becomes a Foundation-registered ability here. The
 * REST surface auto-routes to `/wp-json/wp-abilities/v1/abilities/gk-gravityview/{name}/run`.
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

namespace GravityKit\GravityView\Abilities;

defined( 'ABSPATH' ) || exit;

class Bootstrap {

	/**
	 * Wire categories + ability files. Hooked to gk/foundation/initialized
	 * from src/Core/Core.php:177.
	 *
	 * No-ops when the Abilities API isn't available (e.g. older WP
	 * versions that pre-date the abilities-api landing in core) or when the
	 * active Foundation copy predates the Abilities component — Foundation's
	 * facade returns null for unknown components, so guard before calling.
	 */
	public static function init(): void {
		add_action( 'before_delete_post', [ self::class, 'before_delete_view' ], 10, 2 );

		$abilities    = \GravityKitFoundation::abilities();
		$is_supported = is_object( $abilities ) && is_callable( [ $abilities, 'is_supported' ] ) && $abilities->is_supported();

		if ( ! $is_supported ) {
			return;
		}

		add_filter( 'gk/foundation/abilities/products', [ self::class, 'register_product' ] );

		// Each ability file under `src/Abilities/{Category}/` is a
		// self-contained `add_action( 'gk/foundation/abilities/register/before', ... )`.
		// Load them NOW (at plugins_loaded time) so each file's
		// `add_action()` runs BEFORE Foundation's registration action fires —
		// requiring them from within the action callback would be
		// too late: the callback would be added during the hook's
		// own execution and never called.
		self::load_ability_files();
	}

	/**
	 * Fire a GravityView-specific cleanup hook before force-deleting a View.
	 *
	 * @since 3.0.0
	 *
	 * @param int      $post_id Post id.
	 * @param \WP_Post $post    Post object.
	 */
	public static function before_delete_view( int $post_id, $post ): void {
		if ( ! $post instanceof \WP_Post || 'gravityview' !== $post->post_type ) {
			return;
		}

		do_action( 'gk/gravityview/view/before-delete', $post_id );
	}

	/**
	 * Register the GravityView product with Foundation: the required MCP
	 * tool-name prefix plus the `gk-gravityview` product category and every
	 * scope subcategory. Foundation registers the declared categories before
	 * ability registration, so they exist before any ability references them.
	 *
	 * @param array $products Product declarations keyed by product slug.
	 * @return array
	 */
	public static function register_product( array $products ): array {
		$categories = [
			'gk-gravityview'               => [
				'label'       => __( 'GravityView', 'gk-gravityview' ),
				'description' => __( 'Turn Gravity Forms entries into front-end applications — searchable directories, tables, and databases with entry display, editing, deletion, and approval workflows.', 'gk-gravityview' ),
			],
			'gk-gravityview-discovery'     => [
				'label'       => __( 'GravityView Discovery', 'gk-gravityview' ),
				'description' => __( 'Readonly endpoints that enumerate layouts, widgets, field types, search zones, and registered template settings. AI agents use these to discover what is available before authoring or modifying a View.', 'gk-gravityview' ),
			],
			'gk-gravityview-views'         => [
				'label'       => __( 'GravityView Views', 'gk-gravityview' ),
				'description' => __( 'View-level reads and writes — get / create / apply / patch the full View configuration tree.', 'gk-gravityview' ),
			],
			'gk-gravityview-fields'        => [
				'label'       => __( 'GravityView Fields', 'gk-gravityview' ),
				'description' => __( 'Per-field-slot CRUD inside a View — add, patch, move, remove, render.', 'gk-gravityview' ),
			],
			'gk-gravityview-widgets'       => [
				'label'       => __( 'GravityView Widgets', 'gk-gravityview' ),
				'description' => __( 'Per-widget-slot CRUD inside a View — add, patch, remove.', 'gk-gravityview' ),
			],
			'gk-gravityview-search-fields' => [
				'label'       => __( 'GravityView Search Fields', 'gk-gravityview' ),
				'description' => __( 'Search Bar tools. Use search-bar-add for the common case (create a search bar and populate it with fields in one call); the low-level search-field add/patch/move/remove tools operate on individual slots inside an existing search_bar widget.', 'gk-gravityview' ),
			],
			'gk-gravityview-grid'          => [
				'label'       => __( 'GravityView Grid', 'gk-gravityview' ),
				'description' => __( 'Layout Builder row CRUD — create, patch, delete grid rows in field/widget zones.', 'gk-gravityview' ),
			],
			'gk-gravityview-preview'       => [
				'label'       => __( 'GravityView Preview', 'gk-gravityview' ),
				'description' => __( 'Design Studio preview surface — covers transient stage staging for the preview iframe AND subtree re-render endpoints called by preview clients (e.g. live re-paints when a setting changes).', 'gk-gravityview' ),
			],
		];

		$products['gravityview'] = [
			'mcp_prefix' => 'gv',
			'categories' => $categories,
		];

		return $products;
	}

	/**
	 * Catalog of registered field presets, keyed by preset id.
	 *
	 * Empty by default. Add-ons populate via the
	 * `gk/gravityview/rest/field-presets/list` filter, returning an
	 * array of preset definitions:
	 *
	 *   [
	 *     'standard-directory-header' => [
	 *       'id'          => 'standard-directory-header',
	 *       'label'       => 'Standard Directory Header',
	 *       'description' => 'Search bar + page links — one-line setup.',
	 *       'applies_to'  => [ 'zones' => [ 'directory' ], 'areas' => [], 'template_ids' => [] ],
	 *       'fields'      => [
	 *         [ 'field_id' => 'search_bar', ... ],
	 *         [ 'field_id' => 'page_links', ... ],
	 *       ],
	 *     ],
	 *   ]
	 *
	 * Result is normalised: every preset gains an `id` (the array key)
	 * if missing, an empty `applies_to` if absent, and an empty `fields`
	 * if absent.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array{id:string,label:string,description?:string,applies_to:array,fields:array}>
	 */
	public static function field_presets(): array {
		/**
		 * Filter the registered field presets catalog.
		 *
		 * @since 3.0.0
		 *
		 * @param array<string,array> $presets Catalog keyed by preset id.
		 */
		$presets = apply_filters( 'gk/gravityview/rest/field-presets/list', [] );

		if ( ! is_array( $presets ) ) {
			return [];
		}

		$out = [];
		foreach ( $presets as $key => $preset ) {
			if ( ! is_array( $preset ) ) {
				continue;
			}
			$id = isset( $preset['id'] ) ? (string) $preset['id'] : (string) $key;
			if ( '' === $id ) {
				continue;
			}
			$out[ $id ] = [
				'id'          => $id,
				'label'       => isset( $preset['label'] ) ? (string) $preset['label'] : $id,
				'description' => isset( $preset['description'] ) ? (string) $preset['description'] : '',
				'applies_to'  => isset( $preset['applies_to'] ) && is_array( $preset['applies_to'] ) ? $preset['applies_to'] : [],
				'fields'      => isset( $preset['fields'] ) && is_array( $preset['fields'] ) ? $preset['fields'] : [],
			];
		}
		return $out;
	}

	/**
	 * Validate a preset can be applied to a given View + area. Returns
	 * `null` when compatible, a `WP_Error` 422 when not.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $preset  Normalised preset definition (from `field_presets()`).
	 * @param int    $view_id Target View id.
	 * @param string $area   Target area key.
	 *
	 * @return \WP_Error|null
	 */
	public static function preset_compatibility_error( array $preset, int $view_id, string $area ) {
		$applies = $preset['applies_to'] ?? [];

		// Area constraint.
		$areas = $applies['areas'] ?? [];
		if ( is_array( $areas ) && ! empty( $areas ) && ! in_array( $area, $areas, true ) ) {
			return new \WP_Error(
				'gv_rest_preset_area_mismatch',
				sprintf(
					/* translators: 1: area key, 2: preset id, 3: comma-separated valid areas */
					__( 'Area "%1$s" is not in preset "%2$s"\'s allowed list (%3$s).', 'gk-gravityview' ),
					$area,
					$preset['id'] ?? '?',
					implode( ', ', $areas )
				),
				[ 'status' => 422 ]
			);
		}

		// Zone constraint (`directory` / `single` / `edit`). Inferred
		// from the area key prefix.
		$zones = $applies['zones'] ?? [];
		if ( is_array( $zones ) && ! empty( $zones ) ) {
			$prefix = strtok( $area, '_' );
			if ( false === $prefix || ! in_array( $prefix, $zones, true ) ) {
				return new \WP_Error(
					'gv_rest_preset_zone_mismatch',
					sprintf(
						/* translators: 1: zone derived from area, 2: preset id, 3: comma-separated valid zones */
						__( 'Area "%1$s" resolves to zone "%2$s", not in preset "%3$s"\'s allowed zones (%4$s).', 'gk-gravityview' ),
						$area,
						(string) $prefix,
						$preset['id'] ?? '?',
						implode( ', ', $zones )
					),
					[ 'status' => 422 ]
				);
			}
		}

		// Template-id constraint — checked against the View's directory
		// template (the most common "is this layout compatible" case).
		$templates = $applies['template_ids'] ?? [];
		if ( is_array( $templates ) && ! empty( $templates ) ) {
			$current = (string) get_post_meta( $view_id, '_gravityview_directory_template', true );
			if ( '' !== $current && ! in_array( $current, $templates, true ) ) {
				return new \WP_Error(
					'gv_rest_preset_template_mismatch',
					sprintf(
						/* translators: 1: View's current directory template id, 2: preset id, 3: comma-separated valid templates */
						__( 'View\'s directory template "%1$s" is not in preset "%2$s"\'s allowed templates (%3$s).', 'gk-gravityview' ),
						$current,
						$preset['id'] ?? '?',
						implode( ', ', $templates )
					),
					[ 'status' => 422 ]
				);
			}
		}

		return null;
	}

	/**
	 * Auto-discover and load every ability registration file. Each
	 * file lives under `src/Abilities/{Category}/` and is a single
	 * `add_action( 'gk/foundation/abilities/register/before', ... )` call
	 * that registers exactly one ability via Foundation.
	 *
	 * Files are auto-discovered via glob — no per-file include lines
	 * to maintain. Add a new ability by dropping a file in the right
	 * subdirectory.
	 */
	private static function load_ability_files(): void {
		$files = glob( __DIR__ . '/*/*.php' );
		if ( ! is_array( $files ) ) {
			return;
		}
		foreach ( $files as $file ) {
			require_once $file;
		}
	}

	/**
	 * Centralised map of suggested follow-up abilities for each ability.
	 *
	 * Surfaced verbatim via `meta.annotations.next_steps` so AI agents
	 * (and the inspector UI) can chain calls without out-of-band docs.
	 * Each entry is `[ 'ability' => '<fqn>', 'when' => '<one-sentence trigger>' ]`,
	 * with the `when` string translated through `__()` so localised
	 * builds get translated chain-of-thought guidance too.
	 *
	 * Memoised per request: the underlying `__()` calls only fire on
	 * the first lookup; subsequent calls return the cached map.
	 *
	 * Add a new ability and you almost always want a corresponding
	 * entry here — even `[]` is fine ("leaf call"), but a missing key
	 * silently means "no guidance".
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array<int, array{ability:string,when:string}>>
	 */
	public static function next_steps_map(): array {
		static $map = null;
		if ( null !== $map ) {
			return $map;
		}

		$map = [
			// --- Discovery ---
			'gk-gravityview/layouts-list'                 => [
				[
					'ability' => 'gk-gravityview/view-create',
					'when'    => __( 'After a layout id is chosen, to create a draft View bound to it.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/grid-row-types-list',
					'when'    => __( 'When has_grid is true on the chosen layout, to enumerate row types before adding fields.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-areas-get',
					'when'    => __( 'When has_grid is false, to list the layout\'s static areas before adding fields.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/grid-row-types-list'          => [
				[
					'ability' => 'gk-gravityview/grid-row-add',
					'when'    => __( 'After a row type is chosen, to materialise it on the View.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/widget-zones-list'            => [
				[
					'ability' => 'gk-gravityview/widgets-list',
					'when'    => __( 'After a zone is known, to find a widget for it.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-widget-add',
					'when'    => __( 'When the widget id is already known, to attach it.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/widgets-list'                 => [
				[
					'ability' => 'gk-gravityview/view-widget-add',
					'when'    => __( 'To place the widget into one of the zones from list-widget-zones.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/search-zones-list'            => [
				[
					'ability' => 'gk-gravityview/search-bar-add',
					'when'    => __( 'Preferred: add a search bar with its fields in one call.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/search-input-types-list',
					'when'    => __( 'To inspect the input shapes valid for the search-bar widget.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/search-field-add',
					'when'    => __( 'Advanced: when you already have a target widget_area + widget_slot, to add one search field surgically.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/search-input-types-list'      => [
				[
					'ability' => 'gk-gravityview/search-bar-add',
					'when'    => __( 'Preferred: add a search bar and set each field\'s input in one call.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/search-field-add',
					'when'    => __( 'Advanced: when adding a single search field with a returned input_type.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/available-fields-get'         => [
				[
					'ability' => 'gk-gravityview/view-field-add',
					'when'    => __( 'To place the chosen field_id into one of the View\'s areas.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/forms-list'                   => [
				[
					'ability' => 'gk-gravityview/view-create',
					'when'    => __( 'To bind a new View to the chosen Gravity Forms form.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/merge-tag-data-get',
					'when'    => __( 'After a form is chosen, for its merge-tag catalogue when templating Custom Content, notifications, or search settings.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/field-type-schema-get'        => [
				[
					'ability' => 'gk-gravityview/view-field-patch',
					'when'    => __( 'Once the settings the field type accepts are known, to set them.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/template-settings-schema-get' => [
				[
					'ability' => 'gk-gravityview/view-settings-patch',
					'when'    => __( 'Once the template settings to write are known, to apply them.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/field-presets-list'           => [
				[
					'ability' => 'gk-gravityview/field-preset-apply',
					'when'    => __( 'After a preset id is chosen, to materialise it on a View.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/field-preset-apply'           => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the slots the preset created.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-field-patch',
					'when'    => __( 'To tweak per-slot settings on the freshly-created fields.', 'gk-gravityview' ),
				],
			],

			// --- Views ---
			'gk-gravityview/views-list'                   => [
				[
					'ability' => 'gk-gravityview/views-scan',
					'when'    => __( 'When expensive config-inspection filters are needed, such as field type, widget type, add-on settings, or health.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To inspect a returned View\'s full config.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-clone',
					'when'    => __( 'To clone a returned View as a fresh draft.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-status-set',
					'when'    => __( 'To change a returned View\'s status (publish / draft / trash).', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-delete',
					'when'    => __( 'To soft-delete (default) or permanently remove a returned View.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/views-scan'                   => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To inspect a returned View\'s full config after the scan narrowed candidates.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-settings-patch',
					'when'    => __( 'To patch template settings on a View returned by the scan.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-field-patch',
					'when'    => __( 'To patch a field slot identified by the scan.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-config-get'              => [
				[
					'ability' => 'gk-gravityview/view-config-apply',
					'when'    => __( 'To bulk-write multiple changes in one transactional call.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-field-patch',
					'when'    => __( 'To edit one field slot\'s settings.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-widget-patch',
					'when'    => __( 'To edit one widget slot\'s settings.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-settings-patch',
					'when'    => __( 'To update template-level settings only.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-create'                  => [
				[
					'ability' => 'gk-gravityview/view-areas-get',
					'when'    => __( 'To discover the areas of the layout just bound.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/grid-row-types-list',
					'when'    => __( 'When a Layout Builder template was bound, to enumerate row types first.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-config-apply',
					'when'    => __( 'To bulk-write the initial fields/widgets/settings tree in one call.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-clone'                   => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To inspect the freshly-duplicated draft.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-settings-patch',
					'when'    => __( 'To tweak settings on the duplicate before publishing.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-areas-get'               => [
				[
					'ability' => 'gk-gravityview/view-field-add',
					'when'    => __( 'To place a field into one of the listed areas.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-field-schemas-get'       => [
				[
					'ability' => 'gk-gravityview/view-field-patch',
					'when'    => __( 'After the schema is inspected, to write valid settings.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-config-apply'            => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To verify the persisted state after a bulk write.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-template-switch'         => [
				[
					'ability' => 'gk-gravityview/view-areas-get',
					'when'    => __( 'When a new layout exposes a different set of areas, to re-discover them.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/grid-row-types-list',
					'when'    => __( 'When the new layout is grid-aware, to list row types before adding fields.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-settings-patch'          => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the merged result.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-status-set'              => [
				[
					'ability' => 'gk-gravityview/views-list',
					'when'    => __( 'To re-list and confirm the status change is reflected.', 'gk-gravityview' ),
				],
			],

			// --- Fields ---
			'gk-gravityview/view-field-add'               => [
				[
					'ability' => 'gk-gravityview/view-field-patch',
					'when'    => __( 'To tweak settings on the slot just created.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-field-move',
					'when'    => __( 'To reorder the new slot relative to others.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-field-render',
					'when'    => __( 'To preview how the slot renders before saving the View.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-field-patch'             => [
				[
					'ability' => 'gk-gravityview/view-field-render',
					'when'    => __( 'To preview the slot with the new settings applied.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the persisted slot shape.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-field-move'              => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the new ordering.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-field-remove'            => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the slot is gone.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-field-render'            => [
				[
					'ability' => 'gk-gravityview/view-field-patch',
					'when'    => __( 'When the preview suggests a settings change.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-field-clone'             => [
				[
					'ability' => 'gk-gravityview/view-field-patch',
					'when'    => __( 'To tweak settings on the cloned slot.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-field-move',
					'when'    => __( 'To reposition the clone after creating it.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-field-render',
					'when'    => __( 'To preview how the cloned slot renders.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-field-get'               => [
				[
					'ability' => 'gk-gravityview/view-field-patch',
					'when'    => __( 'To update the slot based on inspector edits.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-field-clone',
					'when'    => __( 'To duplicate the inspected slot.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/grid-row-clone'               => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the new row\'s area keys + position.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/grid-row-patch',
					'when'    => __( 'To change the cloned row\'s column type.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-partial-render'          => [
				[
					'ability' => 'gk-gravityview/view-field-render',
					'when'    => __( 'To re-render a single field after a finer-grain edit.', 'gk-gravityview' ),
				],
			],

			// --- Widgets ---
			'gk-gravityview/view-widget-add'              => [
				[
					'ability' => 'gk-gravityview/view-widget-patch',
					'when'    => __( 'To tweak the new widget\'s settings.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-widget-move',
					'when'    => __( 'To reposition the widget within its area or move it to header/footer.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/search-bar-add',
					'when'    => __( 'Preferred when you added a search_bar: it adds the bar AND its fields in one call (no slot bookkeeping).', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/search-field-add',
					'when'    => __( 'Advanced: place one search field inside an existing search_bar by widget_area + widget_slot.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-widget-patch'            => [
				[
					'ability' => 'gk-gravityview/view-widget-settings-get',
					'when'    => __( 'To read back the resolved settings + schema for this widget.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the persisted widget shape.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-widget-remove'           => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the widget is gone.', 'gk-gravityview' ),
				],
			],

			// --- Search Fields ---
			'gk-gravityview/search-bar-add'               => [
				[
					'ability' => 'gk-gravityview/search-field-patch',
					'when'    => __( 'To tweak a search field\'s settings (input type, label, visibility) after adding it.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the search bar and its fields persisted.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/search-field-add'             => [
				[
					'ability' => 'gk-gravityview/search-field-patch',
					'when'    => __( 'To tweak the new search field\'s settings.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/search-field-move',
					'when'    => __( 'To reorder the search field within the search_bar widget.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/search-field-patch'           => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the persisted search-field shape.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/search-field-remove'          => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the search field is gone.', 'gk-gravityview' ),
				],
			],

			// --- Grid ---
			'gk-gravityview/grid-row-add'                 => [
				[
					'ability' => 'gk-gravityview/view-field-add',
					'when'    => __( 'To place a field into one of the new row\'s areas.', 'gk-gravityview' ),
				],
				[
					'ability' => 'gk-gravityview/grid-row-move',
					'when'    => __( 'To reorder the row within its zone.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/grid-row-patch'               => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the row shape.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/grid-row-remove'              => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the row is gone.', 'gk-gravityview' ),
				],
			],

			// --- Preview ---
			'gk-gravityview/preview-stage-create'         => [
				[
					'ability' => 'gk-gravityview/preview-stage-delete',
					'when'    => __( 'When done previewing, to clear the staged tree.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/preview-stage-delete'         => [],
			// Phase 4i additions — 5 new abilities (3.1.0).
			'gk-gravityview/view-widget-move'             => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To verify the widget landed at the expected position in to_area.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-widget-settings-get'     => [
				[
					'ability' => 'gk-gravityview/view-widget-patch',
					'when'    => __( 'To edit the widget\'s settings using the returned schema.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/search-field-move'            => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the search field is in its new position inside the search_bar widget.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/grid-row-move'                => [
				[
					'ability' => 'gk-gravityview/view-config-get',
					'when'    => __( 'To confirm the row order in the target zone.', 'gk-gravityview' ),
				],
			],
			'gk-gravityview/view-delete'                  => [
				[
					'ability' => 'gk-gravityview/views-list',
					'when'    => __( 'To list remaining Views after deletion.', 'gk-gravityview' ),
				],
			],
		];

		return $map;
	}

	/**
	 * Suggested follow-up abilities for one ability.
	 *
	 * Returns an empty array when the ability has no registered next
	 * steps. The result rides on the ability's
	 * `meta.annotations.next_steps`, surfaced verbatim by the catalog
	 * endpoint so AI clients can chain calls without out-of-band docs.
	 *
	 * @since 3.0.0
	 *
	 * @param string $ability_name Fully-qualified ability name.
	 *
	 * @return array<int, array{ability:string,when:string}>
	 */
	public static function next_steps_for( string $ability_name ): array {
		$map = self::next_steps_map();
		return $map[ $ability_name ] ?? [];
	}

	/**
	 * Run a write callable with all post-meta DB writes short-circuited.
	 *
	 * When `$is_dry` is false, the callable runs normally.
	 *
	 * When `$is_dry` is true, three filters are temporarily registered:
	 *   - `update_post_metadata`
	 *   - `add_post_metadata`
	 *   - `delete_post_metadata`
	 *
	 * Each returns `true` (signalling "the write succeeded") which
	 * short-circuits WordPress's `update_metadata()`, `add_metadata()`,
	 * and `delete_metadata()` BEFORE the DB write, BEFORE the meta-cache
	 * delete, AND BEFORE the post-write action (`updated_post_meta`,
	 * `added_post_meta`, `deleted_post_meta`) fires. Result: a fully
	 * side-effect-free dry-run for meta-only write paths.
	 *
	 * Use this in ability `execute_callback`s that delegate to
	 * `InspectorRoute` methods — those methods run their full
	 * validation + sanitization pipeline against the live tree, then
	 * "persist" through the now-no-op meta calls and return their
	 * normal response shape. Callers stamp `dry_run: true` on the
	 * response data afterwards.
	 *
	 * NOT suitable for writes that go through `wp_insert_post()` or
	 * `wp_update_post()` (post-column writes, status changes) — those
	 * lack a comparable short-circuit filter and would actually persist.
	 *
	 * @since 3.0.0
	 *
	 * @param bool     $is_dry   When true, block all post-meta writes for the callable's duration.
	 * @param callable $callback The write to run.
	 *
	 * @return mixed Whatever `$callback` returns.
	 */
	public static function with_dry_run( bool $is_dry, callable $callback ) {
		if ( ! $is_dry ) {
			return $callback();
		}

		// Each filter receives different arg counts in WP core, so PHP's
		// "extra args are ignored" rule keeps a parameterless closure safe.
		$short_circuit = static function () {
			return true;
		};

		add_filter( 'update_post_metadata', $short_circuit, PHP_INT_MAX );
		add_filter( 'add_post_metadata', $short_circuit, PHP_INT_MAX );
		add_filter( 'delete_post_metadata', $short_circuit, PHP_INT_MAX );

		// Re-entrant counter, not a boolean, so nested with_dry_run
		// calls (which legitimately happen when a write ability calls
		// another write ability inside its callback) don't reset the
		// flag prematurely when the inner finally block runs.
		++self::$dry_run_depth;

		try {
			return $callback();
		} finally {
			--self::$dry_run_depth;
			remove_filter( 'update_post_metadata', $short_circuit, PHP_INT_MAX );
			remove_filter( 'add_post_metadata', $short_circuit, PHP_INT_MAX );
			remove_filter( 'delete_post_metadata', $short_circuit, PHP_INT_MAX );
		}
	}

	/**
	 * Whether a `with_dry_run` callable is currently on the stack.
	 *
	 * Write paths that bypass the `update_post_metadata` filter family
	 * (direct $wpdb writes to wp_posts, wp_insert_post / wp_update_post,
	 * etc.) must check this flag and short-circuit themselves; the
	 * meta-write filters cannot reach them. Callers that DON'T bypass
	 * the meta-write filters can ignore this flag entirely.
	 *
	 * Re-entrant: a non-zero depth means at least one dry-run frame is
	 * active, even if nested write abilities have layered on top.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function is_dry_run_active(): bool {
		return self::$dry_run_depth > 0;
	}

	/**
	 * Re-entrant counter of active `with_dry_run` frames. Incremented
	 * on entry, decremented in finally; never directly mutated.
	 *
	 * @since 3.0.0
	 *
	 * @var int
	 */
	private static $dry_run_depth = 0;

	/**
	 * Standard input-schema fragment for the `dry_run` input — wire
	 * this into every write ability that supports dry-run validation.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string,mixed>
	 */
	public static function dry_run_input_schema(): array {
		return [
			'type'        => 'boolean',
			'description' => __( 'Run validation/planning without persisting changes. Meta-only writes use GravityView dry-run suppression; post-column writes use Foundation post-write guards.', 'gk-gravityview' ),
		];
	}

	/**
	 * Shared post-status filter PROPERTY schema. Reused by every ability
	 * that filters Views by post status (views-list, views-scan) so a
	 * single status (`publish`, `draft`, `pending`, `private`, `trash`),
	 * an array of statuses, or `any` is accepted identically everywhere.
	 *
	 * Returns just the property schema — drop it under a `status` key in
	 * the owning ability's `input_schema.properties`.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string,mixed>
	 */
	public static function status_filter_schema(): array {
		return [
			'description' => __( 'Post status filter. Pass a single status (publish, draft, pending, private, trash), an array of statuses, or `any` for every editable status except trash/auto-draft.', 'gk-gravityview' ),
			'type'        => [ 'string', 'array' ],
			'items'       => [ 'type' => 'string' ],
		];
	}

	/**
	 * Stamp dry-run markers on a response payload returned from a
	 * write ability that ran inside `with_dry_run`. Idempotent.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $response Whatever the route handler returned.
	 * @param bool  $is_dry   Whether the call was actually a dry-run.
	 *
	 * @return mixed
	 */
	public static function mark_dry_run( $response, bool $is_dry ) {
		if ( ! $is_dry || ! is_array( $response ) ) {
			return $response;
		}
		$response['dry_run'] = true;
		return $response;
	}

	/**
	 * Validate `$input['id']` resolves to an existing `gravityview` post.
	 *
	 * Returns the integer view id on success; returns a `WP_Error`
	 * with HTTP 404 when the id is missing, non-positive, the post
	 * doesn't exist, or the post is not a View.
	 *
	 * Why a helper: every per-View ability needs the same guard inside
	 * `execute_callback`. The Abilities API REST controller forces any
	 * `WP_Error` returned by a `permission_callback` to HTTP 401 / 403
	 * regardless of the error's declared `status` data, so a "view not
	 * found" 404 has to come from the execute layer or it gets masked
	 * as 403.
	 *
	 * @since 3.0.0
	 *
	 * @param array $input Raw input passed to `execute_callback`.
	 *
	 * @return int|\WP_Error
	 */
	public static function require_view_id( array $input ) {
		$view_id = (int) ( $input['id'] ?? 0 );
		if ( $view_id <= 0 ) {
			return new \WP_Error(
				'gv_rest_view_not_found',
				__( 'View not found.', 'gk-gravityview' ),
				[ 'status' => 404 ]
			);
		}

		$post = get_post( $view_id );
		if ( ! $post || 'gravityview' !== get_post_type( $post ) ) {
			return new \WP_Error(
				'gv_rest_view_not_found',
				__( 'View not found.', 'gk-gravityview' ),
				[ 'status' => 404 ]
			);
		}

		return $view_id;
	}
}
