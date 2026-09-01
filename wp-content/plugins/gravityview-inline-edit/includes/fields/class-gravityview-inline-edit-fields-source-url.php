<?php

/**
 * @file class-gravityview-inline-edit-field-source-url.php
 *
 * @since 1.0
 */
class GravityView_Inline_Edit_Field_Source_Url extends GravityView_Inline_Edit_Field {

	public $gv_field_name = 'source_url';

	public $inline_edit_type = 'url';

	public $set_value = true;

}

new GravityView_Inline_Edit_Field_Source_Url;
