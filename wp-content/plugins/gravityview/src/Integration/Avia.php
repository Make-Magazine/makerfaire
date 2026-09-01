<?php
/**
 * Add Avia Framework theme compatibility to GravityView
 *
 * @file      class-gravityview-theme-hooks-avia.php
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
class Avia extends AbstractPluginHooks {

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $constant_name = 'AV_FRAMEWORK_VERSION';

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $content_meta_keys = [
		'_aviaLayoutBuilderCleanData',
	];
}
