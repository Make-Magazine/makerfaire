<?php

if (!defined('ESSB3_OFOB_PLUGIN_ROOT')) {
	define ( 'ESSB3_OFOB_PLUGIN_ROOT', ESSB3_PLUGIN_ROOT . 'lib/modules/optin-booster/' );
}

if (!defined('ESSB3_OFOB_PLUGIN_URL')) {
	define ( 'ESSB3_OFOB_PLUGIN_URL', ESSB3_PLUGIN_URL . '/lib/modules/optin-booster/' );
}

if (!defined('ESSB3_OFOB_OPTIONS_NAME')) {
	define ( 'ESSB3_OFOB_OPTIONS_NAME', 'essb3-ofob' );
}

global $essb3ofob_options;

class ESSBOptinBooster {
	private static $_instance;
	private $version = "2.0";
	
	function __construct() {
		
		global $essb3ofob_options;
		
		$essb3ofob_options = get_option ( ESSB3_OFOB_OPTIONS_NAME );
				
		$demo_mode = isset($_REQUEST['optin_booster']) ? $_REQUEST['optin_booster'] : '';
		if ($demo_mode == 'true') {
			$essb3ofob_options['ofob_time'] = 'true';
			$essb3ofob_options['ofob_scroll'] = 'true';
			$essb3ofob_options['ofob_exit'] = 'true';
		}
		
		if ($this->option_bool_value('ofob_time') || $this->option_bool_value('ofob_scroll') || 
				$this->option_bool_value('ofob_exit') || $this->option_bool_value('ofob_manual')) {
				    
			if (!$this->option_bool_value('ofob_manual_mode')) {    
                add_action ( 'wp_footer', array(&$this, 'draw_forms'), 99);
			}
			add_action('init', array(&$this, 'load_assets'), 99);
			add_shortcode('booster-subscribe-form', array(&$this, 'manually_load_form'));
		}		
	}
	
	/**
	 * Check if module can run on the current post/page
	 * @return boolean
	 */
	function is_deactivated_on() {
		global $essb3ofob_options;
	
		if (is_admin ()) {
			return true;
		}
		
		if (is_search() || is_feed()) {
			return true;
		}
	
		$is_deactivated = false;
		$exclude_from = isset ( $essb3ofob_options ['ofob_exclude'] ) ? $essb3ofob_options ['ofob_exclude'] : '';
		if (! empty ( $exclude_from )) {
			$excule_from = explode ( ',', $exclude_from );
				
			$excule_from = array_map ( 'trim', $excule_from );
			if (in_array ( get_the_ID (), $excule_from, false )) {
				$is_deactivated = true;
			}
		}
		
		if ($this->option_bool_value('deactivate_homepage')) {
			if (is_home() || is_front_page()) {
				$is_deactivated = true;
			}
		}
		
		if (essb_option_bool_value('optin_booster_activate_posttypes')) {
			$posttypes = $this->option_value('posttypes');
			if (!is_array($posttypes)) {
				$posttypes = array();
			}
			
			if (!is_singular($posttypes)) {
				$is_deactivated = true;
			}
		}
		
		return $is_deactivated;
	}
	
	public function load_assets() {
		if (function_exists('essb_resource_builder')) {
			essb_resource_builder()->add_static_resource_footer(ESSB3_OFOB_PLUGIN_URL . 'assets/essb-optin-booster.min.js', 'essb-optin-booster', 'js');
		}
	}
	
	public function option_value($param) {
		global $essb3ofob_options;
		
		$value = isset($essb3ofob_options[$param]) ? $essb3ofob_options[$param] : '';
		
		return $value;
	}
	
