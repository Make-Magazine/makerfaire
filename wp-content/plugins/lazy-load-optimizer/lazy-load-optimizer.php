<?php

use LazyLoadOptimizer\LazyLoadOptimizerPlugin;

/**
 *
 * Plugin Name:       Lazy Load Optimizer
 * Plugin URI:        https://processby.com/lazy-load-wordpress/
 * Description:       Lazy loading images to speed up sites page load speed.
 * Version:           1.3.3
 * Author:            Processby
 * Author URI:        https://processby.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lazy-load-optimizer
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

call_user_func( function () {

	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

	$main = new LazyLoadOptimizerPlugin( __FILE__ );

	register_activation_hook( __FILE__, [ $main, 'activate' ] );

	register_deactivation_hook( __FILE__, [ $main, 'deactivate' ] );

	register_uninstall_hook( __FILE__, [ LazyLoadOptimizerPlugin::class, 'uninstall' ] );

	$main->run();
} );



