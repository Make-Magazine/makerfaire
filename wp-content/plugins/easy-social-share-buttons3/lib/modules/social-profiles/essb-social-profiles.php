<?php
/**
 * Manages the social profiles display
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 *
 */
class ESSBSocialProfiles {
	private static $instance = null;
	
	private $activated = true;
	
	public static function get_instance() {
	
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	
		return self::$instance;
	
	} // end get_instance;
	
	function __construct() {
		$is_active = false;
		$resources_loaded = false;		
		
		if (essb_option_bool_value('profiles_display')) {
			$profiles_display_position = essb_option_value('profiles_display_position');
			
			if ($profiles_display_position != 'widget') {
				$is_active = true;
				
				if (essb_option_bool_value('profiles_mobile_deactivate')) {
					if (essb_is_mobile()) {
						$is_active = false;
					}
				}
			}
		}
				
		if ($is_active) {
			add_action( 'wp_enqueue_scripts' , array ( $this , 'register_front_assets' ), 1);
			add_action( 'wp_footer', array($this, 'display_profiles'));
			$resources_loaded = true;
		}
		
		
		if (essb_options_bool_value('profiles_post_display')) {
			if (!$resources_loaded) {
				add_action( 'wp_enqueue_scripts' , array ( $this , 'register_front_assets' ), 1);
			}
			
			add_filter( 'the_content', array($this, 'display_content_profiles') );
		}
	}	
	
