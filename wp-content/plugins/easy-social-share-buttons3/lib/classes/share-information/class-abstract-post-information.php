<?php

/**
 * Single information for post sharing
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 7.1
 */
abstract class ESSB_Post_Information {

    /**
     * Title
     * 
     * @var string
     */
    public $title = '';

    /**
     * @var string
     */
    public $image = '';

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var string
     */
    public $description = '';
    
    /**
     * @var string
     */
    public $opengraph_title = '';
    
    /**
     * @var string
     */
    public $opengraph_image = '';
    
    /**
     * @var string
     */
    public $opengraph_description = '';
    
    /**
     * @var string
     */
    public $opengraph_url = '';
    
    /**
     * @var string
     */
    public $twitter_card_title = '';
    
    /**
     * @var string
     */
    public $twitter_card_image = '';
    
    /**
     * @var string
     */
    public $twitter_card_description = '';
    
    /**
     * @var string
     */
    public $twitter_card_url = '';
    
    /**
     * @var string
     */
    public $tweet = '';
    
    /**
     * @var string
     */
    public $tweet_user = '';
    
    /**
     * @var string
     */
    public $tweet_tags = '';
    
    /**
     * @var string
     */
    public $pinterest_image = '';
    
    /**
     * @var string
     */
    public $pinterest_message = '';
    
    /**
     * @var string
     */
    public $pinterest_id = '';
    
    /**
     * @var string
     */
    public $post_id = '';
    
    /**
     * Load post sharing details
     * 
     * @param unknown $post_id
     */
    public function load ($post_id = null) {
        $this->post_id = $post_id;
        
        $this->opengraph_title = $this->get_opengraph_title($post_id);
        $this->opengraph_description = $this->get_opengraph_description($post_id);
        $this->opengraph_image = $this->get_opengraph_image($post_id);
        $this->opengraph_url = $this->get_opengraph_url($post_id);
        
        $this->title = $this->get_title($post_id);
        $this->description = $this->get_description($post_id);
        $this->image = $this->get_image($post_id);
        $this->url = $this->get_url($post_id);
        
        // Tweet setup
        $this->load_custom_tweet($post_id);
        if ($this->tweet == '') {
            $this->tweet = $this->title;
        }
        
        // Generate Tweet hashtags based on post tags
        if ($this->tweet_tags == '' && essb_option_bool_value('twitter_message_tags_to_hashtags')) {
            $this->tweet_tags = essb_get_post_tags_as_list(get_post($post_id));
        }
        
        // Pinterest setup
        $this->load_pinterest_data($post_id);
        if ($this->pinterest_message == '') {
            $this->pinterest_message = $this->title;
        }
        if ($this->pinterest_image == '') {
            $this->pinterest_image = $this->image;
        }
        
        // Maybe load the Twitter card data
        $this->maybe_load_twitter_cards($post_id);
        
        // CDN support
        $this->apply_cdn_urls_if_needed();
    }
    
    /**
     * @return string[]|unknown[]
     */
    public function compile_share_object() {
        $url = $this->url;
        
        $url = ESSB_Site_Share_Information::attach_affiliate_to_url($url);
        
        return array ( 
            'url' => $url, 
            'title' => $this->prepare_text_value($this->title), 
            'image' => $this->image, 
            'description' => $this->strip_long_description($this->prepare_text_value($this->description)),
            // Added 7.3.1
            'description_plain' => $this->strip_long_description($this->prepare_text_value($this->description)), 
            'twitter_user' => $this->tweet_user, 
            'twitter_hashtags' => $this->tweet_tags, 
            'twitter_tweet' => $this->prepare_text_value($this->tweet), 
            'post_id' => $this->post_id, 
            'user_image_url' => '', 
            'title_plain' => $this->prepare_text_value($this->title), 
            'short_url_whatsapp' => '', 
            'short_url_twitter' => '', 
            'short_url' => '', 
            'pinterest_image' => $this->pinterest_image, 
            'pinterest_desc' => $this->prepare_text_value($this->pinterest_message), 
            'pinterest_id' => $this->pinterest_id 
        );
    }


