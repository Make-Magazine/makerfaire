<?php
/**
 * Plugin Name: GravityView Inline Edit
 * Plugin URI:  https://gravityview.co/extensions/inline-edit/
 * Description: Edit your fields inline in Gravity Forms and GravityView.
 * Version:     1.3.1
 * Author:      GravityView
 * Author URI:  https://gravityview.co
 * Text Domain: gravityview-inline-edit
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 */

/**
 * Version number of the plugin
 *
 * @since 1.0
 */
define( 'GRAVITYVIEW_INLINE_VERSION', '1.3.1' );

/** @define "GRAVITYVIEW_INLINE_DIR" "./" The absolute path to the plugin directory */
define( 'GRAVITYVIEW_INLINE_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The URL to this file, with trailing slash
 *
 * @since 1.0
 */
define( 'GRAVITYVIEW_INLINE_URL', plugin_dir_url( __FILE__ ) );

/**
 * The path to this file
 *
 * @since 1.0
 */
define( 'GRAVITYVIEW_INLINE_FILE', __FILE__ );

/**
 * Load Inline Edit plugin. Wrapper function to make sure GravityView_Extension has loaded.
 *
 * @since 1.0
 *
 * @return void
 */
function gravityview_inline_edit_load() {

	if ( ! class_exists( 'GravityView_Extension' ) ) {
		include_once GRAVITYVIEW_INLINE_DIR . 'lib/class-gravityview-extension.php';
	}

	require_once GRAVITYVIEW_INLINE_DIR . 'class-gravityview-inline-edit.php';
	require_once( GRAVITYVIEW_INLINE_DIR . 'includes/class-gravityview-inline-edit-settings.php' );

	// Won't be loaded if `GFForms` doesn't exist
	if( class_exists('GravityView_Inline_Edit_GFAddon') ) {
		GravityView_Inline_Edit::get_instance( GRAVITYVIEW_INLINE_VERSION, GravityView_Inline_Edit_GFAddon::get_instance() );
	}
}

add_action( 'plugins_loaded', 'gravityview_inline_edit_load', 20 );