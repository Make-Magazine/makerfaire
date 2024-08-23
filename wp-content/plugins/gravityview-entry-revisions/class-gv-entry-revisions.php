<?php

/**
 * Class GV_Entry_Revisions
 */
class GV_Entry_Revisions {

	/**
	 * The value for the 'status' column of the revision entry in GF
	 *
	 * @since 1.0
	 */
	const revision_status_key = 'gv-revision';

	/** @var GV_Entry_Revisions */
	static private $instance = null;

	/**
	 * GV_Entry_Revisions constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		if ( self::$instance ) {
			return;
		}

		$this->init();
	}

	/**
	 * Instantiates the class
	 *
	 * @since 1.0
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}


		return self::$instance;
	}

	/**
	 * Add hooks on the single entry screen
	 *
	 * @since 1.0
	 */
	public function init() {

		include_once GV_ENTRY_REVISIONS_DIR . 'includes/class-gv-entry-revisions-settings.php';
		include_once GV_ENTRY_REVISIONS_DIR . 'includes/functions.php';
		include_once GV_ENTRY_REVISIONS_DIR . 'includes/notifications.php';
		include_once GV_ENTRY_REVISIONS_DIR . 'includes/merge-tags.php';
		include_once GV_ENTRY_REVISIONS_DIR . 'includes/entry_list.php';
		include_once GV_ENTRY_REVISIONS_DIR . 'includes/entry_detail.php';

		add_action( 'gravityview-inline-edit/entry-updated', array( $this, 'inline_edit_update_entry' ), - 100, 5 );

		add_action( 'gform_post_update_entry', array( $this, 'gform_post_update_entry' ), - 100, 2 );

		add_action( 'gform_after_update_entry', array( $this, 'gform_after_update_entry' ), - 100, 3 );

		add_filter( 'gform_entry_meta', array( $this, 'modify_gform_entry_meta' ) );

		add_filter( 'gravityview_noconflict_scripts', array( $this, 'register_noconflict' ) );

		add_filter( 'gravityview_noconflict_styles', array( $this, 'register_noconflict' ) );

		add_filter( 'gform_noconflict_scripts', array( $this, 'register_noconflict' ) );

		add_filter( 'gform_noconflict_styles', array( $this, 'register_noconflict' ) );

		add_action( 'wp_print_scripts', array( $this, 'register_style' ), 1 );

		add_action( 'admin_init', array( $this, 'admin_init_restore_listener' ) );
	}

	public function register_style() {

		$css_dependencies = array();

		// Two stylesheets: one that includes WP revisions styles, other that doesn't
		$admin = is_admin() ? '-admin' : '';

		wp_register_style( 'gv-revisions', plugins_url( 'assets/css/entry-revisions' . $admin . '.css', GV_ENTRY_REVISIONS_FILE ), $css_dependencies );
	}

	/**
	 * Let us operate when GF no-conflict is enabled
	 *
	 * @since 1.0
	 *
	 * @param array $items Scripts or styles to exclude from no-conflict
	 *
	 * @return array
	 */
	public function register_noconflict( $items ) {

		$items[] = 'gv-revisions';

		return $items;
	}

	/**
	 * Returns the additional entry meta details added by this plugin
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	private function get_entry_meta() {

		$meta = array(
			'gv_revision_parent_id' => array(
				'label'             => __( 'Revision Parent Entry ID', 'gk-gravityrevisions' ),
				'is_numeric'        => true,
				'is_default_column' => false,
			),
			'gv_revision_date'      => array(
				'label'             => __( 'Revision Parent Date', 'gk-gravityrevisions' ),
				'is_numeric'        => true,
				'is_default_column' => false,
			),
			'gv_revision_date_gmt'  => array(
				'label'             => __( 'Revision Date (GMT)', 'gk-gravityrevisions' ),
				'is_numeric'        => true,
				'is_default_column' => false,
			),
			'gv_revision_user_id'   => array(
				'label'             => __( 'Revision Created By', 'gk-gravityrevisions' ),
				'is_numeric'        => true,
				'is_default_column' => false,
			),
			'gv_revision_changed'   => array(
				'label'             => __( 'Revision Changed Content', 'gk-gravityrevisions' ),
				'is_numeric'        => false,
				'is_default_column' => false,
			)
		);

		return $meta;
	}

	/**
	 * Returns the field IDs and meta keys that are not displayed in the diff table
	 *
	 * @since 1.0
	 *
	 * @return array Array of entry meta keys and field IDs to not display in the diff, for example [ 'id', 'date_updated', '1.2' ]
	 */
	private function get_diff_ignored_keys( $form = array() ) {

		$ignored_keys   = array_keys( $this->get_entry_meta() );
		$ignored_keys[] = 'id';
		$ignored_keys[] = 'status';
		$ignored_keys[] = 'date_updated';
		$ignored_keys[] = 'is_approved';

		/**
		 * @filter `gravityview/entry-revisions/diff-ignored-keys` Specify the field IDs and meta keys to not display in the diff table
		 * @since 1.0
		 * @param array $ignored_keys Array of field and meta keys, like [ 'id', 'date_updated', '1.2' ]
		 * @param array $form The form connected to the entry/revision diff being displayed
		 */
		$ignored_keys = apply_filters( 'gravityview/entry-revisions/diff-ignored-keys', $ignored_keys, $form );

		return $ignored_keys;
	}

