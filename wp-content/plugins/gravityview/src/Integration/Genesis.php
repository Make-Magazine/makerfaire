<?php
/**
 * Add Genesis Framework compatibility to GravityView, including registering scripts and styles to GravityView no-conflict list
 *
 * @file      class-gravityview-theme-hooks-genesis.php
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
class Genesis extends AbstractPluginHooks {

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $function_name = 'genesis';

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $script_handles = [
		'genesis_admin_js',
	];

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $style_handles = [
		'genesis_admin_css',
	];

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $post_type_support = [
		'genesis-layouts',
		'genesis-seo',
	];
}
