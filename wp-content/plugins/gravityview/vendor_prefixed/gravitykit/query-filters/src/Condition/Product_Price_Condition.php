<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\QueryFilters\Condition;

use GF_Query;
use GF_Query_Call;
use GF_Query_Column;
use GF_Query_Condition;
use GFCommon;
use RGCurrency;

/**
 * Represents a query condition that extracts the price portion from a product field value.
 *
 * Product fields store their value as "label|price" (e.g., "Product name|$10"). This condition
 * wraps an inner {@see GF_Query_Condition} and modifies the generated SQL to extract the numeric
 * price from after the pipe character, stripping any currency symbols for proper numeric comparison.
 *
 * @since 2.9.0
 */
final class Product_Price_Condition extends GF_Query_Condition {
	/**
	 * Require the "::wraps" notation.
	 *
	 * @since 2.9.0
	 */
	private function __construct() {
	}

	/**
	 * The inner condition being wrapped.
	 *
	 * @since 2.9.0
	 *
	 * @var GF_Query_Condition
	 */
	private $inner;

	/**
	 * Creates a new instance wrapping the provided condition.
	 *
	 * @since 2.9.0
	 *
	 * @param GF_Query_Condition $inner The condition to wrap.
	 *
	 * @return self The wrapped condition.
	 */
	public static function wraps( GF_Query_Condition $inner ): self {
		$condition        = new self();
		$condition->inner = $inner;

		return $condition;
	}

	/**
	 * Proxies property access to the inner condition.
	 *
	 * @since 2.9.0
	 *
	 * @param string $key The property name.
	 *
	 * @return mixed The property value.
	 */
	public function __get( $key ) {
		return $this->inner->__get( $key );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.9.0
	 */
	public function get_columns(): array {
		return $this->inner->get_columns();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.9.0
	 */
	public function sql( $query ) {
		$inner = clone $this->inner;

		// For completeness' sake, the value must be cast as a decimal.
		if ( ! $inner->left instanceof GF_Query_Call ) {
			$inner = new GF_Query_Condition(
				GF_Query_Call::CAST( clone $inner->left, GF_Query::TYPE_DECIMAL ),
				$inner->operator,
				clone $inner->right
			);
		}

		$left = $inner->left;

		// Unwrap all query calls.
		while ( $left instanceof GF_Query_Call ) {
			$left = $left->parameters[0];
		}

		if ( ! $left instanceof GF_Query_Column ) {
			return $inner->sql( $query );
		}

		// Need a copy, because ?? will always trigger `null` on $left->source.
		$source = $left->source;

		$column_sql = sprintf( "`%s`.`%s`", $query->_alias( $left->field_id, $source ?? 0, 'm' ), 'meta_value' );
		$replace    = sprintf( 'SUBSTR(%1$s, POSITION("|" IN %1$s) + 1)', $column_sql );

		$replace = $this->maybe_remove_currencies( $replace );

		return str_replace( $column_sql, $replace, $inner->sql( $query ) );
	}

	/**
	 * Adds SQL to remove any currency symbols from the recorded value.
	 *
	 * @since 2.9.0
	 *
	 * @param string $column_sql The SQL for column.
	 *
	 * @return string The updated SQL.
	 */
	private function maybe_remove_currencies( string $column_sql ): string {
		$currency           = RGCurrency::get_currency( GFCommon::get_currency() );
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
			if ( $key === '' ) {
				continue;
			}
			$column_sql = sprintf( 'REPLACE(%s, "%s", "%s")', $column_sql, $key, $value );
		}

		return $column_sql;
	}
}
