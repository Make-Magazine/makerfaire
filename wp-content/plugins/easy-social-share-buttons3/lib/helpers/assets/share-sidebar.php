<?php

if (!function_exists('essb_register_dynamic_share_sidebar_styles')) {    
    /**
     * Share Buttons Display: Sidebar
     */
    function essb_register_dynamic_share_sidebar_styles() {                
        ESSB_Dynamic_CSS_Builder::map_option('.essb_displayed_sidebar_right, .essb_displayed_sidebar', 'top', 'sidebar_fixedtop', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb_displayed_sidebar', 'left', 'sidebar_fixedleft', 'px', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb_displayed_sidebar_right', 'right', 'sidebar_fixedleft', 'px', true);
        
        if (essb_sanitize_option_value('sidebar_leftright_percent') != '') {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_displayed_sidebar_right, .essb_displayed_sidebar', 'display', 'none');            
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_displayed_sidebar_right, .essb_displayed_sidebar', 'transition', 'all 0.5s');
        }
    }
}