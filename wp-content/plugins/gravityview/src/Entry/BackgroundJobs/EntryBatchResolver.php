<?php
/**
 * GravityView entry batch resolver.
 *
 * @package GravityKit\GravityView\Entry\BackgroundJobs
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BackgroundJobs;

/**
 * Rebuilds a View result set and returns one selected batch.
 *
 * @since 3.0.0
 */
final class EntryBatchResolver {
	public const DEFAULT_BATCH_SIZE = 100;

	/**
	 * Resolves one batch of selected entries.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View         $view       View whose entries should be resolved.
	 * @param EntrySelection   $selection  Entry selection descriptor.
	 * @param int              $cursor     Current cursor.
	 * @param int              $batch_size Batch size.
	 * @param \GV\Request|null $request    Optional request object.
	 *
	 * @return array{
	 *     entries: array,
	 *     entry_ids: int[],
	 *     requested_entry_ids: int[],
	 *     cursor: int,
	 *     next_cursor: int,
	 *     has_more: bool,
	 *     selection_total: int|null,
	 *     query_total: int|null
	 * }
	 */
	public function resolve( \GV\View $view, EntrySelection $selection, int $cursor = 0, int $batch_size = self::DEFAULT_BATCH_SIZE, ?\GV\Request $request = null ): array {
		$cursor     = max( 0, $cursor );
		$batch_size = max( 1, $batch_size );
		$request    = $request ? $request : new \GV\Frontend_Request();

		$batch_ids = [];

		if ( $selection->is_explicit() ) {
			$batch_ids = array_slice( $selection->entry_ids(), $cursor, $batch_size );

			return $this->resolve_explicit( $view, $selection, $batch_ids, $cursor );
		}

		$query_filter = function ( &$query, $query_view ) use ( $view, $selection, $batch_ids, $cursor, $batch_size ) {
			if ( ! $query_view instanceof \GV\View || (int) $query_view->ID !== (int) $view->ID ) {
				return;
			}

			if ( $selection->excluded_ids() ) {
				$this->apply_entry_id_condition( $query, $view, $selection->excluded_ids(), true );
			}

			$query->limit( $batch_size )->offset( $this->view_offset( $view ) + $cursor );
		};

		add_action( 'gravityview/view/query', $query_filter, PHP_INT_MAX, 3 );

		try {
			$collection = $this->without_view_cache(
				function () use ( $selection, $view, $request ) {
					return $this->with_request_args(
						$selection->request_args(),
						static function () use ( $view, $request ) {
							return $view->get_entries( $request );
						}
					);
				}
			);

			$entries     = $collection->all();
			$entry_ids   = $this->pluck_entry_ids( $entries );
			$query_total = $this->query_total( $collection, $view, $selection );

			if ( $selection->is_explicit() ) {
				$next_cursor = $cursor + count( $batch_ids );
				$has_more    = $next_cursor < count( $selection->entry_ids() );
			} else {
				$next_cursor = $cursor + count( $entries );
				$has_more    = count( $entries ) === $batch_size && $next_cursor < (int) $query_total;
			}

			return [
				'entries'             => $entries,
				'entry_ids'           => $entry_ids,
				'requested_entry_ids' => $selection->is_explicit() ? $batch_ids : $entry_ids,
				'cursor'              => $cursor,
				'next_cursor'         => $next_cursor,
				'has_more'            => $has_more,
				'selection_total'     => $selection->known_count(),
				'query_total'         => $query_total,
			];
		} finally {
			remove_action( 'gravityview/view/query', $query_filter, PHP_INT_MAX );
		}
	}

