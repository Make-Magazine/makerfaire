<?php
// set up database
$root = $_SERVER['DOCUMENT_ROOT'];
require_once ($root . '/wp-config.php');
require_once ($root . '/wp-includes/wp-db.php');
require_once ($root . '/wp-content/themes/makerfaire/classes/ICS.php');

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=invite.ics');

//check for any filters
$formIDs        = (isset($_POST['forms2use']) ? $_POST['forms2use']:'');
$filter_day     = (isset($_POST['filter_day']) ? strtolower(sanitize_title_for_query($_POST['filter_day'])) : '');
$filter_type    = (isset($_POST['filter_type']) ? strtolower($_POST['filter_type']) : '');
$filter_topic   = (isset($_POST['filter_topic']) ? $_POST['filter_topic'] : '');
$filter_stage   = (isset($_POST['filter_stage']) ? strtolower(urldecode($_POST['filter_stage'])) : '');
$filter_text    = (isset($_POST['filter_text']) ? urldecode($_POST['filter_text']) : '');


$schedules = getSchedule($formIDs);
$parent_slug = 'bay-area';
if (strtolower($parent_slug) === 'bay-area') {
    $location = 'San Mateo County Event Center 1346 Saratoga Dr, San Mateo, CA 94403';
} elseif (strtolower($parent_slug) === 'new-york') {
    $location = 'New York Hall of Science 47-01 111th St, Corona, NY 11368';
}

$ics = array();
$str = '';

foreach ($schedules['schedule'] as $schedule) {    
    if (isset($schedule['time_start'])) {
        //set start and end date/times
        $dt = new DateTime($schedule['time_start']);
        $start = $dt->format('Ymd\THis');
        $dt = new DateTime($schedule['time_end']);
        $end = $dt->format('Ymd\THis');
                               
        //check day filter
        if($filter_day!==''){
            $dow = strtolower($dt->format('l'));
            if($filter_day!=$dow){
                continue; //skip this record
            }
        }
        
        //check category filter
        if($filter_topic!=''){
            $catList = explode(',',$schedule['category']);

            if(!in_array($filter_topic,$catList)){
                continue;
            }
        }
        
        //check stage filter
        if($filter_stage!=''){
            if(strtolower($schedule['nicename'])!=$filter_stage){
                continue;
            }
        }
        
        //check schedule type filter
        if ($filter_type != '') {            
            if ($filter_type != strtolower($schedule['type'])) {
                continue;
            }
        }
        
        //check freeform text search filter
        if($filter_text != ''){
            //if the filter text is found in the exhibit name, sponsor or presenter names, categories, or stage names
            // display the event. else skip it                     
            $pos1 = stripos($schedule['maker_list'], $filter_text);
            $pos2 = stripos($schedule['name'], $filter_text);
            $pos3 = stripos($schedule['category'], $filter_text);
            $pos4 = stripos($schedule['nicename'], $filter_text);
                                    
            if ($pos1 === false && $pos2 === false && $pos3 === false && $pos4 === false) {            
                continue; // we could not find the filter text
            }
        }
        
        $ics[] = array('location' => $location . ' - ' . $schedule['nicename'],
            'summary' => $schedule['name'],
            'dtstart' => $start,
            'dtend' => $end,
            'description' => $schedule['desc'],          
            'url' => "https://makerfaire.com/maker/entry/" . $schedule['id']);
    }
}


$event = new ICS(array('location' => 'MakerFaire'));
$output = $event->buildCal($ics);
echo $output;