	/**
	 * Updates Gravity Forms to fetch revisions with other entry details
	 *
	 * @since 1.0
	 *
	 * @param array $meta
	 *
	 * @return array
	 */
	public function modify_gform_entry_meta( $meta = array() ) {
		return $this->get_entry_meta() + $meta;
	}

	/**
	 * Handle entries modified using GFAPI
	 *
	 * @since 1.1
	 *
	 * @param array $entry
	 * @param array $original_entry
	 */
	public function gform_post_update_entry( $entry, $original_entry ) {

		$form = GFAPI::get_form( $entry['form_id'] );

		if ( ! $form ) {
			GravityKitFoundation::logger()->error( sprintf( '[%s] Not able to update entry #%d because form #%d is missing.', __METHOD__, $entry['id'], $form_id ) );
			return;
		}

		$this->gform_after_update_entry( $form, $entry['id'], $original_entry );
	}

	/**
	 * Handle edits made via Inline Edit
	 *
	 * @since 1.1
	 *
	 * @param bool|WP_Error $update_result  True: the entry has been updated by Gravity Forms or WP_Error if there was a problem
	 * @param array         $entry          The Entry Object that's been updated
	 * @param int           $form_id        The Form ID
	 * @param GF_Field|null $gf_field       The field that's been updated, or null if no field exists (for entry meta)
	 * @param array         $original_entry Original entry, before being updated
	 *
	 * @return bool $update_result, unmodified.
	 */
	public function inline_edit_update_entry( $update_result, $entry, $form_id, $gf_field, $original_entry = array() ) {

		if ( is_wp_error( $update_result ) ) {
			return $update_result;
		}

		// Requires Inline Edit 1.1 or later.
		if ( empty( $original_entry ) ) {
			return $update_result;
		}

		$form = GFAPI::get_form( $form_id );

		if ( ! $form ) {
			GravityKitFoundation::logger()->error( sprintf( '[%s] Not able to update entry #%d because form #%d is missing.', __METHOD__, $entry['id'], $form_id ) );
			return $update_result;
		}

		if ( ! $this->should_process_inline_edit( $form ) ) {
			return $update_result;
		}

		$this->gform_after_update_entry( $form, $entry['id'], $original_entry );

		return $update_result;
	}

	/**
	 * Checks whether to process Inline Edit updates based on the global default and per-form settings.
	 *
	 * @since 1.1
	 *
	 * @param array $form
	 *
	 * @return bool True: process; False: don't!
	 */
	private function should_process_inline_edit( $form ) {
		if ( ! GV_Entry_Revisions_Settings::is_gravityedit_activated() ) {
			return false;
		}

		$global_setting = GV_Entry_Revisions_Settings::GRAVITYEDIT_GLOBAL_SETTING_DEFAULT;

		if ( class_exists( 'GravityKitFoundation' ) && GravityKitFoundation::settings() ) {
			$global_setting = GravityKitFoundation::settings()->get_plugin_setting( 'gravityedit', GV_Entry_Revisions_Settings::GRAVITYEDIT_GLOBAL_SETTING );
		}

		$form_setting = rgar( $form, GV_Entry_Revisions_Settings::GRAVITYEDIT_FORM_SETTING);

		$form_setting = '' === $form_setting ? $global_setting : $form_setting;

		return (bool) $form_setting;
	}

	/**
	 * Fires after the Entry is updated from the entry detail page.
	 *
	 * @since 1.0
	 *
	 * @internal Do not use; this method may change
	 *
	 * @param array $form The form object for the entry.
	 * @param integer $lead ['id']     The entry ID.
	 * @param array $original_entry The entry object before being updated.
	 *
	 * @return void
	 */
	public function gform_after_update_entry( $form = array(), $entry_id = 0, $original_entry = array() ) {

		/**
		 * @filter `gravityview/entry-revisions/add-revision` Whether to add revisions for the entry
		 * @since 1.0.3
		 * @param bool $add_revision Should a revision be added?
		 * @param array $form The form object for the entry.
		 * @param integer $entry_id The entry ID that was updated
		 * @param array $original_entry The entry object before being updated
		 */
		$add_revision = apply_filters( 'gravityview/entry-revisions/add-revision', true, $entry_id, $form, $original_entry );

		if ( ! $add_revision ) {
			GravityKitFoundation::logger()->info( sprintf( '[%s] `gravityview/entry-revisions/add-revision` filter used to prevent adding revisions for entry #%d', __METHOD__, $entry_id ) );
			return;
		}

		$added = $this->add_revision( $entry_id, $original_entry );

		if ( ! $added ) {
			GravityKitFoundation::logger()->error( sprintf( '[%s] Not able to update entry #%d `date_updated` property', __METHOD__, $entry_id ) );
			return;
		}

		if( is_wp_error( $added ) ) {
			GravityKitFoundation::logger()->error( sprintf( '[%s] Not able to add revision for entry #%d: %s', __METHOD__, $entry_id, $added->get_error_message() ) );
			return;
		}


		// 2.4 will update date_updated already
		if ( version_compare( GFFormsModel::get_database_version(), '2.4', '>=' ) ) {
			return;
		}

		$updated = GFAPI::update_entry_property( $entry_id, 'date_updated', gmdate( 'Y-m-d H:i:s' ) );

		if ( ! $updated ) {
			GravityKitFoundation::logger()->error( sprintf( '[%s] Not able to update entry #%d `date_updated` property', __METHOD__, $entry_id ) );
			return;
		}

		GravityKitFoundation::logger()->info( sprintf( '[%s] Updated entry #%d `date_updated` property', __METHOD__, $entry_id ) );
	}

