<?php

namespace GravityKit\GravityView\QueryFilters\Aggregate;

use GF_Query;
use GF_Query_Call;
use GF_Query_Column;
use GF_Query_Condition;
use GF_Query_Literal;
use GFCommon;
use GFFormsModel;
use GravityKit\GravityView\QueryFilters\Condition\Resolves_Owner_Entry;
use GravityKit\GravityView\QueryFilters\Util\QueryHelper;
use InvalidArgumentException;
use RGCurrency;

/**
 * Represents a Query Object that can perform aggregate functions on specific fields.
 *
 * @since 2.4.0
 */
final class Query {
	use Resolves_Owner_Entry;

	/**
	 * The operation types.
	 *
	 * @since 2.4.0
	 */
	public const OPERATION_SUM   = 'SUM';
	public const OPERATION_COUNT = 'COUNT';
	public const OPERATION_AVG   = 'AVG';
	public const OPERATION_MIN   = 'MIN';
	public const OPERATION_MAX   = 'MAX';
	public const OPERATION_ALL   = 'ALL';

	/**
	 * The alias holding the value being aggregated once per owning entry.
	 *
	 * @since 2.16.0
	 */
	private const VALUE_ALIAS = 'gk_aggregate_value';

	/**
	 * The alias of the subquery reducing rows to the values their entries hold.
	 *
	 * @since 2.16.0
	 */
	private const SUBQUERY_ALIAS = 'gk_aggregate';

	/**
	 * The base query.
	 *
	 * @since 2.4.0
	 *
	 * @var GF_Query
	 */
	private $query;

	/**
	 * The currency.
	 *
	 * @since 2.4.0
	 *
	 * @var null|string
	 */
	private $currency = null;

	/**
	 * Whether a table alias is defined by the base query, keyed by alias.
	 *
	 * @since 2.16.0
	 *
	 * @var array<string, bool>
	 */
	private $exposed_sources = [];

	/**
	 * The fields to group by.
	 *
	 * @since 2.4.0
	 *
	 * @var Field[]
	 */
	private $group_by = [];

	/**
	 * The fields to order by.
	 *
	 * @since 2.4.0
	 *
	 * @var string[]
	 */
	private $order_by = [];

	/**
	 * The limit.
	 *
	 * @since 2.4.0
	 *
	 * @var int|null
	 */
	private $limit;

	/**
	 * Microcache for database information.
	 *
	 * @since 2.4.0
	 *
	 * @var string|null
	 */
	private static $db_version;

	/**
	 * Micro cache for timezone support on MySQL.
	 *
	 * @since 2.4.0
	 *
	 * @var bool|null
	 */
	private static $supports_timezones;

	/**
	 * The recorded timezone (GMT by default).
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	private $timezone = 'GMT';

	/**
	 * The total amount of digits on an aggregate value.
	 *
	 * @since 2.4.0
	 *
	 * @var int
	 */
	private $precision_digits = 20;

	/**
	 * The number of decimals on an aggregate value.
	 *
	 * @since 2.4.0
	 *
	 * @var int
	 */
	private $precision_decimals = 4;

	/**
	 * Creates an aggregate query based on a Gravity Forms Query.
	 *
	 * @since 2.4.0
	 *
	 * @param GF_Query $query The base query.
	 */
	private function __construct( GF_Query $query ) {
		$this->query = $query;
	}

	/**
	 * Deep clones the GF_Query object.
	 *
	 * @since 2.4.0
	 */
	public function __clone() {
		$this->query = clone $this->query;
	}

	/**
	 * Creates an instance from a Gravity Forms Query.
	 *
	 * @since 2.4.0
	 *
	 * @param GF_Query $query The base query.
	 *
	 * @return self
	 */
	public static function from( GF_Query $query ): self {
		return new self( $query );
	}

	/**
	 * Returns an instance with a specific currency.
	 *
	 * @since 2.4.0
	 *
	 * @param string|null $currency The currency.
	 *
	 * @return self
	 */
	public function with_currency( ?string $currency ): self {
		$clone           = clone $this;
		$clone->currency = $currency;

		return $clone;
	}

	/**
	 * Returns an instance with a specific timezone.
	 *
	 * @since 2.4.0
	 *
	 * @param string|null $timezone The timezone.
	 *
	 * @return self
	 */
	public function with_timezone( ?string $timezone ): self {
		$clone           = clone $this;
		$clone->timezone = $timezone ?? 'GMT';

		return $clone;
	}

	/**
	 * Returns an instance with a set limit.
	 *
	 * @since 2.4.0
	 *
	 * @param int|null $limit THe limit.
	 *
	 * @return self
	 */
	public function with_limit( ?int $limit ): self {
		$clone        = clone $this;
		$clone->limit = $limit;

		return $clone;
	}

