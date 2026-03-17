<?php
/**
 * Scheduler infrastructure diagnostics.
 *
 * Provides health checks for queue runner, WP-Cron, loopback,
 * recovery, PHP limits, and last activity. Used by the Background
 * Jobs UI and WordPress Site Health.
 *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler\Overview;

use GravityKit\GravityImport\Foundation\Helpers\WP;
use GravityKit\GravityImport\Foundation\Scheduler\Store\DbStore;
use GravityKit\GravityImport\Foundation\Scheduler\Models\HealthCheck;
use GravityKit\GravityImport\Foundation\Scheduler\Handlers\TaskExecutor;

class Diagnostics {

	/**
	 * Transient key for cached diagnostics rows.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const CACHE_KEY = 'gk_scheduler_diagnostics';

	/**
	 * Cache TTL in seconds (5 minutes).
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	const CACHE_TTL = 300;

	/**
	 * Scheduler store instance.
	 *
	 * @since 1.12.0
	 *
	 * @var DbStore
	 */
	private $store;

	/**
	 * Initialises the diagnostics collector.
	 *
	 * @since 1.12.0
	 *
	 * @param DbStore $store Scheduler data store.
	 */
	public function __construct( DbStore $store ) {
		$this->store = $store;
	}

	/**
	 * Computes fresh diagnostics rows and caches the result.
	 *
	 * @since 1.12.0
	 *
	 * @return array[] Array of rows, each with 'key', 'label', 'value', and 'status' (ok|warning|error|neutral).
	 */
	public function get_rows(): array {
		// Single probe — avoids redundant loopback HTTP requests.
		$health = HealthCheck::run();

		$rows = [
			$this->diagnose_queue_runner(),
			$this->diagnose_wp_cron( $health ),
			$this->diagnose_loopback( $health ),
			$this->diagnose_recovery(),
			$this->diagnose_php_limits(),
			$this->diagnose_time_budget(),
			$this->diagnose_last_activity(),
		];

		WP::set_transient( self::CACHE_KEY, $rows, self::CACHE_TTL );

		return $rows;
	}

	/**
	 * Returns cached diagnostics rows, or null if the cache is empty/expired.
	 *
	 * @since 1.12.0
	 *
	 * @return array[]|null
	 */
	public static function cached(): ?array {
		$rows = WP::get_transient( self::CACHE_KEY );

		return is_array( $rows ) ? $rows : null;
	}

	/**
	 * Static factory — creates its own DbStore and computes fresh rows.
	 *
	 * @since 1.12.0
	 *
	 * @return array[]
	 */
	public static function collect(): array {
		return ( new self( DbStore::get_instance() ) )->get_rows();
	}

	/**
	 * Registers the Site Health debug information section.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public static function register_site_health(): void {
		add_filter( 'debug_information', [ static::class, 'add_site_health_section' ] );
	}

	/**
	 * Adds a "GravityKit Background Processing" section to Site Health Info.
	 *
	 * Skips PHP limits since those are already shown in the Server section
	 * (time_limit, memory_limit).
	 *
	 * @since 1.12.0
	 *
	 * @param array $info Site Health debug information sections.
	 *
	 * @return array
	 */
	public static function add_site_health_section( array $info ): array {
		$section_label = __( 'GravityKit Background Processing', 'gk-gravityimport' );

		try {
			$rows = self::cached() ?? self::collect();
		} catch ( \Throwable $e ) {
			$info['gk-scheduler'] = [
				'label'  => $section_label,
				'fields' => [
					'status' => [
						'label' => __( 'Status', 'gk-gravityimport' ),
						'value' => __( 'Unable to collect diagnostics.', 'gk-gravityimport' ),
					],
				],
			];

			return $info;
		}

		$fields = [];

		foreach ( $rows as $row ) {
			// Skip PHP limits — already in the Server section (time_limit, memory_limit).
			if ( 'php' === $row['key'] ) {
				continue;
			}

			$fields[ $row['key'] ] = [
				'label' => $row['label'],
				'value' => str_replace( "\n", ' | ', $row['value'] ),
			];
		}

		$info['gk-scheduler'] = [
			'label'  => $section_label,
			'fields' => $fields,
		];

		return $info;
	}

	/**
	 * Checks whether AS's queue runner is actively processing.
	 *
	 * @since 1.12.0
	 *
	 * @return array{key: string, label: string, value: string, status: string}
	 */
	private function diagnose_queue_runner(): array {
		$label = __( 'Queue Runner', 'gk-gravityimport' );

		// Check if there are active claims (GK actions being processed right now).
		$claim_count = $this->store->get_gk_claim_count();

		if ( $claim_count > 0 ) {
			return [
				'key'    => 'queue-runner',
				'label'  => $label,
				/* translators: [count]: the number of active action claims. */
				'value'  => strtr( _n( 'Processing ([count] claim)', 'Processing ([count] claims)', $claim_count, 'gk-gravityimport' ), [ '[count]' => $claim_count ] ),
				'status' => 'ok',
			];
		}

		// Check if AS's async-request-runner lock is held (recently checked for work).
		if ( class_exists( 'ActionScheduler' ) ) {
			$lock = \ActionScheduler::lock(); // @phpstan-ignore staticMethod.notFound (AS class, not in stubs)

			if ( $lock->is_locked( 'async-request-runner' ) ) {
				return [
					'key'    => 'queue-runner',
					'label'  => $label,
					'value'  => __( 'Recently active', 'gk-gravityimport' ),
					'status' => 'ok',
				];
			}
		}

		// Not currently running — check if there are pending actions that should be.
		$counts  = $this->store->action_counts();
		$pending = (int) ( $counts[ DbStore::STATUS_PENDING ] ?? 0 );
		$running = (int) ( $counts[ DbStore::STATUS_RUNNING ] ?? 0 );

		if ( $running > 0 ) {
			return [
				'key'    => 'queue-runner',
				'label'  => $label,
				/* translators: [count]: the number of stuck in-progress jobs. */
				'value'  => strtr( _n( 'Idle ([count] stuck)', 'Idle ([count] stuck)', $running, 'gk-gravityimport' ), [ '[count]' => $running ] ),
				'status' => 'warning',
			];
		}

		if ( $pending > 0 ) {
			return [
				'key'    => 'queue-runner',
				'label'  => $label,
				/* translators: [count]: the number of pending actions. */
				'value'  => strtr( _n( 'Idle ([count] pending)', 'Idle ([count] pending)', $pending, 'gk-gravityimport' ), [ '[count]' => $pending ] ),
				'status' => 'neutral',
			];
		}

		return [
			'key'    => 'queue-runner',
			'label'  => $label,
			'value'  => __( 'Idle', 'gk-gravityimport' ),
			'status' => 'neutral',
		];
	}

	/**
	 * Checks WP-Cron status.
	 *
	 * @since 1.12.0
	 *
	 * @param HealthCheck $health Pre-computed health check result.
	 *
	 * @return array{key: string, label: string, value: string, status: string}
	 */
	private function diagnose_wp_cron( HealthCheck $health ): array {
		$label          = __( 'WP-Cron', 'gk-gravityimport' );
		$cron_disabled  = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
		$alternate_cron = defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON;

		if ( $cron_disabled ) {
			$parts = [];

			$parts[] = 'DISABLE_WP_CRON: true';

			if ( $alternate_cron ) {
				$parts[] = 'ALTERNATE_WP_CRON: true';

				return [
					'key'    => 'wp-cron',
					'label'  => $label,
					'value'  => implode( "\n", $parts ),
					'status' => 'warning',
				];
			}

			$parts[] = 'ALTERNATE_WP_CRON: ' . __( 'not set', 'gk-gravityimport' );

			// Red only when loopback is also blocked (no execution path).
			// Yellow when loopback works (scheduler can dispatch directly).
			return [
				'key'    => 'wp-cron',
				'label'  => $label,
				'value'  => implode( "\n", $parts ),
				'status' => $health->is_loopback_blocked() ? 'error' : 'warning',
			];
		}

		if ( $alternate_cron ) {
			return [
				'key'    => 'wp-cron',
				'label'  => $label,
				'value'  => 'ALTERNATE_WP_CRON: true',
				'status' => 'warning',
			];
		}

		// Check next scheduled AS queue run.
		$next = wp_next_scheduled( 'action_scheduler_run_queue' );

		if ( $next ) {
			$diff = $next - time();

			if ( $diff > 0 ) {
				// translators: [time] is replaced with a human-readable time difference (e.g., "45 seconds").
				$value = strtr( __( 'OK (next run in [time])', 'gk-gravityimport' ), [ '[time]' => human_time_diff( time(), $next ) ] );
			} else {
				// translators: [time] is replaced with a human-readable time difference (e.g., "2 minutes").
				$value = strtr( __( 'Overdue by [time]', 'gk-gravityimport' ), [ '[time]' => human_time_diff( $next ) ] );
			}

			return [
				'key'    => 'wp-cron',
				'label'  => $label,
				'value'  => $value,
				'status' => $diff < -300 ? 'warning' : 'ok',
			];
		}

		return [
			'key'    => 'wp-cron',
			'label'  => $label,
			'value'  => __( 'Not scheduled', 'gk-gravityimport' ),
			'status' => 'warning',
		];
	}

	/**
	 * Checks loopback connectivity.
	 *
	 * @since 1.12.0
	 *
	 * @param HealthCheck $health Pre-computed health check result.
	 *
	 * @return array{key: string, label: string, value: string, status: string}
	 */
	private function diagnose_loopback( HealthCheck $health ): array {
		$label = __( 'Loopback', 'gk-gravityimport' );

		if ( ! $health->is_loopback_blocked() ) {
			return [
				'key'    => 'loopback',
				'label'  => $label,
				'value'  => __( 'OK', 'gk-gravityimport' ),
				'status' => 'ok',
			];
		}

		// Loopback is blocked. Severity depends on whether the scheduler still has a viable path.
		return [
			'key'    => 'loopback',
			'label'  => $label,
			'value'  => __( 'Blocked', 'gk-gravityimport' ),
			'status' => $health->has_failure() ? 'error' : 'warning',
		];
	}

	/**
	 * Checks the stuck-job recovery heartbeat.
	 *
	 * @since 1.12.0
	 *
	 * @return array{key: string, label: string, value: string, status: string}
	 */
	private function diagnose_recovery(): array {
		$label = __( 'Recovery', 'gk-gravityimport' );

		// Check last heartbeat from the DB.
		$last_heartbeat = $this->store->get_last_recovery_heartbeat();

		if ( ! $last_heartbeat ) {
			// No heartbeat — check if recovery is even scheduled.
			$next = as_next_scheduled_action( TaskExecutor::RECOVERY_HOOK, [], DbStore::TASK_GROUP_ID );

			if ( $next ) {
				return [
					'key'    => 'recovery',
					'label'  => $label,
					'value'  => __( 'Scheduled (no heartbeat yet)', 'gk-gravityimport' ),
					'status' => 'neutral',
				];
			}

			return [
				'key'    => 'recovery',
				'label'  => $label,
				'value'  => __( 'Inactive', 'gk-gravityimport' ),
				'status' => 'neutral',
			];
		}

		$ago = time() - $last_heartbeat;

		// translators: [time] is replaced with a human-readable time difference (e.g., "32 seconds").
		$value = strtr( __( 'Last heartbeat [time] ago', 'gk-gravityimport' ), [ '[time]' => human_time_diff( $last_heartbeat ) ] );

		// Recovery runs every 2 minutes. If >5 minutes since last heartbeat while jobs are active, warn.
		if ( $ago > 300 ) {
			$counts  = $this->store->action_counts();
			$running = (int) ( $counts[ DbStore::STATUS_RUNNING ] ?? 0 );

			if ( $running > 0 ) {
				return [
					'key'    => 'recovery',
					'label'  => $label,
					'value'  => $value . ' ' . __( '(stale)', 'gk-gravityimport' ),
					'status' => 'warning',
				];
			}
		}

		return [
			'key'    => 'recovery',
			'label'  => $label,
			'value'  => $value,
			'status' => 'ok',
		];
	}

	/**
	 * Checks PHP execution limits.
	 *
	 * @since 1.12.0
	 *
	 * @return array{key: string, label: string, value: string, status: string}
	 */
	private function diagnose_php_limits(): array {
		$label    = __( 'PHP', 'gk-gravityimport' );
		$max_exec = (int) ini_get( 'max_execution_time' );
		$memory   = (string) ini_get( 'memory_limit' );

		$parts = [];

		if ( $max_exec > 0 ) {
			$parts[] = 'max_execution_time: ' . $max_exec . 's';
		} else {
			$parts[] = 'max_execution_time: ' . __( 'unlimited', 'gk-gravityimport' );
		}

		$parts[] = 'memory_limit: ' . $memory;

		$status = 'neutral';

		// Warn if execution time is very low.
		if ( $max_exec > 0 && $max_exec < 15 ) {
			$status = 'warning';
		}

		return [
			'key'    => 'php',
			'label'  => $label,
			'value'  => implode( "\n", $parts ),
			'status' => $status,
		];
	}

	/**
	 * Shows the effective task time budget.
	 *
	 * @since 1.12.0
	 *
	 * @return array{key: string, label: string, value: string, status: string}
	 */
	private function diagnose_time_budget(): array {
		$budget = TaskExecutor::get_task_time_budget();

		return [
			'key'    => 'time-budget',
			'label'  => __( 'Task Time Budget', 'gk-gravityimport' ),
			'value'  => rtrim( rtrim( number_format( $budget, 1, '.', '' ), '0' ), '.' ) . 's',
			'status' => 'neutral',
		];
	}

	/**
	 * Shows when GravityKit and Action Scheduler last completed work.
	 *
	 * Two timestamps side-by-side let support distinguish "our jobs are
	 * stuck" (GK stale, AS recent) from "the whole engine is down" (both stale).
	 *
	 * Warns when GK jobs are overdue (pending and past their scheduled date
	 * for more than 10 minutes), indicating something is blocking execution.
	 *
	 * @since 1.12.0
	 *
	 * @return array{key: string, label: string, value: string, status: string}
	 */
	private function diagnose_last_activity(): array {
		$label = __( 'Last Activity', 'gk-gravityimport' );

		$gk_time = $this->store->get_last_activity_time();
		$as_time = $this->store->get_last_as_activity_time();

		$parts = [];

		if ( $gk_time ) {
			// translators: [time] is replaced with a human-readable time difference (e.g., "3 minutes").
			$parts[] = 'GravityKit: ' . strtr( __( '[time] ago', 'gk-gravityimport' ), [ '[time]' => human_time_diff( $gk_time ) ] );
		} else {
			$parts[] = 'GravityKit: ' . __( 'never', 'gk-gravityimport' );
		}

		if ( $as_time ) {
			// translators: [time] is replaced with a human-readable time difference (e.g., "45 seconds").
			$parts[] = 'Action Scheduler: ' . strtr( __( '[time] ago', 'gk-gravityimport' ), [ '[time]' => human_time_diff( $as_time ) ] );
		} else {
			$parts[] = 'Action Scheduler: ' . __( 'never', 'gk-gravityimport' );
		}

		$status        = 'neutral';
		$overdue_count = $this->store->get_overdue_job_count();

		if ( $overdue_count > 0 ) {
			$status = 'warning';

			/* translators: [count]: the number of overdue jobs. */
			$parts[] = strtr( _n( '[count] overdue', '[count] overdue', $overdue_count, 'gk-gravityimport' ), [ '[count]' => $overdue_count ] );
		}

		return [
			'key'    => 'last-activity',
			'label'  => $label,
			'value'  => implode( "\n", $parts ),
			'status' => $status,
		];
	}
}
