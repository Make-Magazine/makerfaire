<?php

/**
 * Responsive
 * @link https://datatables.net/extensions/responsive/
 */
class GV_Extension_DataTables_Responsive extends GV_DataTables_Extension {

	protected $settings_key = 'responsive';

	/**
	 * Add the `responsive` class to the table to enable the functionality
	 * @param string $classes Existing class attributes
	 * @return  string Possibly modified CSS class
	 */
	function add_html_class( $classes = '' ) {

		// we don't pass the 'responsive' class here to prevent enabling the Responsive extension too soon.
		
		if( $this->is_enabled() ) {
			$classes .= '  nowrap';
		}

		return $classes;
	}

	/**
	 * Register the tooltip with Gravity Forms
	 * @param  array  $tooltips Existing tooltips
	 * @return array           Modified tooltips
	 */
	function tooltips( $tooltips = array() ) {

		$tooltips['gv_datatables_responsive'] = array(
			'title' => __('Responsive Tables', 'gv-datatables'),
			'value' => __('Optimize table layout for different screen sizes through the dynamic insertion and removal of columns from the table.', 'gv-datatables')
		);

		return $tooltips;
	}

	/**
	 * Set the default setting
	 * @param  array $settings DataTables settings
	 * @return array           Modified settings
	 */
	function defaults( $settings ) {

		$settings['responsive'] = false;

		return $settings;
	}

	function settings_row( $ds ) {
	?>
		<table class="form-table">
			<caption>Responsive</caption>
			<tr valign="top">
				<td colspan="2">
					<?php
						echo GravityView_Render_Settings::render_field_option( 'datatables_settings[responsive]', array(
								'label' => __( 'Enable Responsive Tables', 'gv-datatables' ),
								'type' => 'checkbox',
								'value' => 1,
								'tooltip' => 'gv_datatables_responsive',
							), $ds['responsive'] );
					?>
				</td>
			</tr>
		</table>
	<?php
	}


	/**
	 * Inject Scripts and Styles if needed
	 */
	function add_scripts( $dt_configs, $views, $post ) {

		$script = false;
		$responsive_configs = array();

		foreach ( $views as $key => $view_data ) {

			if( !$this->is_datatables( $view_data ) ) { continue; }

			// we need to process all the DT views to be consistent with other DT configurations
			if( $this->is_enabled( $view_data['id'] ) ) {
				$responsive_configs[] = array( 'responsive' => 1, 'hide_empty' => $view_data['atts']['hide_empty'] );
				$script = true;
			} else {
				$responsive_configs[] = array( 'responsive' => 0 );
			}

		}

		if( !$script ) { return; }

		$path = plugins_url( 'assets/datatables-responsive/', GV_DT_FILE );
		$script_debug = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		/**
		 * Include Responsive core script (DT plugin)
		 * Use your own DataTables core script by using the `gravityview_dt_responsive_script_src` filter
		 */
		wp_enqueue_script( 'gv-dt-responsive', apply_filters( 'gravityview_dt_responsive_script_src', $path .'js/dataTables.responsive'. $script_debug .'.js' ), array( 'jquery', 'gv-datatables' ), GV_Extension_DataTables::version, true );

		/**
		 * Use your own Responsive stylesheet by using the `gravityview_dt_responsive_style_src` filter
		 */
		wp_enqueue_style( 'gv-dt_responsive_style', apply_filters( 'gravityview_dt_responsive_style_src', $path.'css/responsive.css' ), array('gravityview_style_datatables_table'), GV_Extension_DataTables::version );

		/**
		 * We need to init the DT responsive extension after DataTables so we could tweak the child render row
		 * @since 1.3.2
		 */
		wp_localize_script( 'gv-dt-responsive', 'gvDTResponsive', $responsive_configs );

	}


}

new GV_Extension_DataTables_Responsive;
