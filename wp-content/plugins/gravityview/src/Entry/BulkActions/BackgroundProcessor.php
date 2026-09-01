<?php
/**
 * Background task adapter for frontend bulk actions.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use Throwable;
use WP_Error;

/**
 * Runs a frontend bulk action against one background entry batch.
 *
 * @since 3.0.0
 */
final class BackgroundProcessor {
	/**
	 * Processes one entry batch.
	 *
	 * @since 3.0.0
	 *
	 * @param array $entries Resolved GV entries.
	 * @param array $context Background job context.
	 *
	 * @return array|WP_Error
	 */
	public static function process( array $entries, array $context ) {
		$view       = $context['view'] ?? null;
		$action_key = isset( $context['callback_args']['action_key'] ) ? sanitize_key( $context['callback_args']['action_key'] ) : '';

		if ( ! $view instanceof \GV\View || '' === $action_key ) {
			return new WP_Error( 'gravityview_bulk_background_invalid_context', __( 'The bulk action could not be processed.', 'gk-gravityview' ) );
		}

		try {
			$actions = Registry::get_actions( $view );

			if ( empty( $actions[ $action_key ]['callback'] ) || ! is_callable( $actions[ $action_key ]['callback'] ) ) {
				return new WP_Error( 'gravityview_bulk_background_action_unavailable', __( 'The selected bulk action is not available.', 'gk-gravityview' ) );
			}

			$action = Config::resolve_action_config(
				$view,
				$action_key,
				$actions[ $action_key ],
				isset( $context['callback_args']['action_settings'] ) && is_array( $context['callback_args']['action_settings'] ) ? $context['callback_args']['action_settings'] : null
			);
			$action['request'] = isset( $context['callback_args']['action_request'] ) && is_array( $context['callback_args']['action_request'] )
				? $context['callback_args']['action_request']
				: [];

			$background = Config::get_action_background_config( $action );
			$enabled    = ! empty( $background['always'] )
				|| ! empty( $action['settings'][ Config::ACTION_BACKGROUND_ENABLED_SETTING ] );

			if (
				! Config::action_supports_background( $action )
				|| ! Config::is_background_processing_enabled( $view )
				|| ! $enabled
			) {
				return new WP_Error( 'gravityview_bulk_background_action_unavailable', __( 'The selected bulk action is not available.', 'gk-gravityview' ) );
			}

			if ( ! empty( $action['capability'] ) && ! \GVCommon::has_cap( $action['capability'], $view->ID ) ) {
				return new WP_Error( 'gravityview_bulk_background_action_unavailable', __( 'The selected bulk action is not available.', 'gk-gravityview' ) );
			}

			$available = true;

			if ( ! empty( $action['available_callback'] ) && is_callable( $action['available_callback'] ) ) {
				$available = (bool) call_user_func( $action['available_callback'], $view, $action, $action_key );
			}

			/**
			 * Filters whether a background bulk action is still available in the View.
			 *
			 * @since 3.0.0
			 *
			 * @param bool     $available  Whether the action is available.
			 * @param int      $view_id    View ID.
			 * @param \GV\View $view       View.
			 * @param string   $action_key Action key.
			 * @param array    $action     Action configuration.
			 */
			$available = (bool) apply_filters( 'gk/gravityview/bulk-actions/action-is-available', $available, (int) $view->ID, $view, $action_key, $action );

			if ( ! $available ) {
				return new WP_Error( 'gravityview_bulk_background_action_unavailable', __( 'The selected bulk action is not available.', 'gk-gravityview' ) );
			}
		} catch ( Throwable $e ) {
			self::log_exception( 'Frontend background bulk action resolution failed.', $e, [ 'action_key' => $action_key, 'view_id' => (int) $view->ID ] );

			return new WP_Error( 'gravityview_bulk_background_action_unavailable', __( 'The selected bulk action is not available.', 'gk-gravityview' ) );
		}

		$entry_arrays = self::entry_arrays( $entries );

		$context['background'] = true;

		if ( [] === $entry_arrays ) {
			$result = [
				'processed' => 0,
				'failed'    => 0,
			];
		} else {
			try {
				$result = call_user_func( $action['callback'], array_keys( $entry_arrays ), $entry_arrays, $view, $action_key, $action, $context );
			} catch ( Throwable $e ) {
				self::log_exception( 'Frontend background bulk action callback failed.', $e, [ 'action_key' => $action_key, 'view_id' => (int) $view->ID ] );

				return new WP_Error( 'gravityview_bulk_background_callback_error', __( 'The background action could not be processed.', 'gk-gravityview' ) );
			}
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( self::checkpoint_has_no_progress( $result ) ) {
			return self::checkpoint_error(
				'gravityview_bulk_background_checkpoint_no_progress',
				__( 'The background action requested a checkpoint without processing any entries.', 'gk-gravityview' ),
				$result
			);
		}

		if ( self::checkpoint_skips_entries( $result, $context ) ) {
			return self::checkpoint_error(
				'gravityview_bulk_background_checkpoint_skips_entries',
				__( 'The background action checkpoint skipped unprocessed entries.', 'gk-gravityview' ),
				$result
			);
		}

		if ( self::checkpoint_finishes_final_batch( $result, count( $entry_arrays ), $context ) ) {
			unset( $result['checkpoint'], $result['next_cursor'] );
		}

		if ( empty( $context['has_more'] ) && ! self::result_requests_checkpoint( $result ) ) {
			$context = self::add_cumulative_result_context( $context, $result, count( $entry_arrays ) );

			$complete_result = self::complete_action( $view, $action_key, $action, $context );

			if ( is_wp_error( $complete_result ) ) {
				return self::add_batch_result_to_error( $complete_result, $result );
			}

			$result = self::merge_result_data( $result, $complete_result );
		}

		return $result;
	}

	/**
	 * Returns whether the action callback asked GravityView to resume from a cursor later.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $result Callback result.
	 *
	 * @return bool
	 */
	private static function result_requests_checkpoint( $result ): bool {
		return is_array( $result ) && ! empty( $result['checkpoint'] );
	}

	/**
	 * Adds cumulative counts to the completion callback context.
	 *
	 * @since 3.0.0
	 *
	 * @param array $context     Background job context.
	 * @param mixed $result      Current batch callback result.
	 * @param int   $entry_count Number of entries passed to the callback.
	 *
	 * @return array
	 */
	private static function add_cumulative_result_context( array $context, $result, int $entry_count ): array {
		$batch_counts = self::get_batch_counts( $result, $entry_count );
		$job_data     = isset( $context['job_data'] ) && is_array( $context['job_data'] ) ? $context['job_data'] : [];
		$missing      = max( 0, count( (array) ( $context['requested_entry_ids'] ?? [] ) ) - count( (array) ( $context['entry_ids'] ?? [] ) ) );

		$context['batch_result'] = $result;
		$context['processed']    = max( 0, (int) ( $job_data['processed'] ?? 0 ) ) + $batch_counts['processed'];
		$context['failed']       = max( 0, (int) ( $job_data['failed'] ?? 0 ) ) + $batch_counts['failed'] + $missing;

		return $context;
	}

	/**
	 * Returns normalized current-batch counts.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $result      Current batch callback result.
	 * @param int   $entry_count Number of entries passed to the callback.
	 *
	 * @return array{processed:int,failed:int}
	 */
	private static function get_batch_counts( $result, int $entry_count ): array {
		if ( is_array( $result ) ) {
			return [
				'processed' => max( 0, (int) ( $result['processed'] ?? $entry_count ) ),
				'failed'    => max( 0, (int) ( $result['failed'] ?? 0 ) ),
			];
		}

		if ( is_int( $result ) ) {
			return [
				'processed' => max( 0, $result ),
				'failed'    => 0,
			];
		}

		if ( false === $result ) {
			return [
				'processed' => 0,
				'failed'    => $entry_count,
			];
		}

		return [
			'processed' => $entry_count,
			'failed'    => 0,
		];
	}

	/**
	 * Returns whether a checkpoint result made no measurable progress.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $result Callback result.
	 *
	 * @return bool
	 */
	private static function checkpoint_has_no_progress( $result ): bool {
		return self::result_requests_checkpoint( $result ) && self::checkpoint_progress_count( $result ) <= 0;
	}

	/**
	 * Returns the number of entries a checkpoint result says it handled.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $result Callback result.
	 *
	 * @return int
	 */
	private static function checkpoint_progress_count( $result ): int {
		if ( ! is_array( $result ) ) {
			return 0;
		}

		return max( 0, (int) ( $result['processed'] ?? 0 ) ) + max( 0, (int) ( $result['failed'] ?? 0 ) );
	}

	/**
	 * Returns whether a checkpoint cursor would move past unhandled entries.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $result  Callback result.
	 * @param array $context Background job context.
	 *
	 * @return bool
	 */
	private static function checkpoint_skips_entries( $result, array $context ): bool {
		if ( ! is_array( $result ) || empty( $result['checkpoint'] ) || ! isset( $result['next_cursor'], $context['cursor'] ) ) {
			return false;
		}

		$cursor       = max( 0, (int) $context['cursor'] );
		$next_cursor  = max( 0, (int) $result['next_cursor'] );
		$requested    = count( (array) ( $context['requested_entry_ids'] ?? [] ) );
		$resolved     = count( (array) ( $context['entry_ids'] ?? [] ) );
		$missing      = max( 0, $requested - $resolved );
		$handled      = self::checkpoint_progress_count( $result ) + $missing;
		$handled_until = $cursor + $handled;

		return $next_cursor > $handled_until;
	}

	/**
	 * Returns whether a checkpoint consumed the last resolved entries.
	 *
	 * A callback can checkpoint mid-batch to let Foundation resume later. If it
	 * checkpoints after consuming the final batch, there is no work left to resume,
	 * so completion should run now instead of scheduling an empty follow-up pass.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $result      Callback result.
	 * @param int   $entry_count Number of entries passed to the action.
	 * @param array $context     Background job context.
	 *
	 * @return bool
	 */
	private static function checkpoint_finishes_final_batch( $result, int $entry_count, array $context ): bool {
		if ( ! is_array( $result ) || empty( $result['checkpoint'] ) || ! empty( $context['has_more'] ) ) {
			return false;
		}

		$consumed = self::checkpoint_progress_count( $result );

		if ( $consumed <= 0 ) {
			return false;
		}

		if ( isset( $result['next_cursor'], $context['next_cursor'] ) ) {
			return $consumed >= $entry_count && (int) $result['next_cursor'] >= (int) $context['next_cursor'];
		}

		return $consumed >= $entry_count;
	}

	/**
	 * Adds the already-processed batch result to a completion error.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_Error $error        Completion error.
	 * @param mixed    $batch_result Batch callback result.
	 *
	 * @return void
	 */
	private static function add_batch_result_to_error( WP_Error $error, $batch_result ): WP_Error {
		$error      = clone $error;
		$error_data = $error->get_error_data();

		if ( ! is_array( $error_data ) ) {
			$error_data = null === $error_data ? [] : [ 'previous_data' => $error_data ];
		}

		$error_data['batch_result'] = $batch_result;

		$error->add_data( $error_data, $error->get_error_code() );

		return $error;
	}

	/**
	 * Builds a fatal checkpoint error while preserving the callback counts.
	 *
	 * @since 3.0.0
	 *
	 * @param string $code         Error code.
	 * @param string $message      Error message.
	 * @param mixed  $batch_result Callback result.
	 *
	 * @return WP_Error
	 */
	private static function checkpoint_error( string $code, string $message, $batch_result ): WP_Error {
		$error = new WP_Error( $code, $message );

		return self::add_batch_result_to_error( $error, $batch_result );
	}

	/**
	 * Runs an action-level completion callback after the last background batch.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View $view       View.
	 * @param string   $action_key Action key.
	 * @param array    $action     Action config.
	 * @param array    $context    Background job context.
	 *
	 * @return mixed|WP_Error
	 */
	private static function complete_action( \GV\View $view, $action_key, array $action, array $context ) {
		$background = Config::get_action_background_config( $action );
		$callback   = $background['complete_callback'] ?? null;

		if ( empty( $callback ) ) {
			return null;
		}

		if ( ! is_callable( $callback ) ) {
			return new WP_Error( 'gravityview_bulk_background_complete_callback_invalid', __( 'The background completion callback is not callable.', 'gk-gravityview' ) );
		}

		try {
			$result = call_user_func( $callback, $view, $action_key, $action, $context );
		} catch ( Throwable $e ) {
			self::log_exception( 'Frontend background bulk action completion failed.', $e, [ 'action_key' => $action_key, 'view_id' => (int) $view->ID ] );

			return new WP_Error( 'gravityview_bulk_background_complete_callback_error', __( 'The background action could not be completed.', 'gk-gravityview' ) );
		}

		if ( is_wp_error( $result ) ) {
			self::log_wp_error( 'Frontend background bulk action completion returned an error.', $result, [ 'action_key' => $action_key, 'view_id' => (int) $view->ID ] );

			return new WP_Error(
				'gravityview_bulk_background_complete_callback_error',
				__( 'The background action could not be completed.', 'gk-gravityview' ),
				[
					'previous_error_code' => $result->get_error_code(),
				]
			);
		}

		return $result;
	}

	/**
	 * Logs an unexpected background action exception.
	 *
	 * @since 3.0.0
	 *
	 * @param string    $message Log message.
	 * @param Throwable $e       Exception.
	 * @param array     $context Extra log context.
	 *
	 * @return void
	 */
	private static function log_exception( $message, Throwable $e, array $context = [] ) {
		if ( ! function_exists( 'gravityview' ) || ! is_object( gravityview() ) || empty( gravityview()->log ) ) {
			return;
		}

		gravityview()->log->error(
			$message,
			array_merge(
				$context,
				[
					'exception' => get_class( $e ),
					'message'   => $e->getMessage(),
				]
			)
		);
	}

	/**
	 * Logs an expected background action error before returning generic frontend copy.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $message Log message.
	 * @param WP_Error $error   Error.
	 * @param array    $context Extra log context.
	 *
	 * @return void
	 */
	private static function log_wp_error( $message, WP_Error $error, array $context = [] ) {
		if ( ! function_exists( 'gravityview' ) || ! is_object( gravityview() ) || empty( gravityview()->log ) ) {
			return;
		}

		gravityview()->log->error(
			$message,
			array_merge(
				$context,
				[
					'error_code'    => $error->get_error_code(),
					'error_message' => $error->get_error_message(),
				]
			)
		);
	}

	/**
	 * Adds completion result data to a batch result without changing counts.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $result          Batch result.
	 * @param mixed $complete_result Completion callback result.
	 *
	 * @return mixed
	 */
	private static function merge_result_data( $result, $complete_result ) {
		if ( null === $complete_result || '' === $complete_result ) {
			return $result;
		}

		$merged = is_array( $result ) ? $result : [];

		if ( is_string( $complete_result ) ) {
			$merged['notice'] = [
				'message' => $complete_result,
			];

			return $merged;
		}

		if ( ! is_array( $complete_result ) ) {
			return $result;
		}

		foreach ( $complete_result as $key => $value ) {
			if ( in_array( $key, [ 'processed', 'failed' ], true ) ) {
				continue;
			}

			$merged[ $key ] = $value;
		}

		return $merged;
	}

	/**
	 * Converts GV entries to Gravity Forms entry arrays keyed by ID.
	 *
	 * @since 3.0.0
	 *
	 * @param array $entries GV entries.
	 *
	 * @return array
	 */
	private static function entry_arrays( array $entries ) {
		$entry_arrays = [];

		foreach ( $entries as $entry ) {
			if ( ! $entry instanceof \GV\Entry ) {
				continue;
			}

			$entry_array = $entry->as_entry();
			$entry_id    = empty( $entry_array['id'] ) ? 0 : (int) $entry_array['id'];

			if ( ! $entry_id ) {
				continue;
			}

			$entry_arrays[ $entry_id ] = $entry_array;
		}

		return $entry_arrays;
	}
}
