<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');
essb5_draw_switch_option('social_proof_enable', esc_html__('Activate Usage of Avoid Negative Social Proof', 'essb'), '');
essb5_draw_input_option('button_counter_hidden_till', esc_html__('Display button counter after this value of shares is reached', 'essb'), esc_html__('You can hide your button counter until amount of shares is reached. This option is active only when you enter value in this field - if blank button counter is always displayed. (Example: 10 - this will make button counter appear when at least 10 shares are made).', 'essb'));
essb5_draw_input_option('total_counter_hidden_till', esc_html__('Display total counter after this value of shares is reached', 'essb'), esc_html__('You can hide your total counter until amount of shares is reached. This option is active only when you enter value in this field - if blank total counter is always displayed.', 'essb'));
essb_advancedopts_section_close();
