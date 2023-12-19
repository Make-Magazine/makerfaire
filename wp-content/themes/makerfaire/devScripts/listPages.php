<?php
include '../../../../wp-load.php'; 

$parsed_args = array(
    'depth'        => 0,
    'show_date'    => 'modified',
    'date_format'  => get_option( 'date_format' ),
    'child_of'     => 0,
    'exclude'      => '',
    'title_li'     => __( 'Pages' ),
    'echo'         => 1,
    'authors'      => '',
    'sort_column'  => 'menu_order, post_title',
    'link_before'  => '',
    'link_after'   => '',
    'item_spacing' => 'preserve',
    'walker'       => ''
);

if(isset($_GET['post_status'])){
    $parsed_args['post_status'] = $_GET['post_status'];
}

list_pages_with_status_and_hierarchy();

function list_pages_with_status_and_hierarchy($parent_id = 0, $level = 0) {
    $expand=(isset($_GET['expand'])?TRUE:FALSE);

    // Get all pages with the specified parent ID
    $args = array(
        'post_type'      => 'page',
        'posts_per_page' => -1,
        'post_parent'    => $parent_id,
        'order'          => 'ASC',
        'orderby'        => 'menu_order'
    );

    $pages = get_posts($args);

    // Check if there are any pages
    if ($pages) {
        echo '<ul>';

        // Loop through each page
        foreach ($pages as $page) {
            $status = get_post_status($page->ID);
            $link   = get_page_link($page->ID);            
            $modified_date = get_post_field( 'post_modified', $page->ID );
            
            echo '<li>';
            echo '<strong><a href="'.$link.'">' . esc_html($page->post_title) . '</a></strong> (' . esc_html($status).') '.$modified_date;
            if($expand){
                echo ' Created-> '. get_post_field( 'post_date', $page->ID );                
                echo ' Author-> '. get_the_author_meta('display_name', $page->post_author);
            }
            echo '</li>';

            // Recursively call the function for child pages
            list_pages_with_status_and_hierarchy($page->ID, $level + 1);
        }

        echo '</ul>';
    }
}
