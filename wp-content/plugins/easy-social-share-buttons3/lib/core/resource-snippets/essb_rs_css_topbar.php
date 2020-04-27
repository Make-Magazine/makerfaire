<?php
function essb_rs_css_topbar() {
	$snippet = '';
	
	/**
	 * Top Bar
	 */
	$topbar_top_pos = essb_sanitize_option_value('topbar_top');
	$topbar_top_loggedin = essb_sanitize_option_value('topbar_top_loggedin');
	
	$topbar_bg_color = essb_sanitize_option_value('topbar_bg');
	$topbar_bg_color_opacity = essb_sanitize_option_value('topbar_bg_opacity');
	$topbar_maxwidth = essb_sanitize_option_value('topbar_maxwidth');
	$topbar_height = essb_sanitize_option_value('topbar_height');
	$topbar_contentarea_width = essb_sanitize_option_value('topbar_contentarea_width');
	if ($topbar_contentarea_width == '' && essb_option_bool_value('topbar_contentarea')) {
		$topbar_contentarea_width = '30';
	}
	
	$topbar_top_onscroll = essb_sanitize_option_value('topbar_top_onscroll');
	
	if (is_user_logged_in() && $topbar_top_loggedin != '') {
		$topbar_top_pos = $topbar_top_loggedin;
	}
	
	if ($topbar_bg_color_opacity != '' && $topbar_bg_color == '') {
		$topbar_bg_color = '#ffffff';
	}
	
	if ($topbar_top_pos != '') {
		$snippet .= (sprintf('.essb_topbar { top: %1$spx !important; }', $topbar_top_pos));
	}
	if ($topbar_bg_color != '') {
		if ($topbar_bg_color_opacity != '') {
			$topbar_bg_color = essb_hex2rgba($topbar_bg_color, $topbar_bg_color_opacity);
		}
		$snippet .= (sprintf('.essb_topbar { background: %1$s !important; }', $topbar_bg_color));
	}
	if ($topbar_maxwidth != '') {
		$snippet .= (sprintf('.essb_topbar .essb_topbar_inner { max-width: %1$spx; margin: 0 auto; padding-left: 0px; padding-right: 0px;}', $topbar_maxwidth));
	}
	if ($topbar_height != '') {
		$snippet .= (sprintf('.essb_topbar { height: %1$spx; }', $topbar_height));
	}
	if ($topbar_contentarea_width != '') {
		$topbar_contentarea_width = str_replace('%', '', $topbar_contentarea_width);
		$topbar_contentarea_width = intval($topbar_contentarea_width);
			
		$topbar_buttonarea_width = 100 - $topbar_contentarea_width;
		$snippet .= (sprintf('.essb_topbar .essb_topbar_inner_buttons { width: %1$s; }', $topbar_buttonarea_width.'%'));
		$snippet .= (sprintf('.essb_topbar .essb_topbar_inner_content { width: %1$s; }', $topbar_contentarea_width.'%'));
	}
	
	if ($topbar_top_onscroll != '') {
		$snippet .= ('.essb_topbar { margin-top: -200px; }');
	}
	
	
	return $snippet;
}