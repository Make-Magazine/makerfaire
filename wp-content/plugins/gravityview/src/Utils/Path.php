<?php
/**
 * Centralized path helper for resolving internal file paths.
 *
 * Normalizes template, partial, and include paths so that code
 * does not need to hardcode 'src/' or other base directory prefixes.
 *
 * @since 3.0.0
 *
 * @package GravityKit\GravityView\Utils
 */

namespace GravityKit\GravityView\Utils;

final class Path {

	/**
	 * Base directory for PSR-4 source files, relative to the plugin root.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	const SRC_BASE = 'src/';

	/**
	 * Resolve a file path relative to the `src/` directory.
	 *
	 * Usage:
	 *   Path::file( 'Admin/Metaboxes/views/data-source.php' )
	 *   // returns GRAVITYVIEW_DIR . 'src/Admin/Metaboxes/views/data-source.php'
	 *
	 * @since 3.0.0
	 *
	 * @param string $relative_path Path relative to `src/`, e.g. 'Admin/Metaboxes/views/foo.php'.
	 *
	 * @return string Absolute file path.
	 */
	public static function file( string $relative_path ): string {
		return wp_normalize_path( GRAVITYVIEW_DIR . self::SRC_BASE . ltrim( $relative_path, '/' ) );
	}

	/**
	 * Resolve a directory path relative to the `src/` directory.
	 *
	 * Ensures the returned path ends with a trailing slash.
	 *
	 * Usage:
	 *   Path::dir( 'Admin/Metaboxes/views' )
	 *   // returns GRAVITYVIEW_DIR . 'src/Admin/Metaboxes/views/'
	 *
	 * @since 3.0.0
	 *
	 * @param string $relative_path Directory path relative to `src/`, e.g. 'Extension/EditEntry/views'.
	 *
	 * @return string Absolute directory path with trailing slash.
	 */
	public static function dir( string $relative_path ): string {
		$relative_path = rtrim( ltrim( $relative_path, '/' ), '/' );

		if ( '' === $relative_path ) {
			return wp_normalize_path( GRAVITYVIEW_DIR . self::SRC_BASE );
		}

		return wp_normalize_path( GRAVITYVIEW_DIR . self::SRC_BASE . $relative_path . '/' );
	}

	/**
	 * Resolve a path relative to the plugin root directory.
	 *
	 * Useful for referencing files outside `src/`, such as assets or legacy files.
	 *
	 * Usage:
	 *   Path::root( 'assets/extensions/entry-notes/style.css' )
	 *   // returns GRAVITYVIEW_DIR . 'assets/extensions/entry-notes/style.css'
	 *
	 * @since 3.0.0
	 *
	 * @param string $relative_path Path relative to the plugin root.
	 *
	 * @return string Absolute file path.
	 */
	public static function root( string $relative_path ): string {
		return wp_normalize_path( GRAVITYVIEW_DIR . ltrim( $relative_path, '/' ) );
	}
}
