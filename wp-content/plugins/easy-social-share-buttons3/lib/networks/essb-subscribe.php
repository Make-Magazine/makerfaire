<?php

/**
 * Subsctibe Button Class
 *
 * @since 3.6
 *
 * @package EasySocialShareButtons
 * @author  appscreo <http://codecanyon.net/user/appscreo/portfolio>
 */

class ESSBNetworks_Subscribe {
	private static $version = "1.0";	
	public static $assets_registered = false;
	
	public static $recaptcha_loaded = false;
	
	
	public static function register_assets() { 
		if (!self::$assets_registered) {
			self::$assets_registered = true;
			
			if (!self::$recaptcha_loaded) {
			    /**
			     * Register the reCaptcha if enabled on display
			     */
			    if (self::should_add_recaptcha()) {
			        self::prepare_include_recaptha();
			    }
			    
			    if (self::should_add_turnstile()) {
			        self::prepare_include_turnstile();
			    }
			}
		}
		else {
		    if (!self::$recaptcha_loaded) {
		        /**
		         * Register the reCaptcha if enabled on display
		         */
		        if (self::should_add_recaptcha()) {
		            self::prepare_include_recaptha();
		        }
		        
		        if (self::should_add_turnstile()) {
		            self::prepare_include_turnstile();
		        }
		    }
		}
	}
	
	public static function generate_if_needed_agree_check() {
		$code = '';
		
		if (essb_option_bool_value('subscribe_terms')) {
			$text = essb_option_value('subscribe_terms_text');
			$confirmation_url = essb_option_value('subscribe_terms_link');
			$subscribe_terms_link_text = essb_sanitize_option_value('subscribe_terms_link_text');
			
			if ($text == '') {
				$text = esc_html__('I agree to the privacy policy and terms', 'essb');
			}
			
			if ($confirmation_url != '') {
				
				if ($subscribe_terms_link_text != '') {
					$text .= '<a href="'.esc_url($confirmation_url).'" target="_blank" class="confirmation-link-after" rel="nofollow noopener noreferrer">'.$subscribe_terms_link_text.'</a>';
				}
				else {
					$text = '<a href="'.esc_url($confirmation_url).'" target="_blank" class="confirmation-link" rel="nofollow noopener noreferrer">'.$text.'</a>';
				}
			}
			
			$code = '<div class="essb-subscribe-confirm">';
			$code .= '<input type="checkbox" class="essb-subscribe-confirm" id="essb-subscribe-confirm"/><label for="essb-subscribe-confirm">'.do_shortcode(stripslashes($text)).'</label>';
			$code .= '</div>';
		}
		
		/**
		 * Include the google recaptcha
		 */
		if (self::should_add_recaptcha()) {
			$code .= self::generate_recaptcha_field();
		}

		if (self::should_add_turnstile()) {
		    $code .= self::generate_recaptcha_field();
		}
		
		return $code;
	}
	
	/**
	 * Generating subscribers pop-up form
	 * 
	 * @param string $design
	 * @param string $salt
	 * @return string
	 */
	public static function draw_popup_subscribe_form($design = '', $salt = '') {
	    $mode = "mailchimp";
	    	    
	    $output = '';
	    	    
	    $output .= '<div class="essb-subscribe-form essb-subscribe-form-'.esc_attr($salt).' essb-subscribe-form-popup" data-salt="'.esc_attr($salt).'" style="display:none;" data-popup="1">';
	    
	    if ($mode == "form") {
	        $output .= do_shortcode(ESSBGlobalSettings::$subscribe_content);
	    }
	    else {
	        $output .= self::draw_integrated_subscribe_form($salt, false, $design, false, '');
	    }
	    
	    $output .= '<div class="essb-subscribe-form-close" onclick="essb.subscribe_popup_close(\''.$salt.'\');">'.essb_svg_replace_font_icon('close').'</div>';
	    $output .= '</div>';
	    $output .= '<div class="essb-subscribe-form-overlay essb-subscribe-form-overlay-'.esc_attr($salt).'" onclick="essb.subscribe_popup_close(\''.$salt.'\');"></div>';
	    
	    if (!self::$assets_registered) {
	        self::register_assets();
	    }
	    
	    return $output;
	}
	
