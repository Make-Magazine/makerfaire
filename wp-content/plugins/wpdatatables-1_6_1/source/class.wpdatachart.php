<?php
/**
 * Chart engine of wpDataTables plugin
 */

class WPDataChart {
    
   private $_id = NULL;
   private $_wpdatatable_id = NULL;
   private $_title = '';
   private $_engine = '';
   private $_type = '';
   private $_range_type = 'all';
   private $_selected_columns = array();
   private $_row_range = array();
   private $_follow_filtering = false;
   private $_wpdatatable = NULL;
   private $_show_title = true;
   private $_width = 400;
   private $_height = 400;
   private $_show_grid = true;
   private $_show_legend = true;
   private $_user_defined_series_data = array();
   private $_render_data = NULL;
   private $_highcharts_render_data = array();
   private $_type_counters;
   
   private $_axes = array(
       'major' => array(
           'label' => ''
       ),
       'minor' => array(
           'label' => ''
       )
   );
   private $_series = array(
       
   );
   
   public function __construct(){
       
   }
   
   public function setId( $id ){
       $this->_id = $id;
   }
   
   public function getId(){
       return $this->_id;
   }
   
   public function setShowTitle( $show_title ){
       $this->_show_title = (bool) $show_title;
   }
   
   public function getShowTitle(){
       return $this->_show_title;
   }
   
   public function setWidth( $width ){
       $this->_width = $width;
   }
   
   public function getWidth(){
       return $this->_width;
   }
   
   public function setHeight( $height ){
       $this->_height = $height;
   }
   
   public function getHeight(){
        return $this->_height;
   }
   
   public function setShowGrid( $show_grid ){
       $this->_show_grid = (bool) $show_grid;
   }
   
   public function getShowGrid(){
       return $this->_show_grid;
   }
   
   public function setShowLegend( $show_legend ){
       $this->_show_legend = (bool) $show_legend;
   }
   
   public function getShowLegend(){
       return $this->_show_legend;
   }
   
   public function setMajorAxisLabel( $label ){
       $this->_axes['major']['label'] = $label;
   }
   
   public function getMajorAxisLabel(){
       return $this->_axes['major']['label'];
   }
   
   public function setMinorAxisLabel( $label ){
       $this->_axes['minor']['label'] = $label;
   }
   
   public function getMinorAxisLabel(){
       return $this->_axes['minor']['label'];
   }
   
   public function setUserDefinedSeriesData( $series_data ){
       if( is_array( $series_data ) ){
           $this->_user_defined_series_data = $series_data;
       }
   }
   
   public function getUserDefinedSeriesData(){
       return $this->_user_defined_series_data;
   }
   
   public function setFollowFiltering( $follow_filtering ){
       $this->_follow_filtering = $follow_filtering;
   }
   
   public function getFollowFiltering(){
       return $this->_follow_filtering;
   }
   
   public function setEngine( $engine ){
       $this->_engine = $engine;
   }
   
   public function getEngine(){
       return $this->_engine;
   }
   
   public function setType( $type ){
       $this->_type = $type;
   }
   
   public function getType(){
       return $this->_type;
   }
   
   public function setTitle( $title ){
       $this->_title = $title;
   }
   
   public function getTitle(){
       return $this->_title;
   }
   
   public function setRowRange( $row_range ){
       $this->_row_range = $row_range;
   }
   
   public function getRowRange(){
       return $this->_row_range;
   }
   
   public function setSelectedColumns( $selected_columns ){
       $this->_selected_columns = $selected_columns;
   }
   
   public function getSelectedColumns(){
       return $this->_selected_columns;
   }
   
   public function setwpDataTableId( $wdt_id ){
       $this->_wpdatatable_id = $wdt_id;
   }
   
   public function getwpDataTableId(){
       return $this->_wpdatatable_id;
   }

   public function setRangeType( $range_type ){
       $this->_range_type = $range_type;
   }
   
   public function getRangeType(){
       return $this->_range_type;
   }
   
