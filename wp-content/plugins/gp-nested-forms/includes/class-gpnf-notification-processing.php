<?php

class GPNF_Notification_Processing {

	private static $instance = null;

	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {

		add_filter( 'gform_disable_notification', array( $this, 'should_disable_notification' ), 10, 4 );
		add_filter( 'gform_entry_post_save', array( $this, 'maybe_send_child_notifications' ), 11, 2 );
		add_filter( 'gform_notification', array( $this, 'add_notification_filters' ), 10 );
		add_filter( 'gform_notification', array( $this, 'complicate_parent_merge_tag' ), 10 );

		add_action( 'gform_dropbox_post_upload', array( $this, 'send_child_entry_notifications_for_dropbox' ), 10, 3 );

	}

	public function add_notification_filters( $notification ) {
		remove_filter( 'gform_replace_merge_tags', array( gpnf_parent_merge_tag(), 'parse_parent_merge_tag' ), 5 );
		add_filter( 'gform_replace_merge_tags', array( gpnf_parent_merge_tag(), 'parse_parent_merge_tag' ), 5, 7 );

		return $notification;
	}

	/**
	 * @param $notification
	 *
	 * This changes the merge tag so it won't be caught by the default Gravity Forms {FIELD_LABEL:FIELD_ID} pattern.
	 *
	 * @return mixed
	 */
	public function complicate_parent_merge_tag( $object ) {
		if ( is_scalar( $object ) ) {
			return $object;
		}

		foreach ( $object as $prop => $value ) {
			if ( is_array( $value ) ) {
				$object[ $prop ] = $this->complicate_parent_merge_tag( $value );
			} elseif ( is_string( $value ) ) {
				$object[ $prop ] = preg_replace( '/\{Parent:(.*?)\}/i', '{%GPNF:Parent:$1%}', $value );
			}
		}

		return $object;
	}

	/**
	 * Dropbox disables default notifications and sends them itself after its processed its feeds. The issue is that it
	 * only sends notifications after processing the last feed on the assumption that all feeds will be for the same entry.
	 *
	 * With Nested Forms, multiple child entries will be processed per runtime so this logic doesn't hold up.
	 *
	 * Let's disable notifications triggered by Dropbox in the `should_disable_notification()` method below and send them
	 * ourselves in this method.
	 */
	public function send_child_entry_notifications_for_dropbox( $feed, $entry, $form ) {

		/**
		 * Wait for the last feed to be processed before sending notifications so we can process all child entries at once.
		 * This accounts for the scenario where the child form may have multiple feeds configured which would result in
		 * this method being called multiple times for each child entry.
		 */
		if ( ! rgpost( 'is_last_feed' ) ) {
			return;
		}

		$parent_entry_id = rgar( $entry, GPNF_Entry::ENTRY_PARENT_KEY );
		if ( ! $parent_entry_id ) {
			return;
		}

		$parent_entry = GFAPI::get_entry( $parent_entry_id );
		if ( ! $parent_entry ) {
			return;
		}

		$parent_entry = new GPNF_Entry( $parent_entry );

		foreach ( $parent_entry->get_child_entries() as $child_entry ) {
			// Get entry directly from database to ensure the correct Dropbox links are present.
			GFCommon::send_form_submission_notifications( $form, $child_entry );
		}

		// We handle parent notifications ourselves as well.
		$parent_form = GFAPI::get_form( $parent_entry->form_id );
		GFCommon::send_form_submission_notifications( $parent_form, $parent_entry->get_entry() );

	}

	public function should_disable_notification( $value, $notification, $form, $entry ) {

		if ( $notification['event'] != 'form_submission' ) {
			return $value;
		}

		if ( gp_nested_forms()->is_nested_form_submission() ) {
			$parent_form       = GFAPI::get_form( gp_nested_forms()->get_parent_form_id() );
			$nested_form_field = gp_nested_forms()->get_posted_nested_form_field( $parent_form );

			return ! $this->should_send_notification( 'child', $notification, $parent_form, $nested_form_field, $entry, $form );
		}

		$parent_form_id = rgar( $entry, GPNF_Entry::ENTRY_PARENT_FORM_KEY );

		// With one exception (Dropbox), we don't need to evaluate notifications for child entries on parent form submissions.
		if ( ! $parent_form_id ) {
			if ( function_exists( 'gf_dropbox' ) ) {
				/**
				 * Check if a Dropbox feed is being processed for any child form attached to the current parent form. If so,
				 * disable the parent notification to avoid a parent notification that contains pre-Dropboxed file URLs in
				 * child entries.
				 */
				$nested_form_fields = GFCommon::get_fields_by_type( $form, gp_nested_forms()->field_type );
				foreach ( $nested_form_fields as $nested_form_field ) {
					// If the Dropbox add-on has disabled notifications for a given child form, we know that a feed is being processed for that form.
					if ( has_filter( "gform_disable_notification_{$nested_form_field->gpnfForm}", array( gf_dropbox(), 'disable_notification' ) ) ) {
						return true;
					}
				}
			}

			return $value;
		}

		// Disable notifications triggered by Dropbox for child entries. We'll handle them ourselves. See `send_child_entry_notifications_for_dropbox()` above.
		if ( rgpost( 'action' ) === 'gform_dropbox_upload' && ! doing_action( 'gform_dropbox_post_upload' ) ) {
			return true;
		}

		$parent_form       = GFAPI::get_form( $parent_form_id );
		$nested_form_field = GFFormsModel::get_field( $parent_form, rgar( $entry, GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY ) );

		return ! $this->should_send_notification( 'parent', $notification, $parent_form, $nested_form_field, $entry, $form );
	}

	public function maybe_send_child_notifications( $entry, $form ) {

		if ( ! gp_nested_forms()->has_nested_form_field( $form ) ) {
			return $entry;
		}

		$parent_entry = new GPNF_Entry( $entry );
		if ( ! $parent_entry->has_children() ) {
			return $entry;
		}

		$child_entries = $parent_entry->get_child_entries();
		if ( ! $child_entries ) {
			return $entry;
		}

		foreach ( $child_entries as $child_entry ) {
			$child_form = gp_nested_forms()->get_nested_form( $child_entry['form_id'] );

			GFCommon::send_form_submission_notifications( $child_form, $child_entry );
		}

		return $entry;

	}

	public function should_send_notification( $context, $notification, $parent_form, $nested_form_field, $entry, $child_form ) {

		$should_send_notification = $context === 'parent';

		/**
		 * Indicate whether a notification should be sent by context (parent or child submission).
		 *
		 * @since 1.0-beta-4.10
		 *
		 * @param bool   $should_send_notification Whether the notification should be sent for the given context.
		 * @param array  $notification             The notification object.
		 * @param string $context                  The current context for which notifications are being processed; 'parent' is a parent form submission; 'child' is a nested form submission.
		 * @param array  $parent_form              The parent form object.
		 * @param array  $nested_form_field        The field object of the Nested Form field.
		 * @param array  $entry                    The current entry for which feeds are being processed.
		 * @param array  $child_form               The child form object.
		 */
		$should_send_notification = gf_apply_filters( array(
			'gpnf_should_send_notification',
			$parent_form['id'],
			$nested_form_field->id,
		), $should_send_notification, $notification, $context, $parent_form, $nested_form_field, $entry, $child_form );

		return $should_send_notification;
	}

}

function gpnf_notification_processing() {
	return GPNF_Notification_Processing::get_instance();
}
