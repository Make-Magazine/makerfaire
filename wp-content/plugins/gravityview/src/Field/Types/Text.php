<?php
/**
 * Text field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Text class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 3.0.0
 */
class Text extends \GravityView_Field {

	var $name = 'text';

	var $_gf_field_class_name = 'GF_Field_Text';

	var $is_searchable = true;

	var $search_operators = ['contains', 'is', 'isnot', 'starts_with', 'ends_with'];

	var $group = 'standard';

	var $icon = 'dashicons-editor-textcolor';

	public function __construct() {
		$this->label = esc_html__( 'Single Line Text', 'gk-gravityview' );
		parent::__construct();
	}
}
