<?php
/**
 * Bulk Actions widget type.
 *
 * @package GravityKit\GravityView\Widget\Types
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Widget\Types;

use Closure;
use GravityKit\GravityView\Entry\BulkActions\BackgroundStatus;
use GravityKit\GravityView\Entry\BulkActions\Config;
use GravityKit\GravityView\Entry\BulkActions\EntryResolver;
use GravityKit\GravityView\Entry\BulkActions\FlashMessages;
use GravityKit\GravityView\Entry\BulkActions\Registry;
use GravityKit\GravityView\Entry\BulkActions\Renderer;
use GravityKit\GravityView\Entry\BulkActions\SelectionMode;
use GravityKit\GravityView\Entry\BulkActions\ViewEligibility;
use GravityKit\GravityView\Utils\Assets as AssetUtils;
use GravityKit\GravityView\View\View;

/**
 * Widget that renders the frontend Bulk Actions toolbar for table Views.
 *
 * @since 3.0.0
 */
final class BulkActions extends \GV\Widget {
	/**
	 * Whether admin hooks have been added.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private static $admin_hooks_added = false;

	/**
	 * @since 3.0.0
	 * @var string
	 */
	protected $shortcode_name = 'gravityview_widget_bulk_actions';

	/**
	 * @since 3.0.0
	 * @var string
	 */
	public $icon = 'dashicons-list-view';

	/**
	 * @since 3.0.0
	 * @var bool
	 */
	protected $show_on_single = false;

	/**
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->widget_description = __( 'Display controls for applying actions to selected table entries.', 'gk-gravityview' );

		$settings = [
			'bulk_actions'                    => [
				'type'               => 'multiselect',
				'label'              => __( 'Actions', 'gk-gravityview' ),
				'value'              => Registry::get_default_action_setting_values(),
				'options'            => Registry::get_setting_options(),
				'class'              => 'gv-tom-select',
				'placeholder'        => __( 'Select bulk actions', 'gk-gravityview' ),
				'submit_empty_value' => true,
				'data'               => [
					'bulk-actions-field-requirements' => wp_json_encode( Registry::get_setting_field_requirements() ),
					'bulk-actions-widget-summary'     => '1',
					'bulk-actions-summary-empty'      => __( 'No actions selected', 'gk-gravityview' ),
					'bulk-actions-summary-more-one'   => __( '+[count] action', 'gk-gravityview' ),
					'bulk-actions-summary-more-many'  => __( '+[count] actions', 'gk-gravityview' ),
				],
				'desc'               => __( 'Choose the bulk actions available in this View.', 'gk-gravityview' ),
			],
			Config::ACTION_SETTINGS           => [
				'type'  => 'html',
				'value' => [],
				'desc'  => Closure::fromCallable( [ $this, 'render_action_settings_manager' ] ),
			],
			'bulk_actions_checkbox_position'  => [
				'type'    => 'select',
				'label'   => __( 'Selection checkbox position', 'gk-gravityview' ),
				'value'   => 'first',
				'options' => [
					'first' => __( 'First column', 'gk-gravityview' ),
					'last'  => __( 'Last column', 'gk-gravityview' ),
				],
				'desc'    => __( 'Choose where selection checkboxes appear in the table.', 'gk-gravityview' ),
			],
			'bulk_actions_selection_behavior' => [
				'type'    => 'select',
				'label'   => __( 'Bulk selection behavior', 'gk-gravityview' ),
				'value'   => Config::SELECTION_BEHAVIOR_ACROSS_PAGES,
				'options' => [
					Config::SELECTION_BEHAVIOR_ACROSS_PAGES => __( 'Across pages', 'gk-gravityview' ),
					Config::SELECTION_BEHAVIOR_CURRENT_PAGE => __( 'Current page only', 'gk-gravityview' ),
				],
				'desc'    => __( 'Choose whether selected entries stay selected while users move between pages.', 'gk-gravityview' ),
			],
			'bulk_actions_summary_position'   => [
				'type'    => 'select',
				'label'   => __( 'Selection summary position', 'gk-gravityview' ),
				'value'   => 'table_body_before',
				'options' => [
					'table_body_before' => __( 'Below table header', 'gk-gravityview' ),
					'toolbar'           => __( 'Next to Bulk Actions menu', 'gk-gravityview' ),
					'none'              => __( 'Hidden', 'gk-gravityview' ),
				],
				'desc'    => __( 'Choose where the selected count and select-all links appear.', 'gk-gravityview' ),
			],
			'bulk_actions_show_selected'      => [
				'type'  => 'checkbox',
				'label' => __( 'Show selected entries link', 'gk-gravityview' ),
				'value' => 1,
				'desc'  => __( 'Allow users to see only the entries they have selected.', 'gk-gravityview' ),
			],
		];

		$settings = $this->add_background_processing_settings( $settings );

		if ( ! self::$admin_hooks_added ) {
			self::$admin_hooks_added = true;
			add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ], 1100 );
			add_filter( 'gk/gravityview/admin/widget-info', [ $this, 'add_widget_summary_info' ], 10, 4 );
		}

		parent::__construct(
			__( 'Bulk Actions', 'gk-gravityview' ),
			Config::WIDGET_ID,
			[
				'header' => 1,
				'footer' => 1,
			],
			$settings
		);
	}

	/**
	 * Enqueues Bulk Actions widget admin behavior for the View editor.
	 *
	 * @since 3.0.0
	 *
	 * @param string $hook Current admin hook.
	 *
	 * @return void
	 */
	public function add_admin_scripts( $hook ) {
		if ( ! gravityview()->request->is_admin( $hook, 'single' ) ) {
			return;
		}

		$path = 'js/bulk-actions-admin' . AssetUtils::min() . '.js';

		wp_enqueue_script(
			'gravityview-bulk-actions-admin',
			AssetUtils::url( $path ),
			[ 'jquery', 'gravityview_views_scripts' ],
			filemtime( AssetUtils::path( $path ) ),
			true
		);
	}