	public function option_bool_value($param) {
		global $essb3ofob_options;
		
		$value = isset($essb3ofob_options[$param]) ? $essb3ofob_options[$param] : '';
		
		if ($value == 'true') {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Get static instance of class
	 *
	 * @return ESSB_Manager
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
	
	public function manually_load_form() {
	    add_action ( 'wp_footer', array(&$this, 'draw_forms'), 99);
	}
	
	public function draw_forms() {
		
		if (essb_is_plugin_deactivated_on()) {
			return;
		}
		
		if ($this->is_deactivated_on()) {
			return;
		}
		
		$ofob_single = $this->option_bool_value('ofob_single');
		$ofob_single_time = $this->option_value('ofob_single_time');
		$ofob_creditlink = $this->option_bool_value('ofob_creditlink');
		
		if ($this->option_bool_value('ofob_time')) {
			$ofob_time_delay = $this->option_value('ofob_time_delay');
			$of_time_design = $this->option_value('of_time_design');
			$of_time_bgcolor = $this->option_value('of_time_bgcolor');
			
			if ($ofob_time_delay != '') {
				$callback = ' data-delay="'.esc_attr($ofob_time_delay).'" data-single="'.esc_attr($ofob_single).'"';
				
				if ($ofob_single_time != '') {
					$callback .= ' data-single-days="'.esc_attr($ofob_single_time).'"';
				}
				
				$this->draw_form_code('time', $of_time_design, $of_time_bgcolor, $callback, $ofob_creditlink);
			}
		}
		
		if ($this->option_bool_value('ofob_scroll')) {
			$ofob_scroll_percent = $this->option_value('ofob_scroll_percent');
			$of_scroll_design = $this->option_value('of_scroll_design');
			$of_scroll_bgcolor = $this->option_value('of_scroll_bgcolor');
				
			if ($ofob_scroll_percent != '') {
				$callback = ' data-scroll="'.esc_attr($ofob_scroll_percent).'" data-single="'.esc_attr($ofob_single).'"';
				
				if ($ofob_single_time != '') {
					$callback .= ' data-single-days="'.esc_attr($ofob_single_time).'"';
				}
				
				$this->draw_form_code('scroll', $of_scroll_design, $of_scroll_bgcolor, $callback, $ofob_creditlink);
			}
		}
		
		if ($this->option_bool_value('ofob_exit')) {			
			$of_exit_design = $this->option_value('of_exit_design');
			$of_exit_bgcolor = $this->option_value('of_exit_bgcolor');				
			$callback = ' data-exit="1" data-single="'.esc_attr($ofob_single).'"';
			
			if ($ofob_single_time != '') {
				$callback .= ' data-single-days="'.esc_attr($ofob_single_time).'"';
			}
				
			$this->draw_form_code('exit', $of_exit_design, $of_exit_bgcolor, $callback, $ofob_creditlink);
		}
		
		if ($this->option_bool_value('ofob_manual')) {
			$of_manual_design = $this->option_value('of_manual_design');
			$of_manual_bgcolor = $this->option_value('of_manual_bgcolor');
			$of_manual_selector = $this->option_value('of_manual_selector');
			$callback = ' data-manual="1" data-manual-selector="'.esc_attr($of_manual_selector).'"';
			
			$this->draw_form_code('manual', $of_manual_design, $of_manual_bgcolor, $callback, $ofob_creditlink);
		}
	}
	
	public function draw_form_code($event = '', $design = '', $overlay_color = '', $event_fire = '', $credit_link = false) {
		$output = '';
		
		/**
		 * @since 7.3.1		 
		 */
		$ofob_deactivate_mobile = $this->option_bool_value('ofob_deactivate_mobile');
		if (!$ofob_deactivate_mobile && essb_option_bool_value('subscribe_css_deactivate_mobile')) {
		    $ofob_deactivate_mobile = true;
		}
		
		$close_type = $this->option_value('of_'.$event.'_close');
		$close_color = $this->option_value('of_'.$event.'_closecolor');
		$close_text = $this->option_value('of_'.$event.'_closetext');
		
		$css_color = '';
		if ($close_color != '') {
			$css_color = ' style="color:'.esc_attr($close_color).'!important;"';
		}
		
		if ($close_type == '') {
			$close_type = 'icon';
		}
		
		if ($close_text == '') {
			$close_text = esc_html__("No thanks. I don't want.", 'essb');
		}
		
		$output .= '<div class="essb-optinbooster essb-optinbooster-'.esc_attr($event).($ofob_deactivate_mobile ? ' essb-subscribe-mobile-hidden' : '').'" '.$event_fire.'>';
		
		if ($close_type == 'icon') {
			$output .= '<div class="essb-optinbooster-close essb-optinbooster-closeicon" '.esc_attr($css_color).'><i class="essb_icon_close"></i></div>';
		}
		
		$output .= do_shortcode('[easy-subscribe design="'.$design.'" mode="mailchimp" conversion="booster-'.esc_attr($event).'"]');
		if ($close_type != 'icon') {
			$output .= '<div class="essb-optinbooster-close essb-optinbooster-closetext" '.$css_color.'>'.$close_text.'</div>';
		}
		
		$output .= '</div>';
		$output .= '<div class="essb-optinbooster-overlay essb-optinbooster-overlay-'.esc_attr($event).($ofob_deactivate_mobile ? ' essb-subscribe-mobile-hidden' : '').'"'.($overlay_color != '' ? ' style="background-color:'.esc_attr($overlay_color).'!important;"' : '').'>';
		if ($credit_link) {
			$output .= '<div class="promo">Powered by <a href="http://go.appscreo.com/essb" target="_blank">Best Social Sharing Plugin for WordPress</a> Easy Social Share Buttons</div>';
		}
		
		$output .= '</div>';
		
		echo $output;
	}
}



/**
 * main code *
 */
global $essb_ofob;
function essb_optin_booster() {
	global $essb_ofob;
	
	if (! isset ( $essb_ofob )) {
		$essb_ofob = ESSBOptinBooster::getInstance ();
	}
}

add_action ( 'init', 'essb_optin_booster', 9 );