<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\Field\Source;

use GFAPI;
use GF_Field;
use GravityKit\Lookup\Factory;
use GravityKit\Lookup\Field as LookupField;
use GravityKit\Lookup\Resolver;
use GravityKit\Lookup\Sources\Base;
use GravityKit\Lookup\Sources\GFEntries;
use GravityKit\Lookup\Sources\PostTypes;
use GravityKit\Lookup\Sources\WPUsers;
use GravityKit\AdvancedFilter\QueryFilters\Querying\Field\FieldChoice;
use GravityKit\AdvancedFilter\QueryFilters\Querying\Field\FieldCriteria;

/**
 * {@see ChoiceSource} for Gravity Forms Dynamic Lookup (`lookup`) fields.
 *
 * Delegates to the Lookup choice API when available, with the existing source adapter as fallback.
 *
 * @since TBD
 */
final class DynamicLookupSource implements ChoiceSource {
	/**
	 * Registers this source on the manager when the Gravity Forms Dynamic Lookup plugin is available.
	 *
	 * @since 2.14.0
	 *
	 * @param ChoiceSourceManager $manager The manager to register on.
	 */
	public static function register( ChoiceSourceManager $manager ): void {
		if ( ! class_exists( LookupField::class ) ) {
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
		       && 'lookup' === $field->type
		       && class_exists( Factory::class );
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
	 * @since TBD
	 */
	public function search( FieldCriteria $criteria ): array {
		$field = $this->field( $criteria->form_id(), $criteria->field_id() );
		if ( ! $this->has_choice_api( $field ) ) {
			return $this->legacy_search( $criteria );
		}

		$rows = $field->search_lookup_choices( $criteria->search(), $criteria->offset(), $criteria->limit() );

		return $this->to_choices( $rows );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 */
	public function count( FieldCriteria $criteria ): int {
		$field = $this->field( $criteria->form_id(), $criteria->field_id() );
		if ( ! $this->has_choice_api( $field ) ) {
			return $this->legacy_count( $criteria );
		}

		return $field->count_lookup_choices( $criteria->search(), $criteria->threshold() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since TBD
	 */
	public function resolve( FieldCriteria $criteria ): array {
		$wanted = $criteria->values();
		if ( [] === $wanted ) {
			return [];
		}

		$field = $this->field( $criteria->form_id(), $criteria->field_id() );
		if ( ! $this->has_choice_api( $field ) ) {
			return $this->legacy_resolve( $criteria );
		}

		$rows = $field->resolve_lookup_choices( $wanted );

		if ( 'id' === (string) ( $field->storageFormat ?? '' ) ) {
			$rows = array_filter(
				$rows,
				static function ( $row ): bool {
					return ! is_array( $row )
					       || ! array_key_exists( 'member', $row )
					       || (bool) $row['member'];
				}
			);
		}

		return $this->to_choices( $rows, false );
	}

	/**
	 * Searches through the legacy source adapter.
	 *
	 * @since TBD
	 *
	 * @param FieldCriteria $criteria The search criteria.
	 *
	 * @return FieldChoice[] The matching choices.
	 */
	private function legacy_search( FieldCriteria $criteria ): array {
		$source = $this->source( $criteria, $criteria->offset() + $criteria->limit() );
		if ( ! $source instanceof Base ) {
			return [];
		}

		$results = array_slice( (array) $source->get_results(), $criteria->offset(), $criteria->limit() );

		return $this->to_choices( $results );
	}

	/**
	 * Counts through the legacy source adapter.
	 *
	 * @since TBD
	 *
	 * @param FieldCriteria $criteria The count criteria.
	 *
	 * @return int The raw source count.
	 */
	private function legacy_count( FieldCriteria $criteria ): int {
		$source = $this->source( $criteria, 1 );
		if ( ! $source instanceof Base ) {
			return 0;
		}

		return (int) $source->get_results_count();
	}

	/**
	 * Resolves through the legacy Lookup resolver.
	 *
	 * @since TBD
	 *
	 * @param FieldCriteria $criteria The resolution criteria.
	 *
	 * @return FieldChoice[] The resolved choices.
	 */
	private function legacy_resolve( FieldCriteria $criteria ): array {
		$wanted = $criteria->values();
		if ( $wanted === [] ) {
			return [];
		}

		$field = $this->field( $criteria->form_id(), $criteria->field_id() );
		if ( ! $field instanceof GF_Field || ! class_exists( Resolver::class ) ) {
			return [];
		}

		$resolver = Resolver::instance();
		$selector = (string) ( $field->displayValue ?? '' );
		$source   = (string) ( $field->lookupSourceType ?? 'gravity_forms' );

		if (
			GFEntries::get_source_type() === $source
			&& ! empty( $field->lookupFieldId )
			&& ! $resolver->is_valid_selector( $selector, $source )
		) {
			$selector = 'field:' . $field->lookupFieldId;
		}

		$out = [];
		foreach ( $wanted as $value ) {
			$label = (string) $resolver->get_display_value( $value, $selector, $source, $field );
			$out[] = new FieldChoice( (string) $value, '' !== $label ? $label : (string) $value );
		}

		return $out;
	}

	/**
	 * Whether the field exposes the Lookup choice API.
	 *
	 * @since TBD
	 *
	 * @param GF_Field|null $field The candidate field.
	 *
	 * @return bool Whether all operational methods are available.
	 */
	private function has_choice_api( ?GF_Field $field ): bool {
		return $field instanceof LookupField
		       && method_exists( $field, 'search_lookup_choices' )
		       && method_exists( $field, 'count_lookup_choices' )
		       && method_exists( $field, 'resolve_lookup_choices' );
	}

	/**
	 * Converts source rows into choices, deduplicated by stored value.
	 *
	 * @since TBD
	 *
	 * @param array<int, mixed> $rows        Source rows.
	 * @param bool              $deduplicate Whether duplicate stored values are removed.
	 *
	 * @return FieldChoice[] The normalized choices.
	 */
	private function to_choices( array $rows, bool $deduplicate = true ): array {
		$choices = [];
		$seen    = [];

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || ! isset( $row['value'] ) ) {
				continue;
			}

			$value = (string) $row['value'];
			if ( $deduplicate && isset( $seen[ $value ] ) ) {
				continue;
			}

			if ( $deduplicate ) {
				$seen[ $value ] = true;
			}

			$choices[] = new FieldChoice( $value, (string) ( $row['text'] ?? $value ) );
		}

		return $choices;
	}

	/**
	 * Builds the configured lookup data source with the criteria's search needle and a result
	 * window, mapping the field's lookup settings onto the source parameters.
	 *
	 * @since 2.14.0
	 *
	 * @param FieldCriteria $criteria The search criteria.
	 * @param int           $per_page The number of rows to request from the source.
	 *
	 * @return Base|null The source, or null when the field is not a resolvable lookup field.
	 */
	private function source( FieldCriteria $criteria, int $per_page ): ?Base {
		$field = $this->field( $criteria->form_id(), $criteria->field_id() );
		if ( ! $field instanceof GF_Field || 'lookup' !== $field->type || ! class_exists( Factory::class ) ) {
			return null;
		}

		$type   = (string) ( $field->lookupSourceType ?? GFEntries::get_source_type() );
		$common = [
			'search_term'    => $criteria->search(),
			'page'           => 1,
			'per_page'       => max( 1, $per_page ),
			'sort_direction' => $field->sortDirection ?? 'ASC',
			'storage_format' => $field->storageFormat ?? 'id',
			'unique_results' => $field->uniqueResults ?? true,
		];

		switch ( $type ) {
			case WPUsers::get_source_type():
				$params = $common + [
						'role'          => $field->lookupUserGroup ?? '',
						'orderby'       => $field->orderby ?? 'display_name',
						'display_value' => $field->displayValue ?? 'display_name',
					];
				break;
			case PostTypes::get_source_type():
				$params = $common + [
						'post_type'     => $field->lookupPostType ?? 'post',
						'orderby'       => $field->orderby ?? 'display_name',
						'display_value' => $field->displayValue ?? 'display_name',
					];
				break;
			case GFEntries::get_source_type():
			default:
				$type   = GFEntries::get_source_type();
				$params = $common + [
						'form_id'        => (int) ( $field->lookupFormId ?? 0 ),
						'field_id'       => (int) ( $field->lookupFieldId ?? 0 ),
						'limit_to_user'  => $field->limitToCurrentUser ?? false,
						'choice_display' => $field->choiceDisplay ?? 'text',
					];
				break;
		}

		return Factory::create( $type, $params );
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
