<?php


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
function cff_get_account_and_feed_info() {

    $return = array();
    $feed_options = array();
    $data_att_html = cff_get_shortcode_data_attribute_html( array() );

    //If an access token is set in the shortcode then set "use own access token" to be enabled
    if( isset($feed_options['accesstoken']) ) $feed_options['ownaccesstoken'] = 'on';

    $feed_options = cff_get_processed_options( $feed_options );
    $mobile_num = isset( $feed_options['nummobile'] ) && (int)$feed_options['nummobile'] > 0 ? (int)$feed_options['nummobile'] : 0;
    $desk_num = isset( $feed_options['num'] ) && (int)$feed_options['num'] > 0 ? (int)$feed_options['num'] : 0;
    if ( $desk_num < $mobile_num ) {
        $feed_options['minnum'] = $mobile_num;
    }

    $connected_accounts = cff_legacy_and_builder_connected_accounts();
    $check_array = (array)$connected_accounts;
    if ( empty( $check_array ) ) {
        $connected_accounts = false;
    }
    $access_token = get_option('cff_access_token');
    $page_id = get_option('cff_page_id');
    if ( $connected_accounts === false ) {
        if ( ! empty( $access_token ) && ! empty( $page_id ) ) {
            $connected_accounts = new stdClass();
            $connected_accounts->{ $page_id } = new stdClass();
            $connected_accounts->{ $page_id }->id = $page_id;
            $connected_accounts->{ $page_id }->accesstoken = $access_token;
            $connected_accounts->{ $page_id }->name = $page_id;
        }
    }

    if ( ! empty( $access_token ) && ! empty( $page_id ) && ! isset( $connected_accounts->{ $page_id } ) ) {
        $connected_accounts->{ $page_id } = new stdClass();
        $connected_accounts->{ $page_id }->id = $page_id;
        $connected_accounts->{ $page_id }->accesstoken = $access_token;
        $connected_accounts->{ $page_id }->name = $page_id;
    }

    $types_setting = explode( ',', $feed_options['type'] );
    $num_things = 0;
    $type = '';
    foreach ( $types_setting as $value ) {
        if ( $value !== '' ) {
            $num_things++;
            $type = $value;
        }
    }

    if ( $num_things !== 1 ) {
        $type = 'timeline';
    }

    $type_and_terms = array(
        'type' => $type,
        'term_label' => '',
        'terms' => explode( ',', $feed_options['id'] )
    );

    $return['type_and_terms'] = $type_and_terms;
    $return['connected_accounts'] = $connected_accounts;
    $return['available_types'] = array(
        'timeline' => array(
            'label' => 'Timeline Posts',
            'input' => 'connected',
            'shortcode' => 'timeline',
            'term_shortcode' => 'account',
        ),
        'events' => array(
            'label' => 'Events Page',
            'input' => 'connected',
            'shortcode' => 'events',
            'term_shortcode' => 'account',
        ),
        'videos' => array(
            'label' => 'Videos Page',
            'input' => 'connected',
            'shortcode' => 'videos',
            'term_shortcode' => 'account',
        ),
        'photos' => array(
            'label' => 'Photos Page',
            'input' => 'connected',
            'shortcode' => 'photos',
            'term_shortcode' => 'account',
        ),
        'albums' => array(
            'label' => 'Albums Page',
            'input' => 'connected',
            'shortcode' => 'albums',
            'term_shortcode' => 'account',
        )

    );
    $return['settings'] = array(
        'type' => 'type',
    );


    return $return;
}

/**
 * Return a combination of legacy and new sources with new sources
 * overriding legacy sources.
 *
 * @return StdClass
 *
 * @since 4.0
 */
function cff_legacy_and_builder_connected_accounts() {
	$connected_accounts = CustomFacebookFeed\CFF_Utils::cff_get_connected_accounts_object();

	if ( empty( $connected_accounts ) ) {
		$connected_accounts = new StdClass();
	}

	$new_sources = \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_source_list();

	$encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();

	foreach ( $new_sources as $new_source ) {
		if ( ! empty( $new_source['account_id'] ) ) {
			$account_id = $new_source['account_id'];
			$connected_accounts->{$account_id}              = new StdClass();
			$connected_accounts->{$account_id}->id          = $account_id;
			$connected_accounts->{$account_id}->accesstoken = $encryption->decrypt( $new_source['access_token'] ) ? $encryption->decrypt( $new_source['access_token'] ) : $new_source['access_token'];
			$connected_accounts->{$account_id}->pagetype    = $new_source['account_type'];
			$connected_accounts->{$account_id}->name        = $new_source['username'];
			$connected_accounts->{$account_id}->avatar      = $new_source['avatar_url'];
		}

	}

	return $connected_accounts;
}

