<?php

/**
 * After share events. Calling after share button is clicked and provide support for user-based actions
 
 * @author appscreo
 * @since 1.3
 * @package EasySocialShareButtons
 * 
 * Adding full code escape and Google+ is deprecated
 * @since 6.3
 */

class ESSBAfterCloseShare3 {
	static $instance;
	
	private $options;
	
	public $version = "";
	
	public $resource_files = array();
	public $js_code = array();
	public $social_apis = array();
	
	protected $mobile_detect;	
	protected $single_display_mode = false;
	protected $single_display_cookie_length = 7;
	
	public $pinterest_api_load = false;
	
	function __construct() {
		// Reading all options
		$this->options = ESSB_Plugin_Options::read_all();
		
		$is_active = essb_option_bool_value('afterclose_active');
		$is_deactive_mobile = essb_option_bool_value('afterclose_deactive_mobile');
		
		$is_active_singledisplay = essb_option_bool_value('afterclose_singledisplay');
		$single_display_cookie_length = essb_sanitize_option_value('afterclose_singledisplay_days');
				
		$this->single_display_mode = $is_active_singledisplay;
		$this->single_display_cookie_length = intval($single_display_cookie_length);
		if ($this->single_display_cookie_length == 0) { $this->single_display_cookie_length = 7; }
		
		$afterclose_deactive_sharedisable = essb_option_bool_value('afterclose_deactive_sharedisable');
 		
		$is_active_option = "";
		if (ESSB3_DEMO_MODE) {
			$is_active_option = isset($_REQUEST['aftershare']) ? $_REQUEST['aftershare'] : '';
			if ($is_active_option != '') {
				$is_active = true;
			}
		}
		
		// @since 2.0.3 - deactivate on mobile
		if ($is_active && $is_deactive_mobile && $this->isMobile()) {
			$is_active = false;
		}
		
		// @since 2.0.3
		if ($this->single_display_mode) {
			$cookie_aftershare = isset($_COOKIE['essb_aftershare']) ? true : false;
			if ($cookie_aftershare) {
				$is_active = false;
			}
		}

		if ($is_active) {
			add_action ( 'wp_enqueue_scripts', array ($this, 'check_after_postload_settings' ), 1 );
		}
	}
	
	public function pinterest_api_loaded() {
	    return $this->pinterest_api_load;
	}
	
	public function check_after_postload_settings() {
		
		$is_active = true;
		
		$is_active_option = "";
		if (ESSB3_DEMO_MODE) {
			$is_active_option = isset($_REQUEST['aftershare']) ? $_REQUEST['aftershare'] : '';
			if ($is_active_option != '') {
				$is_active = true;
			}
		}
		
		if ($this->is_user_deactivatedon()) {
			$is_active = false;
		}
		
		// @since 4.0
		$afterclose_activate_all = essb_options_bool_value('afterclose_activate_all');
		if (!$afterclose_activate_all) {
			$post_types_run = essb_option_value('display_in_types');
				
			if (!is_array($post_types_run)) {
				$post_types_run = array();
			}
			
			if (!essb_core()->check_applicability($post_types_run, 'aftershare')) {
				$is_active = false;
			}
		}
		
		if (essb_option_bool_value('afterclose_activate_sharedisable')) {
			$is_active = true;
		}
		
		if (!$is_active) {
			remove_action ( 'wp_footer', array ($this, 'generateFollowWindow' ), 99 );
			remove_action ( 'wp_footer', array ($this, 'generateMessageText' ), 99 );
			remove_action ( 'wp_footer', array ($this, 'generate_option_code' ), 99 );
			remove_action ( 'wp_footer', array ($this, 'generate_popular_posts' ), 99 );
				
		}
		else {
			$this->load($is_active_option);
		}
	}
	
