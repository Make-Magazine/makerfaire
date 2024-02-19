<?php

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
