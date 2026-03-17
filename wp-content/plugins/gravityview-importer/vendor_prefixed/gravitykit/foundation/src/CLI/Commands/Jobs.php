<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\CLI\Commands;

use Exception;
use GravityKit\GravityImport\Foundation\CLI\AbstractCommand;
use GravityKit\GravityImport\Foundation\Scheduler\Handlers\TaskExecutor;
use GravityKit\GravityImport\Foundation\Scheduler\JobScheduler;
use GravityKit\GravityImport\Foundation\Scheduler\Overview\JobActionService;
use GravityKit\GravityImport\Foundation\Scheduler\Overview\JobQueryService;
use GravityKit\GravityImport\Foundation\Scheduler\Overview\JobSerializer;
use GravityKit\GravityImport\Foundation\Scheduler\Store\DbStore;
use WP_CLI;
use function WP_CLI\Utils\format_items;

/**
 * Manage background jobs.
 *
 * @since 1.12.0
 */
class Jobs extends AbstractCommand {

	const DEFAULT_FORMAT = 'table';

	/**
	 * Cached query service instance.
	 *
	 * @since 1.12.0
	 *
	 * @var JobQueryService|null
	 */
	private $cached_query_service;

	/**
	 * Cached action service instance.
	 *
	 * @since 1.12.0
	 *
	 * @var JobActionService|null
	 */
	private $cached_action_service;

	/**
	 * Cached store instance.
	 *
	 * @since 1.12.0
	 *
	 * @var DbStore|null
	 */
	private $cached_store;

	/**
	 * List background jobs.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @subcommand list
	 *
	 * ## OPTIONS
	 *
	 * [--status=<status>]
	 * : Filter by status. Accepted: pending, scheduled, in-progress, complete, failed, canceled, paused.
	 *
	 * [--hook=<hook>]
	 * : Filter by hook name or prefix.
	 *
	 * [--per-page=<n>]
	 * : Number of jobs to display. Default: 20.
	 *
	 * [--format=<format>]
	 * : Output format. Accepted: table, json, csv, count. Default: table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk jobs list
	 *     wp gk jobs list --status=failed
	 *     wp gk jobs list --hook=gk/gravityexport
	 *     wp gk jobs list --format=json
	 *     wp gk jobs list --status=failed --format=count
	 *
	 * @synopsis [--status=<status>] [--hook=<hook>] [--per-page=<n>] [--format=<format>]
	 *
	 * @return void
	 */
	public function list( array $args, array $assoc_args ) {
		$format      = $assoc_args['format'] ?? self::DEFAULT_FORMAT;
		$hook_filter = $assoc_args['hook'] ?? '';
		$filters     = [
			'status'   => $assoc_args['status'] ?? '',
			'per_page' => $hook_filter ? -1 : (int) ( $assoc_args['per-page'] ?? 20 ),
		];

		$result = $this->query_service()->list( $filters );
		$jobs   = $result['jobs'];

		// Post-filter by hook prefix (AS only supports exact match).
		if ( $hook_filter ) {
			$jobs = array_values(
				array_filter(
					$jobs,
					static function ( $job ) use ( $hook_filter ) {
						return 0 === strpos( $job['hook'], $hook_filter );
					}
				)
			);
		}

		if ( 'count' === $format ) {
			WP_CLI::line( (string) count( $jobs ) );

			return;
		}

		if ( empty( $jobs ) ) {
			WP_CLI::line( 'No jobs found.' );

			return;
		}

		if ( in_array( $format, [ 'table', 'csv' ], true ) ) {
			$rows = array_map( [ $this, 'flatten_job_row' ], $jobs );

			format_items( $format, $rows, [ 'ID', 'Label', 'Status', 'Schedule', 'Progress', 'Time' ] );

			return;
		}

		format_items( $format, $jobs, array_keys( reset( $jobs ) ) );
	}

