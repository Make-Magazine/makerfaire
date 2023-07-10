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
		add_action( 'init', array( $this, 'process_inline_edit_callbacks' ), 16 );
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
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is invalid.', 'gravityview-inline-edit', 'gk-gravityedit' ), __( 'Entry ID', 'gravityview-inline-edit', 'gk-gravityedit' ) ) ) ) );
		}

		$form_id = (int) rgpost( 'form_id' );
		if ( 0 === $form_id ) {
			// translators: %s is replaced by the name of the invalid item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is invalid.', 'gravityview-inline-edit', 'gk-gravityedit' ), __( 'Form ID', 'gravityview-inline-edit', 'gk-gravityedit' ) ) ) ) );
		}

		$form = GFAPI::get_form( $form_id );
		if ( ! $form ) {
			// translators: %s is replaced by the name of the invalid item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is invalid.', 'gravityview-inline-edit', 'gk-gravityedit' ), __( 'Form', 'gravityview-inline-edit', 'gk-gravityedit' ) ) ) ) );
		}

		$field_id = (int) rgpost( 'field_id' );
		if ( 0 === $field_id ) {
			// translators: %s is replaced by the name of the invalid item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is invalid.', 'gravityview-inline-edit', 'gk-gravityedit' ), __( 'Field ID', 'gravityview-inline-edit', 'gk-gravityedit' ) ) ) ) );
		}

		/** @var GF_Field_FileUpload $gf_field */
		$gf_field = GFFormsModel::get_field( $form, $field_id );

		if ( ! $gf_field ) {
			// translators: %s is replaced by the name of the required item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is required.', 'gravityview-inline-edit', 'gk-gravityedit' ), _x( 'This field', 'The value used when saying what information is required. For example, "[This field] is required."', 'gravityview-inline-edit', 'gk-gravityedit' ) ) ) ) );
		}

		$files = isset( $_FILES ) ? $_FILES : array();

		if ( $gf_field->isRequired && empty( $files ) ) {
			// translators: %s is replaced by the name of the required item.
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', esc_html( sprintf( __( '%s is required.', 'gravityview-inline-edit', 'gk-gravityedit' ), _x( 'This field', 'The value used when saying what information is required. For example, "[This field] is required."', 'gravityview-inline-edit', 'gk-gravityedit' ) ) ) ) );
		}

		// Remove if nothing is uploaded
		if ( empty( $files ) ) {
			$this->remove_previously_uploaded_files( $entry_id, $field_id, $gf_field );
			$result = GFAPI::update_entry_field( $entry_id, $field_id, '' );
			wp_send_json_success(
				array(
					'removed' => true,
					'message' => esc_html__( 'Empty', 'gravityview-inline-edit', 'gk-gravityedit' ),
				)
			);
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

			$_POST['gform_uploaded_files'] = json_encode( $uploaded_files );

			GFFormsModel::set_uploaded_files( $form_id );
		}

		$gf_field->validate( $uploaded_files, $form );
		if ( $gf_field->failed_validation === true ) {
			wp_send_json_error( new WP_Error( 'fileupload_validation_failed', $gf_field->validation_message ) );
		}

		$this->remove_previously_uploaded_files( $entry_id, $field_id, $gf_field );

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		// Change upload path.
		$gf_upload_path = GF_Field_FileUpload::get_upload_root_info( $form_id );
		add_filter( 'upload_dir', function( $upload ) use ( $gf_upload_path ) {

			$upload['path'] = $gf_upload_path['path'];
			$upload['url']  = $gf_upload_path['url'];

			return $upload;
		} );

		$urls = array();
		foreach ( $files as $file ) {
			$upload = wp_handle_upload( $file, array( 'test_form' => false ) );

			if ( is_wp_error( $upload ) ) {
				wp_send_json_error( $upload );
			}

			$urls[] = $upload['url'];
		}

		$field_value = $urls;

		if ( $gf_field->multipleFiles ) {
			$result = GFAPI::update_entry_field( $entry_id, $field_id, json_encode( $field_value ) );
		} else {
			$result      = GFAPI::update_entry_field( $entry_id, $field_id, $field_value[0] );
			$field_value = $urls[0];
		}

		if ( $result !== true ) {
			wp_send_json_error( $result );
		}

		$view_id = (int) rgpost( 'view_id' );

		// If View ID isn't set, we're inside Gravity Forms entry list.
		// Also, fallback if GravityView functions aren't available!
		if ( empty( $view_id ) || ! function_exists( 'gravityview_get_files_array' ) || ! class_exists( '\GV\View' ) ) {

			if ( ! class_exists( 'GF_Entry_List' ) ) {
				require_once GFCommon::get_base_path() . '/entry_list.php';
			}

			// JSON-encoded values don't work when using GFEntryList::get_icon_url( $file_path ).
			$passed_value = is_array( $field_value ) ? json_encode( $field_value ) : $field_value;

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

			$output = sprintf( "<ul class='gv-field-file-uploads %s'>", $gv_class );

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
	 * @param int  $entry_id
	 * @param int  $field_id
	 * @param \GF_Field $gf_field
	 *
	 * @return void
	 */
	private function remove_previously_uploaded_files( $entry_id, $field_id, $gf_field ) {

		if ( ! $gf_field->multipleFiles ) {
			RGFormsModel::delete_file( $entry_id, $field_id );
			return;
		}

		$entry      = \GFAPI::get_entry( $entry_id );
		$value_json = RGFormsModel::get_lead_field_value( $entry, $gf_field );

		if ( empty( $value_json ) ) {
			return;
		}

		$old_files = json_decode( $value_json, true );
		foreach ( $old_files as $file_index => $file ) {
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

		$search = sanitize_text_field( $_POST['search'] );

		$return = array(
			'results' => array(),
		);

		$args = array(
			'search'         => '*' . esc_attr( $search ) . '*',
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
	 *
	 * @return void Exits with false or JSON payload
	 */
	private function _edit_gravityview_field() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gravityview_inline_edit' ) ) {
			exit( false );
		}

		// Doesn't have minimum version of WordPress
		if ( ! function_exists( 'wp_send_json' ) ) {
			exit( false );
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

		if ( ! GravityView_Inline_Edit::get_instance()->can_edit_entry( $entry_id, $form_id, $view_id ) ) {
			wp_send_json( new WP_Error( 'insufficient_privileges', __( 'You are not allowed to edit this entry.', 'gk-gravityedit' ) ) );
		}
		$entry            = GFAPI::get_entry( $entry_id );
		$entry_pre_update = $entry;
		$form             = GFAPI::get_form( $form_id );
		$gf_field         = GFFormsModel::get_field( $form, $field_id );
		$values_to_update = array();

		// TODO: Move to inline field classes
		switch ( $type ) {
			case 'address':
			case 'name':
				$value = $post_value;

				foreach ( $gf_field->inputs as $index => $input ) {
					$_id                      = $input['id'];
					$_input                   = explode( '.', $_id )[1];
					$values_to_update[ $_id ] = isset( $value[ $_input ] ) ? $value[ $_input ] : $entry[ $_id ];
				}

				$field_validate = $values_to_update;
				break;
			case 'number':
				$value                         = $field_validate = $post_value;
				$values_to_update[ $field_id ] = $value;
				$_POST[ 'input_' . $field_id ] = $entry[ $field_id ];
				break;
			case 'tel':
				$value                         = $field_validate = $post_value;
				$values_to_update[ $field_id ] = $value;
				break;
			case 'checklist':
				if ( (int) $input_id ) {
					$post_value = ( is_array( $post_value ) ) ? $post_value[0] : $post_value;

					$_id = $field_id . '.' . $input_id;

					if ( $post_value !== $entry[ $_id ] ) {
						$values_to_update[ $_id ] = $post_value;
					}
				} else {
					$choice_number = 1;
					foreach ( $gf_field->choices as $i => $choice ) {
						if ( $choice_number % 10 === 0 ) { // hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
							$choice_number ++;
						}

						$_id = $field_id . '.' . $choice_number;

						if ( ! in_array( $choice['value'], (array) $post_value ) && '' !== $entry[ $_id ] ) {
							$values_to_update[ $_id ] = '';
						}

						if ( in_array( $choice['value'], (array) $post_value ) && '' === $entry[ $_id ] ) {
							$values_to_update[ $_id ] = $choice['value'];
						}

						$choice_number ++;
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
			case 'wysihtml5':
				$field_validate                = wp_filter_post_kses( $post_value );
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
					preg_match( '/^(\d*):(\d*) ?(.*)$/', $saved_time, $time_matches );
					for ( $i = 0; $i <= 3; $i ++ ) {// From the values matched, populate the hh,mm and am/pm fields of $value
						if ( ! isset( $value[ $i ] ) ) {
							$value[ $i ] = $time_matches[ $i ];
						}
					}
				}
				$field_validate                = (int) sanitize_text_field( $value[1] ) . ':' . (int) sanitize_text_field( $value[2] ) . ' ' . strtoupper( sanitize_text_field( $value[3] ) );
				$values_to_update[ $field_id ] = $field_validate;
				break;
			case 'product':
				$currency                      = new RGCurrency( $entry['currency'] );
				$field_validate                = $post_value;
				$values_to_update[ $field_id ] = $currency->to_money( $post_value );
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
			if ( $gf_field ) {
				$entry[ $update_id ] = GFFormsModel::prepare_value( $form, $gf_field, $update_value, $input_name, $entry_id );
			} else {
				$entry[ $update_id ] = $update_value;
			}
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
		 * @since 1.2.7
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

		if ( $gf_field instanceof \GF_Field_Checkbox && $gf_field->isRequired && is_array( $field_value ) ) {

			if ( empty( $field_value ) ) {
				return true;
			}

			$values = array();

			foreach ( $gf_field->inputs as $input ) {
				$values[ $input['id'] ] = $entry[ $input['id'] ];
			}

			if ( empty( array_filter( array_merge( $values, $field_value ) ) ) ) {
				return new WP_Error( strtolower( $field_type ) . '_validation_failed', __( 'This field must have at least one checked option.', 'gk-gravityedit' ) );
			};

			return true;
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
