<?php
/**
 * Template Name: Query
 *
 * v2 of the Maker Faire API
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller for grabbing the appropriate API version and files.
 *
 * @version 2.0
 */

// require_once 'plugins/public-pages/locations.php';

// Define the API version.
define( 'MF_API_VERSION', 'v2' );

// Set the post per page for our queries
define( 'MF_POSTS_PER_PAGE', 2000 );

// Set the API keys to run this API.
define( 'MF_API_KEY', sanitize_text_field( get_option( 'make_app_api_key' ) ) );

// Set the Eventbase API version
define( 'MF_EVENTBASE_API_VERSION', '2.06' );


/**
 * SECURITY CHECKS
 */

$allowed_types = array(
	'category',
	'entity',
	'location_category',
	'maker',
	'schedule',
	'venue',
	'sponsor',
	'maker_export'
);

// Check that all required fields are passed before running anything and assign them to variables
$key = ( ! empty( $_REQUEST['key'] ) ? sanitize_text_field( $_REQUEST['key'] ) : null );
$type = ( ! empty( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : null );
$faire = ( ! empty( $_REQUEST['faire'] ) ? sanitize_text_field( $_REQUEST['faire'] ) : null );

// Check that our keys passed are in our $keys array and that a type and faire are passed
if ( empty( $key ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	return;
} elseif ( $key !== MF_API_KEY ) {
	header( 'HTTP/1.0 403 Forbidden' );

	echo '<h2>Invalid: Parameter Not Valid - "' . esc_html( $_REQUEST['key'] ) . '"</h2>';
	return;
} elseif ( empty( $type ) ) {
	header( 'HTTP/1.0 403 Forbidden' );

	echo '<h2>Invalid: Type</h2>';
	return;
} elseif ( ! in_array( $type, $allowed_types ) ) {
	header( 'HTTP/1.0 403 Forbidden' );

	echo '<h2>Invalid: Parameter Not Valid - "' . esc_html( $_REQUEST['type'] ) . '"</h2>';
	return;
} elseif ( empty( $faire ) ) {
	header( 'HTTP/1.0 403 Forbidden' );

	echo '<h2>Invalid: Faire</h2>';
	return;
}


/**
 * RUN THE CONTROLLER
 * Process the passed query string and fetch the appropriate API section
 */

// Get the appropriate API file.
$api_path = __DIR__ . '/api/' . sanitize_title( MF_API_VERSION ) . '/' . sanitize_title( $type ) . '/index.php';

// Prevent Path Traversal
if ( strpos( $api_path, '../' ) !== false || strpos( $api_path, "..\\" ) !== false || strpos( $api_path, '/..' ) !== false || strpos( $api_path, '\..' ) !== false )
	return;

// Make sure the api file exists...
if ( ! file_exists( $api_path ) )
	return;

// Set the JSON header
header( 'Content-type: application/json' );

// Load the file and process everything
include_once( $api_path );
