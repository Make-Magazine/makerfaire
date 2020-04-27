<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

$connector = essb_option_value('subscribe_connector');
if ($connector != 'mymail' && $connector != 'mailster' && $connector != 'mailpoet' && $connector != 'conversio') {
	essb5_draw_heading(esc_html__('Custom Form List', 'essb'), '5');
	essb5_draw_input_option('subscribe_mc_customlist4', esc_html__('List ID', 'essb'), esc_html__('Optional you can set a different list for this form only. To get the list ID you can follow the instructions on the subscribe connector.', 'essb'), true);
}


essb5_draw_heading(esc_html__('Customize Form Texts', 'essb'), '5');
essb5_draw_switch_option('subscribe_mc_namefield4', esc_html__('Display name field', 'essb'), esc_html__('Activate this option to allow customers enter their name.', 'essb'));
essb5_draw_input_option('subscribe_mc_title4', esc_html__('Custom Form Title Text', 'essb'), esc_html__('Setup your own custom text for the title (ex.: Join Our List)', 'essb'), true);
essb5_draw_file_option('subscribe_mc_image4', esc_html__('Choose Image', 'essb'), esc_html__('Select image that will appear on the top or left part of the subscribe form', 'essb'));
essb5_draw_select_option('subscribe_mc_imagealign4', esc_html__('Image Placement', 'essb'), '', array("left" => esc_html__("Left side", "essb"), "right" => esc_html__("Right side", "essb")));

essb5_draw_editor_option('subscribe_mc_text4', esc_html__('Form Text', 'essb'), esc_html__('Customize the default form text (ex.: Subscribe to our list and get awesome news.)', 'essb'));
essb5_draw_input_option('subscribe_mc_name4', esc_html__('Name Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_email4', esc_html__('Email Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_button4', esc_html__('Subscribe Button Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_footer4', esc_html__('Footer Text', 'essb'), esc_html__('Add a footer text that will appear below form (ex.: We respect your privacy'), true);
essb5_draw_input_option('subscribe_mc_success4', esc_html__('Success subscribe messsage', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_error4', esc_html__('Error message', 'essb'), '', true);

essb5_draw_heading(esc_html__('Customize Colors', 'essb'), '5');
essb5_draw_switch_option('activate_mailchimp_customizer4', esc_html__('Activate Color Changing', 'essb'), esc_html__('Set option to Yes for generating of color change', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor4', esc_html__('Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor4', esc_html__('Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor4_bottom', esc_html__('Bottom Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor4_bottom', esc_html__('Bottom Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovercolor4', esc_html__('Accent color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovertextcolor4', esc_html__('Accent Text Color', 'essb'));
essb5_draw_color_option('customizer_subscribe_emailcolor4', esc_html__('Email/Name field background color', 'essb'));