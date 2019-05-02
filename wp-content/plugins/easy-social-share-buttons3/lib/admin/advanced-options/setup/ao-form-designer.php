<?php
$loadingOptions = isset($_REQUEST['loadingOptions']) ? $_REQUEST['loadingOptions'] : array();

$design = isset($loadingOptions['design']) ? $loadingOptions['design'] : '';
$designSetup = essb5_get_form_settings($design);

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options_forms');
}

echo '<input type="hidden" name="form_design_id" id="form_design_id" value="'.$design.'"/>';

essb5_draw_input_option('name', __('Form Name', 'essb'), __('Enter form name that will appear inside the design lists. Use this name for easy recognition of the from in the list', 'essb'), true, true, essb_array_value('name', $designSetup));

essb5_draw_heading(__('Form Texts', 'essb'), '5');
essb5_draw_input_option('title', __('Heading', 'essb'), '', true, true, essb_array_value('title', $designSetup));
essb5_draw_editor_option('text', __('Form custom content', 'essb'), __('HTML code and shortcodes are supported', 'essb'), 'htmlmixed', true, essb_array_value('text', $designSetup));
essb5_draw_input_option('footer', __('Footer Text', 'essb'), '', true, true, essb_array_value('footer', $designSetup));

essb5_draw_input_option('name_placeholder', __('Name field text', 'essb'), '', true, true, essb_array_value('name_placeholder', $designSetup));
essb5_draw_input_option('email_placeholder', __('Email field text', 'essb'), '', true, true, essb_array_value('email_placeholder', $designSetup));
essb5_draw_input_option('button_placeholder', __('Subscribe button text', 'essb'), '', true, true, essb_array_value('button_placeholder', $designSetup));
essb5_draw_switch_option('add_name', __('Include Name Field', 'essb'), '', true, essb_array_value('add_name', $designSetup));
essb5_draw_input_option('error_message', __('Error Subscribe Message', 'essb'), '', true, true, essb_array_value('error_message', $designSetup));
essb5_draw_input_option('ok_message', __('Success Subscribe Message', 'essb'), '', true, true, essb_array_value('ok_message', $designSetup));

essb5_draw_heading(__('Include Image Inside Form', 'essb'), '5');
essb5_draw_file_option('image', __('Select image for the form', 'essb'), __('Optional you can choose an image that will appear inside the form. The image location can be selected from the menu blow', 'essb'), true, essb_array_value('image', $designSetup));
$image_locations = array('' => __('Do not show image', 'essb'), 'left' => __('On the left', 'essb'), 'right' => __('On the right', 'essb'), 'top' => __('At the top above heading', 'essb'), __('below_heading') => __('At the top between heading and content', 'essb'), 'background' => __('As form background image', 'essb'));
essb5_draw_select_option('image_location', __('Image Appearance', 'essb'), '', $image_locations, true, essb_array_value('image_location', $designSetup));
essb5_draw_input_option('image_width', __('Image Width', 'essb'), __('The value is optional but recommended if you plan to use SVG files. You need to fill value with the measuring unit (ex.: 100px, 50%)', 'essb'), false, true, essb_array_value('image_width', $designSetup));
essb5_draw_input_option('image_height', __('Image Height', 'essb'), __('The value is optional but recommended if you plan to use SVG files. You need to fill value with the measuring unit (ex.: 100px, 50%)', 'essb'), false, true, essb_array_value('image_height', $designSetup));
essb5_draw_input_option('image_padding', __('Image Area Padding', 'essb'), '', false, true, essb_array_value('image_padding', $designSetup));
$image_area_width = array('' => __('Default', 'essb'), '25' => '25%', '30' => '30%', '40' => '40%', '50' => '50%');
essb5_draw_select_option('image_area_width', __('Image Area Width', 'essb'), '', $image_area_width, true, essb_array_value('image_area_width', $designSetup));

