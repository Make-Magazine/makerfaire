<?php
/**
 * Legacy shim for GVCommon.
 *
 * @package GravityView
 * @license GPL2+
 * @since 1.5.2
 * @since 3.0.0 Migrated to PSR-4 as GravityKit\GravityView\Legacy\Utility\Common.
 * @deprecated 3.0.0 Use GravityKit\GravityView\Legacy\Utility\Common instead.
 */

/** If this file is called directly, abort. */
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Wrap in conditional to prevent compile-time redeclaration conflict with PSR-4 class aliases.
if ( ! class_exists( 'GVCommon', false ) ) {
	class GVCommon extends \GravityKit\GravityView\Legacy\Utility\Common {
	}
}

