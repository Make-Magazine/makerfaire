<?php
/**
 * Frontend approval bulk action base class.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

use GravityKit\GravityView\Entry\BulkActions\Config;
use GravityView_Entry_Approval;
use GVCommon;
use WP_Error;

/**
 * Base class for approval-status bulk actions.
 *
 * @since 3.0.0
 */
abstract class ApprovalStatusAction implements BulkAction {
	/**
	 * Returns the action label.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	abstract protected function label();

	/**
	 * Returns the approval status value to write.
	 *
	 * @since 3.0.0
	 *
	 * @return int
	 */
	abstract protected function status();

	/**
	 * Returns the plural processed message.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	abstract protected function plural_message();

	/**
	 * Returns the single processed message.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	abstract protected function single_message();

	/**
	 * Returns the action configuration consumed by the bulk action registry.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function config() {
		return [
			'label'      => $this->label(),
			'callback'   => [ $this, 'process' ],
			'capability' => 'gravityview_moderate_entries',
			'background' => [
				'enabled'             => true,
				'completion_behavior' => Config::BACKGROUND_COMPLETE_RELOAD_LINK,
				'complete_callback'   => [ $this, 'complete' ],
			],
		];
	}

	/**
	 * Updates approval status for selected entries.
	 *
	 * @since 3.0.0
	 *
	 * @param int[] $entry_ids Entry IDs.
	 * @param array $entries   Entries keyed by ID.
	 *
	 * @return array|WP_Error
	 */
	public function process( array $entry_ids, array $entries ) {
		$processed = 0;
		$failed    = 0;

		foreach ( $entries as $entry ) {
			if ( empty( $entry['id'] ) || ! GVCommon::has_cap( 'gravityview_moderate_entries', (int) $entry['id'] ) ) {
				++$failed;
				continue;
			}

			if ( GravityView_Entry_Approval::update_approved( (int) $entry['id'], $this->status(), (int) $entry['form_id'] ) ) {
				++$processed;
			} else {
				++$failed;
			}
		}

		if ( ! $processed && $failed ) {
			return new WP_Error( 'gravityview_bulk_approval_failed', __( 'The selected entries could not be updated.', 'gk-gravityview' ) );
		}

		return [
			'processed' => $processed,
			'failed'    => $failed,
			'message'   => ResultMessageFormatter::processed( $processed, $failed, $this->plural_message(), $this->single_message() ),
		];
	}

	/**
	 * Builds the final background result message from cumulative counts.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View $view       View context.
	 * @param string   $action_key Action key.
	 * @param array    $action     Action configuration.
	 * @param array    $context    Background context.
	 *
	 * @return array
	 */
	public function complete( \GV\View $view, $action_key, array $action, array $context ) {
		unset( $view, $action_key, $action );

		return [
			'notice' => [
				'message' => ResultMessageFormatter::processed(
					(int) ( $context['processed'] ?? 0 ),
					(int) ( $context['failed'] ?? 0 ),
					$this->plural_message(),
					$this->single_message()
				),
			],
		];
	}
}
