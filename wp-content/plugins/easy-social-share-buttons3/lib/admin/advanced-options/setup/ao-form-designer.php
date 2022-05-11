<?php
$loadingOptions = isset($_REQUEST['loadingOptions']) ? $_REQUEST['loadingOptions'] : array();

$design = isset($loadingOptions['design']) ? $loadingOptions['design'] : '';

$designSetup = essb5_get_form_settings($design);

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options_forms');
}

echo '<input type="hidden" name="form_design_id" id="form_design_id" value="'.$design.'"/>';

essb5_draw_input_option('name', esc_html__('Form name', 'essb'), esc_html__('The name of the form is the one you will see in the list. This name won\'t appear in the design. Required!', 'essb'), true, true, essb_array_value('name', $designSetup));

$connector = essb_option_value('subscribe_connector');
if ($connector != 'mymail' && $connector != 'mailster' && $connector != 'mailpoet' && $connector != 'conversio') {
	essb5_draw_heading(esc_html__('Custom Form List', 'essb'), '7', '', '', '<i class="fa fa-database"></i>');
	essb5_draw_field_group_open();
	echo '<div class="essb-options-helprow"><div class="help-details" style="display: block;">';
	echo '<div class="desc noButton">Customize the connected list for this form design only. Fill only if the list will be different than the general. Otherwise, leave blank the fields in the section.</div>';
	echo '</div></div>';
	essb5_draw_input_option('customlist', esc_html__('List ID', 'essb'), '', true, true, essb_array_value('customlist', $designSetup));
	
	if ($connector == 'mailchimp') {
	    essb5_draw_input_option('customtags', esc_html__('Tags', 'essb'), esc_html__('Optional. Customize the global tags for this form design only. Leave blank to use the global (or if you are not using tags).', 'essb'), true);
	}
	essb5_draw_field_group_close();
}


essb5_draw_heading(esc_html__('Form Texts', 'essb'), '7', '', '', '<i class="fa fa-align-left"></i>');
essb5_draw_field_group_open();
essb5_draw_switch_option('add_name', esc_html__('Include Name Field', 'essb'), '', true, essb_array_value('add_name', $designSetup));

essb5_draw_input_option('title', esc_html__('Heading', 'essb'), '', true, true, essb_array_value('title', $designSetup));
essb5_draw_editor_option('text', esc_html__('Form custom content', 'essb'), esc_html__('HTML code and shortcodes are supported', 'essb'), 'htmlmixed', true, essb_array_value('text', $designSetup));
essb5_draw_input_option('footer', esc_html__('Footer text', 'essb'), '', true, true, essb_array_value('footer', $designSetup));
essb5_draw_input_option('name_placeholder', esc_html__('Name field text', 'essb'), '', true, true, essb_array_value('name_placeholder', $designSetup));
essb5_draw_input_option('email_placeholder', esc_html__('Email field text', 'essb'), '', true, true, essb_array_value('email_placeholder', $designSetup));
essb5_draw_input_option('button_placeholder', esc_html__('Subscribe button text', 'essb'), '', true, true, essb_array_value('button_placeholder', $designSetup));
essb5_draw_input_option('error_message', esc_html__('Error subscribe message', 'essb'), '', true, true, essb_array_value('error_message', $designSetup));
essb5_draw_input_option('ok_message', esc_html__('Success subscribe mssage', 'essb'), '', true, true, essb_array_value('ok_message', $designSetup));

/**
 * @since 7.6 Multilanguage support for the custom subscribe forms
 */
