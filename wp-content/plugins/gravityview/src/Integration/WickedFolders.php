<?php
/**
 * Add Wicked Folders compatibility to GravityView
 *
 * @file      class-gravityview-theme-hooks-wicked-folders.php
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2020, Katz Web Services, Inc.
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 */
class WickedFolders extends AbstractPluginHooks {

	protected $class_name = 'Wicked_Folders';

	protected $style_handles = [
		'wicked-folders-admin',
	];

	protected $script_handles = [
		'wicked-folders-admin',
		'wicked-folders-app',
	];
}
