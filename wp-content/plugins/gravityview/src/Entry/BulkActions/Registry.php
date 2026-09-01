<?php
/**
 * Frontend bulk action registry.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\Entry\BulkActions\Actions\ApproveAction;
use GravityKit\GravityView\Entry\BulkActions\Actions\BulkEditAction;
use GravityKit\GravityView\Entry\BulkActions\Actions\BulkAction;
use GravityKit\GravityView\Entry\BulkActions\Actions\DeleteEntriesAction;
use GravityKit\GravityView\Entry\BulkActions\Actions\DisapproveAction;
use GravityKit\GravityView\Entry\BulkActions\Actions\DownloadAttachmentsAction;
use GravityKit\GravityView\Entry\BulkActions\Actions\ExportCsvAction;
use GravityKit\GravityView\Entry\BulkActions\Actions\ResendNotificationsAction;
use GravityKit\GravityView\Entry\BulkActions\Actions\UnapproveAction;
use GravityKit\GravityView\View\View;
use GVCommon;

/**
 * Resolves registered, configured, and user-available bulk actions.
 *
 * @since 3.0.0
 */
final class Registry {
	/**
	 * Returns registered bulk actions.
	 *
	 * Plugins can add actions by returning an item with a label and callback:
	 *
	 * `feature_entries => [ 'label' => 'Feature these entries', 'callback' => callable ]`
	 *
	 * Supported action arguments:
	 * - label: Dropdown label.
	 * - callback: Callable that processes the action.
	 * - capability: Optional capability required before display or processing.
	 * - required_fields: Optional field ID or field IDs that must be configured in the View.
	 * - available_callback: Optional callable that receives the View and action config and returns whether the action is available.
	 * - confirmation: Optional boolean, string, or array that controls the confirmation UI.
	 * - background: Optional boolean or array that marks the action as safe for background processing.
	 *   Array options include enabled, batch_size, threshold, default_enabled, queued_message, and complete_callback.
	 * - lock: Optional boolean. Defaults to true. Set false for read-only actions that can run while another action is active for the same View.
	 * - settings_schema: Optional schema for per-action widget settings. The resolved values are available in `$action['settings']`.
	 * - request_callback: Optional callable that sanitizes action-specific submitted data. The sanitized values are available in `$action['request']`.
	 * - frontend_data_callback: Optional callable that returns action-specific data for the frontend controls.
	 * - dismiss_callback: Optional callable that runs when a sticky terminal background notice is dismissed.
	 * - supports_multi_form_view: Optional boolean. Set true for actions that can safely run against Views with joins or unions.
	 *
	 * The callback receives: entry IDs, entry arrays keyed by ID, View, action key, action config.
	 * Background callbacks receive the background context as an optional sixth argument.
	 * Dismiss callbacks receive: View, action key, action config, stored result data, and result token.
	 *
	 * @since 3.0.0
	 *
	 * @param View|null $view The View context.
	 *
	 * @return array
	 */
	public static function get_actions( ?View $view = null ) {
		$actions = self::get_builtin_actions();

		/**
		 * Filters frontend bulk actions available to table Views.
		 *
		 * @since 3.0.0
		 *
		 * @param array     $actions Action configurations keyed by action slug.
		 * @param int       $view_id View ID, or 0 when unavailable.
		 * @param View|null $view    The View context, when available.
		 */
		$actions = apply_filters( 'gk/gravityview/bulk-actions/actions', $actions, $view ? (int) $view->ID : 0, $view );

		return $view ? self::filter_actions_by_required_fields( $actions, $view ) : $actions;
	}

	/**
	 * Returns core bulk actions shipped by GravityView.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	private static function get_builtin_actions() {
		$actions = [];

		foreach ( self::get_builtin_action_objects() as $action ) {
			$actions[ $action->key() ] = $action->config();
		}

		return $actions;
	}

	/**
	 * Returns built-in bulk action objects.
	 *
	 * @since 3.0.0
	 *
	 * @return BulkAction[]
	 */
	private static function get_builtin_action_objects() {
		return [
			new ApproveAction(),
			new DisapproveAction(),
			new UnapproveAction(),
			new DeleteEntriesAction(),
			new BulkEditAction(),
			new ResendNotificationsAction(),
			new DownloadAttachmentsAction(),
			new ExportCsvAction(),
		];
	}

