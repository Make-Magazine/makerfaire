<?php
// safe check for visibility condition
ob_start(); // prevent other plugins output
/** Loads the WordPress Environment and Template */
define( 'WP_USE_THEMES', false );
if ( is_file( '../../../../wp-blog-header.php' ) ) {
	require '../../../../wp-blog-header.php';
} elseif ( is_file( '../../../../wp-load.php' ) ) {
	require '../../../../wp-load.php';
	wp();
}
$header = ob_get_clean();
$element_id = empty( $_GET['eid'] ) ? 0 : sanitize_text_field( $_GET['eid'] );
$favorite_post_id = empty( $_GET['dce_post_id'] ) ? 0 : intval( $_GET['dce_post_id'] );
$list_key = empty( $_GET['dce_list'] ) ? 0 : sanitize_text_field( $_GET['dce_list'] );

if ( $element_id && $favorite_post_id && $list_key ) {

	status_header( 200 );
	global $wp_query;
	$wp_query->is_singular = true;
	$wp_query->is_page = $wp_query->is_singular;
	$wp_query->is_404  = false;

	$element = \DynamicContentForElementor\Helper::get_elementor_element_by_id( $element_id );
	if ( $element ) {
		$element->update_list( $element_id, $favorite_post_id, $list_key );

		$settings = $element->get_settings_for_display();
		$favorite = $element->get_favorite_value( $list_key, $settings['dce_favorite_scope'] );
		echo implode( ', ', $favorite );
	}
}
?><br>Add to Favorites
