<?php

/**
 * Classloader and holder. Prevent multiple instances of class loading.
 * 
 * @since 7.1
 * @author appscreo
 * @package EasySocialShareButtons
 */
class ESSB_Factory_Loader {
    
    /**
     * @var array
     */
    private static $factory = array();
    
    /**
     * @var array
     */
    private static $module_tags = array(
        'sso' => array('opengraph', 'twittercard')
    );
    
    /**
     * @param string $module
     * @param string $class_name
     */
    public static function activate($module = '', $class_name = '') {
        if (!empty($module) && !isset(self::$factory[$module])) {
            self::$factory[$module] = new $class_name;
        }
    }
    
    /**
     * @param string $module
     * @param string $class_name
     */
    public static function activate_instance($module = '', $class_name = '') {
        if (!empty($module) && !isset(self::$factory[$module]) && class_exists($class_name)) {
            self::$factory[$module] = $class_name::instance();
        }
    }
    
    /**
     * @param string $module
     */
    public static function deactivate($module = '') {
        if (isset(self::$factory[$module])) {
            unset (self::$factory[$module]);
        }
    }
    
    /**
     * @param string $module
     * @return NULL|mixed
     */
    public static function get($module = '') {
        return isset(self::$factory[$module]) ? self::$factory[$module] : null;
    }
    
    /**
     * @param string $module
     * @return boolean
     */
    public static function running($module = '') {
        $running = false;
        
        if (isset(self::$module_tags[$module])) {
            foreach (self::$module_tags[$module] as $tag) {
                if (isset(self::$factory[$tag])) {
                    $running = true;
                }
            }
        }
        else if (isset(self::$factory[$module])) {
            $running = true;
        }
        
        return $running;
    }
}