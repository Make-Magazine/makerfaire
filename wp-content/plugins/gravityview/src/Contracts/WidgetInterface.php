<?php
/**
 * Widget Interface for the PSR-4 namespace migration.
 *
 * Defines the core contract for Widget entities in GravityView.
 * Any class that represents a Widget must implement this interface.
 *
 * @package GravityKit\GravityView\Contracts
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Contracts;

/**
 * Contract for Widget entities.
 *
 * A Widget provides supplementary UI elements within a View, such as
 * pagination, search bars, or custom content areas. This interface
 * defines the essential behaviors that any Widget implementation must provide.
 *
 * @since 3.0.0
 */
interface WidgetInterface {

	/**
	 * Returns the widget's unique ID.
	 *
	 * @since 3.0.0
	 *
	 * @return string The widget identifier.
	 */
	public function get_widget_id();

	/**
	 * Renders the widget on the frontend.
	 *
	 * @since 3.0.0
	 *
	 * @param array                            $widget_args Widget arguments.
	 * @param string                           $content     Widget content.
	 * @param string|\GV\Template_Context|null $context     Template context.
	 *
	 * @return void
	 */
	public function render_frontend( $widget_args, $content = '', $context = '' );

	/**
	 * Determines whether this widget should render.
	 *
	 * @since 3.0.0
	 *
	 * @param string|\GV\Template_Context|null $context Template context.
	 *
	 * @return bool True if the widget should render.
	 */
	public function pre_render_frontend( $context = '' );

	/**
	 * Returns the widget configuration as an array.
	 *
	 * @since 3.0.0
	 *
	 * @return array The widget configuration.
	 */
	public function as_configuration();
}
