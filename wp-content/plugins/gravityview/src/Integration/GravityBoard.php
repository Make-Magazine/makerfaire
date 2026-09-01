<?php
/**
 * Add GravityBoard integration to GravityView
 *
 * @file      class-gravityview-plugin-hooks-gravityboard.php
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2025, GravityKit
 *
 * @since 2.41
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 * @since 2.41
 */
class GravityBoard extends AbstractPluginHooks {

	/**
	 * @var string Check for GravityBoard constant
	 */
	protected $constant_name = 'GRAVITYBOARD_FILE';

	/**
	 * @inheritDoc
	 * @since 2.41
	 */
	protected $style_handles = [
		'gravityboard-app-styles',
	];

	/**
	 * @inheritDoc
	 * @since 2.41
	 */
	protected $script_handles = [
		'gravityboard-app',
	];

	/**
	 * Add hooks when GravityBoard is active
	 *
	 * @since 2.41
	 */
	protected function add_hooks() {
		new \GravityView_Widget_GravityBoard();

		parent::add_hooks();
	}
}
