<?php

defined( 'ABSPATH' ) || die();

GFForms::include_addon_framework();

class GFChainedSelects extends GFAddOn {

	protected $_version = GF_CHAINEDSELECTS_VERSION;
	protected $_min_gravityforms_version = '2.8.0';
	protected $_slug = 'gravityformschainedselects';
	protected $_path = 'gravityformschainedselects/chainedselects.php';
	protected $_full_path = __FILE__;
	protected $_url = 'http://www.gravityforms.com';
	protected $_title = 'Gravity Forms Chained Selects Add-On';
	protected $_short_title = 'Chained Selects';
	protected $_enable_rg_autoupgrade = true;

	private static $_instance = null;

	/* Permissions */
	protected $_capabilities_uninstall = 'gravityforms_chainedselects_uninstall';

	/* Members plugin integration */
	protected $_capabilities = array( 'gravityforms_chainedselects', 'gravityforms_chainedselects_uninstall' );

	/* Theme framework */
	protected $_enable_theme_layer = true;
	protected $_asset_min;

	/**
	 * Get instance of this class.
	 *
	 * @access public
	 * @static
	 * @return object $_instance
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {
			self::$_instance = new self;

			if ( ! isset( self::$_instance->_asset_min ) ) {
				self::$_instance->_asset_min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
			}
		}

		return self::$_instance;
	}

	/**
	 * Include Chain Select field class.
	 *
	 * @access public
	 * @return void
	 */
	public function pre_init() {
		parent::pre_init();

		if ( $this->is_gravityforms_supported() && class_exists( 'GF_Field' ) ) {
			require_once 'includes/class-gf-field-chainedselect.php';
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @access public
	 * @return array $scripts
	 */
	public function scripts() {
		$scripts = array(
			array(
				'handle'  => 'gform_chained_selects_admin',
				'deps'    => array( 'jquery', 'backbone', 'plupload', 'gform_form_admin' ),
				'src'     => $this->get_base_url() . "/js/admin{$this->_asset_min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor', 'form_settings' ) ),
				),
				'in_footer' => true,
				'callback'  => array( $this, 'localize_scripts' ),
			),
			array(
				'handle'  => 'gform_chained_selects_admin_form_editor',
				'deps'    => array( 'jquery', 'backbone', 'gform_form_editor', 'plupload' ),
				'src'     => $this->get_base_url() . "/js/admin-form-editor{$this->_asset_min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor' ) ),
				),
				'in_footer' => true,
				'callback'  => array( $this, 'localize_scripts' ),
			),
			array(
				'handle'  => 'gform_chained_selects',
				'deps'    => array( 'jquery', 'gform_gravityforms' ),
				'src'     => $this->get_base_url() . "/js/frontend{$this->_asset_min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'should_enqueue_frontend_script' )
				),
				'callback' => array( $this, 'localize_scripts' ),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Frontend scripts should only be enqueued if we're not on a GF admin page and the form contains our field type.
	 *
	 * @param $form
	 *
	 * @return bool
	 */
	public function should_enqueue_frontend_script( $form ) {
		return ! GFForms::get_page() && ! rgempty( GFFormsModel::get_fields_by_type( $form, array( 'chainedselect' ) ) );
	}

	/**
	 * Enqueue styles.
	 *
	 * @access public
	 * @return array $scripts
	 */
	public function styles() {
		$base_url = $this->get_base_url();
		$styles   = array(
			array(
				'handle'  => 'gform_chained_selects_admin',
				'src'     => $base_url . "/assets/css/dist/admin{$this->_asset_min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor', 'entry_view' ) ),
				),
			),
		);

		if ( ! $this->supports_theme_enqueuing() ) {
			$styles[] = array(
				'handle'  => 'gform_chained_selects_theme',
				'src'     => $base_url . "/assets/css/dist/theme{$this->_asset_min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page'  => array( 'form_editor', 'block_editor' ) ),
					array( 'field_types' => array( 'chainedselect' ) ),
				),
			);
			$styles[] = array(
				'handle'  => 'gform-chainedselects-theme-framework',
				'src'     => $base_url . "/assets/css/dist/theme-framework{$this->_asset_min}.css",
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page'  => array( 'form_editor', 'block_editor' ) ),
					array( 'field_types' => array( 'chainedselect' ) ),
				),
			);
		}

