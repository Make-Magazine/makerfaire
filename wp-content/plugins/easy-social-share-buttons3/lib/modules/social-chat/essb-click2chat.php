<?php
/**
 * Click to Chat Module for Easy Social Share Buttons for WordPress
 *
 * @package EasySocialShareButtons
 * @author appscreo
 * @version 1.0
 * @since 5.6
 */

if (!function_exists('essb_click2chat_register')) {
	
	function essb_click2chat_can_run() {
		if (is_admin() || is_feed()) {
			return false;
		}
		
		$is_deactivated = false;
		$exclude_from = essb_option_value('click2chat_exclude');
		if (! empty ( $exclude_from )) {
			$excule_from = explode ( ',', $exclude_from );
		
			$excule_from = array_map ( 'trim', $excule_from );
			if (in_array ( get_the_ID (), $excule_from, false )) {
				$is_deactivated = true;
			}
		}
		
		if (essb_option_bool_value('click2chat_deactivate_homepage')) {
			if (is_home() || is_front_page()) {
				$is_deactivated = true;
			}
		}
		
		if (essb_option_bool_value('click2chat_posttypes')) {
			$posttypes = $this->option_value('posttypes');
			if (!is_array($posttypes)) {
				$posttypes = array();
			}
		
			if (!is_singular($posttypes)) {
				$is_deactivated = true;
			}
		}
		
		// deactivate display of the functions
		if ($is_deactivated) {
			return false;
		}
		
		return true;
	}
	
	function essb_click2chat_register() {
		if (essb_click2chat_can_run()) {
			essb_click2chat_draw();
		}
	}
	
	function essb_click2chat_register_styles() {
		if (essb_click2chat_can_run()) {
			$styles = '';
			
			$click2chat_text = essb_sanitize_option_value('click2chat_text');
			$click2chat_bgcolor = essb_sanitize_option_value('click2chat_bgcolor');
			$click2chat_color = essb_option_value('click2chat_color');
			
			if ($click2chat_bgcolor != '' || $click2chat_color != '') {
				if ($click2chat_bgcolor != '') {
					$styles .= '.essb-click2chat, .essb-click2chat-window .window-header { background-color: '.esc_attr($click2chat_bgcolor).';}';
				}
					
				if ($click2chat_color != '') {
					$styles .= '.essb-click2chat, .essb-click2chat-window .window-header { color: '.esc_attr($click2chat_color).';}';
				}
			}
			
			if ($styles != '') {
				essb_resource_builder()->add_css($styles, 'click2chat');
			}
		}
	}
	
	function essb_click2chat_draw($settings = array()) {
		
		global $post;
		
		$shortcode_call = false;

		$application_names = array('' => 'WhatsApp', 'whatsapp' => 'WhatsApp', 'viber' => 'Viber', 'email' => 'Email', 'phone' => 'Phone');
		
		$click2chat_icon = essb_option_value('click2chat_icon');
		$click2chat_location = essb_option_value('click2chat_location');
		$click2chat_welcome_text = essb_option_value('click2chat_welcome_text');
		$click2chat_text = essb_sanitize_option_value('click2chat_text');
		$click2chat_bgcolor = essb_sanitize_option_value('click2chat_bgcolor');
		$click2chat_color = essb_option_value('click2chat_color');
		if (isset($settings['shortcode'])) {
			$shortcode_call = true;
			
			$button_text = isset($settings['text']) ? $settings['text'] : $click2chat_text;
			$button_background = isset($settings['background']) ? $settings['background'] : $click2chat_bgcolor;
			$button_color = isset($settings['color']) ? $settings['color'] : $click2chat_color;
			$button_icon = isset($settings['icon']) ? $settings['icon'] : $click2chat_icon;
			$button_align = isset($settings['align']) ? $settings['align'] : 'left';
			
			if ($button_background != '' || $button_color != '') {
				echo '<style type="text/css">';
					
				if ($button_background != '') {
					echo '.essb-click2chat-inline { background-color: '.esc_attr($button_background).';}';
				}
					
				if ($button_color != '') {
					echo '.essb-click2chat-inline { color: '.esc_attr($button_color).';}';
				}
					
				echo '</style>';
			}
		}
		
		if ($click2chat_location == '') {
			$click2chat_location = 'right';
		}
		
		if ($click2chat_text == '') {
			$click2chat_text = esc_html__('Chat With Us', 'essb');
			
			
		}
		if ($shortcode_call) {
			if ($button_text != '') {
				$click2chat_text = $button_text;
			}
		}
		
		if ($click2chat_icon == '') {
			$click2chat_icon = 'comments';
						
		}
		
		if ($shortcode_call) {
			if ($button_icon != '') {
				$click2chat_icon = $button_icon;
			}
		}
		
		if ($shortcode_call) {
			echo '<div class="essb-click2chat-button position-'.esc_attr($button_align).'">';
		}
		echo '<div class="essb-click2chat essb-click2chat-'.esc_attr($click2chat_location).($shortcode_call ? ' essb-click2chat-inline': '').'">';
		echo '<i class="essb_icon_'.esc_attr($click2chat_icon).'"></i><span>'.$click2chat_text.'</span>';
		echo '</div>';
		
		if ($shortcode_call) {
			echo '</div>';
		}
		
		echo '<div class="essb-click2chat-window essb-click2chat-'.esc_attr($click2chat_location).'">';
		
		echo '<div class="window-header">';
		echo '<i class="essb_icon_close chat-close"></i>';
		
		if ($click2chat_welcome_text != '') {
			echo '<div class="welcome-text">';
			echo $click2chat_welcome_text;
			echo '</div>';
		}
		echo '</div>';
		
		echo '<div class="operator-list">';
		
		for ($i=1;$i<=4;$i++) {
			$operator = 'click2chat_operator'.$i.'_';
			
			if (essb_option_bool_value($operator.'active')) {
				$name = essb_option_value($operator.'name');
				$title = essb_option_value($operator.'title');
				$number = essb_option_value($operator.'number');
				$app = essb_option_value($operator.'app');
				$image = essb_option_value($operator.'image');
				$text = essb_option_value($operator.'text');
				
				if ($app == '') {
					$app = 'whatsapp';
				}
				
				$display_name = isset($application_names[$app]) ? $application_names[$app] : '';
				
				if ($image == '') {
					$image = '
<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 489.5 489.5" style="enable-background:new 0 0 489.5 489.5;" xml:space="preserve">
		<path id="XMLID_7410_" style="fill:#94A4A4;" d="M242.85,238.9L242.85,238.9c0.1,0,0.3,0,0.4,0s0.3,0,0.4,0l0,0
			c74.6-0.8,63.8-100.4,63.8-100.4c-3.1-66.5-58.7-66-64.2-65.8c-5.5-0.2-61.1-0.8-64.2,65.8
			C179.15,138.6,168.35,238.2,242.85,238.9z"/>
		<path style="fill:#2C2F33;" d="M244.75,0c-94.6,0-171.6,77-171.6,171.6c0,45.1,17.6,86.3,46.2,116.9c0.6,0.8,1.3,1.6,2.1,2.2
			c31.2,32.3,75,52.5,123.3,52.5c94.6,0,171.6-77,171.6-171.6S339.35,0,244.75,0z M144.55,287.6c46.6-15.2,63-33.7,68.7-45.7
			c8.6,3.9,18.2,5.9,29,6.1c0.2,0,0.5,0,0.7,0h0.4h0.4c0.2,0,0.5,0,0.7,0c11.6-0.2,22-2.6,31.1-7.1c5.3,11.9,21.3,31.1,69.3,46.7
			c-26.9,23.2-61.9,37.4-100.2,37.4C206.45,325,171.45,310.9,144.55,287.6z M243.65,229.9c-0.1,0-0.2,0-0.3,0l0,0
			c-0.1,0-0.2,0-0.3,0c-15.9-0.2-28-5.2-37-15.4c-22.8-25.6-17.8-74.4-17.8-74.9c0-0.2,0-0.4,0-0.6c2.4-53,41.6-57.1,53.6-57.1
			c0.5,0,1,0,1.2,0s0.5,0,0.7,0c0.3,0,0.7,0,1.2,0c11.9,0,51.1,4.1,53.6,57.1c0,0.2,0,0.4,0,0.6c0.1,0.5,5,49.2-17.8,74.9
			C271.65,224.7,259.55,229.7,243.65,229.9z M359.55,273.2c-66.2-19.2-68.4-42.7-68.4-42.9c0,0.3,0,0.5,0,0.5h-1
			c1.4-1.3,2.8-2.7,4.1-4.2c27.3-30.8,22.8-83.6,22.3-88.6c-2.6-54.8-40.2-74.3-71.7-74.3c-0.6,0-1.2,0-1.6,0c-0.4,0-0.9,0-1.6,0
			c-31.4,0-69,19.4-71.7,74.2c-0.5,4.9-5.1,57.8,22.3,88.6c1.7,1.9,3.6,3.7,5.4,5.4c-1.5,5-11.3,24.7-67.9,41.2
			c-24-27.1-38.6-62.6-38.6-101.6c0-84.6,68.8-153.5,153.5-153.5s153.5,68.8,153.5,153.5C398.25,210.5,383.55,246.1,359.55,273.2z"
			/>
		<path style="fill:#2C2F33;" d="M379.75,410.7c0-5-4.1-9.1-9.1-9.1h-251.8c-5,0-9.1,4.1-9.1,9.1s4.1,9.1,9.1,9.1h251.8
			C375.65,419.8,379.75,415.7,379.75,410.7z"/>
		<path style="fill:#2C2F33;" d="M335.45,480.4c0-5-4.1-9.1-9.1-9.1h-163.2c-5,0-9.1,4.1-9.1,9.1s4.1,9.1,9.1,9.1h163.3
			C331.35,489.4,335.45,485.4,335.45,480.4z"/>
</svg>';
				}
				
				else {
					$image = '<img src="'.esc_url($image).'"/>';
				}
				
				if ($text != '' && isset($post)) {
					$url = get_permalink();
					$title_plain = $post->post_title;			
					$text = str_replace('[title]', $title_plain, $text);
					$text = str_replace('[url]', esc_url($url), $text);
				}
				
				echo '<div class="operator operator-app-'.esc_attr($app).'" data-app="'.esc_attr($app).'" data-number="'.esc_attr($number).'" data-message="'.esc_attr($text).'">';
				echo '<div class="image">'.$image.'</div>';
				echo '<div class="data">';
				echo '<span class="title">'.$title.'</span>';
				echo '<span class="name">'.$name.'</span>';
				echo '<span class="title app"><span>'.esc_html($display_name).'</span></span>';
				echo '</div>';
				echo '</div>';
			}
		}
		
		echo '</div>';
		
		echo '</div>';
	}
	
	if (essb_option_bool_value('click2chat_activate')) {
		add_action('wp_enqueue_scripts', 'essb_click2chat_register_styles', 1 );
		add_action('wp_footer', 'essb_click2chat_register');
	}
	

}