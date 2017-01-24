<?php

/**
 * Created by PhpStorm.
 * User: Milos Roksandic
 * Date: 23.2.16.
 * Time: 19.54
 */
class WPExcelDataTable extends WPDataTable
{
    protected static $_columnClass = 'WDTExcelColumn';

    protected function _renderWithJSAndStyles() {
        $tpl = new PDTTpl();
        $minified_js = get_option('wdtMinifiedJs');

        if(WDT_INCLUDE_DATATABLES_CORE){
            if( $minified_js ) {
                wp_register_script('handsontable', WDT_JS_PATH . 'handsontable/handsontable.full.min.js', array('jquery'));
            }else{
                wp_register_script('handsontable', WDT_JS_PATH . 'handsontable/handsontable.full.js', array('jquery'));
            }
            wp_enqueue_script('handsontable');
        }

        wp_enqueue_script('wpdatatables-urijs',WDT_JS_PATH.'urijs/URI.min.js');

        wp_enqueue_script('moment', WDT_JS_PATH.'moment/moment.js');
        if( $minified_js ){
            wp_register_script('wpdatatables_excel',WDT_JS_PATH.'wpdatatables/wpdatatables_excel.min.js',array('jquery','handsontable', 'wpdatatables-urijs'));
            wp_enqueue_script('wpdatatables_excel_plugin', WDT_JS_PATH.'wpdatatables/wpDataTablesExcelPlugin.min.js',array('jquery', 'handsontable', 'jquery-ui-tooltip'));
        }else{
            wp_register_script('wpdatatables_excel',WDT_JS_PATH.'wpdatatables/wpdatatables_excel.js',array('jquery','handsontable', 'wpdatatables-urijs'));
            wp_enqueue_script('wpdatatables_excel_plugin', WDT_JS_PATH.'wpdatatables/wpDataTablesExcelPlugin.js',array('jquery', 'handsontable', 'jquery-ui-tooltip'));
        }
        wp_enqueue_script('wpdatatables_excel');


        // Localization
        wp_localize_script( 'wpdatatables_excel', 'wpdatatables_frontend_strings', WDTTools::getTranslationStrings() );
        wp_localize_script( 'wpdatatables_excel_plugin', 'wpdatatables_frontend_strings', WDTTools::getTranslationStrings() );

        $this->addCSSClass( 'data-t' );
        $tpl->setTemplate( 'wpdatatables_excel_table_main.inc.php' );
        $tpl->addData( 'wpDataTable', $this );
        return $tpl->returnData();
    }

    public function generateTable() {

        $tpl = new PDTTpl();
        if($this->scriptsEnabled) {
            $cssArray = array(
                'wpdatatables-handsontable-min' => WDT_CSS_PATH.'handsontable.full.min.css',
                'wpdatatables-excel-min' => WDT_CSS_PATH.'wpdatatables-excel.min.css'
            );
            foreach($cssArray as $cssKey => $cssFile){
                if (defined('DOING_AJAX') && DOING_AJAX){
                    $tpl->addCss($cssFile);
                }else{
                    wp_enqueue_style( $cssKey, $cssFile );
                }
            }
        }
        $table_content = $this->_renderWithJSAndStyles();
        $tpl->addData( 'wdt_output_table', $table_content );
        $tpl->setTemplate( 'wrap_template.inc.php' );

        $return_data = $tpl->returnData();
        $return_data = apply_filters( 'wpdatatables_excel_filter_table_template', $return_data, $this->getWpId() );
        return $return_data;
    }

    public function getColumnDefinitions() {
        $defs = array();
        foreach($this->_wdtIndexedColumns as $key => &$dataColumn){
            $def = $dataColumn->getColumnJSON();
            $defs[] = $def;
        }
        return $defs;
    }

