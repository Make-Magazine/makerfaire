<?php
function essb_add_loves_column() {
    add_action ( "manage_posts_custom_column", 'essb_generate_total_loves_column');
    add_filter ( "manage_posts_columns", 'essb_register_total_loves_column');
    add_action ( "manage_pages_custom_column", 'essb_generate_total_loves_column');
    add_filter ( "manage_pages_columns", 'essb_register_total_loves_column');
    add_filter ( 'manage_edit-post_sortable_columns', 'essb_sort_total_loves_column');
    add_filter ( 'manage_edit-page_sortable_columns', 'essb_sort_total_loves_column');
    add_action ( 'pre_get_posts', 'essb_sort_totalloves');
}

function essb_sort_totalloves($query) {
    if (! is_admin ()) {
        return;
    }
    
    $orderby = $query->get ( 'orderby' );
    if ('_essb_love' == $orderby) {
        $query->set ( 'meta_key', '_essb_love' );
        $query->set ( 'orderby', 'meta_value_num' );
    }
}

function essb_sort_total_loves_column($defaults) {
    $defaults['essb_loves'] = '_essb_love';
    
    return $defaults;
}

function essb_register_total_loves_column($defaults) {
    $defaults['essb_loves'] = esc_html__('Loves', 'essb');
    
    return $defaults;
}

function essb_generate_total_loves_column($column_name) {
    if ($column_name == 'essb_loves') {
        echo intval ( get_post_meta ( get_the_ID(), '_essb_love', true ) );
    }
}
