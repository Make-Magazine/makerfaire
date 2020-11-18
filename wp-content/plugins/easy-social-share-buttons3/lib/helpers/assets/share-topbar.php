<?php

if (!function_exists('essb_register_dynamic_share_topbar_styles')) {
    /**
     * Share Buttons Display: Top Bar
     */
    function essb_register_dynamic_share_topbar_styles() {
        
        $topbar_top_pos = essb_sanitize_option_value('topbar_top');
        $topbar_top_loggedin = essb_sanitize_option_value('topbar_top_loggedin');
        
        if (is_user_logged_in() && $topbar_top_loggedin != '') {
            $topbar_top_pos = $topbar_top_loggedin;
        }
        
        if ($topbar_top_pos != '') {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_topbar', 'top', $topbar_top_pos, 'px', true);            
        }
        
        $topbar_bg_color = essb_sanitize_option_value('topbar_bg');
        $topbar_bg_color_opacity = essb_sanitize_option_value('topbar_bg_opacity');
        
        if ($topbar_bg_color_opacity != '' && $topbar_bg_color == '') {
            $topbar_bg_color = '#ffffff';
        }
        if ($topbar_bg_color != '') {
            if ($topbar_bg_color_opacity != '') {
                $topbar_bg_color = essb_hex2rgba($topbar_bg_color, $topbar_bg_color_opacity);
            }
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_topbar', 'background', $topbar_bg_color, '', true);  
        }
        
        $topbar_maxwidth = essb_sanitize_option_value('topbar_maxwidth');
        if ($topbar_maxwidth != '') {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_topbar .essb_topbar_inner', 'max-width', $topbar_maxwidth, 'px');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_topbar .essb_topbar_inner', 'margin', '0 auto');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_topbar .essb_topbar_inner', 'padding-left', '0');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_topbar .essb_topbar_inner', 'padding-right', '0');
        }
                
        ESSB_Dynamic_CSS_Builder::map_option('.essb_topbar', 'height', 'topbar_height', 'px');
        
        $topbar_contentarea_width = essb_sanitize_option_value('topbar_contentarea_width');
        if ($topbar_contentarea_width == '' && essb_option_bool_value('topbar_contentarea')) {
            $topbar_contentarea_width = '30';
        }
        
        $topbar_top_onscroll = essb_sanitize_option_value('topbar_top_onscroll');
        if ($topbar_top_onscroll != '') {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_topbar', 'margin-top', '-200px');
        }           
       
        if ($topbar_contentarea_width != '') {
            $topbar_contentarea_width = str_replace('%', '', $topbar_contentarea_width);
            $topbar_contentarea_width = intval($topbar_contentarea_width);
            
            $topbar_buttonarea_width = 100 - $topbar_contentarea_width;

            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_topbar .essb_topbar_inner_buttons', 'width', $topbar_buttonarea_width, '%');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_topbar .essb_topbar_inner_content', 'width', $topbar_contentarea_width, '%');
            
        }        
   }
}