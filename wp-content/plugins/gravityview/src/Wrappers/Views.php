<?php
/**
 * The views magic wrapper.
 *
 * @package GravityKit\GravityView\Wrappers
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Wrappers;

/**
 * The views magic wrapper.
 *
 * Provides magic access to View data via the `gravityview()` helper.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\Wrappers namespace.
 */
class Views {

	/**
	 * @var \GV\View An internal View keeper.
	 */
	private $view = null;

	/**
	 * Gets a View.
	 *
	 * Doesn't care what you provide it. Will try to find
	 *  out what you need from the current context, from the supplied
	 *  args, etc.
	 *
	 * @param string|int|array|\GV\View|\WP_Post|null $view Anything goes.
	 *
	 * @return \GV\View|\GV\View_Collection|null The detected View, Views, or null.
	 */
	public function get( $view = null ) {

		/**
		 * By View.
		 */
		if ( $view instanceof \GV\View && $view->ID ) {
			return $this->get( $view->ID );
		}

		/**
		 * By View ID.
		 */
		if ( is_numeric( $view ) ) {
			return \GV\View::by_id( $view );
		}

		/**
		 * By post object.
		 */
		if ( $view instanceof \WP_Post ) {
			return \GV\View::from_post( $view );
		}

		/**
		 * By array.
		 */
		if ( is_array( $view ) && ! empty( $view['id'] ) ) {
			return $this->get( $view['id'] );
		}

		/**
		 * From various contexts.
		 */
		if ( is_null( $view ) ) {
			if ( gravityview()->request->is_renderable() && $view = gravityview()->request->is_view() ) {
				return $view;
			}

			global $post;

			if ( $post instanceof \WP_Post ) {
				$views = \GV\View_Collection::from_post( $post );

				// When no Views are found, return null.
				if ( 0 === $views->count() ) {
					return $this->view;
				}

				// When only one View is found, return a \GV\View.
				if ( 1 === $views->count() ) {
					return $views->first();
				}

				// Otherwise, return a \GV\View_Collection.
				return $views;
			}

			/**
			 * Final fallback.
			 */
			return $this->view;
		}

		return null;
	}

	/**
	 * Mock the internal pointer.
	 *
	 * @param \GV\View $view The View to supply on fallback in ::get()
	 *
	 * @return void
	 */
	public function set( $view ) {
		$this->view = $view;
	}
}