	public static function draw_subscribe_form($position, $salt, $subscribe_position = '') {
		$output = '';
		$popup_mode = ($position != 'top' && $position != 'bottom' && $position != 'shortcode') ? true : false;
		
		$output .= '<div class="essb-subscribe-form essb-subscribe-form-'.$salt.($popup_mode ? " essb-subscribe-form-popup": " essb-subscribe-form-inline").'" data-popup="'.esc_attr($popup_mode).'" style="display: none;">';
				
		if (ESSBGlobalSettings::$subscribe_function == "form") {
			$output .= do_shortcode(ESSBGlobalSettings::$subscribe_content);
		}
		else {
			$output .= self::draw_integrated_subscribe_form($salt, $popup_mode, '', false, $subscribe_position);
		}
		
		if ($popup_mode) {
		    $output .= '<div class="essb-subscribe-form-close" onclick="essb.subscribe_popup_close(\''.$salt.'\');">'.essb_svg_replace_font_icon('close').'</div>';
		}
		
		$output .= '</div>';
		
		if ($popup_mode) {
			$output .= '<div class="essb-subscribe-form-overlay essb-subscribe-form-overlay-'.esc_attr($salt).'" onclick="essb.subscribe_popup_close(\''.$salt.'\');"></div>';
		}
		
		if (!self::$assets_registered) {
			self::register_assets();
		}
		
		return $output;
	}
	
	public static function draw_inline_subscribe_form($mode = '', $design = '', $is_widget = false, $position = '', $hide_mobile = false) {
		if (empty($mode)) $mode = ESSBGlobalSettings::$subscribe_function;
		$salt = mt_rand();
		
		$output = '<div class="essb-subscribe-form essb-subscribe-form-'.esc_attr($salt).' essb-subscribe-form-inline'.($hide_mobile ? ' essb-subscribe-mobile-hidden': '').'">';
				
		if ($mode == "form") {
			$output .= do_shortcode(ESSBGlobalSettings::$subscribe_content);
		}
		else {
			$output .= self::draw_integrated_subscribe_form($salt, false, $design, $is_widget, $position);
		}
		
		$output .= '</div>';
		
		if (!self::$assets_registered) {
			self::register_assets();
		}
		
		return $output;
	}
	
	
	public static function draw_aftershare_popup_subscribe_form($design = '', $position = '') {
		$mode = "mailchimp";
		$output = '';
		$salt = mt_rand();
	
		$output .= '<div class="essb-subscribe-form essb-aftershare-subscribe-form essb-subscribe-form-'.esc_attr($salt).' essb-subscribe-form-popup" data-salt="'.esc_attr($salt).'" style="display:none;" data-popup="1">';
	
		if ($mode == "form") {
			$output .= do_shortcode(ESSBGlobalSettings::$subscribe_content);
		}
		else {
			$output .= self::draw_integrated_subscribe_form($salt, false, $design, false, $position);
		}
	
		$output .= '<div class="essb-subscribe-form-close" onclick="essb.subscribe_popup_close(\''.$salt.'\');">'.essb_svg_replace_font_icon('close').'</div>';
		$output .= '</div>';
		$output .= '<div class="essb-subscribe-form-overlay essb-subscribe-form-overlay-'.esc_attr($salt).'" onclick="essb.subscribe_popup_close(\''.$salt.'\');"></div>';
	
		if (!self::$assets_registered) {
			self::register_assets();
		}
	
		return $output;
	}
	
