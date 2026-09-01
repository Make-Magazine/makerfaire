<?php

/**
 * Handle all operations related to custom GravityView fields
 */


/**
 * @since 1.0
 */
final class GravityView_Inline_Edit_AJAX {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0
	 *
	 * @var GravityView_Inline_Edit_AJAX
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0
	 *
	 * @return GravityView_Inline_Edit_AJAX A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * GravityView_Inline_Edit_Custom_Fields constructor.
	 */
	private function __construct() {
		$this->_add_hooks();
	}

	/**
	 * Add hooks to initiate editing
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function _add_hooks() {
		// Priority 20 so we can run gp_inventory_type_choices which exist at 20 or after.
		add_action( 'init', array( $this, 'process_inline_edit_callbacks' ), 20 );
		add_action( 'wp_ajax_gv_inline_edit_get_users', array( $this, 'get_users' ) );
		add_action( 'wp_ajax_gv_inline_upload_file', array( $this, 'upload_file' ) );
	}


	/**
	 * Upload files through inline-edit.
	 *
	 * @since 1.8
	 *
	 * @return void
	 */
	public function upload_file() {

		check_ajax_referer( 'gravityview_inline_edit', 'nonce' );

		if ( ! function_exists( 'rgpost' ) || ! class_exists( 'GFAPI' ) ) {
			wp_die();
		}

		$entry_id = (int) rgpost( 'entry_id' );
		if ( 0 === $entry_id ) {
			// translators: %s is replaced by the name of the invalid item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is invalid.', 'gk-gravityedit' ), __( 'Entry ID', 'gk-gravityedit' ) ) ) ) );
		}

		$form_id = (int) rgpost( 'form_id' );
		if ( 0 === $form_id ) {
			// translators: %s is replaced by the name of the invalid item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is invalid.', 'gk-gravityedit' ), __( 'Form ID', 'gk-gravityedit' ) ) ) ) );
		}

		$form = GFAPI::get_form( $form_id );
		if ( ! $form ) {
			// translators: %s is replaced by the name of the invalid item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is invalid.', 'gk-gravityedit' ), __( 'Form', 'gk-gravityedit' ) ) ) ) );
		}

		$field_id = (int) rgpost( 'field_id' );
		if ( 0 === $field_id ) {
			// translators: %s is replaced by the name of the invalid item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is invalid.', 'gk-gravityedit' ), __( 'Field ID', 'gk-gravityedit' ) ) ) ) );
		}

		/** @var GF_Field_FileUpload $gf_field */
		$gf_field = GFFormsModel::get_field( $form, $field_id );

		if ( ! $gf_field ) {
			// translators: %s is replaced by the name of the required item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is required.', 'gk-gravityedit' ), _x( 'This field', 'The value used when saying what information is required. For example, "[This field] is required."', 'gk-gravityedit' ) ) ) ) );
		}

		// Authorize before anything below can change state. Both the empty-files branch and the
		// with-files branch call remove_previously_uploaded_files() before this method returns, and
		// that deletes the entry's stored value. A check placed after either one runs too late.
		$view_id = (int) rgpost( 'view_id' );
		$entry   = GFAPI::get_entry( $entry_id );

		// The entry must belong to the posted form. delete_file() acts on the entry's own form, but
		// $gf_field and the capability check use the posted form, so a mismatch lets a field ID from
		// one form target an entry in another.
		$entry_belongs_to_form = ! is_wp_error( $entry ) && (int) rgar( $entry, 'form_id' ) === $form_id;

		// Only a real file upload field belongs on this endpoint. Otherwise the removal path blanks
		// any non-required field of any type.
		$field_is_file_upload = $gf_field instanceof GF_Field_FileUpload;

		$user_can_edit_entry = GravityView_Inline_Edit::get_instance()->can_edit_entry( $entry_id, $form_id, $view_id );

		if ( ! $entry_belongs_to_form || ! $field_is_file_upload || ! $user_can_edit_entry ) {
			wp_send_json_error( new WP_Error( 'insufficient_privileges', esc_html__( 'You are not allowed to edit this entry.', 'gk-gravityedit' ) ) );
		}

		$files = isset( $_FILES ) ? $_FILES : array();

		if ( $gf_field->isRequired && empty( $files ) ) {
			// translators: %s is replaced by the name of the required item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is required.', 'gk-gravityedit' ), _x( 'This field', 'The value used when saying what information is required. For example, "[This field] is required."', 'gk-gravityedit' ) ) ) ) );
		}

		// Remove if nothing is uploaded
		if ( empty( $files ) ) {
			$this->remove_previously_uploaded_files( $entry_id, $field_id, $gf_field );
			$result = GFAPI::update_entry_field( $entry_id, $field_id, '' );
			wp_send_json_success(
				array(
					'removed' => true,
					'message' => esc_html__( 'Empty', 'gk-gravityedit' ),
				)
			);
		}

		// Validates file sizes against the field's maxFileSize setting. GF's own validate() cannot do
		// this here: the request posts files as input_1..input_N by file index rather than
		// input_{field_id}, so GFFormsModel::get_submission_files() finds nothing and returns early.
		$max_upload_size = $gf_field->maxFileSize > 0 ? $gf_field->maxFileSize * 1048576 : wp_max_upload_size();
		$max_in_mb       = round( $max_upload_size / 1048576, 2 );

		foreach ( $files as $file ) {
			$file_size = isset( $file['size'] ) ? (int) $file['size'] : 0;
			$php_error = isset( $file['error'] ) ? (int) $file['error'] : UPLOAD_ERR_OK;

			// A file over the ini limit arrives with size 0 and UPLOAD_ERR_INI_SIZE, so the size
			// comparison alone never catches it and Gravity Forms reports it further down with the
			// raw php.ini directive name.
			$exceeds_ini_limit = UPLOAD_ERR_INI_SIZE === $php_error || UPLOAD_ERR_FORM_SIZE === $php_error;
			$exceeds_max_size  = $file_size > 0 && $file_size > $max_upload_size;

			if ( $exceeds_ini_limit || $exceeds_max_size ) {
				wp_send_json_error(
					new WP_Error(
						'fileupload_validation_failed',
						/* translators: %s: maximum file size in MB */
						esc_html( sprintf( __( 'File exceeds size limit. Maximum file size: %sMB.', 'gk-gravityedit' ), $max_in_mb ) )
					)
				);
			}
		}

		// Enforce the field's allowed extensions ourselves. GF's validate() runs this check via
		// is_invalid_file() only when it finds submission files, which happens for multi-file fields
		// (set_uploaded_files() below) but not single-file ones — those would otherwise be gated only
		// by WordPress's global mime list, letting a .jpg-only field accept, say, a .zip.
		$allowed_extensions = $gf_field->get_clean_allowed_extensions();

		if ( ! empty( $allowed_extensions ) ) {
			foreach ( $files as $file ) {
				$extension = strtolower( pathinfo( (string) rgar( $file, 'name' ), PATHINFO_EXTENSION ) );

				if ( ! in_array( $extension, $allowed_extensions, true ) ) {
					wp_send_json_error(
						new WP_Error(
							'fileupload_validation_failed',
							/* translators: %s: comma-separated list of allowed file extensions */
							esc_html( sprintf( __( 'The uploaded file type is not allowed. Allowed types: %s.', 'gk-gravityedit' ), implode( ', ', $allowed_extensions ) ) )
						)
					);
				}
			}
		}

		$uploaded_files = $files;
		if ( $gf_field->multipleFiles ) {
			$uploaded_files = array();
			foreach ( $files as $file ) {
				$uploaded_files[ 'input_' . $field_id ][] = array(
					'temp_filename'     => rgar( $file, 'temp' ),
					'uploaded_filename' => rgar( $file, 'name' ),
				);
			}

			$_POST['gform_uploaded_files'] = wp_json_encode( $uploaded_files );

			GFFormsModel::set_uploaded_files( $form_id );
		}

		// Pass an empty value, not the new uploads: GF_Field_FileUpload::validate() adds the count of
		// this argument (the field's existing entry files) on top of the submission it counts on its
		// own. An inline upload replaces the field, so there are no existing files to add, and passing
		// the new set here double-counts it and rejects a legitimate max-files upload.
		$gf_field->validate( '', $form );
		if ( $gf_field->failed_validation === true ) {
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', $gf_field->validation_message ) );
		}

		$this->remove_previously_uploaded_files( $entry_id, $field_id, $gf_field );

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		// Change upload path.
		$gf_upload_path = GF_Field_FileUpload::get_upload_root_info( $form_id );
		$upload_dir_callback = function ( $upload ) use ( $gf_upload_path ) {
			$upload['path'] = $gf_upload_path['path'];
			$upload['url']  = $gf_upload_path['url'];

			return $upload;
		};

		add_filter( 'upload_dir', $upload_dir_callback );

		// A single-file field stores only the first URL, so writing any additional posted files would
		// orphan them on disk. Keep just the first.
		if ( ! $gf_field->multipleFiles && count( $files ) > 1 ) {
			$files = array_slice( $files, 0, 1, true );
		}

		$urls = array();
		foreach ( $files as $file ) {
			$upload = wp_handle_upload( $file, array( 'test_form' => false ) );

			// A third-party upload_dir / wp_handle_upload filter can hand back a WP_Error or a
			// non-array, so reject those too rather than dereferencing.
			if ( is_wp_error( $upload ) || ! is_array( $upload ) || ! empty( $upload['error'] ) ) {
				remove_filter( 'upload_dir', $upload_dir_callback );
				$message = is_wp_error( $upload ) ? $upload->get_error_message() : rgar( (array) $upload, 'error' );
				wp_send_json_error( new WP_Error( 'upload_failed', $message ) );
			}

			$urls[] = $upload['url'];
		}

		remove_filter( 'upload_dir', $upload_dir_callback );

		$field_value = $urls;

		if ( $gf_field->multipleFiles ) {
			$result = GFAPI::update_entry_field( $entry_id, $field_id, wp_json_encode( $field_value ) );
		} else {
			$result      = GFAPI::update_entry_field( $entry_id, $field_id, $field_value[0] );
			$field_value = $urls[0];
		}

		if ( $result !== true ) {
			wp_send_json_error( $result );
		}

		/**
		 * Clear the cache for an entry
		 *
		 * @since 1.0
		 *
		 * @param int $entry_id The ID of the entry to clear cache for
		 */
		do_action( 'gravityview_clear_entry_cache', $entry_id );

		// If View ID isn't set, we're inside Gravity Forms entry list.
		// Also, fallback if GravityView functions aren't available!
		if ( empty( $view_id ) || ! function_exists( 'gravityview_get_files_array' ) || ! class_exists( '\GV\View' ) ) {

			if ( ! class_exists( 'GF_Entry_List' ) ) {
				require_once GFCommon::get_base_path() . '/entry_list.php';
			}

			// JSON-encoded values don't work when using GFEntryList::get_icon_url( $file_path ).
			$passed_value = is_array( $field_value ) ? wp_json_encode( $field_value ) : $field_value;

			$output = $gf_field->get_value_entry_list( $passed_value, null, null, null, null );

			wp_send_json_success( array( 'output' => $output ) );
		}

		// This was initiated inside a GravityView View.
		$view  = \GV\View::by_id( $view_id );
		$entry = \GV\GF_Entry::by_id( $entry_id );

		$gravityview = \GV\Template_Context::from_template(
			array(
				'view'    => $view,
				'field'   => \GV\GF_Field::by_id( $view->form, $field_id ),
				'entry'   => $entry,
				'request' => new \GV\Mock_Request(),
			)
		);

		// There's no good way to fetch the field UID. That means it's hard to fetch the `custom_css` setting.
		// This means, due to practicality, this output isn't going to have _perfect_ HTML parity with GV.
		$gv_class = gv_class( array( 'id' => $field_id ), $form, $entry->as_entry() );

		$files_array = gravityview_get_files_array( $field_value, $gv_class, $gravityview );

		// Multiple Files is displayed in a list created by GravityView.
		if ( $gf_field->multipleFiles ) {

			$output = sprintf( "<ul class='gv-field-file-uploads %s'>", esc_attr( $gv_class ) );

			// For each file, show as a list
			foreach ( $files_array as $file_item ) {
				$output .= '<li>' . $file_item['content'] . '</li>';
			}

			$output .= '</ul>';

		} // Single file upload fields just show the content with no <ul>.
		else {
			$output = $files_array[0]['content'];
		}

		wp_send_json_success( array( 'output' => $output ) );
	}

