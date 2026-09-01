<?php
/**
 * Generic utilities.
 *
 * @package GravityKit\GravityView\Utils
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Utils;

/**
 * Generic utilities.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Utils namespace.
 */
class Utils {
	/**
	 * Grab a value from the _GET superglobal or default.
	 *
	 * @param string $name The key name (will be prefixed).
	 * @param mixed  $default The default value if not found (Default: null)
	 *
	 * @return mixed The value or $default if not found.
	 */
	public static function _GET( $name, $default = null ) {
		return self::get( $_GET, $name, $default );
	}

	/**
	 * Grab a value from the _POST superglobal or default.
	 *
	 * @param string $name The key name (will be prefixed).
	 * @param mixed  $default The default value if not found (Default: null)
	 *
	 * @return mixed The value or $default if not found.
	 */
	public static function _POST( $name, $default = null ) {
		return self::get( $_POST, $name, $default );
	}

	/**
	 * Grab a value from the _REQUEST superglobal or default.
	 *
	 * @param string $name The key name (will be prefixed).
	 * @param mixed  $default The default value if not found (Default: null)
	 *
	 * @return mixed The value or $default if not found.
	 */
	public static function _REQUEST( $name, $default = null ) {
		return self::get( $_REQUEST, $name, $default );
	}

	/**
	 * Grab a value from the _SERVER superglobal or default.
	 *
	 * @param string $name The key name (will be prefixed).
	 * @param mixed  $default The default value if not found (Default: null)
	 *
	 * @return mixed The value or $default if not found.
	 */
	public static function _SERVER( $name, $default = null ) {
		return self::get( $_SERVER, $name, $default );
	}

	/**
	 * Returns the View ID from the current request URL.
	 *
	 * GravityView's canonical View selector is the `gvid` query arg (used to pick
	 * a View when a page has several). Entry-action links (edit, delete, duplicate)
	 * additionally carry `view_id`.
	 *
	 * @since 3.0.1
	 *
	 * @param bool  $require_view_id    Also accept the `view_id` arg as a fallback
	 *                                  when `gvid` is absent. Pass true ONLY for
	 *                                  entry-action permission checks (so dropping
	 *                                  `gvid` can't skip them). Leave false for
	 *                                  view selection: `view_id` is a generic arg a
	 *                                  request may carry for unrelated reasons, and
	 *                                  it must not change which View renders.
	 *                                  Default: false.
	 * @param mixed $return_empty_value Value to return when no View ID is found,
	 *                                  to match a caller's existing default.
	 *                                  Default: null.
	 *
	 * @return int|mixed The View ID (int) when present, otherwise $return_empty_value.
	 */
	public static function get_view_id_from_request( $require_view_id = false, $return_empty_value = null ) {
		$view_id = self::_GET( 'gvid' );
		$missing = null === $view_id || '' === $view_id;

		if ( $missing && $require_view_id ) {
			$view_id = self::_GET( 'view_id' );
			$missing = null === $view_id || '' === $view_id;
		}

		return $missing ? $return_empty_value : (int) $view_id;
	}

	/**
	 * Grab a value from an array or an object or default.
	 *
	 * Supports nested arrays, objects via / key delimiters.
	 *
	 * @param array|object|mixed $array The array (or object). If not array or object, returns $default.
	 * @param string             $key The key.
	 * @param mixed              $default The default value. Default: null
	 *
	 * @return mixed  The value or $default if not found.
	 */
	public static function get( $array, $key, $default = null ) {

		if ( ! is_array( $array ) && ! is_object( $array ) ) {
			return $default;
		}

		// Only allow string or integer keys. No null, array, etc.
		if ( ! is_string( $key ) && ! is_int( $key ) ) {
			return $default;
		}

		/**
		 * Try direct key.
		 */
		if ( is_array( $array ) || $array instanceof \ArrayAccess ) {
			if ( isset( $array[ $key ] ) ) {
				return $array[ $key ];
			}
		} elseif ( is_object( $array ) ) {
			if ( isset( $array->$key ) ) {
				return $array->$key;
			}
		}

		/**
		 * Try subkeys after split.
		 */
		if ( count( $parts = explode( '/', $key, 2 ) ) > 1 ) {
			return self::get( self::get( $array, $parts[0] ), $parts[1], $default );
		}

		return $default;
	}

