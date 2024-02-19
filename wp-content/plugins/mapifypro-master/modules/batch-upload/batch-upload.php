<?php
function mpfy_batch_add_admin_page() {
	add_submenu_page('mapify.php', 'Batch Upload', mpfy_get_icon( 'batch-upload' ) . 'Batch Upload', 'manage_options', 'mpfy-import', 'mpfy_batch_upload_admin_page');
}
add_action('admin_menu', 'mpfy_batch_add_admin_page');

function mpfy_batch_upload_admin_page() {
	include_once('admin/batch-upload.php');
}

function mpfy_batch_bool_value($value) {
	$true_values = array(1, '1', 'true', 'y', 'yes');
	$value = trim(strtolower($value));
	return (bool) (in_array($value, $true_values));
}

function mpfy_batch_download_image( $url ) {
	$response = wp_remote_get( $url );
	if ( is_wp_error( $response ) ) {
		return null;
	}

	$mime_type = mpfy_batch_get_mime_type( $response );
	$extension = mpfy_batch_get_extension( $mime_type );
	if ( ! $extension ) {
		return null;
	}

	$basename = mpfy_batch_generate_basename();
	$dir = mpfy_batch_get_upload_dir();
	$filename = $basename . '.' . $extension;
	$filename = wp_unique_filename( $dir, $filename );
	$full_path = $dir . $filename;
	mpfy_batch_maybe_create_upload_dir();
	file_put_contents( $full_path, $response['body'] );

	return [
		'path'      => $full_path,
		'filename'  => $filename,
		'extension' => $extension,
		'mime_type' => $mime_type,
	];
}

function mpfy_batch_generate_basename() {
	return date( 'Y-m-d-H-i-s' );
}

function mpfy_batch_get_upload_dir() {
	$wp_upload_dir = wp_upload_dir();
	return $wp_upload_dir['basedir'] . '/mapifypro/';
}

function mpfy_batch_get_upload_url() {
	$wp_upload_dir = wp_upload_dir();
	return $wp_upload_dir['baseurl'] . '/mapifypro/';
}

function mpfy_batch_maybe_create_upload_dir() {
	$dir = mpfy_batch_get_upload_dir();
	if ( file_exists( $dir ) ) {
		return;
	}

	wp_mkdir_p( $dir );
}

function mpfy_batch_get_mime_type( $response ) {
	if ( empty( $response['headers']['content-type'] ) ) {
		return null;
	}

	return trim( $response['headers']['content-type'] );
}

function mpfy_batch_get_extension( $mime_type ) {
	if ( ! $mime_type ) {
		return null;
	}

	$match_found = preg_match( '~^image/(jpe?g|png|gif)$~i', $mime_type, $matches );
	if ( ! $match_found ) {
		return null;
	}

	$extension = strtolower( $matches[1] );
	if ( $extension === 'jpeg' ) {
		return 'jpg';
	}

	return $extension;
}

function mpfy_batch_process_featured_image( $post_id, $url ) {
	$attachment_id = attachment_url_to_postid( $url );
	if ( ! $attachment_id ) {
		$download_result = mpfy_batch_download_image( $url );
		if ( ! $download_result ) {
			return;
		}

		$dest_path = $download_result['path'];
		$attachment_id = wp_insert_attachment( [
			'post_title' => '',
			'post_content' => '',
			'post_excerpt' => '',
			'post_status' => 'inherit',
			'post_mime_type' => $download_result['mime_type'],
			'guid' => mpfy_batch_get_upload_url() . $download_result['filename'],
			'post_parent' => $post_id,
		], $dest_path );

		if ( is_wp_error( $attachment_id ) ) {
			return;
		}

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attachment_id, $dest_path );
		wp_update_attachment_metadata( $attachment_id, $attach_data );
	}

	update_post_meta( $post_id, '_thumbnail_id', $attachment_id );
}

