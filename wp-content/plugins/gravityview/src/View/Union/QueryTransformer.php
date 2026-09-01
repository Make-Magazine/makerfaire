<?php
/**
 * Transforms a View query into a UNION across its configured forms.
 *
 * @since       3.2.0
 * @license     GPL2+
 * @package     GravityKit\GravityView\View\Union
 */

namespace GravityKit\GravityView\View\Union;

use GF_Field;
use GF_Query;
use GF_Query_Call;
use GF_Query_Column;
use GF_Query_Condition;
use GF_Query_Literal;
use GFFormsModel;
use GravityKit\GravityView\Field\Field;
use GravityKit\GravityView\GravityForms\QueryExtensions\RewritableCall;
use GravityKit\GravityView\QueryFilters\Util\QueryHelper;
use GravityKit\GravityView\Request\Request;
use GravityKit\GravityView\View\View;

/**
 * Rewrites a View's primary query into a `UNION ALL` across every unioned form.
 *
 * @since 3.2.0
 */
final class QueryTransformer {
	/**
	 * The View whose unions drive the transformation.
	 *
	 * @since 3.2.0
	 *
	 * @var View
	 */
	private $view;

	/**
	 * Captured SQL parts for each unioned form's subquery.
	 *
	 * @since 3.2.0
	 *
	 * @var array[]
	 */
	private $subquery_sql = [];

	/**
	 * @since 3.2.0
	 *
	 * @param View $view The View whose unions drive the transformation.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Registers the UNION transformation on the View's primary query.
	 *
	 * @since 3.2.0
	 *
	 * @param GF_Query     $primary_query The View's primary-form query.
	 * @param Request|null $request       The request rendering the View.
	 */
	public function apply( GF_Query $primary_query, ?Request $request = null ): void {
		$query_parameters = $primary_query->_introspect();

		foreach ( $this->view->unions as $form_id => $field_map ) {
			$this->capture_form_subquery( (int) $form_id, $field_map, $query_parameters, $request );
		}

		add_filter( 'gform_gf_query_sql', [ $this, 'assemble_union_sql' ] );
	}

	/**
	 * Detaches the UNION assembly filter once the primary query has run.
	 *
	 * @since 3.2.0
	 */
	public function detach(): void {
		remove_filter( 'gform_gf_query_sql', [ $this, 'assemble_union_sql' ] );
	}

	/**
	 * Builds a unioned form's subquery and captures its SQL.
	 *
	 * @since 3.2.0
	 *
	 * @param int          $form_id          The unioned form id.
	 * @param Field[]      $field_map        Primary field id to unioned field map.
	 * @param array        $query_parameters The introspected primary query parts.
	 * @param Request|null $request          The request rendering the View.
	 */
	private function capture_form_subquery(
		int $form_id,
		array $field_map,
		array $query_parameters,
		?Request $request
	): void {
		$query_class = $this->view->get_query_class();

		/** @var GF_Query $subquery */
		$subquery = new $query_class( $form_id );

		$subquery->where( $this->substitute_where( $query_parameters['where'], $field_map, $form_id ) );

		$this->apply_order( $subquery, $query_parameters['order'], $field_map, $form_id );

		do_action_ref_array( 'gravityview/view/query', [ &$subquery, $this->view, $request ] );

		$subquery->where( $this->substitute_where( $subquery->_introspect()['where'], $field_map, $form_id ) );

		$this->subquery_sql[] = $this->to_union_fragment( QueryHelper::get_sql_from_query( $subquery ) );
	}

	/**
	 * Copies the primary order clauses onto a subquery, remapping field columns.
	 *
	 * @since 3.2.0
	 *
	 * @param GF_Query $subquery      The unioned form's subquery.
	 * @param array    $order_clauses The introspected order clauses.
	 * @param Field[]  $field_map     Primary field id to unioned field map.
	 * @param int      $form_id       The unioned form id.
	 */
	private function apply_order( GF_Query $subquery, array $order_clauses, array $field_map, int $form_id ): void {
		( function (): void {
			$this->order = [];
		} )->call( $subquery );

		foreach ( $order_clauses as $order_clause ) {
			[ $column, $direction ] = $order_clause;

			$column = $this->substitute_column( $column, $field_map, $form_id );

			if ( ! $column instanceof GF_Query_Column && ! $column instanceof GF_Query_Call ) {
				continue;
			}

			$subquery->order( $column, $direction );
		}
	}

	/**
	 * Remaps a primary-form WHERE condition onto a unioned form.
	 *
	 * @since 3.2.0
	 *
	 * @param GF_Query_Condition $condition The primary-form condition.
	 * @param Field[]            $field_map Primary field id to unioned field map.
	 * @param int                $form_id   The unioned form id.
	 *
	 * @return GF_Query_Condition
	 */
	private function substitute_where(
		GF_Query_Condition $condition,
		array $field_map,
		int $form_id
	): GF_Query_Condition {
		if ( $condition->expressions ) {
			$expressions = [];

			foreach ( $condition->expressions as $expression ) {
				$expressions[] = $this->substitute_where( $expression, $field_map, $form_id );
			}

			return call_user_func_array(
				[ GF_Query_Condition::class, 'AND' === $condition->operator ? '_and' : '_or' ],
				$expressions
			);
		}

		$left = $this->substitute_column( $condition->left, $field_map, $form_id );

		if ( false === $left ) {
			return new GF_Query_Condition(
                new GF_Query_Literal( 1 ),
				GF_Query_Condition::EQ,
                new GF_Query_Literal( 2 )
            );
		}

		if ( $left === $condition->left ) {
			return $condition;
		}

		return new GF_Query_Condition( $left, $condition->operator, $condition->right );
	}

