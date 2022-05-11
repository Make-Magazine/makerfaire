<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advanced_options_relation('activate_fake_counters', 'switch', array('fake_counter_correction'));


essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('activate_fake_counters', esc_html__('Activate fake share counters', 'essb'), esc_html('All options inside the fake section will not work unless this switch is set to Yes.', 'essb'));
essb5_draw_field_group_open();
essb5_draw_input_option('fake_counter_correction', esc_html__('Counter increase value', 'essb'), esc_html__('Set a numeric value that will be used to increase the number of existing shares. Example if you set 5 the existing shares will be multiplied with 5: if you have 100 real shares, the plugin will show 500.', 'essb'));
essb5_draw_field_group_close();
essb5_draw_switch_option('activate_fake_counters_internal', esc_html__('Integrate official API share counters with internal counters', 'essb'), esc_html__('The integration will compare both - official and internal value of shares. And it will show always the greater of them to the front-end.', 'essb'));

essb_advancedopts_section_close();