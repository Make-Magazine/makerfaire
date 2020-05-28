<?php

/**
 * Runtime cache of plugin
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 */
class ESSB_Runtime_Cache {
    
    /**
     * @var array
     */
    private static $internal_cache = array();
    
    /**
     * @var array
     */
    private static $post_details = array();
    
    /**
     * Set cache value
     * 
     * @param string $key
     * @param string|mixed $value
     */
    public static function set ($key = '', $value = '') {
        self::$internal_cache[$key] = $value;
    }
    
    /**
     * Get cache value
     * 
     * @param string $key
     * @return string|mixed
     */
    public static function get ($key = '') {
        return isset(self::$internal_cache[$key]) ? self::$internal_cache[$key] : '';
     }
     
     /**
      * Remove cached key
      * 
      * @param string $key
      */
     public static function delete($key = '') {
         if (isset(self::$internal_cache[$key])) {
             unset (self::$internal_cache[$key]);
         }
     }
     
     /**
      * Generate and share single post share details.
      * 
      * @param unknown $post_id
      * @return mixed ESSB_Single_Post_Information
      */
     public static function get_post_sharing_data($post_id = null) {
         if (!isset(self::$post_details[$post_id])) {
             self::$post_details[$post_id] = new ESSB_Single_Post_Information($post_id);
         }
         
         return self::$post_details[$post_id];
     }
     
     /**
      * Check if cache element is set
      * 
      * @param string $key
      * @return boolean
      */
     public static function is ($key = '') {
         return isset(self::$internal_cache[$key]);
     }
     
     /**
      * Check if the cache element is set and has value true
      * 
      * @param string $key
      * @return unknown
      */
     public static function running($key = '') {
         return isset(self::$internal_cache[$key]) && self::$internal_cache[$key] == true;
     }
}