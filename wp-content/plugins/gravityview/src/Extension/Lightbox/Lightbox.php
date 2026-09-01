<?php
/**
 * Lightbox manager class.
 *
 * PSR-4 migration of the legacy GravityView_Lightbox class.
 *
 * @package GravityKit\GravityView\Extension\Lightbox
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Extension\Lightbox;

use function gravityview;

/**
 * Manage lightbox scripts for GravityView
 *
 * @internal
 */
class Lightbox {
	const DEFAULT_PROVIDER = 'fancybox';

	/**
	 * The registered lightbox providers
	 *
	 * @var \GravityView_Lightbox_Provider[]
	 */
	private static $providers = [];

	/**
	 * The active lightbox provider
	 *
	 * @var \GravityView_Lightbox_Provider|null
	 */
	private static $active_provider = null;

	/**
	 * Whether hooks have been added.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private static $hooks_added = false;

	/**
	 * GravityView_Lightbox_Provider constructor.
	 */
	public function __construct() {
		if ( self::$hooks_added ) {
			return;
		}

		add_action( 'plugins_loaded', [ $this, 'set_provider' ], 11 );

		add_action( 'gravityview/lightbox/provider', [ $this, 'set_provider' ] );

		self::$hooks_added = true;
	}

	/**
	 * Activate the lightbox provider chosen in settings
	 *
	 * @param string|null $provider GravityView_Lightbox_Provider::$slug of provider
	 *
	 * @internal
	 */
	public function set_provider( $provider = null ) {

		if ( gravityview()->request->is_admin() ) {
			return;
		}

		if ( empty( $provider ) ) {
			$provider = gravityview()->plugin->settings->get( 'lightbox', self::DEFAULT_PROVIDER );
		}

		if ( empty( self::$providers[ $provider ] ) || ! class_exists( self::$providers[ $provider ] ) ) {
			gravityview()->log->error( 'Lightbox provider {provider} not registered.', [ 'provider' => $provider ] );
			return;
		}

		// Already set up.
		if ( self::$active_provider && self::$active_provider instanceof self::$providers[ $provider ] ) {
			return;
		}

		// We're switching providers; remove the hooks that were added.
		if ( self::$active_provider ) {
			self::$active_provider->remove_hooks();
		}

		self::$active_provider = new self::$providers[ $provider ]();

		self::$active_provider->add_hooks();
	}

	/**
	 * Register lightbox providers
	 *
	 * @param $provider
	 */
	public static function register( $provider ) {
		self::$providers[ $provider::$slug ] = $provider;
	}

	/**
	 * Returns the configured lightbox provider instance.
	 *
	 * @since 2.45
	 *
	 * @return \GravityView_Lightbox_Provider|null The active lightbox provider, or null if none is set.
	 */
	public static function get_provider() {
		$provider_slug = gravityview()->plugin->settings->get( 'lightbox', self::DEFAULT_PROVIDER );

		if ( isset( self::$providers[ $provider_slug ] ) ) {
			return new self::$providers[ $provider_slug ]();
		}

		return null;
	}
}
