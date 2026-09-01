<?php
/**
 * The Edit Entry Renderer class.
 *
 * The edit entry renderer.
 *
 * @package GravityKit\GravityView\Renderer
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Renderer;

use GV\Entry;
use GV\Entry_Collection;
use GV\Mocks\Legacy_Context;
use GV\Request;
use GV\View;

/**
 * The Edit Entry Renderer class.
 *
 * The edit entry renderer.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Renderer namespace.
 */
class EntryEditRenderer extends \GV\Entry_Renderer {

	/**
	 * Renders a an editable \GV\Entry instance.
	 *
	 * @param \GV\Entry   $entry The Entry instance to render.
	 * @param \GV\View    $view The View connected to the entry.
	 * @param \GV\Request $request The request context we're currently in. Default: `gravityview()->request`
	 *
	 * @todo Just a wrapper around the old code. Cheating. Needs rewrite :)
	 *
	 * @api
	 * @since 2.0
	 *
	 * @return string The rendered Entry edit screen.
	 */
	public function render( Entry $entry, View $view, ?Request $request = null ) {
		$entries = new \GV\Entry_Collection();
		$entries->add( $entry );

		\GV\Mocks\Legacy_Context::push(
			array(
				'view'    => $view,
				'entries' => $entries,
			)
		);

		/**
		 * Register the View on the rendering stack so context-aware checks (such as the
		 * `secret` exemption for shortcodes embedded in their own View) recognize that this
		 * View is currently being rendered. The directory renderer does the same.
		 *
		 * @since 3.0.0
		 */
		\GV\View::push_rendering( $view->ID );

		ob_start();

		/**
		 * Triggered when rendering the Edit Entry screen.
		 *
		 * @since 2.0
		 *
		 * @param null        $deprecated Deprecated parameter. Always null.
		 * @param \GV\Entry   $entry      The Entry instance being edited.
		 * @param \GV\View    $view       The View connected to the entry.
		 * @param \GV\Request $request    The request context.
		 */
		do_action( 'gravityview_edit_entry', null, $entry, $view, $request );

		\GV\View::pop_rendering();

		\GV\Mocks\Legacy_Context::pop();

		return ob_get_clean();
	}
}
