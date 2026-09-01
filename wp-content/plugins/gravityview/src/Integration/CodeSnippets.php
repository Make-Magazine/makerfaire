<?php
/**
 * Add Code Snippet customizations
 *
 * @file      class-gravityview-plugin-hooks-code-snippets.php
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2021, Katz Web Services, Inc.
 *
 * @since 2.13.2
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 * @since 2.13.2
 */
class CodeSnippets extends AbstractPluginHooks {

	/**
	 * @since 2.13.2
	 */
	protected $constant_name = 'CODE_SNIPPETS_FILE';

	/**
	 * @since 2.13.2
	 * @var array
	 */
	protected $style_handles = ['menu-icon-snippets'];
}