	/**
	 * Remaps a primary-form operand onto a unioned form, or false when it has no counterpart.
	 *
	 * @since 3.2.0
	 *
	 * @param GF_Query_Column|GF_Query_Call|mixed $operand   The primary-form operand.
	 * @param Field[]                             $field_map Primary field id to unioned field map.
	 * @param int                                 $form_id   The unioned form id.
	 *
	 * @return GF_Query_Column|GF_Query_Call|mixed|false
	 */
	private function substitute_column( $operand, array $field_map, int $form_id ) {
		if ( $operand instanceof GF_Query_Call ) {
			$parameters = [];

			foreach ( $operand->parameters as $parameter ) {
				$parameter = $this->substitute_column( $parameter, $field_map, $form_id );

				if ( false === $parameter ) {
					return false;
				}

				$parameters[] = $parameter;
			}

			// A subclass' custom sql() depends on its class; rebuild it as itself when it can.
			if ( $operand instanceof RewritableCall ) {
				return $operand->with_parameters( $parameters );
			}

			return new GF_Query_Call( $operand->function_name, $parameters );
		}

		if ( ! $operand instanceof GF_Query_Column ) {
			return $operand;
		}

		if ( $form_id === $operand->source ) {
			return $operand;
		}

		if ( $operand->is_entry_column() || $operand->is_meta_column() || ! is_numeric( $operand->field_id ) ) {
			return new GF_Query_Column( $operand->field_id, $form_id );
		}

		$mapped_field_id = $this->map_field_id( (string) $operand->field_id, $field_map );

		if ( null === $mapped_field_id ) {
			return false;
		}

		return new GF_Query_Column( $mapped_field_id, $form_id );
	}

	/**
	 * Resolves a primary-form field id to its counterpart on a unioned form.
	 *
	 * A composite field is configured as a whole but queried per input: sorting by a Name
	 * field resolves to `8.3` and `8.6`, which the parent's mapping must still cover. The
	 * suffix carries over only between fields of the same type, where the inputs line up.
	 *
	 * @since 3.2.0
	 *
	 * @param string  $field_id  The primary-form field id, possibly a sub-input.
	 * @param Field[] $field_map Primary field id to unioned field map.
	 *
	 * @return string|null The unioned form's field id, or null when there is no counterpart.
	 */
	private function map_field_id( string $field_id, array $field_map ): ?string {
		if ( isset( $field_map[ $field_id ] ) ) {
			return (string) $field_map[ $field_id ]->ID;
		}

		$separator = strpos( $field_id, '.' );

		if ( false === $separator ) {
			return null;
		}

		$parent_id = substr( $field_id, 0, $separator );
		$input_id  = substr( $field_id, $separator + 1 );
		$mapped    = $field_map[ $parent_id ] ?? null;

		if ( ! $mapped || ! $this->has_matching_type( $parent_id, $mapped ) ) {
			return null;
		}

		return $mapped->ID . '.' . $input_id;
	}

	/**
	 * Whether a primary-form field and its unioned counterpart are the same Gravity Forms type.
	 *
	 * @since 3.2.0
	 *
	 * @param string $field_id The primary-form field id.
	 * @param Field  $mapped   The unioned form's field.
	 *
	 * @return bool
	 */
	private function has_matching_type( string $field_id, Field $mapped ): bool {
		$form   = $this->view->form->form ?? null;
		$source = $form ? GFFormsModel::get_field( $form, $field_id ) : null;
		$target = $mapped->field ?? null;

		return $source instanceof GF_Field
			&& $target instanceof GF_Field
			&& $source->type === $target->type;
	}

	/**
	 * Formats a subquery's SQL parts into a UNION fragment.
	 *
	 * @since 3.2.0
	 *
	 * @param array $sql The Gravity Forms query SQL parts.
	 *
	 * @return array
	 */
	private function to_union_fragment( array $sql ): array {
		$select = 'UNION ALL ' . str_replace( 'SQL_CALC_FOUND_ROWS ', '', $sql['select'] );

		return [
			'select' => preg_replace( '#DISTINCT (.*)#', 'DISTINCT ', $select ),
			'from'   => $sql['from'],
			'join'   => $sql['join'],
			'where'  => $sql['where'],
			'order'  => $sql['order'] ?? '',
		];
	}

