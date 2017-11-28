<?php
function getRibbons($year){
  global $wpdb;
  /* return json layout
   * entryID    - numeric   entry ID or lead ID
   * blueCount  - numeric   numer of blue ribbons won
   * redCount   - numeric   numer of red ribbons won
   * project_name
   * project_photo
   * maker_name
   * link
   * project_description
   * faire_data - array
   *      year, faire, ribbonType
   *
   */
  $return = array();

  $sql = "SELECT entry_id, location, year, "
              . "wp_mf_ribbons.project_name as ribbon_proj_name, "
              . "wp_mf_ribbons.project_photo as ribbon_proj_photo, post_id, maker_name, "
              . "wp_mf_entity.presentation_title as project_name, "
              . "wp_mf_entity.project_photo, "
              . "(select sum(red_ribbon.numRibbons)
                  from wp_mf_ribbons red_ribbon
                  where red_ribbon.ribbonType=1 and red_ribbon.entry_id = wp_mf_ribbons.entry_id
                  group by red_ribbon.entry_id) as red_ribbon_cnt,
                (select sum(blue_ribbon.numRibbons)
                  from wp_mf_ribbons blue_ribbon
                  where blue_ribbon.ribbonType=0 and blue_ribbon.entry_id = wp_mf_ribbons.entry_id
                  group by blue_ribbon.entry_id) as blue_ribbon_cnt  "
        . "FROM `wp_mf_ribbons` "
        . "left outer join wp_mf_entity on lead_id=entry_id "
        . "where year= ".($year!=''? $year:date("Y"))
        . " group by entry_id "
        . " ORDER BY entry_id";

    foreach($wpdb->get_results($sql,ARRAY_A) as $ribbon){
      //entry information
      $entry_id       = $ribbon['entry_id'];
      $blue_ribbon_cnt = (is_numeric($ribbon['blue_ribbon_cnt'])?$ribbon['blue_ribbon_cnt']:0);
      $red_ribbon_cnt  = (is_numeric($ribbon['red_ribbon_cnt'])?$ribbon['red_ribbon_cnt']:0);
      //ribbon information
      $location       = $ribbon['location'];
      $year           = $ribbon['year'];

      $post_id        = $ribbon['post_id'];

      //entries from 2015 and forward are in the correct format and post_id will be 0
      if($post_id==0){
        $link           = "/maker/entry/". $entry_id;
        //overwrites
        $project_name   = ($ribbon['ribbon_proj_name']  != '' && !is_null($ribbon['ribbon_proj_name'])  ? $ribbon['ribbon_proj_name']  : $ribbon['project_name']);
        $project_photo  = ($ribbon['ribbon_proj_photo'] != '' && !is_null($ribbon['ribbon_proj_photo']) ? $ribbon['ribbon_proj_photo'] : $ribbon['project_photo']);
        $maker_name     = ($ribbon['maker_name']        != '' && !is_null($ribbon['maker_name'])        ? $ribbon['maker_name']        : getMakerList($entry_id));
      }else{
        $link           = "/mfarchives/". $post_id;
        $project_name   = ($ribbon['ribbon_proj_name']  != '' && !is_null($ribbon['ribbon_proj_name'])  ? $ribbon['ribbon_proj_name']  : '');
        $project_photo  = ($ribbon['ribbon_proj_photo'] != '' && !is_null($ribbon['ribbon_proj_photo']) ? $ribbon['ribbon_proj_photo'] : '');
        $maker_name     = ($ribbon['maker_name']        != '' && !is_null($ribbon['maker_name'])        ? $ribbon['maker_name']        : '');

        //if project name or project photo are not set, then we need to pull the archived data from post information
        if($project_name =='' || $project_photo==='') {
          //if the post_id is set, we need to pull archived information to get maker name, project name and project photo
          $makerSQL = "select post.post_content, wp_postmeta.*
                        from  wp_posts post
                        left outer join wp_postmeta on post.ID = post_id
                        where post.ID = ".$post_id." and
                              (meta_key like '%maker_name%' or meta_key in('project_photo','project_name')) and
                              meta_value != '' and
                              meta_value != '0'
                        ORDER BY wp_postmeta.meta_key DESC";

          foreach($wpdb->get_results($makerSQL,ARRAY_A) as $projData){
            //wpv1 project data is in the post_content field
            //cs project data is in the meta fields
            if($projData['post_content']!=''){
                $jsonArray = json_decode($projData['post_content'], true );
                //if there is an error, try to fix the json
                if(empty($jsonArray)){
                    $content   = fixWPv1Json($projData['post_content'],$post_id);
                    $jsonArray = json_decode($content, true );
                }

                if(!empty($jsonArray)){
                    if($jsonArray['form_type']=='presenter'){
                      $project_name  = $jsonArray['presentation_name'];
                      $project_photo = $jsonArray['presentation_photo'];
                      $maker_name    = $jsonArray['presenter_name'];
                    }elseif($jsonArray['form_type']=='exhibit'){
                      $project_name  = $jsonArray['project_name'];
                      $project_photo = $jsonArray['project_photo'];
                      $maker_name    = $jsonArray['maker_name'];
                    }elseif($jsonArray['form_type']=='performer'){
                      $project_name  = $jsonArray['performer_name'];
                      $project_photo = $jsonArray['performer_photo'];
                      $maker_name    = $jsonArray['name'];
                    }
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
          }
          if(is_array($maker_name)){
            if(!is_array($maker_name[0])){
              $maker_name = $maker_name[0];
            }else{
              $maker_name = '';
            }
          }
        }
      }
      $project_photo  = legacy_get_fit_remote_image_url($project_photo,285,270,0);

      //do not add to ribbon array if $project_name and $project_photo are blank
      if($project_name==''){
        //echo $entry_id .' '. ' '.$project_photo;
        //do nothing
      }else{
        $ribbonType = array();
        if($red_ribbon_cnt>0)   $ribbonType[] = 'red';
        if($blue_ribbon_cnt>0)  $ribbonType[] = 'blue';

        $return[] = array(
            'entryID'               => $entry_id,
            'blueCount'             => (int) $blue_ribbon_cnt,
            'redCount'              => (int) $red_ribbon_cnt,
            'project_name'          => html_entity_decode($project_name),
            'project_photo'         => $project_photo,
            'maker_name'            => html_entity_decode($maker_name),
            'location'              => $location,
            'link'                  => $link,
            'ribbonType'            => $ribbonType,
            'faireYear'             => $year
        );
      }
    }

  return $return;
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
