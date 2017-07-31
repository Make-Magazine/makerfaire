<?php

/*
 * As of ACF 5.60, they default removed the custom fields option on pages. This adds it back
 */

function mf_acf_init() {
	acf_update_setting('remove_wp_meta_box', false);
}

add_action('acf/init', 'mf_acf_init');