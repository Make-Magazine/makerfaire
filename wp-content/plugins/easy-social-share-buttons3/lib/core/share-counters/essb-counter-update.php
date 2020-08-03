<?php
/**
 * Counter Update Functions
 *
 * @package   EasySocialShareButtons
 * @author    AppsCreo
 * @link      http://appscreo.com/
 * @copyright 2017 AppsCreo
 * @since 4.2
 *
 */


/**
 * Execute update of share counters for all social networks used on site
 * 
 * @param number $post_id
 * @param string $url
 * @param string $full_url
 * @param array $networks
 * @param boolean $recover_mode
 * @param string $twitter_counter
 * @return array
 */
function essb_counter_update_simple($post_id, $url, $full_url, $networks = array(), $recover_mode = false, $twitter_counter = 'self') {
	
	$cached_counters = array();
	$cached_counters['total'] = 0;
	
	foreach ( $networks as $k ) {
		switch ($k) {
			case 'facebook' :
				$cached_counters [$k] = essb_get_facebook_count($url);
				break;
			case "facebook_like":
				if (essb_option_bool_value('facebook_likebtn_counter') && !in_array('facebook', $networks)) {
					$cached_counters ['facebook'] = essb_get_facebook_count($url);
				}
				break;
			case 'twitter' :
				if ($twitter_counter == 'api') {
					$cached_counters [$k] = 0;
				}
				else if ($twitter_counter == 'newsc') {
					$cached_counters [$k] = essb_get_tweets_newsc_count($full_url);
				}
				else if ($twitter_counter == 'twitcount') {
					$cached_counters [$k] = essb_get_tweets_twitcount_count($full_url);
				}
				else if ($twitter_counter == 'opensc') {
					$cached_counters [$k] = essb_get_tweets_opensc_count($full_url);
				}
				else {
					if ($twitter_counter == 'self') {
						if (!$recover_mode) {
							$cached_counters [$k] = essb_get_internal_count( $post_id, $k );
						}
						else {
							$cached_counters[$k] = 0;
						}
					}
				}
				break;
			case 'linkedin' :
				
					if (!$recover_mode) {
						$cached_counters [$k] = essb_get_internal_count( $post_id, $k );
					}
					else {
						$cached_counters[$k] = 0;
					}			
			
				break;
			case 'pinterest' :
				$cached_counters [$k] = essb_get_pinterest_count( $url );
				break;
			case 'google' :

					if (!$recover_mode) {
						$cached_counters [$k] = essb_get_internal_count( $post_id, $k );
					}
					else {
						$cached_counters[$k] = 0;
					}					
				break;
			case 'stumbleupon' :
				$cached_counters [$k] = essb_get_stumbleupon_count($url);
				break;
			case 'vk' :
				$cached_counters [$k] = essb_get_vkontake_count($url);
				break;
			case 'reddit' :
				$cached_counters [$k] = essb_get_reddit_count($url);
				break;
			case 'buffer' :
				$cached_counters [$k] = essb_get_buffer_count($url);
				break;
			case 'love' :
				if (!$recover_mode) {
					$cached_counters [$k] = essb_get_loves_count($post_id);
				}
				else {
					$cached_counters[$k] = 0;
				}
				break;
			case 'ok':
				$cached_counters [$k] = essb_get_odnoklassniki_count( $url );
				break;
			case 'mwp' :
				$cached_counters [$k] = 0;
				break;
			case 'xing' :
				$cached_counters [$k] = essb_get_xing_count($url);
				break;
			case 'comments' :
				if (!$recover_mode) {
					$cached_counters [$k] = essb_get_comments_count($post_id);
				}
				else {
					$cached_counters[$k] = 0;
				}
				break;
			case 'yummly' :
				$cached_counters [$k] = essb_get_yummly_count($url);
				break;
			case 'addthis' :
				// @since 7.0
				// According to customers addthis stores the internal counter no matter of 
				// the URL version. In the recovery there is no need to callback again counter 
				// update
				if (!$recover_mode) {
					$cached_counters [$k] = essb_get_addthis_count($url);
				}
				else {
					$cached_counters[$k] = 0;
				}
				break;				
			default:
				if (!$recover_mode) {
					$cached_counters [$k] = essb_get_internal_count($post_id, $k);
				}
				else {
					$cached_counters[$k] = 0;
				}
				break;
	
		}
			
		$cached_counters ['total'] += intval ( isset($cached_counters [$k]) ? $cached_counters [$k] : 0 );
	}
	
	return $cached_counters;
}

