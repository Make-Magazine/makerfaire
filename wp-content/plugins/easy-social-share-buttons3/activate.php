<?php
if (!function_exists('essb_active_oninstall')) {
	/**
	 * The core plugin activate function. It is designed to activate default plugin options
	 * and if needed to activate the first time widget wizard.
	 * 
	 * @package EasySocialShareButtons
	 * @since 6.0
	 * @author appscreo
	 */
	function essb_active_oninstall() {
		$mail_salt_check = get_option(ESSB3_MAIL_SALT);
		if (!$mail_salt_check || empty($mail_salt_check)) {
			$new_salt = mt_rand();
			update_option(ESSB3_MAIL_SALT, $new_salt);
		}
		
		$exist_settings = get_option(ESSB3_OPTIONS_NAME);
		if (!$exist_settings) {
			if (!function_exists('essb_generate_default_settings')) {
				include_once (ESSB3_PLUGIN_ROOT . 'lib/core/options/default-options.php');
			}
			$options_base = essb_generate_default_settings();
			if ($options_base) {
				update_option(ESSB3_OPTIONS_NAME, $options_base);
			}
			update_option(ESSB3_FIRST_TIME_NAME, 'true');
		}
		// clear stored add-ons on activation of plugin
		delete_option('essb3_addons');
	}
}

if (!function_exists('essb_active_install_or_update')) {
    
    /**
     * Install or update custom database tables used by the plugin
     *
     * @package EasySocialShareButtons
     * @since 6.0
     * @author appscreo
     */
    function essb_active_install_or_update() {
        global $wpdb;
        
        if(!function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        if(is_network_admin() && is_plugin_active_for_network('easy-social-share-buttons3/easy-social-share-buttons3.php')) {
            $sites = array_map('get_object_vars', get_sites(array('deleted' => 0)));
            if(is_array($sites) && $sites !== array()) {               
                foreach($sites as $site) {                    
                    //insert/update custom table for blog
                    $blog_prefix = $wpdb->get_blog_prefix($site['blog_id']);
                    ESSB_Post_Meta::install($blog_prefix);
                    
                    if (!class_exists('ESSB_Plugin_Upgrade_Version')) {
                        include_once (ESSB3_PLUGIN_ROOT . 'lib/classes/utilities/class-plugin-upgrade.php');
                    }
                    
                    ESSB_Plugin_Upgrade_Version::init();
                }
            }            
        } 
        else {
            ESSB_Post_Meta::install();
            
            if (!class_exists('ESSB_Plugin_Upgrade_Version')) {
                include_once (ESSB3_PLUGIN_ROOT . 'lib/classes/utilities/class-plugin-upgrade.php');
            }
            
            ESSB_Plugin_Upgrade_Version::init();
        }
    }
}