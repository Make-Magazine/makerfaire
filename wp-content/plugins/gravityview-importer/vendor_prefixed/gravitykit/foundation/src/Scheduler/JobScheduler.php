<?php
/**
 * GravityKit Job Scheduler.
 * Main entry point for background job scheduling. Uses Action Scheduler under the hood.
 * *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler;

use Exception;
use GravityKit\GravityImport\Foundation\Scheduler\Handlers\JobHandler;
use GravityKit\GravityImport\Foundation\Scheduler\Handlers\RequestHandler;
use GravityKit\GravityImport\Foundation\Scheduler\Handlers\JobHistoryHandler;
use GravityKit\GravityImport\Foundation\Scheduler\Handlers\ScheduleHandler;
use GravityKit\GravityImport\Foundation\Scheduler\Models\HealthCheck;
use GravityKit\GravityImport\Foundation\Core;
use GravityKit\GravityImport\Foundation\Scheduler\Notices\ExecutionNotice;
use GravityKit\GravityImport\Foundation\Scheduler\Store\DbStore;
use GravityKit\GravityImport\Foundation\Scheduler\Traits\LoggerTrait;
use GravityKit\GravityImport\Foundation\Helpers\WP;
use GravityKit\GravityImport\Foundation\Settings\Framework as SettingsFramework;

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
	 * @since 1.12.0
	 *
	 * @return void
	 */
	private function register_loopback_url_override(): void {
		$get_base_url = static function () {
			$saved = (string) SettingsFramework::get_instance()->get_plugin_setting( Core::ID, 'scheduler_loopback_url', '' );

			/**
			 * Filters the base URL used for all loopback requests.
			 *
			 * Return a full base URL (e.g. `http://host.docker.internal:8896`) to override the
			 * default WordPress site URL used for loopback connections by Foundation, Action Scheduler,
			 * and WP-Cron.
			 *
			 * @since 1.12.0
			 *
			 * @param string $base_url The base URL for loopback requests. Default: saved setting value.
			 */
			return (string) apply_filters( 'gk/foundation/scheduler/loopback-base-url', $saved );
		};

		$replace_url = static function ( string $original_url, string $base_url ): string {
			$base_url = rtrim( $base_url, '/' );
			$path     = (string) wp_parse_url( $original_url, PHP_URL_PATH );
			$query    = wp_parse_url( $original_url, PHP_URL_QUERY );

			return $base_url . $path . ( $query ? '?' . $query : '' );
		};

		$override_url = static function ( $url ) use ( $get_base_url, $replace_url ) {
			$base_url = $get_base_url();

			return $base_url ? $replace_url( (string) $url, $base_url ) : $url;
		};

		// Foundation RequestHandler async dispatch (filter built dynamically in WP_Async_Request::get_query_url()).
		add_filter( 'gravitykit_async_request_query_url', $override_url );

		// Action Scheduler async queue runner.
		add_filter( 'as_async_request_queue_runner_query_url', $override_url );

		// WP-Cron spawn request.
		add_filter(
			'cron_request',
			static function ( $cron_request ) use ( $get_base_url, $replace_url ) {
				$base_url = $get_base_url();

				if ( $base_url ) {
					$cron_request['url'] = $replace_url( $cron_request['url'], $base_url );
				}

				return $cron_request;
			}
		);
	}

}
