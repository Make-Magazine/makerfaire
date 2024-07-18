<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 05-June-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Settings;

class Helpers {
	/**
	 * Compares 2 values using an operator.
	 *
	 * @see UI/src/lib/validation.js
	 *
	 * @param string $first  First value.
	 * @param string $second Second value.
	 * @param string $op     Operator.
	 *
	 * @return bool
	 */
	public static function compare_values( $first, $second, $op ) {
		// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
		switch ( $op ) {
			case '!=':
				return $first != $second;
			case '>':
				return (int) $first > (int) $second;
			case '<':
				return (int) $first < (int) $second;
			case 'pattern':
				return preg_match( '/' . $first . '/', $second );
			case '=':
			default:
				return $first == $second;
		}
		// phpcs:enable WordPress.PHP.StrictComparisons.LooseComparison
	}
}
