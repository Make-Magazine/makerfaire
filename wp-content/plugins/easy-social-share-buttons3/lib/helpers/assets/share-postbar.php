<?php
if (!function_exists('essb_register_dynamic_share_postbar_styles')) {
    function essb_register_dynamic_share_postbar_styles() {
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar .essb-postbar-container, .essb-postbar-prev-post .essb_prev_post, .essb-postbar-next-post .essb_next_post', 'background-color', 'postbar_bgcolor', '', true);
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar, .essb-postbar a, .essb-postbar-prev-post .essb_prev_post', 'color', 'postbar_color', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar-next-post .essb_next_post_info span.essb_title, .essb-postbar-prev-post .essb_prev_post_info span.essb_title', 'color', 'postbar_color', '', true);
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar .essb-postbar-progress-bar', 'background-color', 'postbar_accentcolor', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar .essb-postbar-category a', 'background-color', 'postbar_accentcolor', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar-next-post .essb_next_post_info span.essb_category, .essb-postbar-prev-post .essb_prev_post_info span.essb_category', 'background-color', 'postbar_accentcolor', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar-close-postpopup', 'background-color', 'postbar_accentcolor', '', true);
        
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar .essb-postbar-category a', 'color', 'postbar_altcolor', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar-next-post .essb_next_post_info span.essb_category, .essb-postbar-prev-post .essb_prev_post_info span.essb_category', 'color', 'postbar_altcolor', '', true);
        ESSB_Dynamic_CSS_Builder::map_option('.essb-postbar-close-postpopup', 'color', 'postbar_altcolor', '', true);
        
        // postbar related code to move body below it
        ESSB_Dynamic_CSS_Builder::register_header_field('body.single', 'margin-bottom', '46px', '', true);            
    }
}