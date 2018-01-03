<?php
/**
 * Plugin Name: GP Field Print
 * Description: Adds an option to your gravity form fields to allow users to add a print option for that field
 * Plugin URI: http://makerfaire.com
 * Version: 1.0
 * Author: Alicia Williams
 * Author URI: http://makerfaire.com
 * License: GPL2
 * Perk: True
 */

define( 'MF_FIELD_PRINT_VERSION', '1.0' );

require 'includes/class-gp-bootstrap.php';

$gp_read_only_bootstrap = new GP_Bootstrap( 'class-gp-field-print.php', __FILE__ );

