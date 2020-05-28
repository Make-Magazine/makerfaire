<?php
/**
 * Prevent from running disabled features of plugin
 *
 * @since 7.0
 * @author appscreo
 * @package EasySocialShareButtons
 */

if (!function_exists('essb_control_running_disabled_modules')) {
    
    function essb_control_running_disabled_modules($options = array()) {
        if (!essb_option_bool_value('social_proof_enable') || essb_option_bool_value('deactivate_ansp')) {
            $options['button_counter_hidden_till'] = '';
            $options['total_counter_hidden_till'] = '';
        }
        
        if (essb_option_bool_value('deactivate_ssr')) {
            $options['counter_recover_active'] = 'false';
        }
        
        if (essb_option_bool_value('deactivate_module_aftershare')) {
            $options['afterclose_active'] = 'false';
        }
        
        if (essb_option_bool_value('deactivate_module_shareoptimize')) {
            $options['opengraph_tags'] = 'false';
            $options['twitter_card'] = 'false';
        }
        
        if (essb_option_bool_value('deactivate_module_analytics')) {
            $options['stats_active'] = 'false';
        }
        
        if (essb_option_bool_value('deactivate_module_pinterestpro')) {
            $options['pinterest_images'] = 'false';
            $options['pinterest_force_description'] = 'false';
            $options['pinterest_force_responsive'] = 'false';
            $options['pinterest_set_datamedia'] = 'false';
        }
        
        if (essb_option_bool_value('deactivate_module_affiliate')) {
            $options['affs_active'] = 'false';
            $options['affs_active_shortcode'] = 'false';
            $options['affwp_active'] = 'false';
            $options['affwp_active_shortcode'] = 'false';
            $options['mycred_activate'] = 'false';
            $options['mycred_activate_custom'] = 'false';
            $options['mycred_referral_activate'] = 'false';
            $options['mycred_referral_activate_shortcode'] = 'false';
        }
        
        if (essb_option_bool_value('deactivate_module_customshare')) {
            $options['customshare'] = 'false';
        }
        
        if (essb_option_bool_value('deactivate_fakecounters')) {
            $options['activate_fake_counters'] = 'false';
            $options['activate_fake_counters_internal'] = 'false';
        }
        
        return $options;
    }
    
    add_filter('essb_after_options_load', 'essb_control_running_disabled_modules');
}