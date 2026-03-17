<?php
/**
 * Transforms internal scheduler models into the stable JSON API contract
 * consumed by the Svelte frontend.
 *
 * This is the API boundary — internal model changes don't break the frontend
 * as long as JobSerializer's output shape stays stable.
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler\Overview;

use GravityKit\GravityImport\Foundation\Scheduler\Models\AbstractAction;
use GravityKit\GravityImport\Foundation\Scheduler\Models\JobProgress;
use GravityKit\GravityImport\Foundation\Scheduler\Models\Task;
use GravityKit\GravityImport\Foundation\Scheduler\Store\DbStore;

/**
 * Serializes scheduler data for the frontend JSON API.
 *
 * @since 1.12.0
 */
class JobSerializer {

	/**
	 * Converts an ActionScheduler row into the JSON API format.
	 *
	 * @since 1.12.0
	 *
	 * @param array $row Row data from JobQueryService::build_row().
	 *
	 * @return array Serialized job data.
	 */
	public function serialize_job( array $row ): array {
		$hook     = $row['hook'] ?? '';
		$status   = strtolower( $row['status_name'] ?? '' );
		$progress = $this->build_progress( $row );
		$logs     = $this->associate_logs_with_tasks( $row );
		$tasks    = $row['args']['tasks'] ?? [];

		$default_label = ( $row['args']['label'] ?? '' ) ?: $this->humanize_hook( $hook );

		$serialized_tasks = [];

		foreach ( $tasks as $name => $definition ) {
			$serialized_tasks[] = $this->serialize_task(
				(string) $name,
				$definition,
				$progress,
				$logs['task_logs'][ $name ] ?? []
			);
		}

		$schedule = $this->serialize_schedule( $row );

		// For completed/failed jobs, use the actual execution timestamp
		// (last_attempt_gmt) instead of the stale scheduled date.
		if ( in_array( $status, [ 'complete', 'failed', 'in-progress' ], true ) && ! empty( $row['ran_at'] ) ) {
			$schedule['timestamp'] = $row['ran_at'];
		}

		// Distinguish "scheduled" (has a real schedule) from plain "pending" (async, run now).
		if ( 'pending' === $status && 'async' !== $schedule['type'] ) {
			$status = 'scheduled';

			if ( ! empty( $schedule['timestamp'] ) && $schedule['timestamp'] > time() ) {
				$schedule['scheduled_for'] = $this->relative_time( $schedule['timestamp'] - time() );
			}
		}

		// Detect overlap-skipped jobs: the only meaningful event is [job_skipped].
		// Override the AS "complete" status to "skipped" so the UI shows the real state.
		if ( 'complete' === $status ) {
			foreach ( $logs['job_events'] as $event ) {
				if ( 'job_skipped' === ( $event['type'] ?? '' ) ) {
					$status = 'skipped';

					// Mark all pending tasks as skipped to match the job state.
					$progress->mark_remaining_skipped();

					break;
				}
			}
		}

		return [
			'id'           => (int) ( $row['ID'] ?? 0 ),
			'hook'         => $hook,
			'hook_label'   => $this->humanize_hook( $hook ),
			'label'        => $default_label,
			'status'       => $status,
			'status_label' => $this->status_label( $status ),
			'schedule'     => $schedule,
			'progress'     => [
				'percent'   => $progress->to_percent(),
				'completed' => count( $progress->completed() ),
				'total'     => $progress->total(),
			],
			'product'      => $this->serialize_product( $row['args']['product'] ?? '' ),
			'tasks'        => $serialized_tasks,
			'events'       => $logs['job_events'],
			'actions'      => $this->available_actions( $status, $schedule['type'] ),
		];
	}

