<?php

/**
 *
 * This is as a webhook for reading changes to ExpoFP
 *
 * @version 2.0
 *
 */

// Stop any direct calls to this file
defined('ABSPATH') or die('This file cannot be called directly!');

error_log(print_r($_REQUEST, TRUE));

$type = (!empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : null);
$form = (!empty($_REQUEST['form']) ? sanitize_text_field($_REQUEST['form']) : false);
