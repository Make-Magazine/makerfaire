<?php

/**
 * Create a relation map for dynamic loaded features inside Easy Social Share Buttons for WordPress
 * 
 * @author appscreo
 * @since 7.1
 * @package EasySocialShareButtons
 */


/**
 * Define core plugin paths for easier usage
 */
define ( 'ESSB3_CORE_PATH', ESSB3_LIB_PATH . 'core/');
define ( 'ESSB3_CLASS_PATH', ESSB3_LIB_PATH . 'classes/');
define ( 'ESSB3_MODULES_PATH', ESSB3_LIB_PATH . 'modules/');
define ( 'ESSB3_INTEGRATIONS_PATH', ESSB3_CORE_PATH . 'integrations/');

define ( 'ESSB3_ASSETS_URL', ESSB3_PLUGIN_URL . '/assets/');


/**
 * Get feature related source file
 * 
 * @since 7.1
 * @param string $feature
 * @return NULL|string[]
 */
function essb_helper_source_map ($feature = '') {
    $map = array(
        'short-url' => array('function' => 'essb_apply_shorturl', 'source' => ESSB3_HELPERS_PATH . 'helpers-short-url.php'),
        'litemode-helper' => array('class' => 'ESSB_LightMode_Helper', 'source' => ESSB3_CORE_PATH . 'classes/class-lightmode-helper.php'),
        // integrations with additional plugins
        'integration-affiliatewp' => array('function' => 'essb_generate_affiliatewp_referral_link', 'source' => ESSB3_INTEGRATIONS_PATH . 'affiliatewp.php')
    );
    
    return isset($map[$feature]) ? $map[$feature] : null;
}

/**
 * Conditional loading of code file
 * 
 * @param string $feature
 */
function essb_helper_maybe_load_feature($feature = '') {
    $map = essb_helper_source_map($feature);
    
    if (isset($map)) {
        // Loading code if function don't exists   
        if (isset($map['function']) && !function_exists($map['function'])) {
            include_once $map['source'];
        }
        
        // Loading code if class don't exists
        if (isset($map['class']) && !class_exists($map['class'])) {
            include_once $map['source'];
        }
    }
}