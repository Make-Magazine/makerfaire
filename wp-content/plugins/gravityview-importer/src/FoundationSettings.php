<?php

namespace GravityKit\GravityImport;

use GravityKitFoundation;
use Throwable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers GravityImport settings under Foundation.
 *
 * @since 2.11.0
 */
class FoundationSettings {
	const PLUGIN_ID = 'gravityimport';

	const SETTING_BACKGROUND_PROCESSING = 'background-processing';

	/**
	 * Hooks into Foundation's settings system.
	 *
	 * @since 2.11.0
	 *
	 * @return void
	 */
	public static function bootstrap() {
		add_filter( 'gk/foundation/settings/data/plugins', array( __CLASS__, 'include_settings' ) );
	}

	/**
	 * Adds GravityImport settings to the Foundation settings page.
	 *
	 * @since 2.11.0
	 *
	 * @param array $plugins_settings Existing plugins' settings.
	 *
	 * @return array Updated plugins' settings.
	 */
	public static function include_settings( $plugins_settings ) {
		$defaults = array(
			self::SETTING_BACKGROUND_PROCESSING => true,
		);

		$current = class_exists( 'GravityKitFoundation' ) && GravityKitFoundation::settings()
			? GravityKitFoundation::settings()->get_plugin_settings( self::PLUGIN_ID )
			: array();

		$values = wp_parse_args( is_array( $current ) ? $current : array(), $defaults );

		$site_health_link = sprintf(
			'<a href="%s" class="gk-link">%s</a>',
			esc_url( admin_url( 'site-health.php?tab=debug' ) ),
			esc_html__( 'Site Health page', 'gk-gravityimport' )
		);

		$description = sprintf(
			/* translators: %s: HTML link to the Site Health page. */
			esc_html__( 'When enabled, imports run in the background so you can close the browser tab and return later. Requires loopback HTTP requests to be working on this site — see the "GravityKit Background Processing" section on the %s to confirm. Disable to force synchronous processing in the browser.', 'gk-gravityimport' ),
			$site_health_link
		);

		$settings = array(
			'id'       => self::PLUGIN_ID,
			'title'    => 'GravityImport',
			'defaults' => $defaults,
			'icon'     => 'data:image/svg+xml;base64,PHN2ZyBmaWxsPSJub25lIiBoZWlnaHQ9IjgwIiB2aWV3Qm94PSIwIDAgODAgODAiIHdpZHRoPSI4MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGNsaXBQYXRoIGlkPSJhIj48cGF0aCBkPSJtMTYgMTloNDh2NDJoLTQ4eiIvPjwvY2xpcFBhdGg+PHJlY3QgZmlsbD0iIzAwZWE5NyIgaGVpZ2h0PSI4MCIgcng9IjgiIHdpZHRoPSI4MCIvPjxnIGNsaXAtcGF0aD0idXJsKCNhKSI+PHBhdGggY2xpcC1ydWxlPSJldmVub2RkIiBkPSJtNDEuMDc2MiA1MS4xNzQ5Yy0uMzc0OS4zNzI5LS45ODI4LjM3MjktMS4zNTc2LjAwMDFsLTEuMzU3NC0xLjM0OTdjLS4zNzUtLjM3MjgtLjM3NS0uOTc3MSAwLTEuMzQ5OGw2LjY0NTItNi41NjY1aC0yNy45OTg0Yy0uNTc3NyAwLS45NTItLjQyNzMtLjk1Mi0uOTU0NXYtMS45MDljMC0uNTI3My4zNzQzLS45NTQ2Ljk1Mi0uOTU0Nmg0Ljg0OTRjLjk3MTMtMTAuNzAyIDEwLjAwNjYtMTkuMDkwOSAyMS4wMjI0LTE5LjA5MDkgMTEuNjYzOSAwIDIxLjExOTMgOS40MDIgMjEuMTE5MyAyMXMtOS40NTU0IDIwLjk5OTktMjEuMTE5MyAyMC45OTk5Yy05LjMyMiAwLTE3LjIyMjctNi4wMDg2LTIwLjAyMTMtMTQuMzQxNi0uMTUzOS0uNDU4LjIwNS0uOTMxLjY5MDgtLjkzMWgyLjIzOThjLjQ5NDkgMCAuOTI1OS4zMDc3IDEuMTEzMS43NjMyIDIuNTc0OCA2LjI2NzYgOC43NDc3IDEwLjY5MTIgMTUuOTc3NiAxMC42OTEyIDkuNTQzMSAwIDE3LjI3OTQtNy42OTI1IDE3LjI3OTQtMTcuMTgxNyAwLTkuNDg5My03LjczNjMtMTcuMTgxOC0xNy4yNzk0LTE3LjE4MTgtOC44OTM2IDAtMTYuMjEwOSA2LjY5MjktMTcuMTY2OSAxNS4yNzI3aDE5LjI5NTlsLTYuNjQ3Ni02LjU2NjVjLS4zNzUtLjM3MjctLjM3NS0uOTc3MSAwLTEuMzQ5OWwxLjM1NzQtMS4zNDk2Yy4zNzQ5LS4zNzI4Ljk4MjctLjM3MjggMS4zNTc2IDBsOS44ODEgOS44NDQzYy43NDk4LjcyNjIuNzQ5OCAxLjkzNDkuMDAwMSAyLjY4MDV6IiBmaWxsPSIjZmZmIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiLz48L2c+PC9zdmc+',
			'sections' => array(
				array(
					'title'    => esc_html__( 'General', 'gk-gravityimport' ),
					'settings' => array(
						array(
							'id'          => self::SETTING_BACKGROUND_PROCESSING,
							'type'        => 'checkbox',
							'value'       => (bool) $values[ self::SETTING_BACKGROUND_PROCESSING ],
							'title'       => esc_html__( 'Enable Background Processing', 'gk-gravityimport' ),
							'description' => $description,
							'unsafe'      => true,
						),
					),
				),
			),
		);

		return array_merge( $plugins_settings, array( self::PLUGIN_ID => $settings ) );
	}

	/**
	 * Returns the current value of a GravityImport setting.
	 *
	 * @since 2.11.0
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Default value when the setting is unavailable.
	 *
	 * @return mixed Setting value or the default.
	 */
	public static function get( $key, $default = null ) {
		if ( ! class_exists( 'GravityKitFoundation' ) ) {
			return $default;
		}

		try {
			$settings = GravityKitFoundation::settings();

			if ( ! $settings ) {
				return $default;
			}

			return $settings->get_plugin_setting( self::PLUGIN_ID, $key, $default );
		} catch ( Throwable $e ) {
			return $default;
		}
	}
}
