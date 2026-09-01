<?php
/**
 * Add Jetpack CRM Client Portal Pro plugin compatibility to GravityView
 *
 * @file      class
 * @package   GravityKit\GravityView\Integration
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      http://www.gravitykit.com
 * @copyright Copyright 2025, Katz Web Services, Inc.
 *
 * @since 2.43.0
 */

namespace GravityKit\GravityView\Integration;

/**
 * Add support for the Jetpack CRM Client Portal Pro plugin
 *
 * @since 2.43.0
 */
class JetpackCRMClientPortalPro extends AbstractPluginHooks {

	use PermalinkOverrideTrait;

	/**
	 * @inheritDoc
	 *
	 * @since 2.43.0
	 *
	 * @var string
	 */
	protected $constant_name = 'ZBS_CLIENTPORTALPRO_ROOTFILE';

	/**
	 * In addition to Client Portal Pro, we need to make sure that Jetpack CRM is loaded.
	 *
	 * @since 2.43.0
	 *
	 * @var string
	 */
	protected $class_name = 'ZeroBSCRM';

	/**
	 * Remove the permalink structure for Jetpack CRM Client Portal endpoints.
	 *
	 * @since 2.43.0
	 *
	 * @return bool Whether to remove the permalink structure from View rendered links.
	 */
	protected function should_disable_permalink_structure() {

		if ( ! is_callable( 'ZeroBSCRM::instance' ) ) {
			return false;
		}

		$zbs = \ZeroBSCRM::instance();

		if ( ! isset( $zbs->modules->portal ) ) {
			return false;
		}

		return $zbs->modules->portal->is_a_client_portal_endpoint();
	}
}
