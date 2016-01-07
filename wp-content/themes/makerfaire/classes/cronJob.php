<?php
/* 
 * this script will hold all the cronjobs for makerfaire
 */


//for testing
/*define( 'BLOCK_LOAD', true );
require_once( '../../../../wp-config.php' );
require_once( '../../../../wp-includes/wp-db.php' );
$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
build_wp_mf_maker(); //for testing*/

add_action('cron_wp_mf_maker', 'build_wp_mf_maker');
add_action('cron_wp_mf_api_entity', 'build_wp_mf_api_entity');

function build_wp_mf_api_entity(){
    global $wpdb;
    $sql = "REPLACE INTO `wp_mf_api_entity`
            (`ID`,
            `project_title`,
            `project_description`,
            `project_url`,
            `category_id`,
            `child_id_ref`,
            `thumb_image_url`,
            `large_image_url`)
            SELECT 
        `wp_rg_lead_detail`.`lead_id` AS `lead_id`,
        trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 151),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ',')) AS `Title`,COALESCE(
        trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 16),
                `l`.`value`,
                NULL)
            SEPARATOR ',')),trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 16),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ','))) AS `Description`,
       trim( GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 27),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ',')) AS `URL`,
         trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) BETWEEN 146.9999 AND 147.9999),
                    SUBSTRING_INDEX(SUBSTRING_INDEX(`wp_rg_lead_detail`.`value`, ':', 2), ':', -1),
               NULL)
            SEPARATOR ',')) AS `Categories`,
		trim(GROUP_CONCAT(distinct maker_id
            SEPARATOR ',')) AS `maker_ids`,
		trim(GROUP_CONCAT(distinct IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 22),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ',')) AS `Photo`,
        trim(GROUP_CONCAT(DISTINCT IF((FORMAT(`wp_rg_lead_detail`.`field_number`,
                    2) = 22),
                `wp_rg_lead_detail`.`value`,
                NULL)
            SEPARATOR ',')) AS `ThumbPhoto`
    FROM
        (`wp_rg_lead_detail`
        left outer JOIN `wp_rg_lead_detail_long` `l` ON ((`wp_rg_lead_detail`.`id` = `l`.`lead_detail_id`)))
        JOIN `wp_rg_lead` `b` ON ((`wp_rg_lead_detail`.`lead_id` = `b`.`id`))
        JOIN `wp_mf_maker` `c` ON (`b`.`id` = `c`.lead_id) and not isnull(`c`.`First Name`)
    GROUP BY `wp_rg_lead_detail`.`lead_id`;";
     $wpdb->get_results($sql);
}
function build_wp_mf_maker(){
    global $wpdb;
    //first empty existing tables
    $sql = "Truncate Table wp_mf_entity;";
    $wpdb->get_results($sql);
    
    $sql = "Truncate Table wp_mf_maker;";
    $wpdb->get_results($sql);
    
    $sql = "Truncate Table wp_mf_maker_to_entity;";
    $wpdb->get_results($sql);
    
    $crossRef = buildCrossRef();
    //retrieve data 
    $sql = "SELECT detail.lead_id, lead.form_id, detail.field_number, detail.value, lead.status, wp_mf_faire.faire, detail_long.value as descLong 
            FROM wp_rg_lead lead join wp_rg_lead_detail detail on lead.id = detail.lead_id and lead.status != 'trash' left outer JOIN wp_rg_lead_detail_long detail_long ON (detail.id = detail_long.lead_detail_id) join wp_mf_faire on FIND_IN_SET (detail.form_id, wp_mf_faire.form_ids)> 0";
    
    $dataArray = array();
    $leadArray = array();
    foreach($wpdb->get_results($sql) as $row){        
        //build array of leads
        $dataArray[$row->form_id][$row->lead_id][$row->field_number] = $row->value;
        if(isset($row->descLong)&&$row->descLong!=NULL){
            $dataArray[$row->form_id][$row->lead_id][$row->field_number] = $row->descLong;
        }
        $dataArray[$row->form_id][$row->lead_id]['data'] = array('status'=>$row->status, 'faire'=>$row->faire);
                
    }
    $x = 0; $m = 0;
    
    foreach($dataArray as $form_id=>$formEntries){           
        foreach($formEntries as $key=>$lead){       
            $faire = $lead['data']['faire'];
            $status=(isset($lead[$crossRef['wp_mf_entity_array']['status']])?$lead[$crossRef['wp_mf_entity_array']['status']]:'');
          
            //ensure field 303 is set
            if($status !=''){
                //build array of categories
                $leadCategory = array();
                $MAD = 0;
                foreach($lead as $leadKey=>$leadValue){
                    //4 additional categories
                    $pos = strpos($leadKey, '321');
                    if ($pos !== false) {
                        $leadCategory[]=$leadValue;
                    }
                    //main catgory
                    $pos = strpos($leadKey, '320');
                    if ($pos !== false) {
                        $leadCategory[]=$leadValue;
                    }
                    //check the flag field 304
                    $pos = strpos($leadKey, '304');
                    if ($pos !== false) {
                        if($leadValue=='Mobile App Discover')  $MAD = 1;    
                    }
                    
                   
                }
                //verify we only have unique categories
                $leadCategory = array_unique($leadCategory);
                $catList = implode(',', $leadCategory);
                    
                //build wp_mf_entity table
                $presentationType   = (isset($lead[$crossRef['wp_mf_entity_array']['presentation_type']])   ? esc_sql($lead[$crossRef['wp_mf_entity_array']['presentation_type']])  : '');
                $presentationTitle  = (isset($lead[$crossRef['wp_mf_entity_array']['presentation_title']])  ? esc_sql($lead[$crossRef['wp_mf_entity_array']['presentation_title']]) : '');
                $specialRequest     = (isset($lead[$crossRef['wp_mf_entity_array']['special_request']])     ? esc_sql($lead[$crossRef['wp_mf_entity_array']['special_request']])    : '');
                $onSitePhone        = (isset($lead[$crossRef['wp_mf_entity_array']['OnsitePhone']])         ? esc_sql($lead[$crossRef['wp_mf_entity_array']['OnsitePhone']])        : '');
                $descShort          = (isset($lead[$crossRef['wp_mf_entity_array']['desc_short']])          ? esc_sql($lead[$crossRef['wp_mf_entity_array']['desc_short']])         : '');
                $descLong           = (isset($lead[$crossRef['wp_mf_entity_array']['desc_long']])           ? esc_sql($lead[$crossRef['wp_mf_entity_array']['desc_long']])          : '');
                $projectPhoto       = (isset($lead[$crossRef['wp_mf_entity_array']['project_photo']])       ? esc_sql($lead[$crossRef['wp_mf_entity_array']['project_photo']])      : '');
                
                $wp_mf_entitysql = "insert into wp_mf_entity "
                         . "    (lead_id, presentation_title, presentation_type, special_request, "
                         . "     OnsitePhone, desc_short, desc_long, project_photo, status,category,faire,mobile_app_discover) "
                         . " VALUES ('".$key."',"
                            . ' "'.$presentationTitle .'", '
                            . ' "'.$presentationType  .'", '
                            . ' "'.$specialRequest    .'", '
                            . ' "'.$onSitePhone       .'", '
                            . ' "'.$descShort         .'", '
                            . ' "'.$descLong          .'", '
                            . ' "'.$projectPhoto      .'", '
                            . ' "'.$status                                                      .'", '
                            . ' "'.$catList                                                     .'", '
                            . ' "'.$faire                                                       .'", '
                            . '  '.$MAD               .') '; 
                $x++;

                $wpdb->get_results($wp_mf_entitysql);
                if($wpdb->insert_id==false){
                    echo 'error inserting record wp_mf_entity:'.$wp_mf_entitysql.'<br/><br/>';
                    $entityID = 0;
                }
                
                //build wp_mf_maker table (up to 10 rows)
                foreach($crossRef['wp_mf_maker_array'] as $type =>$typeArray){  
                    $fNameLoc = (string) $typeArray['First Name'];
                    //if first name is set for this type and the field numer is set in the returned table data, then use this data else use a blank 
                    $firstName  =  (isset($typeArray['First Name']) && isset($lead[$fNameLoc])              ? esc_sql($lead[$fNameLoc]) : '');
                    
                    $lNameLoc = (string) $typeArray['Last Name'];
                    //if first name is set for this type and the field numer is set in the returned table data, then use this data else use a blank 
                    $lastName   = (isset($typeArray['Last Name'])  && isset($lead[$lNameLoc])               ? esc_sql($lead[$lNameLoc])                : '');
                    
                    //we need to have at least 1 presenter.  if these fields are empty, pull from the contact info
                    if(trim($firstName)=='' && trim($lastName)==''){                        
                        if($type=='presenter'){
                           $fNameLoc = (string) $crossRef['wp_mf_maker_array']['contact']['First Name'];
                           //if first name is set for this type and the field numer is set in the returned table data, then use this data else use a blank 
                           $firstName  =  (isset($crossRef['wp_mf_maker_array']['contact']['First Name']) && isset($lead[$fNameLoc])              ? esc_sql($lead[$fNameLoc]) : '');

                           $lNameLoc = (string) $crossRef['wp_mf_maker_array']['contact']['Last Name'];
                           
                           //if first name is set for this type and the field numer is set in the returned table data, then use this data else use a blank 
                           $lastName   = (isset($crossRef['wp_mf_maker_array']['contact']['Last Name'])  && isset($lead[$lNameLoc])               ? esc_sql($lead[$lNameLoc])                : '');
                           $bio        = (isset($crossRef['wp_mf_maker_array']['contact']['Bio'])        && isset($lead[$crossRef['wp_mf_maker_array']['contact']['Bio']])       ? esc_sql($lead[$crossRef['wp_mf_maker_array']['contact']['Bio']])        : '');
                           $email      = (isset($crossRef['wp_mf_maker_array']['contact']['Email'])      && isset($lead[$crossRef['wp_mf_maker_array']['contact']['Email']])     ? esc_sql($lead[$crossRef['wp_mf_maker_array']['contact']['Email']])      : '');
                           $phone      = (isset($crossRef['wp_mf_maker_array']['contact']['phone'])      && isset($lead[$crossRef['wp_mf_maker_array']['contact']['phone']])     ? esc_sql($lead[$crossRef['wp_mf_maker_array']['contact']['phone']])      : '');
                           $twitter    = (isset($crossRef['wp_mf_maker_array']['contact']['TWITTER'])    && isset($lead[$crossRef['wp_mf_maker_array']['contact']['TWITTER']])   ? esc_sql($lead[$crossRef['wp_mf_maker_array']['contact']['TWITTER']])    : '');
                           $photo      = (isset($crossRef['wp_mf_maker_array']['contact']['Photo'])      && isset($lead[$crossRef['wp_mf_maker_array']['contact']['Photo']])     ? esc_sql($lead[$crossRef['wp_mf_maker_array']['contact']['Photo']])      : '');
                           $website    = (isset($crossRef['wp_mf_maker_array']['contact']['website'])    && isset($lead[$crossRef['wp_mf_maker_array']['contact']['website']])   ? esc_sql($lead[$crossRef['wp_mf_maker_array']['contact']['website']])    : '');
                           $guid       = createGUID($key .'-'.$crossRef['wp_mf_maker_array']['contact']['identifier']);    
                        }
                    }
                    
                    if(trim($firstName)=='' && trim($lastName)==''){
                        //don't write the record, no maker here
                    }else{
                        $bio        = (isset($typeArray['Bio'])        && isset($lead[$typeArray['Bio']])       ? esc_sql($lead[$typeArray['Bio']])        : '');
                        $email      = (isset($typeArray['Email'])      && isset($lead[$typeArray['Email']])     ? esc_sql($lead[$typeArray['Email']])      : '');
                        $phone      = (isset($typeArray['phone'])      && isset($lead[$typeArray['phone']])     ? esc_sql($lead[$typeArray['phone']])      : '');
                        $twitter    = (isset($typeArray['TWITTER'])    && isset($lead[$typeArray['TWITTER']])   ? esc_sql($lead[$typeArray['TWITTER']])    : '');
                        $photo      = (isset($typeArray['Photo'])      && isset($lead[$typeArray['Photo']])     ? esc_sql($lead[$typeArray['Photo']])      : '');
                        $website    = (isset($typeArray['website'])    && isset($lead[$typeArray['website']])   ? esc_sql($lead[$typeArray['website']])    : '');
                        $guid       = createGUID($key .'-'.$typeArray['identifier']);
                    
                        $wp_mf_makersql = "INSERT INTO wp_mf_maker(lead_id, `First Name`, `Last Name`, `Bio`, `Email`, `phone`, "
                                                                . " `TWITTER`,  `form_id`, `maker_id`, `Photo`, `website`) "
                                            . " VALUES (".$key.", '".$firstName."','".$lastName."','".$bio."','".$email."', '".$phone."',"
                                                      . " '".$twitter."', ".$form_id.",'".$guid."','".$photo."','".$website."')";
                        $wpdb->get_results($wp_mf_makersql);
                        if($wpdb->insert_id==false){
                            echo 'error inserting record wp_mf_maker:'.$wp_mf_makersql.'<br/><br/>';
                        }                                    
                        $m++;
                        
                        //build maker to entity table
                        $wp_mf_maker_to_entity = "INSERT INTO `wp_mf_maker_to_entity`" . " (`maker_id`, `entity_id`, `maker_type`) " 
                                              . ' VALUES ("'.$guid.'",'.$key.',"'.$type.'")';
                        
                        $wpdb->get_results($wp_mf_maker_to_entity);
                        if($wpdb->insert_id==false){
                            echo 'error inserting record wp_mf_maker_to_entity:'.$wp_mf_maker_to_entity.'<br/><br/>';
                        }  
                    }
                }                                                                    
            } //end check field 303 status            
        }
    }
    echo 'added '.$x. ' entity records<br/>';
    echo 'added '.$m. ' maker records<br/>';   
}