if (essb_installed_wpml() || essb_installed_polylang()) {
    if (ESSBActivationManager::isActivated()) {
        $languages = ESSBWpmlBridge::getLanguages();
        
        foreach ($languages as $key => $name) {
            essb5_draw_panel_start($name, 'Translate the fields you need in this language. If you leave blank it will use the global texts.');

            essb5_draw_input_option('title_'.$key, esc_html__('Heading', 'essb'), '', true, true, essb_array_value('title_'.$key, $designSetup));
            essb5_draw_editor_option('text_'.$key, esc_html__('Form custom content', 'essb'), esc_html__('HTML code and shortcodes are supported', 'essb'), 'htmlmixed', true, essb_array_value('text_'.$key, $designSetup));
            essb5_draw_input_option('footer_'.$key, esc_html__('Footer Text', 'essb'), '', true, true, essb_array_value('footer_'.$key, $designSetup));
            essb5_draw_input_option('name_placeholder_'.$key, esc_html__('Name field text', 'essb'), '', true, true, essb_array_value('name_placeholder_'.$key, $designSetup));
            essb5_draw_input_option('email_placeholder_'.$key, esc_html__('Email field text', 'essb'), '', true, true, essb_array_value('email_placeholder_'.$key, $designSetup));
            essb5_draw_input_option('button_placeholder_'.$key, esc_html__('Subscribe button text', 'essb'), '', true, true, essb_array_value('button_placeholder_'.$key, $designSetup));
            essb5_draw_input_option('error_message_'.$key, esc_html__('Error Subscribe Message', 'essb'), '', true, true, essb_array_value('error_message_'.$key, $designSetup));
            essb5_draw_input_option('ok_message_'.$key, esc_html__('Success Subscribe Message', 'essb'), '', true, true, essb_array_value('ok_message_'.$key, $designSetup));
            
            essb5_draw_panel_end();
        }
    }
}

essb5_draw_field_group_close();

essb5_draw_heading(esc_html__('Include Image', 'essb'), '7', '', '', '<i class="fa fa-picture-o"></i>');
essb5_draw_field_group_open();
essb5_draw_file_option('image', esc_html__('Select image', 'essb'), '', true, essb_array_value('image', $designSetup));
$image_locations = array('' => esc_html__('Do not show image', 'essb'), 'left' => esc_html__('On the left', 'essb'), 'right' => esc_html__('On the right', 'essb'), 'top' => esc_html__('At the top above heading', 'essb'), esc_html__('below_heading') => esc_html__('At the top between heading and content', 'essb'), 'background' => esc_html__('As form background image', 'essb'));
essb5_draw_select_option('image_location', esc_html__('Position', 'essb'), '', $image_locations, true, essb_array_value('image_location', $designSetup));
essb5_draw_input_option('image_width', esc_html__('Width', 'essb'), esc_html__('The value is optional but recommended if you plan to use SVG files. You need to fill value with the measuring unit (ex.: 100px, 50%)', 'essb'), false, true, essb_array_value('image_width', $designSetup));
essb5_draw_input_option('image_height', esc_html__('Height', 'essb'), esc_html__('The value is optional but recommended if you plan to use SVG files. You need to fill value with the measuring unit (ex.: 100px, 50%)', 'essb'), false, true, essb_array_value('image_height', $designSetup));
essb5_draw_input_option('image_padding', esc_html__('Image area padding', 'essb'), 'The padding should be set with the measuring unit (example: 20px or 15px 30px)', false, true, essb_array_value('image_padding', $designSetup));
$image_area_width = array('' => esc_html__('Default', 'essb'), '25' => '25%', '30' => '30%', '40' => '40%', '50' => '50%');
essb5_draw_select_option('image_area_width', esc_html__('Image area width', 'essb'), '', $image_area_width, true, essb_array_value('image_area_width', $designSetup));
essb5_draw_field_group_close();

function essb_ao_form_designer_fontsize_fontweight($key, $title, $designSetup) {
    
    $font_weight_selector = array('' => esc_html__('Theme default', 'essb'), '400' => esc_html__('Normal', 'essb'), '700' => esc_html__('Bold', 'essb'));
    
    $fs_key = $key . '_fontsize';
    $fw_key = $key . '_fontweight';
    
    $fs_value = essb_array_value($fs_key, $designSetup);
    $fw_value = essb_array_value($fw_key, $designSetup);    
    
    ESSBOptionsFramework::draw_options_row_start_advanced_panel($title, '', '');
    ESSBOptionsFramework::draw_input_field($fs_key, false, 'essb_options', $fs_value);
    ESSBOptionsFramework::draw_select_field($fw_key, $font_weight_selector, false, 'essb_options', $fw_value);
    ESSBOptionsFramework::draw_options_row_end();
}
essb5_draw_heading(esc_html__('Font Style & Size', 'essb'), '7', '', '', '<i class="fa fa-font"></i>');
essb5_draw_field_group_open('inline-flex-cols');

