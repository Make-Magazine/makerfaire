<?php
/**
* Plugin Name: GP Copy Cat
* Description: Allow users to copy the value of one field to another automatically or by clicking a checkbox. Is your shipping address the same as your billing? Copy cat!
* Plugin URI: https://gravitywiz.com/documentation/gravity-forms-copy-cat/
* Version: 1.4.87
* Author: Gravity Wiz
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
* Update URI: https://gravitywiz.com/updates/gwcopycat
* Text Domain: gp-copy-cat
*/

define( 'GP_COPY_CAT_VERSION', '1.4.87' );

require plugin_dir_path( __FILE__ ) . 'includes/class-gp-bootstrap.php';

$gp_copy_cat_bootstrap = new GP_Bootstrap( 'class-gp-copy-cat.php', __FILE__ );
