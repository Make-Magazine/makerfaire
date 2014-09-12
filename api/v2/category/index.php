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

//set allowed categories
	$allowed_categories = array(134289,7334,21774,139916,18082,2806,1098,183852052,1234386,74592,129975,10585,14877,181078,1169,1342,4843,292,1445,173,1334084,6877,3866349,79279,1211,91749252,313691,14340,133183,31827,49695267,13426,19557,213030,1256,524354,1438891,367966,25393,1274,144196889,1981,94030,3233,1212,3221,32656,70890761,197443,54,21464,139654,159558,50036,174,137903,78436,44926,400,24600,337,18,5382,1559,30314,41611515,586,238263556,349,5936,4936,5598517,26202,1336,15939,65518,1302369,767441,70627,45212,248375,120,8540250,3169,67778846,62455,6120,209229,2389,98163290);

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