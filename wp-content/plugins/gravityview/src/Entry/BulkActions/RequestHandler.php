<?php
/**
 * Frontend bulk action request handling.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\Entry\BackgroundJobs\EntryBatches;
use GravityKit\GravityView\Entry\BackgroundJobs\EntryBatchResolver;
use GravityKit\GravityView\Entry\BackgroundJobs\EntrySelection;
use GravityKit\GravityView\BackgroundJobs\ResultNotice;
use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\Permissions\Permissions;
use GravityKit\GravityView\View\View;
use Throwable;
use WP_Error;

/**
 * Processes frontend bulk action POST requests.
 *
 * @since 3.0.0
 */
final class RequestHandler {
	/**
	 * @since 3.0.0
	 * @var Registry
	 */
	private $registry;

	/**
	 * @since 3.0.0
	 * @var ViewEligibility
	 */
	private $eligibility;

	/**
	 * @since 3.0.0
	 * @var EntryResolver
	 */
	private $entry_resolver;

	/**
	 * @since 3.0.0
	 * @var FlashMessages
	 */
	private $flash_messages;

	/**
	 * @since 3.0.0
	 * @var SelectionMode
	 */
	private $selection_mode;

	/**
	 * @since 3.0.0
	 * @var BackgroundStatus
	 */
	private $background_status;

	/**
	 * @since 3.0.0
	 * @var ViewActionLock
	 */
	private $view_lock;

	/**
	 * @since 3.0.0
	 *
	 * @param Registry            $registry          Action registry.
	 * @param ViewEligibility     $eligibility       View eligibility checker.
	 * @param EntryResolver       $entry_resolver    Entry resolver.
	 * @param FlashMessages       $flash_messages    Flash message service.
	 * @param SelectionMode       $selection_mode    Selected-results mode service.
	 * @param BackgroundStatus    $background_status Background job status service.
	 * @param ViewActionLock|null $view_lock         View-level action lock service.
	 */
	public function __construct(
		Registry $registry,
		ViewEligibility $eligibility,
		EntryResolver $entry_resolver,
		FlashMessages $flash_messages,
		SelectionMode $selection_mode,
		BackgroundStatus $background_status,
		?ViewActionLock $view_lock = null
	) {
		$this->registry          = $registry;
		$this->eligibility       = $eligibility;
		$this->entry_resolver    = $entry_resolver;
		$this->flash_messages    = $flash_messages;
		$this->selection_mode    = $selection_mode;
		$this->background_status = $background_status;
		$this->view_lock         = $view_lock ? $view_lock : new ViewActionLock();
	}

