<?php

namespace GV\Search\Fields;

use GV\Search\Querying\Search_Filter;
use GV\View;
use GravityView_Deprecated_Hook_Notices;

/**
 * Represents a search field that searches all fields.
 *
 * @since 2.42
 *
 * @extends Search_Field
 */
final class Search_Field_All extends Search_Field {
	/**
	 * The state constant(s).
	 *
	 * @since $ver$
	 */
	private const STATE_PROCESSED = 'processed';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected string $icon = 'dashicons-admin-site-alt3';

	/**
	 * @inheritdoc
	 * @since 2.42
	 */
	protected static string $type = 'search_all';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected static string $field_type = 'search_all';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_name(): string {
		return esc_html__( 'Search Everything', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function get_description(): string {
		return esc_html__( 'Search across all entry fields', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_default_label(): string {
		return esc_html__( 'Search Entries:', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_options(): array {
		return [
			'placeholder' => [
				'type'     => 'text',
				'label'    => esc_html__( 'Placeholder text', 'gk-gravityview' ),
				'value'    => '',
				'class'    => 'widefat',
				'priority' => 1150,
			],
		];
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_input_name(): string {
		return 'gv_search';
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_input_type(): string {
		return 'search_all';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since $ver$
	 */
	public function adjust_filter( Search_Filter $filter, ?View $view = null ): Search_Filter {
		if ( $filter->context( self::STATE_PROCESSED, false ) ) {
			// Already processed.
			return $filter->with_key( null ); // Search Criteria has an empty key for "search_all".
		}

		$filter = parent::adjust_filter( $filter, $view );

		$value = $filter->value();
		if ( ! is_string( $value ) ) {
			return $filter;
		}

		$should_split_words = $this->should_split_words( $view );
		$has_json_storage   = $this->has_json_storage( $view );
		$criteria           = $this->get_criteria_from_query( $value, $should_split_words, $has_json_storage );

		if ( ! $criteria ) {
			// Remove filter.
			return $filter->with_value( null );
		}

		$form_ids = $this->get_form_ids( $filter, $view );
		$filter   = $filter->with_operator( $filter->operator(), [ 'contains', 'not contains', 'ncontains' ] );

		$optional = [];
		$required = [];
		$excluded = [];

		foreach ( $criteria as $criterion ) {
			$is_required = $criterion['required'] ?? false;
			$operator    = $criterion['operator'] ?? $filter->operator();
			$is_excluded = in_array( $operator, [ 'not contains', 'ncontains' ], true );

			$form_conditions = [];
			foreach ( $form_ids as $form_id ) {
				$form_conditions[] = $filter
					->with_form_id( $form_id )
					->with_operator( $operator )
					->with_value( $criterion['value'] ?? '' )
					->with_required( $is_required )
					->with_context( self::STATE_PROCESSED, true ) // Prevent circular execution.
					->as_normalized(); // Prevent normalizing values again, which would remove explicit white spaces.
			}

			// Each word should match in ANY form (OR across forms).
			$word_filter = 1 === count( $form_conditions )
				? $form_conditions[0]
				: Search_Filter::or( ...$form_conditions );

			if ( $is_required ) {
				$required[] = $word_filter;
			} elseif ( $is_excluded ) {
				$excluded[] = $word_filter;
			} else {
				$optional[] = $word_filter;
			}
		}

		$nested      = [];
		$search_mode = $filter->context( 'search_mode', Search_Filter::MODE_OR );

		// Todo: Once Query Filters implements this properly with the Global_Search_Condition, adjust to that filter.
		if ( $optional ) {
			// In AND mode, ALL words must match. In OR mode, ANY word can match.
			$nested[] = Search_Filter::MODE_AND === $search_mode
				? Search_Filter::and( ...$optional )
				: Search_Filter::or( ...$optional );
		}

		if ( $required ) {
			$nested[] = Search_Filter::and( ...$required );
		}

		if ( $excluded ) {
			$nested[] = Search_Filter::and( ...$excluded );
		}

		return Search_Filter::and( ...$nested );
	}

	/**
	 * Returns whether one of the fields is stored as JSON.
	 *
	 * @since $ver$
	 *
	 * @param View|null $view The View.
	 *
	 * @return bool Whether the View contains JSON storage.
	 */
	private function has_json_storage( ?View $view ): bool {
		if ( ! $view ) {
			return false;
		}

		$fields = $view->form->form['fields'] ?? [];
		foreach ( $fields as $field ) {
			if ( 'json' === ( $field['storageType'] ?? null ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns whether words should be split.
	 *
	 * @since $ver$
	 *
	 * @param View|null $view The View.
	 *
	 * @return bool Whether words should be split.
	 */
	private function should_split_words( ?View $view ): bool {
		/**
		 * @deprecated $ver$ Use `gk/gravityview/search/field/all/split-words`.
		 */
		$split_words = GravityView_Deprecated_Hook_Notices::apply_filters(
			'gravityview/search-all-split-words',
			[ true, $view ],
			'2.55',
			'gk/gravityview/search/field/all/split-words'
		);

		/**
		 * Search for each word separately or the whole phrase?
		 *
		 * @since $ver$
		 *
		 * @param bool      $split_words True: split a phrase into words; False: search whole word only. Default: true.
		 * @param View|null $view        The View being searched.
		 */
		return (bool) apply_filters( 'gk/gravityview/search/field/all/split-words', $split_words, $view );
	}

	/**
	 * Retrieves the words in with its operator for querying.
	 *
	 * @since 2.21.1
	 *
	 * @param string $query            The search query.
	 * @param bool   $split_words      Whether to split the words.
	 * @param bool   $has_json_storage Whether the form has JSON fields.
	 *
	 * @return array The search words with their operator.
	 */
	private function get_criteria_from_query( string $query, bool $split_words, bool $has_json_storage ): array {
		$words           = [];
		$quotation_marks = $this->get_quotation_marks();

		$regex = sprintf(
			'/(?<match>(\+|\-))?(%s)(?<word>.*?)(%s)/m',
			implode( '|', self::preg_quote( $quotation_marks['opening'] ?? [] ) ),
			implode( '|', self::preg_quote( $quotation_marks['closing'] ?? [] ) )
		);

		if ( preg_match_all( $regex, $query, $matches ) ) {
			$query = str_replace( $matches[0], '', $query );
			foreach ( $matches['word'] as $i => $value ) {
				$operator = '-' === $matches['match'][ $i ] ? 'not contains' : 'contains';
				$required = '+' === $matches['match'][ $i ];
				$words[]  = array_filter( compact( 'operator', 'value', 'required' ) );
			}
		}

		$values = [];
		if ( $query ) {
			$values = $split_words
				? preg_split( '/\s+/', $query )
				: [ preg_replace( '/\s+/', ' ', $query ) ];
		}

		foreach ( $values as $value ) {
			$is_exclude = '-' === ( $value[0] ?? '' );
			$required   = '+' === ( $value[0] ?? '' );
			$words[]    = array_filter(
				[
					'operator' => $is_exclude ? 'not contains' : 'contains',
					'value'    => ( $is_exclude || $required ) ? substr( $value, 1 ) : $value,
					'required' => $required,
				]
			);
		}

		// If one of the fields has a JSON storage, we add another criteria where the search value is escaped.
		if ( $has_json_storage ) {
			foreach ( $words as $i => $params ) {
				$original_value = $params['value'] ?? null;

				if ( ! is_string( $original_value ) ) {
					continue;
				}

				// This replicates the behavior of GF_Query_JSON_Literal::sql().
				$value = trim( wp_json_encode( $original_value ), '"' );
				$value = str_replace( '\\', '\\\\', $value );
				if ( $value !== $original_value ) {
					// We need to disable `required` on both the original and the copy, as both can't be true.
					unset( $words[ $i ]['required'], $params['required'] );

					$params['value'] = $value;
					$words[]         = $params;
				}
			}
		}

		// Filter out empty words.
		return array_filter( $words, static fn( array $word ) => ! empty( $word['value'] ?? '' ) );
	}

	/**
	 * Returns a list of quotation marks.
	 *
	 * @since 2.21.1
	 *
	 * @return array List of quotation marks with `opening` and `closing` keys.
	 */
	private function get_quotation_marks(): array {
		$quotations_marks = [
			'opening' => [ '"', "'", '“', '‘', '«', '‹', '「', '『', '【', '〖', '〝', '〟', '｢' ],
			'closing' => [ '"', "'", '”', '’', '»', '›', '」', '』', '】', '〗', '〞', '〟', '｣' ],
		];

		/**
		 * Modify the quotation marks used to detect quoted searches.
		 *
		 * @since  2.22
		 *
		 * @param array $quotations_marks List of quotation marks with `opening` and `closing` keys.
		 */
		$quotations_marks = apply_filters( 'gk/gravityview/common/quotation-marks', $quotations_marks );

		return $quotations_marks;
	}

	/**
	 * Quotes values for a regex.
	 *
	 * @since 2.21.1
	 *
	 * @param array[] $words     The words to quote.
	 * @param string  $delimiter The delimiter.
	 *
	 * @return array[] The quoted words.
	 */
	private static function preg_quote( array $words, string $delimiter = '/' ): array {
		return array_map(
			static function ( string $mark ) use ( $delimiter ): string {
				return preg_quote( $mark, $delimiter );
			},
			$words
		);
	}

	/**
	 * Returns the form IDs for the search filter / View.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The search filter.
	 * @param View|null     $view   The View.
	 *
	 * @return array<int|null> The form IDs.
	 */
	private function get_form_ids( Search_Filter $filter, ?View $view = null ): array {
		$form_ids = [ $filter->form_id() ];
		if ( ! $view ) {
			return $form_ids;
		}

		$joins = View::get_joins( $view->get_post() );
		foreach ( $joins as $join ) {
			if ( isset( $join->join_on->ID ) ) {
				$form_ids[] = (int) $join->join_on->ID;
			}
		}

		return $form_ids;
	}
}
