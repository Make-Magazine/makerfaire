<?php
/**
 * GravityKit Job Scheduler.
 * Main entry point for background job scheduling. Uses Action Scheduler under the hood.
 * */

namespace GravityKit\GravityView\Foundation\Scheduler;

use Exception;
use Throwable;
use GravityKit\GravityView\Foundation\Scheduler\Handlers\JobHandler;
use GravityKit\GravityView\Foundation\Scheduler\Handlers\RequestHandler;
use GravityKit\GravityView\Foundation\Scheduler\Handlers\JobHistoryHandler;
use GravityKit\GravityView\Foundation\Scheduler\Handlers\ScheduleHandler;
use GravityKit\GravityView\Foundation\Scheduler\Models\HealthCheck;
use GravityKit\GravityView\Foundation\Scheduler\Models\NextRunRules;
use GravityKit\GravityView\Foundation\Scheduler\Models\Task;
use GravityKit\GravityView\Foundation\Core;
use GravityKit\GravityView\Foundation\Scheduler\Notices\ExecutionNotice;
use GravityKit\GravityView\Foundation\Scheduler\Store\DbStore;
use GravityKit\GravityView\Foundation\Scheduler\Traits\LoggerTrait;
use GravityKit\GravityView\Foundation\Helpers\WP;
use GravityKit\GravityView\Foundation\Settings\Framework as SettingsFramework;

class JobScheduler {
	use LoggerTrait;

	/**
	 * Class instance.
	 *
	 * @since 1.12.0
	 *
	 * @var JobScheduler|null
	 */
	private static $instance;

	/**
	 * DbStore object.
	 *
	 * @since 1.12.0
	 *
	 * @var DbStore|null
	 * */
	protected $store;

	/**
	 * Schedule handler object.
	 *
	 * @since 1.12.0
	 *
	 * @var ScheduleHandler|null
	 * */
	protected $schedule_handler;

	/**
	 * RequestHandler object.
	 *
	 * @since 1.12.0
	 *
	 * @var RequestHandler|null
	 * */
	protected $request_handler;

	/**
	 * Jobs registered flag.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 * */
	protected $jobs_registered = false;

	/**
	 * Class constructor.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	private function __construct() {
		// Register all pending actions in WordPress.
		add_action( 'action_scheduler_before_execute', [ $this, 'register_actions' ], 5 );

		// Clean up scheduler data when all GravityKit plugins are deactivated.
		add_action( 'update_option_active_plugins', [ $this, 'maybe_cleanup_on_deactivation' ], 10, 2 );

		// Detect missing AS tables now (plugins_loaded p100) and schedule
		// recovery at init p0 — before AS's own init at p1 queries them.
		DbStore::schedule_early_recovery();

		$this->request_handler = new RequestHandler();

		( new ExecutionNotice() )->register();

		$this->register_loopback_url_override();

		// Register the task sentinel check for dead process detection.
		$this->manager()->register_sentinel_check();
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.12.0
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Inits all registered jobs in WordPress.
	 *
	 * Skips registration when the current AS action does not belong to a GK group,
	 * avoiding unnecessary DB queries for non-GK actions.
	 *
	 * @since 1.12.0
	 *
	 * @param int $action_id The Action Scheduler action ID about to execute.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function register_actions( int $action_id = 0 ): void {
		// Skip registration for non-GK actions to avoid unnecessary DB queries.
		if ( $action_id && ! $this->is_gk_action( $action_id ) ) {
			return;
		}

		if ( $this->jobs_registered ) {
			// Callbacks were already registered, but a new GK action may have been
			// created after the initial registration (e.g., AS auto-rescheduled a
			// recurring job in the same batch). Ensure its hook is registered.
			$this->manager()->ensure_action_registered( $action_id );

			return;
		}

		$this->manager()->register_actions();
		$this->jobs_registered = true;
	}

	/**
	 * Checks whether an Action Scheduler action belongs to a GravityKit group.
	 *
	 * @since 1.12.0
	 *
	 * @param int $action_id The Action Scheduler action ID.
	 *
	 * @return bool
	 */
	protected function is_gk_action( int $action_id ): bool {
		try {
			$action = $this->store()->fetch_action( $action_id );
			$group  = $action->get_group();

			return in_array( $group, [ DbStore::GROUP_ID, DbStore::TASK_GROUP_ID ], true );
		} catch ( \Throwable $e ) {
			// If we can't determine the group, register defensively.
			return true;
		}
	}

