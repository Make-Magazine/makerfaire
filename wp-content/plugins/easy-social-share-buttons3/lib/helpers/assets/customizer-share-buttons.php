<?php

if (!function_exists('essb_register_dynamic_sharebutton_styles')) {
    function essb_register_dynamic_sharebutton_styles() {        
        $global_bgcolor = essb_sanitize_option_value('customizer_bgcolor');
        $global_textcolor = essb_sanitize_option_value('customizer_textcolor');
        $global_hovercolor = essb_sanitize_option_value('customizer_hovercolor');
        $global_hovertextcolor = essb_sanitize_option_value('customizer_hovertextcolor');
        
        /**
         * Total counter
         */        
        ESSB_Dynamic_CSS_Builder::map_option('.essb_totalcount', 'background', 'customizer_totalbgcolor', '', true);
        if (essb_option_bool_value('customizer_totalnobgcolor')) {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_totalcount', 'background', 'none', '', true);
        }

        ESSB_Dynamic_CSS_Builder::map_option('.essb_totalcount, .essb_totalcount .essb_t_nb_after', 'color', 'customizer_totalcolor', '', true);
 
        ESSB_Dynamic_CSS_Builder::map_option('.essb_totalcount .essb_t_nb', 'font-size', 'customizer_totalfontsize', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb_totalcount .essb_t_nb', 'line-height', 'customizer_totalfontsize', '', true);
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb_totalcount .essb_t_nb_after', 'font-size', 'customizer_totalfontsize_after', '', true);
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb_totalcount_item_before .essb_t_before, .essb_totalcount_item_after .essb_t_before', 'font-size', 'customizer_totalfontsize_beforeafter', '', true);
                
        
        if (essb_option_bool_value('customizer_remove_bg_hover_effects')) {
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links a:hover, .essb_links a:focus', 'background', 'none', '', true);
        }
        
        /**
         * Single network
         * @var array $checkbox_list_networks
         */
        
        $checkbox_list_networks = array ();
        $parse_network_list = essb_available_social_networks();
        foreach ( $parse_network_list as $key => $object ) {
            $checkbox_list_networks [$key] = $object ['name'];
        }
        
        foreach ( $checkbox_list_networks as $k => $v ) {
            $network_bgcolor = essb_sanitize_option_value('customizer_' . $k . '_bgcolor');
            $network_textcolor = essb_sanitize_option_value('customizer_' . $k . '_textcolor');
            $network_hovercolor = essb_sanitize_option_value('customizer_' . $k . '_hovercolor');
            $network_hovertextcolor = essb_sanitize_option_value('customizer_' . $k . '_hovertextcolor');
            
            $network_icon = essb_sanitize_option_value('customizer_' . $k . '_icon');
            $network_hovericon = essb_sanitize_option_value('customizer_' . $k . '_hovericon');
            $network_iconbgsize = essb_sanitize_option_value('customizer_' . $k . '_iconbgsize');
            $network_hovericonbgsize = essb_sanitize_option_value('customizer_' . $k . '_hovericonbgsize');
            
            if (empty($network_bgcolor)) {
                $network_bgcolor = $global_bgcolor;
            }
            if (empty($network_textcolor)) {
                $network_textcolor = $global_textcolor;
            }
            
            if (empty($network_hovercolor)) {
                $network_hovercolor = $global_hovercolor;
            }
            if (empty($network_hovertextcolor)) {
                $network_hovertextcolor = $global_hovertextcolor;
            }
            
            /**
             * Regular button state
             */
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_' . $k . ' a', 'background-color', $network_bgcolor, '', true);
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_' . $k . ' a', 'color', $network_textcolor, '', true);

            if ($k == 'more') {
                ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_more_dots a, .essb_links.essb_share .essb_link_less a', 'background-color', $network_bgcolor, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_more_dots a, .essb_links.essb_share .essb_link_less a', 'color', $network_textcolor, '', true);               
            }
            
            /**
             * Hover state
             */
            
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_' . $k . ' a:hover, .essb_links .essb_link_' . $k . ' a:focus', 'background-color', $network_hovercolor, '', true);
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_' . $k . ' a:hover, .essb_links .essb_link_' . $k . ' a:focus', 'color', $network_hovertextcolor, '', true);
            
            if ($k == 'more') {
                ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_more_dots a:hover, .essb_links .essb_link_more_dots a:focus, .essb_links.essb_share .essb_link_less a:hover, .essb_links .essb_link_less a:focus', 'background-color', $network_hovercolor, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_more_dots a:hover, .essb_links .essb_link_more_dots a:focus, .essb_links.essb_share .essb_link_less a:hover, .essb_links .essb_link_less a:focus', 'color', $network_hovertextcolor, '', true);
            }
            
            /**
             * Icons
             */
            
            if ($network_icon != '') {
                ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_' . $k . ' .essb_icon', 'background', 'url('.esc_url($network_icon).')', '', true);                
                if ($network_iconbgsize != '') {
                    ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_' . $k . ' .essb_icon', ' background-size', $network_iconbgsize, '', true);
                }
            }
            if ($network_hovericon != '') {
                ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_' . $k . ' a:hover .essb_icon', 'background', 'url('.esc_url($network_hovericon).')', '', true);
                if ($network_iconbgsize != '') {
                    ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_link_' . $k . ' a:hover .essb_icon', ' background-size', $network_hovericonbgsize, '', true);
                }                
            }
        }
        
        /**
         * Button chnages
         * @var unknown $global_customizer_iconsize
         */
        $global_customizer_iconsize = essb_sanitize_option_value('customizer_iconsize');
        if ($global_customizer_iconsize != '') {
            $icon_wh = intval($global_customizer_iconsize) * 2;
            $icon_lt = intval($global_customizer_iconsize) / 2;
            $icon_lt = round($icon_lt);
            
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_icon', 'width', $icon_wh, 'px', true);
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_icon', 'height', $icon_wh, 'px', true);

            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_icon:before', 'font-size', $global_customizer_iconsize, 'px', true);
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_icon:before', 'left', $icon_lt, 'px', true);
            ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_icon:before', 'top', $icon_lt, 'px', true);            
        }
        
        $global_customizer_namesize = essb_sanitize_option_value('customizer_namesize');
        $global_customizer_namebold = essb_option_bool_value('customizer_namebold');
        $global_customizer_nameupper = essb_option_bool_value('customizer_nameupper');
        if ($global_customizer_namesize != '' || $global_customizer_namebold || $global_customizer_nameupper) {
            if ($global_customizer_namesize != '') {
                if (intval($global_customizer_namesize) > 0) { $global_customizer_namesize .= 'px'; }
                ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_network_name', 'font-size', $global_customizer_namesize, '', true);                
            }
            if ($global_customizer_namebold) {
                ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_network_name', 'font-weight', 'bold', '', true);
            }
            
            if ($global_customizer_nameupper) {
                ESSB_Dynamic_CSS_Builder::register_header_field('.essb_links.essb_share .essb_network_name', 'text-transform', 'uppercase', '', true);
            }
        }
    }
}