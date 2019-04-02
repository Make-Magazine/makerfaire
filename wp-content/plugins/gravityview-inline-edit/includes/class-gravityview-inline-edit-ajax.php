<?php

/**
 * Handle all operations related to custom GravityView fields
 *
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
			self::$instance = new self;
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
		add_action( 'init', array( $this, 'process_inline_edit_callbacks' ) );
	}

	/**
	 * Check if x-editable POST field `gv_inline_edit_field` is set. If it is, transform value into an x-editable field
	 *
	 * @todo Should we use admin-ajax.php instead?
	 *
	 * @since 1.0
	 *
	 * @return void
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
	 * @param int $passed_input_id ID of input
	 *
	 * @return bool True: input is hidden; False: input is shown
	 */
	private function _is_input_hidden( $field, $passed_input_id ) {

		if ( is_array( $field->inputs ) ) {
			foreach ( $field->inputs as $input ) {

				list( $field_id, $input_id ) = explode( '.', $input['id'] );

				if ( intval( $passed_input_id ) === intval( $input_id ) ) {
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
			wp_send_json( new WP_Error( 'gravity_forms_inactive', __( 'Gravity Forms is not active.', 'gravityview-inline-edit' ) ) );
		}

		$entry_id   = sanitize_key( $_POST['pk'] );
		$type       = sanitize_key( $_POST['type'] );
		$form_id    = sanitize_key( $_POST['form_id'] );
		$field_id   = sanitize_key( $_POST['field_id'] );
		$view_id    = sanitize_key( $_POST['view_id'] );
		$post_value = rgpost( 'value' );

		if ( ! GravityView_Inline_Edit::get_instance()->can_edit_entry( $entry_id, $form_id, $view_id ) ) {
			wp_send_json( new WP_Error( 'insufficient_privileges', __( 'You are not allowed to edit this entry.', 'gravityview-inline-edit' ) ) );
		}

		$entry            = GFAPI::get_entry( $entry_id );
		$entry_pre_update = $entry;
		$form             = GFAPI::get_form( $form_id );
		$gf_field         = GFFormsModel::get_field( $form, $field_id );
		$values_to_update = array();

		// TODO: Move to inline field classes
		switch ( $type ) {
			case 'textarea':
				$field_validate                = $post_value;
				$values_to_update[ $field_id ] = $field_validate;
				break;
			case 'address':
				/** @var array $value */
				$value = $post_value;
				if ( 1 === count( $value ) ) {//single-field mode
					$gf_field->enableCopyValuesOption = $_POST[ 'input_' . $field_id . '_copy_values_activated' ] = true;//Ensure that validation doesn't treat this as a complete field
				}

				foreach ( $value as $input_number => $input_value ) {
					if ( $gf_field->isRequired && empty( $input_value ) ) {
						continue;
					}
					if ( $this->_is_input_hidden( $gf_field, $input_number ) ) {
						do_action( 'gravityview_log_debug', sprintf( '%s: Value not saved; input is hidden in the form (%d.%d)', __METHOD__, $gf_field->id, $input_number ) );
						continue;
					}
					$values_to_update[ $field_id . '.' . $input_number ] = $input_value;
					$_POST[ 'input_' . $field_id . '_' . $input_number ] = $entry[ $field_id . '.' . $input_number ];//Used for GF validation
				}
				$field_validate = $value;
				break;
			case 'name':
				/** @var array $value */
				$value = $post_value;

				if ( 1 === count( $value ) ) {//single-field mode
					$gf_field->nameFormat = 'simple';//Ensures that validate doesn't treat this as a full field
				}

				foreach ( $value as $input_number => $input_value ) {
					if ( $gf_field->isRequired && empty( $input_value ) ) {
						continue;
					}
					if ( $this->_is_input_hidden( $gf_field, $input_number ) ) {
						do_action( 'gravityview_log_debug', sprintf( '%s: Value not saved; input is hidden in the form (%d.%d)', __METHOD__, $gf_field->id, $input_number ) );
						continue;
					}
					$values_to_update[ $field_id . '.' . $input_number ] = $value[ $input_number ];
					$_POST[ 'input_' . $field_id . '_' . $input_number ] = $entry[ $field_id . '.' . $input_number ];//Used for GF validation
				}
				$field_validate = $value;
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
				$choice_number = 1;

				$input_number = rgpost( 'inputNumber' );

				foreach ( $gf_field->choices as $choice ) {
					if ( $choice_number % 10 === 0 ) { //hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
						$choice_number ++;
					}
					$input_id = $field_id . '.' . $choice_number;

					if ( ! $input_number || ( (int) $choice_number === (int) $input_number ) ) {
						if ( ! in_array( $choice['value'], (array) $post_value ) && isset( $entry[ $input_id ] ) ) {
							$entry[ $input_id ] = '';
						} else {
							$values_to_update[ $input_id ] = $choice['value'];
						}
					}
					$choice_number ++;
				}
				$field_validate = $post_value;
				break;
			case 'multiselect':
				/** @var array $post_value */

				//GF's currently has no validate method for multiselect. Do it here.
				if ( $gf_field->isRequired && empty( $post_value ) ) {
					wp_send_json( new WP_Error( 'multiselect_validation_failed', esc_html__( 'This field is required.', 'gravityview-inline-edit' ) ) );
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

				if ( count( $value ) > 1 && ( empty( $value[1] ) || empty( $value[2] ) ) ) {//We define a custom error message here because `$gf_field->validate` (used below), fails silently for this use-case. With count( $value ) > 1, we check if we are in single field mode
					wp_send_json( new WP_Error( 'invalid_time', __( 'Please enter a valid time.', 'gravityview-inline-edit' ) ) );
				}

				if ( 1 === count( $value ) ) {//Single field mode
					$saved_time = isset( $entry[ $field_id ] ) ? $entry[ $field_id ] : '00:00 AM';
					preg_match( '/^(\d*):(\d*) ?(.*)$/', $saved_time, $time_matches );
					for ( $i = 0; $i <= 3; $i ++ ) {//From the values matched, populate the hh,mm and am/pm fields of $value
						if ( ! isset( $value[ $i ] ) ) {
							$value[ $i ] = $time_matches[ $i ];
						}
					}
				}
				$field_validate                = intval( sanitize_text_field( $value[1] ) ) . ':' . intval( sanitize_text_field( $value[2] ) ) . ' ' . strtoupper( sanitize_text_field( $value[3] ) );
				$values_to_update[ $field_id ] = $field_validate;
				break;
			default:
				$field_validate                = $post_value;
				$values_to_update[ $field_id ] = $field_validate;
				break;
		}

		$validation_response = $this->validate_field( $field_validate, $gf_field, $type );

		if ( is_wp_error( $validation_response ) ) {
			wp_send_json( $validation_response );
		}

		//Sanitize the field
		foreach ( $values_to_update as $update_id => $update_value ) {
			$input_name          = 'input_' . str_replace( '.', '_', $update_id );
			$entry[ $update_id ] = GFFormsModel::prepare_value( $form, $gf_field, $update_value, $input_name, $entry_id );
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
	 * @param array $entry The entry object that will be updated
	 * @param int $form_id The Form ID that the entry is connected to
	 * @param GF_Field|null $gf_field Field of the value that will be updated, or null if no field exists (for entry meta)
	 * @param string $type Inline Edit type, defined in {@see GravityView_Inline_Edit_Field->inline_edit_type}
	 * @param array $original_entry Original entry object
	 *
	 * @return bool|WP_Error $update_result True: the entry has been updated by Gravity Forms or WP_Error if there was a problem
	 */
	private function _update_entry( $entry, $form_id = 0, $gf_field = null, $type = 'text', $original_entry = array() ) {

		/**
		 * @since 1.2.7
		 */
		$remove_hooks = apply_filters( 'gravityview-inline-edit/remove-gf-update-hooks', true );

		if( $remove_hooks ) {
			remove_all_filters( 'gform_entry_pre_update' );
			remove_all_filters( 'gform_form_pre_update_entry' );
			remove_all_filters( 'gform_form_pre_update_entry_' . $form_id );
			remove_all_actions( 'gform_post_update_entry' );
			remove_all_actions( 'gform_post_update_entry_' . $form_id );
		}

		$update_result = GFAPI::update_entry( $entry );

		/**
		 * @filter `gravityview-inline-edit/entry-updated` Inline Edit entry updated
		 *
		 * @since 1.0
		 * @since 1.1 Added $original_entry param
		 *
		 * @used-by GravityView_Inline_Edit::update_inline_edit_result
		 *
		 * @param bool|WP_Error $update_result True: the entry has been updated by Gravity Forms or WP_Error if there was a problem
		 * @param array $entry The Entry Object that's been updated
		 * @param int $form_id The Form ID
		 * @param GF_Field|null $gf_field The field that's been updated, or null if no field exists (for entry meta)
		 * @param array $original_entry Original entry, before being updated
		 */
		$update_result = apply_filters( 'gravityview-inline-edit/entry-updated', $update_result, $entry, $form_id, $gf_field, $original_entry );

		/**
		 * @filter `gravityview-inline-edit/entry-updated/{$type}` Inline Edit entry updated, where $type is the GravityView_Inline_Edit_Field->inline_edit_type string
		 *
		 * @since 1.0
		 * @since 1.1 Added $original_entry param
		 *
		 * @used-by GravityView_Inline_Edit::update_inline_edit_result
		 *
		 * @param bool|WP_Error $update_result True: the entry has been updated by Gravity Forms or WP_Error if there was a problem
		 * @param array $entry The Entry Object that's been updated
		 * @param int $form_id The Form ID
		 * @param GF_Field|null $gf_field The field that's been updated, or null if no field exists (for entry meta)
		 * @param array $original_entry Original entry, before being updated
		 */
		$update_result = apply_filters( 'gravityview-inline-edit/entry-updated/' . $type, $update_result, $entry, $form_id, $gf_field, $original_entry );

		return $update_result;
	}

	/**
	 * Validate inputs
	 *
	 * @since 1.0
	 *
	 * @param mixed $field_value The field value to validate
	 * @param GF_Field $gf_field The field to validate
	 * @param int $field_id The field ID
	 * @param string $field_type The type of the field
	 *
	 * @return boolean|WP_Error  true if all's well or WP_Error if the fields not valid
	 */
	private function validate_field( $field_value, $gf_field, $field_type ) {

		$gf_field->validate( $field_value, null );

		if ( $gf_field->failed_validation ) {
			$error_message = ( empty( $gf_field->validation_message ) ? __( 'Invalid value. Please try again.', 'gravityview-inline-edit' ) : $gf_field->validation_message );

			return new WP_Error( strtolower( $field_type ) . '_validation_failed', $error_message );
		}

		return true;
	}

}

GravityView_Inline_Edit_AJAX::get_instance();
