<?php

final class GravityView_Inline_Edit_Gravity_Forms extends GravityView_Inline_Edit_Render {
	/**
	 * @return bool Whether to load the hooks
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	protected function should_add_hooks() {

		// Gravity Forms pages
		$current_page = trim( strtolower( rgget( 'page' ) ) );

		// Entries Page or Form Settings page
		return is_admin() && function_exists('rgget') && in_array( $current_page, array( 'gf_edit_forms', 'gf_entries' ) );
	}

	/**
	 * Add hooks for loading inline edit for Gravity Forms
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	protected function add_hooks() {

		parent::add_hooks();

		add_filter( 'gform_entries_field_value', array( $this, 'wrap_gravity_forms_field_value' ), 10, 4 );

		add_action( 'gform_pre_entry_list', array( $this, 'maybe_enqueue_inline_edit_styles' ), 100 );
		add_action( 'gform_pre_entry_list', array( $this, 'open_container_wrapper' ) );
		add_action( 'gform_pre_entry_list', array( $this, 'maybe_add_inline_edit_toggle_button' ) );
		add_action( 'gform_post_entry_list', array( $this, 'close_container_wrapper' ) );
		add_action( 'gform_post_entry_list', array( $this, 'maybe_enqueue_inline_edit_scripts' ), 200 );

		add_filter( 'gform_pre_form_settings_save', array( $this, 'pre_form_settings_save' ) );
		add_filter( 'gform_tooltips', array( $this, 'tooltips' ) );
		add_filter( 'gform_form_settings', array( $this, 'form_settings' ), 10, 2 );

		// Set priority to 19 so it runs before the "Inline Edit Behavior" settings added by Entry Revisions 1.1
		add_filter( 'gform_form_settings_fields', array( $this, 'add_settings_field' ), 19, 2 );
	}

	/**
	 * Add a tooltip explaining what enabling in the Form will do
	 *
	 * @since 1.0
	 *
	 * @param array $tooltips
	 *
	 * @return array $tooltips - With our tooltip added
	 */
	public function tooltips( $tooltips ) {

		$image   = GRAVITYVIEW_INLINE_URL . 'assets/images/gf-inline-edit-toggle.png';
		$content = sprintf( '<h4>%s</h4><p>%s</p><p>%s</p>',
			esc_html__( 'An Inline Edit button will be added', 'gk-gravityedit' ),
			'<img src="' . esc_url( $image ) . '" class="alignright" width="300" style="max-width: 100%;" />',
			esc_html__( 'If enabled, a button to toggle on and off inline editing will be added to the Gravity Forms Entries screen. If disabled, the button will not be added.', 'gk-gravityedit' )
		);

		$tooltips['gv_inline_edit_enable'] = $content;

		return $tooltips;
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
	 * Check whether Gravity Forms is v2.5-beta or newer
	 *
	 * @since 1.5
	 *
	 * @return bool
	 */
	public static function is_GF_25() {
		return version_compare( '2.5-beta', \GFForms::$version, '<=' );
	}

	/**
	 * Adds the Enable Inline Edit field to the "Form Options" settings group in GF 2.5+
	 *
	 * @see https://docs.gravityforms.com/gform_form_settings_fields/
	 *
	 * @param array $fields Form Settings fields.
	 * @param array $form The current form
	 *
	 * @return array
	 */
	function add_settings_field( $fields, $form = array() ) {

		$fields['form_options']['fields'][] = array(
			'name' => 'gv_inline_edit_enable',
			'type' => 'toggle',
			'label' => esc_html__( 'Enable Inline Edit', 'gk-gravityedit' ),
			'description' => esc_html__( 'Allow Inline Edit when viewing this form\'s entries in Gravity Forms', 'gk-gravityedit' ),
			'tooltip' => gform_tooltip( 'gv_inline_edit_enable', '', true ),
			'default_value' => false,
		);

		return $fields;
	}

	/**
	 * Save the Inline Edit setting when the form is updated
	 *
	 * @since 1.0
	 *
	 * @param array $updated_form Form object
	 *
	 * @return array Form object, with our `gv_inline_edit_enable` setting added
	 */
	public function pre_form_settings_save( $updated_form = array() ) {

		// If running GF 2.5, no need to modify the form settings
		if ( self::is_GF_25() ) {
			return $updated_form;
		}

		$updated_form['gv_inline_edit_enable'] = rgempty( 'gv_inline_edit_enable' ) ? 0 : 1;

		return $updated_form;
	}

	/**
	 * Add a checkbox to toggle Inline Edit at the bottom of the GF Form Settings screen
	 *
	 * @since 1.0
	 *
	 * @param array $form_settings The form settings.
	 * @param array $form The Form Object.
	 *
	 * @return array $form_settings with the Enable Inline Edit setting added
	 */
	public function form_settings( $form_settings, $form = array() ) {

		/** If running GF 2.5, no need to modify the form settings. Instead, we use {@see add_settings_field()}. */
		if ( self::is_GF_25() ) {
			return $form_settings;
		}

		$tr_enable_inline_edit = '
        <tr>
            <th>
                ' . esc_html__( 'Enable Inline Edit', 'gk-gravityedit' ) . ' ' . gform_tooltip( 'gv_inline_edit_enable', '', true ) . '
            </th>
            <td>
            	<input type="hidden" name="gv_inline_edit_enable" value="0" />
                <input type="checkbox" id="gv_inline_edit_enable" name="gv_inline_edit_enable" value="1" ' . checked( true, ! rgempty( 'gv_inline_edit_enable', $form ), false ) . ' />
                <label for="gv_inline_edit_enable">' . esc_html__( 'Allow Inline Edit when viewing this form\'s entries in Gravity Forms', 'gk-gravityedit' ) . '</label>
            </td>
        </tr>';

		$label = esc_html__( 'Inline Edit', 'gk-gravityedit' );

		$form_settings[ $label ] = array(
			'enable' => $tr_enable_inline_edit,
		);

		return $form_settings;
	}

