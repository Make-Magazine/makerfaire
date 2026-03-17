<?php
/**
 * Request handler.
 * *
 * @license GPL-2.0-or-later
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\Scheduler\Handlers;

use ActionScheduler;
use GravityKit\GravityImport\Foundation\Helpers\WP;
use GravityKit\GravityImport\Foundation\Scheduler\Traits\LoggerTrait;
use WP_Async_Request;

class RequestHandler extends WP_Async_Request {

	use LoggerTrait;

	const REQUEST_TIMEOUT_MS = 100;

	/**
	 * The prefix for the async request.
	 *
	 * @var string
	 * @access protected
	 */
	protected $prefix = 'gravitykit';

	/**
	 * @inheritDoc
	 *
	 * @since 1.12.0
	 */
	public function __construct() {
		if ( $this->is_scheduler_debug() && filter_input( INPUT_GET, 'gk_scheduler_debug' ) && current_user_can( 'manage_options' ) ) {
			$this->debug_run_tasks();
		}

		// This check is for the unit tests.
		if ( method_exists( get_parent_class( $this ), '__construct' ) ) {
			parent::__construct();
		}

		// Register WP-Cron fallback handler for loopback failures.
		add_action( 'gk_scheduler_cron_fallback', [ $this, 'cron_fallback' ] );
	}

	/**
	 * Dispatches the async request.
	 *
	 * Logs failures, sets a transient for admin UI warnings, and falls back
	 * to WP-Cron if the loopback request fails.
	 *
	 * @since 1.12.0
	 *
	 * @return array|null
	 */
	public function dispatch(): ? array {
		$this->logger()->debug( __METHOD__ );

		// Modify the timeout via filters since WordPress enforces a minimum of 1 second.
		add_action( 'requests-curl.before_send', [ $this, 'modify_timeout' ] );

		// This check is for unit tests.
		$result = method_exists( get_parent_class( $this ), 'dispatch' ) ? parent::dispatch() : null;

		remove_action( 'requests-curl.before_send', [ $this, 'modify_timeout' ] );

		if ( $this->is_loopback_failure( $result ) ) {
			$this->handle_loopback_failure( $result );

			return is_array( $result ) ? $result : null;
		}

		// Clear any previous failure transient on success.
		WP::delete_transient( 'gk_scheduler_loopback_failed' );

		return is_array( $result ) ? $result : null;
	}

	/**
	 * Checks whether a dispatch result indicates a loopback failure.
	 *
	 * @since 1.12.0
	 *
	 * @param mixed $result The dispatch result from wp_remote_post().
	 *
	 * @return bool
	 */
	protected function is_loopback_failure( $result ): bool {
		if ( is_wp_error( $result ) ) {
			return true;
		}

		if ( is_array( $result ) ) {
			$code = (int) wp_remote_retrieve_response_code( $result );

			// Non-blocking requests return 0 (the response code is `false`, cast to int).
			// This is expected — the request was dispatched but we can't verify the response.
			if ( 0 === $code ) {
				return false;
			}

			return $code < 200 || $code >= 300;
		}

		return false;
	}

	/**
	 * Handles a loopback failure by logging, setting a transient, and scheduling a WP-Cron fallback.
	 *
	 * @since 1.12.0
	 *
	 * @param mixed $result The failed dispatch result.
	 *
	 * @return void
	 */
	protected function handle_loopback_failure( $result ): void {
		if ( is_wp_error( $result ) ) {
			$this->logger()->error(
				'Loopback dispatch failed.',
				[
					'error' => $result->get_error_message(),
					'code'  => $result->get_error_code(),
				]
			);
		} else {
			$this->logger()->error(
				'Loopback dispatch returned non-200 status.',
				[
					'status' => wp_remote_retrieve_response_code( $result ),
				]
			);
		}

		// Surface failure to admin UI.
		WP::set_transient( 'gk_scheduler_loopback_failed', time(), 12 * HOUR_IN_SECONDS );

		// Schedule WP-Cron fallback to run the Action Scheduler queue.
		if ( ! wp_next_scheduled( 'gk_scheduler_cron_fallback' ) ) {
			wp_schedule_single_event( time() + 60, 'gk_scheduler_cron_fallback' );
		}
	}

	/**
	 * Modifies the CURL timeout. Workaround for WordPress enforcing a minimum of 1 second.
	 *
	 * @since  1.12.0
	 *
	 * @param resource $handle The CURL handle.
	 *
	 * @return void
	 */
	public function modify_timeout( $handle ): void {

		/**
		 * Filters the scheduler trigger request timeout.
		 *
		 * @since 1.12.0
		 *
		 * @param int $timeout Scheduler trigger request timeout in milliseconds. Default 100.
		 */
		$timeout = apply_filters( 'gk/foundation/scheduler/request/trigger/timeout', self::REQUEST_TIMEOUT_MS );

		// @phpcs:disable
		if ( function_exists( 'curl_setopt' ) ) {
			curl_setopt( $handle, CURLOPT_TIMEOUT_MS, $timeout );
		}
		// @phpcs:enable
	}

	/**
	 * Checks if the scheduler is in debug mode.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function is_scheduler_debug(): bool {
		return defined( 'GK_SCHEDULER_DEBUG' ) && GK_SCHEDULER_DEBUG;
	}

	/**
	 * Runs the tasks in debug mode, on task per request.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function debug_run_tasks(): void {
		add_filter(
            'action_scheduler_queue_runner_concurrent_batches',
            function () {
				return 2;
			}
        );

		add_action(
            'init',
            function () {
				$this->handle();
			}
        );
	}

	/**
	 * Handles the async request.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	protected function handle() {
		$this->logger()->debug( __METHOD__ );
		ActionScheduler::runner()->run( 'Async Request' ); // @phpstan-ignore arguments.count (AS stubs lack $context param)
	}

	/**
	 * WP-Cron fallback that runs the Action Scheduler queue when loopback fails.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function cron_fallback(): void {
		$this->logger()->debug( 'Running WP-Cron fallback for loopback failure.' );
		ActionScheduler::runner()->run( 'WP Cron' ); // @phpstan-ignore arguments.count (AS stubs lack $context param)
	}
}
