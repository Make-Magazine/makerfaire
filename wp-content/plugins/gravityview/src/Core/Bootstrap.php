<?php
/**
 * Bootstrap marker class for the PSR-4 namespace migration.
 *
 * This class serves as a verification target to confirm that the
 * PSR-4 autoloader is correctly configured for the new namespace.
 *
 * @package GravityKit\GravityView\Core
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Core;

/**
 * Bootstrap marker for PSR-4 autoload verification.
 *
 * @since 3.0.0
 */
class Bootstrap {

	/**
	 * Returns true to confirm the PSR-4 autoloader is working.
	 *
	 * @since 3.0.0
	 *
	 * @return bool Always true.
	 */
	public static function is_loaded() {
		return true;
	}
}