	/**
	 * Adds selected action summary information to the View editor widget row.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $field_info_items Existing info items.
	 * @param string $widget_id        Widget ID.
	 * @param array  $settings         Widget settings.
	 * @param array  $item             Widget item data.
	 *
	 * @return array
	 */
	public function add_widget_summary_info( array $field_info_items, string $widget_id, array $settings, array $item ): array {
		if ( Config::WIDGET_ID !== $widget_id || empty( $settings ) ) {
			return $field_info_items;
		}

		return [
			[
				'value' => $this->get_actions_summary( $settings ),
				'class' => 'gv-bulk-actions-widget-summary',
			],
		];
	}

	/**
	 * Returns selected action labels for the widget summary.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return string
	 */
	private function get_actions_summary( array $settings ) {
		$selected = $settings['bulk_actions'] ?? Registry::get_default_action_setting_values();
		$options  = Registry::get_setting_options();
		$keys     = $this->normalize_selected_action_keys( $selected );
		$labels   = [];

		foreach ( $keys as $key ) {
			if ( isset( $options[ $key ] ) ) {
				$labels[] = $options[ $key ];
			}
		}

		if ( empty( $labels ) ) {
			return __( 'No actions selected', 'gk-gravityview' );
		}

		$visible   = array_slice( $labels, 0, 3 );
		$remaining = count( $labels ) - count( $visible );

		if ( $remaining > 0 ) {
			/* translators: [count] is the number of additional configured bulk actions. */
			$visible[] = strtr(
				_n( '+[count] action', '+[count] actions', $remaining, 'gk-gravityview' ),
				[
					'[count]' => number_format_i18n( $remaining ),
				]
			);
		}

		return implode( ', ', $visible );
	}

	/**
	 * Normalizes selected action keys from widget settings.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $selected Selected action setting.
	 *
	 * @return string[]
	 */
	private function normalize_selected_action_keys( $selected ) {
		if ( ! is_array( $selected ) ) {
			return [];
		}

		$keys = array_keys( array_filter( $selected ) );

		if ( array_values( $selected ) === $selected ) {
			$keys = array_filter( array_map( 'sanitize_key', $selected ) );
		}

		return array_values( $keys );
	}

