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

function essb_subscribe_fields_safe_html() {
    $allowed_tags = array(
        'a' => array(
            'class' => array(),
            'href'  => array(),
            'rel'   => array(),
            'title' => array(),
        ),
        'b' => array(),
        'div' => array(
            'class' => array(),
            'title' => array(),
            'style' => array(),
        ),
        'dl' => array(),
        'dt' => array(),
        'em' => array(),
        'h1' => array(),
        'h2' => array(),
        'h3' => array(),
        'h4' => array(),
        'h5' => array(),
        'h6' => array(),
        'i' => array(),
        'img' => array(
            'alt'    => array(),
            'class'  => array(),
            'height' => array(),
            'src'    => array(),
            'width'  => array(),
        ),
        'li' => array(
            'class' => array(),
        ),
        'ol' => array(
            'class' => array(),
        ),
        'p' => array(
            'class' => array(),
        ),
        'span' => array(
            'class' => array(),
            'title' => array(),
            'style' => array(),
        ),
        'strike' => array(),
        'strong' => array(),
        'ul' => array(
            'class' => array(),
        ),
    );
    
    return $allowed_tags;
}