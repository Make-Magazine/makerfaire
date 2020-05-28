<?php

/**
 * Enable slim styles mode with deactivation of specific components inside plugin for the styles
 * and settings that reduce the overall size at half
 *
 * @since 7.0
 * @author appscreo
 * @package EasySocialShareButtons
 *
 */

add_filter('essb_avaliable_counter_positions', 'essb_slim_avaliable_counter_positions');

function essb_slim_avaliable_counter_positions($counters = array()) {
	
	if (isset($counters['left'])) {
		unset($counters['left']);
	}
	
	if (isset($counters['right'])) {
		unset($counters['right']);
	}
	
	if (isset($counters['leftm'])) {
		unset($counters['leftm']);
	}
	
	if (isset($counters['rightm'])) {
		unset($counters['rightm']);
	}
	
	if (isset($counters['topm'])) {
		unset($counters['topm']);
	}
	
	if (isset($counters['top'])) {
		unset($counters['top']);
	}
	
	if (isset($counters['topn'])) {
		unset($counters['topn']);
	}
	
	if (isset($counters['insidehover'])) {
		unset($counters['insidehover']);
	}
	
	return $counters;
}


add_filter('essb_avaiable_total_counter_position', 'essb_slim_avaiable_total_counter_position');

function essb_slim_avaiable_total_counter_position($counters = array()) {

	if (isset($counters['left'])) {
		unset($counters['left']);
	}

	if (isset($counters['right'])) {
		unset($counters['right']);
	}

	if (isset($counters['after'])) {
		unset($counters['after']);
	}

	if (isset($counters['before'])) {
		unset($counters['before']);
	}

	return $counters;
}

add_filter('essb_available_more_button_commands', 'essb_slim_styles_available_more_button_commands');

function essb_slim_styles_available_more_button_commands($commands = array()) {
	
	if (isset($commands['5'])) {
		unset($commands['5']);
	}
	
	if (isset($commands['4'])) {
		unset($commands['4']);
	}
	
	return $commands;
}


add_filter('essb4_templates', 'essb_slim_styles_templates');

function essb_slim_styles_templates($templates = array()) {
	
	if (isset($templates['58'])) {
		unset($templates['58']);
	}
	
	if (isset($templates['57'])) {
		unset($templates['57']);
	}
	
	if (isset($templates['56'])) {
		unset($templates['56']);
	}
	
	if (isset($templates['55'])) {
		unset($templates['55']);
	}
	
	if (isset($templates['52'])) {
		unset($templates['52']);
	}
	
	if (isset($templates['51'])) {
		unset($templates['51']);
	}
	
	if (isset($templates['43'])) {
		unset($templates['43']);
	}
	
	if (isset($templates['42'])) {
		unset($templates['42']);
	}
	
	if (isset($templates['30'])) {
		unset($templates['30']);
	}
	
	if (isset($templates['29'])) {
		unset($templates['29']);
	}
	
	if (isset($templates['26'])) {
		unset($templates['26']);
	}
	
	if (isset($templates['14'])) {
		unset($templates['14']);
	}
	
	if (isset($templates['13'])) {
		unset($templates['13']);
	}
	
	if (isset($templates['12'])) {
		unset($templates['12']);
	}
	
	return $templates;
}