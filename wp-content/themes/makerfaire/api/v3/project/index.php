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
 * @version 3.0
 */
// Stop any direct calls to this file
defined('ABSPATH') or die('This file cannot be called directly!');
$type = ( ! empty( $wp_query->query_vars['type'] ) ? sanitize_text_field( $wp_query->query_vars['type'] ) : null );
$faire = (!empty($_REQUEST['faire']) ? sanitize_text_field($_REQUEST['faire']) : null );
$dest  = (!empty($_REQUEST['dest'])  ? sanitize_text_field($_REQUEST['dest'])  : null );


// Double check again we have requested this file
if ($type == 'project') {

  // Set the query args.
  /*
   * $args = array(
    'no_found_rows'	 => true,
    'post_type'		 => 'mf_form',
    'post_status'	 => 'accepted',
    'posts_per_page' => absint( MF_POSTS_PER_PAGE ),
    'faire'			 => sanitize_title( $faire ),
    );
    $query = new WP_Query( $args );
   */



  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  $select_query = sprintf("
     SELECT  entity.lead_id,
            `entity`.`presentation_title`,
            `entity`.`desc_short` as Description,
            `entity`.`category` as `Categories`,
            `entity`.`project_photo`,
            `entity`.`form_type`,
            `entity`.`project_video`,
            entity.mobile_app_discover,
            wp_mf_faire.faire_name,
            (select group_concat( distinct maker_id separator ',') as Makers
             from wp_mf_maker_to_entity maker_to_entity
             where entity.lead_id               = maker_to_entity.entity_id AND
                   maker_to_entity.maker_type  != 'Contact'
             group by maker_to_entity.entity_id
            ) as exhibit_makers,
            (select group_concat( distinct maker_id separator ',') as Makers
             from wp_mf_maker_to_entity maker_to_entity
             where entity.lead_id               = maker_to_entity.entity_id AND
                   maker_to_entity.maker_type  = 'Contact'
             group by maker_to_entity.entity_id
            ) as lead_maker,
            (SELECT sum(numRibbons)FROM `wp_mf_ribbons` where ribbonType = 1 and entry_id=entity.lead_id group by entry_id) as redRibbonCnt,
            (SELECT sum(numRibbons)FROM `wp_mf_ribbons` where ribbonType = 0 and entry_id=entity.lead_id group by entry_id) as blueRibbonCnt,
            wp_rg_lead.form_id,
            wp_rg_lead.status,
            wp_rg_lead.date_created,
            (select wp_mf_location.subarea_id
             from   wp_mf_location
             where  wp_mf_location.entry_id = entity.lead_id limit 1
            ) as venue_id,
            IF(wp_mf_onsitecheckin.entry_id IS NOT NULL,
             (select CONCAT(z.subarea_id,z.entry_id)
             from   wp_mf_location z
             where  z.entry_id = entity.lead_id limit 1),null)
            as pin_venue

    FROM    `wp_mf_entity` entity
    JOIN wp_rg_lead on wp_rg_lead.id = entity.lead_id
    JOIN wp_mf_faire on wp_mf_faire.faire  ='$faire'
    LEFT JOIN wp_mf_onsitecheckin ON wp_rg_lead.id = wp_mf_onsitecheckin.entry_id
    WHERE   entity.status = 'Accepted'
    AND 	LOWER(entity.faire)='$faire'
    AND   FIND_IN_SET (`wp_rg_lead`.`form_id`,wp_mf_faire.non_public_forms)<= 0
    and 	wp_rg_lead.status = 'active'
    ");

  $mysqli->query("SET NAMES 'utf8'");

  $result = $mysqli->query($select_query) or trigger_error($mysqli->error . "[$select_query]");

  // Initalize the app container
  $apps = array();
  $count = 0;
  // Loop through the posts
  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $count++;
    // Store the app information
    //$app_data = json_decode( mf_clean_content( $post->post_content ) );
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

    $app['thumb_img_url'] = esc_url(legacy_get_fit_remote_image_url($app_image, '80', '80'));
    $app['large_image_url'] = esc_url($app_image);
    // Should actually be this... Adding it in for the future.
    $app['large_img_url'] = esc_url($app_image);

    // Application Categories
    $category_ids = $row['Categories'];
    $app['category_id_refs'] = explode(',', $category_ids);

    $maker_ids = $row['exhibit_makers'];
    $app['exhibit_accounts'] = (!empty($maker_ids) ) ? explode(',', $maker_ids) : null;

    $lead_maker_ids = $row['lead_maker'];
    $app['lead_accounts'] = (!empty($lead_maker_ids) ) ? explode(',', $lead_maker_ids) : null;

    // Application Description
    $app['description'] = $row['Description'];

    //add makershare data
    if($dest=='makershare'){
      //only return exhibit, presentation and performance
      if($row['form_type'] == 'Exhibit' || $row['form_type'] == 'Presentation' || $row['form_type'] == 'Performance') {
        $app['form_type']     = $row['form_type'];
        $app['faire_name']    = $row['faire_name'];
        $app['submission']    = $row['date_created'];
        $app['project_video'] = $row['project_video'];
        $app['redRibbonCnt']  = ($row['redRibbonCnt']  != NULL ? $row['redRibbonCnt']  : 0);
        $app['blueRibbonCnt'] = ($row['blueRibbonCnt'] != NULL ? $row['blueRibbonCnt'] : 0);

        // Put the application into our list of apps
        array_push($apps, $app);
      }
    }else{
      // Put the application into our list of apps
      array_push($apps, $app);
    }
  }

  $header = array(
      'header' => array(
          'version' => '3.0',
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