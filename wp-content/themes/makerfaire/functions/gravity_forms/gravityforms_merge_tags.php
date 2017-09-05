<?php


//MF custom merge tags
add_filter('gform_custom_merge_tags', 'mf_custom_merge_tags', 10, 4);
add_filter('gform_replace_merge_tags', 'mf_replace_merge_tags', 10, 7);
add_filter('gform_field_content', 'mf_field_content', 10, 5);

/**
* add custom merge tags
* @param array $merge_tags
* @param int $form_id
* @param array $fields
* @param int $element_id
* @return array
*/
function mf_custom_merge_tags($merge_tags, $form_id, $fields, $element_id) {
    $merge_tags[] = array('label' => 'Entry Schedule', 'tag' => '{entry_schedule}');
    $merge_tags[] = array('label' => 'Entry Resources', 'tag' => '{entry_resources}');
    $merge_tags[] = array('label' => 'Entry Attributes', 'tag' => '{entry_attributes}');
    $merge_tags[] = array('label' => 'Scheduled Locations', 'tag' => '{sched_loc}');
    $merge_tags[] = array('label' => 'Faire ID', 'tag' => '{faire_id}');
    $merge_tags[] = array('label' => 'Resource Category Lock Ind', 'tag' => '{rmt_res_cat_lock}');
    $merge_tags[] = array('label' => 'Attribute Lock Ind', 'tag' => '{rmt_att_lock}');

    //add merge tag for Attention field - Confirmation Comment
    $merge_tags[] = array('label' => 'Confirmation Comment', 'tag' => '{CONF_COMMENT}');

    return $merge_tags;
}

