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
		$active_fields = $this->get_active_fields();
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
						\GV\Utils::get( $ds, 'rowgroup_field', 0 )
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
	 * Get active fields from View.
	 *
	 * @return array
	 */
	function get_active_fields() {
		global $post;

		if ( empty( $post->ID ) ) {
			return array();
		}

		$fields = gravityview_get_directory_fields( $post->ID, false );

		if ( empty( $fields['directory_table-columns'] ) ) {
			return array();
		}

		$options = array();
		foreach ( $fields['directory_table-columns'] as $key => $field ) {
			$options[] = $field['label'];
		}

		return $options;
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
		 */
		wp_enqueue_script( 'gv-dt-rowgroup', apply_filters( 'gravityview_dt_rowgroup_script_src', $script_path . 'dataTables.rowGroup' . $script_debug . '.js' ), array(
			'jquery',
			'gv-datatables',
		), GV_Extension_DataTables::version, true );

		/**
		 * Use your own RowGroup stylesheet by using the `gravityview_dt_rowgroup_style_src` filter
		 */
		wp_enqueue_style( 'gv-dt_rowgroup_style', apply_filters( 'gravityview_dt_rowgroup_style_src', $style_path . 'rowGroup.dataTables.css' ), array( 'gravityview_style_datatables_table' ), GV_Extension_DataTables::version );

	}

	/**
	 * Add config of rowgroup to js settings. Only runs when `rowgroup` is enabled.
	 *
	 * @inheritDoc
	 * @return array DataTables configuration array with `rowgroup` key set to value as microseconds.
	 */
	function add_config( $dt_config, $view_id, $post ) {

		// If this has already been set, the settings have been overridden.
		if ( isset( $dt_config['rowgroup'] ) ) {
			return $dt_config;
		}

		$rowgroup_status = $this->get_setting( $view_id, 'rowgroup', false );

		if ( empty( $rowgroup_status ) ) {
			return $dt_config;
		}

		$rowgroup_field_index = (int) $this->get_setting( $view_id, 'rowgroup_field', 0 );
		$rowgroup_direction   = $this->get_setting( $view_id, 'rowgroup_direction', 'asc' );

		// When rowGroup is enabled, we need to lock ordering by the rowGroup field.
		$dt_config['orderFixed'] = [
			[ $rowgroup_field_index, $rowgroup_direction ],
		];

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

		$dt_config['rowGroupSettings'] = $rowgroup_setting;

		gravityview()->log->debug( '[rowgroup_add_config] Inserting rowGroup config. Data:', array( 'data' => $dt_config ) );

		return $dt_config;
	}
}

new GV_Extension_DataTables_RowGroup();
