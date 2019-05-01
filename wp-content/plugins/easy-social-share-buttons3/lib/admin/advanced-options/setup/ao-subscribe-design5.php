<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_heading(__('Customize Form Texts', 'essb'), '5');
essb5_draw_switch_option('subscribe_mc_namefield5', __('Display name field', 'essb'), __('Activate this option to allow customers enter their name.', 'essb'));
essb5_draw_input_option('subscribe_mc_title5', __('Custom Form Title Text', 'essb'), __('Setup your own custom text for the title (ex.: Join Our List)', 'essb'), true);
essb5_draw_editor_option('subscribe_mc_text5', __('Form Text', 'essb'), __('Customize the default form text (ex.: Subscribe to our list and get awesome news.)', 'essb'));
essb5_draw_input_option('subscribe_mc_name5', __('Name Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_email5', __('Email Field Placeholder Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_button5', __('Subscribe Button Text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_footer5', __('Footer Text', 'essb'), __('Add a footer text that will appear below form (ex.: We respect your privacy'), true);
essb5_draw_input_option('subscribe_mc_success5', __('Success subscribe messsage', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_error5', __('Error message', 'essb'), '', true);

essb5_draw_heading(__('Customize Colors', 'essb'), '5');
essb5_draw_switch_option('activate_mailchimp_customizer5', __('Activate Color Changing', 'essb'), __('Set option to Yes for generating of color change', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor5', __('Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor5', __('Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor5_bottom', __('Bottom Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor5_bottom', __('Bottom Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovercolor5', __('Accent color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovertextcolor5', __('Accent Text Color', 'essb'));
essb5_draw_color_option('customizer_subscribe_emailcolor5', __('Email/Name field background color', 'essb'));