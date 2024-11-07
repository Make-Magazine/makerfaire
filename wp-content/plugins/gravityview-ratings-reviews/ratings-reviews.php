<?php
/**
 * Plugin Name:     GravityView - Ratings & Reviews
 * Plugin URI:      https://www.gravitykit.com/extensions/ratings-reviews/
 * Description:     Adds support for rating and reviewing Gravity Forms entries in GravityView
 * Version:         2.3.2
 * Author:          GravityKit
 * Author URI:      https://www.gravitykit.com
 * Text Domain:     gravityview-ratings-reviews
 * License:         GPLv3 or later
 * License URI:     https://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined( 'ABSPATH' ) || exit;

add_action( 'plugins_loaded', function () {
	if ( did_action( 'gravityview/loaded' ) ) {
		return;
	}

	add_action( 'admin_notices', function () {
		$message = wpautop(
			strtr(
				esc_html_x( '[extension] requires [link][plugin][/link] to work. Please install and activate [plugin].', 'gravityview-az-filters' ),
				[
					'[extension]' => 'GravityView - Ratings & Reviews',
					'[plugin]'    => 'GravityView',
					'[link]'      => '<a href="https://www.gravitykit.com/products/gravityview/">',
					'[/link]'     => '</a>',
				]
			)
		);

		echo "<div class='error' style='padding: 1.25em 0 1.25em 1em;'>$message</div>";
	} );
} );

add_action( 'gravityview/loaded', function () {
	require_once __DIR__ . '/includes/class-loader.php';

	$GLOBALS['gv_ratings_reviews'] = new GravityView_Ratings_Reviews_Loader( __FILE__, '2.3.2' );

	// Register the extension with Foundation, which will enable translations and other features.
	if ( ! class_exists( 'GravityKit\GravityView\Foundation\Core' ) ) {
		return;
	}

	GravityKit\GravityView\Foundation\Core::register( __FILE__ );
} );
