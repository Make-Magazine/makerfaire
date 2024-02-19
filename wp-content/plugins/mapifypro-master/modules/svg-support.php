<?php
function mpfy_svg_get_help_text() {
	return '<br /><strong>When using .svg make sure it has it\'s width and height xml attributes set.</strong>';
}

add_filter( 'upload_mimes', 'mpfy_svg_add_mime_support' );
function mpfy_svg_add_mime_support( $mimes = array() ) {
	if ( !isset( $mimes['svg'] ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}

// Required svg fix for WordPress 4.7.1 and 4.7.2 (latest at the time of writing)
add_filter( 'wp_check_filetype_and_ext', 'mpfy_svg_force_mime_support', 10, 4 );
function mpfy_svg_force_mime_support( $filetype_and_ext, $file, $filename, $mimes ) {
	$filetype = wp_check_filetype( $filename, $mimes );
	if ( $filetype_and_ext['ext'] === false && $filetype_and_ext['type'] === false && $filetype['ext'] === 'svg' && $filetype['type'] === 'image/svg+xml' ) {
		$filetype_and_ext['ext'] = $filetype['ext'];
		$filetype_and_ext['type'] = $filetype['type'];
	}
	return $filetype_and_ext;
}

add_filter('mpfy_map_custom_fields_basic', 'mpfy_svg_map_pin_field_support');
function mpfy_svg_map_pin_field_support( $custom_fields ) {
	if ( isset( $custom_fields['map_pin'] ) ) {
		$custom_fields['map_pin']->help_text( $custom_fields['map_pin']->get_help_text() . ' ' . mpfy_svg_get_help_text() );
	}
	return $custom_fields;
}

add_filter('mpfy_map_location_custom_fields', 'mpfy_svg_map_location_pin_field_support');
function mpfy_svg_map_location_pin_field_support( $custom_fields ) {
	if ( isset( $custom_fields['map_location_pin'] ) ) {
		$custom_fields['map_location_pin']->help_text( $custom_fields['map_location_pin']->get_help_text() . ' ' . mpfy_svg_get_help_text() );
	}
	return $custom_fields;
}