essb_ao_form_designer_fontsize_fontweight('heading', esc_html__('Heading', 'essb'), $designSetup);
essb_ao_form_designer_fontsize_fontweight('text', esc_html__('Text', 'essb'), $designSetup);
essb_ao_form_designer_fontsize_fontweight('footer', esc_html__('Footer', 'essb'), $designSetup);
essb_ao_form_designer_fontsize_fontweight('input', esc_html__('Input fields', 'essb'), $designSetup);
essb_ao_form_designer_fontsize_fontweight('button', esc_html__('Button', 'essb'), $designSetup);

$alignment_selector = array('' => esc_html__('Theme Default', 'essb'), 'left' => esc_html__('Left', 'essb'), 'center' => esc_html__('Center', 'essb'), 'right' => esc_html__('Right', 'essb'));
essb5_draw_select_option('align', esc_html__('Content alignment', 'essb'), '', $alignment_selector, true, essb_array_value('align', $designSetup));
essb5_draw_field_group_close();

essb5_draw_heading(esc_html__('Colors & General Styles', 'essb'), '7', '', '', '<i class="fa fa-paint-brush"></i>');
essb5_draw_field_group_open();

essb5_draw_color_option('bgcolor', esc_html__('Background color', 'essb'), '', false, true, essb_array_value('bgcolor', $designSetup));
essb5_draw_color_option('bgcolor2', esc_html__('Secondary background color', 'essb'), esc_html__('Select in addition secondary background color if you wish to create a gradient effect', 'essb'), false, true, essb_array_value('bgcolor2', $designSetup));
essb5_draw_color_option('image_bgcolor', esc_html__('Image area background color', 'essb'), esc_html__('Used only when you are showing image on the form', 'essb'), false, true, essb_array_value('image_bgcolor', $designSetup));
essb5_draw_color_option('textcolor', esc_html__('Text color', 'essb'), '', false, true, essb_array_value('textcolor', $designSetup));
essb5_draw_color_option('headingcolor', esc_html__('Heading color', 'essb'), '', false, true, essb_array_value('headingcolor', $designSetup));
essb5_draw_color_option('footercolor', esc_html__('Footer color', 'essb'), '', false, true, essb_array_value('footercolor', $designSetup));
essb5_draw_color_option('fields_bg', esc_html__('Email/Name fields background color', 'essb'), '', false, true, essb_array_value('fields_bg', $designSetup));
essb5_draw_color_option('fields_text', esc_html__('Email/Name fields text color', 'essb'), '', false, true, essb_array_value('fields_text', $designSetup));
essb5_draw_color_option('button_bg', esc_html__('Subscribe button background', 'essb'), '', false, true, essb_array_value('button_bg', $designSetup));
essb5_draw_color_option('button_text', esc_html__('Subscribe button text', 'essb'), '', false, true, essb_array_value('button_text', $designSetup));
essb5_draw_color_option('border_color', esc_html__('Border color', 'essb'), '', true, true, essb_array_value('border_color', $designSetup));
essb5_draw_input_option('border_width', esc_html__('Border width', 'essb'), 'Example: 1px', false, true, essb_array_value('border_width', $designSetup));
essb5_draw_input_option('border_radius', esc_html__('Border radius', 'essb'), 'Example: 3px, 50%', false, true, essb_array_value('border_radius', $designSetup));
essb5_draw_input_option('padding', esc_html__('Form padding', 'essb'), esc_html__('The padding values should be filled with the measuring unit (ex.: 10px or 10px 20px or 5%). When nothing is filled plugin will apply a default 30px padding from all sides. If you wish to remove the padding you can fill 0', 'essb'), false, true, essb_array_value('padding', $designSetup));
essb5_draw_color_option('glow_color', esc_html__('Glow color', 'essb'), '', true, true, essb_array_value('glow_color', $designSetup));
essb5_draw_input_option('glow_size', esc_html__('Glow size', 'essb'), esc_html__('The value should be numeric without the measuring unit (ex.: 10)', 'essb'), false, true, essb_array_value('glow_size', $designSetup));
essb5_draw_switch_option('sameline_fields', esc_html__('Keep the form fields on the same line', 'essb'), '', true, essb_array_value('sameline_fields', $designSetup));

essb5_draw_field_group_close();