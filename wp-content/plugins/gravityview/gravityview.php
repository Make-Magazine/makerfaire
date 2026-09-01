<?php
/**
 * Plugin Name:         GravityView
 * Plugin URI:          https://www.gravitykit.com
 * Description:         The best, easiest way to display Gravity Forms entries on your website.
 * Version:             3.3.3
 * Requires PHP:        7.4.0
 * Author:              GravityKit
 * Author URI:          https://www.gravitykit.com
 * Text Domain:         gk-gravityview
 * License:             GPLv2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.html
 */

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require_once __DIR__ . '/vendor_prefixed/gravitykit/foundation/src/preflight_check.php';

if ( ! GravityKit\GravityView\Foundation\should_load( __FILE__ ) ) {
	return;
}

/** Constants */

/**
 * The plugin version.
 */
define( 'GV_PLUGIN_VERSION', '3.3.3' );

/**
 * Full path to the GravityView file
 *
 * @define "GRAVITYVIEW_FILE" "./gravityview.php"
 */
define( 'GRAVITYVIEW_FILE', __FILE__ );

/**
 * The URL to this file, with trailing slash
 */
define( 'GRAVITYVIEW_URL', plugin_dir_url( __FILE__ ) );


/** @define "GRAVITYVIEW_DIR" "./" The absolute path to the plugin directory, with trailing slash */
define( 'GRAVITYVIEW_DIR', plugin_dir_path( __FILE__ ) );

/**
 * GravityView requires at least this version of Gravity Forms to function properly.
 */
define( 'GV_MIN_GF_VERSION', '2.6.0' );

/**
 * GravityView will soon require at least this version of Gravity Forms to function properly.
 *
 * @since 1.19.4
 */
define( 'GV_FUTURE_MIN_GF_VERSION', '2.7.0' );

/**
 * GravityView requires at least this version of WordPress to function properly.
 *
 * @since 1.12
 */
define( 'GV_MIN_WP_VERSION', '4.7.0' );

/**
 * GravityView will soon require at least this version of WordPress to function properly.
 *
 * @since 2.9.3
 */
define( 'GV_FUTURE_MIN_WP_VERSION', '5.3' );

/**
 * GravityView will require this version of PHP soon. False if no future PHP version changes are planned.
 *
 * @since 1.19.2
 * @var string|false
 */
define( 'GV_FUTURE_MIN_PHP_VERSION', '8.0.0' );

/** Autoloaders. */
require_once GRAVITYVIEW_DIR . 'vendor/autoload.php';
require_once GRAVITYVIEW_DIR . 'vendor_prefixed/autoload.php';

/**
 * Ensure PSR-4 aliases are registered.
 *
 * Composer's `files` autoload may have already included the alias files,
 * but if loaded before the plugin defined constants (e.g., PHPUnit loads
 * vendor/autoload.php before the test bootstrap), the alias registrations
 * were skipped. Re-include here with `include` to ensure aliases are created.
 *
 * @since 3.0.0
 */
include GRAVITYVIEW_DIR . 'src/Aliases/gv-aliases.php';
include GRAVITYVIEW_DIR . 'src/Aliases/legacy-aliases.php';

/** Register with GravityKit Foundation. */
GravityKit\GravityView\Foundation\Core::register( GRAVITYVIEW_FILE );

/** Load mocks for deprecated GravityView_* global functions. */
require GRAVITYVIEW_DIR . 'src/GravityForms/QueryExtensions/_mocks.php';

/** Initialize the deprecated hook notices handler. */
\GravityKit\GravityView\Deprecation\DeprecatedHookNotices::init();

/** Bootstrap the core. */
\GV\Core::bootstrap();

/**
 * The main GravityView wrapper function.
 *
 * Exposes classes and functionality via the \GV\Core instance.
 *
 * @api
 * @since 2.0
 *
 * @return \GV\Core A global Core instance.
 */
function gravityview() {
	return \GV\Core::get();
}

/** Liftoff. */
add_action( 'plugins_loaded', 'gravityview', 1 );

/** PSR-4 bootstrap marker. */
if ( class_exists( 'GravityKit\GravityView\Core\Bootstrap' ) ) {
	\GravityKit\GravityView\Core\Bootstrap::is_loaded();
}

add_action(
	'plugins_loaded',
	function () {

		if ( class_exists( 'GravityView_Plugin', false ) ) {
			return;
		}

		/**
		 * GravityView_Plugin is only used by the legacy class-gravityview-extension.php that's shipped with extensions.
		 *
		 * @since 1.0
		 * @deprecated 3.0.0 Use GravityKit\GravityView\Core\Plugin instead.
		 *
		 * @TODO Remove once all extensions have been updated to use Foundation.
		 */
		final class GravityView_Plugin {
			const version = GV_PLUGIN_VERSION;
		}
	},
	5
);
