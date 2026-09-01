<?php
/**
 * REST API controller for the Design Studio inspector / view-config editing surface.
 *
 * Registers a family of bespoke routes under `gravityview/v1/views/{id}/…` that
 * read and write the View's editable configuration: field tree, areas, settings
 * schema, widgets, template settings, search criteria, plus per-slot CRUD for
 * fields and widgets. The canonical resource is `/config` — a single coherent
 * JSON document that matches the legacy `_gravityview_*_fields` /
 * `_gravityview_template_settings` post-meta storage shape.
 *
 * Permission policy is uniform: every read and write requires
 * `current_user_can( 'edit_post', $view_id )`. That mirrors the cap check the
 * legacy `AdminViews::save_postdata()` enforces, so the REST surface can't be
 * used to bypass admin permissions.
 *
 * Conflict detection uses an If-Match precondition. Each `/config` PATCH must
 * include the version string returned by the most recent GET — server compares
 * to `post_modified_gmt` + a write counter and returns 412 on mismatch. The
 * client is expected to refetch and replay.
 *
 * @package GravityKit\GravityView\REST
 * @since   TBD
 */

namespace GravityKit\GravityView\REST;

defined( 'ABSPATH' ) || die();

use GravityKit\GravityView\Renderer\Grid;
use GravityKit\GravityView\Search\SearchFieldResolver;
use GravityKit\GravityView\Settings\ViewSettings;
use GravityKit\GravityView\View\View;
use GravityKit\GravityView\Widget\Widget;
use GravityView_Render_Settings;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Inspector / config-editing REST controller.
 *
 * @since 3.0.0
 */
final class InspectorRoute {

	/**
	 * Post-meta key constants. Pulled out of literal strings so a
	 * future GravityView core rename only has to touch this block.
	 *
	 * @since 3.0.0
	 */
	const META_FORM_ID            = '_gravityview_form_id';
	const META_DIRECTORY_TEMPLATE = '_gravityview_directory_template';
	const META_SINGLE_TEMPLATE    = '_gravityview_single_template';
	const META_TEMPLATE_SETTINGS  = '_gravityview_template_settings';
	const META_FIELDS             = '_gravityview_directory_fields';
	const META_WIDGETS            = '_gravityview_directory_widgets';
	const META_VERSION_COUNTER    = '_gravityview_config_version_counter';

	/**
	 * Transient key prefix for the Design Studio preview-staging
	 * mechanism. Full key: `{prefix}{stage_key}`. The 32-char hex
	 * stage_key is unguessable; the stored payload separately carries
	 * `view_id` + `user_id` for defence-in-depth ownership validation.
	 *
	 * @since 3.0.0
	 */
	const STAGE_TRANSIENT_PREFIX = 'gv_preview_stage_';
	const STAGE_TTL_SECONDS      = 300; // 5 minutes

	/**
	 * Fixed widget zone catalogue. Mirrors the legacy widget-zone
	 * inventory `wp_ajax_gv_create_row` accepts for `type=widget`.
	 * Lifted to a constant so the grid surface, /widget-zones
	 * discovery endpoint, and area validation share one source.
	 *
	 * @since 3.0.0
	 */
	/**
	 * Widget meta-zones. The 8 visible zone names customers see in
	 * the inspector (header_top, header_left, footer_right, …) are
	 * actually `{meta_zone}_{column_areaid}` combinations — they're
	 * synthesized from these two zones plus the row-template's per-
	 * column areaid (`top`, `left`, `right`, etc.). See
	 * `AdminViews::render_active_areas` for the rendering side.
	 */
	const WIDGET_ZONES = [ 'header', 'footer' ];

	/**
	 * Per-request accumulator of settings the server silently rejected
	 * during sanitization (today: malformed `conditional_logic` JSON
	 * docs that would crash the public View render). Apply / PATCH
	 * handlers reset this at the top of each call and surface its
	 * contents under `warnings` in the response so callers can see
	 * exactly which slot+key was dropped and why instead of
	 * discovering a missing setting on the next GET.
	 *
	 * Each entry: `{ area: string, slot: string, key: string, reason: string }`.
	 *
	 * @since 3.0.0
	 *
	 * @var array<int, array{area:string, slot:string, key:string, reason:string}>
	 */
	private array $apply_warnings = [];

	/**
	 * Wrap a route callback so any uncaught `Throwable` thrown by the
	 * handler (or by a filter that runs inside it) is turned into a
	 * `WP_Error` with the underlying class, message, file, and line.
	 *
	 * Without this wrapper WP's REST dispatcher lets the throwable
	 * bubble to PHP's fatal handler — the response is a bare HTTP 500
	 * with an empty body and the client only sees "500 ()". This makes
	 * every inspector endpoint self-diagnosing: the JSON response
	 * carries the throw site (and, when `WP_DEBUG` is on, a small
	 * trace) so a customer or support agent can paste it back without
	 * having to grep `wp-content/debug.log` for the matching entry.
	 *
	 * Returns a `Closure` that delegates to `$callback` and catches
	 * everything, so this is safe to apply to every handler in
	 * `register_routes()` without touching their implementations.
	 *
	 * Public so the ability shims under `src/Abilities/` can wrap their
	 * direct InspectorRoute method calls. With register_routes() now a
	 * no-op (the REST surface is reached exclusively through abilities),
	 * any Throwable from a schema callable, filter listener, or
	 * downstream sanitiser would otherwise bypass the normalisation
	 * promised by `gv_rest_handler_exception`. Recommended ability
	 * pattern:
	 *
	 *     $route   = new \GravityKit\GravityView\REST\InspectorRoute();
	 *     $invoke  = $route->safe( [ $route, 'create_view' ] );
	 *     $response = $invoke( $request );
	 *     if ( is_wp_error( $response ) ) { return $response; }
	 *
	 * @since 3.0.0
	 *
	 * @param callable $callback The original handler.
	 *
	 * @return \Closure
	 */
	public function safe( callable $callback ): \Closure {
		return function ( ...$args ) use ( $callback ) {
			try {
				return $callback( ...$args );
			} catch ( \Throwable $e ) {
				$is_debug = defined( 'WP_DEBUG' ) && WP_DEBUG;
				$where    = sprintf( '%s:%d', $e->getFile(), $e->getLine() );

				$data = [
					'status'    => 500,
					'exception' => get_class( $e ),
				];

				// File paths and stack traces are info-disclosure surface
				// in production. Gate them behind WP_DEBUG so a leaked
				// 500 response doesn't reveal the absolute plugin path.
				// The full where + trace always hit the server-side log.
				if ( $is_debug ) {
					$data['where'] = $where;
					$data['trace'] = array_slice( explode( "\n", $e->getTraceAsString() ), 0, 6 );
				}

				if ( function_exists( 'gravityview' ) ) {
					gravityview()->log->error(
						'[InspectorRoute] Uncaught {class} in handler: {message} at {where}',
						[
							'class'   => get_class( $e ),
							'message' => $e->getMessage(),
							'where'   => $where,
						]
					);
				}

				return new WP_Error(
					'gv_rest_handler_exception',
					$e->getMessage() !== '' ? $e->getMessage() : 'Inspector route threw without a message.',
					$data
				);
			}
		};
	}

	/**
	 * Convenience wrapper: pick an InspectorRoute method by name, run
	 * it through `safe()`, and return its result.
	 *
	 * Ability shims under `src/Abilities/` use this in place of
	 * `$route->some_method($request)` so any uncaught Throwable becomes
	 * the normalised `gv_rest_handler_exception` WP_Error instead of a
	 * bare HTTP 500 with an empty body. Equivalent to:
	 *
	 *     $invoke = $route->safe( [ $route, 'some_method' ] );
	 *     return $invoke( ...$args );
	 *
	 * @since 3.0.0
	 *
	 * @param string $method  InspectorRoute public method to invoke.
	 * @param mixed  ...$args Arguments forwarded to the method.
	 *
	 * @return mixed Whatever the method returns, OR a WP_Error with
	 *               code `gv_rest_handler_exception` on Throwable.
	 */
	public function invoke_safely( string $method, ...$args ) {
		$callback = $this->safe( array( $this, $method ) );
		return $callback( ...$args );
	}

