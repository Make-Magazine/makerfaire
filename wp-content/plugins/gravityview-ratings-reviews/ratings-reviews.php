<?php
/**
 * Plugin Name: GravityView - Ratings & Reviews
 * Plugin URI: https://www.gravitykit.com/extensions/ratings-reviews/
 * Description: Adds support for rating and reviewing Gravity Forms entries in GravityView
 * Version: 2.2.1
 * Author: GravityKit
 * Author URI: https://www.gravitykit.com
 * Text Domain: gravityview-ratings-reviews
 * Domain Path: /languages/
 */

defined( 'ABSPATH' ) || exit;

function gv_ratings_reviews_loader() {
	if ( ! class_exists( 'GravityView_Extension' ) ) {
		if ( class_exists( 'GravityView_Plugin' ) && is_callable( array( 'GravityView_Plugin', 'include_extension_framework' ) ) ) {
			GravityView_Plugin::include_extension_framework();
		} else {
			// We prefer to use the one bundled with GravityView, but if it doesn't exist, go here.
			include_once plugin_dir_path( __FILE__ ) . 'lib/class-gravityview-extension.php';
		}
	}

	require_once dirname( __FILE__ ) . '/includes/class-loader.php';
	$GLOBALS['gv_ratings_reviews'] = new GravityView_Ratings_Reviews_Loader( __FILE__, '2.2.1' );
}

add_action( 'plugins_loaded', 'gv_ratings_reviews_loader' );
