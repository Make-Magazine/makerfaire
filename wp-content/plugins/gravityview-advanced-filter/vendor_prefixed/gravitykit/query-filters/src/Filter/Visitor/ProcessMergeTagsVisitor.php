<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 28-April-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor;

use GFCommon;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;
use GravityKit\AdvancedFilter\QueryFilters\Repository\FormRepository;
use GravityView_API;

/**
 * Replaces merge tag on filter values.
 *
 * @since 2.0.0
 */
final class ProcessMergeTagsVisitor implements EntryAwareFilterVisitor {
	use EntryAware;

	/**
	 * The form repository.
	 *
	 * @since 2.0.0
	 * @var FormRepository
	 */
	private $form_repository;

	/**
	 * The form object.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	private $form;

	/**
	 * Creates the visitor.
	 *
	 * @since 2.0.0
	 */
	public function __construct( FormRepository $form_repository, array $form = [] ) {
		$this->form_repository = $form_repository;
		$this->form            = $form;
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function visit_filter( Filter $filter, string $level = '0' ) {
		if ( $filter->is_logic() ) {
			return;
		}

		$value        = $filter->value();
		$has_multiple = is_array( $value );
		if ( is_string( $value ) ) {
			$value = [ $value ];
		}

		if ( ! is_array( $value ) ) {
			return;
		}

		$form = $this->get_form( $filter );

		// Track the original value to detect merge tag processing.
		$original_values = $value;

		foreach ( $value as $i => $unprocessed ) {
			if ( ! is_string( $unprocessed ) ) {
				// Only process strings.
				continue;
			}

			$value[ $i ] = self::process_merge_tags( $unprocessed, $form, $this->entry );
		}

		$final_value = $has_multiple ? $value : reset( $value );

		// Set the resolved value first
		$filter->set_value( $final_value );

		// Fix for contains-type operators with empty values after merge tag processing.
		// When user merge tags resolve to empty for logged-out users, these operators
		// should not match any entries. This must be called AFTER set_value() so that
		// if the filter is locked, the lock sentinel value is not overwritten.
		$this->handle_empty_contains_values( $filter, $original_values );
	}

	/**
	 * Handles filters with empty values and contains-type operators.
	 *
	 * When a filter value becomes empty after merge tag processing (e.g., {user:*} for logged-out users),
	 * contains-type operators should treat this as "no match" rather than "match everything".
	 *
	 * @since 2.7.0
	 *
	 * @param Filter $filter          The filter to check.
	 * @param array  $original_values The original filter values before processing.
	 *
	 * @return void
	 */
	private function handle_empty_contains_values( Filter $filter, array $original_values ) {
		// Get the operator.
		$operator        = $filter->operator();
		$processed_value = $filter->value();

		// Check if we processed user or created_by merge tags.
		// These are the merge tags that resolve to empty for logged-out users.
		$had_user_merge_tags = false;
		foreach ( $original_values as $original ) {
			if ( is_string( $original )
			     && (
				     strpos( $original, '{user:' ) !== false
				     || strpos( $original, '{user}' ) !== false
				     || strpos( $original, '{created_by:' ) !== false
				     || strpos( $original, '{created_by}' ) !== false
			     )
			) {
				$had_user_merge_tags = true;
				break;
			}
		}

		// Only apply special handling if we actually processed user/created_by merge tags.
		if ( ! $had_user_merge_tags ) {
			return;
		}

		// If the value is empty and the operator is a "contains"-type operator.
		if (
			$this->is_value_empty( $processed_value )
			&& $this->is_contains_type_operator( $operator )
		) {
			// We need to ensure this filter doesn't match anything.
			$filter->lock();
		}
	}

	/**
	 * Checks if a value is effectively empty.
	 *
	 * @since 2.7.0
	 *
	 * @param mixed $value The value to check.
	 *
	 * @return bool Whether the value is empty.
	 */
	private function is_value_empty( $value ): bool {
		if ( is_array( $value ) ) {
			// For array values, check if all elements are empty.
			return empty( array_filter( $value, static function ( $v ) {
				return $v !== '' && $v !== null;
			} ) );
		}

		return $value === '' || $value === null;
	}

	/**
	 * Checks if an operator is a contains-type operator that should not match when value is empty.
	 *
	 * @since 2.7.0
	 *
	 * @param string $operator The operator to check.
	 *
	 * @return bool Whether the operator is a contains-type operator.
	 */
	private function is_contains_type_operator( string $operator ): bool {
		$contains_type_operators = [
			'contains',
			'starts_with',
			'ends_with',
			'in',
			// Note: 'ncontains', 'notcontains', and 'not_in' are NOT included here
			// because they have inverse logic (should match everything when value is empty).
		];

		return in_array( $operator, $contains_type_operators, true );
	}

	/**
	 * Returns the proper form object.
	 *
	 * @since 2.0.0
	 *
	 * @param Filter $filter The filter.
	 *
	 * @return array
	 */
	private function get_form( Filter $filter ): array {
		$form = $this->form;

		if ( $filter->form_id() ) {
			$form = $this->form_repository->get_form( $filter->form_id() );
		}

		if ( ! $form ) {
			$form = $this->form_repository->get_form();
		}

		return $form;
	}

	/**
	 * Process merge tags in filter values
	 *
	 * @since 2.0.0
	 *
	 * @param string|null $filter_value Filter value text
	 * @param array       $form         GF Form array
	 * @param array       $entry        GF Entry array
	 *
	 * @return string|null
	 */
	public static function process_merge_tags( $filter_value, $form = [], $entry = [] ) {
		preg_match_all( "/{get:(.*?)}/ism", $filter_value ?? '', $get_merge_tags, PREG_SET_ORDER );

		$urldecode_get_merge_tag_value = function ( $value ) {
			return urldecode( $value );
		};

		foreach ( $get_merge_tags as $merge_tag ) {
			add_filter( 'gravityview/merge_tags/get/value/' . $merge_tag[1], $urldecode_get_merge_tag_value );
		}

		return class_exists( 'GravityView_API' )
			? GravityView_API::replace_variables( $filter_value, $form, $entry )
			: GFCommon::replace_variables( $filter_value, $form, $entry );
	}
}
