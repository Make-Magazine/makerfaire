<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Rest\Endpoint;

use GravityKit\AdvancedFilter\QueryFilters\Querying\Field\Source\ChoiceSourceManager;
use GravityKit\AdvancedFilter\QueryFilters\Querying\Form\FormCriteria;
use GravityKit\AdvancedFilter\QueryFilters\Querying\Form\GravityFormsFormRepository;
use GravityKit\AdvancedFilter\QueryFilters\Querying\User\UserCriteria;
use GravityKit\AdvancedFilter\QueryFilters\Querying\User\WordPressUserRepository;

/**
 * Static registry of ChoicesField REST endpoints keyed by symbolic kind.
 *
 * Wrappers register endpoint URLs once on bootstrap. Field filters reference an endpoint by its
 * kind name (e.g. `'endpoint' => 'users'`) and the URL is resolved at localize time. Raw URLs and
 * structured descriptors are also accepted by {@see self::normalize()}.
 *
 * @since 2.12.0
 */
final class EndpointRegistry {
	/**
	 * Default item count above which a field's static `values` list is dropped in favour of the
	 * registered endpoint. Overridable per request via the `gk/query-filters/auto-endpoint-threshold`
	 * filter.
	 *
	 * @since 2.12.0
	 */
	private const DEFAULT_AUTO_THRESHOLD = 50;

	/**
	 * Upper bound on the auto-switch threshold.
	 *
	 * @since 2.12.0
	 */
	private const MAX_AUTO_THRESHOLD = 10_000;

	/**
	 * Registered URLs keyed by kind. Each entry is either a string URL or a callable returning
	 * one — the callable form defers {@see rest_url()} until lookup so registration is safe
	 * during very early bootstrap hooks (before `$wp_rewrite` is available).
	 *
	 * @since 2.12.0
	 *
	 * @var array<string, string|callable():string>
	 */
	private static array $endpoints = [];

	/**
	 * Counters keyed by kind. Each is `callable(array $field, ?int $threshold): int`.
	 *
	 * @since 2.12.0
	 *
	 * @var array<string, callable>
	 */
	private static array $counters = [];

	/**
	 * Registers a REST endpoint URL under a symbolic kind.
	 *
	 * `$url` may be a fully qualified URL string or a callable returning one. The callable form
	 * defers {@see rest_url()} composition to {@see self::resolve()} so registration is safe even
	 * when `$wp_rewrite` hasn't been initialised yet (typical for `plugins_loaded`).
	 *
	 * @since 2.12.0
	 *
	 * @param string                   $kind    The endpoint identifier (e.g. `users`, `forms`, `field`).
	 * @param string|callable():string $url     The fully qualified REST URL, or a callable returning one.
	 * @param callable|null            $counter A `callable(array $field, ?int $threshold): int` reporting the count for the kind; global kinds ignore the field and may self-memoize.
	 */
	public static function register( string $kind, $url, ?callable $counter = null ): void {
		self::$endpoints[ $kind ] = $url;

		if ( $counter ) {
			self::$counters[ $kind ] = $counter;
		}
	}

	/**
	 * Resolves an endpoint kind to a URL.
	 *
	 * Returns the explicitly registered URL when present. Otherwise, for the built-in kinds
	 * (`users`, `forms`), composes the URL lazily from the prefix passed to
	 * {@see self::register_defaults()}. The lazy path defers the {@see rest_url()} call until
	 * lookup time, which avoids fatals when the registry is seeded during early bootstrap hooks.
	 *
	 * @since 2.12.0
	 *
	 * @param string $kind The endpoint identifier.
	 *
	 * @return string|null The URL, or null when the kind is unknown.
	 */
	public static function resolve( string $kind ): ?string {
		if ( ! isset( self::$endpoints[ $kind ] ) ) {
			return null;
		}

		$entry = self::$endpoints[ $kind ];

		return is_callable( $entry ) ? (string) $entry() : (string) $entry;
	}