	/**
	 * Draw two step subscribe from
	 * ---
	 * draw_inline_subscribe_form_twostep
	 * 
	 * @param string $mode
	 * @param string $design
	 * @param string $open_link_content
	 * @param boolean $is_widget
	 * @return string
	 * @since 3.7
	 */
	public static function draw_inline_subscribe_form_twostep($mode = '', $design = '', $open_link_content = '', $two_step_inline = '', $is_widget = false) {

		// if we have not link content to act like regular inline subscribe form
		if ($open_link_content == '') {
			return ESSBNetworks_Subscribe::draw_inline_subscribe_form($mode, $design);
		}
		
		if (empty($mode)) $mode = ESSBGlobalSettings::$subscribe_function;
		$salt = mt_rand();
	
		$output = '<a href="#" onclick="essb.toggle_subscribe(\''.$salt.'\'); return false;" data-twostep-subscribe="true" data-salt="'.$salt.'" class="essb-twostep-subscribe">'.$open_link_content.'</a>';
		$output .= '<div class="essb-subscribe-form essb-subscribe-form-'.$salt.' essb-subscribe-form-popup" style="display:none;" '.($two_step_inline == 'true' ? 'data-popup="0"' : 'data-popup="1"').'>';
	
		if ($mode == "form") {
			$output .= do_shortcode(ESSBGlobalSettings::$subscribe_content);
		}
		else {
			$output .= self::draw_integrated_subscribe_form($salt, false, $design, $is_widget, 'twostep');
		}
	
		$output .= '<div class="essb-subscribe-form-close" onclick="essb.subscribe_popup_close(\''.$salt.'\');">'.essb_svg_replace_font_icon('close').'</div>';
		$output .= '</div>';
		$output .= '<div class="essb-subscribe-form-overlay essb-subscribe-form-overlay-'.esc_attr($salt).'" onclick="essb.subscribe_popup_close(\''.$salt.'\');"></div>';

		if (!self::$assets_registered) {
			self::register_assets();
		}
	
		return $output;
	}
	
	/**
	 * Generate and display a subscribe form anywhere inside plugin where it is called. The form can generate a popup
	 * or content form. The generation is hold from the optional parameter inside
	 * 
	 * @param unknown_type $salt
	 * @param unknown_type $popup_mode
	 * @param unknown_type $option_design
	 * @param unknown_type $is_widget
	 * @param unknown_type $position
	 */
	public static function draw_integrated_subscribe_form($salt, $popup_mode = false, $option_design = '', $is_widget = false, $position = '') {		
		
		$design_inline = essb_option_value('subscribe_optin_design');
		$design_popup = essb_option_value('subscribe_optin_design_popup');
		
		$user_design = $popup_mode ? $design_popup : $design_inline;
		
		if ($option_design != '') {
			$user_design = $option_design;
		}
				
		
		if ($user_design == '') { $user_design = 'design1'; }
		
		if ($user_design == 'design1') {
			return self::draw_mailchimp_subscribe($salt, $is_widget, $position);
		}
		else if ($user_design == 'design2') {
			return self::draw_mailchimp_subscribe2($salt, $is_widget, $position);
		}
		else if ($user_design == 'design3') {
			return self::draw_mailchimp_subscribe3($salt, $is_widget, $position);
		}
		else if ($user_design == 'design4') {
			return self::draw_mailchimp_subscribe4($salt, $is_widget, $position);
		}
		else if ($user_design == 'design5') {
			return self::draw_mailchimp_subscribe5($salt, $is_widget, $position);
		}
		else if ($user_design == 'design6') {
			return self::draw_mailchimp_subscribe6($salt, $is_widget, $position);
		}
		else if ($user_design == 'design7') {
			return self::draw_mailchimp_subscribe7($salt, $is_widget, $position);
		}
		else if ($user_design == 'design8') {
			return self::draw_mailchimp_subscribe8($salt, $is_widget, $position);
		}
		else if ($user_design == 'design9') {
			return self::draw_mailchimp_subscribe9($salt, $is_widget, $position);
		}
		else {
			return self::draw_user_subscribe_form($salt, $user_design, $is_widget, $position);
		}
	}
	
