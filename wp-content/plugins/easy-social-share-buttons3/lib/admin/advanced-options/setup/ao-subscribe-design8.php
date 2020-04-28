<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_heading(__('Customize Form Texts', 'essb'), '5');
essb5_draw_switch_option('subscribe_mc_namefield8', __('Display name field', 'essb'), __('Activate this option to allow customers enter their name.', 'essb'));
essb5_draw_input_option('subscribe_mc_title8', __('Custom Form Title Text', 'essb'), __('Setup your own custom text for the title (ex.: Join Our List)', 'essb'), true);
essb5_draw_editor_option('subscribe_mc_text8', __('Form Text', 'essb'), __('Customize the default form text (ex.: Subscribe to our list and get awesome news.)', 'essb'));
essb5_draw_input_option('subscribe_mc_name8', __('Name Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_email8', __('Email Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_button8', __('Subscribe Button Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_footer8', __('Footer Text', 'essb'), __('Add a footer text that will appear below form (ex.: We respect your privacy'), true);
essb5_draw_input_option('subscribe_mc_success8', __('Success subscribe messsage', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_error8', __('Error message', 'essb'), '', true);

essb5_draw_heading(__('Customize Colors', 'essb'), '5');
essb5_draw_switch_option('activate_mailchimp_customizer8', __('Activate Color Changing', 'essb'), __('Set option to Yes for generating of color change', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor8', __('Background color #1', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor82', __('Background color #2', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor8', __('Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor8_bottom', __('Bottom Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor8_bottom', __('Bottom Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_buttoncolor8', __('Subscribe Button Color', 'essb'));
essb5_draw_color_option('customizer_subscribe_buttontextcolor8', __('Subscribe Button Text Color', 'essb'));