	/**
	 * Resolves selected entry IDs into a stable list.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View         $view       View whose entries should be resolved.
	 * @param EntrySelection   $selection  Entry selection descriptor.
	 * @param int              $batch_size Snapshot query batch size.
	 * @param \GV\Request|null $request    Optional request object.
	 * @param int              $max_ids    Maximum IDs to snapshot. 0 means unbounded.
	 *
	 * @return int[]
	 * @throws \OverflowException When the snapshot exceeds the maximum ID count.
	 */
	public function snapshot_entry_ids( \GV\View $view, EntrySelection $selection, int $batch_size = self::DEFAULT_BATCH_SIZE, ?\GV\Request $request = null, int $max_ids = 0 ): array {
		if ( $selection->is_explicit() ) {
			return $selection->entry_ids();
		}

		$cursor     = 0;
		$batch_size = max( 1, $batch_size );
		$max_ids    = max( 0, $max_ids );
		$entry_ids  = [];

		do {
			$batch = $this->resolve( $view, $selection, $cursor, $batch_size, $request );

			$entry_ids = array_merge( $entry_ids, $batch['entry_ids'] );

			if ( $max_ids && count( $entry_ids ) > $max_ids ) {
				throw new \OverflowException( 'The select-all snapshot exceeded the allowed entry limit.' );
			}

			if ( empty( $batch['has_more'] ) ) {
				break;
			}

			$next_cursor = max( 0, (int) $batch['next_cursor'] );

			if ( $next_cursor <= $cursor ) {
				break;
			}

			$cursor = $next_cursor;
		} while ( true );

		return EntrySelection::normalize_entry_ids( $entry_ids );
	}

	/**
	 * Resolves an explicit ID batch.
	 *
	 * Explicit selections are already bounded by the caller's batch size, so
	 * each entry can be revalidated against the View without exposing the
	 * unbounded per-entry query path that the background adapter is designed to
	 * avoid.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View       $view       View instance.
	 * @param EntrySelection $selection  Entry selection.
	 * @param int[]          $batch_ids  Requested entry IDs for this batch.
	 * @param int            $cursor     Current cursor.
	 * @return array
	 */
	private function resolve_explicit( \GV\View $view, EntrySelection $selection, array $batch_ids, int $cursor ): array {
		if ( [] === $batch_ids ) {
			return [
				'entries'             => [],
				'entry_ids'           => [],
				'requested_entry_ids' => [],
				'cursor'              => $cursor,
				'next_cursor'         => $cursor,
				'has_more'            => false,
				'selection_total'     => $selection->known_count(),
				'query_total'         => $selection->known_count(),
			];
		}

		$entries = $this->without_view_cache(
			function () use ( $selection, $view, $batch_ids ) {
				return $this->with_request_args(
					$selection->request_args(),
					static function () use ( $view, $batch_ids ) {
						$entries = [];

						foreach ( $batch_ids as $entry_id ) {
							$entry = \GFAPI::get_entry( $entry_id );

							if ( is_wp_error( $entry ) ) {
								continue;
							}

							$entry = \GVCommon::check_entry_display( $entry, $view );

							if ( is_wp_error( $entry ) ) {
								continue;
							}

							$entry = \GV\GF_Entry::from_entry( $entry );

							if ( $entry instanceof \GV\Entry ) {
								$entries[] = $entry;
							}
						}

						return $entries;
					}
				);
			}
		);

		$next_cursor = $cursor + count( $batch_ids );

		return [
			'entries'             => $entries,
			'entry_ids'           => $this->pluck_entry_ids( $entries ),
			'requested_entry_ids' => $batch_ids,
			'cursor'              => $cursor,
			'next_cursor'         => $next_cursor,
			'has_more'            => $next_cursor < count( $selection->entry_ids() ),
			'selection_total'     => $selection->known_count(),
			'query_total'         => $selection->known_count(),
		];
	}

