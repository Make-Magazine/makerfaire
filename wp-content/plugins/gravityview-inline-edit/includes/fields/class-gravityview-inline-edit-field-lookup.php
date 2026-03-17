<?php

/**
 * @file  class-gravityview-inline-edit-field-lookup.php
 *
 * @since 2.4.0
 */
class GravityView_Inline_Edit_Field_Lookup extends GravityView_Inline_Edit_Field {
	var $gv_field_name = 'lookup';

	/** @var GF_Field_Select $gf_field */
	var $inline_edit_type = 'select';

	var $set_value = true;

	/**
	 * Register hooks for all inline edit types the lookup field can use.
	 *
	 * The lookup field dynamically switches between select, radiolist, and checklist
	 * depending on the field's lookupInputType. The parent class only registers the
	 * updated_result filter for the default inline_edit_type (select), so we override
	 * to register for all possible types.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	protected function add_hooks() {
		parent::add_hooks();

		add_filter( 'gravityview-inline-edit/entry-updated/radiolist', array( $this, 'updated_result' ), 10, 4 );
		add_filter( 'gravityview-inline-edit/entry-updated/checklist', array( $this, 'updated_result' ), 10, 4 );
	}

	/**
	 * @since 2.4.0
	 *
	 * @param array         $wrapper_attributes
	 * @param string        $field_input_type
	 * @param string        $field_id
	 * @param array         $entry
	 * @param array         $current_form
	 * @param GF_Field_List $gf_field
	 *
	 * @return array
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {
		$gf_field->storageFormat = 'id';
		$lookup_field_value      = rgar( $entry, $field_id );
		$this->set_value         = true;

		if ( 'dropdown' === $gf_field->lookupInputType ) {
			$this->inline_edit_type = 'select';

		} elseif ( $this->is_radio_type( $gf_field ) ) {
			$this->inline_edit_type = 'radiolist';

			parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $lookup_field_value, $entry ), $current_form['id'], $field_id );

		} elseif ( $this->is_checklist_type( $gf_field ) ) {
			$this->inline_edit_type        = 'checklist';
			$this->standard_live_update    = true;
			$this->live_update_json_encode = false;
			$this->set_value               = false;

			$checklist_value = self::_get_inline_edit_value( $gf_field, $entry, false );

			if ( ! empty( $checklist_value ) ) {
				$wrapper_attributes['data-value'] = json_encode( array_map( 'strval', $checklist_value ) );
			}

			parent::add_field_template( $this->inline_edit_type, $gf_field->get_field_input( $current_form, $lookup_field_value, $entry ), $current_form['id'], $field_id );
		}

		$choices = array_map( function ( $choice ) {
			$choice['value'] = (string) $choice['value'];

			return $choice;
		}, $gf_field->get_lookup_choices() );

		$wrapper_attributes['data-source'] = json_encode( $choices );

		$wrapper_attributes['class'] = $wrapper_attributes['class'] . ' gv-inline-edit-lookup';

		return parent::modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field );
	}

	/**
	 * Whether the lookup field renders as radio buttons.
	 *
	 * Handles both the legacy 'radio' type and the new 'multi_choice' type
	 * with choiceLimit set to 'single'.
	 *
	 * @since TBD
	 *
	 * @param GF_Field $gf_field The lookup field.
	 *
	 * @return bool
	 */
	private function is_radio_type( $gf_field ) {
		if ( 'radio' === $gf_field->lookupInputType ) {
			return true;
		}

		return 'multi_choice' === $gf_field->lookupInputType && 'single' === $gf_field->choiceLimit;
	}

	/**
	 * Set live update properties for checklist-type lookup fields during AJAX saves.
	 *
	 * The modify_inline_edit_attributes method sets these properties during page rendering,
	 * but during AJAX saves, only updated_result is called. We need to detect the field
	 * type and configure live update before the parent processes the result.
	 *
	 * @since TBD
	 *
	 * @param bool|WP_Error $update_result
	 * @param array         $entry
	 * @param int           $form_id
	 * @param GF_Field|null $gf_field
	 *
	 * @return bool|WP_Error|array
	 */
	public function updated_result( $update_result, $entry = array(), $form_id = 0, GF_Field $gf_field = null ) {
		if ( $gf_field && 'lookup' === $gf_field->type && $this->is_checklist_type( $gf_field ) ) {
			$this->standard_live_update    = true;
			$this->live_update_json_encode = false;
		}

		return parent::updated_result( $update_result, $entry, $form_id, $gf_field );
	}

	/**
	 * Whether the lookup field renders as checkboxes.
	 *
	 * Handles both the legacy 'checkboxes' type and the new 'multi_choice' type
	 * with choiceLimit other than 'single'.
	 *
	 * @since TBD
	 *
	 * @param GF_Field $gf_field The lookup field.
	 *
	 * @return bool
	 */
	private function is_checklist_type( $gf_field ) {
		if ( 'multi_choice' === $gf_field->lookupInputType && 'single' !== $gf_field->choiceLimit ) {
			return true;
		}

		// Legacy 'checkboxes' type from pre-1.3.0 Dynamic Lookup plugin.
		return ! in_array( $gf_field->lookupInputType, array( 'dropdown', 'radio', 'multi_choice' ), true );
	}

	/**
	 * Get the value used in Inline Edit `data-value` attribute
	 *
	 * @param GF_Field_Checkbox $gf_field
	 * @param array             $entry   Entry object
	 * @param bool              $as_json Whether to return as JSON-encoded string or raw array
	 *
	 * @return array|string JSON-encoded array, or array (depending on $as_json)
	 */
	public static function _get_inline_edit_value( $gf_field, $entry, $as_json = true ) {
		$field_id = $gf_field->id;

		/** @var GF_Field_Checkbox $gf_field */
		$checklist_value = array();
		$choice_number   = 1;

		foreach ( $gf_field->get_lookup_choices() as $choice ) {
			if ( 0 == $choice_number % 10 ) { // hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
				++$choice_number;
			}
			$input_id = $field_id . '.' . $choice_number;

			$current_checklist_entry = rgar( $entry, $input_id, false );

			if ( $current_checklist_entry ) {
				$checklist_value[] = $current_checklist_entry;
			}
			++$choice_number;
		}

		return $as_json ? json_encode( $checklist_value ) : $checklist_value;
	}
}

new GravityView_Inline_Edit_Field_Lookup();
