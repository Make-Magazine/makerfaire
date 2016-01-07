<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include 'db_connect.php';

//pull all archive data
 $sql = "SELECT post_content,post_title,post_name,ID  FROM wp_posts "
         . " right join wp_postmeta on id = post_id "
         . " and `meta_key` = 'project_name' and meta_value = '' "
         . " WHERE post_type = 'maker-entry-archive' and post_content!=''";

//$sql = "SELECT * FROM wp_posts WHERE post_type = 'maker-entry-archive' and post_content!=''";

$mysqli->query("SET NAMES 'utf8'");
$result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
    //build archive array
    $postData[$row['ID']] = array(                
        'post_content'  => $row['post_content'],
        'post_title'    => $row['post_title'],
        'post_name'     => $row['post_name']  
    );  
}

// Loop through the posts
$errorCount = 0;
$count= 0;
foreach($postData as $ID=>$post){   
    echo 'Updating '.$ID.'<br/>';
    $count++;
    $content = $post['post_content'];    
        
    $makers=array();    
    //build json Array    
    $jsonArray = json_decode( $content, true );
  
    //some data contains double quotes within the json field value.  This will fix it
     if(empty($jsonArray)){                    
        //left and right curly brace
         $content = str_replace('{"',  ' ||squigDQ|| ', $content);
         $content = str_replace('"}',  ' ||DQsquig|| ', $content);
         
         //colon, basic text and empty field values
         $content = str_replace('","', ' ||dqCommadq|| ', $content);
         $content = str_replace('":"', ' ||dqColondq|| ', $content);

         $content = str_replace('""',  ' ||dqdq|| ', $content);
        
         //clean up any remaining
         $content = str_replace('":',  ' ||DQcolon|| ',$content);
         $content = str_replace(':"',  ' ||colonDQ|| ',$content);        
        
         
         $content = str_replace('["',  ' ||LBdq|| ',$content);
         $content = str_replace('"],"',' ||dqrbcommadq|| ',$content);  
         $content = str_replace('],"', ' ||RBcommaDQ|| ',$content);                                
         
         $precontent = $content;
         //remove extra double quotes
         $content = str_replace('"', "'", $content);         
         $content = stripslashes($content);    //get rid of any \
         
         
         //now convert the other data back
         //left and right curly brace
         $content = str_replace(' ||squigDQ|| ', '{"',  $content);
         $content = str_replace(' ||DQsquig|| ', '"}',  $content);
         
         //colon, basic text and empty field values
         $content = str_replace(' ||dqCommadq|| ', '","', $content);
         $content = str_replace(' ||dqColondq|| ', '":"', $content);

         $content = str_replace(' ||dqdq|| ', '""',  $content);
        
         //clean up any remaining
         $content = str_replace(' ||DQcolon|| ','":',  $content);
         $content = str_replace(' ||colonDQ|| ',':"',  $content);        
        
         $content = str_replace(' ||LBdq|| ',       '["',  $content);         
         $content = str_replace(' ||dqrbcommadq|| ','"],"',$content);                   
         $content = str_replace(' ||RBcommaDQ|| ','"],"', $content);
         
         //remove weird stuff 
         $content = str_replace('"""','""',$content);
         $content = str_replace('[""]','[]',$content);
         $content = str_replace('["]','[]',$content);
         $content = str_replace("''","'",$content);
         $content = str_replace(' ""',"'",$content);
         $content = str_replace('"" ',"'",$content);
         
         $errorIDs = array('21749','17738','16062','15524','14121','14058','13371','11674','6621','6170','5754');
         if(in_array($ID,$errorIDs)){
           $content = str_replace('["h","t","t","p",":""]','""',$content);
           $content = str_replace('.""','.SngleQuot"',$content);  
           
           //fix for 6621           
           $content = str_replace('Raspberry Pi""]','Raspberry PiSngleQuot"]',$content);  
           //fix for 14058
           $content = str_replace("'presenter_",'"presenter_',$content);
           //fix for 14121
           $content = str_replace('Objects":','ObjectsSngleQuot:',$content);
           //fix for 16062
           $content = str_replace('The New Literacies""]','The New LiteraciesSngleQuot"]',$content);
           //fix for 17738
           $content = str_replace('u2026','...',$content);
           $content = str_replace('":u00a0n','',$content);
           //fix for 21749
           $content = str_replace('""Science Bob','"SngleQuotScience Bob',$content);
           
           $content = str_replace('SngleQuot',"'",$content);  
         }
         $jsonArray = json_decode($content, true );
        
    }
    
    $makerTitle = '';
    switch ($jsonArray['form_type']) {
         case 'exhibit':
                 $project_faire   = $jsonArray['maker_faire'];
                 $project_name    = $jsonArray['project_name'];
                 $project_photo   = $jsonArray['project_photo'];
                 $project_short   = $jsonArray['public_description'];
                 $project_website = $jsonArray['project_website'];
                 $project_video   = $jsonArray['project_video'];
                 $project_title   = $jsonArray['project_name'];
                 $makers = array();
                 if(is_array($jsonArray['m_maker_name']) &&
                         isset($jsonArray['m_maker_name']) &&
                         trim($jsonArray['m_maker_name'][0])!=''){ 
                     foreach($jsonArray['m_maker_name'] as $mkey=>$mvalue){
                         $makers[] = array('maker_name'         => $mvalue,
                                           'maker_description'  => (isset($jsonArray['m_maker_bio'][$mkey])   ? $jsonArray['m_maker_bio'][$mkey]:''),
                                           'maker_photo'        => (isset($jsonArray['m_maker_photo'][$mkey]) ? $jsonArray['m_maker_photo'][$mkey]:''),
                                           'maker_email'        => (isset($jsonArray['m_maker_email'][$mkey]) ? $jsonArray['m_maker_email'][$mkey]:''));
                     }
                 }else{
                        $makers[] = array('maker_name'        => $jsonArray['maker_name'],
                                          'maker_description' => $jsonArray['maker_bio'],
                                          'maker_photo'       => $jsonArray['maker_photo'],
                                          'maker_email'       => $jsonArray['maker_email']);
                 }
         break;
         case 'performer':             
                 $project_faire = $jsonArray['maker_faire'];
                 $project_name  = $jsonArray['performer_name'];
                 $project_photo = $jsonArray['performer_photo'];
                 $project_short = $jsonArray['public_description'];
                 $project_website = $jsonArray['performer_website'];
                 $project_video = $jsonArray['performer_video'];
                 $project_title = $jsonArray['performer_name'];
                 $makers = array();

                 break;
         default:
                $project_faire = (isset($jsonArray['maker_faire'])           ? $jsonArray['maker_faire']:''); 
                $project_name  = (isset($jsonArray['presentation_name'])     ? $jsonArray['presentation_name']:''); 
                $project_photo = (isset($jsonArray['presentation_photo'])    ? $jsonArray['presentation_photo']:'');
                $project_short = (isset($jsonArray['short_description'])     ? $jsonArray['short_description']:'');
                $project_website = (isset($jsonArray['presentation_website'])?$jsonArray['presentation_website']:'');
                $project_video = (isset($jsonArray['video']) ? $jsonArray['video'] : '');
                $project_title = (isset($jsonArray['presentation_name']) ? $jsonArray['presentation_name'] : '');

                $makers = array();
                 if(is_array($jsonArray['presenter_name']) && trim($jsonArray['presenter_name'][0])!=''){ 
                     foreach($jsonArray['presenter_name'] as $mkey=>$mvalue){
                         $makers[] = array('maker_name'        => $mvalue,
                                           'maker_description' => (isset($jsonArray['presenter_bio'][$mkey])   ? $jsonArray['presenter_bio'][$mkey]:''),
                                           'maker_photo'       => (isset($jsonArray['presenter_photo'][$mkey]) ? $jsonArray['presenter_photo'][$mkey]:''),
                                           'maker_email'       => (isset($jsonArray['presenter_email'][$mkey]) ? $jsonArray['presenter_email'][$mkey]:''));
                     }
                 }else{
                        $makers[] = array('maker_name'        => $jsonArray['presenter_name'],
                                          'maker_description' => $jsonArray['presenter_bio'],
                                          'maker_photo'       => $jsonArray['presenter_photo'],
                                          'maker_email'       => $jsonArray['presenter_email']);
                 }
                break;
       }
       
    $projectEmail = $jsonArray['email'];    
    
    //update project image    
    $attachment_id = fetch_media($project_photo, $ID);
    
    $field_array=array(
        'field_56156d5f04351'=>$project_faire,
        'field_56156d7404352'=>$project_name,
        'field_56156d9304355'=>$project_short,
        'field_56156dbd04358'=>$attachment_id,
        'field_56156d9f04356'=>$project_website,
        'field_56156dcc04359'=>$projectEmail,
        'field_56156da704357'=>$project_video,
        'field_562ed934b9416'=>$ID
    );
    
    //set ACF data
    foreach($field_array as $field_key=>$field){        
        update_field($field_key, $field, $ID);
    }
    
    //maker data    
    foreach($makers as $makerKey=>$maker){
        if($maker['maker_photo']!=''){
            //upload maker images
            $attachment_id = fetch_media($maker['maker_photo'], $ID);
            $makers[$makerKey]['maker_photo'] = $attachment_id;
        }
    }
    update_field('field_56157e9ad04c2',$makers,$ID);       
}

