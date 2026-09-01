<?php
/**
 * Add Query Monitor customizations
 *
 * @file      class-gravityview-plugin-hooks-query-monitor.php
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.16.5
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 * @since 2.0
 */
class QueryMonitor extends AbstractPluginHooks {

	/**
	 * @since 2.0
	 */
	protected $class_name = 'QueryMonitor';

	/**
	 * @since 2.0
	 */
	protected $script_handles = [
		'query-monitor',
	];

	/**
	 * @since 2.0
	 */
	protected $style_handles = [
		'query-monitor',
	];
}