	/**
	 * Adds the date picker's `minDate` / `maxDate` bounds to a config from a source array.
	 *
	 * @since 3.0.0
	 *
	 * @param array $config The picker config to update.
	 * @param array $source The source holding `min_date` and `max_date`.
	 *
	 * @return array The config with `minDate` / `maxDate` set when present.
	 */
	public static function add_date_bounds( array $config, array $source ): array {
		$min_date = self::get( $source, 'min_date' );
		$max_date = self::get( $source, 'max_date' );

		if ( ! gv_empty( $min_date, false, false ) ) {
			$config['minDate'] = $min_date;
		}

		if ( ! gv_empty( $max_date, false, false ) ) {
			$config['maxDate'] = $max_date;
		}

		return $config;
	}

	/**
	 * Sanitizes Excel formulas inside CSV output
	 *
	 * @internal
	 * @since 2.1
	 *
	 * @param string $value The cell value to strip formulas from.
	 *
	 * @return string The sanitized value.
	 */
	public static function strip_excel_formulas( $value ) {
		$value            = (string) $value;
		$formula_prefixes = array( '=', '+', '-', '@' );
		$trimmed          = preg_replace( '/^[\s\x{00A0}\x{FEFF}]+/u', '', $value );
		$trimmed          = null === $trimmed ? ltrim( $value, " \t\r\n\0\x0B" ) : $trimmed;
		$first_character  = substr( $trimmed, 0, 1 );

		if ( '' !== $trimmed && in_array( $first_character, $formula_prefixes, true ) ) {
			$value = "'" . $value;
		}

		return $value;
	}

	/**
	 * Return a value by call.
	 *
	 * Use for quick hook callback returns and whatnot.
	 *
	 * @internal
	 * @since 2.1
	 *
	 * @param mixed $value The value to return from the closure.
	 *
	 * @return \Closure The closure with the $value bound.
	 */
	public static function _return( $value ) {
		return function () use ( $value ) {
			return $value;
		};
	}

	/**
	 * Output an associative array represenation of the query parameters.
	 *
	 * @internal
	 * @since 2.1
	 *
	 * @param \GF_Query $query The query object to dump.
	 *
	 * @return array An associative array of parameters.
	 */
	public static function gf_query_debug( $query ) {
		$introspect = $query->_introspect();
		return [
			'where' => $query->_where_unwrap( $introspect['where'] ),
		];
	}

	/**
	 * Strips aliases in columns
	 *
	 * @see https://github.com/gravityview/GravityView/issues/1308#issuecomment-617075190
	 *
	 * @internal
	 *
	 * @since 2.8.1
	 *
	 * @param \GF_Query_Condition $condition The condition to strip column aliases from.
	 *
	 * @return \GF_Query_Condition
	 */
	public static function gf_query_strip_condition_column_aliases( $condition ) {
		if ( $condition->expressions ) {
			$conditions = [];
			foreach ( $condition->expressions as $expression ) {
				$conditions[] = self::gf_query_strip_condition_column_aliases( $expression );
			}
			return call_user_func_array(
				[ '\GF_Query_Condition', 'AND' == $condition->operator ? '_and' : '_or' ],
				$conditions
			);
		} elseif ( $condition->left instanceof \GF_Query_Column ) {
				return new \GF_Query_Condition(
					new \GF_Query_Column( $condition->left->field_id ),
					$condition->operator,
					$condition->right
				);
		}

		return $condition;
	}
}
