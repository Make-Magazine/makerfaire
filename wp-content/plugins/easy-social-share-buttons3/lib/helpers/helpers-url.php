<?php

/**
 * Get the current URL of site or post/page
 * 
 * @param string $post_id
 * @return unknown
 */
function essb_get_site_current_url ($post_id = '') {    
    $permalink = '';
    
    if (! $post_id) {
        if (isset($_SERVER['HTTP_HOST'])) {
            $port = (int) $_SERVER['SERVER_PORT'];
            $port = 80 !== $port && 443 !== $port ? ( ':' . $port ) : '';
            $url  = ! empty( $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] ) ? $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'] : ( ! empty( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );
            
            if ($port != '') {
                if (strpos($_SERVER['HTTP_HOST'], $port) !== false) {
                    $port = '';
                }
            }
            
            $permalink = 'http' . ( is_ssl() ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'] . $port . $url;            
        }
        else {
            global $wp;
            $permalink = add_query_arg($wp->query_string, '', esc_url(home_url('/')));
        }
    }
    else {
        $permalink = get_permalink($post_id);
    }
    
    return apply_filters('essb_current_permalink', $permalink, $post_id);
}


/**
 * @param string $url
 * @param string $code
 * @return string
 */
function essb_attach_tracking_code($url = '', $code = '') {
    
    $append_tag = strpos($url, '?') === false ? '?' : '&';
    
    return $code != '' ? $url . $append_tag . $code : $url;
}

/**
 * Compatibility with old functions
 * 
 * @param string $mode
 * @return unknown
 */
function essb_get_current_url ($mode = '') {
    return essb_get_site_current_url();
}

/**
 * Compatibility with old functions
 * 
 * @return unknown
 */
function essb_get_current_page_url() {
    return essb_get_site_current_url();
}

/**
 * Replace the domain with the CDN path when used from the settings
 * 
 * @param unknown $current_url
 * @param unknown $cdn_base
 */
function essb_apply_cdn_url($current_url, $cdn_base) {
    
    if ($cdn_base == '') {
        return $current_url;
    }
    else {
        $url_components = parse_url($current_url);
        $current_domain = '';
        
        if (isset($url_components['path']) && $url_components['path'] != '') {
            $current_domain = substr($current_url, 0, strpos($current_url, $url_components['path']));   
        }
        
        if (strpos($cdn_base, 'http://') === false && strpos($cdn_base, 'https://') === false) {
            $cdn_base = $url_components['scheme'] . '://' . $cdn_base;
        }
                
        if ($current_domain != '') {
            $current_url = str_replace ( $current_domain, $cdn_base, $current_url );
        }
        
        return $current_url;
    }
}