<?php
/**
 * Main engine of wpDataTables plugin
 */

class WPDataTable {

    protected static $_columnClass = 'WDTColumn';
    protected $scriptsEnabled = true;
    protected $_wdtIndexedColumns = array( );
    private $_wdtNamedColumns = array( );
    private $_defaultSortColumn = null;
    private $_defaultSortDirection = 'ASC';
    private $_title = '';
    private $_interfaceLanguage;
    private $_responsive = false;
    private $_scrollable = false;
    private $_inlineEditing = false;
    private $_popoverTools = false;
    private $_no_data = false;
    private $_filtering_form = false;
    private $_hide_before_load = false;
    public static $wdt_internal_idcount = 0;
    private $_horAxisCol = '';
    private $_verAxisCol = '';
    private $_chartType = '';
    private $_chartTitle = '';
    public static $mc = null;
    private $_fromCache = false;
    private $_pagination = true;
    private $_showFilter = true;
    private $_firstOnPage = false;
    private $_groupingEnabled = false;
    private $_wdtColumnGroupIndex = 0;
    private $_showAdvancedFilter = false;
    private $_wdtTableSort = true;
    private $_serverProcessing = false;
    private $_wdtColumnTypes = array( );
    private $_dataRows = array( );
    public  $_cacheHash = '';
    private $_showTT = true;
    private $_lengthDisplay = 10;
    private $_cssClassArray = array( );
    private $_style	 = '';
    private $_chartSeriesArr = array();
    private $_editable = false;
    private $_id = '';
    private $_idColumnKey = '';
    private $_db;
    private $_wpId = '';
    private $_onlyOwnRows = false;
    private $_userIdColumn = 0;
    private $_defaultSearchValue = '';
    protected $_sumColumns = array();
    protected $_conditionalFormattingColumns = array();
    private $_columnsCSS = '';
    private $_tableToolsConfig = array();
    private $_autoRefreshInterval = 0;

    public function setNoData($no_data){
       $this->_no_data = $no_data;
    }

    public function getNoData(){
        return $this->_no_data;
    }

    public function getId() {
        return $this->_id;
    }

    public function setId( $id ) {
        $this->_id = $id;
    }

    public function setDefaultSearchValue( $value ) {
	if( !empty($value) ) {
	    $this->_defaultSearchValue = urlencode($value);
	}
    }

    public function getDefaultSearchValue() {
	return urldecode($this->_defaultSearchValue);
    }

    public function sortEnabled() {
        return $this->_wdtTableSort;
    }

    public function sortEnable() {
	$this->_wdtTableSort = true;
    }

    public function sortDisable() {
        $this->_wdtTableSort = false;
    }

    public function addSumColumn( $columnKey ){
        $this->_sumColumns[] = $columnKey;
    }

    public function setSumColumns( $sumColumns ){
        $this->_sumColumns = $sumColumns;
    }

    public function getSumColumns(){
        return $this->_sumColumns;
    }

    public function getColumnsCSS(){
        return $this->_columnsCSS;
    }

    public function setColumnsCss( $css ){
        $this->_columnsCSS = $css;
    }

    public function reorderColumns( $posArray ) {
     	if( !is_array( $posArray )){
     		throw new WDTException('Invalid position data provided!');
     	}
     	$resultArray = array();
     	$resultByKeys = array();

     	foreach( $posArray as $pos=>$dataColumnIndex ){
     		$resultArray[$pos] = $this->_wdtNamedColumns[$dataColumnIndex];
     		$resultByKeys[$dataColumnIndex] = $this->_wdtNamedColumns[$dataColumnIndex];
     	}
     	$this->_wdtIndexedColumns = $resultArray;
     	$this->_wdtNamedColumns = $resultByKeys;
    }

    public function getWpId() {
        return $this->_wpId;
    }

    public function setWpId( $wpId ) {
        $this->_wpId = $wpId;
    }

    public function getCssClassesArr(){
        $classesStr = $this->_cssClassArray;
        $classesStr = apply_filters( 'wpdatatables_filter_table_cssClassArray', $classesStr, $this->getWpId() );
        return $classesStr;
    }

    public function getCSSClasses(){
        return implode(' ', $this->_cssClassArray);
    }

    public function addCSSClass( $cssClass ) {
        $this->_cssClassArray[] = $cssClass;
    }

    public function getCSSStyle() {
        return $this->_style;
    }

    public function setCSSStyle( $style ){
        $this->_style = $style;
    }

    public function setTitle( $title ) {
        $this->_title = $title;
    }

    public function getName(){
        return $this->_title;
    }

    public function setScrollable($scrollable){
        if($scrollable){
            $this->_scrollable = true;
        }else{
            $this->_scrollable = false;
        }
    }

    public function isScrollable(){
        return $this->_scrollable;
    }

    public function setInterfaceLanguage( $lang ) {
       if( empty($lang) ){
               throw new WDTException('Incorrect language parameter!');
       }
       if( !file_exists( WDT_ROOT_PATH.'source/lang/'.$lang ) ){
               throw new WDTException('Language file not found');
       }
       $this->_interfaceLanguage = WDT_ROOT_PATH.'source/lang/'.$lang;
    }

    public function getInterfaceLanguage(){
      return $this->_interfaceLanguage;
    }

    public function setAutoRefresh( $refresh_interval ){
        $this->_autoRefreshInterval = (int)$refresh_interval;
    }

    public function getRefreshInterval(){
        return (int)$this->_autoRefreshInterval;
    }

    public function paginationEnabled() {
        return $this->_pagination;
    }

    public function enablePagination() {
        $this->_pagination = true;
    }

    public function disablePagination() {
        $this->_pagination = false;
    }

    public function enableTT() {
        $this->_showTT = true;
    }

    public function disableTT() {
        $this->_showTT = false;
    }

    public function TTEnabled() {
        return $this->_showTT;
    }

    public function hideToolbar() {
        $this->_toolbar = false;
    }

    public function setDefaultSortColumn( $key ){
        if( !isset( $this->_wdtIndexedColumns[$key] )
                && !isset( $this->_wdtNamedColumns[$key] ) ) {
            throw new WDTException( 'Incorrect column index' );
        }

        if(!is_numeric($key)){
            $key = array_search( $key, array_keys($this->_wdtNamedColumns) );
        }
        $this->_defaultSortColumn = $key;
    }

    public function getDefaultSortColumn(){
        return $this->_defaultSortColumn;
    }

    public function setDefaultSortDirection($direction){
     	if(
                !in_array(
                        $direction,
                        array(
                            'ASC',
                            'DESC'
                            )
                        )
                ){
            return false;
        }
     	$this->_defaultSortDirection = $direction;
    }

    public function getDefaultSortDirection(){
      	return $this->_defaultSortDirection;
    }

    public function hideBeforeLoad(){
        $this->setCSSStyle('display: none; ');
        $this->_hide_before_load = true;
    }

    public function showBeforeLoad(){
        $this->_hide_before_load = false;
    }

    public function doHideBeforeLoad(){
        return $this->_hide_before_load;
    }

    public function getDisplayLength(){
        return $this->_lengthDisplay;
    }

    public function setDisplayLength( $length ){
            if(!in_array($length, array(5, 10, 20, 25, 30, 50, 100, 200, -1))){
                    return false;
            }
            $this->_lengthDisplay = $length;
    }

    public function setIdColumnKey( $key ) {
     	$this->_idColumnKey = $key;
    }

    public function getIdColumnKey(){
      	return $this->_idColumnKey;
    }

    public function __construct( ) {
        //[<-- Full version -->]//
        // connect to MySQL if enabled
        if(WDT_ENABLE_MYSQL && get_option('wdtUseSeparateCon')){
            $this->_db = new PDTSql(WDT_MYSQL_HOST, WDT_MYSQL_DB, WDT_MYSQL_USER, WDT_MYSQL_PASSWORD, WDT_MYSQL_PORT);
        }
        //[<--/ Full version -->]//
        if(self::$wdt_internal_idcount == 0){ $this->_firstOnPage = true; }
        self::$wdt_internal_idcount++;
        $this->_id = 'table_'.self::$wdt_internal_idcount;
    }

    public function wdtDefineColumnsWidth( $widthsArray ) {
        if( empty( $this->_wdtIndexedColumns ) ) { throw new WDTException('wpDataTable reports no columns are defined!'); }
        if( !is_array($widthsArray) ) { throw new WDTException('Incorrect parameter passed!'); }
        if( wdtTools::isArrayAssoc($widthsArray) ) {
            foreach( $widthsArray as  $name =>$value ) {
                if(!isset($this->_wdtNamedColumns[ $name ])) { continue; }
                $this->_wdtNamedColumns[ $name ]->setWidth($value);
            }
        } else{
            // if width is provided in indexed array
            foreach( $widthsArray as  $name =>$value ) {
                $this->_wdtIndexedColumns[ $name ]->setWidth($value);
            }
        }
    }

    public function setColumnsPossibleValues( $valuesArray ) {
        if( empty($this->_wdtIndexedColumns) ) {
                throw new WDTException('No columns in the table!');
        }
        if( !is_array( $valuesArray ) ) {
            throw new WDTException('Valid array of width values is required!');
        }
        if( WDTTools::isArrayAssoc( $valuesArray ) ) {
            foreach( $valuesArray as $key=>$value ) {
                if(!isset($this->_wdtNamedColumns[$key])) { continue; }
                $this->_wdtNamedColumns[$key]->setPossibleValues($value);
            }
        } else{
            foreach( $valuesArray as $key=>$value ) {
                $this->_wdtIndexedColumns[$key]->setPossibleValues( $value );
            }
        }
    }

    public function getHiddenColumnCount(){
        $count = 0;
        foreach($this->_wdtIndexedColumns as $dataColumn){
            if(!$dataColumn->isVisible()){
                $count++;
            }
        }
        return $count;
    }

