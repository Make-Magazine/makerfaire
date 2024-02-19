<?php

add_filter('mpfy_map_background_color', 'mpfy_pc_map_background_color', 10, 2);
function mpfy_pc_map_background_color($value, $map_id) {
    $background = get_post_meta($map_id, '_map_background_color', true);
	if (!$background) {
		$background = $value;
	}

	return $background;
}

add_filter('mpfy_map_tooltip_background_color', 'mpfy_pc_map_tooltip_background_color', 10, 2);
function mpfy_pc_map_tooltip_background_color($value, $map_id) {
	$tooltip_background = get_post_meta($map_id, '_map_tooltip_background_color', true);
	if (!$tooltip_background) {
		$tooltip_background = '#FFFFFF';
	}

	$tooltip_transparency = get_post_meta($map_id, '_map_tooltip_background_transparency', true);
	if ($tooltip_transparency === '') {
		$tooltip_transparency = 1;
	} else {
		$tooltip_transparency = min(100, max(0, intval($tooltip_transparency))) / 100;
	}

	$tooltip_background_rgba = mpfy_hex2rgb($tooltip_background); // array(r,g,b)
	$tooltip_background_rgba[] = $tooltip_transparency; // add alpha

	return $tooltip_background_rgba;
}

add_filter('mpfy_map_tooltip_text_color', 'mpfy_pc_map_tooltip_text_color', 10, 2);
function mpfy_pc_map_tooltip_text_color($value, $map_id) {
	$tooltip_text_color = get_post_meta($map_id, '_map_tooltip_text_color', true);
	if ( ! $tooltip_text_color ) {
		$tooltip_text_color = '#525252';
	}
	return $tooltip_text_color;
}

add_action('mpfy_popup_before_section', 'mpfy_pc_apply_colors', 10, 2);
function mpfy_pc_apply_colors($map_location_id, $map_id) {
	$colors = array(
		'_map_popup_background_color'=>'#FFFFFF',
		'_map_popup_header_background_color'=>'#F7F7F7',
		'_map_popup_date_background_color'=>'#566069',
		'_map_popup_accent_color'=>'#2ED2E1',
	);
	foreach ($colors as $key => $default) {
		$v = get_post_meta($map_id, $key, true);
		if ($v) {
			$colors[$key] = $v;
		}
	}

	$styles = '
	<style type="text/css">
		.mpfy-p-color-popup-background { background-color: ' . $colors['_map_popup_background_color'] . ' !important; }
		.mpfy-p-color-header-background { background-color: ' . $colors['_map_popup_header_background_color'] . ' !important; }
		.mpfy-p-color-header-date-background { background-color: ' . $colors['_map_popup_date_background_color'] . ' !important; }
		.mpfy-p-color-accent-background { background-color: ' . $colors['_map_popup_accent_color'] . ' !important; }
		.mpfy-p-color-accent-color { color: ' . $colors['_map_popup_accent_color'] . ' !important; }
	</style>
	';

	echo $styles;
}
