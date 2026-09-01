<?php
/**
 * The Entry Layout Builder Template class.
 *
 * @package GravityKit\GravityView\Template\Entry
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Template\Entry;

use GV\Field_Renderer_Trait;

/**
 * The single Entry template.
 *
 * @since 3.0.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Template\Entry namespace.
 */
final class LayoutBuilder extends \GV\Entry_Template {
	use Field_Renderer_Trait;
	/**
	 * {@inheritDoc}
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	public static $slug = \GravityView_Layout_Builder::ID;
}