// this is where the shortcode arguments would be processed along with the default
// options and options in the database to create the final options to be used in the feed
function cff_get_processed_options( $feed_options ) {
	if ( isset( $feed_options['feed'] ) ) {
		$feed_id = $feed_options['feed'];

		$feed_saver = new \CustomFacebookFeed\Builder\CFF_Feed_Saver( $feed_id );

		$feed_options   = $feed_saver->get_feed_settings();
		$feed_options['include'] = is_array( $feed_options['type'] ) ? implode( ',', $feed_options['include'] ) : $feed_options['include'];
		$feed_options['type'] = is_array( $feed_options['type'] ) ? implode( ',', $feed_options['type'] ) : $feed_options['type'];
		$feed_options['pagetype'] = ! empty( $feed_options['sources'][0] ) && $feed_options['sources'][0]['account_type'] === 'group' ? 'group' : 'page';

		return $feed_options;
	}

	if ( empty( $feed_options['filter'] ) && ! empty( $feed_options['includewords'] )) {
		$feed_options['filter'] = $feed_options['includewords'];
	}

	if ( empty( $feed_options['exfilter'] ) && ! empty( $feed_options['excludewords'] )) {
		$feed_options['exfilter'] = $feed_options['excludewords'];
	}



		//Which extensions are active?
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $cff_ext_options = get_option('cff_extensions_status');

    //Set extensions in extensions plugin all to false by default
    $cff_ext_multifeed_active_check = false;
    $cff_ext_date_range_active_check = false;
    $cff_ext_featured_post_active_check = false;
    $cff_ext_album_active_check = false;
    $cff_ext_masonry_columns_active_check = false;
    $cff_ext_carousel_active_check = false;
    $cff_extensions_reviews_active = false;

    if (WPW_SL_ITEM_NAME == 'Custom Facebook Feed WordPress Plugin Smash'){
        //Set page variables
        if( isset($cff_ext_options[ 'cff_extensions_multifeed_active' ]) ) $cff_ext_multifeed_active_check = $cff_ext_options[ 'cff_extensions_multifeed_active' ];
        if( isset($cff_ext_options[ 'cff_extensions_date_range_active' ]) ) $cff_ext_date_range_active_check = $cff_ext_options[ 'cff_extensions_date_range_active' ];
        if( isset($cff_ext_options[ 'cff_extensions_featured_post_active' ]) ) $cff_ext_featured_post_active_check = $cff_ext_options[ 'cff_extensions_featured_post_active' ];
        if( isset($cff_ext_options[ 'cff_extensions_album_active' ]) ) $cff_ext_album_active_check = $cff_ext_options[ 'cff_extensions_album_active' ];
        $cff_ext_masonry_columns_active_check = ''; //Deprecated
        if( isset($cff_ext_options[ 'cff_extensions_carousel_active' ]) ) $cff_ext_carousel_active_check = $cff_ext_options[ 'cff_extensions_carousel_active' ];
        if( isset($cff_ext_options[ 'cff_extensions_reviews_active' ]) ) $cff_extensions_reviews_active = $cff_ext_options[ 'cff_extensions_reviews_active' ];
    }

    ( is_plugin_active( 'cff-multifeed/cff-multifeed.php' ) || $cff_ext_multifeed_active_check ) ? $cff_ext_multifeed_active = true : $cff_ext_multifeed_active = false;
    ( is_plugin_active( 'cff-date-range/cff-date-range.php' ) || $cff_ext_date_range_active_check ) ? $cff_ext_date_active = true : $cff_ext_date_active = false;
    ( is_plugin_active( 'cff-featured-post/cff-featured-post.php' ) || $cff_ext_featured_post_active_check ) ? $cff_featured_post_active = true : $cff_featured_post_active = false;
    ( is_plugin_active( 'cff-album/cff-album.php' ) || $cff_ext_album_active_check ) ? $cff_album_active = true : $cff_album_active = false;
    $cff_masonry_columns_active = '';  //Deprecated
    ( is_plugin_active( 'cff-carousel/cff-carousel.php' ) || $cff_ext_carousel_active_check ) ? $cff_carousel_active = true : $cff_carousel_active = false;
    ( is_plugin_active( 'cff-reviews/cff-reviews.php' ) || $cff_extensions_reviews_active ) ? $cff_reviews_active = true : $cff_reviews_active = false;

    //Style options
    $options = get_option( 'cff_style_settings', array() );
    //Create the types string to set as shortcode default
    $type_string = '';
	if ( ! empty( $options[ 'cff_show_links_type' ] ) ) {
		if($options[ 'cff_show_links_type' ]) $type_string .= 'links,';
		if($options[ 'cff_show_event_type' ]) $type_string .= 'events,';
		if($options[ 'cff_show_video_type' ]) $type_string .= 'videos,';
		if($options[ 'cff_show_photos_type' ]) $type_string .= 'photos,';
		//If the album option hasn't been set yet in the $options array (ie. plugin has been updated but the option hasn't been saved) then set albums to display by default
		if( !isset($options[ 'cff_show_albums_type' ]) ) $options[ 'cff_show_albums_type' ] = true;
		if($options[ 'cff_show_albums_type' ]) $type_string .= 'albums,';
		if($options[ 'cff_show_status_type' ]) $type_string .= 'statuses,';
	}


    //Create the includes string to set as shortcode default
    $include_string = '';
	if ( ! empty( $options[ 'cff_show_author' ] ) ) {

		if ( $options['cff_show_author'] ) {
			$include_string .= 'author,';
		}
		if ( $options['cff_show_text'] ) {
			$include_string .= 'text,';
		}
		if ( $options['cff_show_desc'] ) {
			$include_string .= 'desc,';
		}
		if ( $options['cff_show_shared_links'] ) {
			$include_string .= 'sharedlinks,';
		}
		if ( $options['cff_show_date'] ) {
			$include_string .= 'date,';
		}
		if ( $options['cff_show_media'] ) {
			$include_string .= 'media,';
		}
		if ( $options['cff_show_event_title'] ) {
			$include_string .= 'eventtitle,';
		}
		if ( $options['cff_show_event_details'] ) {
			$include_string .= 'eventdetails,';
		}
		if ( $options['cff_show_meta'] ) {
			$include_string .= 'social,';
		}
		if ( $options['cff_show_link'] ) {
			$include_string .= 'link,';
		}
		if ( $options['cff_show_like_box'] ) {
			$include_string .= 'likebox,';
		}
	}

    //Reviews rated string
    $cff_reviews_string = '';
	if ( ! empty( $options[ 'cff_reviews_rated_5' ] ) ) {

		if ( isset( $options['cff_reviews_rated_5'] ) && isset( $options['cff_reviews_rated_4'] ) && isset( $options['cff_reviews_rated_3'] ) && isset( $options['cff_reviews_rated_2'] ) && isset( $options['cff_reviews_rated_1'] ) ) {
			if ( $options['cff_reviews_rated_5'] ) {
				$cff_reviews_string .= '5,';
			}
			if ( $options['cff_reviews_rated_4'] ) {
				$cff_reviews_string .= '4,';
			}
			if ( $options['cff_reviews_rated_3'] ) {
				$cff_reviews_string .= '3,';
			}
			if ( $options['cff_reviews_rated_2'] ) {
				$cff_reviews_string .= '2,';
			}
			if ( $options['cff_reviews_rated_1'] ) {
				$cff_reviews_string .= '1';
			}
		}
	}

    //Get masonry extension options if available
    $cff_masonry_options = get_option('cff_masonry_options');

    //Pass in shortcode attrbutes, include filter for extensions
    $feed_options = shortcode_atts(
    array(
        'accesstoken' => trim(get_option('cff_access_token')),
        'ownaccesstoken' => true,
        'pagetoken' => get_option('cff_page_access_token'),
        'id' => get_option('cff_page_id'),
        'pagetype' => get_option('cff_page_type'),
        'num' => get_option('cff_num_show'),
        'limit' => get_option('cff_post_limit'),
        'others' => '',
        'showpostsby' => get_option('cff_show_others'),
        'cachetype' => get_option('cff_caching_type'),
        'cachetime' => get_option('cff_cache_time'),
        'cacheunit' => get_option('cff_cache_time_unit'),
        'locale' => get_option('cff_locale'),
        'ajax' => get_option('cff_ajax'),
        'offset' => '',
        'account' => '',

        //General
        'cff_enqueue_with_shortcode' => isset($options[ 'cff_enqueue_with_shortcode' ]) ? $options[ 'cff_enqueue_with_shortcode' ] : false,
        'width' => isset($options[ 'cff_feed_width' ]) ? $options[ 'cff_feed_width' ] : '',
        'widthresp' => isset($options[ 'cff_feed_width_resp' ]) ? $options[ 'cff_feed_width_resp' ] : '',
        'height' => isset($options[ 'cff_feed_height' ]) ? $options[ 'cff_feed_height' ] : '',
        'padding' => isset($options[ 'cff_feed_padding' ]) ? $options[ 'cff_feed_padding' ] : '',
        'bgcolor' => isset($options[ 'cff_bg_color' ]) ? $options[ 'cff_bg_color' ] : '',
        'showauthor' => '',
        'showauthornew' => isset($options[ 'cff_show_author' ]) ? $options[ 'cff_show_author' ] : '',
        'class' => isset($options[ 'cff_class' ]) ? $options[ 'cff_class' ] : '',
        'type' => $type_string,
        'gdpr' => isset($options[ 'gdpr' ]) ? $options[ 'gdpr' ] : 'auto',
        //Events only
        'eventsource' => isset($options[ 'cff_events_source' ]) ? $options[ 'cff_events_source' ] : '',
        'eventoffset' => isset($options[ 'cff_event_offset' ]) ? $options[ 'cff_event_offset' ] : '',
        'eventimage' => isset($options[ 'cff_event_image_size' ]) ? $options[ 'cff_event_image_size' ] : '',
        'pastevents' => 'false',
        //Albums only
        'albumsource' => isset($options[ 'cff_albums_source' ]) ? $options[ 'cff_albums_source' ] : '',
        'showalbumtitle' => isset($options[ 'cff_show_album_title' ]) ? $options[ 'cff_show_album_title' ] : '',
        'showalbumnum' => isset($options[ 'cff_show_album_number' ]) ? $options[ 'cff_show_album_number' ] : '',
        'albumcols' => isset($options[ 'cff_album_cols' ]) ? $options[ 'cff_album_cols' ] : '',
        //Photos only
        'photosource' => isset($options[ 'cff_photos_source' ]) ? $options[ 'cff_photos_source' ] : '',
        'photocols' => isset($options[ 'cff_photos_cols' ]) ? $options[ 'cff_photos_cols' ] : '',
        //Videos only
        'videosource' => isset($options[ 'cff_videos_source' ]) ? $options[ 'cff_videos_source' ] : '',
        'showvideoname' => isset($options[ 'cff_show_video_name' ]) ? $options[ 'cff_show_video_name' ] : '',
        'showvideodesc' => isset($options[ 'cff_show_video_desc' ]) ? $options[ 'cff_show_video_desc' ] : '',
        'videocols' => isset($options[ 'cff_video_cols' ]) ? $options[ 'cff_video_cols' ] : '',
        'playlist' => '',

        //Lightbox
        'disablelightbox' => isset($options[ 'cff_disable_lightbox' ]) ? $options[ 'cff_disable_lightbox' ] : '',

        //Filters
        'filter' => isset($options[ 'cff_filter_string' ]) ? trim($options[ 'cff_filter_string' ]) : '',
        'exfilter' => isset($options[ 'cff_exclude_string' ]) ? $options[ 'cff_exclude_string' ] : '',

        //Post Layout
        'layout' => isset($options[ 'cff_preset_layout' ]) ? $options[ 'cff_preset_layout' ] : '',
        'enablenarrow' => isset($options[ 'cff_enable_narrow' ]) ? $options[ 'cff_enable_narrow' ] : '',
        'oneimage' => isset($options[ 'cff_one_image' ]) ? $options[ 'cff_one_image' ] : '',

        'mediaposition' => isset($options[ 'cff_media_position' ]) ? $options[ 'cff_media_position' ] : '',
        'include' => $include_string,
        'exclude' => '',

        //Masonry
        'masonry' => isset($cff_masonry_options[ 'cff_masonry_enabled' ]) ? $cff_masonry_options[ 'cff_masonry_enabled' ] : '',
        'masonrycols' => isset($cff_masonry_options[ 'cff_masonry_desktop_col' ]) ? $cff_masonry_options[ 'cff_masonry_desktop_col' ] : '',
        'masonrycolsmobile' => isset($cff_masonry_options[ 'cff_masonry_mobile_col' ]) ? $cff_masonry_options[ 'cff_masonry_mobile_col' ] : '',
        'masonryjs' => true,

        //New masonry options
        'cols' => isset($options[ 'cff_masonry_desktop_col' ]) ? $options[ 'cff_masonry_desktop_col' ] : '',
        'colsmobile' => isset($options[ 'cff_masonry_mobile_col' ]) ? $options[ 'cff_masonry_mobile_col' ] : '',
        'colsjs' => true,

        //Mobile settings
        'nummobile' => isset($options[ 'cff_num_mobile' ]) ? max( 0, (int)$options[ 'cff_num_mobile' ] ) : '',

        //Post Style
        'poststyle' => isset($options[ 'cff_post_style' ]) ? $options[ 'cff_post_style' ] : '',
        'postbgcolor' => isset($options[ 'cff_post_bg_color' ]) ? $options[ 'cff_post_bg_color' ] : '',
        'postcorners' => isset($options[ 'cff_post_rounded' ]) ? $options[ 'cff_post_rounded' ] : '',
        'boxshadow' => isset($options[ 'cff_box_shadow' ]) ? $options[ 'cff_box_shadow' ] : '',

        //Typography
        'textformat' => isset($options[ 'cff_title_format' ]) ? $options[ 'cff_title_format' ] : '',
        'textsize' => isset($options[ 'cff_title_size' ]) ? $options[ 'cff_title_size' ] : '',
        'textweight' => isset($options[ 'cff_title_weight' ]) ? $options[ 'cff_title_weight' ] : '',
        'textcolor' => isset($options[ 'cff_title_color' ]) ? $options[ 'cff_title_color' ] : '',
        'textlinkcolor' => isset($options[ 'cff_posttext_link_color' ]) ? $options[ 'cff_posttext_link_color' ] : '',
        'textlink' => isset($options[ 'cff_title_link' ]) ? $options[ 'cff_title_link' ] : '',
        'posttags' => isset($options[ 'cff_post_tags' ]) ? $options[ 'cff_post_tags' ] : '',
        'linkhashtags' => isset($options[ 'cff_link_hashtags' ]) ? $options[ 'cff_link_hashtags' ] : '',
        'lightboxcomments' => isset($options[ 'cff_lightbox_comments' ]) ? $options[ 'cff_lightbox_comments' ] : true,

        //Author
        'authorsize' => isset($options[ 'cff_author_size' ]) ? $options[ 'cff_author_size' ] : '',
        'authorcolor' => isset($options[ 'cff_author_color' ]) ? $options[ 'cff_author_color' ] : '',

        //Description
        'descsize' => isset($options[ 'cff_body_size' ]) ? $options[ 'cff_body_size' ] : '',
        'descweight' => isset($options[ 'cff_body_weight' ]) ? $options[ 'cff_body_weight' ] : '',
        'desccolor' => isset($options[ 'cff_body_color' ]) ? $options[ 'cff_body_color' ] : '',
        'linktitleformat' => isset($options[ 'cff_link_title_format' ]) ? $options[ 'cff_link_title_format' ] : '',
        'linktitlesize' => isset($options[ 'cff_link_title_size' ]) ? $options[ 'cff_link_title_size' ] : '',
        'linkdescsize' => isset($options[ 'cff_link_desc_size' ]) ? $options[ 'cff_link_desc_size' ] : '',
        'linkurlsize' => isset($options[ 'cff_link_url_size' ]) ? $options[ 'cff_link_url_size' ] : '',
        'linkdesccolor' => isset($options[ 'cff_link_desc_color' ]) ? $options[ 'cff_link_desc_color' ] : '',
        'linktitlecolor' => isset($options[ 'cff_link_title_color' ]) ? $options[ 'cff_link_title_color' ] : '',
        'linkurlcolor' => isset($options[ 'cff_link_url_color' ]) ? $options[ 'cff_link_url_color' ] : '',
        'linkbgcolor' => isset($options[ 'cff_link_bg_color' ]) ? $options[ 'cff_link_bg_color' ] : '',
        'linkbordercolor' => isset($options[ 'cff_link_border_color' ]) ? $options[ 'cff_link_border_color' ] : '',
        'disablelinkbox' => isset($options[ 'cff_disable_link_box' ]) ? $options[ 'cff_disable_link_box' ] : '',


        //Event title
        'eventtitleformat' => isset($options[ 'cff_event_title_format' ]) ? $options[ 'cff_event_title_format' ] : '',
        'eventtitlesize' => isset($options[ 'cff_event_title_size' ]) ? $options[ 'cff_event_title_size' ] : '',
        'eventtitleweight' => isset($options[ 'cff_event_title_weight' ]) ? $options[ 'cff_event_title_weight' ] : '',
        'eventtitlecolor' => isset($options[ 'cff_event_title_color' ]) ? $options[ 'cff_event_title_color' ] : '',
        'eventtitlelink' => isset($options[ 'cff_event_title_link' ]) ? $options[ 'cff_event_title_link' ] : '',
        //Event date
        'eventdatesize' => isset($options[ 'cff_event_date_size' ]) ? $options[ 'cff_event_date_size' ] : '',
        'eventdateweight' => isset($options[ 'cff_event_date_weight' ]) ? $options[ 'cff_event_date_weight' ] : '',
        'eventdatecolor' => isset($options[ 'cff_event_date_color' ]) ? $options[ 'cff_event_date_color' ] : '',
        'eventdatepos' => isset($options[ 'cff_event_date_position' ]) ? $options[ 'cff_event_date_position' ] : '',
        'eventdateformat' => isset($options[ 'cff_event_date_formatting' ]) ? $options[ 'cff_event_date_formatting' ] : '',
        'eventdatecustom' => isset($options[ 'cff_event_date_custom' ]) ? $options[ 'cff_event_date_custom' ] : '',
        'timezoneoffset' => 'false',

        //Event details
        'eventdetailssize' => isset($options[ 'cff_event_details_size' ]) ? $options[ 'cff_event_details_size' ] : '',
        'eventdetailsweight' => isset($options[ 'cff_event_details_weight' ]) ? $options[ 'cff_event_details_weight' ] : '',
        'eventdetailscolor' => isset($options[ 'cff_event_details_color' ]) ? $options[ 'cff_event_details_color' ] : '',
        'eventlinkcolor' => isset($options[ 'cff_event_link_color' ]) ? $options[ 'cff_event_link_color' ] : '',

        //Date
        'datepos' => isset($options[ 'cff_date_position' ]) ? $options[ 'cff_date_position' ] : '',
        'datesize' => isset($options[ 'cff_date_size' ]) ? $options[ 'cff_date_size' ] : '',
        'dateweight' => isset($options[ 'cff_date_weight' ]) ? $options[ 'cff_date_weight' ] : '',
        'datecolor' => isset($options[ 'cff_date_color' ]) ? $options[ 'cff_date_color' ] : '',
        'dateformat' => isset($options[ 'cff_date_formatting' ]) ? $options[ 'cff_date_formatting' ] : '',
        'datecustom' => isset($options[ 'cff_date_custom' ]) ? $options[ 'cff_date_custom' ] : '',
        'timezone' => isset($options[ 'cff_timezone' ]) ? $options[ 'cff_timezone' ] : 'America/Chicago',
        'beforedate' => isset($options[ 'cff_date_before' ]) ? $options[ 'cff_date_before' ] : '',
        'afterdate' => isset($options[ 'cff_date_after' ]) ? $options[ 'cff_date_after' ] : '',

        //Link to Facebook
        'linksize' => isset($options[ 'cff_link_size' ]) ? $options[ 'cff_link_size' ] : '',
        'linkweight' => isset($options[ 'cff_link_weight' ]) ? $options[ 'cff_link_weight' ] : '',
        'linkcolor' => isset($options[ 'cff_link_color' ]) ? $options[ 'cff_link_color' ] : '',
        'viewlinktext' => isset($options[ 'cff_view_link_text' ]) ? $options[ 'cff_view_link_text' ] : '',
        'linktotimeline' => isset($options[ 'cff_link_to_timeline' ]) ? $options[ 'cff_link_to_timeline' ] : '',

        //Load more button
        'buttoncolor' => isset($options[ 'cff_load_more_bg' ]) ? $options[ 'cff_load_more_bg' ] : '',
        'buttonhovercolor' => isset($options[ 'cff_load_more_bg_hover' ]) ? $options[ 'cff_load_more_bg_hover' ] : '',
        'buttontextcolor' => isset($options[ 'cff_load_more_text_color' ]) ? $options[ 'cff_load_more_text_color' ] : '',
        'buttontext' => isset($options[ 'cff_load_more_text' ]) ? $options[ 'cff_load_more_text' ] : '',
        'nomoretext' => isset($options[ 'cff_no_more_posts_text' ]) ? $options[ 'cff_no_more_posts_text' ] : '',

        //Social
        'iconstyle' => isset($options[ 'cff_icon_style' ]) ? $options[ 'cff_icon_style' ] : '',
        'socialtextcolor' => isset($options[ 'cff_meta_text_color' ]) ? $options[ 'cff_meta_text_color' ] : '',
        'socialbgcolor' => isset($options[ 'cff_meta_bg_color' ]) ? $options[ 'cff_meta_bg_color' ] : '',
        'sociallinkcolor' => isset($options[ 'cff_meta_link_color' ]) ? $options[ 'cff_meta_link_color' ] : '',
        'expandcomments' => isset($options[ 'cff_expand_comments' ]) ? $options[ 'cff_expand_comments' ] : '',
        'commentsnum' => isset($options[ 'cff_comments_num' ]) ? $options[ 'cff_comments_num' ] : '',
        'hidecommentimages' => isset($options[ 'cff_hide_comment_avatars' ]) ? $options[ 'cff_hide_comment_avatars' ] : '',
        'loadcommentsjs' => 'false',
        'salesposts' => 'false',
        'storytags' => 'false',

        //Misc
        'textlength' => get_option('cff_title_length'),
        'desclength' => get_option('cff_body_length'),
        'likeboxpos' => isset($options[ 'cff_like_box_position' ]) ? $options[ 'cff_like_box_position' ] : '',
        'likeboxoutside' => isset($options[ 'cff_like_box_outside' ]) ? $options[ 'cff_like_box_outside' ] : '',
        'likeboxcolor' => isset($options[ 'cff_likebox_bg_color' ]) ? $options[ 'cff_likebox_bg_color' ] : '',
        'likeboxtextcolor' => isset($options[ 'cff_like_box_text_color' ]) ? $options[ 'cff_like_box_text_color' ] : '',
        'likeboxwidth' => isset($options[ 'cff_likebox_width' ]) ? $options[ 'cff_likebox_width' ] : '',
        'likeboxfaces' => isset($options[ 'cff_like_box_faces' ]) ? $options[ 'cff_like_box_faces' ] : '',
        'likeboxborder' => isset($options[ 'cff_like_box_border' ]) ? $options[ 'cff_like_box_border' ] : '',
        'likeboxcover' => isset($options[ 'cff_like_box_cover' ]) ? $options[ 'cff_like_box_cover' ] : '',
        'likeboxsmallheader' => isset($options[ 'cff_like_box_small_header' ]) ? $options[ 'cff_like_box_small_header' ] : '',
        'likeboxhidebtn' => isset($options[ 'cff_like_box_hide_cta' ]) ? $options[ 'cff_like_box_hide_cta' ] : '',

        'credit' => isset($options[ 'cff_show_credit' ]) ? $options[ 'cff_show_credit' ] : '',
        'textissue' => isset($options[ 'cff_format_issue' ]) ? $options[ 'cff_format_issue' ] : '',
        'disablesvgs' => isset($options[ 'cff_disable_svgs' ]) ? $options[ 'cff_disable_svgs' ] : '',
        'restrictedpage' => isset($options[ 'cff_restricted_page' ]) ? $options[ 'cff_restricted_page' ] : '',
        'hidesupporterposts' => isset($options[ 'cff_hide_supporter_posts' ]) ? $options[ 'cff_hide_supporter_posts' ] : '',
        'privategroup' => 'false',
        'nofollow' => 'true',
        'timelinepag' => isset($options[ 'cff_timeline_pag' ]) ? $options[ 'cff_timeline_pag' ] : '',
        'gridpag' => isset($options[ 'cff_grid_pag' ]) ? $options[ 'cff_grid_pag' ] : '',
        'disableresize' => isset($options[ 'cff_disable_resize' ]) ? $options[ 'cff_disable_resize' ] : false,


        //Page Header
        'showheader' => isset($options[ 'cff_show_header' ]) ? $options[ 'cff_show_header' ] : '',
        'headertype' => isset($options[ 'cff_header_type' ]) ? $options[ 'cff_header_type' ] : '',
        'headercover' => isset($options[ 'cff_header_cover' ]) ? $options[ 'cff_header_cover' ] : '',
        'headeravatar' => isset($options[ 'cff_header_avatar' ]) ? $options[ 'cff_header_avatar' ] : '',
        'headername' => isset($options[ 'cff_header_name' ]) ? $options[ 'cff_header_name' ] : '',
        'headerbio' => isset($options[ 'cff_header_bio' ]) ? $options[ 'cff_header_bio' ] : '',
        'headercoverheight' => isset($options[ 'cff_header_cover_height' ]) ? $options[ 'cff_header_cover_height' ] : '',
        'headerlikes' => isset($options[ 'cff_header_likes' ]) ? $options[ 'cff_header_likes' ] : '',
        'headeroutside' => isset($options[ 'cff_header_outside' ]) ? $options[ 'cff_header_outside' ] : '',
        'headertext' => isset($options[ 'cff_header_text' ]) ? $options[ 'cff_header_text' ] : '',
        'headerbg' => isset($options[ 'cff_header_bg_color' ]) ? $options[ 'cff_header_bg_color' ] : '',
        'headerpadding' => isset($options[ 'cff_header_padding' ]) ? $options[ 'cff_header_padding' ] : '',
        'headertextsize' => isset($options[ 'cff_header_text_size' ]) ? $options[ 'cff_header_text_size' ] : '',
        'headertextweight' => isset($options[ 'cff_header_text_weight' ]) ? $options[ 'cff_header_text_weight' ] : '',
        'headertextcolor' => isset($options[ 'cff_header_text_color' ]) ? $options[ 'cff_header_text_color' ] : '',
        'headericon' => isset($options[ 'cff_header_icon' ]) ? $options[ 'cff_header_icon' ] : '',
        'headericoncolor' => isset($options[ 'cff_header_icon_color' ]) ? $options[ 'cff_header_icon_color' ] : '',
        'headericonsize' => isset($options[ 'cff_header_icon_size' ]) ? $options[ 'cff_header_icon_size' ] : '',
        'headerinc' => '',
        'headerexclude' => '',

        //Load More button
        'loadmore' => get_option('cff_load_more'),

        //Misc
        'fulllinkimages' => isset($options[ 'cff_full_link_images' ]) ? $options[ 'cff_full_link_images' ] : '',
        'linkimagesize' => isset($options[ 'cff_link_image_size' ]) ? $options[ 'cff_link_image_size' ] : '',
        'postimagesize' => isset($options[ 'cff_image_size' ]) ? $options[ 'cff_image_size' ] : '',
        'videoheight' => isset($options[ 'cff_video_height' ]) ? $options[ 'cff_video_height' ] : '',
        'videoaction' => isset($options[ 'cff_video_action' ]) ? $options[ 'cff_video_action' ] : '',
        'videoplayer' => isset($options[ 'cff_video_player' ]) ? $options[ 'cff_video_player' ] : '',
        'sepcolor' => isset($options[ 'cff_sep_color' ]) ? $options[ 'cff_sep_color' ] : '',
        'sepsize' => isset($options[ 'cff_sep_size' ]) ? $options[ 'cff_sep_size' ] : '',
		'apipostlimit' => '',

        //Translate
        'seemoretext' => isset( $options[ 'cff_see_more_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_see_more_text' ] ) ) : '',
        'seelesstext' => isset( $options[ 'cff_see_less_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_see_less_text' ] ) ) : '',
        'photostext' => isset( $options[ 'cff_translate_photos_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_photos_text' ] ) ) : '',
        'facebooklinktext' => isset( $options[ 'cff_facebook_link_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_facebook_link_text' ] ) ) : '',
        'sharelinktext' => isset( $options[ 'cff_facebook_share_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_facebook_share_text' ] ) ) : '',
        'showfacebooklink' => isset($options[ 'cff_show_facebook_link' ]) ? $options[ 'cff_show_facebook_link' ] : '',
        'showsharelink' => isset($options[ 'cff_show_facebook_share' ]) ? $options[ 'cff_show_facebook_share' ] : '',
        'buyticketstext' => isset($options[ 'cff_buy_tickets_text' ]) ? $options[ 'cff_buy_tickets_text' ] : '',

        'maptext' => isset( $options[ 'cff_map_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_map_text' ] ) ) : '',
        'interestedtext' => isset( $options[ 'cff_interested_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_interested_text' ] ) ) : '',
        'goingtext' => isset( $options[ 'cff_going_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_going_text' ] ) ) : '',

        'previouscommentstext' => isset( $options[ 'cff_translate_view_previous_comments_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_view_previous_comments_text' ] ) ) : '',
        'commentonfacebooktext' => isset( $options[ 'cff_translate_comment_on_facebook_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_comment_on_facebook_text' ] ) ) : '',
        'likesthistext' => isset( $options[ 'cff_translate_likes_this_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_likes_this_text' ] ) ) : '',
        'likethistext' => isset( $options[ 'cff_translate_like_this_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_like_this_text' ] ) ) : '',
        'reactedtothistext' => isset( $options[ 'cff_translate_reacted_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_reacted_text' ] ) ) : '',
        'andtext' => isset( $options[ 'cff_translate_and_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_and_text' ] ) ) : '',
        'othertext' => isset( $options[ 'cff_translate_other_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_other_text' ] ) ) : '',
        'otherstext' => isset( $options[ 'cff_translate_others_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_others_text' ] ) ) : '',
        'noeventstext' => isset( $options[ 'cff_no_events_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_no_events_text' ] ) ) : '',
        'replytext' => isset( $options[ 'cff_translate_reply_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_reply_text' ] ) ) : '',
        'repliestext' => isset( $options[ 'cff_translate_replies_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_replies_text' ] ) ) : '',

        'learnmoretext' => isset( $options[ 'cff_translate_learn_more_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_learn_more_text' ] ) ) : '',
        'shopnowtext' => isset( $options[ 'cff_translate_shop_now_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_shop_now_text' ] ) ) : '',
        'messagepage' => isset( $options[ 'cff_translate_message_page_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_message_page_text' ] ) ) : '',
        'getdirections' => isset( $options[ 'cff_translate_get_directions_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_get_directions_text' ] ) ) : '',

        'secondtext' => isset( $options[ 'cff_translate_second' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_second' ] ) ) : 'second',
        'secondstext' => isset( $options[ 'cff_translate_seconds' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_seconds' ] ) ) : 'seconds',
        'minutetext' => isset( $options[ 'cff_translate_minute' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_minute' ] ) ) : 'minute',
        'minutestext' => isset( $options[ 'cff_translate_minutes' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_minutes' ] ) ) : 'minutes',
        'hourtext' => isset( $options[ 'cff_translate_hour' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_hour' ] ) ) : 'hour',
        'hourstext' => isset( $options[ 'cff_translate_hours' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_hours' ] ) ) : 'hours',
        'daytext' => isset( $options[ 'cff_translate_day' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_day' ] ) ) : 'day',
        'daystext' => isset( $options[ 'cff_translate_days' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_days' ] ) ) : 'days',
        'weektext' => isset( $options[ 'cff_translate_week' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_week' ] ) ) : 'week',
        'weekstext' => isset( $options[ 'cff_translate_weeks' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_weeks' ] ) ) : 'weeks',
        'monthtext' => isset( $options[ 'cff_translate_month' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_month' ] ) ) : 'month',
        'monthstext' => isset( $options[ 'cff_translate_months' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_months' ] ) ) : 'months',
        'yeartext' => isset( $options[ 'cff_translate_year' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_year' ] ) ) : 'year',
        'yearstext' => isset( $options[ 'cff_translate_years' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_years' ] ) ) : 'years',
        'agotext' => isset( $options[ 'cff_translate_ago' ] ) ? stripslashes( esc_attr( $options[ 'cff_translate_ago' ] ) ) : 'ago',

        //Active extensions
        'multifeedactive' => $cff_ext_multifeed_active,
        'daterangeactive' => $cff_ext_date_active,
        'featuredpostactive' => $cff_featured_post_active,
        'albumactive' => $cff_album_active,
        'masonryactive' => false, //Deprecated
        'carouselactive' => $cff_carousel_active,
        'reviewsactive' => $cff_reviews_active,

        //Extension settings
        'from' => get_option( 'cff_date_from' ),
        'until' => get_option( 'cff_date_until' ),
        'featuredpost' => get_option( 'cff_featured_post_id' ),
        'album' => '',
        'lightbox' => get_option('cff_lightbox'),
        //Reviews
        'reviewsrated' => $cff_reviews_string,
        'starsize' => isset($options[ 'cff_star_size' ]) ? $options[ 'cff_star_size' ] : '',
        'hidenegative' => isset( $options[ 'cff_reviews_hide_negative' ] ) ? stripslashes( esc_attr( $options[ 'cff_reviews_hide_negative' ] ) ) : '',
        'reviewslinktext' => isset( $options[ 'cff_reviews_link_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_reviews_link_text' ] ) ) : '',
        'reviewshidenotext' => isset( $options[ 'cff_reviews_no_text' ] ) ? stripslashes( esc_attr( $options[ 'cff_reviews_no_text' ] ) ) : '',
        'reviewsmethod' => isset( $options[ 'cff_reviews_method' ] ) ? stripslashes( esc_attr( $options[ 'cff_reviews_method' ] ) ) : ''

    ), $feed_options, 'custom_facebook_feed' );


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
	$cff_account = '';
	if ( ! empty( $feed_options['account'] ) ) {
		$cff_account = trim($feed_options['account']);
	} elseif ( ! empty( $feed_options['id'] ) ) {
		$cff_account = trim($feed_options['id']);
	}

	$cff_connected_accounts = cff_legacy_and_builder_connected_accounts();

    if( !empty( $cff_account ) ){

        if( !empty($cff_connected_accounts) ){

        	if ( is_string($cff_connected_accounts) ) {
		        //Replace both single and double quotes before decoding
		        $cff_connected_accounts = str_replace('\"','"', $cff_connected_accounts);
		        $cff_connected_accounts = str_replace("\'","'", $cff_connected_accounts);

		        $cff_connected_accounts = json_decode( $cff_connected_accounts );
	        }


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
    if ( $cff_ext_multifeed_active && strpos($access_token, ':') !== false ) {

        //Define the array
        $access_token_multiple = array();

        //If there are multiple tokens then split them up
        if( strpos($access_token, ',') !== false ){
            $access_token_pieces = explode(",", $access_token);
            foreach ( $access_token_pieces as $at_piece ) {
                $access_token_multiple = CustomFacebookFeed\CFF_Utils::cffSplitToken($at_piece, $access_token_multiple);
            }
        } else {
        //Otherwise just create a 1 item array
            $access_token_multiple = CustomFacebookFeed\CFF_Utils::cffSplitToken($access_token);
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

    return $feed_options;
}
