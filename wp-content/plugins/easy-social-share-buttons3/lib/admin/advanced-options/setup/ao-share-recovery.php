<?php

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

// recovery
essb5_draw_panel_start(__('Recover my shares', 'essb'), __('Share counter recovery allows you restore back shares once you make a permalink change (including installing a SSL certificate). Share recovery will show back shares only if they are present for both versions of URL (before and after change).', 'essb'), 'fa21 fa fa-magic', array("mode" => "switch", 'switch_id' => 'counter_recover_active', 'switch_on' => __('Yes', 'essb'), 'switch_off' => __('No', 'essb')));

$recover_type = array(
		'unchanged'			=> __( 'Unchanged' , 'essb' ),
		'default' 			=> __( 'Plain' , 'essb' ),
		'day_and_name' 		=> __( 'Day and Name' , 'essb' ),
		'month_and_name' 	=> __( 'Month and Name' , 'essb' ),
		'numeric' 			=> __( 'Numeric' , 'essb' ),
		'post_name' 		=> __( 'Post Name' , 'essb' ),
		'custom'			=> __( 'Custom' , 'essb' ),
		'current'           => __( 'Standard URLs', 'essb')
);

essb5_draw_select_option('counter_recover_mode', __('Previous url format', 'essb'), __('Choose how your site address is changed. If you choose custom use the field below to setup your URL structure', 'essb'), $recover_type);
essb5_draw_input_option('counter_recover_custom', __('Custom Permalink Format', 'essb'), __('', 'essb'), true);

$recover_mode = array("unchanged" => "Unchanged", "http2https" => "Switch from http to https", "https2http" => "Switch from https to http");
essb5_draw_select_option('counter_recover_protocol', __('Change of connection protocol', 'essb'), __('If you change your connection protocol then choose here the option that describes it.', 'essb'), $recover_mode);

$recover_domain = array(
		'unchanged'			=> __( 'Unchanged' , 'essb' ),
		'www'				=> __( 'www' , 'essb' ),
		'nonwww'			=> __( 'non-www' , 'essb' ));
essb5_draw_select_option('counter_recover_prefixdomain', __('Previous Domain Prefix', 'essb'), __('If you make a change of your domain prefix than you need to describe it here.', 'essb'), $recover_domain);
essb5_draw_input_option('counter_recover_subdomain', __('Subdomain', 'essb'), __('If you move your site to a subdomain enter here its name (without previx and extra symbols', 'essb'), 'true');

ESSBOptionsStructureHelper::hint(__('Cross-domain recovery', 'essb'), __('If you\'ve migrated your website from one domain to another, fill in these two fields to activate cross-domain share recovery', 'essb'));
essb5_draw_input_option('counter_recover_domain', __('Previous domain name', 'essb'), __('If you have changed your domain name please fill in this field previous domain name with protocol (example http://example.com) and choose recovery mode to be <b>Change domain name</b>', 'essb'), true);
essb5_draw_input_option('counter_recover_newdomain', __('New domain name', 'essb'), __('If plugin is not able to detect your new domain fill here its name with protocol (example http://example.com)', 'essb'), true);
essb5_draw_input_option('counter_recover_date', __('Date of change', 'essb'), __('Fill out date when change was made. Once you fill it share counter recovery will be made for all posts that are published before this date. Date shoud be filled in format <b>yyyy-mm-dd</b>.', 'essb'));
essb5_draw_panel_end();
