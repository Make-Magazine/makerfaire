<?php
$loadingOptions = isset($_REQUEST['loadingOptions']) ? $_REQUEST['loadingOptions'] : array();

$design = isset($loadingOptions['design']) ? $loadingOptions['design'] : '';

$designSetup = essb5_get_form_settings($design);

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options_forms');
}

echo '<input type="hidden" name="form_design_id" id="form_design_id" value="'.$design.'"/>';

essb5_draw_input_option('name', esc_html__('Form Name', 'essb'), esc_html__('Enter form name that will appear inside the design lists. Use this name for easy recognition of the from in the list', 'essb'), true, true, essb_array_value('name', $designSetup));

$connector = essb_option_value('subscribe_connector');
if ($connector != 'mymail' && $connector != 'mailster' && $connector != 'mailpoet' && $connector != 'conversio') {
	essb5_draw_heading(esc_html__('Custom Form List', 'essb'), '5');
	essb5_draw_input_option('customlist', esc_html__('List ID', 'essb'), esc_html__('Optional you can set a different list for this form only. To get the list ID you can follow the instructions on the subscribe connector.', 'essb'), true, true, essb_array_value('customlist', $designSetup));
}


essb5_draw_heading(esc_html__('Form Texts', 'essb'), '5');
essb5_draw_switch_option('add_name', esc_html__('Include Name Field', 'essb'), '', true, essb_array_value('add_name', $designSetup));

essb5_draw_input_option('title', esc_html__('Heading', 'essb'), '', true, true, essb_array_value('title', $designSetup));
essb5_draw_editor_option('text', esc_html__('Form custom content', 'essb'), esc_html__('HTML code and shortcodes are supported', 'essb'), 'htmlmixed', true, essb_array_value('text', $designSetup));
essb5_draw_input_option('footer', esc_html__('Footer Text', 'essb'), '', true, true, essb_array_value('footer', $designSetup));
essb5_draw_input_option('name_placeholder', esc_html__('Name field text', 'essb'), '', true, true, essb_array_value('name_placeholder', $designSetup));
essb5_draw_input_option('email_placeholder', esc_html__('Email field text', 'essb'), '', true, true, essb_array_value('email_placeholder', $designSetup));
essb5_draw_input_option('button_placeholder', esc_html__('Subscribe button text', 'essb'), '', true, true, essb_array_value('button_placeholder', $designSetup));
essb5_draw_input_option('error_message', esc_html__('Error Subscribe Message', 'essb'), '', true, true, essb_array_value('error_message', $designSetup));
essb5_draw_input_option('ok_message', esc_html__('Success Subscribe Message', 'essb'), '', true, true, essb_array_value('ok_message', $designSetup));

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

essb5_draw_heading(esc_html__('Include Image Inside Form', 'essb'), '5');
essb5_draw_file_option('image', esc_html__('Select image for the form', 'essb'), esc_html__('Optional you can choose an image that will appear inside the form. The image location can be selected from the menu blow', 'essb'), true, essb_array_value('image', $designSetup));
$image_locations = array('' => esc_html__('Do not show image', 'essb'), 'left' => esc_html__('On the left', 'essb'), 'right' => esc_html__('On the right', 'essb'), 'top' => esc_html__('At the top above heading', 'essb'), esc_html__('below_heading') => esc_html__('At the top between heading and content', 'essb'), 'background' => esc_html__('As form background image', 'essb'));
essb5_draw_select_option('image_location', esc_html__('Image Appearance', 'essb'), '', $image_locations, true, essb_array_value('image_location', $designSetup));
essb5_draw_input_option('image_width', esc_html__('Image Width', 'essb'), esc_html__('The value is optional but recommended if you plan to use SVG files. You need to fill value with the measuring unit (ex.: 100px, 50%)', 'essb'), false, true, essb_array_value('image_width', $designSetup));
essb5_draw_input_option('image_height', esc_html__('Image Height', 'essb'), esc_html__('The value is optional but recommended if you plan to use SVG files. You need to fill value with the measuring unit (ex.: 100px, 50%)', 'essb'), false, true, essb_array_value('image_height', $designSetup));
essb5_draw_input_option('image_padding', esc_html__('Image Area Padding', 'essb'), '', false, true, essb_array_value('image_padding', $designSetup));
$image_area_width = array('' => esc_html__('Default', 'essb'), '25' => '25%', '30' => '30%', '40' => '40%', '50' => '50%');
essb5_draw_select_option('image_area_width', esc_html__('Image Area Width', 'essb'), '', $image_area_width, true, essb_array_value('image_area_width', $designSetup));

