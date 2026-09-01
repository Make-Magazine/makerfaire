<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Filter;

use DateTimeImmutable;
use Exception;
use GFFormsModel;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\ProcessDateVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Repository\FormRepository;

/**
 * Service that validates entries for a filter.
 *
 * @since 2.0.0
 */
final class EntryFilterService {

	/**
	 * The form repository.
	 *
	 * @since 2.0.0
	 * @var FormRepository
	 */
	private $form_repository;

	/**
	 * Creates the service.
	 *
	 * @since 2.0.0
	 *
	 * @param FormRepository $form_repository The form repository.
	 *
	 */
	public function __construct( FormRepository $form_repository ) {
		$this->form_repository = $form_repository;
	}

	/**
	 * Whether an entry object meets the applied filter.
	 *
	 * @since 2.0.0
	 *
	 * @param Filter $filter The filter.
	 *
	 * @param array  $entry  The entry object.
	 *
	 * @return bool
	 */
	public function meets_filter( array $entry, Filter $filter ): bool {
		if ( ! $filter->is_enabled() ) {
			return true;
		}

		if ( $filter->is_logic() ) {
			return $this->handle_logic( $entry, $filter );
		}

		return $this->handle_filter( $entry, $filter );
	}

	/**
	 * Returns whether the entry meets this non-logic filter.
	 *
	 * @since 2.0.0
	 *
	 * @param Filter $filter The filter to handle.
	 *
	 * @param array  $entry  The entry object.
	 *
	 * @return bool
	 */
	private function handle_filter( array $entry, Filter $filter ): bool {
		// Check the correct entry when it is a multi-entry.
		if ( isset( $entry['_multi'] ) && $filter->form_id() ) {
			return $this->handle_filter( $entry['_multi'][ $filter->form_id() ] ?? [], $filter );
		}

		if ( $filter->key() === '0' ) {
			return $this->matched_any_field( $entry, $filter );
		}

		// Todo: register multiple validators, and pick the one can handle the filter and field.
		$field_id = is_numeric( $filter->key() ) ? (int) $filter->key() : $filter->key();
		$field    = $this->form_repository->get_field( $filter->form_id() ?: $entry['form_id'] ?? 0, $field_id );

		$entry_value  = $entry[ $filter->key() ] ?? '';
		$filter_value = $filter->value();

		$entry_value = $this->maybe_match_user( $entry_value, $filter );

		if ( $field ) {
			if ( $field->inputs && $field->choices ) {
				if ( is_array( $filter_value ) ) {
					return $this->matches_multi_choice( $entry, (string) $field_id, $filter_value, $filter->operator() );
				}

				$input_id = null;
				// Find the selected option input_id.
				foreach ( $field->choices as $i => $choice ) {
					// Absolute match takes precedence.
					if ( $this->matches_operation( $choice['value'], $filter_value, 'is' ) ) {
						$input_id = (string) $field->inputs[ $i ]['id'];
						break;
					}

					// Skip values that don't match at all.
					if ( ! $this->matches_operation( $choice['value'], $filter_value, 'contains' ) ) {
						continue;
					}

					$input_id = (string) $field->inputs[ $i ]['id'];
				}

				if (
					! isset( $entry[ $input_id ] )
					|| ( isset( $entry[ $field_id ] ) && '' === $entry[ $input_id ] )
				) {
					$input_id = $field_id; // Try the field ID as a backup. Radio needs this for example.
				}

				$entry_value = $entry[ $input_id ] ?? '';
			}

			if ( 'file' === $field->type && '[]' === $entry_value ) {
				$entry_value = '';
			}
		}

		$operator = $filter->operator();

		if (
			( $field && ProcessDateVisitor::get_date_format( $field, $filter ) )
			|| ProcessDateVisitor::is_native_date_filter( $filter )
		) {
			$entry_empty  = '' === (string) $entry_value;
			$filter_empty = '' === (string) $filter_value;

			// When either side is empty we short-circuit — `new DateTimeImmutable('')` returns *now*,
			// which would silently turn an empty entry into a match against today's date below.
			if ( $entry_empty || $filter_empty ) {
				// Both sides empty means the entry is empty AND the filter asked for empty (via the
				// `isempty` proxy that rewrites to `is` with an empty value). Only equality matches;
				// every other comparison on two empties is falsy.
				if ( $entry_empty && $filter_empty ) {
					return 'is' === $operator;
				}

				// Exactly one side is empty. An empty value isn't equal to a real date and can't be
				// ordered against one, so only `isnot` is trivially true — all other operators are false.
				return 'isnot' === $operator;
			}

			try {
				// For 'is' and 'isnot' operators, strip the time component so that
				// relative dates like "today" match entries from any time that day.
				if ( in_array( $operator, [ 'is', 'isnot' ], true ) ) {
					$filter_value = ( new DateTimeImmutable( (string) $filter_value ) )->format( 'Y-m-d' );
					$entry_value  = ( new DateTimeImmutable( (string) $entry_value ) )->format( 'Y-m-d' );
				}

				$filter_value = $this->convert_date_to_timestamp( (string) $filter_value );
				$entry_value  = $this->convert_date_to_timestamp( (string) $entry_value );
			} catch ( Exception $e ) {
				// @todo: log exception
				return false;
			}
		}

		return $this->matches_operation( $entry_value, $filter_value, $operator );
	}