	/**
	 * Remove previously uploaded files.
	 *
	 * @since 2.0
	 *
	 * @param int       $entry_id
	 * @param int       $field_id
	 * @param \GF_Field $gf_field
	 *
	 * @return void
	 */
	private function remove_previously_uploaded_files( $entry_id, $field_id, $gf_field ) {

		if ( ! $gf_field->multipleFiles ) {
			RGFormsModel::delete_file( $entry_id, $field_id );
			return;
		}

		$entry = \GFAPI::get_entry( $entry_id );

		if ( is_wp_error( $entry ) ) {
			return;
		}

		$value_json = RGFormsModel::get_lead_field_value( $entry, $gf_field );

		if ( empty( $value_json ) ) {
			return;
		}

		$old_files = json_decode( $value_json, true );

		if ( ! is_array( $old_files ) ) {
			return;
		}

		// delete_file re-indexes the survivors after each delete, so walk from the end backward:
		// deleting the highest index never shifts a lower one. A forward loop would delete only the
		// first file and leave the rest orphaned on disk.
		for ( $file_index = count( $old_files ) - 1; $file_index >= 0; $file_index-- ) {
			RGFormsModel::delete_file( $entry_id, $field_id, $file_index );
		}
	}

	/**
	 * Get users for created_by field.
	 *
	 * @since 1.8
	 *
	 * @return void
	 */
	public function get_users() {
		check_ajax_referer( 'gravityview_inline_edit', 'nonce' );

		if ( empty( $_POST['search'] ) ) {
			wp_die();
		}

		$search = sanitize_text_field( wp_unslash( $_POST['search'] ) );

		$return = array(
			'results' => array(),
		);

		$args = array(
			'search'         => '*' . $search . '*',
			'search_columns' => array( 'user_login', 'user_email', 'display_name', 'user_nicename' ),
			'fields'         => array( 'ID', 'display_name' ),
		);

		$user_query = new WP_User_Query( $args );

		foreach ( $user_query->get_results() as $result ) {
			$return['results'][] = array(
				'id'   => $result->ID,
				'text' => $result->display_name,
			);
		}

		wp_send_json( $return );
	}



