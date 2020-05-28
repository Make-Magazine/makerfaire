<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_heading(esc_html__('Homepage counter', 'essb'), '6');
essb5_draw_switch_option('homepage_total_allposts', esc_html__('Share counter on the homepage is from the shares of all posts and pages', 'essb'), esc_html__('Enable this option to make your homepage show share counters based on the values plugin stores for each of the posts on site.', 'essb'));
essb5_draw_switch_option('site_total_allposts', esc_html__('Share counter on entire site is from the shares of all posts and pages', 'essb'), esc_html__('Enable this option to make each post/page from your site show share counters based on the values plugin stores for each of the posts on site.', 'essb'));
essb5_draw_input_option('homepage_total_cache', esc_html__('Cache for the total counter on the homepage/site', 'essb'), esc_html__('Modify the default caching value of 30 minutes. Fill in just a numeric value representing minutes - example: 60. Avoid using too small values because you can overload the database server.', 'essb'));

essb5_draw_heading(esc_html__('Hide counter on archives / homepage', 'essb'), '6');

essb5_draw_switch_option('hide_counter_homepage', esc_html__('Do not show share counters on the homepage', 'essb'), '');
essb5_draw_switch_option('hide_counter_archive', esc_html__('Do not show share counters on archive pages (category, tags, authors, etc.)', 'essb'), '');


essb_advancedopts_section_close();