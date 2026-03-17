<?php

namespace GV\Search\Querying;

use GV\Admin_Request;
use GV\CLI_Request;
use GV\Mock_Request;
use GV\Request;

/**
 * Represents a search request, which can be created in multiple ways.
 *
 * @since $ver$
 */
final class Search_Request {
	/**
	 * Micro cache for the meta regex part.
	 *
	 * @since $ver$
	 *
	 * @var string
	 */
	private static string $meta_regex;

	/**
	 * Holds the search arguments.
	 *
	 * @since $ver$
	 *
	 * @var array
	 */
	private array $arguments = [];

	/**
	 * The search mode.
	 *
	 * @since $ver$
	 *
	 * @var string
	 */
	private string $mode = Search_Filter::MODE_OR;

	/**
	 * Creates the instance.
	 *
	 * @since $ver$
	 */
	private function __construct() {
	}

	/**
	 * Creates a search request from the legacy `$_REQUEST` structure.
	 *
	 * @param Request $request The request object.
	 *
	 * @return self
	 */
	public static function from_request( Request $request ): ?self {
		$request_arguments = self::get_request_arguments( $request );

		return self::from_arguments( $request_arguments );
	}

	/**
	 * Returns whether the request contains search request markers.
	 *
	 * @since $ver$
	 *
	 * @param Request $request The request object.
	 *
	 * @return bool Whether the request is a search request.
	 */
	public static function is_search_request( Request $request ): bool {
		if ( $request instanceof Admin_Request ) {
			return false;
		}

		$request_arguments = self::get_request_arguments( $request );
		$search_arguments  = self::get_search_arguments( $request_arguments );

		// Remove any filters that are considered empty.
		$search_arguments = array_filter(
			$search_arguments,
			static fn( array $filter ) => is_numeric( $filter['value'] ) || ! empty( $filter['value'] )
		);

		// We need at least ONE non-empty search argument to consider this a valid search.
		return [] !== $search_arguments;
	}

	/**
	 * Creates a search request object from arguments.
	 *
	 * @since $ver$
	 *
	 * @param array $arguments The request arguments.
	 *
	 * @return self|null The search request object.
	 */
	public static function from_arguments( array $arguments ): ?self {
		$search_arguments = self::get_search_arguments( $arguments );
		if ( [] === $search_arguments ) {
			return null;
		}

		$instance = new self();

		$instance->arguments = $search_arguments;
		$instance->set_mode( $arguments );

		return $instance;
	}

	/**
	 * Returns the provided arguments on the current request.
	 *
	 * @since $ver$
	 *
	 * @param Request $request The request object.
	 *
	 * @return array The arguments of this request.
	 */
	private static function get_request_arguments( Request $request ): array {
		if (
			$request instanceof CLI_Request
			|| $request instanceof Mock_Request
		) {
			$arguments = $request->get_arguments();
		} else {
			$search_method = self::method();

			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
			$arguments = 'post' === $search_method ? $_POST : $_GET;
		}

		// Safety measure.
		if ( ! is_array( $arguments ) ) {
			return [];
		}

		$arguments = stripslashes_deep( $arguments );
		if ( ! is_null( $arguments ) ) {
			$arguments = gv_map_deep( $arguments, 'rawurldecode' );
		}

		return $arguments;
	}

	/**
	 * Returns whether the request has a GV field key.
	 *
	 * @since 2.0.7
	 *
	 * @param array $arguments the request arguments.
	 *
	 * @return array The search arguments.
	 */
	private static function get_search_arguments( array $arguments ): array {
		$search_keys        = [ 'gv_search', 'gv_start', 'gv_end', 'gv_by', 'gv_id' ];
		$search_field_regex = self::get_search_field_regex();

		$search_arguments = [];
		foreach ( $arguments as $key => $value ) {
			// Ensure a string key.
			$key = (string) $key;

			if ( in_array( $key, $search_keys, true ) ) {
				$search_arguments[ $key ] = [ 'value' => $value ];
				continue;
			}

			if ( preg_match( $search_field_regex, $key ) ) {
				$search_arguments[ $key ] = [
					'value' => $value,
				];
			}
		}

		foreach ( $search_arguments as $key => $_ ) {
			if ( ! preg_match( $search_field_regex, $key ) ) {
				continue;
			}

			$operator                             = ( $arguments[ $key . '|op' ] ?? null );
			$search_arguments[ $key ]['operator'] = $operator;
		}

		return $search_arguments;
	}

