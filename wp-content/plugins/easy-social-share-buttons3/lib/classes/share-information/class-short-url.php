<?php
/**
 * Easy Social Share Buttons ESSB_Short_URL
 * Short URL dispatcher. Initialize in the end of the file
 *
 * @class   ESSB_Short_URL
 * @package EasySocialShareButtons
 * @since 8.0
 */

class ESSB_Short_URL {

    /**
     * Short URLs enabled
     * @var bool
     */
    private static $active = false;
    
    /**
     * Short URL service
     * @var string
     */
    private static $service = '';
    
    /**
     * URL generation mode (recommended or all)
     * @var string
     */
    private static $mode = '';
    
    /**
     * Deactivate the short URL cache
     * @var bool
     */
    private static $deactivate_cache = false;
    
    /**
     * The list of core support services
     * @var array
     */
    private static $supported_services = array('wp', 'bit.ly', 'rebrand.ly', 'po.st', 'pus');
    
    /**
     * Initialize class
     */
    public static function init() {
        self::$active = essb_option_bool_value('shorturl_activate');
        self::$mode = essb_option_bool_value('twitter_shareshort');
        self::$service = essb_option_value('shorturl_type');
        self::$deactivate_cache = essb_option_bool_value('deactivate_shorturl_cache');
        
        /**
         * @since 8.4 Missing integration with the self-short URLs
         */
        if (self::$service == 'ssu' && !defined('ESSB3_SSU_VERSION')) {
            self::$service = 'wp';
        }
    }
    
    /**
     * Deactivate short URL generation
     * @since 8.1
     */
    public static function deactivate() {
        self::$active = false;
    }
    
    /**
     * Is the short URL service active
     * 
     * @return boolean
     */
    public static function active() {
        return self::$active;
    }
    
    /**
     * Generate short URL and add inside the share object
     * 
     * @param array $share
     * @param string $network
     * @return string|unknown
     */
    public static function apply_short_to_share_object($share = array(), $network = '') {
        /**
         * Generate short URL for a network. The generate function will return or not short URL
         * for each of the social networks based on the recommended setup
         * 
         * @var Ambiguous $short
         */
        $short = self::generate_short_url($share['url'], $share['post_id'], $network);        
                
        if (!empty($short)) {
            /**
             * Integrate the short URL inside the shared object. The legacy parameters are also supported.
             */
            $share['short_url_whatsapp'] = $short;
            $share['short_url_twitter'] = $short;
            $share['short_url'] = $short;
        }
        
        return $share;
    }
    
    /**
     * Generate short URL function
     * 
     * @param string $url
     * @param string $post_id
     * @param string $network
     * @return string|string|unknown
     */
    public static function generate_short_url($url = '', $post_id = '', $network = '') {
        /**
         * If short URL service is not active return the base URL
         */
        if (!self::$active) {
            return $url;
        }
        else {
            if (self::should_generate_for_network($network)) {
                if (self::$service == 'wp') {
                    return self::generate_wp($url, $post_id);
                }
                if (self::$service == 'bit.ly') {
                    return self::generate_bitly_api4($url, $post_id, $network);
                }
                if (self::$service == 'rebrand.ly') {
                    return self::generate_rebrandly($url, $post_id, $network);
                }
                if (self::$service == 'po.st') {
                    return self::generate_post($url, $post_id, $network);
                }
                if (self::$service == 'pus') {
                    return self::generate_premium_short($url, $post_id, $network);
                }
                if (self::$service == 'ssu') {
                    return self::generate_self_hosted_short($url, $post_id, $network);
                }

                // service not supported in the core. Additional filters may apply
                if (!in_array(self::$service, self::$supported_services) && has_filter('essb_generate_short_url_' . self::$service)) {
                    $opts = array('url' => $url, 'post_id' => $post_id, 'network' => $network);
                    
                    $short = apply_filters('essb_generate_short_url_' . self::$service, $opts);
                    return !empty($short) ? $short : $url;
                }
                
                // return origianl URL in case the service is not detected
                return $url;
            }
            else {
                return $url;
            }
        }
    }
    
