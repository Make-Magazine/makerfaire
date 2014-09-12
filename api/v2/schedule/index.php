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

		$schedule['time_start'] = date( DATE_ATOM, strtotime( '-1 hour', strtotime( $dates[$day] . $start . $dates['time_zone'] ) ) );
		$schedule['time_end'] = date( DATE_ATOM, strtotime( '-1 hour', strtotime( $dates[$day] . $stop . $dates['time_zone'] ) ) );
		// Rename the field, keeping 'time_end' to ensure this works.
		$schedule['time_stop'] = date( DATE_ATOM, strtotime( '-1 hour', strtotime( $dates[$day] . $stop . $dates['time_zone'] ) ) );

		// REQUIRED: Venue ID reference
		$locations = get_post_meta( absint( $post->ID ), 'faire_location', true );

		$schedule['venue_id_ref'] = $locations[0];

		// Schedule thumbnails. Nothing more than images from the application it is tied to
		$post_content = json_decode( mf_clean_content( get_page( absint( $app_id ) )->post_content ) );
		$app_image = mf_get_the_maker_image( $post_content );

		$schedule['thumb_img_url'] = esc_url( wpcom_vip_get_resized_remote_image_url( $app_image, '80', '80' ) );
		$schedule['large_img_url'] = esc_url( wpcom_vip_get_resized_remote_image_url( $app_image, '600', '600' ) );


		// A list of applications assigned to this event (should only be one really...)
		$schedule['entity_id_refs'] = array( absint( $app_id ) );

		$event_maker_ids = explode(',',get_post_meta( absint( $post->ID ), 'mfei_event', true ));
		
		// Application Makers

		if((count($event_maker_ids) > 0) && ($event_maker_ids[0] !== '')) {
			foreach ( $event_maker_ids as $maker_id ) {
				$maker_ids[] = absint( $maker_id );
			}
		} else {
			$maker_ids = get_makers_from_app(absint($app_id));
		}


		$schedule['maker_id_refs'] = ( ! empty( $maker_ids ) ) ? $maker_ids : null;

		$maker_ids = array();

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