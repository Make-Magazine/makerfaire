<?php
/**
 * v2 of the Maker Faire API - LOCATION_CATEGORY
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller to grabbing the appropriate API version and files.
 *
 * This page specifically handles the Location Category data. This will output the categories for locations on the map in the mobile app
 * This API endpoint will generate location categories into the entity object for Eventbase
 *
 * @version 2.0
 */

// Stop any direct calls to this file
defined( 'ABSPATH' ) or die( 'This file cannot be called directly!' );
$type = ( ! empty( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : null );
$faire = ( ! empty( $_REQUEST['faire'] ) ? sanitize_text_field( $_REQUEST['faire'] ) : null );

// Double check again we have requested this file
if ( $type == 'location_category' ) {
	// By default we have the taxonomy set to show_ui => false as these will never be updated
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$select_query = sprintf("SELECT `wp_mf_location_elements`.`ID`,
   		 	`wp_mf_location_elements`.`faire`,
    		`wp_mf_location_elements`.`element`
			FROM `wp_mf_location_elements`
			");
 	$mysqli->query("SET NAMES 'utf8'");
	$result = $mysqli->query ( $select_query );
	
	// Loop through the posts
	// Define the API header (specific for Eventbase)
	$header = array(
		'header' => array(
			'version' => esc_html( MF_EVENTBASE_API_VERSION ),
		),
	);

	// Init the entities header
	$loc_cats = array();

	while ( $row = $result->fetch_row () ) {
	

		// REQUIRED: Location Category ID
		$loc_cat['id'] = absint( $row[0] );

		// REQUIRED: Location Category name
		$loc_cat['name'] = esc_html( $row[2] );

		// Put the lcoation category into our list of location categories
		array_push( $loc_cats, $loc_cat );
	}

	$merged = array_merge( $header, array( 'entity' => $loc_cats ) );

	// Output the JSON
	echo json_encode( $merged );

	// Reset the Query
	wp_reset_postdata();
}