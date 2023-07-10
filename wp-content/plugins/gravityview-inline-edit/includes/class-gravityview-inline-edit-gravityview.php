<?php

/**
 * Add GravityView inline edit settings
 *
 */

/**
 * @since 1.0
 */
final class GravityView_Inline_Edit_GravityView extends GravityView_Inline_Edit_Render {
	/*
	 * Cached collection of forms used throughout the request
	 *
	 * @since 2.0
	 *
	 * @var array $forms
	 */
	protected $forms = [];

	/**
	 * Return whether this class should add hooks when initialized
	 *
	 * @since 1.0
	 *
	 * @return bool Whether to add hooks for this class
	 */
	protected function should_add_hooks() {

		$is_valid_nonce = isset( $_POST['nonce'] ) && ( wp_verify_nonce( $_POST['nonce'], 'gravityview_inline_edit' ) || wp_verify_nonce( $_POST['nonce'], 'gravityview_datatables_data' ) );

		$is_inline_edit_request = defined('DOING_AJAX') && DOING_AJAX && $is_valid_nonce;

		return defined( 'GRAVITYVIEW_FILE' ) && ( ! is_admin() || $this->is_gv_admin() || $is_inline_edit_request );
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

		add_filter( 'gravityview-inline-edit/checkbox-wrapper-attributes', array( $this, 'modify_attributes_add_choice_display' ), 10, 9 );
		add_filter( 'gravityview-inline-edit/radio-wrapper-attributes', array( $this, 'modify_attributes_add_choice_display' ), 10, 9 );

		add_filter( 'gravityview/render/container/class', array( $this, 'add_container_class' ), 10, 2 );
		add_action( 'gravityview/template/header', array( $this, 'maybe_add_inline_edit_toggle_button' ) );
		add_filter( 'gravityview/template/field/output', array( $this, 'wrap_gravityview_field_value' ), 10, 2 );
		add_action( 'gravityview/template/before', array( $this, 'maybe_enqueue_inline_edit_styles' ), 1 );
		add_action( 'gravityview/template/after', array( $this, 'maybe_enqueue_inline_edit_scripts' ) );

		add_filter( 'gravityview-inline-edit/user-can-edit-entry', array( $this, 'filter_can_edit_entry' ), 1, 4 );

		add_filter( 'gravityview-inline-edit/entry-updated', array( $this, 'add_to_blocklist' ), 10, 2 );

		add_filter( 'gravityview/datatables/output', array( $this, 'modify_datatables_output' ), 10, 2 );

		add_filter( 'gravityview_template_field_options', array( $this, 'add_inline_edit_option' ), 10, 6 );

		add_filter( 'gravityview/admin/indicator_icons', array( $this, 'add_inline_edit_icon' ), 10, 2 );

	}

	/**
	 * Add inline edit icon in the field setting.
	 *
	 * @param array $icons
	 * @param array $settings
	 *
	 * @return array
	 */
	public function add_inline_edit_icon( $icons, $settings ) {

		$icons['enable_inline_edit'] = array(
			'visible'   => isset( $settings['enable_inline_edit'] ) && 'enabled' === rgar( $settings, 'enable_inline_edit', 'enabled' ),
			'title'     => __( 'This field is allowed to be inline edited', 'gk-gravityedit' ),
			'css_class' => 'dashicons dashicons-edit icon-allow-inline-edit',
		);

		return $icons;
	}

