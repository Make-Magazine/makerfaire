<?php
include '../../../../wp-load.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$create = (isset($_GET['create']) ? $_GET['create'] : '');
$blog_id = (isset($_GET['blogid']) ? $_GET['blogid'] : '');
$page = (isset($_GET['page']) ? $_GET['page'] : 1);
$limit = (isset($_GET['limit']) ? $_GET['limit'] : 100);
$offset = ($page != 1 ? (($page - 1) * $limit) : 0);

if($create==''){
    //display category info
    //output categories from wp_mf_dir_entry so we can translate
    $sql = "SELECT category,main_category FROM `wp_mf_dir_entry`";
    $results = $wpdb->get_results($sql, ARRAY_A);

    $categories=array();
    //loop thru all categories
    foreach ($results as $result) {        
        //loop through main categories
        $catArray = explode("|",$result['category']);
        foreach($catArray as $secondCat){
            $checkCat=trim($secondCat);
            if(isset($categories[$checkCat])){
                ++$categories[$checkCat];
            }else{
                $categories[$checkCat] = 1;
            }    
        }
    }
    ksort($categories);
    //var_dump($categories);
    foreach($categories as $outkey => $outCount){
        echo $outCount . ' - ' .$outkey.'<br/>';
    }
    die();
}

//ok let's build some project posts!

//acf data to set
/*
    title                   field_65971c3f37e2b
    exhibit_description     field_65971c7837e2d
    exhibit_video_link      field_65971cbd37e2f
    exhibit_inspiration     field_65971cd737e30
    exhibit_website         field_65971cf137e31
    exhibit_social repeater field_65971cff37e32
        social_url          field_65971d1137e33
    maker_data  repeater    field_65971d6537e38
        maker_email         field_65971d8137e39
        maker_or_group_name field_65971d8c37e3a
        maker_bio           field_65971d9c37e3b
        maker_photo         field_65971dc73a5c5 
        maker_website       field_65971dd33a5c6
        maker_social repeater   field_65971dde3a5c7
            maker_social_link   field_65971e1b3a5c8
    faire_information group     field_65971e463a5c9
        faire_year              field_65971d4337e35
        faire_post              field_65971e763a5ca
    additional_exhibit_images gallery   field_6597242088832
    project_location    group       field_65a17027e1bcd
        state                       field_65a1703ae1bce
        country                     field_65a17042e1bcf
        region                      field_65a72246d98f4
*/
$sql = "select entry.title, entry.public_desc, entry.project_video, entry.inspiration, entry.website, 
entry.social,
(select GROUP_CONCAT(email SEPARATOR '|') from wp_mf_dir_maker_to_entry m2e left outer join wp_mf_dir_maker maker on m2e.maker_id = maker.maker_id where m2e.maker_type <> 'contact' and m2e.entry_id=entry.entry_id and m2e.blog_id=entry.blog_id ) as maker_email, 
(select GROUP_CONCAT(concat(first_name,' ', last_name) SEPARATOR '|') from wp_mf_dir_maker_to_entry m2e left outer join wp_mf_dir_maker maker on m2e.maker_id = maker.maker_id where m2e.maker_type <> 'contact' and m2e.entry_id=entry.entry_id and m2e.blog_id=entry.blog_id ) as maker_name, 
(select GROUP_CONCAT(bio SEPARATOR '|') from wp_mf_dir_maker_to_entry m2e left outer join wp_mf_dir_maker maker on m2e.maker_id = maker.maker_id where m2e.maker_type <> 'contact' and m2e.entry_id=entry.entry_id and m2e.blog_id=entry.blog_id ) as maker_bio, 
(select GROUP_CONCAT(photo SEPARATOR '|') from wp_mf_dir_maker_to_entry m2e left outer join wp_mf_dir_maker maker on m2e.maker_id = maker.maker_id where m2e.maker_type <> 'contact' and m2e.entry_id=entry.entry_id and m2e.blog_id=entry.blog_id ) as maker_photo, 
(select GROUP_CONCAT(website SEPARATOR '|') from wp_mf_dir_maker_to_entry m2e left outer join wp_mf_dir_maker maker on m2e.maker_id = maker.maker_id where m2e.maker_type <> 'contact' and m2e.entry_id=entry.entry_id and m2e.blog_id=entry.blog_id ) as maker_website,
(select GROUP_CONCAT(social SEPARATOR '|') from wp_mf_dir_maker_to_entry m2e left outer join wp_mf_dir_maker maker on m2e.maker_id = maker.maker_id where m2e.maker_type <> 'contact' and m2e.entry_id=entry.entry_id and m2e.blog_id=entry.blog_id ) as maker_social, 
entry.faire_year, entry.faire_name as faire_post, entry.project_gallery,state, country, region,
entry.project_photo, entry.category, id, blog_id, entry_id, form_id