	/**
	 * Returns action options for the widget setting.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public static function get_setting_options() {
		$options = [];

		foreach ( self::get_actions() as $key => $action ) {
			if ( empty( $action['label'] ) ) {
				continue;
			}

			$options[ $key ] = $action['label'];
		}

		return self::sort_setting_options_by_label( $options );
	}

	/**
	 * Returns labels for actions that can run in the background.
	 *
	 * @since 3.0.0
	 *
	 * @return string[]
	 */
	public static function get_background_capable_setting_labels() {
		return array_values( self::get_background_capable_setting_options() );
	}

	/**
	 * Returns setting options for actions that can run in the background.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public static function get_background_capable_setting_options() {
		$options = [];

		foreach ( self::get_actions() as $key => $action ) {
			if ( empty( $action['label'] ) || ! Config::action_supports_background( $action ) ) {
				continue;
			}

			$options[ $key ] = $action['label'];
		}

		return self::sort_setting_options_by_label( $options );
	}

	/**
	 * Sorts setting options by label using GravityView's natural sort behavior.
	 *
	 * @since 3.0.0
	 *
	 * @param string[] $options Labels keyed by option value.
	 *
	 * @return string[]
	 */
	private static function sort_setting_options_by_label( array $options ) {
		uksort(
			$options,
			static function ( $a, $b ) use ( $options ) {
				$result = strnatcasecmp( (string) $options[ $a ], (string) $options[ $b ] );

				return 0 === $result ? strnatcasecmp( (string) $a, (string) $b ) : $result;
			}
		);

		return $options;
	}