	/**
	 * Stores the REST namespace prefix used to lazily resolve the conventional ChoicesField
	 * endpoints (`users`, `forms`). The actual URL composition is deferred to {@see self::resolve()}
	 * so callers can invoke this safely during very early bootstrap.
	 *
	 * @since 2.12.0
	 * @since 2.14.0 Added the `$manager` parameter.
	 *
	 * @param string                  $prefix  REST namespace prefix used when registering routes.
	 * @param ChoiceSourceManager|null $manager Source manager backing the per-field `field` kind; when null the kind is not registered.
	 */
	public static function register_defaults( string $prefix, ?ChoiceSourceManager $manager = null ): void {
		self::register(
			'forms',
			static fn(): string => rest_url( $prefix . '/query-filters/choice/forms' ),
			static function ( array $field = [], ?int $threshold = null ): int {
				static $n;
				return $n ??= (int) ( new GravityFormsFormRepository( $GLOBALS['wpdb'] ) )->count( FormCriteria::search() );
			}
		);
		self::register(
			'users',
			static fn(): string => rest_url( $prefix . '/query-filters/choice/users' ),
			static function ( array $field = [], ?int $threshold = null ): int {
				static $n;
				return $n ??= (int) ( new WordPressUserRepository() )->count( UserCriteria::search() );
			}
		);

		if ( $manager ) {
			self::register(
				'field',
				static fn(): string => rest_url( $prefix . '/query-filters/choice/field' ),
				static function ( array $field, ?int $threshold ) use ( $manager ): int {
					$form_id  = (int) ( $field['form_id'] ?? 0 );
					$field_id = (string) ( $field['key'] ?? '' );

					if ( $manager->prefers_endpoint( $form_id, $field_id ) ) {
						return $threshold ?? PHP_INT_MAX;
					}

					return $manager->count( $form_id, $field_id, $threshold );
				}
			);
		}
	}

	/**
	 * Normalizes a raw endpoint declaration into a fully resolved {@see Endpoint} descriptor.
	 *
	 * Accepts a string (treated as a kind name) or an array with any combination of
	 * `kind` / `url` / `method` / `params` / `headers`. Returns null when the input cannot be
	 * resolved — e.g. an unregistered kind without an explicit URL.
	 *
	 * @since 2.12.0
	 *
	 * @param mixed $input The raw declaration.
	 *
	 * @return Endpoint|null The descriptor, or null when unresolvable.
	 */
	public static function normalize( $input ): ?Endpoint {
		if ( is_string( $input ) ) {
			$input = [ 'kind' => $input ];
		}

		if ( ! is_array( $input ) ) {
			return null;
		}

		$url = isset( $input['url'] ) ? (string) $input['url'] : '';
		if ( '' === $url && isset( $input['kind'] ) ) {
			$url = (string) ( self::resolve( (string) $input['kind'] ) ?? '' );
		}

		if ( '' === $url ) {
			return null;
		}

		$method  = isset( $input['method'] ) ? (string) $input['method'] : 'GET';
		$params  = isset( $input['params'] ) && is_array( $input['params'] ) ? $input['params'] : [];
		$headers = isset( $input['headers'] ) && is_array( $input['headers'] ) ? $input['headers'] : [];

		return new Endpoint( $url, $method, $params, $headers );
	}

	/**
	 * Walks a field-filter array and normalizes the `endpoint` key on every entry into the
	 * descriptor shape consumed by the UI. Unresolvable endpoints are stripped so the field
	 * falls back to its static `values`/`<select>` renderer instead of breaking.
	 *
	 * @since 2.12.0
	 *
	 * @param array $filters The raw field filters.
	 *
	 * @return array The fields with endpoint declarations normalized.
	 */
	public static function apply_to_field_filters( array $filters ): array {
		$threshold = self::auto_threshold();

		foreach ( $filters as $index => $field ) {
			$filters[ $index ] = self::auto_switch_field( $field, $threshold );

			if ( ! isset( $filters[ $index ]['endpoint'] ) ) {
				continue;
			}

			$endpoint = self::normalize( $filters[ $index ]['endpoint'] );
			if ( null === $endpoint ) {
				unset( $filters[ $index ]['endpoint'] );
				continue;
			}

			$filters[ $index ]['endpoint'] = $endpoint->to_array();
		}

		return array_values( $filters );
	}

