<?php
/**
 * Plugin Name:         GravityView - DIY Layout
 * Plugin URI:          https://www.gravitykit.com/products/diy-layout/
 * Description:         Designers & developers: build your own GravityView layouts...styles not included!
 * Version:             2.5.1
 * Author:              GravityView
 * Author URI:          https://www.gravitykit.com
 * Text Domain:         gravityview-diy
 * License:             GPLv2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

define( 'GRAVITYVIEW_DIY_VERSION', '2.5.1' );

add_action( 'plugins_loaded', 'gv_extension_diy_load' );

/**
 * A simple loader that works with old PHP versions.
 *
 * @return void
 */
function gv_extension_diy_load() {
	if ( ! class_exists( '\GV\Extension' ) ) {
		add_action( 'admin_notices', 'gv_extension_diy_noload' );

		return;
	}

	if ( ! class_exists( '\GV\DIY' ) ) {
		require dirname( __FILE__ ) . '/class-gravityview-diy-layout-extension.php';
		require dirname( __FILE__ ) . '/class-gv-template-view-diy.php';
		require dirname( __FILE__ ) . '/class-gv-template-entry-diy.php';
		require dirname( __FILE__ ) . '/class-gv-edit-entry-diy.php';
	}
}

/**
 * Outputs a loader warning notice.
 *
 * @return void
 */
function gv_extension_diy_noload() {
	echo '<div id="message" class="error">';
	printf( esc_html_x( '%s was not loaded: GravityView %s was not found!', 'First placeholder: Name of the Extension. Second: Version of GravityView not found', 'gravityview-diy' ), 'DIY Layout', '2.0' );
	echo '</div>';
}


// Register the extension with Foundation, which will enable translations and other features.
add_action( 'gravityview/loaded', function () {
	if ( ! class_exists( 'GravityKit\GravityView\Foundation\Core' ) ) {
		return;
	}

	GravityKit\GravityView\Foundation\Core::register( __FILE__ );
} );

