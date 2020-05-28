<?php

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_heading(esc_html__('Left/Right Total Counter Position', 'essb'), '6');
essb5_draw_input_option('counter_total_text', esc_html__('Change text "Total" used on Left/Right position', 'essb'), esc_html__('Enter your own custom text that will replace the default inside plugin (leave blank for the default)', 'essb'));

essb5_draw_heading( esc_html__('Left/Right Total Counter With Big Number (and optional icon)', 'essb'), '6');
essb5_draw_input_option('activate_total_counter_text', esc_html__('Change "Shares" text (plural)', 'essb'), esc_html__('Enter custom text that will appear below value of shares when value is greater than 1 (default: Shares)', 'essb'));
essb5_draw_input_option('activate_total_counter_text_singular', esc_html__('Change "Share" text (singular)', 'essb'), esc_html__('Enter custom text that will appear below value of shares when value is equal to 1 or 0 (default: Share)', 'essb'));
$select_values = array(
		'share' => array('title' => '', 'content' => '<i class="essb_icon_share"></i>'),
		'share-alt-square' => array('title' => '', 'content' => '<i class="essb_icon_share-alt-square"></i>'),
		'share-alt' => array('title' => '', 'content' => '<i class="essb_icon_share-alt"></i>'),
		'share-tiny' => array('title' => '', 'content' => '<i class="essb_icon_share-tiny"></i>'),
		'share-outline' => array('title' => '', 'content' => '<i class="essb_icon_share-outline"></i>')
);
essb5_draw_toggle_option('activate_total_counter_icon', esc_html__('Total counter icon', 'essb'), esc_html__('Choose icon displayed on total counter when position with such is selected', 'essb'), $select_values);

essb5_draw_heading(esc_html__('Before/After Share Buttons Total Counter Position', 'essb'), '6');
essb5_draw_input_option('total_counter_afterbefore_text', esc_html__('Change total counter text when before/after styles are active', 'essb'), esc_html__('Customize the text that is displayed in before/after share buttons display method. To display the total share number use the string {TOTAL} in text. Example: {TOTAL} users share us', 'essb'));

essb5_draw_heading( esc_html__('Global Counter Settings', 'essb'), '6');
$counter_value_mode = array("" => esc_html__('Automatically shorten values above 1000', 'essb'), 'full' => esc_html__('Always display full value (default server settings)', 'essb'), 'fulldot' => esc_html__('Always display full value - dot thousand separator (example 5.000)', 'essb'), 'fullcomma' => esc_html__('Always display full value - comma thousand separator (example 5,000)', 'essb'), 'fullspace' => esc_html__('Always display full value - space thousand separator (example 5 000)', 'essb'), 'no' => esc_html__('Without formating', 'essb'));
essb5_draw_select_option('total_counter_format', esc_html__('Total counter format', 'essb'), esc_html__('Choose how you wish to present your share counter value - short number of full number. This option will not work if you use real time share counters - in this mode you will always see short number format', 'essb'), $counter_value_mode);
essb5_draw_switch_option('animate_total_counter', esc_html__('Animate Numbers', 'essb'), esc_html__('Enable this option to apply nice animation of counters on appear.', 'essb'));
essb5_draw_switch_option('total_counter_all', esc_html__('Always generate total counter based on all social networks', 'essb'), esc_html__('Enable this option if you wish to see total counter generated based on all installed in plugin social networks no matter of ones you have active. Default plugin setup is made to show total counter based on active for display social networks only and using different social networks on different locations may cause to have difference in total counter. Use this option to make it always be the same.', 'essb'));


essb_advancedopts_section_close();