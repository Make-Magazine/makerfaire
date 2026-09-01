<?php

/**
 * @file class-gravityview-inline-edit-field-text.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Text extends GravityView_Inline_Edit_Field {

	/**
	 * The mask characters that stand for something a person types, and what each one accepts.
	 *
	 * These are the three Gravity Forms recognises in GF_Field::mask_to_regex(); every other
	 * character in a mask is a literal the mask supplies itself. Kept with the same meanings, so a
	 * value built from them is one that method's regex accepts.
	 *
	 * @since 2.10.0
	 *
	 * @var array<string, string>
	 */
	const MASK_PLACEHOLDERS = array(
		'9' => '/^[0-9]$/',
		'a' => '/^[a-zA-Z]$/',
		'*' => '/^[a-zA-Z0-9]$/',
	);

	public $gv_field_name = 'text';

	public $inline_edit_type = 'text';

	/**
	 * Whether this field's input mask is measured when the entry is saved.
	 *
	 * Gravity Forms 3.0 added the check to GF_Field_Text::validate(). Before it, a mask was a
	 * browser-side convenience and any string reached the entry. Asking for the method that does
	 * the measuring keeps this following the Gravity Forms that is running rather than a version
	 * number.
	 *
	 * @since 2.10.0
	 *
	 * @param GF_Field|null $gf_field The field being saved.
	 *
	 * @return bool True: a value that does not fit the mask will be refused.
	 */
	public static function enforces_input_mask( $gf_field ) {
		// GF_Field_Text is the only class whose validate() measures a mask, so a number or hidden
		// field carrying the property is left alone even though it posts the same inline-edit type.
		$is_text_field = $gf_field instanceof GF_Field_Text;

		if ( ! $is_text_field ) {
			return false;
		}

		$measures_mask = method_exists( $gf_field, 'mask_to_regex' );

		if ( ! $measures_mask ) {
			return false;
		}

		$mask_is_enabled = ! empty( rgobj( $gf_field, 'inputMask' ) );
		$mask            = (string) rgobj( $gf_field, 'inputMaskValue' );

		return $mask_is_enabled && '' !== $mask;
	}

	/**
	 * Turn what a person typed into the value a masked field stores.
	 *
	 * A form gives a masked field an input that types the mask's punctuation as you go, so what it
	 * submits already carries it. The inline editor is a plain text box with no mask on it, so the
	 * value that arrives is whatever was typed, and on Gravity Forms 3.0 that value is measured
	 * against the mask before it is stored. Filling the mask here is the work the browser would
	 * have done, which is what makes the stored value identical to a real submission's.
	 *
	 * A value the mask cannot hold is handed back exactly as it arrived rather than bent into
	 * something that fits. Gravity Forms then refuses it and names the format it wanted, so the
	 * person sees why nothing was saved instead of finding a value they did not type.
	 *
	 * @since 2.10.0
	 *
	 * @param GF_Field|null $gf_field The field being saved.
	 * @param mixed         $value    The posted value.
	 *
	 * @return mixed The value to validate and store.
	 */
	public static function prepare_save_value( $gf_field, $value ) {
		$has_enforced_mask = self::enforces_input_mask( $gf_field );

		if ( ! $has_enforced_mask || ! is_string( $value ) ) {
			return $value;
		}

		// An empty value clears the field, and Gravity Forms skips the mask check for one, so an
		// optional masked field stays clearable.
		$is_blank = '' === trim( $value );

		if ( $is_blank ) {
			return $value;
		}

		$filled = self::fill_mask( (string) rgobj( $gf_field, 'inputMaskValue' ), $value );

		return null === $filled ? $value : $filled;
	}

	/**
	 * Lay a typed value into a mask, supplying the mask's own punctuation.
	 *
	 * Walks the mask and the value together without ever skipping ahead in the value: a character
	 * that does not belong where the mask wants it fails the whole fill, and so does a value with
	 * characters left over once the mask runs out. Both refusals matter, because the alternative to
	 * each is storing less than was typed and calling it a save.
	 *
	 * A value that already carries the mask's punctuation has it consumed on the way past, so
	 * filling a mask that is already filled returns the same string.
	 *
	 * Characters are compared one byte at a time, as GF_Field::mask_to_regex() builds its regex, so
	 * a multibyte character never satisfies a placeholder. Gravity Forms refuses such a value too.
	 *
	 * @since 2.10.0
	 *
	 * @param string $mask  The mask pattern, e.g. `(999) 999-9999`.
	 * @param string $value The value that was typed.
	 *
	 * @return string|null The filled value, or null when the value does not fit the mask.
	 */
	private static function fill_mask( $mask, $value ) {
		$filled       = '';
		$position     = 0;
		$value_length = strlen( $value );
		$mask_length  = strlen( $mask );
		$is_optional  = false;

		for ( $index = 0; $index < $mask_length; $index++ ) {
			$mask_character = $mask[ $index ];

			// Everything after `?` is optional, which is what mask_to_regex() encodes by adding a
			// `?` quantifier to every pattern that follows it.
			$starts_optional_part = '?' === $mask_character;

			if ( $starts_optional_part ) {
				$is_optional = true;

				continue;
			}

			$has_more_typed = $position < $value_length;

			if ( ! $has_more_typed ) {
				// The value stopped early, which is a complete value only once the rest is optional.
				if ( $is_optional ) {
					break;
				}

				return null;
			}

			$typed_character = $value[ $position ];
			$is_placeholder  = isset( self::MASK_PLACEHOLDERS[ $mask_character ] );

			if ( ! $is_placeholder ) {
				// A literal is supplied by the mask. When the value already carries it, step over
				// it so it is not read as something a placeholder should have taken.
				$filled .= $mask_character;

				if ( $typed_character === $mask_character ) {
					++$position;
				}

				continue;
			}

			$fits = (bool) preg_match( self::MASK_PLACEHOLDERS[ $mask_character ], $typed_character );

			if ( ! $fits ) {
				return null;
			}

			$filled .= $typed_character;
			++$position;
		}

		$has_leftover = $position < $value_length;

		return $has_leftover ? null : $filled;
	}
}

new GravityView_Inline_Edit_Field_Text;
