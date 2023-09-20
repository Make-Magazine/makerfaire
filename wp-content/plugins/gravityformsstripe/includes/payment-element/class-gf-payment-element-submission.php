<?php
/**
 * Gravity Forms Stripe Payment Element Submission manager.
 *
 * This class acts as a wrapper for all the logic required to create an entry from a payment element submission.
 *
 * @since     5.0
 * @package   GravityForms
 * @author    Rocketgenius
 * @copyright Copyright (c) 2021, Rocketgenius
 */
class GF_Payment_Element_Submission {

	/**
	 * Instance of a GFStripe object.
	 *
	 * @since 5.0
	 *
	 * @var GFStripe
	 */
	protected $addon;


	/**
	 * The form ID of the form being processed.
	 *
	 * @since 5.0
	 *
	 * @var integer
	 */
	public $form_id;

	/**
	 * The feed ID of the feed being processed.
	 *
	 * @since 5.0
	 *
	 * @var integer
	 */
	public $feed_id;

	/**
	 * The draft ID created after form validation was successful.
	 *
	 * @since 5.0
	 *
	 * @var string
	 */
	public $draft_id;

	/**
	 * The entry ID of the entry being processed.
	 *
	 * @since 5.0
	 *
	 * @var int The entry ID of the entry being processed.
	 */
	public $entry_id;

	/**
	 * The payment object ID used to create the entry being processed (Payment intent, Setup intent or an invoice)
	 *
	 * @since 5.0
	 *
	 * @var int The intent ID used to create the entry being processed.
	 */
	public $payment_object_id;

	/**
	 * The Subscription ID if this submission is for a subscription payment.
	 *
	 * @since 5.0
	 *
	 * @var int The Subscription ID if this submission is for a subscription payment.
	 */
	public $subscription_id;

	/**
	 * GF_Stripe_Payment_Element_Submission constructor.
	 *
	 * @since 5.0
	 *
	 * @param GFStripe $addon Instance of a GFStripe object.
	 */
	public function __construct( $addon ) {
		$this->addon = $addon;
	}

	/**
	 * Validates a form submission.
	 *
	 * @since 5.0
	 *
	 * @param int $form_id The ID of the form being validated.
	 *
	 * @return bool
	 */
	public function validate( $form_id ) {
		$result = GFAPI::validate_form( $form_id );
		return rgar( $result, 'is_valid', false );
	}

	/**
	 * Creates a draft submission.
	 *
	 * @since 5.0
	 *
	 * @param int $form_id The ID of the form being submitted.
	 *
	 * @return string|WP_Error
	 */
	public function create_draft_submission( $form_id, $stripe_encrypted_params ) {

		$files                           = array();
		$form                            = GFAPI::get_form( $form_id );
		$field_values                    = RGForms::post( 'gform_field_values' );
		$lead                            = GFFormsModel::get_current_lead();
		$lead['stripe_encrypted_params'] = $stripe_encrypted_params;
		$lead['version_hash']            = rgpost( 'version_hash' );
		require_once GFCommon::get_base_path() . '/form_display.php';
		$form  = GFFormDisplay::update_confirmation( $form, $lead, 'form_saved' );
		$files = $this->handle_file_uploads( $form );

		$resume_token = GFFormsModel::save_draft_submission(
			$form,
			$lead,
			$field_values,
			GFFormDisplay::get_source_page( $form_id ),
			$files,
			GFFormsModel::get_form_unique_id( $form_id ),
			rgars( $form, 'personalData/preventIP' ) ? '' : GFFormsModel::get_ip(),
			esc_url_raw( GFFormsModel::get_current_page_url() ),
			sanitize_key( rgpost( 'gform_resume_token' ) )
		);

		return $resume_token;
	}