echo 'Updated '.$count;

/* Import media from url
 *
 * @param string $file_url URL of the existing file from the original site
 * @param int $post_id The post ID of the post to which the imported media is to be attached
 *
 * @return boolean True on success, false on failure
 */

function fetch_media($file_url, $post_id) {
    
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	global $wpdb;

	if(!$post_id) {
		return false;
	}

	//directory to import to	
	$artDir = 'wp-content/uploads/importedmedia/';

	//if the directory doesn't exist, create it	
	if(!file_exists(ABSPATH.$artDir)) {
		mkdir(ABSPATH.$artDir);
	}

	//rename the file... alternatively, you could explode on "/" and keep the original file name
        $url_array = explode(".", $file_url);
	$ext = array_pop($url_array);

        $new_filename = 'entry-'.$post_id.".".$ext; //if your post has multiple files, you may need to add a random number to the file name to prevent overwrites

	if (@fclose(@fopen($file_url, "r"))) { //make sure the file actually exists
            
		copy($file_url, ABSPATH.$artDir.$new_filename);

		$siteurl = get_option('siteurl');
		$file_info = getimagesize(ABSPATH.$artDir.$new_filename);

		//create an array of attachment data to insert into wp_posts table
		$artdata = array();
		$artdata = array(
			'post_author' => 1, 
			'post_date' => current_time('mysql'),
			'post_date_gmt' => current_time('mysql'),
			'post_title' => $new_filename, 
			'post_status' => 'inherit',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
			'post_name' => sanitize_title_with_dashes(str_replace("_", "-", $new_filename)),											'post_modified' => current_time('mysql'),
			'post_modified_gmt' => current_time('mysql'),
			'post_parent' => $post_id,
			'post_type' => 'attachment',
			'guid' => $siteurl.'/'.$artDir.$new_filename,
			'post_mime_type' => $file_info['mime'],
			'post_excerpt' => '',
			'post_content' => ''
		);

		$uploads = wp_upload_dir();
		$save_path = $uploads['basedir'].'/importedmedia/'.$new_filename;

		//insert the database record
		$attach_id = wp_insert_attachment( $artdata, $save_path, $post_id );
/*
		//generate metadata and thumbnails
		if ($attach_data = wp_generate_attachment_metadata( $attach_id, $save_path)) {         
                    echo ' i am here';
			wp_update_attachment_metadata($attach_id, $attach_data);
		}
*/

		//optional make it the featured image of the post it's attached to
		$rows_affected = $wpdb->insert($wpdb->prefix.'postmeta', array('post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attach_id));
        }else {            
		return 'false';
	}

	return $attach_id;
}

