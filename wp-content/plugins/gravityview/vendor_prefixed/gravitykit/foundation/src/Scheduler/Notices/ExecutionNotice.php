<?php
/**
 * Admin notice for background processing issues.
 *
 * @license GPL-2.0-or-later
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Scheduler\Notices;

use GravityKit\GravityView\Foundation\Core;
use GravityKit\GravityView\Foundation\Scheduler\Models\AbstractAction;
use GravityKit\GravityView\Foundation\Scheduler\Models\HealthCheck;
use GravityKit\GravityView\Foundation\Scheduler\Store\DbStore;

/**
 * Registers a runtime notice when background job execution is unavailable.
 *
 * Shown when HealthCheck detects a failure and pending jobs exist.
 * Uses Foundation's notice system for rendering, dismissal, and snooze.
 *
 * @since 1.12.0
 */
class ExecutionNotice {

	/**
	 * Registers the admin notice hook.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_init', [ $this, 'maybe_add_notice' ] );
		add_filter( 'action_scheduler_pastdue_actions_check', [ $this, 'exclude_gk_from_pastdue_notice' ], 10, 4 );
	}

	/**
	 * Adds a runtime notice if execution health is degraded and jobs are pending.
	 *
	 * @since 1.12.0
	 *
	 * @return void
	 */
	public function maybe_add_notice(): void {
		$health = HealthCheck::run();

		if ( ! $health->has_failure() ) {
			return;
		}

		if ( ! $this->has_pending_jobs() ) {
			return;
		}

		$product_names = $this->get_affected_product_names();
		$message       = $this->build_message( $health, $product_names );

		Core::notices()->add_runtime(
			[
				'namespace'    => 'gk-foundation',
				'slug'         => 'scheduler-execution-warning',
				'message'      => $message,
				'severity'     => 'warning',
				'dismissible'  => true,
				'snooze'       => [
					__( '24 hours', 'gk-gravityview' ) => DAY_IN_SECONDS,
				],
				'capabilities' => [ 'manage_options' ],
				'context'      => [ 'site', 'ms_main', 'ms_subsite' ],
			]
		);
	}

	/**
	 * Builds the notice message, including affected product names when available.
	 *
	 * @since 1.12.0
	 *
	 * @param HealthCheck $health        The health check result.
	 * @param string[]    $product_names Product display names.
	 *
	 * @return string
	 */
	protected function build_message( HealthCheck $health, array $product_names ): string {
		$read_more = '<a href="https://docs.gravitykit.com/article/2150-background-processing#Troubleshooting-lsCWB" target="_blank">'
			. esc_html__( 'Read more', 'gk-gravityview' ) . '</a>.';

		if ( ! empty( $product_names ) ) {
			$message = strtr(
				esc_html__( 'Background tasks scheduled by [products] cannot run due to a server configuration issue.', 'gk-gravityview' ),
				[ '[products]' => '<strong>' . implode( ', ', $product_names ) . '</strong>' ]
			);
		} else {
			$message = esc_html__( 'Background tasks cannot run due to a server configuration issue.', 'gk-gravityview' );
		}

		return $message . ' ' . $read_more;
	}

	/**
	 * Returns display names of products that have pending or paused jobs.
	 *
	 * Extracts the `product` text domain from job args, then looks up
	 * the display name from Foundation's product registry.
	 *
	 * @since 1.12.0
	 *
	 * @return string[] Unique product display names.
	 */
	protected function get_affected_product_names(): array {
		$actions = as_get_scheduled_actions(
			[
				'group'    => DbStore::GROUP_ID,
				'status'   => [ DbStore::STATUS_PENDING, DbStore::STATUS_PAUSED ],
				'per_page' => 50,
			]
		);

		if ( empty( $actions ) ) {
			return [];
		}

		$text_domains = [];

		foreach ( $actions as $action ) {
			$args = $action->get_args();
			$td   = $args['product'] ?? '';

			if ( $td && ! in_array( $td, $text_domains, true ) ) {
				$text_domains[] = $td;
			}
		}

		if ( empty( $text_domains ) ) {
			return [];
		}

		return $this->resolve_product_names( $text_domains );
	}

	/**
	 * Resolves text domains to display names via AbstractAction::resolve_product().
	 *
	 * @since 1.12.0
	 *
	 * @param string[] $text_domains Product text domains.
	 *
	 * @return string[] Display names.
	 */
	protected function resolve_product_names( array $text_domains ): array {
		$names = [];

		foreach ( $text_domains as $td ) {
			$name = AbstractAction::resolve_product( $td )['name'] ?? '';

			if ( $name ) {
				$names[] = $name;
			}
		}

		return array_unique( $names );
	}

	/**
	 * Whether pending jobs exist in the scheduler's AS group.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	protected function has_pending_jobs(): bool {
		$pending = as_get_scheduled_actions(
			[
				'group'    => DbStore::GROUP_ID,
				'status'   => DbStore::STATUS_PENDING,
				'per_page' => 1,
			]
		);

		return ! empty( $pending );
	}

	/**
	 * Excludes GravityKit actions from the AS past-due admin notice.
	 *
	 * When the only past-due actions belong to GravityKit groups, suppresses the
	 * generic AS notice. Our own ExecutionNotice and Background Jobs health check
	 * provide a more user-friendly warning.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $has_pastdue       Whether past-due actions were found.
	 * @param int  $num_pastdue       Number of past-due actions found.
	 * @param int  $threshold_seconds Age threshold in seconds. Default: DAY_IN_SECONDS.
	 * @param int  $threshold_min     Minimum count to trigger the notice. Default: 1.
	 *
	 * @return bool
	 */
	public function exclude_gk_from_pastdue_notice( bool $has_pastdue, int $num_pastdue, int $threshold_seconds, int $threshold_min ): bool {
		if ( ! $has_pastdue ) {
			return false;
		}

		$gk_pastdue = 0;

		foreach ( [ DbStore::GROUP_ID, DbStore::TASK_GROUP_ID ] as $group ) {
			$gk_pastdue += (int) as_get_scheduled_actions(
				[
					'group'    => $group,
					'status'   => \ActionScheduler_Store::STATUS_PENDING,
					'date'     => new \ActionScheduler_DateTime( '@' . ( time() - $threshold_seconds ), new \DateTimeZone( 'UTC' ) ),
					'per_page' => $num_pastdue,
				],
				'count'
			);
		}

		// If non-GK actions are still past-due, let the AS notice through.
		return ( $num_pastdue - $gk_pastdue ) >= $threshold_min;
	}
}
