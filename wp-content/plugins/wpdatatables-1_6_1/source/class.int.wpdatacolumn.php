<?php
class IntWDTColumn extends WDTColumn {
    
    protected $_dataType = 'int';
    protected $_jsDataType = 'numeric';
    
    public function __construct( $properties = array () ) {
        parent::__construct( $properties );
        $this->_dataType = 'int';
        $this->_jsDataType = 'formatted-num';

        $number_format = get_option('wdtNumberFormat') ? get_option('wdtNumberFormat') : 1;

        $this->_jsFiltertype = 'number';
        $this->addCSSClass('numdata integer');
    }
    
    public function prepareCellOutput( $content ) {

        if( $content == '' ){ return $content; }

        $number_format = get_option('wdtNumberFormat') ? get_option('wdtNumberFormat') : 1;
        if($number_format == 1){
            $content = number_format( 
                           (int) $content, 
                           0, 
                           ',', 
                           $this->thousandsSeparatorVisible() ? '.' : ''
                        );
        }else{
            $content = number_format( 
                           (int) $content, 
                           0, 
                           '.', 
                           $this->thousandsSeparatorVisible() ? ',' : ''
                        );
        }
        $content = apply_filters( 'wpdatatables_filter_int_cell', $content );
        return $content;
    }        
    
    public function getGoogleChartColumnType(){
        return 'number';
    }    
}


?>