	/**
	 * Register every route in the family.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// All inspector endpoints have been migrated to the WordPress
		// Abilities API. Each former route is now a Foundation-registered
		// ability under the `gk-gravityview/` namespace, exposed at
		// `/wp-json/wp-abilities/v1/abilities/gk-gravityview/{name}/run`.
		// See `src/Abilities/Bootstrap.php` and the per-category folders
		// (`Discovery/`, `Views/`, `Fields/`, `Widgets/`, `SearchFields/`,
		// `Grid/`, `Preview/`).
		//
		// The handler methods on this class (`get_layouts`, `apply_config`,
		// etc.) are kept — every ability shim delegates to them via a
		// synthesised `WP_REST_Request`. They are NOT directly exposed
		// on the wire in production any more.
		//
		// For the PHPUnit suite ONLY, register the legacy `/gravityview/v1/*`
		// routes so the pre-migration `Inspector_Route_Test` + `_Shape_Test`
		// files can keep dispatching through the REST API. The routes wrap
		// the exact same handler methods the abilities call, so the test
		// surface stays representative of production behaviour without
		// duplicating logic. Gated on `DOING_GRAVITYVIEW_TESTS` so production
		// keeps the abilities-only surface.
		if ( ! defined( 'DOING_GRAVITYVIEW_TESTS' ) || ! DOING_GRAVITYVIEW_TESTS ) {
			return;
		}

			$namespace = Core::get_namespace();

			// `/widget-zones` — the 2 widget meta-zones (`header`, `footer`).
			// Used as the `zones` param for surface=widgets grid CRUD.
			register_rest_route(
				$namespace,
				'/widget-zones',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_widget_zones' ] ),
					'permission_callback' => [ $this, 'permission_edit_any_view' ],
				]
			);

			// `/search-zones` — the search-bar internal zones
			// (`search-general`, `search-advanced`). Filterable so add-ons
			// can register additional search sections.
			register_rest_route(
				$namespace,
				'/search-zones',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_search_zones' ] ),
					'permission_callback' => [ $this, 'permission_edit_any_view' ],
				]
			);

			// `/widgets` — list every registered widget id with its label /
			// description / icon. Lets AI agents and Design Studio discover
			// the placeable widgets (search_bar, page_links, custom_content,
			// poll, gravityforms, …) without hardcoding the catalogue.
			register_rest_route(
				$namespace,
				'/widgets',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_widgets' ] ),
					'permission_callback' => [ $this, 'permission_edit_any_view' ],
				]
			);

			// `/grid/row-types` — list every registered Layout Builder row
			// type (100, 50/50, 33/66, 66/33, 33/33/33, 25/25/25/25, …).
			// Sourced from `\GV\Grid::get_row_types()` so add-ons that
			// register custom row layouts surface here automatically.
			register_rest_route(
				$namespace,
				'/grid/row-types',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_grid_row_types' ] ),
					'permission_callback' => [ $this, 'permission_edit_any_view' ],
				]
			);

			// `/views/{id}/grid/_rows` — add a grid row to one of the
			// View's grid surfaces. Surface dispatch table:
			// surface=fields  → _gravityview_directory_fields, prefix
			// by per-zone template id (Layout Builder
			// etc.). Zones: directory | single.
			// surface=widgets → _gravityview_directory_widgets, no
			// prefix (zone name IS the areaid root).
			// Zones: header_top, header_bottom,
			// header_left, header_right, footer_top,
			// footer_bottom, footer_left, footer_right.
			// Returns the new row_uid + areaids it created (per zone).
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/grid/_rows',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'create_grid_row' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						[
							'surface' => [
								'description' => __( 'Grid surface: "fields" (default — View main field tree per zone) or "widgets" (header/footer widget zones).', 'gk-gravityview' ),
								'type'        => 'string',
								'enum'        => [ 'fields', 'widgets' ],
								'default'     => 'fields',
							],
							'type'    => [
								'description' => __( 'Row type. Use GET /grid/row-types for the live list (e.g. "100", "50/50", "33/66", "33/33/33", "25/25/25/25"). Defaults to "100".', 'gk-gravityview' ),
								'type'        => 'string',
							],
							'zones'   => [
								'description' => __( 'Zones to materialise the row in. For surface=fields defaults to ["directory","single"]; for surface=widgets defaults to ALL widget zones the requested area is part of (or the explicit list provided).', 'gk-gravityview' ),
								'type'        => 'array',
							],
						]
					),
				]
			);

			// `/views/{id}/grid/_rows/{row_uid}` — patch (re-key) or delete a row.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/grid/_rows/(?P<row_uid>[a-f0-9]{8,32})',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => $this->safe( [ $this, 'patch_grid_row' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
						'args'                => [
							'type' => [
								'description' => __( 'New row type. Existing fields are re-keyed; if column count changes, surplus fields collapse into the first column.', 'gk-gravityview' ),
								'type'        => 'string',
							],
						],
					],
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => $this->safe( [ $this, 'delete_grid_row' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
					],
				]
			);

			// `/views` — create a draft View, optionally seeded in one
			// shot. Mirrors the `/views/{id}/config/_apply` payload so
			// AI agents can author a complete View without a follow-up
			// request: pass `title` + `form_id` (required), then
			// optionally `template_id`, `template_settings`,
			// `search_criteria`, `fields`, `widgets`, and `mode`.
			register_rest_route(
				$namespace,
				'/views',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'create_view' ] ),
					'permission_callback' => [ $this, 'permission_create' ],
					'args'                => [
						'title'             => [
							'description' => __( 'View title (post title).', 'gk-gravityview' ),
							'type'        => 'string',
							'required'    => true,
						],
						'form_id'           => [
							'description' => __( 'Source Gravity Forms form id.', 'gk-gravityview' ),
							'type'        => 'integer',
							'required'    => true,
						],
						'template_id'       => [
							'description' => __( 'Layout template id for the directory zone (Multiple Entries listing). Defaults to gravityview-layout-builder so AI authoring lands on the most flexible layout. Use template_ids to set Single / Edit zone templates separately.', 'gk-gravityview' ),
							'type'        => 'string',
						],
						'template_ids'      => [
							'description' => __( 'Optional per-zone template overrides: { directory?, single?, edit? }. Single defaults to directory if omitted; edit follows directory unless explicitly set.', 'gk-gravityview' ),
							'type'        => 'object',
						],
						'status'            => [
							'description' => __( 'Initial post status. Defaults to draft so the View doesn\'t go public before its config is finalised.', 'gk-gravityview' ),
							'type'        => 'string',
							'enum'        => [ 'draft', 'publish', 'pending', 'private' ],
							'default'     => 'draft',
						],
						'template_settings' => [
							'description' => __( 'Optional initial template_settings (page_size, lightbox, etc.).', 'gk-gravityview' ),
							'type'        => 'object',
						],
						'search_criteria'   => [
							'description' => __( 'Optional initial search_criteria (sort, pagination defaults).', 'gk-gravityview' ),
							'type'        => 'object',
						],
						'fields'            => [
							'description' => __( 'Optional initial field tree, same shape as POST /views/{id}/config/_apply. Mode defaults to replace.', 'gk-gravityview' ),
							'type'        => 'object',
						],
						'widgets'           => [
							'description' => __( 'Optional initial widget tree, same shape as POST /views/{id}/config/_apply.', 'gk-gravityview' ),
							'type'        => 'object',
						],
						'mode'              => [
							'description' => __( 'replace = each area in fields/widgets replaces existing area. merge = additive. Default: replace.', 'gk-gravityview' ),
							'type'        => 'string',
							'enum'        => [ 'replace', 'merge' ],
							'default'     => 'replace',
						],
					],
				]
			);

			// `/views/{id}/config` — canonical config tree.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/config',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => $this->safe( [ $this, 'get_config' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
						'args'                => $this->arg_id(),
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => $this->safe( [ $this, 'patch_config' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
						'args'                => array_merge(
							$this->arg_id(),
							[
								'fields'            => [
									'description' => __( 'Full or partial field tree, keyed by area then slot uid.', 'gk-gravityview' ),
									'type'        => 'object',
								],
								'widgets'           => [
									'description' => __( 'Full or partial widget tree.', 'gk-gravityview' ),
									'type'        => 'object',
								],
								'template_settings' => [
									'description' => __( 'View-wide template settings (page_size, lightbox, etc.).', 'gk-gravityview' ),
									'type'        => 'object',
								],
								'search_criteria'   => [
									'description' => __( 'Pagination + sort defaults. Persisted into template_settings.', 'gk-gravityview' ),
									'type'        => 'object',
								],
							]
						),
					],
				]
			);

			// `/views/{id}/areas` — area / zone inventory for the View's template.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/areas',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_areas' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
				]
			);

			// `/views/{id}/config/_apply` — bulk-apply a full or partial
			// View configuration in ONE request. The AI-first counterpart
			// to PATCH /config: accepts ordered field arrays (no slot UIDs
			// required — server mints them), runs the same per-setting
			// sanitization + schema validation pipeline, applies template
			// switch + template settings + search criteria in the same
			// pass, and returns the full updated config so the client
			// never needs a follow-up GET.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/config/_apply',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'apply_config' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						[
							'template_id'       => [
								'description' => __( 'Switch the View template before applying fields/widgets. Equivalent to PATCH /template + the apply.', 'gk-gravityview' ),
								'type'        => 'string',
							],
							'template_settings' => [
								'description' => __( 'Partial-merge into _gravityview_template_settings.', 'gk-gravityview' ),
								'type'        => 'object',
							],
							'search_criteria'   => [
								'description' => __( 'Pagination + sort. Persisted into template_settings.', 'gk-gravityview' ),
								'type'        => 'object',
							],
							'fields'            => [
								'description' => __( 'Ordered field arrays per area key. Each item: { field_id (required), slot? (server-generated when omitted), label?, …settings }. Replaces the area entirely when mode=replace.', 'gk-gravityview' ),
								'type'        => 'object',
							],
							'widgets'           => [
								'description' => __( 'Same shape as fields, for widget slots.', 'gk-gravityview' ),
								'type'        => 'object',
							],
							'mode'              => [
								'description' => __( 'replace = each area in fields/widgets replaces existing area. merge = additive. Default: replace.', 'gk-gravityview' ),
								'type'        => 'string',
								'enum'        => [ 'replace', 'merge' ],
								'default'     => 'replace',
							],
						]
					),
				]
			);

			// `/forms` — Gravity Forms available as a data source. AI / UI
			// pickers use it to populate the Data tab's "Choose form"
			// control. Permission: same edit_posts gate as /layouts.
			register_rest_route(
				$namespace,
				'/forms',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_forms' ] ),
					'permission_callback' => [ $this, 'permission_edit_any_view' ],
				]
			);

			// `/layouts` — installed layout engines (Layout Builder, DIY,
			// Table, List, DataTables, Map, …). Drops the legacy `preset_*`
			// entries (migrating to a JSON-import flow) and the
			// `*_placeholder` rows that exist only when an add-on isn't
			// active. AI / external clients use this list to discover valid
			// `template_id` values when creating or switching a View.
			register_rest_route(
				$namespace,
				'/layouts',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_layouts' ] ),
					'permission_callback' => [ $this, 'permission_edit_any_view' ],
				]
			);

			// `/field-types/{type}/schema` — schema for a field type
			// without needing a slot or a View. AI agents building a new
			// View can discover all settings for `custom`, `entry_link`,
			// `text`, etc. before any field is placed.
			register_rest_route(
				$namespace,
				'/field-types/(?P<field_type>[\w-]+)/schema',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_field_type_schema' ] ),
					'permission_callback' => [ $this, 'permission_edit_any_view' ],
					'args'                => [
						'template_id' => [
							'description' => __( 'Layout template id. Defaults to default_list.', 'gk-gravityview' ),
							'type'        => 'string',
							'default'     => 'default_list',
						],
						'context'     => [
							'description' => __( 'Render context. One of: multiple, single, edit, search.', 'gk-gravityview' ),
							'type'        => 'string',
							'default'     => 'multiple',
							'enum'        => [ 'multiple', 'single', 'edit', 'search' ],
						],
						'input_type'  => [
							'description' => __( 'Gravity Forms input type (textarea, select, …) when applicable.', 'gk-gravityview' ),
							'type'        => 'string',
							'default'     => '',
						],
						'form_id'     => [
							'description' => __( 'Gravity Forms form id, for input-type detection.', 'gk-gravityview' ),
							'type'        => 'integer',
							'default'     => 0,
						],
					],
				]
			);

			// `/views/{id}/field-settings-schema` — bulk schema for every configured slot.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/field-settings-schema',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_field_settings_schema_bulk' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
				]
			);

			// `/views/{id}/fields/{area}/{slot}/settings-schema` — single-slot schema.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/fields/(?P<area>[\w%-]+(?:(?:%3A%3A|::)[^/]+)*)/(?P<slot>[a-zA-Z0-9][\w.-]*)/settings-schema',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_field_settings_schema_one' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						$this->arg_area(),
						$this->arg_slot()
					),
				]
			);

			// `/views/{id}/fields/{area}/{slot}` — per-slot CRUD.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/fields/(?P<area>[\w%-]+(?:(?:%3A%3A|::)[^/]+)*)/(?P<slot>[a-zA-Z0-9][\w.-]*)',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => $this->safe( [ $this, 'patch_field' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
						'args'                => array_merge(
							$this->arg_id(),
							$this->arg_area(),
							$this->arg_slot()
						),
					],
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => $this->safe( [ $this, 'delete_field' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
						'args'                => array_merge(
							$this->arg_id(),
							$this->arg_area(),
							$this->arg_slot()
						),
					],
				]
			);

			// `/views/{id}/fields/{area}/_slots` — create slot (returns server-generated UID).
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/fields/(?P<area>[\w%-]+(?:(?:%3A%3A|::)[^/]+)*)/_slots',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'create_field_slot' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						$this->arg_area(),
						[
							'field_id' => [
								'description' => __( 'GF field id (e.g. "1", "5.2") or GravityView meta-field slug (e.g. "entry_link", "custom").', 'gk-gravityview' ),
								'type'        => 'string',
								'required'    => true,
							],
							'label'    => [
								'description' => __( 'Display label for the new slot. Defaults to the form field label or the GV field default.', 'gk-gravityview' ),
								'type'        => 'string',
								'default'     => '',
							],
						]
					),
				]
			);

			// `/views/{id}/fields/_move` — atomic move between areas / within an area.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/fields/_move',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'move_field' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						[
							'from'     => [
								'description' => __( 'Source slot reference: { "area": "directory_list-title", "slot": "abc123" }.', 'gk-gravityview' ),
								'type'        => 'object',
								'required'    => true,
							],
							'to'       => [
								'description' => __( 'Target placement: { "area": <area_key> [, "before_slot": <uid> | "after_slot": <uid> | "position": <int|"start"|"end"> ] }. before_slot / after_slot resolve relative to existing slot UIDs in the area (precedence: before > after > position). The moved slot keeps its UID.', 'gk-gravityview' ),
								'type'        => 'object',
								'required'    => true,
							],
							'position' => [
								'description' => __( 'Insertion position in the target area when before_slot / after_slot aren\'t set. Accepts a zero-based integer, "start", or "end" (default). Negative integers append. Top-level `position` wins over `to.position` if both are set.', 'gk-gravityview' ),
							],
						]
					),
				]
			);

			// `/views/{id}/fields/_clone` — duplicate a field slot.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/fields/_clone',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'clone_field' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						[
							'uid' => [
								'description' => __( 'UID of the slot to clone. The source area is resolved automatically.', 'gk-gravityview' ),
								'type'        => 'string',
								'required'    => true,
							],
							'to'  => [
								'description' => __( 'Optional placement: `{ area?, before_slot? | after_slot? | position? }`. Defaults to immediately after the source in the source area; append when moving cross-area.', 'gk-gravityview' ),
								'type'        => 'object',
								'required'    => false,
							],
						]
					),
				]
			);

			// `/views/{id}/fields/_one` — combined config + schema + rendered_html.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/fields/_one',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_field' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						[
							'uid' => [
								'description' => __( 'Slot UID. The area is resolved automatically.', 'gk-gravityview' ),
								'type'        => 'string',
								'required'    => true,
							],
						]
					),
				]
			);

			// `/views/{id}/render/_partial` — render a subtree (zone/row/slots).
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/render/_partial',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'render_partial' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						[
							'scope' => [
								'description' => __( '`{ type: zone|row|slots, ... }` — see InspectorRoute::render_partial doc.', 'gk-gravityview' ),
								'type'        => 'object',
								'required'    => true,
							],
						]
					),
				]
			);

			// `/views/{id}/grid/_rows/_clone` — duplicate a Layout Builder row.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/grid/_rows/_clone',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'clone_grid_row' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						[
							'row_uid' => [
								'description' => __( 'UID of the row to clone.', 'gk-gravityview' ),
								'type'        => 'string',
								'required'    => true,
							],
						]
					),
				]
			);

			// `/views/{id}/fields/{area}/{slot}/render` — rendered HTML for iframe swap.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/fields/(?P<area>[\w%-]+(?:(?:%3A%3A|::)[^/]+)*)/(?P<slot>[a-zA-Z0-9][\w.-]*)/render',
				[
					'methods'             => [ WP_REST_Server::READABLE, WP_REST_Server::CREATABLE ],
					'callback'            => $this->safe( [ $this, 'render_field' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						$this->arg_area(),
						$this->arg_slot()
					),
				]
			);

			// `/views/{id}/available-fields` — field picker data.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/available-fields',
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $this->safe( [ $this, 'get_available_fields' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
				]
			);

			// `/views/{id}/template` — template switch. Body accepts an
			// optional `zone` (directory|single|edit). Without `zone` the
			// directory template is updated (backward-compatible default).
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/template',
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => $this->safe( [ $this, 'patch_template' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => [
						'template_id' => [
							'description' => __( 'New template id for the targeted zone.', 'gk-gravityview' ),
							'type'        => 'string',
							'required'    => true,
						],
						'zone'        => [
							'description' => __( 'Zone to switch (directory | single | edit). Defaults to directory. The single zone falls back to directory when its meta is empty; clearing single returns it to directory inheritance.', 'gk-gravityview' ),
							'type'        => 'string',
							'enum'        => [ 'directory', 'single', 'edit' ],
							'default'     => 'directory',
						],
					],
				]
			);

			// `/views/{id}/template-settings` — view-level template settings.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/template-settings',
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => $this->safe( [ $this, 'patch_template_settings' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
				]
			);

			// `/views/{id}/search-criteria` — page size, sort, etc.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/search-criteria',
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => $this->safe( [ $this, 'patch_search_criteria' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
				]
			);

			// `/views/{id}/widgets/{area}/{slot}` — per-widget-slot edit / delete.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/widgets/(?P<area>[\w%-]+(?:(?:%3A%3A|::)[^/]+)*)/(?P<slot>[a-zA-Z0-9][\w.-]*)',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => $this->safe( [ $this, 'patch_widget' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
						'args'                => array_merge(
							$this->arg_id(),
							$this->arg_area(),
							$this->arg_slot()
						),
					],
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => $this->safe( [ $this, 'delete_widget' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
						'args'                => array_merge(
							$this->arg_id(),
							$this->arg_area(),
							$this->arg_slot()
						),
					],
				]
			);

			// `/views/{id}/widgets/{area}/_slots` — create widget slot.
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/widgets/(?P<area>[\w%-]+(?:(?:%3A%3A|::)[^/]+)*)/_slots',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'create_widget_slot' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
					'args'                => array_merge(
						$this->arg_id(),
						$this->arg_area()
					),
				]
			);

			// Search Bar internal slot CRUD. Storage is nested inside a
			// search_bar widget's `search_fields_section` setting (modern
			// shape). Widget area + slot identifiers are sent in the body
			// rather than the URL because the widget area can contain
			// compound keys like `header_top::100::ROW_UID` that are
			// painful to route through a regex.
			//
			// POST   /views/{id}/search-fields/_slots
			// body: { widget_area, widget_slot, position, field, slot? }
			// PATCH  /views/{id}/search-fields/{search_slot}
			// body: { widget_area, widget_slot, position, settings }
			// DELETE /views/{id}/search-fields/{search_slot}
			// body: { widget_area, widget_slot, position }
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/search-fields/_slots',
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => $this->safe( [ $this, 'create_search_field_slot' ] ),
					'permission_callback' => [ $this, 'permission_edit' ],
				]
			);
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/search-fields/(?P<search_slot>[a-zA-Z0-9][\w.-]*)',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => $this->safe( [ $this, 'patch_search_field_slot' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
					],
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => $this->safe( [ $this, 'delete_search_field_slot' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
					],
				]
			);

			// `/views/{id}/preview/_stage` — Design Studio live-preview
			// staging. Stores the in-flight `{fields, widgets,
			// template_settings}` tree in a per-user-per-view-per-tab
			// transient so the preview iframe can render unsaved changes
			// WITHOUT touching the View's persisted post meta. The DELETE
			// verb clears the stage on Save (the saved state is the new
			// truth) and Discard (changes were thrown away).
			register_rest_route(
				$namespace,
				'/views/(?P<id>\d+)/preview/_stage',
				[
					[
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => $this->safe( [ $this, 'create_preview_stage' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
					],
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => $this->safe( [ $this, 'delete_preview_stage' ] ),
						'permission_callback' => [ $this, 'permission_edit' ],
					],
				]
			);
	}

	// -------------------------------------------------------------------
	// Permission
	// -------------------------------------------------------------------

	/**
	 * Uniform permission check — must be able to edit the View post.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return bool|WP_Error
	 */
	public function permission_edit( WP_REST_Request $request ) {
		$view_id = (int) $request->get_param( 'id' );

		if ( $view_id <= 0 ) {
			return new WP_Error( 'gv_rest_invalid_view', __( 'Invalid View id.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$post = get_post( $view_id );
		if ( ! $post || View::POST_TYPE !== get_post_type( $post ) ) {
			return new WP_Error( 'gv_rest_view_not_found', __( 'View not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		if ( ! ( new \GravityKit\GravityView\Permissions\Permissions() )->can_edit_view( (int) $view_id ) ) {
			return new WP_Error( 'gv_rest_forbidden', __( 'You don\'t have permission to edit this View.', 'gk-gravityview' ), [ 'status' => rest_authorization_required_code() ] );
		}

		return true;
	}

	/**
	 * Permission check for endpoints that don't target a specific
	 * View (e.g. `/layouts`, `/field-types/{type}/schema`). Delegates to
	 * `DiscoveryPermissions::can_access_discovery()` — anyone with any
	 * of the three GV-author caps (edit_gravityviews, edit_others, or
	 * edit_published) can read these registry-style endpoints.
	 *
	 * @since 3.0.0
	 *
	 * @return bool|WP_Error True if allowed; WP_Error 403 if not.
	 */
	public function permission_edit_any_view() {
		// Discovery-style endpoint gate; routes through the unified
		// DiscoveryPermissions service so the gravityview_full_access
		// shortcut applies and the rule lives in one place.
		if ( ! ( new \GravityKit\GravityView\Permissions\Permissions() )->can_access_discovery() ) {
			return new WP_Error(
				'gv_rest_forbidden',
				__( 'You don\'t have permission to read GravityView templates.', 'gk-gravityview' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		return true;
	}

	/**
	 * Permission check for `POST /views`. Delegates to
	 * `Permissions::can_create_view_with_status('draft')` which gates
	 * on `edit_gravityviews` (the create-baseline cap). Publish-class
	 * statuses additionally require `publish_gravityviews` — but this
	 * route always creates as `draft` so the publish gate doesn't apply
	 * here; ViewsController::create_view enforces it on direct status
	 * specification.
	 *
	 * @since 3.0.0
	 *
	 * @return bool|WP_Error True if allowed; WP_Error 403 if not.
	 */
	public function permission_create() {
		// Status-conditional create gate centralized in
		// Permissions::can_create_view_with_status. Default to 'draft'
		// when the request doesn't include status (most create_view callers).
		// This restores parity with the abilities-side check for create-view.
		$perms  = new \GravityKit\GravityView\Permissions\Permissions();
		$result = $perms->can_create_view_with_status( 'draft' );
		if ( true !== $result ) {
			return new WP_Error(
				'gv_rest_forbidden',
				__( 'You don\'t have permission to create GravityView Views.', 'gk-gravityview' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		return true;
	}

	// -------------------------------------------------------------------
	// Reads
	// -------------------------------------------------------------------

	/**
	 * GET /views/{id}/config — full editable config tree.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_config( WP_REST_Request $request ): WP_REST_Response {
		$view_id = (int) $request['id'];

		$data = [
			'view_id'           => $view_id,
			'version'           => $this->compute_version( $view_id ),
			// `template_id` (singular) is the directory zone's template
			// for backward compatibility; `template_ids` (plural) carries
			// every zone so callers can see when single / edit have
			// been overridden away from directory's template.
			'template_id'       => (string) $this->resolve_template_id( $view_id, 'directory' ),
			'template_ids'      => $this->resolve_template_ids( $view_id ),
			'form_id'           => (int) $this->resolve_form_id( $view_id ),
			'areas'             => $this->build_areas( $view_id ),
			'fields'            => $this->read_fields( $view_id ),
			'widgets'           => $this->read_widgets( $view_id ),
			'template_settings' => $this->read_template_settings( $view_id ),
			'search_criteria'   => $this->read_search_criteria( $view_id ),
		];

		/**
		 * Filter the View config response to add add-on-owned top-level
		 * keys (e.g. Multiple Forms' `form_joins`, DataTables-specific
		 * settings silos, etc.). Add-ons listen here and append their
		 * own keys without core needing to know the schema.
		 *
		 * The companion write-side hook is `gk/gravityview/rest/view-config/apply/after`.
		 *
		 * @since 3.0.0
		 *
		 * @param array $data    Mutable response payload, keyed by top-level field.
		 * @param int   $view_id View post id.
		 */
		$data     = (array) apply_filters( 'gk/gravityview/rest/view-config/get', $data, $view_id );
		$response = new WP_REST_Response( $data, 200 );
		$response->header( 'ETag', '"' . $data['version'] . '"' );
		$response->header( 'Cache-Control', 'private, no-store' );

		return $response;
	}

	/**
	 * GET /views/{id}/areas — area inventory.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_areas( WP_REST_Request $request ): WP_REST_Response {
		$view_id = (int) $request['id'];

		return new WP_REST_Response(
			[
				'view_id'     => $view_id,
				'template_id' => (string) $this->resolve_template_id( $view_id ),
				'zones'       => $this->build_areas( $view_id ),
			],
			200
		);
	}

	/**
	 * GET /views/{id}/field-settings-schema — bulk schema for every configured slot.
	 *
	 * Returns a `{area}/{slot}` → schema map so the inspector can render
	 * any selected field without a follow-up round trip.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_field_settings_schema_bulk( WP_REST_Request $request ): WP_REST_Response {
		$view_id     = (int) $request['id'];
		$template_id = (string) $this->resolve_template_id( $view_id );
		$form_id     = (int) $this->resolve_form_id( $view_id );

		$fields = $this->read_fields( $view_id );
		$out    = [];

		foreach ( $fields as $area_key => $slots ) {
			foreach ( (array) $slots as $slot_uid => $slot ) {
				$key         = $area_key . '/' . $slot_uid;
				$out[ $key ] = $this->compute_slot_schema( $template_id, $form_id, $area_key, $slot, '', $view_id );
			}
		}

		return new WP_REST_Response(
			[
				'view_id' => $view_id,
				'schemas' => $out,
			],
			200
		);
	}

	/**
	 * GET /views/{id}/fields/{area}/{slot}/settings-schema — single-slot fallback.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_field_settings_schema_one( WP_REST_Request $request ) {
		$view_id     = (int) $request['id'];
		$area        = (string) $request['area'];
		$slot_uid    = (string) $request['slot'];
		$template_id = (string) $this->resolve_template_id( $view_id );
		$form_id     = (int) $this->resolve_form_id( $view_id );

		$slot = $this->read_slot( $view_id, $area, $slot_uid );
		if ( null === $slot ) {
			return new WP_Error( 'gv_rest_slot_not_found', __( 'Slot not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		return new WP_REST_Response(
			[
				'view_id' => $view_id,
				'area'    => $area,
				'slot'    => $slot_uid,
				'schema'  => $this->compute_slot_schema( $template_id, $form_id, $area, $slot, '', $view_id ),
				'values'  => $slot,
			],
			200
		);
	}

	/**
	 * GET /forms — Gravity Forms available as a View data source.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_forms( WP_REST_Request $request ): WP_REST_Response {
		$out = [];

		if ( class_exists( '\GFAPI' ) ) {
			$forms = \GFAPI::get_forms();
			foreach ( (array) $forms as $form ) {
				if ( ! is_array( $form ) ) {
					continue;
				}
				$out[] = [
					'id'     => (int) ( $form['id'] ?? 0 ),
					'title'  => (string) ( $form['title'] ?? '' ),
					'fields' => count( $form['fields'] ?? [] ),
				];
			}
		}

		return new WP_REST_Response( [ 'forms' => $out ], 200 );
	}

	/**
	 * GET /layouts — installed layout engines, minus presets and inactive
	 * add-on placeholders.
	 *
	 * The legacy `gravityview_register_directory_template` filter also
	 * returns `preset_*` rows (Business Listings, Job Board, Resume
	 * Board, …) — those are content presets on their way out, replaced
	 * by a forthcoming JSON-import flow. They aren't real layout engines
	 * and would clutter agent-driven discovery, so this endpoint filters
	 * them. `*_placeholder` rows surface when the corresponding add-on
	 * (DataTables, Map, DIY) isn't activated; they're useful for showing
	 * "install to enable" CTAs but not for placing fields, so they're
	 * filtered too.
	 *
	 * Each layout reports `has_grid` so callers know whether they
	 * should drive layout via `POST /views/{id}/grid/_rows` (Layout
	 * Builder today) or the static-area flow.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_layouts( WP_REST_Request $request ): WP_REST_Response {
		$templates = (array) apply_filters( 'gravityview_register_directory_template', [] );

		/**
		 * Filters the list of template ids that drive layout via the
		 * grid surface (`POST /views/{id}/grid/_rows`) rather than the
		 * static-area filter chain.
		 *
		 * Add-ons that ship a grid-aware template can register here so
		 * `/layouts` reports `has_grid: true` for their id and
		 * downstream callers route placement through the row CRUD
		 * endpoints.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $grid_templates Template ids using the grid surface. Defaults to `['gravityview-layout-builder']`.
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
				// Edit-only variant of the Table layout — selected automatically by Edit Entry, not a layout choice.
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

		return new WP_REST_Response(
			[
				'layouts' => $out,
			],
			200
		);
	}

	/**
	 * GET /templates/{template_id}/settings-schema — per-template
	 * settings catalogue.
	 *
	 * Walks every source registered via the
	 * `gk/gravityview/rest/template-settings/sources` filter, gated
	 * by each source's `template_ids`. Returns the same flat
	 * `{slug, type, label, value, options, group}` shape per-setting
	 * the field-type schema endpoints use, so a single client
	 * renderer covers both surfaces.
	 *
	 * For namespaced sources (DataTables registers with
	 * `prefix=datatables`), the `slug` is dotted —
	 * `datatables.responsive` — so writes back through PATCH /apply
	 * route the value to the right silo meta key automatically.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_template_settings_schema( WP_REST_Request $request ): WP_REST_Response {
		$template_id = (string) $request['template_id'];

		// Compute the union of groups owned by namespaced sources so
		// the core source can skip them — otherwise Maps' settings
		// would appear twice (once at top-level via
		// `gravityview_default_args` and once under `maps.*` via the
		// Maps silo source) AND the top-level slug would write to
		// the wrong meta key when an agent picks it from discovery.
		$resolved_sources = $this->template_settings_sources( $template_id );
		$silo_groups      = [];
		foreach ( $resolved_sources as $src ) {
			if ( '' !== $src['prefix'] && ! empty( $src['groups'] ) && is_array( $src['groups'] ) ) {
				foreach ( $src['groups'] as $g ) {
					$silo_groups[ (string) $g ] = true;
				}
			}
		}

		$out = [];
		foreach ( $resolved_sources as $src ) {
			$defs = [];
			if ( is_callable( $src['schema_callable'] ?? null ) ) {
				$result = call_user_func( $src['schema_callable'] );
				if ( is_array( $result ) ) {
					$defs = $result;
				}
			}

			foreach ( $defs as $slug => $def ) {
				if ( ! is_array( $def ) ) {
					continue;
				}
				// Core source skips entries claimed by a namespaced
				// silo source (e.g. `group=maps` belongs to the Maps
				// silo) so each setting appears exactly ONCE in the
				// schema, under the slug that writes to the right
				// meta key.
				if ( '' === $src['prefix'] && isset( $silo_groups[ (string) ( $def['group'] ?? '' ) ] ) ) {
					continue;
				}
				$dotted_slug = '' !== $src['prefix']
					? $src['prefix'] . '.' . (string) $slug
					: (string) $slug;

				$entry = [
					'slug'  => $dotted_slug,
					'type'  => (string) ( $def['type'] ?? 'text' ),
					'label' => $this->stringify_setting_meta( $def['label'] ?? '' ),
					'desc'  => $this->stringify_setting_meta( $def['desc'] ?? '' ),
					'value' => is_scalar( $def['value'] ?? null ) ? $def['value'] : '',
					'group' => (string) ( $def['group'] ?? 'display' ),
				];
				if ( ! empty( $def['choices'] ) && is_array( $def['choices'] ) ) {
					$entry['options'] = $def['choices'];
				} elseif ( ! empty( $def['options'] ) && is_array( $def['options'] ) ) {
					$entry['options'] = $def['options'];
				}

				$out[] = self::trim_schema_item( $entry );
			}
		}

		return new WP_REST_Response(
			[
				'template_id' => $template_id,
				'schema'      => $out,
			],
			200
		);
	}

	/**
	 * POST /views/{id}/preview/_stage — store the in-flight Design
	 * Studio tree so the preview iframe can render it without
	 * persisting to the View's post meta.
	 *
	 * The Design Studio stages edits client-side until Save. Without
	 * a server-side reflection of those edits, the preview iframe
	 * (which renders a real WP frontend request reading post_meta)
	 * shows the SAVED state — not what the customer just dragged in.
	 * This endpoint persists the in-flight `{fields, widgets,
	 * template_settings}` snapshot in a per-user-per-view-per-tab
	 * transient with a 5-minute TTL. A frontend renderer subscribed
	 * to `get_post_metadata` reads the transient by `stage_key` query
	 * arg during the iframe render and overlays the staged tree on
	 * top of saved meta.
	 *
	 * Body shape:
	 *   {
	 *     "tab_nonce": "abc123",      // optional, per-tab id
	 *     "fields": { ... } | null,
	 *     "widgets": { ... } | null,
	 *     "template_settings": { ... } | null
	 *   }
	 *
	 * Returns:
	 *   {
	 *     "stage_key": "<32 hex>",
	 *     "view_id": 9591,
	 *     "expires_at": "<ISO 8601>"
	 *   }
	 *
	 * Bulletproof contract: stages are scoped to (user_id, view_id);
	 * `delete_preview_stage()` validates ownership before clearing.
	 * Transient eviction under cache pressure surfaces as a missing
	 * overlay (preview falls back to saved state); never fatal.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_preview_stage( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];
		$user_id = (int) get_current_user_id();

		$payload = $request->get_json_params();
		if ( ! is_array( $payload ) ) {
			return new WP_Error( 'gv_rest_invalid_payload', __( 'JSON payload required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// The three trees are stored verbatim — the preview render
		// path consumes them through the same code paths the saved
		// state goes through, so any sanitization gap would already
		// have been a problem before staging existed. Accept null /
		// omitted to mean "this tree has no in-flight changes; use
		// the saved meta for it".
		$stage = [
			'view_id'           => $view_id,
			'user_id'           => $user_id,
			'fields'            => $this->normalize_stage_tree( $payload['fields'] ?? null ),
			'widgets'           => $this->normalize_stage_tree( $payload['widgets'] ?? null ),
			'template_settings' => is_array( $payload['template_settings'] ?? null ) ? $payload['template_settings'] : null,
			'created_at'        => time(),
		];

		$stage_key = bin2hex( random_bytes( 16 ) );
		set_transient(
			self::STAGE_TRANSIENT_PREFIX . $stage_key,
			$stage,
			self::STAGE_TTL_SECONDS
		);

		return new WP_REST_Response(
			[
				'stage_key'  => $stage_key,
				'view_id'    => $view_id,
				'expires_at' => gmdate( 'Y-m-d\TH:i:s\Z', time() + self::STAGE_TTL_SECONDS ),
			],
			201
		);
	}

	/**
	 * DELETE /views/{id}/preview/_stage — invalidate a stage. Called
	 * by the React inspector on Save (the saved state is the new
	 * truth) and on Discard (changes were thrown away).
	 *
	 * The body MUST carry `stage_key`. Ownership is verified against
	 * the transient's stored `user_id` / `view_id` so a different
	 * user (or the same user on a different View) can't clear an
	 * unrelated stage.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response
	 */
	public function delete_preview_stage( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];
		$user_id = (int) get_current_user_id();

		$payload   = $request->get_json_params();
		$stage_key = is_array( $payload ) ? (string) ( $payload['stage_key'] ?? '' ) : '';
		$stage_key = $this->sanitize_stage_key( $stage_key );

		if ( '' !== $stage_key ) {
			$stored = get_transient( self::STAGE_TRANSIENT_PREFIX . $stage_key );
			if ( is_array( $stored )
				&& (int) ( $stored['view_id'] ?? 0 ) === $view_id
				&& (int) ( $stored['user_id'] ?? 0 ) === $user_id ) {
				delete_transient( self::STAGE_TRANSIENT_PREFIX . $stage_key );
			}
		}

		// Always 200 — DELETE is idempotent. Missing transient is
		// indistinguishable from "already cleared".
		return new WP_REST_Response(
            [
				'view_id' => $view_id,
				'cleared' => true,
			],
			200
        );
	}

	/**
	 * Coerce a stage tree to either an array or null. Anything else
	 * (scalar, object that didn't survive JSON decode) becomes null.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $tree Raw payload value.
	 *
	 * @return array|null
	 */
	private function normalize_stage_tree( $tree ): ?array {
		return is_array( $tree ) ? $tree : null;
	}

	/**
	 * Normalise a stage_key: must be 32 lowercase hex chars (matches
	 * the `bin2hex( random_bytes( 16 ) )` shape `create_preview_stage`
	 * mints). Returns the empty string for anything else, which
	 * cascades to a no-op delete / silent miss on read.
	 *
	 * @since 3.0.0
	 *
	 * @param string $value Raw value.
	 *
	 * @return string
	 */
	public static function sanitize_stage_key( string $value ): string {
		return (bool) preg_match( '/^[a-f0-9]{32}$/', $value ) ? $value : '';
	}

	/**
	 * GET /widgets — registered widget catalogue.
	 *
	 * Sourced from `Widget::registered()` so third-party widgets
	 * registered via `gravityview/widgets/register` surface here
	 * without any extra code path.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	/**
	 * GET /widget-zones — the 2 widget meta-zones.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_widget_zones( WP_REST_Request $request ): WP_REST_Response {
		return new WP_REST_Response( [ 'widget_zones' => self::WIDGET_ZONES ], 200 );
	}

	/**
	 * GET /search-zones — Search Bar internal zones, filterable.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_search_zones( WP_REST_Request $request ): WP_REST_Response {
		/**
		 * Filters the Search Bar internal zones returned by
		 * `GET /search-zones`. Add-ons that introduce additional
		 * search-bar surfaces (e.g. an "advanced" tab beyond the
		 * built-in general / advanced split) register their zone
		 * keys here so callers placing search fields can target
		 * them.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $zones Search-bar zone keys. Defaults to `['search-general', 'search-advanced']`.
		 */
		$zones = (array) apply_filters(
			'gk/gravityview/rest/search-zones/list/items',
			[ 'search-general', 'search-advanced' ]
		);
		return new WP_REST_Response( [ 'search_zones' => array_values( array_unique( $zones ) ) ], 200 );
	}

	public function get_widgets( WP_REST_Request $request ): WP_REST_Response {
		$registered = class_exists( Widget::class ) ? (array) Widget::registered() : [];
		$out        = [];

		foreach ( $registered as $widget_id => $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}
			$out[] = [
				'id'          => (string) $widget_id,
				'label'       => (string) ( $entry['label'] ?? $widget_id ),
				'description' => (string) ( $entry['description'] ?? '' ),
				'subtitle'    => (string) ( $entry['subtitle'] ?? '' ),
				'icon'        => (string) ( $entry['icon'] ?? '' ),
				'class'       => (string) ( $entry['class'] ?? '' ),
			];
		}

		return new WP_REST_Response( [ 'widgets' => $out ], 200 );
	}

	/**
	 * GET /grid/row-types — registered Layout Builder row types.
	 *
	 * Sourced from `\GV\Grid::get_row_types()` so any add-on that
	 * registers custom row layouts (3-col 25/25/50, etc.) surfaces
	 * here automatically. The `columns` array describes what the
	 * row materialises into per area position.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function get_grid_row_types( WP_REST_Request $request ): WP_REST_Response {
		$types = class_exists( '\GV\Grid' ) ? (array) \GV\Grid::get_row_types() : [];
		$out   = [];

		foreach ( $types as $type_id => $columns ) {
			if ( ! is_array( $columns ) ) {
				continue;
			}
			$column_areas = [];
			foreach ( $columns as $col_key => $areas ) {
				foreach ( (array) $areas as $area ) {
					if ( ! is_array( $area ) || empty( $area['areaid'] ) ) {
						continue;
					}
					$column_areas[] = [
						'col'    => (string) $col_key,
						'areaid' => (string) $area['areaid'],
						'label'  => (string) ( $area['title'] ?? $area['label'] ?? $area['areaid'] ),
					];
				}
			}
			$out[] = [
				'id'      => (string) $type_id,
				'columns' => $column_areas,
			];
		}

		return new WP_REST_Response( [ 'row_types' => $out ], 200 );
	}

	/**
	 * GET /field-types/{type}/schema — schema for a field type
	 * independent of any specific slot or View. Lets AI agents
	 * discover what settings a field type supports BEFORE placing
	 * a field, so they can build a complete configuration in one
	 * pass.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_field_type_schema( WP_REST_Request $request ): WP_REST_Response {
		$field_type  = (string) $request['field_type'];
		$template_id = (string) ( $request->get_param( 'template_id' ) ?: 'default_list' );
		$context     = (string) ( $request->get_param( 'context' ) ?: 'multiple' );
		$input_type  = (string) ( $request->get_param( 'input_type' ) ?: '' );
		$form_id     = (int) ( $request->get_param( 'form_id' ) ?: 0 );

		// Dispatch order is context-aware. Several type slugs have
		// implementations on more than one surface — `created_by`
		// is both an entry-meta field AND a search field; `custom`
		// is the Custom Content field but `custom_content` is the
		// widget. Without context guidance the endpoint would always
		// resolve a search field (because every well-known type slug
		// has a Search_Field subclass), masking the field/widget
		// schema customers actually want when they're placing a
		// directory column or a header widget.
		//
		// context=search                  → search_field first
		// context=multiple|single|edit    → widget → field (no search_field)
		$is_search_context = ( 'search' === $context );

		// Widget dispatch first when NOT in a search context — a
		// widget with a non-overlapping id (page_links, page_info,
		// search_bar, custom_content, etc.) routes to its widget
		// settings array.
		if ( ! $is_search_context && $this->is_registered_widget( $field_type ) ) {
			return new WP_REST_Response(
				[
					'field_type'  => $field_type,
					'kind'        => 'widget',
					'template_id' => $template_id,
					'context'     => $context,
					'schema'      => $this->compute_widget_schema( $field_type ),
				],
				200
			);
		}

		// Search Bar internal field types — only routed when the
		// caller asked for the search context, OR when there's no
		// alternative (the type only exists as a Search_Field).
		$search_field_class = $this->find_search_field_class( $field_type, $form_id );
		if ( null !== $search_field_class && ( $is_search_context || ! $this->is_registered_field( $field_type ) ) ) {
			return new WP_REST_Response(
				[
					'field_type'  => $field_type,
					'kind'        => 'search_field',
					'template_id' => $template_id,
					'context'     => $context,
					'schema'      => $this->compute_search_field_schema( $search_field_class, $form_id ),
				],
				200
			);
		}

		// `compute_slot_schema` accepts an existing slot, but the only
		// thing it actually uses from the slot is the `id`. Synthesise
		// a minimal slot stub so we can reuse the same code path.
		$slot_stub = [ 'id' => $field_type ];

		$schema = $this->compute_slot_schema(
			$template_id,
			$form_id,
			$context === 'multiple'
				? 'directory_list-title'
				: ( 'single' === $context
					? 'single_list-title'
					: ( 'edit' === $context ? 'edit_list-title' : 'directory_list-title' ) ),
			$slot_stub,
			$input_type
		);

		return new WP_REST_Response(
			[
				'field_type'  => $field_type,
				'kind'        => 'field',
				'template_id' => $template_id,
				'context'     => $context,
				'schema'      => $schema,
			],
			200
		);
	}

	/**
	 * Whether `$type` matches a registered GravityView widget. Used to
	 * gate the schema dispatch in `get_field_type_schema()`.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	private function is_registered_widget( string $type ): bool {
		if ( '' === $type || ! class_exists( Widget::class ) ) {
			return false;
		}
		$registered = Widget::registered();
		return is_array( $registered ) && isset( $registered[ $type ] );
	}

	/**
	 * Whether `$type` matches a registered GravityView field that the
	 * field-options filter chain knows about.
	 *
	 * Used by `get_field_type_schema()` to disambiguate type slugs
	 * that overlap between the field and search-field surfaces (e.g.
	 * `created_by` is both an entry-meta field AND a search field).
	 * When the type has BOTH implementations and no context hint
	 * picks one, we prefer field — that's what customers see in
	 * directory / single zones, the most common authoring path.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type Field type slug (e.g. "created_by", "custom").
	 *
	 * @return bool
	 */
	private function is_registered_field( string $type ): bool {
		if ( '' === $type || ! class_exists( '\GravityView_Fields' ) ) {
			return false;
		}
		// `\GravityView_Fields::get( $type )` returns a singleton
		// when the type is registered, null otherwise. Cheap lookup.
		$instance = \GravityView_Fields::get( $type );
		return null !== $instance;
	}

	/**
	 * Build a widget-settings schema in the same shape
	 * `compute_slot_schema()` returns for fields. Each setting becomes
	 * `{ slug, type, label, desc, value, group, priority, options? }`
	 * so AI agents can consume both schema flavours through one
	 * normalization path.
	 *
	 * @since 3.0.0
	 *
	 * @param string $widget_id Widget id (e.g. "search_bar").
	 *
	 * @return array
	 */
	/**
	 * Coerce a setting's `label` / `desc` to a string. Widget
	 * authors sometimes supply a Closure (deferred i18n) or other
	 * non-scalar; treat those as empty rather than fataling the
	 * whole schema response.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	private function stringify_setting_meta( $value ): string {
		if ( is_string( $value ) ) {
			return $value;
		}
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}
		// Closures and other non-scalars — drop quietly. The full
		// label is still discoverable through the View's
		// /field-settings-schema endpoint when the setting actually
		// gets placed on a slot.
		return '';
	}

	/**
	 * Look up a Search_Field subclass by its `$type` slug. Uses the
	 * canonical registry (`Search_Field_Collection::available_fields`)
	 * so add-on Search_Field types surface here automatically.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type    e.g. "search_all", "submit", "search_mode".
	 * @param int    $form_id Optional form id for form-scoped types.
	 *
	 * @return string|null Class name, or null when not registered.
	 */
	/**
	 * Auto-migrate a search_bar widget's settings from the legacy
	 * `search_fields` (JSON-encoded flat list) shape to the modern
	 * `search_fields_section` (PHP array keyed by `{position}`)
	 * shape. The new REST surface only emits modern; running
	 * everything through this helper guarantees existing Views with
	 * legacy data convert silently on first save.
	 *
	 * Idempotent: skips when `search_fields_section` is already
	 * populated, or when `search_fields` is empty / already an array.
	 *
	 * @since 3.0.0
	 *
	 * @param array $widget_settings The full slot record being persisted.
	 * @param int   $view_id         Owning View id (provides form context).
	 *
	 * @return array
	 */

	private function migrate_search_bar_to_modern( array $widget_settings, int $view_id ): array {
		// Already on modern shape — nothing to migrate. The legacy
		// `search_fields` JSON has NO place in fresh storage; the
		// admin metabox derives it at render time from the modern
		// section (see SearchWidget::back_compat_legacy_search_fields).
		if ( ! empty( $widget_settings['search_fields_section'] ) && is_array( $widget_settings['search_fields_section'] ) ) {
			unset( $widget_settings['search_fields'] );
			return $widget_settings;
		}

		$legacy = $widget_settings['search_fields'] ?? null;
		// Treat an absent key AND a valid-but-empty legacy array ("[]", "{}", [])
		// as "nothing to migrate" — drop the empty key and return the canonical
		// modern shape. Without this the empty case looks identical to a parse
		// failure to callers that guard on a lingering search_fields key (e.g.
		// add_search_bar), wrongly blocking the first field on an empty legacy bar.
		$legacy_decoded = is_string( $legacy ) ? json_decode( $legacy, true ) : $legacy;
		if ( empty( $legacy ) || ( is_array( $legacy_decoded ) && empty( $legacy_decoded ) ) ) {
			unset( $widget_settings['search_fields'] );
			return $widget_settings;
		}

		// Reuse SearchFieldCollection's legacy parser → collection,
		// then collection.to_configuration() = the modern shape.
		// Any failure (missing class, bad JSON) is swallowed — we
		// preserve the existing data so a partial migration doesn't
		// corrupt the customer's search bar.
		if ( ! class_exists( '\GV\Search\Search_Field_Collection' ) ) {
			return $widget_settings;
		}

		$view = class_exists( View::class ) ? View::by_id( $view_id ) : null;

		try {
			// Build the legacy-shaped config the parser expects. It
			// reads `search_fields`, `form_id`, `sieve_choices`.
			$legacy_config = $widget_settings;
			if ( is_array( $legacy ) ) {
				// Already an array — re-encode so the parser's
				// json_decode round-trips it.
				$legacy_config['search_fields'] = wp_json_encode( $legacy );
			}
			$legacy_config['form_id'] = $legacy_config['form_id'] ?? (int) $this->resolve_form_id( $view_id );

			$collection = \GV\Search\Search_Field_Collection::from_legacy_configuration( $legacy_config, $view );
			$modern     = $collection->to_configuration();
		} catch ( \Throwable $unused ) {
			return $widget_settings;
		}

		if ( empty( $modern ) ) {
			return $widget_settings;
		}

		$widget_settings['search_fields_section'] = $modern;
		// Clear the legacy key so subsequent reads can't be tempted
		// by stale data.
		unset( $widget_settings['search_fields'] );

		return $widget_settings;
	}

	private function find_search_field_class( string $type, int $form_id = 0 ): ?string {
		if ( '' === $type || ! class_exists( '\GV\Search\Search_Field_Collection' ) ) {
			return null;
		}
		try {
			$available = \GV\Search\Search_Field_Collection::available_fields( $form_id );
		} catch ( \Throwable $unused ) {
			return null;
		}
		if ( ! $available || ! is_iterable( $available ) ) {
			return null;
		}
		foreach ( $available as $field ) {
			if ( ! method_exists( $field, 'get_type' ) ) {
				continue;
			}
			if ( (string) $field->get_type() === $type ) {
				return get_class( $field );
			}
		}
		return null;
	}

	/**
	 * Build a settings schema for a Search_Field subclass in the same
	 * `{ slug, type, label, desc, value, group, priority, options? }`
	 * shape `compute_widget_schema` returns.
	 *
	 * @since 3.0.0
	 *
	 * @param string $class   Search_Field FQCN.
	 * @param int    $form_id Optional form id for instance bootstrapping.
	 *
	 * @return array
	 */
	private function compute_search_field_schema( string $class, int $form_id = 0 ): array {
		if ( ! class_exists( $class ) ) {
			return [];
		}
		try {
			/** @var \GV\Search\Fields\Search_Field $instance */
			$instance = new $class( null, [ 'form_id' => $form_id ] );
		} catch ( \Throwable $unused ) {
			return [];
		}

		// `merge_options()` is the public accessor that returns the
		// final settings catalogue exposed to the inspector UI:
		// per-class `get_options()` merged with the cross-cutting
		// `get_search_field_options()` (custom_label, show_label,
		// etc.). Both inputs are protected, so go through the
		// public method rather than reaching past the visibility.
		$options = [];
		if ( method_exists( $instance, 'merge_options' ) ) {
			$options = (array) $instance->merge_options( [] );
		}

		$out      = [];
		$priority = 100;
		foreach ( $options as $slug => $def ) {
			if ( ! is_array( $def ) ) {
				continue;
			}
			$entry = [
				'slug'     => (string) $slug,
				'type'     => (string) ( $def['type'] ?? 'text' ),
				'label'    => $this->stringify_setting_meta( $def['label'] ?? '' ),
				'desc'     => $this->stringify_setting_meta( $def['desc'] ?? '' ),
				'value'    => is_scalar( $def['value'] ?? null ) ? $def['value'] : '',
				'group'    => (string) ( $def['group'] ?? 'display' ),
				'priority' => (int) ( $def['priority'] ?? $priority ),
			];
			if ( ! empty( $def['choices'] ) && is_array( $def['choices'] ) ) {
				$entry['options'] = $def['choices'];
			} elseif ( ! empty( $def['options'] ) && is_array( $def['options'] ) ) {
				$entry['options'] = $def['options'];
			}
			$out[]     = $entry;
			$priority += 10;
		}
		return array_map( [ self::class, 'trim_schema_item' ], $out );
	}

	private function compute_widget_schema( string $widget_id ): array {
		$registered = Widget::registered();
		$entry      = $registered[ $widget_id ] ?? null;
		if ( ! is_array( $entry ) ) {
			return [];
		}

		// `Widget::registered()` returns metadata, not instances.
		// The `class` key holds the widget class FQCN; instantiate it
		// to read its settings array. Widget singletons self-register
		// from their constructors, so re-instantiating here is safe
		// (the duplicate filter add is idempotent at the WP level).
		$class = isset( $entry['class'] ) ? (string) $entry['class'] : '';
		if ( '' === $class || ! class_exists( $class ) ) {
			return [];
		}

		try {
			$widget = new $class();
		} catch ( \Throwable $unused ) {
			return [];
		}

		if ( ! method_exists( $widget, 'get_settings' ) ) {
			return [];
		}

		$settings = (array) ( $widget->get_settings() ?: [] );

		// Widgets that compose real settings via render-time
		// filters (e.g. SearchWidget's per-area / per-context
		// settings) self-declare them via
		// `Widget::get_inspector_schema_extras()`. The base
		// implementation returns `[]` so widgets that already
		// declare everything in their constructor's $settings
		// array get a no-op merge.
		if ( method_exists( $widget, 'get_inspector_schema_extras' ) ) {
			$extras   = (array) $widget->get_inspector_schema_extras();
			$settings = array_merge( $settings, $extras );
		}

		/**
		 * Filters the resolved settings catalogue for a widget before
		 * it ships to the inspector schema endpoint. Final extension
		 * hook for third-party widgets that want to inject schema
		 * entries cross-cutting — most widgets should prefer
		 * overriding `Widget::get_inspector_schema_extras()` on the
		 * widget class itself; this filter exists for add-ons that
		 * need to layer settings onto a widget they don't own.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $settings  Settings keyed by slug (slug → definition array).
		 * @param string $widget_id Widget id (e.g. "search_bar").
		 * @param object $widget    Widget instance.
		 */
		// Phase 4i hook rename + dual-fire compat alias.
		$settings = (array) apply_filters(
			'gk/gravityview/rest/widget-settings-schema/build/extras',
			$settings,
			$widget_id,
			$widget
		);
		$settings = (array) apply_filters_deprecated(
			'gk/gravityview/rest/widget-schema-extras',
			[ $settings, $widget_id, $widget ],
			'3.1.0',
			'gk/gravityview/rest/widget-settings-schema/build/extras'
		);

		$out      = [];
		$priority = 100;

		foreach ( $settings as $slug => $def ) {
			if ( ! is_array( $def ) ) {
				continue;
			}
			// Some widget settings carry Closures (e.g. lazy
			// description builders) or other non-scalar callables in
			// `label` / `desc`. Cast safely so one weird setting
			// doesn't 500 the whole schema response.
			$entry = [
				'slug'     => (string) $slug,
				'type'     => (string) ( $def['type'] ?? 'text' ),
				'label'    => $this->stringify_setting_meta( $def['label'] ?? '' ),
				'desc'     => $this->stringify_setting_meta( $def['desc'] ?? '' ),
				'value'    => is_scalar( $def['value'] ?? null ) ? $def['value'] : '',
				'group'    => (string) ( $def['group'] ?? 'display' ),
				'priority' => (int) ( $def['priority'] ?? $priority ),
			];

			// Carry forward the choice/option list when the input is a
			// constrained type. Standardise on `options` keyed by
			// value so consumers don't have to handle `choices` /
			// `options` polymorphism.
			if ( ! empty( $def['choices'] ) && is_array( $def['choices'] ) ) {
				$entry['options'] = $def['choices'];
			} elseif ( ! empty( $def['options'] ) && is_array( $def['options'] ) ) {
				$entry['options'] = $def['options'];
			}

			$out[]     = $entry;
			$priority += 10;
		}

		return array_map( [ self::class, 'trim_schema_item' ], $out );
	}

	/**
	 * GET /views/{id}/available-fields — field picker data.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response
	 */
	public function get_available_fields( WP_REST_Request $request ): WP_REST_Response {
		$view_id = (int) $request['id'];
		$form_id = (int) $this->resolve_form_id( $view_id );
		$zone    = $request->get_param( 'zone' ) ? (string) $request->get_param( 'zone' ) : 'directory';
		$context = 'directory' === $zone ? 'multiple' : $zone;

		// GravityView fields registered for the requested zone. The
		// class might not be loaded yet on a stripped-down install
		// (REST routes register early and `GravityView_Fields` lives
		// behind the legacy autoloader); fall back to an empty list
		// rather than fataling the entire response.
		$gv_fields = class_exists( '\GravityView_Fields' )
			? (array) \GravityView_Fields::get_all( '', $context )
			: [];

		// Inline normaliser shared between the primary form and any
		// joined forms (Multiple Forms add-on). Tags every entry with
		// its source `form_id` so callers can disambiguate when the
		// same numeric field id (`"1"`) exists across forms.
		$collect_form_fields = static function ( int $source_form_id ): array {
			$out = [];
			if ( $source_form_id <= 0 || ! class_exists( 'GFAPI' ) ) {
				return $out;
			}
			$form = \GFAPI::get_form( $source_form_id );
			if ( ! $form || empty( $form['fields'] ) ) {
				return $out;
			}
			foreach ( $form['fields'] as $gf_field ) {
				$out[] = [
					'id'         => (string) $gf_field->id,
					'label'      => (string) $gf_field->label,
					'input_type' => (string) $gf_field->get_input_type(),
					'type'       => (string) $gf_field->type,
					'form_id'    => $source_form_id,
				];
			}
			return $out;
		};

		// Gravity Forms fields from the View's primary form.
		$gf_fields = $collect_form_fields( $form_id );

		// Joined-form fields when the View has any active joins (parity
		// with the legacy admin field-picker which already iterates
		// `View::get_joined_forms()` for the same purpose). Empty
		// when the Multiple Forms add-on isn't active or the View has
		// no joins. Returned as a separate `joined_form_fields` list
		// (vs. mixed into `form_fields`) so callers can render group
		// headers per source form without re-grouping by `form_id`.
		$joined_form_fields = [];
		if ( $view_id > 0 && method_exists( View::class, 'get_joined_forms' ) ) {
			$joined_forms = View::get_joined_forms( $view_id );
			if ( is_array( $joined_forms ) ) {
				foreach ( $joined_forms as $joined_form ) {
					$joined_form_id = is_object( $joined_form ) && isset( $joined_form->ID ) ? (int) $joined_form->ID : 0;
					if ( $joined_form_id <= 0 || $joined_form_id === $form_id ) {
						continue;
					}
					foreach ( $collect_form_fields( $joined_form_id ) as $field ) {
						$joined_form_fields[] = $field;
					}
				}
			}
		}

		// GV registered fields normalized for the picker UI.
		$normalized_gv = [];
		foreach ( $gv_fields as $gv_field ) {
			if ( ! is_object( $gv_field ) ) {
				continue;
			}
			$normalized_gv[] = [
				'id'    => (string) ( $gv_field->name ?? '' ),
				'label' => (string) ( $gv_field->label ?? '' ),
				'group' => (string) ( $gv_field->group ?? 'gravityview' ),
				'icon'  => (string) ( $gv_field->icon ?? '' ),
			];
		}

		return new WP_REST_Response(
			[
				'view_id'            => $view_id,
				'form_id'            => $form_id,
				'context'            => $context,
				'form_fields'        => $gf_fields,
				'joined_form_fields' => $joined_form_fields,
				'gv_fields'          => $normalized_gv,
			],
			200
		);
	}

	/**
	 * Hard cap on the number of form IDs accepted by {@see self::get_merge_tag_data()}.
	 *
	 * Prevents a malicious or buggy client from forcing the server to load and
	 * merge an unbounded number of forms in a single request.
	 *
	 * @since 3.0.0
	 */
	const MAX_MERGE_TAG_FORM_IDS = 50;

	/**
	 * Build the merge-tag dataset the View editor's Gravity Forms merge-tag UI
	 * reads when populating its dropdowns.
	 *
	 * Bound to the `gk-gravityview/merge-tag-data-get` ability. The View editor
	 * calls this whenever the Data Source form (or the set of joined forms from
	 * the Multiple Forms add-on) changes, so the dropdowns can refresh without
	 * the user having to save the View first.
	 *
	 * The primary form's id / title / fields drive the `form` portion of the
	 * response (matching the shape of GF's `window.form` global). When multiple
	 * form IDs are supplied — i.e. for joined-form Views — fields from the
	 * additional forms are appended to the primary's field list so every field
	 * across every connected form surfaces as a merge tag.
	 *
	 * Field-level data is sourced from `gravityview_get_form()` (Gravity Forms'
	 * canonical, server-side form definition); merge-tag labels come from
	 * `GFCommon::get_merge_tags()`.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request with a `form_ids` array param.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_merge_tag_data( WP_REST_Request $request ) {
		$raw_ids = $request->get_param( 'form_ids' );

		if ( ! is_array( $raw_ids ) ) {
			$raw_ids = [];
		}

		// Keep only positive-integer scalar IDs. Non-scalars (arrays, objects)
		// and zero / negative values are discarded. `absint()` is intentionally
		// NOT used here so that `-5` doesn't silently become `5`.
		$form_ids = array_values(
			array_unique(
				array_filter(
					array_map(
						static function ( $id ) {
							return is_scalar( $id ) ? (int) $id : 0;
						},
						$raw_ids
					),
					static function ( $id ) {
						return $id > 0;
					}
				)
			)
		);

		if ( count( $form_ids ) > self::MAX_MERGE_TAG_FORM_IDS ) {
			$form_ids = array_slice( $form_ids, 0, self::MAX_MERGE_TAG_FORM_IDS );
		}

		if ( empty( $form_ids ) ) {
			return new WP_REST_Response(
				[
					'form'       => null,
					'merge_tags' => (object) [],
				],
				200
			);
		}

		$primary_form_id = (int) reset( $form_ids );
		$primary_form    = \gravityview_get_form( $primary_form_id );

		if ( ! $primary_form ) {
			return new WP_Error(
				'gv_rest_form_not_found',
				sprintf(
					/* translators: %d is a Gravity Forms form id */
					__( 'No Gravity Forms form was found with id %d.', 'gk-gravityview' ),
					$primary_form_id
				),
				[ 'status' => 404 ]
			);
		}

		$merged_fields = isset( $primary_form['fields'] ) && is_array( $primary_form['fields'] )
			? array_values( $primary_form['fields'] )
			: [];

		foreach ( $form_ids as $form_id ) {
			$form_id = (int) $form_id;

			if ( $form_id === $primary_form_id ) {
				continue;
			}

			$joined_form = \gravityview_get_form( $form_id );

			if ( ! $joined_form || empty( $joined_form['fields'] ) || ! is_array( $joined_form['fields'] ) ) {
				continue;
			}

			foreach ( $joined_form['fields'] as $field ) {
				$merged_fields[] = $field;
			}
		}

		return new WP_REST_Response(
			[
				'form'       => [
					'id'     => (int) $primary_form['id'],
					'title'  => isset( $primary_form['title'] ) ? (string) $primary_form['title'] : '',
					'fields' => $merged_fields,
				],
				'merge_tags' => \GFCommon::get_merge_tags( $merged_fields, '', false ),
			],
			200
		);
	}

	/**
	 * GET / POST /views/{id}/fields/{area}/{slot}/render — rendered HTML
	 * for iframe swap.
	 *
	 * Accepts an optional `settings` override in the POST body so the
	 * React store can preview staged-but-unsaved edits without
	 * persisting them first. Without overrides we render the slot as
	 * it currently sits in `_gravityview_directory_fields`.
	 *
	 * The receiver swaps the returned HTML via `outerHTML`, so the
	 * markup must include the layout's natural wrapper element
	 * (`<td>` for table, `<div>` for list/grid). Any `data-gv-*`
	 * markers an inspector needs for click-targeting are stamped by
	 * a separate filter on `gravityview/field_output/html`.
	 *
	 * Layouts handled today:
	 *   - Table (`directory_table-columns` / `single_table-columns`).
	 *     Returns a `<td>` wrapper matching `Table::the_field()`.
	 *   - List layouts (`directory_list-*` / `single_list-*`).
	 *     Returns a `<div>` wrapper — close enough to the layout's
	 *     own markup that the receiver's outerHTML swap doesn't
	 *     disrupt the flow.
	 *
	 * Layouts not yet supported (Layout Builder rows, DataTables,
	 * Maps) fall through to a 501 so the parent will fall back to a
	 * full iframe reload.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function render_field( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];
		$area    = (string) $request['area'];
		$slot    = (string) $request['slot'];

		$payload = $request->get_json_params();
		$payload = is_array( $payload ) ? $payload : [];

		// `staged_slot` lets the React store render a brand-new slot
		// the user just dragged in BEFORE issuing the apply that
		// would persist it. Without this, the preview can only
		// reflect saved placements — every new drag forced a stale
		// "Save to preview" affordance. Shape mirrors the create-field
		// payload: `{ field_id, label?, ...settings }`.
		//
		// Sanitization uses the same schema-aware path the apply
		// pipeline runs (so HTML in `content` survives, text settings
		// strip tags, conditional_logic is validated, etc.) — the
		// staged render must never honor a setting the persist path
		// would silently sanitize away, or the preview would lie.
		$staged_slot = ( isset( $payload['staged_slot'] ) && is_array( $payload['staged_slot'] ) )
			? $payload['staged_slot']
			: [];

		$slot_record = $this->read_slot( $view_id, $area, $slot );

		// Lazy locals shared by both staged-slot synthesis and the
		// settings-overrides loop. Compute once even when both
		// branches fire — the previous version did the
		// resolve_template_id + resolve_form_id + compute_slot_schema
		// dance twice on every drag-preview render.
		$template_id    = null;
		$form_id        = null;
		$slot_schema    = null;
		$resolve_schema = function ( array $current_record ) use ( $view_id, $area, &$template_id, &$form_id, &$slot_schema ) {
			if ( null === $slot_schema ) {
				$template_id = $this->resolve_template_id( $view_id );
				$form_id     = $this->resolve_form_id( $view_id );
				$slot_schema = $this->compute_slot_schema( $template_id, $form_id, $area, $current_record, '', $view_id );
			}
			return $slot_schema;
		};

		if ( null === $slot_record ) {
			if ( empty( $staged_slot ) || empty( $staged_slot['field_id'] ) ) {
				return new WP_Error( 'gv_rest_slot_not_found', __( 'Slot not found. Pass `staged_slot: { field_id, ...settings }` in the POST body to render an unsaved slot.', 'gk-gravityview' ), [ 'status' => 404 ] );
			}

			$slot_record          = [
				'id'    => (string) $staged_slot['field_id'],
				'label' => isset( $staged_slot['label'] ) ? (string) $staged_slot['label'] : '',
			];
			$schema_for_synthesis = $resolve_schema( $slot_record );
			foreach ( $staged_slot as $key => $value ) {
				$key = sanitize_key( $key );
				if ( '' === $key || in_array( $key, [ 'field_id', 'id', 'label', 'slot' ], true ) ) {
					continue;
				}
				if ( null === $value ) {
					continue;
				}
				if ( 'conditional_logic' === $key ) {
					$cl                  = $this->validate_conditional_logic( $value );
					$slot_record[ $key ] = $cl['value'];
					continue;
				}
				$mode                = $this->sanitize_mode_for( $schema_for_synthesis, $key );
				$slot_record[ $key ] = $this->sanitize_setting_value( $value, $mode );
			}
		}

		// Merge any caller-supplied overrides into the (possibly
		// synthesized) slot's settings. POST body comes through as
		// JSON; GET callers get the as-stored slot.
		$overrides = ( isset( $payload['settings'] ) && is_array( $payload['settings'] ) )
			? $payload['settings']
			: [];
		if ( ! empty( $overrides ) ) {
			$schema_for_overrides = $resolve_schema( $slot_record );
			foreach ( $overrides as $key => $value ) {
				$key = sanitize_key( $key );
				if ( '' === $key ) {
					continue;
				}
				if ( null === $value ) {
					unset( $slot_record[ $key ] );
					continue;
				}
				$mode                = $this->sanitize_mode_for( $schema_for_overrides, $key );
				$slot_record[ $key ] = $this->sanitize_setting_value( $value, $mode );
			}
		}

		// Resolve View + form.
		if ( ! class_exists( View::class ) ) {
			return new WP_Error( 'gv_rest_render_unavailable', __( 'View renderer not available.', 'gk-gravityview' ), [ 'status' => 503 ] );
		}
		$view = View::by_id( $view_id );
		if ( ! $view ) {
			return new WP_Error( 'gv_rest_view_not_found', __( 'View not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		// Pick a representative entry — the most-recent published one
		// for this View's form. Falls back to a synthetic placeholder
		// payload when the form has no entries yet (new install / form
		// just connected). Synthetic shape matches what the frontend
		// renderer expects so the rendered cell looks like real
		// production output.
		$entry = $this->resolve_sample_entry( $view );
		if ( ! $entry ) {
			return new WP_Error( 'gv_rest_no_entry', __( 'No entry available to render against.', 'gk-gravityview' ), [ 'status' => 503 ] );
		}

		$form = $view->form ? $view->form->form : null;
		if ( ! $form ) {
			return new WP_Error( 'gv_rest_no_form', __( 'View has no form attached.', 'gk-gravityview' ), [ 'status' => 503 ] );
		}

		// Pick the markup template matching this layout's wrapper.
		$wrapper = $this->wrapper_markup_for_area( $area );
		if ( null === $wrapper ) {
			return new WP_Error(
				'gv_rest_render_unsupported_layout',
				__( 'This layout does not support per-slot live render yet; the iframe will fall back to a full reload on Save.', 'gk-gravityview' ),
				[ 'status' => 501 ]
			);
		}

		// Build the field-config array the production renderer
		// receives. Slot record carries `id`, `label`, plus every
		// configured setting — that's the same shape
		// `View::fields` -> `Field::from_configuration` reads.
		// `UID` is the marker key any inspector-side click-target
		// filter looks up; without it the receiver can't match the
		// new cell to the slot the customer just edited.
		$field_config        = $slot_record;
		$field_config['UID'] = $slot;
		$field_config['uid'] = $slot;

		// Render the field's actual VALUE through the production
		// renderer chain — `gravityview_field_output()` substitutes
		// `{{ value }}` with whatever we pass, but doesn't itself
		// resolve the entry's field value. That's the FieldRenderer's
		// job (it walks the field-type-specific render pipeline,
		// applies merge tags, link wrapping, date formatting, etc.).
		$rendered_value = '';
		if ( class_exists( '\GV\Field' ) && $view->form ) {
			$gv_field = \GV\Field::from_configuration( $field_config );
			if ( $gv_field && class_exists( '\GravityKit\GravityView\Renderer\FieldRenderer' ) ) {
				$entry_obj = null;
				if ( class_exists( '\GravityKit\GravityView\Entry\EntryGravityForms' ) ) {
					$entry_obj = \GravityKit\GravityView\Entry\EntryGravityForms::from_entry( $entry );
				}
				if ( $entry_obj ) {
					$renderer       = new \GravityKit\GravityView\Renderer\FieldRenderer();
					$source         = is_numeric( $gv_field->ID ) ? $view->form : null;
					$rendered_value = (string) $renderer->render(
						$gv_field,
						$view,
						$source,
						$entry_obj,
						gravityview()->request
					);
				}
			}
		}

		$args = [
			'entry'      => $entry,
			'field'      => $field_config,
			'form'       => $form,
			'value'      => $rendered_value,
			'hide_empty' => false,
			'zone_id'    => $area,
			'label'      => isset( $slot_record['custom_label'] ) && '' !== (string) $slot_record['custom_label']
				? (string) $slot_record['custom_label']
				: (string) ( $slot_record['label'] ?? '' ),
			'markup'     => $wrapper,
		];

		// `gravityview_field_output()` switches between two value
		// resolution paths based on whether a `Template_Context` is
		// passed: with a context it uses `$args['value']` verbatim
		// (our pre-rendered HTML); without one it falls back to
		// legacy `gv_value( $entry, $field )` which doesn't see our
		// FieldRenderer output. Build a minimal context so the
		// rendered-value branch wins.
		$ctx_data = [
			'view'    => $view,
			'source'  => $view->form,
			'field'   => isset( $gv_field ) && $gv_field ? $gv_field : null,
			'entry'   => isset( $entry_obj ) && $entry_obj ? $entry_obj : null,
			'request' => gravityview()->request,
		];
		$context  = \GravityKit\GravityView\Template\TemplateContext::from_template( $ctx_data, [ 'value' => $rendered_value ] );

		$html = (string) gravityview_field_output( $args, $context );

		if ( '' === $html ) {
			return new WP_Error( 'gv_rest_render_empty', __( 'Renderer returned an empty cell.', 'gk-gravityview' ), [ 'status' => 500 ] );
		}

		return new WP_REST_Response(
			[
				'view_id' => $view_id,
				'area'    => $area,
				'slot'    => $slot,
				'html'    => $html,
				'version' => $this->compute_version( $view_id ),
			],
			200
		);
	}

	/**
	 * Pick the layout's wrapper markup template for a given area.
	 *
	 * Returns `null` for layouts we don't yet support per-slot
	 * rendering for — caller surfaces a 501 in that case.
	 *
	 * @since 3.0.0
	 *
	 * @param string $area Storage area key (e.g. `directory_table-columns`).
	 *
	 * @return string|null
	 */
	private function wrapper_markup_for_area( string $area ): ?string {
		// Table layouts — `<td>` matching `Table::the_field()`.
		if ( false !== strpos( $area, 'table-columns' ) ) {
			return '<td id="{{ field_id }}" class="{{ class }}"{{ style_attr }} data-label="{{label_value:data-label}}">{{ value }}</td>';
		}

		// List layouts — title / subtitle / image / description /
		// other. Each registers its own `markup` in the legacy
		// `class-gravityview-list.php`; the generic `<div>` here is
		// close enough that the outer `.gv-list-view-` wrapping in
		// the surrounding row keeps the visual structure intact.
		if ( false !== strpos( $area, 'list-' ) ) {
			return '<div id="{{ field_id }}" class="{{ class }}"{{ style_attr }}>{{ label }}{{ value }}</div>';
		}

		return null;
	}

	/**
	 * Resolve a representative entry for live-preview rendering.
	 *
	 * Prefers a real entry from the View's form so format / merge-tag
	 * output reflects the customer's actual data. Falls back to a
	 * synthetic entry array when the form has no entries yet — minimal
	 * stub keyed on the form's field ids so `gravityview_field_output()`
	 * has something to render against.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View $view View instance.
	 *
	 * @return array|null GF-format entry array, or null if neither.
	 *                    real nor synthetic data is available.
	 */
	private function resolve_sample_entry( \GV\View $view ): ?array {
		$form_id = (int) $view->form->ID;
		if ( $form_id <= 0 ) {
			return null;
		}

		// Real-entry path: GFAPI is universally available in admin
		// REST handlers (Gravity Forms is a hard plugin dependency).
		if ( class_exists( '\GFAPI' ) ) {
			$entries = \GFAPI::get_entries(
				$form_id,
				[ 'status' => 'active' ],
				[
					'key'       => 'date_created',
					'direction' => 'DESC',
				],
				[
					'offset'    => 0,
					'page_size' => 1,
				]
			);
			if ( is_array( $entries ) && ! empty( $entries ) && is_array( $entries[0] ) ) {
				return $entries[0];
			}
		}

		// Synthetic fallback when there are no real entries yet:
		// construct a minimal entry stub keyed on the form's field
		// ids — gravityview_field_output() tolerates missing values
		// (returns empty string), so the preview just shows a
		// structurally-correct empty cell.
		if ( class_exists( '\GFAPI' ) ) {
			$form = \GFAPI::get_form( $form_id );
			if ( is_array( $form ) ) {
				$stub = [
					'id'           => 0,
					'form_id'      => (string) $form_id,
					'date_created' => current_time( 'mysql' ),
					'status'       => 'active',
				];
				foreach ( (array) ( $form['fields'] ?? [] ) as $f ) {
					if ( isset( $f->id ) ) {
						$stub[ (string) $f->id ] = '';
					}
				}
				return $stub;
			}
		}

		return null;
	}

	// -------------------------------------------------------------------
	// Writes
	// -------------------------------------------------------------------

	/**
	 * POST /views — create a draft View, optionally seed in one shot.
	 *
	 * Required: title + form_id. Optional: template_id (defaults to
	 * default_list), status (defaults to draft), and the same
	 * template_settings / search_criteria / fields / widgets / mode
	 * payload that POST /views/{id}/config/_apply accepts.
	 *
	 * Returns 201 with the same envelope GET /views/{id}/config emits
	 * (plus the apply pipeline's `applied` summary when seed data was
	 * provided), so AI clients never need a follow-up GET.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_view( WP_REST_Request $request ) {
		$title   = trim( (string) $request->get_param( 'title' ) );
		$form_id = (int) $request->get_param( 'form_id' );
		// Default to Layout Builder — it ships with one full-width row
		// and lets AI agents add/move grid rows via /views/{id}/grid/_rows
		// without needing to know which fixed-area template to pick.
		$template_id = (string) ( $request->get_param( 'template_id' ) ?: 'gravityview-layout-builder' );
		$status      = (string) ( $request->get_param( 'status' ) ?: 'draft' );

		if ( '' === $title ) {
			return new WP_Error( 'gv_rest_invalid_title', __( 'A non-empty title is required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		if ( $form_id <= 0 ) {
			return new WP_Error( 'gv_rest_invalid_form', __( 'A valid Gravity Forms form_id is required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// Verify the form exists. GFAPI handles missing forms by
		// returning false / a WP_Error depending on version, so guard
		// both shapes.
		if ( class_exists( '\GFAPI' ) ) {
			$form = \GFAPI::get_form( $form_id );
			if ( ! $form || is_wp_error( $form ) ) {
				return new WP_Error(
					'gv_rest_form_not_found',
					/* translators: %d: Gravity Forms form id */
					sprintf( __( 'Gravity Forms form %d not found.', 'gk-gravityview' ), $form_id ),
					[ 'status' => 404 ]
				);
			}
		}

