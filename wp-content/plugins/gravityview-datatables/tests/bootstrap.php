<?php

ob_start();

$path = getenv( 'GV_TESTS_BOOTSTRAP' ) ? : '../GravityView/tests/bootstrap.php';

if ( file_exists( $path ) ) {
    $GLOBALS['wp_tests_options'] = array(
        'active_plugins' => array(
        	'gravityforms/gravityforms.php',
        	'gravityview/gravityview.php',
        	'gravityview-datatables/datatables.php'
        )
    );

    require_once $path;
} else {
    exit( "Couldn't find wordpress-tests/bootstrap.php\n" );
}

/** Bootstrap GravityView. Bootstraps Gravity Forms inside. */
require_once dirname( __FILE__ ) . '/../../GravityView/gravityview.php';

/** Bootstrap the DataTables extension. */
require_once dirname( __FILE__ ) . '/../datatables.php';

gv_extension_datatables_load();
$datatables = new GV_Extension_DataTables();

$datatables->backend_actions();
$datatables->core_actions();
$datatables->register_templates();

ob_end_clean();
