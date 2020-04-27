<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_help(esc_html__('Social Metrics data collection require to have share counters active on your site. All data will be updated and stored inside metrics dashboard on each share counter update. Metrics data cannot be collected if you use real time share counters.', 'essb'));
essb5_draw_switch_option( 'esml_active', esc_html__('Activate social metrics data collection', 'essb'), esc_html__('Switch this option to yes to start collecting data for your shares. Data collection requires to have share counters active on your site with mode different than real time. All data will be updated with each counter update request.', 'essb'));
$data_history = array();
$data_history['1'] = esc_html__('1 day', 'essb');
$data_history['7'] = esc_html__('1 week', 'essb');
$data_history['14'] = esc_html__('2 weeks', 'essb');
$data_history['30'] = esc_html__('1 month', 'essb');
essb5_draw_select_option( 'esml_history', esc_html__('Keep history for', 'essb'), esc_html__('Choose how long plugin to store history data inside cache. All data that is collected will be saved inside post meta fields and choosing a greater period will generate a bigger record.', 'essb'), $data_history);
$listOfOptions = array("manage_options" => "Administrator", "delete_pages" => "Editor", "publish_posts" => "Author", "edit_posts" => "Contributor");
essb5_draw_select_option('esml_access', esc_html__('Metrics report access', 'essb'), esc_html__('Access role will limit which type of users can see the metrics data inside WordPress admin panel. If you are not sure what to choose leave Administrator selected.', 'essb'), $listOfOptions);

essb_advancedopts_section_close();
