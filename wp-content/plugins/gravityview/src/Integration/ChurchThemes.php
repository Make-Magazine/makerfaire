<?php
/**
 * Add Church Themes compatibility to GravityView
 *
 * @file      class-gravityview-theme-hooks-church-themes.php
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2016, Katz Web Services, Inc.
 *
 * @since 1.17
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 * @since 1.17
 */
class ChurchThemes extends AbstractPluginHooks {

	/**
	 * Church Themes framework version number
	 *
	 * @inheritDoc
	 * @since 1.17
	 */
	protected $constant_name = 'CTFW_VERSION';

	/**
	 * Add filters
	 *
	 * @since 1.17
	 *
	 * @return void
	 */
	protected function add_hooks() {
		parent::add_hooks();

		add_filter( 'ctfw_has_content', [ $this, 'if_gravityview_return_true' ] );
	}

	/**
	 * Tell Church Themes that GravityView has content if the current page is a GV post type or has shortcode
	 *
	 * @since 1.17
	 *
	 * @param bool $has_content Does the post have content?
	 *
	 * @return bool True: It is GV post type, or has shortcode, or $has_content was true.
	 */
	public function if_gravityview_return_true( $has_content = false ) {

		if ( ! class_exists( \GravityKit\GravityView\Frontend\Frontend::class ) ) {
			return $has_content;
		}

		$instance = \GravityView_frontend::getInstance();

		return ( $instance->is_gravityview_post_type || $instance->post_has_shortcode ) ? true : $has_content;
	}
}
