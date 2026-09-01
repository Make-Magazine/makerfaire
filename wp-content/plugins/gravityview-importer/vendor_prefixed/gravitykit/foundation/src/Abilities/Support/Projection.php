<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare( strict_types=1 );

namespace GravityKit\GravityImport\Foundation\Abilities\Support;

/**
 * Projection helpers for GravityKit ability responses.
 *
 * This is the shared include parser for the Foundation Abilities catalog.
 * Products should use this helper instead of inventing per-product include
 * semantics.
 *
 * @since 1.23.0
 */
final class Projection {

	/**
	 * Normalizes an include input against an allowed key list.
	 *
	 * Unknown values are discarded. Returned keys follow the allowed-list order
	 * so response shape stays stable regardless of caller input ordering.
	 *
	 * @since 1.23.0
	 *
	 * @param mixed    $raw     Raw include value from ability input.
	 * @param string[] $allowed Allowed projection keys in canonical order.
	 * @return string[] Normalized include keys.
	 */
	public static function normalize_include( $raw, array $allowed ): array {
		if ( ! is_array( $raw ) ) {
			return [];
		}

		$requested = [];
		foreach ( $raw as $key ) {
			if ( is_string( $key ) && '' !== $key ) {
				$requested[ $key ] = true;
			}
		}

		$include = [];
		foreach ( $allowed as $key ) {
			if ( is_string( $key ) && isset( $requested[ $key ] ) ) {
				$include[] = $key;
			}
		}

		return $include;
	}

	/**
	 * Determines whether a projected response key should be included.
	 *
	 * Empty include lists mean "use defaults". Non-empty include lists opt in
	 * only the requested key.
	 *
	 * @since 1.23.0
	 *
	 * @param string   $key      Response key.
	 * @param string[] $include  Normalized include keys.
	 * @param string[] $defaults Default response keys.
	 * @return bool
	 */
	public static function should_include( string $key, array $include, array $defaults ): bool {
		if ( [] === $include ) {
			return in_array( $key, $defaults, true );
		}

		return in_array( $key, $include, true );
	}
}
