<?php
class WDTColumn {
    
    protected $_inputType = '';
    protected $_hiddenOnPhones = false;
    protected $_hiddenOnTablets = false;
    protected $_title;
    protected $_orig_header = '';
    private $_isVisible = true;
    private $_cssStyle;
    private $_width;
    private $_sort;
    protected $_cssClassArray;
    protected $_jsDefinitionTemplate;
    protected $_dataType;
    protected $_jsDataType = 'html';
    protected $_jsFilterType = 'text';
    protected $_possibleValues = array();
    protected $_defaultValue = '';
    protected $_textBefore = '';
    protected $_textAfter = '';
    protected $_notNull = false;
    protected $_showThousandsSeparator = true;
    protected $_conditionalFormattingData = array();
    protected $_searchable = true;

    /**
     * @return array
     */
    public function getConditionalFormattingData()
    {
        return $this->_conditionalFormattingData;
    }

    /**
     * @param array $conditionalFormattingData
     */
    public function setConditionalFormattingData( $conditionalFormattingData )
    {
        $this->_conditionalFormattingData = $conditionalFormattingData;
    }

    public function __construct( $properties = array () ) {
        $this->_cssClassArray = WDTTools::defineDefaultValue($properties, 'classes', array());
        $this->_textBefore = WDTTools::defineDefaultValue($properties, 'text_before', '');
        $this->_textAfter = WDTTools::defineDefaultValue($properties, 'text_after', '');
        $this->_sort = WDTTools::defineDefaultValue($properties, 'sort', true);
        $this->_title = WDTTools::defineDefaultValue($properties, 'title', '');
        $this->_isVisible = WDTTools::defineDefaultValue($properties, 'visible', true);
        $this->_width = WDTTools::defineDefaultValue($properties, 'width', '');
        $this->_orig_header = WDTTools::defineDefaultValue($properties, 'orig_header', '');
    }
    
    public function getTitle() {
        return $this->_title;
    }

    public function getType(){
        return $this->_dataType;
    }

    public function setColumnHeader( $header ) {
        $this->_title = $header;
    }
    
    public function isVisible() {
        return $this->_isVisible;
    }

    public function isVisibleOnMobiles() {
        return ( $this->_isVisible && !$this->_hiddenOnPhones && !$this->_hiddenOnTablets );
    }

    public function show() {
        $this->_isVisible = true;
    }
    
    public function hide() {
        $this->_isVisible = false;
    }
    
    public function getCssClassesArr() {
        return $this->_cssClassArray;
    }
    
    public function addCSSClass( $class ) {
        $this->_cssClassArray[] = $class;
    }
    
    public function getCSSClasses( ) {
        $classesStr = implode(' ', $this->_cssClassArray);
        $classesStr = apply_filters( 'wpdatatables_filter_column_cssClassArray', $classesStr, $this->_title );
        return $classesStr;
    }
    
    public function getWidth() {
        if($this->_width){
            return $this->_width;
        }else{
            return 'auto';
        }
    }
    
    public function returnCellValue( $cellContent ) {
        $cellValue = $this->prepareCellOutput( $cellContent );
        $cellValue = apply_filters( 'wpdatatables_filter_cell_val', $cellValue );
        return $cellValue;
    }
    
    public function prepareCellOutput( $content ) {
        if( is_array( $content ) ){
            return $content['value'];
        }else{
            return $content;
        }
    }
    
    public function getDataType(){
        return $this->_dataType;
    }
    
    public function getFilterType() {
        $ftype = new StdClass();
        $ftype->type = $this->_jsFilterType;
        if(in_array($ftype->type, array('select','checkbox')) && !empty($this->_possibleValues)){
            $ftype->values = $this->_possibleValues;
        }
        return $ftype;
    }
    
    public function getGoogleChartColumnType(){
        return 'string';
    }
	 
    public function setFilterType( $filterType ) {
        if(!in_array( $filterType, 
                        array( 
                            'none',
                            '',
                            'text', 
                            'number', 
                            'select', 
                            'null', 
                            'number-range', 
                            'date-range', 
                            'checkbox' 
                        ) 
                    )
                ){
            throw new WDTException('Unknown column filter type!');
        }
        if(($filterType == 'none') || ($filterType == '')){
            $filterType = 'null';
        }
        $this->_jsFilterType = $filterType;
    }
	  
    public function setPossibleValues($values) {
          if(!empty($values)) {
                $values = explode('|', $values);
                $this->_possibleValues = $values;
          }else{
                $this->_possibleValues = array();
          }
    }
	  
    public function getPossibleValues(){
        return $this->_possibleValues;
    }
	   
    public function setInputType($inputType){
        $this->_inputType = $inputType;
    }
	    
    public function getInputType(){
        return $this->_inputType;
    }
	  	
    public function hideOnPhones(){
        $this->_hiddenOnPhones = true;
    }
	  	 
    public function showOnPhones(){
        $this->_hiddenOnPhones = false;
    }