function essb_counter_request( $encUrl ) {

	$counter_curl_fix = essb_option_value('counter_curl_fix');

	$options = array(
			CURLOPT_RETURNTRANSFER	=> true, 	// return web page
			CURLOPT_HEADER 			=> false, 	// don't return headers
			CURLOPT_ENCODING	 	=> "", 		// handle all encodings
			CURLOPT_USERAGENT	 	=> isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'essb', 	// who am i
			CURLOPT_AUTOREFERER 	=> true, 	// set referer on redirect
			CURLOPT_CONNECTTIMEOUT 	=> 5, 		// timeout on connect
			CURLOPT_TIMEOUT 		=> 10, 		// timeout on response
			CURLOPT_MAXREDIRS 		=> 3, 		// stop after 3 redirects
			CURLOPT_SSL_VERIFYHOST 	=> 0,
			CURLOPT_SSL_VERIFYPEER 	=> false,
			CURLOPT_FAILONERROR => false,
			CURLOPT_NOSIGNAL => 1,
	);
	$ch = curl_init();

	if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
		$options[CURLOPT_FOLLOWLOCATION] = true;
	}

	$options[CURLOPT_URL] = $encUrl;
	curl_setopt_array($ch, $options);
	// force ip v4 - uncomment this
	try {
		if ($counter_curl_fix != 'true') {
			curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
	}
	catch (Exception $e) {

	}

		
	$content	= curl_exec( $ch );
	$err 		= curl_errno( $ch );
	$errmsg 	= curl_error( $ch );

	curl_close( $ch );

	if ($errmsg != '' || $err != '') {
	}
	return $content;
}

function essb_get_comments_count($post_id) {
	$comments_count = wp_count_comments($post_id);

	return $comments_count->approved;
}

function essb_get_xing_count($url) {
	$buttonURL = sprintf('https://www.xing-share.com/app/share?op=get_share_button;url=%s;counter=top;lang=en;type=iframe;hovercard_position=2;shape=rectangle', urlencode($url));
	$data  = essb_counter_request($buttonURL);
	$shares = array();

	$count = 0;
	preg_match( '/<span class="xing-count top">(.*?)<\/span>/s', $data, $shares );

	if (count($shares) > 0) {
		$current_result = $shares[1];

		$count = $current_result;
	}

	return $count;
}

function essb_get_pocket_count($url) {

	return 0;

}


function essb_get_loves_count($postID) {
	if (!is_numeric($postID)) {
		return 0;
	}

	$love_count = get_post_meta($postID, '_essb_love', true);

	if( !$love_count ){
		$love_count = 0;
		add_post_meta($postID, '_essb_love', $love_count, true);
	}

	return $love_count;
}

function essb_get_internal_count($postID, $service) {
	if (!is_numeric($postID)) {
		return -1;
	}

	$current_count = get_post_meta($postID, 'essb_pc_'.$service, true);

	return intval($current_count);;
}

function essb_get_google_count($url) {
	return 0;
}

/**
 * Google+ has removed their counter and button
 * @param unknown_type $url
 * @return number
 */
function essb_get_google_count_api($url) {
	return 0;
}

function essb_get_odnoklassniki_count( $url ) {
	$CHECK_URL_PREFIX = 'https://connect.ok.ru/dk?st.cmd=extLike&uid=odklcnt0&ref=';

	$check_url = $CHECK_URL_PREFIX . $url;
		
	$data   = essb_counter_request( $check_url );
	$shares = array();
	try {
	    /**
	     * Updating the API callback from
	     * preg_match( '/^ODKL\.updateCount\(\'odklcnt0\',\'(\d+)\'\);$/i', $data, $shares );
	     */
	    preg_match( '/^ODKL\.updateCount\(\'\',\'(\d+)\'\);$/i', $data, $shares );

		return (int)$shares[ 1 ];
	}
	catch (Exception $e) {
		return 0;
	}
}

