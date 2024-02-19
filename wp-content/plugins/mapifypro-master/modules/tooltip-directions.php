<?php

add_filter( 'mpfy_map_location_tooltip_text', 'mpfy_ttd_map_location_tooltip_text', 10, 3 );
function mpfy_ttd_map_location_tooltip_text( $text, $map_location_id, $map_id ) {
	$map_location = new Mpfy_Map_location( $map_location_id );
	$directions_url = $map_location->get_directions_url();
	$button_label = mpfy_meta_label( $map_id, '_map_label_directions_button', 'Get Directions' );
	$text = str_replace('[directions]', '<div class="mpfy-tooptip-actions"><a href="' . $directions_url . '" target="_blank">' . $button_label . '<span><strong></strong></span></a></div>', $text);
	return $text;
}
