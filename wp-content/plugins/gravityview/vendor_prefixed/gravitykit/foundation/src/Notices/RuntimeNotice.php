<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 17-November-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Notices;

use InvalidArgumentException;

/**
 * Runtime notice – evaluated on every request using a callback condition.
 *
 * @since 1.3.0
 */
class RuntimeNotice extends Notice implements RuntimeNoticeInterface {
	/**
	 * Default condition callback (always returns true).
	 *
	 * @since 1.3.0
	 *
	 * @var callable|null
	 */
	private const DEFAULT_CONDITION = '__return_true';

	/**
	 * Callable condition that decides if the notice is active.
	 *
	 * @since 1.3.0
	 *
	 * @var callable|string
	 */
	protected $condition_cb;

	/**
	 * Class constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed>  $data         Validated definition.
	 * @param callable|string|null $condition_cb Callback returning bool, callable string, or null for default (__return_true).
	 *
	 * @throws InvalidArgumentException When the provided condition is not callable.
	 */
	protected function __construct( array $data, $condition_cb = null ) {
		parent::__construct( $data );

		$this->condition_cb = $condition_cb ?? self::DEFAULT_CONDITION;

		if ( ! is_callable( $this->condition_cb ) ) {
			throw new InvalidArgumentException( 'The "condition" callback must be callable.' );
		}
	}

	/**
	 * Static factory method to create new RuntimeNotice instances.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed>  $data         Validated definition.
	 * @param callable|string|null $condition_cb Callback returning bool, callable string, or null for default (__return_true).
	 *
	 * @return static New runtime notice instance.
	 *
	 * @throws InvalidArgumentException When the provided condition is not callable.
	 */
	public static function create( array $data, $condition_cb = null ): self {
		/** @phpstan-ignore-next-line new static() is safe here for factory method */
		return new static( $data, $condition_cb );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function show_if() {
		/** @var callable $cb */
		$cb = $this->condition_cb;

		return (bool) $cb( $this );
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_condition() {
		return $this->condition_cb;
	}
}