function mpfy_batch_trigger_hook() {
	global $wpdb;

	/*
	0 Title
	1 Description
	2 Map Tag
	3 Enable Pop-up? Y/N
	4 Link location to another page? Y/N
	5 Enter URL
	6 Enable Tooltip? Y/N
	7 Close Tooltip Automatically/Manually
	8 Include Location Information? Y/N
	9 Include Directions? Y/N
	10 Tooltip Content
	11 Include on Selected Map(s) Location  List? Y/N
	12 Short Description (appears in Interactive List)
	13 Pin Image URL (Leave blank to use Map’s Default pin image)
	14 Video Embed code
	15 Gallery Images: Enter the url of the images you’d like to include in the gallery, separated by a comma.
	16 Location Address or GPS Coordinates
	17 Phone Number
	18 Street Address
	19 State
	20 City
	21 Zip
	22 Country
	23 Enable Sharing? Y/N
	24 Featured Image URL
	*/

	$dictionary = array(
		0  => 'title',
		1  => 'description',
		2  => 'map_tags',
		3  => 'enable_popup',
		4  => 'link_to_page',
		5  => 'link_to_page_url',
		6  => 'enable_tooltip',
		7  => 'close_tooltip_method',
		8  => 'include_location_info',
		9  => 'include_directions',
		10 => 'tooltip_content',
		11 => 'include_in_location_list',
		12 => 'short_description',
		13 => 'pin_image_url',
		14 => 'video_embed_code',
		15 => 'gallery_images',
		16 => 'location_address_or_geo',
		17 => 'phone',
		18 => 'address',
		19 => 'state',
		20 => 'city',
		21 => 'zip',
		22 => 'country',
		23 => 'enable_sharing',
		24 => 'featured_image',
		25 => 'address_2',
	);

	$raw = array_map('trim', $_POST['row']);
	$r = array();

	foreach ( $raw as $i => $value ) {
		if ( isset( $dictionary[ $i ] ) ) {
			$r[ $dictionary[ $i ] ] = trim( $value );
		}
	}
	
	$action = 'add';

	$already_existing = get_page_by_title($r['title'], OBJECT, 'map-location');
	if ($already_existing) {
		$action = 'update';
		$new_post_id = $already_existing->ID;
		$location = new Mpfy_Map_Location($new_post_id);
		$maps = $location->get_maps();
		$maps[] = intval($_POST['map_id']);
		$maps = array_filter(array_unique($maps));
		update_post_meta($new_post_id, '_map_location_map', implode(',', $maps));
	} else {
		$new_post = array(
			'post_type'=>'map-location',
			'post_status'=>'publish',
			'post_title'=>$r['title'],
			'post_content'=>$r['description'],
		);
		$new_post_id = wp_insert_post($new_post);
		update_post_meta($new_post_id, '_map_location_map', intval($_POST['map_id']));
	}
	mpfy_duplicate_location_map_meta($new_post_id);

	$tooltip_close_method = trim(strtolower($r['close_tooltip_method']));
	$tooltip_close_method = (in_array($tooltip_close_method, array('auto', 'automatically'))) ? 'auto' : 'manual';
	update_post_meta($new_post_id, '_map_location_tooltip_enabled', mpfy_batch_bool_value($r['enable_popup']) ? 'yes' : 'no' );
	update_post_meta($new_post_id, '_map_location_tooltip_show', mpfy_batch_bool_value($r['enable_tooltip']) ? 'yes' : 'no');
	update_post_meta($new_post_id, '_map_location_tooltip_close', $tooltip_close_method);
	update_post_meta($new_post_id, '_map_location_popup_location_information', mpfy_batch_bool_value($r['include_location_info']) ? 'yes' : 'no' );
	update_post_meta($new_post_id, '_map_location_popup_directions', mpfy_batch_bool_value($r['include_directions']) ? 'yes' : 'no' );
	update_post_meta($new_post_id, '_map_location_tooltip', $r['tooltip_content']);
	update_post_meta($new_post_id, '_map_location_mll_include', mpfy_batch_bool_value($r['include_in_location_list']) ? 'y' : 'n' );
	update_post_meta($new_post_id, '_map_location_mll_description', $r['short_description']);
	update_post_meta($new_post_id, '_map_location_video_embed', $r['video_embed_code']);
	update_post_meta($new_post_id, '_map_location_phone', $r['phone']);
	update_post_meta($new_post_id, '_map_location_address', $r['address']);
	update_post_meta($new_post_id, '_map_location_state', $r['state']);
	update_post_meta($new_post_id, '_map_location_city', $r['city']);
	update_post_meta($new_post_id, '_map_location_zip', $r['zip']);
	update_post_meta($new_post_id, '_map_location_country', $r['country']);

	if ( isset( $r['address_2'] ) ) {
		update_post_meta($new_post_id, '_map_location_address_2', $r['address_2']);
	}

	update_post_meta($new_post_id, '_map_location_external_url_enable', mpfy_batch_bool_value($r['link_to_page']) ? 'yes' : 'no' );
	update_post_meta($new_post_id, '_map_location_external_url_url', $r['link_to_page_url']);

	if (!empty($r['pin_image_url'])) {
		update_post_meta($new_post_id, '_map_location_pin', $r['pin_image_url']);
	}

	if (!empty($r['gallery_images'])) {
		$wpdb->query($wpdb->prepare('
			DELETE
			FROM ' . $wpdb->postmeta . '
			WHERE `post_id` = %d
			AND `meta_key` LIKE "_map_location_gallery_images_-_image_%%"
		', intval($new_post_id)));

		$images = array_map('trim', explode(',', $r['gallery_images']));
		foreach ($images as $i => $img) {
			add_post_meta($new_post_id, '_map_location_gallery_images_-_image_' . $i, $img);
		}
	}

	if (mpfy_batch_bool_value($r['enable_sharing'])) {
		update_post_meta($new_post_id, '_map_location_share', array('facebook','pinterest','twitter','googleplus','email'));
	}

	if (!empty($r['map_tags'])) {
		$map_tags = array_map('trim', explode(',', $r['map_tags']));
		$valid_terms = array();
		foreach ($map_tags as $term_name) {
			$term = get_term_by('name', $term_name, 'location-tag');
			if ($term) {
				$valid_terms[] = $term->term_id;
			}
		}
		if (!empty($valid_terms)) {
			wp_set_object_terms($new_post_id, $valid_terms, 'location-tag');
		}
	}

	$full_address = $r['location_address_or_geo'];
	if (empty($full_address)) {
		$full_address = implode(', ', array_filter(array($r['address'], $r['city'], $r['state'], $r['zip'], $r['country'])));
	}

	$gps_coordinates_regex = '~^([\d\.]+),?\s?([\d\.]+)$~';
	$gps_coordinates = [];
	$is_gps = preg_match( $gps_coordinates_regex, $full_address, $gps_coordinates );

	if ( $is_gps ) {
		print_r($gps_coordinates);
		update_post_meta( $new_post_id, '_map_location_google_location', $gps_coordinates[1] . ',' . $gps_coordinates[2] );
		update_post_meta( $new_post_id, '_map_location_google_location-lat', $gps_coordinates[1] );
		update_post_meta( $new_post_id, '_map_location_google_location-lng', $gps_coordinates[2] );
		update_post_meta( $new_post_id, '_map_location_google_location-address', $full_address );
		$found_address = true;
	} else {
		$url = 'http://nominatim.openstreetmap.org/search?format=json&q=' . urlencode( $full_address );
		$response = wp_remote_get( $url );
		$body = wp_remote_retrieve_body( $response );
		$results = json_decode( $body, true );

		$found_address = false;
		if ( ! empty( $results ) && isset( $results[0] ) ) {
			$result = $results[0];
			update_post_meta( $new_post_id, '_map_location_google_location', $result['lat'] . ',' . $result['lon'] );
			update_post_meta( $new_post_id, '_map_location_google_location-lat', $result['lat'] );
			update_post_meta( $new_post_id, '_map_location_google_location-lng', $result['lon'] );
			update_post_meta( $new_post_id, '_map_location_google_location-address', $full_address );
			$found_address = true;
		}
	}

	if ( ! empty( $r['featured_image'] ) ) {
		mpfy_batch_process_featured_image( $new_post_id, $r['featured_image'] );
	}

	if ( $found_address ) {
		echo ( $action == 'add' ? 'Added' : 'Updated' ) . ' <em>' . $r['title'] . ' (' . $r['address'] . ')</em>';
	} else {
		echo ( $action == 'add' ? 'Added' : 'Updated' ) . ' <em>' . $r['title'] . ' (' . $r['address'] . ')</em>. <span style="color: red;">OSM Nominatim failed to retrieve exact location (manual entry is required).</span>';
	}
	exit;
}
add_action('wp_ajax_mpfy_batch_upload', 'mpfy_batch_trigger_hook', 1000);
