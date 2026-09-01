<?php
/**
 * Frontend resend notifications bulk action.
 *
 * @package GravityKit\GravityView\Entry\BulkActions\Actions
 * @since 3.0.0-beta.3
 */

namespace GravityKit\GravityView\Entry\BulkActions\Actions;

use GFAPI;
use GFCommon;
use GFForms;
use Gravity_Forms\Gravity_Forms\Async\GF_Background_Process_Service_Provider;
use GravityKit\GravityView\Entry\BulkActions\Config;
use GravityKit\GravityView\View\View;
use GVCommon;
use Throwable;
use WP_Error;

/**
 * Sends selected Gravity Forms notifications for selected entries.
 *
 * @since 3.0.0-beta.3
 */
final class ResendNotificationsAction implements BulkAction {
	private const SETTING_NOTIFICATIONS          = 'notifications';
	private const SETTING_ALLOW_SEND_TO_OVERRIDE = 'allow_send_to_override';
	private const REQUEST_NOTIFICATIONS          = 'notifications';
	private const REQUEST_SEND_TO                = 'send_to';
	private const OUTCOME_PROCESSED              = 'processed';
	private const OUTCOME_SKIPPED                = 'skipped';

	/**
	 * @inheritdoc
	 * @since 3.0.0-beta.3
	 */
	public function key() {
		return 'resend_notifications';
	}

	/**
	 * @inheritdoc
	 * @since 3.0.0-beta.3
	 */
	public function config() {
		return [
			'label'                    => __( 'Resend Notifications', 'gk-gravityview' ),
			'callback'                 => [ $this, 'process' ],
			'available_callback'       => [ $this, 'is_available' ],
			'capability'               => $this->get_capability(),
			'confirmation'             => [
				'enabled'      => true,
				'title'        => __( 'Resend notifications?', 'gk-gravityview' ),
				'message'      => __( 'Eligible notifications will be processed for the selected entries.', 'gk-gravityview' ),
				'action_label' => __( 'Send notifications', 'gk-gravityview' ),
			],
			'request_callback'         => [ $this, 'sanitize_request' ],
			'frontend_data_callback'   => [ $this, 'get_frontend_data' ],
			'supports_multi_form_view' => true,
			'settings_schema'          => [
				self::SETTING_NOTIFICATIONS          => [
					'type'                  => 'multiselect',
					'label'                 => __( 'Allowed notifications', 'gk-gravityview' ),
					'default'               => [],
					'options_callback'      => [ $this, 'get_notification_setting_options' ],
					'empty_options_message' => [ $this, 'get_empty_notification_options_message' ],
					'class'                 => 'gv-tom-select',
					'placeholder'           => __( 'Select notifications', 'gk-gravityview' ),
					'submit_empty_value'    => true,
					'desc'                  => __( 'Only selected notifications can be sent from the front end.', 'gk-gravityview' ),
				],
				self::SETTING_ALLOW_SEND_TO_OVERRIDE => [
					'type'    => 'checkbox',
					'label'   => __( 'Allow Send to override', 'gk-gravityview' ),
					'default' => 0,
					'desc'    => __( 'Allow users to send selected notifications to one email address instead of the notification recipients.', 'gk-gravityview' ),
				],
			],
			'background'               => [
				'enabled'             => true,
				'batch_size'          => 1,
				'threshold'           => 5,
				'default_enabled'     => true,
				'sticky_result'       => true,
				'completion_behavior' => Config::BACKGROUND_COMPLETE_SHOW_MESSAGE,
				'queued_message'      => __( 'Sending notifications in the background.', 'gk-gravityview' ),
				'complete_callback'   => [ $this, 'complete' ],
			],
			'lock'                     => true,
		];
	}

	/**
	 * Returns the capability required to use this action.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @return string
	 */
	public function get_capability() {
		/**
		 * Filters the capability required to resend notifications from frontend bulk actions.
		 *
		 * @since 3.0.0-beta.3
		 *
		 * @param string $capability Required capability.
		 * @param string $action_key Action key.
		 */
		return (string) apply_filters( 'gk/gravityview/bulk-actions/resend-notifications/capability', 'gravityview_edit_entries', $this->key() );
	}

	/**
	 * Returns flattened notification picker options for the action settings UI.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View|null $view       View context.
	 * @param array     $action     Action config.
	 * @param string    $action_key Action key.
	 * @param array     $setting    Setting schema.
	 *
	 * @return array
	 */
	public function get_notification_setting_options( ?View $view = null, array $action = [], $action_key = '', array $setting = [] ) {
		unset( $action, $action_key, $setting );

		$options = [];

		foreach ( $this->get_notification_map( $view, false ) as $token => $item ) {
			$options[ $token ] = $item['admin_label'];
		}

		natcasesort( $options );

		return $options;
	}

