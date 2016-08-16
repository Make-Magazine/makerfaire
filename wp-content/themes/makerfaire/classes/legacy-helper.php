<?php
/**
 * Check if a URL is in a specified whitelist
 *
 * Example whitelist: array( 'mydomain.com', 'mydomain.net' )
 *
 * @param string $url URL to check for
 * @param array $whitelisted_domains Array of whitelisted domains
 * @return bool Returns true if $url is in the $whitelisted_domains
 */
function legacy_is_valid_domain( $url, $whitelisted_domains ) {
	$domain = parse_url( $url, PHP_URL_HOST );

	if ( ! $domain )
		return false;

	// Check if we match the domain exactly
	if ( in_array( $domain, $whitelisted_domains ) )
		return true;

	$valid = false;

	foreach( $whitelisted_domains as $whitelisted_domain ) {
		$whitelisted_domain = '.' . $whitelisted_domain; // Prevent things like 'evilsitetime.com'
		if( strpos( $domain, $whitelisted_domain ) === ( strlen( $domain ) - strlen( $whitelisted_domain ) ) ) {
			$valid = true;
			break;
		}
	}
	return $valid;
}

/**
 * Returns the URL to an image resized and cropped to the given dimensions.
 *
 * You can use this image URL directly -- it's cached and such by our servers.
 * Please use this function to generate the URL rather than doing it yourself as
 * this function uses staticize_subdomain() makes it serve off our CDN network.
 *
 * Somewhat contrary to the function's name, it can be used for ANY image URL, hosted by us or not.
 * So even though it says "remote", you can use it for attachments hosted by us, etc.
 *
 * @link http://vip.wordpress.com/documentation/image-resizing-and-cropping/ Image Resizing And Cropping
 * @param string $url The raw URL to the image (URLs that redirect are currently not supported with the exception of http://foobar.wordpress.com/files/ type URLs)
 * @param int $width The desired width of the final image
 * @param int $height The desired height of the final image
 * @param bool $escape Optional. If true (the default), the URL will be run through esc_url(). Set this to false if you need the raw URL.
 * @return string
 */
function legacy_get_resized_remote_image_url( $url, $width, $height, $escape = true ) {
	if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) ) :
	$width = (int) $width;
	$height = (int) $height;

	// Photon doesn't support redirects, so help it out by doing http://foobar.wordpress.com/files/ to http://foobar.files.wordpress.com/
	if ( function_exists( 'new_file_urls' ) )
		$url = new_file_urls( $url );

	$thumburl = jetpack_photon_url( $url, array( 'resize' => array( $width, $height ) ) );
  
	return ( $escape ) ? esc_url( $thumburl ) : $thumburl;
	endif;
}

function legacy_get_fit_remote_image_url( $url, $width, $height, $escape = true ) {
  if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) ) :
	$width = (int) $width;
	$height = (int) $height;

	// Photon doesn't support redirects, so help it out by doing http://foobar.wordpress.com/files/ to http://foobar.files.wordpress.com/
	if ( function_exists( 'new_file_urls' ) )
		$url = new_file_urls( $url );

	$thumburl = jetpack_photon_url( $url, array( 'fit' => array( $width, $height ) ) );

	return ( $escape ) ? esc_url( $thumburl ) : $thumburl;
	endif;
}

/**
 * Returns the body from a requested url
 *
 * @param string $url The url for the remote request
 * @return string
 */
function legacy_file_get_contents ($url) {
	//Move to wp_remote_get
	$request =   wp_remote_get($url);
	// Get the body of the response
	$json = wp_remote_retrieve_body( $request );
	//$json = wpcom_vip_file_get_contents( $url );

	return $json;
}