	/**
	 * Returns an instance with a set precision.
	 *
	 * If digits = 4, and decimals = 2, a valid value would be `11.22`.
	 *
	 * @since 2.4.0
	 *
	 * @param int|null $decimals The number of decimals.
	 * @param int|null $digits   The total number of digits.
	 *
	 * @return self
	 */
	public function with_precision( ?int $decimals = null, ?int $digits = null ): self {
		$clone = clone $this;

		$clone->precision_digits   = max( 0, min( $digits ?? $clone->precision_digits, 30 ) );
		$clone->precision_decimals = max( 0, min( $decimals ?? $clone->precision_decimals, $clone->precision_digits ) );

		return $clone;
	}

	/**
	 * Returns an instance with the group by fields.
	 *
	 * @since 2.4.0
	 *
	 * @param Field ...$fields The fields to group by.
	 *
	 * @return self
	 */
	public function group_by( Field ...$fields ): self {
		if ( [] === $fields ) {
			throw new InvalidArgumentException( sprintf( '"%s()" requires at least one field object.', __METHOD__ ) );
		}

		$clone           = clone $this;
		$clone->group_by = array_unique( $fields, SORT_REGULAR );

		return $clone;
	}

	/**
	 * Returns an instance with the ORDER BY fields.
	 *
	 * @since 2.4.0
	 *
	 * @param string ...$order_by The order by strings.
	 *
	 * @return self
	 */
	public function order_by( string ...$order_by ): self {
		$clone           = clone $this;
		$clone->order_by = $order_by;

		return $clone;
	}

	/**
	 * Returns an array of the count grouped by the provided group fields.
	 *
	 * Counts every matching row, with no field to hold a value for.
	 *
	 * @since 2.4.0
	 *
	 * @return array{count: int}.
	 */
	public function count(): array {
		return $this->process( self::OPERATION_COUNT );
	}

	/**
	 * Returns an array of the `MAX` of the provided field.
	 *
	 * @since 2.4.0
	 *
	 * @param Field $field The field to perform the operation on.
	 *
	 * @return array
	 */
	public function max( Field $field ): array {
		return $this->process( self::OPERATION_MAX, $field );
	}

	/**
	 * Returns an array of the `MIN` of the provided field.
	 *
	 * @since 2.4.0
	 *
	 * @param Field $field The field to perform the operation on.
	 *
	 * @return array
	 */
	public function min( Field $field ): array {
		return $this->process( self::OPERATION_MIN, $field );
	}

	/**
	 * Returns an array of the `AVG` of the provided field.
	 *
	 * @since 2.4.0
	 *
	 * @param Field $field The field to perform the operation on.
	 *
	 * @return array The average, alongside the `count` of rows it averaged.
	 */
	public function avg( Field $field ): array {
		return $this->process( self::OPERATION_AVG, $field );
	}

	/**
	 * Returns an array of the `SUM` of the provided field.
	 *
	 * @since 2.4.0
	 *
	 * @param Field $field The field to perform the operation on.
	 *
	 * @return array
	 */
	public function sum( Field $field ): array {
		return $this->process( self::OPERATION_SUM, $field );
	}

	/**
	 * Returns an array of all the aggregate operations on the provided field.
	 *
	 * Every value is scoped to the field, the `count` included: it reports the rows holding a value
	 * for it, not the rows that matched. Expect it to be lower than {@see self::count()} whenever
	 * some matching row leaves the field empty, and read the two as answers to different questions.
	 *
	 * @since 2.4.0
	 *
	 * @param Field $field The field to perform the operation on.
	 *
	 * @return array The aggregates, each covering the rows that hold a value for the field.
	 */
	public function all( Field $field ): array {
		return $this->process( self::OPERATION_ALL, $field );
	}

	/**
	 * Returns the recorded timezone for this query.
	 *
	 * @since 2.4.0
	 *
	 * @return string The timezone.
	 */
	public function get_timezone(): string {
		return $this->timezone;
	}

	/**
	 * Process the entries and return the calculated data.
	 *
	 * @since 2.4.0
	 *
	 * @return array The calculated data.
	 */
	public function process( string $operation, ?Field $field = null ): array {
		$operation = strtoupper( $operation );

		$this->guard_against_invalid_types(
			$operation,
			[
				self::OPERATION_SUM,
				self::OPERATION_AVG,
				self::OPERATION_COUNT,
				self::OPERATION_MAX,
				self::OPERATION_MIN,
				self::OPERATION_ALL,
			],
			'Summary request operation can only be one of "%s". "%s" provided.'
		);

		$base                  = $this->query;
		$this->query           = clone $base;
		$this->exposed_sources = [];

		try {
			return $this->run( $operation, $field );
		} finally {
			$this->query           = $base;
			$this->exposed_sources = [];
		}
	}

