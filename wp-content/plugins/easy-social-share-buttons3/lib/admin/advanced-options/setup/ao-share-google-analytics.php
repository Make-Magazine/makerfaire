<?php

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

function essb3_text_analytics() {
	$text = '
	You can
	visit
	<a href="https://support.google.com/analytics/answer/1033867?hl=en"
	target="_blank">this page</a>
	for more information on how to use and generate these parameters.
	<br />
	To include the social network into parameters use the following code
	<b>{network}</b>
	. When that code is reached it will be replaced with the network name (example: facebook). An example campaign trakcing code include network will look like this utm_source=essb_settings&utm_medium=needhelp&utm_campaign={network} - in this configuration when you press Facebook button {network} will be replaced with facebook, if you press Twitter button it will be replaced with twitter.
	To include the post title into parameters use the following code
	<b>{title}</b>
	. When that code is reached it will be replaced with the post title.
	';
	return $text;
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('activate_ga_tracking', esc_html__('Activate Google Analytics Tracking', 'essb'), esc_html__('Activate tracking of social share buttons click using Google Analytics (requires Google Analytics to be active on this site).', 'essb'));
$listOfOptions = array ("simple" => "Simple", "extended" => "Extended" );
essb5_draw_select_option('ga_tracking_mode', esc_html__('Google Analytics Tracking Method', 'essb'), esc_html__('Choose your tracking method: Simple - track clicks by social networks, Extended - track clicks on separate social networks by button display position.', 'essb'), $listOfOptions);
essb5_draw_switch_option('activate_ga_layers', esc_html__('Use Google Tag Manager Data Layer Event Tracking', 'essb'), esc_html__('Activate this option if you use Google Tag Manager to add analytics code and you did not setup automatic event tracking.', 'essb'));
essb5_draw_input_option('activate_ga_campaign_tracking', esc_html__('Add Custom Campaign parameters to your URLs', 'essb'), esc_html__('Paste your custom campaign parameters in this field and they will be automatically added to shared addresses on social networks. Please note as social networks count shares via URL as unique key this option is not compatible with active social share counters as it will make the start from zero.', 'essb'), true);
essb5_draw_hint('', essb3_text_analytics());

essb_advancedopts_section_close();

