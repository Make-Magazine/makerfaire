<?php
/**
 * Add WooCommerce scripts and styles to GravityView no-conflict list
 *
 * @file      class-gravityview-plugin-hooks-woocommerce.php
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.15.2
 */

namespace GravityKit\GravityView\Integration;

/**
 * @inheritDoc
 */
class WooCommerce extends AbstractPluginHooks {

	use PermalinkOverrideTrait;

	/**
	 * The function name to fetch WooCommerce page IDs.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	protected $function_name = 'wc_get_page_id';

	/**
	 * @inheritDoc
	 */
	protected $style_handles = [
		'woocommerce_admin_menu_styles',
		'woocommerce_admin_styles',
	];

	/**
	 * Remove the permalink structure for LearnDash post types.
	 *
	 * @since 3.0.0
	 *
	 * @return bool Whether to remove the permalink structure from View rendered links.
	 */
	protected function should_disable_permalink_structure() {
		$page_id = wc_get_page_id( 'myaccount' );

		if ( get_the_ID() !== $page_id ) {
			return false;
		}

		return true;
	}
}