	/**
	 * Adds a revision for an entry
	 *
	 * @since 1.0
	 *
	 * @param int|array $entry_or_entry_id Current entry ID or current entry array
	 * @param array $revision_to_add Previous entry data to add as a revision
	 *
	 * @return WP_Error|true True: updated; WP_Error: Nothing changed, the entry was not found, or the entry isn't formatted correctly.
	 */
	public function add_revision( $entry_or_entry_id = 0, $revision_to_add = array() ) {

		$current_entry = $entry_or_entry_id;

		if ( is_numeric( $entry_or_entry_id ) ) {
			$current_entry = GFAPI::get_entry( $entry_or_entry_id );
		}

		if ( is_wp_error( $current_entry ) ) {
			GravityKitFoundation::logger()->error( __METHOD__ . ': Entry not found at ID #' . $entry_or_entry_id );

			return $current_entry;
		}

		if ( ! is_array( $current_entry ) ) {
			return new WP_Error( 'not_array', 'Current entry is not an array', $current_entry );
		}

		// Find the fields that changed
		$changed_fields = $this->get_modified_entry_fields( $revision_to_add, $current_entry );

		// Nothing changed
		if ( empty( $changed_fields ) ) {
			GravityKitFoundation::logger()->warning( sprintf( '[%s] Not adding revision for entry #%d (no fields changed)', __METHOD__, $entry_or_entry_id ) );

			return new WP_Error( 'identical', esc_html__( 'This revision is identical to the current entry.', 'gk-gravityrevisions' ) );
		}

		$revision_to_add['status'] = self::revision_status_key;

		$revision_id = GFAPI::add_entry( $revision_to_add );

		$revision_meta = array(
			'gv_revision_parent_id' => $current_entry['id'],
			'gv_revision_date'      => current_time( 'timestamp', 0 ),
			'gv_revision_date_gmt'  => current_time( 'timestamp', 1 ),
			'gv_revision_user_id'   => get_current_user_id(),
			'gv_revision_changed'   => $changed_fields,
		);

		foreach ( $revision_meta as $key => $value ) {
			gform_update_meta( $revision_id, $key, $value );
		}

		GravityKitFoundation::logger()->info( sprintf( '[%s] Added revision #%d for entry #%d', __METHOD__, $revision_id, $entry_or_entry_id ) );

		/**
		 * @filter `gravityview/entry-revisions/send-notifications` Whether to trigger or suppress send notifications.
		 * @param bool  Send or not. Default: true (send notifications)
		 * @param array $revision_to_add The new revision added.
		 * @param array $current_entry The replaced entry.
		 * @param array $changed_fields The new entry, with only the changed fields
		 */
		if ( apply_filters( 'gravityview/entry-revisions/send-notifications', true, $revision_to_add, $current_entry, $changed_fields ) ) {

			if( doing_action( 'gravityview-inline-edit/entry-updated' ) ) {
				$form = GFAPI::get_form( $current_entry['form_id'] );
				gv_revisions_send_notifications( $form, $current_entry['id'], $revision_to_add );
			} else {
				// Triggered in Gravity Forms and GravityView.
				add_action( 'gform_after_update_entry', 'gv_revisions_send_notifications', 10, 3 );
			}

		}

		return $revision_id;
	}


	/**
	 * Compares old entry array to new, return array of differences with the values of the new entry
	 *
	 * @param array $old
	 * @param array $new
	 *
	 * @return array array of differences, with keys preserved
	 */
	private function get_modified_entry_fields( $old = array(), $new = array() ) {

		$return = $new;

		foreach ( $old as $key => $old_value ) {
			// Gravity Forms itself uses == comparison
			if ( rgar( $new, $key ) == $old_value ) {
				unset( $return[ $key ] );
			}
		}

		return $return;
	}

	/**
	 * Returns an entry revision by ID
	 *
	 * @param int $revision_id
	 * @param int $entry_id Optional: Pass an entry ID to check to make sure the Revision ID matches the Entry ID
	 *
	 * @return array|WP_Error
	 */
	public function get_revision( $revision_id = 0, $entry_id = null ) {

		$revision = GFAPI::get_entry( $revision_id );

		if ( is_wp_error( $revision ) ) {

			GravityKitFoundation::logger()->error( sprintf( '%s: Revision #%s not found', __METHOD__, $revision_id ) );

			return new WP_Error( 'not_found', __( 'Revision not found', 'gk-gravityrevisions' ), array(
				'entry_id'    => $entry_id,
				'revision_id' => $revision_id
			) );
		}

		if ( self::revision_status_key !== $revision['status'] ) {

			GravityKitFoundation::logger()->error( sprintf( '%s: Entry #%s is not a revision', __METHOD__, $revision_id ) );

			return new WP_Error( 'not_revision', sprintf( 'Entry #%s is not a revision', $revision_id ), array( 'revision_id' => $revision_id ) );
		}

		if ( ! is_null( $entry_id ) ) {

			$entry_id_from_revision = gform_get_meta( $revision_id, 'gv_revision_parent_id' );

			if ( (int) $entry_id !== (int) $entry_id_from_revision ) {

				GravityKitFoundation::logger()->error( sprintf( '%s: Revision #%s not found', __METHOD__, $revision_id ) );

				return new WP_Error( 'mismatch', __( 'Revision not found', 'gk-gravityrevisions' ), array(
					'entry_id'    => $entry_id,
					'revision_id' => $revision_id
				) );
			}
		}

		return $revision;
	}

