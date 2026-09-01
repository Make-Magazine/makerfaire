<?php
/**
 * View Interface for the PSR-4 namespace migration.
 *
 * Defines the core contract for View entities in GravityView.
 * Any class that represents a View must implement this interface.
 *
 * @package GravityKit\GravityView\Contracts
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Contracts;

/**
 * Contract for View entities.
 *
 * A View is a configured display of form entries, backed by a WordPress
 * custom post type ('gravityview'). This interface defines the essential
 * behaviors that any View implementation must provide.
 *
 * @since 3.0.0
 */
interface ViewInterface {

	/**
	 * Returns the View ID.
	 *
	 * @since 3.0.0
	 *
	 * @return int|null The View ID, or null if not yet persisted.
	 */
	public function get_id();

	/**
	 * Returns the backing WordPress post object.
	 *
	 * @since 3.0.0
	 *
	 * @return \WP_Post|null The WP_Post, or null if not available.
	 */
	public function get_post();

	/**
	 * Retrieves the entries for this View.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\Request|null $request Optional request context.
	 *
	 * @return \GV\Entry_Collection The collection of entries.
	 */
	public function get_entries( $request = null );

	/**
	 * Checks whether this View can be rendered in the given context.
	 *
	 * @since 3.0.0
	 *
	 * @param string[]|null    $context Optional rendering context.
	 * @param \GV\Request|null $request Optional request context.
	 *
	 * @return bool|\WP_Error True if renderable, WP_Error otherwise.
	 */
	public function can_render( $context = null, $request = null );

	/**
	 * Returns the unique anchor ID for the View container.
	 *
	 * @since 3.0.0
	 *
	 * @return string The anchor ID string.
	 */
	public function get_anchor_id();
}
