<?php

/**
 * Handle all operations related to custom GravityView fields
 *
 */


/**
 * @since 1.0
 */
final class GravityView_Inline_Edit_Scripts {

	/**
	 * @var array styles to be added to GF no-conflict list
	 *
	 * @since 1.0
	 */
	private $_styles = array();

	/**
	 * @var array Scripts to be added to GF no-conflict list
	 *
	 * @since 1.0
	 */
	private $_scripts = array();

	/**
	 * @var array
	 *
	 * @since 1.3.1
	 */
	private $_custom_field_scripts = array();

	/**
	 * Instance of this class.
	 *
	 * @since 1.0
	 *
	 * @var GravityView_Inline_Edit_Scripts
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * GravityView_Inline_Edit_Custom_Fields constructor.
	 *
	 * @since 1.0
	 *
	 */
	private function __construct() {

		$this->_add_hooks();
	}

	/**
	 * Add WordPress hooks related to enqueuing and printing scripts
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function _add_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts_and_styles' ) );
		add_action( 'admin_init', array( $this, 'register_scripts_and_styles' ) );
		add_filter( 'gform_noconflict_scripts', array( $this, 'noconflict_scripts' ) );
		add_filter( 'gform_noconflict_styles', array( $this, 'noconflict_styles' ) );

		add_action( 'gravityview-inline-edit/enqueue-styles', array( $this, 'enqueue_styles' ) );
		add_action( 'gravityview-inline-edit/enqueue-scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'gravityview-inline-edit/enqueue-scripts', array( $this, 'datatables_enqueue_field_scripts' ) );
	}

	/**
	 * Enqueues all custom field scripts when a View is running DataTables
	 *
	 * When running DataTables, we aren't able to enqueue scripts dynamically, so we load them all up-front.
	 *
	 * @since 1.3.1
	 *
	 * @param array $item_id Array with [form_id] key set for Form ID, or [view_id] key set for View ID currently being displayed
	 *
	 * @return void
	 */
	public function datatables_enqueue_field_scripts( $item_id = array() ) {

		// Not on GF screens
		if ( empty( $item_id['view_id'] ) ) {
			return;
		}

		if ( ! class_exists( '\GV\View' ) ) {
			return;
		}

		$view = \GV\View::by_id( $item_id['view_id'] );

		// Only DataTables, please
		if( 'datatables_table' !== $view->settings->get('template') ) {
			return;
		}

		foreach ( $this->_custom_field_scripts as $script ) {
			wp_enqueue_script( $script );
		}
	}

	/**
	 * Enqueue and localize scripts
	 *
	 * @since 1.0
	 *
	 * @since 1.3 Added $object_id
	 *
	 * @param array $item_id Array with [form_id] key set for Form ID, or [view_id] key set for View ID currently being displayed
	 *
	 * @return void
	 */
	public function enqueue_scripts( $item_id = array() ) {

		wp_enqueue_script( GravityView_Inline_Edit::get_instance()->get_edit_style() );

		wp_enqueue_script( 'gv-inline-editable' );

		$js_settings = array(
			'mode'        => GravityView_Inline_Edit::get_instance()->get_edit_mode(),
			'buttons'     => GravityView_Inline_Edit::get_instance()->get_buttons_template(),
			'container'   => 'body',
			'showbuttons' => 'bottom',
			'onblur'      => 'cancel',
			'showinputs'  => false,
			'emptytext'   => esc_html__('Empty', 'gravityview-inline-edit')
		);

		/**
		 * @filter `gravityview-inline-edit/js-settings` Modify the settings passed to the x-editable script
		 *
		 * @since 1.0
		 * @since 1.3 Added $item_id
		 *
		 * @param array $js_settings {
		 *  @type string $mode Editing mode. Options: "popup" or "inline" [Default: "popup"]
		 *  @type string $buttons HTML of the Update/Cancel buttons {@see GravityView_Inline_Edit::get_buttons_template}
		 *  @type string $container When using `popup` $mode, jQuery selector used to attach the popup container [Default: "body"]
		 *  @type string|bool $showbuttons Where to show buttons. Form without buttons is auto-submitted. Options are "top", "bottom", "left", "right", or false. [Default: "bottom"]
		 *  @type string $onblur Action when user clicks outside the inline edit form container. Options are "cancel", "submit", "ignore". Setting ignore allows to have several containers open. [Default: "cancel"]
		 *  @type bool|string $showinputs jQuery selectors for which inputs to show without clicking. NOTE: `onblur` must be set to "ignore" {@see http://docs.gravityview.co/article/418-showing-inputs-on-load}
		 * }
		 * @param array $item_id Array with [form_id] key set for Form ID, or [view_id] key set for View ID currently being displayed
		 */
		$js_settings = apply_filters( 'gravityview-inline-edit/js-settings', $js_settings, $item_id );

		// These shouldn't be modified by the filter
		$js_settings['url']           = admin_url( 'admin-ajax.php' );
		$js_settings['nonce']         = wp_create_nonce( 'gravityview_inline_edit' );
		$js_settings['cookie_domain'] = COOKIE_DOMAIN;
		$js_settings['cookiepath']    = COOKIEPATH;
		$js_settings['templates']     = GravityView_Inline_Edit_Field::get_field_templates();

		wp_localize_script( 'gv-inline-editable', 'gv_inline_x', $js_settings );
	}

