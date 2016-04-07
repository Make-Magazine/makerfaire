<?php
/**
* Plugin Name: GP Read Only
* Description: Mark your form fields as read-only to allow users to see field data but not modify it.
* Plugin URI: http://gravitywiz.com/
* Version: 1.2.9
* Author: David Smith
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
*/

/**
* Saftey net for individual perks that are active when core Gravity Perks plugin is inactive.
*/
$gw_perk_file = __FILE__;
if(!require_once(dirname($gw_perk_file) . '/safetynet.php'))
    return;

class GWReadOnly extends GWPerk {

    public $version = '1.2.9';
    protected $min_gravity_perks_version = '1.0.beta3';
    protected $min_gravity_forms_version = '1.6.11';
    protected $min_wp_version = '3.0';

    private $unsupported_field_types = array( 'hidden', 'html', 'list', 'captcha', 'page', 'section' );
    private $disable_attr_field_types = array( 'radio', 'select', 'checkbox', 'multiselect' );

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
            default:
                $search = "<input";
                $replace = $search . " readonly='readonly'";
                break;
        }


        $input_html = str_replace( $search, $replace, $input_html );

        // add hidden capture input markup for disabled field types
        if( in_array( $input_type, $this->disable_attr_field_types ) ) {

            $value = $this->get_field_value( $field );
            $hc_input_markup = '';

            if( is_array( $field['inputs'] ) ) {

                foreach( $field['inputs'] as $input ) {
	                $input_id_bits    = explode( '.', $input['id'] );
                    $hc_input_id      = sprintf( 'gwro_hidden_capture_%d_%d_%d', $form_id, $field['id'], array_pop( $input_id_bits ) );
                    $hc_input_markup .= sprintf( '<input type="hidden" id="%s" name="%s" value="%s" />', $hc_input_id, $hc_input_id, rgar( $value, (string) $input['id'] ) );
                }

            } else {
                $hc_input_id = sprintf( 'gwro_hidden_capture_%d_%d', $form_id, $field['id'] );
                $hc_input_markup = sprintf( '<input type="hidden" id="%s" name="%s" value="%s" />', $hc_input_id, $hc_input_id, $value );
            }

            $input_html .= $hc_input_markup;
        }

		add_filter( 'gform_field_input', array( $this, 'read_only_input' ), 11, 5 );

		return $input_html;
	}

    public function process_hidden_captures( $form ) {

        foreach( $_POST as $key => $value ) {

            if( strpos( $key, 'gwro_hidden_capture_' ) !== 0 ) {
                continue;
            }

            // gets 481, 5, & 1 from a string like "gwro_hidden_capture_481_5_1"
            list( $form_id, $field_id, $input_id ) = array_pad( explode( '_', str_replace( 'gwro_hidden_capture_', '', $key ) ), 3, false );

            // gets "5_1" from an array like array( 5, 1 ) or "5" from an array like array( 5, false )
            $full_input_id = implode( '_', array_filter( array( $field_id, $input_id ) ) );

            $_POST[ "input_{$full_input_id}" ] = $value;

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
			    $submitted_values						= $submission_details['submitted_values'];
			    $field_values                           = $submission_details['field_values'];
		    }
	    }

	    if ( is_array( $submitted_values ) ) {
		    $value = $submitted_values[ $field->id ];
	    } else {
		    $value = GFFormsModel::get_field_value( $field, $field_values );
	    }

        $choices = (array) rgar( $field, 'choices' );

        // if value is not available from post or prepop, check the choices (if field has choices)
        if( ! $value && ! empty( $choices ) ) {

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