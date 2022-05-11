<?php
/**
 * The Feed Templates Settings Trait
 * 
 * It has the default settings for the feed templates for variou feed types
 *
 * @since 4.0
 */

namespace CustomFacebookFeed\Admin\Traits;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


trait CFF_Feed_Templates_Settings {

	/**
	 * Add feed settings depending on feed templates
	 * 
	 * @since 4.2.0
	 */
	public static function get_feed_settings_by_feed_templates( $settings ) {
        // Check if the feedtype is timelime/posts
		if ( $settings['feedtype'] == 'timeline' ) {
			$settings = self::get_timeline_feedtemplate_settings( $settings );
		}
        
        // Check if the feedtype is photos or singlealbum
        if ( $settings['feedtype'] == 'photos' || $settings['feedtype'] == 'singlealbum' ) {
			$settings = self::get_photos_feedtemplate_settings( $settings );
		}
		
        // Check if the feedtype is videos or albums
        if ( $settings['feedtype'] == 'videos' || $settings['feedtype'] == 'albums' ) {
			$settings = self::get_videos_feedtemplate_settings( $settings );
		}

		return $settings;
	}
    
    /**
     * Get feedtemplats settings for Timeline feedtypes
     * 
     * @since 4.2.0
     */
    public static function get_timeline_feedtemplate_settings( $settings ) {
        if ( $settings['feedtemplate'] == 'default' ) {
            // Feed Layout
            $settings['feedlayout'] = 'list';
            $settings['num'] = '3';
            $settings['nummobile'] = '3';
            
            // Posts
            $settings['layout'] = 'half';
            $settings['enablenarrow'] = 'on';
            $settings['poststyle'] = 'regular';
            $settings['sepcolor'] = '#ddd';
            $settings['sepsize'] = '1';
            
            // Load More Button
            $settings['loadmore'] = 'on';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_visual_header_options( $settings );
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'simple_masonry' ) {
            // Feed Layout
            $settings['feedlayout'] = 'masonry';
            $settings['num'] = '9';
            $settings['nummobile'] = '2';
            $settings['cols'] = '3';
            $settings['colstablet'] = '2';
            $settings['colsmobile'] = '1';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';
            $settings['poststyle'] = 'boxed';
            $settings['postbgcolor'] = '#fff';
            $settings['postcorners'] = '4';
            $settings['boxshadow'] = 'on';
            
            // Load More Button
            $settings['loadmore'] = 'on';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings['showheader'] = 'off';
            
            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'simple_carousel' ) {
            // Feed layout
            $settings['feedlayout'] = 'carousel';
            $settings['num'] = '6';
            $settings['nummobile'] = '3';
            $settings['carouselheight'] = 'tallest';
            $settings['carouseldesktop_cols'] = '3';
            $settings['carouselmobile_cols'] = '1';
            $settings['carouselnavigation'] = 'onhover';
            $settings['carouselpagination'] = 'true';
            $settings['carouselautoplay'] = 'true';
            $settings['carouselinterval'] = '5000';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );
            $settings['postcorners'] = '4';

            
            // Load More
            $settings['loadmore'] = 'off';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_text_header_options( $settings );
            $settings['headertext'] = 'We are on Facebook';
            
            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'simple_cards' ) {
            // Feed layout
            $settings['feedlayout'] = 'list';
            $settings['num'] = '6';
            $settings['nummobile'] = '3';
            
            // Posts
            $settings['layout'] = 'half';
            $settings['enablenarrow'] = 'on';

            // Post Style            
            $settings = self::filter_post_style_options( $settings );

            // Load More
            $settings['loadmore'] = 'on';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings['showheader'] = 'off';
            
            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'showcase_carousel' ) {
            // Feed layout
            $settings = self::filter_default_showcase_carousel_options( $settings );

            // Posts
            $settings['layout'] = 'half';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );
            
