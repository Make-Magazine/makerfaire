<?php
class EmailWDTColumn extends WDTColumn {
	
    protected $_jsDataType = 'html';
    protected $_dataType = 'string';
        
    public function __construct( $properties = array () ) {
		parent::__construct( $properties );
		$this->_dataType = 'email';
    }
    
    public function prepareCellOutput( $content ) {
    	if(strpos($content,'||') !== false){
            $link = '';
            list($link,$content) = explode('||',$content);
            $formattedValue = "<a href='mailto:{$link}'>{$content}</a>";
    	}else{
            $formattedValue = "<a href='mailto:{$content}'>{$content}</a>";
    	}
    	$formattedValue = apply_filters( 'wpdatatables_filter_email_cell', $formattedValue );
    	return $formattedValue;
    }    
    
}


?>