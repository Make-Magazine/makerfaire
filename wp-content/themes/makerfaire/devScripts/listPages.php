<?php
include '../../../../wp-load.php'; 


$expand=(isset($_GET['expand'])?TRUE:FALSE);


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

$pages = get_pages( $parsed_args );

$page_array=array();
$parent_array=array();
foreach($pages as $page){
    $page_data = array(
        'ID' => $page->ID,
        'post_author' => $page->post_author,
        'post_date' => $page->post_date,
        'post_title' => $page->post_title,
        'post_status' => $page->post_status,
        'post_modified' => $page->post_modified,
        'post_parent' => $page->post_parent
    );

    if($page->post_parent !=0){
        if(!isset($page_array[$page->post_parent])){
            $parentData = get_post($page->post_parent);
            $page_array[$page->post_parent] = array(
                'ID' => $parentData->ID,
                'post_author' => $parentData->post_author,
                'post_date' => $parentData->post_date,
                'post_title' => $parentData->post_title,
                'post_status' => $parentData->post_status,
                'post_modified' => $parentData->post_modified,
                'post_parent' => $parentData->post_parent
            );
        }
        $page_array[$page->post_parent]['children'][] = $page_data;
    }else{
        if(!isset($page_array[$page->ID]))  {
            $page_array[$page->ID]=array();
        }
        $page_array[$page->ID] = $page_data;
    }            
    
}

//var_dump($page_array);
echo '<ul>';
foreach($page_array as $page){
    echo '<li>';
    $link = get_page_link( $page['ID'] );
    echo '<a href="'.$link.'">'.$page["post_title"].'</a> ('.$page["post_status"]. ')';
    if($expand)
        echo ' Created->'.$page["post_date"].' Modified->'.$page["post_modified"]. ' Author ->'.get_the_author_meta('display_name', $page["post_author"]);
    
    //list child data
    if(isset($page['children'])){
        echo '<ul>';
        foreach($page['children'] as $child){
            $childLink = get_page_link( $child['ID'] );
            echo '<li>';
            echo '<a href="'.$childLink.'">'.$child["post_title"].'</a> ('.$child["post_status"]. ')';
            if($expand)
                echo ' Created->'.$child["post_date"].' Modified->'.$child["post_modified"]. ' Author ->'.get_the_author_meta('display_name', $child["post_author"]);
            echo '</li>';
        }
        echo '</ul>';
    }

    echo '</li>';
    
    
}
echo '</ul>';