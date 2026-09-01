<?php
/**
 * Identity for search-field resources (5-piece, nested under a search_bar widget).
 *
 * @package     GravityKit\GravityView\View
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View;

use GravityKit\GravityView\Contracts\SlotIdentity;

/**
 * `(view_id, widget_area, widget_slot, position, search_slot)` identity.
 *
 * Search fields live INSIDE a specific `search_bar` widget identified by
 * `(widget_area, widget_slot)`, at `search_fields_section[position][search_slot]`.
 *
 * @since 3.0.0
 */
final class SearchFieldIdentity implements SlotIdentity {

	/**
	 * The View post id.
	 *
	 * @since 3.0.0
	 *
	 * @var int
	 */
	private int $view_id;

	/**
	 * Widget area containing the search_bar.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $widget_area;

	/**
	 * Slot UID of the search_bar widget.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $widget_slot;

	/**
	 * Position bucket key (search-bar specific) or numeric/symbolic insert anchor.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $position;

	/**
	 * Search-field slot UID.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private string $search_slot;

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
				(string) ( $data['widget_area'] ?? '' ),
				(string) ( $data['widget_slot'] ?? '' ),
				(string) ( $data['position'] ?? '' ),
				(string) ( $data['search_slot'] ?? '' )
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
	 * @param string $widget_area Widget area key.
	 * @param string $widget_slot Widget slot UID.
	 * @param string $position Position anchor.
	 * @param string $search_slot Search-field slot UID.
	 *
	 * @throws \InvalidArgumentException When any argument is invalid (empty string, view_id <= 0, etc.).
	 */
	public function __construct(
		int $view_id,
		string $widget_area,
		string $widget_slot,
		string $position,
		string $search_slot
	) {
		if ( $view_id <= 0 ) {
			throw new \InvalidArgumentException( 'SearchFieldIdentity requires view_id > 0.' );
		}
		foreach ( [
			'widget_area' => $widget_area,
			'widget_slot' => $widget_slot,
			'position'    => $position,
			'search_slot' => $search_slot,
		] as $key => $value ) {
			if ( '' === $value ) {
				throw new \InvalidArgumentException( sprintf( 'SearchFieldIdentity requires non-empty %s.', esc_html( (string) $key ) ) );
			}
		}
		$this->view_id     = $view_id;
		$this->widget_area = $widget_area;
		$this->widget_slot = $widget_slot;
		$this->position    = $position;
		$this->search_slot = $search_slot;
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
	 * Get the widget area key.
	 *
	 * @since 3.0.0
	 */
	public function widget_area(): string {
		return $this->widget_area;
	}

	/**
	 * Get the widget slot UID.
	 *
	 * @since 3.0.0
	 */
	public function widget_slot(): string {
		return $this->widget_slot;
	}

	/**
	 * Get the position.
	 *
	 * @since 3.0.0
	 */
	public function position(): string {
		return $this->position;
	}

	/**
	 * Get the search-slot UID.
	 *
	 * @since 3.0.0
	 */
	public function search_slot(): string {
		return $this->search_slot;
	}

	/**
	 * Serialize this value object to a plain array.
	 *
	 * @since 3.0.0
	 */
	public function to_array(): array {
		return [
			'view_id'     => $this->view_id,
			'widget_area' => $this->widget_area,
			'widget_slot' => $this->widget_slot,
			'position'    => $this->position,
			'search_slot' => $this->search_slot,
		];
	}
}
