<?php

/**
 * Read boolean value
 * 
 * @param unknown $param
 * @param unknown $options
 * @return boolean
 */
function essb_option_bool_value ($param, $options = null) {
    return ESSB_Plugin_Options::is($param, $options);
}

/**
 * Read other value
 *
 * @param unknown $param            
 * @param unknown $options            
 * @return string
 */
function essb_option_value ($param, $options = null) {
    return ESSB_Plugin_Options::get($param, $options);
}

/**
 * Return an option non-boolean value with applied sanitize_text_field function
 *
 * @param unknown_type $param            
 * @param unknown_type $options            
 */
function essb_sanitize_option_value ($param, $options = null) {
    return sanitize_text_field(essb_option_value($param, $options));
}

/**
 * Check if a value exists inside an array
 *
 * @param unknown $values            
 * @param unknown $key            
 * @return boolean
 */
function essb_exist_in_array ($values, $key) {
    if (! empty($values) && is_array($values)) {
        return in_array($key, $values);
    }
    else {
        return false;
    }
}

/**
 * Remove a key from an associative array
 *
 * @param array $values            
 * @param string $remove_key            
 * @return unknown
 */
function essb_remove_key_from_associative_array ($values = array(), $remove_key = '') {
    if (in_array($remove_key, $values)) {
        if (($key = array_search($remove_key, $values)) !== false) {
            unset($values[$key]);
        }
    }
    
    return $values;
}

/**
 * Legacy callback
 *
 * @param unknown $social_networks            
 * @param string $remove_key            
 * @return unknown
 */
function essb_remove_network_from_list ($social_networks, $remove_key = '') {
    return essb_remove_key_from_associative_array($social_networks, $remove_key);
}

/**
 * @param unknown $object
 * @param unknown $param
 * @param string $default
 * @return unknown|string
 */
function essb_object_value ($object, $param, $default = '') {
    return isset($object[$param]) ? $object[$param] : ($default != '' ? $default : '');
}

/**
 * @param unknown $object
 * @param unknown $param
 * @return boolean
 */
function essb_object_bool_value ($object, $param) {
    $value = isset($object[$param]) ? $object[$param] : '';
    
    return $value == 'true';
}

/**
 * @param unknown $values
 * @return unknown[]
 */
function essb_advanced_array_to_simple_array($values) {
    $new = array();
    
    foreach ($values as $key => $text) {
        $new[] = $key;
    }
    
    return $new;
}