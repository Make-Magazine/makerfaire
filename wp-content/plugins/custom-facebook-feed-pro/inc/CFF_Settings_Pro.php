<?php
/**
 * Class CFF_Settings_Pro
 */

namespace CustomFacebookFeed;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CFF_Settings_Pro {
	/**
	 * @var array
	 */
	protected $atts;

	/**
	 * @var array
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $settings;

	protected $transient_name;

	/**
	 * @var array
	 */
	protected $feed_type_and_terms;

	public function __construct( $atts ) {
		$this->settings = $atts;
	}

	public function get_settings() {
		return $this->settings;
	}

	public function get_feed_type_and_terms() {
		return $this->feed_type_and_terms;
	}

	public function get_transient_name() {
		return $this->transient_name;
	}

	public function set_feed_type_and_terms( $next_urls_arr_safe = null ) {
		$this->feed_type_and_terms = array();

		$feed_options = $this->settings;

		//Define vars
		$access_token = $feed_options['accesstoken'];
		//If the 'Enter my own Access Token' box is unchecked then don't use the user's access token, even if there's one in the field
		$feed_options['ownaccesstoken'] ? $cff_show_access_token = true : $cff_show_access_token = true;
		//Reviews Access Token
		$page_access_token = $feed_options['pagetoken'];
		$cff_show_access_token = true;
		$page_id = trim( $feed_options['id'] );
		$show_posts = isset( $feed_options['minnum'] ) ? $feed_options['minnum'] : $feed_options['num'];
		$cff_post_limit = $feed_options['limit'];
		$cff_page_type = $feed_options[ 'pagetype' ];
		$show_others = $feed_options['others'];
		$show_posts_by = $feed_options['showpostsby'];
		$cff_caching_type = $feed_options['cachetype'];
		$cff_cache_time = $feed_options['cachetime'];
		$cff_cache_time_unit = $feed_options['cacheunit'];
		$cff_locale = $feed_options['locale'];
		//Post types
		$cff_types = $feed_options['type'];
		$cff_events_source = $feed_options[ 'eventsource' ];
		$cff_event_offset = $feed_options[ 'eventoffset' ];
		$cff_albums_source = $feed_options[ 'albumsource' ];
		$cff_photos_source = $feed_options[ 'photosource' ];
		$cff_videos_source = $feed_options[ 'videosource' ];
		//Past events
		$cff_past_events = $feed_options['pastevents'];
		//Active extensions
		$cff_ext_multifeed_active = $feed_options[ 'multifeedactive' ];
		$cff_ext_date_active = $feed_options[ 'daterangeactive' ];
		$cff_featured_post_active = $feed_options[ 'featuredpostactive' ];
		$cff_album_active = $feed_options[ 'albumactive' ];
		$cff_masonry_columns_active = false; //Deprecated
		$cff_carousel_active = $feed_options[ 'carouselactive' ];
		$cff_reviews_active = $feed_options[ 'reviewsactive' ];
		//Extension settings
		$cff_album_id = $feed_options['album'];
		$cff_featured_post_id = $feed_options['featuredpost'];

		//Get show posts attribute. If not set then default to 25
		if (empty($show_posts)) $show_posts = 25;
		if ( $show_posts == 0 || $show_posts == 'undefined' ) $show_posts = 25;

		//Set the page type
		$cff_is_group = false;
		if ($cff_page_type == 'group') $cff_is_group = true;

		//Look for non-plural version of string in the types string in case user specifies singular in shortcode
		$cff_show_links_type = false;
		$cff_show_event_type = false;
		$cff_show_video_type = false;
		$cff_show_photos_type = false;
		$cff_show_status_type = false;
		$cff_show_albums_type = false;
		$cff_reviews = false;
		if ( CFF_Utils::stripos($cff_types, 'link') !== false ) $cff_show_links_type = true;
		if ( CFF_Utils::stripos($cff_types, 'event') !== false ) $cff_show_event_type = true;
		if ( CFF_Utils::stripos($cff_types, 'video') !== false ) $cff_show_video_type = true;
		if ( CFF_Utils::stripos($cff_types, 'photo') !== false ) $cff_show_photos_type = true;
		if ( CFF_Utils::stripos($cff_types, 'album') !== false ) $cff_show_albums_type = true;
		if ( CFF_Utils::stripos($cff_types, 'status') !== false ) $cff_show_status_type = true;
		if ( CFF_Utils::stripos($cff_types, 'review') !== false && $cff_reviews_active ) $cff_reviews = true;

		//Events only
		if ( empty($cff_events_source) || !isset($cff_events_source) ) $cff_events_source = 'eventspage';
		if ( empty($cff_event_offset) || !isset($cff_event_offset) ) $cff_event_offset = '6';
		($cff_show_event_type && !$cff_show_links_type && !$cff_show_video_type && !$cff_show_photos_type && !$cff_show_status_type && !$cff_show_albums_type) ? $cff_events_only = true : $cff_events_only = false;

		//Albums only
		($cff_show_albums_type && !$cff_show_links_type && !$cff_show_video_type && !$cff_show_photos_type && !$cff_show_status_type && !$cff_show_event_type) ? $cff_albums_only = true : $cff_albums_only = false;

		//Photos only
		( ($cff_show_photos_type && $cff_photos_source == 'photospage') && !$cff_show_links_type && !$cff_show_video_type && !$cff_show_event_type && !$cff_show_status_type && !$cff_show_albums_type) ? $cff_photos_only = true : $cff_photos_only = false;
		if( $cff_featured_post_active && !empty($cff_featured_post_id) ) $cff_photos_only = false;

		//Videos only
		( ($cff_show_video_type && $cff_videos_source == 'videospage') && !$cff_show_albums_type && !$cff_show_links_type && !$cff_show_photos_type && !$cff_show_status_type && !$cff_show_event_type) ? $cff_videos_only = true : $cff_videos_only = false;
		if( $cff_featured_post_active && !empty($cff_featured_post_id) ) $cff_videos_only = false;

		//Is it SSL?
		$cff_ssl = '';
		if (is_ssl()) $cff_ssl = '&return_ssl_resources=true';

		//Use posts? or feed?
		$graph_query = 'posts';
		$cff_show_only_others = false;

		//If 'others' shortcode option is used then it overrides any other option
		if ($show_others) {
			//Show posts by everyone
			if ( $show_others == 'on' || $show_others == 'true' || $show_others == true || $cff_is_group ) $graph_query = 'feed';
			//Only show posts by me
			if ( $show_others == 'false' ) $graph_query = 'posts';
		} else {
			//Else use the settings page option or the 'showpostsby' shortcode option
			//Only show posts by me
			if ( $show_posts_by == 'me' ) $graph_query = 'posts';
			//Show posts by everyone
			if ( $show_posts_by == 'others' || $cff_is_group ) $graph_query = 'feed';
			//Show posts ONLY by others
			if ( $show_posts_by == 'onlyothers' && !$cff_is_group ) {
				$graph_query = 'visitor_posts';
				$cff_show_only_others = true;
			}
		}


		//Calculate the cache time in seconds
		if($cff_cache_time_unit == 'minutes') $cff_cache_time_unit = 60;
		if($cff_cache_time_unit == 'hour' || $cff_cache_time_unit == 'hours') $cff_cache_time_unit = 60*60;
		if($cff_cache_time_unit == 'days') $cff_cache_time_unit = 60*60*24;
		$cache_seconds = $cff_cache_time * $cff_cache_time_unit;


		//********************************************//
		//*****************GET POSTS******************//
		//********************************************//

		$FBdata_arr = array(); //Use an array to store the data for each page ID (for multifeed)

		//Multifeed extension
		( $cff_ext_multifeed_active ) ? $page_ids = cff_multifeed_ids($page_id) : $page_ids = array($page_id);

		//If it's an album embed then only use one ID otherwise it loops through and embeds the same album items multiple times
		if( !empty($cff_album_id) ) $page_ids = array($page_ids[0]);

		//If the limit isn't set then set it to be 7 more than the number of posts defined
		if ( isset($cff_post_limit) && $cff_post_limit !== '' ) {
			$cff_post_limit = $cff_post_limit;
		} else {
			if( intval($show_posts) >= 50 ) $cff_post_limit = intval(intval($show_posts) + 7);
			if( intval($show_posts) < 50 ) $cff_post_limit = intval(intval($show_posts) + 5);
			if( intval($show_posts) < 25  ) $cff_post_limit = intval(intval($show_posts) + 4);
			if( intval($show_posts) < 10  ) $cff_post_limit = intval(intval($show_posts) + 3);
			if( intval($show_posts) < 6  ) $cff_post_limit = intval(intval($show_posts) + 2);
			if( intval($show_posts) < 2  ) $cff_post_limit = intval(intval($show_posts) + 1);

			//If using multifeed then set the limit dynamically based on the number of pages if it isn't set
			if( $cff_ext_multifeed_active && count($page_ids) > 1 ){
				$cff_post_limit = ( ceil(intval($show_posts) / count($page_ids)) ) + 1;
			}
		}
		if( $cff_post_limit >= 100 ) $cff_post_limit = 100;

		//If the number of posts is set to zero then don't show any and set limit to one
		if ( ($show_posts == '0' || $show_posts == 0) && $show_posts !== ''){
			$show_posts = 0;
			$cff_post_limit = 1;
		}

		//If the timeline pagination method is set to use the API paging method then set the limit to be the number of posts displayed so that posts aren't skipped when loading more
		if( $feed_options['timelinepag'] == 'paging' ) $cff_post_limit = $show_posts;

		//If multiple Access Tokens are being used then split them up into an associative array
		$cff_multiple_tokens = false;
		if ( $cff_ext_multifeed_active && is_array($access_token) ){
			$cff_multiple_tokens = true;
			$access_token_multiple = $access_token;
		}

		$transient_name = '';

		//Loop through page IDs
		foreach ( $page_ids as $page_id ) {

			if ( $cff_multiple_tokens ) {
				if ( isset( $access_token_multiple[ $page_id ] ) ) {
					$access_token = $access_token_multiple[ $page_id ];
				}

				//If it's an array then that means there's no token assigned to this Page ID, so get the first token from the array and use that for this ID
				if ( is_array( $access_token ) ) {

					//Check whether the first item in the array is a single access token with no ID assigned
					foreach ( $access_token as $key => $value ) {
						break;
					}
					if ( strlen( $key ) > 50 ) {
						$access_token = $key;

						//If it's not a single access token and it has the ID:token format then use the token from that first item
					} else {
						$access_token = reset( $access_token );
					}
				}
			}

			//********************************************//
			//********CREATE THE API REQUEST URL**********//
			//********************************************//

			//These need to go here so that they're in the correct format for each ID looped through in multifeed. Otherwise they get converted to unix below and don't work for the second ID.
			$cff_date_from  = $feed_options['from'];
			$cff_date_until = $feed_options['until'];

			//EVENTS ONLY
			if ( $cff_events_only && $cff_events_source == 'eventspage' ) {

				//Can be used to display group events passed their start time. Default is 6 hours.
				$cff_event_offset_time = '-' . $cff_event_offset . ' hours';
				$curtimeplus           = strtotime( $cff_event_offset_time, time() );

				//Start time string
				$cff_start_time_string   = '';
				$cff_get_upcoming_events = "&time_filter=upcoming";

				//Date range extension
				if ( $cff_ext_date_active && ( ! empty( $cff_date_from ) || ! empty( $cff_date_until ) ) ) {

					( ! empty( $cff_date_from ) ) ? $cff_date_from = strtotime( $cff_date_from ) : $cff_date_from = $curtimeplus;
					( ! empty( $cff_date_until ) ) ? $cff_date_until = strtotime( $cff_date_until ) : $cff_date_until = $curtimeplus;

					$cff_start_time_string = cff_ext_date( $cff_date_from, $cff_date_until );
					//If it's a date range then don't just query upcoming events, query all of them
					$cff_get_upcoming_events = '';

				}

				//Events URL
				$event_fields        = 'id,name,attending_count,cover,start_time,end_time,event_times,timezone,place,description,ticket_uri,interested_count';
				$cff_events_json_url = "https://graph.facebook.com/v3.2/" . $page_id . "/events/?fields=" . $event_fields . $cff_start_time_string . $cff_get_upcoming_events . "&limit=250&access_token=" . $access_token . "&format=json-strings" . $cff_ssl;

				//Past events
				( $cff_past_events !== 'false' && $cff_past_events != false ) ? $cff_past_events = true : $cff_past_events = false;

				//If the limit isn't set then set it to be the number of posts defined
				if ( isset( $feed_options['limit'] ) && $feed_options['limit'] !== '' ) {
					$cff_post_limit = $cff_post_limit;
				} else {
					$cff_post_limit = intval( $show_posts );
				}
				if ( $cff_post_limit >= 100 ) {
					$cff_post_limit = 100;
				}

				//Get past events. Limit must be set high to get all past events and be able to show the newest ones first
				if ( $cff_past_events ) {
					$cff_events_json_url = 'https://graph.facebook.com/v3.2/' . $page_id . '/events?fields=' . $event_fields . '&limit=' . $cff_post_limit . '&time_filter=past&access_token=' . $access_token;
				}

				//Group events
				if ( $cff_is_group && ! $cff_past_events ) {
					$cff_events_json_url = 'https://graph.facebook.com/v3.2/' . $page_id . '/events?fields=name,id,description,start_time,end_time,event_times,timezone,ticket_uri,place,cover,attending_count,interested_count&limit=200&since=' . $curtimeplus . '&access_token=' . $access_token;
				}

				//Featured Post extension
				if ( $cff_featured_post_active && ! empty( $cff_featured_post_id ) ) {
					$cff_events_json_url = cff_featured_event_id( trim( $cff_featured_post_id ), $access_token );
				}

				//Assign it here so that it's returned at the end of the function
				$cff_posts_json_url = $cff_events_json_url;

			} //END EVENTS ONLY

			//ALL POSTS
			if ( ! $cff_events_only || ( $cff_events_only && $cff_events_source == 'timeline' ) ) {

				//Create date range using the Date Range extension
				( $cff_ext_date_active ) ? $cff_date_range = cff_ext_date( strtotime( $cff_date_from ), strtotime( $cff_date_until ) ) : $cff_date_range = '';

				//Fields which are only for pages or groups
				if ( $cff_is_group ) {
					$specific_fields = 'reactions.summary(true).limit(0),';
				} else {
					$specific_fields = 'likes.summary(true).limit(0),';
				}

				//Add option to load comments via JS to workaround Facebook "unknown error" bug caused by comments in the API request
				$feed_options['loadcommentsjs'] == 'true' ? $comments_field = '' : $comments_field = 'comments.summary(true){message,created_time},';

				//Add option to remove attachments description field to workaround Facebook "Unsupported Get Request" bug caused by sales posts in the API request
                $attachments_desc = ( $feed_options['salesposts'] == 'true' ) ? '' : ',description';

	            //Add option to remove story_tags to workaround Facebook "Unknown Error" message returned by API for certain posts
                $story_tags = ( $feed_options['storytags'] == 'true' ) ? '' : ',story_tags';

	            $cff_posts_json_url = 'https://graph.facebook.com/v4.0/' . $page_id . '/' . $graph_query . '?fields=id,from{picture,id,name,link},message,message_tags,story'. $story_tags .',picture,full_picture,status_type,created_time,backdated_time,attachments{title'. $attachments_desc .',media_type,unshimmed_url,target{id},multi_share_end_card,media{source,image},subattachments},shares,'.$comments_field.$specific_fields.'call_to_action,privacy&access_token=' . $access_token . '&limit=' . $cff_post_limit . '&locale=' . $cff_locale . $cff_ssl . $cff_date_range;


				//If the feed is not a timeline feed then set the post limit to be the same as the number of posts being shown as we don't need a buffer
				if ( $cff_reviews || $cff_videos_only || $cff_photos_only || ( $cff_albums_only && $cff_albums_source == 'photospage' ) || $cff_featured_post_active && ! empty( $cff_featured_post_id ) || $cff_album_active && ! empty( $cff_album_id ) ) {

					//If the limit isn't set then set it to be the number of posts defined
					if ( isset( $feed_options['limit'] ) && $feed_options['limit'] !== '' ) {
						$cff_post_limit = $cff_post_limit;
					} else {
						$cff_post_limit = intval( $show_posts );
					}
					if ( $cff_post_limit >= 100 ) {
						$cff_post_limit = 100;
					}

					//If it's a grid feed with the pag method set to be cursor then force the limit to be the same as the number of posts
					if ( $feed_options['gridpag'] == 'cursor' ) {
						$cff_post_limit = $show_posts;
					}
				}

				//REVIEWS
				if ( $cff_reviews ) {
					( $feed_options['reviewsmethod'] == 'all' ) ? $show_all_reviews = true : $show_all_reviews = false;
					if ( $show_all_reviews == true ) {
						$cff_post_limit = '300';
					}
					$cff_posts_json_url = cff_reviews_url( $page_id, $page_access_token, $cff_post_limit, $cff_locale, $cff_date_range );
				}


				$cff_video_playlist = $feed_options['playlist'];
				$videos_id          = $page_id;
				if ( $cff_video_playlist == true ) {
					$videos_id = $cff_video_playlist;
				}

				//VIDEOS ONLY
				if ( $cff_videos_only ) {
					$cff_posts_json_url = 'https://graph.facebook.com/v3.2/' . $videos_id . '/videos/?access_token=' . $access_token . '&fields=source,created_time,title,description,embed_html,format{picture}&locale=' . $cff_locale . $cff_date_range . '&limit=' . $cff_post_limit;
				}

				//PHOTOS ONLY
				if ( $cff_photos_only ) {
					//Photo only feeds only work for pages since Facebook deprecated FQL
					$cff_posts_json_url = 'https://graph.facebook.com/' . $page_id . '/photos?type=uploaded&fields=id,created_time,link,picture,images{width,source},name&limit=' . $cff_post_limit . '&access_token=' . $access_token . $cff_date_range;
				}

				//ALBUMS ONLY
				if ( $cff_albums_only && $cff_albums_source == 'photospage' ) {
					$cff_posts_json_url = 'https://graph.facebook.com/' . $page_id . '/albums?fields=id,name,description,link,cover_photo{source,id},count,created_time,updated_time&access_token=' . $access_token . '&limit=' . $cff_post_limit . '&locale=' . $cff_locale . $cff_date_range;

					if ( $cff_is_group ) {
						$cff_posts_json_url = 'https://graph.facebook.com/' . $page_id . '/albums?fields=created_time,name,count,cover_photo,link,modified,id&access_token=' . $access_token . '&limit=' . $cff_post_limit . '&locale=' . $cff_locale . $cff_date_range;
					}
				}

				//Featured Post extension
				if ( $cff_featured_post_active && ! empty( $cff_featured_post_id ) ) {
					$cff_posts_json_url = cff_featured_post_id( trim( $cff_featured_post_id ), $access_token );
				}

				//ALBUM EMBED
				if ( $cff_album_active && ! empty( $cff_album_id ) ) {
					//Get the JSON back from the Album extension
					$cff_posts_json_url = cff_album_id( trim( $cff_album_id ), $access_token, $cff_post_limit, $cff_date_range );
				}

			} //END ALL POSTS


			//********************************************//
			//*********CREATE THE TRANSIENT NAME**********//
			//********************************************//

			//EVENTS ONLY
			if ( $cff_events_only && $cff_events_source == 'eventspage' ) {

				$events_trans_items_arr = array(
					'page_id'    => $page_id,
					'post_limit' => substr( $cff_post_limit, 0, 3 ),
					'page_type'  => $cff_page_type
				);

				$trans_arr_item_count = 1;
				// $cff_ext_date_active = true;
				if ( $cff_ext_date_active ) {
					$events_trans_items_arr['from']  = $cff_date_from;
					$events_trans_items_arr['until'] = $cff_date_until;
					// $events_trans_items_arr['from'] = '1234567890';
					// $events_trans_items_arr['until'] = '0987654321';
					$trans_arr_item_count = $trans_arr_item_count + 2;
				}
				if ( $cff_featured_post_active && ! empty( $cff_featured_post_id ) ) {
					$events_trans_items_arr['featured_post'] = $cff_featured_post_id;
					$trans_arr_item_count ++;
				}
				if ( $cff_past_events ) {
					$events_trans_items_arr['past_events'] = $cff_past_events;
				}

				$arr_item_max_length      = floor( 32 / $trans_arr_item_count ); //Max length of 45 accounting for the 'cff_ej_' prefix and other options below
				$arr_item_max_length_half = floor( $arr_item_max_length / 2 );

				$transient_name = 'cff_ej_';
				foreach ( $events_trans_items_arr as $key => $value ) {
					if ( $value !== false ) {
						if ( $key == 'page_id' || $key == 'featured_post' || $key == 'from' || $key == 'until' ) {
							$transient_name .= substr( $value, 0, $arr_item_max_length_half ) . substr( $value, $arr_item_max_length_half * - 1 );  //-10
						}
						if ( $key == 'post_limit' ) {
							$transient_name .= substr( $value, 0, 3 );
						}
						if ( $key == 'page_type' || $key == 'past_events' ) {
							$transient_name .= substr( $value, 0, 1 );
						}
					}
				}
				//Make sure it's not more than 45 chars
				$transient_name = substr( $transient_name, 0, 45 );

			} //END EVENTS ONLY

			//ALL POSTS
			if ( ! $cff_events_only || ( $cff_events_only && $cff_events_source == 'timeline' ) ) {

				//If it's a playlist then use the playlist ID instead of the Page ID
				$page_id_caching = $page_id;
				if ( $feed_options['playlist'] ) {
					$page_id_caching = $feed_options['playlist'];
				}

				$trans_items_arr = array(
					'page_id'       => $page_id_caching,
					'post_limit'    => substr( $cff_post_limit, 0, 3 ),
					'show_posts_by' => substr( $show_posts_by, 0, 2 ),
					'locale'        => $cff_locale
				);

				$trans_arr_item_count = 1;
				if ( $cff_ext_date_active ) {
					$trans_items_arr['from']  = $cff_date_from;
					$trans_items_arr['until'] = $cff_date_until;
					$trans_arr_item_count     = $trans_arr_item_count + 2;
				}
				if ( $cff_featured_post_active && ! empty( $cff_featured_post_id ) ) {
					$trans_items_arr['featured_post'] = $cff_featured_post_id;
					$trans_arr_item_count ++;
				}
				if ( $cff_albums_only ) {
					$trans_items_arr['albums_source'] = $cff_albums_source;
				}
				$trans_items_arr['albums_only'] = intval( $cff_albums_only );
				$trans_items_arr['photos_only'] = intval( $cff_photos_only );
				$trans_items_arr['videos_only'] = intval( $cff_videos_only );
				$trans_items_arr['reviews']     = intval( $cff_reviews );

				$arr_item_max_length      = floor( 28 / $trans_arr_item_count ); //40 minus the 12 needed for the other 7 values shown below equals 28
				$arr_item_max_length_half = floor( $arr_item_max_length / 2 );

				$transient_name = 'cff_';
				foreach ( $trans_items_arr as $key => $value ) {
					if ( $value !== false ) {
						if ( $key == 'page_id' || $key == 'featured_post' || $key == 'from' || $key == 'until' ) {
							$transient_name .= substr( $value, 0, $arr_item_max_length_half ) . substr( $value, $arr_item_max_length_half * - 1 );
						}  //-10
						if ( $key == 'locale' ) {
							$transient_name .= substr( $value, 0, 2 );
						}
						if ( $key == 'post_limit' || $key == 'show_posts_by' ) {
							$transient_name .= substr( $value, 0, 3 );
						}
						if ( $key == 'albums_only' || $key == 'photos_only' || $key == 'videos_only' || $key == 'albums_source' || $key == 'reviews' ) {
							$transient_name .= substr( $value, 0, 1 );
						}
					}
				}
				//Make sure it's not more than 45 chars
				$transient_name = substr( $transient_name, 0, 45 );

				//ALBUM EMBED
				if ( $cff_album_active && ! empty( $cff_album_id ) ) {
					$transient_name = 'cff_album_' . $cff_album_id . '_' . $cff_post_limit;
					$transient_name = substr( $transient_name, 0, 45 );
				}

			} //END ALL POSTS

			//Are there more posts to get for this ID?
			$cff_more_posts = true;

			//If the cron caching is enabled then set the caching time to be long so that it doesn't expire before rechecked in the cron function
			if( $cff_caching_type == 'background' ) $cache_seconds = 7 * DAY_IN_SECONDS;

			//Get next set of posts
			if( !is_null( $next_urls_arr_safe ) ) {

				//Get the corresponding next URL from the array of next URLs by using the page_id
				if ( ! empty( $next_urls_arr_safe ) ) {
					if ( isset( $next_urls_arr_safe[ $page_id ] ) ) {
						$next_url_safe = $next_urls_arr_safe[ $page_id ];
					} else {
						$next_url_safe  = '';
						$cff_more_posts = false;
					}
				} else {
					$next_url_safe = '';
				}

				//There are more posts to get for this ID
				if ( $cff_more_posts ) {

					//If it's a reviews feed then use the reviews token
					( $cff_reviews ) ? $feed_token = $feed_options['pagetoken'] : $feed_token = $access_token;

					//Replace the Access Token placeholder with the actual token
					$cff_api_url = str_replace( "x_cff_hide_token_x", $feed_token, $next_url_safe );


					//Get the "until" param from the URL to use in the transient name so that it's unique
					$url_bits = parse_url( $cff_api_url, PHP_URL_QUERY );
					parse_str( $url_bits, $url_bits_arr );

					//Use a unique string in the transient name so that it's saved in a separate unique transient
					if ( isset( $url_bits_arr['until'] ) ) {
						$cff_unique_string = $url_bits_arr['until'];
					} else if ( isset( $url_bits_arr['after'] ) ) {
						$cff_unique_string = $url_bits_arr['after'];
						if ( strlen( $cff_unique_string ) > 15 ) {
							$cff_unique_string = substr( $cff_unique_string, - 15 );
						}
					} else if ( isset( $url_bits_arr['offset'] ) ) {
						$cff_unique_string = $url_bits_arr['offset']; //As this is used with multifeed photo-only feeds with pagination then it doesn't work too well as those feeds are shuffled and so the offset changes. It's still sometimes right, but not all the time as it's partially random.
					} else {
						$cff_unique_string = '';
					}

					$transient_name = $transient_name . '_' . $cff_unique_string;
				}
			}
		}

		$this->transient_name = $transient_name;


		$this->feed_type_and_terms['generic'][] = array(
			'transient' 	=> str_replace( 'cff_', '', $transient_name ),
			'term' 			=> '',
			'params' 		=> array(),
			'page_ids' 		=> $page_ids,
			'type' 			=> $cff_page_type
		);
	}


	public static function feed_type_and_terms_display($connected_accounts, $result, $database_settings) {
		$feeds_list =  explode(',', $result['feed_id']);
		$types = []; $names =[];
		foreach ($feeds_list as $feed_id) {
			$account_settings = new CFF_FB_Settings(json_decode($result['shortcode_atts']), $database_settings);
			$account_settings->set_page_id($feed_id);
			$account_info =	$account_settings->get_id_and_token();
			if(!in_array($account_info['pagetype'], $types)){
				array_push($types, $account_info['pagetype']);
			}
			$account_name = (isset($account_info['name']) && $account_info['name'] != '') ? urldecode($account_info['name']) : $feed_id;
			array_push($names, $account_name);

		}
		return [
			'type' 	=> $types,
			'name' 	=> $names,
		];
	}


}