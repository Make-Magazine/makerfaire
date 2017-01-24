<?php
class StringWDTColumn extends WDTColumn {
	
    protected $_dataType = 'string';
    protected $_jsDataType = 'string';
    
    public function __construct( $properties = array () ) {
        parent::__construct( $properties );
        $this->_dataType = 'string';
    }
    
    public function prepareCellOutput( $content ) {
    	
        $value = str_replace( "\n","<br/>", $content );
        if(WDT_PARSE_SHORTCODES_IN_STRINGS){
                $content = do_shortcode( $content );
        }
        $content = apply_filters( 'wpdatatables_filter_string_cell', $content );
        return $content;
    }    
    
}

?>