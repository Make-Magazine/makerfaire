<?php

/**
 * @file class-gravityview-inline-edit-field-website.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Website extends GravityView_Inline_Edit_Field {

	public $gv_field_name = 'website';

	public $inline_edit_type = 'url';

	public $set_value = true;

}

new GravityView_Inline_Edit_Field_Website;