	/**
	 * Applies an entry ID condition to a GF_Query.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Query $query   GF_Query instance.
	 * @param \GV\View  $view    View instance.
	 * @param int[]     $ids     Entry IDs.
	 * @param bool      $exclude Whether IDs should be excluded.
	 *
	 * @return void
	 */
	private function apply_entry_id_condition( \GF_Query $query, \GV\View $view, array $ids, bool $exclude = false ): void {
		$ids = EntrySelection::normalize_entry_ids( $ids );

		if ( [] === $ids || ! $view->form ) {
			return;
		}

		/**
		 * Allows integrations to choose which form ID should own entry-ID
		 * constraints for joined or customized Views. Return 0 to use GF_Query's
		 * primary form.
		 *
		 * @since 3.0.0
		 *
		 * @param int      $form_id Form ID used for the entry-ID column.
		 * @param \GV\View $view    View instance.
		 * @param int[]    $ids     Entry IDs.
		 * @param bool     $exclude Whether IDs are excluded.
		 */
		$form_id = (int) apply_filters( 'gk/gravityview/background-jobs/entry-batch/query-form-id', $view->form->ID, $view, $ids, $exclude );

		$entry_id_query = new \GF_Query(
			$form_id,
			[
				'field_filters' => [
					'mode' => 'all',
					[
						'key'      => 'id',
						'operator' => $exclude ? 'NOT IN' : 'IN',
						'value'    => $ids,
					],
				],
			]
		);

		$query_parts    = $query->_introspect();
		$id_query_parts = $entry_id_query->_introspect();
		$where          = $query_parts['where'] ?? null;
		$id_where       = $id_query_parts['where'] ?? null;

		if ( $id_where ) {
			$query->where( $where ? \GF_Query_Condition::_and( $where, $id_where ) : $id_where );
		}
	}

	/**
	 * Executes a callback with captured GET arguments.
	 *
	 * @since 3.0.0
	 *
	 * @param array    $request_args Captured request arguments.
	 * @param callable $callback     Callback to run.
	 *
	 * @return mixed
	 */
	private function with_request_args( array $request_args, callable $callback ) {
		if ( [] === $request_args ) {
			return $callback();
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Request args are captured after the original bulk-action nonce is verified and replayed only for View query filters.
		$previous_get = $_GET;
		$_GET         = $request_args;

		try {
			return $callback();
		} finally {
			$_GET = $previous_get;
		}
	}

	/**
	 * Executes a callback without GravityView entry caches.
	 *
	 * Background batches must resolve the current View result set at execution
	 * time. Disabling the View caches here avoids stale cache hits when a job is
	 * processing recently changed entries.
	 *
	 * @since 3.0.0
	 *
	 * @param callable $callback Callback to run.
	 *
	 * @return mixed
	 */
	private function without_view_cache( callable $callback ) {
		add_filter( 'gravityview_use_cache', '__return_false', PHP_INT_MAX );
		add_filter( 'gk/gravityview/view/entries/cache', '__return_false', PHP_INT_MAX );

		try {
			return $callback();
		} finally {
			remove_filter( 'gravityview_use_cache', '__return_false', PHP_INT_MAX );
			remove_filter( 'gk/gravityview/view/entries/cache', '__return_false', PHP_INT_MAX );
		}
	}

	/**
	 * Returns a View's configured entry offset.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View $view View instance.
	 *
	 * @return int
	 */
	private function view_offset( \GV\View $view ): int {
		return max( 0, (int) $view->settings->get( 'offset' ) );
	}

	/**
	 * Returns the total matching query count.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\Entry_Collection $collection Entry collection.
	 * @param \GV\View             $view       View instance.
	 * @param EntrySelection       $selection  Entry selection descriptor.
	 *
	 * @return int|null
	 */
	private function query_total( \GV\Entry_Collection $collection, \GV\View $view, EntrySelection $selection ): ?int {
		if ( $selection->is_explicit() ) {
			return (int) $collection->total();
		}

		return max( 0, (int) $collection->total() - $this->view_offset( $view ) );
	}

	/**
	 * Extracts primary entry IDs from resolved entries.
	 *
	 * @since 3.0.0
	 *
	 * @param array $entries GV entries.
	 *
	 * @return int[]
	 */
	private function pluck_entry_ids( array $entries ): array {
		$ids = [];

		foreach ( $entries as $entry ) {
			if ( $entry instanceof \GV\Entry && $entry->ID ) {
				$ids[] = (int) $entry->ID;
			}
		}

		return $ids;
	}
}
