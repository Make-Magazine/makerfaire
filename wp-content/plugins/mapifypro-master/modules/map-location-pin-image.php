<?php

add_filter( 'mpfy_map_location_pin_image', 'mpfy_mlpi_map_location_apply_pin_image', 10, 3 );
function mpfy_mlpi_map_location_apply_pin_image( $image, $pin_id, $map_id ) {
	$pin_image = get_post_meta( $pin_id, '_map_location_pin', true );
	if ( $pin_image ) {
		$image = $pin_image;
	}
	return $image;
}
