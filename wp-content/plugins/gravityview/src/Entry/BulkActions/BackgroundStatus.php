<?php
/**
 * Frontend bulk action background job status.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\BackgroundJobs\ResultNotice;
use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\BackgroundJobs\Scheduler;
use GravityKit\GravityView\Foundation\Helpers\WP as WPHelper;
use GravityKit\GravityView\View\View;

/**
 * Renders and controls background jobs started by frontend bulk actions.
 *
 * @since 3.0.0
 */
final class BackgroundStatus {
	const ACTIVE_TOKEN_PREFIX = 'gv_bulk_active_';

	/**
	 * @since 3.0.0
	 * @var ResultStore
	 */
	private $result_store;

	/**
	 * @since 3.0.0
	 * @var ViewActionLock
	 */
	private $view_lock;

	/**
	 * @since 3.0.0
	 * @var array
	 */
	private $jobs = [];

	/**
	 * Render instances whose selection should be cleared during the current request.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	private $clear_selection = [];

	/**
	 * @since 3.0.0
	 *
	 * @param ResultStore|null    $result_store Result store.
	 * @param ViewActionLock|null $view_lock    View-level action lock service.
	 */
	public function __construct( ?ResultStore $result_store = null, ?ViewActionLock $view_lock = null ) {
		$this->result_store = $result_store ? $result_store : new ResultStore();
		$this->view_lock    = $view_lock ? $view_lock : new ViewActionLock( $this->result_store );
	}

