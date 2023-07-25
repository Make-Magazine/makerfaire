<?php

namespace GravityKit\MultipleForms\Query;

use GravityKit\MultipleForms\AbstractSingleton;
use GravityKit\MultipleForms\Query;

/**
 * Class Handler
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms
 */
class Handler extends AbstractSingleton {
	/**
	 * Registering happens after the singleton instance has been set up, which is after the extension was confirmed to have
	 * its requirements met and after `plugins_loaded@P20`
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	protected function register(): void {
		// TEMPORARY!
		add_filter( 'gravityview/query/class', [ $this, '_patch_query' ] );

		$feature_joins = $this->get_joins_feature_slug();
		add_filter( "gravityview/plugin/feature/{$feature_joins}", '__return_true' );
	}

	/**
	 * Try to fetch from GV the feature joins using the constant.
	 *
	 * @since 0.3
	 *
	 * @return string
	 */
	public function get_joins_feature_slug(): string {
		return \GV\Plugin::FEATURE_JOINS;
	}

	/**
	 * Temporary patch for the query, until changes are merged into Gravity Forms core
	 *
	 * @see  https://github.com/gravityforms/gravityforms/pull/448
	 * @internal
	 * @todo Remove when GF containing the patch is released.
	 *
	 * @return string The class name of the query to use for a GravityView query ("\GF_Patched_Query")
	 */
	public function _patch_query() {
		return Query::class;
	}
}