    //[<-- Full version -->]//
    public function enableServerProcessing(){
        $this->_serverProcessing = true;
    }

    public function disableServerProcessing(){
        $this->_serverProcessing = false;
    }

    public function serverSide(){
        return $this->_serverProcessing;
    }

    public function setResponsive($responsive){
        if($responsive){
            $this->_responsive = true;
        }else{
            $this->_responsive = false;
        }
    }

    public function isResponsive(){
        return $this->_responsive;
    }

    public function enableEditing(){
        $this->_editable = true;
    }

    public function disableEditing(){
        $this->_editable = false;
    }

    public function isEditable(){
        return $this->_editable;
    }

    public function enablePopoverTools(){
        $this->_popoverTools = true;
    }

    public function disablePopoverTools(){
        $this->_popoverTools = false;
    }

    public function popoverToolsEnabled(){
        return $this->_popoverTools;
    }

    public function enableInlineEditing(){
        $this->_inlineEditing = true;
    }

    public function disableInlineEditing(){
        $this->_inlineEditing = false;
    }

    public function inlineEditingEnabled(){
        return $this->_inlineEditing;
    }

    public function filterEnabled() {
        return $this->_showFilter;
    }

    public function enableFilter() {
        $this->_showFilter = true;
    }

    public function disableFilter() {
        $this->_showFilter = false;
    }

    public function setFilteringForm( $filteringForm ){
    	$this->_filtering_form = (bool) $filteringForm;
    }

    public function getFilteringForm(){
    	return $this->_filtering_form;
    }

    public function advancedFilterEnabled() {
        return $this->_showAdvancedFilter;
    }

    public function enableAdvancedFilter() {
        $this->_showAdvancedFilter = true;
    }

    public function disableAdvancedFilter() {
        $this->_showAdvancedFilter = false;
    }
    //[<--/ Full version -->]//

    public function enableGrouping() {
        $this->_groupingEnabled = true;
    }

    public function disableGrouping() {
        $this->_groupingEnabled = false;
    }

    public function groupingEnabled() {
        return $this->_groupingEnabled;
    }

    public function groupByColumn($key) {
        if( !isset( $this->_wdtIndexedColumns[$key] )
                && !isset( $this->_wdtNamedColumns[$key] ) ){
            throw new WDTException('Column not found!');
        }

        if( !is_numeric( $key ) ){
            $key = array_search(
                        $key,
                        array_keys( $this->_wdtNamedColumns )
                    );
        }

        $this->enableGrouping();
        $this->_wdtColumnGroupIndex = $key;
    }

    /**
     * Returns the index of grouping column
     */
    public function groupingColumnIndex(){
            return $this->_wdtColumnGroupIndex;
    }

    /**
     * Returns the grouping column index
     */
    public function groupingColumn(){
        return $this->_wdtColumnGroupIndex;
    }

    public function countColumns() {
	return count( $this->_wdtIndexedColumns );
    }

    public function getColumnKeys() {
        return array_keys( $this->_wdtNamedColumns );
    }

    public function setOnlyOwnRows( $ownRows ){
        $this->_onlyOwnRows = (bool) $ownRows;
    }

    public function getOnlyOwnRows(){
        return $this->_onlyOwnRows;
    }

    public function setUserIdColumn( $column ){
        $this->_userIdColumn = $column;
    }

    public function getUserIdColumn(){
        return $this->_userIdColumn;
    }

    public function getColumns() {
        return $this->_wdtIndexedColumns;
    }

    public function getColumnsByHeaders() {
        return $this->_wdtNamedColumns;
    }

    public function addConditionalFormattingColumn( $column ){
        $this->_conditionalFormattingColumns[] = $column;
    }

    public function getConditionalFormattingColumns(){
        return $this->_conditionalFormattingColumns;
    }

    public function createColumnsFromArr( $headerArr, $wdtParameters, $wdtColumnTypes ){

        foreach($headerArr as $key) {
            $dataColumnProperties = array( );
            $dataColumnProperties['title']	= isset($wdtParameters['column_titles'][$key]) ? $wdtParameters['column_titles'][$key] : $key;
            $dataColumnProperties['width']	= !empty($wdtParameters['columns_width'][$key]) ? $wdtParameters['columns_width'][$key] : '';
            $dataColumnProperties['sort'] = $this->_wdtTableSort;
            $dataColumnProperties['orig_header'] = $key;

            $tableColumnClass = static::$_columnClass;
            $dataColumn = $tableColumnClass::generateColumn( $wdtColumnTypes[$key], $dataColumnProperties );
            //[<-- Full version -->]//
            if( $wdtColumnTypes[$key] == 'formula' ){
                if( !empty( $wdtParameters['column_formulas'][$key] ) ){
                    $dataColumn->setFormula( $wdtParameters['column_formulas'][$key] );
                    if( $this->serverSide() ){
                        $dataColumn->sortDisable();
                        $dataColumn->searchDisable();
                    }
                }else{
                    $dataColumn->setFormula( '' );
                }
            }
            //[<--/ Full version -->]//
            $this->_wdtIndexedColumns[] = $dataColumn;
            $this->_wdtNamedColumns[$key] = &$this->_wdtIndexedColumns[count($this->_wdtIndexedColumns)-1];
        }

    }

    public function getColumnHeaderOffset($key){
     	$keys = $this->getColumnKeys();
     	if(!empty($key) && in_array($key, $keys)){
            return array_search($key, $keys);
     	}else{
            return -1;
     	}
    }

    public function getColumnDefinitions() {
        $defs = array();
        foreach($this->_wdtIndexedColumns as $key => &$dataColumn){
            $def = $dataColumn->getColumnJSON();
            $def->aTargets = array($key);
            $defs[] = json_encode($def);
        }
        return implode(', ', $defs);
    }

    public function getColumnFilterDefs() {
        $defs = array();
         foreach($this->_wdtIndexedColumns as $key=>$dataColumn){
            $def = $dataColumn->getFilterType();
            if($this->getFilteringForm()){
                $def->sSelector = '#'.$this->getId().'_'.$key.'_filter';
            }
                $def->defaultValue = $dataColumn->getDefaultValue();
            $defs[] = json_encode($def);
        }
        return implode(', ', $defs);
    }

    public function getColumn( $dataColumnIndex ) {
        if( !isset( $dataColumnIndex )
                || ( !isset($this->_wdtNamedColumns[$dataColumnIndex])
                && !isset($this->_wdtIndexedColumns[$dataColumnIndex]) ) ) {
                        return false;
        }
        if( !is_int( $dataColumnIndex ) ){
            return $this->_wdtNamedColumns[$dataColumnIndex];
        } else {
            return $this->_wdtIndexedColumns[$dataColumnIndex];
        }
    }

    /**
     * Generates the structure in memory needed to render the tables
     *
     * @param Array $rawDataArr Array of data for the table content
     * @param Array $wdtParameters Array of rendering parameters
     * @return bool Result of generation
     */
    public function arrayBasedConstruct( $rawDataArr, $wdtParameters ) {

        if( empty( $rawDataArr ) ){
            if(!isset($wdtParameters['data_types'])){
                $rawDataArr = array(0 => array('No data' => 'No data'));
            }else{
                $arrayEntry = array();
                foreach($wdtParameters['data_types'] as $cKey=>$cType){
                    $arrayEntry[$cKey] = $cKey;
                }
                $rawDataArr[] = $arrayEntry;
            }
            $this->setNoData(true);
        }

        $headerArr = WDTTools::extractHeaders( $rawDataArr );
        //[<-- Full version insertion #09 -->]//
        if( !empty( $wdtParameters['column_titles'] ) ){
            $headerArr = array_unique(
                                array_merge(
                                    $headerArr,
                                    array_keys( $wdtParameters['column_titles'] )
                                )
                            );
        }

        $wdtColumnTypes = isset($wdtParameters['data_types']) ? $wdtParameters['data_types'] : array( );

        if( empty( $wdtColumnTypes ) ){
            $wdtColumnTypes = WDTTools::detectColumnDataTypes( $rawDataArr, $headerArr );
        }

        if( empty( $wdtColumnTypes ) ) {
            foreach( $headerArr as $key ){ $wdtColumnTypes[$key] = 'string'; }
        }

        $this->createColumnsFromArr( $headerArr, $wdtParameters, $wdtColumnTypes );

        $this->_wdtColumnTypes = $wdtColumnTypes;

        if(!$this->getNoData()){ $this->_dataRows = $rawDataArr; }

        if( empty( $wdtParameters['dates_detected'] )
            && count( array_intersect( array( 'date', 'datetime', 'time' ), $wdtColumnTypes ) ) ){
            foreach( $wdtColumnTypes as $key => $columnType ){
                if( in_array( $columnType, array( 'date', 'datetime', 'time' ) ) ){
                    foreach( $this->_dataRows as &$dataRow ){
                        $dataRow[$key] = is_int( $dataRow[$key] ) ? $dataRow[$key] : strtotime( $dataRow[$key] );
                    }
                }
            }
        }

        //[<-- Full version -->]//
        // Calculate formula columns
        if( in_array( 'formula', $wdtColumnTypes ) ){
            $this->calculateFormulaCells();
        }
        //[<--/ Full version -->]//

        return true;

    }

    //[<-- Full version -->]//
    /**
     * Helper function that helps to calculate the formula-based cells
     */
    public function calculateFormulaCells(){

        foreach( array_keys( $this->_wdtColumnTypes, 'formula' ) as $column_key ){
            $formula = $this->getColumn($column_key)->getFormula();
            $headers_in_formula = $this->detectHeadersInFormula( $formula );
            foreach( $this->_dataRows as &$row ){
                try {
                    $row[$column_key] =
                        self::solveFormula(
                                    $formula,
                                    $headers_in_formula,
                                    $row
                                );
                }catch(Exception $e){
                    $row[$column_key] = 0;
                }
            }
        }
    }
    //[<--/ Full version -->]//

