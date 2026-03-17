<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Exceptions;

/**
 * Exception for distributed lock acquisition failures.
 *
 * Used when a process cannot acquire a lock for a critical section,
 * typically to prevent concurrent operations like API calls or cache updates.
 *
 * @since 1.7.0
 */
class LockAcquisitionException extends BaseException {
	/**
	 * Creates exception for when lock acquisition fails.
	 *
	 * @since 1.7.0
	 *
	 * @param string              $lock_key Lock key that failed to acquire.
	 * @param string              $message  User-facing error message.
	 * @param array<string,mixed> $data     Additional context.
	 *
	 * @return self
	 */
	public static function failed( string $lock_key, string $message = '', array $data = [] ): self {
		$data['lock_key'] = $lock_key;

		if ( empty( $message ) ) {
			$message = esc_html__( 'Failed to acquire distributed lock. Another process may be running.', 'gk-gravityedit' );
		}

		return new self( 'lock_acquisition_failed', $message, $data );
	}
}
