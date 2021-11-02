<?php

isset($_REQUEST['usegrouptoken']) ? $usegrouptoken = $_REQUEST['usegrouptoken'] : $usegrouptoken = false;
isset($_REQUEST['useowntoken']) ? $useowntoken = $_REQUEST['useowntoken'] : $useowntoken = false;

include('connect.php');

//Get Post ID
$post_id = $_REQUEST['id'];

//Get the JSON
if( isset($_REQUEST['albumsonly']) ){
	$json_object = cff_fetchUrl('https://graph.facebook.com/'.$post_id.'/photos?fields=source,images,name,width,height&access_token='. $access_token .'&limit=100');
} else {
	$json_object = cff_fetchUrl("https://graph.facebook.com/v3.2/" . $post_id . "?fields=attachments{subattachments.limit(100)}&access_token=" . $access_token);
}

//echo the JSON data as a string to the browser to then be converted to a JSON object in the JS file
echo $json_object;