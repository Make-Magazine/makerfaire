<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\Field\Source;

use GravityKit\AdvancedFilter\QueryFilters\Querying\Field\FieldChoice;
use GravityKit\AdvancedFilter\QueryFilters\Querying\Field\FieldCriteria;

/**
 * Resolves the choices of a single Gravity Forms field from one kind of origin.
 *
 * @since 2.14.0
 */
interface ChoiceSource {
	/**
	 * Whether this source claims the given field.
	 *
	 * @since 2.14.0
	 *
	 * @param int    $form_id  The owning form ID.
	 * @param string $field_id The field ID.
	 *
	 * @return bool True when this source resolves the field's choices.
	 */
	public function supports( int $form_id, string $field_id ): bool;

	/**
	 * Whether $other is the same kind of source in the same configuration, making re-registration redundant.
	 *
	 * @since 2.14.0
	 *
	 * @param ChoiceSource $other The source to compare against.
	 *
	 * @return bool True when the two are interchangeable.
	 */
	public function equals( ChoiceSource $other ): bool;

	/**
	 * Returns the choices matching the criteria.
	 *
	 * @since 2.14.0
	 *
	 * @param FieldCriteria $criteria The search criteria.
	 *
	 * @return FieldChoice[] The matching choices.
	 */
	public function search( FieldCriteria $criteria ): array;

	/**
	 * Counts the choices matching the criteria.
	 *
	 * Returns the exact count when it can be computed cheaply. When no efficient count exists, the
	 * source *may* stop once it reaches the criteria's threshold and return at least that. This is
	 * never a hard cap! The caller can still tell the count clears the threshold without counting the
	 * whole set.
	 *
	 * @since 2.14.0
	 *
	 * @param FieldCriteria $criteria The search criteria, carrying the optional count threshold.
	 *
	 * @return int The exact count, or at least the criteria's threshold when exact counting is infeasible.
	 */
	public function count( FieldCriteria $criteria ): int;

	/**
	 * Whether the field should be served by the search endpoint regardless of its choice count.
	 *
	 * @since 2.14.0
	 *
	 * @return bool True to force the endpoint and bypass the count.
	 */
	public function prefers_endpoint(): bool;

	/**
	 * Hydrates the criteria's explicit values into choices for label display.
	 *
	 * @since 2.14.0
	 *
	 * @param FieldCriteria $criteria The criteria carrying the values to hydrate.
	 *
	 * @return FieldChoice[] The hydrated choices.
	 */
	public function resolve( FieldCriteria $criteria ): array;
}