	/**
	 * Get all revisions connected to an entry
	 *
	 * @since 1.0
	 *
	 * @param int $entry_id
	 * @param string $return "entries" or "ids"
	 *
	 * @return array|int[]|WP_Error Empty array if none found. Array if found. WP_Error if no entry ID defined.
	 */
	public function get_revisions( $entry_id = 0, $return = 'entries', $sorting = array(), $paging = array() ) {

		if ( empty( $entry_id ) ) {
			return new WP_Error( 'empty_entry_id', 'Entry ID not defined' );
		}

		if ( ! is_array( $sorting ) ) {
			return new WP_Error( 'not_array', 'Sorting not an array' );
		}

		if ( ! is_array( $paging ) ) {
			return new WP_Error( 'not_array', 'Paging not an array' );
		}

		$sorting = wp_parse_args( $sorting, array(
			'key'        => 'id',
			'direction'  => 'DESC',
			'is_numeric' => true
		) );

		$paging = wp_parse_args( $paging, array(
			'offset'    => 0,
			'page_size' => 0,
		) );

		$search_criteria = array(
			'status'        => self::revision_status_key,
			'field_filters' => array(
				array(
					'key'   => 'gv_revision_parent_id',
					'value' => $entry_id
				),
			),
		);

		if ( 'entries' === $return ) {
			$revisions = GFAPI::get_entries( 0, $search_criteria, $sorting, $paging );
		} else {
			$revisions = GFAPI::get_entry_ids( 0, $search_criteria, $sorting, $paging );

			$revisions = array_map( 'absint', $revisions );
		}

		return $revisions;
	}

	/**
	 * Get the latest revision
	 *
	 * @param $entry_id
	 *
	 * @return array Empty array, if no revisions exist. Otherwise, last revision.
	 */
	public function get_latest_revision( $entry_id ) {

		$revisions = $this->get_revisions( $entry_id, 'entries', array( 'direction' => 'DESC' ), array( 'page_size' => 1 ) );

		if ( is_wp_error( $revisions ) ) {
			GravityKitFoundation::logger()->error( sprintf( 'Could not get latest revision: %s', $revisions->get_error_message() ) );

			return [];
		}

		if ( empty( $revisions ) ) {
			return array();
		}

		$revision = array_pop( $revisions );

		return $revision;
	}

	/**
	 * Get the latest revision
	 *
	 * @param $entry_id
	 *
	 * @return array Empty array, if no revisions exist. Otherwise, last revision.
	 */
	public function get_first_revision( $entry_id ) {

		$revisions = $this->get_revisions( $entry_id, 'entries', array( 'direction' => 'ASC' ), array( 'page_size' => 1 ) );

		if ( empty( $revisions ) || is_wp_error( $revisions ) ) {
			return array();
		}

		$revision = array_pop( $revisions );

		return $revision;
	}

	/**
	 * Deletes all revisions for an entry
	 *
	 * @since 1.0
	 *
	 * @param int $entry_id ID of the entry to remove revsions
	 *
	 * @return array|WP_Error $deleted WP_Error if not valid entry ID. If valid, array keys are entry IDs; value is 1 if deleted, WP_Error if not.
	 */
	public function delete_revisions( $entry_id ) {

		$revision_ids = $this->get_revisions( $entry_id, 'ids' );

		if ( is_wp_error( $revision_ids ) ) {
			return $revision_ids;
		}

		$deleted = array();
		foreach ( $revision_ids as $revision_id ) {

			$success = $this->delete_revision( $revision_id );

			if ( is_wp_error( $success ) ) {
				$deleted[ $revision_id ] = $success;
			} else {
				$deleted[ $revision_id ] = 1;
			}
		}

		return $deleted;
	}

	/**
	 * Remove a revision from an entry
	 *
	 * @since 1.0
	 *
	 * @param int $revision_id ID of the Revision to delete
	 * @param int $entry_id Optional: Pass an entry ID to check to make sure the Revision ID matches the Entry ID
	 *
	 * return bool|WP_Error WP_Error if revision isn't found or submissions blocked; true if revision deleted
	 */
	public function delete_revision( $revision_id = 0, $entry_id = null ) {

		$revision = $this->get_revision( $revision_id, $entry_id );

		if ( is_wp_error( $revision ) ) {

			GravityKitFoundation::logger()->error( sprintf( '[%s] There was an error deleting revision #%d: %s', __METHOD__, $revision_id, $revision->get_error_message() ) );

			return $revision;
		}

		$deleted = GFAPI::delete_entry( $revision_id );

		if ( is_wp_error( $deleted ) ) {
			GravityKitFoundation::logger()->error( sprintf( '[%s] There was an error deleting the revision: %s', __METHOD__, $revision->get_error_message() ) );
		} else {
			GravityKitFoundation::logger()->info( sprintf( '[%s] Revision #%d successfully deleted', __METHOD__, $revision_id ) );
		}

		return $deleted;
	}

