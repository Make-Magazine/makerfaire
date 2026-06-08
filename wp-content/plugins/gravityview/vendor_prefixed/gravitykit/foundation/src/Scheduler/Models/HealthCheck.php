<?php
/**
 * Scheduler execution health check.
 *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Models;

use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityView\Foundation\Helpers\WP;

/**
 * Detects whether the scheduler can execute background jobs.
 *
 * Performs an active loopback probe (cached) and evaluates
 * the WP-Cron / ALTERNATE_WP_CRON configuration to determine
 * whether the scheduler has a viable execution path.
 *
 * Failure codes:
 * - `loopback_and_cron_disabled` — Loopback blocked, WP-Cron disabled. No execution path.
 * - `loopback_and_cron_spawn`   — Loopback blocked, WP-Cron enabled but can't spawn (requires loopback).
 *
 * Usage:
 *
 *     $health = HealthCheck::run();
 *
 *     if ( $health->has_failure() ) {
 *         // Show product-specific message based on failure code.
 *         switch ( $health->failure_code() ) {
 *             case 'loopback_and_cron_disabled':
 *                 echo 'Set up a system cron to process imports.';
 *                 break;
 *             case 'loopback_and_cron_spawn':
 *                 echo 'Your server blocks loopback requests. Enable ALTERNATE_WP_CRON or set up a system cron.';
 *                 break;
 *         }
 *     }
 *
 * @since 1.12.0
 */
class HealthCheck {

	/**
	 * Loopback blocked and WP-Cron is disabled.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const LOOPBACK_AND_CRON_DISABLED = 'loopback_and_cron_disabled';

	/**
	 * Loopback blocked and WP-Cron's spawn mechanism also uses loopback.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	const LOOPBACK_AND_CRON_SPAWN = 'loopback_and_cron_spawn';


	/**
	 * Transient key for the cached health check result.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	private const CACHE_KEY = 'gk_scheduler_health_check';

	/**
	 * Cache duration in seconds for health check results.
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	private const CACHE_TTL = 5 * MINUTE_IN_SECONDS;

	/**
	 * Timeout in seconds for the loopback probe request.
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	private const PROBE_TIMEOUT = 2;

	/**
	 * The failure code, or null if healthy.
	 *
	 * @since 1.12.0
	 *
	 * @var string|null
	 */
	protected $failure_code;

	/**
	 * Human-readable warning message.
	 *
	 * @since 1.12.0
	 *
	 * @var string|null
	 */
	protected $message;

	/**
	 * Whether the loopback probe failed.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 */
	protected $loopback_blocked;

	/**
	 * HealthCheck constructor.
	 *
	 * @since 1.12.0
	 *
	 * @param string|null $failure_code     The failure code, or null if healthy.
	 * @param string|null $message          The warning message, or null if healthy.
	 * @param bool        $loopback_blocked Whether the loopback probe failed.
	 */
	protected function __construct( ?string $failure_code, ?string $message, bool $loopback_blocked = false ) {
		$this->failure_code     = $failure_code;
		$this->message          = $message;
		$this->loopback_blocked = $loopback_blocked;
	}

	/**
	 * Flushes the cached health check result so the next run() performs a fresh probe.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public static function flush(): void {
		WP::delete_transient( self::CACHE_KEY );
	}

	/**
	 * Runs the health check and returns the result.
	 *
	 * Uses a cached active loopback probe. Results are cached
	 * for {@see CACHE_TTL} seconds.
	 *
	 * @since 1.12.0
	 *
	 * @return self
	 */
	public static function run(): self {
		$cached = WP::get_transient( self::CACHE_KEY );

		if ( is_array( $cached ) ) {
			return new self(
				$cached['failure_code'] ?? null,
				$cached['message'] ?? null,
				$cached['loopback_blocked'] ?? false
			);
		}

		return self::evaluate();
	}

