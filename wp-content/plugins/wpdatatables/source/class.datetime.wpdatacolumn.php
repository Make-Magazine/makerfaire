<?php

/**
 * Class IntColumn is a child column class used
 * to describe columns with float numeric content
 *
 * @author Alexander Gilmanov
 *
 * @since May 2012
 */

class DateTimeWDTColumn extends WDTColumn {
	
    protected $_jsDataType = 'datetime';
    protected $_dataType = 'datetime';
    
    public function __construct( $properties = array () ) {
		parent::__construct( $properties );
		$this->_dataType = 'datetime';
		
		switch(get_option('wdtDateFormat')){
			case 'd/m/Y':
			case 'd.m.Y':
			case 'd-m-Y':
			case 'd.m.y':
			case 'd-m-y':
				$this->_jsDataType = 'datetime-eu';
				break;
			case 'd M Y':
				$this->_jsDataType = 'datetime-dd-mmm-yyyy';
				break;
		}

        if( get_option('wdtTimeFormat') == 'h:i A' ){
            $this->_jsDataType.='-am';
        }
    }
    
    public function prepareCellOutput( $content ) {
        if(!is_array($content)){
            if( !empty($content) && ( $content != '0000-00-00' ) ){
                $timestamp = is_int( $content ) ? $content : strtotime( str_replace('/', '-', $content ) );
                $formattedValue = date( get_option('wdtDateFormat').' '.get_option('wdtTimeFormat'), $timestamp );
            }else{
                $formattedValue = '';
            }
        }else{
            $content['value'] = str_replace('/', '-', $content['value']);
            $formattedValue = date( get_option('wdtDateFormat').' '.get_option('wdtTimeFormat'), strtotime($content['value']) );
        }
        $formattedValue = apply_filters('wpdatatables_filter_date_cell', $formattedValue);
        return $formattedValue;
    }
    
    public function getGoogleChartColumnType(){
        return 'datetime';
    }
    
}


?>