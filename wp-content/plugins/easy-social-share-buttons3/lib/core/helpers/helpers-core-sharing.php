<?php

function essb_post_details_to_content($content) {
	global $post;

	if (isset($post)) {
		$url = get_permalink();
		$title_plain = $post->post_title;
		$image = essb_core_get_post_featured_image($post->ID);
		$description = essb_core_get_post_excerpt($post->ID);
			
		$content = preg_replace(array('#%%title%%#', '#%%url%%#', '#%%image%%#', '#%%excerpt%%#'), array($title_plain, $url, $image, $description), $content);
	}

	return $content;
}

function essb_template_folder ($template_id) {
	$folder = 'default';

	if ($template_id == 1) {
		$folder = 'default';
	}
	if ($template_id == 2) {
		$folder = 'metro';
	}
	if ($template_id == 3) {
		$folder = 'modern';
	}
	if ($template_id == 4) {
		$folder = 'round';
	}
	if ($template_id == 5) {
		$folder = 'big';
	}
	if ($template_id == 6) {
		$folder = 'metro-retina';
	}
	if ($template_id == 7) {
		$folder = 'big-retina';
	}
	if ($template_id == 8) {
		$folder = 'light-retina';
	}
	if ($template_id == 9) {
		$folder = 'flat-retina';
	}
	if ($template_id == 10) {
		$folder = 'tiny-retina';
	}
	if ($template_id == 11) {
		$folder = 'round-retina';
	}
	if ($template_id == 12) {
		$folder = 'modern-retina';
	}
	if ($template_id == 13) {
		$folder = 'circles-retina';
	}
	if ($template_id == 14) {
		$folder = 'circles-retina essb_template_blocks-retina';
	}
	if ($template_id == 15) {
		$folder = 'dark-retina';
	}
	if ($template_id == 16) {
		$folder = 'grey-circles-retina';
	}
	if ($template_id == 17) {
		$folder = 'grey-blocks-retina';
	}
	if ($template_id == 18) {
		$folder = 'clear-retina';
	}
	if ($template_id == 19) {
		$folder = 'copy-retina';
	}
	if ($template_id == 20) {
		$folder = 'dimmed-retina';
	}
	if ($template_id == 21) {
		$folder = 'grey-retina';
	}
	if ($template_id == 22) {
		$folder = 'default-retina';
	}
	if ($template_id == 23) {
		$folder = 'jumbo-retina';
	}
	if ($template_id == 24) {
		$folder = 'jumbo-round-retina essb_template_jumbo-retina';
	}
	if ($template_id == 25) {
		$folder = 'fancy-retina';
	}
	if ($template_id == 26) {
		$folder = 'deluxe-retina';
	}
	if ($template_id == 27) {
		$folder = 'modern-retina essb_template_modern-slim-retina';
	}
	if ($template_id == 28) {
		$folder = 'bold-retina';
	}
	if ($template_id == 29) {
		$folder = 'fancy-bold-retina';
	}
	if ($template_id == 30) {
		$folder = 'retro-retina';
	}
	if ($template_id == 31) {
		$folder = 'metro-bold-retina';
	}

	if ($template_id == 32) {
		$folder = 'default4-retina';
	}
	if ($template_id == 33) {
		$folder = 'clear-retina essb_template_clear-rounded-retina';
	}
	if ($template_id == 34) {
		$folder = 'grey-fill-retina';
	}
	if ($template_id == 35) {
		$folder = 'white-fill-retina';
	}
	if ($template_id == 36) {
		$folder = 'white-retina';
	}
	if ($template_id == 37) {
		$folder = 'grey-round-retina';
	}
	if ($template_id == 38) {
		$folder = 'color-leafs';
	}
	if ($template_id == 39) {
		$folder = 'grey-leafs';
	}
	if ($template_id == 40) {
		$folder = 'circles-retina essb_tempate_color-circles-outline-retina';
	}
	if ($template_id == 41) {
		$folder = 'circles-retina essb_template_blocks-retina essb_tempate_color-blocks-outline-retina';
	}
	if ($template_id == 42) {
		$folder = 'grey-circles-outline-retina';
	}
	if ($template_id == 43) {
		$folder = 'grey-circles-outline-retina essb_template_grey-blocks-outline-retina';
	}
	if ($template_id == 44) {
		$folder = 'dark-outline-retina';
	}
	if ($template_id == 45) {
		$folder = 'dark-outline-retina essb_template_dark-round-outline-retina';
	}
	if ($template_id == 46) {
		$folder = 'light-retina essb_template_classic-retina';
	}
	if ($template_id == 47) {
		$folder = 'light-retina essb_template_classic-retina essb_template_classic-round-retina';
	}
	if ($template_id == 48) {
		$folder = 'modern-retina essb_template_classic-fancy-retina';
	}
	
	if ($template_id == 49) {
		$folder = 'default4-retina essb_template_color-circles-retina';
	}
	if ($template_id == 50) {
		$folder = 'default4-retina essb_template_massive-retina';
	}
	
	if ($template_id == 51) {
		$folder = 'round-retina essb_template_cutoff-retina';
	}

	if ($template_id == 52) {
		$folder = 'metro-bold-retina essb_template_cutoff-fill-retina';
	}
	
	
	if ($template_id == 53) {
		$folder = 'round-retina essb_template_modern-light-retina';
	}
	
	if ($template_id == 54) {
		$folder = 'default4-retina essb_template_tiny-color-circles-retina';
	}
	
	if ($template_id == 55) {
		$folder = 'clear-retina essb_template_lollipop-retina';
	}

	if ($template_id == 56) {
		$folder = 'rainbow-retina';
	}

	if ($template_id == 57) {
		$folder = 'round-retina essb_template_modern-light-retina essb_template_flow-retina';
	}

	if ($template_id == 58) {
		$folder = 'round-retina essb_template_modern-light-retina essb_template_flow-retina essb_template_flow-jump-retina';
	}
	
	if ($template_id == 59) {
		$folder = 'default4-retina essb_template_glow-retina';
	}
	
	if (has_filter('essb4_templates_class')) {
		$folder = apply_filters('essb4_templates_class', $folder, $template_id);
	}

	// fix when using template_slug instead of template_id
	if (intval($template_id) == 0 && $template_id != '') {
		$folder = $template_id;
	}


	return $folder;
}


