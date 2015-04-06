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

$type = ( ! empty( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : null );
$faire = ( ! empty( $_REQUEST['faire'] ) ? sanitize_text_field( $_REQUEST['faire'] ) : null );
// Double check again we have requested this file
if ( $type == 'schedule' ) {
	$header = array(
			'header' => array(
					'version' => esc_html( MF_EVENTBASE_API_VERSION ),
					'results' => intval( $query->post_count ),
			),
	);
	
	$faire = sanitize_title( $faire );
	
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$select_query = sprintf("SELECT `wp_mf_schedule`.`ID`,
    `wp_mf_schedule`.`entry_id`,
    `wp_mf_schedule`.`location_id`,
    `wp_mf_schedule`.`faire`,
    `wp_mf_schedule`.`start_dt`,
    `wp_mf_schedule`.`end_dt`,
    `wp_mf_schedule`.`day`
	FROM `wp_mf_schedule` WHERE faire = '$faire'");
 	$result = $mysqli->query ( $select_query );
	
	// Initalize the schedule container
	$schedules = array();

	// Loop through the posts
	while ( $row = $result->fetch_row () ) {
	
		// Return some post meta
		$entry_id = $row[1];
		$app_id = $entry_id;
		$day = $row[6];
		$start = strtotime($row[4]);
		$stop = strtotime($row[5]);
		//$dates = mf_get_faire_date( $faire );

		// REQUIRED: Schedule ID
		$schedule['id'] = $entry_id;
		$entry = GFAPI::get_entry($entry_id);
		$schedule_name = isset ( $lead ['151'] ) ? $lead ['151'] : '';
		$project_photo =  isset ( $lead ['22'] ) ? $lead ['22'] : '';
		// REQUIED: Application title paired to scheduled item
		$schedule['name'] = html_entity_decode( $schedule_name , ENT_COMPAT, 'utf-8' );
		$schedule['time_start'] = date( DATE_ATOM, strtotime( '-1 hour',  $start ) );
		$schedule['time_end'] = date( DATE_ATOM, strtotime( '-1 hour', $stop ) );
		
		//ORIGINAL CALL
		//$schedule['time_start'] = date( DATE_ATOM, strtotime( '-1 hour', strtotime( $dates[$day] . $start . $dates['time_zone'] ) ) );
		//$schedule['time_end'] = date( DATE_ATOM, strtotime( '-1 hour', strtotime( $dates[$day] . $stop . $dates['time_zone'] ) ) );
		// Rename the field, keeping 'time_end' to ensure this works.
		$schedule['time_stop'] = date( DATE_ATOM, strtotime( '-1 hour', $stop ) );

		// REQUIRED: Venue ID reference
		$locations = get_post_meta( absint( $post->ID ), 'faire_location', true );

		$schedule['venue_id_ref'] = $locations[0];

		// Schedule thumbnails. Nothing more than images from the application it is tied to
		//$post_content = json_decode( mf_clean_content( get_page( absint( $app_id ) )->post_content ) );
		$app_image = $project_photo;

		$schedule['thumb_img_url'] = esc_url( legacy_get_resized_remote_image_url( $app_image, '80', '80' ) );
		$schedule['large_img_url'] = esc_url( legacy_get_resized_remote_image_url( $app_image, '600', '600' ) );


		// A list of applications assigned to this event (should only be one really...)
		$schedule['entity_id_refs'] = array( absint( $app_id ) );

		//$event_maker_ids = explode(',',get_post_meta( absint( $post->ID ), 'mfei_event', true ));
		
		// Application Makers

		/* NO Longer have Maker ID's
		 if((count($event_maker_ids) > 0) && ($event_maker_ids[0] !== '')) {
			foreach ( $event_maker_ids as $maker_id ) {
				$maker_ids[] = absint( $maker_id );
			}
		} else {
			$maker_ids = get_makers_from_app(absint($app_id));
		}
		

		$schedule['maker_id_refs'] = ( ! empty( $maker_ids ) ) ? $maker_ids : null;

		$maker_ids = array();
		*/
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