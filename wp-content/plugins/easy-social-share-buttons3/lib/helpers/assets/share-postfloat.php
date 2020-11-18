<?php

if (!function_exists('essb_register_dynamic_share_postfloat_styles')) {
    /**
     * Share Buttons Display: Post Float
     */
    function essb_register_dynamic_share_postfloat_styles() {
        ESSB_Dynamic_CSS_Builder::map_option('body .essb_displayed_postfloat', 'margin-left', 'postfloat_marginleft', 'px', true);
        ESSB_Dynamic_CSS_Builder::map_option('body .essb_displayed_postfloat', 'margin-top', 'postfloat_margintop', 'px', true);
        ESSB_Dynamic_CSS_Builder::map_option('body .essb_displayed_postfloat', 'top', 'postfloat_initialtop', 'px', true);

        ESSB_Dynamic_CSS_Builder::map_option('body .essb_displayed_postfloat.essb_postfloat_fixed', 'top', 'postfloat_top', 'px', true);
        
        if (essb_option_value('postfloat_percent') != '') {
            ESSB_Dynamic_CSS_Builder::register_header_field('body .essb_displayed_postfloat', 'opacity', '0');
            ESSB_Dynamic_CSS_Builder::register_header_field('body .essb_displayed_postfloat', 'transform', 'translateY(50px)');            
        }
    }
}