	/**
	 * Check if x-editable POST field `gv_inline_edit_field` is set. If it is, transform value into an x-editable field
	 *
	 * @since 1.0
	 *
	 * @return void
	 * @todo  Should we use admin-ajax.php instead?
	 */
	public function process_inline_edit_callbacks() {
		if ( isset( $_POST['gv_inline_edit_field'] ) ) {
			$this->_edit_gravityview_field();
		}
	}

	/**
	 * Checks whether a choice is among the values submitted for a field.
	 *
	 * Choice values are not always strings: lookup fields and dynamically populated choices carry
	 * database IDs, which are integers. Submitted values are always strings. Both sides are cast
	 * before comparing so an integer choice still matches its posted value, while the comparison
	 * stays strict so PHP does not treat '1e2' and '100' as the same choice.
	 *
	 * @since 2.11
	 *
	 * @param array $choice     The choice, expected to carry a `value` key.
	 * @param mixed $post_value The submitted value(s) for the field.
	 *
	 * @return bool True: the choice is selected.
	 */
	private static function is_choice_selected( $choice, $post_value ) {
		$choice_value  = (string) rgar( $choice, 'value' );
		$posted_values = array_map( 'strval', (array) $post_value );

		return in_array( $choice_value, $posted_values, true );
	}

	/**
	 * Check whether the input of a field is hidden
	 *
	 * @since 1.0
	 *
	 * @param GF_Field $field
	 * @param int      $passed_input_id ID of input
	 *
	 * @return bool True: input is hidden; False: input is shown
	 */
	private function _is_input_hidden( $field, $passed_input_id ) {

		if ( is_array( $field->inputs ) ) {
			foreach ( $field->inputs as $input ) {

				list( $field_id, $input_id ) = explode( '.', $input['id'] );

				if ( (int) $passed_input_id === (int) $input_id ) {
					return isset( $input['isHidden'] ) ? $input['isHidden'] : false;
				}
			}
		}

		return false;
	}

