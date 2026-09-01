<?php
/**
 * Shared configuration for frontend bulk actions.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\BackgroundJobs\Scheduler;
use GravityKit\GravityView\BackgroundJobs\ResultStore;
use GravityKit\GravityView\Entry\BackgroundJobs\EntryBatchResolver;
use GravityKit\GravityView\View\View;

/**
 * Shared constants and small helpers for frontend bulk actions.
 *
 * @since 3.0.0
 */
final class Config {
	const WIDGET_ID                           = 'bulk_actions';
	const POST_ACTION                         = 'gv_bulk_action';
	const POST_ACTION_INPUT                   = 'gv_bulk_action_input';
	const POST_CANCEL_JOB                     = 'gv_bulk_cancel_job';
	const POST_DISMISS_JOB                    = 'gv_bulk_dismiss_job';
	const POST_ENTRIES                        = 'gv_bulk_entries';
	const POST_EXCLUDED                       = 'gv_bulk_excluded_entries';
	const POST_JOB_NONCE                      = 'gv_bulk_job_nonce';
	const POST_JOB_TOKEN                      = 'gv_bulk_job_token';
	const POST_RENDER_INSTANCE                = 'gv_bulk_render_instance';
	const POST_RELOAD_URL                     = 'gv_bulk_reload_url';
	const POST_SELECT_ALL                     = 'gv_bulk_select_all';
	const POST_SHOW_SELECTED                  = 'gv_bulk_show_selected';
	const POST_VIEW_ID                        = 'gv_bulk_view_id';
	const POST_NONCE                          = 'gv_bulk_nonce';
	const QUERY_TOKEN                         = 'gv_bulk_token';
	const QUERY_BACKGROUND_TOKEN              = 'gv_bulk_job';
	const QUERY_SELECTION_MODE                = 'gv_bulk_selection_mode';
	const QUERY_SELECTION_TOKEN               = 'gv_bulk_selection_token';
	const AJAX_VALIDATE_ACTION                = 'gv_bulk_action_validate';
	const AJAX_STATUS_ACTION                  = 'gv_bulk_action_status';
	const AJAX_SETTING_OPTIONS_ACTION         = 'gv_bulk_action_setting_options';
	const ACTION_SETTINGS                               = 'bulk_action_settings';
	const ACTION_BACKGROUND_ENABLED_SETTING             = 'background_processing';
	const ACTION_BACKGROUND_THRESHOLD_SETTING           = 'background_threshold';
	const ACTION_BACKGROUND_PROGRESS_POLLING_SETTING    = 'background_progress_polling';
	const ACTION_BACKGROUND_COMPLETION_BEHAVIOR_SETTING = 'background_completion_behavior';
	const ACTION_BACKGROUND_STICKY_RESULT_SETTING       = 'background_sticky_result';

	const SELECTION_BEHAVIOR_ACROSS_PAGES  = 'across_pages';
	const SELECTION_BEHAVIOR_CURRENT_PAGE  = 'current_page';
	const SELECTION_MODE_SHOW_SELECTED     = 'show_selected';
	const BACKGROUND_COMPLETE_SHOW_MESSAGE = 'show_message';
	const BACKGROUND_COMPLETE_RELOAD_LINK  = 'reload_link';
	const BACKGROUND_COMPLETE_AUTO_RELOAD  = 'auto_reload';

	/**
	 * @since 3.0.0
	 * @var int
	 */
	const MAX_ENTRY_IDS             = 500;
	const SELECT_ALL_SNAPSHOT_LIMIT = 10000;
	const BACKGROUND_THRESHOLD      = 100;
	const BACKGROUND_POLL_INTERVAL  = 5;
	const SELECTION_TTL             = 3600;
	const SYNC_VIEW_LOCK_TTL        = 600;
	const TYPED_CONFIRM_THRESHOLD   = 100;
	const LOST_JOB_TIMEOUT          = HOUR_IN_SECONDS;

	/**
	 * Template IDs that use the table View template.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view Optional View context.
	 *
	 * @return string[]
	 */
	public static function get_supported_template_ids( ?View $view = null ) {
		$template_ids = [
			'default_table',
			'preset_business_data',
			'preset_issue_tracker',
			'preset_resume_board',
			'preset_job_board',
		];

		/**
		 * Filters the directory template IDs that support frontend bulk actions.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $template_ids Template IDs.
		 * @param int      $view_id      View ID, or 0 when unavailable.
		 * @param View|null $view        View context, or null when unavailable.
		 */
		return apply_filters( 'gk/gravityview/bulk-actions/supported-template-ids', $template_ids, $view ? (int) $view->ID : 0, $view );
	}

