<?php
/**
 * Custom Facebook Feed Main Shortcode Class
 *
 * @since 3.18
 */

namespace CustomFacebookFeed;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class CFF_Shortcode extends CFF_Shortcode_Display{


	/**
	 * @var array
	 */
	protected $atts;

	/**
	 * @var array
	 */
	protected $options;

	/**
	 * @var id
	 */
	protected $page_id;

	/**
	 * @var string
	 */
	protected $access_token;


	/**
	 * @var array
	 */
	protected $feed_options;




	/**
	 * Shortcode constructor
	 *
	 * @since 3.18
	 */
	public function __construct(){
		$this->init();
	}


	/**
	 * Init.
	 *
	 * @since 3.18
	 */
	public function init(){
		add_shortcode('custom-facebook-feed', array($this, 'display_cff'));

		// Ajax Calls To Load More Posts
		add_action( 'wp_ajax_nopriv_cff_get_new_posts', array($this, 'cff_get_new_posts') );
		add_action( 'wp_ajax_cff_get_new_posts', array($this, 'cff_get_new_posts') );

		add_action( 'wp_ajax_cff_resized_images_submit', array($this, 'cff_process_submitted_resize_ids') );
		add_action( 'wp_ajax_nopriv_cff_resized_images_submit', array($this, 'cff_process_submitted_resize_ids') );

	}


	/**
	 * Get Urls Array
	 *
	 * @since 3.18
	 */
	function cff_get_prev_url_parts( $json_data_arr ){
		//Create the prev URLs array to add to the button
	    $prev_urls_arr_safe = '{';
	    $json_data = [];
	    if ( !empty($json_data_arr) ) {
	        //Loop through $json_data_arr and create a JSON string of the prev URLs to return and use in the pag button
	        foreach ( $json_data_arr as $page_id => $json_data ) {
	            if(isset($json_data->api_url)){
	                $prev_url = $json_data->api_url;

	                //Hide the Access Tokens in the URLs
	                $url_queries = parse_url($prev_url, PHP_URL_QUERY);
	                parse_str($url_queries, $output);

	                //If the URL is encoded then encode the Access Token so that it matches when searching
	                if (strpos($prev_url, '%7C') !== false) {
	                    $replace_token = urlencode( $output['access_token'] );
	                } else {
	                    $replace_token = $output['access_token'];
	                }

	                //Hide the token in the URL
	                $safe_prev_url = str_replace($replace_token,"x_cff_hide_token_x",$prev_url);

	                //Add it to the JSON string to be returned
	                $prev_urls_arr_safe .= '&quot;'.$page_id.'&quot;: &quot;'.$safe_prev_url.'&quot;, ';
	            }
	            $json_data = $json_data;

	        }
	    }
	    $prev_urls_arr_safe .= '}';
	    //If the array ends in a comma then remove the comma
	    if( substr($prev_urls_arr_safe, -3) == ', }' ) $prev_urls_arr_safe = str_replace(", }", "}", $prev_urls_arr_safe);
		return [
			'prev_urls_arr_safe' => $prev_urls_arr_safe,
			'json_data' => $json_data
		];
	}

	/**
	 * Whether or not this feed is meant to use the new settings
	 * or legacy settings
	 *
	 * @param array $feed_options
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function using_legacy_feed( $feed_options ) {
		$cff_statuses = get_option( 'cff_statuses', array() );

		if ( isset( $cff_statuses['support_legacy_shortcode'] )
			&& is_array( $cff_statuses['support_legacy_shortcode'] )) {
			return empty( $feed_options['feed'] );
		}

		if ( empty( $cff_statuses['support_legacy_shortcode'] ) ) {
			return false;
		}

		return empty( $feed_options['feed'] );
	}

	/**
	 * If a single unique feed was detected when updating from version 3.x
	 * to version 4.0, a shortcode without a feed specified will be defaulted
	 * to feed=1
	 *
	 * @param $feed_options
	 *
	 * @return bool
	 */
	public function is_legacy_feed_one( $feed_options ) {
		$cff_statuses = get_option( 'cff_statuses', array() );

		if ( isset( $cff_statuses['support_legacy_shortcode'] )
		     && is_array( $cff_statuses['support_legacy_shortcode'] )) {
			return empty( $feed_options['feed'] );
		}

		return false;
	}

	/**
	 * For non-legacy feeds. Queries the new db tables to see if the feed
	 * exists and then converts the settings to what is usable by the plugin.
	 *
	 * @param array $feed_options
	 *
	 * @return array|bool
	 *
	 * @since 4.0
	 */
	function get_settings_for_feed( $feed_options ) {

		if ( ! is_array( $feed_options ) ) {
			$feed_options = array();
		}

		if ( $this->is_legacy_feed_one( $feed_options ) ) {
			$feed_options['feed'] = 1;
		}

		if ( ! $this->using_legacy_feed( $feed_options ) ) {

			$feed_id = isset( $feed_options['feed'] ) ? $feed_options['feed'] : false;

			if ( empty( $feed_id ) ) {
				$feed_list = \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_feed_list();

				if ( count( $feed_list ) === 1 ) {
					$feed_saver = new \CustomFacebookFeed\Builder\CFF_Feed_Saver( $feed_list[0]['id'] );
					$settings   = $feed_saver->get_feed_settings();
				} else {
					if (( current_user_can('editor') || current_user_can('administrator') ) ) {
						echo "<span id='cff-no-id'>" . sprintf( __( "It looks like you have more than one feed. Go to %sthis page%s and enter the intended feed ID in your shortcode like this: [custom-facebook-feed feed=YOUR_FEED_ID_HERE].", 'custom-facebook-feed' ), '<a href="' . esc_url( admin_url( 'admin.php?page=cff-feed-builder' ) ) . '">', '</a>' ) . "</span><br /><br />";
					}
					return false;
				}

			} else {
				$feed_saver = new \CustomFacebookFeed\Builder\CFF_Feed_Saver( $feed_id );
				$settings   = $feed_saver->get_feed_settings();
			}

			if ( empty( $settings ) ) {
				if (( current_user_can('editor') || current_user_can('administrator') ) ) {
					$feed_list = \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_feed_list();
					if ( empty( $feed_list ) ) {
						echo "<span id='cff-no-id'>" . sprintf( __( "It looks like you haven't set up a feed yet. Try going to %sthis page%s to create one and then enter the feed id in the shortcode like this [custom-facebook-feed feed=YOUR_FEED_ID_HERE].", 'custom-facebook-feed' ), '<a href="' . esc_url( admin_url( 'admin.php?page=cff-feed-builder' ) ) . '">', '</a>' ) . "</span><br /><br />";
					}
				}
				return false;
			} elseif ( empty( $settings['sources'] ) ) {
				if (( current_user_can('editor') || current_user_can('administrator') ) ) {
					echo "<span id='cff-no-source'>" . sprintf( __( "No source found for this feed. It looks like you may have removed the account this feed was using to display posts. Go to %sthis page%s, switch to the settings tab and click the sources menu item to manage sources for this feed.", 'custom-facebook-feed' ), '<a href="' . esc_url( admin_url( 'admin.php?page=cff-feed-builder&feed_id=' . (int) $feed_id ) ) . '">', '</a>' ) . "</span><br /><br />";
				}
				return $settings;
			}


			if ( empty( $settings['showpoststypes'] )
			     || $settings['showpoststypes'] === 'all' ) {
				$settings['type'] = 'links,events,videos,photos,albums,statuses';
			}

			if ( ! empty( $settings['feedtype'] ) && $settings['feedtype'] !== 'timeline' ) {
				$settings['type'] = $settings['feedtype'];
			}


		} else {
			$settings = CFF_FB_Settings::get_legacy_settings( $feed_options );

			if ( ! empty( $feed_options['type'] ) ) {
				$settings['feedtype'] = $feed_options['type'];
				$settings['type'] =  $feed_options['type'];

			} else {
				if ( empty( $settings['showpoststypes'] )
				     || $settings['showpoststypes'] === 'all' ) {
					$settings['type'] = 'links,events,videos,photos,albums,statuses';
				}

				if ( ! empty( $settings['feedtype'] ) && $settings['feedtype'] !== 'timeline' ) {
					$settings['type'] = $settings['feedtype'];
				}
			}

			$default_grid = [
				'albums',
				'videos',
				'photos',
				'singlealbum'
			];

			$type_setting_array = is_array( $settings['type'] ) ? array_filter( $settings['type'] ) : array_filter( explode( ',', $settings['type'] ) );

			$single_type = count( $type_setting_array ) === 1 ? $type_setting_array[0] : false;

			if ( ! empty( $feed_options['album'] ) ) {
				$single_type = 'singlealbum';
			}

			if ( $single_type ) {
				if ( in_array( $single_type, $default_grid ) ) {
					if ( empty( $feed_options['feedlayout'] ) ) {
						$settings['feedlayout'] = 'grid';
					}
					if ( empty( $feed_options['cols'] ) ) {
						$colskey = substr( $single_type, 0, -1 ) . 'cols';
						$options_val = isset( $settings[ $colskey ] ) ? $settings[ $colskey ] : 4;
						$settings['cols'] = isset( $settings[ $colskey ] ) ? $settings[ $colskey ] : $options_val;
					}
				}
				$settings['feedtype'] = $single_type;
			}

		}

		$cff_includes = $settings['include'];
		$cff_excludes = $settings['exclude'];

		$cff_show_like_box = false;
		if ( is_string( $cff_includes ) ) {
			if ( CFF_Utils::stripos($cff_includes, 'likebox') !== false ) $cff_show_like_box = true;
		} elseif ( in_array( 'likebox', $cff_includes ) ) {
			$cff_show_like_box = true;
		}
		if ( is_string( $cff_excludes ) ) {
			if ( CFF_Utils::stripos($cff_excludes, 'likebox') !== false ) $cff_show_like_box = false;
		} elseif ( in_array( 'likebox', $cff_excludes ) ) {
			$cff_show_like_box = false;
		}

		if ( ! isset( $feed_options['include'] ) && ! isset( $feed_options['exclude'] )  ) {
			if ( ! empty( $settings['showlikebox'] ) ) {
				$settings['showlikebox'] = $settings['showlikebox'] === false || $settings['showlikebox'] === 'off' ? false : true;
			} else {
				$settings['showlikebox'] = $cff_show_like_box;
			}
		} else {
			$settings['showlikebox'] = $cff_show_like_box;
		}

		if ( ! $settings['showlikebox'] ) {
			$settings['include'] = str_replace( 'likebox,', ',', $settings['include'] );
		}

		if ( ! empty( $settings['headericonenabled'] ) && $settings['headericonenabled'] === 'off' ) {
			$settings['headericon'] = '';
		}

		if ( ! empty( $settings['disablelightbox'] ) && $settings['disablelightbox'] === 'on' ) {
			$settings['lightbox'] = 'off';
			$settings['disablelightbox'] = true;
		} else {
			$settings['lightbox'] = 'on';
			$settings['disablelightbox'] = false;
		}

		if ( ! empty( $settings['disablelightbox'] ) && $settings['disablelightbox'] === 'on' ) {
			$settings['lightbox'] = 'off';
			$settings['disablelightbox'] = true;
		}

		if ( ! empty( $settings['lightboxbgcolor'] )
		     || ! empty( $settings['lightboxtextcolor'] )
		     || ! empty( $settings['lightboxlinkcolor'] ) ) {
			$settings['customlightboxcolors'] = true;
		} else {
			$settings['customlightboxcolors'] = false;
		}

		if ( ! empty( $settings['beforedateenabled'] )
		     && $settings['beforedateenabled'] === 'off') {
			$settings['beforedate'] = '';
		}

		if ( ! empty( $settings['afterdateenabled'] )
		     && $settings['afterdateenabled'] === 'off') {
			$settings['afterdate'] = '';
		}

		if ( ! empty( $settings['apipostlimit'] )
		     && $settings['apipostlimit'] === 'auto') {
			$settings['limit'] = '';
		}

		if ( $settings['poststyle'] === 'regular' ) {
			$settings['boxshadow'] = false;
		}

		if ( isset( $feed_options['ajax'] ) ) {
			$settings['ajax'] = $feed_options['ajax'];
		} else {
			$settings['ajax'] = get_option( 'cff_ajax', '' );
		}
		$settings['locale'] = ( isset( $feed_options['locale'] ) ) ? $feed_options['locale'] : get_option( 'cff_locale', 'en_US' );

		// Default Timezone
		$defaults = array(
			'cff_timezone' => 'America/Chicago',
			'gdpr' => 'auto',
			'cff_show_credit' => false,
			'cff_format_issue' => '',
			'cff_disable_resize' => false,
			'disable_admin_notice' => false
		);
		$style_options = get_option( 'cff_style_settings', $defaults );
		$settings['timezone'] = (isset($style_options[ 'cff_timezone' ])) ?  $style_options[ 'cff_timezone' ] :  $defaults[ 'cff_timezone' ];
		$settings['gdpr'] = (isset($style_options[ 'gdpr' ])) ?  $style_options[ 'gdpr' ] :  $defaults[ 'gdpr' ];
		$settings['credit'] = (isset($style_options[ 'cff_show_credit' ])) ?  $style_options[ 'cff_show_credit' ] :  $defaults[ 'cff_show_credit' ];
		$settings['textissue'] = (isset($style_options[ 'cff_format_issue' ])) ?  $style_options[ 'cff_format_issue' ] :  $defaults[ 'cff_format_issue' ];
		$settings['disableresize'] = (isset($style_options[ 'cff_disable_resize' ])) ?  $style_options[ 'cff_disable_resize' ] :  $defaults[ 'cff_disable_resize' ];

		$settings = $this->filter_extensions_compatibility( $settings, $feed_options );

		$maybe_legacy_shortcode = $feed_options;
		if ( isset( $maybe_legacy_shortcode['feed'] ) ) {
			unset( $maybe_legacy_shortcode['feed'] );
		}

		// Merge in legacy settings (shortcode only settings)
		if ( ! empty( $maybe_legacy_shortcode ) ) {
			$legacy_shortcode_settings = [
				'width',
				'widthresp',
				'mediaposition',
				'masonryjs',
				'colsjs',
				'textformat',
				//all text weight settings
				'textweight',
				'descweight',
				'eventtitleweight',
				'eventdateweight',
				'eventdetailsweight',
				'dateweight',
				'linkweight',
				'headertextweight',
				'posttags',
				'linkhashtags',
				'offset',
				'eventoffset',
				'cff_enqueue_with_shortcode',
				'commentsnum',
				'restrictedpage',
				'hidesupporterposts',
				'privategroup',
				'fulllinkimages',
				'linkimagesize',
				'postimagesize',
				'videoheight',
				'videoaction',
				'videoplayer',
				'class',
				'padding',
				'cachetime'
			];

			foreach ( $maybe_legacy_shortcode as $maybe_legacy => $value ) {
				if ( in_array( $maybe_legacy, $legacy_shortcode_settings, true ) ) {
					$settings[ $maybe_legacy ] = $value;
				}
			}
		}

		if ( isset( $feed_options['cachetime'] ) ) {
			$settings['cacheunit'] = 'minutes';
			$settings['cachetime'] = intval( $settings['cachetime'] ) > 0 ? intval( $settings['cachetime'] ) : 1;
			$settings['cachetype'] = 'page';
		}

		if ( $settings['lightboxcomments'] === 'off' ) {
			$settings['lightboxcomments'] = false;
		}

		if ( $settings['posttags'] === '' ) {
			$settings['posttags'] = true;
		}

		if ( $settings['posttags'] === 'false' ) {
			$settings['posttags'] = false;
		}

		if ( $settings['linkhashtags'] === 'false' ) {
			$settings['linkhashtags'] = false;
		}

		if ( ! empty( $settings['sources'] ) && is_array( $settings['sources'] ) ) {
			foreach ( $settings['sources'] as $source ) {
				if ( is_array( $source ) ) {
					if ( (string)$settings['id'] === (string)$source['account_id'] ) {
						$settings['pagetype'] = $source['account_type'];
					}
				}
			}
		}

		return \CustomFacebookFeed\Builder\CFF_Post_Set::builder_to_general_settings_convert( $settings );
	}

	/**
	 * Provides backwards compatibility for extensions
	 *
	 * @param array $settings
	 * @param array $atts
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function filter_extensions_compatibility( $settings, $atts ) {
		if ( \CustomFacebookFeed\CFF_FB_Settings::check_active_extension( 'carousel' ) ) {

			$settings['carousel']           = isset( $atts['carousel'] ) ? $atts['carousel'] : $settings['feedlayout'] === 'carousel';
			$settings['carouselheight']     = isset( $atts['carouselheight'] ) ? $atts['carouselheight'] : $settings['carouselheight'];
			$settings['carouselcols']       = isset( $atts['carouselcols'] ) ? $atts['carouselcols'] : $settings['carouseldesktop_cols'];
			$settings['carouselmobilecols'] = isset( $atts['carouselmobilecols'] ) ? $atts['carouselmobilecols'] : $settings['carouselmobile_cols'];
			$settings['carouselarrows']     = isset( $atts['carouselarrows'] ) ? $atts['carouselarrows'] : $settings['carouselnavigation'];
			$settings['carouselpag']        = isset( $atts['carouselpag'] ) ? $atts['carouselpag'] : $settings['carouselpagination'];
			$settings['carouselautoplay']   = isset( $atts['carouselautoplay'] ) ? $atts['carouselautoplay'] : $settings['carouselautoplay'];
			$settings['carouseltime']       = isset( $atts['carouseltime'] ) ? $atts['carouseltime'] : $settings['carouselinterval'];

			// Masonry and Carousel feeds are incompatible so we check to see if carousel is active
			// and set Masonry to false if it is
			if ( $settings['carousel'] ) {
				$settings['headeroutside']  = true;
				$settings['likeboxoutside'] = true;
				$settings['masonry']        = false;

				// Carousel feeds are incompatible with the columns setting for the main plugin
				$settings['columnscompatible'] = false;
			}
		}

		return $settings;
	}

	/**
	 * Options.
	 * get Processed Options
	 *
	 * @since 3.18
	 */
	function cff_get_processed_options( $feed_options ){
	    $cff_ext_multifeed_active           = CFF_FB_Settings::check_active_extension( 'multifeed' );
	    $cff_ext_date_active                = CFF_FB_Settings::check_active_extension( 'date_range' );
	    $cff_featured_post_active           = CFF_FB_Settings::check_active_extension( 'featured_post' );
	    $cff_album_active                   = CFF_FB_Settings::check_active_extension( 'album' );
	    $cff_masonry_columns_active         = '';
	    $cff_carousel_active                = CFF_FB_Settings::check_active_extension( 'carousel' );
	    $cff_reviews_active                 = CFF_FB_Settings::check_active_extension( 'reviews' );

		//if ( ! $this->using_legacy_feed( $feed_options ) ) {
		$feed_id = empty( $feed_options['feed'] ) ? 'default' : intval( $feed_options['feed'] );
		$feed_options = $this->get_settings_for_feed( $feed_options );

		if ( empty( $feed_options ) ) {
			$options 		= get_option('cff_style_settings');
			$fdo 			= new CFF_FB_Settings($feed_options, $options);
			$feed_options 	= $fdo->get_settings();
			$feed_options['feederror'] = $feed_id;
			return $feed_options;
		}

	    //Fix the Page ID if they use the full URL
	    //If user pastes their full URL into the Page ID field then strip it out
	    $page_id = $feed_options['id'];
	    $cff_facebook_string = 'facebook.com';
	    ( stripos($page_id, $cff_facebook_string) !== false) ? $cff_page_id_url_check = true : $cff_page_id_url_check = false;
	    if ( $cff_page_id_url_check === true ) {
	        //Remove trailing slash if exists
	        $page_id = preg_replace('{/$}', '', $page_id);
	        //Get last part of url
	        $page_id = substr( $page_id, strrpos( $page_id, '/' )+1 );
	    }
	    //If the Page ID contains a query string at the end then remove it
	    if ( stripos( $page_id, '?') !== false ) $page_id = substr($page_id, 0, strrpos($page_id, '?'));

	    //Always remove slash from end of Page ID
	    $page_id = preg_replace('{/$}', '', $page_id);

	    //Update the page ID in the feed options array for use everywhere
	    $feed_options['id'] = $page_id;


	    //If an 'account' is specified then use that instead of the Page ID/token from the settings
	    $cff_account = trim($feed_options['account']);

	    if( !empty( $cff_account ) ){
	        $cff_connected_accounts = get_option('cff_connected_accounts');
	        if( !empty($cff_connected_accounts) ){

	            //Replace both single and double quotes before decoding
	            $cff_connected_accounts = str_replace('\"','"', $cff_connected_accounts);
	            $cff_connected_accounts = str_replace("\'","'", $cff_connected_accounts);

	            $cff_connected_accounts = json_decode( $cff_connected_accounts );

	            if( $cff_ext_multifeed_active ){

	                //Set the ID to be the comma-sep list of account IDs in the shortcode
	                $feed_options['id'] = $cff_account;

	                //Loop through each account and create the access token format
	                $multifeed_access_token_format = '';
	                foreach ( $cff_connected_accounts as $account ) {
	                    $multifeed_access_token_format .= $account->id.':'.$account->accesstoken.',';
	                }
	                $feed_options['accesstoken'] = $multifeed_access_token_format;

	            } else {
	            	if ( isset( $cff_account ) && is_object( $cff_connected_accounts ) ) {

			            //Grab the ID and token from the connected accounts setting
			            if( isset( $cff_connected_accounts->{ $cff_account } ) ){
	                        $feed_options['id'] = $cff_connected_accounts->{ $cff_account }->{'id'};
	                        $feed_options['accesstoken'] = $cff_connected_accounts->{ $cff_account }->{'accesstoken'};
	                    }

		            }

	            }

	            //Replace the encryption string in the Access Token
	            if (strpos($feed_options['accesstoken'], '02Sb981f26534g75h091287a46p5l63') !== false) {
	                $feed_options['accesstoken'] = str_replace("02Sb981f26534g75h091287a46p5l63","",$feed_options['accesstoken']);
	            }
	        }
	    }


		//If multiple Access Tokens are being used then split them up into an associative array
		$access_token = $feed_options['accesstoken'];
		if ( is_array( $access_token ) ) {
			if ( ! $cff_ext_multifeed_active ) {
				$access_token = reset($access_token);
			}
		} elseif ( $cff_ext_multifeed_active && strpos($access_token, ':') !== false ) {

	    	//Define the array
	    	$access_token_multiple = array();

	    	//If there are multiple tokens then split them up
	    	if( strpos($access_token, ',') !== false ){
	    		$access_token_pieces = explode(",", $access_token);
		    	foreach ( $access_token_pieces as $at_piece ) {
			    	$access_token_multiple = CFF_Utils::cffSplitToken($at_piece, $access_token_multiple);
			    }
	    	} else {
	    	//Otherwise just create a 1 item array
	    		$access_token_multiple = CFF_Utils::cffSplitToken($access_token);
	    	}
	    	//Save the token back into the settings array
		    $feed_options['accesstoken'] = $access_token_multiple;

	    } else {
	    	//Replace the encryption string in the Access Token
		    if (strpos($feed_options['accesstoken'], '02Sb981f26534g75h091287a46p5l63') !== false) {
		        $feed_options['accesstoken'] = str_replace("02Sb981f26534g75h091287a46p5l63","",$feed_options['accesstoken']);
		    }
	    }


	    //If it's a date range feed then disable the pastevents setting as it causes an issue
	    if( !empty($feed_options['from']) || !empty($feed_options['until']) ){
	        $feed_options['pastevents'] = 'false';
	    }

	    //If the reviews api method is set to be 'auto' then change it based on whether the user is filtering the posts
	    $cff_reviews_no_text = $feed_options[ 'reviewshidenotext' ];
	    ( $cff_reviews_no_text == 'on' || $cff_reviews_no_text == 'true' || $cff_reviews_no_text == true ) ? $cff_reviews_no_text = true : $cff_reviews_no_text = false;
	    if( $feed_options[ 'reviewshidenotext' ] == 'false' ) $cff_reviews_no_text = false;

	    if( $feed_options['reviewsmethod'] == 'auto' && ( $cff_reviews_no_text || substr_count($feed_options[ 'reviewsrated' ], ',') < 4 ) ) $feed_options['reviewsmethod'] = 'all';

	    $cff_connected_accounts = get_option('cff_connected_accounts');
	    if(!empty($cff_connected_accounts)){
	    	$connected_accounts = (array)json_decode(stripcslashes($cff_connected_accounts));
	    	if(array_key_exists($feed_options['id'], $connected_accounts)){
	    		$feed_options['pagetype'] = $connected_accounts[$feed_options['id']]->pagetype;
	    	}
	    }

	    if ( ! empty( $feed_options['sources'] ) && is_array( $feed_options['sources'] ) ) {
	    	foreach ( $feed_options['sources'] as $source ) {
	    		if ( (string)$feed_options['id'] === (string)$source['account_id'] ) {
				    $feed_options['pagetype'] = $source['account_type'];
			    }
		    }
	    }

		if ( ! empty( $feed_options['feedlayout'] ) ) {
			if ( $feed_options['feedlayout'] === 'list' ) {
				$feed_options['cols'] = 1;
				$feed_options['colsmobile'] = 1;
				$feed_options['colstablet'] = 1;
				$feed_options['albumcols'] = 1;
				$feed_options['photocols'] = 1;
				$feed_options['videocols'] = 1;
				$feed_options['masonrycols'] = 1;
				$feed_options['masonrycolsmobile'] = 1;
			} elseif ( $feed_options['feedlayout'] === 'masonry' ) {
				$feed_options['albumcols'] = $feed_options['cols'] ;
				$feed_options['photocols'] = $feed_options['cols'] ;
				$feed_options['videocols'] = $feed_options['cols'] ;
				$feed_options['masonrycols'] = $feed_options['cols'] ;
				$feed_options['masonrycolsmobile'] = $feed_options['colsmobile'];
			} elseif ( $feed_options['feedlayout'] === 'grid' && intval( $feed_id ) > 0 ) {
				$feed_options['albumcols'] = $feed_options['cols'] ;
				$feed_options['photocols'] = $feed_options['cols'] ;
				$feed_options['videocols'] = $feed_options['cols'] ;
				$feed_options['masonrycols'] = $feed_options['cols'] ;
				$feed_options['masonrycolsmobile'] = $feed_options['colsmobile'];
			}
		}

		return $feed_options;
	}

	/**
	 *  inside a data attribute
	 * formats the shortcode arguments into a json string that gets outputted into the html on the page later
	 *
	 * @since 3.18
	 */
	function cff_get_shortcode_data_attribute_html( $feed_options ) {
	    //If an access token is set in the shortcode then set "use own access token" to be enabled
	    if( isset($feed_options['accesstoken']) ){
	    	//Add an encryption string to protect token
	    	if ( strpos($feed_options['accesstoken'], ',') !== false ) {
	    		//If there are multiple tokens then just add the string after the colon to avoid having to de/reconstruct the array
		        $feed_options['accesstoken'] = str_replace(":", ":02Sb981f26534g75h091287a46p5l63", $feed_options['accesstoken']);
	    	} else {
	    		//Add an encryption string to protect token
		        $feed_options['accesstoken'] = substr_replace($feed_options['accesstoken'], '02Sb981f26534g75h091287a46p5l63', 25, 0);
	    	}
	    	$feed_options['ownaccesstoken'] = 'on';
	    }

	    if( !empty($feed_options) ){
	        $json_data = '{';
	        $i = 0;
	        $len = count($feed_options);
	        foreach( $feed_options as $key => $value ) {
	            if ($i == $len - 1) {
	                $json_data .= '&quot;'.$key.'&quot;: &quot;'.$value.'&quot;';
	            } else {
	                $json_data .= '&quot;'.$key.'&quot;: &quot;'.$value.'&quot;, ';
	            }
	            $i++;
	        }
	        $json_data .= '}';

		    return $json_data;
	    }
	}

