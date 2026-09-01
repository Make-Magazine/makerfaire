<?php
/**
 * @package GravityView
 * @subpackage Gravityview/admin/metaboxes/views
 * @global $post
 */

global $post;

$current_settings = gravityview_get_template_settings( $post->ID );

?>

<table role="presentation" class="form-table striped">
<?php

	/**
	 * Render Delete Entry metabox settings, if enabled.
	 *
	 * @see GravityView_Delete_Entry_Admin::view_settings_metabox
	 *
	 * @since 2.9.2
	 *
	 * @param array $current_settings The View settings.
	 */
	do_action( 'gravityview/metaboxes/delete_entry', $current_settings );

?>
</table>
