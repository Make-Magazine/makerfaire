<?php
/**
 * GravityView entry batch task.
 *
 * @package GravityKit\GravityView\Entry\BackgroundJobs
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BackgroundJobs;

use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\BackgroundJobs\ResultNotice;
use GravityKit\GravityView\Entry\BulkActions\LifecycleEvents;
use GravityKit\GravityView\Entry\BulkActions\ViewActionLock;

/**
 * Foundation task callback for processing one View entry batch.
 *
 * @since 3.0.0
 */
final class EntryBatchTask {
	/**
	 * Runs one entry batch.
	 *
	 * @since 3.0.0
	 *
	 * @param array      $args     Task arguments.
	 * @param array|null $job_data Shared Foundation job data.
	 *
	 * @return object|null Foundation NextRunRules or null.
	 * @throws \RuntimeException When batch processing fails.
	 * @throws \Throwable        When batch processing fails.
	 */
	public static function run( array $args = [], $job_data = null ) {
		$job_data = is_array( $job_data ) ? $job_data : [];
		$cursor   = max( 0, (int) ( $args['cursor'] ?? 0 ) );

		$previous_user_id = get_current_user_id();
		$blog_id          = max( 0, (int) ( $job_data['blog_id'] ?? 0 ) );
		$actor_user_id    = (int) ( $job_data['actor_user_id'] ?? 0 );
		$failure_stored   = false;
		$switched_blog    = false;

		if ( is_multisite() && $blog_id > 0 && get_current_blog_id() !== $blog_id ) {
			switch_to_blog( $blog_id );
			$switched_blog = true;
		}

		try {
			if ( $actor_user_id > 0 ) {
				wp_set_current_user( $actor_user_id );
			}

			$view      = self::get_view( (int) ( $job_data['view_id'] ?? 0 ) );
			$selection = EntrySelection::from_array( (array) ( $job_data['selection'] ?? [] ) );

			self::assert_view_visible( $view );

			$batch_size = max( 1, (int) ( $job_data['batch_size'] ?? EntryBatchResolver::DEFAULT_BATCH_SIZE ) );
			$resolver   = new EntryBatchResolver();
			$batch      = $resolver->resolve( $view, $selection, $cursor, $batch_size );

			$counts               = [
				'processed' => 0,
				'failed'    => $selection->is_explicit() ? max( 0, count( $batch['requested_entry_ids'] ?? [] ) - count( $batch['entries'] ) ) : 0,
			];
			$checkpoint_requested = false;
			$next_cursor          = $batch['next_cursor'];
			$fatal_error          = '';

			if ( $batch['entries'] || self::should_run_empty_final_callback( $job_data, $batch ) ) {
				$callback_counts      = self::run_callback( $job_data, $batch, $view, $selection, $args );
				$counts['processed'] += $callback_counts['processed'];
				$counts['failed']    += $callback_counts['failed'];
				$fatal_error          = empty( $callback_counts['fatal_error'] ) ? '' : (string) ( $callback_counts['last_error'] ?? '' );
				$checkpoint_requested = '' === $fatal_error && ! empty( $callback_counts['checkpoint'] );

				if ( ! empty( $callback_counts['result'] ) ) {
					$counts['result'] = $callback_counts['result'];
				}

				if ( ! empty( $callback_counts['last_error'] ) ) {
					$counts['last_error'] = $callback_counts['last_error'];
				}

				if ( $checkpoint_requested ) {
					$next_cursor = self::get_checkpoint_cursor( $callback_counts, $batch );
				}
			}

			$has_more      = $checkpoint_requested || $batch['has_more'];
			$next_job_data = self::merge_counts( $job_data, $counts, $batch, $next_cursor );

			if ( '' !== $fatal_error ) {
				$next_job_data['last_error'] = $fatal_error;

				self::store_result( $next_job_data, 'failed' );
				$failure_stored = true;

				$job_data = $next_job_data;

				throw new \RuntimeException( $fatal_error );
			}

			self::store_result( $next_job_data, $has_more ? 'running' : 'complete' );

			if ( $has_more ) {
				return \GravityKitFoundation::scheduler()->checkpoint_with_data(
					[ 'cursor' => $next_cursor ],
					$next_job_data
				);
			}

			$rules = \GravityKitFoundation::scheduler()->checkpoint_with_data( [], $next_job_data );
			$rules->rerun( false );

			return $rules;
		} catch ( \Throwable $e ) {
			$job_data['last_error'] = $e->getMessage();

			if ( ! $failure_stored ) {
				self::store_result( $job_data, 'failed' );
			}

			throw $e;
		} finally {
			if ( $switched_blog ) {
				restore_current_blog();
			}

			wp_set_current_user( $previous_user_id );
		}
	}