essb5_draw_heading(esc_html__('Font Style & Size', 'essb'), '5');
essb5_draw_input_option('heading_fontsize', esc_html__('Heading Font Size', 'essb'), '', false, true, essb_array_value('heading_fontsize', $designSetup));
essb5_draw_input_option('text_fontsize', esc_html__('Custom Content Font Size', 'essb'), '', false, true, essb_array_value('text_fontsize', $designSetup));
essb5_draw_input_option('footer_fontsize', esc_html__('Footer Font Size', 'essb'), '', false, true, essb_array_value('footer_fontsize', $designSetup));
essb5_draw_input_option('input_fontsize', esc_html__('Input Fields Font Size', 'essb'), '', false, true, essb_array_value('input_fontsize', $designSetup));
essb5_draw_input_option('button_fontsize', esc_html__('Button Font Size', 'essb'), '', false, true, essb_array_value('button_fontsize', $designSetup));
$font_weight_selector = array('' => esc_html__('Theme default', 'essb'), '400' => esc_html__('Normal', 'essb'), '700' => esc_html__('Bold', 'essb'));
essb5_draw_select_option('heading_fontweight', esc_html__('Heading Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('heading_fontweight', $designSetup));
essb5_draw_select_option('text_fontweight', esc_html__('Custom Content Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('text_fontweight', $designSetup));
essb5_draw_select_option('footer_fontweight', esc_html__('Footer Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('footer_fontweight', $designSetup));
essb5_draw_select_option('input_fontweight', esc_html__('Input Fields Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('input_fontweight', $designSetup));
essb5_draw_select_option('button_fontweight', esc_html__('Button Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('button_fontweight', $designSetup));

$alignment_selector = array('' => esc_html__('Theme Default', 'essb'), 'left' => esc_html__('Left', 'essb'), 'center' => esc_html__('Center', 'essb'), 'right' => esc_html__('Right', 'essb'));
essb5_draw_select_option('align', esc_html__('Content Alignment', 'essb'), '', $alignment_selector, true, essb_array_value('align', $designSetup));

essb5_draw_heading(esc_html__('Colors', 'essb'), '5');

essb5_draw_color_option('bgcolor', esc_html__('Background color', 'essb'), '', false, true, essb_array_value('bgcolor', $designSetup));
essb5_draw_color_option('bgcolor2', esc_html__('Secondary background color', 'essb'), esc_html__('Select in addition secondary background color if you wish to create a gradient effect', 'essb'), false, true, essb_array_value('bgcolor2', $designSetup));
essb5_draw_color_option('image_bgcolor', esc_html__('Image Area Background color', 'essb'), esc_html__('Used only when you are showing image on the form', 'essb'), false, true, essb_array_value('image_bgcolor', $designSetup));
essb5_draw_color_option('textcolor', esc_html__('Text color', 'essb'), '', false, true, essb_array_value('textcolor', $designSetup));
essb5_draw_color_option('headingcolor', esc_html__('Heading color', 'essb'), '', false, true, essb_array_value('headingcolor', $designSetup));
essb5_draw_color_option('footercolor', esc_html__('Footer color', 'essb'), '', false, true, essb_array_value('footercolor', $designSetup));
essb5_draw_color_option('fields_bg', esc_html__('Email/Name fields background color', 'essb'), '', false, true, essb_array_value('fields_bg', $designSetup));
essb5_draw_color_option('fields_text', esc_html__('Email/Name fields text color', 'essb'), '', false, true, essb_array_value('fields_text', $designSetup));
essb5_draw_color_option('button_bg', esc_html__('Subscribe button background', 'essb'), '', false, true, essb_array_value('button_bg', $designSetup));
essb5_draw_color_option('button_text', esc_html__('Subscribe button text', 'essb'), '', false, true, essb_array_value('button_text', $designSetup));
essb5_draw_color_option('border_color', esc_html__('Border Color', 'essb'), '', true, true, essb_array_value('border_color', $designSetup));
essb5_draw_input_option('border_width', esc_html__('Border Width', 'essb'), '', false, true, essb_array_value('border_width', $designSetup));
essb5_draw_input_option('border_radius', esc_html__('Border Radius', 'essb'), '', false, true, essb_array_value('border_radius', $designSetup));
essb5_draw_input_option('padding', esc_html__('Form Padding', 'essb'), esc_html__('The padding values should be filled with the measuring unit (ex.: 10px or 10px 20px or 5%). When nothing is filled plugin will apply a default 30px padding from all sides. If you wish to remove the padding you can fill 0', 'essb'), false, true, essb_array_value('padding', $designSetup));
essb5_draw_color_option('glow_color', esc_html__('Glow Color', 'essb'), '', true, true, essb_array_value('glow_color', $designSetup));
essb5_draw_input_option('glow_size', esc_html__('Glow Size', 'essb'), esc_html__('The value should be numeric without the measuring unit (ex.: 10)', 'essb'), false, true, essb_array_value('glow_size', $designSetup));
