<?php

/**
 *
 * This is as a webhook for reading changes to ExpoFP
 *
 * @version 2.0
 *
 */

// Stop any direct calls to this file
//error_log("webhook been hooked after 1pm");
defined('ABSPATH') or die('This file cannot be called directly!');

$body = file_get_contents('php://input');
$webhook = json_decode($body);
//error_log(print_r($webhook, TRUE));

$type = (!empty($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : null);