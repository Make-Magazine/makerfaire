<?php

isset($_REQUEST['usegrouptoken']) ? $usegrouptoken = $_REQUEST['usegrouptoken'] : $usegrouptoken = false;
isset($_REQUEST['useowntoken']) ? $useowntoken = $_REQUEST['useowntoken'] : $useowntoken = false;

include('connect.php');

if( isset( $_REQUEST['pageid'] ) && isset($_REQUEST['id'])){
	//Get Post ID
	$post_id = $_REQUEST['id'];
	$source_id = $_REQUEST['pageid'];
	$encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();

	if ( ! empty( $access_token ) && $encryption->decrypt( $access_token ) ) {
		$access_token = $encryption->decrypt( $access_token );
	}else{
		$source_info = \CustomFacebookFeed\Builder\CFF_Source::get_source_info( $source_id );
		if( $source_info !== false){
			$access_token = $encryption->maybe_decrypt( $source_info['access_token'] );
		}
	}


	//Get the JSON
	if( isset($_REQUEST['albumsonly']) ){
		$json_object = \CustomFacebookFeed\CFF_Utils::cff_fetchUrl('https://graph.facebook.com/'.$post_id.'/photos?fields=source,images,name,width,height&access_token='. $access_token .'&limit=100');
	} else {
		$json_object = \CustomFacebookFeed\CFF_Utils::cff_fetchUrl("https://graph.facebook.com/v3.2/" . $post_id . "?fields=attachments{subattachments.limit(100)}&access_token=" . $access_token);
	}
}


//echo the JSON data as a string to the browser to then be converted to a JSON object in the JS file
echo $json_object;