	/**
	 * Whether the entry's selected multi-input choices satisfy a set operator.
	 *
	 * Values are gathered by the `{field_id}.` input-key prefix rather than the field's current
	 * inputs, because Gravity Forms re-numbers input ids when choices are reordered or inserted
	 * while historical entries keep the id stored at submission time.
	 *
	 * @since 2.14.0
	 *
	 * @param array    $entry        The entry object.
	 * @param string   $field_id     The field ID.
	 * @param string[] $filter_value The selected choice values.
	 * @param string   $operator     The resolved operator (`in`, `notin`, or `has_all`).
	 *
	 * @return bool Whether the entry meets the filter.
	 */
	private function matches_multi_choice( array $entry, string $field_id, array $filter_value, string $operator ): bool {
		$prefix   = $field_id . '.';
		$selected = [];
		foreach ( $entry as $entry_key => $entry_value ) {
			if ( 0 !== strpos( (string) $entry_key, $prefix ) ) {
				continue;
			}

			$entry_value = (string) $entry_value;
			if ( '' !== $entry_value ) {
				$selected[] = $entry_value;
			}
		}

		$needed = array_map( 'strval', $filter_value );

		if ( 'has_all' === $operator ) {
			return [] === array_diff( $needed, $selected );
		}

		$found = [] !== array_intersect( $needed, $selected );

		return 'notin' === $operator ? ! $found : $found;
	}

	/**
	 * Returns whether the entry meets this logic filter.
	 *
	 * @since 2.0.0
	 *
	 * @param Filter $filter The logic filter to handle.
	 *
	 * @param array  $entry  The entry object.
	 *
	 * @return bool
	 */
	private function handle_logic( array $entry, Filter $filter ): bool {
		foreach ( $filter->conditions() as $child_filter ) {
			if (
				$filter->mode() === Filter::MODE_OR
				&& $this->meets_filter( $entry, $child_filter )
			) {
				// At least one is true; skip the rest.
				return true;
			}

			if (
				$filter->mode() === Filter::MODE_AND
				&& ! $this->meets_filter( $entry, $child_filter )
			) {
				// At least one is false; skip the rest.
				return false;
			}
		}

		// At this point either:
		// - all the filters were `false` for OR
		// - all the filters were `true` for AND
		return $filter->mode() === Filter::MODE_AND;
	}

