<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * This chunk of code is for testing only 

define( 'BLOCK_LOAD', true );

require_once( '../../../../wp-config.php' );
require_once( '../../../../wp-includes/wp-db.php' );

$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
createJson('2015');/*
*/

function  createJson($year=''){
    global $wpdb;
    
    $ribbonData=array();
    $data = array();
    $json = array();
    $blueList = array();
    $redList = array();
    
    $filter = " and year= ".($year!=''? $year:date("Y"));
    $sql = "SELECT entry_id, location, year, ribbonType, numRibbons,project_name,project_photo, post_id, maker_name "
            . " FROM `wp_mf_ribbons` where entry_id > 0 ".$filter." "
            . " ORDER BY ribbonType ASC, numRibbons desc, entry_id";
        
    foreach($wpdb->get_results($sql,ARRAY_A) as $ribbon){          
        $entry_id       = $ribbon['entry_id'];
        $link           = "/mfarchives/". $entry_id;
        $post_id        = $ribbon['post_id'];
        $project_name   = $ribbon['project_name'];
        $project_photo  = $ribbon['project_photo'];  
        $project_desc   = '';  
        $maker_name     = $ribbon['maker_name'];
        $location   = $ribbon['location'];
        $year       = $ribbon['year'];
        $ribbonType = $ribbon['ribbonType'];
        $numRibbons = $ribbon['numRibbons'];  
        
        //pull the project information - mf_ribbons data takes precedence      
        if($post_id!=0){        //archived record            
            $makerSQL = "select post.post_content, wp_postmeta.* "
                      . " from wp_posts post "
                      . " right outer join wp_postmeta on "
                    . "                     post.ID = post_id and "
                    . "                   ((post_id = $post_id and meta_key like '%maker_name%') or "
                    . "                    (post_id = $post_id and meta_key in('project_photo','project_name')) or "
                    . "                    (post_id = $post_id and meta_key like '%project_description%')) "                
                    . "  where post.ID = $post_id ORDER BY `wp_postmeta`.`meta_key` DESC";

            foreach($wpdb->get_results($makerSQL,ARRAY_A) as $projData){ 
                //wpv1 project data is in the post_content field
                //cs project data is in the meta fields
                if($projData['post_content']!=''){
                    $jsonArray = json_decode($projData['post_content'], true );
                    //if there is an error, try to fix the json
                    if(empty($jsonArray)){  
                        $content = fixWPv1Json($projData['post_content'],$post_id);
                        $jsonArray = json_decode($content, true );
                    }
                    if(!empty($jsonArray)){
                        if($jsonArray['form_type']=='presenter'){
                            $project_name  = $jsonArray['presentation_name'];
                            $project_photo = $jsonArray['presentation_photo'];
                            $maker_name    = $jsonArray['presenter_name'];  
                            $project_desc  = (isset($jsonArray['public_description'])?$jsonArray['public_description']:'');
                        }elseif($jsonArray['form_type']=='exhibit'){                    
                                $project_name  = $jsonArray['project_name'];
                                $project_photo = $jsonArray['project_photo'];
                                $maker_name    = $jsonArray['maker_name'];   
                                $project_desc  = (isset($jsonArray['public_description'])?$jsonArray['public_description']:'');
                        }elseif($jsonArray['form_type']=='performer'){                    
                                $project_name  = $jsonArray['performer_name'];
                                $project_photo = $jsonArray['performer_photo'];
                                $maker_name    = $jsonArray['name'];  
                                $project_desc  = (isset($jsonArray['public_description'])?$jsonArray['public_description']:'');
                        }
                        break;
                    }
                }
                $field = $projData['meta_key'];
                $value = $projData['meta_value'];
                if($field=='project_photo' && $project_photo ==''){  
                    if(is_numeric($value)){                    
                        $project_photo = wp_get_attachment_url( $value);
                    }else{
                        $project_photo = $value;
                    }
                }
                if($field=='project_name'  && $project_name =='')   $project_name  = $value;
                if(strpos($field, 'maker_name')!== false){
                    //if maker name has field_ in it, it is not a valid maker name.
                    if(strpos($value, 'field_')===false && $maker_name=='')  $maker_name = $value;
                }
                if($field=='project_description'  && $project_desc =='')   $project_desc  = $value;
            }
        }else{ //non archived record
            $link           = "/maker/entry/". $entry_id;
            $maker_first_name = $maker_last_name = '';
            //check if this is a GF entry
            $leadSQL = "SELECT  wp_rg_lead_detail.field_number,wp_rg_lead_detail.value,wp_rg_lead_detail_long.value as long_value  "
                    . " FROM wp_rg_lead_detail "
                    . " left outer join wp_rg_lead_detail_long ON "
                    . "     wp_rg_lead_detail.id = wp_rg_lead_detail_long.lead_detail_id "
                    . " WHERE wp_rg_lead_detail.lead_id = ".$entry_id ." order by lead_id, field_number";
            
            foreach($wpdb->get_results($leadSQL,ARRAY_A) as $projData){             
                switch($projData['field_number']){
                    case '22':
                        $project_photo = $projData['value'];
                        break;
                    case '151';
                        $project_name = $projData['value'];
                        break;
                    case '16';
                        if($projData['long_value'] != NULL){
                            $project_desc = $projData['long_value'];
                        }else{
                            $project_desc = $projData['value'];
                        }
                        break;
                    case '160.3':
                        $maker_first_name = $projData['value'];
                        break;
                    case '160.6':
                        $maker_last_name = $projData['value'];
                        break;
                }

                $maker_name = $maker_first_name . ' ' . $maker_last_name;
            }
        }
        //do not add to ribbon array if $project_name, $project_photo and $project_desc are blank
        if($project_name=='' && $project_photo == '' && $project_desc == '' ){      
            //skip
        }else{
            //build ribbon data array                           
            $currCount = (isset($ribbonData[$entry_id]['ribbon'][$ribbonType]['count']) ? $ribbonData[$entry_id]['ribbon'][$ribbonType]['count']:0);
            $ribbonData[$entry_id]['ribbon'][$ribbonType]['count']  = (int) $currCount + (int) $numRibbons;        
            $ribbonData[$entry_id]['fairedata'][]   = array( 'year'=>$year, 'faire'=>$location,'ribbonType'=>($ribbonType==0?'blue':'red'));               
            $ribbonData[$entry_id]['project_name']  = $project_name;
            $ribbonData[$entry_id]['project_photo'] = $project_photo; 
            $ribbonData[$entry_id]['project_desc']  = $project_desc; 
            $ribbonData[$entry_id]['maker_name']    = $maker_name;
            $ribbonData[$entry_id]['link']    = $link;
        }

    }
    
    foreach($ribbonData as $entry_id=>$data){  
        $blueCount = (isset($data['ribbon'][0]['count'])?$data['ribbon'][0]['count']:0);
        $redCount  = (isset($data['ribbon'][1]['count'])?$data['ribbon'][1]['count']:0);
        $project_photo = $data['project_photo'];
        $project_photo  = legacy_get_fit_remote_image_url($project_photo,285,270,0);
        $jsondata = array('entryID'=> $entry_id,
                "blueCount"     => $blueCount,
                "redCount"      => $redCount,
                "project_name"  => html_entity_decode($data['project_name']),
                "project_photo" => $project_photo,
                "maker_name"    => $data['maker_name'],
                "link"          => $data['link'],
                "project_description" => html_entity_decode($data['project_desc']),
                "faireData"     => array_map("unserialize", array_unique(array_map("serialize", $data['fairedata'])))
            );
        $json[] = $jsondata;
        if($blueCount > 0){            
            $blueList[$blueCount]['winners'][]  = $jsondata;
            $blueList[$blueCount]['numRibbons'] = $blueCount;
        }
        if($redCount > 0){
            $redList[$redCount]['winners'][]  = $jsondata;
            $redList[$redCount]['numRibbons'] = $redCount;
        }
    }
    
    //blue list is an array of data
    //$blueList[number of blue ribbons]= array('numRibbons'=>number of blue ribbons
    //                                         'winners' =>ribbon data
    //                                         )    
    
    
    //sort blue list and red list, within each group, alphabetically
    
    array_sort_by_column($blueList, 'numRibbons',SORT_DESC);
    foreach($blueList as $key=>&$value){                
        if(is_array($value['winners'])) {
            array_sort_by_column($value['winners'], 'project_name');          
        }    
        foreach($value['winners'] as $winnerKey=>$winner){
            foreach($winner['faireData'] as $fdKey=>$faireData){
                if($faireData['ribbonType']=='red') unset($value['winners'][$winnerKey]['faireData'][$fdKey]);
            }
        }
        $blueList[$key]['winners'] = $value['winners'];
    }
         
    array_sort_by_column($redList, 'numRibbons',SORT_DESC);
    foreach($redList as $key=>&$value){        
        if(is_array($value['winners'])) {
            array_sort_by_column($value['winners'], 'project_name');
        }  
        foreach($value['winners'] as $winnerKey=>$winner){
            foreach($winner['faireData'] as $fdKey=>$faireData){
                if($faireData['ribbonType']=='blue') unset($value['winners'][$winnerKey]['faireData'][$fdKey]);
            }
        }
        $redList[$key]['winners'] = $value['winners'];
    }
    
    $return['json']     = $json;
    $return['blueList'] = $blueList;
    $return['redList']  = $redList;
    
    return json_encode($return);
    
}

    //sort ribbon data[] by blue ribbon count
   function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }
   
    function fixWPv1Json($content,$ID=0){
                 
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
        return $content;
    }
    
    function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }   