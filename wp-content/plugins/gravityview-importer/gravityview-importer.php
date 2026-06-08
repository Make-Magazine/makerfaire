<?php
/**
 * Plugin Name: GravityImport
 * Plugin URI:  https://www.gravitykit.com/extensions/gravity-forms-entry-importer/
 * Description: The best way to import entries into Gravity Forms. Proud to be a Gravity Forms Certified Add-On.
 * Version:     2.11.2
 * Author:      GravityKit
 * Author URI:  https://www.gravitykit.com
 * Text Domain: gk-gravityimport
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /languages
 */

use GravityKit\GravityImport\Core as ImportCore;
use GravityKit\GravityImport\Foundation\Core as FoundationCore;
use function GravityKit\GravityImport\Foundation\should_load;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once __DIR__ . '/vendor_prefixed/gravitykit/foundation/src/preflight_check.php';

if ( ! should_load( __FILE__ ) ) {
	return;
}

define( 'GV_IMPORT_ENTRIES_VERSION', '2.11.2' );

define( 'GV_IMPORT_ENTRIES_FILE', __FILE__ );

define( 'GV_IMPORT_ENTRIES_MIN_GF', '2.2' );

define( 'GV_IMPORT_ENTRIES_MIN_WP', '6.2' );

// Boot it up.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor_prefixed/autoload.php';

FoundationCore::register( GV_IMPORT_ENTRIES_FILE );

add_action( 'plugins_loaded', 'gv_import_entries_load', 1 );

/**
 * Main plugin loading function.
 *
 * @codeCoverageIgnore Tested during load
 *
 * @return void
 */
function gv_import_entries_load() {
	global $wp_version;

	// Require WordPress min version
	if ( version_compare( $wp_version, GV_IMPORT_ENTRIES_MIN_WP, '<' ) ) {
		add_action( 'admin_notices', 'gv_import_entries_noload_wp' );
		return;
	}

	// Require Gravity Forms min version
	if ( ! class_exists( 'GFForms') || version_compare( GFForms::$version, GV_IMPORT_ENTRIES_MIN_GF, '<' ) ) {
		add_action( 'admin_notices', 'gv_import_entries_noload_gravityforms' );
		return;
	}

	call_user_func( array( ImportCore::class, 'bootstrap' ) );
}

/**
 * Notice output in dashboard if WordPress is incompatible.
 *
 * @since 2.0.2
 *
 * @codeCoverageIgnore Just some output.
 *
 * @return void
 */
function gv_import_entries_noload_wp() {
	$message = wpautop( sprintf( esc_html__( 'The %s Extension requires WordPress Version %s or newer.', 'gk-gravityimport' ), 'GravityImport', GV_IMPORT_ENTRIES_MIN_WP ) );
	echo "<div class='error' style='padding: 1.25em 0 1.25em 1em;'>$message</div>";
}

/**
 * Notice output in dashboard if Gravity Forms is incompatible.
 *
 * @codeCoverageIgnore Just some output.
 *
 * @return void
 */
function gv_import_entries_noload_gravityforms() {
	$message = wpautop( sprintf( esc_html__( '%s requires Gravity Forms Version %s or higher.', 'gk-gravityimport' ), 'GravityImport', GV_IMPORT_ENTRIES_MIN_GF ) );
	echo "<div class='error' style='padding: 1.25em 0 1.25em 1em;'>$message</div>";
}