	/**
	 * Handles uploading the files in the submission.
	 *
	 * @since 5.0
	 *
	 * @param array $form The form object.
	 *
	 * @return array
	 */
	public function handle_file_uploads( $form ) {
		$files = array();
		// Handle single file uploads.
		foreach ( $form['fields'] as $field ) {
			if ( $field->type === 'fileupload' && ! $field->multipleFiles && isset( $_FILES[ 'input_' . $field->id ] ) && ! empty( $_FILES[ 'input_' . $field->id ]['name'] ) ) {
				// File exists in $_FILES and is a single file, just upload it.
				$files[ 'input_' . $field->id ] = $field->upload_file( $form['id'], wp_unslash( $_FILES[ 'input_' . $field->id ] ) );
			} elseif (
				$field->type === 'fileupload'
				&& ! $field->multipleFiles
				&& empty( $_POST[ 'input_' . $field->id ] )
				&& ! empty( $_POST['gform_uploaded_files'] )
			) {
				// File was uploaded in a previous submission, we need to extract the temp file name and then move it
				$file_data = json_decode( stripslashes( $_POST['gform_uploaded_files'] ), true );
				foreach ( $file_data as $input_id => $file_name ) {
					if ( $input_id == 'input_' . $field->id ) {
						$file_extension                 = pathinfo( $file_name, PATHINFO_EXTENSION );
						$files[ 'input_' . $field->id ] = array(
							'uploaded_filename' => $file_name,
							'temp_filename'     => $_POST['gform_unique_id'] . '_input_' . $field->id . '.' . $file_extension,
						);

						$files[ 'input_' . $field->id ] = $field->move_temp_file( $form['id'], $files[ 'input_' . $field->id ] );
					}
				}
			}
		}

		// Handle multi file uploads.
		$multi_file_uploads_json = str_replace( '\\', '', rgar( $_POST, 'gform_uploaded_files' ) );
		$multi_file_uploads      = json_decode( $multi_file_uploads_json, true );
		if ( ! empty( $multi_file_uploads ) ) {
			foreach ( $form['fields'] as $field ) {
				if ( $field->type === 'fileupload' && $field->multipleFiles && isset( $multi_file_uploads[ 'input_' . $field->id ] ) ) {
					$files[ 'input_' . $field->id ] = array();
					foreach ( $multi_file_uploads[ 'input_' . $field->id ] as $file_upload_info ) {
						$files[ 'input_' . $field->id ][] = $field->move_temp_file( $form['id'], $file_upload_info );
					}
					$files[ 'input_' . $field->id ] = json_encode( $files[ 'input_' . $field->id ] );
				}
			}
		}

		return $files;
	}


	/**
	 * Processes a form submission.
	 *
	 * @since 5.0
	 *
	 * @param int                                       $form_id           The id of the form being submitted.
	 * @param int                                       $feed_id           The id of the feed being processed.
	 * @param int                                       $draft_id          The id of the draft created when checkout started.
	 * @param \Stripe\PaymentIntent|\Stripe\SetupIntent $intent            The intent created when checkout started.
	 * @param int|null                                  $subscription_id   The subscription ID if this is a subscription payment.
	 *
	 * @return false|void
	 */
	public function process_submission( $form_id, $feed_id, $draft_id, $intent, $subscription_id = null ) {

		// Store the IDs, because once the form processing starts, other methods will be called that need to use the same values.
		$this->draft_id          = $draft_id;
		$this->form_id           = $form_id;
		$this->feed_id           = $feed_id;
		$this->payment_object_id = $intent->id;
		$this->subscription_id   = $subscription_id;

		// Prepare the submission values from the draft created while validating the form.
		$draft      = GFFormsModel::get_draft_submission_values( $this->draft_id );
		$submission = json_decode( rgar( $draft, 'submission' ), true );
		if ( ! $submission ) {
			return false;
		}

		// Gravity Flow stripe extension should not run until the actual form submission is being triggered.
		if ( function_exists( 'gravity_flow' ) ) {
			remove_action( 'gform_post_add_entry', array( gravity_flow(), 'action_gform_post_add_entry' ) );
		}

		// This adds the entry, but doesn't run the complete form processing flow.
		$this->entry_id = GFAPI::add_entry( $submission['partial_entry'] );

		if ( function_exists( 'gravity_flow' ) ) {
			add_action( 'gform_post_add_entry', array( gravity_flow(), 'action_gform_post_add_entry' ), 10, 2 );
		}

		// Populate $_POST from the submission values and the intent values.
		$this->prepare_post_values( $submission, $intent );

		// `GFFormsModel::$uploaded_files` is wiped out during `GFFormDisplay::process_form()`
		// So setting it before processing the submission will not work
		// This filter allows us to add the file values before the field values are being saved to the entry meta directly.
		add_filter(
			'gform_save_field_value',
			function( $value, $entry, $field, $form, $input_id ) {

				if ( empty( $_POST[ 'input_' . $input_id ] ) ) {
					return $value;
				}

				$input_post_value = filter_var( $_POST[ 'input_' . $input_id ], FILTER_SANITIZE_URL );

				if ( $entry['id'] !== $this->entry_id || ! $input_post_value ) {
					return $value;
				}

				if ( $field->type === 'fileupload' ) {

					GFFormsModel::$uploaded_files[ $form['id'] ] = array( 'input_' . $input_id => $input_post_value );

					return $input_post_value;
				}

				return $value;
			},
			1,
			5
		);

		// since we are simulating a form submission, we need to set the target page to be always 0
		// in case of a multi-page form, instead of just validating the page, the form will be actually submitted.
		add_filter(
			'gform_target_page',
			function( $page_number, $form, $current_page, $field_values ) {
				return 0;
			},
			1,
			4
		);

		// Run the complete form processing flow.
		require_once GFCommon::get_base_path() . '/form_display.php';
		GFFormDisplay::process_form( $this->form_id, GFFormDisplay::SUBMISSION_INITIATED_BY_WEBFORM );

		// Delete the draft.
		GFFormsModel::delete_draft_submission( $this->draft_id );

		// Get the entry and form to handle confirmation.
		$entry        = GFAPI::get_entry( $this->entry_id );
		$form         = GFAPI::get_form( $this->form_id );
		$confirmation = GFFormDisplay::handle_confirmation( $form, $entry, false );

		if ( is_array( $confirmation ) && isset( $confirmation['redirect'] ) ) {
			header( "Location: {$confirmation['redirect']}" );
			exit;
		}

		GFFormDisplay::$submission[ $this->form_id ] = array(
			'is_confirmation'      => true,
			'confirmation_message' => $confirmation,
			'form'                 => $form,
			'lead'                 => $entry,
		);
	}