	/**
	 * Whether any of the entry fields matches the filter.
	 *
	 * @param array  $entry  The entry object.
	 * @param Filter $filter The filter.
	 *
	 * @scince 2.0.0
	 * @return void
	 */
	private function matched_any_field( array $entry, Filter $filter ): bool {
		foreach ( $entry as $field_id => $entry_value ) {
			if ( ! is_numeric( $field_id ) ) {
				// form fields always have numeric IDs
				continue;
			}

			if ( ! $field = $this->form_repository->get_field( $entry['form_id'] ?? 0, $field_id ) ) {
				continue;
			}

			$filter_value = $filter->value();

			if ( $field->type === 'date' ) {
				try {
					$filter_value = $this->convert_date_to_timestamp( (string) $filter_value );
					$entry_value  = $this->convert_date_to_timestamp( (string) $entry_value );
				} catch ( Exception $e ) {
					// @todo: log exception
					return false;
				}
			}

			if ( $this->matches_operation( $entry_value, $filter_value, $filter->operator() ) ) {
				// Matched a field.
				return true;
			}
		}

		return false;
	}

	/**
	 * Converts a datetime string to a timestamp.
	 *
	 * @since 2.0.0
	 *
	 * @param string $filter_value The datetime string.
	 *
	 * @return int The timestamp.
	 * @throws Exception
	 */
	private function convert_date_to_timestamp( string $filter_value ): int {
		$filter_date = new DateTimeImmutable( $filter_value );

		return $filter_date->getTimestamp();
	}

	/**
	 * Returns whether the operation matches.
	 *
	 * @since 2.3.0
	 *
	 * @param mixed  $value_1
	 * @param mixed  $value_2
	 * @param string $operation The operation.
	 *
	 * @return bool Whether the operation matches.
	 */
	private function matches_operation( $value_1, $value_2, $operation ): bool {
		if ( in_array( $operation, [ 'ncontains', 'notcontains' ], true ) ) {
			return ! $this->matches_operation( $value_1, $value_2, 'contains' );
		}

		if ( in_array( $operation, [ 'in', 'notin' ], true ) ) {
			$allowed = array_map( 'strval', (array) $value_2 );
			$found   = in_array( (string) $value_1, $allowed, true );

			return 'in' === $operation ? $found : ! $found;
		}

		if ( method_exists( GFFormsModel::class, 'matches_conditional_operation' ) ) {
			return GFFormsModel::matches_conditional_operation( $value_1, $value_2, $operation );
		}

		return GFFormsModel::matches_operation( $value_1, $value_2, $operation );
	}

	/**
	 * Resolves the entry value for created_by filters by matching against user data.
	 *
	 * When the filter targets `created_by`, the raw entry value is a numeric user ID.
	 * This method looks up the user and checks whether any of their data fields match
	 * the filter value using the filter's operator. If a match is found, the matching
	 * user field value is returned so the caller's comparison succeeds.
	 *
	 * @since 2.9.0
	 *
	 * @param string $value  The raw entry value (user ID for created_by filters).
	 * @param Filter $filter The filter being evaluated.
	 *
	 * @return string The matched user field value, or the original value if no match.
	 */
	private function maybe_match_user( $value, Filter $filter ): string {
		if ( 'created_by' !== $filter->key() ) {
			return $value;
		}

		$user = get_user( $value );
		if ( false === $user ) {
			return $value;
		}

		$user_fields = [
			'nickname',
			'first_name',
			'last_name',
			'user_nicename',
			'user_login',
			'display_name',
			'user_email',
		];

		/**
		 * Modifies the user fields to search in the created_by condition.
		 *
		 * @since  2.9.0
		 *
		 * @param array  $user_fields The user fields.
		 * @param Filter $filter      The form ID.
		 */
		$user_fields = apply_filters(
			'gk/query-filters/entry-filter/created-by/user-fields',
			$user_fields,
			$filter,
		);

		if ( ! is_array( $user_fields ) ) {
			return $value;
		}

		$data         = array_filter(
			(array) $user->data,
			static fn( $key ) => in_array( $key, $user_fields, true ),
			ARRAY_FILTER_USE_KEY
		);
		$filter_value = $filter->value() ?? '';

		foreach ( $data as $user_value ) {
			if ( $this->matches_operation( $user_value, $filter_value, $filter->operator() ) ) {
				return $user_value;
			}
		}

		return $value;
	}
}
