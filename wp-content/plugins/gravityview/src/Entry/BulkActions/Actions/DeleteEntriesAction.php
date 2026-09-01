<?php
/**
 * Frontend delete entries bulk action.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

use GravityKit\GravityView\Entry\BulkActions\Config;
use GravityKit\GravityView\View\View;
use GravityView_Delete_Entry;
use GVCommon;
use WP_Error;

/**
 * Deletes selected entries.
 *
 * @since 3.0.0
 */
final class DeleteEntriesAction implements BulkAction {
	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	public function key() {
		return 'delete';
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	public function config() {
		return [
			'label'              => __( 'Delete Entries', 'gk-gravityview' ),
			'callback'           => [ $this, 'process' ],
			'available_callback' => [ $this, 'is_available' ],
			'background'         => [
				'enabled'             => true,
				'completion_behavior' => Config::BACKGROUND_COMPLETE_RELOAD_LINK,
				'complete_callback'   => [ $this, 'complete' ],
			],
			'lock'               => true,
			'confirmation'       => [
				'enabled'               => true,
				'title'                 => __( 'Delete selected entries?', 'gk-gravityview' ),
				'title_singular'        => __( 'Delete selected entry?', 'gk-gravityview' ),
				'message'               => __( 'This will delete the selected entries. This cannot be undone.', 'gk-gravityview' ),
				'message_singular'      => __( 'This will delete the selected entry. This cannot be undone.', 'gk-gravityview' ),
				'action_label'          => __( 'Delete Entries', 'gk-gravityview' ),
				'action_label_singular' => __( 'Delete Entry', 'gk-gravityview' ),
			],
		];
	}

	/**
	 * Deletes selected entries.
	 *
	 * @since 3.0.0
	 *
	 * @param int[] $entry_ids Entry IDs.
	 * @param array $entries   Entries keyed by ID.
	 * @param View  $view      View context.
	 *
	 * @return array|WP_Error
	 */
	public function process( array $entry_ids, array $entries, View $view ) {
		$processed = 0;
		$failed    = 0;
		$deleter   = GravityView_Delete_Entry::getInstance();

		foreach ( $entries as $entry ) {
			if ( ! GravityView_Delete_Entry::check_user_cap_delete_entry( $entry, [], $view ) ) {
				++$failed;
				continue;
			}

			$result = $deleter->delete_or_trash_entry( $entry, $view->ID );

			if ( is_wp_error( $result ) ) {
				++$failed;
				continue;
			}

			++$processed;
		}

		if ( ! $processed && $failed ) {
			return new WP_Error( 'gravityview_bulk_delete_failed', __( 'You do not have permission to delete the selected entries.', 'gk-gravityview' ) );
		}

		return [
			'processed' => $processed,
			'failed'    => $failed,
			'message'   => ResultMessageFormatter::processed(
				$processed,
				$failed,
				/* translators: [count] is the number of entries deleted. */
				__( '[count] entries deleted.', 'gk-gravityview' ),
				/* translators: [count] is the number of entries deleted. */
				__( '[count] entry deleted.', 'gk-gravityview' )
			),
		];
	}

	/**
	 * Builds the final background result message from cumulative counts.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View context.
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param array  $context    Background context.
	 *
	 * @return array
	 */
	public function complete( View $view, $action_key, array $action, array $context ) {
		unset( $view, $action_key, $action );

		return [
			'notice' => [
				'message' => ResultMessageFormatter::processed(
					(int) ( $context['processed'] ?? 0 ),
					(int) ( $context['failed'] ?? 0 ),
					/* translators: [count] is the number of entries deleted. */
					__( '[count] entries deleted.', 'gk-gravityview' ),
					/* translators: [count] is the number of entries deleted. */
					__( '[count] entry deleted.', 'gk-gravityview' )
				),
			],
		];
	}

	/**
	 * Whether the current user may see the Delete Entries action for the View.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view   View.
	 * @param array $action Action configuration.
	 *
	 * @return bool
	 */
	public function is_available( View $view, array $action = [] ) {
		return self::current_user_can_delete_entries( $view, $action );
	}

	/**
	 * Whether the current user may delete entries in the View.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view   View.
	 * @param array $action Action configuration.
	 *
	 * @return bool
	 */
	public static function current_user_can_delete_entries( View $view, array $action = [] ) {
		if ( GVCommon::has_cap( [ 'gravityforms_delete_entries', 'gravityview_delete_others_entries' ] ) ) {
			return true;
		}

		if ( $view->settings->get( 'user_delete' ) && GVCommon::has_cap( 'gravityview_delete_entries' ) ) {
			return true;
		}

		/**
		 * Filters whether the Delete Entries action should appear without a broad delete capability.
		 *
		 * This affects action visibility only. Processing still checks delete permission for
		 * each entry before deleting or trashing it.
		 *
		 * @since 3.0.0
		 *
		 * @param bool  $available Whether the action should appear.
		 * @param int   $view_id   View ID.
		 * @param View  $view      View.
		 * @param array $action    Action configuration.
		 */
		return (bool) apply_filters( 'gk/gravityview/bulk-actions/delete-action-available', false, (int) $view->ID, $view, $action );
	}
}
