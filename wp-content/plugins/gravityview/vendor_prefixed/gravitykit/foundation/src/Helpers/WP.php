<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 08-December-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityView\Foundation\Helpers;

use Closure;
use Exception;

class WP {
	/**
	 * Wrapper around {@see set_transient()}. Transient is stored as an option in order to avoid object caching issues.
	 *
	 * @since 1.2.6
	 *
	 * @param string $transient  Transient name.
	 * @param mixed  $value      Transient value.
	 * @param int    $expiration (optional) Time until expiration in seconds. Default: 0 (no expiration).
	 *
	 *
	 * @return bool True if the value was set, false otherwise.
	 */
	public static function set_transient( string $transient, $value, int $expiration = 0 ): bool {
		$expiration = (int) $expiration;

		$value = apply_filters( "pre_set_transient_{$transient}", $value, $expiration, $transient );

		$expiration = apply_filters( "expiration_of_transient_{$transient}", $expiration, $value, $transient );

		$data = [
			'expiration' => 0 === $expiration ? $expiration : time() + $expiration,
			'value'      => $value,
		];

		$result = update_option( $transient, $data );

		if ( $result ) {
			do_action( "set_transient_{$transient}", $value, $expiration, $transient );
			do_action( 'setted_transient', $transient, $value, $expiration );
		}

		return $result;
	}

	/**
	 * Wrapper around {@see set_site_transient()}. Transient is stored as an option in order to avoid object caching issues.
	 *
	 * @since 1.2.6
	 *
	 * @param string $transient  Transient name.
	 * @param mixed  $value      Transient value.
	 * @param int    $expiration (optional) Time until expiration in seconds. Default: 0 (no expiration).
	 *
	 *
	 * @return bool True if the value was set, false otherwise.
	 */
	public static function set_site_transient( string $transient, $value, int $expiration = 0 ): bool {
		$expiration = (int) $expiration;

		$value = apply_filters( "pre_set_site_transient_{$transient}", $value, $transient );

		$expiration = apply_filters( "expiration_of_site_transient_{$transient}", $expiration, $value, $transient );

		$data = [
			'expiration' => 0 === $expiration ? $expiration : time() + $expiration,
			'value'      => $value,
		];

		$result = update_site_option( $transient, $data );

		if ( $result ) {
			do_action( "set_site_transient_{$transient}", $value, $expiration, $transient );
			do_action( 'setted_site_transient', $transient, $value, $expiration );
		}

		return $result;
	}

	/**
	 * Wrapper around {@see get_transient()}. Transient is stored as an option {@see self::set_transient()} in order to avoid object caching issues.
	 *
	 * @since 1.2.6
	 *
	 * @param string $transient Transient name.
	 *
	 * @return mixed Transient value.
	 */
	public static function get_transient( string $transient ) {
		$pre = apply_filters( "pre_transient_{$transient}", false, $transient );

		if ( false !== $pre ) {
			return $pre;
		}

		$data = get_option( $transient );

		if ( is_array( $data ) && array_key_exists( 'expiration', $data ) && array_key_exists( 'value', $data ) ) {
			if ( 0 !== ( $data['expiration'] ?? 0 ) && time() > $data['expiration'] ) {
				delete_option( $transient );

				return false;
			}

			$value = $data['value'];
		} else {
			$value = false;
		}

		return apply_filters( "transient_{$transient}", $value, $transient );
	}

	/**
	 * Wrapper around {@see get_site_transient()}. Transient is stored as an option {@see self::set_site_transient()} in order to avoid object caching issues.
	 *
	 * @since 1.2.6
	 *
	 * @param string $transient Transient name.
	 *
	 * @return mixed Transient value.
	 */
	public static function get_site_transient( string $transient ) {
		$pre = apply_filters( "pre_site_transient_{$transient}", false, $transient );

		if ( false !== $pre ) {
			return $pre;
		}

		$data = get_site_option( $transient );

		if ( is_array( $data ) && array_key_exists( 'expiration', $data ) && array_key_exists( 'value', $data ) ) {
			if ( 0 !== ( $data['expiration'] ?? 0 ) && time() > $data['expiration'] ) {
				delete_option( $transient );

				return false;
			}

			$value = $data['value'];
		} else {
			$value = false;
		}

		return apply_filters( "site_transient_{$transient}", $value, $transient );
	}
}
