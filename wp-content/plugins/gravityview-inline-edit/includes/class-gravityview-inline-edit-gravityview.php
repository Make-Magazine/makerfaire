<?php

/**
 * Add GravityView inline edit settings
 *
 */

/**
 * @since 1.0
 */
final class GravityView_Inline_Edit_GravityView extends GravityView_Inline_Edit_Render {

	/**
	 * Return whether this class should add hooks when initialized
	 *
	 * @since 1.0
	 *
	 * @return bool Whether to add hooks for this class
	 */
	protected function should_add_hooks() {

		$is_gv_admin = class_exists( 'GravityView_Admin' ) && GravityView_Admin::is_admin_page() || ( function_exists( 'gravityview' ) && gravityview()->request->is_admin() );

		$is_valid_nonce = isset( $_POST['nonce'] ) && ( wp_verify_nonce( $_POST['nonce'], 'gravityview_inline_edit' ) || wp_verify_nonce( $_POST['nonce'], 'gravityview_datatables_data' ) );

		$is_inline_edit_request = defined('DOING_AJAX') && DOING_AJAX && $is_valid_nonce;

		return defined( 'GRAVITYVIEW_FILE' ) && ( ! is_admin() || $is_gv_admin || $is_inline_edit_request );
	}

	/**
	 * Add hooks for inline edit on GravityView frontend
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	protected function add_hooks() {

		parent::add_hooks();

		add_filter( 'gravityview_default_args', array( $this, 'add_inline_edit_toggle_setting' ) );

		add_action( 'gravityview_admin_directory_settings', array( $this, 'render_inline_edit_setting' ) );

		add_filter( 'gravityview_settings_fields', array( $this, 'add_inline_edit_mode_setting' ) );

		add_filter( 'gravityview/render/container/class', array( $this, 'add_container_class' ) );

		add_filter( "gravityview-inline-edit/checkbox-wrapper-attributes", array( $this, 'modify_attributes_add_choice_display' ), 10, 6 );
		add_filter( "gravityview-inline-edit/radio-wrapper-attributes", array( $this, 'modify_attributes_add_choice_display' ), 10, 6 );

		add_action( 'gravityview_header', array( $this, 'maybe_add_inline_edit_toggle_button' ) );

		add_filter( 'gravityview_field_entry_value', array( $this, 'wrap_gravityview_field_value' ), 10, 4 );

		add_action( 'gravityview_before', array( $this, 'maybe_enqueue_inline_edit_styles' ), 1 );
		add_action( 'gravityview_after', array( $this, 'maybe_enqueue_inline_edit_scripts' ) );

		add_filter( 'gravityview-inline-edit/user-can-edit-entry', array( $this, 'filter_can_edit_entry' ), 1, 4 );

		add_filter( 'gravityview-inline-edit/entry-updated', array( $this, 'add_to_blacklist' ), 10, 2 );

		add_filter( 'gravityview/datatables/output', array( $this, 'modify_datatables_output' ), 10, 2 );
	}

	/**
	 * Clear the GravityView cache when an entry is updated via Inline Edit (if the update is valid)
	 *
	 * @since 1.0.2
	 *
	 * @param bool|WP_Error $update_result True: the entry has been updated by Gravity Forms or WP_Error if there was a problem
	 * @param array $entry The Entry Object that's been updated
	 * @param int $form_id The Form ID
	 * @param GF_Field|null $gf_field The field that's been updated, or null if no field exists (for entry meta)
	 *
	 * @return bool|WP_Error Original $update_result
	 */
	function add_to_blacklist( $update_result, $entry ) {

		if ( $update_result && ! is_wp_error( $update_result ) ) {
			do_action( 'gravityview_clear_entry_cache', $entry['id'] );
		}

		return $update_result;
	}

	/**
	 * Pass the choice_display attribute so that labels/values/checkboxes are processed properly
	 *
	 * @since 1.0
	 *
	 * @param array $wrapper_attributes
	 * @param string $field_input_type
	 * @param string|int $field_id
	 * @param array $entry
	 * @param array $current_form
	 * @param GF_Field_Checkbox $gf_field
	 *
	 * @return array
	 */
	public function modify_attributes_add_choice_display( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		if( ! class_exists( 'GravityView_View' ) ) {
			return $wrapper_attributes;
		}

		// "value", "label" or "tick" (default)
		$wrapper_attributes['data-choice_display'] = GravityView_View::getInstance()->getCurrentFieldSetting( 'choice_display' );

		return $wrapper_attributes;
	}