   public static function factory( $constructedChartData, $loadFromDB = true ){
       $chartObj = new self();
       
       if( isset( $constructedChartData['chart_id'] ) ){
            $chartObj->setId( (int) $constructedChartData['chart_id'] );
            if( $loadFromDB ){
                $chartObj->loadFromDB();
                $chartObj->prepareData();
                $chartObj->shiftStringColumnUp();
            }
       }
       
       // Main data (steps 1-3 of chart constructor)
       $chartObj->setwpDataTableId( $constructedChartData['wpdatatable_id'] );
       $chartObj->setTitle( $constructedChartData['chart_title'] );
       $chartObj->setEngine( $constructedChartData['chart_engine'] );
       $chartObj->setType( $constructedChartData['chart_type'] );
       $chartObj->setSelectedColumns( $constructedChartData['selected_columns'] );
       $chartObj->setRangeType( $constructedChartData['range_type'] );
       if( isset( $constructedChartData['range_data'] ) ){
         $chartObj->setRowRange( $constructedChartData['range_data'] );
       }
       $chartObj->setFollowFiltering( (bool) $constructedChartData['follow_filtering'] );
       
       // Render data (step 4 or chart constructor)
       $chartObj->setShowTitle( WDTTools::defineDefaultValue( $constructedChartData, 'show_title', '' ) );
       $chartObj->setWidth( WDTTools::defineDefaultValue( $constructedChartData, 'width', 400 ) );
       $chartObj->setHeight( WDTTools::defineDefaultValue( $constructedChartData, 'height', 400 ) );
       $chartObj->setShowGrid( WDTTools::defineDefaultValue( $constructedChartData, 'show_grid', true ) );
       $chartObj->setShowLegend( WDTTools::defineDefaultValue( $constructedChartData, 'show_legend', true ) );
       $chartObj->setMajorAxisLabel( WDTTools::defineDefaultValue( $constructedChartData, 'horizontal_axis_label', '' ) );
       $chartObj->setMinorAxisLabel( WDTTools::defineDefaultValue( $constructedChartData, 'vertical_axis_label', '' ) );
       
       if( !empty( $constructedChartData['series_data'] ) ){
           $chartObj->setUserDefinedSeriesData( $constructedChartData['series_data'] );
       }
       
       $chartObj->loadChildWPDataTable();
       
       return $chartObj;
   }
   
   public function loadChildWPDataTable(){
       if( empty( $this->_wpdatatable_id ) ) { return false; }
       $this->_wpdatatable = wdt_get_wpdatatable( $this->_wpdatatable_id, empty( $this->_follow_filtering ) );
   }
   
   public function shiftStringColumnUp(){
       /**
        * Check if the string column is not in the beginning and move it up
        */
       if( count( $this->_render_data['columns'] ) > 1 ){
            $shiftNeeded = false;
            $shiftIndex = 0;
            for( $i = 1; $i < count( $this->_render_data['columns'] ); $i++ ){
                if( $this->_render_data['columns'][$i]['type'] == 'string' ){
                    $shiftNeeded = true;
                    $shiftIndex = $i;
                    break;
                }
            }
            
            if( $shiftNeeded ){
                // Shift columns
                $strColumn = $this->_render_data['columns'][$shiftIndex];
                unset( $this->_render_data['columns'][$shiftIndex] );
                array_unshift( $this->_render_data['columns'], $strColumn );
                // Shift rows
                for( $j=0; $j<count( $this->_render_data['rows'] ); $j++ ){
                    $strCell = $this->_render_data['rows'][$j][$shiftIndex];
                    unset( $this->_render_data['rows'][$j][$shiftIndex] );
                    array_unshift( $this->_render_data['rows'][$j], $strCell );
                }
                // Shift column indexes
                if( isset( $this->_render_data['column_indexes'] ) ){
                    $shiftedIndex = $this->_render_data['column_indexes'][$shiftIndex];
                    unset( $this->_render_data['column_indexes'][$shiftIndex] );
                    array_unshift( $this->_render_data['column_indexes'], $shiftedIndex );
                }
            }
       }
       
       // Format axes
        $this->_render_data['axes']['major'] = array(
            'type' => $this->_render_data['columns'][0]['type'],
            'label' => !empty( $this->_render_data['hAxis']['title'] ) ?
                $this->_render_data['hAxis']['title'] : $this->_render_data['columns'][0]['label']
        );
        $this->_render_data['axes']['minor'] = array(
            'type' => $this->_render_data['columns'][1]['type'],
            'label' => !empty( $this->_render_data['vAxis']['title'] ) ?
                $this->_render_data['vAxis']['title'] : ''
        );

        // Get all series names
        if( empty( $this->_render_data['series'] ) ){
            for( $i = 1; $i < count( $this->_render_data['columns'] ); $i++ ){
                $this->_render_data['series'][] = array( 
                    'label' => $this->_render_data['columns'][$i]['label'],
                    'color' => '',
                    'orig_header' => $this->_render_data['columns'][$i]['orig_header']
                );
            }        
        }
       
   }
  
