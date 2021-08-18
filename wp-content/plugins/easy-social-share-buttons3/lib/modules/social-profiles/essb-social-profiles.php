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
	    
	    /**
	     * Loading Module Assets
	     */
	    if (!class_exists('ESSBSocialFollowersCounterAssets')) {
	        // include visual draw class
	        include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-assets.php');
	    }
	    ESSBSocialFollowersCounterAssets::init_profiles();
		
		/**
		 * Floating sidebar
		 */
		if (essb_option_bool_value('profiles_display')) {
		    add_action( 'wp_footer', array($this, 'display_profiles'));
		}
		
		/**
		 * Content Bar
		 */
		if (essb_options_bool_value('profiles_post_display')) {
		    add_filter( 'the_content', array($this, 'display_content_profiles') );
		}
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
		
		$root_classes = [];
		$root_classes[] = 'essb-fc-fixed';
		$root_classes[] = 'essb-fc-fixed-v';
		$root_classes[] = 'essb-fc-fixed-'.esc_attr($profiles_display_position);
		
		$options = array(
				'position' => $profiles_display_position,
				'template' => $profiles_template,
				'animation' => $profiles_animation,
				'nospace' => $profiles_nospace,
				'networks' => $profiles,
				'size' => $profiles_size
				);
		
		echo '<div class="'.implode(' ', $root_classes).'">';
		echo $this->draw_social_profiles($options);
		echo '</div>';
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
		        'align' => $profiles_post_align,
				'size' => $profiles_post_size,
				'template' => $profiles_post_template,
				'animation' => $profiles_post_animation,
				'nospace' => $profiles_post_nospace,
				'cta' => $profiles_post_show_text ? 'yes' : '',
		        'cta_vertical' => $profiles_post_show_text ? 'yes' : '',
		        'columns' => $profiles_post_width,
				'networks' => $profiles
		);
		
		$profile_bar_buttons = self::draw_social_profiles($options);
		
		
		$profile_bar = '<div class="essb-profiles-post essb-profiles-post-'.esc_attr($profiles_post_align).' essb-profiles-content-'.esc_attr($profiles_post_content_pos).'">';
		if ($profiles_post_content != '') {
			$profile_bar .= '<div class="user-content">'.$profiles_post_content.'</div>';
		}
		
		$profile_bar .= '<div class="user-buttons">'.$profile_bar_buttons.'</div>';
		
		$profile_bar .= '</div>';
		
		return $profile_bar;
	}
	
	public static function draw_social_profiles ($options = array()) {
	    
	    if (has_filter('essb_profiles_draw_options')) {
	        $options = apply_filters('essb_profiles_draw_options', $options);
	    }
	    
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
	    $cta = isset($options['cta']) ? $options['cta'] : '';
	    $cta_vertical = isset($options['cta_vertical']) ? $options['cta_vertical'] : '';
	    $instance_columns = isset($options['columns']) ? $options['columns'] : 'row';
	    
	    $link_nofollow = (intval ( $instance_nofollow ) == 1) ? ' rel="noreferrer noopener nofollow"' : '';
	    $link_newwindow = (intval ( $instance_new_window ) == 1) ? ' target="_blank"' : '';	    
	    
	    	    
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
	        
	        /**
	         * Convert deprecated templates
	         */
	        if ($instance_template == 'metro essbfc-template-fancy') { $instance_template = 'metrofancy'; }
	        if ($instance_template == 'metro essbfc-template-bold') { $instance_template = 'metrobold'; }
	    }
	    
	    if (!class_exists('ESSBSocialFollowersCounterDraw')) {
	        include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-draw.php');
	    }
	    
	    /**
	     * Loading Animations
	     */
	    if (! empty ( $instance_animation )) {
	        essb_resource_builder ()->add_static_footer_css ( ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/animations.css', 'essb-social-followers-counter-animations', 'css' );
	    }
	    
	    /**
	     * Load the SVG icons if not present
	     */
	    if (!class_exists('ESSB_SVG_Icons')) {
	        include_once (ESSB3_CLASS_PATH . 'assets/class-svg-icons.php');
	    }
	    
	    /**
	     * Building core classes
	     */
	    $root_classes = array();
	    $root_classes[] = 'essb-social-followers-variables';
	    $root_classes[] = 'essb-fc-grid';
	    $root_classes[] = 'essb-profiles';
	    
	    if ($cta == 'yes' && $instance_align != '') { $instance_align .= '-button'; }
	    
	    if (!empty($instance_template)) { $root_classes[] = 'essb-fc-template-'.esc_attr($instance_template); }
	    if (!empty($instance_animation)) { $root_classes[] = 'essb-fc-animation-'.esc_attr($instance_animation); }
	    if (!empty($instance_columns)) { $root_classes[] = 'essb-fc-columns-'.esc_attr($instance_columns); }
	    if ($instance_nospace == 1) { $root_classes[] = 'essb-fc-nospace'; }
	    if (!empty($instance_class)) { $root_classes[] = $instance_class; }
	    if (!empty($instance_align)) { $root_classes[] = 'essb-fc-profile-align-'.esc_attr($instance_align); }
	    if (!empty($instance_size)) { $root_classes[] = 'essb-fc-profile-size-'.esc_attr($instance_size); }
	    if ($cta == 'yes' && $cta_vertical != 'yes') { $root_classes[] = 'essb-fc-profile-h'; }
	    if ($cta == 'yes' && $cta_vertical == 'yes') { $root_classes[] = 'essb-fc-profile-v'; }
	    
	    $additional_classes = self::additional_block_classes($instance_template);
	    if ($additional_classes != '') { $root_classes[] = $additional_classes; }
	    
	    $code = '';
	    
	    /**
	     * Generate parent element class
	     */
	    $code .= '<div class="'.implode(' ', $root_classes).'">';
	    
	    /**
	     * Begin network drawing
	     */
	    $names = ESSBSocialProfilesHelper::get_text_of_buttons();
	    foreach ($instance_networks as $social => $url) {
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
	        
	        $social_icon = ESSB_SVG_Icons::get_icon($social_display);
	        
	        $network_text = isset($names[$social]) ? $names[$social] : '';
	        
	        if ($cta != 'yes') { $network_text = ''; }
	        
	        $opts = array(
	            'block_classes' => 'essb-fc-network-'.$social_display .' '. ESSBSocialFollowersCounterDraw::block_template_class($instance_template, $social_display),
	            'block_atts' => '',
	            'icon_classes' => ESSBSocialFollowersCounterDraw::icon_template_class($instance_template, $social_display),
	            'url_atts' => $link_nofollow.$link_newwindow
	        );
	        
	        $opts['block_classes'] = str_replace( 'essb-fc-tiny-block', '', $opts['block_classes']);
	        
	        if ($cta == 'yes' && $cta_vertical != 'yes') {  $opts['block_classes'] .= ' essb-fc-tiny-block'; }
	        	        
	        $code .= self::generate_single_block($social_icon, $network_text, $url, $opts);
	        
	    }
	    
	    $code .= '</div>';
	    
	    return $code;
	}
	
	public static function additional_block_classes ( $template = '' ) {
	    $r = '';
	    
	    switch ($template) {
	        case 'color':
	        case 'roundcolor':
	        case 'outlinecolor':
	        case 'grey':
	        case 'roundgrey':
	        case 'outlinegrey': 
	        case 'light':
	        case 'roundlight':
	        case 'outlinelight':
	        case 'metrofancy':
	        case 'minimal':
	        case 'boxed':
	            $r = 'essb-profiles-iconic';
	            break;	
	            
	        case 'tinycolor':
	        case 'tinygrey':
	        case 'tinylight':
	        case 'tinymodern':
	            $r = 'essb-profiles-smallfont';
	            break;
	    }
	    
	    return $r;
	}
	
	/**
	 * Generate single network block
	 *
	 * @param string $icon
	 * @param string $value
	 * @param string $text
	 * @param string $url
	 * @param string $extra_classes
	 * @param string $extra_atts
	 * @return string
	 */
	public static function generate_single_block($icon = '', $text = '', $url = '', $opts = array()) {
	    
	    $extra_classes = isset($opts['block_classes']) ? $opts['block_classes'] : '';
	    $extra_atts = isset($opts['block_atts']) ? $opts['block_atts'] : '';
	    $url_atts = isset($opts['url_atts']) ? ' '. $opts['url_atts'] : '';
	    $icon_classes = isset($opts['icon_classes']) ? $opts['icon_classes'] : '';
	    
	    if ($icon_classes != '') {
	        $icon_classes = ' class="'.esc_attr($icon_classes). '"';
	    }
	    
	    $output = '<div class="essb-fc-block '.esc_attr($extra_classes).'"';
	    if ($extra_atts != '') {
	        $output .= ' '.$extra_atts;
	    }
	    $output .= '>';
	    
	    $output .= '<div class="essb-fc-block-icon"><i'.$icon_classes.'>'.$icon.'</i></div>';
	    
	    if ($text != '') {
    	    $output .= '<div class="essb-fc-block-details">';
    	    $output .= '<span class="text">'.($text != '' ? esc_attr($text) : '&nbsp;').'</span>';
    	    $output .= '</div>';
	    }
	    
	    if ($url != '') {
	        $output .= '<a href="'.esc_url($url).'"'.$url_atts.'></a>';
	    }
	    
	    $output .= '</div>'; // essb-fc-block
	    
	    return $output;
	}
}

?>