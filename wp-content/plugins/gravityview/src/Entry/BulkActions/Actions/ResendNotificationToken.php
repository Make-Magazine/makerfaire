<?php
/**
 * Frontend resend notification token helper.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0-beta.3
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

/**
 * Normalizes form-scoped notification tokens.
 *
 * @since 3.0.0-beta.3
 */
final class ResendNotificationToken {
	/**
	 * Builds a token from a form ID and notification ID.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int|string $form_id         Gravity Forms form ID.
	 * @param int|string $notification_id Gravity Forms notification ID.
	 *
	 * @return string
	 */
	public static function build( $form_id, $notification_id ) {
		$form_id         = (int) $form_id;
		$notification_id = trim( (string) $notification_id );

		if ( $form_id < 1 || '' === $notification_id || false !== strpos( $notification_id, ':' ) ) {
			return '';
		}

		return $form_id . ':' . $notification_id;
	}

	/**
	 * Parses a token into its form and notification IDs.
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

		list( $form_id, $notification_id ) = explode( ':', $token, 2 );
		$form_id                           = trim( $form_id );
		$notification_id                   = trim( $notification_id );

		if ( '' === $form_id || ! ctype_digit( $form_id ) || (int) $form_id < 1 || '' === $notification_id ) {
			return null;
		}

		return [
			'form_id'         => (int) $form_id,
			'notification_id' => $notification_id,
			'token'           => self::build( $form_id, $notification_id ),
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