	public function is_user_deactivatedon() {
		$is_user_deactivated = false;
		$display_exclude_from = essb_object_value($this->options, 'display_exclude_from');
		
		if ($display_exclude_from != "") {
			$excule_from = explode(',', $display_exclude_from);
		
			$excule_from = array_map('trim', $excule_from);
		
			if (in_array(get_the_ID(), $excule_from, false)) {
				$is_user_deactivated = true;
			}
		}
		
		if ( essb_is_module_deactivated_on('aftershare')) {
			$is_user_deactivated = true;
		}
		
		$essb_off = get_post_meta(get_the_ID(),'essb_off',true);
		
		if ($essb_off == "true") {
			$is_user_deactivated = true;
		}
		
		if (essb_option_bool_value('afterclose_activate_sharedisable')) {
			$is_user_deactivated = false;
		}
		
		return $is_user_deactivated;
	}
	
	public static function get_instance() {
	
		if ( ! self::$instance )
			self::$instance = new ESSBAfterCloseShare3();
	
		return self::$instance;	
	}
	
	public function isMobile() {
		$isMobile = essb_is_mobile();
		return $isMobile;
	}
	
	private function load($demo_mode = '') {
		$acs_type = essb_sanitize_option_value('afterclose_type');
		
		$always_use_code = essb_option_bool_value('afterclose_code_always_use');
		
		if ($demo_mode != '') {
			$acs_type = $demo_mode;
		}
		
		switch ($acs_type) {
			case "follow":
				$this->prepare_required_social_apis();
				$this->register_asc_assets();
				add_action ( 'wp_footer', array ($this, 'generateFollowWindow' ), 99 );
				if ($always_use_code) {
					$this->generateMessageCode();
				}
				break;				
			case "message":
				$this->register_asc_assets();
				add_action ( 'wp_footer', array ($this, 'generateMessageText' ), 99 );
				if ($always_use_code) {
					$this->generateMessageCode();
				}
				break;
			case "follow_profile":
				$this->register_asc_assets();
				add_action ( 'wp_footer', array ($this, 'generateMessageSocialProfiles' ), 99 );
				if ($always_use_code) {
					$this->generateMessageCode();
				}
				break;				
			case "code":
				$this->generateMessageCode();
				break;		
			case "optin":
				add_action ( 'wp_footer', array ($this, 'generate_option_code' ), 99 );
				if ($always_use_code) {
					$this->generateMessageCode();
				}
				break;
			case "popular":
				$this->register_asc_assets();				
				add_action ( 'wp_footer', array ($this, 'generate_popular_posts' ), 99 );
				if ($always_use_code) {
					$this->generateMessageCode();
				}
				break;					
		}
		
		foreach ($this->js_code as $key => $code) {
			essb_resource_builder()->add_js($code, false, 'essbasc_custom'.$key);
		}
		
		foreach ($this->social_apis as $key => $code) {
			essb_resource_builder()->add_social_api($key);
		}
	}
	
	public function register_asc_assets() {
				
		foreach ($this->resource_files as $key => $object) {
			
			if (isset($object['noasync'])) {
				essb_resource_builder()->add_static_resource_footer($object["file"], $object["key"], $object["type"], true);
			}
			else {
				essb_resource_builder()->add_static_resource_footer($object["file"], $object["key"], $object["type"]);
			}
		}
	}
	
	public function generateMessageCode() {
		$user_js_code = essb_object_value($this->options, 'afterclose_code_text');
		
		if ($user_js_code != '') {
			$user_js_code = stripslashes($user_js_code);
			
			$this->js_code[] = 'function essb_acs_code(oService, oPostID) { '.$user_js_code.' }';
		}
	} 
	
