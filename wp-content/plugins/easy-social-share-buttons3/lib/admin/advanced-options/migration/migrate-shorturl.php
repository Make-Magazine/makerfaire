<?php

/**
 * Move current short URL cache to the new post meta table
 */
function essb_data_migrate_previous_shorturl() {
    global $wpdb;
    
    if (class_exists('ESSB_Post_Meta')) {        
        $mid = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE %s", 'essb_shorturl_%') );
        
        if (!empty($mid)) {
            foreach ($mid as $record) {
                essb_update_post_meta($record->post_id, $record->meta_key, $record->meta_value);
            }
         }
    }
    
}

/**
 * Clear short URLs stored inside the post meta table
 */
function essb_data_migrate_previous_shorturl_clear() {
    delete_post_meta_by_key('essb_shorturl_googl');
    delete_post_meta_by_key('essb_shorturl_post');
    delete_post_meta_by_key('essb_shorturl_bitly');
    delete_post_meta_by_key('essb_shorturl_ssu');
    delete_post_meta_by_key('essb_shorturl_rebrand');
    delete_post_meta_by_key('essb_shorturl_pus');
}