    public function hideColumn( $dataColumnIndex ) {
        if( !isset($dataColumnIndex)
                || !isset($this->_wdtNamedColumns[$dataColumnIndex]) ) {
            throw new WDTException('A column with provided header does not exist.');
        }
        $this->_wdtNamedColumns[$dataColumnIndex]->hide();
    }

    public function showColumn( $dataColumnIndex ) {
        if( !isset($dataColumnIndex)
                || !isset($this->_wdtNamedColumns[$dataColumnIndex]) ) {
            throw new WDTException('A column with provided header does not exist.');
        }
        $this->_wdtNamedColumns[$dataColumnIndex]->show();
    }


    public function getCell( $dataColumnIndex, $rowKey ) {
        if( !isset( $dataColumnIndex )
            || !isset( $rowKey ) ) {
                throw new WDTException('Please provide the column key and the row key');
        }
        if( !isset( $this->_dataRows[$rowKey]) ) {
            throw new WDTException('Row does not exist.');
        }
        if( !isset( $this->_wdtNamedColumns[$dataColumnIndex])
                && !isset( $this->_wdtIndexedColumns[$dataColumnIndex] ) ) {
            throw new WDTException('Column does not exist.');
        }
        return $this->_dataRows[$rowKey][$dataColumnIndex];
    }

    public function returnCellValue( $cellContent, $wdtColumnIndex ) {
        if( !isset($wdtColumnIndex) ) {
            throw new WDTException('Column index not provided!');
        }
        if( !isset( $this->_wdtNamedColumns[ $wdtColumnIndex ] ) ) {
            throw new WDTException('Column index out of bounds!');
        }
        return $this->_wdtNamedColumns[ $wdtColumnIndex ]->returnCellValue( $cellContent);
    }

    public function getDataRows() {
	    return $this->_dataRows;
    }

    public function getRow( $index ) {
        if( !isset($index) || !isset($this->_dataRows[$index]) ) {
            throw new WDTException('Invalid row index!');
        }
        $rowArray = &$this->_dataRows[$index];
        apply_filters( 'wdt_get_row', $rowArray );
        return $rowArray;
    }

    public function addDataColumn( &$dataColumn ) {
        if( !($dataColumn instanceof WDTColumn) ) { throw new WDTException('Please provide a wpDataTable column.'); }
        apply_filters( 'wdt_add_column', $dataColumn );
        $this->_wdtIndexedColumns[] = &$dataColumn;
        return true;
    }

    public function addColumns( &$dataColumns ) {
        if( !is_array( $dataColumns ) ) { throw new WDTException('Please provide an array of wpDataTable column objects.'); }
        apply_filters( 'wdt_add_columns', $dataColumns );
        foreach( $dataColumns as &$dataColumn ) {
            $this->addDataColumn( $dataColumn );
        }
    }

    public function addWDTRow( $row ) {
        if( count( $this->_wdtIndexedColumns ) == 0 ) {
            throw new WDTException('Please add columns to wpDataTable first.');
        }
        if( !( $row instanceof WDTRow ) ) {
            throw new WDTException( 'Please provide a proper wpDataTables Row' );
        }
        if( $row->countCells() != $this->countColumns() ) {
            throw new WDTException( 'Count of the columns in table and in row is not equal. Row: '.$row->countCells().', table: '.$this->countColumns() );
        }
        apply_filters( 'wdt_add_row', $row );
        $this->_dataRows[] = &$row;
    }

    public function addRows( &$rows ) {
        if( !is_array( $rows ) ) {
            throw new WDTException('Please provide an array of WDTRow objects.');
        }
        apply_filters( 'wdt_add_dataRows', $rows );
        foreach( $rows as &$row ) {
            $this->addWDTRow( $row );
        }
    }

    public function setChartHorizontalAxis($dataColumnIndex){
        if( !isset($dataColumnIndex) || !isset($this->_wdtNamedColumns[$dataColumnIndex]) ) {
            throw new WDTException('Please provide a correct column index.');
        }
        $this->_horAxisCol = $dataColumnIndex;
    }

    //[<-- Full version -->]//
    public function setChartVerticalAxis($dataColumnIndex){
        // if we are getting the data from cache - just skip the call
        if(self::$mc && $this->_fromCache){
            return false;
        }
        if( !isset($dataColumnIndex) || !isset($this->_wdtNamedColumns[$dataColumnIndex]) ) {
                throw new WDTException('Invalid columnKey provided!');
        }
        $this->_verAxisCol = $dataColumnIndex;
    }

    public function setChartTitle($title){
        if(empty($title)) { return false; };
        $this->_chartTitle = $title;
    }

    public function getChartTitle(){
        return $this->_chartTitle;
    }

    public function setChartType($type){
        if(empty($type)
                || ( !in_array(
                        $type,
                        array(
                            'Area',
                            'Bar',
                            'Column',
                            'Line',
                            'Pie'
                        )
                    )
                    )
            ) {
            throw new WDTException('Invalid chart type provided!');
        };
        $this->_chartType = $type;
    }

    public function getChartType(){
        return $this->_chartType;
    }

    public function addChartSeries($dataColumnIndex){
        if( !isset($dataColumnIndex) || !isset($this->_wdtNamedColumns[$dataColumnIndex]) ) {
                throw new WDTException('Invalid columnKey provided!');
        }
        $this->_chartSeriesArr[] = $dataColumnIndex;
    }
    //[<--/ Full version -->]//

    public function disableScripts(){
     	$this->scriptsEnabled = false;
    }

    public function scriptsEnabled(){
      	return $this->scriptsEnabled;
    }

    //[<-- Full version -->]//
    public function queryBasedConstruct( $query, $queryParams = array(), $wdtParameters = array (), $init_read = false ) {
        global $wdt_var1, $wdt_var2, $wdt_var3, $wpdb;

        // Sanitizing query
        $query = wpdatatables_sanitize_query( $query );
        $query = str_replace( '`', '', $query );

        $query = WDTTools::applyPlaceholders( $query );

        // Adding limits if necessary
        if(!empty($wdtParameters['limit'])
        	&& (strpos(strtolower($query), 'limit') === false)){
       		$query .= ' LIMIT '.$wdtParameters['limit'];
        }

        // Server-side requests
        if($this->serverSide()) {

            $query = apply_filters( 'wpdatatables_filter_query_before_limit', $query, $this->getWpId() );

            if(!isset($_POST['draw'])){
                if( empty( $wdtParameters['disable_limit'] ) ){
                    $query .= ' LIMIT '.$this->getDisplayLength();
                }
            } else {
                // Server-side params
                $limit = '';
                $orderby = '';
                $search = '';
                $aColumns = array_keys( $wdtParameters['column_titles'] );

                if ( isset( $_POST['start'] ) && $_POST['length'] != '-1' ) {
                            $limit = "LIMIT ".addslashes( $_POST['start'] ).", ".
                                addslashes( $_POST['length'] );
                        }


                // Adding sort parameters for AJAX if necessary
                if ( isset( $_POST['order'] ) )
                {
                    $orderby = "ORDER BY  ";
                    for ( $i=0 ; $i<count( $_POST['order'] ) ; $i++ )
                    {
                        if ( $_POST['columns'][$_POST['order'][$i]['column']]['orderable'] == "true" )
                        {
                            $orderby .= '`'.$aColumns[ $_POST['order'][$i]['column'] ]."`
                                    ".addslashes( $_POST['order'][$i]['dir'] ) .", ";
                        }
                    }

                    $orderby = substr_replace( $orderby, "", -2 );
                    if ( $orderby == "ORDER BY" )
                    {
                        $orderby = "";
                    }
                }

                // filtering
                if ( !empty( $_POST['search']['value'] ) )
                {
                    $search = " (";
                    for ( $i=0 ; $i<count($aColumns) ; $i++ )
                    {
                        if( $_POST['columns'][$i]['searchable'] == "true" ){
                            $search .= '`'.$aColumns[$i]."` LIKE '%".addslashes( $_POST['search']['value'] )."%' OR ";
                        }
                    }
                    $search = substr_replace( $search, "", -3 );
                    $search .= ')';
                }

                /* Individual column filtering */
                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    if ( ( $_POST['columns'][$i]['searchable'] == "true" )
                            && ( $_POST['columns'][$i]['search']['value'] != '')
                            && ( $_POST['columns'][$i]['search']['value'] != '~') ){
                        if(!empty($search)){
                                $search .= ' AND ';
                        }
                        switch($wdtParameters['filter_types'][$aColumns[$i]]) {
                            case 'number':
                                $search .= '`'.$aColumns[$i]."` = ".$_POST['columns'][$i]['search']['value']." ";
                                break;
                            case 'number-range':
                                list($left, $right) = explode('~', $_POST['columns'][$i]['search']['value']);
                                if($left !== ''){
                                    $left = (float) $left;
                                    $search .= '`'.$aColumns[$i]."` >= $left ";
                                }
                                if($right !== ''){
                                    $right = (float) $right;
                                    if(!empty($search) && $left !== ''){ $search .= ' AND '; }
                                    $search .= '`'.$aColumns[$i]."` <= $right ";
                                }
                                break;
                            case 'date-range':
                            case 'time-range':
                            case 'datetime-range':
                                list( $left, $right ) = explode('~', $_POST['columns'][$i]['search']['value']);
                                $date_format = '';
                                if( $wdtParameters['filter_types'][$aColumns[$i]] != 'time-range' ){
                                    $date_format = str_replace('m', '%m', get_option('wdtDateFormat'));
                                    $date_format = str_replace('M', '%M', $date_format);
                                    $date_format = str_replace('Y', '%Y', $date_format);
                                    $date_format = str_replace('y', '%y', $date_format);
                                    $date_format = str_replace('d', '%d', $date_format);
                                }
                                if( $wdtParameters['filter_types'][$aColumns[$i]] == 'datetime-range'
                                    || $wdtParameters['filter_types'][$aColumns[$i]] == 'time-range' ){
                                    $date_format .= ' '.get_option('wdtTimeFormat');
                                    $date_format = str_replace( 'H', '%H', $date_format );
                                    $date_format = str_replace( 'h', '%h', $date_format );
                                    $date_format = str_replace( 'i', '%i', $date_format );
                                    $date_format = str_replace( 'A', '%p', $date_format );
                                }
                                if( $left && $right ){
                                    $search .= '`'.$aColumns[$i]."` BETWEEN STR_TO_DATE('$left', '$date_format') AND STR_TO_DATE('$right', '$date_format') ";
                                }elseif($left){
                                    $search .= '`'.$aColumns[$i]."` >= STR_TO_DATE('$left', '$date_format') ";
                                }elseif($right){
                                    $search .= '`'.$aColumns[$i]."` <= STR_TO_DATE('$right', '$date_format') ";
                                }
                                break;
                            case 'select':
                                $search .= '`'.$aColumns[$i]."` = '".addslashes( $_POST['columns'][$i]['search']['value'] )."' ";
                                break;
                            case 'checkbox':
                                $checkboxSearches = explode('$|^', $_POST['columns'][$i]['search']['value'] );
                               	// Trim regex parts for first and last one
                               	$checkboxSearches[0] =  substr( $checkboxSearches[0], 1 );
                               	if( count( $checkboxSearches ) > 1 ){
                                    $checkboxSearches[count($checkboxSearches)-1] =  substr( $checkboxSearches[count($checkboxSearches)-1], 0, -1 );
                                }else{
                                    $checkboxSearches[0] =  substr( $checkboxSearches[0], 0, -1 );
                                }
                                $j = 0;
                                $search .= " (";
                                foreach($checkboxSearches as $checkboxSearch){
                                    if($j > 0){
                                        $search .= " OR ";
                                    }
                                    $search .= '`'.$aColumns[$i]."` = '".addslashes($checkboxSearch)."' ";
                                    $j++;
                                }
                                $search .= ") ";
                                break;
                            case 'text':
                            default:
                                $search .= '`'.$aColumns[$i]."` LIKE '%".addslashes( $_POST['columns'][$i]['search']['value'] )."%' ";
                        }
                    }
                }

            }

        }

