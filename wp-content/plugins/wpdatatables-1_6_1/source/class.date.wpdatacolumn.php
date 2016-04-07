<?php

/**
 * Class IntColumn is a child column class used
 * to describe columns with float numeric content
 *
 * @author Alexander Gilmanov
 *
 * @since May 2012
 */

class DateWDTColumn extends WDTColumn {
	
    protected $_jsDataType = 'date';
    protected $_dataType = 'date';
    
    public function __construct( $properties = array () ) {
		parent::__construct( $properties );
		$this->_dataType = 'date';
		
		switch(get_option('wdtDateFormat')){
			case 'd/m/Y':
			case 'd.m.Y':
			case 'd-m-Y':
			case 'd.m.y':
			case 'd-m-y':
				$this->_jsDataType = 'date-eu';
				break;
			case 'd-m-Y':
				$this->_jsDataType = 'date-dd-mmm-yyyy';
				break;
		}
				
    }
    
    public function prepareCellOutput( $content ) {
        if(!is_array($content)){
            if( !empty($content) && ( $content != '0000-00-00' ) ){
                $content = str_replace('/', '-', $content);
                $formattedValue = date( get_option('wdtDateFormat'), strtotime($content) );
            }else{
                $formattedValue = '';
            }
        }else{
            $content['value'] = str_replace('/', '-', $content['value']);
            $formattedValue = date( get_option('wdtDateFormat'), strtotime($content['value']) );
        }
        $formattedValue = apply_filters('wpdatatables_filter_date_cell', $formattedValue);
        return $formattedValue;
    }
    
    public function getGoogleChartColumnType(){
        return 'date';
    }
    
}


?>