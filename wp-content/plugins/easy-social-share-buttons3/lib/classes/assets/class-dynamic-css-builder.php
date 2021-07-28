<?php

add_filter('essb_css_buffer_head', array('ESSB_Dynamic_CSS_Builder', 'register_dynamic_assets'));
add_filter('essb_css_buffer_head', array('ESSB_Dynamic_CSS_Builder', 'register_header_custom_code'));
add_filter('essb_css_buffer_footer', array('ESSB_Dynamic_CSS_Builder', 'register_footer_custom_code'));

/**
 * Dynamic CSS compiler and builder
 * 
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 7.2
 * 
 */
class ESSB_Dynamic_CSS_Builder {
    
    /**
     * Header fields
     * 
     * @var array
     */
    private static $header_dynamic_props = array();
    
    /**
     * Footer fields
     * 
     * @var array
     */
    private static $footer_dynamic_props = array();
    
    /**
     * Header static code (pre-build)
     * 
     * @var string
     */
    private static $header_static_code = '';
    
    /**
     * @var array
     */
    private static $header_dynamic_code = array();
    
    /**
     * Footer static code (pre-build)
     * 
     * @var string
     */
    private static $footer_static_code = '';
        
    /**
     * @var array
     */
    private static $footer_dynamic_code = array();    
    
    /**
     * Header code is already generated. If so the code will go to the footer
     * 
     * @var boolean
     */
    private static $header_generated = false;
    
    public static function register_header_custom_code($buffer) {
        $global_user_defined_css = essb_option_value('customizer_css');
        
        
        if ($global_user_defined_css != '') {
            $global_user_defined_css = stripslashes ( $global_user_defined_css );
            $buffer .= $global_user_defined_css;
        }	
        
        return $buffer;
    }
    
    public static function register_footer_custom_code($buffer) {
        $global_user_defined_css = essb_option_value('customizer_css_footer');
        
        
        if ($global_user_defined_css != '') {
            $global_user_defined_css = stripslashes ( $global_user_defined_css );
            $buffer .= $global_user_defined_css;
        }
        
        return $buffer;
    }
    
    /**
     * Call the filter to register the dynamic styles
     */
    public static function register_dynamic_assets($buffer) {
        /**
         * Click to Tweet
         */
        if (essb_option_bool_value('activate_cct_customizer')) {            
            if (!function_exists('essb_register_dynamic_cct_styles')) {
                include_once (ESSB3_HELPERS_PATH. 'assets/customizer-click-to-tweet.php');
            }
            essb_register_dynamic_cct_styles();
        }
        
        /** 
         * Share Buttons: Sidebar
         */
        if (self::should_add_position_styles('sidebar')) {
            if (!function_exists('essb_register_dynamic_share_sidebar_styles')) {
                include_once (ESSB3_HELPERS_PATH. 'assets/share-sidebar.php');
            }
            essb_register_dynamic_share_sidebar_styles();
        }
        
        /**
         * Share Buttons: Top Bar
         */
        if (self::should_add_position_styles('topbar')) {
            if (!function_exists('essb_register_dynamic_share_topbar_styles')) {
                include_once (ESSB3_HELPERS_PATH. 'assets/share-topbar.php');
            }
            essb_register_dynamic_share_topbar_styles();
        }
        
        /**
         * Share Buttons: Bottom Bar
         */
        if (self::should_add_position_styles('bottombar')) {
            if (!function_exists('essb_register_dynamic_share_bottombar_styles')) {
                include_once (ESSB3_HELPERS_PATH. 'assets/share-bottombar.php');
            }
            essb_register_dynamic_share_bottombar_styles();
        }
        
        /**
         * Share Buttons: Float from Top
         */
        if (self::should_add_position_styles('float')) {
            if (!function_exists('essb_register_dynamic_share_float_styles')) {
                include_once (ESSB3_HELPERS_PATH. 'assets/share-float.php');
            }
            essb_register_dynamic_share_float_styles();
        }
        
        /**
         * Share Buttons: Post Vertical Float
         */
        if (self::should_add_position_styles('postfloat')) {
            if (!function_exists('essb_register_dynamic_share_postfloat_styles')) {
                include_once (ESSB3_HELPERS_PATH. 'assets/share-postfloat.php');
            }

            essb_register_dynamic_share_postfloat_styles();
        }
        
        /**
         * Share Buttons: Post Bar
         * 
         * Styles will appear only if the Post Bar is enabled as position
         */
        if (essb_is_position_active('postbar')) {
            if (!function_exists('essb_register_dynamic_share_postbar_styles')) {
                include_once (ESSB3_HELPERS_PATH. 'assets/share-postbar.php');
            }
            essb_register_dynamic_share_postbar_styles();
        }
        
        /**
         * Style Customizer
         */
        if (essb_option_bool_value('customizer_is_active')) {
            if (!function_exists('essb_register_dynamic_sharebutton_styles')) {
                include_once (ESSB3_HELPERS_PATH. 'assets/customizer-share-buttons.php');
            }
            essb_register_dynamic_sharebutton_styles();
        }        
        
        /** 
         * Subscribe forms
         */
        
        if (essb_option_bool_value('activate_mailchimp_customizer') || 
            essb_option_bool_value('activate_mailchimp_customizer2') || 
            essb_option_bool_value('activate_mailchimp_customizer3') || 
            essb_option_bool_value('activate_mailchimp_customizer4') || 
            essb_option_bool_value('activate_mailchimp_customizer5') || 
            essb_option_bool_value('activate_mailchimp_customizer6') || 
            essb_option_bool_value('activate_mailchimp_customizer7') || 
            essb_option_bool_value('activate_mailchimp_customizer8') ||
            essb_option_bool_value('activate_mailchimp_customizer9')) {
           
                if (!function_exists('essb_register_dynamic_subscribe_form_styles')) {
                    include_once (ESSB3_HELPERS_PATH. 'assets/customizer-subscribe-forms.php');
                }
                
                $buffer .= essb_register_dynamic_subscribe_form_styles();
                
        }
        
        if (essb_option_bool_value('subscribe_css_deactivate_mobile')) {
            if (!function_exists('essb_register_dynamic_subscribe_mobile_hide')) {
                include_once (ESSB3_HELPERS_PATH. 'assets/customizer-subscribe-forms-mobile.php');
            }
            
            essb_register_dynamic_subscribe_mobile_hide();
        }
        
        return $buffer;
    }
    
