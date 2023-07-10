<?php

/**
 * @since 1.0
 */
final class GravityView_Inline_Edit {

	/**
	 * @var string Version number of the plugin, set during initialization
	 *
	 * @since 1.0
	 */
	protected $_version = NULL;

	/**
	 * @var int The ID of the download on www.gravitykit.com
	 *
	 * @since 1.0
	 */
	protected $_item_id = 532208;

	/**
	 * @var GravityView_Inline_Edit
	 *
	 * @since 1.0
	 */
	private static $_instance;


	/**
	 * GravityView_Inline_Edit constructor.
	 *
	 * @since 1.0
	 *
	 * @param string $version_number Current version of the plugin
	 * @param GravityView_Inline_Edit_GFAddon $gf_addon
	 */
	public function __construct( $version_number = '', $gf_addon = null ) {

		$this->_title = esc_html__( 'GravityEdit', 'gk-gravityedit' );
		$this->_version = $version_number;
		$this->GFAddon = $gf_addon;

		$this->_require_files();
		$this->_include_field_files();
	}

	/**
	 * Singleton instance
	 *
	 * @since 1.0
	 *
	 * @param string $version_number Current version of the plugin
	 * @param GravityView_Inline_Edit_GFAddon $gf_addon
	 *
	 * @return GravityView_Inline_Edit  GravityView_Plugin object
	 */
	public static function get_instance( $version_number = '', $gf_addon = null ) {

		if ( empty( self::$_instance ) ) {
			self::$_instance = new self( $version_number, $gf_addon );
		}

		return self::$_instance;
	}

	/**
	 * Get the current edit style
	 *
	 * @since 1.0
	 *
	 * @return string "jquery-editable", "jqueryui-editable" or "bootstrap3-editable"
	 */
	public function get_edit_style() {

		/**
		 * @var string "jquery-editable", "jqueryui-editable" or "bootstrap3-editable"
		 *
		 * @since 1.0
		 */
		$default_style = 'bootstrap3-editable';

		/**
		 * @filter `gravityview-inline-edit/edit-style` Modify the inline edit style
		 *
		 * @since 1.0
		 *
		 * @param string $edit_style Editing style. Options: "jquery-editable", "jqueryui-editable" or "bootstrap3-editable" [Default: "bootstrap3-editable"]
		 */
		$edit_style = apply_filters( 'gravityview-inline-edit/edit-style', $default_style );

		return in_array( $edit_style, array( 'jquery-editable', 'jqueryui-editable', 'bootstrap3-editable' ) ) ? $edit_style : $default_style;
	}

	/**
	 * Get the current edit mode ("popup" or "inline")
	 *
	 * @since 1.0
	 *
	 * @return string "popup" or "inline"
	 */
	public function get_edit_mode() {

		$edit_mode = GravityView_Inline_Edit_GFAddon::get_instance()->get_plugin_setting( 'inline-edit-mode' );

		/**
		 * @filter `gravityview-inline-edit/edit-mode` Modify the inline edit mode.
		 *
		 * @since 1.0
		 *
		 * @param string $edit_mode Editing mode. Options: "popup" or "inline" [Default: "popup"]
		 */
		$edit_mode = apply_filters( 'gravityview-inline-edit/edit-mode', $edit_mode );

		return in_array( $edit_mode, array( 'popup', 'inline' ) ) ? $edit_mode : 'popup';
	}

	/**
	 * Load all the files needed for the plugin
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function _require_files() {
		require_once( GRAVITYEDIT_DIR . 'includes/class-gravityview-inline-edit-scripts.php' );
		require_once( GRAVITYEDIT_DIR . 'includes/class-gravityview-inline-edit-render-abstract.php' );
		require_once( GRAVITYEDIT_DIR . 'includes/class-gravityview-inline-edit-gravityview.php' );
		require_once( GRAVITYEDIT_DIR . 'includes/class-gravityview-inline-edit-gravity-forms.php' );
		require_once( GRAVITYEDIT_DIR . 'includes/class-gravityview-inline-edit-user-registration.php' );
		require_once( GRAVITYEDIT_DIR . 'includes/class-gravityview-inline-edit-ajax.php' );
	}

	/**
	 * Include files related to field types
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function _include_field_files() {

		include_once( GRAVITYEDIT_DIR . 'includes/fields/class-gravityview-inline-edit-field.php' );

		// Load all field files automatically
		foreach ( glob( GRAVITYEDIT_DIR . 'includes/fields/class-gravityview-inline-edit-field*.php' ) as $gv_inline_field_filename ) {
			include_once( $gv_inline_field_filename );
		}
	}

	/**
	 * Get the fields ignored by inline edit
	 *
	 * @since 1.0
	 *
	 * @return array The ignored fields
	 */
	public function get_ignored_fields() {

		$ignored_fields = array(
			'notes',
			'entry_approval',
			'edit_link',
			'delete_link',
		);

		/**
		 * @filter `gravityview-inline-edit/ignored-fields` The fields ignored by GravityView Inline Edit
		 *
		 * @since 1.0
		 *
		 * @param array $ignored_fields The ignored fields
		 */
		return apply_filters( 'gravityview-inline-edit/ignored-fields', $ignored_fields );
	}

