<?php
/**
 * v2 of the Maker Faire API - MAKER
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller to grabbing the appropriate API version and files.
 *
 * This page specifically handles the Maker data.
 *
 * @version 2.0
 */

// Stop any direct calls to this file
defined( 'ABSPATH' ) or die( 'This file cannot be called directly!' );

// Double check again we have requested this file
if ( $type == 'maker_export' ) {

	// Set the query args.
	$args = array(
		'no_found_rows'  => true,
		'post_type' 	 => 'maker',
		'post_status' 	 => 'any',
		'posts_per_page' => absint( MF_POSTS_PER_PAGE ),
		'faire'			 => sanitize_title( $faire ),
	);
	$query = new WP_Query( $args );

	// Define the API header (specific for Eventbase)
	$header = array(
		'header' => array(
			'version' => esc_html( MF_EVENTBASE_API_VERSION ),
			'results' => intval( $query->post_count ),
		),
	);


	// Init the entities header
	$makers = array();

	// Loop through the posts
	foreach ( $query->posts as $post ) {

		// REQUIRED: The maker ID
		$maker['id'] = absint( $post->ID );

		// REQUIRED: The maker name
		$maker['name'] = html_entity_decode( get_the_title(), ENT_COMPAT, 'utf-8' );

		// Maker Thumbnail and Large Images
		$maker_image = get_post_meta( absint( $post->ID ), 'photo_url', true );
		$maker['thumb_img_url'] = esc_url( wpcom_vip_get_resized_remote_image_url( $maker_image, '80', '80' ) );
		$maker['large_image_url'] = esc_url( wpcom_vip_get_resized_remote_image_url( $maker_image, '600', '600' ) );;

		// Application ID this maker is assigned to
		$maker['child_id_refs'] = array_unique( get_post_meta( absint( $post->ID ), 'mfei_record' ) );

		// Maker bio information
		$maker['description'] = ( ! empty( $post->post_content ) ) ? mf_clean_content( $post->post_content ) : null;

		// Maker Video link
		$maker_video = get_post_meta( absint( $post->ID ), 'video', true );
		$maker['youtube_url'] = ( ! empty( $maker_video ) ) ? esc_url( $maker_video ) : null;

		// Maker Website link
		$maker_website = get_post_meta( absint( $post->ID ), 'website', true );
		$maker['website_url'] = ( ! empty( $maker_website ) ) ? esc_url( $maker_website ) : null;

		// Maker Email link
		$maker_email = get_post_meta( absint( $post->ID ), 'email', true );
		$maker['email'] = ( ! empty( $maker_email) ) ? $maker_email : null;


		// Put the maker into our list of makers
		array_push( $makers, $maker );
	}

	// Merge the header and the entities
	$merged = array_merge( $header, array( 'entity' => $makers ) );

	// Output the JSON
	echo json_encode( $merged );

	// Reset the Query
	wp_reset_postdata();
}