	public static function draw_user_subscribe_form($salt, $design = '', $is_widget = false, $position = '') {
		if (!function_exists('essb_user_subscribe_form_design')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-user-subscribe-design.php');
		}
		
		return essb_user_subscribe_form_design($salt, $design, $is_widget, $position);
	}
	
	public static function draw_mailchimp_subscribe($salt, $is_widget = false, $position = '') {

		if (!function_exists('essb_subscribe_form_design1')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-design1.php');
		}
		
		return essb_subscribe_form_design1($salt, $is_widget, $position);
	}
	
	public static function draw_mailchimp_subscribe2($salt, $is_widget = false, $position = '') {
		
		if (!function_exists('essb_subscribe_form_design2')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-design2.php');
		}
		
		return essb_subscribe_form_design2($salt, $is_widget, $position);
	}

	public static function draw_mailchimp_subscribe3($salt, $is_widget = false, $position = '') {
		if (!function_exists('essb_subscribe_form_design3')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-design3.php');
		}
		
		return essb_subscribe_form_design3($salt, $is_widget, $position);
	}
	
	
	public static function draw_mailchimp_subscribe4($salt, $is_widget = false, $position = '') {
		if (!function_exists('essb_subscribe_form_design4')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-design4.php');
		}
		
		return essb_subscribe_form_design4($salt, $is_widget, $position);
	}

	public static function draw_mailchimp_subscribe5($salt, $is_widget = false, $position = '') {
		if (!function_exists('essb_subscribe_form_design5')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-design5.php');
		}
		
		return essb_subscribe_form_design5($salt, $is_widget, $position);
	}
	
	public static function draw_mailchimp_subscribe6($salt, $is_widget = false, $position = '') {
		if (!function_exists('essb_subscribe_form_design6')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-design6.php');
		}
	
		return essb_subscribe_form_design6($salt, $is_widget, $position);
	}

	public static function draw_mailchimp_subscribe7($salt, $is_widget = false, $position = '') {
		if (!function_exists('essb_subscribe_form_design7')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-design7.php');
		}
	
		return essb_subscribe_form_design7($salt, $is_widget, $position);
	}

	public static function draw_mailchimp_subscribe8($salt, $is_widget = false, $position = '') {
		if (!function_exists('essb_subscribe_form_design8')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-design8.php');
		}
	
		return essb_subscribe_form_design8($salt, $is_widget, $position);
	}
	
	public static function draw_mailchimp_subscribe9($salt, $is_widget = false, $position = '') {
		if (!function_exists('essb_subscribe_form_design9')) {
			include_once (ESSB3_PLUGIN_ROOT . 'lib/networks/essb-subscribe-design9.php');
		}
	
		return essb_subscribe_form_design9($salt, $is_widget, $position);
	}	
	
	public static function should_add_recaptcha() {
		$recaptcha = essb_option_bool_value('subscribe_recaptcha') && ! empty( essb_sanitize_option_value('subscribe_recaptcha_site') ) && ! empty( essb_sanitize_option_value('subscribe_recaptcha_secret') );
		
		return $recaptcha;
	}
	
	public static function should_add_turnstile() {
	    $recaptcha = essb_option_bool_value('subscribe_turnstile') && ! empty( essb_sanitize_option_value('subscribe_turnstile_site') ) && ! empty( essb_sanitize_option_value('subscribe_turnstile_secret') );
	    
	    return $recaptcha;
	}
	