	/**
	 * Builds and runs the aggregate against a throwaway copy of the query.
	 *
	 * Reaching every row means rewriting the `where` and dropping the limit, and the joins Gravity
	 * Forms infers from a rewritten `where` are not the ones it inferred before. Both belong to the
	 * copy {@see self::process()} installs, never to the query a caller handed over.
	 *
	 * @since 2.16.0
	 *
	 * @param string     $operation The aggregate operation.
	 * @param Field|null $field     The field to aggregate, when the operation needs one.
	 *
	 * @return array The calculated data.
	 */
	private function run( string $operation, ?Field $field ): array {
		$query = $this->query;

		$is_count = $operation === self::OPERATION_COUNT;
		if ( ! $is_count && ! $field ) {
			return [];
		}

		$parts = $query->_introspect();
		$query->limit( 0 ); // Retrieve all results.

		// Add inferred joins to preform Aggregate functions on.
		$conditions   = [ $parts['where'] ];
		$where_fields = $this->group_by;
		if ( ! $is_count && ! in_array( $field, $where_fields, true ) ) {
			$where_fields[] = $field;
		}

		$extra_conditions = [];
		foreach ( array_unique( $where_fields, SORT_REGULAR ) as $where_field ) {
			$extra_conditions[] = $this->get_conditions_for_field( $where_field );
		}

		$conditions = array_merge( $conditions, ...$extra_conditions );
		$query->where( GF_Query_Condition::_and( ...$conditions ) );

		$sql = $this->get_sql_from_query( $query );
		if ( ! $sql ) {
			return [];
		}

		$sql['group'] = $this->get_group_by_field_statement();
		$sql['order'] = $this->get_order_by_statement();
		$sql['join']  = $this->get_join_statement( $sql['join'] ?? '', $where_fields );

		$tables       = trim( ( $sql['from'] ?? '' ) . ' ' . ( $sql['join'] ?? '' ) );
		$entry_select = $sql['select'] ?? '';
		$entry_select = is_array( $entry_select ) ? implode( ' ', $entry_select ) : (string) $entry_select;

		$fans_out = count( $this->get_row_identity_columns( $tables, $entry_select ) ) > 1
			|| $this->joins_unkeyed_entry_meta( $tables );

		$deduplicated = $fans_out && $field
			? array_values(
				array_intersect(
					self::OPERATION_ALL === $operation
						? [ self::OPERATION_MIN, self::OPERATION_MAX, self::OPERATION_AVG, self::OPERATION_SUM ]
						: [ $operation ],
					[ self::OPERATION_MIN, self::OPERATION_MAX, self::OPERATION_AVG, self::OPERATION_SUM ]
				)
			)
			: [];

		if ( [] !== $deduplicated ) {
			$statement = $this->get_deduplicated_statement( $deduplicated, $field, $sql );
		} else {
			$sql['select'] = $this->get_select_statement( $operation, $field, $tables, $entry_select );

			if ( is_int( $this->limit ) ) {
				$sql['limit'] = sprintf( 'LIMIT %d', $this->limit );
			}

			$statement = implode( ' ', $sql );
		}

		global $wpdb;
		$result = $wpdb->get_results( $statement, ARRAY_A );

		$result = $this->maybe_process_json_values( $result, $this->group_by );

		if ( self::OPERATION_ALL === $operation && [] !== $deduplicated ) {
			$result = $this->with_row_counts( $result );
		}

		return array_values( $result );
	}

	/**
	 * Adds the row count to rows whose aggregates were read per owning entry.
	 *
	 * Reading every aggregate at once still reports how many rows matched, which the deduplicated
	 * rows cannot answer: they hold one row per value held, not per result.
	 *
	 * @since 2.16.0
	 *
	 * @param array<int, array<string, mixed>> $rows The aggregated rows.
	 *
	 * @return array<int, array<string, mixed>> The rows, each carrying its count.
	 */
	private function with_row_counts( array $rows ): array {
		$counts = $this->process( self::OPERATION_COUNT );

		if ( [] === $this->group_by ) {
			foreach ( $rows as $index => $row ) {
				$rows[ $index ]['count'] = $counts[0]['count'] ?? '0';
			}

			return $rows;
		}

		$by_group = [];
		foreach ( $counts as $row ) {
			$by_group[ $this->get_group_key( $row ) ] = $row['count'] ?? '0';
		}

		foreach ( $rows as $index => $row ) {
			$rows[ $index ]['count'] = $by_group[ $this->get_group_key( $row ) ] ?? '0';
		}

		return $rows;
	}

	/**
	 * Returns the key identifying which group a row belongs to.
	 *
	 * @since 2.16.0
	 *
	 * @param array<string, mixed> $row The row.
	 *
	 * @return string The key.
	 */
	private function get_group_key( array $row ): string {
		$key = [];
		foreach ( $this->group_by as $group_by_field ) {
			$key[] = (string) ( $row[ $group_by_field->alias() ] ?? '' );
		}

		return implode( "\0", $key );
	}

