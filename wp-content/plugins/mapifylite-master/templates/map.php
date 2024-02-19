<?php
global $wpdb;

$errors = array();

$mode = $map->get_mode();
$zoom_level = $map->get_zoom_level();
$zoom_enabled = $map->get_zoom_enabled();
$manual_zoom_enabled = $map->get_manual_zoom_enabled();
$tileset = apply_filters('mpfy_map_get_tileset', array('url'=>'', 'message'=>''), $map->get_id());
if (!$tileset['url'] && $tileset['message']) {
	$errors[] = $tileset['message'];
}

$map_default_pin_image = $map->get_default_pin_image();

$map_enable_use_my_location = $map->get_use_my_location_enabled();

$animate_tooltips = $map->get_animate_tooltips();
$tooltip_image_orientation = $map->get_tooltip_image_orientation();
$animate_pinpoints = $map->get_animate_pinpoints();

$raw_pins = $map->get_locations( false );
$pins = array();
foreach ( $raw_pins as $index => $p ) {
	$post = get_post($p->ID);
	$map_location = new Mpfy_Map_Location( $post->ID );

	$pin = array(
		'id' => $post->ID,
		'title' => $post->post_title,
		'latlng' => $map_location->get_coordinates(),
		'animateTooltips' => $animate_tooltips,
		'animatePinpoints' => $animate_pinpoints,
		'image' => $map_location->get_pin_image(),
		'city' => $map_location->get_city(),
		'zip' => $map_location->get_zip(),
		'tags' => array(),
		'popupEnabled' => $map_location->get_popup_enabled(),
		'tooltipEnabled' => $map_location->get_tooltip_enabled(),
		'tooltipCloseBehavior' => $map_location->get_tooltip_close_behavior(),
		'tooltipContent' => $map_location->get_tooltip_content( $map->get_id() ),
		'thumbnail' => $map_location->get_thumbnail(),
	);

	$tags = $map_location->get_tags();
	foreach ($tags as $t) {
		$pin['tags'][ $t->term_id ] = $t->term_id;
	}

	$pins[] = $pin;
}

$map_id = $map->get_id();

$map_background_color = apply_filters('mpfy_map_background_color', '', $map->get_id());
$tooltip_background = apply_filters('mpfy_map_tooltip_background_color', array(255, 255, 255, 1), $map->get_id());
$tooltip_text_color = apply_filters('mpfy_map_tooltip_text_color', '', $map->get_id());

$map_type = $map->get_type();

$google_map_style = apply_filters('mpfy_google_map_style', 'default', $map->get_id());

$map_tags = $map->get_tags();
$search_enabled = $map->get_search_enabled();
$search_radius_unit_name = $map->get_search_radius_unit_name();
$search_radius_unit = $map->get_search_radius_unit();
$search_radius = $map->get_search_radius();
$search_region_bias = $map->get_search_region_bias();
$search_center = $map->get_search_center_behavior();
$filters_center = $map->get_filters_center_behavior();
$clustering_enabled = apply_filters('mpfy_clustering_enabled', false, $map->get_id());

$filters_enabled = $map->get_filters_enabled();
$filters_list_enabled = $map->get_filters_list_enabled();

$center = $map->get_center();

$routes = apply_filters('pretty_routes_load_routes', array(), $map->get_id());

ob_start();
include('map.html.php');
$html = ob_get_clean();

ob_start();
include('map.js.php');
$script = ob_get_clean();

return array('html'=>$html, 'script'=>$script);
