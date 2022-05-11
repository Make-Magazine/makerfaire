<?php 

class ESSB_Plugin_Upgrade_Version {
    
    /**
     * Current version
     * @var string
     */
    private static $version = '';
    
    /**
     * Current version option
     * @var string
     */
    private static $version_option = 'essb_version';   
    
    /**
     * If an upgrade was made
     * @var boolean
     */
    private static $upgraded = false;
    
    /**
     * Start the upgrade
     */
    public static function init() {
        self::$version = get_option( self::$version_option );
        
        /**
         * Upgrade process begin
         */
        if ( version_compare( self::$version, '7.9.5', '<' ) ) {
            self::v795_upgrade();
        }
        
        
        if ( version_compare( self::$version, ESSB3_VERSION, '<>' ) ) {
            self::$upgraded = true;
        }
        
        // If upgrades have occurred
        if ( self::$upgraded ) {
            update_option( self::$version_option, ESSB3_VERSION, false );
        }
    }
    
    /**
     * Upgrade by versions
     */
    
    private static function v795_upgrade() {        
        $exist_settings = get_option(ESSB3_OPTIONS_NAME);
        
        if (!empty($exist_settings)) {
            $options_modified = false;
            
            /**
             * UTM tracking code upgrade
             */
            
            if (isset($exist_settings['activate_ga_campaign_tracking']) && !empty($exist_settings['activate_ga_campaign_tracking'])) {
                $exist_settings['activate_utm'] = 'true';
                
                $current_utm = array();
                parse_str($exist_settings['activate_ga_campaign_tracking'], $current_utm);
                
                if (isset($current_utm['utm_source'])) {
                    $exist_settings['activate_utm_source'] = $current_utm['utm_source'];
                }
                
                if (isset($current_utm['utm_medium'])) {
                    $exist_settings['activate_utm_medium'] = $current_utm['utm_medium'];
                }
                
                if (isset($current_utm['utm_campaign'])) {
                    $exist_settings['activate_utm_name'] = $current_utm['utm_campaign'];
                }
                
                $options_modified = true;
            }
            
            /**
             * Short URLs in the previous format
             */
            if (isset($exist_settings['shorturl_activate']) && $exist_settings['shorturl_activate'] == 'true') {
                $exist_settings['legacy_shorturl_cache'] = 'true';
            }
            
            /**
             * Save options if modified
             */
            if ($options_modified) {
                update_option(ESSB3_OPTIONS_NAME, $exist_settings);
            }
        }
        
        self::$upgraded = true;
    }
    
}