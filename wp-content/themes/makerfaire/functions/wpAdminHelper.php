<?php
function make_remove_dashboard_widgets() {
  // Remove Quick Press Widget (Also known as Quick Draft)
  remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
  // Remove WordPress Blog Widget
  remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
  // Remove Other WordPress News Widget
  remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
  //Elementor Widget
  remove_meta_box( 'e-dashboard-overview', 'dashboard', 'normal' );
  //site health
  remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
  //at a glance
  remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
  
  // Remove WP Activity Widget
  remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
  //Jetpack
  remove_meta_box( 'jetpack_summary_widget', 'dashboard', 'normal' );
  
  //yoast
  remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
  remove_meta_box( 'wpseo-wincher-dashboard-overview', 'dashboard', 'normal' );

}
add_action('wp_dashboard_setup', 'make_remove_dashboard_widgets' );

//returns array of area/subarea by faire
function retSubAreaByFaire($faire) {
  global $wpdb;
  $subAreaArr = array();
  $sql  = "select   wp_mf_faire_subarea.id, wp_mf_faire_area.area, subarea, nicename "
. "        from     wp_mf_faire_subarea "
. "        join     wp_mf_faire_area on wp_mf_faire_subarea.area_id = wp_mf_faire_area.ID "
. "        join     wp_mf_faire on faire='" .strtoupper($faire) ."'"
. "        order by area ASC, subarea ASC";

  $results = $wpdb->get_results($sql);
  if($wpdb->num_rows > 0){
    foreach($results as $row){
      $subArea = (isset($row->nicename) && $row->nicename != '' ? $row->nicename:$row->subarea);
      $subAreaArr[$row->area][] = $subArea;
    }
  }
	return $subAreaArr;
}

//returns array of area/subarea by entry
function retSubAreaByEntry($entry_id) {
  global $wpdb;
  $sql = "select  location.entry_id, area.area, subarea.subarea, location.subarea_id,
                  subarea.nicename, location.location, schedule.start_dt, schedule.end_dt,
                  location.id as location_id
            from  wp_mf_location location
            join  wp_mf_faire_subarea subarea
                ON  location.subarea_id = subarea.ID
            join wp_mf_faire_area area
                ON subarea.area_id = area.ID
            left join wp_mf_schedule schedule
                ON location.ID = schedule.location_id
             where location.entry_id=$entry_id";
  $results = $wpdb->get_results($sql);

  //return array key = location ID
  // fields: area, subarea, nicename, location, start_dt and end_dt
  $retArray = array();
  if($wpdb->num_rows > 0){
    foreach($results as $row){
      $retArray[$row->location_id] = array(
            'area'      =>  $row->area,
            'subarea'   =>  $row->subarea,
            'nicename'  =>  $row->nicename,
            'location'  =>  $row->location,
            'start_dt'  =>  $row->start_dt,
            'end_dt'    =>  $row->end_dt
          );
    }
  }

	return $retArray;
}

//used to find the current resource information for a specific entry
/* Return array of resource information for lead */
function retResByEntry($entry_id) {
  global $wpdb;
  $return = array();

  if($entry_id!=''){
    //gather resource data
    $sql = "SELECT er.qty, type, wp_rmt_resource_categories.category as item, wp_rmt_resources.token "
            . "FROM `wp_rmt_entry_resources` er, wp_rmt_resources, wp_rmt_resource_categories "
            . "where er.resource_id = wp_rmt_resources.ID "
            . "and resource_category_id = wp_rmt_resource_categories.ID  "
            . "and er.entry_id = ".$entry_id." order by item ASC, type ASC";
    $results = $wpdb->get_results($sql);
    foreach($results as $result){
      $return[]= array('item'=>$result->item, 'type'=>$result->type, 'qty'=> $result->qty,'token'=>$result->token);
    }
  }
  return $return;
}


// alphabetize menu items
function sort_admin_menu() {
	if(is_admin()) {
	    global $menu;
	    // alphabetize submenu items
		if($menu) {
		    usort( $menu, function ( $a, $b ) {
		        if(isset($a['5']) && $a[5]!='menu-dashboard'){
		          // format of a submenu item is [ 'My Item', 'read', 'manage-my-items', 'My Item' ]
		          return strcasecmp( strip_tags($a[0]), strip_tags($b[0]) );
		        }
		    } );
		    //remove separators
		    $menu = array_filter($menu, function($item) {
		        return $item[0] != '';
		    });
		}
	}
}
add_action('admin_init','sort_admin_menu');
