<?php

add_filter('essb_js_buffer_footer', array('ESSB_Dynamic_JS_Builder', 'register_footer_custom_code'));
add_filter('essb_js_buffer_header', array('ESSB_Dynamic_JS_Builder', 'register_header_custom_code'));

/**
 * Hold and register custom javascript by the plugin
 * 
 * @author appscreo
 * @since 8.0
 */
class ESSB_Dynamic_JS_Builder {
    /**
     * Javascript code added to the header of the website
     * @var array
     */
    private static $header_code = array();
    
    /**
     * Javascript code added to the footer of the website
     * @var array
     */
    private static $footer_code = array();
    
    /**
     * Generate the footer javascript code (all)
     * @return string
     */
    private static function compile_footer_code() {
        $output = '';
        
        foreach (self::$footer_code as $key => $code) {
            $output .= $code;
        }
        
        $customizer_js_footer = essb_option_value('customizer_js_footer');
        if ($customizer_js_footer != '') {
            $customizer_js_footer = stripslashes ( $customizer_js_footer );
            $output .= $customizer_js_footer;
        }
        
        return $output;
    }
    
    /**
     * Generate header javascript code (all)
     * @return string
     */
    private static function compile_header_code() {
        $output = '';
        
        foreach (self::$header_code as $key => $code) {
            $output .= $code;
        }
                
        return $output;
    }
    
    public static function push_footer_code($key, $code) {
        self::$footer_code[$key] = $code;
    }
    
    public static function push_header_code($key, $code) {
        self::$header_code[$key] = $code;
    }
    
    public static function register_header_custom_code($buffer = '') {
        $custom_code = self::compile_header_code();
        if ($custom_code != '') {
            $custom_code = self::minify_spaces($custom_code);
            $buffer .= $custom_code;
        }
        
        return $buffer;
    }
    
    /**
     * Register the custom Javascript code
     * 
     * @param string $buffer
     * @return string
     */
    public static function register_footer_custom_code($buffer = '') {        
        $custom_code = self::compile_footer_code();
        if ($custom_code != '') {
            $custom_code = self::minify_spaces($custom_code);
            $buffer .= $custom_code;
        }
        
        return $buffer;        
    }
    
    public static function minify_spaces($code = '') {
        $code = trim(preg_replace('/\s+/', ' ', $code));
        
        return $code;
    }
}