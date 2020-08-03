<?php

if (!function_exists('essb_register_dynamic_cct_styles')) {
    /**
     * Register dynamic Click to Tweet styles
     */
    function essb_register_dynamic_cct_styles() {
        ESSB_Dynamic_CSS_Builder::register_header_field('.essb-ctt-user, .essb-ctt-user:hover', 'border', '0');
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user', 'background-color', 'customize_cct_bg');
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user', 'color', 'customize_cct_color');
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user', 'border', 'customizer_cct_border');
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user', 'border-radius', 'customizer_cct_border_radius');
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user', 'text-align', 'customizer_cct_align');
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user', 'padding', 'customizer_cct_padding');
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user .essb-ctt-button', 'text-align', 'customizer_cct_align');
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user .essb-ctt-quote', 'font-size', 'customizer_cct_fontsize');
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user:hover', 'background-color', 'customize_cct_bg_hover');
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user:hover', 'color', 'customize_cct_color_hover');
        ESSB_Dynamic_CSS_Builder::map_option('.essb-ctt-user:hover', 'border', 'customizer_cct_border_hover');        
   }
}