function essb_get_vkontake_count( $url ) {
	$CHECK_URL_PREFIX = 'https://vk.com/share.php?act=count&url=';

	$check_url = $CHECK_URL_PREFIX . $url;

	$data   = essb_counter_request( $check_url );
	$shares = array();

	preg_match( '/^VK\.Share\.count\(\d, (\d+)\);$/i', $data, $shares );

	return $shares[ 1 ];
}

function essb_get_managewp_count($url = '') {
    return 0;
}

function _deprecated_essb_get_managewp_count($url) {
	$buttonURL = sprintf('https://managewp.org/share/frame/small?url=%s', urlencode($url));
	$data  = essb_counter_request($buttonURL);
	$shares = array();

	$count = 0;
	preg_match( '/<form(.*?)<\/form>/s', $data, $shares );

	if (count($shares) > 0) {
		$current_result = $shares[1];

		$second_parse = array();
		preg_match( '/<div>(.*?)<\/div>/s', $current_result, $second_parse );

		$value = $second_parse[1];
		$value = str_replace("<span>", "", $value);
		$value = str_replace("</span>", "", $value);

		$count = $value;
	}

	return $count;
}

function essb_get_reddit_count($url) {
	$reddit_url = 'https://www.reddit.com/api/info.json?url='.$url;
	$format = "json";
	$score = $ups = $downs = 0; //initialize

	//http://stackoverflow.com/questions/8963485/error-429-when-invoking-reddit-api-from-google-app-engine
	/* action */
	$content = essb_counter_request( $reddit_url );
	if($content) {
		if($format == 'json') {
			$json = json_decode($content,true);

			if (isset($json['data']) && isset($json['data']['children'])) {
				foreach($json['data']['children'] as $child) { // we want all children for this example
					$ups+= (int) $child['data']['ups'];
					$downs+= (int) $child['data']['downs'];
				}
				$score = $ups - $downs;
			}
		}
	}

	return $score;
}

function essb_get_facebook_count($url) {
	$api3 = true;
	$api2 = false;
	$api4 = false;
	$parse_url = 'https://graph.facebook.com/?id='.$url.'&fields=og_object{engagement}';

	$facebook_token = essb_option_value('facebook_counter_token');
	$sharedcount_token = essb_option_value('sharedcount_token');
	
	if (has_filter('essb4_facebook_token_randomizer')) {
		$facebook_token = apply_filters('essb4_facebook_token_randomizer', $facebook_token);
	}
	
	if ($facebook_token != '') {
		$parse_url = 'https://graph.facebook.com/?id='.$url.'&fields=og_object{engagement}&access_token=' . sanitize_text_field($facebook_token);
	}
	
	// Applying method API #2 only if token is also provided. Otherwise the method will not return any data
	if (essb_option_value('facebook_counter_api') == 'api2' && $facebook_token != '') {
		$parse_url = 'https://graph.facebook.com/?fields=engagement&id='.$url.'&access_token='.sanitize_text_field($facebook_token);
		$api2 = true;
		$api3 = false;
	}
	if (essb_option_value('facebook_counter_api') == 'sharedcount' && $sharedcount_token != '') {
		$api2 = false;
		$api3 = false;
		$api4 = true;
		$parse_url = 'https://api.sharedcount.com/v1.0/?apikey=' . sanitize_text_field( $sharedcount_token ) . '&url='.$url;
	}

		
	$content = essb_counter_request ( $parse_url );
	$result = 0;
	$result_comments = 0;

	if ($content != '') {
		$content = json_decode ( $content, true );
		$data_parsers = $content;
		if ($api3) {
			$result = isset( $data_parsers['og_object']['engagement']['count']) ? intval ( $data_parsers['og_object']['engagement']['count'] ) : 0;
		}
		else if ($api2) {
			if( !empty( $data_parsers['engagement'] ) ){
				$likes = $data_parsers['engagement']['reaction_count'];
				$comments = $data_parsers['engagement']['comment_count'];
				$shares = $data_parsers['engagement']['share_count'];
				$comments_plugin = $data_parsers['engagement']['comment_plugin_count'];
			} else {
				$comments = 0;
				$shares = 0;
				$likes = 0;
				$comments_plugin = 0;
			}
			
			$result = $likes + $comments + $shares + $comments_plugin;
		}
		else if ($api4) {
			if ( isset( $data_parsers->Facebook )) {
				if ( isset( $data_parsers->Facebook->total_count ) ) {
					$result = intval( $data_parsers->Facebook->total_count );
				}
			}	
		}
		else {
			$result = isset( $data_parsers['og_object']['engagement']['count']) ? intval ( $data_parsers['og_object']['engagement']['count'] ) : 0;
		}
	}

	return $result;

}


