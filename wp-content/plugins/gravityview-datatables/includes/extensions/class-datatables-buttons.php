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
			'print' => 1,
			'colvis' => 0,
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
			'title' => __('Enable Buttons', 'gv-datatables'),
			'value' => __('Display buttons that allow users to print or export the current results.', 'gv-datatables')
		);

		return $tooltips;
	}

	function settings_row( $ds ) {

		$buttons_labels = self::button_labels('admin');

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
                                'article' => array(
                                    'id' => '5ea73bab04286364bc9914ba',
                                    'url' => 'https://docs.gravityview.co/article/710-datatables-buttons',
                                ),
							), $ds['buttons'] );
					?>
				</td>
			</tr>
			<tr valign="top">
				<td colspan="2">
					<label><?php esc_html_e( 'Buttons', 'gv-datatables' ); ?></label>
					<ul >
						<?php

						$export_button_settings = $ds['export_buttons'];

						foreach( $buttons_labels as $button_key => $button_label ) {

							echo '<li>'.GravityView_Render_Settings::render_field_option(
									'datatables_settings[export_buttons]['. $button_key .']',
									array(
										'label' => $button_label,
										'type' => 'checkbox',
										'value' => 1
									),
									\GV\Utils::get( $export_button_settings, $button_key, 0 )
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
     *
     * @since 2.4 Added $context parameter
     *
     * @param string $context Where the labels are being shown. Allows for modifying the label for the View Settings vs frontend (default: "admin")
     *
	 * @return array
	 */
	public static function button_labels( $context = 'admin' ) {

		if ( 'admin' === $context ) {
		    $colvis_label = __( 'Column Visibility', 'gv-datatables' );
		} else {
			$colvis_label = __( 'Columns', 'gv-datatables' );
        }

		$button_labels = array(
			'copy' => __( 'Copy', 'gv-datatables' ),
			'csv' => 'CSV',
			'excel' => 'Excel',
			'pdf' => 'PDF',
			'print' => __( 'Print', 'gv-datatables' ),
			'colvis' => $colvis_label,
		);

		/**
         * @filter `gravityview_datatables_button_labels` Modify labels buttons
         * @param array $button_labels Array of button types and their associated labels
         * @param string $context Where the labels are being shown. Allows for modifying the label for the View Settings vs frontend (default: "admin")
		 * @since 2.4
		 */
		$button_labels = apply_filters( 'gravityview_datatables_button_labels', $button_labels, $context );

		return $button_labels;
	}

	/**
	 * Inject Buttons Scripts and Styles if needed
	 */
	function add_scripts( $dt_configs, $views, $post ) {

		if( ! $add_scripts = parent::add_scripts( $dt_configs, $views, $post ) ) {
			return;
		}

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
			$button_labels = self::button_labels( 'frontend' );

			//calculate who's in
			$buttons = array_keys( $buttons, 1 );

			if( !empty( $buttons ) ) {
				foreach( $buttons as $button ) {

					$button_config = array(
						'extend' => $button,
						'text' => esc_html( $button_labels[ $button ] ),
					);

					/**
					 * @filter `gravityview/datatables/button` or `gravityview/datatables/button_{type}` customise the button export options ( `type` is 'pdf', 'csv', 'excel', 'colvis' )
					 * @since 2.0
					 * @param array $button_config Associative array of button options (mandatory 'extend' and 'text' (e.g. add pdf orientation with 'orientation' => 'landscape' )
					 * @param int $view_id View ID
					 */
					$button_config = apply_filters( 'gravityview/datatables/button', apply_filters( 'gravityview/datatables/button_'.$button, $button_config, $view_id ), $button, $view_id );

					$dt_config['buttons'][] = $button_config;
				}
			}

		}

		gravityview()->log->debug(  __METHOD__ .': Inserting Buttons config. Data: ', array( 'data' => $dt_config ) );

		return $dt_config;
	}

}

new GV_Extension_DataTables_Buttons;
