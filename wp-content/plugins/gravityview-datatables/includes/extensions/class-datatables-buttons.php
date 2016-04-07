<?php

class GV_Extension_DataTables_Buttons extends GV_DataTables_Extension {

	protected $settings_key = 'buttons';

	function defaults( $settings ) {

		$settings['buttons'] = true;
		$settings['export_buttons'] = array(
			'copy' => 1,
			'csv' => 1,
			'excel' => 0,
			'pdf' => 0,
			'print' => 1
		);

		return $settings;
	}

	/**
	 * Register the tooltip with Gravity Forms
	 * @param  array  $tooltips Existing tooltips
	 * @return array           Modified tooltips
	 */
	function tooltips( $tooltips = array() ) {

		$tooltips['gv_datatables_buttons'] = array(
			'title' => __('Export Buttons', 'gv-datatables'),
			'value' => __('Display buttons that allow users to print or export the current results.', 'gv-datatables')
		);

		return $tooltips;
	}

	function settings_row( $ds ) {

		$buttons_labels = self::button_labels();

		?>
		<table class="form-table">
			<caption>Buttons</caption>
			<tr valign="top">
				<td colspan="2">
					<?php
						echo GravityView_Render_Settings::render_field_option( 'datatables_settings[buttons]', array(
								'label' => __( 'Enable Buttons', 'gv-datatables' ),
								'type' => 'checkbox',
								'value' => 1,
								'tooltip' => 'gv_datatables_buttons',
							), $ds['buttons'] );
					?>
				</td>
			</tr>
			<tr valign="top">
				<td colspan="2">
					<label><?php esc_html_e( 'Export Buttons', 'gv-datatables' ); ?></label>
					<ul >
						<?php
						foreach( $ds['export_buttons'] as $b_key => $b_value ) {
							if( empty( $buttons_labels[ $b_key ] )) { continue; }

							echo '<li>'.GravityView_Render_Settings::render_field_option(
								'datatables_settings[export_buttons]['. $b_key .']',
								array(
									'label' => $buttons_labels[ $b_key ],
									'type' => 'checkbox',
									'value' => 1
								),
								$ds['export_buttons'][ $b_key ]
							).'</li>';
						}
						?>
					</ul>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Returns the Buttons buttons' labels
	 * @return array
	 */
	public static function button_labels() {
		return array(
			'copy' => __( 'Copy', 'gv-datatables' ),
			'csv' => 'CSV',
			'excel' => 'Excel',
			'pdf' => 'PDF',
			'print' => __( 'Print', 'gv-datatables' )
		);
	}

	/**
	 * Inject Buttons Scripts and Styles if needed
	 */
	function add_scripts( $dt_configs, $views, $post ) {

		$script = false;

		foreach ( $views as $key => $view_data ) {
			if( !$this->is_datatables( $view_data ) || !$this->is_enabled( $view_data['id'] ) ) { continue; }
			$script = true;
		}

		if( !$script ) { return; }

		$path = plugins_url( 'assets/datatables-buttons/', GV_DT_FILE );

		//jsZip
		wp_enqueue_script( 'gv-dt-buttons-jszip', plugins_url( 'assets/jszip/dist/jszip.min.js', GV_DT_FILE ), array( 'jquery' ), GV_Extension_DataTables::version, true );

		//pdfmake
		wp_enqueue_script( 'gv-dt-buttons-pdfmake', plugins_url( 'assets/pdfmake/build/pdfmake.min.js', GV_DT_FILE ), array( 'jquery' ), GV_Extension_DataTables::version, true );
		wp_enqueue_script( 'gv-dt-buttons-vfs-fonts', plugins_url( 'assets/pdfmake/build/vfs_fonts.js', GV_DT_FILE ), array( 'jquery' ), GV_Extension_DataTables::version, true );


		/**
		 * @filter `gravityview_dt_buttons_script_src` Use your own DataTables Buttons core script
		 * @since 2.0
		 * @param string $script_url The JS script url for Buttons
		 */
		wp_enqueue_script( 'gv-dt-buttons', apply_filters( 'gravityview_dt_buttons_script_src', $path .'js/gv-buttons.min.js' ), array( 'jquery', 'gv-datatables', 'gv-dt-buttons-jszip', 'gv-dt-buttons-pdfmake', 'gv-dt-buttons-vfs-fonts' ), GV_Extension_DataTables::version, true );


		/**
		 * @filter `gravityview_dt_buttons_style_src` Use your own Buttons stylesheet
		 * @since 2.0
		 * @param string $styles_url The CSS url for Buttons
		 */
		wp_enqueue_style( 'gv-dt_buttons_style', apply_filters( 'gravityview_dt_buttons_style_src', $path.'css/buttons.css' ), array('gravityview_style_datatables_table'), GV_Extension_DataTables::version, 'all' );


		wp_localize_script( 'gv-datatables-cfg', 'gvDTButtons', array( 'swf' => plugins_url( 'assets/datatables-buttons/swf/flashExport.swf', GV_DT_FILE ) ) );
	}


	/**
	 * Buttons add specific config data based on admin settings
	 */
	function add_config( $dt_config, $view_id, $post  ) {

		// init Buttons
		$dt_config['dom'] = empty( $dt_config['dom'] ) ? 'Blfrtip' : 'B' . $dt_config['dom'];

		// View DataTables settings
		$buttons = $this->get_setting( $view_id, 'export_buttons' );

		// display buttons
		if( !empty( $buttons ) && is_array( $buttons ) ) {

			//fetch buttons' labels
			$button_labels = self::button_labels();

			//calculate who's in
			$buttons = array_keys( $buttons, 1 );

			if( !empty( $buttons ) ) {
				foreach( $buttons as $button ) {
					$button_config = array(
						'extend' => $button,
						'text' => $button_labels[ $button ],
					);

					/**
					 * @filter `gravityview/datatables/button` or `gravityview/datatables/button_{type}` customise the button export options ( `type` is 'pdf', 'csv', 'excel' )
					 * @since 2.0
					 * @param array $button_config Associative array of button options (mandatory 'extend' and 'text' (e.g. add pdf orientation with 'orientation' => 'landscape' )
					 * @param int $view_id View ID
					 */
					$button_config = apply_filters( 'gravityview/datatables/button', apply_filters( 'gravityview/datatables/button_'.$button, $button_config, $view_id ), $button, $view_id );

					$dt_config['buttons'][] = $button_config;
				}
			}

		}

		do_action( 'gravityview_log_debug', __METHOD__ .': Inserting Buttons config. Data: ', $dt_config );

		return $dt_config;
	}

}

new GV_Extension_DataTables_Buttons;
