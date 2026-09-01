<?php
/**
 * @package     GravityKit\GravityView\Entry
 * @license     GPL2+
 * @since       1.15
 * @since       3.0.0 Migrated to GravityKit\GravityView\Entry namespace.
 * @author      Katz Web Services, Inc.
 * @link        http://www.gravitykit.com
 * @copyright   Copyright 2016, Katz Web Services, Inc.
 */

namespace GravityKit\GravityView\Entry;

use GFAPI;
use GFFormsModel;
use GravityKit\GravityView\Utils\Assets;
use WP_Error;

use function gravityview;

/**
 * Class EntryNotes
 *
 * Utility class for entry notes management.
 *
 * @since 1.15
 * @since 3.0.0 Migrated to GravityKit\GravityView\Entry namespace.
 */
class EntryNotes {

	/**
	 * Whether hooks have been added.
	 *
	 * Prevents double-registration when both PSR-4 autoload and legacy
	 * include paths trigger instantiation.
	 *
	 * @since 3.0.0
	 * @var bool
	 */
	private static $hooks_added = false;

	/**
	 * EntryNotes constructor.
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * @since 1.15
	 */
	private function add_hooks() {
		if ( self::$hooks_added ) {
			return;
		}

		add_filter( 'gform_notes_avatar', [ 'GravityView_Entry_Notes', 'filter_avatar' ], 10, 2 );

		self::$hooks_added = true;
	}


	/**
	 * Alias for GFFormsModel::add_note() with default note_type of 'gravityview'
	 *
	 * @see GFFormsModel::add_note()
	 *
	 * @since 1.15
	 * @since 1.17 Added return value
	 *
	 * @param int    $lead_id ID of the Entry
	 * @param int    $user_id ID of the user creating the note
	 * @param string $user_name User name of the user creating the note
	 * @param string $note Note content.
	 * @param string $note_type Type of note. Default: `gravityview`
	 *
	 * @return int|\WP_Error Note ID, if success. WP_Error with $wpdb->last_error message, if failed.
	 */
	public static function add_note( $lead_id, $user_id, $user_name, $note = '', $note_type = 'gravityview' ) {
		global $wpdb;

		$default_note = [
			'lead_id'   => 0,
			'user_id'   => 0,
			'user_name' => '',
			'note'      => '',
			'note_type' => 'gravityview',
		];

		/**
		 * Modify note values before added using GFFormsModel::add_note().
		 *
		 * @since 1.15.2
		 *
		 * @see GFFormsModel::add_note()
		 *
		 * @param array $note Array with `lead_id`, `user_id`, `user_name`, `note`, and `note_type` key value pairs.
		 */
		$note = apply_filters( 'gravityview/entry_notes/add_note', compact( 'lead_id', 'user_id', 'user_name', 'note', 'note_type' ) );

		// Make sure the keys are all set
		$note = wp_parse_args( $note, $default_note );

		$entry_id     = (int) $note['lead_id'];
		$user_id      = (int) $note['user_id'];
		$user_name    = esc_attr( $note['user_name'] );
		$note_content = $note['note'];
		$note_type    = esc_attr( $note['note_type'] );

		// Call directly instead of through GFAPI::add_note() alias.
		GFFormsModel::add_note( $entry_id, $user_id, $user_name, $note_content, $note_type );

		// If last_error is empty string, there was no error.
		if ( empty( $wpdb->last_error ) ) {
			$return = $wpdb->insert_id;
		} else {
			$return = new WP_Error( 'gravityview-add-note', $wpdb->last_error );
		}

		return $return;
	}

	/**
	 * Alias for GFFormsModel::delete_note()
	 *
	 * @see GFFormsModel::delete_note()
	 * @param int $note_id Entry note ID
	 */
	public static function delete_note( $note_id ) {
		GFFormsModel::delete_note( $note_id );
	}

