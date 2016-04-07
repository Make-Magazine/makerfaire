<?php
/**
 * @package wpDataTables
 * @version 1.6.0
 */
/**
 * The admin page
 */
?>
<?php

        // Labels for create/edit JS
        global $wdt_admin_translation_array;
        $wdt_admin_translation_array = array(
        		'table_type_not_empty' => __('Table type cannot be empty','wpdatatables'),
        		'error_label' => __('Error!','wpdatatables'),
        		'table_input_source_not_empty' => __('Table input data source cannot be empty','wpdatatables'),
				'mysql_query_cannot_be_empty' =>  __('MySQL query cannot be empty','wpdatatables'),
        		'mysql_table_name_not_set' => __('MySQL table name for front-end editing is not set','wpdatatables'),
				'userid_column_not_set' => __( 'User ID column is not set. Please choose a column which will be used to identify rows belonging to the user.', 'wpdatatables' ),
        		'empty_result_error' => __('This usually happens when the MySQL query returns an empty result or is broken. Please check the results of the query in some DB manager (e.g. PHPMyAdmin)','wpdatatables'),
        		'empty_manual_table_error' => __('Please add at least 1 row to the table through the editor before changing the settings. wpDataTables uses the actual table data to initialize table settings, so configuration for tables with no rows cannot be modified. Click "Go to editor" to start entering data.','wpdatatables'),
				'backend_error_report' => __('wpDataTables backend error: ','wpdatatables'),
        		'successful_save' => __('Table saved successfully!','wpdatatables'),
        		'success_label' => __('Success!','wpdatatables'),
        		'file_too_large' => __('This error is usually occuring because you are trying to load file which is too large. Please try another datasource, use a smaller file.','wpdatatables'),
        		'id_column_not_set' => __('ID column for front-end editing feature is not set','wpdatatables'),
        		'are_you_sure_label' => __('Are you sure?','wpdatatables'),
        		'are_you_sure_lose_unsaved_label' => __('Are you sure? You will lose unsaved changes!','wpdatatables'),
        		'choose_file' => __('Use in wpDataTable','wpdatatables'),
        		'select_excel_csv' => __('Select Excel or CSV file','wpdatatables'),
        		'mysql_server_side_query_too_complicated' => __('Complicated queries (with WHERE clause, conditions, or with JOINs) are not supported together with server-side processing. Please store the query in a MySQL view and then create a wpDataTable based on the view.','wpdatatables'),
                'choose_id_column' => __('Please choose an ID column for editing...','wpdatatables'),
                'choose_user_id_column' => __('Please choose a user ID column...','wpdatatables'),
                'cancel' => __('Cancel','wpdatatables'),
                'ok' => __('OK','wpdatatables'),
                'no_formatting_rules' => __('No formatting rules for this column yet.', 'wpdatatables'),
                'merge' => __('Merge', 'wpdatatables'),
                'replace' => __('Replace', 'wpdatatables')
	        );

		// add the page to WP Admin
		add_action( 'admin_menu', 'wpdatatables_admin_menu' );
        
        add_action( 'admin_print_styles', 'wdt_print_min_css' );
        
        function wdt_print_min_css(){
            wp_register_style('wpdatatables-min',WDT_CSS_PATH.'wpdatatables.min.css');
            wp_enqueue_style('wpdatatables-min');
            wp_register_style('wpdatatables-admin',WDT_ASSETS_PATH.'css/wpdatatables_admin.css');
            wp_enqueue_style('wpdatatables-admin');
        }
        
	
	/**
	 * Generates the admin menu in admin panel sidebar
	 */
	function wpdatatables_admin_menu() {
            $wdt_pages = array(
                            add_menu_page( 'wpDataTables', 'wpDataTables', 'manage_options', 'wpdatatables-administration', 'wpdatatables_browse', 'none'),
                            add_submenu_page( 'wpdatatables-administration', 'Add a new wpDataTable', 'Add from data source', 'manage_options', 'wpdatatables-addnew', 'wpdatatables_addnew'),
                            add_submenu_page( 'wpdatatables-administration', 'wpDataTable Constructor', 'wpDataTable Constructor', 'manage_options', 'wpdatatables-constructor', 'wpdatatables_constructor'),
                            add_submenu_page( 'wpdatatables-administration', 'wpDataTables Charts', 'wpDataTables Charts', 'manage_options', 'wpdatatables-charts', 'wpdatatables_charts'),
                            add_submenu_page( 'wpdatatables-administration', 'Create Chart Wizard', 'Create Chart Wizard', 'manage_options', 'wpdatatables-chart-wizard', 'wpdatatables_chart_wizard'),
                            add_submenu_page( 'wpdatatables-administration', 'wpDataTables settings', 'Settings', 'manage_options', 'wpdatatables-settings', 'wpdatatables_settings'),
                            add_submenu_page( null, 'wpDataTables editor', 'Editor', 'manage_options', 'wpdatatables-editor', 'wpdatatables_editor')
                        );
            foreach( $wdt_pages as $wdt_page ){
                add_action( 'admin_print_styles-' . $wdt_page, 'wdt_admin_styles' );
                add_action( 'admin_print_scripts-' . $wdt_page, 'wdt_admin_scripts' );
            }
	}
	
	/**
	 * Adds JS to the admin panel
	 */
	function wdt_admin_scripts() {
		wp_enqueue_media();
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('postbox');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-datepicker');
		do_action('wpdatatables_admin_scripts');
	}	
	
	/**
	 * Adds CSS styles in the admin
	 */
	function wdt_admin_styles( $page ) {
            wp_register_style('wpdatatables-min',WDT_CSS_PATH.'wpdatatables.min.css');
            wp_enqueue_style('wpdatatables-min');
			wp_register_style('jquery-style',WDT_CSS_PATH.'jquery-ui.min.css');
			wp_enqueue_style('jquery-style');
            wp_enqueue_style('thickbox');
            wp_register_style('pickadate-default',WDT_ASSETS_PATH.'css/datepicker.default.css');
            wp_enqueue_style('pickadate-default');
            wp_register_style('pickadate-default-date',WDT_ASSETS_PATH.'css/datepicker.default.date.css');
            wp_enqueue_style('pickadate-default-date');
            wp_register_style('jquery-fileupload',WDT_CSS_PATH.'jquery.fileupload.css');
            wp_enqueue_style('jquery-fileupload');
            wp_register_style('tabletools',WDT_CSS_PATH.'TableTools.css');
            wp_enqueue_style('tabletools');
            wp_register_style('datatables-responsive',WDT_ASSETS_PATH.'css/datatables.responsive.css');
            wp_enqueue_style('datatables-responsive');
            // Get skin setting
            $skin = get_option('wdtBaseSkin');
            if(empty($skin)){ $skin = 'skin1'; }
            $renderSkin = $skin == 'skin1' ? WDT_ASSETS_PATH.'css/wpDataTablesSkin.css' : WDT_ASSETS_PATH.'css/wpDataTablesSkin_1.css';
            wp_register_style('wpdatatables-skin',$renderSkin);
            wp_enqueue_style('wpdatatables-skin');
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wp-color-picker');
            do_action('wpdatatables_admin_styles');
	}	

	/**
	 * Get all tables for the browser
	 */
	 function wdt_get_all_tables(){
            global $wpdb;
            $query = "SELECT id, title, table_type
							FROM {$wpdb->prefix}wpdatatables ";

            if(isset($_REQUEST['s'])){
                    $query .= " WHERE title LIKE '%".sanitize_text_field($_POST['s'])."%' ";
            }

            if(isset($_REQUEST['orderby'])){
                    if(in_array($_REQUEST['orderby'], array('id','title','table_type'))){
                            $query .= " ORDER BY ".$_GET['orderby'];
                            if($_REQUEST['order'] == 'desc'){
                                    $query .= " DESC";
                            }else{
                                    $query .= " ASC";
                            }
                    }
            }else{
                    $query .= " ORDER BY id ASC ";
            }

            if(isset($_REQUEST['paged'])){
                    $paged = (int) $_REQUEST['paged'];
            }else{
                    $paged = 1;
            }

            $tables_per_page = get_option('wdtTablesPerPage') ? get_option('wdtTablesPerPage') : 10;

            $query .= " LIMIT ".($paged-1)*$tables_per_page.", ".$tables_per_page;

            $all_tables = $wpdb->get_results( $query, ARRAY_A );

            $all_tables = apply_filters('wpdatatables_filter_browse_tables', $all_tables);

            return $all_tables;
	 }
         
	/**
	 * Get all tables non-paged for the chart wizard
	 */
	 function wdt_get_all_tables_nonpaged(){
            global $wpdb;
            $query = "SELECT id, title, table_type, server_side
                                    FROM {$wpdb->prefix}wpdatatables ORDER BY id";

            $all_tables = $wpdb->get_results( $query, ARRAY_A );

            return $all_tables;
	 }         
         
	/**
	 * Get all charts non-paged for the MCE editor
	 */
	 function wdt_get_all_charts_nonpaged(){
            global $wpdb;
            $query = "SELECT id, title
                                    FROM {$wpdb->prefix}wpdatacharts ";

            $all_charts = $wpdb->get_results( $query, ARRAY_A );

            return $all_charts;
	 }   
         
	 /**
	  * Get table count for the browser
	  */
	  function wdt_get_table_count(){
            global $wpdb;

            $query = "SELECT COUNT(*) FROM {$wpdb->prefix}wpdatatables";

            $count = $wpdb->get_var( $query );

            return $count;
	  }
	 
	/**
	 * Helper method which creates the
	 * columnset in the DB from a WPDataTable object
	 */
	function wdt_create_columns_from_table( $table, $table_id ){
		global $wpdb;
		
		do_action('wpdatatables_before_create_columns', $table, $table_id);
		
		// Get existing columns array (except formulas)
		$existing_columns_query = $wpdb->prepare("SELECT orig_header 
															FROM ".$wpdb->prefix ."wpdatatables_columns
															WHERE table_id = %d
															AND column_type != 'formula'",
															$table_id
												);
		$columns_to_delete = $wpdb->get_col( $existing_columns_query );
		if( !empty( $_POST['columns_to_delete'] ) ){
			$columns_to_delete = array_merge( $columns_to_delete, $_POST['columns_to_delete'] );
		}

		$columns = $table->getColumns();
                
		foreach($columns as $key=>&$column){
			$column->table_id = $table_id;
			$column->orig_header = $column->getTitle();
			$column->possible_values = '';
			$column->default_value = '';
			$column->input_type = 'text';
			$column->input_mandatory = 0;
			$column->display_header = $column->getTitle();
			$column->filter_type = $column->getFilterType()->type;
			$column->column_type = $column->getDataType();
			$column->use_in_chart = false;
			$column->chart_horiz_axis = false;
			$column->group_column = false;
			$column->pos = $key;
			$column->width = '';
			$column->visible = 1;
			
			$column = apply_filters( 'wpdatatables_filter_column_before_save', $column, $table_id );
			
			if(($delete_key = array_search($column->orig_header, $columns_to_delete)) !== false) {
			    unset($columns_to_delete[$delete_key]);
			}

			// Check if column with this header exists in the DB
			$column_query = $wpdb->prepare("SELECT id 
                                                                FROM ".$wpdb->prefix ."wpdatatables_columns 
                                                                WHERE table_id = %d
                                                                AND orig_header = %s",
                                                                $table_id,
                                                                $column->orig_header); 
                        
			$column_id = $wpdb->get_var( $column_query );
			
			if(!empty($column_id)){
				// If column exists we update it
				$update_array = array(
					'display_header' => $column->display_header,
					'possible_values' => $column->possible_values,
					'default_value' => $column->default_value,
					'input_type' => $column->input_type,
					'filter_type' => $column->filter_type,
					'column_type' => $column->column_type,
					'group_column' => 0,
					'sort_column' => 0,
					'use_in_chart' => (int)$column->use_in_chart,
					'chart_horiz_axis' => (int)$column->chart_horiz_axis,
					'pos' => $column->pos,
					'width' => $column->width,
					'visible' => $column->visible
					);
			
				$update_array = apply_filters( 'wpdatatables_filter_update_column_array', $update_array, $table_id, $column );
				
				$column->id = $column_id;
				
				$wpdb->update(
										$wpdb->prefix .'wpdatatables_columns',
										$update_array,
										array(
												'id' => $column_id
										),
										array(
												'%d'
										)
								);
			
			}else{
				// If column doesn't exist we insert it
				$insert_array = array(
					'table_id' => $table_id,
					'orig_header' => $column->orig_header,
					'display_header' => $column->display_header,
					'possible_values' => $column->possible_values,
					'default_value' => $column->default_value,
					'input_type' => $column->input_type,
					'filter_type' => $column->filter_type,
					'group_column' => 0,
					'sort_column' => 0,
					'use_in_chart' => (int)$column->use_in_chart,
					'chart_horiz_axis' => (int)$column->chart_horiz_axis,
					'pos' => $column->pos,
					'width' => $column->width,
					'visible' => $column->visible
					);
			
				$insert_array = apply_filters( 'wpdatatables_filter_insert_column_array', $insert_array, $table_id, $column );
				
				$wpdb->insert($wpdb->prefix .'wpdatatables_columns', $insert_array);
				$column->id = $wpdb->insert_id;
			}
			
			do_action( 'wpdatatables_after_insert_column', $column, $table_id );
			
		}
		
		// Delete from DB all columns that do not exist any more
		foreach($columns_to_delete as $delete_header){
			$wpdb->delete( 
							$wpdb->prefix."wpdatatables_columns",
							array(
									'orig_header' => $delete_header,
									'table_id' => $table_id
							),
							array(
									'%s',
									'%d'
							)
					);
		}
		
		return $columns;
	}
	
	/**
	 * Tries to generate a WPDataTable object by user's setiings
	 */
	function wdt_try_generate_table( $type, $content ) {
		$tbl = new WPDataTable();
		$result = array();
		
		do_action( 'wpdatatables_try_generate_table', $type, $content );
		
		$table_params = array( 'limit' => '10' );
		switch($type){
                    case 'mysql' :
                    case 'manual' :
                            try {
                                    $tbl->queryBasedConstruct( $content, array(), $table_params, true );
                                    $result['table'] = $tbl;
                            }catch( Exception $e ) {
                                    $result['error'] = $e->getMessage();
                                    return $result;
                            } 
                            break;
                    case 'csv' :
                    case 'xls' :
                            try {
                                    $tbl->excelBasedConstruct( $content, $table_params );
                                    $result['table'] = $tbl;
                            } catch( Exception $e ) {
                                    $result['error'] = $e->getMessage();
                                    return $result;
                            } 
                            break;
                    case 'xml' :
                            try {
                                    $tbl->XMLBasedConstruct( $content, $table_params );
                                    $result['table'] = $tbl;
                            } catch( Exception $e ) {
                                    $result['error'] = $e->getMessage();
                                    return $result;
                            } 
                            break;
                    case 'json' :
                            try {
                                    $tbl->jsonBasedConstruct( $content, $table_params );
                                    $result['table'] = $tbl;
                            } catch( Exception $e ) {
                                    $result['error'] = $e->getMessage();
                                    return $result;
                            } 
                            break;
                    case 'serialized' :
                            try {
                                    $array = unserialize( WDTTools::curlGetData( $content ) );
                                    $tbl->arrayBasedConstruct( $array, $table_params );
                                    $result['table'] = $tbl;
                            } catch( Exception $e ) {
                                    $result['error'] = $e->getMessage();
                                    return $result;
                            } 
                            break;
                    case 'google_spreadsheet':
                            try {
                                $array = WDTTools::extractGoogleSpreadsheetArray( $content );
								if( empty( $array ) ){
									$result['error'] = __(
															'Could not read Google spreadsheet, please check if the URL is correct and the spreadsheet is published to everyone' ,
															'wpdatatables'
														 );
								}
                                $tbl->arrayBasedConstruct( $array, $table_params );
                                $result['table'] = $tbl;
                            } catch( Exception $e ) {
                                $result['error'] = $e->getMessage();
                                return $result;
                            } 
                            break;
		}
		
		$result = apply_filters( 'wpdatatables_try_generate_table_result', $result );
		
		return $result;		
	}
        
        function wpdatatables_enqueue_editing_scripts(){
            global $wdt_admin_translation_array;
            // Admin JS
            wp_enqueue_script('wpdatatables-admin',WDT_JS_PATH.'wpdatatables/wpdatatables_admin.js');
            // Google Charts
            wp_enqueue_script('wdt_google_charts','https://www.google.com/jsapi');  
            // Selecter
            wp_enqueue_script('wpdatatables-selecter',WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
            wp_enqueue_style('wpdatatables-selecter',WDT_CSS_PATH.'jquery.fs.selecter.css');
            // iCheck
            wp_enqueue_script('wpdatatables-icheck',WDT_JS_PATH.'icheck/icheck.min.js');
            wp_enqueue_style('wpdatatables-icheck',WDT_CSS_PATH.'icheck.minimal.css');
            // Popup
            wp_enqueue_script('wpdatatables-popup',WDT_JS_PATH.'popup/jquery.remodal.min.js');
            wp_enqueue_style('wpdatatables-popup',WDT_CSS_PATH.'jquery.remodal.css');
            // JsRender
            wp_enqueue_script('wpdatatables-jsrender',WDT_JS_PATH.'jsrender/jsrender.min.js');
			// Responsive
            wp_enqueue_script('wpdatatables-responsive',WDT_JS_PATH.'responsive/datatables.responsive.min.js');
            // Media upload
			// Loader
			wp_enqueue_script('jquery-loading',WDT_JS_PATH.'jquery-loader/loading.js');
			wp_enqueue_style('jquery-loading',WDT_CSS_PATH.'loading.css');
			// Datepicker
			wp_enqueue_script('pickadate-main',WDT_JS_PATH.'datepicker/picker.js');
			wp_enqueue_script('pickadate-date',WDT_JS_PATH.'datepicker/picker.date.js');
            // Table create/edit JS
            wp_enqueue_script('wpdatatables-edit',WDT_JS_PATH.'wpdatatables/wpdatatables_edit_table.js');
            // Media upload
            wp_enqueue_script('media-upload');
            // ACE syntax highlight
            wp_enqueue_script( 'wpdatatables-ace', WDT_JS_PATH.'ace/ace.js' );
			//tags input
			wp_enqueue_script('wpdatatables-tagsinput',WDT_JS_PATH.'tagsinput/jquery.tagsinput.min.js');
			wp_enqueue_style('wpdatatables-tagsinput',WDT_CSS_PATH.'jquery.tagsinput.min.css');
            // Localization
            wp_localize_script('wpdatatables-edit','wpdatatables_edit_strings',$wdt_admin_translation_array);
            wp_localize_script( 'wpdatatables-edit', 'wpdatatables_frontend_strings', WDTTools::getTranslationStrings() );
			// New Table Tools
			//wp_enqueue_style('wpdt-buttons-css', WDT_JS_PATH.'export-tools/buttons.dataTables.css');


        }

	/**
	 * Helper function to delte manually created table if needed
	 */
	function wpdatatables_delete_manual_table( $id ){
		global $wpdb;

		// First check if the table is manual, and if it is delete the table from DB
		$tbl_data = wdt_get_table_by_id( $id );
		if( !empty($tbl_data['table_type']) ){
			if( $tbl_data['table_type'] == 'manual' ){
				$wpdb->query("DROP TABLE {$tbl_data['mysql_table_name']}");
			}
		}

	}
	
	/**
	 * Renders the browser of existing tables
	 */
	function wpdatatables_browse() {
		global $wpdb, $wdt_admin_translation_array;
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); 
		}
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		if($action == 'edit'){
			$id = $_GET['table_id'];
			$tpl = new PDTTpl();
			$tpl->setTemplate('edit_table.inc.php');
			wpdatatables_enqueue_editing_scripts();
			$tpl->addData('wpShowTitle', __('Edit wpDataTable','wpdatatables'));
			$tpl->addData('table_id', $id);
			$tpl->addData('wdtDateFormat', get_option('wdtDateFormat'));

			$table_data =  wdt_get_table_by_id($id);

			if (!empty($table_data['table_type']) && $table_data['table_type'] != 'mysql') {
				$uploads_dir = wp_upload_dir();
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
					$table_content = str_replace( str_replace('/', '\\', $uploads_dir['basedir']), $uploads_dir['baseurl'], $table_data['content']);
				} else {
					$table_content = str_replace( $uploads_dir['basedir'], $uploads_dir['baseurl'], $table_data['content']);
				}
			}else{
				$table_content = '';
			}

			$tpl->addData('table_data', $table_data);
			$tpl->addData('table_content', $table_content);
			$tpl->addData('column_data', wdt_get_columns_by_table_id($id));
			$tpl->addData('wdtUserRoles', get_editable_roles());


			$edit_page = $tpl->returnData();
			
			$edit_page = apply_filters( 'wpdatatables_filter_edit_page', $edit_page );
			
			echo $edit_page;
			
		}else{
			if($action == 'delete') {
				$id = $_REQUEST['table_id'];

				if(!is_array($id)){
					wpdatatables_delete_manual_table( $id );
					$wpdb->query("DELETE 
									FROM {$wpdb->prefix}wpdatatables
									WHERE id={$id}");
					$wpdb->query("DELETE 
									FROM {$wpdb->prefix}wpdatatables_columns
									WHERE table_id={$id}");
					$wpdb->query("DELETE 
									FROM {$wpdb->prefix}wpdatacharts
									WHERE wpdatatable_id={$id}");
				}else{
					foreach($id as $single_id){
						wpdatatables_delete_manual_table( $single_id );
						$wpdb->query("DELETE
										FROM {$wpdb->prefix}wpdatatables
										WHERE id={$single_id}");
						$wpdb->query("DELETE
										FROM {$wpdb->prefix}wpdatatables_columns
										WHERE table_id={$single_id}");
						$wpdb->query("DELETE
										FROM {$wpdb->prefix}wpdatacharts
										WHERE wpdatatable_id={$single_id}");
					}
				}
			}
			
			$wdtBrowseTable = new WDTBrowseTable();
			
			$wdtBrowseTable->prepare_items();
			
			ob_start();
			$wdtBrowseTable->search_box('search','search_id');
			$wdtBrowseTable->display();
			$tableHTML = ob_get_contents();
			ob_end_clean();
			
			$tpl = new PDTTpl();
			// Popup
			$tpl->addJs(WDT_JS_PATH.'popup/jquery.remodal.min.js');
			$tpl->addCss(WDT_CSS_PATH.'jquery.remodal.css');
			// Admin JS
			$tpl->addJs(WDT_JS_PATH.'wpdatatables/wpdatatables_admin.js');
			$tpl->setTemplate( 'browse.inc.php' );
			$tpl->addData( 'tableHTML', $tableHTML );
			$browse_page = $tpl->returnData();
			$browse_page = apply_filters( 'wpdatatables_filter_browse_page', $browse_page );
			
			echo $browse_page;
		}
		
		do_action( 'wpdatatables_browse_page' );
	}
	
	/**
	 * Add new page
	 */
	function wpdatatables_addnew() {
		global $wdt_admin_translation_array;
		
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$tpl = new PDTTpl();
		wpdatatables_enqueue_editing_scripts();
		$tpl->setTemplate( 'edit_table.inc.php' );
		$tpl->addData('wdtUserRoles', get_editable_roles());
		$tpl->addData('wdtDateFormat', get_option('wdtDateFormat'));
		$tpl->addData( 'table_content','' );
		$tpl->addData('wpShowTitle', 'Add a new wpDataTable');
		$tpl->addData('table_data', array( 'tabletools_config' => array( 'print' => 1, 'copy' => 1, 'excel' => 1, 'csv' => 1, 'pdf' => 1 ) ));
		$newtable_page = $tpl->returnData();
		
		$newtable_page = apply_filters( 'wpdatatables_filter_new_table_page', $newtable_page );
		
		echo $newtable_page;
		
		do_action( 'wpdatatables_addnew_page' );
		
	}
        
	
	/**
	 * Charts browse
	 */
        function wpdatatables_charts() {
		global $wdt_admin_translation_array;
		
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$tpl = new PDTTpl();
                
                /**
                 * Handle deleting 
                 */
		if( isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] == 'delete' ) ){
                    $delete_id = $_REQUEST['chart_id'];
                    if( is_array( $delete_id ) ){
                        foreach( $delete_id as $single_delete_id ){
                            $single_delete_id = filter_var( $single_delete_id, FILTER_SANITIZE_NUMBER_INT );
                            wpDataChart::deleteChart( $single_delete_id );
                        }
                    }else{
                        $delete_id = filter_var( $delete_id, FILTER_SANITIZE_NUMBER_INT );
                        wpDataChart::deleteChart( $delete_id );
                    }
                }
                
                
		// Admin JS
                wp_enqueue_script('wpdatatables-admin',WDT_JS_PATH.'wpdatatables/wpdatatables_admin.js');
		// Selecter
                wp_enqueue_script('wpdatatables-selecter',WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
		wp_enqueue_style('wpdatatables-selecter',WDT_CSS_PATH.'jquery.fs.selecter.css');
		// iCheck
                wp_enqueue_script('wpdatatables-icheck',WDT_JS_PATH.'icheck/icheck.min.js');
                wp_enqueue_style('wpdatatables-icheck',WDT_CSS_PATH.'icheck.minimal.css');
                // Popup
                wp_enqueue_script('wpdatatables-popup',WDT_JS_PATH.'popup/jquery.remodal.min.js');
		wp_enqueue_style('wpdatatables-popup',WDT_CSS_PATH.'jquery.remodal.css');

                wp_localize_script('wpdatatables-edit','wpdatatables_edit_strings',$wdt_admin_translation_array);
		
                $wdtBrowseChartsTable = new WDTBrowseChartsTable();

                $wdtBrowseChartsTable->prepare_items();

                ob_start();
                $wdtBrowseChartsTable->search_box('search','search_id');
                $wdtBrowseChartsTable->display();
                $tableHTML = ob_get_contents();
                ob_end_clean();

                
		$tpl->addData('wdtUserRoles', get_editable_roles());
		$tpl->setTemplate( 'browse_charts.inc.php' );
		$tpl->addData('wpShowTitle', 'Browse wpDataTables Charts');
                $tpl->addData( 'tableHTML', $tableHTML );
		$browse_charts_page = $tpl->returnData();
		
		$browse_charts_page = apply_filters( 'wpdatatables_filter_charts_table_page', $browse_charts_page );
		
		echo $browse_charts_page;
		
		do_action( 'wpdatatables_browse_charts_page' );
		
	}
        
        /**
         * Create/edit chart wizard
         */
        function wpdatatables_chart_wizard(){
		global $wdt_admin_translation_array;
		
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$tpl = new PDTTpl();
		
                // Google Chart JS
                wp_enqueue_script('wdt_google_charts','//www.google.com/jsapi');
                // Highcharts JS
                wp_enqueue_script('wdt_highcharts','//code.highcharts.com/highcharts.js');
                wp_enqueue_script('wdt_highcharts3d','//code.highcharts.com/highcharts-3d.js');
		// Admin JS
                wp_enqueue_script('wpdatatables-admin',WDT_JS_PATH.'wpdatatables/wpdatatables_admin.js');
		// Selecter
                wp_enqueue_script('wpdatatables-selecter',WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
		wp_enqueue_style('wpdatatables-selecter',WDT_CSS_PATH.'jquery.fs.selecter.css');
		// iCheck
                wp_enqueue_script('wpdatatables-icheck',WDT_JS_PATH.'icheck/icheck.min.js');
                wp_enqueue_style('wpdatatables-icheck',WDT_CSS_PATH.'icheck.minimal.css');
                // Popup
                wp_enqueue_script('wpdatatables-popup',WDT_JS_PATH.'popup/jquery.remodal.min.js');
				wp_enqueue_style('wpdatatables-popup',WDT_CSS_PATH.'jquery.remodal.css');
                // JsRender
                wp_enqueue_script('wpdatatables-jsrender',WDT_JS_PATH.'jsrender/jsrender.min.js');
                // ImagePicker
                wp_enqueue_script( 'wpdatatables-image-picker', WDT_JS_PATH.'image-picker/image-picker.min.js' );
				wp_enqueue_style( 'wpdatatables-image-picker', WDT_CSS_PATH.'image-picker.css' );
                // Chart wizard JS
                wp_enqueue_script('wpdatatables-chart-wizard',WDT_JS_PATH.'wpdatatables/wpdatatables_chart_wizard.js');
                // Google Chart wpDataTable JS library
                wp_enqueue_script('wpdatatables-google-chart',WDT_JS_PATH.'wpdatatables/wpdatatables_google_charts.js');
                // Highchart wpDataTable JS library
                wp_enqueue_script('wpdatatables-highcharts',WDT_JS_PATH.'wpdatatables/wpdatatables_highcharts.js');
                
                wp_localize_script('wpdatatables-edit','wpdatatables_edit_strings',$wdt_admin_translation_array);
		
                // Edit or add new
                $chart_id = isset( $_GET['chart_id'] ) ? filter_var( $_GET['chart_id'], FILTER_SANITIZE_NUMBER_INT ) : false;
                if( !empty( $chart_id ) ){
                    $chartObj = new WPDataChart();
                    $chartObj->setId( $chart_id );
                    $chartObj->loadFromDB();
                    $chartObj->prepareData();
                    $chartObj->shiftStringColumnUp();
                }
		
		$tpl->setTemplate( 'chart_wizard.inc.php' );
                $tpl->addData( 'chart_id', $chart_id );
                if( !empty( $chart_id ) ){
                    $tpl->addData( 'wpShowTitle', __( 'wpDataTables Chart Edit Wizard', 'wpdatatables' ) );
                    $tpl->addData( 'chartObj', $chartObj );
                }else{
                    $tpl->addData( 'wpShowTitle', __( 'wpDataTables Chart Create Wizard', 'wpdatatables' ) );
                }
                $tpl->addData( 'chartDataJson', json_encode( array() ) );
		$chart_wizard_page = $tpl->returnData();
		
		$chart_wizard_page = apply_filters( 'wpdatatables_filter_chart_wizard_page', $chart_wizard_page );
		
		echo $chart_wizard_page;
		
		do_action( 'wpdatatables_chart_wizard_page' );            
        }
	
	/**
	 * Settings page
	 */
        function wpdatatables_settings() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		wp_enqueue_script('wdt-colorpicker');
		
		$languages = array();
		
		foreach(glob( WDT_ROOT_PATH .'source/lang/*.inc.php') as $lang_filename) {
			$lang_filename = str_replace(WDT_ROOT_PATH .'source/lang/', '', $lang_filename);
			$name = ucwords(str_replace('_', ' ', $lang_filename));
			$name = str_replace('.inc.php', '', $name);
			$languages[] = array('file' => $lang_filename, 'name' => $name);
		}
		
		$tpl = new PDTTpl();
		$tpl->setTemplate( 'settings.inc.php' );
		$tpl->addData('wpUseSeparateCon', get_option('wdtUseSeparateCon'));
		$tpl->addData('wpMySqlHost', get_option('wdtMySqlHost'));
		$tpl->addData('wpMySqlDB', get_option('wdtMySqlDB'));
		$tpl->addData('wpMySqlUser', get_option('wdtMySqlUser'));
		$tpl->addData('wpMySqlPwd', get_option('wdtMySqlPwd'));
		$tpl->addData('wpMySqlPort', get_option('wdtMySqlPort'));
		$tpl->addData('wpRenderCharts', get_option('wdtRenderCharts'));
		$tpl->addData('wpRenderFilter', get_option('wdtRenderFilter'));
		$tpl->addData('wdtTablesPerPage', get_option('wdtTablesPerPage'));
		$tpl->addData('wdtNumberFormat', get_option('wdtNumberFormat'));
		$tpl->addData('wdtDecimalPlaces', get_option('wdtDecimalPlaces'));
		$tpl->addData('wdtNumbersAlign', get_option('wdtNumbersAlign'));
		$tpl->addData('wdtTabletWidth', get_option('wdtTabletWidth'));
		$tpl->addData('wdtMobileWidth', get_option('wdtMobileWidth'));
		$tpl->addData('wdtPurchaseCode', get_option('wdtPurchaseCode'));
		$tpl->addData('wdtCustomJs', get_option('wdtCustomJs'));
		$tpl->addData('wdtCustomCss', get_option('wdtCustomCss'));
		$tpl->addData('wdtMinifiedJs', get_option('wdtMinifiedJs'));
		$tpl->addData('wpDateFormat', get_option('wdtDateFormat'));
		$tpl->addData('wpTopOffset', get_option('wdtTopOffset'));
		$tpl->addData('wpLeftOffset', get_option('wdtLeftOffset'));
		$tpl->addData('languages', $languages);
		$tpl->addData('wpInterfaceLanguage', get_option('wdtInterfaceLanguage'));
		$tpl->addData('wdtFonts', wdt_get_system_fonts());
		$tpl->addData('wdtBaseSkin', get_option('wdtBaseSkin'));
		$wpFontColorSettings = get_option('wdtFontColorSettings');
		if(!empty($wpFontColorSettings)){
                    $wpFontColorSettings = unserialize($wpFontColorSettings);
		}else{
                    $wpFontColorSettings = array();
		}
		// Admin JS
		$tpl->addJs(WDT_JS_PATH.'wpdatatables/wpdatatables_admin.js');
		// Selecter
		$tpl->addJs(WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
		$tpl->addCss(WDT_CSS_PATH.'jquery.fs.selecter.css');
		// iCheck
                wp_enqueue_script('wpdatatables-icheck',WDT_JS_PATH.'icheck/icheck.min.js');
                wp_enqueue_style('wpdatatables-icheck',WDT_CSS_PATH.'icheck.minimal.css');
		// Popup
		$tpl->addJs(WDT_JS_PATH.'popup/jquery.remodal.min.js');
		$tpl->addCss(WDT_CSS_PATH.'jquery.remodal.css');
		
		$tpl->addData('wdtFontColorSettings', $wpFontColorSettings);
		$settings_page = $tpl->returnData();
		
		$settings_page = apply_filters( 'wpdatatables_filter_settings_page', $settings_page );
		
		echo $settings_page;
	
		do_action( 'wpdatatables_settings_page' );
	}
        
	/**
	 * Constructor page
	 */
	 function wpdatatables_constructor() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}	 	
		
                wp_enqueue_script( 'wpdatatables-ace', WDT_JS_PATH.'ace/ace.js' );
                wp_enqueue_script('wpdatatables-jsrender',WDT_JS_PATH.'jsrender/jsrender.min.js');
                wp_enqueue_script('wpdatatables-constructor',WDT_JS_PATH.'wpdatatables/wpdatatables_constructor.js');
		wp_enqueue_script('wpdatatables-selecter',WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
		wp_enqueue_style('wpdatatables-selecter',WDT_CSS_PATH.'jquery.fs.selecter.css');
		wp_enqueue_script('wpdatatables-tagsinput',WDT_JS_PATH.'tagsinput/jquery.tagsinput.min.js');
		wp_enqueue_style('wpdatatables-tagsinput',WDT_CSS_PATH.'jquery.tagsinput.min.css');
        
			$translatedStrings = array(
					'new_column_name' => __('New column', 'wpdatatables'),
					'select_excel_csv' => __('Select an Excel or CSV file','wpdatatables'),
					'choose_file' => __('Choose file','wpdatatables'),
					'fileupload_empty_file' => __('Please upload or choose a file from Media Library!','wpdatatables')
			);
        
                wp_localize_script( 'wpdatatables-jsrender', 'wdt_constructor_strings', $translatedStrings );
		
		$tpl = new PDTTpl();
		$tpl->setTemplate( 'constructor.inc.php' );
		$constructor_page = $tpl->returnData();
		
		$constructor_page = apply_filters( 'wpdatatables_filter_constructor_page', $constructor_page );
		
		echo $constructor_page;
	
		do_action( 'wpdatatables_constructor_page' );		
		
	 }
         
         /**
          * Editor page
          */
         function wpdatatables_editor(){
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
                
		wp_enqueue_script('wpdatatables-jsrender',WDT_JS_PATH.'jsrender/jsrender.min.js');
		wp_enqueue_script('wpdatatables-selecter',WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
		wp_enqueue_style('wpdatatables-selecter',WDT_CSS_PATH.'jquery.fs.selecter.css');
                
		$table_id = $_GET['table_id'];

		$table_data = wdt_get_table_by_id( $table_id );
		$column_data = wdt_get_columns_by_table_id( $table_id );
                
		// Do action before rendering a table
		do_action( 'wpdatatables_before_render_table', $table_id );                

                // Preparing column properties
                $column_order = array();
                $column_titles = array();
                $column_widths = array();
                $column_types = array();
                $column_possible_values = array();
                foreach($column_data as $column){
                    $column_order[(int)$column->pos] = $column->orig_header;
                    if($column->display_header){
                            $column_titles[$column->orig_header] = $column->display_header;
                    }
                    if($column->width){
                            $column_widths[$column->orig_header] = $column->width;
                    }
                    if($column->column_type != 'autodetect'){
                            $column_types[$column->orig_header] = $column->column_type;
                    }
                    $column_possible_values[$column->orig_header] = $column->possible_values;
                }
                $tbl = new WPDataTable();
                $tbl->setWpId( $table_id );
                
                $tbl->queryBasedConstruct($table_data['content'], array(),
		   				array(
		   					'data_types'=>$column_types,
		   					'column_titles'=>$column_titles
		   					)
	   					);
                
                $tbl->reorderColumns( $column_order );
                $tbl->wdtDefineColumnsWidth( $column_widths );
                $tbl->setColumnsPossibleValues( $column_possible_values );
                
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-progressbar');
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_script('jquery-ui-button');
                wp_enqueue_style( 'dashicons' );
                wp_enqueue_script('wdt_google_charts','https://www.google.com/jsapi');
                wp_enqueue_script('formstone-selecter',WDT_JS_PATH.'selecter/jquery.fs.selecter.min.js');
                wp_enqueue_style('formstone-selecter',WDT_CSS_PATH.'jquery.fs.selecter.css');
                wp_enqueue_script('wpdatatables-icheck',WDT_JS_PATH.'icheck/icheck.min.js');
                wp_enqueue_style('wpdatatables-icheck',WDT_CSS_PATH.'icheck.minimal.css');
                wp_enqueue_script('remodal-popup',WDT_JS_PATH.'popup/jquery.remodal.min.js');
                wp_enqueue_style('remodal-popup',WDT_CSS_PATH.'jquery.remodal.css');
                wp_enqueue_script('pickadate-main',WDT_JS_PATH.'datepicker/picker.js');
                wp_enqueue_script('pickadate-date',WDT_JS_PATH.'datepicker/picker.date.js');
                wp_enqueue_style('pickadate-main',WDT_CSS_PATH.'datepicker.default.css');
                wp_enqueue_style('pickadate-date',WDT_CSS_PATH.'datepicker.default.date.css');
				wp_enqueue_script('wpdatatables-tagsinput',WDT_JS_PATH.'tagsinput/jquery.tagsinput.min.js');
				wp_enqueue_style('wpdatatables-tagsinput',WDT_CSS_PATH.'jquery.tagsinput.min.css');
                wp_enqueue_script('wpdatatables-editor',WDT_JS_PATH.'wpdatatables/wpdatatables_editor.js');
                wp_enqueue_media();
		
		    // Check the saerch values passed from URL
		    if( isset($_GET['wdt_search']) ){
			$tbl->setDefaultSearchValue($_GET['wdt_search']);
		    }

		   foreach($column_data as $column){
                                // Set filter types
		   		$tbl->getColumn($column->orig_header)->setFilterType($column->filter_type);
		   		// Set CSS class
		   		$tbl->getColumn($column->orig_header)->addCSSClass($column->css_class);
		   		// set visibility
		   		if(!$column->visible){
		   			$tbl->getColumn($column->orig_header)->hide();
				}
				// Set default value
				$tbl->getColumn($column->orig_header)->setDefaultValue($column->default_value);
				
				// Check the default values passed from URL
				if(isset($_GET['wdt_column_filter'])){
					foreach($_GET['wdt_column_filter'] as $fltColKey => $fltDefVal){
						$tbl->getColumn($fltColKey)->setDefaultValue($fltDefVal);
					}
				}
				
				// Set hiding on phones and tablets for responsiveness
				if($tbl->isResponsive()){
					if($column->hide_on_phones){
						$tbl->getColumn($column->orig_header)->hideOnPhones();
					}
					if($column->hide_on_tablets){
						$tbl->getColumn($column->orig_header)->hideOnTablets();
					}
				}
				// if grouping enabled for this column, passing it to table class
				if($column->group_column){
					$tbl->groupByColumn($column->orig_header);
				}
				if($column->sort_column !== '0'){
                                    $tbl->setDefaultSortColumn($column->orig_header);
                                    if($column->sort_column == '1'){
                                        $tbl->setDefaultSortDirection('ASC');
                                    }elseif($column->sort_column == '2'){
                                        $tbl->setDefaultSortDirection('DESC');
                                    }
				}
  			    if($table_data['chart']!='none'){
                                if($column->use_in_chart){
                                    $tbl->addChartSeries($column->orig_header);
                                }
                                if($column->chart_horiz_axis){
                                    $tbl->setChartHorizontalAxis($column->orig_header);
                                }
 			    }
 			    // Set ID column if specified
 			    if($column->id_column){
 			    	$tbl->setIdColumnKey($column->orig_header);
 			    }
 			    // Set front-end editor input type
 			    $tbl->getColumn($column->orig_header)->setInputType($column->input_type);
		   }
		   $output_str = '';
                   
                   if($table_data['title']){
                        $output_str .= apply_filters('wpdatatables_filter_table_title', (empty($table_data['title']) ? '' : '<h2>'. $table_data['title'] .'</h2>'), $table_id );
                   }

                   $tbl->disableTT();
                  
                   $tbl->enableEditing();
				   if ( $table_data['inline_editing'] ) {
						$tbl->enableInlineEditing();
				   }
				   if ( $table_data['popover_tools'] ) {
					   $tbl->enablePopoverTools();
				   }
                   $tbl->enableServerProcessing();

                   if(get_option('wdtInterfaceLanguage') != ''){
                         $tbl->setInterfaceLanguage(get_option('wdtInterfaceLanguage'));
                   }

                    $output_str .= $tbl->generateTable();
                
                $tpl = new PDTTpl();
                $tpl->addData( 'table_id', $table_id );
                $tpl->addData( 'table_to_edit', $output_str );
                $tpl->addData( 'column_data', $column_data );
		$tpl->setTemplate( 'editor.inc.php' );
		$editor_page = $tpl->returnData();
		$editor_page = apply_filters( 'wpdatatables_filter_editor_page', $editor_page );
		
		echo $editor_page;
	
		do_action( 'wpdatatables_settings_page' );		
                
         }
        

?>
