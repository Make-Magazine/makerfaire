<?php
/**
 * Core class for Instagram feed generation in Easy Social Share Buttons. The class
 * also reference the shortcode and its data
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @version 2.0
 * @since 7.9
 */
class ESSBInstagramFeed {
	
	private static $_instance;
	
	private $resources = false;
	private $optimized = false;
	private $cache_ttl = 6;
	
	private $api_media_url = 'https://graph.instagram.com/me/media';
	private $api_me_url = 'https://graph.instagram.com/me';
	private $api_refresh_token = 'https://graph.instagram.com/refresh_access_token';
	
	private static $option_name = 'essb_instagram_accounts';
	
	private static $transient_prefix = 'essb-ig-';
	private static $transient_prefix_long = 'essb-long-ig-';
	
	/**
	 * Store loaded accounts
	 * @var array
	 */
	private static $cached_accounts = array();
	
	/**
	 * Get static instance of class
	 */
	public static function instance() {
		if (! (self::$_instance instanceof self)) {
			self::$_instance = new self ();
		}
	
		return self::$_instance;
	}
	
	/**
	 * Cloning disabled
	 */
	public function __clone() {
	}
	
	/**
	 * Serialization disabled
	 */
	public function __sleep() {
	}
	
	/**
	 * De-serialization disabled
	 */
	public function __wakeup() {
	}
	
	public function __construct() {
		
		$this->optimized = true;		
		
		// @since 7.0.1 
		// Deactivate feed shortcode if Smashing Baloon Instagram Feed is working
		if (!defined('SBIVER')) {
            add_shortcode('instagram-feed', array($this, 'generate_shortcode'));
		}
		else {
		    add_shortcode('essb-instagram-feed', array($this, 'generate_shortcode'));
		}
						
		/** 
		 * Check if need to load resources
		 */
		if (essb_option_bool_value('instagram_styles')) {
			if (function_exists ( 'essb_resource_builder' )) {
				essb_resource_builder ()->add_static_resource ( ESSB3_PLUGIN_URL . '/lib/modules/instagram-feed/assets/essb-instagramfeed'.($this->optimized ? '.min': '').'.css', 'essb-instagram-feed', 'css' );
				essb_resource_builder ()->add_static_resource ( ESSB3_PLUGIN_URL . '/lib/modules/instagram-feed/assets/essb-instagramfeed'.($this->optimized ? '.min': '').'.js', 'essb-instagram-feed', 'js' );
				$this->resources = true;
			}
		}
		
		/**
		 * Setting user cache expiration if present
		 */
		$user_cache_ttl = essb_sanitize_option_value('instagram_cache');
		if ($user_cache_ttl != '' && intval($user_cache_ttl) > 0) {
			$this->cache_ttl = intval($user_cache_ttl);
		}
		
		if (essb_option_bool_value('instagramfeed_content')) {
			add_filter ( 'the_content', array($this, 'draw_widget_below_content'), 101);
		}
		
		if (essb_option_bool_value('instagramfeed_popup')) {
			add_action ( 'wp_footer', array($this, 'draw_widget_popup'), 101);
		}		
	}	
	
