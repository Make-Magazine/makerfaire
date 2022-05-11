<?php

/**
 * Validate if plugin can run on the current resource
 *
 * @since 7.9
 * @author appscreo
 * @package EasySocialShareButtons
 */

class ESSB_Plugin_Loader {
    
    /**
     * Do not check twice the result
     * @var array
     */
    private static $checked = array();
    
    /**
     * Contains bound deactivation URL lists
     * @var array
     */
    private static $url_deactivations = array();
    
    /**
     * Return type of current content
     * @return string
     */
    public static function type () {
        $r = 'other';
        
        if (is_front_page()) {
            $r = 'front';
        }
        else if (is_home()) {
            $r = 'home';
        }
        else if (is_search()) {
            $r = 'search';
        }
        else if (is_category()) {
            $r = 'category';
        }
        else if (is_tag()) {
            $r = 'tag';
        }
        else if (is_tax()) {
            $r = 'tax';
        }
        else if (is_author()) {
            $r = 'author';
        }
        else if (is_single() || is_page()) {
            $r = 'single';
        }
        
        if (has_filter('essb_plugin_loader_type')) {
            $r = apply_filters('essb_plugin_loader_type', $r);
        }
        
        return $r;
    }
    
    /**
     * Get current post's type
     * @return string
     */
    public static function current_post_type () {
        return get_post_type( get_the_ID() );
    }
    
    /**
     * Validate if selected types can run
     * @param array $types
     * @return boolean
     */
    public static function type_selected($types = array()) {
        $r = false;
        
        if (!is_array($types)) {
            $types = array();
        }
        
        $archive_types = array('search', 'category', 'tag', 'tax', 'author', 'home');
        $current_type = self::type();
        
        // If this is an archive
        if (in_array($current_type, $archive_types)) {
            $r = in_array('all_lists', $types);
        }
        else if ($current_type == 'home') {
            $r = in_array('homepage', $types);
        }
        else {
            unset($types['homepage']);
            unset($types['all_lists']);
            
            $r = is_singular($types);
        }
        
        return $r;
    }
    
    /**
     * Check if the current URL is deactivated for the running
     * @param string $component (plugin for the plugin or module ID)
     * @param string $urls
     * @return boolean
     */
    public static function is_url_deactivated($component = 'plugin', $urls = '') {
        if (is_admin()) {
            return false;
        }        
        
        if (isset(self::$url_deactivations[$component])) {
            return self::$url_deactivations[$component];
        }
        else {
            $r = false;
            
            if (!empty($urls)) {
                if (!is_array($urls)) {
                    $urls = explode( "\n", $urls );
                    $urls = array_map( 'trim', $urls );
                    $urls = array_map( 'esc_url', $urls );
                    $urls = array_filter( $urls );
                    $urls = array_unique( $urls );
                }
                                
                self::$url_deactivations[$component] = essb_is_deactivated_on_uri($urls);
            }
            else {
                self::$url_deactivations[$component] = false;
            }
            
            return self::$url_deactivations[$component];
        }
    }
    
    /**
     * Check if the current post is deactivated (all options)
     * @param string $compoment
     * @return boolean
     */
    public static function is_post_deactivated($compoment = 'plugin') {
        $r = false;
        
        if (is_admin()) {
            return $r;
        }
        
        // Plugin is fully deactivate with the post option
        if (get_post_meta(get_the_ID(), 'essb_off', true) == 'true') {
            $r = true;
        }
        
        if ($compoment != 'plugin' && !$r) {
            if (get_post_meta(get_the_ID(), 'essb_off_component_' . $compoment, true) == 'true') {
                $r = true;
            }
        }
        
        return $r;
    }
    
