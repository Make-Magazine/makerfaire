<?php

if (!function_exists('essb_rs_css_build_cct_customizer')) {
	function essb_rs_css_build_cct_customizer() {
		$snippet = '';
		
		$customize_cct_bg = essb_sanitize_option_value('customize_cct_bg');
		$customize_cct_bg_hover = essb_sanitize_option_value('customize_cct_bg_hover');
		$customize_cct_color = essb_sanitize_option_value('customize_cct_color');
		$customize_cct_color_hover = essb_sanitize_option_value('customize_cct_color_hover');
		$customizer_cct_border = essb_sanitize_option_value('customizer_cct_border');
		$customizer_cct_border_hover = essb_sanitize_option_value('customizer_cct_border_hover');
		$customizer_cct_border_radius = essb_sanitize_option_value('customizer_cct_border_radius');
		$customizer_cct_padding = essb_sanitize_option_value('customizer_cct_padding');
		$customizer_cct_align = essb_sanitize_option_value('customizer_cct_align');
		$customizer_cct_fontsize = essb_sanitize_option_value('customizer_cct_fontsize');
		
		$snippet .= '.essb-ctt-user, .essb-ctt-user:hover { border: 0; }';
		
		if ($customize_cct_bg != '' || $customize_cct_color || 
				$customizer_cct_border != '' || $customizer_cct_border_radius != '' || $customizer_cct_padding != '' || 
				$customizer_cct_align != '' || $customizer_cct_fontsize != '') {
			
			$snippet .= '.essb-ctt-user {';
			
			if ($customize_cct_bg != '') { 
				$snippet .= 'background-color: '.$customize_cct_bg.';';
			}
			
			if ($customize_cct_color != '') {
				$snippet .= 'color: '.$customize_cct_color.';';
			}
			
			if ($customizer_cct_border != '') {
				$snippet .= 'border: '.$customizer_cct_border.';';
			}
			
			if ($customizer_cct_border_radius != '') {
				$snippet .= 'border-radius: '.$customizer_cct_border_radius.';';
			}
			
			if ($customizer_cct_padding != '') {
				$snippet .= 'padding: '.$customizer_cct_padding.';';
			}
			
			if ($customizer_cct_align != '') {
				$snippet .= 'text-align: '.$customizer_cct_align.';';
			}
			
			$snippet .= '}';
			
			if ($customizer_cct_align != '') {
				$snippet .= '.essb-ctt-user .essb-ctt-button { text-align: '.$customizer_cct_align.'; }';
			}
			
			if ($customizer_cct_fontsize != '') {
				$snippet .= '.essb-ctt-user .essb-ctt-quote { font-size: '.$customizer_cct_fontsize.'; }';
			}
		}
		
		/**
		 * Hover state
		 */
		
		if ($customize_cct_bg_hover != '' || $customize_cct_color_hover ||
				$customizer_cct_border_hover != '') {
				
			$snippet .= '.essb-ctt-user:hover {';
				
			if ($customize_cct_bg_hover != '') {
				$snippet .= 'background-color: '.$customize_cct_bg_hover.';';
			}
				
			if ($customize_cct_color_hover != '') {
				$snippet .= 'color: '.$customize_cct_color_hover.';';
			}
				
			if ($customizer_cct_border_hover != '') {
				$snippet .= 'border: '.$customizer_cct_border_hover.';';
			}
				
			$snippet .= '}';
		}
		
		return $snippet;
	}
}