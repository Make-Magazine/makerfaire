<?php
function essb_rs_css_sidebar() {
	$snippet = '';
	
	$custom_sidebarpos = essb_sanitize_option_value('sidebar_fixedtop');
	$custom_appearance_pos = essb_sanitize_option_value('sidebar_leftright_percent');
	$custom_sidebar_leftright = essb_sanitize_option_value('sidebar_fixedleft');
	
	$snippet = '';
	
	if ($custom_sidebarpos != '') {
		$snippet .= ('.essb_displayed_sidebar_right, .essb_displayed_sidebar { top: '.$custom_sidebarpos.' !important;}');
	}
	if ($custom_appearance_pos != '') {
		$snippet .= ('.essb_displayed_sidebar_right, .essb_displayed_sidebar { display: none; -webkit-transition: all 0.5s; -moz-transition: all 0.5s;-ms-transition: all 0.5s;-o-transition: all 0.5s;transition: all 0.5s;}');
	}
	if ($custom_sidebar_leftright != '') {
		$snippet .= '.essb_displayed_sidebar { left: '.$custom_sidebar_leftright.'px !important; } .essb_displayed_sidebar_right { right: '.$custom_sidebar_leftright.'!important;}';
	}
	
	return $snippet;
}