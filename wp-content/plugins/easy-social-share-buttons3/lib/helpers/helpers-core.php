<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}


if (!function_exists('essb_manager')) {
    function essb_manager() {
        return ESSB_Manager::instance();
    }
}

if (!function_exists('essb_core')) {
    function essb_core() {
        return essb_manager()->essb();
    }
}

if (!function_exists('essb_resource_builder')) {
    function essb_resource_builder() {
        return essb_manager()->resourceBuilder();
    }
}

if (!function_exists('easy_share_deactivate')) {
    function easy_share_deactivate() {
        essb_manager()->deactiveExecution();
    }
}

if (!function_exists('easy_share_reactivate')) {
    function easy_share_reactivate() {
        essb_manager()->reactivateExecution();
    }
}

if (!function_exists('essb_native_privacy')) {
    function essb_native_privacy() {
        return essb_manager()->privacyNativeButtons();
    }
}

if (!function_exists ('essb_options_value')) {
    function essb_options_value($param, $default = '') {
        return essb_option_value($param);
    }
}

if (!function_exists('essb_options_bool_value')) {
    function essb_options_bool_value($param) {
        return essb_option_bool_value($param);
    }
}

if (!function_exists('essb_options')) {
    function essb_options() {
        return ESSB_Plugin_Options::read_all();
    }
}

if (!function_exists('essb_followers_counter')) {
    function essb_followers_counter() {
        return essb_manager()->socialFollowersCounter();
    }
}

if (!function_exists('essb_is_mobile')) {
    function essb_is_mobile() {
        return essb_manager()->isMobile();
    }
}

if (!function_exists('essb_is_tablet')) {
    function essb_is_tablet() {
        return essb_manager()->isTablet();
    }
}

if (!function_exists('essb_is_plugin_activated_on')) {
    function essb_is_plugin_activated_on() {
        if (is_admin()) {
            return false;
        }
        
        //display_deactivate_on
        $is_activated = false;
        $display_include_on = essb_options_value('display_include_on');
        if ($display_include_on != '') {
            $excule_from = explode(',', $display_include_on);
            
            $excule_from = array_map('trim', $excule_from);
            if (in_array(get_the_ID(), $excule_from, false)) {
                $is_activated = true;
            }
        }
        return $is_activated;
    }
}

if (!function_exists('essb_is_plugin_deactivated_on')) {
    /**
     * @return boolean
     */
    function essb_is_plugin_deactivated_on() {
        if (is_admin()) {
            return;
        }
        
        //display_deactivate_on
        $is_deactivated = false;
        $display_deactivate_on = essb_options_value('display_deactivate_on');
        if ($display_deactivate_on != '') {
            $excule_from = explode(',', $display_deactivate_on);
            
            $excule_from = array_map('trim', $excule_from);
            if (in_array(get_the_ID(), $excule_from, false)) {
                $is_deactivated = true;
            }
        }
                
        if (!$is_deactivated) {
            if (essb_is_mobile() && essb_option_bool_value('deactivate_mobile')) {
                $is_deactivated = true;
            }
        }
                
        if (!$is_deactivated && ESSBGlobalSettings::$url_deactivate_full_running) {
            if (essb_is_deactivated_on_uri(ESSBGlobalSettings::$url_deactivate_full)) {
                $is_deactivated = true;
            }
        }
                
        if (!$is_deactivated && has_filter('essb_is_plugin_deactivated_on')) {
            $is_deactivated = apply_filters('essb_is_plugin_deactivated_on', $is_deactivated);
        }
                
        return $is_deactivated;
    }
}

if (!function_exists('essb_is_module_deactivated_on_category')) {
    function essb_is_module_deactivated_on_category($module = 'share') {
        if (is_admin()) {
            return;
        }
        
        $is_deactivated = false;
        $exclude_from = essb_options_value( 'deactivate_on_'.$module.'_cats');
        if (!empty($exclude_from) && is_single()) {
            $excule_from = explode(',', $exclude_from);
            
            $excule_from = array_map('trim', $excule_from);
            
            $categories = get_the_category();
            if ($categories) {
                foreach ($categories as $cat) {
                    if (in_array($cat->term_id, $excule_from, false)) {
                        $is_deactivated = true;
                    }
                }
            }
            
            
        }
        return $is_deactivated;
    }
}

if (!function_exists('essb_is_module_deactivated_on')) {
    function essb_is_module_deactivated_on($module = 'share') {
        if (is_admin()) {
            return;
        }
        
        $is_deactivated = false;
        $exclude_from = essb_options_value( 'deactivate_on_'.$module);
        if (!empty($exclude_from)) {
            $excule_from = explode(',', $exclude_from);
            
            $excule_from = array_map('trim', $excule_from);
            if (in_array(get_the_ID(), $excule_from, false)) {
                $is_deactivated = true;
            }
        }
        
        if (!$is_deactivated && $module == 'share' && ESSBGlobalSettings::$url_deactivate_share_running) {
            if (essb_is_deactivated_on_uri(ESSBGlobalSettings::$url_deactivate_share)) {
                $is_deactivated = true;
            }
        }
        
        return $is_deactivated;
    }
}