	/**
	 * Get the fields supported by inline edit
	 *
	 * @since 1.0
	 *
	 * @return array The supported fields
	 */
	public function get_supported_fields() {
		$supported_fields = array(
			'address',
			'checkbox',
			'date',
			'email',
			'hidden',
			'list',
			'multiselect',
			'name',
			'number',
			'phone',
			'product',
			'radio',
			'select',
			'text',
			'textarea',
			'time',
			'website',
			'fileupload',
			'created_by',
			'source_url',
			'date_created',
		);

		/**
		 * @filter `gravityview-inline-edit/supported-fields` The fields supported by GravityView Inline Edit
		 *
		 * @since 1.0
		 *
		 * @param array $supported_fields The supported fields
		 */
		return apply_filters( 'gravityview-inline-edit/supported-fields', $supported_fields );
	}

	/**
	 * Get the template used for the inline edit buttons
	 *
	 * @since 1.0
	 *
	 * @return string HTML for the buttons used by inline edit
	 */
	public function get_buttons_template() {

		$buttons = array(
			'ok'     => array(
				'text'  => __( 'Update', 'gk-gravityedit' ),
				//can be replaced with <i class="glyphicon glyphicon-ok"></i>
				'class' => ( is_admin() ? ' button button-primary button-large alignleft' : '' ),
			),
			'cancel' => array(
				'text'  => __( 'Cancel', 'gk-gravityedit' ),
				//can be replaced with <i class="glyphicon glyphicon-remove"></i>
				'class' => ( is_admin() ? ' button button-secondary alignright' : '' ),
			),
		);

		/**
		 * @filter `gravityview-inline-edit/form-buttons` Modify the text and CSS classes used inline edit buttons
		 *
		 * @since 1.0
		 *
		 * @param array $buttons The default button configuration
		 */
		$buttons = apply_filters( 'gravityview-inline-edit/form-buttons', $buttons );

		ob_start();
		require( GRAVITYEDIT_DIR . 'templates/buttons-edit.php' );

		return ob_get_clean();
	}

	/**
	 * Can the current user edit entries?
	 *
	 * @since 1.0
	 * @since 1.2 Added $view_id param
	 *
	 * @param null|int $entry_id ID of a specific entry being displayed
	 * @param null|int $form_id ID of the form connected to the current View
	 * @param null|int $view_id ID of the current View
	 *
	 * @return bool True: the current user can edit the entry; false: no, they do not have permission
	 */
	public function can_edit_entry( $entry_id = null, $form_id = null, $view_id = null ) {

		// Require Gravity Forms
		if ( ! class_exists( 'GFCommon' ) ) {
			return false;
		}

		$can_edit = false;

		// Edit all entries
		$caps = array(
			'gravityforms_edit_entries',
		);

		/**
		 * @filter `gravityview-inline-edit/inline-edit-caps` Caps required for an user to edit an entry. Passed to GFCommon::current_user_can_any()
		 *
		 * @since 1.0
		 *
		 * @uses GFCommon::current_user_can_any()
		 *
		 * @param array $caps Array of user capabilities needed to allow inline editing of entries
		 */
		$caps = apply_filters( 'gravityview-inline-edit/inline-edit-caps', $caps );

		if ( GFCommon::current_user_can_any( $caps ) ) {
			$can_edit = true;
		}

		/**
		 * @filter `gravityview-inline-edit/user-can-edit-entry` Modify whether the current user can edit an entry
		 *
		 * @since 1.0
		 * @since 1.2 Added $view_id parameter
		 *
		 * @param bool $can_edit_entry True: User can edit the entry at $entry_id; False; they just can't
		 * @param int $entry_id Entry ID to check
		 * @param int $form_id Form connected to $entry_id
		 * @param int|null $view_id ID of the View being edited, if exists. Otherwise, NULL.
		 */
		return apply_filters( 'gravityview-inline-edit/user-can-edit-entry', $can_edit, $entry_id, $form_id, $view_id );
	}

	/**
	 * Get the current version of the plugin
	 *
	 * @since 1.0
	 *
	 * @return string Version number
	 */
	public static function get_version() {
		return self::get_instance()->_version;
	}

}
