<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Repository;

use GF_Field;
use GV\View;

/**
 * Form Repository.
 * @since 2.0.0
 */
interface FormRepository {
	/**
	 * Returns the form for a view.
	 *
	 * @param View $view The view.
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function get_form_by_view( View $view ): array;

	/**
	 * Returns the form..
	 *
	 * @param int|null $form_id The form id.
	 *
	 * @return array The form object.
	 * @since 2.0.0
	 */
	public function get_form( $form_id = null ): array;

	/**
	 * Returns a field.
	 *
	 * @param int $form_id The form id.
	 * @param string|int $field_id The field id.
	 *
	 * @return null|GF_Field The field.
	 * @since 2.0.0
	 */
	public function get_field( int $form_id, $field_id ): ?GF_Field;

	/**
	 * Returns the field filter options from Gravity Forms and modifies them.
	 *
	 * @param int $form_id THe form id.
	 *
	 * @return array{key: string, text: string, operators: string[], preventMultiple: bool, values: array, cssClass: string }[] The field filter options.
	 */
	public function get_field_filters( int $form_id ): array;
}
