<?php

/**
 * ESSBSocialFollowersCounterDraw
 *
 * Followers counter draw engine
 *
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 3.4
 *
 */
class ESSBSocialFollowersCounterDraw {

	public static function followers_number($count) {
		$format = essb_followers_option ( 'format' );

		$result = "";

		switch ($format) {
			case 'full' :
				$result = number_format ( $count, 0, '', ',' );
				break;
			case 'fulldot' :
				$result = number_format ( $count, 0, '', '.' );
				break;
			case 'fullspace':
			    $result = number_format( $count, 0, '', ' ');
			    break;
			case 'short' :
				$result = self::followers_number_shorten ( $count );
				break;
			default :
				$result = $count;
				break;
		}

		return $result;
	}

	public static function followers_number_shorten($count) {
		if (! is_numeric ( $count ))
			return $count;

		if ($count >= 1000000) {
			return round ( ($count / 1000) / 1000, 1 ) . "M";
		} elseif ($count >= 100000) {
			return round ( $count / 1000, 0 ) . "k";
		} else if ($count >= 1000) {
			return round ( $count / 1000, 1 ) . "k";
		} else {
			return @number_format ( $count );
		}
	}
	
	public static function draw_followers_sidebar($options) {
	    $instance_position = isset ( $options ['position'] ) ? $options ['position'] : '';
	    $instance_button = isset($options['button']) ? $options['button'] : 'h';
	    $instance_width = isset($options['width']) ? $options['width'] : '0';
	    $instance_show_total = isset($options['total']) ? $options['total'] : 0;
	    
	    $options['nofollow'] = 1;
	    $options['new_window'] = 1;
	    $options['columns'] = 1;
	    $options['force_tiny'] = true;
	        
	    if (intval($instance_show_total) == 1) {
	        $options['show_total'] = 1;
	        $options['total_type'] = 'button_single';
	    }
	    
	    $root_classes = [];
	    $root_classes[] = 'essb-fc-fixed';
	    $root_classes[] = 'essb-fc-fixed-'.esc_attr($instance_position);
	    $root_classes[] = 'essb-fc-fixed-'.esc_attr($instance_button);
	    
	    if ($instance_width != '') {
	        echo '<style type="text/css">';
	        echo '.essb-social-followers-variables { --essb-sf-followers-fixed-width: '.$instance_width.'px; }';
	        echo '</style>';
	    }
	    
	    echo '<div class="'.implode(' ', $root_classes).'">';
	    self::draw_followers($options, false, false);
	    echo '</div>';	    
	}

	/**
	 * Convert string/numeric value to boolean output
	 * 
	 * @param unknown $value
	 * @return number
	 */
	public static function covert_boolean_value($value) {
		$r = 0;

		if ($value === 'yes' || $value === 'true' || intval($value) == 1) {
			$r = 1;
		}

		return $r;
	}
	
	/**
	 * Apply default options over user
	 * 
	 * @param unknown $options
	 * @param array $defaults
	 * @return unknown
	 */
	public static function apply_defaults($options, $defaults = array()) {
	    foreach ($defaults as $key => $value) {
	        if (!isset($options[$key])) {
	            $options[$key] = $value;
	        }
	    }
	    
	    return $options;
	}
	
	/**
	 * Read internal option value
	 * 
	 * @param array $options
	 * @param string $param
	 * @param string $convert_boolean
	 * @return number|string|unknown
	 */
	public static function get_internal_option_value($options = array(), $param = '', $convert_boolean = false) {
	    $value = isset($options[$param]) ? $options[$param] : '';
	    
	    return $convert_boolean ? self::covert_boolean_value($value) : $value;
	}
	
