<?php
// Template Name: Signage
//get the URL variables
if(!is_user_logged_in()) {
	die('You do not have permission to access this page.');
}
$location = ( isset($_GET['loc']) ? intval($_GET['loc']) : '' );
$faire = (isset($_GET['faire']) ? $_GET['faire'] : 'ny15');
$short_description = (!isset($_GET['description']) ? true : false);
$orderBy = (isset($_GET['orderBy']) ? $_GET['orderBy'] : '' );
$day = (isset($_GET['day']) ? sanitize_title_for_query($_GET['day']) : '');
$qr = (isset($_GET['qr']) ? $_GET['qr'] : '');
$filter_type = (isset($_GET['type']) ? strtolower($_GET['type']) : '');
$filter_topic = (isset($_GET['topic']) ? $_GET['topic'] : '');
$filter_stage = (isset($_GET['stage']) ? urldecode($_GET['stage']) : '');
$filter_text = (isset($_GET['text']) ? urldecode($_GET['text']) : '');

if (!empty($location))
    $term = get_term_by('name', $location, 'location');

$schedList = get_schedule_list($location, $short_description, $day, $faire);
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Stage Signage - <?php echo sanitize_title($location); ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <style>
            body { font-family: 'Benton Sans', Helvetica, sans-serif; }
            a { text-decoration:none; color:#000; }
            h1, h2, h3, h4 { margin:5px 0 0; }
            .sumome-react-wysiwyg-popup-container, #wpadminbar { display: none !important; } 
            .qr-code-print {
                position:fixed;
                z-index:999;
                bottom:0px;
                right:0px;
                display:block;
                width:100px;
            }
        </style>
    </head>
    <body>
        <?php
        if ($filter_type != '' || $filter_topic != '' || $filter_stage != '' || $filter_text != '') {
            echo '<h2>Filtered for:</h2><br/>';
            if ($filter_type != '')
                echo 'Type: ' . ucwords($filter_type) . '<br/>';
            if ($filter_topic != '')
                echo 'Topic: ' . ucwords($filter_topic) . '<br/>';
            if ($filter_stage != '')
                echo 'Stage: ' . $filter_stage . '<br/>';
            if ($filter_text != '' && $filter_text != 'undefined')
                echo 'Text: '. ucwords($filter_text) . '<br/>';
        }
        echo $schedList;
        if ($qr != '') {
            ?> <img src="/wp-content/themes/makerfaire/img/qrcode-schedule.png" class="qr-code-print" /> <?php } ?>
    </body>
</html>
<?php

/**
 * Get our schedule stuff
 * @param  String $location [description]
 * @return [type]           [description]
 */
function get_schedule_list($location, $short_description = false, $day_set = '', $faire = 'ny15') {
    global $orderBy;
    global $wpdb;
    global $filter_type;
    global $filter_topic;
    global $filter_stage;
    global $filter_text;
    $output = '';
    //retrieve Data
    $sql = "SELECT  DAYNAME(schedule.start_dt) as Day,
                    DATE_FORMAT(schedule.start_dt,'%l:%i %p') as 'Start Time',
                    DATE_FORMAT(schedule.end_dt,'%l:%i %p') as 'End Time',
                    if(subarea.niceName = '' or subarea.niceName is null,subarea.subarea,subarea.niceName) as nicename,
                       area.area, entity.presentation_title as 'Exhibit',

                    (select  group_concat( distinct concat(maker.`FIRST NAME`,' ',maker.`LAST NAME`) separator ', ') as Makers
                                    from    wp_mf_maker maker,
                                            wp_mf_maker_to_entity maker_to_entity
                                    where   schedule.entry_id           = maker_to_entity.entity_id  AND
                                            maker_to_entity.maker_id    = maker.maker_id AND
                                            maker_to_entity.maker_type != 'Contact'
                                    group by maker_to_entity.entity_id) as Presenters, 
                    type, entity.form_id,
                    (select group_concat( meta_value separator ',') as cat from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = schedule.entry_id AND (wp_gf_entry_meta.meta_key like '%320%' OR wp_gf_entry_meta.meta_key like '%321%')) as category 
            FROM    wp_mf_schedule schedule
            JOIN    wp_mf_entity entity on schedule.entry_id   = entity.lead_id AND entity.status       = 'Accepted'
            JOIN    wp_mf_location location on schedule.location_id  = location.ID AND schedule.entry_id = location.entry_id
            JOIN    wp_mf_faire_subarea subarea on location.subarea_id = subarea.id
            JOIN    wp_mf_faire_area area on subarea.area_id = area.id

            WHERE   schedule.faire          = '" . $faire . "'"
            . ($day_set != '' ? " and DAYNAME(`schedule`.`start_dt`)='" . ucfirst($day_set) . "'" : '')
            . ($filter_stage != '' ? " and lower(nicename)='" . $filter_stage . "'" : '');

    if ($orderBy == 'time') {
        $sql .= " order by schedule.start_dt ASC, schedule.end_dt ASC, nicename ASC, 'Exhibit' ASC";
    } else {
        $sql .= " order by nicename ASC, schedule.start_dt ASC, schedule.end_dt ASC,  'Exhibit' ASC";
    }

    //group by stage and date
    $dayOfWeek = '';
    $stage = '';

    foreach ($wpdb->get_results($sql, ARRAY_A) as $key => $row) {
        //filter by category?
        
        //get array of categories. set name based on category id
        $category = array();
        if(isset($row['category'])){
            $leadCategory = explode(',', $row['category']);
            foreach ($leadCategory as $leadCat) {
                $category[] = htmlspecialchars_decode(get_CPT_name($leadCat));
            }

            //check if this record is in the category specified
            if ($filter_topic != '' && !in_array($filter_topic, $category)) {
                continue;
            }                
        }

        $search_cats = implode(', ', $category);
        

        //filter by schedule type
        if ($filter_type != '') {
            $form = GFAPI::get_form($row['form_id']);
            $form_type = $form['form_type'];
            //set default values for schedule type if not set
            if ($row['type'] == '') {
                //demo, performance, talk, workshop
                if ($form_type == 'Performance') {
                    $sched_type = 'performance';
                } else {
                    $sched_type = 'talk';
                }
            } else {
                $sched_type = $row['type'];
            }
            
            if ($filter_type != $sched_type) {
                continue;
            }
        }
        
        //if text filter
        
        if($filter_text != '') {
            //if the filter text is found in the exhibit name, sponsor or presenter names, categories, or stage names
            // display the event. else skip it                     
            $pos1 = stripos($row['Exhibit'], $filter_text);
            $pos2 = stripos($row['Presenters'], $filter_text);
            $pos3 = stripos($search_cats, $filter_text);
            $pos4 = stripos($row['nicename'], $filter_text);
                                    
            if ($pos1 === false && $pos2 === false && $pos3 === false && $pos4 === false) {            
                continue; // we could not find the filter text
            }
            
        }
        if ($orderBy == 'time') { //break by stage. day goes in h1
            $stage = $row['nicename'];
            if ($dayOfWeek != $row['Day']) {
                //skip the page break after if this is the first time
                if ($dayOfWeek != '')
                    $output .= '<div style="page-break-after: always;"></div>';
                $dayOfWeek = $row['Day'];

                $output .= '<div style="clear:both;width:100%;height:32px;"><h1 style="font-size:2.2em; margin:31px 0 0; max-width:75%;float:left">' . $dayOfWeek . '</h1>
                                <h2 style="float:right;margin-top:31px;"><img src="/wp-content/uploads/2016/01/mf_logo.jpg" style="width:200px;" alt="" ></h2>
                                <p></p>
                                <p></p>
                                <p></p></div><br /><br /><br />';
            }
        } else {
            if ($stage != $row['nicename'] || $dayOfWeek != $row['Day']) {
                //skip the page break after if this is the first time
                if ($stage != '')
                    $output .= '<div style="page-break-after: always;"></div>';
                $stage = $row['nicename'];
                $dayOfWeek = $row['Day'];

                $output .= '<div style="clear:both;width:100%;height:32px;"><h1 style="font-size:2.2em; margin:31px 0 0; max-width:75%;float:left">' . $stage . ' <small>(' . $row['area'] . ')</small> </h1>
                                <h2 style="float:right;margin-top:31px;"><img src="/wp-content/uploads/2016/01/mf_logo.jpg" style="width:200px;" alt="" ></h2>
                                <p></p>
                                <p></p>
                                <p></p>';
                $output .= '<div style="clear:both"><h2>' . $dayOfWeek . '</h2></div></div><br /><br /><br />';
            }
        }

        $output .= '<table style="width:94%;">';

        $output .= '<tr>';
        $output .= '<td width="25%" style="padding:15px 0;" valign="top">';

        $output .= '<h2 style="font-size:.9em; color:#333; margin-top:3px;">' . $row['Start Time'] . ' &mdash; ' . $row['End Time'] . '</h2>';
        if ($orderBy == 'time') {
            $output .= $stage . ' (' . $row['area'] . ')';
        }
        $output .= '</td>';
        $output .= '<td>';
        $output .= '<h3 style="margin-top:0;">' . $row['Exhibit'] . '</h3>';
        $output .= $row['Presenters'];
        $output .= '<tr><td colspan="2"><div style="border-bottom:2px solid #ccc;"></div></td></tr>';
        $output .= '</td>';
        $output .= '</tr>';

        $output .= '</table>';
    }

    return $output;
}
