<?php
error_reporting( 'NONE' );
/**
 * v2 of the Maker Faire API - ENTITY
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller to grabbing the appropriate API version and files.
 *
 * This page specifically handles the Entity type for the mobile app. AKA the applications.
 *
 * @version 2.0
 */

// Stop any direct calls to this file
defined( 'ABSPATH' ) or die( 'This file cannot be called directly!' );

// We need to have access to the $mfform object so we can utilize the merge_fields() function
global $mfform;

// Double check again we have requested this file
if ( $type == 'sponsor' ) {

	// Set the query args.
	$args = array(
		'no_found_rows'	 => true,
		'post_type'		 => 'mf_form',
		'post_status'	 => 'accepted',
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


	// Initalize the app container
	$apps = array();

	// Loop through the posts
	foreach ( $query->posts as $post ) {
		// Store the app information
		$app_data = json_decode( mf_clean_content( $post->post_content ) );

		// REQUIRED: Application ID
		$app['id'] = absint( $post->ID );

		// REQUIRED: Application name
		$app['name'] = html_entity_decode( get_the_title(), ENT_COMPAT, 'utf-8' );

		// Application Thumbnail and Large Images
		$app_image = mf_get_the_maker_image( $app_data );
		$app['thumb_img_url'] = esc_url( wpcom_vip_get_resized_remote_image_url( $app_image, '80', '80' ) );
		$app['large_image_url'] = esc_url( $app_image );
		// Should actually be this... Adding it in for the future.
		$app['large_img_url'] = esc_url( $app_image );

		// Application Locations
		$locations = mf_get_locations( $post->ID, true );

		$location_output = array();
		foreach ( $locations as $location ) {
			$location_output[] = $location->ID;
		}

		if ( empty ( $location_output ) )
			$location_output = null;

		$app['venue_id_ref'] = $location_output[0];

		// Application Makers
		$maker_args = array(
			'post_type' => 'maker',
			'posts_per_page' => 20,
			'faire' => sanitize_title( $faire ),
			'meta_key' => 'mfei_record',
			'meta_value' => absint( $post->ID ),
		);
		$makers = new WP_Query( $maker_args );

		$maker_ids = array();

		foreach ( $makers->posts as $maker ) {
			$maker_ids[] = absint( $maker->ID );
		}

		$app['child_id_refs'] = ( ! empty( $maker_ids ) ) ? $maker_ids : null;

		// Application Categories
		$cats = wp_get_post_terms( absint( $post->ID ), array( 'category', 'post_tag', 'group' ) );

		$category_ids = array();

		if ( $cats && ! is_wp_error( $cats ) ) {
			foreach( $cats as $cat ) {
				$category_ids[] = absint( $cat->term_id );
			}
		} else {
			$category_ids = null;
		}

		$app['category_id_refs'] = $category_ids;

		// Application Description
		$app_description_field = $mfform->merge_fields( 'project_description', $app_data->form_type );

		$app['description'] = ( !empty( $app_description_field ) ) ? $app_data->{$app_description_field} : '';

		// Application YouTube URL
		$video_field = $mfform->merge_fields( 'project_video', $app_data->form_type );
		$app['youtube_url'] = ( ! empty( $app_data->{$video_field} ) ) ? esc_url( $app_data->{$video_field} ) : null;

		// Application Website URL
		$website_field = $mfform->merge_fields( 'project_website', $app_data->form_type );
		$app['website_url'] = ( ! empty( $app_data->{$website_field} ) ) ? esc_url( $app_data->{$website_field} ) : null;

		// Application Email
		$app['email'] = ( ! empty( $app_data->email ) ) ? sanitize_email( $app_data->email ) : null;

		// Application Tags
		$tags_list = get_the_tags();
		$tags = '';

		if ( ! empty( $tags_list ) && is_array( $tags_list ) ) {
			$last_tag = end( $tags_list );
			foreach ( $tags_list as $tag ) {
				$tags .= esc_html( $tag->name );

				// Don't append the comma if it's the last item in the array
				if ( $tag !== $last_tag )
					$tags .= ',';
			}
		}

		$app['tags'] = $tags;

		// Put the application into our list of apps
		array_push( $apps, $app );
	}

	// Merge the header and the entities
	$merged = array_merge( $header, array( 'entity' => $apps ) );

	// Output the JSON
	echo json_encode( $merged );

	// Reset the Query
	wp_reset_postdata();

}