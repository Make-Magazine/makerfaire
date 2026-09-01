<?php
/**
 * A (search) field or widget in GravityView view configuration.
 *
 * PSR-4 migration of the legacy GravityView_Admin_View_Item class.
 *
 * @package GravityKit\GravityView\Admin
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin;

/**
 * A (search) field or widget in GravityView view configuration
 */
abstract class ViewItem {

	/**
	 * @var string Name of the item in the field or widget picker
	 */
	protected $title;

	/**
	 * @var string The field ID or the widget slug ( `2.3` or `custom_content`)
	 */
	protected $id;

	/**
	 * @var string Description of the item
	 */
	protected $subtitle;

	/**
	 * @var string The type of item ("field" or "widget")
	 */
	protected $label_type;

	/**
	 * @var array Associative array of item details
	 */
	protected $item;

	/**
	 * @var array Existing settings for the item
	 */
	protected $settings;

	/**
	 * @var string For ID, if available
	 */
	protected $form_id;

	/**
	 * @var array Form data, if available
	 */
	protected $form;

	function __construct( $title = '', $item_id = '', $item = [], $settings = [], $form_id = null, $form = [] ) {

		// Backward compat
		if ( ! empty( $item['type'] ) ) {
			$item['input_type'] = $item['type'];
			unset( $item['type'] );
		}

		if ( $admin_label = \GV\Utils::get( $settings, 'admin_label' ) ) {
			$title = $admin_label;
		}

		// Prevent items from not having index set
		$item = wp_parse_args(
			$item,
			[
				'label_text'    => $title,
				'field_id'      => null,
				'parent_label'  => null,
				'label_type'    => null,
				'input_type'    => null,
				'settings_html' => null,
				'adminLabel'    => null,
				'adminOnly'     => null,
				'subtitle'      => null,
				'placeholder'   => null,
				'icon'          => null,
			]
		);

		$this->title      = $title;
		$this->item       = $item;
		$this->id         = $item_id;
		$this->form_id    = $form_id;
		$this->form       = $form;
		$this->settings   = $settings;
		$this->label_type = $item['label_type'];
	}

	/**
	 * When echoing this class, print the HTML output
	 *
	 * @return string HTML output of the class
	 */
	public function __toString() {

		return $this->getOutput();
	}

	/**
	 * Overridden by child classes
	 *
	 * @return array Array of content with arrays for each item. Those arrays have `value`, `label` and (optional) `class` keys
	 */
	protected function additional_info() {
		return [];
	}

	/**
	 * Generate the output for a field based on the additional_info() output
	 *
	 * @see GravityView_Admin_View_Item::additional_info()
	 * @param  boolean $html Display HTML output? If yes, output is wrapped in spans. If no, plaintext.
	 * @return string|null        If empty, return null. Otherwise, return output HTML/text.
	 */
	protected function get_item_info( $html = true ) {

		$output           = null;
		$field_info_items = $this->additional_info();

		/**
		 * Tap in to modify the field information displayed next to an item.
		 *
		 * @since 1.17.3
		 *
		 * @param array                        $field_info_items Additional information to display in a field.
		 * @param GravityView_Admin_View_Field $field            Field shown in the admin.
		 */
		$field_info_items = apply_filters( 'gravityview_admin_label_item_info', $field_info_items, $this );

		if ( $html ) {

			foreach ( $field_info_items as $item ) {

				if ( \GV\Utils::get( $item, 'hide_in_picker', false ) ) {
					continue;
				}

				$class = isset( $item['class'] ) ? gravityview_sanitize_html_class( $item['class'] ) . ' description' : 'description';
				// Add the title in case the value's long, in which case, it'll be truncated by CSS.
				$output .= '<span class="' . $class . '">';
				$output .= esc_html( $item['value'] );
				$output .= '</span>';
			}
		} else {

			$values = wp_list_pluck( $field_info_items, 'value' );

			$output = esc_html( implode( "\n", $values ) );

		}

		return empty( $output ) ? null : $output;
	}

