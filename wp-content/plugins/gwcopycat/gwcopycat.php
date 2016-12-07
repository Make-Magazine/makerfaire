<?php
/**
* Plugin Name: GP Copy Cat
* Description: Allow users to copy the value of one field to another automatically or by clicking a checkbox. Is your shipping address the same as your billing? Copy cat!
* Plugin URI: http://gravitywiz/category/perks/
* Version: 1.3.12
* Author: David Smith
* Author URI: http://gravitywiz.com/
* License: GPL2
* Perk: True
*/

require 'includes/class-gp-bootstrap.php';

$gp_copy_cat_bootstrap = new GP_Bootstrap( 'class-gp-copy-cat.php', __FILE__ );
