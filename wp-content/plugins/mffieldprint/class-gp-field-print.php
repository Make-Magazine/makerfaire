<?php

class GP_Field_Print extends GWPerk {

	public $version = MF_FIELD_PRINT_VERSION;
	protected $min_gravity_perks_version = '1.0.beta3';
	protected $min_gravity_forms_version = '1.6.11';
	protected $min_wp_version = '3.0';

	private $supported_field_types = array( 'html', 'captcha', 'section' );
  //private $unsupported_field_types = array( 'hidden', 'html', 'captcha', 'page', 'section' );
	//private $disable_attr_field_types = array( 'radio', 'select', 'checkbox', 'multiselect', 'time', 'workflow_user', 'workflow_role', 'workflow_assignee_select' );

  public function init() {

		$this->add_tooltip($this->key('printfield'), __('<h6>Add Print Button</h6> This will add a print button to allow the user to print the contents of this field', 'gravityperks'));
		$this->enqueue_field_settings();

    // Filters
		add_filter( 'gform_field_input', array( $this, 'print_field_content' ), 11, 5 );
    add_filter( 'gform_register_init_scripts', array( $this, 'print_field_js'), 11, 1);

	}

	public function field_settings_ui() {
		?>

		<li class="<?php echo $this->key('field_setting'); ?> field_setting" style="display:none;">
			<input type="checkbox" id="<?php echo $this->key('field_checkbox'); ?>" value="1" onclick="SetFieldProperty('<?php echo $this->key('enable'); ?>', this.checked)">

			<label class="inline" for="<?php echo $this->key('field_checkbox'); ?>">
				<?php _e('Print Field', 'gravityperks'); ?>
				<?php gform_tooltip($this->key('printfield')); ?>
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
						if(isfieldprintFieldType(i))
							fieldSettings[i] += ', .mffieldprint_field_setting';
					}

				});

				$(document).bind('gform_load_field_settings', function(event, field, form) {
					$("#<?php echo $this->key('field_checkbox'); ?>").attr('checked', field["<?php echo $this->key('enable'); ?>"] == true);
					if( ! isfieldprintFieldType( GetInputType( field ) ) || isCalcEnabled( field ) ) {
						field["<?php echo $this->key('enable'); ?>"] = false;
						$('.mffieldprint_field_setting').hide();
					}
				});

				function isfieldprintFieldType(type) {
					var supportedFieldTypes = <?php echo json_encode($this->supported_field_types); ?>;
					return $.inArray(type, supportedFieldTypes) != -1 ? true : false;
				}

				function isCalcEnabled( field ) {
					return field.enableCalculation == true || GetInputType( field ) == 'calculation';
				}

			})(jQuery);

		</script>

		<?php
	}

  public function print_field_js($form) {
    ?>
    <script type="text/javascript">
        function printField(fieldID) {
          var divToPrint=document.getElementById("field_<?php echo $form['id'];?>_"+fieldID);
          newWin= window.open("");
/*use this line to add CSS
          newWin.document.write('<html><head><title>Print it!</title>');

            newWin.document.write('<link rel="stylesheet" type="text/css" href="styles.css">\n\');

          newWin.document.write('</head>');
          newWin.document.write('<body>');
          */
          newWin.document.write(divToPrint.outerHTML);
          /*win.document.write('</body></html>');*/

          newWin.print();
          newWin.close();
        }

    </script>
    <?php
    //GFFormDisplay::add_init_script($form['id'], 'printField', GFFormDisplay::ON_PAGE_RENDER, $script);

    return $form;
  }

  public function print_field_content( $input_html, $field, $value, $entry_id, $form_id ) {

    //display only on entry
		if( $field->is_entry_detail() || GFCommon::is_form_editor()) {
			return $input_html;
		}

		$input_type = RGFormsModel::get_input_type($field);
		if( !in_array( $input_type, $this->supported_field_types ) || ! rgar( $field, $this->key( 'enable' ) ) ) {
			return $input_html;
		}

		remove_filter( 'gform_field_container', array( $this, 'print_field_content' ), 11, 6 );

    $input_html .= '<input type="button" onclick="printField(\''.$field->id.'\')" value="Print '.$field->label.'">';
    add_filter( 'gform_field_container', array( $this, 'print_field_content' ), 11, 6 );

    return $input_html;

  }
}

class MFFieldPrint extends GP_Field_Print { };