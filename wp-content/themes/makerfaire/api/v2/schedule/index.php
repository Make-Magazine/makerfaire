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
	
	$faire = sanitize_title( $faire );
	
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$select_query = sprintf("SELECT `wp_mf_schedule`.`ID`,
                                        `wp_mf_schedule`.`entry_id`,
                                        `wp_mf_schedule`.`start_dt`,
                                        `wp_mf_schedule`.`end_dt`,
                                        `wp_mf_schedule`.`day`,
                                         wp_mf_entity.project_photo,
                                         wp_mf_entity.presentation_title,  
                                          `wp_mf_entity`.`desc_short` as Description,
           
                                        wp_mf_faire_subarea.ID as venue_id,
                                       (select group_concat( distinct maker_id separator ',') as Makers 
                         from wp_mf_maker_to_entity maker_to_entity 
                         where wp_mf_entity.lead_id               = maker_to_entity.entity_id AND 
                               maker_to_entity.maker_type  != 'Contact' 
                         group by maker_to_entity.entity_id
                        ) as exhibit_makers
                                FROM `wp_mf_schedule`, 
									  wp_mf_entity,
                                     `wp_mf_location`,                                                                            
                                      wp_mf_faire_area, 
                                      wp_mf_faire_subarea  
                                WHERE  `wp_mf_schedule`.faire = '$faire' and 
                                     wp_mf_entity.status = 'Accepted' and 
                                     wp_mf_schedule.entry_id = wp_mf_entity.lead_id AND 
                                     wp_mf_schedule.location_id = wp_mf_location.ID AND 
                                        wp_mf_faire_subarea.ID  = wp_mf_location.subarea_id AND
                                         wp_mf_faire_area.ID     = wp_mf_faire_subarea.area_id  "
                                );
 	$mysqli->query("SET NAMES 'utf8'");
 		
        $result = $mysqli->query($select_query) or trigger_error($mysqli->error."[$select_query]");

        // Initalize the schedule container
	$schedules = array();

	// Loop through the posts
	while ( $row = $result->fetch_array(MYSQLI_ASSOC)  ) {
	
		// Return some post meta
		$schedule_id    = $row['ID'];
		$entry_id       = $row['entry_id'];
		$app_id         = $entry_id;
		$day            = $row['day'];
		$start          = strtotime($row['start_dt']);
		$stop           = strtotime($row['end_dt']);

		// REQUIRED: Schedule ID
		$schedule['id'] = $schedule_id;
		$schedule_name  = isset ( $row['presentation_title'] ) ? $row['presentation_title'] : '';
		$project_photo  = isset ( $row['project_photo'] )      ? $row['project_photo']      : '';
                
		// REQUIED: Application title paired to scheduled item
		$schedule['name']       = html_entity_decode( $schedule_name , ENT_COMPAT, 'utf-8' );
		$schedule['description'] = $row['Description'];                //??
    $schedule['time_start'] = date( DATE_ATOM, strtotime( '+4 hour',  $start ) );
		$schedule['time_end']   = date( DATE_ATOM, strtotime( '+4 hour', $stop ) );
		
		//ORIGINAL CALL
		//$schedule['time_start'] = date( DATE_ATOM, strtotime( '-1 hour', strtotime( $dates[$day] . $start . $dates['time_zone'] ) ) );
		//$schedule['time_end'] = date( DATE_ATOM, strtotime( '-1 hour', strtotime( $dates[$day] . $stop . $dates['time_zone'] ) ) );
		// Rename the field, keeping 'time_end' to ensure this works.
		$schedule['time_stop'] = date( DATE_ATOM, strtotime( '+4 hour', $stop ) );			

		// Schedule thumbnails. Nothing more than images from the application it is tied to
		$app_image = $project_photo;
                //find out if there is an override image for this page
                $overrideImg = findOverride($entry_id,'app');  
                if($overrideImg!='') $app_image = $overrideImg;
                
		$schedule['thumb_img_url'] = esc_url( legacy_get_resized_remote_image_url( $app_image, '80', '80' ) );
		$schedule['large_img_url'] = esc_url( legacy_get_resized_remote_image_url( $app_image, '600', '600' ) );

		// REQUIRED: Venue ID reference
		$schedule['venue_id_ref'] = $row['venue_id'];                //??
		$maker_ids = $row['exhibit_makers'];

		$app['exhibit_makers'] = ( ! empty( $maker_ids ) ) ? explode(',',$maker_ids) : null;
		
		// A list of applications assigned to this event (should only be one really...)
		$schedule['entity_id_refs'] = array_merge(array( $entry_id));

		// Application Makers
		$schedule['maker_id_refs'] = $maker_ids;
				
		// Put the application into our list of schedules
		array_push( $schedules, $schedule );
	}
	$header = array(
			'header' => array(
					'version' => esc_html( MF_EVENTBASE_API_VERSION ),
					'results' => intval( $result->num_rows ),
			),
	);
	
	// Merge the header and the entities
	$merged = array_merge( $header, array( 'schedule' => $schedules ) );

	// Output the JSON
	echo json_encode( $merged );

	// Reset the Query
	wp_reset_postdata();

}