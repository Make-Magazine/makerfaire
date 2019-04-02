<?php

/**
 * @file class-gravityview-inline-edit-field-email.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Email extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'email';

	/** @see GF_Field_Email $gf_field */
	var $inline_edit_type = 'email';

	var $set_value = true;

}

new GravityView_Inline_Edit_Field_Email;