    /**
     * 
     * @param string $template
     * @param string $network
     */
	public static function block_template_class($template = '', $network = '') {
	    $r = '';
	    
	    /**
	     * Bind network colors
	     */
	    if ($network == 'total_followers') { $network = 'love'; }
	    
	    switch ($template) {
	        case 'flat' :
	        case 'metro':
	        case 'gradient':
	            $r = 'essb-fc-bg-'.esc_attr($network);
	            break;
	        case 'dark':
	            $r = 'essb-fc-bg-dark';
	            break;
	        case 'tinycolor':
	            $r = 'essb-fc-tiny-block essb-fc-bg-'.esc_attr($network);
	            break;
	        case 'tinygrey':
	            $r = 'essb-fc-tiny-block essb-fc-bg-dark';
	            break;
	        case 'tinylight':
	            $r = 'essb-fc-tiny-block essb-fc-bg-light';
	            break;
	        case 'tinymodern':
	            $r = 'essb-fc-tiny-block essb-fc-hbg-'.esc_attr($network) . ' essb-fc-c-' . esc_attr($network);
	            break;
	        case 'modern':
	            $r = 'essb-fc-hbg-'.esc_attr($network). ' essb-fc-border-bottom essb-fc-border-'.esc_attr($network);
                break;
	        case 'modernlight':
	            $r = 'essb-fc-hbg-'.esc_attr($network) . ' essb-fc-c-' . esc_attr($network);
	            break;
	        case 'modernlight':
	            $r = 'essb-fc-hbg-'.esc_attr($network) . ' essb-fc-c-' . esc_attr($network);
	            break;
	        case 'metrofancy':
	            $r = 'essb-fc-bg-'.esc_attr($network) .' essb-fc-icon-fancy';
	            break;
	        case 'metrobold':
	            $r = 'essb-fc-bg-'.esc_attr($network) .' essb-fc-icon-boldbg';
	            break;
	        case 'metrooutline':
	        case 'modernoutline':
	            $r = 'essb-fc-hbg-'.esc_attr($network) . ' essb-fc-c-' . esc_attr($network). ' essb-fc-border-' . esc_attr($network);
	            break;
	        case 'boxed':
	            $r = 'essb-fc-border-'.esc_attr($network);
	            break;
	    }
	    
	    return $r;
	}
	
