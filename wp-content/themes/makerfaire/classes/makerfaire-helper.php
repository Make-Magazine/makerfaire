<?php

// BEGINING AMAZING HACKS
function maker_url_vars($rules) {
  $newrules = array();
  $newrules['maker/entry/(\d*)/?'] = 'index.php?post_type=page&pagename=entry-page-do-not-delete&e_id=$matches[1]';
  $newrules['([^\/]*)/meet-the-makers/topics/([^\/]*)/([0-9]{1,})/?$'] = 'index.php?post_type=page&pagename=listing-page-do-not-delete&f=$matches[1]&t_slug=$matches[2]&offset=$matches[3]';
  $newrules['([^\/]*)/meet-the-makers/search/([0-9]{1,})/?$'] = 'index.php?post_type=page&pagename=search-results-page-do-not-delete&f=$matches[1]&offset=$matches[2]';
  $newrules['([^\/]*)/meet-the-makers/topics/([^\/]*)/?$'] = 'index.php?offset=1&post_type=page&pagename=listing-page-do-not-delete&f=$matches[1]&t_slug=$matches[2]';
  $newrules['([^\/]*)/meet-the-makers/search/?$'] = 'index.php?offset=1&post_type=page&pagename=search-results-page-do-not-delete&f=$matches[1]';
  return $newrules + $rules;
}

add_filter('rewrite_rules_array', 'maker_url_vars');
/*
  add_action( 'wp_loaded','my_flush_rules' );

  // flush_rules() if our rules are not yet included
  function my_flush_rules(){
  $rules = get_option( 'rewrite_rules' );

  if ( ! isset( $rules['maker/entry/(\d*)/?'] ) || ! isset( $rules['([^/]*)/meet-the-makers/topics/([^/]*)/?'] )) {
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
  }

  } */

add_filter('query_vars', 'my_query_vars');

function my_query_vars($query_vars) {
  $query_vars[] = 'e_id';
  $query_vars[] = 't_slug';
  $query_vars[] = 's_keyword';
  $query_vars[] = 'offset';
  $query_vars[] = 'f';
  return $query_vars;
}

// END AMAZING HACKS

/**
 * The new and improved schedule by location shortcode.
 *
 * We wanted a better code base that is more maintainable and performant that also supports our new location architecture.
 * To use, pass in the location_id attribute in the shortcode with the locations ID along with what faire you want applications from. Passing the faire is necessary for future archiving.
 * From there we'll query all events for each day and cache them.
 * Now we'll loop through each day spitting out all applications scheduled for those days from 10am to closing of that day.
 *
 * @param  array $atts The attributes being passed through the shortcode
 * @return string
 */