   public function prepareSeriesData(){
       // Init render data if it is empty
       if( empty($this->_render_data) ){
            $this->_render_data = array(
                'columns' => array(),
                'rows' => array(),
                'axes' => array(),
                'options' => array(
                    'title' => $this->_show_title ? $this->_title : '',
                    'series' => array(),
                    'width' => $this->_width,
                    'height' => $this->_height
                ),
                'vAxis' => array(

                ),
                'hAxis' => array(

                ),
                'errors' => array(),
                'series' => array()
            );
       }
        
       $this->_type_counters = array(
           'date' => 0,
           'string' => 0,
           'number' => 0
       );
       
       // Define columns
       foreach( $this->getSelectedColumns() as $columnKey ){
           $columnType = $this->_wpdatatable->getColumn( $columnKey )->getGoogleChartColumnType();
           $this->_render_data['columns'][] = array(
                    'type' =>  $columnType,
                    'label' =>  isset( $this->_user_defined_series_data[$columnKey]['label'] ) ? 
                                    $this->_user_defined_series_data[$columnKey]['label'] : $this->_wpdatatable->getColumn( $columnKey )->getTitle(),
                    'orig_header' => $columnKey
                );
           $this->_type_counters[$columnType]++;
       }
       
       // Define axes titles
       if( isset( $this->_axes['major']['label'] ) ){
           $this->_render_data['options']['hAxis']['title'] = $this->_axes['major']['label'];
       }
       if( isset( $this->_axes['minor']['label'] ) ){
           $this->_render_data['options']['vAxis']['title'] = $this->_axes['minor']['label'];
       }
       
       // Define series colors
       if( !empty( $this->_user_defined_series_data ) ){
           $seriesIndex = 0;
           foreach( $this->_user_defined_series_data as $series_data ){
                if( !empty( $series_data['color'] ) ){
                    $this->_render_data['options']['series'][(int) $seriesIndex] = array(
                        'color' => $series_data['color']
                    );
                }
                $seriesIndex++;
           }
       }
       
       // Define grid settings
       if( !$this->_show_grid ){
           if( !isset( $this->_render_data['options']['hAxis'] ) ){
               $this->_render_data['options']['hAxis'] = array();
           }
           $this->_render_data['options']['hAxis']['gridlines'] = array(
               'color' => 'transparent'
           );
           if( !isset( $this->_render_data['options']['vAxis'] ) ){
               $this->_render_data['options']['vAxis'] = array();
           }
           $this->_render_data['options']['vAxis']['gridlines'] = array(
               'color' => 'transparent'
           );
        }
       
        // Define legend settings
        if( !$this->_show_legend ){
            $this->_render_data['options']['legend'] = 'none';
        }

       // Detect errors
       if( $this->_type_counters['string'] > 1 ){
           $this->_render_data['errors'][] = __( 'Only one column can be of type String', 'wpdatatables' );
       }
       if( ( $this->_type_counters['number'] > 1 ) && ( $this->_type_counters['date'] > 1 ) ){
           $this->_render_data['errors'][] = __( 'You are mixing data types (several date axes and several number)', 'wpdatatables' );
       }
       
   }
   
   
   /**
    * Prepares the data for Google charts format
    */
   public function prepareData(){
       
       // Prepare series and columns
       if( empty( $this->_render_data['columns'] ) ){
            $this->prepareSeriesData();
       }
       
       $dateFormat = ( $this->getEngine() == 'google' ) ? DateTime::RFC2822 : get_option( 'wdtDateFormat' );
       
       // The data itself
       if( empty( $this->_render_data['rows'] ) ){
            if( $this->getRangeType() == 'all_rows' ){
                 foreach( $this->_wpdatatable->getDataRows() as $row ){
                     $return_data_row = array( );
                     foreach( $this->getSelectedColumns() as $columnKey ){
                          $dataType = $this->_wpdatatable->getColumn( $columnKey )->getDataType();
                          switch( $dataType ){
                              case 'date':
                                  $return_data_row[] = date( 
                                          $dateFormat, 
                                          strtotime( 
                                                     str_replace( '/', '-', $row[$columnKey] )
                                                 )
                                          );
                                  break;
                              case 'int':
                                  $return_data_row[] = (float) $row[$columnKey];
                                  break;
                              case 'float':
                                  $return_data_row[] = (float) $row[$columnKey];
                                  break;
                              case 'string':
                              default:
                                 $return_data_row[] = $row[$columnKey];
                                 break;
                          }
                     }
                     $this->_render_data['rows'][] = $return_data_row;
                 }
            }else{
                 foreach( $this->_row_range as $rowIndex ){
                     $return_data_row = array( );
                      foreach( $this->getSelectedColumns() as $columnKey ){

                          $dataType = $this->_wpdatatable->getColumn( $columnKey )->getDataType();
                          switch( $dataType ){
                              case 'date':
                                  $return_data_row[] = date( 
                                          $dateFormat,
                                          strtotime( 
                                                     str_replace( '/', '-', $this->_wpdatatable->getCell( $columnKey, $rowIndex ) )
                                                  )
                                          );
                                  break;
                              case 'int':
                                  $return_data_row[] = (float) $this->_wpdatatable->getCell( $columnKey, $rowIndex );
                                  break;
                              case 'float':
                                  $return_data_row[] = (float) $this->_wpdatatable->getCell( $columnKey, $rowIndex );
                                  break;
                              case 'string':
                              default:
                                 $return_data_row[] = $this->_wpdatatable->getCell( $columnKey, $rowIndex );
                                 break;
                          }

                      }
                     $this->_render_data['rows'][] = $return_data_row;
                 }
            }
           
       }
       
      $this->_render_data['type'] = $this->_type;
      return $this->_render_data;  
    }
   
