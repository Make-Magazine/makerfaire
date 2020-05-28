<?php

if (function_exists('essb_advancedopts_settings_group')) {
    essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('active_internal_counters', esc_html__('Track share clicks to get share counts for networks without APIs?', 'essb'), '');
essb5_draw_switch_option('deactive_internal_counters_mail', esc_html__('Deactivate counters for Mail & Print', 'essb'), esc_html__('Enable this option if you wish to deactivate internal counters for mail & print buttons. That buttons are in the list of default social networks that support counters. Deactivating them will lower down request to internal WordPress AJAX event.', 'essb'));
essb5_draw_switch_option('deactivate_postcount', esc_html__('Fully deactivate internal share counter tracking', 'essb'), esc_html__('Even when you do not display share counters on site at this moment plugin tracks internal counter with each button click. This is made to provide a share counter value when you decide to show or use share counters. Activation of this option will completely remove the execution and work of code for all internal tracked share counters - if you have any existing internal counter values they will stop increase and for all others it will not add a value. Hint: Rember that major networks like LinkedIn and Google+ removed share counters and there is no alternative of counter value at this time rather than internal counter.', 'essb'));


essb_advancedopts_section_close();