	/**
	 * Returns the maximum number of entries a single request can process.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view Optional View context.
	 *
	 * @return int
	 */
	public static function get_max_entry_ids( ?View $view = null ) {
		$max = self::MAX_ENTRY_IDS;

		/**
		 * Filters the maximum number of entries allowed in one synchronous frontend bulk action request.
		 *
		 * @since 3.0.0
		 *
		 * @param int       $max     Maximum entry IDs. Default 500.
		 * @param int       $view_id View ID, or 0 when unavailable.
		 * @param View|null $view    View context, or null when unavailable.
		 */
		return max( 1, (int) apply_filters( 'gk/gravityview/bulk-actions/max-entry-ids', $max, $view ? (int) $view->ID : 0, $view ) );
	}

	/**
	 * Returns the maximum number of IDs a select-all background request may snapshot.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view Optional View context.
	 *
	 * @return int
	 */
	public static function get_select_all_snapshot_limit( ?View $view = null ) {
		$limit = self::SELECT_ALL_SNAPSHOT_LIMIT;

		/**
		 * Filters the maximum number of IDs a select-all background request may snapshot before queueing.
		 *
		 * @since 3.0.0
		 *
		 * @param int       $limit   Snapshot ID limit. Default 10000.
		 * @param int       $view_id View ID, or 0 when unavailable.
		 * @param View|null $view    View context, or null when unavailable.
		 */
		return max( 1, (int) apply_filters( 'gk/gravityview/bulk-actions/select-all-snapshot-limit', $limit, $view ? (int) $view->ID : 0, $view ) );
	}

	/**
	 * Returns the inclusive entry count threshold where supported actions use background processing.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view Optional View context.
	 *
	 * @return int
	 */
	public static function get_background_threshold( ?View $view = null ) {
		$threshold = self::BACKGROUND_THRESHOLD;

		/**
		 * Filters the inclusive entry count where supported bulk actions use background processing.
		 *
		 * @since 3.0.0
		 *
		 * @param int       $threshold Entry count threshold. Default 100.
		 * @param int       $view_id   View ID, or 0 when unavailable.
		 * @param View|null $view      View context, or null when unavailable.
		 */
		return max( 1, (int) apply_filters( 'gk/gravityview/bulk-actions/background-threshold', $threshold, $view ? (int) $view->ID : 0, $view ) );
	}

	/**
	 * Returns the entry count where typed confirmation is required for an action.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view       Optional View context.
	 * @param string    $action_key Action key.
	 *
	 * @return int
	 */
	public static function get_typed_confirmation_threshold( ?View $view = null, $action_key = '' ) {
		$threshold  = self::TYPED_CONFIRM_THRESHOLD;
		$action_key = sanitize_key( (string) $action_key );

		/**
		 * Filters the entry count where typed confirmation is required for a frontend bulk action.
		 *
		 * @since 3.0.0
		 *
		 * @param int       $threshold Entry count threshold. Default 100.
		 * @param int       $view_id   View ID, or 0 when unavailable.
		 * @param View|null $view      View context, or null when unavailable.
		 * @param string    $action_key Action key.
		 */
		return max( 1, (int) apply_filters( 'gk/gravityview/bulk-actions/typed-confirmation-threshold', $threshold, $view ? (int) $view->ID : 0, $view, $action_key ) );
	}

	/**
	 * Returns whether an action should use typed confirmation at the configured threshold.
	 *
	 * By default, only Delete Entries uses typed confirmation. It is the only
	 * built-in action that is irreversible from the Bulk Actions UI.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param View   $view       View.
	 *
	 * @return bool
	 */
	public static function action_uses_typed_confirmation( $action_key, array $action, View $view ) {
		unset( $action );

		$action_key = sanitize_key( (string) $action_key );
		$keys       = [ 'delete' ];

		/**
		 * Filters action keys that require typed confirmation when their selected count reaches the threshold.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $keys       Action keys. Default: delete.
		 * @param int      $view_id    View ID.
		 * @param View     $view       View.
		 * @param string   $action_key Current action key being checked.
		 */
		$keys = apply_filters( 'gk/gravityview/bulk-actions/typed-confirmation-action-keys', $keys, (int) $view->ID, $view, $action_key );
		$keys = is_array( $keys ) ? array_map( 'sanitize_key', $keys ) : [];

		return in_array( $action_key, $keys, true );
	}

	/**
	 * Returns action keys configured to use background processing.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view Optional View context.
	 *
	 * @return string[]
	 */
	public static function get_background_action_keys( ?View $view = null ) {
		$keys = [];

		if ( $view ) {
			foreach ( Registry::get_actions( $view ) as $action_key => $action ) {
				$action_key = sanitize_key( $action_key );

				if ( self::action_supports_background( $action ) && self::is_action_background_setting_enabled( $action_key, $action, $view ) ) {
					$keys[] = $action_key;
				}
			}
		} else {
			$keys = Registry::get_default_background_action_setting_values();
		}

		return array_values( array_unique( array_map( 'sanitize_key', $keys ) ) );
	}

