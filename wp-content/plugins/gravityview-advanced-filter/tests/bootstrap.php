<?php
ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

$_gv_plugin_dir = getenv( 'GV_PLUGIN_DIR' ) ? : '/tmp/gravityview';
$_gv_advanced_filters_dir = __DIR__;

// Load the GV testing enviornment.
require_once $_gv_plugin_dir . '/tests/bootstrap.php';
require_once $_gv_advanced_filters_dir . '/../advanced-filter.php';
gv_extension_advanced_filtering_load();
