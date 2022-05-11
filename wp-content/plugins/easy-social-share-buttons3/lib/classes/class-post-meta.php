<?php 
/**
 * Easy Social Share Buttons ESSB_Post_Meta
 * Custom metadata manager for Easy Social Share Buttons
 *
 * @class   ESSB_Post_Meta
 * @package EasySocialShareButtons
 * @since 8.0
 */

defined( 'ABSPATH' ) || exit;

class ESSB_Post_Meta {
    /**
     * Name of the table remain private to avoid hook
     * @var string
     */
    private static $table_name = 'essb_post_meta';
    
    /**
     * Internal cache of already read values
     * @var array
     */
    private static $cache = array();
    
    /**
     * Clear internal meta cache
     */
    public static function meta_cache_flush() {
        self::$cache = array();
    }
    
    /**
     * Set value in internal meta cache (prevent multiple readings)
     * @param string $post_id
     * @param string $meta_key
     * @param string $meta_value
     */
    public static function meta_cache_set($post_id = '', $meta_key = '', $meta_value = '') {
        if (!isset(self::$cache[$post_id])) {
            self::$cache[$post_id] = array();
        }
        
        self::$cache[$post_id][$meta_key] = $meta_value;
    }
    
    /**
     * Exist key in the cache
     * @param string $post_id
     * @param string $meta_key
     * @return boolean
     */
    public static function meta_cache_exists($post_id = '', $meta_key = '') {
        if (isset(self::$cache[$post_id]) && isset(self::$cache[$post_id][$meta_key])) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Read cached value
     * @param string $post_id
     * @param string $meta_key
     * @return mixed|boolean
     */
    public static function meta_cache_get($post_id = '', $meta_key = '') {
        if (isset(self::$cache[$post_id]) && isset(self::$cache[$post_id][$meta_key])) {
            return self::$cache[$post_id][$meta_key];
        }
        else {
            return false;
        }
    }
    
    /**
     * Delete cached meta for a single key or entire post
     * @param string $post_id
     * @param string $meta_key
     */
    public static function meta_cache_delete($post_id = '', $meta_key = '') {
        if (!empty($meta_key)) {
            if (isset(self::$cache[$post_id]) && isset(self::$cache[$post_id][$meta_key])) {
                unset(self::$cache[$post_id][$meta_key]);
            }
        }
        else {
            if (isset(self::$cache[$post_id])) {
                unset(self::$cache[$post_id]);
            }
        }
    }
    
    /**
     * Delete all cached key records (for all posts)
     * @param string $meta_key
     */
    public static function meta_cache_delete_by_key($meta_key = '') {
        foreach (self::$cache as $post_id => $keys) {
            if (isset($keys[$meta_key])) {
                unset (self::$cache[$post_id][$meta_key]);
            }
        }
    }
    
    /**
     * Read all post meta data
     * 
     * @param string $post_id
     * @return NULL[]
     */
    public static function read_post_meta($post_id = '') {
        global $wpdb;
        
        self::meta_cache_delete($post_id);
        
        if (empty($post_id)) {
            $post_id = get_the_ID();
        }
        
        $table_name = $wpdb->prefix . self::$table_name;
        $rows = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM {$table_name} WHERE post_id = %d", $post_id));
        
        $result = array();
        
        foreach ($rows as $row) {
            self::meta_cache_set($post_id, $row->meta_key, maybe_unserialize($row->meta_value));
            $result[$row->meta_key] = maybe_unserialize($row->meta_value);
        }
        
        return $result;
    }
    
    /**
     * Read post meta value
     * @param string $post_id
     * @param string $meta_key
     * @return unknown
     */
    public static function get_post_meta($post_id = '', $meta_key = '') {
        global $wpdb;
        
        if (self::meta_cache_exists($post_id, $meta_key)) {
            return self::meta_cache_get($post_id, $meta_key);
        }
        
        if (empty($post_id)) {
            $post_id = get_the_ID();
        }
        
        $table_name = $wpdb->prefix . self::$table_name;
        $value = maybe_unserialize($wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$table_name} WHERE post_id = %d AND meta_key = %s", $post_id, $meta_key)));
        
        self::meta_cache_set($post_id, $meta_key, $value);
        return $value;
    }
    
    /**
     * Read multiple post meta keys
     * @param string $post_id
     * @param array $meta_keys
     * @return string[]|NULL[]
     */
    public static function get_post_meta_keys($post_id = '', $meta_keys = array()) {
        $r = array();
        
        $post_metas = self::read_post_meta($post_id);
        
        foreach ($meta_keys as $key) {
            $r[$key] = isset($post_metas[$key]) ? $post_metas[$key] : '';
        }
        
        return $r;
    }
    
    /**
     * Read multiple post meta keys
     * @param string $post_id
     * @param array $meta_keys
     * @return string[]|NULL[]
     */
    public static function get_post_meta_matching_keys($post_id = '', $meta_prefix = '') {
        $r = array();
        
        $post_metas = self::read_post_meta($post_id);
        
        foreach ($post_metas as $key => $value) {
            if (strpos($key, $meta_prefix) !== false) {
                $r[$key] = $value;
            }
        }
        
        return $r;
    }
    
    /**
     * Save custom post meta value
     * 
     * @param string $post_id
     * @param string $meta_key
     * @param string $meta_value
     */
    public static function save_post_meta($post_id = '', $meta_key = '', $meta_value = '') {
        global $wpdb;
        
        // Make sure meta is added to the post, not a revision.
        $the_post = wp_is_post_revision( $post_id );
        if ( $the_post ) {
            $post_id = $the_post;
        }
        
        $table_name = $wpdb->prefix . self::$table_name;
        
        $has_meta_id = self::get_post_meta_id($post_id, $meta_key);        
        
        $result = $wpdb->replace($table_name, array ( 
            'meta_id' => (! empty($has_meta_id) ? $has_meta_id : ''), 
            'post_id' => $post_id, 
            'meta_key' => $meta_key, 
            'meta_value' => maybe_serialize($meta_value) 
        ), array ( 
            '%d', '%d', '%s', '%s' 
        ));
        
        self::meta_cache_set($post_id, $meta_key, $meta_value);
        
        return $result;
    }
    
    /**
     * Return meta_id column for a specific meta value
     * @param string $post_id
     * @param string $meta_key
     * @return unknown
     */
    public static function get_post_meta_id($post_id = '', $meta_key = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::$table_name;
        return $wpdb->get_var($wpdb->prepare("SELECT meta_id FROM {$table_name} WHERE post_id = %d AND meta_key = %s", $post_id, $meta_key));
    }
    
    /**
     * Remove specific post single meta key or all (if $meta_key is empty)
     * @param string $post_id
     * @param string $meta_key
     */
    public static function delete_post_meta($post_id = '', $meta_key = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::$table_name;
        
        if (empty($meta_key)) {
            $sql = $wpdb->prepare("DELETE FROM {$table_name} WHERE post_id = %d", $post_id);
            self::meta_cache_delete($post_id);
        }
        else {
            $sql = $wpdb->prepare("DELETE FROM {$table_name} WHERE post_id = %d AND meta_key = %s", $post_id, $meta_key);
            self::meta_cache_delete($post_id, $meta_key);
        }
        
        return $wpdb->query($sql);
    }
    
    /**
     * Delete all meta_key records
     * @param string $meta_key
     */
    public static function delete_post_meta_by_key($meta_key = '', $post_id = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::$table_name;
        if (!empty($post_id)) {
            $sql = $wpdb->prepare("DELETE FROM {$table_name} WHERE meta_key = %s AND post_id = %d", $meta_key, $post_id);
        }
        else {
            $sql = $wpdb->prepare("DELETE FROM {$table_name} WHERE meta_key = %s", $meta_key);
        }
        
        self::meta_cache_delete_by_key($meta_key);
        return $wpdb->query($sql);
    }
    
    /**
     * Delete matching meta key records (example bitly_%)
     * @param string $meta_match_key
     */
    public static function delete_post_meta_by_matching_keys($meta_match_key = '', $post_id = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . self::$table_name;
        if (!empty($post_id)) {
            $sql = $wpdb->prepare("DELETE FROM {$table_name} WHERE meta_key LIKE %s AND post_id = %d", $meta_match_key, $post_id);
        }
        else {
            $sql = $wpdb->prepare("DELETE FROM {$table_name} WHERE meta_key LIKE %s", $meta_match_key);
        }
        
        return $wpdb->query($sql);
    }
    
    /**
     * Create the database table
     * @since 8.0
     */
    public static function install($prefix = '') {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $max_index_length = 191;
        $table_name = (!empty($prefix) ? $prefix : $wpdb->prefix) . self::$table_name;
        
        $sql = "CREATE TABLE {$table_name} (
            meta_id BIGINT(20) NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20),
            meta_key VARCHAR(255) DEFAULT '' NOT NULL,
            meta_value LONGTEXT,
            PRIMARY KEY (meta_id),
            KEY post_id (post_id),
            KEY meta_key (meta_key($max_index_length))
        ) $charset_collate;";        
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');        
        dbDelta($sql);
    }
    
    /**
     * Clear stored table information
     * @since 8.0
     */
    public static function clear_data() {
        global $wpdb;
        $table  = $wpdb->prefix . self::$table_name;
        $delete = $wpdb->query(("TRUNCATE TABLE $table"));
        
        self::meta_cache_flush();
    }
    
    /**
     * Clear stored table information and delete it from the database
     * @since 8.0
     */
    public static function uninstall() {
        global $wpdb;
        $table  = $wpdb->prefix . self::$table_name;
        $wpdb->query( "DROP TABLE IF EXISTS ".$table );
        
        self::meta_cache_flush();
    }
}

