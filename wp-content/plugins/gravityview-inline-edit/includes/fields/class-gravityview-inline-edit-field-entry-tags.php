<?php

use GravityKit\EntryTags\EntryTagField;
use GV\GF_Entry;
use GV\Template_Context;
use GV\View;
use GV\GF_Field;

/**
 * @file class-gravityview-inline-edit-field-entry-tag.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_EntryTags extends GravityView_Inline_Edit_Field {
	var $gv_field_name = 'entry_tags';

	/** @see GF_Field_Tag $gf_field */
	var $inline_edit_type = 'entry_tags';

	var $set_value = true;

	/**
	 * @since 1.0
	 *
	 * @param               $wrapper_attributes
	 * @param               $field_input_type
	 * @param               $field_id
	 * @param               $entry
	 * @param               $current_form
	 * @param EntryTagField $gf_field
	 *
	 * @return array
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {
		$field_value = rgar( $entry, $field_id );

		$wrapper_attributes['data-source'] = json_encode( $gf_field->choices );
		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $field_value, $entry ), $current_form['id'], $field_id );

		$view  = View::by_id( $wrapper_attributes['data-viewid'] ?? 0 );
		$entry = GF_Entry::by_id( $wrapper_attributes['data-entryid'] ?? 0 );
		$field = GF_Field::by_id( $view->form ?? 0, $field_id );

		$context = Template_Context::from_template(
			array(
				'view'  => $view,
				'field' => $field,
				'entry' => $entry,
			)
		);

		$tag_value                             = '{replace_value}';
		$fitler_link                           = ( new EntryTagField() )->get_tag_filter_link( $tag_value, $current_form, $field_id, $context, $entry ?? [] );
		$wrapper_attributes['data-entry-link'] = $fitler_link;

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry ?? [], $current_form, $gf_field );
	}
}

new GravityView_Inline_Edit_Field_EntryTags();
