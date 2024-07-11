<?php
/* This function is used by the individual entry pages to display if this entry won any ribbons */
function checkForRibbons($postID=0,$entryID=0){
    global $wpdb;
    if($postID != 0){
        $sql = "select * from wp_mf_ribbons where post_id = ".$postID." order by ribbonType";
    }else{
      if($entryID==0 || $entryID==''){
        return '';
      }else{
        $sql = "select * from wp_mf_ribbons where entry_id = ".$entryID." order by ribbonType";
      }        
    }
    
    $ribbons = $wpdb->get_results($sql);
    $return = "";
    if(!empty($ribbons)) {
      $return = '<div class="ribbon-wrapper">';
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
      $return .= '</div>';    
    }
    return $return;
}


/* This function searches the database to see if any of the image overrides are set in the gravity form
 * If it is, it retrieves the value set for that override
 * Field ID         Description
 * 324              Image Override 1
 * 334              Image Override 1 place
 * 330              Image Override 2
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
        $sql = "select * from wp_gf_entry_meta as detail join "
                . "             (SELECT entry_id, meta_key FROM `wp_gf_entry_meta` "
                . "                 WHERE `entry_id` = $entry_id AND `meta_key` BETWEEN '334.0' and '338.9' AND `meta_value` = '$type' "
                . "                 ORDER BY `wp_gf_entry_meta`.`meta_key` ASC limit 1) "
                . "             as override on detail.entry_id = override.entry_id "
                . "         where   (detail.meta_key = '331' and override.meta_key like '335.%') or "
                . "                 (detail.meta_key = '332' and override.meta_key like '336.%') or "
                . "                 (detail.meta_key = '333' and override.meta_key like '337.%') or "
                . "                 (detail.meta_key = '330' and override.meta_key like '338.%') or "
                . "                 (detail.meta_key = '329' and override.meta_key like '334.%')";
        $results = $wpdb->get_results($sql);
        if($wpdb->num_rows > 0){
            return $results[0]->meta_value;
        }
    }
    return '';
}