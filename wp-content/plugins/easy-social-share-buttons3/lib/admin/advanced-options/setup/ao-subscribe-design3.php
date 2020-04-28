<?php

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_heading(__('Customize Form Texts', 'essb'), '5');
essb5_draw_switch_option('subscribe_mc_namefield3', __('Display name field', 'essb'), __('Activate this option to allow customers enter their name.', 'essb'));
essb5_draw_input_option('subscribe_mc_title3', __('Custom Form Title Text', 'essb'), __('Setup your own custom text for the title (ex.: Join Our List)', 'essb'), true);
essb5_draw_file_option('subscribe_mc_image3', __('Choose Image', 'essb'), __('Select image that will appear on the top or left part of the subscribe form', 'essb'));
essb5_draw_select_option('subscribe_mc_imagealign3', __('Image Placement', 'essb'), '', array("left" => __("Left side", "essb"), "right" => __("Right side", "essb")));

essb5_draw_editor_option('subscribe_mc_text3', __('Form Text', 'essb'), __('Customize the default form text (ex.: Subscribe to our list and get awesome news.)', 'essb'));
essb5_draw_input_option('subscribe_mc_name3', __('Name Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_email3', __('Email Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_button3', __('Subscribe Button Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_footer3', __('Footer Text', 'essb'), __('Add a footer text that will appear below form (ex.: We respect your privacy'), true);
essb5_draw_input_option('subscribe_mc_success3', __('Success subscribe messsage', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_error3', __('Error message', 'essb'), '', true);

essb5_draw_heading(__('Customize Colors', 'essb'), '5');
essb5_draw_switch_option('activate_mailchimp_customizer3', __('Activate Color Changing', 'essb'), __('Set option to Yes for generating of color change', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor3', __('Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor3', __('Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor3_bottom', __('Bottom Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor3_bottom', __('Bottom Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovercolor3', __('Accent color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovertextcolor3', __('Accent Text Color', 'essb'));
essb5_draw_color_option('customizer_subscribe_emailcolor3', __('Email/Name field background color', 'essb'));

?>