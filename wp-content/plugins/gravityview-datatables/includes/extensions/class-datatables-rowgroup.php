<?php

/**
 * RowGroup Extension.
 *
 * @link https://datatables.net/extensions/rowgroup/
 */
class GV_Extension_DataTables_RowGroup extends GV_DataTables_Extension {

	protected $settings_key = 'rowgroup';

	function __construct() {
		parent::__construct();

	}

	/**
	 * Set the default setting
	 *
	 * @param array $settings DataTables settings
	 *
	 * @return array           Modified settings
	 */
	function defaults( $settings ) {

		$settings['rowgroup'] = false;

		return $settings;
	}

	/**
	 * Put settings row.
	 *
	 * @param array $ds
	 *
	 * @return void
	 */
	function settings_row( $ds ) {
		$active_fields    = $this->get_active_fields();
		$selected_field   = \GV\Utils::get( $ds, 'rowgroup_field', 0 );

		// A legacy positional index does not match any of the UID-keyed options above, so
		// the select would show nothing chosen even though the setting still resolves to a
		// real field. Map it to that field's UID for display; saving the View then upgrades
		// the stored value to the UID on its own. But a digit-only UID (a plain field ID with
		// no duplicate suffix) is also a real, exact-matching option key, so try that first --
		// only reinterpret as a legacy position when there's no exact match.
		if ( ! array_key_exists( $selected_field, $active_fields ) && is_numeric( $selected_field ) && ctype_digit( (string) $selected_field ) ) {
			$uids           = array_keys( $active_fields );
			$selected_field = isset( $uids[ (int) $selected_field ] ) ? $uids[ (int) $selected_field ] : $selected_field;
		}
		?>
        <table class="form-table">
            <caption>RowGroup</caption>
            <tr valign="top">
                <td colspan="2">
					<?php
					echo GravityView_Render_Settings::render_field_option(
						'datatables_settings[rowgroup]',
						array(
							'label' => __( 'Enable RowGroup Tables', 'gv-datatables' ),
							'type'  => 'checkbox',
							'value' => 1,
							'desc'  => __( 'Group rows that share a field value.', 'gv-datatables' ) . '<br><br>' . esc_html__( 'Note: Scroller is not compatible with RowGroup and Buttons do not support exporting the grouping information.', 'gv-datatables' ),
						),
						$ds['rowgroup']
					);
					?>
                </td>
            </tr>

            <tr valign="top">
                <td scope="row">
					<?php
					echo GravityView_Render_Settings::render_field_option(
						'datatables_settings[rowgroup_field]',
						array(
							'label'   => __( 'Choose a field to row group', 'gv-datatables' ),
							'type'    => 'select',
							'options' => $active_fields,
						),
						$selected_field
					);
					?>
                </td>
            </tr>

            <tr valign="top">
                <td scope="row">
                    <label><?php esc_html_e( 'Row Group Position', 'gv-datatables' ); ?></label>
                    <ul>
						<?php

						$positions = [
							'start' => [
								'label'   => esc_html__( 'Start of the group', 'gv-datatables' ),
								'default' => true,
							],
							'end'   => [
								'label'   => esc_html__( 'End of the group', 'gv-datatables' ),
								'default' => false,
							],
						];

						foreach ( $positions as $key => $position ) {

							echo '<li>' . GravityView_Render_Settings::render_field_option(
									'datatables_settings[rowgroup_position][' . $key . ']',
									array(
										'label' => $position['label'],
										'type'  => 'checkbox',
										'value' => 1,
									),
									\GV\Utils::get( $ds, "rowgroup_position/{$key}", $position['default'] )
								) . '</li>';

						}
						?>
                    </ul>
                </td>
            </tr>


            <tr valign="top">
                <td scope="row">
					<?php
					echo GravityView_Render_Settings::render_field_option(
						'datatables_settings[rowgroup_direction]',
						array(
							'label'   => __( 'Choose the direction of the grouping row', 'gv-datatables' ),
							'type'    => 'select',
							'options' => array(
								'asc'  => 'ASC',
								'desc' => 'DESC',
							),
						),
						\GV\Utils::get( $ds, 'rowgroup_direction', '' )
					);
					?>
                </td>
            </tr>

        </table>
		<?php
	}

	/**
	 * Field position used for the RowGroup column, matching GV_DataTables_Column_Control.
	 */
	const FIELD_POSITION = 'directory_table-columns';

