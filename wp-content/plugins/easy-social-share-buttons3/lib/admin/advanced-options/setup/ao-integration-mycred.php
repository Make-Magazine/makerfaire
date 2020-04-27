<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_heading( esc_html__('Award users for clicking on share button', 'essb'), '5');
essb5_draw_switch_option('mycred_activate', esc_html__('Activate myCred integration for click on links', 'essb'), esc_html__('In order to work the myCred integration you need to have myCred Points for click on links hook activated (if you use custom points group you need to activated inside custom points group settings).', 'essb'));
essb5_draw_input_option('mycred_points', esc_html__('myCred reward points for share link click', 'essb'), esc_html__('Provide custom points to reward user when share link. If nothing is provided 1 point will be included.', 'essb'));
essb5_draw_input_option('mycred_group', esc_html__('myCred custom point type', 'essb'), esc_html__('Provide custom meta key for the points that user will get to share link. To create your own please visit this tutorial: http://codex.mycred.me/get-started/multiple-point-types/. Leave blank to use the default (mycred_default)', 'essb'));
essb5_draw_switch_option('mycred_activate_custom', esc_html__('Activate myCred integration for points for social sharing via the Easy Social Share Buttons for WordPress hook', 'essb'), esc_html__('Use Easy Social Share Buttons custom hook in myCred to award points for click on share buttons', 'essb'));

essb5_draw_heading( esc_html__('Award users when someone uses their share link', 'essb'), '5');

essb5_draw_switch_option('mycred_referral_activate', esc_html__('Activate myCred Referral usage', 'essb'), esc_html__('That option requires you to have the Points for referrals hook enabled. That option is not compatible with share counters because adding referral id to url will reset social share counters to zero.', 'essb'));
essb5_draw_switch_option('mycred_referral_activate_shortcode', esc_html__('Activate myCred Referral usage in shortcodes', 'essb'), esc_html__('Activate this option in combination with referrals hook to allow affiliate ID appear also when you use shortcodes.', 'essb'));

essb_advancedopts_section_close();