<?php

add_filter( 'mpfy_map_enable_use_my_location', 'mpfy_uml_filter_map_enable_use_my_location', 10, 2 );
function mpfy_uml_filter_map_enable_use_my_location( $enabled, $map_id ) {
	return mpfy_meta_to_bool( $map_id, '_map_enable_use_my_location', false );
}
