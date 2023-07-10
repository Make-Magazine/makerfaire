<?php
/*
Plugin Name: GravityView - Maps
Plugin URI: https://www.gravitykit.com/extensions/maps/
Description: Display your Gravity Forms entries on a map.
Version: 2.2
Author: GravityKit
Author URI: https://www.gravitykit.com
Text Domain: gk-gravitymaps
*/

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor_prefixed/gravitykit/foundation/src/preflight_check.php';

if ( ! GravityKit\GravityMaps\Foundation\should_load( __FILE__ ) ) {
	return;
}

/** @since 1.7.7 */
define( 'GRAVITYVIEW_MAPS_VERSION', '2.2' );

/** @since 1.7.7 */
define( 'GRAVITYVIEW_MAPS_FILE', __FILE__ );

/** @since 1.7.8 */
define( 'GRAVITYVIEW_MAPS_MIN_GV_VERSION', '2.16' );

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor_prefixed/autoload.php';

GravityKit\GravityMaps\Foundation\Core::register( GRAVITYVIEW_MAPS_FILE );

function gravityview_extension_maps_loader() {
	$_add_admin_notice = function ( $notice ) {
		add_action( 'admin_notices', function () use ( $notice ) {
			echo "<div class='error' style='padding: 1.25em 0 1.25em 1em;'>{$notice}</div>";
		} );
	};

	if ( ! defined( 'GV_PLUGIN_VERSION' ) || version_compare( GV_PLUGIN_VERSION, GRAVITYVIEW_MAPS_MIN_GV_VERSION, '<' ) ) {
		$notice = strtr(
			esc_html_x( 'GravityView Maps requires [url]GravityView[/url] [version] or newer.', 'Placeholders inside [] are not to be translated.', 'gk-gravitymaps' ),
			[
				'[url]'     => '<a href="https://www.gravitykit.com/features/">',
				'[/url]'    => '</a>',
				'[version]' => GRAVITYVIEW_MAPS_MIN_GV_VERSION
			]
		);

		return $_add_admin_notice( $notice );
	}

	if ( ! class_exists( 'GFCommon' ) ) {
		$notice = strtr(
			esc_html_x( 'GravityView Maps requires [url]Gravity Forms[/url] to be active.', 'Placeholders inside [] are not to be translated.', 'gk-gravitymaps' ),
			[
				'[url]'  => '<a href="https://www.gravityforms.com/">',
				'[/url]' => '</a>',
			]
		);

		return $_add_admin_notice( $notice );
	}

	$GLOBALS['gravityview_maps'] = new GravityKit\GravityMaps\Loader( GRAVITYVIEW_MAPS_FILE, GRAVITYVIEW_MAPS_VERSION );
}

add_action( 'plugins_loaded', 'gravityview_extension_maps_loader' );