   public function getAxesAndSeries(){
       if( empty( $this->_render_data['columns'] ) ){
            $this->prepareSeriesData();
            $this->shiftStringColumnUp();
       }
       return $this->_render_data;
   }
   
   public function returnGoogleChartData(){
        $this->prepareData();
        $this->shiftStringColumnUp();
        return $this->_render_data;
   }
   
   public function prepareHighchartsRender(){
       $highchartsRender = array( 
                'title' => array(
                    'text' => $this->_show_title ? $this->getTitle() : ''
                ),
                'series' => array(), 
                'xAxis' => array()
           );
       if( !in_array( 
               $this->_type, 
               array( 
                   'highcharts_pie_chart',
                   'highcharts_pie_with_gradient_chart',
                   'highcharts_donut_chart',
                   'highcharts_3d_pie_chart',
                   'highcharts_3d_donut_chart',
                   'highcharts_angular_gauge_chart',
                   'highcharts_solid_gauge_chart'
                   ) 
               ) ){
            for( $i=0; $i < count( $this->_render_data['columns'] ); $i++ ){
                if( $i == 0 ){
                 $highchartsRender['xAxis']['categories'] = array();
                }else{
                    
                 $seriesEntry = array(
                     'name' => $this->_render_data['series'][$i-1]['label'],
                     'color' => isset( $this->_render_data['options']['series'][$i-1] ) ? 
                            $this->_render_data['options']['series'][$i-1]['color'] : '',
                     'data' => array()
                 );
                }
                foreach( $this->_render_data['rows'] as $row ){
                     if( $i == 0 ){
                      $highchartsRender['xAxis']['categories'][] = $row[$i];
                     }else{
                      $seriesEntry['data'][] = $row[$i];
                     }
                }
                if( $i != 0 ){
                    $highchartsRender['series'][] = $seriesEntry;
                }
            }
       }else{
           if( 
                in_array(
                            $this->_type,
                            array(
                                'highcharts_pie_chart',
                                'highcharts_pie_with_gradient_chart',
                                'highcharts_donut_chart',
                                'highcharts_3d_pie_chart',
                                'highcharts_3d_donut_chart'
                            )
                        ) 
                   ){
                       $seriesEntry = array(
                           'type' => 'pie',
                           'data' => array()
                       );
                    $highchartsRender['series'] = array(
                        array(
                            'type' => 'pie',
                            'name' => $this->_render_data['columns'][1]['label'],
                            'data' => $this->_render_data['rows']
                        )
                    );
                    unset( $highchartsRender['xAxis'] );
           }
       }
       $this->_highcharts_render_data['options'] = $highchartsRender;
       $this->_highcharts_render_data['width'] = $this->getWidth();
       $this->_highcharts_render_data['height'] = $this->getHeight();
       $this->_highcharts_render_data['type'] = $this->getType();
       if( !empty( $this->_render_data['options']['hAxis']['title'] ) ){
           $this->_highcharts_render_data['options']['xAxis']['title']['text'] =  $this->_render_data['options']['hAxis']['title'];
       }
       if( !empty( $this->_render_data['options']['vAxis']['title'] ) ){
           $this->_highcharts_render_data['options']['yAxis']['title']['text'] =  $this->_render_data['options']['vAxis']['title'];
       }
       if( $this->_follow_filtering ){
           if( isset( $this->_render_data['column_indexes'] ) ){
                $this->_highcharts_render_data['column_indexes'] = $this->_render_data['column_indexes'];
           }
       }
       if( !$this->_show_grid ){
            $this->_highcharts_render_data['options']['xAxis']['lineWidth'] = 0;
            $this->_highcharts_render_data['options']['xAxis']['minorGridLineWidth'] = 0;
            $this->_highcharts_render_data['options']['xAxis']['lineColor'] = 'transparent';
            $this->_highcharts_render_data['options']['xAxis']['minorTickLength'] = 0;
            $this->_highcharts_render_data['options']['xAxis']['tickLength'] = 0;
            $this->_highcharts_render_data['options']['yAxis']['lineWidth'] = 0;
            $this->_highcharts_render_data['options']['yAxis']['gridLineWidth'] = 0;
            $this->_highcharts_render_data['options']['yAxis']['minorGridLineWidth'] = 0;
            $this->_highcharts_render_data['options']['yAxis']['lineColor'] = 'transparent';
            $this->_highcharts_render_data['options']['yAxis']['labels'] = array( 'enabled' => false );
            $this->_highcharts_render_data['options']['yAxis']['minorTickLength'] = 0;
            $this->_highcharts_render_data['options']['yAxis']['tickLength'] = 0;
       }
       if( !$this->_show_legend ){
           $this->_highcharts_render_data['options']['legend']['enabled'] = false;
       }
   }
   
