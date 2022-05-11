<?php
#use CFF_Utils;

isset($_REQUEST['pageid']) ? $pageid = $_REQUEST['pageid'] : $pageid = '';
include_once CFF_PLUGIN_DIR . 'inc/SB_Facebook_Data_Encryption.php';

//Use the token from the shortcode
$shortcode_token = false;
if( isset($_REQUEST['at']) ){

    $at = $_REQUEST['at'];
    // $shortcode_token = $at;
	$encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();

	if ( ! empty( $at ) && $encryption->decrypt( $at ) ) {
		$at = $encryption->decrypt( $at );
	}

    if (strpos($at, '02Sb981f26534g75h091287a46p5l63') !== false) {
        $at = str_replace("02Sb981f26534g75h091287a46p5l63","",$at);
    }
    $shortcode_token = $at;

    // if( strpos($at, ':') !== false ){
    //     $shortcode_token = cffDecodeToken($at,$pageid);
    // }
}

function cffDecodeToken($at,$pageid){
    $access_token_pieces = explode(",", $at);
    $access_token_multiple = array();
    $shortcode_token = '';

    foreach ( $access_token_pieces as $at_piece ) {
        $access_token_split = explode(":", $at_piece);
        $token_only = trim($access_token_split[1]);
        $page_id_only = str_replace("%20","",$access_token_split[0]);

        //Find the token which matches the Page ID passed in
        if( $page_id_only == $pageid ){
	        $encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();

	        if ( ! empty( $token_only ) && $encryption->decrypt( $token_only ) ) {
		        $token_only = $encryption->decrypt( $token_only );
	        }
            if (strpos($token_only, '02Sb981f26534g75h091287a46p5l63') !== false) {
                $token_only = str_replace("02Sb981f26534g75h091287a46p5l63","",$token_only);
            }
            $shortcode_token = $token_only;
        }
    }
    return $shortcode_token;
}



//If displaying albums from a group then get the User Access Token from the DB
$token_from_db = false;
$cff_connected_accounts = false;
if( ( (isset($usegrouptoken) && $usegrouptoken != false) || $useowntoken ) && !$shortcode_token ){
	if ( ! function_exists( 'cff_get_wp_config_path' ) ) {

		function cff_get_wp_config_path() {
			$base = dirname( __FILE__ );
			$path = false;
			if ( @file_exists( dirname( dirname( $base ) ) . "/wp-config.php" ) ) {
				$path = dirname( dirname( $base ) ) . "/wp-config.php";
			} else if ( @file_exists( dirname( dirname( dirname( $base ) ) ) . "/wp-config.php" ) ) {
				$path = dirname( dirname( dirname( $base ) ) ) . "/wp-config.php";
			} else {
				$path = false;
			}
			if ( $path != false ) {
				$path = str_replace( "\\", "/", $path );
			}

			return $path;
		}
	}

    $config_path = cff_get_wp_config_path();
    $check_path = realpath($config_path);
    if($check_path && $config_path){
    	if ( ! defined( 'SHORTINIT' ) ) {
		    define( 'SHORTINIT', true );
	    }
        require_once( $config_path );

	    $sources_table_name = $wpdb->prefix . 'cff_sources';

	    if ( isset( $pageid ) ) {
		    $sql = $wpdb->prepare( "
			SELECT * FROM $sources_table_name
			WHERE account_id = %s;
		 ", $pageid );

	        $results = $wpdb->get_results( $sql, ARRAY_A );

		    if ( isset( $results[0]['access_token'] ) ) {
			    $encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();

			    if ( ! empty( $results[0]['access_token'] ) && $encryption->decrypt( $results[0]['access_token'] ) ) {
				    $results[0]['access_token'] = $encryption->decrypt( $results[0]['access_token'] );
			    }
			    $db_query_access_token = $results[0]['access_token'];
		    }
	    }



    }

}

//Set the kind of token to use
$access_token = '';

if( $shortcode_token ){
    $access_token = $shortcode_token;
} else {

    //If not using a token directly in the shortcode then next check for one in connected accounts
    if( ! empty( $db_query_access_token ) ){
        //Get from connected account
		$access_token = $db_query_access_token;
    }

    //If nothing in connected accounts then use main token from settings
    if( $token_from_db && ( $access_token == '' || is_null($access_token) ) ){
        $access_token = $token_from_db;

        if ( strpos($access_token, ':') !== false ) {

            //Define the array
            $access_token_multiple = array();

            function splitToken($at_piece, $access_token_multiple=false){
                $access_token_split = explode(":", $at_piece);

                ( count($access_token_split) > 1 ) ? $token_only = trim($access_token_split[1]) : $token_only = '';

                if (strpos($token_only, '02Sb981f26534g75h091287a46p5l63') !== false) {
                    $token_only = str_replace("02Sb981f26534g75h091287a46p5l63","",$token_only);
                }

                $access_token_multiple[ trim($access_token_split[0]) ] = $token_only;
                return $access_token_multiple;
            }

            //If there are multiple tokens then split them up
            if( strpos($access_token, ',') !== false ){
                $access_token_pieces = explode(",", $access_token);
                foreach ( $access_token_pieces as $at_piece ) {
                    $access_token_multiple = splitToken($at_piece, $access_token_multiple);
                }
            } else {
            //Otherwise just create a 1 item array
                $access_token_multiple = splitToken($access_token);
            }
            //Assign the tokens
            $access_token = $access_token_multiple;


            //Check to see if there's a token for this ID and if so then use it
            if( isset($access_token_multiple[$page_id]) ) $access_token = $access_token_multiple[$page_id];

            //If it's an array then that means there's no token assigned to this Page ID, so get the first token from the array and use that for this ID
            if( is_array($access_token) ){

                //Check whether the first item in the array is a single access token with no ID assigned
                foreach ($access_token as $key => $value) {
                    break;
                }
                if( strlen($key) > 50 ){
                    $access_token = $key;

                //If it's not a single access token and it has the ID:token format then use the token from that first item
                } else {
                    $access_token = reset($access_token);
                }
            }

        } else {
            //Replace the encryption string in the Access Token
            if (strpos($access_token, '02Sb981f26534g75h091287a46p5l63') !== false) {
                $access_token = str_replace("02Sb981f26534g75h091287a46p5l63","",$access_token);
            }
        }

    }

}

if ( ! function_exists( 'cff_fetchUrl' ) ) {
	function cff_fetchUrl($url){
		$args = array(
			'timeout' => 60
		);
		$response = wp_remote_get( $url, $args );
		if( is_wp_error( $response ) ) {
			//Don't display an error, just use the Server config Error Reference message
			return '';
		} else {
			$feedData = wp_remote_retrieve_body($response);
		}

		return $feedData;
	}
}

?>
