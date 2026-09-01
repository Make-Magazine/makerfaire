<?php
/**
 * Register RCP scripts and styles to GravityView no-conflict list
 *
 * @file      class-gravityview-theme-hooks-rcp.php
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2017, Katz Web Services, Inc.
 *
 * @since 1.21.5
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 */
class RCP extends AbstractPluginHooks {

	/**
	 * @inheritDoc
	 * @since 1.21.5
	 */
	protected $script_handles = [
		'rcp-admin-scripts',
		'bbq',
	];

	/**
	 * @inheritDoc
	 * @since 1.21.5
	 */
	protected $constant_name = 'RCP_PLUGIN_VERSION';
}
