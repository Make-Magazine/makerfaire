<?php
/**
 * Entry Interface for the PSR-4 namespace migration.
 *
 * Defines the core contract for Entry entities in GravityView.
 * Any class that represents an Entry must implement this interface.
 *
 * @package GravityKit\GravityView\Contracts
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Contracts;

/**
 * Contract for Entry entities.
 *
 * An Entry represents a single form submission that can be displayed
 * within a View. This interface defines the essential behaviors that
 * any Entry implementation must provide.
 *
 * @since 3.0.0
 */
interface EntryInterface {

	/**
	 * Returns the Entry ID.
	 *
	 * @since 3.0.0
	 *
	 * @return int|string|null The Entry ID, or null if not available.
	 */
	public function get_id();

	/**
	 * Returns the backing entry object as an array.
	 *
	 * @since 3.0.0
	 *
	 * @return array The raw entry data.
	 */
	public function as_entry();

	/**
	 * Returns the permalink to this entry in the given View context.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View|null    $view    Optional View context.
	 * @param \GV\Request|null $request Optional request context.
	 * @param bool             $track_directory Whether to track directory context.
	 *
	 * @return string The entry permalink URL.
	 */
	public function get_permalink( $view = null, $request = null, $track_directory = true );

	/**
	 * Checks whether the current user can access this entry.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View         $view    The View context.
	 * @param \GV\Request|null $request Optional request context.
	 *
	 * @return bool|\WP_Error True if accessible, WP_Error otherwise.
	 */
	public function check_access( $view, $request = null );

	/**
	 * Checks whether this is a multi-entry (joined entry).
	 *
	 * @since 3.0.0
	 *
	 * @return bool True if this is a multi-entry.
	 */
	public function is_multi();
}