	/**
	 * Returns whether an action may use background processing for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param View   $view       View.
	 *
	 * @return bool
	 */
	public static function is_action_background_processing_enabled( $action_key, array $action, View $view ) {
		$enabled = self::action_supports_background( $action )
			&& self::is_background_processing_enabled( $view )
			&& self::is_action_background_setting_enabled( $action_key, $action, $view );

		return $enabled;
	}

	/**
	 * Returns whether an action should block other locked actions for the View while it runs.
	 *
	 * Actions are locked by default. Read-only actions, such as exports, can opt
	 * out by setting `lock => false` in their action config.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param View   $view       View.
	 *
	 * @return bool
	 */
	public static function action_uses_view_lock( $action_key, array $action, View $view ) {
		unset( $view );

		$action_key = sanitize_key( $action_key );
		$forced     = in_array( $action_key, self::get_force_locked_action_keys(), true );
		$enabled    = $forced || ! array_key_exists( 'lock', $action ) || false !== $action['lock'];

		return $enabled;
	}

	/**
	 * Returns destructive built-in actions that cannot opt out of View locking.
	 *
	 * @since 3.0.0
	 *
	 * @return string[]
	 */
	private static function get_force_locked_action_keys() {
		return [ 'approve', 'delete', 'disapprove', 'edit_entries', 'unapprove' ];
	}

	/**
	 * Returns the active background job status polling interval in seconds.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view Optional View context.
	 *
	 * @return int
	 */
	public static function get_background_poll_interval( ?View $view = null ) {
		unset( $view );

		return self::BACKGROUND_POLL_INTERVAL;
	}

	/**
	 * Returns the number of entries processed in each background batch.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view Optional View context.
	 *
	 * @return int
	 */
	public static function get_background_batch_size( ?View $view = null ) {
		unset( $view );

		return EntryBatchResolver::DEFAULT_BATCH_SIZE;
	}

	/**
	 * Returns the View-level bulk action lock lifetime.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view       Optional View context.
	 * @param bool      $background Whether the action is running in the background.
	 *
	 * @return int
	 */
	public static function get_view_lock_ttl( ?View $view = null, $background = false ) {
		unset( $view );

		return $background ? ResultStore::DEFAULT_TTL : self::SYNC_VIEW_LOCK_TTL;
	}

	/**
	 * Returns how long a queued/running result may point at a missing Foundation job before failing.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view   Optional View context.
	 * @param array     $result Stored result data.
	 *
	 * @return int
	 */
	public static function get_lost_job_timeout( ?View $view = null, array $result = [] ) {
		$timeout = self::LOST_JOB_TIMEOUT;

		/**
		 * Filters how long a queued/running bulk action result may point at a missing Foundation job before failing.
		 *
		 * @since 3.0.0
		 *
		 * @param int       $timeout Timeout in seconds. Default: one hour.
		 * @param int       $view_id View ID, or 0 when unavailable.
		 * @param View|null $view    View context, or null when unavailable.
		 * @param array     $result  Stored result data.
		 */
		return max( MINUTE_IN_SECONDS, (int) apply_filters( 'gk/gravityview/bulk-actions/lost-job-timeout', $timeout, $view ? (int) $view->ID : 0, $view, $result ) );
	}

	/**
	 * Returns normalized background settings for an action.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	public static function get_action_background_config( array $action ) {
		$background = $action['background'] ?? false;

		if ( true === $background ) {
			return [
				'enabled' => true,
			];
		}

		if ( ! is_array( $background ) ) {
			return [
				'enabled' => false,
			];
		}

		$background['enabled'] = ! array_key_exists( 'enabled', $background ) || (bool) $background['enabled'];

		return $background;
	}

	/**
	 * Returns whether an action has opted into background processing.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action config.
	 *
	 * @return bool
	 */
	public static function action_supports_background( array $action ) {
		$background = self::get_action_background_config( $action );

		return ! empty( $background['enabled'] );
	}

	/**
	 * Returns an action config with resolved, sanitized per-action settings.
	 *
	 * Background jobs pass a saved settings snapshot so later widget edits do not
	 * change already-queued work.
	 *
	 * @since 3.0.0
	 *
	 * @param View       $view              View.
	 * @param string     $action_key        Action key.
	 * @param array      $action            Action config.
	 * @param array|null $settings_snapshot Optional saved settings snapshot.
	 *
	 * @return array
	 */
	public static function resolve_action_config( View $view, $action_key, array $action, ?array $settings_snapshot = null ) {
		$action['settings'] = self::get_action_settings( $view, $action_key, $action, $settings_snapshot );

		/**
		 * Filters a bulk action config after per-action settings have been resolved.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $action     Action config.
		 * @param int    $view_id    View ID.
		 * @param View   $view       View.
		 * @param string $action_key Action key.
		 */
		return (array) apply_filters( 'gk/gravityview/bulk-actions/action-config', $action, (int) $view->ID, $view, sanitize_key( $action_key ) );
	}

