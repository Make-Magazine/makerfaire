<?php
/**
 * A legacy fallback View template.
 *
 * Can be used to render old templates as needed.
 *
 * @package GravityKit\GravityView\Template\View
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Template\View;

/**
 * A legacy fallback View template.
 *
 * Can be used to render old templates as needed.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Template\View namespace.
 */
class Legacy extends \GV\View_Template {
	/**
	 * Render an old template.
	 */
	public function render() {
		if ( ! class_exists( \GravityKit\GravityView\Template\LegacyTemplate::class ) ) {
			return;
		}

		$context = [
			'view'    => $this->view,
			'fields'  => $this->view->fields->by_visible( $this->view ),
			'entries' => $this->entries,
			'request' => $this->request,
		];

		global $post;

		if ( $post ) {
			$context['post'] = $post;
		}

		\GV\Mocks\Legacy_Context::push( $context );

		$sections = [ 'header', 'body', 'footer' ];

		$sections = apply_filters( 'gravityview_render_view_sections', $sections, $this->view->settings->get( 'template' ) );

		$template = \GravityView_View::getInstance();

		$slug = gravityview_get_template_slug( $this->view->settings->get( 'template' ), 'directory' );

		foreach ( $sections as $section ) {
			$template->render( $slug, $section, false );
		}

		\GV\Mocks\Legacy_Context::pop();
	}
}
