<?php
/**
 * Love This button
 *
 * @since 5.0
 *
 * @package EasySocialShareButtons
 * @author  appscreo <http://codecanyon.net/user/appscreo/portfolio>
 */

add_filter('essb_js_buffer_footer', 'essb_js_build_lovethis_code');

function essb_js_build_lovethis_code($buffer) {
	$message_loved = essb_option_value('translate_love_loved');
	$message_thanks = essb_option_value('translate_love_thanks');

	if ($message_loved == "") {
		$message_loved = esc_html__("You already love this today.", 'essb');
	}
	if ($message_thanks == "") {
		$message_thanks = esc_html__("Thank you for loving this.", 'essb');
	}


	$script = '
	var essb_clicked_lovethis = window.essb_clicked_lovethis = false;
	var essb_love_you_message_thanks = window.essb_love_you_message_thanks = "'.$message_thanks.'";
	var essb_love_you_message_loved = window.essb_love_you_message_loved = "'.$message_loved.'";';
	
	if (essb_option_bool_value('lovethis_disable_thankyou')) {
		$script .= 'if (essb) essb.loveDisableThanks = true;';
	}
	
	if (essb_option_bool_value('lovethis_disable_loved')) {
		$script .= 'if (essb) essb.loveDisableLoved = true;';
	}

	$script = trim(preg_replace('/\s+/', ' ', $script));
	return $buffer.$script;

}
