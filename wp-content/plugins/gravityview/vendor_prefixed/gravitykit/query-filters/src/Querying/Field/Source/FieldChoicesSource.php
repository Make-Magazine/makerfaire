<?php

namespace GravityKit\GravityView\QueryFilters\Querying\Field\Source;

use GFAPI;
use GF_Field;
use GravityKit\GravityView\QueryFilters\Querying\Field\Exception\FieldNotFoundException;
use GravityKit\GravityView\QueryFilters\Querying\Field\Exception\FormNotFoundException;
use GravityKit\GravityView\QueryFilters\Querying\Field\Exception\UnsupportedFieldTypeException;
use GravityKit\GravityView\QueryFilters\Querying\Field\FieldChoice;
use GravityKit\GravityView\QueryFilters\Querying\Field\FieldCriteria;

/**
 * Terminal {@see ChoiceSource} backed by the field's own `$field->choices` array.
 *
 * @since 2.14.0
 */
final class FieldChoicesSource implements ChoiceSource {
	/**
	 * Field types whose `$field->choices` are exposed verbatim.
	 *
	 * @since 2.14.0
	 */
	private const SUPPORTED_FIELD_TYPES = [ 'select', 'radio', 'checkbox', 'multiselect' ];

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 */
	public function supports( int $form_id, string $field_id ): bool {
		$form = GFAPI::get_form( $form_id );
		if ( ! $form ) {
			return false;
		}

		$field = $this->find_field( $form, $field_id );

		return $field instanceof GF_Field && in_array( $field->type, self::SUPPORTED_FIELD_TYPES, true );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 */
	public function equals( ChoiceSource $other ): bool {
		return $other instanceof self;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 */
	public function prefers_endpoint(): bool {
		return false;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 *
	 * @throws FormNotFoundException         When the form does not exist.
	 * @throws FieldNotFoundException        When the field does not exist on the form.
	 * @throws UnsupportedFieldTypeException When the field type does not expose static choices.
	 */
	public function search( FieldCriteria $criteria ): array {
		return array_slice( $this->match( $criteria ), $criteria->offset(), $criteria->limit() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 *
	 * @throws FormNotFoundException         When the form does not exist.
	 * @throws FieldNotFoundException        When the field does not exist on the form.
	 * @throws UnsupportedFieldTypeException When the field type does not expose static choices.
	 */
	public function count( FieldCriteria $criteria ): int {
		return count( $this->match( $criteria ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 *
	 * @throws FormNotFoundException         When the form does not exist.
	 * @throws FieldNotFoundException        When the field does not exist on the form.
	 * @throws UnsupportedFieldTypeException When the field type does not expose static choices.
	 */
	public function resolve( FieldCriteria $criteria ): array {
		$wanted = $criteria->values();
		if ( $wanted === [] ) {
			return [];
		}

		$choices = $this->load_choices( $criteria->form_id(), $criteria->field_id() );

		return array_values(
			array_filter(
				$choices,
				static fn( FieldChoice $choice ): bool => in_array( $choice->value(), $wanted, true )
			)
		);
	}

	/**
	 * Loads the field's choices and applies the criteria's value and search filters, ignoring
	 * pagination.
	 *
	 * @since 2.14.0
	 *
	 * @param FieldCriteria $criteria The search criteria.
	 *
	 * @return FieldChoice[] The matching choices.
	 */
	private function match( FieldCriteria $criteria ): array {
		$choices = $this->load_choices( $criteria->form_id(), $criteria->field_id() );

		if ( $criteria->values() !== [] ) {
			$wanted  = $criteria->values();
			$choices = array_filter(
				$choices,
				static fn( FieldChoice $choice ): bool => in_array( $choice->value(), $wanted, true )
			);
		}

		if ( $criteria->search() !== '' ) {
			$needle  = strtolower( $criteria->search() );
			$choices = array_filter(
				$choices,
				static fn( FieldChoice $choice ): bool =>
					stripos( $choice->label(), $needle ) !== false
					|| stripos( $choice->value(), $needle ) !== false
			);
		}

		return array_values( $choices );
	}

	/**
	 * Resolves a form + field into the normalized choice list.
	 *
	 * @since 2.14.0
	 *
	 * @param int    $form_id  The form ID.
	 * @param string $field_id The field ID.
	 *
	 * @return FieldChoice[] The choices in author-defined order.
	 *
	 * @throws FormNotFoundException         When the form does not exist.
	 * @throws FieldNotFoundException        When the field does not exist on the form.
	 * @throws UnsupportedFieldTypeException When the field type does not expose static choices.
	 */
	private function load_choices( int $form_id, string $field_id ): array {
		$form = GFAPI::get_form( $form_id );
		if ( ! $form ) {
			throw FormNotFoundException::for_id( $form_id );
		}

		$field = $this->find_field( $form, $field_id );
		if ( ! $field instanceof GF_Field ) {
			throw FieldNotFoundException::for_field( $form_id, $field_id );
		}

		if ( ! in_array( $field->type, self::SUPPORTED_FIELD_TYPES, true ) ) {
			throw UnsupportedFieldTypeException::for_type( (string) $field->type );
		}

		return self::normalize_choices( $field->choices ?? [] );
	}

	/**
	 * Looks up a field on a form by ID. Accepts both numeric and string field IDs (e.g. `5.3`).
	 *
	 * @since 2.14.0
	 *
	 * @param array  $form     The form array.
	 * @param string $field_id The field ID.
	 *
	 * @return GF_Field|null The field, when found.
	 */
	private function find_field( array $form, string $field_id ): ?GF_Field {
		if ( empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
			return null;
		}

		foreach ( $form['fields'] as $field ) {
			if ( (string) $field->id === $field_id ) {
				return $field;
			}
		}

		return null;
	}

	/**
	 * Normalizes Gravity Forms' `{text, value, isSelected, ...}` choice rows into FieldChoice objects.
	 *
	 * @since 2.14.0
	 *
	 * @param array $choices Raw GF choice array.
	 *
	 * @return FieldChoice[] The normalized choices.
	 */
	private static function normalize_choices( array $choices ): array {
		$out = [];
		foreach ( $choices as $choice ) {
			if ( ! is_array( $choice ) || ! isset( $choice['value'] ) ) {
				continue;
			}

			$value = (string) $choice['value'];
			$label = (string) ( $choice['text'] ?? $choice['value'] );

			$out[] = new FieldChoice( $value, $label );
		}

		return $out;
	}
}