	/**
	 * Renders per-action settings inside the Bulk Actions widget settings dialog.
	 *
	 * @since 3.0.0
	 *
	 * @param array $args Field arguments.
	 *
	 * @return string
	 */
	private function render_action_settings_manager( array $args ) {
		$view                 = $this->get_admin_view_context();
		$actions              = Registry::get_actions();
		$saved                = is_array( $args['value'] ?? null ) ? $args['value'] : [];
		$name                 = (string) ( $args['name'] ?? Config::ACTION_SETTINGS );
		$panels               = [];
		$rows                 = [];
		$rendered_action_keys = [];

		foreach ( $actions as $action_key => $action ) {
			$schema = Config::get_action_settings_schema( $action, $view, $action_key );

			if ( ! Config::is_background_processing_configurable() ) {
				unset(
					$schema[ Config::ACTION_BACKGROUND_ENABLED_SETTING ],
					$schema[ Config::ACTION_BACKGROUND_THRESHOLD_SETTING ],
					$schema[ Config::ACTION_BACKGROUND_PROGRESS_POLLING_SETTING ],
					$schema[ Config::ACTION_BACKGROUND_COMPLETION_BEHAVIOR_SETTING ],
					$schema[ Config::ACTION_BACKGROUND_STICKY_RESULT_SETTING ]
				);
			}

			if ( [] === $schema ) {
				continue;
			}

			$action_key = sanitize_key( $action_key );
			$settings   = Config::sanitize_action_settings(
				isset( $saved[ $action_key ] ) && is_array( $saved[ $action_key ] ) ? $saved[ $action_key ] : [],
				$schema
			);

			$defaults  = Config::get_action_setting_defaults( $action, $view, $action_key );
			$settings  = array_merge( $defaults, $settings );
			$label     = $action['label'] ?? $action_key;
			$preserved = isset( $saved[ $action_key ] ) && is_array( $saved[ $action_key ] ) ? array_diff_key( $saved[ $action_key ], $schema ) : [];

			$unavailable_notice = $this->get_action_unavailable_notice( $view, $action );

			$rows[] = $this->render_action_settings_row( $action_key, $label, $unavailable_notice );

			if ( null !== $unavailable_notice ) {
				continue;
			}

			$rendered_action_keys[] = $action_key;
			$panels[]               = $this->render_hidden_action_setting_values( $name . '[' . $action_key . ']', $preserved )
				. $this->render_action_settings_panel( $name, $action_key, $label, $schema, $settings );
		}

		ob_start();
		?>
		<div class="gv-bulk-action-settings" data-bulk-action-settings>
			<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Generated by render_hidden_action_setting_values(), which escapes names and values. ?>
			<?php echo $this->render_hidden_saved_action_settings( $name, $saved, $rendered_action_keys ); ?>
			<div class="gv-bulk-action-settings-header">
				<span class="gv-label"><?php echo esc_html__( 'Configure Individual Actions', 'gk-gravityview' ); ?></span>
				<span class="howto"><?php echo esc_html__( 'Selected actions with additional settings appear below.', 'gk-gravityview' ); ?></span>
			</div>

			<?php if ( [] === $rows ) : ?>
				<p class="description"><?php echo esc_html__( 'No registered bulk actions have settings.', 'gk-gravityview' ); ?></p>
			<?php else : ?>
				<div class="gv-bulk-action-settings-list" role="list">
					<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rows are rendered by render_action_settings_row(), which escapes action labels and attributes. ?>
					<?php echo implode( '', $rows ); ?>
				</div>
				<p class="description" data-bulk-action-settings-none-selected hidden>
					<?php echo esc_html__( 'Select an action above to configure its options.', 'gk-gravityview' ); ?>
				</p>
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Panels are rendered by render_action_settings_panel(), which escapes labels and delegates setting output to the admin renderer. ?>
				<?php echo implode( '', $panels ); ?>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Returns the View being edited in the admin, when available.
	 *
	 * @since 3.0.0
	 *
	 * @return View|null
	 */
	private function get_admin_view_context() {
		$view_id = absint( get_the_ID() );

		if ( ! $view_id && ! empty( $_POST['view_id'] ) ) {
			$view_id = absint( wp_unslash( $_POST['view_id'] ) );
		}

		if ( ! $view_id ) {
			return null;
		}

		$view = View::by_id( $view_id );

		return $view instanceof View ? $view : null;
	}

	/**
	 * Returns the action's View editor unavailable notice, when available.
	 *
	 * @since 3.1.0
	 *
	 * @param View|null $view   View context.
	 * @param array     $action Action config.
	 *
	 * @return string|null
	 */
	private function get_action_unavailable_notice( ?View $view, array $action ) {
		if ( ! $view || empty( $action['unavailable_notice_callback'] ) || ! is_callable( $action['unavailable_notice_callback'] ) ) {
			return null;
		}

		$notice = call_user_func( $action['unavailable_notice_callback'], $view );

		$notice = is_string( $notice ) ? trim( $notice ) : '';

		if ( '' === $notice ) {
			return null;
		}

		return $notice;
	}

	/**
	 * Renders one action settings list row.
	 *
	 * @since 3.0.0
	 *
	 * @param string      $action_key           Action key.
	 * @param string      $label                Action label.
	 * @param string|null $unavailable_notice   Unavailable notice.
	 *
	 * @return string
	 */
	private function render_action_settings_row( $action_key, $label, $unavailable_notice = null ) {
		$unavailable_notice = is_string( $unavailable_notice ) ? $unavailable_notice : '';

		ob_start();
		?>
		<div class="gv-bulk-action-settings-row" role="listitem" data-bulk-action-settings-row="<?php echo esc_attr( $action_key ); ?>" hidden>
			<?php if ( '' !== $unavailable_notice ) : ?>
				<p class="gv-bulk-action-unavailable-notice" data-bulk-action-unavailable="<?php echo esc_attr( $action_key ); ?>">
					<span class="dashicons dashicons-warning" aria-hidden="true"></span>
					<span><?php echo esc_html( $unavailable_notice ); ?></span>
				</p>
			<?php else : ?>
				<div class="gv-bulk-action-settings-row-label">
					<?php echo esc_html( $label ); ?>
				</div>
				<button type="button" class="button button-secondary gv-bulk-action-settings-open" data-bulk-action-settings-open="<?php echo esc_attr( $action_key ); ?>">
					<?php echo esc_html__( 'Configure', 'gk-gravityview' ); ?>
				</button>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Renders one action settings side panel.
	 *
	 * @since 3.0.0
	 *
	 * @param string $base_name  Base input name.
	 * @param string $action_key Action key.
	 * @param string $label      Action label.
	 * @param array  $schema     Settings schema.
	 * @param array  $settings   Current settings.
	 *
	 * @return string
	 */
	private function render_action_settings_panel( $base_name, $action_key, $label, array $schema, array $settings ) {
		/* translators: [label] is the bulk action label. */
		$title = strtr(
			__( '[label] Settings', 'gk-gravityview' ),
			[
				'[label]' => $label,
			]
		);

		ob_start();
		?>
		<div class="gv-bulk-action-settings-panel" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $title ); ?>" data-bulk-action-settings-panel="<?php echo esc_attr( $action_key ); ?>" aria-hidden="true" hidden>
			<div class="gv-bulk-action-settings-panel-details">
				<h3 class="search-field-title">
					<span><?php echo esc_html( $label ); ?></span>
				</h3>
			</div>
			<div class="gv-bulk-action-settings-panel-content">
				<?php foreach ( $schema as $setting_key => $setting ) : ?>
					<?php $setting['current_settings'] = $settings; ?>
					<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Setting rows are rendered by GravityView admin field renderers. ?>
					<?php echo $this->render_action_setting_row( $base_name, $action_key, $setting_key, $setting, $settings[ $setting_key ] ?? null ); ?>
				<?php endforeach; ?>
			</div>
			<button data-bulk-action-settings-close type="button" title="<?php echo esc_attr__( 'Close', 'gk-gravityview' ); ?>" class="ui-button ui-dialog-titlebar-close">
				<?php echo esc_html__( 'Close', 'gk-gravityview' ); ?>
			</button>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Renders one action setting row.
	 *
	 * @since 3.0.0
	 *
	 * @param string $base_name   Base input name.
	 * @param string $action_key  Action key.
	 * @param string $setting_key Setting key.
	 * @param array  $setting     Setting config.
	 * @param mixed  $value       Current value.
	 *
	 * @return string
	 */
	private function render_action_setting_row( $base_name, $action_key, $setting_key, array $setting, $value ) {
		$setting['id']      = 'gv_bulk_action_' . substr( md5( $base_name ), 0, 8 ) . '_' . sanitize_key( $action_key ) . '_' . sanitize_key( $setting_key );
		$setting['tooltip'] = '';
		$setting['current_settings'] = $setting['current_settings'] ?? [];
		$setting = $this->add_picker_data_attributes( $action_key, $setting_key, $setting );

		$name = $base_name . '[' . sanitize_key( $action_key ) . '][' . sanitize_key( $setting_key ) . ']';

		if ( $this->should_render_empty_options_setting( $setting ) ) {
			$output = $this->render_empty_options_setting( $name, $setting );
		} else {
			$output = \GravityView_Render_Settings::render_field_option( $name, $setting, $value );
		}

		if ( '' === $output ) {
			return '';
		}

		$classes = [
			'gv-setting-container',
			'gv-setting-container-' . sanitize_html_class( $setting_key ),
		];
		$attrs   = '';

		if ( 'hidden' === ( $setting['type'] ?? '' ) ) {
			$classes[] = 'screen-reader-text';
		}

		if ( ! empty( $setting['requires'] ) ) {
			$attrs .= ' data-requires="' . esc_attr( $setting['requires'] ) . '"';
		}

		if ( ! empty( $setting['requires_not'] ) ) {
			$attrs .= ' data-requires-not="' . esc_attr( $setting['requires_not'] ) . '"';
		}

		return '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"' . $attrs . '>' . $output . '</div>';
	}

	/**
	 * Adds picker data attributes for action settings controls.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key  Action key.
	 * @param string $setting_key Setting key.
	 * @param array  $setting     Setting config.
	 *
	 * @return array
	 */
	private function add_picker_data_attributes( $action_key, $setting_key, array $setting ) {
		$type = sanitize_key( (string) ( $setting['type'] ?? '' ) );

		if ( ! in_array( $type, [ 'select', 'multiselect' ], true ) ) {
			return $setting;
		}

		$setting['data']                            = isset( $setting['data'] ) && is_array( $setting['data'] ) ? $setting['data'] : [];
		$setting['data']['bulk-action-key']         = sanitize_key( $action_key );
		$setting['data']['bulk-action-setting-key'] = sanitize_key( $setting_key );

		if ( ! empty( $setting['picker_config'] ) && is_array( $setting['picker_config'] ) ) {
			$setting['data']['picker-config'] = wp_json_encode( $setting['picker_config'] );
		}

		if ( ! empty( $setting['depends_on'] ) && is_array( $setting['depends_on'] ) ) {
			$setting['data']['depends-on'] = wp_json_encode( array_values( $setting['depends_on'] ) );
		}

		return $setting;
	}

	/**
	 * Whether a select-like setting should render an empty options message.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param array $setting Setting config.
	 *
	 * @return bool
	 */
	private function should_render_empty_options_setting( array $setting ) {
		$type    = sanitize_key( (string) ( $setting['type'] ?? '' ) );
		$options = isset( $setting['options'] ) && is_array( $setting['options'] ) ? $setting['options'] : [];
		$message = trim( (string) ( $setting['empty_options_message'] ?? '' ) );

		return in_array( $type, [ 'select', 'multiselect' ], true ) && [] === $options && '' !== $message;
	}

	/**
	 * Renders a select-like setting when no options are available.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $name    Input name.
	 * @param array  $setting Setting config.
	 *
	 * @return string
	 */
	private function render_empty_options_setting( $name, array $setting ) {
		$type    = sanitize_key( (string) ( $setting['type'] ?? '' ) );
		$message = (string) ( $setting['empty_options_message'] ?? '' );

		$output = '<label for="' . esc_attr( $setting['id'] ) . '" class="gv-label-' . esc_attr( sanitize_html_class( $type ) ) . '">';
		$output .= '<span class="gv-label">' . esc_html( trim( (string) ( $setting['label'] ?? '' ) ) ) . '</span>';

		if ( ! empty( $setting['desc'] ) ) {
			$output .= '<span class="howto">' . wp_kses_post( (string) $setting['desc'] ) . '</span>';
		}

		$output .= '</label>';
		$output .= $this->render_empty_options_hidden_input( $name, $setting );
		$output .= '<p class="description gv-setting-empty">' . wp_kses(
			$message,
			[
				'a' => [
					'href'   => true,
					'target' => true,
					'rel'    => true,
				],
			]
		) . '</p>';

		return $output;
	}

	/**
	 * Renders the hidden empty-value input for an empty options setting.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param string $name    Input name.
	 * @param array  $setting Setting config.
	 *
	 * @return string
	 */
	private function render_empty_options_hidden_input( $name, array $setting ) {
		if ( empty( $setting['submit_empty_value'] ) ) {
			return '';
		}

		$type = sanitize_key( (string) ( $setting['type'] ?? '' ) );

		if ( 'multiselect' === $type ) {
			$name .= '[]';
		}

		return '<input type="hidden" name="' . esc_attr( $name ) . '" value="">';
	}

	/**
	 * Preserves saved settings for temporarily unavailable third-party actions.
	 *
	 * @since 3.0.0
	 *
	 * @param string   $base_name Base input name.
	 * @param array    $saved     Saved settings.
	 * @param string[] $available Registered action keys.
	 *
	 * @return string
	 */
	private function render_hidden_saved_action_settings( $base_name, array $saved, array $available ) {
		$available = array_map( 'sanitize_key', $available );
		$output    = '';

		foreach ( $saved as $action_key => $settings ) {
			$action_key = sanitize_key( $action_key );

			if ( in_array( $action_key, $available, true ) || ! is_array( $settings ) ) {
				continue;
			}

			$output .= $this->render_hidden_action_setting_values( $base_name . '[' . $action_key . ']', $settings );
		}

		return $output;
	}

	/**
	 * Recursively renders hidden inputs for saved settings.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name  Input name.
	 * @param array  $value Saved value.
	 *
	 * @return string
	 */
	private function render_hidden_action_setting_values( $name, array $value ) {
		$output = '';

		foreach ( $value as $key => $item ) {
			$input_name = $name . '[' . sanitize_key( $key ) . ']';

			if ( is_array( $item ) ) {
				$output .= $this->render_hidden_action_setting_values( $input_name, $item );
				continue;
			}

			if ( ! is_scalar( $item ) ) {
				continue;
			}

			$output .= sprintf(
				'<input type="hidden" name="%s" value="%s" />',
				esc_attr( $input_name ),
				esc_attr( $item )
			);
		}

		return $output;
	}

	/**
	 * Adds background processing settings when Foundation background processing is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array
	 */
	private function add_background_processing_settings( array $settings ) {
		if ( Config::is_background_processing_configurable() ) {
			return $settings;
		}

		$insert_after        = 'bulk_actions_show_selected';
		$background_settings = [
			'bulk_actions_background_processing_unavailable' => [
				'type' => 'html',
				'desc' => $this->get_background_processing_unavailable_description(),
			],
		];

		$position = array_search( $insert_after, array_keys( $settings ), true );

		if ( false === $position ) {
			return $settings + $background_settings;
		}

		++$position;

		return array_slice( $settings, 0, $position, true )
			+ $background_settings
			+ array_slice( $settings, $position, null, true );
	}

	/**
	 * Returns the unavailable background processing notice.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function get_background_processing_unavailable_description() {
		return esc_html__( 'Background Processing is unavailable. Enable it in GravityKit settings and resolve any scheduler diagnostics before using background bulk actions.', 'gk-gravityview' );
	}

	/**
	 * Adds widget metadata used by the View editor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $widgets Registered widgets.
	 *
	 * @return array
	 */
	public function register_widget( $widgets ) {
		$widgets = parent::register_widget( $widgets );

		$widgets[ Config::WIDGET_ID ]['allowed_once']     = true;
		$widgets[ Config::WIDGET_ID ]['show_in_template'] = Config::get_supported_template_ids();

		return $widgets;
	}

	/**
	 * Renders the Bulk Actions toolbar.
	 *
	 * @since 3.0.0
	 *
	 * @param array                       $widget_args Widget settings.
	 * @param string                      $content     Widget content.
	 * @param string|\GV\Template_Context $context     Template context.
	 *
	 * @return void
	 */
	public function render_frontend( $widget_args, $content = '', $context = '' ) {
		if ( ! $this->pre_render_frontend( $context ) ) {
			return;
		}

		$registry          = new Registry();
		$eligibility       = new ViewEligibility( $registry );
		$flash_messages    = new FlashMessages();
		$entry_resolver    = new EntryResolver();
		$selection_mode    = new SelectionMode( $entry_resolver, $eligibility, $flash_messages, false );
		$background_status = new BackgroundStatus();

		if ( ! $eligibility->is_table_context( $context ) || ! $eligibility->is_enabled_for_view( $context->view ) ) {
			return;
		}

		$renderer = new Renderer( $registry, $eligibility, $flash_messages, $selection_mode, $background_status );
		$renderer->render_toolbar( $context );
	}
}
