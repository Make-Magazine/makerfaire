<?php
/**
 * Beaver Builder frontend render template.
 *
 * @package GravityKit\GravityView\PageBuilder\BeaverBuilder
 * @since 3.0.0
 */

if ( ! isset( $module ) || ! is_object( $module ) ) {
	return;
}

if ( method_exists( $module, 'frontend' ) ) {
	$module->frontend();
}
