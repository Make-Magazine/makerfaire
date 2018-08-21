<?php

/*
 * As of ACF 5.60, they default removed the custom fields option on pages. This adds it back
 */

function mf_acf_init() {
	acf_update_setting('remove_wp_meta_box', false);
}

add_action('acf/init', 'mf_acf_init');

add_filter('_wp_post_revision_fields', 'add_field_debug_preview');
function add_field_debug_preview($fields){
   $fields["debug_preview"] = "debug_preview";
   return $fields;
}

add_action( 'edit_form_after_title', 'add_input_debug_preview' );
function add_input_debug_preview() {
   echo '<input type="hidden" name="debug_preview" value="debug_preview">';
}