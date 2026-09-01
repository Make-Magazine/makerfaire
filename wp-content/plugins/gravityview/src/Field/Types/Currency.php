<?php
/**
 * Currency field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Currency class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * @since 1.16
 */
class Currency extends \GravityView_Field {

	var $name = 'currency';

	var $is_searchable = true;

	var $is_numeric = true;

	var $search_operators = ['is', 'isnot'];

	var $group = 'pricing';

	var $_custom_merge_tag = 'currency';

	public $icon = 'dashicons-money-alt';

	/**
	 * GravityView_Field_Currency constructor.
	 */
	public function __construct() {
		$this->label = esc_html__( 'Currency', 'gk-gravityview' );
		parent::__construct();
	}
}
