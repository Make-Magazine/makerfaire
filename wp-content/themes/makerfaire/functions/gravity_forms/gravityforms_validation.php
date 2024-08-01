<?php
//add custom field validation messages
add_filter( 'gform_field_validation', 'gf_user_defined_price_validation', 10, 4 );
function gf_user_defined_price_validation( $result, $value, $form, $field ) {
    if ( 'website' === $field->type) {        
        if($value !=''){
            if (strpos($value, "https://") === false && strpos($value, "http://") === false) {            
                $result['is_valid'] = false;
                $result['message'] = 'Website must start with http:// or https://. Did you mean https://'.$value.'?';
            }
        }        
    }
    
    return $result;
}