	/**
	 * Returns any additional {@see GF_Query_Condition} needed for the field.
	 *
	 * A joined form read through entry meta gets its `meta_key` pinned explicitly: the query planner
	 * rewrites such a join's correlation and drops the key from it, leaving the alias spanning every
	 * meta row of the joined entry.
	 *
	 * @since 2.4.0
	 *
	 * @param Field $field The field object.
	 *
	 * @return GF_Query_Condition[] The required conditions.
	 */
	private function get_conditions_for_field( Field $field ): array {
		$column = $field->as_column();
		if ( $column->is_entry_column() ) {
			return [];
		}

		$conditions = [];

		if ( ! $this->is_joined_source( $column->source ) ) {
			$conditions[] = new GF_Query_Condition( $column );

			$alias = $this->query->_alias( $column->field_id, $column->source, 'm' );
			if ( ! $this->is_primary_source( $column->source ) && $this->join_lacks_meta_key( $alias ) ) {
				$meta_key = new GF_Query_Column( 'meta_key', $column->source, $alias );

				$conditions[] = GF_Query_Condition::_or(
					new GF_Query_Condition( $meta_key, GF_Query_Condition::EQ, new GF_Query_Literal( (string) $column->field_id ) ),
					new GF_Query_Condition( $meta_key, GF_Query_Condition::LIKE, new GF_Query_Literal( $column->field_id . '.%' ) )
				);
			}
		}
		// Hack to make sure the column is not NULL. GF_Query_Literal would escape the SQL as a string.
		$conditions[] = new GF_Query_Condition(
			new GF_Query_Call( '', [ $field->get_sql_column( $this ) ] ),
			GF_Query_Condition::ISNOT,
			GF_Query_Condition::NULL
		);

		if ( $field->is_json() && $this->supports_json_table() ) {
			$conditions[] = new GF_Query_Condition(
				new GF_Query_Call( 'JSON_VALID', [ $field->get_sql_column( $this ) ] )
			);
		}

		return $conditions;
	}

	/**
	 * Retrieves a copy of the SQL query for the provided query.
	 *
	 * @since 2.4.0
	 *
	 * @param GF_Query $query The query object.
	 *
	 * @return array|null The SQL parts.
	 */
	private function get_sql_from_query( GF_Query $query ): array {
		$sql = QueryHelper::get_sql_from_query( $query );

		unset ( $sql['paginate'], $sql['order'] );

		return $sql;
	}

	/**
	 * Retrieves the DISTINCT values for the provided {@see Field}.
	 *
	 * @since 2.4.0
	 *
	 * @param Field $field The field object.
	 *
	 * @return array The values keyed by the field alias.
	 */
	public function distinct( Field $field ): array {
		$query = $this->query;
		$parts = $query->_introspect();
		$query->limit( 0 ); // Retrieve all results.

		// Add inferred joins to preform Aggregate functions on.
		$conditions = [ $parts['where'] ];
		$conditions = array_merge( $conditions, $this->get_conditions_for_field( $field ) );
		$query->where( GF_Query_Condition::_and( ...$conditions ) );

		$sql           = $this->get_sql_from_query( $this->query );
		$sql['select'] = sprintf( 'SELECT DISTINCT %s', $this->get_select_by_field( $field ) );
		$sql['order']  = $this->get_order_by_statement();

		global $wpdb;

		$result = $wpdb->get_results( implode( ' ', $sql ), ARRAY_A );
		$result = $this->maybe_process_json_values( $result, [ $field ] );

		return $result;
	}

	/**
	 * Returns the currency object.
	 *
	 * @since 2.4.0
	 *
	 * @return array The currency object.
	 */
	public function get_currency(): array {
		$currency = $this->currency ?? GFCommon::get_currency();

		return RGCurrency::get_currency( $currency );
	}

	/**
	 * Returns the `GROUP BY` statement for the query.
	 *
	 * @since 2.4.0
	 *
	 * @return string The SQL.
	 */
	private function get_group_by_field_statement(): string {
		if ( ! $this->group_by ) {
			return '';
		}

		$ids = array_map(
			function ( Field $field ): string {
				return $this->get_group_expression( $field );
			},
			$this->group_by
		);

		return 'GROUP BY ' . implode( ', ', $ids );
	}

	/**
	 * Returns the `SELECT` statement for the query.
	 *
	 * @since 2.4.0
	 *
	 * @return string The SQL.
	 */
	private function get_select_statement( string $operation, ?Field $field, string $tables = '', string $entry_select = '' ): string {
		$select = [];

		foreach ( $this->group_by as $group_by_field ) {
			$select[] = $this->get_select_by_field( $group_by_field );
		}

		$operations = $operation === self::OPERATION_ALL
			? [
				self::OPERATION_MIN,
				self::OPERATION_MAX,
				self::OPERATION_AVG,
				self::OPERATION_SUM,
				self::OPERATION_COUNT,
			]
			: [ $operation ];

		if ( $operation === self::OPERATION_AVG ) {
			$operations = [ $operation, self::OPERATION_COUNT ];
		}

		foreach ( $operations as $o ) {
			$is_count   = $o === self::OPERATION_COUNT || is_null( $field );
			$result_sql = $this->get_count_expression( $tables, $entry_select );
			if ( ! $is_count ) {
				$result_sql = sprintf(
					'ROUND(%s(CAST(%s AS DECIMAL(%4$d, %3$d))), %3$d)',
					strtoupper( $o ),
					$field->get_sql( $this ),
					$this->precision_decimals,
					$this->precision_digits
				);
			}

			$select[] = sprintf( '%s as `%s`', $result_sql, strtolower( $o ) );
		}

		return 'SELECT ' . implode( ', ', $select );
	}

