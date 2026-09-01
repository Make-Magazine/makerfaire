<?php
/**
 * GravityView entry batch job scheduler.
 *
 * @package GravityKit\GravityView\Entry\BackgroundJobs
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BackgroundJobs;

use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\BackgroundJobs\ResultNotice;
use GravityKit\GravityView\BackgroundJobs\Scheduler;
use GravityKit\GravityView\Entry\BulkActions\LifecycleEvents;
use WP_Error;

/**
 * Schedules entry-batch jobs through Foundation.
 *
 * @since 3.0.0
 */
final class EntryBatchJob {
	public const DEFAULT_JOB_NAME = 'entry_batch';

	public const TASK_NAME = 'process_entries';

	/**
	 * Schedules an entry-batch job.
	 *
	 * Supported args:
	 * - callback: Required static array callback. Receives (`\GV\Entry[] $entries`, `array $context`).
	 * - callback_args: Optional JSON-safe data passed in the callback context.
	 * - label: Optional Foundation job label.
	 * - product: Optional product text domain for Foundation attribution.
	 * - batch_size: Optional entries per batch. Default 100.
	 * - actor_user_id: Optional acting user ID. Default current user.
	 * - result_ttl: Optional result-store TTL.
	 * - dispatch: Whether to dispatch immediately with Foundation `run()`. Default true.
	 * - require_dispatch: Whether healthy dispatch is required before queueing. Default true.
	 * - fresh_dispatch_check: Whether to refresh Foundation's dispatch health probe. Default false.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View       $view      View whose result set should be processed.
	 * @param EntrySelection $selection Entry selection descriptor.
	 * @param array          $args      Job options.
	 * @param Scheduler|null $scheduler Optional scheduler adapter.
	 *
	 * @return array|WP_Error
	 */
	public static function schedule( \GV\View $view, EntrySelection $selection, array $args, ?Scheduler $scheduler = null ) {
		$scheduler = $scheduler ? $scheduler : Scheduler::instance();
		$callback  = $args['callback'] ?? null;

		if ( ! $scheduler->is_enabled() ) {
			$dispatch_error = $scheduler->dispatch_error();

			return is_wp_error( $dispatch_error ) ? $dispatch_error : new WP_Error(
				'gravityview_background_scheduler_disabled',
				__( 'Background processing is disabled.', 'gk-gravityview' )
			);
		}

		if ( ! EntryBatchTask::is_supported_callback( $callback ) ) {
			return new WP_Error(
				'gravityview_background_entry_batch_callback_invalid',
				__( 'The entry batch callback must be a callable static class method.', 'gk-gravityview' )
			);
		}

		if ( ! $view->form ) {
			return new WP_Error(
				'gravityview_background_entry_batch_view_invalid',
				__( 'The View is not connected to a form.', 'gk-gravityview' )
			);
		}

		$require_dispatch = isset( $args['require_dispatch'] ) ? (bool) $args['require_dispatch'] : true;

		/**
		 * Filters whether entry-batch jobs require healthy dispatch before queueing.
		 *
		 * @since 3.0.0
		 *
		 * @param bool           $require_dispatch Whether dispatch is required.
		 * @param \GV\View       $view             View instance.
		 * @param EntrySelection $selection        Entry selection.
		 * @param array          $args             Job options.
		 */
		$require_dispatch = (bool) apply_filters( 'gk/gravityview/background-jobs/entry-batch/require-dispatch', $require_dispatch, $view, $selection, $args );

		if ( $require_dispatch ) {
			$dispatch_error = $scheduler->dispatch_error( ! empty( $args['fresh_dispatch_check'] ) );

			if ( is_wp_error( $dispatch_error ) ) {
				return $dispatch_error;
			}
		}

		$result_store = new ResultStore();
		$result_token = empty( $args['result_token'] ) ? $result_store->create_token() : (string) $args['result_token'];
		$batch_size   = max( 1, (int) ( $args['batch_size'] ?? EntryBatchResolver::DEFAULT_BATCH_SIZE ) );

		/**
		 * Filters the entry batch size for GravityView background jobs.
		 *
		 * @since 3.0.0
		 *
		 * @param int            $batch_size Batch size.
		 * @param \GV\View       $view       View instance.
		 * @param EntrySelection $selection  Entry selection.
		 * @param array          $args       Job options.
		 */
		$batch_size = max( 1, (int) apply_filters( 'gk/gravityview/background-jobs/entry-batch/batch-size', $batch_size, $view, $selection, $args ) );

		$job_data = [
			'view_id'         => (int) $view->ID,
			'blog_id'         => get_current_blog_id(),
			'actor_user_id'   => max( 0, (int) ( $args['actor_user_id'] ?? get_current_user_id() ) ),
			'render_instance' => sanitize_key( (string) ( $args['render_instance'] ?? '' ) ),
			'selection'       => $selection->to_array(),
			'callback'        => $callback,
			'callback_args'   => (array) ( $args['callback_args'] ?? [] ),
			'batch_size'      => $batch_size,
			'result_token'    => $result_token,
			'result_ttl'      => max( 1, (int) ( $args['result_ttl'] ?? ResultStore::DEFAULT_TTL ) ),
			'processed'       => 0,
			'failed'          => 0,
			'cursor'          => 0,
			'created_at'      => time(),
		];

		/**
		 * Filters the Foundation job data used for an entry batch job.
		 *
		 * @since 3.0.0
		 *
		 * @param array          $job_data  Foundation job data.
		 * @param \GV\View       $view      View instance.
		 * @param EntrySelection $selection Entry selection.
		 * @param array          $args      Job options.
		 */
		$job_data = (array) apply_filters( 'gk/gravityview/background-jobs/entry-batch/job-data', $job_data, $view, $selection, $args );

		$job = $scheduler->create_job(
			(string) ( $args['job_name'] ?? self::DEFAULT_JOB_NAME . '_' . (int) $view->ID ),
			(string) ( $args['label'] ?? __( 'GravityView Entry Batch', 'gk-gravityview' ) ),
			(string) ( $args['product'] ?? Scheduler::PRODUCT ),
			$job_data
		);

		if ( is_wp_error( $job ) ) {
			return $job;
		}

		try {
			$job->task()
				->create( self::TASK_NAME, [ self::task_class(), 'run' ], [ 'cursor' => 0 ] )
				->set_label( (string) ( $args['task_label'] ?? __( 'Process Entries', 'gk-gravityview' ) ) )
				->queue();
		} catch ( \Throwable $e ) {
			return new WP_Error(
				'gravityview_background_entry_batch_task_failed',
				$e->getMessage()
			);
		}

		$result_store->set(
			$result_token,
			self::get_result_data(
				$job_data,
				[
					'status'      => 'queued',
					'job_id'      => 0,
					'processed'   => 0,
					'failed'      => 0,
					'cursor'      => 0,
					'query_total' => null,
					'last_error'  => '',
				]
			),
			(int) $job_data['result_ttl']
		);

		$result = empty( $args['dispatch'] ) && array_key_exists( 'dispatch', $args ) ? self::schedule_async( $job ) : $scheduler->run( $job );

		if ( is_wp_error( $result ) ) {
			$result_store->set(
				$result_token,
				self::get_result_data(
					$job_data,
					[
						'status'      => 'failed',
						'job_id'      => 0,
						'processed'   => 0,
						'failed'      => 0,
						'cursor'      => 0,
						'query_total' => null,
						'last_error'  => $result->get_error_message(),
					]
				),
				(int) $job_data['result_ttl']
			);

			return $result;
		}

		if ( ! is_callable( [ $result, 'succeeded' ] ) || ! $result->succeeded() ) {
			$result_store->set(
				$result_token,
				self::get_result_data(
					$job_data,
					[
						'status'      => 'failed',
						'job_id'      => is_callable( [ $result, 'job_id' ] ) ? (int) $result->job_id() : 0,
						'processed'   => 0,
						'failed'      => 0,
						'cursor'      => 0,
						'query_total' => null,
						'last_error'  => __( 'The entry batch job could not be scheduled.', 'gk-gravityview' ),
					]
				),
				(int) $job_data['result_ttl']
			);

			return new WP_Error(
				'gravityview_background_entry_batch_schedule_failed',
				__( 'The entry batch job could not be scheduled.', 'gk-gravityview' )
			);
		}

		$job_id = is_callable( [ $result, 'job_id' ] ) ? (int) $result->job_id() : 0;

		$updated_result = $result_store->update(
			$result_token,
			static function ( array $latest ) use ( $job_data, $job_id ) {
				if ( ! empty( $latest['consumed'] ) ) {
					return null;
				}

				if ( [] === $latest ) {
					return self::get_result_data(
						array_merge( $job_data, [ 'job_id' => $job_id ] ),
						[
							'status'      => 'queued',
							'job_id'      => $job_id,
							'processed'   => 0,
							'failed'      => 0,
							'cursor'      => 0,
							'query_total' => null,
							'last_error'  => '',
						]
					);
				}

				$latest['job_id'] = $job_id;

				return $latest;
			},
			(int) $job_data['result_ttl'],
			[
				'view_id'    => (int) ( $job_data['view_id'] ?? 0 ),
				'job_id'     => $job_id,
				'status'     => 'queued',
				'action_key' => sanitize_key( (string) ( $job_data['callback_args']['action_key'] ?? '' ) ),
			]
		);

		if ( is_array( $updated_result ) ) {
			LifecycleEvents::job_started( $updated_result, $result_token );
		}

		return [
			'job_result'   => $result,
			'job_id'       => $job_id,
			'result_token' => $result_token,
			'warning'      => is_callable( [ $result, 'has_warning' ] ) && $result->has_warning() ? $result->warning() : null,
		];
	}

