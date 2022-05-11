<?php

/**
 * Read previously stored MashShare Facebook counter and apply it on the shares (only if greater than the current)
 * 
 * @since 8.0
 * @package EasySocialShareButtons
 */

if (!function_exists('essb_mashshare_integrate_facebook_counter')) {
    add_filter('essb_get_post_cached_counters', 'essb_mashshare_integrate_facebook_counter', 10, 2);
    
    function essb_mashshare_integrate_facebook_counter($post_id, $cached_counters) {
        $result = json_decode(get_post_meta( $post_id, 'mashsb_jsonshares', true ), true);
        $facebook_counter = isset($result['facebook_total']) ? $result['facebook_total'] : 0;
        
        if (intval($facebook_counter) > 0) {
            $current_facebook = isset($cached_counters['facebook']) ? $cached_counters['facebook'] : 0;
            if (intval($current_facebook) < intval($facebook_counter)) {
                $cached_counters['facebook'] = $facebook_counter;
            }
        }
        
        return $cached_counters;
    }
}