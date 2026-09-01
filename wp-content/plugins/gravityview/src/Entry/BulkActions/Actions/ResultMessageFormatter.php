<?php
/**
 * Frontend bulk action result message formatting.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

/**
 * Formats common bulk action result messages.
 *
 * @since 3.0.0
 */
final class ResultMessageFormatter {
	/**
	 * Formats a processed/failed result message.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $processed      Number of processed entries.
	 * @param int    $failed         Number of failed entries.
	 * @param string $plural_message Plural success message.
	 * @param string $single_message Single success message.
	 *
	 * @return string
	 */
	public static function processed( $processed, $failed, $plural_message, $single_message ) {
		$message = strtr(
			1 === (int) $processed ? $single_message : $plural_message,
			[
				'[count]' => (int) $processed,
			]
		);

		if ( $failed ) {
			$message .= ' ' . strtr(
				/* translators: [count] is the number of entries that could not be processed. */
				_n( '[count] entry could not be processed.', '[count] entries could not be processed.', (int) $failed, 'gk-gravityview' ),
				[
					'[count]' => (int) $failed,
				]
			);
		}

		return $message;
	}
}
