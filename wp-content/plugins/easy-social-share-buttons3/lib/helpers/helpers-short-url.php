<?php

/**
 * Function related to integration of short URLs
 * 
 * @author appscreo
 * @since 7.1
 * @package EasySocialShareButtons
 */
function essb_short_url($url, $provider, $post_id = '', $bitly_user = '', $bitly_api = '') {
    
    if (is_preview()) {
        return $url;
    }
    
    $deactivate_cache = essb_options_bool_value('deactivate_shorturl_cache');
    $shorturl_googlapi = essb_options_value('shorturl_googlapi');
    
    $bitly_api_version = essb_options_value('shorturl_bitlyapi_version');
    
    $rebrandly_api_key = essb_option_value('shorturl_rebrandpi');
    $post_api_key = essb_option_value('shorturl_postapi');
    
    $short_url = '';
        
    if ($provider == 'ssu') {
        if (! defined('ESSB3_SSU_VERSION')) {
            $provider = 'wp';
        }
    }
    
    switch ($provider) {
        case 'wp':
            $short_url = wp_get_shortlink($post_id);
            
            /**
             * 7.2.2 Fix passing UTM parameters to WordPress shortlink
             */
            if (strpos($url, '?') !== false) {
                $url_parts = explode('?', $url);
                $short_url = essb_attach_tracking_code($short_url, $url_parts[1]);                
            }
            
            break;
        case 'goo.gl':
            $short_url = essb_short_googl($url, $post_id, $deactivate_cache, $shorturl_googlapi);
            break;
        case 'bit.ly':
            $short_url = essb_short_bitly($url, $bitly_user, $bitly_api, $post_id, $deactivate_cache, $bitly_api_version);
            break;
        case 'rebrand.ly':
            $short_url = essb_short_rebrandly($url, $post_id, $deactivate_cache, $rebrandly_api_key);
            break;
        case 'po.st':
            $short_url = essb_short_post($url, $post_id, $deactivate_cache, $post_api_key);
            break;
        case 'pus':
            /**
             * @since 7.7 support for the Premium URL Shortener
             */
            $shorturl_pus_url = essb_option_value('shorturl_pus_url');
            $shorturl_pus_api = essb_option_value('shorturl_pus_api');
            
            $short_url = essb_short_pus($url, $post_id, $deactivate_cache, $shorturl_pus_url, $shorturl_pus_api);
            break;
        case 'ssu':
            $short_url = essb_short_ssu($url, $post_id, $deactivate_cache);
            break;
    }
    
    // @since 3.4 affiliate intergration with wp shorturl
    $affwp_active = essb_options_bool_value('affwp_active');
    if ($affwp_active && $provider != 'ssu') {
        essb_helper_maybe_load_feature('integration-affiliatewp');
        $short_url = essb_generate_affiliatewp_referral_link($short_url);
    }
    
    $affs_active = essb_options_bool_value('affs_active');
    if ($affs_active) {
        $short_url = do_shortcode('[affiliates_url]' . $short_url . '[/affiliates_url]');
    }
    
    return $short_url;
}

/**
 * Apply short URL over the existing share details
 *
 * @param array $post_share_details            
 * @param bool $only_recommended            
 * @param string $url            
 * @param string $provider            
 * @param string $post_id            
 * @param string $bitly_user            
 * @param string $bitly_api            
 *
 * @return array
 */
function essb_apply_shorturl($post_share_details, $only_recommended, $url, $provider, $post_id = '', $bitly_user = '', $bitly_api = '') {
    // generating short urls only for selected networks
    if ($only_recommended) {
        $generated_shorturl = essb_short_url($post_share_details['url'], $provider, get_the_ID(), essb_option_value('shorturl_bitlyuser'), essb_option_value('shorturl_bitlyapi'));
        $post_share_details['short_url_twitter'] = $generated_shorturl;
        $post_share_details['short_url_whatsapp'] = $generated_shorturl;
    }
    else {
        // generate short url for all networks
        $post_share_details['short_url'] = essb_short_url($post_share_details['url'], $provider, get_the_ID(), essb_option_value('shorturl_bitlyuser'), essb_option_value('shorturl_bitlyapi'));
        
        $post_share_details['short_url_twitter'] = $post_share_details['short_url'];
        $post_share_details['short_url_whatsapp'] = $post_share_details['short_url'];
    }
    
    if (empty($post_share_details['short_url'])) {
        $post_share_details['short_url'] = $post_share_details['url'];
    }
    if (empty($post_share_details['short_url_twitter'])) {
        $post_share_details['short_url_twitter'] = $post_share_details['url'];
    }
    if (empty($post_share_details['short_url_whatsapp'])) {
        $post_share_details['short_url_whatsapp'] = $post_share_details['url'];
    }
    
    return $post_share_details;
}

/**
 * Rebradn.ly Short URL Integration
 *
 * @param string $url            
 * @param mixed $post_id            
 * @param boolean $deactivate_cache            
 * @param string $api_key            
 * @return string
 * @since 4.3
 *       
 */
function essb_short_rebrandly($url, $post_id = '', $deactivate_cache = false, $api_key = '') {
    if (! empty($post_id) && ! $deactivate_cache) {
        $exist_shorturl = get_post_meta($post_id, 'essb_shorturl_rebrand', true);
        
        if (! empty($exist_shorturl)) {
            return $exist_shorturl;
        }
    }
    
    $domain_id = essb_sanitize_option_value('shorturl_rebrandpi_domain');
    $api_key = sanitize_text_field($api_key);
    
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
    if (is_wp_error($result))
        return $url;
    
    $result = json_decode($result['body']);
    $shortlink = isset($result->shortUrl) ? $result->shortUrl : '';
    
    if ($shortlink != '') {
        $shortlink = (essb_option_bool_value('shorturl_rebrandpi_https') ? 'https://' : 'http://') . $shortlink;
        
        if ($post_id != '') {
            update_post_meta($post_id, 'essb_shorturl_rebrand', $shortlink);
        }
        
        return $shortlink;
    }
    
    return $url;
}

