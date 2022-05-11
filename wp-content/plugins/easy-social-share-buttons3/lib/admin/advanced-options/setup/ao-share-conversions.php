<?php 
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('conversions_lite_run', esc_html__('Activate conversions tracking', 'essb'), esc_html__('Activate the option to start tracking and collecting information about share buttons\' conversions. This data is fully anonymous and no personal details are collected or stored (you don\'t need to ask for permission). The conversion dashboard report will appear in the Analytics menu.', 'essb'));

essb_advancedopts_section_close();