	/**
	 * Processes a submitted bulk action.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_request() {
		if ( ! empty( $_POST[ Config::POST_SHOW_SELECTED ] ) ) {
			return;
		}

		if ( empty( $_POST[ Config::POST_ACTION ] ) || empty( $_POST[ Config::POST_VIEW_ID ] ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		$view_id         = absint( wp_unslash( $_POST[ Config::POST_VIEW_ID ] ) );
		$render_instance = $this->get_request_render_instance();
		$view            = View::by_id( $view_id );

		if ( ! $view ) {
			return $this->flash_messages->redirect( $view_id, __( 'The View could not be found.', 'gk-gravityview' ), 'error', $render_instance );
		}

		$nonce = empty( $_POST[ Config::POST_NONCE ] ) ? '' : sanitize_text_field( wp_unslash( $_POST[ Config::POST_NONCE ] ) );

		if ( ! wp_verify_nonce( $nonce, Config::get_nonce_action( $view ) ) ) {
			if ( '' === $render_instance ) {
				return;
			}

			return $this->flash_messages->redirect( $view_id, __( 'The request was invalid. Refresh the page and try again.', 'gk-gravityview' ), 'error', $render_instance );
		}

		if ( ! $this->eligibility->is_enabled_for_view( $view ) ) {
			return $this->flash_messages->redirect( $view_id, __( 'Bulk actions are not enabled for this View.', 'gk-gravityview' ), 'error', $render_instance );
		}

		if ( '' === $render_instance ) {
			return $this->flash_messages->redirect_system(
				$view_id,
				__( 'The bulk action form is missing required data. Refresh the page and try again.', 'gk-gravityview' ),
				'error'
			);
		}

		$action_key = sanitize_key( wp_unslash( $_POST[ Config::POST_ACTION ] ) );
		$actions    = $this->registry->get_available_actions( $view );

		if ( empty( $actions[ $action_key ] ) ) {
			return $this->flash_messages->redirect( $view_id, __( 'The selected bulk action is not available.', 'gk-gravityview' ), 'error', $render_instance );
		}

		$selection = $this->get_background_selection_from_request( $view, $action_key, $actions[ $action_key ] );

		if ( is_wp_error( $selection ) ) {
			return $this->flash_messages->redirect( $view_id, $selection->get_error_message(), 'error', $render_instance );
		}

		$action_input = $this->get_action_input_from_request( $view, $action_key, $actions[ $action_key ], $this->get_action_request_context( $view, $selection, 'api-submit' ) );

		if ( is_wp_error( $action_input ) ) {
			return $this->flash_messages->redirect( $view_id, $action_input->get_error_message(), 'error', $render_instance );
		}

		$actions[ $action_key ]['request'] = $action_input;

		$selected_count = $this->get_selected_count( $view, $selection );

		if ( $this->should_process_in_background( $action_key, $actions[ $action_key ], $view, $selection, $selected_count ) ) {
			$lock = $this->acquire_view_lock( $view, $action_key, $actions[ $action_key ], true );

			if ( is_wp_error( $lock ) ) {
				return $this->flash_messages->redirect( $view_id, $lock->get_error_message(), 'error', $render_instance );
			}

			try {
				$result = $this->schedule_background_action( $action_key, $actions[ $action_key ], $view, $selection, $selected_count, is_array( $lock ) ? (string) ( $lock['result_token'] ?? '' ) : '', $render_instance );
			} catch ( Throwable $e ) {
				$this->release_view_lock( $view, $lock );

				$this->log_exception( 'Frontend bulk action background scheduling failed.', $e, [ 'action_key' => $action_key, 'view_id' => (int) $view->ID ] );

				return $this->flash_messages->redirect( $view_id, $this->get_schedule_exception_message( $action_key, $actions[ $action_key ] ), 'error', $render_instance );
			}

			if ( is_wp_error( $result ) ) {
				$this->release_view_lock( $view, $lock );

				return $this->flash_messages->redirect( $view_id, $result->get_error_message(), 'error', $render_instance );
			}

			$this->background_status->remember( $view_id, $result['result_token'], $render_instance );

			return $this->background_status->redirect( $view_id, $result['result_token'] );
		}

		$lock = $this->acquire_view_lock( $view, $action_key, $actions[ $action_key ], false );

		if ( is_wp_error( $lock ) ) {
			return $this->flash_messages->redirect( $view_id, $lock->get_error_message(), 'error', $render_instance );
		}

		$entries = $this->entry_resolver->resolve_from_request( $view );

		if ( is_wp_error( $entries ) ) {
			$this->release_view_lock( $view, $lock );

			return $this->flash_messages->redirect( $view_id, $entries->get_error_message(), 'error', $render_instance );
		}

		try {
			$result = $this->execute_action( $action_key, $actions[ $action_key ], $entries, $view );
		} catch ( Throwable $e ) {
			$this->release_view_lock( $view, $lock );

			$this->log_exception( 'Frontend bulk action callback failed.', $e, [ 'action_key' => $action_key, 'view_id' => (int) $view->ID ] );

			return $this->flash_messages->redirect( $view_id, $this->get_callback_exception_message( $e, $action_key, $actions[ $action_key ] ), 'error', $render_instance );
		}

		if ( is_wp_error( $result ) ) {
			$this->release_view_lock( $view, $lock );

			return $this->flash_messages->redirect( $view_id, $result->get_error_message(), 'error', $render_instance );
		}

		$this->release_view_lock( $view, $lock );

		return $this->flash_messages->redirect( $view_id, $this->get_result_notice( $result, $action_key, $actions[ $action_key ], count( $entries ) ), 'success', $render_instance );
	}

	/**
	 * Validates action-specific submitted data without processing entries.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_ajax_validation_request() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error(
				[
					'message' => __( 'The bulk action request was invalid.', 'gk-gravityview' ),
				],
				403
			);
		}

		$view_id         = empty( $_POST[ Config::POST_VIEW_ID ] ) ? 0 : absint( wp_unslash( $_POST[ Config::POST_VIEW_ID ] ) );
		$render_instance = $this->get_request_render_instance();
		$action_key      = empty( $_POST[ Config::POST_ACTION ] ) ? '' : sanitize_key( wp_unslash( $_POST[ Config::POST_ACTION ] ) );
		$view            = $view_id ? View::by_id( $view_id ) : null;

		if ( ! $view || '' === $action_key ) {
			wp_send_json_error(
				[
					'message' => __( 'The bulk action request was invalid.', 'gk-gravityview' ),
				],
				404
			);
		}

		$nonce = empty( $_POST[ Config::POST_NONCE ] ) ? '' : sanitize_text_field( wp_unslash( $_POST[ Config::POST_NONCE ] ) );

		if ( ! wp_verify_nonce( $nonce, Config::get_nonce_action( $view ) ) ) {
			wp_send_json_error(
				[
					'message' => __( 'The bulk action request was invalid.', 'gk-gravityview' ),
				],
				403
			);
		}

		if ( ! $this->eligibility->is_enabled_for_view( $view ) || '' === $render_instance ) {
			wp_send_json_error(
				[
					'message' => __( 'The bulk action request was invalid.', 'gk-gravityview' ),
				],
				400
			);
		}

		$actions = $this->registry->get_available_actions( $view );

		if ( empty( $actions[ $action_key ] ) ) {
			wp_send_json_error(
				[
					'message' => __( 'The selected bulk action is not available.', 'gk-gravityview' ),
				],
				400
			);
		}

		$selection = $this->get_background_selection_from_request( $view, $action_key, $actions[ $action_key ] );

		if ( is_wp_error( $selection ) ) {
			wp_send_json_error(
				[
					'message' => $selection->get_error_message(),
				],
				400
			);
		}

		$action_input = $this->get_action_input_from_request( $view, $action_key, $actions[ $action_key ], $this->get_action_request_context( $view, $selection, 'api-validate' ) );

		if ( is_wp_error( $action_input ) ) {
			$error_data = $this->get_ajax_error_data( $action_input );

			wp_send_json_error(
				$error_data,
				400
			);
		}

		wp_send_json_success(
			[
				'message' => __( 'The bulk action data is valid.', 'gk-gravityview' ),
			]
		);
	}

	/**
	 * Processes an AJAX action setting options request.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_ajax_setting_options_request() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error(
				[
					'message' => __( 'The bulk action settings request was invalid.', 'gk-gravityview' ),
				],
				403
			);
		}

		$nonce = empty( $_POST['nonce'] ) ? '' : sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'gravityview_ajaxviews' ) ) {
			wp_send_json_error(
				[
					'message' => __( 'The bulk action settings request was invalid.', 'gk-gravityview' ),
				],
				403
			);
		}

		$view_id     = empty( $_POST['view_id'] ) ? 0 : absint( wp_unslash( $_POST['view_id'] ) );
		$action_key  = empty( $_POST['action_key'] ) ? '' : sanitize_key( wp_unslash( $_POST['action_key'] ) );
		$setting_key = empty( $_POST['setting_key'] ) ? '' : sanitize_key( wp_unslash( $_POST['setting_key'] ) );
		$view        = $view_id ? View::by_id( $view_id ) : null;

		if ( ! $view || '' === $action_key || '' === $setting_key ) {
			wp_send_json_error(
				[
					'message' => __( 'The bulk action settings request was invalid.', 'gk-gravityview' ),
				],
				404
			);
		}

		if ( ! ( new Permissions() )->can_edit_view( (int) $view->ID ) ) {
			wp_send_json_error(
				[
					'message' => __( 'You are not allowed to edit this View.', 'gk-gravityview' ),
				],
				403
			);
		}

		$actions = Registry::get_actions( $view );

		if ( empty( $actions[ $action_key ] ) ) {
			wp_send_json_error(
				[
					'message' => __( 'The selected bulk action is not available.', 'gk-gravityview' ),
				],
				404
			);
		}

		$actions[ $action_key ]['settings'] = $this->get_current_drawer_state_from_request();
		$schema                            = Config::get_action_settings_schema( $actions[ $action_key ], $view, $action_key );
		$setting                           = $schema[ $setting_key ] ?? null;

		if ( ! is_array( $setting ) || ! in_array( $setting['type'] ?? '', [ 'select', 'multiselect' ], true ) ) {
			wp_send_json_error(
				[
					'message' => __( 'The bulk action setting is not available.', 'gk-gravityview' ),
				],
				404
			);
		}

		$options = isset( $setting['options'] ) && is_array( $setting['options'] ) ? $setting['options'] : [];

		wp_send_json_success(
			[
				'options'               => array_map( 'strval', $options ),
				'empty_options_message' => (string) ( $setting['empty_options_message'] ?? '' ),
			]
		);
	}

	/**
	 * Returns current unsaved action setting values from an AJAX request.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	private function get_current_drawer_state_from_request() {
		$state = isset( $_POST['current_drawer_state'] ) ? wp_unslash( $_POST['current_drawer_state'] ) : [];

		if ( ! is_array( $state ) ) {
			return [];
		}

		return map_deep( $state, 'sanitize_text_field' );
	}

	/**
	 * Executes an action callback.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param array  $entries    Entries keyed by ID.
	 * @param View   $view       View.
	 *
	 * @return mixed
	 */
	private function execute_action( $action_key, array $action, array $entries, View $view ) {
		return call_user_func( $action['callback'], array_keys( $entries ), $entries, $view, $action_key, $action );
	}

