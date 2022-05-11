<?php
/**
 * Utility functions related to the share counters
 * 
 * @since 8.0
 * @package EasySocialShareButtons
 * @author appscreo
 */

/**
 * Update single post sahre counters
 * 
 * @param string $post_id
 * @return array
 */
function essb_update_single_post_counters($post_id = '') {
    $result = array();
    
    if (!empty($post_id)) {
        /**
         * Remove the last expiration time to get the counters fresh
         */
        delete_post_meta($post_id, 'essb_cache_expire');
        $share_data = essb_get_single_post_share_details($post_id);        
        $share_data['full_url'] = $share_data['url'];
        $networks = essb_option_value('networks');
        $result = ESSBCachedCounters::get_counters(get_the_ID(), $share_data, $networks);
    }
    
    return $result();
}