<?php

/**
 * Apply automation over plugin settings
 * 
 * @param array $options
 * @param string $key
 * @return array
 */
function essb_admin_automation_enable($options, $key = '') {
    
    if ($key == 'automatic-network-setup') {
        $options = essb_admin_automation_active_networks($options);
    }
    
    if ($key == 'automatic-responsive-networks') {
        $options = essb_admin_automation_reponsive_networks($options);
    }
    
    if ($key == 'automation-avoid-negative') {
        $options = essb_admin_automation_avoid_negative_proof($options);
    }
    
    if ($key == 'automation-https-recover') {
        $options = essb_admin_automation_https_recovery($options);
    }
    
    if ($key == 'automation-deactivate-internal') {
        $options = essb_admin_automation_deactivate_internal($options);
    }
    
    if ($key == 'automatic-positions-setup') {
        $options = essb_admin_automation_positions_setup($options);
    }
    
    return $options;
}

function essb_admin_automation_positions_setup($options = array()) {
    $options['activate_automatic_position'] = 'true';
    $options['activate_automatic_mobile_content'] = 'true';
    
    return $options;
}

function essb_admin_automation_deactivate_internal($options = array()) {
    $options['active_internal_counters'] = 'false';
    $options['deactive_internal_counters_mail'] = 'true';
    $options['deactivate_postcount'] = 'true';
    
    return $options;
}

function essb_admin_automation_https_recovery($options = array()) {
    $options['counter_recover_active'] = 'true';
    $options['counter_recover_mode'] = 'unchanged';
    $options['counter_recover_custom'] = '';
    $options['counter_recover_protocol'] = 'http2https';
    $options['counter_recover_prefixdomain'] = 'unchanged';
    $options['counter_recover_subdomain'] = '';
    $options['counter_recover_domain'] = '';
    $options['counter_recover_newdomain'] = '';
    $options['counter_recover_date'] = '';
    
    return $options;
}

function essb_admin_automation_avoid_negative_proof($options = array()) {
    $options['social_proof_enable'] = 'true';
    $options['button_counter_hidden_till'] = '1';
    $options['total_counter_hidden_till'] = '1';
    
    return $options;
}

function essb_admin_automation_reponsive_networks($options = array()) {
    
    $mobile_only = array('whatsapp', 'line', 'viber', 'sms', 'telegram', 'skype', 'messenger', 'kakao');
    
    $options['activate_networks_responsive'] = 'true';
    
    foreach ($mobile_only as $key) {
        $options['responsive_' . $key . '_mobile'] = 'true';
    }
    
    return $options;
}

/**
 * Automation for using the same networks on entire site
 * 
 * @param array $options
 */
function essb_admin_automation_active_networks($options = array()) {
    
    $networks = isset($options['networks']) ? $options['networks'] : array();
    if (!is_array($networks)) {
        $networks = array();
    }
    
    if (count($networks) > 0) {
        $options['activate_networks_manage'] = 'true';
        $options['functions_networks'] = $networks;
        $options['user_fixed_networks'] = 'true';
    }
    
    return $options;
}