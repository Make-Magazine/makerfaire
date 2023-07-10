<?php 

/**
 * Static resource cache. Packed all CSS and Javascript files and save them in a single file.
 * 
 * @author EasySocialShareButtons
 * @since 8.7
 */

class ESSB_Plugin_Assets_Cache {

    private static $cache_folder = "";
    private static $cache_url = "";
    private static $cache_time = YEAR_IN_SECONDS;
    private static $active = false;
    
    public static function is_active() {
        return self::$active;
    }
    
    public static function activate() {
        /**
         * Default path
         * @var Ambiguous $base_path
         */
        $base_path = ABSPATH.'wp-content/easysocialsharebuttons-assets/compiled/';
        $base_url = content_url('easysocialsharebuttons-assets/compiled/');
        
        $store_folder = essb_option_value('precompiled_folder');
        if ($store_folder == 'uploads') {
            $upload_dir = wp_upload_dir ();
            
            $base_path = $upload_dir ['basedir'] . '/essb-cache/';
            $base_url = $upload_dir['baseurl'] . '/essb-cache/';
        }
        
        if ($store_folder == 'plugin') {
            $base_path = ESSB3_PLUGIN_ROOT. 'essb-cache/';
            $base_url = ESSB3_PLUGIN_URL . '/essb-cache/';
        }
        
        
        if (has_filter('essb_precompiled_folder')) {
            $base_path = apply_filters('essb_precompiled_folder', $base_path);
        }
        
        if (has_filter('essb_precompiled_url')) {
            $base_url = apply_filters('essb_precompiled_url', $base_url);
        }
        
        
        if ( is_ssl()) {
            $base_url = str_replace( 'http://', 'https://', $base_url );
        }
        
        if (! is_dir ( $base_path )) {
            if (! wp_mkdir_p ( $base_path, 0777 )) {                
                return false;
            }
            
        }
        self::$cache_folder = $base_path;
        self::$cache_url = $base_url;
        
        define('ESSB3_PRECOMPILED_RESOURCE', true);
        self::$active = true;
        return true;
    }
    
    public static function key_parser($id) {
        $id = strtolower ( $id );
        $id = str_replace ( ' ', '_', $id );
        $id = md5($id);
        return $id;
    }
    
    public static function put_resource($id = '', $data = '', $type = 'css') {
        if (!self::$active) {
            return;
        }
        $id = self::key_parser ( $id );
        
        $filename = self::$cache_folder . $id . '.'.$type;          
        
        if (! file_put_contents ( $filename, $data )) {
            return false;
        }       
        
        return true;
    }
    
    public static function get_resource($id = '', $type = 'css') {
        if (!self::$active) {
            return "";
        }
        $id = self::key_parser ( $id );
        
        $cached_file = $id;
        $filename = self::$cache_folder . $id . '.'.$type;
        
        if ($cached_file != '' && file_exists ( $filename )) {
            $data = self::$cache_url. $cached_file . '.'.$type;
            return $data;
        }
        else {
            $filename = self::$cache_folder . $id . '.'.$type;
            if (file_exists ( $filename )) {
                $expires = self::$cache_time;
                $age = (time() - filemtime ($filename));
                if ($age < $expires) {
                    $data = self::$cache_url. $id . '.'.$type;
                    return $data;
                }
                else {
                    return "";
                }
            }
            else {
                return "";
            }
        }
    }
    
    public static function flush() {
        if (!self::$active) {
            return;
        }
        $base_path = self::$cache_folder;
        
        if (is_dir ( $base_path )) {
            self::recursive_remove_directory ( $base_path );
        }
        
        return false;
    }
    
    public static function recursive_remove_directory($directory) {
        foreach ( glob ( "{$directory}/*" ) as $file ) {
            if (is_dir ( $file )) {
                self::recursive_remove_directory ( $file );
            } else {
                unlink ( $file );
            }
        }
    }
    

    /**
     * Generate asset relative path
     * @param string $base_url
     * @param string $item_url
     * @return boolean|mixed
     */
    public static function get_asset_relative_path( $base_url = '', $item_url = '' ) {
        
        // Remove protocol reference from the local base URL
        $base_url = preg_replace( '/^(https?:\/\/|\/\/)/i', '', $base_url );
        
        // Check if this is a local asset which we can include
        $src_parts = explode( $base_url, $item_url );
        
        // Get the trailing part of the local URL
        $maybe_relative = end( $src_parts );
        
        if ( ! file_exists( ABSPATH . $maybe_relative ) )
            return false;
            
        return $maybe_relative;
    }
    

    /**
     * Generate a random string
     * 
     * @return string
     */
    public static function get_unique_identifier() {
        $stored_key = get_transient( 'essb_precache_unique_key');
        
        if (!$stored_key || empty($stored_key)) {
            $stored_key = self::generate_string(20);
            set_transient('essb_precache_unique_key', $stored_key, YEAR_IN_SECONDS );
        }
        
        return $stored_key;
    }
    
    
    
    /**
     * Generate an unique string with a number of symbols
     *
     * @param number $strength
     * @return string
     */
    public static function generate_string($strength = 16) {
        $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        
        return $random_string;
    }
}

if (!class_exists('ESSBPrecompiledResources')) {
    class ESSBPrecompiledResources extends ESSB_Plugin_Assets_Cache {
        
    }
}