if (!function_exists('essb_is_position_active')) {
    /**
     * Check if the position that is selected is active inside plugin settings
     *
     * @param unknown_type $position
     * @return boolean
     */
    function essb_is_position_active($position = '') {
        global $post;
        
        $content_position = essb_option_value('content_position');
        $button_positions = essb_option_value('button_position');
        
        if (!is_array($button_positions)) {
            $button_positions = array();
        }
        
        if (essb_option_bool_value('positions_by_pt') && isset($post)) {
            $current_post_type = $post->post_type;
            
            $content_position_by_pt = essb_option_value('content_position_'.$current_post_type);
            $button_position_by_pt = essb_option_value('button_position_'.$current_post_type);
            
            if (!empty($content_position_by_pt)) {
                $content_position = $content_position_by_pt;
            }
            
            if (is_array($button_position_by_pt) && count($button_position_by_pt) > 0) {
                $button_positions = $button_position_by_pt;
            }
        }
        
        /**
         * Homepage
         */
        if (essb_option_bool_value('positions_by_pt') && is_front_page()) {
            $current_post_type = 'homepage';
            
            $content_position_by_pt = essb_option_value('content_position_'.$current_post_type);
            $button_position_by_pt = essb_option_value('button_position_'.$current_post_type);
            
            if (!empty($content_position_by_pt)) {
                $content_position = $content_position_by_pt;
            }
            
            if (is_array($button_position_by_pt) && count($button_position_by_pt) > 0) {
                $button_positions = $button_position_by_pt;
            }
        }
        
        return $content_position == $position || in_array($position, $button_positions);
    }
}


if (!function_exists('essb_depend_load_function')) {
    function essb_depend_load_function($function, $path) {
        if (!function_exists($function)) {
            include_once ESSB3_PLUGIN_ROOT.$path;
        }
    }
}

if (!function_exists('essb_depend_load_class')) {
    function essb_depend_load_class($class, $path) {
        if (!class_exists($class)) {
            include_once ESSB3_PLUGIN_ROOT.$path;
        }
    }
}

if (!function_exists('essb_installed_wpml')) {
    function essb_installed_wpml() {
        
        if (essb_option_bool_value('deactivate_multilang')) {
            return false;
        }
        else {
            if (class_exists ( 'SitePress' ))
                return (true);
                else
                    return (false);
        }
    }
}

if (!function_exists('essb_installed_polylang')) {
    function essb_installed_polylang() {
        
        if (essb_option_bool_value('deactivate_multilang')) {
            return false;
        }
        else {
            if (function_exists('pll_languages_list')) {
                return true;
            }
            else {
                return false;
            }
        }
    }
}

if (!function_exists('essb_live_customizer_can_run')) {
    function essb_live_customizer_can_run() {
        $can_run = false;
        if (is_user_logged_in()) {
            if (current_user_can('administrator')) {
                $can_run = true;
            }
        }
        
        if ($can_run) {
            if (essb_option_bool_value('live_customizer_disabled')) {
                $can_run = false;
            }
        }
        
        return apply_filters('essb_live_customizer_can_run', $can_run);
    }
}

if (!function_exists('essb_is_amp_page')) {
    function essb_is_amp_page(){
        // Defined in https://wordpress.org/plugins/amp/ is_amp_endpoint()
        
        if (  function_exists('is_amp_endpoint') && is_amp_endpoint()){
            return true;
        }
        
        if ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) {
            return true;
        }
        return false;
    }
}

if (!function_exists('essb_internal_cache_set')) {
    function essb_internal_cache_set($key, $value) {
        ESSB_Runtime_Cache::set($key, $value);
    }
}

if (!function_exists('essb_internal_cache_get')) {
    function essb_internal_cache_get($key) {
        return ESSB_Runtime_Cache::get($key);
    }
}

if (!function_exists('essb_internal_cache_remove')) {
    function essb_internal_cache_remove($key) {
        ESSB_Runtime_Cache::delete($key);
    }
}

if (!function_exists('essb_is_deactivated_on_uri')) {
    function essb_is_deactivated_on_uri($uris = array()) {
        
        $request_uri = explode( '?', $_SERVER['REQUEST_URI'] );
        $request_uri = reset( $request_uri );
        
        $is_deactivated = false;
        
        $reject_list = essb_generate_deactivate_running_uri($uris);
        if (preg_match( '#^(' . $reject_list . ')$#', $request_uri ) ) {
            $is_deactivated = true;
        }
        
        return $is_deactivated;
    }
}

if (!function_exists('essb_generate_deactivate_running_uri')) {
    function essb_generate_deactivate_running_uri($uris = array()) {
        
        $home_url = home_url('/');
        $home_root = wp_parse_url($home_url);
        
        if ( ! empty( $home_root['path'] ) ) {
            $home_root = '/' . trim( $home_root['path'], '/' );
            $home_root = rtrim( $home_root, '/' );
        } else {
            $home_root = '';
        }
        
        if ( '' !== $home_root ) {
            $home_root_escaped = preg_quote( $home_root, '/' );
            $home_root_len     = strlen( $home_root );
            
            foreach ( $uris as $i => $uri ) {
                if ( ! preg_match( '/' . $home_root_escaped . '\(?\//', $uri ) ) {
                    unset( $uris[ $i ] );
                    continue;
                }
                
                $uris[ $i ] = substr( $uri, $home_root_len );
            }
        }
        
        $uris = array_filter( $uris );
        
        if ( ! $uris ) {
            return '';
        }
        
        if ( '' !== $home_root ) {
            foreach ( $uris as $i => $uri ) {
                if ( preg_match( '/' . $home_root_escaped . '\(?\//', $uri ) ) {
                    $uris[ $i ] = substr( $uri, $home_root_len );
                }
            }
        }
        
        $uris = implode( '|', $uris );
        
        if ( '' !== $home_root ) {
            $uris = $home_root . '(' . $uris . ')';
        }
        
        return $uris;
    }
}