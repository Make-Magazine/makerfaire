<?php
/**
 * v2 of the Maker Faire API - MAKER
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller to grabbing the appropriate API version and files.
 *
 * This page specifically handles the Maker data.
 *
 * @version 2.0
 */

// Stop any direct calls to this file
defined( 'ABSPATH' ) or die( 'This file cannot be called directly!' );
global $wp_query;
$type = ( ! empty( $wp_query->query_vars['type'] ) ? sanitize_text_field( $wp_query->query_vars['type'] ) : null );
$faire = ( ! empty( $_REQUEST['faire'] ) ? sanitize_text_field( $_REQUEST['faire'] ) : null );

// Double check again we have requested this file
if ( $type == 'account' ) {


	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$select_query = sprintf("select * from
    (SELECT wp_mf_entity.lead_id, wp_mf_maker_to_entity.maker_type, `wp_mf_maker`.`First Name` as first_name, `wp_mf_maker`.`Last Name` as last_name,
           `wp_mf_maker`.`Bio`, `wp_mf_maker`.`Photo`, `wp_mf_maker`.`Email`, `wp_mf_maker`.`TWITTER`, `wp_rg_lead`.`form_id`, `wp_mf_maker`.`maker_id`, wp_mf_entity.category
      FROM `wp_mf_maker`, wp_mf_maker_to_entity, wp_mf_entity, wp_mf_faire,wp_rg_lead
      where wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id
      and   wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id
      AND   wp_mf_entity.status = 'Accepted'
      AND   wp_mf_maker_to_entity.maker_type != 'contact'
      and   LOWER(wp_mf_faire.faire) = '".$faire."'
      AND   FIND_IN_SET (`wp_rg_lead`.`form_id`,wp_mf_faire.form_ids)> 0
      and   wp_rg_lead.id = `wp_mf_maker_to_entity`.`entity_id`
      and   wp_rg_lead.status = 'active'
      ORDER BY `wp_mf_maker`.`maker_id` ASC, wp_mf_maker_to_entity.maker_type ASC)
    AS tmp_table GROUP by `maker_id`
  ");
	$mysqli->query("SET NAMES 'utf8'");
	$result = $mysqli->query ( $select_query );

	// Define the API header (specific for Eventbase)
	$header = array(
		'header' => array(
			'version' => esc_html( MF_EVENTBASE_API_VERSION ),
			'results' => intval( $result->num_rows ),
		),
	);

	// Init the entities header
	$makers = array();
  $count=0;
	// Loop through the posts
	while ( $row = $result->fetch_array(MYSQLI_ASSOC)  ) {
    $count++;
		//Check for null makers
		if (!isset($row['lead_id'])) continue;

		// REQUIRED: The maker ID
		$maker['id'] = $row['maker_id'];

		// REQUIRED: The maker name
		$maker['first_name']=$row['first_name'];
		$maker['last_name']=$row['last_name'];
		$maker['description']=$row['Bio'];
		$maker['email']=$row['Email'];
		$maker['image']=$row['Photo'];
		$maker['twitter']=$row['TWITTER'];

		$maker['name'] = $row['first_name'].' '.$row['last_name'];
		$maker['child_id_refs'] = array(); //array_unique( get_post_meta( absint( $post->ID ), 'mfei_record' ) );
		$maker['category_id_refs'] = explode(',', $row['category']); //array_unique( get_post_meta( absint( $post->ID ), 'mfei_record' ) );
		//add the sponsor category 333 if using a sponsor form

    //look for the word sponsor in the form name
    $form = GFAPI::get_form( $row['form_id'] );
    $formTitle = $form['title'];
    $formType = $form['form_type'];

    
		// No longer have these
		// Maker Thumbnail and Large Images
		//$maker_image = isset($entry['217']) ? $entry['217']  : null;
		//$maker['thumb_img_url'] = esc_url( legacy_get_resized_remote_image_url( $maker_image, '80', '80' ) );
		//$maker['large_image_url'] = esc_url( legacy_get_resized_remote_image_url( $maker_image, '600', '600' ) );;

		// Maker bio information
		//$maker['description'] =isset($entry['234']) ? $entry['234']  : null;

		// Maker Video link
		//$maker_video = isset($entry['32']) ? $entry['32']  : null;
		//$maker['youtube_url'] = ( ! empty( $maker_video ) ) ? esc_url( $maker_video ) : null;

		// Maker Website link
		//$maker_website = isset($entry['27']) ? $entry['27']  : null;
		//$maker['website_url'] = ( ! empty( $maker_website ) ) ? esc_url( $maker_website ) : null;

		// Put the maker into our list of makers
      array_push( $makers, $maker );  
   
  
	}
  // Define the API header (specific for Eventbase)
  $header = array(
      'header' => array(
          'version' => '3.0',
          'results' => intval($count),
      ),
  );
	// Merge the header and the entities
	$merged = array_merge( $header, array( 'account' => $makers ) );
	$json_results = json_encode( $merged );
	// Output the JSON
	echo $json_results;

	// Reset the Queryv
	wp_reset_postdata();
}