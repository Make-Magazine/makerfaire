<?php

if (! function_exists ( 'vc_map' )) {
	return;
}

$shortcode_setup = array();

$param = array();
$param['type'] = 'dropdown';
$param['heading'] = esc_html__('Choose design/position', 'essb');
$param['param_name'] = 'display';
$param['value'] = array();
$custom_positions = essb5_get_custom_positions();
foreach ($custom_positions as $key => $value) {
	$param['value'][$value] = $key;
}
$param['admin_label'] = true;
$shortcode_setup[] = $param;

$param = array();
$param['type'] = 'checkbox';
$param['heading'] = esc_html__('Always Show', 'essb');
$param['description'] = esc_html__('Tick this option if you wish to bypass the position check and always show the display', 'essb');
$param['param_name'] = 'force';
$param['value'] = array();
$param['value']['Yes'] = 'true';
$param['admin_label'] = true;
$shortcode_setup[] = $param;

vc_map ( array (
		"name" => esc_html__('Custom Share Buttons Display', 'essb'),
		"base" => 'social-share-display',
		"icon" => 'vc-social-share-display',
		"category" => esc_html__ ( 'Easy Social Share Buttons', 'essb' ),
		"description" => esc_html__('Show custom registered inside positions share buttons display', 'essb'),
		"value" => esc_html__('Show custom registered inside positions share buttons display', 'essb'),
		"params" => $shortcode_setup ));