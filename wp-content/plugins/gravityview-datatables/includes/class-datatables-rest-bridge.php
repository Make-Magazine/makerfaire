<?php
/**
 * REST inspector bridge for DataTables.
 *
 * Registers DataTables' `_gravityview_datatables_settings` silo meta as a
 * source on GravityView's `gk/gravityview/rest/template-settings/list/sources`
 * filter, so the inspector REST surface (read /config, PATCH
 * /template-settings, /apply, GET /templates/{id}/settings-schema) and the
 * Abilities API shims that delegate to the same InspectorRoute handlers can
 * read, write, and discover the DataTables settings under the `datatables.*`
 * namespace.
 *
 * Those settings live only in DataTables' own silo meta, not the core
 * `_gravityview_template_settings` meta, so without this source the
 * inspector's read / write / schema paths silently miss them: the
 * GravityKit MCP (https://gravitykit.com/mcp/) or another external client
 * configuring a `datatables_table` View could set page_size and
 * sort_field but never turn on Responsive mode, configure RowGroup, or
 * pick which Buttons appear.
 *
 * The schema callable returns setting *definitions* (slug => type / label /
 * group / options), not bare defaults — that is the shape
 * InspectorRoute's schema builder consumes to emit each `datatables.*` slug
 * and to type- and enum-validate writes.
 *
 * Loaded from `GV_Extension_DataTables::core_actions()`.
 *
 * @since 3.9.0
 *
 * @package GravityView_DataTables
 */

defined( 'WPINC' ) || exit;

/**
 * Registers the DataTables silo as a GravityView inspector source.
 *
 * @since 3.9.0
 */
class GV_DataTables_REST_Bridge {
	/**
	 * The DataTables silo post meta key.
	 *
	 * @since 3.9.0
	 */
	const META_KEY = '_gravityview_datatables_settings';

	/**
	 * The inspector namespace DataTables settings surface under.
	 *
	 * @since 3.9.0
	 */
	const PREFIX = 'datatables';

	/**
	 * Registers the inspector source filter.
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	public function load() {
		add_filter( 'gk/gravityview/rest/template-settings/list/sources', [ $this, 'register_source' ] );
	}

	/**
	 * Append the DataTables source to the inspector's source list.
	 *
	 * @since 3.9.0
	 *
	 * @param array $sources Existing registered sources.
	 *
	 * @return array
	 */
	public function register_source( $sources ) {
		if ( ! is_array( $sources ) ) {
			$sources = [];
		}

		$sources[] = [
			'meta_key'        => self::META_KEY,
			'prefix'          => self::PREFIX,
			'schema_callable' => [ $this, 'settings_schema' ],
			'template_ids'    => [ 'datatables_table' ],
			// Claims the `datatables` group for this source so the schema
			// endpoint emits each setting once under `datatables.*` rather
			// than also at top-level via the core source.
			'groups'          => [ self::PREFIX ],
		];

		return $sources;
	}

