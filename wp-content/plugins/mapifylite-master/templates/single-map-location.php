<?php
$map_location = new Mpfy_Map_Location(get_the_ID());

if (isset($_GET['mpfy_map'])) {
	$map_id = intval($_GET['mpfy_map']);
} else {
	$map_id = $map_location->get_maps();
	$map_id = $map_id[0];
}

$map = new Mpfy_Map($map_id);
$map_mode = $map->get_mode();

if (!MPFY_IS_AJAX) {	
	include('single-map-location.noajax.php');
} else {
	the_post();

	$video_embed_code = $map_location->get_video_embed();
	$video_thumb = $map_location->get_video_thumb();

	$images = $map_location->get_gallery_images();

	$media_count = $images;
	$media_count[] = $video_embed_code;
	$media_count = count(array_filter($media_count));

	$layout = 'map_location';
	if ($post->post_type == 'post') {
		$layout = 'post';
	}

	$classes = array(
		'mpfy-p-popup',
	);
	if ($layout == 'map_location') {
		$classes[] = 'mpfy-p-popup-style-two';
	} else {
		$classes[] = 'mpfy-p-popup-style-one';
	}
	if ($media_count == 0) {
		$classes[] = 'mpfy-p-nomedia';
	}

	include('single-map-location.' . $layout . '.php');
}