<?php
namespace GravityKit\GravityView\GravityForms\QueryExtensions;

if ( ! class_exists( 'GF_Query_Condition', false ) ) {
	return;
}

/**
 * Gravity Forms GF_Query does not support NULL condition clauses.
 *
 * Implement them as needed.
 */
class GFQueryConditionIsNull extends \GF_Query_Condition {
	private $override_placeholder = '{GF_Query_Condition_IS_NULL_override}';

	public function __construct( $left = null, $operator = null, $right = null ) {
		parent::__construct( $left, self::EQ, new \GF_Query_Literal( $this->override_placeholder ) );
	}

	public function sql( $query ) {
		return str_replace( "= '{$this->override_placeholder}'", 'IS NULL', parent::sql( $query ) );
	}
}
