<?php
/**
 * Plugin Name: GravityEdit
 * Plugin URI:  https://www.gravitykit.com/extensions/gravityview-inline-edit/
 * Description: Edit your fields inline in Gravity Forms and GravityView.
 * Version:     2.0
 * Author:      GravityKit
 * Author URI:  https://www.gravitykit.com/
 * Text Domain: gk-gravityedit
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once __DIR__ . '/vendor_prefixed/gravitykit/foundation/src/preflight_check.php';

if ( ! GravityKit\GravityEdit\Foundation\should_load( __FILE__ ) ) {
	return;
}

/**
 * Version number of the plugin
 *
 * @since 1.0
 */
define( 'GRAVITYEDIT_VERSION', '2.0' );

/** @define "GRAVITYEDIT_DIR" "./" The absolute path to the plugin directory */
define( 'GRAVITYEDIT_DIR', plugin_dir_path( __FILE__ ) );

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
define( 'GRAVITYEDIT_FILE', __FILE__ );

require_once  GRAVITYEDIT_DIR . 'vendor/autoload.php';
require_once  GRAVITYEDIT_DIR . 'vendor_prefixed/autoload.php';

GravityKit\GravityEdit\Foundation\Core::register( GRAVITYEDIT_FILE );

/**
 * Load GravityEdit. Wrapper function to make sure GravityView_Extension has loaded.
 *
 * @since 1.0
 *
 * @return void
 */
function gravityedit_load() {
	require_once GRAVITYEDIT_DIR . 'class-gravityview-inline-edit.php';
	require_once GRAVITYEDIT_DIR . 'includes/class-gravityview-inline-edit-settings.php';

	// Won't be loaded if `GFForms` doesn't exist
	if ( class_exists( 'GravityView_Inline_Edit_GFAddon' ) ) {
		GravityView_Inline_Edit::get_instance( GRAVITYEDIT_VERSION, GravityView_Inline_Edit_GFAddon::get_instance() );
	}
}

add_action( 'plugins_loaded', 'gravityedit_load', 20 );
