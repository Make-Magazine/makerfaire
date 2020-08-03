<?php

if (!function_exists('essb_register_dynamic_share_float_styles')) {
    /**
     * Share Buttons Display: Float 
     */
    function essb_register_dynamic_share_float_styles() {
        $top_pos = essb_sanitize_option_value('float_top');
        $float_top_loggedin = essb_sanitize_option_value('float_top_loggedin');
        if (is_user_logged_in() && $float_top_loggedin != '') {
            $top_pos = $float_top_loggedin;
        }
        
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed', 'top', $top_pos, 'px', true);
        
        $bg_color = essb_sanitize_option_value('float_bg');
        $bg_color_opacity = essb_sanitize_option_value('float_bg_opacity');
        
        if ($bg_color_opacity != '' && $bg_color == '') {
            $bg_color = '#ffffff';
        }
        
        if ($bg_color != '') {
            if ($bg_color_opacity != '') {
                $bg_color = essb_hex2rgba($bg_color, $bg_color_opacity);
            }
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed', 'background', $bg_color, 'px', true);
        }
        
        if (essb_option_bool_value('float_full')) {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed', 'left', '0');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed', 'width', '100%');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed', 'max-width', '100%');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed', 'padding-left', '10px');
        }
        
        if (essb_option_bool_value('float_remove_margin')) {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed', 'margin', '0', 'px', true);
        }
        
        $float_full_maxwidth = essb_sanitize_option_value('float_full_maxwidth');
        
          
        if ($float_full_maxwidth != '') {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed.essb_links ul', 'margin', '0 auto', '', true);
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed.essb_links ul', 'max-width', $float_full_maxwidth, 'px');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_fixed', 'padding-left', '0');
        }        
    }
}