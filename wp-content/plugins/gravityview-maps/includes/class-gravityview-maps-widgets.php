<?php
/**
 * Register and launch View widgets
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */
class GravityView_Maps_Widgets extends GravityView_Maps_Component {

	function load() {

		// Register the Maps widget to GV core
		add_action( 'init', array( $this, 'register_widget' ), 20 );

	}


	/**
	 * Register the Maps widget to GV core
	 *
	 * @return void
	 */
	function register_widget() {
		include_once $this->loader->includes_dir .'class-gravityview-maps-view-widget.php';
		new GravityView_Maps_View_Widget;
	}
}

