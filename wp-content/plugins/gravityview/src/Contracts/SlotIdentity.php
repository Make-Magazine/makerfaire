<?php
/**
 * Marker interface for slot-identity value objects.
 *
 * @package     GravityKit\GravityView\Contracts
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\Contracts;

/**
 * Concrete impls:
 *  - `AreaSlotIdentity (view_id, area, slot)` — fields, widgets.
 *  - `SearchFieldIdentity (view_id, widget_area, widget_slot, position, search_slot)`
 *    — search fields, nested under a `search_bar` widget.
 *  - `RowSlotIdentity (view_id, surface, zone, row_uid)` — grid rows.
 *
 * Each impl knows its own identity shape; the `SlotRepository` family
 * type-hints `SlotIdentity` to handle any shape uniformly.
 *
 * @since 3.0.0
 */
interface SlotIdentity {

	/**
	 * The View id this identity is scoped to. Every slot lives inside one View.
	 *
	 * @since 3.0.0
	 */
	public function view_id(): int;
}