	/**
	 * Get active fields from View.
	 *
	 * Options are keyed by the field's own UID, not by array position: a positional
	 * index silently points at a different field the moment a column is reordered or
	 * deleted, regrouping the table with no warning.
	 *
	 * @return array<string,string>
	 */
	function get_active_fields() {
		global $post;

		if ( empty( $post->ID ) ) {
			return array();
		}

		$fields = gravityview_get_directory_fields( $post->ID, false );

		if ( empty( $fields[ self::FIELD_POSITION ] ) ) {
			return array();
		}

		$options = array();
		foreach ( $fields[ self::FIELD_POSITION ] as $uid => $field ) {
			$options[ $uid ] = $field['label'];
		}

		return $options;
	}

	/**
	 * Resolves the stored `rowgroup_field` setting to a DataTables column index.
	 *
	 * Two formats coexist. A saved integer is the legacy positional index and is used
	 * directly as the column index with no resolution, matching how existing Views
	 * already behaved; changing that would regroup them on upgrade. A saved non-numeric
	 * string is the field's UID; it is resolved against the View's visible fields, since
	 * a field can be dropped from the rendered columns for viewers who cannot act on it
	 * (e.g. Edit Entry links), which shifts its column index independent of its
	 * configured position.
	 *
	 * @since 3.12.0
	 *
	 * A `null` return means the stored UID matched none of the View's currently visible
	 * fields at all (e.g. it was later deleted or hidden); callers must treat that as
	 * "cannot resolve," not silently group by column 0. This is distinct from a UID that
	 * *does* match a visible field whose computed position falls outside the (possibly
	 * shorter, e.g. per-viewer-capability-trimmed) `$dt_config['columns']` -- that case
	 * still falls back to column 0, unchanged.
	 *
	 * @param mixed    $stored_value The raw `rowgroup_field` setting.
	 * @param int      $view_id      The View ID.
	 * @param array    $dt_config    The DataTables configuration being built.
	 *
	 * @return int|null The resolved column index, or null if the UID matches no field.
	 */
	public static function resolve_field_column_index( $stored_value, $view_id, array $dt_config ) {
		if ( is_numeric( $stored_value ) && ctype_digit( (string) $stored_value ) ) {
			return (int) $stored_value;
		}

		if ( empty( $dt_config['columns'] ) ) {
			return 0;
		}

		$view = \GV\View::by_id( $view_id );

		if ( ! $view ) {
			return 0;
		}

		$configuration = $view->fields->by_position( self::FIELD_POSITION )->by_visible()->as_configuration();
		$fields        = \GV\Utils::get( $configuration, self::FIELD_POSITION, array() );

		$position = null;
		$index    = 0;

		foreach ( $fields as $uid => $field_config ) {
			if ( (string) $uid === (string) $stored_value ) {
				$position = $index;
				break;
			}

			$index++;
		}

		if ( null === $position ) {
			return null;
		}

		$field_column_indexes = GV_DataTables_Column_Control::field_column_indexes( $dt_config['columns'] );

		return isset( $field_column_indexes[ $position ] ) ? $field_column_indexes[ $position ] : 0;
	}


	/**
	 * Inject Scripts and Styles if needed
	 */
	function add_scripts( $dt_configs, $views, $post ) {

		if ( ! parent::add_scripts( $dt_configs, $views, $post ) ) {
			return;
		}

		$script_path = plugins_url( 'assets/js/third-party/datatables/', GV_DT_FILE );
		$style_path  = plugins_url( 'assets/css/third-party/datatables/', GV_DT_FILE );

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		/**
		 * Include RowGroup core script (DT plugin)
		 * Use your own DataTables core script by using the `gravityview_dt_rowgroup_script_src` filter
		 *
		 * @since 1.0
		 *
		 * @param string $script_src The URL to the RowGroup script file.
		 */
		wp_enqueue_script( 'gv-dt-rowgroup', apply_filters( 'gravityview_dt_rowgroup_script_src', $script_path . 'dataTables.rowGroup' . $script_debug . '.js' ), array(
			'jquery',
			'gv-datatables',
		), GV_Extension_DataTables::version, true );

		/**
		 * Use your own RowGroup stylesheet by using the `gravityview_dt_rowgroup_style_src` filter
		 *
		 * @since 1.0
		 *
		 * @param string $style_src The URL to the RowGroup stylesheet file.
		 */
		wp_enqueue_style( 'gv-dt_rowgroup_style', apply_filters( 'gravityview_dt_rowgroup_style_src', $style_path . 'rowGroup.dataTables.css' ), array( 'gravityview_style_datatables_table' ), GV_Extension_DataTables::version );

	}

