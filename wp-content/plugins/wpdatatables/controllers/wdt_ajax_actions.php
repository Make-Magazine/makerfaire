<?php
/**
 * @package wpDataTables
 * @version 1.6.0
 */
/**
 * Controller for AJAX actions
 */
?>
<?php

	/**
	 * Handler which returns the AJAX response
	 */
	 function wdt_get_ajax_data(){
        global $wdt_var1, $wdt_var2, $wdt_var3;

	 	$id = filter_var( $_GET['table_id'], FILTER_SANITIZE_NUMBER_INT );
	 	
	 	do_action('wpdatatables_get_ajax_data', $id);
	 	
	   	$table_data = wdt_get_table_by_id( $id );
	   	$column_data = wdt_get_columns_by_table_id( $id );
	   	$column_titles = array();
	   	$column_types = array();
	   	$column_filtertypes = array();
	   	$column_inputtypes = array();
        $column_formulas = array();
        $skip_thousands = array();
        $sum_columns = array();
        $userid_column_header = '';

        $wdt_var1 = isset( $_GET['wdt_var1'] ) ? wpdatatables_sanitize_query( $_GET['wdt_var1'] ) : $table_data['var1'];
        $wdt_var2 = isset( $_GET['wdt_var2'] ) ? wpdatatables_sanitize_query( $_GET['wdt_var2'] ) : $table_data['var2'];
        $wdt_var3 = isset( $_GET['wdt_var3'] ) ? wpdatatables_sanitize_query( $_GET['wdt_var3'] ) : $table_data['var3'];

        $table_view =  isset( $_POST['table'] ) ? $_POST['table']: '';

        foreach( $column_data as $column ){
             $column_order[(int)$column->pos] = $column->orig_header;
             if($column->display_header){
                 $column_titles[$column->orig_header] = $column->display_header;
             }else{
                 $column_titles[$column->orig_header] = $column->orig_header;
             }
             if($column->column_type != 'autodetect'){
                 $column_types[$column->orig_header] = $column->column_type;
                 if( $column->column_type == 'formula' ){
                     $column_formulas[$column->orig_header] = $column->calc_formula;
                 }
                 if( $column->column_type == 'int' && $column->skip_thousands_separator ){
                     $skip_thousands[] = $column->orig_header;
                 }
             }else{
                 $column_types[$column->orig_header] = 'string';
             }
             $column_filtertypes[$column->orig_header] = $column->filter_type;
             $column_inputtypes[$column->orig_header] = $column->input_type;
             if( $table_data['edit_only_own_rows']
                     && ( $table_data['userid_column_id'] == $column->id ) ){
                 $userid_column_header = $column->orig_header;
             }
             if( $column->sum_column ){
                 $sum_columns[] = $column->orig_header;
             }
        }
         if ( $table_view == 'excel' ) {
             $tbl = new WPExcelDataTable();
         } else {
             $tbl = new WPDataTable();
         }

        $tbl->setWpId( $id );
        $tbl->enableServerProcessing();
        if( $table_data['edit_only_own_rows'] ){
            $tbl->setOnlyOwnRows( true );
            $tbl->setUserIdColumn( $userid_column_header );
        }
        $tbl->setSumColumns( $sum_columns );
        $json = $tbl->queryBasedConstruct(
                $table_data['content'],
                array(),
                array(
                        'data_types'=>$column_types,
                        'column_titles'=>$column_titles,
                        'filter_types'=>$column_filtertypes,
                        'input_types'=>$column_inputtypes,
                        'column_order'=>$column_order,
                        'column_formulas'=>$column_formulas,
                        'skip_thousands'=>$skip_thousands
                    )
        );
		
		$json = apply_filters( 'wpdatatables_filter_server_side_data', $json, $id, $_GET );

		echo $json;
	 	
	 	exit();
	 }
	add_action( 'wp_ajax_get_wdtable', 'wdt_get_ajax_data' );
	add_action( 'wp_ajax_nopriv_get_wdtable', 'wdt_get_ajax_data' );

	/**
	 * Saves the table from frontend
	 */
	 function wdt_save_table_frontend(){
	 	global $wpdb;
	 	$formdata = $_POST['formdata'];
                
        $return_result = array( 'success' => '', 'error' => '', 'is_new' => false );
	 	
		$table_id = filter_var( $formdata['table_id'], FILTER_SANITIZE_NUMBER_INT );
	 	unset($formdata['table_id']);
	 	
	 	$formdata = apply_filters('wpdatatables_filter_frontend_formdata', $formdata, $table_id);
	 	
	 	$table_data = wdt_get_table_by_id( $table_id );
	 	$mysql_table_name = $table_data['mysql_table_name'];
	 	
	 	$columns_data = wdt_get_columns_by_table_id( $table_id );
 	 	$id_key = '';
                $id_val = '';
                $date_format = get_option('wdtDateFormat');
                $time_format = get_option('wdtTimeFormat');
                $valueQuote = '';

                if(get_option('wdtUseSeparateCon')){
                    $valueQuote = "'";
                }
                foreach($columns_data as $column){
                        if( $column->id_column ){
                            $id_key = $column->orig_header;
                            $id_val = filter_var( $formdata[$id_key], FILTER_SANITIZE_NUMBER_INT );
                            unset($formdata[$id_key]);
                        }else{
                            
                            // Defining the values for User ID columns and for "none" input types
                            if( $column->id == $table_data['userid_column_id'] ){
                                $formdata[ $column->orig_header ] = get_current_user_id();
                            }elseif($column->input_type == 'none'){
                               if($id_val == '0'){
                                   // For new values we take the default value (if defined)
                                   if( !empty( $column->default_value ) ){
                                       $formdata[$column->orig_header] = $column->default_value;
                                   }else{
                                       unset($formdata[$column->orig_header]);
                                   }
                               }else{
                                   // For updating values we do not modify the cell at all
                                   unset($formdata[$column->orig_header]);
                               }
                            }
                            
                            if( isset( $formdata[$column->orig_header] ) ){
				
                                // Sanitize data
                                $formdata[$column->orig_header] = strip_tags( $formdata[$column->orig_header], '<br/><br><b><strong><h1><h2><h3><a><i><em><ol><ul><li><img><blockquote><div><hr><p><span><select><option><sup><sub>' );

                                // Formatting for DB based on column type
                                switch($column->column_type){
                                       case 'date':
                                           if($formdata[$column->orig_header] != ''){

                                               $formdata[$column->orig_header] = 
                                                                       $valueQuote . DateTime::createFromFormat(
                                                                               $date_format,
                                                                               $formdata[$column->orig_header]
                                                                       )->format('Y-m-d') . $valueQuote;
                                           }else{
                                               $formdata[$column->orig_header] = $valueQuote.'NULL'.$valueQuote;
                                           }
                                           break;
                                       case 'datetime':
                                           if($formdata[$column->orig_header] != ''){

                                               $formdata[$column->orig_header] =
                                                   $valueQuote . DateTime::createFromFormat(
                                                       $date_format.' '.$time_format,
                                                       $formdata[$column->orig_header]
                                                   )->format('Y-m-d H:i:s') . $valueQuote;
                                           }else{
                                               $formdata[$column->orig_header] = $valueQuote.'NULL'.$valueQuote;
                                           }
                                           break;
                                       case 'float':
                                           $number_format = get_option('wdtNumberFormat') ? get_option('wdtNumberFormat') : 1;
                                           if($number_format == 1){
                                                   $formdata[$column->orig_header] = str_replace('.','',$formdata[$column->orig_header]);
                                                   $formdata[$column->orig_header] = str_replace(',','.',$formdata[$column->orig_header]);
                                           }else{
                                                   $formdata[$column->orig_header] = str_replace(',','',$formdata[$column->orig_header]);
                                           }
                                           $formdata[$column->orig_header] = filter_var( $formdata[$column->orig_header], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND );
                                       default:
                                           $formdata[$column->orig_header] = $valueQuote.$formdata[$column->orig_header].$valueQuote;
                                           break;
                                }
                                if( $column->input_type == 'textarea' ){
                                    $formdata[$column->orig_header] = str_replace( "\n" , '<br/>', $formdata[$column->orig_header] );
                                }
                                
                            }

                        }
                }

         $formdata = apply_filters( 'wpdatatables_filter_formdata_before_save', $formdata, $table_id );

	 	// If the plugin is using WP DB
	 	if(!get_option('wdtUseSeparateCon')){
            $formdata = stripslashes_deep($formdata);
			if($id_val != '0'){
                $res = $wpdb->update($mysql_table_name,
                                      $formdata,
                                        array(
                                                $id_key => $id_val
                                            )
                                        );
                if( !$res ){
                    if( !empty( $wpdb->last_error ) ){
                        $return_result['error'] = __('There was an error trying to update the row! Error: ', 'wpdatatables' ).$wpdb->last_error;
                    }else{
                        $return_result['success'] = $id_val;
                    }
                }else{
                    $return_result['success'] = $id_val;
                }
			}else{
                $return_result['is_new'] = true;
                $res = $wpdb->insert($mysql_table_name,
                                      $formdata );
                if( !$res ){
                    $return_result['error'] = __( 'There was an error trying to insert a new row! Error: ', 'wpdatatables' ).$wpdb->last_error;
                }else{
                    $return_result['success'] = $wpdb->insert_id;
                    $id_val = $wpdb->insert_id;
                }
			}
	 	}else{
                    // If plugin is using a separate DB
                    $sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
                    if($id_val != '0'){
                        $query = 'UPDATE '.$mysql_table_name.' SET ';
                        $i = 1;
                        foreach($formdata AS $column_key=>$column_value){
                            if($column_value == "''"){
                                    $column_value = "NULL";
                            }
                            $query .= '`'.$column_key.'` = '.$column_value.' ';
                            if($i < count($formdata)){
                                    $query .= ', ';
                            }
                            $i++;
                        }
                        $query .= ' WHERE `'.$id_key.'` = '.$id_val;
                        $query = apply_filters( 'wpdatatables_query_before_save_frontend', $query, $table_id );
                        if( $sql->doQuery($query) ){
                            $return_result['success'] = $id_val;
                        }else{
                            if( $sql->getLastError() !== '' ){
                                $return_result['error'] = __( 'There was an error trying to update the row! Error: ', 'wpdatatables' ).$sql->getLastError();
                            }else{
                                $return_result['success'] = $id_val;
                                $id_val = $sql->getLastInsertId();
                            }
                        }
                    }else{
                        $return_result['is_new'] = true;
                        $query = 'INSERT INTO '.$mysql_table_name.' ';
                        $columns = array();
                        $values = array();
                        foreach($formdata AS $column_key=>$column_value){
                            if($column_value == "''"){
                                    $column_value = "NULL";
                            }
                            $columns[] = '`'.$column_key.'`';
                            $values[] = $column_value;
                        }
                        $query .= ' ('.implode(',',$columns).') VALUES ';
                        $query .= ' ('.implode(',',$values).')';
                        $query = apply_filters( 'wpdatatables_query_before_save_frontend', $query, $table_id );
                        $sql->doQuery($query);
                        if( $sql->getLastError() == '' ){
                           $return_result['success'] = $sql->getLastInsertId();
                        }else{
                            $return_result['error'] = __( 'There was an error trying to insert a new row! Error: ', 'wpdatatables' ).$sql->getLastError();
                        }
                    }
	 	}
	 	
	 	do_action('wpdatatables_after_frontent_edit_row', $formdata, $id_val, $table_id );
                
	 	echo json_encode( $return_result );
	 	
	 	exit();
	 }
	add_action( 'wp_ajax_wdt_save_table_frontend', 'wdt_save_table_frontend' );
	add_action( 'wp_ajax_nopriv_wdt_save_table_frontend', 'wdt_save_table_frontend' );

    /**
     * Save changes on excel table cells
     */
    function wdt_save_table_cells_frontend(){
        global $wpdb;

        $return_result = array( 'success' => array(), 'error' => '', 'has_new' => false );

        $table_id = filter_var( $_POST['table_id'], FILTER_SANITIZE_NUMBER_INT );

        $cells_data = apply_filters('wpdatatables_excel_filter_frontend_formdata', $_POST['cells'], $table_id);

        $table_data = wdt_get_table_by_id( $table_id );
        $mysql_table_name = $table_data['mysql_table_name'];

        // If current user cannot edit - do nothing
        if( !wdt_current_user_can_edit( $table_data['editor_roles'], $table_id ) ){
            exit();
        }

        //getting distinct column names from sent data for change
        $column_names = call_user_func_array( 'array_merge', $cells_data );
        $column_names = array_keys( $column_names );

        //taking meta for changing columns
        //$columns_meta = wdt_get_columns_by_table_id( $table_id, $column_names );
        $columns_meta = wdt_get_columns_by_table_id( $table_id );
        $id_column_key = null;
        $q_placeholder = '%s';
        if( get_option('wdtUseSeparateCon') ){
            $q_placeholder = '?';
        }

        $formula_columns = array();
        $all_columns_names = array();

        //extracting key for id column
        foreach ($columns_meta as $column_meta) {
            $all_columns_names[] = $column_meta->orig_header;

            if( $column_meta->id_column ) {
                $id_column_key = $column_meta->orig_header;
            }

            if( $column_meta->column_type == 'formula' ) {
                $formula_columns[] = $column_meta;
            }
        }

        $non_existing_cols = array_diff( $column_names, $all_columns_names );

        //if some column not exist, error is returned
        if( !empty( $non_existing_cols ) ) {
            $return_result['error'] = __( 'Bad column names supplied: ', 'wpdatatables' ) . implode( ', ', $non_existing_cols );
        } else if( !in_array($id_column_key, $column_names) ) { //if id column not found among sent data, error is returned
            $return_result['error'] = __( 'ID column not supplied', 'wpdatatables' );
        } else {
            foreach ( $cells_data as $cell_data ) {
                //if there is no id column sent in cell data, error is returned
                if( !key_exists( $id_column_key, $cell_data ) ) {
                    $return_result['error'] = __( 'ID column not supplied for a cell', 'wpdatatables' );
                    break;//??
                } else {
                    //this is id column's value of changing cell's row
                    $cell_id_value = $cell_data[$id_column_key];

                    unset( $cell_data[$id_column_key] );
                    reset( $cell_data );

                    if( empty( $cell_id_value ) ) {
                        $insert_column_names = array_keys( $cell_data );
                        $q_column_names = '`' . implode( '`,`', $insert_column_names ) . '`';
                        $q_values = str_repeat( "$q_placeholder,", count( $cell_data ) );
                        $q_values = rtrim( $q_values , ',' );

                        $query = "INSERT INTO $mysql_table_name ($q_column_names) VALUES ($q_values)";

                        $q_params = array_values( $cell_data );
                        $q_action_flag = 'insert';
                    } else {
                        $q_set = '';
                        $q_params = array();

                        foreach ($cell_data as $cell_column_key => $cell_value) {
                            $q_set .= ( !empty( $q_set ) )? ', ': '';
                            $q_set .= "`$cell_column_key` = $q_placeholder";
                            $q_params[] = $cell_value;
                        }

                        $q_where = "`$id_column_key` = $q_placeholder";
                        $q_params[] = $cell_id_value;

                        $query = "UPDATE $mysql_table_name SET $q_set WHERE $q_where";
                        $q_action_flag = 'update';
                    }

                    $query = apply_filters( 'wpdatatables_filter_excel_editor_query', $query, $table_id );

                    if( get_option('wdtUseSeparateCon') ){
                        $sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD);
                        $sql->doQuery( $query, $q_params );
                        $sql_last_error = $sql->getLastError();

                        if( $sql_last_error != '' ){
                            $return_result['error'] = __( 'There was an error trying to insert a new row! Error: ', 'wpdatatables' ).$sql_last_error;
                            break;
                        } else {
                            $cell_id_value = ( $q_action_flag == 'update' )? $cell_id_value: $sql->getLastInsertId();
                            $return_result['success'][] = array(
                                                    "$id_column_key" => $cell_id_value,
                                                    'action' => $q_action_flag
                                );

                            if( $q_action_flag == 'insert' ) {
                                $return_result['has_new'] = true;
                            }
                        }

                        do_action('wpdatatables_excel_after_frontent_edit_row', $table_id, $cell_id_value, $cell_data, $q_action_flag, $sql_last_error );
                    } else {
                        array_unshift( $q_params, $query );
                        $res = $wpdb->query( call_user_func_array( array( $wpdb, 'prepare' ), $q_params ) );

                        if( $res === false  ) {
                            $return_result['error'] = __('There was an error trying to update the row! Error: ', 'wpdatatables' ).$wpdb->last_error;
                        }else{
                            $cell_id_value = ( $q_action_flag == 'update' )? $cell_id_value: $wpdb->insert_id;
                            $return_result['success'][] = array( "$id_column_key" => ( $q_action_flag == 'update' )? $cell_id_value: $wpdb->insert_id,
                                'action' => $q_action_flag
                            );

                            if( $q_action_flag == 'insert' ) {
                                $return_result['has_new'] = true;
                            }
                        }

                        do_action('wpdatatables_excel_after_frontent_edit_row', $table_id, $cell_id_value, $cell_data, $q_action_flag, $wpdb->last_error );
                    }
                }
            }
        }


        if( empty( $return_result['error'] ) ) {
            $calculated_formula_rows = array();
            $rows_data = $_POST['rows'];

            if( !empty( $formula_columns ) && !empty( $rows_data ) ) {
                foreach( $formula_columns as $formula_col ) {
                    $formula = $formula_col->calc_formula;
                    $col_key = $formula_col->orig_header;
                    $headers_in_formula = WDTTools::getColHeadersInFormula( $formula, $all_columns_names );

                    foreach( $rows_data as $row_data ) {
                        try {
                            $formula_value =
                                WPDataTable::solveFormula(
                                    $formula,
                                    $headers_in_formula,
                                    $row_data
                                );
                        }catch(Exception $e){
                            $formula_value = 0;
                        }

                        $id_col_value = $row_data[$id_column_key];

                        if( !isset( $calculated_formula_rows["$id_col_value"] ) ) {
                            $calculated_formula_rows["$id_col_value"] = array(
                                $id_column_key => $id_col_value,
                                $col_key => $formula_value
                            );
                        } else {
                            $calculated_formula_rows["$id_col_value"][$col_key] = $formula_value;
                        }
                    }
                }

            }
        }

        $return_result['formula_cells'] = array_values( $calculated_formula_rows );

        do_action('wpdatatables_excel_after_frontent_edit_cells', $_POST['cells'], $return_result, $table_id );

        echo json_encode( $return_result );

        exit();

    }

    add_action( 'wp_ajax_wdt_save_table_cells_frontend', 'wdt_save_table_cells_frontend' );
    add_action( 'wp_ajax_nopriv_wdt_save_table_cells_frontend', 'wdt_save_table_cells_frontend' );

	 /**
	  * Handle table row delete
	  */
	  function wdt_delete_table_row(){
	 	global $wpdb;

		$table_id = filter_var( $_POST['table_id'], FILTER_SANITIZE_NUMBER_INT );
	 	$id_key = filter_var( $_POST['id_key'], FILTER_SANITIZE_STRING );
	 	$id_val = filter_var( $_POST['id_val'], FILTER_SANITIZE_NUMBER_INT );
	 	
	 	$table_data = wdt_get_table_by_id( $table_id );
	 	$mysql_table_name = $table_data['mysql_table_name'];
                
        // If current user cannot edit - do nothing
        if( !wdt_current_user_can_edit( $table_data['editor_roles'], $table_id ) ){
            exit();
        }
	 	
	 	do_action( 'wpdatatables_before_delete_row', $id_val, $table_id, $id_key );
	 	
	 	// If the plugin is using WP DB
	 	if(!get_option('wdtUseSeparateCon')){
                    $wpdb->delete($mysql_table_name, array($id_key => $id_val));
	 	}else{
                    $sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
                    $query = "DELETE FROM ".$mysql_table_name." WHERE `".$id_key."`='".$id_val."'";
                    $sql->doQuery($query);
	 	}
	 	
	 	exit();
	  }
	add_action( 'wp_ajax_wdt_delete_table_row', 'wdt_delete_table_row' );
	add_action( 'wp_ajax_nopriv_wdt_delete_table_row', 'wdt_delete_table_row' );

    /**
     * Handle table multiple rows delete
     */
    function wdt_delete_table_rows() {
        global $wpdb;

        $return_result = array( 'success' => array(), 'error' => '' );

        $table_id = filter_var( $_POST['table_id'], FILTER_SANITIZE_NUMBER_INT );

        $rows = apply_filters('wpdatatables_excel_filter_delete_rows', $_POST['rows'], $table_id);

        if( empty( $rows ) ) {
            $return_result['error'] = __('Nothing to delete.', 'wpdatatables');
            echo json_encode( $return_result );
            exit();
        } else if( !is_array( $rows ) ) {
            $return_result['error'] = __('Bad request format.', 'wpdatatables');
            echo json_encode( $return_result );
            exit();
        }

        //first key(should be only key) as a id column name
        reset( $rows );
        $id_col_name = key( $rows );

        if( empty( $rows[$id_col_name] ) ) {
            $return_result['error'] =  __('Nothing to delete.', 'wpdatatables');
            echo json_encode( $return_result );
            exit();
        } else if( !is_array( $rows[$id_col_name] ) ) {
            $return_result['error'] =  __('Bad request format.', 'wpdatatables');
            echo json_encode( $return_result );
            exit();
        }

        $table_data = wdt_get_table_by_id( $table_id );
        $mysql_table_name = $table_data['mysql_table_name'];

        // If current user cannot edit - do nothing
        if( !wdt_current_user_can_edit( $table_data['editor_roles'], $table_id ) ){
            $return_result['error'] = __('You don\'t have permission to change this table.', 'wpdatatables');
            echo json_encode( $return_result );
            exit();
        }

        $columns_meta = wdt_get_columns_by_table_id( $table_id, array( $id_col_name ) );

        if( count($columns_meta) == 0 ) {
            $return_result['error'] = __('Supplied id column not exist.', 'wpdatatables');
            echo json_encode( $return_result );
            exit();
        } else {
            $column_meta = $columns_meta[0];

            if( $column_meta->id_column ) {
                $id_column_key = $column_meta->orig_header;

                $delete_row_ids = $rows[$id_column_key];

                foreach( $delete_row_ids as $row_id ) {
                    $row_id = intval( $row_id );

                    if( empty( $row_id ) ) {
                        continue;
                    }

                    do_action( 'wpdatatables_excel_before_delete_row', $row_id, $table_id );

                    // If the plugin is using WP DB
                    if(!get_option('wdtUseSeparateCon')){
                        $res = $wpdb->delete($mysql_table_name, array($id_column_key => $row_id));

                        if( $res === false  ) {
                            $return_result['error'] = __('There was an error trying to delete row! Error: ', 'wpdatatables' ).$wpdb->last_error;
                        }else{
                            if( !isset( $return_result['success']['deleted'] ) ) {
                                $return_result['success']['deleted'] = array();
                            }

                            $return_result['success']['deleted'][] = $row_id;
                        }

                        do_action( 'wpdatatables_excel_after_delete_row', $row_id, $table_id, $wpdb->last_error );
                    }else{
                        $sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD);
                        $query = "DELETE FROM ".$mysql_table_name." WHERE `".$id_column_key."`='".$row_id."'";
                        $sql->doQuery($query);
                        $sql_last_error = $sql->getLastError();
                        if( $sql_last_error != '' ){
                            $return_result['error'] = __( 'There was an error trying to delete row! Error: ', 'wpdatatables' ).$sql_last_error;
                            break;
                        } else {
                            if( !isset( $return_result['success']['deleted'] ) ) {
                                $return_result['success']['deleted'] = array();
                            }

                            $return_result['success']['deleted'][] = $row_id;
                        }

                        do_action( 'wpdatatables_excel_after_delete_row', $row_id, $table_id, $sql_last_error );
                    }
                }

                do_action( 'wpdatatables_excel_after_delete_all_rows', $table_id, $return_result );
            } else {
                $return_result['error'] = 'Supplied column is not id column.';
                echo json_encode( $return_result );
                exit();
            }
        }

        echo json_encode( $return_result );
        exit();
    }
//
    add_action( 'wp_ajax_wdt_delete_table_rows', 'wdt_delete_table_rows' );
    add_action( 'wp_ajax_nopriv_wdt_delete_table_rows', 'wdt_delete_table_rows' );


?>
