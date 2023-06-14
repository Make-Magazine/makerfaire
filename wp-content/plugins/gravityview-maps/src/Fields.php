<?php

namespace GravityKit\GravityMaps;

/**
 * Registers View map fields
 *
 * @since 1.0.0
 */
class Fields extends Component {
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
	 * @param array $field_options
	 *
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
	 * @param $fields array List of entry default fields
	 * @param $form   array Form object
	 * @param $zone   string Either 'single', 'directory', 'header', 'footer'
	 *
	 * @return array
	 */
	function register_field( $fields, $form, $zone ) {
		if ( 'edit' === $zone ) {
			return $fields;
		}

		$fields['entry_map'] = array(
			'label' => __( 'Entry Map', 'gk-gravitymaps' ),
			'type'  => 'entry_map',
			'desc'  => __( 'Display a map showing the location of the entry', 'gk-gravitymaps' ),
			'icon'  => 'dashicons-location-alt',
			'group' => 'gravityview',
		);

		return $fields;
	}
}
