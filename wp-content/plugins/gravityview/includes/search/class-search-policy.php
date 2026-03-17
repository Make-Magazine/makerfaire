<?php

namespace GV\Search;

use GV\Search\Policies\Search_Fields_Policy;
use GV\View;

/**
 * The Search Policy enforces/defines the rules of search.
 *
 * It answers questions like:
 * - What operators are allowed?
 * - What fields are searchable?
 * - What date format is expected?
 * - Should input be trimmed?
 *
 * @since $ver$
 */
final class Search_Policy {
	/**
	 * Cache for the datepicker format key.
	 *
	 * @since $ver$
	 *
	 * @var string|null
	 */
	private static ?string $datepicker_format_cache = null;

	/**
	 * Cache for operator allowlist per key.
	 *
	 * @since $ver$
	 *
	 * @var array<string, string[]>
	 */
	private static array $operator_allowlist_cache = [];

	/**
	 * Cache for whitespace stripping setting per View.
	 *
	 * @since $ver$
	 *
	 * @var array<int|string, bool>
	 */
	private static array $whitespace_stripped_cache = [];

	/**
	 * The map of date format keys to PHP date format strings.
	 *
	 * @since $ver$
	 *
	 * @var array<string, string>
	 */
	private const DATE_FORMATS = [
		'mdy'       => 'm/d/Y',
		'dmy_dash'  => 'd-m-Y',
		'dmy_dot'   => 'd.m.Y',
		'dmy'       => 'd/m/Y',
		'ymd_slash' => 'Y/m/d',
		'ymd_dash'  => 'Y-m-d',
		'ymd_dot'   => 'Y.m.d',
	];

	/**
	 * The default date format key.
	 *
	 * @since $ver$
	 *
	 * @var string
	 */
	private const DEFAULT_DATE_FORMAT = 'mdy';

	/**
	 * Clears all internal caches.
	 *
	 * @since    $ver$
	 *
	 * @internal Used for testing purposes only.
	 */
	public static function clear_cache(): void {
		self::$datepicker_format_cache   = null;
		self::$operator_allowlist_cache  = [];
		self::$whitespace_stripped_cache = [];
		Search_Fields_Policy::clear_cache();
	}

	/**
	 * Returns the allowed operators for a specific key.
	 *
	 * @since $ver$
	 *
	 * @param string   $key      The filter key.
	 * @param string[] $defaults The default allowed operators.
	 *
	 * @return string[] The allowed operators.
	 */
	private static function get_allowed_operators( string $key, array $defaults = [ '=' ] ): array {
		if ( isset( self::$operator_allowlist_cache[ $key ] ) ) {
			return self::$operator_allowlist_cache[ $key ];
		}

		/**
		 * @deprecated 2.14 Use `gk/gravityview/search/operators/allowed`.
		 */
		$allowed = apply_filters_deprecated(
			'gravityview/search/operator_whitelist',
			[ $defaults, $key ],
			'2.14',
			'gravityview/search/operator_allowlist'
		);

		/**
		 * @deprecated $ver$ Use `gk/gravityview/search/operators/allowed`.
		 */
		$allowed = apply_filters_deprecated(
			'gravityview/search/operator_allowlist',
			[ $allowed, $key ],
			'$ver$',
			'gk/gravityview/search/operators/allowed'
		);

		/**
		 * Modifies an array of allowed operators for a field.
		 *
		 * @since  $ver$
		 *
		 * @param string[] $allowed An allowlist of operators.
		 * @param string   $key     The filter key (legacy request key or canonical key).
		 */
		$allowed = (array) apply_filters( 'gk/gravityview/search/operators/allowed', $allowed, $key );

		self::$operator_allowlist_cache[ $key ] = $allowed;

		return $allowed;
	}

