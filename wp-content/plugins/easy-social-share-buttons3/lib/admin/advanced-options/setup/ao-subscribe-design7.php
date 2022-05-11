<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}
$connector = essb_option_value('subscribe_connector');
if ($connector != 'mymail' && $connector != 'mailster' && $connector != 'mailpoet' && $connector != 'conversio') {
    essb5_draw_heading(esc_html__('Custom Form List', 'essb'), '7', '', '', '<i class="fa fa-database"></i>');
    essb5_draw_field_group_open();
    echo '<div class="essb-options-helprow"><div class="help-details" style="display: block;">';
    echo '<div class="desc noButton">Customize the connected list for this form design only. Fill only if the list will be different than the general. Otherwise, leave blank the fields in the section.</div>';
    echo '</div></div>';
    
	essb5_draw_input_option('subscribe_mc_customlist7', esc_html__('List ID', 'essb'), '', true);
	
	if ($connector == 'mailchimp') {
	    essb5_draw_input_option('subscribe_mc_customtags7', esc_html__('Tags', 'essb'), '', true);
	}
	essb5_draw_field_group_close();
}

essb5_draw_heading(esc_html__('Form Texts', 'essb'), '7', '', '', '<i class="fa fa-align-left"></i>');
essb5_draw_field_group_open();
essb5_draw_switch_option('subscribe_mc_namefield7', esc_html__('Display name field', 'essb'), '');
essb5_draw_input_option('subscribe_mc_title7', esc_html__('Heading', 'essb'), '', true);
essb5_draw_editor_option('subscribe_mc_text7', esc_html__('Form custom content', 'essb'), esc_html__('HTML code and shortcodes are supported', 'essb'));
essb5_draw_input_option('subscribe_mc_name7', esc_html__('Name field placeholder text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_email7', esc_html__('Email field placeholder text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_button7', esc_html__('Subscribe button text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_footer7', esc_html__('Footer text', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_success7', esc_html__('Success subscribe messsage', 'essb'), '', true);
essb5_draw_input_option('subscribe_mc_error7', esc_html__('Error message', 'essb'), '', true);
essb5_draw_field_group_close();

essb5_draw_heading(esc_html__('Colors & General Styles', 'essb'), '7', '', '', '<i class="fa fa-paint-brush"></i>');
essb5_draw_field_group_open();
essb5_draw_switch_option('activate_mailchimp_customizer7', esc_html__('Enable color changing', 'essb'), '');
essb5_draw_color_option('customizer_subscribe_bgcolor7', esc_html__('Background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor7', esc_html__('Text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_bgcolor7_bottom', esc_html__('Bottom background color', 'essb'));
essb5_draw_color_option('customizer_subscribe_textcolor7_bottom', esc_html__('Bottom text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovercolor7', esc_html__('Accent color', 'essb'));
essb5_draw_color_option('customizer_subscribe_hovertextcolor7', esc_html__('Accent text color', 'essb'));
essb5_draw_color_option('customizer_subscribe_emailcolor7', esc_html__('Email/Name field background color', 'essb'));
essb5_draw_switch_option('customizer_subscribe_noborder7', esc_html__('Remove form border', 'essb'), '');
essb5_draw_field_group_close();

