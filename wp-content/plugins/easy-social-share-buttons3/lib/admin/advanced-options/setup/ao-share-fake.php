<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb5_draw_panel_start( __('Increase Shares', 'essb'), __('Increase shares includes a various options to modify the official share counter. Those techniques are not ethical. Use on your own responsibility.', 'essb'), 'fa21 fa fa-cogs', array("mode" => "switch", 'switch_id' => 'counter_fake_active', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));
essb5_draw_input_option('button_counter_hidden_till', __('Display button counter after this value of shares is reached', 'essb'), __('You can hide your button counter until amount of shares is reached. This option is active only when you enter value in this field - if blank button counter is always displayed. (Example: 10 - this will make button counter appear when at least 10 shares are made).', 'essb'));
essb5_draw_input_option('total_counter_hidden_till', __('Display total counter after this value of shares is reached', 'essb'), __('You can hide your total counter until amount of shares is reached. This option is active only when you enter value in this field - if blank total counter is always displayed.', 'essb'));
essb5_draw_panel_end();