function mf_display_schedule_by_area($atts) {
  global $wpdb;
  $sponsorArr = array();

  //loop thru ACF data to see if this stage is sponsored
  if(have_rows('stage_sponsor_rep')){
    while(have_rows('stage_sponsor_rep')){
      the_row();
      //get sponsor and subarea information
      $subarea_id   = get_sub_field('subarea_id');
      $sponsor_name  = get_sub_field('sponsor_name');
      $sponsor_image = get_sub_field('sponsor_image');

      //build array
      $sponsorArr[$subarea_id] = array('name'=>$sponsor_name,'image'=>$sponsor_image['url']);
    }
  }

  // Build list of locations. Only display those locations that have exhibits scheduled
  $faire = $atts['faire'];
  $scheduleArray = get_mf_schedule_by_faire($faire);

  //build array with the return data
  foreach ($scheduleArray as $data) {
    $subarea = ($data['nicename'] != '' || $data['nicename'] != NULL ? $data['nicename'] : $data['subarea']);
    $schedule[$subarea]['days'][$data['day']]['entries'][] = $data;
    $schedule[$subarea]['subarea_id'] = $data['subarea_id'];
  }

  $dropdownLi = '';
  $scheduleData = '';
  $count = 0;
  $activetab = '';
  foreach ($schedule as $subarea => $scheduleArea) {
    $subarea_id = $scheduleArea['subarea_id'];
    //add to the li drop down
    $href = strtolower(preg_replace("/[^A-Za-z0-9]/", "", $subarea));
    $active = ($count == 0 ? 'active' : '');
    $activetab = ($count == 0) ? $subarea : $activetab;
    $count++;
    $dropdownLi .= '<li class="' . $active . '"><a href="#' . $href . '" data-toggle="tab">' . $subarea . '</a></li>';
    //begin building the schedule Data for this area
    $scheduleData .= '<div class="tab-pane ' . $active . '" id="' . $href . '">';

    //each area contains a row with tabs for each date, print icon and a tabable div for each day
    $scheduleData .= '<div class="row padtop schedInfo">'
              . '<ul id="tabs" class="nav nav-tabs">||navtabs||</ul>'
              . '<div class="pull-right printIcon"">'
              . '    <a href="#" onclick="window.print();return false;"><img src="' . get_stylesheet_directory_uri() . '/images/print-ico.png" alt="Print this schedule" /></a>'
              . '</div>';

    //check if this stage is sponsored
    if(isset($sponsorArr[$subarea_id])){
      $scheduleData .=    '<div class="pull-right schedIcon sponsorSec"><span class="sponsorText">Presented By</span><img class="sponsorImg" src="'.$sponsorArr[$subarea_id]['image'].'" alt="'.$sponsorArr[$subarea_id]['name'].'" /></div>';
    }

    $scheduleData .= '</div>';

    $scheduleData .= ' <div class="tab-content">';
    $navTabs = '';
    foreach ($scheduleArea['days'] as $dayKey => $dayData) {
      $day = strtolower($dayKey);

      $dayhref = strtolower(preg_replace("/[^A-Za-z0-9]/", "", $subarea . $day));
      $navTabs .= '<li class="' . ($day == 'saturday' ? 'active' : '') . '"><a class="text-capitalize" href="#' . $dayhref . '" data-toggle="tab">' . esc_attr($day) . '</a></li>';
      // Start the day's schedule
      $scheduleData .= '<div id="' . $dayhref . '" class="tab-pane fade in ' . ($day == 'saturday' ? 'active' : '') . '">';
      $scheduleData .= '<table id="' . esc_attr($day) . '" class="table table-bordered table-schedule">';
      if (isset($dayData['entries'])) {
        foreach ($dayData['entries'] as $entry) {
          $scheduleData .= buildScheduleData($entry);
        }
      }
      $scheduleData .= '</table></div>';
    }
    $scheduleData = str_replace('||navtabs||', $navTabs, $scheduleData);
    $scheduleData .= '</div>'; //close .tab-content
    $scheduleData .= '</div>'; //close .tab-pane
  }


  $scheduleTab = '<div class="tabbable tabs-left" id="scheduleTab">
	<div class="tab-content">' . $scheduleData . '</div></div>';
  $dropdown_menu = '<ul class="dropdown-menu" style="border: thin solid #00bef3; width: 100%;  text-align: left;font-size:20px;"> ' .
          $dropdownLi . '</ul>';
  $output = '<div class="dropdown">
	<button class="btn btn-primary dropdown-toggle" style="border: thin solid #00bef3; width: 100%; background: none; color: #00bef3; text-align: left;font-size:20px;" type="button" data-toggle="dropdown">
	<span class="pickText pull-left" >' . $activetab . '</span>
	<span class="glyphicon glyphicon-chevron-down pull-right" ></span></button>' . $dropdown_menu . '</div>' . $scheduleTab;

  return $output;
}

function buildScheduleData($scheduleditem) {
  $event_id = $scheduleditem['id'];
  $output = '';
  $output .= '<tr>';
  $output .= '<td class="dateTime col-xs-2 col-sm-3 col-md-3 col-lg-3">';
  $output .= '<h4>' . esc_html($scheduleditem['day']) . '</h4>';
  $output .= '<p><span class="visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block">' . esc_html(date('g:i A', strtotime($scheduleditem['time_start']))) . ' &mdash; </span>' . esc_html(date('g:i A', strtotime($scheduleditem['time_end']))) . '</p>';

  if (isset($scheduleditem['large_img_url']) || isset($scheduleditem['thumb_img_url'])) {
    $output .= '<div class="pull-left">';
    // We may want to over ride the photo of an application on the schedule page by checking if there is a featured image on the event item
    if ($scheduleditem['thumb_img_url']) {
      $output .= '<a class="thumbnail" href="/maker/entry/' . $scheduleditem['id'] . '"><img src="' . legacy_get_resized_remote_image_url($scheduleditem['thumb_img_url'], 140, 140) . '" alt="' . esc_attr($scheduleditem['thumb_img_url']) . '"></a>';
    } else {
      $output .= '<a class="thumbnail" href="/maker/entry/' . $scheduleditem['id'] . '"><img src="' . legacy_get_resized_remote_image_url($scheduleditem['large_img_url'], 140, 140) . '" alt="' . esc_attr($scheduleditem['thumb_img_url']) . '"></a>';
    }
    $output .= '</div>';
  }
  $output .= '</td><td>';
  $output .= '<h4><a href="/maker/entry/' . $scheduleditem['id'] . '">' . $scheduleditem['name'] . '</a></h4>';

  // Presenter Name(s)
  $output .= '<h4 class="maker-name">' . $scheduleditem['maker_list'] . '</h4>';
  // Application Descriptions
  $description = $scheduleditem['project_description'];
  if (!empty($description))
    $output .= $description;

  $output .= '</td>';
  $output .= '</tr>';

  return $output;
}

