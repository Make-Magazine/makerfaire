<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor;

use DateTimeImmutable;
use GF_Field;
use GFCommon;
use GravityKit\AdvancedFilter\QueryFilters\Clock\Clock;
use GravityKit\AdvancedFilter\QueryFilters\Clock\SystemClock;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;
use GravityKit\AdvancedFilter\QueryFilters\Repository\FormRepository;

/**
 * Replaces date fields with a proper date value.
 * @since 2.0.0
 */
final class ProcessDateVisitor implements FilterVisitor {
	/**
	 * The fields to check.
	 * @since 2.0.0
	 */
	private const DATE_FIELD_KEYS = [
		'date_created',
		'date_updated',
		'payment_date',
	];

	/**
	 * The fields to check.
	 * @since 2.0.0
	 * @var array<string, string> mapping of field types with date format.
	 */
	private const DATE_FIELD_TYPES = [
		'date'                              => 'Y-m-d',
		'workflow_current_status_timestamp' => 'U',
	];

	/**
	 * The form object.
	 * @since 2.0.1
	 * @var array
	 */
	private $form;

	/**
	 * The clock.
	 * @since 2.0.0
	 * @var Clock
	 */
	private $clock;

	/**
	 * Form repository.
	 * @since 2.0.0
	 * @var FormRepository
	 */
	private $form_repository;

	/**
	 * Creates the visitor.
	 * @since 2.0.0
	 * @since 2.0.1 Introduced the $form parameter.
	 *
	 * @param FormRepository $form_repository The form repository.
	 * @param array          $form            The form object. Default: empty array.
	 * @param Clock|null     $clock           The clock. Default: null.
	 */
	public function __construct( FormRepository $form_repository, array $form = [], ?Clock $clock = null ) {
		$this->form_repository = $form_repository;
		$this->form            = $form;
		$this->setClock( $clock );
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function visit_filter( Filter $filter, string $level = '0' ) {
		if (
			$filter->is_logic()
			|| empty( $filter->value() )
			|| ! $this->form
		) {
			return;
		}

		$field      = $this->form_repository->get_field( $this->form['id'] ?? 0, $filter->key() );

		if ( self::is_native_date_filter( $filter ) ) {
			$date        = $this->getDate( $filter );
			$date_format = $this->is_valid_datetime( $filter->value() ) ? 'Y-m-d' : 'Y-m-d H:i:s';

			// These fields are all stored in GMT+0, which is why we use `gmtdate`.
			$filter->set_value( gmdate( $date_format, $date ) );

			return;
		}

		if ( ! $field || ! is_numeric( $filter->key() ) ) {
			return;
		}

		$date_format = self::get_date_format( $field, $filter );
		if ( ! $date_format ) {
			return;
		}

		if ( ! $date = $this->getDate( $filter ) ) {
			return;
		}

		$filter->set_value( date( $date_format, $date ) );
	}

	/**
	 * Returns the parsed date.
	 *
	 * @param Filter $filter The filter object.
	 *
	 * @return string The parsed date.
	 * @since 2.0.0
	 */
	private function getDate( Filter $filter ): string {
		$date = '';
		if ( is_string( $filter->value() ) ) {
			$local_timestamp = GFCommon::get_local_timestamp( $this->clock->now()->getTimestamp() );
			$date            = strtotime( $filter->value(), $local_timestamp );
		}

		if ( ! $date ) {
			do_action( 'gravityview_log_error', __METHOD__ . ' - Date formatting passed to Query Filters is invalid', $filter->value() );
		}

		return $date;
	}

	/**
	 * Sets the clock.
	 *
	 * @param Clock|null $clock The clock
	 *
	 * @return void
	 * @since 2.0.0
	 */
	private function setClock( ?Clock $clock ) {
		if ( ! $clock instanceof Clock ) {
			$clock = new SystemClock();
		}

		$this->clock = $clock;
	}

	/**
	 * Alias of gravityview_is_valid_datetime()
	 *
	 * Check whether a string is a expected date format
	 *
	 * @param string $datetime        The date to check
	 * @param string $expected_format Check whether the date is formatted as expected. Default: Y-m-d
	 *
	 * @return bool True: it's a valid datetime, formatted as expected. False: it's not a date formatted as expected.
	 *
	 * @since 1.0.12
	 */
	private function is_valid_datetime( string $datetime, string $expected_format = 'Y-m-d' ): bool {
		$formatted_date = DateTimeImmutable::createFromFormat( $expected_format, $datetime );

		/**
		 * @see http://stackoverflow.com/a/19271434/480856
		 */
		return ( $formatted_date && $formatted_date->format( $expected_format ) === $datetime );
	}

	/**
	 * Returns the date format for the provided field.
	 *
	 * @since 2.0.3
	 *
	 * @param null|GF_Field $field  The field.
	 * @param Filter        $filter The filter.
	 *
	 * @return string The date format.
	 */
	public static function get_date_format( GF_Field $field, Filter $filter ): string {
		$field_type  = $field->get_input_type();
		$date_format = self::DATE_FIELD_TYPES[ $field_type ] ?? '';

		/**
		 * Modifies the date field's format.
		 *
		 * @filter `gk/query-filters/filter/process-date/date-format`
		 *
		 * @since  2.0.3
		 *
		 * @param string   $date_format The date format.
		 * @param GF_Field $field       The field.
		 * @param string   $filter_key  The filter key.
		 */
		return apply_filters(
			'gk/query-filters/filter/process-date/date-format',
			$date_format,
			$field,
			$filter->key()
		);
	}

	/**
	 * Whether this filter contains a native date field key.
	 * @since $ver$
	 *
	 * @param Filter $filter The filter.
	 *
	 * @return bool Whether the filter key is a native date field.
	 */
	public static function is_native_date_filter( Filter $filter ): bool {
		return in_array( $filter->key(), self::DATE_FIELD_KEYS, true );
	}
}