	/**
	 * Whether background processing is enabled.
	 *
	 * The setting value can be overridden using the {@see 'gk/foundation/scheduler/enabled'} filter.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public static function is_enabled(): bool {
		$enabled = (bool) SettingsFramework::get_instance()->get_plugin_setting( Core::ID, 'background_processing', 1 );

		/**
		 * Overrides whether background processing is enabled.
		 *
		 * @since 1.12.0
		 *
		 * @param bool $enabled Whether background processing is enabled. Default: value of the "Background Processing" setting.
		 */
		return (bool) apply_filters( 'gk/foundation/scheduler/enabled', $enabled );
	}

	/**
	 * Whether the scheduler can dispatch jobs asynchronously via loopback.
	 *
	 * Returns true only when the scheduler is enabled AND the loopback
	 * dispatch mechanism works. ALTERNATE_WP_CRON (inline execution)
	 * returns false — it is not true background processing.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $fresh Whether to bypass the cache and run a fresh loopback probe. Default false.
	 *
	 * @return bool
	 */
	public function can_dispatch( bool $fresh = false ): bool {
		if ( ! self::is_enabled() ) {
			return false;
		}

		try {
			$health = $this->health( $fresh );

			return ! $health->has_failure() && ! $health->is_loopback_blocked();
		} catch ( \Throwable $e ) {
			return false;
		}
	}

	/**
	 * Returns the scheduler health check result.
	 *
	 * Evaluates loopback connectivity and WP-Cron configuration
	 * to determine whether the scheduler has a viable execution path.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $fresh Whether to bypass the cache and run a fresh probe. Default false.
	 *
	 * @return HealthCheck
	 */
	public function health( bool $fresh = false ): HealthCheck {
		if ( $fresh ) {
			HealthCheck::flush();
		}

		return HealthCheck::run();
	}

	/**
	 * Gets the Job Handler.
	 *
	 * @return JobHandler Job Handler object.
	 */
	public function job(): JobHandler {
		return new JobHandler( $this->manager(), $this->store() );
	}

	/**
	 * Gets the job history handler object.
	 *
	 * @since 1.12.0
	 *
	 * @param string $job_name The job name.
	 *
	 * @return JobHistoryHandler Runs handler object.
	 */
	public function history( string $job_name ): JobHistoryHandler {
		return new JobHistoryHandler( $job_name, $this->store() );
	}

	/**
	 * Gets the SchedulerStore object.
	 *
	 * @since 1.12.0
	 *
	 * @return DbStore
	 */
	public function store(): DbStore {
		if ( ! $this->store ) {
			$this->store = DbStore::get_instance();
		}

		return $this->store;
	}

	/**
	 * Gets the RequestHandler object.
	 *
	 * @since 1.12.0
	 *
	 * @return RequestHandler
	 */
	public function request(): RequestHandler {
		return $this->request_handler;
	}

	/**
	 * Gets the job manager for scheduling, executing, and controlling job lifecycle.
	 *
	 * @since 1.12.0
	 *
	 * @return ScheduleHandler
	 */
	public function manager(): ScheduleHandler {
		if ( ! $this->schedule_handler ) {
			$this->schedule_handler = new ScheduleHandler( $this->store(), $this->request() );
		}

		return $this->schedule_handler;
	}

	/**
	 * Checks whether a task callback should keep working, or stop and checkpoint.
	 *
	 * Call it in loops or before expensive operations. Returns true when no
	 * deadline is set, so tasks work without time budget enforcement.
	 *
	 * @since 1.16.0
	 * @since 1.29.0 Stops when memory runs low, not only when time does.
	 *
	 * @param array $args   The task args. The deadline and the memory baseline
	 *                      live in `$args['_meta']`; the memory check only runs
	 *                      when the executor recorded both there.
	 * @param int   $margin Seconds before the deadline to stop. Default: 2.
	 *
	 * @return bool True if the task should keep working.
	 */
	public static function should_continue( array $args, int $margin = 2 ): bool {
		$meta = $args[ Task::META_KEY ] ?? null;
		$meta = is_array( $meta ) ? $meta : [];

		$deadline = $meta['deadline'] ?? null;

		// Only the executor sets a deadline, and only a task it started has a
		// rerun for a stop to checkpoint into. Without one, saying stop would
		// end the work rather than resume it.
		if ( null === $deadline ) {
			return true;
		}

		// Memory can run out long before the deadline (`SAVEQUERIES` alone can
		// fill a request), and exhausting the limit abandons work a checkpoint
		// would have saved.
		$baseline = $meta['memory_baseline'] ?? null;

		if ( is_numeric( $baseline ) && ! self::has_memory_headroom( max( 0.0, (float) $baseline ) ) ) {
			return false;
		}

		return microtime( true ) < ( (float) $deadline - max( 0, $margin ) );
	}

	/**
	 * Whether enough memory remains to keep working in this process.
	 *
	 * Reports headroom when the limit cannot be read, so a check that cannot
	 * answer never stops work. An unlimited limit is an answer rather than a
	 * silence, and is measured against a ceiling: nothing caps the process, but
	 * the kernel still will, and it does not checkpoint first.
	 *
	 * @since 1.29.0
	 *
	 * @param float $baseline Bytes allocated when the task started.
	 *
	 * @return bool Whether the task should keep working.
	 */
	private static function has_memory_headroom( float $baseline ): bool {
		$limit = self::memory_limit_bytes();

		// 0 means the limit could not be read, which is not the same as knowing
		// there is none: without a number there is nothing to be a share of.
		if ( 0 === $limit ) {
			return true;
		}

		if ( $limit < 0 ) {
			/**
			 * Modifies the ceiling a task is measured against when PHP has no
			 * memory limit of its own.
			 *
			 * @since 1.29.0
			 *
			 * @param int $ceiling Bytes.
			 */
			$ceiling = 32 * 1024 * 1024 * 1024;

			if ( function_exists( 'apply_filters' ) ) {
				$ceiling = apply_filters( 'gk/foundation/scheduler/unlimited-memory-ceiling', $ceiling );
			}

			// A ceiling that is not a positive finite number turns the check
			// off rather than becoming a limit every task instantly exceeds.
			$ceiling = is_numeric( $ceiling ) ? (float) $ceiling : 0.0;

			if ( ! is_finite( $ceiling ) || $ceiling <= 0 ) {
				return true;
			}

			$limit = $ceiling >= (float) PHP_INT_MAX ? PHP_INT_MAX : (int) $ceiling;
		}

		/**
		 * Modifies the share of the memory limit a task may use before it is
		 * told to stop and checkpoint.
		 *
		 * Defaults to 0.9, matching Action Scheduler's own batch cutoff so a
		 * stopped task is never rerun by the same already-full process.
		 *
		 * @since 1.29.0
		 *
		 * @param float $threshold Share of the limit, between 0 and 1 exclusive.
		 */
		$threshold = 0.9;

		if ( function_exists( 'apply_filters' ) ) {
			$filtered  = apply_filters( 'gk/foundation/scheduler/memory-threshold', $threshold );
			$threshold = is_numeric( $filtered ) ? (float) $filtered : $threshold;
		}

		// Outside this range the check would stop every task immediately, or
		// never stop one. NAN is rejected explicitly: every comparison against it
		// is false, so it passes the range check and then makes the memory
		// comparison below false too, stopping every task.
		if ( ! is_finite( $threshold ) || $threshold <= 0 || $threshold >= 1 ) {
			$threshold = 0.9;
		}

		$cutoff = $limit * $threshold;

		// Stopping only helps when a rerun would start lower than the process
		// is now. Past-the-cutoff before the task did anything means a fresh
		// process starts there too; stopping would checkpoint in place until
		// the no-progress watchdog fails the task, so let it run instead.
		if ( $baseline >= $cutoff ) {
			return true;
		}

		// `true` reports memory actually allocated from the system, which is
		// what the limit is enforced against.
		return memory_get_usage( true ) < $cutoff;
	}

	/**
	 * Returns this process's memory limit in bytes.
	 *
	 * Resolves without WordPress loaded.
	 *
	 * @since 1.29.0
	 *
	 * @return int Bytes, -1 when PHP has no limit, or 0 when it cannot be read.
	 */
	private static function memory_limit_bytes(): int {
		// Hosts can disable ini_get(), and this runs inside the loop every task
		// calls, so an unguarded call would fatal every job on such a host.
		if ( ! function_exists( 'ini_get' ) ) {
			return 0;
		}

		$raw = trim( (string) ini_get( 'memory_limit' ) );

		if ( '' === $raw ) {
			return 0;
		}

		if ( '-1' === $raw ) {
			return -1;
		}

		if ( function_exists( 'wp_convert_hr_to_bytes' ) ) {
			return (int) wp_convert_hr_to_bytes( $raw );
		}

		// PHP's shorthand: a number with an optional G, M or K suffix.
		$value = (int) $raw;
		$unit  = strtolower( substr( $raw, -1 ) );

		if ( 'g' === $unit ) {
			return $value * 1024 * 1024 * 1024;
		}

		if ( 'm' === $unit ) {
			return $value * 1024 * 1024;
		}

		if ( 'k' === $unit ) {
			return $value * 1024;
		}

		return $value;
	}

	/**
	 * Creates a NextRunRules object to checkpoint and continue in a new execution.
	 *
	 * Convenience factory for the common pattern of returning a rerun with
	 * updated args. Pass only the keys that changed (e.g., offset); existing
	 * args are merged automatically by the scheduler.
	 *
	 * Resolves through the winning Foundation instance so the returned object
	 * lives in the same namespace as the Task that will consume it, even when
	 * multiple vendored Foundation copies coexist.
	 *
	 * @since 1.16.0
	 *
	 * @param array $next_args Keys to merge for the next execution (e.g., `['offset' => 500]`).
	 *
	 * @return NextRunRules
	 */
	public static function checkpoint( array $next_args = [] ): NextRunRules {
		$rules = new NextRunRules();
		$rules->rerun( true );

		if ( ! empty( $next_args ) ) {
			$rules->set_next_task_args( $next_args );
		}

		return $rules;
	}

	/**
	 * Checkpoints with both updated task args and shared job data.
	 *
	 * Like `checkpoint()`, but also updates the job-level data shared across
	 * all tasks in the job. Use this when a task needs to both save its own
	 * progress (e.g., offset) and pass results to downstream tasks (e.g.,
	 * processed count, generated file path).
	 *
	 * @since 1.16.0
	 *
	 * @param array $next_args Keys to merge into task args for the next execution.
	 * @param array $job_data  Job-level shared data; replaces the stored array.
	 *
	 * @return NextRunRules
	 */
	public static function checkpoint_with_data( array $next_args, array $job_data ): NextRunRules {
		$rules = self::checkpoint( $next_args );
		$rules->set_job_data( $job_data );

		return $rules;
	}

	/**
	 * Creates a NextRunRules object that marks the task as finished.
	 *
	 * Counterpart to `checkpoint()` for the completion path. The scheduler
	 * treats a non-null return with rerun disabled as task completion, so
	 * `return GravityKitFoundation::scheduler()->complete();` is equivalent
	 * to `return null;` — use it as the base for `complete_with_data()` or
	 * when chaining `set_next_task_args()` to pass args to the next task.
	 *
	 * Resolves through the winning Foundation instance so the returned object
	 * lives in the same namespace as the Task that will consume it, even when
	 * multiple vendored Foundation copies coexist. Never construct
	 * `new NextRunRules()` directly in a plugin for this reason.
	 *
	 * @since 1.29.0
	 *
	 * @return NextRunRules
	 */
	public static function complete(): NextRunRules {
		$rules = new NextRunRules();
		$rules->rerun( false );

		return $rules;
	}

	/**
	 * Completes the task while storing job-level shared data.
	 *
	 * Use this when a task is done and needs to pass results to downstream
	 * tasks (processed count, generated file path). The task is marked
	 * completed — rerun stays disabled — and the array replaces the job-level
	 * shared data, which the scheduler persists for the remaining tasks. To
	 * keep existing keys, merge them in: `complete_with_data( array_merge(
	 * (array) $job_data, [ 'file' => $path ] ) )`.
	 *
	 * @since 1.29.0
	 *
	 * @param array $job_data The job-level shared data to store.
	 *
	 * @return NextRunRules
	 */
	public static function complete_with_data( array $job_data ): NextRunRules {
		$rules = self::complete();
		$rules->set_job_data( $job_data );

		return $rules;
	}

	/**
	 * Cleans up all scheduler data when no GravityKit plugins remain active.
	 *
	 * Hooked into `update_option_active_plugins` which fires after WordPress
	 * persists the active plugins list. This reliably catches both single
	 * and bulk plugin deactivation.
	 *
	 * @since 1.12.0
	 *
	 * @param array $old_value Previously active plugins.
	 * @param array $new_value Currently active plugins.
	 *
	 * @return void
	 */
	public function maybe_cleanup_on_deactivation( $old_value, $new_value ): void {
		$old_value = (array) $old_value;
		$new_value = (array) $new_value;

		// Only act when plugins are removed (deactivation), not added.
		if ( count( $new_value ) >= count( $old_value ) ) {
			return;
		}

		$core = Core::get_instance();

		// @phpstan-ignore-next-line get_instance() can return null before init.
		if ( ! $core ) {
			return;
		}

		// Only consider plugins that bundle Foundation (loads_foundation = true).
		// Piggy-backing plugins (e.g., Multiple Forms using GravityView's Foundation)
		// can't provide the scheduler on their own.
		$foundation_providers = array_filter(
			$core->get_registered_plugins(),
			static function ( $plugin ) {
				return ! empty( $plugin['loads_foundation'] );
			}
		);

		// Registered plugins are keyed by absolute paths (__FILE__), but
		// WordPress stores active plugins as relative paths (e.g. "gravityview/gravityview.php").
		$provider_basenames = array_map( 'plugin_basename', array_keys( $foundation_providers ) );

		// If any Foundation-bundling plugin is still active, the scheduler is still needed.
		if ( array_intersect( $provider_basenames, $new_value ) ) {
			return;
		}

		$this->cleanup_scheduler_data();
	}

	/**
	 * Removes all pending, running, and paused scheduler actions from the database.
	 *
	 * Physically deletes action rows in both the `gravitykit` (parent jobs) and
	 * `gktask` (task execution + recovery) groups. Completed, failed, and canceled
	 * actions are left as historical records.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	protected function cleanup_scheduler_data(): void {
		try {
			$this->store()->delete_actions_by_groups(
				[ DbStore::GROUP_ID, DbStore::TASK_GROUP_ID ],
				[ DbStore::STATUS_PENDING, DbStore::STATUS_RUNNING, DbStore::STATUS_PAUSED ]
			);
		} catch ( \Throwable $e ) {
			$this->logger()->error(
				'Scheduler deactivation cleanup failed.',
				[ 'error' => $e->getMessage() ]
			);
		}

		// Remove scheduler transients.
		WP::delete_transient( 'gk_scheduler_loopback_failed' );
		WP::delete_transient( 'gk_scheduler_health_check' );

		// Remove the cron fallback event.
		wp_unschedule_hook( 'gk_scheduler_cron_fallback' );
	}

	/**
	 * Registers loopback URL override hooks for all components that make self-referencing HTTP requests.
	 *
	 * Reads the saved setting and passes it through a filter. When a non-empty base URL is returned,
	 * it overrides the loopback URL for Foundation HealthCheck, Foundation RequestHandler,
	 * Action Scheduler's async runner, and WP-Cron's spawn requests.
	 *
	 * The saved URL may embed HTTP Basic Auth credentials in RFC 3986 userinfo syntax
	 * (`https://user:pass@host.example.com`). Credentials are stripped from the URL
	 * and injected as an `Authorization` header on matching outbound requests, keeping
	 * credentials out of logs and any downstream URL handling.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	private function register_loopback_url_override(): void {
		$get_base_url_parts = static function (): array {
			$empty = [
				'clean_url'   => '',
				'host'        => '',
				'auth_header' => '',
			];

			$saved = (string) SettingsFramework::get_instance()->get_plugin_setting( Core::ID, 'scheduler_loopback_url', '' );

			/**
			 * Filters the base URL used for all loopback requests.
			 *
			 * Return a full base URL (e.g. `http://host.docker.internal:8896`) to override the
			 * default WordPress site URL used for loopback connections by Foundation, Action Scheduler,
			 * and WP-Cron. Credentials may be embedded (`https://user:pass@host`) for sites behind
			 * HTTP Basic Authentication (e.g. Flywheel staging).
			 *
			 * @since 1.12.0
			 *
			 * @param string $base_url The base URL for loopback requests. Default: saved setting value.
			 */
			$saved = (string) apply_filters( 'gk/foundation/scheduler/loopback-base-url', $saved );

			if ( '' === $saved ) {
				return $empty;
			}

			$parts = wp_parse_url( $saved );

			if ( ! is_array( $parts ) || empty( $parts['host'] ) ) {
				return $empty;
			}

			$scheme = $parts['scheme'] ?? 'https';
			$host   = $parts['host'];
			$port   = isset( $parts['port'] ) ? ':' . $parts['port'] : '';
			$user   = $parts['user'] ?? '';
			$pass   = $parts['pass'] ?? '';

			return [
				'clean_url'   => $scheme . '://' . $host . $port,
				'host'        => $host,
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Encoding HTTP Basic Auth credentials per RFC 7617; not obfuscation.
				'auth_header' => '' !== $user ? 'Basic ' . base64_encode( $user . ':' . $pass ) : '',
			];
		};

		$replace_url = static function ( string $original_url, string $base_url ): string {
			$base_url = rtrim( $base_url, '/' );
			$path     = (string) wp_parse_url( $original_url, PHP_URL_PATH );
			$query    = wp_parse_url( $original_url, PHP_URL_QUERY );

			return $base_url . $path . ( $query ? '?' . $query : '' );
		};

		$override_url = static function ( $url ) use ( $get_base_url_parts, $replace_url ) {
			$parts = $get_base_url_parts();

			return $parts['clean_url'] ? $replace_url( (string) $url, $parts['clean_url'] ) : $url;
		};

		// Foundation RequestHandler async dispatch (filter built dynamically in WP_Async_Request::get_query_url()).
		add_filter( 'gravitykit_async_request_query_url', $override_url );

		// Action Scheduler async queue runner.
		add_filter( 'as_async_request_queue_runner_query_url', $override_url );

		// WP-Cron spawn request.
		add_filter(
			'cron_request',
			static function ( $cron_request ) use ( $get_base_url_parts, $replace_url ) {
				$parts = $get_base_url_parts();

				if ( '' === $parts['clean_url'] ) {
					return $cron_request;
				}

				$cron_request['url'] = $replace_url( $cron_request['url'], $parts['clean_url'] );

				if ( '' !== $parts['auth_header'] ) {
					if ( ! isset( $cron_request['args']['headers'] ) || ! is_array( $cron_request['args']['headers'] ) ) {
						$cron_request['args']['headers'] = [];
					}

					$cron_request['args']['headers']['Authorization'] = $parts['auth_header'];
				}

				return $cron_request;
			}
		);

		// Inject Basic Auth header on outbound requests targeting the configured
		// loopback host. Only registered when the saved URL actually has
		// credentials — `http_request_args` fires on every outbound HTTP call
		// (feeds, updates, REST), so the filter stays off the critical path
		// when Basic Auth isn't configured.
		try {
			$initial_parts = $get_base_url_parts();
		} catch ( Throwable $e ) {
			// Settings unavailable during init (e.g., in isolated test runs).
			// Skip registration; the URL-override filters above already bail
			// safely when settings can't be read at filter-fire time.
			return;
		}

		if ( '' === $initial_parts['auth_header'] ) {
			return;
		}

		add_filter(
			'http_request_args',
			static function ( $args, $url ) use ( $get_base_url_parts ) {
				$parts = $get_base_url_parts();

				// Filter reads settings fresh on every invocation; guard the
				// runtime case where the loopback URL lost its credentials
				// after this filter was registered.
				// @phpstan-ignore-next-line PHPStan narrows auth_header to non-empty from the registration-time guard, but each call re-evaluates the setting.
				if ( '' === $parts['auth_header'] || '' === $parts['host'] ) {
					return $args;
				}

				$request_host = wp_parse_url( (string) $url, PHP_URL_HOST );

				if ( $request_host !== $parts['host'] ) {
					return $args;
				}

				if ( ! isset( $args['headers'] ) || ! is_array( $args['headers'] ) ) {
					$args['headers'] = [];
				}

				$args['headers']['Authorization'] = $parts['auth_header'];

				return $args;
			},
			10,
			2
		);
	}
}
