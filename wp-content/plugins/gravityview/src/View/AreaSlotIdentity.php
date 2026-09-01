<?php
/**
 * Identity for field-slot and widget-slot resources.
 *
 * @package     GravityKit\GravityView\View
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View;

use GravityKit\GravityView\Contracts\SlotIdentity;

/**
 * `(view_id, area, slot)` identity used by Fields and Widgets.
 *
 * Immutable value object.
 *
 * @since 3.0.0
 */
final class AreaSlotIdentity implements SlotIdentity {

	/**
	 * The View post id.
	 *
	 * @since 3.0.0
	 *
	 * @var int
	 */
	private int $view_id;

	/**
	 * Area key in the View's layout.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $area;

	/**
	 * Slot UID within the area.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $slot;

	/**
	 * Construct from a parsed request payload. Returns WP_Error on invalid
	 * input rather than throwing — convenience over the strict constructor.
	 *
	 * Accepts `view_id` or `id` for the view id key (REST URL captures use
	 * `id`; programmatic callers may use `view_id`).
	 *
	 * @since 3.0.0
	 *
	 * @param array<string,mixed> $data Parsed payload.
	 *
	 * @return self|\WP_Error
	 */
	public static function from_array( array $data ) {
		try {
			return new self(
				(int) ( $data['view_id'] ?? $data['id'] ?? 0 ),
				(string) ( $data['area'] ?? '' ),
				(string) ( $data['slot'] ?? '' )
			);
		} catch ( \InvalidArgumentException $e ) {
			return new \WP_Error(
				'gv_rest_invalid_input',
				$e->getMessage(),
				[ 'status' => 400 ]
			);
		}
	}

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id View post id.
	 * @param string $area Area key.
	 * @param string $slot Slot UID.
	 *
	 * @throws \InvalidArgumentException When any argument is invalid (empty string, view_id <= 0, etc.).
	 */
	public function __construct( int $view_id, string $area, string $slot ) {
		if ( $view_id <= 0 ) {
			throw new \InvalidArgumentException( 'AreaSlotIdentity requires view_id > 0.' );
		}
		if ( '' === $area ) {
			throw new \InvalidArgumentException( 'AreaSlotIdentity requires non-empty area.' );
		}
		if ( '' === $slot ) {
			throw new \InvalidArgumentException( 'AreaSlotIdentity requires non-empty slot.' );
		}
		$this->view_id = $view_id;
		$this->area    = $area;
		$this->slot    = $slot;
	}

	/**
	 * Get the view_id.
	 *
	 * @since 3.0.0
	 */
	public function view_id(): int {
		return $this->view_id;
	}

	/**
	 * Get the area key.
	 *
	 * @since 3.0.0
	 */
	public function area(): string {
		return $this->area;
	}

	/**
	 * Get the slot UID.
	 *
	 * @since 3.0.0
	 */
	public function slot(): string {
		return $this->slot;
	}

	/**
	 * Serialize this value object to a plain array.
	 *
	 * @since 3.0.0
	 */
	public function to_array(): array {
		return [
			'view_id' => $this->view_id,
			'area'    => $this->area,
			'slot'    => $this->slot,
		];
	}
}