	/**
	 * Returns whether an empty final explicit batch still needs action finalization.
	 *
	 * @since 3.0.0
	 *
	 * @param array $job_data Shared job data.
	 * @param array $batch    Resolved batch data.
	 *
	 * @return bool
	 */
	private static function should_run_empty_final_callback( array $job_data, array $batch ): bool {
		if ( ! empty( $batch['entries'] ) || ! empty( $batch['has_more'] ) || empty( $batch['requested_entry_ids'] ) ) {
			return false;
		}

		$callback = $job_data['callback'] ?? null;

		return is_array( $callback )
			&& 2 === count( $callback )
			&& \GravityKit\GravityView\Entry\BulkActions\BackgroundProcessor::class === $callback[0]
			&& 'process' === $callback[1];
	}

	/**
	 * Loads a View by ID.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View ID.
	 *
	 * @return \GV\View
	 * @throws \RuntimeException When the View cannot be found.
	 */
	private static function get_view( int $view_id ): \GV\View {
		$post = $view_id > 0 ? get_post( $view_id ) : null;
		$view = $post ? \GV\View::from_post( $post ) : null;

		if ( ! $view instanceof \GV\View ) {
			throw new \RuntimeException( esc_html__( 'The View could not be found.', 'gk-gravityview' ) );
		}

		return $view;
	}

