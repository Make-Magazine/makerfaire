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
        $sum_columns = array();
        $userid_column_header = '';

        $wdt_var1 = isset( $_GET['wdt_var1'] ) ? wpdatatables_sanitize_query( $_GET['wdt_var1'] ) : $table_data['var1'];
        $wdt_var2 = isset( $_GET['wdt_var2'] ) ? wpdatatables_sanitize_query( $_GET['wdt_var2'] ) : $table_data['var2'];
        $wdt_var3 = isset( $_GET['wdt_var3'] ) ? wpdatatables_sanitize_query( $_GET['wdt_var3'] ) : $table_data['var3'];

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
        $tbl = new WPDataTable();
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
                        'column_formulas'=>$column_formulas
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
                $date_format =  get_option('wdtDateFormat');
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
				
				// Sanitize a data
				$formdata[$column->orig_header] = strip_tags( $formdata[$column->orig_header], '<br/><b><strong><h1><h2><h3><a><i><em><ol><ul><blockquote>' );

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
	 	
	 	do_action( 'wpdatatables_before_delete_row', $id_val, $table_id );
	 	
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


?>