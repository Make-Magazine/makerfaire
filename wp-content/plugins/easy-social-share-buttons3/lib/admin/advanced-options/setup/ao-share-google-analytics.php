<?php

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb_advanced_options_relation('activate_utm', 'switch', array('activate_utm_source', 'activate_utm_medium', 'activate_utm_name'));
essb_advanced_options_relation('activate_ga_tracking', 'switch', array('ga_tracking_mode', 'activate_ga_layers'));

essb5_draw_switch_option('activate_ga_tracking', esc_html__('Activate Google Analytics tracking', 'essb'), esc_html__('Enable tracking via universal analytics or the Google Tag Manager. The analytics code should already be present on the page (the plugin will only send the events).', 'essb'));
essb5_draw_field_group_open();
$listOfOptions = array ("simple" => "Simple", "extended" => "Extended" );
essb5_draw_select_option('ga_tracking_mode', esc_html__('Google Analytics Tracking Method', 'essb'), esc_html__('Choose your tracking method: Simple - track clicks by social networks, Extended - track clicks on separate social networks by button display position.', 'essb'), $listOfOptions);
essb5_draw_switch_option('activate_ga_layers', esc_html__('Use Google Tag Manager Data Layer Event Tracking', 'essb'), esc_html__('Activate this option if you use Google Tag Manager to add analytics code and you did not setup automatic event tracking.', 'essb'));
essb5_draw_field_group_close();

// since 8.0 new Google UTM
essb5_draw_switch_option('activate_utm', esc_html__('Enable UTM tracking', 'essb'), esc_html__('Add UTM parameters to the shared social links.', 'essb'));
essb5_draw_field_group_open();
essb5_draw_input_option('activate_utm_source', esc_html__('Campaign UTM source', 'essb'), esc_html__('The value of the UTM source parameter is added to the social share links. Available variables: {network} for the social network or {title} for the post title. Default if blank: {network}', 'essb'), true);
essb5_draw_input_option('activate_utm_medium', esc_html__('Campaign UTM medium', 'essb'), esc_html__('The value of the UTM medium parameter is added to the social share links. Available variables: {network} for the social network or {title} for the post title. Default if blank: social', 'essb'), true);
essb5_draw_input_option('activate_utm_name', esc_html__('Campaign UTM name', 'essb'), esc_html__('The value of the UTM name parameter is added to the social share links. Available variables: {network} for the social network or {title} for the post title. Default if blank: EasySocialShareButtons', 'essb'), true);
essb5_draw_field_group_close();

essb5_draw_switch_option('activate_ga_ntg_tracking', esc_html__('Activate News Tagging Guide (NTG)', 'essb'), esc_html__('Measuring News Tagging Guide (NTG) conversions', 'essb'));


essb_advancedopts_section_close();

