<?php
/**
 * Core class for Instagram feed generation in Easy Social Share Buttons. The class
 * also reference the shortcode and its data
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @version 1.0
 * @since 7.0
 */
class ESSBInstagramFeed {
	
	private static $_instance;
	
	private $resources = false;
	private $optimized = false;
	private $cache_ttl = 6;
	
	/**
	 * Get static instance of class
	 */
	public static function getInstance() {
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
		add_shortcode('instagram-photo', array($this, 'generate_image_code'));
		add_shortcode('instagram-image', array($this, 'generate_image_code'));
		
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
		if ($user_cache_ttl != '') {
			$this->cache_ttl = intval($user_cache_ttl);
		}
		
		if (essb_option_bool_value('instagramfeed_content')) {
			add_filter ( 'the_content', array($this, 'draw_widget_below_content'), 101);
		}
		
		if (essb_option_bool_value('instagramfeed_popup')) {
			add_action ( 'wp_footer', array($this, 'draw_widget_popup'), 101);
		}
	}
	
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
		$r['username'] = array('type' => 'text', 'title' => esc_html__('Username', 'essb'), 'description' => esc_html__('Fill just one of username or hashtag at same time', 'essb'));
		$r['tag'] = array('type' => 'text', 'title' => esc_html__('Hashtag', 'essb'), 'description' => esc_html__('Fill just one of username or hashtag at same time', 'essb'));
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
		$r['show'] = array('type' => 'text', 'title' => esc_html__('Images to show', 'essb'), 'description' => esc_html__('Enter number between 1 and 12', 'essb'));
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
		$r['image_size'] = array('type' => 'select', 'title' => esc_html__('Image size', 'essb'),
				'options' => array(
						'' => esc_html__('Default', 'essb'),
						'thumbnail' => esc_html__('Thumbnail', 'essb'),
						'small' => esc_html__('Small', 'essb'),
						'large' => esc_html__('Large', 'essb'),
						'original' => esc_html__('Original', 'essb')));
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
		$r['info'] = array('type' => 'select', 'title' => esc_html__('Hide hover image details', 'essb'),
				'options' => array(
						'' => esc_html__('Default from settings', 'essb'),
						'false' => esc_html__('Yes', 'essb')));
		
