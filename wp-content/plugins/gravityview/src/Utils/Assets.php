<?php
/**
 * Centralized assets utility for resolving asset URLs and filesystem paths.
 *
 * Normalizes references to the top-level `assets/` directory so that code
 * does not need to scatter `plugins_url()` or `GRAVITYVIEW_DIR` concatenations.
 *
 * @since 3.0.0
 *
 * @package GravityKit\GravityView\Utils
 */

namespace GravityKit\GravityView\Utils;

defined( 'ABSPATH' ) || die();

class Assets {

	/**
	 * Base directory for assets, relative to the plugin root.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	const ASSETS_BASE = 'assets/';

	/**
	 * Resolve a URL for an asset relative to the `assets/` directory.
	 *
	 * Usage:
	 *   Assets::url( 'widgets/search-widget/style.css' )
	 *   // returns plugins_url( 'assets/widgets/search-widget/style.css', GRAVITYVIEW_FILE )
	 *
	 * @since 3.0.0
	 *
	 * @param string $relative_path Path relative to `assets/`, e.g. 'css/admin-views.css'.
	 *
	 * @return string The full URL to the asset.
	 */
	public static function url( $relative_path ) {
		return \plugins_url( self::ASSETS_BASE . ltrim( $relative_path, '/' ), GRAVITYVIEW_FILE );
	}

	/**
	 * Resolve a filesystem path for an asset relative to the `assets/` directory.
	 *
	 * Usage:
	 *   Assets::path( 'extensions/entry-notes/js/entry-notes.js' )
	 *   // returns GRAVITYVIEW_DIR . 'assets/extensions/entry-notes/js/entry-notes.js'
	 *
	 * @since 3.0.0
	 *
	 * @param string $relative_path Path relative to `assets/`, e.g. 'extensions/entry-notes/script.js'.
	 *
	 * @return string Absolute filesystem path to the asset.
	 */
	public static function path( $relative_path ) {
		return GRAVITYVIEW_DIR . self::ASSETS_BASE . ltrim( $relative_path, '/' );
	}

	/**
	 * Resolve a directory path for assets relative to the `assets/` directory.
	 *
	 * Ensures the returned path ends with a trailing slash.
	 *
	 * Usage:
	 *   Assets::dir( 'extensions/entry-notes/css' )
	 *   // returns GRAVITYVIEW_DIR . 'assets/extensions/entry-notes/css/'
	 *
	 * @since 3.0.0
	 *
	 * @param string $relative_path Directory path relative to `assets/`.
	 *
	 * @return string Absolute directory path with trailing slash.
	 */
	public static function dir( $relative_path ) {
		return GRAVITYVIEW_DIR . self::ASSETS_BASE . rtrim( ltrim( $relative_path, '/' ), '/' ) . '/';
	}

	/**
	 * Returns the `.min` filename suffix for production, or an empty string when `SCRIPT_DEBUG` is on.
	 *
	 * Usage:
	 *   Assets::url( 'js/admin-entries-list' . Assets::min() . '.js' )
	 *
	 * @since 3.0.0
	 *
	 * @return string `.min` in production, empty string when `SCRIPT_DEBUG` is true.
	 */
	public static function min() {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	}
}
