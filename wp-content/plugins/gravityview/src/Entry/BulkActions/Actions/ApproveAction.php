<?php
/**
 * Frontend approve bulk action.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

use GravityView_Entry_Approval_Status;

/**
 * Approves selected entries.
 *
 * @since 3.0.0
 */
final class ApproveAction extends ApprovalStatusAction {
	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	public function key() {
		return 'approve';
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function label() {
		return GravityView_Entry_Approval_Status::get_string( 'approved', 'action' );
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function status() {
		return GravityView_Entry_Approval_Status::APPROVED;
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function plural_message() {
		/* translators: [count] is the number of entries approved. */
		return __( '[count] entries approved.', 'gk-gravityview' );
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function single_message() {
		/* translators: [count] is the number of entries approved. */
		return __( '[count] entry approved.', 'gk-gravityview' );
	}
}