	/**
	 * Restores an entry to a specific revision, if the revision is found
	 *
	 * @param int $entry_id ID of entry
	 * @param int $revision_id ID of revision (GMT timestamp)
	 * @param array $rows Specific fields of the revision to be restored
	 *
	 * @return bool|WP_Error WP_Error if there was an error during restore. true if success; false if failure
	 */
	public function restore_revision( $entry_id = 0, $revision_id = 0, $rows = array() ) {

		$revision = $this->get_revision( $revision_id, $entry_id );

		// Revision has already been deleted or does not exist
		if ( is_wp_error( $revision ) ) {

			GravityKitFoundation::logger()->error( sprintf( '[%s] There was an error restoring revision #%d: %s', __METHOD__, $revision_id, $revision->get_error_message() ) );

			return $revision;
		}

		// Use the current entry as the starting point
		$new_entry = $prior_entry = GFAPI::get_entry( $entry_id );

		// Don't compare status or IDs; they will always be different
		unset( $new_entry['status'], $revision['status'], $new_entry['id'], $revision['id'] );

		// Then remove revision entry meta keys
		foreach ( $this->get_entry_meta() as $key => $value ) {
			unset( $new_entry[ $key ], $revision[ $key ] );
		}

		if ( $new_entry === $revision ) {

			GravityKit\GravityRevisions\Foundation\Core::logger()->debug( sprintf( '[%s] Not restoring: Revision (#%s) is identical to the current entry (#%s).', __METHOD__, $revision_id, $entry_id ) );

			return new WP_Error( 'identical', esc_html__( 'This revision is identical to the current entry.', 'gk-gravityrevisions' ) );
		}

		if ( empty( $rows ) ) {
			$new_entry = $revision;
		} else {

			// Then update the values using the defined field IDs
			foreach ( $rows as $field_id ) {

				if ( ! isset( $revision[ $field_id ] ) ) {
					GravityKitFoundation::logger()->warning( sprintf( '[%s] Could not update field id "%s": not set for revision #%d', __METHOD__, $field_id, $revision_id ) );
					continue;
				}

				$new_entry[ $field_id ] = $revision[ $field_id ];
			}

			if ( $revision === $new_entry ) {

				GravityKitFoundation::logger()->warning( sprintf( '[%s] Not restoring: Revision (#%s) is identical to the current entry (#%s).', __METHOD__, $revision_id, $entry_id ) );

				return new WP_Error( 'identical', esc_html__( 'This revision is identical to the current entry.', 'gk-gravityrevisions' ) );
			}
		}

		/**
		 * Remove all Gravity Forms hooks when restoring a revision
		 * @since 1.0
		 * @param bool $remove_hooks [Default: true]
		 * @param int  $entry_id ID of entry being restored to
		 */
		if ( apply_filters( 'gravityview/entry-revisions/restore/remove-gf-hooks', true, $entry_id ) ) {

			GravityKitFoundation::logger()->info( sprintf( '[%s] Removing Gravity Forms update hooks', __METHOD__ ) );

			remove_all_filters( 'gform_entry_pre_update' );
			remove_all_filters( 'gform_form_pre_update_entry' );
			remove_all_filters( sprintf( 'gform_form_pre_update_entry_%s', $new_entry['form_id'] ) );
			remove_all_actions( 'gform_post_update_entry' );
			remove_all_actions( sprintf( 'gform_post_update_entry_%s', $new_entry['form_id'] ) );
		}

		$updated_result = GFAPI::update_entry( $new_entry, $entry_id );

		if ( is_wp_error( $updated_result ) ) {

			GravityKitFoundation::logger()->error( $updated_result->get_error_message() );

			return $updated_result;
		}

		/**
		 * @filter `gravityview/entry-revisions/restore/add-new` Should a new revision be created with the prior state, when creating a revision?
		 * @since 1.0
		 * @param bool $add_new Should a new revision be created based on the entry before the restoration? [Default: True]
		 * @param array $prior_entry Entry before restoring values
		 * @param array $new_entry Current entry, after restoring values
		 */
		if ( apply_filters( 'gravityview/entry-revisions/restore/add-new', true, $prior_entry, $new_entry ) ) {
			$this->add_revision( $entry_id, $prior_entry );
		}

		/**
		 * @filter `gravityview/entry-revisions/restore/delete-after` Should the revision be removed after it has been restored? Default: false
		 * @since 1.0
		 * @param bool $remove_after_restore [Default: false]
		 * @param int  $revision_id ID of revision being restored
		 * @param int  $entry_id ID of connected entry
		 */
		if ( apply_filters( 'gravityview/entry-revisions/restore/delete-after', false, $revision_id, $entry_id ) ) {
			$this->delete_revision( $revision_id, $entry_id );
		}

		GravityKitFoundation::logger()->info( sprintf( '[%s] Restored %s from revision #%s to entry #%s.', __METHOD__, sprintf( '%d %s', count( $rows ), ( count( $rows ) === 1 ? 'row' : 'rows' ) ), $revision_id, $entry_id ) );

		return true;
	}

