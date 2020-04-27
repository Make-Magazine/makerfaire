<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}
$connector = essb_option_value('subscribe_connector');
if ($connector != 'mymail' && $connector != 'mailster' && $connector != 'mailpoet' && $connector != 'conversio') {
	essb5_draw_heading(esc_html__('Custom Form List', 'essb'), '5');
	essb5_draw_input_option('subscribe_mc_customlist7', esc_html__('List ID', 'essb'), esc_html__('Optional you can set a different list for this form only. To get the list ID you can follow the instructions on the subscribe connector.', 'essb'), true);
}

essb5_draw_heading(esc_html__('Customize Form Texts', 'essb'), '5');
essb5_draw_switch_option('subscribe_mc_namefield7', esc_html__('Display name field', 'essb'), esc_html__('Activate this option to allow customers enter their name.', 'essb'));
essb5_draw_input_option('subscribe_mc_title7', esc_html__('Custom Form Title Text', 'essb'), esc_html__('Setup your own custom text for the title (ex.: Join Our List)', 'essb'), true);
essb5_draw_editor_option('subscribe_mc_text7', esc_html__('Form Text', 'essb'), esc_html__('Customize the default form text (ex.: Subscribe to our list and get awesome news.)', 'essb'));
essb5_draw_input_option('subscribe_mc_name7', esc_html__('Name Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_email7', esc_html__('Email Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_button7', esc_html__('Subscribe Button Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_footer7', esc_html__('Footer Text', 'essb'), esc_html__('Add a footer text that will appear below form (ex.: We respect your privacy'), true);
essb5_draw_input_option('subscribe_mc_success7', esc_html__('Success subscribe messsage', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_error7', esc_html__('Error message', 'essb'), '', true);

essb5_draw_heading(esc_html__('Customize Colors', 'essb'), '5');
essb5_draw_switch_option('activate_mailchimp_customizer7', esc_html__('Activate Color Changing', 'essb'), esc_html__('Set option to Yes for generating of color change', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor7', esc_html__('Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor7', esc_html__('Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor7_bottom', esc_html__('Bottom Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor7_bottom', esc_html__('Bottom Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovercolor7', esc_html__('Accent color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovertextcolor7', esc_html__('Accent Text Color', 'essb'));
essb5_draw_color_option('customizer_subscribe_emailcolor7', esc_html__('Email/Name field background color', 'essb'));
essb5_draw_switch_option('customizer_subscribe_noborder7', esc_html__('Remove Form Border', 'essb'), '');
