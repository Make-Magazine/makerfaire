<?php
/**
 * Easy Social Share Buttons ESSB_Short_URL
 * Short URL dispatcher
 *
 * @class   ESSB_Short_URL
 * @package EasySocialShareButtons
 * @since 8.0
 */

class ESSB_ShareURL_UTM_Tracking {
    
    /**
     * Tracking is running
     * @var boolean
     */
    private static $active = false;
    
    /**
     * Source
     * @var string
     */
    private static $utm_source = '';
    
    /**
     * Medium
     * @var string
     */
    private static $utm_medium = '';
    
    /**
     * Campaign name
     * @var string
     */
    private static $utm_name = '';
    
    /**
     * Initialize and apply defaults
     */
    public static function init() {
        
        if (essb_option_bool_value('activate_utm')) {
            self::$active = true;
            
            self::$utm_source = essb_sanitize_option_value('activate_utm_source');
            self::$utm_medium = essb_sanitize_option_value('activate_utm_medium');
            self::$utm_name = essb_sanitize_option_value('activate_utm_name');
            
            if (empty(self::$utm_source)) {
                self::$utm_source = '{network}';
            }
            
            if (empty(self::$utm_medium)) {
                self::$utm_medium = 'social';
            }
            
            if (empty(self::$utm_name)) {
                self::$utm_name = 'EasySocialShareButtons';
            }
        }        
    }    
    
    /**
     * Get UTM tracking options
     * @return string[]
     */
    public static function get() {
        $utm = array(
            'utm_source' => self::$utm_source,
            'utm_medium' => self::$utm_medium,
            'utm_campaign' => self::$utm_name
        );
        
        /**
         * Integrate the custom post meta options essb_activate_ga_campaign_tracking
         */
        
        
        if (has_filter('essb_shareurl_utm_tracking')) {
            $utm = apply_filters('essb_shareurl_utm_tracking', $utm);
        }
        
        return $utm;
    }
    
    /**
     * Tracking is active or not
     * @return boolean
     */
    public static function is_active() {
        $state = self::$active;
        
        if (has_filter('essb_shareurl_utm_tracking_active')) {
            $state = apply_filters('essb_shareurl_utm_tracking_active', $state);
        }
        
        return $state;
    }
    
    /**
     * Check if the {network} variable is used in the options
     * @param array $utm
     * @return boolean
     */
    public static function has_network_variable() {
        if (!self::is_active()) {
            return false;
        }
        
        $r = false;
        
        $utm = self::get();
        
        foreach ($utm as $key => $value) {
            if ($value == '{network}') {
                $r = true;
            }
        }
        
        return $r;
    }
    
    /**
     * Attach UTM tracking code to the post sharing URL
     
     * @param string $url
     * @param string $network
     * @param string $post_title
     * @return string
     */
    public static function attach_tracking_code($url, $network = '', $post_title = '', $postid = '') {
        if (!self::is_active()) {
            return $url;
        }
        else {
            if (has_filter('essb_shareurl_utm_tracking_disable_' . $network)) {
                $state = false;
                $state = apply_filters('essb_shareurl_utm_tracking_disable_' . $network, $state);
                if ($state) {
                    return $url;
                }
            }
            
            $utm = self::get();
            foreach ($utm as $param => $value) {
                if ($value == '{network}') {
                    $utm[$param] = $network;
                }
                
                if ($value == '{title}') {
                    
                    if (empty($post_title)) {
                        $post_title = get_the_title(get_the_ID());
                    }
                    
                    $utm[$param] = $post_title;
                }
                
                if ($value == '{postid}') {
                    
                    if (empty($postid)) {
                        $postid = get_the_ID();
                    }
                    
                    $utm[$param] = $postid;
                }
            }
            
            return add_query_arg($utm, $url);
        }
    }    
    
    /**
     * Attach the UTM tracking to the share object
     * 
     * @param array $share
     * @param string $network
     * @return array
     */
    public static function attach_to_share_object($share = array(), $network = '') {
        
        $share['url'] = self::attach_tracking_code($share['url'], $network, $share['title'], $share['post_id']);
        $share['full_url'] = self::attach_tracking_code($share['url'], $network, $share['title'], $share['post_id']);
        $share['clear_twitter_url'] = false;
        
        $share['short_url_twitter'] = '';
        
        return $share;
    }
}

/**
 * Initialize
 */
ESSB_ShareURL_UTM_Tracking::init();