	/**
	 * Modify whether the current user can edit an entry. Checks against GravityView custom hooks.
	 *
	 * @since 1.0
	 *
	 * @param bool $can_edit True: User can edit the entry at $entry_id; False; they just can't
	 * @param int $entry_id Entry ID to check
	 * @param int $form_id Form connected to $entry_id
	 * @param int|$view_id View ID, if set
	 *
	 * @return bool True: User can edit this entry. False: Nope.
	 */
	public function filter_can_edit_entry( $can_edit, $entry_id = 0, $form_id = 0, $view_id = null ) {

		// Edit all entries from a form
		if ( $form_id && GVCommon::has_cap( 'gravityview_edit_form_entries', $form_id ) ) {
			return true;
		}

		// Edit specific entry
		if ( $entry_id && GVCommon::has_cap( 'gravityview_edit_entry', $entry_id ) ) {
			return true;
		}

		if ( $entry_id && class_exists( 'GravityView_Edit_Entry' ) ) {

			$entry = GFAPI::get_entry( $entry_id );

			if( ! is_wp_error( $entry ) ) {

				// GravityView_View not loaded in AJAX, but used by GravityView_Edit_Entry
				if ( ! class_exists( 'GravityView_View' ) && defined('GRAVITYVIEW_DIR') ) {
					include_once( GRAVITYVIEW_DIR .'includes/class-template.php' );
				}

				if( class_exists( 'GravityView_View' ) ) {
					return GravityView_Edit_Entry::check_user_cap_edit_entry( $entry, $view_id );
				}
			}
		}

		return $can_edit;
	}

