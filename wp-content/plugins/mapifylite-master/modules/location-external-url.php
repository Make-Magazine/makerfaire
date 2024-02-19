<?php
function mpfy_leu_map_location_custom_fields($custom_fields) {
	$custom_fields = mpfy_array_push_key($custom_fields, 'position_after_popup', array(
		'map_location_external_url_enable'=>Carbon_Field::factory( 'select', 'map_location_external_url_enable', __( 'Take visitor to another page on clicking location?', 'mpfy' ) )
			->add_options(array( 'no' => 'No', 'yes' => 'Yes' )),
		'map_location_external_url_url'=>Carbon_Field::factory( 'text', 'map_location_external_url_url', __( 'Enter URL', 'mpfy' ) )
			->help_text('This will override the pop-up and take the visitor to a new page when they click on the location marker.'),
		'map_location_external_url_target'=>Carbon_Field::factory( 'select', 'map_location_external_url_target', __( 'Open in', 'mpfy' ) )
			->add_options(array( '_blank' => 'New Window', '_self' => 'Current Window' )),
	));

	return $custom_fields;
}
add_filter('mpfy_map_location_custom_fields', 'mpfy_leu_map_location_custom_fields');

function mpfy_leu_pin_trigger_settings_filter($settings, $pin_id) {
	$enabled = mpfy_meta_to_bool($pin_id, '_map_location_external_url_enable', true);
	$url = get_post_meta($pin_id, '_map_location_external_url_url', true);
	$target = get_post_meta($pin_id, '_map_location_external_url_target', true);
	$target = ($target) ? $target : '_blank';

	if ($enabled && $url) {
		$settings['href'] = esc_url($url);
		$settings['target'] = $target;
		$settings['classes'][] = 'mpfy-external-link';
	}

	return $settings;
}
add_filter('mpfy_pin_trigger_settings', 'mpfy_leu_pin_trigger_settings_filter', 10, 2);
