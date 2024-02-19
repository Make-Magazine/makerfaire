<?php
global $wpdb;

$errors              = array();
$mode                = $map->get_mode();
$zoom_level          = $map->get_zoom_level();
$zoom_enabled        = $map->get_zoom_enabled();
$manual_zoom_enabled = $map->get_manual_zoom_enabled();
$tileset             = apply_filters('mpfy_map_get_tileset', array('url'=>'', 'message'=>''), $map->get_id());

if ( ! $tileset['url'] && $tileset['message'] ) {
	$errors[] = $tileset['message'];
}

$map_default_pin_image      = $map->get_default_pin_image();
$map_enable_use_my_location = $map->get_use_my_location_enabled();
$animate_tooltips           = $map->get_animate_tooltips();
$tooltip_image_orientation  = $map->get_tooltip_image_orientation();
$animate_pinpoints          = $map->get_animate_pinpoints();
$raw_pins                   = $map->get_locations( false );
$pins                       = array();
$crowdmap_enabled           = $map->get_crowdmap_enabled();

foreach ( $raw_pins as $index => $p ) {
	$post         = get_post( $p->ID );
	$map_location = new Mpfy_Map_Location( $post->ID );
	$tags         = $map_location->get_tags();
	$pin          = array(
		'id'                   => $post->ID,
		'title'                => $post->post_title,
		'latlng'               => $map_location->get_coordinates(),
		'animateTooltips'      => $animate_tooltips,
		'animatePinpoints'     => $animate_pinpoints,
		'image'                => $map_location->get_pin_image( $map->get_id() ),
		'city'                 => $map_location->get_city(),
		'zip'                  => $map_location->get_zip(),
		'tags'                 => array(),
		'popupEnabled'         => $map_location->get_popup_enabled(),
		'tooltipEnabled'       => $map_location->get_tooltip_enabled(),
		'tooltipCloseBehavior' => $map_location->get_tooltip_close_behavior(),
		'tooltipContent'       => $map_location->get_tooltip_content( $map->get_id() ),
		'thumbnail'            => $map_location->get_thumbnail(),
	);
	
	foreach ($tags as $t) {
		$pin['tags'][ $t->term_id ] = $t->term_id;
	}

	$pins[] = $pin;
}

$pins_hide_initially     = $map->get_hide_pins_by_default();
$map_id                  = $map->get_id();
$map_background_color    = apply_filters( 'mpfy_map_background_color', '', $map->get_id() );
$tooltip_background      = apply_filters( 'mpfy_map_tooltip_background_color', array( 255, 255, 255, 1 ), $map->get_id() );
$tooltip_text_color      = apply_filters( 'mpfy_map_tooltip_text_color', '', $map->get_id() );
$location_circle_color   = $map->get_location_circle_color();
$map_type                = $map->get_type();
$google_map_style        = apply_filters( 'mpfy_google_map_style', 'default', $map->get_id() );
$map_tags                = $map->get_tags();
$search_enabled          = $map->get_search_enabled();
$search_radius_unit_name = $map->get_search_radius_unit_name();
$search_radius_unit      = $map->get_search_radius_unit();
$search_radius           = $map->get_search_radius();
$search_region_bias      = $map->get_search_region_bias();
$search_center           = $map->get_search_center_behavior();
$filters_center          = $map->get_filters_center_behavior();
$clustering_enabled      = apply_filters( 'mpfy_clustering_enabled', false, $map->get_id() );
$filters_enabled         = $map->get_filters_enabled();
$filters_list_enabled    = $map->get_filters_list_enabled();
$center                  = $map->get_center();
$routes                  = apply_filters( 'pretty_routes_load_routes', array(), $map->get_id() );

// whether to load the prettyroutes assets or not
if (!defined('MPFY_PRETTYROUTES_LOAD_ASSETS') && is_array($routes) && count($routes) > 0) {
	define('MPFY_PRETTYROUTES_LOAD_ASSETS', true);
}