	/**
	 * Calculates (and caches) the meta-fields regex part.
	 *
	 * @since $ver$
	 *
	 * @return string The regex part.
	 */
	private static function get_meta_field_regex(): string {
		if ( isset( self::$meta_regex ) ) {
			return self::$meta_regex;
		}

		$gv_fields = \GravityView_Fields::get_all();

		$meta = [];
		foreach ( $gv_fields as $field ) {
			if ( ! empty( $field->_gf_field_class_name ) ) {
				continue;
			}

			$meta[] = preg_quote( $field->name, '/' );
		}

		self::$meta_regex = implode( '|', $meta );

		return self::$meta_regex;
	}

	/**
	 * Returns the Search Field regular expression.
	 *
	 * @since $ver$
	 *
	 * @return string The regex.
	 */
	private static function get_search_field_regex(): string {
		$meta_regex = self::get_meta_field_regex();
		$regex      = '(?:filter|input)_(([0-9:_]+)';

		if ( $meta_regex ) {
			$regex .= '|(' . $meta_regex . ')';
		}

		// Close OR group.
		$regex .= ')';

		return '/^' . $regex . '$/sm';
	}

	/**
	 * Returns the request method.
	 *
	 * @since $ver$
	 *
	 * @return string
	 */
	public static function method(): string {
		/**
		 * @deprecated $ver$ Use `gk/gravityview/search/request/method`.
		 */
		$search_method = apply_filters_deprecated(
			'gravityview/search/method',
			[ 'get' ],
			'$ver$',
			'gk/gravityview/search/request/method'
		);

		/**
		 * Modifies the search request method.
		 *
		 * @since  $ver$
		 *
		 * @param string $method The request method. Default: 'get'.
		 */
		$search_method = strtolower( apply_filters( 'gk/gravityview/search/request/method', $search_method ) );

		return $search_method;
	}

	/**
	 * Convert the request to an array object.
	 *
	 * @since $ver$
	 *
	 * @return array<int, array{key: string, operator:string, value:mixed, request_key?: string}> The filters list.
	 */
	private function get_filters_data(): array {
		$filters = [];

		foreach ( $this->arguments as $key => $data ) {
			$operator = (string) ( $data['operator'] ?? '' );
			if ( in_array( $key, [ 'gv_start', 'gv_end' ], true ) ) {
				// We handle these further down the line as a single filter.
				continue;
			}

			if ( 'gv_search' === $key ) {
				$filters[] = [
					'key'         => 'search_all',
					'request_key' => $key,
					'operator'    => $operator ?: 'contains',
					'value'       => $data['value'],
				];
				continue;
			}

			if ( 'gv_id' === $key ) {
				$filters[] = [
					'key'         => 'entry_id',
					'request_key' => $key,
					'operator'    => '=',
					'value'       => absint( $data['value'] ?? 0 ),
				];
				continue;
			}

			if ( 'gv_by' === $key ) {
				$filters[] = [
					'key'         => 'created_by',
					'request_key' => $key,
					'operator'    => '=',
					'value'       => $data['value'] ?? '',
				];
				continue;
			}

			// Normalize key.
			$key = preg_replace( self::get_search_field_regex(), '\1', (string) $key );
			if ( preg_match( '/^[0-9_:]+$/m', $key ) ) {
				$key = str_replace( '_', '.', $key );
			}

			// Default, we keep the provided operator for now.
			$filter = [
				'key'      => $key,
				'operator' => $operator,
				'value'    => $data['value'] ?? '',
			];

			$filter_key = explode( ':', $key ); // When the key is provided as <field_id>:<form_id>.
			if ( count( $filter_key ) === 1 ) {
				$filter['field_id'] = $filter_key[0];
			} elseif ( count( $filter_key ) === 2 ) {
				$filter['field_id'] = $filter_key[0];
				$filter['form_id']  = $filter_key[1];
				$filter['key']      = $filter_key[0];
			}

			$filters[] = $filter;
		}

		if ( isset( $this->arguments['gv_start'] ) || isset( $this->arguments['gv_end'] ) ) {
			$date_filter = [
				'key'        => 'entry_date',
				'start_date' => $this->arguments['gv_start']['value'] ?? null,
				'end_date'   => $this->arguments['gv_end']['value'] ?? null,
			];

			// If only gv_start is provided (no gv_end parameter at all), it's a single-day (24 hours) search.
			if (
				! empty( $date_filter['start_date'] )
				&& empty( $date_filter['end_date'] )
				&& ! isset( $this->arguments['gv_end'] )
			) {
				// Single day search.
				$date_filter['type'] = 'day';
			}

			$filters[] = $date_filter;
		}

		return $filters;
	}

