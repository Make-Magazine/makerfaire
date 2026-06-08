<?php
/**
 * The default Image Choice field output template.
 *
 * @since 2.31.0
 *
 * @global Template_Context $gravityview
 */

use GV\Template_Context;
use GV\Utils;

if ( ! isset( $gravityview ) || empty( $gravityview->template ) ) {
	gravityview()->log->error( '{file} template loaded without context', [ 'file' => __FILE__ ] );

	return;
}

$field_id        = $gravityview->field->ID;
$field           = $gravityview->field->field;
$entry           = $gravityview->entry->as_entry();
$field_settings  = $gravityview->field->as_configuration();
$display_value   = $gravityview->display_value;
$value           = $gravityview->value;
$is_single_input = floor( $field_id ) !== floatval( $field_id );

// For single inputs (e.g., 1.1, 1.2), get the specific input's value.
if ( $is_single_input ) {
	$value = gravityview_get_field_value( $entry, $field_id, $display_value );

	// If this specific input has no value, output nothing.
	if ( '' === $value ) {
		return;
	}
}

$display_type = Utils::get( $field_settings, 'choice_display' );

if ( 'image' === $display_type ) {
	$gravityview_view = GravityView_View::getInstance();
	$form             = $gravityview_view->getForm();
	$image_choice     = new GravityView_Field_Image_Choice();

	echo $image_choice->output_image_choice( $value, $field, $form );
} else {
	/**
	 * Overrides whether to show the value or the label of an Image Choice field.
	 *
	 * @since 2.31.0
	 *
	 * @param bool                             $show_label  True to display the label of the choice; false to display the value. Default is false.
	 * @param array                            $entry       The Gravity Forms entry.
	 * @param GF_Field_Checkbox|GF_Field_Radio $field       The Gravity Forms field (can be either a radio or checkbox field).
	 * @param Template_Context                 $gravityview The GravityView template context.
	 */
	$show_label = apply_filters( 'gravityview/fields/image_choice/output_label', ( 'label' === $display_type ), $entry, $field, $gravityview );

	if ( $is_single_input ) {
		// For single inputs, output the value or label directly.
		if ( $show_label ) {
			$gravityview_view = GravityView_View::getInstance();
			$form             = $gravityview_view->getForm();

			echo gravityview_get_field_label( $form, $field_id, $value );
		} else {
			echo esc_html( $value );
		}
	} else {
		echo GravityView_GF_Compat::get_entry_detail( $field, $value, $entry, $show_label );
	}
}
