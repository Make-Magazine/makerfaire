<?php
function mpfy_ajax_mpfy_get_map_settings() {
	$post_id = intval( $_GET['pid'] );
	$map     = new Mpfy_Map( $post_id );

	$return = array(
		'mode'         => $map->get_mode(),
		'terrain_mode' => $map->get_type(),
		'style'        => apply_filters( 'mpfy_google_map_style', 'default', $post_id ),
		'background'   => apply_filters( 'mpfy_map_background_color', '#E5E3DF', $post_id ),
	);

	$return = apply_filters( 'mpfy_map_settings_service', $return, $post_id );
	echo json_encode( $return );
	exit;
}
add_action( 'wp_ajax_mpfy_get_map_settings', 'mpfy_ajax_mpfy_get_map_settings' );