        // Add the filtering by user ID column, if requested
        if( $this->_onlyOwnRows ){
            $userIdColumnCondition =  '`'.$this->_userIdColumn.'` = ' . get_current_user_id();
            $whereInsertIndex = count( $query );
            // Detect where to insert the string
            if( false !== stripos( $query, 'WHERE' ) ){
                // If WHERE is already present in the query
                $query = substr_replace( $query, ' '.$userIdColumnCondition.' AND', stripos( $query, 'WHERE' )+5, 0 );
            }else{
                // If WHERE is not present
                if( false !== stripos( $query, 'LIMIT' )  ){
                    // If LIMIT is present
                    $query = substr_replace( $query, ' WHERE '.$userIdColumnCondition.' ', stripos( $query, 'LIMIT' ), 0 );
                }else{
                    $query .= " WHERE ".$userIdColumnCondition;
                }
            }
        }

        // The serverside return scenario
        if(isset($_GET['action']) && ($_GET['action'] == 'get_wdtable')){

	        /**
	         * 1. Forming the query
	         */
	        $query = preg_replace('/SELECT /i', 'SELECT SQL_CALC_FOUND_ROWS ', $query, 1);
	        if( $search ){
                    if( stripos( $query, 'WHERE' ) ){
                        $query = substr_replace( $query, ' '.$search.' AND', stripos( $query, 'WHERE' )+5, 0 );
                    }else{
                        $query .= ' WHERE '.$search;
                    }
	        }
	        $query .= ' '.$orderby;
	        $query .= ' '.$limit;

	        $query = apply_filters( 'wpdatatables_filter_mysql_query', $query, $this->getWpId() );

	        /**
	         * 2. Executing the queries
	         */
	            // The main query
                // Prepare query - replace all duplicated spaces, newlines, etc.
                $query = preg_replace( '!\s+!', ' ', $query );

                if(get_option('wdtUseSeparateCon')){
                    $main_res_dataRows = $this->_db->getAssoc( $query, $queryParams );
                }else{
                    // querying using the WP driver otherwise
                    $main_res_dataRows = $wpdb->get_results( $query, ARRAY_A );
                }
                // result length after filtering
                if(get_option('wdtUseSeparateCon')){
                    $res_length = $this->_db->getField('SELECT FOUND_ROWS()');
                }else{
                    // querying using the WP driver otherwise
                    $res_length = $wpdb->get_row( 'SELECT FOUND_ROWS()', ARRAY_A );
                    $res_length = $res_length['FOUND_ROWS()'];
                }
                // total data length
                // get the table name
                $table_title = substr( $query, strpos(strtolower($query), 'from')+5);
                $table_title = substr($table_title, 0, strpos($table_title, ' '));
                $table_title = trim( $table_title );
                if(get_option('wdtUseSeparateCon')){
                        $total_length_query = 'SELECT COUNT(*) FROM '.$table_title;
                        // If "Only own rows" options is defined, do not count other user's rows
                        if( isset( $userIdColumnCondition ) ){
                            $total_length_query .= ' WHERE '.$userIdColumnCondition;
                        }
                        $total_length = $this->_db->getField( $total_length_query );
                }else{
                        // querying using the WP driver otherwise
                        $total_length_query = 'SELECT COUNT(*) as cnt_total FROM '.$table_title;
                        // If "Only own rows" options is defined, do not count other user's rows
                        if( isset( $userIdColumnCondition ) ){
                            $total_length_query .= ' WHERE '.$userIdColumnCondition;
                        }
                        $total_length = $wpdb->get_row( $total_length_query, ARRAY_A );
                        $total_length = $total_length['cnt_total'];
                }

                /**
                 * 3. Forming the output
                 */
                // base array
                $output = array(
                        "draw" => intval($_POST['draw']),
                        "recordsTotal" => $total_length,
                        "recordsFiltered" => $res_length,
                        "data" => array()
                );


                // create the supplementary array of column objects
                // which we will use for formatting
                $col_objs = array();
                foreach($wdtParameters['data_types'] as $dataColumn_key=>$dataColumn_type){
                    $tableColumnClass = static::$_columnClass;
                    $col_objs[$dataColumn_key] = $tableColumnClass::generateColumn( $dataColumn_type, array('title' => $wdtParameters['column_titles'][$dataColumn_key]) );
                    $col_objs[$dataColumn_key]->setInputType( $wdtParameters['input_types'][$dataColumn_key] );
                    if( $dataColumn_type == 'int' && in_array( $dataColumn_key, $wdtParameters['skip_thousands']  )  ){
                        $col_objs[$dataColumn_key]->disableThousandsSeparator();
                    }
                }
                // reformat output array and reorder as user wanted
                if( !empty( $main_res_dataRows ) ){
                        foreach( $main_res_dataRows as $res_row ){
                            $row = array();
                            foreach( $wdtParameters['column_order'] as $dataColumn_key ){
                                if( $wdtParameters['data_types'][$dataColumn_key] == 'formula' ){
                                    try{
                                        $formulaVal =
                                            self::solveFormula(
                                                $wdtParameters['column_formulas'][$dataColumn_key],
                                                $this->detectHeadersInFormula(
                                                        $wdtParameters['column_formulas'][$dataColumn_key],
                                                        array_keys( $wdtParameters['data_types'] )
                                                ),
                                                $res_row
                                            );
                                        $row[$dataColumn_key] = apply_filters(
                                            'wpdatatables_filter_cell_output',
                                            $col_objs[$dataColumn_key]->returnCellValue( $formulaVal ),
                                            $this->_wpId,
                                            $dataColumn_key
                                        );
                                    }catch(Exception $e){
                                        $row[$dataColumn_key] = 0;
                                    }
                                }else{
                                    $row[$dataColumn_key] = apply_filters( 'wpdatatables_filter_cell_output', $col_objs[$dataColumn_key]->returnCellValue($res_row[$dataColumn_key]), $this->_wpId, $dataColumn_key );
                                }
                            }
                            $output['data'][] = $this->formatAjaxQueryResultRow( $row );
                        }
                }

                // If SUM requested for some rows
                if( !empty( $this->_sumColumns ) ){
                    // Remove the order by and limit parts
                    if( strripos( $query, 'LIMIT' ) !== false ){
                        $sum_query = substr( $query, 0, strripos( $query, 'LIMIT' ) );
                    }else{
                        $sum_query = $query;
                    }
                    if( strripos( $query, 'ORDER BY' ) !== false ){
                        $sum_query = substr( $sum_query, 0, strripos( $sum_query, 'ORDER BY' ) );
                    }
                    // generate the query for sum
                    if( strpos( $query, '*' ) === FALSE ){
                        foreach( $this->_sumColumns as $column_title ){
                            $sum_query = preg_replace( "/{$column_title}/", 'SUM(`'.$column_title.'`) AS `'.$column_title.'`', $sum_query, 1 );
                        }
                    }else{
                        $select_sum_arr = array();
                        foreach( $this->_sumColumns as $column_title ){
                            $select_sum_arr[] = 'SUM(`'.$column_title.'`) AS `'.$column_title.'`';
                        }
                        $sum_query = preg_replace( '/\*/', implode( $select_sum_arr, ', ' ), $sum_query, 1 );
                    }
                    // execute query
                    if(get_option('wdtUseSeparateCon')){
                        $sum_row = $this->_db->getRow( $sum_query, $queryParams );
                    }else{
                        // querying using the WP driver otherwise
                        $sum_row = $wpdb->get_row( $sum_query, ARRAY_A );
                    }
                    foreach( $this->_sumColumns as $column_title ){
                        if( is_null( $sum_row[$column_title] ) ){ $sum_row[$column_title] = 0; }
                        $output['sum_columns_values'][$column_title] = $col_objs[$column_title]->returnCellValue( $sum_row[$column_title] );
                    }
                }

                /**
                 * 4. Returning the result
                 */
                return json_encode($output);
        }else{

            // Getting the query result
            // getting by own SQL driver if the user wanted a separate connection
            if(get_option('wdtUseSeparateCon')){
                $query = apply_filters( 'wpdatatables_filter_mysql_query', $query, $this->getWpId() );
                $res_dataRows = $this->_db->getAssoc($query, $queryParams);
                $mysql_error = $this->_db->getLastError();
            }else{
                // querying using the WP driver otherwise
                $query = apply_filters( 'wpdatatables_filter_mysql_query', $query, $this->getWpId() );
                $res_dataRows = $wpdb->get_results( $query, ARRAY_A );
                $mysql_error = $wpdb->last_error;
            }

            // If this is the table initialization from WP-admin, and no data is returned, throw an exception
            if( $init_read && empty( $res_dataRows ) ){
                $msg = __( 'No data fetched! ', 'wpdatatables' );
                $msg .= '<br/>' . __( 'Rendered query: ', 'wpdatatables' ) . '<strong>' . $query.'</strong><br/>';
                if( !empty( $mysql_error ) ){
                    $msg .= __(' MySQL said: ', 'wpdatatables').$mysql_error;
                }
                throw new Exception( $msg );
            }

            // Sending the array to arrayBasedConstruct
            return $this->arrayBasedConstruct( $res_dataRows, $wdtParameters );

        }
    }
    //[<--/ Full version -->]//

    /**
     * Formatting row data structure for ajax display table
     * @param $row key => value pairs as column name and cell value of a row
     * @return array formatted row
     */
    protected function formatAjaxQueryResultRow( $row ) {
        return array_values( $row );
    }


    public function jsonBasedConstruct( $json, $wdtParameters = array() ) {
        $json = WDTTools::applyPlaceholders( $json );
        $json = WDTTools::curlGetData( $json );
        $json = apply_filters( 'wpdatatables_filter_json', $json, $this->getWpId() );
	return $this->arrayBasedConstruct(json_decode($json, true), $wdtParameters);
    }

    public function XMLBasedConstruct( $xml, $wdtParameters = array() ) {
        if(!$xml) {
            throw new WDTException('File you provided cannot be found.');
        }
        if(strpos($xml, '.xml')===false){
            throw new WDTException('Non-XML file provided!');
        }
        $xml = WDTTools::applyPlaceholders( $xml );
        $XMLObject = simplexml_load_file($xml);
        $XMLObject = apply_filters( 'wpdatatables_filter_simplexml', $XMLObject, $this->getWpId() );
        $XMLArray = WDTTools::convertXMLtoArr($XMLObject);
        foreach($XMLArray as &$xml_el){
            if( is_array($xml_el) && array_key_exists('attributes', $xml_el)) {
                $xml_el = $xml_el['attributes'];
            }
        }
        return $this->arrayBasedConstruct( $XMLArray, $wdtParameters );
    }

    public function excelBasedConstruct( $xls_url, $wdtParameters = array() ) {
    	ini_set("memory_limit", "2048M");
        if(!$xls_url) {
            throw new WDTException('Excel file not found!');
        }
        if(!file_exists($xls_url)){
            throw new WDTException('Provided file '.stripcslashes($xls_url).' does not exist!');
        }
        require_once(WDT_ROOT_PATH.'/lib/phpExcel/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $format = 'xls';
        if(strpos(strtolower($xls_url), '.xlsx')){
            $objReader = new PHPExcel_Reader_Excel2007();
            $objReader->setReadDataOnly(true);
        }elseif(strpos(strtolower($xls_url), '.xls')){
            $objReader = new PHPExcel_Reader_Excel5();
            $objReader->setReadDataOnly(true);
        }elseif(strpos(strtolower($xls_url), '.ods')){
            $format = 'ods';
            $objReader = new PHPExcel_Reader_OOCalc();
            $objReader->setReadDataOnly(true);
        }elseif(strpos(strtolower($xls_url), '.csv')){
            $format = 'csv';
            $objReader = new PHPExcel_Reader_CSV();
        }else{
            throw new WDTException('File format not supported!');
        }
        $objPHPExcel = $objReader->load($xls_url);
        $objWorksheet = $objPHPExcel->getActiveSheet();
		$highestRow = $objWorksheet->getHighestRow();
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
                    if( !empty( $wdtParameters['data_types'][$dataColumnHeading] ) ){
                        if( in_array( $wdtParameters['data_types'][$dataColumnHeading], array( 'date', 'datetime', 'time' ) ) ) {
                            if ($format == 'xls') {
                                $namedDataArray[$r][$dataColumnHeading] = PHPExcel_Shared_Date::ExcelToPHP($dataRow[$row][$dataColumnIndex]);
                            } elseif ($format == 'csv') {
                                $namedDataArray[$r][$dataColumnHeading] = strtotime( str_replace('/', '-', $dataRow[$row][$dataColumnIndex] ) );
                            }
                        }
		            }
		        }
		    }
		}

		// Let arrayBasedConstruct know that dates have been converted to timestamps
		$wdtParameters['dates_detected'] = true;

		$namedDataArray = apply_filters( 'wpdatatables_filter_excel_array', $namedDataArray, $this->getWpId(), $xls_url );

        return $this->arrayBasedConstruct($namedDataArray, $wdtParameters);
    }
    
    protected function _renderWithJSAndStyles() {
        $tpl = new PDTTpl();
        $minified_js = get_option('wdtMinifiedJs');

	if(WDT_INCLUDE_DATATABLES_CORE){
	     wp_register_script('datatables',WDT_JS_PATH.'jquery-datatables/jquery.dataTables.min.js',array('jquery'));
	     wp_enqueue_script('datatables');
	}

	if($this->TTEnabled()){
	    wp_enqueue_script('wpdt-buttons', WDT_JS_PATH.'export-tools/dataTables.buttons.js', array('jquery','datatables'));
	    wp_enqueue_script('wpdt-jszip', WDT_JS_PATH.'export-tools/jszip.js', array('jquery','datatables'));
	    wp_enqueue_script('wpdt-dfmake', WDT_JS_PATH.'export-tools/pdfmake.js', array('jquery','datatables'));
	    wp_enqueue_script('wpdt-vfs_fonts', WDT_JS_PATH.'export-tools/vfs_fonts.js', array('jquery','datatables'));
	    wp_enqueue_script('wpdt-buttons-html5', WDT_JS_PATH.'export-tools/buttons.html5.js', array('jquery','datatables'));
	    wp_enqueue_script('wpdt-button-print', WDT_JS_PATH.'export-tools/buttons.print.js', array('jquery','datatables'));
	    wp_enqueue_script('wpdt-buttonvis', WDT_JS_PATH.'export-tools/buttons.colVis.js', array('jquery','datatables'));
	}
    // Moment JS
    wp_register_script('momentjs', WDT_JS_PATH.'moment/moment.js');

	if( $minified_js ){
	    wp_register_script('wpdatatables-funcs',WDT_JS_PATH.'php-datatables/wpdatatables.funcs.min.js',array('jquery','datatables'));
	    wp_register_script('wpdatatables-rowgrouping',WDT_JS_PATH.'jquery-datatables/jquery.dataTables.rowGrouping.min.js',array('jquery','datatables'));
	}else{
	    wp_register_script('wpdatatables-funcs',WDT_JS_PATH.'php-datatables/wpdatatables.funcs.js',array('jquery','datatables'));
	    wp_register_script('wpdatatables-rowgrouping',WDT_JS_PATH.'jquery-datatables/jquery.dataTables.rowGrouping.js',array('jquery','datatables'));
	}
	wp_enqueue_script('wpdatatables-funcs');
	wp_enqueue_script('wpdatatables-rowgrouping');
        //[<-- Full version -->]//
	if($this->filterEnabled()){
	    if( $minified_js ){
		wp_register_script('wpdatatables-advancedfilter',WDT_JS_PATH.'jquery-datatables/jquery.dataTables.columnFilter.min.js');
	    }else{
		wp_register_script('wpdatatables-advancedfilter',WDT_JS_PATH.'jquery-datatables/jquery.dataTables.columnFilter.js');
	    }
	    wp_enqueue_script('wpdatatables-advancedfilter');
	}
	if($this->isEditable()){
	    wp_enqueue_script('wpdt-buttons', WDT_JS_PATH.'export-tools/dataTables.buttons.js', array('jquery','datatables'));
	    wp_register_script('jquery-maskmoney',WDT_JS_PATH.'maskmoney/jquery.maskMoney.js',array('jquery'));
	    wp_enqueue_script('jquery-maskmoney');
	    // Media upload
	    wp_enqueue_script('media-upload');
	    wp_enqueue_media();
        // Mce editor
        wp_enqueue_script('wpdatatables_tiny_mce', '/wp-includes/js/tinymce/tinymce.min.js');
        // Inline editing
        if ( $this->inlineEditingEnabled() ) {
            if ( $minified_js ) {
                wp_enqueue_script('wpdatatables-inline-editing', WDT_JS_PATH . 'wpdatatables/wpdatatables_inline_editing.min.js');
            } else {
                wp_enqueue_script('wpdatatables-inline-editing', WDT_JS_PATH . 'wpdatatables/wpdatatables_inline_editing.js');
            }
        }
	}
	if($this->isResponsive()){
	    wp_register_script('lodash',WDT_JS_PATH.'responsive/lodash.min.js');
	    if( $minified_js ){
		wp_register_script('wpdatatables-responsive',WDT_JS_PATH.'responsive/datatables.responsive.min.js');
	    }else{
		wp_register_script('wpdatatables-responsive',WDT_JS_PATH.'responsive/datatables.responsive.js');
	    }
        if ( !$this->isEditable() ) {
		wp_enqueue_script('lodash');
	    }
	    wp_enqueue_script('wpdatatables-responsive');
	}
        //[<--/ Full version -->]//
	wp_enqueue_script('jquery-effects-core');
	wp_enqueue_script('jquery-effects-fade');
	if( $minified_js ){
	    wp_register_script('wpdatatables',WDT_JS_PATH.'wpdatatables/wpdatatables.min.js',array('jquery','datatables'));
	}else{
	    wp_register_script('wpdatatables',WDT_JS_PATH.'wpdatatables/wpdatatables.js',array('jquery','datatables'));
	}
	wp_enqueue_script('wpdatatables');

	// Localization
	wp_localize_script( 'wpdatatables', 'wpdatatables_frontend_strings', WDTTools::getTranslationStrings() );
	wp_localize_script( 'wpdatatables-advancedfilter', 'wpdatatables_frontend_strings', WDTTools::getTranslationStrings() );

        $this->addCSSClass( 'data-t' );
        $tpl->setTemplate( 'wpdatatables_table_main.inc.php' );
        $tpl->addData( 'wpDataTable', $this );
        return $tpl->returnData();
    }

    public function generateTable() {

        $tpl = new PDTTpl();
        if($this->scriptsEnabled) {
            $skin = get_option('wdtBaseSkin');
            if(empty($skin)){ $skin = 'skin1'; }
            $renderSkin = $skin == 'skin1' ? WDT_ASSETS_PATH.'css/wpDataTablesSkin.css' : WDT_ASSETS_PATH.'css/wpDataTablesSkin_1.css';
            $renderSkin = apply_filters( 'wpdatatables_link_to_skin_css', $renderSkin, $this->getWpId() );

            $cssArray = array(
                'wpdatatables-min' => WDT_CSS_PATH.'wpdatatables.min.css',
                'wpdatatables-tabletools' => WDT_CSS_PATH.'TableTools.css',
                //[<-- Full version -->]//
                'wpdatatables-responsive' => WDT_ASSETS_PATH.'css/datatables.responsive.css',
                //[<--/ Full version -->]//
                'wpdatatables-skin' => $renderSkin
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
        return $return_data;
    }

    //[<-- Full version -->]//
    /**
     * Renders the chart block, which will be supposed to render in the
     * div with provided ID
     * @param string Container div ID
     */
    public function renderChart( $divId ) {
        if(!$divId){
            throw new WDTException('No div ID provided!');
        }

        do_action( 'wpdatatable_before_render_chart', $this->getWpId() );

        $tpl = new PDTTpl();
        $tpl->setTemplate( 'chart_js_template.inc.php' );
        $series_headers = array();
        $series_values = array();

        foreach($this->_chartSeriesArr as $dataColumnIndex){
            $series_headers[] = $this->getColumn($dataColumnIndex)->getTitle();
        }


        $chartProperties = array();

        $chartProperties['values'] = array();
        $chartProperties['values'][] = $series_headers;
        foreach($this->getDataRows() as $row) {
            $series_values_entry = array();
            foreach($this->_chartSeriesArr as $dataColumnIndex){
                $val = $row[$dataColumnIndex];
                if(($dataType = $this->getColumn($dataColumnIndex)->getDataType()) != 'string') {
                        if($dataType == 'date') {
                            $val = str_replace( '/', '-', $val );
                            $val = date( get_option('wdtDateFormat'), strtotime( $val ) );
                        }elseif($dataType == 'int') {
                            $val = (int)$val;
                        }elseif($dataType == 'float') {
                            $val = (float)$val;
                        }
                    $series_values_entry[] = $val;
                } else {
                    $series_values_entry[] = $val;
                }
            }
            $chartProperties['values'][] = $series_values_entry;
        }

        $chartProperties['type'] = $this->getChartType();
        $chartProperties['container'] = $divId;
        $chartProperties['options'] = array();
        $chartProperties['options']['title'] = $this->getChartTitle();
        if(!empty($this->_horAxisCol)){
            $chartProperties['options']['hAxis'] = array('title' => $this->getColumn($this->_horAxisCol)->getTitle());
        }
        if(!empty($this->_verAxisCol)){
            $chartProperties['options']['vAxis'] = array('title' => $this->getColumn($this->_verAxisCol)->getTitle());
        }
        $tpl->addData('tableId',$this->getId());
        $tpl->addData('chartProperties',json_encode( $chartProperties, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG ));
        $return_data = $tpl->returnData();

        $return_data = apply_filters( 'wpdatatables_filter_chart', $return_data, $series_headers, $series_values, $this->getWpId() );

        return $return_data;
    }

    /**
     * Prints the rendered chart
     */
    public function printChart( $divId ) {
        if (!$divId) {
            throw new WDTException('No div ID provided!');
        }
        echo $this->renderChart($divId);
    }
    //[<--/ Full version -->]//

    /**
     * Helper method which prepares the column data from values stored in DB
     */
    public function prepareColumnData( $column_data, $table_data ){
        $return_array = array(
            'column_widths' => array(),
            'column_titles' => array(),
            'column_order' => array(),
            'column_types' => array(),
            'userid_column_header' => NULL,
            'column_possible_values' => array(),
            //[<-- Full version -->]//
            'column_formulas' => array()
            //[<--/ Full version -->]//
        );
        foreach( $column_data as $column ){
            $return_array['column_order'][(int)$column->pos] = $column->orig_header;
            if( $column->display_header ){
                $return_array['column_titles'][$column->orig_header] = $column->display_header;
            }
            if( $column->width ){
                $return_array['column_widths'][$column->orig_header] = $column->width;
            }
            if( $column->column_type != 'autodetect' ){
                $return_array['column_types'][$column->orig_header] = $column->column_type;
            }
            //[<-- Full version -->]//
            if( $column->column_type == 'formula' ){
                $return_array['column_formulas'][$column->orig_header] = $column->calc_formula;
            }
            //[<--/ Full version -->]//
            if( $table_data['edit_only_own_rows']
                && ( $table_data['userid_column_id'] == $column->id ) ){
                $return_array['userid_column_header'] = $column->orig_header;
            }
            $return_array['column_possible_values'][$column->orig_header] = $column->possible_values;
        }
        return $return_array;
    }

    //[<-- Full version -->]//
    /**
     * Helper method to detect the headers that are present in formula
     */
    public function detectHeadersInFormula( $formula, $headers = null ){
        if( is_null( $headers ) ){
            $headers = $this->getColumnKeys();
        }

        return WDTTools::getColHeadersInFormula( $formula, $headers );
    }

    /**
     * Helper method to solve a formula for a given row
     */
    public static function solveFormula( $formula, $headers_in_formula, $row ){
        $vars = array();
        foreach( $headers_in_formula as $header ){
            $vars[
            str_replace(
                array( '$','_','&',' ' ),
                '',
                $header
            )
            ] = floatval( $row[$header] );
        };
        $formula = str_replace( array('$','_','&' ), '', $formula );

        require_once( WDT_ROOT_PATH.'/lib/eos/Parser.php' );
        require_once( WDT_ROOT_PATH.'/lib/eos/Math.php' );
        require_once( WDT_ROOT_PATH.'/lib/eos/Trig.php' );
        require_once( WDT_ROOT_PATH.'/lib/eos/Stack.php' );
        require_once( WDT_ROOT_PATH.'/lib/eos/AdvancedFunctions.php' );

        $parser = new \jlawrence\eos\Parser();
        return $parser->solve( $formula, $vars );
    }

    /**
     * Tries to calculate formula value for first 5 table rows (or less if table has less than 5 rows)
     * @param String $formula A string representation of formula to calculate
     * @return String A result - first
     */
    public function calcFormulaPreview( $formula ){
        $headers_in_formula = $this->detectHeadersInFormula( $formula );
        $count = count( $this->_dataRows ) > 5 ? 5 : count( $this->_dataRows );
        $result = __('Unable to calculate','wpdatatables');
        if( $count > 0 ){
            $res_arr = array();
            try {
                for ($i = 0; $i < $count; $i++) {
                    $res_arr[] = self::solveFormula($formula, $headers_in_formula, $this->_dataRows[$i]);
                }
                $result = __('Result for first 5 rows: ', 'wpdatatables') . implode(', ', $res_arr);
            }catch( Exception $e ){
                $result = __('Unable to calculate, error message: ','wpdatatables').$e->getMessage();
            }
        }
        return $result;
    }
    //[<--/ Full version -->]//

    /**
     * Helper method which populates the wpdatatables object with passed in parameters and data (stored in DB)
     */
    public function fillFromData( $table_data, $column_data ){
        if( empty( $table_data['table_type'] ) ){ return; }
        global $wdt_var1, $wdt_var2, $wdt_var3;
        // Set placeholders


        $wdt_var1 = $wdt_var1 === '' ? $table_data['var1'] : $wdt_var1;
        $wdt_var2 = $wdt_var2 === '' ? $table_data['var2'] : $wdt_var2;
        $wdt_var3 = $wdt_var3 === '' ? $table_data['var3'] : $wdt_var3;

        switch( $table_data['table_type'] ){
            //[<-- Full version -->]//
            case 'mysql' :
            case 'manual' :
                if($table_data['server_side']){
                    $this->enableServerProcessing();
                    if( !empty( $table_data['auto_refresh'] ) ){
                        $this->setAutoRefresh( (int)$table_data['auto_refresh'] );
                    }
                }
                if($table_data['editable']){
                    if( wdt_current_user_can_edit( $table_data['editor_roles'], $this->getWpId() ) ){
                        $this->enableEditing();
                    }
                }
                if($table_data['inline_editing']){
                    if( wdt_current_user_can_edit( $table_data['editor_roles'], $this->getWpId() )){
                        $this->enableInlineEditing();
                    }
                }
                if($table_data['popover_tools']){
                    if( wdt_current_user_can_edit( $table_data['editor_roles'], $this->getWpId() )){
                        $this->enablePopoverTools();
                    }
                }
                if( !empty( $table_data['edit_only_own_rows'] ) ){
                    $this->setOnlyOwnRows( true );
                    $this->setUserIdColumn( $column_data['userid_column_header'] );
                }
                $this->queryBasedConstruct($table_data['content'], array(),
                    array(
                        'data_types' => $column_data['column_types'],
                        'column_titles' => $column_data['column_titles'],
                        'column_formulas' => $column_data['column_formulas'],
                        'disable_limit' => !empty( $table_data['disable_limit'] )
                    )
                );
                break;
            //[<--/ Full version -->]//
            case 'xls':
            case 'csv':
                $this->excelBasedConstruct($table_data['content'],
                    array(
                        'data_types' => $column_data['column_types'],
                        'column_titles' => $column_data['column_titles'],
                        //[<-- Full version -->]//
                        'column_formulas' => $column_data['column_formulas']
                        //[<--/ Full version -->]//
                    )
                );
                break;
            case 'xml':
                $this->XMLBasedConstruct($table_data['content'],
                    array(
                        'data_types' => $column_data['column_types'],
                        'column_titles' => $column_data['column_titles'],
                        //[<-- Full version -->]//
                        'column_formulas' => $column_data['column_formulas']
                        //[<--/ Full version -->]//
                    )
                );
                break;
            case 'json':
                $this->jsonBasedConstruct($table_data['content'],
                    array(
                        'data_types' => $column_data['column_types'],
                        'column_titles' => $column_data['column_titles'],
                        //[<-- Full version -->]//
                        'column_formulas' => $column_data['column_formulas']
                        //[<--/ Full version -->]//
                    )
                );
                break;
            case 'serialized':
                $serialized_content = apply_filters( 'wpdatatables_filter_serialized', WDTTools::curlGetData( $table_data['content'] ), $this->_wpId );
                $array = unserialize( $serialized_content );
                $this->arrayBasedConstruct( $array,
                    array(
                        'data_types' => $column_data['column_types'],
                        'column_titles' => $column_data['column_titles'],
                        //[<-- Full version -->]//
                        'column_formulas' => $column_data['column_formulas']
                        //[<--/ Full version -->]//
                    )
                );
                break;
            //[<-- Full version -->]//
            case 'google_spreadsheet':
                $array = WDTTools::extractGoogleSpreadsheetArray( $table_data['content'] );
                $this->arrayBasedConstruct(
                    $array,
                    array(
                        'data_types' => $column_data['column_types'],
                        'column_titles'=> $column_data['column_titles'],
                        'column_formulas' => $column_data['column_formulas']
                    )
                );
                break;
            //[<--/ Full version -->]//
        }
        // Set title
        if( $table_data['title'] ){
            $this->setTitle( $table_data['title'] );
        }
        if($table_data['hide_before_load']){
            $this->hideBeforeLoad();
        }else{
            $this->showBeforeLoad();
        }
        //[<-- Full version -->]//
        // Applying form
        $this->setFilteringForm($table_data['filtering_form']);
        // Applying responsiveness
        if($table_data['responsive']){
            $this->setResponsive(true);
        }
        //[<--/ Full version -->]//
        // Applying scrollable
        if($table_data['scrollable']){
            $this->setScrollable(true);
        }
        if(!$table_data['sorting']){
            $this->sortDisable();
        }
        // Table tools
        if(!$table_data['tools']){
            $this->disableTT();
        }else{
            $this->enableTT();
            $this->_tableToolsConfig = $table_data['tabletools_config'];
        }
        // display length
        if($table_data['display_length'] != 0) {
            $this->setDisplayLength($table_data['display_length']);
        } else {
            $this->disablePagination();
        }
        if(get_option('wdtInterfaceLanguage') != ''){
            $this->setInterfaceLanguage(get_option('wdtInterfaceLanguage'));
        }
        //[<-- Full version -->]//
        // Applying filter, if enabled
        if($table_data['filtering']){
            $this->enableAdvancedFilter();
        }
        //[<--/ Full version -->]//
    }

    /**
     * Helper method that prepares the rendering rules
     */
    public function prepareRenderingRules( $column_data ){
        $columnIndex = 1;
        // Check the search values passed from URL
        if( isset($_GET['wdt_search']) ){
            $this->setDefaultSearchValue($_GET['wdt_search']);
        }

        // Define all column-dependent rendering rules
        foreach( $column_data as $column ){
            //[<-- Full version -->]//
            // Set filter types
            $this->getColumn($column->orig_header)->setFilterType($column->filter_type);
            //[<--/ Full version -->]//
            // Set CSS class
            $this->getColumn($column->orig_header)->addCSSClass($column->css_class);
            // Set visibility
            if(!$column->visible){
                $this->getColumn($column->orig_header)->hide();
            }
            // Set default value
            $this->getColumn($column->orig_header)->setDefaultValue($column->default_value);
            // Set conditional formatting rules
            if( $column->formatting_rules ){
                $this->getColumn( $column->orig_header )
                     ->setConditionalFormattingData(
                        json_decode( $column->formatting_rules )
                    );
                $this->addConditionalFormattingColumn( $column->orig_header );
            }
            //[<-- Full version -->]//
            // Set SUM columns
            if( $column->sum_column ){
                $this->addSumColumn( $column->orig_header );
            }
            // Set hiding on phones and tablets for responsiveness
            if($this->isResponsive()){
                if($column->hide_on_phones){
                    $this->getColumn($column->orig_header)->hideOnPhones();
                }
                if($column->hide_on_tablets){
                    $this->getColumn($column->orig_header)->hideOnTablets();
                }
            }
            //[<--/ Full version -->]//
            // if grouping enabled for this column, passing it to table class
            if($column->group_column){
                $this->groupByColumn($column->orig_header);
            }
            if($column->sort_column !== '0'){
                $this->setDefaultSortColumn($column->orig_header);
                if($column->sort_column == '1'){
                    $this->setDefaultSortDirection('ASC');
                }elseif($column->sort_column == '2'){
                    $this->setDefaultSortDirection('DESC');
                }
            }
            // if thousands separator is disabled pass it to the column class instance
            if( $column->skip_thousands_separator ){
                $this->getColumn($column->orig_header)->disableThousandsSeparator();
            }
            // Set ID column if specified
            if($column->id_column){
                $this->setIdColumnKey($column->orig_header);
            }
            // Set front-end editor input type
            $this->getColumn($column->orig_header)
                 ->setInputType($column->input_type);
            // Define if input cannot be empty
            $this->getColumn($column->orig_header)
                 ->setNotNull( (bool) $column->input_mandatory );
            if( $column->visible ){
                // Get display before/after and color
                $cssColumnHeader = str_replace(' ','.',$column->orig_header);
                if( $column->text_before != '' ){
                    $this->_columnsCSS .= "\n#{$this->getId()} > tbody > tr > td.{$cssColumnHeader}:before,
                                           \n#{$this->getId()} > tbody > tr.row-detail ul li.{$cssColumnHeader} span.columnValue:before
                                                { content: '{$column->text_before}' }";
                }
                if( $column->text_after != '' ){
                    $this->_columnsCSS .= "\n#{$this->getId()} > tbody > tr > td.{$cssColumnHeader}:after,
                                           \n#{$this->getId()} > tbody > tr.row-detail ul li.{$cssColumnHeader} span.columnValue:after
                                                { content: '{$column->text_after}' }";
                }
                if( $column->color != '' ){
                    $this->_columnsCSS .= "\n#{$this->getId()} > tbody > tr > td.{$cssColumnHeader}, "
                        . "#{$this->getId()} > tbody > tr.row-detail ul li.{$cssColumnHeader}, "
                        . "#{$this->getId()} > thead > tr > th.{$cssColumnHeader}, "
                        . "#{$this->getId()} > tfoot > tr > th.{$cssColumnHeader} { background-color: {$column->color} !important; }";
                }
                $columnIndex++;
            }
        }

        //[<-- Full version -->]//
        // Check the default values passed from URL
        if(isset($_GET['wdt_column_filter'])){
            foreach($_GET['wdt_column_filter'] as $fltColKey => $fltDefVal){
                $wdtCol = $this->getColumn( $fltColKey );
                if( !empty( $wdtCol ) ){
                    $this->getColumn($fltColKey)->setDefaultValue($fltDefVal);
                }
            }
        }
        //[<--/ Full version -->]//
    }

    /**
     * Returns JSON object for table description
     */
     public function getJsonDescription(){
         //[<-- Full version -->]//
        global $wdt_var1, $wdt_var2, $wdt_var3;
         //[<--/ Full version -->]//
        global $wdt_export_file_name;

     	$obj = new stdClass();
     	$obj->tableId = $this->getId();
     	$obj->selector = '#'.$this->getId();
         //[<-- Full version -->]//
     	$obj->responsive = $this->isResponsive();
     	$obj->editable = $this->isEditable();
	    $obj->inlineEditing = $this->inlineEditingEnabled();
	    $obj->popoverTools = $this->popoverToolsEnabled();
         //[<--/ Full version -->]//
     	$obj->hideBeforeLoad = $this->doHideBeforeLoad();
     	$obj->number_format = (int) (get_option('wdtNumberFormat') ? get_option('wdtNumberFormat') : 1);
     	$obj->decimal_places = (int) (get_option('wdtDecimalPlaces') ? get_option('wdtDecimalPlaces') : 2);
         //[<-- Full version -->]//
     	if($this->isEditable()){
     		$obj->fileUploadBaseUrl = site_url().'/wp-admin/admin-ajax.php?action=wdt_upload_file&table_id='.$this->getWpId();
     		$obj->adminAjaxBaseUrl =  site_url().'/wp-admin/admin-ajax.php';
     		$obj->idColumnIndex = $this->getColumnHeaderOffset($this->getIdColumnKey());
     		$obj->idColumnKey = $this->getIdColumnKey();
     	}
         //[<--/ Full version -->]//
   		$obj->spinnerSrc = WDT_ASSETS_PATH.'/img/spinner.gif';
     	$obj->groupingEnabled = $this->groupingEnabled();
     	if($this->groupingEnabled()){
	     	$obj->groupingColumnIndex = $this->groupingColumn();
     	}
     	$obj->tableWpId = $this->getWpId();
     	$obj->dataTableParams = new StdClass();
     	$obj->dataTableParams->sDom = 'BT<"clear">lftip';
         if($this->isScrollable()){
             $obj->dataTableParams->sDom = 'BT<"clear">lf<"wdtscroll"t>ip';
         }
	    $obj->dataTableParams->bSortCellsTop = false;
         //[<-- Full version -->]//
     	$obj->dataTableParams->bFilter = $this->filterEnabled();
         //[<--/ Full version -->]//
     	if($this->paginationEnabled()){
     		$obj->dataTableParams->bPaginate = true;
     		$obj->dataTableParams->aLengthMenu = json_decode('[[10,25,50,100,-1],[10,25,50,100,"All"]]');
     		$obj->dataTableParams->iDisplayLength = (int)$this->getDisplayLength();
     	}else{
     		$obj->dataTableParams->bPaginate = false;
     		if($this->groupingEnabled()){
     			$obj->dataTableParams->aaSortingFixed = json_decode('[['.$this->groupingColumn().', "asc"]]');
     		}
     	}
     	$obj->dataTableParams->columnDefs = json_decode('['.$this->getColumnDefinitions().']');
     	$obj->dataTableParams->bAutoWidth = false;

        if( !is_null( $this->getDefaultSortColumn() ) ){
            $obj->dataTableParams->order = json_decode('[[' . $this->getDefaultSortColumn() . ', "' . strtolower($this->getDefaultSortDirection()) . '" ]]');
        }else{
            $obj->dataTableParams->order = json_decode('[[0,"asc"]]');
        }

         if($this->sortEnabled()){
             $obj->dataTableParams->ordering = true;
         }else{
             $obj->dataTableParams->ordering = false;
         }

     	if($this->getInterfaceLanguage()){
            $obj->dataTableParams->oLanguage = json_decode(file_get_contents($this->getInterfaceLanguage()));
     	}

	if ( empty($wdt_export_file_name) ) {
        if( !empty( $this->_title ) ){
            $wdt_export_file_name = $this->_title;
        }else{
            $wdt_export_file_name = 'wpdt_export';
        }
	}

	if($this->TTEnabled()){
	    $obj->dataTableParams->buttons = array();
        if( !empty($this->_tableToolsConfig['columns'] ) ){
            $obj->dataTableParams->buttons[] =
                array(
                    'extend' => 'colvis',
                    'className' => 'DTTT_button DTTT_button_colvis',
                    'text' => __('Columns','wpdatatables')
                );
        }
        if( !empty($this->_tableToolsConfig['print'] ) ){
            $obj->dataTableParams->buttons[] =
                array(
                    'extend' => 'print',
                    'exportOptions' => array('columns' => ':visible'),
                    'className' => 'DTTT_button DTTT_button_print',
                    'text' => __('Print','wpdatatables')
                );
        }
        if( !empty($this->_tableToolsConfig['excel'] ) ) {
            $obj->dataTableParams->buttons[] =
                array(
                    'extend' => 'excelHtml5',
                    'exportOptions' => array('columns' => ':visible'),
                    'className' => 'DTTT_button DTTT_button_xls',
                    'title' => $wdt_export_file_name,
                    'text' => __('Excel','wpdatatables')
                );
        }
        if( !empty($this->_tableToolsConfig['csv'] ) ) {
            $obj->dataTableParams->buttons[] =
                array(
                    'extend' => 'csvHtml5',
                    'exportOptions' => array('columns' => ':visible'),
                    'className' => 'DTTT_button DTTT_button_csv',
                    'title' => $wdt_export_file_name,
                    'text' => __('CSV','wpdatatables')
                );
        }
        if( !empty($this->_tableToolsConfig['copy'] ) ) {
            $obj->dataTableParams->buttons[] =
                array(
                    'extend' => 'copyHtml5',
                    'exportOptions' => array('columns' => ':visible'),
                    'className' => 'DTTT_button DTTT_button_copy',
                    'title' => $wdt_export_file_name,
                    'text' => __('Copy','wpdatatables')
                );
        }
        if( !empty($this->_tableToolsConfig['pdf'] ) ) {
            $obj->dataTableParams->buttons[] =
                array(
                    'extend' => 'pdfHtml5',
                    'exportOptions' => array('columns' => ':visible'),
                    'className' => 'DTTT_button DTTT_button_pdf',
                    'orientation' => 'portrait',
                    'title' => $wdt_export_file_name,
                    'text' => __('PDF','wpdatatables')
                );
        }
    }

         //[<-- Full version -->]//
	if($this->isEditable()) {
	    ( !isset($obj->dataTableParams->buttons) ) ? $obj->dataTableParams->buttons = array() : '';
	    array_push (
            $obj->dataTableParams->buttons,
		    array(
                'text' => __('New', 'wpdatatables'),
                'className' => 'new_table_entry DTTT_button DTTT_button_new'
			),
		    array (
                'text' => __('Edit', 'wpdatatables'),
                'className' => 'edit_table DTTT_button DTTT_button_edit',
                'enabled' => false
			),
		    array (
                'text' => __('Delete', 'wpdatatables'),
                'className' => 'delete_table_entry DTTT_button DTTT_button_delete',
                'enabled' => false
		    )
	    );
	}
         //[<--/ Full version -->]//

        if( !isset( $obj->dataTableParams->buttons ) ){
            $obj->dataTableParams->buttons = array();
        }

         //[<-- Full version -->]//
        $obj->dataTableParams->bProcessing = false;
     	if($this->serverSide()){
            $obj->serverSide = true;
            $obj->autoRefreshInterval = $this->getRefreshInterval();
            $obj->dataTableParams->serverSide = true;
            $obj->processing = true;
            $obj->dataTableParams->ajax = array(
                'url' => site_url().'/wp-admin/admin-ajax.php?action=get_wdtable&table_id='.$this->getWpId(),
                'type' => 'POST'
            );
            if( isset( $wdt_var1 ) && $wdt_var1 !== '' ){
                $obj->dataTableParams->ajax['url'] .= '&wdt_var1='.urlencode( $wdt_var1 );
            }
            if( isset( $wdt_var2 ) && $wdt_var2 !== '' ){
                $obj->dataTableParams->ajax['url'] .= '&wdt_var2='.urlencode( $wdt_var2 );
            }
            if( isset( $wdt_var3 ) && $wdt_var3 !== '' ){
                $obj->dataTableParams->ajax['url'] .= '&wdt_var3='.urlencode( $wdt_var3 );
            }
            $obj->fnServerData = true;
     	}else{
            $obj->serverSide = false;
     	}
         //[<--/ Full version -->]//
        $obj->dataTableParams->sPaginationType = 'full_numbers';
        $obj->columnsFixed = 0;
         //[<-- Full version -->]//
     	if(!$this->getNoData() && $this->advancedFilterEnabled()){
            $obj->advancedFilterEnabled = true;
            $obj->advancedFilterOptions = array();
            if(get_option('wdtRenderFilter') == 'header'){
                    $obj->advancedFilterOptions['sPlaceHolder'] = "head:before";
            }
            if($this->getFilteringForm()){
                    $obj->externalFilter = true;
            }else{
                    $obj->externalFilter = false;
            }
            $obj->advancedFilterOptions['aoColumns'] = json_decode('['.$this->getColumnFilterDefs().']');
            $obj->advancedFilterOptions['bUseColVis'] = true;
     	}else{
            $obj->advancedFilterEnabled = false;
     	}
        if( !empty( $this->_sumColumns ) ){
            $obj->has_sum_columns = true;
            $obj->sum_columns = $this->_sumColumns;
        }
        if( !empty( $this->_conditionalFormattingColumns ) ){
            $obj->conditional_formatting_columns = $this->_conditionalFormattingColumns;
        }
         //[<--/ Full version -->]//
        $init_format = get_option('wdtDateFormat');
        $datepick_format = str_replace('d','dd',$init_format);
        $datepick_format = str_replace('m','mm',$datepick_format);
        $datepick_format = str_replace('Y','yy',$datepick_format);

        $obj->timeFormat = get_option('wdtTimeFormat');
        $obj->timepickRange = get_option('wdtTimepickerRange') ? intval( get_option('wdtTimepickerRange') ) : 5;
     	$obj->datepickFormat = $datepick_format;

     	if(get_option('wdtTabletWidth')){
     		$obj->tabletWidth = get_option('wdtTabletWidth');
     	}
     	if(get_option('wdtMobileWidth')){
     		$obj->mobileWidth = get_option('wdtMobileWidth');
     	}

        $obj->dataTableParams->oSearch = array( 'bSmart' => false, 'bRegex' => false, 'sSearch' => $this->getDefaultSearchValue() );

     	$obj = apply_filters( 'wpdatatables_filter_table_description', $obj, $this->getWpId() );

     	return json_encode( $obj, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG );
     }

}

?>
