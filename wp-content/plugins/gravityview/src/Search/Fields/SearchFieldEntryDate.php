<?php
/**
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Search\Fields;

use GravityKit\GravityView\Search\Querying\SearchFilter;
use GravityKit\GravityView\Search\SearchPolicy;
use GravityKit\GravityView\View\View;

/**
 * Represents a search field that searches on the Entry Date.
 *
 * @since 2.42
 * @since 3.0.0 Migrated to PSR-4 namespace.
 *
 * @extends SearchField
 */
final class SearchFieldEntryDate extends SearchField {
	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected string $icon = 'dashicons-calendar-alt';

	/**
	 * @inheritdoc
	 * @since 2.42
	 */
	protected static string $type = 'entry_date';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected static string $field_type = 'entry_date';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_name(): string {
		return esc_html__( 'Entry Date', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_default_label(): string {
		return esc_html__( 'Filter by date:', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function get_description(): string {
		return esc_html__( 'Search on entry date within a range', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_input_type(): string {
		return 'entry_date';
	}

	/**
	 * @inheritDoc
	 * @since 3.0.0
	 */
	protected function get_options(): array {
		$options = parent::get_options();

		$options['date_range_mode'] = $this->get_date_range_mode_option();

		return array_merge( $options, $this->get_date_bound_options() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * Falls back to the View's enforced date range.
	 *
	 * @since 3.0.0
	 */
	protected function get_default_date_bound( string $key ): ?string {
		if ( ! $this->view || $this->view->settings->get( 'allow_date_range_overwrite', false ) ) {
			return null;
		}

		$setting = 'min_date' === $key ? 'start_date' : 'end_date';
		$value   = (string) $this->view->settings->get( $setting, '' );

		return '' !== $value ? $value : null;
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function collect_template_data(): array {
		$data = parent::collect_template_data();
		// Requires the parent's value.
		$data['input_type'] = parent::get_input_type();

		return $this->apply_date_bounds( $data );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_input_value() {
		return [
			'start' => $this->get_request_value( 'gv_start', '' ),
			'end'   => $this->get_request_value( 'gv_end', '' ),
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.57.0
	 */
	public function adjust_filter( SearchFilter $filter, ?View $view = null ): SearchFilter {
		$filter   = parent::adjust_filter( $filter, $view );
		$operator = $filter->operator();
		$value    = $filter->value();

		$mapping = [];
		if ( is_array( $value ) && 'between' === $operator ) {
			$mapping['start_date'] = $value[0] ?? null;
			$mapping['end_date']   = $value[1] ?? null;
		} elseif ( '>=' === $operator || 'day' === $operator ) {
			$mapping['start_date'] = $value;
		} elseif ( '<=' === $operator ) {
			$mapping['end_date'] = $value;
		}

		$clamp = $view && ! $view->settings->get( 'allow_date_range_overwrite', false );

		foreach ( $mapping as $key => $date ) {
			if ( null === $date ) {
				continue;
			}

			// Picker bounds first, View range last.
			$resolved        = $this->clamp_to_date_bounds( SearchPolicy::resolve_date( (string) $date ) );
			$mapping[ $key ] = $resolved;

			if ( ! $clamp ) {
				continue;
			}

			$stored     = $view->settings->get( $key );
			$resolved_t = $resolved ? strtotime( $resolved ) : false;
			$stored_t   = $stored ? strtotime( $stored ) : false;

			if ( ! $resolved_t || ! $stored_t ) {
				continue;
			}

			if (
				( 'start_date' === $key && $resolved_t < $stored_t )
				|| ( 'end_date' === $key && $resolved_t > $stored_t )
			) {
				$mapping[ $key ] = $stored;
			}
		}

		// Rebuild the filter value from the resolved (and possibly clamped) mapping.
		if ( is_array( $value ) && 'between' === $operator ) {
			return $filter->with_value( [ $mapping['start_date'], $mapping['end_date'] ] );
		}

		return $filter->with_value( $mapping['start_date'] ?? $mapping['end_date'] );
	}
}
