<?php

add_filter( 'gform_notification_events', 'gv_revisions_notification_events', 10, 2 );

/**
 * This is controlled via the `gravityview/entry-revisions/send-notifications`
 * filter inside `GV_Entry_Revisions::add_revision`, which defaults to true.
 *
 * @since 1.0
 * @since 1.2 Allowed passing the entry as an array.
 *
 * @param array $form The form array.
 * @param int|array $entry Entry array or ID.
 * @param array $original_entry The entry that is now being stored as a revision.
 */
function gv_revisions_send_notifications( $form = array(), $entry_or_id = array(), $original_entry = array() ) {
	remove_action( 'gform_after_update_entry', __FUNCTION__, 10, 3 ); // Only run once

	if ( empty( $entry_or_id ) ) {
		GravityKitFoundation::logger()->debug( __FUNCTION__ . ': No entry id passed.' );
		return;
	}

	if ( is_array( $entry_or_id ) && empty( $entry_or_id['id'] ) ) {
		GravityKitFoundation::logger()->debug( __FUNCTION__ . ': No entry id passed.' );
		return;
	}

	$entry = is_array( $entry_or_id ) ? $entry_or_id : GFAPI::get_entry( $entry_or_id );

	if ( is_wp_error( $entry ) && ! is_array( $entry_or_id ) ) {
		GravityKitFoundation::logger()->debug( __FUNCTION__ . ': Entry not found at ID #' . $entry_or_id );
		return;
	}

	$data = array(
		'revision' => $original_entry
	);

	// Prevent notifications from affecting GravityEdit AJAX response.
	_gk_gravityrevisions_ob_start();

	GFAPI::send_notifications( $form, $entry, 'gravityview/entry-revisions/gform_after_update_entry', $data );

	// Stop preventing output.
	_gk_gravityrevisions_ob_get_clean();
}

/**
 * Allow custom notification events to be added.
 *
 * @since 1.0
 *
 * @param array $notification_events The notification events.
 * @param array $form The current form.
 */
function gv_revisions_notification_events( $notification_events = array(), $form = array() ) {

	$notification_events['gravityview/entry-revisions/gform_after_update_entry'] = esc_html_x( 'Entry is updated, revision is saved', 'The title for an event in a notifications drop down list.', 'gk-gravityrevisions' );

	return $notification_events;
}
