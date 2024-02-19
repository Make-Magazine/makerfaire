<?php

add_filter( 'mpfy_map_search_enabled', 'mpfy_s_filter_map_search_enabled', 10, 2 );
function mpfy_s_filter_map_search_enabled( $enabled, $map_id ) {
	return mpfy_meta_to_bool( $map_id, '_map_enable_search', false );
}

add_filter( 'mpfy_map_search_center', 'mpfy_s_filter_map_search_center', 10, 2 );
function mpfy_s_filter_map_search_center( $enabled, $map_id ) {
	return mpfy_meta_to_bool( $map_id, '_map_search_center', false );
}

add_filter( 'mpfy_map_search_radius_unit', 'mpfy_s_filter_map_search_radius_unit', 10, 2 );
function mpfy_s_filter_map_search_radius_unit( $search_radius_unit, $map_id ) {
	$value = get_post_meta( $map_id, '_map_search_radius_unit', true );
	$value = ($value == 'km') ? 'km' : 'mi';
	return $value;
}

add_filter( 'mpfy_map_search_radius', 'mpfy_s_filter_map_search_radius', 10, 2 );
function mpfy_s_filter_map_search_radius( $search_radius, $map_id ) {
	return max( 1, intval( get_post_meta( $map_id, '_map_search_radius', true ) ) );
}

add_filter( 'mpfy_map_search_region_bias', 'mpfy_s_filter_map_search_region_bias', 10, 2 );
function mpfy_s_filter_map_search_region_bias( $region_bias, $map_id ) {
	return get_post_meta( $map_id, '_map_search_region_bias', true );
}