	/**
	 * Serializes product identification data.
	 *
	 * @since 1.12.0
	 *
	 * @param string $text_domain Product text domain from job args.
	 *
	 * @return array{text_domain: string, name: string}
	 */
	protected function serialize_product( string $text_domain ): array {
		if ( '' === $text_domain ) {
			return [
				'text_domain' => '',
				'name'        => '',
			];
		}

		$info = AbstractAction::resolve_product( $text_domain );

		return [
			'text_domain' => $text_domain,
			'name'        => $info['name'] ?? $text_domain,
		];
	}

	/**
	 * Adds the actual execution timestamp to a row for non-pending statuses.
	 *
	 * For in-progress jobs, reads `_meta.started_at` from the job args (set by
	 * ScheduleHandler::run_job_tasks). We cannot use AS's `get_date()` here
	 * because the heartbeat mechanism (TaskExecutor::extend_job_timeout)
	 * overwrites `last_attempt_gmt` with a future timestamp for stuck-job
	 * detection.
	 *
	 * For completed/failed jobs, uses AS's `get_date()` which returns
	 * `last_attempt_gmt` — accurate because mark_complete/mark_failure
	 * reset it to the real time.
	 *
	 * @since 1.12.0
	 *
	 * @param array  $row   Row data from JobQueryService::build_row().
	 * @param object $store ActionScheduler store instance.
	 *
	 * @return array Row data with `ran_at` timestamp added.
	 */
	public function enrich_row_with_ran_at( array $row, $store ): array {
		$status = strtolower( $row['status_name'] ?? '' );

		if ( ! in_array( $status, [ 'complete', 'failed', 'in-progress' ], true ) ) {
			return $row;
		}

		// For in-progress jobs, use started_at from _meta. AS's last_attempt_gmt
		// is unreliable here because extend_job_timeout() overwrites it with a
		// future heartbeat value for stuck-job detection.
		$meta = $row['args']['_meta'] ?? [];

		if ( 'in-progress' === $status && ! empty( $meta['started_at'] ) ) {
			$row['ran_at'] = (int) $meta['started_at'];

			return $row;
		}

		// For terminal statuses (complete/failed), last_attempt_gmt is accurate
		// because mark_complete()/mark_failure() reset it to the real time.
		$id = (int) ( $row['ID'] ?? 0 );

		if ( $id < 1 ) {
			return $row;
		}

		try {
			$date = $store->get_date( $id ); // @phpstan-ignore method.notFound

			if ( $date ) {
				$row['ran_at'] = $date->getTimestamp();
			}
		} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Store lookup failed — fall through to log-based fallback.
		}

