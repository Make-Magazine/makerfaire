<?php

if (!function_exists('essb_ms_custom_data')) {
    /**
     * Read previous set data in Social Snap plugin
     *
     * @return string[]|NULL[]|unknown[]
     */
    function essb_ms_custom_data() {
        
        global $post;
        
        $result = array('og_title' => '', 'og_description' => '', 'og_image' => '', 'custom_tweet' => '', 'pin_image' => '', 'pin_description' => '' );
        
        if (isset($post)) {
            $swp_custom_tweet = get_post_meta($post->ID, 'mashsb_custom_tweet', true);
            if ($swp_custom_tweet != '') {
                $result['custom_tweet'] = $swp_custom_tweet;
            }
            
            $swp_og_description = get_post_meta($post->ID, 'mashsb_og_description', true);
            if ($swp_og_description != '') {
                $result['og_description'] = $swp_og_description;
            }
            
            $swp_og_image = get_post_meta($post->ID, 'mashsb_og_image', true);
            if ($swp_og_image != '') {
                $result['og_image'] = wp_get_attachment_url($swp_og_image);
            }
            
            $swp_og_title = get_post_meta($post->ID, 'mashsb_og_title', true);
            if ($swp_og_title != '') {
                $result['og_title'] = $swp_og_title;
            }
            
            $swp_pinterest_image = get_post_meta($post->ID, 'mashsb_pinterest_image', true);
            if ($swp_pinterest_image != '') {
                $result['pin_image'] = wp_get_attachment_url($swp_pinterest_image);
            }
            
            $swp_pinterest_description = get_post_meta($post->ID, 'mashsb_pinterest_description', true);
            if ($swp_pinterest_description != '') {
                $result['pin_description'] = $swp_pinterest_description;
            }
            
        }
        
        return $result;
    }
    
}