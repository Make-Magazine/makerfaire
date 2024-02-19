<?php

include_once( 'mpfy_map.class.php' );
include_once( 'mpfy_map_location.class.php' );

// Returns the plugin files version
function mpfy_get_version() {
	$version = get_option( 'mpfy_plugin_version' );
	
	if ( ! $version ) {
		update_option( 'mpfy_flush_required', 'y' );
		update_option( 'mpfy_plugin_version', MAPIFY_PLUGIN_VERSION );
		return MAPIFY_PLUGIN_VERSION;
	}

	return $version;
}

// Returns the plugin cache buster query string
function mpfy_get_cache_buster_version() {
	$is_cache_buster_activated = get_field( 'mapifypro_enable_javascript_cache_buster', 'option' );
	$creation_interval         = get_field( 'mapifypro_cache_buster_creation_interval', 'option' );
	$cache_buster_version      = MAPIFY_PLUGIN_VERSION;
	$transient_name            = 'mpfy_cache_buster_version';

	if ( $is_cache_buster_activated ) {
		if ( 'always' === $creation_interval ) {
			$cache_buster_version = uniqid();
		} else {
			$cache_buster_version = get_transient( $transient_name );

			if ( ! $cache_buster_version ) {
				$cache_buster_version = uniqid();

				switch ( $creation_interval ) {
					case 'hourly':
						$expiration = HOUR_IN_SECONDS;
						break;

					case 'twice_a_day':
						$expiration = 6 * HOUR_IN_SECONDS;
						break;

					case 'daily':
						$expiration = 12 * HOUR_IN_SECONDS;
						break;

					default:
						$expiration = 0;
						break;
				}

				set_transient( $transient_name, $cache_buster_version, $expiration );
			}
		}

		$cache_buster_version = MAPIFY_PLUGIN_VERSION . '-' . $cache_buster_version;
	}

	return $cache_buster_version;
}

// Update license version
function mpfy_update_plugin_version() {
	update_option( 'mpfy_plugin_version', MAPIFY_PLUGIN_VERSION );
	do_action( 'mpfy_version_updated' );
}

// Returns the latest plugin file version that has been license-activated
function mpfy_is_new_license_version() {
	$mpfy_plugin_license_version = get_option( 'mpfy_plugin_license_version', 0 );
	return version_compare( $mpfy_plugin_license_version, MAPIFY_PLUGIN_VERSION, '<' );
}

// Update license version
function mpfy_update_license_version() {
	update_option( 'mpfy_plugin_license_version', MAPIFY_PLUGIN_VERSION );
}

// Returns a url to a thumbnail of the provided src
function mpfy_get_thumb($src, $w, $h, $crop=true) {
	if (substr($src, 0, 2) == '//') {
		$src = 'http' . (is_ssl() ? 's' : '') . ':' . $src;
	}
	$thumb = apply_filters('mpfy_get_thumb', $src, $src, $w, $h, $crop);
	return $thumb;
}

// Converts a url into a local path
function mpfy_get_file_real_path($source_url) {
	$dirs = wp_upload_dir();

	$base_url = parse_url($dirs['baseurl']);
	$b_scheme = isset($base_url['scheme']) ? $base_url['scheme'] : 'http';
	$b_host = isset($base_url['host']) ? $base_url['host'] : '';
	$b_port = isset($base_url['port']) ? ':' . $base_url['port'] : '';
	$b_path = isset($base_url['path']) ? $base_url['path'] : '/';

	$relative_url = $source_url;
	if (substr($source_url, 0, 1) == '/') {
		$relative_url = preg_replace('/^' . preg_quote($b_path, '/') . '/i', '', $relative_url);
	} else {
		$relative_url = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $relative_url);
		$relative_url = preg_replace('/^' . preg_quote(preg_replace('/^www\./', '', $b_host) . $b_port . $b_path, '/') . '/i', '', $relative_url);
	}

	$real_path = preg_replace('/\/+|\\+/', DIRECTORY_SEPARATOR, $dirs['basedir'] . '/' . $relative_url);

	if (file_exists($real_path)) {
		return $real_path;
	}

	return '';
}

// Converts a local path to a url
function mpfy_get_file_real_url($url) {
	if (preg_match('/^https?:\/\//', $url)) {
		return $url;
	}

	$upload_dir = wp_upload_dir();
	if (!stristr($url, $upload_dir['baseurl'])) {
		$url = $upload_dir['baseurl'] . $url;
	}

	return $url;
}

// Converts HEX color code to an array of RGB values
function mpfy_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);

   return $rgb;
}

