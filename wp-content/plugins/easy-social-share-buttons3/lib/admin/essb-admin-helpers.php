<?php
/**
 * Admin helpers functions are running here
 */

/**
 * Return the state of opration for the advanced popup options
 * @return boolean
 */
function essb_admin_advanced_options() {
	return true;
}

function essb_editor_capability_can() {
	$can = true;
	
	$setup_capability = essb_option_value('limit_editor_fields_access');
	if ($setup_capability == '') {
		$setup_capability = 'manage_options';
	}
	
	if (function_exists('current_user_can')) {
		if (!current_user_can($setup_capability)) {
			$can = false;
		}
	}
	
	return $can;
}