FROM `wp_mf_dir_entry` entry 
where status='Accepted' ".
($blog_id != ''?" and blog_id=".$blog_id." ":"").
"limit $limit offset $offset";
$entries = $wpdb->get_results($sql, ARRAY_A);
echo $sql.'<br/>';
$categories=array();
//loop thru all categories
foreach ($entries as $entry) {  
    $projects_post = array(
        'post_title' => $entry['title'],
        'post_content' => 'Maker Names (not publicly visible) '.$entry['maker_name'],
        'post_excerpt' =>$entry['public_desc'],
        'post_status' => 'publish',
        'post_author' => 12416,
        'post_type' => 'projects'        
        );
         
    $post_id = wp_insert_post( $projects_post );    
    if($post_id){        
        //featured image        
        $photo_url = $entry['project_photo'];

        //if a project photo isn't set, maybe you could use maker 1 photo?
        if($photo_url==''){                        
            $maker_photo    = explode('|', $entry['maker_photo']);            
            $photo_url      = (isset($maker_photo[0])?$maker_photo[0]:'');
        }

        //if the project photo is STILL empty we have an issue, skip this project
        if($photo_url==''){
            echo 'no project photo set for '.$entry['id'].'<br/>';
            //continue;
        }else{
            $thumbnail_id = get_img_id($photo_url, $post_id );
            if($thumbnail_id==0){
                echo 'error in adding '.$photo_url.' to media library for '.$entry['id'].'<br/>';
                //continue;            
            }
            set_post_thumbnail($post_id, $thumbnail_id);                        
        }                                                

        //set post categories
        $taxonomy = 'mf-project-cat';
        $categories = explode('|', $entry['category']);
        
        foreach($categories as $value){
            if($value=='')  continue;
            $term = term_exists( $value, $taxonomy );
            
            // If the term doesn't exist, then we create it
            if ( 0 === $term || null === $term ) {
                $term = wp_insert_term(
                    $value,
                    $taxonomy,
                    array(
                        'slug' => strtolower( str_ireplace( ' ', '-', $value ) )
                    )
                );

            }
            
            // Then we can set the taxonomy
            wp_set_post_terms( $post_id, $term, $taxonomy, true );
        }                

        //title
        update_field('field_65971c3f37e2b', $entry['title'], $post_id);
        //exhibit_description     
        update_field('field_65971c7837e2d', $entry['public_desc'], $post_id);
        //exhibit_video_link      
        update_field('field_65971cbd37e2f', $entry['project_video'], $post_id);
        //exhibit_inspiration     
        update_field('field_65971cd737e30', $entry['inspiration'], $post_id);
        //exhibit_website         
        update_field('field_65971cf137e31', $entry['website'], $post_id);

        //exhibit_social repeater field_65971cff37e32
        if($entry['social']!=''){
            $proj_social_arr = array();
            $project_social = unserialize($entry['social']);
            if(is_array($project_social)){
                foreach($project_social as $social){
                    $proj_social_arr[] = array('field_65971d1137e33'=>$social['Link']);
                }
            }else{
                echo 'error in unsocializing project social!! Dir Entry Id='.$entry['id'].' blog_id='.$entry['blog_id'].
                ' entry_id='.$entry['entry_id'].' form_id='.$entry['form_id'];
                die('abort');
            }
            update_field('field_65971cff37e32', $proj_social_arr, $post_id);
        }
            
        //maker email
        $maker_email    = explode('|', $entry['maker_email']);
        $maker_name     = explode('|', $entry['maker_name']);
        $maker_bio      = explode('|', $entry['maker_bio']);
        $maker_website  = explode('|', $entry['maker_website']);
        $maker_social   = explode('|', $entry['maker_social']);      
        $maker_photo    = explode('|', $entry['maker_photo']);

        $makerCount = count($maker_name);
    
        $maker_array = array();
        $key = 0;
        while ($key < $makerCount) {                
            //maker social
            $maker_social_array = array();        
            if(is_array($maker_social)){    
                if(isset($maker_social[$key]) && is_serialized($maker_social[$key])){
                    $msocial_data = unserialize($maker_social[$key]);

                    //is maker social serialized?
                    if($msocial_data){
                        foreach($maker_social_array as $msocial){
                            $maker_social_array[] = array(
                                'field_65971e1b3a5c8' => $msocial
                            );
                        }
                    }else{ //if $maker_social_array is false, data isn't serialized
                        $maker_social_array[] = array(
                            'field_65971e1b3a5c8' => $maker_social[$key]
                        );
                    }
                }                 
            }        
            //maker photo
            $thumbnail_id = get_img_id( $maker_photo[$key], $post_id);
        
            $maker_array[] = array(
                // field key => value pairs
                'field_65971d8137e39' => (isset($maker_email[$key])?$maker_email[$key]:''),
                'field_65971d8c37e3a' => (isset($maker_name[$key])?$maker_name[$key]:''),
                'field_65971d9c37e3b' => (isset($maker_bio[$key])?$maker_bio[$key]:''),
                'field_65971dd33a5c6' => (isset($maker_website[$key])?$maker_website[$key]:''),
                'field_65971dde3a5c7' => $maker_social_array,
                'field_65971dc73a5c5' => ($thumbnail_id!=0?$thumbnail_id:'')
            );
            $key++;
        }

        //set maker data
        update_field( 'field_65971d6537e38', $maker_array, $post_id );

            
        //Faire Information
        $faire_information = array(	
            'field_65971d4337e35'	=>	$entry['faire_year'],
            'field_65971e763a5ca'	=>	$entry['faire_post']	
        );

        //Update the field using this array as value:
        update_field( 'field_65971e463a5c9', $faire_information, $post_id );        
                        
        //additional_exhibit_images gallery   
        $project_gallery = explode(',', $entry['project_gallery']);      
        $project_gallery='';
        if($project_gallery !=''){
            $gallery_array = array();
            if(is_array($project_gallery)){
                foreach($project_gallery as $image){                    
                    $thumbnail_id = get_img_id($image, $post_id);
                    if($thumbnail_id!='')
                        $gallery_array[] = $thumbnail_id;
                }
            }else{                
                echo 'project gallery is not an array.';
                var_dump($project_gallery);
                die();
            }
            update_field( 'field_6597242088832', $gallery_array, $post_id );        
        }    
        
        //Project Location Information
        $region_id=term_exists($entry['region'],'regions');
        if($region_id==NULL){
            //create the region term
            $region_id = wp_insert_term(
                $entry['region'],
                'regions',
                array(
                    'slug' => strtolower( str_ireplace( ' ', '-', $entry['region'] ) )
                )
            );
        }

        $project_location = array(	
            'field_65a1703ae1bce'	=>	$entry['state'],
            'field_65a17042e1bcf'	=>	$entry['country'],
            'field_65a72246d98f4'	=>	$region_id,	
        );

        update_field( 'field_65a17027e1bcd', $project_location, $post_id );           

    }else{
        echo 'error adding post<br/>';
        var_dump($projects_post);
        die();
    }
    //var_dump($entry);
    echo 'added post '.$post_id.'<br/>';
}    

