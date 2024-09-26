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
    $merge_tags[] = array('label' => 'Entry Locations', 'tag' => '{entry_loc}');
    $merge_tags[] = array('label' => 'Entry Area Only', 'tag' => '{entry_area}');
    $merge_tags[] = array('label' => 'Entry Subarea Only', 'tag' => '{entry_subarea}');
    $merge_tags[] = array('label' => 'Entry Booth Number', 'tag' => '{entry_booth}');
    $merge_tags[] = array('label' => 'Faire ID', 'tag' => '{faire_id}');
    $merge_tags[] = array('label' => 'Resource Category Lock Ind', 'tag' => '{rmt_res_cat_lock}');
    $merge_tags[] = array('label' => 'Attribute Lock Ind', 'tag' => '{rmt_att_lock}');
    $merge_tags[] = array('label' => 'Supplemental Form Token', 'tag' => '{supp_form_token}');
    $merge_tags[] = array('label' => 'Exposure', 'tag' => '{exposure_token}');

    $merge_tags[] = array('label' => 'Confirmation Comment', 'tag' => '{CONF_COMMENT}'); //Attention field - Confirmation Comment
    $merge_tags[] = array('label' => 'Confirmation Button', 'tag' => '{CONF_BUTTON}');

    $merge_tags[] = array('label' => 'Requested Logistics', 'tag' => '{requested_logistics}');

    return $merge_tags;
}

/**
 * replace custom merge tags in notifications
 * @param string $text
 * @param array $form
 * @param array $entry
 * @param bool $url_encode
 * @param bool $esc_html
 * @param bool $nl2br
 * @param string $format
 * @return string
 */