    /**
     * Chek if short URL should be generated based on the network
     * 
     * @param string $network
     * @return boolean
     */
    public static function should_generate_for_network($network = '') {
        $recommended = self::recommended_networks();
        
        if (self::$mode == 'true') {
            return in_array($network, $recommended);
        }
        else {
            return true;
        }
    }
    
    /**
     * Get a list of recommended social networks for short URL generation
     * 
     * @return string[]
     */
    public static function recommended_networks() {
        /**
         * @since 8.2.3 Adding also email to the list of networks
         * @var array $recommended
         */
        $recommended = array('twitter', 'copy', 'whatsapp', 'sms', 'viber', 'telegram', 'mail');
        
        if (has_filter('essb_generate_short_url_recommended')) {
            $recommended = apply_filters('essb_generate_short_url_recommended', $recommended);
        }
        
        return $recommended;
    }

    /**
     * Get meta cache ID for network 
     * 
     * @param string $service
     * @param string $network
     * @return string
     */
    public static function post_short_cache_id($service = '', $network = '') {
        $key = '';
        
        if ($service == 'bitly') {
            $key = 'essb_shorturl_bitly';            
        }
        
        if ($service == 'rebrandly') {
            $key = 'essb_shorturl_rebrand';
        }
        
        if ($service == 'post') {
            $key = 'essb_shorturl_post';
        }
        
        if ($service == 'pus') {
            $key = 'essb_shorturl_pus';
        }
        
        if ($service == 'ssu') {
            $key = 'essb_shorturl_ssu';
        }
        
        if (class_exists('ESSB_ShareURL_UTM_Tracking') && ESSB_ShareURL_UTM_Tracking::has_network_variable()) {
            $key .= '_' .$network;
        }
        
        /**
         * AffiliateWP integration - store short URL with affiliate id
         */
        if (essb_option_bool_value('affwp_active') && essb_option_bool_value('affwp_bridge_short')) {
            if (function_exists('affwp_is_affiliate') && is_user_logged_in () && affwp_is_affiliate ()) {
                $key .= '_' . 'affiliatewp_' . affwp_get_affiliate_id();
            }
        }
        
        /**
         * SliceWP integration - store short URL with affiliate ID
         */
        if (essb_option_bool_value('slicewp_active') && essb_option_bool_value('slicewp_bridge_short')) {
            if (function_exists('slicewp_is_user_affiliate') && is_user_logged_in () && slicewp_is_user_affiliate ()) {
                $key .= '_' . 'affiliatewp_' . slicewp_get_current_affiliate_id();
            }
        }
        
        return $key;
    }
    
    /**
     * Get meta cache ID
     * 
     * @param string $service
     * @return string
     */
    public static function post_base_short_cache_id($service = '') {
        $key = '';
        
        if (empty($service)) {
            $service = self::$service;
        }
        
        if ($service == 'bitly') {
            $key = 'essb_shorturl_bitly';
        }
        
        if ($service == 'rebrandly') {
            $key = 'essb_shorturl_rebrand';
        }
        
        if ($service == 'post') {
            $key = 'essb_shorturl_post';
        }
        
        if ($service == 'pus') {
            $key = 'essb_shorturl_pus';
        }
        
        if ($service == 'ssu') {
            $key = 'essb_shorturl_ssu';
        }
        
        return $key;
    }
    
    /**
     * Check if short URL is saved for a network
     * 
     * @param string $post_id
     * @param string $cache_key
     * @return string|unknown
     */
    public static function has_saved_url($post_id = '',  $cache_key = '') {
        // cache deactivated
        if (self::$deactivate_cache || empty($post_id)) {
            return '';
        }        
        
        $short_url = '';
        
        if (empty($short_url) && class_exists('ESSB_Post_Meta')) {
            $short_url = essb_get_post_meta($post_id, $cache_key);
        }
        
        /**
         * Reading the old short URLs
         */
        if (empty($short_url) && essb_option_bool_value('legacy_shorturl_cache')) {
            $short_url = get_post_meta($post_id, $cache_key, true);
        }
                
        return $short_url;
    }
    
