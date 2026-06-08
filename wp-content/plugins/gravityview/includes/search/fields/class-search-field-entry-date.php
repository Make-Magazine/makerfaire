<?php

namespace GV\Search\Fields;

use GV\Search\Querying\Search_Filter;
use GV\Search\Search_Policy;
use GV\View;

/**
 * Represents a search field that searches on the Entry Date.
 *
 * @since 2.42
 *
 * @extends Search_Field<array{start:string, end:string}>
 */
final class Search_Field_Entry_Date extends Search_Field {
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
	 * @since 2.42
	 */
	protected function collect_template_data(): array {
		$data = parent::collect_template_data();
		// Requires the parent's value.
		$data['input_type'] = parent::get_input_type();

		return $data;
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
	public function adjust_filter( Search_Filter $filter, ?View $view = null ): Search_Filter {
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

		// Always resolve dates; only clamp when the View doesn't allow overwriting.
		$clamp = $view && ! $view->settings->get( 'allow_date_range_overwrite', false );

		foreach ( $mapping as $key => $date ) {
			if ( null === $date ) {
				continue;
			}

			$resolved        = Search_Policy::resolve_date( (string) $date );
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
