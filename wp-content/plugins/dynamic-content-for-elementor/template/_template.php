<?php
/**
 * Template Dynamic Content
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$get_id = apply_filters( 'wpml_object_id', $dce_default_template, 'elementor_library', true );
if ( isset( $inlinecss ) && $inlinecss ) {
	$inlcss = $inlinecss;
} else {
	$inlcss = false;
}
// Check if the template is created with Elementor
$elementor = get_post_meta( $get_id, '_elementor_edit_mode', true );
$template_page = '';
// If Elementor
if ( class_exists( 'Elementor\Plugin' ) && $elementor ) {
	$template_page = Elementor\Plugin::instance()->frontend->get_builder_content( $get_id, $inlcss );
} else {
	$post_n = get_post( $get_id );
	$content_n = apply_filters( 'the_content', $post_n->post_content );
	echo $content_n;

}
