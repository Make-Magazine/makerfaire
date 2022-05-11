<?php

namespace InstagramFeed\Helpers;

/**
 * @since 6.0.5
 */
class Util {
	/**
	 * Returns the enabled debugging flag state.
	 *
	 * @return bool
	 */
	public static function isDebugging() {
		return ( defined( 'SBI_DEBUG' ) && SBI_DEBUG === true ) || isset( $_GET['sbi_debug'] ) || isset( $_GET['sb_debug'] );
	}
  
  public static function isIFPage() {
		return get_current_screen() !== null && ! empty( $_GET['page'] ) && strpos( $_GET['page'], 'sbi-' ) !== false;
	}
}