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
if ( $type == 'maker' ) {

	// Set the query args.
	$args = array(
		'no_found_rows'  => true,
		'post_type' 	 => 'mf_form',
		'post_status' 	 => array('accepted'),
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

    $maker_meta_content = json_decode( $post->post_content);
		// REQUIRED: The maker name
		if (isset($maker_meta_content->maker_name)) {
			$maker['name'] = html_entity_decode($maker_meta_content->maker_name, ENT_COMPAT, 'utf-8' );
		} else {
			$maker['name'] = html_entity_decode(get_the_title(), ENT_COMPAT, 'utf-8' );
    }
    
		// Maker Thumbnail and Large Images
		if (isset($maker_meta_content->maker_photo)) {
			$maker_image = $maker_meta_content->maker_photo;
			$maker['thumb_img_url'] = esc_url( wpcom_vip_get_resized_remote_image_url( $maker_image, '80', '80' ) );
			$maker['large_image_url'] = esc_url( wpcom_vip_get_resized_remote_image_url( $maker_image, '600', '600' ) );
		} else {
			$maker['thumb_img_url'] = '';
			$maker['large_image_url'] = '';
		}

		// Application ID this maker is assigned to
		$maker['child_id_refs'] = array_unique( get_post_meta( absint( $post->ID ), 'mfei_record' ) );

		// Maker bio information
		$maker['description'] = ((isset($maker_meta_content->maker_bio)) && ($maker_meta_content->maker_bio != '')) ? html_entity_decode($maker_meta_content->maker_bio) : null;

		// Maker Video link
		//$maker_video = get_post_meta( absint( $post->ID ), 'video', true );
		//$maker['youtube_url'] = ( ! empty( $maker_video ) ) ? esc_url( $maker_video ) : null;
		$maker['youtube_url'] = '';

		// Maker Website link
		//$maker_website = get_post_meta( absint( $post->ID ), 'website', true );
		//$maker['website_url'] = ( ! empty( $maker_website ) ) ? esc_url( $maker_website ) : null;
		$maker['website_url'] = '';

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
