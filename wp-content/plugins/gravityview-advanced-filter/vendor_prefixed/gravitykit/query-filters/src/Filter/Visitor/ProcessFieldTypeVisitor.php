<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor;

use GF_Field;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;
use GravityKit\AdvancedFilter\QueryFilters\Repository\FormRepository;

/**
 * Visitor that changes filters based on the field type.
 * @since 2.0.0
 */
final class ProcessFieldTypeVisitor implements FilterVisitor {
	/**
	 * Form repository.
	 * @since 2.0.0
	 * @var FormRepository
	 */
	private $form_repository;

	/**
	 * The form object.
	 * @since 2.0.0
	 * @var array
	 */
	private $form;

	/**
	 * Creates the visitor.
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

		if (
			! $filter->key()
			|| 'created_by' === $filter->key()
		) {
			return;
		}

		$form       = $this->get_form();
		$field      = $this->form_repository->get_field( $form['id'] ?? 0, $filter->key() );
		$field_type = $field ? $field->type : $filter->key();

		if ( ! $field_type ) {
			return;
		}

		$callback = $this->get_callback_for_field_type( $field_type );
		if ( ! $callback ) {
			return;
		}

		$callback( $filter, $field );
	}

	/**
	 * Returns the form for the filter.
	 * @return array
	 * @since 2.0.0
	 */
	private function get_form(): array {
		$form = $this->form;

		// todo: can this be removed, or is $filter['form_id'] a legit use case?

//		if ( isset( $filter['form_id'] ) ) {
//			$form = GFAPI::get_form( $filter['form_id'] );
//		}

		if ( ! $form ) {
			$form = $this->form_repository->get_form();
		}

		return $form;
	}

	/**
	 * Returns a callback for a specific field type.
	 *
	 * @param string $field_type The field type.
	 *
	 * @return null|callable{Filter, ?GF_Field}
	 * @since 2.0.0
	 */
	private function get_callback_for_field_type( string $field_type ): ?callable {
		$callbacks = [
			/**
			 * @since 2.0.0
			 */
			'entry_id'      => function ( Filter $filter ) {
				$filter->set_key( 'id' );
			},
			/**
			 * @since 2.0.0
			 */
			'post_category' => function ( Filter $filter ) {
				$category_name = get_term_field( 'name', $filter->value(), 'category', 'raw' );
				if ( $category_name && ! is_wp_error( $category_name ) ) {
					$filter->set_value( $category_name . ':' . $filter->value() );
				}
			},
			/**
			 * Empty multi-file upload field contains '[]' (empty JSON array) as a value
			 *
			 * @since 2.0.0
			 */
			'fileupload'    => function ( Filter $filter, GF_Field $field ) {
				if (
					$field->multipleFiles
					&& $filter->value() === ''
					&& in_array(
						$filter->operator(),
						[
							'is',
							'isnot'
						],
						true
					) ) {
					$filter->set_value( '[]' );
				}
			},
			'partial_entry_percent' => function ( Filter $filter ) {
				if (
					$filter->value() !== '' // complete
					|| ! in_array( $filter->operator(), [ 'is', 'isnot' ], true )
				) {
					return;
				}

				// Consider 100% as complete.
				$filter->set_operator($filter->operator() === 'is' ? 'in' : 'notin');
				$filter->set_value( [ '', 100 ] );
			},
		];

		$callbacks = apply_filters( 'gk/query-filters/process-field-type/callbacks', $callbacks );

		return $callbacks[ $field_type ] ?? null;
	}
}