	/**
	 * Returns the validated operator for a specific key.
	 *
	 * Applies filter hooks twice when key differs from request_key:
	 * 1. First with request_key (BC for existing hooks using legacy keys like `gv_search`).
	 * 2. Then with key (new canonical names like `search_all`).
	 *
	 * @since $ver$
	 *
	 * @param string   $operator    The provided operator.
	 * @param string   $request_key The key as it appeared in the request (used in filter hooks for BC).
	 * @param string   $key         The canonical key name.
	 * @param string[] $defaults    The default allowed operators.
	 * @param string   $fallback    The fallback operator in case the provided isn't allowed.
	 *
	 * @return string The validated operator.
	 */
	public static function resolve_operator(
		string $operator,
		string $request_key,
		string $key,
		array $defaults = [ '=' ],
		string $fallback = '='
	): string {
		// Apply with request_key first for backwards compatibility.
		$allowed = self::get_allowed_operators( $request_key, $defaults );

		// Apply with the canonical key if it is different from the request_key.
		if ( $key !== $request_key ) {
			$allowed = self::get_allowed_operators( $key, $allowed );
		}

		if ( ! in_array( $operator, $allowed, true ) ) {
			return $fallback;
		}

		return $operator;
	}

	/**
	 * Returns whether the field ID is searchable.
	 *
	 * @since $ver$
	 *
	 * @param View     $view     The View.
	 * @param string   $field_id The field ID.
	 * @param int|null $form_id  The optional Form ID.
	 *
	 * @return bool Whether the field is searchable.
	 */
	public static function is_field_searchable( View $view, string $field_id, ?int $form_id = null ): bool {
		return ( new Search_Fields_Policy( $view ) )->is_field_searchable( $field_id, $form_id );
	}

	/**
	 * Returns the datepicker format key.
	 *
	 * @since $ver$
	 *
	 * @return string The format key (e.g., 'mdy', 'ymd_dash').
	 */
	public static function get_date_format_key(): string {
		$format = self::get_date_format_from_filter();

		// If the format key isn't valid, return the default format key.
		return isset( self::DATE_FORMATS[ $format ] ) ? $format : self::DEFAULT_DATE_FORMAT;
	}

	/**
	 * Returns the PHP date format string for the datepicker.
	 *
	 * @since $ver$
	 *
	 * @return string The PHP date format (e.g., 'm/d/Y', 'Y-m-d').
	 */
	public static function get_date_php_format(): string {
		$format = self::get_date_format_from_filter();

		// If the format key isn't valid, return the default format value.
		return self::DATE_FORMATS[ $format ] ?? self::DATE_FORMATS[ self::DEFAULT_DATE_FORMAT ];
	}

	/**
	 * Returns the date format key from the filter, with caching.
	 *
	 * @since $ver$
	 *
	 * @see   https://docs.gravitykit.com/article/115-changing-the-format-of-the-search-widgets-date-picker
	 *
	 * @return string The format key from the filter.
	 */
	private static function get_date_format_from_filter(): string {
		if ( null !== self::$datepicker_format_cache ) {
			return self::$datepicker_format_cache;
		}

		/**
		 * @deprecated $ver$ Use `gk/gravityview/search/datepicker/format`.
		 */
		$format = apply_filters_deprecated(
			'gravityview/widgets/search/datepicker/format',
			[ self::DEFAULT_DATE_FORMAT ],
			'$ver$',
			'gk/gravityview/search/datepicker/format'
		);

		/**
		 * Modifies the datepicker format.
		 *
		 * @since  $ver$
		 *
		 * @param string $format  Default: mdy
		 *                        Options are:
		 *                        - `mdy` (mm/dd/yyyy)
		 *                        - `dmy` (dd/mm/yyyy)
		 *                        - `dmy_dash` (dd-mm-yyyy)
		 *                        - `dmy_dot` (dd.mm.yyyy)
		 *                        - `ymd_slash` (yyyy/mm/dd)
		 *                        - `ymd_dash` (yyyy-mm-dd)
		 *                        - `ymd_dot` (yyyy.mm.dd)
		 */
		self::$datepicker_format_cache = apply_filters( 'gk/gravityview/search/datepicker/format', $format );

		return self::$datepicker_format_cache;
	}