//check if image was previously uploaded to the media library, if it wasn't - add it
function get_img_id($filename, $post_id) {
    //echo 'get img id for '.$filename.'- basename is: '.basename($filename).'<br/>';
    global $wpdb;
    
    $basename     = basename($filename);
    $thumbnail_id = intval( $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE guid LIKE '%/$basename'" ) );

    if($thumbnail_id==0){
        //image not found in media library, need to upload it.
        $thumbnail_id = upload_img_by_url( $filename, $post_id );
        
        //if we the upload of the photo failed abort
        if($thumbnail_id==0){
            echo 'error in adding '.$filename.' to media library <br/>';
        }
    }
    return $thumbnail_id;
 }  

 /**
 * Upload image from URL 
 */
function upload_img_by_url($url, $post_id){
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    include_once( ABSPATH . 'wp-admin/includes/admin.php' );

    $file = array();
    $file['name'] = $url;
    $file['tmp_name'] = download_url($url);
    $attachmentId = '';

    if (is_wp_error($file['tmp_name'])) {
        @unlink($file['tmp_name']);
        var_dump( $file['tmp_name']->get_error_messages( ) );
    } else {
        $attachmentId = media_handle_sideload($file, $post_id);
         
        if ( is_wp_error($attachmentId) ) {
            @unlink($file['tmp_name']);
            var_dump( $attachmentId->get_error_messages( ) );
        } 
    }

    return $attachmentId;
}