    /**
     * Loading opengraph social media optimization details
     * 
     * @param unknown $post_id
     * @return string|NULL|unknown
     */
    public function get_opengraph_title ($post_id = null) {
        $title = get_post_meta ( $post_id, 'essb_post_og_title', true );
        
        if (empty($title) && $this->integration_is_running()) {
            $title = $this->otherplugin_integration_value('og_title');
        }
        
        if ($this->wpseo_detected()) {
            // import SEO details
            if (empty($title) && !essb_option_bool_value('deactivate_pair_yoast_sso')) {
                $title = get_post_meta( $post_id, '_yoast_wpseo_opengraph-title' , true );
            }
            
            if (empty($title) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_seo')) {
                $title = get_post_meta( $post_id, '_yoast_wpseo_title' , true );
            }
            
            // include WPSEO replace vars
            if ($this->wpseo_detected() && strpos($title, '%%') !== false && function_exists('wpseo_replace_vars')) {
                $title = wpseo_replace_vars($title, get_post($post_id));
            }
        }        
        
        if (empty($title)) {
            $title = get_the_title ($post_id);
            
            /**
             * Added strip all html code - Secondary Title plugin
             * @since 7.4.1
             */
            
            if (defined('SECONDARY_TITLE_VERSION')) {
                $title = wp_strip_all_tags($title);
            }
            
            /**
             * Added strip all html code - Transposh
             * @since 7.7.4
             */
            if (class_exists('transposh_plugin')) {
                $title = wp_strip_all_tags($title);
            }
            
            
            $title = trim( essb_core_convert_smart_quotes( htmlspecialchars_decode($title)));;
        }
        
        return $title;
    }
    
    /**
     * Loading sharing title
     * 
     * @param unknown $post_id
     * @return string|NULL|unknown
     */
    public function get_title($post_id = null) {
        // Loading the social media optimizations
        $title = $this->get_opengraph_title($post_id);
        
        if (essb_option_bool_value('customshare')) {
            $custom_global_share_title = essb_option_value('customshare_text');
        }
        
        $post_essb_post_share_text = get_post_meta($post_id, 'essb_post_share_text', true);
        if ($post_essb_post_share_text != '') {
            $title = $post_essb_post_share_text;
        }
        
        return $title;
    }
    
    
    /**
     * @param unknown $post_id
     * @return string|NULL|unknown
     */
    public function get_opengraph_description ($post_id = null) {
        $description = get_post_meta ( $post_id, 'essb_post_og_desc', true );
        
        if (empty($description) && $this->integration_is_running()) {
            $description = $this->otherplugin_integration_value('og_description');
        }
        
        // import SEO details
        if (empty($description) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_sso')) {
            $description = get_post_meta( $post_id, '_yoast_wpseo_opengraph-description' , true );
        }
        if (empty($description) && $this->wpseo_detected() && !essb_option_bool_value('deactivate_pair_yoast_seo')) {
            $description = get_post_meta( $post_id, '_yoast_wpseo_metadesc' , true );
        }
        
        if (empty($description)) {
            // Add reading directly of the content instead of default WordPress functions
            // perfromace optimization: https://github.com/appscreo/easy-social-share-buttons3/issues/52
            
            $description = trim( essb_core_convert_smart_quotes( htmlspecialchars_decode(essb_core_get_post_excerpt($post_id))));
            
            // Adding an additional description filter
            if (has_filter("essb_get_opengraph_description")) {
                $description = apply_filters("essb_get_opengraph_description", $description);
            }
            
        }
        
        return $description;
    }

