<?php
/**
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Search\Fields;

use \GravityView_Entry_Approval_Status;
use GV\Search\Querying\Search_Filter;
use GV\Search\Querying\Visitors\Search_Criteria_Visitor;
use GV\View;

/**
 * Represents a search field that searches on the Entry Date.
 *
 * @since 2.42
 * @since 3.0.0 Migrated to PSR-4 namespace.
 *
 * @extends \GV\Search\Fields\Search_Field_Choices
 */
final class SearchFieldIsApproved extends \GV\Search\Fields\Search_Field_Choices {
	/**
	 * The state constant(s).
	 *
	 * @since 2.55.0
	 */
	private const STATE_PROCESSED = 'processed';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected string $icon = 'dashicons-yes-alt';

	/**
	 * @inheritdoc
	 * @since 2.42
	 */
	protected static string $type = 'is_approved';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected static string $field_type = 'multi';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_name(): string {
		return esc_html__( 'Approval Status', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function get_description(): string {
		return esc_html__( 'Filter on approval status', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_default_label(): string {
		return esc_html__( 'Approval:', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_choices(): array {
		return array_map(
			static fn( array $choice ): array => [
				'text'  => (string) ( $choice['label'] ?? '' ),
				'value' => (string) ( $choice['value'] ?? '' ),
			],
			GravityView_Entry_Approval_Status::get_all()
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.55.0
	 */
	public function adjust_filter( Search_Filter $filter, ?View $view = null ): Search_Filter {
		$filter = parent::adjust_filter( $filter, $view );

		if (
			// Search Criteria can't handle this nested group properly.
			$filter->context( 'current_visitor' ) instanceof Search_Criteria_Visitor
			// Prevent infinite loop.
			|| $filter->context( self::STATE_PROCESSED, false )
		) {
			return $filter;
		}

		$filter = $filter
			// Value is always an array, so we use IN.
			->with_operator( 'in', [ 'in' ] )
			// Ensure int[] value.
			->with_value( array_map( 'intval', (array) $filter->value() ) )
			// Prevent infinite loop.
			->with_context( self::STATE_PROCESSED, true );

		if ( ! in_array( GravityView_Entry_Approval_Status::UNAPPROVED, $filter->value(), true ) ) {
			return $filter;
		}

		return Search_Filter::or(
			$filter,
			$filter
				->with_operator( '=', [ '=' ] )
				->with_value( '' )
				->as_normalized() // Prevent empty value from being removed.
		);
	}
}
