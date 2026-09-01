<?php

namespace GravityKit\GravityView\Core;

use GFAPI;
use GVCommon;

use function gf_apply_filters;
use function gravityview;

/**
 * Endpoint responsible for firing off notifications.
 *
 * @since 3.0.0
 * @todo Create a Notifier (Interface and GF implementation) that can be replaced to aid with unit tests.
 */
final class Notifications {
	/**
	 * Passes along notification triggers to {@see GFAPI::send_notifications()}
	 *
	 * @internal
	 * @since 3.0.0
	 *
	 * @param int    $entry_id ID of entry being updated.
	 * @param string $event    Hook that triggered the notification. This is used as the key in the GF notifications
	 *                         array.
	 * @param array  $entry    The entry object.
	 */
	public static function send_notifications( int $entry_id = 0, string $event = '', array $entry = [] ): void {
		if ( ! $entry ) {
			$entry = GFAPI::get_entry( $entry_id );
		}

		if ( ! $entry || is_wp_error( $entry ) ) {
			gravityview()->log->error( 'Entry not found at ID #{entry_id}', [ 'entry_id' => $entry_id ] );

			return;
		}

		$form = GVCommon::get_form( $entry['form_id'] );

		if ( ! $form ) {
			gravityview()->log->error(
				'Form not found at ID #{form_id} for entry #{entry_id}',
				[
					'form_id'  => $entry['form_id'],
					'entry_id' => $entry_id,
				]
			);

			return;
		}

		$stored_notifications = $form['notifications'] ?? [];

		$filtered_form = gf_apply_filters( [ 'gform_pre_render', $form['id'] ], $form, false, [] );

		if ( is_array( $filtered_form ) && ! empty( $filtered_form['id'] ) ) {
			$form = $filtered_form;
		}

		// gform_pre_render is a display filter, so callbacks routinely rebuild the form. Gravity Forms
		// looks notifications up by ID, so a rebuild that drops or re-keys them silently sends nothing.
		$rebuilt = $form['notifications'] ?? null;

		// An empty array is a callback deliberately suppressing them; anything else is mangling.
		$source = is_array( $rebuilt ) ? $rebuilt : $stored_notifications;

		$notifications = [];

		foreach ( $source as $key => $notification ) {
			// Leave anything that is not a notification array alone; Gravity Forms skips it on its own.
			if ( ! is_array( $notification ) ) {
				$notifications[ $key ] = $notification;

				continue;
			}

			if ( ! isset( $notification['id'] ) || ! is_scalar( $notification['id'] ) ) {
				$notification['id'] = $key;
			}

			$notifications[ $notification['id'] ] = $notification;
		}

		$form['notifications'] = $notifications;

		// Force synchronous delivery for GV notification events. GV notifications
		// are triggered by admin actions (delete, approve) where the entry may no
		// longer exist by the time a background request processes the queue.
		$disable_async = static function () {
			return false;
		};

		add_filter( 'gform_is_asynchronous_notifications_enabled', $disable_async );

		try {
			GFAPI::send_notifications( $form, $entry, $event );
		} finally {
			remove_filter( 'gform_is_asynchronous_notifications_enabled', $disable_async );
		}
	}
}