	/**
	 * Get details for a single job.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @subcommand get
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The job ID.
	 *
	 * [--format=<format>]
	 * : Output format. Accepted: table, json. Default: table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk jobs get 42
	 *     wp gk jobs get 42 --format=json
	 *
	 * @synopsis <id> [--format=<format>]
	 *
	 * @return void
	 */
	public function get( array $args, array $assoc_args ) {
		$id     = $this->ensure_numeric_id( $args[0] );
		$format = $assoc_args['format'] ?? self::DEFAULT_FORMAT;
		$job    = $this->query_service()->get( $id );

		if ( ! $job ) {
			WP_CLI::error( "Job {$id} not found." );
		}

		if ( 'json' === $format ) {
			WP_CLI::line( (string) wp_json_encode( $job, JSON_PRETTY_PRINT ) );

			return;
		}

		$this->render_job_detail( $job );
	}

	/**
	 * Show scheduler health and diagnostics.
	 *
	 * Note: The loopback probe is performed from the CLI context and may
	 * not reflect web-request behavior.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @subcommand health
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Output format. Accepted: table, json. Default: table.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk jobs health
	 *     wp gk jobs health --format=json
	 *
	 * @synopsis [--format=<format>]
	 *
	 * @return void
	 */
	public function health( array $args, array $assoc_args ) {
		$format = $assoc_args['format'] ?? self::DEFAULT_FORMAT;
		$data   = $this->query_service()->diagnostics();

		if ( 'json' === $format ) {
			WP_CLI::line( (string) wp_json_encode( $data, JSON_PRETTY_PRINT ) );

			if ( $data['health']['has_failure'] ) {
				WP_CLI::halt( 1 );
			}

			return;
		}

		$rows = [];

		foreach ( $data['diagnostics'] as $row ) {
			$rows[] = [
				'Check'  => $row['label'],
				'Status' => strtoupper( $row['status'] ),
				'Value'  => str_replace( "\n", ' | ', wp_strip_all_tags( $row['value'] ) ),
			];
		}

		format_items( 'table', $rows, [ 'Check', 'Status', 'Value' ] );

		WP_CLI::line( '' );
		WP_CLI::warning( 'Loopback test was performed from CLI context and may differ from web.' );

		if ( $data['health']['has_failure'] ) {
			WP_CLI::error( $data['health']['message'] ?: 'Health check detected problems.' );
		}

		WP_CLI::success( 'Scheduler is healthy.' );
	}

