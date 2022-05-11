<?php

/**
 * The Settings Page
 *
 * @since 4.0
 */

namespace CustomFacebookFeed\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use CustomFacebookFeed\CFF_View;
use CustomFacebookFeed\CFF_Response;

class CFF_Extensions {
    /**
	 * Admin menu page slug.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	const SLUG = 'cff-extensions-manager';

	/**
	 * Initializing the class
	 *
	 * @since 4.0
	 */
	function __construct(){
		$this->init();
	}

    /**
	 * Determining if the user is viewing the our page, if so, party on.
	 *
	 * @since 4.0
	 */
	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', [ $this, 'register_menu' ] );
	}

	/**
	 * Register Menu.
	 *
	 * @since 4.0
	 */
	function register_menu() {
        $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
        $cap = apply_filters( 'cff_settings_pages_capability', $cap );

       $extensions_manager = add_submenu_page(
           'cff-top',
           __( 'Extensions', 'custom-facebook-feed' ),
           __( 'Extensions', 'custom-facebook-feed' ),
           $cap,
           self::SLUG,
           [$this, 'extensions_manager'],
           3
       );
       add_action( 'load-' . $extensions_manager, [$this,'extensions_manager_enqueue_assets']);
   }

   	/**
	 * Enqueue Extension CSS & Script.
	 *
	 * Loads only for Extension page
	 *
	 * @since 4.0
	 */
    public function extensions_manager_enqueue_assets(){
        if( ! get_current_screen() ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! 'facebook-feed_page_cff-extensions-manager' === $screen->id ) {
            return;
		}

		wp_enqueue_style(
			'extensions-style',
			CFF_PLUGIN_URL . 'admin/assets/css/extensions.css',
			false,
			CFFVER
		);

		wp_enqueue_script(
			'vue-main',
			'https://cdn.jsdelivr.net/npm/vue@2.6.12',
			null,
			'2.6.12',
			true
		);

		wp_enqueue_script(
			'extensions-app',
			CFF_PLUGIN_URL.'admin/assets/js/extensions.js',
			null,
			CFFVER,
			true
		);

		$cff_extensions = $this->page_data();

        wp_localize_script(
			'extensions-app',
			'cff_extensions',
			$cff_extensions
		);
    }

    /**
     * Page Data to use in front end
     * 
     * @since 4.0
     * 
     * @return array
     */
    public function page_data() {
        // get the WordPress's core list of installed plugins
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $license_key = null;
		if ( get_option('cff_license_key') ) {
			$license_key = get_option('cff_license_key');
		}
        
        $installed_plugins = get_plugins();

        $cff_ext = is_plugin_active( 'cff-extensions/cff-extensions.php' );
        $cff_ext_options = get_option('cff_extensions_status');

        // define necessary variables
        $carosel_installed = false;
        $album_installed = false;
        $multifeed_installed = false;
        $reviews_installed = false;
        $date_range_installed = false;
        $featured_posts_installed = false;

        // check whether the extensions plugin installed or not
        if ( $cff_ext ) {
            $carosel_installed = true;
            $album_installed = true;
            $multifeed_installed = true;
            $reviews_installed = true;
            $date_range_installed = true;
            $featured_posts_installed = true;
        } else {
            if ( isset( $installed_plugins['cff-carousel/cff-carousel.php'] ) ) {
                $carosel_installed = true;
            }
            if ( isset( $installed_plugins['cff-album/cff-album.php'] ) ) {
                $album_installed = true;
            }
            if ( isset( $installed_plugins['cff-multifeed/cff-multifeed.php'] ) ) {
                $multifeed_installed = true;
            }
            if ( isset( $installed_plugins['cff-reviews/cff-reviews.php'] ) ) {
                $reviews_installed = true;
            }
            if ( isset( $installed_plugins['cff-date-range/cff-date-range.php'] ) ) {
                $date_range_installed = true;
            }
            if ( isset( $installed_plugins['cff-featured-post/cff-featured-post.php'] ) ) {
                $featured_posts_installed = true;
            }
        }

        // check whether the pro or free plugins are installed
        $is_facebook_installed = false;
        $facebook_plugin = 'custom-facebook-feed/custom-facebook-feed.php';
        if ( isset( $installed_plugins['custom-facebook-feed-pro/custom-facebook-feed.php'] ) ) {
            $is_facebook_installed = true;
            $facebook_plugin = 'custom-facebook-feed-pro/custom-facebook-feed.php';
        } else if ( isset( $installed_plugins['custom-facebook-feed/custom-facebook-feed.php'] ) ) {
            $is_facebook_installed = true;
        }

        $is_instagram_installed = false;
        $instagram_plugin = 'instagram-feed/instagram-feed.php';
        if ( isset( $installed_plugins['instagram-feed-pro/instagram-feed.php'] ) ) {
            $is_instagram_installed = true;
            $instagram_plugin = 'instagram-feed-pro/instagram-feed.php';
        } else if ( isset( $installed_plugins['instagram-feed/instagram-feed.php'] ) ) {
            $is_instagram_installed = true;
        }

        $is_twitter_installed = false;
        $twitter_plugin = 'custom-twitter-feeds/custom-twitter-feed.php';
        if ( isset( $installed_plugins['custom-twitter-feeds-pro/custom-twitter-feed.php'] ) ) {
            $is_twitter_installed = true;
            $twitter_plugin = 'custom-twitter-feeds-pro/custom-twitter-feed.php';
        } else if ( isset( $installed_plugins['custom-twitter-feeds/custom-twitter-feed.php'] ) ) {
            $is_twitter_installed = true;
        }

        $is_youtube_installed = false;
        $youtube_plugin = 'feeds-for-youtube/youtube-feed.php';
        if ( isset( $installed_plugins['youtube-feed-pro/youtube-feed.php'] ) ) {
            $is_youtube_installed = true;
            $youtube_plugin = 'youtube-feed-pro/youtube-feed.php';
        } else if ( isset( $installed_plugins['feeds-for-youtube/youtube-feed.php'] ) ) {
            $is_youtube_installed = true;
        }

        $return = array(
			'admin_url' 		=> admin_url(),
			'ajax_handler'		=> admin_url( 'admin-ajax.php' ),
			'nonce'		        =>  wp_create_nonce( 'cff-admin' ),
            'supportPageUrl'    => admin_url( 'admin.php?page=cff-support' ),
            'extentions_bundle' => $cff_ext,
			'links'				=> \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_links_with_utm(),
			'socialWallLinks'   => \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_social_wall_links(),
			'socialWallActivated' => is_plugin_active( 'social-wall/social-wall.php' ),
			'genericText'       => array(
				'help' => __( 'Help', 'custom-facebook-feed' ),
				'title' => __( 'Extensions', 'custom-facebook-feed' ),
				'title2' => __( 'Our Plugins for other Social Platforms', 'custom-facebook-feed' ),
				'description' => __( 'Extend the functionality of your Facebook feed with these extensions', 'custom-facebook-feed' ),
				'description2' => __( 'We’re more than just a Facebook plugin! Check out our other plugins and add more content to your site.', 'custom-facebook-feed' ),
            ),
            'extensions_info'    => array(
                'carousel'  => array(
                    'plugin' => $cff_ext ? 'cff_extensions_carousel_active' : 'cff-carousel/cff-carousel.php',
                    'title' => __( 'Carousel', 'custom-facebook-feed' ),
                    'description' => __( 'Adds the ability to display your Facebook content in a sliding carousel.', 'custom-facebook-feed' ),
                    'icon' => '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect y="11" width="10" height="24" rx="1" fill="#BFE8FF"/><rect x="12" y="11" width="24" height="24" rx="1" fill="#0096CC"/><rect x="38" y="11" width="10" height="24" rx="1" fill="#BFE8FF"/></svg>',
                    'permalink' => 'http://smashballoon.com/extensions/carousel/?utm_campaign=facebook-pro&utm_source=extensions&utm_medium=carousel',
                    'installed' => $carosel_installed,
                    'activated' => $cff_ext ? 
                        isset( $cff_ext_options['cff_extensions_carousel_active'] ) && $cff_ext_options['cff_extensions_carousel_active'] == true :
                        is_plugin_active('cff-carousel/cff-carousel.php'),
                ),
                'album'  => array(
                    'plugin' => $cff_ext ? 'cff_extensions_album_active' : 'cff-album/cff-album.php',
                    'title' => __( 'Album', 'custom-facebook-feed' ),
                    'description' => __( 'Adds the ability to embed a specific Facebook album and display its photos.', 'custom-facebook-feed' ),
                    'icon' => '<svg width="49" height="48" viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="17.9328" y="9.09863" width="24" height="24" rx="1" transform="rotate(10 17.9328 9.09863)" fill="#BFE8FF"/><rect x="9.66666" y="11" width="24" height="24" rx="1" fill="#0096CC"/><path fill-rule="evenodd" clip-rule="evenodd" d="M30.6666 29L24.6666 21L19.0829 27.3814L16.6667 24L12.6667 29H17.6666L17.6666 29H30.6666Z" fill="white"/><circle cx="17.6667" cy="19" r="2" fill="white"/></svg>',
                    'permalink' => 'http://smashballoon.com/extensions/album/?utm_campaign=facebook-pro&utm_source=extensions&utm_medium=album',
                    'installed' => $album_installed,
                    'activated' => $cff_ext ? 
                        isset( $cff_ext_options['cff_extensions_album_active'] ) && $cff_ext_options['cff_extensions_album_active'] == true : 
                        is_plugin_active('cff-album/cff-album.php'),
                ),
                'multifeed'  => array(
                    'plugin' => $cff_ext ? 'cff_extensions_multifeed_active' : 'cff-multifeed/cff-multifeed.php',
                    'title' => __( 'Multifeed', 'custom-facebook-feed' ),
                    'description' => __( 'Adds the ability to aggregate posts from multiple Facebook pages into one feed.', 'custom-facebook-feed' ),
                    'icon' => '<svg width="49" height="48" viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="10.3826" y="11.1331" width="12" height="12" rx="1" transform="rotate(-10 10.3826 11.1331)" fill="#0096CC"/><rect x="12.3333" y="24" width="12" height="12" rx="1" fill="#BFE8FF"/><rect x="26.3333" y="10" width="12" height="12" rx="1" fill="#BFE8FF"/><rect x="26.3333" y="24" width="12" height="12" rx="1" fill="#BFE8FF"/></svg>',
                    'permalink' => 'http://smashballoon.com/extensions/multifeed/?utm_campaign=facebook-pro&utm_source=extensions&utm_medium=multifeed',
                    'installed' => $multifeed_installed,
                    'activated' => $cff_ext ? 
                        isset( $cff_ext_options['cff_extensions_multifeed_active'] ) && $cff_ext_options['cff_extensions_multifeed_active'] == true : 
                        is_plugin_active('cff-multifeed/cff-multifeed.php'), 
                ),
                'reviews'  => array(
                    'plugin' => $cff_ext ? 'cff_extensions_reviews_active' : 'cff-reviews/cff-reviews.php',
                    'title' => __( 'Reviews', 'custom-facebook-feed' ),
                    'description' => __( 'Adds the ability to display reviews from your Facebook page.', 'custom-facebook-feed' ),
                    'icon' => '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 8C5 7.44772 5.44772 7 6 7H42C42.5523 7 43 7.44772 43 8V30C43 30.5523 42.5523 31 42 31H29.4142C29.149 31 28.8946 31.1054 28.7071 31.2929L23.7385 36.2615C23.3359 36.6641 22.679 36.6498 22.2943 36.2301L17.7973 31.3243C17.6078 31.1176 17.3404 31 17.0601 31H6C5.44772 31 5 30.5523 5 30V8Z" fill="#BFE8FF"/><path d="M23.319 12.4765C23.587 11.8953 24.413 11.8953 24.681 12.4765L26.0813 15.5122C26.1905 15.749 26.415 15.9121 26.674 15.9428L29.9938 16.3364C30.6293 16.4118 30.8845 17.1973 30.4147 17.6318L27.9603 19.9016C27.7688 20.0787 27.683 20.3426 27.7339 20.5984L28.3854 23.8773C28.5101 24.505 27.8419 24.9905 27.2834 24.6779L24.3663 23.0451C24.1387 22.9177 23.8613 22.9177 23.6337 23.0451L20.7166 24.6779C20.1581 24.9905 19.4899 24.505 19.6146 23.8773L20.2661 20.5984C20.317 20.3426 20.2312 20.0787 20.0397 19.9016L17.5853 17.6318C17.1155 17.1973 17.3707 16.4118 18.0062 16.3364L21.326 15.9428C21.585 15.9121 21.8095 15.749 21.9187 15.5122L23.319 12.4765Z" fill="#0096CC"/></svg>',
                    'permalink' => 'http://smashballoon.com/extensions/reviews/?utm_campaign=facebook-pro&utm_source=extensions&utm_medium=reviews',
                    'installed' => $reviews_installed,
                    'activated' => $cff_ext ? 
                        isset( $cff_ext_options['cff_extensions_reviews_active'] ) && $cff_ext_options['cff_extensions_reviews_active'] == true : 
                        is_plugin_active('cff-reviews/cff-reviews.php'),
                ),
                'date_range'  => array(
                    'plugin' => $cff_ext ? 'cff_extensions_date_range_active' : 'cff-date-range/cff-date-range.php',
                    'title' => __( 'Date Range', 'custom-facebook-feed' ),
                    'description' => __( 'Adds the ability to display posts from a specific date range.', 'custom-facebook-feed' ),
                    'icon' => '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M14 7.5C14 7.22386 14.2239 7 14.5 7H16.5C16.7761 7 17 7.22386 17 7.5V9H30V7.5C30 7.22386 30.2239 7 30.5 7H32.5C32.7761 7 33 7.22386 33 7.5V9H37C38.1046 9 39 9.89543 39 11V17H9V11C9 9.89543 9.89543 9 11 9H14V7.5Z" fill="#0096CC"/><path d="M9 17H39V36C39 37.1046 38.1046 38 37 38H11C9.89543 38 9 37.1046 9 36V17Z" fill="#BFE8FF"/></svg>',
                    'permalink' => 'http://smashballoon.com/extensions/date-range/?utm_campaign=facebook-pro&utm_source=extensions&utm_medium=date-range',
                    'installed' => $date_range_installed,
                    'activated' => $cff_ext ? 
                        isset( $cff_ext_options['cff_extensions_date_range_active'] ) && $cff_ext_options['cff_extensions_date_range_active'] == true : 
                        is_plugin_active('cff-date-range/cff-date-range.php'),
                ),
                'featured_posts'  => array(
                    'plugin' => $cff_ext ? 'cff_extensions_featured_post_active' : 'cff-featured-post/cff-featured-post.php',
                    'title' => __( 'Featured Posts', 'custom-facebook-feed' ),
                    'description' => __( 'Adds the ability to display a specific post or event based on its ID.', 'custom-facebook-feed' ),
                    'icon' => '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="9.11111" y="7" width="31.7778" height="33" rx="1.22222" fill="#BFE8FF"/><path d="M9.11111 8.22222C9.11111 7.54721 9.65832 7 10.3333 7H39.6667C40.3417 7 40.8889 7.54721 40.8889 8.22222V30.2222H9.11111V8.22222Z" fill="#0096CC"/><path fill-rule="evenodd" clip-rule="evenodd" d="M36 25.3334L28.6667 15.5557L21.8421 23.3552L18.8889 19.2223L14 25.3334H20.1111H23.2557H36Z" fill="#F7FDFF"/><circle cx="20.1111" cy="14.3334" r="2.44444" fill="#F7FDFF"/><rect x="12.7778" y="33.8889" width="20.7778" height="2" fill="#0096CC"/></svg>',
                    'permalink' => 'http://smashballoon.com/extensions/featured-post/?utm_campaign=facebook-pro&utm_source=extensions&utm_medium=featured-post',
                    'installed' => $featured_posts_installed,
                    'activated' => $cff_ext ? 
                        isset( $cff_ext_options['cff_extensions_featured_post_active'] ) && $cff_ext_options['cff_extensions_featured_post_active'] == true : 
                        is_plugin_active('cff-featured-post/cff-featured-post.php'),
                )
            ),
            'pluginsInfo'      => array(
                'facebook'  => array(
                    'plugin' => 'custom-facebook-feed/custom-facebook-feed.php',
                    'title' => __( 'Custom Facebook Feed', 'custom-facebook-feed' ),
                    'description' => __( 'Add Facebook posts from your timeline, albums and much more.', 'custom-facebook-feed' ),
                    'icon' => CFF_PLUGIN_URL . 'admin/assets/img/fb-icon.svg',
                    'installed' => isset( $installed_plugins['custom-facebook-feed-pro/custom-facebook-feed.php'] ) ? true : false,
                    'activated' => is_plugin_active('custom-facebook-feed-pro/custom-facebook-feed.php'),
                ),
                'instagram'  => array(
                    'plugin' => $instagram_plugin,
                    'download_plugin' => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
                    'dashboard_permalink' => admin_url( 'admin.php?page=sb-instagram-feed' ),
                    'title' => __( 'Instagram Feed', 'custom-facebook-feed' ),
                    'description' => __( 'A quick and elegant way to add your Instagram posts to your website. ', 'custom-facebook-feed' ),
                    'icon' => CFF_PLUGIN_URL . 'admin/assets/img/insta-icon.svg',
                    'installed' => $is_instagram_installed,
                    'activated' => is_plugin_active( $instagram_plugin ),
                ),
                'twitter'  => array(
                    'plugin' => $twitter_plugin,
                    'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
                    'dashboard_permalink' => admin_url( 'admin.php?page=custom-twitter-feeds' ),
                    'title' => __( 'Custom Twitter Feeds', 'custom-facebook-feed' ),
                    'description' => __( 'A customizable way to display tweets from your Twitter account. ', 'custom-facebook-feed' ),
                    'icon' => CFF_PLUGIN_URL . 'admin/assets/img/twitter-icon.svg',
                    'installed' => $is_twitter_installed,
                    'activated' => is_plugin_active( $twitter_plugin ),
                ),
                'youtube'  => array(
                    'plugin' =>  $youtube_plugin,
                    'download_plugin' => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
                    'dashboard_permalink' => admin_url( 'admin.php?page=youtube-feed' ),
                    'title' => __( 'Feeds for YouTube', 'custom-facebook-feed' ),
                    'description' => __( 'A simple yet powerful way to display videos from YouTube. ', 'custom-facebook-feed' ),
                    'icon' => CFF_PLUGIN_URL . 'admin/assets/img/youtube-icon.svg',
                    'installed' => $is_youtube_installed,
                    'activated' => is_plugin_active( $youtube_plugin ),
                )
            ),
            'social_wall'  => array(
                'plugin' => 'social-wall/social-wall.php',
                'title' => __( 'Social Wall', 'custom-facebook-feed' ),
                'description' => __( 'Get all our social plugins and use them in combination', 'custom-facebook-feed' ),
                'graphic' => CFF_PLUGIN_URL . 'admin/assets/img/social-wall-graphic.png',
                'permalink' => sprintf('https://smashballoon.com/social-wall/demo?license_key=%s&upgrade=true&utm_campaign=facebook-pro&utm_source=extensions&utm_medium=social-wall', $license_key),
                'installed' => isset( $installed_plugins['social-wall/social-wall.php'] ) ? true : false,
                'activated' => is_plugin_active('social-wall/social-wall.php'),
            ),
			'socialWallLinks'   => \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_social_wall_links(),
			'buttons'          => array(
                'add' => __( 'Add', 'custom-facebook-feed' ),
                'viewDemo' => __( 'View Demo', 'custom-facebook-feed' ),
                'install' => __( 'Install', 'custom-facebook-feed' ),
                'installed' => __( 'Installed', 'custom-facebook-feed' ),
                'activate' => __( 'Activate', 'custom-facebook-feed' ),
                'deactivate' => __( 'Deactivate', 'custom-facebook-feed' ),
                'open' => __( 'Open', 'custom-facebook-feed' ),
            ),
            'icons' => array(
                'plusIcon' => '<svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.0832 6.83317H7.08317V11.8332H5.4165V6.83317H0.416504V5.1665H5.4165V0.166504H7.08317V5.1665H12.0832V6.83317Z" fill="white"/></svg>',
                'loaderSVG' => '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/></path></svg>',
                'checkmarkSVG' => '<svg width="13" height="10" viewBox="0 0 13 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.13112 6.88917L11.4951 0.525204L12.9093 1.93942L5.13112 9.71759L0.888482 5.47495L2.3027 4.06074L5.13112 6.88917Z" fill="#8C8F9A"/></svg>',
                'link'  => '<svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.333374 9.22668L7.39338 2.16668H3.00004V0.833344H9.66671V7.50001H8.33338V3.10668L1.27337 10.1667L0.333374 9.22668Z" fill="#141B38"/></svg>'
            ),
        );

        return $return;
    }

   	/**
	 * Extensions Manager Page View Template
	 *
	 * @since 4.0
	 */
	public function extensions_manager(){
		return CFF_View::render( 'extensions.index' );
	}
}