	/**
	 * Can the current user edit any entries currently being shown in GravityView?
	 *
	 * @since 1.2
	 *
	 * @return bool|null Null means that gravityview() isn't loaded; upgrade GV.
	 */
	public function can_edit_any_entries( $view_id = 0 ) {

		if( ! function_exists( 'gravityview' ) || ! class_exists( '\GV\View' ) || empty( $view_id ) ) {
			return null;
		}

		/** @var GV\View $view */
		$view = \GV\View::by_id( $view_id );

		$view_entries = GravityView_frontend::get_view_entries( $view->settings->as_atts(), $view->form->ID );

		foreach ( $view_entries['entries'] as $entry ) {
			if( $this->filter_can_edit_entry( false, $entry['id'], $view->form->ID ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the inline edit mode
	 *
	 * @since 1.0
	 *
	 * @param string $mode Existing mode. Default: `popup`
	 *
	 * @return string The mode to use. Can be `popup` or `inline`
	 */
	function filter_inline_edit_mode( $mode = '' ) {

		if ( ! class_exists( 'GravityView_Settings' ) ) {
			return $mode;
		}

		$inline_edit_mode = GravityView_Settings::getSetting( 'inline-edit-mode' );

		return ( empty( $inline_edit_mode ) ? $mode : $inline_edit_mode );
	}

	/**
	 * If inline edit is enabled, enqueue styles
	 *
	 * @since 1.0
	 *
	 * @param int $view_id ID of the current View
	 *
	 * @return void
	 */
	function maybe_enqueue_inline_edit_styles( $view_id ) {

		if ( ! $this->is_inline_edit_enabled( $view_id ) ) {
			return;
		}

		if ( ! $this->can_edit_any_entries( $view_id ) ) {
			return;
		}

		do_action( 'gravityview-inline-edit/enqueue-styles', compact( 'view_id' ) );
	}

	/**
	 * If inline edit is enabled, enqueue scripts
	 *
	 * @since 1.0
	 *
	 * @param int $view_id ID of the current View
	 *
	 * @return void
	 */
	public function maybe_enqueue_inline_edit_scripts( $view_id ) {

		if ( ! $this->is_inline_edit_enabled( $view_id ) ) {
			return;
		}

		if ( ! $this->can_edit_any_entries( $view_id ) ) {
			return;
		}

		do_action( 'gravityview-inline-edit/enqueue-scripts', compact( 'view_id' ) );
	}

	/**
	 * Convert GravityView field value into an X-editable formatted link
	 *
	 * @since 1.0
	 *
	 * @param  string $output The field output HTML
	 * @param  array $entry The GF entry array
	 * @param  array $field_settings Settings for the particular GV field
	 * @param  array $field Field array, as fetched from GravityView_View::getCurrentField()
	 *
	 * @return string HTML for the field value wrapped in an X-editable-format
	 *
	 */
	public function wrap_gravityview_field_value( $output, $entry, $field_settings, $field ) {

		$view_id = GravityView_View::getInstance()->getViewId();

		if ( ! $this->is_inline_edit_enabled( $view_id ) ) {
			return $output;
		}

		// Don't expose additional information about the entry
		if ( ! GravityView_Inline_Edit::get_instance()->can_edit_entry( $entry['id'], $entry['form_id'] ) ) {
			return $output;
		}

		$current_field = rgar( $field, 'field' );
		$form          = rgar( $field, 'form' );

		$gf_field = is_a( $current_field, 'GF_Field' ) ? GVCommon::get_field( $form, $current_field->id ) : null;

		return parent::wrap_field_value( $output, $entry, $field_settings['id'], $gf_field, $form );
	}

	/**
	 * Check whether Inline Edit is enabled for this View
	 *
	 * @since 1.0
	 *
	 * @param int $view_id ID of the View currently being displayed
	 *
	 * @return bool True: yes, it is. False, why no! It is not.
	 */
	protected function is_inline_edit_enabled( $view_id ) {

		$view_settings = gravityview_get_template_settings( $view_id );

		return ( isset( $view_settings['inline_edit'] ) && ! empty( $view_settings['inline_edit'] ) );
	}

	/**
	 * Add a setting to toggle Inline editable
	 *
	 * @since 1.0
	 *
	 * @param array $gv_settings GravityView settings
	 *
	 * @return array Settings with new "Enable Inline Edit" setting
	 */
	public function add_inline_edit_toggle_setting( $gv_settings ) {

		$gv_settings['inline_edit'] = array(
			'label'             => esc_html__( 'Enable Inline Edit', 'gravityview-inline-edit' ),
			'desc'              => esc_html__( 'Adds a link to toggle Inline Editing capabilities.', 'gravityview-inline-edit' ),
			'type'              => 'checkbox',
			'group'             => 'default',
			'value'             => 0,
			'tooltip'           => false,
			'show_in_shortcode' => false,
		);

		return $gv_settings;
	}

	/**
	 * Print the "Enable Inline Edit" setting in GV
	 *
	 * @since 1.0
	 *
	 * @param array $current_settings
	 *
	 * @return void
	 */
	public function render_inline_edit_setting( $current_settings ) {
		GravityView_Render_Settings::render_setting_row( 'inline_edit', $current_settings );
	}

	/**
	 * Render the "Toggle Inline Edit" button if enabled and user can edit entries
	 *
	 * @since 1.0
	 *
	 * @param int $view_id
	 *
	 * @return void
	 */
	public function maybe_add_inline_edit_toggle_button( $view_id = 0 ) {

		if ( ! $this->is_inline_edit_enabled( $view_id ) ) {
			return;
		}

		if ( ! $this->can_edit_any_entries( $view_id ) ) {
			return;
		}

		$this->add_inline_edit_toggle_button();

		if ( $view_id ) {
			echo '<input type="hidden" class="gravityview-inline-edit-id" value="view-' . esc_attr( $view_id ) . '" />';
		}
	}

	/**
	 * Add CSS class used to indicate the View contents are editable via X-editable
	 *
	 * @since 1.0
	 *
	 * @param string $css_class Existing CSS classes for the GravityView container
	 *
	 * @return string CSS class with X-editable CSS class added
	 */
	public function add_container_class( $css_class ) {

		$view_id = GravityView_View::getInstance()->getViewId();

		if( empty( $view_id ) || ! $this->is_inline_edit_enabled( $view_id ) ) {
			return $css_class;
		}

		return $css_class . ' gv-inline-editable-view';
	}

	/**
	 * Returns HTML tooltip for the     edit mode setting
	 *
	 * @since 1.0
	 *
	 * @return string HTML for the tooltip about the edit modes
	 */
	private function _get_edit_mode_tooltip_html() {

		$tooltips = array(
			array(
				'image' => 'popup',
				'description' => esc_html__('Popup: The edit form will appear above the content.', 'gravityview-inline-edit'),
			),
			array(
				'image' => 'in-place',
				'description' => esc_html__('In-Place: The edit form for the field will show in the same place as the content.', 'gravityview-inline-edit'),
			),
		);

		$tooltip_format = '<p><img src="%s" height="150" style="display: block; margin-bottom: .5em;" /><strong>%s</strong></p>';

		$tooltip_html = '';

		foreach ( $tooltips as $tooltip ) {

			$image_link = plugins_url( "assets/images/{$tooltip['image']}.png", GRAVITYVIEW_INLINE_FILE );

			$tooltip_html .= sprintf( $tooltip_format, $image_link, $tooltip['description'] );
		}

		return $tooltip_html;
	}

	/**
	 * Add a settings to GV global settings to declare the inline edit style and mode
	 *
	 * @since 1.0
	 *
	 * @param array $gv_settings GravityView settings
	 *
	 * @return array Array with settings
	 */
	public function add_inline_edit_mode_setting( $gv_settings ) {

		$gv_settings[] = array(
			'name'          => 'inline-edit-mode',
			'type'          => 'select',
			'label'         => __( 'Inline Edit Mode', 'gravityview-inline-edit' ),
			'tooltip'       => $this->_get_edit_mode_tooltip_html(),
			'description'   => esc_html__( 'Change where the Inline Edit form appears &ndash; above the content or in its place.', 'gravityview-inline-edit' ) . ' ' . esc_html__( 'Hover over the ? above for examples of each mode.', 'gravityview-inline-edit' ),
			'default_value' => 'popup',
			'horizontal'    => 1,
			'choices'       => array(
				array
				(
					'label' => esc_html__( 'Popup', 'gravityview-inline-edit' ),
					'value' => 'popup',
				),
				array
				(
					'label' => esc_html__( 'In-Place', 'gravityview-inline-edit' ),
					'value' => 'inline',
				),

			),
		);

		return $gv_settings;
	}

	/**
	 * Add field template data to DataTables output
	 *
	 * @since 1.3
	 *
	 * @param array $output DataTables output being sent to the AJAX request
	 *
	 * @return array Array with DataTables output data
	 */
	public function modify_datatables_output( $output = array() ) {

		$output['inlineEditTemplatesData'] = GravityView_Inline_Edit_Field::get_field_templates();

		return $output;
	}
}

GravityView_Inline_Edit_GravityView::get_instance();