	/**
	 * Restores an entry
	 *
	 * @since 1.0
	 *
	 * @return void Redirects to single entry view after completion
	 */
	public function admin_init_restore_listener() {

		if ( ! function_exists( 'rgpost' ) ) {
			return;
		}

		if ( ! rgpost( '_wpnonce' ) || ! rgpost( 'revision' ) || ! rgpost( 'rows' ) || ! rgpost( 'entry_id' ) ) {
			return;
		}

		// No access!
		if ( ! GFCommon::current_user_can_any( 'gravityforms_edit_entries' ) ) {
			GravityKitFoundation::logger()->error( 'Restoring the entry revision failed: user does not have the "gravityforms_edit_entries" capability.' );
			return;
		}

		$revision_id  = rgpost( 'revision' );
		$entry_id     = rgpost( 'entry_id' );
		$nonce        = rgpost( '_wpnonce' );
		$nonce_action = $this->generate_restore_nonce_action( $entry_id, $revision_id );
		$valid        = wp_verify_nonce( $nonce, $nonce_action );

		// Nonce didn't validate
		if ( ! $valid ) {
			GravityKitFoundation::logger()->error( 'Restoring the entry revision failed: nonce validation failed.' );
			return;
		}

		$rows = (array) rgpost( 'rows' );

		foreach ( $rows as $key => $row ) {
			if ( 'deleted' !== $row ) {
				unset( $rows[ $key ] );
			}
		}

		$rows = array_keys( $rows );

		// Handle restoring the entry
		$restored = $this->restore_revision( $entry_id, $revision_id, $rows );

		$redirect_url = remove_query_arg( array(
			'restore',
			'revision',
			'screen_mode',
			'restore-error',
			'restore-success'
		) );

		if ( is_wp_error( $restored ) ) {
			$redirect_url = add_query_arg( 'restore-error', $restored->get_error_code(), $redirect_url );
		} else {
			$redirect_url = add_query_arg( 'restore-success', $revision_id, $redirect_url );
		}

		wp_safe_redirect( $redirect_url );

		exit();
	}

	/**
	 * Returns HTML diff table
	 *
	 * @uses ./includes/diff-table.html.php The template file receives variables from this method
	 *
	 * @param array $entry The current entry
	 * @param array $revision The revision to compare against
	 * @param bool $restore_links Whether to include restore UI or not. [Default: false]
	 *
	 * @return string
	 */
	public function get_diff_html( array $entry, array $revision, $restore_links = false ) {

		$form = GFAPI::get_form( $entry['form_id'] );

		$diffs = $this->get_diffs( $revision, $entry, $form, $restore_links );

		if ( empty( $diffs ) ) {
			return esc_html__( 'This revision is identical to the current entry.', 'gk-gravityrevisions' );
		}

		$revision_title      = $this->revision_title( $revision, false, esc_html__( 'Entry modified by %2$s %3$s ago.', 'gk-gravityrevisions' ), $entry );
		$user_can_edit_entry = GFCommon::current_user_can_any( 'gravityforms_edit_entries' );
		$nonce_field         = wp_nonce_field( $this->generate_restore_nonce_action( $revision['gv_revision_parent_id'], $revision['id'] ) );
		$url_cancel          = esc_url( remove_query_arg( array( 'revision', 'screen_mode' ) ) );
		$date = $this->revision_title( $revision, false, esc_html_x( 'Saved on %4$s', '%4$s will be replaced by the date', 'gk-gravityrevisions' ), $entry );;

		ob_start();

		include( GV_ENTRY_REVISIONS_DIR . 'includes/diff-table.html.php' );

		$diff_output = ob_get_clean();

		return $diff_output;
	}


	/**
	 * Gets an array of diff table output comparing two entries
	 *
	 * @uses wp_text_diff()
	 *
	 * @param array $previous Previous entry
	 * @param array $current Current entry
	 * @param array $form Entry form
	 * @param bool $show_inputs Whether to show the radio buttons to select restore values. Also requires `gravityforms_edit_entries` capability.
	 *
	 * @return string Array of diff output generated by wp_text_diff()
	 */
	private function get_diffs( $previous = array(), $current = array(), $form = array(), $show_inputs = true ) {

		$return = array();

		$ignored_keys = $this->get_diff_ignored_keys( $form );

		if ( empty( $form ) ) {
			$form = GFAPI::get_form( $previous['form_id'] );
		}

		if ( empty( $form ) || is_wp_error( $form ) ) {
			return array();
		}

		// Validate that users have the ability to edit the entry before showing the inputs
		$show_inputs = $show_inputs ? GFCommon::current_user_can_any( 'gravityforms_edit_entries' ) : false;

		$diffs = array();

		foreach ( $previous as $key => $previous_value ) {

			// Don't compare `gv_revision` data
			if ( in_array( $key, $ignored_keys, false ) ) {
				continue;
			}

			$field = GFFormsModel::get_field( $form, $key );

			if ( ! $field ) {
				continue;
			}

			$details = array();

			if ( $field->get_entry_inputs() ) {
				$subtitle    = $field->get_field_label( false, $previous_value );
				$field_label = GFCommon::get_label( $field, $key, true );
				$details[]   = '<div><span class="subtitle">' . esc_html( $subtitle ) . '</span></div>';
			} else {
				$field_label = GFCommon::get_label( $field, $key, false );
				$subtitle    = '';
			}

			$details[] = sprintf( esc_html__( 'Field ID: %s', 'gk-gravityrevisions' ), $key );

			/**
			 * @filter `gravityview/entry-revisions/diff-row-args` Modify how the diff rows are rendered
			 * @since 1.0
			 * @param array $diff_row_args Args passed to GV_Entry_Revisions::text_diff_row(). {
			 *   @type string $empty_value Value shown when a row is empty
			 *   @type string $row_label Label for the row
			 *   @type bool   $show_inputs Whether to show the radio buttons used to restore a revision
			 * }
			 * @param array   $context Additional information about the current row being rendered. {
			 *   @type GF_Field $field Field being rendered
			 *   @type string $field_label Label of the field being rendered
			 *   @type string $key Input ID of the field being rendered
			 * }
			 */
			$diff_row_args = apply_filters( 'gravityview/entry-revisions/diff-row-args', array(
				'empty_value' => '<em>' . esc_html_x( 'No Value', 'Shown when the field value is empty before or after editing.', 'gk-gravityrevisions' ) . '</em>',
				'row_label'   => sprintf( '%s <div class="diff-row-details">%s</div>', $field_label, '<div>' . implode( '</div><div>', $details ) . '</div>' ),
				'show_inputs' => $show_inputs,
			), compact( "field", "field_label", "key" ) );

			$diff_row_args['row_id'] = $key; // Don't allow row ID to be filtered

			$previous_value = nl2br( $field->get_value_export( $previous, $key, true ) );
			$current_value  = nl2br( $field->get_value_export( $current, $key, true ) );

			$diffs[ $key ] = $this->text_diff_row( $previous_value, $current_value, $diff_row_args );

		}

		return array_filter( $diffs );
	}

