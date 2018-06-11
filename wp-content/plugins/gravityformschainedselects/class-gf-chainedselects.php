<?php
	
GFForms::include_feed_addon_framework();

class GFChainedSelects extends GFAddOn {
	
	protected $_version = GF_CHAINEDSELECTS_VERSION;
	protected $_min_gravityforms_version = '2.2.2';
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

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array(
			array(
				'handle'  => 'gform_chained_selects_admin',
				'deps'    => array( 'jquery', 'backbone', 'plupload', 'gform_form_admin' ),
				'src'     => $this->get_base_url() . "/js/admin{$min}.js",
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
				'src'     => $this->get_base_url() . "/js/admin-form-editor{$min}.js",
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
				'src'     => $this->get_base_url() . "/js/frontend{$min}.js",
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

		$styles = array(
			array(
				'handle'  => 'gform_chained_selects_admin',
				'src'     => $this->get_base_url() . '/css/admin.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor', 'entry_view' ) ),
				),
			),
			array(
				'handle'  => 'gform_chained_selects',
				'src'     => $this->get_base_url() . '/css/frontend.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'should_enqueue_frontend_script' )
				),
			),
		);

		return array_merge( parent::styles(), $styles );
	}

	public function localize_scripts() {

		wp_localize_script( 'gform_chained_selects_admin', 'gformChainedSelectData', array(
			'defaultChoices' => $this->get_default_choices(),
			'defaultInputs'  => $this->get_default_inputs(),
			'fileUploadUrl'  => trailingslashit( site_url() ) . '?gf_page=' . GFCommon::get_upload_page_slug(),
			'maxFileSize' => $this->get_max_file_size(),
			'strings' => array(
				'errorProcessingFile'      => esc_html__( 'There was an error processing this file.', 'gravityformschainedselects' ),
				'errorUploadingFile'       => esc_html__( 'There was an error uploading this file.', 'gravityformschainedselects' ),
				'errorFileType'            => esc_html__( 'Only CSV files are allowed.', 'gravityformschainedselects' ),
				'errorFileSize'            => sprintf( esc_html__( 'This file is too big. Max file size is %dMB.', 'gravityformschainedselects' ), round( $this->get_max_file_size() / 1000000 ) ),
				'importedFilterFile'       => sprintf( esc_html__( 'This file is imported via %sa filter%s and cannot be modified here.', 'gravityformschainedselects' ), '<a href="@todo">', '</a>' ),
				'errorImportingFilterFile' => sprintf( esc_html__( 'There was an error importing the file via %sthe filter%s.', 'gravityformschainedselects' ), '<a href="@todo">', '</a>' ),
			)
		) );

		wp_localize_script( 'gform_chained_selects', 'gformChainedSelectData', array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'gform_get_next_chained_select_choices' ),
			'spinner'     => GFCommon::get_base_url() . '/images/spinner.gif',
			'strings'     => array(
				'loading'   => __( 'Loading', 'gravityformschainedselects' ),
				'noOptions' => __( 'No options', 'gravityformschainedselects' ),
			),
		) );

	}

	public function get_default_choices() {
		// ids are set in JS based on newly created field
		return  array(
			array(
				'text'       => __( 'Parent 1', 'gravityformschainedselects' ),
				'value'      => __( 'Parent 1', 'gravityformschainedselects' ),
				'isSelected' => true,
				'choices' => array(
					array(
						'text'       => __( 'Child 1', 'gravityformschainedselects' ),
						'value'      => __( 'Child 1', 'gravityformschainedselects' ),
						'isSelected' => true,
					),
					array(
						'text'       => __( 'Child 2', 'gravityformschainedselects' ),
						'value'      => __( 'Child 2', 'gravityformschainedselects' ),
						'isSelected' => false,
					),
					array(
						'text'       => __( 'Child 3', 'gravityformschainedselects' ),
						'value'      => __( 'Child 3', 'gravityformschainedselects' ),
						'isSelected' => false,
					)
				)
			),
			array(
				'text'       => __( 'Parent 2', 'gravityformschainedselects' ),
				'value'      => __( 'Parent 2', 'gravityformschainedselects' ),
				'isSelected' => false,
				'choices' => array(
					array(
						'text'       => __( 'Child 4', 'gravityformschainedselects' ),
						'value'      => __( 'Child 4', 'gravityformschainedselects' ),
						'isSelected' => false,
					),
					array(
						'text'       => __( 'Child 5', 'gravityformschainedselects' ),
						'value'      => __( 'Child 5', 'gravityformschainedselects' ),
						'isSelected' => false,
					),
					array(
						'text'       => __( 'Child 6', 'gravityformschainedselects' ),
						'value'      => __( 'Child 6', 'gravityformschainedselects' ),
						'isSelected' => false,
					)
				)
			),
			array(
				'text'       => __( 'Parent 3', 'gravityformschainedselects' ),
				'value'      => __( 'Parent 3', 'gravityformschainedselects' ),
				'isSelected' => false,
				'choices' => array(
					array(
						'text'       => __( 'Child 7', 'gravityformschainedselects' ),
						'value'      => __( 'Child 7', 'gravityformschainedselects' ),
						'isSelected' => false,
					),
					array(
						'text'       => __( 'Child 8', 'gravityformschainedselects' ),
						'value'      => __( 'Child 8', 'gravityformschainedselects' ),
						'isSelected' => false,
					),
					array(
						'text'       => __( 'Child 9', 'gravityformschainedselects' ),
						'value'      => __( 'Child 9', 'gravityformschainedselects' ),
						'isSelected' => false,
					)
				)
			),
		);
	}

	public function get_default_inputs() {
		return array(
			array(
				'label' => __( 'Parents', 'gravityformschainedselects' ),
				'id'    => '',
			),
			array(
				'label' => __( 'Children', 'gravityformschainedselects' ),
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