    /**
     * @param unknown $post_id
     * @return unknown|string|NULL
     */
    public function get_description ($post_id) {
        $description = $this->get_opengraph_description($post_id);
        
        if (essb_option_bool_value('customshare')) {            
            $custom_description = essb_option_value('customshare_description');
            if ($custom_description != '') {
                $description = $custom_description;
            }
        }
        
        $post_essb_post_share_text = get_post_meta($post_id, 'essb_post_share_text', true);
        if ($post_essb_post_share_text != '') {
            $description = $post_essb_post_share_text;
        }
        
        return $description;
    }
    
    /**
     * @param unknown $post_id
     * @return unknown
     */
    public function get_opengraph_image ($post_id = null) {
        $image = get_post_meta ( $post_id, 'essb_post_og_image', true );
        
        if (empty($image) && $this->integration_is_running()) {
            $image = $this->otherplugin_integration_value('og_image');
        }
        
        // import SEO details
        if (empty($image) && $this->wpseo_detected()) {
            $image = get_post_meta( $post_id, '_yoast_wpseo_opengraph-image' , true );
        }
        
        if (empty($image)) {
            $image = essb_core_get_post_featured_image($post_id);
        }
        
        if (empty($image)) {
            $image = essb_option_value('sso_default_image');
        }
        
        return $image;
    }
    
    /**
     * @param unknown $post_id
     * @return unknown
     */
    public function get_image($post_id = null) {
        $image = $this->get_opengraph_image($post_id);
        
        // apply custom share options
        if (essb_option_bool_value('customshare')) {            
            if (essb_option_value('customshare_image') != '') {
                $image = essb_option_value('customshare_image');
            }
        }
        
        $post_essb_post_share_image = get_post_meta($post_id, 'essb_post_share_image', true);
        if ($post_essb_post_share_image != '') {
            $image = $post_essb_post_share_image;
        }
        
        return $image;
    }
    
    /**
     * @param unknown $post_id
     * @return unknown
     */
    public function get_opengraph_url($post_id = null) {
        $url = get_permalink($post_id);
        
        $custom_og_url = get_post_meta ( $post_id, 'essb_post_og_url', true );
        if ($custom_og_url != '') {
            $url = $custom_og_url;
        }
        
        return $url;
    }
    
    /**
     * @param unknown $post_id
     * @return unknown
     */
    public function get_url ($post_id = null) {
        $url = $this->get_opengraph_url($post_id);
        
        // apply additional settings over the share URL
        if (essb_option_bool_value( 'avoid_nextpage')) {
            $url = $post_id ? get_permalink($post_id) : essb_get_current_url( 'raw' );
        }
        
        if (essb_option_bool_value('force_wp_fullurl')) {
            $url = essb_get_current_page_url();
        }
        
        // apply custom share options
        if (essb_option_bool_value('customshare')) {
            if (essb_option_value('customshare_url') != '') {
                $url = essb_option_value('customshare_url');
            }
        }
        
        $post_essb_post_share_url = get_post_meta($post_id, 'essb_post_share_url', true);
        if ($post_essb_post_share_url != '') {
            $url = $post_essb_post_share_url;
        }
        
        return $url;
    }
    
    
    /**
     * Load custom Tweet assigned for post
     * 
     * @param unknown $post_id
     */
    public function load_custom_tweet($post_id = null) {
        $this->tweet_user = get_post_meta($post_id, 'essb_post_twitter_username', true);
        $this->tweet_tags = get_post_meta($post_id, 'essb_post_twitter_hashtags', true);
        $this->tweet = get_post_meta($post_id, 'essb_post_twitter_tweet', true);        
        
        // Social Warfare integration
        $sw_previous_tweet = $this->otherplugin_integration_value('custom_tweet');
        if ($this->tweet == '' && $sw_previous_tweet != '') {
            $this->tweet = $sw_previous_tweet;
        }
        
        // Loading global defaults if blank
        
        if ($this->tweet_user == '') {
            $this->tweet_user = essb_sanitize_option_value('twitteruser');
        }
        
        if ($this->tweet_tags == '') {
            $this->tweet_tags = essb_sanitize_option_value('twitterhashtags');
        }
        
        if ($this->tweet_user != '') {
            $this->tweet_user = str_replace('@', '', $this->tweet_user);
        }
    }
    
