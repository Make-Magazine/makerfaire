<?php
/**
 * Contract for slot repositories that support atomic batch operations.
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

namespace GravityKit\GravityView\View\Contracts;

defined( 'ABSPATH' ) || exit;

/**
 * Implemented by FieldSlotRepository, WidgetSlotRepository,
 * SearchFieldSlotRepository, and GridRowRepository. Surfaces the
 * `batch_apply` method so {@see BatchHelpers::handle_repository_batch}
 * can type-check against the contract instead of `object`.
 *
 * @since 3.0.0
 */
interface BatchableSlotRepository {

	/**
	 * Apply a batch of slot operations atomically.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id       View post id.
	 * @param array  $items         Per-item ops in canonical batch shape.
	 * @param string $version_token Optimistic-concurrency token.
	 * @param bool   $dry_run       When true, validate + plan without persisting.
	 *
	 * @return array|\WP_Error Rows of `[ok, result|error]` shape, or WP_Error on top-level failure.
	 */
	public function batch_apply( int $view_id, array $items, string $version_token, bool $dry_run );
}