    /**
     * Check if plugin is deactivated from the settings by ID of post, page, custom post type
     * @param string $component
     * @return boolean
     */
    public static function is_deactivated_by_id ($component = 'plugin') {
        $is_deactivated = false;
        
        if (is_admin()) {
            return $is_deactivated;
        }
        
        /**
         * Plugin is deactivated on
         */
        if ($component == 'plugin') {
            $display_deactivate_on = essb_options_value('display_deactivate_on');
            if ($display_deactivate_on != '') {
                $excule_from = explode(',', $display_deactivate_on);
                
                $excule_from = array_map('trim', $excule_from);
                if (in_array(get_the_ID(), $excule_from, false)) {
                    $is_deactivated = true;
                }
            }
        }
        else {
            /**
             * Module is deactivated on
             */
            $exclude_from = essb_options_value( 'deactivate_on_'.$component);
            if (!empty($exclude_from)) {
                $excule_from = explode(',', $exclude_from);
                
                $excule_from = array_map('trim', $excule_from);
                if (in_array(get_the_ID(), $excule_from, false)) {
                    $is_deactivated = true;
                }
            }
            
            /**
             * If module is: share (social share buttons)
             */
            if ($component == 'share' && is_single()) {
                $exclude_from = essb_options_value( 'deactivate_on_'.$component.'_cats');
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
            }
        }
        
        return $is_deactivated;
    }
    
    /**
     * Check if the plugin is deactivated on the selected location
     * @return boolean
     */
    public static function is_plugin_deactivated() {
        $is_deactivated = self::is_post_deactivated();
        
        if (!$is_deactivated) {
            $is_deactivated = self::is_url_deactivated('plugin', essb_option_value( 'url_deactivate_full'));
        }
        
        if (!$is_deactivated) {
            $is_deactivated = self::is_deactivated_by_id();
        }
        
        if (!$is_deactivated && has_filter('essb_is_plugin_deactivated_on')) {
            $is_deactivated = apply_filters('essb_is_plugin_deactivated_on', $is_deactivated);
        }
        
        return $is_deactivated;
    }
    
    public static function is_post_type_deactivated($component = 'plugin') {
        $r = false;
        
        if ($component != 'plugin') {
            $types = essb_option_value( 'posttype_deactivate_' . $component);
            if (!empty($types) && self::type_selected($types)) {
                $r = true;
            }
        }
        
        return $r;
    }
    
    /**
     * Check if module is deactivated on specific location
     * @param string $module_id
     * @return boolean
     */
    public static function is_module_deactivated($module_id = '') {       
        $is_deactivated = self::is_post_deactivated($module_id);        
        if (!$is_deactivated) {
            $is_deactivated = self::is_url_deactivated($module_id, essb_option_value( 'url_deactivate_' . $module_id));
        }
        
        if (!$is_deactivated) {
            $is_deactivated = self::is_deactivated_by_id($module_id);            
        }
        
        if (!$is_deactivated) {
            $is_deactivated = self::is_post_type_deactivated($module_id);
        }
        
        if (!$is_deactivated && has_filter('essb_is_module_deactivated_on')) {
            $is_deactivated = apply_filters('essb_is_module_deactivated_on', $module_id);
        }
        
        return $is_deactivated;
    }
    
    /**
     * Check if module is deactivated on the homepage
     * 
     * @since 8.3
     * @param string $module_id
     * @return boolean
     */
    public static function is_module_homepage_deactivate($module_id = '') {
        $r = false;
        $is_deactivated = essb_option_bool_value('home_deactivate_' . $module_id);
        
        if (!$is_deactivated && has_filter('essb_is_module_homepage_deactivated_on')) {
            $is_deactivated = apply_filters('essb_is_module_homepage_deactivated_on', $module_id);
        }
        
        
        
        if ($is_deactivated && (is_home() || is_front_page())) {
            $r = true;
        }

        return $r;
    }
    
    /**
     * Return a list of all supported devices for selection
     * @return array
     */
    public static function supported_device_types() {
        $r = array();
        
        $r['desktop'] = esc_html__('Desktop', 'essb');
        $r['tablet'] = esc_html__('Tablet', 'essb');
        $r['mobile'] = esc_html__('Mobile', 'essb');
    }
    
    /**
     * Return a list of all supported post types to choose from
     * @return array
     */
    public static function supported_post_types($homepage = true, $lists = true) {        
        global $wp_post_types;
        
        $r = array();
        
        if ($homepage) {
            $r['homepage'] = esc_html__('Homepage', 'essb');
        }
        
        if ($lists) {
            $r['all_lists'] = esc_html__('List of articles (blog, category, archive, etc.)', 'essb');
        }
        
        $pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
        $cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );
        
        foreach ($pts as $pt) {
            $r[$pt] = $wp_post_types[$pt]->label;   
        }
        
        foreach ($cpts as $pt) {
            $r[$pt] = $wp_post_types[$pt]->label;
        }
        
        return $r;
    }
}