essb5_draw_heading(__('Font Style & Size', 'essb'), '5');
essb5_draw_input_option('heading_fontsize', __('Heading Font Size', 'essb'), '', false, true, essb_array_value('heading_fontsize', $designSetup));
essb5_draw_input_option('text_fontsize', __('Custom Content Font Size', 'essb'), '', false, true, essb_array_value('text_fontsize', $designSetup));
essb5_draw_input_option('footer_fontsize', __('Footer Font Size', 'essb'), '', false, true, essb_array_value('footer_fontsize', $designSetup));
essb5_draw_input_option('input_fontsize', __('Input Fields Font Size', 'essb'), '', false, true, essb_array_value('input_fontsize', $designSetup));
essb5_draw_input_option('button_fontsize', __('Button Font Size', 'essb'), '', false, true, essb_array_value('button_fontsize', $designSetup));
$font_weight_selector = array('' => __('Theme default', 'essb'), '400' => __('Normal', 'essb'), '700' => __('Bold', 'essb'));
essb5_draw_select_option('heading_fontweight', __('Heading Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('heading_fontweight', $designSetup));
essb5_draw_select_option('text_fontweight', __('Custom Content Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('text_fontweight', $designSetup));
essb5_draw_select_option('footer_fontweight', __('Footer Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('footer_fontweight', $designSetup));
essb5_draw_select_option('input_fontweight', __('Input Fields Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('input_fontweight', $designSetup));
essb5_draw_select_option('button_fontweight', __('Button Font Weight', 'essb'), '', $font_weight_selector, true, essb_array_value('button_fontweight', $designSetup));

$alignment_selector = array('' => __('Theme Default', 'essb'), 'left' => __('Left', 'essb'), 'center' => __('Center', 'essb'), 'right' => __('Right', 'essb'));
essb5_draw_select_option('align', __('Content Alignment', 'essb'), '', $alignment_selector, true, essb_array_value('align', $designSetup));

essb5_draw_heading(__('Colors', 'essb'), '5');

essb5_draw_color_option('bgcolor', __('Background color', 'essb'), '', false, true, essb_array_value('bgcolor', $designSetup));
essb5_draw_color_option('bgcolor2', __('Secondary background color', 'essb'), __('Select in addition secondary background color if you wish to create a gradient effect', 'essb'), false, true, essb_array_value('bgcolor2', $designSetup));
essb5_draw_color_option('image_bgcolor', __('Image Area Background color', 'essb'), __('Used only when you are showing image on the form', 'essb'), false, true, essb_array_value('image_bgcolor', $designSetup));
essb5_draw_color_option('textcolor', __('Text color', 'essb'), '', false, true, essb_array_value('textcolor', $designSetup));
essb5_draw_color_option('headingcolor', __('Heading color', 'essb'), '', false, true, essb_array_value('headingcolor', $designSetup));
essb5_draw_color_option('footercolor', __('Footer color', 'essb'), '', false, true, essb_array_value('footercolor', $designSetup));
essb5_draw_color_option('fields_bg', __('Email/Name fields background color', 'essb'), '', false, true, essb_array_value('fields_bg', $designSetup));
essb5_draw_color_option('fields_text', __('Email/Name fields text color', 'essb'), '', false, true, essb_array_value('fields_text', $designSetup));
essb5_draw_color_option('button_bg', __('Subscribe button background', 'essb'), '', false, true, essb_array_value('button_bg', $designSetup));
essb5_draw_color_option('button_text', __('Subscribe button text', 'essb'), '', false, true, essb_array_value('button_text', $designSetup));
essb5_draw_color_option('border_color', __('Border Color', 'essb'), '', true, true, essb_array_value('border_color', $designSetup));
essb5_draw_input_option('border_width', __('Border Width', 'essb'), '', false, true, essb_array_value('border_width', $designSetup));
essb5_draw_input_option('border_radius', __('Border Radius', 'essb'), '', false, true, essb_array_value('border_radius', $designSetup));
essb5_draw_input_option('padding', __('Form Padding', 'essb'), __('The padding values should be filled with the measuring unit (ex.: 10px or 10px 20px or 5%). When nothing is filled plugin will apply a default 30px padding from all sides. If you wish to remove the padding you can fill 0', 'essb'), false, true, essb_array_value('padding', $designSetup));
essb5_draw_color_option('glow_color', __('Glow Color', 'essb'), '', true, true, essb_array_value('glow_color', $designSetup));
essb5_draw_input_option('glow_size', __('Glow Size', 'essb'), __('The value should be numeric without the measuring unit (ex.: 10)', 'essb'), false, true, essb_array_value('glow_size', $designSetup));
