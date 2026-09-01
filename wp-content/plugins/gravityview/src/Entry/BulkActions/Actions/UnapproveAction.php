<?php
/**
 * Frontend reset approval bulk action.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

use GravityView_Entry_Approval_Status;

/**
 * Resets approval status for selected entries.
 *
 * @since 3.0.0
 */
final class UnapproveAction extends ApprovalStatusAction {
	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	public function key() {
		return 'unapprove';
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function label() {
		return GravityView_Entry_Approval_Status::get_string( 'unapproved', 'action' );
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function status() {
		return GravityView_Entry_Approval_Status::UNAPPROVED;
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function plural_message() {
		/* translators: [count] is the number of entries unapproved. */
		return __( '[count] entries unapproved.', 'gk-gravityview' );
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0
	 */
	protected function single_message() {
		/* translators: [count] is the number of entries unapproved. */
		return __( '[count] entry unapproved.', 'gk-gravityview' );
	}
}