	/**
	 * Returns the Request as a Search Filter.
	 *
	 * @since $ver$
	 *
	 * @return Search_Filter The search filter.
	 */
	public function to_filter(): Search_Filter {
		return Search_Filter::group(
			$this->mode,
			...array_map(
				[ $this, 'create_filter' ],
				$this->get_filters_data(),
			)
		);
	}

	/**
	 * Returns the used search keys in this request.
	 *
	 * @since $ver$
	 *
	 * @param array $arguments Arguments to parse for search keys.
	 *
	 * @return array The search keys.
	 */
	public static function get_reserved_keys( array $arguments ): array {
		$keys = [
			'gv_search',
			'gv_start',
			'gv_end',
			'gv_id',
			'gv_by',
			'mode',
		];

		$request = self::from_arguments( $arguments );
		if ( ! $request ) {
			return $keys;
		}

		$keys = array_merge( $keys, array_keys( $request->arguments ) );

		return array_values( array_unique( $keys ) );
	}

	/**
	 * Creates a Search_Filter from a filter array.
	 *
	 * Handles special cases like date range filters that need to be converted to filter groups.
	 *
	 * @since $ver$
	 *
	 * @param array $filter The filter data.
	 *
	 * @return Search_Filter The search filter.
	 */
	private function create_filter( array $filter ): Search_Filter {
		// Handle date range filters specially.
		if ( 'entry_date' === ( $filter['key'] ?? '' ) ) {
			return $this->create_date_filter( $filter );
		}

		return Search_Filter::from_array( $filter );
	}

	/**
	 * Creates a Search_Filter for date range filters.
	 *
	 * @since $ver$
	 *
	 * @param array $filter The date filter data.
	 *
	 * @return Search_Filter The search filter.
	 */
	private function create_date_filter( array $filter ): Search_Filter {
		$start_date = $filter['start_date'] ?? null;
		$end_date   = $filter['end_date'] ?? null;

		// Both dates present: use "between" operator with array value.
		if ( $start_date && $end_date ) {
			return Search_Filter::create( 'entry_date', [ $start_date, $end_date ], 'between' );
		}

		// Only start date: single filter with >= operator.
		if ( $start_date ) {
			$operator = '>=';
			// If only gv_start is provided (no gv_end parameter at all), it's a single-day (24 hours) search.
			if (
				! empty( $start_date )
				&& empty( $end_date )
				&& ! isset( $this->arguments['gv_end'] )
			) {
				// Single day search.
				$operator = 'day';
			}

			return Search_Filter::create( 'entry_date', $start_date, $operator );
		}

		// Only end date: single filter with <= operator.
		return Search_Filter::create( 'entry_date', $end_date, '<=' );
	}

	/**
	 * Set the search mode.
	 *
	 * @since $ver$
	 *
	 * @param array $arguments The request arguments.
	 */
	private function set_mode( array $arguments ): void {
		$mode = Search_Filter::MODE_OR;
		if ( isset( $arguments['mode'] ) && is_string( $arguments['mode'] ) ) {
			$mode = strtolower( $arguments['mode'] );
		}

		// Support legacy 'all'/'any' terminology.
		$mapping = [
			'all' => Search_Filter::MODE_AND,
			'any' => Search_Filter::MODE_OR,
		];

		$mode = $mapping[ $mode ] ?? $mode;

		if ( ! in_array( $mode, [ Search_Filter::MODE_AND, Search_Filter::MODE_OR ], true ) ) {
			$mode = Search_Filter::MODE_OR;
		}

		$this->mode = $mode;
	}

	/**
	 * Returns the normalized mode.
	 *
	 * @since $ver$
	 *
	 * @return string The mode.
	 */
	public function mode(): string {
		return $this->mode;
	}
}