	/**
	 * Returns whether the field can be duplicated.
	 *
	 * @since 2.42
	 *
	 * @return bool Whether the field can be duplicated.
	 */
	protected function can_duplicate(): bool {
		/**
		 * Modify whether a field can be duplicated.
		 *
		 * @since 2.42
		 *
		 * @param bool                         $can_duplicate Whether the field can be duplicated.
		 * @param GravityView_Admin_View_Field $field         Field shown in the admin.
		 */
		return (bool) apply_filters( 'gk/gravityview/admin/can_duplicate_field', true, $this );
	}
	/**
	 * Generate HTML for field or a widget modal
	 *
	 * @return string
	 */
	function getOutput() {

		/* translators: %s: the field or widget label. */
		$settings_title    = sprintf( __( 'Configure %s Settings', 'gk-gravityview' ), esc_html( rgar( $this->item, 'label', ucfirst( $this->label_type ?: '' ) ) ) );
		/* translators: %s: the field or widget label. */
		$delete_title      = sprintf( __( 'Remove %s', 'gk-gravityview' ), ucfirst( $this->label_type ?: '' ) );

		// $settings_html will just be hidden inputs if empty. Otherwise, it'll have an <ul>. Ugly hack, I know.
		// TODO: Un-hack this
		$hide_settings_link_class = ( empty( $this->item['settings_html'] ) || strpos( $this->item['settings_html'], \GravityView_Render_Settings::NO_OPTIONS ) > 0 ) ? 'hide-if-js' : '';
		$settings_link            = sprintf( '<button class="gv-field-settings %2$s" title="%1$s" aria-label="%1$s"><span class="dashicons-admin-generic dashicons"></span></button>', esc_attr( $settings_title ), $hide_settings_link_class );

		// When a field label is empty, use the Field ID
		/* translators: %s: the field ID number. */
		$label = empty( $this->title ) ? sprintf( _x( 'Field #%s (No Label)', 'Label in field picker for empty label', 'gk-gravityview' ), $this->id ) : $this->title;

		// Admin label always takes precedence in the View editor.
		if ( empty( $this->settings['admin_label'] ) ) {
			if ( ! empty( $this->settings['custom_label'] ) && ! empty( $this->settings['show_label'] ) ) {
				$label = $this->settings['custom_label'];
			} elseif ( ! empty( $this->item['customLabel'] ) ) {
				$label = $this->item['customLabel'];
			}
		}

		$label = (string) esc_attr( $label );

		$field_icon = '';

		$form = ! empty( $this->form_id ) ? \GVCommon::get_form( $this->form_id ) : false;

		$nonexistent_form_field = $form && $this->id && preg_match( '/^\d+\.\d+$|^\d+$/', $this->id ) && ! gravityview_get_field( $form, $this->id );

		if ( $this->item['icon'] ) {
			$has_gf_icon  = ( false !== strpos( $this->item['icon'], 'gform-icon' ) );
			$has_dashicon = ( false !== strpos( $this->item['icon'], 'dashicons' ) );

			if ( 0 === strpos( $this->item['icon'], 'data:' ) ) {
				// Inline icon SVG
				$field_icon = '<i class="dashicons background-icon" style="background-image: url(\'' . esc_attr( $this->item['icon'] ) . '\');"></i>';
			} elseif ( $has_gf_icon && gravityview()->plugin->is_GF_25() ) {
				// Gravity Forms icon font
				$field_icon = '<i class="gform-icon ' . esc_attr( $this->item['icon'] ) . '"></i>';
			} elseif ( $has_dashicon ) {
				// Dashicon; prefix with "dashicons"
				$field_icon = '<i class="dashicons ' . esc_attr( $this->item['icon'] ) . '"></i>';
			} else {
				// Not dashicon icon
				$field_icon = '<i class="' . esc_attr( $this->item['icon'] ) . '"></i>';
			}

			$field_icon .= ' ';
		}

		if ( $this->is_child() && ! $this->is_parent() ) {
			$field_icon = '<i class="gv-icon gv-icon-level-down"></i> ';
		}

		/* translators: %s: the field label. */
		$output = '<button class="gv-add-field screen-reader-text">' . sprintf( esc_html__( 'Add "%s"', 'gk-gravityview' ), $label ) . '</button>';
		// This needs to be an `<a`-tag to please Firefox.
		$output .= sprintf(
			'<a tabindex="0" href="javascript:void(0);" role="button" class="gv-add-field-before" title="%s"><span class="dashicons dashicons-plus-alt"></span></a>',
		esc_html( $this->settings['add_button_label'] ?? __( 'Add Field', 'gk-gravityview' ) )
		);

		$title = esc_attr( $this->get_title( $label ) );

		if ( ! $nonexistent_form_field ) {
			$title .= "\n" . $this->get_item_info( false );
		} else {
			$output        = '';
			$settings_link = '';
			/* translators: %s: the field label. */
			$label         = '<span class="dashicons-warning dashicons"></span> ' . esc_html( sprintf( __( 'The field connected to "%s" was deleted from the form. The associated entry data no longer exists.', 'gk-gravityview' ), $label ) );
		}

		$output .= '<h5 class="selectable gfield field-id-' . esc_attr( $this->id ) . '">';

		$output .= '<span class="gv-field-controls">' . $settings_link . $this->get_indicator_icons() . '</span>';

		$output .= '<span class="gv-field-label" data-original-title="' . esc_attr( $label ) . '" title="' . $title . '">' . $field_icon . '<span class="gv-field-label-text-container">' . $label . '</span></span>';

		$move_field_up    = esc_attr__( 'Move field up', 'gk-gravityview' );
		$move_field_down  = esc_attr__( 'Move field down', 'gk-gravityview' );
		$move_field_left  = esc_attr__( 'Move field to the previous column', 'gk-gravityview' );
		$move_field_right = esc_attr__( 'Move field to the next column', 'gk-gravityview' );

		$output .= '<span class="gv-field-actions" role="toolbar" aria-orientation="horizontal" aria-label="' . esc_attr__( 'Field actions', 'gk-gravityview' ) . '">';
		$output .= sprintf(
			'<button type="button" class="gv-field-action gv-field-move-left" aria-label="%1$s" title="%1$s" hidden aria-hidden="true"><span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span></button>',
			$move_field_left
		);
		$output .= sprintf(
			'<button type="button" class="gv-field-action gv-field-move-up" aria-label="%1$s" title="%1$s" hidden aria-hidden="true"><span class="dashicons dashicons-arrow-up-alt2" aria-hidden="true"></span></button>',
			$move_field_up
		);
		$output .= sprintf(
			'<button type="button" class="gv-field-action gv-field-move-down" aria-label="%1$s" title="%1$s" hidden aria-hidden="true"><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>',
			$move_field_down
		);
		$output .= sprintf(
			'<button type="button" class="gv-field-action gv-field-move-right" aria-label="%1$s" title="%1$s" hidden aria-hidden="true"><span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span></button>',
			$move_field_right
		);

		if ( $this->can_duplicate() ) {
			$output .= sprintf(
				'<button class="gv-field-action gv-field-duplicate" type="button" title="%1$s" aria-label="%1$s"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span></button>',
				esc_attr__( 'Duplicate this field', 'gk-gravityview' )
			);
		}

		$output .= '</span>';

		$output .= '<span class="gv-field-controls"><button class="gv-remove-field" aria-label="' . esc_attr( $delete_title ) . '" title="' . esc_attr( $delete_title ) . '"><span class="dashicons-dismiss dashicons"></span></button></span>';

		// Displays only in the field/widget picker
		if ( ! $nonexistent_form_field && $field_info = $this->get_item_info() ) {
			$output .= '<span class="gv-field-info">' . $field_info . '</span>';
		}

		$output .= '</h5>';

		$container_class = ! empty( $this->item['parent'] ) ? ' gv-child-field' : '';

		$container_class .= $nonexistent_form_field ? ' gv-nonexistent-form-field' : '';

		$container_class .= empty( $this->settings['show_as_link'] ) ? '' : ' has-single-entry-link';

		$container_class .= empty( $this->settings['only_loggedin'] ) ? '' : ' has-custom-visibility';

		$data_form_id = $form ? ' data-formid="' . esc_attr( $this->form_id ) . '"' : '';

		$data_allowed_once = ! empty( $this->item['allowed_once'] ) ? ' data-allowed-once="true"' : '';

		$show_in_template = $this->item['show_in_template'] ?? [];
		$show_in_template = is_array( $show_in_template ) ? $show_in_template : [ $show_in_template ];
		$show_in_template = array_filter( array_map( 'sanitize_key', $show_in_template ) );
		$data_template    = $show_in_template ? ' data-show-in-template="' . esc_attr( implode( ' ', $show_in_template ) ) . '"' : '';

		$parent_label_attr = esc_attr( $this->item['parent']['label'] ?? '' );
		$data_parent_label = ! empty( $this->item['parent'] ) ? ' data-parent-label="' . $parent_label_attr . '"' : '';

		$style = '';
		if ( $this->is_child() ) {
			// Use JSON encoding to safely escape quotes and special characters for CSS string value.
			$parent_label_css = esc_attr( wp_json_encode( $this->item['parent']['label'] ?? '' ) );

			$style = sprintf(
				' style="--field-level: %s; --parent-label: %s;"',
				$this->get_nesting_level(),
				$parent_label_css,
			);
		}

		$output = '<div data-fieldid="' . esc_attr( $this->id ) . '" ' . $data_form_id . $data_parent_label . $data_allowed_once . $data_template . ' data-inputtype="' . esc_attr( $this->item['input_type'] ) . '" class="gv-fields' . $container_class . '"' . $style . '>' . $output . $this->item['settings_html'] . '</div>';

		return $output;
	}

