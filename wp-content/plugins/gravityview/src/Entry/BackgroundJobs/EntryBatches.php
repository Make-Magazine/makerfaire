<?php
/**
 * Public facade for GravityView entry-batch background jobs.
 *
 * @package GravityKit\GravityView\Entry\BackgroundJobs
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BackgroundJobs;

use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\BackgroundJobs\Scheduler;

/**
 * Convenience API for scheduling background work against View entries.
 *
 * Third parties should use Foundation directly for generic background work.
 * Use this facade when the work needs GravityView to resolve entries from a
 * View result set.
 *
 * @since 3.0.0
 */
final class EntryBatches {
	/**
	 * Schedules a background job for selected View entries.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View       $view      View whose result set should be processed.
	 * @param EntrySelection $selection Entry selection descriptor.
	 * @param array          $args      Job options.
	 *
	 * @return array|\WP_Error
	 */
	public static function schedule( \GV\View $view, EntrySelection $selection, array $args ) {
		return Scheduler::instance()->schedule_entry_batch( $view, $selection, $args );
	}

	/**
	 * Creates an explicit entry-ID selection.
	 *
	 * @since 3.0.0
	 *
	 * @param array $entry_ids    Entry IDs.
	 * @param array $request_args Request arguments needed to rebuild the View result set.
	 *
	 * @return EntrySelection
	 */
	public static function selected( array $entry_ids, array $request_args = [] ): EntrySelection {
		return EntrySelection::from_entry_ids( $entry_ids, $request_args );
	}

	/**
	 * Creates a select-all selection.
	 *
	 * @since 3.0.0
	 *
	 * @param array $excluded_ids Entry IDs excluded from the select-all selection.
	 * @param array $request_args Request arguments needed to rebuild the View result set.
	 *
	 * @return EntrySelection
	 */
	public static function all( array $excluded_ids = [], array $request_args = [] ): EntrySelection {
		return EntrySelection::all( $excluded_ids, $request_args );
	}

	/**
	 * Reads a stored result snapshot.
	 *
	 * @since 3.0.0
	 *
	 * @param string $result_token Result token returned by schedule().
	 *
	 * @return array|null
	 */
	public static function result( string $result_token ): ?array {
		return ( new ResultStore() )->get( $result_token );
	}
}
