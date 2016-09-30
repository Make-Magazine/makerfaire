<?php

/**
 * v2 of the Maker Faire API - VENUE
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller to grabbing the appropriate API version and files.
 *
 * This page specifically handles the Venue data.
 *
 * @version 2.0
 *
 * Read from location_elements
 */
// Stop any direct calls to this file
defined('ABSPATH') or die('This file cannot be called directly!');
$type = (!empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : null );
$faire = (!empty($_REQUEST['faire']) ? sanitize_text_field($_REQUEST['faire']) : null );

// Double check again we have requested this file
if ($type == 'venue') {

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

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $select_query = sprintf("select
		b.id as ID,
		b.area as description,
		group_concat(a.`id`  separator ',') as 'child_id_refs',
		c.faire as faire,
    null as latitude,
    null as longitude

                from wp_mf_faire_subarea a

                join wp_mf_faire_area b on a.area_id=b.id
		join wp_mf_faire c on b.faire_id=c.ID

                where c.faire= '$faire'

		group by b.id
union all
select c.id
  ,concat(d.area,' ',c.subarea)
	,(select group_concat( distinct CONCAT(a.subarea_id,b.entry_id) separator ',')) as child_id_refs
	,e.faire as faire
    ,null as latitude
	,null as longitude
from  wp_mf_location a
  left join `wp_mf_onsitecheckin` b on a.entry_id=b.entry_id
  join `wp_mf_faire_subarea` c on a.subarea_id=c.id
  join wp_mf_faire_area d on c.area_id=d.id
  join wp_mf_faire e on d.faire_id=e.ID
  where faire = '$faire'
  group by a.subarea_id
union all
select CONCAT(a.subarea_id,'',b.entry_id) as ID
	,CONCAT (mfe.desc_short,' (',IFNULL(NULLIF(c.nicename,''),c.subarea),')')
	,null as child_id_refs
	,e.faire as faire
	,b.latitude as latitude
	,b.longitude as longitude
from  wp_mf_location a
 join `wp_mf_onsitecheckin` b on a.entry_id=b.entry_id
 join `wp_mf_faire_subarea` c on a.subarea_id=c.id
 join wp_mf_faire_area d on c.area_id=d.id
 join wp_mf_faire e on d.faire_id=e.ID
 join wp_mf_entity mfe on a.entry_id=mfe.lead_id
 where e.faire = '$faire'
             ");
  $mysqli->query("SET NAMES 'utf8'");
  $result = $mysqli->query($select_query);

  $header = array(
      'header' => array(
          'version' => esc_html(MF_EVENTBASE_API_VERSION),
          'results' => intval($result->num_rows),
      ),
  );

  // Loop through the posts
  while ($row = $result->fetch_row()) {

    // Open the array.
    $venue = array();

    // REQUIRED: The venue ID
    $venue['id'] = absint($row[0]);

    // REQUIRED: The venue name
    $venue['name'] = html_entity_decode($row[1], ENT_COMPAT, 'utf-8');

    // Get the child locations
    $venue['child_id_refs'] = explode(',', $row[2]);


    // Get the description, if it exists.
    $venue['description'] = html_entity_decode(trim(Markdown($row[1])), ENT_COMPAT, 'utf-8');

    // Do we have a subtitle?
    //$venue['subtitle'] = ( $post->post_parent != 0 ) ? get_the_title( $post->post_parent ) : '';
    // Do we have lat/long?
    //$meta = get_post_meta( $post->ID );
    // Attach the lat/long to the data feed
    $venue['latitude']	= ( isset( $row[4] ) ) ? floatval( $row[4] ) : '';
    $venue['longitude']	= ( isset( $row[5] ) ) ? floatval( $row[5] ) : '';
    // They apparently changed the spec.
    //$venue['gps_lat']	= $venue['latitude'];
    //$venue['gps_long']	= $venue['longitude'];
    // Let's add the venue categories
    //$venue['category_id_refs'] = explode(',',$row[7]);
    // Put the maker into our list of makers
    array_push($venues, $venue);
  }

  // Merge the header and the entities
  $merged = array_merge($header, array('entity' => $venues));

  // Output the JSON
  echo json_encode($merged);

  // Reset the Query
  wp_reset_postdata();
}
