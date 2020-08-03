<?php

if (!function_exists('essb_css_build_mobile_responsive')) {
    
    function essb_css_build_mobile_responsive() {
        $mobile_css_screensize = essb_sanitize_option_value('mobile_css_screensize');
        if (empty($mobile_css_screensize)) {
            $mobile_css_screensize = "768";
        }
        $mobile_css_readblock =  essb_option_bool_value('mobile_css_readblock');
        $mobile_css_all = essb_option_bool_value('mobile_css_all');
        $mobile_css_optimized = essb_option_bool_value('mobile_css_optimized');
        
        $snippet = '';
        
        if ($mobile_css_readblock) {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_displayed_sidebar, .essb_links.essb_displayed_sidebar_right, .essb_links.essb_displayed_postfloat', 'display', 'none', '', false, 'static', '', $mobile_css_screensize);
        }
        if ($mobile_css_all) {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links', 'display', 'none', '', false, 'static', '', $mobile_css_screensize);
        }
        
        if ($mobile_css_optimized) {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharebar, .essb-mobile-sharepoint, .essb-mobile-sharebottom, .essb-mobile-sharebottom .essb_links, .essb-mobile-sharebar-window .essb_links, .essb-mobile-sharepoint .essb_links', 'display', 'block', '', false, 'static', '', $mobile_css_screensize);
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharebar .essb_native_buttons, .essb-mobile-sharepoint .essb_native_buttons, .essb-mobile-sharebottom .essb_native_buttons, .essb-mobile-sharebottom .essb_native_item, .essb-mobile-sharebar-window .essb_native_item, .essb-mobile-sharepoint .essb_native_item', 'display', 'none', '', false, 'static', '', $mobile_css_screensize);
            
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharebar, .essb-mobile-sharepoint, .essb-mobile-sharebottom', 'display', 'none', '', false, 'static', $mobile_css_screensize);
            
        }
        else {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb-mobile-sharebar, .essb-mobile-sharepoint, .essb-mobile-sharebottom', 'display', 'none');            
        }
    }
    
    essb_css_build_mobile_responsive();
}