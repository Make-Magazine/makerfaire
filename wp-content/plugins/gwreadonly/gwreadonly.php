<?php
/**
 * Plugin Name: GP Read Only
 * Description: Mark your form fields as read-only to allow users to see field data but not modify it.
 * Plugin URI: http://gravitywiz.com/
 * Version: 1.3.2
 * Author: David Smith
 * Author URI: http://gravitywiz.com/
 * License: GPL2
 * Perk: True
 */

define( 'GP_READ_ONLY_VERSION', '1.3.2' );

require 'includes/class-gp-bootstrap.php';

$gp_read_only_bootstrap = new GP_Bootstrap( 'class-gp-read-only.php', __FILE__ );

