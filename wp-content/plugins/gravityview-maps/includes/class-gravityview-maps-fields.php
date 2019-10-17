<?php
/**
 * Register and launch View map fields
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */
class GravityView_Maps_Fields extends GravityView_Maps_Component {

	function load() {

		// Register extra Maps fields on View configuration
		add_filter( 'gravityview_entry_default_fields', array( $this, 'register_field' ), 10, 3 );

		add_filter( 'gravityview_template_entry_map_options', array( $this, 'field_options' ) );

	}

	/**
	 * Field options for entry_map field.
	 *
	 * @since 1.1
	 *
	 * @param  array $field_options
	 * @return array
	 */
	public function field_options( $field_options ) {

		// Always a link!
		unset( $field_options['show_as_link'], $field_options['search_filter'], $field_options['new_window'] );

		return $field_options;
	}

	/**
	 * Register the extra Maps fields for the View configuration
	 *
	 * @return array
	 */

	/**
	 * @param $fields array List of entry default fields
	 * @param $form array Form object
	 * @param $zone string Either 'single', 'directory', 'header', 'footer'
	 *
	 * @return array
	 */
	function register_field( $fields, $form, $zone ) {

		if( 'edit' === $zone ) {
			return $fields;
		}

		$fields['entry_map'] = array(
			'label'	=> __( 'Entry Map', 'gravityview-maps' ),
			'type'	=> 'entry_map',
			'desc'	=> __( 'Display a map showing the location of the entry', 'gravityview-maps' ),
		);

		return $fields;
	}
}
