<?php
if (!function_exists('essb_register_dynamic_share_point_styles')) {
    function essb_register_dynamic_share_point_styles() {
        ESSB_Dynamic_CSS_Builder::map_option('.essb-point .essbpb-share', 'background-color', 'point_bgcolor', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb-point .essbpb-share', 'color', 'point_color', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb-point .essbpb-share:hover', 'background-color', 'point_bgcolor_hover', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb-point .essbpb-share:hover', 'color', 'point_color_hover', '', true);
        
    }
}