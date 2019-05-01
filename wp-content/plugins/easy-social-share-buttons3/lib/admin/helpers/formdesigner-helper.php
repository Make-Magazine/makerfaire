<?php

define('ESSB_FORM_DESIGNS', 'essb_options_forms');

/**
 * Read the saved designs inside the database
 * 
 * @return multitype:array
 */
function essb5_get_form_designs() {
	$r = get_option(ESSB_FORM_DESIGNS);
	
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
function essb5_save_form_designs($designs = array()) {
	update_option(ESSB_FORM_DESIGNS, $designs, 'no', 'no');
}

/**
 * Create a blank new design inside the library
 * 
 * @package EasySocialShareButtons
 * @since 5.9
 * @author appscreo
 */
function essb5_create_form_design() {
	$designs = essb5_get_form_designs();
	$new_design_id = count($designs);
	
	return $new_design_id;
}

/**
 * Read the design settings for a new design
 * 
 * @param unknown_type $design
 * @return multitype:
 */
function essb5_get_form_settings($design = '') {
	$designs = essb5_get_form_designs();
	
	$r = array();
	if ($design != 'new' && isset($designs[$design])) {
		$r = $designs[$design];
	}
	
	return $r;
}

function essb5_form_remove_design($design = '') {
	$designs = essb5_get_form_designs();
	
	if (isset($designs[$design])) {
		unset ($designs[$design]);
	}
	
	essb5_save_form_designs($designs);
}

function essb_array_value($field = '', $options = array()) {
	return isset($options[$field]) ? $options[$field] : '';
}