    /**
     * @param string $position
     * @return boolean
     */
    public static function should_add_position_styles($position = '') {
        $r = true;
        
        if (essb_option_bool_value('load_css_active')) {
            $r = essb_is_position_active($position);
        }
        
        return $r;
    }
    
    /**
     * Map an option field to the settings
     * 
     * @param string $selector
     * @param string $prop
     * @param string $option_key
     * @param string $suffix
     * @param string $important
     */
    public static function map_option($selector = '', $prop = '', $option_key = '', $suffix = '', $important = false) {
        self::register_header_field($selector, $prop, $option_key, $suffix, $important, 'options');
    }
    
    public static function map_important_option($selector = '', $prop = '', $option_key = '') {
        self::register_header_field($selector, $prop, $option_key, '', true, 'options');
    }
    
    /**
     * Register dynamic header selector property
     * 
     * @param string $selector
     * @param string $prop
     * @param string $value
     * @param string $suffix
     * @param string $important
     * @param string $type (static|options)
     * @param string $min_width
     * @param string $max_width
     */
    public static function register_header_field($selector = '', $prop = '', $value = '', $suffix = '', $important = false, $type = 'static', $min_width = '', $max_width= '') {
        if (!isset(self::$header_dynamic_props[$selector])) {
            self::$header_dynamic_props[$selector] = array();
        }
        
        self::$header_dynamic_props[$selector][$prop] = array('value' => $value, 'type' => $type, 'suffix' => $suffix, 'important' => $important, 'min_width' => $min_width, 'max_width' => $max_width);
        
        // in case header is build code goes to the footer
        if (self::$header_generated) {
            self::register_footer_field($selector, $prop, $value, $suffix, $important, $type, $min_width, $max_width);
        }
    }
    
    /**
     * Register dynamic header selector property
     *
     * @param string $selector
     * @param string $prop
     * @param string $value
     * @param string $suffix
     * @param string $important
     * @param string $type (static|options)
     * @param string $min_width
     * @param string $max_width
     */
    public static function register_footer_field($selector = '', $prop = '', $value = '', $suffix = '', $important = false, $type = 'static', $min_width = '', $max_width = '') {
        if (!isset(self::$footer_dynamic_props[$selector])) {
            self::$footer_dynamic_props[$selector] = array();
        }
        
        self::$footer_dynamic_props[$selector][$prop] = array('value' => $value, 'type' => $type, 'suffix' => $suffix, 'important' => $important, 'min_width' => $min_width, 'max_width' => $max_width);
    }
    
    
    /**
     * Register custom pre-built CSS code
     * 
     * @param string $code
     * @param string $location (header|footer)
     */
    public static function register_static_code ($code = '', $location = 'header') {
        if ($location == 'header') {
            self::$header_static_code .= $code;
            
            if (self::$header_generated) {
                self::$footer_static_code .= $code;
            }
        }
        else {
            self::$footer_static_code .= $code;
        }
    }
    
