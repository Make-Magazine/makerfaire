<?php

/**
 * @file class-gravityview-inline-edit-field-phone.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Phone extends GravityView_Inline_Edit_Field {

	var $gv_field_name = 'phone';

	var $inline_edit_type = 'tel';

	var $set_value = true;

}

new GravityView_Inline_Edit_Field_Phone;
