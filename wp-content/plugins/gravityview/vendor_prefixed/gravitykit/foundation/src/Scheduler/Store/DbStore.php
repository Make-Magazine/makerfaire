<?php
/**
 * Low-level data access layer for the Scheduler system.
 *
 * This class provides direct database operations for Action Scheduler actions.
 * It should return raw Action Scheduler objects or primitive data types.
 *
 * For job objects (JobInstance, etc.), use ScheduleHandler methods instead.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Store;

use ActionScheduler;
use ActionScheduler_Action;
use RuntimeException;

use ActionScheduler_DBStore;
use ActionScheduler_TimezoneHelper;
use DateTime;
use DateTimeZone;
use Exception;
use GravityKit\GravityView\Foundation\Scheduler\Handlers\TaskExecutor;
use wpdb;
use GravityKit\GravityView\Foundation\Scheduler\Traits\LoggerTrait;
use GravityKit\GravityView\Foundation\Scheduler\Traits\SingletonTrait;

class DbStore extends ActionScheduler_DBStore {

	use LoggerTrait;
	use SingletonTrait;

	/**
	 * The task paused status.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const STATUS_PAUSED = 'paused';

	/**
	 * The task pending status.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const STATUS_PENDING = 'pending';

	/**
	 * The scheduler group ID. Should be the same for all GravityKit jobs.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const GROUP_ID = 'gk_scheduler';

	/**
	 * The task execution group ID.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const TASK_GROUP_ID = 'gk_scheduler_task';

	/**
	 * Seconds after scheduled_date_gmt before a pending job is considered overdue.
	 *
	 * 10 minutes accounts for normal AS pipeline delays (cron gap + lock +
	 * batch processing) while catching broken infrastructure quickly.
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	const OVERDUE_THRESHOLD = 600;

	/**
	 * Wpdb instance.
	 *
	 * @since 1.12.0
	 *
	 * @var wpdb|null
	 * */
	protected $db;

	/**
	 * The four AS tables that must exist for the scheduler to function.
	 *
	 * @since 1.12.0
	 *
	 * @var string[]
	 */
	const REQUIRED_TABLES = [
		'actionscheduler_actions',
		'actionscheduler_logs',
		'actionscheduler_groups',
		'actionscheduler_claims',
	];

	/**
	 * Whether AS tables have been verified to exist during this request.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 */
	private static $tables_verified = false;

	/**
	 * Whether table recovery has already failed during this request.
	 *
	 * Prevents repeated SHOW TABLES + recovery attempts on every db() call
	 * when tables are permanently unavailable.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 */
	private static $recovery_failed = false;

	/**
	 * Callback invoked when a job instance's args are persisted to the database.
	 *
	 * Used for cache invalidation by ScheduleHandler. Not a WordPress hook — this
	 * is module-internal communication between DbStore and its consumers.
	 *
	 * @since 1.12.0
	 *
	 * @var callable|null
	 */
	private $on_instance_persisted;

	/**
	 * Constructor. Registers internal hook listeners.
	 *
	 * @since 1.12.0
	 */
	protected function __construct() {
		add_action( 'gk/foundation/plugin-activated', [ self::class, 'clear_table_cache' ] );
	}

	/**
	 * Registers a callback to be invoked when job instance args are persisted.
	 *
	 * @since 1.12.0
	 *
	 * @param callable $callback Receives the action ID (int) as its only argument.
	 *
	 * @return void
	 */
	public function on_instance_persisted( callable $callback ): void {
		$this->on_instance_persisted = $callback;
	}

	/**
	 * Query for action count or list of action IDs.
	 *
	 * @inheritDoc
	 *
	 * @since 1.12.0
	 *
	 * @param array  $query Query filtering options.
	 * @param string $query_type Whether to select or count the results. Defaults to select.
	 *
	 * @return string|array|null The IDs of actions matching the query. Null on failure.
	 * */
	public function query_actions( $query = [], $query_type = 'select' ) {
		$query['group'] = self::GROUP_ID;

		return parent::query_actions( $query, $query_type );
	}

	/**
	 * Overrides the parent function to count only GravityKit jobs.
	 * Gets a count of all GravityKit jobs grouped by status.
	 *
	 * @since 1.12.0
	 *
	 * @return array Set of 'status' => int $count pairs for statuses with 1 or more actions of that status.
	 * @see parent::action_counts()
	 */
	public function action_counts(): array {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT a.status, COUNT(a.status) as count
				FROM {$wpdb->actionscheduler_actions} a
				INNER JOIN {$wpdb->actionscheduler_groups} g
					ON g.group_id = a.group_id AND g.slug = %s
				GROUP BY a.status",
				self::GROUP_ID
			)
		);

		$actions_count_by_status = [];
		$labels                  = $this->get_status_labels();

		foreach ( $results as $action_data ) {
			// Ignore any actions with invalid status.
			if ( array_key_exists( $action_data->status, $labels ) ) {
				$actions_count_by_status[ $action_data->status ] = $action_data->count;
			}
		}

		return $actions_count_by_status;
	}

	/**
	 * Adds Paused label
	 *
	 * @return array|string[]|void[]
	 */
	public function get_status_labels(): array {
		$defaults = parent::get_status_labels();

		$insert = [
			self::STATUS_PAUSED => __( 'Paused', 'gk-gravityview' ),
		];

		return array_merge( $insert, $defaults );
	}

	/**
	 * Creates the job instance to run once immediately.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $name The job name.
	 * @param array|null $args Arguments to pass when the job triggers.
	 * @param bool       $unique Whether the task should be unique. It will not be scheduled if another pending or running action has the same hook and group parameters.
	 * @param int        $priority Lower values take precedence over higher values. Defaults to 10, with acceptable values falling in the range 0-255.
	 *
	 * @return int instance ID. Zero if there was an error scheduling the task.
	 */
	public function create_job_async( string $name, ?array $args = null, bool $unique = false, int $priority = 10 ): int {
		/**
		 * Fires before a job is scheduled.
		 *
		 * @since 1.12.0
		 *
		 * @param string $name          The job name.
		 * @param array  $args          The job args.
		 * @param string $schedule_type The schedule type. Accepts 'async', 'single', 'recurring', or 'cron'.
		 */
		do_action( 'gk/foundation/scheduler/job/schedule/before', $name, $args, 'async' );

		$job_id = intval( as_enqueue_async_action( $name, $args, self::GROUP_ID, $unique, $priority ) );

		if ( $job_id ) {
			/**
			 * Fires after a job is successfully scheduled.
			 *
			 * @since 1.12.0
			 *
			 * @param int    $job_id        The job ID.
			 * @param string $name          The job name.
			 * @param string $schedule_type The schedule type. Accepts 'async', 'single', 'recurring', or 'cron'.
			 */
			do_action( 'gk/foundation/scheduler/job/schedule/after', $job_id, $name, 'async' );
		} else {
			/**
			 * Fires when a job fails to schedule.
			 *
			 * @since 1.12.0
			 *
			 * @param string $name          The job name.
			 * @param array  $args          The job args.
			 * @param string $schedule_type The schedule type. Accepts 'async', 'single', 'recurring', or 'cron'.
			 */
			do_action( 'gk/foundation/scheduler/job/schedule/failed', $name, $args, 'async' );
		}

		return $job_id;
	}

	/**
	 * Creates a single job instance.
	 *
	 * @since 1.12.0
	 *
	 * @param int        $timestamp When the task will run.
	 * @param string     $name The job name.
	 * @param array|null $args Arguments to pass when the job triggers.
	 * @param bool       $unique Whether the task should be unique. It will not be scheduled if another pending or running action has the same hook and group parameters.
	 * @param int        $priority Lower values take precedence over higher values. Defaults to 10, with acceptable values falling in the range 0-255.
	 *
	 * @return int instance ID. Zero if there was an error scheduling the task.
	 */
	public function create_job_single( int $timestamp, string $name, ?array $args = null, bool $unique = true, int $priority = 10 ): int {
		/** This action is documented in DbStore::create_job_async(). */
		do_action( 'gk/foundation/scheduler/job/schedule/before', $name, $args, 'single' );

		$job_id = intval( as_schedule_single_action( $timestamp, $name, $args, self::GROUP_ID, $unique, $priority ) );

		if ( $job_id ) {
			/** This action is documented in DbStore::create_job_async(). */
			do_action( 'gk/foundation/scheduler/job/schedule/after', $job_id, $name, 'single' );
		} else {
			/** This action is documented in DbStore::create_job_async(). */
			do_action( 'gk/foundation/scheduler/job/schedule/failed', $name, $args, 'single' );
		}

		return $job_id;
	}

	/**
	 * Creates a recurring job instance.
	 *
	 * @since 1.12.0
	 *
	 * @param int        $timestamp When the first run will start.
	 * @param int        $interval_in_seconds How long to wait between runs.
	 * @param string     $name The Job name to trigger.
	 * @param array|null $args Arguments to pass when the hook triggers.
	 * @param bool       $unique Whether the action should be unique. It will not be scheduled if another pending or running action has the same hook and group parameters.
	 * @param int        $priority Lower values take precedence over higher values.
	 *
	 * @return int instance ID. Zero if there was an error scheduling the job.
	 */
	public function create_job_recurring( int $timestamp, int $interval_in_seconds, string $name, ?array $args = null, bool $unique = true, int $priority = 10 ): int {
		/** This action is documented in DbStore::create_job_async(). */
		do_action( 'gk/foundation/scheduler/job/schedule/before', $name, $args, 'recurring' );

		$job_id = intval( as_schedule_recurring_action( $timestamp, $interval_in_seconds, $name, $args, self::GROUP_ID, $unique, $priority ) );

		if ( $job_id ) {
			/** This action is documented in DbStore::create_job_async(). */
			do_action( 'gk/foundation/scheduler/job/schedule/after', $job_id, $name, 'recurring' );
		} else {
			/** This action is documented in DbStore::create_job_async(). */
			do_action( 'gk/foundation/scheduler/job/schedule/failed', $name, $args, 'recurring' );
		}

		return $job_id;
	}

	/**
	 * Creates a job instance that recurs on a cron-like schedule.
	 *
	 * @since 1.12.0
	 *
	 * @param int        $timestamp The first run will be scheduled to start at a time calculated
	 *                          after this timestamp matching the cron expression.
	 *                          This can be used to delay the first run.
	 * @param string     $schedule A cron schedule string.
	 * @param string     $name The job name to trigger.
	 * @param array|null $args Arguments to pass to the job.
	 * @param bool       $unique Whether the run should be unique. It will not be scheduled if another pending or executing run has the same hook and group parameters.
	 * @param int        $priority Lower values take precedence over higher values. Defaults to 10, with acceptable values falling in the range 0-255.
	 *
	 * @return int instance ID. Zero if there was an error scheduling the job.
	 */
	public function create_job_cron( int $timestamp, string $schedule, string $name, ?array $args = null, bool $unique = true, int $priority = 10 ): int {
		/** This action is documented in DbStore::create_job_async(). */
		do_action( 'gk/foundation/scheduler/job/schedule/before', $name, $args, 'cron' );

		$job_id = intval( as_schedule_cron_action( $timestamp, $schedule, $name, $args, self::GROUP_ID, $unique, $priority ) );

		if ( $job_id ) {
			/** This action is documented in DbStore::create_job_async(). */
			do_action( 'gk/foundation/scheduler/job/schedule/after', $job_id, $name, 'cron' );
		} else {
			/** This action is documented in DbStore::create_job_async(). */
			do_action( 'gk/foundation/scheduler/job/schedule/failed', $name, $args, 'cron' );
		}

		return $job_id;
	}

	/**
	 * Unschedules the latest pending job instance.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $job_name The job name.
	 * @param array|null $args     Args passed to the job. Null matches any args.
	 *
	 * @return int|null The run ID if a scheduled instance was found, or null if no matching instances found.
	 */
	public function unschedule_latest( string $job_name, ?array $args = null ): ?int {
		/**
		 * Fires before unscheduling the latest job run.
		 *
		 * @since 1.12.0
		 *
		 * @param string     $job_name The job name.
		 * @param array|null $args     The job args.
		 */
		do_action( 'gk/foundation/scheduler/job/' . $job_name . '/unschedule/latest', $job_name, $args );

		$id = as_unschedule_action( $job_name, $args, self::GROUP_ID );

		return null !== $id ? intval( $id ) : null;
	}

	/**
	 * Unschedules all job instances.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $job_name The job name.
	 * @param array|null $args Args passed to the job.
	 *
	 * @return void
	 */
	public function unschedule_job( string $job_name, ?array $args = null ): void {
		/**
		 * Fires before unscheduling all job instances.
		 *
		 * @since 1.12.0
		 *
		 * @param string     $job_name The job name.
		 * @param array|null $args     The job args.
		 */
		do_action( 'gk/foundation/scheduler/job/' . $job_name . '/unschedule', $job_name, $args );

		as_unschedule_all_actions( $job_name, $args, self::GROUP_ID );
	}

	/**
	 * Deletes all pending job instances (physical removal from the database).
	 *
	 * Unlike unschedule_job() which soft-cancels (sets status to 'canceled'),
	 * this method permanently removes pending actions. Use this when replacing
	 * a schedule (e.g., interval change) so no ghost canceled entries remain.
	 *
	 * @since 1.12.0
	 *
	 * @param string $job_name The job name (hook).
	 *
	 * @return void
	 */
	public function delete_job( string $job_name ): void {
		/**
		 * Fires before deleting all pending and paused job instances.
		 *
		 * @since 1.12.0
		 *
		 * @param string $job_name The job name.
		 */
		do_action( 'gk/foundation/scheduler/job/' . $job_name . '/delete', $job_name );

		$ids = $this->query_actions(
			[
				'hook'     => $job_name,
				'group'    => self::GROUP_ID,
				'status'   => [ self::STATUS_PENDING, self::STATUS_PAUSED ],
				'per_page' => -1,
			]
		);

		foreach ( (array) $ids as $id ) {
			$this->delete_action( $id );
		}
	}

	/**
	 * Pauses all pending job instances.
	 *
	 * @since 1.12.0
	 *
	 * @param string $name The job name.
	 *
	 * @return bool
	 */
	public function pause_job( string $name ): bool {
		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		$updated = $this->db()->query(
			$this->db()->prepare(
				"UPDATE {$table} a
				 INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
				 SET a.status = %s
				 WHERE a.hook = %s AND a.status = %s AND g.slug = %s",
				self::STATUS_PAUSED,
				$name,
				self::STATUS_PENDING,
				self::GROUP_ID
			)
		);

		/**
		 * Fires when the job is paused.
		 *
		 * @since 1.12.0
		 */
		do_action( 'gk/foundation/scheduler/job/' . $name . '/paused' );

		return boolval( $updated );
	}

	/**
	 * Unpauses all job instances.
	 *
	 * @since 1.12.0
	 *
	 * @param string $name The job name.
	 *
	 * @return bool
	 */
	public function unpause_job( string $name ): bool {
		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		$updated = $this->db()->query(
			$this->db()->prepare(
				"UPDATE {$table} a
				 INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
				 SET a.status = %s
				 WHERE a.hook = %s AND a.status = %s AND g.slug = %s",
				self::STATUS_PENDING,
				$name,
				self::STATUS_PAUSED,
				self::GROUP_ID
			)
		);

		/**
		 * Fires when the job is unpaused.
		 *
		 * @since 1.12.0
		 */
		do_action( 'gk/foundation/scheduler/job/' . $name . '/unpaused' );

		return boolval( $updated );
	}

	/**
	 * Pauses the job instance.
	 *
	 * @since 1.12.0
	 *
	 * @param int $instance_id The instance ID.
	 *
	 * @return bool If the run paused or not.
	 *
	 * @throws Exception
	 */
	public function pause_job_instance( int $instance_id ): bool {
		$table = $this->actions_table();

		// Accept both pending and running jobs. Pending jobs haven't started
		// yet; running jobs have tasks in progress.
		$updated = $this->db()->query(
			$this->db()->prepare(
				"UPDATE {$table} SET `status` = %s WHERE `action_id` = %d AND `status` IN (%s, %s)",
				self::STATUS_PAUSED,
				$instance_id,
				self::STATUS_PENDING,
				self::STATUS_RUNNING
			)
		);

		if ( ! $updated ) {
			// translators: [id] is replaced with the run ID.
			throw new Exception( strtr( __( 'Unable to pause run [id]. It may have been changed by another process.', 'gk-gravityview' ), [ '[id]' => $instance_id ] ) );
		}

		return true;
	}

	/**
	 * Unpauses the job instance.
	 *
	 * @since 1.12.0
	 *
	 * @param int $instance_id The job instance ID.
	 *
	 * @return bool
	 *
	 * @throws Exception
	 */
	public function unpause_job_instance( int $instance_id ): bool {

		$updated = $this->db()->update(
			$this->actions_table(),
			[ 'status' => self::STATUS_PENDING ],
			[
				'action_id' => $instance_id,
				'status'    => self::STATUS_PAUSED,
			],
			[ '%s' ],
			[ '%d', '%s' ]
		);

		if ( ! $updated ) {
			// translators: [id] is replaced with the run ID.
			throw new Exception(
				strtr( esc_html__( 'Unidentified run [id]: we were unable to unpause this run. It may have been changed by another process.', 'gk-gravityview' ), [ '[id]' => intval( $instance_id ) ] )
			);
		}

		return true;
	}

	/**
	 * Checks if the job was paused.
	 *
	 * @since  1.12.0
	 *
	 * @param string $job_name Job name.
	 *
	 * @return bool
	 */
	public function is_job_paused( string $job_name ): bool {
		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		$res = $this->db()->get_row(
			$this->db()->prepare(
				"SELECT a.action_id FROM {$table} a
				 INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
				 WHERE a.status = %s AND a.hook = %s AND g.slug = %s",
				self::STATUS_PAUSED,
				$job_name,
				self::GROUP_ID
			)
		);

		return boolval( $res );
	}

	/**
	 * Checks if the job was scheduled.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $name The job name.
	 * @param array|null $args Task args.
	 *
	 * @return bool True if there is a pending or running job instances, false otherwise.
	 */
	public function is_scheduled( string $name, ?array $args = null ): bool {
		if ( ! function_exists( 'as_has_scheduled_action' ) ) {
			return false;
		}

		return as_has_scheduled_action( $name, $args, self::GROUP_ID );
	}

	/**
	 * Gets the job instances with all possible statuses.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $job_name The job name.
	 * @param array|null $query Query args.
	 *
	 * @return ActionScheduler_Action[] All job instances list.
	 *
	 * @see default_instances_query() for all possible query args.
	 */
	public function get_all_instances( string $job_name = '', ?array $query = [] ): array {
		$defaults = $this->default_instances_query( $job_name );

		$query = wp_parse_args( (array) $query, $defaults );

		return $this->get_instances( $query );
	}

	/**
	 * Gets the pending instances.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $job_name The job name.
	 * @param array|null $query Query args.
	 *
	 * @return ActionScheduler_Action[] Pending job instances list.
	 *
	 * @see default_instances_query() for all possible query args.
	 */
	public function get_pending_instances( string $job_name = '', ?array $query = [] ): array {
		$defaults = $this->default_instances_query( $job_name, self::STATUS_PENDING );

		$query = wp_parse_args( (array) $query, $defaults );

		return $this->get_instances( $query );
	}

	/**
	 * Gets the job args.
	 *
	 * @param int $job_id The job ID.
	 *
	 * @return array|null
	 */
	public function get_job_args( int $job_id ): ?array {
		$table        = $this->actions_table();
		$groups_table = $this->action_groups_table();

		$row = $this->db()->get_row(
			$this->db()->prepare(
				"SELECT `args`, `extended_args` FROM {$table} a
                       LEFT JOIN {$groups_table} g ON g.group_id = a.group_id
                       WHERE a.`action_id` = %d
                       AND g.slug = %s",
				$job_id,
				self::GROUP_ID
			)
		);

		if ( ! $row ) {
			return null;
		}

		// Prefer extended_args if available, otherwise fall back to args.
		$raw = $row->extended_args ?: $row->args;

		if ( ! $raw ) {
			return null;
		}

		$decoded = json_decode( $raw, true );

		return is_array( $decoded ) ? $decoded : null;
	}

	/**
	 * Updates the action instance args.
	 *
	 * @since 1.12.0
	 *
	 * @param int   $action_id The job instance ID.
	 * @param array $args The job args.
	 *
	 * @return bool
	 */
	public function update_instance_args( int $action_id, array $args ): bool {

		$this->logger()->debug( __METHOD__, compact( 'action_id', 'args' ) );

		$rows = $this->db()->update(
			$this->actions_table(),
			[
				'extended_args' => wp_json_encode( $args ),
			],
			[
				'action_id' => $action_id,
			],
			[ '%s' ],
			[ '%d' ]
		);

		if ( $this->on_instance_persisted ) {
			call_user_func( $this->on_instance_persisted, $action_id );
		}

		return boolval( $rows );
	}

	/**
	 * Updates action instance time.
	 *
	 * @since 1.12.0
	 *
	 * @param int      $action_id The action ID.
	 * @param DateTime $time The time to set.
	 *
	 * @return bool
	 */
	public function update_instance_time( int $action_id, DateTime $time ): bool {

		$gmt = clone( $time );
		$gmt->setTimezone( new DateTimeZone( 'UTC' ) );
		$local = clone( $time );
		// @phpstan-ignore-next-line
		ActionScheduler_TimezoneHelper::set_local_timezone( $local );

		$data    = [
			'scheduled_date_gmt'   => $gmt->format( 'Y-m-d H:i:s' ),
			'scheduled_date_local' => $local->format( 'Y-m-d H:i:s' ),
		];
		$formats = [ '%s', '%s' ];

		$new_schedule = $this->rebuild_schedule( $action_id, $gmt );

		if ( $new_schedule ) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
			$data['schedule'] = serialize( $new_schedule );
			$formats[]        = '%s';
		}

		$rows = $this->db()->update(
			$this->actions_table(),
			$data,
			[ 'action_id' => $action_id ],
			$formats,
			[ '%d' ]
		);

		return boolval( $rows );
	}

	/**
	 * Rebuilds a schedule object with a new date, preserving recurrence.
	 *
	 * @since 1.12.0
	 *
	 * @param int      $action_id The action ID.
	 * @param DateTime $new_date  The new scheduled date (UTC).
	 *
	 * @return \ActionScheduler_CronSchedule|\ActionScheduler_IntervalSchedule|\ActionScheduler_SimpleSchedule|null The rebuilt schedule, or null if it can't be rebuilt.
	 */
	private function rebuild_schedule( int $action_id, DateTime $new_date ) {
		$action = $this->fetch_action( $action_id );

		if ( $action instanceof \ActionScheduler_NullAction ) {
			return null;
		}

		$schedule = $action->get_schedule();

		if ( $schedule instanceof \ActionScheduler_CronSchedule ) {
			return new \ActionScheduler_CronSchedule( $new_date, $schedule->get_recurrence(), $schedule->get_first_date() );
		}

		if ( $schedule instanceof \ActionScheduler_IntervalSchedule ) {
			return new \ActionScheduler_IntervalSchedule( $new_date, $schedule->get_recurrence(), $schedule->get_first_date() );
		}

		if ( $schedule instanceof \ActionScheduler_SimpleSchedule ) {
			return new \ActionScheduler_SimpleSchedule( $new_date );
		}

		return null;
	}

	/**
	 * Gets the actions table name.
	 *
	 * @since 1.12.0
	 *
	 * @return string
	 */
	public function actions_table(): string {
		// @phpstan-ignore-next-line
		return $this->db()->actionscheduler_actions;
	}

	/**
	 * Gets the action groups table name.
	 *
	 * @since 1.12.0
	 *
	 * @return string
	 */
	public function action_groups_table(): string {
		// @phpstan-ignore-next-line
		return $this->db()->actionscheduler_groups;
	}

	/**
	 * Marks the job as completed.
	 *
	 * @since 1.12.0
	 *
	 * @param string $job_name The job name.
	 * @param int    $job_id The job ID.
	 *
	 * @return bool
	 */
	public function mark_job_completed( string $job_name, int $job_id ): bool {

		$rows = $this->db()->update(
			$this->actions_table(),
			[
				'status'             => self::STATUS_COMPLETE,
				'last_attempt_gmt'   => current_time( 'mysql', true ),
				'last_attempt_local' => current_time( 'mysql' ),
			],
			[
				'hook'      => $job_name,
				'action_id' => $job_id,
			],
			[ '%s', '%s', '%s' ],
			[ '%s', '%d' ]
		);

		return boolval( $rows );
	}

	/**
	 * Marks the job as failed.
	 *
	 * @since 1.12.0
	 *
	 * @param string $job_name The job name.
	 * @param int    $job_id   The job ID.
	 *
	 * @return bool
	 */
	public function mark_running_job_failed( string $job_name, int $job_id ): bool {

		$rows = $this->db()->update(
			$this->actions_table(),
			[
				'status'             => self::STATUS_FAILED,
				'last_attempt_gmt'   => current_time( 'mysql', true ),
				'last_attempt_local' => current_time( 'mysql' ),
			],
			[
				'hook'      => $job_name,
				'action_id' => $job_id,
				'status'    => self::STATUS_RUNNING,
			],
			[ '%s', '%s', '%s' ],
			[ '%s', '%d', '%s' ]
		);

		return boolval( $rows );
	}

	/**
	 * Updates the action status.
	 *
	 * @since 1.12.0
	 *
	 * @param int    $action_id The action ID.
	 * @param string $status The status to set.
	 *
	 * @return bool
	 */
	public function update_instance_status( int $action_id, string $status ): bool {
		$rows = $this->db()->update(
			$this->actions_table(),
			[
				'status'             => $status,
				'claim_id'           => 0,
				'last_attempt_gmt'   => current_time( 'mysql', true ),
				'last_attempt_local' => current_time( 'mysql' ),
			],
			[
				'action_id' => $action_id,
			],
			[ '%s', '%d', '%s', '%s' ],
			[ '%d' ]
		);

		return boolval( $rows );
	}

	/**
	 * Gets the action status by action ID.
	 *
	 * @param int $action_id The action ID.
	 *
	 * @return string|null
	 */
	public function get_instance_status( int $action_id ): ?string {
		$table = $this->actions_table();

		$status = $this->db()->get_var(
			$this->db()->prepare(
				"SELECT `status` FROM $table WHERE `action_id` = %d",
				$action_id
			)
		);

		return $status ?: null;
	}

	/**
	 * Retrieves the hook name for a given action ID.
	 *
	 * @since 1.12.0
	 *
	 * @param int $action_id The action ID.
	 *
	 * @return string|null The hook name, or null if not found.
	 */
	public function get_action_hook( int $action_id ): ?string {
		$table = $this->actions_table();

		$hook = $this->db()->get_var(
			$this->db()->prepare(
				"SELECT `hook` FROM $table WHERE `action_id` = %d",
				$action_id
			)
		);

		return $hook ?: null;
	}

	/**
	 * Updates the last attempt timestamp for an action.
	 *
	 * @since 1.12.0
	 *
	 * @param int $action_id The action ID.
	 * @param int $timestamp Unix timestamp to set as last attempt time.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function update_last_attempt( int $action_id, int $timestamp ): bool {
		$table = $this->actions_table();

		$result = $this->db()->update(
			$table,
			[
				'last_attempt_gmt'   => gmdate( 'Y-m-d H:i:s', $timestamp ),
				'last_attempt_local' => wp_date( 'Y-m-d H:i:s', $timestamp ),
			],
			[ 'action_id' => $action_id ]
		);

		return false !== $result;
	}

	/**
	 * Gets the running action ID by hook name.
	 *
	 * @since 1.12.0
	 *
	 * @param string $hook The action hook name.
	 *
	 * @return int|null The action ID, or null if not found.
	 */
	public function get_running_action_id( string $hook ): ?int {
		$table = $this->actions_table();

		$action_id = $this->db()->get_var(
			$this->db()->prepare(
				"SELECT `action_id` FROM $table WHERE `hook` = %s AND `status` = %s LIMIT 1",
				$hook,
				self::STATUS_RUNNING
			)
		);

		return $action_id ? (int) $action_id : null;
	}

	/**
	 * Retrieves the args for a given action ID.
	 *
	 * Checks both `args` and `extended_args` columns.
	 *
	 * @since 1.12.0
	 *
	 * @param int $action_id The action ID.
	 *
	 * @return array|null The action args, or null if not found.
	 */
	public function get_action_args( int $action_id ): ?array {
		$table = $this->actions_table();

		$row = $this->db()->get_row(
			$this->db()->prepare(
				"SELECT `args`, `extended_args` FROM $table WHERE `action_id` = %d",
				$action_id
			)
		);

		if ( ! $row ) {
			return null;
		}

		// Prefer extended_args if available, otherwise use args.
		$args = $row->extended_args ?: $row->args;

		if ( ! $args ) {
			return null;
		}

		$decoded = json_decode( $args, true );

		return is_array( $decoded ) ? $decoded : null;
	}

	/**
	 * Removes all actions matching the given hook name.
	 *
	 * Unlike as_unschedule_all_actions() which sets status to 'canceled',
	 * this method completely deletes the rows from the database.
	 *
	 * @since 1.12.0
	 *
	 * @param string $hook The action hook name.
	 *
	 * @return bool True if any rows were deleted, false otherwise.
	 */
	public function remove_actions_by_hook( string $hook ): bool {
		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		// Scope deletion to our group to avoid removing other plugins' actions.
		$rows = $this->db()->query(
			$this->db()->prepare(
				"DELETE a FROM {$table} a
				 INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
				 WHERE a.hook = %s AND g.slug = %s",
				$hook,
				self::TASK_GROUP_ID
			)
		);

		return boolval( $rows );
	}

	/**
	 * Deletes all actions in the specified groups with the given statuses.
	 *
	 * Physically removes rows from the actions table. Used during plugin
	 * deactivation to prevent orphaned actions from firing without a
	 * registered callback.
	 *
	 * @since 1.12.0
	 *
	 * @param string[] $groups   Group slugs to target.
	 * @param string[] $statuses Action statuses to delete.
	 *
	 * @return int Number of rows deleted.
	 */
	public function delete_actions_by_groups( array $groups, array $statuses ): int {
		if ( empty( $groups ) || empty( $statuses ) ) {
			return 0;
		}

		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		$group_placeholders  = implode( ', ', array_fill( 0, count( $groups ), '%s' ) );
		$status_placeholders = implode( ', ', array_fill( 0, count( $statuses ), '%s' ) );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql = "DELETE a FROM {$table} a
				INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
				WHERE g.slug IN ({$group_placeholders})
				AND a.status IN ({$status_placeholders})";

		$params = array_merge( $groups, $statuses );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = $this->db()->query( $this->db()->prepare( $sql, $params ) );

		return intval( $result );
	}

	/**
	 * Gets completed job instances.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $job_name The job name.
	 * @param array|null $query Query args.
	 *
	 * @return ActionScheduler_Action[] Completed job instances list.
	 *
	 * @see default_instances_query() for all possible query args.
	 */
	public function get_completed_instances( string $job_name = '', ?array $query = [] ): array {
		$defaults = $this->default_instances_query( $job_name, self::STATUS_COMPLETE );

		$query = wp_parse_args( (array) $query, $defaults );

		return $this->get_instances( $query );
	}

	/**
	 * Gets running job instances.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $job_name The job name.
	 * @param array|null $query Query args.
	 *
	 * @return ActionScheduler_Action[] Running job instances list (always one if the job is unique).
	 *
	 * @see default_instances_query() for all possible query args.
	 */
	public function get_running_instances( string $job_name = '', ?array $query = [] ): array {
		$defaults = $this->default_instances_query( $job_name, self::STATUS_RUNNING );

		$query = wp_parse_args( (array) $query, $defaults );

		return $this->get_instances( $query );
	}

	/**
	 * Gets failed job instances.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $job_name The job name.
	 * @param array|null $query Query args.
	 *
	 * @return ActionScheduler_Action[] Failed job instances list.
	 *
	 * @see default_instances_query() for all possible query args.
	 */
	public function get_failed_instances( string $job_name = '', ?array $query = [] ): array {
		$defaults = $this->default_instances_query( $job_name, self::STATUS_FAILED );

		$query = wp_parse_args( (array) $query, $defaults );

		return $this->get_instances( $query );
	}

	/**
	 * Gets canceled job instances.
	 *
	 * @since 1.12.0
	 *
	 * @param string     $job_name The job name.
	 * @param array|null $query Query args.
	 *
	 * @return ActionScheduler_Action[] Canceled job instances list.
	 *
	 * @see default_instances_query() for all possible query args.
	 */
	public function get_canceled_instances( string $job_name = '', ?array $query = [] ): array {
		$defaults = $this->default_instances_query( $job_name, self::STATUS_CANCELED );

		$query = wp_parse_args( (array) $query, $defaults );

		return $this->get_instances( $query );
	}

	/**
	 * Returns the number of active claims for GravityKit actions only.
	 *
	 * Unlike the parent's get_claim_count() which counts all AS claims
	 * system-wide, this scopes to GK groups so diagnostics don't report
	 * claims from other plugins (WooCommerce, etc.).
	 *
	 * @since 1.12.0
	 *
	 * @return int
	 */
	public function get_gk_claim_count(): int {
		$wpdb      = $this->db();
		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		$like = $wpdb->esc_like( self::GROUP_ID ) . '%';

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- Table names from helper methods.
		$sql = $wpdb->prepare(
			"SELECT COUNT(DISTINCT a.claim_id)
			FROM {$table} a
			INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
			WHERE a.claim_id != 0
			AND a.status IN (%s, %s)
			AND g.slug LIKE %s",
			self::STATUS_PENDING,
			self::STATUS_RUNNING,
			$like
		);

		return (int) $wpdb->get_var( $sql );
		// phpcs:enable
	}

	/**
	 * Gets the timestamp of the most recent activity for any GravityKit action.
	 *
	 * Returns the most recent last_attempt_gmt across all statuses. Any action
	 * with a non-zero last_attempt_gmt has been executed by Action Scheduler,
	 * regardless of its current status (completed, paused, failed, etc.).
	 *
	 * @since 1.12.0
	 *
	 * @return int|null Unix timestamp, or null if no actions have executed.
	 */
	public function get_last_activity_time(): ?int {
		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		/*
		 * Only check actual task execution actions — exclude:
		 * - Parent job actions (gk_scheduler group): last_attempt_gmt set by AS's queue
		 *   runner, which can lag 15-20 minutes behind actual task execution.
		 * - Maintenance actions (gk_scheduler_maintenance): infrastructure
		 *   hook that runs every 2 minutes, not user-visible activity.
		 */
		$time = $this->db()->get_var(
			$this->db()->prepare(
				"SELECT MAX(a.last_attempt_gmt)
				 FROM {$table} a
				 INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
				 WHERE g.slug = %s
				   AND a.hook = %s
				   AND a.last_attempt_gmt != '0000-00-00 00:00:00'",
				self::TASK_GROUP_ID,
				TaskExecutor::HOOK
			)
		);

		if ( ! $time ) {
			return null;
		}

		return strtotime( $time . ' UTC' ) ?: null;
	}

	/**
	 * Returns the timestamp of the last recovery heartbeat.
	 *
	 * @since 1.12.0
	 *
	 * @return int|null Unix timestamp, or null if no heartbeat found.
	 */
	public function get_last_recovery_heartbeat(): ?int {
		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		$time = $this->db()->get_var(
			$this->db()->prepare(
				"SELECT MAX(a.last_attempt_gmt)
				 FROM {$table} a
				 INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
				 WHERE g.slug = %s
				   AND a.hook = %s
				   AND a.last_attempt_gmt != '0000-00-00 00:00:00'",
				self::TASK_GROUP_ID,
				TaskExecutor::RECOVERY_HOOK
			)
		);

		if ( ! $time ) {
			return null;
		}

		return strtotime( $time . ' UTC' ) ?: null;
	}

	/**
	 * Returns the number of GravityKit jobs that are overdue.
	 *
	 * A job is overdue when it's pending and its scheduled date is more
	 * than 10 minutes in the past.
	 *
	 * @since 1.12.0
	 *
	 * @return int Number of overdue jobs.
	 */
	public function get_overdue_job_count(): int {
		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		/**
		 * Filters the overdue threshold for pending jobs.
		 *
		 * @since 1.12.0
		 *
		 * @param int $threshold Seconds after scheduled date before a pending job is considered overdue. Default 600.
		 */
		$threshold = (int) apply_filters( 'gk/foundation/scheduler/overdue-threshold', self::OVERDUE_THRESHOLD );
		$cutoff    = gmdate( 'Y-m-d H:i:s', time() - $threshold );

		return (int) $this->db()->get_var(
			$this->db()->prepare(
				"SELECT COUNT(*)
				 FROM {$table} a
				 INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
				 WHERE g.slug = %s
				   AND a.status = %s
				   AND a.scheduled_date_gmt < %s",
				self::GROUP_ID,
				self::STATUS_PENDING,
				$cutoff
			)
		);
	}

	/**
	 * Returns the timestamp of the last completed action across all AS groups.
	 *
	 * Answers "is Action Scheduler itself running?" regardless of whether
	 * GravityKit has pending work.
	 *
	 * @since 1.12.0
	 *
	 * @return int|null Unix timestamp, or null if no actions have completed.
	 */
	public function get_last_as_activity_time(): ?int {
		$table = $this->actions_table();

		$time = $this->db()->get_var(
			"SELECT MAX(last_attempt_gmt)
			 FROM {$table}
			 WHERE status = 'complete'
			   AND last_attempt_gmt != '0000-00-00 00:00:00'"
		);

		if ( ! $time ) {
			return null;
		}

		return strtotime( $time . ' UTC' ) ?: null;
	}

	/**
	 * Finds RUNNING jobs whose last_attempt_gmt is older than a threshold.
	 *
	 * These are jobs where a task process likely crashed (OOM, host timeout)
	 * before scheduling the next task. Returns action_id and hook for each.
	 *
	 * @since 1.12.0
	 *
	 * @param int $threshold_minutes How many minutes without a heartbeat before
	 *                               a running job is considered stuck. Default 5.
	 *
	 * @return object[] Array of objects with action_id and hook properties.
	 */
	public function get_stuck_running_jobs( int $threshold_minutes = 5 ): array {
		$table     = $this->actions_table();
		$group_tbl = $this->action_groups_table();

		$cutoff = gmdate( 'Y-m-d H:i:s', time() - ( $threshold_minutes * 60 ) );

		$results = $this->db()->get_results(
			$this->db()->prepare(
				"SELECT a.action_id, a.hook
				 FROM {$table} a
				 INNER JOIN {$group_tbl} g ON a.group_id = g.group_id
				 WHERE g.slug = %s
				   AND a.status = %s
				   AND a.last_attempt_gmt < %s",
				self::GROUP_ID,
				self::STATUS_RUNNING,
				$cutoff
			)
		);

		return is_array( $results ) ? $results : [];
	}

	/**
	 * Checks whether any jobs are currently running.
	 *
	 * Uses AS's native store API with a per_page limit of 1 for efficiency.
	 *
	 * @since 1.12.0
	 *
	 * @return bool True if at least one running job exists.
	 */
	public function has_running_jobs(): bool {
		$running = as_get_scheduled_actions(
			[
				'status'   => self::STATUS_RUNNING,
				'group'    => self::GROUP_ID,
				'per_page' => 1,
			]
		);

		return ! empty( $running );
	}

	/**
	 * Runs a job instances query, keyed by action ID like as_get_scheduled_actions().
	 *
	 * Action Scheduler defines no `paused` status, and its legacy post store throws an
	 * InvalidArgumentException on any status it does not know. Queries asking for paused
	 * instances are therefore served from this store's own tables, where the status lives.
	 *
	 * @since 1.30.0
	 *
	 * @param array $query Query args.
	 *
	 * @return ActionScheduler_Action[] Instances keyed by action ID.
	 */
	public function get_instances( array $query ): array {
		$statuses = array_filter( (array) ( $query['status'] ?? [] ) );

		if ( ! in_array( self::STATUS_PAUSED, $statuses, true ) ) {
			return (array) as_get_scheduled_actions( $query );
		}

		if ( ! ActionScheduler::is_initialized( __FUNCTION__ ) ) {
			return [];
		}

		// The two branches must agree on everything but the store, so mirror what
		// as_get_scheduled_actions() does to the query before handing it over: date strings
		// become DateTime objects, and the caller's group is left alone.
		foreach ( [ 'date', 'modified' ] as $key ) {
			if ( isset( $query[ $key ] ) ) {
				$query[ $key ] = as_get_datetime_object( $query[ $key ] );
			}
		}

		$instances = [];

		foreach ( (array) parent::query_actions( $query ) as $action_id ) {
			$instances[ (int) $action_id ] = $this->fetch_action( (int) $action_id );
		}

		return $instances;
	}

	/**
	 * Gets default instances query args for the job instances retrieval functions.
	 *
	 * @since 1.12.0
	 *
	 * @param string $job_name The job name.
	 * @param string $status The job run status.
	 *
	 * @return array
	 *
	 * @see ActionScheduler_Store for all possible job instance statuses.
	 */
	private function default_instances_query( string $job_name, string $status = '' ): array {

		/**
		 * Modifies the job instances query defaults.
		 *
		 * @since 1.12.0
		 *
		 * @param array $defaults Retrieval defaults.
		 */
		return apply_filters(
			'gk/foundation/scheduler/job/instances/query/default',
			[
				'hook'             => $job_name,
				'date'             => null,
				'date_compare'     => '<=',
				'modified'         => null,
				'modified_compare' => '<=',
				'group'            => self::GROUP_ID,
				'status'           => $status,
				'claimed'          => null,
				'per_page'         => 100,
				'offset'           => 0,
				'orderby'          => 'date',
				'order'            => 'ASC',
			]
		);
	}


	/**
	 * Checks whether all required AS tables exist.
	 *
	 * Uses global $wpdb directly (not $this->db()) to avoid recursion.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public static function tables_exist(): bool {
		global $wpdb;

		$found = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}actionscheduler%'" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		foreach ( self::REQUIRED_TABLES as $table ) {
			if ( ! in_array( $wpdb->prefix . $table, $found, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Restores missing Action Scheduler tables via dbDelta.
	 *
	 * Resets AS schema version options so AS's own migration system
	 * also recognizes the need for recreation, then runs dbDelta via
	 * AS's register_tables() method.
	 *
	 * @since 1.12.0
	 *
	 * @return bool Whether recovery succeeded.
	 */
	public static function recover_tables(): bool {
		if ( ! class_exists( 'ActionScheduler_StoreSchema' ) || ! class_exists( 'ActionScheduler_LoggerSchema' ) ) {
			return false;
		}

		try {
			delete_option( 'schema-ActionScheduler_StoreSchema' );
			delete_option( 'schema-ActionScheduler_LoggerSchema' );

			$store_schema  = new \ActionScheduler_StoreSchema();
			$logger_schema = new \ActionScheduler_LoggerSchema();
			$store_schema->register_tables( true );
			$logger_schema->register_tables( true );

			self::$tables_verified = true;
			self::$recovery_failed = false;

			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Detects missing AS tables and schedules early recovery at init p0.
	 *
	 * Call this during plugins_loaded (before init fires) so tables are
	 * recreated before AS's own init at priority 1 queries them. This
	 * prevents AS's internal SQL errors, not just Foundation's.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public static function schedule_early_recovery(): void {
		// Reset table verification when switching blogs in multisite.
		static $hook_registered = false;

		if ( ! $hook_registered ) {
			add_action( 'switch_blog', [ __CLASS__, 'clear_table_cache' ] );
			$hook_registered = true;
		}

		if ( self::tables_exist() ) {
			self::$tables_verified = true;

			return;
		}

		// Clear AS schema options now so AS's register_tables() also re-runs.
		delete_option( 'schema-ActionScheduler_StoreSchema' );
		delete_option( 'schema-ActionScheduler_LoggerSchema' );

		add_action(
			'init',
			static function () {
				self::recover_tables();
			},
			0
		);
	}

	/**
	 * Resets the table verification flags so the next db() call
	 * re-checks via SHOW TABLES.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public static function clear_table_cache(): void {
		self::$tables_verified = false;
		self::$recovery_failed = false;
	}

	/**
	 * Gets wpdb instance, ensuring AS tables exist first.
	 *
	 * @since  1.12.0
	 *
	 * @return wpdb
	 */
	protected function db() {
		if ( ! $this->db ) {
			global $wpdb;
			$this->db = $wpdb;
		}

		if ( ! self::$tables_verified && ! self::$recovery_failed ) {
			if ( self::tables_exist() ) {
				self::$tables_verified = true;
			} elseif ( ! self::recover_tables() ) {
				self::$recovery_failed = true;
			}
		}

		return $this->db;
	}
}
