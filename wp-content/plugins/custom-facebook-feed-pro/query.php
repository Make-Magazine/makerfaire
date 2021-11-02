<?php

isset($_REQUEST['usegrouptoken']) ? $usegrouptoken = $_REQUEST['usegrouptoken'] : $usegrouptoken = false;
isset($_REQUEST['useowntoken']) ? $useowntoken = $_REQUEST['useowntoken'] : $useowntoken = false;


include trailingslashit( CFF_PLUGIN_DIR ) . 'connect.php';

//Use either object ID or post ID
( $_REQUEST['use_id'] == 'object' ) ? $use_object_id = true : $use_object_id = false;
( isset($_REQUEST['o_id']) ) ? $object_id = $_REQUEST['o_id'] : $object_id = '';
( isset($_REQUEST['post_id']) ) ? $post_id = $_REQUEST['post_id'] : $post_id = '';

//Check whether it's a timeline album/video as they require different rules
( isset($_REQUEST['timelinealbum']) ) ? $timelinealbum = $_REQUEST['timelinealbum'] : $timelinealbum = false;
( isset($_REQUEST['isvideo']) ) ? $isvideo = $_REQUEST['isvideo'] : $isvideo = false;

//If it's a group then just use the post ID
if( $usegrouptoken || $isvideo ) $use_object_id = false;

( $use_object_id ) ? $id = $object_id : $id = $post_id;

//Check IDs to make sure they're numeric as they could be printed in an error message
check_id($object_id);
check_id($post_id);

function check_id($check_id){
	if (preg_match("#^[0-9_]+$#", $check_id) || empty($check_id) || $check_id == 'undefined' ) {
		//Contains numbers, underscores, is empty or undefined
	} else {
		exit;
	}
}

//How many comments does this post have?
( isset($_REQUEST['comments_num']) ) ? $comments_num = intval($_REQUEST['comments_num']) : $comments_num = 0;
( isset($_REQUEST['likes_num']) ) ? $likes_num = intval($_REQUEST['likes_num']) : $likes_num = 0;

//Which meta type should we query?
( isset($_REQUEST['type']) ) ? $metaType = $_REQUEST['type'] : $metaType = '';

//Make the API request to get the data from Facebook
function api_call($id, $likes, $reactions, $images, $access_token, $attachments){
	$json_object = cff_fetchUrl("https://graph.facebook.com/v3.2/" . $id . "/?fields=".$likes."comments.summary(true){id,from{id,name,picture{url},link},message,message_tags,created_time,like_count,comment_count,attachment{media}}".$reactions.$images.$attachments."&access_token=" . $access_token);

	return $json_object;
}


if( $metaType == 'meta' ){

	//If there's an object ID then request the full size images
	( $use_object_id ) ? $images = ",images" : $images = '';
	( $isvideo ) ? $attachments = ",attachments" : $attachments = '';

	$reactions = ",reactions.type(LOVE).summary(total_count).limit(0).as(love),reactions.type(WOW).summary(total_count).limit(0).as(wow),reactions.type(HAHA).summary(total_count).limit(0).as(haha),reactions.type(SAD).summary(total_count).limit(0).as(sad),reactions.type(ANGRY).summary(total_count).limit(0).as(angry)";

	$likes = "likes.summary(true).limit(3){id,name,link},";
	if($usegrouptoken) $likes = "reactions.summary(true).limit(3){id,name,link},"; //likes don't work for groups anymore so use reactions instead

	//If it's a timeline event then don't request reactions or images
	isset($_REQUEST['timeline_event']) ? $timeline_events = true : $timeline_events = false;
	if( $timeline_events ){
		$reactions = "";
		// $likes = ""; //If it's an event then use the post ID and then can comment this line out
		$images = "";
	}

	$json_object = api_call($id, $likes, $reactions, $images, $access_token, $attachments);

	//Encode comment text
	if( isset($json_object->comments->data) ){
		$c_1 = 0;
		foreach($json_object->comments->data as $comment ) {
			$json_object->comments->data[$c_1]->message = htmlentities($comment->message, ENT_QUOTES, 'UTF-8');
			$c_1++;
		}
	}

	//Convert to an object
	$json_object = json_decode($json_object);

	//Remove paging fields as we don't need them and they take up space
	if( isset( $json_object->likes->paging ) ) $json_object->likes->paging = '';
	if( isset( $json_object->comments->paging ) ) $json_object->comments->paging = '';


	//If no comments were returned for the object ID but there should be some then make another request using the Post ID instead to get the comments
	$first_response_json = $json_object;


	//If there's no comments/likes data available in the object ID request then make a second request using the Post ID to get the comments/likes
	//The likes check only applies to timeline album posts as they're the only ones that don't return the likes in the first request. Other post types do.
	if( ( $use_object_id && $comments_num > 0 && ( !isset($first_response_json->comments) || empty($first_response_json->comments->data) ) ) || ( $timelinealbum && ( $use_object_id && $likes_num > 0 ) ) ){
		$second_response = api_call($post_id, $likes, $reactions, '', $access_token, $attachments);

		//Add the images from the original response to the full comments/likes/reactions data in the second response and return
		$second_response_json = json_decode($second_response);

		//If it's a timeline album post then the "images" field doesn't exist in the second response so create it
		if( $timelinealbum && isset($first_response_json->images) ) $second_response_json->images = $first_response_json->images;

		//Encode comment text
		if( isset($second_response_json->comments->data) ){
			$c_2 = 0;
			foreach($second_response_json->comments->data as $comment ) {
				$second_response_json->comments->data[$c_2]->message = htmlentities($comment->message, ENT_QUOTES, 'UTF-8');
				$c_2++;
			}
		}

		if( isset($second_response_json->images) ) $second_response_json->images = $first_response_json->images;

		//Remove paging fields as we don't need them and they take up space
		if( isset( $second_response_json->likes->paging ) ) $second_response_json->likes->paging = '';
		if( isset( $second_response_json->comments->paging ) ) $second_response_json->comments->paging = '';

		$json_object = $second_response_json;

	}

	//Convert to a string so we can cache it
	$json_object = json_encode($json_object);

	//Remove any access token as it's not needed and is insecure to show it
	$access_token_encoded = str_replace('|', '\u00257C', $access_token); //The separator is encoded in some of the tokens in the text file
    $access_tokens_arr = array($access_token, $access_token_encoded);
    $json_object = str_replace($access_tokens_arr, "x_cff_hide_token_x", $json_object);

	echo $json_object;
	
}

?>