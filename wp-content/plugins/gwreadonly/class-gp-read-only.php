<?php 

class GP_Read_Only extends GWPerk {

    public $version = '1.2.14';
    protected $min_gravity_perks_version = '1.0.beta3';
    protected $min_gravity_forms_version = '1.6.11';
    protected $min_wp_version = '3.0';

    private $unsupported_field_types = array( 'hidden', 'html', 'list', 'captcha', 'page', 'section' );
    private $disable_attr_field_types = array( 'radio', 'select', 'checkbox', 'multiselect', 'time' );

    public function init() {

        $this->add_tooltip($this->key('readonly'), __('<h6>Read-only</h6> Set field as "readonly". Read-only fields will be visible on the form but cannot be modified by the user.', 'gravityperks'));
        $this->enqueue_field_settings();

        // Filters
        add_filter( 'gform_field_input', array( $this, 'read_only_input' ), 11, 5 );

        add_filter( 'gform_pre_process', array( $this, 'process_hidden_captures' ) );

    }

    public function field_settings_ui() {
        ?>

        <li class="<?php echo $this->key('field_setting'); ?> field_setting" style="display:none;">
            <input type="checkbox" id="<?php echo $this->key('field_checkbox'); ?>" value="1" onclick="SetFieldProperty('<?php echo $this->key('enable'); ?>', this.checked)">

            <label class="inline" for="<?php echo $this->key('field_checkbox'); ?>">
                <?php _e('Read-only', 'gravityperks'); ?>
                <?php gform_tooltip($this->key('readonly')); ?>
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
                    $("#<?php echo $this->key('field_checkbox'); ?>").attr('checked', field["<?php echo $this->key('enable'); ?>"] == true);
                    if( ! isReadOnlyFieldType( GetInputType( field ) ) || isCalcEnabled( field ) ) {
                        field["<?php echo $this->key('enable'); ?>"] = false;
                        $('.gwreadonly_field_setting').hide();
                    }
                });

                function isReadOnlyFieldType(type) {
                    var unsupportedFieldTypes = <?php echo json_encode($this->unsupported_field_types); ?>;
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

        if( $field->is_entry_detail() ) {
            return $input_html;
        }

        $input_type = RGFormsModel::get_input_type($field);
        if( in_array( $input_type, $this->unsupported_field_types ) || ! rgar( $field, $this->key( 'enable' ) ) ) {
            return $input_html;
        }

        remove_filter( 'gform_field_input', array( $this, 'read_only_input' ), 11, 5 );

        $input_html = GFCommon::get_field_input( $field, $value, $entry_id, $form_id, GFAPI::get_form( $form_id ) );

        switch( $input_type ) {
            case 'textarea':
            case 'post_content':
            case 'post_excerpt':
                $search = "<textarea";
                $replace = $search . " readonly='readonly'";
                break;
            case 'multiselect':
            case 'select':
                $search = "<select";
                $replace = $search . " disabled='disabled'";
                break;
            case 'radio':
            case 'checkbox':
                $search = "<input";
                $replace = $search . " disabled='disabled'";
                break;
            case 'time':
                $search = array(
	                "<input"  => "<input readonly='readonly'",
                    "<select" => "<select disabled='disabled'",
                );
                break;
            default:
                $search = "<input";
                $replace = $search . " readonly='readonly'";
                break;
        }

        if( ! is_array( $search ) ) {
            $search = array( $search => $replace );
        }

        if( $input_type == 'date' && $field->dateType == 'datepicker' ) {
	        /**
	         * Disable the datepicker for read-only Datepicker fields.
             *
             * @since 1.2.13
             *
             * @param bool          $is_disabled Whether or not to disable the datepicker for this read-only input.
	         * @param GF_Field_Date $field       GF_Field_Date The current Date field object.
	         * @param int           $entry_id    The current entry ID; 0 when no entry ID is provided.
	         */
            $disable_datepicker = gf_apply_filters( array( 'gpro_disable_datepicker', $form_id, $field->id ), false, $field, $entry_id );
            if( $disable_datepicker ) {
                // Find 'datepicker' CSS class and remove it.
                $search['\'datepicker '] = '';
            }
        }

        foreach( $search as $_search => $replace ) {
	        $input_html = str_replace( $_search, $replace, $input_html );
        }

        // add hidden capture input markup for disabled field types
        if( in_array( $input_type, $this->disable_attr_field_types ) ) {

            $value = $this->get_field_value( $field );
            $hc_input_markup = '';

            if( is_array( $field['inputs'] ) ) {

                switch( $input_type ) {
                    case 'time':
	                    $hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $field->id . '.3', array_pop( $value ) );
                        break;
                    default:
	                    foreach( $field['inputs'] as $input ) {
		                    $hc_input_markup .= $this->get_hidden_capture_markup( $form_id, $input['id'], $value );
	                    }
                }

            } else {

                $hc_input_markup = $this->get_hidden_capture_markup( $form_id, $field->id, $value );

            }

            $input_html .= $hc_input_markup;

        }

        add_filter( 'gform_field_input', array( $this, 'read_only_input' ), 11, 5 );

        return $input_html;
    }

    public function get_hidden_capture_input_id( $form_id, $input_id ) {

        if( intval( $input_id ) != $input_id ) {
	        $input_id_bits               = explode( '.', $input_id );
	        list( $field_id, $input_id ) = $input_id_bits;
	        $hc_input_id = sprintf( 'gwro_hidden_capture_%d_%d_%d', $form_id, $field_id, $input_id );
        } else {
	        $hc_input_id = sprintf( 'gwro_hidden_capture_%d_%d', $form_id, $input_id );
        }

	    return $hc_input_id;
    }

    public function get_hidden_capture_markup( $form_id, $input_id, $value ) {

        $hc_input_id = $this->get_hidden_capture_input_id( $form_id, $input_id );

        if( is_array( $value ) ) {
            $value = rgar( $value, (string) $input_id );
        }

        return sprintf( '<input type="hidden" id="%s" name="%s" value="%s" />', $hc_input_id, $hc_input_id, $value );
    }

    public function process_hidden_captures( $form ) {

        foreach( $_POST as $key => $value ) {

            if( strpos( $key, 'gwro_hidden_capture_' ) !== 0 ) {
                continue;
            }

            // gets 481, 5, & 1 from a string like "gwro_hidden_capture_481_5_1"
            list( $form_id, $field_id, $input_id ) = array_pad( explode( '_', str_replace( 'gwro_hidden_capture_', '', $key ) ), 3, false );

            $field = GFFormsModel::get_field( $form, $field_id );
            switch( $field->get_input_type() ) {
                // time fields are in array format in the POST
                case 'time':
	                $full_input_id = $field_id;
                    $full_value    = rgpost( "input_{$full_input_id}" );
                    $full_value[]  = $value;
                    $value         = $full_value;
                    break;
                default:
	                // gets "5_1" from an array like array( 5, 1 ) or "5" from an array like array( 5, false )
	                $full_input_id = implode( '_', array_filter( array( $field_id, $input_id ) ) );
            }

            // Only use hidden capture if $_POST does not already contain a value for this inputs;
            // this allows support for checking/unchecking via JS (i.e. checkbox fields).
            if( empty( $_POST[ "input_{$full_input_id}" ] ) && $value ) {
	            $_POST[ "input_{$full_input_id}" ] = $value;
            }

        }

        return $form;
    }

    public function get_field_value( $field ) {

        $field_values = $submitted_values = false;

        if ( isset( $_GET['gf_token'] ) ) {
            $incomplete_submission_info = GFFormsModel::get_incomplete_submission_values( $_GET['gf_token'] );
            if ( $incomplete_submission_info['form_id'] == $field['formId'] ) {
                $submission_details_json                = $incomplete_submission_info['submission'];
                $submission_details                     = json_decode( $submission_details_json, true );
                $submitted_values                        = $submission_details['submitted_values'];
                $field_values                           = $submission_details['field_values'];
            }
        }

        if ( is_array( $submitted_values ) ) {
            $value = $submitted_values[ $field->id ];
        } else {
            $value = $field->get_value_default_if_empty( GFFormsModel::get_field_value( $field, $field_values ) );
        }

        $choices = (array) rgar( $field, 'choices' );
        $choices = array_filter( $choices );

        if( ! $value && $field->get_input_type() == 'time' ) {

        }
        // if value is not available from post or prepop, check the choices (if field has choices)
        else if( ! $value && ! empty( $choices ) ) {

            $values = array();
            $index  = 1;

            foreach( $choices as $choice ) {

                if( $index % 10 == 0 ) {
                    $index++;
                }

                if( $choice['isSelected'] ) {
                    $full_input_id = sprintf( '%d.%d', $field['id'], $index );
                    $price         = rgempty( 'price', $choice ) ? 0 : GFCommon::to_number( rgar( $choice, 'price' ) );
                    $choice_value  = $field['type'] == 'product' ? sprintf( '%s|%s', $choice['value'], $price ) : $choice['value'];
                    $values[ $full_input_id ] = $choice_value;
                }

                $index++;

            }

            $input_type = GFFormsModel::get_input_type( $field );

            // if no choice is preselected and this is a select, get the first choice's value since it will be selected by default in the browser
            if( empty( $values ) && $input_type == 'select' ) {
                $values[] = rgars( $choices, '0/value' );
            }

            switch( $input_type ) {
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

    public function documentation() {
        return array(
            'type'  => 'url',
            'value' => 'http://gravitywiz.com/documentation/gp-read-only/'
        );
    }

}

class GWReadOnly extends GP_Read_Only { };