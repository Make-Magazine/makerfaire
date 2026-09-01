<?php
/**
 * Frontend disapprove bulk action.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

use GravityView_Entry_Approval_Status;

/**
 * Disapproves selected entries.
 *
 * @since 3.0.0
 */
final class DisapproveAction extends ApprovalStatusAction {
	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	public function key() {
		return 'disapprove';
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function label() {
		return GravityView_Entry_Approval_Status::get_string( 'disapproved', 'action' );
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function status() {
		return GravityView_Entry_Approval_Status::DISAPPROVED;
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function plural_message() {
		/* translators: [count] is the number of entries disapproved. */
		return __( '[count] entries disapproved.', 'gk-gravityview' );
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function single_message() {
		/* translators: [count] is the number of entries disapproved. */
		return __( '[count] entry disapproved.', 'gk-gravityview' );
	}
}