	public static function prepare_include_recaptha() {
		$recaptcha = essb_option_bool_value('subscribe_recaptcha') && ! empty( essb_sanitize_option_value('subscribe_recaptcha_site') ) && ! empty( essb_sanitize_option_value('subscribe_recaptcha_secret') );
		if ( $recaptcha ) {
			wp_enqueue_script(
				'recaptcha',
				'https://www.google.com/recaptcha/api.js?hl=en&render=explicit',
				array(),
				'2.0',
				true
			);
						
			$args = array();
			$args['recaptchaSitekey'] = sanitize_text_field( essb_sanitize_option_value('subscribe_recaptcha_site') );
			wp_localize_script( 'recaptcha', 'essb_subscribe_recaptcha', $args );
		}
	}
	
	public static function prepare_include_turnstile() {
	    $recaptcha = self::should_add_turnstile();
	    if ( $recaptcha ) {
	        wp_enqueue_script(
	            'recaptcha',
	            'https://challenges.cloudflare.com/turnstile/v0/api.js?compat=recaptcha',
	            array(),
	            '1.0',
	            true
	            );
	        
	        $args = array();
	        $args['recaptchaSitekey'] = sanitize_text_field( essb_sanitize_option_value('subscribe_turnstile_site') );
	        $args['turnstile'] = true;
	        wp_localize_script( 'recaptcha', 'essb_subscribe_recaptcha', $args );
	    }
	}
	
	public static function generate_recaptcha_field() {
		$recaptcha = essb_option_bool_value('subscribe_recaptcha') && ! empty( essb_sanitize_option_value('subscribe_recaptcha_site') ) && ! empty( essb_sanitize_option_value('subscribe_recaptcha_secret') );
		$code = '';
		
		if (self::should_add_turnstile()) {
		    $recaptcha = true;
		}
		
		if ($recaptcha) {
			$code = '<div id="essb-subscribe-captcha-'.mt_rand().'" class="essb-subscribe-captcha"></div>';
		}
		
		return $code;
	}
	
	public static function safe_html_tags() {
	    $allowed_tags = array(
	        'a' => array(
	            'class' => array(),
	            'href'  => array(),
	            'rel'   => array(),
	            'title' => array(),
	        ),
	        'b' => array(),
	        'div' => array(
	            'class' => array(),
	            'title' => array(),
	            'style' => array(),
	        ),
	        'dl' => array(),
	        'dt' => array(),
	        'em' => array(),
	        'h1' => array(),
	        'h2' => array(),
	        'h3' => array(),
	        'h4' => array(),
	        'h5' => array(),
	        'h6' => array(),
	        'i' => array(),
	        'img' => array(
	            'alt'    => array(),
	            'class'  => array(),
	            'height' => array(),
	            'src'    => array(),
	            'width'  => array(),
	        ),
	        'li' => array(
	            'class' => array(),
	        ),
	        'ol' => array(
	            'class' => array(),
	        ),
	        'p' => array(
	            'class' => array(),
	        ),
	        'span' => array(
	            'class' => array(),
	            'title' => array(),
	            'style' => array(),
	        ),
	        'strike' => array(),
	        'strong' => array(),
	        'ul' => array(
	            'class' => array(),
	        ),
	    );
	    
	    return $allowed_tags;
	}
	
	public static function sanitize_html($value) {
	    return wp_kses($value, self::safe_html_tags());
	}
	
	public static function generate_custom_fields() {
	    $output = '';
	    if (has_filter('essb_custom_subscribe_form_fields')) {
	        
	        $custom_fields = array();
	        $custom_fields = apply_filters('essb_custom_subscribe_form_fields', $custom_fields);
	        
	        foreach ($custom_fields as $key => $data) {
	            $type = isset($data['type']) ? $data['type'] : 'input';
	            $required = isset($data['required']) ? $data['required'] : false;
	            
	            if ($type == 'input') {
	                $output .= '<input type="text" class="essb-subscribe-custom'.($required ? ' essb-subscribe-required': '').'" placeholder="'.esc_attr($data['display']).'" data-field="'.$key.'" />';
	            }
	        }
	    }
	    
	    return $output;
	}
}