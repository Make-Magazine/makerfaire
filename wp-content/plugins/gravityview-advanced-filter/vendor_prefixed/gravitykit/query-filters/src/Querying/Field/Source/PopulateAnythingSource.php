<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\Field\Source;

use GFAPI;
use GF_Field;
use GP_Populate_Anything;
use GravityKit\AdvancedFilter\QueryFilters\Querying\Field\FieldChoice;
use GravityKit\AdvancedFilter\QueryFilters\Querying\Field\FieldCriteria;

/**
 * {@see ChoiceSource} for fields whose choices are populated by Gravity Perks Populate Anything.
 *
 * Delegates choice resolution to Populate Anything's own query layer so its object types, filter
 * groups, templates, and uniqueness all apply, then filters and paginates the returned set in
 * memory; the count uses a probe bounded to the criteria threshold rather than loading the full set.
 *
 * @since 2.14.0
 */
final class PopulateAnythingSource implements ChoiceSource {
	/**
	 * Registers this source on the manager when Gravity Perks Populate Anything is available.
	 *
	 * @since 2.14.0
	 *
	 * @param ChoiceSourceManager $manager The manager to register on.
	 */
	public static function register( ChoiceSourceManager $manager ): void {
		if ( ! class_exists( GP_Populate_Anything::class ) || ! function_exists( 'gp_populate_anything' ) ) {
			return;
		}

		$manager->register( new self() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 */
	public function supports( int $form_id, string $field_id ): bool {
		$field = $this->field( $form_id, $field_id );

		return $field instanceof GF_Field
		       && ! empty( $field['gppa-choices-enabled'] )
		       && ! empty( $field['gppa-choices-object-type'] )
		       && function_exists( 'gp_populate_anything' );
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
		return true;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 */
	public function search( FieldCriteria $criteria ): array {
		$reach = $criteria->offset() + $criteria->limit();

		$matches = $this->with_query_limit( $reach + 1, fn(): array => $this->match( $criteria ) );

		return array_slice( $matches, $criteria->offset(), $criteria->limit() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * Probes `threshold + 1` so a low display limit cannot suppress the count, bounded to the
	 * threshold boundary rather than enumerating the full set.
	 *
	 * @since 2.14.0
	 */
	public function count( FieldCriteria $criteria ): int {
		$threshold = $criteria->threshold();
		if ( null === $threshold ) {
			return count( $this->match( $criteria ) );
		}

		return $this->with_query_limit( $threshold + 1, fn(): int => count( $this->match( $criteria ) ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.14.0
	 */
	public function resolve( FieldCriteria $criteria ): array {
		$wanted = $criteria->values();
		if ( $wanted === [] ) {
			return [];
		}

		$choices = $this->with_query_limit(
			$criteria->limit() + 1,
			fn(): array => $this->choices( $criteria->form_id(), $criteria->field_id() )
		);

		return array_values(
			array_filter(
				$choices,
				static fn( FieldChoice $choice ): bool => in_array( $choice->value(), $wanted, true )
			)
		);
	}

	/**
	 * Loads the populated choices and applies the criteria's value and search filters, ignoring
	 * pagination.
	 *
	 * @since 2.14.0
	 *
	 * @param FieldCriteria $criteria The search criteria.
	 *
	 * @return FieldChoice[] The matching choices.
	 */
	private function match( FieldCriteria $criteria ): array {
		$choices = $this->choices( $criteria->form_id(), $criteria->field_id() );

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
	 * Runs a callback with Populate Anything's query limit raised to the given size.
	 *
	 * Populate Anything treats the limit as one more than the number of choices it yields (the extra
	 * row is its has-more sentinel), so callers pass `wanted + 1` to receive up to `wanted` choices.
	 *
	 * @since 2.14.0
	 *
	 * @param int      $limit    The Populate Anything query limit to apply.
	 * @param callable $callback The callback to run under the raised limit.
	 *
	 * @return mixed The callback's return value.
	 */
	private function with_query_limit( int $limit, callable $callback ) {
		$override = static fn(): int => $limit;

		add_filter( 'gppa_query_limit', $override );
		add_filter( 'gppa_query_limit_paged', $override );

		try {
			return $callback();
		} finally {
			remove_filter( 'gppa_query_limit', $override );
			remove_filter( 'gppa_query_limit_paged', $override );
		}
	}

	/**
	 * Resolves the field's Populate Anything choices into the normalized choice list.
	 *
	 * @since 2.14.0
	 *
	 * @param int    $form_id  The form ID.
	 * @param string $field_id The field ID.
	 *
	 * @return FieldChoice[] The populated choices in Populate Anything's order.
	 */
	private function choices( int $form_id, string $field_id ): array {
		$field = $this->field( $form_id, $field_id );
		if ( ! $field instanceof GF_Field ) {
			return [];
		}

		$choices = gp_populate_anything()->get_input_choices( $field, null, false );

		return self::normalize_choices( is_array( $choices ) ? $choices : [] );
	}

	/**
	 * Normalizes Populate Anything's `{value, text, ...}` choice rows into FieldChoice objects,
	 * dropping its placeholder rows (`gppaErrorChoice`) for unmet dependencies or empty results.
	 *
	 * @since 2.14.0
	 *
	 * @param array $choices Raw Populate Anything choice array.
	 *
	 * @return FieldChoice[] The normalized choices.
	 */
	private static function normalize_choices( array $choices ): array {
		$out = [];
		foreach ( $choices as $choice ) {
			if ( ! is_array( $choice ) || ! isset( $choice['value'] ) || ! empty( $choice['gppaErrorChoice'] ) ) {
				continue;
			}

			$value = (string) $choice['value'];
			$label = (string) ( $choice['text'] ?? $choice['value'] );

			$out[] = new FieldChoice( $value, $label );
		}

		return $out;
	}

	/**
	 * Looks up a field on a form by ID.
	 *
	 * @since 2.14.0
	 *
	 * @param int    $form_id  The form ID.
	 * @param string $field_id The field ID.
	 *
	 * @return GF_Field|null The field, when found.
	 */
	private function field( int $form_id, string $field_id ): ?GF_Field {
		$form = GFAPI::get_form( $form_id );
		if ( ! $form || empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
			return null;
		}

		foreach ( $form['fields'] as $field ) {
			if ( (string) $field->id === $field_id ) {
				return $field;
			}
		}

		return null;
	}
}
