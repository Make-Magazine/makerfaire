<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 05-December-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Aggregate;

use GF_Field;
use GF_Query_Column;
use GFAPI;
use GFCommon;
use GFExport;

/**
 * Represents an Entry field to preform aggregate functions on.
 *
 * @since 2.4.0
 */
final class Field {
	/**
	 * The format options.
	 *
	 * @since 2.4.0
	 */
	public const FORMAT_NONE    = '';
	public const FORMAT_DATE    = 'DATE';
	public const FORMAT_YEAR    = 'YEAR';
	public const FORMAT_QUARTER = 'QUARTER';
	public const FORMAT_MONTH   = 'MONTH';
	public const FORMAT_WEEK    = 'WEEK';
	public const FORMAT_HOUR    = 'HOUR';

	/**
	 * Contains the wrapped GF_Field.
	 *
	 * @since 2.4.0
	 *
	 * @var GF_Field
	 */
	private $gf_field;

	/**
	 * The format for a date related field.
	 *
	 * @since 2.4.0
	 * @var string
	 */
	private $date_format = self::FORMAT_NONE;

	/**
	 * The provided field ID.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	private $field_id;

	/**
	 * The provided alias.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	private $alias;

	/**
	 * Creates the Field instance.
	 *
	 * @since 2.4.0
	 *
	 * @param GF_Field $gf_field The Gravity Forms Field.
	 */
	private function __construct( GF_Field $gf_field ) {
		$this->gf_field = $gf_field;
	}

	/**
	 * Returns a new instance from a provided Field.
	 *
	 * @since 2.4.0
	 *
	 * @param GF_Field $gf_field The Gravity Forms Field.
	 *
	 * @return self The field instance.
	 */
	public static function from_field( GF_Field $gf_field ): self {
		$gf_field->id       = (string) $gf_field->id;
		$instance           = new self( $gf_field );
		$instance->field_id = $gf_field->id;

		return $instance;
	}

	/**
	 * Returns an instance from a provided form and field ID.
	 *
	 * @since 2.4.0
	 *
	 * @param int    $form_id  The form ID.
	 * @param string $field_id The field ID.
	 *
	 * @return self|null The field instance.
	 */
	public static function from_ids( int $form_id, string $field_id ): ?self {
		$form = GFAPI::get_form( $form_id );
		if ( ! $form ) {
			return null;
		}

		if ( ! class_exists( 'GFExport' ) ) {
			require_once GFCommon::get_base_path() . '/export.php';
		}

		$form = GFExport::add_default_export_fields( $form );

		$gf_field = GFAPI::get_field( $form, $field_id );
		if ( ! $gf_field instanceof GF_Field ) {
			return null;
		}

		$field           = self::from_field( $gf_field );
		$field->field_id = (string) $field_id;

		return $field;
	}

	/**
	 * Returns the field as a query column.
	 *
	 * @since 2.4.0
	 *
	 * @return GF_Query_Column The query column.
	 */
	public function as_column(): GF_Query_Column {
		return new GF_Query_Column( $this->field_id, $this->gf_field->formId );
	}

	/**
	 * Returns an instance of the field with a formatted date.
	 *
	 * @since 2.4.0
	 *
	 * @return self
	 */
	public function as_date(): self {
		$clone              = clone $this;
		$clone->date_format = self::FORMAT_DATE;

		return $clone;
	}

	/**
	 * Returns an instance of the field with a formatted date.
	 *
	 * @since 2.4.0
	 *
	 * @return self
	 */
	public function as_year(): self {
		$clone              = clone $this;
		$clone->date_format = self::FORMAT_YEAR;

		return $clone;
	}

	/**
	 * Returns an instance of the field with a formatted date.
	 *
	 * @since 2.4.0
	 *
	 * @return self
	 */
	public function as_quarter(): self {
		$clone              = clone $this;
		$clone->date_format = self::FORMAT_QUARTER;

		return $clone;
	}

	/**
	 * Returns an instance of the field with a formatted date.
	 *
	 * @since 2.4.0
	 *
	 * @return self
	 */
	public function as_month(): self {
		$clone              = clone $this;
		$clone->date_format = self::FORMAT_MONTH;

		return $clone;
	}

