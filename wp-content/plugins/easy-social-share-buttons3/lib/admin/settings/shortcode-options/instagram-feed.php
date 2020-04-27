<?php

if (!function_exists('essb_get_shortcode_options_instagram_feed')) {
	function essb_get_shortcode_options_instagram_feed() {
		$r = array();

		if (function_exists('essb_instagram_feed')) {
			$r = essb_instagram_feed()->get_settings();
		}

		return $r;
	}
}