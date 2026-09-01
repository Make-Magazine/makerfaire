<?php

namespace GravityKit\GravityView\Foundation\Exceptions;

use JsonSerializable;
use Throwable;
use Exception;

/**
 * Generic base exception providing structured context data and JSON/array serialization.
 *
 * Uses snake_case for methods to match WordPress coding standards.
 * Inherited methods like getMessage() use camelCase per PHP native Exception API.
 *
 * @since 1.3.0
 */
class BaseException extends Exception implements JsonSerializable {
	/**
	 * Original (string) error code supplied by the caller.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	protected $code_string = '';

	/**
	 * Structured context data.
	 *
	 * @since 1.3.0
	 *
	 * @var array<string,mixed>
	 */
	protected $data = [];

	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param string|int          $code      Error code.
	 * @param string              $message   Error message.
	 * @param array<string,mixed> $data      Additional context data.
	 * @param Throwable|null      $previous  Previous throwable for chaining.
	 */
	public function __construct( $code, $message = '', array $data = [], ?Throwable $previous = null ) {
		$this->code_string = (string) $code;
		$this->data        = $data;

		// Map string code to an int so parent::__construct() gets something legal.
		$code_int = abs( crc32( $this->code_string ) );

		parent::__construct( $message, $code_int, $previous );
	}

	/**
	 * Returns the original string error code.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_code_string() {
		return $this->code_string;
	}

	/**
	 * Returns the structured context data.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string,mixed>
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Converts the exception to an associative array.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string,mixed>
	 */
	public function to_array() {
		return [
			'code'    => $this->code_string,
			'message' => $this->getMessage(),
			'data'    => $this->data,
		];
	}

	/**
	 * Implements JsonSerializable.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string,mixed>
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return $this->to_array();
	}

	/**
	 * Generic factory.
	 *
	 * @since 1.3.0
	 *
	 * @param string              $code    Error code.
	 * @param string              $message Error message.
	 * @param array<string,mixed> $data    Extra data.
	 *
	 * @return self
	 */
	public static function create( $code, $message, array $data = [] ) {
		return new self( $code, $message, $data );
	}

	/**
	 * Creates an exception from an arbitrary Throwable.
	 *
	 * @since 1.3.0
	 *
	 * @param Throwable           $throwable Source throwable.
	 * @param array<string,mixed> $context   Additional context.
	 *
	 * @return self
	 */
	public static function from_throwable( Throwable $throwable, array $context = [] ) {
		$context += [
			'exception_class' => get_class( $throwable ),
			'file'            => $throwable->getFile(),
			'line'            => $throwable->getLine(),
		];

		return new self( 'exception_occurred', $throwable->getMessage(), $context, $throwable );
	}
}
