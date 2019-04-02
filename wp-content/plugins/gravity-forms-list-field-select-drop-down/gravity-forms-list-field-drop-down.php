<?php
/*
Plugin Name: Drop Down List Field for Gravity Forms
Description: Gives the option of adding a drop down (select) list to a list field column
Version: 1.8.2
Author: Adrian Gordon
Author URI: https://www.itsupportguides.com
License: GPL2
Text Domain: gravity-forms-list-field-select-drop-down

------------------------------------------------------------------------
Copyright 2015 Adrian Gordon

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

load_plugin_textdomain( 'gravity-forms-list-field-select-drop-down', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

add_action('admin_notices', array('ITSG_GF_List_Field_Drop_Down', 'admin_warnings'), 20);

if ( !class_exists('ITSG_GF_List_Field_Drop_Down') ) {
    class ITSG_GF_List_Field_Drop_Down {
		private static $name = 'Drop Down List Field for Gravity Forms';
		private static $slug = 'gravity-forms-list-field-select-drop-down';

		/**
         * Construct the plugin object
         */
		 public function __construct() {
			// register plugin functions through 'gform_loaded' -
			// this delays the registration until Gravity Forms has loaded, ensuring it does not run before Gravity Forms is available.
			add_action( 'gform_loaded', array( $this, 'register_actions' ) );
		}

		/*
        * Register plugin functions
        */
		function register_actions() {
			// register actions
			// addon framework
			require_once( plugin_dir_path( __FILE__ ).'gravity-forms-list-field-drop-down-addon.php' );

			// start the plugin
			add_filter( 'gform_column_input', array( $this, 'change_column_input' ), 10, 5 );
			add_filter( 'gform_column_input_content', array( $this, 'change_column_content' ), 99, 6 );

			add_action( 'gform_field_standard_settings', array( $this, 'single_field_settings' ) , 10, 2 );
			add_filter( 'gform_tooltips', array( $this, 'field_tooltips' ) );

			// patch to allow JS and CSS to load when loading forms through wp-ajax requests
			add_action( 'gform_enqueue_scripts', array( $this, 'enqueue_scripts' ), 90, 2 );

			add_filter( 'gform_pre_render', array( $this, 'add_user_option' ), 10, 1 );
			add_filter( 'gform_pre_validation', array( $this, 'add_user_option' ), 10, 1 );
			add_filter( 'gform_admin_pre_render', array( $this, 'add_user_option' ), 10, 1 );
			add_filter( 'gform_pre_submission_filter', array( $this, 'add_user_option' ), 10, 1 );

			add_filter( 'gform_get_input_value', array( $this, 'display_dropdown_value' ), 10, 4 );
		}

		// ensures that the drop down LABEL is displayed - the VALUE is passed in the POST and stored by GF so we do a lookup to get the LABEL from the VALUE
		function display_dropdown_value( $value, $entry, $field, $input_id ) {
			if ( $value ) {
				$is_entry_detail = GFCommon::is_entry_detail();
				if ( !( $is_entry_detail && 'edit' == rgpost( 'screen_mode' ) ) && is_object( $field ) && 'list' == $field->get_input_type() ) {
					$has_columns = is_array( $field->choices );
					$list_values = unserialize( $value );
					if ( ! empty( $list_values ) ) {
						$form_id = $entry['form_id'];
						$field_id = $field->id;
						$submit_value = array(); // we'll be storing the row in here for passing later
						if ( $has_columns ) {
							foreach ( $list_values as $row ) { // get each row
								foreach ( $field->choices as $key => $choice ) { // for each column
									$column = rgars( $field->choices, "{$key}/text" ); // we'll be using the column label as the key
									$isDropDown = rgar( $choice, 'isDropDown' );
									$isDropDownEnableChoiceValue = rgar( $choice, 'isDropDownEnableChoiceValue' );

									if ( $isDropDown && $isDropDownEnableChoiceValue ) {
										$choices = rgar( $choice, 'isDropDownChoices' );
										foreach ( $choices as $ch ) { // get each row
											if ( rgar( $row, $column ) == $ch['value'] ) {
												$row[ $column ] = $ch['text'];
											}
										}

									}
								}
								array_push( $submit_value, $row ); // add row to submit value array
							}
						} else {
							foreach ( $list_values as $key => $value ) { // get each row
								$isDropDown = $field->itsg_list_field_drop_down;
								$isDropDownEnableChoiceValue = $field->isDropDownEnableChoiceValue;
								$choices = $field->isDropDownChoices;
								if ( $isDropDown && $isDropDownEnableChoiceValue && $choices ) {
									foreach ( $choices as $ch ) { // get each row
										if ( $value == $ch['value'] ) {
											$list_values[ $key ] = $ch['text'];
										}
									}

								}
							}
							$submit_value = $list_values; // add row to submit value array
						}
						$value = serialize( $submit_value );
					}
				}
			}
			return $value;
		} // END display_dropdown_value

		function drop_down_maybe_string_to_array( $choices, $column ) {
			if ( is_string( $choices ) ) {
				// allows a backslash to be used in string to escape a comma - allows an option value to include a comma
				$isDropDownChoices = str_replace( '\,', 'ITSG_TEMP_DELIM', $choices ); // replace escaped comma with temp string
				$options = explode( ',', $isDropDownChoices );
				foreach ( $options as &$option ) {
					$option = str_replace( 'ITSG_TEMP_DELIM', ',', $option ); // replace temp string with comma
				}

				$options = array_map( 'trim', $options ); // remove blank spaces at start or end of each option

				$new_array = array();

				foreach ( $options as $option ) {
					$new_array[] = array(
						'value' => $option,
						'text' => $option,
						);
				}

				return $new_array;
			} elseif ( is_array( $choices ) ) {
				$isDropDownEnableChoiceValue = rgar( $column, 'isDropDownEnableChoiceValue' );

				if ( ! $isDropDownEnableChoiceValue ) {
					foreach ( $choices as &$option ) {
						$option['value'] = $option['text'];
					}
				}
			}
			return $choices;
		}

		/*
		 * When the 'Enable add options' option is enabled and submitted value does not exist add it to the list of options
		 */
		function add_user_option( $form ) {
			if ( GFCommon::is_form_editor() ) {
				return $form;
			}

			if ( is_array( $form ) || is_object( $form ) ) {
				if ( isset( $_GET['gf_token'] ) ) { // if resuming saved form
					$incomplete_submission_info = GFFormsModel::get_incomplete_submission_values( $_GET['gf_token'] );
					if ( $incomplete_submission_info['form_id'] == $form['id'] ) {
						$submission_details_json = $incomplete_submission_info['submission'];
						$submission_details = json_decode( $submission_details_json, true );
						$submitted_values = $submission_details['submitted_values'];
					}
				} elseif ( GFCommon::is_entry_detail() ) { // if viewing entry in entry editor
					$lead_id = absint( rgar( $_POST, 'lid' ) ? rgar( $_POST, 'lid' ) : rgar( $_GET, 'lid' ) );
					if ( empty( $lead_id ) ) {
						return;
					}
					$lead = GFAPI::get_entry( $lead_id );
					if ( is_wp_error( $lead ) || ! $lead ) {
						return;
					}
				}

				foreach ( $form['fields'] as &$field ) {  // for all form fields
					$field_id = $field->id;
					if ( 'list' == $field->get_input_type() ) {

						if ( isset( $_GET['gf_token'] ) ) {
							$list_values = maybe_unserialize( $submitted_values[ $field_id ] );
						} elseif ( GFCommon::is_entry_detail() ) {
							$list_values =  maybe_unserialize( $lead[ $field_id ] );
						} else {
							$list_values = maybe_unserialize( RGFormsModel::get_field_value( $field ) );  // get the value of the field
						}

						if ( empty( $list_values) ) {
							continue;
						}

						$has_columns = is_array( $field->choices );

						if ( $has_columns ) {
							foreach ( $list_values as $row ) { // get each row
								foreach ( $field->choices as $key => &$choice ) { // for each column
									$column = rgars( $field->choices, "{$key}/text" ); // we'll be using the column label as the key
									$isDropDown = rgar( $choice, 'isDropDown' );
									if ( $isDropDown ) {
										$choices = rgar( $choice, 'isDropDownChoices' );

										$choices = $this->drop_down_maybe_string_to_array( $choices, $choice );

										$choice_value = ( string ) rgar( $row, $column  );
										foreach ( $choices as $ch ) { // get each row
											$arr_values[] = $ch['value'];
										}
										if ( ! in_array( $choice_value, $arr_values ) ) {
											$new_value = array(
												'value' => $choice_value,
												'text' => $choice_value,
											);
											$choices[] = $new_value;
											$choice['isDropDownChoices'] = $choices;
										}
									}
								}
							}
						} else {
							foreach ( $list_values as $row ) { // get each row
								$isDropDown = $field->itsg_list_field_drop_down;
								$isDropDownEnhanced = $field->itsg_list_field_drop_down_enhanced;
								$isDropDownEnhancedOther = $field->list_choice_drop_down_enhanced_other;
								if ( $isDropDown ) {
									$choices = $field->itsg_list_field_drop_down_options;

									$choices = $this->drop_down_maybe_string_to_array( $choices, $field );

									$choice_value = ( string ) $row;
									foreach ( $choices as $ch ) { // get each row
										$arr_values[] = $ch['value'];
									}
									if ( ! in_array( $choice_value, $arr_values ) ) {
										$new_value = array(
											'value' => $choice_value,
											'text' => $choice_value,
										);
										$choices[] = $new_value;
										$field->itsg_list_field_drop_down_options = $choices;

									}
								}
							}
						}
					}
				}
			}
			return $form;
		} // END add_user_option

	/**
	 * BEGIN: patch to allow JS and CSS to load when loading forms through wp-ajax requests
	 *
	 */

		/*
         * Enqueue JavaScript to footer
         */
		public function enqueue_scripts( $form, $is_ajax ) {
			if ( $this->requires_scripts( $form, $is_ajax ) ) {
				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

				wp_enqueue_script( 'gform_chosen' );
				wp_enqueue_style( 'listdropdown-style',  plugins_url( "/css/listdropdown-style{$min}.css", __FILE__ ) );
				wp_register_script( 'itsg_listdropdown_js', plugins_url( "/js/listdropdown-script{$min}.js", __FILE__ ),  array( 'jquery' ) );

				// Localize the script with new data
				$this->localize_scripts( $form, $is_ajax );

			}
		} // END datepicker_js

		public function requires_scripts( $form, $is_ajax ) {
			if ( is_admin() && ! GFCommon::is_form_editor() && is_array( $form ) ) {
				foreach ( $form['fields'] as $field ) {
					if ( 'list' == $field->get_input_type() ) {
						$has_columns = is_array( $field->choices );
						if ( $has_columns ) {
							foreach( $field->choices as $choice ) {
								if ( rgar( $choice, 'isDropDown' ) )  {
									return true;
								}
							}
						} elseif ( $field->itsg_list_field_dropdown || $field->itsg_list_field_drop_down ) {
							return true;
						}
					}
				}
			}

			return false;
		} // END requires_scripts

		function localize_scripts( $form, $is_ajax ) {
			// Localize the script with new data
			$is_entry_detail = GFCommon::is_entry_detail();

			$settings_array = array(
				'is_entry_detail' => $is_entry_detail ? $is_entry_detail : 0,
			);

			wp_localize_script( 'itsg_listdropdown_js', 'itsg_gf_listdropdown_js_settings', $settings_array );

			// Enqueued script with localized data.
			wp_enqueue_script( 'itsg_listdropdown_js' );

		} // END localize_scripts

	/**
	 * END: patch to allow JS and CSS to load when loading forms through wp-ajax requests
	 *
	 */

		/**
         * Replaces field content for repeater lists - adds title to select drop down fields using the column title
         */
		function change_column_content( $input, $input_info, $field, $text, $value, $form_id ) {
			if ( 'list' == $field->get_input_type() ) {
				$input = str_replace( '<select ', "<select title='".$text."'",$input);
				$has_columns = is_array( $field->choices );
				if ( $has_columns ) {
					foreach( $field->choices as $choice ) {
						if ( $text == $choice['text'] && rgar( $choice, 'isDropDownEnhanced' ) && '' != rgar( $choice, 'isDropDownChoices' ) ) {
							$classes = rgar( $choice, 'isDropDownEnhancedOther' ) ? 'chosen other' : 'chosen';
							$input = str_replace( "<select ", "<select class='{$classes}' ", $input );
						}
					}
				} else {
					if ( $field->itsg_list_field_drop_down_enhanced && '' != $field->itsg_list_field_drop_down_options ) {
						$classes = $field->list_choice_drop_down_enhanced_other ? 'chosen other' : 'chosen';
						$input = str_replace( "<select ", "<select class='{$classes}' ", $input );
					}
				}
			}
			return $input;
		} // END change_column_content

		/*
         * Changes column field if 'drop down field' option is ticked. Creates array of options, changes input type to select and add options.
         */
		function change_column_input( $input_info, $field, $column, $value, $form_id ) {
			if ( 'list' == $field->get_input_type() ) {
				$has_columns = is_array( $field->choices );
				if ( $has_columns ) {
					foreach( $field->choices as $choice ) {
						if ( $column == $choice['text']  &&  rgar( $choice, 'isDropDown' ) && '' != rgar( $choice, 'isDropDownChoices' ) ) {
							// if value is being pre-populated (array required) -- TO DO -- more work on this, likely need custom hook because pre-population currently only applies when form is loaded - not when navigated or resumed
							if ( is_array( $value ) ) {
								return array( 'type' => 'select', 'choices' => $value );
							}

							$isDropDownChoices = rgar( $choice, 'isDropDownChoices' );

							$isDropDownChoices = $this->drop_down_maybe_string_to_array( $isDropDownChoices, $choice );

							$isDropDownEnableChoiceValue = rgar( $choice, 'isDropDownEnableChoiceValue' );

							if ( ! $isDropDownEnableChoiceValue ) {
								foreach ( $isDropDownChoices as &$option ) {
									$option['value'] = $option['text'];
								}
							}

							// check if value is already in the list of options - if not, add it in. This is important if the isDropDownEnhancedOther option is enabled or for existing entries when the list of options has been changed.
							if ( ! empty( $value ) && ! $this->does_option_exist( $isDropDownChoices, 'value', $value) ) {
								array_unshift( $isDropDownChoices, array( 'value' => $value, 'text' => $value) );  // push current value into select list if options list is empty
							}

							return array( 'type' => 'select', 'choices' => $isDropDownChoices );


						}
					}
				} else {
					if ( $field->itsg_list_field_drop_down && '' != $field->itsg_list_field_drop_down_options ) {
						$isDropDownChoices = $field->itsg_list_field_drop_down_options;

						$isDropDownChoices = $this->drop_down_maybe_string_to_array( $isDropDownChoices, $field );

						$isDropDownEnableChoiceValue = $field->isDropDownEnableChoiceValue;

						if ( ! $isDropDownEnableChoiceValue) {
							foreach ( $isDropDownChoices as &$option ) {
								$option['value'] = $option['text'];
							}
						}

						// check if value is already in the list of options - if not, add it in. This is important if the isDropDownEnhancedOther option is enabled or for existing entries when the list of options has been changed.
						if ( ! empty( $value ) && ! $this->does_option_exist( $isDropDownChoices, 'value', $value) ) {
							array_unshift( $isDropDownChoices, array( 'value' => $value, 'text' => $value) );  // push current value into select list if options list is empty
						}

						return array( 'type' => 'select', 'choices' => $isDropDownChoices );
					}
					return $input_info;
				}
			}
		} // END change_column_input

		function does_option_exist( $array, $key, $val ) {
			foreach ( $array as $item ) {
				if ( isset( $item[ $key ] ) && $item[ $key ] == $val ) {
					return true;
					}
				}
			return false;
		}
		/*
         * Tooltip for for datepicker option
         */
		public static function field_tooltips( $tooltips ){
			$tooltips['itsg_list_field_drop_down'] = '<h6>' . __( 'Drop Down', 'gravity-forms-list-field-select-drop-down' ) . '</h6>' . __( 'Changes column to a drop down field. Only applies to single column list fields.', 'gravity-forms-list-field-select-drop-down' );
			return $tooltips;
		} // END field_tooltips

		/*
          * Adds custom settings for single column list field
          */
        public static function single_field_settings( $position, $form_id ) {
            // Create settings on position 50 (top position)
            if ( 50 == $position ) {
				?>
				<li class="list_drop_down_settings field_setting">
					<label class="section_label"><?php _e( 'Drop Down', 'gravity-forms-list-field-select-drop-down' ); ?></label>
					<input type="checkbox" id="list_choice_dropdown_enable_single" onclick="SetFieldProperty( 'itsg_list_field_drop_down', this.checked);itsg_gf_list_drop_down_init();">
					<label class="inline" for="list_choice_dropdown_enable_single">
					<?php esc_attr_e( 'Apply drop down', 'gravity-forms-list-field-select-drop-down' ); ?>
					<?php gform_tooltip( 'itsg_list_field_drop_down' );?>
					</label>
					<div id="list_drop_down_options" class="choices_setting_single" style="display:none; background: rgb(244, 244, 244) none repeat scroll 0px 0px; padding: 10px; border-bottom: 1px solid grey; margin-top: 10px;" >
						<div style="float:right;">
								<input type="checkbox" id="list_choice_values_enabled_single" onclick="SetFieldChoiceDropDown(0);  " onkeypress="SetFieldProperty('enableChoiceValue', this.checked); ToggleChoiceValue(); SetFieldChoices();">
								<label for="list_choice_values_enabled_single" class="inline gfield_value_label"><?php esc_attr_e( 'show values', 'gravity-forms-list-field-select-drop-down' ); ?></label>
							</div>
							<label for="choices" class="section_label"><?php esc_attr_e( 'Choices', 'gravity-forms-list-field-select-drop-down' ); ?></label>
							<div>
								<label class="gfield_choice_header_label"><?php esc_attr_e( 'Label', 'gravity-forms-list-field-select-drop-down' ); ?></label>
								<label class="gfield_choice_header_value"><?php esc_attr_e( 'Value', 'gravity-forms-list-field-select-drop-down' ); ?></label>
							</div>
						<div id="field_choices_single" >
							<!-- JAVASCRIPT WILL ADD CONTENT HERE -->
						</div>
						<br>
						<input type="checkbox" onclick="SetFieldProperty( 'itsg_list_field_drop_down_enhanced', this.checked );itsg_gf_list_drop_down_init();" id="list_choice_dropdown_enhanced_single" >
						<label for="list_choice_dropdown_enhanced_single" style="display: inline; "><?php esc_attr_e( 'Enable enhanced user interface', 'gravity-forms-list-field-select-drop-down' ); ?></label>
						<br>
						<input type="checkbox" onclick="SetFieldProperty( 'list_choice_drop_down_enhanced_other', this.checked );" id="list_choice_dropdown_enhanced_other_single" >
						<label for="list_choice_dropdown_enhanced_other_single" style="display: inline; "><?php esc_attr_e( 'Enable add options', 'gravity-forms-list-field-select-drop-down' ); ?></label>
					</div>
				</li>
			<?php
            }
        } // END single_field_settings

		/*
         * Warning message if Gravity Forms is not installed and enabled
         */
		public static function admin_warnings() {
			if ( !self::is_gravityforms_installed() ) {
				printf(
					'<div class="error"><h3>%s</h3><p>%s</p><p>%s</p></div>',
						__( 'Warning', 'gravity-forms-list-field-select-drop-down' ),
						sprintf ( __( 'The plugin %s requires Gravity Forms to be installed.', 'gravity-forms-list-field-select-drop-down' ), '<strong>'.self::$name.'</strong>' ),
						sprintf ( esc_html__( 'Please %sdownload the latest version of Gravity Forms%s and try again.', 'gravity-forms-list-field-select-drop-down' ), '<a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank" >', '</a>' )
				);
			}
		} // END admin_warnings

		/*
		* Check if GF is installed
		*/
		private static function is_gravityforms_installed() {
			return class_exists( 'GFCommon' );
        } // END is_gravityforms_installed

		/*
         * Check if current form has a drop-down enabled list field
         */
		public static function list_has_dropdown_field( $form ) {
			if ( is_array( $form['fields'] ) ) {
				foreach ( $form['fields'] as $field ) {
					if ( 'list' == $field->get_input_type() ) {
						$has_columns = is_array( $field->choices );
						if ( $has_columns ) {
							foreach( $field->choices as $choice ) {
								if ( rgar( $choice, 'isDropDown' ) )  {
									return true;
								}
							}
						} else if ( $field->itsg_list_field_dropdown || $field->itsg_list_field_drop_down ) {
							return true;
						}
					}
				}
			}
			return false;
		} // END list_has_datepicker_field

	}
    $ITSG_GF_List_Field_Drop_Down = new ITSG_GF_List_Field_Drop_Down();
}