	/**
	 * Returns sanitized settings for one action.
	 *
	 * @since 3.0.0
	 *
	 * @param View       $view              View.
	 * @param string     $action_key        Action key.
	 * @param array      $action            Action config.
	 * @param array|null $settings_snapshot Optional saved settings snapshot.
	 *
	 * @return array
	 */
	public static function get_action_settings( View $view, $action_key, array $action, ?array $settings_snapshot = null ) {
		$schema     = self::get_action_settings_schema( $action, $view, $action_key );
		$settings   = self::get_action_setting_defaults( $action, $view, $action_key );
		$action_key = sanitize_key( $action_key );

		if ( [] === $schema ) {
			return $settings;
		}

		if ( null !== $settings_snapshot ) {
			$saved = $settings_snapshot;
		} else {
			$widget_settings = self::get_widget_settings( $view );
			$saved_settings  = $widget_settings ? (array) $widget_settings->get( self::ACTION_SETTINGS, [] ) : [];
			$saved           = isset( $saved_settings[ $action_key ] ) && is_array( $saved_settings[ $action_key ] ) ? $saved_settings[ $action_key ] : [];
		}

		$settings = array_merge( $settings, self::sanitize_action_settings( (array) $saved, $schema ) );

		/**
		 * Filters resolved settings for one bulk action.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $settings   Resolved settings.
		 * @param int    $view_id    View ID.
		 * @param View   $view       View.
		 * @param string $action_key Action key.
		 * @param array  $action     Action config.
		 * @param array  $schema     Action settings schema.
		 */
		return (array) apply_filters( 'gk/gravityview/bulk-actions/action-settings', $settings, (int) $view->ID, $view, $action_key, $action, $schema );
	}

	/**
	 * Returns whether an action is enabled for background processing in widget settings.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param View   $view       View.
	 *
	 * @return bool
	 */
	public static function is_action_background_setting_enabled( $action_key, array $action, View $view ) {
		if ( ! self::action_supports_background( $action ) ) {
			return false;
		}

		$background = self::get_action_background_config( $action );

		if ( ! empty( $background['always'] ) ) {
			return true;
		}

		$settings = self::get_action_settings( $view, $action_key, $action );

		return ! empty( $settings[ self::ACTION_BACKGROUND_ENABLED_SETTING ] );
	}

	/**
	 * Returns the configured background threshold for one action.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param View   $view       View.
	 *
	 * @return int
	 */
	public static function get_action_background_threshold( $action_key, array $action, View $view ) {
		$settings  = self::get_action_settings( $view, $action_key, $action );
		$threshold = isset( $settings[ self::ACTION_BACKGROUND_THRESHOLD_SETTING ] )
			? (int) $settings[ self::ACTION_BACKGROUND_THRESHOLD_SETTING ]
			: self::get_background_threshold( $view );

		return max( 1, $threshold );
	}

	/**
	 * Returns the entry count that queues an action after applying the synchronous processing limit.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param View   $view       View.
	 *
	 * @return int
	 */
	public static function get_background_queue_threshold( $action_key, array $action, View $view ) {
		$threshold  = self::get_action_background_threshold( $action_key, $action, $view );
		$sync_limit = self::get_max_entry_ids( $view );

		return min( $threshold, $sync_limit + 1 );
	}

	/**
	 * Returns action-specific background status display settings.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string     $action_key        Action key.
	 * @param array      $action            Action config.
	 * @param View       $view              View.
	 * @param array|null $settings_snapshot Optional saved settings snapshot.
	 *
	 * @return array{polling:bool,completion_behavior:string,sticky_result:bool}
	 */
	public static function get_action_background_status_settings( $action_key, array $action, View $view, ?array $settings_snapshot = null ) {
		return self::normalize_background_status_settings(
			self::get_action_settings( $view, $action_key, $action, $settings_snapshot )
		);
	}

	/**
	 * Returns normalized background status display settings.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $settings Action settings.
	 *
	 * @return array{polling:bool,completion_behavior:string,sticky_result:bool}
	 */
	public static function normalize_background_status_settings( array $settings ) {
		$polling = array_key_exists( self::ACTION_BACKGROUND_PROGRESS_POLLING_SETTING, $settings )
			? (bool) $settings[ self::ACTION_BACKGROUND_PROGRESS_POLLING_SETTING ]
			: true;

		$sticky_result = array_key_exists( self::ACTION_BACKGROUND_STICKY_RESULT_SETTING, $settings )
			? (bool) $settings[ self::ACTION_BACKGROUND_STICKY_RESULT_SETTING ]
			: false;

		return [
			'polling'             => $polling,
			'completion_behavior' => self::normalize_background_completion_behavior(
				$settings[ self::ACTION_BACKGROUND_COMPLETION_BEHAVIOR_SETTING ] ?? self::BACKGROUND_COMPLETE_SHOW_MESSAGE
			),
			'sticky_result'       => $sticky_result,
		];
	}

