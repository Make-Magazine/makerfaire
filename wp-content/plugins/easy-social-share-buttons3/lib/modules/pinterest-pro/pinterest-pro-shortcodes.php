<?php

/**
 * Pinterest Pro: Shortcodes for creating pinable images inside content or adding pinable galleries
 *
 * @package EasySocialShareButtons
 * @author appscreo
 * @version 1.0
 * @since 5.9
 *
 */

if (!function_exists('essb5_generate_pinterest_image')) {
	/**
	 * The [pinterest-image] shortcode generation function
	 * 
	 * @param unknown_type $atts
	 */
	function essb5_generate_pinterest_image($atts) {
		
		$type = isset($atts['type']) ? $atts['type'] : '';
		$image = isset($atts['image']) ? $atts['image'] : '';
		$message = isset($atts['message']) ? $atts['message'] : '';
		$custom_image = isset($atts['custom_image']) ? $atts['custom_image'] : '';
		$align = isset($atts['align']) ? $atts['align'] : '';
		$custom_class = isset($atts['class']) ? $atts['class'] : '';
		$elementor = isset($atts['elementor']) ? $atts['elementor'] : false;
		$class_align = '';
		
		if ($elementor) {
			if ($image && is_array($image)) {
				$image = $image['url'];
			}
			
			if ($custom_image && is_array($custom_image)) {
				$custom_image = $custom_image['url'];
			}
		}
		
		$share_image = '';
		$share_message = '';
		$share_url = get_permalink();
		
		// prevent sending blank images in the share command
		if (empty($custom_image)) {
			$custom_image = $image;
		}
		
		if (empty($message)) {
			$message = get_the_title();
		}
		
		if ($align == '') {
			$class_align = 'alignnone size-full';
		}
		else if ($align == 'center') {
			$class_align = 'aligncenter size-full';
		}
		else if ($align == 'left') {
			$class_align = 'alignleft size-full';
		}
		else if ($align == 'right') {
			$class_align = 'alignright size-full';
		}
		
		if ($type == 'post') {
			$essb_post_pin_image = get_post_meta( get_the_ID(), 'essb_post_pin_image', true);
			$essb_post_pin_desc = get_post_meta( get_the_ID(), 'essb_post_pin_desc', true);
			
			$share_message = !empty($essb_post_pin_desc) ? $essb_post_pin_desc : $message;
			$share_image = !empty($essb_post_pin_image) ? $essb_post_pin_image : $custom_image;
		}
		else {
			$share_image = $custom_image;
			$share_message = $message;
		}
		
		$options = array(
				'screen_image' => $image,
				'align' => $class_align,
				'share_image' => $share_image,
				'share_message' => $share_message,
				'share_url' => $share_url,
				'custom_class' => $custom_class
		);
		
		return essb_output_pinable_image_code($options);
	}
}

