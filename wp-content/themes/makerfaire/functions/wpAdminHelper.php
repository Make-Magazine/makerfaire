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

