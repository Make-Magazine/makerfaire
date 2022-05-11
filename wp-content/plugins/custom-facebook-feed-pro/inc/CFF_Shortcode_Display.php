<?php
/**
 * Shortcode Display Class
 *
 * Contains all the functions for the diplay purposes! (Generates CSS, CSS Classes, HTML Attributes...)
 *
 * @since 3.18
 */

namespace CustomFacebookFeed;
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Display_Elements_Pro;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class CFF_Shortcode_Display {

	#------------------------------
	/**
	 * Display.
	 * The main Shortcode display
	 *
	 * @since 3.18
	 */
	public function display_cff( $feed_options ) {
		$original_atts 					= (array)$feed_options;
    	$data_att_html 					= $this->cff_get_shortcode_data_attribute_html( $feed_options );
    	if( isset($feed_options['accesstoken']) ) $feed_options['ownaccesstoken'] = 'on';
		$this->options 					= get_option('cff_style_settings');
		$this->feed_options 			= $this->cff_get_processed_options( $feed_options );

		if ( ! empty( $this->feed_options['colorpalette'] )
             && $this->feed_options['colorpalette'] !== 'inherit' ) {
			$this->feed_options['linkcolor'] = '';
		}

		$feed_options 					= $this->feed_options;
		$atts 							= $this->feed_options;
		$options 						= $this->options;
		$access_token 					= $this->feed_options['accesstoken'];

		$feed_id = isset( $original_atts['feed'] ) ? (int)$original_atts['feed'] : $this->feed_options['id'];

		// for non-legacy feeds. We don't want to show a feed if the ID
        // used does not actually exist
		if ( ! empty( $feed_options['feederror'] ) ) {

		    if ( $feed_options['feederror'] === 'default' ) {
			    $feed_list = \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_feed_list();

			    if ( count( $feed_list ) !== 1 ) {
				    return '';
			    }
            }
			return "<span id='cff-no-id'>" . sprintf( __( "No feed found with the ID %s. Go to the %sAll Feeds page%s and select an ID from an existing feed.", 'custom-facebook-feed' ), esc_html( $feed_options['feederror'] ), '<a href="' . esc_url( admin_url( 'admin.php?page=cff-feed-builder' ) ) . '">', '</a>' ) . "</span><br /><br />";
		}

        if ( $feed_options['cff_enqueue_with_shortcode'] === 'on' || $feed_options['cff_enqueue_with_shortcode'] === 'true' ) {
            wp_enqueue_style( 'cff' );
            wp_enqueue_script( 'cffscripts' );
        }

		$mobile_num = isset( $this->feed_options['nummobile'] ) && (int)$this->feed_options['nummobile'] > 0 ? (int)$this->feed_options['nummobile'] : 0;
		$desk_num = isset( $this->feed_options['num'] ) && (int)$this->feed_options['num'] > 0 ? (int)$this->feed_options['num'] : 0;
		if ( $desk_num < $mobile_num ) {
			$this->feed_options['minnum'] = $mobile_num;
		}

	    $json_data_arr = CFF_Shortcode::cff_get_json_data( $this->feed_options, null, $data_att_html );
	    isset($json_data_arr) ? $next_urls_arr_safe = CFF_Shortcode::cff_get_next_url_parts( $json_data_arr ) : $next_urls_arr_safe = '';
	    $html = $this->cff_get_post_set_html( $this->feed_options, $json_data_arr, $original_atts );
	    //Create the prev URLs array to add to the button
	    $prev_info 				= $this->cff_get_prev_url_parts( $json_data_arr );
	    $prev_urls_arr_safe 	= $prev_info['prev_urls_arr_safe'];
	    $json_data 				= $prev_info['json_data'];
	    $page_id 				= $this->feed_options['id'];


	    //***FEED CONTAINER HTML (header, likebox, load more, etc)***//
	    //Width
	    $cff_feed_width = CFF_Utils::get_css_distance( $this->feed_options[ 'width' ] ) ;
	    //Set to be 100% width on mobile?
	    $cff_feed_width_resp = $this->feed_options[ 'widthresp' ];
	    ( $cff_feed_width_resp == 'on' || $cff_feed_width_resp == 'true' || $cff_feed_width_resp == true ) ? $cff_feed_width_resp = true : $cff_feed_width_resp = false;
	    if( $this->feed_options[ 'widthresp' ] == 'false' ) $cff_feed_width_resp = false;

	    //Height
	    $cff_feed_height = CFF_Utils::get_css_distance( $this->feed_options[ 'height' ] ) ;
	    //Padding
	    $cff_feed_padding = CFF_Utils::get_css_distance( $this->feed_options[ 'padding' ] );
	    //Bg color
	    $cff_bg_color = $this->feed_options[ 'bgcolor' ];

	    //Page or Group
	    $cff_page_type 	= $this->feed_options[ 'pagetype' ];
	    $cff_is_group 	= ($cff_page_type == 'group') ? true : false;


	    //Include string
	    $cff_includes = $this->feed_options[ 'include' ];
	    $cff_show_media = ( CFF_Utils::stripos($cff_includes, 'media') !== false ) ? true : false;

	    //Lightbox
	    $cff_disable_lightbox = !CFF_Utils::check_if_on( $this->feed_options['disablelightbox'] );
	    ( $cff_disable_lightbox == 'on' || $cff_disable_lightbox == 'true' || $cff_disable_lightbox == true ) ? $cff_disable_lightbox = true : $cff_disable_lightbox = false;
	    if( $this->feed_options[ 'disablelightbox' ] == 'false' ) $cff_disable_lightbox = false;



	    $cff_multifeed_active 			= $this->feed_options[ 'multifeedactive' ];
	    $cff_featured_post_active 		= $this->feed_options[ 'featuredpostactive' ];
	    $cff_album_active 				= $this->feed_options[ 'albumactive' ];
	    $cff_masonry_columns_active 	= false; //Deprecated
	    $cff_carousel_active 			= $this->feed_options[ 'carouselactive' ];
	    $cff_reviews_active 			= $this->feed_options[ 'reviewsactive' ];

	    $cff_album_id = $this->feed_options['album'];
	    ( $cff_album_active && !empty($cff_album_id) ) ? $cff_album_embed = true : $cff_album_embed = false;

	    ( $this->feed_options['reviewsmethod'] == 'all' ) ? $show_all_reviews = true : $show_all_reviews = false;

	    //Post types
	    $cff_types = $this->feed_options['type'];
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
	    $cff_events_source = $this->feed_options[ 'eventsource' ];
	    if ( empty($cff_events_source) || !isset($cff_events_source) ) $cff_events_source = 'eventspage';
	    $cff_event_offset = $this->feed_options[ 'eventoffset' ];
	    if ( empty($cff_event_offset) || !isset($cff_event_offset) ) $cff_event_offset = '6';
	    ($cff_show_event_type && !$cff_show_links_type && !$cff_show_video_type && !$cff_show_photos_type && !$cff_show_status_type && !$cff_show_albums_type) ? $cff_events_only = true : $cff_events_only = false;

	    //Albums only
	    $cff_albums_source = $this->feed_options[ 'albumsource' ];
	    ( ($cff_show_albums_type && $cff_albums_source == 'photospage') && !$cff_show_links_type && !$cff_show_video_type && !$cff_show_photos_type && !$cff_show_status_type && !$cff_show_event_type) ? $cff_albums_only = true : $cff_albums_only = false;

	    //Photos only
	    $cff_photos_source = $this->feed_options[ 'photosource' ];
	    ( ($cff_show_photos_type && $cff_photos_source == 'photospage') && !$cff_show_links_type && !$cff_show_video_type && !$cff_show_event_type && !$cff_show_status_type && !$cff_show_albums_type) ? $cff_photos_only = true : $cff_photos_only = false;

	    //Videos only
	    $cff_videos_source = $this->feed_options[ 'videosource' ];
	    ( ($cff_show_video_type && $cff_videos_source == 'videospage') && !$cff_show_albums_type && !$cff_show_links_type && !$cff_show_photos_type && !$cff_show_status_type && !$cff_show_event_type) ? $cff_videos_only = true : $cff_videos_only = false;

	    //If it's a featured post then it isn't a dedicated feed type
	    if( $cff_featured_post_active && !empty($this->feed_options['featuredpost']) ){
	        $cff_albums_only = false;
	        $cff_photos_only = false;
	        $cff_videos_only = false;
	    }

	    //Post layout
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

	    //Masonry
	    $masonry = $this->feed_options['masonry'];
	    //Or if new options set to more than 1 column then enable masonry
	    if( intval($this->feed_options['cols']) > 1 ) $masonry = true;

	    //Disable masonry for grid feeds
	    if( $cff_albums_only || $cff_photos_only || $cff_videos_only || $cff_album_embed ) $masonry = false;

	    $js_only = isset( $this->feed_options['colsjs'] ) ? $this->feed_options['colsjs'] : false;
	    if( $js_only === 'false' ) $js_only = false;

	    // Masonry and Carousel feeds are incompatible so we check to see if carousel is active
	    // and set Masonry to false if it is
	    if( $cff_carousel_active && ( $this->feed_options['carousel'] === 'on' || $this->feed_options['carousel'] === "true" || $this->feed_options['carousel'] === true ) ) {
	        $masonry = false;
	    }
	    if( $masonry || $masonry == 'true' ) {
	        $this->feed_options['headeroutside'] = true;

	        // Carousel feeds are incompatible with the columns setting for the main plugin
	        $this->feed_options['columnscompatible'] = false;
	    }

	    $masonry_opaque_comments = false;
	    $masonry_classes = '';
		$cols = (int)$this->feed_options['cols'];
		$colstablet = (int)$this->feed_options['colstablet'];
		$colsmobile = (int)$this->feed_options['colsmobile'];

		if ( $this->feed_options['feedlayout'] === 'masonry' ) {
			if ( $cols > 1
			     || $colstablet > 1
			     || $colsmobile > 1) {
				$masonry = true;
			}
        }


	    if( isset($masonry) ) {
	        if( $masonry === 'on' || $masonry === true || $masonry === 'true' ) {
	            if( ( empty($cols) || !isset($cols) ) && isset($this->feed_options['masonrycols']) ) $cols = $this->feed_options['masonrycols'];
	            if( ( empty($colsmobile) || !isset($colsmobile) ) && isset($this->feed_options['masonrycolsmobile']) ) $colsmobile = $this->feed_options['masonrycolsmobile'];

	            $masonry_classes .= 'cff-masonry cff-disable-liquid';

	            if( $this->feed_options['cols'] != 3 ) {
	                $masonry_classes .= sprintf( ' masonry-%s-desktop', $cols );
	            }
	            if( $colsmobile > 1 ) {
		            $masonry_classes .= sprintf( ' masonry-%s-mobile', $colsmobile );
	            }
		        if( $colstablet > 1 ) {
			        $masonry_classes .= sprintf( ' masonry-%s-tablet', $colstablet );
		        }
		        if( ! $js_only ) {
	                $masonry_classes .= ' cff-masonry-css';
	            } else {
	                $masonry_classes .= ' cff-masonry-js';
	            }

	            //Is there a bg color set on either the post or the comments box?
	            if( ( $this->feed_options['poststyle'] == 'boxed' && strlen($this->feed_options['postbgcolor']) > 2 ) || strlen($this->feed_options['socialbgcolor']) > 2 ){
	                $masonry_opaque_comments = true;
	                $masonry_classes .= ' cff-opaque-comments';
	            }
	        }
	    }

	    //Set like box variable
	    //If there are more than one page id then use the first one
	    $like_box_page_id = explode(",", str_replace(' ', '', $this->feed_options['id']) );
	    $cff_like_box_position = $this->feed_options[ 'likeboxpos' ];
	    $cff_like_box_outside = $this->feed_options[ 'likeboxoutside' ];
	    $cff_likebox_bg_color = $this->feed_options[ 'likeboxcolor' ];
	    $cff_like_box_text_color = $this->feed_options[ 'likeboxtextcolor' ];
	    $cff_like_box_colorscheme = 'light';
	    if ($cff_like_box_text_color == 'white') $cff_like_box_colorscheme = 'dark';


	    $cff_locale = $this->feed_options[ 'locale' ];
	    if ( empty($cff_locale) || !isset($cff_locale) || $cff_locale == '' ) $cff_locale = 'en_US';

	    $cff_facebook_link_text = $this->feed_options[ 'facebooklinktext' ];


	    if($cff_is_group){
	    	if(isset($json_data_arr[$page_id]->load_from_cache) && $json_data_arr[$page_id]->load_from_cache != null){
	    		$next_urls_arr_safe = $json_data_arr[$page_id]->latest_record_date;
	    	}
	    }

	    //Text limits
	    $title_limit = $this->feed_options['textlength'];
	    if (!isset($title_limit)) $title_limit = 9999;

	    //LOAD MORE BUTTON
		$cff_load_more 		= CFF_Utils::check_if_on( $this->feed_options[ 'loadmore' ] );

	    //HEADER
		if ( CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			$cff_header_type = 'text';
		}
	    $cff_show_header 		= CFF_Utils::check_if_on( $this->feed_options['showheader'] );
	    $cff_header_type 		= strtolower( $this->feed_options['headertype'] );
	    $cff_header_outside 	= CFF_Utils::check_if_on( $this->feed_options['headeroutside'] );
		$cff_header_styles 		= $this->get_style_attribute( 'header' );
	    $cff_header = '';
	    if( ($cff_album_active && !empty($cff_album_id) ) && $cff_show_header && function_exists('cff_get_album_details') && $cff_header_type != "text" ){
	        $cff_header = cff_get_album_details($this->feed_options, $cff_header_styles, $cff_header_outside);
	    }
	    else{
	   		$cff_cache_time = $this->feed_options['cachetime'];
	    	$cff_header = CFF_Utils::print_template_part( 'header', get_defined_vars(), $this);
	    }

	    //Narrow styles
	    $cff_enable_narrow = $this->feed_options['enablenarrow'];
	    ($cff_enable_narrow == 'true' || $cff_enable_narrow == 'on') ? $cff_enable_narrow = true : $cff_enable_narrow = false;

	    $cff_class = $this->feed_options['class'];

	    //Is it a restricted page?
	    $cff_restricted_page = $this->feed_options['restrictedpage'];
	    ($cff_restricted_page == 'true' || $cff_restricted_page == 'on') ? $cff_restricted_page = true : $cff_restricted_page = false;

	    //Should we hide supporter posts?
	    $cff_hide_supporter_posts = $this->feed_options['hidesupporterposts'];
	    ($cff_hide_supporter_posts == 'true' || $cff_hide_supporter_posts == 'on') ? $cff_hide_supporter_posts = true : $cff_hide_supporter_posts = false;

	    //Compile feed styles
	    $cff_feed_styles = '';
	    if ( !empty($cff_feed_width)) $cff_feed_styles .= 'style="';
	  	  if ( !empty($cff_feed_width) ) $cff_feed_styles .= 'width:' . $cff_feed_width . '; ';
	    if ( !empty($cff_feed_width)) $cff_feed_styles .= '"';


	    $cff_insider_style = '';
	    if (!empty($cff_feed_padding)  || (!empty($cff_bg_color) && $cff_bg_color != '#')  || !empty($cff_feed_height)) $cff_insider_style .= 'style="';
	     	if ( !empty($cff_feed_padding) ) $cff_insider_style .= 'padding:' . $cff_feed_padding . '; ';
	      	if ( !empty($cff_bg_color) && $cff_bg_color != '#' ) $cff_insider_style .= 'background-color:#' . str_replace('#', '', $cff_bg_color) . '; ';
	      	if ( !empty($cff_feed_height) ) $cff_insider_style .= 'height:' . $cff_feed_height . '; ';
	    if ( !empty($cff_feed_padding)  || (!empty($cff_bg_color) && $cff_bg_color != '#')  || !empty($cff_feed_height) ) $cff_insider_style .= '"';

	    $cff_nofollow = CFF_Utils::check_if_on( $this->feed_options['nofollow'] );

	    ( $cff_nofollow ) ? $cff_nofollow = ' rel="nofollow noopener"' : $cff_nofollow = '';

	    //The main wrapper, only outputted once
	    $cff_content = '';
	    //Create CFF container HTML
	    $cff_content .= '<div class="cff-wrapper">';

	    //Add the page header to the outside of the top of feed
	    if ($cff_show_header && $cff_header_outside) $cff_content .= $cff_header;

	    //Like Box
		$cff_show_like_box = $this->feed_options['showlikebox'];
	    $like_box = CFF_Utils::print_template_part( 'likebox', get_defined_vars());

	    //Add like box to the outside of the top of feed
	    if ($cff_like_box_position == 'top' && $cff_show_like_box && $cff_like_box_outside) $cff_content .= $like_box;

	    $custom_wrp_class = !empty($cff_feed_height) ? ' cff-wrapper-fixed-height' : '';

	    $cff_content .= '<div class="cff-wrapper-ctn '.$custom_wrp_class.'" '.$cff_insider_style.'>';
	    $cff_content .= '<div id="cff" ';
	    if( !empty($title_limit) ) $cff_content .= 'data-char="'.$title_limit.'" ';
	    $cff_content .= 'class="cff ';
	    if( !empty($cff_class) ) $cff_content .= $cff_class . ' ';

	    $mobile_cols_class = '';
	    if (! empty( $this->feed_options['colsmobile'] ) && (int)$this->feed_options['colsmobile'] > 0) {
		    $mobile_cols_class = ' cff-mob-cols-' . (int)$this->feed_options['colsmobile'];
	    }

		$tablet_cols_class = '';
		if (! empty( $this->feed_options['colstablet'] )
            && (int)$this->feed_options['colstablet'] > 0
            && (int)$this->feed_options['colstablet'] !== 2) {
			$tablet_cols_class = ' cff-tab-cols-' . (int)$this->feed_options['colstablet'];
		}

	    // Hook for adding classes to the #cff element
	    $classes = '';
	    $classes .= apply_filters( 'cff_feed_class', $classes, $this->feed_options ).' ';
	    $cff_content .= $masonry_classes . $mobile_cols_class . $tablet_cols_class . ' ';
	    $cff_content .= $classes;

		$palette = '';
		$custom_palette_class = '';
		$doing_custom_styles = false;

		if ( ! empty( $this->feed_options['colorpalette'] ) ) {
			switch ( $this->feed_options['colorpalette'] ) {
                case 'dark' :
                    $palette = 'cff-dark ';
	                $this->feed_options['iconstyle'] = 'light';
	                break;
		        case 'light' :
			        $palette = 'cff-light ';
			        $this->feed_options['iconstyle'] = 'dark';
			        break;
		        case 'custom' :
			        $doing_custom_styles = true;
			        $custom_palette_class = 'cff-palette-' . $feed_id . ' ';
			        $this->feed_options['buttoncolor'] = '';
			        $this->feed_options['buttontextcolor'] = '';
			        break;
                default:
                    $palette = '';
            }
        }
        if ( ! empty( $this->feed_options['customlightboxcolors'] ) ) {
			$doing_custom_styles = true;
		}

		$cff_content .= $palette . $custom_palette_class;
	    if ( !empty($cff_feed_height) ) $cff_content .= 'cff-fixed-height ';
	    if ( $cff_thumb_layout ) $cff_content .= 'cff-thumb-layout ';
	    if ( $cff_half_layout ) $cff_content .= 'cff-half-layout ';
	    if ( !$cff_enable_narrow ) $cff_content .= 'cff-disable-narrow ';
	    if ( $cff_feed_width_resp ) $cff_content .= 'cff-width-resp ';
	    if ( !$cff_albums_only && !$cff_photos_only && !$cff_videos_only && !$cff_events_only && !$cff_album_embed ) $cff_content .= 'cff-timeline-feed ';
	    if ( $cff_albums_only || $cff_photos_only || $cff_videos_only || $cff_album_embed ) $cff_content .= 'cff-album-items-feed ';
	    if ( $cff_load_more ) $cff_content .= 'cff-pag ';
	    if ( $cff_is_group ) $cff_content .= 'cff-group ';
	    if ( CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			$cff_content .= 'cff-doing-gdpr ';
		}
	    if( $this->feed_options['privategroup'] == 'true' ) $cff_content .= 'cff-private-group ';
	    if ( $show_all_reviews ) $cff_content .= 'cff-all-reviews ';

	    $cff_no_svgs = $this->feed_options['disablesvgs'];
	    if ( $cff_no_svgs ) $cff_content .= 'cff-no-svgs ';
	    $cff_content .= 'cff-nojs ';
	    //Lightbox extension
	    if ( $cff_disable_lightbox && ($this->feed_options['lightbox'] == 'true' || $this->feed_options['lightbox'] == 'on') ) $cff_content .= ' cff-lightbox';
	    if ( $cff_disable_lightbox ) {
	        $cff_content .= ' cff-lb';
	    }
	    $cff_content .= '" ' . $cff_feed_styles;
	    $cff_content .= ' data-fb-text="'.stripslashes(__( $cff_facebook_link_text, 'custom-facebook-feed' ) ).'"';
	    $cff_content .= ' data-offset="'.$this->feed_options['offset'].'"';

	    //Timeline pagination method
	    $cff_timeline_pag = $this->feed_options['timelinepag'];
	    if( $cff_timeline_pag == 'paging' ) $cff_content .= ' data-timeline-pag="true"';

	    //Using own token - pass to connect.php
	    if( $this->feed_options['ownaccesstoken'] ) $cff_content .= ' data-own-token="true"';

	    //Grid pagination method
	    $cff_grid_pag = $this->feed_options['gridpag'];

	    //If it's set to auto then decide the method in the PHP using the vars above
	    if( $cff_grid_pag == 'auto' ){
	        //Set to cursor initially
	        $cff_grid_pag = 'cursor';
	        //If there's a filter being used, it's a multifeed, or the limit is set to be higher than the num, then use the offset method instead
	        if( !empty($this->feed_options[ 'filter' ]) || !empty($this->feed_options[ 'exfilter' ]) || ( $cff_multifeed_active && strpos($this->feed_options['id'], ',') !== false ) || ( intval($this->feed_options[ 'limit' ]) > intval($this->feed_options[ 'num' ]) ) ) $cff_grid_pag = 'offset';
	    }
	    $cff_content .= ' data-grid-pag="'.$cff_grid_pag.'"';
	    if( $cff_restricted_page ) $cff_content .= ' data-restricted="true"';

	    //Lightbox comments
	    $cff_lightbox_comments = true;
	    if( $this->feed_options[ 'lightboxcomments' ] === 'false' || $this->feed_options['lightboxcomments'] == false ) $cff_lightbox_comments = false;

	    //Disable lightbox comments if it's a dedicated feed type
	    if( ( $cff_events_only && $cff_events_source == 'eventspage' ) || $cff_albums_only || $cff_photos_only || $cff_videos_only) $cff_lightbox_comments = false;

	    //Add data attr for lightbox comments
	    $cff_content .= ( $cff_lightbox_comments && !$cff_album_embed ) ? ' data-lb-comments="true"' : ' data-lb-comments="false"';

	    //If the number of posts isn't set then set the pagination number to be 25
	    $pag_num = $this->feed_options['num'];
	    if( (!isset($pag_num) || empty($pag_num) || $pag_num == '') && $pag_num != '0' ) $pag_num = 25;

	    $cff_content .= ' data-pag-num="'.$pag_num.'"';

		$mobile_num = !$cff_carousel_active && isset( $this->feed_options['nummobile'] ) && (int)$this->feed_options['nummobile'] !== (int)$pag_num ? (int)$this->feed_options['nummobile'] : false;
		if ( $mobile_num ) {
			$cff_content .= ' data-nummobile="'.$mobile_num.'"';
		}

	    //Add the absolute path to the container to be used in the connect.php file for group albums
	    if($cff_albums_only && $cff_albums_source == 'photospage' && $cff_is_group) $cff_content .= ' data-group="true" ';

	    // $cff_content .= apply_filters('cff_data_atts',$cff_content,$this->feed_options).' ';
	    $cff_carousel_active = $this->feed_options['carouselactive'];
	    $cff_is_carousel = false;
	    if( $cff_carousel_active ){
	        if( function_exists('cff_carousel_data_atts') ){
	            $cff_content .= cff_carousel_data_atts( $this->feed_options );
	            $cff_is_carousel = true;
	        }
	    }


	    ( $this->feed_options['featuredpostactive'] && !empty($this->feed_options['featuredpost']) ) ? $cff_featured_post = true : $cff_featured_post = false;
	    //If the Featured Post is enabled then disable the load more button
	    if( $cff_featured_post ) $cff_load_more = false;
	    //Add the shortcode data for pagination
	    $cff_content .= ' data-cff-shortcode="'. $data_att_html .'" data-postid="' . esc_attr( get_the_ID() ) . '"';

	    $flags = [];

		if ( CFF_GDPR_Integrations::doing_gdpr( $this->feed_options ) ) {
			$flags[] = 'gdpr';
			if ( ! CFF_GDPR_Integrations::blocking_cdn( $this->feed_options ) ) {
				$flags[] = 'overrideBlockCDN';
			}
		}

		$fo = $this->cff_get_processed_options( $original_atts );
		$facebook_settings = new CFF_Settings_Pro( $fo );
		$facebook_settings->set_feed_type_and_terms();

		if( CFF_Feed_Locator::should_do_ajax_locating( $this->feed_options['id'], get_the_ID() ) ){
			$flags[] = 'locator';
		}
		if ( CFF_Feed_Locator::should_do_locating() ) {
			$feed_details = array(
				'feed_id' => $this->feed_options['id'],
				'atts' => $original_atts,
				'location' => array(
					'post_id' => get_the_ID(),
					'html' => 'unknown'
				)
			);
			$locator = new CFF_Feed_Locator( $feed_details );
			$locator->add_or_update_entry();
		}


		if ( ! empty( $flags ) ){
			$cff_content .= ' data-cff-flags="' . implode(',', $flags ) . '"';
		}

		$cff_content .= '>';

	    if ( !$cff_no_svgs ) $cff_content .= '<svg width="24px" height="24px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="cff-screenreader" role="img" aria-labelledby="metaSVGid metaSVGdesc"><title id="metaSVGid">Comments Box SVG icons</title><desc id="metaSVGdesc">Used for the like, share, comment, and reaction icons</desc><defs><linearGradient id="angryGrad" x1="0" x2="0" y1="0" y2="1"><stop offset="0%" stop-color="#f9ae9e" /><stop offset="70%" stop-color="#ffe7a4" /></linearGradient><linearGradient id="likeGrad"><stop offset="25%" stop-color="rgba(0,0,0,0.05)" /><stop offset="26%" stop-color="rgba(255,255,255,0.7)" /></linearGradient><linearGradient id="likeGradHover"><stop offset="25%" stop-color="#a3caff" /><stop offset="26%" stop-color="#fff" /></linearGradient><linearGradient id="likeGradDark"><stop offset="25%" stop-color="rgba(255,255,255,0.5)" /><stop offset="26%" stop-color="rgba(255,255,255,0.7)" /></linearGradient></defs></svg>';

	    //Add the page header to the inside of the top of feed
	    if ($cff_show_header && !$cff_header_outside) $cff_content .= $cff_header;
	    //Add like box to the inside of the top of feed



	    //ERROR NOTICES


	    //Interpret data with JSON
	    $FBdata = $json_data;
	    $cff_error_notice = CFF_Utils::print_template_part( 'error-message', get_defined_vars());

	    //****INSERT THE POSTS*****//
	    $cff_content .= $cff_error_notice;
	    $cff_content .= '<div class="cff-posts-wrap">';
	    if ($cff_like_box_position == 'top' && $cff_show_like_box && !$cff_like_box_outside) $cff_content .= $like_box;
	    $cff_content .= $html;
	    $cff_content .= '</div>';

		if ( empty( $_POST['pag_url'] ) ) {
		    $use_cache_object = ! empty( $original_atts['feed'] ) ? (int)$original_atts['feed'] : false;
			$cff_content .= CFF_Utils::cff_add_resized_image_data( $facebook_settings->get_transient_name(), $facebook_settings->get_settings(), 1, $use_cache_object );
		}

	    //Don't show the load more button or credit link if there's an error
	    ( !empty($cff_error_notice) && strpos($cff_error_notice, 'cff-warning-notice') == false ) ? $cff_is_error = true : $cff_is_error = false;

	    if( !$cff_is_error ){

	        //If the load more is enabled and the number of posts is not set to be zero then show the load more button
	        if( $cff_load_more && $pag_num > 0 ){
	            //Load More button
	    		$cff_content .= CFF_Utils::print_template_part( 'load_more', get_defined_vars(), $this);
	        }

	        //Add the Like Box inside
	        if ($cff_like_box_position == 'bottom' && $cff_show_like_box && !$cff_like_box_outside) $cff_content .= $like_box;
	        	$cff_posttext_link_color = str_replace('#', '', $this->feed_options['textlinkcolor'] );
	    		$cff_content .= CFF_Utils::print_template_part( 'credit', get_defined_vars());
	    } // !$cff_is_error


	    //End the feed
	    $cff_content .= '</div>';
	    $cff_content .= '</div>';
	    $cff_content .= '<div class="cff-clear"></div>';
	    //Add the Like Box outside
	    if ($cff_like_box_position == 'bottom' && $cff_show_like_box && $cff_like_box_outside) $cff_content .= $like_box;

	    //If the feed is loaded via Ajax then put the scripts into the shortcode itself
	    $ajax_theme = $this->feed_options['ajax'];
	    ( $ajax_theme == 'on' || $ajax_theme == 'true' || $ajax_theme == '1' || $ajax_theme == true ) ? $ajax_theme = true : $ajax_theme = false;
	    if( $this->feed_options[ 'ajax' ] == 'false' ) $ajax_theme = false;

	    if ($ajax_theme) {
	        //Minify files?
	        $options = get_option('cff_style_settings');
	        isset($options[ 'cff_minify' ]) ? $cff_minify = $options[ 'cff_minify' ] : $cff_minify = '';
	        $cff_minify ? $cff_min = '.min' : $cff_min = '';

	        $url = plugins_url();
	        $path = urlencode(ABSPATH);
	        $cff_link_hashtags = $this->feed_options['linkhashtags'];
	        $cff_title_link = $this->feed_options['textlink'];
	        ($cff_link_hashtags == 'true' || $cff_link_hashtags == 'on') ? $cff_link_hashtags = 'true' : $cff_link_hashtags = 'false';
	        if($cff_title_link == 'true' || $cff_title_link == 'on') $cff_link_hashtags = 'false';
		    $cffOptionsObj = array(
			    'placeholder' => trailingslashit( CFF_PLUGIN_URL ) . 'assets/img/placeholder.png',
			    'resized_url' => Cff_Utils::cff_get_resized_uploads_url(),
		    );
		    //Pass option to JS file
	        $cff_content .= '<script type="text/javascript">var cffsiteurl = "' . $url . '", cfflinkhashtags = "' . $cff_link_hashtags . '";';
		    $cff_content .= 'var cffOptions = ' . CFF_Utils::cff_json_encode( $cffOptionsObj ) . ';';
		    $cff_content .= '</script>';
	        $cff_content .= '<script type="text/javascript" src="' . CFF_PLUGIN_URL . 'assets/js/cff-scripts'.$cff_min.'.js'  . '"></script>';
	    }
	    $cff_content .= '</div>';

	    if ( $doing_custom_styles ) {
		    $cff_content .= '<style type="text/css">'. "\n";

		    if ( ! empty( $this->feed_options['colorpalette'] )
		         && $this->feed_options['colorpalette'] === 'custom' ) {

			    $wrap_selector = '#cff.' . $custom_palette_class;

			    if ( ! empty( $this->feed_options['custombgcolor1'] ) ) {
				    $cff_content .= $wrap_selector . ' ' . '.cff-item,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-item.cff-box,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-item.cff-box:first-child,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-album-item {' . "\n";
				    $cff_content .= '  ' . 'background-color: ' . esc_attr( $this->feed_options['custombgcolor1'] ) . ';' ."\n";
				    $cff_content .= '}' . "\n";
			    }

			    if ( ! empty( $this->feed_options['custombgcolor2'] ) ) {
				    $cff_content .= $wrap_selector . ' ' . '.cff-view-comments,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-load-more,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-shared-link {' . "\n";
				    $cff_content .= '  ' . 'background-color: ' . esc_attr( $this->feed_options['custombgcolor2'] ) . ';' ."\n";
				    $cff_content .= '}' . "\n";
			    }

			    if ( ! empty( $this->feed_options['textcolor1'] ) ) {
				    $cff_content .= $wrap_selector . ' ' . '.cff-comment .cff-comment-text p,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-album-info p,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-story,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-text {' . "\n";
				    $cff_content .= '  ' . 'color: ' . esc_attr( $this->feed_options['textcolor1'] ) . ';' ."\n";
				    $cff_content .= '}' . "\n";
			    }

			    if ( ! empty( $this->feed_options['textcolor2'] ) ) {
				    $cff_content .= $wrap_selector . ' ' . '.cff-comment-date,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-text-link .cff-post-desc,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-link-caption,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-date {' . "\n";
				    $cff_content .= '  ' . 'color: ' . esc_attr( $this->feed_options['textcolor2'] ) . ';' ."\n";
				    $cff_content .= '}' . "\n";
			    }

			    if ( ! empty( $this->feed_options['customlinkcolor'] ) ) {
				    $cff_content .= $wrap_selector . ' ' . 'a,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . '.cff-post-links a,' . "\n";
				    $cff_content .= $wrap_selector . ' ' . 'a {' . "\n";
				    $cff_content .= '  ' . 'color: ' . esc_attr( $this->feed_options['customlinkcolor'] ) . ';' ."\n";
				    $cff_content .= '}' . "\n";
			    }

		    }
		    $lightbox_selector = '#cff-lightbox-wrapper';

		    if ( ! empty( $this->feed_options['lightboxbgcolor'] ) ) {
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-dataContainer,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-sidebar {' . "\n";
			    $cff_content .= '  ' . 'background-color: ' . esc_attr( $this->feed_options['lightboxbgcolor'] ) . ';' ."\n";
			    $cff_content .= '}' . "\n";
		    }

		    if ( ! empty( $this->feed_options['lightboxtextcolor'] ) ) {
			    $cff_content .= $lightbox_selector . ' ' . '.cff-author .cff-date,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-closeContainer svg,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-caption-text {' . "\n";
			    $cff_content .= '  ' . 'color: ' . esc_attr( $this->feed_options['lightboxtextcolor'] ) . ';' ."\n";
			    $cff_content .= '}' . "\n";
		    }

		    if ( ! empty( $this->feed_options['lightboxlinkcolor'] ) ) {
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-caption-text a:link,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-caption-text a:hover,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-caption-text a:active,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-caption-text a:visited,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-facebook:link,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-facebook:hover,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-facebook:active,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . '.cff-lightbox-facebook:visited,' . "\n";
			    $cff_content .= $lightbox_selector . ' ' . 'a {' . "\n";
			    $cff_content .= '  ' . 'color: ' . esc_attr( $this->feed_options['lightboxlinkcolor'] ) . ';' ."\n";
			    $cff_content .= '}' . "\n";
		    }

		    $cff_content .= '</style>';

	    }


	    if( isset( $cff_posttext_link_color ) && !empty( $cff_posttext_link_color ) ) $cff_content .= '<style>#cff .cff-post-text a{ color: #'.$cff_posttext_link_color.'; }</style>';

		if ( isset( $_GET['sb_debug'] ) ) {
			$cff_content .= $this->sb_get_debug_report( $feed_options );
		}

	    //Hook to perform actions before returning $cff_content
	    do_action( 'cff_before_return_content', $this->feed_options );

	    return $cff_content;
	}
	#------------------------------

	/**
	 * Get Debug Report for Feed
	 *
	 * @since 4.0
	 *
	 * @param array $feed_opitons
	 *
	 * @return string $output
	 */
	public function sb_get_debug_report( $feed_options ) {
		if ( !isset( $_GET['sb_debug'] ) ) {
			return;
		}
		$cff_options = get_option( 'cff_style_settings' );

		$output = '';
		$output .= '<p>Settings</p>';
		$output .= '<ul style="word-break: break-all;">';

		$output .= '<li>Optimize Images: ';
        $output .= isset( $cff_options[ 'cff_disable_resize' ] ) && $cff_options[ 'cff_disable_resize' ] == 'on' ? 'Enabled' : 'Disabled';
        $output .= "</li>";
        $output .= "</li>";
        $output .= '<li>AJAX theme loading fix: ';
        $output .= isset( $cff_options[ 'cff_disable_ajax_cache' ] ) && $cff_options[ 'cff_disable_ajax_cache' ] == true ? 'Enabled' : 'Disabled';
        $output .= "</li>";
        $output .= '<li>Show Credit Link: ';
        $output .= isset( $cff_options['cff_format_issue'] ) && $cff_options['cff_format_issue'] == true ? 'Enabled' : 'Disabled';
        $output .= "</li>";
        $output .= '<li>Fix Text Shortening Issue: ';
        $output .= isset( $cff_options['cff_show_credit'] ) && $cff_options['cff_show_credit'] == true ? 'Enabled' : 'Disabled';
        $output .= "</li>";
        $output .= '<li>Admin Error Notice: ';
        $output .= isset( $cff_options['disable_admin_notice'] ) && $cff_options['disable_admin_notice'] == true ? 'Enabled' : 'Disabled';
        $output .= "</li>";
		$output .= '</ul>';

		$output .= '<p>Feed Options</p>';
		$public_settings_keys = CFF_Shortcode_Display::get_public_db_settings_keys();

		$output .= '<ul style="word-break: break-all;">';
		foreach( $feed_options as $key => $option ) {
			if ( is_array( $option ) ) continue;
			if ( in_array( $key, $public_settings_keys, true ) ) {
				$output .= sprintf('<li>%s: %s</li>', esc_html( $key ), esc_html( $option ) );
			}
		}
		$output .= '</ul>';

		return $output;
	}

	/**
	 * The plugin will output settings on the frontend for debugging purposes.
	 * Safe settings to display are added here.
	 **
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_public_db_settings_keys() {
		$public = array(
			'ownaccesstoken',
			'id',
			'pagetype',
			'num',
			'limit',
			'others',
			'showpostsby',
			'cachetype',
			'cachetime',
			'cacheunit',
			'locale',
			'storytags',
			'ajax',
			'offset',
			'account',
			'width',
			'widthresp',
			'height',
			'padding',
			'bgcolor',
			'showauthor',
			'showauthornew',
			'class',
			'type',
			'gdpr',
			'loadiframes',
			'eventsource',
			'eventoffset',
			'eventimage',
			'pastevents',
			'albumsource',
			'showalbumtitle',
			'showalbumnum',
			'albumcols',
			'photosource',
			'photocols',
			'videosource',
			'showvideoname',
			'showvideodesc',
			'videocols',
			'playlist',
			'disablelightbox',
			'filter',
			'exfilter',
			'layout',
			'enablenarrow',
			'oneimage',
			'mediaposition' => 'above',
			'include',
			'exclude',
			'masonry',
			'masonrycols',
			'masonrycolsmobile',
			'masonryjs',
			'cols',
			'colsmobile',
			'colsjs',
			'nummobile',
			'poststyle',
			'postbgcolor',
			'postcorners',
			'boxshadow',
			'textformat',
			'textsize',
			'textweight',
			'textcolor',
			'textlinkcolor',
			'textlink',
			'posttags',
			'linkhashtags',
			'lightboxcomments',
			'authorsize',
			'authorcolor',
			'descsize',
			'descweight',
			'desccolor',
			'linktitleformat',
			'linktitlesize',
			'linkdescsize',
			'linkurlsize',
			'linkdesccolor',
			'linktitlecolor',
			'linkurlcolor',
			'linkbgcolor',
			'linkbordercolor',
			'disablelinkbox',
			'eventtitleformat',
			'eventtitlesize',
			'eventtitleweight',
			'eventtitlecolor',
			'eventtitlelink',
			'eventdatesize',
			'eventdateweight',
			'eventdatecolor',
			'eventdatepos',
			'eventdateformat',
			'eventdatecustom',
			'timezoneoffset',
			'cff_enqueue_with_shortcode',
			'eventdetailssize',
			'eventdetailsweight',
			'eventdetailscolor',
			'eventlinkcolor',
			'datepos',
			'datesize',
			'dateweight',
			'datecolor',
			'dateformat',
			'datecustom',
			'timezone',
			'beforedate',
			'afterdate',
			'linksize',
			'linkweight',
			'linkcolor',
			'viewlinktext',
			'linktotimeline',
			'buttoncolor',
			'buttonhovercolor',
			'buttontextcolor',
			'buttontext',
			'nomoretext',
			'iconstyle',
			'socialtextcolor',
			'socialbgcolor',
			'sociallinkcolor',
			'expandcomments',
			'commentsnum',
			'hidecommentimages',
			'loadcommentsjs',
			'salesposts',
			'textlength',
			'desclength',
			'showlikebox',
			'likeboxpos',
			'likeboxoutside',
			'likeboxcolor',
			'likeboxtextcolor',
			'likeboxwidth',
			'likeboxfaces',
			'likeboxborder',
			'likeboxcover',
			'likeboxsmallheader',
			'likeboxhidebtn',
			'credit',
			'textissue',
			'disablesvgs',
			'restrictedpage',
			'hidesupporterposts',
			'privategroup',
			'nofollow',
			'timelinepag',
			'gridpag',
			'disableresize',
			'showheader',
			'headertype',
			'headercover',
			'headeravatar',
			'headername',
			'headerbio',
			'headercoverheight',
			'headerlikes',
			'headeroutside',
			'headertext',
			'headerbg',
			'headerpadding',
			'headertextsize',
			'headertextweight',
			'headertextcolor',
			'headericon',
			'headericoncolor',
			'headericonsize',
			'headerinc',
			'headerexclude',
			'loadmore',
			'fulllinkimages',
			'linkimagesize',
			'postimagesize',
			'videoheight',
			'videoaction',
			'videoplayer',
			'sepcolor',
			'sepsize',
			'seemoretext',
			'seelesstext',
			'photostext',
			'facebooklinktext',
			'sharelinktext',
			'showfacebooklink',
			'showsharelink',
			'buyticketstext',
			'maptext',
			'interestedtext',
			'goingtext',
			'previouscommentstext',
			'commentonfacebooktext',
			'likesthistext',
			'likethistext',
			'reactedtothistext',
			'andtext',
			'othertext',
			'otherstext',
			'noeventstext',
			'replytext',
			'repliestext',
			'learnmoretext',
			'shopnowtext',
			'messagepage',
			'getdirections',
			'secondtext',
			'secondstext',
			'minutetext',
			'minutestext',
			'hourtext',
			'hourstext',
			'daytext',
			'daystext',
			'weektext',
			'weekstext',
			'monthtext',
			'monthstext',
			'yeartext',
			'yearstext',
			'agotext',
			'multifeedactive',
			'daterangeactive',
			'featuredpostactive',
			'albumactive',
			'masonryactive',
			'carouselactive',
			'reviewsactive',
			'from',
			'until',
			'featuredpost',
			'album',
			'daterange',
			'lightbox',
			'reviewsrated',
			'starsize',
			'hidenegative',
			'reviewslinktext',
			'reviewshidenotext',
			'reviewsmethod',
			'feedtype',
			'likeboxcustomwidth',
			'colstablet',
			'feedlayout',
			'colorpalette',
			'custombgcolor1',
			'custombgcolor2',
			'textcolor1',
			'textcolor2',
			'posttextcolor',
			'misctextcolor',
			'misclinkcolor',
			'headericonenabled',
			'lightboxbgcolor',
			'lightboxtextcolor',
			'lightboxlinkcolor',
			'beforedateenabled',
			'afterdateenabled',
			'showpoststypes',
			'headerbiosize',
			'headerbiocolor',
			'apipostlimit',
			'carouselheight',
			'carouseldesktop_cols',
			'carouselmobile_cols',
			'carouselnavigation',
			'carouselpagination',
			'carouselautoplay',
			'carouselinterval',
		);

		return $public;
	}


	/**
	 * Style Compiler.
	 *
	 * Returns an array containing all the styles for the Feed
	 *
	 * @since 3.18
	 * @return String
	 */
	public function style_compiler( $style_array ){
		$style = '';
		foreach ($style_array as $single_style) {
			if( !empty($single_style['value']) && $single_style['value'] != '#' && $single_style['value'] != 'inherit' && $single_style['value'] != '0' ){
				$style .= 	$single_style['css_name'] . ':' .
							(isset($single_style['pref']) ? $single_style['pref'] : '') .
							$single_style['value'] .
							(isset($single_style['suff']) ? $single_style['suff'] : '') .
							';';
			}
		}
		$style = ( !empty($style) ) ? ' style="' . $style . '" ' : '';
		return $style;
	}

	/**
	 *
	 * Style Attribute
	 * Generates the Style attribute for the Feed Elements
	 *
	 * @since 3.18
	 * @return String
	 */
	public function get_style_attribute( $element ){
		$style_array = [];
		switch ($element) {
			case 'load_more':
				$style_array = [
					['css_name' => 'background-color', 'value' => str_replace('#', '', $this->feed_options['buttoncolor']), 'pref' => '#'],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['buttontextcolor']), 'pref' => '#']
				];
			break;
			case 'header':
				$style_array = [
					['css_name' => 'background-color', 'value' => str_replace('#', '', $this->feed_options['headerbg']), 'pref' => '#'],
					['css_name' => 'padding', 'value' => CFF_Utils::get_css_distance( $this->feed_options['headerpadding'] ) ],
					['css_name' => 'font-size', 'value' => $this->feed_options['headertextsize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->feed_options['headertextweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['headertextcolor']), 'pref' => '#']
				];
			break;
			case 'header_visual':
				$style_array = [
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['headertextcolor']), 'pref' => '#'],
					['css_name' => 'font-size', 'value' => $this->feed_options['headertextsize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->feed_options['headertextweight']]
				];
			break;

			case 'header_icon':
				$style_array = [
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['headericoncolor']), 'pref' => '#'],
					['css_name' => 'font-size', 'value' => $this->feed_options['headericonsize'], 'suff' => 'px']
				];
			break;
			case 'likes_comment_box':
				$style_array = [
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['socialtextcolor']), 'pref' => '#'],
					['css_name' => 'background-color', 'value' => str_replace('#', '', $this->feed_options['socialbgcolor']), 'pref' => '#'],
				];
			break;

			case 'post_text':
				$style_array = [
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['textcolor']), 'pref' => '#'],
					['css_name' => 'font-size', 'value' => $this->feed_options['textsize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->feed_options['textweight']]
				];
			break;

			case 'author':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->feed_options['authorsize'], 'suff' => 'px'],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['authorcolor']), 'pref' => '#']
				];
			break;

			case 'body_description':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->feed_options['descsize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->feed_options['descweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['desccolor']), 'pref' => '#']
				];
			break;

			case 'link_box':
				$style_array = [
					['css_name' => 'border', 'value' => str_replace('#', '', $this->feed_options['linkbordercolor']), 'pref' => ' 1px solid #'],
					['css_name' => 'background-color', 'value' => str_replace('#', '', $this->feed_options['linkbgcolor']), 'pref' => '#']
				];
			break;

			case 'event_title':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->feed_options['eventtitlesize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->feed_options['eventtitleweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['eventtitlecolor']), 'pref' => '#']
				];
			break;
			case 'event_date':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->feed_options['eventdatesize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->feed_options['eventdateweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['eventdatecolor']), 'pref' => '#']
				];
			break;
			case 'event_detail':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->feed_options['eventdetailssize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->feed_options['eventdetailsweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['eventdetailscolor']), 'pref' => '#']
				];
			break;
			case 'date':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->feed_options['datesize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->feed_options['dateweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['datecolor']), 'pref' => '#']
				];
			break;
			case 'post_link':
				$style_array = [
					['css_name' => 'font-size', 'value' => $this->feed_options['linksize'], 'suff' => 'px'],
					['css_name' => 'font-weight', 'value' => $this->feed_options['linkweight']],
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['linkcolor']), 'pref' => '#']
				];
			break;
			case 'text_link':
				$style_array = [
					['css_name' => 'color', 'value' => str_replace('#', '', $this->feed_options['textlinkcolor']), 'pref' => '#']
				];
			break;


		}

		return $this->style_compiler( $style_array );
	}

	/**
	 *
	 * Get Likebox Data
	 * Get the likebox data for the templates
	 *
	 * @since 3.18
	 * -----------------------------------------
	 */
	static function get_likebox_height( $cff_like_box_small_header, $cff_like_box_faces ){
		//Calculate the like box height
		$cff_likebox_height = 135;
		if( $cff_like_box_small_header == 'true' ) $cff_likebox_height = 75;
		if( $cff_like_box_faces == 'true' ) $cff_likebox_height = 219;
		if( $cff_like_box_small_header == 'true' && $cff_like_box_faces == 'true' ) $cff_likebox_height = 159;
		return $cff_likebox_height;
	}

	static function get_likebox_width( $cff_likebox_width ){
	    if ( is_numeric(substr($cff_likebox_width, -1, 1)) ) $cff_likebox_width = $cff_likebox_width;
	    if ( !isset($cff_likebox_width) || empty($cff_likebox_width) || $cff_likebox_width == '' ) $cff_likebox_width = '';
	    if( $cff_likebox_width == '100%' ) $cff_likebox_width = 500;
	    $cff_likebox_width = str_replace("%", "", $cff_likebox_width);
	    return $cff_likebox_width;
	}

	static function get_likebox_classes( $atts, $cff_show_like_box, $cff_like_box_outside ){
		$cut_class = ($cff_show_like_box && !$cff_like_box_outside) ? " cff-item" : '';
		$cut_class = "";

		return "cff-likebox" . ( $atts[ 'likeboxoutside' ] ? " cff-outside" : '' ) . ( $atts[ 'likeboxpos' ] == 'top' ? ' cff-top' : ' cff-bottom' ) . $cut_class;
	}

	static function get_likebox_tag( $atts ){
		return ( $atts[ 'likeboxpos' ] == 'top') ? 'section' : 'div';
	}


	/**
	 *
	 * Get Load More Button Data
	 * Get the load more button data for the templates
	 *
	 * @since 3.18
	 * -----------------------------------------
	 */
	static function get_load_more_button_attr( $atts ){
	    $palette = ! empty( $atts['colorpalette'] ) ? $atts['colorpalette'] : '';
		$translations = get_option( 'cff_style_settings', false );

		$atts['nomoretext'] = isset( $translations[ 'cff_no_more_posts_text' ] ) ? stripslashes( esc_attr( $translations[ 'cff_no_more_posts_text' ] ) ) : __( 'No more posts', 'custom-facebook-feed' );

	    if ( ! empty( $palette )
             && ($palette === 'dark' || $palette === 'light') ) {
	        return ' data-no-more="'.$atts['nomoretext'].'"';
        }
		return ' data-cff-bg="'. esc_attr( $atts['buttoncolor'] ) .'" data-cff-hover="'. esc_attr( $atts['buttonhovercolor'] ) .'" data-no-more="'. esc_attr( $atts['nomoretext'] ) .'"';
	}

	/**
	 *
	 * Get Header Data
	 * Get the Header data for the templates
	 *
	 * @since 3.18
	 * -----------------------------------------
	 */
	static function get_header_txt_classes( $cff_header_outside ){
		return ($cff_header_outside) ? " cff-outside" : '';
	}
	static function get_header_parts( $atts ){
		if ( !empty( $atts['headerinc'] ) || !empty( $atts['headerexclude'] ) ) {
			if ( !empty( $atts['headerinc'] ) ) {
				$header_inc = explode( ',', str_replace( ' ', '', strtolower( $atts['headerinc'] ) ) );
				$cff_header_cover = in_array( 'cover', $header_inc, true );
				$cff_header_name = in_array( 'name', $header_inc, true );
				$cff_header_bio = in_array( 'about', $header_inc, true );
			} else {
				$header_exc = explode( ',', str_replace( ' ', '', strtolower( $atts['headerexclude'] ) ) );
				$cff_header_cover = ! in_array( 'cover', $header_exc, true );
				$cff_header_name = ! in_array( 'name', $header_exc, true );
				$cff_header_bio = ! in_array( 'about', $header_exc, true );
			}
		}else{
			$cff_header_cover = CFF_Utils::check_if_on( $atts['headercover'] );
			$cff_header_name = CFF_Utils::check_if_on( $atts['headername'] );
			$cff_header_bio = CFF_Utils::check_if_on( $atts['headerbio'] );
		}

		return [
			'cover' 		=> $cff_header_cover,
			'name' 			=> $cff_header_name,
			'bio'			=> $cff_header_bio
		];
	}

	static function get_header_height_style( $atts ){
		$cff_header_cover_height = ! empty( $atts['headercoverheight'] ) ? (int)$atts['headercoverheight'] : 300;
		$header_hero_style = $cff_header_cover_height !== 300 ? ' style="height: '.$cff_header_cover_height.'px";' : '';
		return $header_hero_style;
	}

	static function get_header_font_size( $atts ){
		return !empty($atts['headertextsize']) ? 'style="font-size:'. $atts['headertextsize'] .'px;"'  : '';
	}

	static function get_header_link( $header_data, $page_id ){
		$link = CFF_Parse_Pro::get_link( $header_data );
		if( $link == 'https://facebook.com' ) $link .= '/'.$page_id;
		return $link;
	}

	/**
	 *
	 * Get Error Message Data
	 * Get the error message data for the templates
	 *
	 * @since 3.18
	 * -----------------------------------------
	 */
	static function get_error_check( $page_id, $user_id, $access_token ){
		$cff_ppca_check_error = false;
		if( ! get_user_meta($user_id, 'cff_ppca_check_notice_dismiss') && strpos($page_id, ',') == false && !is_array($access_token) ){
			$cff_posts_json_url = 'https://graph.facebook.com/v8.0/'.$page_id.'/posts?limit=1&access_token='.$access_token;
			$transient_name = 'cff_ppca_' . substr($page_id, 0, 5) . substr($page_id, strlen($page_id)-5, 5) . '_' . substr($access_token, 15, 10);
			$cff_cache_time = 1;
			$cache_seconds = YEAR_IN_SECONDS;
			$cff_ppca_check = CFF_Utils::cff_get_set_cache($cff_posts_json_url, $transient_name, $cff_cache_time, $cache_seconds, '', true, $access_token, $backup=false);
			$cff_ppca_check_json = json_decode($cff_ppca_check);

			if( isset( $cff_ppca_check_json->error ) && strpos($cff_ppca_check_json->error->message, 'Public Content Access') !== false ){
				$cff_ppca_check_error = true;
			}
		}
		return $cff_ppca_check_error;
	}
	static function get_error_message_cap( ){
		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );
		return $cap;
	}
	static function get_error_check_ppca( $FBdata ){
		//Is it a PPCA error from the API?
		return ( isset($FBdata->error->message) && strpos($FBdata->error->message, 'Public Content Access') !== false ) ? true : false;
	}

	static function get_error_check_no_data( $FBdata, $cff_events_only, $cff_events_source, $cff_featured_post_active, $page_id, $cff_ppca_check_error, $atts ){
		//If there's no data then show a pretty error message
		return (( empty($FBdata->data) && empty($FBdata->videos) ) && !($cff_events_only && $cff_events_source == 'eventspage') && (!$cff_featured_post_active || empty($atts['featuredpost'])) && strpos($page_id, ',') == false || isset($FBdata->cached_error) || $cff_ppca_check_error );
	}

	/**
	 *
	 * Style Attribute
	 * Generates the Style attribute for the Feed Elements
	 *
	 * @since 3.18
	 * @return String
	 */
	public function check_show_section( $section_name ){
		$include_array = explode(',' , $this->feed_options[ 'include' ]);
		$exclude_array = explode(',' , $this->feed_options[ 'exclude' ]);
		$is_shown = in_array($section_name, $include_array);
		$is_shown = in_array($section_name, $exclude_array) ? false : $is_shown;
		//$is_shown = ( CFF_Utils::stripos($this->feed_options[ 'include' ], $section_name) !== false ) ? true : false;
		//$is_shown = ( CFF_Utils::stripos($this->feed_options[ 'exclude' ], $section_name) !== false ) ? false : $is_shown;
		return $is_shown;
	}


	/**
	 *
	 * Get Author Template Data
	 * Get Authors the data for the templates
	 *
	 * @since 3.18
	 * -----------------------------------------
	 */

	static function get_author_name( $news ){
		return isset($news->from->name) ? str_replace('"', "", htmlentities($news->from->name, ENT_QUOTES, 'UTF-8')) : '';
	}

	static function get_author_link_atts( $cff_new_from_link, $news, $target, $cff_nofollow, $cff_author_styles ){
	 	return empty($cff_new_from_link) ? '' : ' href="https://facebook.com/' . $news->from->id . '" '.$target.$cff_nofollow.' '.$cff_author_styles;
	}

	static function get_author_link_el( $cff_new_from_link, $news ){
		return empty($cff_new_from_link) ? 'span' : 'a';
	}

	static function get_author_new_from_link_( $news ){
		$cff_new_from_link = isset( $news->from->link ) ? $news->from->link : '';
		$cff_new_from_link = apply_filters( 'cff_new_from_link', $cff_new_from_link );
		return $cff_new_from_link;
	}

	static function get_author_post_text_story( $post_text_story,  $cff_author_name ){
		if( !empty($cff_author_name) ){
			$cff_author_name_pos = strpos($post_text_story, $cff_author_name);
			if ($cff_author_name_pos !== false) {
				$post_text_story = substr_replace($post_text_story, '', $cff_author_name_pos, strlen($cff_author_name));
			}
		}
		return $post_text_story;
	}

	static function get_author_pic_src_class( $news, $atts ){
		$cff_author_src = $cff_author_img_src = isset($news->from->picture->data->url) ? $news->from->picture->data->url : '';
		$img_class = '';
		if ( CFF_GDPR_Integrations::doing_gdpr( $atts ) && CFF_GDPR_Integrations::blocking_cdn( $atts ) ){
			$cff_author_img_src = CFF_PLUGIN_URL. '/assets/img/placeholder.png';
			$img_class = ' cff-no-consent';
		}
		return [
			'real_image' 	=> $cff_author_src,
			'image' 		=> $cff_author_img_src,
			'class' 		=> $img_class
		];
	}

	/**
	 *
	 * Get Date Data
	 * Get Date the data for the templates
	 *
	 * @since 3.18
	 * -----------------------------------------
	 */
	static function get_post_date( $atts, $news ){
		$cff_timezone = $atts['timezone'];
		//Posted ago strings
		$cff_date_translate_strings = array(
			'cff_translate_second' 		=> $atts['secondtext'],
			'cff_translate_seconds' 	=> $atts['secondstext'],
			'cff_translate_minute' 		=> $atts['minutetext'],
			'cff_translate_minutes' 	=> $atts['minutestext'],
			'cff_translate_hour' 		=> $atts['hourtext'],
			'cff_translate_hours' 		=> $atts['hourstext'],
			'cff_translate_day' 		=> $atts['daytext'],
			'cff_translate_days' 		=> $atts['daystext'],
			'cff_translate_week' 		=> $atts['weektext'],
			'cff_translate_weeks' 		=> $atts['weekstext'],
			'cff_translate_month' 		=> $atts['monthtext'],
			'cff_translate_months' 		=> $atts['monthstext'],
			'cff_translate_year' 		=> $atts['yeartext'],
			'cff_translate_years' 		=> $atts['yearstext'],
			'cff_translate_ago' 		=> $atts['agotext']
		);
		$cff_date_formatting 	= $atts[ 'dateformat' ];
		$cff_date_custom 		= $atts[ 'datecustom' ];

		$post_time = isset($news->created_time) ? $news->created_time : '';
		$post_time = isset($news->backdated_time) ? $news->backdated_time : $post_time; //If the post is backdated then use that as the date instead
		return CFF_Utils::cff_getdate(strtotime($post_time), $cff_date_formatting, $cff_date_custom, $cff_date_translate_strings, $cff_timezone);
	}
	static function get_date( $atts, $news ){
		$cff_date_before = isset($atts[ 'beforedate' ]) ? $atts[ 'beforedate' ] : '';
		$cff_date_after = isset($atts[ 'afterdate' ]) ? $atts[ 'afterdate' ] : '';
		return $cff_date_before . ' ' .CFF_Shortcode_Display::get_post_date( $atts, $news ) . ' ' . $cff_date_after;
	}

	static function get_date_classes( $cff_date_position,$cff_show_author ){
		return ( $cff_date_position == 'below' || ($cff_date_position == 'author' && !$cff_show_author) ) ? ' cff-date-below' : '';
	}


	/**
	 *
	 * Get Like Comment Data
	 * Get Like & Comment Box the data for the templates
	 *
	 * @since 3.18
	 * -----------------------------------------
	 */
	static function get_like_comment_btn_classes( $cff_lightbox_comments , $cff_show_meta ){
		return 'class="cff-view-comments ' . ( $cff_lightbox_comments && !$cff_show_meta ? 'cff-hide-comments' : '') .'"' ;
	}

	static function get_like_comment_icons_info( $cff_post_type, $news, $news_event, $cff_is_group ){
		$news_object = ( $cff_post_type == 'event' ) ? $news_event : $news;
		$like_count = $share_count = $comment_count = '0';
		if($cff_is_group){
			$like_count = isset( $news_object->reactions->summary->total_count ) ? $news_object->reactions->summary->total_count : 0;
		} else {
			$like_count = isset( $news_object->likes->summary->total_count ) ? $news_object->likes->summary->total_count : 0;
		}
		$share_count = empty($news->shares->count) ? '0' : $news->shares->count;
		$comment_count = !empty($news->comments->data) && isset($news->comments->summary->total_count) ? intval($news->comments->summary->total_count) : 0;

		return [
			'like' => [
				'icon' =>  	'<svg width="24px" height="24px" role="img" aria-hidden="true" aria-label="Like" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M496.656 285.683C506.583 272.809 512 256 512 235.468c-.001-37.674-32.073-72.571-72.727-72.571h-70.15c8.72-17.368 20.695-38.911 20.695-69.817C389.819 34.672 366.518 0 306.91 0c-29.995 0-41.126 37.918-46.829 67.228-3.407 17.511-6.626 34.052-16.525 43.951C219.986 134.75 184 192 162.382 203.625c-2.189.922-4.986 1.648-8.032 2.223C148.577 197.484 138.931 192 128 192H32c-17.673 0-32 14.327-32 32v256c0 17.673 14.327 32 32 32h96c17.673 0 32-14.327 32-32v-8.74c32.495 0 100.687 40.747 177.455 40.726 5.505.003 37.65.03 41.013 0 59.282.014 92.255-35.887 90.335-89.793 15.127-17.727 22.539-43.337 18.225-67.105 12.456-19.526 15.126-47.07 9.628-69.405zM32 480V224h96v256H32zm424.017-203.648C472 288 472 336 450.41 347.017c13.522 22.76 1.352 53.216-15.015 61.996 8.293 52.54-18.961 70.606-57.212 70.974-3.312.03-37.247 0-40.727 0-72.929 0-134.742-40.727-177.455-40.727V235.625c37.708 0 72.305-67.939 106.183-101.818 30.545-30.545 20.363-81.454 40.727-101.817 50.909 0 50.909 35.517 50.909 61.091 0 42.189-30.545 61.09-30.545 101.817h111.999c22.73 0 40.627 20.364 40.727 40.727.099 20.363-8.001 36.375-23.984 40.727zM104 432c0 13.255-10.745 24-24 24s-24-10.745-24-24 10.745-24 24-24 24 10.745 24 24z"></path></svg>'.'<svg width="24px" height="24px" class="cff-svg-bg" role="img" aria-hidden="true" aria-label="background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M104 224H24c-13.255 0-24 10.745-24 24v240c0 13.255 10.745 24 24 24h80c13.255 0 24-10.745 24-24V248c0-13.255-10.745-24-24-24zM64 472c-13.255 0-24-10.745-24-24s10.745-24 24-24 24 10.745 24 24-10.745 24-24 24zM384 81.452c0 42.416-25.97 66.208-33.277 94.548h101.723c33.397 0 59.397 27.746 59.553 58.098.084 17.938-7.546 37.249-19.439 49.197l-.11.11c9.836 23.337 8.237 56.037-9.308 79.469 8.681 25.895-.069 57.704-16.382 74.757 4.298 17.598 2.244 32.575-6.148 44.632C440.202 511.587 389.616 512 346.839 512l-2.845-.001c-48.287-.017-87.806-17.598-119.56-31.725-15.957-7.099-36.821-15.887-52.651-16.178-6.54-.12-11.783-5.457-11.783-11.998v-213.77c0-3.2 1.282-6.271 3.558-8.521 39.614-39.144 56.648-80.587 89.117-113.111 14.804-14.832 20.188-37.236 25.393-58.902C282.515 39.293 291.817 0 312 0c24 0 72 8 72 81.452z"></path></svg>',
				'count' => 	$like_count
			],
			'share' => [
				'icon' =>	'<svg width="24px" height="24px" role="img" aria-hidden="true" aria-label="Share" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M564.907 196.35L388.91 12.366C364.216-13.45 320 3.746 320 40.016v88.154C154.548 130.155 0 160.103 0 331.19c0 94.98 55.84 150.231 89.13 174.571 24.233 17.722 58.021-4.992 49.68-34.51C100.937 336.887 165.575 321.972 320 320.16V408c0 36.239 44.19 53.494 68.91 27.65l175.998-184c14.79-15.47 14.79-39.83-.001-55.3zm-23.127 33.18l-176 184c-4.933 5.16-13.78 1.73-13.78-5.53V288c-171.396 0-295.313 9.707-243.98 191.7C72 453.36 32 405.59 32 331.19 32 171.18 194.886 160 352 160V40c0-7.262 8.851-10.69 13.78-5.53l176 184a7.978 7.978 0 0 1 0 11.06z"></path></svg>'.'<svg width="24px" height="24px" class="cff-svg-bg" role="img" aria-hidden="true" aria-label="background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z"></path></svg>',
				'count' => 	$share_count
			],
			'comment' => [
				'icon' => 	'<svg width="24px" height="24px" role="img" aria-hidden="true" aria-label="Comment" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm32 352c0 17.6-14.4 32-32 32H293.3l-8.5 6.4L192 460v-76H64c-17.6 0-32-14.4-32-32V64c0-17.6 14.4-32 32-32h384c17.6 0 32 14.4 32 32v288z"></path></svg>'.'<svg width="24px" height="24px" class="cff-svg-bg" role="img" aria-hidden="true" aria-label="background" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"></path></svg>',
				'count' => 	$comment_count
			]
		];
	}

	/**
	 *
	 * Get Post Link Data
	 * Get the Post link data for the templates
	 *
	 * @since 3.18
	 * -----------------------------------------
	 */
	static function get_post_link_social_links( $link, $cff_post_text_to_share ){
		return [
			'facebook' => [
				'icon' => 'facebook-square',
				'text' => esc_html__('Share on Facebook', 'custom-facebook-feed'),
				'share_link' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($link)
			],
			'twitter' => [
				'icon' => 'twitter',
				'text' => esc_html__('Share on Twitter', 'custom-facebook-feed'),
				'share_link' => 'https://twitter.com/intent/tweet?text=' . urlencode($link)
			],
			'linkedin' => [
				'icon' => 'linkedin',
				'text' => esc_html__('Share on Linked In', 'custom-facebook-feed'),
				'share_link' => 'https://www.linkedin.com/shareArticle?mini=true&amp;url=' . urlencode($link) . '&amp;title=' . rawurlencode( strip_tags($cff_post_text_to_share) )
			],
			'email' => [
				'icon' => 'envelope',
				'text' => esc_html__('Share by Email', 'custom-facebook-feed'),
				'share_link' => 'mailto:?subject=Facebook&amp;body=' . urlencode($link) . '%20-%20' . rawurlencode( strip_tags($cff_post_text_to_share) )
			]
		];
	}

	static function get_post_link_text_to_share( $cff_post_text ){
		$cff_post_text_to_share = '';
		if( strpos($cff_post_text, '<span class="cff-expand">') !== false ){
			$cff_post_text_to_share = explode('<span class="cff-expand">', $cff_post_text);
			if( is_array($cff_post_text_to_share) ) $cff_post_text_to_share = $cff_post_text_to_share[0];
		}
		return $cff_post_text_to_share;
	}

	static function get_post_link_text_link( $atts, $cff_post_type ){
		$cff_facebook_link_text = $atts[ 'facebooklinktext' ];
		$link_text = ($cff_facebook_link_text != '' && !empty($cff_facebook_link_text))  ? $cff_facebook_link_text : esc_html__('View on Facebook', 'custom-facebook-feed');
		//If it's an offer post then change the text
		if ($cff_post_type == 'offer') $link_text = esc_html__('View Offer', 'custom-facebook-feed');
		return $link_text;
	}

	static function get_post_link_fb_share_text( $atts ){
		return ( $atts[ 'sharelinktext' ] ) ? $atts[ 'sharelinktext' ]  : esc_html__('Share', 'custom-facebook-feed');
	}

	static function get_post_share_link( $atts, $news, $cff_post_type, $page_id, $PostID ){
	}

	/*
	*
	* PRINT THE GDPR NTOCE FOR ADMINS IN THE FRON END
	*
	*/
	static function print_gdpr_notice($element_name, $custom_class = ''){
		if ( ! is_user_logged_in()  || ! current_user_can( 'edit_posts' )) {
			return;
		}
	?>
		<div class="cff-gdpr-notice <?php echo $custom_class; ?>">
			<?php echo CFF_Display_Elements_Pro::get_icon( 'lock' ) ?>
			<?php echo esc_html__('This notice is visible to admins only.','custom-facebook-feed') ?><br/>
			<?php echo $element_name.' '.esc_html__('disabled due to GDPR setting.','custom-facebook-feed') ?> <a href="<?php echo esc_url(admin_url('admin.php?page=cff-style&tab=misc#gdpr')); ?>"><?php echo esc_html__('Click here','custom-facebook-feed') ?></a> <?php echo esc_html__('for more info.','custom-facebook-feed') ?>
		</div>
	<?php
	}

}