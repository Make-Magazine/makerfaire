<?php
/**
 * v2 of the Maker Faire API - SCHEDULE
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller to grabbing the appropriate API version and files.
 *
 * This page specifically handles the Schedule data.
 *
 * @version 2.0
 */

// Stop any direct calls to this file
defined( 'ABSPATH' ) or die( 'This file cannot be called directly!' );

// Double check again we have requested this file
if ( $type == 'schedule' ) {

	// Set the query args.
	$args = array(
		'no_found_rows'	 => true,
		'post_type' 	 => 'event-items',
		'post_status' 	 => 'publish',
		'posts_per_page' => absint( MF_POSTS_PER_PAGE ),
		'faire'			 => sanitize_title( $faire )
	);
	$query = new WP_Query( $args );


	// Define the API header (specific for Eventbase)
	$header = array(
		'header' => array(
			'version' => esc_html( MF_EVENTBASE_API_VERSION ),
			'results' => intval( $query->post_count ),
		),
	);

	// Initalize the schedule container
	$schedules = array();

	// Loop through the posts
	foreach ( $query->posts as $post ) {
		// Return some post meta
		$app_id = get_post_meta( absint( $post->ID ), 'mfei_record', true );
		$day = get_post_meta( absint( $post->ID ), 'mfei_day', true );
		$start = get_post_meta( absint( $post->ID ), 'mfei_start', true );
		$stop = get_post_meta( absint( $post->ID ), 'mfei_stop', true );
		$dates = mf_get_faire_date( $faire );

		// REQUIRED: Schedule ID
		$schedule['id'] = absint( $post->ID );

		// REQUIED: Application title paired to scheduled item
		$schedule['name'] = html_entity_decode( get_the_title( absint( $app_id ) ), ENT_COMPAT, 'utf-8' );

		// REQUIRED: Schedule start and end times
		if ( strpos( $faire, 'new-york' ) !== false ) {
			$time_zone = ' EST';
		} else {
			$time_zone = ' PST';
		}

		$schedule['time_start'] = date( DATE_ATOM, strtotime( '-1 hour', strtotime( $dates[$day] . $start . $time_zone ) ) );
		$schedule['time_end'] = date( DATE_ATOM, strtotime( '-1 hour', strtotime( $dates[$day] . $stop . $time_zone ) ) );

		// REQUIRED: Venue ID reference
		$locations = get_post_meta( absint( $post->ID ), 'faire_location', true );

		$schedule['venue_id_ref'] = $locations[0];

		// Schedule thumbnails. Nothing more than images from the application it is tied to
		$post_content = json_decode( mf_clean_content( get_page( absint( $app_id ) )->post_content ) );
		$app_image = mf_get_the_maker_image( $post_content );

		$schedule['thumb_img_url'] = esc_url( wpcom_vip_get_resized_remote_image_url( $app_image, '80', '80' ) );
		$schedule['large_img_url'] = esc_url( $app_image );


		// A list of applications assigned to this event (should only be one really...)
		$schedule['entity_id_refs'] = array( absint( $app_id ) );

		// Put the application into our list of schedules
		array_push( $schedules, $schedule );
	}

	// Merge the header and the entities
	$merged = array_merge( $header, array( 'schedule' => $schedules ) );

	// Output the JSON
	echo json_encode( $merged );

	// Reset the Query
	wp_reset_postdata();

}