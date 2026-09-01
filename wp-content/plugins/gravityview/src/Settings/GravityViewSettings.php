<?php
/**
 * GravityView Settings class (get/set) using the Gravity Forms App framework
 *
 * @since 1.7.4 (Before, used the Redux Framework)
 * @since 3.0.0 Migrated to PSR-4 namespace.
 * @deprecated 3.0.0 gravityview()->plugin->settings
 *
 * @package GravityKit\GravityView\Settings
 */

namespace GravityKit\GravityView\Settings;

/**
 * Deprecated settings wrapper that delegates to Plugin_Settings.
 *
 * @since 1.7.4
 * @since 3.0.0 Migrated to GravityKit\GravityView\Settings namespace.
 * @deprecated 3.0.0 gravityview()->plugin->settings
 */
class GravityViewSettings extends \GV\Plugin_Settings {
	/**
	 * @deprecated 3.0.0 gravityview()->plugin->settings
	 * @return \GV\Settings
	 */
	public function __wakeup() {}
	public function __clone() {}

	/**
	 * @deprecated 3.0.0 gravityview()->plugin->settings
	 * @return \GV\Plugin_Settings
	 */
	public static function get_instance() {
		\gravityview()->log->warning( '\GravityView_Settings is deprecated. Use gravityview()->plugin->settings instead.' );
		return gravityview()->plugin->settings;
	}
}