	/**
	 * Add config of rowgroup to js settings. Only runs when `rowgroup` is enabled.
	 *
	 * @inheritDoc
	 * @return array DataTables configuration array with `rowgroup` key set to value as microseconds.
	 */
	function add_config( $dt_config, $view_id, $post, $object ) {

		// If this has already been set, the settings have been overridden.
		if ( isset( $dt_config['rowgroup'] ) ) {
			return $dt_config;
		}

		$rowgroup_status = $this->get_setting( $view_id, 'rowgroup', false );

		if ( empty( $rowgroup_status ) ) {
			return $dt_config;
		}

		$rowgroup_field_setting = $this->get_setting( $view_id, 'rowgroup_field', 0 );
		$rowgroup_field_index   = self::resolve_field_column_index( $rowgroup_field_setting, $view_id, $dt_config );

		// The configured field matches none of the View's currently visible columns (e.g. it
		// was deleted or hidden since being saved). Disable RowGroup rather than silently
		// group by whichever column happens to be first.
		if ( null === $rowgroup_field_index ) {
			gravityview()->log->debug( '[rowgroup_add_config] rowgroup_field does not resolve to a visible column; RowGroup is not applied.' );

			return $dt_config;
		}

		$rowgroup_direction = $this->get_setting( $view_id, 'rowgroup_direction', 'asc' );

		$rowgroup_setting = [
			'status'      => $rowgroup_status,
			'index'       => $rowgroup_field_index,
			'startRender' => null,
			'endRender'   => null,
		];

		$rowgroup_position = $this->get_setting( $view_id, 'rowgroup_position', [ 'start' => 1 ] );

		if ( ! empty( $rowgroup_position['start'] ) ) {
			$rowgroup_setting['startRender'] = true;
		}

		if ( ! empty( $rowgroup_position['end'] ) ) {
			$rowgroup_setting['endRender'] = true;
		}

		// The checkbox field type's hidden `value="0"` companion means both positions can be
		// genuinely unchecked (stored as `0`, not absent). With no start or end render there
		// is no group row to draw, so bail before forcing an order the Sort metabox never
		// showed -- otherwise the table's ordering silently disagrees with the View's Sort
		// setting for no visible grouping benefit.
		if ( null === $rowgroup_setting['startRender'] && null === $rowgroup_setting['endRender'] ) {
			gravityview()->log->debug( '[rowgroup_add_config] Neither group position is enabled; RowGroup is not applied.' );

			return $dt_config;
		}

		// If the View's Sort is already ordering by the grouping column, defer to that
		// direction instead of RowGroup's own, so the Sort metabox stays truthful and the
		// column isn't ordered twice in opposite directions. Otherwise keep forcing the
		// RowGroup direction: DataTables still needs the data pre-sorted by that column
		// for grouping to render intact.
		$primary_order = isset( $dt_config['order'][0] ) ? $dt_config['order'][0] : null;

		// `gravityview_datatables_js_options` can hand back DataTables' flat `order` form
		// ( [ 0, 'asc' ] ), whose element 0 is the column index rather than a pair. Reading
		// $primary_order[0] off that integer warns and evaluates to null, which casts to
		// column 0 and matches the grouping column by accident.
		$order_is_pair = is_array( $primary_order ) && isset( $primary_order[0], $primary_order[1] );

		if ( $order_is_pair && (int) $primary_order[0] === $rowgroup_field_index ) {
			$rowgroup_direction = $primary_order[1];
		}

		// orderFixed understands 'asc' and 'desc' only, and either the setting or a filtered
		// `order` can carry another spelling.
		$rowgroup_direction = 'desc' === strtolower( (string) $rowgroup_direction ) ? 'desc' : 'asc';

		// When rowGroup is enabled, we need to lock ordering by the rowGroup field.
		$dt_config['orderFixed'] = [
			[ $rowgroup_field_index, $rowgroup_direction ],
		];

		$dt_config['rowGroupSettings'] = $rowgroup_setting;

		gravityview()->log->debug( '[rowgroup_add_config] Inserting rowGroup config. Data:', array( 'data' => $dt_config ) );

		return $dt_config;
	}
}

new GV_Extension_DataTables_RowGroup();
