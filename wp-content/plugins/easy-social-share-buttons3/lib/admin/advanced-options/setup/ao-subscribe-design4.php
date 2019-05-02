<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_heading(__('Customize Form Texts', 'essb'), '5');
essb5_draw_switch_option('subscribe_mc_namefield4', __('Display name field', 'essb'), __('Activate this option to allow customers enter their name.', 'essb'));
essb5_draw_input_option('subscribe_mc_title4', __('Custom Form Title Text', 'essb'), __('Setup your own custom text for the title (ex.: Join Our List)', 'essb'), true);
essb5_draw_file_option('subscribe_mc_image4', __('Choose Image', 'essb'), __('Select image that will appear on the top or left part of the subscribe form', 'essb'));
essb5_draw_select_option('subscribe_mc_imagealign4', __('Image Placement', 'essb'), '', array("left" => __("Left side", "essb"), "right" => __("Right side", "essb")));

essb5_draw_editor_option('subscribe_mc_text4', __('Form Text', 'essb'), __('Customize the default form text (ex.: Subscribe to our list and get awesome news.)', 'essb'));
essb5_draw_input_option('subscribe_mc_name4', __('Name Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_email4', __('Email Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_button4', __('Subscribe Button Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_footer4', __('Footer Text', 'essb'), __('Add a footer text that will appear below form (ex.: We respect your privacy'), true);
essb5_draw_input_option('subscribe_mc_success4', __('Success subscribe messsage', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_error4', __('Error message', 'essb'), '', true);

essb5_draw_heading(__('Customize Colors', 'essb'), '5');
essb5_draw_switch_option('activate_mailchimp_customizer4', __('Activate Color Changing', 'essb'), __('Set option to Yes for generating of color change', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor4', __('Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor4', __('Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor4_bottom', __('Bottom Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor4_bottom', __('Bottom Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovercolor4', __('Accent color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovertextcolor4', __('Accent Text Color', 'essb'));
essb5_draw_color_option('customizer_subscribe_emailcolor4', __('Email/Name field background color', 'essb'));