	/**
	 * Performs the active loopback test and evaluates the environment.
	 *
	 * @since 1.12.0
	 *
	 * @return self
	 */
	private static function evaluate(): self {
		$loopback_blocked = self::probe_loopback();

		if ( ! $loopback_blocked ) {
			// Loopback works — clear any stale dispatch failure transient.
			WP::delete_transient( 'gk_scheduler_loopback_failed' );

			return self::cache_result( null, null, false );
		}

		$cron_disabled  = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
		$alternate_cron = defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON;

		// ALTERNATE_WP_CRON runs cron inline — slow but functional regardless of other settings.
		if ( $alternate_cron ) {
			return self::cache_result( null, null, true );
		}

		$message = 'Background tasks cannot run due to a server configuration issue.';

		if ( $cron_disabled ) {
			return self::cache_result( self::LOOPBACK_AND_CRON_DISABLED, $message, true );
		}

		// WP-Cron enabled but its spawn mechanism also requires loopback.
		return self::cache_result( self::LOOPBACK_AND_CRON_SPAWN, $message, true );
	}

	/**
	 * Probes loopback connectivity with a lightweight HTTP request.
	 *
	 * Any HTTP response (even 400) means loopback works.
	 * Only a WP_Error (connection refused, timeout) means blocked.
	 *
	 * @since 1.12.0
	 *
	 * @return bool True if loopback is blocked.
	 */
	private static function probe_loopback(): bool {
		$url      = \apply_filters( 'as_async_request_queue_runner_query_url', \admin_url( 'admin-ajax.php' ) );
		$response = \wp_remote_get(
			$url,
			[
				'timeout'     => self::PROBE_TIMEOUT,
				// Loopback requests follow WordPress core's convention — default off, filterable
				// via `https_local_ssl_verify` so anyone overriding it for Site Health picks up
				// the same behaviour here. See wp-admin/includes/class-wp-site-health.php and
				// wp-includes/cron.php — both unconditionally disable sslverify on loopback.
				//
				// See https://developer.wordpress.org/reference/hooks/https_local_ssl_verify/.
				'sslverify'   => \apply_filters( 'https_local_ssl_verify', false ),
				'redirection' => 0,
			]
		);

		return \is_wp_error( $response );
	}

	/**
	 * Caches and returns a health check result.
	 *
	 * @since 1.12.0
	 *
	 * @param string|null $failure_code     The failure code.
	 * @param string|null $message          The warning message.
	 * @param bool        $loopback_blocked Whether the loopback probe failed.
	 *
	 * @return self
	 */
	private static function cache_result( ?string $failure_code, ?string $message, bool $loopback_blocked ): self {
		WP::set_transient(
			self::CACHE_KEY,
			[
				'failure_code'     => $failure_code,
				'message'          => $message,
				'loopback_blocked' => $loopback_blocked,
			],
			self::CACHE_TTL
		);

		return new self( $failure_code, $message, $loopback_blocked );
	}

	/**
	 * Whether a failure was detected.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function has_failure(): bool {
		return null !== $this->failure_code;
	}

	/**
	 * Returns the failure code.
	 *
	 * @since 1.12.0
	 *
	 * @return string|null One of the class constants, or null if healthy.
	 */
	public function failure_code(): ?string {
		return $this->failure_code;
	}

	/**
	 * Returns the human-readable warning message.
	 *
	 * @since 1.12.0
	 *
	 * @return string|null Warning message, or null if healthy.
	 */
	public function message(): ?string {
		return $this->message;
	}

	/**
	 * Whether the loopback probe failed.
	 *
	 * This may be true even when has_failure() is false — e.g., when
	 * ALTERNATE_WP_CRON compensates for the broken loopback.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function is_loopback_blocked(): bool {
		return $this->loopback_blocked;
	}

	/**
	 * Returns a serializable array for the frontend.
	 *
	 * @since 1.12.0
	 *
	 * @return array{has_failure: bool, failure_code: string|null, message: string|null, docs_url: string|null}
	 */
	public function to_array(): array {
		$message = $this->message();

		if ( $message ) {
			$message = esc_html__( $message, 'gk-gravityview' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- Stored untranslated so cached transients are language-neutral.
		}

		return [
			'has_failure'  => $this->has_failure(),
			'failure_code' => $this->failure_code(),
			'message'      => $message,
			'docs_url'     => $this->has_failure() ? 'https://docs.gravitykit.com/article/2150-background-processing#Troubleshooting-lsCWB' : null,
		];
	}
}
