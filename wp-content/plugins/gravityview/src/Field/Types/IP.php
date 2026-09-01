<?php
/**
 * IP field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_IP class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 3.0.0
 */
class IP extends \GravityView_Field {

	var $name = 'ip';

	var $is_searchable = true;

	var $search_operators = ['is', 'isnot', 'contains'];

	var $group = 'meta';

	var $icon = 'dashicons-laptop';

	var $is_numeric = true;

	public function __construct() {
		$this->label       = __( 'User IP', 'gk-gravityview' );
		$this->description = __( 'The IP Address of the user who created the entry.', 'gk-gravityview' );
		parent::__construct();
	}
}
