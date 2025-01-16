<?php
/**
 * Plugin Name: Make: Elementor Widgets
 * Description: This plugin adds some common Make: dashboard widgets to Elementor
 * Version:     1.3.2
 * Author:      Make: Developers
 * Text Domain: elementor-make-widgets
 * Elementor tested up to: 3.16.0
 * Elementor Pro tested up to: 3.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function elementor_make_widgets() {

	// Load plugin file
	require_once( __DIR__ . '/includes/plugin.php' );

	// Run the plugin
	\Elementor_Make_Widgets\Plugin::instance();

}
add_action( 'plugins_loaded', 'elementor_make_widgets' );