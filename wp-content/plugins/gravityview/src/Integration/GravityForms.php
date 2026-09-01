<?php
/**
 * Add Gravity Forms scripts and styles to GravityView no-conflict list
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
class GravityForms extends AbstractPluginHooks {

	public $class_name = 'GFForms';

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $style_handles = [
		'gform_tooltip',
		'gform_font_awesome',
		'gform_admin_icons',
	];

	/**
	 * @inheritDoc
	 * @since 1.15.2
	 */
	protected $script_handles = [
		'gform_tooltip_init',
		'gform_field_filter',
		'gform_forms',
	];
}
