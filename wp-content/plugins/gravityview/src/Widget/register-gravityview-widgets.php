<?php
/**
 * GravityView default widgets and generic widget class
 *
 * @package   GravityView
 * @license   GPL2+
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2020, Katz Web Services, Inc.
 */

/**
 * Register the default widgets
 *
 * A widget registers itself via the `gravityview/widgets/register` filter from
 * its constructor, so each must be instantiated here. Instantiate on `init`
 * (not at autoload) so the constructors' translation calls run after
 * `after_setup_theme`; running them earlier trips WordPress 6.7's
 * `_load_textdomain_just_in_time` notice. Widget::is_registered() guards
 * against double registration.
 *
 * @since 3.0.0
 *
 * @return void
 */
function gravityview_register_gravityview_widgets() {
	new \GravityView_Widget_Search();
	new \GravityView_Widget_Custom_Content();
	new \GravityView_Widget_Export_Link();
	new \GravityView_Widget_Gravity_Forms();
	new \GravityKit\GravityView\Widget\Types\BulkActions();
	new \GravityKit\GravityView\Widget\Types\PageLinks();
	new \GravityKit\GravityView\Widget\Types\PageSize();
	new \GravityKit\GravityView\Widget\Types\PaginationInfo();
	new \GravityKit\GravityView\Widget\Types\Poll();
}

// Load default widgets
add_action( 'init', 'gravityview_register_gravityview_widgets', 11 );
