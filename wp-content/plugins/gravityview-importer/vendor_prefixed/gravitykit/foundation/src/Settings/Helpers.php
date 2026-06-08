<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Settings;

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
		// phpcs:disable Universal.Operators.StrictComparisons
		switch ( $op ) {
			case '!=':
				return $first != $second;
			case '>':
				return (int) $first > (int) $second;
			case '<':
				return (int) $first < (int) $second;
			case 'pattern':
				return (bool) preg_match( '/' . $first . '/', $second );
			case '=':
			default:
				return $first == $second;
		}
		// phpcs:enable Universal.Operators.StrictComparisons
	}
}
