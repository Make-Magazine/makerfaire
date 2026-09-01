<?php
/**
 * REST API Core initialization class.
 *
 * @package GravityKit\GravityView\REST
 * @since 3.0.0
 */

namespace GravityKit\GravityView\REST;

/**
 * REST API Core initialization class.
 *
 * Bootstraps the GravityView REST API by registering routes and providing
 * namespace/URL helpers.
 *
 * @since 2.0
 * @since 3.0.0 Migrated to GravityKit\GravityView\REST namespace.
 */
class Core {
	public static $routes;

	/**
	 * Initialization.
	 */
	public static function init() {
		if ( ! gravityview()->plugin->supports( \GV\Plugin::FEATURE_REST ) ) {
			return;
		}

		self::$routes['views'] = $views = new ViewsRoute();
		$views->register_routes();

		// Inspector routes — production traffic goes through the Abilities API
		// (`/wp-json/wp-abilities/v1/abilities/gk-gravityview/{name}/run`), so
		// `register_routes()` here is a no-op outside the test suite. Inside
		// PHPUnit (`DOING_GRAVITYVIEW_TESTS`), it brings back the legacy
		// `/gravityview/v1/views/{id}/…` surface so `Inspector_Route_Test` +
		// `Inspector_Route_Shape_Test` can keep dispatching through REST
		// without re-introducing the dual surface on production wires. The
		// InspectorRoute class itself is still constructed in both cases
		// because its handler methods are dispatched by the ability shims via
		// synthesised WP_REST_Requests.
		self::$routes['inspector'] = $inspector = new InspectorRoute();
		$inspector->register_routes();

		// Register the default template-settings sources the inspector reads,
		// writes, and discovers through. Includes the core
		// `_gravityview_template_settings` source (covers Maps and any plugin
		// using `gravityview_default_args`) and the DataTables silo bridge
		// (gated on the DT extension being active). Runs even when the legacy
		// REST routes are not exposed.
		InspectorRoute::register_default_sources();
	}

	/**
	 * Get namespace for GravityView REST API endpoints
	 *
	 * @since 2.0
	 * @return string
	 */
	public static function get_namespace() {
		return 'gravityview/v1';
	}

	/**
	 * Get root URL for GravityView REST API
	 *
	 * @since 2.0
	 * @return string
	 */
	public static function get_url() {
		return rest_url( self::get_namespace() );
	}
}