	/**
	 * this where you could take the feed options to get the feed data for the first set
	 * of posts or, if the $before and $after parameters are set, get the next set of posts
	 *
	 * @since 3.18
	 */
	static function cff_get_json_data( $feed_options, $next_urls_arr_safe, $data_att_html, $is_customizer = false ) {
	    //Define vars
		$access_token = $feed_options['accesstoken'];
	    //If the 'Enter my own Access Token' box is unchecked then don't use the user's access token, even if there's one in the field
	    $feed_options['ownaccesstoken'] ? $cff_show_access_token = true : $cff_show_access_token = true;
	    //Reviews Access Token
	    $page_access_token = $feed_options['pagetoken'];
	    $cff_show_access_token = true;
		$page_id = trim( $feed_options['id'] );
	    $show_posts = isset( $feed_options['minnum'] ) ? $feed_options['minnum'] : $feed_options['num'];
	    $show_posts_number = isset( $feed_options['minnum'] ) ? $feed_options['minnum'] : $feed_options['num'];

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
	    #$cff_ext_date_active = $feed_options[ 'daterangeactive' ];
	    $cff_ext_date_active = CFF_FB_Settings::check_active_extension( 'date_range' ) && CFF_Utils::check_if_on($feed_options['daterange']);

	    $cff_featured_post_active = $feed_options[ 'featuredpostactive' ];
	    $cff_album_active = $feed_options[ 'albumactive' ];
	    $cff_masonry_columns_active = false; //Deprecated
	    $cff_carousel_active = $feed_options[ 'carouselactive' ];
	    $cff_reviews_active = $feed_options[ 'reviewsactive' ];
	    //Extension settings
	    $cff_album_id = $feed_options['album'];
	    $cff_featured_post_id = $feed_options['featuredpost'];
		$include_extras = isset( $feed_options['include_extras'] );

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

	    //Loop through page IDs
	    foreach ( $page_ids as $page_id ) {

	    	if( $cff_multiple_tokens ){
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
	        }

	        //********************************************//
	        //********CREATE THE API REQUEST URL**********//
	        //********************************************//

	        //These need to go here so that they're in the correct format for each ID looped through in multifeed. Otherwise they get converted to unix below and don't work for the second ID.
	        $cff_date_from = $feed_options['from'];
	        $cff_date_until = $feed_options['until'];

	        //EVENTS ONLY
	        if ($cff_events_only && $cff_events_source == 'eventspage'){

	            //Can be used to display group events passed their start time. Default is 6 hours.
	            $cff_event_offset_time = '-' . $cff_event_offset . ' hours';
	            $curtimeplus = strtotime($cff_event_offset_time, time());

	            //Start time string
	            $cff_start_time_string = '';
	            $cff_get_upcoming_events = "&time_filter=upcoming";

	            //Date range extension
	            if ( $cff_ext_date_active && ( !empty($cff_date_from) || !empty($cff_date_until) ) ) {

	                ( !empty($cff_date_from) ) ? $cff_date_from = strtotime($cff_date_from) : $cff_date_from = $curtimeplus;
	                ( !empty($cff_date_until) ) ? $cff_date_until = strtotime($cff_date_until) : $cff_date_until = $curtimeplus;

	                $cff_start_time_string = cff_ext_date($cff_date_from, $cff_date_until);
	                //If it's a date range then don't just query upcoming events, query all of them
	                $cff_get_upcoming_events = '';

	            }

	            //Events URL
	            $event_fields = 'id,name,attending_count,cover,start_time,end_time,event_times,timezone,place,description,ticket_uri,interested_count,updated_time,created_time';
	            if ( $include_extras ) {
		            $event_fields .= ',owner{picture,id,name,link}';
	            }
	            $cff_events_json_url = "https://graph.facebook.com/v3.2/".$page_id."/events/?fields=".$event_fields.$cff_start_time_string.$cff_get_upcoming_events."&limit=250&access_token=" . $access_token . "&format=json-strings" . $cff_ssl;

	            //Past events
	            ( $cff_past_events !== 'false' && $cff_past_events != false ) ? $cff_past_events = true : $cff_past_events = false;

	            //If the limit isn't set then set it to be the number of posts defined
	            if ( isset($feed_options['limit']) && $feed_options['limit'] !== '' ) {
	                $cff_post_limit = $cff_post_limit;
	            } else {
	                $cff_post_limit = intval($show_posts);
	            }
	            if( $cff_post_limit >= 100 ) $cff_post_limit = 100;

	            //Get past events. Limit must be set high to get all past events and be able to show the newest ones first
	            if($cff_past_events) $cff_events_json_url = 'https://graph.facebook.com/v3.2/'.$page_id.'/events?fields='.$event_fields.'&limit='.$cff_post_limit.'&time_filter=past&until='.$curtimeplus.'&access_token='.$access_token;

	            //Group events
	            if($cff_is_group && !$cff_past_events) $cff_events_json_url = 'https://graph.facebook.com/v3.2/' . $page_id . '/events?fields=name,id,description,created_time,updated_time,start_time,end_time,event_times,timezone,ticket_uri,place,cover,attending_count,interested_count&limit=200&since='.$curtimeplus.'&access_token=' . $access_token;

	            //Featured Post extension
	            if( $cff_featured_post_active && !empty($cff_featured_post_id) ) $cff_events_json_url = cff_featured_event_id( trim( $cff_featured_post_id ), $access_token );

	            //Assign it here so that it's returned at the end of the function
	            $cff_posts_json_url = $cff_events_json_url;

	        } //END EVENTS ONLY

	        //ALL POSTS
	        if (!$cff_events_only || ($cff_events_only && $cff_events_source == 'timeline') ){

	            //Create date range using the Date Range extension
	            ( $cff_ext_date_active ) ? $cff_date_range = cff_ext_date(strtotime( $cff_date_from ), strtotime( $cff_date_until )) : $cff_date_range = '';

	            //Fields which are only for pages or groups
	            if($cff_is_group){
	                $specific_fields = 'reactions.summary(true).limit(0),';
	            } else {
	                $specific_fields = 'likes.summary(true).limit(0),';
	            }

	            //Add option to load comments via JS to workaround Facebook "unknown error" bug caused by comments in the API request
	            $feed_options['loadcommentsjs'] == 'true' ? $comments_field = '' : $comments_field = 'comments.summary(true){message,created_time},';
	            $comments_field = ( $is_customizer ) ? 'comments.summary(true).limit(5){created_time,from{name,id,picture{url},link},id,message,message_tags,attachment,like_count},' : $comments_field;


	            //Add option to remove attachments description field to workaround Facebook "Unsupported Get Request" bug caused by sales posts in the API request
                $attachments_desc = ( $feed_options['salesposts'] == 'true' ) ? '' : ',description';

	            //Add option to remove story_tags to workaround Facebook "Unknown Error" message returned by API for certain posts
                $story_tags = ( $feed_options['storytags'] == 'true' ) ? '' : ',story_tags';
		        if ( is_array( $access_token ) ) {
			        $access_token = array_shift($access_token );
		        }
		        $cff_posts_json_url = 'https://graph.facebook.com/v4.0/' . $page_id . '/' . $graph_query . '?fields=id,updated_time,from{picture,id,name,link},message,message_tags,story'. $story_tags .',picture,full_picture,status_type,created_time,backdated_time,attachments{title'. $attachments_desc .',media_type,unshimmed_url,target{id},multi_share_end_card,media{source,image},subattachments},shares,'.$comments_field.$specific_fields.'call_to_action,privacy&access_token=' . $access_token . '&limit=' . $cff_post_limit . '&locale=' . $cff_locale . $cff_ssl . $cff_date_range;


	            //If the feed is not a timeline feed then set the post limit to be the same as the number of posts being shown as we don't need a buffer
	            if ( $cff_reviews || $cff_videos_only || $cff_photos_only || ($cff_albums_only && $cff_albums_source == 'photospage') || $cff_featured_post_active && !empty($cff_featured_post_id) || $cff_album_active && !empty($cff_album_id) ){

	                //If the limit isn't set then set it to be the number of posts defined
	                if ( isset($feed_options['limit']) && $feed_options['limit'] !== '' ) {
	                    $cff_post_limit = $cff_post_limit;
	                } else {
	                    $cff_post_limit = intval($show_posts);
	                }
	                if( $cff_post_limit >= 100 ) $cff_post_limit = 100;

	                //If it's a grid feed with the pag method set to be cursor then force the limit to be the same as the number of posts
	                if( $feed_options['gridpag'] == 'cursor') $cff_post_limit = $show_posts;
	            }

	            //REVIEWS
	            if ( $cff_reviews ){
	                ( $feed_options['reviewsmethod'] == 'all' ) ? $show_all_reviews = true : $show_all_reviews = false;
	                if( $show_all_reviews == true ) $cff_post_limit = '300';
	                $cff_posts_json_url = cff_reviews_url( $page_id, $page_access_token, $cff_post_limit, $cff_locale, $cff_date_range );
	            }


	            $cff_video_playlist = $feed_options['playlist'];
	            $videos_id = $page_id;
	            if( $cff_video_playlist == true ) $videos_id = $cff_video_playlist;

	            //VIDEOS ONLY
	            if($cff_videos_only){
	            	$from_field = $include_extras ? ',from{picture,id,name,link},length,likes.summary(true).limit(0),comments.summary(true).limit(0)' : '';
	                $cff_posts_json_url = 'https://graph.facebook.com/v3.2/'.$videos_id.'/videos/?access_token='.$access_token.'&fields=published,source,updated_time,created_time,title,description,embed_html,format{picture}'.$from_field.'&locale='.$cff_locale . $cff_date_range . '&limit=' . $cff_post_limit;
	            }

	            //PHOTOS ONLY
	            if($cff_photos_only){
	                //Photo only feeds only work for pages since Facebook deprecated FQL
		            $from_field = $include_extras ? ',from{picture,id,name,link},likes.summary(true).limit(0),comments.summary(true).limit(0)' : '';
	                $cff_posts_json_url = 'https://graph.facebook.com/'.$page_id.'/photos?type=uploaded&fields=id,updated_time,created_time,link,picture,images{width,source},name'.$from_field.'&limit='.$cff_post_limit.'&access_token='.$access_token.$cff_date_range;
	            }

	            //ALBUMS ONLY
	            if($cff_albums_only && $cff_albums_source == 'photospage'){
		            $from_field = $include_extras ? ',from{picture,id,name,link}' : '';
	                $cff_posts_json_url = 'https://graph.facebook.com/' . $page_id . '/albums?fields=id,name,description,link,cover_photo{source,id},count,created_time,updated_time'.$from_field.'&access_token=' . $access_token . '&limit=' . $cff_post_limit. '&locale=' . $cff_locale . $cff_date_range;

	                if($cff_is_group) $cff_posts_json_url = 'https://graph.facebook.com/' . $page_id . '/albums?fields=created_time,updated_time,name,count,cover_photo,link,modified,id'.$from_field.'&access_token=' . $access_token . '&limit=' . $cff_post_limit. '&locale=' . $cff_locale . $cff_date_range;
	            }

	            //Featured Post extension
	            if ( $cff_featured_post_active && !empty($cff_featured_post_id) ) $cff_posts_json_url = cff_featured_post_id( trim( $cff_featured_post_id ), $access_token );

	            //ALBUM EMBED
	            if( $cff_album_active && !empty($cff_album_id) ) {
	                //Get the JSON back from the Album extension
	                $cff_posts_json_url = cff_album_id( trim( $cff_album_id ), $access_token, $cff_post_limit, $cff_date_range );
	            }

	        } //END ALL POSTS


	        //********************************************//
	        //*********CREATE THE TRANSIENT NAME**********//
	        //********************************************//

	        //EVENTS ONLY
	        if ($cff_events_only && $cff_events_source == 'eventspage'){

	            $events_trans_items_arr = array(
	                'page_id' => $page_id,
	                'post_limit' => substr($cff_post_limit, 0, 3),
	                'page_type' => $cff_page_type
	            );

	            $trans_arr_item_count = 1;
	            // $cff_ext_date_active = true;
	            if($cff_ext_date_active){
	                $events_trans_items_arr['from'] = $cff_date_from;
	                $events_trans_items_arr['until'] = $cff_date_until;
	                // $events_trans_items_arr['from'] = '1234567890';
	                // $events_trans_items_arr['until'] = '0987654321';
	                $trans_arr_item_count = $trans_arr_item_count+2;
	            }
	            if( $cff_featured_post_active && !empty($cff_featured_post_id) ){
	                $events_trans_items_arr['featured_post'] = $cff_featured_post_id;
	                $trans_arr_item_count++;
	            }
	            if($cff_past_events) $events_trans_items_arr['past_events'] = $cff_past_events;

	            $arr_item_max_length = floor( 32/$trans_arr_item_count ); //Max length of 45 accounting for the 'cff_ej_' prefix and other options below
	            $arr_item_max_length_half = floor($arr_item_max_length/2);

	            $transient_name = 'cff_ej_';
	            foreach ($events_trans_items_arr as $key => $value) {
	                if($value !== false){
	                    if( $key == 'page_id' || $key == 'featured_post' || $key == 'from' || $key == 'until' ){
	                    $transient_name .= substr($value, 0, $arr_item_max_length_half) . substr($value, $arr_item_max_length_half*-1);  //-10
	                }
	                    if( $key == 'post_limit' ) $transient_name .= substr($value, 0, 3);
	                    if( $key == 'page_type' || $key == 'past_events' ) $transient_name .= substr($value, 0, 1);
	                }
	            }
	            //Make sure it's not more than 45 chars
	            $transient_name = substr($transient_name, 0, 45);

	        } //END EVENTS ONLY

	        //ALL POSTS
	        if (!$cff_events_only || ($cff_events_only && $cff_events_source == 'timeline') ){

	            //If it's a playlist then use the playlist ID instead of the Page ID
	            $page_id_caching = $page_id;
	            if( $feed_options['playlist'] ){
	                $page_id_caching = $feed_options['playlist'];
	            }

	            $trans_items_arr = array(
	                'page_id' => $page_id_caching,
	                'post_limit' => substr($cff_post_limit, 0, 3),
	                'show_posts_by' => substr($show_posts_by, 0, 2),
	                'locale' => $cff_locale
	            );

	            $trans_arr_item_count = 1;
	            if($cff_ext_date_active){
	                $trans_items_arr['from'] = $cff_date_from;
	                $trans_items_arr['until'] = $cff_date_until;
	                $trans_arr_item_count = $trans_arr_item_count+2;
	            }
	            if( $cff_featured_post_active && !empty($cff_featured_post_id) ){
	                $trans_items_arr['featured_post'] = $cff_featured_post_id;
	                $trans_arr_item_count++;
	            }
	            if($cff_albums_only) $trans_items_arr['albums_source'] = $cff_albums_source;
	            $trans_items_arr['albums_only'] = intval($cff_albums_only);
	            $trans_items_arr['photos_only'] = intval($cff_photos_only);
	            $trans_items_arr['videos_only'] = intval($cff_videos_only);
	            $trans_items_arr['reviews'] = intval($cff_reviews);

	            $arr_item_max_length = floor( 28/$trans_arr_item_count ); //40 minus the 12 needed for the other 7 values shown below equals 28
	            $arr_item_max_length_half = floor($arr_item_max_length/2);

	            $transient_name = 'cff_';
	            foreach ($trans_items_arr as $key => $value) {
	                if($value !== false){
	                    if( $key == 'page_id' || $key == 'featured_post' || $key == 'from' || $key == 'until' ) $transient_name .= substr($value, 0, $arr_item_max_length_half) . substr($value, $arr_item_max_length_half*-1);  //-10
	                    if( $key == 'locale' ) $transient_name .= substr($value, 0, 2);
	                    if( $key == 'post_limit' || $key == 'show_posts_by' ) $transient_name .= substr($value, 0, 3);
	                    if( $key == 'albums_only' || $key == 'photos_only' || $key == 'videos_only' || $key == 'albums_source' || $key == 'reviews' ) $transient_name .= substr($value, 0, 1);
	                }
	            }
	            //Make sure it's not more than 45 chars
	            $transient_name = substr($transient_name, 0, 45);

	            //ALBUM EMBED
	            if( $cff_album_active && !empty($cff_album_id) ) {
	                $transient_name = 'cff_album_' . $cff_album_id . '_' . $cff_post_limit;
	                $transient_name = substr($transient_name, 0, 45);
	            }

	        } //END ALL POSTS



	        //Are there more posts to get for this ID?
	        $cff_more_posts = true;

            // Set number of posts to API limit to ensure filtering properly works for group feeds.
            if($cff_is_group){                
                $show_posts_number = ( isset( $feed_options['limit'] ) && $feed_options['apipostlimit'] == 'manual' ) ? $feed_options['limit'] : $show_posts_number;
            }

	        //If the cron caching is enabled then set the caching time to be long so that it doesn't expire before rechecked in the cron function
	        if( $cff_caching_type == 'background' ) $cache_seconds = 7 * DAY_IN_SECONDS;

	        //Get next set of posts
	        if( !is_null( $next_urls_arr_safe ) ) {
	            //Get the corresponding next URL from the array of next URLs by using the page_id
	        	if( !empty($next_urls_arr_safe) ){
	        		if( isset($next_urls_arr_safe[$page_id]) ){
	        			$next_url_safe = $next_urls_arr_safe[$page_id];
	        		} else {
	        			$next_url_safe = '';
	        			$cff_more_posts = false;
	        		}
	        	} else {
	        		$next_url_safe = '';
	        	}

	        	if($cff_is_group){
	        		if(!empty($next_urls_arr_safe) && $next_urls_arr_safe != 0){
	        			$cff_more_posts = true;
	        		}else{
	        			$cff_more_posts = false;
	        		}
	        	}

	            //There are more posts to get for this ID
	            if( $cff_more_posts ){
	                //If it's a reviews feed then use the reviews token
	                ( $cff_reviews ) ? $feed_token = $feed_options['pagetoken'] : $feed_token = $access_token;

	                //Replace the Access Token placeholder with the actual token
	                $cff_api_url = str_replace("x_cff_hide_token_x",$feed_token,$next_url_safe);

	                //Get the "until" param from the URL to use in the transient name so that it's unique
	                $url_bits = parse_url($cff_api_url, PHP_URL_QUERY);
	                parse_str($url_bits, $url_bits_arr);

	                //Use a unique string in the transient name so that it's saved in a separate unique transient
	                if( isset($url_bits_arr['until']) ){
	                    $cff_unique_string = $url_bits_arr['until'];
	                } else if ( isset($url_bits_arr['after']) ) {
	                    $cff_unique_string = $url_bits_arr['after'];
	                    if( strlen($cff_unique_string) > 15 ) $cff_unique_string = substr($cff_unique_string, -15);
	                } else if ( isset($url_bits_arr['offset']) ) {
	                    $cff_unique_string = $url_bits_arr['offset']; //As this is used with multifeed photo-only feeds with pagination then it doesn't work too well as those feeds are shuffled and so the offset changes. It's still sometimes right, but not all the time as it's partially random.
	                } else {
	                    $cff_unique_string = '';
	                }
	                //Check the cache and/or make an API request
		            $is_multifeed = CFF_Shortcode::is_multifeed( $feed_options );

		            $posts_json = CFF_Utils::cff_get_set_cache($cff_api_url, $transient_name . '_' . $cff_unique_string, $cff_cache_time, $cache_seconds, $data_att_html, $cff_show_access_token, $access_token, false, $is_multifeed);
	               	if( $cff_is_group ){
						$groups_post = new CFF_Group_Posts($page_id, $feed_options, $cff_posts_json_url, $data_att_html, false);
						$latest_record_date = (isset($next_urls_arr_safe['latest_record_date'])) ? $next_urls_arr_safe['latest_record_date'] : 0;
						$groups_post_result = $groups_post->init_group_posts($posts_json, $latest_record_date, $show_posts_number);
						$posts_json = $groups_post_result['posts_json'];
					}
	                $FBdata = json_decode($posts_json);

	            }
	        //Get first set of posts
	        } else {
	            //EVENTS ONLY
	            if ($cff_events_only && $cff_events_source == 'eventspage'){
		            $is_multifeed = CFF_Shortcode::is_multifeed( $feed_options );

		            $events_json = CFF_Utils::cff_get_set_cache($cff_events_json_url, $transient_name, $cff_cache_time, $cache_seconds, $data_att_html, $cff_show_access_token, $access_token, false, $is_multifeed);
	                if( $cff_is_group ){
						$groups_post = new CFF_Group_Posts($page_id, $feed_options, $cff_events_json_url, $data_att_html, true);
						$groups_post_result = $groups_post->init_group_posts($events_json, false, $show_posts_number);
						$events_json = $groups_post_result['posts_json'];
					}

	                //Interpret data with JSON
	                //Convert id integer to a string otherwise json_decode returns it as a float
	                $events_json = preg_replace('/"id":(\d+)/', '"id":"$1"', $events_json);
	                //If it's a Featured Post event then wrap it in a "data" object so that it's parsed the same way as regular events in the loop below
	                if( $cff_featured_post_active && !empty($cff_featured_post_id) ) $events_json = '{"data": ['.$events_json.'] }';
	                $FBdata = json_decode($events_json);

	            } //End events URL formation

	            //ALL POSTS
	            if (!$cff_events_only || ($cff_events_only && $cff_events_source == 'timeline') ){
					$is_multifeed = CFF_Shortcode::is_multifeed( $feed_options );

		            $posts_json = CFF_Utils::cff_get_set_cache($cff_posts_json_url, $transient_name, $cff_cache_time, $cache_seconds, $data_att_html, $cff_show_access_token, $access_token, true, $is_multifeed);
	                if( $cff_is_group ){
						$groups_post = new CFF_Group_Posts($page_id, $feed_options, $cff_posts_json_url, $data_att_html, false);
						$groups_post_result = $groups_post->init_group_posts($posts_json, false, $show_posts_number);
						$posts_json = $groups_post_result['posts_json'];
					}
	                //ALBUM EMBED
	                if( $cff_album_active && !empty($cff_album_id) ) $album_json = $posts_json;

	                //Interpret data with JSON
	                $FBdata = json_decode($posts_json);

		            if ( $is_customizer ) {
		            	//Get more data about single events since timelines don't have it
		            	$can_check_for_events = false;
			            $event_supporting_access_tokens = array();
		            	if ( isset( $feed_options['sources'][0] ) ) {
		            		foreach ( $feed_options['sources'] as $source ) {
		            			#if ( $source['privilege'] === 'events' ) {
						            $can_check_for_events = true;
						            $event_supporting_access_tokens[ $source['account_id'] ] = $source['access_token'];
					           # }
				            }
			            }


		            	if ( $can_check_for_events ) {
							if ( isset( $FBdata->data ) ) {
								$post_index = 0;
								foreach ( $FBdata->data as $post ) {
									$type = CFF_Parse_Pro::get_status_type( $post );

									if ( $type === 'created_event' ) {
										$account_id = CFF_Parse_Pro::get_from_id( $post );
										$post_id = CFF_Parse_Pro::get_post_id( $post );
										$event_id = explode( '_', $post_id );
										$event_id = $event_id[1];

										if ( ! empty( $event_supporting_access_tokens[ $account_id ] ) ) {
											$single_data = CFF_Shortcode::get_single_event_data( $event_id, $event_supporting_access_tokens[ $account_id ] );
											foreach( $single_data as $property => $value ) {
												$FBdata->data[ $post_index ]->$property = $value;
											}

										}
									}
									$post_index++;
								}
							}
			            }
		            }
	                //ALBUM EMBED
	                if( $cff_album_active && !empty($cff_album_id) ) $FBdata = json_decode($album_json);

	            } // End ALL POSTS

	        } // End get more posts

	        if( $cff_more_posts ){
	            //Add the data to the array to be returned and parsed into HTML
	            $FBdata_arr[$page_id] = $FBdata;
	        } else {
	            //Add something to the array for the ID if there's no posts to prevent any PHP notices
	            $FBdata_arr[$page_id] = 'no_more_posts';
	        }

	        //Add the API URL to the json array so that we can grab it and add to the button if needed
	        if( isset($FBdata_arr[$page_id]) ){
	            if( !isset($FBdata_arr[$page_id]->api_url) && $FBdata_arr[$page_id] != 'no_more_posts' ){
	                //Replace the actual token with the Access Token placeholder
	                $cff_api_url = str_replace($access_token,"x_cff_hide_token_x",$cff_posts_json_url);
	                //Add it to the json array
	                $FBdata_arr[$page_id]->api_url = $cff_api_url;
	            }
	        }

	    } //End page_id loop

	    if($cff_is_group && isset($FBdata_arr[$page_id]) && $FBdata_arr[$page_id] != 'no_more_posts'){
	    	$FBdata_arr[$page_id]->is_load_cache = (isset($groups_post_result['latest_record_date'])) && $groups_post_result['latest_record_date'] !== 0 && $groups_post_result['load_from_cache'] !== false ? $groups_post_result['load_from_cache'] : false;
	    	$FBdata_arr[$page_id]->latest_record_date = $groups_post_result['latest_record_date'];
	    }
	    return $FBdata_arr;
	}

	public static function get_single_event_data( $eventID, $access_token ) {

		//Is it SSL?
		$cff_ssl = '';
		if (is_ssl()) $cff_ssl = '&return_ssl_resources=true';

		//Get the contents of the event
		$event_json_url = 'https://graph.facebook.com/v3.3/'.$eventID.'?fields=cover,place,name,owner,start_time,timezone,id,comments.summary(true){message,created_time},description&access_token=' . $access_token . $cff_ssl;

		// Get any existing copy of our transient data
		$transient_name = 'cff_tle_' . $eventID;
		$transient_name = substr($transient_name, 0, 45);

		if ( false === ( $event_json = get_transient( $transient_name ) ) || $event_json === null ) {
			//Get the contents of the Facebook page
			$event_json = CFF_Utils::cff_fetchUrl($event_json_url);
			//Cache the JSON for 180 days as the timeline event info probably isn't going to change
			set_transient( $transient_name, $event_json, 60 * 60 * 24 * 180 );
		} else {
			$event_json = get_transient( $transient_name );
			//If we can't find the transient then fall back to just getting the json from the api
			if ($event_json == false) $event_json = CFF_Utils::cff_fetchUrl($event_json_url);
		}

		//Interpret data with JSON
		$event_object = json_decode($event_json);

		$description_text = '';
		if( isset($event_object->name) ) $description_text .= $event_object->name . ' ';
		if( isset($event_object->place->location->city) ) $description_text .= $event_object->place->location->city . ' ';
		if( isset($event_object->place->location->country) ) $description_text .= $event_object->place->location->country . ' ';
		if( isset($event_object->place->location->street) ) $description_text .= $event_object->place->location->street . ' ';
		if( isset($event_object->place->name) ) $description_text .= $event_object->place->name . ' ';
		if( isset($event_object->description) ) $description_text .= $event_object->description;
		$event_object->description_text = $description_text;

		return $event_object;

	}



	/**
	 * this function breaks up the "next" url from the json data into an array of parts to load into
	 * the html to be retrieved on click and pieced back together
	 *
	 * @since 3.18
	 */
	static function cff_get_next_url_parts( $json_data_arr ) {
	    $next_urls_arr_safe = '{';
	    $latest_record_date = 0;
	    if ( !empty($json_data_arr) ) {
	        //Loop through $json_data_arr and create a JSON string of the next URLs to return and use in the pag button
	        foreach ( $json_data_arr as $page_id => $json_data ) {
	            if(isset($json_data->paging->next)){
	                $next_url = $json_data->paging->next;
	                //Hide the Access Tokens in the URLs
	                $url_queries = parse_url($next_url, PHP_URL_QUERY);
	                parse_str($url_queries, $output);
	                //If the URL is encoded then encode the Access Token so that it matches when searching
	                if (strpos($next_url, '%7C') !== false) {
	                    $replace_token = urlencode( $output['access_token'] );
	                } else {
	                    $replace_token = $output['access_token'];
	                }
	                //Hide the token in the URL
	                $safe_url = str_replace($replace_token,"x_cff_hide_token_x",$next_url);
	                //Add it to the JSON string to be returned
	                $next_urls_arr_safe .= '&quot;'.$page_id.'&quot;: &quot;'.$safe_url.'&quot;, ';
	            }
	            if(isset($json_data->latest_record_date)){
	    			$latest_record_date = $json_data->latest_record_date;
	            }

	        }
	    }
	    $next_urls_arr_safe .= ($latest_record_date != 0 ) ? '&quot;latest_record_date&quot;: &quot;'.$latest_record_date.'&quot;, ' : '';
	    $next_urls_arr_safe .= '}';
	    //If the array ends in a comma then remove the comma
	    if( substr($next_urls_arr_safe, -3) == ', }' ) $next_urls_arr_safe = str_replace(", }", "}", $next_urls_arr_safe);
	    return $next_urls_arr_safe;
	}

