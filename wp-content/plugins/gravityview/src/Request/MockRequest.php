<?php
/**
 * A mock for testing.
 *
 * @package GravityKit\GravityView\Request
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Request;

/**
 * A mock for testing.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Request namespace.
 */
class MockRequest extends Request {
	/**
	 * @var array The return values.
	 */
	public $returns = [
		'is_view'       => false,
		'is_entry'      => false,
		'is_edit_entry' => false,
		'is_search'     => false,
		'get_arguments' => [],
	];

	public function is_view( $return_view = true ) {
		return $this->__call( __FUNCTION__, func_get_args() );
	}

	public function is_entry( $form_id = 0 ) {
		return $this->__call( __FUNCTION__, func_get_args() );
	}

	public function is_edit_entry( $form_id = 0 ) {
		return $this->__call( __FUNCTION__, func_get_args() );
	}

	public function is_search( $view = null ) {
		return $this->__call( __FUNCTION__, func_get_args() );
	}

	public function get_arguments(): array {
		return (array) $this->__call( __FUNCTION__, func_get_args() );
	}

	public function __call( $function, $args ) {
		return \GV\Utils::get( $this->returns, $function, null );
	}
}
