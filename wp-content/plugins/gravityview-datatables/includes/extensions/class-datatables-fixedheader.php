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
							'tooltip' => 'gv_datatables_fixedheader',
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
							'tooltip' => 'gv_datatables_fixedcolumns',
						), $ds['fixedcolumns'] );
					?>
				</td>
			</tr>
		</table>
	<?php
	}

	/**
	 * Register the tooltip with Gravity Forms
	 * @param  array  $tooltips Existing tooltips
	 * @return array           Modified tooltips
	 */
	function tooltips( $tooltips = array() ) {

		$tooltips['gv_datatables_fixedcolumns'] = array(
			'title' => __('FixedColumns', 'gv-datatables'),
			'value' => __('Fix the first column in place while horizontally scrolling a table. The first column and its contents will remain visible at all times.', 'gv-datatables')
		);

		$tooltips['gv_datatables_fixedheader'] = array(
			'title' => __('FixedHeader', 'gv-datatables'),
			'value' => __('Float the column headers above the table to keep the column titles visible at all times.', 'gv-datatables')
		);

		return $tooltips;
	}

	/**
	 * Inject FixedHeader & FixedColumns Scripts and Styles if needed
	 */
	function add_scripts( $dt_configs, $views, $post ) {

		if( ! $add_scripts = parent::add_scripts( $dt_configs, $views, $post ) ) {
			return;
		}

		$script_debug = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		$path = plugins_url( 'assets/datatables-fixedheader/', GV_DT_FILE );

		wp_enqueue_script( 'gv-dt-fixedheader', apply_filters( 'gravityview_dt_fixedheader_script_src', $path.'js/dataTables.fixedHeader'.$script_debug.'.js' ), array( 'jquery', 'gv-datatables' ), GV_Extension_DataTables::version, true );
		wp_enqueue_style( 'gv-dt_fixedheader_style', apply_filters( 'gravityview_dt_fixedheader_style_src', $path.'css/fixedHeader.css' ), array('gravityview_style_datatables_table'), GV_Extension_DataTables::version, 'all' );

		$path = plugins_url( 'assets/datatables-fixedcolumns/', GV_DT_FILE );

		wp_enqueue_script( 'gv-dt-fixedcolumns', apply_filters( 'gravityview_dt_fixedcolumns_script_src', $path.'js/dataTables.fixedColumns'.$script_debug.'.js' ), array( 'jquery', 'gv-datatables' ), GV_Extension_DataTables::version, true );
		wp_enqueue_style( 'gv-dt_fixedcolumns_style', apply_filters( 'gravityview_dt_fixedcolumns_style_src', $path.'css/fixedColumns.css' ), array('gravityview_style_datatables_table'), GV_Extension_DataTables::version, 'all' );
	}

	/**
	 * FixedColumns add specific config data based on admin settings
	 */
	function add_config( $dt_config, $view_id, $post  ) {

		// FixedColumns need scrollX to be set
		$dt_config['scrollX'] = true;

		gravityview()->log->debug( '[fixedheadercolumns_add_config] Inserting FixedColumns config. Data: ', array( 'data' => $dt_config ) );

		return $dt_config;
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

		$fixed_config = array();

		$settings = get_post_meta( $gravityview->view->ID, '_gravityview_datatables_settings', true );

		foreach ( array( 'fixedheader', 'fixedcolumns' ) as $key ) {
			if ( ! empty( $settings[ $key ] ) ) {
				$fixed_config[ $key ] = 1;
			} else {
				$fixed_config[ $key ] = 0;
			}
		}

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