	/**
	 * Extracts the order data from the submission.
	 *
	 * This function will also create a temporary entry array that is not saved to DB to be used later with other functions.
	 *
	 * @since 5.0
	 *
	 * @param int   $feed_id The id of the current feed object being processed.
	 * @param int   $form_id The id of the current form object being processed.
	 * @param array $entry   The entry being processed.
	 *
	 * @return array The order data including the temporary entry array.
	 */
	public function extract_order_from_submission( $feed_id, $form_id, $entry = null ) {
		$feed            = $this->addon->get_feed( $feed_id );
		$form            = GFAPI::get_form( $form_id );
		$form_meta       = GFFormsModel::get_form_meta( $form['id'] );
		$temp_lead       = $entry === null ? GFFormsModel::create_lead( $form_meta ) : $entry;
		$submission_data = $this->addon->get_order_data( $feed, $form, $temp_lead );
		$line_items      = array();
		$item_total      = 0;
		$shipping        = 0;
		$discount_total  = 0;

		$items = rgar( $submission_data, 'line_items', rgar( $submission_data, 'items', array() ) );
		foreach ( $items as $item ) {
			if ( rgar( $item, 'is_shipping' ) && $item['is_shipping'] === 1 ) {
				$shipping = $item['unit_price'] * $item['quantity'];
			} else {
				$line_items[] = array(
					'name'        => GFCommon::safe_substr( $item['name'], 0, 127 ),
					'description' => GFCommon::safe_substr( $item['description'], 0, 127 ),
					'unit_amount' => array(
						'value'         => strval( $item['unit_price'] ),
						'currency_code' => rgar( $temp_lead, 'currency' ),
					),
					'quantity'    => $item['quantity'],
				);

				$item_total += GFCommon::to_number( $item['unit_price'] * $item['quantity'] );
			}
		}

		foreach ( $submission_data['discounts'] as $discount ) {
			$discount_total += GFCommon::to_number( $discount['unit_price'] * $discount['quantity'] );
		}

		return array(
			'line_items'    => $line_items,
			'subTotal'      => $item_total,
			'total'         => $submission_data['payment_amount'],
			'shipping'      => $shipping,
			'discountTotal' => $discount_total,
			'setup_fee'     => rgar( $submission_data, 'setup_fee', 0 ),
			'trial_days'    => rgars( $feed, 'meta/trialPeriod' ) ? $submission_data['trial'] : 0,
			'temp_lead'     => $temp_lead,
			'submission'    => $submission_data,
		);
	}

