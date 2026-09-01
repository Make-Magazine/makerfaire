<?php
/**
 * Add Avada Theme compatibility to GravityView, including registering scripts and styles to GravityView no-conflict list
 *
 * @file      class-gravityview-theme-hooks-avada.php
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
class Avada extends AbstractPluginHooks {

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $function_name = 'avada_scripts';

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $content_meta_keys = [
		'sbg_selected_sidebar',
	];

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $script_handles = [
		'jquery.biscuit',
		'avada_upload',
		'tipsy',
		'jquery-ui-slider',
		'smof',
		'cookie',
		'kd-multiple-featured-images',
	];
}
