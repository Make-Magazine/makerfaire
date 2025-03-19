<?php
include 'db_connect.php';
$mysqli->select_db('mf_events');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('NETWORK_HOME_URL',network_home_url());


/*
$oAuthToken = OAUTH_TOKEN; //BA and NY
$orgID = 2581145421;
*/

$oAuthToken = GOAUTH_TOKEN; //global
$orgID    = GEB_ORG; //global mf
$headers = array("authorization: Bearer ".$oAuthToken); 

$pageNum = (isset($_GET['pagenum'])?$_GET['pagenum']:1);
$offset = (isset($_GET['offset'])?$_GET['offset']:0); //last run for global was 120

/*
//build event db
$url = "https://www.eventbriteapi.com/v3/organizations/".$orgID."/events/?page=".$pageNum;
$headers = array("authorization: Bearer ".$oAuthToken);
$EBdata = postCurl($url, $headers, NULL,"GET");
$EBdata = json_decode($EBdata);

$page_count = $EBdata->pagination->page_count;
$curr_page = $EBdata->pagination->page_number;;

echo 'Processed page '. $curr_page .' of '. $page_count.'<br/>';
foreach($EBdata->events as $event){
    $name = str_replace('"', '', $event->name->text ?? '');
    // add to the eb_events table
    $sql = "INSERT INTO `eb_events`(`org_id`, `event_id`, `event_name`, `event_status`, `event_uri`) 
        VALUES (".$orgID.",".$event->id.",\"".$name."\",\"".$event->status."\",\"".$event->resource_uri."\")";
    
    $result=mysqli_query($mysqli,$sql) or die("error in SQL ".mysqli_error($mysqli).' '.$sql);            
}                
*/

/*
for global, only process attendees to these events:
    https://eastbay.makerfaire.com/
    https://sanjose.makerfaire.com/
    https://santacruz.makerfaire.com/
    https://benecia.makerfaire.com/
    https://solanocounty.makerfaire.com/
    https://gilroy.makerfaire.com/
*/

//$sql = "select * from eb_events where event_status <> 'draft' and org_id=".$orgID." and event_id=31971408343";
$sql = "select * from eb_events where event_status <> 'draft' and org_id=".$orgID." and 
(event_name like '%east bay%' or event_name like '%san jose%' or event_name like '%santa cruz%') limit 10 offset ".$offset;
$eventRes=mysqli_query($mysqli,$sql) or die("error in SQL ".mysqli_error($mysqli).' '.$sql);            
foreach($eventRes as $event){
    $eventID = $event['event_id'];
    //build attendee data
    echo 'Pulling Attendee Data for event '.$eventID.' - '.$event['event_name'].'<br/>';
    $pageNum = 1;
    $page_count = eb_attendee($eventID,$pageNum);
    
    while($pageNum<$page_count){
        $pageNum = $pageNum + 1;
        echo 'more to process. $page_count is '.$page_count.'. current page is '.$pageNum.'<br/>';
        $page_count = eb_attendee($eventID,$pageNum);
    }
}

function eb_attendee($eventID,$pageNum) {
    global $headers; global $mysqli;
    $url = "https://www.eventbriteapi.com/v3/events/".$eventID. "/attendees/?page=".$pageNum;
    $EBdata = postCurl($url, $headers, NULL,"GET");
    $EBdata = json_decode($EBdata);

    if(!isset($EBdata->pagination)){
        echo 'error in EventBrite call<br/>';
        var_dump($EBdata);
        die();
    }
    $page_count = $EBdata->pagination->page_count;
    $curr_page = $EBdata->pagination->page_number;;
    echo 'Processed page '. $curr_page .' of '. $page_count.'<br/>';
    foreach($EBdata->attendees as $attendee){
        $name = str_replace('"', '', $attendee->profile->name ?? '');
        $ticket_class_name = str_replace('"', '', $attendee->ticket_class_name ?? '');
        $sql = "INSERT INTO `eb_attendees`(`event_id`, `name`, `email`, `ticket_class_name`, `status`, `created`) VALUES (\"".$attendee->event_id."\",
        \"".$name."\",
        \"".(isset($attendee->profile->email)?$attendee->profile->email:'')."\",
        \"".$ticket_class_name."\", 
        \"".$attendee->status."\",
        \"".$attendee->created."\")";
        $result=mysqli_query($mysqli,$sql) or die("error in SQL ".mysqli_error($mysqli).' '.$sql);            
    }

    return $page_count;
}
function postCurl($url, $headers = null, $datastring = null,$type="POST") {
	$ch = curl_init($url);

	if (strpos(NETWORK_HOME_URL, '.local') > -1  || strpos(NETWORK_HOME_URL, '.test') > -1) { // wpengine local environments
	  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}

  //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
  //curl_setopt($ch, CURLOPT_STDERR, $verbose = fopen('php://temp', 'rw+'));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

	if($datastring != null) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
	}

	if ($headers != null) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}

	$response = curl_exec($ch);

  //echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";
	if(curl_errno($ch)){
	  throw new Exception(curl_error($ch));
	}

	curl_close($ch);
  return $response;
}