	/**
	 * Add our styles to Gravity Forms no-conflict list
	 *
	 * @since 1.0
	 *
	 * @param array $styles Existing no-conflict styles
	 *
	 * @return array No-conflict styles, with our own added
	 */
	public function noconflict_styles( $styles = array() ) {
		$styles = array_merge( $styles, $this->_styles );

		return $styles;
	}

	/**
	 * Add our scripts to Gravity Forms no-conflict list
	 *
	 * @since 1.0
	 *
	 * @param array $scripts Existing no-conflict scripts
	 *
	 * @return array No-conflict scripts, with our own added
	 */
	public function noconflict_scripts( $scripts = array() ) {
		$scripts = array_merge( $scripts, $this->_scripts );

		return $scripts;
	}

	/**
	 * Call all functions to register scripts and styles. No enqueuing.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function register_scripts_and_styles() {
		$this->_register_scripts();
		$this->_register_custom_field_scripts();
		$this->_register_styles();
	}

	/**
	 * Register field type scripts, and add them to the $_scripts var
	 *
	 * @since 1.0
	 *
	 * @todo Register field scripts in the field classes themselves by defining script name as a property
	 *
	 * @return void
	 */
	private function _register_custom_field_scripts() {

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$custom_inline_edit_field_types = array( 'address', 'multiselect', 'radiolist', 'name', 'gvtime', 'checklist' );

		foreach ( $custom_inline_edit_field_types as $custom_field ) {
			wp_register_script( 'gv-inline-edit-' . $custom_field, GRAVITYVIEW_INLINE_URL . 'assets/js/fields/' . $custom_field . $script_debug . '.js', array(
				'jquery',
				GravityView_Inline_Edit::get_instance()->get_edit_style(),
				'gv-inline-edit-gvutils',
			), GravityView_Inline_Edit::get_version() );
			$this->_scripts[] = 'gv-inline-edit-' . $custom_field;
			$this->_custom_field_scripts[] = 'gv-inline-edit-' . $custom_field;
		}

		wp_register_script( 'gv-inline-edit-gvlist', GRAVITYVIEW_INLINE_URL . 'assets/js/fields/gvlist' . $script_debug . '.js', array(
			'jquery',
			GravityView_Inline_Edit::get_instance()->get_edit_style(),
			'gform_gravityforms',
		), GravityView_Inline_Edit::get_version() );

		$this->_scripts[] = 'gv-inline-edit-gvlist';
	}