	/**
	 * Cancel a running or pending job.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @subcommand cancel
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The job ID to cancel.
	 *
	 * [--yes]
	 * : Skip confirmation prompt.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk jobs cancel 42
	 *     wp gk jobs cancel 42 --yes
	 *
	 * @synopsis <id> [--yes]
	 *
	 * @return void
	 */
	public function cancel( array $args, array $assoc_args ) {
		$id  = $this->ensure_numeric_id( $args[0] );
		$job = $this->require_job( $id );

		if ( ! in_array( 'cancel', $job['actions'], true ) ) {
			WP_CLI::error( "Job {$id} cannot be canceled (status: {$job['status']})." );
		}

		WP_CLI::confirm( "Cancel job {$id}?", $assoc_args );

		try {
			$this->action_service()->cancel( $id );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		WP_CLI::success( "Job {$id} canceled." );
	}

	/**
	 * Pause a running or pending job.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @subcommand pause
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The job ID to pause.
	 *
	 * [--yes]
	 * : Skip confirmation prompt.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk jobs pause 42
	 *     wp gk jobs pause 42 --yes
	 *
	 * @synopsis <id> [--yes]
	 *
	 * @return void
	 */
	public function pause( array $args, array $assoc_args ) {
		$id  = $this->ensure_numeric_id( $args[0] );
		$job = $this->require_job( $id );

		if ( ! in_array( 'pause', $job['actions'], true ) ) {
			WP_CLI::error( "Job {$id} cannot be paused (status: {$job['status']})." );
		}

		WP_CLI::confirm( "Pause job {$id}?", $assoc_args );

		try {
			$this->action_service()->pause( $id );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		WP_CLI::success( "Job {$id} paused." );
	}

	/**
	 * Unpause a paused job.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @subcommand unpause
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The job ID to unpause.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk jobs unpause 42
	 *
	 * @synopsis <id>
	 *
	 * @return void
	 */
	public function unpause( array $args, array $assoc_args ) {
		$id  = $this->ensure_numeric_id( $args[0] );
		$job = $this->require_job( $id );

		if ( ! in_array( 'unpause', $job['actions'], true ) ) {
			WP_CLI::error( "Job {$id} cannot be unpaused (status: {$job['status']})." );
		}

		try {
			$this->action_service()->unpause( $id );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		WP_CLI::success( "Job {$id} unpaused." );
	}

	/**
	 * Retry a failed job from the point of failure.
	 *
	 * Resets failed and skipped tasks to pending, clears retry metadata,
	 * and restarts the task chain.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @subcommand retry
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The job ID to retry.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk jobs retry 42
	 *
	 * @synopsis <id>
	 *
	 * @return void
	 */
	public function retry( array $args, array $assoc_args ) {
		$id  = $this->ensure_numeric_id( $args[0] );
		$job = $this->require_job( $id );

		if ( 'failed' !== $job['status'] ) {
			WP_CLI::error( "Job {$id} is not failed (status: {$job['status']}). Only failed jobs can be retried." );
		}

		try {
			$this->action_service()->retry( $id );
		} catch ( Exception $e ) {
			WP_CLI::error( 'Retry failed: ' . $e->getMessage() );
		}

		WP_CLI::success( "Job {$id} retried. Tasks will resume from the point of failure." );
	}

	/**
	 * Delete one or more jobs.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @subcommand delete
	 *
	 * ## OPTIONS
	 *
	 * [<id>]
	 * : The job ID to delete.
	 *
	 * [--status=<status>]
	 * : When combined with --all, delete all jobs matching this status. Use 'all' to delete regardless of status.
	 *
	 * [--all]
	 * : Delete all matching jobs. Requires --status.
	 *
	 * [--yes]
	 * : Skip confirmation prompt.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk jobs delete 42
	 *     wp gk jobs delete --status=failed --all --yes
	 *     wp gk jobs delete --status=complete --all --yes
	 *     wp gk jobs delete --status=all --all --yes
	 *
	 * @synopsis [<id>] [--status=<status>] [--all] [--yes]
	 *
	 * @return void
	 */
	public function delete( array $args, array $assoc_args ) {
		if ( isset( $assoc_args['all'] ) ) {
			$this->delete_bulk( $assoc_args );

			return;
		}

		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( 'Provide a job ID, or use --status=<status> --all to delete in bulk.' );
		}

		$id = $this->ensure_numeric_id( $args[0] );

		$this->require_job( $id );

		WP_CLI::confirm( "Delete job {$id}?", $assoc_args );

		try {
			$this->action_service()->delete( $id );
		} catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}

		WP_CLI::success( "Job {$id} deleted." );
	}

	/**
	 * Run a job synchronously or dispatch it asynchronously.
	 *
	 * By default, runs inline in the current process with per-task progress
	 * output. With --async, dispatches for background processing and returns
	 * immediately.
	 *
	 * @since 1.12.0
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @subcommand run
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The job ID to run.
	 *
	 * [--reschedule]
	 * : For recurring jobs, reset the next occurrence relative to now.
	 *
	 * [--async]
	 * : Dispatch for background processing instead of running synchronously.
	 *
	 * [--timeout=<seconds>]
	 * : Maximum seconds for sync execution. Default: 300.
	 *
	 * ## EXAMPLES
	 *
	 *     wp gk jobs run 42
	 *     wp gk jobs run 42 --reschedule
	 *     wp gk jobs run 42 --async
	 *     wp gk jobs run 42 --timeout=60
	 *
	 * @synopsis <id> [--reschedule] [--async] [--timeout=<seconds>]
	 *
	 * @return void
	 */
	public function run( array $args, array $assoc_args ) {
		$id         = $this->ensure_numeric_id( $args[0] );
		$reschedule = isset( $assoc_args['reschedule'] );
		$async      = isset( $assoc_args['async'] );
		$timeout    = (int) ( $assoc_args['timeout'] ?? 300 );
		$job        = $this->require_job( $id );

		$can_run = in_array( 'run_now', $job['actions'], true )
			|| in_array( 'run_reschedule', $job['actions'], true );

		if ( ! $can_run ) {
			WP_CLI::error( "Job {$id} cannot be run (status: {$job['status']})." );
		}

		if ( $async ) {
			try {
				if ( $reschedule ) {
					$this->action_service()->run_reschedule( $id );
				} else {
					$this->action_service()->run_now( $id );
				}
			} catch ( Exception $e ) {
				WP_CLI::error( $e->getMessage() );
			}

			WP_CLI::success( "Job {$id} dispatched for background processing." );

			return;
		}

		// Sync mode: process the job action inline, blocking AS dispatch
		// so the CLI loop can process task actions itself.
		$this->run_sync( $id, $reschedule, $timeout );
	}

	// ------------------------------------------------------------------
	// Private helpers
	// ------------------------------------------------------------------

	/**
	 * Flattens a serialized job array into a table row.
	 *
	 * @since 1.12.0
	 *
	 * @param array $job Serialized job data.
	 *
	 * @return array Flat row with ID, Label, Status, Schedule, Progress, Time.
	 */
	private function flatten_job_row( array $job ): array {
		$total     = $job['progress']['total'] ?? 0;
		$completed = $job['progress']['completed'] ?? 0;
		$progress  = $total > 0 ? "{$completed}/{$total}" : '-';
		$time      = ! empty( $job['schedule']['timestamp'] )
			? wp_date( 'Y-m-d H:i:s', $job['schedule']['timestamp'] )
			: '';

		return [
			'ID'       => $job['id'],
			'Label'    => $job['label'],
			'Status'   => $job['status'],
			'Schedule' => $job['schedule']['recurrence'] ?: $job['schedule']['type'],
			'Progress' => $progress,
			'Time'     => $time,
		];
	}

	/**
	 * Renders detailed job output for table format.
	 *
	 * @since 1.12.0
	 *
	 * @param array $job Serialized job data.
	 *
	 * @return void
	 */
	private function render_job_detail( array $job ): void {
		WP_CLI::line( '' );
		WP_CLI::line( "Job #{$job['id']}: {$job['label']}" );
		WP_CLI::line( str_repeat( '-', 50 ) );

		$header = [
			[
				'Field' => 'Status',
				'Value' => $job['status'],
			],
			[
				'Field' => 'Hook',
				'Value' => $job['hook'],
			],
			[
				'Field' => 'Schedule',
				'Value' => $job['schedule']['recurrence'] ?: $job['schedule']['type'],
			],
			[
				'Field' => 'Progress',
				'Value' => "{$job['progress']['completed']}/{$job['progress']['total']} ({$job['progress']['percent']}%)",
			],
		];

		if ( ! empty( $job['schedule']['timestamp'] ) ) {
			$header[] = [
				'Field' => 'Time',
				'Value' => wp_date( 'Y-m-d H:i:s', $job['schedule']['timestamp'] ),
			];
		}

		if ( ! empty( $job['actions'] ) ) {
			$header[] = [
				'Field' => 'Actions',
				'Value' => implode( ', ', $job['actions'] ),
			];
		}

		format_items( 'table', $header, [ 'Field', 'Value' ] );

		if ( ! empty( $job['tasks'] ) ) {
			$this->render_tasks( $job['tasks'] );
		}

		if ( ! empty( $job['events'] ) ) {
			$this->render_events( $job['events'] );
		}
	}

	/**
	 * Renders task list for job detail output.
	 *
	 * @since 1.12.0
	 *
	 * @param array $tasks Serialized task list.
	 *
	 * @return void
	 */
	private function render_tasks( array $tasks ): void {
		WP_CLI::line( "\nTasks:" );

		$has_errors = false;
		$task_rows  = [];

		foreach ( $tasks as $task ) {
			$row = [
				'Name'   => $task['name'],
				'Label'  => $task['label'],
				'Status' => $task['status'],
				'Time'   => $task['time'] ?: '-',
				'Error'  => '',
			];

			if ( ! empty( $task['error'] ) ) {
				$row['Error'] = $task['error'];
				$has_errors   = true;
			}

			$task_rows[] = $row;
		}

		$columns = $has_errors
			? [ 'Name', 'Label', 'Status', 'Time', 'Error' ]
			: [ 'Name', 'Label', 'Status', 'Time' ];

		format_items( 'table', $task_rows, $columns );
	}

	/**
	 * Renders event list for job detail output.
	 *
	 * @since 1.12.0
	 *
	 * @param array $events Serialized event list.
	 *
	 * @return void
	 */
	private function render_events( array $events ): void {
		WP_CLI::line( "\nEvents:" );

		$rows = [];

		foreach ( $events as $event ) {
			$rows[] = [
				'Time'    => wp_date( 'Y-m-d H:i:s', $event['time'] ),
				'Type'    => $event['type'],
				'Message' => $event['message'],
			];
		}

		format_items( 'table', $rows, [ 'Time', 'Type', 'Message' ] );
	}

	/**
	 * Loads a job or exits with an error.
	 *
	 * @since 1.12.0
	 *
	 * @param int $id The job ID.
	 *
	 * @return array Serialized job data.
	 */
	private function require_job( int $id ): array {
		$job = $this->query_service()->get( $id );

		if ( ! $job ) {
			WP_CLI::error( "Job {$id} not found." );
		}

		return $job;
	}

	/**
	 * Deletes jobs in bulk by status.
	 *
	 * @since 1.12.0
	 *
	 * @param array $assoc_args Command associative arguments.
	 *
	 * @return void
	 */
	private function delete_bulk( array $assoc_args ): void {
		$status = $assoc_args['status'] ?? '';

		if ( ! $status ) {
			WP_CLI::error( 'The --status flag is required for bulk deletion.' );
		}

		$query_status = 'all' === $status ? '' : $status;

		$result = $this->query_service()->list(
			[
				'status'   => $query_status,
				'per_page' => -1,
			]
		);

		$jobs = $result['jobs'];

		if ( empty( $jobs ) ) {
			WP_CLI::line( "No jobs found with status: {$status}." );

			return;
		}

		$count   = count( $jobs );
		$actions = $this->action_service();

		WP_CLI::confirm( "Delete {$count} job(s) with status '{$status}'?", $assoc_args );

		$deleted = 0;

		foreach ( $jobs as $job ) {
			try {
				$actions->delete( $job['id'] );
				$deleted++;
			} catch ( Exception $e ) {
				WP_CLI::warning( "Failed to delete job {$job['id']}: " . $e->getMessage() );
			}
		}

		if ( $deleted === $count ) {
			WP_CLI::success( "Deleted {$deleted} job(s)." );
		} else {
			WP_CLI::error( "Deleted {$deleted} of {$count} job(s). " . ( $count - $deleted ) . ' failed.' );
		}
	}

	/**
	 * Runs a job synchronously by processing actions inline.
	 *
	 * Blocks AS loopback dispatch so the CLI process owns task execution.
	 * Registers a SIGINT handler if pcntl is available for graceful interruption.
	 *
	 * @since 1.12.0
	 *
	 * @param int  $id         The job action ID.
	 * @param bool $reschedule Whether to reschedule after execution.
	 * @param int  $timeout    Maximum seconds for sync execution.
	 *
	 * @return void
	 */
	private function run_sync( int $id, bool $reschedule = false, int $timeout = 300 ): void {
		// Block AS/WP-Cron loopback requests so tasks stay pending for us.
		// Non-AS requests pass through so task callbacks can make HTTP calls.
		$block_dispatch = function ( $preempt, $args, $url ) {
			if ( false !== strpos( $url, 'action_scheduler' )
				|| false !== strpos( $url, 'doing_wp_cron' )
				|| false !== strpos( $url, 'wp-cron.php' )
			) {
				return new \WP_Error( 'cli_sync', 'Blocked AS dispatch during CLI sync execution.' );
			}

			return $preempt;
		};

		add_filter( 'pre_http_request', $block_dispatch, 0, 3 );

		try {
			if ( $reschedule ) {
				$this->action_service()->run_reschedule( $id );
			} else {
				$this->action_service()->run_now( $id );
			}
		} catch ( Exception $e ) {
			remove_filter( 'pre_http_request', $block_dispatch, 0 );

			WP_CLI::error( $e->getMessage() );
		}

		$this->process_task_loop( $id, $block_dispatch, $timeout );
	}

	/**
	 * Processes pending task actions in a loop with progress output.
	 *
	 * @since 1.12.0
	 *
	 * @param int      $id             The job action ID.
	 * @param callable $block_dispatch The dispatch-blocking filter callback.
	 * @param int      $timeout        Maximum seconds for the loop.
	 *
	 * @return void
	 */
	private function process_task_loop( int $id, callable $block_dispatch, int $timeout = 300 ): void {
		$interrupted    = false;
		$max_iterations = 1000;
		$iteration      = 0;
		$empty_polls    = 0;
		$max_empty      = 10;
		$start_time     = time();

		// Register SIGINT and SIGTERM handlers for graceful interruption.
		if ( function_exists( 'pcntl_signal' ) && function_exists( 'pcntl_async_signals' ) ) {
			pcntl_async_signals( true );

			$signal_handler = function () use ( &$interrupted ) {
				$interrupted = true;

				WP_CLI::warning( 'Interrupted. Finishing current task...' );
			};

			pcntl_signal( SIGINT, $signal_handler );
			pcntl_signal( SIGTERM, $signal_handler );
		}

		while ( ! $interrupted && $iteration < $max_iterations ) {
			// Check timeout.
			if ( ( time() - $start_time ) >= $timeout ) {
				WP_CLI::warning( "Sync execution timed out after {$timeout} seconds." );

				break;
			}
			$iteration++;

			$task_action_id = $this->find_pending_task( $id );

			if ( ! $task_action_id ) {
				$job = $this->query_service()->get( $id );

				if ( ! $job || ! in_array( $job['status'], [ 'pending', 'in-progress', 'scheduled' ], true ) ) {
					break;
				}

				$empty_polls++;

				if ( $empty_polls >= $max_empty ) {
					break;
				}

				// Brief wait for the next task to be scheduled.
				usleep( 250000 );

				continue;
			}

			$empty_polls = 0;

			// Identify the current pending task from the job's progress tracker.
			$job       = $this->query_service()->get( $id );
			$task_name = $this->get_current_task_name( $job );

			WP_CLI::line( "Running task: {$task_name}..." );

			try {
				\ActionScheduler::runner()->process_action( $task_action_id, 'WP-CLI' ); // @phpstan-ignore staticMethod.notFound

				WP_CLI::line( "  Task {$task_name} done." );
			} catch ( \Throwable $e ) {
				WP_CLI::warning( "  Task {$task_name} failed: " . $e->getMessage() );
			}
		}

		remove_filter( 'pre_http_request', $block_dispatch, 0 );

		// Show final status.
		$job = $this->query_service()->get( $id );

		if ( ! $job ) {
			WP_CLI::line( 'Job no longer exists.' );

			return;
		}

		WP_CLI::line( '' );

		if ( 'complete' === $job['status'] ) {
			WP_CLI::success( "Job {$id} completed ({$job['progress']['completed']}/{$job['progress']['total']} tasks)." );
		} elseif ( 'failed' === $job['status'] ) {
			WP_CLI::error( "Job {$id} failed ({$job['progress']['completed']}/{$job['progress']['total']} tasks)." );
		} elseif ( $interrupted ) {
			WP_CLI::warning( "Job {$id} interrupted (status: {$job['status']})." );
		} else {
			WP_CLI::line( "Job {$id} status: {$job['status']} ({$job['progress']['completed']}/{$job['progress']['total']} tasks)." );
		}
	}

	/**
	 * Finds the next pending task action for a job.
	 *
	 * @since 1.12.0
	 *
	 * @param int $job_id The parent job ID.
	 *
	 * @return int|null The task action ID, or null if none pending.
	 */
	private function find_pending_task( int $job_id ): ?int {
		$pending = as_get_scheduled_actions(
			[
				'hook'     => TaskExecutor::HOOK,
				'group'    => DbStore::TASK_GROUP_ID,
				'status'   => \ActionScheduler_Store::STATUS_PENDING,
				'per_page' => -1,
				'orderby'  => 'date',
				'order'    => 'ASC',
			]
		);

		if ( empty( $pending ) ) {
			return null;
		}

		// Find the first task belonging to our job.
		// Task args are positional: [0 => job_id, 1 => job_name].
		foreach ( $pending as $action_id => $action ) {
			$args = $action->get_args();

			if ( (int) ( $args[0] ?? 0 ) === $job_id ) {
				return (int) $action_id;
			}
		}

		return null;
	}

	/**
	 * Returns the name of the next pending task from the serialized job data.
	 *
	 * @since 1.12.0
	 *
	 * @param array|null $job Serialized job data.
	 *
	 * @return string Task label or name.
	 */
	private function get_current_task_name( ?array $job ): string {
		if ( ! $job || empty( $job['tasks'] ) ) {
			return 'unknown';
		}

		foreach ( $job['tasks'] as $task ) {
			if ( 'pending' === $task['status'] || 'running' === $task['status'] ) {
				return $task['label'] ?: $task['name'];
			}
		}

		return 'unknown';
	}

	// ------------------------------------------------------------------
	// Validation helpers
	// ------------------------------------------------------------------

	/**
	 * Validates and converts a raw argument to a positive integer ID.
	 *
	 * @since 1.12.0
	 *
	 * @param string $raw The raw argument value.
	 *
	 * @return int The validated ID.
	 */
	private function ensure_numeric_id( string $raw ): int {
		if ( ! preg_match( '/^[1-9]\d*$/', $raw ) ) {
			WP_CLI::error( "Invalid job ID: {$raw}. Must be a positive integer." );
		}

		return (int) $raw;
	}

	// ------------------------------------------------------------------
	// Service accessors
	// ------------------------------------------------------------------

	/**
	 * Returns the job query service instance.
	 *
	 * @since 1.12.0
	 *
	 * @return JobQueryService
	 */
	protected function query_service(): JobQueryService {
		if ( ! $this->cached_query_service ) {
			$this->cached_query_service = new JobQueryService(
				DbStore::get_instance(),
				new JobSerializer()
			);
		}

		return $this->cached_query_service;
	}

	/**
	 * Returns the job action service instance.
	 *
	 * @since 1.12.0
	 *
	 * @return JobActionService
	 */
	protected function action_service(): JobActionService {
		if ( ! $this->cached_action_service ) {
			$this->cached_action_service = new JobActionService(
				JobScheduler::get_instance()->manager(),
				DbStore::get_instance()
			);
		}

		return $this->cached_action_service;
	}

	/**
	 * Returns the database store instance.
	 *
	 * @since 1.12.0
	 *
	 * @return DbStore
	 */
	protected function store(): DbStore {
		if ( ! $this->cached_store ) {
			$this->cached_store = DbStore::get_instance();
		}

		return $this->cached_store;
	}
}
