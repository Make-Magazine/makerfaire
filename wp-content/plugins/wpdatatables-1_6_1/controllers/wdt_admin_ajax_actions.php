<?php
/**
 * @package wpDataTables
 * @version 1.6.0
 */
/**
 * Controller for admin panel AJAX actions
 */
?>
<?php

	/**
	 * Handler which returns the AJAX preview
	 */
	 function wdt_get_ajax_preview(){
	 	$no_scripts = !empty($_POST['no_scripts']) ? 1 : 0;
                
		$js_ext = get_option('wdtMinifiedJs') ? '.min.js' : '.js';
                
	 	if(!$no_scripts){
				$scripts = array(
					WDT_JS_PATH.'jquery-datatables/jquery.dataTables.min.js',
					WDT_JS_PATH.'export-tools/dataTables.buttons.js',
					WDT_JS_PATH.'export-tools/buttons.html5.min.js',
					WDT_JS_PATH.'export-tools/buttons.print.min.js',
					WDT_JS_PATH.'export-tools/pdfmake.min.js',
					WDT_JS_PATH.'export-tools/jszip.min.js',
					WDT_JS_PATH.'export-tools/buttons.colVis.js',
					WDT_JS_PATH.'export-tools/vfs_fonts.js',
					WDT_JS_PATH.'datepicker/picker.date.js',
					WDT_JS_PATH.'responsive/lodash.min.js',
					WDT_JS_PATH.'responsive/datatables.responsive.min.js',
					WDT_JS_PATH.'php-datatables/wpdatatables.funcs'.$js_ext,
					WDT_JS_PATH.'jquery-datatables/jquery.dataTables.rowGrouping.min.js',
					WDT_JS_PATH.'jquery-datatables/jquery.dataTables.columnFilter.min.js',
					WDT_JS_PATH.'maskmoney/jquery.maskMoney.js',
					WDT_JS_PATH.'wpdatatables/wpdatatables_inline_editing'.$js_ext,
					WDT_JS_PATH.'wpdatatables/wpdatatables'.$js_ext
				);
	 	}else{
				$scripts = array(WDT_JS_PATH.'wpdatatables/wpdatatables'.$js_ext);
	 	}
		echo wdt_output_table($_POST['table_id'], $no_scripts);
		foreach($scripts as $script){
			echo '<script type="text/javascript" src="'.$script.'"></script>';
	 	}
		exit();
	 }	 
	add_action( 'wp_ajax_wdt_get_preview', 'wdt_get_ajax_preview' );
	
		/**
         * Test the MySQL connection settings
         */
        function wpdatatables_test_mysql_settings(){
            
            $return_array = array( 'success' => array(), 'errors' => array() );
            
            $mysql_connection_settings = $_POST['mysql_settings'];
            
            try{
                $Sql = new PDTSql(  
                            $mysql_connection_settings['host'],
                            $mysql_connection_settings['db'],
                            $mysql_connection_settings['user'],
                            $mysql_connection_settings['password'],
                            $mysql_connection_settings['port']
                        );
                if( $Sql->isConnected() ){
                    $return_array['success'][] = __( 'Successfully connected to the MySQL server', 'wpdatatables' );
                }else{
                    $return_array['errors'][] = __( 'wpDataTables could not connect to MySQL server.', 'wpdatatables' );
                }
            }catch( Exception $e ){
                $return_array['errors'][] = __( 'wpDataTables could not connect to MySQL server. MySQL said: ', 'wpdatatables' ) . $e->getMessage();
            }
            echo json_encode( $return_array );
            exit();
        }
        add_action( 'wp_ajax_wpdatatables_test_mysql_settings', 'wpdatatables_test_mysql_settings' );
	
	/**
	 * Function which saves the global settings for the plugin
	 */
	function wdt_save_settings(){
		
		$_POST = apply_filters( 'wpdatatables_before_save_settings', $_POST );
		
		// Get and write main settings
		$wpUseSeparateCon = ($_POST['wpUseSeparateCon'] == 'true');
		$wpMySqlHost = $_POST['wpMySqlHost'];
		$wpMySqlDB = $_POST['wpMySqlDB'];
		$wpMySqlUser = $_POST['wpMySqlUser'];
		$wpMySqlPwd = $_POST['wpMySqlPwd'];
		$wpMySqlPort = $_POST['wpMySqlPort'];
		$wpRenderFilter = $_POST['wpRenderFilter'];
		$wpInterfaceLanguage = $_POST['wpInterfaceLanguage'];
		$wpDateFormat = $_POST['wpDateFormat'];
		$wpTopOffset = $_POST['wpTopOffset'];
		$wpLeftOffset = $_POST['wpLeftOffset'];
		$wdtBaseSkin = $_POST['wdtBaseSkin'];
		$wdtTablesPerPage = $_POST['wdtTablesPerPage'];
		$wdtNumberFormat = $_POST['wdtNumberFormat'];
		$wdtDecimalPlaces = $_POST['wdtDecimalPlaces'];
		$wdtNumbersAlign = $_POST['wdtNumbersAlign'];
		$wdtCustomJs = $_POST['wdtCustomJs'];
		$wdtCustomCss = $_POST['wdtCustomCss'];
		$wdtMinifiedJs = $_POST['wdtMinifiedJs'];
		$wdtMobileWidth = $_POST['wdtMobileWidth'];
		$wdtTabletWidth = $_POST['wdtTabletWidth'];
                $wdtPurchaseCode = $_POST['wdtPurchaseCode'];
		
		update_option('wdtUseSeparateCon', $wpUseSeparateCon);
		update_option('wdtMySqlHost', $wpMySqlHost);
		update_option('wdtMySqlDB', $wpMySqlDB);
		update_option('wdtMySqlUser', $wpMySqlUser);
		update_option('wdtMySqlPwd', $wpMySqlPwd);
		update_option('wdtMySqlPort', $wpMySqlPort);
		update_option('wdtRenderCharts', 'below'); // Deprecated, delete after 1.6
		update_option('wdtRenderFilter', $wpRenderFilter);
		update_option('wdtInterfaceLanguage', $wpInterfaceLanguage);
		update_option('wdtDateFormat', $wpDateFormat);
		update_option('wdtTopOffset', $wpTopOffset);
		update_option('wdtLeftOffset', $wpLeftOffset);
		update_option('wdtBaseSkin', $wdtBaseSkin);
		update_option('wdtTablesPerPage', $wdtTablesPerPage);
		update_option('wdtNumberFormat', $wdtNumberFormat);
		update_option('wdtDecimalPlaces', $wdtDecimalPlaces);
		update_option('wdtNumbersAlign', $wdtNumbersAlign);
		update_option('wdtCustomJs', $wdtCustomJs);
		update_option('wdtCustomCss', $wdtCustomCss);
		update_option('wdtMinifiedJs', $wdtMinifiedJs);
		update_option('wdtMobileWidth', $wdtMobileWidth);
		update_option('wdtTabletWidth', $wdtTabletWidth);
                update_option('wdtPurchaseCode',$wdtPurchaseCode);
		
		// Get font and color settings
		$wdtFontColorSettings = array();
		$wdtFontColorSettings['wdtHeaderBaseColor'] = $_POST['wdtHeaderBaseColor'];
		$wdtFontColorSettings['wdtHeaderActiveColor'] = $_POST['wdtHeaderActiveColor'];
		$wdtFontColorSettings['wdtHeaderFontColor'] = $_POST['wdtHeaderFontColor'];
		$wdtFontColorSettings['wdtHeaderBorderColor'] = $_POST['wdtHeaderBorderColor'];
		$wdtFontColorSettings['wdtTableOuterBorderColor'] = $_POST['wdtTableOuterBorderColor'];
		$wdtFontColorSettings['wdtTableInnerBorderColor'] = $_POST['wdtTableInnerBorderColor'];
		$wdtFontColorSettings['wdtTableFontColor'] = $_POST['wdtTableFontColor'];
		$wdtFontColorSettings['wdtTableFont'] = $_POST['wdtTableFont'];
		$wdtFontColorSettings['wdtHoverRowColor'] = $_POST['wdtHoverRowColor'];
		$wdtFontColorSettings['wdtOddRowColor'] = $_POST['wdtOddRowColor'];
		$wdtFontColorSettings['wdtEvenRowColor'] = $_POST['wdtEvenRowColor'];
		$wdtFontColorSettings['wdtActiveOddCellColor'] = $_POST['wdtActiveOddCellColor'];
		$wdtFontColorSettings['wdtActiveEvenCellColor'] = $_POST['wdtActiveEvenCellColor'];
		$wdtFontColorSettings['wdtSelectedRowColor'] = $_POST['wdtSelectedRowColor'];
		$wdtFontColorSettings['wdtButtonColor'] = $_POST['wdtButtonColor'];
		$wdtFontColorSettings['wdtButtonBorderColor'] = $_POST['wdtButtonBorderColor'];
		$wdtFontColorSettings['wdtButtonFontColor'] = $_POST['wdtButtonFontColor'];
		$wdtFontColorSettings['wdtButtonBackgroundHoverColor'] = $_POST['wdtButtonBackgroundHoverColor'];
		$wdtFontColorSettings['wdtButtonBorderHoverColor'] = $_POST['wdtButtonBorderHoverColor'];
		$wdtFontColorSettings['wdtButtonFontHoverColor'] = $_POST['wdtButtonFontHoverColor'];
		$wdtFontColorSettings['wdtModalFontColor'] = $_POST['wdtModalFontColor'];
		$wdtFontColorSettings['wdtModalBackgroundColor'] = $_POST['wdtModalBackgroundColor'];
		$wdtFontColorSettings['wdtOverlayColor'] = $_POST['wdtOverlayColor'];
		$wdtFontColorSettings['wdtBorderRadius'] = $_POST['wdtBorderRadius'];
		
		// Serialize settings and save to DB
		update_option('wdtFontColorSettings',serialize($wdtFontColorSettings));
		
		do_action( 'wpdatatables_after_save_settings' );
		die( 'success' );
	}
	add_action( 'wp_ajax_wdt_save_settings', 'wdt_save_settings');
	
	/**
	 * Saves the general settings for the table, tries to generate the table 
	 * and default settings for the columns
	 */
	function wdt_save_table(){
		global $wpdb, $wdt_var1, $wdt_var2, $wdt_var3;
		
		$_POST = apply_filters( 'wpdatatables_before_save_table', $_POST );
		$table_id = $_POST['table_id'];
		$table_title = $_POST['table_title'];
		$show_title = $_POST['show_title'];
		$table_type = $_POST['table_type'];
		if(($table_type == 'csv') || ($table_type == 'xls')){
			$uploads_dir = wp_upload_dir();
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				$table_content = str_replace($uploads_dir['baseurl'], str_replace('\\', '/', $uploads_dir['basedir']), $_POST['table_content']);
			}else{
				$table_content = str_replace($uploads_dir['baseurl'], $uploads_dir['basedir'], $_POST['table_content']);
			}
		}else{
				$table_content = $_POST['table_content'];
		}
		$table_hide_before_loaded = ($_POST['hide_before_loaded'] == 'true');
		$table_advanced_filtering = ($_POST['table_advanced_filtering'] == 'true');
		$table_filter_form = ($_POST['table_filter_form'] == 'true');
		$table_tools = ($_POST['table_tools'] == 'true');
		if( $table_tools ){
			$table_tools_config = serialize( $_POST['table_tools_config'] );
		}else{
			$table_tools_config = serialize( array() );
		}
		$table_sorting = ($_POST['table_sorting'] == 'true');
		$table_fixed_layout = ($_POST['fixed_layout'] == 'true');
		$table_word_wrap = ($_POST['word_wrap'] == 'true');
		$table_display_length = $_POST['table_display_length'];
		$table_fixheader = ($_POST['table_fixheader'] == 'true');
		$table_fixcolumns = $_POST['table_fixcolumns'];
		$table_serverside = ($_POST['table_serverside'] == 'true');
		if( $table_serverside ){
			$table_auto_refresh = $_POST['table_auto_refresh'];
		}else{
			$table_auto_refresh = 0;
		}
		$table_editable = in_array( $table_type, array('mysql', 'manual') ) ? ($_POST['table_editable'] == 'true') : false;
		$table_inline_editing = ($_POST['table_inline_editing'] == 'true');
		$table_popover_tools = ($_POST['table_popover_tools'] == 'true');
		$table_mysql_name = ($table_editable || $table_type == 'manual') ? $_POST['table_mysql_name'] : '';
		$table_responsive = $_POST['responsive'] == 'true';
		$table_scrollable = $_POST['scrollable'] == 'true';
		$table_editor_roles = !empty($_POST['editor_roles']) ? $_POST['editor_roles'] : '';
		$table_edit_only_own_rows = !empty( $_POST['edit_only_own_rows'] ) ? (int) $_POST['edit_only_own_rows'] : 0;
		$table_userid_column_id = !empty( $_POST['userid_column_id'] ) ? (int) $_POST['userid_column_id']  : 0;

		$wdt_var1 = isset( $_POST['var1_placeholder'] ) ? $_POST['var1_placeholder']  : '';
		$wdt_var2 = isset( $_POST['var2_placeholder'] ) ? $_POST['var2_placeholder']  : '';
		$wdt_var3 = isset( $_POST['var3_placeholder'] ) ? $_POST['var3_placeholder']  : '';
		
		if(!$table_fixheader){
			$table_fixcolumns = -1;
		}else{
			$table_fixcolumns = (int)$table_fixcolumns;
		}
		if(!$table_id){
			// adding new table
			// trying to generate a WPDataTable
			$res = wdt_try_generate_table( $table_type, $table_content );
			if( !empty( $res['error'] ) ){
					// if WPDataTables returns an error, replying to the page
					echo json_encode( $res ); die();
			}else{
				// if no problem reported, first saving the table parameters to DB
				$table_array = array(
										'title' => $table_title,
										'show_title' => (int) $show_title,
										'table_type' => $table_type,
										'content' => $table_content,
										'filtering' => (int)$table_advanced_filtering,
										'filtering_form' => (int)$table_filter_form,
										'sorting' => (int)$table_sorting,
										'fixed_layout' => (int)$table_fixed_layout,
										'responsive' => (int)$table_responsive,
										'scrollable' => (int)$table_scrollable,
										'word_wrap' => (int)$table_word_wrap,
										'tools' => (int)$table_tools,
										'display_length' => $table_display_length,
										'fixed_columns' => $table_fixcolumns,
										'chart' => 'none', // deprecated, delete after 1.6
										'chart_title' => '', // deprecated, delete after 1.6
										'server_side' => (int)$table_serverside,
										'auto_refresh' => (int)$table_auto_refresh,
										'editable' => (int)$table_editable,
										'inline_editing' => (int)$table_inline_editing,
										'popover_tools' => (int)$table_popover_tools,
										'editor_roles' => $table_editor_roles,
										'mysql_table_name' => $table_mysql_name,
										'hide_before_load' => $table_hide_before_loaded,
										'edit_only_own_rows' => $table_edit_only_own_rows,
										'userid_column_id' => $table_userid_column_id,
										'var1' => $wdt_var1,
										'var2' => $wdt_var2,
										'var3' => $wdt_var3,
										'tabletools_config' => $table_tools_config
								);

				$table_array = apply_filters('wpdatatables_filter_insert_table_array', $table_array);
				
				$wpdb->insert($wpdb->prefix .'wpdatatables', $table_array);
				// get the newly generated table ID
				$table_id = $wpdb->insert_id;
				$res['table_id'] = $table_id;
				// creating default columns for the new table
				$res['columns'] = wdt_create_columns_from_table( $res['table'], $table_id );
				do_action( 'wpdatatables_after_save_table', $table_id );
				echo json_encode($res); die();
			}
		}else{
                        // Trying to rebuild the table and reloading the columnset
                        $res = wdt_try_generate_table( $table_type, $table_content );
                        if(!empty($res['error'])){
                                // if WPDataTables returns an error, replying to the page
                                do_action( 'wpdatatables_after_save_table' );
                                echo json_encode( $res ); die();
                        }else{
                                // otherwise updating the table
                                $table_array = array(
                                                    'title' => $table_title,
                                                    'show_title' => (int) $show_title,
                                                    'table_type' => $table_type,
                                                    'content' => $table_content,
                                                    'filtering' => (int)$table_advanced_filtering,
                                                    'filtering_form' => (int)$table_filter_form,
                                                    'sorting' => (int)$table_sorting,
                                                    'fixed_layout' => (int)$table_fixed_layout,
                                                    'word_wrap' => (int)$table_word_wrap,
                                                    'responsive' => (int)$table_responsive,
									                'scrollable' => (int)$table_scrollable,
                                                    'tools' => (int)$table_tools,
                                                    'display_length' => $table_display_length,
                                                    'fixed_columns' => $table_fixcolumns,
                                                    'chart' => 'none',  // deprecated, delete after 1.6
                                                    'chart_title' => '',  // deprecated, delete after 1.6
                                                    'server_side' => (int)$table_serverside,
													'auto_refresh' => (int)$table_auto_refresh,
                                                    'editable' => (int)$table_editable,
													'inline_editing' => (int)$table_inline_editing,
													'popover_tools' => (int)$table_popover_tools,
                                                    'editor_roles' => $table_editor_roles,
                                                    'mysql_table_name' => $table_mysql_name,
                                                    'hide_before_load' => $table_hide_before_loaded,
                                                    'edit_only_own_rows' => $table_edit_only_own_rows,
                                                    'userid_column_id' => $table_userid_column_id,
                                                    'var1' => $wdt_var1,
                                                    'var2' => $wdt_var2,
                                                    'var3' => $wdt_var3,
													'tabletools_config' => $table_tools_config
                                                );

                                $table_array = apply_filters( 'wpdatatables_filter_update_table_array', $table_array, $table_id );

                                $wpdb->update(
												$wpdb->prefix.'wpdatatables',
												$table_array,
												array(
														'id' => $table_id
														)
												);
                                $res['table_id'] = $table_id;
                                // rebuilding the columnset
                                $res['columns'] = wdt_create_columns_from_table( $res['table'], $table_id );
                                do_action( 'wpdatatables_after_save_table' );
                                echo json_encode($res); die();				 	
                        }
		}
		
	}
	add_action( 'wp_ajax_wdt_save_table', 'wdt_save_table');
	
	/**
	 * Saves the settings for columns
	 */
	function wdt_save_columns(){
		global $wpdb;
		
		$_POST = apply_filters( 'wpdatatables_before_save_columns', $_POST );
		
		$table_id = intval( $_POST['table_id'] );
		$columns = json_decode( stripslashes_deep( $_POST['columns'] ), true );

		foreach($columns as $column){
			if( $column['orig_header'] == 'wdt_ID' ){
				$column['id_column'] = 'true';
			}
			if( !empty( $column['id'] ) ){
				// Updating existing columns
				$wpdb->update(
					$wpdb->prefix.'wpdatatables_columns',
					array(
						'display_header' => $column['display_header'],
						'css_class' => $column['css_class'],
						'possible_values' => !empty( $column['possible_values'] ) ? $column['possible_values'] : '',
						'default_value' => !empty( $column['default_value'] ) ? $column['default_value'] : '',
						'input_type' => isset($column['input_type']) ? $column['input_type'] : '',
						'input_mandatory' => (int) $column['input_mandatory'],
						'filter_type' => !empty( $column['filter_type'] ) ? $column['filter_type'] : '',
						'column_type' => $column['column_type'],
						'id_column' => (int)($column['id_column'] == 'true'),
						'group_column' => (int)($column['group_column'] == 'true'),
						'sort_column' => (int)($column['sort_column']),
						'hide_on_phones' => (int)($column['hide_on_phones'] == 'true'),
						'hide_on_tablets' => (int)($column['hide_on_tablets'] == 'true'),
						'use_in_chart' => 0,  // deprecated, delete after 1.6
						'chart_horiz_axis' => 0,  // deprecated, delete after 1.6
						'visible' => (int)($column['visible'] == 'true'),
						'width' => $column['width'],
						'text_before' => $column['text_before'],
						'text_after' => $column['text_after'],
						'formatting_rules' => $column['formatting_rules'],
						'color' => isset( $column['color'] ) ? $column['color'] : '',
						'pos' => $column['pos'],
						'sum_column' => (int)$column['sum_column'],
						'calc_formula' => $column['calc_formula'],
						'skip_thousands_separator' => (int)$column['skip_thousands_separator']
					),
					array(
						'id' => $column['id']
					)
				);
			}else{
				// inserting new formula columns
				$wpdb->insert(
					$wpdb->prefix.'wpdatatables_columns',
					array(
						'orig_header' => $column['orig_header'],
						'display_header' => $column['display_header'],
						'css_class' => $column['css_class'],
						'possible_values' => !empty( $column['possible_values'] ) ? $column['possible_values'] : '',
						'default_value' => !empty( $column['default_value'] ) ? $column['default_value'] : '',
						'input_type' => isset($column['input_type']) ? $column['input_type'] : '',
						'input_mandatory' => (int) $column['input_mandatory'],
						'filter_type' => !empty( $column['filter_type'] ) ? $column['filter_type'] : '',
						'column_type' => $column['column_type'],
						'id_column' => (int)($column['id_column'] == 'true'),
						'group_column' => (int)($column['group_column'] == 'true'),
						'sort_column' => (int)($column['sort_column']),
						'hide_on_phones' => (int)($column['hide_on_phones'] == 'true'),
						'hide_on_tablets' => (int)($column['hide_on_tablets'] == 'true'),
						'use_in_chart' => 0,  // deprecated, delete after 1.6
						'chart_horiz_axis' => 0,  // deprecated, delete after 1.6
						'visible' => (int)($column['visible'] == 'true'),
						'width' => $column['width'],
						'text_before' => $column['text_before'],
						'text_after' => $column['text_after'],
						'formatting_rules' => $column['formatting_rules'],
						'color' => isset( $column['color'] ) ? $column['color'] : '',
						'pos' => $column['pos'],
						'sum_column' => (int)$column['sum_column'],
						'calc_formula' => $column['calc_formula'],
						'skip_thousands_separator' => (int)$column['skip_thousands_separator'],
						'table_id' => (int)$table_id
					)
				);
				$column['id'] = $wpdb->insert_id;
			}
		}
		$res['columns'] = wdt_get_columns_by_table_id( $table_id );
		
		do_action( 'wpdatatables_after_save_columns' );
		
		echo json_encode($res); exit();
	}
	add_action( 'wp_ajax_wdt_save_columns', 'wdt_save_columns');
	
	/**
	 * Duplicate the table
	 */
	 function wpdatatables_duplicate_table(){
	 	global $wpdb;
	 	
	 	$table_id = $query = wpdatatables_sanitize_query( $_POST['table_id'] );
	 	$new_table_name = wpdatatables_sanitize_query( $_POST['new_table_name'] );
	 	
	 	// Getting the table data
	 	$table_data = wdt_get_table_by_id( $table_id );
	 	
	 	// Creating new table
	 	$wpdb->insert(
	 		$wpdb->prefix.'wpdatatables',
	 		array(
	 			'title' => $new_table_name,
				'show_title' => $table_data['show_title'],
	 			'table_type' => $table_data['table_type'],
	 			'content' => $table_data['content'],
	 			'filtering' => $table_data['filtering'],
	 			'filtering_form' => $table_data['filtering_form'],
	 			'sorting' => $table_data['sorting'],
	 			'tools' => $table_data['tools'],
	 			'display_length' => $table_data['display_length'],
	 			'fixed_columns' => $table_data['fixed_columns'],
	 			'chart' => 'none',
	 			'chart_title' => '',
	 			'server_side' => $table_data['server_side'],
				'auto_refresh' => $table_data['auto_refresh'],
	 			'fixed_layout' => $table_data['fixed_layout'],
	 			'word_wrap' => $table_data['word_wrap'],
	 			'editable' => $table_data['editable'],
				'inline_editing' => $table_data['inline_editing'],
				'popover_tools' => $table_data['popover_tools'],
	 			'mysql_table_name' => $table_data['mysql_table_name'],
	 			'responsive' => $table_data['responsive'],
				'scrollable' => $table_data['scrollable'],
	 			'filtering_form' => $table_data['filtering_form'],
	 			'editor_roles' => $table_data['editor_roles'],
				'hide_before_load' => $table_data['hide_before_load'],
				'edit_only_own_rows' => $table_data['edit_only_own_rows'],
				'userid_column_id' => $table_data['userid_column_id'],
				'var1' => $table_data['var1'],
				'var2' => $table_data['var2'],
				'var3' => $table_data['var3'],
				'tabletools_config' => $table_data['tabletools_config']
	 		)
	 	);
	 	
	 	$new_table_id = $wpdb->insert_id;
	 	
	 	// Getting the column data
	 	$columns = wdt_get_columns_by_table_id( $table_id );
	 	
	 	// Creating new columns
	 	foreach($columns as $column){
	 		$wpdb->insert(
	 			$wpdb->prefix.'wpdatatables_columns',
	 			array(
	 				'table_id' => $new_table_id,
	 				'orig_header' => $column->orig_header,
					'css_class' => $column->css_class,
	 				'display_header' => $column->display_header,
	 				'filter_type' => $column->filter_type,
	 				'column_type' => $column->column_type,
	 				'group_column' => $column->group_column,
	 				'use_in_chart' => $column->use_in_chart,
	 				'chart_horiz_axis' => $column->chart_horiz_axis,
	 				'visible' => $column->visible,
	 				'width' => $column->width,
					'text_before' => $column->text_before,
					'text_after' => $column->text_after,
					'formatting_rules' => $column->formatting_rules,
					'color' => $column->color,
	 				'pos' => $column->pos,
	 				'input_type' => $column->input_type,
					'input_mandatory' => $column->input_mandatory,
	 				'id_column' => $column->id_column,
	 				'sort_column' => $column->sort_column,
	 				'possible_values' => $column->possible_values,
	 				'hide_on_phones' => $column->hide_on_phones,
	 				'hide_on_tablets' => $column->hide_on_tablets,
	 				'default_value' => $column->default_value,
					'css_class' => $column->css_class,
					'text_before' => $column->text_before,
					'text_after' => $column->text_after,
					'formatting_rules' => $column->formatting_rules,
					'color' => $column->color,
					'sum_column' => $column->sum_column,
					'calc_formula' => $column->calc_formula,
					'skip_thousands_separator' => $column->skip_thousands_separator
	 			)
	 		);
	 	}
	 	
	 	exit();
	 	
	 }
	add_action( 'wp_ajax_wpdatatables_duplicate_table', 'wpdatatables_duplicate_table');

	/**
	 * Create a manually built table, and open in editor
	 */
	function wpdatatables_create_and_open_in_editor(){
            global $wpdb;
            $table_data = $_POST['table_data'];
            $table_data = apply_filters( 'wpdatatables_before_create_and_open_in_editor', $table_data );

            // Create a new Constructor object
            $constructor = new wpDataTableConstructor();

            // Generate and return a new 'Manual' type table
            $newTableId = $constructor->generateManualTable( $table_data );
            
            // Generate a link for new table
            echo admin_url( 'admin.php?page=wpdatatables-editor&table_id=' . $newTableId );
            
            exit();
	}
	add_action( 'wp_ajax_wpdatatables_create_and_open_in_editor', 'wpdatatables_create_and_open_in_editor');
	
	/**
	 * Prepare a list of all possible meta keys for provided post types 
	 * arranged in multidimensional array
	 */
	function wpdatatables_get_post_meta_keys_for_post_types(){
	    global $wpdb;
            
	    $query = "
	        SELECT $wpdb->postmeta.meta_key, $wpdb->posts.post_type
	        FROM $wpdb->posts
	        LEFT JOIN $wpdb->postmeta 
	        ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
                AND $wpdb->postmeta.meta_key != '' 
	        AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)' 
	        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
	        GROUP BY $wpdb->postmeta.meta_key;
	    ";

            $meta_res = $wpdb->get_results($query);
	    
	    $meta_keys = array();
	    
	    foreach($meta_res as $meta_row){
	    	if(!isset($meta_keys[$meta_row->post_type])){
	    		$meta_keys[$meta_row->post_type] = array();
	    	}
	    	$meta_keys[$meta_row->post_type][] = $meta_row->meta_key;
	    }

	    echo json_encode($meta_keys);
	}
	add_action( 'wp_ajax_wpdatatables_get_post_meta_keys_for_post_types', 'wpdatatables_get_post_meta_keys_for_post_types' );

	/**
	 * Prepare a list of all possible meta keys for provided post types 
	 * arranged in multidimensional array
	 */
	function wpdatatables_get_taxonomies_for_post_types(){
            global $wp_taxonomies;
            
            $return_taxonomies = array();
            
            foreach($wp_taxonomies as $tax_name => $tax_obj){
                foreach($tax_obj->object_type as $post_type){
                    if(!isset($return_taxonomies[$post_type])){
                        $return_taxonomies[$post_type] = array();
                    }
                    $return_taxonomies[$post_type][] = $tax_name;
                }
            }
            echo json_encode($return_taxonomies);
        }        
        
        /**
         * Action for generating a WP-based MySQL query
         */
        function wpdatatables_generate_wp_based_query(){

                global $wpdb;
		$table_data = $_POST['table_data'];
		$table_data = apply_filters( 'wpdatatables_before_generate_wp_based_query', $table_data );
		
		// Create a new Constructor object
		$constructor = new wpDataTableConstructor();
		
		// Generate and return a new 'Manual' type table
		$constructor->generateWPBasedQuery( $table_data );
                
                $result = array(
                    'query' => $constructor->getQuery(),
                    'preview' => $constructor->getQueryPreview()
                );
                
                echo json_encode( $result );
                exit();
        }
        
        add_action( 'wp_ajax_wpdatatables_generate_wp_based_query', 'wpdatatables_generate_wp_based_query' );
        
        /**
         * Action for refreshing the WP-based query
         */
        function wpdatatables_refresh_wp_query_preview(){
            $query = $_POST['query'];
            $constructor = new wpDataTableConstructor();
            $constructor->setQuery( $query );
            echo $constructor->getQueryPreview();
            exit();
        }
        
        add_action( 'wp_ajax_wpdatatables_refresh_wp_query_preview', 'wpdatatables_refresh_wp_query_preview' );
        
        /**
         * Action for generating the table from query/constructed table data
         */
        function wpdatatables_constructor_generate_wdt(){
            $table_data = $_POST['table_data'];
            
            // Create a new Constructor object
            $constructor = new wpDataTableConstructor();
            
            // Prepare a wpDataTable and get the ID
            $res = $constructor->generateWdtBasedOnQuery( $table_data );
            
            // Prepare the redirect link
            $link = get_admin_url()."?page=wpdatatables-administration&action=edit&table_id={$res['table_id']}";
            echo $link;
            exit();
            
        }
        add_action( 'wp_ajax_wpdatatables_constructor_generate_wdt', 'wpdatatables_constructor_generate_wdt' );
        
        /**
         * Request the column list for the selected tables
         */
        function wpdatatables_constructor_get_mysql_table_columns(){
            $tables = $_POST['tables'];
            
            $columns = wpDataTableConstructor::listMySQLColumns( $tables );
            
            echo json_encode( $columns );
            exit();
        }
        add_action( 'wp_ajax_wpdatatables_constructor_get_mysql_table_columns', 'wpdatatables_constructor_get_mysql_table_columns' );
        
        /**
         * Action for generating a WP-based MySQL query
         */
        function wpdatatables_generate_mysql_based_query(){

                global $wpdb;
		$table_data = $_POST['table_data'];
		$table_data = apply_filters( 'wpdatatables_before_generate_mysql_based_query', $table_data );
		
		// Create a new Constructor object
		$constructor = new wpDataTableConstructor();
		
		// Generate and return a new 'Manual' type table
		$constructor->generateMySQLBasedQuery( $table_data );
                
                $result = array(
                    'query' => $constructor->getQuery(),
                    'preview' => $constructor->getQueryPreview()
                );
                
                echo json_encode( $result );
                exit();
        }
        
        add_action( 'wp_ajax_wpdatatables_generate_mysql_based_query', 'wpdatatables_generate_mysql_based_query' );
                
         /**
          * Generate a file-based table preview (first 4 rows)
          */
         function wpdatatables_preview_file_table(){
             
             $table_data = $_POST['table_data'];
             $table_data = apply_filters( 'wpdatatables_before_preview_file_table', $table_data );
             
             // Create a new Constructor object
             $constructor = new wpDataTableConstructor();
             
             $result = $constructor->previewFileTable( $table_data );
             
             echo json_encode( $result );
             
             exit();
             
         }
         add_action( 'wp_ajax_wpdatatables_preview_file_table', 'wpdatatables_preview_file_table' );
         
         /**
          * Read data from file and generate the table
          */
         function wpdatatables_constructor_read_file_data(){
             
             $table_data = $_POST['table_data'];
             $table_data = apply_filters( 'wpdatatables_before_read_file_data', $table_data );
             
             // Create a new Constructor object
             $constructor = new wpDataTableConstructor();
             
             $constructor->readFileData( $table_data );
             
             $link = get_admin_url()."?page=wpdatatables-administration&action=edit&table_id=".$constructor->getTableId();
             echo $link;
             
             exit();
             
         }
         add_action( 'wp_ajax_wpdatatables_constructor_read_file_data', 'wpdatatables_constructor_read_file_data' );
        
        /**
         * Add a column to a manually  created table
         */ 
         function wpdatatables_add_new_manual_column(){
             $table_id = filter_var( $_POST['table_id'], FILTER_SANITIZE_NUMBER_INT );             
             $column_data = $_POST['column_data'];
             wpDataTableConstructor::addNewManualColumn( $table_id, $column_data );
             exit();
         }
         add_action( 'wp_ajax_wpdatatables_add_new_manual_column', 'wpdatatables_add_new_manual_column' );
         
         /**
          * Delete a column from a manually created table
          */
         function wpdatatables_delete_manual_column(){
             $table_id = filter_var( $_POST['table_id'], FILTER_SANITIZE_NUMBER_INT );             
             $column_name = filter_var( $_POST['column_name'], FILTER_SANITIZE_STRING );     
             wpDataTableConstructor::deleteManualColumn( $table_id, $column_name );
             exit();
         }
         add_action( 'wp_ajax_wpdatatables_delete_manual_column', 'wpdatatables_delete_manual_column' );
         
         /**
          * Return all columns for a provided table
          */
         function wpdatatables_get_columns_data_by_table_id(){
             $table_id = filter_var( $_POST['table_id'], FILTER_SANITIZE_NUMBER_INT );             
             echo json_encode( wdt_get_columns_by_table_id( $table_id ) );
             exit();
         }
         add_action( 'wp_ajax_wpdatatables_get_columns_data_by_table_id', 'wpdatatables_get_columns_data_by_table_id' );
         
         /**
          * Returns the complete table for the range picker
          */
         function wpdatatables_get_complete_table_json_by_id(){
            $table_id = filter_var( $_POST['table_id'], FILTER_SANITIZE_NUMBER_INT );
			$tbl = wdt_get_wpdatatable( $table_id );
            echo json_encode( $tbl->getDataRows() );
            exit();
         }
         add_action( 'wp_ajax_wpdatatables_get_complete_table_json_by_id', 'wpdatatables_get_complete_table_json_by_id' );
         
         /**
          * Get the chart axes and series names
          */
         function wpdatatable_get_chart_axes_and_series(){
             $chart_data = $_POST['chart_data'];
             $wpDataChart = WPDataChart::factory( $chart_data );
             echo json_encode( $wpDataChart->getAxesAndSeries(), JSON_NUMERIC_CHECK );
             exit();
         }
         add_action( 'wp_ajax_wpdatatable_get_chart_axes_and_series', 'wpdatatable_get_chart_axes_and_series' );
         
         function wpdatatable_show_chart_from_data(){
             $chart_data = $_POST['chart_data'];
             $wpDataChart = WPDataChart::factory( $chart_data, false );
             echo json_encode( $wpDataChart->returnData(), JSON_NUMERIC_CHECK );
             exit();
         }
         add_action( 'wp_ajax_wpdatatable_show_chart_from_data', 'wpdatatable_show_chart_from_data' );
         
         
         function wpdatatable_save_chart_get_shortcode(){
             $chart_data = $_POST['chart_data'];
             $wpDataChart = WPDataChart::factory( $chart_data, false );
             $wpDataChart->save();
             echo json_encode( array( 'id' => $wpDataChart->getId(), 'shortcode' => $wpDataChart->getShortCode() ) );
             exit();
         }
         add_action( 'wp_ajax_wpdatatable_save_chart_get_shortcode', 'wpdatatable_save_chart_get_shortcode' );
                 
         /**
          * List all tables in JSON
          */
         function wpdatatable_list_all_tables(){
             echo json_encode( wdt_get_all_tables_nonpaged() );
             exit();
         }
         add_action( 'wp_ajax_wpdatatable_list_all_tables', 'wpdatatable_list_all_tables' );
         
         /**
          * List all charts in JSON
          */
         function wpdatatable_list_all_charts(){
             echo json_encode( wdt_get_all_charts_nonpaged() );
             exit();
         }
         add_action( 'wp_ajax_wpdatatable_list_all_charts', 'wpdatatable_list_all_charts' );

		/**
		 * Get all distinct values for the column
		 */
		function wpdatatable_get_column_distinct_values(){
			$dist_vals = array();
			$table_id = intval( $_POST['table_id'] );
			$column_id = intval( $_POST['column_id'] );

			$table_data = wdt_get_table_by_id( $table_id );

			if( $table_data['table_type'] !== 'mysql' && $table_data['table_type'] !== 'manual' ) {
				_e('Wrong table type!','wpdatatables');
				die();
			}

			$column_data = wdt_get_column_data( $column_id );

			$mysql_table_query = $table_data['content'];
			$original_column_name = $column_data['orig_header'];

			$dist_vals_query = "SELECT DISTINCT(`$original_column_name`) AS `$original_column_name` FROM ( $mysql_table_query ) tbl";

			if(!get_option('wdtUseSeparateCon')){
				global $wpdb;

				$dist_vals = $wpdb->get_col( $dist_vals_query );
			} else {
				$sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
				$rows = $sql->getArray( $dist_vals_query );

				foreach ($rows as $row) {
					$dist_vals[] = $row[0];
				}
			}

			echo json_encode( $dist_vals );
			exit();
		}
		add_action( 'wp_ajax_wpdatatable_get_column_distinct_values', 'wpdatatable_get_column_distinct_values' );

		/**
		 * Get the preview for formula column
		 */
		function wpdatatables_preview_formula_result(){
			$table_id = intval( $_POST['table_id'] );
			$formula = filter_var( $_POST['formula'], FILTER_SANITIZE_STRING );
			// Get table raw data
			$tbl = wdt_get_wpdatatable( $table_id );
			// Calculate values for formula for first 5 rows
			echo $tbl->calcFormulaPreview( $formula );
			exit();
		}
		add_action( 'wp_ajax_wpdatatables_preview_formula_result', 'wpdatatables_preview_formula_result' );

?>