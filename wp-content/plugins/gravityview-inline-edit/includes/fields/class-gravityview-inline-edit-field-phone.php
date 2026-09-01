<?php

/**
 * @file class-gravityview-inline-edit-field-phone.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Phone extends GravityView_Inline_Edit_Field {

	/**
	 * The phone format that stores an object instead of the string the person typed.
	 *
	 * Gravity Forms 3.0 added "International (formatted)", made it the default for a new phone
	 * field, and made it the fallback for a field saved without a format at all. It stores
	 * {country, national, formatted, e164} as JSON, and both GF_Field_Phone::validate() and
	 * GF_Field_Phone::get_value_save_input() reject anything else: validate() fails the save, and
	 * get_value_save_input() returns an empty string. Gravity Forms below 3.0 has no such format.
	 *
	 * @since 2.10.0
	 *
	 * @var string
	 */
	const OBJECT_PHONE_FORMAT = 'formatted';

	public $gv_field_name = 'phone';

	public $inline_edit_type = 'tel';

	public $set_value = true;

	/**
	 * Whether this field stores its value as the Gravity Forms international phone object.
	 *
	 * Asks Gravity Forms whether it offers the format rather than comparing versions, so the answer
	 * follows the Gravity Forms that is actually running, including the `gform_phone_formats` filter.
	 *
	 * @since 2.10.0
	 *
	 * @param GF_Field|null $gf_field The field being saved or rendered.
	 *
	 * @return bool True: the field stores a JSON object. False: it stores the string as typed.
	 */
	public static function stores_phone_object( $gf_field ) {
		$is_phone_field = $gf_field instanceof GF_Field_Phone;

		if ( ! $is_phone_field ) {
			return false;
		}

		$is_object_format = self::OBJECT_PHONE_FORMAT === rgobj( $gf_field, 'phoneFormat' );

		if ( ! $is_object_format ) {
			return false;
		}

		$formats = $gf_field->get_phone_formats();

		return is_array( $formats ) && isset( $formats[ self::OBJECT_PHONE_FORMAT ] );
	}

	/**
	 * Turn the number a person typed into the value Gravity Forms stores for this field.
	 *
	 * The inline editor is a text box, so it posts a plain string. For every phone format but the
	 * international object one that string *is* the stored value and comes back untouched. For the
	 * object format the number is resolved through Gravity Forms' own E164Validator, which supplies
	 * the country and national number, and the object is handed back for GF_Field_Phone to validate
	 * and sanitize as though it had arrived from the form's own international phone control.
	 *
	 * A number that cannot be resolved is refused rather than translated. Gravity Forms would refuse
	 * it too, and its get_value_save_input() answers an unresolvable value with an empty string,
	 * which would erase the number already on the entry.
	 *
	 * @since 2.10.0
	 *
	 * @param GF_Field|null $gf_field The field being saved.
	 * @param mixed         $value    The posted value.
	 *
	 * @return mixed|WP_Error The value to validate and store, or an error explaining the refusal.
	 */
	public static function prepare_save_value( $gf_field, $value ) {
		$stores_object = self::stores_phone_object( $gf_field );

		if ( ! $stores_object || ! is_string( $value ) ) {
			return $value;
		}

		// An empty value clears the field, which Gravity Forms allows for any format.
		$is_blank = '' === trim( $value );

		// A value that is already the object needs no translating; Gravity Forms validates it next.
		$is_already_object = GFCommon::is_json( $value );

		if ( $is_blank || $is_already_object ) {
			return $value;
		}

		$e164    = self::to_e164( $value );
		$details = self::describe_e164( $e164 );

		$is_resolved = ! empty( $details['valid'] ) && self::is_country_code( rgar( $details, 'territory' ) );

		if ( ! $is_resolved ) {
			return new WP_Error(
				'tel_validation_failed',
				esc_html__( 'Enter the phone number in international format, including the country code. For example: +1 555 123 4567', 'gk-gravityedit' )
			);
		}

		return wp_json_encode( array(
			'country'   => rgar( $details, 'territory' ),
			'national'  => rgar( $details, 'national_number' ),

			// What the person typed is the human-readable form of this number, and it is what the
			// entries table shows, since GF_Field_Phone::get_value_entry_list() returns this member.
			'formatted' => $value,
			'e164'      => $e164,
		) );
	}

	/**
	 * Reads the stored value back as a number a person can edit.
	 *
	 * The editor is seeded from `data-value`, which the parent fills with the raw entry value. For
	 * the object format that raw value is JSON, so the editor would open showing
	 * `{"country":"US",…}`. The E.164 member is preferred over the formatted one because it always
	 * carries the country code, so what the editor opens with is always something a save can resolve.
	 *
	 * @since 2.10.0
	 *
	 * @param GF_Field|null $gf_field The field being rendered.
	 * @param mixed         $value    The stored entry value.
	 *
	 * @return mixed The number to edit.
	 */
	public static function prepare_edit_value( $gf_field, $value ) {
		$stores_object = self::stores_phone_object( $gf_field );
		$is_object     = $stores_object && is_string( $value ) && GFCommon::is_json( $value );

		if ( ! $is_object ) {
			return $value;
		}

		$decoded = json_decode( $value, true );

		if ( ! is_array( $decoded ) ) {
			return $value;
		}

		return rgar( $decoded, 'e164', rgar( $decoded, 'formatted', $value ) );
	}

	/**
	 * @inheritDoc
	 *
	 * @since 2.10.0
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {
		$wrapper_attributes = parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );

		if ( ! $this->set_value ) {
			return $wrapper_attributes;
		}

		$wrapper_attributes['data-value'] = self::prepare_edit_value( $gf_field, rgar( $entry, $field_id ) );

		return $wrapper_attributes;
	}

	/**
	 * Reads a typed phone number as an E.164 number.
	 *
	 * Everything but the digits is dropped, since E.164 is digits behind a plus sign. A number
	 * written with an international dialling prefix instead of a plus sign (00 in most of the world,
	 * 011 in the North American Numbering Plan) has that prefix removed rather than read as part of
	 * the country code.
	 *
	 * @since 2.10.0
	 *
	 * @param string $value The typed number.
	 *
	 * @return string The E.164 candidate, or an empty string when there were no digits.
	 */
	private static function to_e164( $value ) {
		$trimmed = trim( $value );
		$digits  = preg_replace( '/\D/', '', $trimmed );

		if ( '' === $digits ) {
			return '';
		}

		$is_written_with_plus = 0 === strpos( $trimmed, '+' );

		if ( ! $is_written_with_plus ) {
			$dialling_prefixes = array( '011', '00' );

			foreach ( $dialling_prefixes as $prefix ) {
				if ( 0 === strpos( $digits, $prefix ) ) {
					$digits = substr( $digits, strlen( $prefix ) );
					break;
				}
			}
		}

		return '' === $digits ? '' : '+' . $digits;
	}

	/**
	 * Resolves an E.164 number through Gravity Forms' own E.164 validator.
	 *
	 * E164Validator is the same class GF_Field_Phone::validate() uses, and it is loaded the same
	 * way, so a number this accepts is a number Gravity Forms accepts.
	 *
	 * @since 2.10.0
	 *
	 * @param string $e164 The E.164 candidate.
	 *
	 * @return array The validator's detailed result, or an invalid result when it is unavailable.
	 */
	private static function describe_e164( $e164 ) {
		$invalid = array( 'valid' => false );

		if ( '' === $e164 ) {
			return $invalid;
		}

		if ( ! class_exists( 'E164Validator' ) ) {
			$validator_path = GFCommon::get_base_path() . '/includes/validation/class-e164-validator.php';

			if ( ! file_exists( $validator_path ) ) {
				return $invalid;
			}

			require_once $validator_path;
		}

		$validator = new E164Validator();

		return (array) $validator->validate( $e164, true );
	}

	/**
	 * Whether a value is a two-letter country code.
	 *
	 * GF_Field_Phone::get_value_save_input() throws the whole number away when the country member is
	 * not exactly two characters, so a value that would not survive that is refused before it is
	 * built rather than after it has erased the entry.
	 *
	 * @since 2.10.0
	 *
	 * @param mixed $country The country member.
	 *
	 * @return bool
	 */
	private static function is_country_code( $country ) {
		return is_string( $country ) && 2 === strlen( $country );
	}
}

new GravityView_Inline_Edit_Field_Phone;