	/**
	 * Returns the JSON error payload for action input validation errors.
	 *
	 * @since 3.0.0
	 *
	 * @param WP_Error $error Validation error.
	 *
	 * @return array{
	 *     message:string,
	 *     field_errors?:array<string,string>
	 * }
	 */
	private function get_ajax_error_data( WP_Error $error ) {
		$message = $error->get_error_message();
		$data    = $error->get_error_data( $error->get_error_code() );
		$payload = [
			'message' => $message,
		];

		if ( is_array( $data ) && ! empty( $data['field_id'] ) ) {
			$field_id = $this->normalize_ajax_field_error_id( $data['field_id'] );

			if ( $field_id ) {
				$payload['field_errors'] = [
					$field_id => $message,
				];
			}
		}

		if ( is_array( $data ) && ! empty( $data['field_errors'] ) && is_array( $data['field_errors'] ) ) {
			$payload['field_errors'] = [];

			foreach ( $data['field_errors'] as $field_id => $field_message ) {
				$field_id = $this->normalize_ajax_field_error_id( $field_id );

				if ( ! $field_id ) {
					continue;
				}

				$payload['field_errors'][ $field_id ] = (string) $field_message;
			}
		}

		return $payload;
	}

	/**
	 * Normalizes an AJAX field error target ID.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $field_id Field or entry-property target ID.
	 *
	 * @return string
	 */
	private function normalize_ajax_field_error_id( $field_id ) {
		if ( ! is_scalar( $field_id ) ) {
			return '';
		}

		if ( is_int( $field_id ) || ctype_digit( (string) $field_id ) ) {
			$field_id = absint( $field_id );

			return $field_id ? (string) $field_id : '';
		}

		$field_id = sanitize_text_field( wp_unslash( (string) $field_id ) );

		if ( '' === $field_id || ! preg_match( '/^[A-Za-z0-9_.:-]+$/', $field_id ) ) {
			return '';
		}

		return $field_id;
	}

