<?php
/**
 * Notice callback wrapper functions.
 *
 * Plain function wrappers avoid namespace/backslash serialization issues
 * when storing callable strings in the database for Foundation Notices.
 *
 * @since 2.9.0
 *
 * @package GravityKit\GravityImport\Notices
 */

use GravityKit\GravityImport\Notices\ImportNotices;

/**
 * Condition callback to check if import live notice should be displayed.
 *
 * @since 2.9.0
 *
 * @param object $notice The notice object being evaluated.
 *
 * @return bool True if notice should be displayed.
 */
function gk_gravityimport_should_display_notice( $notice ) {
	return ImportNotices::should_display( $notice );
}

/**
 * Live notice callback for import progress.
 *
 * @since 2.9.0
 *
 * @param array $notice The notice context.
 *
 * @return array Updated notice data with message and progress.
 */
function gk_gravityimport_live_callback( $notice ) {
	return ImportNotices::live_callback( $notice );
}