		return array_merge( parent::styles(), $styles );
	}

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @since 1.9
	 *
	 * @return string
	 */
	public function get_menu_icon() {
		return 'gform-icon--chained-selects';
	}

	/**
	 * Helper method that returns the theme styles that should be enqueued for the add-on.
	 * Returns an array in the format accepted by the Gravity Forms theme layer set_styles() method
	 *
	 * @since 1.8.0
	 *
	 * @param array  $form               The current form object to enqueue styles for.
	 * @param string $field_type         The field type associated with the add-on. Styles will only be enqueued on the frontend if the form has a field with the specified field type.
	 * @param string $gravity_theme_path The path to the gravity theme style. Optional. Only needed for add-ons that implement the gravity theme outside of the default /assets/css/dist/theme.css path.
	 *
	 * @return array Returns and array of styles to enqueue in the format accepted by the Gravity Forms theme layer set_styles() method.
	 */
	public function get_theme_layer_styles( $form, $field_type = '', $gravity_theme_path = '' ) {
		$styles = parent::get_theme_layer_styles( $form, $field_type, $gravity_theme_path );

		unset( $styles['foundation'] );

		return $styles;
	}

	/**
	 * Checks if the version of Gravity Forms used supports
	 * the theme framework enqueuing system.
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	public function supports_theme_enqueuing() {
		return method_exists( 'GFAddOn', 'get_theme_layer_styles' );
	}

	/**
	 * An array of styles to enqueue.
	 *
	 * @since 1.6.0
	 *
	 * @param $form
	 * @param $ajax
	 * @param $settings
	 * @param $block_settings
	 *
	 * @return array|\string[][]
	 */
	public function theme_layer_styles( $form, $ajax, $settings, $block_settings = array() ) {
		return $this->supports_theme_enqueuing() ? $this->get_theme_layer_styles( $form, 'chainedselect' ) : array();
	}

	public function localize_scripts() {

		wp_localize_script( 'gform_chained_selects_admin', 'gformChainedSelectData', array(
			'defaultChoices' => $this->get_default_choices(),
			'defaultInputs'  => $this->get_default_inputs(),
			'fileUploadUrl'  => trailingslashit( site_url() ) . '?gf_page=' . GFCommon::get_upload_page_slug(),
			'maxFileSize' => $this->get_max_file_size(),
			'strings' => array(
				'errorProcessingFile'      => wp_strip_all_tags( __( 'There was an error processing this file.', 'gravityformschainedselects' ) ),
				'errorUploadingFile'       => wp_strip_all_tags( __( 'There was an error uploading this file.', 'gravityformschainedselects' ) ),
				'errorFileType'            => wp_strip_all_tags( __( 'Only CSV files are allowed.', 'gravityformschainedselects' ) ),
				'errorFileSize'            => sprintf( wp_strip_all_tags( __( 'This file is too big. Max file size is %dMB.', 'gravityformschainedselects' ) ), round( $this->get_max_file_size() / 1000000 ) ),
				'importedFilterFile'       => sprintf( wp_strip_all_tags( __( 'This file is imported via %sa filter%s and cannot be modified here.', 'gravityformschainedselects' ) ), '<a href="@todo">', '</a>' ),
				'errorImportingFilterFile' => sprintf( wp_strip_all_tags( __( 'There was an error importing the file via %sthe filter%s.', 'gravityformschainedselects' ) ), '<a href="@todo">', '</a>' ),
			)
		) );

		wp_localize_script( 'gform_chained_selects', 'gformChainedSelectData', array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'gform_get_next_chained_select_choices' ),
			'spinner'     => $this->get_spinner_url(),
			'strings'     => array(
				'loading'   => wp_strip_all_tags( __( 'Loading', 'gravityformschainedselects' ) ),
				'noOptions' => wp_strip_all_tags( __( 'No options', 'gravityformschainedselects' ) ),
			),
		) );

	}

	/**
	 * Returns the URL of the file containing the spinner.
	 *
	 * @since 1.6
	 *
	 * @return string
	 */
	public function get_spinner_url() {
		return GFCommon::get_base_url() . '/images/spinner' . ( $this->is_gravityforms_supported( '2.5' ) ? '.svg' : '.gif' );
	}

	public function get_default_choices() {
		// ids are set in JS based on newly created field
		return  array(
			array(
				'text'       => wp_strip_all_tags( __( 'Parent 1', 'gravityformschainedselects' ) ),
				'value'      => wp_strip_all_tags( __( 'Parent 1', 'gravityformschainedselects' ) ),
				'isSelected' => true,
				'choices' => array(
					array(
						'text'       => wp_strip_all_tags( __( 'Child 1', 'gravityformschainedselects' ) ),
						'value'      => wp_strip_all_tags( __( 'Child 1', 'gravityformschainedselects' ) ),
						'isSelected' => true,
					),
					array(
						'text'       => wp_strip_all_tags( __( 'Child 2', 'gravityformschainedselects' ) ),
						'value'      => wp_strip_all_tags( __( 'Child 2', 'gravityformschainedselects' ) ),
						'isSelected' => false,
					),
					array(
						'text'       => wp_strip_all_tags( __( 'Child 3', 'gravityformschainedselects' ) ),
						'value'      => wp_strip_all_tags( __( 'Child 3', 'gravityformschainedselects' ) ),
						'isSelected' => false,
					)
				)
			),
			array(
				'text'       => wp_strip_all_tags( __( 'Parent 2', 'gravityformschainedselects' ) ),
				'value'      => wp_strip_all_tags( __( 'Parent 2', 'gravityformschainedselects' ) ),
				'isSelected' => false,
				'choices' => array(
					array(
						'text'       => wp_strip_all_tags( __( 'Child 4', 'gravityformschainedselects' ) ),
						'value'      => wp_strip_all_tags( __( 'Child 4', 'gravityformschainedselects' ) ),
						'isSelected' => false,
					),
					array(
						'text'       => wp_strip_all_tags( __( 'Child 5', 'gravityformschainedselects' ) ),
						'value'      => wp_strip_all_tags( __( 'Child 5', 'gravityformschainedselects' ) ),
						'isSelected' => false,
					),
					array(
						'text'       => wp_strip_all_tags( __( 'Child 6', 'gravityformschainedselects' ) ),
						'value'      => wp_strip_all_tags( __( 'Child 6', 'gravityformschainedselects' ) ),
						'isSelected' => false,
					)
				)
			),
			array(
				'text'       => wp_strip_all_tags( __( 'Parent 3', 'gravityformschainedselects' ) ),
				'value'      => wp_strip_all_tags( __( 'Parent 3', 'gravityformschainedselects' ) ),
				'isSelected' => false,
				'choices' => array(
					array(
						'text'       => wp_strip_all_tags( __( 'Child 7', 'gravityformschainedselects' ) ),
						'value'      => wp_strip_all_tags( __( 'Child 7', 'gravityformschainedselects' ) ),
						'isSelected' => false,
					),
					array(
						'text'       => wp_strip_all_tags( __( 'Child 8', 'gravityformschainedselects' ) ),
						'value'      => wp_strip_all_tags( __( 'Child 8', 'gravityformschainedselects' ) ),
						'isSelected' => false,
					),
					array(
						'text'       => wp_strip_all_tags( __( 'Child 9', 'gravityformschainedselects' ) ),
						'value'      => wp_strip_all_tags( __( 'Child 9', 'gravityformschainedselects' ) ),
						'isSelected' => false,
					)
				)
			),
		);
	}

	public function get_default_inputs() {
		return array(
			array(
				'label' => wp_strip_all_tags( __( 'Parents', 'gravityformschainedselects' ) ),
				'id'    => '',
			),
			array(
				'label' => wp_strip_all_tags( __( 'Children', 'gravityformschainedselects' ) ),
				'id'    => '',
			)
		);
	}

	public function get_max_file_size() {
		/**
		 * Filter the max file size for imported Chained Select files.
		 *
		 * @param int $size The max file size in bytes.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'gform_chainedselects_max_file_size', 1000000 ); // 1mb
	}

}