	/**
	 * Returns a valid background completion behavior.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param mixed $behavior Completion behavior.
	 *
	 * @return string
	 */
	public static function normalize_background_completion_behavior( $behavior ) {
		$behavior = (string) $behavior;

		return in_array( $behavior, self::get_background_completion_behaviors(), true ) ? $behavior : self::BACKGROUND_COMPLETE_SHOW_MESSAGE;
	}

	/**
	 * Returns valid background completion behavior values.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @return string[]
	 */
	private static function get_background_completion_behaviors() {
		return [
			self::BACKGROUND_COMPLETE_SHOW_MESSAGE,
			self::BACKGROUND_COMPLETE_RELOAD_LINK,
			self::BACKGROUND_COMPLETE_AUTO_RELOAD,
		];
	}

	/**
	 * Returns normalized action settings schema.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	public static function get_action_settings_schema( array $action, ?View $view = null, $action_key = '' ) {
		$schema = $action['settings_schema'] ?? [];
		$action = self::inject_saved_action_settings_for_schema( $action, $view, $action_key );

		if ( ! is_array( $schema ) ) {
			$schema = [];
		}

		if ( self::action_supports_background( $action ) ) {
			$background_schema = self::get_background_action_settings_schema( $action );

			foreach ( array_keys( $background_schema ) as $reserved_key ) {
				unset( $schema[ $reserved_key ] );
			}

			$schema = $schema + $background_schema;
		}

		$normalized = [];

		foreach ( $schema as $setting_key => $setting ) {
			$setting_key = sanitize_key( $setting_key );

			if ( '' === $setting_key || ! is_array( $setting ) ) {
				continue;
			}

			$setting['type']  = empty( $setting['type'] ) ? 'text' : sanitize_key( $setting['type'] );
			$setting['value'] = array_key_exists( 'default', $setting ) ? $setting['default'] : ( $setting['value'] ?? null );

			if ( isset( $setting['picker_config'] ) ) {
				$setting['picker_config'] = self::normalize_picker_config( $setting['picker_config'] );
			}

			if ( isset( $setting['depends_on'] ) ) {
				$setting['depends_on'] = self::normalize_depends_on( $setting['depends_on'] );
			}

			$dynamic_settings = [
				'options'               => 'options_callback',
				'hidden_options'        => 'hidden_options_callback',
				'empty_options_message' => 'empty_options_message',
			];

			foreach ( $dynamic_settings as $dynamic_key => $callback_key ) {
				if ( empty( $setting[ $callback_key ] ) || ! is_callable( $setting[ $callback_key ] ) ) {
					continue;
				}

				$dynamic_value = call_user_func( $setting[ $callback_key ], $view, $action, sanitize_key( (string) $action_key ), $setting );

				if ( 'empty_options_message' === $dynamic_key ) {
					$setting[ $dynamic_key ] = is_scalar( $dynamic_value ) ? (string) $dynamic_value : '';
					continue;
				}

				if ( is_array( $dynamic_value ) ) {
					$setting[ $dynamic_key ] = $dynamic_value;
				}
			}

			$normalized[ $setting_key ] = $setting;
		}

		return $normalized;
	}

	/**
	 * Adds saved action settings to the action passed to schema callbacks.
	 *
	 * @since 3.0.0
	 *
	 * @param array     $action     Action config.
	 * @param View|null $view       View.
	 * @param string    $action_key Action key.
	 *
	 * @return array
	 */
	private static function inject_saved_action_settings_for_schema( array $action, ?View $view, $action_key ) {
		$action_key = sanitize_key( (string) $action_key );

		if ( ! $view || '' === $action_key ) {
			return $action;
		}

		$saved = self::get_raw_action_settings( $view, $action_key );

		if ( [] === $saved ) {
			return $action;
		}

		$current            = isset( $action['settings'] ) && is_array( $action['settings'] ) ? $action['settings'] : [];
		$action['settings'] = array_merge( $saved, $current );

		return $action;
	}

	/**
	 * Returns raw saved settings for one action.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view       View.
	 * @param string $action_key Action key.
	 *
	 * @return array
	 */
	private static function get_raw_action_settings( View $view, $action_key ) {
		static $cache = [];

		$cache_key = (int) $view->ID . ':' . sanitize_key( (string) $action_key );

		if ( array_key_exists( $cache_key, $cache ) ) {
			return $cache[ $cache_key ];
		}

		$widget_settings = self::get_widget_settings( $view );
		$saved_settings  = $widget_settings ? (array) $widget_settings->get( self::ACTION_SETTINGS, [] ) : [];

		$cache[ $cache_key ] = isset( $saved_settings[ $action_key ] ) && is_array( $saved_settings[ $action_key ] ) ? $saved_settings[ $action_key ] : [];

		return $cache[ $cache_key ];
	}

