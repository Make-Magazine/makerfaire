<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('affwp_active', esc_html__('Append Affiliate ID to shared address', 'essb'), esc_html__('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated. You need to have installed AffiliateWP plugin to use it', 'essb'));
$listOfOptions = array("id" => "User ID", "name" => "Username");
essb5_draw_select_option('affwp_active_mode', esc_html__('ID Append Mode', 'essb'), esc_html__('Choose between usage of user id or username when you add affiliate id to outgoing shares.', 'essb'), $listOfOptions);
essb5_draw_switch_option('affwp_active_shortcode', esc_html__('Append Affiliate ID to shortcodes', 'essb'), esc_html__('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated when shortcode has a custom url parameter.', 'essb'));
essb5_draw_switch_option('affwp_active_pretty', esc_html__('Use pretty affiliate URLs', 'essb'), esc_html__('Activate this option if you already have make it active inside AffiliateWP to allow Easy Social Share Buttons generate pretty affiliate URLs.', 'essb'));

essb_advancedopts_section_close();