function mf_replace_merge_tags($text, $form, $entry, $url_encode, $esc_html, $nl2br, $format) {
    global $wpdb;
    $entry_id = (isset($entry['id']) ? $entry['id'] : '');

    //faire id
    if (strpos($text, '{faire_id}')       !== false) {
        $sql = "select faire from wp_mf_faire where FIND_IN_SET (" . $entry['form_id'] . ",wp_mf_faire.form_ids)> 0";
        $faireId = $wpdb->get_var($sql);
        $text = str_replace('{faire_id}', $faireId, $text);
    }

    //Entry Schedule
    if (strpos($text, '{entry_schedule}') !== false) {
        $schedule = get_schedule($entry);
        $text = str_replace('{entry_schedule}', $schedule, $text);
    }

    //scheduled locations {entry_loc}
    if (strpos($text, '{entry_loc}') !== false) {
        $schedule = get_location($entry, 'full');
        $text = str_replace('{entry_loc}', $schedule, $text);
    }

    //Exposure Token
    if (strpos($text, '{exposure_token}') !== false) {
        $exposure = get_exposure($entry);
        $text = str_replace('{exposure_token}', $exposure, $text);
    }

    //scheduled locations {entry_area}
    if (strpos($text, '{entry_area}') !== false) {
        $schedule = get_location($entry, 'area');
        $text = str_replace('{entry_area}', $schedule, $text);
    }

    //scheduled locations {entry_subarea}
    if (strpos($text, '{entry_subarea}') !== false) {
        $schedule = get_location($entry, 'subarea');
        $text = str_replace('{entry_subarea}', $schedule, $text);
    }

    //scheduled locations {entry_booth}
    if (strpos($text, '{entry_booth}') !== false) {
        $schedule = get_location($entry, 'booth');
        $text = str_replace('{entry_booth}', $schedule, $text);
    }

    //requested logistics     
    if (strpos($text, '{requested_logistics}') !== false) {
        //find out if this entry is placed
        $exhibit_placed = gform_get_meta($entry['id'], 'expofp_placed');

        //pull attributes
        $sql = "select * from wp_rmt_entry_attributes where entry_id=" . $entry['id'];
        $attributes         = $wpdb->get_results($sql, ARRAY_A);
        $set_atts           = array_column($attributes, 'attribute_id');

        //requested space size - attribute 19
        $attKey             = array_search('19', $set_atts);
        $reqSpaceSize       = ($attKey !== FALSE ? $attributes[$attKey]['value'] : '');

        //final space size - attribute 2
        $attKey             = array_search('2', $set_atts);
        $finalSpaceSize     = ($attKey !== FALSE ? $attributes[$attKey]['value'] : '');

        //requested exposure - attribute 20
        $attKey             = array_search('20', $set_atts);
        $reqExposure        = ($attKey !== FALSE ? $attributes[$attKey]['value'] : '');

        //final exposure - subarea table
        $sql = "SELECT exposure FROM `wp_mf_location` 
                left outer join wp_mf_faire_subarea on subarea_id = wp_mf_faire_subarea.id 
                left outer join wp_mf_schedule on wp_mf_location.id=location_id 
                where   wp_mf_location.entry_id = " . $entry['id'] . "
                and     start_dt is NULL 
                order by wp_mf_location.ID 
                DESC limit 1;";
        $finalExposure      = $wpdb->get_var($sql);

        //pull resources
        $sql = "SELECT GROUP_CONCAT(concat(qty, ' - ', description) separator '<br/>') as description, resource_category_id 
                FROM `wp_rmt_entry_resources` 
                left outer join wp_rmt_resources on wp_rmt_resources.id=resource_id 
                where entry_id=" . $entry['id'] .
            " group by entry_id, resource_category_id;";
        
        $resources          = $wpdb->get_results($sql, ARRAY_A);
        $set_resources      = array_column($resources, 'resource_category_id');

        //final tables - resource category 2
        $resKey             = array_search('2', $set_resources);
        $finalTables        = ($resKey !== FALSE ? $resources[$resKey]['description'] : '');

        //final chairs - resource category 3
        $resKey             = array_search('3', $set_resources);
        $finalChairs        = ($resKey !== FALSE ? $resources[$resKey]['description'] : '');

        //final electricity - resource category 9 - 120V
        $resKey             = array_search('9', $set_resources); //120V
        $finalElec          = ($resKey !== FALSE ? $resources[$resKey]['description'] : '');

        
        //final electricity - resource category 10 - 220V
        $resKey         = array_search('10', $set_resources); //220V
        $elec220        = ($resKey !== FALSE ? $resources[$resKey]['description'] : '');     
        if($finalElec!='' && $elec220!='')  $finalElec .= '<br/>';
        $finalElec      .= $elec220;
        

        //requested tables and chairs
        $reqTables = '0';
        $reqChairs = '0';
        if (isset($entry['62'])) {
            if (stripos($entry['62'], '1 table') !== false) {
                //1 table and 2 chairs
                $reqTables = '1';
                $reqChairs = '2';
            } elseif (stripos($entry['62'], 'more than') !== false) {
                //More than 1 table and 2 chairs
                $reqTables = $entry['347'];
                $reqChairs = $entry['348'];
            }
        }

        //requested electricity
        $reqElec = '';
        //if 75 contains the word other, pull field 76 - special power requirements
        if (isset($entry['75']) && stripos($entry['75'], 'other') === false) {
            //the word 'other' is not found in field 75
            $reqElec = $entry['75'];
        } elseif (isset($entry['76'])) {
            //Other selected
            $reqElec = $entry['76'];
        }

        $reqLogistics = "<table class='resource_table'>
                            <thead>
                                <tr>
                                    <th scope='col' width='20%'></th>
                                    <th scope='col' width='40%'>Requested</th>
                                    <th scope='col' width='40%'>".($exhibit_placed == 'Placed' ? 'As Placed' : '') ."</th>
                                </tr>
                            </thead>
                            <tbody>             
                                <tr>
                                    <th scope='row'>Tables</th>
                                    <td>$reqTables</td>                                
                                    <td>" . ($exhibit_placed == 'Placed' ? $finalTables : '') . "</td>
                                </tr>
                                <tr>
                                    <th scope='row'>Chairs</th>
                                    <td>$reqChairs</td>                                
                                    <td>" . ($exhibit_placed == 'Placed' ? $finalChairs : '') . "</td>
                                </tr>
                                <tr>
                                    <th scope='row'>Electricity</th>
                                    <td>$reqElec</td>                                                
                                    <td>" . ($exhibit_placed == 'Placed' ? $finalElec : '') . "</td>
                                </tr>
                                <tr>
                                    <th scope='row'>Space Size</th>
                                    <td>$reqSpaceSize</td>
                                    <td>" . ($exhibit_placed == 'Placed' ? $finalSpaceSize : '') . "</td>
                                </tr>              
                                <tr>
                                    <th scope='row'>Exposure</th>
                                    <td>$reqExposure</td>                                                
                                    <td>" . ($exhibit_placed == 'Placed' ? $finalExposure : '') . "</td>
                                </tr>
                            </tbody>
                        </table>";

        $text = str_replace('{requested_logistics}', $reqLogistics, $text);
    }

    //Entry Resources
    if (strpos($text, '{entry_resources') !== false) {
        $startPos         = strpos($text, '{entry_resources'); //pos of start of merge tag
        $closeBracketPos  = strpos($text, '}', $startPos); //find the closing bracket of the merge tag

        //pull full merge tag
        $res_merge_tag    = substr($text, $startPos, $closeBracketPos - $startPos + 1);

        //exclude resources
        $excResources = ''; //default
        $excStartPos  = strpos($res_merge_tag, 'not="'); //pos of start of excluded resource id's

        //are there resources to exclude?
        if ($excStartPos !== false) {
            $excStartPos += 5;   //add 5 to move past the not="
            //find the end of the not section
            $excEndPos        = strpos($res_merge_tag, '"', $excStartPos);
            $excResources     = substr($res_merge_tag, $excStartPos, $excEndPos - $excStartPos);
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

            $incResources     = substr($res_merge_tag, $incStartPos, $incEndPos - $incStartPos);
        }

        $resTable = '<table cellpadding="10" width=60%><tr><th width="40%">Resource</th><th>Quantity</th></tr>';
        $resources = get_mf_resources($entry, $excResources, $incResources);

        foreach ($resources as $entRes) {
            $resTable .= '<tr><td>' . $entRes['resource'] . '</td><td style="text-align:center">' . $entRes['qty'] . '</td></tr>';
        }
        $resTable .= '</table>';
        $text = str_replace($res_merge_tag, $resTable, $text);
    }

    //individual attributes {entry_attributes:2,4,6,9}
    if (strpos($text, '{entry_attributes') !== false) {
        $startPos    = strpos($text, '{entry_attributes'); //pos of start of merge tag
        $attStartPos = strpos($text, ':', $startPos);       //pos of start of attribute id's
        $closeBracketPos = strpos($text, '}', $startPos); //find the closing bracket of the merge tag

        //attribute ID's will be a comma separated list between $attStartPos and $closeBracketPos
        $attIDs = substr($text, $attStartPos + 1, $closeBracketPos - $attStartPos - 1);

        $attArr = explode(",", $attIDs);
        $attTable  = '<table cellpadding="10"  width=60%><tr><th width="40%">Attribute</th><th>Value</th></tr>';
        foreach ($attArr as $att) {
            $AttText = get_attribute($entry, trim($att));
            if (!empty($AttText)) {
                $attTable .= '<tr>';
                foreach ($AttText as $attDetail) {
                    $attTable .= '<td>' . $attDetail['attribute'] . '</td>' .
                        '<td style="text-align:center">' . $attDetail['value'] . '</td>';
                }
                $attTable .= '</tr>';
            }
        }
        $attTable  .= '</table>';
        //full merge tag for replace
        $mergeTag = substr($text, $startPos, $closeBracketPos - $startPos + 1);
        $text = str_replace($mergeTag, $attTable, $text);
    }

    //attention field
    if (strpos($text, '{CONF_COMMENT}') !== false) {
        $sql = "SELECT comment "
            . " from wp_rmt_entry_attn,wp_rmt_attn"
            . " where entry_id = " . $entry_id
            . " and wp_rmt_attn.ID = attn_id"
            . " and token = 'CONF_COMMENT'";
        $attnText = $wpdb->get_var($sql);
        $text = str_replace('{CONF_COMMENT}', $attnText, $text);
    }

    //Confirmation Button
    if (strpos($text, '{CONF_BUTTON}') !== false) {
        $suppToken  = (isset($entry['fg_easypassthrough_token']) ? $entry['fg_easypassthrough_token'] : '');
        $confButton = '<a href="https://makerfaire.com/query/?type=entry&token=' . $suppToken . '">' .
            ' <button style="border-radius:2px;border: solid 1px #eb002a;background:#eb002a;color:#fff;padding:0px 15px;height:30px;font-weight:500;cursor:pointer;">Yes, I\'ll be there!</button>' .
            '</a>';
        $text = str_replace('{CONF_BUTTON}', $confButton, $text);
    }
    //resource lock indicator
    if (strpos($text, '{rmt_res_cat_lock') !== false) {
        $startPos        = strpos($text, '{rmt_res_cat_lock'); //pos of start of merge tag
        $RmtStartPos     = strpos($text, ':', $startPos);   //pos of start RMT field ID
        $closeBracketPos = strpos($text, '}', $startPos);  //find the closing bracket of the merge tag

        //resource ID
        $RMTcatID = substr($text, $RmtStartPos + 1, $closeBracketPos - $RmtStartPos - 1);

        //is this a valid RMT field??
        if (is_numeric($RMTcatID)) {
            //find locked value of RMT field
            $lockCount = $wpdb->get_var('SELECT count(*) as count
        FROM `wp_rmt_entry_resources`
        left outer join wp_rmt_resources
            on wp_rmt_entry_resources.resource_id = wp_rmt_resources.id
        where wp_rmt_resources.resource_category_id = ' . $RMTcatID . ' and lockBit=1 and entry_id = ' . $entry_id);
            $mergeTag = substr($text, $startPos, $closeBracketPos - $startPos + 1);
            $text = str_replace($mergeTag, ($lockCount > 0 ? 'Yes' : 'No'), $text);
        }
    }


    //attribute lock indicator
    if (strpos($text, '{rmt_att_lock') !== false) {
        $startPos        = strpos($text, '{rmt_att_lock'); //pos of start of merge tag
        $RmtStartPos     = strpos($text, ':', $startPos);   //pos of start RMT field ID
        $closeBracketPos = strpos($text, '}', $startPos);  //find the closing bracket of the merge tag

        //attribute ID
        $RMTid = substr($text, $RmtStartPos + 1, $closeBracketPos - $RmtStartPos - 1);

        //is this a valid RMT field??
        if (is_numeric($RMTid)) {
            //find locked value of RMT field
            $lockBit = $wpdb->get_var('SELECT lockBit FROM `wp_rmt_entry_attributes` where attribute_id = ' . $RMTid . ' and entry_id = ' . $entry_id . ' limit 1');
            $mergeTag = substr($text, $startPos, $closeBracketPos - $startPos + 1);
            $text = str_replace($mergeTag, ($lockBit == 1 ? 'Yes' : 'No'), $text);
        }
    }

    //Supplemental Form Token
    if (strpos($text, '{supp_form_token') !== false) {
        //pull form information for this entry
        $form = GFAPI::get_form($entry['form_id']);

        //If this form was supposed to create a master entry, we need to pull the supplemental form token from the master entry
        if (isset($form['master_form_id']) && $form['master_form_id'] != '') {
            $master_entry_id = $wpdb->get_var('SELECT meta_value FROM wp_gf_entry_meta where entry_id=' . $entry_id . ' and meta_key="master_entry_id" limit 1');

            if ($master_entry_id == '') {
                error_log('master_entry_id is blank for entry id ') . $entry_id;
                return $text;
            }
            $entry_id = $master_entry_id;
        }

        if ($entry_id != '') {
            $mf_supplemental_token = $wpdb->get_var('SELECT meta_value FROM wp_gf_entry_meta where entry_id=' . $entry_id . ' and meta_key="fg_easypassthrough_token" limit 1');
            if ($mf_supplemental_token != '') {
                $text = str_replace('{supp_form_token}', $mf_supplemental_token, $text);
            }
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
        $lead = GFAPI::get_entry($lead_id);
        $schedule = get_schedule($lead);

        $field_content = str_replace('{entry_schedule}', $schedule, $field_content);
    }

    return $field_content;
}
/** End MF custom merge tags **/

/* Return value and attribute of selected attribute per entry if set */
function get_attribute($lead, $attID) {
    global $wpdb;
    $return = array();
    $entry_id = (isset($lead['id']) ? $lead['id'] : '');

    if (!is_numeric($attID) || !is_numeric($entry_id)) {
        return $return;
    }

    if ($entry_id != '' && $attID != '') {
        //gather resource data
        $sql = "SELECT value,"
            . " (select category from wp_rmt_entry_att_categories where wp_rmt_entry_att_categories.ID = attribute_id) as attribute "
            . " FROM `wp_rmt_entry_attributes`  "
            . " where entry_id = " . $entry_id . " and attribute_id = " . $attID . " order by attribute ASC, value ASC";
        $results = $wpdb->get_results($sql);
        foreach ($results as $result) {
            $return[] = array('attribute' => $result->attribute, 'value' => $result->value);
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
function get_mf_resources($lead, $excRes = '', $incRes = '') {
    global $wpdb;
    $return = array();
    $entry_id = (isset($lead['id']) ? $lead['id'] : '');

    if ($entry_id != '') {
        $excSQL = ($excRes != '' ? " and wp_rmt_resource_categories.ID not in($excRes) " : '');
        $incSQL = ($incRes != '' ? " and wp_rmt_resource_categories.ID in($incRes) " : '');

        //gather resource data
        $sql = "SELECT er.qty, type, wp_rmt_resource_categories.category as item "
            . "FROM `wp_rmt_entry_resources` er, wp_rmt_resources, wp_rmt_resource_categories "
            . "where er.resource_id = wp_rmt_resources.ID "
            . "and resource_category_id = wp_rmt_resource_categories.ID  "
            . $excSQL . $incSQL
            . "and er.entry_id = " . $entry_id . " order by item ASC, type ASC";

        $results = $wpdb->get_results($sql);
        foreach ($results as $result) {
            $return[] = array('resource' => $result->item . ' - ' . $result->type, 'qty' => $result->qty);
        }
    }

    return $return;
}

/* Return schedule for lead */
function get_schedule($lead, $locsOnly = false) {
    global $wpdb;
    $schedule = '';
    $entry_id = (isset($lead['id']) ? $lead['id'] : '');

    if ($entry_id != '') {
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
        if ($wpdb->num_rows > 0) {
            foreach ($results as $row) {
                $subarea = ($row->nicename != '' && $row->nicename != '' ? $row->nicename : $row->subarea);
                $start_dt = strtotime($row->start_dt);
                $end_dt = strtotime($row->end_dt);
                if ($locsOnly) {
                    $schedule .= ($schedule != '' ? ',' : '') . $subarea;
                } else {
                    $schedule .= $row->area . ' - ' . $subarea;
                    $schedule .= '<br/>';
                    $schedule .= '<span>' . date("l, n/j/y, g:i A", $start_dt) . ' to ' . date("l, n/j/y, g:i A", $end_dt) . '</span><br/>';
                }
            }
        }
    }
    return $schedule;
}

/* Return area/subarea for entry */
function get_location($entry, $type = 'full') {
    global $wpdb;
    $location = '';
    $entry_id = (isset($entry['id']) ? $entry['id'] : '');

    if ($entry_id != '') {
        //get scheduling information for this entry
        $sql = "SELECT  area.area, subarea.subarea, subarea.nicename, location
              FROM    wp_mf_location location,
                      wp_mf_faire_subarea subarea,
                      wp_mf_faire_area area

              where       location.entry_id   = $entry_id
                      and subarea.id          = location.subarea_id
                      and area.id             = subarea.area_id";

        $results = $wpdb->get_results($sql);
        if ($wpdb->num_rows > 0) {
            foreach ($results as $row) {
                //if there are multiple locations separate with a space
                $location .= ($location != '' ? ',' : '');

                //set subarea text to nicename if set
                $subarea = ($row->nicename != '' && $row->nicename != '' ? $row->nicename : $row->subarea);

                //set response based on type
                if ($type == 'subarea') {
                    $location .= $subarea;
                } elseif ($type == 'area') {
                    $location .= $row->area;
                } elseif ($type == 'booth') {
                    $location .= ucfirst($row->location);
                } else {
                    if ($row->location != '') {
                        $location .= ucfirst($row->location) . ' - ' . $row->area . ' : ' . $subarea;
                    }
                }
            }
        }
    }
    return $location;
}


/* Return exposure for entry */
function get_exposure($entry) {
    global $wpdb;
    $exposure = '';
    $entry_id = (isset($entry['id']) ? $entry['id'] : '');

    if ($entry_id != '') {
        $sql = "SELECT group_concat(subarea.exposure separator ', ') as exposure
              FROM    wp_mf_location location,
                      wp_mf_faire_subarea subarea
              WHERE   location.entry_id   = $entry_id
                  and subarea.id          = location.subarea_id
                  and subarea.exposure    != ''
              GROUP BY entry_id";

        $results = $wpdb->get_row($sql);

        if ($wpdb->num_rows > 0) {
            foreach ($results as $key => $value) {
                // either get the exposure, or leave it blank
                $exposure .= $key == 'exposure' ? $value : "";
            }
        }
    }
    return $exposure;
}