if (!function_exists('essb_get_post_meta')) {
    /**
     * Retrieves a post meta field for the given post ID.
     * 
     * @param int $post_id
     * @param string $meta_key
     * @return mixed
     */
    function essb_get_post_meta($post_id = '', $meta_key = '') {
        return ESSB_Post_Meta::get_post_meta($post_id, $meta_key);
    }
}

if (!function_exists('essb_get_post_meta_keys')) {
    /**
     * Retrieves a post meta fields for the given post ID.
     *
     * @param int $post_id
     * @param array $meta_keys
     * @return array
     */
    function essb_get_post_meta_keys($post_id = '', $meta_keys = array()) {
        return ESSB_Post_Meta::get_post_meta_keys($post_id, $meta_keys);
    }
}

if (!function_exists('essb_get_post_meta_matching_keys')) {
    /**
     * Retrieves a post meta fields for the given post ID.
     *
     * @param int $post_id
     * @param string $meta_prefix
     * @return array
     */
    function essb_get_post_meta_matching_keys($post_id = '', $meta_prefix = '') {
        return ESSB_Post_Meta::get_post_meta_matching_keys($post_id, $meta_prefix);
    }
}

if (!function_exists('essb_update_post_meta')) {
    /**
     * Updates a post meta field based on the given post ID.
     * 
     * @param int $post_id
     * @param string $meta_key
     * @param mixed $meta_value
     */
    function essb_update_post_meta($post_id = '', $meta_key = '', $meta_value = '') {
        return ESSB_Post_Meta::save_post_meta($post_id, $meta_key, $meta_value);
    }
}

