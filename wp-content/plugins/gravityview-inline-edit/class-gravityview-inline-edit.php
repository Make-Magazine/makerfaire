<?php

/**
 * @since 1.0
 */
final class GravityView_Inline_Edit extends GravityView_Extension {

	/**
	 * @var string Name of the plugin in gravityview.co
	 *
	 * @since 1.0
	 */
	protected $_title = 'Inline Edit by GravityView';

	/**
	 * @var string Version number of the plugin, set during initialization
	 *
	 * @since 1.0
	 */
	protected $_version = NULL;

	/**
	 * @var int The ID of the download on gravityview.co
	 *
	 * @since 1.0
	 */
	protected $_item_id = 532208;

	/**
	 * @var string Minimum version of GravityView the Extension requires
	 */
	protected $_min_gravityview_version = '2.0-dev';

	/**
	 * @var string Translation textdomain
	 *
	 * @since 1.0
	 */
	protected $_text_domain = 'gravityview-inline-edit';

	/**
	 * @var string Path to main plugin file
	 *
	 * @since 1.0
	 */
	protected $_path = GRAVITYVIEW_INLINE_FILE;

	/**
	 * @var GravityView_Inline_Edit
	 *
	 * @since 1.0
	 */
	private static $_instance;

	/**
	 * @var GravityView_Inline_Edit_GFAddon|null
	 */
	private $GFAddon = null;

	/**
	 * @var bool Only refresh the license one, if settings have been saved
	 */
	private $_did_license_refresh = false;


	/**
	 * GravityView_Inline_Edit constructor.
	 *
	 * @since 1.0
	 *
	 * @param string $version_number Current version of the plugin
	 * @param GravityView_Inline_Edit_GFAddon $gf_addon
	 */
	public function __construct( $version_number = '', $gf_addon = null ) {

		$this->_title = esc_html__( 'Inline Edit by GravityView', 'gravityview-inline-edit' );
		$this->_version = $version_number;
		$this->GFAddon = $gf_addon;

		$this->_require_files();
		$this->_include_field_files();

		parent::__construct();
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

		/**
		 * @filter `gravityview-inline-edit/edit-mode` Modify the inline edit mode.
		 *
		 * @since 1.0
		 *
		 * @param string $edit_mode Editing mode. Options: "popup" or "inline" [Default: "popup"]
		 */
		$edit_mode = apply_filters( 'gravityview-inline-edit/edit-mode', 'popup' );

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
		require_once( GRAVITYVIEW_INLINE_DIR . 'includes/class-gravityview-inline-edit-scripts.php' );
		require_once( GRAVITYVIEW_INLINE_DIR . 'includes/class-gravityview-inline-edit-render-abstract.php' );
		require_once( GRAVITYVIEW_INLINE_DIR . 'includes/class-gravityview-inline-edit-gravityview.php' );
		require_once( GRAVITYVIEW_INLINE_DIR . 'includes/class-gravityview-inline-edit-gravity-forms.php' );
		require_once( GRAVITYVIEW_INLINE_DIR . 'includes/class-gravityview-inline-edit-ajax.php' );
	}

	/**
	 * Include files related to field types
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function _include_field_files() {

		include_once( GRAVITYVIEW_INLINE_DIR . 'includes/fields/class-gravityview-inline-edit-field.php' );

		// Load all field files automatically
		foreach ( glob( GRAVITYVIEW_INLINE_DIR . 'includes/fields/class-gravityview-inline-edit-field*.php' ) as $gv_inline_field_filename ) {
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
			'checkbox',
			'radio',
			'multiselect',
			'address',
			'date',
			'email',
			'textarea',
			'name',
			'list',
			'select',
			'phone',
			'time',
			'url',
			'website',
			'number',
			'text',
			'hidden',
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
				'text'  => __( 'Update', 'gravityview-inline-edit' ),
				//can be replaced with <i class="glyphicon glyphicon-ok"></i>
				'class' => ( is_admin() ? ' button button-primary button-large alignleft' : '' ),
			),
			'cancel' => array(
				'text'  => __( 'Cancel', 'gravityview-inline-edit' ),
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
		require( GRAVITYVIEW_INLINE_DIR . 'templates/buttons-edit.php' );

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
	 * Check whether the extension is supported:
	 *
	 * - Checks if Gravity Forms is active
	 * - Sets self::$is_compatible to boolean value
	 *
	 * @since 1.0
	 *
	 * @return boolean Is the extension able to continue running?
	 */
	protected function is_extension_supported() {

		self::$is_compatible = true;

		if ( ! class_exists( 'GFCommon' ) ) {

			$reason = esc_html__('The plugin requires Gravity Forms.', 'gravityview-inline-edit' );
			$message = esc_html_x('Could not activate %s: %s', '1st replacement is the plugin name; 2nd replacement is the reason why', 'gravityview-inline-edit' );
			$message = sprintf( $message, esc_html__('Inline Edit by GravityView', 'gravityview-inline-edit'), $reason );

			self::add_notice( $message );

			self::$is_compatible = false;
		}

		return self::$is_compatible;
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

	/**
	 * Get the title of the plugin
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function get_title() {
		return self::get_instance()->_title;
	}

	/**
	 * @since 1.0
	 *
	 * @return int Post ID of this plugin on gravityview.co
	 */
	public static function get_item_id() {
		return self::get_instance()->_item_id;
	}

	/**
	 * @since 1.0
	 *
	 * @return string Author of the plugin
	 */
	public static function get_author() {
		return self::get_instance()->_author;
	}

	/**
	 * @since 1.0
	 *
	 * @return string The URL to fetch license info from
	 */
	public static function get_remote_update_url() {
		return self::get_instance()->_remote_update_url;
	}

	/**
	 * @param object|array|string|WP_Error $response
	 */
	public function set_license_response( $response ) {
		set_transient( 'gravityview-inline-edit-license', (array) $response, DAY_IN_SECONDS );
	}

	/**
	 * Get license data from the website
	 *
	 * @uses GravityView_Inline_Edit_GFAddon::license_call()
	 *
	 * @param array $settings {
	 * @type string $edd_action The EDD action to perform, like `check_license`
	 * @type string $license The license key
	 * @type string $format If `object`, return the object of the license data. `array` for array, `json` to return the JSON-encoded object. [Default: "array"]
	 * }
	 *
	 * @return array|object|string|WP_Error Returns license data in the format specified (default: array). If error, returns WP_Error.
	 */
	public function fetch_license( $params = array() ) {

		$license_response = $this->GFAddon->license_call( $params );

		$this->set_license_response( $license_response );

		$this->_did_license_refresh = true;

		return $license_response;
	}

	/**
	 * Get license information for this plugin
	 *
	 * Fetches fresh information if the plugin's GF settings have been saved, or if $force_refresh is true
	 *
	 * @param bool $force_refresh Whether to force fetching fresh data about the license from the website
	 * @param array $params
	 *
	 * @return array License details. If error, returns array with error message.
	 */
	public function get_license( $force_refresh = false, $params = array() ) {

		$license = get_transient( 'gravityview-inline-edit-license' );

		$force_refresh = ( $force_refresh || $this->GFAddon->is_save_postback() );

		if ( empty( $license ) || $force_refresh ) {
			if( ! $this->_did_license_refresh ) {
				$license = $this->fetch_license( $params );
			}
		}

		if ( is_wp_error( $license ) ) {
			$license = array(
				'success' => false,
				'message' => $license->get_error_message(),
			);
		}

		return (array) $license;
	}

}
