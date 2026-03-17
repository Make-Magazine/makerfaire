<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Notices;

use GravityKit\GravityEdit\Foundation\Exceptions\BaseException;
use WP_Error;
use Throwable;

/**
 * Provides convenient factories for common failure categories.
 *
 * @since 1.3.0
 */
class NoticeException extends BaseException {
	/**
	 * Validation/definition problems.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const VALIDATION_FAILED = 'validation_failed';

	/**
	 * Persistence (database/user meta) failures.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const PERSISTENCE_FAILED = 'persistence_failed';

	/**
	 * Runtime evaluation failures.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const EVALUATION_FAILED = 'evaluation_failed';

	/**
	 * Ajax request errors.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const AJAX_ERROR = 'ajax_error';

	/**
	 * Access forbidden errors.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const FORBIDDEN = 'forbidden';

	/**
	 * Resource not found errors.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const NOT_FOUND = 'not_found';

	/**
	 * Creates a validation error.
	 *
	 * @since 1.3.0
	 *
	 * @param string              $message Error message.
	 * @param array<string,mixed> $data    Optional extra data.
	 *
	 * @return self
	 */
	public static function validation( string $message, array $data = [] ): self {
		return new self( self::VALIDATION_FAILED, $message, $data );
	}

	/**
	 * Persistence (database/user meta) failures.
	 *
	 * @since 1.3.0
	 *
	 * @param string              $context Arbitrary context string (e.g. method).
	 * @param array<string,mixed> $data    Additional debug data.
	 *
	 * @return self
	 */
	public static function persistence( string $context, array $data = [] ): self {
		return new self(
			self::PERSISTENCE_FAILED,
			sprintf( 'Database write failed during "%s".', $context ),
			$data + [ 'context' => $context ]
		);
	}

	/**
	 * Runtime evaluation failures.
	 *
	 * @since 1.3.0
	 *
	 * @param string              $message Error message.
	 * @param array<string,mixed> $data    Optional extra data.
	 *
	 * @return self
	 */
	public static function evaluation( string $message, array $data = [] ): self {
		return new self( self::EVALUATION_FAILED, $message, $data );
	}

	/**
	 * Turns any Throwable into a NoticeException (evaluation context).
	 *
	 * @since 1.3.0
	 *
	 * @param Throwable           $throwable Exception or throwable object.
	 * @param array<string,mixed> $context   Optional additional context.
	 *
	 * @return self
	 */
	public static function from_throwable( Throwable $throwable, array $context = [] ): self {
		$context += [
			'exception_class' => get_class( $throwable ),
			'trace'           => $throwable->getTraceAsString(),
		];

		return new self( self::EVALUATION_FAILED, $throwable->getMessage(), $context, $throwable );
	}

	/**
	 * Converts a WP_Error into an exception (evaluation/live context).
	 *
	 * @since 1.3.0
	 *
	 * @param WP_Error            $error   WordPress error object.
	 * @param array<string,mixed> $context Optional additional context.
	 *
	 * @return self
	 */
	public static function from_wp_error( WP_Error $error, array $context = [] ): self {
		$context += [
			'wp_error_code' => $error->get_error_code(),
			'wp_error_data' => $error->get_error_data(),
		];

		return new self( self::EVALUATION_FAILED, $error->get_error_message(), $context );
	}

	/**
	 * Permission/capability guard failure.
	 *
	 * @since 1.3.0
	 *
	 * @param string              $message Error message.
	 * @param array<string,mixed> $data    Optional extra data.
	 *
	 * @return self
	 */
	public static function forbidden( string $message = 'Insufficient permissions.', array $data = [] ): self {
		return new self( self::FORBIDDEN, $message, $data );
	}

	/**
	 * Not-found helper.
	 *
	 * @since 1.3.0
	 *
	 * @param string              $what    What was not found.
	 * @param array<string,mixed> $data    Optional extra data.
	 *
	 * @return self
	 */
	public static function not_found( string $what, array $data = [] ): self {
		return new self(
            self::NOT_FOUND,
            strtr( __( '[object] not found.', 'gk-gravityedit' ), [ '[object]' => $what ] ),
            $data + [ 'target' => $what ]
        );
	}

	/**
	 * Human-readable representation for logs.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function __toString(): string {
		return sprintf( '[%s] %s in %s:%d', $this->get_code_string(), $this->getMessage(), $this->getFile(), $this->getLine() );
	}

	/**
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_error_message() {
		return $this->getMessage();
	}

	/**
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_error_code() {
		return $this->get_code_string();
	}

	/**
	 * @since 1.3.0
	 *
	 * @return array<string,mixed>
	 */
	public function get_error_data() {
		return $this->get_data();
	}
}
