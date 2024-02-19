<?php
function mpfy_tileset_get_url($map_id) {
	$uploads_dir = wp_upload_dir();
	$url = $uploads_dir['baseurl'] . '/mpfy/' . $map_id . '/';
	return $url;
}