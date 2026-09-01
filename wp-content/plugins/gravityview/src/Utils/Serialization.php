<?php
/**
 * Serialization helpers.
 *
 * @package   GravityKit\GravityView\Utils
 * @license   GPL2+
 * @link      http://www.gravitykit.com
 */

namespace GravityKit\GravityView\Utils;

/**
 * Object-safe (de)serialization helpers.
 *
 * @since 3.0.1
 */
final class Serialization {

	/**
	 * Object-safe drop-in replacement for WordPress's maybe_unserialize().
	 *
	 * Returns the unserialized value when $value is serialized, or the original
	 * value when it is not. Unlike maybe_unserialize(), serialized objects are
	 * NOT instantiated (`allowed_classes => false`), so an untrusted value
	 * (entry field values, post meta, imported data) cannot trigger PHP object
	 * injection. Legitimate serialized arrays of scalars are returned unchanged.
	 *
	 * @since 3.0.1
	 *
	 * @param mixed $value Value to unserialize if serialized.
	 *
	 * @return mixed The unserialized value, or $value unchanged when not serialized.
	 */
	public static function maybe_unserialize( $value ) {
		if ( ! is_string( $value ) || ! is_serialized( $value ) ) {
			return $value;
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- This IS the safe wrapper: allowed_classes => false prevents object injection, and existing entry/meta values are PHP-serialized (not JSON).
		return unserialize( $value, [ 'allowed_classes' => false ] );
	}
}
