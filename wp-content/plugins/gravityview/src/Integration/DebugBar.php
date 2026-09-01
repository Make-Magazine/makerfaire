<?php
/**
 * Add Debug Bar scripts and styles to GravityView no-conflict list
 *
 * @file      class-gravityview-plugin-hooks-gravity-forms.php
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.15.2
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 * @since 1.15.2
 */
class DebugBar extends AbstractPluginHooks {

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $class_name = 'Debug_Bar';

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $style_handles = [
		'debug-bar-extender',
		'debug-bar',
		'debug-bar-codemirror',
		'debug-bar-console',
		'puc-debug-bar-style',
	];

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $script_handles = [
		'debug-bar-extender',
		'debug-bar',
		'debug-bar-codemirror',
		'debug-bar-console',
		'puc-debug-bar-js',
	];
}
