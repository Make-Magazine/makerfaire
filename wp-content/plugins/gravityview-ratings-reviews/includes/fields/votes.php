<?php

defined( 'ABSPATH' ) || exit;

/**
 * Add custom options for Votes field
 * @since 1.3
 */
class GravityView_Field_Votes extends GravityView_Field {

	var $name = 'votes';

	var $icon = 'dashicons-star-half';

	/**
	 * @type string The description of the field in the field picker
	 */
	var $description;

	/**
	 * @type string The label of the field in the field picker
	 */
	var $label;

	function __construct() {

		parent::__construct();

		$this->label = __( 'Votes Rating', 'gravityview-ratings-reviews' );
		$this->description = __('Display the aggregate up/down rating.', 'gravityview-ratings-reviews' );

		$this->add_hooks();
	}

	/**
	 * @since 1.3
	 */
	protected function add_hooks() {

		add_filter( 'gravityview_entry_default_fields', array( $this, 'add_field_to_field_picker' ) );
	}

	/**
	 * Add the field to the field picker
	 *
	 * @since 1.3
	 *
	 * @param array $fields Array of fields to display in field picker
	 * @param array $form Connected Gravity Forms form
	 * @param string $zone Current tab in field configuration
	 *
	 * @return array
	 */
	function add_field_to_field_picker( $fields = array(), $form = array(), $zone = '' ) {

		if( 'edit' === $zone ) {
			return $fields;
		}

		$fields['votes'] = array(
			'label' => $this->label,
			'type' => $this->name,
			'desc' => $this->description,
		);

		return $fields;
	}

	/**
	 *
	 * @since 1.3
	 *
	 * @param array $field_options
	 * @param string $template_id
	 * @param string $field_id
	 * @param string $context
	 * @param string $input_type
	 *
	 * @return array
	 */
	function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id = 0 ) {

		unset ( $field_options['show_as_link'] );

		return $field_options;
	}

}

new GravityView_Field_Votes;
