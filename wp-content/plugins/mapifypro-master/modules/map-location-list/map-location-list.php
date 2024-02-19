<?php

add_action('mpfy_template_after_map', 'mpfy_mll_template_after_map');
function mpfy_mll_template_after_map($map_id) {
	$enabled = mpfy_meta_to_bool($map_id, '_map_mll_include', false);
	if (!$enabled) {
		return;
	}

	$map = new Mpfy_Map($map_id);
	$locations = $map->get_locations();
	$number_of_locations = get_post_meta($map_id, '_map_mll_number_of_locations', true);
	$number_of_locations = max(0, abs(intval($number_of_locations)));
	$number_of_locations = $number_of_locations == 0 ? 3 : $number_of_locations;
	$hide_in_default_view = mpfy_meta_to_bool($map_id, '_map_mll_hide_in_default_view', false);

	include('templates/list.php');
}

function mpfy_location_list_open_popup_bgcolor( $color, $map_id ) {
	$meta_color = get_post_meta( $map_id, '_map_label_interactive_list_open_popup_bgcolor', true );
	if ( empty( $meta_color ) ) {
		return '#61849c';
	}

	return $meta_color;
}
add_filter( 'mpfy_location_list_open_popup_bgcolor', 'mpfy_location_list_open_popup_bgcolor', 10, 2 );

function mpfy_location_list_get_directions_bgcolor( $color, $map_id ) {
	$meta_color = get_post_meta( $map_id, '_map_label_interactive_list_directions_bgcolor', true );
	if ( empty( $meta_color ) ) {
		return '#d2845b';
	}

	return $meta_color;
}
add_filter( 'mpfy_location_list_get_directions_bgcolor', 'mpfy_location_list_get_directions_bgcolor', 10, 2 );

function mpfy_location_list_open_popup_color( $color, $map_id ) {
	$meta_color = get_post_meta( $map_id, '_map_label_interactive_list_open_popup_color', true );
	if ( empty( $meta_color ) ) {
		return '#ffffff';
	}

	return $meta_color;
}
add_filter( 'mpfy_location_list_open_popup_color', 'mpfy_location_list_open_popup_color', 10, 2 );

function mpfy_location_list_get_directions_color( $color, $map_id ) {
	$meta_color = get_post_meta( $map_id, '_map_label_interactive_list_directions_color', true );
	if ( empty( $meta_color ) ) {
		return '#ffffff';
	}

	return $meta_color;
}
add_filter( 'mpfy_location_list_get_directions_color', 'mpfy_location_list_get_directions_color', 10, 2 );

function mpfy_location_list_button_hover_bgcolor( $color, $map_id ) {
	$meta_color = get_post_meta( $map_id, '_map_label_interactive_list_buttons_hover_bgcolor', true );
	if ( empty( $meta_color ) ) {
		return '#ffffff';
	}

	return $meta_color;
}
add_filter( 'mpfy_location_list_button_hover_bgcolor', 'mpfy_location_list_button_hover_bgcolor', 10, 2 );

function mpfy_location_list_button_hover_color( $color, $map_id ) {
	$meta_color = get_post_meta( $map_id, '_map_label_interactive_list_buttons_hover_color', true );
	if ( empty( $meta_color ) ) {
		return '#ffffff';
	}

	return $meta_color;
}
add_filter( 'mpfy_location_list_button_hover_color', 'mpfy_location_list_button_hover_color', 10, 2 );
