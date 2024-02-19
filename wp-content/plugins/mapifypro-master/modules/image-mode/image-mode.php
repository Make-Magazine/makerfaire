<?php
include_once('tileset.php');

add_filter('mpfy_map_modes', 'mpfy_im_map_modes');
function mpfy_im_map_modes($map_modes) {

	$map_modes['image'] = 'Image';

	return $map_modes;
}

add_filter('mpfy_map_settings_service', 'mpfy_im_map_settings_service', 10, 2);
function mpfy_im_map_settings_service($settings, $map_id) {

	$uploads_dir = wp_upload_dir();
	$settings['image_status'] = get_post_meta($map_id, '_map_tileset_status', true);
	$settings['image_source'] = mpfy_tileset_get_url($map_id);

	return $settings;
}

add_filter('mpfy_map_get_tileset', 'mpfy_im_map_get_tileset', 10, 2);
function mpfy_im_map_get_tileset($tileset, $map_id) {
	$map = new Mpfy_Map($map_id);

	if ($map->get_mode() == 'image') {
		$image_big = get_post_meta($map->get_id(), '_map_image_big', true);
		$status = get_post_meta($map->get_id(), '_map_tileset_status', true);
		$url = mpfy_tileset_get_url($map->get_id());

		if (!$image_big) {
			$tileset['message'] = 'Please select an image for the map.';
			return $tileset;
		}
		if ($status != 'ready') {
			$tileset['message'] = 'Your image has not been processed, yet.';
			return $tileset;
		}

		$tileset['url'] = $url;
	}

	return $tileset;
}
