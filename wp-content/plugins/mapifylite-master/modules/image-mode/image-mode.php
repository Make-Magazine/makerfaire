<?php
include_once('tileset.php');

add_filter('mpfy_map_modes', 'mpfy_im_map_modes');
function mpfy_im_map_modes($map_modes) {

	$map_modes['image'] = 'Image';

	return $map_modes;
}

add_action('admin_enqueue_scripts', 'mpfy_im_admin_behaviors');
function mpfy_im_admin_behaviors($hook) {
    if ($hook !== 'post.php' || !isset($_GET['post'])) {
        return;
    }

    $map_id = intval($_GET['post']);

    $uploads_dir = wp_upload_dir();
    $image_source = mpfy_tileset_get_url($map_id);

    wp_register_script('mpfy-image-mode-admin', plugins_url('modules/image-mode/admin.js', MAPIFY_PLUGIN_FILE), array('jquery'), '1.0.0', true);
    wp_localize_script('mpfy-image-mode-admin', 'mpfy_image_mode', array(
    	'image_status'=>get_post_meta($map_id, '_map_tileset_status', true),
    	'image_source'=>$image_source,
    ));
    wp_enqueue_script('mpfy-image-mode-admin');
}


add_action( 'mpfy_post_tooltip_settings_container', 'mpfy_mll_register_image_mode_container' );
function mpfy_mll_register_image_mode_container() {
	$fields = array(
		'position_start'=>'',

		'map_image_big'=>Carbon_Field::factory( 'image', 'map_image_big', __( 'Map Image', 'mpfy' ) )
			->help_text('Note: you must update your map and the image will be processed to support multiple zoom levels.'),

		'map_tileset'=>Carbon_Field::factory( 'tileset', 'map_tileset', __( 'Status', 'mpfy' ) ),

		'position_end'=>'',
	);
	$fields = apply_filters( 'mpfy_map_custom_fields_image_mode', $fields );
	$fields = array_filter( $fields );

	Carbon_Container::factory( 'custom_fields', 'Image Mode Settings' )
		->show_on_post_type( 'map' )
		->add_fields( array_values( $fields ) );
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