	public function prepare_required_social_apis() {
		$afterclose_like_text = essb_object_value($this->options, 'afterclose_like_text');
		$afterclose_like_fb_like_url = essb_object_value($this->options, 'afterclose_like_fb_like_url');
		$afterclose_like_fb_follow_url = essb_object_value($this->options, 'afterclose_like_fb_follow_url');

		/**
		 * @since 6.3
		 * Google+ is closed on April 2019. To prevent issues appearing on sites the generation of the button
		 * is removed directly from code even if the settings are filled
		 */
		$afterclose_like_google_url = '';
		$afterclose_like_google_follow_url = '';
		
		$afterclose_like_twitter_profile = essb_object_value($this->options, 'afterclose_like_twitter_profile');
		$afterclose_like_pin_follow_url = essb_object_value($this->options, 'afterclose_like_pin_follow_url');
		$afterclose_like_youtube_channel = essb_object_value($this->options, 'afterclose_like_youtube_channel');
		$afterclose_like_youtube_user = essb_option_value('afterclose_like_youtube_user');
		$afterclose_like_linkedin_company = essb_object_value($this->options, 'afterclose_like_linkedin_company');
		$afterclose_like_vk = essb_option_value('afterclose_like_vk');
		
		if ($afterclose_like_fb_like_url != '') {
			$this->social_apis['facebook'] = 'load';
		}
		if ($afterclose_like_fb_follow_url != '') {
			$this->social_apis['facebook'] = 'load';
		}
		if ($afterclose_like_google_url != '') {
			$this->social_apis['google'] = 'load';
		}
		if ($afterclose_like_google_follow_url != '') {
			$this->social_apis['google'] = 'load';
		}
		if ($afterclose_like_pin_follow_url != '') {
			$this->resource_files[] = array("key" => "pinterest-api", "file" => '//assets.pinterest.com/js/pinit.js', "type" => "js", 'noasync' => true);
			
			// Plugin will load Pinterest JS API
			$this->pinterest_api_load = true;
		}
		if ($afterclose_like_youtube_channel != '' || $afterclose_like_youtube_user != '') {
			$this->social_apis['google'] = 'load';
		}
	}
	
	public function generateFollowButton($social_code, $network_key, $icon_key) {
		$output = '';
		
		$output .= '<div class="essbasc-fans-single essbasc-fans-'.esc_attr($network_key).'">
				<div class="essbasc-fans-icon">
					<i class="essb_icon_'.esc_attr($icon_key).'"></i>
				</div>
				<div class="essbasc-fans-text">
		'.$social_code.'
		</div>
		</div>';
		
		return $output;
	}
	