	/**
	 * Returns a JSON-safe picker config array.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $config Picker config.
	 *
	 * @return array
	 */
	public static function normalize_picker_config( $config ) {
		if ( ! is_array( $config ) ) {
			return [];
		}

		$valid  = true;
		$config = self::normalize_json_value( $config, $valid );

		return $valid && is_array( $config ) ? $config : [];
	}

	/**
	 * Returns JSON-safe data, dropping functions, objects, and resources.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value Value.
	 * @param bool  $valid Whether the value is JSON-safe.
	 *
	 * @return mixed
	 */
	private static function normalize_json_value( $value, &$valid = true ) {
		if ( null === $value || is_bool( $value ) || is_int( $value ) || is_float( $value ) || is_string( $value ) ) {
			$valid = true;

			return $value;
		}

		if ( ! is_array( $value ) ) {
			$valid = false;

			return null;
		}

		$normalized = [];

		foreach ( $value as $key => $item ) {
			$item_valid = true;
			$item       = self::normalize_json_value( $item, $item_valid );

			if ( ! $item_valid ) {
				continue;
			}

			$normalized[ $key ] = $item;
		}

		$valid = true;

		return $normalized;
	}

	/**
	 * Normalizes setting dependencies.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $depends_on Setting dependency keys.
	 *
	 * @return string[]
	 */
	private static function normalize_depends_on( $depends_on ) {
		$depends_on = is_array( $depends_on ) ? $depends_on : [ $depends_on ];
		$depends_on = array_map( 'sanitize_key', array_map( 'strval', $depends_on ) );
		$depends_on = array_values( array_filter( array_unique( $depends_on ) ) );

		return $depends_on;
	}

	/**
	 * Returns the shared settings schema for background-capable actions.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	private static function get_background_action_settings_schema( array $action ) {
		$background          = self::get_action_background_config( $action );
		$always              = ! empty( $background['always'] );
		$threshold           = isset( $background['threshold'] )
			? max( 1, (int) $background['threshold'] )
			: self::BACKGROUND_THRESHOLD;
		$completion_behavior = self::normalize_background_completion_behavior(
			$background['completion_behavior'] ?? self::BACKGROUND_COMPLETE_SHOW_MESSAGE
		);
		$schema              = [];

		if ( ! $always ) {
			$schema[ self::ACTION_BACKGROUND_ENABLED_SETTING ] = [
				'type'    => 'checkbox',
				'label'   => __( 'Run in background', 'gk-gravityview' ),
				'default' => array_key_exists( 'default_enabled', $background ) ? (int) (bool) $background['default_enabled'] : 1,
				'desc'    => __( 'Queue this action instead of running it during the page request when the selected entry count reaches the threshold below.', 'gk-gravityview' ),
			];

			$schema[ self::ACTION_BACKGROUND_THRESHOLD_SETTING ] = [
				'type'     => 'number',
				'label'    => __( 'Queue when selection is at least', 'gk-gravityview' ),
				'default'  => $threshold,
				'min'      => 1,
				'class'    => 'small-text',
				'requires' => self::ACTION_BACKGROUND_ENABLED_SETTING,
				'desc'     => __( 'Smaller selections run immediately. Set to 1 to always queue this action. Selections above the synchronous processing limit are queued even if this value is higher.', 'gk-gravityview' ),
			];
		}

		$schema[ self::ACTION_BACKGROUND_PROGRESS_POLLING_SETTING ] = [
			'type'    => 'checkbox',
			'label'   => __( 'Update progress automatically', 'gk-gravityview' ),
			'default' => array_key_exists( 'progress_polling', $background ) ? (int) (bool) $background['progress_polling'] : 1,
			'desc'    => __( 'Show live progress in the background action notice while this action runs.', 'gk-gravityview' ),
		];

		$schema[ self::ACTION_BACKGROUND_COMPLETION_BEHAVIOR_SETTING ] = [
			'type'    => 'select',
			'label'   => __( 'When background processing finishes', 'gk-gravityview' ),
			'default' => $completion_behavior,
			'options' => [
				self::BACKGROUND_COMPLETE_SHOW_MESSAGE => __( 'Show the result message', 'gk-gravityview' ),
				self::BACKGROUND_COMPLETE_RELOAD_LINK  => __( 'Show the result message and a reload link', 'gk-gravityview' ),
				self::BACKGROUND_COMPLETE_AUTO_RELOAD  => __( 'Reload the View automatically', 'gk-gravityview' ),
			],
			'desc'    => __( 'Use a reload option when this action changes entries displayed in the View.', 'gk-gravityview' ),
		];

		$schema[ self::ACTION_BACKGROUND_STICKY_RESULT_SETTING ] = [
			'type'    => 'checkbox',
			'label'   => __( 'Keep result message until dismissed', 'gk-gravityview' ),
			'default' => array_key_exists( 'sticky_result', $background ) ? (int) (bool) $background['sticky_result'] : 0,
			'desc'    => __( 'Keep finished background action messages visible across page reloads until users dismiss them.', 'gk-gravityview' ),
		];

		if ( ! $always ) {
			$schema[ self::ACTION_BACKGROUND_PROGRESS_POLLING_SETTING ]['requires']     = self::ACTION_BACKGROUND_ENABLED_SETTING;
			$schema[ self::ACTION_BACKGROUND_COMPLETION_BEHAVIOR_SETTING ]['requires'] = self::ACTION_BACKGROUND_ENABLED_SETTING;
			$schema[ self::ACTION_BACKGROUND_STICKY_RESULT_SETTING ]['requires']       = self::ACTION_BACKGROUND_ENABLED_SETTING;
		}

		return $schema;
	}

	/**
	 * Returns default values from an action settings schema.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action config.
	 *
	 * @return array
	 */
	public static function get_action_setting_defaults( array $action, ?View $view = null, $action_key = '' ) {
		$defaults = [];

		foreach ( self::get_action_settings_schema( $action, $view, $action_key ) as $setting_key => $setting ) {
			$defaults[ $setting_key ] = $setting['value'] ?? null;
		}

		return $defaults;
	}

