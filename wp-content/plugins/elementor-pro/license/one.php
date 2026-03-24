<?php
namespace ElementorPro\License;

use ElementorOne\Connect\Facade;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class One {

	/**
	 * Check if Elementor One is connected.
	 *
	 * @return bool
	 */
	public static function is_connected(): bool {
		if ( ! class_exists( Facade::class ) ) {
			return false;
		}

		$facade = Facade::get( 'elementor-pro' );

		return $facade ? $facade->utils()->is_connected() : false;
	}

	/**
	 * Get the subscription display name for Elementor One.
	 *
	 * @return string
	 */
	public static function get_subscription_display_name(): string {
		return 'Elementor One';
	}

	/**
	 * Get the manage subscription URL when One is connected.
	 *
	 * @return string
	 */
	public static function get_manage_subscription_url(): string {
		return admin_url( 'admin.php?page=elementor-home#/home/tool-manager' );
	}
}