function mf_display_schedule_by_area_old($atts) {
  global $mfform;

  // Get the faire date array. If the
  $faire = $atts['faire'];
  $area = $atts['area'];
  $subarea = htmlspecialchars_decode($atts['subarea']);

  $subarea_clean_name = strtolower(str_replace('&', '', (str_replace(' ', '', str_replace(':', '', $subarea)))));
  // Make sure we actually passed a valid faire...
  //if ( empty( $faire_date ) )
  //	return '<h3>Not a valid faire!</h3>';
  // Get Friday events by location
  $friday = wp_cache_get($faire . '_friday_schedule_' . $area . '_' . $subarea_clean_name, 'area');
  if ($friday === false) {
    $friday = get_mf_schedule_by_faire($faire, 'Friday', $area, $subarea);
    wp_cache_set($faire . '_friday_schedule_' . $area . '_' . $subarea_clean_name, $friday, 'area', 3000);
  }
  // Get Saturday events by location
  $saturday = wp_cache_get($faire . '_saturday_schedule_' . $area . '_' . $subarea_clean_name, 'area');
  if ($saturday === false) {
    $saturday = get_mf_schedule_by_faire($faire, 'Saturday', $area, $subarea);
    wp_cache_set($faire . '_saturday_schedule_' . $area . '_' . $subarea_clean_name, $saturday, 'area', 3000);
  }
  // Get Saturday events by location
  $sunday = wp_cache_get($faire . '_sunday_schedule_' . $area . '_' . $subarea_clean_name, 'area');
  if ($sunday === false) {
    $sunday = get_mf_schedule_by_faire($faire, 'Sunday', $area, $subarea);
    wp_cache_set($faire . '_sunday_schedule_' . $area . '_' . $subarea_clean_name, $sunday, 'area', 3000);
  }


  //$output = '<div class="row"><div class="col-md-4"><h2><a href="' . esc_url( get_permalink( absint( $data['area'] ) ) . '?faire=' . $data['faire'] ) . '">' . $subarea_array[2] . '</a></h2></div> <div class="col-md-1 pull-right" style="position:relative; top:7px;"><a href="#" onclick="window.print();return false;"><img src="' . get_stylesheet_directory_uri() . '/images/print-ico.png" alt="Print this schedule" /></a></div></div>';
  $output = '<div class="row padtop" style="height:58px;overflow:hidden;margin:0;">'
          . '<ul id="tabs" class="nav nav-tabs">||navtabs||</ul>'
          . '<div class="pull-right" style="position:relative; top:-31px;"><a href="#" onclick="window.print();return false;"><img src="' . get_stylesheet_directory_uri() . '/images/print-ico.png" alt="Print this schedule" /></a></div></div>';

  // Let's loop through each day and spit out a schedule?
  $days = array('friday', 'saturday', 'sunday');

  $output .= ' <div class="tab-content">';

  $navTabs = '';
  foreach ($days as $day) {

    if (count(${ $day }) > 0) {

      $navTabs .= '<li class="' . ($day == 'saturday' ? 'active' : '') . '"><a class="text-capitalize" href="#' . str_replace(' ', '', $subarea_clean_name) . esc_attr($day) . '" data-toggle="tab">' . esc_attr($day) . '</a></li>';

      // Start the schedule
      $output .= '<div id="' . str_replace(' ', '', $subarea_clean_name) . esc_attr($day) . '" class="tab-pane fade in ' . ($day == 'saturday' ? 'active' : '') . '">';
      $output .= '<table id="' . esc_attr($day) . '" class="table table-bordered table-schedule">';

      // Loop through the events and get the applications
      foreach (${ $day } as $scheduleditem) :

        $event_id = $scheduleditem['id'];

        $output .= '<tr>';
        $output .= '<td class="dateTime col-xs-2 col-sm-3 col-md-3 col-lg-3">';
        $output .= '<h4>' . esc_html($scheduleditem['day']) . '</h4>';
        $output .= '<p><span class="visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block">' . esc_html(date('h:i A', strtotime($scheduleditem['time_start']))) . ' &mdash; </span>' . esc_html(date('h:i A', strtotime($scheduleditem['time_end']))) . '</p>';

        if (isset($scheduleditem['large_img_url']) || isset($scheduleditem['thumb_img_url'])) {
          $output .= '<div class="pull-left">';
          // We may want to over ride the photo of an application on the schedule page by checking if there is a featured image on the event item
          if ($scheduleditem['thumb_img_url']) {
            $output .= '<a class="thumbnail" href="/maker/entry/' . $scheduleditem['id'] . '"><img src="' . legacy_get_resized_remote_image_url($scheduleditem['thumb_img_url'], 140, 140) . '" alt="' . esc_attr($scheduleditem['thumb_img_url']) . '"></a>';
          } else {

            $output .= '<a class="thumbnail" href="/maker/entry/' . $scheduleditem['id'] . '"><img src="' . legacy_get_resized_remote_image_url($scheduleditem['large_img_url'], 140, 140) . '" alt="' . esc_attr($scheduleditem['thumb_img_url']) . '"></a>';
          }
          $output .= '</div>';
        }
        $output .= '</td><td>';
        $output .= '<h4><a href="/maker/entry/' . $scheduleditem['id'] . '">' . $scheduleditem['name'] . '</a></h4>';

        // Presenter Name(s)
        $output .= '<h4 class="maker-name">' . $scheduleditem['maker_list'] . '</h4>';
        // Application Descriptions
        $description = $scheduleditem['project_description'];
        if (!empty($description))
          $output .= $description;

        $output .= '</td>';
        $output .= '</tr>';
      endforeach;

      $output .= '</table></div>';
    }
  }
  $output .='</div>';
  $output = str_replace('||navtabs||', $navTabs, $output);
  return $output;
}

