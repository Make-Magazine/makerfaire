<?php

class GPNF_Easy_Passthrough {

	private static $instance = null;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {

		add_filter( 'gpnf_can_user_edit_entry', array( $this, 'can_user_edit_entry' ), 10, 3 );

	}

	/**
	 * Check if the child entry that is about to be edited belongs to the parent entry that was populated by GPEP.
	 * Currently only supports entries populated via an EP token as this is the only use case where a user may not own
	 * the entry and be populating entries from a different session.
	 *
	 * @param $can_user_edit_entry
	 * @param $entry
	 * @param $current_user
	 *
	 * @return bool|mixed
	 */
	public function can_user_edit_entry( $can_user_edit_entry, $entry, $current_user ) {

		if ( ! is_callable( 'gp_easy_passthrough' ) ) {
			return $can_user_edit_entry;
		}

		$parent_entry_id = gform_get_meta( $entry['id'], GPNF_Entry::ENTRY_PARENT_KEY );
		if ( $parent_entry_id ) {
			$parent_entry = GFAPI::get_entry( $parent_entry_id );
			if ( ! is_wp_error( $parent_entry ) ) {
				$gpep_session = gp_easy_passthrough()->session_manager();
				if ( ! is_wp_error( $gpep_session ) ) {
					$gpep_entry_id = $gpep_session[ gp_easy_passthrough()->get_slug() . '_' . $parent_entry['form_id'] ];
					if ( $gpep_entry_id == $parent_entry_id ) {
						return true;
					}
				}
			}
		}

		return $can_user_edit_entry;
	}

}

function gpnf_easy_passthrough() {
	return GPNF_Easy_Passthrough::get_instance();
}
