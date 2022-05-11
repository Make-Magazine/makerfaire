<?php 

/**
 * Automatic responsive mobile options
 * 
 * @since 8.0
 */

if (!function_exists('essb_setup_responsive_mobile_display')) {
    add_filter('essb4_options_extender_after_load', 'essb_setup_responsive_mobile_display');
    
    /**
     * Overwrite the default options to enable the responsive mode
     * 
     * @param array $options
     * @return array
     * @since 8.0
     */
    function essb_setup_responsive_mobile_display($options = array()) {
        
        /**
         * If we are browsing the website in the admin
         */
        if (is_admin()) {
            return $options;
        }
        
        /**
         * On mobiles more button is not required
         */
        $mobile_networks = array();
        
        $all_networks = essb_option_value('networks');
        
        foreach($all_networks as $key) {
            if ($key != 'more' && $key != 'share') {
                $mobile_networks[] = $key;
            }
        }
                
        $mobile_positions = essb_option_value('functions_mode_mobile_auto_responsive');
        
        $options['mobile_positions'] = 'true';
        $options['button_position_mobile'] = array();
        $options['button_position_mobile'] = $mobile_positions;
        
        $options['mobile_css_activate'] = 'true';
        $options['mobile_css_optimized'] = 'true';
        
        $functions_mode_mobile_auto_breakpoint = essb_option_value('functions_mode_mobile_auto_breakpoint');
        if ($functions_mode_mobile_auto_breakpoint != '' && intval($functions_mode_mobile_auto_breakpoint) > 0) {
            $options['mobile_css_screensize'] = $functions_mode_mobile_auto_breakpoint;
        }        
        
        return $options;
    }
}