	/**
	 * Returns the admin empty-state message when no resend notifications are available.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View|null $view       View context.
	 * @param array     $action     Action config.
	 * @param string    $action_key Action key.
	 * @param array     $setting    Setting schema.
	 *
	 * @return string
	 */
	public function get_empty_notification_options_message( ?View $view = null, array $action = [], $action_key = '', array $setting = [] ) {
		unset( $action, $action_key, $setting );

		$forms = $this->get_view_forms( $view );

		if ( [] === $forms ) {
			return strtr(
				/* translators: [action] is the Resend Notifications bulk action label. */
				__( 'Select a form to choose allowed notifications for [action].', 'gk-gravityview' ),
				[
					'[action]' => __( 'Resend Notifications', 'gk-gravityview' ),
				]
			);
		}

		if ( [] !== $this->get_notification_map( $view, false ) ) {
			return '';
		}

		if ( 1 === count( $forms ) ) {
			$form_id = (int) key( $forms );

			return strtr(
				/* translators: [link]Create a notification[/link] links to the Gravity Forms notification settings screen. */
				__( 'No resend-eligible notifications are configured for this form. [link]Create a notification[/link].', 'gk-gravityview' ),
				[
					'[link]'  => '<a href="' . esc_url( $this->get_form_notifications_admin_url( $form_id ) ) . '" target="_blank" rel="noopener noreferrer">',
					'[/link]' => '</a>',
				]
			);
		}

		$links = [];

		foreach ( $forms as $form_id => $form ) {
			$label   = (string) ( $form['title'] ?? $form_id );
			$links[] = '<a href="' . esc_url( $this->get_form_notifications_admin_url( (int) $form_id ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $label ) . '</a>';
		}

		return strtr(
			/* translators: [links] is a comma-separated list of form links to the Gravity Forms notification settings screen. */
			__( 'No resend-eligible notifications are configured for this View. Create one for: [links].', 'gk-gravityview' ),
			[
				'[links]' => implode( ', ', $links ),
			]
		);
	}

	/**
	 * Sanitizes submitted resend notification data.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $input      Raw action input.
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 *
	 * @return array|WP_Error
	 */
	public function sanitize_request( array $input, View $view, $action_key = '', array $action = [] ) {
		unset( $action_key );

		$allowed = $this->get_allowed_notification_map( $view, $action, false );
		$tokens  = ResendNotificationToken::normalize_list( $input[ self::REQUEST_NOTIFICATIONS ] ?? [] );

		if ( [] === $tokens ) {
			return $this->request_error(
				'gravityview_bulk_resend_notifications_missing',
				__( 'Select at least one notification.', 'gk-gravityview' ),
				self::REQUEST_NOTIFICATIONS
			);
		}

		foreach ( $tokens as $token ) {
			if ( ! isset( $allowed[ $token ] ) ) {
				return $this->request_error(
					'gravityview_bulk_resend_notifications_invalid',
					__( 'One or more selected notifications are no longer available.', 'gk-gravityview' ),
					self::REQUEST_NOTIFICATIONS
				);
			}
		}

		$send_to = '';

		if ( $this->send_to_override_allowed( $action ) && isset( $input[ self::REQUEST_SEND_TO ] ) ) {
			$send_to = sanitize_text_field( (string) $input[ self::REQUEST_SEND_TO ] );

			if ( '' !== $send_to && ! is_email( $send_to ) ) {
				return $this->request_error(
					'gravityview_bulk_resend_notifications_invalid_email',
					__( 'Enter a valid Send to email address.', 'gk-gravityview' ),
					self::REQUEST_SEND_TO
				);
			}

			$send_to = '' === $send_to ? '' : sanitize_email( $send_to );
		}

		return [
			self::REQUEST_NOTIFICATIONS => $tokens,
			self::REQUEST_SEND_TO       => $send_to,
		];
	}

	/**
	 * Returns frontend data for the picker modal.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View  $view   View.
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	public function get_frontend_data( View $view, array $action = [] ) {
		$notifications = array_values( $this->get_allowed_notification_map( $view, $action, true ) );
		$groups        = [];

		foreach ( $notifications as $notification ) {
			$form_id = (int) $notification['form_id'];

			if ( ! isset( $groups[ $form_id ] ) ) {
				$groups[ $form_id ] = [
					'formId'        => $form_id,
					'label'         => $notification['form_label'],
					'notifications' => [],
				];
			}

			$groups[ $form_id ]['notifications'][] = [
				'token'      => $notification['token'],
				'id'         => $notification['notification_id'],
				'label'      => $notification['notification_label'],
				'formId'     => $form_id,
				'formLabel'  => $notification['form_label'],
				'adminLabel' => $notification['admin_label'],
			];
		}

		return [
			'notifications'         => array_map(
				static function ( $notification ) {
					return [
						'token'      => $notification['token'],
						'id'         => $notification['notification_id'],
						'label'      => $notification['notification_label'],
						'formId'     => (int) $notification['form_id'],
						'formLabel'  => $notification['form_label'],
						'adminLabel' => $notification['admin_label'],
					];
				},
				$notifications
			),
			'groups'                => array_values( $groups ),
			'sendToOverrideAllowed' => $this->send_to_override_allowed( $action ),
		];
	}

	/**
	 * Whether this action should appear for the View.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View  $view   View.
	 * @param array $action Action config.
	 *
	 * @return bool
	 */
	public function is_available( View $view, array $action = [] ) {
		return [] !== $this->get_allowed_notification_map( $view, $action, true );
	}

	/**
	 * Sends the selected notifications for selected entries.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int[]  $entry_ids  Entry IDs.
	 * @param array  $entries    Entries keyed by ID.
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param array  $context    Processing context.
	 *
	 * @return array|WP_Error
	 */
	public function process( array $entry_ids, array $entries, View $view, $action_key = '', array $action = [], array $context = [] ) {
		unset( $entry_ids, $action_key );

		$request = isset( $action['request'] ) && is_array( $action['request'] ) ? $action['request'] : [];
		$tokens  = ResendNotificationToken::normalize_list( $request[ self::REQUEST_NOTIFICATIONS ] ?? [] );

		if ( [] === $tokens ) {
			return new WP_Error( 'gravityview_bulk_resend_notifications_missing', __( 'No notifications were selected.', 'gk-gravityview' ) );
		}

		$allowed         = $this->get_allowed_notification_map( $view, $action, false );
		$send_to         = $this->get_process_send_to( $action, $request );
		$view_form_ids   = array_fill_keys( array_keys( $this->get_view_forms( $view ) ), true );
		$processed       = 0;
		$failed          = 0;
		$skipped         = 0;
		$notifications   = 0;
		$sent_count      = 0;
		$queued_count    = 0;
		$last_sent_form  = null;
		$last_sent_entry = null;
		$last_error      = '';
		$is_background   = $this->is_background_context( $context );

		foreach ( $this->expand_entries( $entries ) as $entry ) {
			$form_id          = (int) ( $entry['form_id'] ?? 0 );
			$matched_entry    = false;
			$processed_entry  = false;
			$failed_entry     = false;
			$skipped_entry    = false;
			$last_entry_error = '';

			if ( ! $form_id || ! isset( $view_form_ids[ $form_id ] ) ) {
				continue;
			}

			foreach ( $tokens as $token ) {
				$parsed = ResendNotificationToken::parse( $token );

				if ( null === $parsed || $parsed['form_id'] !== $form_id ) {
					continue;
				}

				$matched_entry = true;

				if ( empty( $allowed[ $token ] ) ) {
					$last_entry_error = __( 'One or more selected notifications are no longer available.', 'gk-gravityview' );
					$failed_entry     = true;
					continue;
				}

				$result = $this->send_notification( $allowed[ $token ], $entry, $view, $action, $send_to );

				if ( is_wp_error( $result ) ) {
					$last_entry_error = $result->get_error_message();
					$failed_entry     = true;
					continue;
				}

				if ( self::OUTCOME_PROCESSED === ( $result['status'] ?? '' ) ) {
					$processed_entry = true;
					$last_sent_form  = $result['form'];
					$last_sent_entry = $entry;

					++$notifications;

					if ( ! empty( $result['queued'] ) ) {
						++$queued_count;
					} else {
						++$sent_count;
					}

					continue;
				}

				$skipped_entry = true;
			}

			if ( ! $matched_entry ) {
				continue;
			}

			if ( $processed_entry ) {
				++$processed;
			}

			if ( $failed_entry ) {
				$last_error = $last_entry_error;
				++$failed;
				continue;
			}

			if ( ! $processed_entry && $skipped_entry ) {
				++$skipped;
			}
		}

		if ( ! $is_background && $last_sent_form && $last_sent_entry ) {
			// Fire only after delivery work starts; eligibility-only skips are deliberate no-ops.
			$this->fire_post_resend_all_notifications( $last_sent_form, $last_sent_entry );
		}

		if ( $is_background ) {
			$skipped       += $this->get_background_result_count( $context, 'skipped' );
			$notifications += $this->get_background_result_count( $context, 'notifications' );
			$sent_count    += $this->get_background_result_count( $context, 'sent' );
			$queued_count  += $this->get_background_result_count( $context, 'queued' );
		}

		$result = [
			'processed'     => $processed,
			'failed'        => $failed,
			'skipped'       => $skipped,
			'notifications' => $notifications,
			'sent'          => $sent_count,
			'queued'        => $queued_count,
			'message'       => $this->format_processed_message( $processed, $failed, $skipped, $notifications, $sent_count, $queued_count ),
		];

		if ( $last_sent_form && $last_sent_entry ) {
			$result['last_sent_form_id']  = (int) ( $last_sent_form['id'] ?? 0 );
			$result['last_sent_entry_id'] = (int) ( $last_sent_entry['id'] ?? 0 );
		}

		if ( ! $is_background ) {
			$this->fire_complete_action( $processed, $failed, $skipped, $view, $action, $this->get_delivery_counts( $notifications, $sent_count, $queued_count ) );
		}

		if ( ! $processed && $failed ) {
			return new WP_Error(
				'gravityview_bulk_resend_notifications_failed',
				$last_error ? $last_error : __( 'No notifications were processed.', 'gk-gravityview' )
			);
		}

		return $result;
	}

	/**
	 * Builds the final background result message from cumulative counts.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View   $view       View context.
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param array  $context    Background context.
	 *
	 * @return array
	 */
	public function complete( View $view, $action_key, array $action, array $context ) {
		unset( $action_key );

		$processed     = (int) ( $context['processed'] ?? 0 );
		$failed        = (int) ( $context['failed'] ?? 0 );
		$skipped       = $this->get_background_result_count( $context, 'skipped' );
		$notifications = $this->get_background_result_count( $context, 'notifications' );
		$sent_count    = $this->get_background_result_count( $context, 'sent' );
		$queued_count  = $this->get_background_result_count( $context, 'queued' );

		$last_sent = $this->get_last_sent_from_context( $context );

		if ( $last_sent ) {
			$this->fire_post_resend_all_notifications( $last_sent['form'], $last_sent['entry'] );
		}

		$this->fire_complete_action( $processed, $failed, $skipped, $view, $action, $this->get_delivery_counts( $notifications, $sent_count, $queued_count ) );

		return [
			'notice' => [
				'message' => $this->format_processed_message( $processed, $failed, $skipped, $notifications, $sent_count, $queued_count ),
			],
		];
	}

	/**
	 * Sends one notification for one entry.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $item    Notification map item.
	 * @param array  $entry   Entry.
	 * @param View   $view    View.
	 * @param array  $action  Action config.
	 * @param string $send_to Send to override email.
	 *
	 * @return array|WP_Error
	 */
	private function send_notification( array $item, array $entry, View $view, array $action, $send_to = '' ) {
		$form         = $item['form'];
		$notification = $item['notification'];

		// The frontend action intentionally honors inactive and conditionally excluded notifications.
		if ( ! $this->notification_is_active( $notification ) ) {
			return [
				'status' => self::OUTCOME_SKIPPED,
				'sent'   => false,
				'form'   => $form,
			];
		}

		if ( ! GFCommon::evaluate_conditional_logic( rgar( $notification, 'conditionalLogic' ), $form, $entry ) ) {
			return [
				'status' => self::OUTCOME_SKIPPED,
				'sent'   => false,
				'form'   => $form,
			];
		}

		if ( '' !== $send_to ) {
			$notification['to']     = $send_to;
			$notification['toType'] = 'email';
		}

		/**
		 * Filters the notification object before Gravity Forms resend hooks run.
		 *
		 * @since 3.0.0-beta.3
		 *
		 * @param array $notification Notification object.
		 * @param array $form         Form object.
		 * @param array $entry        Entry object.
		 * @param int   $view_id      View ID.
		 * @param View  $view         View.
		 * @param array $action       Action config.
		 */
		$notification = (array) apply_filters( 'gk/gravityview/bulk-actions/resend-notifications/notification', $notification, $form, $entry, (int) $view->ID, $view, $action );

		$abort_email = apply_filters( 'gform_disable_resend_notification', false, $notification, $form, $entry );
		$sent        = false;
		$queued      = false;

		try {
			if ( ! $abort_email ) {
				$queued = $this->notification_will_be_queued( $notification, $form, $entry );

				// Mirror `GFAPI::send_notifications()` async branching:
				// when the GF background processor reports it would queue
				// this notification, push to its queue instead of sending
				// synchronously. Calling `GFAPI::send_notification` on the
				// queued path delivers the email immediately AND reports
				// `queued=true`, producing inconsistent caller state.
				if ( $queued ) {
					$this->enqueue_notification( $notification, $form, $entry );
				} else {
					$this->dispatch_send( $notification, $form, $entry );
				}

				$sent = true;
			}
		} catch ( Throwable $e ) {
			gravityview()->log->error(
				'Frontend bulk resend notification failed.',
				[
					'error'           => $e->getMessage(),
					'entry_id'        => $entry['id'] ?? 0,
					'form_id'         => $form['id'] ?? 0,
					'notification_id' => $notification['id'] ?? '',
				]
			);

			return new WP_Error( 'gravityview_bulk_resend_notifications_send_failed', __( 'A notification could not be processed.', 'gk-gravityview' ) );
		} finally {
			do_action( 'gform_post_resend_notification', $notification, $form, $entry );
		}

		return [
			'status' => $sent ? self::OUTCOME_PROCESSED : self::OUTCOME_SKIPPED,
			'sent'   => $sent,
			'queued' => $sent && $queued,
			'form'   => $form,
		];
	}

	/**
	 * Dispatches a notification through the newest available Gravity Forms API.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array     $notification Notification object.
	 * @param array     $form         Form object.
	 * @param array     $entry        Entry object.
	 * @param bool|null $gfapi_available Whether GFAPI::send_notification is available. Null checks runtime.
	 *
	 * @return void
	 */
	private function dispatch_send( array $notification, array $form, array $entry, $gfapi_available = null ) {
		$dispatcher = $this->get_send_dispatcher( $gfapi_available );

		/**
		 * Filters the callable used to dispatch a frontend resend notification.
		 *
		 * @since 3.0.0-beta.3
		 *
		 * @param callable $dispatcher   Callable receiving notification, form, and entry.
		 * @param array    $notification Notification object.
		 * @param array    $form         Form object.
		 * @param array    $entry        Entry object.
		 */
		$dispatcher = apply_filters( 'gk/gravityview/bulk-actions/resend-notifications/dispatch-send', $dispatcher, $notification, $form, $entry );

		call_user_func( $dispatcher, $notification, $form, $entry );
	}

	/**
	 * Enqueues a notification into the Gravity Forms async background
	 * processor instead of sending it synchronously.
	 *
	 * Replicates the queueing branch of `GFAPI::send_notifications()`
	 * so a single notification can be queued the same way GF's own
	 * pipeline would queue it during form submission.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $notification Notification object.
	 * @param array $form         Form object.
	 * @param array $entry        Entry object.
	 *
	 * @return void
	 */
	private function enqueue_notification( array $notification, array $form, array $entry ) {
		if ( ! class_exists( 'GFForms' ) || ! class_exists( GF_Background_Process_Service_Provider::class ) ) {
			// Should never reach here — `notification_will_be_queued`
			// returned true so both classes must exist — but guard so
			// a missing dependency doesn't fatal.
			$this->dispatch_send( $notification, $form, $entry );
			return;
		}

		$processor = GFForms::get_service_container()->get( GF_Background_Process_Service_Provider::NOTIFICATIONS );

		$processor->push_to_queue(
			[
				'notifications' => [ $notification ],
				'form_id'       => (int) ( $form['id'] ?? 0 ),
				'entry_id'      => (int) ( $entry['id'] ?? 0 ),
				'event'         => (string) rgar( $notification, 'event', 'custom' ),
				'data'          => [],
			]
		);

		$processor->save()->dispatch_on_shutdown();
	}

	/**
	 * Returns the Gravity Forms notification dispatcher callable.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param bool|null $gfapi_available Whether GFAPI::send_notification is available. Null checks runtime.
	 *
	 * @return callable
	 */
	private function get_send_dispatcher( $gfapi_available = null ) {
		if ( null === $gfapi_available ) {
			$gfapi_available = method_exists( GFAPI::class, 'send_notification' );
		}

		if ( $gfapi_available ) {
			return [ GFAPI::class, 'send_notification' ];
		}

		return [ GFCommon::class, 'send_notification' ];
	}

	/**
	 * Returns whether Gravity Forms will queue the notification asynchronously.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $notification Notification object.
	 * @param array $form         Form object.
	 * @param array $entry        Entry object.
	 *
	 * @return bool
	 */
	private function notification_will_be_queued( array $notification, array $form, array $entry ) {
		if ( ! class_exists( 'GFForms' ) || ! class_exists( GF_Background_Process_Service_Provider::class ) ) {
			return false;
		}

		try {
			$processor = GFForms::get_service_container()->get( GF_Background_Process_Service_Provider::NOTIFICATIONS );
		} catch ( Throwable $e ) {
			return false;
		}

		if ( ! is_object( $processor ) || ! method_exists( $processor, 'is_enabled' ) ) {
			return false;
		}

		$notification_id = rgar( $notification, 'id', 'custom' );
		$event           = rgar( $notification, 'event', 'custom' );

		return (bool) $processor->is_enabled( [ $notification_id ], $form, $entry, $event, [] );
	}

	/**
	 * Returns allowed notifications keyed by token.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View  $view        View.
	 * @param array $action      Action config.
	 * @param bool  $active_only Whether to include active notifications only.
	 *
	 * @return array
	 */
	private function get_allowed_notification_map( View $view, array $action, $active_only ) {
		$settings = isset( $action['settings'] ) && is_array( $action['settings'] ) ? $action['settings'] : [];
		$tokens   = ResendNotificationToken::normalize_list( $settings[ self::SETTING_NOTIFICATIONS ] ?? [] );

		if ( [] === $tokens ) {
			return [];
		}

		$notifications = $this->get_notification_map( $view, (bool) $active_only );
		$allowed       = [];

		foreach ( $tokens as $token ) {
			if ( isset( $notifications[ $token ] ) ) {
				$allowed[ $token ] = $notifications[ $token ];
			}
		}

		return $allowed;
	}

	/**
	 * Returns all resendable View notifications keyed by token.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View $view        View.
	 * @param bool $active_only Whether to include active notifications only.
	 *
	 * @return array
	 */
	private function get_notification_map( ?View $view, $active_only ) {
		$items = [];

		foreach ( $this->get_view_forms( $view ) as $form_id => $form ) {
			foreach ( $this->get_form_notifications( $form ) as $notification_id => $notification ) {
				$notification_id = (string) ( $notification['id'] ?? $notification_id );
				$token           = ResendNotificationToken::build( $form_id, $notification_id );

				if ( '' === $token || ( $active_only && ! $this->notification_is_active( $notification ) ) ) {
					continue;
				}

				$form_label         = (string) ( $form['title'] ?? $form_id );
				$notification_label = (string) ( $notification['name'] ?? $notification_id );

				$items[ $token ] = [
					'token'              => $token,
					'form_id'            => (int) $form_id,
					'form_label'         => $form_label,
					'notification_id'    => $notification_id,
					'notification_label' => $notification_label,
					'admin_label'        => $form_label . ': ' . $notification_label,
					'notification'       => $notification,
					'form'               => $form,
				];
			}
		}

		uasort(
			$items,
			static function ( $a, $b ) {
				$result = strnatcasecmp( (string) $a['admin_label'], (string) $b['admin_label'] );

				return 0 === $result ? strnatcasecmp( (string) $a['token'], (string) $b['token'] ) : $result;
			}
		);

		return $items;
	}

	/**
	 * Returns forms available to the View, falling back to the admin form picker's
	 * currently-selected form when the View context is null or unsaved.
	 *
	 * The Bulk Actions widget can be added before the View is first saved, so
	 * `$view->form->ID` may be 0 (auto-draft state) or `$view` may be null
	 * entirely (the AJAX path that renders widget settings). In both cases the
	 * admin AJAX request still carries `form_id` from the Data Source picker,
	 * which is enough to populate the notification list without forcing a save.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View|null $view View.
	 *
	 * @return array
	 */
	private function get_view_forms( ?View $view ) {
		$forms = [];

		if ( $view && ! empty( $view->form->ID ) ) {
			$form = GVCommon::get_form( (int) $view->form->ID );

			if ( $form ) {
				$forms[ (int) $form['id'] ] = $form;
			}
		}

		if ( $view ) {
			foreach ( View::get_joined_forms( (int) $view->ID ) as $joined_form ) {
				if ( empty( $joined_form->ID ) ) {
					continue;
				}

				$form = GVCommon::get_form( (int) $joined_form->ID );

				if ( $form ) {
					$forms[ (int) $form['id'] ] = $form;
				}
			}
		}

		if ( [] !== $forms ) {
			return $forms;
		}

		// Fall back to the admin form picker's current value (sent on every gv_field_options AJAX request).
		$fallback_form_id = isset( $_POST['form_id'] ) ? absint( wp_unslash( $_POST['form_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- gv_field_options handler verifies the nonce before this callback runs.

		if ( ! $fallback_form_id ) {
			return $forms;
		}

		$fallback_form = GVCommon::get_form( $fallback_form_id );

		if ( $fallback_form ) {
			$forms[ $fallback_form_id ] = $fallback_form;
		}

		return $forms;
	}

	/**
	 * Returns resendable notifications for a form.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $form Form.
	 *
	 * @return array
	 */
	private function get_form_notifications( array $form ) {
		$notifications = GFCommon::get_notifications( 'resend_notifications', $form );

		return is_array( $notifications ) ? $notifications : [];
	}

	/**
	 * Returns the Gravity Forms notification settings URL for a form.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return string
	 */
	private function get_form_notifications_admin_url( $form_id ) {
		return admin_url(
			add_query_arg(
				[
					'page'    => 'gf_edit_forms',
					'view'    => 'settings',
					'subview' => 'notification',
					'id'      => (int) $form_id,
				],
				'admin.php'
			)
		);
	}

	/**
	 * Expands row entries into concrete Gravity Forms entries.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $entries Entries keyed by row entry ID.
	 *
	 * @return array
	 */
	private function expand_entries( array $entries ) {
		$expanded = [];

		foreach ( $entries as $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}

			if ( ! empty( $entry['_multi'] ) && is_array( $entry['_multi'] ) ) {
				foreach ( $entry['_multi'] as $multi_entry ) {
					if ( is_array( $multi_entry ) && ! empty( $multi_entry['id'] ) && ! empty( $multi_entry['form_id'] ) ) {
						$expanded[ (int) $multi_entry['form_id'] . ':' . (int) $multi_entry['id'] ] = $multi_entry;
					}
				}

				continue;
			}

			if ( ! empty( $entry['id'] ) && ! empty( $entry['form_id'] ) ) {
				$expanded[ (int) $entry['form_id'] . ':' . (int) $entry['id'] ] = $entry;
			}
		}

		return array_values( $expanded );
	}

	/**
	 * Whether a notification is active.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $notification Notification object.
	 *
	 * @return bool
	 */
	private function notification_is_active( array $notification ) {
		return ! isset( $notification['isActive'] ) || (bool) $notification['isActive'];
	}

	/**
	 * Whether the Send to override is enabled for the action.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $action Action config.
	 *
	 * @return bool
	 */
	private function send_to_override_allowed( array $action ) {
		return ! empty( $action['settings'][ self::SETTING_ALLOW_SEND_TO_OVERRIDE ] );
	}

	/**
	 * Whether the process call is running as a background batch.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $context Processing context.
	 *
	 * @return bool
	 */
	private function is_background_context( array $context ) {
		return ! empty( $context['background'] );
	}

	/**
	 * Returns the last sent form and entry from background result metadata.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $context Completion context.
	 *
	 * @return array|null
	 */
	private function get_last_sent_from_context( array $context ) {
		$sources = [
			isset( $context['batch_result'] ) && is_array( $context['batch_result'] ) ? $context['batch_result'] : [],
			isset( $context['job_data']['result'] ) && is_array( $context['job_data']['result'] ) ? $context['job_data']['result'] : [],
		];

		foreach ( $sources as $source ) {
			$form_id  = max( 0, (int) ( $source['last_sent_form_id'] ?? 0 ) );
			$entry_id = max( 0, (int) ( $source['last_sent_entry_id'] ?? 0 ) );

			if ( ! $form_id || ! $entry_id ) {
				continue;
			}

			$form  = GVCommon::get_form( $form_id );
			$entry = GFAPI::get_entry( $entry_id );

			if ( ! $form || is_wp_error( $entry ) || ! is_array( $entry ) || (int) ( $entry['form_id'] ?? 0 ) !== $form_id ) {
				continue;
			}

			return [
				'form'  => $form,
				'entry' => $entry,
			];
		}

		return null;
	}

	/**
	 * Returns a cumulative custom count from background result metadata.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array  $context Completion or batch context.
	 * @param string $key     Count key.
	 *
	 * @return int
	 */
	private function get_background_result_count( array $context, $key ) {
		$sources = [
			$context,
			isset( $context['batch_result'] ) && is_array( $context['batch_result'] ) ? $context['batch_result'] : [],
			isset( $context['job_data']['result'] ) && is_array( $context['job_data']['result'] ) ? $context['job_data']['result'] : [],
		];

		foreach ( $sources as $source ) {
			if ( array_key_exists( $key, $source ) ) {
				return max( 0, (int) $source[ $key ] );
			}
		}

		return 0;
	}

	/**
	 * Fires the Gravity Forms all-resend hook.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $form  Form object.
	 * @param array $entry Entry object.
	 *
	 * @return void
	 */
	private function fire_post_resend_all_notifications( array $form, array $entry ) {
		do_action( 'gform_post_resend_all_notifications', $form, $entry );
	}

	/**
	 * Fires the GravityView completion hook once per resend operation.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int      $processed Number of processed entries.
	 * @param int      $failed    Number of failed entries.
	 * @param int|null $skipped   Number of skipped entries, or null when unavailable.
	 * @param View     $view      View.
	 * @param array    $action    Action config.
	 * @param array    $counts    Notification delivery counts.
	 *
	 * @return void
	 */
	private function fire_complete_action( $processed, $failed, $skipped, View $view, array $action, array $counts = [] ) {
		/**
		 * Fires after frontend resend notifications processing finishes.
		 *
		 * @since 3.0.0-beta.3
		 *
		 * @param array $result  Processing counts.
		 * @param int   $view_id View ID.
		 * @param View  $view    View.
		 * @param array $action  Action config.
		 */
		$result = [
			'processed' => (int) $processed,
			'failed'    => (int) $failed,
		];

		if ( null !== $skipped ) {
			$result['skipped'] = (int) $skipped;
		}

		foreach ( [ 'notifications', 'sent', 'queued' ] as $key ) {
			if ( array_key_exists( $key, $counts ) ) {
				$result[ $key ] = max( 0, (int) $counts[ $key ] );
			}
		}

		do_action(
			'gk/gravityview/bulk-actions/resend-notifications/complete',
			$result,
			(int) $view->ID,
			$view,
			$action
		);
	}

	/**
	 * Normalizes notification delivery counts for hooks and messages.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $notifications Successful notification count.
	 * @param int $sent_count    Synchronously sent notification count.
	 * @param int $queued_count  Queued notification count.
	 *
	 * @return array
	 */
	private function get_delivery_counts( $notifications, $sent_count, $queued_count ) {
		return [
			'notifications' => max( 0, (int) $notifications ),
			'sent'          => max( 0, (int) $sent_count ),
			'queued'        => max( 0, (int) $queued_count ),
		];
	}

	/**
	 * Returns a validated Send to override for processing.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $action  Action config.
	 * @param array $request Sanitized or stored request data.
	 *
	 * @return string
	 */
	private function get_process_send_to( array $action, array $request ) {
		if ( ! $this->send_to_override_allowed( $action ) ) {
			return '';
		}

		$send_to = sanitize_text_field( (string) ( $request[ self::REQUEST_SEND_TO ] ?? '' ) );

		if ( '' === $send_to || ! is_email( $send_to ) ) {
			return '';
		}

		return sanitize_email( $send_to );
	}

	/**
	 * Builds a request validation error.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $code    Error code.
	 * @param string $message Error message.
	 * @param string $field   Field key.
	 *
	 * @return WP_Error
	 */
	private function request_error( $code, $message, $field ) {
		return new WP_Error(
			$code,
			$message,
			[
				'field_errors' => [
					$field => $message,
				],
			]
		);
	}

	/**
	 * Formats the processed result message.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int      $processed     Number of entries with processed notifications.
	 * @param int      $failed        Number of entries whose notifications could not be processed.
	 * @param int      $skipped       Number of entries with no eligible notifications.
	 * @param int|null $notifications Number of notifications processed.
	 * @param int|null $sent_count    Number of synchronously sent notifications.
	 * @param int|null $queued_count  Number of queued notifications.
	 *
	 * @return string
	 */
	private function format_processed_message( $processed, $failed, $skipped = 0, $notifications = null, $sent_count = null, $queued_count = null ) {
		$processed     = max( 0, (int) $processed );
		$failed        = max( 0, (int) $failed );
		$skipped       = max( 0, (int) $skipped );
		$notifications = null === $notifications ? $processed : max( 0, (int) $notifications );
		$queued_count  = null === $queued_count ? 0 : max( 0, (int) $queued_count );
		$sent_count    = null === $sent_count ? max( 0, $notifications - $queued_count ) : max( 0, (int) $sent_count );

		if ( $sent_count + $queued_count < $notifications ) {
			$sent_count += $notifications - ( $sent_count + $queued_count );
		}

		$messages = [];

		if ( 0 === $processed && 0 === $failed && $skipped ) {
			$messages[] = $this->format_no_eligible_message( $skipped );
		} elseif ( 0 === $processed && $failed ) {
			$messages[] = $this->format_failed_message( $failed );
		} elseif ( 0 === $processed ) {
			$messages[] = __( 'No eligible notifications for the selected entries.', 'gk-gravityview' );
		} else {
			$messages[] = $this->format_delivery_message( $processed, $notifications, $sent_count, $queued_count );
		}

		if ( $failed && $processed ) {
			$messages[] = $this->format_failed_message( $failed );
		}

		if ( $skipped && ( $processed || $failed ) ) {
			$messages[] = $this->format_skipped_message( $skipped );
		}

		return implode( ' ', array_filter( $messages ) );
	}

	/**
	 * Formats a successful delivery count.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $processed     Number of entries with processed notifications.
	 * @param int $notifications Successful notification count.
	 * @param int $sent_count    Synchronously sent notification count.
	 * @param int $queued_count  Queued notification count.
	 *
	 * @return string
	 */
	private function format_delivery_message( $processed, $notifications, $sent_count, $queued_count ) {
		$entry_count = $this->format_entry_count( $processed );

		if ( $sent_count && $queued_count ) {
			return strtr(
				/* translators: [sent] is a sent notification count phrase. [queued] is a queued notification count phrase. [entries] is a formatted entry count. */
				__( '[sent] and [queued] for [entries].', 'gk-gravityview' ),
				[
					'[sent]'    => $this->format_sent_notification_count( $sent_count ),
					'[queued]'  => $this->format_queued_notification_count( $queued_count ),
					'[entries]' => $entry_count,
				]
			);
		}

		if ( $queued_count ) {
			return strtr(
				/* translators: [notifications] is the number of queued notifications. [entries] is a formatted entry count. */
				_n( 'Queued [notifications] notification for [entries].', 'Queued [notifications] notifications for [entries].', $queued_count, 'gk-gravityview' ),
				[
					'[notifications]' => $queued_count,
					'[entries]'       => $entry_count,
				]
			);
		}

		return strtr(
			/* translators: [notifications] is the number of sent notifications. [entries] is a formatted entry count. */
			_n( 'Sent [notifications] notification for [entries].', 'Sent [notifications] notifications for [entries].', $notifications, 'gk-gravityview' ),
			[
				'[notifications]' => $notifications,
				'[entries]'       => $entry_count,
			]
		);
	}

	/**
	 * Formats an entry count phrase.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $count Entry count.
	 *
	 * @return string
	 */
	private function format_entry_count( $count ) {
		$count = max( 0, (int) $count );

		return strtr(
			/* translators: [count] is the number of entries. */
			_n( '[count] entry', '[count] entries', $count, 'gk-gravityview' ),
			[
				'[count]' => $count,
			]
		);
	}

	/**
	 * Formats a sent notification count phrase.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $count Notification count.
	 *
	 * @return string
	 */
	private function format_sent_notification_count( $count ) {
		$count = max( 0, (int) $count );

		return strtr(
			/* translators: [count] is the number of sent notifications. */
			_n( 'Sent [count] notification', 'Sent [count] notifications', $count, 'gk-gravityview' ),
			[
				'[count]' => $count,
			]
		);
	}

	/**
	 * Formats a queued notification count phrase.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $count Notification count.
	 *
	 * @return string
	 */
	private function format_queued_notification_count( $count ) {
		$count = max( 0, (int) $count );

		return strtr(
			/* translators: [count] is the number of queued notifications. */
			_n( 'queued [count] notification', 'queued [count] notifications', $count, 'gk-gravityview' ),
			[
				'[count]' => $count,
			]
		);
	}

	/**
	 * Formats a failed entry count message.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $count Failed entry count.
	 *
	 * @return string
	 */
	private function format_failed_message( $count ) {
		$count = max( 0, (int) $count );

		return strtr(
			/* translators: [count] is the number of entries that could not be processed. */
			_n( '[count] entry could not be processed.', '[count] entries could not be processed.', $count, 'gk-gravityview' ),
			[
				'[count]' => $count,
			]
		);
	}

	/**
	 * Formats a no-eligible-notifications message.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $count Skipped entry count.
	 *
	 * @return string
	 */
	private function format_no_eligible_message( $count ) {
		$count = max( 0, (int) $count );

		return strtr(
			/* translators: [count] is the number of selected entries with no eligible notifications. */
			_n( 'No eligible notifications for [count] selected entry.', 'No eligible notifications for [count] selected entries.', $count, 'gk-gravityview' ),
			[
				'[count]' => $count,
			]
		);
	}

	/**
	 * Formats a skipped entry count message.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param int $count Skipped entry count.
	 *
	 * @return string
	 */
	private function format_skipped_message( $count ) {
		$count = max( 0, (int) $count );

		return strtr(
			/* translators: [count] is the number of selected entries with no eligible notifications. */
			_n( '[count] selected entry had no eligible notifications.', '[count] selected entries had no eligible notifications.', $count, 'gk-gravityview' ),
			[
				'[count]' => $count,
			]
		);
	}
}
