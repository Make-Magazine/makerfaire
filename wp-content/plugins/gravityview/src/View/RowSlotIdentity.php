<?php
/**
 * Identity for grid-row resources within a View's Layout Builder grid.
 *
 * @package     GravityKit\GravityView\View
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View;

use GravityKit\GravityView\Contracts\SlotIdentity;

/**
 * `(view_id, surface, zone, row_uid)` identity.
 *
 * On the `fields` surface, a single `row_uid` is materialized in multiple
 * zones (`directory` and `single`); each zone tracks its own area-key
 * sequence. The zone is part of the identity to disambiguate operations
 * across materializations.
 *
 * @since 3.0.0
 */
final class RowSlotIdentity implements SlotIdentity {

	/**
	 * The View post id.
	 *
	 * @since 3.0.0
	 *
	 * @var int
	 */
	private int $view_id;

	/**
	 * Surface key (fields | widgets).
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $surface;

	/**
	 * Zone key within the surface.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $zone;

	/**
	 * Row UID within the zone.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $row_uid;

	/**
	 * Construct from a parsed request payload. Returns WP_Error on
	 * invalid input rather than throwing.
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
				(string) ( $data['surface'] ?? '' ),
				(string) ( $data['zone'] ?? '' ),
				(string) ( $data['row_uid'] ?? '' )
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
	 * @param string $surface Surface (fields | widgets).
	 * @param string $zone Zone key.
	 * @param string $row_uid Row UID.
	 *
	 * @throws \InvalidArgumentException When any argument is invalid (empty string, view_id <= 0, etc.).
	 */
	public function __construct( int $view_id, string $surface, string $zone, string $row_uid ) {
		if ( $view_id <= 0 ) {
			throw new \InvalidArgumentException( 'RowSlotIdentity requires view_id > 0.' );
		}
		if ( ! in_array( $surface, [ 'fields', 'widgets' ], true ) ) {
			throw new \InvalidArgumentException( sprintf( 'RowSlotIdentity surface must be fields or widgets, got: %s', esc_html( $surface ) ) );
		}
		if ( '' === $zone ) {
			throw new \InvalidArgumentException( 'RowSlotIdentity requires non-empty zone.' );
		}
		if ( '' === $row_uid ) {
			throw new \InvalidArgumentException( 'RowSlotIdentity requires non-empty row_uid.' );
		}
		$this->view_id = $view_id;
		$this->surface = $surface;
		$this->zone    = $zone;
		$this->row_uid = $row_uid;
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
	 * Get the surface (fields | widgets).
	 *
	 * @since 3.0.0
	 */
	public function surface(): string {
		return $this->surface;
	}

	/**
	 * Get the zone key.
	 *
	 * @since 3.0.0
	 */
	public function zone(): string {
		return $this->zone;
	}

	/**
	 * Get the row UID.
	 *
	 * @since 3.0.0
	 */
	public function row_uid(): string {
		return $this->row_uid;
	}

	/**
	 * Serialize this value object to a plain array.
	 *
	 * @since 3.0.0
	 */
	public function to_array(): array {
		return [
			'view_id' => $this->view_id,
			'surface' => $this->surface,
			'zone'    => $this->zone,
			'row_uid' => $this->row_uid,
		];
	}
}