function createGUID($id){

        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid($id, true)));
        $hyphen = chr(45);// "-"
        $uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"
        return $uuid;
}

    /*
     * We can get up to 10 records from one form entry 
     * Contact, Presenter 1-7, and group
     */
function buildCrossRef(){
    //fields for wp_mf_maker
    $crossRef['wp_mf_maker_array'] = 
    array('contact'    => array('First Name' =>  96.3, 'Last Name' =>  96.6,               'Email' =>  98, 'phone' =>  99, 'TWITTER' => 201, 'identifier' => 1),
          'presenter'  => array('First Name' => 160.3, 'Last Name' => 160.6, 'Bio' => 234, 'Email' => 161, 'phone' => 185, 'TWITTER' => 201, 'Photo' => 217, 'website' => 209, 'identifier' => 2),
          'presenter2' => array('First Name' => 158.3, 'Last Name' => 158.6, 'Bio' => 258, 'Email' => 162, 'phone' => 192, 'TWITTER' => 208, 'Photo' => 224, 'website' => 216, 'identifier' => 3),
          'presenter3' => array('First Name' => 155.3, 'Last Name' => 155.6, 'Bio' => 259, 'Email' => 167, 'phone' => 190, 'TWITTER' => 207, 'Photo' => 223, 'website' => 215, 'identifier' => 4),
          'presenter4' => array('First Name' => 156.3, 'Last Name' => 156.6, 'Bio' => 260, 'Email' => 166, 'phone' => 191, 'TWITTER' => 206, 'Photo' => 222, 'website' => 214, 'identifier' => 5),
          'presenter5' => array('First Name' => 157.3, 'Last Name' => 157.6, 'Bio' => 261, 'Email' => 165, 'phone' => 189, 'TWITTER' => 205, 'Photo' => 220, 'website' => 213, 'identifier' => 6),
          'presenter6' => array('First Name' => 159.3, 'Last Name' => 159.6, 'Bio' => 262, 'Email' => 164, 'phone' => 188, 'TWITTER' => 204, 'Photo' => 221, 'website' => 211, 'identifier' => 7),
          'presenter7' => array('First Name' => 154.3, 'Last Name' => 154.6, 'Bio' => 263, 'Email' => 163, 'phone' => 187, 'TWITTER' => 203, 'Photo' => 219, 'website' => 212, 'identifier' => 8),
          'group'      => array('First Name' => 109.3, 'Last Name' => 109.6, 'Bio' => 110,                                 'TWITTER' => 322, 'Photo' => 111, 'website' => 112, 'identifier' => 9)
    );       
    
    //fields for wp_mf_entity
    $crossRef['wp_mf_entity_array']  = array('presentation_title' => 151, 'presentation_type' => 1, 'special_request' => 64, 'OnsitePhone' => 265, 'desc_short' => 16, 'desc_long' => 11, 'project_photo' => 22, 'project_website' => 27, 'project_video' => 32, 'status' => 303);    
    return $crossRef;
}


//this cron action will create the JSON files used by the blue ribbon page
add_action('cron_ribbonJSON', 'build_ribbonJSON');

function build_ribbonJSON(){
    global $wpdb;
    require_once( TEMPLATEPATH. '/partials/ribbonJSON.php' );  
  
    $yearSql  = $wpdb->get_results("SELECT distinct(year) FROM wp_mf_ribbons  where entry_id > 0 order by year desc");

    foreach($yearSql as $year){
        $json = createJSON($year->year);
        //write json file
        unlink(TEMPLATEPATH.'/partials/data/'.$year->year.'ribbonData.json'); //delete json file if exists
        $fp = fopen(TEMPLATEPATH.'/partials/data/'.$year->year.'ribbonData.json', 'w');//create json file
        
        fwrite($fp, $json);
        fclose($fp);
    }      
}