    public function hideOnTablets(){
        $this->_hiddenOnTablets = true;
    }
	  	 
    public function showOnTablets(){
        $this->_hiddenOnTablets = false;
    }
	  	 
    public function getHiddenAttr(){
        $hidden = array();
        if($this->_hiddenOnPhones){
            $hidden[] = 'phone';
        }
        if($this->_hiddenOnTablets){
            $hidden[] = 'tablet';
        }
        return implode(',',$hidden);
    }

    public function setDefaultValue( $value ){
     	if(strpos($value,'|') !== false){
            $value = explode('|',$value);
     	}
     	$this->_defaultValue = $value;
    }
     
    private function applyPlaceholders($value){
        global $wdt_var1, $wdt_var2, $wdt_var3;

        // Current user ID
        if(strpos($value, '%CURRENT_USER_ID%') !== false){
                $value = str_replace('%CURRENT_USER_ID%', get_current_user_id(), $value);
        }

        // Shortcode VAR1
        if(strpos($value, '%VAR1%') !== false){
                $value = str_replace('%VAR1%', $wdt_var1, $value);
        }

        // Shortcode VAR2
        if(strpos($value, '%VAR2%') !== false){
                $value = str_replace('%VAR2%', $wdt_var2, $value);
        }

        // Shortcode VAR3
        if(strpos($value, '%VAR3%') !== false){
                $value = str_replace('%VAR3%', $wdt_var3, $value);
        }

        return $value;
    }
     
    public function getDefaultValue(){
       $value = $this->_defaultValue;
       if(is_array($value)){
           foreach($value as &$singleValue){
               $singleValue = $this->applyPlaceholders($singleValue);
           }
       }else{
           $value = $this->applyPlaceholders($value);
       }
       return $value;
    }
    
    public function getCSSStyle() {
	return $this->_cssStyle;
    }
    
    public function setCSSStyle( $style ) {
	$this->_cssStyle = $style;
    }
    
    public function sortEnabled() {
        return $this->_sort;
    }
    
    public function sortEnable() {
        $this->_sort = true;
    }
    
    public function sortDisable() {
        $this->_sort = false;
    }

    public function searchEnable() {
        $this->_searchable = true;
    }

    public function searchDisable() {
        $this->_searchable = false;
    }


    public static function generateColumn( $wdtColumnType = 'string', $properties = array( ) ) {
        global $wdtAllowTypes;
        if( !$wdtColumnType ){ $wdtColumnType = 'string'; }
        if( !in_array( $wdtColumnType, $wdtAllowTypes )) {
            throw new WDTException( 'Wrong wpDataTable column type passed.' );
        }
        $columnObj = ucfirst($wdtColumnType) . 'WDTColumn';
        $columnFormatterFileName =  'class.' . strtolower( $wdtColumnType ). '.wpdatacolumn.php';
        require_once( $columnFormatterFileName );
        return new $columnObj( $properties );
    }
    
    public function getColumnJSON() {
        $colJsDefinition = new StdClass();
        $colJsDefinition->sType = $this->_jsDataType;
        $colJsDefinition->wdtType = $this->_dataType;
        $colJsDefinition->className = $this->getCSSClasses().' '.$this->_orig_header;
        $colJsDefinition->bVisible = $this->isVisible();
        $colJsDefinition->bSortable = $this->sortEnabled();
        $colJsDefinition->searchable = $this->_searchable;
        $colJsDefinition->InputType = $this->_inputType;
        $colJsDefinition->name = $this->_orig_header;
        $colJsDefinition->origHeader = $this->_orig_header;
        $colJsDefinition->notNull = $this->_notNull;
        $colJsDefinition->conditionalFormattingRules = $this->getConditionalFormattingData();
        if($this->_width != ''){
            $colJsDefinition->sWidth = $this->_width;
        }
        $colJsDefinition = apply_filters( 'wpdatatables_filter_column_js_definition', $colJsDefinition, $this->_title );
        return $colJsDefinition;
    }
    
    public function setWidth( $width ) {
        $this->_width = $width;
    }
    
    public function setTextBefore( $text_before ){
        $this->_textBefore = $text_before;
    }
    
    public function getTextBefore(){
        return $this->_textBefore;
    }
    
    public function setTextAfter( $text_after ){
        $this->_textAfter = $text_after;
    }
    
    public function getTextAfter(){
        return $this->_textAfter;
    }
    
    public function setNotNull( $input_mandatory ){
        $this->_notNull = (bool) $input_mandatory;
    } 
    
    public function getNotNull(){
        return $this->_notNull;
    }
    
    public function enableThousandsSeparator(){
        $this->_showThousandsSeparator = true;
    }
    
    public function disableThousandsSeparator(){
        $this->_showThousandsSeparator = false;
    }
    
    public function thousandsSeparatorVisible(){
        return $this->_showThousandsSeparator;
    }
    
}

?>