<?php
function essb_rs_css_float() {
	$snippet = '';
	
	$top_pos = essb_sanitize_option_value('float_top');
	$float_top_loggedin = essb_sanitize_option_value('float_top_loggedin');
	
	$bg_color = essb_sanitize_option_value('float_bg');
	$bg_color_opacity = essb_sanitize_option_value('float_bg_opacity');
	$float_full = essb_sanitize_option_value('float_full');
	$float_remove_margin = essb_sanitize_option_value('float_remove_margin');
	$float_full_maxwidth = essb_sanitize_option_value('float_full_maxwidth');
	
	if (is_user_logged_in() && $float_top_loggedin != '') {
		$top_pos = $float_top_loggedin;
	}
	
	if ($bg_color_opacity != '' && $bg_color == '') {
		$bg_color = '#ffffff';
	}
	
	if ($top_pos != '') {
		$snippet .= (sprintf('.essb_fixed { top: %1$spx !important; }', $top_pos));
	}
	if ($bg_color != '') {
		if ($bg_color_opacity != '') {
			$bg_color = essb_hex2rgba($bg_color, $bg_color_opacity);
		}
		$snippet .= (sprintf('.essb_fixed { background: %1$s !important; }', $bg_color));
	}
	
	if ($float_full == 'true') {
		$snippet .= ('.essb_fixed { left: 0; width: 100%; min-width: 100%; padding-left: 10px; }');
	}
	if ($float_remove_margin == 'true') {
		$snippet .= ('.essb_fixed { margin: 0px !important; }');
	}
	
	if ($float_full_maxwidth != '') {
		$snippet .= (sprintf('.essb_fixed.essb_links ul { max-width: %1$spx; margin: 0 auto !important; } .essb_fixed { padding-left: 0px; }', $float_full_maxwidth));
	}
	
	
	return $snippet;
}