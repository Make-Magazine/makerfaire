<?php

if (!function_exists('essb_register_dynamic_share_sharebar_styles')) {
    /**
     * Share Buttons Display: Top Bar
     */
    function essb_register_dynamic_share_sharebar_styles() {
        
        $mobile_sharebar_bg = essb_sanitize_option_value('mobile_sharebar_bg');
        $mobile_sharebar_color = essb_sanitize_option_value('mobile_sharebar_color');
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharebar', 'opacity', '1', '', true);
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharebar', 'background-color', $mobile_sharebar_bg, '', true);
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharebar', 'color', $mobile_sharebar_color, '', true);
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharebar svg', 'fill', $mobile_sharebar_color, '', true);
    }
}