    /**
     * Load post Pinterest information
     * 
     * @param unknown $post_id
     */
    public function load_pinterest_data ($post_id = null) {
        $this->pinterest_message = get_post_meta( $post_id, 'essb_post_pin_desc', true);
        $this->pinterest_id = get_post_meta( $post_id, 'essb_post_pin_id', true);
        $this->pinterest_image = get_post_meta($post_id, 'essb_post_pin_image', true);
        
        // Social Warfare integration
        $sw_previous_image = $this->otherplugin_integration_value('pin_image');
        $sw_previous_desc = $this->otherplugin_integration_value('pin_description');
        
        if ($this->pinterest_image == '' && $sw_previous_image != '') {
            $this->pinterest_image = $sw_previous_image;
        }
        
        if ($this->pinterest_message == '' && $sw_previous_desc != '') {
            $this->pinterest_message = $sw_previous_desc;
        }
    }
    
    /**
     * @param unknown $post_id
     */
    public function maybe_load_twitter_cards($post_id = null) {
        if (essb_option_bool_value('twitter_card')) {
            $this->twitter_card_description = $this->opengraph_description;
            $this->twitter_card_image = $this->opengraph_image;
            $this->twitter_card_title = $this->opengraph_title;
            $this->twitter_card_url = $this->opengraph_url;
            
            $twitter_description =  get_post_meta(get_the_ID(),'essb_post_twitter_desc',true);
            $twitter_title =  get_post_meta(get_the_ID(),'essb_post_twitter_title',true);
            $twitter_image =  get_post_meta(get_the_ID(),'essb_post_twitter_image',true);
            
            if ($twitter_description != '') { 
                $this->twitter_card_description = $twitter_description;
            }
            
            if ($twitter_title != '') {
                $this->twitter_card_title = $twitter_title;
            }
            
            if ($twitter_image != '') {
                $this->twitter_card_image = $twitter_image;
            }
        }
    }
    
    /**
     * Apply CDN on the social media optimization tags and custom PIN image
     * 
     * @since 7.3
     */
    public function apply_cdn_urls_if_needed() {
        if (essb_option_bool_value('activate_cdn_sso') || essb_option_bool_value('activate_cdn_pinterest')) {
            $cdn_domain = essb_sanitize_option_value('cdn_domain');
            
            // Pinterest
            if (essb_option_bool_value('activate_cdn_pinterest') && $this->pinterest_image != '') {
                $this->pinterest_image = essb_apply_cdn_url($this->pinterest_image, $cdn_domain);
            }
            
            // SSO Tags
            if (essb_option_bool_value('activate_cdn_sso')) {
                if ($this->opengraph_image != '') {
                    $this->opengraph_image = essb_apply_cdn_url($this->opengraph_image, $cdn_domain);
                }
                
                if ($this->twitter_card_image != '') {
                    $this->twitter_card_image = essb_apply_cdn_url($this->twitter_card_image, $cdn_domain);
                }
            }
        }
    }
    
    /**
     * Getting opengraph value
     * 
     * @param string $param
     * @return string
     */
    public function opengraph_value($param = '') {
        if ($param == 'title') {            
            return $this->prepare_text_value($this->opengraph_title);
        }
        else if ($param == 'description') {
            return $this->prepare_text_value($this->opengraph_description);
        }
        else if ($param == 'image') {
            return $this->opengraph_image;
        }
        else if ($param == 'url') {
            return $this->opengraph_url;
        }
        else {
            return '';
        }
    }
    