	/**
	 * Populate POST values from the intent and submission data before simulating form processing.
	 *
	 * @since 5.0
	 *
	 * @param array                 $submission             The submission data.
	 * @param \Stripe\PaymentIntent $intent The payment intent.
	 *
	 * @return void
	 */
	private function prepare_post_values( $submission, $intent ) {
		foreach ( $submission['partial_entry'] as $key => $value ) {
			if ( is_numeric( $key ) ) {
				$_POST[ 'input_' . str_replace( '.', '_', $key ) ] = $value;
			} else {
				$_POST[ $key ] = $value;
			}
		}
		$_POST[ 'is_submit_' . $this->form_id ] = '1';
		$_POST[ 'state_' . $this->form_id ]     = '1';
		$_POST['gform_submit']                  = $this->form_id;
		$_POST['version_hash']                  = $submission['partial_entry']['version_hash'];

		$files = rgar( $submission, 'files' );
		if ( ! empty( $files ) ) {
			foreach ( $files as $input_id => $file_data ) {
				$_POST[ $input_id ] = $file_data;
			}
		}

		if ( rgars( $intent, 'charges/data/0/payment_method_details/card/brand' ) ) {
			$_POST['stripe_credit_card_type']      = rgars( $intent, 'charges/data/0/payment_method_details/card/brand' );
			$_POST['stripe_credit_card_last_four'] = 'XXXXXXXXXXXX' . rgars( $intent, 'charges/data/0/payment_method_details/card/last4' );
		} else {
			$payment_method_type = rgars( $intent, 'payment_method/type' );
			$payment_method_data = rgars( $intent, 'payment_method/' . $payment_method_type );

			if ( empty( $payment_method_data ) ) {
				$billing_details = rgars( $intent, 'payment_method/billing_details' );
				if ( is_object( $billing_details ) ) {
					$payment_method_data = $billing_details->toArray();
				}
			}

			switch ( $payment_method_type ) {
				case 'link':
					$payment_method_data_string = 'Email: ' . $payment_method_data['email'];

					break;

				default:
					if ( is_object( $payment_method_data ) || is_array( $payment_method_data ) ) {
						$payment_method_data        = (array) $payment_method_data;
						$payment_method_data_string = '';
						foreach ( $payment_method_data as $key => $value ) {
							if ( is_array( $key ) || is_object( $key ) || is_array( $value ) || is_object( $value ) || empty( $value ) ) {
								continue;
							}
							$payment_method_data_string .= ucwords( str_replace( '_', ' ', $key ) ) . ': ' . ucwords( str_replace( '_', ' ', $value ) ) . "\n";
						}
					} else {
						$payment_method_data_string = $payment_method_data;
					}
			}

			$_POST['stripe_credit_card_type']      = ucwords( str_replace( '_', ' ', $payment_method_type ) );
			$_POST['stripe_credit_card_last_four'] = $payment_method_data_string;
		}
	}

	/**
	 * Hooks to the pre save lead filter and returns the current entry ID.
	 *
	 * This prevents creating a new entry when calling process_form().
	 *
	 * @since 5.0
	 *
	 * @param int   $id   The ID of the entry.
	 * @param array $form The current form being processed.
	 *
	 * @return int
	 */
	public function get_pending_entry_id( $id, $form ) {
		if ( rgar( $form, 'id' ) == $this->form_id ) {
			return $this->entry_id;
		}

		return $id;
	}

	/**
	 * Hooks to the skip field validation to skip the fields validating while simulating form processing as the fields were already validated but would fail state validation.
	 *
	 * @since 5.0
	 *
	 * @param array    $result  An array containing the validation result properties.
	 * @param mixed    $value   The field value currently being validated.
	 * @param array    $form    The form currently being validated.
	 * @param GF_Field $field   The field currently being validated.
	 *
	 * @return array|mixed
	 */
	public function maybe_skip_field_validation( $result, $value, $form, $field ) {
		if ( rgar( $form, 'id' ) == $this->form_id ) {
			return array(
				'is_valid' => true,
				'message'  => '',
			);
		}

		return $result;
	}
}