	/**
	 * Returns the statement aggregating a field once per entry that owns a value.
	 *
	 * A join yields a row per matched pair, so a field of the form being selected from repeats across
	 * those rows. Summing them adds a value the entry holds once as many times as it matched, and
	 * averaging them weights each entry by how often it matched. Reducing the rows to the distinct
	 * values held, per group, before aggregating leaves both reading the entries rather than the rows.
	 *
	 * Grouping is part of that grain: an entry contributes to every group it appears in, but once to
	 * each.
	 *
	 * @since 2.16.0
	 *
	 * @param string[]              $operations The aggregate operations.
	 * @param Field                 $field      The field being aggregated.
	 * @param array<string, string> $sql        The statement parts of the entry query.
	 *
	 * @return string The SQL.
	 */
	private function get_deduplicated_statement( array $operations, Field $field, array $sql ): string {
		$columns = [];
		foreach ( $this->group_by as $group_by_field ) {
			$columns[] = $this->get_select_by_field( $group_by_field );
		}

		$source     = (int) $field->as_column()->source;
		$columns[] = $this->owner_entry_sql( $this->query, $source, $this->exposes_source_table( $source ) );
		$columns[] = sprintf( '%s as `%s`', $field->get_sql( $this ), self::VALUE_ALIAS );

		$sql['select'] = 'SELECT DISTINCT ' . implode( ', ', $columns );
		unset( $sql['group'], $sql['order'], $sql['limit'] );

		$outer = [];
		foreach ( $this->group_by as $group_by_field ) {
			$outer[] = sprintf( '`%s`', $group_by_field->alias() );
		}

		foreach ( $operations as $operation ) {
			$outer[] = sprintf(
				'ROUND(%s(CAST(`%s` AS DECIMAL(%4$d, %3$d))), %3$d) as `%5$s`',
				$operation,
				self::VALUE_ALIAS,
				$this->precision_decimals,
				$this->precision_digits,
				strtolower( $operation )
			);
		}

		if ( [ self::OPERATION_AVG ] === $operations ) {
			$outer[] = 'COUNT(*) as `count`';
		}

		$statement = sprintf(
			'SELECT %s FROM (%s) as `%s`',
			implode( ', ', $outer ),
			implode( ' ', array_filter( $sql ) ),
			self::SUBQUERY_ALIAS
		);

		if ( [] !== $this->group_by ) {
			$statement .= sprintf(
				' GROUP BY %s',
				implode(
					', ',
					array_map(
						static fn( Field $group_by_field ): string => sprintf( '`%s`', $group_by_field->alias() ),
						$this->group_by
					)
				)
			);
		}

		$order = $this->get_order_by_statement();
		if ( $order ) {
			$statement .= ' ' . $order;
		}

		if ( is_int( $this->limit ) ) {
			$statement .= sprintf( ' LIMIT %d', $this->limit );
		}

		return $statement;
	}

	/**
	 * Returns the expression counting matched rows.
	 *
	 * A row is one entry, or one entry per joined entry it matches: a join produces a row per pair by
	 * design, and the count reports what the query returns. Meta rows are not rows in that sense, so a
	 * multi-input field storing several values for one entry must not multiply it.
	 *
	 * @since 2.16.0
	 *
	 * @param string $tables       The `FROM` and `JOIN` clauses the query would run.
	 * @param string $entry_select The `SELECT` the query would run.
	 *
	 * @return string The SQL.
	 */
	private function get_count_expression( string $tables, string $entry_select ): string {
		$columns = $this->get_row_identity_columns( $tables, $entry_select );
		if ( [] === $columns ) {
			return 'COUNT(*)';
		}

		$first = array_shift( $columns );
		$rest  = array_map(
			static fn( string $column ): string => sprintf( 'COALESCE(%s, 0)', $column ),
			$columns
		);

		return sprintf( 'COUNT(DISTINCT %s)', implode( ', ', array_merge( [ $first ], $rest ) ) );
	}

