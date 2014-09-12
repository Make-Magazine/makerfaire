<?php
/**
 * v2 of the Maker Faire API - CATEGORY
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller to grabbing the appropriate API version and files.
 *
 * This page specifically handles the Category data.
 *
 * @version 2.0
 */

// Stop any direct calls to this file
defined( 'ABSPATH' ) or die( 'This file cannot be called directly!' );

//Make a silly call that gets around the need to hit the database with a tremdously complicated join and just puts most of the operation on cpu
$all_events = file_get_contents("http://makerfaire.com/query/?key=d184iC3I5cw4eeC&type=entity&faire=".sanitize_title($faire));
$all_events = json_decode($all_events,1);
$allowed_categories = array();
foreach ($all_events['entity'] as $event) {
  foreach ($event['category_id_refs'] as $category_id) {
    if(!in_array($category_id, $allowed_categories)){
     array_push($allowed_categories, $category_id);
    }
  }
}

$taxonomies = array(
	'category',
	'post_tag',
	'group',
);

// Double check again we have requested this file
if ( $type == 'category') {
	// Fetch the categories and tags as one
	$terms = get_terms( $taxonomies, array(
		'hide_empty' => 0,
	) );

	// Define the API header (specific for Eventbase)
	$header = array(
		'header' => array(
			'version' => esc_html( MF_EVENTBASE_API_VERSION ),
			'results' => count( $terms ),
		),
	);

	// Initalize the app container
	$venues = array();

	// Loop through the terms
	foreach ( $terms as $term ) {
		// REQUIRED: Category ID
		$venue['id'] = absint( $term->term_id );

		// REQUIRED: Category Name
		$venue['name'] = html_entity_decode( esc_js( $term->name ) );

		// Put the application into our list of apps if in allowed array
		if(in_array($venue['id'], $allowed_categories)){
		array_push( $venues, $venue );
		}
	}

	$merged = array_merge( $header, array( 'entity' => $venues, ) );

	// Output the JSON
	echo json_encode( $merged );

}