if (!function_exists('essb_delete_post_meta')) {
    /**
     * Deletes a post meta field for the given post ID.
     * 
     * @param int $post_id
     * @param string $meta_key
     */
    function essb_delete_post_meta($post_id = '', $meta_key = '') {
        return ESSB_Post_Meta::delete_post_meta($post_id, $meta_key);
    }
}

if (!function_exists('essb_delete_post_meta_by_key')) {
    /**
     * Deletes everything from post meta matching the given meta key.
     * 
     * @param string $meta_key
     */
    function essb_delete_post_meta_by_key($meta_key = '', $post_id = '') {
        return ESSB_Post_Meta::delete_post_meta_by_key($meta_key, $post_id);
    }
}

if (!function_exists('essb_delete_post_meta_by_matching_keys')) {
    /**
     * Deletes everything from post meta matching the given meta key.
     *
     * @param string $meta_key
     */
    function essb_delete_post_meta_by_matching_keys($meta_key = '', $post_id = '') {
        return ESSB_Post_Meta::delete_post_meta_by_matching_keys($meta_key, $post_id);
    }
}

if (!function_exists('essb_read_post_meta')) {
    /**
     * Read everything from post meta matching the given post ID.
     * 
     * @param int $post_id
     * @return array
     */
    function essb_read_post_meta($post_id = '') {
        return ESSB_Post_Meta::read_post_meta($post_id);
    }
}