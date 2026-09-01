<?php
/**
 * Frontend bulk action cleanup helpers.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\BackgroundJobs\Scheduler;
use GravityKit\GravityView\Entry\BulkActions\Actions\DownloadAttachmentsAction;
use GravityKit\GravityView\Entry\BulkActions\Actions\ExportCsvAction;
use GravityKit\GravityView\Foundation\Helpers\WP as WPHelper;

/**
 * Removes transient bulk action runtime state on plugin shutdown events.
 *
 * @since 3.0.0
 */
final class Cleanup {
	/**
	 * Cleans runtime state on deactivation.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $network_wide Whether the plugin is being deactivated network-wide.
	 *
	 * @return void
	 */
	public static function deactivate( $network_wide = false ) {
		self::for_sites( (bool) $network_wide, [ self::class, 'cleanup_deactivation_site' ] );
	}

	/**
	 * Cleans runtime state on uninstall.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function uninstall() {
		self::for_sites( is_multisite(), [ self::class, 'cleanup_uninstall_site' ] );
	}

	/**
	 * Cleans runtime state for the current site during deactivation.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private static function cleanup_deactivation_site() {
		self::cancel_active_jobs();
		self::delete_options_with_prefix(
			[
				ViewActionLock::OPTION_PREFIX,
				ViewActionLock::MUTATION_LOCK_PREFIX,
				'gravityview_background_result_lock_',
			]
		);
		self::delete_transients_with_prefix(
			[
				BackgroundStatus::ACTIVE_TOKEN_PREFIX,
				FlashMessages::TRANSIENT_PREFIX,
				FlashMessages::SYSTEM_TRANSIENT_PREFIX,
			]
		);
	}

	/**
	 * Cleans all Bulk Actions runtime state for the current site during uninstall.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private static function cleanup_uninstall_site() {
		self::cancel_active_jobs();
		ExportCsvAction::cleanup_all_exports_for_current_site();
		DownloadAttachmentsAction::cleanup_all_downloads_for_current_site();
		self::delete_options_with_prefix(
			[
				ViewActionLock::OPTION_PREFIX,
				ViewActionLock::MUTATION_LOCK_PREFIX,
				'gravityview_background_result_lock_',
			]
		);
		self::delete_transients_with_prefix(
			[
				ResultStore::PREFIX,
				BackgroundStatus::ACTIVE_TOKEN_PREFIX,
				FlashMessages::TRANSIENT_PREFIX,
				FlashMessages::SYSTEM_TRANSIENT_PREFIX,
				SelectionMode::TRANSIENT_PREFIX,
			]
		);
	}

	/**
	 * Runs a callback on the current site or every site in a network.
	 *
	 * @since 3.0.0
	 *
	 * @param bool     $network_wide Whether to iterate sites.
	 * @param callable $callback     Callback.
	 *
	 * @return void
	 */
	private static function for_sites( $network_wide, $callback ) {
		if ( ! is_multisite() || ! $network_wide || ! function_exists( 'get_sites' ) ) {
			call_user_func( $callback );
			return;
		}

		$original_blog_id = get_current_blog_id();
		$number           = 100;
		$offset           = 0;

		do {
			$site_ids = get_sites(
				[
					'fields' => 'ids',
					'number' => $number,
					'offset' => $offset,
				]
			);

			foreach ( $site_ids as $site_id ) {
				switch_to_blog( (int) $site_id );

				try {
					call_user_func( $callback );
				} finally {
					restore_current_blog();
				}
			}

			$site_count = count( $site_ids );
			$offset    += $number;
		} while ( $site_count === $number );

		if ( get_current_blog_id() !== $original_blog_id ) {
			switch_to_blog( $original_blog_id );
		}
	}

	/**
	 * Cancels queued or running GravityView bulk action jobs for the current site.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	private static function cancel_active_jobs() {
		foreach ( self::get_stored_results() as $result ) {
			if ( ! is_array( $result ) || ! in_array( (string) ( $result['status'] ?? '' ), [ 'queued', 'running' ], true ) ) {
				continue;
			}

			$job_id = (int) ( $result['job_id'] ?? 0 );

			if ( $job_id <= 0 ) {
				continue;
			}

			$canceled = Scheduler::instance()->cancel( $job_id );

			if ( is_wp_error( $canceled ) ) {
				gravityview()->log->debug(
					'Bulk Actions cleanup could not cancel a background job.',
					[
						'job_id' => $job_id,
						'error'  => $canceled->get_error_message(),
					]
				);
			}
		}
	}

	/**
	 * Returns stored bulk action background results from the current site.
	 *
	 * @since 3.0.0
	 *
	 * @return array[]
	 */
	private static function get_stored_results() {
		global $wpdb;

		$option_names = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( ResultStore::PREFIX ) . '%'
			)
		);

		$result_store = new ResultStore();
		$results      = [];

		foreach ( (array) $option_names as $option_name ) {
			$token  = substr( (string) $option_name, strlen( ResultStore::PREFIX ) );
			$result = $result_store->get( $token );

			if ( ! is_array( $result ) ) {
				continue;
			}

			$results[] = $result;
		}

		return $results;
	}

	/**
	 * Deletes option rows matching one of the given prefixes.
	 *
	 * @since 3.0.0
	 *
	 * @param string[] $prefixes Option name prefixes.
	 *
	 * @return void
	 */
	private static function delete_options_with_prefix( array $prefixes ) {
		global $wpdb;

		foreach ( $prefixes as $prefix ) {
			$prefix = (string) $prefix;

			if ( '' === $prefix ) {
				continue;
			}

			$option_names = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
					$wpdb->esc_like( $prefix ) . '%'
				)
			);

			foreach ( (array) $option_names as $option_name ) {
				delete_option( $option_name );
			}
		}
	}

	/**
	 * Deletes Foundation helper transients matching one of the given prefixes.
	 *
	 * Foundation stores transients as non-autoloaded options using the transient
	 * name directly, so cleanup must use the same helper to clear the helper's
	 * in-request cache as well as the database row.
	 *
	 * @since 3.0.0
	 *
	 * @param string[] $prefixes Transient name prefixes.
	 *
	 * @return void
	 */
	private static function delete_transients_with_prefix( array $prefixes ) {
		global $wpdb;

		foreach ( $prefixes as $prefix ) {
			$prefix = (string) $prefix;

			if ( '' === $prefix ) {
				continue;
			}

			$transient_names = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
					$wpdb->esc_like( $prefix ) . '%'
				)
			);

			foreach ( (array) $transient_names as $transient_name ) {
				WPHelper::delete_transient( (string) $transient_name );
			}
		}
	}
}
