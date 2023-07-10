<?php
/**
 * Contains all module with related style that needs to be loaded
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 */
class ESSB_Module_Assets {
    
    /**
     * Minified CSS
     * @var boolean
     */
    private static $optimized_css = false;
    
    /**
     * Minified Javascript
     * @var boolean
     */
    private static $optimized_js = false;
    
    /**
     * Default modules folder
     * @var string
     */
    private static $module_base_folder = ESSB3_PLUGIN_URL . '/assets/modules/';
    
    /**
     * List of assets registered to load
     * @var array
     */
    private static $assets_to_load = array();
    
    private static $loaded_assets = array();
    
    /**
     * Return when optimized the minified extension of the file
     * @return string
     */
    public static function is_optimized($type = '') {
        
        if ($type == 'js') {
            return self::$optimized_js ? '.min' : '';
        }
        else {
            return self::$optimized_css ? '.min' : '';
        }
    }
    
    /**
     * Check if a module is disabled to run on a specific post type
     * @param string $module
     * @return boolean
     */
    private static function allowed($module = '') {
        $can_run = true;
        
        /**
         * Post Type check
         */
        $module_types = essb_option_value($module . '_post_types');
        if (!empty($module_types) && is_array($module_types)) {
            $active_type = get_post_type();
            
            if (is_archive() || is_front_page() || is_search() || is_tag() || is_post_type_archive() || is_home()) {
                $active_type = 'all_lists';
            }
            
            if (!empty($active_type) && !in_array($active_type, $module_types)) {
                $can_run = false;
            }
        }
        
        return $can_run;
    }
    
    /**
     * Include resources to load in the queue
     * @param string $file
     * @param string $type
     */
    private static function register_resource($module_id = '', $file = '', $type = '') {
        self::$assets_to_load[$module_id] = array('file' => $file, 'type' => $type);        
    }
    
    public static function get_modules_base_folder() {
        return self::$module_base_folder;
    }
    
    public static function is_registered($module_id = '') {
        return isset(self::$loaded_assets[$module_id]) ? true : false;
    }
    
    /**
     * Check and register required for Pinterest Pro static assets
     */
    public static function register_pinterest_pro() {
        /**
         * Validate if module can run
         */
        if (essb_is_module_deactivated_on('pinpro') || 
            essb_option_bool_value('deactivate_module_pinterestpro') || 
            !self::allowed('pinpro')) {
            return;
        }  
        
        
        add_action('wp', function() {
            if (ESSB_Plugin_Loader::is_module_homepage_deactivate('pinpro')) {
                essb_resource_builder()->remove_static_js_resource('pinterest-pro-js');
            }
        });
        
        self::register_resource('pinterest-pro-js', self::$module_base_folder . 'pinterest-pro' . self::is_optimized('js') . '.js', 'js');
    }
    
    public static function register_subscribe_forms() {
        /**
         * Validate if module can run
         */
        if (essb_is_module_deactivated_on('subscribe_forms') ||
            essb_option_bool_value('deactivate_module_subscribe') ||
            !self::allowed('subscribe_forms')) {
                return;
        }    

        self::register_resource('subscribe-forms-js', self::$module_base_folder . 'subscribe-forms' . self::is_optimized('js') . '.js', 'js');
        self::register_resource('subscribe-forms-css', self::$module_base_folder . 'subscribe-forms' . self::is_optimized('css') . '.css', 'css');
    }
    
    public static function register_after_share_actions() {
        self::register_resource('after-share-actions-css', self::$module_base_folder . 'after-share-actions' . self::is_optimized('css') . '.css', 'css');
    }
    
    public static function regsiter_click2chat() {
        self::register_resource('click2chat-css', self::$module_base_folder . 'click-to-chat' . self::is_optimized('css') . '.css', 'css');        
        self::register_resource('click2chat-js', self::$module_base_folder . 'click-to-chat' . self::is_optimized('js') . '.js', 'js');
    }    
    
    public static function register_click2tweet() {
        /**
         * Module is deactivated
         */
        if (essb_option_bool_value('deactivate_ctt')) {
            return;
        }
        
        self::register_resource('click2tweet-css', self::$module_base_folder . 'click-to-tweet' . self::is_optimized('css') . '.css', 'css');        
    }
    
    /**
     * Register and load all module static assets
     */
    public static function register_and_load() {
        self::$optimized_css = essb_option_bool_value('use_minified_css');
        self::$optimized_js = essb_option_bool_value('use_minified_js');
        
        self::register_pinterest_pro();        
        self::register_subscribe_forms();
        self::register_click2tweet();
        
        if (function_exists('essb_click2chat_can_run')) {
            if (essb_click2chat_can_run()) {
                self::regsiter_click2chat();
            }
        }
        
        /**
         * Loading registered assets
         */       
        foreach (self::$assets_to_load as $module_id => $resource) {
            $loaded_assets[$module_id] = true;
            if ($resource['type'] == 'css') {
                essb_resource_builder()->add_static_resource($resource['file'], $module_id, 'css');
            }
            else if ($resource['type'] == 'js') {
                essb_resource_builder()->add_static_resource($resource['file'], $module_id, 'js');                
            }
        }
    }
    
    public static function load_css_resource($module_id = '', $path = '', $type = '') {
        essb_resource_builder()->enqueue_style_single_css($module_id, $path, ESSB3_VERSION);
    }
    
    public static function load_js_resource($module_id = '', $path = '', $type = '') {
        wp_enqueue_script ( $module_id, $path, array ( 'jquery' ), ESSB3_VERSION, true );
    }
    
    /**
     * Register and load all module assets
     */
    public static function load() {
        add_action('init', array(__CLASS__, 'register_and_load'));
    }
}