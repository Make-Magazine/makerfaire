<?php
/**
 * GravityImport/src/Server.php
 *
 * @since      2.6.0
 * @subpackage Server
 * @package    GravityImport
 */

namespace GravityKit\GravityImport;

/**
 * Server class.
 *
 * @since 2.6.0
 */
class Server {

	/**
	 * The maximum timeout in seconds.
	 *
	 * @since 2.6.0
	 *
	 * @var int Default: 6000 (10 minutes).
	 */
	const MAX_TIMEOUT = 6000;

	/**
	 * The default timeout in seconds.
	 *
	 * @since 2.6.0
	 *
	 * @var int
	 */
	const DEFAULT_TIMEOUT = 20;

	/**
	 * The timeout reserve in seconds.
	 *
	 * @since 2.6.0
	 *
	 * @var int
	 */
	const TIMEOUT_RESERVE = 5;

	/**
	 * The memory reserve in bytes.
	 *
	 * @since 2.6.0
	 *
	 * @var int
	 */
	const MEMORY_RESERVE_MB = 8;

	/**
	 * Get the timeout based on the execution limit for the current PHP process.
	 *
	 * If the execution limit is 0, we assume that the memory limit is the limiting factor.
	 *
	 * @since 2.6.0
	 *
	 * @internal
	 *
	 * @return int The timeout in seconds.
	 */
	public static function get_timeout() {
		static $timeout = null;

		if ( ! is_null( $timeout ) ) {
			return $timeout;
		}

		$execution_limit = self::DEFAULT_TIMEOUT;

		if ( function_exists( 'ini_get' ) ) {
			$raw_limit       = ini_get( 'max_execution_time' );
			$execution_limit = is_numeric( $raw_limit ) ? (int) $raw_limit : self::DEFAULT_TIMEOUT;
		}

		// Okay, they say we have no execution limit.
		// That means we need to rely on the memory limit to restrict the execution.
		if ( 0 === $execution_limit ) {
			$timeout = self::MAX_TIMEOUT;

			return $timeout;
		}

		$timeout = $execution_limit - self::TIMEOUT_RESERVE;

		if ( $timeout <= 0 ) {
			$timeout = self::DEFAULT_TIMEOUT;

			return $timeout;
		}

		// Cap the timeout at MAX_TIMEOUT to prevent excessive values.
		$timeout = min( $timeout, self::MAX_TIMEOUT );

		return $timeout;
	}

	/**
	 * Get the PHP memory limit in bytes.
	 *
	 * @return int Memory limit in bytes. Returns 0 for unlimited.
	 */
	public static function get_memory_limit() {
		static $memory_limit;

		if ( ! is_null( $memory_limit ) ) {
			return $memory_limit;
		}

		/**
		 * @var int $fallback The fallback memory limit in bytes. 128 MB.
		 */
		$fallback = 128 * 1024 * 1024;

		if ( ! function_exists( 'ini_get' ) ) {
			$memory_limit = $fallback;

			return $memory_limit;
		}

		$raw_limit = ini_get( 'memory_limit' );

		// If the memory limit is not a number, use the fallback.
		if ( ! preg_match( '#(-?\d+)([KMG]?)#i', $raw_limit, $matches ) ) {
			$memory_limit = $fallback;

			return $memory_limit;
		}

		$value = (int) $matches[1];

		// The unit is kb, mb, or gb.
		$unit = Core::strtolower( $matches[2] );

		if ( $value <= 0 ) {
			// -1 or 0 means unlimited, which is allowed in PHP.
			$memory_limit = 0;

			return $memory_limit;
		}

		$unit_multipliers = [
			'k' => 1024, // 1 KB.
			'm' => 1024 * 1024, // 1 MB.
			'g' => 1024 * 1024 * 1024, // 1 GB.
		];

		$multiplier   = $unit_multipliers[ $unit ] ?? 1; // Assume bytes if no unit is provided.
		$memory_limit = $value * $multiplier;

		return $memory_limit;
	}

	/**
	 * Calculate current memory usage limits.
	 *
	 * @since 2.6.0
	 *
	 * @param int $memory The memory limit in bytes.
	 *
	 * @return bool True if the memory is exceeded, false otherwise.
	 */
	public static function is_memory_exceeded( $memory ) {
		// Validate input parameter.
		if ( ! is_numeric( $memory ) || $memory <= 0 ) {
			// Return false for unlimited memory (0 or -1).
			return false;
		}

		$memory  = (int) $memory;
		$usage   = memory_get_usage();
		$reserve = self::MEMORY_RESERVE_MB * MB_IN_BYTES;
		$needed  = $usage + $reserve;

		return $needed > $memory;
	}
}
