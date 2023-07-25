<?php
/**
 * Plugin Name: GravityView - Multiple Forms
 * Plugin URI:  https://www.gravitykit.com/extensions/multiple-forms/
 * Description: Display values from multiple forms in a single View.
 * Version:     0.3.4
 * Author:      GravityKit
 * Author URI:  https://www.gravitykit.com
 * Text Domain: gravityview-multiple-forms
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 */
namespace GravityKit\MultipleForms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Please do not use these constants directly to test if the Extension is active.
 * They might give you a false positive, since the actual plugin in WordPress can be active without meeting all the
 * extensions requirements, leading these constants to be active but not all the functionality to be loaded, which
 * could cause unexpected errors.
 */
define( 'GV_MF_VERSION', '0.3.4' );

define( 'GV_MF_FILE', __FILE__ );

define( 'GV_MF_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Autoloading from Composer, no classes or objects are actually initialized at this point.
 */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Initializes the whole plugin, which will eventually set up the extension and properly initialize functionality.
 */
Plugin::instance();
