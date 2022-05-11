<?php

/**
 * Create subscribe locking features
 * 
 * @author appscreo
 * @since 8.0
 * @package EasySocialShareButtons
 */
class ESSB_Optin_Locker {
    
    /**
     * singleton
     * @var unknown
     */
	private static $_instance;
	
	/**
	 * internal versioning
	 * @var string
	 */
	private $version = "1.0";
	
	/**
	 * options
	 * @var unknown
	 */
	private $options;
	
	function __construct() {
				
		$this->options = get_option ( 'essb3-oflock' );
		if (!is_array($this->options)) {
		    $this->options = array();
		}
				
		$demo_mode = isset($_REQUEST['optin_locker']) ? $_REQUEST['optin_locker'] : '';
		if ($demo_mode == 'true') {
			$this->options['oflock_time'] = 'true';
			$this->options['oflock_scroll'] = 'true';
		}
		
		if ($this->option_bool_value('oflock_time') || $this->option_bool_value('oflock_scroll')) {				    
            add_action ( 'wp_footer', array(&$this, 'draw_forms'), 99);
		}		
	}
	
	/**
	 * Check if module can run on the current post/page
	 * @return boolean
	 */
	function is_deactivated_on() {	
		if (is_admin ()) {
			return true;
		}
		
		if (is_search() || is_feed()) {
			return true;
		}
	
		$is_deactivated = false;
		$exclude_from = isset ( $this->options ['oflock_exclude'] ) ? $this->options ['oflock_exclude'] : '';
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
		
		$posttypes = $this->option_value('posttypes');
		if (!is_array($posttypes)) {
			$posttypes = array();
		}
			
		if (!empty($posttypes)) {
		  if (!is_singular($posttypes)) {
				$is_deactivated = true;
			}
		}
		
		return $is_deactivated;
	}
	
	/**
	 * Return single option's value
	 * @param unknown $param
	 * @return string|unknown
	 */
	public function option_value($param) {		
		$value = isset($this->options[$param]) ? $this->options[$param] : '';
		
		return $value;
	}
	
	/**
	 * Return single option's boolean value
	 * @param unknown $param
	 * @return boolean
	 */
	public function option_bool_value($param) {		
		$value = isset($this->options[$param]) ? $this->options[$param] : '';
		
		if ($value == 'true') {
			return true;
		}
		else {
			return false;
		}
	}
	
	
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
		
	public function draw_forms() {
		
		if (essb_is_plugin_deactivated_on()) {
			return;
		}
		
		if ($this->is_deactivated_on()) {
			return;
		}
		
		$single_time = $this->option_value('single_time');
		$allow_close = $this->option_bool_value('oflock_allow_close');
				
		if ($this->option_bool_value('oflock_time')) {
			$ofob_time_delay = $this->option_value('oflock_time_delay');
			$of_time_design = $this->option_value('oflock_time_design');
			$of_time_bgcolor = $this->option_value('oflock_time_bgcolor');
			
			if ($ofob_time_delay != '') {
				$callback = ' data-delay="'.esc_attr($ofob_time_delay).'"';
				
				if ($single_time != '') {
				    $callback .= ' data-unlock="'.esc_attr($single_time).'"';
				}
				
				$callback .= ' data-close="'.esc_attr($allow_close).'"';
				
				$this->draw_form_code('time', $of_time_design, $of_time_bgcolor, $callback, $allow_close);
			}
		}
		
		if ($this->option_bool_value('oflock_scroll')) {
			$ofob_scroll_percent = $this->option_value('oflock_scroll_percent');
			$of_scroll_design = $this->option_value('oflock_scroll_design');
			$of_scroll_bgcolor = $this->option_value('oflock_scroll_bgcolor');
				
			if ($ofob_scroll_percent != '') {
				$callback = ' data-scroll="'.esc_attr($ofob_scroll_percent).'"';
				
				if ($single_time != '') {
				    $callback .= ' data-unlock="'.esc_attr($single_time).'"';
				}
				
				$callback .= ' data-close="'.esc_attr($allow_close).'"';
				
				$this->draw_form_code('scroll', $of_scroll_design, $of_scroll_bgcolor, $callback, $allow_close);
			}
		}		
	}
	
	public function draw_form_code($event = '', $design = '', $overlay_color = '', $event_fire = '', $allow_close = false) {
		$output = '';
		
		/**
		 * @since 7.3.1		 
		 */
		$ofob_deactivate_mobile = $this->option_bool_value('oflock_deactivate_mobile');
		if (!$ofob_deactivate_mobile && essb_option_bool_value('subscribe_css_deactivate_mobile')) {
		    $ofob_deactivate_mobile = true;
		}
		
		$close_type = $this->option_value('oflock_'.$event.'_close');
		$close_color = $this->option_value('oflock_'.$event.'_closecolor');
		$close_text = $this->option_value('oflock_'.$event.'_closetext');
		
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
		
		$output .= '<div class="essb-optinlocker essb-optinlocker-'.esc_attr($event).($ofob_deactivate_mobile ? ' essb-subscribe-mobile-hidden' : '').'" '.$event_fire.'>';
		
		if ($close_type == 'icon' && $allow_close) {
		    ESSB_Dynamic_CSS_Builder::register_footer_field('.essb-optinlocker-'.esc_attr($event).' .essb-optinlocker-close svg', 'fill', $close_color);
		    $output .= '<div class="essb-optinlocker-close essb-optinlocker-closeicon" '.esc_attr($css_color).'>' . essb_svg_icon('close', $close_color) . '</div>';
		}
		
		$output .= do_shortcode('[easy-subscribe design="'.$design.'" mode="mailchimp" conversion="locker-'.esc_attr($event).'"]');
		
		if ($close_type != 'icon' && $allow_close) {
			$output .= '<div class="essb-optinlocker-close essb-optinlocker-closetext" '.$css_color.'>'.$close_text.'</div>';
		}
		
		$output .= '</div>';
		$output .= '<div class="essb-optinlocker-overlay essb-optinlocker-overlay-'.esc_attr($event).($ofob_deactivate_mobile ? ' essb-subscribe-mobile-hidden' : '').'"'.($overlay_color != '' ? ' style="background-color:'.esc_attr($overlay_color).'!important;"' : '').'>';
		
		$output .= '</div>';
		
		echo $output;
	}
}

ESSB_Factory_Loader::activate_instance('subscribe-locker', 'ESSB_Optin_Locker');