	/**
	 * Whether the query joins entry meta without narrowing it to a key.
	 *
	 * A join naming a `meta_key` contributes the rows holding that key. One that leaves the key open
	 * contributes every meta row an entry holds, which is what a search across all fields asks for, so
	 * an entry returns as many rows as it holds matching values.
	 *
	 * @since 2.16.0
	 *
	 * @param string $tables The `FROM` and `JOIN` clauses the query would run.
	 *
	 * @return bool Whether entry meta is joined without a key.
	 */
	private function joins_unkeyed_entry_meta( string $tables ): bool {
		$join    = '(?:(?:LEFT|RIGHT|INNER|OUTER|CROSS|FULL)\s+)*JOIN';
		$pattern = sprintf(
			'/^%s\s+`?%s`?[\s`]/i',
			$join,
			preg_quote( GFFormsModel::get_entry_meta_table_name(), '/' )
		);

		foreach ( preg_split( sprintf( '/\s+(?=%s\s)/i', $join ), $tables ) as $clause ) {
			if ( preg_match( $pattern, $clause ) && false === stripos( $clause, 'meta_key' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the columns that together identify one result row.
	 *
	 * Two places declare that identity and neither is complete on its own. A source selected from or
	 * joined as a table appears in `FROM`/`JOIN` under a `t` alias. A source joined through meta has
	 * no table, and is identified by the meta row the query selects for it, which shows up in the
	 * query's own `SELECT` and nowhere else.
	 *
	 * @since 2.16.0
	 *
	 * @param string $tables       The `FROM` and `JOIN` clauses the query would run.
	 * @param string $entry_select The `SELECT` the query would run.
	 *
	 * @return string[] The identity columns, the source selected from first.
	 */
	private function get_row_identity_columns( string $tables, string $entry_select ): array {
		$columns = [];

		if ( preg_match_all( '/\bAS\s+`(t\d+)`/i', $tables, $matches ) ) {
			foreach ( array_unique( $matches[1] ) as $alias ) {
				$columns[] = sprintf( '`%s`.`id`', $alias );
			}
		}

		if ( preg_match_all( '/`(m\d+)`\.`entry_id`/', $entry_select, $matches ) ) {
			foreach ( array_unique( $matches[1] ) as $alias ) {
				$columns[] = sprintf( '`%s`.`entry_id`', $alias );
			}
		}

		return array_values( array_unique( $columns ) );
	}

	/**
	 * Returns the SELECT statement for a specific {@see Field}.
	 *
	 * @since 2.4.0
	 *
	 * @param Field $field The field object.
	 *
	 * @return string The SQL.
	 */
	private function get_select_by_field( Field $field ): string {
		return sprintf(
			'%s as `%s`',
			$this->get_group_expression( $field ),
			$field->alias()
		);
	}

	/**
	 * Returns the SQL expression for a {@see Field} used in both SELECT and GROUP BY.
	 *
	 * Grouping by this expression rather than the SELECT alias avoids ambiguity when a joined
	 * subquery exposes a column whose name matches the alias.
	 *
	 * @since 2.16.0
	 *
	 * @param Field $field The field object.
	 *
	 * @return string The SQL.
	 */
	private function get_group_expression( Field $field ): string {
		if ( $field->is_json() && $this->supports_json_table() ) {
			return sprintf( '`%s`.`val`', $field->alias() . '_jt' );
		}

		return $field->get_sql( $this, true );
	}

	/**
	 * Returns the SQL for the column on the current query.
	 *
	 * @since 2.4.0
	 *
	 * @param GF_Query_Column $column       The column object.
	 * @param string          $table_column The entry-meta column to read. Default `meta_value`.
	 *
	 * @return string The SQL.
	 */
	public function get_value_column_sql( GF_Query_Column $column, string $table_column = 'meta_value' ): string {
		if ( $column->is_entry_column() ) {
			return $column->sql( $this->query );
		}

		if ( $this->is_joined_source( $column->source ) ) {
			return sprintf( '`%s`.`%s`', $this->query->_alias( '', $column->source, 't' ), $column->field_id );
		}

		return sprintf( "`%s`.`%s`", $this->query->_alias( $column->field_id, $column->source ?: 0, 'm' ), $table_column );
	}

	/**
	 * Returns whether the query's join for a meta alias carries no `meta_key`.
	 *
	 * A join the query does not define yet will be inferred while the SQL renders, and for a joined
	 * source the planner drops the key from it, so an absent join counts as unkeyed.
	 *
	 * @since 2.16.0
	 *
	 * @param string $alias The meta-table alias.
	 *
	 * @return bool Whether the join leaves the alias unkeyed.
	 */
	private function join_lacks_meta_key( string $alias ): bool {
		$sql  = QueryHelper::get_sql_from_query( $this->query );
		$join = ( $sql['from'] ?? '' ) . ' ' . ( $sql['join'] ?? '' );

		if ( ! preg_match( sprintf( '/AS\s*`%s`\s*ON\s*(\([^)]*\)|\S+(?:\s*=\s*\S+)?)/', preg_quote( $alias, '/' ) ), $join, $matches ) ) {
			return true;
		}

		return false === strpos( $matches[1], 'meta_key' );
	}

	/**
	 * Returns whether the source is one the query selects from.
	 *
	 * @since 2.16.0
	 *
	 * @param int|string|null $source The column source (form ID).
	 *
	 * @return bool Whether the source is selected from directly.
	 */
	private function is_primary_source( $source ): bool {
		$from = $this->query->_introspect()['from'] ?? [];

		return is_array( $from ) && in_array( (int) $source, array_map( 'intval', $from ), true );
	}

	/**
	 * Returns whether the column source is read from a derived table of its own.
	 *
	 * A joined form is only sometimes exposed as a derived-table alias (`t2`, `t3`, …) carrying its
	 * fields as columns, in which case its values are read as `t2`.`field_id`. Other joins reach the
	 * form through the entry-meta table instead and never define that alias, so its values are read
	 * through a meta-table alias like any other source.
	 *
	 * @since 2.16.0
	 *
	 * @param int|string|null $source The column source (form ID).
	 *
	 * @return bool Whether the source has a derived table exposing its fields.
	 */
	private function is_joined_source( $source ): bool {
		if ( empty( $source ) ) {
			return false;
		}

		$from = $this->query->_introspect()['from'] ?? [];
		if ( ! is_array( $from ) ) {
			return false;
		}

		if ( in_array( (int) $source, array_map( 'intval', $from ), true ) ) {
			return false;
		}

		return $this->exposes_source_table( $source );
	}

	/**
	 * Returns whether the base query defines a derived table for the source.
	 *
	 * The alias must close a subquery (`) AS `tN``): an alias on a plain entry table also exists in
	 * the join SQL, but carries no field columns.
	 *
	 * @since 2.16.0
	 *
	 * @param int|string $source The column source (form ID).
	 *
	 * @return bool Whether the source has a derived table.
	 */
	private function exposes_source_table( $source ): bool {
		$alias = $this->query->_alias( '', $source, 't' );

		if ( ! isset( $this->exposed_sources[ $alias ] ) ) {
			$sql = QueryHelper::get_sql_from_query( $this->query );

			$this->exposed_sources[ $alias ] = (bool) preg_match(
				sprintf( '/\)\s*AS\s*`%s`/', preg_quote( $alias, '/' ) ),
				( $sql['from'] ?? '' ) . ' ' . ( $sql['join'] ?? '' )
			);
		}

		return $this->exposed_sources[ $alias ];
	}

	/**
	 * Helper method to validate a type against a predefined set of valid types.
	 *
	 * @since 2.4.0
	 *
	 * @param string $type        The type to validate.
	 * @param array  $valid_types The valid types.
	 * @param string $message     The message to throw when the type is invalid.
	 *
	 * @throws \InvalidArgumentException If the type is in valid.
	 */
	private function guard_against_invalid_types( string $type, array $valid_types, string $message ): void {
		if ( ! in_array( $type, $valid_types, true ) ) {
			throw new \InvalidArgumentException(
				sprintf( esc_html( $message ), implode( ', ', $valid_types ), $type )
			);
		}
	}

	/**
	 * Returns the ORDER BY statement for the SQL.
	 *
	 * @since 2.4.0
	 *
	 * @return string The SQL.
	 */
	private function get_order_by_statement(): string {
		$order_by = $this->get_effective_order_by();
		if ( ! $order_by ) {
			return '';
		}

		return 'ORDER BY ' . implode( ', ', $order_by );
	}

	/**
	 * Returns the ORDER BY fields with the group values appended as tie-breakers.
	 *
	 * Rows that tie on the requested order would otherwise come back in an engine-defined order.
	 *
	 * @since 2.16.0
	 *
	 * @return string[] The order by strings.
	 */
	private function get_effective_order_by(): array {
		$order_by = $this->order_by;
		if ( ! $order_by ) {
			return [];
		}

		$ordered = implode( ', ', $order_by );
		foreach ( $this->group_by as $field ) {
			if ( false !== strpos( $ordered, sprintf( '`%s`', $field->alias() ) ) ) {
				continue;
			}

			$order_by[] = $field->asc();
		}

		return $order_by;
	}

	/**
	 * If the field value is stores as JSON, and the database does not support JSON_TABLE, this  method will aggregate
	 * the values correctly for every value in the json blob.
	 *
	 * @since 2.4.0
	 *
	 * @param array $result The aggregate query results.
	 *
	 * @return array THe updated query results.
	 */
	private function maybe_process_json_values( array $result, array $fields ): array {
		if ( $this->supports_json_table() ) {
			return $result;
		}

		if ( ! $result ) {
			return $result;
		}

		foreach ( $fields as $group_by_field ) {
			if ( ! $group_by_field->is_json() ) {
				continue;
			}

			$tally = [];
			foreach ( $result as $row ) {
				$value = $row[ $group_by_field->alias() ];

				if ( is_string( $value ) ) {
					$possible_json = json_decode( $value, true );
					if ( is_array( $possible_json ) ) {
						$value = $possible_json;
					}
				}

				if ( ! is_array( $value ) ) {
					continue; // Invalid value.
				}

				foreach ( $value as $sub ) {
					$row[ $group_by_field->alias() ] = $sub;
					if ( ! isset( $tally[ $sub ] ) ) {
						$tally[ $sub ] = [];
					}
					$tally[ $sub ][] = $row;
				}
			}

			$result = [];
			foreach ( $tally as $rows ) {
				$row          = $rows[0];
				$count_result = null;
				foreach (
					[
						self::OPERATION_COUNT,
						self::OPERATION_SUM,
						self::OPERATION_AVG,
						self::OPERATION_MAX,
						self::OPERATION_MIN,
					] as $operation
				) {
					$opp = strtolower( $operation );
					if ( ! isset( $row[ $opp ] ) ) {
						continue;
					}

					$values = array_column( $rows, $opp );

					if ( in_array( $operation, [ self::OPERATION_COUNT, self::OPERATION_SUM ], true ) ) {
						$value = array_sum( $values );
					}

					if ( self::OPERATION_COUNT === $operation ) {
						$count_result = $value;
					}

					if ( self::OPERATION_AVG === $operation && isset( $value ) && $count_result > 0 ) {
						$value /= ( $count_result ?: 1 );
					}

					if ( 'min' === $opp ) {
						$value = min( $values );
					}

					if ( 'max' === $opp ) {
						$value = max( $values );
					}

					if ( ! isset( $value ) ) {
						continue;
					}

					$row[ $opp ] = $opp === 'count' ? (string) $value : sprintf( '%.04f', ( $value ?? 0 ) );
				}
				$result[] = $row;
			}
		}

		$order = [];
		foreach ( $this->get_effective_order_by() as $order_by ) {
			if ( ! preg_match( '/`(.+)` (ASC|DESC)/is', $order_by, $matches ) ) {
				continue;
			}

			if ( ! array_key_exists( $matches[1], $result[0] ?? [] ) ) {
				continue;
			}

			$order[ $matches[1] ] = 'ASC' === strtoupper( $matches[2] );
		}

		if ( [] !== $order ) {
			usort( $result, static function ( array $a, array $b ) use ( $order ): int {
				foreach ( $order as $column => $is_ascending ) {
					$comparison = $is_ascending
						? $a[ $column ] <=> $b[ $column ]
						: $b[ $column ] <=> $a[ $column ];

					if ( 0 !== $comparison ) {
						return $comparison;
					}
				}

				return 0;
			} );
		}

		return $result;
	}

	/**
	 * Returns whether the current database supports `JSON_TABLE` functions.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	private function supports_json_table(): bool {
		if ( self::is_sqlite_db() ) {
			return false;
		}

		return version_compare(
			self::db_version(),
			self::is_maria_db() ? '10.6.0' : '8.0.0',
			'>='
		);
	}

	/**
	 * Returns whether the database supports timezones.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public static function supports_timezones(): bool {
		if ( isset( self::$supports_timezones ) ) {
			return self::$supports_timezones;
		}

		if ( self::is_sqlite_db() ) {
			return self::$supports_timezones ??= false;
		}

		global $wpdb;
		self::$supports_timezones = '1' === $wpdb->get_var( "SELECT CONVERT_TZ('2025-01-01 12:00:00', 'GMT', 'America/New_York') = '2025-01-01 07:00:00';" );

		return self::$supports_timezones;
	}

	/**
	 * Returns the database version information.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	private static function get_db_version_info(): string {
		global $wpdb;
		if ( ! isset( self::$db_version ) ) {
			self::$db_version = $wpdb->db_server_info();
		}

		return self::$db_version;
	}

	/**
	 * Returns whether the current database is MariaDB.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	private static function is_maria_db(): bool {
		return stripos( self::get_db_version_info(), 'mariadb' ) !== false;
	}

	/**
	 * Returns whether the current database is SQLite.
	 *
	 * @since 2.8.1
	 *
	 * @return bool
	 */
	private static function is_sqlite_db(): bool {
		return defined( 'DB_ENGINE' ) && 'sqlite' === DB_ENGINE;
	}

	/**
	 * Returns the current database version.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	private static function db_version(): string {
		return preg_replace( '/[^0-9.].*/', '', self::get_db_version_info() );
	}

	/**
	 * Upgrades the JOIN statements to use a JSON_TABLE when available.
	 *
	 * @since 2.4.0
	 *
	 * @param string  $join         The original join.
	 * @param Field[] $where_fields The possible fields to add `JOIN_TABLE` for.
	 *
	 * @return string The updated `JOIN` statement.
	 */
	private function get_join_statement( string $join, array $where_fields ): string {
		if ( ! $this->supports_json_table() ) {
			return $join;
		}

		$joins = [ $join ];
		foreach ( $where_fields as $field ) {
			if ( ! $field->is_json() ) {
				continue;
			}

			$joins[] = sprintf(
				'LEFT JOIN JSON_TABLE(%s, "$[*]" COLUMNS(`val` VARCHAR(200) PATH "$")) as `%s` ON TRUE',
				$field->get_sql_column( $this ),
				$field->alias() . '_jt'
			);
		}

		return implode( ' ', $joins );
	}
}
