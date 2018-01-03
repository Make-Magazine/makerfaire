<?php
/*
* Plugin Name: GravityView - Use Alternate DataTables Sources
* Description: Use CDNJS.com sources for the DataTables library
* Version: 1.0
* Author: Katz Web Services, Inc.
* Author URI: https://gravityview.co
*/

class Replace_GV_DataTables_Scripts {
public static function init() {
new Replace_GV_DataTables_Scripts;
}
function __construct() {
//add_filter('gravityview_datatables_script_src', array( $this, 'datatables_js') );
//add_filter('gravityview_datatables_style_src', array( $this, 'datatables_css') );
add_filter('gravityview_dt_tabletools_script_src', array( $this, 'tabletools_js' ) );
add_filter('gravityview_dt_tabletools_style_src', array( $this, 'tabletools_css' ) );
add_filter( 'gravityview_datatables_js_options', array( $this, 'add_config' ), 10, 3 );

}

function datatables_js() {
return '//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.1/js/jquery.dataTables.min.js';
}
function datatables_css() {
return '//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.1/css/jquery.dataTables.min.css';
}
function tabletools_js() {
return '//cdn.datatables.net/tabletools/2.2.4/js/dataTables.tableTools.min.js';
}
function tabletools_css() {
return '//cdn.datatables.net/tabletools/2.2.4/css/dataTables.tableTools.css';
}

/**
 * Add Javascript specific config data based on admin settings
 */

function add_config( $dt_config, $view_id, $post  ) {

	//$dt_config['dom'] = 'ptp';
 	return $dt_config;
}
}

//Replace_GV_DataTables_Scripts::init();
add_action( 'init', array('Replace_GV_DataTables_Scripts', 'init' ) );




