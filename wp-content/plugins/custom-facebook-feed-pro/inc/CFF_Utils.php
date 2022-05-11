<?php
/**
 * Class CFF_Utils
 *
 * Contains miscellaneous CFF functions
 *
 * @since 3.18
 */
namespace CustomFacebookFeed;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class CFF_Utils{



	/**
	 * Get JSON object of feed data
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_fetchUrl($url){
	    //Style options
	    $options = get_option('cff_style_settings');
	    isset( $options['cff_request_method'] ) ? $cff_request_method = $options['cff_request_method'] : $cff_request_method = 'auto';

	    if($cff_request_method == '1'){
	        //Use cURL
	        if(is_callable('curl_init')){
	            $ch = curl_init();
	            // Use global proxy settings
	            if (defined('WP_PROXY_HOST')) {
	              curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
	            }
	            if (defined('WP_PROXY_PORT')) {
	              curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
	            }
	            if (defined('WP_PROXY_USERNAME')){
	              $auth = WP_PROXY_USERNAME;
	              if (defined('WP_PROXY_PASSWORD')){
	                $auth .= ':' . WP_PROXY_PASSWORD;
	              }
	              curl_setopt($ch, CURLOPT_PROXYUSERPWD, $auth);
	            }
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	            curl_setopt($ch, CURLOPT_ENCODING, '');
	            $feedData = curl_exec($ch);
	            curl_close($ch);
	        }
	    } else if($cff_request_method == '2') {
	        //Use file_get_contents
	        if ( (ini_get('allow_url_fopen') == 1 || ini_get('allow_url_fopen') === TRUE ) && in_array('https', stream_get_wrappers()) ){
	            $feedData = @file_get_contents($url);
	        }
	    } else if($cff_request_method == '3'){
	        //Use the WP HTTP API
	        $request = new \WP_Http;
	        $response = $request->request($url, array('timeout' => 60, 'sslverify' => false));
		    if( CFF_Utils::cff_is_wp_error( $response ) ) {
			    CFF_Utils::cff_log_wp_error( $response, $url );
	            //Don't display an error, just use the Server config Error Reference message
	            $FBdata = null;
	        } else {
	            $feedData = wp_remote_retrieve_body($response);
	        }
	    } else {
	        //Auto detect
	        if(is_callable('curl_init')){
	            $ch = curl_init();
	            // Use global proxy settings
	            if (defined('WP_PROXY_HOST')) {
	              curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
	            }
	            if (defined('WP_PROXY_PORT')) {
	              curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
	            }
	            if (defined('WP_PROXY_USERNAME')){
	              $auth = WP_PROXY_USERNAME;
	              if (defined('WP_PROXY_PASSWORD')){
	                $auth .= ':' . WP_PROXY_PASSWORD;
	              }
	              curl_setopt($ch, CURLOPT_PROXYUSERPWD, $auth);
	            }
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	            curl_setopt($ch, CURLOPT_ENCODING, '');
	            $feedData = curl_exec($ch);
	            curl_close($ch);
	        } elseif ( (ini_get('allow_url_fopen') == 1 || ini_get('allow_url_fopen') === TRUE ) && in_array('https', stream_get_wrappers()) ) {
	            $feedData = @file_get_contents($url);
	        } else {
	            $request = new \WP_Http;
	            $response = $request->request($url, array('timeout' => 60, 'sslverify' => false));
	            if( CFF_Utils::cff_is_wp_error( $response ) ) {
		            // not a header request
		            if ( strpos( $url, '&limit=' ) !== false ) {
			            CFF_Utils::cff_log_wp_error( $response, $url );
		            }

		            $feedData = null;
	            } else {
	                $feedData = wp_remote_retrieve_body($response);
	            }
	        }
	    }

		if ( ! CFF_Utils::cff_is_fb_error( $feedData ) ) {
			\cff_main_pro()->cff_error_reporter->remove_error( 'connection' );
		} else {
			// not a header request
			if ( strpos( $url, '&limit=' ) !== false ) {
				CFF_Utils::cff_log_fb_error( $feedData, $url );

				$error = json_decode( $feedData, true );

				$reporter = CFF_Utils::cff_is_pro_version() ? \cff_main_pro()->cff_error_reporter : \cff_main()->cff_error_reporter;

				if ( $reporter->is_critical_error( $error ) ) {
					$parsed_url = parse_url( $url );
					if ( ! empty( $parsed_url['query'] ) ) {
						parse_str( $parsed_url['query'], $parsed );

						if ( isset( $parsed['access_token'] ) ) {
							$args = [
								'access_token' => $parsed['access_token']
							];
							$source_data = \CustomFacebookFeed\Builder\CFF_Db::source_query( $args );

							if ( $source_data ) {
								foreach ( $source_data as $source ) {
									\CustomFacebookFeed\Builder\CFF_Source::add_error( $source['account_id'], $feedData );
								}
							}
						}
					}
				}
			}
		}

	    $feedData = apply_filters( 'cff_filter_api_data', $feedData );

	    return $feedData;
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_desc_tags($description){
	    preg_match_all( "/@\[(.*?)\]/", $description, $cff_tag_matches );
	    $replace_strings_arr = array();
	    foreach ( $cff_tag_matches[1] as $cff_tag_match ) {
	        $cff_tag_parts = explode( ':', $cff_tag_match );
	        $replace_strings_arr[] = '<a href="https://facebook.com/'.$cff_tag_parts[0].'" rel="noopener">'.$cff_tag_parts[2].'</a>';
	    }
	    $cff_tag_iterator = 0;
	    $cff_description_tagged = '';
	    $cff_text_split = preg_split( "/@\[(.*?)\]/" , $description );
	    foreach ( $cff_text_split as $cff_desc_split ) {
	        if ( $cff_tag_iterator < count( $replace_strings_arr ) ) {
	            $cff_description_tagged .= $cff_desc_split . $replace_strings_arr[ $cff_tag_iterator ];
	        } else {
	            $cff_description_tagged .= $cff_desc_split;
	        }
	        $cff_tag_iterator++;
	    }

	    return $cff_description_tagged;
	}


	/**
	 * Sort message tags by offset value
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cffSortTags($a, $b) {
	    return $a['offset'] - $b['offset'];
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_is_wp_error( $response ) {
		return is_wp_error( $response );
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_log_wp_error( $response, $url ) {
		if ( is_wp_error( $response ) ) {
			delete_option( 'cff_dismiss_critical_notice' );
			$admin_message = sprintf( __( 'Error connecting to %s.', 'custom-facebook-feed' ), $url );
			$public_message =__( 'Unable to make remote requests to the Facebook API. Log in as an admin to view more details.', 'custom-facebook-feed' );
			$frontend_directions = '<p class="cff-error-directions"><a href="https://smashballoon.com/custom-facebook-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a></p>';
			$backend_directions = '<a class="button button-primary" href="https://smashballoon.com/custom-facebook-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a>';
			$error = array(
				'accesstoken' => 'none',
				'public_message' => $public_message,
				'admin_message' => $admin_message,
				'frontend_directions' => $frontend_directions,
				'backend_directions' => $backend_directions,
				'post_id' => get_the_ID(),
				'errorno' => 'wp_remote_get'
			);
			\cff_main_pro()->cff_error_reporter->add_error( 'wp_remote_get', $error );
		}else{
			\cff_main_pro()->cff_error_reporter->remove_error( 'connection' );
		}
	}

	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_is_fb_error( $response ) {
		return (strpos( $response, '{"error":' ) === 0);
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_log_fb_error( $response, $url ) {
		if ( is_admin() ) {
			return;
		}
		delete_option( 'cff_dismiss_critical_notice' );

		$access_token_refresh_errors = array( 10, 4, 200 );

		$response = json_decode( $response, true );
		$api_error_code = $response['error']['code'];

	    //Page Public Content Access error
	    $ppca_error = false;
	    if( strpos($response['error']['message'], 'Public Content Access') !== false ) $ppca_error = true;

		if ( in_array( (int)$api_error_code, $access_token_refresh_errors, true ) && !$ppca_error ) {
			$pieces = explode( 'access_token=', $url );
			$accesstoken_parts = isset( $pieces[1] ) ? explode( '&', $pieces[1] ) : 'none';
			$accesstoken = $accesstoken_parts[0];

			$api_error_number_message = sprintf( __( 'API Error %s:', 'custom-facebook-feed' ), $api_error_code );
			$link = admin_url( 'admin.php?page=cff-settings' );
			$error = array(
				'accesstoken' => $accesstoken,
				'post_id' => get_the_ID(),
				'errorno' => $api_error_code
			);

			\cff_main_pro()->cff_error_reporter->add_error( 'accesstoken', $error);
		} else {
			\cff_main_pro()->cff_error_reporter->add_error( 'api', $response );
		}
	}
	/**
	 * Make links into span instead when the post text is made clickable
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_wrap_span($text) {
	    $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
		return preg_replace_callback($pattern, array('CustomFacebookFeed\CFF_Utils','cff_wrap_span_callback'), $text);
	}

	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_wrap_span_callback($matches) {
		$max_url_length = 50;
		$max_depth_if_over_length = 2;
		$ellipsis = '&hellip;';
		$target = 'target="_blank"';
		$url_full = $matches[0];
		$url_short = '';
		if (strlen($url_full) > $max_url_length) {
			$parts = parse_url($url_full);
			if (isset($parts['path'])){
				if(isset($parts['scheme']) && isset($parts['host'])) $url_short = $parts['scheme'] . '://' . preg_replace('/^www\./', '', $parts['host']) . '/';
				$path_components = explode('/', trim($parts['path'], '/'));
				foreach ($path_components as $dir) {
					$url_string_components[] = $dir . '/';
				}
				if (!empty($parts['query'])) {
					$url_string_components[] = '?' . $parts['query'];
				}
				if (!empty($parts['fragment'])) {
					$url_string_components[] = '#' . $parts['fragment'];
				}
				for ($k = 0; $k < count($url_string_components); $k++) {
					$curr_component = $url_string_components[$k];
					if ($k >= $max_depth_if_over_length || strlen($url_short) + strlen($curr_component) > $max_url_length) {
						if ($k == 0 && strlen($url_short) < $max_url_length) {
							// Always show a portion of first directory
							$url_short .= substr($curr_component, 0, $max_url_length - strlen($url_short));
						}
						$url_short .= $ellipsis;
						break;
					}
					$url_short .= $curr_component;
				}
			} else {
				$url_short = $url_full;
			}
		}
		return "<span class='cff-break-word'>$url_short</span>";
	}


	/**
	 * Use the timezone to offset the date as all post dates are in UTC +0000
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_set_timezone($original, $cff_timezone){
	    $cff_date_time = new \DateTime(date('m/d g:i a'), new \DateTimeZone('UTC'));
	    $cff_date_time->setTimeZone(new \DateTimeZone($cff_timezone));
	    $cff_date_time_offset = $cff_date_time->getOffset();

	    $original = $original + $cff_date_time_offset;

	    return $original;
	}


	/**
	 * Time stamp functison - used for posts
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_getdate($original, $date_format, $custom_date, $cff_date_translate_strings, $cff_timezone) {

	    //Offset the date by the timezone
	    $new_time = CFF_Utils::cff_set_timezone($original, $cff_timezone);

	    switch ($date_format) {

	        case '2':
	            $print = date_i18n('F jS, g:i a', $new_time);
	            break;
	        case '3':
	            $print = date_i18n('F jS', $new_time);
	            break;
	        case '4':
	            $print = date_i18n('D F jS', $new_time);
	            break;
	        case '5':
	            $print = date_i18n('l F jS', $new_time);
	            break;
	        case '6':
	            $print = date_i18n('D M jS, Y', $new_time);
	            break;
	        case '7':
	            $print = date_i18n('l F jS, Y', $new_time);
	            break;
	        case '8':
	            $print = date_i18n('l F jS, Y - g:i a', $new_time);
	            break;
	        case '9':
	            $print = date_i18n("l M jS, 'y", $new_time);
	            break;
	        case '10':
	            $print = date_i18n('m.d.y', $new_time);
	            break;
	        case '11':
	            $print = date_i18n('m/d/y', $new_time);
	            break;
	        case '12':
	            $print = date_i18n('d.m.y', $new_time);
	            break;
	        case '13':
	            $print = date_i18n('d/m/y', $new_time);
	            break;
	        case '14':
	            $print = date_i18n('d-m-Y, G:i', $new_time);
	            break;
	        case '15':
	            $print = date_i18n('jS F Y, G:i', $new_time);
	            break;
	        case '16':
	            $print = date_i18n('d M Y, G:i', $new_time);
	            break;
	        case '17':
	            $print = date_i18n('l jS F Y, G:i', $new_time);
	            break;
	        case '18':
	            $print = date_i18n('m.d.y - G:i', $new_time);
	            break;
	        case '19':
	            $print = date_i18n('d.m.y - G:i', $new_time);
	            break;
	        default:

	            $cff_second = $cff_date_translate_strings['cff_translate_second'];
	            $cff_seconds = $cff_date_translate_strings['cff_translate_seconds'];
	            $cff_minute = $cff_date_translate_strings['cff_translate_minute'];
	            $cff_minutes = $cff_date_translate_strings['cff_translate_minutes'];
	            $cff_hour = $cff_date_translate_strings['cff_translate_hour'];
	            $cff_hours = $cff_date_translate_strings['cff_translate_hours'];
	            $cff_day = $cff_date_translate_strings['cff_translate_day'];
	            $cff_days = $cff_date_translate_strings['cff_translate_days'];
	            $cff_week = $cff_date_translate_strings['cff_translate_week'];
	            $cff_weeks = $cff_date_translate_strings['cff_translate_weeks'];
	            $cff_month = $cff_date_translate_strings['cff_translate_month'];
	            $cff_months = $cff_date_translate_strings['cff_translate_months'];
	            $cff_year = $cff_date_translate_strings['cff_translate_years'];
	            $cff_years = $cff_date_translate_strings['cff_translate_years'];
	            $cff_ago = $cff_date_translate_strings['cff_translate_ago'];


	            $periods = array($cff_second, $cff_minute, $cff_hour, $cff_day, $cff_week, $cff_month, $cff_year, "decade");
	            $periods_plural = array($cff_seconds, $cff_minutes, $cff_hours, $cff_days, $cff_weeks, $cff_months, $cff_years, "decade");

	            $lengths = array("60","60","24","7","4.35","12","10");
	            $now = time();

	            // is it future date or past date
	            if($now > $original) {
	                $difference = $now - $original;
	                $tense = $cff_ago;
	            } else {
	                $difference = $original - $now;
	                $tense = $cff_ago;
	            }
	            for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	                $difference /= $lengths[$j];
	            }

	            $difference = round($difference);

	            if($difference != 1) {
	                $periods[$j] = $periods_plural[$j];
	            }
	            $print = "$difference $periods[$j] {$tense}";
	            break;

	    }
	    if ( !empty($custom_date) ){
	        $print = date_i18n($custom_date, $new_time);
	    }
	    return $print;
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_eventdate($original, $date_format, $custom_date, $cff_event_timezone_offset, $cff_timezone) {
	    //Event dates don't need to be offset by timezone as they include the timezone in the date in the API response
	    //Added setting as this caused an issue on some websites
	    if( $cff_event_timezone_offset == 'true' ) $original = CFF_Utils::cff_set_timezone($original, $cff_timezone);

	    //If timezone migration is enabled then remove last 5 characters
	    if ( strlen($original) == 24 ) $original = substr($original, 0, -5);

	    switch ($date_format) {

	        case '2':
	            $print = date_i18n('<k>F jS, </k>g:ia', $original);
	            break;
	        case '3':
	            $print = date_i18n('g:ia<k> - F jS</k>', $original);
	            break;
	        case '4':
	            $print = date_i18n('g:ia<k>, F jS</k>', $original);
	            break;
	        case '5':
	            $print = date_i18n('<k>l F jS - </k> g:ia', $original);
	            break;
	        case '6':
	            $print = date_i18n('<k>D M jS, Y, </k>g:iA', $original);
	            break;
	        case '7':
	            $print = date_i18n('<k>l F jS, Y, </k>g:iA', $original);
	            break;
	        case '8':
	            $print = date_i18n('<k>l F jS, Y - </k>g:ia', $original);
	            break;
	        case '9':
	            $print = date_i18n("<k>l M jS, 'y</k>", $original);
	            break;
	        case '10':
	            $print = date_i18n('<k>m.d.y - </k>g:iA', $original);
	            break;
	        case '11':
	            $print = date_i18n('<k>m/d/y, </k>g:ia', $original);
	            break;
	        case '12':
	            $print = date_i18n('<k>d.m.y - </k>g:iA', $original);
	            break;
	        case '13':
	            $print = date_i18n('<k>d/m/y, </k>g:ia', $original);
	            break;
	        case '14':
	            $print = date_i18n('<k>M j, </k>g:ia', $original);
	            break;
	        case '15':
	            $print = date_i18n('<k>M j, </k>G:i', $original);
	            break;
	        case '16':
	            $print = date_i18n('<k>d-m-Y, </k>G:i', $original);
	            break;
	        case '17':
	            $print = date_i18n('<k>jS F Y, </k>G:i', $original);
	            break;
	        case '18':
	            $print = date_i18n('<k>d M Y, </k>G:i', $original);
	            break;
	        case '19':
	            $print = date_i18n('<k>l jS F Y, </k>G:i', $original);
	            break;
	        case '20':
	            $print = date_i18n('<k>m.d.y - </k>G:i', $original);
	            break;
	        case '21':
	            $print = date_i18n('<k>d.m.y - </k>G:i', $original);
	            break;
	        default:
	            $print = date_i18n('<k>F j, Y, </k>g:ia', $original);
	            break;
	    }
	    if ( !empty($custom_date) ){
	        $print = date_i18n($custom_date, $original);
	    }

	    return $print;
	}


	/**
	 * Use custom stripos function if it's not available (only available in PHP 5+)
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function stripos($haystack, $needle){
		if( empty( stristr( $haystack, $needle ) ) )
			return false;
		return strpos($haystack, stristr( $haystack, $needle ) );
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_stripos_arr($haystack, $needle) {
	    if(!is_array($needle)) $needle = array($needle);
	    foreach($needle as $what) {
	        if(($pos = CFF_Utils::stripos($haystack, ltrim($what) ))!==false) return $pos;
	    }
	    return false;
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_stripos_arr_filter($haystack, $needle) {
	    if(!is_array($needle)) $needle = array($needle);

	    foreach($needle as $word) {

	        $word = strtolower( trim($word) );
	        if( empty($word) ) return false;
	        $regex = '/'.$word.'\b/u'; //The u at the end converts to UTF-8 and so matches foreign chars

	        //Escape parentheses
	        if (strpos($word, '(') !== false || strpos($word, ')') !== false) {
	            $word = str_replace("(", "\\(", $word);
	            $word = str_replace(")", "\\)", $word);
	            $regex = '/'.$word.'/';
	        }

	        //Escape double hashtags
	        if ( strpos($word, '##') !== false) {
	            $word = str_replace("##", "\\##", $word);
	            $regex = '/'.$word.'/';
	        }

	        preg_match($regex, strtolower($haystack), $match, PREG_OFFSET_CAPTURE);

	        if( isset($match[0]) ){
	            $pos = $match[0][1];
	            return $pos;
	        }
	    }
	    return false;
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_mb_substr_replace($string, $replacement, $start, $length=NULL) {
	    if (is_array($string)) {
	        $num = count($string);
	        // $replacement
	        $replacement = is_array($replacement) ? array_slice($replacement, 0, $num) : array_pad(array($replacement), $num, $replacement);
	        // $start
	        if (is_array($start)) {
	            $start = array_slice($start, 0, $num);
	            foreach ($start as $key => $value)
	                $start[$key] = is_int($value) ? $value : 0;
	        }
	        else {
	            $start = array_pad(array($start), $num, $start);
	        }
	        // $length
	        if (!isset($length)) {
	            $length = array_fill(0, $num, 0);
	        }
	        elseif (is_array($length)) {
	            $length = array_slice($length, 0, $num);
	            foreach ($length as $key => $value)
	                $length[$key] = isset($value) ? (is_int($value) ? $value : $num) : 0;
	        }
	        else {
	            $length = array_pad(array($length), $num, $length);
	        }
	        // Recursive call
	        return array_map(__FUNCTION__, $string, $replacement, $start, $length);
	    }
	    preg_match_all('/./us', (string)$string, $smatches);
	    preg_match_all('/./us', (string)$replacement, $rmatches);
	    if ($length === NULL) $length = mb_strlen($string);
	    array_splice($smatches[0], $start, $length, $rmatches[0]);
	    return join($smatches[0]);
	}

	/**
	 * Push to assoc array
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_array_push_assoc($array, $key, $value){
	    $array[$key] = $value;
	    return $array;
	}


	/**
	 * Convert string to slug
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_to_slug($string){
	    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_get_utc_offset() {
		return get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS;
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_schedule_report_email() {
		$options = get_option( 'cff_style_settings' );

		$input     = isset( $options['email_notification'] ) ? $options['email_notification'] : 'monday';
		$timestamp = strtotime( 'next ' . $input );
		$timestamp = $timestamp + ( 3600 * 24 * 7 );

		$six_am_local = $timestamp + CFF_Utils::cff_get_utc_offset() + ( 6 * 60 * 60 );

		wp_schedule_event( $six_am_local, 'cffweekly', 'cff_feed_issue_email' );
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_is_pro_version() {
		return defined( 'CFFWELCOME_VER' );
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_get_set_cache($cff_posts_json_url, $transient_name, $cff_cache_time, $cache_seconds, $data_att_html, $cff_show_access_token, $access_token, $backup=false, $is_multifeed = false ) {
		$cache_seconds = max(60, $cache_seconds );

		// Trying to use new cache table
		$shortcode_atts = is_array( $data_att_html ) ? $data_att_html : json_decode( str_replace( '&quot;', '"', $data_att_html ), true );

		if ( ! empty( $shortcode_atts['feed'] ) ) {
			$feed_id = (int)$shortcode_atts['feed'];
			$is_legacy = false;
		} else {
			$feed_id = $transient_name;
			$is_legacy = true;
		}

		if ( $is_multifeed ) {
			$feed_id = $transient_name;
			$is_legacy = true;
		}

		$feed_page = isset( $shortcode_atts['feedPage'] ) ? $shortcode_atts['feedPage'] : 1;
		$feed_cache = new CFF_Cache( $feed_id, $feed_page, $cache_seconds, $is_legacy );
		$feed_cache->retrieve_and_set();

		$cache_type = 'posts';
		if ( strpos( $transient_name, 'cff_header_' ) !== false ) {
			$cache_type = 'header';
		}

		$cache_type_page = $cache_type;
		if ( $cache_type === 'posts' && $feed_page > 1 ) {
			$cache_type_page = 'posts_' . $feed_page;
		}

		if ( $feed_cache->is_expired( $cache_type ) ) {
			//Get the contents of the Facebook page
			$posts_json = CFF_Utils::cff_fetchUrl($cff_posts_json_url);

			//Check whether any data is returned from the API. If it isn't then don't cache the error response and instead keep checking the API on every page load until data is returned.
			$FBdata = json_decode($posts_json);

			//Check whether the JSON is wrapped in a "data" property as if it doesn't then it's a featured post
			$prefix_data = '{"data":';
			(substr($posts_json, 0, strlen($prefix_data)) == $prefix_data) ? $cff_featured_post = false : $cff_featured_post = true;

			//Add API URL to beginning of JSON array
			$prefix = '{';
			if (substr($posts_json, 0, strlen($prefix)) == $prefix) $posts_json = substr($posts_json, strlen($prefix));

			//Encode and replace quotes so can be stored as a string
			$data_att_html = str_replace( '"', '&quot;', json_encode($data_att_html) );
			$posts_json = '{"api_url":"'.$cff_posts_json_url.'", "shortcode_options":"'.$data_att_html.'", ' . $posts_json;

			//If it's a featured post then it doesn't contain 'data'
			( $cff_featured_post ) ? $FBdata = $FBdata : $FBdata = $FBdata->data;

			//Check the API response
			if( !empty($FBdata) ) {

				//Error returned by API
				if( isset($FBdata->error) ){

					//Cache the error JSON so doesn't keep making repeated requests
					//See if a backup cache exists
					if ( false !== $feed_cache->get( $cache_type . '_backup' ) ) {

						$posts_json = $feed_cache->get( $cache_type . '_backup' );

						//Add error message to backup cache so can be displayed at top of feed
						isset( $FBdata->error->message ) ? $error_message = $FBdata->error->message : $error_message = '';
						isset( $FBdata->error->type ) ? $error_type = $FBdata->error->type : $error_type = '';
						$prefix = '{';
						if (substr($posts_json, 0, strlen($prefix)) == $prefix) $posts_json = substr($posts_json, strlen($prefix));
						$error_json = '{"cached_error": { "message": "'.$error_message.'", "type": "'.$error_type.'" }';
						$error_json .= !empty($posts_json) ? ', ' . $posts_json : '}';
						$posts_json = $error_json;
					}

					//Posts data returned by API
				} else {

					//If a backup should be created for this data then create one
					if ( $backup ){
						$feed_cache->update_or_insert( $cache_type . '_backup', $posts_json );
					}

					if ( $cache_type === 'posts' ) {
						$feed_cache->after_new_posts_retrieved();
					}
				}

				$feed_cache->update_or_insert( $cache_type, $posts_json );
			}
		} else {
			$posts_json = $feed_cache->get( $cache_type_page );
			if ( strpos($posts_json, '"error":{"message":') !== false && false !== get_transient( '!cff_backup_' . $transient_name ) ){
				//Use backup cache if exists
				$posts_json = $feed_cache->get( $cache_type . '_backup' );
			}

			//If we can't find the transient then fall back to just getting the json from the api
			if ($posts_json == false){
				$posts_json = CFF_Utils::cff_fetchUrl( $cff_posts_json_url );
			}
		}

		return $posts_json;
	}


	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cffSplitToken($at_piece, $access_token_multiple=false){
	    $access_token_split = explode(":", $at_piece);

	    ( count($access_token_split) > 1 ) ? $token_only = trim($access_token_split[1]) : $token_only = '';

	    if (strpos($token_only, '02Sb981f26534g75h091287a46p5l63') !== false) {
	        $token_only = str_replace("02Sb981f26534g75h091287a46p5l63","",$token_only);
	    }

	    $access_token_multiple[ trim($access_token_split[0]) ] = $token_only;

	    return $access_token_multiple;
	}

	/**
	 * Time stamp function - used for comments
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_timesince($original, $cff_date_translate_strings) {

	    $cff_second = $cff_date_translate_strings['cff_translate_second'];
	    $cff_seconds = $cff_date_translate_strings['cff_translate_seconds'];
	    $cff_minute = $cff_date_translate_strings['cff_translate_minute'];
	    $cff_minutes = $cff_date_translate_strings['cff_translate_minutes'];
	    $cff_hour = $cff_date_translate_strings['cff_translate_hour'];
	    $cff_hours = $cff_date_translate_strings['cff_translate_hours'];
	    $cff_day = $cff_date_translate_strings['cff_translate_day'];
	    $cff_days = $cff_date_translate_strings['cff_translate_days'];
	    $cff_week = $cff_date_translate_strings['cff_translate_week'];
	    $cff_weeks = $cff_date_translate_strings['cff_translate_weeks'];
	    $cff_month = $cff_date_translate_strings['cff_translate_month'];
	    $cff_months = $cff_date_translate_strings['cff_translate_months'];
	    $cff_year = $cff_date_translate_strings['cff_translate_years'];
	    $cff_years = $cff_date_translate_strings['cff_translate_years'];
	    $cff_ago = $cff_date_translate_strings['cff_translate_ago'];


	    $periods = array($cff_second, $cff_minute, $cff_hour, $cff_day, $cff_week, $cff_month, $cff_year, "decade");
	    $periods_plural = array($cff_seconds, $cff_minutes, $cff_hours, $cff_days, $cff_weeks, $cff_months, $cff_years, "decade");

	    $lengths = array("60","60","24","7","4.35","12","10");
	    $now = time();

	    // is it future date or past date
	    if($now > $original) {
	        $difference = $now - $original;
	        $tense = $cff_ago;;
	    } else {
	        $difference = $original - $now;
	        $tense = $cff_ago;
	    }
	    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	        $difference /= $lengths[$j];
	    }

	    $difference = round($difference);

	    if($difference != 1) {
	        $periods[$j] = $periods_plural[$j];
	    }
	    return "$difference $periods[$j] {$tense}";

	}


	/**
	 *
	 * Push to assoc array
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	function cff_array_push_assoc_photos($array, $key, $value, $post_time){
	    $array[$key]['post'] = $value;
	    $array[$key]['post_time'] = $post_time;

	    return $array;
	}



	/**
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	function cff_get_default_type_and_terms() {
		$feed_options = cff_get_processed_options( array() );

		$page_id = trim( $feed_options['id'] );
		$cff_types = $feed_options['pagetype'];

		//var_dump( $tw_feed_type_and_terms );
		//die();
		$return = array(
			'type' => $cff_types,
			'term_label' => $cff_types.'(s)',
			'terms' => $page_id
		);

		return $return;
	}


	/**
	 * Check if On
	 * Function to check if a shortcode options is set to ON or TRUE
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 * @return boolean
	 */
	static function check_if_on( $value ){
		return ( isset( $value ) && !empty( $value ) && ( $value == 'true' || $value == 'on') ) ?  true : false;
	}

