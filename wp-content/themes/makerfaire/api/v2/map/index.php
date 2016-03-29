<?php
/**
 * v2 of the Maker Faire API - MAP
 *
 * This is the call for the API to handle map data for faires.
 *
 * This page specifically handles the Faire map location data.
 *
 * @version 2.0
 * 
 * Read from location_elements
 */

// Stop any direct calls to this file
defined( 'ABSPATH' ) or die( 'This file cannot be called directly!' );
$type = ( ! empty( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : null );

// Double check again we have requested this file
if ( $type == 'map' ) {

	// Set the query args.
	/*
	 * 
	 $args = array(
		'no_found_rows'  => true,
		'post_type' 	 => 'location',
		'post_status' 	 => 'any',
		'posts_per_page' => absint( MF_POSTS_PER_PAGE ),
		'faire'			 => sanitize_title( $faire ),
	);
	$query = new WP_Query( $args );
	*/
	// Define the API header (specific for Eventbase)
	
	// Init the entities header
	$venues = array();
	
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
  
	$select_query = sprintf("
    SELECT `ID`
      , `annual`
      , `faire_shortcode`
      , `faire_name`
      , `lat`
      , `lng`
      , `faire_year`
      , `event_type`
      , `event_start_dt`
      , `event_end_dt`
      , `cfm_start_dt`
      , `cfm_end_dt`
      , `cfm_url`
      , `faire_url`
      , `ticket_site_url`
      , `free_event`
      , `venue_address_street`
      , `venue_address_city`
      , `venue_address_state`
      , `venue_address_country`
      , `venue_address_postal_code`
      , `venue_address_region` 
      FROM `wp_mf_global_faire` WHERE 1");
 	$mysqli->query("SET NAMES 'utf8'");
	$result = $mysqli->query ( $select_query );
        
        $header = array(
		'header' => array(
			'version' => esc_html( MF_EVENTBASE_API_VERSION ),
			'results' => intval( $result->num_rows ),
		),
	);


  //Initialize the locations array      
  $points = array();

  // Loop through the posts
  while ( $row = $result->fetch_array(MYSQLI_ASSOC)  ) {
    // Open the array.
    $point = array();
    
    // REQUIRED: The venue name
    $point['ID']                        = $row['ID'];
    $point['name']                      = html_entity_decode(trim( $row['faire_name'] ));
    $point['description']               = html_entity_decode(trim( $row['faire_location'] ));
    $point['category']                  = html_entity_decode(trim( $row['event_type'] ));
    $point['faire_shortcode']           = html_entity_decode(trim( $row['faire_shortcode'] ));
    $point['annual']                    = $row['annual'];
    $point['faire_name']                = html_entity_decode(trim( $row['faire_name'] ));
    $point['faire_year']                = $row['faire_year'];
    $point['event_type']                = html_entity_decode(trim( $row['event_type'] ));
    $point['event_start_dt']            = html_entity_decode(trim( $row['event_start_dt'] ));
    $point['event_end_dt']              = html_entity_decode(trim( $row['event_end_dt'] ));
    $point['cfm_start_dt']              = html_entity_decode(trim( $row['cfm_start_dt'] ));
    $point['cfm_end_dt']                = html_entity_decode(trim( $row['cfm_end_dt'] ));
    $point['cfm_url']                   = html_entity_decode(trim( $row['cfm_url'] ));
    $point['faire_url']                 = html_entity_decode(trim( $row['faire_url'] ));
    $point['ticket_site_url']           = html_entity_decode(trim( $row['ticket_site_url'] ));
    $point['free_event']                = html_entity_decode(trim( $row['free_event'] ));
    $point['venue_address_street']      = html_entity_decode(trim( $row['venue_address_street'] ));
    $point['venue_address_city']        = html_entity_decode(trim( $row['venue_address_city'] ));
    $point['venue_address_state']       = html_entity_decode(trim( $row['venue_address_state'] ));
    $point['venue_address_country']     = html_entity_decode(trim( $row['venue_address_country'] ));
    $point['venue_address_postal_code'] = html_entity_decode(trim( $row['venue_address_postal_code'] ));
    $point['venue_address_region']      = html_entity_decode(trim( $row['venue_address_region'] ));
    // Get the child locations
    $point['lat']                       = $row['lat'];
    $point['lng']                       = $row['lng'];

    // Put the maker into our list of makers
    array_push($points, $point); 
  }
  // Merge the header and the entities
  $merged = array_merge( $header, array("Locations"=>$points) );

  // Output the JSON
  echo json_encode( $merged );

  exit;
}
