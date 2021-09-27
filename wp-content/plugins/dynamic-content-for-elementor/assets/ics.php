<?php
/** Loads the WordPress Environment and Template */
define( 'WP_USE_THEMES', false );
require '../../../../wp-blog-header.php';

use DynamicContentForElementor\Helper;

status_header( 200 );

// Escapes a string of characters
function escape_string( $string ) {
	return preg_replace( '/([\,;])/', '\\\$1', $string );
}

// Cut it
function shorter_version( $string, $lenght ) {
	if ( strlen( $string ) >= $lenght ) {
		return substr( $string, 0, $lenght );
	} else {
		return $string;
	}
}

$element_id = sanitize_text_field( $_GET['element_id'] ) ?? false;
$ics_post_id = intval( $_GET['post_id'] ) ?? false;
$ics_queried_id = intval( $_GET['queried_id'] ) ?? false;

if ( $element_id === false || $ics_post_id === false ) {
	wp_die( 'Element and post ids not provided' );
}

if ( $element_id && $ics_post_id ) {
	// $queried_id the post for dynamic values data.
	if ( $ics_queried_id ) {
		$queried_id = $ics_queried_id;
	} else {
		$queried_id = $ics_post_id;
	}

	// Make the post as global post for dynamic values.
	$elementor = Elementor\Plugin::instance();
	$elementor->db->switch_to_post( $queried_id );
	// The following lines likely wouldn't be need if this were an ajax.
	// Elementor switch_to_post does not change the queried id. And this is
	// enough for ajax calls, because in an ajax the queried id is 0 and thus
	// ignored. But loading the file directly as php like we do here, can make
	// the queried non-zero, so we have to handle it.
	global $wp_query;
	$queried_post = get_post( $queried_id );
	$wp_query->queried_object = $queried_post;
	$wp_query->queried_object_id = $queried_id;
	$document = $elementor->documents->get( $ics_post_id );
	if ( $document ) {
		$form = Helper::find_element_recursive( $document->get_elements_data(), $element_id );
	}
	if ( empty( $form ) ) {
		wp_die( 'ICS Error' );
	}
	// restore default values
	$widget = $elementor->elements_manager->create_element_instance( $form );

	$created_date = get_post_time( 'Ymd\THis\Z', true, $queried_id );
	// create an instance of widget to get its dynamic data
	$settings = $widget->get_settings_for_display();

	$title = escape_string( ! empty( $settings['dce_calendar_title'] ) ? $settings['dce_calendar_title'] : get_the_title( $queried_id ) );
	$description = escape_string( ! empty( $settings['dce_calendar_description'] ) ? strip_tags( nl2br( $settings['dce_calendar_description'] ) ) : '' );
	$location = escape_string( ! empty( $settings['dce_calendar_location'] ) ? $settings['dce_calendar_location'] : '' );
	$organiser = get_bloginfo( 'name' );

	// FORMAT
	$date_format = $settings['dce_calendar_datetime_string_format'];
	if ( empty( $date_format ) ) {
		$date_format = 'Y-m-d H:i';
	}
	// START
	$start = ( 'string' !== $settings['dce_calendar_datetime_format'] ) ? $settings['dce_calendar_datetime_start'] : $settings['dce_calendar_datetime_start_string'];
	if ( empty( $start ) ) {
		$start = new \DateTime();
	} else {
		$start = \DateTime::createFromFormat( $date_format, $start );
	}
	if ( $start ) {
		$start_field = get_gmt_from_date( $start->format( 'Y-m-d H:i' ), 'Ymd\\THi00\\Z' );
	}
	// END
	$end = ( 'string' !== $settings['dce_calendar_datetime_format'] ) ? $settings['dce_calendar_datetime_end'] : $settings['dce_calendar_datetime_end_string'];
	if ( empty( $end ) && $start ) {
		$end = new \DateTime( $start->format( 'Y-m-d H:i' ) );
		$end = $end->modify( '+ 1 day' );
	} elseif ( empty( $end ) && ! $start ) {
		$end = new \DateTime();
	} else {
		$end = \DateTime::createFromFormat( $date_format, $end );
	}
	if ( $end ) {
		$end_field = get_gmt_from_date( $end->format( 'Y-m-d H:i' ), 'Ymd\\THi00\\Z' );
	}

	$filename = urlencode( $title . '.ics' );

	ob_start();

	// Set the correct headers for this file
	header( 'Content-Type: text/calendar; charset=utf-8' );
	header( 'Content-Transfer-Encoding: Binary' );
	header( 'Content-Description: File Transfer' );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate' );
	header( 'Pragma: public' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	define( 'ICS_EOL', "\r\n" );
	?>BEGIN:VCALENDAR<?php echo ICS_EOL; ?>
VERSION:2.0<?php echo ICS_EOL; ?>
PRODID:-//Dynamic.ooo //NONSGML DCE Calendar //EN<?php echo ICS_EOL; ?>
CALSCALE:GREGORIAN<?php echo ICS_EOL; ?>
METHOD:PUBLISH<?php echo ICS_EOL; ?>
X-WR-TIMEZONE:<?php echo get_option( 'timezone_string' );
echo ICS_EOL; ?>
BEGIN:VEVENT<?php echo ICS_EOL; ?>
ORGANIZER:<?php echo escape_string( $organiser );
echo ICS_EOL; ?>
CREATED:<?php echo $created_date;
echo ICS_EOL; ?>
URL;VALUE=URI:<?php echo get_permalink( $queried_id );
echo ICS_EOL; ?>
DTSTART;VALUE=DATE:<?php echo $start_field;
echo ICS_EOL; ?>
DTEND;VALUE=DATE:<?php echo $end_field;
echo ICS_EOL; ?>
DTSTAMP:<?php echo date_i18n( 'Ymd\THis\Z', time(), true );
echo ICS_EOL; ?>
SUMMARY:<?php echo $title;
echo ICS_EOL; ?>
DESCRIPTION:<?php echo $description;
echo ICS_EOL; ?>
LOCATION:<?php echo $location;
echo ICS_EOL; ?>
TRANSP:OPAQUE<?php echo ICS_EOL; ?>
UID:<?php echo md5( $settings['dce_calendar_title'] . '-' . $element_id . '-' . $queried_id );
echo ICS_EOL; ?>
BEGIN:VALARM<?php echo ICS_EOL; ?>
ACTION:DISPLAY<?php echo ICS_EOL; ?>
TRIGGER;VALUE=DATE-TIME:<?php echo $start_field;
echo ICS_EOL; ?>
DESCRIPTION:<?php echo $title;
echo ICS_EOL; ?>
END:VALARM<?php echo ICS_EOL; ?>
END:VEVENT<?php echo ICS_EOL; ?>
END:VCALENDAR<?php
	//Collect output and echo
	$eventsical = ob_get_contents();
	ob_end_clean();
	echo $eventsical;
	exit();
} else {
	echo 'ERROR';
}