add_shortcode('mf_schedule_by_area', 'mf_display_schedule_by_area');

function get_mf_schedule_by_faire($faire, $day = '', $area = '', $subarea = '') {
  $faire = strtoupper($faire);
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  $select_query = "SELECT  area.area,subarea.subarea,subarea.nicename,location.subarea_id,
                                entity.lead_id as entry_id, DAYNAME(schedule.start_dt) as day,
                                entity.project_photo as photo, schedule.start_dt, schedule.end_dt,
                                entity.presentation_title, entity.desc_short as description,
                                (select  group_concat( distinct concat(maker.`FIRST NAME`,' ',maker.`LAST NAME`) separator ', ') as Makers
                                    from    wp_mf_maker maker,
                                            wp_mf_maker_to_entity maker_to_entity
                                    where   schedule.entry_id           = maker_to_entity.entity_id  AND
                                            maker_to_entity.maker_id    = maker.maker_id AND
                                            maker_to_entity.maker_type != 'Contact'
                                    group by maker_to_entity.entity_id
                                )  as makers_list    ,
                                (select  Photo from    wp_mf_maker maker,
                                            wp_mf_maker_to_entity maker_to_entity
                                    where   schedule.entry_id           = maker_to_entity.entity_id  AND
                                            maker_to_entity.maker_id    = maker.maker_id AND
                                            maker_to_entity.maker_type = 'presenter'
                                   group by maker_to_entity.entity_id
                                )  as maker_photo

                        FROM    wp_mf_schedule schedule,
                                wp_mf_entity entity,
                                wp_mf_location location,
                                wp_mf_faire_subarea subarea,
                                wp_mf_faire_area area

                        where   schedule.entry_id   = entity.lead_id
                                AND entity.status       = 'Accepted'
                                and location.entry_id   = schedule.entry_id
                                and location.ID         = schedule.location_id
                                and subarea.id          = location.subarea_id
                                and area.id             = subarea.area_id
                                and schedule.faire      = '" . $faire . "' " .
          " ORDER BY   subarea.sort_order,
                                    schedule.start_dt ASC";

  $mysqli->query("SET NAMES 'utf8'");
  $result = $mysqli->query($select_query) or trigger_error($mysqli->error . "[$select_query]");

// Initalize the schedule container
  $schedules = array();

// Loop through the posts
  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    // Return some post meta
    $entry_id = $row['entry_id'];
    $app_id = $entry_id;
    $day = $row['day'];
    $start = strtotime($row['start_dt']);
    $stop = strtotime($row['end_dt']);
    $schedule['nicename'] = $row['nicename'];
    $schedule['subarea'] = $row['subarea'];
    $schedule['subarea_id'] = $row['subarea_id'];
    // REQUIRED: Schedule ID
    $schedule['id'] = $entry_id;
    $schedule_name = isset($row['presentation_title']) ? $row['presentation_title'] : '';

    // Get Maker and Project Photo images.  Use the maker if it is set.
    $maker_photo = !empty($row['maker_photo']) ? $row['maker_photo'] : '';
    $project_photo = !empty($row['photo']) ? $row['photo'] : '';
    $app_photo = empty($maker_photo) ? $project_photo : $maker_photo;

    //find out if there is an override image for this page
    $overrideImg = findOverride($entry_id, 'schedule');
    if ($overrideImg != '')
      $app_photo = $overrideImg;
    // REQUIED: Application title paired to scheduled item
    $schedule['name'] = html_entity_decode($schedule_name, ENT_COMPAT, 'utf-8');
    $schedule['time_start'] = date(DATE_ATOM, $start);
    $schedule['time_end'] = date(DATE_ATOM, $stop);
    $schedule['day'] = $day;
    $schedule['project_description'] = isset($row['description']) ? $row['description'] : '';

    // Rename the field, keeping 'time_end' to ensure this works.
    $schedule['time_stop'] = date(DATE_ATOM, strtotime('-1 hour', $stop));

    // Schedule thumbnails. Nothing more than images from the application it is tied to
    $schedule['thumb_img_url'] = esc_url(legacy_get_resized_remote_image_url($app_photo, '80', '80'));
    $schedule['large_img_url'] = esc_url(legacy_get_resized_remote_image_url($app_photo, '600', '600'));

    // A list of applications assigned to this event (should only be one really...)
    $schedule['entity_id_refs'] = array(absint($entry_id));

    // Application Makers
    $schedule['maker_list'] = (!empty($row['makers_list']) ) ? $row['makers_list'] : null;

    $maker_ids = array();

    // Put the application into our list of schedules
    array_push($schedules, $schedule);
  }
  return $schedules;
}