		return $r;
	}
	
	public function generate_image_code($atts = array()) {
		$id = isset($atts['id']) ? $atts['id'] : '';
		$profile = isset($atts['profile']) ? $atts['profile'] : '';
		$post_info = isset($atts['info']) ? $atts['info'] : '';
		
		if ($id == '') {
			return '';
		}
		else {
			return $this->draw_instagram_image($id, $profile == 'true', $post_info == 'true');
		}
	}
	
	public function generate_shortcode($atts = array()) {
		$username = isset($atts['username']) ? $atts['username'] : '';
		$tag = isset($atts['tag']) ? $atts['tag'] : '';
		
		$type = isset($atts['type']) ? $atts['type'] : '3cols';
		$show = isset($atts['show']) ? $atts['show'] : '';
		$profile = isset($atts['profile']) ? $atts['profile'] : '';
		$follow_button = isset($atts['followbtn']) ? $atts['followbtn'] : '';
		$image_size = isset($atts['image_size']) ? $atts['image_size'] : 'large';
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
					$link_mode, $post_info_style, $image_size, $masonry == 'true', $widget == 'true', $profile_size);
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
			
			echo '<div class="essb-instagramfeed-popup" data-delay="'.esc_attr($instagramfeed_popup_delay).'" data-width="'.esc_attr($instagramfeed_popup_width).'" data-hidefor="'.esc_attr($instagramfeed_popup_appear_again).'">';
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
	
	public function draw_instagram_image($image_id = '', $profile = false, $info = false) {
		$data = $this->scrape_instagram_post($image_id);

		$link_url = 'https://www.instagram.com/p/'.esc_attr($image_id).'/';
		$output = '';
		
		$output .= '<div class="essb-instagramfeed-photo">';
		
		if ($profile) {
			$follow_text = esc_html__('Follow', 'essb');
				
			$output .= '<div class="essb-instagramfeed-photo-profile">';
			$image = !empty($data['profile']['profile']) ? $data['profile']['profile'] : '';	
			$instagram_username = !empty($data['profile']['user']) ? $data['profile']['user'] : '';
			$instagram_profile_url = 'https://instagram.com/'.esc_attr($instagram_username);
			$instagram_name = !empty($data['profile']['full_name']) ? $data['profile']['full_name'] : '';
				
			if (!empty($image)) {
				$output .= '<div class="essb-instagramfeed-photo-profile-photo">';
				$output .= '<a href="'.esc_url($instagram_profile_url).'" target="_blank" rel="nofollow noreferrer noopener"><img src="'.esc_url($image).'"/></a>';
				$output .= '</div>';
			}
			
			if (!empty($instagram_name)) {
				$output .= '<div class="essb-instagramfeed-photo-profile-name">';
				$output .= '<a href="'.esc_url($instagram_profile_url).'" target="_blank" rel="nofollow noreferrer noopener">'.$instagram_name.'</a>';
				$output .= '</div>';
			}
				
			$output .= '<div class="essb-instagramfeed-photo-profile-followbtn">';
			$output .= '<a href="'.esc_url($instagram_profile_url).'" target="_blank" rel="nofollow noreferrer noopener">'.$follow_text.'</a>';
			$output .= '</div>';
				
			$output .= '</div>';
		}
		
		$image_url = !empty($data['images']['image_url']) ? $data['images']['image_url'] : '';
		$image_desc = !empty($data['images']['description']) ? $data['images']['description'] : '';
		$image_likes = !empty($data['images']['likes']) ? $data['images']['likes'] : '';
		$image_comments = !empty($data['images']['comments']) ? $data['images']['comments'] : '';
		$image_time = !empty($data['images']['time']) ? $data['images']['time'] : '';
		
		$output .= '<div class="essb-instagramfeed-photo-image">';
		$output .= '<a href="'.esc_url($link_url).'" target="_blank" rel="nofollow noreferrer noopener">';
		
		$output .= '<div class="essb-instagramfeed-photo-single-image">';
		$output .= '<img src="'.esc_url($image_url).'"/>';
		$output .= '</div>';
		$output .= '</a>';
		$output .= '</div>';
		
		if ($info) {
			$output .= '<div class="essb-instagramfeed-single-image-info">';
			$output .= '<div class="essb-instagramfeed-single-image-info-image">';
			$output .= '<div class="essb-instagramfeed-single-image-info-counters">';
			$output .= '<div class="essb-instagramfeed-single-image-info-counters-comment">';
			$output .= '<span class="icon">';
			$output .= '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 511.6 511.6" style="enable-background:new 0 0 511.6 511.6;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#FFFFFF;}
</style>
<g>
	<path class="st0" d="M477.4,127.4c-22.8-28.1-53.9-50.2-93.1-66.5c-39.2-16.3-82-24.4-128.5-24.4c-34.6,0-67.8,4.8-99.4,14.4
		c-31.6,9.6-58.8,22.6-81.7,39c-22.8,16.4-41,35.8-54.5,58.4C6.8,170.8,0,194.5,0,219.2c0,28.5,8.6,55.3,25.8,80.2
		c17.2,24.9,40.8,45.9,70.7,62.8c-2.1,7.6-4.6,14.8-7.4,21.7c-2.9,6.9-5.4,12.5-7.7,16.9c-2.3,4.4-5.4,9.2-9.3,14.6
		c-3.9,5.3-6.8,9.1-8.8,11.3c-2,2.2-5.3,5.8-9.9,10.8c-4.6,5-7.5,8.3-8.8,9.9c-0.2,0.1-1,1-2.3,2.6c-1.3,1.6-2,2.4-2,2.4l-1.7,2.6
		c-1,1.4-1.4,2.3-1.3,2.7c0.1,0.4-0.1,1.3-0.6,2.9c-0.5,1.5-0.4,2.7,0.1,3.4v0.3c0.8,3.4,2.4,6.2,5,8.3c2.6,2.1,5.5,3,8.7,2.6
		c12.4-1.5,23.2-3.6,32.5-6.3C133,456,176.7,433,214.4,399.7c14.3,1.5,28.1,2.3,41.4,2.3c46.4,0,89.3-8.1,128.5-24.4
		c39.2-16.3,70.2-38.4,93.1-66.5c22.8-28.1,34.3-58.7,34.3-91.8C511.6,186.1,500.2,155.5,477.4,127.4z"/>
</g>
</svg>';
			$output .= '</span>';
			$output .= '<span class="value">'.esc_html(essb_kilomega($image_comments)).'</span>';
			$output .= '</div>'; // comments
			$output .= '<div class="essb-instagramfeed-single-image-info-counters-likes">';
			$output .= '<span class="icon">';
			$output .= '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#FFFFFF;}
</style>
<path class="st0" d="M24.9,10.1c2-4.8,6.6-8.1,12-8.1c7.2,0,12.4,6.2,13.1,13.5c0,0,0.4,1.8-0.4,5.1c-1.1,4.5-3.5,8.5-6.9,11.5
	L24.9,48L7.4,32.2c-3.4-3-5.8-7-6.9-11.5c-0.8-3.3-0.4-5.1-0.4-5.1C0.7,8.2,5.9,2,13.2,2C18.5,2,22.8,5.3,24.9,10.1z"/>
</svg>';
			$output .= '</span>';
			$output .= '<span class="value">'.esc_html(essb_kilomega($image_likes)).'</span>';
			$output .= '</div>'; // likes
			$output .= '</div>'; // end counters
			$output .= '<div class="viewmore">';
			$output .= '<a href="'.esc_url($link_url).'" target="_blank" rel="nofollow noreferrer noopener">'.esc_html__('View more on Instagram', 'essb').'</a>';
			$output .= '</div>';
			$output .= '</div>'; // end parent counters
			$output .= '<div class="essb-instagramfeed-single-image-info-desc">';
			$output .= $this->wrap_tags_and_users($image_desc);
			$output .= '</div>'; // end desc
			$output .= '</div>'; // end info
		
		}
		
		$output .= '</div>';
		
		return $output;
	}
	
	public function draw_instagram($username_tag = '', $type = '3cols', $show = 12, $space = '', $profile = false, 
			$follow_button = false, $link_mode = 'direct', $post_data = '', $image_size = 'original', $masonry = false,
			$widget = false, $profile_size = '') {
	    
		$data = $this->scrape_instagram($username_tag);
				
		/**
		 * The profile card and follow button can appear only for the personal profiles
		 */
		if (substr( $username_tag, 0, 1 ) != '@') {
			$profile = false;
			$follow_button = false;
		}
		
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
		
		$parent_classes[] = 'essb-forced-hidden';		
		
		$output = '<div class="'.esc_attr(join(' ', $parent_classes)).'">';
		
		/**
		 * Profile card if shown
		 */
		if ($profile) {
			$follow_text = esc_html__('Follow', 'essb');
			
			$output .= '<div class="essb-instagramfeed-profile'.($follow_button && !$widget ? ' essb-instagramfeed-profilefollow': '').'">';
			$image = !empty($data['profile']['profile_hd']) ? $data['profile']['profile_hd'] : (!empty($data['profile']['profile']) ? $data['profile']['profile'] : '');
			$bio = !empty($data['profile']['bio']) ? $data['profile']['bio'] : '';
			$followers_value = !empty($data['profile']['followers']) ? $data['profile']['followers'] : 0;
			
			$instagram_profile_url = 'https://instagram.com/'.esc_attr(str_replace('@', '', $username_tag ));
			
			if (!empty($image)) {
				$output .= '<div class="essb-instagramfeed-profile-photo">';
				$output .= '<a href="'.esc_url($instagram_profile_url).'" target="_blank" rel="nofollow noreferrer noopener"><img src="'.esc_url($image).'"/></a>';
				$output .= '</div>';
			}
			
			if (!empty($bio)) {
				$output .= '<div class="essb-instagramfeed-profile-bio">';
				$output .= '<span class="profile-name">'.esc_html(str_replace('@', '', $username_tag )).'</span>';
				if (intval($followers_value) > 0) {
					$output .= '<span class="profile-likes">';
					$output .= '<b>'.essb_kilomega($data['profile']['followers']).'</b> '.esc_html__('followers', 'essb');
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
		
		if (intval($show) > 12) {
			$show = 12;
		}
		
		$output .= '<div class="essb-instagramfeed-images">';
		$count = 1;
		foreach ($data['images'] as $image) {
			
			if ($count > $show) {
				continue;
			}
			
			$image_url = isset($image[$image_size]) ? $image[$image_size] : $image['original'];
			$link_url = $image['link'];
			$type = isset($image['type']) ? $image['type'] : 'image';
			
			$key = mt_rand();
			
			$output .= '<div class="essb-instagramfeed-single essb-instagramfeed-single-'.esc_attr($key).' essb-instagramfeed-single-'.esc_attr($type).'" style="background: url('.esc_url($image_url).');">';
			$output .= '<a href="'.esc_url($link_url).'" target="_blank" rel="nofollow noreferrer noopener">';
			$output .= '<div class="essb-instagramfeed-single-image">';
			$output .= '<img src="'.esc_url($image_url).'"/>';
			
			$output .= '</div>';
			
			$output .= '<div class="essb-instagramfeed-single-image-info">';
			$output .= '<div class="essb-instagramfeed-single-image-info-counters">';
			$output .= '<div class="essb-instagramfeed-single-image-info-counters-comment">';
			$output .= '<span class="icon">';
			$output .= '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 511.6 511.6" style="enable-background:new 0 0 511.6 511.6;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#FFFFFF;}
</style>
<g>
	<path class="st0" d="M477.4,127.4c-22.8-28.1-53.9-50.2-93.1-66.5c-39.2-16.3-82-24.4-128.5-24.4c-34.6,0-67.8,4.8-99.4,14.4
		c-31.6,9.6-58.8,22.6-81.7,39c-22.8,16.4-41,35.8-54.5,58.4C6.8,170.8,0,194.5,0,219.2c0,28.5,8.6,55.3,25.8,80.2
		c17.2,24.9,40.8,45.9,70.7,62.8c-2.1,7.6-4.6,14.8-7.4,21.7c-2.9,6.9-5.4,12.5-7.7,16.9c-2.3,4.4-5.4,9.2-9.3,14.6
		c-3.9,5.3-6.8,9.1-8.8,11.3c-2,2.2-5.3,5.8-9.9,10.8c-4.6,5-7.5,8.3-8.8,9.9c-0.2,0.1-1,1-2.3,2.6c-1.3,1.6-2,2.4-2,2.4l-1.7,2.6
		c-1,1.4-1.4,2.3-1.3,2.7c0.1,0.4-0.1,1.3-0.6,2.9c-0.5,1.5-0.4,2.7,0.1,3.4v0.3c0.8,3.4,2.4,6.2,5,8.3c2.6,2.1,5.5,3,8.7,2.6
		c12.4-1.5,23.2-3.6,32.5-6.3C133,456,176.7,433,214.4,399.7c14.3,1.5,28.1,2.3,41.4,2.3c46.4,0,89.3-8.1,128.5-24.4
		c39.2-16.3,70.2-38.4,93.1-66.5c22.8-28.1,34.3-58.7,34.3-91.8C511.6,186.1,500.2,155.5,477.4,127.4z"/>
</g>
</svg>';
			$output .= '</span>';
			$output .= '<span class="value">'.esc_html(essb_kilomega($image['comments'])).'</span>';
			$output .= '</div>'; // comments
			$output .= '<div class="essb-instagramfeed-single-image-info-counters-likes">';
			$output .= '<span class="icon">';
			$output .= '<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
<style type="text/css">
	.st0{fill:#FFFFFF;}
</style>
<path class="st0" d="M24.9,10.1c2-4.8,6.6-8.1,12-8.1c7.2,0,12.4,6.2,13.1,13.5c0,0,0.4,1.8-0.4,5.1c-1.1,4.5-3.5,8.5-6.9,11.5
	L24.9,48L7.4,32.2c-3.4-3-5.8-7-6.9-11.5c-0.8-3.3-0.4-5.1-0.4-5.1C0.7,8.2,5.9,2,13.2,2C18.5,2,22.8,5.3,24.9,10.1z"/>
</svg>';
			$output .= '</span>';
			$output .= '<span class="value">'.esc_html(essb_kilomega($image['likes'])).'</span>';
			$output .= '</div>'; // likes
			$output .= '</div>'; // end counters
			$output .= '<div class="essb-instagramfeed-single-image-info-desc">';
			$output .= $this->wrap_tags_and_users($image['description']);
			$output .= '</div>'; // end desc
			$output .= '</div>'; // end info
			
			$output .= '</a>';
			$output .= '</div>';
			
			$count++;
		}
		
		$output .= '</div>'; // -images
		$output .= '</div>';

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
	
	public function scrape_instagram_post($post_id) {
		$url = 'https://www.instagram.com/p/' . esc_attr($post_id) . '/?__a=1';
		$transient_prefix = 'p';
		
		/**
		 * Check if already a cached data exists in the database for that tag
		 */
		if ( false === ( $instagram = get_transient( 'essb-' . $transient_prefix . '-' . sanitize_title_with_dashes( $post_id ) ) ) ) {
		
			$remote = wp_remote_get( $url, array(
					'user-agent' => 'Instagram/' . ESSB3_VERSION . '; ' . home_url()
			) );
		
			if ( is_wp_error( $remote ) ) {
				return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'essb' ) );
			}
		
			if ( 200 !== wp_remote_retrieve_response_code( $remote ) ) {
				return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'essb' ) );
			}
		
			$insta_array = json_decode( $remote['body'], true );
		
			if ( ! $insta_array ) {
				return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'essb' ) );
			}
			
			if (!isset($insta_array['graphql']) || !isset($insta_array['graphql']['shortcode_media'])) {
				return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'essb' ) );
			}
				
			$media_data = $insta_array['graphql']['shortcode_media'];
			$instagram = array();
			
			$instagram['image_url'] = isset($media_data['display_url']) ? $media_data['display_url'] : '';
			$instagram['description'] = '';
			if ( ! empty( $media_data['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
				$instagram['description'] = wp_kses( $media_data['edge_media_to_caption']['edges'][0]['node']['text'], array() );
			}
			
			$instagram['link'] = trailingslashit( '//www.instagram.com/p/' . $media_data['shortcode']);
			$instagram['time'] = $media_data['taken_at_timestamp'];
			$instagram['comments'] = $media_data['edge_media_preview_comment']['count'];
			$instagram['likes'] = $media_data['edge_media_preview_like']['count'];
				
			$profile_data = array();
			if (isset($media_data['owner'])) {
				$profile_data = array(
						'user' => $media_data['owner']['username'],
						'full_name' => $media_data['owner']['full_name'],
						'id' => $media_data['owner']['id'],
						'profile' => $media_data['owner']['profile_pic_url'],
				);
			}
		
			// do not set an empty transient - should help catch private or empty accounts. Set a shorter transient in other cases to stop hammering Instagram
			if ( ! empty( $instagram ) ) {
				$data = array('profile' => $profile_data, 'images' => $instagram);
				$instagram = base64_encode( serialize( $data ) );
				set_transient( 'essb-' . $transient_prefix . '-' . sanitize_title_with_dashes( $post_id ), $instagram, $this->cache_ttl * HOUR_IN_SECONDS );
			} else {
				$data = array('profile' => array(), 'images' => array());
				$instagram = base64_encode( serialize( $data ) );
				set_transient( 'essb-' . $transient_prefix . '-' . sanitize_title_with_dashes( $post_id ), $instagram, MINUTE_IN_SECONDS * 1 );
			}
		}
		
		if ( ! empty( $instagram ) ) {
			return unserialize( base64_decode( $instagram ) );
		} else {
			return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'essb' ) );
		}
		
	}
	
	/**
	 * Scrape data from instagram for tag or username
	 * @param string $username_or_tag (@user or #hashtag)
	 */
	public function scrape_instagram($username_or_tag = '') {
		$username = trim( strtolower( $username_or_tag ) );
				
		switch ( substr( $username, 0, 1 ) ) {
			case '#':
				$url = 'https://www.instagram.com/explore/tags/' . str_replace( '#', '', $username ) . '?__a=1';
				$transient_prefix = 'h';
				break;
		
			default:
				$url = 'https://www.instagram.com/' . str_replace( '@', '', $username ) . '?__a=1';
				$transient_prefix = 'u';
				break;
		}
		
		
		/**
		 * Check if already a cached data exists in the database for that tag
		 */
		if ( false === ( $instagram = get_transient( 'essb-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username ) ) ) ) {
		
			$remote = wp_remote_get( $url, array(
					'user-agent' => 'Instagram/' . ESSB3_VERSION . '; ' . home_url()
			) );
		
			if ( is_wp_error( $remote ) ) {
			    return $this->blank_instagram_feed( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'essb' ) );
			}
		
			if ( 200 !== wp_remote_retrieve_response_code( $remote ) ) {
			    return $this->blank_instagram_feed( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'essb' ) );
			}
		
			$insta_array = json_decode( $remote['body'], true );
		
			if ( ! $insta_array ) {
			    return $this->blank_instagram_feed( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'essb' ) );
			}			
			
			if ( isset( $insta_array['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
				$images = $insta_array['graphql']['user']['edge_owner_to_timeline_media']['edges'];
			} elseif ( isset( $insta_array['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
				$images = $insta_array['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
			} else {
			    return $this->blank_instagram_feed( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'essb' ) );
			}
		
			if ( ! is_array( $images ) ) {
			    return $this->blank_instagram_feed( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'essb' ) );
			}
			
			$profile_data = array();
			if (isset($insta_array['graphql']['user'])) {
				$profile_data = array(
						'bio' => $insta_array['graphql']['user']['biography'],
						'external' => $insta_array['graphql']['user']['external_url'],
						'followers' => $insta_array['graphql']['user']['edge_followed_by']['count'],
						'profile' => $insta_array['graphql']['user']['profile_pic_url'],
						'profile_hd' => $insta_array['graphql']['user']['profile_pic_url_hd'],
				);
			}
		
			$instagram = array();
		
			foreach ( $images as $image ) {
				if ( true === $image['node']['is_video'] ) {
					$type = 'video';
				} else {
					$type = 'image';
				}
		
				$caption = __( 'Instagram Image', 'essb' );
				if ( ! empty( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
					$caption = wp_kses( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'], array() );
				}
		
				$instagram[] = array(
						'description' => $caption,
						'link'        => trailingslashit( '//www.instagram.com/p/' . $image['node']['shortcode'] ),
						'time'        => $image['node']['taken_at_timestamp'],
						'comments'    => $image['node']['edge_media_to_comment']['count'],
						'likes'       => $image['node']['edge_liked_by']['count'],
						'thumbnail'   => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][0]['src'] ),
						'small'       => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][2]['src'] ),
						'large'       => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][4]['src'] ),
						'original'    => preg_replace( '/^https?\:/i', '', $image['node']['display_url'] ),
						'type'        => $type,
				);
			} // End foreach().
			
			// do not set an empty transient - should help catch private or empty accounts. Set a shorter transient in other cases to stop hammering Instagram
			if ( ! empty( $instagram ) ) {
				$data = array('profile' => $profile_data, 'images' => $instagram);
				$instagram = base64_encode( serialize( $data ) );
				set_transient( 'essb-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username ), $instagram, $this->cache_ttl * HOUR_IN_SECONDS );
			} else {
				$data = array('profile' => array(), 'images' => array());
				$instagram = base64_encode( serialize( $data ) );
				set_transient( 'essb-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username ), $instagram, MINUTE_IN_SECONDS * 1 );
			}
		}
		
		if ( ! empty( $instagram ) ) {
			return unserialize( base64_decode( $instagram ) );
		} else {
		    return $this->blank_instagram_feed( 'no_images', esc_html__( 'Instagram did not return any images.', 'essb' ) );
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
}

if (!function_exists('essb_instagram_feed')) {
	function essb_instagram_feed() {
	    return ESSB_Factory_Loader::get('instagram');
	}
}