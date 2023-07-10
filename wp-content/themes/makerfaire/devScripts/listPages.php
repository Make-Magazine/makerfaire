<?php
include '../../../../wp-load.php'; 
wp_list_pages(array('show_date'=>'modified', 'title_li'     => __( 'Pages & Last Updated Date' )));
?>