	public function generateFollowWindow() {
		
		$afterclose_like_text = essb_object_value($this->options, 'afterclose_like_text');
		$afterclose_like_fb_like_url = essb_object_value($this->options, 'afterclose_like_fb_like_url');
		$afterclose_like_fb_follow_url = essb_object_value($this->options, 'afterclose_like_fb_follow_url');
		$afterclose_like_google_url = essb_object_value($this->options, 'afterclose_like_google_url');
		$afterclose_like_google_follow_url = essb_object_value($this->options, 'afterclose_like_google_follow_url');
		$afterclose_like_twitter_profile = essb_object_value($this->options, 'afterclose_like_twitter_profile');
		$afterclose_like_pin_follow_url = essb_object_value($this->options, 'afterclose_like_pin_follow_url');
		$afterclose_like_youtube_channel = essb_object_value($this->options, 'afterclose_like_youtube_channel');
		$afterclose_like_youtube_user = essb_option_value('afterclose_like_youtube_user');
		$afterclose_like_linkedin_company = essb_object_value($this->options, 'afterclose_like_linkedin_company');
		$afterclose_like_vk = essb_option_value('afterclose_like_vk');
		
		$afterclose_like_cols = essb_object_value($this->options, 'afterclose_like_cols', 'onecol');
						
		$afterclose_like_text = stripslashes($afterclose_like_text);
		
		$widget = "";
		
		if ($afterclose_like_text != '') {
			$widget .= '<div class="essbasc-text-before">'.$afterclose_like_text.'</div>';
		}
		
		$widget .= '<div class="essbasc-fans '.esc_attr($afterclose_like_cols).'">';
		
		if ($afterclose_like_fb_like_url != '') {
			$social_code = '<div class="fb-like" data-href="'.esc_url($afterclose_like_fb_like_url).'" data-width="" data-layout="button_count" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div>';
			$widget .= $this->generateFollowButton($social_code, 'facebook', 'facebook');
		}
		if ($afterclose_like_fb_follow_url != '') {
			$social_code = '<div class="fb-follow" data-href="'.esc_url($afterclose_like_fb_follow_url).'" data-colorscheme="light" data-layout="button_count" data-show-faces="true"></div>';
			$widget .= $this->generateFollowButton($social_code, 'facebook', 'facebook');
		}
		if ($afterclose_like_twitter_profile != '') {
			$social_code = '<a href="https://twitter.com/'.esc_attr($afterclose_like_twitter_profile).'" class="twitter-follow-button" data-show-count="true" data-show-screen-name="false">Follow @'.$afterclose_like_twitter_profile.'</a>';
			$social_code .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";
			$widget .= $this->generateFollowButton($social_code, 'twitter', 'twitter');
		}	
		if ($afterclose_like_pin_follow_url != '') {
			$social_code = '<a data-pin-do="buttonFollow" href="'.esc_url($afterclose_like_pin_follow_url).'">'.'Follow'.'</a>';
			$widget .= $this->generateFollowButton($social_code, 'pinterest', 'pinterest');
		}	
		if ($afterclose_like_youtube_channel != '') {
			$social_code = '<div class="g-ytsubscribe" data-channelid="'.esc_attr($afterclose_like_youtube_channel).'" data-layout="default" data-count="default"></div>';
			$widget .= $this->generateFollowButton($social_code, 'youtube', 'youtube-play');				
		}
		if ($afterclose_like_youtube_user != '') {
			$social_code = '<div class="g-ytsubscribe" data-channel="'.esc_attr($afterclose_like_youtube_user).'" data-layout="default" data-count="default"></div>';
			$widget .= $this->generateFollowButton($social_code, 'youtube', 'youtube-play');				
		}
		if ($afterclose_like_linkedin_company != '') {
			$social_code = '<script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script><script type="IN/FollowCompany" data-id="'.$afterclose_like_linkedin_company.'" data-counter="right"></script>';
			$widget .= $this->generateFollowButton($social_code, 'linkedin', 'linkedin');				
		}
		if ($afterclose_like_vk != '') {
			$social_code = '<!-- VK Widget -->
			<script type="text/javascript" src="//vk.com/js/api/openapi.js?139"></script><div id="vk_subscribe"></div><script type="text/javascript">VK.Widgets.Subscribe("vk_subscribe", {soft: 1}, '.esc_attr($afterclose_like_vk).');</script>';
			$widget .= $this->generateFollowButton($social_code, 'vk', 'vk');
		}
		
		$widget .= '</div>';
		
		$this->popupWindowGenerate($widget, 'follow', '', true, essb_sanitize_option_value('afterclose_follow_title'));
		
	}
	
	public function generateMessageText() {
		$user_html_code = essb_object_value($this->options, 'afterclose_message_text');
		$user_html_code = stripslashes($user_html_code);		
		$user_html_code = do_shortcode($user_html_code);
		
		$this->popupWindowGenerate($user_html_code, 'html', '', true, essb_sanitize_option_value('afterclose_message_title'));
	}
	
