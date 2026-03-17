<?php

namespace GV\Search\Querying\Visitors;

use GV\GF_Form;
use GV\Logger;
use GV\Search\Fields\Search_Field;
use GV\Search\Fields\Search_Field_Gravity_Forms;
use GV\Search\Querying\Search_Filter;
use GV\Search\Querying\Search_Filter_Visitor;
use GV\Search\Search_Field_Collection;
use GV\Search\Search_Policy;
use GV\View;

/**
 * Abstract base class for Search Filter visitors with shared functionality.
 *
 * @since $ver$
 */
abstract class Abstract_Search_Filter_Visitor implements Search_Filter_Visitor {
	/**
	 * The View.
	 *
	 * @since $ver$
	 *
	 * @var View|null
	 */
	protected ?View $view;

	/**
	 * The logger.
	 *
	 * @since $ver$
	 *
	 * @var Logger
	 */
	protected Logger $logger;

	/**
	 * Creates the visitor.
	 *
	 * @since $ver$
	 *
	 * @param View|null   $view   The View.
	 * @param Logger|null $logger The logger.
	 */
	public function __construct( ?View $view = null, ?Logger $logger = null ) {
		$this->view   = $view;
		$this->logger = $logger ?? gravityview()->log;
	}

	/**
	 * Returns whether the field ID is searchable.
	 *
	 * @since $ver$
	 *
	 * @param string   $field_id The field ID.
	 * @param int|null $form_id  The optional Form ID.
	 *
	 * @return bool Whether the field is searchable.
	 */
	protected function is_searchable_field( string $field_id, ?int $form_id = null ): bool {
		if ( ! $this->view ) {
			$this->logger->debug( 'Cannot check searchability: no View context.' );

			return false;
		}

		return Search_Policy::is_field_searchable( $this->view, $field_id, $form_id );
	}

	/**
	 * Returns whether empty field values should be ignored.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The search filter.
	 *
	 * @return bool Whether to ignore empty values.
	 */
	protected function should_ignore_empty_values( Search_Filter $filter ): bool {
		if ( $filter->is_normalized() ) {
			return false;
		}

		return Search_Policy::should_ignore_empty(
			$filter->key(),
			$this->view ? $this->view->ID ?? null : null,
			$filter->form_id()
		);
	}

	/**
	 * Resolves the form for a filter.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter.
	 *
	 * @return GF_Form|null The form, or null if not found.
	 */
	protected function resolve_form( Search_Filter $filter ): ?GF_Form {
		$form_id = $filter->form_id();

		$form = $form_id ? GF_Form::by_id( $form_id ) : null;
		if ( ! $form && $this->view ) {
			$form = $this->view->form ?? null;
		}

		return $form;
	}

	/**
	 * Finds the search field for a filter.
	 *
	 * @since $ver$
	 *
	 * @param int    $form_id The form ID.
	 * @param string $key     The field key.
	 *
	 * @return Search_Field|null The search field, or null if not found.
	 */
	protected function find_search_field( int $form_id, string $key ): ?Search_Field {
		$search_field = Search_Field_Collection::get_field_by_field_id( $form_id, $key );
		if ( ! $search_field ) {
			// Might be a Gravity Forms field.
			$gf_field_id  = Search_Field_Gravity_Forms::generate_field_id( $form_id, $key );
			$search_field = Search_Field_Collection::get_field_by_field_id( $form_id, $gf_field_id );
		}

		return $search_field;
	}

	/**
	 * Adjusts the filter through the search field.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter|null The adjusted filter, or null if it should be skipped.
	 */
	protected function adjust_filter( Search_Filter $filter ): ?Search_Filter {
		$key  = $filter->key();
		$form = $this->resolve_form( $filter );

		if ( ! $form ) {
			$this->logger->debug(
				'Cannot process filter: no form found for "{key}" on form {form_id}.',
				[
					'key'     => $key,
					'form_id' => (int) $filter->form_id(),
				]
			);

			return null;
		}

		if ( ! $filter->form_id() ) {
			$filter = $filter->with_form_id( $form->ID );
		}

		$search_field = $this->find_search_field( $form->ID, $key );
		if ( ! $search_field ) {
			$this->logger->debug(
				'Search field not found for "{key}" in form {form_id}.',
				[
					'key'     => $key,
					'form_id' => (int) $form->ID,
				]
			);

			return null;
		}

		// Add the current visitor as context.
		$filter = $filter->with_context( 'current_visitor', $this );
		// Allow the search field to adjust the filter.
		$filter = $search_field->adjust_filter( $filter, $this->view );

		// The original root filter does not have this group, so we actively test this new group.
		if ( $filter->is_group() ) {
			$filter->accept( $this );

			return null;
		}

		// Ignore if the value was removed.
		if ( ! $filter->has_value() && $this->should_ignore_empty_values( $filter ) ) {
			$this->logger->debug( 'Value was removed and ignored for "{key}".', [ 'key' => $filter->key() ] );

			return null;
		}

		return $filter;
	}