	public function register_front_assets() {
		if (essb_is_plugin_deactivated_on() || essb_is_module_deactivated_on('profiles')) {
			$this->activated = false;
			return;
		}
		
		essb_resource_builder()->add_static_resource(ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/css/essb-followers-counter.min.css', 'essb-social-followers-counter', 'css');
		essb_resource_builder()->activate_resource('profiles_css');
	
	}
	
	/**
	 * Add profile content buttons below content of posts
	 * 
	 * @param unknown_type $content
	 * @return unknown|string
	 */
	function display_content_profiles($content) {
		// Do not attach buttons if plugin or module is deactivated on that location
		if (essb_is_plugin_deactivated_on() || essb_is_module_deactivated_on('profiles')) {
			return $content;
		}
		
		
		if (!is_singular()) {
			return $content;
		}
		
		$profile_bar = ESSBSocialProfiles::draw_social_profiles_bar();
		
		return $content.$profile_bar;
	}
	
	function display_profiles() {
		if (essb_is_plugin_deactivated_on() || essb_is_module_deactivated_on('profiles')) {
			return "";
		}
		
		
		$profiles_display_position = essb_option_value('profiles_display_position');
		$profiles_template = essb_option_value('profiles_template');
		$profiles_animation = essb_option_value('profiles_animation');
		$profiles_nospace = essb_option_bool_value('profiles_nospace');
		$profiles_size = essb_option_value('profiles_size');

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
		
		$options = array(
				'position' => $profiles_display_position,
				'template' => $profiles_template,
				'animation' => $profiles_animation,
				'nospace' => $profiles_nospace,
				'networks' => $profiles,
				'size' => $profiles_size
				);
		
		echo $this->draw_social_profiles($options);
	}
	
	/**
	 * Static function that generates the profile links bar. The function is used
	 * to generate automatically bar below content or the bar used with shortcode on site
	 * 
	 */
	public static function draw_social_profiles_bar() {
		$profiles_post_align = essb_option_value('profiles_post_align');
		$profiles_post_content_pos = essb_option_value('profiles_post_content_pos');
		$profiles_post_content = essb_option_value('profiles_post_content'); //stripslashes
		if ($profiles_post_content != '') {
			$profiles_post_content = stripslashes($profiles_post_content);
			$profiles_post_content = do_shortcode($profiles_post_content);
		}
		
		if ($profiles_post_align == '') {
			$profiles_post_align = 'left';
		}
		
		if ($profiles_post_content_pos == '') {
			$profiles_post_content_pos = 'above';
		}
		
		$profiles_post_width = essb_option_value('profiles_post_width');
		
		$profiles_post_template = essb_option_value('profiles_post_template');
		$profiles_post_animation = essb_option_value('profiles_post_animation');
		$profiles_post_nospace = essb_option_bool_value('profiles_post_nospace');
		$profiles_post_size = essb_option_value('profiles_post_size');
		$profiles_post_show_text = essb_option_bool_value('profiles_post_show_text');
		
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
		
		$options = array(
				'class' => 'essbfc-profiles-postbar',
				'size' => $profiles_post_size,
				'template' => $profiles_post_template,
				'animation' => $profiles_post_animation,
				'nospace' => $profiles_post_nospace,
				'show_text' => $profiles_post_show_text ? 'yes' : 'no',
				'networks' => $profiles
		);
		
		$profile_bar_buttons = self::draw_social_profiles($options);
		
		
		$profile_bar = '<div class="essbfc-profiles-post essbfc-profile-width-'.esc_attr($profiles_post_width).' essbfc-profiles-post-'.esc_attr($profiles_post_align).' essbfc-content-pos-'.esc_attr($profiles_post_content_pos).'">';
		if ($profiles_post_content != '') {
			$profile_bar .= '<div class="user-content">'.$profiles_post_content.'</div>';
		}
		
		$profile_bar .= '<div class="user-buttons">'.$profile_bar_buttons.'</div>';
		
		$profile_bar .= '</div>';
		
		return $profile_bar;
	}
	
	/**
	 * draw_social_profiles
	 * 
	 * @param array $options
	 * @since 4.0
	 */
	public static function draw_social_profiles($options) {		
		$instance_position = isset ( $options ['position'] ) ? $options ['position'] : '';
		$instance_new_window = 1;
		$instance_nofollow = 1;
		$instance_template = isset ( $options ['template'] ) ? $options ['template'] : 'flat';
		$instance_animation = isset ( $options ['animation'] ) ? $options ['animation'] : '';
		$instance_nospace = isset ( $options ['nospace'] ) ? $options ['nospace'] : 0;
		$instance_networks = isset($options['networks']) ? $options['networks'] : array();
		$instance_networks_text = isset($options['networks_text']) ? $options['networks_text'] : array();
		
		$instance_align = isset($options['align']) ? $options['align'] : '';
		$instance_size = isset($options['size']) ? $options['size'] : '';
		$instance_class = isset($options['class']) ? $options['class'] : '';
		$instance_show_text = isset($options['show_text'])? $options['show_text'] : '';
		$cta = isset($options['cta']) ? $options['cta'] : '';
		$cta_vertical = isset($options['cta_vertical']) ? $options['cta_vertical'] : '';
		$columns = isset($options['columns']) ? $options['columns'] : '';		
		
		if ($instance_show_text == 'true') {
			$instance_show_text = 'yes';
		}
	

		// compatibility with previous template slugs
		if (!empty($instance_template)) {
			if ($instance_template == "lite") {
				$instance_template = "light";
			}
			if ($instance_template == "grey-transparent") {
				$instance_template = "grey";
			}
			if ($instance_template == "color-transparent") {
				$instance_template = "color";
			}
		}
		
		if ($instance_show_text == 'yes') {
			$instance_class .= ' essbfc-profiles-button';
		}
		
		if ($cta == 'yes') {
			$instance_class .= ' essbfc-profiles-cta';
		}
		else {
			$instance_class .= ' essbfc-profiles-nocta';
		}
		
		if ($cta_vertical == 'yes' && $cta == 'yes') {
			$instance_class .= ' essbfc-profiles-ctavert';
		}
		else if ($cta_vertical != 'yes' && $cta == 'yes') {
			$instance_class .= ' essbfc-profiles-ctah';
		}
		
		if ($columns != '') {
			$instance_class .= ' essbfc-profiles-columns essbfc-profiles-columns-'.esc_attr($columns);
		}
		
		// adding additional template classes
		$global_extra_class = true;
		if ($instance_template == 'color' || $instance_template == 'grey' || $instance_template == 'light') {
			$instance_class .= ' essbfc-profiles-design-icon';
			$global_extra_class = false;
		}
		
		if ($instance_template == 'roundcolor' || $instance_template == 'roundgrey' || $instance_template == 'roundlight') {
			$instance_class .= ' essbfc-profiles-design-roundicon';
			$global_extra_class = false;
		}
		
		if ($instance_template == 'outlinecolor' || $instance_template == 'outlinegrey' || $instance_template == 'outlinelight') {
			$instance_class .= ' essbfc-profiles-design-outlineicon';
			$global_extra_class = false;
		}
		
		if ($global_extra_class) {
			$instance_class .= ' essbfc-profiles-design-general';
		}
		
		$names = ESSBSocialProfilesHelper::get_text_of_buttons();
		
		$class_template = (! empty ( $instance_template )) ? " essbfc-template-" . $instance_template : '';
		$class_animation = (! empty ( $instance_animation )) ? " essbfc-icon-" . $instance_animation : '';
		$class_columns = 'essbfc-col-profiles';
		$class_nospace = (intval ( $instance_nospace ) == 1) ? " essbfc-nospace" : "";
				
		$class_position = ($instance_position != '') ? ' essbfc-profiles-bar essbfc-profiles-'.$instance_position : '';
		
		$class_align = !empty($instance_align) ? ' essbfc-profiles-align-'.$instance_align : '';
		$class_size = !empty($instance_size) ? ' essbfc-profiles-size-'.$instance_size : '';
		
		if ($instance_class != '') {
			$class_size .= ' '.$instance_class;
		}
		
		$link_nofollow = (intval ( $instance_nofollow ) == 1) ? ' rel="nofollow"' : '';
		$link_newwindow = (intval ( $instance_new_window ) == 1) ? ' target="_blank"' : '';
		
		// loading animations
		if (! empty ( $class_animation )) {
			essb_resource_builder ()->add_static_footer_css ( ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/css/hover.css', 'essb-social-followers-counter-animations', 'css' );
		}
		
		$code = '';
		// followers main element
		$code .= sprintf ( '<div class="essbfc-container essbfc-container-profiles %1$s%2$s%3$s%4$s%5$s%6$s%7$s">', 
					'', 
					esc_attr($class_columns), esc_attr($class_template), esc_attr($class_nospace), 
					esc_attr($class_position), esc_attr($class_align), esc_attr($class_size) );
		
		
		$code .= '<ul>';
		
		$subscribe_salt = mt_rand();
		$draw_subscribe_form = false;
		$subscribe_design = '';
		
		foreach ( $instance_networks as $social => $url ) {
			$social_display = $social;
			if ($social_display == "instgram") {
				$social_display = "instagram";
			}
			
			/**
			 * Apply additional user texts that can be part of the shortcode or widget
			 */
			$user_text = isset($instance_networks_text[$social]) ? $instance_networks_text[$social] : '';
			if ($user_text != '') {
				$names[$social] = $user_text;
			}

			$social_custom_icon = '';
		
			$code .= sprintf ( '<li class="essbfc-%1$s">', esc_attr($social_display) );
			
			$link_title = isset($names[$social]) ? ' title="'.$names[$social].'"' : '';
			
			$network_text = isset($names[$social]) ? $names[$social] : '';
			
			$network_nofollow = $link_nofollow;
			if ($social == 'rss') {
				$deactivate_trigger = false;
				$deactivate_trigger = apply_filters('essb5_remove_profile_rss_nofollow', $deactivate_trigger);
				
				if ($deactivate_trigger) {
					$network_nofollow = '';
				}
			}
		
			$follow_url = $url;
			if (! empty ( $follow_url )) {
			    if ($social == 'subscribe_form') {
			        $code .= sprintf ( '<a href="#"%2$s%3$s%4$s data-subscribe-form="%1$s" onclick="essb.toggle_subscribe(\'%5$s\'); return false;">', esc_attr($follow_url), $link_newwindow, $network_nofollow, $link_title, $subscribe_salt );
			        $draw_subscribe_form = true;
			        $subscribe_design = $follow_url;
			    }
			    else {
				    $code .= sprintf ( '<a href="%1$s"%2$s%3$s%4$s>', esc_url($follow_url), $link_newwindow, $network_nofollow, $link_title );
			    }
			}
		
			$code .= '<div class="essbfc-network">';
			$code .= sprintf ( '<i class="essbfc-icon essbfc-icon-%1$s%2$s%3$s"></i>', esc_attr($social_display), esc_attr($class_animation), esc_attr($social_custom_icon) );
			if ($instance_show_text == 'yes' && $network_text != '' ) {
				$code .= '<span class="essbfc-profile-cta">'.$network_text.'</span>';
			}
			
			/**
			 * New CTA button design
			 */
			if ($cta == 'yes' && $network_text != '') {
				$code .= '<span class="essbfc-profile-cta_text">'.$network_text.'</span>';
			}
			
			$code .= '</div>';
		
			if (! empty ( $follow_url )) {
				$code .= '</a>';
			}
			$code .= '</li>';
		}
		
		$code .= '</ul>';
		
		$code .= '</div>';
		
		if ($draw_subscribe_form) {
		    if (!class_exists('ESSBNetworks_Subscribe')) {
		        include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe.php');
		    }
		    $code .= ESSBNetworks_Subscribe::draw_popup_subscribe_form($subscribe_design, $subscribe_salt);
		    
		}
		
		return $code;
	}
}

?>