   public function returnHighChartsData(){
       $this->prepareData();
       $this->shiftStringColumnUp();
       $this->prepareHighchartsRender();
       return $this->_highcharts_render_data;
   }
   
   public function returnData(){
       if( $this->getEngine() == 'google' ){
           return $this->returnGoogleChartData();
       }else{
           return $this->returnHighChartsData();
       }
   }
    
   
   /**
    * Saves the chart data to DB
    * @global WPDB $wpdb
    */
   public function save(){
       global $wpdb;
       
        $this->prepareSeriesData();
        $this->shiftStringColumnUp();
           
        $render_data = array(
            'selected_columns' => $this->getSelectedColumns(),
            'range_type' => $this->getRangeType(),
            'row_range' => $this->getRowRange(),
            'follow_filtering' => $this->getFollowFiltering(),
            'render_data' => $this->_render_data,
            'show_title' => $this->_show_title,
            'show_legend' => $this->_show_legend,
            'show_grid' => $this->_show_grid
        );
        
       
       if( empty( $this->_id ) ){
           // This is a new chart
           
           $wpdb->insert(
                   $wpdb->prefix."wpdatacharts",
                   array(
                       'wpdatatable_id' => $this->_wpdatatable_id,
                       'title' => $this->_title,
                       'engine' => $this->_engine,
                       'type' => $this->_type,
                       'json_render_data' => json_encode( $render_data )
                   )
            );
           
           $this->_id = $wpdb->insert_id;
           
       }else{
           // Updating the chart
           $wpdb->update(
                   $wpdb->prefix."wpdatacharts",
                   array(
                       'wpdatatable_id' => $this->_wpdatatable_id,
                       'title' => $this->_title,
                       'engine' => $this->_engine,
                       'type' => $this->_type,
                       'json_render_data' => json_encode( $render_data )
                   ),
                   array(
                       'id' => $this->_id
                   )
            );
           
       }
       
   }
   
   public function getColumnIndexes(){
       
       foreach( $this->getSelectedColumns() as $columnKey ){
           $this->_render_data['column_indexes'][] = $this->_wpdatatable->getColumnHeaderOffset( $columnKey );
       }
   }
   
   /**
    * Return the shortcode
    */
   public function getShortCode(){
       if( !empty( $this->_id ) ){
           return '[wpdatachart id='.$this->_id.']';
       }else{
           return '';
       }
   }
   