    /**
     * Save short URL for selected social network in the meta cache
     * 
     * @param string $post_id
     * @param string $cache_key
     * @param string $cache_url
     */
    public static function save_url($post_id = '', $cache_key = '', $cache_url = '') {
        // cache deactivated
        if (self::$deactivate_cache || empty($post_id) || empty($cache_url)) {
            return;
        }        
                
        if (class_exists('ESSB_Post_Meta')) {
            essb_update_post_meta($post_id, $cache_key, $cache_url);
        }
        else {
            update_post_meta($post_id, $cache_key, $cache_url);
        }
    }
    
    /**
     * Generate short URL via the default WordPress function
     * 
     * @param string $url
     * @param string $post_id
     * @return string|unknown
     */
    public static function generate_wp($url = '', $post_id = '') {
        $short_url = wp_get_shortlink($post_id);        
        
        /**
         * 7.2.2 Fix passing UTM parameters to WordPress shortlink
         */
        if (strpos($url, '?') !== false) {
            $url_parts = explode('?', $url);
            $short_url = essb_attach_tracking_code($short_url, $url_parts[1]);
        }
        
        $short_url = ESSB_Site_Share_Information::attach_affiliate_to_url($short_url);
        
        return $short_url;
    }
    
    public static function generate_bitly_api4($url = '', $post_id = '', $network = '') {
        $api_key = essb_sanitize_option_value('shorturl_bitlyapi');
        // pending further implementation
        $shorten_domain = essb_sanitize_option_value('shorturl_bitlydomain');
        $group_guid = '';
        
        // without short url it is impossible to generate short URL
        if (empty($api_key)) {
            return $url;
        }
        
        $cache_key = self::post_short_cache_id('bitly', $network);
        
        // short URL exist
        $result = self::has_saved_url($post_id, $cache_key);
        if (!empty($result)) {
            return $result;
        }
        
        $encoded_url = $url;
        
        if(!$shorten_domain || empty($shorten_domain)){
            $payload = array(
                "group_guid" =>"".$group_guid."",
                "long_url"   =>"".$encoded_url.""
            );
        }else{
            $payload = array(
                "group_guid" =>"".$group_guid."",
                "domain"     =>"".$shorten_domain."",
                "long_url"   =>"".$encoded_url.""
            );
        }
        
        
        $json_payload = json_encode($payload);
        
        $headers = array (
            "Host"          => "api-ssl.bitly.com",
            "Authorization" => "Bearer ".$api_key ,
            "Content-Type"  => "application/json"
        );
        
        
        $result = $url;
        
        $response = wp_remote_post( "https://api-ssl.bitly.com/v4/shorten" , array(
            'method'      => 'POST',
            'headers'     => $headers,
            'body'        => $json_payload
        )
            );       
        
        
        if ( is_wp_error( $response ) ) {
            return $result;
        } else {
            $response_array = json_decode($response['body']);
            $result = isset($response_array->link) ? $response_array->link : $url;
            self::save_url($post_id, $cache_key, $result);            
        }       
        
        return $result;
    }
        
    /**
     * Generate short URL using bit.ly
     * 
     * @param string $url
     * @param string $post_id
     * @param string $network
     * @return string|string|unknown
     */
    public static function generate_bitly($url = '', $post_id = '', $network = '') {
        $api_key = essb_sanitize_option_value('shorturl_bitlyapi');
        
        // without short url it is impossible to generate short URL
        if (empty($api_key)) {
            return $url;
        }        
        
        $cache_key = self::post_short_cache_id('bitly', $network);
        
        // short URL exist
        $result = self::has_saved_url($post_id, $cache_key);
        if (!empty($result)) {
            return $result;
        }
        
        $encoded_url = $url;
        
        $params = http_build_query(array (
            'access_token' => $api_key,'uri' => $encoded_url,'format' => 'json'
        ));
        
        $result = $url;
        
        $rest_url = 'https://api-ssl.bitly.com/v3/shorten?' . $params;
        
        $response = wp_remote_get($rest_url);
        // if we get a valid response, save the url as meta data for this post
        if (! is_wp_error($response)) {
            
            $json = json_decode(wp_remote_retrieve_body($response));
            if (isset($json->data->url)) {
                
                $result = $json->data->url;
                self::save_url($post_id, $cache_key, $result);
            }
        }
        
        return $result;
    }
    