	/**
	 * Check Value
	 * Function to check a value if exists or return a default one
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 * @return mixed
	 */
	static function return_value( $value , $default = ''){
		return ( isset( $value ) && !empty( $value ) ) ?  $value  : $default;
	}


	/**
	 * Get CSS value
	 * Checks if the value is a valid CSS distance
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 * @return string
	 */
	static function get_css_distance( $value ){
		return ( is_numeric(substr($value, -1, 1)) ) ? $value . 'px' : $value;
	}


	/**
	 *
	 *
	 * Print Template
	 * returns an HTML Template
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function print_template_part( $template_name, $args = array(), $this_class = null){
		$this_class = $this_class;
		extract($args);
		ob_start();
		include trailingslashit( CFF_PLUGIN_DIR ) . 'templates/' . $template_name . '.php';
		$template = ob_get_contents();
		ob_get_clean();
		return $template;
	}

	/**
	 *
	 *
	 * Add Resized Image Data
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function cff_add_resized_image_data( $feed_id, $settings, $page = 1, $cache_feed_id = false ) {
		$disable_resizing = CFF_Utils::check_if_on( $settings['disableresize'] );
		if ( $disable_resizing ) {
			return '';
		}
		$args = array(
			'limit' => isset( $settings['minnum'] ) ? $settings['minnum'] : $settings['num']
		);
		if ( $page > 1 ) {
			$args['offset'] = (int)$page * $args['limit'];
		}

		if ( ! empty( $cache_feed_id ) ) {
			$feed_cache = new CFF_Cache( $cache_feed_id );
			$feed_cache->retrieve_and_set();

			$resized_image_cache = $feed_cache->get( 'resized_images' );

			if ( empty( $resized_image_cache ) ) {
				$resized_data = CFF_Resizer::get_resized_image_data_for_set( $feed_id, $args );

				$feed_cache->update_or_insert( 'resized_images', $resized_data, false, false );

				$encoded_resized_image_data = CFF_Utils::cff_json_encode( $resized_data );
			} else {
				$encoded_resized_image_data = $resized_image_cache;
			}
		} else {
			$resized_data = CFF_Resizer::get_resized_image_data_for_set( $feed_id, $args );

			$encoded_resized_image_data = CFF_Utils::cff_json_encode( $resized_data );
		}

		$return = '<span class="cff_resized_image_data" data-feedid="' . esc_attr( $feed_id ) . '" data-resized="'. esc_attr( $encoded_resized_image_data ) .'">';
		$return .= '</span>';

		return $return;
	}


	/**
	 * @return string
	 *
	 * @since 2.1.1
	 */
	static function cff_get_resized_uploads_url() {
		$upload = wp_upload_dir();

		$base_url = $upload['baseurl'];
		$home_url = home_url();

		if ( strpos( $home_url, 'https:' ) !== false ) {
			str_replace( 'http:', 'https:', $base_url );
		}

		$resize_url = apply_filters( 'cff_resize_url', trailingslashit( $base_url ) . trailingslashit( CFF_UPLOADS_NAME ) );

		return $resize_url;
	}


