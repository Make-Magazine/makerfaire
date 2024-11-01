<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Copy_Cat extends GP_Plugin {

	private static $_instance = null;

	protected $_version     = GP_COPY_CAT_VERSION;
	protected $_path        = 'gwcopycat/gwcopycat.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-copy-cat';
	protected $_title       = 'Gravity Forms Copy Cat';
	protected $_short_title = 'Copy Cat';

	public static function get_instance() {
		if( self::$_instance == null ) {
			self::$_instance = isset ( self::$perk ) ? new self ( new self::$perk ) : new self();
		}
		return self::$_instance;
	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '1.9.3',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '1.0.6',
				),
			),
		);
	}	

	public function init() {
		parent::init();

		load_plugin_textdomain( 'gp-copy-cat', false, basename( dirname( __file__ ) ) . '/languages/' );

		add_action( 'gform_register_init_scripts', array( $this, 'register_init_scripts' ) );
		add_filter( 'gform_pre_render', array( $this, 'modify_frontend_form' ) );

	}

	public function register_init_scripts( $form ) {

		if ( ! $this->has_copy_cat_field( $form ) ) {
			return;
		}

		$enable_overwrite = gf_apply_filters( array( 'gpcc_overwrite_existing_values', $form['id'] ), true, $form ); /* @since 1.3.7 Used to default to false */
		/**
		 * Enable overwriting target values when form is initialized.
		 *
		 * By default, source values will not overwrite target values when the form is initialized. This means data that
		 * is pre-populated into target fields will take precedence over values copied from source fields.
		 *
		 * @since 1.4.15
		 *
		 * @param bool  $enable_overwrite_on_init Whether to overwrite target values when the form renders.
		 * @param array $form                     The current form object.
		 */
		$enable_overwrite_on_init = gf_apply_filters( array( 'gpcc_overwrite_existing_values_on_init', $form['id'] ), false, $form );

		$args = array(
			'formId'          => $form['id'],
			'fields'          => $this->get_copy_cat_fields( $form ),
			'overwrite'       => $enable_overwrite,
			'overwriteOnInit' => $enable_overwrite_on_init,
		);

		$script = 'new gwCopyObj( ' . json_encode( $args ) . ' );';

		GFFormDisplay::add_init_script( $form['id'], 'gp-copy-cat', GFFormDisplay::ON_PAGE_RENDER, $script );

	}

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array(
			// Frontend
			array(
				'handle'    => 'gp-copy-cat-frontend',
				'src'       => $this->get_base_url() . '/js/gp-copy-cat' . $min . '.js',
				'version'   => $this->_version,
				'deps'      => array(
					'jquery',
					'gform_gravityforms',
				),
				'enqueue' => array(
					array( $this, 'has_copy_cat_field' ),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	public function modify_frontend_form( $form ) {

		if ( ! $this->has_copy_cat_field( $form ) ) {
			return $form;
		}

		$copy_field_ids = array_keys( $this->get_copy_cat_fields( $form ) );

		foreach ( $form['fields'] as &$field ) {

			if ( in_array( $field['id'], $copy_field_ids ) ) {
				$field->cssClass .= ' gwcopy';
			}

			// Set maxrows class for use in script.
			if ( $field->get_input_type() == 'list' ) {
				$field->cssClass .= sprintf( ' gp-field-maxrows-%d', $field->maxRows );
			}
		}

		return $form;
	}

	function has_copy_cat_field( $form ) {
		$copy_fields = $this->get_copy_cat_fields( $form );
		return ! empty( $copy_fields );
	}

	function get_copy_cat_fields( $form ) {

		$copy_fields = array();

		if ( ! $form ) {
			return $copy_fields;
		}

		foreach ( $form['fields'] as &$field ) {

			preg_match_all( '/copy-([0-9]+(?:.[0-9]+)*)-to-([0-9]+(?:.[0-9]+)*)(?:-if-([0-9]+(?:.[0-9]+)*))?/', $field['cssClass'], $matches, PREG_SET_ORDER );
			if ( empty( $matches ) ) {
				continue;
			}

			foreach ( $matches as $match ) {

				list( $class, $source_field_id, $target_field_id ) = $match;

				$source_form_id       = $form['id'];
				$target_form_id       = $form['id'];
				$if_condition_form_id = $form['id'];

				$source_bits  = explode( '.', $source_field_id );
				$source_field = GFFormsModel::get_field( $form, intval( $source_field_id ) );
				if ( $source_field == null ) {
					continue;
				}

				if ( count( $source_bits ) == 3 && $source_field->get_input_type() != 'list' ) {
					$source_form_id  = array_shift( $source_bits );
					$source_field_id = $source_bits[1] == 0 ? $source_bits[0] : implode( '.', $source_bits );
				}

				$target_bits  = explode( '.', $target_field_id );
				$target_field = GFFormsModel::get_field( $form, intval( $target_field_id ) );
				if ( $target_field == null || $target_field_id == $field->id ) {
					continue;
				}

				if ( count( $target_bits ) == 3 && $target_field->get_input_type() != 'list' ) {
					$target_form_id  = array_shift( $target_bits );
					$target_field_id = $target_bits[1] == 0 ? $target_bits[0] : implode( '.', $target_bits );
				}

				$if_condition_field_id = null;

				if ( isset( $match[3] ) ) {
					$if_condition_field_id = $match[3];

					$if_condition_pieces = explode( '.', $if_condition_field_id );
					if ( count( $if_condition_pieces ) == 3 && $source_field->get_input_type() != 'list' ) {
						$if_condition_form_id  = array_shift( $if_condition_pieces );
						$if_condition_field_id = $if_condition_pieces[1] == 0 ? $if_condition_pieces[0] : implode( '.', $if_condition_pieces );
					}
	
					$if_condition_field = null;
					if ( ! empty( $if_condition_field_id ) ) {
						$if_condition_field = GFFormsModel::get_field( $form, intval( $if_condition_field_id ) );
					}
	
					if ( empty( $if_condition_field ) ) {
						$if_condition_field_id = null;
					}
				}

				$copy_fields[ $field['id'] ][] = array(
					'source'          => $source_field_id,
					'target'          => $target_field_id,
					'condition'       => $if_condition_field_id,
					'sourceFormId'    => $source_form_id,
					'targetFormId'    => $target_form_id,
					'conditionFormId' => $if_condition_form_id,
					'trigger'         => $field['id'],
				);

			}
		}

		/**
		 * Modify the data that will be passed to the Copy Cat script on the frontend.
		 *
		 * @since 1.4.3
		 *
		 * @param array $copy_fields {
		 *     An array of data that dictates which fields should be copied to which.
		 *
		 *     @type int|string $source       The field ID from which the value will be retrieved.
		 *     @type int|string $target       The field ID to which the value will be copied.
		 *     @type int        $sourceFormId The ID of the form from which the value will be retrieved.
		 *     @type int        $targetFormId The ID of the form to which the value will be copied.
		 * }
		 * @param array $form The current form object.
		 */
		return gf_apply_filters( array( 'gpcc_copy_cat_fields', $form['id'] ), $copy_fields, $form );
	}


}

class GWCopyCat extends GP_Copy_Cat { }

GFAddOn::register( 'GP_Copy_Cat' );