    /**
     * @param string $key
     * @param string $code
     * @param string $location
     */
    public static function register_dynamic_code($key = '', $code = '', $location = 'header') {
        if ($location == 'header') {
            self::$header_dynamic_code[$key] = $code;
            
            if (self::$header_generated) {
                self::$footer_dynamic_code[$key] = $code;
            }
        }
        else {
            self::$footer_dynamic_code[$key] = $code;
        }
    }
    
    /**
     * Compile all selectors and static code to ready CSS (Header)
     * 
     * @return string
     */
    public static function compile_header() {
        self::$header_dynamic_props = apply_filters('essb/assets/dynamic_header_css', self::$header_dynamic_props);
        self::$header_static_code = apply_filters('essb/assets/static_header_css', self::$header_static_code);                     
        
        $output = '';
        
        $header_selectors = self::split_by_devices(self::$header_dynamic_props); 
                
        // Begin generation of general selectors
        $output .= self::compile_dynamic_selectors($header_selectors['global']);
        
        // Begin generation of device selectors
        foreach ($header_selectors['devices'] as $key => $device_data) {
            $output .= '@media ';
            
            if (!empty($device_data['min_width']) && !empty($device_data['max_width'])) {
                $output .= '(min-width: '.esc_attr($device_data['min_width']).'px) and (max-width: '.esc_attr($device_data['max_width']).'px)';
            }
            else if (!empty($device_data['min_width'])) {
                $output .= '(min-width: '.esc_attr($device_data['min_width']).'px)';
            }
            else if (!empty($device_data['max_width'])) {
                $output .= '(max-width: '.esc_attr($device_data['max_width']).'px)';
            }
            
            $output .= '{';
            
            $output .= self::compile_dynamic_selectors($device_data['selectors']);
            
            $output .= '}';
        }
        
        foreach (self::$header_dynamic_code as $key => $code) {
            $output .= $code;
        }
        
        $output .= self::$header_static_code;
        
        self::$header_generated = true;
        
        // Minify and sanitize
        $output = self::sanitize_css_output($output);
        $output = self::minify_spaces($output);
                
        return $output;
    }
    
    /**
     * Compile all selectors and static code to ready CSS (Footer)
     *
     * @return string
     */
    public static function compile_footer() {
        self::$footer_dynamic_props = apply_filters('essb/assets/dynamic_footer_css', self::$footer_dynamic_props);
        self::$footer_static_code = apply_filters('essb/assets/static_footer_css', self::$footer_static_code);        
        
        $output = '';
        
        $footer_selectors = self::split_by_devices(self::$footer_dynamic_props);        
        
        // Begin generation of general selectors
        $output .= self::compile_dynamic_selectors($footer_selectors['global']);
        
        // Begin generation of device selectors
        foreach ($footer_selectors['devices'] as $key => $device_data) {
            $output .= '@media ';
            
            if (!empty($device_data['min_width']) && !empty($device_data['max_width'])) {
                $output .= '(min-width: '.esc_attr($device_data['min_width']).'px) and (max-width: '.esc_attr($device_data['max_width']).'px)';
            }
            else if (!empty($device_data['min_width'])) {
                $output .= '(min-width: '.esc_attr($device_data['min_width']).'px)';
            }
            else if (!empty($device_data['max_width'])) {
                $output .= '(max-width: '.esc_attr($device_data['max_width']).'px)';
            }
            
            $output .= '{';
            
            $output .= self::compile_dynamic_selectors($device_data['selectors']);
            
            $output .= '}';
        }
        
        foreach (self::$footer_dynamic_code as $key => $code) {
            $output .= $code;
        }
        
        $output .= self::$footer_static_code;      
        
        // Minify and sanitize
        $output = self::sanitize_css_output($output);
        $output = self::minify_spaces($output);
        
        return $output;
    }
    