	/**
	 * Builds a background entry selection from the current request.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 *
	 * @return EntrySelection|\WP_Error
	 */
	private function get_background_selection_from_request( View $view, $action_key, array $action ) {
		$request_args = $this->entry_resolver->get_request_args_for_background_selection();
		$parse_limit  = $this->get_entry_id_parse_limit( $view, $action_key, $action );

		if ( $this->entry_resolver->is_select_all_request() ) {
			if ( ! Config::is_cross_page_selection_enabled( $view ) ) {
				return new \WP_Error( 'gravityview_bulk_select_all_disabled', __( 'Selecting entries across pages is not enabled for this View.', 'gk-gravityview' ) );
			}

			$excluded_entry_ids = $this->entry_resolver->get_excluded_entry_ids( $view, $parse_limit );

			if ( is_wp_error( $excluded_entry_ids ) ) {
				return $excluded_entry_ids;
			}

			return EntryBatches::all( $excluded_entry_ids, $request_args );
		}

		$entry_ids = $this->entry_resolver->get_requested_entry_ids( $view, $parse_limit );

		if ( is_wp_error( $entry_ids ) ) {
			return $entry_ids;
		}

		return EntryBatches::selected( $entry_ids, $request_args );
	}

	/**
	 * Returns the ID parse limit for building a background-capable selection descriptor.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 *
	 * @return int
	 */
	private function get_entry_id_parse_limit( View $view, $action_key, array $action ) {
		$sync_limit = Config::get_max_entry_ids( $view );

		if ( Config::is_action_background_processing_enabled( $action_key, $action, $view ) ) {
			return max( $sync_limit, Config::MAX_ENTRY_IDS );
		}

		return $sync_limit;
	}

