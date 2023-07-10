<?php
/**
 * Update options or components to deactivate/deregister deprecated functions or features
 * 
 * @since 8.9
 */

if (!function_exists('essb_remove_deprecated_social_share_networks')) {
    function essb_remove_deprecated_social_share_networks($networks = array()) {
        
        if (isset($networks['flattr'])) {
            unset($networks['flattr']);
        }
        
        return $networks;
    }
    
    add_filter('essb_available_social_share_networks', 'essb_remove_deprecated_social_share_networks');
}