function essb_short_post($url, $post_id = '', $deactivate_cache = false, $api_key = '') {
    if (! empty($post_id) && ! $deactivate_cache) {
        $exist_shorturl = get_post_meta($post_id, 'essb_shorturl_post', true);
        
        if (! empty($exist_shorturl)) {
            return $exist_shorturl;
        }
    }
    
    $api_key = sanitize_text_field($api_key);
    
    $encoded_url = $url;
    
    $result = wp_remote_get('http://po.st/api/shorten?longUrl=' . esc_url_raw($encoded_url) . '&apiKey=' . $api_key);
    
    // Return the URL if the request got an error.
    if (is_wp_error($result))
        return $url;
    
    $result = json_decode($result['body']);
    $shortlink = isset($result->short_url) ? $result->short_url : '';
    
    if ($shortlink != '') {
        if ($post_id != '') {
            update_post_meta($post_id, 'essb_shorturl_post', $shortlink);
        }
        
        return $shortlink;
    }
    
    return $url;
}

function essb_short_googl($url, $post_id = '', $deactivate_cache = false, $api_key = '') {
    if (! empty($post_id) && ! $deactivate_cache) {
        $exist_shorturl = get_post_meta($post_id, 'essb_shorturl_googl', true);
        
        if (! empty($exist_shorturl)) {
            return $exist_shorturl;
        }
    }
    
    /**
     * Goo.gl is no loger operating.
     * No new short URLs will be generated or outgoing calls will be set. But plugin
     * will return if stored and exist short as they are working.
     */
    
    return $url;
}

function essb_short_bitly($url, $user = '', $api = '', $post_id = '', $deactivate_cache = false, $bitly_api_version = '') {
    if (! empty($post_id) && ! $deactivate_cache) {
        $exist_shorturl = get_post_meta($post_id, 'essb_shorturl_bitly', true);
        
        if (! empty($exist_shorturl)) {
            return $exist_shorturl;
        }
    }
    
    $api = sanitize_text_field($api);
    
    $encoded_url = ($url);
    
    if ($bitly_api_version == 'new') {
        $params = http_build_query(array (
            'access_token' => $api,'uri' => ($encoded_url),'format' => 'json'
        ));
    }
    else {
        $params = http_build_query(array (
            'login' => $user,'apiKey' => $api,'longUrl' => $encoded_url,'format' => 'json'
        ));
    }
    
    $result = $url;
    
    $rest_url = 'https://api-ssl.bitly.com/v3/shorten?' . $params;
    
    $response = wp_remote_get($rest_url);
    // if we get a valid response, save the url as meta data for this post
    if (! is_wp_error($response)) {
        
        $json = json_decode(wp_remote_retrieve_body($response));
        if (isset($json->data->url)) {
            
            $result = $json->data->url;
            update_post_meta($post_id, 'essb_shorturl_bitly', $result);
        }
    }
    
    return $result;
}

function essb_short_ssu($url, $post_id, $deactivate_cache = false) {
    $result = $url;
    
    if (is_user_logged_in() && (essb_option_bool_value('mycred_referral_activate') || essb_option_bool_value('affwp_active'))) {
        $deactivate_cache = true;
    }
    
    if (! empty($post_id) && ! $deactivate_cache) {
        $exist_shorturl = get_post_meta($post_id, 'essb_shorturl_ssu', true);
        
        if (! empty($exist_shorturl)) {
            return $exist_shorturl;
        }
    }
    $short_url = '';
    if (defined('ESSB3_SSU_VERSION')) {
        if (class_exists('ESSBSelfShortUrlHelper')) {
            if ($post_id != '' && ! essb_option_bool_value('mycred_referral_activate') && ! essb_option_bool_value('affwp_active')) {
                $short_url = ESSBSelfShortUrlHelper::get_post_shorturl($post_id);
            }
            
            if ($short_url == '') {
                $short_url = ESSBSelfShortUrlHelper::get_external_short_url($url);
            }
            
            if (! empty($short_url)) {
                $result = ESSBSelfShortUrlHelper::get_base_path() . $short_url;
                if (! essb_option_bool_value('mycred_referral_activate') && ! essb_option_bool_value('affwp_active')) {
                    update_post_meta($post_id, 'essb_shorturl_ssu', $result);
                }
            }
        }
    }
    
    return $result;
}

/**
 * Premium URL Shortener support
 * 
 * @param unknown $url
 * @param unknown $post_id
 * @param string $deactivate_cache
 * @param string $api_url
 * @param string $api_key
 * @return unknown
 */
function essb_short_pus($url, $post_id, $deactivate_cache = false, $api_url = '', $api_key = '') {
    if (! empty($post_id) && ! $deactivate_cache) {
        $exist_shorturl = get_post_meta($post_id, 'essb_shorturl_pus', true);
        
        if (! empty($exist_shorturl)) {
            return $exist_shorturl;
        }
    }
    
    $result = $url;
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array ( 
        CURLOPT_URL => rtrim($api_url, "/") . "/api?api=" . $api_key . "&url=" . strip_tags(trim($url)), 
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
        update_post_meta($post_id, 'essb_shorturl_pus', $result);
    }

    return $result;
}