	/**
	 * Based on wp_text_diff() but only outputs one row of a table, not a table
	 *
	 * @see wp_text_diff()
	 *
	 * @param string $left_string "old" (left) version of string
	 * @param string $right_string "new" (right) version of string
	 * @param string|array $args Optional. Change 'title', 'title_left', and 'title_right' defaults.
	 *
	 * @return string Empty string if strings are equivalent or HTML with differences.
	 */
	function text_diff_row( $left_string, $right_string, $args = null ) {

		$defaults = array(
			'row_label'   => '',
			'row_id'      => '',
			'show_inputs' => true,
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! class_exists( 'WP_Text_Diff_Renderer_Table', false ) ) {
			require( ABSPATH . WPINC . '/wp-diff.php' );
		}

		if ( ! class_exists( 'GravityView_Diff_Renderer_Table', false ) ) {
			require( GV_ENTRY_REVISIONS_DIR . 'includes/class-gravityview-diff-renderer-table.php' );
		}

		$left_string  = normalize_whitespace( $left_string );
		$right_string = normalize_whitespace( $right_string );

		$left_lines  = array( $left_string );
		$right_lines = array( $right_string );

		$text_diff = new Text_Diff( $left_lines, $right_lines );
		$renderer  = new GravityView_Diff_Renderer_Table( $args );
		$diff      = $renderer->render( $text_diff );

		return $diff;
	}

	/**
	 * Generate a nonce action to secure the restoring process
	 *
	 * @since 1.0
	 *
	 * @param int $entry_id
	 * @param int $revision_id
	 *
	 * @return string
	 */
	private function generate_restore_nonce_action( $entry_id = 0, $revision_id = 0 ) {
		return sprintf( 'gv-restore-entry-%d-revision-%d', intval( $entry_id ), intval( $revision_id ), 'gv-restore-entry' );
	}

	/**
	 * Returns nonce URL to restore a revision
	 *
	 * @param array $revision Revision entry array
	 *
	 * @return string|WP_Error
	 */
	private function get_restore_url( $revision = array() ) {

		if ( empty( $revision ) ) {
			GravityKitFoundation::logger()->error( sprintf( '[%s] Revision #%d was not found; could not create restore URL', __METHOD__, $revision['id'] ) );

			return new WP_Error( 'not_found', 'The revision was not found; could not create restore URL' );
		}

		$nonce_action = $this->generate_restore_nonce_action( $revision['gv_revision_parent_id'], $revision['id'] );

		$add_args = array( 'restore' => $revision['id'] );

		$remove_args = array( 'revision', 'screen_mode', 'restore-success', 'restore-error' );

		$base_url = admin_url( sprintf( 'admin.php?page=gf_entries&view=entry&id=%d&lid=%d', $revision['form_id'], $revision['gv_revision_parent_id'] ) );

		return wp_nonce_url( add_query_arg( $add_args, remove_query_arg( $remove_args, $base_url ) ), $nonce_action );
	}

	/**
	 * Generate a nonce action to secure the revision diff process
	 *
	 * @since 1.0
	 *
	 * @param int $entry_id
	 * @param int $revision_id
	 *
	 * @return string
	 */
	private function generate_revision_diff_nonce_action( $entry_id = 0, $revision_id = 0 ) {
		return sprintf( 'gv-restore-entry-%d-revision-%d-gv-revision-diff', intval( $entry_id ), intval( $revision_id ) );
	}

	/**
	 * Returns the nonce URL to a revision diff
	 *
	 * @param array $revision Entry revision (not original entry)
	 *
	 * @return string URL with nonce generated based on revision ID, with these query args removed: 'revision', 'screen_mode', 'restore-success', 'restore-error'
	 */
	private function get_revision_diff_url( $revision = array() ) {

		$nonce_action = $this->generate_revision_diff_nonce_action( $revision['gv_revision_parent_id'], $revision['id'] );

		$add_args = array(
			'revision' => $revision['id'],
			'lid'      => $revision['gv_revision_parent_id']
		);

		$remove_args = array( 'revision', 'screen_mode', 'restore-success', 'restore-error' );

		$base_url = admin_url( sprintf( 'admin.php?page=gf_entries&view=entry&id=%d&lid=%d', $revision['form_id'], $revision['gv_revision_parent_id'] ) );

		return wp_nonce_url( add_query_arg( $add_args, remove_query_arg( $remove_args, $base_url ) ), $nonce_action );
	}

