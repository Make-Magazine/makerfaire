<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');
essb_advanced_options_relation('social_proof_enable', 'switch', array('button_counter_hidden_till', 'total_counter_hidden_till'));
essb5_draw_switch_option('social_proof_enable', esc_html__('Enable avoid negative social proof', 'essb'), '');
essb5_draw_field_group_open();
essb5_draw_input_option('button_counter_hidden_till', esc_html__('Minimal share value to display single button counter', 'essb'), esc_html__('Fill the minimal number of shares - example 10. If you leave it blank the counter will always appear. If you need to hide only zero-based shares place 1 in the field.', 'essb'));
essb5_draw_input_option('total_counter_hidden_till', esc_html__('Minimal share value to display total counter', 'essb'), esc_html__('Fill the minimal number of shares - example 10. If you leave it blank the counter will always appear. If you need to hide only zero-based shares place 1 in the field.', 'essb'));
essb5_draw_field_group_close();
essb_advancedopts_section_close();