	/**
	 * Returns whether the timezone should be adjusted for date entries.
	 *
	 * @since $ver$
	 *
	 * @param string $context Where the filter is being called from (default: 'search').
	 *
	 * @return bool Whether to adjust the timezone.
	 */
	public static function should_adjust_timezone( string $context = 'search' ): bool {
		/**
		 * @deprecated $ver$ Use `gk/gravityview/search/date/adjust-timezone`.
		 */
		$adjust_tz = apply_filters_deprecated(
			'gravityview_date_created_adjust_timezone',
			[ false, $context ],
			'$ver$',
			'gk/gravityview/search/date/adjust-timezone'
		);

		/**
		 * Whether to adjust the timezone for entries.
		 *
		 * `date_created` is stored in UTC format. Convert search date into UTC (also used on templates/fields/date_created.php).
		 *  This is for backward compatibility before \GF_Query started to automatically apply the timezone offset.
		 *
		 * @since  $ver$
		 *
		 * @param bool   $adjust_tz Whether to use timezone-adjusted datetime. Default: false.
		 * @param string $context   Where the filter is being called from.
		 */
		return (bool) apply_filters( 'gk/gravityview/search/date/adjust-timezone', $adjust_tz, $context );
	}

	/**
	 * Normalize date from a datepicker format to Y-m-d format.
	 *
	 * @since $ver$
	 *
	 * @param string $date_string The date string to normalize.
	 *
	 * @return string Normalized date string or empty string if invalid.
	 */
	public static function resolve_date( string $date_string ): string {
		if ( empty( $date_string ) ) {
			return '';
		}

		// Date string is already in the proper format with time.
		$date = date_create_from_format( 'Y-m-d H:i:s', $date_string );
		if ( $date ) {
			return $date_string;
		}

		// Try parsing with the datepicker format.
		$date = date_create_from_format( self::get_date_php_format(), $date_string );
		if ( $date ) {
			return $date->format( 'Y-m-d' );
		}

		return '';
	}

	/**
	 * Returns whether whitespace should be stripped from a search value.
	 *
	 * @since $ver$
	 *
	 * @param View|null $view The View.
	 *
	 * @return bool Whether whitespace should be stripped.
	 */
	public static function should_trim_input( ?View $view = null ): bool {
		$cache_key = $view ? ( $view->ID ?? 0 ) : 0;

		if ( isset( self::$whitespace_stripped_cache[ $cache_key ] ) ) {
			return self::$whitespace_stripped_cache[ $cache_key ];
		}

		/**
		 * @deprecated $ver$ Use `gk/gravityview/search/value/trim`.
		 */
		$trim = apply_filters_deprecated(
			'gravityview/search-trim-input',
			[ true, $view ],
			'$ver$',
			'gk/gravityview/search/value/trim'
		);

		/**
		 * Whether to remove leading/trailing whitespaces from search value.
		 *
		 * @since  $ver$
		 *
		 * @param bool      $trim Whether to remove whitespace. Default: true.
		 * @param View|null $view The View being searched.
		 */
		self::$whitespace_stripped_cache[ $cache_key ] = (bool) apply_filters(
			'gk/gravityview/search/value/trim',
			$trim,
			$view
		);

		return self::$whitespace_stripped_cache[ $cache_key ];
	}

	/**
	 * Returns whether empty field values should be ignored or strictly matched.
	 *
	 * @since $ver$
	 *
	 * @param string   $key     The filter key.
	 * @param int|null $view_id The View ID.
	 * @param int|null $form_id The Form ID.
	 *
	 * @return bool Whether to ignore empty values (default: true).
	 */
	public static function should_ignore_empty( string $key, ?int $view_id = null, ?int $form_id = null ): bool {
		/**
		 * @deprecated $ver$ Use `gk/gravityview/search/value/ignore-empty`.
		 */
		$ignore_empty = apply_filters_deprecated(
			'gravityview/search/ignore-empty-values',
			[ true, $key, $view_id, $form_id ],
			'$ver$',
			'gk/gravityview/search/value/ignore-empty'
		);

		/**
		 * Whether empty field values should be ignored or strictly matched.
		 *
		 * @since  $ver$
		 *
		 * @param bool     $ignore_empty Whether to ignore empty values. Default: true.
		 * @param string   $key          The filter key.
		 * @param int|null $view_id      The View ID.
		 * @param int|null $form_id      The Form ID.
		 */
		return (bool) apply_filters(
			'gk/gravityview/search/value/ignore-empty',
			$ignore_empty,
			$key,
			$view_id,
			$form_id
		);
	}
}
