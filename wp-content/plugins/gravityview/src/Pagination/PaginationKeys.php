<?php

namespace GravityKit\GravityView\Pagination;

use GravityKit\GravityView\Utils\Utils;
use GravityKit\GravityView\View\View;

use function gravityview;

/**
 * Resolves the pagination query key and current page for a View.
 *
 * Reading is always scoped-aware: a `pagenum_{view_id}` parameter wins over
 * the base `pagenum`, which remains a permanent fallback so existing URLs
 * keep working. Writing scoped keys into links is opt-in via the
 * `gk/gravityview/pagination/scoped-keys` filter (default: off).
 *
 * @since 3.0.0
 */
final class PaginationKeys {

	/**
	 * The legacy, page-global pagination key.
	 *
	 * @since 3.0.0
	 */
	const BASE_KEY = 'pagenum';

	/**
	 * Returns the per-View pagination key, e.g. `pagenum_123`.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return string
	 */
	public static function scoped_key( $view ) {
		return self::BASE_KEY . '_' . self::view_id( $view );
	}

	/**
	 * Whether pagination links for this View should use the scoped key.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return bool
	 */
	public static function is_scoped( $view ) {
		$view = gravityview()->views->get( $view );

		/**
		 * Controls whether pagination links are built with a per-View key
		 * (`pagenum_{view_id}`) instead of the page-global `pagenum`.
		 *
		 * Returning false preserves the legacy behavior exactly: one
		 * `pagenum` parameter paginates every View on the page.
		 *
		 * @filter `gk/gravityview/pagination/scoped-keys`
		 *
		 * @since 3.0.0
		 *
		 * @param bool      $is_scoped Whether to emit per-View pagination keys. Default: false.
		 * @param View|null $view      The View being paginated.
		 */
		$is_scoped = apply_filters( 'gk/gravityview/pagination/scoped-keys', false, $view );

		return (bool) $is_scoped;
	}

	/**
	 * Returns the pagination key to use when building links for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return string Either `pagenum` (default) or `pagenum_{view_id}`.
	 */
	public static function key( $view ) {
		$is_scoped = self::is_scoped( $view );

		if ( ! $is_scoped ) {
			return self::BASE_KEY;
		}

		return self::scoped_key( $view );
	}

	/**
	 * Returns the current page for a View from the request.
	 *
	 * The scoped key always wins when present, regardless of the
	 * `gk/gravityview/pagination/scoped-keys` filter, so scoped URLs work
	 * even on sites that have not opted into writing them.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return int The current page, minimum 1.
	 */
	public static function current_page( $view ) {
		$scoped_value = Utils::_GET( self::scoped_key( $view ) );

		$has_scoped_value = null !== $scoped_value && '' !== $scoped_value;

		if ( $has_scoped_value ) {
			return max( 1, (int) $scoped_value );
		}

		$base_value = Utils::_GET( self::BASE_KEY );

		if ( empty( $base_value ) ) {
			return 1;
		}

		// Legacy semantics for the base key are preserved exactly.
		return (int) $base_value;
	}

	/**
	 * Returns the request keys that reset pagination for a View.
	 *
	 * Used by link builders that should land on page 1, like sorting links,
	 * search links, and bulk-action redirects.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return string[]
	 */
	public static function keys_to_strip( $view ) {
		$view_id = self::view_id( $view );

		if ( ! $view_id ) {
			return [ self::BASE_KEY ];
		}

		return [ self::BASE_KEY, self::scoped_key( $view_id ) ];
	}

	/**
	 * Returns the active pagination state as URL arguments for a View.
	 *
	 * Used by entry-link builders that track the directory page. The scoped
	 * key wins; the base key keeps its legacy semantics.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return array One of `[ 'pagenum_{id}' => int ]`, `[ 'pagenum' => int ]`, or `[]`.
	 */
	public static function request_pagination_args( $view ) {
		$scoped_key   = self::scoped_key( $view );
		$scoped_value = Utils::_GET( $scoped_key );

		$has_scoped_value = null !== $scoped_value && '' !== $scoped_value;

		if ( $has_scoped_value ) {
			return [ $scoped_key => max( 1, (int) $scoped_value ) ];
		}

		$base_value = Utils::_GET( self::BASE_KEY );

		if ( empty( $base_value ) ) {
			return [];
		}

		return [ self::BASE_KEY => (int) $base_value ];
	}

	/**
	 * Returns the scoped pagination keys present in an array of URL arguments.
	 *
	 * Used to register the request's scoped keys as reserved query args,
	 * alongside the base `pagenum` (a reserved argument since 2.10).
	 *
	 * @since 3.0.0
	 *
	 * @param array $args URL arguments, keyed by parameter name.
	 *
	 * @return string[]
	 */
	public static function scoped_keys_in( array $args ) {
		$scoped_keys = [];

		foreach ( array_keys( $args ) as $key ) {
			$is_scoped_pagination_key = (bool) preg_match( '/^' . self::BASE_KEY . '_\d+$/', (string) $key );

			if ( $is_scoped_pagination_key ) {
				$scoped_keys[] = (string) $key;
			}
		}

		return $scoped_keys;
	}

	/**
	 * Removes every scoped pagination key from an array of URL arguments.
	 *
	 * The base `pagenum` key is left alone; callers that also want it gone
	 * already handle it (it has been a reserved argument since 2.10).
	 *
	 * @since 3.0.0
	 *
	 * @param array $args URL arguments, keyed by parameter name.
	 *
	 * @return array
	 */
	public static function strip_scoped( array $args ) {
		foreach ( self::scoped_keys_in( $args ) as $scoped_key ) {
			unset( $args[ $scoped_key ] );
		}

		return $args;
	}

	/**
	 * Normalizes a View or View ID to the View ID.
	 *
	 * @since 3.0.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return int
	 */
	private static function view_id( $view ) {
		if ( $view instanceof View ) {
			return (int) $view->ID;
		}

		return absint( $view );
	}
}
