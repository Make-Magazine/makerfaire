<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 10-February-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

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
		if ( $filter->key() === '0' ) {
			return $this->matched_any_field( $entry, $filter );
		}

		// Todo: register multiple validators, and pick the one can handle the filter and field.
		$field_id = is_numeric( $filter->key() ) ? (int) $filter->key() : $filter->key();
		$field    = $this->form_repository->get_field( $entry['form_id'] ?? 0, $field_id );

		$entry_value  = $entry[ $filter->key() ] ?? '';
		$filter_value = $filter->value();

		if ( $field ) {
			if ( $field->inputs && $field->choices ) {
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

				$entry_value = $entry[ $input_id ] ?? '';
			}

			if ( 'file' === $field->type && '[]' === $entry_value ) {
				$entry_value = '';
			}
		}

		if (
			( $field && ProcessDateVisitor::get_date_format( $field, $filter ) )
			|| ProcessDateVisitor::is_native_date_filter( $filter )
		) {
			try {
				$filter_value = $this->convert_date_to_timestamp( (string) $filter_value );
				$entry_value  = $this->convert_date_to_timestamp( (string) $entry_value );
			} catch ( Exception $e ) {
				// @todo: log exception
				return false;
			}
		}

		return $this->matches_operation( $entry_value, $filter_value, $filter->operator() );
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
		if ( method_exists( GFFormsModel::class, 'matches_conditional_operation' ) ) {
			return GFFormsModel::matches_conditional_operation( $value_1, $value_2, $operation );
		}

		return GFFormsModel::matches_operation( $value_1, $value_2, $operation );
	}
}