	/**
	 * Sanitizes saved action settings using a schema.
	 *
	 * @since 3.0.0
	 *
	 * @param array $values Saved values.
	 * @param array $schema Settings schema.
	 *
	 * @return array
	 */
	public static function sanitize_action_settings( array $values, array $schema ) {
		$sanitized = [];

		foreach ( $schema as $setting_key => $setting ) {
			if ( ! array_key_exists( $setting_key, $values ) ) {
				continue;
			}

			$value = $values[ $setting_key ];

			if ( ! empty( $setting['sanitize_callback'] ) && is_callable( $setting['sanitize_callback'] ) ) {
				$sanitized[ $setting_key ] = call_user_func( $setting['sanitize_callback'], $value, $setting );
				continue;
			}

			$scalar_value = is_scalar( $value ) ? $value : '';

			switch ( $setting['type'] ?? 'text' ) {
				case 'checkbox':
					$sanitized[ $setting_key ] = (int) (bool) $value;
					break;
				case 'number':
					$number = is_numeric( $scalar_value ) ? 0 + $scalar_value : 0;

					if ( isset( $setting['min'] ) && is_numeric( $setting['min'] ) ) {
						$number = max( $number, 0 + $setting['min'] );
					}

					if ( isset( $setting['max'] ) && is_numeric( $setting['max'] ) ) {
						$number = min( $number, 0 + $setting['max'] );
					}

					$sanitized[ $setting_key ] = $number;
					break;
				case 'select':
				case 'radio':
					$options = ! empty( $setting['options'] ) && is_array( $setting['options'] ) ? array_keys( $setting['options'] ) : [];
					$value   = sanitize_text_field( wp_unslash( (string) $scalar_value ) );

					if ( [] !== $options && ! in_array( $value, array_map( 'strval', $options ), true ) ) {
						$value = (string) ( $setting['value'] ?? '' );
					}

					$sanitized[ $setting_key ] = $value;
					break;
				case 'multiselect':
					$options = ! empty( $setting['options'] ) && is_array( $setting['options'] ) ? array_map( 'strval', array_keys( $setting['options'] ) ) : [];
					$items   = is_array( $value ) ? $value : [];
					$items   = array_map(
						static function ( $item ) {
							return sanitize_text_field( wp_unslash( (string) $item ) );
						},
						$items
					);
					$items   = array_values( array_filter( $items, 'strlen' ) );
					$items   = array_values( array_unique( $items ) );

					if ( [] !== $options ) {
						$items = array_values( array_intersect( $items, $options ) );
					}

					$sanitized[ $setting_key ] = $items;
					break;
				default:
					$sanitized[ $setting_key ] = sanitize_text_field( wp_unslash( (string) $scalar_value ) );
					break;
			}
		}

		return $sanitized;
	}

	/**
	 * Normalizes action key settings.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $selected Selected action keys.
	 *
	 * @return string[]
	 */
	private static function normalize_action_key_setting( $selected ) {
		if ( ! is_array( $selected ) ) {
			return [];
		}

		$keys = array_keys( array_filter( $selected ) );

		if ( array_values( $selected ) === $selected ) {
			$keys = array_filter( array_map( 'sanitize_key', $selected ) );
		}

		return array_values( array_map( 'sanitize_key', $keys ) );
	}

	/**
	 * Returns the current background processing dispatch error, if any.
	 *
	 * @since 3.0.0
	 *
	 * @return \WP_Error|null
	 */
	public static function get_background_processing_error() {
		return Scheduler::instance()->dispatch_error( false );
	}

