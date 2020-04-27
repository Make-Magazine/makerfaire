<?php

function essb_actions_update_pinterest_count() {
	$post_id = $_POST['post_id'];
	$count = $_POST['count'];

	$post_id = sanitize_text_field($post_id);
	$count = sanitize_text_field($count);

	$past_shares = intval(get_post_meta($post_id, 'essb_c_pinterest', true));

	if ( $count > $past_shares || essb_option_bool_value('cache_counter_force') ) {
		delete_post_meta( $post_id, 'essb_c_pinterest' );
		update_post_meta( $post_id, 'essb_c_pinterest', $count );
	}

	echo json_encode(array('network' => 'pinterest', 'past_shares' => $past_shares, 'new_shares' => $count));

	wp_die();
}