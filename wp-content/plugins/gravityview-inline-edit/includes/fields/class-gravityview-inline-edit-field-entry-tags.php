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
	public $gv_field_name = 'entry_tags';

	/** @see GF_Field_Tag $gf_field */
	public $inline_edit_type = 'entry_tags';

	public $set_value = true;

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

		if ( ! class_exists( EntryTagField::class ) ) {
			return $wrapper_attributes;
		}

		$field_value = rgar( $entry, $field_id );

		$wrapper_attributes['data-source'] = wp_json_encode( $gf_field->choices );
		parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $field_value, $entry ), $current_form['id'], $field_id );

		$view     = View::by_id( $wrapper_attributes['data-viewid'] ?? 0 );
		$gv_entry = GF_Entry::by_id( $wrapper_attributes['data-entryid'] ?? 0 );

		$field = $view ? GF_Field::by_id( $view->form, $field_id ) : null;

		if ( $view && $gv_entry && $field ) {
			$context = Template_Context::from_template(
				array(
					'view'  => $view,
					'field' => $field,
					'entry' => $gv_entry,
				)
			);

			$tag_value                             = '{replace_value}';
			$filter_link                           = ( new EntryTagField() )->get_tag_filter_link( $tag_value, $current_form, $field_id, $context, $gv_entry );
			$wrapper_attributes['data-entry-link'] = $filter_link;
		}

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}
}

new GravityView_Inline_Edit_Field_EntryTags();