	/**
	 * Returns an instance of the field with a formatted date.
	 *
	 * @since 2.4.0
	 *
	 * @return self
	 */
	public function as_week(): self {
		$clone              = clone $this;
		$clone->date_format = self::FORMAT_WEEK;

		return $clone;
	}

	/**
	 * Returns an instance of the field with a formatted date.
	 *
	 * @since 2.4.0
	 *
	 * @return self
	 */
	public function as_hour(): self {
		$clone              = clone $this;
		$clone->date_format = self::FORMAT_HOUR;

		return $clone;
	}

	/**
	 * Returns the name of the field on the query.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function alias(): string {
		if ( $this->alias ) {
			return $this->alias;
		}

		if ( $this->date_format === self::FORMAT_NONE ) {
			return $this->field_id;
		}

		return sprintf( '%s_%s', $this->field_id, strtolower( $this->date_format ) );
	}

	/**
	 * Returns the correctly aliased SQL needed for the provided column.
	 *
	 * @param Query $query The query object.
	 *
	 * @return string The SQL.
	 */
	public function get_sql( Query $query, bool $is_group_by = false ): string {
		$sql = $this->get_sql_column( $query );
		$sql = $this->maybe_get_product_value_or_price( $sql, $is_group_by );
		$sql = $this->maybe_remove_currencies( $sql, $query );
		$sql = $this->maybe_add_timezone_adjustment( $sql, $query );
		$sql = $this->maybe_add_date_formatting( $sql );

		return $sql;
	}

	/**
	 * Returns the column with the applied key reference (eg `m2`, m3`, etc.).
	 *
	 * @since 2.4.0
	 *
	 * @param Query $query The query object.
	 *
	 * @return string The column name for SQL.
	 */
	public function get_sql_column( Query $query ): string {
		return $query->get_value_column_sql( $this->as_column() );
	}

	/**
	 * Returns whether the field is a product field.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function is_product_field(): bool {
		if ( 'quantity' === $this->gf_field->type ) {
			return false;
		}

		if (
			'number' === $this->gf_field->get_input_type()
			&& 'currency' === ($this->gf_field->numberFormat ?? null)
		){
			return true;
		}

		return GFCommon::is_product_field( $this->gf_field->type ?? '' );
	}

	/**
	 * Whether this product is split up into multiple fields.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function is_multipart_product_field(): bool {
		return in_array( $this->gf_field->get_input_type(), [ 'singleproduct', 'hiddenproduct' ], true );
	}

	/**
	 * Adds SQL to remove any currency symbols from the recorded value.
	 *
	 * @since 2.4.0
	 *
	 * @param string $column_sql The SQL for column.
	 * @param Query  $query      The query object.
	 *
	 * @return string The updated SQL.
	 */
	private function maybe_remove_currencies( string $column_sql, Query $query ): string {
		if ( ! $this->is_multipart_product_field() ) {
			return $column_sql;
		}

		$currency           = $query->get_currency();
		$symbol_left        = html_entity_decode( rgar( $currency, 'symbol_left' ) );
		$symbol_right       = html_entity_decode( rgar( $currency, 'symbol_right' ) );
		$symbol_code        = rgar( $currency, 'code' );
		$thousand_separator = rgar( $currency, 'thousand_separator' );
		$decimal_separator  = rgar( $currency, 'decimal_separator' );

		$replacements = [ $symbol_left => '', $symbol_right => '', $symbol_code => '', $thousand_separator => '' ];
		if ( ',' === $decimal_separator ) {
			$replacements[','] = '.';
		}

		// Remove currency symbol and format properly for aggregate functions.
		foreach ( $replacements as $key => $value ) {
			$column_sql = sprintf( 'REPLACE(%s, "%s", "%s")', $column_sql, $key, $value );
		}

		return $column_sql;
	}

