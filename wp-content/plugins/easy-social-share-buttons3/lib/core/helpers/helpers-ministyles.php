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
	
	if (isset($templates['53'])) {
	    unset($templates['53']);
	}
	
	if (isset($templates['52'])) {
		unset($templates['52']);
	}
	
	if (isset($templates['51'])) {
		unset($templates['51']);
	}
	
	if (isset($templates['48'])) {
	    unset($templates['48']);
	}

	if (isset($templates['47'])) {
	    unset($templates['47']);
	}
	
	if (isset($templates['46'])) {
	    unset($templates['46']);
	}	
	
	if (isset($templates['45'])) {
	    unset($templates['45']);
	}
	
	if (isset($templates['44'])) {
	    unset($templates['44']);
	}
	
	if (isset($templates['43'])) {
		unset($templates['43']);
	}
	
	if (isset($templates['42'])) {
		unset($templates['42']);
	}
	
	if (isset($templates['41'])) {
	    unset($templates['41']);
	}
	
	if (isset($templates['40'])) {
	    unset($templates['40']);
	}
	
	if (isset($templates['33'])) {
	    unset($templates['33']);
	}
	
	if (isset($templates['30'])) {
		unset($templates['30']);
	}
	
	if (isset($templates['29'])) {
		unset($templates['29']);
	}
	
	if (isset($templates['27'])) {
	    unset($templates['27']);
	}
	
	if (isset($templates['26'])) {
		unset($templates['26']);
	}
	
	if (isset($templates['20'])) {
	    unset($templates['20']);
	}
	
	if (isset($templates['18'])) {
	    unset($templates['18']);
	}
	
	if (isset($templates['17'])) {
	    unset($templates['17']);
	}
	
	if (isset($templates['16'])) {
	    unset($templates['16']);
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
	
	
	if (isset($templates['11'])) {
	    unset($templates['11']);
	}
	
	if (isset($templates['8'])) {
	    unset($templates['8']);
	}
	
	return $templates;
}

add_filter('essb4_social_networks', 'essb_slim_essb4_social_networks');

function essb_slim_essb4_social_networks($networks = array()) {
    
    $disable_networks = array('digg', 'del', 'flattr', 'meneame', 'blogger', 'amazon', 'gmail',
        'aol', 'newsvine', 'myspace', 'viadeo', 'comments', 'yahoomail', 'kakaotalk',
        'livejournal', 'yammer', 'meetedgar', 'fintel', 'share'
    );
    
    foreach ($disable_networks as $key) {
        if (isset($networks[$key])) {
            unset ($networks[$key]);
        }
    }
    
    return $networks;
}