		return $row;
	}

	/**
	 * Converts a single task into the JSON API format.
	 *
	 * @since 1.12.0
	 *
	 * @param string      $name       The task name.
	 * @param array       $definition The task definition from job args.
	 * @param JobProgress $progress   The job's progress tracker.
	 * @param array       $logs       Log entries for this task.
	 *
	 * @return array Serialized task data.
	 */
	public function serialize_task( string $name, array $definition, JobProgress $progress, array $logs = [] ): array {
		$callback = $this->resolve_callback( $definition['callback'] ?? null );

		$args          = $definition['args'] ?? [];
		$meta          = $args[ Task::META_KEY ] ?? [];
		$filtered_args = $this->filter_internal_keys( $args );

		$error = $meta['error'] ?? '';

		// Strip [task_failed]-tagged entries when the task has a dedicated error
		// field (avoids duplicate display). The tag is added by ScheduleHandler
		// for final failures only; retry logs remain untagged and are preserved.
		if ( $error && $logs ) {
			$logs = array_values(
				array_filter(
					$logs,
					function ( $entry ) {
						return 'task_failed' !== ( $entry['type'] ?? '' );
					}
				)
			);
		}

		$started_at = '';

		foreach ( $logs as $event ) {
			if ( 'started' === ( $event['type'] ?? '' ) ) {
				$started_at = $event['time'];
				break;
			}
		}

		return [
			'name'         => $name,
			'label'        => $definition['label'] ?? '',
			'status'       => $progress->task_status( $name ),
			'callback'     => $callback,
			'args'         => $filtered_args,
			'dependencies' => $definition['dependencies'] ?? [],
			'started_at'   => $started_at,
			'time'         => $progress->task_time( $name ),
			'events'       => $logs,
			'error'        => $error,
			'retries'      => (int) ( $meta['retries'] ?? 0 ),
		];
	}

	/**
	 * Formats diagnostic rows for the Svelte UI.
	 *
	 * Converts newline separators in multi-part values to HTML line
	 * breaks and escapes the rest for safe {@html} rendering.
	 *
	 * @since 1.12.0
	 *
	 * @param array $rows Diagnostic rows.
	 *
	 * @return array
	 */
	public function serialize_diagnostics( array $rows ): array {
		foreach ( $rows as &$row ) {
			$row['value'] = nl2br( esc_html( $row['value'] ) );
		}

		return $rows;
	}

	/**
	 * Returns valid actions for a given job status.
	 *
	 * "Run & reschedule" only applies to recurring/cron jobs.
	 *
	 * @since 1.12.0
	 *
	 * @param string $status        The job status key.
	 * @param string $schedule_type Schedule type: 'async', 'single', 'recurring', or 'cron'.
	 *
	 * @return array List of available action strings.
	 */
	public function available_actions( string $status, string $schedule_type = 'async' ): array {
		$is_repeating = in_array( $schedule_type, [ 'recurring', 'cron' ], true );

		$map = [
			'pending'     => [ 'run_now', 'delete' ],
			'scheduled'   => $is_repeating
				? [ 'run_now', 'run_reschedule', 'pause', 'delete' ]
				: [ 'run_now', 'pause', 'delete' ],
			'in-progress' => [ 'pause', 'cancel' ],
			'paused'      => [ 'unpause', 'cancel', 'delete' ],
			'complete'    => [ 'delete' ],
			'failed'      => [ 'retry', 'delete' ],
			'canceled'    => [ 'delete' ],
		];

		return $map[ $status ] ?? [ 'delete' ];
	}

	/**
	 * Builds a JobProgress instance from a row's args.
	 *
	 * @since 1.12.0
	 *
	 * @param array $row Row data.
	 *
	 * @return JobProgress
	 */
	protected function build_progress( array $row ): JobProgress {
		$progress = JobProgress::restore( $row['args']['progress'] ?? [] );

		// Jobs that haven't started yet have empty progress.
		if ( $progress->is_empty() && ! empty( $row['args']['tasks'] ) ) {
			$status      = strtolower( $row['status_name'] ?? '' );
			$is_terminal = in_array( $status, [ 'canceled', 'failed' ], true );

			foreach ( array_keys( $row['args']['tasks'] ) as $name ) {
				if ( $is_terminal ) {
					$progress->update_task_status( (string) $name, Task::STATUS_SKIPPED );
				} else {
					$progress->add_pending( (string) $name );
				}
			}
		}

		return $progress;
	}

	/**
	 * Serializes the schedule metadata from a row.
	 *
	 * @since 1.12.0
	 *
	 * @param array $row Row data.
	 *
	 * @return array{type: string, timestamp: int|null, recurrence: string}
	 */
	protected function serialize_schedule( array $row ): array {
		$timestamp      = null;
		$schedule       = $row['schedule'] ?? null;
		$type           = 'async';
		$raw_recurrence = null;

		if ( is_object( $schedule ) ) {
			$type = $this->detect_schedule_type( $schedule );

			if ( method_exists( $schedule, 'get_date' ) && $schedule->get_date() ) {
				$timestamp = $schedule->get_date()->getTimestamp();
			}

			// Extract raw recurrence from the schedule object (seconds for interval, cron string for cron).
			if ( method_exists( $schedule, 'get_recurrence' ) ) {
				$raw_recurrence = $schedule->get_recurrence();
			}
		}

		if ( ! $timestamp && ! empty( $row['log_entries'] ) ) {
			// Fallback to first log entry date for completed jobs.
			$first_entry = reset( $row['log_entries'] );

			$entry_date = is_object( $first_entry ) && method_exists( $first_entry, 'get_date' ) ? $first_entry->get_date() : null; // @phpstan-ignore method.nonObject

			if ( $entry_date ) {
				$timestamp = $entry_date->getTimestamp();
			}
		}

		$recurrence = $this->humanize_recurrence( $raw_recurrence, $type );

		return [
			'type'       => $type,
			'timestamp'  => $timestamp,
			'recurrence' => $recurrence,
		];
	}

	/**
	 * Detects the schedule type from an ActionScheduler schedule object.
	 *
	 * @since 1.12.0
	 *
	 * @param object $schedule The ActionScheduler schedule object.
	 *
	 * @return string One of 'cron', 'recurring', 'single', or 'async'.
	 */
	protected function detect_schedule_type( $schedule ): string {
		$class = get_class( $schedule );

		if ( false !== strpos( $class, 'CronSchedule' ) ) {
			return 'cron';
		}

		if ( false !== strpos( $class, 'IntervalSchedule' ) ) {
			return 'recurring';
		}

		if ( false !== strpos( $class, 'NullSchedule' ) ) {
			return 'async';
		}

		return 'single';
	}

	/**
	 * Converts a raw recurrence value into a human-readable label.
	 *
	 * @since 1.12.0
	 *
	 * @param mixed  $recurrence Raw recurrence from the schedule object: int (seconds) for interval, string for cron, null for non-repeating.
	 * @param string $type       Schedule type from detect_schedule_type().
	 *
	 * @return string Human-readable recurrence label.
	 */
	protected function humanize_recurrence( $recurrence, string $type ): string {
		if ( null === $recurrence || '' === $recurrence ) {
			return '';
		}

		if ( 'cron' === $type && is_string( $recurrence ) ) {
			return $this->humanize_cron( $recurrence );
		}

		if ( 'recurring' === $type && is_numeric( $recurrence ) ) {
			return $this->humanize_interval( (int) $recurrence );
		}

		return '';
	}

	/**
	 * Converts an interval in seconds into a human-readable label.
	 *
	 * @since 1.12.0
	 *
	 * @param int $seconds The interval in seconds.
	 *
	 * @return string Human-readable interval (e.g. "Every minute", "Every 5 minutes").
	 */
	protected function humanize_interval( int $seconds ): string {
		if ( $seconds < 60 ) {
			/* translators: [count]: number of seconds. */
			return strtr( _n( 'Every [count] second', 'Every [count] seconds', $seconds, 'gk-gravityimport' ), [ '[count]' => $seconds ] );
		}

		$minutes = (int) round( $seconds / 60 );

		if ( 0 === $seconds % 60 && $minutes < 60 ) {
			if ( 1 === $minutes ) {
				return __( 'Every minute', 'gk-gravityimport' );
			}

			/* translators: [count]: number of minutes. */
			return strtr( _n( 'Every [count] minute', 'Every [count] minutes', $minutes, 'gk-gravityimport' ), [ '[count]' => $minutes ] );
		}

		$hours = (int) round( $seconds / 3600 );

		if ( 0 === $seconds % 3600 ) {
			if ( 1 === $hours ) {
				return __( 'Every hour', 'gk-gravityimport' );
			}

			/* translators: [count]: number of hours. */
			return strtr( _n( 'Every [count] hour', 'Every [count] hours', $hours, 'gk-gravityimport' ), [ '[count]' => $hours ] );
		}

		$days = (int) round( $seconds / 86400 );

		if ( 0 === $seconds % 86400 ) {
			if ( 1 === $days ) {
				return __( 'Every day', 'gk-gravityimport' );
			}

			/* translators: [count]: number of days. */
			return strtr( _n( 'Every [count] day', 'Every [count] days', $days, 'gk-gravityimport' ), [ '[count]' => $days ] );
		}

		/* translators: [count]: number of minutes. */
		return strtr( _n( 'Every [count] minute', 'Every [count] minutes', $minutes, 'gk-gravityimport' ), [ '[count]' => $minutes ] );
	}

	/**
	 * Converts a cron expression into a human-readable description.
	 *
	 * Handles common patterns. Falls back to showing the raw expression
	 * with a "Cron:" prefix for uncommon patterns.
	 *
	 * @since 1.12.0
	 *
	 * @param string $cron The cron expression (5 fields: min hour day month weekday).
	 *
	 * @return string Human-readable description.
	 */
	protected function humanize_cron( string $cron ): string {
		$parts = preg_split( '/\s+/', trim( $cron ) );

		if ( ! $parts || count( $parts ) !== 5 ) {
			/* translators: [cron]: raw cron expression. */
			return strtr( __( 'Cron: [cron]', 'gk-gravityimport' ), [ '[cron]' => $cron ] );
		}

		list( $min, $hour, $day, $month, $weekday ) = $parts;

		// Every minute: * * * * *.
		if ( '*' === $min && '*' === $hour && '*' === $day && '*' === $month && '*' === $weekday ) {
			return __( 'Every minute', 'gk-gravityimport' );
		}

		// Every N minutes: */N * * * *.
		if ( preg_match( '#^\*/(\d+)$#', $min, $m ) && '*' === $hour && '*' === $day && '*' === $month && '*' === $weekday ) {
			$n = (int) $m[1];

			/* translators: [count]: number of minutes. */
			return strtr( _n( 'Every [count] minute', 'Every [count] minutes', $n, 'gk-gravityimport' ), [ '[count]' => $n ] );
		}

		// Every hour at minute N: N * * * *.
		if ( is_numeric( $min ) && '*' === $hour && '*' === $day && '*' === $month && '*' === $weekday ) {
			return __( 'Every hour', 'gk-gravityimport' );
		}

		// Every N hours: 0 */N * * *.
		if ( '0' === $min && preg_match( '#^\*/(\d+)$#', $hour, $m ) && '*' === $day && '*' === $month && '*' === $weekday ) {
			$n = (int) $m[1];

			/* translators: [count]: number of hours. */
			return strtr( _n( 'Every [count] hour', 'Every [count] hours', $n, 'gk-gravityimport' ), [ '[count]' => $n ] );
		}

		// Daily at specific time: N N * * *.
		if ( is_numeric( $min ) && is_numeric( $hour ) && '*' === $day && '*' === $month && '*' === $weekday ) {
			/* translators: [time]: time of day (e.g. "03:00"). */
			return strtr( __( 'Daily at [time]', 'gk-gravityimport' ), [ '[time]' => sprintf( '%02d:%02d', (int) $hour, (int) $min ) ] );
		}

		// Weekly: N N * * N.
		if ( is_numeric( $min ) && is_numeric( $hour ) && '*' === $day && '*' === $month && is_numeric( $weekday ) ) {
			$day_names = [
				__( 'Sunday', 'gk-gravityimport' ),
				__( 'Monday', 'gk-gravityimport' ),
				__( 'Tuesday', 'gk-gravityimport' ),
				__( 'Wednesday', 'gk-gravityimport' ),
				__( 'Thursday', 'gk-gravityimport' ),
				__( 'Friday', 'gk-gravityimport' ),
				__( 'Saturday', 'gk-gravityimport' ),
			];
			$day_name  = $day_names[ (int) $weekday % 7 ] ?? $weekday;

			/* translators: [day]: day of week, [time]: time of day. */
			return strtr(
				__( 'Weekly on [day] at [time]', 'gk-gravityimport' ),
				[
					'[day]'  => $day_name,
					'[time]' => sprintf( '%02d:%02d', (int) $hour, (int) $min ),
				]
			);
		}

		/* translators: [cron]: raw cron expression. */
		return strtr( __( 'Cron: [cron]', 'gk-gravityimport' ), [ '[cron]' => $cron ] );
	}

	/**
	 * Matches log entries to task names, separating job events, task logs,
	 * and task errors.
	 *
	 * @since 1.12.0
	 *
	 * @param array $row Row data containing log_entries and args.
	 *
	 * @return array{job_events: array, task_logs: array}
	 */
	protected function associate_logs_with_tasks( array $row ): array {
		$result = [
			'job_events' => [],
			'task_logs'  => [],
		];

		if ( empty( $row['log_entries'] ) ) {
			return $result;
		}

		$task_names = array_keys( $row['args']['tasks'] ?? [] );

		foreach ( $row['log_entries'] as $entry ) {
			$message = $entry->get_message();

			if ( $this->is_internal_log( $message ) ) {
				continue;
			}

			$timestamp = $entry->get_date()->getTimestamp();

			// Messages tagged with [task:name] are routed to that task's Activity log.
			// The tag is stripped so the display message stays clean.
			$matched_task = '';

			if ( preg_match( '/^\[task:([^\]]+)\]\s*/', $message, $m ) ) {
				$matched_task = $m[1];
				$message      = substr( $message, strlen( $m[0] ) );
			}

			if ( $matched_task && in_array( $matched_task, $task_names, true ) ) {
				$parsed = $this->parse_event_tag( $message );

				$entry_data = [
					'time'    => $timestamp,
					'type'    => $parsed['type'],
					'message' => $parsed['message'],
				];

				// Extract checkpoint JSON from rerun messages (e.g. "Rerunning … — {"processed":6}").
				$separator     = ' — ';
				$separator_pos = strpos( $entry_data['message'], $separator . '{' );

				if ( false !== $separator_pos ) {
					$entry_data['checkpoint'] = substr( $entry_data['message'], $separator_pos + strlen( $separator ) );
					$entry_data['message']    = substr( $entry_data['message'], 0, $separator_pos );
				}

				$result['task_logs'][ $matched_task ][] = $entry_data;
			} else {
				$parsed = $this->parse_event_tag( $message );

				// Skip redundant events: task skips (shown via task status) and
				// legacy retry markers (retry history lives in task activity logs).
				if ( 'task_skipped' === $parsed['type'] || 'job_retried' === $parsed['type'] ) {
					continue;
				}

				$result['job_events'][] = [
					'time'    => $timestamp,
					'message' => $parsed['message'],
					'type'    => $parsed['type'],
				];
			}
		}

		// When a job is retried, earlier terminal events (job_failed) become
		// historical context. Keep only the most recent one so the timeline
		// doesn't show duplicate "Job failed." entries.
		$result['job_events'] = $this->deduplicate_terminal_events( $result['job_events'] );

		return $result;
	}

	/**
	 * Keeps only the last terminal event when a job has been retried.
	 *
	 * After retries, the log accumulates multiple "Job failed." entries.
	 * This keeps only the last one so the timeline shows a clean narrative.
	 *
	 * @since 1.12.0
	 *
	 * @param array $events Job events array.
	 *
	 * @return array Filtered events.
	 */
	protected function deduplicate_terminal_events( array $events ): array {
		$terminal_types = [ 'job_failed', 'job_completed', 'job_canceled' ];

		// Find the index of the last terminal event.
		$last_terminal_index = -1;

		foreach ( $events as $i => $event ) {
			if ( in_array( $event['type'] ?? '', $terminal_types, true ) ) {
				$last_terminal_index = $i;
			}
		}

		if ( $last_terminal_index < 1 ) {
			return $events;
		}

		// Keep only the last terminal event, drop all earlier ones.
		return array_values(
			array_filter(
				$events,
				static function ( $event, $i ) use ( $terminal_types, $last_terminal_index ) {
					if ( in_array( $event['type'] ?? '', $terminal_types, true ) && $i !== $last_terminal_index ) {
						return false;
					}

					return true;
				},
				ARRAY_FILTER_USE_BOTH
			)
		);
	}

	/**
	 * Checks whether a log message is an internal Action Scheduler lifecycle message.
	 *
	 * @since 1.12.0
	 *
	 * @param string $message The log message.
	 *
	 * @return bool True if the message is internal AS plumbing.
	 */
	protected function is_internal_log( string $message ): bool {
		$lower = strtolower( $message );

		// Keep failure messages — they contain the reason the job failed.
		if ( false !== strpos( $lower, 'action failed' ) ) {
			return false;
		}

		return 0 === strpos( $lower, 'action ' );
	}

	/**
	 * Extracts a `[tag]` prefix from a log message and returns the tag and clean message.
	 *
	 * Log messages may carry a machine-readable prefix like `[job_started] Job started.`.
	 * This method splits it into the tag (for frontend logic) and the human-readable message.
	 *
	 * @since 1.12.0
	 *
	 * @param string $message The raw log message.
	 *
	 * @return array{type: string, message: string} The extracted type and cleaned message.
	 */
	protected function parse_event_tag( string $message ): array {
		if ( preg_match( '/^\[([a-z_]+)\]\s*(.+)$/i', $message, $matches ) ) {
			return [
				'type'    => $matches[1],
				'message' => $matches[2],
			];
		}

		// AS failure messages: "action failed via [context]: [reason]".
		if ( preg_match( '/^action failed.*?:\s*(.+)$/i', $message, $matches ) ) {
			return [
				'type'    => 'job_failed',
				'message' => $matches[1],
			];
		}

		return [
			'type'    => 'info',
			'message' => $message,
		];
	}

	/**
	 * Strips internal keys (prefixed with underscore) from an associative array.
	 *
	 * @since 1.12.0
	 *
	 * @param array $data The data array to filter.
	 *
	 * @return array Filtered array without internal keys.
	 */
	protected function filter_internal_keys( array $data ): array {
		$filtered = [];

		foreach ( $data as $key => $value ) {
			if ( 0 !== strpos( (string) $key, '_' ) ) {
				$filtered[ $key ] = $value;
			}
		}

		return $filtered;
	}

	/**
	 * Resolves a task callback to its display name and source file location.
	 *
	 * @since 1.12.0
	 *
	 * @param mixed $callback The task callback.
	 *
	 * @return array{name: string, location: string} Callback name and file:line location.
	 */
	protected function resolve_callback( $callback ): array {
		$result = [
			'name'     => (string) wp_json_encode( $callback ),
			'location' => '',
		];

		if ( ! is_array( $callback ) || count( $callback ) !== 2 ) {
			return $result;
		}

		$class  = is_object( $callback[0] ) ? get_class( $callback[0] ) : (string) $callback[0];
		$method = $callback[1];

		$result['name'] = $class . '::' . $method . '()';

		try {
			$reflection = new \ReflectionMethod( $class, $method );
			$file       = $reflection->getFileName();

			if ( $file ) {
				$result['location'] = $this->shorten_path( $file ) . ':' . $reflection->getStartLine();
			}
		} catch ( \ReflectionException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Class or method not found — skip location.
		}

		return $result;
	}

	/**
	 * Shortens an absolute file path for display.
	 *
	 * Tries to make the path relative to wp-content, then ABSPATH,
	 * then falls back to the last two directory segments + filename.
	 *
	 * @since 1.12.0
	 *
	 * @param string $path Absolute file path.
	 *
	 * @return string Shortened path.
	 */
	protected function shorten_path( string $path ): string {
		// Normalize to forward slashes so comparisons work on Windows.
		$path = wp_normalize_path( $path );

		// Try wp-content relative.
		if ( defined( 'WP_CONTENT_DIR' ) ) {
			$wp_content = wp_normalize_path( (string) WP_CONTENT_DIR );

			if ( 0 === strpos( $path, $wp_content ) ) {
				return ltrim( substr( $path, strlen( $wp_content ) ), '/' );
			}
		}

		// Try ABSPATH relative.
		if ( defined( 'ABSPATH' ) ) {
			$abspath = wp_normalize_path( (string) ABSPATH );

			if ( 0 === strpos( $path, $abspath ) ) {
				return ltrim( substr( $path, strlen( $abspath ) ), '/' );
			}
		}

		// Fallback: show last meaningful segments (e.g., "GKSchedulerLab/src/Callbacks.php").
		$parts = array_values( array_filter( explode( '/', $path ) ) );

		if ( count( $parts ) > 3 ) {
			return implode( '/', array_slice( $parts, -3 ) );
		}

		return implode( '/', $parts );
	}

	/**
	 * Converts a hook name to a human-readable label.
	 *
	 * Strips common prefixes and replaces underscores with spaces.
	 *
	 * @since 1.12.0
	 *
	 * @param string $hook The raw hook name.
	 *
	 * @return string
	 */
	protected function humanize_hook( string $hook ): string {
		// Strip common GravityKit prefixes.
		$stripped = preg_replace( '/^gk(_foundation)?_/', '', $hook );

		return ucwords( str_replace( '_', ' ', $stripped ?: $hook ) );
	}

	/**
	 * Converts seconds into a human-readable relative time string.
	 *
	 * @since 1.12.0
	 *
	 * @param int $seconds Number of seconds from now.
	 *
	 * @return string Relative time string (e.g. "in 2 minutes").
	 */
	protected function relative_time( int $seconds ): string {
		if ( $seconds < 60 ) {
			/* translators: [count]: number of seconds. */
			return strtr( _n( 'in [count] second', 'in [count] seconds', $seconds, 'gk-gravityimport' ), [ '[count]' => $seconds ] );
		}

		$minutes = (int) round( $seconds / 60 );

		if ( $minutes < 60 ) {
			/* translators: [count]: number of minutes. */
			return strtr( _n( 'in [count] minute', 'in [count] minutes', $minutes, 'gk-gravityimport' ), [ '[count]' => $minutes ] );
		}

		$hours = (int) round( $minutes / 60 );

		/* translators: [count]: number of hours. */
		return strtr( _n( 'in [count] hour', 'in [count] hours', $hours, 'gk-gravityimport' ), [ '[count]' => $hours ] );
	}

	/**
	 * Returns a human-readable status label for a status key.
	 *
	 * @since 1.12.0
	 *
	 * @param string $status_key Status key.
	 *
	 * @return string
	 */
	protected function status_label( string $status_key ): string {
		$labels = [
			Task::STATUS_PENDING   => _x( 'pending', 'task_status', 'gk-gravityimport' ),
			Task::STATUS_RUNNING   => _x( 'running', 'task_status', 'gk-gravityimport' ),
			Task::STATUS_COMPLETED => _x( 'completed', 'task_status', 'gk-gravityimport' ),
			Task::STATUS_FAILED    => _x( 'failed', 'task_status', 'gk-gravityimport' ),
			Task::STATUS_SKIPPED   => _x( 'skipped', 'task_status', 'gk-gravityimport' ),
			DbStore::STATUS_PAUSED => _x( 'paused', 'task_status', 'gk-gravityimport' ),
			'in-progress'          => _x( 'in progress', 'task_status', 'gk-gravityimport' ),
			'complete'             => _x( 'completed', 'task_status', 'gk-gravityimport' ),
			'canceled'             => _x( 'canceled', 'task_status', 'gk-gravityimport' ),
			'scheduled'            => _x( 'scheduled', 'task_status', 'gk-gravityimport' ),
		];

		return $labels[ $status_key ] ?? '';
	}
}
