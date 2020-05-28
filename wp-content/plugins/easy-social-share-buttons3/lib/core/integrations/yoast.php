<?php

if (!function_exists('essb_yoast_custom_data')) {
	function essb_yoast_custom_data() {
		global $post;
		
		$r = array('title' => '', 'description' => '');
		
		$yoast_title = get_post_meta( $post->ID, '_yoast_wpseo_title', true);
		$yoast_description = get_post_meta( $post->ID, '_yoast_wpseo_metadesc', true);
			
		$yoast_sso_title = get_post_meta( $post->ID, '_yoast_wpseo_opengraph-title' , true );
		if ($yoast_sso_title != '') {
			$yoast_title = $yoast_sso_title;
		}
			
		$yoast_sso_description = get_post_meta( $post->ID, '_yoast_wpseo_opengraph-description' , true );
		if ($yoast_sso_description != '') {
			$yoast_description = $yoast_sso_description;
		}
			
		if ($yoast_title != '') {
			// include WPSEO replace vars
			if (strpos($yoast_title, '%%') !== false && function_exists('wpseo_replace_vars')) {
				$yoast_title = wpseo_replace_vars($yoast_title, $post);
			}
		
			$r['title'] = $yoast_title;
		}
		if ($yoast_description != '') {
			$r['description'] = $yoast_description;
		}
		
		return $r;
	}
}