<?php
/**
 * GravityView WP Widgets
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 1.6
 */


/**
 * Register GravityView widgets
 *
 * @since 1.6
 * @since 3.0.0 Removed redundant class-file includes; classes now resolve via PSR-4 autoloading.
 *
 * @return void
 */
function gravityview_register_widgets() {

	register_widget( 'GravityView_Recent_Entries_Widget' );

	register_widget( 'GravityView_Search_WP_Widget' );
}

add_action( 'widgets_init', 'gravityview_register_widgets' );