// get map areas
$area_ids                = get_field( 'mapify_map_area_selector', $map->get_id() );
$map_areas               = false; 

if ( is_array( $area_ids ) ) {
	$map_areas = array();

	foreach ( $area_ids as $area_id ) {
		$mapify_map_drawer = new \Acf_Mapifypro\Model\Mapify_Map_Drawer( $area_id );
		$map_areas[]       = array(
			'coordinates'  => $mapify_map_drawer->get_area_coordinates(),
			'image_url'    => $mapify_map_drawer->get_area_image( 'medium' ),
			'border_color' => $mapify_map_drawer->get_area_border_color(),
			'fill_color'   => $mapify_map_drawer->get_area_fill_color(),
			'fill_opacity' => $mapify_map_drawer->get_area_fill_opacity(),
			'description'  => $mapify_map_drawer->get_area_description(),
		);
	}
}

$has_controls     = ($filters_enabled && $map_tags) || $search_enabled;
$controls_classes = array();

if ( ( $filters_enabled && $map_tags ) && $search_enabled ) {
	$controls_classes[] = 'mpfy-controls-all';
}

if ( ! $has_controls ) {
	$controls_classes[] = 'mpfy-hidden';
}

if ( ! $search_enabled ) {
	$controls_classes[] = 'mpfy-without-search';
}

if ( ! $filters_enabled || ! $map_tags ) {
	$controls_classes[] = 'mpfy-without-dropdown';
}

$wrap_classes         = array('mpfy-container', 'mpfy-map-id-' . $map_id);
$wrap_classes         = apply_filters('mpfy_map_wrap_classes', $wrap_classes, $map_id);
$map_proprietary_data = apply_filters('mpfy_map_proprietary_data', array(), $map_id);
$map_attrs_data       = apply_filters('mpfy_map_attrs_data', array(), $map_id);
$canvas_style         = array('overflow: hidden');

if ( $width !== 0 ) {
	$canvas_style[] = 'width: ' . $width;
}

if ( $height !== 0 ) {
	$canvas_style[] = 'height: ' . $height;
}

$canvas_style                       = implode( '; ', $canvas_style );
$label_filter_dropdown_default_view = mpfy_meta_label( $map->get_id(), '_map_label_filter_dropdown', 'Default View' );
$label_filter_list_default_view     = mpfy_meta_label( $map->get_id(), '_map_label_filter_list', 'Default View' );
$label_search_first                 = mpfy_meta_label( $map->get_id(), '_map_label_search', 'Enter location..' );
$label_search_second                = mpfy_meta_label( $map->get_id(), '_map_label_search_second', '..or enter a name!' );
$open_popup_bgcolor                 = apply_filters( 'mpfy_location_list_open_popup_bgcolor', '#61849c', $map_id );
$get_directions_bgcolor             = apply_filters( 'mpfy_location_list_get_directions_bgcolor', '#d2845b', $map_id );
$open_popup_color                   = apply_filters( 'mpfy_location_list_open_popup_color', '#ffffff', $map_id );
$get_directions_color               = apply_filters( 'mpfy_location_list_get_directions_color', '#ffffff', $map_id );
$button_hover_bgcolor               = apply_filters( 'mpfy_location_list_button_hover_bgcolor', '#ffffff', $map_id );
$button_hover_color                 = apply_filters( 'mpfy_location_list_button_hover_color', '#000000', $map_id );
$enable_search_radius               = mpfy_meta_to_bool( $map->get_id(), '_map_enable_search_radius', false );

ob_start();
include( 'map.html.php' );
$html = ob_get_clean();

ob_start();
include( 'map.js.php' );
$script = ob_get_clean();

ob_start();
include( 'google-rich-results.php' );
$google_search_results = ob_get_clean();

return array(
	'html'   => $html, 
	'script' => $script,
	'gsr'    => $google_search_results,
);
