<?php

namespace GravityKit\GravityView\Foundation\WP;

use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;
use GravityKit\GravityView\Foundation\Helpers\Arr;
use Exception;
use Throwable;

class AjaxRouter {
	const WP_AJAX_ACTION = 'gk_foundation_do_ajax';

	const AJAX_ROUTER = 'core';

	/**
	 * Class instance.
	 *
	 * @since 1.0.11
	 *
	 * @var AjaxRouter|null;
	 */
	private static $_instance = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.11
	 */
	private function __construct() {
		// Process Foundation AJAX on early admin_init to run before other plugins'
		// admin_init callbacks that may redirect and exit (e.g., activation welcome pages).
		if ( wp_doing_ajax() && ( $_REQUEST['action'] ?? '' ) === self::WP_AJAX_ACTION ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_action( 'admin_init', [ $this, 'process_ajax_request' ], PHP_INT_MIN );
		}

		add_action( 'wp_ajax_' . self::WP_AJAX_ACTION, [ $this, 'process_ajax_request' ] );
	}

	/**
	 * Returns class instance.
	 *
	 * @since 1.0.11
	 *
	 * @return AjaxRouter
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Returns Ajax request defaults.
	 *
	 * @since 1.0.0
	 *
	 * @param string $router Ajax router that will be handling the request.
	 *
	 * @return array
	 */
	public static function get_ajax_params( $router ) {
		$router = $router ?: self::AJAX_ROUTER;

		$params = [
			'_wpNonce'      => wp_create_nonce( self::WP_AJAX_ACTION ),
			'_wpRestUrl'    => get_rest_url(),
			'_wpRestNonce'  => wp_create_nonce( 'wp_rest' ),
			'_wpAjaxUrl'    => admin_url( 'admin-ajax.php' ),
			'_wpAjaxAction' => self::WP_AJAX_ACTION,
			'ajaxRouter'    => $router,
		];

		return apply_filters( "gk/foundation/ajax/{$router}/params", $params, $router );
	}

	/**
	 * Processes Ajax request and routes it to the appropriate endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function process_ajax_request() {
		$request = wp_parse_args(
			$_POST, // phpcs:ignore WordPress.Security.NonceVerification.Missing
			[
				'nonce'      => null,
				'payload'    => [],
				'ajaxRouter' => null,
				'ajaxRoute'  => null,
			]
		);

		list ( $nonce, $payload, $router, $route ) = array_values( $request );

		if ( ! is_array( $payload ) ) {
			$payload = json_decode( stripslashes_deep( $payload ), true ) ?? [];
		}

		$is_valid_nonce = wp_verify_nonce( $nonce, self::WP_AJAX_ACTION );

		if ( ! wp_doing_ajax() || ! $is_valid_nonce ) {
			wp_die( '', '', [ 'response' => 403 ] );
		}

		/**
		 * Modifies a list of Ajax routes that map to backend functions/class methods. $router groups routes to avoid a name collision (e.g., 'settings', 'licenses').
		 *
		 * @filter gk/foundation/ajax/{$router}/routes
		 *
		 * @since  1.0.0
		 *
		 * @param array[] $routes Ajax route to function/class method map.
		 */
		$ajax_route_to_class_method_map = apply_filters( "gk/foundation/ajax/{$router}/routes", [] );

		$route_callback = Arr::get( $ajax_route_to_class_method_map, $route );

		if ( ! CoreHelpers::is_callable_function( $route_callback ) && ! CoreHelpers::is_callable_class_method( $route_callback ) ) {
			wp_die( '', '', [ 'response' => 404 ] );
		}

		try {
			/**
			 * Fires before the Ajax call is processed.
			 *
			 * @action gk/foundation/ajax/before
			 *
			 * @since  1.0.11
			 *
			 * @param string $router
			 * @param string $route
			 * @param array  $payload
			 */
			do_action( 'gk/foundation/ajax/before', $router, $route, $payload );

			$result = call_user_func( $route_callback, $payload );
		} catch ( Throwable $e ) {
			// Widen to Throwable so PHP 7+ Error subclasses (TypeError, etc.) are caught
			// and routed through the same failure path as Exceptions, rather than bubbling
			// uncaught and bypassing the /result filter and /after guard below.
			$result = new Exception( $e->getMessage() );
		}

		/**
		 * Modifies Ajax call result. Listeners can transform the response, including
		 * converting a success to an error or vice versa. Receives the raw Exception or
		 * WP_Error when the route failed.
		 *
		 * @filter gk/foundation/ajax/result
		 *
		 * @since  1.0.11
		 *
		 * @param mixed|Exception|\WP_Error $result
		 * @param string                    $router
		 * @param string                    $route
		 * @param array                     $payload
		 */
		$result = apply_filters( 'gk/foundation/ajax/result', $result, $router, $route, $payload );

		// Skip the /after hook when the route threw or returned an error. Listeners cannot safely
		// act on a failed result, and a blanket fire-on-failure invites bugs in listeners that
		// forget to check $result (a prior sleep() listener allowed an authenticated DoS).
		if ( ! ( $result instanceof Exception ) && ! is_wp_error( $result ) ) {
			/**
			 * Fires after the Ajax call is processed successfully. Does not fire when the route
			 * throws or the /result filter transforms the response into an Exception or WP_Error.
			 *
			 * @action gk/foundation/ajax/after
			 *
			 * @since  1.0.11
			 * @since  1.15.0 Only fires on successful results. Listeners no longer need to defend
			 *             against Exception/WP_Error being passed as $result.
			 *
			 * @param string $router
			 * @param string $route
			 * @param array  $payload
			 * @param mixed  $result
			 */
			do_action( 'gk/foundation/ajax/after', $router, $route, $payload, $result );
		}

		CoreHelpers::process_return( $result );
	}
}