	/**
	 * Only enqueue styles on Entry List page
	 *
	 * @since 1.0
	 *
	 * @param int $form_id ID of the current form entries are being displayed for
	 *
	 * @return void
	 */
	function maybe_enqueue_inline_edit_styles( $form_id = 0 ) {

		if ( ! $this->is_inline_edit_enabled( $form_id ) ) {
			return;
		}

		do_action( 'gravityview-inline-edit/enqueue-styles', compact( 'form_id' ) );
	}

	/**
	 * Add a <div> wrapper to the Gravity Forms entries table
	 *
	 * @since 1.0
	 *
	 * @param int $form_id ID of the current form entries are being displayed for
	 *
	 * @return void
	 */
	function open_container_wrapper( $form_id = 0) {

		if ( ! $this->is_inline_edit_enabled( $form_id ) ) {
			return;
		}

		echo '<!-- start gv-inline-edit container --> 
		<div class="gv-inline-editable-view">
		<input type="hidden" class="gravityview-inline-edit-id" value="form-' . esc_html( rgget( 'id' ) ) . '" />';
	}

	/**
	 * Close the <div> wrapper container
	 *
	 * @since 1.0
	 *
	 * @param int $form_id ID of the current form entries are being displayed for
	 *
	 * @return void
	 */
	function close_container_wrapper( $form_id = 0 ) {

		if ( ! $this->is_inline_edit_enabled( $form_id ) ) {
			return;
		}

		echo '</div> 
		<!-- end gv-inline-edit container -->';
	}

	/**
	 * Check whether Inline Edit is enabled for a form in form settings
	 *
	 * @since 1.0
	 *
	 * @param int $form_id The ID of the form to check
	 *
	 * @return bool True: Inline Edit is enabled for this form; False: nope!
	 */
	function is_inline_edit_enabled( $form_id = 0 ) {

		if ( empty( $form_id ) ) {
			return false;
		}

		$form = RGFormsModel::get_form_meta( $form_id );

		if ( ! $form ) {
			return false;
		}

		return ! rgempty( 'gv_inline_edit_enable', $form );
	}

	/**
	 * Enqueue inline edit scripts, if enabled for the form
	 *
	 * @since 1.0
	 *
	 * @param int $form_id ID of the form to
	 *
	 * @return void
	 */
	public function maybe_enqueue_inline_edit_scripts( $form_id = 0 ) {

		if ( ! $this->is_inline_edit_enabled( $form_id ) ) {
			return;
		}

		do_action( 'gravityview-inline-edit/enqueue-scripts', compact( 'form_id' ) );
	}

	/**
	 * Add the inline edit toggle button, if form has inline edit enabled
	 *
	 * @since 1.0
	 *
	 * @param int $form_id Form currently displaying entries
	 *
	 * @return void
	 */
	public function maybe_add_inline_edit_toggle_button( $form_id = 0 ) {

		if ( ! $this->is_inline_edit_enabled( $form_id ) ) {
			return;
		}

		if ( 0 === GFAPI::count_entries( $form_id ) ) {
			return;
		}

		$this->add_inline_edit_toggle_button();
	}

	/**
	 * Wrap the field values in inline edit HTML
	 *
	 * @since 1.0
	 *
	 * @param string $value The HTML of the field value
	 * @param int $form_id The ID of the form being displayed
	 * @param int|string $field_id ID of the field or entry meta
	 * @param array $entry Entry object
	 *
	 * @return string HTML
	 */
	public function wrap_gravity_forms_field_value( $value, $form_id, $field_id, $entry ) {

		if ( ! $this->is_inline_edit_enabled( $form_id ) ) {
			return $value;
		}

		$forms = parent::get_cached_forms();

		if ( ! isset( $forms[ $form_id ] ) ) {
			$form = parent::cache_form( GFAPI::get_form( $form_id ) );
		} else {
			$form = $forms[ $form_id ];
		}

		$gf_field = GFFormsModel::get_field( $form, $field_id );

		// This is a nasty hack to make sure that the file icons are wrapped in <div>s instead of <span>s;
		// if wrapped in spans, the value is shown as "Empty" and the file icon isn't clickable for editing.
		// This works because we're adding a block-level element to the output.
		if( $gf_field instanceof GF_Field_FileUpload ) {
			$value .= '<div style="display: none;"></div>';
		}

		return parent::wrap_field_value( $value, $entry, $field_id, $gf_field, $form );
	}
}

GravityView_Inline_Edit_Gravity_Forms::get_instance();
