<?php
/**
 * Class Constructor contains methods and properties for constructing the tables
 * in wpDataTables WordPress plugin
 *
 * @author cjbug@ya.ru
 * 
 * @since June 2014
 */
 
 class wpDataTableConstructor {
 	
 	private $_name;
 	private $_index;
 	private $_db;
        private $_id;
        
        
        /*** For the WP DB type query ***/
        
        private $_tables_fields = array();
        private $_select_arr = array();
        private $_where_arr = array();
        private $_group_arr = array();
        private $_from_arr = array();
        private $_inner_join_arr = array();
        private $_left_join_arr = array();
        private $_table_aliases = array();
        private $_column_aliases = array();
        private $_column_headers = array();
        private $_has_groups = false;
        
        /** Query text **/
 	private $_query = '';
        
 	/**
 	 * The constructor
 	 */
 	public function __construct(){
            if(WDT_ENABLE_MYSQL && get_option('wdtUseSeparateCon')){
                $this->_db = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
            }
 	}
        
        /**
         * Sets ID;
         * @param type $id
         */
        public function setTableId( $id ){
            $this->_id = $id;
        }
        
        /**
         * Gets table ID
         */
        public function getTableId(){
            return $this->_id;
        }
 	
 	/**
 	 * Generate the new unique table name (For MySQL)
 	 */
 	 public function generateTableName(){
 	 	
 	 	$this->_index = (int) get_option('wdtGeneratedTablesCount', 0);
 	 	$this->_index += 1;
 	 	
 	 	$this->_name = 'wpdatatable_'.$this->_index;
 	 	
 	 	if(!get_option('wdtUseSeparateCon')){
 	 		global $wpdb;
	 	 	$this->_name = $wpdb->prefix.$this->_name;
 	 	}
 	 	
 	 	$this->_name = apply_filters( 'wpdatatables_before_generate_constructed_table_name', $this->_name );
		
                return $this->_name;
 	 }
         
         /**
          * Helper function to translate special UTF-8 to latin for MySQL
          */
         private static function slugify($text){
                // replace non letter or digits by _
                $text = preg_replace('#[^\\pL\d]+#u', '_', $text);

                // trim
                $text = trim($text, '_');

                // transliterate
                if (function_exists('iconv')){
                  $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
                }

                // lowercase
                $text = strtolower($text);

                // remove unwanted characters
                $text = preg_replace('#[^-\w]+#', '', $text);
                
                // WP sanitize
                $text = str_replace( array( '-', '_' ), '', sanitize_title( $text ) );

                if ( empty($text) || is_numeric($text)){
                    return 'wdt_column';
                }

               return $text;
         }
         
         /**
          * Helper function to generate unique MySQL column headers
          */
         private static function generateMySQLColumnName( $header, $existing_headers ){
            // Prepare the column MySQL title
            $column_header = self::slugify( $header );

            // Add index until column header becomes unique
            if( in_array( $column_header, $existing_headers ) ){
                    $index = 0;
                    do {
                            $index++;
                            $try_column_header = $column_header.'_'.$index;
                    } while( in_array( $try_column_header, $existing_headers ) );
                    $column_header = $try_column_header;
            }
            
            return $column_header;
         }
         
         /**
          * Helper function to prepare the filter, editor, column types, and create statement
          */
         private static function defineColumnProperties( $column_header, $column_type ){
             
                        // Defaults
                        $columnProperties = array(
                            'editor_type' => 'text',
                            'column_type' => 'string',
                            'filter_type' => 'text',
                            'create_block' => "`{$column_header}` VARCHAR(2000) "
                        );

                        switch( $column_type ){
                            case 'input':
                                $columnProperties = array(
                                    'editor_type' => 'text',
                                    'column_type' => 'string',
                                    'filter_type' => 'text',
                                    'create_block' => "`{$column_header}` VARCHAR(255) "
                                );
                                break;
                            case 'integer':
                                $columnProperties = array(
                                    'editor_type' => 'text',
                                    'column_type' => 'int',
                                    'filter_type' => 'number',
                                    'create_block' => "`{$column_header}` INT(11) "
                                );
                                break;
                            case 'float':
                                $columnProperties = array(
                                    'editor_type' => 'text',
                                    'column_type' => 'float',
                                    'filter_type' => 'number',
                                    'create_block' => "`{$column_header}` FLOAT(16,4) "
                                );
                                break;
                            case 'memo':
                                $columnProperties = array(
                                    'editor_type' => 'textarea',
                                    'column_type' => 'string',
                                    'filter_type' => 'text',
                                    'create_block' => "`{$column_header}` TEXT "
                                );
                               break;
                            case 'select':
                                $columnProperties = array(
                                    'editor_type' => 'selectbox',
                                    'column_type' => 'string',
                                    'filter_type' => 'select',
                                    'create_block' => "`{$column_header}` VARCHAR(2000) "
                                );
                                break;
                            case 'multiselect':
                                $columnProperties = array(
                                    'editor_type' => 'multi-selectbox',
                                    'column_type' => 'string',
                                    'filter_type' => 'select',
                                    'create_block' => "`{$column_header}` VARCHAR(2000) "
                                );
                                break;
                            case 'date':
                                $columnProperties = array(
                                    'editor_type' => 'date',
                                    'column_type' => 'date',
                                    'filter_type' => 'date-range',
                                    'create_block' => "`{$column_header}` DATE "
                                );
                                break;
                            case 'email':
                                $columnProperties = array(
                                    'editor_type' => 'email',
                                    'column_type' => 'email',
                                    'filter_type' => 'text',
                                    'create_block' => "`{$column_header}` VARCHAR(2000) "
                                );
                                break;
                            case 'link':
                                $columnProperties = array(
                                    'editor_type' => 'link',
                                    'column_type' => 'link',
                                    'filter_type' => 'text',
                                    'create_block' => "`{$column_header}` VARCHAR(2000) "
                                );
                                break;
                            case 'file':
                                $columnProperties = array(
                                    'editor_type' => 'link',
                                    'column_type' => 'attachment',
                                    'filter_type' => 'none',
                                    'create_block' => "`{$column_header}` VARCHAR(2000) "
                                );
                                break;
                            case 'image':
                                $columnProperties = array(
                                    'editor_type' => 'image',
                                    'column_type' => 'attachment',
                                    'filter_type' => 'none',
                                    'create_block' => "`{$column_header}` VARCHAR(2000) "
                                );
                                break;
                            default:
                                $columnProperties = array(
                                    'editor_type' => 'text',
                                    'column_type' => 'string',
                                    'filter_type' => 'text',
                                    'create_block' => "`{$column_header}` VARCHAR(2000) "
                                );
                                break;
                        }
                        
                        return $columnProperties;
             
         }
 	 
 	 /**
 	  * Generates and saves a new MySQL table and a new wpDataTable
 	  */
 	 public function generateManualTable( $table_data ){
 	 	global $wpdb;
                
                $this->_table_data = apply_filters( 'wdt_before_generate_manual_table', $table_data );
 	 	
 	 	// Generate the MySQL table name
 	 	$this->generateTableName();
 	 	
 	 	// Create the wpDataTable metadata
		$wpdb->insert(
			$wpdb->prefix."wpdatatables",
			array(
                            'title' => $this->_table_data['name'],
                            'table_type' => 'manual',
                            'content' => 'SELECT * FROM '.$this->_name,
                            'server_side' => 1,
                            'mysql_table_name' => $this->_name
			)
		);
		
		// Store the new table metadata ID
		$wpdatatable_id = $wpdb->insert_id;

 	 	// Prepare the create statement for the table itself
 	 	$create_statement = "CREATE TABLE ".$this->_name." (
 	 							wdt_ID INT( 11 ) NOT NULL AUTO_INCREMENT,";
 	 	
 	 	$column_headers = array();
 	 	
 	 	$column_index = 0;
 	 	
 	 	// Add metadata for ID column
 		$wpdb->insert(
 			$wpdb->prefix."wpdatatables_columns",
 			array(
 				'table_id' => $wpdatatable_id,
 				'orig_header' => 'wdt_ID',
 				'display_header' => 'wdt_ID',
 				'filter_type' => 'null_str',
 				'column_type' => 'int',
 				'visible' => 0,
 				'pos' => $column_index,
                                'id_column' => 1
 			)
 		); 
 		$column_index++;
                
 	 	foreach( $this->_table_data['columns'] as $column ){
 	 		
                        $column_header = self::generateMySQLColumnName( $column['name'], $column_headers );
                    
 	 		$column_headers[] = $column_header;
                        if( !isset( $column['orig_header'] ) ){
                            $column['orig_header'] = $column['name'];
                        }
                        $this->_column_headers[ $column['orig_header'] ] = $column_header;
                        
                        $columnProperties = self::defineColumnProperties($column_header, $column['type']);
                        
 	 		// Create the column metadata in WPDB
 	 		$wpdb->insert(
 	 			$wpdb->prefix."wpdatatables_columns",
 	 			array(
 	 				'table_id' => $wpdatatable_id,
 	 				'orig_header' => $column_header,
 	 				'display_header' => $column['name'],
 	 				'filter_type' => $columnProperties['filter_type'],
 	 				'column_type' => $columnProperties['column_type'],
 	 				'pos' => $column_index,
                                        'possible_values' => str_replace( ',,;,|', '|', $column['possible_values'] ),
                                        'default_value' => $column['default_value'],
                                        'input_type' => $columnProperties['editor_type']
 	 			)
 	 		);
 	 		
 	 		$create_statement .= $columnProperties['create_block'].', ';
 	 		
 	 		$column_index++;
 	 	}
 	 	
 	 	// Add the ID unique key
 	 	$create_statement .= " UNIQUE KEY wdt_ID (wdt_ID)) CHARACTER SET=utf8 COLLATE utf8_general_ci";
 	 	
 	 	// Call the create statement on WPDB or on external DB if it is defined
 	 	if(get_option('wdtUseSeparateCon')){
 	 		// External DB
 	 		$this->_db->doQuery( $create_statement, array() );
 	 	}else{
 	 		$wpdb->query( $create_statement );
 	 	}
 	 	
 	 	// Update the index in WPDB
 	 	update_option('wdtGeneratedTablesCount',$this->_index);
                
                return $wpdatatable_id;
 	 	
 	 }
         
         /**
          * Generates and returns a MySQL query based on user's visual input
          */
         public function generateMySQLBasedQuery( $table_data ){
             global $wpdb;
             
             $this->_table_data = apply_filters( 'wdt_before_generate_mysql_based_query', $table_data );
             
             if( !isset( $this->_table_data['where_conditions'] ) ){
                 $this->_table_data['where_conditions'] = array();
             }
             
             if( isset( $this->_table_data['grouping_rules'] ) ){
                 $this->_has_groups = true;
             }
             
             if( !isset( $table_data['mysql_columns']  ) ){
                 $table_data['mysql_columns'] = array();
             }
             
             // Initializing structure for the SELECT part of query
             $this->_prepareMySQLSelectBlock( $table_data['mysql_columns'] );
             
             // Initializing structure for the WHERE part of query
             $this->_prepareMySQLWhereBlock();

             // Prepare the GROUP BY block
             $this->_prepareMySQLGroupByBlock();
             
             // Prepare the join rules
             $this->_prepareMySQLJoinedQueryStructure();
             
             // Prepare the query itself
             $this->_query = $this->_buildMySQLQuery();
         }
         
         
         /**
          * Generates and returns a WP based MySQL query based on user's visual input
          */
         public function generateWPBasedQuery( $table_data ){
             global $wpdb;
             $this->_table_data = apply_filters( 'wdt_before_generate_wp_based_query', $table_data );
             
             if( !isset( $this->_table_data['where_conditions'] ) ){
                 $this->_table_data['where_conditions'] = array();
             }
             
             if( isset( $this->_table_data['grouping_rules'] ) ){
                 $this->_has_groups = true;
             }
             
             // Prepare the tables fields
             $this->_tables_fields = self::generateTablesFieldsStructureWPBased( $this->_table_data['post_columns'] );
             
             // Initializing structure for the SELECT part of query
             $this->_prepareWPSelectBlock();
             
             // We need to go through the rest of where conditions and add 'inner join' parts for them if needed
             $this->_prepareWPWhereBlock();
             
             // We need to add 'GROUP BY' blocks
             $this->_prepareWPGroupByBlock();
             
             if( ( $this->_table_data['handle_post_types'] == 'join' ) 
                     || count( $this->_table_data['post_types'] ) == 1
                     ){
                // We do JOINs
                $this->_prepareWPJoinedQueryStructure();
                
                $this->_query = $this->_buildWPJoinedQuery();

             }else{
                 // We do UNIONs
                 $this->_query = $this->_buildWPJoinedQuery();
             }
             
         }
         
         /**
          * Helper function to generate the fields structure from MySQL tables
          */
         private function _prepareMySQLSelectBlock(){
             
            foreach( $this->_table_data['mysql_columns'] as $mysql_column ){
                
                $mysql_column_arr = explode( '.', $mysql_column );
                if( !isset($this->_select_arr[$mysql_column_arr[0]]) ){
                    $this->_select_arr[$mysql_column_arr[0]] = array();
                }
                $this->_select_arr[$mysql_column_arr[0]][] = $mysql_column;
                
                if( !in_array( $mysql_column_arr[0], $this->_from_arr ) ){
                    $this->_from_arr[] = $mysql_column_arr[0];
                }
                
            }
            
         }         
         
         /**
          * Helper function to generate the fields structire from WP Posts data
          */
         public static function generateTablesFieldsStructureWPBased( $columns ){
             $tables_fields = array();
             
             // Parse the columns list, generate table aliases and the columns
             foreach( $columns as $post_column ){
                 $post_column_arr = explode('.',$post_column);

                 if(count($post_column_arr) == 2){
                     // This is a column of a post table
                     if(!isset($tables_fields[$post_column_arr[0]])){
                         $tables_fields[$post_column_arr[0]] = array(
                             'table' => 'posts',
                             'post_type' => $post_column_arr[0],
                             'sql_alias' => self::prepareSqlAlias( 'posts_' . $post_column_arr[0] ),
                             'columns' => array()
                         );
                     }
                     $tables_fields[$post_column_arr[0]]['columns'][] = array(
                         'field' => $post_column_arr[1],
                         'col_alias' => self::prepareSqlAlias( $post_column ),
                         'col_internal_name' => $post_column
                      );
                 }else{
                     // This is a taxonomy or a meta value
                     if($post_column_arr[1] == 'meta'){
                        // This is a meta value
                        $tables_fields[$post_column_arr[2]] = array(
                            'table' => 'postmeta',
                            'sql_alias' => self::prepareSqlAlias( $post_column.'_tbl' ),
                            'col_alias' => self::prepareSqlAlias( $post_column ),
                            'col_internal_name' => $post_column,
                            'post_type' => $post_column_arr[0]
                        );
                     }elseif($post_column_arr[1] == 'taxonomy'){
                         // This is a taxonomy value
                        $tables_fields[$post_column_arr[2]] = array(
                            'table' => 'taxonomy',
                            'sql_alias' => self::prepareSqlAlias( $post_column.'_tbl' ),
                            'col_alias' => self::prepareSqlAlias( $post_column ),
                            'col_internal_name' => $post_column,
                            'post_type' => $post_column_arr[0]
                        );
                     }
                 }
             }
             
             return $tables_fields;
             
         }
         
         public static function prepareSqlAlias( $alias ){
             
             $sqlAlias = str_replace( '.', '_', $alias );
             $sqlAlias = str_replace( '-', '_', $sqlAlias );
             
             return $sqlAlias;
         }
         
         public static function buildWhereCondition( $leftOperand, $operator, $rightOperand, $isValue = true ){
             $rightOperand = stripslashes_deep( $rightOperand );
             $wrap = $isValue ? "'" : "";
             switch($operator){
                 case 'eq':
                     return "{$leftOperand} = {$wrap}{$rightOperand}{$wrap}";
                 case 'neq':
                     return "{$leftOperand} != {$wrap}{$rightOperand}{$wrap}";
                 case 'gt':
                     return "{$leftOperand} > {$wrap}{$rightOperand}{$wrap}";
                 case 'gtoreq':
                     return "{$leftOperand} >= {$wrap}{$rightOperand}{$wrap}";
                 case 'lt':
                     return "{$leftOperand} < {$wrap}{$rightOperand}{$wrap}";
                 case 'ltoreq':
                     return "{$leftOperand} <= {$wrap}{$rightOperand}{$wrap}";
                 case 'in':
                     return "{$leftOperand} IN ({$rightOperand})";
                 case 'like':
                     return "{$leftOperand} LIKE {$wrap}{$rightOperand}{$wrap}";
                 case 'plikep':
                     return "{$leftOperand} LIKE {$wrap}%{$rightOperand}%{$wrap}";
             }
         }
         
         /**
          * Prepares the SELECT part for the WP-based tables
          */
         private function _prepareWPSelectBlock(){
             global $wpdb;
             
             if(empty($this->_tables_fields)){ return; }
             
             $thumbSizeString = self::getThumbSizeString();
             
             foreach($this->_tables_fields as $valueName => &$fields){
                 // Fill in the SQL alias of the table
                 $this->_table_aliases[] = $fields['sql_alias'];

                 if($fields['table'] == 'posts'){

                     foreach($fields['columns'] as $table_column){
                         if(!isset($this->_select_arr[$fields['sql_alias']])){
                             $this->_select_arr[$fields['sql_alias']] = array();
                         }
                         
                         if( $table_column['field'] == 'title_with_link_to_post' ){
                             // Generating an "<a href="..."" link to the post
                            $this->_select_arr[$fields['sql_alias']][] = 'CONCAT(\'<a href="\','.$fields['sql_alias'].'.guid,\'">\','.$fields['sql_alias'].'.post_title,\'</a>\') AS '.$table_column['col_alias'];                             
                         }elseif( $table_column['field'] == 'thumbnail_with_link_to_post' ){
                             // Generating an "<a href="..."" link to the post and a thumbnail URL depending on WP settings
                             $this->_select_arr[$fields['sql_alias'].'_img'][] = 'CONCAT(
                                    \'<a href="\',
                                    '.$fields['sql_alias'].'.guid,
                                    \'"><img src="\', 
                                    REPLACE( 
                                        '.$fields['sql_alias'].'_img'.'.guid,
                                        CONCAT(
                                            \'.\',
                                            SUBSTRING_INDEX(  
                                                '.$fields['sql_alias'].'_img'.'.guid,
                                                \'.\',
                                                -1
                                            )
                                        ),
                                        CONCAT(
                                            \''.$thumbSizeString.'\' ,
                                            SUBSTRING_INDEX(  
                                                '.$fields['sql_alias'].'_img'.'.guid,
                                                \'.\',
                                                -1
                                            )
                                        )
                                        ), 
                                    \'" /></a>\'
                                  ) AS '.$table_column['col_alias'];
                             $this->_left_join_arr[$fields['sql_alias'].'_img'] = '(SELECT '.$fields['sql_alias'].'_imgposts.guid AS guid, '.$fields['sql_alias'].'_imgpostmeta.post_id AS post_id
                                        FROM '.$wpdb->postmeta.' AS '.$fields['sql_alias'].'_imgpostmeta 
                                        INNER JOIN '. $wpdb->posts .' AS '.$fields['sql_alias'].'_imgposts 
                                            ON '.$fields['sql_alias'].'_imgpostmeta.meta_value = '.$fields['sql_alias'].'_imgposts.ID
                                        WHERE '.$fields['sql_alias']."_imgpostmeta.meta_key = '_thumbnail_id' ".
                                        ') AS '.$fields['sql_alias'].'_img';
                             $this->_where_arr[$fields['sql_alias'].'_img'][] = $fields['sql_alias'].'_img.post_id = '.$fields['sql_alias'].'.ID';
                         }elseif( $table_column['field'] == 'post_author' ){
                             // Get the author nicename instead of ID
                             $this->_select_arr[$fields['sql_alias'].'_author'][] = $fields['sql_alias'].'_author.display_name AS '.$table_column['col_alias'];
                             $this->_inner_join_arr[$fields['sql_alias'].'_author'] = $wpdb->users.' AS '.$fields['sql_alias'].'_author';
                             $this->_where_arr[$fields['sql_alias'].'_author'][] = $fields['sql_alias'].'_author.ID = '.$fields['sql_alias'].'.post_author';
                         }elseif( $table_column['field'] == 'post_content_limited_100_chars' ){
                             // Get post content limited to 100 chars
                            $this->_select_arr[$fields['sql_alias']][] = 'LEFT( '.$fields['sql_alias'].'.post_content, 100) AS '.$table_column['col_alias'];
                         }else{
                            $this->_select_arr[$fields['sql_alias']][] = $fields['sql_alias'].'.'.$table_column['field'].' AS '.$table_column['col_alias'];
                         }
                         
                         $this->_column_aliases[$table_column['col_internal_name']] = $table_column['col_alias'];
                         
                         // Look up for this column in additional 'where' conditions
                         foreach( $this->_table_data['where_conditions'] as $where_key=>&$where_condition ){
                            $where_column_arr = explode( '.', $where_condition['column'] );
                            if( ( count( $where_column_arr ) == 2 ) 
                                    && ( $valueName == $where_column_arr[0] ) ){
                                       if(!isset($this->_where_arr[$fields['sql_alias']])){
                                          $this->_where_arr[$fields['sql_alias']] = array();
                                       }                                
                                       $this->_where_arr[$fields['sql_alias']][] = self::buildWhereCondition(
                                                  'posts_'.$where_condition['column'], 
                                                  $where_condition['operator'],
                                                  $where_condition['value']
                                              );
                                       unset( $this->_table_data['where_conditions'][$where_key] );
                            }
                         }
                     }
                    $this->_from_arr[$fields['sql_alias']] = $wpdb->posts.' AS '.$fields['sql_alias'];
                    if( $fields['post_type'] != 'all' ){
                        $this->_where_arr[$fields['sql_alias']][] = $fields['sql_alias'].".post_type = '".$fields['post_type']."'";
                    }
                 }elseif($fields['table'] == 'postmeta'){
                     if(!isset($this->_select_arr[$fields['sql_alias']])){
                        $this->_select_arr[$fields['sql_alias']] = array();
                     }
                     if( !$this->_has_groups ){
                        $this->_select_arr[$fields['sql_alias']][] = $fields['sql_alias'].'.meta_value AS '.$fields['col_alias'];
                     }else{
                        $this->_select_arr[$fields['sql_alias']][] = 'GROUP_CONCAT(distinct '.$fields['sql_alias'].'.meta_value) AS '.$fields['col_alias']; 
                     }
                     $this->_inner_join_arr[$fields['sql_alias']] = self::preparePostMetaSubquery( $fields['sql_alias'], $fields['post_type'] );
                             
                     if( !isset( $this->_where_arr[$fields['sql_alias']] ) ){
                        $this->_where_arr[$fields['sql_alias']] = array();
                     }
                     $this->_where_arr[$fields['sql_alias']][] = $fields['sql_alias'].".meta_key = '".$valueName."' AND ".$fields['sql_alias'].".id = posts_".$fields['post_type'].".ID ";

                     $this->_column_aliases[$fields['col_internal_name']] = $fields['col_alias'];
                     
                    foreach( $this->_table_data['where_conditions'] as $where_key=>&$where_condition ){
                        $where_column_arr = explode( '.', $where_condition['column'] );
                        if( ( count( $where_column_arr ) == 3 ) 
                                && ( $where_column_arr[1] == 'meta' )
                                && ( $valueName == $where_column_arr[2] ) ){
                                   if(!isset($this->_where_arr[$fields['sql_alias']])){
                                      $this->_where_arr[$fields['sql_alias']] = array();
                                   }
                                   $this->_where_arr[$fields['sql_alias']][] = self::buildWhereCondition(
                                              $fields['col_alias'], 
                                              $where_condition['operator'],
                                              $where_condition['value']
                                          );
                                   unset($this->_table_data['where_conditions'][$where_key]);
                        }
                    }
                     
                 }elseif($fields['table'] == 'taxonomy'){
                     if(!isset($this->_select_arr[$fields['sql_alias']])){
                        $this->_select_arr[$fields['sql_alias']] = array();
                     }
                     if( !$this->_has_groups ){
                        $this->_select_arr[$fields['sql_alias']][] = $fields['sql_alias'].'.name AS '.$fields['col_alias'];
                     }else{
                        $this->_select_arr[$fields['sql_alias']][] = 'GROUP_CONCAT(distinct '.$fields['sql_alias'].'.name) AS '.$fields['col_alias'];
                     }
                     $this->_inner_join_arr[$fields['sql_alias']] = self::preparePostTaxonomySubquery( $fields['sql_alias'], $valueName );
                     $this->_where_arr[$fields['sql_alias']][] = $fields['sql_alias'].".ID = posts_".$fields['post_type'].".id ";
                     
                    $this->_column_aliases[$fields['col_internal_name']] = $fields['col_alias'];
                                    
                    foreach( $this->_table_data['where_conditions'] as $where_key=>&$where_condition ){
                        $where_column_arr = explode( '.', $where_condition['column'] );
                        if( ( count( $where_column_arr ) == 3 ) 
                                && ( $where_column_arr[1] == 'taxonomy' )
                                && ( $valueName == $where_column_arr[2] ) ){
                                    if(!isset($this->_where_arr[$fields['sql_alias']])){
                                       $this->_where_arr[$fields['sql_alias']] = array();
                                    }
                                   $this->_where_arr[$fields['sql_alias']][] = self::buildWhereCondition(
                                                  $fields['col_alias'], 
                                                  $where_condition['operator'],
                                                  $where_condition['value']
                                                );
                                   unset($this->_table_data['where_conditions'][$where_key]);
                        }
                    }
                 }
                 
             }
             
         }
         
         private function _prepareMySQLWhereBlock(){

             if( empty( $this->_table_data['where_conditions'] ) ){
                 return;
             }
             
             foreach( $this->_table_data['where_conditions'] as $where_condition ){
                 
                $where_column_arr = explode( '.', $where_condition['column'] );
                 
                if( !in_array( $where_column_arr[0], $this->_from_arr ) ){
                    $this->_from_arr[] = $where_column_arr[0];
                }
                
                $this->_where_arr[$where_column_arr[0]][] = self::buildWhereCondition(
                                                                $where_condition['column'],
                                                                $where_condition['operator'],
                                                                $where_condition['value']
                                                            );
                
             }
             
         }
         
         /**
          * Prepares the WHERE block for WP-based query
          */
         private function _prepareWPWhereBlock(){
             global $wpdb;
             
             if( empty( $this->_table_data['where_conditions'] ) ){
                 return;
             }
             
             foreach( $this->_table_data['where_conditions'] as $where_condition ){
                 
                 $where_column_arr = explode('.',$where_condition['column']);
                 if(count($where_column_arr) == 2){
                     $tbl_alias = 'posts_'.$where_column_arr[0];
                     $tbl_alias = str_replace( '-','_',$tbl_alias );
                     $this->_from_arr[$tbl_alias] = $wpdb->posts.'_'.$where_column_arr[0].' AS '.$tbl_alias;
                     $this->_where_arr[$tbl_alias] = array();
                     $this->_where_arr[$tbl_alias][] = self::buildWhereCondition(
                                              $where_column_arr[1], 
                                              $where_condition['operator'],
                                              $where_condition['value']
                                          );
                 }else{
                    if(count($where_column_arr) == 3){
                        $tbl_alias = str_replace( '.','_',$where_condition['column'] ).'_tbl';
                        $tbl_alias = str_replace( '-','_',$tbl_alias );
                        if($where_column_arr[1] == 'meta'){
                            $this->_inner_join_arr[$tbl_alias] = self::preparePostMetaSubquery( 
                                                                        $tbl_alias, 
                                                                        $where_column_arr[0],  
                                                                        $where_column_arr[2] 
                                                                    );
                            $this->_where_arr[$tbl_alias] = array();
                            $this->_where_arr[$tbl_alias][] = self::buildWhereCondition(
                                                                $tbl_alias.'.meta_value',
                                                                $where_condition['operator'],
                                                                $where_condition['value']
                                                            );
                        }elseif($where_column_arr[1] == 'taxonomy'){
                            
                            $this->_inner_join_arr[$tbl_alias] = self::preparePostTaxonomySubquery( $tbl_alias, $where_column_arr[2] );
                            
                            $this->_where_arr[$tbl_alias] = array();
                            $this->_where_arr[$tbl_alias][] = self::buildWhereCondition(
                                                                $tbl_alias.'.name',
                                                                $where_condition['operator'],
                                                                $where_condition['value']                            
                                                            );
                        }
                        $this->_where_arr[$tbl_alias][] = self::buildWhereCondition(
                                                            $tbl_alias.'.id',
                                                            'eq',
                                                            'posts_'.$where_column_arr[0].'.ID'
                                                        );                            
                        
                    }
                 }
             }             
         }
         
         public static function preparePostMetaSubquery( $alias, $post_type, $meta_key = '' ){
             global $wpdb;
             
             if(empty($alias) || empty($post_type)){ return ''; }
             
             $post_meta_subquery = "(SELECT {$alias}_posts.ID as id, meta_value, meta_key ";
             $post_meta_subquery .= " FROM {$wpdb->postmeta} AS {$alias}_postmeta ";
             $post_meta_subquery .= " INNER JOIN {$wpdb->posts} AS {$alias}_posts ";
             $post_meta_subquery .= "  ON {$alias}_postmeta.post_id = {$alias}_posts.ID ";
             if( !empty( $meta_key ) ){
                $post_meta_subquery .= "  AND {$alias}_postmeta.meta_key = '{$meta_key}'";
             } 
             $post_meta_subquery .= "  AND {$alias}_posts.post_type = '{$post_type}') AS {$alias}";

             return $post_meta_subquery;
             
         }
         
         /**
          * Prepare the query text for the WP based wpDataTable
          */
         private function _buildWPJoinedQuery(){
             
                // Build the final output
                $query = "SELECT ";
                $i = 0;
                foreach($this->_select_arr as $table_alias=>$select_block){
                    $query .= implode( ",\n       ", $select_block );
                    $i++;
                    if($i<count($this->_select_arr)){
                        $query .= ",\n       ";
                    }
                }
                $query .= "\nFROM ";
                $query .= implode( ', ', $this->_from_arr )."\n";
                if(!empty($this->_inner_join_arr)){
                    $i = 0;
                    foreach($this->_inner_join_arr as $table_alias => $inner_join_block){
                        $query .= "  INNER JOIN ".$inner_join_block."\n";
                        if( !empty( $this->_where_arr[$table_alias] ) ){
                            $query .= "     ON ".implode( "\n     AND ", $this->_where_arr[$table_alias] )."\n";
                            unset( $this->_where_arr[$table_alias] );
                        }
                    }
                }
                if(!empty($this->_left_join_arr)){
                    
                    foreach($this->_left_join_arr as $table_alias => $left_join_block){
                        $query .= "  LEFT JOIN ".$left_join_block."\n";
                        if( !empty( $this->_where_arr[$table_alias] ) ){
                            $query .= "     ON ".implode( "\n     AND ", $this->_where_arr[$table_alias] )."\n";
                            unset( $this->_where_arr[$table_alias] );
                        }
                    }
                }
                if(!empty($this->_where_arr)){
                    $query .= "WHERE 1=1 \n   AND ";
                    $i = 0;
                    foreach($this->_where_arr as $table_alias => $where_block){
                        $query .= implode("\n   AND ", $where_block);
                        $i++;
                        if($i<count($this->_where_arr)){
                            $query .= "\n   AND ";
                        }
                    }
                }
                if( !empty( $this->_group_arr ) ){
                    $query .= "\nGROUP BY ". implode( ', ', $this->_group_arr );
                }
                return $query;
         }
         
        /**
         * Prepares the structure of the JOIN rules for MySQL based tables
         */
        private function _prepareMySQLJoinedQueryStructure(){
            if( !isset( $this->_table_data['join_rules'] ) ){ return; }
            
            foreach( $this->_table_data['join_rules'] as $join_rule ){
                if( empty( $join_rule['initiator_column'] ) 
                        || empty( $join_rule['connected_column'] ) ){ 
                    continue;
                }
                
                $connected_column_arr = explode( '.', $join_rule['connected_column'] );
                
                if( in_array( $connected_column_arr[0], $this->_from_arr ) 
                        && count( $this->_from_arr ) > 1 ){
                    if( $join_rule['type'] == 'left' ){
                        $this->_left_join_arr[$connected_column_arr[0]] = $connected_column_arr[0];
                    }else{
                        $this->_inner_join_arr[$connected_column_arr[0]] = $connected_column_arr[0];
                    }
                    unset( $this->_from_arr[array_search($connected_column_arr[0], $this->_from_arr)] );
                }else{
                    if( $join_rule['type'] == 'left' ){
                        $this->_left_join_arr[$connected_column_arr[0]] = $connected_column_arr[0];
                    }else{
                        $this->_inner_join_arr[$connected_column_arr[0]] = $connected_column_arr[0];
                    }
                }

                $this->_where_arr[$connected_column_arr[0]][] = self::buildWhereCondition(
                                                                    $join_rule['initiator_table'].'.'.$join_rule['initiator_column'],
                                                                    'eq',
                                                                    $join_rule['connected_column'],
                                                                    false
                                                                );                
            }
            
        }
         
        /**
         * Prepares the query text for MySQL based table
         */
        private function _buildMySQLQuery(){
            
                // Build the final output
                $query = "SELECT ";
                $i = 0;
                foreach($this->_select_arr as $table_alias=>$select_block){
                    $query .= implode( ",\n       ", $select_block );
                    $i++;
                    if($i<count($this->_select_arr)){
                        $query .= ",\n       ";
                    }
                }
                $query .= "\nFROM ";
                $query .= implode( ', ', $this->_from_arr )."\n";
                if(!empty($this->_inner_join_arr)){
                    $i = 0;
                    foreach($this->_inner_join_arr as $table_alias => $inner_join_block){
                        $query .= "  INNER JOIN ".$inner_join_block."\n";
                        if( !empty( $this->_where_arr[$table_alias] ) ){
                            $query .= "     ON ".implode( "\n     AND ", $this->_where_arr[$table_alias] )."\n";
                            unset( $this->_where_arr[$table_alias] );
                        }
                    }
                }
                if(!empty($this->_left_join_arr)){
                    
                    foreach($this->_left_join_arr as $table_alias => $left_join_block){
                        $query .= "  LEFT JOIN ".$left_join_block."\n";
                        if( !empty( $this->_where_arr[$table_alias] ) ){
                            $query .= "     ON ".implode( "\n     AND ", $this->_where_arr[$table_alias] )."\n";
                            unset( $this->_where_arr[$table_alias] );
                        }
                    }
                }
                if(!empty($this->_where_arr)){
                    $query .= "WHERE 1=1 \n   AND ";
                    $i = 0;
                    foreach($this->_where_arr as $table_alias => $where_block){
                        $query .= implode("\n   AND ", $where_block);
                        $i++;
                        if($i<count($this->_where_arr)){
                            $query .= "\n   AND ";
                        }
                    }
                }
                if( !empty( $this->_group_arr ) ){
                    $query .= "\nGROUP BY ". implode( ', ', $this->_group_arr );
                }
                return $query;
            
        }
                 
         /**
          * Prepares the Joined query structure for WP-based wpDataTables
          */
         private function _prepareWPJoinedQueryStructure(){
            global $wpdb;
            
            if( !isset( $this->_table_data['join_rules'] ) ){ return; }
            
            // Need to go through each post type and define the join rule
            foreach( $this->_table_data['join_rules'] as $join_rule ){
                if( empty( $join_rule['initiator_column'] ) 
                        || empty( $join_rule['connected_column'] ) ){ 
                    continue;
                }

                $connected_column_arr = explode( '.',$join_rule['connected_column'] );
                if( count( $connected_column_arr ) == 2 ){
                    // Joining by posts table column
                    
                    $tbl_alias = self::prepareSqlAlias( 'posts_'.$connected_column_arr[0] );
                    
                    if(!isset($this->_where_arr[$tbl_alias])){
                        $this->_where_arr[$tbl_alias] = array();
                    }
                    if( isset( $this->_from_arr[$tbl_alias] ) 
                            && count( $this->_from_arr ) > 1 ){
                        if( $join_rule['type'] == 'left' ){
                            $this->_left_join_arr[$tbl_alias] = $this->_from_arr[$tbl_alias];
                        }else{
                            $this->_inner_join_arr[$tbl_alias] = $this->_from_arr[$tbl_alias];
                        }
                        unset( $this->_from_arr[$tbl_alias] );
                    }else{
                        if( $join_rule['type'] == 'left' ){
                            $this->_left_join_arr[$tbl_alias] = $wpdb->posts.' AS '.$tbl_alias;
                        }else{
                            $this->_inner_join_arr[$tbl_alias] = $wpdb->posts.' AS '.$tbl_alias;
                        }
                        $this->_where_arr[$tbl_alias][] = $tbl_alias.".post_type = '".$connected_column_arr[0]."'";
                    }

                    $this->_where_arr[$tbl_alias][] = self::buildWhereCondition(
                                                            str_replace( '-', '_','posts_'.$join_rule['connected_column'] ),
                                                            'eq',
                                                             self::prepareSqlAlias( 'posts_' . $join_rule['initiator_post_type'] )
                                                            . '.' . self::prepareSqlAlias( $join_rule['initiator_column'] ),
                                                            false
                                                        );
                }else{
                    if($connected_column_arr[1] == 'meta'){
                        // joining by a meta value
                        $tbl_alias = self::prepareSqlAlias( $join_rule['connected_column'].'_tbl' );
                        
                        if(!isset($this->_where_arr[$tbl_alias])){
                            $this->_where_arr[$tbl_alias] = array();
                        }
                        if( isset( $this->_from_arr[$tbl_alias] ) 
                                && count( $this->_from_arr ) > 1 ){
                            if( $join_rule['type'] == 'left' ){
                                $this->_left_join_arr[$tbl_alias] = $this->_from_arr[$tbl_alias];
                            }else{
                                $this->_inner_join_arr[$tbl_alias] = $this->_from_arr[$tbl_alias];
                            }
                            unset( $this->_from_arr[$tbl_alias] );
                        }elseif( !isset( $this->_inner_join_arr[$tbl_alias] ) &&  !isset( $this->_left_join_arr[$tbl_alias] ) ){
                                $rule = self::preparePostMetaSubquery( $tbl_alias, $connected_column_arr[0], $connected_column_arr[2] );
                                $this->_where_arr[$tbl_alias] = array();
                            if( $join_rule['type'] == 'left' ){
                                $this->_left_join_arr[$tbl_alias] = $rule;
                                if( isset($this->_from_arr['posts_'.$connected_column_arr[0]]) && count($this->_from_arr) > 1 ){
                                    $this->_left_join_arr['posts_'.$connected_column_arr[0]] = $this->_from_arr['posts_'.$connected_column_arr[0]];
                                    unset($this->_from_arr['posts_'.$connected_column_arr[0]]);
                                }else{
                                    $this->_left_join_arr['posts_'.$connected_column_arr[0]] = $wpdb->posts.'AS posts_'.$connected_column_arr[0];
                                }
                            }else{
                                $this->_inner_join_arr[$tbl_alias] = $rule;
                                if( isset($this->_from_arr['posts_'.$connected_column_arr[0]]) && count($this->_from_arr) > 1 ){
                                    $this->_inner_join_arr['posts_'.$connected_column_arr[0]] = $this->_from_arr['posts_'.$connected_column_arr[0]];
                                    unset($this->_from_arr['posts_'.$connected_column_arr[0]]);
                                }else{
                                    $this->_inner_join_arr['posts_'.$connected_column_arr[0]] = $wpdb->posts.'AS posts_'.$connected_column_arr[0];
                                }
                            }
                        }
                        $this->_where_arr[$tbl_alias][] = self::buildWhereCondition(
                                                            $tbl_alias.'.meta_value',
                                                            'eq',
                                                            'posts_' . $join_rule['initiator_post_type'] 
                                                            . '.' . $join_rule['initiator_column'],
                                                            false
                                                        );

                    }elseif($connected_column_arr[1] == 'taxonomy'){
                        // joining by taxonomy

                        $tbl_alias = self::prepareSqlAlias( $join_rule['connected_column'].'_tbl' );
                        
                        if(!isset($this->_where_arr[$tbl_alias])){
                            $this->_where_arr[$tbl_alias] = array();
                        }
                        if( isset( $this->_from_arr[$tbl_alias] ) 
                                && count( $this->_from_arr ) > 1 ){
                            if( $join_rule['type'] == 'left' ){
                                $this->_left_join_arr[$tbl_alias] = $this->_from_arr[$tbl_alias];
                            }else{
                                $this->_inner_join_arr[$tbl_alias] = $this->_from_arr[$tbl_alias];
                            }
                            unset( $this->_from_arr[$tbl_alias] );
                        }elseif( !isset( $this->_inner_join_arr[$tbl_alias] ) &&  !isset( $this->_left_join_arr[$tbl_alias] ) ){
                                $rule = self::preparePostTaxonomySubquery( $tbl_alias, $connected_column_arr[2] );

                                $this->_where_arr[$tbl_alias] = array();
                                $this->_where_arr[$tbl_alias][] = self::buildWhereCondition(
                                                                    $tbl_alias.'.name',
                                                                    'eq',
                                                                    $where_condition['value']                            
                                                                );
                                $this->_where_arr[$tbl_alias][] = self::buildWhereCondition(
                                                                    $tbl_alias.'.id',
                                                                    'eq',
                                                                    'posts_'.$where_column_arr[0].'.ID',
                                                                    false
                                                                );                            
                            if( $join_rule['type'] == 'left' ){
                                $this->_left_join_arr[$tbl_alias] = $rule;
                                if( isset($this->_from_arr['posts_'.$connected_column_arr[0]]) && count($this->_from_arr) > 1 ){
                                    $this->_left_join_arr['posts_'.$connected_column_arr[0]] = $this->_from_arr['posts_'.$connected_column_arr[0]];
                                    unset($this->_from_arr['posts_'.$connected_column_arr[0]]);
                                }else{
                                    $this->_left_join_arr['posts_'.$connected_column_arr[0]] = $wpdb->posts.'AS posts_'.$connected_column_arr[0];
                                }
                            }else{
                                $this->_inner_join_arr[$tbl_alias] = $rule;
                                if( isset($this->_from_arr['posts_'.$connected_column_arr[0]]) && count($this->_from_arr) > 1  ){
                                    $this->_inner_join_arr['posts_'.$connected_column_arr[0]] = $this->_from_arr['posts_'.$connected_column_arr[0]];
                                    unset($this->_from_arr['posts_'.$connected_column_arr[0]]);
                                }else{
                                    $this->_inner_join_arr['posts_'.$connected_column_arr[0]] = $wpdb->posts.'AS posts_'.$connected_column_arr[0];
                                }
                            }
                        }
                        $this->_where_arr[$tbl_alias][] = self::buildWhereCondition(
                                                            $tbl_alias.'.meta_value',
                                                            'eq',
                                                            'posts_' . $join_rule['initiator_post_type'] 
                                                            . '.' . $join_rule['initiator_column'],
                                                            false
                                                        );

                    }
                }
            }
         }
         
         /**
          * Prepare a GROUP BY block for MySQL based wpDataTables
          */
         private function _prepareMySQLGroupByBlock(){
             if( !$this->_has_groups ){
                 return;
             }
             
             foreach( $this->_table_data['grouping_rules'] as $grouping_rule ){
                 if( empty( $grouping_rule ) ){ continue; }
                 $this->_group_arr[] = $grouping_rule;                 
             }
             
         }
         
         /**
          * Prepare a GROUP BY block for WP based wpDataTables
          */
         private function _prepareWPGroupByBlock(){
             if( !$this->_has_groups ){
                 return;
             }
             
             foreach( $this->_table_data['grouping_rules'] AS $grouping_rule ){
                 if( empty( $grouping_rule ) ){ continue; }
                 $this->_group_arr[] = $this->_column_aliases[$grouping_rule];
             }
            
         }
         
         public static function preparePostTaxonomySubquery( $alias, $taxonomy ){
             global $wpdb;

             if(empty($alias) || empty($taxonomy)){ return ''; }
             
             $taxonomy_subquery = "(SELECT name, object_id as id";
             $taxonomy_subquery .= " FROM {$wpdb->terms} AS {$alias}_terms";
             $taxonomy_subquery .= " INNER JOIN {$wpdb->term_taxonomy} AS {$alias}_termtaxonomy";
             $taxonomy_subquery .= " ON {$alias}_termtaxonomy.term_id = {$alias}_terms.term_id ";
             $taxonomy_subquery .= " AND {$alias}_termtaxonomy.taxonomy = '{$taxonomy}'";
             $taxonomy_subquery .= " INNER JOIN {$wpdb->term_relationships} AS rel_{$alias}";
             $taxonomy_subquery .= "  ON {$alias}_termtaxonomy.term_taxonomy_id = rel_{$alias}.term_taxonomy_id";
             $taxonomy_subquery .= ") AS {$alias}";
             
             return $taxonomy_subquery;
             
         }
         
         public function setQuery( $query ){
            $this->_query = wpdatatables_sanitize_query( $query );
         }
         
         public function getQuery(){
             return $this->_query;
         }
         
         public function getQueryPreview(){
             
            if( get_option( 'wdtUseSeparateCon' ) ){
                $sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
                $result = $sql->getAssoc( $this->_query.' LIMIT 5', array() );
            }else{
                global $wpdb;
                $result = $wpdb->get_results( $this->_query.' LIMIT 5', ARRAY_A );
            }

            if( !empty( $result ) ){
                ob_start();
                include( WDT_TEMPLATE_PATH . 'constructor_preview.inc.php' );
                $ret_val = ob_get_contents();
                ob_end_clean();
            }else{
                $ret_val = __('No results found','wpdatatables');
            }
            return $ret_val;
         }
         
         /**
          * Returns the ending of the thumbnail URL string
          * @return type
          */
         public static function getThumbSizeString(){
             return '-' . get_option( 'thumbnail_size_w' ) . 'x' . get_option( 'thumbnail_size_h' ).'.';
         }
         
         /**
          * Generates a wpDataTable based on WP data query
          */
         public function generateWdtBasedOnQuery( $table_data ){
             global $wpdb;
             
            $table_data['query'] = wpdatatables_sanitize_query( $table_data['query'] );

            $table_array = array(
                                    'title' => '',
                                    'table_type' => 'mysql',
                                    'content' => '',
                                    'filtering' => 1,
                                    'filtering_form' => 0,
                                    'sorting' => 1,
                                    'fixed_layout' => 0,
                                    'responsive' => 0,
                                    'word_wrap' => 1,
                                    'tools' => 1,
                                    'display_length' => 10,
                                    'fixed_columns' => 0,
                                    'chart' => 'none',
                                    'chart_title' => '',
                                    'server_side' => 0,
                                    'editable' => 0,
                                    'editor_roles' => '',
                                    'mysql_table_name' => '',
                                    'hide_before_load' => 1
                                );

            $table_array['content'] = $table_data['query'];
            
            $res = wdt_try_generate_table( 'mysql', $table_array['content'] );
             
            $wpdb->insert($wpdb->prefix .'wpdatatables', $table_array);
            // get the newly generated table ID
            $table_id = $wpdb->insert_id;
            $res['table_id'] = $table_id;
            // creating default columns for the new table
            $res['columns'] = wdt_create_columns_from_table( $res['table'], $table_id );
            do_action( 'wpdatatables_after_save_table', $table_id );             
            
            return $res;
             
         }
         
         
         /**
          * Returns a list of tables in the chosen DB
          * @return array
          */
         public static function listMySQLTables(){
             
             $tables = array();
             
            if( get_option( 'wdtUseSeparateCon' ) ){
                try{
                    $sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
                } catch (Exception $ex) {
                    return $tables;
                }
                $result = $sql->getArray( 'SHOW TABLES', array() );
                if( empty( $result ) ){
                    return $tables;
                }
            }else{
                global $wpdb;
                $result = $wpdb->get_results( 'SHOW TABLES', ARRAY_N );
            }
            // Formatting the result to plain array
            foreach( $result as $row ){
                $tables[] = $row[0];
            }
            
            return $tables;
            
         }
         
         /**
          * Return a list of columns for the selected tables
          */
         public static function listMySQLColumns( $tables ){
             $columns = array( 'all_columns' => array(), 'sorted_columns' => array() );
             if( !empty( $tables ) ){
                 if( get_option( 'wdtUseSeparateCon' ) ){
                    try{
                        $sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
                    } catch (Exception $ex) {
                        return $columns;
                    }
                    foreach( $tables as $table ){
                        $columns['sorted_columns'][$table] = array();
                        $columns_query = "SHOW COLUMNS FROM {$table}";
                        $table_columns = $sql->getAssoc( $columns_query );
                        foreach( $table_columns as $table_column ){
                            $columns['sorted_columns'][$table][] = "{$table_column['Field']}";
                            $columns['all_columns'][] = "{$table}.{$table_column['Field']}";
                        }
                    }
                 }else{
                    global $wpdb;
                    foreach( $tables as $table ){
                        $columns['sorted_columns'][$table] = array();
                        $table_columns = $wpdb->get_results( "SHOW COLUMNS FROM {$table};", ARRAY_A ); 
                        foreach( $table_columns as $table_column ){
                            $columns['sorted_columns'][$table][] = "{$table}.{$table_column['Field']}";
                            $columns['all_columns'][] = "{$table}.{$table_column['Field']}";
                        }
                    }
                 }
             }
             
             return $columns;
         }
         
         /**
          * Generates a table based on the provided file and shows a preview
          */
         public function previewFileTable( $table_data ){
             
            if( !empty( $table_data['file'] ) ){
                $xls_url = urldecode( $table_data['file'] );
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $xls_url = str_replace( site_url(), str_replace('\\', '/', ABSPATH), $xls_url ); 
                }else{
                    $xls_url = str_replace( site_url(), ABSPATH, $xls_url );
                }
            }else{
                return array( 'result' => 'error', 'message' => __( 'Empty file', 'wpdatatables' ) );
            }

             if( strpos( strtolower($xls_url), 'https://docs.google.com/spreadsheets' ) !== false ){
                 // Preview from Google Spreadsheet
                 $namedDataArray = WDTTools::extractGoogleSpreadsheetArray( $xls_url );
                 if( !empty( $namedDataArray ) ){
                     $headingsArray = array_keys( $namedDataArray[0] );
                     $namedDataArray = array_slice( $namedDataArray, 0, 5 );
                 }else{
                     return  array(
                                 'result' => 'error',
                                 'message' => __(
                                                    'Could not read Google spreadsheet, please check if the URL is correct and the spreadsheet is published to everyone',
                                                    'wpdatatables'
                                                )
                             );
                 }
             }else{
                 require_once(WDT_ROOT_PATH.'/lib/phpExcel/PHPExcel.php');
                 $objPHPExcel = new PHPExcel();
                 if(strpos(strtolower($xls_url), '.xlsx')){
                     $objReader = new PHPExcel_Reader_Excel2007();
                     $objReader->setReadDataOnly(true);
                 }elseif(strpos(strtolower($xls_url), '.xls')){
                     $objReader = new PHPExcel_Reader_Excel5();
                     $objReader->setReadDataOnly(true);
                 }elseif(strpos(strtolower($xls_url), '.ods')){
                     $objReader = new PHPExcel_Reader_OOCalc();
                     $objReader->setReadDataOnly(true);
                 }elseif(strpos(strtolower($xls_url), '.csv')){
                     $objReader = new PHPExcel_Reader_CSV();
                 }else{
                     return array( 'result' => 'error', 'message' => __( 'Could not read input file!', 'wpdatatables' ) );
                 }

                 $objPHPExcel = $objReader->load($xls_url);
                 $objWorksheet = $objPHPExcel->getActiveSheet();
                 $highestRow = $objWorksheet->getHighestRow();
                 $highestRow = $highestRow > 5 ? 5 : $highestRow;
                 $highestColumn = $objWorksheet->getHighestColumn();

                 $headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
                 $headingsArray = $headingsArray[1];

                 $r = -1;
                 $namedDataArray = array();
                 for ($row = 2; $row <= $highestRow; ++$row) {
                     $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
                     if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                         ++$r;
                         foreach($headingsArray as $dataColumnIndex => $dataColumnHeading) {
                             $namedDataArray[$r][$dataColumnHeading] = $dataRow[$row][$dataColumnIndex];
                             if(WDT_DETECT_DATES_IN_EXCEL){
                                 $cellID = $dataColumnIndex.$row;
                                 if(PHPExcel_Shared_Date::isDateTime($objPHPExcel->getActiveSheet()->getCell($cellID))){
                                     $namedDataArray[$r][$dataColumnHeading] = PHPExcel_Shared_Date::ExcelToPHP($dataRow[$row][$dataColumnIndex]);
                                 }
                             }
                         }
                     }
                 }
             }

            $columnTypeArray = WDTTools::detectColumnDataTypes( $namedDataArray, $headingsArray );
            $possibleColumnTypes = WDTTools::getPossibleColumnTypes();
            
            $ret_val = '';
            
            if( !empty( $namedDataArray ) ){
                ob_start();
                include( WDT_TEMPLATE_PATH . 'constructor_file_preview.inc.php' );
                $ret_val = ob_get_contents();
                ob_end_clean();
            }
            
            return array( 'result' => 'success', 'message' => $ret_val );
             
         }
         
         /**
          * Reads the data from file in the DB and generates a wpDataTable
          */
         public function readFileData( $table_data ){
             
            $columnTypes = array();
             
            if( !empty( $table_data['file'] ) ){
                $xls_url = urldecode( $table_data['file'] );
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $xls_url = str_replace( site_url(), str_replace('\\', '/', ABSPATH), $xls_url ); 
                }else{
                    $xls_url = str_replace( site_url(), ABSPATH, $xls_url );
                }
            }else{
                return _( 'Empty file', 'wpdatatables' );
            }
            
            for( $i=0; $i < count($table_data['columns']); $i++ ){
                if( $table_data['columns'][$i]['orig_header'] == '%%NEW_COLUMN%%' ){
                    $table_data['columns'][$i]['orig_header'] = 'column'.$i;
                }
                $columnTypes[$table_data['columns'][$i]['orig_header']] = $table_data['columns'][$i]['type'];
            }
            
            $this->_id = $this->generateManualTable( $table_data );

             if ( strpos( strtolower( $xls_url ), 'https://docs.google.com/spreadsheets' ) !== false  ) {
                 $table_type = 'google';
                 $namedDataArray = WDTTools::extractGoogleSpreadsheetArray( $xls_url );
                 $headingsArray = array_keys( $namedDataArray[0] );
                 $highestRow = count( $namedDataArray ) - 1;
             } else {
                 $table_type = 'excel';
                 require_once(WDT_ROOT_PATH . '/lib/phpExcel/PHPExcel.php');
                 $objPHPExcel = new PHPExcel();
                 if (strpos(strtolower($xls_url), '.xlsx')) {
                     $objReader = new PHPExcel_Reader_Excel2007();
                     $objReader->setReadDataOnly(true);
                 } elseif (strpos(strtolower($xls_url), '.xls')) {
                     $objReader = new PHPExcel_Reader_Excel5();
                     $objReader->setReadDataOnly(true);
                 } elseif (strpos(strtolower($xls_url), '.ods')) {
                     $objReader = new PHPExcel_Reader_OOCalc();
                     $objReader->setReadDataOnly(true);
                 } elseif (strpos(strtolower($xls_url), '.csv')) {
                     $objReader = new PHPExcel_Reader_CSV();
                 } else {
                     return _('File format not supported!', 'wpdatatables');
                 }

                 $objPHPExcel = $objReader->load($xls_url);
                 $objWorksheet = $objPHPExcel->getActiveSheet();
                 $highestRow = $objWorksheet->getHighestRow();
                 $highestColumn = $objWorksheet->getHighestColumn();

                 $headingsArray = $objWorksheet->rangeToArray( 'A1:'.$highestColumn.'1',null, true, true, true );
                 $headingsArray = $headingsArray[1];
             }

            $r = -1;
            
            $insertArray = array();
            
            // Insert statement default beginning
             $insert_statement_beginning = "INSERT INTO "
                                            . $this->_name . " ("
                                            . implode(
                                                        ', ',
                                                        array_map(
                                                            function( $header ){
                                                                    return "`{$header}`";
                                                            },
                                                            array_values( $this->_column_headers )
                                                        )
                                                    )
                                            .") ";
             $insert_blocks = array();
            
            for ($row = 0; $row <= $highestRow; ++$row) {

                if( ( $row <= 1 ) && ( $table_type == 'excel' ) ){ continue; }
                
                // Set all cells in the row to their defaults
                foreach( $table_data['columns'] as $column ){
                    $insertArray[ $this->_column_headers[ $column['orig_header'] ] ] = "'" . esc_sql( $column['default_value'] ) . "'";
                }

                if ( $table_type == 'google' ) {
                    foreach( $headingsArray as $dataColumnIndex => $dataColumnHeading ) {
                        if( !in_array( $dataColumnHeading, array_keys( $this->_column_headers ) ) ) {
                            continue;
                        }

                        if( $columnTypes[$dataColumnHeading] != 'date' ) {
                            $insertArray[$this->_column_headers[$dataColumnHeading]] = "'" . esc_sql($namedDataArray[$row][$dataColumnHeading]) . "'";
                        } else {
                            $date = strtotime(str_replace('/', '-', $namedDataArray[$row][$dataColumnHeading]));
                            $insertArray[$this->_column_headers[$dataColumnHeading]] = "'" . date('Y-m-d', $date) . "'";
                        }
                    }
                } else {
                    $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
                    if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                        ++$r;
                        foreach ($headingsArray as $dataColumnIndex => $dataColumnHeading) {

                            if (!in_array($dataColumnHeading, array_keys($this->_column_headers))) {
                                continue;
                            }

                            if ($columnTypes[$dataColumnHeading] != 'date') {
                                $insertArray[$this->_column_headers[$dataColumnHeading]] = "'" . esc_sql($dataRow[$row][$dataColumnIndex]) . "'";
                            } else {
                                if ($objReader instanceof PHPExcel_Reader_CSV) {
                                    $date = strtotime(str_replace('/', '-', $dataRow[$row][$dataColumnIndex]));
                                } else {
                                    $date = esc_sql(PHPExcel_Shared_Date::ExcelToPHP($dataRow[$row][$dataColumnIndex]));
                                }
                                $insertArray[$this->_column_headers[$dataColumnHeading]] = "'" . date('Y-m-d', $date) . "'";

                            }

                        }
                    }
                }
                
                $insert_blocks[] = '(' . implode( ', ', $insertArray ) . ')';
                
                if( $row % 100 == 0 ){
                    $this->insertRowsChunk( $insert_statement_beginning, $insert_blocks );
                    $insert_blocks = array();
                }
                
            }
            
            $this->insertRowsChunk( $insert_statement_beginning, $insert_blocks );
            
         }
         
         /**
          * Inserts a blocks of rows read from file
          */
         private function insertRowsChunk( $insert_statement_beginning, $insert_blocks ){
             global $wpdb;
             
                if( count( $insert_blocks ) > 0 ){
                    $insert_statement = $insert_statement_beginning . " VALUES " . implode( ', ', $insert_blocks );
                    if(get_option('wdtUseSeparateCon')){
                            // External DB
                            $this->_db->doQuery( $insert_statement, array() );
                    }else{
                            $wpdb->query( $insert_statement );
                    }
                }
         }
         
         /**
          * Delete a column from a manually generated table
          */
         public static function deleteManualColumn( $table_id, $column_name ){
             global $wpdb;
             
             $table_data = wdt_get_table_by_id( $table_id );
             $existing_columns = wdt_get_columns_by_table_id( $table_id );
             
             $delete_column_id = 0;
             $delete_column_index = 0;
             
             foreach( $existing_columns as $existing_column ){
                 if( $existing_column->orig_header == $column_name ){
                     $delete_column_index = $existing_column->pos;
                     $delete_column_id = $existing_column->id;
                     break;
                 }
             }
             
             $drop_statement = "ALTER TABLE {$table_data['mysql_table_name']} DROP COLUMN {$column_name}";
             
             // First delete the column from the MySQL table
             if(get_option('wdtUseSeparateCon')){
                // External DB
                $Sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
                $Sql->doQuery( $drop_statement, array() );
             }else{
                $wpdb->query( $drop_statement );
             }
             
             if( $delete_column_id != 0 ){
                // Delete the column from wp_wpdatatable_columns
                $wpdb->delete( 
                           $wpdb->prefix.'wpdatatables_columns', 
                           array( 'id' => $delete_column_id ) 
                        );

                // Update the order of other columns
                $update_statement = "UPDATE ".$wpdb->prefix."wpdatatables_columns 
                                      SET pos = pos - 1 
                                      WHERE table_id = {$table_id} 
                                          AND pos >= ".(int) $delete_column_index;
                $wpdb->query( $update_statement );
                
             }
             
         }
         
         /**
          * Add a new column to manually generated table
          */
         public static function addNewManualColumn( $table_id, $column_data ){
             global $wpdb;
             
             $table_data = wdt_get_table_by_id( $table_id );
             $existing_columns = wdt_get_columns_by_table_id( $table_id );
             
             $existing_headers = array();
             $column_index = 0;
             foreach( $existing_columns as $existing_column ){
                 $existing_headers[] = $existing_column->orig_header;
                 if( $existing_column->orig_header == $column_data['insert_after'] ){
                     $column_index = $existing_column->pos + 1;
                 }
             }
             
             $new_column_mysql_name = self::generateMySQLColumnName( $column_data['name'], $existing_headers );
             $columnProperties = self::defineColumnProperties( $new_column_mysql_name, $column_data['type'] );
             
             // Add the column to MySQL table
             $alter_table_statement = "ALTER TABLE {$table_data['mysql_table_name']} 
                                        ADD COLUMN {$columnProperties['create_block']} ";
             if( $column_data['insert_after'] == '%%beginning%%' ){
                $alter_table_statement .= " FIRST";
             }else if( $column_data['insert_after'] != '%%end%%' ){
                $alter_table_statement .= " AFTER `{$column_data['insert_after']}`";
             }
             
                // Call the create statement on WPDB or on external DB if it is defined
             if(get_option('wdtUseSeparateCon')){
                // External DB
               $Sql = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
               $Sql->doQuery( $alter_table_statement, array() );
             }else{
                $wpdb->query( $alter_table_statement );
             }
                
             // Fill in with default value if requested
             if( $column_data['fill_default'] == 1 ){
                 $update_fill_default = "UPDATE {$table_data['mysql_table_name']} 
                                            SET `{$new_column_mysql_name}` = '{$column_data['default_value']}' 
                                            WHERE 1";
                if(get_option('wdtUseSeparateCon')){
                   // External DB
                   $this->_db->doQuery( $update_fill_default, array() );
                }else{
                   $wpdb->query( $update_fill_default );
                }
             }
             
             // Move the existing columns if necessary
             if( $column_data['insert_after'] == '%%end%%' ){
                 $column_index = count ($existing_columns );
             }else{
                 $update_statement = "UPDATE ".$wpdb->prefix."wpdatatables_columns 
                                        SET pos = pos + 1 
                                        WHERE table_id = {$table_id} 
                                            AND pos >= ".(int) $column_index;
                 $wpdb->query( $update_statement );
             }
             // Add the column to wp_wpdatatables_columns
            $wpdb->insert(
                    $wpdb->prefix."wpdatatables_columns",
                    array(
                            'table_id' => $table_id,
                            'orig_header' => $new_column_mysql_name,
                            'display_header' => $column_data['name'],
                            'filter_type' => $columnProperties['filter_type'],
                            'column_type' => $columnProperties['column_type'],
                            'pos' => $column_index,
                            'possible_values' => str_replace( ',,;,|', '|', $column_data['possible_values'] ),
                            'default_value' => $column_data['default_value'],
                            'input_type' => $columnProperties['editor_type']
                    )
            );
            
             
         }
         
 }

?>