	/**
	 * Get network specific icon color
	 * 
	 * @param string $template
	 * @param string $network
	 * @return string
	 */
	public static function icon_template_class($template = '', $network = '') {
	    $r = '';
	    
	    /**
	     * Bind network colors
	     */
	    if ($network == 'total_followers') { $network = 'love'; }
	    
	    switch ($template) {
	        case 'color' :
	        case 'modern':
	            $r = 'essb-fc-c-'.esc_attr($network);
	            break;
	        case 'roundcolor':
	            $r = 'essb-fc-bg-'.esc_attr($network) .' essb-fc-icon-circle essb-fc-icon-light';
	            break;
	        case 'roundgrey':
	            $r = 'essb-fc-bg-dark essb-fc-icon-circle essb-fc-icon-light';
	            break;
	        case 'outlinecolor' :
	            $r = 'essb-fc-c-'.esc_attr($network) . ' essb-fc-icon-circle essb-fc-icon-circle-border essb-fc-border-'.esc_attr($network);
	            break;
	        case 'outlinegrey' :
	            $r = 'essb-fc-c-dark essb-fc-icon-circle essb-fc-icon-circle-border essb-fc-border-dark';
	            break;	            
	        case 'roundlight':
	            $r = 'essb-fc-bg-light essb-fc-icon-circle essb-fc-icon-semidark';
	            break;
	        case 'outlinelight' :
	            $r = 'essb-fc-c-light essb-fc-icon-circle essb-fc-icon-circle-border essb-fc-border-light';
	            break;
	        case 'modernlight':
	        case 'tinymodern':
	            $r = 'essb-fc-c-'.esc_attr($network);
	            break;
	        case 'metrofancy':
	            $r = 'essb-fc-icon-circle';
	            break;
	        case 'metrobold':
	            $r = 'essb-fc-icon28';
	            break;
	        case 'minimal':
	        case 'boxed':
	            $r = 'essb-fc-bg-'.esc_attr($network) . ' essb-fc-icon-light essb-fc-icon-block';
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
	public static function generate_single_block($icon = '', $value = '', $text = '', $url = '', $opts = array()) {
	    
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
	    
	    /**
	     * Convert to short value
	     */
	    if ($value != '' && intval($value) > 0) {
	        $value = self::followers_number($value);
	    }
	    
	    $output .= '<div class="essb-fc-block-icon"><i'.$icon_classes.'>'.$icon.'</i></div>';
	    $output .= '<div class="essb-fc-block-details">';
	    $output .= '<span class="count">'.($value != '' ? esc_attr($value) : '&nbsp;').'</span>';
	    $output .= '<span class="text">'.($text != '' ? esc_attr($text) : '&nbsp;').'</span>';
	    $output .= '</div>';
	    
	    if ($url != '') {
	        $output .= '<a href="'.esc_url($url).'"'.$url_atts.'></a>';
	    }
	    	    
	    $output .= '</div>'; // essb-fc-block
	    
	    return $output;
	}
	
	/**
	 * Generate a cover box over the profiles
	 * 
	 * @param array $options
	 */
	public static function generate_cover($options = array()) {	    	    
	    $coverbox_style = isset($options['style']) ? $options['style'] : '';
	    $coverbox_bg = isset($options['bg']) ? $options['bg'] : '';
	    $coverbox_profile = isset($options['profile']) ? $options['profile'] : '';
	    $coverbox_title = isset($options['title']) ? $options['title'] : '';
	    $coverbox_desc = isset($options['desc']) ? $options['desc'] : '';
	    $coverbox_align = isset($options['align']) ? $options['align'] : '';
	    
	    $parent_classes = [];
	    $parent_classes[] = 'essb-fc-cover';
	    
	    if ($coverbox_align != '') {
	        $parent_classes[] = ' essb-fc-cover-align-'.esc_attr($coverbox_align);
	    }	    
	    
	    if ($coverbox_style != '') {
	        $parent_classes[] = 'essb-fc-style-'.esc_attr($coverbox_style);
	    }
	    
	    
	    if ($coverbox_title != '') {
	        $coverbox_title = stripslashes($coverbox_title);
	        $coverbox_title = do_shortcode($coverbox_title);
	    }
	    
	    if ($coverbox_desc != '') {
	        $coverbox_desc = stripslashes($coverbox_desc);
	        $coverbox_desc = do_shortcode($coverbox_desc);
	    }
	    
	    echo '<div class="'.implode(' ', $parent_classes).'"'.($coverbox_bg != '' ? ' style="background:'.esc_attr($coverbox_bg).';"' : '').'>';
	    
	    if ($coverbox_profile != '') {
	        echo '<img src="'.esc_url($coverbox_profile).'" class="profile"/>';
	    }
	    if ($coverbox_title != '') {
	        echo '<div class="title">'.$coverbox_title.'</div>';
	    }
	    
	    if ($coverbox_desc != '') {
	        echo '<div class="desc">'.$coverbox_desc.'</div>';
	    }
	    
	    echo '</div>';
	}
	
	public static function get_layout_builder_coverbox() {
	    $r = array();
	    
	    $r['style'] = essb_followers_option('coverbox_style');
	    $r['bg'] = essb_followers_option('coverbox_bg');
	    $r['profile'] = essb_followers_option('coverbox_profile');
	    $r['title'] = essb_followers_option('coverbox_title');
	    $r['desc'] = essb_followers_option('coverbox_desc');

	    return $r;
	}
	
	public static function get_profilebar_coverbox() {
	    $r = array();
	    
	    $r['style'] = essb_followers_option('profile_c_style');
	    $r['align'] = essb_followers_option('profile_c_align');
	    $r['bg'] = essb_followers_option('profile_c_bg');
	    $r['profile'] = essb_followers_option('profile_c_profile');
	    $r['title'] = essb_followers_option('profile_c_title');
	    $r['desc'] = essb_followers_option('profile_c_desc');
	    
	    return $r;
	}
	
	/**
	 * Get text below number for single social network
	 * 
	 * @param unknown $social
	 * @return string|unknown
	 */
	public static function get_followers_text($social) {
	    $social_followers_text = essb_followers_option ( $social . '_text' );
	    
	    if (has_filter('essb_followers_counter_network_text')) {
	        $social_followers_text = apply_filters('essb_followers_counter_network_text', $social, $social_followers_text);
	    }
	    
	    return $social_followers_text;
	}

	/**
	 * Output Social Followers Counter
	 * 
	 * @param array $options
	 * @param string $draw_title
	 * @param string $layout_builder
	 */
	public static function draw_followers($options = array(), $draw_title = false, $layout_builder = false) {
	    
	    if (has_filter('essb_followers_draw_options')) {
	        $options = apply_filters('essb_followers_draw_options', $options);
	    }
	    
	    $hide_title = isset ( $options ['hide_title'] ) ? $options ['hide_title'] : 0;
	    if (intval ( $hide_title ) == 1) {
	        $draw_title = false;
	    }

	    $defaults = array(
	       'total_type' => 'button_single',
	       'template' => 'flat',
	       'columns' => '3'
	    );
	    
	    $options = self::apply_defaults($options, $defaults);
	    
	    /**
	     * Reading the default options
	     */
	    $instance_title = self::get_internal_option_value($options, 'title');
	    $instance_new_window = self::get_internal_option_value($options, 'new_window', true);
	    $instance_nofollow = self::get_internal_option_value($options, 'nofollow', true);
	    $instance_show_total = self::get_internal_option_value($options, 'show_total', true);
	    $instance_total_type = self::get_internal_option_value($options, 'total_type');
	    $instance_columns = self::get_internal_option_value($options, 'columns');
	    $instance_template = self::get_internal_option_value($options, 'template');
	    $instance_animation = self::get_internal_option_value($options, 'animation');
	    $instance_bgcolor = self::get_internal_option_value($options, 'bgcolor');
	    $instance_nospace = self::get_internal_option_value($options, 'nospace', true);

	    $instance_hide_value = self::get_internal_option_value($options, 'hide_value', true);
	    $instance_hide_text = self::get_internal_option_value($options, 'hide_text', true);
	    	    
	    /**
	     * Adding support for custom network list
	     */
	    $instance_show_user_networks = false;
	    $instance_user_networks = array();
	    if (isset($options['networks']) && $options['networks'] != '') {
	        $instance_show_user_networks = true;
	        $instance_user_networks = explode(',', $options['networks']);
	    }
	    
	    if ($layout_builder) {
	        $instance_columns = essb_followers_option('layout_cols');
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
	    
	    /**
	     * Convert deprecated templates
	     */
	    if ($instance_template == 'metro essbfc-template-fancy') { $instance_template = 'metrofancy'; }
	    if ($instance_template == 'metro essbfc-template-bold') { $instance_template = 'metrobold'; }
	    
	    $root_classes = array();
	    $root_classes[] = 'essb-social-followers-variables';
	    $root_classes[] = 'essb-fc-grid';
	    $root_classes[] = 'essb-followers';
	    
	    if (!empty($instance_template)) { $root_classes[] = 'essb-fc-template-'.esc_attr($instance_template); }
	    if (!empty($instance_animation)) { $root_classes[] = 'essb-fc-animation-'.esc_attr($instance_animation); }
	    if (!empty($instance_columns)) { $root_classes[] = 'essb-fc-columns-'.esc_attr($instance_columns); }
	    if ($instance_nospace == 1) { $root_classes[] = 'essb-fc-nospace'; }
	    if ($instance_hide_value == 1) { $root_classes[] = 'essb-fc-novalue'; }
	    if ($instance_hide_text == 1) { $root_classes[] = 'essb-fc-notextvalue'; }
	    
	    $style_bgcolor = (! empty ( $instance_bgcolor )) ? ' style="background-color:' . esc_attr($instance_bgcolor) . ';"' : '';
	    
	    $link_nofollow = (intval ( $instance_nofollow ) == 1) ? ' rel="noreferrer noopener nofollow"' : '';
	    $link_newwindow = (intval ( $instance_new_window ) == 1) ? ' target="_blank"' : '';
	    
	    /**
	     * Loading Animations
	     */
	    if (! empty ( $instance_animation )) {
	        essb_resource_builder ()->add_static_footer_css ( ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/animations.css', 'essb-social-followers-counter-animations', 'css' );
	    }
	    
	    /** 
	     * Reading the followers
	     */
	    $followers_count = essb_followers_counter ()->get_followers ();
	    
	    /**
	     * Total counter aggregator
	     */
	    $display_total = (intval ( $instance_show_total ) == 1) ? true : false;
	    $total_followers = 0;
	    if ($display_total || $layout_builder) {
	        foreach ( $followers_count as $network => $count ) {
	            if (intval ( $count ) > 0) {
	                $total_followers += intval ( $count );
	            }
	        }
	    }	   
	    
	    $subscribe_salt = mt_rand();
	    $draw_subscribe_form = false;
	    $subscribe_design = '';
	    
	    echo '<div class="essb-fc-root">';
	    
	    if ($draw_title && ! empty ( $instance_title )) {
	        printf ( '<h3>%1$s</h3>', $instance_title );
	    }	    
	    
	    if ($display_total && $instance_total_type == "text_before") {	        
	        printf ( '<div class="essb-fc-totalastext essb-fc-beforeblock"><label class="count">%1$s</label> <label class="text">%2$s</label></div>', self::followers_number ( $total_followers ), essb_followers_option ( 'total_text' ) );
	    }
	    
	    /** 
	     * Generate the cover box for layout builder
	     */
	    if ($layout_builder && essb_followers_option('coverbox_show')) {
	        self::generate_cover(self::get_layout_builder_coverbox());
	    }
	    else if (isset($options['profilebar']) && essb_followers_option('profile_c_show')) {
	        self::generate_cover(self::get_profilebar_coverbox());
	    }
	    
	    /**
	     * Generate parent element class
	     */
	    echo '<div class="'.implode(' ', $root_classes).'"'.($style_bgcolor != '' ? $style_bgcolor : '').'>';
	    
	    /**
	     * Generate list of all social networks
	     */
	    $display_networks = $instance_show_user_networks && count($instance_user_networks) > 0 ? $instance_user_networks : essb_followers_counter ()->active_social_networks ();
	    
	    /**
	     * Load the SVG icons if not present
	     */
	    if (!class_exists('ESSB_SVG_Icons')) {
	        include_once (ESSB3_CLASS_PATH . 'assets/class-svg-icons.php');
	    }
	    
	    foreach ( $display_networks as $social ) {
	        $social_followers_text = self::get_followers_text($social);
	        $social_followers_counter = isset ( $followers_count [$social] ) ? $followers_count [$social] : 0;
	        $social_follow_url = essb_followers_counter ()->create_follow_address ( $social );
	        
	        if (has_filter('essb_followers_counter_url')) {
	            $social_follow_url = apply_filters('essb_followers_counter_url', $social, $social_follow_url);
	        }
	        
	        $custom_li_class = '';
	        
	        if ($layout_builder) {
	            $network_columns = essb_followers_option('layout_cols_'.$social);
	            if ($network_columns != '') {
	                $custom_li_class = ' blocksize-'.$network_columns;
	            }
	        }
	        
	        $social_display = $social;
	        if ($social_display == "instgram") {
	            $social_display = "instagram";
	        }
	        
	        $social_icon = ESSB_SVG_Icons::get_icon($social_display);
	        
	        $opts = array(
	            'block_classes' => 'essb-fc-network-'.$social_display .' '. self::block_template_class($instance_template, $social_display).$custom_li_class,
	            'block_atts' => '',
	            'icon_classes' => self::icon_template_class($instance_template, $social_display),
	            'url_atts' => $link_nofollow.$link_newwindow
	        );
	        
	        echo self::generate_single_block($social_icon, $social_followers_counter, $social_followers_text, 
	            $social_follow_url, $opts);
	        
	    }
	    
	    /**
	     * Display total as a single button
	     */
	    if ($display_total && $instance_total_type == 'button_single') {
	        $social_display = 'total_followers';
	        $social_followers_counter = $total_followers;
	        $social_followers_text = self::get_followers_text('total');
	        
	        $custom_li_class = '';
	        
	        $social_icon = ESSB_SVG_Icons::get_icon($social_display);
	        
	        $opts = array(
	            'block_classes' => 'essb-fc-network-'.$social_display .' '. self::block_template_class($instance_template, $social_display).$custom_li_class,
	            'block_atts' => '',
	            'icon_classes' => self::icon_template_class($instance_template, $social_display),
	            'url_atts' => $link_nofollow.$link_newwindow
	        );
	        
	        echo self::generate_single_block($social_icon, $social_followers_counter, $social_followers_text,
	            $social_follow_url, $opts);
	        
	    }
	    
	    
	    echo '</div>'; // essb-fc-grid
	    
	    if ($display_total && $instance_total_type == "text_after") {
	        printf ( '<div class="essb-fc-totalastext essb-fc-afterblock"><label class="count">%1$s</label> <label class="text">%2$s</label></div>', self::followers_number ( $total_followers ), essb_followers_option ( 'total_text' ) );
	    }
	    
	    
	    echo '</div>'; // essb-fc-root
	}
	

    /**
     * Generate followers bar
     * 
     * @since 7.7
     * @return string
     */
	public static function draw_followers_bar() {
	    
	    $options = array();
	    $options['title'] = '';
	    $options['new_window'] = 1;
	    $options['nofollow'] = 1;
	    $options['show_total'] = 0;
	    $options['total_type'] = 'button_single';
	    $options['profilebar'] = 1;
	    
	    $options['columns'] = essb_followers_option('profile_cols');
	    $options['template'] = essb_followers_option('profile_template');
	    $options['animation'] = essb_followers_option('profile_animation');
	    $options['nospace'] = essb_followers_option('profile_nospace');
	    $options['template'] = essb_followers_option('profile_template');

	    $options['hide_value'] = essb_followers_option('profile_nonumber');
	    $options['hide_text'] = essb_followers_option('profile_notext');


	    if ($options['columns'] == '') { $options['columns'] = 'flex'; }
	    
	    ob_start();

	    self::draw_followers($options);
	    $html = ob_get_contents();
	    ob_end_clean();
	    
	    return $html;
	}

}