	/**
	 * This is the callback which processes AJAX calls from
	 * x-editable when a field is modified.
	 *
	 * @since 1.0
	 * @since 2.9.0 Returns JSON error instead of empty response on nonce failure.
	 *
	 * @return void Exits with JSON payload.
	 */
	private function _edit_gravityview_field() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'gravityview_inline_edit' ) ) {
			wp_send_json( new WP_Error( 'invalid_nonce', esc_html__( 'Your session has expired or the security token is invalid. Please reload the page and try again.', 'gk-gravityedit' ) ) );
		}

		if ( ! function_exists( 'rgpost' ) ) {
			wp_send_json( new WP_Error( 'gravity_forms_inactive', __( 'Gravity Forms is not active.', 'gk-gravityedit' ) ) );
		}

		$entry_id   = sanitize_key( rgpost( 'pk' ) );
		$type       = sanitize_key( rgpost( 'type' ) );
		$form_id    = sanitize_key( rgpost( 'form_id' ) );
		$field_id   = sanitize_key( rgpost( 'field_id' ) );
		$input_id   = sanitize_key( rgpost( 'input_id' ) );
		$view_id    = sanitize_key( rgpost( 'view_id' ) );
		$post_value = rgpost( 'value' );

		// Sanitizes scalar values; arrays and HTML fields are handled per field type in the switch below.
		if ( 'wysihtml5' === $type || 'textarea' === $type || 'richtext' === $type ) {
			// A rich text field (wysihtml5/textarea/richtext) defers sanitization to the switch case;
			// sanitize_text_field() would strip its markup. rgpost() already unslashed $post_value, so
			// it is intentionally left as-is: a second wp_unslash() here would eat literal backslashes
			// the user typed (e.g. a Windows path) before the value is ever stored.
			$post_value = (string) $post_value;
		} elseif ( is_string( $post_value ) ) {
			$post_value = sanitize_text_field( wp_unslash( $post_value ) );
		} elseif ( is_array( $post_value ) ) {
			$post_value = array_map( function ( $item ) {
				if ( is_string( $item ) ) {
					return sanitize_text_field( wp_unslash( $item ) );
				}
				if ( is_array( $item ) ) {
					return array_map( 'sanitize_text_field', array_map( 'wp_unslash', $item ) );
				}
				return $item;
			}, $post_value );
		}

		if ( ! GravityView_Inline_Edit::get_instance()->can_edit_entry( $entry_id, $form_id, $view_id ) ) {
			wp_send_json( new WP_Error( 'insufficient_privileges', __( 'You are not allowed to edit this entry.', 'gk-gravityedit' ) ) );
		}
		$entry = GFAPI::get_entry( $entry_id );

		if ( is_wp_error( $entry ) ) {
			wp_send_json( new WP_Error( 'entry_not_found', __( 'The entry could not be found.', 'gk-gravityedit' ) ) );
		}

		// The entry and form IDs are submitted independently. Without this, a field resolved from
		// the named form is written onto an entry belonging to a different one.
		$entry_belongs_to_form = (int) rgar( $entry, 'form_id' ) === (int) $form_id;

		if ( ! $entry_belongs_to_form ) {
			wp_send_json( new WP_Error( 'entry_form_mismatch', __( 'The entry does not belong to the form.', 'gk-gravityedit' ) ) );
		}

		$form = GFAPI::get_form( $form_id );

		if ( ! $form ) {
			wp_send_json( new WP_Error( 'form_not_found', __( 'The form could not be found.', 'gk-gravityedit' ) ) );
		}

		$entry_pre_update = $entry;

		// Apply pre-render filter so that dynamically populated choices (e.g., via GP Populate Anything) are available during save.
		$form = gf_apply_filters( array( 'gform_pre_render', $form_id ), $form, false, false );

		$gf_field         = GFFormsModel::get_field( $form, $field_id );

		// Entry meta (created_by, source_url, date_created, entry tags) has no form field, and the
		// switch below writes it with a null field. Only a numeric ID promises a form field, so
		// that is the only case where a missing one is an error.
		if ( ! $gf_field && is_numeric( $field_id ) ) {
			wp_send_json( new WP_Error( 'field_not_found', __( 'The field could not be found.', 'gk-gravityedit' ) ) );
		}

		$values_to_update = array();

		// TODO: Move to inline field classes
		switch ( $type ) {
			case 'address':
			case 'name':
				$value = $post_value;

				foreach ( $gf_field->inputs as $index => $input ) {
					$_id                      = $input['id'];
					$_input                   = explode( '.', $_id )[1];
					$values_to_update[ $_id ] = isset( $value[ $_input ] ) ? $value[ $_input ] : rgar( $entry, $_id, '' );
				}

				$field_validate = $values_to_update;
				break;
			case 'number':
				$value                         = $field_validate = $post_value;
				$values_to_update[ $field_id ] = $value;
				$_POST[ 'input_' . $field_id ] = rgar( $entry, $field_id, '' );
				break;
			case 'tel':
				// Gravity Forms 3.0's international phone format stores an object, not the string the
				// editor posts. Translating here keeps validation and storage on the same value.
				$value = GravityView_Inline_Edit_Field_Phone::prepare_save_value( $gf_field, $post_value );

				if ( is_wp_error( $value ) ) {
					wp_send_json( $value );
				}

				$field_validate                = $value;
				$values_to_update[ $field_id ] = $value;
				break;
			case 'text':
				// Gravity Forms 3.0 measures a text field's input mask when the entry is saved. The
				// inline editor has no masked input to type into, so the mask's own punctuation is
				// supplied here instead of by the browser.
				$field_validate                = GravityView_Inline_Edit_Field_Text::prepare_save_value( $gf_field, $post_value );
				$values_to_update[ $field_id ] = $field_validate;
				break;
			case 'checklist':
				if ( (int) $input_id ) {
					$post_value = ( is_array( $post_value ) ) ? $post_value[0] : $post_value;

					$_id = $field_id . '.' . $input_id;

					if ( $post_value !== rgar( $entry, $_id ) ) {
						$values_to_update[ $_id ] = $post_value;
					}
				} else {
					$choice_number = 1;
					$choices = $gf_field->choices;
					if ( 'lookup' === $gf_field->type ) {
						$choices = $gf_field->get_lookup_choices();
					}

					foreach ( $choices as $i => $choice ) {
						if ( $choice_number % 10 === 0 ) { // hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
							++$choice_number;
						}

						$_id           = $field_id . '.' . $choice_number;
						$current_value = rgar( $entry, $_id );
						$is_selected   = self::is_choice_selected( $choice, $post_value );

						if ( ! $is_selected && '' !== $current_value ) {
							$values_to_update[ $_id ] = '';
						}

						// Writes on every selected choice, not only an empty slot: a choice whose value
						// changed since the entry was saved stays selected, so the clearing branch
						// never runs and the stale value would survive.
						if ( $is_selected && (string) rgar( $choice, 'value' ) !== (string) $current_value ) {
							$values_to_update[ $_id ] = rgar( $choice, 'value' );
						}

						++$choice_number;
					}
				}

				$field_validate = $values_to_update;
				break;
			case 'multiselect':
				/** @var array $post_value */

				// GF's currently has no validate method for multiselect. Do it here.
				if ( $gf_field->isRequired && empty( $post_value ) ) {
					wp_send_json( new WP_Error( 'multiselect_validation_failed', esc_html__( 'This field is required.', 'gk-gravityedit' ) ) );
				}

				if ( 'json' === rgobj( $gf_field, 'storageType' ) ) {
					$value = $post_value;
				} else {
					$value = implode( ',', $post_value );
				}

				$field_validate                = is_array( $value ) ? $value : rtrim( $value, ',' );
				$values_to_update[ $field_id ] = $field_validate;
				break;

			case 'entry_tags':
				$field_validate                = $post_value;
				$values_to_update[ $field_id ] = $field_validate;
				break;
			case 'textarea':
				// A Paragraph field with the rich text editor enabled posts as `textarea`. Preserve its
				// safe HTML with the same kses filter used for wysihtml5; a plain paragraph gets tags
				// stripped but keeps its newlines.
				$is_rich_text                  = $gf_field && ! empty( $gf_field->useRichTextEditor );
				$field_validate                = $is_rich_text ? wp_kses_post( $post_value ) : sanitize_textarea_field( $post_value );
				$values_to_update[ $field_id ] = $field_validate;
				break;
			case 'richtext':
			case 'wysihtml5':
				// wp_kses_post() (not wp_filter_post_kses) matches the value already unslashed above; the
				// slashing variant strips real backslashes and slash-escapes every quote. The editor
				// `type` is client-supplied, so only a genuine rich text field may store HTML; anything
				// else falls back to plain-text sanitization.
				$is_rich_text                  = $gf_field && ! empty( $gf_field->useRichTextEditor );
				$field_validate                = $is_rich_text ? wp_kses_post( $post_value ) : sanitize_textarea_field( $post_value );
				$values_to_update[ $field_id ] = $field_validate;
				break;
			case 'gvlist':
				/** @var array $post_value */
				$value                = $field_validate = $post_value;
				$raw_multi_list_value = array();
				if ( isset( $value[0] ) && is_array( $value[0] ) ) {
					foreach ( $value as $row ) {
						foreach ( $row as $column ) {
							$raw_multi_list_value[] = $column;
						}
					}
					$values_to_update[ $field_id ] = $raw_multi_list_value;
				} else {
					$values_to_update[ $field_id ] = $field_validate;
				}
				break;
			case 'gvtime':
				/** @var array $value */
				$value = $post_value;

				if ( count( $value ) > 1 && ( empty( $value[1] ) || empty( $value[2] ) ) ) {
					// We define a custom error message here because `$gf_field->validate` (used below), fails silently for this use-case. With count( $value ) > 1, we check if we are in single field mode
					wp_send_json( new WP_Error( 'invalid_time', __( 'Please enter a valid time.', 'gk-gravityedit' ) ) );
				}

				if ( 1 === count( $value ) ) {// Single field mode
					$saved_time = isset( $entry[ $field_id ] ) ? $entry[ $field_id ] : '00:00 AM';
					if ( preg_match( '/^(\d*):(\d*) ?(.*)$/', $saved_time, $time_matches ) ) {
						for ( $i = 0; $i <= 3; $i++ ) {// From the values matched, populate the hh,mm and am/pm fields of $value
							if ( ! isset( $value[ $i ] ) ) {
								$value[ $i ] = $time_matches[ $i ] ?? '';
							}
						}
					}
				}
				$hour           = (int) sanitize_text_field( $value[1] ?? '' );
				$minute         = (int) sanitize_text_field( $value[2] ?? '' );
				$meridiem       = strtoupper( sanitize_text_field( $value[3] ?? '' ) );
				$field_validate = $hour . ':' . $minute . ' ' . $meridiem;
				$values_to_update[ $field_id ] = $field_validate;
				break;
			case 'product':
				$currency                      = new RGCurrency( rgar( $entry, 'currency', 'USD' ) );
				$field_validate                = $post_value;
				$values_to_update[ $field_id ] = $currency->to_money( $post_value );
				break;
			case 'image_choice':
				if ( ! $gf_field instanceof GF_Field_Radio ) {
					// Multiple selection (checkbox) behavior
					$choice_number = 1;
					foreach ( $gf_field->choices as $i => $choice ) {
						if ( $choice_number % 10 === 0 ) { // Skip numbers ending in 0
							++$choice_number;
						}

						$_id           = $field_id . '.' . $choice_number;
						$current_value = rgar( $entry, $_id );
						$is_selected   = self::is_choice_selected( $choice, $post_value );

						if ( ! $is_selected && '' !== $current_value ) {
							$values_to_update[ $_id ] = '';
						}

						// Writes on every selected choice, not only an empty slot: a choice whose value
						// changed since the entry was saved stays selected, so the clearing branch
						// never runs and the stale value would survive.
						if ( $is_selected && (string) rgar( $choice, 'value' ) !== (string) $current_value ) {
							$values_to_update[ $_id ] = rgar( $choice, 'value' );
						}

						++$choice_number;
					}
				} else {
					// Single selection (radio) behavior
					$value = is_array( $post_value ) ? $post_value[0] : $post_value;
					$values_to_update[ $field_id ] = $value;
				}

				$field_validate = $values_to_update;
				break;
			case 'survey':
				$subtype = isset( $gf_field->inputType ) ? $gf_field->inputType : 'survey';
				
				if ( $subtype === 'checkbox' ) {
					$choice_number = 1;
					foreach ( $gf_field->choices as $choice ) {
						// Skip numbers ending in 0 to avoid conflicts
						if ( $choice_number % 10 === 0 ) {
							++$choice_number;
						}
						
						$_id           = $field_id . '.' . $choice_number;
						$current_value = rgar( $entry, $_id );
						$is_selected   = self::is_choice_selected( $choice, $post_value );

						// Uncheck if not in post_value array, check if in array.
						if ( ! $is_selected && '' !== $current_value ) {
							$values_to_update[ $_id ] = '';
						}

						// Writes on every selected choice, not only an empty slot: a choice whose value
						// changed since the entry was saved stays selected, so the clearing branch
						// never runs and the stale value would survive.
						if ( $is_selected && (string) rgar( $choice, 'value' ) !== (string) $current_value ) {
							$values_to_update[ $_id ] = rgar( $choice, 'value' );
						}

						++$choice_number;
					}
					$field_validate = $values_to_update;
				} elseif ( is_array( $post_value ) ) {
					// Check if this is multi-row likert by looking for decimal points in keys
					$first_key = key( $post_value );
					if ( is_string( $first_key ) && strpos( $first_key, '.' ) !== false ) {
						// Multi-row Likert: update each input; clear when not provided.
						$expected_inputs = is_array( $gf_field->inputs ) ? wp_list_pluck( $gf_field->inputs, 'id' ) : array_keys( (array) $post_value );
						foreach ( $expected_inputs as $expected_id ) {
							$val                              = array_key_exists( $expected_id, $post_value ) ? $post_value[ $expected_id ] : '';
							$values_to_update[ $expected_id ] = ( null === $val ? '' : $val );
						}
						$field_validate = $values_to_update;
					} else {
						$field_validate                = $post_value;
						$values_to_update[ $field_id ] = $field_validate;
					}
				} else {
					// Simple survey fields (rank, rating, radio, select): update single value
					$field_validate                = $post_value;
					$values_to_update[ $field_id ] = $field_validate;
				}
				break;
			default:
				$field_validate                = $post_value;
				$values_to_update[ $field_id ] = $field_validate;
				break;
		}

		if ( $gf_field ) {
			$validation_response = $this->validate_field( $field_validate, $gf_field, $type, $entry );

			if ( is_wp_error( $validation_response ) ) {
				wp_send_json( $validation_response );
			}
		}

		// Sanitize the field
		foreach ( $values_to_update as $update_id => $update_value ) {
			$input_name = 'input_' . str_replace( '.', '_', $update_id );
			if ( ! $gf_field ) {
				$entry[ $update_id ] = $update_value;

				continue;
			}

			// GFFormsModel::prepare_value() is deprecated since Gravity Forms 3.0.
			$entry[ $update_id ] = method_exists( $gf_field, 'get_value_save_input' )
				? $gf_field->get_value_save_input( $update_value, $form, $input_name, $entry_id, $entry )
				: GFFormsModel::prepare_value( $form, $gf_field, $update_value, $input_name, $entry_id );
		}

		$update_result = $this->_update_entry( $entry, $form_id, $gf_field, $type, $entry_pre_update );

		wp_send_json( $update_result );
	}

	/**
	 * Actually update the entry
	 *
	 * @since 1.0
	 * @since 1.1 Added $original_entry param
	 *
	 * @param array         $entry          The entry object that will be updated
	 * @param int           $form_id        The Form ID that the entry is connected to
	 * @param GF_Field|null $gf_field       Field of the value that will be updated, or null if no field exists (for entry meta)
	 * @param string        $type           Inline Edit type, defined in {@see GravityView_Inline_Edit_Field->inline_edit_type}
	 * @param array         $original_entry Original entry object
	 *
	 * @return bool|WP_Error $update_result True: the entry has been updated by Gravity Forms or WP_Error if there was a problem
	 */
	private function _update_entry( $entry, $form_id = 0, $gf_field = null, $type = 'text', $original_entry = array() ) {

		/**
		 * Remove Gravity Forms update hooks before updating entry
		 *
		 * @since 1.2.7
		 *
		 * @param bool $remove_hooks Whether to remove Gravity Forms update hooks. Default: true
		 */
		$remove_hooks = apply_filters( 'gravityview-inline-edit/remove-gf-update-hooks', true );

		if ( $remove_hooks ) {
			remove_all_filters( 'gform_entry_pre_update' );
			remove_all_filters( 'gform_form_pre_update_entry' );
			remove_all_filters( 'gform_form_pre_update_entry_' . $form_id );
			remove_all_actions( 'gform_post_update_entry' );
			remove_all_actions( 'gform_post_update_entry_' . $form_id );
		}

		// Clear entry's "date_updated" value in order for it to be populated with the current date
		unset( $entry['date_updated'] );

		$update_result = GFAPI::update_entry( $entry );

		/**
		 * @filter  `gravityview-inline-edit/entry-updated` Inline Edit entry updated
		 *
		 * @since   1.0
		 * @since   1.1 Added $original_entry param
		 *
		 * @used-by GravityView_Inline_Edit::update_inline_edit_result
		 *
		 * @param bool|WP_Error $update_result  True: the entry has been updated by Gravity Forms or WP_Error if there was a problem
		 * @param array         $entry          The Entry Object that's been updated
		 * @param int           $form_id        The Form ID
		 * @param GF_Field|null $gf_field       The field that's been updated, or null if no field exists (for entry meta)
		 * @param array         $original_entry Original entry, before being updated
		 */
		$update_result = apply_filters( 'gravityview-inline-edit/entry-updated', $update_result, $entry, $form_id, $gf_field, $original_entry );

		/**
		 * @filter  `gravityview-inline-edit/entry-updated/{$type}` Inline Edit entry updated, where $type is the GravityView_Inline_Edit_Field->inline_edit_type string
		 *
		 * @since   1.0
		 * @since   1.1 Added $original_entry param
		 *
		 * @used-by GravityView_Inline_Edit::update_inline_edit_result
		 *
		 * @param bool|WP_Error $update_result  True: the entry has been updated by Gravity Forms or WP_Error if there was a problem
		 * @param array         $entry          The Entry Object that's been updated
		 * @param int           $form_id        The Form ID
		 * @param GF_Field|null $gf_field       The field that's been updated, or null if no field exists (for entry meta)
		 * @param array         $original_entry Original entry, before being updated
		 */
		$update_result = apply_filters( 'gravityview-inline-edit/entry-updated/' . $type, $update_result, $entry, $form_id, $gf_field, $original_entry );

		return $update_result;
	}

	/**
	 * Validate inputs
	 *
	 * @since 1.0
	 * @since 1.4 Added $entry parameter
	 *
	 * @param mixed    $field_value The field value to validate
	 * @param GF_Field $gf_field    The field to validate
	 * @param int      $field_id    The field ID
	 * @param string   $field_type  The type of the field
	 * @param array    $entry       Entry data
	 *
	 * @return boolean|WP_Error  true if all's well or WP_Error if the fields not valid
	 */
	private function validate_field( $field_value, $gf_field, $field_type, $entry = array() ) {
		// For Multiple Choice field, we need to merge the current entry values with the new field values for the selections validation to work.
		if ( 'multi_choice' === $gf_field->type && is_array( $field_value ) ) {
			$complete_field_value = array();
			
			foreach ( $gf_field->inputs as $input ) {
				$input_id = $input['id'];

				if ( isset( $field_value[ $input_id ] ) ) {
					$complete_field_value[ $input_id ] = $field_value[ $input_id ];
				} else {
					$complete_field_value[ $input_id ] = rgar( $entry, $input_id );
				}
			}

			$field_value = $complete_field_value;
		}

		if ( $gf_field instanceof \GF_Field_Checkbox && $gf_field->isRequired && is_array( $field_value ) ) {

			if ( empty( $field_value ) ) {
				return true;
			}

			$values = array();

			foreach ( $gf_field->inputs as $input ) {
				$values[ $input['id'] ] = rgar( $entry, $input['id'], '' );
			}

			if ( empty( array_filter( array_merge( $values, $field_value ) ) ) ) {
				return new WP_Error( strtolower( $field_type ) . '_validation_failed', __( 'This field must have at least one checked option.', 'gk-gravityedit' ) );
			}

		}

		// Get all values for the image choice field so we can pass it to the validation, because it doesn't pass existing values to the $field_value
		if ( $gf_field instanceof \GF_Field_Checkbox && $gf_field->type === 'image_choice' ) {
			$image_choice_values = [];

			foreach ( $gf_field->inputs as $input ) {
				$image_choice_values[ $input['id'] ] = rgar( $entry, $input['id'], '' );
			}

			$field_value = array_filter( array_merge( $image_choice_values, $field_value ) );
		}

		// Bypass validation for decimal format.
		if ( $gf_field instanceof \GF_Field_Number && 'decimal_comma' === $gf_field->numberFormat ) {
			$gf_field->numberFormat = 'decimal_dot';
		}

		// No Duplicate.
		$old_field_value = rgar( $entry, $gf_field->id );

		if ( $gf_field->noDuplicates && $old_field_value !== $field_value && GFFormsModel::is_duplicate( $gf_field->formId, $gf_field, $field_value ) ) {
			return new WP_Error(
				strtolower( $field_type ) . '_validation_failed',
				strtr(
					_x( "This field requires a unique entry and '[value]' has already been used.", 'Placeholders inside [] are not to be translated.', 'gk-gravityedit' ),
					[ '[value]' => $field_value ]
				)
			);

		}

		// Inline editing only has a single email input, so pass the value as an array
		// with both elements matching to bypass the "Your emails do not match" validation.
		if ( $gf_field instanceof \GF_Field_Email && $gf_field->emailConfirmEnabled ) {
			$email = is_array( $field_value ) ? rgar( $field_value, 0 ) : $field_value;

			$field_value = array( $email, $email );
		}

		$gf_field->validate( $field_value, null );

		if ( $gf_field->failed_validation ) {
			$error_message = ( empty( $gf_field->validation_message ) ? __( 'Invalid value. Please try again.', 'gk-gravityedit' ) : $gf_field->validation_message );

			return new WP_Error( strtolower( $field_type ) . '_validation_failed', $error_message );
		}

		return true;
	}
}

GravityView_Inline_Edit_AJAX::get_instance();
