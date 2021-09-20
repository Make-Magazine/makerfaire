<?php
/**
 * The default template for displaying post meta.
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get meta sections
$sections = oceanwp_blog_entry_meta();

// Return if sections are empty
if ( empty( $sections ) ) {
	return;
}
