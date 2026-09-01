<?php
/**
 * Marker + factory contract for value-object inputs.
 *
 * @package     GravityKit\GravityView\Contracts
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\Contracts;

use WP_Error;

/**
 * Every domain-service input value object implements this interface.
 *
 * The `from_array()` factory pattern is preferred over throwing constructors
 * because it composes cleanly with `WP_Error`-returning conventions — adapters
 * can short-circuit on invalid input without try/catch.
 *
 * Example:
 * ```php
 * $input = CreateViewInput::from_array( $request->get_params() );
 * if ( is_wp_error( $input ) ) {
 *     return $input;
 * }
 * return $this->factory->create( $input );
 * ```
 *
 * @since 3.0.0
 */
interface Input {

	/**
	 * Construct the value object from a parsed input array, validating fields
	 * as part of construction. Returns the object on success, WP_Error on
	 * validation failure.
	 *
	 * @since 3.0.0
	 *
	 * @param array<string,mixed> $data Parsed input data (from REST request,.
	 *                                  ability execute_callback, etc).
	 *
	 * @return self|WP_Error
	 */
	public static function from_array( array $data );
}
