<?php
function essb_rs_css_bottombar() {
	$snippet = '';
	
	$topbar_bg_color = essb_sanitize_option_value('bottombar_bg');
	$topbar_bg_color_opacity = essb_sanitize_option_value('bottombar_bg_opacity');
	$topbar_maxwidth = essb_sanitize_option_value('bottombar_maxwidth');
	$topbar_height = essb_sanitize_option_value('bottombar_height');
	$topbar_contentarea_width = essb_sanitize_option_value('bottombar_contentarea_width');
	if ($topbar_contentarea_width == '' && essb_option_bool_value('bottombar_contentarea')) {
		$topbar_contentarea_width = '30';
	}
	
	$topbar_top_onscroll = essb_option_value('bottombar_top_onscroll');
	
	if ($topbar_bg_color_opacity != '' && $topbar_bg_color == '') {
		$topbar_bg_color = '#ffffff';
	}
	
	if ($topbar_bg_color != '') {
		if ($topbar_bg_color_opacity != '') {
			$topbar_bg_color = essb_hex2rgba($topbar_bg_color, $topbar_bg_color_opacity);
		}
		$snippet .= (sprintf('.essb_bottombar { background: %1$s !important; }', $topbar_bg_color));
	}
	if ($topbar_maxwidth != '') {
		$snippet .= (sprintf('.essb_bottombar .essb_bottombar_inner { max-width: %1$spx; margin: 0 auto; padding-left: 0px; padding-right: 0px;}', $topbar_maxwidth));
	}
	if ($topbar_height != '') {
		$snippet .= (sprintf('.essb_bottombar { height: %1$spx; }', $topbar_height));
	}
	if ($topbar_contentarea_width != '') {
		$topbar_contentarea_width = str_replace('%', '', $topbar_contentarea_width);
		$topbar_contentarea_width = intval($topbar_contentarea_width);
	
		$topbar_buttonarea_width = 100 - $topbar_contentarea_width;
		$snippet .= (sprintf('.essb_bottombar .essb_bottombar_inner_buttons { width: %1$s; }', $topbar_buttonarea_width.'%'));
		$snippet .= (sprintf('.essb_bottombar .essb_bottombar_inner_content { width: %1$s; }', $topbar_contentarea_width.'%'));
	}
	
	if ($topbar_top_onscroll != '') {
		$snippet .= ('.essb_bottombar { margin-bottom: -200px; }');
	}
	
	
	return $snippet;
}