    /**
     * Generate short URL using rebrandly
     * 
     * @param string $url
     * @param string $post_id
     * @param string $network
     * @return string|string|unknown
     */
    public static function generate_rebrandly($url = '', $post_id = '', $network = '') {        
        $api_key = essb_sanitize_option_value('shorturl_rebrandpi');
        $domain_id = essb_sanitize_option_value('shorturl_rebrandpi_domain');        
        $https_always = essb_option_bool_value('shorturl_rebrandpi_https');
        
        if (empty($api_key)) {
            return $url;
        }
        
        $cache_key = self::post_short_cache_id('rebrandly', $network);
        
        // short URL exist
        $result = self::has_saved_url($post_id, $cache_key);
        if (!empty($result)) {
            return $result;
        }
        
        /**
         * @since 8.1.1
         */
        $encoded_url = $url;
        
        if ($domain_id != '') {
            $result = wp_remote_post('https://api.rebrandly.com/v1/links', array (
                'body' => json_encode(array (
                    'destination' => esc_url_raw($encoded_url),'domain' => array (
                        'id' => $domain_id
                    )
                )),'headers' => array (
                    'Content-Type' => 'application/json','apikey' => $api_key
                )
            ));
        }
        else {
            $result = wp_remote_post('https://api.rebrandly.com/v1/links', array (
                'body' => json_encode(array (
                    'destination' => esc_url_raw($encoded_url)
                )),'headers' => array (
                    'Content-Type' => 'application/json','apikey' => $api_key
                )
            ));
        }
        
        
        
        // Return the URL if the request got an error.
        if (is_wp_error($result)) {
            return $url;
        }
            
        $result = json_decode($result['body']);
        $shortlink = isset($result->shortUrl) ? $result->shortUrl : '';
            
        if ($shortlink != '') {
            $shortlink = ($https_always ? 'https://' : 'http://') . $shortlink;
            self::save_url($post_id, $cache_key, $shortlink);    
            return $shortlink;
        }
            
        return $url;
    }
    
    /**
     * Generate short URL using po.st
     * 
     * @param string $url
     * @param string $post_id
     * @param string $network
     * @return string|string|unknown
     */
    public static function generate_post($url = '', $post_id = '', $network = '') {
        $api_key = essb_sanitize_option_value('shorturl_postapi');
        
        if (empty($api_key)) {
            return $url;
        }
        
        $cache_key = self::post_short_cache_id('rebrandly', $network);
        
        // short URL exist
        $result = self::has_saved_url($post_id, $cache_key);
        if (!empty($result)) {
            return $result;
        }
        
        $result = wp_remote_get('http://po.st/api/shorten?longUrl=' . esc_url_raw($url) . '&apiKey=' . $api_key);
        
        // Return the URL if the request got an error.
        if (is_wp_error($result)) {
            return $url;
        }
            
        $result = json_decode($result['body']);
        $shortlink = isset($result->short_url) ? $result->short_url : '';
            
        if ($shortlink != '') {
            self::save_url($post_id, $cache_key, $shortlink);
            return $shortlink;
        }
            
        return $url;
    }
    
    /**
     * Generate short URL using Premium URL Shortener
     * 
     * @param string $url
     * @param string $post_id
     * @param string $network
     * @return string|string|unknown|unknown
     */
    public static function generate_premium_short($url = '', $post_id = '', $network = '') {
        $api_url = essb_sanitize_option_value('shorturl_pus_url');
        $api_key = essb_sanitize_option_value('shorturl_pus_api');
        
        if (empty($api_key) || empty($api_url)) {
            return $url;
        }
        
        $cache_key = self::post_short_cache_id('pus', $network);
        
        // short URL exist
        $result = self::has_saved_url($post_id, $cache_key);
        if (!empty($result)) {
            return $result;
        }
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array (
            CURLOPT_URL => rtrim($api_url, "/") . "/api?api=" . $api_key . "&url=" . urlencode(strip_tags(trim($url))),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array (
                "Authorization: Token " . $api_key, "Content-Type: application/json"
            )
        ));
        
        $short = curl_exec($curl);
        curl_close($curl);
        
        $short = json_decode($short, TRUE);
        
        if (!$short['error']) {
            $result = $short["short"];
            self::save_url($post_id, $cache_key, $result);
            return $result;
        }
        