	/**
	 * The DataTables settings catalog, as inspector schema definitions.
	 *
	 * Each entry is keyed by the silo setting slug and carries the
	 * `type` / `label` / `desc` / `group` (and `options` for selects)
	 * that InspectorRoute's schema builder reads. Every setting belongs
	 * to the `datatables` group claimed by {@see self::register_source()}.
	 *
	 * The compound `export_buttons` setting is stored as a nested map rather
	 * than a flat scalar, so it is not represented here; it remains writable
	 * through the silo via the `datatables.*` prefix, but is not surfaced as
	 * discoverable schema. `rowgroup_position` is also a nested map
	 * (`{start: bool, end: bool}`), but is represented below as a `text`
	 * entry documenting that shape, so it is at least discoverable even
	 * though the schema builder cannot enum-validate an object.
	 *
	 * @since 3.9.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function settings_schema() {
		return [
			'save_state'         => [
				'type'  => 'checkbox',
				'label' => __( 'Save Table State', 'gv-datatables' ),
				'desc'  => __( "Preserve the table's pagination and sorting settings across page reloads.", 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'processing_mode'    => [
				'type'    => 'select',
				'label'   => __( 'Processing Mode', 'gv-datatables' ),
				'desc'    => __( 'Server-side processing calls the website every time there is a change in search, sorting, or paging. Client-side pre-loads all the data so the View will take longer to load initially, but then navigating data will be instantaneous.', 'gv-datatables' ),
				'group'   => self::PREFIX,
				'options' => [
					'serverSide' => __( 'Ajax (Server-side)', 'gv-datatables' ),
					'clientSide' => __( 'Preloaded (Client-side)', 'gv-datatables' ),
				],
			],
			'field_filters'      => [
				'type'  => 'checkbox',
				'label' => __( 'Enable Field Filters', 'gv-datatables' ),
				'desc'  => __( 'Display search fields in the table footer to filter results by each field.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'field_filter_location' => [
				'type'    => 'select',
				'label'   => __( 'Input Location', 'gv-datatables' ),
				'desc'    => __( 'Choose where the filter inputs appear on the table.', 'gv-datatables' ),
				'group'   => self::PREFIX,
				'options' => [
					'footer' => _x( 'Footer', 'The footer of an HTML table', 'gv-datatables' ),
					'header' => _x( 'Header', 'The header of an HTML table', 'gv-datatables' ),
					'both'   => _x( 'Both', 'Both options', 'gv-datatables' ),
				],
			],
			'fields_with_filter' => [
				// Matches the View editor's own multiselect input for this setting
				// (class-datatables-field-filters.php); it is saved and consumed as a
				// collection of field UIDs, not a scalar.
				'type'  => 'multiselect',
				'label' => __( 'Fields With Filter', 'gv-datatables' ),
				'desc'  => __( 'The field UIDs for which filtering is enabled. An empty value means no restriction (all filterable fields); the available fields depend on the View.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'date_filter_type'   => [
				'type'    => 'select',
				'label'   => __( 'Date Filter Type', 'gv-datatables' ),
				'desc'    => __( 'Select how to apply date filters for column values. Date Range is only available with client-side processing.', 'gv-datatables' ),
				'group'   => self::PREFIX,
				'options' => [
					'date'       => __( 'Single Date Input', 'gv-datatables' ),
					'date_range' => __( 'Date Range', 'gv-datatables' ),
				],
			],
			'clear_filters_button' => [
				'type'  => 'checkbox',
				'label' => __( 'Show "Clear Filters" button', 'gv-datatables' ),
				'desc'  => __( 'Display a button that clears all field filters at once when any filter is active.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'responsive'         => [
				'type'  => 'checkbox',
				'label' => __( 'Enable Responsive Tables', 'gv-datatables' ),
				'desc'  => __( 'Optimize table layout for different screen sizes by dynamically inserting and removing columns.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'fixedheader'        => [
				'type'  => 'checkbox',
				'label' => __( 'Enable FixedHeader', 'gv-datatables' ),
				'desc'  => __( 'Float the column headers above the table so the column titles stay visible while scrolling.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'fixedcolumns'       => [
				'type'  => 'checkbox',
				'label' => __( 'Enable FixedColumns', 'gv-datatables' ),
				'desc'  => __( 'Keep the first column visible while scrolling a table horizontally.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'buttons'            => [
				'type'  => 'checkbox',
				'label' => __( 'Enable Buttons', 'gv-datatables' ),
				'desc'  => __( 'Display buttons that let users print or export the current results.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'scroller'           => [
				'type'  => 'checkbox',
				'label' => __( 'Enable Scroller', 'gv-datatables' ),
				'desc'  => __( 'Render large datasets on screen in one continuous, virtually-scrolled page.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'scrolly'            => [
				'type'  => 'number',
				'label' => __( 'Table Height', 'gv-datatables' ),
				'desc'  => __( 'The height, in pixels, of the scrolling area when Scroller is enabled.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'auto_update'        => [
				'type'  => 'checkbox',
				'label' => __( 'Enable Auto-Update', 'gv-datatables' ),
				'desc'  => __( 'Automatically refresh the table at an interval without reloading the page.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'update_interval'    => [
				'type'  => 'number',
				'label' => __( 'Auto-Update Interval', 'gv-datatables' ),
				'desc'  => __( 'How often, in minutes, the table refreshes when Auto-Update is enabled.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'rowgroup'           => [
				'type'  => 'checkbox',
				'label' => __( 'Enable RowGroup Tables', 'gv-datatables' ),
				'desc'  => __( 'Group rows that share a field value.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'rowgroup_field'     => [
				'type'  => 'text',
				'label' => __( 'RowGroup Field', 'gv-datatables' ),
				'desc'  => __( 'The field whose value rows are grouped by. Accepts a field ID; the available fields depend on the View.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'rowgroup_direction' => [
				'type'    => 'select',
				'label'   => __( 'RowGroup Direction', 'gv-datatables' ),
				'desc'    => __( 'The sort direction of the grouping rows.', 'gv-datatables' ),
				'group'   => self::PREFIX,
				'options' => [
					'asc'  => 'ASC',
					'desc' => 'DESC',
				],
			],
			'rowgroup_position'  => [
				'type'  => 'text',
				'label' => __( 'RowGroup Position', 'gv-datatables' ),
				'desc'  => __( 'Where the grouping row appears relative to its group. Stored as an object with boolean start/end keys.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
			'version'            => [
				'type'  => 'text',
				'label' => __( 'DataTables Settings Version', 'gv-datatables' ),
				'desc'  => __( 'The plugin version a View last saved its DataTables settings under, used as a backward-compatibility gate.', 'gv-datatables' ),
				'group' => self::PREFIX,
			],
		];
	}
}

( new GV_DataTables_REST_Bridge() )->load();
