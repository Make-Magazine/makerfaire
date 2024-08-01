<?php

namespace GravityWP\Advanced_Merge_Tags;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();


/**
 * Gravity Perks Populate Anything compatibility.
 */
class Gravity_Perks_Populate_Anything {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// Hook into the 'gppa_process_template' filter to allow advanced merge tags and modifiers in GPPA choice templates.
		add_filter( 'gppa_process_template', array( $this, 'modify_gppa_template' ), 10, 6 );
	}

	/**
	 * Modify the template being processed by GPPA.
	 *
	 * This method is hooked into the 'gppa_process_template' filter and allows for the dynamic modification
	 * of the template based on various conditions and logic.
	 *
	 * @param string            $template_value The current template value.
	 * @param \GF_Field         $field          The current field object.
	 * @param string            $template       The template.
	 * @param string            $populate       The populate parameter.
	 * @param object            $object         The current object (entry) being processed.
	 * @param \GPPA_Object_Type $object_type    The type of the object.
	 *
	 * @return string The modified template value.
	 */
	public function modify_gppa_template( $template_value, $field, $template, $populate, $object, $object_type ) {

		if ( isset( $object_type->id ) && $object_type->id === 'gf_entry' ) {

			// $field is being passed by GPPA as an array when the example is loaded from the form-editor.
			if ( isset( $field->formId ) ) {
				$form_id = $field->formId;
			} elseif ( isset( $field['formId'] ) ) {
				$form_id = $field['formId'];
			} else {
				return $template_value;
			}

			$form = \GFAPI::get_form( $form_id );

			// Catch entry related advanced merge tags like gwp_date_created.
			if ( strpos( $template_value, 'gf_entry:gwp_' ) ) {
				$template_value = str_replace( 'gf_entry:gwp_', 'gwp_', $template_value );
				$template_value = GravityWP_Advanced_Merge_Tags::gwp_process_mergetags( $template_value, $form, (array) $object, false, false, false, 'text' );
			}

			// Catch field merge tags with advanced modifiers.
			if ( preg_match( '/gf_field_([\d.])+:gwp_/', $template_value ) ) {
				$template_value = preg_replace( '/gf_field_([\d.]+):gwp_/', '${1}:gwp_', $template_value );
				$template_value = GravityWP_Advanced_Merge_Tags::gwp_process_modifiers( $template_value, $form, (array) $object, false, false, false, 'text' );
			}
		}
		return $template_value;
	}

}

// Initialize the class.
new Gravity_Perks_Populate_Anything();