        return $url;
    }
    
    /**
     * Integrated the Self Hosted Short URLs Add-on
     * @param string $url
     * @param string $post_id
     * @param string $network
     * @return string|string|unknown
     */
    public static function generate_self_hosted_short($url = '', $post_id = '', $network = '') {
        if (!defined('ESSB3_SSU_VERSION') || !class_exists('ESSBSelfShortUrlHelper')) {
            return $url;
        }
        
        $cache_key = self::post_short_cache_id('ssu', $network);
        $base_cache_key = self::post_base_short_cache_id('ssu');
        
        // short URL exist
        $result = self::has_saved_url($post_id, $cache_key);
        if (!empty($result)) {
            return $result;
        }
        
        $short_url = '';
        
        // No additional URL options
        if ($base_cache_key == $cache_key) {
            $short_url = ESSBSelfShortUrlHelper::get_post_shorturl($post_id);
        }
        
        if (empty($short_url)) {
            $short_url = ESSBSelfShortUrlHelper::get_external_short_url($url);
        }
        
        if (!empty($short_url)) {
            $short_url = ESSBSelfShortUrlHelper::get_base_path() . $short_url;
            self::save_url($post_id, $cache_key, $short_url);
            return $short_url;
        }
        
        return $url;
    }
    
    /**
     * Remove cached short URLs
     */
    public static function clear_cached_urls() {
        delete_post_meta_by_key('essb_shorturl_googl');
        delete_post_meta_by_key('essb_shorturl_post');
        delete_post_meta_by_key('essb_shorturl_bitly');
        delete_post_meta_by_key('essb_shorturl_ssu');
        delete_post_meta_by_key('essb_shorturl_rebrand');
        delete_post_meta_by_key('essb_shorturl_pus');
        
        if (class_exists('ESSB_Post_Meta')) {
            essb_delete_post_meta_by_key('essb_shorturl_googl');
            essb_delete_post_meta_by_key('essb_shorturl_post');
            essb_delete_post_meta_by_key('essb_shorturl_bitly');
            essb_delete_post_meta_by_key('essb_shorturl_ssu');
            essb_delete_post_meta_by_key('essb_shorturl_rebrand');
            essb_delete_post_meta_by_key('essb_shorturl_pus');
            
            // short URL by social network
            essb_delete_post_meta_by_matching_keys('essb_shorturl_post_%');
            essb_delete_post_meta_by_matching_keys('essb_shorturl_bitly_%');
            essb_delete_post_meta_by_matching_keys('essb_shorturl_rebrand_%');
            essb_delete_post_meta_by_matching_keys('essb_shorturl_pus_%');
        }
    }
    
    /**
     * Clear all cached short URLS for a single post
     * @param string $post_id
     */
    public static function clear_post_cached_urls($post_id = '') {
        delete_post_meta($post_id, 'essb_shorturl_googl');
        delete_post_meta($post_id, 'essb_shorturl_post');
        delete_post_meta($post_id, 'essb_shorturl_bitly');
        delete_post_meta($post_id, 'essb_shorturl_ssu');
        delete_post_meta($post_id, 'essb_shorturl_rebrand');
        delete_post_meta($post_id, 'essb_shorturl_pus');
        
        if (class_exists('ESSB_Post_Meta')) {
            essb_delete_post_meta($post_id, 'essb_shorturl_googl');
            essb_delete_post_meta($post_id, 'essb_shorturl_post');
            essb_delete_post_meta($post_id, 'essb_shorturl_bitly');
            essb_delete_post_meta($post_id, 'essb_shorturl_ssu');
            essb_delete_post_meta($post_id, 'essb_shorturl_rebrand');
            essb_delete_post_meta($post_id, 'essb_shorturl_pus');
            
            // short URL by social network
            essb_delete_post_meta_by_matching_keys('essb_shorturl_post_%', $post_id);
            essb_delete_post_meta_by_matching_keys('essb_shorturl_bitly_%', $post_id);
            essb_delete_post_meta_by_matching_keys('essb_shorturl_rebrand_%', $post_id);
            essb_delete_post_meta_by_matching_keys('essb_shorturl_pus_%', $post_id);
        }
    }
}

/**
 * Initialize the short URL
 */
ESSB_Short_URL::init();