	/**
	 * Confirms the acting user can still access the View.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View $view View instance.
	 *
	 * @return void
	 * @throws \RuntimeException When the actor cannot access the View.
	 */
	private static function assert_view_visible( \GV\View $view ): void {
		$visible = $view->can_render( [ 'shortcode' ], new \GV\Frontend_Request() );

		if ( is_wp_error( $visible ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Persisted as plain text and escaped when rendered.
			throw new \RuntimeException( wp_strip_all_tags( (string) $visible->get_error_message() ) );
		}
	}

	/**
	 * Runs the integration callback for a resolved batch.
	 *
	 * @since 3.0.0
	 *
	 * @param array          $job_data  Shared job data.
	 * @param array          $batch     Resolved batch data.
	 * @param \GV\View       $view      View instance.
	 * @param EntrySelection $selection Entry selection.
	 * @param array          $task_args Task arguments.
	 *
	 * @return array{processed:int, failed:int}
	 * @throws \RuntimeException When the callback is not callable.
	 */
	private static function run_callback( array $job_data, array $batch, \GV\View $view, EntrySelection $selection, array $task_args ): array {
		$callback = $job_data['callback'] ?? null;

		if ( ! self::is_supported_callback( $callback ) ) {
			throw new \RuntimeException( esc_html__( 'The background job callback is not callable.', 'gk-gravityview' ) );
		}

		$result = call_user_func(
			$callback,
			$batch['entries'],
			[
				'view'                => $view,
				'selection'           => $selection,
				'entry_ids'           => $batch['entry_ids'],
				'requested_entry_ids' => $batch['requested_entry_ids'] ?? $batch['entry_ids'],
				'cursor'              => $batch['cursor'],
				'next_cursor'         => $batch['next_cursor'],
				'batch_size'          => (int) ( $job_data['batch_size'] ?? EntryBatchResolver::DEFAULT_BATCH_SIZE ),
				'selection_total'     => $batch['selection_total'],
				'query_total'         => $batch['query_total'],
				'has_more'            => $batch['has_more'],
				'job_data'            => $job_data,
				'task_args'           => $task_args,
				'callback_args'       => (array) ( $job_data['callback_args'] ?? [] ),
			]
		);

		if ( self::is_fatal_callback_error( $result ) ) {
			return self::normalize_fatal_callback_error( $result, count( $batch['entries'] ) );
		}

		return self::normalize_callback_result( $result, count( $batch['entries'] ) );
	}

	/**
	 * Normalizes a fatal callback error while preserving any known batch counts.
	 *
	 * @since 3.0.0
	 *
	 * @param \WP_Error $result      Callback error.
	 * @param int       $entry_count Number of entries passed to the callback.
	 *
	 * @return array
	 */
	private static function normalize_fatal_callback_error( \WP_Error $result, int $entry_count ): array {
		$error_data = $result->get_error_data();

		if ( is_array( $error_data ) && array_key_exists( 'batch_result', $error_data ) ) {
			$normalized = self::normalize_callback_result( $error_data['batch_result'], $entry_count );
		} else {
			$normalized = [
				'processed' => 0,
				'failed'    => 0,
			];
		}

		$normalized['fatal_error'] = true;
		$last_error                = $result->get_error_message();

		$normalized['last_error'] = $last_error ? $last_error : esc_html__( 'The background action could not be processed.', 'gk-gravityview' );

		return $normalized;
	}

	/**
	 * Returns whether a callback can be persisted through Foundation.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $callback Callback.
	 *
	 * @return bool
	 */
	public static function is_supported_callback( $callback ): bool {
		return is_array( $callback )
			&& 2 === count( $callback )
			&& is_string( $callback[0] )
			&& is_string( $callback[1] )
			&& is_callable( $callback );
	}

	/**
	 * Normalizes callback return values into counts.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $result      Callback result.
	 * @param int   $entry_count Number of entries passed to the callback.
	 *
	 * @return array
	 */
	private static function normalize_callback_result( $result, int $entry_count ): array {
		if ( is_wp_error( $result ) ) {
			$error_data = $result->get_error_data();

			if ( is_array( $error_data ) && array_key_exists( 'batch_result', $error_data ) && ! is_wp_error( $error_data['batch_result'] ) ) {
				$normalized = self::normalize_callback_result( $error_data['batch_result'], $entry_count );
				unset( $normalized['checkpoint'], $normalized['next_cursor'] );
				$normalized['last_error'] = $result->get_error_message();

				return $normalized;
			}

			return [
				'processed'  => 0,
				'failed'     => $entry_count,
				'last_error' => $result->get_error_message(),
			];
		}

		if ( is_array( $result ) ) {
			$normalized = [
				'processed' => max( 0, (int) ( $result['processed'] ?? $entry_count ) ),
				'failed'    => max( 0, (int) ( $result['failed'] ?? 0 ) ),
			];

			if ( ! empty( $result['checkpoint'] ) ) {
				$normalized['checkpoint'] = true;
			}

			if ( isset( $result['next_cursor'] ) ) {
				$normalized['next_cursor'] = max( 0, (int) $result['next_cursor'] );
			}

			unset( $result['processed'], $result['failed'], $result['checkpoint'], $result['next_cursor'] );

			if ( [] !== $result ) {
				$normalized['result'] = self::normalize_result_data( $result );
			}

			return $normalized;
		}

		if ( is_int( $result ) ) {
			return [
				'processed' => max( 0, $result ),
				'failed'    => 0,
			];
		}

		if ( false === $result ) {
			return [
				'processed' => 0,
				'failed'    => $entry_count,
			];
		}

		return [
			'processed' => $entry_count,
			'failed'    => 0,
		];
	}

	/**
	 * Returns whether a callback error should fail the job instead of the batch.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $result Callback result.
	 *
	 * @return bool
	 */
	private static function is_fatal_callback_error( $result ): bool {
		return is_wp_error( $result ) && in_array(
			$result->get_error_code(),
			[
				'gravityview_bulk_background_action_unavailable',
				'gravityview_bulk_background_callback_error',
				'gravityview_bulk_background_checkpoint_no_progress',
				'gravityview_bulk_background_checkpoint_skips_entries',
				'gravityview_bulk_background_complete_callback_error',
				'gravityview_bulk_background_complete_callback_invalid',
				'gravityview_bulk_background_invalid_context',
			],
			true
		);
	}

	/**
	 * Returns the cursor that should be used after a cooperative checkpoint.
	 *
	 * @since 3.0.0
	 *
	 * @param array $callback_counts Normalized callback counts.
	 * @param array $batch           Current batch data.
	 *
	 * @return int
	 * @throws \RuntimeException When the callback requests a checkpoint without advancing.
	 */
	private static function get_checkpoint_cursor( array $callback_counts, array $batch ): int {
		$cursor      = max( 0, (int) ( $batch['cursor'] ?? 0 ) );
		$batch_end   = max( $cursor, (int) ( $batch['next_cursor'] ?? $cursor ) );
		$next_cursor = isset( $callback_counts['next_cursor'] )
			? max( 0, (int) $callback_counts['next_cursor'] )
			: $cursor + max( 0, (int) $callback_counts['processed'] ) + max( 0, (int) $callback_counts['failed'] );

		if ( $next_cursor <= $cursor ) {
			throw new \RuntimeException( esc_html__( 'The background action requested a checkpoint without processing any entries.', 'gk-gravityview' ) );
		}

		if ( $next_cursor > $batch_end ) {
			throw new \RuntimeException( esc_html__( 'The background action checkpoint is outside the current batch.', 'gk-gravityview' ) );
		}

		$requested = count( (array) ( $batch['requested_entry_ids'] ?? [] ) );
		$resolved  = count( (array) ( $batch['entries'] ?? [] ) );
		$missing   = max( 0, $requested - $resolved );
		$handled   = max( 0, (int) $callback_counts['processed'] )
			+ max( 0, (int) $callback_counts['failed'] )
			+ $missing;

		if ( $next_cursor > $cursor + $handled ) {
			throw new \RuntimeException( esc_html__( 'The background action checkpoint skipped unprocessed entries.', 'gk-gravityview' ) );
		}

		return $next_cursor;
	}

	/**
	 * Merges batch counts into job data.
	 *
	 * @since 3.0.0
	 *
	 * @param array    $job_data    Existing job data.
	 * @param array    $counts      Current batch counts and notice data.
	 * @param array    $batch       Batch data.
	 * @param int|null $next_cursor Cursor to store, or null to use the batch end.
	 *
	 * @return array
	 */
	private static function merge_counts( array $job_data, array $counts, array $batch, ?int $next_cursor = null ): array {
		$job_data['processed']   = max( 0, (int) ( $job_data['processed'] ?? 0 ) ) + $counts['processed'];
		$job_data['failed']      = max( 0, (int) ( $job_data['failed'] ?? 0 ) ) + $counts['failed'];
		$job_data['cursor']      = null === $next_cursor ? $batch['next_cursor'] : max( 0, $next_cursor );
		$job_data['query_total'] = $batch['query_total'];
		$job_data['updated_at']  = time();

		if ( ! empty( $counts['result'] ) ) {
			$job_data['result'] = array_replace(
				isset( $job_data['result'] ) && is_array( $job_data['result'] ) ? $job_data['result'] : [],
				self::normalize_result_data( $counts['result'] )
			);
		}

		if ( ! empty( $counts['last_error'] ) ) {
			$job_data['last_error'] = (string) $counts['last_error'];
		}

		return $job_data;
	}

	/**
	 * Normalizes callback result metadata for persistence in Foundation job data.
	 *
	 * @since 3.0.0
	 *
	 * @param array $result Result metadata.
	 *
	 * @return array
	 */
	private static function normalize_result_data( array $result ): array {
		if ( isset( $result['notice'] ) ) {
			$result['notice'] = ResultNotice::normalize( $result['notice'] );
		} elseif ( isset( $result['message'] ) ) {
			$result['notice'] = ResultNotice::normalize( $result );
			unset( $result['message'] );
		}

		return $result;
	}

	/**
	 * Stores a lightweight result snapshot.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $job_data Job data.
	 * @param string $status   Result status.
	 *
	 * @return void
	 */
	private static function store_result( array $job_data, string $status ): void {
		if ( empty( $job_data['result_token'] ) ) {
			return;
		}

		$ttl           = max( 1, (int) ( $job_data['result_ttl'] ?? ResultStore::DEFAULT_TTL ) );
		$result_store  = new ResultStore();
		$view_lock     = new ViewActionLock( $result_store );
		$token         = (string) $job_data['result_token'];
		$view_id       = (int) ( $job_data['view_id'] ?? 0 );
		$job_id        = (int) ( $job_data['job_id'] ?? 0 );
		$callback_args = (array) ( $job_data['callback_args'] ?? [] );
		$status        = sanitize_key( $status );

		$next = [
			'status'          => $status,
			'job_id'          => $job_id,
			'view_id'         => $view_id,
			'blog_id'         => (int) ( $job_data['blog_id'] ?? get_current_blog_id() ),
			'actor_user_id'   => max( 0, (int) ( $job_data['actor_user_id'] ?? 0 ) ),
			'render_instance' => sanitize_key( (string) ( $job_data['render_instance'] ?? '' ) ),
			'action_key'      => isset( $callback_args['action_key'] ) ? sanitize_key( $callback_args['action_key'] ) : '',
			'action_label'    => isset( $callback_args['action_label'] ) ? sanitize_text_field( $callback_args['action_label'] ) : '',
			'action_settings' => isset( $callback_args['action_settings'] ) && is_array( $callback_args['action_settings'] ) ? $callback_args['action_settings'] : [],
			'queued_message'  => isset( $callback_args['queued_message'] ) ? sanitize_text_field( $callback_args['queued_message'] ) : '',
			'message'         => isset( $job_data['message'] ) ? ResultNotice::normalize_message( $job_data['message'] ) : '',
			'result'          => isset( $job_data['result'] ) && is_array( $job_data['result'] ) ? self::normalize_result_data( $job_data['result'] ) : [],
			'processed'       => max( 0, (int) ( $job_data['processed'] ?? 0 ) ),
			'failed'          => max( 0, (int) ( $job_data['failed'] ?? 0 ) ),
			'cursor'          => max( 0, (int) ( $job_data['cursor'] ?? 0 ) ),
			'created_at'      => max( 0, (int) ( $job_data['created_at'] ?? time() ) ),
			'updated_at'      => max( 0, (int) ( $job_data['updated_at'] ?? time() ) ),
			'query_total'     => isset( $job_data['query_total'] ) ? max( 0, (int) $job_data['query_total'] ) : null,
			'last_error'      => isset( $job_data['last_error'] ) ? (string) $job_data['last_error'] : '',
		];

		$previous_status = '';
		$updated         = $result_store->update(
			$token,
			static function ( array $latest ) use ( $next, &$previous_status ) {
				$previous_status = sanitize_key( (string) ( $latest['status'] ?? '' ) );

				if ( ! empty( $latest['consumed'] ) || in_array( (string) ( $latest['status'] ?? '' ), [ 'canceled', 'failed', 'complete' ], true ) ) {
					return null;
				}

				if ( empty( $next['job_id'] ) && ! empty( $latest['job_id'] ) ) {
					$next['job_id'] = (int) $latest['job_id'];
				}

				return $next;
			},
			$ttl,
			[
				'view_id'    => (int) ( $job_data['view_id'] ?? 0 ),
				'job_id'     => $job_id,
				'status'     => $status,
				'action_key' => isset( $callback_args['action_key'] ) ? sanitize_key( $callback_args['action_key'] ) : '',
			]
		);

		if ( is_array( $updated ) && in_array( (string) ( $updated['status'] ?? '' ), [ 'queued', 'running' ], true ) ) {
			$view_lock->touch( $view_id, $token, true );
		} elseif ( ! is_array( $updated ) || ! in_array( (string) ( $updated['status'] ?? '' ), [ 'queued', 'running' ], true ) ) {
			$view_lock->release( $view_id, $token );
		}

		if ( is_array( $updated ) ) {
			LifecycleEvents::terminal_transition( $updated, $previous_status, $token );
		}
	}
}
