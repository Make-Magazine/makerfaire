<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Read_Only extends GP_Plugin {

	private static $_instance = null;

	protected $_version     = GP_READ_ONLY_VERSION;
	protected $_path        = 'gwreadonly/gwreadonly.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-read-only';
	protected $_title       = 'Gravity Forms Read Only';
	protected $_short_title = 'Read Only';

	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = isset ( self::$perk ) ? new self ( new self::$perk ) : new self();
		}
		return self::$_instance;
	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.4',
			),
			'wordpress'    => array(
				'version' => '3.0',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '1.0-beta-3',
				),
			),
		);
	}

	private $unsupported_field_types  = array( 'hidden', 'html', 'captcha', 'page', 'section', 'form', 'fileupload' );
	private $disable_attr_field_types = array( 'radio', 'select', 'checkbox', 'multiselect', 'time', 'date', 'name', 'address', 'workflow_user', 'workflow_role', 'workflow_assignee_select', 'consent' );

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gwreadonly', false, basename( dirname( __file__ ) ) . '/languages/' );

		$this->perk->enqueue_field_settings();

		// Actions
		add_action( 'gperk_field_settings', array( $this, 'field_settings_ui' ) );
		add_action( 'gform_editor_js', array( $this, 'field_settings_js' ) );

		// Filters
		add_filter( 'gform_field_input', array( $this, 'read_only_input' ), 11, 5 );

		add_filter( 'gform_pre_process', array( $this, 'process_hidden_captures' ), 11, 1 );

		add_filter( 'gform_rich_text_editor_options', array( $this, 'filter_rich_text_editor_options' ), 10, 2 );

		// Add support for Gravity View since `gform_pre_process` never fires in GV's edit path.
		add_action( 'gravityview_edit_entry', array( $this, 'process_hidden_captures_gravityview' ), 5, 4 );

		/**
		 * Add support for Gravity Flow's User Input step
		 *
		 * The user input step does not seem to fire the standard form submission's `gform_pre_process` hook.
		 * Here we attempt to intercept validation but only in the `in_progress`/`complete` states which indicate
		 * that an entry is being updated.
		 */
		if ( class_exists( 'Gravity_Flow' ) && in_array( rgpost( 'gravityflow_status' ), array( 'in_progress', 'complete' ) ) ) {
			add_filter( 'gform_pre_validation', function ( $form ) {
				return $this->process_hidden_captures( $form );
			}, 5, 1 );
		}

		/**
		 * Stripe Payment Elements compatibility
		 *
		 * This is a bizarre hook to use, but it's the only decent one I could find when the temporary lead is created
		 * with GFFormsModel::create_lead().
		 */
		add_filter( 'gform_currency_pre_save_entry', function( $currency, $form ) {
			if ( rgpost( 'action' ) !== 'gfstripe_validate_form' ) {
				return $currency;
			}

			$this->process_hidden_captures( $form );

			return $currency;
		}, 10, 2 );

	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$styles = array(

			array(
				'handle'  => 'gwreadonly',
				'src'     => $this->get_base_url() . '/css/gwreadonly.css',
				'version' => $this->_version,
				'enqueue' => array(
					array(
						array( $this, 'should_enqueue_frontend' ),
					),
				),
			),
		);

		return array_merge( parent::styles(), $styles );
	}	

	/**
	 * Determine if frontend scripts/styles should be enqueued. Loop through fields and check if read only is enabled
	 * on any field.
	 *
	 * @param $form
	 *
	 * @return bool
	 */
	public function should_enqueue_frontend( $form ) {
		if ( GFCommon::is_form_editor() ) {
			return false;
		}

		return $this->is_applicable_form( $form );
	}

	/**
	 * @param $form
	 *
	 * @return boolean Whether this form has read only forms.
	 */
	public function is_applicable_form( $form ) {
		return ! empty( $this->get_readonly_fields( $form ) );
	}

	/**
	 * @param $form
	 *
	 * @return GF_Field[] List of fields that are read-only.
	 */
	public function get_readonly_fields( $form ) {
		if ( empty( $form['fields'] ) ) {
			return array();
		}

		$fields = array();

		foreach ( $form['fields'] as $field ) {
			if ( $this->is_readonly_field( $field ) ) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * @param GF_Field $field
	 *
	 * @return boolean
	 */
	public function is_readonly_field( $field ) {
		$input_type = RGFormsModel::get_input_type( $field );

		if ( in_array( $input_type, $this->unsupported_field_types ) ) {
			return false;
		}

		return ! ! rgar( $field, $this->perk->key( 'enable' ) );
	}

	public function field_settings_ui() {
		?>

		<li class="<?php echo $this->perk->key( 'field_setting' ); ?> field_setting" style="display:none;">
			<input type="checkbox" id="<?php echo $this->perk->key( 'field_checkbox' ); ?>" value="1" onclick="SetFieldProperty('<?php echo $this->perk->key( 'enable' ); ?>', this.checked)">

			<label class="inline" for="<?php echo $this->perk->key( 'field_checkbox' ); ?>">
				<?php _e( 'Read-only', 'gravityperks' ); ?>
				<?php gform_tooltip( $this->perk->key( 'readonly' ) ); ?>
			</label>
		</li>

		<?php
	}

	public function field_settings_js() {
		?>

		<script type="text/javascript">

			(function($) {

				$(document).ready(function(){

					for(i in fieldSettings) {
						if(isReadOnlyFieldType(i))
							fieldSettings[i] += ', .gwreadonly_field_setting';
					}

				});

				$(document).bind('gform_load_field_settings', function(event, field, form) {
					$("#<?php echo $this->perk->key( 'field_checkbox' ); ?>").prop( 'checked', field["<?php echo $this->perk->key( 'enable' ); ?>"] === true );

					// If calculation is enabled, we typically don't need this Perk since the input will be read-only
					// However, in the case of the product field with a quantity field, the quantity field won't
					// be read-only.
					if( ! isReadOnlyFieldType( GetInputType( field ) ) || (isCalcEnabled( field ) && field.type !== 'product') ) {
						field["<?php echo $this->perk->key( 'enable' ); ?>"] = false;
						$('.gwreadonly_field_setting').hide();
					}
				});

				function isReadOnlyFieldType(type) {
					var unsupportedFieldTypes = <?php echo json_encode( $this->unsupported_field_types ); ?>;
					return $.inArray(type, unsupportedFieldTypes) != -1 ? false : true;
				}

				function isCalcEnabled( field ) {
					return field.enableCalculation == true || GetInputType( field ) == 'calculation';
				}

			})(jQuery);

		</script>

		<?php
	}

	public function read_only_input( $input_html, $field, $value, $entry_id, $form_id ) {

		$form  = GFAPI::get_form( $form_id );
		$entry = GFAPI::get_entry( $entry_id );

		if ( $field->is_entry_detail() || GFCommon::is_form_editor() ) {
			return $input_html;
		}

		$input_type = RGFormsModel::get_input_type( $field );
		if ( in_array( $input_type, $this->unsupported_field_types ) || ! rgar( $field, $this->perk->key( 'enable' ) ) ) {
			return $input_html;
		}

		remove_filter( 'gform_field_input', array( $this, 'read_only_input' ), 11, 5 );

		$input_html = GFCommon::get_field_input( $field, $value, $entry_id, $form_id, GFAPI::get_form( $form_id ) );

		switch ( $input_type ) {
			case 'textarea':
			case 'post_content':
			case 'post_excerpt':
			case 'workflow_discussion': // @gravityflow
				$search  = '<textarea';
				$replace = $search . " readonly='readonly'";
				break;
			case 'multiselect':
			case 'select':
			case 'workflow_user': // @gravityflow
			case 'workflow_role': // @gravityflow
			case 'workflow_assignee_select': // @gravityflow
				$search  = '<select';
				$replace = $search . " disabled='disabled'";
				break;
			case 'radio':
			case 'checkbox':
				$search  = '<input';
				$replace = $search . " disabled='disabled'";
				break;
			case 'time':
			case 'address':
			case 'name':
			case 'date':
				$search = array(
					'<input'  => "<input readonly='readonly'",
					'<select' => "<select disabled='disabled'",
				);
				break;
			case 'list':
				// Remove add/remove buttons.
				$input_html = preg_replace( '/<(?:td|div) class=\'gfield_list_icons(?: gform-grid-col)?\'>[\s\S]+?<\/(?:td|div)>/', '', $input_html );
				// Remove add/remove column header.
				$input_html = str_replace( '<div class="gfield_header_item gfield_header_item--icons">&nbsp;</div>', '', $input_html );
				$search     = array(
					'<input'  => "<input readonly='readonly'",
					'<select' => "<select disabled='disabled'",
				);
				break;
			case 'signature':
				$input_html = preg_replace( '/<a href=[\'"]#[\'"].*?signature_image.*?>.*?<\/a>/', '', $input_html ); // Remove sign again button

				if ( rgblank( $value ) ) {
					$input_html = preg_replace( '/<div ((style)|(class)=\'.*\')?\s*?><div id=\'input_' . $form_id . '_' . $field->id . '_Container\' .*?>.*?<\/div><\/div>/', '<div style="display: none;"></div><!-- GPRO placeholder -->', $input_html ); // Remove HTML that contains the canvas
				}

				$search  = '<input';
				$replace = $search . " readonly='readonly'";
				break;
			case 'consent':
				$search  = "type='checkbox'";
				$replace = $search . " disabled='disabled'";
				break;
			default:
				$search  = '<input';
				$replace = $search . " readonly='readonly'";
				break;
		}

		if ( ! is_array( $search ) ) {
			$search = array( $search => $replace );
		}

		if ( $input_type == 'date' && $field->dateType == 'datepicker' ) {
			/**
			 * Disable the datepicker for read-only Datepicker fields.
			 *
			 * @since 1.2.13
			 *
			 * @param bool          $is_disabled Whether or not to disable the datepicker for this read-only input.
			 * @param GF_Field_Date $field       GF_Field_Date The current Date field object.
			 * @param int           $entry_id    The current entry ID; 0 when no entry ID is provided.
			 */
			$disable_datepicker = gf_apply_filters( array( 'gpro_disable_datepicker', $form_id, $field->id ), true, $field, $entry_id );
			if ( $disable_datepicker ) {
				// Find 'datepicker' and 'gform-datepicker' CSS class and replace it with our custom class indicating that we've disabled it.
				// This class is used by Conditional Logic Dates to identify read-only Datepicker fields.
				$search['class=\'datepicker gform-datepicker '] = 'class=\'gpro-disabled-datepicker ';

				// Replace only 'datepicker' class for older GF versions.
				$search['class=\'datepicker ']  = 'class=\'gpro-disabled-datepicker ';
			}
		}

		foreach ( $search as $_search => $replace ) {
			$input_html = str_replace( $_search, $replace, $input_html );
		}

		// add hidden capture input markup for disabled field types
		if ( in_array( $input_type, $this->disable_attr_field_types ) ) {

			// Use $value if we have it as it'll likely be from dynamic population (e.g. query param or shortcode).
			$value           = ! rgblank( $value ) ? $value : $this->get_field_value( $form, $entry, $field['id'] );
			$hc_input_markup = '';

			if ( is_array( $field['inputs'] ) ) {

				switch ( $input_type ) {
					case 'time':
						// Check if $value is an array or a string that needs parsing.
						if ( ! is_array( $value ) && strpos( $value, ' ' ) !== false ) {
							$value = $this->parse_time_string( $value );
						}

						$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.3', is_array( $value ) ? array_pop( $value ) : $value );
						break;
					case 'date':
						switch ( rgar( $field, 'dateFormat' ) ) {
							case 'mdy':
								$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.1', rgar( $value, 'm' ) );
								$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.2', rgar( $value, 'd' ) );
								$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.3', rgar( $value, 'y' ) );
								break;
							case 'dmy':
								$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.1', rgar( $value, 'd' ) );
								$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.2', rgar( $value, 'm' ) );
								$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.3', rgar( $value, 'y' ) );
								break;
							case 'ymd':
								$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.1', rgar( $value, 'y' ) );
								$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.2', rgar( $value, 'm' ) );
								$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.3', rgar( $value, 'd' ) );
								break;
						}

						break;
					case 'address':
						$input_id         = sprintf( '%d.%d', $field->id, $this->get_address_select_input_id( $field ) );
						$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $input_id, rgar( $value, $input_id ) );
						break;
					default:
						foreach ( $field['inputs'] as $input ) {
							$hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $input['id'], $value );
						}
				}
			} else {

				$hc_input_markup = $this->get_hidden_capture_markup( $form_id, $field->id, $value );

			}

			// Check if there's a closing div tag
			if ( strpos( $input_html, '</div>' ) !== false ) {
				// Append GPRO hidden input before last closing div tag.
				// This ensures that GPPA will replace the hidden GPRO input during XHR requests.
				$input_html = preg_replace( '/<\/div>(?!\s*<\/?div>?\s*)(.*)/', str_replace( '$', '\$', $hc_input_markup ) . '</div>$1', $input_html );
			} else {
				// No closing div tag, append GPRO hidden input to the end
				$input_html .= $hc_input_markup;
			}
		}

		add_filter( 'gform_field_input', array( $this, 'read_only_input' ), 11, 5 );

		return $input_html;
	}

	private function parse_time_string( $value ) {
	    // Use a regular expression to match the time format
	    if ( preg_match( '/^(\d{1,2}):(\d{2})(?:\s?(AM|PM))?$/i', $value, $matches ) ) {
	        $hours   = $matches[1];
	        $minutes = $matches[2];
	        $ampm    = isset( $matches[3] ) ? strtoupper( $matches[3] ) : null; // AM/PM or null if not present
	        return array( $hours, $minutes, $ampm );
	    }

	    // Return the original value if it doesn't match the expected format
	    return $value;
	}

	public function get_hidden_capture_input_id( $form_id, $input_id ) {

		if ( intval( $input_id ) != $input_id ) {
			$input_id_bits               = explode( '.', $input_id );
			list( $field_id, $input_id ) = $input_id_bits;
			$hc_input_id                 = sprintf( 'gwro_hidden_capture_%d_%d_%d', $form_id, $field_id, $input_id );
		} else {
			$hc_input_id = sprintf( 'gwro_hidden_capture_%d_%d', $form_id, $input_id );
		}

		return $hc_input_id;
	}

	public function get_hidden_capture_markup( $form_id, $input_id, $value ) {

		$hc_input_id = $this->get_hidden_capture_input_id( $form_id, $input_id );

		$field = GFAPI::get_field( $form_id, $input_id );

		if ( is_array( $value ) && ! empty( $field->inputs ) ) {
			$value = rgar( $value, (string) $input_id );
		} elseif ( is_array( $value ) ) {
			$value = json_encode( $value );
		}

		return sprintf( '<input type="hidden" id="%s" name="%s" value="%s" class="gf-default-disabled" />', $hc_input_id, $hc_input_id, esc_attr( $value ) );
	}

	public function process_hidden_captures( $form ) {

		/**
		 * In some instances (i.e. parent submission of Nested Forms), the gform_pre_process filter may be applied to a
		 * form that is not currently being submitted. Let's make sure we're only working with the submitted form.
		 * Update: We also need a second check here for Gravity Flow as they use `gravityflow_submit` instead. HS#27204
		 */
		if ( rgpost( 'gform_submit' ) != $form['id'] && rgpost( 'gravityflow_submit' ) != $form['id'] ) {
			return $form;
		}

		foreach ( $_POST as $key => $value ) {

			if ( strpos( $key, 'gwro_hidden_capture_' ) !== 0 ) {
				continue;
			}

			// gets 481, 5, & 1 from a string like "gwro_hidden_capture_481_5_1"
			list( $form_id, $field_id, $input_id ) = array_pad( explode( '_', str_replace( 'gwro_hidden_capture_', '', $key ) ), 3, false );

			$field = GFFormsModel::get_field( $form, $field_id );
			switch ( $field->get_input_type() ) {
				// time fields are in array format in the POST
				case 'time':
					$full_input_id = $field_id;
					$full_value    = rgpost( "input_{$full_input_id}" );

					if ( ! is_array( $full_value ) ) {
						break;
					}

					$full_value[] = $value;
					$value        = $full_value;
					break;
				// date drop downs are in array format in the POST
				case 'date':
					$full_input_id = $field_id;
					$full_value    = array(
						rgpost( 'gwro_hidden_capture_' . $form_id . '_' . $field_id . '_1' ),
						rgpost( 'gwro_hidden_capture_' . $form_id . '_' . $field_id . '_2' ),
						rgpost( 'gwro_hidden_capture_' . $form_id . '_' . $field_id . '_3' ),
					);

					if ( count( array_filter( $full_value ) ) !== 3 ) {
						break;
					}

					$value = $full_value;
					break;
				default:
					// gets "5_1" from an array like array( 5, 1 ) or "5" from an array like array( 5, false )
					$full_input_id = implode( '_', array_filter( array( $field_id, $input_id ) ) );
			}

			// Only use hidden capture if $_POST does not already contain a value for this inputs;
			// this allows support for checking/unchecking via JS (i.e. checkbox fields).
			if ( $this->is_empty_hidden_capture( $full_input_id, $field ) && $value ) {
				if ( method_exists( 'GFCommon', 'is_json' ) && is_string( $value ) ) {
					$stripped_slashes_value = stripslashes( $value );

					if ( GFCommon::is_json( $stripped_slashes_value ) ) {
						$value = GFCommon::maybe_decode_json( $stripped_slashes_value );
					}
				}

				$_POST[ "input_{$full_input_id}" ] = $value;
			}
		}

		return $form;
	}

	private function is_empty_hidden_capture( $full_input_id, $field ) {
		// For a time field, ensure all 3 values are stored (Hours - Minutes - AM/PM).
		if ( $field->type == 'time' && is_array( $_POST[ "input_{$full_input_id}" ] ) && count( $_POST[ "input_{$full_input_id}" ] ) != 3 ) {
			return true;
		}

		if ( empty( $_POST[ "input_{$full_input_id}" ] ) ) {
			return true;
		}

		return false;
	}

	public function process_hidden_captures_gravityview( $_, $entry, $view, $request ) {
		if ( ! wp_verify_nonce( rgpost( 'is_gv_edit_entry' ), 'is_gv_edit_entry' ) ) {
			return;
		}

		$form = GFAPI::get_form( $entry['form_id'] );
		$this->process_hidden_captures( $form );
	}

	public function get_field_value( $form, $entry, $field_id ) {

		$field        = GFAPI::get_field( $form, $field_id );
		$field_values = $submitted_values = false;
		$entry = null;

		if ( isset( $_GET['gf_token'] ) ) {
			$incomplete_submission_info = GFFormsModel::get_draft_submission_values( $_GET['gf_token'] );
			if ( $incomplete_submission_info['form_id'] == $field['formId'] ) {
				$submission_details_json = $incomplete_submission_info['submission'];
				$submission_details      = json_decode( $submission_details_json, true );
				$submitted_values        = $submission_details['submitted_values'];
				$field_values            = $submission_details['field_values'];
			}
		}

		if ( function_exists( 'gravityview' ) && gravityview()->request->is_edit_entry() ) {
			$entry = gravityview()->request->is_edit_entry()->as_entry();
			$value = rgar( $entry, $field->id );
		} elseif (
			method_exists( 'GP_Entry_Blocks\GF_Queryer', 'attach_to_current_block' )
			&& GP_Entry_Blocks\GF_Queryer::attach_to_current_block()
			&& GP_Entry_Blocks\GF_Queryer::attach_to_current_block()->is_edit_entry()
		) {
			$entry = GP_Entry_Blocks\GF_Queryer::attach_to_current_block()->entry;
			$value = rgar( $entry, $field->id );
		} elseif ( is_array( $submitted_values ) ) {
			$value = $submitted_values[ $field->id ];
		} else {
			$value = $field->get_value_default_if_empty( GFFormsModel::get_field_value( $field, $field_values ) );
		}

		$choices = (array) rgar( $field, 'choices' );
		$choices = array_filter( $choices );

		// Use GPPA hydrated value if current value is empty and gppa-values is enabled
		if ( rgar( $field, 'gppa-values-enabled', false ) && GFCommon::is_empty_array( $value ) ) {
			$value = $field->gppa_hydrated_value;
		}
		if ( ! $value && $field->get_input_type() == 'time' ) {

		}
		// if value is not available from post or prepop, check the choices (if field has choices)
		elseif ( ! $value && ! empty( $choices ) ) {

			$values = array();
			$index  = 1;

			foreach ( $choices as $choice ) {

				if ( $index % 10 == 0 ) {
					$index++;
				}

				$full_input_id = sprintf( '%d.%d', $field['id'], $index );

				if ( $entry ) {
					$values[ $full_input_id ] = rgar( $entry, $full_input_id );
				} else if ( rgar( $choice, 'isSelected' ) ) {
					$values[ $full_input_id ] = $this->get_choice_value( $choice, $field );
				}

				$index++;

			}

			$input_type = GFFormsModel::get_input_type( $field );

			// if no choice is preselected and this is a select, get the first choice's value since it will be selected by default in the browser if there is no placeholder.
			if ( empty( $values ) && in_array( $input_type, array( 'select', 'workflow_user', 'workflow_role', 'workflow_assignee_select' ) ) && ! $field->placeholder ) {
				$choice = reset( $choices );

				$values[] = $this->get_choice_value( $choice, $field );
			}

			switch ( $input_type ) {
				case 'multiselect':
					$value = implode( ',', $values );
					break;
				case 'checkbox':
					$value = $values;
					break;
				default:
					$value = reset( $values );
					break;
			}
		}

		return $value;
	}

	public function get_choice_value( $choice, $field ) {
		$price = rgempty( 'price', $choice ) ? 0 : GFCommon::to_number( rgar( $choice, 'price' ) );

		return in_array( $field['type'], array( 'product', 'option', 'shipping' ) ) ? sprintf( '%s|%s', $choice['value'], $price ) : $choice['value'];
	}

	public function filter_rich_text_editor_options( $settings, $field ) {

		if ( $field->gwreadonly_enable ) {
			$settings['tinymce']['init_instance_callback'] = str_replace( 'function (editor) {', 'function (editor) { editor.setMode( "readonly" );', $settings['tinymce']['init_instance_callback'] );
		}

		return $settings;
	}

	public function get_address_select_input_id( $field ) {
		$input_id = false;
		switch ( $field->addressType ) {
			// US, Canadian, and any added using https://docs.gravityforms.com/gform_address_types/
			default:
				$input_id = 4;
				break;
			case 'international':
				$input_id = 6;
				break;
		}
		return $input_id;
	}

	/**
	 * Adds tooltips for the settings.
	 *
	 * @param array $tooltips An array with the existing tooltips.
	 */
	public function tooltips( $tooltips ) {
		$tooltips[ $this->perk->key( 'readonly' ) ] = __( '<h6>Read-only</h6> Set field as "readonly". Read-only fields will be visible on the form but cannot be modified by the user.', 'gravityperks' );
		return $tooltips;
	}

}

class GWReadOnly extends GP_Read_Only { };

GFAddOn::register( 'GP_Read_Only' );
