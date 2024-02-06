<?php

function random_pic($dir = '/uploads'){
    $files = glob($dir . '/*.*');
	if(!empty($files)) {
    	$file = array_rand($files);
    	return str_replace(ABSPATH, get_site_url() . '/', $files[$file]);
	}
}


function skip_photon_lazy_images( $blocked_classes ) {
    $blocked_classes[] = 'wp-image-643979';
    return $blocked_classes;
}
if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) ) {
	add_filter( 'jetpack_lazy_images_blocked_classes', 'skip_photon_lazy_images' );
}

/* preload the featured image on all pages for better page load speeds
<<BETTER IDEA, ADD AN ACF FOR PRELOAD IMAGE AND PRELOAD ONLY IF THAT ACF IS SET>>
function wpp_preloadimages() {
	$post_id = get_queried_object_id();
	if((is_single($post_id) || is_page($post_id)) && has_post_thumbnail($post_id)) {
		echo '<link rel="preload" as="image" href="' . get_the_post_thumbnail_url($post_id) . '" />';
	}
}
add_action( 'wp_head', 'wpp_preloadimages' );*/