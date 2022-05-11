<?php

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_heading( esc_html__('Global Counter Settings', 'essb'), '6');
essb5_draw_field_group_open();
$counter_value_mode = array("" => esc_html__('Automatically shorten values above 1000', 'essb'), 'full' => esc_html__('Always display full value (default server settings)', 'essb'), 'fulldot' => esc_html__('Always display full value - dot thousand separator (example 5.000)', 'essb'), 'fullcomma' => esc_html__('Always display full value - comma thousand separator (example 5,000)', 'essb'), 'fullspace' => esc_html__('Always display full value - space thousand separator (example 5 000)', 'essb'), 'no' => esc_html__('Without formating', 'essb'));
essb5_draw_select_option('total_counter_format', esc_html__('Total value format', 'essb'), '', $counter_value_mode);
essb5_draw_switch_option('animate_total_counter', esc_html__('Animate value appearance', 'essb'), '');
essb5_draw_switch_option('total_counter_all', esc_html__('Always generate total counter based on all social networks', 'essb') . essb_generate_expert_badge() , esc_html__('Enable the option to make the total counter consists of the shares from all available inside the plugin networks. The option does not initiate an update for all networks - it will create a sum of the shares for all available in the plugin networks that are already stored.', 'essb'));
essb5_draw_field_group_close();

essb5_draw_heading(esc_html__('Left/Right Total Counter Position', 'essb'), '6');
essb5_draw_field_group_open();
essb5_draw_input_option('counter_total_text', esc_html__('Change the text "Total"', 'essb'), '');
essb5_draw_field_group_close();

essb5_draw_heading( esc_html__('Left/Right Total Counter With Big Number (and optional icon)', 'essb'), '6');
essb5_draw_field_group_open();
essb5_draw_input_option('activate_total_counter_text', esc_html__('Change "Shares" text (plural)', 'essb'), '');
essb5_draw_input_option('activate_total_counter_text_singular', esc_html__('Change "Share" text (singular)', 'essb'), '');
$select_values = array(
		'share' => array('title' => '', 'content' => '<i class="essb_icon_share"></i>'),
		'share-alt-square' => array('title' => '', 'content' => '<i class="essb_icon_share-alt-square"></i>'),
		'share-alt' => array('title' => '', 'content' => '<i class="essb_icon_share-alt"></i>'),
		'share-tiny' => array('title' => '', 'content' => '<i class="essb_icon_share-tiny"></i>'),
		'share-outline' => array('title' => '', 'content' => '<i class="essb_icon_share-outline"></i>')
);
essb5_draw_toggle_option('activate_total_counter_icon', esc_html__('Total counter icon', 'essb'), '', $select_values);
essb5_draw_field_group_close();

essb5_draw_heading(esc_html__('Before/After Share Buttons Total Counter Position', 'essb'), '6');
essb5_draw_field_group_open();
essb5_draw_input_option('total_counter_afterbefore_text', esc_html__('Change total counter text when before/after styles are active', 'essb'), esc_html__('Customize the text that is displayed in before/after share buttons display method. To display the total share number use the string {TOTAL} in text. Example: {TOTAL} users share us', 'essb'), true);
essb5_draw_field_group_close();

essb_advancedopts_section_close();