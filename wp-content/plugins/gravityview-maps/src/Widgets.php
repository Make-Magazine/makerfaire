<?php

namespace GravityKit\GravityMaps;

/**
 * Handles View widget logic
 *
 * @since     1.0.0
 */
class Widgets extends Component {
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
		new View_Widget;
	}
}
