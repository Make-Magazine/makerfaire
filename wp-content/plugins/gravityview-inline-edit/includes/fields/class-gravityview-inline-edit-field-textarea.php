<?php

/**
 * @file class-gravityview-inline-edit-field-textarea.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Textarea extends GravityView_Inline_Edit_Field {

	public $gv_field_name = 'textarea';

	public $inline_edit_type = 'textarea';

	public $set_value = true;

	/**
	 * @since 1.0
	 * 
	 * @param array $wrapper_attributes
	 * @param string $field_input_type
	 * @param int $field_id
	 * @param array $entry
	 * @param $current_form
	 * @param GF_Field_Textarea $gf_field
	 *
	 * @return array
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		$wrapper_attributes['data-maxlength'] = $gf_field->maxLength;

		$wrapper_attributes = parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );

		// A Paragraph field with the rich text editor is edited with TinyMCE (the `richtext` x-editable
		// type), so the inline editor shows formatted content instead of raw HTML. The client falls
		// back to a plain textarea when the editor runtime is unavailable. Load TinyMCE and the
		// richtext type only where such a field is actually rendered.
		if ( ! empty( $gf_field->useRichTextEditor ) ) {
			$wrapper_attributes['data-type'] = 'richtext';

			if ( wp_script_is( 'gv-inline-edit-richtext', 'registered' ) ) {
				wp_enqueue_script( 'gv-inline-edit-richtext' );
			}

			if ( function_exists( 'wp_enqueue_editor' ) ) {
				wp_enqueue_editor();
			}
		}

		return $wrapper_attributes;
	}

}

new GravityView_Inline_Edit_Field_Textarea;
