<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 26-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Licenses\WP;

use WP_Error;
use WP_Upgrader_Skin;
use Exception;

/**
 * This is class is used to catch errors and suppress output during product installation/update.
 *
 * @since 1.0.0
 *
 * @see   WP_Upgrader_Skin
 */
class WPUpgraderSkin extends WP_Upgrader_Skin {
	/**
	 * Silences header display.
	 *
	 * @inheritDoc
	 *
	 * @since      1.0.0
	 *
	 * @return void
	 */
	public function header() {
	}

	/**
	 * Silences footer display.
	 *
	 * @inheritDoc
	 *
	 * @since      1.0.0
	 *
	 * @return void
	 */
	public function footer() {
	}

	/**
	 * Silences results.
	 *
	 * @inheritDoc
	 *
	 * @since      1.0.0
	 *
	 * @param string $feedback Message data.
	 * @param mixed  ...$args  Optional text replacements.
	 *
	 * @return void
	 */
	public function feedback( $feedback, ...$args ) {
	}

	/**
	 * Throws an error when one (or multiple) is encountered.
	 *
	 * @since 1.0.0
	 *
	 * @param string|WP_Error $errors Errors.
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function error( $errors ) {
		if ( is_wp_error( $errors ) ) {
			// One error is enough to get a sense of why the installation failed.
			$output = $errors->get_error_messages()[0] ?? esc_html__( 'Unknown WordPress error', 'gk-gravityview' );
		} else {
			$output = $errors;
		}

		throw new Exception( $output );
	}
}