	/**
	 * Delete an array of notes
	 * Alias for GFFormsModel::delete_notes()
	 *
	 * @todo Write more efficient delete note method using SQL
	 * @param int[] $note_ids Array of entry note ids
	 */
	public static function delete_notes( $note_ids = [] ) {

		if ( ! is_array( $note_ids ) ) {

			gravityview()->log->error( 'Note IDs not an array. Not processing delete request.', [ 'data' => $note_ids ] );

			return;
		}

		GFFormsModel::delete_notes( $note_ids );
	}

	/**
	 * Delete notes, but only those that belong to a specific entry.
	 *
	 * Filters the requested note IDs down to those that actually belong to
	 * `$entry_id` before deleting, so a caller authorized for one entry cannot
	 * delete another entry's notes by passing arbitrary IDs.
	 *
	 * @since 3.0.1
	 *
	 * @param int   $entry_id The entry the notes must belong to.
	 * @param int[] $note_ids Requested note IDs.
	 *
	 * @return int[] The note IDs that were deleted (those of `$note_ids` belonging to the entry).
	 */
	public static function delete_notes_for_entry( $entry_id, $note_ids ) {

		$requested   = array_map( 'absint', (array) $note_ids );
		$entry_notes = self::get_notes( $entry_id );
		$allowed_ids = $entry_notes ? array_map( 'absint', wp_list_pluck( $entry_notes, 'id' ) ) : [];
		$to_delete   = array_values( array_intersect( $requested, $allowed_ids ) );

		if ( $to_delete ) {
			self::delete_notes( $to_delete );
		}

		return $to_delete;
	}

	/**
	 * Alias for GFFormsModel::get_lead_notes()
	 *
	 * @see GFFormsModel::get_lead_notes
	 * @param int $entry_id Entry to get notes for
	 *
	 * @return \stdClass[]|null Integer-keyed array of note objects
	 */
	public static function get_notes( $entry_id ) {

		$notes = GFFormsModel::get_lead_notes( $entry_id );

		/**
		 * Modify the notes array for an entry.
		 *
		 * @since 1.15
		 *
		 * @param \stdClass[]|null $notes Integer-keyed array of note objects.
		 * @param int             $entry_id Entry to get notes for.
		 */
		$notes = apply_filters( 'gravityview/entry_notes/get_notes', $notes, $entry_id );

		return $notes;
	}

	/**
	 * Get a single note by note ID
	 *
	 * @since 1.17
	 * @since 3.0.0 Deprecated in favor of GFAPI::get_note()
	 *
	 * @deprecated 3.0.0
	 *
	 * @param int $note_id The ID of the note in the `{prefix}_rg_lead_notes` table
	 *
	 * @return object|false False if not found; note object otherwise.
	 */
	public static function get_note( $note_id ) {

		_deprecated_function( 'GravityView_Entry_Notes::get_note', '3.0.0', 'GFAPI::get_note()' );

		$note = GFAPI::get_note( $note_id );

		if ( is_wp_error( $note ) ) {
			return false;
		}

		return $note;
	}

	/**
	 * Use the GravityView avatar for notes created by GravityView
	 * Note: The function is static so that it's easier to remove the filter: `remove_filter( 'gform_notes_avatar', array( 'GravityView_Entry_Notes', 'filter_avatar' ) );`
	 *
	 * @since 1.15
	 * @param string $avatar Avatar image, if available. 48px x 48px by default.
	 * @param object $note Note object with id, user_id, date_created, value, note_type, user_name, user_email vars.
	 * @return string Possibly-modified avatar.
	 */
	public static function filter_avatar( $avatar = '', $note = null ) {

		if ( 'gravityview' === $note->note_type && -1 === (int) $note->user_id ) {
			$avatar = sprintf( '<img src="%s" width="48" height="48" alt="GravityView" class="avatar avatar-48 gravityview-avatar" />', esc_url_raw( Assets::url( 'images/floaty-avatar.png' ) ) );
		}

		return $avatar;
	}
}
