<?php

// This function is called via ajax to retriev the blue ribbon data
function retrieveRibbonData() {
   global $wpdb;
   require_once( TEMPLATEPATH. '/partials/ribbonJSON.php' );
    // IMPORTANT: don't forget to "exit"
    exit;
}
add_action( 'wp_ajax_nopriv_getRibbonData', 'retrieveRibbonData' );
add_action( 'wp_ajax_getRibbonData', 'retrieveRibbonData' );



/* This function is used by the individual entry pages to display if this entry one any ribbons */
function checkForRibbons($postID=0,$entryID=0){
    global $wpdb;
    if($postID != 0){
        $sql = "select * from wp_mf_ribbons where post_id = ".$postID." order by ribbonType";
    }else{
        $sql = "select * from wp_mf_ribbons where entry_id = ".$entryID." order by ribbonType";
    }
    $ribbons = $wpdb->get_results($sql);
    $return = "";
    //check for 0??
    $blueCount = $redCount = 0;
    foreach($ribbons as $ribbon){
      if($ribbon->ribbonType==0){
        for($i=0; $i< $ribbon->numRibbons;$i++){
          $return .= '<div class="blueMakey"></div>';
        }
      }

      if($ribbon->ribbonType==1){
        for($i=0; $i< $ribbon->numRibbons;$i++){
          $return .= '<div class="redMakey"></div>';
        }
      }
    }
    return $return;
}


/* This function searches the database to see if any of the image overrides are set in the gravity form
 * If it is, it retrieves the value set for that override
 * Field ID         Description
 * 324              Image Override 1
 * 334              Image Override 1 place
 * 326              Image Override 2
 * 338              Image Override 2 place
 * 333              Image Override 2
 * 337              Image Override 3 place
 * 332              Image Override 4
 * 336              Image Override 4 place
 * 331              Image Override 5
 * 335              Image Override 5 place
 */
function findOverride($entry_id, $type){
    global $wpdb;
    if($entry_id!=''){
        $sql = "select * from wp_rg_lead_detail as detail join "
                . "             (SELECT lead_id,field_number FROM `wp_rg_lead_detail` "
                . "                 WHERE `lead_id` = $entry_id AND `field_number` BETWEEN 334.0 and 338.9 AND `value` = '$type' "
                . "                 ORDER BY `wp_rg_lead_detail`.`field_number` ASC limit 1) "
                . "             as override on detail.lead_id = override.lead_id "
                . "         where   (detail.field_number = 331 and override.field_number between 335.0 and 335.9999) or "
                . "                 (detail.field_number = 332 and override.field_number between 336.0 and 336.9999) or "
                . "                 (detail.field_number = 333 and override.field_number between 337.0 and 337.9999) or "
                . "                 (detail.field_number = 330 and override.field_number between 338.0 and 338.9999) or "
                . "                 (detail.field_number = 329 and override.field_number between 334.0 and 334.9999)";
        $results = $wpdb->get_results($sql);
        if($wpdb->num_rows > 0){

            return $results[0]->value;
        }
    }
    return '';
}