	/**
	 * Processes a cancel request for a background bulk action job.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_cancel_request() {
		if ( empty( $_POST[ Config::POST_CANCEL_JOB ] ) ) {
			return;
		}

		$view_id = empty( $_POST[ Config::POST_VIEW_ID ] ) ? 0 : absint( wp_unslash( $_POST[ Config::POST_VIEW_ID ] ) );
		$token   = empty( $_POST[ Config::POST_JOB_TOKEN ] ) ? '' : sanitize_key( wp_unslash( $_POST[ Config::POST_JOB_TOKEN ] ) );
		$view    = $view_id ? View::by_id( $view_id ) : null;

		if ( ! $view || ! $token ) {
			return;
		}

		$nonce = empty( $_POST[ Config::POST_JOB_NONCE ] ) ? '' : sanitize_text_field( wp_unslash( $_POST[ Config::POST_JOB_NONCE ] ) );

		if ( ! wp_verify_nonce( $nonce, Config::get_background_job_nonce_action( $view, $token ) ) ) {
			return $this->redirect( $view_id, $token );
		}

		$job = $this->get_job_for_view( $view, $token );

		if ( ! $job ) {
			return $this->redirect( $view_id, $token );
		}

		$result = $job['result'];

		if ( ! $this->is_active_result( $result ) ) {
			return $this->redirect( $view_id, $token );
		}

		$render_instance = $this->get_request_render_instance();

		if ( ! $this->result_matches_render_instance( $result, $render_instance ) ) {
			return $this->redirect( $view_id, $token );
		}

		$cancel_result = Scheduler::instance()->cancel( (int) ( $result['job_id'] ?? 0 ) );

		if ( is_wp_error( $cancel_result ) ) {
			if ( $this->is_missing_foundation_job_error( $cancel_result ) ) {
				$this->store_job_result(
					$token,
					$result,
					[
						'status'       => 'canceled',
						'cancel_error' => '',
					]
				);
				$this->clear_active_token( $view, $this->get_result_render_instance( $result ) );

				return $this->redirect( $view_id, $token );
			}

			$this->store_job_result(
				$token,
				$result,
				[
					'cancel_error' => $cancel_result->get_error_message(),
				]
			);

			return $this->redirect( $view_id, $token );
		}

		$this->store_job_result(
			$token,
			$result,
			[
				'status'       => 'canceled',
				'cancel_error' => '',
			]
		);
		$this->clear_active_token( $view, $this->get_result_render_instance( $result ) );

		return $this->redirect( $view_id, $token );
	}

	/**
	 * Processes a dismiss request for a terminal background bulk action notice.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_dismiss_request() {
		if ( empty( $_POST[ Config::POST_DISMISS_JOB ] ) ) {
			return;
		}

		$view_id = empty( $_POST[ Config::POST_VIEW_ID ] ) ? 0 : absint( wp_unslash( $_POST[ Config::POST_VIEW_ID ] ) );
		$token   = empty( $_POST[ Config::POST_JOB_TOKEN ] ) ? '' : sanitize_key( wp_unslash( $_POST[ Config::POST_JOB_TOKEN ] ) );
		$view    = $view_id ? View::by_id( $view_id ) : null;

		if ( ! $view || ! $token ) {
			return;
		}

		$nonce = empty( $_POST[ Config::POST_JOB_NONCE ] ) ? '' : sanitize_text_field( wp_unslash( $_POST[ Config::POST_JOB_NONCE ] ) );

		if ( ! wp_verify_nonce( $nonce, Config::get_background_job_nonce_action( $view, $token ) ) ) {
			return $this->redirect( $view_id, $token );
		}

		$job = $this->get_job_for_view( $view, $token );

		if ( ! $job || $this->is_active_result( $job['result'] ) ) {
			return $this->redirect( $view_id, $token );
		}

		$render_instance = $this->get_request_render_instance();

		if ( ! $this->result_matches_render_instance( $job['result'], $render_instance ) ) {
			return $this->redirect( $view_id, $token );
		}

		if ( ! empty( $this->get_background_status_settings( $view, $job['result'] )['sticky_result'] ) ) {
			$this->run_dismiss_callback( $view, $token, $job['result'] );
		}

		$this->clear_active_token( $view, $this->get_result_render_instance( $job['result'] ) );
		$this->consume_terminal_result( $view, $token, $this->get_result_render_instance( $job['result'] ) );
		LifecycleEvents::result_dismissed( $job['result'], $token );

		return $this->redirect( $view_id, '' );
	}

	/**
	 * Processes an AJAX request for the current background job status.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_ajax_status_request() {
		$view_id         = empty( $_POST[ Config::POST_VIEW_ID ] ) ? 0 : absint( wp_unslash( $_POST[ Config::POST_VIEW_ID ] ) );
		$token           = empty( $_POST[ Config::POST_JOB_TOKEN ] ) ? '' : sanitize_key( wp_unslash( $_POST[ Config::POST_JOB_TOKEN ] ) );
		$reload_url      = empty( $_POST[ Config::POST_RELOAD_URL ] ) ? '' : esc_url_raw( wp_unslash( $_POST[ Config::POST_RELOAD_URL ] ) );
		$render_instance = empty( $_POST[ Config::POST_RENDER_INSTANCE ] ) ? '' : sanitize_key( wp_unslash( $_POST[ Config::POST_RENDER_INSTANCE ] ) );
		$view            = $view_id ? View::by_id( $view_id ) : null;

		if ( ! $view || ! $token ) {
			wp_send_json_error(
				[
					'message' => __( 'The background job could not be found.', 'gk-gravityview' ),
				],
				404
			);
		}

		$nonce = empty( $_POST[ Config::POST_JOB_NONCE ] ) ? '' : sanitize_text_field( wp_unslash( $_POST[ Config::POST_JOB_NONCE ] ) );

		if ( ! wp_verify_nonce( $nonce, Config::get_background_job_nonce_action( $view, $token ) ) ) {
			wp_send_json_error(
				[
					'message' => __( 'The background job status request was invalid.', 'gk-gravityview' ),
				],
				403
			);
		}

		$payload = $this->get_status_payload( $view, $token, true, $reload_url, $render_instance );

		if ( ! $payload ) {
			wp_send_json_error(
				[
					'message' => __( 'The background job could not be found.', 'gk-gravityview' ),
				],
				404
			);
		}

		wp_send_json_success( $payload );
	}

	/**
	 * Renders the current background job status notice for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view            View.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return void
	 */
	public function render( View $view, $render_instance = '' ) {
		$payload = $this->get_status_payload( $view, '', true, '', $render_instance );

		if ( ! $payload ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_status_wrapper_html() and normalize_status_payload() return sanitized notice markup/classes for GVCommon::generate_notice().
		echo \GVCommon::generate_notice( $this->get_status_wrapper_html( $view, $payload ), $payload['notice_class'] );
	}

	/**
	 * Returns a frontend-safe status payload for the active background job.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view             View.
	 * @param string $token            Optional result token. Defaults to the current request or active token.
	 * @param bool   $consume_terminal Whether terminal results should be consumed after building the payload.
	 * @param string $reload_url       Optional URL to use for reload links and automatic reloads.
	 * @param string $render_instance  Optional frontend render instance ID.
	 *
	 * @return array|null
	 */
	public function get_status_payload( View $view, $token = '', $consume_terminal = false, $reload_url = '', $render_instance = '' ) {
		$render_instance = sanitize_key( (string) $render_instance );
		$job             = $token ? $this->get_job_for_view( $view, $token ) : $this->get_current_job( $view, $render_instance );

		if ( ! $job ) {
			return null;
		}

		$result                     = $job['result'];
		$token                      = sanitize_key( $job['token'] );
		$status                     = (string) ( $result['status'] ?? '' );
		$active                     = $this->is_active_result( $result );

		if ( ! $this->should_render_status_for_instance( $result, $render_instance ) ) {
			return null;
		}

		$background_status_settings = $this->get_background_status_settings( $view, $result );
		$completion_behavior        = $background_status_settings['completion_behavior'];
		$reload_url                 = $this->get_reload_url( $view, $result, $reload_url );
		$message                    = $this->get_message( $result );

		if ( ! $active ) {
			$message = $this->add_completion_message_link( $message, $result, $completion_behavior, $reload_url, $view );
		}

		$payload_defaults = [
			'action'              => Config::AJAX_STATUS_ACTION,
			'active'              => $active,
			'completion_behavior' => $completion_behavior,
			'failed'              => max( 0, (int) ( $result['failed'] ?? 0 ) ),
			'message'             => $message,
			'notice_class'        => $this->get_notice_class( $status ),
			'polling'             => $background_status_settings['polling'],
			'poll_interval'       => Config::get_background_poll_interval( $view ),
			'processed'           => max( 0, (int) ( $result['processed'] ?? 0 ) ),
			'reload_url'          => $reload_url,
			'render_instance'     => $render_instance ?: sanitize_key( (string) ( $result['render_instance'] ?? '' ) ),
			'should_reload'       => ! $active && 'complete' === $status && Config::BACKGROUND_COMPLETE_AUTO_RELOAD === $completion_behavior,
			'status'              => $status,
			'sticky_result'       => $background_status_settings['sticky_result'],
			'terminal'            => ! $active,
			'token'               => $token,
			'total'               => isset( $result['query_total'] ) && null !== $result['query_total'] ? max( 0, (int) $result['query_total'] ) : null,
			'view_id'             => (int) $view->ID,
		];

		/**
		 * Filters the frontend background bulk action status payload.
		 *
		 * The payload is structured data. Any filtered `html` value is ignored and
		 * regenerated from the sanitized message after this filter runs.
		 * Identity and control fields are server-owned and restored after filtering.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $payload Status payload.
		 * @param array  $result  Stored result data.
		 * @param string $token   Result token.
		 * @param View   $view    View.
		 */
		$payload         = $this->normalize_status_payload(
			apply_filters( 'gk/gravityview/bulk-actions/background-job/payload', $payload_defaults, $result, $token, $view ),
			$payload_defaults
		);
		$payload['html'] = $this->get_status_content_html( $view, $payload, $result );

		if ( $consume_terminal && empty( $payload['active'] ) && empty( $payload['sticky_result'] ) ) {
			$this->clear_active_token( $view, $this->get_result_render_instance( $result ) );
			$this->consume_terminal_result( $view, $token, $this->get_result_render_instance( $result ) );
		}

		return $payload;
	}

	/**
	 * Returns action-specific display settings for a background result.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View  $view   View.
	 * @param array $result Stored result data.
	 *
	 * @return array{polling:bool,completion_behavior:string,sticky_result:bool}
	 */
	private function get_background_status_settings( View $view, array $result ) {
		$action_key        = sanitize_key( (string) ( $result['action_key'] ?? '' ) );
		$settings_snapshot = isset( $result['action_settings'] ) && is_array( $result['action_settings'] ) ? $result['action_settings'] : null;
		$actions           = '' === $action_key ? [] : Registry::get_actions( $view );

		if ( '' !== $action_key && isset( $actions[ $action_key ] ) ) {
			return Config::get_action_background_status_settings( $action_key, $actions[ $action_key ], $view, $settings_snapshot );
		}

		return Config::normalize_background_status_settings( $settings_snapshot ?: [] );
	}

	/**
	 * Whether the current View render instance has an active background job status token.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view            View.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return bool
	 */
	public function is_active( View $view, $render_instance = '' ) {
		$job = $this->get_current_job( $view, $render_instance );

		return $job ? $this->is_active_result( $job['result'] ) : false;
	}

	/**
	 * Whether the current request has a background job status for a View render instance.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view            View.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return bool
	 */
	public function should_clear_selection( View $view, $render_instance = '' ) {
		if ( $this->should_clear_render_instance_selection( (int) $view->ID, $render_instance ) ) {
			return true;
		}

		$job = $this->get_current_job( $view, $render_instance );

		return $job ? $this->result_matches_render_instance( $job['result'], $render_instance ) : false;
	}

	/**
	 * Stores the active background job token for a View render instance and user.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id         View ID.
	 * @param string $token           Background result token.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return array|null
	 */
	public function remember( $view_id, $token, $render_instance ) {
		$view_id         = absint( $view_id );
		$token           = sanitize_key( $token );
		$render_instance = sanitize_key( (string) $render_instance );

		if ( ! $view_id || ! $token || '' === $render_instance ) {
			return;
		}

		$view = View::by_id( $view_id );

		if ( $view ) {
			$this->supersede_previous_terminal_result( $view, $token, $render_instance );
		}

		WPHelper::set_transient( $this->get_active_token_key( $view_id, $render_instance ), $token, ResultStore::DEFAULT_TTL );
	}

	/**
	 * Consumes the previous terminal result when a new result becomes active for the same render instance.
	 *
	 * The UI presents one active background result per user, View, and render
	 * instance. When a new result replaces an older completed sticky result for
	 * that same render instance, the old token should not resurface through
	 * browser history.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view            View.
	 * @param string $new_token       New active result token.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return void
	 */
	private function supersede_previous_terminal_result( View $view, $new_token, $render_instance ) {
		$previous_token = $this->get_active_token( $view, $render_instance );
		$new_token      = sanitize_key( $new_token );

		if ( ! $previous_token || $previous_token === $new_token ) {
			return;
		}

		$job = $this->get_job_for_view( $view, $previous_token );

		if ( ! $job || $this->is_active_result( $job['result'] ) ) {
			return;
		}

		$this->consume_terminal_result( $view, $previous_token, (string) ( $job['result']['render_instance'] ?? '' ) );
	}

	/**
	 * Redirects back to the View URL with the background job token.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id View ID.
	 * @param string $token   Background result token.
	 *
	 * @return void
	 */
	public function redirect( $view_id, $token ) {
		$token = sanitize_key( $token );
		$url   = $token ? add_query_arg( Config::QUERY_BACKGROUND_TOKEN, rawurlencode( $token ), Config::get_form_action_url() ) : Config::get_form_action_url();

		wp_safe_redirect( $url );

		/**
		 * Filters whether request processing should exit after redirecting.
		 *
		 * @since 3.0.0
		 *
		 * @param bool   $exit    Whether to exit after redirect.
		 * @param int    $view_id View ID.
		 * @param string $status  Redirect status.
		 * @param string $url     Redirect URL.
		 * @param string $message Redirect message.
		 */
		if ( apply_filters( 'gk/gravityview/bulk-actions/exit-after-redirect', true, absint( $view_id ), 'background', $url, '' ) ) {
			exit;
		}
	}

	/**
	 * Returns the current background job token from the request.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function get_request_token() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only routing token used to display a previously stored result; POST actions still verify nonces.
		return empty( $_GET[ Config::QUERY_BACKGROUND_TOKEN ] ) ? '' : sanitize_key( wp_unslash( $_GET[ Config::QUERY_BACKGROUND_TOKEN ] ) );
	}

	/**
	 * Returns the submitted render instance ID.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function get_request_render_instance() {
		return empty( $_POST[ Config::POST_RENDER_INSTANCE ] ) ? '' : sanitize_key( wp_unslash( $_POST[ Config::POST_RENDER_INSTANCE ] ) );
	}

	/**
	 * Returns the current background job for a View render instance.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view            View.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return array|null
	 */
	private function get_current_job( View $view, $render_instance = '' ) {
		$request_token = $this->get_request_token();
		$render_instance = sanitize_key( (string) $render_instance );

		if ( $request_token ) {
			$job = $this->get_job_for_view( $view, $request_token );

			if ( $job && $this->result_matches_render_instance( $job['result'], $render_instance ) ) {
				return $job;
			}
		}

		$token = $this->get_active_token( $view, $render_instance );

		if ( ! $token || $token === $request_token ) {
			return null;
		}

		$job = $this->get_job_for_view( $view, $token );

		if ( ! $job ) {
			$this->clear_active_token( $view, $render_instance );
		}

		return $job;
	}

	/**
	 * Returns a background job result by token if it belongs to the View and user.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view  View.
	 * @param string $token Result token.
	 *
	 * @return array|null
	 */
	private function get_job_for_view( View $view, $token ) {
		$token = sanitize_key( $token );

		if ( ! $token ) {
			return null;
		}

		$cache_key = (int) $view->ID . ':' . $token;

		if ( array_key_exists( $cache_key, $this->jobs ) ) {
			return $this->jobs[ $cache_key ];
		}

		$result = $this->result_store->get( $token );

		if ( ! is_array( $result ) || (int) ( $result['view_id'] ?? 0 ) !== (int) $view->ID || ! $this->is_current_user_result( $result ) ) {
			$this->jobs[ $cache_key ] = null;

			return null;
		}

		if ( ! empty( $result['consumed'] ) ) {
			$this->clear_active_token( $view, $this->get_result_render_instance( $result ) );
			$this->jobs[ $cache_key ] = null;

			return null;
		}

		$result = $this->reconcile_foundation_status( $view, $token, $result );

		$this->jobs[ $cache_key ] = [
			'token'  => $token,
			'result' => $result,
		];

		return $this->jobs[ $cache_key ];
	}

	/**
	 * Returns whether a result belongs to the current user.
	 *
	 * @since 3.0.0
	 *
	 * @param array $result Result data.
	 *
	 * @return bool
	 */
	private function is_current_user_result( array $result ) {
		$actor_user_id = (int) ( $result['actor_user_id'] ?? 0 );

		return $actor_user_id > 0 && get_current_user_id() === $actor_user_id;
	}

	/**
	 * Returns the stored active job token for a View render instance.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view            View.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return string
	 */
	private function get_active_token( View $view, $render_instance = '' ) {
		$render_instance = sanitize_key( (string) $render_instance );

		if ( '' === $render_instance ) {
			return '';
		}

		$token = WPHelper::get_transient( $this->get_active_token_key( (int) $view->ID, $render_instance ) );

		return is_string( $token ) ? sanitize_key( $token ) : '';
	}

	/**
	 * Clears the stored active job token for a View render instance.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view            View.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return void
	 */
	private function clear_active_token( View $view, $render_instance = '' ) {
		$render_instance = sanitize_key( (string) $render_instance );

		if ( '' === $render_instance ) {
			return;
		}

		WPHelper::delete_transient( $this->get_active_token_key( (int) $view->ID, $render_instance ) );
	}

	/**
	 * Consumes a terminal job result after it has been rendered once.
	 *
	 * Callers must first verify that the result belongs to the requested render
	 * instance; this method only marks the already-approved result consumed and
	 * clears the selection bucket identified by the stored result.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view            View.
	 * @param string $token           Result token.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return void
	 */
	private function consume_terminal_result( View $view, $token, $render_instance = '' ) {
		$this->mark_clear_selection( (int) $view->ID, $render_instance );
		$token = sanitize_key( $token );

		if ( ! $token ) {
			return;
		}

		$this->result_store->update(
			$token,
			static function ( array $result ) {
				if ( [] === $result ) {
					return null;
				}

				$result['consumed'] = true;

				return $result;
			},
			ResultStore::DEFAULT_TTL,
			[
				'view_id' => (int) $view->ID,
				'status'  => 'consumed',
			]
		);
		$this->view_lock->release( $view, $token );

		unset( $this->jobs[ (int) $view->ID . ':' . $token ] );
	}

	/**
	 * Whether a stored background status should render for the current render instance.
	 *
	 * Active and terminal results are render-instance-scoped so duplicate embeds
	 * do not render, consume, or clear each other's result state. The View action
	 * lock can still block conflicting submissions from sibling embeds.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $result          Stored result data.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return bool
	 */
	private function should_render_status_for_instance( array $result, $render_instance ) {
		return $this->result_matches_render_instance( $result, $render_instance );
	}

	/**
	 * Marks a render instance's selection for clearing during the current request.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id         View ID.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return void
	 */
	private function mark_clear_selection( $view_id, $render_instance = '' ) {
		$view_id         = absint( $view_id );
		$render_instance = sanitize_key( (string) $render_instance );

		if ( ! $view_id ) {
			return;
		}

		if ( '' === $render_instance ) {
			return;
		}

		$this->clear_selection[ $view_id ][ $render_instance ] = true;
	}

	/**
	 * Returns whether a render instance's selection should be cleared.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id         View ID.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return bool
	 */
	private function should_clear_render_instance_selection( $view_id, $render_instance = '' ) {
		$view_id         = absint( $view_id );
		$render_instance = sanitize_key( (string) $render_instance );

		if ( empty( $this->clear_selection[ $view_id ] ) ) {
			return false;
		}

		return '' !== $render_instance && ! empty( $this->clear_selection[ $view_id ][ $render_instance ] );
	}

	/**
	 * Returns whether a result belongs to a render instance.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $result          Stored result data.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return bool
	 */
	private function result_matches_render_instance( array $result, $render_instance = '' ) {
		$stored_render_instance = sanitize_key( (string) ( $result['render_instance'] ?? '' ) );
		$render_instance        = sanitize_key( (string) $render_instance );

		return '' !== $stored_render_instance && '' !== $render_instance && $stored_render_instance === $render_instance;
	}

	/**
	 * Returns the render instance that owns a stored background result.
	 *
	 * Empty render instances are treated as invalid for terminal result ownership.
	 *
	 * @since 3.0.0
	 *
	 * @param array $result Stored result data.
	 *
	 * @return string
	 */
	private function get_result_render_instance( array $result ) {
		return sanitize_key( (string) ( $result['render_instance'] ?? '' ) );
	}

	/**
	 * Runs an action-level dismiss callback for a terminal background notice.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view   View.
	 * @param string $token  Result token.
	 * @param array  $result Stored result data.
	 *
	 * @return void
	 */
	private function run_dismiss_callback( View $view, $token, array $result ) {
		$action_key = sanitize_key( (string) ( $result['action_key'] ?? '' ) );

		if ( '' === $action_key ) {
			return;
		}

		$actions = Registry::get_actions( $view );

		if ( empty( $actions[ $action_key ] ) || empty( $actions[ $action_key ]['dismiss_callback'] ) ) {
			return;
		}

		$callback = $actions[ $action_key ]['dismiss_callback'];

		if ( ! is_callable( $callback ) ) {
			gravityview()->log->warning(
				'Bulk action dismiss callback is not callable for action {action}.',
				[
					'action'  => $action_key,
					'view_id' => (int) $view->ID,
					'token'   => sanitize_key( $token ),
				]
			);

			return;
		}

		$settings_snapshot = isset( $result['action_settings'] ) && is_array( $result['action_settings'] ) ? $result['action_settings'] : null;
		$action            = Config::resolve_action_config( $view, $action_key, $actions[ $action_key ], $settings_snapshot );

		try {
			call_user_func( $callback, $view, $action_key, $action, $result, sanitize_key( $token ) );
		} catch ( \Throwable $e ) {
			gravityview()->log->error(
				'Bulk action dismiss callback failed for action {action}: {message}',
				[
					'action'  => $action_key,
					'view_id' => (int) $view->ID,
					'token'   => sanitize_key( $token ),
					'message' => $e->getMessage(),
				]
			);
		}
	}

	/**
	 * Returns the active job token transient key.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $view_id         View ID.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return string
	 */
	private function get_active_token_key( $view_id, $render_instance ) {
		return self::ACTIVE_TOKEN_PREFIX . get_current_user_id() . '_' . absint( $view_id ) . '_' . sanitize_key( (string) $render_instance );
	}

	/**
	 * Returns whether a background result is still active.
	 *
	 * @since 3.0.0
	 *
	 * @param array $result Result data.
	 *
	 * @return bool
	 */
	private function is_active_result( array $result ) {
		return in_array( (string) ( $result['status'] ?? '' ), [ 'queued', 'running' ], true );
	}

	/**
	 * Reconciles a stored active result with Foundation's source-of-truth job status.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view   View.
	 * @param string $token  Result token.
	 * @param array  $result Result data.
	 *
	 * @return array
	 */
	private function reconcile_foundation_status( View $view, $token, array $result ) {
		if ( ! $this->is_active_result( $result ) || empty( $result['job_id'] ) ) {
			return $result;
		}

		$status = $this->get_foundation_job_status( (int) $result['job_id'], $result, $token, $view );

		if ( '' === $status ) {
			return $result;
		}

		if ( 'missing' === $status && ! $this->is_missing_job_stale( $result, $view ) ) {
			return $result;
		}

		if ( 'missing' === $status ) {
			return $this->store_job_result(
				$token,
				$result,
				[
					'status'     => 'failed',
					'last_error' => __( 'The background job could no longer be found. Start the bulk action again if needed.', 'gk-gravityview' ),
				]
			);
		}

		$mapped_status = $this->map_foundation_status( $status );

		if ( '' === $mapped_status || (string) ( $result['status'] ?? '' ) === $mapped_status ) {
			return $result;
		}

		$overrides = [
			'status' => $mapped_status,
		];

		if ( 'failed' === $mapped_status && empty( $result['last_error'] ) ) {
			$overrides['last_error'] = __( 'The background job did not finish successfully.', 'gk-gravityview' );
		}

		if ( 'canceled' === $mapped_status ) {
			$overrides['cancel_error'] = '';
		}

		$result = $this->store_job_result( $token, $result, $overrides );

		return $result;
	}

	/**
	 * Returns Foundation's current job status for a background result.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $job_id Foundation job/action ID.
	 * @param array  $result Result data.
	 * @param string $token  Result token.
	 * @param View   $view   View.
	 *
	 * @return string
	 */
	private function get_foundation_job_status( $job_id, array $result, $token, View $view ) {
		$status = Scheduler::instance()->get_job_status( (int) $job_id );

		if ( is_wp_error( $status ) ) {
			$status = $this->is_missing_foundation_job_error( $status ) ? 'missing' : '';
		} elseif ( null === $status ) {
			$status = 'missing';
		} else {
			$status = sanitize_key( (string) $status );
		}

		/**
		 * Filters the current Foundation job status used to reconcile a frontend bulk action notice.
		 *
		 * @since 3.0.0
		 *
		 * @param string $status Foundation job status.
		 * @param int    $job_id Foundation job/action ID.
		 * @param array  $result Stored bulk action result data.
		 * @param string $token  Result token.
		 * @param View   $view   View.
		 */
		$status = apply_filters( 'gk/gravityview/bulk-actions/background-job/status', $status, (int) $job_id, $result, sanitize_key( $token ), $view );

		return is_string( $status ) ? sanitize_key( $status ) : '';
	}

	/**
	 * Maps Foundation or Action Scheduler status strings to bulk action result statuses.
	 *
	 * @since 3.0.0
	 *
	 * @param string $status Foundation status.
	 *
	 * @return string
	 */
	private function map_foundation_status( $status ) {
		$status = sanitize_key( (string) $status );

		if ( in_array( $status, [ 'complete', 'completed' ], true ) ) {
			return 'complete';
		}

		if ( in_array( $status, [ 'failed', 'failure' ], true ) ) {
			return 'failed';
		}

		if ( in_array( $status, [ 'canceled', 'cancelled' ], true ) ) {
			return 'canceled';
		}

		if ( in_array( $status, [ 'deleted', 'not_found' ], true ) ) {
			return 'canceled';
		}

		return '';
	}

	/**
	 * Returns whether a missing Foundation job has been missing long enough to fail.
	 *
	 * @since 3.0.0
	 *
	 * @param array $result Stored result data.
	 * @param View  $view   View.
	 *
	 * @return bool
	 */
	private function is_missing_job_stale( array $result, View $view ) {
		$created_at = max( 0, (int) ( $result['created_at'] ?? 0 ) );

		if ( 0 === $created_at ) {
			return true;
		}

		return time() - $created_at >= Config::get_lost_job_timeout( $view, $result );
	}

	/**
	 * Returns whether a scheduler error means the underlying Foundation job no longer exists.
	 *
	 * @since 3.0.0
	 *
	 * @param \WP_Error $error Error.
	 *
	 * @return bool
	 */
	private function is_missing_foundation_job_error( \WP_Error $error ) {
		return 'gravityview_background_job_missing' === $error->get_error_code()
			|| (bool) preg_match( '/^Job [0-9]+ not found\\.?$/', trim( $error->get_error_message() ) );
	}

	/**
	 * Stores updated job status data.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token     Result token.
	 * @param array  $result    Existing result data.
	 * @param array  $overrides Result overrides.
	 *
	 * @return array
	 */
	private function store_job_result( $token, array $result, array $overrides ) {
		$token = sanitize_key( $token );

		if ( ! $token ) {
			return array_merge( $result, $overrides );
		}

		$previous_status = '';
		$updated         = $this->result_store->update(
			$token,
			static function ( array $latest ) use ( $result, $overrides, &$previous_status ) {
				if ( [] === $latest ) {
					$latest = $result;
				}

				$previous_status = sanitize_key( (string) ( $latest['status'] ?? '' ) );

				foreach ( [ 'status', 'cancel_error', 'last_error' ] as $field ) {
					if ( array_key_exists( $field, $overrides ) ) {
						$latest[ $field ] = $overrides[ $field ];
					}
				}

				return $latest;
			},
			ResultStore::DEFAULT_TTL,
			[
				'view_id'    => (int) ( $result['view_id'] ?? 0 ),
				'job_id'     => (int) ( $result['job_id'] ?? 0 ),
				'status'     => sanitize_key( (string) ( $overrides['status'] ?? ( $result['status'] ?? '' ) ) ),
				'action_key' => sanitize_key( (string) ( $result['action_key'] ?? '' ) ),
			]
		);

		$this->jobs = [];
		$updated    = is_array( $updated ) ? $updated : array_merge( $result, $overrides );

		if ( ! $this->is_active_result( $updated ) ) {
			$this->view_lock->release( (int) ( $updated['view_id'] ?? 0 ), $token );
		}

		LifecycleEvents::terminal_transition( $updated, $previous_status, $token );

		return $updated;
	}

	/**
	 * Returns the status notice message.
	 *
	 * @since 3.0.0
	 *
	 * @param array $result Result data.
	 *
	 * @return string
	 */
	private function get_message( array $result ) {
		$status       = (string) ( $result['status'] ?? '' );
		$action_label = $this->get_action_label( $result );
		$processed    = number_format_i18n( max( 0, (int) ( $result['processed'] ?? 0 ) ) );
		$failed       = number_format_i18n( max( 0, (int) ( $result['failed'] ?? 0 ) ) );
		$total        = '';

		if ( isset( $result['query_total'] ) && null !== $result['query_total'] ) {
			$total = number_format_i18n( max( 0, (int) $result['query_total'] ) );
		}

		if ( 'queued' === $status && ! empty( $result['queued_message'] ) ) {
			return (string) $result['queued_message'];
		}

		$notice = $this->get_result_notice( $result );

		if ( 'complete' === $status && '' !== $notice['message'] ) {
			return $notice['message'];
		}

		if ( 'complete' === $status && (int) ( $result['failed'] ?? 0 ) > 0 ) {
			return strtr(
				/* translators: [action] is the bulk action label. [processed] is a processed entry count. [failed] is a failed entry count. */
				__( '[action] finished. [processed] entries processed. [failed] could not be processed.', 'gk-gravityview' ),
				[
					'[action]'    => $action_label,
					'[processed]' => $processed,
					'[failed]'    => $failed,
				]
			);
		}

		if ( 'complete' === $status ) {
			return strtr(
				/* translators: [action] is the bulk action label. [processed] is a processed entry count. */
				__( '[action] finished. [processed] entries processed.', 'gk-gravityview' ),
				[
					'[action]'    => $action_label,
					'[processed]' => $processed,
				]
			);
		}

		if ( 'failed' === $status ) {
			$error = trim( (string) ( $result['last_error'] ?? '' ) );

			if ( '' !== $error ) {
				return strtr(
					/* translators: [action] is the bulk action label. [error] is the background job error message. */
					__( '[action] failed: [error]', 'gk-gravityview' ),
					[
						'[action]' => $action_label,
						'[error]'  => $error,
					]
				);
			}

			return strtr(
				/* translators: [action] is the bulk action label. */
				__( '[action] failed.', 'gk-gravityview' ),
				[
					'[action]' => $action_label,
				]
			);
		}

		if ( 'canceled' === $status ) {
			return strtr(
				/* translators: [action] is the bulk action label. [processed] is a processed entry count. */
				__( '[action] was canceled. [processed] entries processed.', 'gk-gravityview' ),
				[
					'[action]'    => $action_label,
					'[processed]' => $processed,
				]
			);
		}

		$message = '' !== $total ? strtr(
			/* translators: [action] is the bulk action label. [processed] is a processed entry count. [total] is the total entry count. */
			__( '[action] is running. [processed] of [total] entries processed.', 'gk-gravityview' ),
			[
				'[action]'    => $action_label,
				'[processed]' => $processed,
				'[total]'     => $total,
			]
		) : strtr(
			/* translators: [action] is the bulk action label. [processed] is a processed entry count. */
			__( '[action] is running. [processed] entries processed.', 'gk-gravityview' ),
			[
				'[action]'    => $action_label,
				'[processed]' => $processed,
			]
		);

		if ( empty( $result['cancel_error'] ) ) {
			return $message;
		}

		return $message . ' ' . strtr(
			/* translators: [error] is the cancellation error message. */
			__( 'Cancellation failed: [error]', 'gk-gravityview' ),
			[
				'[error]' => (string) $result['cancel_error'],
			]
		);
	}

	/**
	 * Returns the human label for the action.
	 *
	 * @since 3.0.0
	 *
	 * @param array $result Result data.
	 *
	 * @return string
	 */
	private function get_action_label( array $result ) {
		$label = trim( (string) ( $result['action_label'] ?? '' ) );

		return '' !== $label ? $label : __( 'Bulk action', 'gk-gravityview' );
	}

	/**
	 * Returns a normalized frontend status payload.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $payload  Filtered payload.
	 * @param array $defaults Default payload.
	 *
	 * @return array
	 */
	private function normalize_status_payload( $payload, array $defaults ) {
		$payload = is_array( $payload ) ? $payload : [];
		unset( $payload['html'] );

		$payload = array_merge( $defaults, $payload );

		$server_owned_fields = [
			'action',
			'active',
			'completion_behavior',
			'polling',
			'poll_interval',
			'reload_url',
			'render_instance',
			'should_reload',
			'status',
			'sticky_result',
			'terminal',
			'token',
			'view_id',
		];

		foreach ( $server_owned_fields as $field ) {
			$payload[ $field ] = $defaults[ $field ];
		}

		$payload['action']              = sanitize_key( (string) ( $payload['action'] ?? Config::AJAX_STATUS_ACTION ) );
		$payload['active']              = (bool) ( $payload['active'] ?? false );
		$payload['completion_behavior'] = in_array(
			(string) ( $payload['completion_behavior'] ?? '' ),
			[ Config::BACKGROUND_COMPLETE_SHOW_MESSAGE, Config::BACKGROUND_COMPLETE_RELOAD_LINK, Config::BACKGROUND_COMPLETE_AUTO_RELOAD ],
			true
		) ? (string) $payload['completion_behavior'] : Config::BACKGROUND_COMPLETE_SHOW_MESSAGE;
		$payload['failed']              = max( 0, (int) ( $payload['failed'] ?? 0 ) );
		$payload['message']             = ResultNotice::normalize_message( $payload['message'] ?? '' );
		$payload['notice_class']        = $this->normalize_notice_class( $payload['notice_class'] ?? '' );
		$payload['polling']             = (bool) ( $payload['polling'] ?? false );
		$payload['poll_interval']       = max( 1, (int) ( $payload['poll_interval'] ?? Config::BACKGROUND_POLL_INTERVAL ) );
		$payload['processed']           = max( 0, (int) ( $payload['processed'] ?? 0 ) );
		$payload['reload_url']          = $this->normalize_reload_url( $payload['reload_url'] ?? '' );
		$payload['reload_url']          = '' !== $payload['reload_url'] ? $payload['reload_url'] : (string) ( $defaults['reload_url'] ?? '' );
		$payload['render_instance']     = sanitize_key( (string) ( $payload['render_instance'] ?? '' ) );
		$payload['should_reload']       = (bool) ( $payload['should_reload'] ?? false );
		$payload['status']              = sanitize_key( (string) ( $payload['status'] ?? '' ) );
		$payload['sticky_result']       = (bool) ( $payload['sticky_result'] ?? false );
		$payload['terminal']            = (bool) ( $payload['terminal'] ?? ! $payload['active'] );
		$payload['token']               = sanitize_key( (string) ( $payload['token'] ?? '' ) );
		$payload['total']               = null === $payload['total'] ? null : max( 0, (int) $payload['total'] );
		$payload['view_id']             = absint( $payload['view_id'] ?? 0 );

		return $payload;
	}

	/**
	 * Returns safe CSS class tokens for the notice wrapper.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $class_name Class string.
	 *
	 * @return string
	 */
	private function normalize_notice_class( $class_name ) {
		$tokens = preg_split( '/\s+/', trim( (string) $class_name ), -1, PREG_SPLIT_NO_EMPTY );
		$tokens = array_filter( array_map( 'sanitize_html_class', is_array( $tokens ) ? $tokens : [] ) );

		return $tokens ? implode( ' ', $tokens ) : 'gv-bulk-actions-message';
	}

	/**
	 * Returns the HTML wrapper used by frontend polling.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view    View.
	 * @param array $payload Status payload.
	 *
	 * @return string
	 */
	private function get_status_wrapper_html( View $view, array $payload ) {
		return strtr(
			'<div class="gv-bulk-actions-background-status"'
			. ' data-gv-bulk-background-status="1"'
			. ' data-view-id="[view_id]"'
			. ' data-token="[token]"'
			. ' data-nonce="[nonce]"'
			. ' data-ajax-action="[ajax_action]"'
			. ' data-ajax-url="[ajax_url]"'
			. ' data-polling="[polling]"'
			. ' data-poll-interval="[poll_interval]"'
			. ' data-render-instance="[render_instance]"'
			. ' data-completion-behavior="[completion_behavior]"'
			. ' data-reload-url="[reload_url]"'
			. ' data-status="[status]"'
			. '>[html]</div>',
			[
				'[ajax_action]'         => esc_attr( Config::AJAX_STATUS_ACTION ),
				'[ajax_url]'            => esc_url( admin_url( 'admin-ajax.php' ) ),
				'[completion_behavior]' => esc_attr( (string) ( $payload['completion_behavior'] ?? Config::BACKGROUND_COMPLETE_SHOW_MESSAGE ) ),
				'[html]'                => (string) ( $payload['html'] ?? '' ),
				'[nonce]'               => esc_attr( wp_create_nonce( Config::get_background_job_nonce_action( $view, (string) $payload['token'] ) ) ),
				'[poll_interval]'       => esc_attr( (string) (int) ( $payload['poll_interval'] ?? Config::BACKGROUND_POLL_INTERVAL ) ),
				'[polling]'             => empty( $payload['polling'] ) ? '0' : '1',
				'[reload_url]'          => esc_url( (string) ( $payload['reload_url'] ?? $this->get_reload_url( $view ) ) ),
				'[render_instance]'     => esc_attr( sanitize_key( (string) ( $payload['render_instance'] ?? '' ) ) ),
				'[status]'              => esc_attr( (string) ( $payload['status'] ?? '' ) ),
				'[token]'               => esc_attr( (string) ( $payload['token'] ?? '' ) ),
				'[view_id]'             => esc_attr( (string) (int) ( $payload['view_id'] ?? 0 ) ),
			]
		);
	}

	/**
	 * Returns the notice content HTML for one status payload.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view    View.
	 * @param array $payload Status payload.
	 * @param array $result  Stored result data.
	 *
	 * @return string
	 */
	private function get_status_content_html( View $view, array $payload, array $result ) {
		$html = $this->get_status_message_html(
			(string) ( $payload['message'] ?? '' ),
			! empty( $payload['active'] ) && ! empty( $payload['polling'] )
		);

		$action_url = $this->normalize_reload_url( $payload['reload_url'] ?? '' );

		if ( ! empty( $payload['active'] ) && ! empty( $result['job_id'] ) ) {
			$html .= $this->get_cancel_form( $view, (string) $payload['token'], $action_url, (string) ( $payload['render_instance'] ?? '' ) );
		} elseif ( ! empty( $payload['sticky_result'] ) && ! empty( $payload['token'] ) ) {
			$html .= $this->get_dismiss_form( $view, (string) $payload['token'], $action_url, (string) ( $payload['render_instance'] ?? '' ) );
		}

		return $html;
	}

	/**
	 * Returns the status message paragraph.
	 *
	 * @since 3.0.0
	 *
	 * @param string $message      Status message.
	 * @param bool   $show_spinner Whether to include a spinner.
	 *
	 * @return string
	 */
	private function get_status_message_html( $message, $show_spinner ) {
		$html = '<p class="gv-bulk-actions-background-message">';

		if ( $show_spinner ) {
			$html .= '<span class="gv-bulk-actions-spinner" aria-hidden="true"></span> ';
		}

		$html .= '<span class="gv-bulk-actions-background-message-text" aria-live="polite" aria-atomic="true">' . ResultNotice::normalize_message( $message ) . '</span>';

		return $html . '</p>';
	}

	/**
	 * Returns the stored result notice.
	 *
	 * @since 3.0.0
	 *
	 * @param array $result Result data.
	 *
	 * @return array{message:string}
	 */
	private function get_result_notice( array $result ) {
		$result_data = isset( $result['result'] ) && is_array( $result['result'] ) ? $result['result'] : [];

		if ( ! empty( $result_data['notice'] ) ) {
			return ResultNotice::normalize( $result_data['notice'] );
		}

		return ResultNotice::normalize( $result['message'] ?? '' );
	}

	/**
	 * Adds the configured completion link to a terminal background action message.
	 *
	 * @since 3.0.0
	 *
	 * @param string $message             Notice message.
	 * @param array  $result              Stored result data.
	 * @param string $completion_behavior Completion behavior.
	 * @param string $reload_url          URL to use for the reload link.
	 * @param View   $view                View.
	 *
	 * @return string
	 */
	private function add_completion_message_link( $message, array $result, $completion_behavior, $reload_url, View $view ) {
		$status = (string) ( $result['status'] ?? '' );

		if ( Config::BACKGROUND_COMPLETE_RELOAD_LINK === $completion_behavior && in_array( $status, [ 'complete', 'canceled' ], true ) ) {
			$message = trim( $message ) . ' <a href="' . esc_url( $reload_url ) . '">' . esc_html__( 'Reload View', 'gk-gravityview' ) . '</a>';
		}

		/**
		 * Filters the terminal background bulk action notice message.
		 *
		 * @since 3.0.0
		 *
		 * @param string $message             Notice message. Safe HTML is allowed.
		 * @param array  $result              Stored result data.
		 * @param View   $view                View.
		 * @param string $completion_behavior Completion behavior.
		 */
		return ResultNotice::normalize_message( apply_filters( 'gk/gravityview/bulk-actions/background-job/message', $message, $result, $view, $completion_behavior ) );
	}

	/**
	 * Returns the URL used to reload the current View page.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view          View.
	 * @param array  $result        Stored result data.
	 * @param string $requested_url Optional reload URL supplied by the frontend.
	 *
	 * @return string
	 */
	private function get_reload_url( View $view, array $result = [], $requested_url = '' ) {
		$candidates = [
			$requested_url,
			$result['reload_url'] ?? '',
			Config::get_form_action_url(),
			get_permalink( (int) $view->ID ),
			home_url( '/' ),
		];

		foreach ( $candidates as $candidate ) {
			$url = $this->normalize_reload_url( $candidate );

			if ( '' !== $url ) {
				return $url;
			}
		}

		return home_url( '/' );
	}

	/**
	 * Returns a safe same-site reload URL.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $url URL.
	 *
	 * @return string
	 */
	private function normalize_reload_url( $url ) {
		$url = trim( (string) $url );

		if ( '' === $url ) {
			return '';
		}

		$url = remove_query_arg( [ Config::QUERY_TOKEN, Config::QUERY_BACKGROUND_TOKEN ], $url );
		$url = wp_validate_redirect( $url, '' );

		return $url ? $url : '';
	}

	/**
	 * Returns the GravityView notice class for a status.
	 *
	 * @since 3.0.0
	 *
	 * @param string $status Status.
	 *
	 * @return string
	 */
	private function get_notice_class( $status ) {
		if ( 'failed' === $status ) {
			return 'gv-bulk-actions-message gv-error error';
		}

		if ( 'canceled' === $status ) {
			return 'gv-bulk-actions-message warning';
		}

		return 'gv-bulk-actions-message updated';
	}

	/**
	 * Returns the cancel form for an active background job.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $token      Result token.
	 * @param string $action_url Frontend URL to post back to.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return string
	 */
	private function get_cancel_form( View $view, $token, $action_url = '', $render_instance = '' ) {
		$action_url = '' !== $action_url ? $action_url : Config::get_form_action_url();

		return strtr(
			'<form method="post" action="[action]" class="gv-bulk-actions-cancel-job">'
			. '<input type="hidden" name="[post_cancel]" value="1">'
			. '<input type="hidden" name="[post_view]" value="[view_id]">'
			. '<input type="hidden" name="[post_render_instance]" value="[render_instance]">'
			. '<input type="hidden" name="[post_token]" value="[token]">'
			. '<input type="hidden" name="[post_nonce]" value="[nonce]">'
			. '<button type="submit" class="button gv-bulk-actions-cancel-job-button">[button]</button>'
			. '</form>',
			[
				'[action]'               => esc_url( $action_url ),
				'[post_cancel]'          => esc_attr( Config::POST_CANCEL_JOB ),
				'[post_view]'            => esc_attr( Config::POST_VIEW_ID ),
				'[view_id]'              => esc_attr( (string) (int) $view->ID ),
				'[post_render_instance]' => esc_attr( Config::POST_RENDER_INSTANCE ),
				'[render_instance]'      => esc_attr( sanitize_key( (string) $render_instance ) ),
				'[post_token]'           => esc_attr( Config::POST_JOB_TOKEN ),
				'[token]'                => esc_attr( sanitize_key( $token ) ),
				'[post_nonce]'           => esc_attr( Config::POST_JOB_NONCE ),
				'[nonce]'                => esc_attr( wp_create_nonce( Config::get_background_job_nonce_action( $view, $token ) ) ),
				'[button]'               => esc_html__( 'Cancel', 'gk-gravityview' ),
			]
		);
	}

	/**
	 * Returns the dismiss form for a terminal background job notice.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $token      Result token.
	 * @param string $action_url Frontend URL to post back to.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return string
	 */
	private function get_dismiss_form( View $view, $token, $action_url = '', $render_instance = '' ) {
		$action_url = '' !== $action_url ? $action_url : Config::get_form_action_url();

		return strtr(
			'<form method="post" action="[action]" class="gv-bulk-actions-dismiss-job">'
			. '<input type="hidden" name="[post_dismiss]" value="1">'
			. '<input type="hidden" name="[post_view]" value="[view_id]">'
			. '<input type="hidden" name="[post_render_instance]" value="[render_instance]">'
			. '<input type="hidden" name="[post_token]" value="[token]">'
			. '<input type="hidden" name="[post_nonce]" value="[nonce]">'
			. '<button type="submit" class="button gv-bulk-actions-dismiss-job-button">[button]</button>'
			. '</form>',
			[
				'[action]'               => esc_url( $action_url ),
				'[post_dismiss]'         => esc_attr( Config::POST_DISMISS_JOB ),
				'[post_view]'            => esc_attr( Config::POST_VIEW_ID ),
				'[view_id]'              => esc_attr( (string) (int) $view->ID ),
				'[post_render_instance]' => esc_attr( Config::POST_RENDER_INSTANCE ),
				'[render_instance]'      => esc_attr( sanitize_key( (string) $render_instance ) ),
				'[post_token]'           => esc_attr( Config::POST_JOB_TOKEN ),
				'[token]'                => esc_attr( sanitize_key( $token ) ),
				'[post_nonce]'           => esc_attr( Config::POST_JOB_NONCE ),
				'[nonce]'                => esc_attr( wp_create_nonce( Config::get_background_job_nonce_action( $view, $token ) ) ),
				'[button]'               => esc_html__( 'Dismiss', 'gk-gravityview' ),
			]
		);
	}
}
