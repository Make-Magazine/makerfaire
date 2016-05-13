<?php
/*
add_filter('acf/load_field/name=stage_list', 'acf_load_stage_choices');
// used to set the drop down for sponsor per stage
function acf_load_stage_choices( $value) {

  echo 'chcking now';

while( have_rows('stage_sponsor_rep',479338)){
$faire = get_sub_field('faire');
echo 'faire'.$faire;
}
  /*
  if(have_rows('field_5732654df7547','option')){
    echo 'hello';
  }*/

  //$faire = get_sub_field('faire');
  //echo 'faire is '.$faire.'<br/><br/> ';
  //var_dump($field);
  /*
  if(get_field('stage_sponsor')){
  //if( have_rows('stage_sponsor')) {
    echo 'yes there are rows<br/>';
    // while has rows
    while(has_sub_field('stage_sponsor')){
    //while( have_rows('stage_sponsor') ) {
      echo 'in the while loop<br/>';
        // instantiate row
        the_row();
        // vars
        $faire = get_sub_field('faire');
        echo $faire.'<br/>';
        $field['choices'] = array();
        if($faire!=''){
          $choices = retSubAreaByFaire($faire);
        }else{
          $choices = array('Please set the faire and save and refresh the page to see the stage choices');
        }


        // loop through array and add to field 'choices'
         if( is_array($choices) ) {
           foreach( $choices as $key=>$choice ) {
             $field['choices'][ $key ] = $choice;
           }
         }
         die('end while');
      }

    }*/
/*
  // return the field
  return $value;

}*/



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


//function to create table tags by faire
function genTableTags($faire) {
  global $wpdb;
  //error_log('faire is '.$faire);
  //find the exhibit and sponsor forms by faire
  $sql = "select form_ids from wp_mf_faire where faire='".$faire."'";
  $formIds = $wpdb->get_var($sql);
  //remove any spaces
  $formIds = str_replace(' ', '', $formIds);
  $forms = explode(",", $formIds);
  foreach($forms as $formId){
    //error_log($formId .' is a type of '.$form['form_type'] );
    $form = GFAPI::get_form($formId);
    if($form['form_type']=='Exhibit' || $form['form_type']=='Sponsor' || $form['form_type']=='Startup Sponsor'){
      $search_criteria['status'] = 'active';
      $entries         = GFAPI::get_entries( $formId, $search_criteria );
      foreach($entries as $entry){
        $url = TEMPLATEPATH.'/fpdi/tabletag.php?eid='.$entry['id'].'&faire='.$faire.'&type=save';
        error_log($url);
        file_get_contents($url);
      }
    }
  }
}

add_action( 'gen_table_tags', 'genTableTags', 10, 1 );