// Unshifts values before the entered key
function mpfy_array_unshift_key($array, $key, $new_keyvalues) {
	$new_array = array();

	$indexes = array_flip(array_keys($array));
	if (!isset($indexes[$key])) {
		return $array; // key not found at all; return original array; maybe raise an error?
	}

	$index = $indexes[$key];
	// append first part
	$new_array += array_slice($array, 0, $index, true);

	// append $new_keyvalues
	$new_array += $new_keyvalues;

	// append second part
	$new_array += array_slice($array, $index, NULL, true);
	return $new_array;
}

// Push values after the entered key
function mpfy_array_push_key($array, $key, $new_keyvalues) {
	$new_array = array();

	$indexes = array_flip(array_keys($array));
	if (!isset($indexes[$key])) {
		return $array; // key not found at all; return original array; maybe raise an error?
	}

	$index = $indexes[$key];
	// append first part
	$new_array += array_slice($array, 0, $index + 1, true);

	// append $new_keyvalues
	$new_array += $new_keyvalues;

	// append second part
	$new_array += array_slice($array, $index + 1, NULL, true);
	return $new_array;
}

// Gets a single meta and makes sure the return value is boolean
function mpfy_meta_to_bool($post_id, $meta_key, $default) {
	$value = get_post_meta($post_id, $meta_key, true);

	// return if value is already boolean
	if (is_bool($value)) {
		return $value;
	}

	// the default should be returned if a blank string value (i.e. when post meta does not exist)
	if ($value === '') {
		return $default;
	}

	// consider only the listed values as true
	$true_values = array(1, '1', 'true', 'y', 'yes');
	if (in_array($value, $true_values)) {
		return true;
	}

	// all other cases are considered false
	return false;
}

// Return a meta or fallback to a default
function mpfy_meta( $post_id, $meta_key, $default ) {
	$value = get_post_meta( $post_id, $meta_key, true );
	return $value ? $value : $default;
}

// Return a meta or fallback to a default
function mpfy_meta_label( $post_id, $meta_key, $default ) {
	return esc_html( __( mpfy_meta( $post_id, $meta_key, $default ) ), 'mpfy' );
}

// Returns the post types which are considered as locations
function mpfy_get_supported_post_types() {
	$post_types = array( 'map-location' );

	if ( mpfy_blog_post_is_map_location() ) {
		$post_types[] = 'post';
	}

	return apply_filters( 'mpfy_supported_post_types', $post_types );
}

// Return whether the blog-post is included as the map-location or not
function mpfy_blog_post_is_map_location() {
	return apply_filters( 'mpfy_blog_post_is_map_location', true );
}

// Load non-compat sidebar
function mpfy_load_non_compat_sidebar() {
	$template_sidebar  = locate_template( 'sidebar.php' );
	$theme_compat_path = ABSPATH . WPINC . '/theme-compat/sidebar.php'; 
	
	if ( ! empty( $template_sidebar ) && $template_sidebar !== $theme_compat_path ) {
		get_sidebar(); 
	}
}

// Get current WC_AM API Key
function mpfy_get_api_key() {
	global $wcam_lib;

	$api_key_data = get_option( $wcam_lib->data_key );
	$api_key      = '';

	if ( $api_key_data && isset( $api_key_data[ $wcam_lib->data_key . '_api_key' ] ) ) {
		$api_key = trim( $api_key_data[ $wcam_lib->data_key . '_api_key' ] );
	}

	return $api_key;
}

// Get current WC_AM API is_activated status
function mpfy_api_is_activated() {
	global $wcam_lib;

	$is_activated      = false;
	$activation_status = isset( $wcam_lib->wc_am_activated_key ) ? get_option( $wcam_lib->wc_am_activated_key ) : false;

	if ( 'Activated' === $activation_status ) {
		$is_activated = true;
	}

	return $is_activated;
}

/**
 * Function to activate WC_AM API key
 * 
 * @param string WC_AM API key.
 * @param string WC_AM API Instance.
 * @return boolean Whether the API key is successfully activated or not.
 */
function mpfy_activate_api_key( $input_api_key = null ) {
	global $wcam_lib;

	$api_key          = $input_api_key ? $input_api_key : mpfy_get_api_key();
	$is_activated     = false;
	$activate_results = json_decode( $wcam_lib->activate( array( 'api_key'  => $api_key ) ), true );

	// if the license is successfully activated
	if ( $activate_results[ 'success' ] === true && $activate_results[ 'activated' ] === true ) {
		update_option( $wcam_lib->wc_am_activated_key, 'Activated' );
		update_option( $wcam_lib->wc_am_deactivate_checkbox_key, 'off' );

		$is_activated = true;
	}

	return $is_activated;
}