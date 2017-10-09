<?php
/**
 * v3 of the Maker Faire API - MAKER
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller to grabbing the appropriate API version and files.
 *
 * This page specifically handles the Maker data.
 *
 * Variables accepted:
 *  type    Required  Can only be 'account'
 *  faire   Optional  Request information for a specific faire.  If empty, all faire data is returned
 *  dest    Optional  Who is requesting the data
 *                    Valid options - makershare
 *  lchange Optional  If supplied, must be in mmddyyyy format.  Will return all data on and after this date.
 *
 * @version 3.2
 */

// Stop any direct calls to this file
defined( 'ABSPATH' ) or die( 'This file cannot be called directly!' );

global $wp_query;

$faire    = filter_input(INPUT_GET, 'faire', FILTER_SANITIZE_STRING);
$dest     = filter_input(INPUT_GET, 'dest', FILTER_SANITIZE_STRING);
$lchange  = filter_input(INPUT_GET, 'lchange', FILTER_SANITIZE_STRING);

$statusIn = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
$status   = ($statusIn!=''?"'" . implode("','",explode(",",$statusIn)) . "'":"'Accepted'");

// Double check again we have requested this file
if ( $type == 'account' ) {
	$mysqli = new mysqli(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
	if ($mysqli->connect_errno) {
		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}

  //if dest = makershare, age_range must be set
  //if lchange is set, only return makers changed on or after given date
  $where = array();
  if($dest == 'makershare') $where[] = " age_range != ''";
  if($lchange != '')        $where[] = " wp_mf_maker.last_change_date >= STR_TO_DATE('".$lchange." 235959', '%m%d%Y %H%i%s')";


  if($faire == ''){
      $select_query = "select maker_id, `First Name` as first_name, `Last Name` as last_name,
                              Bio, Email, Photo, TWITTER, website, age_range,
                              city, state, country, zipcode, last_change_date,
                              (select faire
                                from  wp_mf_entity, wp_mf_maker_to_entity
                                where lead_id = wp_mf_maker_to_entity.entity_id
                                AND   wp_mf_maker_to_entity.maker_id=wp_mf_maker.maker_id) as faire
                      FROM  wp_mf_maker "
                    .(!empty($where)?' where '. implode(' AND ',$where):'')
                    ." order by maker_id ASC";
  }else{
    //if faire is set, only pull makers associated with that faire
    //Pull Accepted records and exclude contacts as they do not have an age range set for makershare
    $select_query = "SELECT * FROM
        (SELECT wp_mf_maker.maker_id, `First Name` as first_name, `Last Name` as last_name,
                Bio, Email, Photo, TWITTER, website, age_range,
                city, state, country, zipcode, wp_mf_maker.last_change_date, wp_mf_entity.status
         FROM   `wp_mf_maker`, wp_mf_maker_to_entity, wp_mf_entity
         WHERE  wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id
         AND    wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id
         AND    LOWER(wp_mf_entity.faire) = '".strtolower($faire)."'"
         .($status  != "'all'" ? " AND wp_mf_entity.status in($status)":'')
         . " AND    wp_mf_maker_to_entity.maker_type != 'contact'"
         .(!empty($where)?' AND '.implode(' AND ',$where):'')
    ." ORDER BY `wp_mf_maker`.`maker_id` ASC, wp_mf_maker_to_entity.maker_type ASC)
    AS tmp_table GROUP by `maker_id`";
  }

	$mysqli->query("SET NAMES 'utf8'");
	$result = $mysqli->query ( $select_query );

	// Init the entities header
	$makers = array();
  $count=0;

	// Loop through the posts
	while ( $row = $result->fetch_array(MYSQLI_ASSOC)  ) {
		// REQUIRED: The maker ID
		$maker['id'] = $row['maker_id'];

		// REQUIRED: The maker name
		$maker['first_name']  = $row['first_name'];
		$maker['last_name']   = $row['last_name'];
		$maker['description'] = $row['Bio'];
		$maker['email']       = $row['Email'];
		$maker['image']       = $row['Photo'];
		$maker['twitter']     = $row['TWITTER'];
    $maker['website']     = $row['website'];


    //logic specific for makershare
    if($dest=='makershare'){
      //don't return makers under 13 or group makers
      if($row['age_range'] != '0-6' && $row['age_range'] != '7-12'){
        $maker['location'] = array( 'city'    => $row['city'],
                                    'state'   => $row['state'],
                                    'zipcode' => $row['zipcode'],
                                    'country' => $row['country']);
        $count++;
        array_push( $makers, $maker );
      }

    } else {
      // Put the maker into our list of makers
      $count++;
      array_push( $makers, $maker );
    }

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
