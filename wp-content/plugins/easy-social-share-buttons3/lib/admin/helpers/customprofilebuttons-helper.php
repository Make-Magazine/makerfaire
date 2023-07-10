<?php

define('ESSB_CUSTOM_PROFILE_BUTTONS', 'essb_custom_profile_buttons');

/**
 * Read the saved designs inside the database
 *
 * @return multitype:array
 */
function essb_get_custom_profile_buttons() {
    $r = get_option(ESSB_CUSTOM_PROFILE_BUTTONS);

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
function essb_save_custom_profile_buttons($buttons = array()) {
    update_option(ESSB_CUSTOM_PROFILE_BUTTONS, $buttons, 'no', 'no');
}


/**
 * Read the design settings for a new design
 *
 * @param unknown_type $design
 * @return multitype:
 */
function essb_get_custom_profile_button_settings($network_id = '') {
	$buttons = essb_get_custom_profile_buttons();

	$r = array();
	if ($network_id != 'new' && isset($buttons[$network_id])) {
		$r = $buttons[$network_id];
	}

	return $r;
}

function essb_remove_custom_profile_button($network_id = '') {
    $buttons = essb_get_custom_profile_buttons();

	if (isset($buttons[$network_id])) {
		unset ($buttons[$network_id]);
	}

	essb_save_custom_profile_buttons($buttons);
}

function essb_remove_all_custom_profile_buttons() {
    delete_option(ESSB_CUSTOM_PROFILE_BUTTONS);    
}


function essb_create_custom_profile_button($obj) {
    $network_id = isset($obj['network_id']) ? $obj['network_id'] : '';
    
    if (!empty($network_id)) {
        $buttons = essb_get_custom_profile_buttons();
        $buttons[$network_id] = $obj;
        
        essb_save_custom_profile_buttons($buttons);
    }
}
