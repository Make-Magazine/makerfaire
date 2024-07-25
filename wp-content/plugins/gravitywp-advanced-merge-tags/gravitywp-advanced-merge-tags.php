<?php
/**
 * Plugin Name: GravityWP - Advanced Merge Tags
 * Plugin URI: https://gravitywp.com
 * Description: Add powerful Merge Tags and Merge Tag modifiers to Gravity Forms
 * Version: 1.6.1
 * Requires PHP: 7.0
 * Author: GravityWP
 * Author URI: https://gravitywp.com
 * License: GPL-3.0+
 * Text Domain: gravitywpadvancedmergetags
 * Domain Path: /languages
 *
 * ------------------------------------------------------------------------
 * Copyright 2019 GravityWP.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses.
 */

// Defines the current version of the GravityWP Advanced Merge Tags Add-On.
define( 'GWP_ADVANCED_MERGE_TAGS_VERSION', '1.6.1' );

// Defines the minimum version of Gravity Forms required to run GravityWP Advanced Uploads Add-On.
define( 'GWP_ADVANCED_MERGE_TAGS_MIN_GF_VERSION', '2.4' );

add_action( 'gform_loaded', array( 'GWP_Advanced_Merge_Tags_Bootstrap', 'load_addon' ) );

/**
 * Loads the GravityWP Advanced Merge Tags Add-On.
 *
 * Includes the main class and registers it with GFAddOn.
 *
 * @since 1.0
 */
class GWP_Advanced_Merge_Tags_Bootstrap {

	/**
	 * Loads the required files.
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @return void
	 */
	public static function load_addon() {

		if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
			return;
		}

		// Registers the class name with GFAddOn.
		GFAddOn::register( 'GravityWP\Advanced_Merge_Tags\GravityWP_Advanced_Merge_Tags' );

		// Autoloader.
		require_once __DIR__ . '/lib/autoload.php';

		// Requires the class file.
		require_once plugin_dir_path( __FILE__ ) . 'class-gravitywp-advanced-merge-tags.php';
		
		// GPPA compatibility.
		if ( class_exists( 'GP_Populate_Anything' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/compatibility/class-gravity-perks-populate-anything.php';
		}
		// GravityKit Advanced Filter.
		if ( class_exists( 'GravityView_Advanced_Filtering' ) || class_exists( '\GravityKit\AdvancedFilter\Core' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-gk-advanced-filter-merge-tag.php';
		}
	}
}
