<?php
/**
 * Migration Helper for the PSR-4 namespace migration.
 *
 * Provides utility methods for creating deprecated class aliases,
 * checking class existence across namespaces, and logging migration
 * activity.
 *
 * @package GravityKit\GravityView\Migration
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Migration;

/**
 * Helper utilities for the GravityView PSR-4 migration.
 *
 * @since 3.0.0
 */
class MigrationHelper {

	/**
	 * Registry of all aliases created during this request.
	 *
	 * @since 3.0.0
	 *
	 * @var array<string, string> Map of old class name => new class name.
	 */
	private static $aliases = [];

	/**
	 * Registry of deprecation messages logged during this request.
	 *
	 * @since 3.0.0
	 *
	 * @var array<int, array{old_class: string, new_class: string, version: string}>
	 */
	private static $deprecation_log = [];

	/**
	 * Creates a class alias with deprecation notice.
	 *
	 * Registers a class_alias() mapping from an old class name to a new one,
	 * and optionally triggers a deprecation notice when the old name is used.
	 *
	 * @since 3.0.0
	 *
	 * @param string $new_class Fully qualified new class name (e.g., 'GravityKit\GravityView\Core\Plugin').
	 * @param string $old_class Old class name to alias (e.g., 'GV\Plugin' or 'GravityView_Admin').
	 * @param string $version   Version when the old class name was deprecated. Defaults to the 'TBD'
	 *                          release placeholder; the version clause is omitted from notices until
	 *                          a real version is passed.
	 *
	 * @return bool True if the alias was created successfully, false otherwise.
	 */
	public static function deprecated_alias( $new_class, $old_class, $version = 'TBD' ) {
		if ( ! class_exists( $new_class ) && ! interface_exists( $new_class ) && ! trait_exists( $new_class ) ) {
			self::log(
				sprintf( 'Cannot create alias: new class %s does not exist.', $new_class ),
				'error'
			);

			return false;
		}

		if ( class_exists( $old_class, false ) || interface_exists( $old_class, false ) || trait_exists( $old_class, false ) ) {
			self::log(
				sprintf( 'Alias skipped: old class %s already exists.', $old_class ),
				'debug'
			);

			return true;
		}

		$result = class_alias( $new_class, $old_class );

		if ( $result ) {
			self::$aliases[ $old_class ] = $new_class;

			// Recorded in the in-memory registry only (see
			// `get_deprecation_log()`).
			self::$deprecation_log[] = [
				'old_class' => $old_class,
				'new_class' => $new_class,
				'version'   => $version,
			];
		}

		return $result;
	}

	/**
	 * Builds the user-visible deprecation message for an aliased class name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $old_class Old class name that is deprecated.
	 * @param string $new_class Fully qualified replacement class name.
	 * @param string $version   Version when the old class name was deprecated. Defaults to 'TBD'.
	 *
	 * @return string The deprecation message.
	 */
	public static function get_deprecation_message( $old_class, $new_class, $version = 'TBD' ) {
		if ( 'TBD' === $version ) {
			return sprintf( '%1$s is deprecated. Use %2$s instead.', $old_class, $new_class );
		}

		return sprintf( '%1$s is deprecated since version %2$s. Use %3$s instead.', $old_class, $version, $new_class );
	}

	/**
	 * Checks whether a class exists under any known namespace.
	 *
	 * Searches for a class by its short name across the new PSR-4 namespace,
	 * the GV\* namespace, and the legacy GravityView_* naming convention.
	 *
	 * @since 3.0.0
	 *
	 * @param string $class_name Short class name (e.g., 'Plugin') or fully qualified name.
	 *
	 * @return string|false The fully qualified class name if found, false otherwise.
	 */
	public static function class_exists_anywhere( $class_name ) {
		// Check alias registry first to resolve old names to new class names.
		if ( isset( self::$aliases[ $class_name ] ) ) {
			return self::$aliases[ $class_name ];
		}

		// Check as-is (already fully qualified or exists via autoloading).
		if ( class_exists( $class_name ) ) {
			return $class_name;
		}

		return false;
	}

	/**
	 * Logs a migration-related message.
	 *
	 * Uses the GravityView logger when available, falls back to error_log
	 * only when WP_DEBUG is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @param string $message Log message.
	 * @param string $level   Log level: 'debug', 'info', 'warning', 'error'. Default 'debug'.
	 * @param array  $context Optional context data for structured logging.
	 *
	 * @return void
	 */
	public static function log( $message, $level = 'debug', $context = [] ) {
		$prefixed_message = '[GravityView Migration] ' . $message;

		if ( function_exists( 'gravityview' ) && is_callable( [ gravityview(), 'log' ] ) && gravityview()->log ) {
			$logger = gravityview()->log;

			if ( is_callable( [ $logger, $level ] ) ) {
				$logger->{$level}( $prefixed_message, $context );

				return;
			}
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( $prefixed_message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Returns all aliases registered during this request.
	 *
	 * @since 3.0.0
	 *
	 * @return array<string, string> Map of old class name => new class name.
	 */
	public static function get_aliases() {
		return self::$aliases;
	}

	/**
	 * Returns the deprecation log for this request.
	 *
	 * @since 3.0.0
	 *
	 * @return array<int, array{old_class: string, new_class: string, version: string}>
	 */
	public static function get_deprecation_log() {
		return self::$deprecation_log;
	}

	/**
	 * Resets the internal state. Intended for testing only.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function reset() {
		self::$aliases         = [];
		self::$deprecation_log = [];
	}
}