	/**
	 * Register default scripts and add them to the $_scripts var
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function _register_scripts() {

		$edit_style = GravityView_Inline_Edit::get_instance()->get_edit_style();

		//Supported versions and their corresponding filenames & dependencies
		$supported_scripts_versions = array(
			'jqueryui-editable'   => array(
				'file' => 'jqueryui-editable.js',
				'deps' => array( 'jquery', 'gv-jquery-ui-core' ),
			),
			'jquery-editable'     => array(
				'file' => 'jquery-editable-poshytip.js',
				'deps' => array( 'jquery', 'poshytip' ),
			),
			'bootstrap3-editable' => array(
				'file' => 'bootstrap-editable.js',
				'deps' => array( 'jquery', 'gv-bootstrap' ),
			),
		);

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_script( 'poshytip', GRAVITYVIEW_INLINE_URL . 'bower_components/poshytip/src/jquery.poshytip.js', array( 'jquery' ), GravityView_Inline_Edit::get_version() );
		wp_register_script( 'gv-bootstrap', GRAVITYVIEW_INLINE_URL . 'bower_components/bootstrap-sass/assets/javascripts/bootstrap' . $script_debug . '.js', array(), GravityView_Inline_Edit::get_version() );
		wp_register_script( 'gv-jquery-ui-core', GRAVITYVIEW_INLINE_URL . 'assets/js/jquery-ui-1.11.0.min.js', array( 'jquery' ) );
		wp_register_script( 'jquery-cookie', GRAVITYVIEW_INLINE_URL . 'bower_components/jquery.cookie/jquery.cookie.js', array( 'jquery' ) );
		wp_register_script( $edit_style, GRAVITYVIEW_INLINE_URL . 'bower_components/x-editable/dist/' . $edit_style . '/js/' . $supported_scripts_versions[ $edit_style ]['file'], $supported_scripts_versions[ $edit_style ]['deps'], GravityView_Inline_Edit::get_version() );

		$this->_scripts = array_merge( array_keys( $supported_scripts_versions ), $this->_scripts );
		$this->_scripts = array_merge( array(
			'poshytip',
			'gv-bootstrap',
			'gv-jquery-ui-core',
			'jquery-cookie',
			$edit_style,
		), $this->_scripts );

		wp_register_script( 'gv-inline-editable', GRAVITYVIEW_INLINE_URL . 'assets/js/fields-inline-editable' . $script_debug . '.js', array(
			'jquery',
			'jquery-cookie',
			$edit_style,
		), GravityView_Inline_Edit::get_version(), true );
		$this->_scripts[] = 'gv-inline-editable';

		wp_register_script( 'gv-inline-edit-gvutils', GRAVITYVIEW_INLINE_URL . 'assets/js/fields/gvutils' . $script_debug . '.js', array(
			'jquery',
			$edit_style,
		), GravityView_Inline_Edit::get_version() );
		$this->_scripts[] = 'gv-inline-edit-gvutils';

		wp_register_script( 'gv-inline-edit-wysihtml5', GRAVITYVIEW_INLINE_URL . 'assets/js/wysihtml5-0.3.0.min.js', array(), GravityView_Inline_Edit::get_version() );

		if ( 'bootstrap3-editable' === $edit_style ) {//Register Bootstrap 3 wysihtml5 with deps
			wp_register_script( 'gv-inline-edit-wysihtml5-bootstrap3', GRAVITYVIEW_INLINE_URL . 'assets/js/bootstrap-wysihtml5-0.0.3.min.js', array(), GravityView_Inline_Edit::get_version() );
			$wysihtml5_deps = array(
				$edit_style,
				'gv-inline-edit-wysihtml5',
				'gv-inline-edit-wysihtml5-bootstrap3',
			);
			wp_register_script( 'gv-inline-edit-wysihtml5-js', GRAVITYVIEW_INLINE_URL . 'assets/js/wysihtml5-0.0.3.js', $wysihtml5_deps, GravityView_Inline_Edit::get_version() );

			$this->_scripts   = array_merge( $this->_scripts, $wysihtml5_deps );
			$this->_scripts[] = 'gv-inline-edit-wysihtml5-js';
		}
	}

	/**
	 * Register styles and add them to the $_styles var
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function _register_styles() {

		/**
		 * @filter `gravityview-inline-edit/jquery-ui-theme` Modify the jQuery UI theme to use, if jQuery UI editor style is active
		 *
		 * @since 1.0
		 *
		 * @param string $jquery_ui_theme Name of jQuery UI theme to use. Default: "base"
		 *
		 * @see http://jqueryui.com/themeroller/#themeGallery for examples
		 */
		$jquery_ui_theme = apply_filters( 'gravityview-inline-edit/jquery-ui-theme', 'base' );

