<?php
/**
 * Background job and bulk action notice helpers.
 *
 * @package GravityKit\GravityView\BackgroundJobs
 * @since 3.0.0
 */

namespace GravityKit\GravityView\BackgroundJobs;

/**
 * Normalizes and renders trusted notice data.
 *
 * @since 3.0.0
 */
final class ResultNotice {
	/**
	 * Normalizes a notice payload.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $notice Notice message string or array with a message.
	 *
	 * @return array{message:string}
	 */
	public static function normalize( $notice ): array {
		if ( is_array( $notice ) ) {
			if ( isset( $notice['notice'] ) ) {
				return self::normalize( $notice['notice'] );
			}

			return [
				'message' => self::normalize_message( $notice['message'] ?? '' ),
			];
		}

		return [
			'message' => self::normalize_message( $notice ),
		];
	}

	/**
	 * Normalizes a notice message.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $message Notice message.
	 *
	 * @return string
	 */
	public static function normalize_message( $message ): string {
		return wp_kses( (string) $message, self::allowed_html(), [ 'http', 'https' ] );
	}

	/**
	 * Returns allowed notice HTML.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	private static function allowed_html(): array {
		return [
			'a'      => [
				'href'   => true,
				'rel'    => true,
				'target' => true,
				'title'  => true,
			],
			'br'     => [],
			'code'   => [],
			'em'     => [],
			'strong' => [],
		];
	}

	/**
	 * Renders a notice paragraph.
	 *
	 * @since 3.0.0
	 *
	 * @param string $message Notice message.
	 *
	 * @return string
	 */
	public static function render( string $message ): string {
		return '<p>' . self::normalize_message( $message ) . '</p>';
	}
}