            // Load More
            $settings['loadmore'] = 'off';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header            
            $settings = self::filter_text_header_options( $settings );

            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'latest_post' ) {
            // Feed Layout
            $settings['feedlayout'] = 'list';
            $settings['num'] = '1';
            $settings['nummobile'] = '1';
            
            // Posts
            $settings['layout'] = 'half';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );

            // Load More Button
            $settings['loadmore'] = 'off';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings['showheader'] = 'off';
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'widget' ) {
            // Feed Layout
            $settings['feedlayout'] = 'list';
            $settings['num'] = '2';
            $settings['nummobile'] = '2';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );
            $settings['mediaposition'] = 'above';
			$settings['include'] = 'text,desc,sharedlinks,date,media,medialink,eventtitle,eventdetails,social,link,likebox';
            
            // Load More Button
            $settings['loadmore'] = 'off';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_visual_header_options( $settings );
            $settings['headercoverheight'] = '200';
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        return $settings;
    }
    
    /**
     * Get feedtemplats settings for photos or single album feedtypes
     * 
     * @since 4.2.0
     */
    public static function get_photos_feedtemplate_settings( $settings ) {
        if ( $settings['feedtemplate'] == 'default' ) {
            // Feed Layout
            $settings['feedlayout'] = 'grid';
            $settings['num'] = '6';
            $settings['nummobile'] = '4';
            $settings['cols'] = '3';
            $settings['colstablet'] = '3';
            $settings['colsmobile'] = '2';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';
            $settings['poststyle'] = 'regular';
            $settings['sepcolor'] = '#ddd';
            $settings['sepsize'] = '1';
            
            // Load More Button
            $settings['loadmore'] = 'on';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_visual_header_options( $settings );
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'simple_masonry' ) {
            // Feed Layout
            $settings['feedlayout'] = 'masonry';
            $settings['num'] = '16';
            $settings['nummobile'] = '6';
            $settings['cols'] = '4';
            $settings['colstablet'] = '3';
            $settings['colsmobile'] = '2';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';
            
            // Post Style
            $settings = self::filter_post_style_regular_options( $settings );
            
            // Load More Button
            $settings['loadmore'] = 'on';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings['showheader'] = 'off';
            
            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'simple_carousel' ) {
            // Feed layout
            $settings['feedlayout'] = 'carousel';
            $settings['num'] = '6';
            $settings['nummobile'] = '3';
            $settings['carouselheight'] = 'tallest';
            $settings['carouseldesktop_cols'] = '3';
            $settings['carouselmobile_cols'] = '1';
            $settings['carouselnavigation'] = 'onhover';
            $settings['carouselpagination'] = 'true';
            $settings['carouselautoplay'] = 'true';
            $settings['carouselinterval'] = '5000';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_regular_options( $settings );
            
            // Load More
            $settings['loadmore'] = 'off';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_text_header_options( $settings );
            
            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        // This works as 'Large Grid' for Photos/Single Album feed type
        if ( $settings['feedtemplate'] == 'simple_cards' ) {
            // Feed Layout
            $settings['feedlayout'] = 'grid';
            $settings['num'] = '24';
            $settings['nummobile'] = '9';
            $settings['cols'] = '6';
            $settings['colstablet'] = '4';
            $settings['colsmobile'] = '3';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_regular_options( $settings );
            
            // Load More Button
            $settings['loadmore'] = 'on';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_visual_header_options( $settings );

            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'showcase_carousel' ) {
            // Feed layout
            $settings = self::filter_default_showcase_carousel_options( $settings );

            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );

            // Load More
            $settings['loadmore'] = 'off';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_text_header_options( $settings );

            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'latest_post' ) {
            // Feed Layout
            $settings['feedlayout'] = 'list';
            $settings['num'] = '1';
            $settings['nummobile'] = '1';
            
            // Posts
            $settings['layout'] = 'half';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );
            
            // Load More Button
            $settings['loadmore'] = 'off';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_text_header_options( $settings );
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'widget' ) {
            // Feed Layout
            $settings['feedlayout'] = 'list';
            $settings['num'] = '3';
            $settings['nummobile'] = '3';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );
            $settings['mediaposition'] = 'above';
			$settings['include'] = 'text,desc,sharedlinks,date,media,medialink,eventtitle,eventdetails,social,link,likebox';
            
            // Load More Button
            $settings['loadmore'] = 'off';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_visual_header_options( $settings );
            $settings['headercoverheight'] = '200';
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        return $settings;
    }
    
    /**
     * Get feedtemplats settings for videos or albums feedtypes
     * 
     * @since 4.2.0
     */
    public static function get_videos_feedtemplate_settings( $settings ) {
        if ( $settings['feedtemplate'] == 'default' ) {
            // Feed Layout
            $settings['feedlayout'] = 'grid';
            $settings['num'] = '6';
            $settings['nummobile'] = '6';
            $settings['cols'] = '3';
            $settings['colstablet'] = '3';
            $settings['colsmobile'] = '2';
            
            // Posts
            $settings['layout'] = 'half';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_regular_options( $settings );

            // Load More Button
            $settings['loadmore'] = 'on';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_visual_header_options( $settings );
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'simple_masonry' ) {
            // Feed Layout
            $settings['feedlayout'] = 'masonry';
            $settings['num'] = '16';
            $settings['nummobile'] = '6';
            $settings['cols'] = '4';
            $settings['colstablet'] = '3';
            $settings['colsmobile'] = '2';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';
            
            // Post Style
            $settings = self::filter_post_style_regular_options( $settings );
            
            // Load More Button
            $settings['loadmore'] = 'on';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings['showheader'] = 'off';
            
            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'simple_carousel' ) {
            // Feed layout
            $settings['feedlayout'] = 'carousel';
            $settings['num'] = '6';
            $settings['nummobile'] = '3';
            $settings['carouselheight'] = 'tallest';
            $settings['carouseldesktop_cols'] = '3';
            $settings['carouselmobile_cols'] = '1';
            $settings['carouselnavigation'] = 'onhover';
            $settings['carouselpagination'] = 'true';
            $settings['carouselautoplay'] = 'true';
            $settings['carouselinterval'] = '5000';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_regular_options( $settings );
            
            // Load More
            $settings['loadmore'] = 'on';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_text_header_options( $settings );
            
            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        // This works as 'Large Grid' for Albums/Videos feed type
        if ( $settings['feedtemplate'] == 'simple_cards' ) {
            // Feed Layout
            $settings['feedlayout'] = 'grid';
            $settings['num'] = '24';
            $settings['nummobile'] = '9';
            $settings['cols'] = '6';
            $settings['colstablet'] = '4';
            $settings['colsmobile'] = '3';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_regular_options( $settings );
            
            // Load More Button
            $settings['loadmore'] = 'on';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_visual_header_options( $settings );
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'showcase_carousel' ) {
            // Feed layout
            $settings = self::filter_default_showcase_carousel_options( $settings );

            // Posts
            $settings['layout'] = 'half';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );
            
            // Load More
            $settings['loadmore'] = 'off';

            // Likebox
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_text_header_options( $settings );

            // Lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'latest_post' ) {
            // Feed Layout
            $settings['feedlayout'] = 'list';
            $settings['num'] = '1';
            $settings['nummobile'] = '1';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );
            
            // Load More Button
            $settings['loadmore'] = 'off';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings['showheader'] = 'off';
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        if ( $settings['feedtemplate'] == 'widget' ) {
            // Feed Layout
            $settings['feedlayout'] = 'list';
            $settings['num'] = '3';
            $settings['nummobile'] = '3';
            
            // Posts
            $settings['layout'] = 'full';
            $settings['enablenarrow'] = 'on';

            // Post Style
            $settings = self::filter_post_style_options( $settings );
            $settings['mediaposition'] = 'above';
			$settings['include'] = 'text,desc,sharedlinks,date,media,medialink,eventtitle,eventdetails,social,link,likebox';
            
            // Load More Button
            $settings['loadmore'] = 'off';

            // Like Box
            $settings['showlikebox'] = 'off';
            
            // Header
            $settings = self::filter_visual_header_options( $settings );
            $settings['headercoverheight'] = '200';
            
            // lightbox
            $settings['disablelightbox'] = 'off';
        }

        return $settings;
    }

    /**
     * Filter default showcase carousel options 
     * 
     * @since 4.2
     */
    public static function filter_default_showcase_carousel_options( $settings ) {
        $settings['feedlayout']           = 'carousel';
        $settings['num']                  = '6';
        $settings['nummobile']            = '3';
        $settings['carouselheight']       = 'tallest';
        $settings['carouseldesktop_cols'] = '1';
        $settings['carouselmobile_cols']  = '1';
        $settings['carouselnavigation']   = 'onhover';
        $settings['carouselpagination']   = 'true';
        $settings['carouselautoplay']     = 'true';
        $settings['carouselinterval']     = '5000';

        return $settings;
    }

    /**
     * Filter post style options
     * 
     * @since 4.2
     */
    public static function filter_post_style_options( $settings ) {
        $settings['poststyle']      = 'boxed';
        $settings['postbgcolor']    = '#FFFFFF';
        $settings['postcorners']    = '2';
        $settings['boxshadow']      = 'on';

        return $settings;
    }

    /**
     * Filter regular post style options
     * 
     * @since 4.2
     */
    public static function filter_post_style_regular_options( $settings ) {
        $settings['poststyle'] = 'regular';
        $settings['sepcolor'] = '#ddd';
        $settings['sepsize'] = '1';

        return $settings;
    }

    /**
     * Filter visual header options
     * 
     * @since 4.2
     */
    public static function filter_visual_header_options( $settings ) {
        $settings['showheader']         = 'on';
        $settings['headertype']         = 'visual';
        $settings['headercoverheight']  = '300';
        $settings['headername']         = 'on';
        $settings['headerbio']          = 'on';
        $settings['headeroutside']      = 'off';

        return $settings;
    }

    /**
     * Filter visual header options
     * 
     * @since 4.2
     */
    public static function filter_text_header_options( $settings ) {
        $settings['showheader']         = 'on';
        $settings['headertype']         = 'text';
        $settings['headericonenabled']  = 'off';
        $settings['headertext']         = 'Find us on Facebook';
        $settings['headericonsize']     = '42';

        return $settings;
    }
}