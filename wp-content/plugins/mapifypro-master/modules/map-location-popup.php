<?php
add_filter( 'mpfy_map_location_popup_enabled', 'mpfy_mlp_filter_map_location_popup_enabled', 10, 2 );
function mpfy_mlp_filter_map_location_popup_enabled( $enabled, $map_location_id ) {
	return mpfy_meta_to_bool( $map_location_id, '_map_location_tooltip_enabled', true);
}