<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_help('', esc_html__('The plugin analytics, if activated, collects anonymous data for click over share buttons. Based on that data you can select the networks and positions that works best on your site. As the data is logged with each button click. That makes it usually to be different from the official share counter.', 'essb'), array('Learn more for data in analytics' => 'https://docs.socialsharingplugin.com/knowledgebase/how-built-in-analytics-works-and-what-data-is-collected/'));
essb5_draw_switch_option('stats_active', esc_html__('Activate analytics and collect data for click over buttons', 'essb'), esc_html__('Build-in analytics is exteremly powerful tool which will let you to track how your visitors interact with share buttons. Get reports by positions, device type, social networks, for periods or for content', 'essb'));

essb_advancedopts_section_close();