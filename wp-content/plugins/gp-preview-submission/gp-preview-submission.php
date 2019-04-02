<?php
/**
 * Plugin Name: GP Preview Submission
 * Plugin URI: http://gravitywiz.com/
 * Description: Add a simple submission preview to allow users to confirm their submission is correct before submitting the form.
 * Version: 1.2.13
 * Author: David Smith
 * Author URI: http://gravitywiz.com
 * License: GPL2
 * Perk: True
 */

define( 'GP_PREVIEW_SUBMISSION_VERSION', '1.2.13' );

require 'includes/class-gp-bootstrap.php';

$gp_preview_submission_bootstrap = new GP_Bootstrap( 'class-gp-preview-submission.php', __FILE__ );