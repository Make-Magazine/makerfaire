<?php

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

// recovery
essb_advancedopts_section_open('ao-small-values');
essb5_draw_switch_option('counter_recover_active', esc_html__('Activate Share Recovery', 'essb'), '');

$recover_type = array(
		'unchanged'			=> esc_html__( 'Unchanged' , 'essb' ),
		'default' 			=> esc_html__( 'Plain' , 'essb' ),
		'day_and_name' 		=> esc_html__( 'Day and Name' , 'essb' ),
		'month_and_name' 	=> esc_html__( 'Month and Name' , 'essb' ),
		'numeric' 			=> esc_html__( 'Numeric' , 'essb' ),
		'post_name' 		=> esc_html__( 'Post Name' , 'essb' ),
		'custom'			=> esc_html__( 'Custom' , 'essb' ),
		'current'           => esc_html__( 'Standard URLs', 'essb')
);

essb5_draw_select_option('counter_recover_mode', esc_html__('Previous url format', 'essb'), esc_html__('Choose how your site address is changed. If you choose custom use the field below to setup your URL structure', 'essb'), $recover_type);
essb5_draw_input_option('counter_recover_custom', esc_html__('Custom Permalink Format', 'essb'), '', true);

$recover_mode = array("unchanged" => "Unchanged", "http2https" => "Switch from http to https", "https2http" => "Switch from https to http");
essb5_draw_select_option('counter_recover_protocol', esc_html__('Change of connection protocol', 'essb'), esc_html__('If you change your connection protocol then choose here the option that describes it.', 'essb'), $recover_mode);

$recover_domain = array(
		'unchanged'			=> esc_html__( 'Unchanged' , 'essb' ),
		'www'				=> esc_html__( 'www' , 'essb' ),
		'nonwww'			=> esc_html__( 'non-www' , 'essb' ));
essb5_draw_select_option('counter_recover_prefixdomain', esc_html__('Previous Domain Prefix', 'essb'), esc_html__('If you make a change of your domain prefix than you need to describe it here.', 'essb'), $recover_domain);
essb5_draw_input_option('counter_recover_subdomain', esc_html__('Subdomain', 'essb'), esc_html__('If you move your site to a subdomain enter here its name (without previx and extra symbols', 'essb'), 'true');

ESSBOptionsStructureHelper::hint(esc_html__('Cross-domain recovery', 'essb'), esc_html__('If you\'ve migrated your website from one domain to another, fill in these two fields to activate cross-domain share recovery', 'essb'));
essb5_draw_input_option('counter_recover_domain', esc_html__('Previous domain name', 'essb'), esc_html__('If you have changed your domain name please fill in this field previous domain name with protocol (example http://example.com) and choose recovery mode to be Change domain name', 'essb'), true);
essb5_draw_input_option('counter_recover_newdomain', esc_html__('New domain name', 'essb'), esc_html__('If plugin is not able to detect your new domain fill here its name with protocol (example http://example.com)', 'essb'), true);
essb5_draw_input_option('counter_recover_date', esc_html__('Date of change', 'essb'), esc_html__('Fill out date when change was made. Once you fill it share counter recovery will be made for all posts that are published before this date. Date shoud be filled in format yyyy-mm-dd.', 'essb'));
essb5_draw_panel_end();

essb_advancedopts_section_close();
