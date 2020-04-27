<?php

function essb_actions_extender_postcount() {
	$post_id = isset($_POST["post_id"]) ? $_POST["post_id"] : '';
	$service_id = isset($_POST["service"]) ? $_POST["service"] : '';
	
	$post_id = sanitize_text_field($post_id);
	$service_id = sanitize_text_field($service_id);
	
	$post_id = intval($post_id);
	
	
	if ($service_id == "print_friendly") {
		$service_id = "print";
	}
	
	$all_networks_object = essb_available_social_networks(true);
	$all_networks = array();
	foreach ($all_networks_object as $social => $data) {
		$all_networks[] = $social;
	}

	if (in_array($service_id, $all_networks)) {
		$current_value = get_post_meta($post_id, 'essb_pc_'.$service_id, true);
		$current_value = intval($current_value) + 1;
		update_post_meta ( $post_id, 'essb_pc_'.$service_id, $current_value );
		
		// since 5.6
		if (essb_is_internal_counted($service_id)) {
			delete_post_meta( $post_id, 'essb_c_'.$service_id );
			update_post_meta( $post_id, 'essb_c_'.$service_id, $current_value );
		}
		
		// @since 3.6
		// adding custom hook to execute when click on share buttons
		// @revision 5.6 - adding to event as a parameter the shared post_id and network		
		$action_options = array('post_id' => $post_id, 'network' => $service_id);
		
		do_action('essb_after_sharebutton_click', $action_options);
	}
	else {
		$post_id = '';
		$service_id = '';
		$current_value = '';
	}
	
	return array("post_id" => $post_id, "service" => $service_id, "current_value" => $current_value);
}