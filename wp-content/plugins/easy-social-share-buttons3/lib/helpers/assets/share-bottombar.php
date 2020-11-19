<?php

if (!function_exists('essb_register_dynamic_share_bottombar_styles')) {
    /**
     * Share Buttons Display: Bottom Bar
     */
    function essb_register_dynamic_share_bottombar_styles() {
        
        $bottombar_bg_color = essb_sanitize_option_value('bottombar_bg');
        $bottombar_bg_color_opacity = essb_sanitize_option_value('bottombar_bg_opacity');
        
        if ($bottombar_bg_color_opacity != '' && $bottombar_bg_color == '') {
            $bottombar_bg_color = '#ffffff';
        }
        if ($bottombar_bg_color != '') {
            if ($bottombar_bg_color_opacity != '') {
                $bottombar_bg_color = essb_hex2rgba($bottombar_bg_color, $bottombar_bg_color_opacity);
            }
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_bottombar', 'background', $bottombar_bg_color, '', true);
        }
        
        $bottombar_maxwidth = essb_sanitize_option_value('bottombar_maxwidth');
        if ($bottombar_maxwidth != '') {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_bottombar .essb_bottombar_inner', 'max-width', $bottombar_maxwidth, 'px');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_bottombar .essb_bottombar_inner', 'margin', '0 auto');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_bottombar .essb_bottombar_inner', 'padding-left', '0');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_bottombar .essb_bottombar_inner', 'padding-right', '0');
        }
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb_bottombar', 'height', 'bottombar_height', 'px');
        
        $bottombar_contentarea_width = essb_sanitize_option_value('bottombar_contentarea_width');
        if ($bottombar_contentarea_width == '' && essb_option_bool_value('bottombar_contentarea')) {
            $bottombar_contentarea_width = '30';
        }
        
        $bottombar_top_onscroll = essb_sanitize_option_value('bottombar_top_onscroll');
        if ($bottombar_top_onscroll != '') {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_bottombar', 'margin-bottom', '-200px');
        }
        
        if ($bottombar_contentarea_width != '') {
            $bottombar_contentarea_width = str_replace('%', '', $bottombar_contentarea_width);
            $bottombar_contentarea_width = intval($bottombar_contentarea_width);
            
            $bottombar_buttonarea_width = 100 - $bottombar_contentarea_width;
            
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_bottombar .essb_bottombar_inner_buttons', 'width', $bottombar_buttonarea_width, '%');
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_bottombar .essb_bottombar_inner_content', 'width', $bottombar_contentarea_width, '%');
            
        }
    }
}