<?php

defined( 'ABSPATH' ) || exit;

/**
 * Add custom options for HTML field
 */
class GravityView_Field_Stars extends GravityView_Field {

	var $name = 'stars';

	var $icon = 'dashicons-star-half';

	function __construct() {

		parent::__construct();

		add_filter( 'gravityview_entry_default_fields', array( $this, 'add_field_to_field_picker' ) );
	}

	function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id = 0 ) {

		unset ( $field_options['show_as_link'] );

		return $field_options;
	}

	/**
	 * Add the field to the field picker
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	function add_field_to_field_picker( $fields = array(), $form = array(), $zone = '' ) {

		if( 'edit' === $zone ) {
			return $fields;
		}

		$fields['stars'] = array(
			'label' => __( 'Stars Rating', 'gravityview-ratings-reviews' ),
			'type' => 'stars',
			'desc' => __('Display the entry\'s star rating (out of 5)', 'gravityview-ratings-reviews' ),
		);

		return $fields;
	}

}

new GravityView_Field_Stars;
