<?php
if (!function_exists('essb_register_dynamic_followers_styles')) {
    /**
     * Register dynamic Social Followers Counter
     */
    function essb_register_dynamic_followers_styles() {
        
        $network_list = ESSBSocialFollowersCounterHelper::available_social_networks();
        
        foreach ($network_list as $network => $title) {
            $color_isset = essb_sanitize_option_value('fanscustomizer_'.$network);
            if ($color_isset == '' && $network == 'instagram') {
                $color_isset = essb_sanitize_option_value('fanscustomizer_instgram');
            }
            
            $color_isset_hover = essb_sanitize_option_value('fanscustomizer_hover_'.$network);
            $user_hover = true;
            if ($color_isset_hover == '' && $network == 'instagram') {
                $color_isset_hover = essb_sanitize_option_value('fanscustomizer_hover_instgram');
            }
            
            if ($color_isset_hover == '') {
                $color_isset_hover = $color_isset;
                $user_hover = false;
            }
            
            /**
             * Regular color
             */
            if ($color_isset != '') {
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-color .essbfc-icon-'.$network.', .essbfc-template-grey .essbfc-icon-'.$network, 'color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-roundcolor .essbfc-icon-'.$network.', .essbfc-template-roundgrey .essbfc-icon-'.$network, 'background-color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-outlinecolor .essbfc-icon-'.$network.', .essbfc-template-outlinegrey .essbfc-icon-'.$network, 'color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-outlinecolor .essbfc-icon-'.$network.', .essbfc-template-outlinegrey .essbfc-icon-'.$network, 'border-color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-metro .essbfc-'.$network.' .essbfc-network', 'background-color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-flat .essbfc-'.$network.' .essbfc-network', 'background-color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-dark .essbfc-'.$network.' .essbfc-network', 'background-color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-tinycolor .essbfc-'.$network.' .essbfc-network', 'background-color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.' .essbfc-network i', 'color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.' .essbfc-network', 'border-bottom', '3px solid '.$color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modernlight .essbfc-'.$network.' .essbfc-network i', 'color', $color_isset, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modernlight .essbfc-'.$network.' .essbfc-network', 'color', $color_isset, '', true);
                
                if ($network == 'instgram') {
                    $network = 'instagram';
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-color .essbfc-icon-'.$network.', .essbfc-template-grey .essbfc-icon-'.$network, 'color', $color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-roundcolor .essbfc-icon-'.$network.', .essbfc-template-roundgrey .essbfc-icon-'.$network, 'background-color', $color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-outlinecolor .essbfc-icon-'.$network.', .essbfc-template-outlinegrey .essbfc-icon-'.$network, 'color', $color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-metro .essbfc-'.$network.' .essbfc-network', 'background-color', $color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-flat .essbfc-'.$network.' .essbfc-network', 'background-color', $color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-dark .essbfc-'.$network.' .essbfc-network', 'background-color', $color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-tinycolor .essbfc-'.$network.' .essbfc-network', 'background-color', $color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.' .essbfc-network i', 'color', $color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.' .essbfc-network', 'border-bottom', '3px solid '.$color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modernlight .essbfc-'.$network.' .essbfc-network i', 'color', $color_isset, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modernlight .essbfc-'.$network.' .essbfc-network', 'color', $color_isset, '', true);
                }
            }
            
            /**
             * Hover color
             */
            if ($color_isset_hover != '') {
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-outlinecolor li:hover .essbfc-icon-'.$network.', .essbfc-template-outlinegrey li:hover .essbfc-icon-'.$network, 'background-color', $color_isset_hover, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-outlinecolor li:hover .essbfc-icon-'.$network.', .essbfc-template-outlinegrey li:hover .essbfc-icon-'.$network, 'color', '#ffffff', '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-outlinecolor li:hover .essbfc-icon-'.$network.', .essbfc-template-outlinegrey li:hover .essbfc-icon-'.$network, 'border-color', $color_isset_hover, '', true);
                
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.':hover .essbfc-network i', 'color', '#ffffff', '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.':hover .essbfc-network', 'border-color', $color_isset_hover, '', true);
                
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modernlight .essbfc-'.$network.':hover .essbfc-network i', 'color', '#ffffff', '', true);
                ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modernlight .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                
                if ($user_hover) {
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-color li:hover .essbfc-icon-'.$network.', .essbfc-template-grey li:hover .essbfc-icon-'.$network, 'color', $color_isset_hover, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-roundcolor li:hover .essbfc-icon-'.$network.', .essbfc-template-roundgrey li:hover .essbfc-icon-'.$network, 'background-color', $color_isset_hover, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-metro .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-flat .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-dark .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-tinycolor .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                }
                
                if ($network == 'instgram') {
                    $network = 'instagram';
                    
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-outlinecolor li:hover .essbfc-icon-'.$network.', .essbfc-template-outlinegrey li:hover .essbfc-icon-'.$network, 'background-color', $color_isset_hover, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-outlinecolor li:hover .essbfc-icon-'.$network.', .essbfc-template-outlinegrey li:hover .essbfc-icon-'.$network, 'color', '#ffffff', '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-outlinecolor li:hover .essbfc-icon-'.$network.', .essbfc-template-outlinegrey li:hover .essbfc-icon-'.$network, 'border-color', $color_isset_hover, '', true);
                    
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.':hover .essbfc-network i', 'color', '#ffffff', '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modern .essbfc-'.$network.':hover .essbfc-network', 'border-color', $color_isset_hover, '', true);
                    
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modernlight .essbfc-'.$network.':hover .essbfc-network i', 'color', '#ffffff', '', true);
                    ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-modernlight .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                    
                    if ($user_hover) {
                        ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-color li:hover .essbfc-icon-'.$network.', .essbfc-template-grey li:hover .essbfc-icon-'.$network, 'color', $color_isset_hover, '', true);
                        ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-roundcolor li:hover .essbfc-icon-'.$network.', .essbfc-template-roundgrey li:hover .essbfc-icon-'.$network, 'background-color', $color_isset_hover, '', true);
                        ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-metro .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                        ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-flat .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                        ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-dark .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                        ESSB_Dynamic_CSS_Builder::register_header_field('body .essb-followers-counter.essbfc-template-tinycolor .essbfc-'.$network.':hover .essbfc-network', 'background-color', $color_isset_hover, '', true);
                    }
                }
            }
        }
    }
}