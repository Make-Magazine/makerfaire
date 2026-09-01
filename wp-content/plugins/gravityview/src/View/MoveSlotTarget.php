<?php
/**
 * Anchor-aware target for slot/row move operations.
 *
 * @package     GravityKit\GravityView\View
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View;

use WP_Error;

/**
 * Value object capturing the destination of a slot move.
 *
 * Mirrors the legacy `move_field()` anchor semantics: a target area + exactly
 * ONE of:
 *  - `before_slot` (string slot UID) — insert immediately before this slot.
 *  - `after_slot` (string slot UID) — insert immediately after this slot.
 *  - `position` (int >= 0) — insert at this 0-based index in the target area.
 *  - symbolic `start` / `end` (special positions).
 *
 * Constructor enforces mutual exclusion. Use {@see from_array()} to construct
 * from a parsed request payload — returns WP_Error on invalid combination.
 *
 * @since 3.0.0
 */
final class MoveSlotTarget {

	/**
	 * Symbolic position: insert at the beginning of the target area.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public const POSITION_START = 'start';

	/**
	 * Symbolic position: insert at the end of the target area.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public const POSITION_END = 'end';

	/**
	 * Destination area key for a move operation.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $to_area;

	/**
	 * Slot UID to insert before, or null.
	 *
	 * @since 3.0.0
	 *
	 * @var string|null
	 */
	private ?string $before_slot;

	/**
	 * Slot UID to insert after, or null.
	 *
	 * @since 3.0.0
	 *
	 * @var string|null
	 */
	private ?string $after_slot;

	/**
	 * Numeric position (0+) or symbolic 'start'/'end', or null.
	 *
	 * @since 3.0.0
	 *
	 * @var int|string|null
	 */
	private $position;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param string          $to_area     Destination area key (must be valid for the resource).
	 * @param string|null     $before_slot Slot UID to insert before, or null.
	 * @param string|null     $after_slot  Slot UID to insert after, or null.
	 * @param int|string|null $position    Numeric index or symbolic 'start'/'end', or null.
	 */
	public function __construct(
		string $to_area,
		?string $before_slot = null,
		?string $after_slot = null,
		$position = null
	) {
		$this->to_area     = $to_area;
		$this->before_slot = $before_slot;
		$this->after_slot  = $after_slot;
		$this->position    = $position;
	}

	/**
	 * Construct from a parsed payload, validating mutual exclusion.
	 *
	 * @since 3.0.0
	 *
	 * @param array<string,mixed> $data Parsed payload array.
	 *
	 * @return self|WP_Error
	 */
	public static function from_array( array $data ) {
		$to_area = isset( $data['to_area'] ) ? (string) $data['to_area'] : '';
		if ( '' === $to_area ) {
			return new WP_Error(
				'gv_rest_invalid_input',
				__( 'The destination area is missing.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		$before = isset( $data['before_slot'] ) ? (string) $data['before_slot'] : null;
		$after  = isset( $data['after_slot'] ) ? (string) $data['after_slot'] : null;
		$pos    = $data['position'] ?? null;

		$specified = array_filter(
			[
				'before_slot' => $before,
				'after_slot'  => $after,
				'position'    => $pos,
			],
			static fn( $v ): bool => null !== $v && '' !== $v
		);

			if ( count( $specified ) > 1 ) {
				return new WP_Error(
                    'gv_rest_invalid_input',
                    __( 'Choose only one of: before_slot, after_slot, or position — not multiple.', 'gk-gravityview' ),
                    [ 'status' => 400 ]
				);
			}

			if ( null !== $pos && ! in_array( $pos, [ self::POSITION_START, self::POSITION_END ], true ) ) {
				// Accept only int OR a digit-only string (matches non-negative
				// integer). is_numeric would silently accept floats, e-notation,
				// negative numbers, and hex — all wrong shapes for an index.
				$is_int       = is_int( $pos ) && $pos >= 0;
				$is_digit_str = is_string( $pos ) && ctype_digit( $pos );
				if ( ! $is_int && ! $is_digit_str ) {
					return new WP_Error(
                        'gv_rest_invalid_input',
                        __( 'Position must be a whole number (0 or higher), or \'start\' / \'end\'.', 'gk-gravityview' ),
                        [ 'status' => 400 ]
					);
				}
				$pos = (int) $pos;
			}

			return new self( $to_area, $before, $after, $pos );
	}

	/**
	 * Get the destination area key.
	 *
	 * @since 3.0.0
	 */
	public function to_area(): string {
		return $this->to_area;
	}

	/**
	 * Get the optional before_slot anchor.
	 *
	 * @since 3.0.0
	 */
	public function before_slot(): ?string {
		return $this->before_slot;
	}

	/**
	 * Get the optional after_slot anchor.
	 *
	 * @since 3.0.0
	 */
	public function after_slot(): ?string {
		return $this->after_slot;
	}

	/**
	 * Get the position anchor.
	 *
	 * @since 3.0.0
	 *
	 * @return int|string|null
	 */
	public function position() {
		return $this->position;
	}

	/**
	 * Did the caller specify any anchor? When false, the resource's default
	 * (typically "append to end") applies.
	 *
	 * @since 3.0.0
	 */
	public function has_anchor(): bool {
		return null !== $this->before_slot
			|| null !== $this->after_slot
			|| null !== $this->position;
	}
}