/**
* replace custom merge tags in notifications
* @param string $text
* @param array $form
* @param array $lead
* @param bool $url_encode
* @param bool $esc_html
* @param bool $nl2br
* @param string $format
* @return string
*/
function mf_replace_merge_tags($text, $form, $lead, $url_encode, $esc_html, $nl2br, $format) {
  global $wpdb;
  $entry_id = (isset($lead['id'])?$lead['id']:'');

  //faire id
  if (strpos($text, '{faire_id}')       !== false) {
    $sql = "select faire from wp_mf_faire where FIND_IN_SET (".$lead['form_id'].",wp_mf_faire.form_ids)> 0";
    $faireId = $wpdb->get_var($sql);
    $text = str_replace('{faire_id}', $faireId, $text);
  }

  //Entry Schedule
  if (strpos($text, '{entry_schedule}') !== false) {
    $schedule = get_schedule($lead);
    $text = str_replace('{entry_schedule}', $schedule, $text);
  }

  //scheduled locations {sched_loc}
  if (strpos($text, '{sched_loc}') !== false) {
    $schedule = get_schedule($lead,true);
    $text = str_replace('{sched_loc}', $schedule, $text);
  }

  //Entry Resources
  if (strpos($text, '{entry_resources') !== false) {
    $startPos         = strpos($text, '{entry_resources'); //pos of start of merge tag
    $closeBracketPos  = strpos($text, '}', $startPos); //find the closing bracket of the merge tag

    //pull full merge tag
    $res_merge_tag    = substr ( $text , $startPos, $closeBracketPos - $startPos + 1);

    //exclude resources
    $excResources = ''; //default
    $excStartPos  = strpos($res_merge_tag, 'not="'); //pos of start of excluded resource id's

    //are there resources to exclude?
    if ($excStartPos !== false) {
      $excStartPos += 5;   //add 5 to move past the not="
      //find the end of the not section
      $excEndPos        = strpos($res_merge_tag, '"', $excStartPos);
      $excResources     = substr($res_merge_tag , $excStartPos, $excEndPos - $excStartPos);
    }

    //include resources
    $incResources = ''; //default
    $incStartPos  = strpos($res_merge_tag, ':');   //pos of start of excluded resource id's

    //are there specific resources to include?
    if ($incStartPos !== false) {
      $incStartPos += 1;   //add 1 to move past the :"
      //find the end of the include section
      $incEndPos        = strpos($res_merge_tag, ' ', $incStartPos);

      //can be ended by a space or the closing bracket
      if ($incEndPos === false) $incEndPos = strpos($res_merge_tag, '}', $incStartPos);

      $incResources     = substr($res_merge_tag , $incStartPos, $incEndPos - $incStartPos);
    }

    $resTable = '<table cellpadding="10" width=60%><tr><th width="40%">Resource</th><th>Quantity</th></tr>';
    $resources = get_mf_resources($lead, $excResources, $incResources);

    foreach($resources as $entRes){
      $resTable .= '<tr><td>'.$entRes['resource'].'</td><td style="text-align:center">'.$entRes['qty'].'</td></tr>';
    }
    $resTable .= '</table>';
    $text = str_replace($res_merge_tag, $resTable, $text);
  }

  //individual attributes {entry_attributes:2,4,6,9}
  if (strpos($text, '{entry_attributes') !== false) {
    $startPos    = strpos($text, '{entry_attributes'); //pos of start of merge tag
    $attStartPos = strpos($text, ':',$startPos);       //pos of start of attribute id's
    $closeBracketPos = strpos($text, '}', $startPos); //find the closing bracket of the merge tag

    //attribute ID's will be a comma separated list between $attStartPos and $closeBracketPos
    $attIDs = substr($text, $attStartPos+1,$closeBracketPos-$attStartPos-1);

    $attArr = explode(",",$attIDs);
    $attTable  = '<table cellpadding="10"  width=60%><tr><th width="40%">Attribute</th><th>Value</th></tr>';
    foreach($attArr as $att){
      $AttText = get_attribute($lead,trim($att));
      if(!empty($AttText)){
        $attTable .= '<tr>';
        foreach($AttText as $attDetail){
          $attTable .= '<td>'.$attDetail['attribute'].'</td>'.
                       '<td style="text-align:center">'.$attDetail['value'].'</td>';
        }
        $attTable .= '</tr>';
      }
    }
    $attTable  .= '</table>';
    //full merge tag for replace
    $mergeTag = substr($text, $startPos,$closeBracketPos-$startPos+1);
    $text = str_replace($mergeTag, $attTable, $text);
  }

  //attention field
  if (strpos($text, '{CONF_COMMENT}') !== false) {
    $sql = "SELECT comment "
        . " from wp_rmt_entry_attn,wp_rmt_attn"
        . " where entry_id = ".$entry_id
        . " and wp_rmt_attn.ID = attn_id"
        . " and token = 'CONF_COMMENT'";
    $attnText = $wpdb->get_var($sql);
    $text = str_replace('{CONF_COMMENT}', $attnText, $text);
  }

  //resource lock indicator
  if (strpos($text, '{rmt_res_cat_lock') !== false) {
    $startPos        = strpos($text, '{rmt_res_cat_lock'); //pos of start of merge tag
    $RmtStartPos     = strpos($text, ':',$startPos);   //pos of start RMT field ID
    $closeBracketPos = strpos($text, '}', $startPos);  //find the closing bracket of the merge tag

    //resource ID
    $RMTcatID = substr($text, $RmtStartPos+1,$closeBracketPos-$RmtStartPos-1);

    //is this a valid RMT field??
    if(is_numeric($RMTcatID)) {
      //find locked value of RMT field
      $lockCount = $wpdb->get_var('SELECT count(*) as count
        FROM `wp_rmt_entry_resources`
        left outer join wp_rmt_resources
            on wp_rmt_entry_resources.resource_id = wp_rmt_resources.id
        where wp_rmt_resources.resource_category_id = '.$RMTcatID.' and lockBit=1 and entry_id = '.$entry_id);
      $mergeTag = substr($text, $startPos,$closeBracketPos-$startPos+1);
      $text = str_replace($mergeTag, ($lockCount>0?'Yes':'No'), $text);
    }
  }


  //attribute lock indicator
  if (strpos($text, '{rmt_att_lock') !== false) {
    $startPos        = strpos($text, '{rmt_att_lock'); //pos of start of merge tag
    $RmtStartPos     = strpos($text, ':',$startPos);   //pos of start RMT field ID
    $closeBracketPos = strpos($text, '}', $startPos);  //find the closing bracket of the merge tag

    //attribute ID
    $RMTid = substr($text, $RmtStartPos+1,$closeBracketPos-$RmtStartPos-1);

    //is this a valid RMT field??
    if(is_numeric($RMTid)) {
      //find locked value of RMT field
      $lockBit = $wpdb->get_var('SELECT lockBit FROM `wp_rmt_entry_attributes` where attribute_id = '.$RMTid. ' and entry_id = '.$entry_id.' limit 1');
      $mergeTag = substr($text, $startPos,$closeBracketPos-$startPos+1);
      $text = str_replace($mergeTag, ($lockBit==1?'Yes':'No'), $text);
    }
  }

  return $text;
}

/**
* replace custom merge tags in field content
* @param string $field_content
* @param array $field
* @param string $value
* @param int $lead_id
* @param int $form_id
* @return string
*/
function mf_field_content($field_content, $field, $value, $lead_id, $form_id) {
    if (strpos($field_content, '{entry_schedule}') !== false) {
        $lead = GFAPI::get_entry( $lead_id );
        $schedule = get_schedule($lead);

        $field_content = str_replace('{entry_schedule}', $schedule, $field_content);
    }

    return $field_content;
}
/** End MF custom merge tags **/

/* Return value and attribute of selected attribute per entry if set */
function get_attribute($lead,$attID){
  global $wpdb;
  $return = array();
  $entry_id = (isset($lead['id'])?$lead['id']:'');

  if($entry_id!='' && $attID!=''){
    //gather resource data
    $sql = "SELECT value,"
            . " (select category from wp_rmt_entry_att_categories where wp_rmt_entry_att_categories.ID = attribute_id)as attribute "
            . " FROM `wp_rmt_entry_attributes`  "
            . " where entry_id = ".$entry_id." and attribute_id = ".$attID." order by attribute ASC, value ASC";
    $results = $wpdb->get_results($sql);
    foreach($results as $result){
      $return[] = array('attribute'=>$result->attribute, 'value'=> $result->value);
    }
  }
  return $return;
}

/*
 * Return array of resource information for lead
 * $lead   = Entry object
 * $excRes = comma separatd list of resource catgories to exclude
 * $incRes = comma separatd list of resource catgories to include
 */
/* Return array of resource information for lead*/
function get_mf_resources($lead, $excRes = '', $incRes=''){
  global $wpdb;
  $return = array();
  $entry_id = (isset($lead['id'])?$lead['id']:'');

  if($entry_id!=''){
    $excSQL = ($excRes!='' ? " and wp_rmt_resource_categories.ID not in($excRes) " : '');
    $incSQL = ($incRes!='' ? " and wp_rmt_resource_categories.ID in($incRes) " : '');

    //gather resource data
    $sql = "SELECT er.qty, type, wp_rmt_resource_categories.category as item "
            . "FROM `wp_rmt_entry_resources` er, wp_rmt_resources, wp_rmt_resource_categories "
            . "where er.resource_id = wp_rmt_resources.ID "
            . "and resource_category_id = wp_rmt_resource_categories.ID  "
            . $excSQL . $incSQL
            . "and er.entry_id = ".$entry_id." order by item ASC, type ASC";

    $results = $wpdb->get_results($sql);
    foreach($results as $result){
      $return[]= array('resource'=>$result->item.' - '.$result->type, 'qty'=> $result->qty);
    }
  }

  return $return;
}

/* Return schedule for lead */
function get_schedule($lead,$locsOnly = false){
    global $wpdb;
    $schedule = '';
    $entry_id = (isset($lead['id'])?$lead['id']:'');

    if($entry_id!=''){
        //get scheduling information for this lead
        $sql = "SELECT  area.area,subarea.subarea,subarea.nicename,
                        schedule.start_dt, schedule.end_dt
                FROM    wp_mf_schedule schedule,
                        wp_mf_location location,
                        wp_mf_faire_subarea subarea,
                        wp_mf_faire_area area

                where       schedule.entry_id   = $entry_id
                        and schedule.location_id=location.ID
                        and location.entry_id   = schedule.entry_id
                        and subarea.id          = location.subarea_id
                        and area.id             = subarea.area_id";

        $results = $wpdb->get_results($sql);
        if($wpdb->num_rows > 0){
            foreach($results as $row){
              $subarea = ($row->nicename!=''&&$row->nicename!=''?$row->nicename:$row->subarea);
              $start_dt = strtotime($row->start_dt);
              $end_dt = strtotime($row->end_dt);
              if($locsOnly){
                $schedule .= ($schedule!=''?',':'').$subarea;
              }else{
                $schedule .= $row->area.' '.$subarea;
                $schedule .= '<br/>';
                $schedule .= '<span>'.date("l, n/j/y, g:i A",$start_dt).' to '.date("l, n/j/y, g:i A",$end_dt).'</span><br/>';
              }
            }
        }
    }
    return $schedule;
}

