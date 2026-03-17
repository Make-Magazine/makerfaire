<?php

namespace GravityKit\GravityImport\Notices;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use GravityKit\GravityImport\BackgroundProcessor;
use GravityKit\GravityImport\Batch;
use GravityKit\GravityImport\UI;

/**
 * Manages live admin notices for background import progress.
 *
 * Uses Foundation's Notices system to display real-time progress bars
 * on any admin page while an import is running in the background.
 *
 * @since 2.9.0
 */
class ImportNotices {

	/**
	 * @since 2.9.0
	 *
	 * @var ImportNotices|null
	 */
	private static $instance;

	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		require_once __DIR__ . '/notice-callbacks.php';
	}

	/**
	 * Returns singleton instance.
	 *
	 * @since 2.9.0
	 *
	 * @return ImportNotices
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Creates a live notice for an import operation.
	 *
	 * @since 2.9.0
	 *
	 * @param int $user_id  User ID who started the import.
	 * @param int $job_id   Scheduler job ID.
	 * @param int $batch_id Import batch ID.
	 *
	 * @return object|null Notice object if created, null otherwise.
	 */
	public function add_import_live_notice( $user_id, $job_id, $batch_id ) {
		$notice_manager = \GravityKitFoundation::notices();

		if ( ! $notice_manager ) {
			return null;
		}

		$user = get_user_by( 'ID', $user_id );

		if ( ! $user || ! $job_id ) {
			return null;
		}

		$notice = [
			'namespace'   => 'gk-gravityimport',
			'slug'        => 'import-progress-' . $batch_id,
			'message'     => __( 'Importing entries...', 'gk-gravityimport' ),
			'severity'    => 'info',
			'dismissible' => true,
			'screens'     => [ 'dashboard' ],
			'context'     => [ 'site', 'ms_main', 'ms_subsite' ],
			'scope'       => 'user',
			'users'       => [ $user_id ],
			'condition'   => 'gk_gravityimport_should_display_notice',
			'live'        => [
				'callback'           => 'gk_gravityimport_live_callback',
				'refresh_interval'   => 3,
				'show_progress'      => true,
				'auto_hide_progress' => true,
				'progress'           => 0,
				'auto_dismiss'       => false,
			],
			'extra' => [
				'batch_id' => $batch_id,
				'job_id'   => $job_id,
				'user_id'  => $user_id,
			],
		];

		try {
			return $notice_manager->add_stored( $notice ) ?: null;
		} catch ( \Throwable $e ) {
			return null;
		}
	}

	/**
	 * Live callback for import progress notice.
	 *
	 * Reads batch progress and returns data for Foundation's live notice UI.
	 *
	 * @since 2.9.0
	 *
	 * @param array $context Notice context with 'extra' containing batch_id.
	 *
	 * @return array{message: string, progress: int} Notice update data.
	 */
	public static function live_callback( $context ) {
		$batch_id = $context['extra']['batch_id'] ?? 0;
		$batch    = $batch_id ? Batch::get( $batch_id ) : null;

		if ( ! $batch ) {
			return [
				'message'         => __( 'Import status unavailable.', 'gk-gravityimport' ),
				'progress'        => 100,
				'show_progress'   => false,
				'severity'        => 'warning',
				'disable_polling' => true,
			];
		}

		$progress  = $batch['progress'];
		$total     = $batch['meta']['rows'] ?? 0;
		$processed = ( $progress['processed'] ?? 0 ) + ( $progress['skipped'] ?? 0 ) + ( $progress['error'] ?? 0 );

		$import_url  = admin_url( 'admin.php?page=' . UI::PAGE_SLUG . '&batch_id=' . $batch_id ) . '#/step5/import-data';
		$import_link = sprintf( '<a href="%s">%s</a>.', esc_url( $import_url ), __( 'View import', 'gk-gravityimport' ) );

		$imported_count = $progress['processed'] ?? 0;
		$error_count    = $progress['error'] ?? 0;

		// Detect stale batches where the scheduler job has failed, cancelled, or paused.
		$batch = BackgroundProcessor::maybe_mark_failed_job( $batch );

		if ( ! empty( $batch['scheduler_status'] ) && 'pending' === $batch['scheduler_status'] ) {
			$percent = $total > 0 ? min( 99, (int) round( $processed / $total * 100 ) ) : 0;

			return [
				'message'         => __( 'Resuming import&hellip;', 'gk-gravityimport' ) . ' ' . $import_link,
				'progress'        => $percent,
				'show_progress'   => true,
				'severity'        => 'info',
				'disable_polling' => false,
			];
		}

		if ( ! empty( $batch['scheduler_status'] ) && 'paused' === $batch['scheduler_status'] ) {
			$percent = $total > 0 ? min( 99, (int) round( $processed / $total * 100 ) ) : 0;

			$message = strtr(
				__( 'Import paused &mdash; [processed] of [total] entries imported.', 'gk-gravityimport' ),
				[
					'[processed]' => number_format_i18n( $processed ),
					'[total]'     => number_format_i18n( $total ),
				]
			);

			return [
				'message'         => $message . ' ' . $import_link,
				'progress'        => $percent,
				'show_progress'   => false,
				'severity'        => 'warning',
				'disable_polling' => false,
			];
		}

		switch ( $batch['status'] ) {
			case 'done':
				// All succeeded.
				if ( 0 === $error_count ) {
					$message = strtr(
						__( 'Successfully imported [count] entries.', 'gk-gravityimport' ),
						[ '[count]' => number_format_i18n( $imported_count ) ]
					);

					return [
						'message'         => $message . ' ' . $import_link,
						'progress'        => 100,
						'show_progress'   => false,
						'severity'        => 'success',
						'disable_polling' => true,
					];
				}

				// Some failed.
				if ( $imported_count > 0 ) {
					$message = strtr(
						__( 'Import finished &mdash; [imported] of [total] entries imported.', 'gk-gravityimport' ),
						[
							'[imported]' => number_format_i18n( $imported_count ),
							'[total]'    => number_format_i18n( $total ),
						]
					);

					return [
						'message'         => $message . ' ' . $import_link,
						'progress'        => 100,
						'show_progress'   => false,
						'severity'        => 'warning',
						'disable_polling' => true,
					];
				}

				// All failed.
				$message = __( 'Import failed &mdash; no entries could be imported.', 'gk-gravityimport' );

				return [
					'message'         => $message . ' ' . $import_link,
					'progress'        => 100,
					'show_progress'   => false,
					'severity'        => 'error',
					'disable_polling' => true,
				];

			case 'error':
				if ( $imported_count > 0 ) {
					$message = strtr(
						__( 'Import stopped after importing [imported] of [total] entries.', 'gk-gravityimport' ),
						[
							'[imported]' => number_format_i18n( $imported_count ),
							'[total]'    => number_format_i18n( $total ),
						]
					);

					return [
						'message'         => $message . ' ' . $import_link,
						'progress'        => 100,
						'show_progress'   => false,
						'severity'        => 'warning',
						'disable_polling' => true,
					];
				}

				return [
					'message'         => __( 'Import failed &mdash; no entries could be imported.', 'gk-gravityimport' ) . ' ' . $import_link,
					'progress'        => 100,
					'show_progress'   => false,
					'severity'        => 'error',
					'disable_polling' => true,
				];

			case 'halted':
				return [
					'message'         => __( 'Import was cancelled.', 'gk-gravityimport' ) . ' ' . $import_link,
					'progress'        => 100,
					'show_progress'   => false,
					'severity'        => 'warning',
					'disable_polling' => true,
				];

			default:
				// Show "Preparing" until at least one entry has been processed.
				if ( 0 === $processed ) {
					return [
						'message'       => __( 'Preparing your import&hellip;', 'gk-gravityimport' ) . ' ' . $import_link,
						'progress'      => 0,
						'show_progress' => true,
						'severity'      => 'info',
					];
				}

				$percent = $total > 0 ? min( 99, (int) round( $processed / $total * 100 ) ) : 0;

				$message = strtr(
					__( 'Importing [processed] of [total] entries&hellip;', 'gk-gravityimport' ),
					[
						'[processed]' => number_format_i18n( $processed ),
						'[total]'     => number_format_i18n( $total ),
					]
				);

				return [
					'message'       => $message . ' ' . $import_link,
					'progress'      => $percent,
					'show_progress' => true,
					'severity'      => 'info',
				];
		}
	}

	/**
	 * Condition callback to check if the notice should be displayed.
	 *
	 * Shows the notice while the import is active and after it completes
	 * so the user can see the result. The notice is dismissible.
	 *
	 * @since 2.9.0
	 *
	 * @param object $notice The notice object.
	 *
	 * @return bool True if notice should be shown.
	 */
	public static function should_display( $notice ) {
		$extra    = $notice->get_extra();
		$batch_id = $extra['batch_id'] ?? 0;

		if ( ! $batch_id ) {
			return false;
		}

		$batch = Batch::get( $batch_id );

		if ( ! $batch ) {
			return false;
		}

		$visible_statuses = [ 'process', 'processing', 'parsing', 'done', 'error', 'halted' ];

		return in_array( $batch['status'], $visible_statuses, true );
	}

	/**
	 * Removes the live notice for a batch.
	 *
	 * @since 2.9.0
	 *
	 * @param int $batch_id The batch ID.
	 */
	public function remove( $batch_id ) {
		$notice_manager = \GravityKitFoundation::notices();

		if ( ! $notice_manager ) {
			return;
		}

		try {
			$notice_manager->remove( 'gk-gravityimport/import-progress-' . $batch_id );
		} catch ( \Throwable $e ) {
			// Notice may already be removed.
		}
	}

	/**
	 * Removes all import progress notices by querying batches with scheduler jobs.
	 *
	 * @since 2.9.0
	 */
	public function remove_all() {
		$batches = Batch::all( [
			'limit' => 50,
			'order' => 'ID',
			'sort'  => 'DESC',
		] );

		foreach ( $batches as $batch ) {
			$job_id = Batch::get_job_id( $batch['id'] );

			if ( $job_id ) {
				$this->remove( $batch['id'] );
			}
		}
	}
}
