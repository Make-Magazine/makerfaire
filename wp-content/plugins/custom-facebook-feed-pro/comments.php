<?php

isset($_REQUEST['usegrouptoken']) ? $usegrouptoken = $_REQUEST['usegrouptoken'] : $usegrouptoken = false;
isset($_REQUEST['useowntoken']) ? $useowntoken = $_REQUEST['useowntoken'] : $useowntoken = false;

include('connect.php');

//Get Post ID
$comment_id = $_REQUEST['id'];

$json_object = cff_fetchUrl("https://graph.facebook.com/" . $comment_id . "/?fields=comments{created_time,from{name,id,picture{url},link},id,message,message_tags,attachment,like_count}&access_token=" . $access_token);

//Encode comment text
$json_object = json_decode($json_object);
$c_3 = 0;
if( isset($json_object->comments->data) ){
	$c_3 = 0;
	foreach($json_object->comments->data as $comment ) {
		$json_object->comments->data[$c_3]->message = htmlentities($comment->message, ENT_QUOTES, 'UTF-8');
		$c_3++;
	}
}
$json_object = json_encode($json_object);

//If no data returned then return
if( empty($json_object) || $json_object == '' ) echo '';

echo $json_object;