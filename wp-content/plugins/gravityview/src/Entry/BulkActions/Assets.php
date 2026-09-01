<?php
/**
 * Frontend bulk action assets.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\Utils\Assets as AssetUtils;
use GravityKit\GravityView\View\View;

/**
 * Registers and enqueues frontend bulk action assets.
 *
 * @since 3.0.0
 */
final class Assets {
	const HANDLE = 'gravityview-bulk-actions';

	/**
	 * @since 3.0.0
	 * @var ViewEligibility
	 */
	private static $eligibility;

	/**
	 * @since 3.0.0
	 * @var Registry
	 */
	private static $registry;

	/**
	 * Registers the frontend bulk actions script.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function register() {
		$path = 'js/bulk-actions' . AssetUtils::min() . '.js';

		wp_register_script(
			self::HANDLE,
			AssetUtils::url( $path ),
			[ 'gravityview-fe-view', 'wp-hooks' ],
			filemtime( AssetUtils::path( $path ) ),
			true
		);
	}

	/**
	 * Enqueues the frontend bulk actions script if enabled for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return void
	 */
	public static function enqueue_for_view( View $view ) {
		if ( ! self::eligibility()->is_enabled_for_view( $view ) ) {
			return;
		}

		if ( empty( self::registry()->get_available_actions( $view ) ) ) {
			return;
		}

		wp_enqueue_script( self::HANDLE );
	}

	/**
	 * Returns the shared eligibility checker.
	 *
	 * @since 3.0.0
	 *
	 * @return ViewEligibility
	 */
	private static function eligibility() {
		if ( ! self::$eligibility ) {
			self::$eligibility = new ViewEligibility( new Registry() );
		}

		return self::$eligibility;
	}

	/**
	 * Returns the shared action registry.
	 *
	 * @since 3.0.0
	 *
	 * @return Registry
	 */
	private static function registry() {
		if ( ! self::$registry ) {
			self::$registry = new Registry();
		}

		return self::$registry;
	}
}
