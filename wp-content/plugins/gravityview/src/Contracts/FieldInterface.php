<?php
/**
 * Field Interface for the PSR-4 namespace migration.
 *
 * Defines the core contract for Field entities in GravityView.
 * Any class that represents a Field must implement this interface.
 *
 * @package GravityKit\GravityView\Contracts
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Contracts;

/**
 * Contract for Field entities.
 *
 * A Field represents a single data point within a View, such as a
 * form field value, custom content, or computed value. This interface
 * defines the essential behaviors that any Field implementation must provide.
 *
 * @since 3.0.0
 */
interface FieldInterface {

	/**
	 * Returns the field label for display.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View|null    $view    Optional View context.
	 * @param \GV\Source|null   $source  Optional data source.
	 * @param \GV\Entry|null   $entry   Optional entry context.
	 * @param \GV\Request|null $request Optional request context.
	 *
	 * @return string The display label.
	 */
	public function get_label( $view = null, $source = null, $entry = null, $request = null );

	/**
	 * Returns the field value.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View|null    $view    Optional View context.
	 * @param \GV\Source|null   $source  Optional data source.
	 * @param \GV\Entry|null   $entry   Optional entry context.
	 * @param \GV\Request|null $request Optional request context.
	 *
	 * @return mixed The field value.
	 */
	public function get_value( $view = null, $source = null, $entry = null, $request = null );

	/**
	 * Checks whether this field is visible in the given View context.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View|null $view Optional View context.
	 *
	 * @return bool True if visible.
	 */
	public function is_visible( $view = null );

	/**
	 * Returns the field configuration as an array.
	 *
	 * @since 3.0.0
	 *
	 * @return array The field configuration.
	 */
	public function as_configuration();

	/**
	 * Updates the field configuration.
	 *
	 * @since 3.0.0
	 *
	 * @param array $configuration The new configuration values.
	 *
	 * @return void
	 */
	public function update_configuration( array $configuration );
}
