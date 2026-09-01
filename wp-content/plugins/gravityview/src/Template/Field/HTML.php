<?php
/**
 * The Field HTML Template class.
 *
 * Attached to a \GV\Field and used by a \GV\Field_Renderer.
 *
 * @package GravityKit\GravityView\Template\Field
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Template\Field;

use GravityKit\GravityView\Template\TemplateField;

/**
 * The Field HTML Template class.
 *
 * Attached to a \GV\Field and used by a \GV\Field_Renderer.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Template\Field namespace.
 */
class HTML extends TemplateField {
	/**
	 * @var string The template slug to be loaded (like "table", "list", "plain")
	 */
	public static $slug = 'html';
}