	/**
	 * Resolves the operator for a filter.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter.
	 *
	 * @return string The resolved operator.
	 */
	protected function resolve_operator( Search_Filter $filter ): string {
		$key         = $filter->key();
		$request_key = $filter->request_key() ?? $key;

		$operator = Search_Policy::resolve_operator(
			$filter->operator(),
			(string) $request_key,
			(string) $key,
			$filter->allowed_operators(),
			$filter->allowed_operators()[0] ?? '='
		);

		/**
		 * Modify the search operator for the field (contains, is, isnot, etc)
		 *
		 * @since  2.0 Added $view parameter
		 *
		 * @param string   $operator Existing search operator
		 * @param array    $filter   array with `key`, `value`, `operator`, `type` keys
		 * @param \GV\View $view     The View we're operating on.
		 */
		return apply_filters( 'gravityview_search_operator', $operator, $filter->to_array(), $this->view );
	}

	/**
	 * Resolves and clamps date range values from a search filter.
	 *
	 * Handles the 'day' operator, resolves dates, clamps against View settings,
	 * appends appropriate times, and adjusts for timezone if configured.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The entry_date search filter.
	 *
	 * @return array{0: \DateTimeImmutable|null, 1: \DateTimeImmutable|null} Start and end dates.
	 */
	protected function resolve_date_range( Search_Filter $filter ): array {
		// Handle a single day filter: when the operator is 'day'.
		if ( 'day' === $filter->operator() ) {
			$value = $filter->value();
			$date  = Search_Policy::resolve_date( $value );
			if ( ! $date ) {
				return [ null, null ];
			}

			$timestamp = strtotime( $date );
			if ( ! $timestamp ) {
				return [ null, null ];
			}

			//phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date -- Adjusted later on.
			$date_only = date( Search_Policy::get_date_php_format(), $timestamp );

			$filter = $filter
				->with_operator( 'between' )
				->with_value( [ $value, $date_only ] );
		}

		$adjust_tz = Search_Policy::should_adjust_timezone();

		$dates = [
			'start_date' => null,
			'end_date'   => null,
		];

		$value = $filter->value();
		if ( is_array( $value ) && 'between' === $filter->operator() ) {
			$dates['start_date'] = $value[0] ?? null;
			$dates['end_date']   = $value[1] ?? null;
		} elseif ( '>=' === $filter->operator() ) {
			$dates['start_date'] = $value;
		} elseif ( '<=' === $filter->operator() ) {
			$dates['end_date'] = $value;
		} elseif ( is_array( $value ) ) {
			return [ null, null ];
		}

		$result = [ null, null ];
		$keys   = [ 'start_date', 'end_date' ];

		foreach ( $keys as $index => $key ) {
			if ( null === $dates[ $key ] ) {
				continue;
			}

			$date = Search_Policy::resolve_date( $dates[ $key ] );
			if ( '' === $date ) {
				continue;
			}

			// Clamp against View settings if the provided date is outside the stored range.
			if ( $this->view ) {
				$stored_date    = $this->view->settings->get( $key );
				$date_timestamp = strtotime( $date );
				if (
					( $stored_date && $date_timestamp )
					&& (
						( 'start_date' === $key && $date_timestamp < strtotime( $stored_date ) )
						|| ( 'end_date' === $key && $date_timestamp > strtotime( $stored_date ) )
					)
				) {
					$this->logger->debug(
						'Entry date {key} constrained by View setting: "{date}".',
						[
							'key'  => $key,
							'date' => $stored_date,
						]
					);

					$date = $stored_date;
				}
			}

			// Only append time if the original date doesn't already have one.
			if ( ! str_contains( $date, ':' ) ) {
				$date .= ' ' . ( 'start_date' === $key ? '00:00:00' : '23:59:59' );
			}

			if ( $adjust_tz ) {
				$date = get_gmt_from_date( $date );
			}

			try {
				$result[ $index ] = new \DateTimeImmutable( $date );
			} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
				// Invalid date, leave as null.
			}
		}

		return $result;
	}

	/**
	 * Returns a filter with a possibly trimmed value.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The original search filter.
	 *
	 * @return Search_Filter The updated search filter.
	 */
	protected function maybe_trim_value( Search_Filter $filter ): Search_Filter {
		$value = $filter->value();
		if ( ! Search_Policy::should_trim_input( $this->view ) || $filter->is_normalized() ) {
			return $filter;
		}

		$value = is_string( $value ) ? trim( $value ) : $value;
		$value = is_array( $value )
			? array_map(
				static fn( $value ) => is_string( $value )
					? trim( $value )
					: $value,
				$value
			)
			: $value;

		if ( $value === $filter->value() ) {
			return $filter;
		}

		return $filter->with_value( $value );
	}
}