	/**
	 * Default selected background action widget setting values.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public static function get_default_background_action_setting_values() {
		return array_keys( self::get_background_capable_setting_options() );
	}

	/**
	 * Returns action field requirements for admin-side availability updates.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public static function get_setting_field_requirements() {
		$requirements = [];
		$actions      = self::get_actions();

		foreach ( $actions as $action_key => $action ) {
			$requirements[ $action_key ] = self::get_required_fields( $action );
		}

		/**
		 * Filters action field requirements exposed to the View editor.
		 *
		 * Return an empty array for an action to keep it available regardless of configured fields.
		 *
		 * @since 3.0.0
		 *
		 * @param array $requirements Required field IDs keyed by action key.
		 * @param array $actions      Registered actions keyed by action key.
		 * @param int   $view_id      Current admin View ID, or 0 when unavailable.
		 */
		return (array) apply_filters( 'gk/gravityview/bulk-actions/action-field-requirements', $requirements, $actions, absint( get_the_ID() ) );
	}

	/**
	 * Default selected action widget setting values.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public static function get_default_action_setting_values() {
		return [];
	}

	/**
	 * Returns actions selected in the Bulk Actions widget and available to current user.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return array
	 */
	public function get_available_actions( View $view ) {
		$actions       = self::get_actions( $view );
		$selected_keys = $this->get_selected_action_keys( $view );
		$available     = [];
		$multi_form    = ( new ViewEligibility( $this ) )->is_multi_form_view( $view );

		foreach ( $selected_keys as $key ) {
			if ( empty( $actions[ $key ]['label'] ) || empty( $actions[ $key ]['callback'] ) || ! is_callable( $actions[ $key ]['callback'] ) ) {
				continue;
			}

			$action = Config::resolve_action_config( $view, $key, $actions[ $key ] );

			if ( $multi_form && ! self::action_supports_multi_form_view( $action ) ) {
				continue;
			}

			if ( ! empty( $action['capability'] ) && ! GVCommon::has_cap( $action['capability'], $view->ID ) ) {
				continue;
			}

			if ( ! $this->is_action_available( $key, $action, $view ) ) {
				continue;
			}

			$available[ $key ] = $action;
		}

		return $available;
	}

	/**
	 * Whether an action declares support for multi-form Views.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $action Action configuration.
	 *
	 * @return bool
	 */
	public static function action_supports_multi_form_view( array $action ) {
		return ! empty( $action['supports_multi_form_view'] );
	}

	/**
	 * Returns action keys selected in the Bulk Actions widget settings.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return string[]
	 */
	public function get_selected_action_keys( View $view ) {
		$settings = $this->get_widget_settings( $view );

		if ( null === $settings ) {
			return [];
		}

		$selected = $settings->get( 'bulk_actions', self::get_default_action_setting_values() );

		if ( empty( $selected ) || ! is_array( $selected ) ) {
			return [];
		}

		$keys = array_keys( array_filter( $selected ) );

		if ( array_values( $selected ) === $selected ) {
			$keys = array_filter( array_map( 'sanitize_key', $selected ) );
		}

		return array_values( array_intersect( $keys, array_keys( self::get_actions( $view ) ) ) );
	}

	/**
	 * Whether the View has a Bulk Actions widget configured.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return bool
	 */
	public function has_widget( View $view ) {
		return null !== $this->get_widget( $view );
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
	public function get_widget( View $view ) {
		return Config::get_widget( $view );
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
	public function get_widget_settings( View $view ) {
		return Config::get_widget_settings( $view );
	}

	/**
	 * Filters actions by required View fields.
	 *
	 * @since 3.0.0
	 *
	 * @param array $actions Actions.
	 * @param View  $view    View.
	 *
	 * @return array
	 */
	private static function filter_actions_by_required_fields( array $actions, View $view ) {
		foreach ( $actions as $key => $action ) {
			if ( self::action_has_required_fields( $key, $action, $view ) ) {
				continue;
			}

			unset( $actions[ $key ] );
		}

		return $actions;
	}

	/**
	 * Whether an action's required fields are configured in the View.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param View   $view       View.
	 *
	 * @return bool
	 */
	private static function action_has_required_fields( $action_key, array $action, View $view ) {
		$required_fields = self::get_required_fields( $action );

		if ( empty( $required_fields ) ) {
			return true;
		}

		$has_fields = self::view_has_fields( $view, $required_fields );

		/**
		 * Filters whether an action's required View fields are configured.
		 *
		 * Return true to bypass field-based availability for an action.
		 *
		 * @since 3.0.0
		 *
		 * @param bool   $has_fields      Whether the required fields are configured.
		 * @param int    $view_id         View ID.
		 * @param View   $view            View.
		 * @param string $action_key      Action key.
		 * @param array  $action          Action configuration.
		 * @param array  $required_fields Required field IDs.
		 */
		return (bool) apply_filters( 'gk/gravityview/bulk-actions/action-field-requirement-met', $has_fields, (int) $view->ID, $view, $action_key, $action, $required_fields );
	}

	/**
	 * Returns normalized required field IDs for an action.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action configuration.
	 *
	 * @return string[]
	 */
	private static function get_required_fields( array $action ) {
		$required_fields = $action['required_fields'] ?? [];

		return array_values( array_unique( array_filter( array_map( 'strval', (array) $required_fields ) ) ) );
	}

	/**
	 * Whether all field IDs are configured in the View's multiple-entry layout.
	 *
	 * @since 3.0.0
	 *
	 * @param View     $view      View.
	 * @param string[] $field_ids Field IDs.
	 *
	 * @return bool
	 */
	private static function view_has_fields( View $view, array $field_ids ) {
		$configured = [];

		foreach ( $view->fields->by_position( 'directory_*' )->all() as $field ) {
			$configured[] = (string) $field->ID;
		}

		return empty( array_diff( $field_ids, $configured ) );
	}

	/**
	 * Whether an action is available beyond settings and capability checks.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action configuration.
	 * @param View   $view       View.
	 *
	 * @return bool
	 */
	private function is_action_available( $action_key, array $action, View $view ) {
		$available = true;

		if ( ! empty( $action['available_callback'] ) && is_callable( $action['available_callback'] ) ) {
			$available = (bool) call_user_func( $action['available_callback'], $view, $action, $action_key );
		}

		/**
		 * Filters whether an action is available in the View.
		 *
		 * @since 3.0.0
		 *
		 * @param bool   $available  Whether the action is available.
		 * @param int    $view_id    View ID.
		 * @param View   $view       View.
		 * @param string $action_key Action key.
		 * @param array  $action     Action configuration.
		 */
		return (bool) apply_filters( 'gk/gravityview/bulk-actions/action-is-available', $available, (int) $view->ID, $view, $action_key, $action );
	}
}