function essb_core_helper_generate_list_networks($all_networks = false) {
	global $essb_networks, $essb_options;
	$networks = array();

	$listOfNetworks = ($all_networks) ? essb_core_helper_generate_network_list() : essb_options_value( 'networks');
	if (!is_array($listOfNetworks)) {
		$listOfNetworks = essb_core_helper_generate_network_list();
	}

	foreach ($listOfNetworks as $single) {
		if ($single != 'more' && $single != 'share') {
			$networks[] = $single;
		}
	}

	return $networks;
}

function essb_core_helper_generate_list_networks_with_more($all_networks = false) {
	global $essb_networks, $essb_options;
	$networks = array();

	$listOfNetworks = ($all_networks) ? essb_core_helper_generate_network_list() : essb_options_value( 'networks');

	foreach ($listOfNetworks as $single) {
		
		$networks[] = $single;
		
	}

	return $networks;
}

function essb_core_helper_networks_without_more($networks) {
	$more_appear = false;
	$new_list = array();

	foreach ($networks as $network) {

		if ($network != 'more' && $network != 'share') {
			$new_list[] = $network;
		}
	}

	return $new_list;
}

function essb_core_helper_networks_after_more($networks) {
	$more_appear = false;
	$new_list = array();
	
	foreach ($networks as $network) {
		
		if ($more_appear) {
			$new_list[] = $network;
		}
		
		if ($network == 'more' || $network == 'share') {
			$more_appear = true;
		}
	}
	
	return $new_list;
}

