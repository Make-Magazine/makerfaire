<?php

$networks_with_api = array(
    'facebook' => 'Facebook',
    'pinterest' => 'Pinterest',
    'vk' => 'VKontakte',
    'ok' => 'Odnoklassniki',
    'reddit' => 'Reddit',
    'buffer' => 'Buffer',
    'xing' => 'Xing',
    'yummly' => 'Yummly'
);

if (function_exists('essb_advancedopts_settings_group')) {
    essb_advancedopts_settings_group('essb_options');
}

essb_advanced_options_relation('active_internal_counters_advanced', 'switch', array('active_internal_counters_advanced_networks'));

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('active_internal_counters', esc_html__('Display share counters for social networks without API based on button clicks', 'essb'), '');
essb5_draw_switch_option('active_internal_counters_advanced', esc_html__('Overwrite the official counters with internal', 'essb'), 'Change the official API counter to internal. Enable the option to display a field where you can select which networks to overwrite.');
essb5_draw_field_group_open();
essb5_draw_select_option('active_internal_counters_advanced_networks', esc_html__('Networks', 'essb'), esc_html('The list includes only networks with official counter API. Other networks have an internal counter by default and you can enable it from the "Display share counters for social networks without API based on button clicks"', 'essb'), $networks_with_api, false, '', true);
essb5_draw_field_group_close();
essb5_draw_switch_option('deactive_internal_counters_mail', esc_html__('Don\'t show share counter for Mail and Print buttons', 'essb'), esc_html__('Enable to hide the internal share counter for Mail and Print buttons. This won\'t stop the tracking and you can revert back at any time without losing the values.', 'essb'));
essb5_draw_switch_option('deactivate_postcount', esc_html__('Fully deactivate internal share counter tracking', 'essb'), esc_html__('The internal click tracking on each share button runs automatically in the background even when you don\'t show share counters. This ensures that the values will be ready as soon as you tick to show the share counters. Enable the option if you wish to completely disable the internal share counter (and not internal counter values will be generated).', 'essb'));


essb_advancedopts_section_close();