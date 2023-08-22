<?php
include '../../../../wp-load.php'; 
$args = array('show_date'=>'modified', 'title_li'     => __( 'Pages & Last Updated Date' ), 'sort_column'=> 'post_parent');
if(isset($_GET['child_of'])){
    $args['child_of'] = $_GET['child_of'];
}
//Default 'publish'
if(isset($_GET['post_status'])){
    $args['post_status'] = $_GET['post_status'];
}

$pages = wp_list_pages($args);

?>