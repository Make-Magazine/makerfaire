<?php
/**
 * Form Interface for the PSR-4 namespace migration.
 *
 * Defines the core contract for Form entities in GravityView.
 * Any class that represents a Form must implement this interface.
 *
 * @package GravityKit\GravityView\Contracts
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Contracts;

/**
 * Contract for Form entities.
 *
 * A Form is the data source that provides entries to a View.
 * This interface defines the essential behaviors that any Form
 * implementation must provide, regardless of the backing form system
 * (e.g., Gravity Forms, internal sources).
 *
 * @since 3.0.0
 */
interface FormInterface {

	/**
	 * Returns the Form ID.
	 *
	 * @since 3.0.0
	 *
	 * @return int|null The Form ID, or null if not available.
	 */
	public function get_id();

	/**
	 * Retrieves all entries for this form.
	 *
	 * @since 3.0.0
	 *
	 * @return \GV\Entry_Collection The collection of entries.
	 */
	public function get_entries();
}
