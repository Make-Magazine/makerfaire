<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('affs_active', esc_html__('Append Affiliate ID to shared address', 'essb'), esc_html__('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated. You need to have installed Affiliates plugin to use it', 'essb'));
essb5_draw_switch_option('affs_active_shortcode', esc_html__('Append Affiliate ID to shortcodes', 'essb'), esc_html__('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated when shortcode has a custom url parameter.', 'essb'));

essb_advancedopts_section_close();

