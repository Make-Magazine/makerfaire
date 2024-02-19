<?php
function mpfy_wpt_load_wpthumb() {
	if ( ! function_exists( 'wpthumb' ) ) {
		include_once( MAPIFY_PLUGIN_DIR . '/lib/wpthumb/wpthumb.php' );

		// Prevent WPThumb from hooking into normal wp thumbnail functions
		remove_filter( 'image_downsize', 'wpthumb_post_image', 99 );
	}
}
add_action( 'wp', 'mpfy_wpt_load_wpthumb' );

function mpfy_wpt_get_thumb( $thumbnail, $src, $w, $h, $crop = true ) {
	$thumbnail = wpthumb( $src, array(
		'width'  => $w,
		'height' => $h,
		'crop'   => $crop,
	) );

	return $thumbnail;
}
add_filter( 'mpfy_get_thumb', 'mpfy_wpt_get_thumb', 10, 5 );