	/**
	 * Wraps the primary query and captured subqueries into a single UNION.
	 *
	 * @since 3.2.0
	 *
	 * @param array $sql The Gravity Forms query SQL parts.
	 *
	 * @return array The unioned SQL parts, or the parts as given when the entry id column cannot be located.
	 */
	public function assemble_union_sql( $sql ): array {
		$select = str_replace( 'SQL_CALC_FOUND_ROWS ', '', $sql['select'] ?? '' );

		if ( ! preg_match( '#DISTINCT (`[motc]\d+`.`.*?`)#', $select, $select_match ) ) {
			return $sql;
		}

		$id_column     = $select_match[1];
		$sql['select'] = preg_replace( '#DISTINCT (.*)#', 'DISTINCT ', $select );

		[ $table_alias, $id_alias ] = $this->split_column( $id_column );

		$main_order = $this->parse_order_terms( $sql['order'] ?? '' );

		$outer_order     = [];
		$order_positions = [];

		foreach ( $main_order as $position => [$expression, $direction] ) {
			if ( $expression === $id_column ) {
				$outer_order[] = trim( $id_column . ' ' . $direction );

				continue;
			}

			$alias                        = sprintf( '`union_order_%d`', $position );
			$order_positions[ $position ] = $alias;
			$outer_order[]                = trim( $alias . ' ' . $direction );
		}

		$sql['select'] .= $this->union_select_columns( $id_column, $id_alias, $main_order, $order_positions );

		$union_statements = [];

		foreach ( $this->subquery_sql as $fragment ) {
			$arm_order = $this->parse_order_terms( $fragment['order'] ?? '' );

			$arm_parts = [
				$fragment['select'] . $this->union_select_columns( $id_column, $id_alias, $arm_order, $order_positions ),
				$fragment['from'],
				$fragment['join'],
				$fragment['where'],
			];

			$union_statements[] = implode( ' ', $arm_parts );
		}

		return [
			'select'   => 'SELECT SQL_CALC_FOUND_ROWS ' . $id_column,
			'from'     => 'FROM (' . $sql['select'] . ' ' . ( $sql['from'] ?? '' ),
			'join'     => $sql['join'] ?? '',
			'where'    => $sql['where'] ?? '',
			'union'    => implode( ' ', $union_statements ) . ') AS ' . $table_alias,
			'order'    => $outer_order ? 'ORDER BY ' . implode( ', ', $outer_order ) : '',
			'paginate' => $sql['paginate'] ?? '',
		];
	}

	/**
	 * Splits a qualified column into its table alias and column name.
	 *
	 * @since 3.3.0
	 *
	 * @param string $column The backtick-quoted `table`.`column` reference.
	 *
	 * @return array{0: string, 1: string} The table alias and the column name, both quoted.
	 */
	private function split_column( string $column ): array {
		$parts = explode( '`.`', trim( $column, '`' ) );

		return [ '`' . ( $parts[0] ?? '' ) . '`', '`' . ( $parts[1] ?? '' ) . '`' ];
	}

	/**
	 * Builds the shared SELECT columns for a union arm: the entry id plus each order value.
	 *
	 * @since 3.2.0
	 *
	 * @param string   $id_column       The entry id column reference.
	 * @param string   $id_alias        The shared alias for the entry id.
	 * @param array    $order_terms     The arm's parsed order terms.
	 * @param string[] $order_positions Position to shared-alias map for non-id order columns.
	 *
	 * @return string
	 */
	private function union_select_columns( string $id_column, string $id_alias, array $order_terms, array $order_positions ): string {
		$columns = [ sprintf( '%s AS %s', $id_column, $id_alias ) ];

		foreach ( $order_positions as $position => $alias ) {
			$expression = $order_terms[ $position ][0] ?? $id_column;
			$columns[]  = sprintf( '%s AS %s', $expression, $alias );
		}

		return implode( ', ', $columns );
	}

	/**
	 * Splits an ORDER BY clause into expression/direction pairs, respecting nested parentheses.
	 *
	 * @since 3.2.0
	 *
	 * @param string $order The ORDER BY clause.
	 *
	 * @return array[] List of [ expression, direction ] pairs.
	 */
	private function parse_order_terms( string $order ): array {
		$order = trim( preg_replace( '/^\s*ORDER BY\s+/i', '', $order ) );

		if ( '' === $order ) {
			return [];
		}

		$terms  = [];
		$buffer = '';
		$depth  = 0;

		foreach ( str_split( $order ) as $character ) {
			if ( '(' === $character ) {
				++$depth;
			} elseif ( ')' === $character ) {
				--$depth;
			}

			if ( ',' === $character && 0 === $depth ) {
				$terms[] = $buffer;
				$buffer  = '';

				continue;
			}

			$buffer .= $character;
		}

		if ( '' !== trim( $buffer ) ) {
			$terms[] = $buffer;
		}

		return array_map(
			static function ( string $term ): array {
				$term = trim( $term );

				if ( preg_match( '/^(.*?)\s+(ASC|DESC)$/i', $term, $matches ) ) {
					return [ trim( $matches[1] ), strtoupper( $matches[2] ) ];
				}

				return [ $term, '' ];
			},
			$terms
		);
	}
}