	/**
	 * Add inline edit checkbox in the field setting
	 *
	 * @since 1.7 Moved to this class from GravityView_Inline_Edit_GFAddon.
	 *
	 * @param array $field_options
	 * @param integer $template_id
	 * @param integer $field_id
	 * @param string $context
	 * @param string $input_type
	 * @param integer $form_id
	 *
	 * @return array
	 */
	public function add_inline_edit_option( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {
		// Show option only to supported fields.
		$supported_fields = GravityView_Inline_Edit::get_instance()->get_supported_fields();
		if ( ! in_array( $input_type, $supported_fields ) ) {
			return $field_options;
		}

		$field_options['enable_inline_edit'] = array(
			'type'     => 'radio',
			'label'    => __( 'Enable editing this field with Inline Edit', 'gk-gravityedit' ),
			'value'    => 'enabled',
			'choices'  => array(
				'enabled'  => __( 'Enabled', 'gk-gravityedit' ),
				'disabled' => __( 'Disabled', 'gk-gravityedit' ),
			),
			'class'    => 'checkbox',
			'priority' => 5100,
			'group'    => 'advanced',
		);

		return $field_options;
	}

	/**
	 * @since 1.0.2
	 * @depecated 1.5
	 */
	public function add_to_blacklist( $update_result, $entry ) {
		_deprecated_function( __METHOD__, '1.5', 'GravityView_Inline_Edit_GravityView::add_to_blocklist' );
		return $this->add_to_blocklist( $update_result, $entry );
	}

	/**
	 * Clear the GravityView cache when an entry is updated via Inline Edit (if the update is valid)
	 *
	 * @since 1.5
	 *
	 * @param bool|WP_Error $update_result True: the entry has been updated by Gravity Forms or WP_Error if there was a problem
	 * @param array $entry The Entry Object that's been updated
	 *
	 * @return bool|WP_Error Original $update_result
	 */
	function add_to_blocklist( $update_result, $entry ) {

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
	 * @param string $output The original field value HTML.
	 * @param array $field_settings GravityView field settings array.
	 * @param null|\GV\Template_Context $context The GravityView Template Context, if available.
	 *
	 * @return array
	 */
	public function modify_attributes_add_choice_display( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field, $output, $field_settings, $context = null ) {

		if( ! $context instanceof \GV\Template_Context ) {
			return $wrapper_attributes;
		}

		$field = $context->field->as_configuration();

		// "value", "label" or "tick" (default)
		$wrapper_attributes['data-choice_display'] = rgar( $field, 'choice_display', 'tick' );

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

		// Prevent from running multiple times for one request.
		static $_can_edit_cache;

		$_can_edit_cache = isset( $_can_edit_cache ) ? $_can_edit_cache : [];

		// Already processed!
		if ( isset( $_can_edit_cache[ $view_id ] ) ) {
			return $_can_edit_cache[ $view_id ];
		}

		/** @var GV\View $view */
		$view = \GV\View::by_id( $view_id );

		$view_entries = GravityView_frontend::get_view_entries( $view->settings->as_atts(), $view->form->ID );

		$_can_edit_cache[ $view_id ] = false;

		foreach ( $view_entries['entries'] as $entry ) {
			if( $this->filter_can_edit_entry( false, $entry['id'], $view->form->ID ) ) {
				$_can_edit_cache[ $view_id ] = true;
				break;
			}
		}

		return $_can_edit_cache[ $view_id ];
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

		if ( ! class_exists( 'GravityKitFoundation' ) ) {
			return $mode;
		}

		$inline_edit_mode = GravityKitFoundation::settings()->get_plugin_setting( 'gravityedit', 'inline-edit-mode' );

		return ( empty( $inline_edit_mode ) ? $mode : $inline_edit_mode );
	}

	/**
	 * If inline edit is enabled, enqueue styles
	 *
	 * @since 1.0
	 * @since 2.0 Removed $view_id parameter and replaced with $context.
	 *
	 * @param \GV\Template_Context $context The current context.
	 *
	 * @return void
	 */
	function maybe_enqueue_inline_edit_styles( $context ) {

		if ( ! $this->is_inline_edit_enabled( $context ) ) {
			return;
		}

		$view_id = $context->view->ID;

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
	 * @param \GV\Template_Context $context The current context.
	 *
	 * @return void
	 */
	public function maybe_enqueue_inline_edit_scripts( $context ) {

		if ( ! $this->is_inline_edit_enabled( $context ) ) {
			return;
		}

		$view_id = $context->view->ID;

		if ( ! $this->can_edit_any_entries( $view_id ) ) {
			return;
		}

		do_action( 'gravityview-inline-edit/enqueue-scripts', compact( 'view_id' ) );
	}

	/**
	 * Convert GravityView field value into an X-editable formatted link
	 *
	 * @since 1.0
	 * @since 2.0 Removed $entry, $field_settings, and $field parameters and used $context instead.
	 *
	 * @param  string $output The field output HTML
	 * @param  \GV\Template_Context $context The context of the field.
	 *
	 * @return string HTML for the field value wrapped in an X-editable-format
	 *
	 */
	public function wrap_gravityview_field_value( $output, $context ) {

		if ( ! $this->is_inline_edit_enabled( $context ) ) {

			// Don't keep running this filter.
			remove_filter( 'gravityview/template/field/output', array( $this, 'wrap_gravityview_field_value' ), 10 );

			return $output;
		}

		$entry = $context->entry->as_entry();

		// Don't expose additional information about the entry
		if ( ! GravityView_Inline_Edit::get_instance()->can_edit_entry( $entry['id'], $entry['form_id'] ) ) {
			return $output;
		}

		$field_settings = $context->field->as_configuration();

		// Check if field inline edit is disabled
		if ( 'disabled' === rgar( $field_settings, 'enable_inline_edit', 'enabled' ) ) {
			return $output;
		}

		$gf_field = $context->field->field instanceof \GF_Field ? $context->field->field : null;

		$forms = parent::get_cached_forms();

		$form_id = $entry['form_id'];

		if ( ! isset( $forms[ $form_id ] ) ) {
			$form = parent::cache_form( GFAPI::get_form( $form_id ) );
		} else {
			$form = $forms[ $form_id ];
		}

		add_filter( 'gravityview-inline-edit/wrapper-attributes', array( $this, 'filter_wrapper_attribute_add_entry_link' ), 10, 9 );

		$return = parent::wrap_field_value( $output, $entry, $field_settings['id'], $gf_field, $form, $field_settings, $context );

		remove_filter( 'gravityview-inline-edit/wrapper-attributes', array( $this, 'filter_wrapper_attribute_add_entry_link' ), 10 );

		return $return;
	}

	/**
	 * If the View field is linking to the single entry, set the data attribute for use in the UI to fix the link being stripped by Editable when changing the field value
	 *
	 * @since 1.4
	 *
	 * @param $wrapper_attributes
	 * @param $input_type
	 * @param $gf_field_id
	 * @param $entry
	 * @param $form
	 * @param $gf_field
	 * @param $output
	 * @param $field_settings
	 *
	 * @return array Modified attributes, with 'data-viewid' and 'data-entry-link' (optional) keys
	 */
	public function filter_wrapper_attribute_add_entry_link( $wrapper_attributes, $input_type, $gf_field_id, $entry, $form, $gf_field, $output, $field_settings, $context = null ) {

		$wrapper_attributes['data-viewid'] = $context->view->ID;

		$entry_link = gv_entry_link( $entry, $context->view->ID );

		if ( strpos( $output, $entry_link ) !== false) {
			$wrapper_attributes['data-entry-link'] = $entry_link;
		}

		return $wrapper_attributes;
	}

	/**
	 * Check whether Inline Edit is enabled for this View
	 *
	 * @since 1.0
	 * @since 2.0 Removed $view_id parameter and used $context instead.
	 *
	 * @param int|\GV\Template_Context $context Current context.
	 *
	 * @return bool True: yes, it is. False, why no! It is not.
	 */
	protected function is_inline_edit_enabled( $context ) {

		if ( ! $context instanceof \GV\Template_Context ) {
			return false;
		}

		$inline_edit = $context->view->settings->get( 'inline_edit', false );

		return ! empty( $inline_edit );
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
			'label'             => esc_html__( 'Enable Inline Edit', 'gk-gravityedit' ),
			'desc'              => esc_html__( 'Adds a link to toggle Inline Editing capabilities.', 'gk-gravityedit' ),
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
	public function maybe_add_inline_edit_toggle_button( $context = 0 ) {

		if ( ! $this->is_inline_edit_enabled( $context ) ) {
			return;
		}

		$view_id = $context->view->ID;

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
	 * @since 2.0 Added $context parameter.
	 *
	 * @param string $css_class Existing CSS classes for the GravityView container.
	 * @param \GV\Template_Context $context Current context.
	 *
	 * @return string CSS class with X-editable CSS class added
	 */
	public function add_container_class( $css_class, $context ) {

		if( ! $this->is_inline_edit_enabled( $context ) ) {
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
				'description' => esc_html__('Popup: The edit form will appear above the content.', 'gk-gravityedit' ),
			),
			array(
				'image' => 'in-place',
				'description' => esc_html__('In-Place: The edit form for the field will show in the same place as the content.', 'gk-gravityedit' ),
			),
		);

		$tooltip_format = '<p><img src="%s" height="150" style="display: block; margin-bottom: .5em;" /><strong>%s</strong></p>';

		$tooltip_html = '';

		foreach ( $tooltips as $tooltip ) {

			$image_link = plugins_url( "assets/images/{$tooltip['image']}.png", GRAVITYEDIT_FILE );

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
			'label'         => __( 'Inline Edit Mode', 'gk-gravityedit' ),
			'tooltip'       => $this->_get_edit_mode_tooltip_html(),
			'description'   => esc_html__( 'Change where the Inline Edit form appears &ndash; above the content or in its place.', 'gk-gravityedit' ) . ' ' . esc_html__( 'Hover over the ? above for examples of each mode.', 'gk-gravityedit' ),
			'default_value' => 'popup',
			'horizontal'    => 1,
			'choices'       => array(
				array
				(
					'label' => esc_html__( 'Popup', 'gk-gravityedit' ),
					'value' => 'popup',
				),
				array
				(
					'label' => esc_html__( 'In-Place', 'gk-gravityedit' ),
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

	/**
	 * Whether the current request is an admin request.
	 *
	 * @return bool
	 *
	 * @since 2.0
	 */
	private function is_gv_admin() {
		if ( function_exists( 'gravityview' ) ) {
			return gravityview()->request->is_admin();
		}

		if ( class_exists( 'GravityView_Admin' ) ) {
			return GravityView_Admin::is_admin_page();
		}

		return false;
	}
}

GravityView_Inline_Edit_GravityView::get_instance();