	public function generateMessageSocialProfiles() {
		$user_html_code = essb_option_value('aftershare_profiles_message');
		$user_html_code = stripslashes($user_html_code);
		$user_html_code = do_shortcode($user_html_code);
		
		if ($user_html_code != '') {
			$user_html_code = '<div class="essbasc-text-before">'.$user_html_code.'</div>';
		}
				
		if (class_exists('ESSBSocialProfiles')) {
			$options = array();
			$options['template'] = essb_sanitize_option_value('aftershare_profiles_template');
			$options['animation'] = essb_sanitize_option_value('aftershare_profiles_animation');
			$options['align'] = essb_sanitize_option_value('aftershare_profiles_align');
			$options['size'] = essb_sanitize_option_value('aftershare_profiles_size');
			$options['columns'] = essb_sanitize_option_value('aftershare_profiles_columns');
				
			$options['nospace'] = essb_option_bool_value('aftershare_profiles_nospace');
			$options['cta'] = essb_option_bool_value('aftershare_profiles_cta');
			$options['cta_vertical'] = essb_option_bool_value('aftershare_profiles_cta_vertical');
			
			$profile_networks = ESSBSocialProfilesHelper::get_active_networks();
			
			if (!is_array($profile_networks)) {
				$profile_networks = array();
			}
			
			$profile_networks_order = ESSBSocialProfilesHelper::get_active_networks_order();
			
			if (!is_array($profile_networks_order)) {
				$profile_networks_order = array();
			}
			
			$profiles = array();
			foreach ($profile_networks_order as $network) {
			
				if (in_array($network, $profile_networks)) {
					$value_address = essb_option_value('profile_'.$network);
			
					if (!empty($value_address)) {
						$profiles[$network] = $value_address;
					}
				}
			}
			
			$options['networks'] = $profiles;
			
			$user_html_code .= ESSBSocialProfiles::draw_social_profiles($options);
		}
		
		$this->popupWindowGenerate($user_html_code, 'profiles', '', true, essb_sanitize_option_value('afterclose_profile_title'));
	}
	
	public function popupWindowGenerate($html, $type = '', $force_width = '', $title_mode = false, $title = '') {
		
		if ($title_mode && $title == '') { 
			$title = '&nbsp;';
		}
		
		$popup_width = essb_object_value($this->options, 'afterclose_popup_width', '500');
		
		if (trim($popup_width) == '') { $popup_width = '400'; }
		
		if ($force_width != '') { $popup_width = $force_width; }
		
		if ($type != '') {
			$type = ' essbasc-popup-'.$type;
		}
		
		if ($title_mode) {
			$type .= ' essbasc-title-mode';
		}
		
		echo '<div class="essbasc-popup'.esc_attr($type).'" data-popup-width="'.esc_attr($popup_width).'" data-single="'.($this->single_display_mode ? 'true' : 'false').'">';
		if ($title_mode) {
			echo '<div class="essbasc-popup-header">';
			if ($title != '') {
				echo '<span>'.$title.'</span>';
			}
		}
		echo '<a href="#" class="essbasc-popup-close" onclick="essbasc_popup_close(); return false;"><i class="essb_icon_close"></i></a>';
		if ($title_mode) {
			echo '</div>';
		}
		echo '<div class="essbasc-popup-content">';
		echo $html;
		echo '</div>';
		
		echo '</div>';
		echo '<div class="essbasc-popup-shadow" onclick="essbasc_popup_close();"></div>';		
		essb_resource_builder()->add_js('var essbasc_cookie_live = '.intval(esc_attr($this->single_display_cookie_length)).';', true, 'essb-asc-cookie-live');
	}
	
	public function generate_option_code() {
		$design = essb_option_value('aftershare_optin_design');
		if ($design == '') { $design = 'design1'; }
		
		if (!class_exists('ESSBNetworks_Subscribe')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe.php');
		}
		echo ESSBNetworks_Subscribe::draw_aftershare_popup_subscribe_form($design);
	}
	
	public function generate_popular_posts() {
		
		$user_title = essb_option_value('translate_as_popular_title');
		if ($user_title == '') {
			$user_title = esc_html__('Popular posts', 'essb');
		}
		
		$user_shares = essb_option_value('translate_as_popular_shares');
		if ($user_shares == '') {
			$user_shares = esc_html__('Shares', 'essb');
		}
		
		$code = do_shortcode('[easy-popular-posts title="'.$user_title.'" show_num="yes" num_text="'.$user_shares.'" number="4" same_cat="true" show_thumb="true" thumb_size="thumb"]');
		$this->popupWindowGenerate($code, 'follow', '500');
	}
}

?>