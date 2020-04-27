<?php 
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('conversions_lite_run', esc_html__('Track social share buttons conversion', 'essb'), esc_html__('Share buttons conversion is an easy way to manage and optimize display of share buttons on your site. Once active plugin will collect data for each displayed position and social networks along with click on each. All that data you will see in easy to understand dashboard. With such information you can easy make a decision of what you really need on your site. You have also access to past 7 days historical data.', 'essb'));

essb_advancedopts_section_close();