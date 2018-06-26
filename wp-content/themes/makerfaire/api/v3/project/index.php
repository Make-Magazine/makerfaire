<?php

error_reporting('NONE');

/**
 * v3 of the Maker Faire API - ENTITY
 *
 * Built specifically for the mobile app but we have interest in building it further
 * This page is the controller to grabbing the appropriate API version and files.
 *
 * This page specifically handles the Entity type for the mobile app. AKA the applications.
 *
 * Variables accepted:
 *  type    Required  Can only be 'project'
 *  faire   Optional  Request information for a specific faire.  If empty, all faire data is returned
 *  dest    Optional  Who is requesting the data
 *                    Valid options - makershare
 *  lchange Optional  If supplied, must be in mmddyyyy format.  Will return all data on and after this date.
 *
 * @version 3.2
 */

// Stop any direct calls to this file
defined('ABSPATH') or die('This file cannot be called directly!');

//variables passed in call
$faire    = filter_input(INPUT_GET, 'faire', FILTER_SANITIZE_STRING);
$dest     = filter_input(INPUT_GET, 'dest', FILTER_SANITIZE_STRING);
$lchange  = filter_input(INPUT_GET, 'lchange', FILTER_SANITIZE_STRING);

$statusIn = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
$status   = ($statusIn!=''?"'" . implode("','",explode(",",$statusIn)) . "'":"'Accepted'");

// Double check again we have requested this file
if ($type == 'project') {
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  //TBD update tables. ensure entity has all faire id's and
  //maker to entity has correct role info
  //include entries marked as Accepted and active (not trashed)
  $select_query = "
     SELECT  entity.lead_id,
            `entity`.`presentation_title`,
            `entity`.`project_photo`,
            `entity`.`category` as `Categories`,
            `entity`.`desc_short` as Description,
            `entity`.`form_type`,
            entity.status as entity_status,
            (select meta_value as value from wp_gf_entry_meta where meta_key = '105' and wp_gf_entry_meta.entry_id = entity.lead_id limit 1) as projType,
            entity.faire,
            (select faire_name from wp_mf_faire where wp_mf_faire.faire=entity.faire) as faire_name,
             wp_gf_entry.date_created,
            `entity`.`project_video`,
            `entity`.`inspiration`,
             entity.mobile_app_discover,
            (SELECT sum(numRibbons)FROM `wp_mf_ribbons` where ribbonType = 1 and entry_id=entity.lead_id group by entry_id) as redRibbonCnt,
            (SELECT sum(numRibbons)FROM `wp_mf_ribbons` where ribbonType = 0 and entry_id=entity.lead_id group by entry_id) as blueRibbonCnt,
             wp_gf_entry.form_id,
             wp_gf_entry.status

    FROM  `wp_mf_entity` entity
    JOIN  wp_gf_entry on wp_gf_entry.id = entity.lead_id
    WHERE wp_gf_entry.status = 'active' "
    .($status  != "'all'" ? " AND entity.status in($status)":'')
    .($faire   != '' ? " AND LOWER(entity.faire)='".$faire."' " : '')
    .($lchange != '' ? " AND entity.last_change_date >= STR_TO_DATE('".$lchange." 235959', '%m%d%Y %H%i%s')" : '');

  $mysqli->query("SET NAMES 'utf8'");

  $result = $mysqli->query($select_query) or trigger_error($mysqli->error . "[$select_query]");

  // Initalize the app container
  $apps = array();
  $count = 0;

  // Loop through the projects
  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $app = array();
    // check the project type (field 105 - Who would you like listed as the maker of the project?).
    $isGroup    = false; //default to false
    if(isset($row['projType'])  && $row['projType']!= ''){
      $isGroup    = (strpos($row['projType'], 'group')  !== false ? true:false);
    }

    // if the project is marked as 'A group or association', do not pass this to makershare
    if($dest=='makershare' && $isGroup) {
      continue;
    }

    // Store the app information
    // REQUIRED: Application ID
    $app['id'] = absint($row['lead_id']);

    // REQUIRED: Application name
    $app['name'] = html_entity_decode($row['presentation_title'], ENT_COMPAT, 'utf-8');

    // Application Thumbnail and Large Images
    $app_image = $row['project_photo'];

    //find out if there is an override image for this page
    $overrideImg = findOverride($row['lead_id'], 'app');
    if ($overrideImg != '')
      $app_image = $overrideImg;

    $app['thumb_img_url']   = esc_url(legacy_get_fit_remote_image_url($app_image, '80', '80'));
    $app['large_image_url'] = esc_url(legacy_get_fit_remote_image_url($app_image, '800', '800'));
    $app['large_img_url']   = esc_url($app_image);

    // Application Categories
    $category_ids = $row['Categories'];
    $app['category_id_refs'] = explode(',', $category_ids);

    //run sub query to pull the contact and makers for this project along with their roles.
    //exclude makers where age_range is blank
    $subQuery = "select wp_mf_maker_to_entity.maker_id, maker_type, maker_role, wp_mf_maker.age_range "
              . "from wp_mf_maker_to_entity "
              . "left outer join wp_mf_maker on wp_mf_maker_to_entity.maker_id = wp_mf_maker.maker_id "
              . "where entity_id = ".$app['id']." and wp_mf_maker.age_range !='' ";
    $subResult = $mysqli->query($subQuery) or trigger_error($mysqli->error . "[$subQuery]");
    $makersArr = array();
    while ($subRow = $subResult->fetch_array(MYSQLI_ASSOC)) {
      if($subRow['age_range'] != '0-6' && $subRow['age_range'] != '7-12'){
        $makersArr[] = array('maker_id'=>$subRow['maker_id'],'role'=>$subRow['maker_role']);
      }
    }
    //if this project has no makers that are over 13, skip this project
    if(empty($makersArr)){
      continue;
    }
    $app['exhibit_accounts'] = $makersArr;

    // Application Description
    $app['description'] = $row['Description'];

    //add makershare data
    if($dest=='makershare'){
      //only return exhibit, presentation and performance
      if($row['form_type'] == 'Exhibit' || $row['form_type'] == 'Presentation' || $row['form_type'] == 'Performance') {
        $app['form_type']     = $row['form_type'];
        $app['faire_name']    = $row['faire_name'];
        $app['faire']         = $row['faire'];
        $app['submission']    = $row['date_created'];
        $app['project_video'] = $row['project_video'];
        $app['inspiration']   = $row['inspiration'];
        //TBD determine how to send these based on ribbon last change date
        $app['redRibbonCnt']  = ($row['redRibbonCnt']  != NULL ? $row['redRibbonCnt']  : 0);
        $app['blueRibbonCnt'] = ($row['blueRibbonCnt'] != NULL ? $row['blueRibbonCnt'] : 0);

        // Put the application into our list of apps
        $count++;
        array_push($apps, $app);
      }
    }else{
      // Put the application into our list of apps
      $count++;
      array_push($apps, $app);
    }
  }

  $header = array(
      'header' => array(
          'version' => '3.2',
          'results' => intval($count),
      ),
  );

  // Merge the header and the entities
  $merged = array_merge($header, array('project' => $apps));

  // Output the JSON
  echo json_encode($merged);

  // Reset the Query
  wp_reset_postdata();
}