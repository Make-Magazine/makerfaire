<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('activate_fake_counters', esc_html__('Activate fake share counters', 'essb'), esc_html('All options inside the fake section will not work unless this switch is set to Yes.', 'essb'));
essb5_draw_input_option('fake_counter_correction', esc_html__('Counter increase value', 'essb'), esc_html__('Set a numeric value that will be used to increase the number of existing shares. Example if you set 5 the existing shares will be multiplied with 5: if you have 100 real shares, the plugin will show 500.', 'essb'));
essb5_draw_switch_option('activate_fake_counters_internal', esc_html__('Make all share counters internal', 'essb'), esc_html__('Switching to internal counters will change the way how share values are updated for networks with API. Instead of getting the real number of shares, the value will increase from the click over the share button (and stored internally on your site).', 'essb'));

essb_advancedopts_section_close();