	/**
	 * Resolves the auto-switch threshold, allowing consumers to override it via filter hook.
	 *
	 * @since 2.12.0
	 *
	 * @return int The threshold (minimum 1).
	 */
	public static function auto_threshold(): int {
		/**
		 * Filter the maximum number of items a field's static `values` list may hold before its
		 * `auto_endpoint` hint takes over and swaps the list for an async endpoint.
		 *
		 * @since 2.12.0
		 *
		 * @param int $threshold The auto-switch threshold.
		 */
		$value = (int) apply_filters( 'gk/query-filters/auto-endpoint-threshold', self::DEFAULT_AUTO_THRESHOLD );

		if ( $value < 1 ) {
			return 1;
		}

		return min( $value, self::MAX_AUTO_THRESHOLD );
	}

	/**
	 * Applies the auto-switch rule to a single field.
	 *
	 * @since 2.12.0
	 *
	 * @param array $field     The raw field filter.
	 * @param int   $threshold The auto-switch threshold.
	 *
	 * @return array The possibly-rewritten field.
	 */
	private static function auto_switch_field( array $field, int $threshold ): array {
		$kind = $field['auto_endpoint'] ?? null;
		unset( $field['auto_endpoint'] );

		if ( ! is_string( $kind ) || '' === $kind ) {
			return $field;
		}

		if ( isset( $field['endpoint'] ) ) {
			return $field;
		}

		$count = self::resolve_count( $kind, $field, $threshold + 1 );
		if ( null === $count ) {
			return $field;
		}

		$field['control'] = 'combobox';

		if ( $count <= $threshold ) {
			return $field;
		}

		$field['endpoint'] = [
			'kind'   => $kind,
			'params' => [
				'form_id'  => (int) ( $field['form_id'] ?? 0 ),
				'field_id' => (string) ( $field['key'] ?? '' ),
			],
		];
		$field = self::keep_pinned_values( $field );

		return $field;
	}

	/**
	 * Drops a field's static values once an endpoint serves them, retaining only options flagged
	 * `pinned` — special sentinels (e.g. created-by current-user options) the endpoint cannot
	 * produce. The `values` key is removed entirely when nothing is pinned.
	 *
	 * @since 2.14.0
	 *
	 * @param array $field The field whose values are now backed by an endpoint.
	 *
	 * @return array The field with only its pinned values, or none.
	 */
	private static function keep_pinned_values( array $field ): array {
		$pinned = array_values( array_filter(
			(array) ( $field['values'] ?? [] ),
			static fn( $value ): bool => is_array( $value ) && ! empty( $value['pinned'] )
		) );

		if ( $pinned === [] ) {
			unset( $field['values'] );

			return $field;
		}

		$field['values'] = $pinned;

		return $field;
	}

	/**
	 * Resolves the count for a kind via its registered counter, passing the field and threshold.
	 * Returns null when no counter is registered.
	 *
	 * @since 2.12.0
	 * @since 2.14.0 Added the `$field` and `$threshold` parameters.
	 *
	 * @param string   $kind    The endpoint identifier.
	 * @param array    $field   The field filter, passed to the counter.
	 * @param int|null $threshold The threshold, passed to the counter.
	 *
	 * @return int|null The count, or null when the kind has no counter.
	 */
	public static function resolve_count( string $kind, array $field = [], ?int $threshold = null ): ?int {
		if ( ! isset( self::$counters[ $kind ] ) ) {
			return null;
		}

		return (int) ( self::$counters[ $kind ] )( $field, $threshold );
	}

	/**
	 * Clears all registered endpoints. Intended for use in tests.
	 *
	 * @since 2.12.0
	 *
	 * @internal
	 */
	public static function reset(): void {
		self::$endpoints = [];
		self::$counters  = [];
	}
}
