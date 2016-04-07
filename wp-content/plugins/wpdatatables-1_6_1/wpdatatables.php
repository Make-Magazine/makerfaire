<?php
/**
 * @package wpDataTables
 * @version 1.6.1
 */
/*
Plugin Name: wpDataTables
Plugin URI: http://wpdatatables.com
Description: Add interactive tables easily from any input source
Version: 1.6.1
Author: touchmesoft
Author URI: http://touchmesoft.net
Text Domain: wpdatatables
Domain Path: /languages
*/
?>
<?php

    /******************************
     * Includes and configuration *
     ******************************/

    define('WDT_ROOT_PATH', plugin_dir_path(__FILE__)); // full path to the wpDataTables root directory
    define('WDT_ROOT_URL', plugin_dir_url(__FILE__)); // URL of wpDataTables plugin

    // Config file
    require_once(WDT_ROOT_PATH.'/config/config.inc.php');
    
    // AJAX actions handlers
    require_once(WDT_ROOT_PATH.'controllers/wdt_ajax_actions.php');
    
    // Plugin functions
    require_once(WDT_ROOT_PATH.'controllers/wdt_functions.php');
    
    function wpdatatables_load(){
        require_once(WDT_ROOT_PATH.'source/class.tpl.php');
        if(is_admin()){
            // Admin panel controller
            require_once(WDT_ROOT_PATH.'controllers/wdt_admin.php');
            // Admin panel AJAX actions
            require_once(WDT_ROOT_PATH.'controllers/wdt_admin_ajax_actions.php');
            // Table constructor
            require_once(WDT_ROOT_PATH.'source/class.constructor.php');
        }
        require_once(WDT_ROOT_PATH.'source/class.wdttools.php');
        require_once(WDT_ROOT_PATH.'source/class.wdtexception.php');
        require_once(WDT_ROOT_PATH.'source/class.sql.php');
        require_once(WDT_ROOT_PATH.'source/class.wpdatatable.php');
        require_once(WDT_ROOT_PATH.'source/class.wpdatacolumn.php');
        require_once(WDT_ROOT_PATH.'source/class.filterwidget.php');
        require_once(WDT_ROOT_PATH.'source/class.wdtbrowsetable.php');
        require_once(WDT_ROOT_PATH.'source/class.wdtbrowsechartstable.php');
        require_once(WDT_ROOT_PATH.'source/class.wpdatachart.php');
        
        add_action( 'plugins_loaded', 'wpdatatables_load_textdomain' );
    }

    // Globals for the shortcode variables
    $wdt_var1 = '';
    $wdt_var2 = '';
    $wdt_var3 = '';
	  
    /*******************
     * Filtering widget *
     *******************/
    function wdt_register_widget(){
        register_widget('wdtFilterWidget');
    }
	  
    /********
     * Hooks *
     ********/
    register_activation_hook(__FILE__, 'wpdatatables_activation');
    register_deactivation_hook(__FILE__, 'wpdatatables_deactivation');	
    register_uninstall_hook(__FILE__, 'wpdatatables_uninstall');	

    add_shortcode( 'wpdatatable', 'wpdatatable_shortcode_handler' );
    add_shortcode( 'wpdatachart', 'wpdatachart_shortcode_handler' );

    // Widget
    add_action( 'widgets_init', 'wdt_register_widget' );	

    wpdatatables_load();

?>