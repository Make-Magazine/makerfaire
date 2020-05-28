<?php
function essb_rs_css_postfloat() {
	$snippet = '';
	
	$postfloat_marginleft = essb_sanitize_option_value('postfloat_marginleft');
	$postfloat_margintop = essb_sanitize_option_value('postfloat_margintop');
	$postfloat_top = essb_sanitize_option_value('postfloat_top');
	$postfloat_percent = essb_sanitize_option_value('postfloat_percent');
	$postfloat_initialtop = essb_sanitize_option_value('postfloat_initialtop');
	
	if ($postfloat_marginleft != '') {
		$snippet .= (sprintf('body .essb_displayed_postfloat { margin-left: %1$spx !important; }', $postfloat_marginleft));
	}
	if ($postfloat_margintop != '') {
		$snippet .= (sprintf('body .essb_displayed_postfloat { margin-top: %1$spx !important; }', $postfloat_margintop));
	}
	if ($postfloat_top != '') {
		$snippet .= (sprintf('body .essb_displayed_postfloat.essb_postfloat_fixed { top: %1$spx !important; }', $postfloat_top));
	}
	if ($postfloat_initialtop != '') {
		$snippet .= (sprintf('body .essb_displayed_postfloat { top: %1$spx !important; }', $postfloat_initialtop));
	}
	if ($postfloat_percent != '') {
		$snippet .= ('body .essb_displayed_postfloat { opacity: 0; transform: translateY(50px); }');
	}
	
	return $snippet;
}