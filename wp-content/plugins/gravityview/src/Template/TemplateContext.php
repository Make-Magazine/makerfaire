<?php
/**
 * A template Context class.
 *
 * This is provided to most template files as a global.
 *
 * @package GravityKit\GravityView\Template
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Template;

/**
 * A template Context class.
 *
 * This is provided to most template files as a global.
 */
class TemplateContext extends \GV\Context {
	/**
	 * @var \GV\Template The template.
	 */
	public $template;

	/**
	 * @var \GV\View The view.
	 */
	public $view;

	/**
	 * @var \GV\Entry The entry. If single-entry view.
	 */
	public $entry;

	/**
	 * @var \GV\Entry_Collection The entries. If directory view.
	 */
	public $entries;

	/**
	 * @var \GV\Field_Collection The fields.
	 */
	public $fields;

	/**
	 * @var \GV\Field The field. When rendering a single field.
	 */
	public $field;

	/**
	 * @var \GV\Source The data source for a field.
	 */
	public $source;

	/**
	 * @var mixed The display value for a field.
	 */
	public $display_value;

	/**
	 * @var mixed The raw value for a field.
	 */
	public $value;

	/**
	 * @var \GV\Request The request.
	 */
	public $request;

	/**
	 * Create a context from a Template
	 *
	 * @param \GV\Template|array $template The template or array with values expected in a template
	 * @param array              $data Additional data not tied to the template object.
	 *
	 * @return \GV\Template_Context The context holder.
	 */
	public static function from_template( $template, $data = [] ) {
		$context = new self();

		$context->template = $template;

		/**
		 * Data.
		 */
		$context->display_value = \GV\Utils::get( $data, 'display_value' );
		$context->value         = \GV\Utils::get( $data, 'value' );

		/**
		 * Shortcuts.
		 */
		$context->view    = \GV\Utils::get( $template, 'view' );
		$context->source  = \GV\Utils::get( $template, 'source' );
		$context->field   = \GV\Utils::get( $template, 'field' ) ? : \GV\Utils::get( $data, 'field' );
		$context->entry   = \GV\Utils::get( $template, 'entry' ) ? : \GV\Utils::get( $data, 'entry' );
		$context->request = \GV\Utils::get( $template, 'request' );

		$context->entries = \GV\Utils::get( $template, 'entries' ) ? $template->entries : null;
		$context->fields  = \GV\Utils::get( $template, 'fields' )
			?: \GV\Utils::get( $data, 'fields' )
			?: ( $context->view ? $context->view->fields : null );

		return $context;
	}
}
