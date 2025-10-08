<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 25-September-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Aggregate;

use GF_Query;
use GF_Query_Call;
use GF_Query_Column;
use GF_Query_Condition;
use GFCommon;
use InvalidArgumentException;
use RGCurrency;

/**
 * Represents a Query Object that can perform aggregate functions on specific fields.
 *
 * @since 2.4.0
 */
final class Query {
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
	 * @param int|null $digits The total number of digits.
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
	 * @return array
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
	 * @since 2.4.0
	 *
	 * @param Field $field The field to perform the operation on.
	 *
	 * @return array
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

		$sql['select'] = $this->get_select_statement( $operation, $field );
		$sql['group']  = $this->get_group_by_field_statement();
		$sql['order']  = $this->get_order_by_statement();
		$sql['join']   = $this->get_join_statement( $sql['join'] ?? '', $where_fields );

		if ( is_int( $this->limit ) ) {
			$sql['limit'] = sprintf( 'LIMIT %d', $this->limit );
		}

		global $wpdb;
		$result = $wpdb->get_results( implode( ' ', $sql ), ARRAY_A );

		$result = $this->maybe_process_json_values( $result, $this->group_by );

		return array_values( $result );
	}

	/**
	 * Returns any additional {@see GF_Query_Condition} needed for the field.
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

		// Adds the column to the query with an inferred join.
		$conditions[] = new GF_Query_Condition( $column );
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
		$sql = [];

		add_filter(
			'gform_gf_query_sql',
			$select = function ( $original_sql ) use ( &$sql ) {
				// get a copy of the query;
				$sql = $original_sql;

				// Prevent the original query from running.
				return [];
			}
		);

		// Trigger SQL.
		$clone = clone $query; // Prevent any action on the original query.
		$clone->get();

		remove_filter( 'gform_gf_query_sql', $select );

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
			static function ( Field $field ): string {
				return sprintf( '`%s`', $field->alias() );
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
	private function get_select_statement( string $operation, ?Field $field ): string {
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
			$result_sql = 'COUNT(*)';
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
	 * Returns the SELECT statement for a specific {@see Field}.
	 *
	 * @since 2.4.0
	 *
	 * @param Field $field The field object.
	 *
	 * @return string The SQL.
	 */
	private function get_select_by_field( Field $field ): string {
		if ( $field->is_json() && $this->supports_json_table() ) {
			return sprintf(
				'`%s`.`val` as `%s`',
				$field->alias() . '_jt',
				$field->alias()
			);
		}

		return sprintf(
			'%s as `%s`',
			$field->get_sql( $this, true ),
			$field->alias()
		);
	}

	/**
	 * Returns the SQL for the column on the current query.
	 *
	 * @since 2.4.0
	 *
	 * @param GF_Query_Column $column The column object.
	 *
	 * @return string The SQL.
	 */
	public function get_value_column_sql( GF_Query_Column $column, string $table_column = 'meta_value' ): string {
		return $column->is_entry_column()
			? $column->sql( $this->query )
			: sprintf( "`%s`.`%s`", $this->query->_alias( $column->field_id, 0, 'm' ), $table_column );
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
		if ( ! $this->order_by ) {
			return '';
		}

		return 'ORDER BY ' . implode( ', ', $this->order_by );
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

		foreach ( $this->order_by as $order_by ) {
			if ( ! preg_match( '/`(.+)` (ASC|DESC)/is', $order_by, $matches ) ) {
				continue;
			}
			$column = $matches[1];

			if ( ! isset( $result[ $column ] ) ) {
				continue;
			}

			$is_ascending = strtoupper( $matches[2] ) === 'ASC';
			usort( $result, static function ( array $a, array $b ) use ( $column, $is_ascending ) {
				return $is_ascending
					? $a[ $column ] <=> $b[ $column ]
					: $b[ $column ] <=> $a[ $column ];
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
		if ( ! isset( self::$supports_timezones ) ) {
			global $wpdb;
			self::$supports_timezones = '1' === $wpdb->get_var( "SELECT CONVERT_TZ('2025-01-01 12:00:00', 'GMT', 'America/New_York') = '2025-01-01 07:00:00';" );
		}

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
