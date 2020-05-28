<?php

define('ESSB_CUSTOM_BUTTONS', 'essb_custom_buttons');

/**
 * Read the saved designs inside the database
 *
 * @return multitype:array
 */
function essb_get_custom_buttons() {
	$r = get_option(ESSB_CUSTOM_BUTTONS);

	if (!$r || !is_array($r)) {
		$r = array();
	}

	return $r;
}

/**
 * Update designs and store them inside the database
 *
 * @param unknown_type $designs
 */
function essb_save_custom_buttons($buttons = array()) {
	update_option(ESSB_CUSTOM_BUTTONS, $buttons, 'no', 'no');
}


/**
 * Read the design settings for a new design
 *
 * @param unknown_type $design
 * @return multitype:
 */
function essb_get_custom_button_settings($network_id = '') {
	$buttons = essb_get_custom_buttons();

	$r = array();
	if ($network_id != 'new' && isset($buttons[$network_id])) {
		$r = $buttons[$network_id];
	}

	return $r;
}

function essb_remove_custom_button($network_id = '') {
	$buttons = essb_get_custom_buttons();

	if (isset($buttons[$network_id])) {
		unset ($buttons[$network_id]);
	}

	essb_save_custom_buttons($buttons);
}
