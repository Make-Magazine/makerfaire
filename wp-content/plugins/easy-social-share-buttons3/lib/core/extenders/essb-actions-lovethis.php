<?php

function essb_actions_extender_lovethis() {	
	$post_id = isset ( $_POST ['post_id'] ) ? $_POST ['post_id'] : '';
	$service_id = isset ( $_POST ['service'] ) ? $_POST ['service'] : '';
	
	$post_id = sanitize_text_field($post_id);
	$service_id = sanitize_text_field($service_id);
	
	$love_count = get_post_meta($post_id, '_essb_love', true);
	if( isset($_COOKIE['essb_love_'. $post_id]) ) die( $love_count);
	if (!isset($love_count)) {
		$love_count = 0;
	}
	$love_count = intval($love_count);
	$love_count++;
	update_post_meta($post_id, '_essb_love', $love_count);
	$cookie_information = 'essb_love_'. $post_id.' = '.$love_count;
	setcookie('essb_love_'. $post_id, $cookie_information, time()+(3600 * 24), "/", "",  0);
	
	die ( json_encode ( array ("success" => 'Log handled - post_id = '.$post_id.' count = '.$love_count ) ) );
}