	/**
	 * Validate if the feed should be included in the content
	 * 
	 * @return boolean
	 */
	public function can_add_automatic_content_widget() {
		if (is_admin () || is_search() || is_feed()) {
			return false;
		}
		
		$posttypes = essb_option_value('instagramfeed_content_types');
		if (!is_array($posttypes)) {
			return false;
		}
				
		if (!is_singular($posttypes)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Validate if the feed should be added as a pop-up
	 * @return boolean
	 */
	public function can_add_automatic_popup_widget() {
		if (is_admin () || is_search() || is_feed()) {
			return false;
		}
		
		$posttypes = essb_option_value('instagramfeed_popup_types');
		if (!is_array($posttypes)) {
			return false;
		}
		
		if (!is_singular($posttypes)) {
			return false;
		}
		
		return true;
	}
	
	public function get_settings($values = array()) {
		$r = array();
		
		$r['username'] = array('type' => 'select', 'title' => esc_html__('Username', 'essb'), 'options' => self::get_account_list());
		$r['type'] = array('type' => 'select', 'title' => esc_html__('Display type', 'essb'), 
				'options' => array(
						'1col' => esc_html__('1 Column', 'essb'), 
						'2cols' => esc_html__('2 Columns', 'essb'), 
						'3cols' => esc_html__('3 Columns', 'essb'), 
						'4cols' => esc_html__('4 Columns', 'essb'), 
						'5cols' => esc_html__('5 Columns', 'essb'), 
						'carousel' => esc_html__('Carousel', 'essb'),
						'carousel-1' => esc_html__('Carousel with 1 image (slider)', 'essb'),
						'carousel-2' => esc_html__('Carousel with 2 images', 'essb'),
						'row' => esc_html__('Row', 'essb')));
		$r['show'] = array('type' => 'text', 'title' => esc_html__('Images to show', 'essb'), 'description' => esc_html__('Enter number between 1 and 15', 'essb'));
		$r['profile'] = array('type' => 'select', 'title' => esc_html__('Show profile information', 'essb'), 'description' => esc_html__('Only when username is provided', 'essb'), 
				'options' => array(
					'false' => esc_html__('No', 'essb'),
					'true' => esc_html__('Yes', 'essb')));
		$r['followbtn'] = array('type' => 'select', 'title' => esc_html__('Show profile follow button', 'essb'), 'description' => esc_html__('Only when username is provided and profile information is visible', 'essb'), 
				'options' => array(
					'false' => esc_html__('No', 'essb'),
					'true' => esc_html__('Yes', 'essb')));
		$r['profile_size'] = array('type' => 'select', 'title' => esc_html__('Profile size', 'essb'), 'description' => esc_html__('Only when profile is active', 'essb'),
				'options' => array(
						'normal' => esc_html__('Normal', 'essb'),
						'small' => esc_html__('Small', 'essb')));
		$r['space'] = array('type' => 'select', 'title' => esc_html__('Space between images', 'essb'),
				'options' => array(
						'' => esc_html__('Without space', 'essb'),
						'small' => esc_html__('Small', 'essb'),
						'medium' => esc_html__('Medium', 'essb'),
						'large' => esc_html__('Large', 'essb'),
						'xlarge' => esc_html__('Extra Large', 'essb'),
						'xxlarge' => esc_html__('Extra Extra Large', 'essb')));	
		$r['masonry'] = array('type' => 'select', 'title' => esc_html__('Masonry', 'essb'), 'description' => esc_html__('Only when columns display is used', 'essb'),
				'options' => array(
						'false' => esc_html__('No', 'essb'),
						'true' => esc_html__('Yes', 'essb')));		
		
		return $r;
	}
	
	public function generate_shortcode($atts = array()) {
		$username = isset($atts['username']) ? $atts['username'] : '';
		$tag = isset($atts['tag']) ? $atts['tag'] : '';
		
		$type = isset($atts['type']) ? $atts['type'] : '3cols';
		$show = isset($atts['show']) ? $atts['show'] : '';
		$profile = isset($atts['profile']) ? $atts['profile'] : '';
		$follow_button = isset($atts['followbtn']) ? $atts['followbtn'] : '';
		$space = isset($atts['space']) ? $atts['space'] : '';
		$masonry = isset($atts['masonry']) ? $atts['masonry'] : '';
		$profile_size = isset($atts['profile_size']) ? $atts['profile_size'] : '';
		
		$post_info_style = isset($atts['info']) ? $atts['info'] : '';
		if ($post_info_style == '') {
			$post_info_style = essb_sanitize_option_value('instagram_postinfo_style');
		}
		
		if (intval($show) == 0) {
			$show = 12;
		}
		
		/**
		 * Validate shortcode options to prevent errors
		 */
		
		$link_mode = essb_sanitize_option_value('instagram_linkmode');
		
		if ($username != '') {
			$username = '@' . str_replace( '@', '', $username );
		}
		
		if ($tag != '') {
			$username = '#' . str_replace( '#', '', $tag );
		}
		
		$widget = isset($atts['widget']) ? $atts['widget'] : '';
		
		if ($username != '') {
			return $this->draw_instagram($username, $type, $show, $space, $profile == 'true', $follow_button == 'true',
					$link_mode, $post_info_style, '', $masonry == 'true', $widget == 'true', $profile_size);
		}
		else {
			return '';
		}
	}
	
	public function draw_widget_popup() {
		if (!$this->can_add_automatic_popup_widget()) {
			return;
		}
		
		$user = essb_sanitize_option_value('instagramfeed_popup_user');
		$image_count = essb_sanitize_option_value('instagramfeed_popup_images');
		$columns = essb_sanitize_option_value('instagramfeed_popup_columns');
		$profile = 'true';
		$followbtn = 'true';
		$masonry = essb_sanitize_option_value('instagramfeed_popup_masonry');
		$space = essb_sanitize_option_value('instagramfeed_popup_space');
		
		$link_mode = essb_sanitize_option_value('instagram_linkmode');
		$post_info_style = essb_sanitize_option_value('instagram_postinfo_style');
		$profile_size = essb_sanitize_option_value('instagramfeed_popup_profile_size');
		
		if ($user != '') {
			if (substr( $user, 0, 1 ) != '@') {
				$user = '@'.$user;
			}
				
			$instagram_widget = $this->draw_instagram($user, $columns, $image_count, $space,
					$profile == 'true', $followbtn == 'true', $link_mode, $post_info_style, 'large', $masonry == 'true',
					false, $profile_size );
			
			$instagramfeed_popup_delay = essb_sanitize_option_value('instagramfeed_popup_delay');
			$instagramfeed_popup_width = essb_sanitize_option_value('instagramfeed_popup_width');
			$instagramfeed_popup_appear_again = essb_sanitize_option_value('instagramfeed_popup_appear_again');
			
			/**
			 * @since 7.7.3
			 * @var Ambiguous $instagramfeed_popup_disablemobile
			 */
			$instagramfeed_popup_disablemobile = essb_option_bool_value('instagramfeed_popup_disablemobile');
			
			echo '<div class="essb-instagramfeed-popup" data-delay="'.esc_attr($instagramfeed_popup_delay).'" data-width="'.esc_attr($instagramfeed_popup_width).'" data-hidefor="'.esc_attr($instagramfeed_popup_appear_again).'" data-disablemobile="'.esc_attr($instagramfeed_popup_disablemobile).'">';
			echo $instagram_widget;
			echo '</div>';
			echo '<div class="essb-instagramfeed-popup-overlay"></div>';
		}
	}
	
	public function draw_widget_below_content($content) {
		
		if (!$this->can_add_automatic_content_widget()) {
			return $content;
		}
		
		$instagram_widget = '';
		
		/**
		 * Reading settings of the widget
		 */
		$user = essb_sanitize_option_value('instagramfeed_content_user');
		$image_count = essb_sanitize_option_value('instagramfeed_content_images');
		$columns = essb_sanitize_option_value('instagramfeed_content_columns');
		$profile = essb_sanitize_option_value('instagramfeed_content_profile');
		$followbtn = essb_sanitize_option_value('instagramfeed_content_followbtn');
		$masonry = essb_sanitize_option_value('instagramfeed_content_masonry');
		$space = essb_sanitize_option_value('instagramfeed_content_space');
		
		$link_mode = essb_sanitize_option_value('instagram_linkmode');
		$post_info_style = essb_sanitize_option_value('instagram_postinfo_style');
		$profile_size = essb_sanitize_option_value('instagramfeed_content_profile_size');
		
		if ($user != '') {
			if (substr( $user, 0, 1 ) != '@') {
				$user = '@'.$user;
			}
			
			$instagram_widget = $this->draw_instagram($user, $columns, $image_count, $space,
					$profile == 'true', $followbtn == 'true', $link_mode, $post_info_style, 'large', $masonry == 'true',
					false, $profile_size );
		}
		
		return $content.$instagram_widget;
	}	
	
	public function draw_instagram($username_tag = '', $type = '3cols', $show = 12, $space = '', $profile = false, 
			$follow_button = false, $link_mode = 'direct', $post_data = '', $image_size = 'original', $masonry = false,
			$widget = false, $profile_size = '') {
	    
        $username_tag = str_replace('@', '', $username_tag);

        $data = $this->scrape_instagram($username_tag);		
        
        $account_data = self::get_username_data($username_tag); 
		
		// Image loading action
		$instagram_open_as = essb_sanitize_option_value('instagram_open_as');
		
		$parent_classes = array();
		$parent_classes[] = 'essb-instagramfeed';
		$parent_classes[] = 'essb-instagramfeed-'.esc_attr($type);
		if ($profile) { 
			$parent_classes[] = 'essb-instagramfeed-withprofile';
		}
		if ($follow_button) {
			$parent_classes[] = 'essb-instagramfeed-withfollowbtn';
		}
		
		if ($type != 'row') {
			$parent_classes[] = 'essb-instagramfeed-responsive';
		}
		
		if ($space != '') {
			$parent_classes[] = 'essb-instagramfeed-space-'.esc_attr($space);
		}
		
		if ($instagram_open_as != 'link') {
			$parent_classes[] = 'essb-instagramfeed-lightbox';
		}
		
		if ($masonry) {
			$parent_classes[] = 'essb-instagramfeed-masonry';
		}
		
		if ($post_data == 'false') {
			$parent_classes[] = 'essb-instagramfeed-nohover';
		}
		
		if ($widget) {
			$parent_classes[] = 'essb-instagramfeed-widget';
		}
		
		if ($profile_size != '') {
			$parent_classes[] = 'essb-instagramfeed-profile-'.esc_attr($profile_size);
		}
		
		if (essb_option_bool_value('instagram_deactivate_mobile')) {
		    $parent_classes[] = 'essb-instagramfeed-mobilehidden';
		}
		
		$output = '';
		
		//
		if (empty($update_source)) {
            $output = '<div class="'.esc_attr(join(' ', $parent_classes)).'">';
		}
		/**
		 * Profile card if shown
		 */
		if ($profile) {
			$follow_text = esc_html__('Follow', 'essb');
			$followers_text = esc_html__('followers', 'essb');
			
			/**
			 * @since 7.8 Translate from plugin settings
			 */
			$translate_follow_text = essb_sanitize_option_value('instagram_follow_button_text');
			$translate_followers_text = essb_sanitize_option_value('instagram_followers_text');
			
			if (!empty($translate_follow_text)) {
			    $follow_text = $translate_follow_text;
			}
			
			if (!empty($translate_followers_text)) {
			    $followers_text = $translate_followers_text;
			}
			
			$output .= '<div class="essb-instagramfeed-profile'.($follow_button && !$widget ? ' essb-instagramfeed-profilefollow': '').'">';
			$image = !empty($account_data['display_pic']) ? $account_data['display_pic'] : '';
			$name = !empty($account_data['display_name']) ? $account_data['display_name'] : $account_data['username'];			
			$bio = !empty($account_data['display_bio']) ? $account_data['display_bio'] : '';
			$followers_value = !empty($account_data['followers']) ? $account_data['followers'] : 0;
			
			$instagram_profile_url = 'https://instagram.com/'.esc_attr(str_replace('@', '', $username_tag ));
			
			if (!empty($image)) {
				$output .= '<div class="essb-instagramfeed-profile-photo">';
				$output .= '<a href="'.esc_url($instagram_profile_url).'" target="_blank" rel="nofollow noreferrer noopener"><img src="'.esc_url($image).'"/></a>';
				$output .= '</div>';
			}
			
			if (!empty($bio) || !empty($name)) {
				$output .= '<div class="essb-instagramfeed-profile-bio">';
				$output .= '<span class="profile-name">'.esc_html(str_replace('@', '', $name )).'</span>';
				if (intval($followers_value) > 0) {
					$output .= '<span class="profile-likes">';
					$output .= '<b>'.essb_kilomega($data['profile']['followers']).'</b> '.$followers_text;
					$output .= '</span>';
				}
				$output .= '<span class="profile-info">'.$bio.'</span>';
				
				if ($follow_button && $widget) {
					$output .= '<div class="essb-instagramfeed-profile-followbtn">';
					$output .= '<a href="'.esc_url($instagram_profile_url).'" target="_blank" rel="nofollow noreferrer noopener">'.$follow_text.'</a>';
					$output .= '</div>';
				}
				
				$output .= '</div>';
			}
			
			if ($follow_button && !$widget) {
				$output .= '<div class="essb-instagramfeed-profile-followbtn">';
				$output .= '<a href="'.esc_url($instagram_profile_url).'" target="_blank" rel="nofollow noreferrer noopener">'.$follow_text.'</a>';
				$output .= '</div>';
			}
			
			$output .= '</div>';
		}
		
		// check what is the best number of possible images
		if (intval($show) > 15) {
			$show = 15;
		}
		
		$output .= '<div class="essb-instagramfeed-images">';
		$count = 1;
		foreach ($data as $image) {
			
			if ($count > $show) {
				continue;
			}
			
			$image_url = !empty($image['image']) ? $image['image'] : '';
			$link_url = !empty($image['permalink']) ? $image['permalink'] : '';
			$type = !empty($image['type']) ? $image['type'] : 'IMAGE';
			$caption = !empty($image['caption']) ? $image['caption'] : '';
			
			$key = mt_rand();
			
			$output .= '<div class="essb-instagramfeed-single essb-instagramfeed-single-'.esc_attr($key).' essb-instagramfeed-single-'.esc_attr($type).'" style="background-image: url('.esc_url($image_url).');">';
			$output .= '<a href="'.esc_url($link_url).'" target="_blank" rel="nofollow noreferrer noopener">';
			$output .= '<div class="essb-instagramfeed-single-image">';
			$output .= '<img src="'.esc_url($image_url).'"/>';
			
			$output .= '</div>';
			
			$output .= '<div class="essb-instagramfeed-single-image-info">';
			$output .= '<div class="essb-instagramfeed-single-image-info-desc">';
			$output .= $this->wrap_tags_and_users($caption);
			$output .= '</div>'; // end desc
			$output .= '</div>'; // end info
			
			$output .= '</a>';
			$output .= '</div>';
			
			$count++;
		}
		
		$output .= '</div>'; // -images
		
		if (empty($update_source)) {
            $output .= '</div>';
		}
		
		if (function_exists ( 'essb_resource_builder' ) && !$this->resources) {
			essb_resource_builder ()->add_static_resource_footer ( ESSB3_PLUGIN_URL . '/lib/modules/instagram-feed/assets/essb-instagramfeed'.($this->optimized ? '.min': '').'.css', 'essb-instagram-feed', 'css' );
			essb_resource_builder ()->add_static_resource_footer ( ESSB3_PLUGIN_URL . '/lib/modules/instagram-feed/assets/essb-instagramfeed'.($this->optimized ? '.min': '').'.js', 'essb-instagram-feed', 'js' );
			$this->resources = true;
	
		}
		
		if ($masonry) {
			wp_enqueue_script('masonry');
		}
		
		return $output;
	}
	
	public function wrap_tags_and_users($text = '') {
		return $text;
	}
	
	
	/**
	 * Scrape data for Instagram account
	 * @param string $username_or_tag
	 * @return mixed|array
	 */
	public function scrape_instagram($username_or_tag = '') {
	    $username = trim( strtolower( $username_or_tag ) );
	    $username = str_replace('@', '', $username);
	    	    
	    if ( false === ( $instagram = get_transient( self::$transient_prefix . sanitize_title_with_dashes( $username ) ) ) ) {
	        	        
	        $url = add_query_arg( array(
	            'fields'       => 'caption, id, media_type, media_url, permalink, thumbnail_url, timestamp, username',
	            'access_token' => self::get_username_token($username),
	        ), $this->api_media_url );
	        
	        
	        // Get the new images if the images are not fetched.
	        $response = wp_remote_get( $url );
	        
	        $data = json_decode( wp_remote_retrieve_body( $response ) );
	        
	        $instagram = array();
	        
	        if (!empty($data->data)) {
	            foreach ($data->data as $one_image) {
	                
	                $instagram[] = array(
	                    'caption' => !empty($one_image->caption) ? $one_image->caption : '',
	                    'type' => !empty($one_image->media_type) ? $one_image->media_type : '',
	                    'image' => !empty($one_image->media_url) ? $one_image->media_url : '',
	                    'permalink' => !empty($one_image->permalink) ? $one_image->permalink : '',
	                    'timestamp' => !empty($one_image->timestamp) ? $one_image->timestamp : '',
	                    'username' => !empty($one_image->username) ? $one_image->username : ''
	                );
	            }
	        }
	        
	        
	        /**
	         * @since 7.8
	         */
	        if (essb_option_bool_value('instagram_extra_cache') && empty( $instagram )) {
	            $cached_instagram = get_transient(self::$transient_prefix_long . sanitize_title_with_dashes( $username ));
	            
	            if (!empty($cached_instagram)) {
	                $instagram = unserialize( base64_decode( $cached_instagram ) );
	            }
	        }
	        
	        // do not set an empty transient - should help catch private or empty accounts. Set a shorter transient in other cases to stop hammering Instagram
	        if ( ! empty( $instagram ) ) {
	            $instagram = base64_encode( serialize( $instagram ) );
	            set_transient( self::$transient_prefix . sanitize_title_with_dashes( $username ), $instagram, $this->cache_ttl * HOUR_IN_SECONDS );
	            
	            /**
	             * @since 7.8 Save permanent cache data
	             */
	            if (essb_option_bool_value('instagram_extra_cache')) {
	                set_transient(self::$transient_prefix_long . sanitize_title_with_dashes( $username ), $instagram, YEAR_IN_SECONDS);
	            }
	        } 
	    }
	    
	    if ( ! empty( $instagram ) ) {
	        return unserialize( base64_decode( $instagram ) );
	    } else {
	        return array();
	    }
	}
		
	/**
	 * Return a blank Instagram array instead of an error
	 * 
	 * @param string $code
	 * @param string $message
	 * @return array[]|string[][]
	 */
	private function blank_instagram_feed($code = '', $message = '') {
	    
	    $output = array();
	    $output['profile'] = array();
	    $output['images'] = array();
	    $output['error'] = array('code' => $code, 'message' => $message);
	    
	    return $output;
	}
	

	/**
	 * Get all saved Instagram accounts
	 * @return array
	 */
	public static function get_accounts() {
	    if (!empty(self::$cached_accounts)) {
	        $r = self::$cached_accounts;
	    }
	    else {
	       $r = get_option(self::$option_name);
	    }
	    
	    if (!$r || !is_array($r)) {
	        $r = array();
	    }
	    
	    if (empty(self::$cached_accounts)) {
	        self::$cached_accounts = $r;
	    }
	    
	    return $r;
	}
	
	/**
	 * Save all Instagram accounts
	 * 
	 * @param array $accounts
	 */
	public static function save_accounts($accounts = array()) {
	    update_option(self::$option_name, $accounts, 'no', 'no');
	    self::$cached_accounts = array();
	}
	
	/**
	 * Remove single Instagram account
	 * @param string $account_id
	 */
	public static function remove_account($account_id = '') {
	    $accounts = self::get_accounts();
	    
	    if (isset($accounts[$account_id])) {
	        
	        /**
	         * Remove stored data before removing the account
	         */
	        $username = !empty($accounts[$account_id]['username']) ? $accounts[$account_id]['username'] : '';
	        if (!empty($username)) {
	            delete_transient(self::$transient_prefix . $username);
	            delete_transient(self::$transient_prefix_long . $username);
	        }
	        
	        unset ($accounts[$account_id]);
	    }
	    
	    self::save_accounts($accounts);
	}
	
	/**
	 * Update token to an existing account
	 * 
	 * @param string $account_id
	 * @param string $token
	 */
	public static function update_token($account_id = '', $token = '') {
	    $accounts = self::get_accounts();
	    
	    if (isset($accounts[$account_id])) {
	        $accounts[$account_id]['token'] = $token;
	    }
	    
	    self::save_accounts($accounts);
	}
	
	/**
	 * Generate a new unique account numeric ID
	 * @return number
	 */
	public static function get_new_account_id() {
	    $accounts = self::get_accounts();
	    $new_design_id = count($accounts) + 1;
	    
	    return uniqid();
	}
	
	/**
	 * Get single account data
	 * @param string $account_id
	 * @return array|mixed
	 */
	public static function get_account($account_id = '') {
	    $accounts = self::get_accounts();
	    $r = array();
	    
	    if (isset($accounts[$account_id])) {
	        $r = $accounts[$account_id];
	    }
	    
	    return $r;
	}
	
	/**
	 * Save single Instagram account data
	 * @param string $account_id
	 * @param array $data
	 */
	public static function save_account($account_id = '', $data = array()) {
	    $accounts = self::get_accounts();
	    $accounts[$account_id] = $data;
	    
	    self::save_accounts($accounts);
	}
	
	/**
	 * Remove all stored Instagram accounts
	 */
	public static function remove_all_accounts() {
	    
	    $accounts = self::get_accounts();
	    
	    foreach ($accounts as $account_id => $account_data) {	        
	        /**
	         * Remove stored data before removing the account
	         */
	        $username = !empty($accounts[$account_id]['username']) ? $accounts[$account_id]['username'] : '';
	        if (!empty($username)) {
	            delete_transient(self::$transient_prefix . $username);
	            delete_transient(self::$transient_prefix_long . $username);
	        }
	    }
	    
	    delete_option(self::$option_name);
	}
	
	
	/**
	 * Generate a list for account selection in the settings
	 * 
	 * @return string[]|unknown[]
	 */
	public static function get_account_list() {
	    $r = array();
	    $accounts = self::get_accounts();
	    
	    foreach ($accounts as $account_id => $account_data) {
	        $username = isset($account_data['username']) ? $account_data['username'] : '';
	        $display_name = isset($account_data['display_name']) ? $account_data['display_name'] : '';
	        
	        $r[$username] = $display_name != '' ? $display_name . ' (' . $username . ')' : $username;
	    }
	    
	    return $r;
	}
	
	/**
	 * Get account details by username
	 * 
	 * @param string $username
	 * @return array|unknown
	 */
	public static function get_username_data($username = '') {
	    $r = array();
	    
	    $accounts = self::get_accounts();
	    
	    foreach ($accounts as $account_id => $account_data) {
	        $current_username = isset($account_data['username']) ? $account_data['username'] : '';
	        
	        if ($current_username == $username) {
	            $r = $account_data;
	        }
	    }
	    
	    return $r;
	}
	
	/**
	 * Get token related to a specific account
	 * @param string $username
	 * @return string
	 */
	public static function get_username_token($username = '') {
	    $account = self::get_username_data($username);
	    
	    return isset($account['token']) ? $account['token'] : '';
	}
	
	/**
	 * Update images for a single username
	 * @param string $username
	 */
	public static function update_images($username = '') {
	    delete_transient(self::$transient_prefix . $username);
	    essb_instagram_feed()->scrape_instagram($username);
	}
}

if (!function_exists('essb_instagram_feed')) {
	function essb_instagram_feed() {
	    return ESSB_Factory_Loader::get('instagram');
	}
}