/* Adjust admin bar and wp-admin handling by login */
add_action('admin_init', 'mf_remove_dashboard');

function mf_remove_dashboard() {
  global $current_user;
  $user = wp_get_current_user();
  if (is_array($user->roles) && in_array('maker', $user->roles)) {
    $requestURI = (isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'');
    if (!current_user_can('manage_options') && $requestURI != '/wp-admin/admin-ajax.php') {
      wp_redirect(home_url());
      exit;
    }
  }
}
//redirect makers to the edit entry page  **need to replace site url with actual view path **
function mf_login_redirect($redirect_to, $request, $user) {
  return ( is_array($user->roles) && in_array('maker', $user->roles) ? site_url() : admin_url());
}

add_filter('login_redirect', 'mf_login_redirect', 10, 3);
add_action('admin_init', 'remove_admin_bar');
add_action('init', 'remove_admin_bar');

function remove_admin_bar() {
  global $current_user;
  $user = wp_get_current_user();
  if (is_array($user->roles) && in_array('maker', $user->roles)) {
    show_admin_bar(false);
    add_filter('show_admin_bar', '__return_false');
  }
}

/* Rewrite Rules */
add_action('init', 'onsitecheckin_rewrite_rules');
function onsitecheckin_rewrite_rules() {
    add_rewrite_rule( 'onsitecheckin/?([^/]*)', 'index.php?onsitecheckin=true&token=$matches[1]', 'top' );
    add_rewrite_rule( 'processonsitecheckin/?([^/]*)', 'index.php?processonsitecheckin=true&token=$matches[1]', 'top' );
}
/* Query Vars */
add_filter( 'query_vars', 'onsitecheckin_register_query_var' );
function onsitecheckin_register_query_var( $vars ) {
    $vars[] = 'processonsitecheckin';
    $vars[] = 'onsitecheckin';
    $vars[] = 'token';
    return $vars;
}
/* Template Include */
add_filter('template_include', 'onsitecheckin_include', 1, 1); 
function onsitecheckin_include($template)
{
    global $wp_query; //Load $wp_query object
    $page_value = $wp_query->query_vars['onsitecheckin']; //Check for query var "blah"

    if ($page_value && $page_value == "true") { //Verify "blah" exists and value is "true".
        return $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/makerfaire/page-onsite-checkin.php'; //Load your template or file
    }

    return $template; //Load normal template when $page_value != "true" as a fallback
}
add_filter('template_include', 'processonsitecheckin_include', 1, 1); 
function processonsitecheckin_include($template)
{
    global $wp_query; //Load $wp_query object
    $page_value = $wp_query->query_vars['processonsitecheckin']; //Check for query var "blah"

    if ($page_value && $page_value == "true") { //Verify "blah" exists and value is "true".
        return $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/makerfaire/php/process-onsite-checkin.php'; //Load your template or file
    }

    return $template; //Load normal template when $page_value != "true" as a fallback
}