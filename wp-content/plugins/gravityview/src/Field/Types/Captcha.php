<?php
/**
 * Captcha field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Captcha class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

class Captcha extends \GravityView_Field {

	var $name = 'captcha';

	var $is_searchable = false;

	var $_gf_field_class_name = 'GF_Field_CAPTCHA';

	var $group = 'advanced';

	var $icon = 'dashicons-shield-alt';

	public function __construct() {
		$this->label = esc_html__( 'CAPTCHA', 'gk-gravityview' );
		parent::__construct();
	}
}