if (!function_exists('essb5_generate_pinterest_gallery')) {
	/**
	 * The [pinterest-gallery] shortcode
	 * 
	 * @param unknown_type $atts
	 */
	
	function essb5_generate_pinterest_gallery($atts = array()) {
		$message = isset($atts['message']) ? $atts['message'] : '';
		$images = isset($atts['images']) ? $atts['images'] : '';
		$columns = isset($atts['columns']) ? $atts['columns'] : '';
		$custom_class = isset($atts['class']) ? $atts['class'] : '';
		$adjust = isset($atts['adjust']) ? $atts['adjust'] : '';
		$spacing = isset($atts['spacing']) ? $atts['spacing'] : '';
		
		
		$selected_images = explode(',', $images);
		$salt = mt_rand();
		$instance_class = 'pg-'.$salt;
		if ($columns == '') { $columns = '3'; }
		
		$custom_class .= $instance_class;
		
		if ($adjust == 'yes') {
			$custom_class .= ' essb-pin-adjust';
		}
		
		$output = '<div class="essb-pin-gallery columns-'.esc_attr($columns).' '.esc_attr($custom_class).'" data-adjust="'.esc_attr($adjust).'" data-spacing="'.esc_attr($spacing).'">';
		
		$share_url = get_permalink();
		$post_title = get_the_title();
		
		foreach ($selected_images as $image_id) {
			$image_url = wp_get_attachment_url($image_id);
			$custom_desc = get_post_meta( $image_id, 'essb_pin_description', true );
			
			if ($message != '') { $custom_desc = $message; }
			
			if ($custom_desc == '') { $custom_desc = $post_title; }
			
			$options = array(
					'screen_image' => $image_url,
					'align' => '',
					'share_image' => $image_url,
					'share_message' => $message,
					'share_url' => $share_url,
					'custom_class' => 'in-gallery',
				    'image_link' => $image_url
			);
			
			$output .= essb_output_pinable_image_code($options);
			
		}
		
		$output .= '</div>';
		
		
		if ($spacing != '' && intval($spacing) != 0) {
			$custom_css = '.essb-pin-gallery.'.esc_attr($instance_class).' .essb-pin { padding: '.esc_attr($spacing).'px !important; }';
			essb_resource_builder()->add_css($custom_css, $instance_class, 'footer');	
		}
		
		essb_resource_builder()->add_static_resource_footer(ESSB3_PLUGIN_URL . '/lib/modules/pinterest-pro/assets/simplelightbox.min.css', 'simplelightbox', 'css');
		essb_resource_builder()->add_static_resource_footer(ESSB3_PLUGIN_URL . '/lib/modules/pinterest-pro/assets/simple-lightbox.min.js', 'simplelightbox', 'js', true);
		
		$pinsc_text = essb_option_value('pinsc_text');
		if ($pinsc_text == '') { $pinsc_text = 'Pin'; }
		
		$root_url = get_permalink();
		
		$code = '
jQuery(document).ready(function($){
	var pg_gallery = jQuery(\'.'.esc_attr($instance_class).' a.image-link\').simpleLightbox( { "additionalHtml": "<div class=\"essb-fullscreen-pin\"><a href=\"#\" target=\"_blank\" data-post-url=\"'.$root_url.'\"><span class=\"essb_icon essb_icon_pinterest\"></span><span class=\"network-text\">'.$pinsc_text.'</span></a></div>"} );
});';
		
		essb_resource_builder()->add_js($code, false, $instance_class);
		
		return $output;
	}
}

if (!function_exists('essb_output_pinable_image_code')) {
	/**
	 * The code drawing function. It will use options to generate the code for image with Pin button over it.
	 * 
	 * @param options_array $atts
	 */
	function essb_output_pinable_image_code($atts = array()) {
		$output = '';
		
		$screen_image = isset($atts['screen_image']) ? $atts['screen_image'] : '';
		$align = isset($atts['align']) ? $atts['align'] : '';
		$share_image = isset($atts['share_image']) ? $atts['share_image'] : '';
		$share_message = isset($atts['share_message']) ? $atts['share_message'] : '';
		$share_url = isset($atts['share_url']) ? $atts['share_url'] : '';
		$custom_class = isset($atts['custom_class']) ? $atts['custom_class'] : '';
		$image_link = isset($atts['image_link']) ? $atts['image_link'] : '';
		
		// asigning defaults if nothing is send
		if (empty($share_url)) { $share_url = get_permalink(); }
		if (empty($share_message)) { $share_message = get_the_title(); }
		if (empty($screen_image)) { $screen_image = $share_image; }
		
		// getting the default button setup
		$pinsc_template = essb_option_value('pinsc_template');
		$pinsc_button_style = essb_option_value('pinsc_button_style');
		$pinsc_button_size = essb_option_value('pinsc_button_size');
		$pinsc_css_animations = essb_option_value('pinsc_css_animations');
		$pinsc_position = essb_option_value('pinsc_position');
		$pinsc_text = essb_option_value('pinsc_text');
		$pinsc_alwayscustom = essb_option_bool_value('pinsc_alwayscustom');
		
		$pinsc_template = essb_template_folder($pinsc_template);
		
		if ($pinsc_alwayscustom) {
			$custom_post_pin = get_post_meta( get_the_ID(), 'essb_post_pin_desc', true);
			
			if ($custom_post_pin != '') {
				$share_message = $custom_post_pin;
			}
		}
		
		$shareCmd = 'http://pinterest.com/pin/create/bookmarklet/?media=' . urlencode($share_image) . '&url=' . urlencode($share_url) . '&is_video=false' . '&description=' . urlencode($share_message);
		$buttonStyleClasses = '';
		$buttonSizeClasses = '';
		
		// begin generation of the share button commands
		if ($pinsc_button_style == 'icon_hover') {
			$buttonStyleClasses = ' essb_hide_name';
		}
		if ($pinsc_button_style == 'icon') {
			$buttonStyleClasses = ' essb_force_hide_name essb_force_hide';
		}
		if ($pinsc_button_style == 'button_name') {
			$buttonStyleClasses = ' essb_hide_icon';
		}
		if ($pinsc_button_style == 'vertical') {
			$buttonStyleClasses = ' essb_vertical_name';
		}
			
		if (!empty($pinsc_button_size)) $buttonSizeClasses = ' essb_size_' . $pinsc_button_size;
		if (!empty($pinsc_css_animations)) $buttonSizeClasses .= ' ' . $pinsc_css_animations;
		if (!empty($pinsc_position)) $buttonSizeClasses .= ' essb_pos_' . $pinsc_position;
		
		$output .= '<div class="essb-pin'.($custom_class != '' ? ' '.esc_attr($custom_class) : '').($align != '' ? ' '.esc_attr($align) : '').'">';
		if ($image_link != '') {
			$output .= '<a href="'.esc_url($image_link).'" class="image-link">';
		}
		$output .= '<img class="pin-generated" src="'.esc_url($screen_image).'" '.($share_image != '' ? 'data-pin-media="'.esc_url($share_image).'"' : '').' title="'.esc_attr($share_message).'" />';
		
		if ($image_link != '') {
			$output .= '</a>';
		}
		
		$output .= '<div class="essb_links essb_displayed_pinimage essb_template_'.esc_attr($pinsc_template).esc_attr($buttonSizeClasses).'">';
		$output .= '<ul class="essb_links_list'.($buttonStyleClasses != '' ? ' ' . esc_attr($buttonStyleClasses) : '').'">';
		$output .= '<li class="essb_item essb_link_pinterest nolightbox">';
		$output .= '<a class="nolightbox" href="'.esc_url($shareCmd).'" target="_blank" onclick="essb.share_window(&#39;'.$shareCmd.'&#39;, &#39;pinit&#39;, &#39;pinterest&#39;); return false;"><span class="essb_icon essb_icon_pinterest"></span><span class="essb_network_name">'.($pinsc_text != '' ? $pinsc_text : 'Pin').'</span></a>';
		$output .= '</li>';
		$output .= '</ul>';
		$output .= '</div>';
		
		$output .= '</div>';
		
		return $output;
	}
}