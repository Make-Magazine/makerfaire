<?php
/**
 * The Legacy Entry Template class.
 *
 * A back-compatibility layer for old templates to work.
 *
 * @package GravityKit\GravityView\Template\Entry
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Template\Entry;

/**
 * The Legacy Entry Template class.
 *
 * A back-compatibility layer for old templates to work.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Template\Entry namespace.
 */
class Legacy extends \GV\Entry_Template {
	/**
	 * Render an old template.
	 */
	public function render() {
		if ( ! class_exists( \GravityKit\GravityView\Template\LegacyTemplate::class ) ) {
			return;
		}

		$entries = new \GV\Entry_Collection();
		$entries->add( $this->entry );

		$context = [
			'view'    => $this->view,
			'fields'  => $this->view->fields->by_visible( $this->view ),
			'entries' => $entries,
			'entry'   => $this->entry,
			'request' => $this->request,
		];

		global $post;

		if ( $post ) {
			$context['post'] = $post;
		}

		\GV\Mocks\Legacy_Context::push( $context );

		$sections = [ 'single' ];

		$sections = apply_filters( 'gravityview_render_view_sections', $sections, $this->view->settings->get( 'template' ) );

		$template = \GravityView_View::getInstance();

		$slug = gravityview_get_template_slug( $this->view->settings->get( 'template' ), 'directory' );

		foreach ( $sections as $section ) {
			$template->render( $slug, $section, false );
		}

		\GV\Mocks\Legacy_Context::pop();
	}
}