    /**
     * Split generated dynamic selectors by devices
     * 
     * @param array $selectors
     * @return array[]|unknown
     */
    public static function split_by_devices($selectors = array()) {
        $output = array( 'global' => array(), 'devices' => array());
        
        foreach ($selectors as $selector => $props) {
            foreach ($props as $prop => $data) {
                
                if ($data['value'] == '') {
                    continue;
                }
                        
                // This code runs no matter of the device
                if (empty($data['min_width']) && empty($data['max_width'])) {
                    if (!isset($output['global'][$selector])) {
                        $output['global'][$selector] = array();
                    }
                    $output['global'][$selector][$prop] = $data;
                }
                else {
                    $device_key = 'width-'.$data['min_width'].'-'.$data['max_width'];
                    
                    if (!isset($output['devices'][$device_key])) {
                        $output['devices'][$device_key] = array('min_width' => $data['min_width'], 'max_width' => $data['max_width'], 'selectors' => array());
                    }
                    
                    if (!isset($output['devices'][$device_key]['selectors'][$selector])) {
                        $output['devices'][$device_key]['selectors'][$selector] = array();
                    }
                    
                    $output['devices'][$device_key]['selectors'][$selector][$prop] = $data;
                }
            }
        }
        
        return $output;
    }
    
    /**
     * Compile the dynamic selectors into static CSS code
     * 
     * @param array $selectors
     * @return string
     */
    public static function compile_dynamic_selectors($selectors = array()) {
        $output = '';        
        
        foreach ($selectors as $selector => $props) {
            $part = $selector . '{';
            
            $one_prop = false;
            foreach ($props as $prop => $data) {
                if ($data['value'] == '') {
                    continue;
                }
                
                $value = $data['value'];
                // reading dynamic data from plugin options
                if ($data['type'] == 'options') {
                    $value = essb_sanitize_option_value($value);
                    
                    // if there is no value remove the prop
                    if ($value == '') {
                        continue;
                    }
                }
                                
                $part .= esc_attr($prop).':'.$value;
                if (!empty($data['suffix'])) {
                    $part .= $data['suffix'];
                }
                
                if ($data['important']) {
                    $part .= '!important';
                }
                
                $part .= ';';
                $one_prop = true;
                
                /**
                 * Transition variations
                 */
                if ($prop == 'transition') {
                    $part .= '-webkit-transition:'.$value;
                    if (!empty($data['suffix'])) {
                        $part .= $data['suffix'];
                    }
                    
                    if ($data['important']) {
                        $part .= '!important';
                    }
                    
                    $part .= ';';
                    
                    $part .= '-moz-transition:'.$value;
                    if (!empty($data['suffix'])) {
                        $part .= $data['suffix'];
                    }
                    
                    if ($data['important']) {
                        $part .= '!important';
                    }
                    
                    $part .= ';';
                }
                // end transition generator
            }
            
            $part .= '}';
            
            if ($one_prop) {
                $output .= $part;
            }
        }
        
        return $output;
    }
    
    public static function minify_spaces($code = '') {
        $code = trim(preg_replace('/\s+/', ' ', $code));
        
        return $code;
    }
    
    public static function sanitize_css_output($code = '') {
        return wp_strip_all_tags($code);
    }
    
    public static function adjust_brightness($hex, $steps) {
        // Steps should be between -255 and 255. Negative = darker, positive =
        // lighter
        $steps = max ( - 255, min ( 255, $steps ) );
        
        // Normalize into a six character long hex string
        $hex = str_replace ( '#', '', $hex );
        if (strlen ( $hex ) == 3) {
            $hex = str_repeat ( substr ( $hex, 0, 1 ), 2 ) . str_repeat ( substr ( $hex, 1, 1 ), 2 ) . str_repeat ( substr ( $hex, 2, 1 ), 2 );
        }
        
        // Split into three parts: R, G and B
        $color_parts = str_split ( $hex, 2 );
        $return = '#';
        
        foreach ( $color_parts as $color ) {
            $color = hexdec ( $color ); // Convert to decimal
            $color = max ( 0, min ( 255, $color + $steps ) ); // Adjust color
            $return .= str_pad ( dechex ( $color ), 2, '0', STR_PAD_LEFT ); // Make two
            // char hex code
        }
        
        return $return;
    }
    
    public static function light_or_dark($color, $steps_light = 30, $steps_dark = -30) {
        $hex = str_replace( '#', '', $color );
        
        $c_r = hexdec( substr( $hex, 0, 2 ) );
        $c_g = hexdec( substr( $hex, 2, 2 ) );
        $c_b = hexdec( substr( $hex, 4, 2 ) );
        
        $brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;
        
        return $brightness > 155 ? $steps_dark : $steps_light;
    }
}