<?php
/**
 * GravityKit Job Scheduler.
 * Main entry point for background job scheduling. Uses Action Scheduler under the hood.
 * *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

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
	 * Checks whether a task callback should continue processing within its time budget.
	 *
	 * Compares the current wall-clock time against the task's injected deadline.
	 * Call this in loops or before expensive operations to support cooperative
	 * time budgeting. Returns true (keep going) when no deadline is set, so
	 * tasks work correctly even without time budget enforcement.
	 *
	 * @since 1.16.0
	 *
	 * @param array $args   The task args (deadline lives in `$args['_meta']['deadline']`).
	 * @param int   $margin Seconds before the deadline to stop. Default: 2.
	 *
	 * @return bool True if there is still time remaining.
	 */
	public static function should_continue( array $args, int $margin = 2 ): bool {
		$deadline = $args[ Task::META_KEY ]['deadline'] ?? null;

		if ( null === $deadline ) {
			return true;
		}

		return microtime( true ) < ( (float) $deadline - max( 0, $margin ) );
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
	 * @param array $job_data  Keys to merge into job-level shared data.
	 *
	 * @return NextRunRules
	 */
	public static function checkpoint_with_data( array $next_args, array $job_data ): NextRunRules {
		$rules = self::checkpoint( $next_args );
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
