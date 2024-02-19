<?php
function mpfy_comments_template( $template ) {
	return MAPIFY_PLUGIN_DIR . '/templates/comments.php';
}

function mpfy_comments_redirect_field() {
	echo '<input type="hidden" name="mpfy_comment_redirect" value="' . esc_attr( MPFY_COMMENT_REDIRECT_URL ) . '" />';
}

function mpfy_redirect_after_comments( $location ) {
	if ( isset( $_POST['mpfy_comment_redirect'] ) ) {
		return sanitize_text_field( $_POST['mpfy_comment_redirect'] );
	}

	return $location;
}
add_filter( 'comment_post_redirect', 'mpfy_redirect_after_comments' );
