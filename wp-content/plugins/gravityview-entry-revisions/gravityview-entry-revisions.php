<?php
/**
 * Plugin Name:         GravityRevisions
 * Plugin URI:          https://www.gravitykit.com/extensions/entry-revisions/
 * Description:         Track changes to Gravity Forms entries and forms and restore values from earlier versions.
 * Version:             1.4.0
 * Author:              GravityKit
 * Author URI:          https://www.gravitykit.com
 * Text Domain:         gk-gravityrevisions
 * License:             GPLv2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once __DIR__ . '/vendor_prefixed/gravitykit/foundation/src/preflight_check.php';

if ( ! GravityKit\GravityRevisions\Foundation\should_load( __FILE__ ) ) {
	return;
}

/**
 * The plugin version number
 *
 * @since 1.0
 */
define( 'GV_ENTRY_REVISIONS_VERSION', '1.4.0' );

/** @define "GV_ENTRY_REVISIONS_DIR" "./" The absolute path to the plugin directory */
define( 'GV_ENTRY_REVISIONS_DIR', plugin_dir_path( __FILE__ ) );

/**
 * The path to this file
 *
 * @since 1.0
 */
define( 'GV_ENTRY_REVISIONS_FILE', __FILE__ );

require_once GV_ENTRY_REVISIONS_DIR . 'vendor/autoload.php';
require_once GV_ENTRY_REVISIONS_DIR . 'vendor_prefixed/autoload.php';

GravityKit\GravityRevisions\Foundation\Core::register( GV_ENTRY_REVISIONS_FILE );

/**
 * Load Inline Edit plugin. Wrapper function to make sure GravityView_Extension has loaded.
 *
 * @since 1.0
 *
 * @return void
 */
function gravityview_entry_revisions_load() {

	require_once GV_ENTRY_REVISIONS_DIR . 'class-gv-entry-revisions.php';
	require_once GV_ENTRY_REVISIONS_DIR . 'includes/class-gv-form-revisions.php';

	GV_Entry_Revisions::get_instance();

	// Won't be loaded if `GFForms` doesn't exist
	if( class_exists('GV_Entry_Revisions_Settings') ) {
		GV_Entry_Revisions_Settings::get_instance();
	}

	if ( class_exists( 'GV_Form_Revisions' ) ) {
		GV_Form_Revisions::get_instance();
	}
}

add_action( 'plugins_loaded', 'gravityview_entry_revisions_load', 20 );
