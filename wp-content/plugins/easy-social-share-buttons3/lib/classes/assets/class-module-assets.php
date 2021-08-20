<?php
/**
 * Contains all module with related style that needs to be loaded
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 */
class ESSB_Module_Assets {
    
    /**
     * Default modules folder
     * @var string
     */
    private static $module_base_folder = ESSB3_PLUGIN_URL . '/assets/modules/';
    
    /**
     * Currently running modules
     * @var array
     */
    public static $active_modules = array();
    
    /**
     * Installed and available modules
     * @var array
     */
    public static $available_modules = array();
    
    /**
     * Load all available modules to initialize the responsible styles
     */
    public static function load_all() {
        self::$available_modules = array();
        
        self::$available_modules['sharing-core'] = array(
            'description' => 'Social Sharing Base Styles',
            'css' => self::$module_base_folder.'css/essb-sharing-base',
            'js' => self::$module_base_folder.'js/essb-sharing-base'
        );
    }
}