<?php

/**
 * Restore back the recovery URL after ReallySimpleSSL apply the text replacement
 * 
 * @package EasySocialShareButtons
 * @since 5.0
 * @param string $code
 */

function essb_rsssl_fix_output($code) {

	$code = str_replace(
			'"facebook_post_recovery_url":"https:\/\/',
			'"facebook_post_recovery_url":"http:\/\/',
			$code);
	return $code;
}
add_filter("rsssl_fixer_output","essb_rsssl_fix_output");