	/**
	 * Returns the selected entry count when known.
	 *
	 * @since 3.0.0
	 *
	 * @param View           $view      View.
	 * @param EntrySelection $selection Selection descriptor.
	 *
	 * @return int|null
	 */
	private function get_selected_count( View $view, EntrySelection $selection ) {
		if ( $selection->is_explicit() ) {
			return $selection->known_count();
		}

		return $this->entry_resolver->get_selected_count_from_request( $view, $selection->excluded_ids() );
	}

	/**
	 * Whether a submitted bulk action should be queued.
	 *
	 * @since 3.0.0
	 *
	 * @param string         $action_key     Action key.
	 * @param array          $action         Action config.
	 * @param View           $view           View.
	 * @param EntrySelection $selection      Selection descriptor.
	 * @param int|null       $selected_count Selected entry count, when known.
	 *
	 * @return bool
	 */
	private function should_process_in_background( $action_key, array $action, View $view, EntrySelection $selection, $selected_count ) {
		$background = Config::get_action_background_config( $action );

		if ( empty( $background['enabled'] ) || ! Config::is_action_background_processing_enabled( $action_key, $action, $view ) ) {
			$use_background = false;
		} else {
			$threshold      = Config::get_background_queue_threshold( $action_key, $action, $view );
			$use_background = ! empty( $background['always'] )
				|| ( $selection->is_all() && ( null === $selected_count || $selected_count >= $threshold ) )
				|| ( $selection->is_explicit() && null !== $selected_count && $selected_count >= $threshold );
		}

		/**
		 * Filters whether a frontend bulk action should use background processing.
		 *
		 * @since 3.0.0
		 *
		 * @param bool           $use_background Whether to queue the action.
		 * @param int            $view_id        View ID.
		 * @param View           $view           View.
		 * @param string         $action_key     Action key.
		 * @param array          $action         Action config.
		 * @param EntrySelection $selection      Selection descriptor.
		 * @param int|null       $selected_count Selected entry count, when known.
		 */
		return (bool) apply_filters( 'gk/gravityview/bulk-actions/use-background-processing', $use_background, (int) $view->ID, $view, $action_key, $action, $selection, $selected_count );
	}

