<?php
/**
 * Frontend download attachment field token helper.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0-beta.3
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

/**
 * Normalizes form-scoped file field tokens.
 *
 * @since 3.0.0-beta.3
 */
final class DownloadAttachmentFieldToken {
	/**
	 * Builds a token from a form ID and field ID.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int|string $form_id  Gravity Forms form ID.
	 * @param int|string $field_id Gravity Forms field ID.
	 *
	 * @return string
	 */
	public static function build( $form_id, $field_id ) {
		$form_id  = (int) $form_id;
		$field_id = trim( (string) $field_id );

		if ( $form_id < 1 || '' === $field_id || false !== strpos( $field_id, ':' ) || ! is_numeric( $field_id ) || (float) $field_id <= 0 ) {
			return '';
		}

		return $form_id . ':' . $field_id;
	}

	/**
	 * Parses a token into its form and field IDs.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $token Token value.
	 *
	 * @return array|null
	 */
	public static function parse( $token ) {
		if ( ! is_scalar( $token ) ) {
			return null;
		}

		$token = trim( (string) $token );

		if ( '' === $token || 1 !== substr_count( $token, ':' ) ) {
			return null;
		}

		list( $form_id, $field_id ) = explode( ':', $token, 2 );
		$form_id                    = trim( $form_id );
		$field_id                   = trim( $field_id );

		if ( '' === $form_id || ! ctype_digit( $form_id ) || (int) $form_id < 1 || '' === $field_id || ! is_numeric( $field_id ) || (float) $field_id <= 0 ) {
			return null;
		}

		return [
			'form_id'  => (int) $form_id,
			'field_id' => $field_id,
			'token'    => self::build( $form_id, $field_id ),
		];
	}

	/**
	 * Normalizes a list of token values.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $tokens Raw token list.
	 *
	 * @return string[]
	 */
	public static function normalize_list( $tokens ) {
		if ( ! is_array( $tokens ) ) {
			$tokens = is_scalar( $tokens ) ? [ $tokens ] : [];
		}

		$normalized = [];

		foreach ( $tokens as $token ) {
			$parsed = self::parse( $token );

			if ( null === $parsed || isset( $normalized[ $parsed['token'] ] ) ) {
				continue;
			}

			$normalized[ $parsed['token'] ] = $parsed['token'];
		}

		return array_values( $normalized );
	}
}