		// Validate template_id against the registry so a typo doesn't
		// silently land an unrenderable View. Same source the /layouts
		// endpoint reads — see validate_template_id_or_error().
		$invalid = $this->validate_template_id_or_error( $template_id, 'template_id' );
		if ( $invalid instanceof \WP_Error ) {
			return $invalid;
		}

		// Seed the timestamp columns explicitly. `wp_insert_post`
		// occasionally leaves `post_modified_gmt` as `0000-00-00
		// 00:00:00` for draft inserts — `compute_version()` would then
		// fall back to its sentinel timestamp instead of reflecting
		// when the View was actually created. Setting the columns up
		// front means the very first version string the client sees
		// already carries a real timestamp.
		$now_local = current_time( 'mysql', false );
		$now_gmt   = current_time( 'mysql', true );
		$post_id   = wp_insert_post(
			[
				'post_type'         => View::POST_TYPE,
				'post_status'       => $status,
				'post_title'        => $title,
				'post_date'         => $now_local,
				'post_date_gmt'     => $now_gmt,
				'post_modified'     => $now_local,
				'post_modified_gmt' => $now_gmt,
			],
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return new WP_Error(
				'gv_rest_create_failed',
				$post_id->get_error_message(),
				[ 'status' => 500 ]
			);
		}

		// Seed the form + template assignments. The rest of GravityView
		// reads these meta keys (see `resolve_form_id` /
		// `resolve_template_id`) for everything from rendering to the
		// admin metabox UI.
		update_post_meta( $post_id, self::META_FORM_ID, $form_id );
		update_post_meta( $post_id, self::META_DIRECTORY_TEMPLATE, $template_id );

		// Optional per-zone template overrides. The Single zone has
		// its own meta; Edit follows directory unless the legacy
		// fallback is replaced. Validate each before writing — same
		// rule as the directory template above.
		$template_ids_payload = (array) ( $request->get_param( 'template_ids' ) ?: [] );
		foreach ( [ 'single', 'edit' ] as $zone ) {
			if ( empty( $template_ids_payload[ $zone ] ) ) {
				continue;
			}
			$zone_template = (string) $template_ids_payload[ $zone ];
			$invalid       = $this->validate_template_id_or_error( $zone_template, "template_ids.{$zone}" );
			if ( $invalid instanceof \WP_Error ) {
				// Best-effort rollback of the post we just inserted —
				// the View has no fields yet, so a hard delete leaves
				// no orphaned meta. Any failure here is logged through
				// wp_delete_post's normal hooks.
				wp_delete_post( $post_id, true );
				return $invalid;
			}
			update_post_meta( $post_id, $this->template_meta_key_for_zone( $zone ), $zone_template );
		}

		// Persist the template id inside template_settings as well so
		// `apply_config` (called below) sees a consistent baseline. The
		// settings array is the canonical source for everything except
		// the meta-key shorthand above.
		update_post_meta(
			$post_id,
			self::META_TEMPLATE_SETTINGS,
			[ 'template' => $template_id ]
		);

		// If the caller supplied any seed payload, chain straight into
		// apply_config so a single round-trip yields a fully-built View.
		// Mutate the route param so apply_config sees the new id, and
		// strip our own create-only params so the apply doesn't try to
		// reapply the template (already set above).
		$has_seed = (bool) (
			$request->get_param( 'fields' ) ||
			$request->get_param( 'widgets' ) ||
			$request->get_param( 'template_settings' ) ||
			$request->get_param( 'search_criteria' )
		);

		if ( $has_seed ) {
			$request->set_param( 'id', $post_id );
			$apply_response = $this->apply_config( $request );
			if ( is_wp_error( $apply_response ) ) {
				// Roll back the partial create so a half-built View
				// doesn't accumulate. Force-delete because draft trash
				// would still leave the post resolvable.
				wp_delete_post( $post_id, true );
				return $apply_response;
			}

			$body              = $apply_response->get_data();
			$body['view_id']   = (int) $post_id;
			$body['created']   = true;
			$body['admin_url'] = get_edit_post_link( $post_id, 'raw' );
			$response          = new WP_REST_Response( $body, 201 );
			$response->header( 'Location', rest_url( Core::get_namespace() . '/views/' . $post_id . '/config' ) );
			$response->header( 'ETag', '"' . $this->compute_version( $post_id ) . '"' );
			return $response;
		}

