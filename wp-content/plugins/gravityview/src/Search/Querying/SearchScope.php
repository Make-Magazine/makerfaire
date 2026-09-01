<?php

namespace GravityKit\GravityView\Search\Querying;

use GravityKit\GravityView\Utils\Utils;
use GravityKit\GravityView\View\View;

use function gravityview;

/**
 * Scopes a front-end search to the View it was performed on.
 *
 * A search form emits a `gv_search_view` parameter carrying its View ID, so
 * another View on the same page neither applies the search nor reports its own
 * "no results". Scoping is on by default; a request without the parameter (a
 * legacy URL, a hand-built search link) applies to every View, and the
 * `gk/gravityview/search/scope-to-view` filter turns scoping off so one search
 * bar can drive every View on the page again.
 *
 * @since 3.1.0
 */
final class SearchScope {

	/**
	 * The request parameter carrying the searched View's ID.
	 *
	 * @since 3.1.0
	 */
	const KEY = 'gv_search_view';

	/**
	 * Returns the View ID a search was scoped to, or null when unscoped.
	 *
	 * Read from the same superglobal the search itself is read from (the search
	 * form's configurable method), so the scope decision always agrees with the
	 * search decision, including when a background job replays request args.
	 *
	 * @since 3.1.0
	 *
	 * @return int|null
	 */
	public static function requested_view_id(): ?int {
		$is_post = 'post' === SearchRequest::method();
		$value   = $is_post ? Utils::_POST( self::KEY ) : Utils::_GET( self::KEY );

		return self::parse_view_id( $value );
	}

	/**
	 * Whether searches are scoped to the View that emitted them.
	 *
	 * @since 3.1.0
	 *
	 * @param View|int|null $view The View being evaluated, if known.
	 *
	 * @return bool
	 */
	public static function is_enabled( $view = null ): bool {
		$view_object = $view instanceof View ? $view : gravityview()->views->get( $view );

		/**
		 * Controls whether a front-end search is scoped to the View it was performed on.
		 *
		 * Returning false restores the legacy behavior: a single search bar filters
		 * every View on the page.
		 *
		 * @filter `gk/gravityview/search/scope-to-view`
		 *
		 * @since 3.1.0
		 *
		 * @param bool      $enabled Whether to scope searches per View. Default: true.
		 * @param View|null $view    The View being evaluated, if known.
		 */
		return (bool) apply_filters( 'gk/gravityview/search/scope-to-view', true, $view_object );
	}

	/**
	 * Whether the current request's search applies to the given View.
	 *
	 * True when scoping is off, when the request carries no scope (a legacy
	 * URL), or when the scope matches this View; false only when scoping is on
	 * and the request targets a different View.
	 *
	 * @since 3.1.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return bool
	 */
	public static function matches( $view ): bool {
		if ( ! self::is_enabled( $view ) ) {
			return true;
		}

		return self::matches_view_id( $view, self::requested_view_id() );
	}

	/**
	 * Whether a request's own search arguments apply to the given View.
	 *
	 * Same rule as {@see self::matches()}, but the scope is read from a
	 * request-argument array rather than the superglobals, so CLI and background
	 * requests (which carry their arguments instead of `$_GET`/`$_POST`) agree
	 * with the search parser.
	 *
	 * @since 3.1.0
	 *
	 * @param array    $arguments The request arguments.
	 * @param View|int $view      The View or View ID.
	 *
	 * @return bool
	 */
	public static function matches_in( array $arguments, $view ): bool {
		if ( ! self::is_enabled( $view ) ) {
			return true;
		}

		return self::matches_view_id( $view, self::parse_view_id( $arguments[ self::KEY ] ?? null ) );
	}

	/**
	 * Returns the scope parameter as URL arguments for a View.
	 *
	 * Used by search form actions and search-link fields so the search they
	 * submit is attributed to their own View.
	 *
	 * @since 3.1.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return array{gv_search_view:int}
	 */
	public static function request_args( $view ): array {
		return [ self::KEY => self::view_id( $view ) ];
	}

	/**
	 * Whether a resolved scope View ID applies to the given View.
	 *
	 * @since 3.1.0
	 *
	 * @param View|int $view              The View or View ID.
	 * @param int|null $requested_view_id The scoped View ID, or null when unscoped.
	 *
	 * @return bool
	 */
	private static function matches_view_id( $view, ?int $requested_view_id ): bool {
		if ( null === $requested_view_id ) {
			return true;
		}

		return $requested_view_id === self::view_id( $view );
	}

	/**
	 * Parses a raw scope value into a View ID, or null when absent/invalid.
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $value The raw scope value.
	 *
	 * @return int|null
	 */
	private static function parse_view_id( $value ): ?int {
		// A request-controllable value: reject non-scalars (e.g. gv_search_view[]=1)
		// and empty strings so a dirty request degrades to unscoped, never a fatal
		// or a bogus scope. absint() handles negatives/whitespace/non-numeric.
		if ( ! is_scalar( $value ) || '' === $value ) {
			return null;
		}

		$view_id = absint( $value );

		return $view_id ?: null;
	}

	/**
	 * Normalizes a View or View ID to the View ID.
	 *
	 * @since 3.1.0
	 *
	 * @param View|int $view The View or View ID.
	 *
	 * @return int
	 */
	private static function view_id( $view ): int {
		if ( $view instanceof View ) {
			return (int) $view->ID;
		}

		return absint( $view );
	}
}