	function cff_add_translations() {
		$translations = get_option( 'cff_style_settings', false );

		$final_translations = [
			'buttontext'                => isset( $translations[ 'cff_load_more_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_load_more_text' ] ) ) : __( 'Load More', 'custom-facebook-feed' ),
			'seemoretext' 				=> isset( $translations[ 'cff_see_more_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_see_more_text' ] ) ) : __( 'See More', 'custom-facebook-feed' ),
			'seelesstext' 				=> isset( $translations[ 'cff_see_less_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_see_less_text' ] ) ) : __( 'See Less', 'custom-facebook-feed' ),
			'nomoretext' 				=> isset( $translations[ 'cff_no_more_posts_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_no_more_posts_text' ] ) ) : __( 'No more posts', 'custom-facebook-feed' ),
			'viewlinktext'              => isset( $translations[ 'cff_view_link_text' ] ) ? $translations[ 'cff_view_link_text' ] : 'View Link',

			'photostext' 				=> isset( $translations[ 'cff_translate_photos_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_photos_text' ] ) ) : __( 'photos', 'custom-facebook-feed' ),
			'facebooklinktext' 			=> isset( $translations[ 'cff_facebook_link_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_facebook_link_text' ] ) ) : __( 'View on Facebook', 'custom-facebook-feed' ),
			'sharelinktext' 			=> isset( $translations[ 'cff_facebook_share_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_facebook_share_text' ] ) ) : __( 'Share', 'custom-facebook-feed' ),
			'buyticketstext' 			=> isset($translations[ 'cff_buy_tickets_text' ]) ? $translations[ 'cff_buy_tickets_text' ] : __( 'Buy Tickets', 'custom-facebook-feed' ),

			'maptext' 					=> isset( $translations[ 'cff_map_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_map_text' ] ) ) : __( 'Map', 'custom-facebook-feed' ),
			'interestedtext' 			=> isset( $translations[ 'cff_interested_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_interested_text' ] ) ) : __( 'interested', 'custom-facebook-feed' ),
			'goingtext' 				=> isset( $translations[ 'cff_going_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_going_text' ] ) ) : __( 'interested', 'custom-facebook-feed' ),

			'previouscommentstext' 		=> isset( $translations[ 'cff_translate_view_previous_comments_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_view_previous_comments_text' ] ) ) : __( 'View more comments', 'custom-facebook-feed' ),
			'commentonfacebooktext' 	=> isset( $translations[ 'cff_translate_comment_on_facebook_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_comment_on_facebook_text' ] ) ) : __( 'Comment on Facebook', 'custom-facebook-feed' ),
			'likesthistext' 			=> isset( $translations[ 'cff_translate_likes_this_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_likes_this_text' ] ) ) : __( 'likes this', 'custom-facebook-feed' ),
			'likethistext' 				=> isset( $translations[ 'cff_translate_like_this_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_like_this_text' ] ) ) : __( 'like this', 'custom-facebook-feed' ),
			'reactedtothistext' 		=> isset( $translations[ 'cff_translate_reacted_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_reacted_text' ] ) ) : __( 'reacted to this', 'custom-facebook-feed' ),
			'andtext' 					=> isset( $translations[ 'cff_translate_and_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_and_text' ] ) ) : __( 'and', 'custom-facebook-feed' ),
			'othertext' 				=> isset( $translations[ 'cff_translate_other_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_other_text' ] ) ) : __( 'other', 'custom-facebook-feed' ),
			'otherstext' 				=> isset( $translations[ 'cff_translate_others_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_others_text' ] ) ) : __( 'others', 'custom-facebook-feed' ),
			'noeventstext' 				=> isset( $translations[ 'cff_no_events_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_no_events_text' ] ) ) : __( 'No upcoming events', 'custom-facebook-feed' ),
			'replytext' 				=> isset( $translations[ 'cff_translate_reply_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_reply_text' ] ) ) : __( 'Reply', 'custom-facebook-feed' ),
			'repliestext' 				=> isset( $translations[ 'cff_translate_replies_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_replies_text' ] ) ) : __( 'Replies', 'custom-facebook-feed' ),

			'learnmoretext' 			=> isset( $translations[ 'cff_translate_learn_more_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_learn_more_text' ] ) ) : __( 'Learn More', 'custom-facebook-feed' ),
			'shopnowtext' 				=> isset( $translations[ 'cff_translate_shop_now_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_shop_now_text' ] ) ) : __( 'Shop Now', 'custom-facebook-feed' ),
			'messagepage' 				=> isset( $translations[ 'cff_translate_message_page_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_message_page_text' ] ) ) : __( 'Message Page', 'custom-facebook-feed' ),
			'getdirections' 			=> isset( $translations[ 'cff_translate_get_directions_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_get_directions_text' ] ) ) : __( 'Get Directions', 'custom-facebook-feed' ),

			'secondtext' 				=> isset( $translations[ 'cff_translate_second' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_second' ] ) ) : 'second',
			'secondstext' 				=> isset( $translations[ 'cff_translate_seconds' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_seconds' ] ) ) : 'seconds',
			'minutetext' 				=> isset( $translations[ 'cff_translate_minute' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_minute' ] ) ) : 'minute',
			'minutestext' 				=> isset( $translations[ 'cff_translate_minutes' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_minutes' ] ) ) : 'minutes',
			'hourtext' 					=> isset( $translations[ 'cff_translate_hour' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_hour' ] ) ) : 'hour',
			'hourstext' 				=> isset( $translations[ 'cff_translate_hours' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_hours' ] ) ) : 'hours',
			'daytext' 					=> isset( $translations[ 'cff_translate_day' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_day' ] ) ) : 'day',
			'daystext' 					=> isset( $translations[ 'cff_translate_days' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_days' ] ) ) : 'days',
			'weektext' 					=> isset( $translations[ 'cff_translate_week' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_week' ] ) ) : 'week',
			'weekstext' 				=> isset( $translations[ 'cff_translate_weeks' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_weeks' ] ) ) : 'weeks',
			'monthtext' 				=> isset( $translations[ 'cff_translate_month' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_month' ] ) ) : 'month',
			'monthstext' 				=> isset( $translations[ 'cff_translate_months' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_months' ] ) ) : 'months',
			'yeartext' 					=> isset( $translations[ 'cff_translate_year' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_year' ] ) ) : 'year',
			'yearstext' 				=> isset( $translations[ 'cff_translate_years' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_years' ] ) ) : 'years',
			'agotext' 					=> isset( $translations[ 'cff_translate_ago' ] ) ? stripslashes( esc_attr( $translations[ 'cff_translate_ago' ] ) ) : 'ago',
		];

		$final_translations['facebooklinktext'] = ! empty( $this->feed_options['facebooklinktext'] ) ? $this->feed_options['facebooklinktext'] : $final_translations['facebooklinktext'];
		$final_translations['sharelinktext'] = ! empty( $this->feed_options['sharelinktext'] ) ? $this->feed_options['sharelinktext'] : $final_translations['sharelinktext'];
		$final_translations['buttontext'] = ! empty( $this->feed_options['buttontext'] ) ? $this->feed_options['buttontext'] : $final_translations['buttontext'];

		$this->feed_options = array_merge( $this->feed_options, $final_translations );
	}

	/**
	 * Whether or not the feed is contains more than
	 * one source. Helps a workaround for the updating
	 * caching system not working for legacy feeds
	 *
	 * @param array $feed_options
	 *
	 * @return bool
	 */
	public static function is_multifeed( $feed_options ) {
		$is_multifeed = false;
		if ( isset( $feed_options['sources'] )
		     && is_array( $feed_options['sources'] )
		     && count( $feed_options['sources'] ) > 1 ) {
			$is_multifeed = true;
		}

		return $is_multifeed;
	}

	/**
	 * this function is where the json data from the Facebook API and the feed options
	 * come together to generate all of the html needed to output the feed on the page
	 *
	 * @since 3.18
	 */
	function cff_get_post_set_html( $feed_options, $json_data_arr, $original_atts = array() ) {
		if ( ! empty( $feed_options['feederror'] ) ) {
			return false;
		}

		$this->feed_options = $feed_options;

		$this->cff_add_translations();

		$feed_options = $this->feed_options;

	    //Active extensions
	    $cff_ext_multifeed_active 	= $this->feed_options[ 'multifeedactive' ];
	    $cff_ext_date_active 		= $this->feed_options[ 'daterangeactive' ];
	    $cff_featured_post_active 	= $this->feed_options[ 'featuredpostactive' ];
	    $cff_album_active 			= $this->feed_options[ 'albumactive' ];
	    $cff_masonry_columns_active = false; //Deprecated
	    $cff_carousel_active 		= $this->feed_options[ 'carouselactive' ];
	    $cff_reviews_active 		= $this->feed_options[ 'reviewsactive' ];

	    /********** GENERAL **********/
	    $cff_page_type = $this->feed_options[ 'pagetype' ];
	    $cff_is_group = ($cff_page_type == 'group')  ? true : false;
	    $cff_show_author = $this->feed_options[ 'showauthornew' ];
	    $cff_cache_time = $this->feed_options[ 'cachetime' ];
	    $cff_locale = $this->feed_options[ 'locale' ];
	    if ( empty($cff_locale) || !isset($cff_locale) || $cff_locale == '' ) $cff_locale = 'en_US';

	    $cff_cache_time_unit = $this->feed_options[ 'cacheunit' ];

	    //Don't allow cache time to be zero - set to 1 minute instead to minimize API requests
	    if(!isset($cff_cache_time) || $cff_cache_time == '0' || (intval($cff_cache_time) < 15 && $cff_cache_time_unit == 'minutes' ) ){
	        $cff_cache_time = 15;
	        $cff_cache_time_unit = 'minutes';
	    }
	    if($cff_cache_time == 'nocaching') $cff_cache_time = 0;


	    //Open links in new window?
	    $target = 'target="_blank"';
	    /********** POST TYPES **********/
	    $cff_types = $this->feed_options[ 'type' ];
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

	    //Only events
	    $cff_events_source = $this->feed_options[ 'eventsource' ];
	    if ( empty($cff_events_source) || !isset($cff_events_source) ) $cff_events_source = 'eventspage';

	    $cff_event_offset = $this->feed_options[ 'eventoffset' ];
	    if ( empty($cff_event_offset) || !isset($cff_event_offset) ) $cff_event_offset = '6';

	    $cff_events_only = false;
	    if ($cff_show_event_type && !$cff_show_links_type && !$cff_show_video_type && !$cff_show_photos_type && !$cff_show_status_type && !$cff_show_albums_type) $cff_events_only = true;

	    //Past events
	    ( $this->feed_options['pastevents'] !== 'false' ) ? $cff_past_events = true : $cff_past_events = false;

	    //ALBUMS ONLY
	    $cff_albums_source 		= $this->feed_options[ 'albumsource' ];
	    $cff_album_cols 		= $this->feed_options['albumcols'];
	    $cff_show_album_title 	= CFF_Utils::check_if_on( $this->feed_options['showalbumtitle'] );
	    $cff_show_album_number 	= CFF_Utils::check_if_on( $this->feed_options['showalbumnum'] );


	    $cff_albums_only = false;
	    if ( ($cff_show_albums_type && $cff_albums_source == 'photospage') && !$cff_show_links_type && !$cff_show_video_type && !$cff_show_photos_type && !$cff_show_status_type && !$cff_show_event_type) $cff_albums_only = true;

	    //PHOTOS ONLY
	    $cff_photos_source = $this->feed_options[ 'photosource' ];
	    isset($this->feed_options['photocols']) ? $cff_photos_cols = $this->feed_options['photocols'] : $cff_photos_cols = '1';

	    $cff_photos_only = false;
	    if ( ($cff_show_photos_type && $cff_photos_source == 'photospage') && !$cff_show_links_type && !$cff_show_video_type && !$cff_show_event_type && !$cff_show_status_type && !$cff_show_albums_type) $cff_photos_only = true;
	    if( $cff_featured_post_active && !empty($this->feed_options['featuredpost']) ) $cff_photos_only = false;


	    //VIDEOS ONLY
	    $cff_videos_source 		= $this->feed_options[ 'videosource' ];
	    $cff_video_cols 		= $this->feed_options['videocols'];
	    $cff_show_video_name 	= CFF_Utils::check_if_on( $this->feed_options['showvideoname'] );
	    $cff_show_video_desc 	= CFF_Utils::check_if_on( $this->feed_options['showvideodesc'] );


	    $cff_videos_only = false;
	    if ( ($cff_show_video_type && $cff_videos_source == 'videospage') && !$cff_show_albums_type && !$cff_show_links_type && !$cff_show_photos_type && !$cff_show_status_type && !$cff_show_event_type) $cff_videos_only = true;
	    if( $cff_featured_post_active && !empty($this->feed_options['featuredpost']) ) $cff_videos_only = false;

	    /********** LAYOUT **********/
	    //Include string
	    $cff_includes = $this->feed_options[ 'include' ];
	    $cff_excludes = $this->feed_options[ 'exclude' ];
	    //Look for non-plural version of string in the types string in case user specifies singular in shortcode
	    $cff_show_author			= $this->check_show_section( 'author' );
		$cff_show_text				= $this->check_show_section( 'text' );
		$cff_show_desc				= $this->check_show_section( 'desc' );
		$cff_show_shared_links		= $this->check_show_section( 'sharedlink' );
		if ( ! $cff_show_shared_links ) {
			$cff_show_shared_links		= $this->check_show_section( 'sharedlinks' );
		}
		$cff_show_date				= $this->check_show_section( 'date' );
		$cff_show_media				= $this->check_show_section( 'media' );
		$cff_show_media_link		= $this->check_show_section( 'medialink' );
		$cff_show_event_title		= $this->check_show_section( 'eventtitle' );
		$cff_show_event_details		= $this->check_show_section( 'eventdetail' );
		if ( !$cff_show_event_details ) {
			$cff_show_event_details		= $this->check_show_section( 'eventdetails' );
		}
		$cff_show_meta				= $this->check_show_section( 'social' );
		$cff_show_link				= $this->check_show_section( 'link' );
		$cff_show_like_box			= $this->check_show_section( 'like' );


	    $cff_preset_layout = $this->feed_options[ 'layout' ];
	    //Default is thumbnail layout
	    $cff_thumb_layout = false;
	    $cff_half_layout = false;
	    $cff_full_layout = true;
	    if (($cff_preset_layout == 'thumb' || empty($cff_preset_layout)) && $cff_show_media) {
	        $cff_thumb_layout = true;
	    } else if ($cff_preset_layout == 'half'  && $cff_show_media) {
	        $cff_half_layout = true;
	    } else {
	        $cff_full_layout = true;
	    }

	    //Get the media position
	    $cff_media_position = $this->feed_options['mediaposition'];
	    if ( $cff_thumb_layout || $cff_half_layout) $cff_media_position = 'below';

	    //If the old shortcode option 'showauthor' is being used then apply it
	    $cff_show_author_old = $this->feed_options[ 'showauthor' ];
	    if( $cff_show_author_old == 'false' ) $cff_show_author = false;
	    if( $cff_show_author_old == 'true' ) $cff_show_author = true;

	    //LIGHTBOX
		if ( $this->feed_options['disablelightbox'] === false ) {
			$cff_disable_lightbox = false;
		} elseif ( $this->feed_options['disablelightbox'] === true ) {
			$cff_disable_lightbox = true;
		} else {
			$cff_disable_lightbox = !CFF_Utils::check_if_on( $this->feed_options['disablelightbox'] );
		}

	    /********** META **********/
		if ( ! empty( $this->feed_options['colorpalette'] ) ) {
			switch ( $this->feed_options['colorpalette'] ) {
				case 'dark' :
					$this->feed_options['iconstyle'] = 'dark';
					break;
				case 'light' :
					$this->feed_options['iconstyle'] = 'light';
					break;
			}
		}


	    $cff_icon_style = 'cff-' . $this->feed_options[ 'iconstyle' ];
	    $cff_expand_comments = CFF_Utils::check_if_on( $this->feed_options['expandcomments'] );
	    !isset( $this->feed_options['commentsnum'] ) ? $cff_comments_num = '4' : $cff_comments_num = $this->feed_options['commentsnum'];
	    $cff_hide_comment_avatars = CFF_Utils::check_if_on( $this->feed_options['hidecommentimages'] );

	    $cff_meta_styles = $this->get_style_attribute( 'likes_comment_box' );



	    $cff_meta_link_color = '#' . str_replace('#', '', $this->feed_options['sociallinkcolor']);

	    /********** TYPOGRAPHY **********/
	    //See More text
	    $cff_see_more_text = $this->feed_options[ 'seemoretext' ];
	    $cff_see_less_text = $this->feed_options[ 'seelesstext' ];
	    //See Less text
	    //Title
	    $cff_title_format = empty($cff_title_format) || $cff_title_format == 'p' ? 'p' : $this->feed_options[ 'textformat' ];
	    $cff_title_styles = $this->get_style_attribute( 'post_text' );

	    //Text link color
	    $cff_posttext_link_color 		= str_replace('#', '', $this->feed_options['textlinkcolor'] );
	    $cff_posttext_link_color_html 	= $this->get_style_attribute( 'text_link' );
	    $cff_title_link 				= CFF_Utils::check_if_on( $this->feed_options['textlink'] );
	    $cff_author_styles 				= $this->get_style_attribute( 'author' );
	    $cff_body_styles 				= $this->get_style_attribute( 'body_description' );


	    //Shared link title
	    $cff_link_title_format = empty($cff_link_title_format) ? 'p' : $this->feed_options[ 'linktitleformat' ];
	    $cff_link_title_size = $this->feed_options[ 'linktitlesize' ];
	    $cff_link_title_color = str_replace('#', '', $this->feed_options[ 'linktitlecolor' ]);

	    $cff_link_title_styles = '';
	    if ( !empty($cff_link_title_size) && $cff_link_title_size != 'inherit' ) $cff_link_title_styles =  'style="font-size:' . $cff_link_title_size . 'px;"';

	    //Shared link description
	    $cff_link_desc_size = $this->feed_options[ 'linkdescsize' ];
	    $cff_link_desc_color = $this->feed_options[ 'linkdesccolor' ];

	    //Shared link URL
	    $cff_link_url_size = $this->feed_options[ 'linkurlsize' ];
	    $cff_link_url_color = $this->feed_options[ 'linkurlcolor' ];

	    //Shared link box
	    $cff_disable_link_box 	= CFF_Utils::check_if_on( $this->feed_options['disablelinkbox'] );
	    $cff_full_link_images 	= CFF_Utils::check_if_on( $this->feed_options['fulllinkimages'] );
	    $cff_image_size 		= $this->feed_options['postimagesize'];
	    $cff_link_image_size 	= $this->feed_options['linkimagesize'];
	    $cff_link_box_styles 	= $this->get_style_attribute( 'link_box' );



	    //Event Title
	    $cff_event_title_format = empty($cff_event_title_format) ? 'p' : $this->feed_options[ 'eventtitleformat' ];
	    $cff_event_title_styles = $this->get_style_attribute( 'event_title' );
	    $cff_event_title_link = CFF_Utils::check_if_on( $this->feed_options['eventtitlelink'] );

	    //Event Date
	    $cff_event_date_position = $this->feed_options[ 'eventdatepos' ];
	    $cff_event_date_formatting = $this->feed_options[ 'eventdateformat' ];
	    $cff_event_date_custom = $this->feed_options[ 'eventdatecustom' ];

	    $cff_event_date_styles = $this->get_style_attribute( 'event_date' );
	    $cff_event_timezone_offset = $this->feed_options[ 'timezoneoffset' ];

	    //Event Details
	    $cff_event_link_color = str_replace('#', '', $this->feed_options[ 'eventlinkcolor' ]);
	    $cff_event_details_styles = $this->get_style_attribute( 'event_detail' );

	    //No Upcoming Events text
	    $cff_no_events_text = $this->feed_options['noeventstext'];
	    if (!isset($cff_no_events_text) || empty($cff_no_events_text)) $cff_no_events_text = 'No upcoming events';

	    //Date
	    $cff_date_position = !isset($this->feed_options[ 'datepos' ]) ? 'below' : $this->feed_options[ 'datepos' ];

	    $cff_date_before = isset( $this->feed_options[ 'beforedate' ] ) ? stripslashes( esc_attr( $this->feed_options[ 'beforedate' ] ) ) : '';
	    $cff_date_after = isset( $this->feed_options[ 'afterdate' ] ) ? stripslashes( esc_attr( $this->feed_options[ 'afterdate' ] ) ) : '';

	    //Timezone. The post date is adjusted by the timezone offset in the CFF_Utils::cff_getdate function.
	    $cff_timezone = $this->feed_options['timezone'];

	    //Posted ago strings
	    $cff_date_translate_strings = array(
	        'cff_translate_second' => $this->feed_options['secondtext'],
	        'cff_translate_second' => $this->feed_options['secondtext'],
	        'cff_translate_seconds' => $this->feed_options['secondstext'],
	        'cff_translate_minute' => $this->feed_options['minutetext'],
	        'cff_translate_minutes' => $this->feed_options['minutestext'],
	        'cff_translate_hour' => $this->feed_options['hourtext'],
	        'cff_translate_hours' => $this->feed_options['hourstext'],
	        'cff_translate_day' => $this->feed_options['daytext'],
	        'cff_translate_days' => $this->feed_options['daystext'],
	        'cff_translate_week' => $this->feed_options['weektext'],
	        'cff_translate_weeks' => $this->feed_options['weekstext'],
	        'cff_translate_month' => $this->feed_options['monthtext'],
	        'cff_translate_months' => $this->feed_options['monthstext'],
	        'cff_translate_year' => $this->feed_options['yeartext'],
	        'cff_translate_years' => $this->feed_options['yearstext'],
	        'cff_translate_ago' => $this->feed_options['agotext']
	    );

	    //Link to Facebook
		if ( ! empty( $this->feed_options['colorpalette'] ) &&
		     $this->feed_options['colorpalette'] !== 'inherit' ) {
			$cff_link_styles = '';
		} else {
			$cff_link_styles = $this->get_style_attribute( 'post_link' );
		}

	    $cff_facebook_link_text = $this->feed_options[ 'facebooklinktext' ];
	    $cff_facebook_share_text = $this->feed_options[ 'sharelinktext' ];
	    if ($cff_facebook_share_text == '') $cff_facebook_share_text = 'Share';


	    //Show Facebook link
	    $cff_show_facebook_link = CFF_Utils::check_if_on( $this->feed_options['showfacebooklink'] );


	    //Show Share link
	    $cff_show_facebook_share = CFF_Utils::check_if_on( $this->feed_options['showsharelink'] );

	    $cff_view_link_text = $this->feed_options[ 'viewlinktext' ];
	    $cff_link_to_timeline = $this->feed_options[ 'linktotimeline' ];

	    /********** MISC **********/

	    //Photos translate text
	    $cff_translate_photos_text = CFF_Utils::return_value( $this->feed_options['photostext'], 'photos' );

	    //Is it a restricted page?
	 	$cff_restricted_page = CFF_Utils::check_if_on( $this->feed_options['restrictedpage'] );

	    //Should we hide supporter posts?
	 	$cff_hide_supporter_posts = CFF_Utils::check_if_on( $this->feed_options['hidesupporterposts'] );

	    //Video
	    $cff_video_height = $this->feed_options[ 'videoheight' ];
	    $cff_video_action = $this->feed_options[ 'videoaction' ];

	    //Post Style settings
	    $cff_post_style = $this->feed_options['poststyle'];

	    $cff_post_bg_color = str_replace('#', '', $this->feed_options['postbgcolor']);
	    $cff_post_rounded = $this->feed_options['postcorners'];
	    ( ($cff_post_bg_color !== '#' && $cff_post_bg_color !== '') && $cff_post_style != 'regular' ) ? $cff_post_bg_color_check = true : $cff_post_bg_color_check = false;
		$cff_box_shadow = CFF_Utils::check_if_on( $this->feed_options['boxshadow'] );

	    //Separating Line
	    $cff_sep_color = $this->feed_options[ 'sepcolor' ];
	    if (empty($cff_sep_color)) $cff_sep_color = 'ddd';
	    $cff_sep_size = $this->feed_options[ 'sepsize' ];
	    $cff_sep_size_check = true;
	    //If empty then set a 0px border
	    if ( empty($cff_sep_size) || $cff_sep_size == '' ) {
	        $cff_sep_size = 0;
	        //Need to set a color otherwise the CSS is invalid
	        $cff_sep_color = 'fff';
	        $cff_sep_size_check = false;
	    }
	    ($cff_sep_color !== '#' && $cff_sep_color !== '') ? $cff_sep_color_check = true : $cff_sep_color_check = false;

	    //CFF item styles
	    $cff_item_styles = '';
	    if( $cff_post_style == 'boxed' || $cff_post_bg_color_check ){
	        $cff_item_styles = 'style="';
	        if($cff_post_bg_color_check) $cff_item_styles .= 'background-color: #' . $cff_post_bg_color . '; ';
	        if( isset($cff_post_rounded) && $cff_post_rounded !== '0' && !empty($cff_post_rounded) ) $cff_item_styles .= '-webkit-border-radius: ' . $cff_post_rounded . 'px; -moz-border-radius: ' . $cff_post_rounded . 'px; border-radius: ' . $cff_post_rounded . 'px; ';
	        $cff_item_styles .= '"';
	    }
	    if( $cff_post_style == 'regular' && ($cff_sep_color_check || $cff_sep_size_check) ){
	        $cff_item_styles .= 'style="border-bottom: ' . $cff_sep_size . 'px solid #' . str_replace('#', '', $cff_sep_color) . ';"';
	    }

	    //Text limits
	    $title_limit = $this->feed_options['textlength'];
	    if (!isset($title_limit)) $title_limit = 9999;
	    $body_limit = $this->feed_options['desclength'];


	    //Assign the Access Token
	    $access_token = $this->feed_options['accesstoken'];

	    //If the 'Enter my own Access Token' box is unchecked then don't use the user's access token, even if there's one in the field
	    $this->feed_options['ownaccesstoken'] ? $cff_show_access_token = true : $cff_show_access_token = false;

	    //Page ID
	    $page_id = trim( $this->feed_options['id'] );
	    //If user pastes their full URL into the Page ID field then strip it out
	    $cff_facebook_string = 'facebook.com';
	    ( CFF_Utils::stripos($page_id, $cff_facebook_string) !== false) ? $cff_page_id_url_check = true : $cff_page_id_url_check = false;

	    if ( $cff_page_id_url_check === true ) {
	        //Remove trailing slash if exists
	        $page_id = preg_replace('{/$}', '', $page_id);
	        //Get last part of url
	        $page_id = substr( $page_id, strrpos( $page_id, '/' )+1 );
	    }

	    //If the Page ID contains a query string at the end then remove it
	    if ( CFF_Utils::stripos( $page_id, '?') !== false ) $page_id = substr($page_id, 0, strrpos($page_id, '?'));


	    //Get show posts attribute. If not set then default to 25
	    $show_posts = isset( $this->feed_options['minnum'] ) ? $this->feed_options['minnum'] : $this->feed_options['num'];
	    if (empty($show_posts)) $show_posts = 25;
	    if ( $show_posts == 0 || $show_posts == 'undefined' ) $show_posts = 25;

	    //If it's the last batch of album-items then display them all and then hide them in JS as we don't make any further API requests
	    // if( $last_album_batch == 'true' ) $show_posts = 999;

	    //Check whether a Page ID has been defined
	    if ( $page_id == '' && ( current_user_can('editor') || current_user_can('administrator') ) ) {
	    	//echo "<span id='cff-no-id'>" . sprintf( __( "It looks like you haven't set up a feed yet. Try going to %sthis page%s to create one and then enter the feed id in the shortcode like this [custom-facebook-feed feed=YOUR_FEED_ID_HERE].", 'custom-facebook-feed' ), '<a href="'.esc_url( admin_url( 'admin.php?page=cff-feed-builder' ) ).'">', '</a>' ) . "</span><br /><br />";
	    }

	    //Use posts? or feed?
	    $show_others = $this->feed_options['others'];
	    $show_posts_by = $this->feed_options['showpostsby'];
	    $graph_query = 'posts';
	    $cff_show_only_others = false;

	    //If 'others' shortcode option is used then it overrides any other option
	    if (!$show_others) {
	        //Show posts ONLY by others
	        if ( $show_posts_by == 'onlyothers' && !$cff_is_group ) {
	            $cff_show_only_others = true;
	        }
	    }

	    //Misc Settings
	 	$cff_nofollow = CFF_Utils::check_if_on( $this->feed_options['nofollow'] );

	    ( $cff_nofollow ) ? $cff_nofollow = ' rel="nofollow noopener"' : $cff_nofollow = '';
	    $cff_nofollow_referrer = ' rel="nofollow noopener noreferrer"';

	    //If the number of posts is set to zero then don't show any and set limit to one
	    if ( ($this->feed_options['num'] == '0' || $this->feed_options['num'] == 0) && $this->feed_options['num'] !== ''){
	        $show_posts = 0;
	        $cff_post_limit = 1;
	    }


	    //Set the cache time (for timeline events)
	    //Calculate the cache time in seconds
	    if($cff_cache_time_unit == 'minutes') $cff_cache_time_unit = 60;
	    if($cff_cache_time_unit == 'hours') $cff_cache_time_unit = 60*60;
	    if($cff_cache_time_unit == 'days') $cff_cache_time_unit = 60*60*24;
	    $cache_seconds = $cff_cache_time * $cff_cache_time_unit;

	    //Extension settings
	    $cff_album_id = $this->feed_options['album'];
	    ( $this->feed_options['reviewsmethod'] == 'all' ) ? $show_all_reviews = true : $show_all_reviews = false;

	    //***START POSTS HTML***
	    $cff_content = "";

	    //Limit var
	    $i = 0;

	    //Multifeed extension
	    ( $cff_ext_multifeed_active ) ? $page_ids = cff_multifeed_ids($page_id) : $page_ids = array($page_id);

	    //If multiple Access Tokens are being used then split them up into an associative array
	    $cff_multiple_tokens = false;
	    if ( $cff_ext_multifeed_active && is_array($access_token) ){
	    	$cff_multiple_tokens = true;
	    	$access_token_multiple = $access_token;
	    }

	    //Define array for post items
	    $cff_posts_array = array();

	    //If it's an album embed then only use one ID otherwise it loops through and embeds the same album items multiple times
	    if( !empty($cff_album_id) ) $page_ids = array($page_ids[0]);

	    //LOOP THROUGH PAGE IDs
	    foreach ( $page_ids as $page_id ) {

	    	//If using multiple Page Access Tokens then grab the right one for this ID
	        if( $cff_multiple_tokens ){
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
	        }

	        //Set the JSON data to be the JSON data that corresponds to the page_id in the multifeed JSON data array
	        $json_data = $json_data_arr[$page_id];

	        //EVENTS ONLY
	        if ($cff_events_only && $cff_events_source == 'eventspage'){

	            //If there is no event data then show a message
	            if( empty($json_data->data) ){
	                //Message no longer relevant
	            } else {

	                //EVENTS LOOP
	                foreach ($json_data->data as $event )
	                {
	                    //Only create posts for the amount of posts specified
	                    // if ( $i == $show_posts ) break;
	                    $i++;
	                    isset($event->id) ? $id = $event->id : $id = '';
	                    //Object ID
	                    ( !empty($event->object_id) ) ? $object_id = $event->object_id : $object_id = '';

	                    isset($event->name) ? $event_name = $event->name : $event_name = '';
	                    isset($event->attending_count) ? $attending_count = $event->attending_count : $attending_count = '';

	                    //Picture source
	                    $cff_no_event_img = false;
	                    if( isset($event->cover) ){
	                        $pic_big = $event->cover->source;
	                    } else {
	                        $cff_no_event_img = true;
	                        $pic_big = plugins_url( '/assets/img/event-image.png' , dirname(__FILE__) );
	                        $pic_big_lightbox = plugins_url( '/assets/img/event-image-cover.png' , dirname(__FILE__) );
	                    }
	                    ( $this->feed_options['eventimage'] == 'cropped' ) ? $crop_event_pic = true : $crop_event_pic = false;

	                    isset($event->start_time) ? $start_time = $event->start_time : $start_time = '';
	                    isset($event->end_time) ? $end_time = $event->end_time : $end_time = '';
	                    isset($event->timezone) ? $timezone = $event->timezone : $timezone = '';

	                    //Venue
	                    isset($event->place->location->latitude) ? $venue_latitude = $event->place->location->latitude : $venue_latitude = '';
	                    isset($event->place->location->longitude) ? $venue_longitude = $event->place->location->longitude : $venue_longitude = '';
	                    isset($event->place->location->city) ? $venue_city = $event->place->location->city : $venue_city = '';
	                    isset($event->place->location->state) ? $venue_state = $event->place->location->state : $venue_state = '';
	                    isset($event->place->location->country ) ? $venue_country = htmlentities($event->place->location->country, ENT_QUOTES, 'UTF-8') : $venue_country = '';
	                    isset($event->place->id) ? $venue_id = $event->place->id : $venue_id = '';
	                    $venue_link = 'https://facebook.com/' . $venue_id;
	                    isset($event->place->location->street) ? $venue_street = $event->place->location->street : $venue_street = '';
	                    isset($event->place->location->zip) ? $venue_zip = $event->place->location->zip : $venue_zip = '';
	                    isset($event->place->name) ? $location = $event->place->name : $location = '';

	                    isset($event->description) ? $description = $event->description : $description = '';
	                    $event_link = 'https://facebook.com/events/' . $id;
	                    isset($event->ticket_uri) ? $ticket_uri = htmlentities($event->ticket_uri, ENT_QUOTES, 'UTF-8') : $ticket_uri = '';

	                    //Interested in/going
	                    isset($event->interested_count) ? $interested_count = $event->interested_count : $interested_count = '';
	                    isset($event->attending_count) ? $attending_count = $event->attending_count : $attending_count = '';

	                    $cff_buy_tickets_text = $this->feed_options['buyticketstext'];

	                    //Event date
	                    $event_time = $start_time;

	                    //If timezone migration is enabled then remove last 5 characters
	                    if ( strlen($event_time) == 24 ) $event_time = substr($event_time, 0, -5);

	                    //Event title
	                    $cff_event_title = '';
	                    $cff_event_title .= '<' . $cff_event_title_format . ' class="cff-event-title" ' . $cff_event_title_styles . '>';
	                    if ($cff_event_title_link) $cff_event_title .= '<a href="'.$event_link.'" '.$target.$cff_nofollow.'>';
	                    $cff_event_title .= $event_name;
	                    if ($cff_event_title_link) $cff_event_title .= '</a>';
	                    $cff_event_title .= '</' . $cff_event_title_format . '>';


	                    //Get the filter string
	                    $cff_filter_string = $this->feed_options[ 'filter' ];
	                    //Create a string from the event title, location and address to use in the filter check below
	                    $cff_event_address_string = $cff_event_title . $location . $venue_street . $venue_city . $venue_state . $venue_zip;

	                    $cff_show_post = true;
	                    if ( $cff_filter_string != '' ){
	                        //Explode it into multiples
	                        $cff_filter_strings_array = explode(',', $cff_filter_string);
	                        //Hide the post if both the post text and description don't contain the string
	                        $string_in_address = true;
	                        $string_in_desc = true;
	                        if ( CFF_Utils::cff_stripos_arr_filter($cff_event_address_string, $cff_filter_strings_array) === false ) $string_in_address = false;
	                        if ( CFF_Utils::cff_stripos_arr_filter($description, $cff_filter_strings_array) === false ) $string_in_desc = false;

	                        if( $string_in_address == false && $string_in_desc == false ) $cff_show_post = false;
	                    }

	                    $cff_exclude_string = $this->feed_options[ 'exfilter' ];
	                    if ( $cff_exclude_string != '' ){
	                        //Explode it into multiples
	                        $cff_exclude_strings_array = explode(',', $cff_exclude_string);
	                        //Hide the post if both the post text and description don't contain the string
	                        $string_in_address = false;
	                        $string_in_desc = false;

	                        if ( CFF_Utils::cff_stripos_arr_filter($cff_event_address_string, $cff_exclude_strings_array) !== false ) $string_in_address = true;
	                        if ( CFF_Utils::cff_stripos_arr_filter($description, $cff_exclude_strings_array) !== false ) $string_in_desc = true;

	                        if( $string_in_address == true || $string_in_desc == true ) $cff_show_post = false;
	                    }

	                    //Encode these after the filtering is done
	                    $event_name = htmlentities($event_name, ENT_QUOTES, 'UTF-8');
	                    $location = htmlentities($location, ENT_QUOTES, 'UTF-8');
	                    $venue_street = htmlentities($venue_street, ENT_QUOTES, 'UTF-8');
	                    $venue_city = htmlentities($venue_city, ENT_QUOTES, 'UTF-8');
	                    $venue_state = htmlentities($venue_state, ENT_QUOTES, 'UTF-8');
	                    $venue_zip = htmlentities($venue_zip, ENT_QUOTES, 'UTF-8');
	                    $description = htmlentities($description, ENT_QUOTES, 'UTF-8');

	                    //Recurring events time
	                    $cur_time = strtotime(date('Y-m-d'));
	                    if( $cff_past_events ) $cur_time = strtotime($event_time)-1; //If past events then can't use current time, so use the first event start date and subtract 1 so is before.
	                    $cff_multiple_date_count = 0;
	                    $event_time_item_id = '';

	                    if( isset($event->event_times) ){

	                        //Set time diff to be really high initially so the time difference comparison will be less than it
	                        $event_time_diff = 99999999999;
	                        $event_time_arr = array();

	                        foreach ( $event->event_times as $event_time_item){
	                            $event_item_time = $event_time_item->start_time;
	                            //If timezone migration is enabled then remove last 5 characters
	                            if ( strlen($event_item_time) == 24 ) $event_item_time = substr($event_item_time, 0, -5);
	                            $event_item_time = strtotime($event_item_time);

	                            if( $event_item_time > $cur_time ){
	                                //Find smallest diff between start_time and current time
	                                if( abs( $event_item_time - $cur_time ) < $event_time_diff ){
	                                    $event_time_diff = abs( $event_item_time - $cur_time );

	                                    //Use the start and end times from this "event_times" item
	                                    $event_time = $event_time_item->start_time;
	                                    //If timezone migration is enabled then remove last 5 characters
	                                    if ( strlen($event_time) == 24 ) $event_time = substr($event_time, 0, -5);

	                                    if( isset($event_time_item->end_time) ) $end_time = $event_time_item->end_time;
	                                }
	                                $cff_multiple_date_count++;

	                                //Create a custom array from the event times so I can sort them and loop through below
	                                $event_time_arr = CFF_Utils::cff_array_push_assoc(
	                                    $event_time_arr,
	                                    $event_item_time,
	                                    array(
	                                        'id' => $event_time_item->id,
	                                        'end_time' => $event_time_item->end_time
	                                    )
	                                );
	                            } //End if

	                        } //End for loop

	                        //Convert to unix
	                        $event_time = strtotime($event_time);

	                        //If timezone migration is enabled then remove last 5 characters
	                        if ( strlen($event_time) == 24 ) $event_time = substr($event_time, 0, -5);

	                        //-1 to account for date already being displayed
	                        $cff_multiple_date_count--;

	                        //Sort the array by date so they're shown chronologically
	                        ksort($event_time_arr);
	                    } else {
	                        $event_time = strtotime($event_time);
	                        // $event_time = $event_time;
	                    }

	                    //If timezone migration is enabled then remove last 5 characters from end time
	                    if ( strlen($end_time) == 24 ) $end_time = substr($end_time, 0, -5);

	                    //Create the event date HTML
	                    $cff_event_date = '';
	                    if (!empty($event_time)){
	                        $cff_event_date = '<p class="cff-date"><span class="cff-start-date" '.$cff_event_date_styles.'>' . CFF_Utils::cff_eventdate($event_time, $cff_event_date_formatting, $cff_event_date_custom, $cff_event_timezone_offset, $cff_timezone) . '</span>';
	                        // $cff_event_date = '<p class="cff-date"><span class="cff-start-date" '.$cff_event_date_styles.'>' . CFF_Utils::cff_eventdate(strtotime($event_time), $cff_event_date_formatting, $cff_event_date_custom) . '</span>';
	                        if( isset($event->end_time) ) $cff_event_date .= '<span class="cff-end-date" '.$cff_event_date_styles.'> - ' . CFF_Utils::cff_eventdate(strtotime($end_time), $cff_event_date_formatting, $cff_event_date_custom, $cff_event_timezone_offset, $cff_timezone) . '</span>';
	                        if( $cff_multiple_date_count > 0 ) $cff_event_date .= '<a href="javascript:void(0);" class="cff-more-dates">+'.$cff_multiple_date_count.'</a>';

	                        //Include the additional dates if there are some
	                        if( $cff_multiple_date_count > 0 ){
	                            $cff_event_date .= '<span class="cff-multiple-dates">';

	                            foreach ( $event_time_arr as $event_time_unix => $event_time_item){

	                                //Don't include the date which is used as the main event date so it's not repeated
	                                if( $event_time != $event_time_unix ){

	                                    //If timezone migration is enabled then remove last 5 characters from end time
	                                    if ( strlen($event_time_unix) == 24 ) $event_time_unix = substr($event_time_unix, 0, -5);

	                                    //Create the HTML for the additional dates to display below
	                                    $cff_event_date .= '<span class="cff-multiple-date" id="cff_'.$event_time_item['id'].'"><span class="cff-start-date">' . CFF_Utils::cff_eventdate($event_time_unix, $cff_event_date_formatting, $cff_event_date_custom, $cff_event_timezone_offset, $cff_timezone) . '</span>';
	                                    if( isset($event_time_item['end_time']) ){

	                                        $cff_event_end_time = $event_time_item['end_time'];

	                                        //If timezone migration is enabled then remove last 5 characters from end time
	                                        if ( strlen($cff_event_end_time) == 24 ) $cff_event_end_time = substr($cff_event_end_time, 0, -5);

	                                        $cff_event_date .= '<span class="cff-end-date"> - ' . CFF_Utils::cff_eventdate(strtotime($cff_event_end_time), $cff_event_date_formatting, $cff_event_date_custom, $cff_event_timezone_offset, $cff_timezone) . '</span>';
	                                    }
	                                    $cff_event_date .= '</span>';
	                                }
	                            }
	                            $cff_event_date .= '</span>';
	                        }
	                        $cff_event_date .= '</p>';
	                    }


	                    //***************************//
	                    //***CREATE THE EVENT HTML***//
	                    //***************************//
	                    $cff_post_item = '<div class="cff-item cff-event author-'. CFF_Utils::cff_to_slug($page_id);
	                    if( !$cff_past_events ) $cff_post_item .= ' cff-upcoming-event';
	                    if ($cff_post_bg_color_check || $cff_post_style == "boxed") $cff_post_item .= ' cff-box';
	                    $cff_post_item .= ' cff-new';
	                    if( $event_time < $cur_time ) $cff_post_item .= ' cff-past';
	                    if( $cff_box_shadow ) $cff_post_item .= ' cff-shadow';
	                    $cff_post_item .= '"';
	                    $cff_post_item .= ' data-cff-timestamp="';
	                    if( isset($event->start_time) ) $cff_post_item .= strtotime($event->start_time);
	                    $cff_post_item .= '"';
	                    $cff_post_item .= ' id="cff_'. $id .'" ' . $cff_item_styles.'>';
	                    //Picture
	                    if($cff_show_media){

	                        //Fix Photon (Jetpack) issue
	                        $cff_picture_querystring = '';
	                        if (parse_url($pic_big, PHP_URL_QUERY)){
	                            $picture_url_parts = parse_url($pic_big);
	                            $cff_picture_querystring = $picture_url_parts['query'];
	                        }

	                        //Remove any quotes from event name to use in the image alt tag
	                        $event_name = str_replace('"', "", $event_name);
	                        $event_name = str_replace("'", "", $event_name);
	                        //Alt text
	                        isset( $event_name ) ? $cff_alt_text = strip_tags($event_name) : $cff_alt_text = $cff_facebook_link_text;
		                    $cff_alt_text = apply_filters( 'cff_img_alt', $cff_alt_text );
							$media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $event ) ) ) . '"';
	                        $cff_post_item .= '<div class="cff-media-wrap">';
	                        $cff_post_item .= '<a class="cff-photo nofancybox';
	                        if( $crop_event_pic ) $cff_post_item .= ' cff-crop';
	                        $cff_post_item .= '" href="'.$event_link.'" '.$target.$cff_nofollow.$media_src_set_att.'><img src="'. CFF_Display_Elements_Pro::get_media_placeholder( $pic_big ) .'" data-orig-source="'. $pic_big .'" alt="'.htmlspecialchars($cff_alt_text).'" data-querystring="'.$cff_picture_querystring.'" ';
	                        if( $cff_no_event_img ) $cff_post_item .= 'data-cff-no-event-img-large="'.$pic_big_lightbox.'"';
	                        $cff_post_item .= ' /></a></div>';
	                    }

	                    //Start text wrapper
	                    if ( ($cff_thumb_layout || $cff_half_layout) ) $cff_post_item .= '<div class="cff-details">';
	                        //show event date above title
	                        if ($cff_show_date && $cff_event_date_position == 'above') $cff_post_item .= $cff_event_date;
	                        //Show event title
	                        if ($cff_show_event_title && !empty($event_name)) $cff_post_item .= $cff_event_title;
	                        //show event date below title
	                        if ($cff_show_date && $cff_event_date_position !== 'above') $cff_post_item .= $cff_event_date;
	                        //Show event details
	                        if ($cff_show_event_details){
	                            if (!empty($location)) $cff_post_item .= '<p class="cff-location" ' . $cff_event_details_styles . '>';

	                            $cff_event_link_color_html = '';
	                            if( isset($cff_event_link_color) && !empty($cff_event_link_color) && $cff_event_link_color != '#' ) $cff_event_link_color_html = 'style="color: #'.$cff_event_link_color.'"';

	                            if (!empty($venue_id)) $cff_post_item .= '<a href="'. $venue_link .'" '.$target.$cff_nofollow.' '.$cff_event_link_color_html.'>';
	                            if (!empty($location)) $cff_post_item .= '<b class="cff-event-place">' . $location . '</b>';
	                            if (!empty($venue_id)) $cff_post_item .= '</a>';
	                            if (!empty($venue_street)) $cff_post_item .= '<span class="cff-event-street">' . $venue_street . '</span>';
	                            if (!empty($venue_city)) $cff_post_item .= '<span class="cff-event-city">' . $venue_city . ',</span>';
	                            if (!empty($venue_state)) $cff_post_item .= '<span class="cff-event-state"> ' . $venue_state . '</span>';
	                            if (!empty($venue_zip)) $cff_post_item .= '<span class="cff-event-zip">' . $venue_zip . '</span>';
	                            $cff_map_text = $this->feed_options[ 'maptext' ];

	                            //Create the map link
	                            if( isset($event->place->location) ){
	                                $map_url = 'https://maps.google.com/maps?q=' . $venue_latitude . ',+' . $venue_longitude;
	                            //If an address is used instead of a "place" then check whether it contains a number and is over a certain length. If it does, then it's likely a real address and so we can use it in the map link
	                            } else if( 1 === preg_match('~[0-9]~', $location) && strlen($location) > 10 ) {
	                                $map_url = 'https://maps.google.com/maps?q=' . $location;
	                            } else {
	                                $map_url = '';
	                            }

	                            //Map link
	                            $cff_event_link_color_html = '';
	                            if( isset($cff_event_link_color) && !empty($cff_event_link_color) && $cff_event_link_color != '#' ) $cff_event_link_color_html = 'style="color: #'.$cff_event_link_color.'"';
		                        $shortened_description = ! empty( $description ) ? CFF_Utils::cff_maybe_shorten_text( $description ) : CFF_Utils::cff_maybe_shorten_text( $cff_event_title );

	                            if (!empty($map_url)) $cff_post_item .= ' <a href="'.$map_url.'" '.$target.$cff_nofollow.' '.$cff_event_link_color_html.' class="cff-event-map-link">'.stripslashes(__( $cff_map_text, 'custom-facebook-feed' ) ).'</a>';

	                            if (!empty($location)) $cff_post_item .= '</p>';
	                            if (!empty($description)){

	                                $cff_post_item .= '<p class="cff-desc" ';

	                                //Set the char limit on the element
	                                if (!empty($title_limit)) {
	                                    if (strlen($description) > $title_limit) $cff_post_item .= 'data-char="'. $title_limit .'" ';
	                                }

	                                //Used to fix the content formatting issue caused by some themes
	                                $cff_format_issue = $this->feed_options['textissue'];
	                                ($cff_format_issue == 'true' || $cff_format_issue == 'on') ? $cff_format_issue = true : $cff_format_issue = false;
	                                $cff_linebreak_el = '<br />';
	                                if( $cff_format_issue ) $cff_linebreak_el = '<img class="cff-linebreak" />';

	                                //Replace line breaks in text (needed for IE8 and to prevent lost line breaks in HTML minification)
	                                $description = preg_replace("/\r\n|\r|\n/",$cff_linebreak_el, $description);

	                                $description = CFF_Autolink::cff_autolink($description, $link_color=$cff_event_link_color);
	                                $cff_description_tagged = CFF_Utils::cff_desc_tags($description);

	                                $cff_post_item .= $cff_event_details_styles . '><span class="cff-desc-text">' . $cff_description_tagged . '</span>';

	                                //Add the See More and See Less links if needed
	                                if (!empty($title_limit)) {
	                                    if (strlen($description) > $title_limit) $cff_post_item .= '<span class="cff-expand">... <a href="#" '.$cff_posttext_link_color_html.'><span class="cff-more">' . stripslashes(__( $cff_see_more_text, 'custom-facebook-feed' ) ) . '</span><span class="cff-less">' . stripslashes(__( $cff_see_less_text, 'custom-facebook-feed' ) ) . '</span></a></span>';
	                                }

	                                $cff_post_item .= '</p>';

	                            }

	                            //Interested in/going
	                            $cff_interested_text = $this->feed_options[ 'interestedtext' ];
	                            $cff_going_text = $this->feed_options[ 'goingtext' ];
	                            if( empty($cff_interested_text) || $cff_interested_text == '' ) $cff_interested_text = 'interested';
	                            if( empty($cff_going_text) || $cff_going_text == '' ) $cff_going_text = 'going';
	                            isset($interested_count) ? $interested_count_num = intval($interested_count) : $interested_count_num = 0;
	                            isset($attending_count) ? $attending_count_num = intval($attending_count) : $attending_count_num = 0;
	                            if( $interested_count_num > 0 || $attending_count_num > 0 ){
	                                $cff_post_item .= '<div class="cff-event-meta">';
	                                if( $interested_count_num > 0 ) $cff_post_item .= $interested_count . ' ' . stripslashes(__( $cff_interested_text, 'custom-facebook-feed' ) );
	                                if( $interested_count_num > 0 && $attending_count_num > 0 ) $cff_post_item .= ' &nbsp;&middot;&nbsp; ';
	                                if( $attending_count_num > 0 ) $cff_post_item .= $attending_count . ' ' . stripslashes(__( $cff_going_text, 'custom-facebook-feed' ) );
	                                $cff_post_item .= '</div>';
	                            }

	                        }
	                    //End details
	                    if ( ($cff_thumb_layout || $cff_half_layout) ) $cff_post_item .= '</div>';
	                    $cff_post_item .= '<div class="cff-meta-wrap">';

	                    $cff_post_item .= '<div class="cff-post-links">';


	                    //Social media sharing URLs
	                    $cff_share_facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($event_link);
	                    $cff_share_twitter = 'https://twitter.com/intent/tweet?text=' . urlencode($event_link);
	                    $cff_share_google = 'https://plus.google.com/share?url=' . urlencode($event_link);
	                    $cff_share_linkedin = 'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . urlencode($event_link) . '&amp;title=' . rawurlencode( strip_tags($cff_event_title) . ' - ' . strip_tags($cff_event_date) );
	                    $cff_share_email = 'mailto:?subject=Facebook&amp;body=' . urlencode($event_link) . '%20-%20' . rawurlencode( strip_tags($cff_event_title) . ' - ' . strip_tags($cff_event_date) );

	                        //Buy tickets link
	                        if($ticket_uri !== '') $cff_post_item .= '<a href="' . $ticket_uri . '" target="_blank" '.$cff_nofollow.' ' . $cff_link_styles . '>'.stripslashes(__( $cff_buy_tickets_text, 'custom-facebook-feed' ) ).'</a><span class="cff-dot" ' . $cff_link_styles . '>&middot;</span>';

	                        $shortened_description = ! empty( $description ) ? CFF_Utils::cff_maybe_shorten_text( $description ) : CFF_Utils::cff_maybe_shorten_text( $cff_event_title );
	                        //View on Facebook link
	                        if($cff_show_facebook_link) $cff_post_item .= '<a class="cff-viewpost" href="' . $event_link . '" ' . $target . $cff_nofollow.' ' . $cff_link_styles . '>'.$cff_facebook_link_text.  '</a>';

	                        //Share link
	                        if($cff_show_facebook_share){
	                            $cff_post_item .= '<div class="cff-share-container">';

	                            if($cff_show_facebook_link) $cff_post_item .= '<span class="cff-dot" ' . $cff_link_styles . '>&middot;</span>';

	                            $cff_post_item .= '<a class="cff-share-link" href="'.$cff_share_facebook.'" title="' . $cff_facebook_share_text . '" ' . $cff_link_styles . '>' . $cff_facebook_share_text . '</a>';
	                            $cff_post_item .= "<div class='cff-share-tooltip'><a href='".$cff_share_facebook."' target='_blank' ".$cff_nofollow." class='cff-facebook-icon'>". CFF_Display_Elements_Pro::get_icon( 'facebook-square' ) . "<span class='cff-screenreader'>Share on Facebook ".esc_html( $shortened_description )."</span></a><a href='".$cff_share_twitter."' target='_blank' ".$cff_nofollow." class='cff-twitter-icon'>". CFF_Display_Elements_Pro::get_icon( 'twitter' ) . "<span class='cff-screenreader'>Share on Twitter ".esc_html( $shortened_description )."</span></a><a href='".$cff_share_linkedin."' target='_blank' ".$cff_nofollow." class='cff-linkedin-icon'>". CFF_Display_Elements_Pro::get_icon( 'linkedin' ) . "<span class='cff-screenreader'>Share on Linked In ".esc_html( $shortened_description )."</span></a><a href='".$cff_share_email."' target='_blank' ".$cff_nofollow." class='cff-email-icon'>" . CFF_Display_Elements_Pro::get_icon( 'envelope' ) . "<span class='cff-screenreader'>Share by Email ".esc_html( $shortened_description )."</span></a>" . CFF_Display_Elements_Pro::get_icon( 'play' ) . "</div></div>";
	                        }

	                        $cff_post_item .= '</div>';

	                    $cff_post_item .= '</div></div>';

	                    //Change the seconds value of the event_time unix value so that if more than 1 event has the same start time then it doesn't get replaced in the posts array
	                    $event_time = substr( $event_time , 0, -1) . rand(1, 9);

	                    //PUSH TO ARRAY if the post should be shown
	                    if( $cff_show_post !== false ) $cff_posts_array = CFF_Utils::cff_array_push_assoc($cff_posts_array, $event_time, $cff_post_item);

	                } // End the loop

	            } // End empty() check

	            //Sort all of the events by all page IDs to show the most recent upcoming events first
	            if(!$cff_past_events) ksort($cff_posts_array);
	            if($cff_past_events) krsort($cff_posts_array);

	        } //End EVENTS ONLY


	        //ALL POSTS
	        if (!$cff_events_only || ($cff_events_only && $cff_events_source == 'timeline') ){

	            //Interpret data with JSON
	            $FBdata = $json_data;
		        if( isset($FBdata->data) ) {
			        if ( ! \cff_main_pro()->cff_error_reporter->are_critical_errors()
			             && isset( $this->atts['sources'] )
			             && is_array( $this->atts['sources'] ) ) {
				        foreach ( $this->atts['sources'] as $source ) {
					        if ( ! empty( $source['error'] ) ) {
						        \CustomFacebookFeed\Builder\CFF_Source::clear_error( $source['account_id'] );
					        }
				        }
			        }
		        }

	            //***STARTS POSTS LOOP***
	            $fbdata_string = '';

	           	$is_featured_post = false;
	            //If the Featured Post extension is active then adjust the loop as there is no 'data'
	            if($cff_featured_post_active && !empty($this->feed_options['featuredpost'])){
	                if( isset($FBdata) && !empty($FBdata) ) $fbdata_string = $FBdata;
	            	$is_featured_post = true;
	            } else {

	                if( $cff_videos_only && isset($FBdata->videos) ){
	                    //Videos only
	                    $fbdata_string = $FBdata->videos->data;
	                } else {
	                    //All other posts
	                    if( isset($FBdata->data) ) $fbdata_string = $FBdata->data;
	                }
	            }

	            $numeric_page_id = '';
	            if( !empty($fbdata_string) && !$is_featured_post ){
	                if ( ($cff_show_only_others || $show_posts_by == 'others') && count($fbdata_string) > 0 && !$cff_reviews ) {
	                    //Get the numeric ID of the page so can compare it to the author of each post
	                    $first_post_id = explode("_", $fbdata_string[0]->id);
	                    $numeric_page_id = $first_post_id[0];
	                }
	            }

	            if($fbdata_string){

	                foreach ($fbdata_string as $news)
	                {

	                    if ($cff_featured_post_active && !empty($this->feed_options['featuredpost'])) $news = $FBdata;

	                    $cff_post_item = '';

	                    //Explode News and Page ID's into 2 values
	                    $PostID = '';
	                    if( isset($news->id) ){
	                        $cff_post_id = $news->id;
	                        $PostID = explode("_", $cff_post_id);
	                    }
	                    if( isset($PostID[0]) ) $orig_post_id = $PostID[0];
	                    if( isset($PostID[1]) ) $orig_post_id .= '_' . $PostID[1];

	                    //Check if it's an album embed
	                    $cff_album_id = $this->feed_options['album'];
	                    ( $cff_album_active && !empty($cff_album_id) ) ? $cff_album_embed = true : $cff_album_embed = false;


	                    //Reassign variable changes from API v3.3 update if it's a timeline feed
	                    // if( strlen($this->feed_options[ 'type' ]) > 10 ){
	                    if( !$cff_albums_only && !$cff_photos_only && !$cff_videos_only && (!$cff_events_only || ($cff_events_only && $cff_events_source == 'timeline') ) && !$cff_album_embed && !$cff_reviews ){

	                        //If the type field is still set then it's still pulling from the existing cache as the new fields aren't being returned
	                        if( !isset($news->type) ){
	                            $news->type 			= isset($news->attachments->data[0]->media_type) ? $news->attachments->data[0]->media_type : '';
	                            $news->link 			= isset($news->attachments->data[0]->unshimmed_url) ? $news->attachments->data[0]->unshimmed_url : '';
	                            $news->description 		= isset($news->attachments->data[0]->description) ? $news->attachments->data[0]->description : '';
	                            $news->name 			= isset($news->attachments->data[0]->title)  ? $news->attachments->data[0]->title : '';
	                            $news->caption 			= isset($news->attachments->data[0]->title)  ? $news->attachments->data[0]->title : '';
	                            $news->source 			= isset($news->attachments->data[0]->media->source) ? $news->attachments->data[0]->media->source : '';
	                            $news->object_id 		= isset($news->attachments->data[0]->target->id) ? $news->attachments->data[0]->target->id : '';

	                        }

	                    }

	                    //Object ID
	                    ( !empty($news->object_id) ) ? $object_id = $news->object_id : $object_id = '';

	                    //Check the post type
	                    $cff_post_type = 'status';
	                    if( isset($news->type) ) $cff_post_type = $news->type;
	                    if( isset($news->attachments->data[0]->media_type) ) $cff_post_type = $news->attachments->data[0]->media_type;

	                    if ($cff_post_type == 'link') {
	                        isset($news->story) ? $story = htmlentities($news->story, ENT_QUOTES, 'UTF-8') : $story = '';
	                        //Check whether it's an event
	                        $event_link_check = "facebook.com/events/";
	                        //Make sure URL doesn't include 'permalink' as that indicates someone else sharing a post from within an event (eg: https://www.facebook.com/events/617323338414282/permalink/617324268414189/) and the event ID is then not retrieved properly from the event URL as it's formatted like so: facebook.com/events/EVENT_ID/permalink/POST_ID
	                        if( isset($news->link) ){
	                            $event_link_check = CFF_Utils::stripos($news->link, $event_link_check);
	                            $event_link_check_2 = CFF_Utils::stripos($news->link, "permalink/");
	                            if ( $event_link_check && !$event_link_check_2 ) $cff_post_type = 'event';
	                        }

	                        //Check whether it's a marketplace post, as Facebook sets them to be the "link" post type. Convert them to be a photo/album type.
	                        if( CFF_Utils::stripos($news->link, "sale_post_id") && isset( $news->attachments->data[0]->subattachments ) ){
	                            $cff_post_type = 'album';
	                            $news->type = 'album';
	                        }
	                    }

	                    //Set the post link
	                    isset($news->link) ? $link = htmlspecialchars($news->link) : $link = '';

	                    //If there's no link provided then link to the individual post
	                    if (empty($news->link)) {
	                        //Link to individual post
	                        if( isset($PostID[1]) ) $link = "https://www.facebook.com/" . $page_id . "/posts/" . $PostID[1];
	                    }

	                    //If it's an event then check whether the URL contains facebook.com
	                    if(isset($news->link)){
	                        if( CFF_Utils::stripos($news->link, "events/") && $cff_post_type == 'event' ){
	                            //Facebook changed the event link from absolute to relative, and so if the link isn't absolute then add facebook.com to front
	                            ( CFF_Utils::stripos($link, 'facebook.com') ) ? $link = $link : $link = 'https://facebook.com' . $link;
	                        }
	                    }

	                    //Is it an album?
	                    $cff_album = false;
	                    $num_photos = 0;

	                    //The album check has to be done this way as checking for attachments/subattachments doesn't work as the posts which have the wrong posts IDs (the album ID instead of the post ID - see Facebook bug report) don't have any post attachments in the API even though they do on Facebook. (Changed in v3.9.1 to use subattachments rather than number in post).
	                    if( !isset($news->status_type) ) $news->status_type = 'status';
	                    if( isset($news->type) && $news->type == 'album' ){

	                        //If it's an "added_photos" post or if it's a photo post with subattachments (eg: a shared post which has multiple photos)
	                        if( $news->status_type == 'added_photos' || $news->type == 'album' || ($news->type == 'photo' && isset( $news->attachments->data[0]->subattachments )) ){

	                            //If it's a group post with attachments, or if the story doesn't contain a number but it has subattachments (eg: a shared post which has multiple photos)
	                            if ( isset( $news->attachments->data[0]->subattachments ) ) {

	                                //... and the link is to an album then it most likely has photo attachments
	                                if (strpos($link,'photos/a.') !== false || $news->type == 'album'){
	                                    $albumLinkArr1 = explode('photos/a.', $link);
	                                    if( isset($albumLinkArr1[1]) ){
	                                        //If it contains a dot then use that
	                                        if (strpos($albumLinkArr1[1], '.') !== false){
	                                            $albumLinkArr2 = explode('.', $albumLinkArr1[1]);
	                                        }
	                                        //Some posts use this format: album_id/post_id/?type=3 so check for this to get the post ID
	                                        //Eg: https://www.facebook.com/352519381830075/photos/a.365472027201477/580676195681058/?type=3
	                                        if (strpos($albumLinkArr1[1], '/') !== false){
	                                            $albumLinkArr2 = explode('/', $albumLinkArr1[1]);
	                                            if( count($albumLinkArr2) > 1 ){
	                                                if( is_numeric( intval($albumLinkArr2[1]) ) ) array_shift($albumLinkArr2);
	                                            }
	                                        }
	                                    }

	                                    //If it has an album link then set the post type to be album
	                                    if( isset($albumLinkArr1[1]) || $news->type == 'album' ){

	                                        $cff_album = true;

	                                        //If the post has subattachments then don't change the post ID to the album ID. If it doesn't then change it to the album ID so that we can at least show the photos from the album
	                                        if( !isset($news->attachments) ){
	                                            //Change the Post ID to be to the post about adding photos to the album
	                                            $cff_post_id = $PostID[0] . '_' . $albumLinkArr2[0];
	                                        }

	                                        //Link to the album instead of the photo
	                                        $album_link = str_replace('photo.php?','media/set/?',$link);
	                                        $link = "https://www.facebook.com/" . $page_id . "/posts/" . $PostID[1];

	                                        //If the album link is a new format then link it to the post
	                                        $album_link_check = 'media/set/?';
	                                        if( CFF_Utils::stripos($album_link, $album_link_check) !== true ) $album_link = $link;

	                                    }
	                                }
	                            }
	                        }

	                    }


	                    //Should we show this post or not?
	                    $cff_show_post = false;
	                    switch ($cff_post_type) {
	                        case 'link':
	                            if ( $cff_show_links_type ) $cff_show_post = true;
	                            break;
	                        case 'event':
	                            if ( $cff_show_event_type ) $cff_show_post = true;
	                            break;
	                        case 'video':
	                             if ( $cff_show_video_type ) $cff_show_post = true;
	                            break;
	                        case 'swf':
	                             if ( $cff_show_video_type ) $cff_show_post = true;
	                            break;
	                        case 'photo':
	                        case 'album':
	                             if ( $cff_show_photos_type && !$cff_album ) $cff_show_post = true;
	                             if ( $cff_show_albums_type && $cff_album ) $cff_show_post = true;
	                            break;
	                        case 'offer':
	                            //Show offer posts if links are shown
	                             if ( $cff_show_links_type ) $cff_show_post = true;
	                            break;
	                        case 'music':
	                            //Show music posts if statuses are shown
	                             if ( $cff_show_status_type ) $cff_show_post = true;
	                            break;
	                        default:
	                            //Check whether it's a status (author comment or like)
	                            if ( $cff_show_status_type && ( !empty($news->message) || isset($news->call_to_action->type) ) ){
	                                //Only show the post if it has post text or a call to action button (eg: job posting)
	                                $cff_show_post = true;
	                            }
	                            break;
	                    }

	                    //Featured Post extension
	                    if( $cff_featured_post_active && !empty($this->feed_options['featuredpost']) ) {
	                        //Always show the post if using the Featured Post extension
	                        $cff_show_post = true;

	                        if( $cff_show_links_type ) $cff_post_type = 'link';
	                        if( $cff_show_event_type ) $cff_post_type = 'event';
	                        if( $cff_show_video_type ) $cff_post_type = 'video';
	                        if( $cff_show_photos_type ) $cff_post_type = 'photo';
	                        if( $cff_show_albums_type ) $cff_post_type = 'album';
	                        if( $cff_show_status_type ) $cff_post_type = 'status';

	                        //If it's a status then use full-width layout by default
	                        if($cff_post_type == 'status') {
	                            $cff_thumb_layout = false;
	                            $cff_half_layout = false;
	                        }
	                    }

	                    //Hide supporter only posts if selected
	                    if( $cff_hide_supporter_posts && isset( $news->privacy->description ) ){
	                        if( $news->privacy->description == 'Supporters only' ) $cff_show_post = false;
	                    }

	                    if ( $show_posts_by === 'me' ) {
		                    // Workaround for mentions included in feeds due to API bug. Might be removable
		                    $from_id = CFF_Parse_Pro::get_from_id( $news );
		                    $post_id = CFF_Parse_Pro::get_post_id( $news );

		                    // only for specific posts types
		                    if ( is_string( $from_id )
		                         && is_string( $post_id )
		                         && $cff_post_type === 'status' ) {
			                    if ( strpos( $post_id, $from_id ) === false )  {
				                    $cff_show_post = false;
			                    }
		                    }


	                    }

	                    //Only show posts containing specified string
	                    //Get post text
	                    $post_text = '';
	                    if (!empty($news->story)) $post_text = $news->story;
	                    if (!empty($news->message)) $post_text = $news->message;
	                    if (!empty($news->name) && empty($news->story) && empty($news->message)) $post_text = $news->name;

	                    //Get description text
	                    if( isset($news->description) ){
	                        $description_text = $news->description;
	                    } else {
	                        isset( $news->caption ) ? $description_text = $news->caption : $description_text = '';
	                    }

	                    //Get timeline event text so can filter it here
	                    if ($cff_post_type == 'event') {
	                        //Get the event id from the event URL. eg: http://www.facebook.com/events/123451234512345/
	                        $event_url = parse_url($link);
	                        $url_parts = explode('/', $event_url['path']);
	                        //Get the id from the parts
	                        $eventID = $url_parts[count($url_parts)-2];

	                        //Is it SSL?
	                        $cff_ssl = '';
	                        if (is_ssl()) $cff_ssl = '&return_ssl_resources=true';

	                        //Get the contents of the event
	                        $event_json_url = 'https://graph.facebook.com/v3.3/'.$eventID.'?fields=cover,place,name,owner,start_time,timezone,id,comments.summary(true){message,created_time},description&access_token=' . $access_token . $cff_ssl;

	                        // Get any existing copy of our transient data
	                        $transient_name = 'cff_tle_' . $eventID;
	                        $transient_name = substr($transient_name, 0, 45);

	                        if ( false === ( $event_json = get_transient( $transient_name ) ) || $event_json === null ) {
	                            //Get the contents of the Facebook page
	                            $event_json = CFF_Utils::cff_fetchUrl($event_json_url);
	                            //Cache the JSON for 180 days as the timeline event info probably isn't going to change
	                            set_transient( $transient_name, $event_json, 60 * 60 * 24 * 180 );
	                        } else {
	                            $event_json = get_transient( $transient_name );
	                            //If we can't find the transient then fall back to just getting the json from the api
	                            if ($event_json == false) $event_json = CFF_Utils::cff_fetchUrl($event_json_url);
	                        }

	                        //Interpret data with JSON
	                        $event_object = json_decode($event_json);

	                        $description_text = '';
	                        if( isset($event_object->name) ) $description_text .= $event_object->name . ' ';
	                        if( isset($event_object->place->location->city) ) $description_text .= $event_object->place->location->city . ' ';
	                        if( isset($event_object->place->location->country) ) $description_text .= $event_object->place->location->country . ' ';
	                        if( isset($event_object->place->location->street) ) $description_text .= $event_object->place->location->street . ' ';
	                        if( isset($event_object->place->name) ) $description_text .= $event_object->place->name . ' ';
	                        if( isset($event_object->description) ) $description_text .= $event_object->description;
	                    }

	                    //Get the filter string
	                    $cff_filter_string = $this->feed_options[ 'filter' ];

	                    if ( $cff_filter_string != '' ){
	                        //Explode it into multiples
	                        $cff_filter_strings_array = explode(',', $cff_filter_string);
	                        //Hide the post if both the post text and description don't contain the string
	                        $string_in_post_text = true;
	                        $string_in_desc = true;

	                        $cff_text_to_be_filtered = '';
	                        (!empty($news->story)) ? $cff_text_to_be_filtered = $news->story . ' ' . $post_text : $cff_text_to_be_filtered = $post_text;
	                        (!empty($news->name)) ? $cff_text_to_be_filtered .= ' ' . $news->name : false;

	                        if ( CFF_Utils::cff_stripos_arr_filter($cff_text_to_be_filtered, $cff_filter_strings_array) === false ) $string_in_post_text = false;
	                        if ( CFF_Utils::cff_stripos_arr_filter($description_text, $cff_filter_strings_array) === false ) $string_in_desc = false;

	                        if( $string_in_post_text == false && $string_in_desc == false ) $cff_show_post = false;
	                    }

	                    $cff_exclude_string = $this->feed_options[ 'exfilter' ];
	                    if ( $cff_exclude_string != '' ){
	                        //Explode it into multiples
	                        $cff_exclude_strings_array = explode(',', $cff_exclude_string);
	                        //Hide the post if both the post text and description don't contain the string
	                        $string_in_post_text = false;
	                        $string_in_desc = false;

	                        $cff_text_to_be_filtered = '';
	                        (!empty($news->story)) ? $cff_text_to_be_filtered = $news->story . ' ' . $post_text : $cff_text_to_be_filtered = $post_text;
	                        (!empty($news->name)) ? $cff_text_to_be_filtered .= ' ' . $news->name : false;

	                        if ( CFF_Utils::cff_stripos_arr_filter($cff_text_to_be_filtered, $cff_exclude_strings_array) !== false ) $string_in_post_text = true;
	                        if ( CFF_Utils::cff_stripos_arr_filter($description_text, $cff_exclude_strings_array) !== false ) $string_in_desc = true;

	                        if( $string_in_post_text == true || $string_in_desc == true ) $cff_show_post = false;
	                    }

	                    // apply PHP filter post text
						$post_text = apply_filters( 'cff_post_text', $post_text );
	                    //Encode after the filtering is done to prevent special characters not working in the filtering
	                    $post_text = htmlentities($post_text, ENT_QUOTES, 'UTF-8');
	                    $description_text = htmlentities($description_text, ENT_QUOTES, 'UTF-8');


	                    //Is it a duplicate post?
	                    if (!isset($prev_post_message)) $prev_post_message = '';
	                    if (!isset($prev_post_link)) $prev_post_link = '';
	                    if (!isset($prev_post_description)) $prev_post_description = '';
	                    isset($news->message) ? $pm = $news->message : $pm = '';
	                    isset($news->link) ? $pl = $news->link : $pl = '';
	                    isset($news->description) ? $pd = $news->description : $pd = '';
	                    if ( ($prev_post_message == $pm) && ($prev_post_link == $pl) && ($prev_post_description == $pd) && !isset($news->call_to_action->type) ) $cff_show_post = false;

	                    //ALBUMS ONLY
	                    if($cff_albums_only && $cff_albums_source == 'photospage') $cff_show_post = true;

	                    //ALBUM EMBED
	                    if( $cff_album_active && !empty($cff_album_id) ) $cff_show_post = true;

	                    //PHOTOS ONLY
	                    if($cff_photos_only) $cff_show_post = true;

	                    //VIDEOS ONLY
	                    if($cff_videos_only) $cff_show_post = true;

	                    //REVIEWS
	                    if($cff_reviews) $cff_show_post = true;

	                    //Check post type and display post if selected
	                    if ( $cff_show_post ) {
	                        //If it isn't then create the post

	                        $cff_offset_show_post = true;
	                        //Offset. If the post index ($i) is less than the offset then don't show the post
	                        if( intval($i) < intval($this->feed_options['offset']) ){
	                            $cff_offset_show_post = false;
	                            $i++;
	                        }

	                        //If there's an offset then show the post until it's set to false above. This has been moved here so that the offset works correctly when only displaying specific post types, as previously it only worked accurately when all posts were shown
	                        if($cff_offset_show_post){

	                            if( !$cff_ext_multifeed_active && !$show_all_reviews ){
	                                //Only create posts for the amount of posts specified
	                                if( intval($this->feed_options['offset']) > 0 ){
	                                    //If offset is being used then stop after showing the number of posts + the offset
	                                    if ( $i == (intval($show_posts) + intval($this->feed_options['offset'])) ) break;
	                                } else {
	                                    //Else just stop after the number of posts to be displayed is reached, unless it's albums only or photos only
	                                    if( ($cff_albums_only && $cff_albums_source == 'photospage') || ( $cff_photos_only && empty($cff_album_id) ) || $cff_videos_only ){
	                                        //Keep going
	                                    } else {
	                                        if ( $i == $show_posts ) break;
	                                    }

	                                }
	                            }
	                            $i++;


	                            //********************************//
	                            //***COMPILE SECTION VARIABLES***//
	                            //********************************//
	                            //Change image size based on layout

	                            if ( !empty($news->picture) ) {

	                                if (!empty($news->object_id)) {
	                                    $picture = 'https://graph.facebook.com/'.$object_id.'/picture?type=normal&amp;width=9999&amp;height=9999';
	                                } else {
	                                    $picture = $news->picture;
	                                }

	                            } else {
	                                //Some group posts don't include the picture field for album posts - but some do. If it doesn't then use the first attachment instead.

	                                if( isset( $news->attachments->data[0]->subattachments->data[0]->media->image->src ) ){
	                                    $picture = $news->attachments->data[0]->subattachments->data[0]->media->image->src;
	                                }

	                            }

	                            //DATE
	                            isset($news->created_time) ? $post_time = $news->created_time : $post_time = '';
	                            if( isset($news->backdated_time) ) $post_time = $news->backdated_time; //If the post is backdated then use that as the date instead

	                            $cff_post_date 	= CFF_Shortcode_Display::get_post_date( $this->feed_options, $news);
	                            $cff_date 		= CFF_Utils::print_template_part( 'item/date', get_defined_vars(), $this);


	                            //Page name and date for lightbox sidebar
	                            $cff_author_name 		= CFF_Shortcode_Display::get_author_name( $news );

	                            $cff_post_date = str_replace('"', "", $cff_post_date);
	                            $cff_lightbox_sidebar_atts = ' data-cff-page-name="'.$cff_author_name.'" data-cff-post-time="'.$cff_post_date.'"';

	                            //Only run if NOT only showing photos from the photos page, or albums, or an album embed
	                            if( !$cff_photos_only && !$cff_videos_only && !($cff_albums_only && $cff_albums_source == 'photospage') && empty($cff_album_id) && !$cff_reviews ){

	                                //Story/post text vars
	                                $post_text = '';
	                                $cff_post_text_type = '';
	                                $cff_story_raw = '';
	                                $cff_message_raw = '';
	                                $cff_name_raw = '';
	                                $text_tags = '';
	                                $post_text_story = '';
	                                $post_text_message = '';

	                                //STORY TAGS
	                                $cff_post_tags = $this->feed_options[ 'posttags' ];

	                                //Use the story
	                                if (!empty($news->story)) {
	                                    $cff_story_raw = $news->story;
	                                    $post_text_story .= htmlspecialchars($cff_story_raw);
	                                    $cff_post_text_type = 'story';


	                                    //Add message and story tags if there are any and the post text is the message or the story
	                                    if( $cff_post_tags && isset($news->story_tags) && !$cff_title_link){

	                                        $text_tags = $news->story_tags;

	                                        //Does the Post Text contain any html tags? - the & symbol is the best indicator of this
	                                        $cff_html_check_array = array('&lt;', '’', '“', '&quot;', '&amp;', '&gt;&gt;');

	                                        //Use the text replace method if contains chars above. Also, to fix a weird Greek language issues when there are more than 3 story tags.
	                                        if( CFF_Utils::cff_stripos_arr($post_text_story, $cff_html_check_array) !== false || ($cff_locale == 'el_GR' && count($news->story_tags) > 3) ) {

	                                            //Loop through the tags
	                                            foreach($text_tags as $message_tag ) {

	                                                ( isset($message_tag->id) ) ? $message_tag = $message_tag : $message_tag = $message_tag[0];

	                                                $tag_name = $message_tag->name;
	                                                $tag_link = '<a href="https://facebook.com/' . $message_tag->id . '" '.$cff_nofollow.'>' . $message_tag->name . '</a>';

	                                                $post_text_story = str_replace($tag_name, $tag_link, $post_text_story);
	                                            }

	                                        } else {

	                                            //If it doesn't contain HTMl tags then use the offset to replace message tags
	                                            $message_tags_arr = array();

	                                            $tag = 0;
	                                            foreach($text_tags as $message_tag ) {
	                                                $tag++;
	                                                ( isset($message_tag->id) ) ? $message_tag = $message_tag : $message_tag = $message_tag[0];

	                                                isset($message_tag->type) ? $tag_type = $message_tag->type : $tag_type = '';

	                                                $message_tags_arr = CFF_Utils::cff_array_push_assoc(
	                                                    $message_tags_arr,
	                                                    $tag,
	                                                    array(
	                                                        'id' => $message_tag->id,
	                                                        'name' => $message_tag->name,
	                                                        'type' => isset($message_tag->type) ? $message_tag->type : '',
	                                                        'offset' => $message_tag->offset,
	                                                        'length' => $message_tag->length
	                                                    )
	                                                );

	                                            }

	                                            //Keep track of the offsets so that if two tags have the same offset then only one is used. Need this as API 2.5 update changed the story_tag JSON format. A duplicate offset usually means '__ was with __ and 3 others'. We don't want to link the '3 others' part.
	                                            $cff_story_tag_offsets = '';
	                                            $cff_story_duplicate_offset = '';

	                                            //Check if there are any duplicate offsets. If so, assign to the cff_story_duplicate_offset var.
	                                            for($tag = count($message_tags_arr); $tag >= 1; $tag--) {
	                                                $c = (string)$message_tags_arr[$tag]['offset'];
	                                                if( strpos( $cff_story_tag_offsets, $c ) !== false && $c !== '0' ){
	                                                    $cff_story_duplicate_offset = $c;
	                                                } else {
	                                                    $cff_story_tag_offsets .= $c . ',';
	                                                }

	                                            }

	                                            for($tag = count($message_tags_arr); $tag >= 1; $tag--) {

	                                                //If the name is blank (aka the story tag doesn't work properly) then don't use it
	                                                if( $message_tags_arr[$tag]['name'] !== '' ) {

	                                                    //If it's an event tag or it has the same offset as another tag then don't display it
	                                                    if( $message_tags_arr[$tag]['type'] == 'event' || $message_tags_arr[$tag]['offset'] == $cff_story_duplicate_offset || $message_tags_arr[$tag]['type'] == 'page' ){
	                                                        //Don't use the story tag in this case otherwise it changes '__ created an event' to '__ created an Name Of Event'
	                                                        //Don't use the story tag if it's a page as it causes an issue when sharing a page: Smash Balloon Dev shared a Smash Balloon.
	                                                    } else {
	                                                        $b = '<a href="https://facebook.com/' . $message_tags_arr[$tag]['id'] . '" target="_blank" '.$cff_nofollow.'>' . $message_tags_arr[$tag]['name'] . '</a>';
	                                                        $c = $message_tags_arr[$tag]['offset'];
	                                                        $d = $message_tags_arr[$tag]['length'];
	                                                        $post_text_story = CFF_Utils::cff_mb_substr_replace( $post_text_story, $b, $c, $d);
	                                                    }

	                                                }

	                                            }


	                                        } // end if/else


	                                    } //END STORY TAGS


	                                }


	                                //POST AUTHOR
	                                $cff_from_id 	= isset($news->from->id) ? $news->from->id : '';
	                                $cff_author 	= CFF_Utils::print_template_part( 'item/author', get_defined_vars());




	                                //Get the actual post text
	                                //Which content should we use?
	                                //Use the message
	                                if (!empty($news->message)) {
	                                    $cff_message_raw = $news->message;

	                                    $post_text_message = htmlspecialchars($cff_message_raw);
	                                    $cff_post_text_type = 'message';

	                                    //MESSAGE TAGS
	                                    //Add message and story tags if there are any and the post text is the message or the story
	                                    if( $cff_post_tags && isset($news->message_tags) && !$cff_title_link){

	                                        $text_tags = $news->message_tags;

	                                        //Does the Post Text contain any html tags? - the & symbol is the best indicator of this
	                                        $cff_html_check_array = array('&lt;', '’', '“', '&quot;', '&amp;', '&gt;&gt;', '&gt;');

	                                        //always use the text replace method if the post contains HTML as the offset char count isn't accurate then
	                                        if( CFF_Utils::cff_stripos_arr($post_text_message, $cff_html_check_array) !== false ) {

	                                            //Loop through the tags
	                                            foreach($text_tags as $message_tag ) {

	                                                ( isset($message_tag->id) ) ? $message_tag = $message_tag : $message_tag = $message_tag[0];

	                                                $tag_name = $message_tag->name;
	                                                $tag_link = '<a href="https://facebook.com/' . $message_tag->id . '" '.$cff_nofollow.'>' . $message_tag->name . '</a>';

	                                                $post_text_message = str_replace($tag_name, $tag_link, $post_text_message);
	                                            }

	                                        } else {
	                                        //If it doesn't contain HTMl tags then use the offset to replace message tags
	                                            $message_tags_arr = array();

	                                            $tag = 0;
	                                            foreach($text_tags as $message_tag ) {

	                                                $tag++;

	                                                ( isset($message_tag->id) ) ? $message_tag = $message_tag : $message_tag = $message_tag[0];

	                                                $message_tags_arr = CFF_Utils::cff_array_push_assoc(
	                                                    $message_tags_arr,
	                                                    $tag,
	                                                    array(
	                                                        'id' => $message_tag->id,
	                                                        'name' => $message_tag->name,
	                                                        'type' => isset($message_tag->type) ? $message_tag->type : '',
	                                                        'offset' => $message_tag->offset,
	                                                        'length' => $message_tag->length
	                                                    )
	                                                );
	                                            }

	                                            //Sort the array by the "offset" key as Facebook doesn't always return them in the correct order
	                                            usort($message_tags_arr, "CustomFacebookFeed\CFF_Utils::cffSortTags");

	                                            for($tag = count($message_tags_arr)-1; $tag >= 0; $tag--) {

	                                                //If the name is blank (aka the story tag doesn't work properly) then don't use it
	                                                if( $message_tags_arr[$tag]['name'] !== '' ) {

                                                            // The API doesn't maintain the upper/lower case of the tags so we need to grab it from the original message so we find the text in the post content and use that in the post text when creating the actual tag link

                                                            // The message_tag['offset'] isn't always accurate when searching the post text due to formatting so we have to find the correct position
                                                            $post_tag_position = strpos( strtolower( $post_text_message ), strtolower( $message_tags_arr[$tag]['name'] ) );

                                                            $original_text_offset = substr($post_text_message, $post_tag_position, $message_tags_arr[$tag]['length']);

	                                                        $b = '<a href="https://facebook.com/' . $message_tags_arr[$tag]['id'] . '" '.$cff_nofollow.'>' . $original_text_offset . '</a>';
	                                                        $c = $message_tags_arr[$tag]['offset'];
	                                                        $d = $message_tags_arr[$tag]['length'];
	                                                        $post_text_message = CFF_Utils::cff_mb_substr_replace( $post_text_message, $b, $c, $d);

	                                                }

	                                            }

	                                        } // end if/else

	                                    } //END MESSAGE TAGS

	                                }


	                                //Check to see whether it's an embedded video so that we can show the name above the post text if necessary
	                                $cff_soundcloud = false;
	                                $cff_spotify = false;
	                                $cff_is_video_embed = false;
	                                if ($cff_post_type == 'video' || $cff_post_type == 'music' || $cff_post_type == 'link'){
	                                    if( !empty($news->source) ){
	                                        $url = $news->source;
	                                    } elseif ( !empty($news->link) ) {
	                                        $url = $news->link;
	                                    } else {
	                                        $url = '';
	                                    }
	                                    //Embeddable video strings
	                                    $youtube = 'youtube.com';
	                                    $youtu = 'youtu.com';
	                                    $vimeo = 'vimeo';
	                                    $youtubeembed = 'youtube.com/embed';
	                                    $soundcloud = 'soundcloud.com';
	                                    $spotify = 'spotify.com';
	                                    $swf = '.swf';
	                                    //Check whether it's a youtube video
	                                    $youtube = CFF_Utils::stripos($url, $youtube);
	                                    $youtu = CFF_Utils::stripos($url, $youtu);
	                                    $youtubeembed = CFF_Utils::stripos($url, $youtubeembed);
	                                    //Check whether it's a SoundCloud embed
	                                    $soundcloudembed = CFF_Utils::stripos($url, $soundcloud);
	                                    //Check whether it's a SoundCloud embed
	                                    $spotifyembed = CFF_Utils::stripos($url, $spotify);
	                                    //Check whether it's a youtube video
	                                    if($youtube || $youtu || $youtubeembed || (CFF_Utils::stripos($url, $vimeo) !== false)) {
	                                        $cff_is_video_embed = true;
	                                    }
	                                    //If it's soundcloud then add it into the shared link box at the bottom of the post
	                                    if( $soundcloudembed ) $cff_soundcloud = true;
	                                    if( $spotifyembed ) $cff_spotify = true;

	                                    $cff_video_name = '';
	                                    if ( $cff_post_type == 'link' && $cff_is_video_embed ) {
	                                        $cff_post_type = 'video';
	                                    }
	                                    //If the name exists and it's a non-embedded video then show the name at the top of the post text
	                                    if( $cff_post_type == 'video' && isset($news->name) && !$cff_is_video_embed ){
	                                        if (!$cff_title_link) $cff_video_name .= '<a href="'.$link.'" '.$target.$cff_nofollow.' '.$cff_posttext_link_color_html.'>';
	                                        $cff_video_name .= htmlspecialchars($news->name);
	                                        if (!$cff_title_link) $cff_video_name .= '</a>';
	                                        $cff_video_name .= '<br />';

	                                        //Only show the video name if there's no post text
	                                        if( empty($post_text_message) || $post_text_message == '' || strlen($post_text_message) < 1 ){

	                                            //If there's no description then show the video name above the post text, otherwise we'll show it below
	                                            if( empty($cff_description) || $cff_description == '' ) $post_text = $cff_video_name;

	                                        }
	                                    }
	                                }

	                                //Add the story and message together
	                                $post_text = '';

	                                //DESCRIPTION
	                                $cff_description = '';
	                                if ( !empty($news->description) || !empty($news->caption) ) {
	                                    $description_text = '';

	                                    if ( !empty($news->description) ) {
	                                        $description_text = $news->description;
	                                    }

	                                    //If the description is the same as the post text then don't show it
	                                    if( $description_text ==  $cff_story_raw || $description_text ==  $cff_message_raw || $description_text ==  $cff_name_raw ){
	                                        $cff_description = '';
	                                    } else {
	                                        //Add links and create HTML
	                                        $cff_description .= '<span class="cff-post-desc" '.$cff_body_styles.'>';

	                                        if ($cff_title_link) {
	                                            $cff_description_tagged = CFF_Utils::cff_wrap_span( $description_text );
	                                        } else {
	                                            $cff_description_text = CFF_Autolink::cff_autolink( $description_text, $link_color=$cff_posttext_link_color );
	                                            $cff_description_tagged = CFF_Utils::cff_desc_tags($cff_description_text);
	                                        }
	                                        $cff_description .= $cff_description_tagged;
	                                        $cff_description .= ' </span>';
	                                    }

	                                    if( $cff_post_type == 'event' || $cff_is_video_embed || $cff_soundcloud || $cff_spotify ) $cff_description = '';
	                                }

	                                //Add the message
	                                if($cff_show_text) $post_text .= $post_text_message;

	                                //If it's a shared video post then add the video name after the post text above the video description so it's all one chunk
	                                if ($news->type == 'video'){
	                                    if( !empty($cff_description) && $cff_description != '' ){
	                                        if( (!empty($post_text) && $post_text != '') && !empty($cff_video_name) ) $post_text .= '<br /><br />';
	                                        $post_text .= $cff_video_name;
	                                    }
	                                }


	                                //Use the name
	                                if (!empty($news->name) && empty($news->story) && empty($news->message)) {
	                                    $cff_name_raw = $news->name;
	                                    $post_text = htmlspecialchars($cff_name_raw);
	                                    $cff_post_text_type = 'name';
	                                }

	                                //OFFER TEXT
	                                if ($cff_post_type == 'offer'){
	                                    isset($news->story) ? $post_text = htmlspecialchars($news->story) . '<br /><br />' : $post_text = '';
	                                    $post_text .= htmlspecialchars($news->name);
	                                    $cff_post_text_type = 'story';
	                                }

	                                //Add the description
	                                if( $cff_show_desc && $cff_post_type != 'offer' && $cff_post_type != 'link' ) $post_text .= $cff_description;

	                                //Change the linebreak element if the text issue setting is enabled
	                                $cff_format_issue = $this->feed_options['textissue'];
	                                ($cff_format_issue == 'true' || $cff_format_issue == 'on') ? $cff_format_issue = true : $cff_format_issue = false;

	                                $cff_linebreak_el = '<br />';
	                                if( $cff_format_issue ) $cff_linebreak_el = '<img class="cff-linebreak" />';


	                                //EVENT
	                                $cff_event_has_cover_photo = false;
	                                $cff_event = '';

	                                if ($cff_show_event_title || $cff_show_event_details) {
	                                    //Check for media
	                                    if ($cff_post_type == 'event') {

	                                        //Get the event id from the event URL. eg: http://www.facebook.com/events/123451234512345/
	                                        $event_url = parse_url($link);
	                                        $url_parts = explode('/', $event_url['path']);
	                                        //Get the id from the parts
	                                        $eventID = $url_parts[count($url_parts)-2];

	                                        //Facebook changed the event link from absolute to relative, and so if the link isn't absolute then add facebook.com to front
	                                        ( CFF_Utils::stripos($link, 'facebook.com') ) ? $link = $link : $link = 'https://facebook.com' . $link;

	                                        //Is it SSL?
	                                        $cff_ssl = '';
	                                        if (is_ssl()) $cff_ssl = '&return_ssl_resources=true';

	                                        //Get the contents of the event
	                                        $event_json_url = 'https://graph.facebook.com/v3.3/'.$eventID.'?fields=cover,place,name,owner,start_time,timezone,id,comments.summary(true){message,created_time},description&access_token=' . $access_token . $cff_ssl;

	                                        // Get any existing copy of our transient data
	                                        $transient_name = 'cff_tle_' . $eventID;
	                                        $transient_name = substr($transient_name, 0, 45);

	                                        if ( false === ( $event_json = get_transient( $transient_name ) ) || $event_json === null ) {
	                                            //Get the contents of the Facebook page
	                                            $event_json = CFF_Utils::cff_fetchUrl($event_json_url);
	                                            //Cache the JSON for 180 days as the timeline event info probably isn't going to change
	                                            set_transient( $transient_name, $event_json, 60 * 60 * 24 * 180 );
	                                        } else {
	                                            $event_json = get_transient( $transient_name );
	                                            //If we can't find the transient then fall back to just getting the json from the api
	                                            if ($event_json == false) $event_json = CFF_Utils::cff_fetchUrl($event_json_url);
	                                        }

	                                        //Interpret data with JSON
	                                        $event_object = json_decode($event_json);
	                                        //Picture
	                                        if( isset($event_object->cover) ){
	                                            $cff_timeline_event_image = $event_object->cover->source;
	                                            $cff_event_has_cover_photo = true;
	                                        } else {
	                                            $cff_timeline_event_image = false;
	                                        }

	                                        $cff_timeline_event_photo = '';
	                                        if($cff_show_media && $cff_timeline_event_image){

	                                            //Fix Photon (Jetpack) issue
	                                            $cff_picture_querystring = '';
	                                            if (parse_url($cff_timeline_event_image, PHP_URL_QUERY)){
	                                                $picture_url_parts = parse_url($cff_timeline_event_image);
	                                                $cff_picture_querystring = $picture_url_parts['query'];
	                                            }

	                                            //Remove any quotes from event name to use in the image alt tag
	                                            (!empty($event_object->name)) ? $cff_event_title = $event_object->name : $cff_event_title = '';
	                                            $cff_event_title = str_replace('"', "", $cff_event_title);
	                                            $cff_event_title = str_replace("'", "", $cff_event_title);

	                                            //Alt text
	                                            isset( $cff_event_title ) ? $cff_alt_text = strip_tags($cff_event_title) : $cff_alt_text = $cff_facebook_link_text;

	                                            $cff_timeline_event_photo .= '<div class="cff-media-wrap">';
	                                            $cff_timeline_event_photo .= '<a title="'.$cff_facebook_link_text.'" class="cff-event-thumb';
	                                            if($cff_event_has_cover_photo) $cff_timeline_event_photo .= ' cff-has-cover';
		                                        $cff_alt_text = apply_filters( 'cff_img_alt', $cff_alt_text );

		                                        $cff_timeline_event_photo .= ' nofancybox" href="'.$link.'" '.$target.$cff_nofollow.' '.$cff_lightbox_sidebar_atts.'><img src="'.$cff_timeline_event_image.'" alt="'.htmlspecialchars($cff_alt_text).'" data-querystring="'.$cff_picture_querystring.'" /></a>';
	                                            $cff_timeline_event_photo .= '</div>';
	                                        }

	                                        //Event date
	                                        isset($event_object->start_time)? $event_time = $event_object->start_time : $event_time = '';
	                                        isset($event_object->end_time) ? $event_end_time = ' - <span class="cff-end-date">' . CFF_Utils::cff_eventdate(strtotime($event_object->end_time), $cff_event_date_formatting, $cff_event_date_custom, $cff_event_timezone_offset, $cff_timezone) . '</span>' : $event_end_time = '';
	                                        //If timezone migration is enabled then remove last 5 characters
	                                        if ( strlen($event_time) == 24 ) $event_time = substr($event_time, 0, -5);
	                                        $cff_event_date = '';
	                                        if (!empty($event_time)) $cff_event_date = '<span class="cff-date" '.$cff_event_date_styles.'><span class="cff-start-date">' . CFF_Utils::cff_eventdate(strtotime($event_time), $cff_event_date_formatting, $cff_event_date_custom, $cff_event_timezone_offset, $cff_timezone) . '</span>' . $event_end_time.'</span>';

	                                        //EVENT
	                                        //Display the event details
	                                        $cff_event .= '<span class="cff-details';
	                                        if($cff_event_has_cover_photo) $cff_event .= ' cff-has-cover';
	                                        $cff_event .= '">';
	                                        //show event date above title
	                                        if ($cff_event_date_position == 'above') $cff_event .= $cff_event_date;
	                                        //Show event title
	                                        if ($cff_show_event_title && !empty($event_object->name)) {
	                                            $cff_event .= '<span class="cff-timeline-event-title" ' . $cff_event_title_styles . '>';
	                                            if ($cff_event_title_link) $cff_event .= '<a href="'.$link.'" '.$target.$cff_nofollow.'>';
	                                            $cff_event .= $event_object->name;
	                                            if ($cff_event_title_link) $cff_event .= '</a>';
	                                            $cff_event .= '</span>';
	                                        }
	                                        //show event date below title
	                                        if ($cff_event_date_position !== 'above') $cff_event .= $cff_event_date;
	                                        //Show event details
	                                        if ($cff_show_event_details){
	                                            //Location
	                                            if (!empty($event_object->place->name)) $cff_event .= '<span class="cff-where" ' . $cff_event_details_styles . '>' . $event_object->place->name . '</span>';
	                                            //Description
	                                            if ( !empty($news->message) || isset($event_object->description) ){

	                                                if( !empty($news->message) ) $description_safe = htmlentities($news->message, ENT_QUOTES, 'UTF-8');
	                                                if( isset($event_object->description) ) $description_safe = htmlentities($event_object->description, ENT_QUOTES, 'UTF-8');

	                                                $description = CFF_Autolink::cff_autolink($description_safe, $link_color=$cff_event_link_color);
	                                                $cff_description_tagged = CFF_Utils::cff_desc_tags($description);

	                                                //If the post test and the event description are the same then don't show the post text otherwise it's shown twice
	                                            	if( $post_text == $cff_description_tagged ) $post_text = '';

	                                                $cff_event .= '<span class="cff-info" ' . $cff_event_details_styles . '>' . $cff_description_tagged . '</span>';

	                                            }
	                                        }
	                                        $cff_event .= '</span>';

	                                        //Add event to post text so it can be included in the char count
	                                        if( !empty($post_text) && $post_text != '' ) $post_text .= $cff_linebreak_el.$cff_linebreak_el;
	                                        $post_text .= $cff_event;

	                                    }

	                                }


	                                //Create note
	                                if ($cff_post_type == 'note') {

	                                    // Get any existing copy of our transient data
	                                    $transient_name = 'cff_tle_' . $cff_post_id;
	                                    $transient_name = substr($transient_name, 0, 45);

	                                    if ( false !== ( $cff_note_json = get_transient( $transient_name ) ) ) {
	                                        $cff_note_json = get_transient( $transient_name );

	                                        //Interpret data with JSON
	                                        $cff_note_obj = json_decode($cff_note_json);
	                                        $cff_note_object = $cff_note_obj->attachments->data[0];
	                                        isset($cff_note_object->title) ? $cff_note_title = htmlentities($cff_note_object->title, ENT_QUOTES, 'UTF-8') : $cff_note_title = '';
	                                        isset($cff_note_object->description) ? $cff_note_description = htmlentities($cff_note_object->description, ENT_QUOTES, 'UTF-8') : $cff_note_description = '';
	                                        isset($cff_note_object->url) ? $cff_note_link = $cff_note_object->url : $cff_note_link = '';
	                                        isset( $cff_note_object->media->image->src ) ? $cff_note_media_src = $cff_note_object->media->image->src : $cff_note_media_src = false;
	                                    } else {
	                                        $attachment_data = '';
	                                        if(isset($news->attachments->data[0])){
	                                            $attachment_data = $news->attachments->data[0];
	                                            isset($attachment_data->title) ? $cff_note_title = htmlentities($attachment_data->title, ENT_QUOTES, 'UTF-8') : $cff_note_title = '';
	                                            isset($attachment_data->description) ? $cff_note_description = htmlentities($attachment_data->description, ENT_QUOTES, 'UTF-8') : $cff_note_description = '';
	                                            isset($attachment_data->unshimmed_url) ? $cff_note_link = $attachment_data->unshimmed_url : $cff_note_link = '';
	                                            isset($attachment_data->media->image->src) ? $cff_note_media_src = $attachment_data->media->image->src : $cff_note_media_src = '';
	                                        }
	                                    }

	                                    //Picture
	                                    $cff_note_picture_html = '';
	                                    if($cff_note_media_src && $cff_show_media){

	                                        //Fix Photon (Jetpack) issue
	                                        $cff_picture_querystring = '';
	                                        if (parse_url($cff_note_media_src, PHP_URL_QUERY)){
	                                            $picture_url_parts = parse_url($cff_note_media_src);
	                                            $cff_picture_querystring = $picture_url_parts['query'];
	                                        }

	                                        //Remove any quotes from note name to use in the image alt tag
	                                        $cff_note_title = str_replace('"', "", $cff_note_title);
	                                        $cff_note_title = str_replace("'", "", $cff_note_title);

	                                        //Alt text
	                                        isset( $cff_note_title ) ? $cff_alt_text = strip_tags($cff_note_title) : $cff_alt_text = $cff_facebook_link_text;

	                                        $cff_note_picture_html .= '<div class="cff-media-wrap">';
	                                        $cff_note_picture_html .= '<a title="'.$cff_facebook_link_text.'" class="cff-event-thumb';
	                                        if($cff_note_media_src) $cff_note_picture_html .= ' cff-has-cover';
		                                    $cff_alt_text = apply_filters( 'cff_img_alt', $cff_alt_text );

		                                    $cff_note_picture_html .= ' nofancybox" href="'.$link.'" '.$target.$cff_nofollow.$cff_lightbox_sidebar_atts.'><img src="'.$cff_note_media_src.'" alt="'.htmlspecialchars($cff_alt_text).'" data-querystring="'.$cff_picture_querystring.'" /></a>';
	                                        $cff_note_picture_html .= '</div>';
	                                    }


	                                    //Note details
	                                    $cff_note = '<span class="cff-details">';
	                                    $cff_note = '<span class="cff-note-title">'.$cff_note_title.'</span>';
	                                    $cff_note .= $cff_note_description;
	                                    $cff_note .= '</span>';

	                                    //Notes don't include any post text and so just replace the post text with the note content
	                                    $post_text = $cff_note;
	                                }

	                               $cff_post_text = CFF_Utils::print_template_part( 'item/post-text', get_defined_vars());
	                                //END POST TEXT

	                                //Add a call to action button if included
	                                if( isset($news->call_to_action->value->link) && !empty($news->call_to_action->value->link) ){

	                                    $cff_cta_link = $news->call_to_action->value->link;

	                                    //If it starts with a slash then it's a relative link so prefix it with facebook.com
	                                    if( $cff_cta_link[0] == '/' ){
	                                        $cff_cta_link = 'https://facebook.com' . $cff_cta_link;
	                                    } else {
	                                        //If it doesn't start with 'http' then add it otherwise the link doesn't work. Don't do this if it's a tel num.
	                                        if (strpos($cff_cta_link, 'http') === false && strpos($cff_cta_link, 'tel:') === false) $cff_cta_link = 'http://' . $cff_cta_link;
	                                    }

	                                    $cff_button_type = $news->call_to_action->type;

	                                    switch ($cff_button_type) {
	                                        case 'GET_DIRECTIONS':
	                                            $cff_translate_get_directions_text = $this->feed_options['getdirections'];
	                                            if (!isset($cff_translate_get_directions_text) || empty($cff_translate_get_directions_text)) $cff_translate_get_directions_text = 'Get Directions';
	                                            $cff_cta_button_text = $cff_translate_get_directions_text;

	                                            //Check for fbgeo link
	                                            if(strpos($cff_cta_link, 'fbgeo://') !== false){
	                                                $cff_cta_link_pieces = explode('"', $cff_cta_link);
	                                                $cff_cta_link = 'https://wego.here.com/directions/mix/'.$cff_cta_link_pieces[1];
	                                            }
	                                            break;
	                                        case 'SHOP_NOW':
	                                            $cff_translate_shop_now_text = $this->feed_options['shopnowtext'];
	                                            if (!isset($cff_translate_shop_now_text) || empty($cff_translate_shop_now_text)) $cff_translate_shop_now_text = 'Shop Now';
	                                            $cff_cta_button_text = $cff_translate_shop_now_text;
	                                            break;
	                                        case 'MESSAGE_PAGE':
	                                            $cff_translate_message_page_text = $this->feed_options['messagepage'];
	                                            if (!isset($cff_translate_message_page_text) || empty($cff_translate_message_page_text)) $cff_translate_message_page_text = 'Message Page';
	                                            $cff_cta_button_text = $cff_translate_message_page_text;
	                                            break;
	                                        case 'LEARN_MORE':
	                                            $cff_translate_learn_more_text = $this->feed_options['learnmoretext'];
	                                            if (!isset($cff_translate_learn_more_text) || empty($cff_translate_learn_more_text)) $cff_translate_learn_more_text = 'Learn More';
	                                            $cff_cta_button_text = $cff_translate_learn_more_text;
	                                            break;
	                                        default:
	                                           $cff_cta_button_text = ucwords(strtolower( str_replace('_',' ',$cff_button_type) ) );
	                                    }

	                                    isset($news->call_to_action->value->app_link) ? $cff_app_link = $news->call_to_action->value->app_link : $cff_app_link = '';

                                        // Set the message page cta to use the default messenger link as the API can sometimes send an invalid link
                                        if ( $cff_button_type == 'MESSAGE_PAGE' ) $cff_cta_link = 'https://m.me/' . $cff_from_id;

	                                    //Add the button to the post if the text isn't "NO_BUTTON"
	                                    if( $cff_button_type != 'NO_BUTTON' ) $cff_post_text .= '<p class="cff-cta-link" '.$cff_title_styles.'><a href="'.$cff_cta_link.'" '.$cff_nofollow.' target="_blank" data-app-link="'.$cff_app_link.'" '.$cff_posttext_link_color_html.'>'.$cff_cta_button_text.'</a></p>';
	                                }


	                                //LINK
	                                $cff_shared_link = '';
	                                //Display shared link
	                                if ($cff_post_type == 'link' || $cff_soundcloud || $cff_spotify) {

	                                    if( $cff_soundcloud ){
	                                        //Put this here so that is also hidden when hiding shared links in the Post Layout settings
	                                        if($cff_soundcloud) {
		                                        if ( ! CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			                                        $cff_shared_link .= '<iframe class="cff-soundcloud" width="100%" height="100" scrolling="no" title="Music player" frameborder="no" src="https://w.soundcloud.com/player/?url=' . $news->link . '&amp;auto_play=false&amp;hide_related=true&amp;show_comments=false&amp;show_user=true&amp;show_reposts=false&amp;visual=false"></iframe>';
		                                        } else {
			                                        $cff_shared_link .= '<span class="cff-iframe-placeholder" data-src="https://w.soundcloud.com/player/?url=' . $news->link . '&amp;auto_play=false&amp;hide_related=true&amp;show_comments=false&amp;show_user=true&amp;show_reposts=false&amp;visual=false" data-type="soundcloud" style="display: none;">placeholder</span>';
		                                        }
	                                        }


	                                    } else if( $cff_spotify ) {

	                                        $spotify_url_arr = array_slice(explode('/', $url), -1);
	                                        $spotify_id = $spotify_url_arr[0];
	                                        $spotify_type = 'track';

	                                        if( strpos($url,'show') !== false ) $spotify_type = 'show';
	                                        if( strpos($url,'playlist') !== false ) $spotify_type = 'playlist';
	                                        if( strpos($url,'album') !== false ) $spotify_type = 'album';
	                                        if( strpos($url,'artist') !== false ) $spotify_type = 'artist';

	                                        //Put this here so that is also hidden when hiding shared links in the Post Layout settings
	                                        if($cff_spotify) {
		                                        if ( ! CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			                                        $cff_shared_link .= '<iframe class="cff-spotify" src="https://open.spotify.com/embed/'.$spotify_type.'/'.$spotify_id.'" width="100%" height="80" frameborder="0" allowtransparency="true" allow="encrypted-media"></iframe>';
		                                        } else {
			                                        $cff_shared_link .= '<span class="cff-iframe-placeholder" data-src="https://open.spotify.com/embed/'.$spotify_type.'/'.$spotify_id.'" data-type="spotify" style="display: none;">placeholder</span>';
		                                        }
	                                        }
	                                    } else {

	                                        $cff_shared_link .= '<div class="cff-shared-link';
	                                        if($cff_disable_link_box) $cff_shared_link .= ' cff-no-styles';

	                                        if($cff_full_link_images) $cff_shared_link .= ' cff-full-size';

	                                        $cff_shared_link .= '" ';

	                                        if(!$cff_disable_link_box) $cff_shared_link .= $cff_link_box_styles;
	                                        $cff_shared_link .= '>';
	                                        $cff_link_image = false;

	                                        if ( isset($news->picture) || isset($news->attachments->data[0]->media->image) ){

	                                            if (!empty($news->picture)) {
	                                                $picture = $news->picture;
	                                                ( isset($news->full_picture) && !empty($news->full_picture) ) ? $full_picture = $news->full_picture : $full_picture = $picture;

	                                                //Set the link image to be the full-size image
	                                                // if($cff_full_link_images) $picture = $full_picture;

	                                                //Check whether the image is a 1x1 placeholder
	                                                $cff_link_image = true;
	                                                $cff_one_x_one = '1x1.';
	                                                if( CFF_Utils::stripos($news->picture, $cff_one_x_one) == true || empty($news->picture) ) $cff_link_image = false;
	                                            }

	                                            //If there's no link image then try the attachment field instead
	                                            if( !$cff_link_image ){
	                                                if( isset($news->attachments->data[0]->media->image) ){
	                                                    $picture = $full_picture = $news->attachments->data[0]->media->image->src;
	                                                    $cff_link_image = true;
	                                                }
	                                            }
	                                            //If there's a picture accompanying the link then display it
	                                            if ($cff_link_image && $cff_show_media) {

	                                                //If link_image_size setting is not set then use the full version
	                                                if( empty($cff_link_image_size) ){
	                                                    $cff_link_image_size = 'largesquare';
	                                                }
	                                                //If the full_link_images is unchecked then use small version
	                                                if( $cff_full_link_images == false ){
	                                                    $cff_link_image_size = 'smallsquare';
	                                                }

	                                                //Manually set image size using setting
	                                                switch ($cff_link_image_size) {
	                                                    case "smallsquare":
	                                                        $picture = $picture;
	                                                        break;
	                                                    case "largesquare":
	                                                        if( isset($news->attachments->data[0]->media->image) ){
	                                                            $picture = $full_picture = $news->attachments->data[0]->media->image->src;
	                                                            $cff_link_image = true;
	                                                        }
	                                                        break;
	                                                    default:
	                                                        $picture = $full_picture;
	                                                        break;
	                                                }


	                                                if( $cff_full_link_images && isset( $news->attachments->data[0]->subattachments->data ) ){
		                                                $media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $news ) ) ) . '"';
		                                                $cff_shared_link .= '<div class="cff-link-slider">';
	                                                    $cff_shared_link .= '<a href="#" class="cff-slider-next">' . CFF_Display_Elements_Pro::get_icon( 'chevron-right' ) . '</a>
	                                                    <a href="#" class="cff-slider-prev">' . CFF_Display_Elements_Pro::get_icon( 'chevron-left' ) . '</a>';

	                                                    $cff_shared_link .= '<a href="'.$link.'" target="_blank" class="cff-link-slider-slides"'.$media_src_set_att.'>';

	                                                    //Loop through attachments
	                                                    foreach ($news->attachments->data[0]->subattachments->data as $attachment_item ) {
	                                                        if( isset($attachment_item->media->image->src) ){
	                                                            $cff_shared_link .= '<div class="cff-link-slider-item">';
	                                                            $cff_shared_link .= '<img src="' . CFF_Display_Elements_Pro::get_media_placeholder( $attachment_item->media->image->src ) . '" alt="Link image" data-orig-source="' . esc_attr( $attachment_item->media->image->src ) . '" />';
	                                                            $cff_shared_link .= '</div>';
	                                                        }
	                                                    }
	                                                    //Put default image at end
	                                                    $cff_shared_link .= '<div class="cff-link-slider-item cff-final-item">';
	                                                    $cff_shared_link .= '<img src="' . CFF_Display_Elements_Pro::get_media_placeholder( $picture ) . '" data-orig-source="'. $full_picture .'" alt="Link image" />';
	                                                    $cff_shared_link .= '</div>';

	                                                    $cff_shared_link .= '</a>'; //End .cff-link-slider-slides
	                                                    $cff_shared_link .= '</div>'; //End .cff-link-slider

	                                                } else {
		                                                $media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $news ) ) ) . '"';
		                                                $cff_shared_link .= '<a class="cff-link" href="'.$link.'" '.$target.$cff_nofollow.$media_src_set_att.' data-full="'.$full_picture.'">';
	                                                    $cff_shared_link .= '<img src="'. CFF_Display_Elements_Pro::get_media_placeholder( $picture ) .'" data-orig-source="'. $picture .'" alt="Link thumbnail" />';
	                                                    $cff_shared_link .= '</a>';
	                                                }

	                                            }
	                                        }

	                                        //Display link name and description
	                                        // if (!empty($news->description)) {
	                                        $cff_shared_link .= '<div class="cff-text-link ';
	                                        if (!$cff_link_image) $cff_shared_link .= 'cff-no-image';
	                                        //The link title:
	                                        $cff_link_title_color_html = '';
	                                        if( isset($cff_link_title_color) && !empty($cff_link_title_color) && $cff_link_title_color != '#' ) $cff_link_title_color_html = 'style="color: #'.$cff_link_title_color.'"';

	                                        if( isset($news->name) ) $cff_shared_link .= '"><'.$cff_link_title_format.' class="cff-link-title" '.$cff_link_title_styles.'><a href="'.$link.'" '.$target.$cff_nofollow_referrer.' '.$cff_link_title_color_html.'>'. htmlentities($news->name, ENT_QUOTES, 'UTF-8') . '</a></'.$cff_link_title_format.'>';
	                                        //The link source:
	                                        if( !empty($news->link) ){
	                                            $cff_link_caption = htmlentities($news->link, ENT_QUOTES, 'UTF-8');
	                                            $cff_link_caption_parts = explode('/', $cff_link_caption);
	                                            if( isset($cff_link_caption_parts[2]) ) $cff_link_caption = $cff_link_caption_parts[2];
	                                        } else {
	                                            $cff_link_caption = '';
	                                        }


	                                        //Shared link styles
	                                        $cff_link_url_color_html = '';
	                                        $cff_link_url_size_html = '';
	                                        if( isset($cff_link_url_color) && !empty($cff_link_url_color) && $cff_link_url_color != '#' ) $cff_link_url_color_html = 'color: #'.str_replace('#', '', $cff_link_url_color).';';
	                                        if( $cff_link_url_size != 'inherit' && !empty($cff_link_url_size) ) $cff_link_url_size_html = 'font-size:'.$cff_link_url_size.'px;';

	                                        $cff_link_styles_html = '';
	                                        if( strlen($cff_link_url_color_html) > 1 || strlen($cff_link_url_size_html) > 1 ) $cff_link_styles_html = 'style="';
	                                        if( strlen($cff_link_url_color_html) > 1 ) $cff_link_styles_html .= $cff_link_url_color_html;
	                                        if( strlen($cff_link_url_size_html) > 1 ) $cff_link_styles_html .= $cff_link_url_size_html;
	                                        if( strlen($cff_link_url_color_html) > 1 || strlen($cff_link_url_size_html) > 1 ) $cff_link_styles_html .= '"';

	                                        if(!empty($cff_link_caption)) $cff_shared_link .= '<p class="cff-link-caption" '.$cff_link_styles_html.'>'.$cff_link_caption.'</p>';
	                                        if ($cff_show_desc) {
	                                            //Truncate desc
	                                            if (!empty($body_limit)) {
	                                                if (strlen($description_text) > $body_limit) $description_text = substr($description_text, 0, $body_limit) . '...';
	                                            }

	                                            //Shared link desc styles
	                                            $cff_link_desc_color_html = '';
	                                            $cff_link_desc_size_html = '';
	                                            if( isset($cff_link_desc_color) && !empty($cff_link_desc_color) && $cff_link_desc_color != '#' ) $cff_link_desc_color_html = 'color: #'.str_replace('#', '', $cff_link_desc_color).';';
	                                            if( $cff_link_desc_size != 'inherit' && !empty($cff_link_desc_size) ) $cff_link_desc_size_html = 'font-size:'.$cff_link_desc_size.'px;';

	                                            $cff_link_desc_styles_html = '';
	                                            if( strlen($cff_link_desc_color_html) > 1 || strlen($cff_link_desc_size_html) > 1 ) $cff_link_desc_styles_html = 'style="';
	                                            if( strlen($cff_link_desc_color_html) > 1 ) $cff_link_desc_styles_html .= $cff_link_desc_color_html;
	                                            if( strlen($cff_link_desc_size_html) > 1 ) $cff_link_desc_styles_html .= $cff_link_desc_size_html;
	                                            if( strlen($cff_link_desc_color_html) > 1 || strlen($cff_link_desc_size_html) > 1 ) $cff_link_desc_styles_html .= '"';

	                                            //Add links and create HTML
	                                            $cff_link_description = '<span class="cff-post-desc" '.$cff_link_desc_styles_html.'>';
	                                            if ($cff_title_link) {
	                                                $cff_link_description .= CFF_Utils::cff_wrap_span( $description_text );
	                                            } else {
	                                                $description_text = CFF_Autolink::cff_autolink( $description_text, $link_color=$cff_posttext_link_color );
	                                                //Replace line breaks with <br> tags
	                                                $cff_link_description .= nl2br($description_text);
	                                            }
	                                            $cff_link_description .= ' </span>';


	                                            if( $description_text != $cff_link_caption ) $cff_shared_link .= $cff_link_description;
	                                        }

	                                        $cff_shared_link .= '</div>';
	                                        // }

	                                        $cff_shared_link .= '</div>';

	                                    } //End soundcloud check

	                                }


	                                //MEDIA
	                                $cff_media = '';
	                                //If it's a photo or a Featured post which is an image
	                                if ($news->type == 'photo' || $news->type == 'album' || $news->type == 'offer' || ( $cff_featured_post_active && !empty($this->feed_options['featuredpost']) && isset($news->images) ) ) {

	                                    if ($cff_post_type == 'offer' && !empty($news->picture)){
	                                        $picture = $news->picture;
	                                    }

	                                    //If the full_picture option is available then use that instead of the object ID method
	                                    if( isset($news->full_picture) ) $picture = $news->full_picture;

	                                    //Use the small image size if set
	                                    if( $cff_image_size == 'small' && isset($news->picture) ) $picture = $news->picture;

	                                    //If $news->picture field is empty then try the attachment instead
	                                    if( empty($news->picture) && isset($news->attachments->data[0]->media_type) ){
	                                        if( isset($news->attachments->data[0]->media->image->src) ){
	                                            $picture = $news->attachments->data[0]->media->image->src;
	                                        }
	                                    }

	                                    if ($cff_facebook_link_text == '') $cff_facebook_link_text = 'View on Facebook';
	                                    $link_text = $cff_facebook_link_text;

	                                    //Disable/enable multi-photo layout
	                                    $cff_multi_img_layout = true;
	                                    $cff_one_image = $this->feed_options['oneimage'];
	                                    ($cff_one_image == 'true' || $cff_one_image == 'on') ? $cff_one_image = true : $cff_one_image = false;
	                                    if( $cff_one_image ) $cff_multi_img_layout = false;

	                                    //Are there multiple photos to display?
	                                    $cff_img_count = 1;
	                                    $cff_portrait = false;
	                                    $cff_img_attachments_html = '';
	                                    $cff_main_img_width = 1200; //Set max-width as 1200px as default and then override with value below
	                                    $cff_main_img_height = 1;

	                                    if( $cff_multi_img_layout ){

	                                        if( $cff_album && isset( $news->attachments->data[0]->subattachments ) ){

	                                            //Total number of attachments
	                                            $cff_attachment_total  = count($news->attachments->data[0]->subattachments->data);
	                                            $cff_attachment_total_display = $cff_attachment_total - 3;
	                                            if( $cff_attachment_total >= 12 ) $cff_attachment_total_display = ''; //If there's more than 12 then we can't display the number as we can only count up to 12 subattachments in the posts API.

	                                            //Loop through attachments
	                                            $cff_img_attachments = '';
	                                            $a = 0;
	                                            foreach ($news->attachments->data[0]->subattachments->data as $attachment_item ) {

	                                                //Check whether it's a product attachment
	                                                $is_product_attachment = false;
	                                                if( isset($attachment_item->type) ){
	                                                    if (strpos($attachment_item->type, 'product') !== false) {
	                                                        $is_product_attachment = true;
	                                                        //If there's only one attachment and it's a product then it's not an album
	                                                        if( $cff_attachment_total < 2 ) $cff_album = false;
	                                                    }
	                                                }

	                                                if( !$is_product_attachment ){
	                                                    $attachment_src = $attachment_item->media->image->src;
	                                                    //Check dimensions of main image
	                                                    if( $a == 0 ){
	                                                        $cff_main_img_width = $attachment_item->media->image->width;
	                                                        $cff_main_img_height = $attachment_item->media->image->height;
	                                                        if( $cff_main_img_height > $cff_main_img_width ) $cff_portrait = true;
	                                                    }
	                                                    //Create HTML for attachments
	                                                    if( $a > 0 && $a < 4 ){
	                                                        $cff_img_attachments .= '<span class="cff-img-wrap cff-crop">';

	                                                        if($cff_img_count == 3) $cff_img_attachments .= '<span class="cff-more-attachments"><span>+'.$cff_attachment_total_display.'</span></span>';

	                                                        $cff_img_attachments .= '<img src="'.CFF_Display_Elements_Pro::get_media_placeholder( $attachment_src ).'" data-orig-source="'.$attachment_src.'" alt="Image attachment" class="cff-multi-image" /></span>';
	                                                        $cff_img_count++;
	                                                    }
	                                                    $a++;
	                                                }

	                                            }

	                                            if( $cff_img_count > 2 ) $cff_img_attachments_html = '<span class="cff-img-attachments">';
	                                            $cff_img_attachments_html .= $cff_img_attachments;
	                                            if( $cff_img_count > 2 ) $cff_img_attachments_html .= '</span>';
	                                        }
	                                    }

		                                $media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $news ) ) ) . '"';

	                                    $cff_media = '<div class="cff-media-wrap">';
	                                    $cff_media .= '<a class="cff-photo';
	                                    if($cff_media_position == 'above') $cff_media .= ' cff-media-above';
	                                    if( $cff_img_count > 1 ) $cff_media .= ' cff-multiple cff-img-layout-'.$cff_img_count;
	                                    if( $cff_portrait ) $cff_media .= ' cff-portrait';
	                                    $cff_media .= ' nofancybox" ';
	                                    if( $cff_img_count > 1 ) $cff_media .= 'style="max-width: '.$cff_main_img_width.'px;" ';
	                                    $cff_media .= $cff_lightbox_sidebar_atts.$media_src_set_att.' href="';

	                                    //If it's an album then link the photo to the album
	                                    if ($cff_album) {
	                                        $link = $album_link;
	                                    }

	                                    //If it's a shared post then change the link to use the Post ID so that it links to the shared post and not the original post that's being shared
	                                    // if( isset($news->status_type) ){
	                                    //     if( $news->status_type == 'shared_story' ) $link = "https://www.facebook.com/" . $cff_post_id;
	                                    // }

	                                    $cff_media .= $link.'" '.$target.$cff_nofollow.'>';

	                                    //Remove any quotes from message
	                                    $cff_message_raw = str_replace('"', "", $cff_message_raw);
	                                    $cff_message_raw = str_replace("'", "", $cff_message_raw);

	                                    //Alt text
	                                    isset( $cff_message_raw ) ? $cff_alt_text = strip_tags($cff_message_raw) : $cff_alt_text = $cff_facebook_link_text;

	                                    if($cff_album) $cff_media .= '<span class="cff-album-icon"></span>';

	                                    //Fix Photon (Jetpack) issue
	                                    $cff_picture_querystring = '';
	                                    if (parse_url($picture, PHP_URL_QUERY)){
	                                        $picture_url_parts = parse_url($picture);
	                                        $cff_picture_querystring = $picture_url_parts['query'];
	                                    }
	                                    if( $cff_img_count > 1 ) $cff_media .= '<span class="cff-img-wrap cff-main-image cff-crop">';
		                                $cff_alt_text = apply_filters( 'cff_img_alt', $cff_alt_text );
		                                $cff_media .= '<img src="'. CFF_Display_Elements_Pro::get_media_placeholder( $picture ) .'"  data-orig-source="'. $picture .'" alt="'.htmlspecialchars($cff_alt_text).'" data-querystring="'.$cff_picture_querystring.'" data-ratio="'.round($cff_main_img_width/$cff_main_img_height,3).'" class="cff-multi-image" />';
	                                    if( $cff_img_count > 1 ) $cff_media .= '</span>';
	                                    $cff_media .= $cff_img_attachments_html;
	                                    $cff_media .= '</a>';
	                                    $cff_media .= '</div>';
	                                }
	                                if ($news->type == 'swf') {

	                                    if (!empty($news->picture)) {
	                                        $picture = $news->picture;
	                                    }
		                                $media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $news ) ) ) . '"';

	                                    $cff_swf_url = 'https://www.facebook.com/permalink.php?story_fbid='.$PostID["1"].'&amp;id='.$PostID['0'];
	                                    $cff_media = '<a href="'.$cff_swf_url.'" class="cff-photo nofancybox';
	                                    if($cff_media_position == 'above') $cff_media .= ' cff-media-above';
	                                    $cff_media .= '" ' . $target . $cff_nofollow.$cff_lightbox_sidebar_atts.$media_src_set_att.'><img src="' . CFF_Display_Elements_Pro::get_media_placeholder( $picture ) . '" /></a>';
	                                }

	                                if ($cff_post_type == 'video' && !$cff_soundcloud && !$cff_spotify) {

	                                    if( !isset($picture) ) $picture = '';

	                                    if (!empty($news->picture)) {
	                                        $picture = $news->picture;
	                                    }

	                                    //If $news->picture field is empty then try the attachment instead
	                                    if( empty($news->picture) && isset($news->attachments->data[0]->media_type) ){
	                                        if( isset($news->attachments->data[0]->media->image->src) ){
	                                            $picture = $news->attachments->data[0]->media->image->src;
	                                        }
	                                    }

	                                    //Type of player to use
	                                    $cff_video_player = $this->feed_options[ 'videoplayer' ];

	                                    // URL of video
	                                    if ( !empty( $news->source ) ) {
	                                        $url = $news->source;
	                                    }
                                    	elseif ( !empty($news->link) ){
                                       		$url = $news->link;
	                                    } else {
	                                        $url = '';
	                                        //If the source field isn't available in the API then use the Facebook Video Player instead which doesn't require that field
	                                        $cff_video_player = 'facebook';
	                                    }

	                                    //Check whether it's a youtube video
	                                    if($youtube || $youtu || $youtubeembed) {
	                                        //Get the unique video id from the url by matching the pattern
	                                        if ($youtube || $youtubeembed) {
	                                            if (preg_match("/v=([^&]+)/i", $url, $matches)) {
	                                                $id = $matches[1];
	                                            }   elseif(preg_match("/\/v\/([^&]+)/i", $url, $matches)) {
	                                                $id = $matches[1];
	                                            }   elseif(preg_match("/\/embed\/([^&]+)/i", $url, $matches)) {
	                                                $id = $matches[1];
	                                            }
	                                        } elseif ($youtu) {
	                                            $youtu_url_arr = array_slice(explode('/', $url), -1);
	                                            $id = $youtu_url_arr[0];
	                                        }
	                                        if( strrpos($id, '?') ) $id = substr($id, 0, strrpos($id, '?'));
	                                        // this is your template for generating embed codes
		                                    if ( ! CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			                                    $code = '<iframe class="youtube-player" type="text/html" src="https://www.youtube.com/embed/{id}" allowfullscreen title="YouTube video"></iframe>';
		                                    } else {
			                                    $code = '<span class="cff-iframe-placeholder" data-src="https://www.youtube.com/embed/{id}" data-type="youtube" style="display: none;">placeholder</span>';
		                                    }
	                                        // we replace each {id} with the actual ID of the video to get embed code for this particular video
	                                        $code = str_replace('{id}', $id, $code);
		                                    $iframe_class = '';
		                                    if ( ! CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
		                                    	$iframe_class = ' cff-iframe-wrap-disabled';
		                                    }
		                                    $cff_media_video = '<div class="cff-iframe-wrap'.$iframe_class.'" data-poster="'.$picture.'" '.$cff_lightbox_sidebar_atts;
		                                    if(!empty($cff_video_height)) $cff_media_video .= 'style="height: '. $cff_video_height . '"';
		                                    $cff_media_video .= '>';


	                                        //Don't use full post ID as link to Facebook post no longer works
	                                        if( isset($PostID[1]) ) $cff_post_id = $PostID[1];

	                                        if($cff_video_action == 'facebook') $cff_media_video .= '<a href="https://facebook.com/'.$cff_post_id.'" target="_blank" '.$cff_nofollow.' class="cff-media-overlay"></a>';

	                                        //Add image as it's needed for lightbox ordering for embedded iframe videos
		                                    if ( ! CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			                                    $cff_media_video .= '<img src="'.$picture.'" alt="Video image" class="cff-iframe-img" />';
		                                    }

	                                        $cff_media_video .= $code . '</div>';

	                                    //Check whether it's a vimeo
	                                    } else if(CFF_Utils::stripos($url, $vimeo) !== false) {

                                            // The API doesn't always use the source field so we check for the unshimmed_url field here
                                            $news->source = ( !empty( $news->source ) ) ? $news->source : $news->attachments->data[0]->unshimmed_url;

	                                        if ( !empty( $news->source ) ) {

	                                            $clip_id = '';
	                                            //http://vimeo.com/moogaloop.swf?clip_id=101557016&autoplay=1
	                                            $query = parse_url($news->source, PHP_URL_QUERY);
	                                            parse_str($query, $params);
	                                            if(isset($params['clip_id'])) $clip_id = $params['clip_id'];

	                                            //https://player.vimeo.com/video/116446625?autoplay=1
	                                            if( !isset($clip_id) || $clip_id == '' ){
	                                                $vimeo_url = strtok($news->source,'?');
	                                                $vimeo_url_arr = array_slice(explode('/', $vimeo_url), -1);
	                                                $clip_id = $vimeo_url_arr[0];
	                                            }

	                                            $cff_media_video = '<div class="cff-iframe-wrap" data-poster="'.$picture.'" '.$cff_lightbox_sidebar_atts;
	                                            if(!empty($cff_video_height)) $cff_media_video .= 'style="height: '. $cff_video_height . '"';
	                                            $cff_media_video .= '>';

	                                            //Don't use full post ID as link to Facebook post no longer works
	                                            if( isset($PostID[1]) ) $cff_post_id = $PostID[1];

	                                            if($cff_video_action == 'facebook') $cff_media_video .= '<a href="https://facebook.com/'.$cff_post_id.'" target="_blank" '.$cff_nofollow.' class="cff-media-overlay"></a>';

	                                            //Add image as it's needed for lightbox ordering
	                                            $cff_media_video .= '<img src="'.CFF_Display_Elements_Pro::get_media_placeholder( $picture ).'" alt="Video image" class="cff-iframe-img" />';

		                                        if ( ! CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			                                        $cff_media_video .= '<iframe src="https://player.vimeo.com/video/'.$clip_id.'" webkitAllowFullScreen mozallowfullscreen allowFullScreen title="Vimeo video"></iframe></div>';
		                                        } else {
			                                        $cff_media_video .= '<span class="cff-iframe-placeholder" data-src="https://player.vimeo.com/video/'.$clip_id.'" data-type="vimeo" style="display: none;">placeholder</span></div>';
		                                        }
	                                        }

	                                    //Else use a video player
	                                    } else {

	                                        //Don't use full post ID as link to Facebook post no longer works
	                                        if( isset($PostID[1]) ) $cff_post_id = $PostID[1];

	                                        //Show play button over video thumbnail
	                                        isset($news->source) ? $vid_link = $news->source : $vid_link = '';
	                                        //Check whether the video source contains an mp4, as the HTML5 video player can't play any other type
	                                        $cff_mp4_check = CFF_Utils::stripos($vid_link, '.mp4');
	                                        //Check whether it's a live video. When live the source format is https://www.facebook.com/video/playback/playlist.m3u8?v=1201515613297938
	                                        $cff_live_video = CFF_Utils::stripos($vid_link, '/video/playback/playlist');
	                                        ( $cff_live_video ) ? $cff_live_video = 'true' : $cff_live_video = 'false';

	                                        if ($cff_video_action == 'facebook' && $cff_disable_lightbox) $vid_link = $link;

	                                        //Title & alt text
	                                        isset( $news->name ) ? $vid_title = htmlentities($news->name, ENT_QUOTES, 'UTF-8') : $vid_title = $cff_facebook_link_text;

	                                        if (empty($picture)) {
	                                            $cff_is_video_embed = true;
	                                            $cff_media_video = '<a class="cff-playbtn-solo" title="' . $vid_title . '" href="' . $vid_link . '" target="_blank" '.$cff_nofollow.'>' . CFF_Display_Elements_Pro::get_icon( 'play', '', 'cff-playbtn' ) . '</a>';
	                                        }

	                                        ( isset($news->full_picture) && !empty($news->full_picture) ) ? $poster = $news->full_picture : $poster = $picture;
	                                        //If there's an image in the attachment then use that instead
	                                        if( isset( $news->attachments->data[0]->media->image->src ) ) $poster = $news->attachments->data[0]->media->image->src;


	                                        //Check to see whether it's a swf file and if it is then load it into an iframe in the lightbox
	                                        (CFF_Utils::stripos($url, $swf) !== false) ? $swf_file = true : $swf_file = false;

	                                        //Fix Photon (Jetpack) issue
	                                        $cff_picture_querystring = '';
	                                        if (parse_url($poster, PHP_URL_QUERY)){
	                                            $picture_url_parts = parse_url($poster);
	                                            $cff_picture_querystring = $picture_url_parts['query'];
	                                        }

	                                        //If it's a canvas_doc video then use the video URL from the subattachment field as the main video doesn't work in the Facebook Video Player widget
	                                        if( isset($news->link) ){
	                                            if (strpos($news->link, '/canvas_doc/') !== false) {
	                                                if( isset( $news->attachments->data[0]->subattachments ) ){
	                                                    $link = $news->attachments->data[0]->subattachments->data[0]->url;
	                                                }
	                                            }
	                                        }

	                                        //Disable/enable multi-photo layout
	                                        $cff_multi_img_layout = true;
	                                        $cff_one_image = $this->feed_options['oneimage'];
	                                        ($cff_one_image == 'true' || $cff_one_image == 'on') ? $cff_one_image = true : $cff_one_image = false;
	                                        if( $cff_one_image ) $cff_multi_img_layout = false;

	                                        //Are there multiple photos to display?
	                                        $cff_img_count = 1;
	                                        $cff_portrait = false;
	                                        $cff_img_attachments_html = '';
	                                        $cff_main_img_width = 1200; //Set max-width as 1200px as default and then override with value below
	                                        $cff_main_img_height = 1;

	                                        if( $cff_multi_img_layout ){
	                                            if( isset( $news->attachments->data[0]->subattachments ) ){

	                                                //Total number of attachments
	                                                $cff_attachment_total  = count($news->attachments->data[0]->subattachments->data);
	                                                $cff_attachment_total_display = $cff_attachment_total - 3;
	                                                if( $cff_attachment_total >= 12 ) $cff_attachment_total_display = '';

	                                                //Loop through attachments
	                                                $cff_img_attachments = '';
	                                                $a = 0;
	                                                foreach ($news->attachments->data[0]->subattachments->data as $attachment_item ) {

	                                                    //Check whether it's a product attachment
	                                                    $is_product_attachment = false;
	                                                    if( isset($attachment_item->type) ){
	                                                        if (strpos($attachment_item->type, 'product') !== false) {
	                                                            $is_product_attachment = true;
	                                                            //If there's only one attachment and it's a product then it's not an album
	                                                            if( $cff_attachment_total < 2 ) $cff_album = false;
	                                                        }
	                                                    }

	                                                    if( !$is_product_attachment ){

	                                                        $attachment_src = $attachment_item->media->image->src;
	                                                        //Check dimensions of main image
	                                                        if( $a == 0 ){
	                                                            $cff_main_img_width = $attachment_item->media->image->width;
	                                                            $cff_main_img_height = $attachment_item->media->image->height;
	                                                            if( $cff_main_img_height > $cff_main_img_width ) $cff_portrait = true;
	                                                        }
	                                                        //Create HTML for attachments
	                                                        if( $a > 0 && $a < 4 ){
	                                                            $cff_img_attachments .= '<span class="cff-img-wrap cff-crop">';

	                                                            if( $cff_disable_lightbox ) $cff_img_attachments .= "<a href='https://www.facebook.com/" . $page_id . "/posts/" . $PostID[1] ."' ".$target.$cff_nofollow." style='width:100%; float:left; clear: both; z-index: 1; position: relative;'>";

	                                                            if($cff_img_count == 3) $cff_img_attachments .= '<span class="cff-more-attachments"><span>+'.$cff_attachment_total_display.'</span></span>';

	                                                            if( $cff_disable_lightbox ) $cff_img_attachments .= '</a>';

	                                                            $cff_img_attachments .= '<img src="'.CFF_Display_Elements_Pro::get_media_placeholder( $attachment_src ).'" alt="Image attachment" /></span>';
	                                                            $cff_img_count++;
	                                                        }
	                                                        $a++;

	                                                    }

	                                                }

	                                                if( $cff_img_count > 2 ) $cff_img_attachments_html = '<span class="cff-img-attachments">';
	                                                $cff_img_attachments_html .= $cff_img_attachments;
	                                                if( $cff_img_count > 2 ) $cff_img_attachments_html .= '</span>';
	                                            }
	                                        }


	                                        //If the video action is file then add the HTML5 video tags
	                                        $cff_media_video = '';
	                                        $cff_media_video .= '<div class="cff-html5-video';
	                                        if( $cff_img_count > 1 ) $cff_media_video .= ' cff-multiple cff-img-layout-'.$cff_img_count;
	                                        if( $cff_portrait ) $cff_media_video .= ' cff-portrait';
	                                        if( $swf_file ) $cff_media_video .= ' cff-swf';
	                                        $cff_media_video .= '"'.$cff_lightbox_sidebar_atts . ' data-cff-video-link="'.$link.'" data-cff-video-player="'.$cff_video_player.'" data-cff-live="'.$cff_live_video.'">';

	                                        if( $cff_img_count > 1 ){
	                                            $cff_media_video .= '<span class="cff-img-wrap cff-main-image';

	                                            if( $cff_video_action !== 'facebook' && $cff_mp4_check && $cff_disable_lightbox ){
	                                                //Don't crop the video so we can play it directly in the feed
	                                            } else {
	                                                //Else crop it
	                                                $cff_media_video .= ' cff-crop';
	                                            }

	                                            $cff_media_video .= '">';
	                                        }

	                                        $poster_img = $poster;
	                                        //Use the small image size if set
	                                        if( $cff_image_size == 'small' && isset($news->picture) ) $poster_img = $news->picture;

	                                        //Include the VIDEO element
	                                        //Fallback video link
	                                        $cff_vid_link = '<a title="' . $vid_title . '" class="cff-vidLink" href="' . $link . '" '.$target.$cff_nofollow.'>' . CFF_Display_Elements_Pro::get_icon( 'play', '', 'cff-playbtn' ) . '<img class="cff-poster" src="' . $poster_img . '" data-orig-source="' . $poster_img . '" alt="' . htmlspecialchars($vid_title) . '" data-querystring="'.$cff_picture_querystring.'" data-ratio="'.round($cff_main_img_width/$cff_main_img_height,3).'" /></a>';

		                                    $media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $news ) ) ) . '"';
	                                        //If lightbox enabled
	                                        if( !$cff_disable_lightbox ){

	                                            //Add image and play button
	                                            $cff_media_video .= '<a href="https://facebook.com/'.$cff_post_id.'" class="cff-html5-play">' . CFF_Display_Elements_Pro::get_icon( 'play', '', 'cff-playbtn' ) . '</span><span class="cff-screenreader">Play</span></a>';
	                                            $cff_media_video .= '<img class="cff-poster" src="' . CFF_Display_Elements_Pro::get_media_placeholder( $poster_img ) . '" data-orig-source="' . $poster_img . '" data-cff-full-img="'.$poster.'" alt="' . htmlspecialchars($vid_title) . '" data-querystring="'.$cff_picture_querystring.'" data-cff-video="'.$vid_link.'" style="float: left;" data-ratio="'.round($cff_main_img_width/$cff_main_img_height,3).'"' .$media_src_set_att. ' />';

	                                        } else { // Lightbox disabled


	                                            //Link to Facebook post
	                                            if( $cff_video_action == 'facebook' ){

	                                                $cff_media_video .= $cff_vid_link;

	                                            } else { //Play in feed

	                                                //Use HTML5 player
	                                                if( isset( $news->source ) && $cff_mp4_check && $cff_video_player == 'standard' ){

	                                                    $cff_media_video .= '<a href="https://facebook.com/'.$cff_post_id.'" class="cff-html5-play" '.$cff_nofollow.'>' . CFF_Display_Elements_Pro::get_icon( 'play', '', 'cff-playbtn' ) . '</a>';
	                                                    //If pagination is enabled then display the poster image over the video element as in Chrome there's a video flicker when loading more posts
	                                                    if( $this->feed_options['loadmore'] ) $cff_media_video .= '<img class="cff-poster" src="' . $poster_img . '" alt="' . htmlspecialchars($vid_title) . '" data-querystring="'.$cff_picture_querystring.'" style="position: absolute; top: 0; left: 0; z-index: 7;" data-ratio="'.round($cff_main_img_width/$cff_main_img_height,3).'" />';

		                                                if ( ! CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			                                                $cff_media_video .= '<video src="'.$vid_link.'" poster="'.$poster_img.'" preload="none" >';
			                                                $cff_media_video .= $cff_vid_link;
			                                                $cff_media_video .= '</video>';
		                                                } else {
			                                                $cff_media_video .= '<span class="cff-iframe-placeholder" data-src="'.$vid_link.'" data-poster-src="'.$poster_img.'" data-type="video" style="display: none;">'.$cff_vid_link.'</span>';
		                                                }


	                                                } else { //Use Facebook Video Player

	                                                    $cff_media_video .= '<div class="cff-vidLink cff-video-player">';
	                                                    $cff_media_video .= '<div class="fb-video" data-href="'.$link.'" data-show-text="false" fb-xfbml-state="rendered">';
		                                                if ( ! CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			                                                $cff_media_video .= '<iframe src="https://www.facebook.com/v2.3/plugins/video.php?href='.$link.'" title="Facebook video player" allowfullscreen frameborder="0" webkitallowfullscreen mozallowfullscreen></iframe></div>';
		                                                } else {
			                                                $cff_media_video .= '<span class="cff-iframe-placeholder" data-src="https://www.facebook.com/v2.3/plugins/video.php?href='.$link.'" data-type="facebook" style="display: none;">placeholder</span></div>';
		                                                }

	                                                    $cff_media_video .= '<img class="cff-poster" src="' . CFF_Display_Elements_Pro::get_media_placeholder( $poster_img ) . '" alt="' . htmlspecialchars($vid_title) . '"'.$media_src_set_att.' />';
	                                                    $cff_media_video .= '</div>';

	                                                }

	                                            }

	                                        }

	                                        //Link to the Facebook post if it's a link or a video
	                                        if($cff_post_type == 'link' || $cff_post_type == 'video') $link = "https://www.facebook.com/" . $page_id . "/posts/" . $PostID[1];

	                                        if( $cff_img_count > 1 ) $cff_media_video .= '</span>';
	                                        if( $cff_disable_lightbox && $cff_img_count < 2 ) $cff_media_video .= "<a href='https://www.facebook.com/" . $page_id . "/posts/" . $PostID[1] ."' ".$target.$cff_nofollow." style='width:100%; float:left; clear: both; z-index: 1; position: relative;'>";
	                                        $cff_media_video .= $cff_img_attachments_html;
	                                        if( $cff_disable_lightbox && $cff_img_count < 2 ) $cff_media_video .= '</a>';
	                                        $cff_media_video .= '</div>';

	                                    }
	                                    //Add video to HTML
	                                    $cff_media = $cff_media_video;


	                                    //Add the name to the description if it's a video embed
	                                    if($cff_is_video_embed) {
	                                        $cff_description = '<div class="cff-desc-wrap ';
	                                        if (empty($picture)) $cff_description .= 'cff-no-image';

	                                        $cff_link_title_color_html = '';
	                                        if( isset($cff_link_title_color) && !empty($cff_link_title_color) && $cff_link_title_color != '#' ) $cff_link_title_color_html = 'style="color: #'.$cff_link_title_color.'"';

	                                        $cff_description .= '"><'.$cff_link_title_format.' class="cff-link-title" '.$cff_link_title_styles.'><a href="'.$link.'" '.$target.$cff_nofollow.' '.$cff_link_title_color_html.'>'. htmlentities($news->name, ENT_QUOTES, 'UTF-8') . '</a></'.$cff_link_title_format.'>';

	                                        if (!empty($body_limit)) {
	                                            if (strlen($description_text) > $body_limit) $description_text = substr($description_text, 0, $body_limit) . '...';
	                                        }

	                                        $cff_description .= '<p class="cff-post-desc" '.$cff_body_styles.'><span>';
	                                        if ($cff_title_link) {
	                                            $cff_description_tagged = CFF_Utils::cff_wrap_span( $description_text );
	                                        } else {
	                                            $cff_description_text = CFF_Autolink::cff_autolink( $description_text, $link_color=$cff_posttext_link_color );
	                                            $cff_description_tagged = CFF_Utils::cff_desc_tags($cff_description_text);
	                                        }
	                                        $cff_description .= $cff_description_tagged;
	                                        $cff_description .= ' </span></p></div>';

	                                    }
	                                }

	                                //META
	                                //how many comments are there?
	                                $comment_count = 0;
	                                $comment_count_display = '0';

	                                //Save the original $news object to a variable so can use it after the comments section
	                                $news_event = $news;
	                                //If it's a timeline event then switch to the event_object variable which contains the comments
	                                if( $cff_post_type == 'event' && isset($event_object) ) $news = $event_object;

	                                if (!empty($news->comments->data)) {
	                                    isset($news->comments->summary->total_count) ? $comment_count = intval($news->comments->summary->total_count) : $comment_count = 0;
	                                    $comment_count_display = $comment_count;
	                                }

	                                $cff_like_svg = '<svg width="24px" height="24px" role="img" aria-hidden="true" aria-label="Like" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M496.656 285.683C506.583 272.809 512 256 512 235.468c-.001-37.674-32.073-72.571-72.727-72.571h-70.15c8.72-17.368 20.695-38.911 20.695-69.817C389.819 34.672 366.518 0 306.91 0c-29.995 0-41.126 37.918-46.829 67.228-3.407 17.511-6.626 34.052-16.525 43.951C219.986 134.75 184 192 162.382 203.625c-2.189.922-4.986 1.648-8.032 2.223C148.577 197.484 138.931 192 128 192H32c-17.673 0-32 14.327-32 32v256c0 17.673 14.327 32 32 32h96c17.673 0 32-14.327 32-32v-8.74c32.495 0 100.687 40.747 177.455 40.726 5.505.003 37.65.03 41.013 0 59.282.014 92.255-35.887 90.335-89.793 15.127-17.727 22.539-43.337 18.225-67.105 12.456-19.526 15.126-47.07 9.628-69.405zM32 480V224h96v256H32zm424.017-203.648C472 288 472 336 450.41 347.017c13.522 22.76 1.352 53.216-15.015 61.996 8.293 52.54-18.961 70.606-57.212 70.974-3.312.03-37.247 0-40.727 0-72.929 0-134.742-40.727-177.455-40.727V235.625c37.708 0 72.305-67.939 106.183-101.818 30.545-30.545 20.363-81.454 40.727-101.817 50.909 0 50.909 35.517 50.909 61.091 0 42.189-30.545 61.09-30.545 101.817h111.999c22.73 0 40.627 20.364 40.727 40.727.099 20.363-8.001 36.375-23.984 40.727zM104 432c0 13.255-10.745 24-24 24s-24-10.745-24-24 10.745-24 24-24 24 10.745 24 24z"></path></svg>'.'<svg width="24px" height="24px" class="cff-svg-bg" role="img" aria-hidden="true" aria-label="background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M104 224H24c-13.255 0-24 10.745-24 24v240c0 13.255 10.745 24 24 24h80c13.255 0 24-10.745 24-24V248c0-13.255-10.745-24-24-24zM64 472c-13.255 0-24-10.745-24-24s10.745-24 24-24 24 10.745 24 24-10.745 24-24 24zM384 81.452c0 42.416-25.97 66.208-33.277 94.548h101.723c33.397 0 59.397 27.746 59.553 58.098.084 17.938-7.546 37.249-19.439 49.197l-.11.11c9.836 23.337 8.237 56.037-9.308 79.469 8.681 25.895-.069 57.704-16.382 74.757 4.298 17.598 2.244 32.575-6.148 44.632C440.202 511.587 389.616 512 346.839 512l-2.845-.001c-48.287-.017-87.806-17.598-119.56-31.725-15.957-7.099-36.821-15.887-52.651-16.178-6.54-.12-11.783-5.457-11.783-11.998v-213.77c0-3.2 1.282-6.271 3.558-8.521 39.614-39.144 56.648-80.587 89.117-113.111 14.804-14.832 20.188-37.236 25.393-58.902C282.515 39.293 291.817 0 312 0c24 0 72 8 72 81.452z"></path></svg>';
	                                $cff_share_svg ='<svg width="24px" height="24px" role="img" aria-hidden="true" aria-label="Share" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M564.907 196.35L388.91 12.366C364.216-13.45 320 3.746 320 40.016v88.154C154.548 130.155 0 160.103 0 331.19c0 94.98 55.84 150.231 89.13 174.571 24.233 17.722 58.021-4.992 49.68-34.51C100.937 336.887 165.575 321.972 320 320.16V408c0 36.239 44.19 53.494 68.91 27.65l175.998-184c14.79-15.47 14.79-39.83-.001-55.3zm-23.127 33.18l-176 184c-4.933 5.16-13.78 1.73-13.78-5.53V288c-171.396 0-295.313 9.707-243.98 191.7C72 453.36 32 405.59 32 331.19 32 171.18 194.886 160 352 160V40c0-7.262 8.851-10.69 13.78-5.53l176 184a7.978 7.978 0 0 1 0 11.06z"></path></svg>'.'<svg width="24px" height="24px" class="cff-svg-bg" role="img" aria-hidden="true" aria-label="background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z"></path></svg>';
	                                $cff_comment_svg = '<svg width="24px" height="24px" role="img" aria-hidden="true" aria-label="Comment" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm32 352c0 17.6-14.4 32-32 32H293.3l-8.5 6.4L192 460v-76H64c-17.6 0-32-14.4-32-32V64c0-17.6 14.4-32 32-32h384c17.6 0 32 14.4 32 32v288z"></path></svg>'.'<svg width="24px" height="24px" class="cff-svg-bg" role="img" aria-hidden="true" aria-label="background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"></path></svg>';

	                                //Start comments HTML
	                                $cff_meta_total = '<div class="cff-meta-wrap">';

	                                //If lightbox comments are enabled but regular comments aren't then enable regular comments and enable using CSS
	                                //Lightbox comments
	                                $cff_lightbox_comments = true;
	                                if( $this->feed_options[ 'lightboxcomments' ] === 'false' || $this->feed_options['lightboxcomments'] == false ) $cff_lightbox_comments = false;

	                                //Disable lightbox comments if it's a dedicated feed type
	                                if( ( $cff_events_only && $cff_events_source == 'eventspage' ) || $cff_albums_only || $cff_photos_only || $cff_videos_only) $cff_lightbox_comments = false;
	                                //Include string for meta
	                                $cff_includes = $this->feed_options[ 'include' ];
	                                $cff_show_meta = false;
	                                if ( CFF_Utils::stripos($cff_includes, 'social') !== false ) $cff_show_meta = true;
	                                //Exclude string for meta
	                                $cff_excludes = $this->feed_options[ 'exclude' ];
	                                if ( CFF_Utils::stripos($cff_excludes, 'social') !== false ) $cff_show_meta = false;

	                                //Create the likes, shares, comments box
	                                $l_c_s_info = CFF_Shortcode_Display::get_like_comment_icons_info( $cff_post_type, $news, $news_event, $cff_is_group );
	                                $like_count = $l_c_s_info['like']['count'];
									if( $cff_show_meta || $cff_lightbox_comments ){
										$cff_meta = CFF_Utils::print_template_part( 'item/likes-comments-box', get_defined_vars(), $this);
									}

	                                //Link to the Facebook post if it's a link or a video
	                                if($cff_post_type == 'link' || $cff_post_type == 'video') $link = "https://www.facebook.com/" . $page_id . "/posts/" . $PostID[1];

	                                //If Featured Post extension then change the $link var based on whether a full or half post ID is used
	                                if ($cff_featured_post_active && !empty($this->feed_options['featuredpost'])) {

	                                    //If the post type is a link or a video (other link types have the link included in the JSON)
	                                    if($cff_post_type == 'link' || $cff_post_type == 'video'){

	                                        if ( CFF_Utils::stripos($cff_post_id, '_') !== false ) {
	                                            //If using the full post ID with an underscore then create the link like this:
	                                            $link = "https://www.facebook.com/" . $PostID[0] . "/posts/" . $PostID[1];
	                                        } else {
	                                            //If just using the short ID then create the link like this:
	                                            $link = "https://www.facebook.com/" . $cff_post_id;
	                                        }
	                                    }

	                                }
	                                //If there's an object_id then use that as it's more reliable for posts by other people
	                                if( !empty($news->object_id) ){
	                                    $link = "https://www.facebook.com/" . $news->object_id;
	                                }



	                                //Create post action links HTML
	                                $cff_link =  CFF_Utils::print_template_part( 'item/post-link', get_defined_vars(), $this);


	                                //If lightbox comments are enabled then enable comments (they're hidden using a style tag added to the cff-meta element)
	                                if($cff_lightbox_comments) $cff_show_meta = true;

	                                //Compile the meta and link if included

	                                if ($cff_show_link) $cff_meta_total .= $cff_link;
	                                #$cff_meta_total .= $cff_link;
	                                if ($cff_show_meta) $cff_meta_total .= $cff_meta;
	                                $cff_meta_total .= '</div>';
	                                $cff_comments = '';

	                                //Get custom text strings


	                                $cff_translate_view_previous_comments_text 	= CFF_Utils::return_value( $this->feed_options['previouscommentstext'], esc_html__('View previous comments', 'custom-facebook-feed') );
	                                $cff_translate_comment_on_facebook_text 	= CFF_Utils::return_value( $this->feed_options['commentonfacebooktext'], esc_html__('Comment on Facebook', 'custom-facebook-feed') );
	                                $cff_translate_likes_this_text 				= CFF_Utils::return_value( $this->feed_options['likesthistext'], esc_html__('likes this', 'custom-facebook-feed') );
	                                $cff_translate_like_this_text 				= CFF_Utils::return_value( $this->feed_options['likethistext'], esc_html__('like this', 'custom-facebook-feed') );
	                                $cff_translate_reacted_text 				= CFF_Utils::return_value( $this->feed_options['reactedtothistext'], esc_html__('reacted to this', 'custom-facebook-feed') );
	                                $cff_translate_and_text 					= CFF_Utils::return_value( $this->feed_options['andtext'], esc_html__('and', 'custom-facebook-feed') );
	                                $cff_translate_other_text 					= CFF_Utils::return_value( $this->feed_options['othertext'], esc_html__('other', 'custom-facebook-feed') );
	                                $cff_translate_others_text 					= CFF_Utils::return_value( $this->feed_options['otherstext'], esc_html__('others', 'custom-facebook-feed') );
	                                $cff_translate_reply_text					= CFF_Utils::return_value( $this->feed_options['replytext'], esc_html__('Reply', 'custom-facebook-feed') );
	                                $cff_translate_replies_text 				= CFF_Utils::return_value( $this->feed_options['repliestext'], esc_html__('Replies', 'custom-facebook-feed') );


	                                //Create the comments box
	                                if( $cff_show_meta || $cff_lightbox_comments ){

	                                    $cff_comments .= '<div class="cff-comments-box ' . $cff_icon_style;
	                                    if( $comment_count === 0 || $cff_comments_num === 0 ) $cff_comments .= ' cff-no-comments';

	                                    //If it's a shared post then add a class so I can use this in the query.php request, as it uses the post_id to get likes for shared posts
	                                    if( isset($news->status_type) ){
	                                        if( $news->status_type == 'shared_story' ) $cff_comments .= ' cff-shared-story';
	                                    }

	                                    $cff_comments .= '"';
	                                    $cff_comments .= ' data-cff-like-text="'.$cff_translate_like_this_text.'" data-cff-likes-text="'.$cff_translate_likes_this_text.'"  data-cff-reacted-text="'.$cff_translate_reacted_text.'" data-cff-and-text="'.$cff_translate_and_text.'" data-cff-other-text="'.$cff_translate_other_text.'" data-cff-others-text="'.$cff_translate_others_text.'" data-cff-reply-text="'.$cff_translate_reply_text.'" data-cff-replies-text="'.$cff_translate_replies_text.'"';

	                                    //Expand comments box initially
	                                    if( $cff_expand_comments ) $cff_comments .= ' style="display: block;"';
	                                    //Number of comments to show initially
	                                    $cff_comments .= ' data-num="' . $cff_comments_num . '"';
	                                    $cff_comments .= ' data-cff-meta-link-color="' . $cff_meta_link_color . '"';
	                                    $cff_comments .= ' data-cff-hide-avatars="' . $cff_hide_comment_avatars . '"';
	                                    $cff_comments .= ' data-cff-expand-comments="' . $cff_expand_comments . '"';
	                                    $cff_comments .= ' data-cff-post-tags="' . $cff_post_tags . '"';
	                                    $cff_comments .= '>';


	                                    //If it's a timeline event then change the $news object to be the original news object before it was changed to get the event comment count above
	                                    if( $cff_post_type == 'event' ) $news = $news_event;

	                                    //Get the likes
	                                    //If there are likes and it's not an events only feed then add the likes section to the top of the comments box to be populated in JS
	                                    if ( $like_count > 0 && ( $cff_events_only && $cff_events_source == 'eventspage' ) == false ){
	                                        $cff_comments .= '<p class="cff-comment-likes cff-likes" ' . $cff_meta_styles . '></p>';
	                                    }

	                                    //If it's a timeline event then change the $news object to be the event object to get the event comment count above
	                                    if( $cff_post_type == 'event' && isset($event_object) ) $news = $event_object;
		                                $shortened_description = ! empty( $description_text ) ? CFF_Utils::cff_maybe_shorten_text( $description_text ) : $cff_post_id;

	                                    //Comment on Facebook link
	                                    $cff_comments .= '<p class="cff-comments cff-comment-on-facebook" ' . $cff_meta_styles . '><a href="'.$link.'" '.$target.$cff_nofollow.' style="color:'.$cff_meta_link_color.'"><span class="cff-icon">'.$cff_comment_svg.'</span>'.$cff_translate_comment_on_facebook_text.'</a></p>';

	                                    $cff_comments .= '<div class="cff-comments-wrap" ' . $cff_meta_styles . '>';

	                                    //Display just the comments text
	                                    if (!empty($news->comments->data)){
	                                        //Give the comment an index so we know which one it is
	                                        $comment_index = 0;

	                                        //Loop through comments
	                                        foreach ($news->comments->data as $comment_item ) {
	                                            $comment = htmlentities($comment_item->message, ENT_QUOTES, 'UTF-8');

	                                            //Create comments. These are replaced using JS.
	                                            $cff_comments .= '<div class="cff-comment" id="cff_'.$comment_item->id.'" data-cff-comment-date="'. CFF_Shortcode_Display::get_date( $this->feed_options, $comment_item ) .'">';
	                                            $cff_comments .= '<p ' . $cff_meta_styles . '>' . CFF_Autolink::cff_autolink( $comment, $link_color=str_replace('#', '', $this->feed_options['sociallinkcolor']) ) . '</p>';
	                                            $cff_comments .= '</div>'; //End .cff-comment

	                                            $comment_index++;
	                                        }

	                                    }

	                                    //Show more comments


	                                    $cff_elipsis_svg = '<svg width="24px" height="24px" role="img" aria-hidden="true" aria-label="ellipsis" xmlns="http://www.w3.org/2000/svg" viewBox="0 150 320 200"><path d="M192 256c0 17.7-14.3 32-32 32s-32-14.3-32-32 14.3-32 32-32 32 14.3 32 32zm88-32c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32zm-240 0c-17.7 0-32 14.3-32 32s14.3 32 32 32 32-14.3 32-32-14.3-32-32-32z"></path></svg>';
	                                    $cff_translate_view_previous_comments_text = $cff_elipsis_svg . '<span class="cff-screenreader">View more comments</span>';

	                                    if ( $comment_count > $cff_comments_num ) $cff_comments .= '<p class="cff-comments cff-show-more-comments" ' . $cff_meta_styles . '><a class="cff-show-more-comments-a" href="javascript:void(0);" style="color:'.$cff_meta_link_color.'">'.$cff_translate_view_previous_comments_text.'</a></p>';

	                                    $cff_comments .= '</div>';

	                                    $cff_comments .= '</div>';
	                                    //Compile comments if meta is included
	                                    if ($cff_show_meta) $cff_meta_total .= $cff_comments;

	                                } // End creating the comments box

	                                //If it's an event then set the $news object back to the original posts data rather than the new event data object used to get the comments for the event
	                                if( $cff_post_type == 'event' ) $news = $news_event;

	                                //Calculate the z-index value so masonry comments won't overlap when opened
	                                $zindex = intval($show_posts)+10 - $i;
	                                //Converts negative values to be 1
	                                $zindex = max($zindex, 1);

	                                //CFF item styles
	                                $cff_item_styles = '';
	                                if( $cff_post_style == 'boxed' || $cff_post_bg_color_check ){
	                                    $cff_item_styles = 'style="';
	                                    $cff_item_styles .= 'z-index: '.$zindex.';';
	                                    if($cff_post_bg_color_check) $cff_item_styles .= 'background-color: #' . $cff_post_bg_color . '; ';
	                                    if( isset($cff_post_rounded) && $cff_post_rounded !== '0' && !empty($cff_post_rounded) ) $cff_item_styles .= '-webkit-border-radius: ' . $cff_post_rounded . 'px; -moz-border-radius: ' . $cff_post_rounded . 'px; border-radius: ' . $cff_post_rounded . 'px; ';
	                                    $cff_item_styles .= '"';
	                                }
	                                if( $cff_post_style == 'regular' && ($cff_sep_color_check || $cff_sep_size_check) ){
	                                    $cff_item_styles .= 'style="border-bottom: ' . $cff_sep_size . 'px solid #' . str_replace('#', '', $cff_sep_color) . '; z-index: '.$zindex.';"';
	                                }

	                                //**************************//
	                                //***CREATE THE POST HTML***//
	                                //**************************//

	                                //Start the container
	                                $cff_post_item .= '<div class="cff-item ';
	                                $cff_post_type_class = ' cff-status-post';
	                                if ($cff_post_type == 'link') $cff_post_type_class = 'cff-link-item';
	                                if ($cff_post_type == 'event') $cff_post_type_class = 'cff-timeline-event';
	                                if ($cff_post_type == 'photo' || $cff_post_type == 'album') $cff_post_type_class = 'cff-photo-post';
	                                if ($cff_post_type == 'video' && !$cff_soundcloud) $cff_post_item .= 'cff-video-post';
	                                if ($cff_soundcloud || $cff_spotify || $cff_post_type == 'music') $cff_post_item .= 'cff-audio-post ';
	                                if ($cff_is_video_embed) $cff_post_item .= ' cff-embedded-video ';
	                                if ($cff_post_type == 'swf') $cff_post_type_class = 'cff-swf-post';
	                                if ($cff_post_type == 'offer') $cff_post_type_class = 'cff-offer-post';
	                                $cff_post_item .= $cff_post_type_class;

	                                if ($cff_album) $cff_post_item .= ' cff-album';
	                                if ($cff_post_bg_color_check || $cff_post_style == "boxed") $cff_post_item .= ' cff-box';
	                                if( $cff_box_shadow ) $cff_post_item .= ' cff-shadow';
	                                $cff_post_item .= ' author-';
	                                if(isset($news->from->name)) $cff_post_item .= CFF_Utils::cff_to_slug($news->from->name);
	                                $cff_post_item .= ' cff-' . $page_id;
	                                $cff_post_item .= ' cff-new';
	                                $cff_post_item .= '" id="cff_'. $cff_post_id .'"';
	                    			$cff_post_item .= ' data-page-id="'.$page_id.'"';
	                                $cff_post_item .= ' data-cff-timestamp="';
	                                if( isset($news->created_time) ) $cff_post_item .= strtotime($news->created_time);
	                                $cff_post_item .= '"';
	                                if( isset($news->backdated_time) ) $cff_post_item .= ' data-cff-backdated="' . strtotime($news->backdated_time) . '"';
	                                $cff_post_item .= ' data-object-id="'.$object_id.'"';
	                                if( isset($news->from->id) ) $cff_post_item .= ' data-cff-from="'.$news->from->id.'"';
	                                if ( $this->feed_options['loadcommentsjs'] == 'true' ) $cff_post_item .= 'data-comments-js="true"';
	                                $cff_post_item .= ' ' . $cff_item_styles . '>';

	                                //POST AUTHOR
	                                $cff_is_video_embed = false;
	                                if($cff_is_video_embed){
	                                    if($cff_show_author) $cff_post_item .= $cff_author;
	                                    //DATE ABOVE
	                                    if ($cff_show_date && $cff_date_position == 'above') $cff_post_item .= $cff_date;
	                                    //If embedded video then show post text above the wrapper
	                                    if($cff_show_text) $cff_post_item .= $cff_post_text;

	                                    $cff_post_item .= '<div class="cff-embed-wrap">';
	                                }


	                                //Start text wrapper
	                                if ( ($cff_thumb_layout || $cff_half_layout) && (!empty($news->picture) || $cff_post_type == 'album' || ($cff_post_type == 'event' && $cff_event_has_cover_photo) || ($news->type == 'note' && $cff_note_media_src) ) ){
	                                    $cff_post_item .= '<div class="cff-text-wrapper">';
	                                }

	                                    //POST AUTHOR
	                                    if($cff_show_author && !$cff_is_video_embed) $cff_post_item .= $cff_author;
	                                    //MEDIA
	                                    if($cff_show_media && $cff_media_position == 'above'){
	                                        if( $cff_post_type == 'event' ) $cff_media = $cff_timeline_event_photo;
	                                        if( $news->type == 'note' ) $cff_media = $cff_note_picture_html;
	                                        $cff_post_item .= $cff_media;
	                                    }
	                                    //DATE ABOVE
	                                    if ($cff_show_date && $cff_date_position == 'above' && !$cff_is_video_embed) $cff_post_item .= $cff_date;
	                                    //POST TEXT
	                                    if( ($cff_show_text || $cff_show_desc) && !$cff_is_video_embed) $cff_post_item .= $cff_post_text;
	                                    //LINK
	                                    if($cff_show_shared_links) $cff_post_item .= $cff_shared_link;
	                                    //DATE BELOW
	                                    if ( (!$cff_show_author && $cff_date_position == 'author') || $cff_show_date && $cff_date_position == 'below' && !$cff_is_video_embed && ($cff_thumb_layout || $cff_half_layout) ) {
	                                        if($cff_show_date && $cff_post_type !== 'event') $cff_post_item .= $cff_date;
	                                    }

	                                //End text wrapper
	                                if ( ($cff_thumb_layout || $cff_half_layout) && (!empty($news->picture) || $cff_post_type == 'album' || ($cff_post_type == 'event' && $cff_event_has_cover_photo) || ($news->type == 'note' && $cff_note_media_src) ) ){
	                                    $cff_post_item .= '</div>';
	                                }


	                                //MEDIA
	                                if($cff_show_media && $cff_media_position !== 'above') {
	                                    if( $cff_post_type == 'event' ) $cff_media = $cff_timeline_event_photo;
	                                    if( $news->type == 'note' ) $cff_media = $cff_note_picture_html;
	                                    $cff_post_item .= $cff_media;
	                                    if($cff_is_video_embed) $cff_post_item .= '</div>';
	                                }
	                                //DATE BELOW
	                                if ($cff_show_date && $cff_date_position == 'below' && ( (!$cff_thumb_layout && !$cff_half_layout) || $cff_is_video_embed) ) $cff_post_item .= $cff_date;
	                                if($cff_show_date && $cff_post_type == 'event' && ($cff_date_position == 'below' || ($cff_date_position == 'author' && !$cff_show_author) ) ){
	                                    $cff_post_item .= $cff_date;
	                                }
	                                //META
	                                if($cff_show_meta || $cff_show_link) $cff_post_item .= $cff_meta_total;
	                                //End the post item
	                                $cff_post_item .= '</div>';
	                                // $cff_post_item .= '<div class="cff-clear"></div>';

	                            } // End !$cff_photos_only || albums only || album embed

	                            //REVIEWS
	                            if($cff_reviews){
	                                $cff_post_item = cff_ext_reviews($news, '', $this->feed_options, $page_id, $target, $cff_nofollow, $cff_author_styles, $cff_show_date, $cff_date_position, $cff_title_format, $cff_title_styles, $cff_posttext_link_color, $cff_see_more_text, $cff_date, $cff_title_link, $cff_see_less_text, $cff_show_facebook_link, $cff_post_bg_color_check, $post_time, $cff_item_styles, $cff_show_author, $cff_show_link, $cff_post_type, $link, $cff_link_styles, $cff_show_text, $cff_show_post, $cff_filter_string, $cff_exclude_string, $this->feed_options['pagetoken']);
	                            }


	                            //ALBUMS ONLY
	                            if( ($cff_albums_only && $cff_albums_source == 'photospage') && empty($cff_album_id) ){

	                                isset($news->link) ? $cff_album_link = $news->link : $cff_album_link = '';
	                                isset($news->name) ? $cff_album_name = $news->name : $cff_album_name = '';
	                                isset($news->description) ? $cff_album_description = $news->description : $cff_album_description = '';

	                                $cff_show_post = true;
	                                //Get the filter string
	                                $cff_filter_string = $this->feed_options[ 'filter' ];

	                                if ( $cff_filter_string != '' ){
	                                    //Explode it into multiples
	                                    $cff_filter_strings_array = explode(',', $cff_filter_string);

	                                    $cff_text_to_be_filtered = $cff_album_name . ' ' . $cff_album_description;

	                                    if ( CFF_Utils::cff_stripos_arr_filter($cff_text_to_be_filtered, $cff_filter_strings_array) === false ) $cff_show_post = false;
	                                }


	                                $cff_exclude_string = $this->feed_options[ 'exfilter' ];
	                                if ( $cff_exclude_string != '' ){
	                                    //Explode it into multiples
	                                    $cff_exclude_strings_array = explode(',', $cff_exclude_string);

	                                    $cff_text_to_be_filtered = $cff_album_name . ' ' . $cff_album_description;

	                                    if ( CFF_Utils::cff_stripos_arr_filter($cff_text_to_be_filtered, $cff_exclude_strings_array) !== false ) $cff_show_post = false;
	                                }

	                                //Encode text after filtering is done
	                                $cff_album_name = htmlentities($cff_album_name, ENT_QUOTES, 'UTF-8');
	                                $cff_album_description = htmlentities($cff_album_description, ENT_QUOTES, 'UTF-8');

	                                if( $cff_show_post ){

	                                    $cff_cover_photos_available = true;
	                                    $album_full_picture = '';
	                                    $thumb = '';

	                                    //ALBUMS ONLY
	                                    if($cff_is_group){ //Groups need to use token in the request:
	                                        if( isset($news->cover_photo->id) ){
	                                            $thumb = 'https://graph.facebook.com/' . $news->cover_photo->id . '/picture?access_token='.$access_token;
	                                        } else {
	                                            $thumb = '';
	                                            $cff_cover_photos_available = false;
	                                        }
	                                    } else {
	                                        if( isset($news->cover_photo) ){

	                                            if( isset($news->cover_photo->source) ){
	                                                $thumb = $news->cover_photo->source;
	                                            } else if( isset($news->cover_photo->id) ){
	                                                $thumb = 'https://graph.facebook.com/' . $news->cover_photo->id . '/picture';
	                                            } else {
	                                                $thumb = '';
	                                                $cff_cover_photos_available = false;
	                                            }

	                                        }
	                                    }


	                                    isset($news->count) ? $cff_album_count = $news->count : $cff_album_count = '';

	                                    //Cover photos aren't available for group albums unless using a User Access Token
	                                    $cff_post_item = '<div class="cff-album-item cff-albums-only cff-col-';
	                                    $cff_post_item .= $cff_album_cols;
	                                    if( isset($page_id) ) $cff_post_item .= ' cff-' . $page_id;
	                                    $cff_post_item .= ' cff-new" ';
	                    				$cff_post_item .= ' data-page-id="'.$page_id.'"';
	                                    $cff_post_item .= ' data-cff-timestamp="';
	                                    if( isset($news->created_time) ) $cff_post_item .= strtotime($news->created_time);
	                                    $cff_post_item .= '"';
	                                    $cff_post_item .= 'id="cff_'. $news->id .'">';

	                                    //Fix Photon (Jetpack) issue
	                                    $cff_picture_querystring = '';
	                                    if (parse_url($thumb, PHP_URL_QUERY)){
	                                        $picture_url_parts = parse_url($thumb);
	                                        $cff_picture_querystring = $picture_url_parts['query'];
	                                    }
	                                    // here
										$media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $thumb ) ) ) . '"';
	                                    if( $cff_cover_photos_available ) $cff_post_item .= '<a href="' . $cff_album_link . '" class="cff-album-cover nofancybox" '.$target.$cff_nofollow.$media_src_set_att.'><img src="'.CFF_Display_Elements_Pro::get_media_placeholder( $thumb ).'" data-orig-source="'.$thumb.'" alt="' . htmlspecialchars($cff_album_name) . '" data-querystring="'.$cff_picture_querystring.'"data-cff-full-img="'.$album_full_picture.'" /></a>';
	                                    if($cff_show_album_title || $cff_show_album_number) $cff_post_item .= '<div class="cff-album-info">';
	                                    if($cff_show_album_title) $cff_post_item .= '<h4><a href="' . $cff_album_link . '" '.$target.$cff_nofollow.'>' . $cff_album_name . '</a></h4>';
	                                    if( $cff_show_album_number && isset($news->count) ) $cff_post_item .= '<p>' . $cff_album_count . ' '. $cff_translate_photos_text . '</p>';
	                                    if($cff_show_album_title || $cff_show_album_number) $cff_post_item .= '</div>';
	                                    $cff_post_item .= '</div>';

	                                    //Group albums use 'created' instead of 'created_time' like other posts
	                                    if($cff_is_group){
	                                        ( isset($news->created) ) ? $post_time = $news->created : $post_time = $news->created_time;
	                                    } else {
	                                        //By default albums aren't ordered by date as we use the native order of the API (which is based on when a photo was last added to an album) but for multifeed we need to use the date to order them and so set it to be the updated_time so that when we order the array then it's more accurate as they're more likely to be based on when a photo was added than the created_time
	                                        if( $cff_ext_multifeed_active && count($page_ids) > 1 ){
	                                            ( isset($news->updated_time) ) ? $post_time = $news->updated_time : $post_time = $news->created_time;
	                                        }

	                                        //If there's no photos in the album then don't show it
	                                        if( !isset($news->cover_photo) ){
	                                            $cff_post_item = '<p class="cff-empty-album';
	                                            if( isset($page_id) ) $cff_post_item .= ' cff-' . $page_id;
	                                            $cff_post_item .= '"></p>';
	                                        }
	                                    }

	                                }

	                                //If the album item is filtered out then set it to be an empty album item so that it doesn't affect the offset pagination
	                                if( empty($cff_post_item) ){
	                                    $cff_post_item = '<p class="cff-empty-album';
	                                    if( isset($page_id) ) $cff_post_item .= ' cff-' . $page_id;
	                                    $cff_post_item .= '"></p>';
	                                }

	                            }

	                            //ALBUM EMBED
	                            if( $cff_album_active && !empty($cff_album_id) ){

	                                isset($news->name) ? $cff_album_desc = $news->name : $cff_album_desc = '';

	                                $cff_show_post = true;
	                                //Get the filter string
	                                $cff_filter_string = $this->feed_options[ 'filter' ];

	                                if ( $cff_filter_string != '' ){
	                                    //Explode it into multiples
	                                    $cff_filter_strings_array = explode(',', $cff_filter_string);
	                                    //Hide the post if both the post text and description don't contain the string
	                                    $string_in_post_text = true;
	                                    $string_in_desc = true;
	                                    if ( CFF_Utils::cff_stripos_arr_filter($cff_album_desc, $cff_filter_strings_array) === false ) $cff_show_post = false;
	                                }

	                                $cff_exclude_string = $this->feed_options[ 'exfilter' ];
	                                if ( $cff_exclude_string != '' ){
	                                    //Explode it into multiples
	                                    $cff_exclude_strings_array = explode(',', $cff_exclude_string);
	                                    //Hide the post if both the post text and description don't contain the string
	                                    $string_in_post_text = false;
	                                    $string_in_desc = false;
	                                    if ( CFF_Utils::cff_stripos_arr_filter($cff_album_desc, $cff_exclude_strings_array) !== false ) $cff_show_post = false;
	                                }

	                                //Encode text after filtering
	                                $cff_album_desc = htmlentities($cff_album_desc, ENT_QUOTES, 'UTF-8');

	                                //Escape quotes
	                                $cff_album_desc = str_replace( '"', '&quot;', $cff_album_desc );

	                                if( $cff_show_post ){
	                                    $cff_post_item = '<div class="cff-album-item cff-col-';
	                                    $cff_post_item .= $cff_album_cols;


	                                    //Fix Photon (Jetpack) issue
	                                    $cff_picture_querystring = '';
	                                    if (parse_url($news->source, PHP_URL_QUERY)){
	                                        $picture_url_parts = parse_url($news->source);
	                                        $cff_picture_querystring = $picture_url_parts['query'];
	                                    }

	                                    //Add the full size image source to an attr so can be added to the lightbox link
	                                    ( isset($news->images) ) ? $cff_full_size_image = $news->images[0]->source : $cff_full_size_image = '';

	                                    if( isset($page_id) ) $cff_post_item .= ' cff-' . $page_id;
	                                    $cff_post_item .= ' cff-new';
	                                    $cff_post_item .= '" id="cff_'. $news->id .'"';
	                                    $cff_post_item .= ' data-cff-timestamp="';
	                                    if( isset($news->created_time) ) $cff_post_item .= strtotime($news->created_time);
	                                    $cff_post_item .= '"';
	                                    $cff_post_item .= ' data-object-id="'.$object_id.'"';
	                                    $cff_post_item .= ' data-cff-full-size="'.$cff_full_size_image.'">';
		                                $media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $news ) ) ) . '"';

		                                $cff_post_item .= '<a href="https://facebook.com/'.$news->id.'" class="cff-album-cover nofancybox" '.$target.$cff_nofollow.$media_src_set_att.'><img src="'. CFF_Display_Elements_Pro::get_media_placeholder($news->source) .'" data-orig-source="'. $news->source .'" alt="'.htmlspecialchars($cff_album_desc).'" data-querystring="'.$cff_picture_querystring.'" /></a>';
	                                    $cff_post_item .= '</div>';
	                                    $post_time = $i;
	                                }

	                                //If the album item is filtered out then set it to be an empty album item so that it doesn't affect the offset pagination
	                                if( empty($cff_post_item) ){
	                                    $cff_post_item = '<p class="cff-empty-album';
	                                    if( isset($page_id) ) $cff_post_item .= ' cff-' . $page_id;
	                                    $cff_post_item .= '"></p>';
	                                }
	                            }


	                            //VIDEOS ONLY
	                            if($cff_videos_only && empty($cff_album_id)){
	                                $cff_post_item = '';

	                                isset($news->description) ? $description_text = $news->description : $description_text = '';
	                                isset($news->title) ? $video_name = $news->title : $video_name = '';

	                                $cff_filter_string = $this->feed_options[ 'filter' ];
	                                $cff_show_post = true;

	                                if ( $cff_filter_string != '' ){
	                                    //Explode it into multiples
	                                    $cff_filter_strings_array = explode(',', $cff_filter_string);

	                                    $cff_text_to_be_filtered = $video_name . ' ' . $description_text;

	                                    if ( CFF_Utils::cff_stripos_arr_filter($cff_text_to_be_filtered, $cff_filter_strings_array) === false ) $cff_show_post = false;
	                                }

	                                $cff_exclude_string = $this->feed_options[ 'exfilter' ];
	                                if ( $cff_exclude_string != '' ){
	                                    //Explode it into multiples
	                                    $cff_exclude_strings_array = explode(',', $cff_exclude_string);

	                                    $cff_text_to_be_filtered = $video_name . ' ' . $description_text;

	                                    if ( CFF_Utils::cff_stripos_arr_filter($cff_text_to_be_filtered, $cff_exclude_strings_array) !== false ) $cff_show_post = false;
	                                }

	                                //Check to see if a duplicate video with the same source exists. If so, then exclude it as it's likely not meant to be included.
	                                if( isset($news->source) ){
	                                    if( $i > 1 ){ //No need to check the first video
	                                        //If the source matches that of the video before then exclude it
	                                        if( $news->source == $json_data->data[$i-2]->source ){
	                                            $cff_show_post = false;
	                                        }
	                                    }
	                                }

	                                //Encode text after filtering
	                                $description_text = htmlentities($description_text, ENT_QUOTES, 'UTF-8');
	                                $video_name = htmlentities($video_name, ENT_QUOTES, 'UTF-8');

	                                if ( isset( $news->published ) && $news->published === false ) $cff_show_post = false;

	                                if( $cff_show_post ){

	                                    foreach ($news->format as $value) {
	                                        //If there's a large image then use it
	                                        if( isset( $value->picture ) ){
	                                            $poster = $value->picture;
	                                        //Otherwise use the small one
	                                        } else if( isset( $news->picture ) ) {
	                                            $poster = $news->picture;
	                                        } else {
	                                            $poster = '';
	                                        }
	                                    }

	                                    $poster_alt = $video_name;
	                                    if( !empty($video_name) && !empty($description_text) ) $poster_alt .= ' - ';
	                                    $poster_alt .= $description_text;

	                                    //Fix Photon (Jetpack) issue
	                                    $cff_picture_querystring = '';
	                                    if (parse_url($poster, PHP_URL_QUERY)){
	                                        $picture_url_parts = parse_url($poster);
	                                        $cff_picture_querystring = $picture_url_parts['query'];
	                                    }

	                                    $cff_post_item .= '<div class="cff-album-item cff-video cff-col-' . $cff_video_cols . ' cff-new';
	                                    if( isset($page_id) ) $cff_post_item .= ' cff-' . $page_id;
	                                    $cff_post_item .= '" id="cff_' . $news->id . '" data-object-id="'.$object_id.'"';
	                                    $cff_post_item .= ' data-cff-timestamp="';
	                                    if( isset($news->created_time) ) $cff_post_item .= strtotime($news->created_time);
	                                    $cff_post_item .= '"';
	                                    $cff_video_player = $this->feed_options[ 'videoplayer' ];

	                                    //If there's no videos source then switch to the Facebook Video Player
	                                    if( !isset($news->source) ) $cff_video_player = 'facebook';

	                                    $cff_post_item .= ' data-cff-video-link="https://www.facebook.com/smashballoondev/videos/'.$news->id.'" data-cff-video-player="'.$cff_video_player.'"';
	                                    $cff_post_item .= '>';

	                                    isset($news->source) ? $cff_vid_source = $news->source : $cff_vid_source = '';
		                                $media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $news ) ) ) . '"';

	                                    $cff_post_item .= '<a href="https://facebook.com/' . $news->id . '" class="cff-album-cover cff-video" ' . $target . $cff_nofollow .$media_src_set_att. ' id="' . $news->id . '" data-source="' . $cff_vid_source . '">' . CFF_Display_Elements_Pro::get_icon( 'play', '', 'cff-playbtn' ) . '<img src="' . CFF_Display_Elements_Pro::get_media_placeholder( $poster ) . '" data-orig-source="' . $poster . '" alt="' . htmlspecialchars($poster_alt) . '" data-querystring="'.$cff_picture_querystring.'" /></a>';

	                                    if($cff_show_video_name) $cff_post_item .= '<div class="cff-album-info">';
	                                        if( $cff_show_video_name && !empty($video_name) ) $cff_post_item .= '<h4><a href="https://facebook.com/' . $news->id . '" '.$target.$cff_nofollow.'>' . $video_name . '</a></h4>';

	                                        if($cff_show_video_desc){
	                                            $cff_post_item .= '<p>' . substr($description_text, 0, 50);
	                                            if( strlen($description_text) > 50 ) $cff_post_item .= '...';
	                                            $cff_post_item .= '</p>';
	                                        }

	                                    if($cff_show_video_name) $cff_post_item .= '</div>';

	                                    $cff_post_item .= '</div>';
	                                    $post_time = $i;
	                                }

	                                //If the album item is filtered out then set it to be an empty album item so that it doesn't affect the offset pagination
	                                if( empty($cff_post_item) ){
	                                    $cff_post_item = '<p class="cff-empty-album';
	                                    if( isset($page_id) ) $cff_post_item .= ' cff-' . $page_id;
	                                    $cff_post_item .= '"></p>';
	                                }

	                            }


	                            //PHOTOS ONLY
	                            if($cff_photos_only && empty($cff_album_id)){

	                                //Get the caption
	                                !empty($news->name) ? $cff_caption = $news->name : $cff_caption = ' ';
	                                $id = $news->id;
	                                $picture = $news->picture;

	                                $cff_filter_string = $this->feed_options[ 'filter' ];
	                                $cff_show_post = true;

	                                if ( $cff_filter_string != '' ){
	                                    //Explode it into multiples
	                                    $cff_filter_strings_array = explode(',', $cff_filter_string);
	                                    //Hide the post if both the post text and description don't contain the string
	                                    $string_in_post_text = true;
	                                    $string_in_desc = true;
	                                    if ( CFF_Utils::cff_stripos_arr_filter($cff_caption, $cff_filter_strings_array) === false ) $cff_show_post = false;
	                                }

	                                $cff_exclude_string = $this->feed_options[ 'exfilter' ];
	                                if ( $cff_exclude_string != '' ){
	                                    //Explode it into multiples
	                                    $cff_exclude_strings_array = explode(',', $cff_exclude_string);
	                                    //Hide the post if both the post text and description don't contain the string
	                                    $string_in_post_text = false;
	                                    $string_in_desc = false;
	                                    if ( CFF_Utils::cff_stripos_arr_filter($cff_caption, $cff_exclude_strings_array) !== false ) $cff_show_post = false;
	                                }

	                                //Encode text after filtering
	                                $cff_caption = htmlentities($cff_caption, ENT_QUOTES, 'UTF-8');

	                                $cff_post_item = '';
	                                if( $cff_show_post ){

	                                    //Get full size image
	                                    if (!empty($picture)) $picture = 'https://graph.facebook.com/'.$id.'/picture?type=normal&width=9999&height=9999';
	                                    if (!empty($picture) && isset($news->images)) $full_picture = $news->images[0]->source;

	                                    //Loop through images array to find the right size
	                                    $image_to_use = $picture;
	                                    foreach ( $news->images as $image ) {
	                                        if( $image->width > 500 && $image->width < 900 ){
	                                            $image_to_use = $image->source;
	                                        } else {
	                                            $image_to_use = $full_picture;
	                                        }
	                                    }

	                                    //Fix Photon (Jetpack) issue
	                                    $cff_picture_querystring = '';
	                                    if (parse_url($image_to_use, PHP_URL_QUERY)){
	                                        $picture_url_parts = parse_url($image_to_use);
	                                        $cff_picture_querystring = $picture_url_parts['query'];
	                                    }


	                                    $cff_post_item .= '<div class="cff-album-item cff-col-'.$cff_photos_cols.' cff-new';
	                                    if( isset($page_id) ) $cff_post_item .= ' cff-' . $page_id;
	                                    $cff_post_item .= '" id="cff_'. $id .'" data-cff-full-size="'.$full_picture.'"';
	                                    $cff_post_item .= ' data-cff-timestamp="';
	                                    if( isset($news->created_time) ) $cff_post_item .= strtotime($news->created_time);
	                                    $cff_post_item .= '">';
		                                $media_src_set_att = ' data-img-src-set="' . esc_attr( CFF_Utils::cff_json_encode( CFF_Parse_Pro::get_media_src_set( $news ) ) ) . '"';

	                                    $cff_post_item .= '<a href="'.$news->link.'" class="cff-album-cover nofancybox" '.$target.$cff_nofollow.$media_src_set_att.'><img src="'. CFF_Display_Elements_Pro::get_media_placeholder( $image_to_use ).'" data-orig-source="'. $image_to_use .'" alt="'.htmlspecialchars($cff_caption).'" data-querystring="'.$cff_picture_querystring.'" /></a>';
	                                    $cff_post_item .= '</div>';
	                                }

	                                //If the album item is filtered out then set it to be an empty album item so that it doesn't affect the offset pagination
	                                if( empty($cff_post_item) ){
	                                    $cff_post_item = '<p class="cff-empty-album';
	                                    if( isset($page_id) ) $cff_post_item .= ' cff-' . $page_id;
	                                    $cff_post_item .= '"></p>';
	                                }

	                                //Have to use $i instead of the post_time otherwise if a photo is added at the same time then as another then it replaces it in the array. We shuffle the array below if it's multifeed.
	                                $cff_posts_array = CFF_Utils::cff_array_push_assoc($cff_posts_array, $i, $cff_post_item);


	                            } else {
	                                //PUSH POSTS TO ARRAY

	                                //If it's a page or a multifeed (always need to order posts by date in multifeeds) then use post date to order them
	                                if( $cff_ext_multifeed_active && count($page_ids) > 1 ){
	                                    $cff_posts_array = CFF_Utils::cff_array_push_assoc($cff_posts_array, $post_time, $cff_post_item);
	                                } else {
	                                //If it's a group then respect the order in the Facebook API (based on activity)
	                                    $cff_posts_array = CFF_Utils::cff_array_push_assoc($cff_posts_array, $i, $cff_post_item);
	                                }

	                            }

	                        } // End offset

	                    } // End post type check

	                    if (isset($news->message)) $prev_post_message = $news->message;
	                    if (isset($news->link))  $prev_post_link = $news->link;
	                    if (isset($news->description))  $prev_post_description = $news->description;
	                } // End the loop

	            } // End if($json_data) check
	            if( $cff_photos_only && empty($cff_album_id)){
	                //PHOTOS ONLY
	                //If it's a multifeed photo feed then shuffle the images together as can't order them by date as if two photos have the same date then one isn't shown
	                if($cff_ext_multifeed_active && count($page_ids) > 1) shuffle($cff_posts_array);

	            } else if( $cff_album_active && !empty($cff_album_id) || $cff_videos_only || $cff_albums_only ) {
	                //Don't sort array. Display posts in their native order, unless multifeed is enabled then we need to sort by date.
	                if($cff_ext_multifeed_active && count($page_ids) > 1) krsort($cff_posts_array);
	            } else {
	                //Sort the array in reverse order (newest first)
	                if( $cff_ext_multifeed_active && count($page_ids) > 1 ){
	                    krsort($cff_posts_array);
	                } else {
	                    ksort($cff_posts_array);
	                }
	            }

	        } // End ALL POSTS

	    } // END PAGE_IDS LOOP

	    if ($cff_events_only && $cff_events_source == 'eventspage'){
	        //EVENTS ONLY OFFSET - Use offset to remove items from the array which shouldn't be shown
	        if( !empty($this->feed_options['offset']) ) $cff_posts_array = array_slice($cff_posts_array, intval($this->feed_options['offset']));

	        //If no events then add notice
	        if ( empty($cff_posts_array) ) $cff_posts_array = CFF_Utils::cff_array_push_assoc($cff_posts_array, 1, '<p class="cff-no-events">'.stripslashes(__( $cff_no_events_text, 'custom-facebook-feed' ) ).'</p>');
	    }

	    $cff_load_more = $this->feed_options['loadmore'];
	    ($cff_load_more || $cff_load_more == 'true' || $cff_load_more == 'on') ? $cff_load_more = true : $cff_load_more = false;
	    if( $this->feed_options[ 'loadmore' ] === 'false' ) $cff_load_more = false;

	    //Output the posts array
	    if($cff_photos_only && empty($cff_album_id)){
	        //PHOTOS ONLY
	        $p = 0;
	        foreach ($cff_posts_array as $post ) {
	            if ( $p == $show_posts ) break;
	            $cff_content .= $post;
	            $p++;
	        }
	    //If it's an events feed and pagination is enabled then render all the events to the page so pagination can be done in JS
	    } else if( $cff_load_more && ( ($cff_events_only && $cff_events_source == 'eventspage') || ( $cff_reviews && $show_all_reviews ) ) ){
	        foreach ($cff_posts_array as $post ) {
	            $cff_content .= $post;
	        }
	    } else {
	        $p = 0;
	        foreach ($cff_posts_array as $post ) {

	            //For album items. If it's an empty album post then don't include it in the count and show another post instead.
	            if( strpos($post, 'cff-empty-album') !== false ) $p--;

	            if ( $p == $show_posts ) break;
	            $cff_content .= $post;
	            $p++;
	        }
	    }
		//Return our feed HTML to display
	    return $cff_content;

	}


	/**
	 *
	 * @since 3.18
	 */
	function cff_process_submitted_resize_ids() {
		if ( ! isset( $_POST['feed_id'] ) || strpos( $_POST['feed_id'], 'cff' ) === false ) {
			die( 'invalid feed ID');
		}

		$feed_id = sanitize_text_field( $_POST['feed_id'] );
		$images_need_resizing_raw = isset( $_POST['needs_resizing'] ) ? $_POST['needs_resizing'] : array();
		if ( is_array( $images_need_resizing_raw ) ) {
			array_map( 'sanitize_text_field', $images_need_resizing_raw );
		} else {
			$images_need_resizing_raw = array();
		}
		$images_need_resizing = $images_need_resizing_raw;

		$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
		if ( is_array( $atts_raw ) ) {
			array_map( 'sanitize_text_field', $atts_raw );
		} else {
			$atts_raw = array();
		}
		$atts = $atts_raw; // now sanitized

		! empty($_POST['pag_url']) ? $next_urls_arr_safe = json_decode( str_replace( '\"', '"', $_POST['pag_url'] ), true ) : $next_urls_arr_safe = '';

		if ( ! empty( $next_urls_arr_safe ) ) {
			$fo = $this->cff_get_processed_options( $atts );
			$facebook_settings = new CFF_Settings_Pro( $fo );

			$facebook_settings->set_feed_type_and_terms( $next_urls_arr_safe );

			$feed_id = $facebook_settings->get_transient_name();
		} else {
			$next_urls_arr_safe = null;
		}

		$data_att_html = $this->cff_get_shortcode_data_attribute_html( $atts );

		//If an access token is set in the shortcode then set "use own access token" to be enabled
		if( isset($atts['accesstoken']) ) $atts['ownaccesstoken'] = 'on';

		$feed_options = $this->cff_get_processed_options( $atts );
		$mobile_num = isset( $feed_options['nummobile'] ) && (int)$feed_options['nummobile'] > 0 ? (int)$feed_options['nummobile'] : 0;
		$desk_num = isset( $feed_options['num'] ) && (int)$feed_options['num'] > 0 ? (int)$feed_options['num'] : 0;
		if ( $desk_num < $mobile_num ) {
			$feed_options['minnum'] = $mobile_num;
		}
		$json_data_arr = CFF_Shortcode::cff_get_json_data( $feed_options, $next_urls_arr_safe, $data_att_html );

		$posts = array();

		foreach ( $json_data_arr as $cached_thing ) {
			$cached_thing_posts = isset( $cached_thing->data ) ? $cached_thing->data : array();
			$posts = array_merge( $posts, $cached_thing_posts );
		}
		$resizer = new CFF_Resizer( $images_need_resizing, $feed_id, $posts, $feed_options );

		if ( ! $resizer->image_resizing_disabled() ) {
			$resizer->do_resizing();
			$results = CFF_Resizer::get_resized_image_data_for_set( $images_need_resizing );

			$cache_feed_id = ! empty( $atts['feed'] ) ? (int)$atts['feed'] : false;
			if ( ! empty( $cache_feed_id ) ) {
				$feed_cache = new CFF_Cache( $cache_feed_id );
				$feed_cache->clear( 'resized_images' );
			}

			$return = array();
			if ( !empty( $results ) && is_array( $results ) ) {

				foreach ( $results as $result ) {

					$sizes = json_decode( $result['sizes'], true );
					if ( ! is_array( $sizes ) ) {
						$sizes = CFF_Resizer::image_sizes( $feed_options );
					}
					$return[ $result['facebook_id'] ] = array(
						'id' => $result['media_id'],
						'sizes' => $sizes
					);
				}

				$fb_return['resizing'] = $return;
				echo CFF_Utils::cff_json_encode( $fb_return );

				wp_die();
			}
		}

		$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
		$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
		$feed_details = array(
			'feed_id' => $feed_options['id'],
			'atts' => $atts,
			'location' => array(
				'post_id' => $post_id,
				'html' => $location
			)
		);

		CFF_Feed_Locator::do_background_tasks( $feed_details );

		die();
	}

	/**
	 * Get New Posts from Load More Button
	 *
	 * @since 3.18
	 */
	function cff_get_new_posts(){
	    //Get posted values from the ajax request
	    $shortcode_data = json_decode( str_replace( '\"', '"', $_POST['shortcode_data'] ), true ); // necessary to unescape quotes
	    isset($_POST['pag_url']) ? $next_urls_arr_safe = json_decode( str_replace( '\"', '"', $_POST['pag_url'] ), true ) : $next_urls_arr_safe = '';
	    // isset($_POST['last_album_batch']) ? $last_album_batch = $_POST['last_album_batch'] : $last_album_batch = 'false';

	    //Store the previous pag URL so that we can use it on the button for album items
	    $prev_pag_url = json_encode( $next_urls_arr_safe );
	    $prev_pag_url = str_replace( '"', '&quot;', $prev_pag_url);
	    //Run the functions to get more posts
	    $feed_options = $this->cff_get_processed_options( $shortcode_data );
	    $facebook_settings = new CFF_Settings_Pro( $feed_options );
		$facebook_settings->set_feed_type_and_terms();

		$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
		$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
		$feed_details = array(
			'feed_id' => $feed_options['id'],
			'atts' => $shortcode_data,
			'location' => array(
				'post_id' => $post_id,
				'html' => $location
			)
		);

		CFF_Feed_Locator::do_background_tasks( $feed_details );

		if ( ! empty( $shortcode_data['feed'] ) ) {
			$page = isset( $_POST['page'] ) ? (int)$_POST['page'] : 2;
			$shortcode_data['feedPage'] = $page;

		}

		$json_data_arr = CFF_Shortcode::cff_get_json_data( $feed_options, $next_urls_arr_safe, $shortcode_data );
		isset($json_data_arr) ? $next_urls_arr_safe = CFF_Shortcode::cff_get_next_url_parts( $json_data_arr ) : $next_urls_arr_safe = '';

	    $html = $this->cff_get_post_set_html( $feed_options, $json_data_arr );
	    echo $html;
	    // hidden input field added each time with the new "next url" information
	    echo '<input class="cff-pag-url" type="hidden" data-cff-pag-url="'.$next_urls_arr_safe.'" data-cff-prev-url="'.$prev_pag_url.'" data-transient-name="' . $facebook_settings->get_transient_name() . '" data-post-id="' . $post_id . '" data-feed-id="'.$feed_options['id'].'" value="">';

	    die();
	}

}