function essb_core_helper_generate_network_list() {
	global $essb_networks;
		
	$network_order = array();
		
	foreach ($essb_networks as $key => $data) {
		$network_order[] = $key;
	}
		
	return $network_order;
}

function essb_core_helper_nonlatin_textencode($str = '') {
    $str = str_replace('&#8211;', '-', $str); 
    $str = str_replace(' ', '%20', $str);
    $str = str_replace("'", '%27', $str);
    $str = str_replace("\"", '%22', $str);
    $str = str_replace('#', '%23', $str);
    $str = str_replace('$', '%24', $str);
    $str = str_replace('&', '%26', $str);
    $str = str_replace(',', '%2C', $str);
    $str = str_replace('/', '%2F', $str);
    $str = str_replace(':', '%3A', $str);
    $str = str_replace(';', '%3B', $str);
    $str = str_replace('=', '%3D', $str);
    $str = str_replace('?', '%3F', $str);
    $str = str_replace('@', '%40', $str);
    $str = str_replace('|', '%7C', $str);
    $str = str_replace('\%27', '%27', $str);
    $str = str_replace('%26%238211%3B', '-', $str);
    
    return $str;
}

function essb_core_helper_prevent_percent_break_tweet($str = '') {
    $str = str_replace('%', '%25', $str);
    
    return $str;
}

function essb_core_helper_textencode($str) {
    /**
     * @since 7.3
     * Handle the unicode long dash and percentage mark
     */    
    $str = str_replace('&#8211;', '-', $str);    
    
	$str = str_replace(' ', '%20', $str);
	$str = str_replace("'", '%27', $str);
	$str = str_replace("\"", '%22', $str);
	$str = str_replace('#', '%23', $str);
	$str = str_replace('$', '%24', $str);
	$str = str_replace('&', '%26', $str);
	$str = str_replace(',', '%2C', $str);
	$str = str_replace('/', '%2F', $str);
	$str = str_replace(':', '%3A', $str);
	$str = str_replace(';', '%3B', $str);
	$str = str_replace('=', '%3D', $str);
	$str = str_replace('?', '%3F', $str);
	$str = str_replace('@', '%40', $str);
	$str = str_replace('|', '%7C', $str);
	$str = str_replace('\%27', '%27', $str);
	$str = str_replace('%26%238211%3B', '-', $str);
	
	return $str;
}

function essb_core_helper_urlencode($str) {
	return essb_core_helper_textencode($str);
}


function essb_get_native_button_settings($position = '', $only_share = false) {
	$are_active = true;

	if ($only_share) {
		$are_active = false;
		return array('active' => false);
	}

	if (!defined('ESSB3_NATIVE_ACTIVE')) {
		$are_active = false;
	}
	else {
		if (!ESSB3_NATIVE_ACTIVE) {
			$are_active = false;
		}
	}
		
	if (defined('ESSB3_NATIVE_DEACTIVE')) {
		$are_active = false;
	}

	if (essb_is_mobile()) {
		if (!essb_option_bool_value('allow_native_mobile')) {
			$are_active = false;
		}
	}

	if (!empty($position)) {
		if (essb_option_bool_value( $position.'_native_deactivate')) {
			$are_active = false;
		}
	}

	if (essb_is_module_deactivated_on('native')) {
		$are_active = false;
	}

	if (!$are_active) {
		return array('active' => false);
	}

	$native_options = ESSBNativeButtonsHelper::native_button_defaults();
	$native_options['active'] = $are_active;
	$native_options['message_like_buttons'] = '';

	$deactivate_message_for_location = essb_option_bool_value( $position.'_text_deactivate');
	if (!$deactivate_message_for_location) {
		$native_options['message_like_buttons'] = essb_option_value('message_like_buttons');
	}

	return $native_options;
}
