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
$upcoming = ( ! empty( $_REQUEST['upcoming'] ) ? sanitize_text_field( $_REQUEST['upcoming'] ) : false );
$number = ( ! empty( $_REQUEST['number'] ) ? sanitize_text_field( $_REQUEST['number'] ) : null );

// Double check again we have requested this file
if ( $type == 'map' ) {

  // Set the query args.
  /*
   *
   $args = array(
    'no_found_rows'  => true,
    'post_type'    => 'location',
    'post_status'    => 'any',
    'posts_per_page' => absint( MF_POSTS_PER_PAGE ),
    'faire'      => sanitize_title( $faire ),
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

  $select_query = '
    SELECT  `ID`,
			`faire_shortcode`,
			`faire_name`,
			`lat`,
			`lng`,
			`faire_year`,
			`event_type`,
			`event_dt`,
			`event_start_dt`,
			`event_end_dt`,
			`cfm_start_dt`,
			`cfm_end_dt`,
			`cfm_url`,
			`faire_url`,
			`ticket_site_url`,
			`free_event`,
			`venue_address_street`,
			`venue_address_city`,
			`venue_address_state`,
			`venue_address_country`,
			`venue_address_postal_code`,
			`venue_address_region`,
			states.state FROM `wp_mf_global_faire` left outer join states on state_code = venue_address_state';
  if($upcoming == true) {
	  $select_query .= ' where event_start_dt >= CURDATE()
	  					ORDER BY `wp_mf_global_faire`.`event_start_dt` ASC';
  }
  if($number != null && is_numeric($number)) {
	  $select_query .= ' limit ' . $number;
  }
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
    $point['description']               = html_entity_decode(trim( $row['faire_name'] ));
    $point['category']                  = html_entity_decode(trim( $row['event_type'] ));
    $point['faire_shortcode']           = html_entity_decode(trim( $row['faire_shortcode'] ));
    $point['faire_name']                = html_entity_decode(trim( $row['faire_name'] ));
    $point['faire_year']                = $row['faire_year'];
    $point['event_type']                = html_entity_decode(trim( $row['event_type'] ));
    $point['event_dt']                  = html_entity_decode(trim( $row['event_dt'] ));

    $point['event_start_dt']            = date('m/d/Y h:i:s a', strtotime($row['event_start_dt']));
    $point['event_end_dt']              = date('m/d/Y h:i:s a', strtotime($row['event_end_dt']));
    $point['cfm_start_dt']              = html_entity_decode(trim( $row['cfm_start_dt'] ));
    $point['cfm_end_dt']                = html_entity_decode(trim( $row['cfm_end_dt'] ));
    $point['cfm_url']                   = html_entity_decode(trim( $row['cfm_url'] ));
    $point['faire_url']                 = html_entity_decode(trim( $row['faire_url'] ));
    $point['ticket_site_url']           = html_entity_decode(trim( $row['ticket_site_url'] ));
    $point['free_event']                = html_entity_decode(trim( $row['free_event'] ));
    $point['venue_address_street']      = html_entity_decode(trim( $row['venue_address_street'] ));
    $point['venue_address_city']        = html_entity_decode(trim( $row['venue_address_city'] ));
    $point['venue_address_state']       = ($row['state']!=NULL ? html_entity_decode(trim( $row['state'] )): html_entity_decode(trim( $row['venue_address_state'] )));
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


function JSdate($in,$type){
    if($type=='date'){
        //Dates are patterned 'yyyy-MM-dd'
        preg_match('/(\d{4})-(\d{2})-(\d{2})/', $in, $match);
    } elseif($type=='datetime'){
        //Datetimes are patterned 'yyyy-MM-dd hh:mm:ss'
        preg_match('/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/', $in, $match);
    }

    $year = (int) $match[1];
    $month = (int) $match[2] - 1; // Month conversion between indexes
    $day = (int) $match[3];

    if ($type=='date'){
        return "Date($year, $month, $day)";
    } elseif ($type=='datetime'){
        $hours = (int) $match[4];
        $minutes = (int) $match[5];
        $seconds = (int) $match[6];
        return "Date($year, $month, $day, $hours, $minutes, $seconds)";
    }
}
