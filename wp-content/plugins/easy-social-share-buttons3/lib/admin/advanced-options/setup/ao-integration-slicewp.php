<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('slicewp_active', esc_html__('Append affiliate ID to shared address', 'essb'), esc_html__('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated. You need to have installed SliceWP plugin to use it', 'essb'));
essb5_draw_switch_option('slicewp_active_shortcode', esc_html__('Append affiliate ID to shortcodes', 'essb'), esc_html__('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated when shortcode has a custom url parameter.', 'essb'));
essb5_draw_switch_option('slicewp_bridge_short', esc_html__('Generate separate short URL when affiliate ID is added', 'essb'), esc_html__('The option will integrate the affiliate ID checks inside short URL generation. This will help to build a separate short URL for each of the affiliate users. The option works only if the short URL option inside the plugin is enabled.', 'essb'));


essb_advancedopts_section_close();