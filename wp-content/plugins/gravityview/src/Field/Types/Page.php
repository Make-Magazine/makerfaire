<?php
/**
 * Page field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Page class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

class Page extends \GravityView_Field {

	var $name = 'page';

	var $is_searchable = false;

	/** @see GF_Field_Page */
	var $_gf_field_class_name = 'GF_Field_Page';

	var $group = 'standard';

	var $icon = 'dashicons-media-text';

	public function __construct() {
		$this->label = esc_html__( 'Page', 'gk-gravityview' );
		parent::__construct();
	}
}
