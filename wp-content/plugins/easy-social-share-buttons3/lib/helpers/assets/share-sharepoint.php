<?php

if (!function_exists('essb_register_dynamic_share_sharepoint_styles')) {
    /**
     * Share Buttons Display: Top Bar
     */
    function essb_register_dynamic_share_sharepoint_styles() {
        
        $mobile_sharebar_bg = essb_sanitize_option_value('sharepoint_bg');
        $mobile_sharebar_color = essb_sanitize_option_value('sharepoint_icon_color');
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharepoint', 'opacity', '1', '', true);
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharepoint', 'background-color', $mobile_sharebar_bg, '', true);
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharepoint svg', 'fill', $mobile_sharebar_color, '', true);
    }
}