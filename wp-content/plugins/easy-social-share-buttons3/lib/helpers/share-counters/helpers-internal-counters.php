<?php
/**
 * Transform network with official counter to internal counter
 *
 * @since 8.0
 * @package EasySocialShareButtons
 * @author appscreo
 */

if (!function_exists('essb_migrate_official_to_internal_counter')) {
    
    add_filter('essb_get_post_cached_counters', 'essb_migrate_official_to_internal_counter', 10, 2);
    
    function essb_migrate_official_to_internal_counter($post_id = '', $cached_counters = array()) {
        
        $current_networks = essb_option_value('active_internal_counters_advanced_networks');
        if (is_array($current_networks)) {
            
            $require_calcuate = false;
            
            foreach ($current_networks as $key) {
                $internal_value = get_post_meta($post_id, 'essb_pc_' . $key, true);
                $current_value = isset($cached_counters[$key]) ? $cached_counters[$key] : '';
                
                if (intval($current_value) < intval($internal_value)) {
                    $cached_counters[$key] = $internal_value;
                    $require_calcuate = true;
                }
            }
            
            if ($require_calcuate) {
                $cached_counters['total'] = 0;
                
                foreach ($cached_counters as $k => $value) {
                    if ($k != 'total') {
                        $cached_counters['total'] += intval($value);
                    }
                }
            }
        }
        
        return $cached_counters;
    }
}