<?php

/**
 * Static CSS loader
 *
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 7.2
 *
 */
class ESSB_Static_CSS_Loader {
    
    /**
     * CSS files loaded in the header
     */
    private static $header_files = array();
    
    /**
     * CSS files loaded in the footer
     */
    private static $footer_files = array();
    
    /**
     * Track when header styles will appear
     */
    private static $header_shown = false;
    
    /**
     * 
     * @var array
     */
    public static $active_resources = array();
    
    /**
     * @param string $key
     * @param string $url
     */
    public static function register_header_style($key = '', $url = '') {
        self::$header_files[$key] = $url;
        
        if (self::$header_shown) {
            self::$footer_files[$key] = $url;
        }
    }
    
    /**
     * @param string $key
     * @param string $url
     */
    public static function register_footer_style($key = '', $url = '') {
        if (!self::style_loaded($key)) {
            self::$footer_files[$key] = $url;
        }
    }
    
    /**
     * @param string $key
     * @return boolean
     */
    public static function style_loaded($key = '') {
        $r = false;
        
        foreach (self::$header_files as $loaded_key => $url) {
            if ($loaded_key == $key) {
                $r = true;
            }
        }
        
        if (!$r) {
            foreach (self::$footer_files as $loaded_key => $url) {
                if ($loaded_key == $key) {
                    $r = true;
                }
            }
        }
        
        return $r;
    }
    
    /**
     * @param string $key
     */
    public static function unregister_style($key = '') {
        if (isset(self::$header_files[$key])) {
            unset (self::$header_files[$key]);
        }
        
        if (isset(self::$footer_files[$key])) {
            unset (self::$footer_files[$key]);
        }
    }
    
    public static function get($type = 'header') {
        $r = array();
        
        if ($type == 'header' || $type == 'all') {
            $r = array_merge($r, self::$header_files);
        }
        
        if ($type == 'footer' || $type == 'all') {
            $r = array_merge($r, self::$footer_files);
        }
        
        return $r;
    }
}