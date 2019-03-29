<?php

/**
 * @file class-gravityview-inline-edit-field-select.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Select extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'select';

	/** @var GF_Field_Select $gf_field */
	var $inline_edit_type = 'select';

	var $set_value = true;

}

new GravityView_Inline_Edit_Field_Select;