	/**
	 * Returns whether background processing is available and able to dispatch jobs.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function is_background_processing_available() {
		$error     = self::get_background_processing_error();
		$available = null === $error;

		/**
		 * Filters whether frontend bulk action background processing is available.
		 *
		 * @since 3.0.0
		 *
		 * @param bool           $available Whether background processing can dispatch jobs.
		 * @param \WP_Error|null $error     Dispatch error, or null when available.
		 */
		return (bool) apply_filters( 'gk/gravityview/bulk-actions/background-processing-available', $available, $error );
	}

	/**
	 * Returns whether background processing settings should be shown in the View editor.
	 *
	 * This intentionally checks only whether Foundation background processing is enabled.
	 * It must not run a dispatch health probe while widgets are registered, because widget
	 * registration happens on broad admin requests, including Foundation's Background Jobs page.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public static function is_background_processing_configurable() {
		return Scheduler::instance()->is_enabled();
	}

	/**
	 * Returns whether background processing is enabled for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return bool
	 */
	public static function is_background_processing_enabled( View $view ) {
		unset( $view );

		return self::is_background_processing_available();
	}

	/**
	 * Returns the selected bulk action selection behavior for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return string
	 */
	public static function get_selection_behavior( View $view ) {
		$settings = self::get_widget_settings( $view );
		$behavior = $settings ? $settings->get( 'bulk_actions_selection_behavior', self::SELECTION_BEHAVIOR_ACROSS_PAGES ) : self::SELECTION_BEHAVIOR_ACROSS_PAGES;

		return in_array( $behavior, [ self::SELECTION_BEHAVIOR_ACROSS_PAGES, self::SELECTION_BEHAVIOR_CURRENT_PAGE ], true ) ? $behavior : self::SELECTION_BEHAVIOR_ACROSS_PAGES;
	}

	/**
	 * Returns the Bulk Actions widget instance for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return \GV\Widget|null
	 */
	public static function get_widget( View $view ) {
		if ( ! $view->widgets ) {
			return null;
		}

		$widget = $view->widgets->by_id( self::WIDGET_ID )->first();

		return $widget ? $widget : null;
	}

	/**
	 * Returns the Bulk Actions widget settings for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return \GV\Settings|null
	 */
	public static function get_widget_settings( View $view ) {
		$widget = self::get_widget( $view );

		return $widget ? $widget->configuration : null;
	}

	/**
	 * Whether selected entries can persist across pages for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return bool
	 */
	public static function is_cross_page_selection_enabled( View $view ) {
		return self::SELECTION_BEHAVIOR_ACROSS_PAGES === self::get_selection_behavior( $view );
	}

	/**
	 * Whether the Show selected entries link is enabled for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return bool
	 */
	public static function is_show_selected_enabled( View $view ) {
		$settings = self::get_widget_settings( $view );

		return $settings ? (bool) $settings->get( 'bulk_actions_show_selected', true ) : true;
	}

	/**
	 * Returns the selected-entry state lifetime in seconds.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view Optional View context.
	 *
	 * @return int
	 */
	public static function get_selection_ttl( ?View $view = null ) {
		unset( $view );

		return self::SELECTION_TTL;
	}

	/**
	 * Returns the selected-results token lifetime in seconds.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return int
	 */
	public static function get_show_selected_ttl( View $view ) {
		unset( $view );

		return self::SELECTION_TTL;
	}

	/**
	 * Returns the nonce action for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return string
	 */
	public static function get_nonce_action( View $view ) {
		return 'gk_gravityview_bulk_actions_' . $view->ID;
	}

	/**
	 * Returns the nonce action for a View background job request.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view  View.
	 * @param string $token Background result token.
	 *
	 * @return string
	 */
	public static function get_background_job_nonce_action( View $view, $token ) {
		return 'gk_gravityview_bulk_actions_job_' . $view->ID . '_' . sanitize_key( $token );
	}

	/**
	 * Returns the localStorage key for a View, form, and render instance.
	 *
	 * @since 3.0.0
	 *
	 * @param View   $view            View.
	 * @param string $render_instance Render instance ID.
	 *
	 * @return string
	 */
	public static function get_storage_key( View $view, $render_instance ) {
		$parts = [
			(int) $view->ID,
			$view->form ? (int) $view->form->ID : 0,
			sanitize_key( (string) $render_instance ),
		];

		return 'gk-gravityview-bulk-actions-' . md5( implode( '|', $parts ) );
	}

	/**
	 * Returns the form action URL without previous bulk action messages.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public static function get_form_action_url() {
		return remove_query_arg( [ self::QUERY_TOKEN, self::QUERY_BACKGROUND_TOKEN, self::QUERY_SELECTION_MODE, self::QUERY_SELECTION_TOKEN, 'gv_bulk_status', 'gv_bulk_message', 'gv_bulk_view_id' ] );
	}
}