    /**
     * Returns JSON object for table description
     */
    public function getJsonDescription(){
        global $wdt_var1, $wdt_var2, $wdt_var3, $wdt_export_file_name;

        $obj = new stdClass();
        $obj->tableId = $this->getId();
        $obj->selector = '#'.$this->getId();
        $obj->tableWpId = $this->getWpId();
        $obj->responsive = $this->isResponsive();
        $obj->editable = $this->isEditable();

        $obj->decimal_places = (int) (get_option('wdtDecimalPlaces') ? get_option('wdtDecimalPlaces') : 2);

        $obj->dataTableParams = new StdClass();
        $obj->dataTableParams->number_format = (int) (get_option('wdtNumberFormat') ? get_option('wdtNumberFormat') : 1);
        $obj->dataTableParams->readOnly = !$this->isEditable();//set max row number for non editable tables
        $obj->dataTableParams->allowInvalid = false;

        $init_date_format = get_option('wdtDateFormat');
        $obj->dataTableParams->displayDateFormat = WDTTools::convertPhpToMomentDateFormat( $init_date_format );//custom option
        $obj->dataTableParams->dataSourceDateFormat = $obj->dataTableParams->displayDateFormat;//custom option
        $timeFormat = get_option('wdtTimeFormat');

        $obj->dataTableParams->origTimeFormat = $timeFormat;
        $obj->dataTableParams->timepickRange = get_option('wdtTimepickerRange') ? intval( get_option('wdtTimepickerRange') ) : 5;
        $obj->dataTableParams->timepickTimeFormat = str_replace( 'H', 'HH', $timeFormat );
        $obj->dataTableParams->momentTimeFormat = str_replace( 'i', 'mm', $timeFormat );

        if($this->isEditable()){
            $obj->dataTableParams->adminAjaxBaseUrl =  site_url().'/wp-admin/admin-ajax.php';
            $obj->dataTableParams->idColumnIndex = $this->getColumnHeaderOffset($this->getIdColumnKey());
            $obj->dataTableParams->idColumnKey = $this->getIdColumnKey();
            $obj->dataTableParams->dateFormat = $obj->dataTableParams->displayDateFormat;
            $obj->dataTableParams->datePickerConfig = array( 'format' => $obj->dataTableParams->displayDateFormat );
            $obj->dataTableParams->dataSourceDateFormat = WDTTools::convertPhpToMomentDateFormat( 'Y-m-d' );
        }
        $obj->dataTableParams->columns = $this->getColumnDefinitions();

        if($this->sortEnabled()){
            $sort_column = 0;
            $sort_direction = true;//true for ascending, false for descending

            if( !is_null($this->getDefaultSortColumn()) ) {
                $sort_column = $this->getDefaultSortColumn();

                if( strtolower($this->getDefaultSortDirection()) == 'desc' ) {
                    $sort_direction = false;
                }
            }

            $obj->dataTableParams->columnSorting = array( 'column' => $sort_column, 'sortOrder' => $sort_direction );
            $obj->dataTableParams->sortIndicator = true;
        }else{
            $obj->dataTableParams->columnSorting = false;
        }

        if($this->serverSide()){
            $obj->serverSide = true;
            $obj->dataTableParams->serverSide = true;

            $obj->dataTableParams->ajax = array(
                'url' => site_url().'/wp-admin/admin-ajax.php?action=get_wdtable&table_id='.$this->getWpId(),
                'type' => 'POST'
            );
            if( !empty( $wdt_var1 ) ){
                $obj->dataTableParams->ajax['url'] .= '&wdt_var1='.urlencode( $wdt_var1 );
            }
            if( !empty( $wdt_var2 ) ){
                $obj->dataTableParams->ajax['url'] .= '&wdt_var2='.urlencode( $wdt_var2 );
            }
            if( !empty( $wdt_var3 ) ){
                $obj->dataTableParams->ajax['url'] .= '&wdt_var3='.urlencode( $wdt_var3 );
            }

        }else{
            $obj->serverSide = false;
        }

        if(get_option('wdtTabletWidth')){
            $obj->tabletWidth = get_option('wdtTabletWidth');
        }
        if(get_option('wdtMobileWidth')){
            $obj->mobileWidth = get_option('wdtMobileWidth');
        }

        $obj->dataTableParams->search = true;
        $obj->dataTableParams->searchDefaultValue = $this->getDefaultSearchValue();

        $obj = apply_filters( 'wpdatatables_excel_filter_table_description', $obj, $this->getWpId() );

        return json_encode( $obj, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG );
    }

    /**
     * Formatting row data structure for ajax display table
     * @param $row key => value pairs as column name and cell value of a row
     * @return array formatted row
     */
    protected function formatAjaxQueryResultRow( $row ) {
        return $row;
    }

}