   /**
    * Load from DB
    */
   public function loadFromDB(){
       global $wpdb;
       
       if( empty( $this->_id ) ){
           return false;
       }
       
       // Load json data from DB
       $chartQuery = $wpdb->prepare( 
                                "SELECT * 
                                    FROM ".$wpdb->prefix."wpdatacharts 
                                    WHERE id = %d", 
                                    $this->_id 
                            );
       $chartData = $wpdb->get_row( $chartQuery );
       
       $this->setTitle( $chartData->title );
       $this->setEngine( $chartData->engine );
       $this->setwpDataTableId( $chartData->wpdatatable_id );
       $this->setType( $chartData->type );
       
       $renderData = json_decode( $chartData->json_render_data, true );
       $this->_render_data = $renderData['render_data'];
       $this->setSelectedColumns( $renderData['selected_columns'] );
       $this->setFollowFiltering( $renderData['follow_filtering'] );
       $this->setRangeType( $renderData['range_type'] );
       $this->setRowRange( $renderData['row_range'] );
       $this->setShowGrid( isset( $renderData['show_grid'] ) ? $renderData['show_grid'] : false );
       $this->setShowTitle( isset( $renderData['show_title'] ) ? $renderData['show_title'] : false );
       $this->setShowLegend( isset( $renderData['show_legend'] ) ? $renderData['show_legend'] : false );
       $this->setWidth( $renderData['render_data']['options']['width'] );
       $this->setHeight( $renderData['render_data']['options']['height'] );
       $this->loadChildWPDataTable();
   }
   
   /**
    * Render Chart
    */
   public function renderChart(){
       
       $minified_js = get_option('wdtMinifiedJs');
       
       $this->prepareData();
       if( $this->_follow_filtering ){
            $this->getColumnIndexes();
       }

       $this->shiftStringColumnUp();
       
       if( $this->_engine == 'google' ){
            // Google Chart JS
            wp_enqueue_script( 'wdt_google_charts', '//www.google.com/jsapi' );           
            if( $minified_js ){
                // Google Chart wpDataTable JS library
                wp_enqueue_script( 'wpdatatables-google-chart', WDT_JS_PATH.'wpdatatables/wpdatatables_google_charts.min.js' );
                // wpDataCharts render script
                wp_enqueue_script( 'wpdatatables-render-chart', WDT_JS_PATH.'wpdatatables/wpdatacharts_render.min.js' );
            }else{
                // Google Chart wpDataTable JS library
                wp_enqueue_script( 'wpdatatables-google-chart', WDT_JS_PATH.'wpdatatables/wpdatatables_google_charts.js' );
                // wpDataCharts render script
                wp_enqueue_script( 'wpdatatables-render-chart', WDT_JS_PATH.'wpdatatables/wpdatacharts_render.js' );
            }
            $json_chart_render_data = json_encode( $this->_render_data );
       }else{
            $this->prepareHighchartsRender();
            // Highchart JS
            wp_enqueue_script('wdt_highcharts','//code.highcharts.com/highcharts.js');
            wp_enqueue_script('wdt_highcharts3d','//code.highcharts.com/highcharts-3d.js');
            if( $minified_js ){
                // Highchart wpDataTable JS library
                wp_enqueue_script('wpdatatables-highcharts',WDT_JS_PATH.'wpdatatables/wpdatatables_highcharts.min.js');
                // wpDataCharts render script
                wp_enqueue_script( 'wpdatatables-render-chart', WDT_JS_PATH.'wpdatatables/wpdatacharts_render.min.js' );
            }else{
                // Highchart wpDataTable JS library
                wp_enqueue_script('wpdatatables-highcharts',WDT_JS_PATH.'wpdatatables/wpdatatables_highcharts.js');
                // wpDataCharts render script
                wp_enqueue_script( 'wpdatatables-render-chart', WDT_JS_PATH.'wpdatatables/wpdatacharts_render.js' );
            }
            $json_chart_render_data = json_encode( $this->_highcharts_render_data );
       }
       
       $chart_id = $this->_id;
       ob_start();
       include( WDT_TEMPLATE_PATH.'wpdatachart.inc.php' );
       $chart_html = ob_get_contents();
       ob_end_clean();
       return $chart_html;
       
   }
   
   /**
    * Return render data
    */
   public function getRenderData(){
        return $this->_render_data;
   }
   
   /**
    * Delete chart by ID
    */
   public static function deleteChart( $chartId ){
       global $wpdb;
       
       $wpdb->delete(
            $wpdb->prefix."wpdatacharts",
            array(
                'id' => (int) $chartId
            )
        );
       
   }
   
}
?>