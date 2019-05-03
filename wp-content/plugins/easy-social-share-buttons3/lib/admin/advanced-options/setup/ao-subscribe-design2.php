<?php

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_heading(__('Customize Form Texts', 'essb'), '5');
essb5_draw_switch_option('subscribe_mc_namefield2', __('Display name field', 'essb'), __('Activate this option to allow customers enter their name.', 'essb'));
essb5_draw_input_option('subscribe_mc_title2', __('Custom Form Title Text', 'essb'), __('Setup your own custom text for the title (ex.: Join Our List)', 'essb'), true);
essb5_draw_editor_option('subscribe_mc_text2', __('Form Text', 'essb'), __('Customize the default form text (ex.: Subscribe to our list and get awesome news.)', 'essb'));
essb5_draw_input_option('subscribe_mc_name2', __('Name Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_email2', __('Email Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_button2', __('Subscribe Button Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_footer2', __('Footer Text', 'essb'), __('Add a footer text that will appear below form (ex.: We respect your privacy'), true);
essb5_draw_input_option('subscribe_mc_success2', __('Success subscribe messsage', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_error2', __('Error message', 'essb'), '', true);

essb5_draw_heading(__('Customize Colors', 'essb'), '5');
essb5_draw_switch_option('activate_mailchimp_customizer2', __('Activate Color Changing', 'essb'), __('Set option to Yes for generating of color change', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor2', __('Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor2', __('Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovercolor2', __('Accent color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovertextcolor2', __('Accent Text Color', 'essb'));
essb5_draw_color_option('customizer_subscribe_emailcolor2', __('Email/Name field background color', 'essb'));
essb5_draw_switch_option('customizer_subscribe_noborder2', __('Remove form border', 'essb'));

?>