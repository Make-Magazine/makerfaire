<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by The GravityKit Team on 11-September-2025 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Exceptions;

/**
 * Exception for user-related operations.
 *
 * @since 1.3.0
 */
class UserException extends BaseException {
	/**
	 * Creates exception for when user is not logged in.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Additional context.
	 *
	 * @return self
	 */
	public static function not_logged_in( array $data = [] ): self {
		return new self( 'user_not_logged_in', 'No logged-in user.', $data );
	}

	/**
	 * Creates exception for when user is not found.
	 *
	 * @since 1.3.0
	 *
	 * @param int                 $user_id User ID that was not found.
	 * @param array<string,mixed> $data    Additional context.
	 *
	 * @return self
	 */
	public static function not_found( int $user_id, array $data = [] ): self {
		$data['user_id'] = $user_id;

		return new self( 'user_not_found', sprintf( 'User with ID %d does not exist.', $user_id ), $data );
	}
}
