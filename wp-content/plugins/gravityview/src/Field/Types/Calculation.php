<?php
/**
 * Calculation field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Calculation class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

class Calculation extends \GravityView_Field {

	var $name = 'calculation';

	var $is_searchable = false;

	var $group = 'pricing';

	var $_gf_field_class_name = 'GF_Field_Calculation';

	/**
	 * Calculation constructor.
	 */
	public function __construct() {

		$this->label = esc_html__( 'Calculation', 'gk-gravityview' );

		add_filter( 'gravityview_blocklist_field_types', [$this, 'blocklist_field_types'], 10, 2 );

		parent::__construct();
	}

	/**
	 * @deprecated 2.14
	 */
	public function blacklist_field_types( $field_types = [], $context = '' ) {
		_deprecated_function( 'GravityView_Field_Calculation::blacklist_field_types', '2.14', 'GravityView_Field_Calculation::blocklist_field_types' );
		return $this->blocklist_field_types( $field_types, $context );
	}


	/**
	 * Don't show the Calculation field in field picker
	 *
	 * @since 2.14
	 *
	 * @param array  $field_types Array of field types
	 * @param string $context
	 *
	 * @return array Field types with calculation added, if not Edit Entry context
	 */
	public function blocklist_field_types( $field_types = [], $context = '' ) {

		// Allow Calculation field in Edit Entry
		if ( 'edit' !== $context ) {
			$field_types[] = $this->name;
		}

		return $field_types;
	}
}