	/**
	 * Returns array of item icons used to represent field settings state
	 *
	 * Has `gravityview/admin/indicator_icons` filter for other components to modify displayed icons.
	 *
	 * @since 2.9.5
	 *
	 * @return string HTML output of icons
	 */
	private function get_indicator_icons() {

		$icons = [
			'show_as_link'  => [
				'visible'   => ( ! empty( $this->settings['show_as_link'] ) ),
				'title'     => __( 'This field links to the Single Entry', 'gk-gravityview' ),
				'css_class' => 'dashicons dashicons-media-default icon-link-to-single-entry',
			],
			'only_loggedin' => [
				'visible'   => ( \GV\Utils::get( $this->settings, 'only_loggedin' ) || isset( $this->settings['allow_edit_cap'] ) && 'read' !== $this->settings['allow_edit_cap'] ),
				'title'     => __( 'This field has modified visibility', 'gk-gravityview' ),
				'css_class' => 'dashicons dashicons-lock icon-custom-visibility',
			],
			'hidden' => [
				'visible' => 'hidden' === \GV\Utils::get( $this->settings, 'input_type' ),
				'title'   => __( 'This field is hidden', 'gk-gravityview' ),
				'css_class' => 'dashicons dashicons-hidden icon-hidden',
			],
		];

		$output = '';

		/**
		 * Modify the icon output to add additional indicator icons.
		 *
		 * @internal This is currently internally used. Consider not relying on it until further notice :-)
		 *
		 * @since 2.10
		 *
		 * @param array $icons    Array of icons to be shown, with `visible`, `title`, `css_class` keys.
		 * @param array $settings Settings for the current item (widget or field).
		 */
		$icons = (array) apply_filters( 'gravityview/admin/indicator_icons', $icons, $this->settings );

		foreach ( $icons as $icon ) {

			if ( empty( $icon['css_class'] ) || empty( $icon['title'] ) ) {
				continue;
			}

			$css_class = trim( $icon['css_class'] );

			if ( empty( $icon['visible'] ) ) {
				$css_class .= ' hide-if-js';
			}

			$output .= '<span class="' . gravityview_sanitize_html_class( $css_class ) . '" title="' . esc_attr( $icon['title'] ) . '"></span>';
		}

		return $output;
	}

	/**
	 * Returns the label.
	 *
	 * @since 2.42
	 *
	 * @param string $label The label.
	 *
	 * @return string The title.
	 */
	protected function get_title( string $label ): string {
		return $label;
	}

	/**
	 * Returns whether this field is a parent field.
	 *
	 * @since 2.51.0
	 *
	 * @return bool Whether this field is a parent field.
	 */
	protected function is_parent(): bool {
		return false;
	}

	/**
	 * Returns whether this field has a parent.
	 *
	 * @since 2.51.0
	 *
	 * @return bool Whether this field is a child field.
	 */
	protected function is_child(): bool {
		return (bool) ( $this->item['parent'] ?? null );
	}

	/**
	 * Returns the nesting level for this field.
	 * @since 2.51.0
	 * @return int The nesting level.
	 */
	protected function get_nesting_level(): int {
		return $this->is_child() ? 1 : 0;
	}
}
