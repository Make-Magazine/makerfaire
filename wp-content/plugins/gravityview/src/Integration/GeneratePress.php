<?php
/**
 * Add GeneratePress Theme compatibility to GravityView
 *
 * @file      class-gravityview-theme-hooks-generatepress.php
 * @since     1.15.2
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @package   GravityKit\GravityView\Integration
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 * @since 1.15.2
 */
class GeneratePress extends AbstractPluginHooks {

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $constant_name = 'GENERATE_VERSION';

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $content_meta_keys = [
		'_generate-sidebar-layout-meta',
		'_generate-footer-widget-meta',
	];
}
