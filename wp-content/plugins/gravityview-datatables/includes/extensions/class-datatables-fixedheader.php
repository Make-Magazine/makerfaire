<?php
/**
 * FixedHeader & FixedColumns
 */
class GV_Extension_DataTables_FixedHeader extends GV_DataTables_Extension {

	protected $settings_key = array('fixedheader', 'fixedcolumns');

	function __construct() {
		parent::__construct();

		add_action( 'gravityview/template/after', array( $this, 'output_config' ) );
	}

	function defaults( $settings ) {
		$settings['fixedcolumns'] = false;
		$settings['fixedheader'] = false;

		return $settings;
	}

	function settings_row( $ds ) {
		?>
		<table class="form-table">
			<caption>
				FixedHeader &amp; FixedColumns
				<p class="description"><?php esc_html_e('Keep headers or columns in place and visible while scrolling a table.', 'gv-datatables' ); ?></p>
			</caption>
			<tr valign="top">
				<td colspan="2">
					<?php
						echo GravityView_Render_Settings::render_field_option( 'datatables_settings[fixedheader]', array(
							'label' => __( 'Enable FixedHeader', 'gv-datatables' ),
							'type' => 'checkbox',
							'value' => 1,
							'desc'  => esc_html__('Float the column headers above the table to keep the column titles visible at all times.', 'gv-datatables' ),
						), $ds['fixedheader'] );
					?>
				</td>
			</tr>
			<tr valign="top">
				<td colspan="2">
					<?php
						echo GravityView_Render_Settings::render_field_option( 'datatables_settings[fixedcolumns]', array(
							'label' => __( 'Enable FixedColumns', 'gv-datatables' ),
							'type' => 'checkbox',
							'value' => 1,
							'desc' => esc_html__( 'Fix the first column in place while horizontally scrolling a table. The first column and its contents will remain visible at all times.', 'gv-datatables' ),
						), $ds['fixedcolumns'] );
					?>
					<p id="gv-dt-legacy-fixedcolumns-note" class="description" role="status" aria-live="polite" hidden></p>
				</td>
			</tr>
		</table>
	<?php
	}

	/**
	 * Inject FixedHeader & FixedColumns Scripts and Styles if needed
	 */
	function add_scripts( $dt_configs, $views, $post ) {

		if( ! $add_scripts = parent::add_scripts( $dt_configs, $views, $post ) ) {
			return;
		}

		$script_debug = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		$script_path = plugins_url( 'assets/js/third-party/datatables/', GV_DT_FILE );
		$style_path = plugins_url( 'assets/css/third-party/datatables/', GV_DT_FILE );

		wp_enqueue_script(
			'gv-dt-fixedheader',
			apply_filters(
				'gravityview_dt_fixedheader_script_src',
				$script_path . 'dataTables.fixedHeader' . $script_debug . '.js'
			),
			array( 'jquery', 'gv-datatables' ),
			GV_Extension_DataTables::version, true
		);

		wp_enqueue_style(
			'gv-dt_fixedheader_style',
			apply_filters(
				'gravityview_dt_fixedheader_style_src',
				$style_path . 'fixedHeader.dataTables' . $script_debug . '.css'
			),
			array( 'gravityview_style_datatables_table' ),
			GV_Extension_DataTables::version
		);

		wp_enqueue_script(
			'gv-dt-fixedcolumns',
			apply_filters(
				'gravityview_dt_fixedcolumns_script_src',
				$script_path . 'dataTables.fixedColumns' . $script_debug . '.js'
			),
			array( 'jquery', 'gv-datatables' ),
			GV_Extension_DataTables::version,
			true
		);
		wp_enqueue_style(
			'gv-dt_fixedcolumns_style',
			apply_filters(
				'gravityview_dt_fixedcolumns_style_src',
				$style_path . 'fixedColumns.dataTables' . $script_debug . '.css'
			),
			array( 'gravityview_style_datatables_table' ),
			GV_Extension_DataTables::version
		);
	}

	/**
	 * FixedColumns add specific config data based on admin settings
	 */
	function add_config( $dt_config, $view_id, $post, $object ) {

		$settings          = $this->get_settings( $view_id );
		$has_fixed_columns = ! empty( $settings['fixedcolumns'] );

		// This class owns both settings and runs when either is enabled, but only
		// FixedColumns needs horizontal scrolling. Forcing it for FixedHeader alone wraps
		// the table in scroll containers and clones the header, which reflows the layout and
		// drops the widths the original cells carry.
		if ( $has_fixed_columns ) {
			$dt_config['scrollX'] = true;
		}

		gravityview()->log->debug( '[fixedheadercolumns_add_config] Inserting FixedColumns config. Data: ', array( 'data' => $dt_config ) );

		return $dt_config;
	}

	/**
	 * Returns the FixedHeader/FixedColumns inline-script config for a View.
	 *
	 * Includes the View ID so the frontend can match the config to its table
	 * instead of relying on DOM order.
	 *
	 * @since 3.10.0
	 *
	 * @param \GV\View $view The View.
	 *
	 * @return array
	 */
	public function get_output_config_data( $view ) {
		$fixed_config = array( 'view_id' => $view->ID );

		$settings = get_post_meta( $view->ID, '_gravityview_datatables_settings', true );

		foreach ( array( 'fixedheader', 'fixedcolumns' ) as $key ) {
			$fixed_config[ $key ] = empty( $settings[ $key ] ) ? 0 : 1;
		}

		return $fixed_config;
	}

	/**
	 * Output the fixed headers configuration.
	 *
	 * @param object $gravityview The template $gravityview object.
	 *
	 * @return void
	 */
	function output_config( $gravityview ) {
		if ( ! $this->is_datatables( $gravityview->view->as_data() ) ) {
			return;
		}

		$fixed_config = $this->get_output_config_data( $gravityview->view );

		?>
			<script type="text/javascript">
				if (!window.gvDTFixedHeaderColumns) {
					window.gvDTFixedHeaderColumns = [];
				}

				window.gvDTFixedHeaderColumns.push(<?php echo json_encode( $fixed_config ); ?>);
			</script>
		<?php
	}
}

new GV_Extension_DataTables_FixedHeader;