		// No seed payload — return the freshly-created config straight
		// from get_config so the response shape stays identical to the
		// seeded path.
		$request->set_param( 'id', $post_id );
		$config_response   = $this->get_config( $request );
		$body              = $config_response->get_data();
		$body['view_id']   = (int) $post_id;
		$body['created']   = true;
		$body['admin_url'] = get_edit_post_link( $post_id, 'raw' );
		$response          = new WP_REST_Response( $body, 201 );
		$response->header( 'Location', rest_url( Core::get_namespace() . '/views/' . $post_id . '/config' ) );
		$response->header( 'ETag', '"' . $this->compute_version( $post_id ) . '"' );
		return $response;
	}

	// ===================================================================
	// Grid (Layout Builder) row CRUD
	// ===================================================================

	/**
	 * POST /views/{id}/grid/_rows
	 *
	 * Adds a Layout Builder grid row by writing empty placeholder
	 * area entries into the field tree. The Layout Builder template
	 * infers rows from the field tree (`Grid::get_rows_from_collection`),
	 * so creating empty area keys with a fresh row_uid materialises a
	 * row the inspector + frontend will render.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_grid_row( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$type    = (string) ( $request->get_param( 'type' ) ?: '100' );
		$surface = $this->resolve_grid_surface( $view_id, (string) ( $request->get_param( 'surface' ) ?: 'fields' ) );
		if ( is_wp_error( $surface ) ) {
			return $surface;
		}

		$row_template = Grid::get_row_type( $type );
		if ( null === $row_template ) {
			return new WP_Error(
				'gv_rest_invalid_grid_type',
				/* translators: %s: requested row type */
				sprintf( __( 'Unknown grid row type "%s". Use GET /grid/row-types for the live list.', 'gk-gravityview' ), $type ),
				[ 'status' => 400 ]
			);
		}

		$zones_raw = $request->get_param( 'zones' );
		$zones     = is_array( $zones_raw ) && $zones_raw ? $zones_raw : $surface['default_zones'];

		$row_uid = Grid::uid();
		$tree    = ( $surface['read'] )( $view_id );
		$created = [];
		$skipped = [];

		foreach ( $zones as $zone ) {
			if ( ! is_string( $zone ) || '' === $zone ) {
				continue;
			}
			if ( ! in_array( $zone, $surface['valid_zones'], true ) ) {
				$skipped[ $zone ] = 'invalid-zone-for-surface';
				continue;
			}
			$prefix = ( $surface['prefix'] )( $view_id, $zone );
			if ( null === $prefix ) {
				$skipped[ $zone ] = 'zone-template-not-grid-aware';
				continue;
			}
			$zone_areaids     = Grid::area_keys_for_row( $prefix, $row_template, $type, $row_uid );
			$created[ $zone ] = [];
			foreach ( $zone_areaids as $areaid ) {
				$key = $zone . '_' . $areaid;
				if ( ! isset( $tree[ $key ] ) ) {
					$tree[ $key ] = [];
				}
				$created[ $zone ][] = $areaid;
			}
		}

		if ( empty( $created ) ) {
			return new WP_Error(
				'gv_rest_grid_unsupported',
				__( 'None of the requested zones support grid rows on this surface. For surface=fields, switch a zone to gravityview-layout-builder first via PATCH /views/{id}/template { template_id, zone }.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		( $surface['write'] )( $view_id, $tree );
		$this->bump_version( $view_id );

		// `area_keys` is the ready-to-use list of fully-qualified area
		// keys ({zone}_{areaid}) — pass these straight to apply_config
		// or add_view_field. The legacy `created` map (keyed by zone)
		// stays for callers that want to inspect per-zone results, but
		// composing the area key from `created` is error-prone (the
		// missing-prefix bug surfaces as silent writes to phantom
		// areas).
		$area_keys = [];
		foreach ( $created as $zone => $areaids ) {
			foreach ( $areaids as $areaid ) {
				$area_keys[] = "{$zone}_{$areaid}";
			}
		}

		$response = new WP_REST_Response(
			[
				'view_id'   => $view_id,
				'surface'   => $surface['id'],
				'row_uid'   => $row_uid,
				'type'      => $type,
				'area_keys' => $area_keys,
				'created'   => $created,
				// skipped is a zone => reason map (declared an object). When nothing
				// was skipped it is empty; cast so it serializes as {} not [].
				'skipped'   => empty( $skipped ) ? (object) array() : $skipped,
				'version'   => $this->compute_version( $view_id ),
			],
			201
		);
		$response->header( 'ETag', '"' . $this->compute_version( $view_id ) . '"' );
		return $response;
	}

	/**
	 * PATCH /views/{id}/grid/_rows/{row_uid}
	 *
	 * Re-keys every field in the row from `::{old_type}::{row_uid}` to
	 * `::{new_type}::{row_uid}`. When the new type has fewer columns,
	 * surplus fields collapse into the first column of the new row so
	 * nothing silently disappears.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function patch_grid_row( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];
		$row_uid = (string) $request['row_uid'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$new_type = (string) $request->get_param( 'type' );
		if ( '' === $new_type ) {
			return new WP_Error( 'gv_rest_missing_type', __( 'type is required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$row_template = Grid::get_row_type( $new_type );
		if ( null === $row_template ) {
			return new WP_Error(
				'gv_rest_invalid_grid_type',
				/* translators: %s: requested row type */
				sprintf( __( 'Unknown grid row type "%s".', 'gk-gravityview' ), $new_type ),
				[ 'status' => 400 ]
			);
		}

		$surface = $this->resolve_grid_surface( $view_id, (string) ( $request->get_param( 'surface' ) ?: 'fields' ) );
		if ( is_wp_error( $surface ) ) {
			return $surface;
		}

		$tree    = ( $surface['read'] )( $view_id );
		$next    = [];
		$touched = 0;

		// Re-key every entry whose key carries `::{row_uid}` to the
		// new row type. Surface-aware: prefix is resolved per zone
		// from the active surface (e.g. fields' per-zone template,
		// widgets' empty prefix).
		foreach ( $tree as $key => $slots ) {
			if ( false === strpos( $key, '::' . $row_uid ) ) {
				$next[ $key ] = $slots;
				continue;
			}
			++$touched;
			$zone   = explode( '_', $key, 2 )[0] ?? '';
			$prefix = ( $surface['prefix'] )( $view_id, $zone );
			if ( null === $prefix ) {
				// Zone no longer supports the grid — preserve the
				// existing key so we don't strand fields.
				$next[ $key ] = $slots;
				continue;
			}
			$zone_areaids = Grid::area_keys_for_row( $prefix, $row_template, $new_type, $row_uid );
			if ( empty( $zone_areaids ) ) {
				$next[ $key ] = $slots;
				continue;
			}
			$col_index     = Grid::column_index_from_key( $key, $prefix );
			$target_idx    = min( $col_index, count( $zone_areaids ) - 1 );
			$target_areaid = $zone_areaids[ $target_idx ];
			$target_key    = $zone . '_' . $target_areaid;

			if ( ! isset( $next[ $target_key ] ) ) {
				$next[ $target_key ] = [];
			}
			foreach ( (array) $slots as $slot_uid => $slot ) {
				$next[ $target_key ][ $slot_uid ] = $slot;
			}
		}

		if ( 0 === $touched ) {
			return new WP_Error( 'gv_rest_grid_row_not_found', __( 'Row UID not found in the View configuration.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		( $surface['write'] )( $view_id, $next );
		$this->bump_version( $view_id );

		$response = new WP_REST_Response(
			[
				'view_id' => $view_id,
				'surface' => $surface['id'],
				'row_uid' => $row_uid,
				'type'    => $new_type,
				'touched' => $touched,
				'version' => $this->compute_version( $view_id ),
			],
			200
		);
		$response->header( 'ETag', '"' . $this->compute_version( $view_id ) . '"' );
		return $response;
	}

	/**
	 * DELETE /views/{id}/grid/_rows/{row_uid}
	 *
	 * Removes every field-tree entry whose area key references the
	 * row_uid. Idempotent — no-op (404) if the row doesn't exist.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_grid_row( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];
		$row_uid = (string) $request['row_uid'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$surface = $this->resolve_grid_surface( $view_id, (string) ( $request->get_param( 'surface' ) ?: 'fields' ) );
		if ( is_wp_error( $surface ) ) {
			return $surface;
		}

		$tree    = ( $surface['read'] )( $view_id );
		$next    = [];
		$removed = 0;
		foreach ( $tree as $key => $slots ) {
			if ( false !== strpos( $key, '::' . $row_uid ) ) {
				++$removed;
				continue;
			}
			$next[ $key ] = $slots;
		}

		if ( 0 === $removed ) {
			return new WP_Error( 'gv_rest_grid_row_not_found', __( 'Row UID not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		( $surface['write'] )( $view_id, $next );
		$this->bump_version( $view_id );

		$response = new WP_REST_Response(
			[
				'view_id'       => $view_id,
				'surface'       => $surface['id'],
				'row_uid'       => $row_uid,
				'removed_areas' => $removed,
				'version'       => $this->compute_version( $view_id ),
			],
			200
		);
		$response->header( 'ETag', '"' . $this->compute_version( $view_id ) . '"' );
		return $response;
	}

	// ===================================================================
	// Grid helpers
	// ===================================================================


	/**
	 * Resolve the area-id prefix for a grid-aware View. Returns the
	 * empty string when the template doesn't use the Layout Builder
	 * area-prefixing convention (so callers can short-circuit out of
	 * grid CRUD).
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id
	 *
	 * @return string
	 */
	private function layout_builder_area_prefix( int $view_id, string $zone = 'directory' ): string {
		$template_id = $this->resolve_template_id( $view_id, $zone );

		/** This filter is documented above in `get_layouts()`. */
		$grid_templates = apply_filters(
			'gk/gravityview/rest/grid-aware-templates',
			[ 'gravityview-layout-builder' ]
		);
		return in_array( $template_id, (array) $grid_templates, true ) ? $template_id : '';
	}

	/**
	 * Resolve a grid surface to its operations spec. Returns a
	 * `WP_Error` for unknown surfaces. The returned array carries
	 * everything `create_grid_row` / `patch_grid_row` /
	 * `delete_grid_row` need to operate uniformly:
	 *
	 *   - `id`            string surface identifier (echoed in response)
	 *   - `read`          callable(view_id) → array tree
	 *   - `write`         callable(view_id, array tree)
	 *   - `prefix`        callable(view_id, zone) → string|null prefix
	 *                     (null when the zone doesn't currently support
	 *                     a grid — e.g. fields zone with non-Layout-
	 *                     Builder template)
	 *   - `valid_zones`   string[] zones the surface accepts
	 *   - `default_zones` string[] zones to write to when caller
	 *                     omits the zones param
	 *
	 * Add new surfaces by extending the switch. The widgets surface
	 * uses an empty prefix because widget zone names ARE the areaid
	 * root; the fields surface defers to per-zone template lookup.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id
	 * @param string $surface_id `fields` | `widgets`.
	 *
	 * @return array|\WP_Error
	 */
	private function resolve_grid_surface( int $view_id, string $surface_id ) {
		switch ( $surface_id ) {
			case 'fields':
				return [
					'id'            => 'fields',
					'read'          => function ( $vid ) {
						return $this->read_fields( $vid );
					},
					'write'         => function ( $vid, $tree ) {
						$this->write_fields( $vid, $tree );
					},
					'prefix'        => function ( $vid, $zone ) {
						$prefix = $this->layout_builder_area_prefix( $vid, $zone );
						return '' === $prefix ? null : $prefix;
					},
					'valid_zones'   => [ 'directory', 'single' ],
					'default_zones' => [ 'directory', 'single' ],
				];

			case 'widgets':
				return [
					'id'            => 'widgets',
					'read'          => function ( $vid ) {
						return $this->read_widgets( $vid );
					},
					'write'         => function ( $vid, $tree ) {
						$this->write_widgets( $vid, $tree );
					},
					// Widget zones use no template prefix — the
					// areaid coming out of `Grid::get_row_by_type`
					// is used directly. Returning empty-string
					// (not null) signals "supported, no prefix".
					'prefix'        => function () {
						return '';
					},
					'valid_zones'   => self::WIDGET_ZONES,
					'default_zones' => self::WIDGET_ZONES,
				];
		}

		return new WP_Error(
			'gv_rest_invalid_surface',
			/* translators: %s: requested surface id */
			sprintf( __( 'Unknown grid surface "%s". Use one of: fields, widgets.', 'gk-gravityview' ), $surface_id ),
			[ 'status' => 400 ]
		);
	}

	/**
	 * PATCH /views/{id}/config — bulk replace / partial merge.
	 *
	 * Honors `If-Match` for optimistic-concurrency checks. The payload may
	 * be a partial config (only top-level keys present are merged) or a full
	 * tree (whole subtrees replaced).
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function patch_config( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload = $request->get_json_params();
		if ( ! is_array( $payload ) ) {
			return new WP_Error( 'gv_rest_invalid_payload', __( 'JSON payload required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		if ( isset( $payload['fields'] ) && is_array( $payload['fields'] ) ) {
			$this->write_fields( $view_id, $this->sanitize_field_tree( $view_id, $payload['fields'] ) );
		}
		if ( isset( $payload['widgets'] ) && is_array( $payload['widgets'] ) ) {
			$this->write_widgets( $view_id, $payload['widgets'] );
		}
		if ( isset( $payload['template_settings'] ) && is_array( $payload['template_settings'] ) ) {
			$err = $this->merge_template_settings( $view_id, $payload['template_settings'] );
			if ( is_wp_error( $err ) ) {
				return $err;
			}
		}
		if ( isset( $payload['search_criteria'] ) && is_array( $payload['search_criteria'] ) ) {
			$err = $this->merge_template_settings( $view_id, $payload['search_criteria'] );
			if ( is_wp_error( $err ) ) {
				return $err;
			}
		}

		$this->bump_version( $view_id );

		return $this->get_config( $request );
	}

	/**
	 * POST /views/{id}/config/_apply — bulk-apply a config tree.
	 *
	 * Semantics:
	 *   - `template_id` (if present) is applied first via the same
	 *     codepath as `patch_template`.
	 *   - `fields` is a `{area_key: [item, …]}` map where each item
	 *     is `{ field_id, slot?, label?, …settings }`. The server
	 *     mints fresh slot UIDs for items that don't supply one,
	 *     preserves any UID the spec carries (so an AI can author
	 *     deterministic Views), validates the schema, and applies
	 *     per-setting sanitization modes.
	 *   - In `mode=replace` (default), each area present in `fields`
	 *     REPLACES the existing area entirely; absent areas are
	 *     untouched. In `mode=merge`, items append to the existing
	 *     area.
	 *   - Same treatment for `widgets`.
	 *   - `template_settings` / `search_criteria` partial-merge into
	 *     `_gravityview_template_settings`.
	 *   - Atomic per meta key: the in-memory tree is fully built and
	 *     validated before any post-meta write happens; a validation
	 *     error aborts with the original state untouched.
	 *   - Returns the full `/config` response so the client never
	 *     needs a follow-up GET.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function apply_config( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload = $request->get_json_params();
		if ( ! is_array( $payload ) ) {
			return new WP_Error( 'gv_rest_invalid_payload', __( 'JSON payload required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// Reset per-request warning accumulator. The nested
		// `apply_collection()` calls below push to this when they
		// detect a setting (today: conditional_logic) was silently
		// dropped during sanitization.
		$this->apply_warnings = [];

		$mode = isset( $payload['mode'] ) && in_array( $payload['mode'], [ 'replace', 'merge' ], true )
			? $payload['mode']
			: 'replace';

		// Snapshot for error reporting — we DO NOT roll back at the
		// post-meta layer (WP has no transaction across meta keys);
		// we instead build the entire next-state in memory and only
		// write if every step validated. The snapshot is kept so a
		// later phase (or a client retry) can compare.
		$original = [
			'template_id'       => $this->resolve_template_id( $view_id ),
			'fields'            => $this->read_fields( $view_id ),
			'widgets'           => $this->read_widgets( $view_id ),
			'template_settings' => $this->read_template_settings( $view_id ),
		];

		// 1. Resolve next template (may impact schema-aware sanitization).
		// Validate against the registered catalogue before treating the
		// payload value as authoritative; otherwise a typo strands the
		// View on an unrenderable template id (the meta write below
		// would persist whatever string the caller sent).
		$next_template = $original['template_id'];
		if ( isset( $payload['template_id'] ) && is_string( $payload['template_id'] ) && '' !== $payload['template_id'] ) {
			$invalid = $this->validate_template_id_or_error( $payload['template_id'], 'template_id' );
			if ( $invalid instanceof \WP_Error ) {
				return $invalid;
			}
			$next_template = $payload['template_id'];
		}

		// 2. Build next fields tree.
		$next_fields    = $original['fields'];
		$applied_fields = [];
		if ( isset( $payload['fields'] ) && is_array( $payload['fields'] ) ) {
			$result = $this->apply_collection(
				$view_id,
				$next_template,
				$next_fields,
				$payload['fields'],
				$mode,
				'fields'
			);
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			$next_fields    = $result['tree'];
			$applied_fields = $result['applied'];
		}

		// 3. Build next widgets tree (same shape).
		$next_widgets    = $original['widgets'];
		$applied_widgets = [];
		if ( isset( $payload['widgets'] ) && is_array( $payload['widgets'] ) ) {
			$result = $this->apply_collection(
				$view_id,
				$next_template,
				$next_widgets,
				$payload['widgets'],
				$mode,
				'widgets'
			);
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			$next_widgets    = $result['tree'];
			$applied_widgets = $result['applied'];
		}

		// 4. Merge template_settings + search_criteria. Namespaced
		// keys (e.g. `datatables` mapped from a registered
		// template-settings source) keep their nested shape so the
		// splitter below routes them to the right silo meta key on
		// write; everything else stays as top-level entries on the
		// primary `_gravityview_template_settings` meta.
		$next_settings = $original['template_settings'];
		$ts_sources    = $this->template_settings_sources();
		$by_prefix     = $this->prefix_map( $ts_sources );
		foreach ( [ 'template_settings', 'search_criteria' ] as $bucket ) {
			if ( ! isset( $payload[ $bucket ] ) || ! is_array( $payload[ $bucket ] ) ) {
				continue;
			}
			foreach ( $payload[ $bucket ] as $key => $value ) {
				$key = sanitize_key( $key );
				if ( '' === $key ) {
					continue;
				}
				if ( '' !== $key && isset( $by_prefix[ $key ] ) && is_array( $value ) ) {
					if ( ! isset( $next_settings[ $key ] ) || ! is_array( $next_settings[ $key ] ) ) {
						$next_settings[ $key ] = [];
					}
					foreach ( $value as $sub_key => $sub_value ) {
						$sub_key = sanitize_key( $sub_key );
						if ( '' === $sub_key ) {
							continue;
						}
						// Pass the prefix so the validator can find
						// the silo's schema; the legacy code path skipped
						// prefixed sources entirely, letting DT/MFV
						// settings bypass type + enum validation.
						$validation_error = $this->validate_template_setting_value( $sub_key, $sub_value, $ts_sources, $key );
						if ( is_wp_error( $validation_error ) ) {
							return $validation_error;
						}
						$next_settings[ $key ][ $sub_key ] = $this->sanitize_setting_value( $sub_value );
					}
					continue;
				}
				$validation_error = $this->validate_template_setting_value( $key, $value, $ts_sources );
				if ( is_wp_error( $validation_error ) ) {
					return $validation_error;
				}
				$next_settings[ $key ] = $this->sanitize_setting_value( $value );
			}
		}

		// 5. Persist. Order: template first (affects schema), then
		// settings, then field/widget trees. The directory template
		// lives in its own post meta (`_gravityview_directory_template`);
		// we do NOT duplicate it into template_settings — that was a
		// legacy storage habit and is redundant now that template_ids
		// carries the per-zone breakdown.
		if ( $next_template !== $original['template_id'] ) {
			update_post_meta( $view_id, self::META_DIRECTORY_TEMPLATE, $next_template );
		}
		unset( $next_settings['template'] );

		// Per-zone template overrides (single + edit). Validated
		// against the registered template list — typoed values are
		// rejected before write so an invalid id can't strand a
		// zone with no rendered output.
		if ( isset( $payload['template_ids'] ) && is_array( $payload['template_ids'] ) ) {
			foreach ( [ 'single', 'edit' ] as $zone ) {
				if ( ! isset( $payload['template_ids'][ $zone ] ) ) {
					continue;
				}
				$zone_template = (string) $payload['template_ids'][ $zone ];
				// Empty string clears the per-zone override → falls
				// back to directory inheritance via resolve_template_id.
				if ( '' === $zone_template ) {
					delete_post_meta( $view_id, $this->template_meta_key_for_zone( $zone ) );
					continue;
				}
				$invalid = $this->validate_template_id_or_error( $zone_template, "template_ids.{$zone}" );
				if ( $invalid instanceof \WP_Error ) {
					return $invalid;
				}
				update_post_meta( $view_id, $this->template_meta_key_for_zone( $zone ), $zone_template );
			}
		}

		$this->write_template_settings_tree( $view_id, $next_settings );
		update_post_meta( $view_id, self::META_FIELDS, $next_fields );
		update_post_meta( $view_id, self::META_WIDGETS, $next_widgets );

		/**
		 * Fires after the core apply-config writes have persisted, so
		 * add-ons can write their own meta keys (e.g. Multiple Forms'
		 * `_gravityview_form_joins`) from the same payload — atomically
		 * with the core write from the caller's perspective.
		 *
		 * Listeners receive the raw, unsanitized payload; they own
		 * validation for their keys. Listeners MUST return early when
		 * none of "their" keys are present in the payload.
		 *
		 * Companion read-side hook is the `gk/gravityview/rest/view-config/get`
		 * filter on `get_config()`.
		 *
		 * Does not fire while a dry run is active, so a listener never
		 * writes its own meta for a request the core write skipped.
		 *
		 * @since 3.0.0
		 *
		 * @param int             $view_id View post id.
		 * @param array           $payload Raw JSON payload the caller sent.
		 * @param WP_REST_Request $request Full request (for header / param access).
		 */
		if ( ! \GravityKit\GravityView\Abilities\Bootstrap::is_dry_run_active() ) {
			do_action( 'gk/gravityview/rest/view-config/apply/after', $view_id, $payload, $request );
		}
		$this->bump_version( $view_id );

		// Compact response by default — the caller already has the
		// payload they sent, and they can re-fetch via GET /config if
		// they want to see how the server normalized everything.
		// Echoing the full tree on every multi-slot apply added
		// 5–10 KB per response with zero new information. Pass
		// `?return=full` to opt into the legacy "echo whole config"
		// shape — useful for the Design Studio's optimistic UI sync.
		$return_mode = (string) ( $request->get_param( 'return' ) ?: 'compact' );
		if ( 'full' === $return_mode ) {
			$config_response        = $this->get_config( $request );
			$config_data            = $config_response->get_data();
			$config_data['applied'] = [
				'fields'  => $applied_fields,
				'widgets' => $applied_widgets,
				'mode'    => $mode,
			];
			if ( ! empty( $this->apply_warnings ) ) {
				$config_data['warnings'] = $this->apply_warnings;
			}
			return new WP_REST_Response( $config_data, 200 );
		}

		$response_body = [
			'view_id' => $view_id,
			'version' => $this->compute_version( $view_id ),
			'applied' => [
				'fields'  => $applied_fields,
				'widgets' => $applied_widgets,
				'mode'    => $mode,
			],
		];
		if ( ! empty( $this->apply_warnings ) ) {
			$response_body['warnings'] = $this->apply_warnings;
		}
		return new WP_REST_Response(
			$response_body,
			200
		);
	}

	/**
	 * Build the next-state for a fields or widgets collection.
	 *
	 * For each area in the payload:
	 *   - In `replace` mode, the target area is rebuilt from the
	 *     payload's ordered items.
	 *   - In `merge` mode, the payload's items append to the existing
	 *     area.
	 *
	 * Each item is normalized:
	 *   - `field_id` is required.
	 *   - `slot` (optional) is preserved when supplied; otherwise a
	 *     server-generated UID is minted. Useful for deterministic
	 *     AI-authored Views where the spec carries its own UIDs.
	 *   - Every other key is sanitized through the schema-aware
	 *     pipeline so textarea / extension-slot settings keep richer
	 *     markup while everything else goes through
	 *     `sanitize_text_field`.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id     View id.
	 * @param string $template_id Template id (post-switch).
	 * @param array  $existing    Existing tree (keyed by area → slot).
	 * @param array  $payload     `{area: [items]}` from the request.
	 * @param string $mode        'replace' | 'merge'.
	 * @param string $collection  'fields' | 'widgets' (for error messages).
	 *
	 * @return array{tree: array, applied: array}|WP_Error.
	 */
	private function apply_collection( int $view_id, string $template_id, array $existing, array $payload, string $mode, string $collection ) {
		$form_id = $this->resolve_form_id( $view_id );
		$tree    = $existing;
		$applied = [];

		// Authoritative area list for this template + collection. We
		// validate every payload key against it before writing — without
		// this guard a typo (or a malicious payload) silently grows a
		// new area in `_gravityview_directory_fields` that no template
		// renders, and the customer is left wondering why their fields
		// went missing.
		$known_areas = $this->known_areas_for( $template_id, $collection, $view_id );

		foreach ( $payload as $area => $items ) {
			if ( ! is_array( $items ) ) {
				return new WP_Error(
					'gv_rest_invalid_apply',
					sprintf(
						/* translators: 1: collection name, 2: area key */
						__( '%1$s[%2$s] must be an array of items.', 'gk-gravityview' ),
						$collection,
						$area
					),
					[ 'status' => 400 ]
				);
			}
			$area = (string) $area;

			// Length cap — area keys are post-meta map keys, no
			// legitimate templated area name comes anywhere near 256
			// chars. A 10K-char key is a bloat-attack signature.
			if ( strlen( $area ) > 256 ) {
				return new WP_Error(
					'gv_rest_invalid_area_key',
					sprintf(
						/* translators: 1: collection, 2: actual length */
						__( '%1$s area key exceeds 256 chars (got %2$d). Templates produce area keys far shorter than this; reject as a bloat-attack vector.', 'gk-gravityview' ),
						$collection,
						strlen( $area )
					),
					[
						'status'          => 400,
						'received_length' => strlen( $area ),
					]
				);
			}

			// Control-char check — newlines, tabs, NUL bytes, and
			// other ASCII < 0x20 + 0x7F never appear in a real
			// area key. Their presence is a sign of a malformed or
			// adversarial payload that would otherwise create a
			// phantom area in post-meta the admin UI can't surface.
			if ( preg_match( '/[\x00-\x1F\x7F]/', $area ) ) {
				return new WP_Error(
					'gv_rest_invalid_area_key',
					sprintf(
						/* translators: 1: collection, 2: hex-encoded area key for safe display */
						__( '%1$s area key contains control characters. Hex preview: %2$s', 'gk-gravityview' ),
						$collection,
						bin2hex( $area )
					),
					[ 'status' => 400 ]
				);
			}

			// Structural prefix check — every fields area key MUST
			// start with `directory_`, `single_`, or `edit_`; every
			// widget area key MUST be a known widget meta-zone. A
			// caller that forgets the zone prefix (a common mistake
			// when composing the key from gv_create_grid_row's
			// per-zone `created` map) would otherwise silently grow
			// a phantom area in storage that no template renders.
			// Grid-aware open mode (above) trusts the AREAID part —
			// but the ZONE prefix is still mandatory.
			if ( 'fields' === $collection ) {
				$has_valid_prefix = ( 0 === strpos( $area, 'directory_' ) )
					|| ( 0 === strpos( $area, 'single_' ) )
					|| ( 0 === strpos( $area, 'edit_' ) );
				if ( ! $has_valid_prefix ) {
					return new WP_Error(
						'gv_rest_invalid_area_key',
						sprintf(
							/* translators: %s: area key supplied by the caller */
							__( 'fields[%s]: area key is missing its zone prefix. Use the `area_keys` array returned by gv_create_grid_row (each entry is already prefixed as `directory_…`, `single_…`, or `edit_…`).', 'gk-gravityview' ),
							$area
						),
						[
							'status' => 400,
							'hint'   => 'Prepend `{zone}_` (zone is directory / single / edit) before the areaid returned by gv_create_grid_row.',
						]
					);
				}
			}
			if ( 'widgets' === $collection ) {
				$valid_widget_zones = [
					'header_top',
					'header_bottom',
					'header_left',
					'header_right',
					'footer_top',
					'footer_bottom',
					'footer_left',
					'footer_right',
				];
				// Layout Builder widget areas append `::cols::row_uid`
				// to the base zone — strip that for the prefix check.
				$base = strstr( $area, '::', true );
				if ( false === $base ) {
					$base = $area;
				}
				if ( ! in_array( $base, $valid_widget_zones, true ) ) {
					return new WP_Error(
						'gv_rest_invalid_area_key',
						sprintf(
							/* translators: %s: widget area key supplied by the caller */
							__( 'widgets[%s]: area must begin with a widget meta-zone (header_top, header_bottom, header_left, header_right, footer_top, footer_bottom, footer_left, footer_right). For Layout Builder widget grids, the meta-zone is the segment BEFORE the first `::`. To add a search bar, use gv_search_bar_add (it places the bar for you); for other widgets use gv_view_widget_add with an area like "header_top".', 'gk-gravityview' ),
							$area
						),
						[ 'status' => 400 ]
					);
				}
			}

			// Edit zone bypasses the grid: only the canonical
			// `edit_edit-fields` area is valid. Grid-style edit keys
			// (e.g. `edit_gravityview-layout-builder-top::100::row`)
			// would silently disappear because the Edit Entry
			// renderer never looks at them — reject loudly with the
			// canonical key in the error so the caller can correct.
			if ( 'fields' === $collection && 0 === strpos( $area, 'edit_' ) && 'edit_edit-fields' !== $area ) {
				return new WP_Error(
					'gv_rest_unknown_edit_area',
					sprintf(
						/* translators: %s: area key supplied by the caller */
						__( 'fields[%s]: Edit Entry doesn\'t use the layout grid. Place edit-zone fields under the canonical area key "edit_edit-fields" instead.', 'gk-gravityview' ),
						$area
					),
					[
						'status'        => 400,
						'expected_area' => 'edit_edit-fields',
					]
				);
			}

			if ( ! empty( $known_areas ) && ! in_array( $area, $known_areas, true ) && 'edit_edit-fields' !== $area ) {
				return new WP_Error(
					'gv_rest_unknown_area',
					sprintf(
						/* translators: 1: collection name, 2: area key, 3: template id */
						__( '%1$s[%2$s]: area not defined by template "%3$s".', 'gk-gravityview' ),
						$collection,
						$area,
						$template_id
					),
					[
						'status'      => 400,
						'known_areas' => $known_areas,
					]
				);
			}

			// Grid-aware permissive mode (above) trusts the area key,
			// but a typoed row_uid would let the field land in storage
			// where Layout Builder can't render it. Require the row to
			// exist either in the View's current field tree or in this
			// same payload (so multi-area writes within one apply
			// against a freshly-created row still work).
			if ( 'fields' === $collection && false !== strpos( $area, '::' ) ) {
				$row_check = $this->require_known_grid_row(
					$view_id,
					$area,
					$tree,
					$payload
				);
				if ( is_wp_error( $row_check ) ) {
					return $row_check;
				}
			}

			$area_tree = 'replace' === $mode ? [] : ( $tree[ $area ] ?? [] );

			foreach ( $items as $index => $item ) {
				if ( ! is_array( $item ) ) {
					return new WP_Error(
						'gv_rest_invalid_apply',
						sprintf(
							/* translators: 1: collection, 2: area, 3: index */
							__( '%1$s[%2$s][%3$d] must be an object.', 'gk-gravityview' ),
							$collection,
							$area,
							$index
						),
						[ 'status' => 400 ]
					);
				}

				// Strict type check before the (string) cast — PHP
				// would otherwise silently coerce an object/array to
				// the literal string "Array" and persist a corrupt
				// slot that points at no real field.
				if ( 'fields' === $collection && isset( $item['field_id'] ) && ! is_string( $item['field_id'] ) && ! is_numeric( $item['field_id'] ) ) {
					return new WP_Error(
						'gv_rest_invalid_field_id',
						sprintf(
							/* translators: 1: collection, 2: area, 3: index, 4: actual type */
							__( '%1$s[%2$s][%3$d].field_id must be a string or number; received %4$s.', 'gk-gravityview' ),
							$collection,
							$area,
							$index,
							gettype( $item['field_id'] )
						),
						[
							'status'         => 400,
							'received_type'  => gettype( $item['field_id'] ),
							'received_value' => $item['field_id'],
						]
					);
				}
				$field_id = ( isset( $item['field_id'] ) && is_scalar( $item['field_id'] ) ) ? (string) $item['field_id'] : '';
				// Widgets are identified by widget_id — but the read surface and
				// the schema tools spell it `id`/`type`, and a small model often
				// sends `{type:"search_bar"}`. Accept all of those for the widgets
				// collection so it isn't stranded on a field_id error it can't
				// reasonably decode.
				$widget_type_is_id = false;
				// For widgets the canonical identity is widget_id > id > type; it
				// WINS over the legacy field_id alias so a payload carrying both
				// can't silently target the wrong widget.
				if ( 'widgets' === $collection ) {
					foreach ( array( 'widget_id', 'id', 'type' ) as $widget_alias ) {
						// Scalar-only: a non-scalar (array/object) would cast to
						// "Array" and persist as a bogus widget id.
						if ( ! isset( $item[ $widget_alias ] ) || ! is_scalar( $item[ $widget_alias ] ) || '' === (string) $item[ $widget_alias ] ) {
							continue;
						}
						$candidate = (string) $item[ $widget_alias ];
						// `type` is a real widget setting (e.g. export_link csv/tsv)
						// unless it names a registered widget — only then is it the id.
						if ( 'type' === $widget_alias && ! $this->is_registered_widget( $candidate ) ) {
							continue;
						}
						$field_id          = $candidate;
						$widget_type_is_id = ( 'type' === $widget_alias );
						break;
					}
				}
				if ( '' === $field_id ) {
					$missing_hint = 'widgets' === $collection
						? __( 'Widget entries identify the widget by `widget_id` (e.g. "search_bar", "page_info"). To add a search bar with fields in one call use gv_search_bar_add; for other widgets use gv_view_widget_add.', 'gk-gravityview' )
						: __( 'Field entries require `field_id` (a Gravity Forms field id or a virtual id).', 'gk-gravityview' );
					return new WP_Error(
						'gv_rest_missing_field_id',
						sprintf(
							/* translators: 1: collection, 2: area, 3: index, 4: corrective hint */
							__( '%1$s[%2$s][%3$d].field_id is required. %4$s', 'gk-gravityview' ),
							$collection,
							$area,
							$index,
							$missing_hint
						),
						[ 'status' => 400 ]
					);
				}

				// Slot UID: take the caller's when provided, else mint one.
				// Validate through the shared is_valid_slot_uid() so _apply
				// matches the get/patch/move/clone paths — case-insensitive,
				// because pre-2.0 installs seeded slot UIDs with the mixed-case
				// wp_generate_password() shape, which a lowercase-only check
				// would reject on a config round-trip (config-get → config-set),
				// silently dropping legacy slots.
				$slot = isset( $item['slot'] ) ? (string) $item['slot'] : '';
				if ( '' !== $slot && ! $this->is_valid_slot_uid( $slot ) ) {
					return new WP_Error(
						'gv_rest_invalid_slot_uid',
						sprintf(
							/* translators: 1: collection, 2: area, 3: index */
							__( '%1$s[%2$s][%3$d].slot must contain only letters, digits, dots, underscores, and hyphens (max 64 characters).', 'gk-gravityview' ),
							$collection,
							$area,
							$index
						),
						[ 'status' => 400 ]
					);
				}
				if ( '' === $slot ) {
					$slot = $this->generate_slot_uid();
				}

				// Schema-aware sanitization. For widgets we skip the
				// per-slot field-options compute (widgets have their
				// own filter chain not exposed via the inspector
				// today) and apply default sanitization; for fields
				// we look up the schema and apply per-setting mode.
				//
				// MERGE mode: start from the slot's CURRENT record so a
				// partial payload (e.g. `{slot, field_id, custom_label}`)
				// doesn't drop the customer's previously-configured
				// settings (show_label, only_loggedin, …). Replace mode
				// still starts fresh — that's the contract callers opt
				// into when they send the full tree.
				//
				// `label` falls back through three sources so a partial
				// payload doesn't blank the GF field's stored name:
				// 1. payload `label` (caller explicitly overriding)
				// 2. existing slot `label` (preserved)
				// 3. empty string (no prior state)
				$existing_slot = ( 'merge' === $mode && isset( $area_tree[ $slot ] ) && is_array( $area_tree[ $slot ] ) )
					? $area_tree[ $slot ]
					: [];

				$slot_record          = $existing_slot;
				$slot_record['id']    = $field_id;
				$slot_record['label'] = isset( $item['label'] )
					? (string) $item['label']
					: ( isset( $existing_slot['label'] ) ? (string) $existing_slot['label'] : '' );

				$slot_schema = 'fields' === $collection
					? $this->compute_slot_schema( $template_id, $form_id, $area, $slot_record, '', $view_id )
					: [];

				$identity_keys = self::identity_alias_keys( $collection, $widget_type_is_id );
				foreach ( $item as $key => $value ) {
					$key = sanitize_key( $key );
					if ( '' === $key || in_array( $key, $identity_keys, true ) ) {
						continue;
					}
					if ( 'conditional_logic' === $key ) {
						$cl = $this->validate_conditional_logic( $value );
						if ( $cl['rejected'] ) {
							$this->record_warning( $area, $slot, $key, $cl['reason'] );
						}
						$slot_record[ $key ] = $cl['value'];
						continue;
					}
					$mode_for_value      = 'fields' === $collection
						? $this->sanitize_mode_for( $slot_schema, $key )
						: 'default';
					$slot_record[ $key ] = $this->sanitize_setting_value( $value, $mode_for_value );
				}

				// Auto-migrate search_bar widgets from legacy
				// `search_fields` (JSON-encoded flat array) to modern
				// `search_fields_section` (keyed-by-position PHP array).
				// New writes only produce modern; existing legacy data
				// converts on first save through this API.
				if ( 'widgets' === $collection && 'search_bar' === $field_id ) {
					$slot_record = $this->migrate_search_bar_to_modern( $slot_record, $view_id );

					// Bulk-apply path: when the caller sent a nested
					// `search_fields_section` (e.g. via gv_apply_view_config),
					// route every per-slot entry through the same
					// Search_Field domain normaliser the per-field
					// CRUD endpoints use. Without this, agent-friendly
					// shorthand (`input` alias, bare numeric GF ids,
					// missing form_id / custom_label / custom_class /
					// only_loggedin defaults) would slip into storage
					// and the legacy admin metabox would drop the
					// fields on its next save.
					if ( ! empty( $slot_record['search_fields_section'] ) && is_array( $slot_record['search_fields_section'] ) ) {
						foreach ( $slot_record['search_fields_section'] as $position => $row ) {
							if ( ! is_array( $row ) ) {
								continue;
							}
							foreach ( $row as $search_uid => $search_field ) {
								if ( 'area_settings' === $search_uid || ! is_array( $search_field ) ) {
									continue;
								}
								// Preserve unknown per-field add-on settings: normalise
								// canonicalises the core keys but re-adds any add-on key
								// the domain didn't re-emit, so a bulk apply that merely
								// round-trips a search bar can't silently drop them.
								$slot_record['search_fields_section'][ $position ][ $search_uid ] =
									( new SearchFieldResolver( $view_id ) )->normalize( $search_field );
							}
						}
					}
				}

				// Edit Entry's `merge_field_properties()` wipes the
				// rendered label when `show_label` is empty, even if
				// a `custom_label` is set. A caller passing
				// `custom_label` clearly wants the label rendered;
				// auto-enable show_label so the intent isn't lost
				// silently. Caller can still pass an explicit
				// `show_label: 0` to opt out.
				if (
					'fields' === $collection
					&& 'edit_edit-fields' === $area
					&& ! empty( $slot_record['custom_label'] )
					&& ! array_key_exists( 'show_label', $slot_record )
				) {
					$slot_record['show_label'] = '1';
				}

				$area_tree[ $slot ] = $slot_record;
				$applied[]          = [
					'area'     => $area,
					'slot'     => $slot,
					'field_id' => $field_id,
				];
			}

			$tree[ $area ] = $area_tree;
		}

		return [
			'tree'    => $tree,
			'applied' => $applied,
		];
	}

	/**
	 * PATCH /views/{id}/fields/{area}/{slot} — single-slot edit.
	 *
	 * Payload is a `{setting_key: new_value}` map. Settings not in the
	 * payload are left untouched; settings set to `null` are removed.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function patch_field( WP_REST_Request $request ) {
		$view_id  = (int) $request['id'];
		$area     = (string) $request['area'];
		$slot_uid = (string) $request['slot'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$slot = $this->read_slot( $view_id, $area, $slot_uid );
		if ( null === $slot ) {
			return new WP_Error( 'gv_rest_slot_not_found', __( 'Slot not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		$payload = $request->get_json_params();
		if ( ! is_array( $payload ) ) {
			return new WP_Error( 'gv_rest_invalid_payload', __( 'JSON payload required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// Compute the schema once so per-setting sanitization knows
		// which inputs allow richer content (textarea / html).
		$template_id = $this->resolve_template_id( $view_id );
		$form_id     = $this->resolve_form_id( $view_id );
		$schema      = $this->compute_slot_schema( $template_id, $form_id, $area, $slot, '', $view_id );

		$this->apply_warnings = [];
		foreach ( $payload as $key => $value ) {
			$key = sanitize_key( $key );
			if ( '' === $key ) {
				continue;
			}
			if ( null === $value ) {
				unset( $slot[ $key ] );
				continue;
			}
			if ( 'conditional_logic' === $key ) {
				$cl = $this->validate_conditional_logic( $value );
				if ( $cl['rejected'] ) {
					$this->record_warning( $area, $slot_uid, $key, $cl['reason'] );
				}
				$slot[ $key ] = $cl['value'];
				continue;
			}
			$mode         = $this->sanitize_mode_for( $schema, $key );
			$slot[ $key ] = $this->sanitize_setting_value( $value, $mode );
		}

		$fields                       = $this->read_fields( $view_id );
		$fields[ $area ][ $slot_uid ] = $slot;
		$this->write_fields( $view_id, $fields );
		$this->bump_version( $view_id );

		$response_body = [
			'view_id' => $view_id,
			'area'    => $area,
			'slot'    => $slot_uid,
			'values'  => $slot,
			'version' => $this->compute_version( $view_id ),
		];
		if ( ! empty( $this->apply_warnings ) ) {
			$response_body['warnings'] = $this->apply_warnings;
		}
		return new WP_REST_Response(
			$response_body,
			200
		);
	}

	/**
	 * DELETE /views/{id}/fields/{area}/{slot} — remove a slot.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_field( WP_REST_Request $request ) {
		$view_id  = (int) $request['id'];
		$area     = (string) $request['area'];
		$slot_uid = (string) $request['slot'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$fields = $this->read_fields( $view_id );
		if ( ! isset( $fields[ $area ][ $slot_uid ] ) ) {
			return new WP_Error( 'gv_rest_slot_not_found', __( 'Slot not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		unset( $fields[ $area ][ $slot_uid ] );
		if ( empty( $fields[ $area ] ) ) {
			unset( $fields[ $area ] );
		}

		$this->write_fields( $view_id, $fields );
		$this->bump_version( $view_id );

		return new WP_REST_Response(
            [
				'view_id' => $view_id,
				'area'    => $area,
				'slot'    => $slot_uid,
				'version' => $this->compute_version( $view_id ),
			],
			200
        );
	}

	/**
	 * POST /views/{id}/fields/{area}/_slots — create slot with server-generated UID.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_field_slot( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];
		$area    = (string) $request['area'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload  = $request->get_json_params();
		$field_id = isset( $payload['field_id'] ) ? (string) $payload['field_id'] : '';

		if ( '' === $field_id ) {
			return new WP_Error( 'gv_rest_missing_field_id', __( 'field_id is required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// Pre-mint a slot UID so the caller knows it before delegating
		// to apply_collection. The collection accepts a slot value in
		// the item payload and reuses it instead of generating its own.
		$slot_uid = $this->generate_slot_uid();

		// Delegate to the same code path apply-view-config uses.
		// Without this, create_field_slot only persisted `field_id` +
		// `label` plus hard-coded defaults — every other setting in the
		// payload (`show_label`, `custom_label`, `only_loggedin`,
		// `conditional_logic`, per-field-type settings, etc.) was
		// silently dropped. Routing through apply_collection inherits
		// the full sanitisation + schema-aware merge + area validation
		// + grid-row check + warnings rollup that the bulk-apply gets.
		$item             = $payload;
		$item['field_id'] = $field_id;
		$item['slot']     = $slot_uid;
		$collection_input = [ $area => [ $item ] ];

		$template_id = $this->resolve_template_id( $view_id );
		$existing    = $this->read_fields( $view_id );

		// Reset the per-request warnings rollup before delegating so
		// the response only surfaces drops from THIS write, not from
		// any earlier write on the same request (some clients batch).
		$this->apply_warnings = [];

		$result = $this->apply_collection(
			$view_id,
			$template_id,
			$existing,
			$collection_input,
			'merge',
			'fields'
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$this->write_fields( $view_id, $result['tree'] );
		$this->bump_version( $view_id );

		$persisted_slot = isset( $result['tree'][ $area ][ $slot_uid ] )
			? $result['tree'][ $area ][ $slot_uid ]
			: [];

		$response = [
			'view_id' => $view_id,
			'area'    => $area,
			'slot'    => $slot_uid,
			'values'  => $persisted_slot,
			'version' => $this->compute_version( $view_id ),
		];

		// Surface any sanitiser drops apply_collection rolled up. Same
		// contract as the bulk-apply response.
		if ( ! empty( $this->apply_warnings ) ) {
			$response['warnings'] = $this->apply_warnings;
		}

		return new WP_REST_Response( $response, 201 );
	}

	/**
	 * POST /views/{id}/fields/_move — atomic move between or within areas.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function move_field( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload = $request->get_json_params();

		// Defensive payload guards — `from`/`to` might be missing or
		// non-array (PHP `null['area']` throws under strict_types).
		$from      = is_array( $payload['from'] ?? null ) ? $payload['from'] : [];
		$to        = is_array( $payload['to'] ?? null ) ? $payload['to'] : [];
		$from_area = isset( $from['area'] ) ? (string) $from['area'] : '';
		$from_slot = isset( $from['slot'] ) ? (string) $from['slot'] : '';
		$to_area   = isset( $to['area'] ) ? (string) $to['area'] : '';

		if ( '' === $from_area || '' === $from_slot || '' === $to_area ) {
			return new WP_Error( 'gv_rest_invalid_move', __( 'from.area, from.slot, and to.area are required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// Reject a caller-supplied source UID with unsafe characters
		// before using it as a lookup key — funnel into the existing
		// not-found / 404 path. Valid UIDs are unaffected.
		$from_slot_is_safe = $this->is_valid_slot_uid( $from_slot );
		if ( ! $from_slot_is_safe ) {
			return new WP_Error( 'gv_rest_slot_not_found', __( 'Source slot not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		$fields = $this->read_fields( $view_id );
		if ( ! isset( $fields[ $from_area ][ $from_slot ] ) ) {
			return new WP_Error( 'gv_rest_slot_not_found', __( 'Source slot not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		$slot_data = $fields[ $from_area ][ $from_slot ];
		unset( $fields[ $from_area ][ $from_slot ] );
		if ( empty( $fields[ $from_area ] ) ) {
			unset( $fields[ $from_area ] );
		}

		if ( ! isset( $fields[ $to_area ] ) ) {
			$fields[ $to_area ] = [];
		}

		// Resolve target position from the supplied placement args.
		// Precedence: before_slot > after_slot > position. Borrowed
		// from block-mcp's ref-relative semantics — agents shouldn't
		// have to count slots to insert "right before the title".
		// Accept `position` either at the top level or nested under
		// `to.position`. Block-MCP-style callers naturally write
		// `{ to: { area, position: "start" } }`, so the nested shape
		// shouldn't silently fall back to "end".
		$position_arg = $payload['position'] ?? ( $to['position'] ?? null );
		$before_slot  = isset( $to['before_slot'] ) ? (string) $to['before_slot'] : '';
		$after_slot   = isset( $to['after_slot'] ) ? (string) $to['after_slot'] : '';
		$position     = $this->resolve_move_position(
			$fields[ $to_area ],
			$position_arg,
			$before_slot,
			$after_slot
		);
		if ( is_wp_error( $position ) ) {
			return $position;
		}

		[ $fields[ $to_area ], $applied_position ] = $this->splice_slot_at_position(
			$fields[ $to_area ],
			$from_slot,
			$slot_data,
			$position
		);

		$this->write_fields( $view_id, $fields );
		$this->bump_version( $view_id );

		return new WP_REST_Response(
			[
				'view_id'  => $view_id,
				'from'     => [
					'area' => $from_area,
					'slot' => $from_slot,
				],
				'to'       => [
					'area'        => $to_area,
					'slot'        => $from_slot,
					'before_slot' => $before_slot ?: null,
					'after_slot'  => $after_slot ?: null,
				],
				'position' => $applied_position,
				'version'  => $this->compute_version( $view_id ),
			],
			200
		);
	}

	/**
	 * Translate the move payload's placement args into a concrete
	 * insertion index against the (already-removed-source) target area.
	 *
	 * Accepted forms (precedence top-down):
	 *   1. `to.before_slot: <slot_uid>` — insert immediately before that slot.
	 *   2. `to.after_slot:  <slot_uid>` — insert immediately after that slot.
	 *   3. `position: 'start' | 'end' | int` — symbolic or numeric index.
	 *      `'end'` (default), negative numbers, and out-of-range numbers
	 *      all mean "append".
	 *
	 * @since 3.0.0
	 *
	 * @param array  $target_area  Slot UID → data map of the destination area.
	 * @param mixed  $position_arg Raw `position` argument from the payload.
	 * @param string $before_slot  `to.before_slot` value (empty when not used).
	 * @param string $after_slot   `to.after_slot` value (empty when not used).
	 *
	 * @return int|\WP_Error Resolved zero-based index, or a 4xx error.
	 *                       when a referenced slot doesn't exist in the area.
	 */
	private function resolve_move_position( array $target_area, $position_arg, string $before_slot, string $after_slot ) {
		$target_uids = array_keys( $target_area );
		$count       = count( $target_uids );

		// A placement ref with unsafe characters can't match any
		// stored slot UID (those are all validated on the way in), so
		// treat it as "no ref" and fall through to the position
		// default rather than echoing the crafted value back in a
		// "not found" error. Valid-shaped-but-absent refs still hit the
		// existing not-found branches below.
		$before_slot_is_safe = '' === $before_slot || $this->is_valid_slot_uid( $before_slot );
		$after_slot_is_safe  = '' === $after_slot || $this->is_valid_slot_uid( $after_slot );
		if ( ! $before_slot_is_safe ) {
			$before_slot = '';
		}
		if ( ! $after_slot_is_safe ) {
			$after_slot = '';
		}

		if ( '' !== $before_slot ) {
			$idx = array_search( $before_slot, $target_uids, true );
			if ( false === $idx ) {
				return new WP_Error(
					'gv_rest_anchor_slot_not_found',
					/* translators: %s: requested slot UID */
					sprintf( __( 'before_slot "%s" not found in the target area. Re-fetch the View config to see current slot UIDs.', 'gk-gravityview' ), $before_slot ),
					[ 'status' => 400 ]
				);
			}
			return (int) $idx;
		}

		if ( '' !== $after_slot ) {
			$idx = array_search( $after_slot, $target_uids, true );
			if ( false === $idx ) {
				return new WP_Error(
					'gv_rest_anchor_slot_not_found',
					/* translators: %s: requested slot UID */
					sprintf( __( 'after_slot "%s" not found in the target area.', 'gk-gravityview' ), $after_slot ),
					[ 'status' => 400 ]
				);
			}
			return (int) $idx + 1;
		}

		// Symbolic / numeric positions. `'start'` → 0; `'end'` → $count
		// (forces append). Negative ints / out-of-range = append.
		if ( 'start' === $position_arg ) {
			return 0;
		}
		if ( 'end' === $position_arg || null === $position_arg ) {
			return $count;
		}
		$position = (int) $position_arg;
		if ( $position < 0 ) {
			return $count;
		}
		return $position;
	}

	/**
	 * Splice a slot into an area's UID-keyed map at a resolved index.
	 *
	 * Shared by `move_field` and `clone_field` — both need the same
	 * "insert at index N into an associative array, preserving key
	 * order" semantics. PHP doesn't ship this primitive; the manual
	 * walk is the canonical idiom.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $area_slots Current UID → data map of the target area.
	 * @param string $new_uid    UID to assign to the inserted slot.
	 * @param mixed  $slot_data  Slot payload to insert.
	 * @param int    $position   Zero-based index. >= count() → append.
	 *
	 * @return array{0:array,1:int} [ newly-ordered area map, applied position ]
	 */
	private function splice_slot_at_position( array $area_slots, string $new_uid, $slot_data, int $position ): array {
		$count = count( $area_slots );

		if ( $position >= $count ) {
			$area_slots[ $new_uid ] = $slot_data;
			return [ $area_slots, $count ];
		}

		$reordered = [];
		$index     = 0;
		foreach ( $area_slots as $uid => $data ) {
			if ( $index === $position ) {
				$reordered[ $new_uid ] = $slot_data;
			}
			$reordered[ $uid ] = $data;
			++$index;
		}
		return [ $reordered, $position ];
	}

	/**
	 * Locate which area a slot UID belongs to.
	 *
	 * Returns `[ $area, $slot_data ]` for the first match across every
	 * area in the field tree, or `null` when the UID doesn't exist.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id The View's post id.
	 * @param string $uid     Slot UID.
	 *
	 * @return array{0:string,1:mixed}|null
	 */
	private function find_slot_area( int $view_id, string $uid ): ?array {
		// Reject caller-supplied UIDs with unsafe characters before
		// using them as a lookup key. Returning null funnels into the
		// existing not-found / 404 path; valid UIDs are unaffected.
		$uid_is_safe = $this->is_valid_slot_uid( $uid );
		if ( ! $uid_is_safe ) {
			return null;
		}
		foreach ( $this->read_fields( $view_id ) as $area_key => $slots ) {
			if ( is_array( $slots ) && isset( $slots[ $uid ] ) ) {
				return [ (string) $area_key, $slots[ $uid ] ];
			}
		}
		return null;
	}

	/**
	 * Whether a field-tree area key belongs to a Layout Builder row.
	 *
	 * Row identity is encoded as the trailing `::{row_uid}` segment of
	 * keys like `directory_gravityview-layout-builder-left::1-2::abc123`.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key     Area key from the field tree.
	 * @param string $row_uid Row UID to match against.
	 *
	 * @return bool
	 */
	private function area_key_belongs_to_row( string $key, string $row_uid ): bool {
		$parts = explode( '::', $key );
		$count = count( $parts );
		// Layout Builder keys have at least three `::`-separated parts:
		// `{prefix-areaid}::{row_type}::{row_uid}`. Anything shorter
		// is a non-grid area key (List/Table/DIY).
		return $count >= 3 && $parts[ $count - 1 ] === $row_uid;
	}

	/**
	 * Deep-copy a slot's settings payload without sharing array identity.
	 *
	 * `array_replace_recursive([], $source)` is the only safe one-liner
	 * — `+ $source` and `array_merge` both preserve references in PHP's
	 * by-default copy-on-write semantics for arrays-of-arrays. Wrapping
	 * it in a named helper makes the intent explicit and prevents future
	 * callers from dropping the `is_array` guard.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $slot_data Slot record (typically an associative array).
	 *
	 * @return mixed Deep-copied slot data, or the value unchanged when not an array.
	 */
	private function deep_clone_slot( $slot_data ) {
		return is_array( $slot_data ) ? array_replace_recursive( [], $slot_data ) : $slot_data;
	}

	/**
	 * POST /views/{id}/fields/_clone — duplicate a field slot, optionally
	 * placing the clone in a different area or at a specific position.
	 *
	 * Body: `{ uid, to: { area?, before_slot?, after_slot?, position? } }`.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function clone_field( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload    = $request->get_json_params();
		$source_uid = isset( $payload['uid'] ) ? (string) $payload['uid'] : '';
		if ( '' === $source_uid ) {
			return new WP_Error( 'gv_rest_missing_uid', __( 'uid is required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$to       = is_array( $payload['to'] ?? null ) ? $payload['to'] : [];
		$to_area  = isset( $to['area'] ) ? (string) $to['area'] : '';
		$position = $to['position'] ?? null;
		$before   = isset( $to['before_slot'] ) ? (string) $to['before_slot'] : '';
		$after    = isset( $to['after_slot'] ) ? (string) $to['after_slot'] : '';

		$found = $this->find_slot_area( $view_id, $source_uid );
		if ( null === $found ) {
			return new WP_Error( 'gv_rest_slot_not_found', __( 'Source slot not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}
		[ $source_area, $source_data ] = $found;
		$fields                        = $this->read_fields( $view_id );

		if ( '' === $to_area ) {
			$to_area = $source_area;
		}
		if ( ! isset( $fields[ $to_area ] ) ) {
			$fields[ $to_area ] = [];
		}

		// Default position when no targeting args were supplied AND the
		// clone stays in the source area: insert immediately after the
		// source. Cross-area clones with no targeting append.
		if ( null === $position && '' === $before && '' === $after && $to_area === $source_area ) {
			$after = $source_uid;
		}

		$resolved_position = $this->resolve_move_position(
			$fields[ $to_area ],
			$position,
			$before,
			$after
		);
		if ( is_wp_error( $resolved_position ) ) {
			return $resolved_position;
		}

		$new_uid    = $this->generate_slot_uid();
		$clone_data = $this->deep_clone_slot( $source_data );

		[ $fields[ $to_area ], ] = $this->splice_slot_at_position(
			$fields[ $to_area ],
			$new_uid,
			$clone_data,
			$resolved_position
		);

		$this->write_fields( $view_id, $fields );
		$this->bump_version( $view_id );

		return new WP_REST_Response(
			[
				'view_id' => $view_id,
				'area'    => $to_area,
				'slot'    => $new_uid,
				'source'  => [
					'area' => $source_area,
					'slot' => $source_uid,
				],
				'values'  => $clone_data,
				'version' => $this->compute_version( $view_id ),
			],
			201
		);
	}

	/**
	 * GET /views/{id}/fields/_one — combined config + schema + rendered_html
	 * for a single slot (resolved by UID alone).
	 *
	 * Collapses three separate fetches a page builder would otherwise need
	 * (`get_config`, `get_field_settings_schema_one`, `render_field`) into
	 * a single round-trip. The schema is identical to the canonical
	 * `/settings-schema` endpoint's output.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_field( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];
		$uid     = (string) ( $request->get_param( 'uid' ) ?? '' );
		if ( '' === $uid ) {
			return new WP_Error( 'gv_rest_missing_uid', __( 'uid is required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$found = $this->find_slot_area( $view_id, $uid );
		if ( null === $found ) {
			return new WP_Error( 'gv_rest_slot_not_found', __( 'Slot not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}
		[ $area, $config ] = $found;

		$schema_request = new WP_REST_Request( 'GET', '' );
		$schema_request->set_param( 'id', $view_id );
		$schema_request->set_param( 'area', $area );
		$schema_request->set_param( 'slot', $uid );
		$schema_response = $this->get_field_settings_schema_one( $schema_request );
		$schema_data     = is_wp_error( $schema_response ) ? [] : ( method_exists( $schema_response, 'get_data' ) ? $schema_response->get_data() : [] );
		$schema          = isset( $schema_data['settings'] ) && is_array( $schema_data['settings'] )
			? $schema_data['settings']
			: ( is_array( $schema_data ) ? $schema_data : [] );

		$render_request = new WP_REST_Request( 'POST', '' );
		$render_request->set_param( 'id', $view_id );
		$render_request->set_param( 'area', $area );
		$render_request->set_param( 'slot', $uid );
		$render_response = $this->render_field( $render_request );
		$render_data     = is_wp_error( $render_response ) ? [] : ( method_exists( $render_response, 'get_data' ) ? $render_response->get_data() : [] );
		$rendered_html   = is_array( $render_data ) ? (string) ( $render_data['html'] ?? '' ) : '';

		return new WP_REST_Response(
			[
				'view_id'       => $view_id,
				'uid'           => $uid,
				'area'          => $area,
				'config'        => $config,
				'schema'        => $schema,
				'rendered_html' => $rendered_html,
				'version'       => $this->compute_version( $view_id ),
			],
			200
		);
	}

	/**
	 * POST /views/{id}/render/_partial — render a subtree (zone, row, or
	 * explicit slot list) so page-builder clients can re-paint only the
	 * dirty section after a token / field edit. Delegates to `render_field`
	 * per slot so the per-layout wrapper markup matches the page render.
	 *
	 * Body shape:
	 *   { "scope": { "type": "zone",  "area":    "<area-key>" } }
	 *   { "scope": { "type": "row",   "row_uid": "<row-uid>" } }
	 *   { "scope": { "type": "slots", "slots":   [ { area, slot }, ... ] } }
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function render_partial( WP_REST_Request $request ) {
		$view_id   = (int) $request['id'];
		$raw_scope = $request->get_param( 'scope' );
		$scope     = is_array( $raw_scope ) ? $raw_scope : [];
		$type      = isset( $scope['type'] ) ? (string) $scope['type'] : '';

		if ( ! in_array( $type, [ 'zone', 'row', 'slots' ], true ) ) {
			return new WP_Error( 'gv_rest_invalid_scope', __( 'scope.type must be one of: zone, row, slots.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$tree  = $this->read_fields( $view_id );
		$pairs = [];

		switch ( $type ) {
			case 'zone':
				$area = isset( $scope['area'] ) ? (string) $scope['area'] : '';
				if ( '' === $area ) {
					return new WP_Error( 'gv_rest_invalid_scope', __( 'scope.area is required for zone scope.', 'gk-gravityview' ), [ 'status' => 400 ] );
				}
				// 404 only when the area key doesn't exist. An EMPTY
				// area is a valid state — re-rendering returns
				// `{rendered: []}`, not an error.
				if ( ! array_key_exists( $area, $tree ) ) {
					return new WP_Error( 'gv_rest_area_not_found', __( 'Area not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
				}
				foreach ( array_keys( (array) $tree[ $area ] ) as $slot ) {
					$pairs[] = [ $area, (string) $slot ];
				}
				break;

			case 'row':
				$row_uid = isset( $scope['row_uid'] ) ? (string) $scope['row_uid'] : '';
				if ( '' === $row_uid ) {
					return new WP_Error( 'gv_rest_invalid_scope', __( 'scope.row_uid is required for row scope.', 'gk-gravityview' ), [ 'status' => 400 ] );
				}
				$pairs_before = count( $pairs );
				foreach ( $tree as $key => $slots ) {
					if ( ! $this->area_key_belongs_to_row( (string) $key, $row_uid ) ) {
						continue;
					}
					foreach ( array_keys( (array) $slots ) as $slot ) {
						$pairs[] = [ (string) $key, (string) $slot ];
					}
				}
				if ( count( $pairs ) === $pairs_before ) {
					return new WP_Error( 'gv_rest_row_not_found', __( 'Row not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
				}
				break;

			case 'slots':
				$slots_in = is_array( $scope['slots'] ?? null ) ? $scope['slots'] : [];
				foreach ( $slots_in as $entry ) {
					if ( ! is_array( $entry ) ) {
						continue;
					}
					$area = isset( $entry['area'] ) ? (string) $entry['area'] : '';
					$slot = isset( $entry['slot'] ) ? (string) $entry['slot'] : '';
					if ( '' === $area || '' === $slot ) {
						continue;
					}
					$pairs[] = [ $area, $slot ];
				}
				if ( empty( $pairs ) ) {
					return new WP_Error( 'gv_rest_invalid_scope', __( 'scope.slots must be a non-empty array of { area, slot } pairs.', 'gk-gravityview' ), [ 'status' => 400 ] );
				}
				break;
		}

		$rendered = [];
		foreach ( $pairs as [ $area, $slot ] ) {
			$inner = new WP_REST_Request( 'POST', '' );
			$inner->set_param( 'id', $view_id );
			$inner->set_param( 'area', $area );
			$inner->set_param( 'slot', $slot );
			$res = $this->render_field( $inner );
			if ( is_wp_error( $res ) ) {
				$rendered[] = [
					'area'  => $area,
					'slot'  => $slot,
					'error' => $res->get_error_code(),
				];
				continue;
			}
			$body       = method_exists( $res, 'get_data' ) ? $res->get_data() : [];
			$html       = is_array( $body ) ? ( $body['html'] ?? '' ) : '';
			$rendered[] = [
				'area' => $area,
				'slot' => $slot,
				'html' => (string) $html,
			];
		}

		return new WP_REST_Response(
			[
				'view_id'  => $view_id,
				'scope'    => $scope,
				'rendered' => $rendered,
				'version'  => $this->compute_version( $view_id ),
			],
			200
		);
	}

	/**
	 * POST /views/{id}/grid/_rows/_clone — duplicate a Layout Builder row.
	 *
	 * Finds every area key suffixed `::{row_uid}`, generates a fresh row_uid,
	 * copies the slot tree into new area keys (with new slot UIDs), and
	 * inserts those keys immediately after the source row's keys in the
	 * field tree (preserving Layout Builder row order).
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function clone_grid_row( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload = $request->get_json_params();
		$row_uid = isset( $payload['row_uid'] ) ? (string) $payload['row_uid'] : '';
		if ( '' === $row_uid ) {
			return new WP_Error( 'gv_rest_missing_row_uid', __( 'row_uid is required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$tree = $this->read_fields( $view_id );

		$source_keys = [];
		$source_type = '';
		foreach ( array_keys( $tree ) as $key ) {
			$key_str = (string) $key;
			if ( ! $this->area_key_belongs_to_row( $key_str, $row_uid ) ) {
				continue;
			}
			$source_keys[] = $key;
			if ( '' === $source_type ) {
				$parts       = explode( '::', $key_str );
				$source_type = $parts[ count( $parts ) - 2 ];
			}
		}

		if ( empty( $source_keys ) ) {
			return new WP_Error( 'gv_rest_row_not_found', __( 'Source row not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		$new_row_uid    = Grid::uid();
		$suffix_len     = strlen( '::' . $row_uid );
		$new_key_suffix = '::' . $new_row_uid;
		$cloned_blocks  = [];
		foreach ( $source_keys as $key ) {
			$new_key = substr( (string) $key, 0, -$suffix_len ) . $new_key_suffix;
			$slots   = is_array( $tree[ $key ] ) ? $tree[ $key ] : [];

			$rekeyed = [];
			foreach ( $slots as $slot_data ) {
				$rekeyed[ $this->generate_slot_uid() ] = $this->deep_clone_slot( $slot_data );
			}
			$cloned_blocks[ $new_key ] = $rekeyed;
		}

		$last_source_key = end( $source_keys );
		$next_tree       = [];
		foreach ( $tree as $key => $slots ) {
			$next_tree[ $key ] = $slots;
			if ( $key === $last_source_key ) {
				foreach ( $cloned_blocks as $clone_key => $clone_slots ) {
					$next_tree[ $clone_key ] = $clone_slots;
				}
			}
		}

		$this->write_fields( $view_id, $next_tree );
		$this->bump_version( $view_id );

		return new WP_REST_Response(
			[
				'view_id'   => $view_id,
				'row_uid'   => $new_row_uid,
				'type'      => $source_type,
				'area_keys' => array_keys( $cloned_blocks ),
				'source'    => [
					'row_uid'   => $row_uid,
					'area_keys' => $source_keys,
				],
				'version'   => $this->compute_version( $view_id ),
			],
			201
		);
	}

	/**
	 * PATCH /views/{id}/template — switch template with policy.
	 *
	 * Policy:
	 *   - `discard` (default): replaces the template and clears fields/widgets.
	 *     Matches legacy editor behavior.
	 *   - `preserve` (alias: `keep`): replaces the template but leaves
	 *     field/widget meta intact; fields configured in areas the new
	 *     template lacks won't render until they're moved or removed.
	 *     Anything that isn't `discard` is treated as preserve.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function patch_template( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload     = $request->get_json_params();
		$template_id = isset( $payload['template_id'] ) ? (string) $payload['template_id'] : (string) $request->get_param( 'template_id' );
		$zone        = (string) ( $payload['zone'] ?? $request->get_param( 'zone' ) ?: 'directory' );
		$policy      = isset( $payload['policy'] ) ? (string) $payload['policy'] : 'discard';

		if ( '' === $template_id ) {
			return new WP_Error( 'gv_rest_missing_template', __( 'template_id required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		if ( ! in_array( $zone, [ 'directory', 'single', 'edit' ], true ) ) {
			return new WP_Error( 'gv_rest_invalid_zone', __( 'zone must be one of: directory, single, edit.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// Reject unregistered template ids before any meta write —
		// without this guard a caller could persist a typo / hostile
		// string into the per-zone template meta and strand the View
		// on a template the rest of the codebase has never heard of.
		$invalid = $this->validate_template_id_or_error( $template_id, 'template_id' );
		if ( $invalid instanceof \WP_Error ) {
			return $invalid;
		}

		// Per-zone template write. The directory zone also seeds the
		// `template` key inside template_settings because legacy code
		// paths read it from there. Single + edit don't have that
		// duplication.
		$meta_key = $this->template_meta_key_for_zone( $zone );
		update_post_meta( $view_id, $meta_key, $template_id );

		if ( 'directory' === $zone ) {
			$ts             = (array) get_post_meta( $view_id, self::META_TEMPLATE_SETTINGS, true );
			$ts['template'] = $template_id;
			update_post_meta( $view_id, self::META_TEMPLATE_SETTINGS, $ts );
		}

		if ( 'discard' === $policy ) {
			// Only discard the FIELDS / WIDGETS for the affected zone.
			// Per-zone keys live in the same meta entry (`directory_*`,
			// `single_*`, `edit_*`) — strip just the touched zone's
			// keys so the other zones' configurations survive.
			$this->discard_zone_entries( $view_id, $zone );
		}

		$this->bump_version( $view_id );

		return $this->get_config( $request );
	}

	/**
	 * Strip every field-tree / widget-tree entry whose key starts with
	 * `{zone}_` from the View's storage. Used by `patch_template` when
	 * the discard policy is on so a template change doesn't leave
	 * orphaned area placements that no longer match.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id
	 * @param string $zone
	 */
	private function discard_zone_entries( int $view_id, string $zone ): void {
		$prefix = $zone . '_';
		foreach ( [ '_gravityview_directory_fields', self::META_WIDGETS ] as $meta_key ) {
			$tree = get_post_meta( $view_id, $meta_key, true );
			if ( ! is_array( $tree ) ) {
				continue;
			}
			$next = [];
			foreach ( $tree as $key => $slots ) {
				if ( 0 === strpos( $key, $prefix ) ) {
					continue;
				}
				$next[ $key ] = $slots;
			}
			update_post_meta( $view_id, $meta_key, $next );
		}
	}

	/**
	 * PATCH /views/{id}/template-settings — view-level template settings.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function patch_template_settings( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload = $request->get_json_params();

		if ( ! is_array( $payload ) ) {
			return new WP_Error( 'gv_rest_invalid_payload', __( 'JSON payload required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$merge_error = $this->merge_template_settings( $view_id, $payload );
		if ( is_wp_error( $merge_error ) ) {
			return $merge_error;
		}
		$this->bump_version( $view_id );

		return new WP_REST_Response(
			[
				'view_id'           => $view_id,
				'template_settings' => $this->read_template_settings( $view_id ),
				'version'           => $this->compute_version( $view_id ),
			],
			200
		);
	}

	/**
	 * PATCH /views/{id}/search-criteria — pagination / sort.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function patch_search_criteria( WP_REST_Request $request ) {
		// search_criteria values live in the same flat template_settings array.
		return $this->patch_template_settings( $request );
	}

	/**
	 * PATCH /views/{id}/widgets/{area}/{slot} — single widget edit.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function patch_widget( WP_REST_Request $request ) {
		$view_id  = (int) $request['id'];
		$area     = (string) $request['area'];
		$slot_uid = (string) $request['slot'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$widgets = $this->read_widgets( $view_id );
		if ( ! isset( $widgets[ $area ][ $slot_uid ] ) ) {
			return new WP_Error( 'gv_rest_widget_not_found', __( 'Widget slot not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		$payload = $request->get_json_params();
		if ( ! is_array( $payload ) ) {
			return new WP_Error( 'gv_rest_invalid_payload', __( 'JSON payload required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$this->apply_warnings = [];
		foreach ( $payload as $key => $value ) {
			$key = sanitize_key( $key );
			if ( '' === $key ) {
				continue;
			}
			if ( null === $value ) {
				unset( $widgets[ $area ][ $slot_uid ][ $key ] );
				continue;
			}
			if ( 'conditional_logic' === $key ) {
				$cl = $this->validate_conditional_logic( $value );
				if ( $cl['rejected'] ) {
					$this->record_warning( $area, $slot_uid, $key, $cl['reason'] );
				}
				$widgets[ $area ][ $slot_uid ][ $key ] = $cl['value'];
				continue;
			}
			$widgets[ $area ][ $slot_uid ][ $key ] = $this->sanitize_setting_value( $value );
		}

		$this->write_widgets( $view_id, $widgets );
		$this->bump_version( $view_id );

		$response_body = [
			'view_id' => $view_id,
			'area'    => $area,
			'slot'    => $slot_uid,
			'values'  => $widgets[ $area ][ $slot_uid ],
			'version' => $this->compute_version( $view_id ),
		];
		if ( ! empty( $this->apply_warnings ) ) {
			$response_body['warnings'] = $this->apply_warnings;
		}
		return new WP_REST_Response(
			$response_body,
			200
		);
	}

	/**
	 * DELETE /views/{id}/widgets/{area}/{slot} — remove a widget slot.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_widget( WP_REST_Request $request ) {
		$view_id  = (int) $request['id'];
		$area     = (string) $request['area'];
		$slot_uid = (string) $request['slot'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$widgets = $this->read_widgets( $view_id );
		if ( ! isset( $widgets[ $area ][ $slot_uid ] ) ) {
			return new WP_Error( 'gv_rest_widget_not_found', __( 'Widget slot not found.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		unset( $widgets[ $area ][ $slot_uid ] );
		if ( empty( $widgets[ $area ] ) ) {
			unset( $widgets[ $area ] );
		}

		$this->write_widgets( $view_id, $widgets );
		$this->bump_version( $view_id );

		return new WP_REST_Response(
            [
				'view_id' => $view_id,
				'area'    => $area,
				'slot'    => $slot_uid,
				'version' => $this->compute_version( $view_id ),
			],
			200
        );
	}

	/**
	 * POST /views/{id}/widgets/{area}/_slots — create widget slot.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_widget_slot( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];
		$area    = (string) $request['area'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload = $request->get_json_params();
		// Accept either `widget_id` (legacy) or `field_id` (matches the
		// create_field_slot contract — `gv_add_view_widget`'s payload
		// shape mirrors `gv_add_view_field` for caller consistency).
		// Accept widget_id (canonical), or its aliases field_id (mirrors the
		// add-view-field contract) and type (the word the schema tools use) so
		// callers aren't tripped by the identity-key inconsistency. Scalar-only:
		// a non-scalar would cast to "Array" and create a broken widget slot.
		$widget_id  = '';
		$type_is_id = false;
		foreach ( [ 'widget_id', 'field_id', 'type' ] as $id_key ) {
			if ( '' !== $widget_id || ! isset( $payload[ $id_key ] ) || ! is_scalar( $payload[ $id_key ] ) ) {
				continue;
			}
			$candidate = (string) $payload[ $id_key ];
			// `type` is only an identity alias when it names a registered widget;
			// otherwise it's a real widget setting (e.g. export_link csv/tsv).
			if ( 'type' === $id_key && ! $this->is_registered_widget( $candidate ) ) {
				continue;
			}
			$widget_id  = $candidate;
			$type_is_id = ( 'type' === $id_key );
		}

		if ( '' === $widget_id ) {
			return new WP_Error( 'gv_rest_missing_widget_id', __( 'widget_id is required (the widget to instantiate, e.g. "search_bar", "page_info"). To add a search bar with fields in one call, use gv_search_bar_add.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$slot_uid    = $this->generate_slot_uid();
		$widgets     = $this->read_widgets( $view_id );
		$slot_record = [
			'id'    => $widget_id,
			'label' => isset( $payload['label'] ) ? (string) $payload['label'] : '',
		];

		// Compute the widget's settings schema once so every
		// textarea/html/extension-slot setting registered by the
		// widget (or by `gk/gravityview/rest/widget-schema-extras`
		// add-ons) routes through `wp_kses_post`/raw sanitization on
		// first write. Falling back to an empty schema would silently
		// downgrade everything except the hardcoded `content`/`conditional_logic`
		// fallbacks to `sanitize_text_field`, stripping any rich
		// markup add-ons declared in their widget schema.
		$widget_schema = $this->compute_widget_schema( $widget_id );

		// If the caller sent nested search-field settings inline with
		// a search_bar widget, vet them through the same allow-list
		// the dedicated /search-fields endpoint enforces. Without
		// this, a single create_widget_slot call could persist a
		// search_bar with `search_fields_section[…].input_type =
		// "datepiker"`, bypassing the per-field validation entirely.
		if ( is_array( $payload ) ) {
			$nested_check = $this->validate_search_fields_section_payload( $view_id, $payload );
			if ( is_wp_error( $nested_check ) ) {
				return $nested_check;
			}
		}

		// Persist every additional setting the caller sent (e.g.
		// search_bar's `search_layout`, `search_fields_section`,
		// `search_clear`, custom_content widget's `content`). Prior
		// implementation silently dropped everything beyond
		// id+label, so a widget created via the API came up with
		// none of its schema-defined settings populated. Reuse the
		// same sanitization profile patch_widget runs.
		$this->apply_warnings = [];
		// `type` is a real widget setting for some widgets (e.g. export_link's
		// csv/tsv) — only reserve it when it was consumed AS the widget id.
		$reserved             = [ 'slot', 'widget_id', 'field_id', 'id', 'label' ];
		if ( $type_is_id ) {
			$reserved[] = 'type';
		}
		if ( is_array( $payload ) ) {
			foreach ( $payload as $key => $value ) {
				$key = sanitize_key( $key );
				if ( '' === $key || in_array( $key, $reserved, true ) ) {
					continue;
				}
				if ( null === $value ) {
					continue;
				}
				if ( 'conditional_logic' === $key ) {
					$cl = $this->validate_conditional_logic( $value );
					if ( $cl['rejected'] ) {
						$this->record_warning( $area, $slot_uid, $key, $cl['reason'] );
					}
					$slot_record[ $key ] = $cl['value'];
					continue;
				}
				$mode_for_value      = $this->sanitize_mode_for( $widget_schema, $key );
				$slot_record[ $key ] = $this->sanitize_setting_value( $value, $mode_for_value );
			}
		}

		$widgets[ $area ][ $slot_uid ] = $slot_record;

		// Auto-migrate legacy search_bar payloads to the modern
		// `search_fields_section` shape so a brand-new widget created
		// via REST never persists the deprecated `search_fields` JSON.
		// Mirrors the same call apply_collection makes on save.
		if ( 'search_bar' === $widget_id ) {
			$widgets[ $area ][ $slot_uid ] = $this->migrate_search_bar_to_modern( $widgets[ $area ][ $slot_uid ], $view_id );

			// Run every inline search field through the Search_Field domain
			// normaliser — same as apply_collection — so a search_bar created
			// with a nested `search_fields_section` can't persist non-canonical
			// entries (missing form_id/defaults, the `input` alias) that the
			// renderer or the legacy admin metabox would drop on next save.
			$nested_section = $widgets[ $area ][ $slot_uid ]['search_fields_section'] ?? null;
			if ( is_array( $nested_section ) ) {
				foreach ( $nested_section as $nested_position => $nested_row ) {
					if ( ! is_array( $nested_row ) ) {
						continue;
					}
					foreach ( $nested_row as $nested_uid => $nested_field ) {
						if ( 'area_settings' === $nested_uid || ! is_array( $nested_field ) ) {
							continue;
						}
						// Preserve caller-supplied add-on per-field settings while
						// canonicalising the core keys (see apply_collection).
						$widgets[ $area ][ $slot_uid ]['search_fields_section'][ $nested_position ][ $nested_uid ] =
							( new SearchFieldResolver( $view_id ) )->normalize( $nested_field );
					}
				}
			}
		}

		$this->write_widgets( $view_id, $widgets );
		$this->bump_version( $view_id );

		$response_body = [
			'view_id' => $view_id,
			'area'    => $area,
			'slot'    => $slot_uid,
			'values'  => $widgets[ $area ][ $slot_uid ],
			'version' => $this->compute_version( $view_id ),
		];
		if ( ! empty( $this->apply_warnings ) ) {
			$response_body['warnings'] = $this->apply_warnings;
		}
		return new WP_REST_Response(
			$response_body,
			201
		);
	}

	// ===================================================================
	// Search Bar internal slot CRUD (modern shape only)
	// ===================================================================

	/**
	 * Ensure a View has a search_bar widget carrying the requested search
	 * fields — the one-call "add a search bar with these fields" primitive
	 * behind gk-gravityview/search-bar-add.
	 *
	 * Hides the 5-piece slot identity a small model can't assemble: it
	 * find-or-creates a single search_bar in the default widget zone, then
	 * upserts each requested field (idempotent by field id) into the modern
	 * `search_fields_section`, all in ONE version-bumped transaction.
	 *
	 *   - 0 search bars  → create one in `area` (default header_top)
	 *   - 1 search bar   → reuse it (ignore `zone` unless explicitly targeted)
	 *   - 2+ search bars → 400 gv_rest_ambiguous_search_bar with candidates,
	 *                      unless `area` + `widget_slot` name one explicitly
	 *
	 * Body:
	 *   {
	 *     zone?:        "header" | "footer"  (default "header" → header_top)
	 *     area?:        explicit widget area key (overrides zone)
	 *     widget_slot?: explicit search_bar slot uid (with area, targets one bar)
	 *     position?:    search position bucket (default "search-general_top")
	 *     fields:       [ { field_id, input?, label? }, ... ]
	 *   }
	 *
	 * Field input is validated against the same per-field allow-list the
	 * dedicated create_search_field_slot endpoint enforces, and every field is
	 * normalised through normalize_search_field so the widget lands on
	 * the modern, render-correct, legacy-mirrored shape.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function add_search_bar( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload = $request->get_json_params() ?: [];
		$fields  = isset( $payload['fields'] ) && is_array( $payload['fields'] ) ? $payload['fields'] : [];
		if ( empty( $fields ) ) {
			return new WP_Error( 'gv_rest_invalid_fields', __( 'fields[] is required and must list at least one { field_id } object.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// Normalise the requested position to a real, renderable search-bar row
		// (a bare zone like "search-general" maps to its default bucket).
		$position = isset( $payload['position'] ) && '' !== (string) $payload['position']
			? (string) $payload['position']
			: 'search-general_top';
		$position = $this->normalize_search_position( $position );
		if ( '' === $position ) {
			return new WP_Error(
				'gv_rest_invalid_position',
				__( 'position must be "search-general_top" or "search-advanced_top" (or the bare zone "search-general" / "search-advanced").', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		// Map a friendly zone family to a placeable widget meta-zone. A caller may
		// also pass a full meta-zone (e.g. "header_top") directly. The primary
		// area is the first canonical grid area id, so this tracks the editor.
		$zone = isset( $payload['zone'] ) ? (string) $payload['zone'] : 'header';
		if ( false !== strpos( $zone, '_' ) ) {
			$default_area = $zone;
		} else {
			$area_ids     = $this->default_widget_area_ids();
			$default_area = ( 'footer' === $zone ? 'footer' : 'header' ) . '_' . ( $area_ids[0] ?? 'top' );
		}
		$explicit_area = isset( $payload['area'] ) ? (string) $payload['area'] : '';
		$explicit_slot = isset( $payload['widget_slot'] ) ? (string) $payload['widget_slot'] : '';

		$widgets          = $this->read_widgets( $view_id );
		$original_widgets = $widgets;

		$target = $this->resolve_or_create_search_bar( $view_id, $widgets, $explicit_area, $explicit_slot, $default_area );
		if ( is_wp_error( $target ) ) {
			return $target;
		}
		$target_area    = $target['area'];
		$target_slot    = $target['slot'];
		$created_widget = $target['created'];

		// Normalise the target bar to the modern shape before editing.
		$widget = $this->migrate_search_bar_to_modern( $widgets[ $target_area ][ $target_slot ], $view_id );

		// migrate_search_bar_to_modern returns the widget UNCHANGED (legacy
		// search_fields intact, no section) when it cannot convert — corrupt
		// JSON, the collection class unavailable, an empty parse. Building a
		// fresh section and dropping search_fields now would erase those legacy
		// fields, so abort instead of risking data loss.
		if ( ! empty( $widget['search_fields'] ) ) {
			return new WP_Error(
				'gv_rest_search_bar_migration_failed',
				__( 'This search bar has legacy search fields that could not be migrated to the modern format; aborting to avoid data loss. Inspect the View configuration before adding fields via this API.', 'gk-gravityview' ),
				[ 'status' => 409 ]
			);
		}

		$section = isset( $widget['search_fields_section'] ) && is_array( $widget['search_fields_section'] )
			? $widget['search_fields_section']
			: [];

		$applied = [];
		foreach ( $fields as $raw ) {
			if ( ! is_array( $raw ) ) {
				continue;
			}
			$result = $this->upsert_search_field_into_section( $section, $view_id, $raw, $position );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
			$applied[] = $result;
		}

		$widget['search_fields_section'] = $section;
		// Drop legacy key so the widget is fully on the modern shape.
		unset( $widget['search_fields'] );
		$widgets[ $target_area ][ $target_slot ] = $widget;

		// Idempotent: only persist (and bump the version) when something actually
		// changed, so a no-op retry doesn't churn the ETag and 412 other clients.
		if ( $widgets !== $original_widgets ) {
			$this->write_widgets( $view_id, $widgets );
			$this->bump_version( $view_id );
		}

		return new WP_REST_Response(
			[
				'view_id'        => $view_id,
				'widget_area'    => $target_area,
				'widget_slot'    => $target_slot,
				'created_widget' => $created_widget,
				'fields'         => $applied,
				'version'        => $this->compute_version( $view_id ),
			],
			$created_widget ? 201 : 200
		);
	}

	/**
	 * Resolve the search_bar this request targets, creating one when the View has
	 * none. Reuses the sole existing bar when unambiguous; requires area +
	 * widget_slot to disambiguate when the View has several. On create, the new
	 * bar is added to $widgets (passed by reference).
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id       View id.
	 * @param array  $widgets       Widgets tree, mutated when a bar is created.
	 * @param string $explicit_area Caller-supplied target area ('' if none).
	 * @param string $explicit_slot Caller-supplied target widget slot ('' if none).
	 * @param string $default_area  Meta-zone to create the bar in when none exists.
	 *
	 * @return array{area:string,slot:string,created:bool}|WP_Error
	 */
	private function resolve_or_create_search_bar( int $view_id, array &$widgets, string $explicit_area, string $explicit_slot, string $default_area ) {
		// widget_slot only makes sense paired with the area it lives in.
		if ( '' !== $explicit_slot && '' === $explicit_area ) {
			return new WP_Error( 'gv_rest_invalid_target', __( 'widget_slot requires area (the widget area the target search_bar lives in).', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// Locate every existing search_bar so we reuse rather than duplicate.
		$found = [];
		foreach ( $widgets as $area_key => $slots ) {
			foreach ( (array) $slots as $slot_uid => $widget ) {
				if ( is_array( $widget ) && 'search_bar' === ( $widget['id'] ?? '' ) ) {
					$found[] = [ 'area' => (string) $area_key, 'slot' => (string) $slot_uid ];
				}
			}
		}

		// Explicit target: it must already be a search_bar.
		if ( '' !== $explicit_area && '' !== $explicit_slot ) {
			$is_bar = isset( $widgets[ $explicit_area ][ $explicit_slot ] )
				&& 'search_bar' === ( $widgets[ $explicit_area ][ $explicit_slot ]['id'] ?? '' );
			if ( ! $is_bar ) {
				return new WP_Error( 'gv_rest_search_bar_not_found', __( 'No search_bar widget exists at the given area + widget_slot.', 'gk-gravityview' ), [ 'status' => 404 ] );
			}
			return [ 'area' => $explicit_area, 'slot' => $explicit_slot, 'created' => false ];
		}

		// Exactly one bar: reuse it.
		if ( 1 === count( $found ) ) {
			return [ 'area' => $found[0]['area'], 'slot' => $found[0]['slot'], 'created' => false ];
		}

		// Several bars: never guess — make the caller disambiguate.
		if ( count( $found ) > 1 ) {
			return new WP_Error(
				'gv_rest_ambiguous_search_bar',
				__( 'This View has more than one search bar. Pass area + widget_slot to choose which one (see candidates).', 'gk-gravityview' ),
				[ 'status' => 400, 'candidates' => $found ]
			);
		}

		// None exist: create one in the explicit area, else the zone default.
		$target_area = '' !== $explicit_area ? $explicit_area : $default_area;
		$area_check  = $this->assert_placeable_widget_area( $target_area );
		if ( is_wp_error( $area_check ) ) {
			return $area_check;
		}
		$target_slot                             = $this->generate_slot_uid();
		$widgets[ $target_area ][ $target_slot ] = $this->migrate_search_bar_to_modern(
			[ 'id' => 'search_bar', 'label' => '' ],
			$view_id
		);
		return [ 'area' => $target_area, 'slot' => $target_slot, 'created' => true ];
	}

	/**
	 * Add or update ONE search field inside a search_fields_section (passed by
	 * reference). Resolves an existing slot first (so a bare id addressing a
	 * stored joined-form field validates against the right form), enforces the
	 * searchable + input-type gates, then merges settings into the matched slot
	 * or creates a new one at $position.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $section  search_fields_section tree, mutated in place.
	 * @param int    $view_id  View id.
	 * @param array  $raw      One caller-supplied field payload.
	 * @param string $position Bucket to create a new field in.
	 *
	 * @return array{field_id:string,position:string,search_slot:string,created:bool}|WP_Error
	 */
	private function upsert_search_field_into_section( array &$section, int $view_id, array $raw, string $position ) {
		$field_id = (string) ( $raw['field_id'] ?? $raw['id'] ?? '' );
		if ( '' === $field_id ) {
			return new WP_Error( 'gv_rest_invalid_fields', __( 'Each fields[] item needs a field_id (a GF field id or a virtual id such as search_all, submit, search_mode).', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$resolver = new SearchFieldResolver( $view_id );

		// Find an existing slot FIRST so an update keyed by a bare id resolves to
		// the stored (possibly form-qualified) field before validation.
		$matches = SearchFieldResolver::find_slots( $section, $field_id );
		if ( count( $matches ) > 1 ) {
			// More than one stored slot matches — never guess which to update. A
			// bare id on a Multiple Forms View resolves with the qualified id;
			// genuine duplicates must be de-duped first.
			$is_unqualified = false === strpos( $field_id, '::' );
			if ( $is_unqualified ) {
				// translators: %s: the field ID string.
				$hint = sprintf( __( 'Target one with the form-qualified id "{form_id}::%s".', 'gk-gravityview' ), $field_id );
			} else {
				$hint = __( 'Duplicate slots exist for this field — remove the extras with gv_search_field_remove first.', 'gk-gravityview' );
			}
			return new WP_Error(
				'gv_rest_ambiguous_search_field',
				sprintf(
					/* translators: 1: field id, 2: corrective hint */
					__( 'Field id "%1$s" matches more than one search field. %2$s', 'gk-gravityview' ),
					$field_id,
					$hint
				),
				[ 'status' => 400 ]
			);
		}

		$is_update = ( 1 === count( $matches ) );
		// An UPDATE validates against the stored field's own (qualified) identity
		// so a bare id addressing a joined-form field resolves to the right form;
		// a CREATE validates the requested id.
		$canonical_id = $field_id;
		if ( $is_update ) {
			$derived = $this->stored_search_field_identity( $section, $matches[0][0], $matches[0][1] );
			if ( '' !== $derived ) {
				$canonical_id = $derived;
			}
		}

		// The field must resolve to a real searchable field on a form this View
		// searches, otherwise it would persist a slot that never renders.
		$field_check = $resolver->assert_searchable_id( $canonical_id );
		if ( is_wp_error( $field_check ) ) {
			return $field_check;
		}

		$input = (string) ( $raw['input'] ?? $raw['input_type'] ?? '' );
		if ( '' !== $input ) {
			$valid = $resolver->valid_input_types( $canonical_id );
			if ( ! in_array( $input, $valid, true ) ) {
				return new WP_Error(
					'gv_rest_invalid_search_input',
					sprintf(
						/* translators: 1: input slug, 2: field id, 3: allowed slugs */
						__( 'Search input "%1$s" is not allowed for field "%2$s". Allowed: %3$s.', 'gk-gravityview' ),
						$input,
						$canonical_id,
						implode( ', ', $valid )
					),
					[ 'status' => 400 ]
				);
			}
		}

		$settings = SearchFieldResolver::build_settings( $raw, $input );

		if ( $is_update ) {
			// Merge settings into the existing slot, preserving its identity.
			list( $epos, $eslot )       = $matches[0];
			$merged                     = array_merge( (array) $section[ $epos ][ $eslot ], $settings );
			$section[ $epos ][ $eslot ] = $resolver->normalize( $merged );
			return [ 'field_id' => $canonical_id, 'position' => $epos, 'search_slot' => $eslot, 'created' => false ];
		}

		// Create: identity from the requested id. normalize_search_field()
		// canonicalizes a qualified id and stamps form_id itself, so no pre-stamp.
		$create = array_merge( [ 'id' => $field_id ], $settings );
		if ( ! isset( $section[ $position ] ) || ! is_array( $section[ $position ] ) ) {
			$section[ $position ] = [];
		}
		$search_slot                         = Grid::uid();
		$section[ $position ][ $search_slot ] = $resolver->normalize( $create );
		return [ 'field_id' => $field_id, 'position' => $position, 'search_slot' => $search_slot, 'created' => true ];
	}

	/**
	 * Identity/bookkeeping keys that must never persist as stray slot settings in
	 * apply_collection: `slot` (loop bookkeeping) and the identity aliases already
	 * resolved into the slot id. For widgets, `widget_id` is always an alias and
	 * `type` is one only when it named the widget (rather than being a real setting
	 * like export_link's csv/tsv).
	 *
	 * @since 3.0.0
	 *
	 * @param string $collection        'fields' or 'widgets'.
	 * @param bool   $widget_type_is_id Whether a widget's `type` key supplied its id.
	 *
	 * @return string[]
	 */
	private static function identity_alias_keys( string $collection, bool $widget_type_is_id ): array {
		$keys = [ 'slot', 'field_id', 'id' ];
		if ( 'widgets' === $collection ) {
			$keys[] = 'widget_id';
			if ( $widget_type_is_id ) {
				$keys[] = 'type';
			}
		}
		return $keys;
	}


	/**
	 * Validate that a widget area is a placeable widget meta-zone (optionally
	 * grid-suffixed `::cols::row`). Mirrors the apply_collection zone gate.
	 *
	 * @since 3.0.0
	 *
	 * @param string $area Widget area key.
	 *
	 * @return true|\WP_Error
	 */
	private function assert_placeable_widget_area( string $area ) {
		// Exact match against the canonical meta-zones — a NEW search bar is
		// created in a clean meta-zone, never a grid-suffixed area (e.g.
		// header_top::100::row) which could be a phantom row that no template
		// renders. To add a field to a bar already placed in a grid row, target
		// it with area + widget_slot instead.
		$valid = $this->placeable_widget_areas();
		if ( ! in_array( $area, $valid, true ) ) {
			return new WP_Error(
				'gv_rest_invalid_area_key',
				sprintf(
					/* translators: 1: area key supplied by the caller, 2: comma-separated valid meta-zones */
					__( 'area "%1$s" must be a widget meta-zone (%2$s) when creating a search bar.', 'gk-gravityview' ),
					$area,
					implode( ', ', $valid )
				),
				[ 'status' => 400 ]
			);
		}
		return true;
	}

	/**
	 * Canonical grid area ids for a fresh widget, sourced from GravityView's
	 * default widget grid (Widget::get_default_widget_areas) so this stays in
	 * lockstep with the View editor instead of duplicating the list. Falls back to
	 * the stable grid defaults when the widget class isn't loaded.
	 *
	 * @since 3.0.0
	 *
	 * @return string[] e.g. ['top','left','right'].
	 */
	private function default_widget_area_ids(): array {
		$ids = [];
		foreach ( (array) Widget::get_default_widget_areas() as $row ) {
			foreach ( (array) $row as $columns ) {
				foreach ( (array) $columns as $area ) {
					if ( ! is_array( $area ) || ! isset( $area['areaid'] ) ) {
						continue;
					}
					// The grid stamps each areaid with its row type + a fresh
					// per-call random UID (e.g. "top::100::a1b2c3"); keep only the
					// stable bare position so the clean meta-zone vocabulary is
					// deterministic. A bar placed into a specific grid row (added
					// via gv_grid_row_add) is targeted by area + widget_slot, not
					// auto-created here.
					$bare = strstr( (string) $area['areaid'], '::', true );
					$bare = false !== $bare ? $bare : (string) $area['areaid'];
					if ( '' !== $bare ) {
						$ids[ $bare ] = true;
					}
				}
			}
		}
		// A filter could in principle empty the area set; fall back to the stable
		// grid defaults so a search bar can always be placed.
		return ! empty( $ids ) ? array_keys( $ids ) : [ 'top', 'left', 'right' ];
	}

	/**
	 * Placeable widget meta-zones a fresh search bar may be created in: the
	 * {header, footer} families crossed with the canonical grid area ids.
	 *
	 * @since 3.0.0
	 *
	 * @return string[] e.g. ['header_top','header_left','header_right','footer_top', …].
	 */
	private function placeable_widget_areas(): array {
		$areas = [];
		foreach ( [ 'header', 'footer' ] as $family ) {
			foreach ( $this->default_widget_area_ids() as $area_id ) {
				$areas[] = $family . '_' . $area_id;
			}
		}
		return $areas;
	}

	/**
	 * The canonical identity of a stored search-field slot, so an update keyed by
	 * a bare id validates against the stored field's real form.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $section search_fields_section tree.
	 * @param string $pos     Position key.
	 * @param string $slot    Slot uid.
	 *
	 * @return string
	 */
	private function stored_search_field_identity( array $section, string $pos, string $slot ): string {
		$field = $section[ $pos ][ $slot ] ?? null;
		return is_array( $field ) ? SearchFieldResolver::effective_id( $field ) : '';
	}

	/**
	 * Normalise a search position bucket: map a bare zone (as search-zones-list
	 * returns) to its default `_top` bucket, and reject anything that isn't a
	 * real, renderable search-bar row.
	 *
	 * @since 3.0.0
	 *
	 * @param string $position Caller-supplied position.
	 *
	 * @return string Normalised position, or '' when invalid.
	 */
	private function normalize_search_position( string $position ): string {
		$aliases = [
			'search-general'  => 'search-general_top',
			'search-advanced' => 'search-advanced_top',
		];
		if ( isset( $aliases[ $position ] ) ) {
			return $aliases[ $position ];
		}
		// Accept any real bucket of a known search zone — top/bottom/left/right,
		// optionally grid-suffixed (`::cols::row_uid`) for multi-column bars —
		// while still rejecting typos like "search-general_bogus".
		$base = strstr( $position, '::', true );
		if ( false === $base ) {
			$base = $position;
		}
		if ( preg_match( '/^(search-general|search-advanced)_(top|bottom|left|right)$/', $base ) ) {
			return $position;
		}
		return '';
	}

	/**
	 * POST /views/{id}/search-fields/_slots
	 *
	 * Add a Search Field to a search_bar widget's modern
	 * `search_fields_section` tree. The widget area + slot live in
	 * the body (not the URL) because compound widget area keys
	 * (`header_top::100::ROW_UID`) are URL-unfriendly.
	 *
	 * Body:
	 *   {
	 *     widget_area: "header_top::100::ROW_UID",
	 *     widget_slot: "<search_bar slot uid>",
	 *     position:    "search-general_top::100::SEARCH_ROW_UID",
	 *     field:       { id, type, input, label?, ...settings },
	 *     slot?:       "<custom search slot uid>"
	 *   }
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_search_field_slot( WP_REST_Request $request ) {
		$resolved = $this->resolve_search_widget( $request );
		if ( is_wp_error( $resolved ) ) {
			return $resolved;
		}

		$payload  = $request->get_json_params() ?: [];
		$position = isset( $payload['position'] ) ? (string) $payload['position'] : '';
		$field    = isset( $payload['field'] ) && is_array( $payload['field'] ) ? $payload['field'] : [];

		if ( '' === $position ) {
			return new WP_Error( 'gv_rest_missing_position', __( 'position is required (e.g. "search-general_top::100::ROW_UID").', 'gk-gravityview' ), [ 'status' => 400 ] );
		}
		if ( empty( $field ) || empty( $field['id'] ) ) {
			return new WP_Error( 'gv_rest_invalid_field', __( 'field must be an object with at least an `id` (e.g. "search_all", "submit", or a GF field id).', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$view_id_for_checks = (int) ( $resolved['view_id'] ?? 0 );
		$resolver           = new SearchFieldResolver( $view_id_for_checks );

		// Resolve + form-scope the field BEFORE any write and capture its
		// effective (qualified) identity for input narrowing — the shared gate
		// every search-field write path runs, so this endpoint can't bind a
		// field the bulk orchestrator or batch repository would reject.
		$effective_id = $resolver->resolve_searchable_id( $field );
		if ( is_wp_error( $effective_id ) ) {
			return $effective_id;
		}

		// Reject unknown / field-invalid input slugs BEFORE write so a
		// typo can't silently produce a Search Bar field with no
		// input control, and so combinations the renderer would
		// silently coerce (e.g. `date_range` on `search_mode`) get a
		// clear 400 instead of nonsense output.
		// `input` is the caller-facing slug; `input_type` is the
		// persisted name. Both go through the per-field allow-list.
		$caller_input = '';
		if ( isset( $field['input'] ) && '' !== (string) $field['input'] ) {
			$caller_input = (string) $field['input'];
		} elseif ( isset( $field['input_type'] ) && '' !== (string) $field['input_type'] ) {
			$caller_input = (string) $field['input_type'];
		}
		if ( '' !== $caller_input ) {
			$valid = $resolver->valid_input_types( $effective_id );
			if ( ! in_array( $caller_input, $valid, true ) ) {
				return new WP_Error(
					'gv_rest_invalid_search_input',
					sprintf(
						/* translators: 1: rejected input slug, 2: field id, 3: comma-separated list of valid slugs */
						__( 'Search input "%1$s" is not allowed for field "%2$s". Allowed for this field: %3$s.', 'gk-gravityview' ),
						$caller_input,
						$effective_id,
						implode( ', ', $valid )
					),
					[ 'status' => 400 ]
				);
			}
		}

		$search_slot = isset( $payload['slot'] ) ? (string) $payload['slot'] : '';
		if ( '' === $search_slot ) {
			$search_slot = Grid::uid();
		}

		$widget_settings = $resolved['widget_settings'];
		$section         = isset( $widget_settings['search_fields_section'] ) && is_array( $widget_settings['search_fields_section'] )
			? $widget_settings['search_fields_section']
			: [];
		if ( ! isset( $section[ $position ] ) || ! is_array( $section[ $position ] ) ) {
			$section[ $position ] = [];
		}
		// Preserve caller-supplied add-on per-field settings (normalize
		// canonicalizes the core keys but keeps unknown add-on keys), so a
		// search field created with add-on settings round-trips intact.
		$section[ $position ][ $search_slot ] = $resolver->normalize( $field );

		$resolved['widget_settings']['search_fields_section'] = $section;
		// Drop legacy key so the widget is fully on the modern shape.
		unset( $resolved['widget_settings']['search_fields'] );
		$this->persist_search_widget( $resolved );

		$response = new WP_REST_Response(
			[
				'view_id'     => $resolved['view_id'],
				'widget_area' => $resolved['widget_area'],
				'widget_slot' => $resolved['widget_slot'],
				'position'    => $position,
				'search_slot' => $search_slot,
				'field'       => $field,
				'version'     => $this->compute_version( $resolved['view_id'] ),
			],
			201
		);
		$response->header( 'ETag', '"' . $this->compute_version( $resolved['view_id'] ) . '"' );
		return $response;
	}

	/**
	 * PATCH /views/{id}/search-fields/{search_slot}
	 *
	 * Patch settings on an existing search field slot. Only keys
	 * present in `settings` are modified; rest is preserved.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function patch_search_field_slot( WP_REST_Request $request ) {
		$resolved = $this->resolve_search_widget( $request );
		if ( is_wp_error( $resolved ) ) {
			return $resolved;
		}

		$payload     = $request->get_json_params() ?: [];
		$position    = isset( $payload['position'] ) ? (string) $payload['position'] : '';
		$search_slot = (string) $request['search_slot'];
		$settings    = isset( $payload['settings'] ) && is_array( $payload['settings'] ) ? $payload['settings'] : [];

		if ( '' === $position ) {
			return new WP_Error( 'gv_rest_missing_position', __( 'position is required.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}
		if ( empty( $settings ) ) {
			return new WP_Error( 'gv_rest_empty_settings', __( 'settings must be a non-empty object.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$widget_settings = $resolved['widget_settings'];
		$section         = $widget_settings['search_fields_section'] ?? [];
		if ( ! isset( $section[ $position ][ $search_slot ] ) ) {
			return new WP_Error( 'gv_rest_search_slot_not_found', __( 'Search slot not found at the given position.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		$existing = (array) $section[ $position ][ $search_slot ];

		// Same input-type allow-list as create_search_field_slot —
		// PATCH can ALSO change the input control, and a typoed value
		// would silently break the rendered search bar. Narrow by the
		// SLOT'S CURRENT field id (the slot's identity doesn't change
		// on a settings patch) so combinations the renderer would
		// coerce — e.g. `date_range` on a `search_mode` slot — get a
		// clear 400 instead of nonsense output.
		$incoming_input = '';
		if ( isset( $settings['input'] ) && '' !== (string) $settings['input'] ) {
			$incoming_input = (string) $settings['input'];
		} elseif ( isset( $settings['input_type'] ) && '' !== (string) $settings['input_type'] ) {
			$incoming_input = (string) $settings['input_type'];
		}
		if ( '' !== $incoming_input ) {
			// Narrow against the slot's CANONICAL stored identity (qualified
			// {form_id}::id for GF / joined fields) so a joined-form slot is
			// validated against its real form, not the View's primary form.
			$slot_field_id = $this->stored_search_field_identity( $section, $position, $search_slot );
			if ( '' === $slot_field_id ) {
				$slot_field_id = isset( $existing['id'] ) ? (string) $existing['id'] : '';
			}
			$valid = ( new SearchFieldResolver( (int) ( $resolved['view_id'] ?? 0 ) ) )->valid_input_types( $slot_field_id );
			if ( ! in_array( $incoming_input, $valid, true ) ) {
				return new WP_Error(
					'gv_rest_invalid_search_input',
					sprintf(
						/* translators: 1: rejected input slug, 2: field id, 3: allowed list */
						__( 'Search input "%1$s" is not allowed for field "%2$s". Allowed for this field: %3$s.', 'gk-gravityview' ),
						$incoming_input,
						$slot_field_id,
						implode( ', ', $valid )
					),
					[ 'status' => 400 ]
				);
			}
		}
		foreach ( $settings as $k => $v ) {
			$k = sanitize_key( $k );
			if ( '' === $k ) {
				continue;
            }
			if ( null === $v ) {
				unset( $existing[ $k ] );
			} else {
				$existing[ $k ] = $this->sanitize_setting_value( $v );
			}
		}
		// Preserve unknown add-on settings already on the stored slot: a
		// label/input patch must not strip an add-on's per-field settings.
		$section[ $position ][ $search_slot ] = ( new SearchFieldResolver( (int) ( $resolved['view_id'] ?? 0 ) ) )->normalize( $existing );

		$resolved['widget_settings']['search_fields_section'] = $section;
		unset( $resolved['widget_settings']['search_fields'] );
		$this->persist_search_widget( $resolved );

		$response = new WP_REST_Response(
			[
				'view_id'     => $resolved['view_id'],
				'widget_area' => $resolved['widget_area'],
				'widget_slot' => $resolved['widget_slot'],
				'position'    => $position,
				'search_slot' => $search_slot,
				'values'      => $existing,
				'version'     => $this->compute_version( $resolved['view_id'] ),
			],
			200
		);
		$response->header( 'ETag', '"' . $this->compute_version( $resolved['view_id'] ) . '"' );
		return $response;
	}

	/**
	 * DELETE /views/{id}/search-fields/{search_slot}
	 *
	 * Remove a search field slot from the search_bar widget.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_search_field_slot( WP_REST_Request $request ) {
		$resolved = $this->resolve_search_widget( $request );
		if ( is_wp_error( $resolved ) ) {
			return $resolved;
		}

		$payload     = $request->get_json_params() ?: [];
		$position    = isset( $payload['position'] )
			? (string) $payload['position']
			: (string) ( $request->get_param( 'position' ) ?: '' );
		$search_slot = (string) $request['search_slot'];

		if ( '' === $position ) {
			return new WP_Error( 'gv_rest_missing_position', __( 'position is required (in body or query).', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		$widget_settings = $resolved['widget_settings'];
		$section         = $widget_settings['search_fields_section'] ?? [];
		if ( ! isset( $section[ $position ][ $search_slot ] ) ) {
			return new WP_Error( 'gv_rest_search_slot_not_found', __( 'Search slot not found at the given position.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		unset( $section[ $position ][ $search_slot ] );
		// Clean up the position if it's now empty (excluding area_settings).
		$remaining = array_diff_key( (array) $section[ $position ], [ 'area_settings' => true ] );
		if ( empty( $remaining ) ) {
			unset( $section[ $position ] );
		}

		$resolved['widget_settings']['search_fields_section'] = $section;
		unset( $resolved['widget_settings']['search_fields'] );
		$this->persist_search_widget( $resolved );

		return new WP_REST_Response(
			[
				'view_id'     => $resolved['view_id'],
				'widget_area' => $resolved['widget_area'],
				'widget_slot' => $resolved['widget_slot'],
				'position'    => $position,
				'search_slot' => $search_slot,
				'deleted'     => true,
				'version'     => $this->compute_version( $resolved['view_id'] ),
			],
			200
		);
	}

	/**
	 * Resolve the targeted search_bar widget from the request body
	 * for the search-fields slot CRUD endpoints. Returns either the
	 * resolved spec (view_id, widget_area, widget_slot, widget_settings,
	 * widgets_tree) or a WP_Error suitable for direct response.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array|\WP_Error
	 */
	private function resolve_search_widget( WP_REST_Request $request ) {
		$view_id = (int) $request['id'];

		$precondition = $this->check_precondition( $request, $view_id );
		if ( is_wp_error( $precondition ) ) {
			return $precondition;
		}

		$payload     = $request->get_json_params() ?: [];
		$widget_area = isset( $payload['widget_area'] ) ? (string) $payload['widget_area'] : '';
		$widget_slot = isset( $payload['widget_slot'] ) ? (string) $payload['widget_slot'] : '';

		if ( '' === $widget_area || '' === $widget_slot ) {
			return new WP_Error(
				'gv_rest_missing_widget_target',
				__( 'widget_area and widget_slot are required (the search_bar widget the search field belongs to).', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		$widgets_tree = $this->read_widgets( $view_id );
		if ( ! isset( $widgets_tree[ $widget_area ][ $widget_slot ] ) ) {
			return new WP_Error( 'gv_rest_widget_not_found', __( 'Widget slot not found at the given area.', 'gk-gravityview' ), [ 'status' => 404 ] );
		}

		$widget_settings = (array) $widgets_tree[ $widget_area ][ $widget_slot ];
		if ( 'search_bar' !== ( $widget_settings['id'] ?? '' ) ) {
			return new WP_Error( 'gv_rest_not_search_bar', __( 'Targeted widget is not a search_bar.', 'gk-gravityview' ), [ 'status' => 400 ] );
		}

		// One-shot legacy → modern migration so subsequent slot CRUD
		// always operates on the canonical shape.
		$widget_settings = $this->migrate_search_bar_to_modern( $widget_settings, $view_id );

		return [
			'view_id'         => $view_id,
			'widget_area'     => $widget_area,
			'widget_slot'     => $widget_slot,
			'widget_settings' => $widget_settings,
			'widgets_tree'    => $widgets_tree,
		];
	}

	/**
	 * GET /search-fields/input-types — discover the valid set of
	 * search-field input type slugs. Returns the canonical core list
	 * plus anything add-ons have registered via
	 * `gravityview/search/input_labels`. Used by the MCP client to
	 * validate user-supplied `field.input` values BEFORE issuing the
	 * write — a server-side reject is the safety net.
	 *
	 * Response: `{ input_types: ["input_text", "select", …] }`.
	 *
	 * @since 3.0.0
	 *
	 * @return WP_REST_Response
	 */
	public function get_search_field_input_types(): WP_REST_Response {
		return new WP_REST_Response( [ 'input_types' => SearchFieldResolver::default_input_types() ], 200 );
	}

	/**
	 * Vet every entry inside a `search_fields_section` payload that
	 * arrived attached to a widget create/update. Without this gate,
	 * a single `create_widget_slot` body could persist a search_bar
	 * with `search_fields_section[…].input_type = "datepiker"` and
	 * bypass the dedicated /search-fields endpoint's allow-list
	 * entirely.
	 *
	 * Returns `null` when the payload is clean, or a `WP_Error`
	 * (status 400) carrying the first invalid combination so the
	 * caller knows exactly which nested entry was rejected.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View id (drives per-field narrowing).
	 * @param array $payload Inbound widget payload — checked for nested search-fields.
	 *
	 * @return null|WP_Error
	 */
	public function validate_search_fields_section_payload( int $view_id, array $payload ) {
		$section = $payload['search_fields_section'] ?? null;
		if ( ! is_array( $section ) || empty( $section ) ) {
			return null;
		}

		$resolver = new SearchFieldResolver( $view_id );

		foreach ( $section as $position => $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			foreach ( $row as $search_slot => $field ) {
				if ( 'area_settings' === $search_slot || ! is_array( $field ) ) {
					continue;
				}
				// Resolve + form-scope the entry's effective identity (the same gate
				// add_search_bar applies). A real entry with no derivable id is
				// rejected, not skipped, so nothing reaches storage unvalidated.
				$field_id = $resolver->resolve_searchable_id( $field );
				if ( is_wp_error( $field_id ) ) {
					return $field_id;
				}
				$input    = isset( $field['input'] ) && '' !== (string) $field['input']
					? (string) $field['input']
					: ( isset( $field['input_type'] ) ? (string) $field['input_type'] : '' );
				if ( '' === $input ) {
					continue;
				}
				$allowed = $resolver->valid_input_types( $field_id );
				if ( ! in_array( $input, $allowed, true ) ) {
					return new WP_Error(
						'gv_rest_invalid_search_input',
						sprintf(
							/* translators: 1: nested search slot, 2: rejected input, 3: field id, 4: allowed list */
							__( 'Nested search field "%1$s" has invalid input "%2$s" for field id "%3$s". Allowed: %4$s.', 'gk-gravityview' ),
							(string) $search_slot,
							$input,
							$field_id,
							implode( ', ', $allowed )
						),
						[ 'status' => 400 ]
					);
				}
			}
		}

		return null;
	}

	/**
	 * Persist the mutated widget settings back to post-meta and bump
	 * the View's version counter. Companion to resolve_search_widget().
	 *
	 * @since 3.0.0
	 *
	 * @param array $resolved Output of resolve_search_widget.
	 */
	private function persist_search_widget( array $resolved ): void {
		$tree = $resolved['widgets_tree'];
		$tree[ $resolved['widget_area'] ][ $resolved['widget_slot'] ] = $resolved['widget_settings'];
		$this->write_widgets( $resolved['view_id'], $tree );
		$this->bump_version( $resolved['view_id'] );
	}

	// -------------------------------------------------------------------
	// Data layer helpers
	// -------------------------------------------------------------------

	/**
	 * Read every configured field, keyed by area then slot UID.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View id.
	 *
	 * @return array
	 */
	private function read_fields( int $view_id ): array {
		$raw = get_post_meta( $view_id, self::META_FIELDS, true );
		return is_array( $raw ) ? $raw : [];
	}

	/**
	 * Persist the field tree.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View id.
	 * @param array $fields  Field tree.
	 *
	 * @return void
	 */
	private function write_fields( int $view_id, array $fields ): void {
		update_post_meta( $view_id, self::META_FIELDS, $fields );
	}

	/**
	 * Read configured widgets.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View id.
	 *
	 * @return array
	 */
	private function read_widgets( int $view_id ): array {
		$raw = get_post_meta( $view_id, self::META_WIDGETS, true );
		return is_array( $raw ) ? $raw : [];
	}

	/**
	 * Persist the widget tree.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View id.
	 * @param array $widgets Widget tree.
	 *
	 * @return void
	 */
	private function write_widgets( int $view_id, array $widgets ): void {
		update_post_meta( $view_id, self::META_WIDGETS, $widgets );
	}

	/**
	 * Read the flat template-settings array (page_size, sort_field, etc.).
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View id.
	 *
	 * @return array
	 */
	private function read_template_settings( int $view_id ): array {
		$merged = [];
		foreach ( $this->template_settings_sources() as $src ) {
			$raw = get_post_meta( $view_id, $src['meta_key'], true );
			if ( ! is_array( $raw ) ) {
				continue;
			}
			if ( '' === $src['prefix'] ) {
				// Default source — top-level keys (page_size, sort_field,
				// map_zoom registered via gravityview_default_args, etc).
				$merged = array_merge( $merged, $raw );
			} else {
				// Namespaced source (e.g. `datatables.*`). Nest the
				// entire silo array under the prefix so callers can
				// reach it via `template_settings.datatables.responsive`.
				$merged[ $src['prefix'] ] = $raw;
			}
		}
		// The legacy `template` key inside template_settings is a stale
		// duplicate of `template_ids.directory` (the canonical store is
		// `_gravityview_directory_template`). Strip it from inspector
		// responses so callers have one source of truth per zone.
		unset( $merged['template'] );
		return $merged;
	}

	/**
	 * Filter-discoverable list of post-meta sources that contribute
	 * to a View's template_settings surface. Lets template plugins
	 * with their own silo meta keys (DataTables stores under
	 * `_gravityview_datatables_settings`, for example) bridge into
	 * the inspector's read / write / schema-discovery surface
	 * WITHOUT core having to name them.
	 *
	 * Each registered source is a map:
	 *
	 *     [
	 *         'meta_key'        => '_gravityview_template_settings',
	 *         'prefix'          => '',                                                                 // '' → top-level keys
	 *         'schema_callable' => static fn() => (array) ViewSettings::defaults( true ),
	 *         'template_ids'    => [],                                                                 // [] → applies to every template
	 *         'groups'          => [],                                                                 // setting `group` slugs this source owns (used for dedupe)
	 *     ]
	 *
	 * Sanitisation runs through the standard `sanitize_setting_value`
	 * pipeline regardless of source — the silo meta keys aren't
	 * special-cased on write.
	 *
	 * GravityView core auto-registers the primary source (the
	 * `_gravityview_template_settings` meta + `gravityview_default_args`
	 * filter Maps and the core templates already hook into). Add-ons
	 * with their own silo meta keys register an additional entry.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template_id Optional template id; when set, only
	 *                            sources whose `template_ids` is empty
	 *                            (all-templates) or contains this id
	 *                            are returned. Used by the schema
	 *                            endpoint. Read/write paths pass `''`
	 *                            so historical data on the wrong
	 *                            template still round-trips.
	 *
	 * @return array<int, array{meta_key:string, prefix:string, schema_callable:callable|null, template_ids:array<int,string>, groups:array<int,string>}>
	 */
	private function template_settings_sources( string $template_id = '' ): array {
		/**
		 * Filters the set of post-meta sources that contribute to the
		 * inspector's `template_settings` read / write / schema surface.
		 *
		 * @since 3.0.0
		 *
		 * @param array<int, array{ meta_key: string, prefix: string, schema_callable: callable, template_ids: array<int, string>, groups: array<int, string> }> $sources Registered sources.
		 */
		$sources  = (array) apply_filters( 'gk/gravityview/rest/template-settings/list/sources', [] );
		$resolved = [];
		foreach ( $sources as $src ) {
			if ( ! is_array( $src ) || empty( $src['meta_key'] ) ) {
				continue;
			}
			$template_ids = isset( $src['template_ids'] ) && is_array( $src['template_ids'] )
				? array_values( array_filter( array_map( 'strval', $src['template_ids'] ) ) )
				: [];
			if ( '' !== $template_id && ! empty( $template_ids ) && ! in_array( $template_id, $template_ids, true ) ) {
				continue;
			}
			$resolved[] = [
				'meta_key'        => (string) $src['meta_key'],
				'prefix'          => (string) ( $src['prefix'] ?? '' ),
				'schema_callable' => $src['schema_callable'] ?? null,
				'template_ids'    => $template_ids,
				// Setting `group` slugs this source claims ownership
				// of. The schema endpoint uses these to suppress
				// duplicate emission from the core source — without
				// this, e.g. Maps' `map_zoom` would surface BOTH at
				// top-level (via `gravityview_default_args`) and
				// under `maps.map_zoom`, with the top-level slug
				// silently writing to the wrong meta key.
				'groups'          => isset( $src['groups'] ) && is_array( $src['groups'] )
					? array_values( array_filter( array_map( 'strval', $src['groups'] ) ) )
					: [],
			];
		}
		return $resolved;
	}

	/**
	 * Register the core post-meta source the inspector reads / writes
	 * / discovers through. Other plugins (DataTables, Maps, future
	 * add-ons) self-register their silo sources via the
	 * `gk/gravityview/rest/template-settings/sources` filter — core
	 * carries no per-plugin knowledge.
	 *
	 * @since 3.0.0
	 */
	public static function register_default_sources(): void {
		add_filter(
			'gk/gravityview/rest/template-settings/list/sources',
			static function ( $sources ) {
				if ( ! is_array( $sources ) ) {
					$sources = [];
				}

				// `View_Settings::defaults( true )` is the canonical
				// detailed-catalog accessor; calling
				// `apply_filters( 'gravityview_default_args', [] )`
				// directly bypasses the bootstrap and returns an
				// empty array. The callable lets every add-on hook
				// into `gravityview_default_args` surface here.
				$sources[] = [
					'meta_key'        => self::META_TEMPLATE_SETTINGS,
					'prefix'          => '',
					'schema_callable' => static fn() => class_exists( ViewSettings::class )
						? (array) ViewSettings::defaults( true )
						: [],
					'template_ids'    => [],
				];

				return $sources;
			}
		);
	}

	/**
	 * Build a `prefix => source` lookup from a list of resolved
	 * sources. Replaces the same three-line loop that previously
	 * lived inside every read/write/schema path.
	 *
	 * @since 3.0.0
	 *
	 * @param array $sources Output of `template_settings_sources()`.
	 *
	 * @return array<string, array> Source map keyed by prefix (default source has key `''`).
	 */
	private function prefix_map( array $sources ): array {
		$map = [];
		foreach ( $sources as $src ) {
			$map[ $src['prefix'] ] = $src;
		}
		return $map;
	}

	/**
	 * Persist a fully-resolved template_settings tree, splitting
	 * each namespaced prefix back to its silo meta key. The default
	 * (empty-prefix) source receives every top-level key; sub-arrays
	 * keyed by a registered prefix go to that source's meta.
	 *
	 * Wire shape examples (both legal in a single payload):
	 *
	 *   { "page_size": 50, "map_zoom": "12" }
	 *       → _gravityview_template_settings
	 *
	 *   { "datatables": { "responsive": "1", "rowgroup_field": "1.3" } }
	 *       → _gravityview_datatables_settings
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id  View id.
	 * @param array $tree     Resolved template_settings tree (already sanitised).
	 *
	 * @return void
	 */
	private function write_template_settings_tree( int $view_id, array $tree ): void {
		$by_prefix = $this->prefix_map( $this->template_settings_sources() );
		$default   = $by_prefix[''] ?? null;
		if ( ! $default ) {
			// Core ALWAYS registers the empty-prefix source at init.
			// Defensive — if the filter was somehow cleared, fall
			// through to writing the standard meta key so the call
			// still succeeds.
			update_post_meta( $view_id, self::META_TEMPLATE_SETTINGS, $tree );
			return;
		}

		$writes                         = [];
		$writes[ $default['meta_key'] ] = [];
		foreach ( $tree as $key => $value ) {
			if ( is_string( $key ) && isset( $by_prefix[ $key ] ) && '' !== $key && is_array( $value ) ) {
				$writes[ $by_prefix[ $key ]['meta_key'] ] = $value;
				continue;
			}
			$writes[ $default['meta_key'] ][ $key ] = $value;
		}

		foreach ( $writes as $meta_key => $payload ) {
			update_post_meta( $view_id, $meta_key, $payload );
		}
	}

	/**
	 * Read the search-criteria slice from template-settings.
	 *
	 * IMPORTANT: these values live INSIDE `template_settings`. The
	 * `/config` response exposes them under `search_criteria` for
	 * client convenience (page-size pickers, sort controls), but
	 * writes MUST go through `patch_template_settings` /
	 * `patch_search_criteria` to keep the canonical store in sync.
	 * Earlier versions of this controller surfaced both keys as
	 * writable surfaces; that drifted out of sync. The shape today:
	 *
	 *   - GET /config → `search_criteria` slice mirrored at top level
	 *                    AND remains visible inside `template_settings`
	 *                    so legacy consumers keep working.
	 *   - PATCH writes → both `patch_template_settings` and
	 *                    `patch_search_criteria` merge into the same
	 *                    `_gravityview_template_settings` post meta.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View id.
	 *
	 * @return array
	 */
	private function read_search_criteria( int $view_id ): array {
		$ts = $this->read_template_settings( $view_id );

		return [
			'page_size'      => $ts['page_size'] ?? 25,
			'sort_field'     => $ts['sort_field'] ?? '',
			'sort_direction' => $ts['sort_direction'] ?? 'ASC',
			'offset'         => $ts['offset'] ?? 0,
		];
	}

	/**
	 * Read a single slot.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id  View id.
	 * @param string $area     Area key.
	 * @param string $slot_uid Slot UID.
	 *
	 * @return array|null
	 */
	private function read_slot( int $view_id, string $area, string $slot_uid ): ?array {
		// Reject caller-supplied UIDs with unsafe characters before
		// using them as a lookup key. Returning null funnels into the
		// existing not-found / 404 path; valid UIDs are unaffected.
		$slot_uid_is_safe = $this->is_valid_slot_uid( $slot_uid );
		if ( ! $slot_uid_is_safe ) {
			return null;
		}
		$fields = $this->read_fields( $view_id );
		return $fields[ $area ][ $slot_uid ] ?? null;
	}

	/**
	 * Merge a partial template-settings payload into the saved meta.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View id.
	 * @param array $partial Partial settings map.
	 *
	 * @return void
	 */
	private function merge_template_settings( int $view_id, array $partial ) {
		$sources   = $this->template_settings_sources();
		$by_prefix = $this->prefix_map( $sources );
		$default   = $by_prefix[''] ?? null;
		if ( ! $default ) {
			return null;
		}

		// Seed each source's bucket with its existing post meta so
		// keys not in the partial payload survive the write. Single
		// pass — no intermediate tree, no second-pass split.
		$writes = [];
		foreach ( $sources as $src ) {
			$existing                   = get_post_meta( $view_id, $src['meta_key'], true );
			$writes[ $src['meta_key'] ] = is_array( $existing ) ? $existing : [];
		}

		foreach ( $partial as $key => $value ) {
			$key = sanitize_key( $key );
			if ( '' === $key ) {
				continue;
			}
			// Namespaced write: `{ datatables: { responsive: 1 } }`
			// → routes the inner map to `_gravityview_datatables_settings`
			// without touching the primary template_settings meta.
			if ( '' !== $key && isset( $by_prefix[ $key ] ) && is_array( $value ) ) {
				$silo_meta = $by_prefix[ $key ]['meta_key'];
				foreach ( $value as $sub_key => $sub_value ) {
					$sub_key = sanitize_key( $sub_key );
					if ( '' === $sub_key ) {
						continue;
					}
					// Pass the prefix so the validator finds the silo's
					// schema (DT/MFV) instead of silently skipping it
					// like the legacy code did.
					$validation_error = $this->validate_template_setting_value( $sub_key, $sub_value, $sources, $key );
					if ( is_wp_error( $validation_error ) ) {
						return $validation_error;
					}
					$writes[ $silo_meta ][ $sub_key ] = $this->sanitize_setting_value( $sub_value );
				}
				continue;
			}
			$validation_error = $this->validate_template_setting_value( $key, $value, $sources );
			if ( is_wp_error( $validation_error ) ) {
				return $validation_error;
			}
			$writes[ $default['meta_key'] ][ $key ] = $this->sanitize_setting_value( $value );
		}

		foreach ( $writes as $meta_key => $payload ) {
			update_post_meta( $view_id, $meta_key, $payload );
		}
		return null;
	}

	/**
	 * Schema-aware type check for a single template_setting value.
	 *
	 * Looks up the expected type from the registered schema sources
	 * (gravityview_default_args, gravityview_dt_default_settings,
	 * etc. via template_settings_sources()) and rejects values that
	 * can't satisfy that type — e.g. `page_size = "abc"` returns
	 * 400 instead of silently persisting the literal `"abc"` only
	 * for the renderer to coerce it to 0 entries later.
	 *
	 * Numeric / integer types: must be is_numeric(). Strings, booleans,
	 * and other types pass through (the caller's sanitize_setting_value
	 * handles their per-mode cleanup).
	 *
	 * @since 3.0.0
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $value   Incoming value.
	 * @param array  $sources Output of template_settings_sources().
	 *
	 * @return null|\WP_Error null on success.
	 */
	private function validate_template_setting_value( string $key, $value, array $sources, string $prefix = '' ) {
		$schema = $this->resolve_template_setting_schema( $key, $sources, $prefix );
		if ( null === $schema ) {
			return null;
		}
		$expected    = isset( $schema['type'] ) ? (string) $schema['type'] : '';
		$display_key = '' === $prefix ? $key : ( $prefix . '.' . $key );

		// A scalar setting stored as an array reaches consumers expecting
		// a string, rendering the literal `Array` and fatalling on typed
		// reads. `ViewStyles::sanitize_payload()` enforces the same rule
		// on the classic save, silently, since a form post has no channel
		// to report a rejected field. Scoped to the default source; silo
		// sources own their own shapes.
		if ( '' === $prefix && is_array( $value ) && ! wp_is_numeric_array( $value ) && ! ViewSettings::declares_array_value( $schema ) ) {
			return new \WP_Error(
				'gv_rest_invalid_template_setting_type',
				sprintf(
					/* translators: 1: setting key, 2: literal incoming value */
					__( 'template_settings.%1$s does not accept an array (its registered schema declares a single value). Received: %2$s', 'gk-gravityview' ),
					$display_key,
					wp_json_encode( $value )
				),
				[
					'status'         => 400,
					'setting'        => $display_key,
					'expected_type'  => '' !== $expected ? $expected : 'scalar',
					'received_value' => $value,
				]
			);
		}

		if ( in_array( $expected, [ 'number', 'integer', 'numeric' ], true ) ) {
			if ( null === $value || '' === $value ) {
				return null;
			}
			if ( ! is_numeric( $value ) && ! is_bool( $value ) ) {
				return new \WP_Error(
					'gv_rest_invalid_template_setting_type',
					sprintf(
						/* translators: 1: setting key, 2: expected type, 3: literal incoming value */
						__( 'template_settings.%1$s expects a numeric value (per its registered schema, type "%2$s"). Received: %3$s', 'gk-gravityview' ),
						$display_key,
						$expected,
						wp_json_encode( $value )
					),
					[
						'status'         => 400,
						'setting'        => $display_key,
						'expected_type'  => $expected,
						'received_value' => $value,
					]
				);
			}
		}

		// Boolean / checkbox settings: GravityView stores these as
		// `"1"` / `"0"` strings (legacy form-post convention) but
		// callers may legitimately send true/false/0/1/null/''. Reject
		// only structurally-wrong shapes (objects, arrays, non-numeric
		// strings other than 0/1/true/false/yes/no/on/off).
		if ( in_array( $expected, [ 'boolean', 'checkbox', 'bool' ], true ) ) {
			if ( null === $value || '' === $value || is_bool( $value ) ) {
				return null;
			}
			$normalised = is_string( $value ) ? strtolower( $value ) : $value;
			$accepted   = [ true, false, 0, 1, '0', '1', 'true', 'false', 'yes', 'no', 'on', 'off' ];
			if ( ! in_array( $normalised, $accepted, true ) ) {
				return new \WP_Error(
					'gv_rest_invalid_template_setting_type',
					sprintf(
						/* translators: 1: setting key, 2: literal incoming value */
						__( 'template_settings.%1$s expects a boolean (true/false/0/1). Received: %2$s', 'gk-gravityview' ),
						$display_key,
						wp_json_encode( $value )
					),
					[
						'status'         => 400,
						'setting'        => $display_key,
						'expected_type'  => 'boolean',
						'received_value' => $value,
					]
				);
			}
		}

		// Enum / select settings: when the schema declares `choices`
		// or `options` (either as an assoc map of value=>label or as a
		// list of strings), reject any value not in the declared set.
		// This catches typos and prevents arbitrary strings from
		// landing on settings like sort_direction, format, etc.
		$choices = [];
		if ( isset( $schema['choices'] ) && is_array( $schema['choices'] ) ) {
			$choices = $schema['choices'];
		} elseif ( isset( $schema['options'] ) && is_array( $schema['options'] ) ) {
			$choices = $schema['options'];
		} elseif ( isset( $schema['enum'] ) && is_array( $schema['enum'] ) ) {
			$choices = array_combine( $schema['enum'], $schema['enum'] );
		}

		if ( ! empty( $choices ) ) {
			if ( null === $value || '' === $value ) {
				return null;
			}
			$allowed_values = array_keys( $choices );
			// Tolerate the value being a list-style member when the
			// choices array uses sequential numeric keys (i.e. it's a
			// plain list of options, not value=>label).
			if ( array_is_list( $choices ) ) {
				$allowed_values = array_values( $choices );
			}
			// Array-valued settings carry one choice per member rather
			// than a single value: `checkboxes` posts `{choice: '1'}`
			// (choices are the keys), multisort posts `['a','b']`
			// (choices are the values). Stringifying the whole array
			// here would compare the literal "array" and reject every
			// legitimate write.
			$candidates = [];
			if ( is_array( $value ) ) {
				$candidates = wp_is_numeric_array( $value )
					? array_values( $value )
					: array_keys( $value );
			} elseif ( is_scalar( $value ) ) {
				$candidates = [ $value ];
			}

			$unmatched = null;
			foreach ( $candidates as $candidate ) {
				$candidate = is_scalar( $candidate ) ? (string) $candidate : null;
				$matched   = false;

				foreach ( $allowed_values as $allowed ) {
					if ( (string) $allowed === $candidate ) {
						$matched = true;
						break;
					}
				}

				if ( ! $matched ) {
					$unmatched = $candidate;
					break;
				}
			}

			if ( empty( $candidates ) || null !== $unmatched ) {
				return new \WP_Error(
					'gv_rest_invalid_template_setting_enum',
					sprintf(
						/* translators: 1: setting key, 2: incoming value, 3: comma-separated allowed values */
						__( 'template_settings.%1$s value "%2$s" is not in the declared choice set: %3$s', 'gk-gravityview' ),
						$display_key,
						null !== $unmatched ? $unmatched : gettype( $value ),
						implode( ', ', array_map( 'strval', $allowed_values ) )
					),
					[
						'status'         => 400,
						'setting'        => $display_key,
						'allowed_values' => array_values( array_map( 'strval', $allowed_values ) ),
						'received_value' => $value,
					]
				);
			}
		}

		return null;
	}

	/**
	 * Resolve the full schema entry for a top-level OR namespaced
	 * template setting. Walks all registered sources (including
	 * prefixed ones, so DT/MFV silo settings can't bypass validation) and
	 * returns the FULL definition (`{ type, choices, options, ... }`)
	 * so callers can validate booleans/enums/numbers without re-
	 * resolving the schema themselves.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key     Bare setting key (e.g. "page_size", "responsive").
	 * @param array  $sources Output of template_settings_sources().
	 * @param string $prefix  Namespace prefix when the key is silo-scoped
	 *                        (e.g. "datatables" for `datatables.responsive`),
	 *                        or `''` for top-level core settings.
	 *
	 * @return array<string,mixed>|null Full schema entry, or null when
	 *                                  the key is unknown.
	 */
	private function resolve_template_setting_schema( string $key, array $sources, string $prefix = '' ): ?array {
		foreach ( $sources as $src ) {
			$src_prefix = isset( $src['prefix'] ) ? (string) $src['prefix'] : '';
			if ( $src_prefix !== $prefix ) {
				continue;
			}
			if ( empty( $src['schema_callable'] ) || ! is_callable( $src['schema_callable'] ) ) {
				continue;
			}
			$defs = call_user_func( $src['schema_callable'] );
			if ( ! is_array( $defs ) ) {
				continue;
			}
			if ( isset( $defs[ $key ] ) && is_array( $defs[ $key ] ) ) {
				return $defs[ $key ];
			}
		}
		return null;
	}

	/**
	 * Validate an incoming `conditional_logic` value. The advanced-filter
	 * reader crashes on the public View page when the stored value is a
	 * partial JSON document (no numeric `version` key — its
	 * `should_upgrade()` heuristic treats that as v1 and tries to iterate
	 * non-array entries). Reject malformed shapes BEFORE write so the
	 * crash can never reach the frontend, and record the rejection
	 * reason for the apply response so the caller doesn't have to guess
	 * why their rule vanished.
	 *
	 * Accepts an array or string. Returns:
	 *   - `value`    : the canonical JSON string to store (may be '').
	 *   - `rejected` : true when the value was dropped entirely.
	 *   - `reason`   : short slug describing why (when rejected).
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value Incoming value (string or array).
	 *
	 * @return array{value:string, rejected:bool, reason:string}
	 */
	private function validate_conditional_logic( $value ): array {
		// Re-encode object payloads to canonical JSON so the caller can
		// send either a string or a structured object and the
		// validation rules apply uniformly.
		if ( is_array( $value ) ) {
			$value = wp_json_encode( $value );
			if ( false === $value ) {
				return [
					'value'    => '',
					'rejected' => true,
					'reason'   => 'json_encode_failed',
				];
			}
		}
		$string  = is_string( $value ) ? $value : '';
		$cleaned = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $string );
		// All downstream checks operate on the trimmed copy so JSON
		// with leading/trailing whitespace (common in pretty-printed
		// payloads) doesn't fail the substr-and-decode shape check.
		$trimmed = trim( $cleaned );

		// Empty / null are valid — that's how the editor clears the rule.
		if ( '' === $trimmed || 'null' === $trimmed ) {
			return [
				'value'    => $trimmed,
				'rejected' => false,
				'reason'   => '',
			];
		}

		if ( '{' !== substr( $trimmed, 0, 1 ) ) {
			return [
				'value'    => '',
				'rejected' => true,
				'reason'   => 'not_json_object',
			];
		}

		$decoded = json_decode( $trimmed, true );
		if ( ! is_array( $decoded ) ) {
			return [
				'value'    => '',
				'rejected' => true,
				'reason'   => 'invalid_json',
			];
		}

		// `{}` round-trips fine; the editor's empty shape.
		if ( empty( $decoded ) ) {
			return [
				'value'    => $trimmed,
				'rejected' => false,
				'reason'   => '',
			];
		}

		// Anything claiming to be a filter MUST carry a numeric
		// `version`. Without it the reader runs the v1 upgrade path
		// and crashes on non-array entries.
		if ( ! isset( $decoded['version'] ) || ! is_numeric( $decoded['version'] ) ) {
			return [
				'value'    => '',
				'rejected' => true,
				'reason'   => 'missing_version',
			];
		}

		return [
			'value'    => $trimmed,
			'rejected' => false,
			'reason'   => '',
		];
	}

	/**
	 * Record a per-request rejection. Surfaced to the client under
	 * `warnings` in the apply / patch response so a dropped setting
	 * never goes unnoticed.
	 *
	 * @since 3.0.0
	 *
	 * @param string $area   Area key.
	 * @param string $slot   Slot UID.
	 * @param string $key    Setting slug that was rejected.
	 * @param string $reason Short machine-readable reason.
	 */
	private function record_warning( string $area, string $slot, string $key, string $reason ): void {
		$this->apply_warnings[] = [
			'area'   => $area,
			'slot'   => $slot,
			'key'    => $key,
			'reason' => $reason,
		];
	}

	/**
	 * Sanitize a single setting value. Defaults to `sanitize_text_field`
	 * which is the right choice for the vast majority of settings (CSS
	 * class names, slugs, short labels, capability slugs). Callers that
	 * know a setting type allows richer content — textarea inputs for
	 * Custom Content / HTML / advanced-filter's fallback output — pass
	 * `'textarea'` or `'html'` as `$mode` to allow `wp_kses_post`.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed  $value Incoming value.
	 * @param string $mode  Sanitisation profile: 'default', 'textarea', 'html', 'raw'.
	 *
	 * @return mixed
	 */
	private function sanitize_setting_value( $value, string $mode = 'default' ) {
		if ( is_bool( $value ) ) {
			return $value ? '1' : '0';
		}
		if ( is_numeric( $value ) ) {
			return is_float( $value + 0 ) ? (float) $value : (int) $value;
		}
		if ( is_array( $value ) ) {
			$mode_for_children = $mode;
			return array_map(
				function ( $entry ) use ( $mode_for_children ) {
					return $this->sanitize_setting_value( $entry, $mode_for_children );
				},
				$value
			);
		}

		$string = (string) $value;

		switch ( $mode ) {
			case 'textarea':
			case 'html':
				// Allows merge tags + the basic markup customers use
				// inside Custom Content / advanced-filter fail-output.
				return wp_kses_post( $string );
			case 'raw':
				// CSS, raw JSON blobs (e.g. advanced-filter's
				// conditional_logic JSON). We trust the client to have
				// vetted these but strip dangerous control characters.
				$cleaned = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $string );
				// Defensive: anything destined for advanced-filter's
				// `QueryFilters` reader must either be an empty string
				// or a v2-shaped JSON document. Earlier this controller
				// happily stored partial JSON (no `version` key) which
				// the reader's `should_upgrade()` heuristic detected as
				// v1 and tried to iterate as nested filter records,
				// producing a fatal `Cannot access offset of type
				// string on string` on every public View render. Reject
				// invalid shapes here so the crash can never reach the
				// frontend.
				if ( '' === trim( $cleaned ) || 'null' === trim( $cleaned ) ) {
					return $cleaned;
				}
				if ( '{' === substr( $cleaned, 0, 1 ) ) {
					$decoded = json_decode( $cleaned, true );
					if ( ! is_array( $decoded ) ) {
						return '';
					}
					// Empty filter document — fine, store as-is so the
					// editor round-trips its own empty shape.
					if ( empty( $decoded ) ) {
						return $cleaned;
					}
					// Anything claiming to be a filter MUST carry a
					// numeric `version`. Without it the reader runs
					// the v1 upgrade path and crashes on non-array
					// entries.
					if ( ! isset( $decoded['version'] ) || ! is_numeric( $decoded['version'] ) ) {
						return '';
					}
				}
				return $cleaned;
			default:
				// `sanitize_text_field` strips tags and collapses
				// whitespace but preserves NUL bytes and Unicode
				// directional override characters — both are real
				// foot-guns:
				// - NUL (`\x00`) is invalid in MySQL `utf8mb4_*`
				// text columns, truncates strings in C-aware
				// code paths, and corrupts JSON / log lines.
				// - U+202E RIGHT-TO-LEFT OVERRIDE (and its LTR
				// siblings) is a known label-spoofing vector
				// ("legitimate.exe" → "exe.etamigel"-looking).
				// Strip both BEFORE handing to sanitize_text_field
				// so storage stays clean.
				$string = preg_replace(
					[ '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '/[\x{202A}-\x{202E}\x{2066}-\x{2069}]/u' ],
					'',
					$string
				);
				return sanitize_text_field( $string );
		}
	}

	/**
	 * Resolve the sanitization `mode` for a given setting slug using
	 * the schema we already compute server-side. Falls back to
	 * `default` (sanitize_text_field) for unknown / missing slugs —
	 * the safest choice.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $slot_schema Schema entries for the slot.
	 * @param string $slug        Setting slug.
	 *
	 * @return string
	 */
	private function sanitize_mode_for( array $slot_schema, string $slug ): string {
		foreach ( $slot_schema as $entry ) {
			if ( ( $entry['slug'] ?? null ) !== $slug ) {
				continue;
			}
			$type = (string) ( $entry['type'] ?? '' );
			if ( 'textarea' === $type ) {
				return 'textarea';
			}
			if ( 'html' === $type || 'extension-slot' === $type ) {
				// Extension slots typically round-trip JSON / raw markup
				// the add-on widget owns. Strip control chars but leave
				// quotes / braces alone.
				return 'raw';
			}
			// `conditional_logic` is type=hidden but stores the
			// advanced-filter JSON document; treating it as `default`
			// would route through `sanitize_text_field()` and skip the
			// JSON-shape validation that prevents v1-upgrade crashes
			// on the public View page. Force raw so the validator
			// runs.
			if ( 'conditional_logic' === $slug ) {
				return 'raw';
			}
			return 'default';
		}
		// Same slug-level override when schema lookup misses (e.g.
		// the slot's schema couldn't be computed). Belt-and-braces:
		// the data shape matters more than the type registration.
		if ( 'conditional_logic' === $slug ) {
			return 'raw';
		}
		// Known HTML-bearing settings on built-in field/widget types.
		// `compute_slot_schema()` doesn't always surface the schema for
		// GravityView meta-fields like `custom` (Custom Content), so
		// the slug-level fallback rescues their HTML body from
		// `sanitize_text_field()`'s tag-stripping.
		if ( 'content' === $slug ) {
			return 'textarea';
		}
		return 'default';
	}

	/**
	 * Whether a caller-supplied slot UID is safe to use as a storage
	 * key / lookup key / reflected identifier.
	 *
	 * Slot UIDs reach this controller from external callers (the
	 * Abilities tools `AddViewField` / `MoveViewField` / `PatchViewField`
	 * / `GetViewField` / `CloneViewField`, and direct REST clients) and
	 * end up as array KEYS in the persisted field tree, then get
	 * reflected into server-rendered editor markup (element ids,
	 * `data-` attributes). Without a character-class gate a crafted UID
	 * containing quotes, angle brackets, or whitespace could break out
	 * of an attribute / tag and execute when another user (e.g. an
	 * admin) opens the editor — a stored XSS via the UID itself.
	 *
	 * The accepted shape is the union of every UID format already in the
	 * wild so this never weakens an existing control or rejects a valid
	 * slot:
	 *   - UUID v4 from `generate_slot_uid()` / `wp_generate_uuid4()`
	 *     (lowercase hex + `-`).
	 *   - Legacy 13-char MD5 hex from `Grid.php`.
	 *   - `uniqid( '', true )` from `AdminViews.php` (13 hex + `.` + 8 digits).
	 *   - The `wp_generate_password( 13, false, false )` mixed-case
	 *     alphanumeric seed used by tests + some pre-2.0 customer installs.
	 * All four are covered by `[A-Za-z0-9._-]`. The 64-char ceiling is a
	 * defensive bound — the longest legitimate format (UUID v4) is 36.
	 *
	 * @since 3.0.0
	 *
	 * @param string $uid Caller-supplied slot UID.
	 *
	 * @return bool True when the UID is non-empty, within length, and
	 *              contains only safe characters.
	 */
	private function is_valid_slot_uid( string $uid ): bool {
		return '' !== $uid
			&& strlen( $uid ) <= 64
			&& (bool) preg_match( '/^[A-Za-z0-9._-]+$/', $uid );
	}

	/**
	 * Sanitise an incoming field tree, dropping any keys that aren't
	 * recognizable area/slot pairs. Honors per-setting sanitization
	 * modes so textarea / extension-slot values keep their richer
	 * markup while every other setting goes through the strict
	 * `sanitize_text_field` path.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $view_id View id (used to compute per-slot schemas).
	 * @param array $tree    Incoming tree from the client.
	 *
	 * @return array
	 */
	private function sanitize_field_tree( int $view_id, array $tree ): array {
		$out         = [];
		$template_id = $this->resolve_template_id( $view_id );
		$form_id     = $this->resolve_form_id( $view_id );

		foreach ( $tree as $area => $slots ) {
			if ( ! is_array( $slots ) ) {
				continue;
			}
			$area = (string) $area;
			foreach ( $slots as $slot_uid => $slot ) {
				if ( ! is_array( $slot ) ) {
					continue;
				}
				$slot_uid = (string) $slot_uid;
				// The slot UID becomes a stored array key and is later
				// reflected into editor markup. Drop any slot whose
				// caller-supplied key carries unsafe characters rather
				// than persisting a crafted key (stored-XSS guard).
				// Silent skip mirrors the is_array / empty-key drops
				// already in this loop.
				$slot_uid_is_safe = $this->is_valid_slot_uid( $slot_uid );
				if ( ! $slot_uid_is_safe ) {
					continue;
				}
				$clean = [];

				// Per-slot schema lookup so we know which settings
				// are textarea / html / extension-slot and can apply
				// the right sanitization mode for each.
				$slot_schema = $this->compute_slot_schema(
					$template_id,
					$form_id,
					$area,
					$slot,
					'',
					$view_id
				);

				foreach ( $slot as $key => $value ) {
					$key = sanitize_key( $key );
					if ( '' === $key ) {
						continue;
					}
					$mode          = $this->sanitize_mode_for( $slot_schema, $key );
					$clean[ $key ] = $this->sanitize_setting_value( $value, $mode );
				}
				$out[ $area ][ $slot_uid ] = $clean;
			}
		}
		return $out;
	}

	/**
	 * Build the area inventory by re-running the legacy
	 * `gravityview_template_active_areas` filter — the same source the
	 * legacy editor reads — for each zone the template exposes.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View id.
	 *
	 * @return array<string, array<int, array{areaid: string, label: string, row_uid: string|null}>>.
	 */
	private function build_areas( int $view_id ): array {
		$zones = [ 'directory', 'single', 'edit' ];
		$out   = [];

		// Pass the View's current fields to the active-areas filter
		// so dynamic-grid templates (Layout Builder) include every
		// row the customer has materialised, not just their default
		// empty row. Without this, /areas only ever shows the
		// template's seed row even after the View has been built out.
		$current_fields = $this->read_fields( $view_id );

		foreach ( $zones as $zone ) {
			// Edit Entry doesn't use the layout template's grid —
			// `EditEntryRender::get_configured_edit_fields()` reads
			// a flat list from `edit_edit-fields` regardless of the
			// directory template. Surface that single canonical area
			// so agents/clients placing Edit fields know where to
			// write; without this they'd see Layout Builder rows
			// here and place fields that the Edit renderer ignores.
			if ( 'edit' === $zone ) {
				$out[ $zone ] = [
					[
						'row_uid' => null,
						'areas'   => [
							[
								'areaid'  => 'edit-fields',
								'label'   => __( 'Edit Entry Fields', 'gk-gravityview' ),
								'row_uid' => null,
							],
						],
					],
				];
				continue;
			}

			// Resolve the template id PER ZONE — single can diverge
			// from directory (its own meta key). Computing rows
			// against the wrong template id would surface areas
			// that don't actually exist for that zone.
			$template_id  = (string) $this->resolve_template_id( $view_id, $zone );
			$rows         = $this->resolve_template_active_areas( $template_id, $zone, $current_fields );
			$out[ $zone ] = $this->normalise_rows( (array) $rows );
		}

		return $out;
	}

	/**
	 * Resolves a template's active areas, merging the modern and
	 * legacy filter chains.
	 *
	 * Layout Builder and other dynamic-grid templates register their
	 * row inventory on the modern
	 * `gk/gravityview/admin-views/view/template/active-areas` filter
	 * (4 args, includes the current field collection so they can
	 * derive rows from it). Older static templates register on the
	 * legacy `gravityview_template_active_areas` filter. Calling only
	 * one would miss the other surface — this helper calls both and
	 * returns the first non-empty result, preferring modern.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template_id    Template id.
	 * @param string $zone           Zone key (`directory`, `single`, `edit`).
	 * @param array  $current_fields Current field tree, passed through to
	 *                               grid-aware templates so they can compute
	 *                               rows from the View's actual content.
	 *
	 * @return array Active-areas array (empty when neither filter responds).
	 */
	private function resolve_template_active_areas( string $template_id, string $zone, array $current_fields ): array {
		$modern = (array) apply_filters(
			'gk/gravityview/admin-views/view/template/active-areas',
			[],
			$template_id,
			$zone,
			$current_fields
		);

		// Grid-aware path: ALWAYS run the storage-merge, even when the
		// modern filter returned empty. Layout Builder's `replace_active_
		// areas` derives rows from PLACED FIELDS via `Grid::get_rows_
		// from_collection()` — so rows materialised by `POST /grid/_rows`
		// but not yet populated with fields are invisible to the filter
		// (collection has no elements → no rows). Without this, an
		// agent that creates a row + immediately reads /areas would see
		// a different synthetic seed UID (Grid::uid() mints fresh on
		// every call) instead of the row they just created.

		/** This filter is documented above in `get_layouts()`. */
		$grid_templates = (array) apply_filters( 'gk/gravityview/rest/layouts/list/grid-aware-templates', [ 'gravityview-layout-builder' ] );
		if ( in_array( $template_id, $grid_templates, true ) ) {
			return $this->merge_storage_rows_for_zone( $modern, $zone, $current_fields );
		}

		if ( ! empty( $modern ) ) {
			return $modern;
		}

		$legacy = (array) apply_filters(
			'gravityview_template_active_areas',
			[],
			$template_id,
			$zone,
			$current_fields
		);
		return $legacy;
	}

	/**
	 * Augment a grid-aware template's active-areas list with rows that
	 * exist in storage but carry no placed fields. The Grid renderer's
	 * `get_rows_from_collection()` only sees populated areas; empty
	 * rows minted by `gv_create_grid_row` would otherwise vanish from
	 * /areas until a field lands in them, leaving the caller to guess
	 * an unstable synthetic row UID.
	 *
	 * Walks `_gravityview_directory_fields` keys for the requested
	 * zone, parses `{prefix}-{areaid}::{type}::{row_uid}` triplets,
	 * and synthesises Grid-shaped row entries for any row UID the
	 * filter result didn't already cover. Order: filter result first
	 * (preserves Layout Builder's ordering for placed rows), empty
	 * rows appended after.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $rows           Filter result.
	 * @param string $zone           Zone key (`directory`, `single`).
	 * @param array  $current_fields Field tree.
	 *
	 * @return array
	 */
	private function merge_storage_rows_for_zone( array $rows, string $zone, array $current_fields ): array {
		if ( ! class_exists( '\GV\Grid' ) ) {
			return $rows;
		}

		$existing_uids = [];
		foreach ( $rows as $row ) {
			$uid = Grid::extract_row_uid( $row );
			if ( '' !== $uid ) {
				$existing_uids[ $uid ] = true;
			}
		}

		$prefix       = $zone . '_';
		$prefix_len   = strlen( $prefix );
		$seen_in_zone = [];

		foreach ( array_keys( $current_fields ) as $area_key ) {
			$area_key = (string) $area_key;
			if ( 0 !== strpos( $area_key, $prefix ) ) {
				continue;
			}
			$bare  = substr( $area_key, $prefix_len );
			$parts = explode( '::', $bare, 3 );
			if ( count( $parts ) !== 3 ) {
				continue;
			}
			[ $areaid_with_pfx, $type, $row_uid ] = $parts;
			if ( '' === $row_uid || isset( $existing_uids[ $row_uid ] ) || isset( $seen_in_zone[ $row_uid ] ) ) {
				continue;
			}
			$seen_in_zone[ $row_uid ] = $type;
		}

		if ( empty( $seen_in_zone ) ) {
			return $rows;
		}

		// Wrap each missing row with the Layout Builder area prefix so
		// `Grid::get_row_by_type()` mints area keys that match storage.
		// `prefixed()` reads from a static slot Grid consults inside
		// `get_row_by_type()`, mirroring how the renderer scopes prefix.
		$lb_prefix = '';
		// Pull the prefix from the first area_key we saw so we don't
		// hardcode `gravityview-layout-builder` (forward-compat with
		// future grid-aware templates that register their own prefix).
		foreach ( array_keys( $current_fields ) as $area_key ) {
			$area_key = (string) $area_key;
			if ( 0 !== strpos( $area_key, $prefix ) ) {
				continue;
			}
			$bare  = substr( $area_key, $prefix_len );
			$parts = explode( '::', $bare, 3 );
			if ( count( $parts ) === 3 ) {
				$lb_prefix = preg_replace( '/-(top|left|right|middle|first|second|third|fourth)$/', '', $parts[0] );
				break;
			}
		}

		foreach ( $seen_in_zone as $row_uid => $type ) {
			$row = \GV\Grid::get_row_by_type( $type, $row_uid );
			if ( empty( $row ) ) {
				continue;
			}
			// `get_row_by_type` returns areaids shaped `{areaid}::{type}::{uid}`
			// — Layout Builder's storage keys carry an extra
			// `{prefix}-` segment (`gravityview-layout-builder-top::…`)
			// that the renderer derives from the active template id.
			// Without the prefix, the row UIDs we add here wouldn't
			// align with the storage area keys downstream consumers
			// (apply validators, click-to-select, etc.) check against.
			if ( '' !== $lb_prefix ) {
				foreach ( $row as $col_key => $areas ) {
					if ( ! is_array( $areas ) ) {
						continue;
					}
					foreach ( $areas as $i => $area ) {
						if ( isset( $area['areaid'] ) ) {
							$row[ $col_key ][ $i ]['areaid'] = $lb_prefix . '-' . $area['areaid'];
						}
					}
				}
			}
			$rows[] = $row;
		}

		return $rows;
	}

	/**
	 * Confirms the row UID embedded in a grid-style area key already
	 * exists somewhere the server can see.
	 *
	 * Looks in two places: the View's current field tree (rows
	 * materialised by a previous `POST /views/{id}/grid/_rows` call)
	 * and the apply payload itself (so seeding multiple areas of a
	 * freshly-created row in one round-trip still works).
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id View id whose field tree to consult.
	 * @param string $area    The area key being written, e.g.
	 *                        `directory_gravityview-layout-builder-top::100::ROW_UID`.
	 * @param array  $tree    Current field tree (read prior to apply).
	 * @param array  $payload The full apply payload's `fields` map so.
	 *                        sibling areas in the same call can vouch
	 *                        for a fresh row.
	 *
	 * @return null|\WP_Error
	 */
	private function require_known_grid_row( int $view_id, string $area, array $tree, array $payload ) {
		$parts = explode( '::', $area );
		if ( count( $parts ) < 3 ) {
			return null;
		}
		$row_uid = end( $parts );
		if ( '' === $row_uid ) {
			return null;
		}

		$needle = '::' . $row_uid;

		foreach ( array_keys( $tree ) as $existing_area ) {
			if ( false !== strpos( (string) $existing_area, $needle ) ) {
				return null;
			}
		}

		// Sibling-area check inside the same apply payload: the row
		// counts as known if ANOTHER area in the payload also
		// references it (multi-area writes against a fresh row).
		// The area being validated naturally includes the needle —
		// skip it so the validator doesn't self-vouch and accept
		// every typoed UID.
		foreach ( array_keys( $payload ) as $payload_area ) {
			if ( (string) $payload_area === $area ) {
				continue;
			}
			if ( false !== strpos( (string) $payload_area, $needle ) ) {
				return null;
			}
		}

		return new WP_Error(
			'gv_rest_unknown_grid_row',
			sprintf(
				/* translators: 1: area key the caller supplied. 2: row UID extracted from the area key. */
				__( 'fields[%1$s]: grid row UID "%2$s" doesn\'t exist on this View. Create it with POST /views/%3$d/grid/_rows first.', 'gk-gravityview' ),
				$area,
				$row_uid,
				$view_id
			),
			[
				'status'  => 400,
				'row_uid' => $row_uid,
				'view_id' => $view_id,
			]
		);
	}

	/**
	 * Returns the flat allow-list of `{zone}_{areaid}` storage keys
	 * defined by a template + collection.
	 *
	 * Used by `apply_collection()` to reject payloads that try to
	 * write a previously-unknown area (typo or malicious injection).
	 * Empty return = open mode: when the template can't enumerate its
	 * areas (a third-party template that doesn't register via the
	 * legacy filter), validation is skipped — better to allow the
	 * write than to lock the add-on out entirely. Grid-aware
	 * templates (e.g. Layout Builder) also return empty here because
	 * their area space is dynamic; row UIDs are validated separately
	 * by `require_known_grid_row()`.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template_id The directory template id.
	 * @param string $collection  Either `fields` or `widgets`.
	 * @param int    $view_id     Optional View id; passed to the.
	 *                            template's active-areas filter so
	 *                            grid templates can compute rows
	 *                            from the View's current fields.
	 *
	 * @return array<int,string> Flat allow-list, or empty in open mode.
	 */
	private function known_areas_for( string $template_id, string $collection, int $view_id = 0 ): array {
		// Grid-aware templates (Layout Builder, etc.) generate their
		// areas dynamically from the field collection at render time.
		// `Layout_Builder::replace_active_areas()` derives rows from
		// the fields' positions — an empty area produces no row, so a
		// static area allowlist would reject area keys that the
		// template will accept the moment a field is placed in them.
		// Empty list = open mode = trust the caller for grid-aware
		// surfaces. Static templates still get the strict allowlist.
		// `edit_edit-fields` is whitelisted unconditionally because
		// the Edit Entry renderer reads it regardless of the chosen
		// directory template — agents placing Edit fields target it
		// directly even when the directory zone runs Layout Builder.
		if ( 'fields' === $collection ) {
			/** This filter is documented above in `get_layouts()`. */
			$grid_templates = (array) apply_filters(
				'gk/gravityview/rest/grid-aware-templates',
				[ 'gravityview-layout-builder' ]
			);
			if ( in_array( $template_id, $grid_templates, true ) ) {
				return [];
			}
		}

		$zones = 'widgets' === $collection
			? [ 'header_top', 'header_bottom', 'header_left', 'header_right', 'footer_top', 'footer_bottom', 'footer_left', 'footer_right' ]
			: [ 'directory', 'single', 'edit' ];

		// Layout Builder (and other dynamic-grid templates) hooks
		// the modern `gk/gravityview/admin-views/view/template/active-areas`
		// filter with the 4th `$fields` arg — `replace_active_areas`
		// derives rows from that collection. Static templates hook
		// the legacy `gravityview_template_active_areas` filter; we
		// merge both via `resolve_template_active_areas()` so the
		// validator sees the union for fields and the (single, legacy)
		// widget areas filter for widgets.
		$current_fields = ( 'fields' === $collection && $view_id > 0 )
			? $this->read_fields( $view_id )
			: [];

		$allow = [];

		foreach ( $zones as $zone ) {
			if ( 'widgets' === $collection ) {
				$rows = apply_filters( 'gravityview_widget_active_areas', [], $template_id, $zone );
			} else {
				$rows = $this->resolve_template_active_areas( $template_id, $zone, $current_fields );
			}
			if ( ! is_array( $rows ) ) {
				continue;
			}

			$normalized = $this->normalise_rows( $rows );
			foreach ( $normalized as $row ) {
				foreach ( ( $row['areas'] ?? [] ) as $area ) {
					if ( empty( $area['areaid'] ) ) {
						continue;
					}
					$allow[] = $zone . '_' . (string) $area['areaid'];
				}
			}
		}

		return array_values( array_unique( $allow ) );
	}

	/**
	 * Preserve the row → areas structure from
	 * `gravityview_template_active_areas` so Layout Builder / Grid
	 * templates can render their rows correctly client-side. Each
	 * row carries its own `row_uid` (extracted from any compound
	 * areaid in the row) plus the areas it contains.
	 *
	 * Shape:
	 *
	 *   [
	 *     {
	 *       row_uid: "abc123" | null,
	 *       areas:   [ { areaid, label, row_uid }, … ]
	 *     },
	 *     …
	 *   ]
	 *
	 * The default-list template returns a single implicit row with
	 * `row_uid: null` and every area inside — that's why simple
	 * templates didn't notice the earlier flat shape was wrong.
	 *
	 * @since 3.0.0
	 *
	 * @param array $rows Nested rows from the filter.
	 *
	 * @return array<int, array{row_uid: string|null, areas: array<int, array{areaid: string, label: string, row_uid: string|null}>}>.
	 */
	private function normalise_rows( array $rows ): array {
		$out = [];

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$areas   = [];
			$row_uid = null;

			// The legacy filter returns rows in one of two shapes:
			//
			// Shape A: `[ row_index → [ col_key → [ area, … ] ] ]`
			// e.g. default_list — each row has columns
			// (`'1-1'`, `'1-3'`, `'2-3'`), each column holds
			// a list of areas.
			// Shape B: `[ row_index → [ area, … ] ]`
			// some templates inline the areas directly into
			// the row without the column-key wrapper.
			//
			// Detect by peeking at the first child: if it's a list of
			// area-dicts (sequential integer keys), we're in Shape B
			// and iterate `$row` directly. If it's an associative map
			// keyed by column slug, we're in Shape A and iterate one
			// level deeper.
			$normalized_rows_for_iter = $this->columns_or_areas( $row );

			foreach ( $normalized_rows_for_iter as $area ) {
				if ( ! is_array( $area ) || empty( $area['areaid'] ) ) {
					continue;
				}
				$areaid       = (string) $area['areaid'];
				$label        = (string) ( $area['title'] ?? $area['label'] ?? $areaid );
				$parts        = explode( '::', $areaid );
				$area_row_uid = count( $parts ) === 3 ? $parts[2] : null;
				if ( $area_row_uid && null === $row_uid ) {
					$row_uid = $area_row_uid;
				}
				$areas[] = [
					'areaid'  => $areaid,
					'label'   => $label,
					'row_uid' => $area_row_uid,
				];
			}

			if ( empty( $areas ) ) {
				continue;
			}

			$out[] = [
				'row_uid' => $row_uid,
				'areas'   => $areas,
			];
		}

		return $out;
	}

	/**
	 * Detect whether a row holds areas directly (Shape B) or columns
	 * of areas (Shape A), and return a flat list of area-dicts either
	 * way. Walks one or two levels as needed.
	 *
	 * @since 3.0.0
	 *
	 * @param array $row Row from the legacy filter.
	 *
	 * @return array<int, array> Flat list of area dicts.
	 */
	private function columns_or_areas( array $row ): array {
		// Shape A heuristic: any child value is itself a list of
		// array-of-dicts. If the first child is an array whose first
		// child is also an array, we have nested columns.
		$first_value = reset( $row );
		if ( is_array( $first_value ) ) {
			$first_inner = reset( $first_value );
			if ( is_array( $first_inner ) && ! isset( $first_value['areaid'] ) ) {
				// Shape A: flatten one extra level.
				$flat = [];
				foreach ( $row as $col_areas ) {
					if ( ! is_array( $col_areas ) ) {
						continue;
					}
					foreach ( $col_areas as $area ) {
						$flat[] = $area;
					}
				}
				return $flat;
			}
		}

		// Shape B: the row IS the list of areas.
		return array_values( $row );
	}

	/**
	 * Ids of the forms a View joins, keyed by id.
	 *
	 * Deliberately not memoized: `GVCommon::get_form()` already caches the lookups, and a cache
	 * here would go stale the moment a request writes new joins before reading a schema back.
	 *
	 * @since 3.3.1
	 *
	 * @param int $view_id View id.
	 *
	 * @return array<int, true>
	 */
	private function joined_form_ids( int $view_id ): array {
		if ( $view_id <= 0 ) {
			return [];
		}

		$ids = [];

		foreach ( array_keys( \GV\View::get_joined_forms( $view_id ) ) as $joined_form_id ) {
			$ids[ (int) $joined_form_id ] = true;
		}

		return $ids;
	}

	/**
	 * Compute the schema for a single slot using the legacy
	 * `get_default_field_options()` codepath so add-on-injected
	 * settings appear identically here as in the legacy modal.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template_id         Template slug.
	 * @param int    $form_id             Form id.
	 * @param string $area                Area key.
	 * @param array  $slot                Slot configuration.
	 * @param string $input_type_override Optional. Input type to use instead of inferring one. Default: ''.
	 * @param int    $view_id             Optional. View id, required to honor a joined form's slot. Default: 0.
	 *
	 * @return array<int, array<string, mixed>>.
	 */
	private function compute_slot_schema( string $template_id, int $form_id, string $area, array $slot, string $input_type_override = '', int $view_id = 0 ): array {
		$field_id   = (string) ( $slot['id'] ?? '' );
		$input_type = '';

		// A slot from a joined form belongs to that form, not the View's form. Only forms this
		// View actually joins are honored, so a stale or forged slot cannot pull in a stranger's
		// form schema.
		$slot_form_id = isset( $slot['form_id'] ) && is_scalar( $slot['form_id'] ) ? (int) $slot['form_id'] : 0;

		if ( $slot_form_id > 0 && $slot_form_id !== $form_id && isset( $this->joined_form_ids( $view_id )[ $slot_form_id ] ) ) {
			$form_id = $slot_form_id;
		}

		// `field_type` always stays `field` so the base option set
		// (show_label, custom_label, custom_class, only_loggedin*)
		// populates first. The per-field-id overlay (CustomContent's
		// content/wpautop/oembed, EditLink's lightbox/new_window,
		// DeleteLink's allow_edit_cap, etc.) gets composed on top via
		// the `gravityview_template_{$input_type}_options` filter pass
		// inside `get_default_field_options()`. For numeric GF field
		// ids `input_type` comes from the form schema; for non-numeric
		// registered field types it's the field id itself, which IS
		// the slug each subclass registered against in
		// `Field/Types/GravityViewField.php` (`gravityview_template_
		// {$this->name}_options`). Mirrors the legacy admin's exact
		// dispatch so add-ons that hook either filter surface here.
		$field_type = 'field';

		if ( '' !== $input_type_override ) {
			// Caller (typically `get_field_type_schema` with an
			// `?input_type=` query arg) is asking for a specific
			// overlay — honour it without trying to infer from the
			// slot stub or form schema. This is what lets the
			// standalone `/field-types/field/schema?input_type=email`
			// probe return the email-specific overlay
			// (emailmailto / emailsubject / emailbody / emailencrypt)
			// without needing a real slot bound to a real form.
			$input_type = $input_type_override;
		} elseif ( $form_id > 0 && is_numeric( $field_id ) && class_exists( '\GFAPI' ) ) {
			$form = \GFAPI::get_form( $form_id );
			if ( $form && ! empty( $form['fields'] ) ) {
				foreach ( $form['fields'] as $gf_field ) {
					if ( is_object( $gf_field )
						&& (string) $gf_field->id === $field_id
						&& method_exists( $gf_field, 'get_input_type' ) ) {
						$input_type = (string) $gf_field->get_input_type();
						break;
					}
				}
			}
		} elseif ( '' !== $field_id && ! is_numeric( $field_id ) && $this->is_registered_field( $field_id ) ) {
			$input_type = $field_id;
		}

		// Strip any compound suffix from the area key when figuring out
		// context — the legacy schema filter pattern only knows the bare
		// zone+area name (e.g. `directory_list-title`, not
		// `directory_list-body::50/50::xyz`).
		$bare_area = explode( '::', $area )[0];
		// Prefix-based context derivation. Earlier this also matched
		// `search` anywhere in the area name, which falsely tagged
		// `directory_search_widget_area` as a search context and gave
		// it the wrong schema. Limit `search` to the dedicated search
		// zones (`search-fields-*` etc.) by prefix.
		if ( 0 === strpos( $bare_area, 'single_' ) ) {
			$context = 'single';
		} elseif ( 0 === strpos( $bare_area, 'edit_' ) ) {
			$context = 'edit';
		} elseif ( 0 === strpos( $bare_area, 'search-' )
			|| 0 === strpos( $bare_area, 'search_' ) ) {
			$context = 'search';
		} else {
			$context = 'directory';
		}

		$schema = GravityView_Render_Settings::get_default_field_options(
			$field_type,
			$template_id,
			$field_id,
			$context,
			$input_type,
			$form_id,
			false
		);

		// The legacy method returns an associative array keyed by slug.
		// Reshape to a list so the order survives JSON encoding (PHP
		// associative arrays keep insertion order but some clients
		// re-key on parse). Also detect known add-on mount targets and
		// re-tag them as `extension-slot` so the React inspector
		// renders the placeholder + fires `dialogopen` (advanced-filter
		// in particular ships an `html` setting carrying a
		// `<div class="gv-field-conditional-logic">` placeholder; the
		// plugin's Svelte widget mounts into that div).
		$mount_class_map = self::known_extension_mount_classes();
		$out             = [];
		foreach ( $schema as $slug => $definition ) {
			$definition['slug'] = $slug;

			if ( 'html' === ( $definition['type'] ?? '' ) ) {
				$detected = self::detect_extension_mount( $definition, $mount_class_map );
				if ( $detected ) {
					$definition['type']         = 'extension-slot';
					$definition['mount_target'] = $detected['class'];
					$definition['extension']    = $detected['extension'];
				}
			}

			// Parsed `requires` / `requires_not` rules so AI / generic
			// clients don't have to ship a parser for the bespoke
			// string DSL (`grid_columns>1`, `show_label=1`, bare slugs).
			// The original strings stay in place for the legacy
			// jQuery `toggleRequired()` engine.
			if ( ! empty( $definition['requires'] ) ) {
				$parsed = self::parse_rule( (string) $definition['requires'] );
				if ( ! empty( $parsed ) ) {
					$definition['requires_parsed'] = $parsed;
				}
			}
			if ( ! empty( $definition['requires_not'] ) ) {
				$parsed_not = self::parse_rule( (string) $definition['requires_not'] );
				if ( ! empty( $parsed_not ) ) {
					$definition['requires_not_parsed'] = $parsed_not;
				}
			}

			$out[] = self::trim_schema_item( $definition );
		}

		return $out;
	}

	/**
	 * Strip pure-UI noise from a schema item before it leaves the API.
	 *
	 * Each row of the schema is consumed by either the legacy jQuery
	 * inspector (which uses `priority`, `class`, `tooltip`, `article`,
	 * `codemirror`, etc. for layout / hint rendering) OR by an external
	 * client (Design Studio React, AI agents) that only needs the
	 * actionable contract: type, slug, label, value, choices, contexts,
	 * conditional rules. The legacy admin doesn't read these REST
	 * responses at all — it composes its own settings server-side via
	 * `GravityView_Render_Settings::render_field_options()` directly.
	 * That makes it safe to trim aggressively here.
	 *
	 * Removed:
	 *   - `priority`           UI sort order
	 *   - `class`              CSS class for input element
	 *   - `tooltip`            tooltip id / content
	 *   - `article`            docs URL pointer (UI link)
	 *   - `codemirror`         editor config
	 *   - `mount_target` /     UI mount points for extension widgets;
	 *     `extension`          retained pre-trim for the React Inspector,
	 *                          dropped here for AI / generic callers
	 *   - `requires`,          raw legacy DSL — replaced by `requires_parsed`
	 *     `requires_not`       and `requires_not_parsed`
	 *   - `_id` keys inside    UI dedup hashes from `parse_rule()`
	 *     `requires_parsed`
	 *
	 * Also drops every empty/null/empty-array value from the top level
	 * (the schema rendering side has reasonable defaults — sending
	 * `"value": ""` is just bytes).
	 *
	 * Callers wanting the original verbose shape pass `?detail=full` on
	 * the schema endpoints; that path skips this trim entirely.
	 *
	 * @since 3.0.0
	 *
	 * @param array $definition Single schema item.
	 *
	 * @return array Trimmed item.
	 */
	private static function trim_schema_item( array $definition ): array {
		static $drop_keys = [
			'priority'     => true,
			'class'        => true,
			'tooltip'      => true,
			'article'      => true,
			'codemirror'   => true,
			'mount_target' => true,
			'extension'    => true,
			'requires'     => true,
			'requires_not' => true,
		];

		// Hoist the parsed conditional-logic rules into one envelope
		// (`requires.show` + `requires.hide`) before the main field
		// walk so we don't have to special-case both legacy keys
		// downstream. The inner shape remains Query-Filters native
		// (`{mode, conditions: [{key, operator, value}]}`) so QF can
		// ingest the document via `Filter::from_array()` without a
		// translation step.
		$show_rule = self::compact_rule( $definition['requires_parsed'] ?? null );
		$hide_rule = self::compact_rule( $definition['requires_not_parsed'] ?? null );
		$requires  = [];
		if ( null !== $show_rule ) {
			$requires['show'] = $show_rule;
		}
		if ( null !== $hide_rule ) {
			$requires['hide'] = $hide_rule;
		}

		$out = [];
		foreach ( $definition as $key => $value ) {
			if ( isset( $drop_keys[ $key ] ) ) {
				continue;
			}
			// Replaced by the unified `requires` envelope built above.
			if ( 'requires_parsed' === $key || 'requires_not_parsed' === $key ) {
				continue;
			}
			// Drop empty scalars / arrays. `false` and `0` are MEANINGFUL
			// (default values for checkboxes / numeric settings), so
			// they survive — only `null`, `''`, and `[]` are stripped.
			if ( null === $value || '' === $value || ( is_array( $value ) && [] === $value ) ) {
				continue;
			}
			// `desc` and `label` ship from legacy admin code as HTML
			// blobs (tutorial spans, beacon-anchor article links, the
			// conditional-logic mount markup). Plain text reads the
			// same to humans + AI agents but at a fraction of the
			// bytes — anchor labels survive (wp_strip_all_tags keeps
			// inner text), and runs of whitespace collapse to a
			// single space.
			if ( ( 'desc' === $key || 'label' === $key ) && is_string( $value ) ) {
				$value = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $value ) ) );
				if ( '' === $value ) {
					continue;
				}
			}
			$out[ $key ] = $value;
		}

		if ( ! empty( $requires ) ) {
			$out['requires'] = $requires;
		}

		return $out;
	}

	/**
	 * Collapse a parsed conditional-logic rule to its tightest QF-
	 * compatible representation:
	 *
	 *   - Strip the synthetic `_id` hashes parse_rule() stamps for UI
	 *     dedup (Query Filters' real `_id`s are persistent identifiers
	 *     stored in the document; ours are deterministic content
	 *     hashes that round-trip to the same value, so they carry no
	 *     information).
	 *   - When a Group document holds exactly ONE condition, return
	 *     the leaf Filter directly. QF accepts both shapes via
	 *     `from_array()`, so the wrap is a no-op for consumers but
	 *     adds two keys (`mode`, `conditions[0]`) of overhead.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $rule Parsed rule, parsed-rule stub, or null.
	 *
	 * @return array|null Compact rule, or null when input was empty.
	 */
	private static function compact_rule( $rule ): ?array {
		if ( ! is_array( $rule ) || empty( $rule ) ) {
			return null;
		}
		$rule = self::strip_rule_ids( $rule );

		// Single-condition groups collapse to the bare leaf — saves
		// the `mode` + `conditions` wrapper for the common case.
		if ( isset( $rule['conditions'] ) && is_array( $rule['conditions'] ) && 1 === count( $rule['conditions'] ) ) {
			$leaf = reset( $rule['conditions'] );
			if ( is_array( $leaf ) ) {
				return $leaf;
			}
		}

		return $rule;
	}

	/**
	 * Walk a parsed-rule tree and remove the synthetic `_id` keys
	 * `parse_rule()` adds for UI dedup. Agents key on `key` + `operator`
	 * + `value`; the hash is dead weight in the wire payload.
	 *
	 * @since 3.0.0
	 *
	 * @param array $rule Parsed rule produced by `parse_rule()`.
	 *
	 * @return array Rule with every `_id` stripped recursively.
	 */
	private static function strip_rule_ids( array $rule ): array {
		// ONLY strip ids that match our synthetic format —
		// `parse_rule()` stamps `req-` + 8 hex chars on Groups and
		// `cond-` + 8 hex chars on leaf Filters. A Query Filters
		// document round-tripped through this code path would carry
		// persistent random identifiers in `_id`; matching the prefix
		// + length precisely keeps those alive while still dropping
		// our content-hash dedup ids that carry no information.
		if ( isset( $rule['_id'] ) && is_string( $rule['_id'] )
			&& preg_match( '/^(req|cond)-[a-f0-9]{8}$/', $rule['_id'] ) ) {
			unset( $rule['_id'] );
		}
		if ( isset( $rule['conditions'] ) && is_array( $rule['conditions'] ) ) {
			$rule['conditions'] = array_map(
				static fn( $cond ) => is_array( $cond ) ? self::strip_rule_ids( $cond ) : $cond,
				$rule['conditions']
			);
		}
		return $rule;
	}

	/**
	 * Parse a `requires` / `requires_not` rule into the Query Filters
	 * conditional-logic format — same shape the `gravityview-advanced-filter`
	 * plugin and the Query Filters Svelte component consume. Lets one
	 * conditional-logic UI render every flavour of conditional in the
	 * View configuration: visibility rules, filter conditions, and the
	 * inspector's setting-show-when-other-setting-is-set rules.
	 *
	 * The legacy GravityView DSL → Query Filters operator map:
	 *
	 *   `=` / `==` → `is`
	 *   `!=`       → `isnot`
	 *   `>`        → `>`
	 *   `<`        → `<`
	 *   `>=`       → `>=`         (Query Filters consumes any string)
	 *   `<=`       → `<=`
	 *   bare slug  → `isnot ''` AND `isnot '0'` (matches GravityView's
	 *                truthy semantics where '0' counts as falsy)
	 *
	 * Returns a `ConditionGroup` per the Query Filters type definition
	 * (see `Query-Filters/UI/src/types.js`): `{ _id, mode, conditions }`
	 * where `conditions` is an array of `{ _id, key, operator, value }`
	 * or nested groups. The legacy strings stay on the schema entry
	 * (`requires` / `requires_not`) so the legacy jQuery
	 * `toggleRequired()` engine keeps working unmodified.
	 *
	 * @since 3.0.0
	 *
	 * @param string $rule Rule string in the legacy DSL.
	 *
	 * @return array{_id: string, mode: string, conditions: array}.
	 */
	private static function parse_rule( string $rule ): array {
		$group_id = 'req-' . substr( md5( $rule ), 0, 8 );

		// Priority-ordered: multi-char first so `<=` doesn't match as `<`.
		$operator_map = [
			'<=' => '<=',
			'>=' => '>=',
			'==' => 'is',
			'!=' => 'isnot',
			'='  => 'is',
			'<'  => '<',
			'>'  => '>',
		];

		foreach ( $operator_map as $legacy => $qf_operator ) {
			$pos = strpos( $rule, $legacy );
			if ( false !== $pos ) {
				return [
					'_id'        => $group_id,
					'mode'       => 'and',
					'conditions' => [
						[
							'_id'      => 'cond-' . substr( md5( $rule ), 0, 8 ),
							'key'      => trim( substr( $rule, 0, $pos ) ),
							'operator' => $qf_operator,
							'value'    => trim( substr( $rule, $pos + strlen( $legacy ) ) ),
						],
					],
				];
			}
		}

		// Bare slug → truthy. GravityView's legacy engine treats both
		// `''` and `'0'` as falsy, so the equivalent Query Filters
		// condition is `(slug isnot '') AND (slug isnot '0')`. This
		// preserves the legacy semantics exactly when consumed by a
		// Query Filters-aware evaluator.
		$slug = trim( $rule );
		return [
			'_id'        => $group_id,
			'mode'       => 'and',
			'conditions' => [
				[
					'_id'      => 'cond-' . substr( md5( $rule . ':a' ), 0, 8 ),
					'key'      => $slug,
					'operator' => 'isnot',
					'value'    => '',
				],
				[
					'_id'      => 'cond-' . substr( md5( $rule . ':b' ), 0, 8 ),
					'key'      => $slug,
					'operator' => 'isnot',
					'value'    => '0',
				],
			],
		];
	}

	/**
	 * Allow-list of known add-on mount classes the inspector treats as
	 * extension slots. Filterable so future add-ons can register their
	 * own placeholders without a core release.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, string> `class_name => extension_handle` map.
	 */
	private static function known_extension_mount_classes(): array {
		/**
		 * Filters the map of recognized extension-slot mount classes.
		 *
		 * Keys are CSS class names the add-on injects into its `html`
		 * setting. Values are short extension identifiers used for
		 * telemetry / UI labelling.
		 *
		 * @since 3.0.0
		 *
		 * @param array<string, string> $map
		 */
		return (array) apply_filters(
			'gravityview/inspector/extension_mount_classes',
			[
				'gv-field-conditional-logic' => 'gravityview-advanced-filter',
			]
		);
	}

	/**
	 * Scan an `html`-typed setting's payload for any recognized mount
	 * class. Returns the matched class + extension handle, or null.
	 *
	 * @since 3.0.0
	 *
	 * @param array $definition Setting definition.
	 * @param array $map        Class → extension map.
	 *
	 * @return array{class: string, extension: string}|null.
	 */
	private static function detect_extension_mount( array $definition, array $map ): ?array {
		$haystack = '';
		foreach ( [ 'html', 'desc', 'description', 'value' ] as $key ) {
			if ( isset( $definition[ $key ] ) && is_string( $definition[ $key ] ) ) {
				$haystack .= ' ' . $definition[ $key ];
			}
		}
		if ( '' === $haystack ) {
			return null;
		}

		foreach ( $map as $class => $extension ) {
			if ( false !== strpos( $haystack, 'class="' . $class . '"' )
				|| false !== strpos( $haystack, "class='" . $class . "'" )
				|| false !== strpos( $haystack, $class ) ) {
				return [
					'class'     => (string) $class,
					'extension' => (string) $extension,
				];
			}
		}

		return null;
	}

	/**
	 * Compute a stable version string from the View post's modified
	 * timestamp + a counter we increment on every config write. The
	 * counter is necessary because two PATCH requests in the same
	 * second would otherwise share a version and skip optimistic
	 * concurrency.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View id.
	 *
	 * @return string
	 */
	private function compute_version( int $view_id ): string {
		// Prefer `post_modified_gmt` — `bump_version` advances it on
		// every write so the version string changes per edit. Fall back
		// chain when modified_gmt is empty / `0000-00-00 00:00:00`
		// (rare: very fresh draft created without a follow-up update):
		// post_date_gmt, then request time. Returning the Unix epoch
		// here — as the previous fallback did — masked that the View
		// had never actually been written to; clients reasonably
		// interpreted `1970-01-01T00:00:00Z` as a broken response.
		$timestamp = get_post_modified_time( 'Y-m-d\TH:i:s\Z', true, $view_id );
		if ( ! is_string( $timestamp ) || '' === $timestamp ) {
			$post = get_post( $view_id );
			if ( $post
				&& ! empty( $post->post_date_gmt )
				&& '0000-00-00 00:00:00' !== $post->post_date_gmt ) {
				$timestamp = gmdate( 'Y-m-d\TH:i:s\Z', strtotime( $post->post_date_gmt . ' UTC' ) );
			} else {
				$timestamp = gmdate( 'Y-m-d\TH:i:s\Z' );
			}
		}
		$counter = (int) get_post_meta( $view_id, self::META_VERSION_COUNTER, true );

		return $timestamp . ':' . $counter;
	}

	/**
	 * Increment the write counter AND stamp `post_modified_gmt` so the
	 * version string reflects when the last edit happened.
	 *
	 * Direct UPDATE on the post-table columns (rather than
	 * `wp_update_post`) skips the save_post hook chain — those hooks
	 * fire term assignments, post-meta side effects, transition_post_
	 * status callbacks, etc. Heavy-handed for a "bump the version"
	 * call that runs after every REST write. The targeted UPDATE keeps
	 * `bump_version` cheap (1 SQL statement + 1 meta update) while
	 * still giving the version string a real timestamp.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View id.
	 *
	 * @return void
	 */
	private function bump_version( int $view_id ): void {
		// Skip ALL state mutation when a dry-run frame is active. The
		// direct $wpdb->update on wp_posts bypasses the
		// update_post_metadata filter family that Bootstrap::with_dry_run
		// hooks, so without this short-circuit a rehearsal would mutate
		// post_modified_gmt + the version counter, invalidate every
		// connected client's cached ETag, and 412 their next real write.
		if ( \GravityKit\GravityView\Abilities\Bootstrap::is_dry_run_active() ) {
			return;
		}

		global $wpdb;
		$now_local = current_time( 'mysql', false );
		$now_gmt   = current_time( 'mysql', true );
		$wpdb->update(
			$wpdb->posts,
			[
				'post_modified'     => $now_local,
				'post_modified_gmt' => $now_gmt,
			],
			[ 'ID' => $view_id ],
			[ '%s', '%s' ],
			[ '%d' ]
		);
		clean_post_cache( $view_id );

		$current = (int) get_post_meta( $view_id, self::META_VERSION_COUNTER, true );
		update_post_meta( $view_id, self::META_VERSION_COUNTER, $current + 1 );
	}

	/**
	 * Enforce the `If-Match` precondition when present. Skipped when
	 * the client doesn't send one (allowed for backwards-compatible
	 * write paths during the transition).
	 *
	 * @since 3.0.0
	 *
	 * @param WP_REST_Request $request Request.
	 * @param int             $view_id View id.
	 *
	 * @return true|WP_Error
	 */
	private function check_precondition( WP_REST_Request $request, int $view_id ) {
		// Per-view advisory lock prevents the check-then-act race that
		// would let parallel writes against the SAME ETag all pass the
		// precondition (each reads the pre-bump counter), then trample
		// each other on write. MySQL session-bound `GET_LOCK` auto-
		// releases on connection close so we don't have to thread a
		// `RELEASE_LOCK` through every early return / error path.
		// Acquired before EVERY write handler that calls this method,
		// regardless of whether the caller sent an `If-Match` header,
		// so two concurrent unconditional writes still serialise.
		$this->acquire_view_lock( $view_id );

		$if_match = $request->get_header( 'if-match' );
		if ( ! $if_match ) {
			return true;
		}

		// Accept BOTH wire formats: the HTTP-standard quoted ETag
		// (`"timestamp:counter"`) sent by browser clients, and the bare
		// version string sent by the abilities API (which passes
		// $input['ifMatch'] verbatim from a prior get-view-config
		// response, where `version` is the unquoted body field). Without
		// this normalisation the documented agent happy path 412s on
		// every write because the bare version never strict-equals the
		// quoted current. Strip a single matching pair of double quotes
		// from either side and compare on the bare strings.
		$normalize = static function ( string $candidate ): string {
			$trimmed = trim( $candidate );
			if ( strlen( $trimmed ) >= 2 && '"' === $trimmed[0] && '"' === substr( $trimmed, -1 ) ) {
				return substr( $trimmed, 1, -1 );
			}
			return $trimmed;
		};

		$current_bare = $this->compute_version( $view_id );
		if ( $normalize( (string) $if_match ) !== $current_bare ) {
			return new WP_Error(
				'gv_rest_precondition_failed',
				__( 'View config has been modified since the version you have. Refetch and try again.', 'gk-gravityview' ),
				[
					'status'          => 412,
					'current_version' => $current_bare,
				]
			);
		}

		return true;
	}

	/**
	 * Acquire a MySQL session-bound advisory lock for a view's write
	 * critical section. Bounded wait — never block forever — and best-
	 * effort: a missing `GET_LOCK` (rare, e.g. proxy SQL middleware)
	 * silently no-ops rather than fail the request.
	 *
	 * Auto-releases on database connection close (request shutdown),
	 * so callers don't need to thread a release through every error
	 * path. A second call within the same request is a no-op
	 * (`GET_LOCK` is reentrant on the same session).
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id
	 *
	 * @return void
	 */
	private function acquire_view_lock( int $view_id ): void {
		global $wpdb;
		$lock_name = 'gv_view_' . $view_id;
		$timeout   = 5;
		// suppress_errors so a SQL middleware that doesn't grok GET_LOCK
		// (e.g. some HyperDB / Vitess deployments) doesn't blow up.
		$prev = $wpdb->suppress_errors( true );
		$wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $lock_name, $timeout ) );
		$wpdb->suppress_errors( $prev );
	}

	/**
	 * Resolve the View's template id from post meta directly.
	 *
	 * Reads `_gravityview_directory_template`. Avoids the deprecated
	 * `gravityview_get_template_id()` connector function so this
	 * controller doesn't trigger a deprecation notice on every read.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View id.
	 *
	 * @return string
	 */
	private function resolve_template_id( int $view_id, string $zone = 'directory' ): string {
		$meta_key = $this->template_meta_key_for_zone( $zone );
		$value    = (string) get_post_meta( $view_id, $meta_key, true );

		// `single` and `edit` zones fall back to directory when not
		// explicitly overridden — matches the legacy admin UI's
		// "single uses directory unless customised" convention.
		if ( '' === $value && 'directory' !== $zone ) {
			$value = (string) get_post_meta( $view_id, self::META_DIRECTORY_TEMPLATE, true );
		}

		return $value;
	}

	/**
	 * Return every per-zone template id resolved for this View.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id
	 *
	 * @return array<string,string> Zone → template id (resolved with fallbacks).
	 */
	private function resolve_template_ids( int $view_id ): array {
		// Edit Entry is NOT a layout choice — it always renders via the
		// Edit Entry context regardless of which template the directory
		// or single zone uses. Returning a per-zone template for `edit`
		// suggested a configurability that doesn't exist. Surface only
		// the zones that actually have a meaningful template override.
		return [
			'directory' => $this->resolve_template_id( $view_id, 'directory' ),
			'single'    => $this->resolve_template_id( $view_id, 'single' ),
		];
	}

	/**
	 * Map a zone to the post-meta key GravityView reads for that zone's
	 * template. The Edit zone has historically followed the directory
	 * template (no dedicated meta key in the legacy code), so we use
	 * the directory key for it. If GravityView ever introduces a
	 * dedicated `_gravityview_edit_template` meta, swap the case here.
	 *
	 * @since 3.0.0
	 *
	 * @param string $zone
	 *
	 * @return string
	 */
	private function template_meta_key_for_zone( string $zone ): string {
		switch ( $zone ) {
			case 'single':
				return self::META_SINGLE_TEMPLATE;
			case 'edit':
				// Edit follows directory by convention. Update if a
				// dedicated `_gravityview_edit_template` meta lands.
				return self::META_DIRECTORY_TEMPLATE;
			case 'directory':
			default:
				return self::META_DIRECTORY_TEMPLATE;
		}
	}

	/**
	 * List the currently-registered directory template ids. Source of
	 * truth for any write path that accepts a template_id from a
	 * caller — without this whitelist, a typo or malicious string
	 * lands in post meta and strands the View on an unrenderable
	 * template that nothing on the registry knows about.
	 *
	 * Same filter the /layouts discovery endpoint reads, so caller
	 * discoverability (gv_list_layouts) and write validation are
	 * driven by the same source.
	 *
	 * @since 3.0.0
	 *
	 * @return string[] Registered template ids.
	 */
	private function known_template_ids(): array {
		return array_keys(
			(array) apply_filters( 'gravityview_register_directory_template', [] )
		);
	}

	/**
	 * Validate a caller-supplied template_id against the registered
	 * catalogue. Returns null on success; returns a WP_Error suitable
	 * for direct response otherwise.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template_id Caller-supplied template id.
	 * @param string $context     Human-readable context for the error message
	 *                            (e.g. "template_id", "template_ids.single").
	 *
	 * @return \WP_Error|null
	 */
	private function validate_template_id_or_error( string $template_id, string $context = 'template_id' ) {
		if ( in_array( $template_id, $this->known_template_ids(), true ) ) {
			return null;
		}
		return new \WP_Error(
			'gv_rest_invalid_template',
			sprintf(
				/* translators: 1: context (which field), 2: requested template id */
				__( 'Unknown %1$s "%2$s". Call GET /layouts to list valid ids.', 'gk-gravityview' ),
				$context,
				$template_id
			),
			[ 'status' => 400 ]
		);
	}

	/**
	 * Resolve the View's source form id from post meta directly.
	 *
	 * Reads `_gravityview_form_id`.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View id.
	 *
	 * @return int
	 */
	private function resolve_form_id( int $view_id ): int {
		// Canonical accessor (GVCommon::get_meta_form_id under the hood) rather
		// than a raw post-meta read, so this tracks any future storage change.
		return (int) \gravityview_get_form_id( $view_id );
	}

	/**
	 * Server-side slot UID generator. Matches the legacy editor's
	 * 13-char MD5 hex format (`src/Renderer/Grid.php:72`) so legacy
	 * and modern slot UIDs are indistinguishable.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	/**
	 * Common `id` path-parameter schema. Shared across every route
	 * that targets a specific View. Surfaces in the auto-generated
	 * OPTIONS / `wp-json` index so AI / external clients can
	 * introspect the contract.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array{description: string, type: string, required: bool}>.
	 */
	private function arg_id(): array {
		return [
			'id' => [
				'description' => __( 'GravityView post id.', 'gk-gravityview' ),
				'type'        => 'integer',
				'required'    => true,
			],
		];
	}

	/**
	 * Common `area` path-parameter schema. `{zone}_{areaid}` shape
	 * (`directory_list-title`, `single_table-columns`), optionally
	 * compounded with `::cols::row_uid` for Grid templates.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array>.
	 */
	private function arg_area(): array {
		return [
			'area' => [
				'description'       => __( 'Area key in the form {zone}_{areaid} (e.g. directory_list-title). Layout Builder / Grid templates append ::cols::row_uid for compound keys.', 'gk-gravityview' ),
				'type'              => 'string',
				'required'          => true,
				// WordPress doesn't decode percent-encoded characters captured
				// from path patterns, so a client that encodes `::` as
				// `%3A%3A` would otherwise miss the storage key. Decode once
				// here so every handler reads the canonical literal form.
				'sanitize_callback' => static function ( $value ) {
					return is_string( $value ) ? rawurldecode( $value ) : $value;
				},
			],
		];
	}

	/**
	 * Common `slot` path-parameter schema. 13-char MD5 hex UID
	 * stamped by the row builder when a field is placed.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, array>.
	 */
	private function arg_slot(): array {
		return [
			'slot' => [
				'description' => __( 'Slot UID. New slots are UUID v4 (xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx); pre-existing slots may use the legacy 13-char MD5 hex format (Grid.php) or `uniqid("", true)` format (13 hex + `.` + 8 digits, AdminViews.php), plus the `wp_generate_password()` 13-char mixed-case alphanumeric seed used by tests + some pre-2.0 customer installs.', 'gk-gravityview' ),
				'type'        => 'string',
				'required'    => true,
				// Match the URL-pattern accept range so any slot UID that
				// could land in the meta blob via legacy code paths can be
				// addressed via this surface. The URL pattern lives in
				// `register_routes()` — keep these two in sync.
				'pattern'     => '^[a-zA-Z0-9][\w.-]*$',
			],
		];
	}

	/**
	 * Server-side slot UID generator. Uses `wp_generate_uuid4()`
	 * (RFC 4122 v4 UUID) for new slots. Globally unique by design
	 * — no counter+timestamp collision risk under concurrent
	 * writes, no dependency on microtime resolution, and an
	 * AI / external client can author the same format client-side
	 * and round-trip it through `/config/_apply`.
	 *
	 * Legacy slot UIDs already in storage (13-char MD5 hex from
	 * `Grid.php:72`, or `uniqid("", true)` from `AdminViews.php`)
	 * remain valid; the slot regex accepts all three formats.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function generate_slot_uid(): string {
		// `wp_generate_uuid4` is available since WP 4.7. Fall back to a
		// hex-only synthesiser if WP is somehow missing it (test
		// scaffolds, stripped-down core).
		if ( function_exists( 'wp_generate_uuid4' ) ) {
			return wp_generate_uuid4();
		}
		return sprintf(
			'%08x-%04x-%04x-%04x-%012x',
			mt_rand( 0, 0xffffffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffffffffffff )
		);
	}
}
