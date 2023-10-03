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