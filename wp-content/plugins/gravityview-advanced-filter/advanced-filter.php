<?php
/**
 * Plugin Name:         GravityView - Advanced Filter Extension
 * Plugin URI:          https://www.gravitykit.com/extensions/advanced-filter/
 * Description:         Filter which entries are shown in a View based on their values.
 * Version:             3.0.5
 * Author:              GravityKit
 * Author URI:          https://www.gravitykit.com
 * Text Domain:         gravityview-advanced-filter
 * License:             GPLv3 or later
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.en.html
 */

use GravityKit\AdvancedFilter\Core as AdvancedFiltersCore;
use GV\Core as GravityViewCore;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';

const GRAVITYKIT_ADVANCED_FILTERING_VERSION  = '3.0.5';
const GRAVITYKIT_ADVANCED_FILTER_PLUGIN_FILE = __FILE__;

add_action( 'gravityview/loaded', function () {
	// Initialize the extension.
	$plugin = new AdvancedFiltersCore( GravityViewCore::get() );

	// Keep backward compatibility.
	class_alias( AdvancedFiltersCore::class, 'GravityView_Advanced_Filtering' );

	// Dispatch initialized event with the plugin.
	do_action( 'gk/advanced-filters/initialized', $plugin );

	// Register the extension with Foundation, which will enable translations and other features.
	if ( class_exists( 'GravityKit\GravityView\Foundation\Core' ) ) {
		GravityKit\GravityView\Foundation\Core::register( __FILE__ );
	}
} );