		$jquery_themes = array(
			'base',
			'black-tie',
			'blitzer',
			'cupertino',
			'dark-hive',
			'dot-luv',
			'eggplant',
			'excite-bike',
			'flick',
			'hot-sneaks',
			'humanity',
			'le-frog',
			'mint-choc',
			'overcast',
			'pepper-grinder',
			'redmond',
			'smoothness',
			'south-street',
			'start',
			'sunny',
			'swanky-purse',
			'trontastic',
			'ui-darkness',
			'ui-lightness',
			'vader',
		);

		// Only allow valid theme options
		$jquery_ui_theme = in_array( $jquery_ui_theme, $jquery_themes ) ? $jquery_ui_theme : 'base';

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_style( 'gv-inline-edit-jquery-ui', GRAVITYVIEW_INLINE_URL . 'bower_components/jquery-ui/themes/' . $jquery_ui_theme . '/jquery-ui' . $script_debug . '.css' );

		/**
		 * @filter `gravityview-inline-edit/poshytip-theme` Modify the Poshytip theme to use, if jQuery editor style is active
		 *
		 * @since 1.0
		 *
		 * @see http://vadikom.com/demos/poshytip/ for examples
		 *
		 * @param string $poshytip_theme Name of jQuery popup tooltip theme to use. Default: "yellowsimple"
		 */
		$poshytip_theme = apply_filters( 'gravityview-inline-edit/poshytip-theme', 'yellowsimple' );

		$poshytip_themes = array( 'darkgray', 'green', 'skyblue', 'twitter', 'violet', 'yellow', 'yellowsimple' );

		// Only allow valid theme options
		$poshytip_theme = in_array( $poshytip_theme, $poshytip_themes ) ? $poshytip_theme : 'yellowsimple';

		wp_register_style( 'poshytip', GRAVITYVIEW_INLINE_URL . 'bower_components/poshytip/src/tip-' . $poshytip_theme . '/tip-' . $poshytip_theme . '.css' );

		wp_register_style( 'gv-inline-edit-datepicker', GRAVITYVIEW_INLINE_URL . 'assets/css/datepicker.min.css', array(), GravityView_Inline_Edit::get_version() );

		wp_register_style( 'gv-inline-edit-wysihtml5', GRAVITYVIEW_INLINE_URL . 'assets/css/bootstrap-wysihtml5-0.0.3.css', array(), GravityView_Inline_Edit::get_version() );

		//Supported versions and their corresponding file names and dependencies
		$supported_styles_versions = array(
			'jqueryui-editable'   => array(
				'file' => 'jqueryui-editable.css',
				'deps' => array( 'gv-inline-edit-jquery-ui' ),
			),
			'jquery-editable'     => array(
				'file' => 'jquery-editable.css',
				'deps' => array( 'poshytip' ),
			),
			'bootstrap3-editable' => array(
				'file' => 'bootstrap-editable.css',
				'deps' => array(),
			),
		);

		foreach ( $supported_styles_versions as $key => $style ) {
			wp_register_style( 'gv-inline-edit-style-' . $key, GRAVITYVIEW_INLINE_URL . 'assets/css/' . $style['file'], $style['deps'] );
			$this->_styles[] = 'gv-inline-edit-style-' . $key;
		}

		wp_register_style( 'gv-inline-edit-fields', GRAVITYVIEW_INLINE_URL . 'assets/css/inline-editable.css', array( 'gv-inline-edit-style-' . GravityView_Inline_Edit::get_instance()->get_edit_style() ) );

		$styles = array(
			'gv-inline-edit-fields',
			'gv-inline-edit-jquery-ui',
			'poshytip',
			'gv-inline-edit-datepicker',
			'gv-inline-edit-wysihtml5',
		);

		$this->_styles = array_merge( $styles, $this->_styles );
	}

	/**
	 * Enqueue styles for inline edit
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'gv-inline-edit-fields' );
	}

}

GravityView_Inline_Edit_Scripts::get_instance();