    /**
     * @param string $param
     * @return string
     */
    public function twittercard_value($param = '') {
        if ($param == 'title') {
            return $this->prepare_text_value($this->twitter_card_title);
        }
        else if ($param == 'description') {
            return $this->prepare_text_value($this->twitter_card_description);
        }
        else if ($param == 'image') {
            return $this->twitter_card_image;
        }
        else if ($param == 'url') {
            return $this->twitter_card_url;
        }
        else {
            return '';
        }
    }
    
    /**
     * @param string $value
     * @return string
     */
    private function prepare_text_value($value = '') {
        // stripslashes/wp_strip_all_tags
        $value = str_replace('&nbsp;', '', $this->remove_shortcodes_keep_content($value));
        return trim(strip_shortcodes(addslashes($value)));
    }
    
    /**
     * Truncate description - issue appearing in share to few networks if it is too long
     * 
     * @param string $value
     * @return string
     */
    private function strip_long_description($value = '') {
        $max_length = 256;
        
        if (has_filter("essb/helpers/share_description_length")) {
            $description = apply_filters("essb/helpers/share_description_length", $max_length);
        }
        
        if (strlen($value) > $max_length) {
            $value = substr($value, 0, $max_length);
            $value .= '...';
        }
        
        return $value;
    }
    
    /**
     * Remove shortcodes but keep contents inside
     * 
     * @param string $value
     * @return mixed
     */
    private function remove_shortcodes_keep_content($value = '') {
        return preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $value);        
    }
    
    /**
     * Integration value dispatcher
     * 
     * @param string $key
     * @return string|NULL|unknown
     */
    private function otherplugin_integration_value ($key = '') {
        $value = '';
        
        if (essb_option_bool_value('activate_sw_bridge')) {
            $value = $this->socialwarfare_integration_value($key);
        }
        
        if (essb_option_bool_value('activate_ss_bridge')) {
            $value = $this->socialsnap_integration_value($key);
        }
        
        if (essb_option_bool_value('activate_ms_bridge')) {
            $value = $this->mashshare_integration_value($key);
        }
        
        return $value;
    }
    
    /**
     * Detect if integration is running
     * 
     * @return boolean
     */
    private function integration_is_running() {
        return essb_option_bool_value('activate_sw_bridge') || essb_option_bool_value('activate_ss_bridge') || essb_option_bool_value('activate_ms_bridge');
    }

    /**
     * Read previous set values in Social Warfare
     * 
     * @param string $key
     * @return string|NULL|unknown|string
     */
    private function socialwarfare_integration_value ($key = '') {
        if (essb_option_bool_value('activate_sw_bridge') && function_exists('essb_sw_custom_data')) {
            $sw_setup = essb_sw_custom_data();
            return isset($sw_setup[$key]) ? $sw_setup[$key] : '';
        }
        else {
            return '';
        }
    }
    
    /**
     * Read previous set values in Social Snap
     * 
     * @param string $key
     * @return string|NULL|unknown|string
     */
    private function socialsnap_integration_value ($key = '') {
        if (essb_option_bool_value('activate_ss_bridge') && function_exists('essb_ss_custom_data')) {
            $sw_setup = essb_ss_custom_data();
            return isset($sw_setup[$key]) ? $sw_setup[$key] : '';
        }
        else {
            return '';
        }
    }
    
    /**
     * Read previous set values in MashShare
     * 
     * @param string $key
     * @return string|NULL|unknown|string
     */
    private function mashshare_integration_value ($key = '') {
        if (essb_option_bool_value('activate_ms_bridge') && function_exists('essb_ms_custom_data')) {
            $sw_setup = essb_ms_custom_data();
            return isset($sw_setup[$key]) ? $sw_setup[$key] : '';
        }
        else {
            return '';
        }
    }
    
    /**
     * Detect running WordPress SEO plugin to get settings that are set for SEO on post
     *
     * @return boolean
     */
    public function wpseo_detected () {
        return defined('WPSEO_VERSION') ? true: false;
    }
}