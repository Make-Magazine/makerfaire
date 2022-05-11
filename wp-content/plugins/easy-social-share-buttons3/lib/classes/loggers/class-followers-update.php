<?php
/**
 * Log request and response during the followers counter update process
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 8.2
 */
class ESSB_Logger_Followers_Update {
    const LOG_NAME = 'essb_log_followers';
    const MAX_LOG_ENTRIES = 99;    
    
    public static function log($network, $request, $response) {
        $log = array(
            'date' => current_time('mysql'),
            'network' => $network,
            'response' => $response,
            'request' => $request,
        );
        
        $key = uniqid();        
        self::save_log($key, $log);
    }
    
    public static function save_log( $key, $item ) {
        $log = self::maybe_truncate_log();        
        $log[$key] = $item;                
        update_option( self::LOG_NAME, $log, 'no' );
    }
    
    public static function clear() {
        delete_option( self::LOG_NAME );
    }
    
    private static function maybe_truncate_log() {
        /** @var Log_Item[] $log */
        $log = self::get_log();
        
        if ( self::MAX_LOG_ENTRIES < count( $log ) ) {
            $log = array_slice( $log, -self::MAX_LOG_ENTRIES );
        }
        
        return $log;
    }
    
    public static function get_log() {
        // Clear cache.
        wp_cache_delete( self::LOG_NAME, 'options' );
        
        $log = get_option( self::LOG_NAME, [] );
        
        // In case the DB log is corrupted.
        if ( ! is_array( $log ) ) {
            $log = [];
        }
        
        return $log;
    }
}