function essb_get_tweets_newsc_count($url) {
	$json_string = essb_counter_request( 'https://public.newsharecounts.com/count.json?url=' . $url );
	$json = json_decode ( $json_string, true );
	$result = isset ( $json ['count'] ) ? intval ( $json ['count'] ) : 0;

	return $result;
}

function essb_get_tweets_twitcount_count($url) {
	$json_string = essb_counter_request( 'https://counts.twitcount.com/counts.php?url=' . $url );
	$json = json_decode ( $json_string, true );
	$result = isset ( $json ['count'] ) ? intval ( $json ['count'] ) : 0;

	return $result;
}

function essb_get_tweets_opensc_count($url) {
	$json_string = essb_counter_request( 'https://opensharecount.com/count.json?url=' . $url );
	$json = json_decode ( $json_string, true );
	$result = isset ( $json ['count'] ) ? intval ( $json ['count'] ) : 0;

	return $result;
}


function essb_get_linkedin_count($url) {
	$json_string = essb_counter_request ( 'https://www.linkedin.com/countserv/count/share?url='.$url.'&format=json' );
	$json = json_decode ( $json_string, true );
	$result = isset ( $json ['count'] ) ? intval ( $json ['count'] ) : 0;
	return $result;
}

function essb_get_pinterest_count($url) {
	$return_data = essb_counter_request ( 'https://api.pinterest.com/v1/urls/count.json?url=' . $url );
	$json_string = preg_replace ( '/^receiveCount\((.*)\)$/', "\\1", $return_data );
	$json = json_decode ( $json_string, true );
	$result = isset ( $json ['count'] ) ? intval ( $json ['count'] ) : 0;

	return $result;
}

function essb_get_buffer_count($url) {
	$return_data = essb_counter_request ('https://api.bufferapp.com/1/links/shares.json?url='.$url);

	$result = 0;
	if (!empty($return_data)) {
		$json = json_decode($return_data, true);
		$result = isset($json['shares']) ? intval($json['shares']) : 0;
	}

	return $result;
}

function essb_get_stumbleupon_count($url) {
	$count = 0;
	$content = essb_counter_request ( 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url='.$url );

	$result = json_decode ( $content );
	if (isset ( $result->result->views )) {
		$count = $result->result->views;
	}

	return $count;
}

function essb_get_yummly_count($url) {
	$return_data = essb_counter_request('https://www.yummly.com/services/yum-count?url='.$url);

	$result = 0;
	if (!empty($return_data)) {
		$json = json_decode($return_data, true);
		$result = isset($json['count']) ? intval($json['count']) : 0;
	}

	return $result;
}

function essb_get_addthis_count($url) {
	$return_data = essb_counter_request('https://api-public.addthis.com/url/shares.json?url='.$url);

	$result = 0;
	if (!empty($return_data)) {
		$json = json_decode($return_data, true);
		$result = isset($json['shares']) ? intval($json['shares']) : 0;
	}

	return $result;
}