	/**
	 * Updates the SQL based on the formatting of the field.
	 *
	 * @since 2.4.0
	 *
	 * @param string $sql The current SQL.
	 *
	 * @return string The updated SQL.
	 */
	private function maybe_add_date_formatting( string $sql ): string {
		switch ( $this->date_format ) {
			case self::FORMAT_DATE:
			case self::FORMAT_YEAR:
			case self::FORMAT_MONTH:
			case self::FORMAT_QUARTER:
				return sprintf( '%s(%s)', $this->date_format, $sql );
			case self::FORMAT_WEEK:
				// Monday is first day, Week 1 is the first week with 4 days.
				return sprintf( '%s(%s, 3)', $this->date_format, $sql );
			case self::FORMAT_HOUR:
				return $this->format_hour( $sql );
			case self::FORMAT_NONE:
			default:
				return $sql;
		}
	}

	/**
	 * Adjusts the SQL if a CONVERT_TZ adjustment should be done.
	 *
	 * @since 2.4.0
	 *
	 * @param string $sql   The SQL.
	 * @param Query  $query The query object.
	 *
	 * @return string The adjusted SQL.
	 */
	private function maybe_add_timezone_adjustment( string $sql, Query $query ): string {
		if (
			! in_array( $this->gf_field->id, [ 'date_created', 'date_updated' ], true )
			|| $query->get_timezone() === 'GMT'
			|| ! Query::supports_timezones()
		) {
			return $sql;
		}

		return sprintf( 'CONVERT_TZ(%s, "GMT", "%s")', $sql, $query->get_timezone() );
	}

	/**
	 * Returns the SQL for a field that is formatted as an HOUR.
	 *
	 * @since 2.4.0
	 *
	 * @param string $sql The SQL.
	 *
	 * @return string The updated SQL.
	 */
	private function format_hour( string $sql ): string {
		if ( 'time' === $this->gf_field->type ) {
			$sql =
				// We check if there is an "M" in the value, representing AM or PM. Based on that we change the value parsing.
				strtr(
					'IF(INSTR(%sql%, "m"), STR_TO_DATE(%sql%, "%I:%i %p"), STR_TO_DATE(%sql%, "%H:%i"))',
					[ '%sql%' => $sql ]
				);
		}

		return sprintf( 'HOUR(%s)', $sql );
	}

	/**
	 * Returns a field instance with a specific alias.
	 *
	 * @since 2.4.0
	 *
	 * @param string $alias The alias.
	 *
	 * @return self
	 */
	public function with_alias( string $alias ): self {
		$clone        = clone $this;
		$clone->alias = esc_sql( str_replace( '`', '', $alias ) );

		return $clone;
	}

	/**
	 * Returns an ASC order string for this field.
	 *
	 * @since 2.4.0
	 *
	 * @return string The SQL.
	 */
	public function asc(): string {
		return sprintf( '`%s` ASC', $this->alias() );
	}

	/**
	 * Returns an DESC order string for this field.
	 *
	 * @since 2.4.0
	 *
	 * @return string The SQL.
	 */
	public function desc(): string {
		return sprintf( '`%s` DESC', $this->alias() );
	}

	/**
	 * Whether the fields values are stored as JSON.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function is_json(): bool {
		return 'multiselect' === $this->gf_field->get_input_type();
	}

	/**
	 * Updates the SQL Query to preform a `SUBSTR` on the value and extract either the value or the price.
	 *
	 * @since 2.4.0
	 *
	 * @param string $column_sql  The current SQL QUERY.
	 * @param bool   $is_group_by Whether the field is used by GROUP BY.
	 *
	 * @return string The updated SQL Query.
	 */
	private function maybe_get_product_value_or_price( string $column_sql, bool $is_group_by ): string {
		if (
			! $this->is_product_field()
			|| $this->is_multipart_product_field()
			|| in_array( $this->gf_field->type, [ 'quantity', 'total' ], true )
		) {
			return $column_sql;
		}

		// Remove everything in front of the pipe.
		return $is_group_by
			// Select value (everything before the `|` pipe).
			? sprintf( 'SUBSTR(%1$s, 1, POSITION("|" IN %1$s) - 1)', $column_sql )
			// Select price (everything after the `|` pipe).
			: sprintf( 'SUBSTR(%1$s, POSITION("|" IN %1$s) + 1)', $column_sql );
	}
}