	/**
	 * Schedules a bulk action background job.
	 *
	 * @since 3.0.0
	 *
	 * @param string         $action_key     Action key.
	 * @param array          $action         Action config.
	 * @param View           $view           View.
	 * @param EntrySelection $selection      Selection descriptor.
	 * @param int|null       $selected_count Selected entry count, when known.
	 * @param string         $result_token   Existing result token, when a View lock already created one.
	 * @param string         $render_instance Render instance ID.
	 *
	 * @return array|\WP_Error
	 */
	private function schedule_background_action( $action_key, array $action, View $view, EntrySelection $selection, $selected_count, $result_token = '', $render_instance = '' ) {
		$background      = Config::get_action_background_config( $action );
		$batch_size      = isset( $background['batch_size'] ) ? max( 1, (int) $background['batch_size'] ) : Config::get_background_batch_size( $view );
		$result_token    = sanitize_key( (string) $result_token );
		$render_instance = sanitize_key( (string) $render_instance );

		if ( $selection->is_all() ) {
			$snapshot_limit = Config::get_select_all_snapshot_limit( $view );

			if ( null !== $selected_count && $selected_count > $snapshot_limit ) {
				return $this->get_select_all_snapshot_limit_error( $snapshot_limit );
			}

			try {
				$entry_ids = ( new EntryBatchResolver() )->snapshot_entry_ids( $view, $selection, max( EntryBatchResolver::DEFAULT_BATCH_SIZE, $batch_size ), null, $snapshot_limit );
			} catch ( \OverflowException $e ) {
				return $this->get_select_all_snapshot_limit_error( $snapshot_limit );
			}

			$selection      = EntryBatches::selected( $entry_ids, $selection->request_args() );
			$selected_count = count( $entry_ids );
		}

		if ( '' === $result_token ) {
			$result_token = ( new ResultStore() )->create_token();
		}

		$job_name = 'bulk_action_' . sanitize_key( $action_key ) . '_' . (int) $view->ID
			. ( '' !== $render_instance ? '_' . $render_instance : '' )
			. '_r' . substr( hash( 'sha256', $result_token ), 0, 8 );

		$args = [
			'render_instance' => $render_instance,
			'result_token'    => $result_token,
			'callback'        => [ BackgroundProcessor::class, 'process' ],
			'callback_args'   => [
				'action_key'      => $action_key,
				'action_label'    => $action['label'] ?? $action_key,
				'action_request'  => $action['request'] ?? [],
				'action_settings' => $action['settings'] ?? [],
				'queued_message'  => $this->get_background_queued_message( $action_key, $action, $selected_count ),
			],
			'batch_size'      => $batch_size,
			'job_name'        => $job_name,
			'label'           => strtr(
				/* translators: [action] is the bulk action label. [view_id] is the View ID. */
				__( 'GravityView Bulk Action: [action] (View #[view_id])', 'gk-gravityview' ),
				[
					'[action]'  => $action['label'] ?? $action_key,
					'[view_id]' => (int) $view->ID,
				]
			),
		];

		/**
		 * Filters the entry-batch job arguments for a frontend bulk action.
		 *
		 * @since 3.0.0
		 *
		 * @param array          $args           Entry-batch job arguments.
		 * @param int            $view_id        View ID.
		 * @param View           $view           View.
		 * @param string         $action_key     Action key.
		 * @param array          $action         Action config.
		 * @param EntrySelection $selection      Selection descriptor.
		 * @param int|null       $selected_count Selected entry count, when known.
		 */
		$args = (array) apply_filters( 'gk/gravityview/bulk-actions/background-job-args', $args, (int) $view->ID, $view, $action_key, $action, $selection, $selected_count );

		// The result token is created before filters so default job names can be unique, but the token itself is internal state and must remain stable.
		$args['result_token'] = $result_token;

		return EntryBatches::schedule( $view, $selection, $args );
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
	 * Returns sanitized action-specific submitted data.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 *
	 * @return array|\WP_Error
	 */
	private function get_action_input_from_request( View $view, $action_key, array $action, array $context = [] ) {
		$raw_input = [];

		if ( isset( $_POST[ Config::POST_ACTION_INPUT ][ $action_key ] ) && is_array( $_POST[ Config::POST_ACTION_INPUT ][ $action_key ] ) ) {
			$raw_input = wp_unslash( $_POST[ Config::POST_ACTION_INPUT ][ $action_key ] );
		}

		if ( empty( $action['request_callback'] ) || ! is_callable( $action['request_callback'] ) ) {
			return [];
		}

		try {
			$input = call_user_func( $action['request_callback'], $raw_input, $view, $action_key, $action, $context );
		} catch ( Throwable $e ) {
			$this->log_exception( 'Frontend bulk action request callback failed.', $e, [ 'action_key' => $action_key, 'view_id' => (int) $view->ID ] );

			return new WP_Error(
				'gravityview_bulk_action_request_callback_failed',
				$this->get_callback_exception_message( $e, $action_key, $action )
			);
		}

		if ( is_wp_error( $input ) ) {
			return $input;
		}

		if ( ! is_array( $input ) ) {
			return [];
		}

		$input = $this->normalize_action_input( $input );

		if ( is_wp_error( $input ) ) {
			return $input;
		}

		return $input;
	}

	/**
	 * Builds context for action request sanitization.
	 *
	 * @since 3.0.0
	 *
	 * @param View           $view      View.
	 * @param EntrySelection $selection Selection descriptor.
	 * @param string         $validation_context Gravity Forms validation context.
	 *
	 * @return array
	 */
	private function get_action_request_context( View $view, EntrySelection $selection, $validation_context ) {
		return [
			'representative_entry' => $this->get_representative_entry( $view, $selection ),
			'validation_context'   => $validation_context,
		];
	}

	/**
	 * Returns one selected entry for validation callbacks that need entry context.
	 *
	 * @since 3.0.0
	 *
	 * @param View           $view      View.
	 * @param EntrySelection $selection Selection descriptor.
	 *
	 * @return array
	 */
	private function get_representative_entry( View $view, EntrySelection $selection ) {
		$form_id = $view->form && ! empty( $view->form->ID ) ? (int) $view->form->ID : 0;

		if ( $selection->is_explicit() ) {
			$selected_ids = array_fill_keys( array_map( 'absint', $selection->entry_ids() ), true );

			try {
				foreach ( $view->get_entries()->all() as $entry ) {
					$entry_array = $entry->as_entry();
					$entry_id    = empty( $entry_array['id'] ) ? 0 : (int) $entry_array['id'];

					if ( $entry_id && isset( $selected_ids[ $entry_id ] ) && ( ! $form_id || (int) ( $entry_array['form_id'] ?? 0 ) === $form_id ) ) {
						return $entry_array;
					}
				}
			} catch ( Throwable $e ) {
				return [];
			}

			return [];
		}

		try {
			foreach ( $view->get_entries()->all() as $entry ) {
				$entry_array = $entry->as_entry();
				$entry_id    = empty( $entry_array['id'] ) ? 0 : (int) $entry_array['id'];

				if ( $entry_id && ! in_array( $entry_id, $selection->excluded_ids(), true ) ) {
					return $entry_array;
				}
			}
		} catch ( Throwable $e ) {
			return [];
		}

		return [];
	}

	/**
	 * Normalizes action input so it can be stored in a background job safely.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value Submitted action input.
	 * @param int   $depth Current recursion depth.
	 *
	 * @return mixed|\WP_Error
	 */
	private function normalize_action_input( $value, $depth = 0 ) {
		if ( $depth > 8 ) {
			return new \WP_Error( 'gravityview_bulk_action_input_too_deep', __( 'The bulk action data is invalid.', 'gk-gravityview' ) );
		}

		if ( null === $value || is_scalar( $value ) ) {
			return $value;
		}

		if ( ! is_array( $value ) ) {
			return new \WP_Error( 'gravityview_bulk_action_input_invalid', __( 'The bulk action data is invalid.', 'gk-gravityview' ) );
		}

		$normalized = [];

		foreach ( $value as $key => $item ) {
			$item = $this->normalize_action_input( $item, $depth + 1 );

			if ( is_wp_error( $item ) ) {
				return $item;
			}

			$normalized[ $key ] = $item;
		}

		return $normalized;
	}

	/**
	 * Acquires a View lock when the action requires one.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param bool   $background Whether the action is being queued.
	 *
	 * @return array|\WP_Error|null
	 */
	private function acquire_view_lock( View $view, $action_key, array $action, $background ) {
		if ( ! Config::action_uses_view_lock( $action_key, $action, $view ) ) {
			return null;
		}

		return $this->view_lock->acquire( $view, $action_key, $action, (bool) $background );
	}

	/**
	 * Releases a View lock created by this request.
	 *
	 * @since 3.0.0
	 *
	 * @param View       $view View.
	 * @param array|null $lock Lock data.
	 *
	 * @return void
	 */
	private function release_view_lock( View $view, $lock ) {
		if ( ! is_array( $lock ) ) {
			return;
		}

		$this->view_lock->release( $view, (string) ( $lock['result_token'] ?? '' ) );
	}

	/**
	 * Returns the queued background job message.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $action_key     Action key.
	 * @param array    $action         Action config.
	 * @param int|null $selected_count Selected entry count, when known.
	 *
	 * @return string
	 */
	private function get_background_queued_message( $action_key, array $action, $selected_count ) {
		$background = Config::get_action_background_config( $action );

		if ( ! empty( $background['queued_message'] ) && is_string( $background['queued_message'] ) ) {
			return strtr(
				$background['queued_message'],
				[
					'[action]' => $action['label'] ?? $action_key,
					'[count]'  => null === $selected_count ? '' : number_format_i18n( $selected_count ),
				]
			);
		}

		return strtr(
			/* translators: [action] is the bulk action label. */
			__( '[action] is running in the background.', 'gk-gravityview' ),
			[
				'[action]' => $action['label'] ?? $action_key,
			]
		);
	}

	/**
	 * Returns the notice for a callback result.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed  $result      Callback result.
	 * @param string $action_key  Action key.
	 * @param array  $action      Action config.
	 * @param int    $entry_count Entry count.
	 *
	 * @return array{message:string}
	 */
	private function get_result_notice( $result, $action_key, array $action, $entry_count ) {
		if ( is_array( $result ) && ( ! empty( $result['message'] ) || ! empty( $result['notice'] ) ) ) {
			return ResultNotice::normalize( $result );
		}

		if ( is_string( $result ) ) {
			return ResultNotice::normalize( $result );
		}

		return ResultNotice::normalize(
			strtr(
				/* translators: [count] is the number of processed entries. [action] is the bulk action label. */
				__( '[count] entries processed by "[action]".', 'gk-gravityview' ),
				[
					'[count]'  => $entry_count,
					'[action]' => $action['label'] ?? $action_key,
				]
			)
		);
	}

	/**
	 * Returns a user-facing message for unexpected callback exceptions.
	 *
	 * @since 3.0.0
	 *
	 * @param Throwable $e          Exception.
	 * @param string    $action_key Action key.
	 * @param array     $action     Action config.
	 *
	 * @return string
	 */
	private function get_callback_exception_message( Throwable $e, $action_key, array $action ) {
		unset( $e );

		$label = wp_strip_all_tags( (string) ( $action['label'] ?? $action_key ) );

		return strtr(
			/* translators: [action] is the bulk action label. */
			__( '[action] could not be processed.', 'gk-gravityview' ),
			[
				'[action]' => $label,
			]
		);
	}

	/**
	 * Returns a user-facing message for unexpected background scheduling exceptions.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 *
	 * @return string
	 */
	private function get_schedule_exception_message( $action_key, array $action ) {
		return strtr(
			/* translators: [action] is the bulk action label. */
			__( '[action] could not be queued. Refresh the page and try again.', 'gk-gravityview' ),
			[
				'[action]' => wp_strip_all_tags( (string) ( $action['label'] ?? $action_key ) ),
			]
		);
	}

	/**
	 * Returns an error when a select-all snapshot would be too large to queue safely.
	 *
	 * @since 3.0.0
	 *
	 * @param int $max Maximum snapshot size.
	 *
	 * @return WP_Error
	 */
	private function get_select_all_snapshot_limit_error( $max ) {
		return new WP_Error(
			'gravityview_bulk_select_all_snapshot_too_large',
			strtr(
				/* translators: [count] is the maximum number of entries allowed in a select-all background action snapshot. */
				_n( 'Select [count] entry or fewer at a time.', 'Select [count] entries or fewer at a time.', $max, 'gk-gravityview' ),
				[
					'[count]' => $max,
				]
			)
		);
	}

	/**
	 * Logs unexpected callback exceptions.
	 *
	 * @since 3.0.0
	 *
	 * @param string    $message Log message.
	 * @param Throwable $e       Exception.
	 * @param array     $context Extra log context.
	 *
	 * @return void
	 */
	private function log_exception( $message, Throwable $e, array $context = [] ) {
		if ( ! function_exists( 'gravityview' ) || ! gravityview() || empty( gravityview()->log ) || ! method_exists( gravityview()->log, 'error' ) ) {
			return;
		}

		gravityview()->log->error(
			$message,
			array_merge(
				[
					'exception' => get_class( $e ),
					'message'   => $e->getMessage(),
				],
				$context
			)
		);
	}
}
