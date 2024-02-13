<?php
include '../../../../wp-load.php'; 

$count=0;
$status = (isset($_GET['status'])?$_GET['status']:'publish');
$statArray = explode(',',trim($status));

$show   = (isset($_GET['show'])?$_GET['show']:'');

$sql = 'SELECT ID, post_date, post_title, post_status,post_password, post_parent,post_type, post_modified, '.
       ' (select meta_value from wp_postmeta where meta_key="_wp_page_template" and post_id = wp_posts.id limit 1) as page_template '.
        'FROM `wp_posts` '.
        'where post_type="page" and post_status in('."'" . implode("','", $statArray) . "'".') '.
        'order by post_parent ASC, post_title ASC;';
    //echo $sql;    
$results = $wpdb->get_results($sql, ARRAY_A);

echo '<h2>List of pages with a status of '.$status.'</h2> <h3>Found '.count($results).' records</h3>';
echo '<i>To include page templates, click <a href="/wp-content/themes/makerfaire/devScripts/listPages.php?show=template&status='.$status.'">here</a></i><br/>';
echo '<i>To show both Private and Published pages, click <a href="/wp-content/themes/makerfaire/devScripts/listPages.php?status=private,publish">here</a></i><br/>';
$post_array=array();
foreach($results as $row) {
    $post_array[$row['ID']]=$row;
    $parent_id = $row['post_parent'];
     
    if(!isset($post_array[$parent_id] ) ){
        $post_array[$parent_id] = array();
    }
    //set as a child row of the parent//set post data                
    $post_array[ $parent_id ]['child'][]=$row['ID'];
        
}

echo '<ul>';            
foreach($post_array[0]['child'] as $page_ID){
    list_pages($page_ID);    
}
echo '</ul>';    


//now find orphaned pages
unset($post_array['0']); 
echo '<h2>Orphaned Pages</h2><ul>';
foreach($post_array as $key=>$page){    
    if(!isset($page['ID']) && isset($page['child'])){
        foreach($page['child'] as $page_ID){            
            list_pages($page_ID);    
        }
    }
    
}
echo '</ul>';

echo 'Wrote '. $count.' records';

//var_dump($post_array);
function list_pages($page_ID){    
    global $count; global $show; global $post_array;
    ++$count;
        
    $page = $post_array[$page_ID];
    
    echo '<li>';
        echo    '<strong>'.
                '<a href="'.get_page_link($page['ID']).'">' . ($page['post_title']!==''?esc_html($page['post_title']):'{{No Title Set}}') . '</a>'.
                '</strong>'.
                '('.$page['post_status'].')'.
                'Created: ' . $page['post_date'] .' Last Updated: '.$page['post_modified'] .                
                (isset($page['post_password']) && $page['post_password']!=''?' (Password='.$page['post_password'].')':'');
        echo ($show=='template'? '<br/>Page Template: '.$page['page_template']:'');
        if(isset($page['child'])){
            echo '<ul>';
            
            foreach($page['child'] as $child_ID){
                list_pages($child_ID);
            }
            echo '</ul>';
        }        
    echo '</li>';
}   