	/**
	 * Schedules the job without immediate dispatch.
	 *
	 * @since 3.0.0
	 *
	 * @param object $job Foundation job handler.
	 *
	 * @return object|WP_Error
	 */
	private static function schedule_async( $job ) {
		if ( ! is_object( $job ) || ! is_callable( [ $job, 'async' ] ) ) {
			return new WP_Error(
				'gravityview_background_entry_batch_job_invalid',
				__( 'The entry batch job could not be scheduled.', 'gk-gravityview' )
			);
		}

		try {
			return $job->async();
		} catch ( \Throwable $e ) {
			return new WP_Error(
				'gravityview_background_entry_batch_schedule_failed',
				$e->getMessage()
			);
		}
	}

	/**
	 * Returns the task class.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private static function task_class(): string {
		return EntryBatchTask::class;
	}

	/**
	 * Builds a normalized result-store snapshot.
	 *
	 * @since 3.0.0
	 *
	 * @param array $job_data  Job data.
	 * @param array $overrides Snapshot overrides.
	 *
	 * @return array
	 */
	private static function get_result_data( array $job_data, array $overrides = [] ): array {
		$callback_args = (array) ( $job_data['callback_args'] ?? [] );

		return array_merge(
			[
				'status'          => 'queued',
				'job_id'          => (int) ( $job_data['job_id'] ?? 0 ),
				'view_id'         => (int) ( $job_data['view_id'] ?? 0 ),
				'blog_id'         => (int) ( $job_data['blog_id'] ?? get_current_blog_id() ),
				'actor_user_id'   => max( 0, (int) ( $job_data['actor_user_id'] ?? 0 ) ),
				'render_instance' => sanitize_key( (string) ( $job_data['render_instance'] ?? '' ) ),
				'action_key'      => isset( $callback_args['action_key'] ) ? sanitize_key( $callback_args['action_key'] ) : '',
				'action_label'    => isset( $callback_args['action_label'] ) ? sanitize_text_field( $callback_args['action_label'] ) : '',
				'action_settings' => isset( $callback_args['action_settings'] ) && is_array( $callback_args['action_settings'] ) ? $callback_args['action_settings'] : [],
				'queued_message'  => isset( $callback_args['queued_message'] ) ? sanitize_text_field( $callback_args['queued_message'] ) : '',
				'message'         => isset( $job_data['message'] ) ? ResultNotice::normalize_message( $job_data['message'] ) : '',
				'result'          => isset( $job_data['result'] ) && is_array( $job_data['result'] ) ? $job_data['result'] : [],
				'processed'       => max( 0, (int) ( $job_data['processed'] ?? 0 ) ),
				'failed'          => max( 0, (int) ( $job_data['failed'] ?? 0 ) ),
				'cursor'          => max( 0, (int) ( $job_data['cursor'] ?? 0 ) ),
				'created_at'      => max( 0, (int) ( $job_data['created_at'] ?? time() ) ),
				'updated_at'      => max( 0, (int) ( $job_data['updated_at'] ?? time() ) ),
				'query_total'     => isset( $job_data['query_total'] ) ? max( 0, (int) $job_data['query_total'] ) : null,
				'last_error'      => isset( $job_data['last_error'] ) ? (string) $job_data['last_error'] : '',
			],
			$overrides
		);
	}
}