	/**
	 * @return Json Encode
	 *
	 * @since 2.1.1
	 */
	static function cff_json_encode( $thing ) {
		return wp_json_encode( $thing );
	}


	/**
	 *
	 * Shorten the Text
	 * @return String
	 * @since 2.1.1
	 */
	static function cff_maybe_shorten_text( $string ) {

		$limit = 20;

		if ( strlen( $string ) <= $limit ) {
			return $string;
		}

		$string = str_replace( '<br />', "\n\r", $string );

		$parts = preg_split( '/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE );
		$parts_count = count( $parts );

		$length = 0;
		$last_part = 0;
		for ( ; $last_part < $parts_count; ++$last_part ) {
			$length += strlen( $parts[ $last_part ] );
			if ( $length > $limit ) { break; }
		}

		$last_part = $last_part !== 0 ? $last_part - 1 : 0;
		$parts[ $last_part ] = $parts[ $last_part ] . '...';

		$return_parts = array_slice( $parts, 0, $last_part + 1 );

		$return = implode( ' ', $return_parts );

		return $return;
	}


	/**
	 *
	 * Get Connected Accounts
	 * @since 3.18
	 */
	static function cff_get_connected_accounts() {
		$cff_connected_accounts = get_option('cff_connected_accounts', array());
		if( !empty($cff_connected_accounts) ){
			$cff_connected_accounts = str_replace('\"','"', $cff_connected_accounts);
            $cff_connected_accounts = str_replace("\'","'", $cff_connected_accounts);
            $cff_connected_accounts = json_decode( $cff_connected_accounts, true );
		}
		if(!is_array($cff_connected_accounts) || $cff_connected_accounts == null){
			$cff_connected_accounts = [];
		}
		return $cff_connected_accounts;
	}

	/**
	 *
	 * Get Connected Accounts
	 * @since 3.18
	 */
	static function cff_get_connected_accounts_object() {
		$cff_connected_accounts = get_option('cff_connected_accounts', array());
		if( !empty($cff_connected_accounts) ){
			$cff_connected_accounts = str_replace('\"','"', $cff_connected_accounts);
            $cff_connected_accounts = str_replace("\'","'", $cff_connected_accounts);
            $cff_connected_accounts = json_decode( $cff_connected_accounts);
		}
		$check_array = (array)$cff_connected_accounts;
	    if ( empty( $check_array ) ) {
	        $cff_connected_accounts = false;
	    }
		return $cff_connected_accounts;
	}

	/**
	 *
	 *
	 * This function will get the Profile Pic, Cover, Name, About
	 * For the visual header display
	 *
	 * @access public
  	 * @static
	 * @since 3.18
	 */
	static function fetch_header_data( $page_id, $cff_is_group, $access_token, $cff_cache_time, $cff_multifeed_active, $data_att_html){
		// Create Transient Name
		$transient_name = 'cff_header_' . $page_id;
		$transient_name = substr($transient_name, 0, 45);

	    //These fields only apply to pages
		!$cff_is_group ? $page_only_fields = ',fan_count,about' : $page_only_fields = '';

	    //Check to see whether it's multifeed and set ID/token accordingly
		$header_page_id = $page_id;
		$header_access_token = $access_token;
		if( $cff_multifeed_active && strpos($page_id, ',') !== false ){
			$header_page_id_arr = explode(',', $page_id);
			if( is_array($header_page_id_arr) ) $header_page_id = $header_page_id_arr[0];
		}
		if( is_array($access_token) ){
			$header_access_token = reset($access_token);
			if( empty($header_access_token) ) $header_access_token = key($access_token);
		}
		$header_details_json_url = 'https://graph.facebook.com/v4.0/'.$header_page_id.'?fields=id,picture.height(150).width(150),cover,name,link'.$page_only_fields.'&access_token='. $header_access_token;
		//Get the data
		$header_details = CFF_Utils::cff_get_set_cache($header_details_json_url, $transient_name, $cff_cache_time, WEEK_IN_SECONDS, $data_att_html, false, $access_token);
		$header_details = json_decode($header_details);
		return $header_details;
	}

}