	/**
	 * Retrieve formatted date timestamp of a revision (linked to that revision details page).
	 *
	 * @since 1.0
	 *
	 * @see wp_post_revision_title() for inspiration
	 *
	 * @param array $revision Revision entry array
	 * @param bool $link Optional, default is true. Link to revision details page?
	 * @param string $format post revision title: 1: author avatar, 2: author name, 3: time ago, 4: date
	 * @param array $current_entry The current entry array
	 *
	 * @return string HTML of the revision version
	 */
	private function revision_title( $revision, $link = true, $format = '%1$s %2$s, %3$s ago (%4$s)', $current_entry = array() ) {

		$revision_user_id = rgar( $revision, 'gv_revision_user_id' );

		$author = get_the_author_meta( 'display_name', $revision_user_id );
		/* translators: revision date format, see http://php.net/date */
		$datef = _x( 'F j, Y @ H:i:s', 'revision date format', 'gk-gravityrevisions' );
		$date     = esc_html( date_i18n( $datef, $revision['gv_revision_date'] ) );

		$gravatar = get_avatar( $revision_user_id, 32 );

		if ( $link && GFCommon::current_user_can_any( array( 'gravityforms_edit_entries' ) ) ) {
			$url  = $this->get_revision_diff_url( $revision );
			$date = sprintf( '<a href="%s">%s</a>', esc_url( $url ), $date );
		}

		$revision_title = sprintf(
			$format,
			$gravatar,
			$author,
			human_time_diff( $revision['gv_revision_date_gmt'], current_time( 'timestamp', true ) ),
			$date
		);

		/**
		 * Filters the revision title, used in rendering the revision list as well as
		 * @since 1.0
		 * @param string $revision_title Existing revision title
		 * @param array $revision
		 * @param array $revision_details Additional information used in the title
		 */
		$revision_title = apply_filters( 'gravityview/entry-revisions/revision-title', $revision_title, $revision, compact( "format", "gravatar", "author", "date", "current_entry" ) );

		return $revision_title;
	}

	/**
	 * Render the entry revisions
	 *
	 * @since 1.0
	 *
	 * @param int $entry_id
	 * @param array $entry
	 * @param array $form
	 */
	public function get_revisions_list_html( $entry_id = 0, $atts = array() ) {

		$atts = wp_parse_args( $atts, array(
			'container_css' => 'gv-entry-revisions',
			'wpautop'       => 1,
			'format'        => _x( '%1$s %2$s, %3$s ago (%4$s)', 'The default date format. %1 is the avatar, %2 is the name of the person who modified the entry, %3 is how long ago the entry was modified, and %5 is a timestamp.', 'gk-gravityrevisions' ),
			'strings'       => array(
				'no_revisions' => __( 'This entry has no revisions.', 'gk-gravityrevisions' ),
				'not_found'    => __( 'Revision not found', 'gk-gravityrevisions' ),
				'compare'      => __( 'This is an entry revision. %sCompare to the current entry%s.', 'gk-gravityrevisions' ),
			)
		) );

		$entry = GFAPI::get_entry( $entry_id );

		// Entry not found!
		if ( ! $entry || is_wp_error( $entry ) ) {

			$output = esc_html( $atts['strings']['not_found'] );

			return apply_filters( 'gravityview/entry-revisions/list-html/output', $output, $entry, null );
		}

		// We're currently looking at a revision; link to the comparison screen
		if( self::revision_status_key === $entry['status'] ) {

			$output = esc_html( $atts['strings']['compare'] );

			$output = sprintf( $output, '<a href="' . esc_url( $this->get_revision_diff_url( $entry ) ) . '">', '</a>' );

			return apply_filters( 'gravityview/entry-revisions/list-html/output', $output, $entry, null );
		}

		$form = GFAPI::get_form( $entry['form_id'] );

		$revisions = $this->get_revisions( $entry_id );

		$container_css = esc_attr( $atts['container_css'] );

		$output = esc_html( $atts['strings']['no_revisions'] );

		if ( ! empty( $revisions ) ) {

			$rows = '';
			foreach ( $revisions as $revision ) {
				$diffs = $this->get_diffs( $revision, $entry, $form );

				// Only show if there are differences
				if ( ! empty( $diffs ) ) {
					$rows .= "\t<li>" . $this->revision_title( $revision, true, $atts['format'], $entry ) . "</li>\n";
				}
			}

			$output = "<ul class='{$container_css}'>\n" . $rows . "</ul>";
		}

		if ( $atts['wpautop'] ) {
			$output = wpautop( $output );
		}

		/**
		 * @filter `gravityview/entry-revisions/list-html/output` Modify the output of the revisions list
		 * @since 1.0
		 * @param string $output HTML output
		 * @param array  $entry Entry displaying the revisions for
		 * @param array  $revisions Array of revisions
		 */
		$output = apply_filters( 'gravityview/entry-revisions/list-html/output', $output, $entry, $revisions );

		return $output;
	}
}
