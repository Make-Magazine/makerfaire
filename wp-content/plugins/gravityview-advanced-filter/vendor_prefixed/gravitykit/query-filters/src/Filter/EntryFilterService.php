<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter;

use DateTimeImmutable;
use Exception;
use GFFormsModel;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\ProcessDateVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Repository\FormRepository;

/**
 * Service that validates entries for a filter.
 * @since 2.0.0
 */
final class EntryFilterService {

	/**
	 * The form repository.
	 * @since 2.0.0
	 * @var FormRepository
	 */
	private $form_repository;

	/**
	 * Creates the service.
	 *
	 * @param FormRepository $form_repository The form repository.
	 *
	 * @since 2.0.0
	 */
	public function __construct( FormRepository $form_repository ) {
		$this->form_repository = $form_repository;
	}

	/**
	 * Whether an entry object meets the applied filter.
	 *
	 * @param array  $entry  The entry object.
	 * @param Filter $filter The filter.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function meets_filter( array $entry, Filter $filter ): bool {
		if (!$filter->is_enabled()) {
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
	 * @param array  $entry  The entry object.
	 * @param Filter $filter The filter to handle.
	 *
	 * @return bool
	 * @since 2.0.0
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
					if ( GFFormsModel::matches_operation( $choice['value'], $filter_value, 'is' ) ) {
						$input_id = (string) $field->inputs[ $i ]['id'];
						break;
					}

					// Skip values that don't match at all.
					if ( ! GFFormsModel::matches_operation( $choice['value'], $filter_value, 'contains' ) ) {
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

		return GFFormsModel::matches_operation( $entry_value, $filter_value, $filter->operator() );
	}

	/**
	 * Returns whether the entry meets this logic filter.
	 *
	 * @param array  $entry  The entry object.
	 * @param Filter $filter The logic filter to handle.
	 *
	 * @return bool
	 * @since 2.0.0
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

			if ( GFFormsModel::matches_operation( $entry_value, $filter_value, $filter->operator() ) ) {
				// Matched a field.
				return true;
			}
		}

		return false;
	}

	/**
	 * Converts a datetime string to a timestamp.
	 *
	 * @param string $filter_value The datetime string.
	 *
	 * @return int The timestamp.
	 * @throws Exception
	 * @since 2.0.0
	 */
	private function convert_date_to_timestamp( string $filter_value ): int {
		$filter_date = new DateTimeImmutable( $filter_value );

		return $filter_date->getTimestamp();
	}
}
