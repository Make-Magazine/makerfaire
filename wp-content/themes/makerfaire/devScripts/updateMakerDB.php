<?php
//include 'db_connect.php';
include '../../../../wp-load.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$form = (isset($_GET['form']) ? $_GET['form'] : '');
$page = (isset($_GET['page']) ? $_GET['page'] : 1);
$limit = (isset($_GET['limit']) ? $_GET['limit'] : 30);
$offset = ($page != 1 ? (($page - 1) * $limit) : 0);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body>

        <?php
        global $wpdb;        
       
        $blog_id = 9999; //we will use blog id of 9999 for flagship    
        $formtable = "wp_gf_form";
        $metatable = 'wp_gf_form_meta';
            
        //find all forms for this blog that are not trashed
        $formSQL = "SELECT title, formtable.date_created, form_meta.display_meta, form_meta.form_id "
                . " FROM ".$formtable. " formtable "
                . " left outer join ".$metatable." form_meta on formtable.id=form_meta.form_id "
                . " WHERE is_trash=0 " .($form!=''?' and id='.$form:'')
                . " and id <> 252"

                //. " WHERE is_trash=0 and year(date_created)>=2022"
                . " ORDER BY `form_meta`.`form_id` DESC ";                    
        
        $formResults = $wpdb->get_results($formSQL, ARRAY_A);
        //echo $formSQL;
        $updArray = array();
                    
        //loop thru all forms in this blog
        foreach ($formResults as $formrow) {                            
            $form_id = $formrow['form_id'];                
            //determine faire name from form id            
            $formSQL = "select faire_name, start_dt from wp_mf_faire where FIND_IN_SET ($form_id, wp_mf_faire.form_ids)> 0";
            //echo $formSQL.'<br/>';
            $results = $wpdb->get_row($formSQL);
            if ($wpdb->num_rows > 0) {
                $faire = $results->faire_name;    
                $start_dt = $results->start_dt;            
            }else{
                $faire = get_bloginfo('name');  
                $start_dt = 'unknown';       
            }
         

            $fieldData = '';
            $output = true;
            $json = json_decode($formrow['display_meta']);                        
            $form_type = (isset($json->form_type) ? $json->form_type : '');    

            //only process Call for Makers forms            
            if ($form_type == 'Master' || $form_type == 'Exhibit') {                                         
                echo 'Form '.$formrow['title'].' FormID='.$form_id.' FormType='.$form_type  .' Faire Start= '.$start_dt.'<br/>';       
                $count=0;
                $entries = GFAPI::get_entries($form_id,array('status' => 'active'), null, array( 'offset' => 0, 'page_size' => 999 ), $count);                                
                
                if($count>999){
                    echo 'WARNING!! More than 999 entries found. Total found = '.$count.'<br/>';
                    die('mission aborted');
                }
                echo '&emsp;found '.count($entries).' entries<br/>';                        
                $approved = 0;

                //loop through entries
                foreach ($entries as $entry) {   
                    //faire year is based on the date the entry was created
                    $datetime = new DateTime($entry['date_created']);
                    $faire_month = $datetime->format('m');                
                    $faire_year = $datetime->format('Y');
                    
                    if($faire_month >= 11){
                        $faire_year++;
                    }

                    //only pull entries for 2023
                    if($faire_year!='2023'){
                        //echo 'entry date created is '.$entry['date_created'];
                        //echo '$faire_year is '.$faire_year.'<Br/>';
                        continue;
                    }

                    //if status is not set, chances are this is not a valid cfm entry
                    if(isset($entry['303'])){
                        $count++;
                        updateMakerTables((array) $entry, $form_id, $blog_id);
                    }
                                        
                }
                echo '&emsp;wrote '.count($entries).' entries<br/>';                  
            
                // die('check entries');
            }else{
                //echo 'Invalid Form type. FormType = '.$form_type.'<br/>';
            }
        }                            
        ?>
    </body>
</html>

<?php
/* Function to add/update the maker data tables for entity/project and maker data
 *
 *  Entity/project and maker data only saved for
 *   - Exhibit
 *   - Presentation
 *   - Performance
 *   - Startup Sponsor
 *   - Sponsor
 *  All other form types are skipped
 */

function updateMakerTables($entry, $form_id, $blog_id) {
    global $wpdb;
    
    $entryID = $entry['id'];
    //echo 'adding entry id '.$entryID.'<br/>'; 
    
    //build Maker and Entry Data Array
    $data = buildMakerData($entry, $form_id);
    
    //if this maker is marked as do not display, exit
    if(!$data){
        return;
    }
    $makerData  = $data['maker'];
    $entryData  = $data['entry'];
    
    $categories = (is_array($entryData['category']) ? implode(',', $entryData['category']) : '');

    if($entryData['title']==''){
        echo 'Danger Will Robinson!! No Project Title set. Abort! Abort!! ';
        //echo 'blog id = '.$blog_id.", entry id= " . $entryID . ", form id = " . $entryData['form_id'] .'<br/>';        
        return;            
    }
    
    /*
     * Update Entry Table - wp_mf_dir_entry
     * Fields - blog_id, entry_id, form_id, title, type, public_desc, project_photo, main_category, category, project_video, inspiration, website, social, state, country, faire_name, faire_year, status, last_change_date, 
     
     */
    $wp_mf_entitysql = "insert into wp_mf_dir_entry (blog_id, entry_id, form_id, title, type, public_desc, project_photo, main_category, category, project_video, inspiration, website, social, state, country, faire_name, faire_year, status, entry_link, last_change_date) "
            . " VALUES (".$blog_id."," . $entryID . "," . $entryData['form_id'] . ','
            . ' "' . $entryData['title'] . '", "'.$entryData['type'].'", "'.$entryData['public_desc'].'", '
            . ' "' . $entryData['project_photo'] . '", "'.$entryData['main_category'].'", "'. $categories.'", '
            . ' "' . $entryData['project_video'] . '", "'.$entryData['inspiration'].'", "'.$entryData['website'].'", '
            . ' "' . $entryData['social'] . '", "'.$entryData['state'].'", "'.$entryData['country'].'", '
            . ' "' . $entryData['faire_name'] . '", "'.$entryData['faire_year'].'", "'.$entryData['status'].'", "'.$entryData['link'].'", now()'
            . ') '
            . ' ON DUPLICATE KEY UPDATE title  = "' . $entryData['title'] . '", '
            . '                         type   = "' . $entryData['type'] . '", '
            . '                         public_desc   = "' . $entryData['public_desc'] . '", '
            . '                         project_photo   = "' . $entryData['project_photo'] . '", '
            . '                         main_category   = "' . $entryData['main_category'] . '", '
            . '                         category   = "' . $categories . '", '
            . '                         project_video   = "' . $entryData['project_video'] . '", '
            . '                         inspiration   = "' . $entryData['inspiration'] . '", '
            . '                         website   = "' . $entryData['website'] . '", '
            . '                         social   = "' . $entryData['social'] . '", '
            . '                         state   = "' . $entryData['state'] . '", '
            . '                         country   = "' . $entryData['country'] . '", '
            . '                         faire_name   = "' . $entryData['faire_name'] . '", '
            . '                         faire_year   = "' . $entryData['faire_year'] . '", '
            . '                         status   = "' . $entryData['status'] . '", '            
            . '                         entry_link   = "' . $entryData['link'] . '", '            
            . '                         last_change_date    = now()';
    
    $wpdb->query($wp_mf_entitysql);
    
    /*  Update Maker Table - wp_mf_dir_maker table
        Fields: email, first_name, last_name, bio, social, photo, website, age_range, maker_id, last_change_date
     */
    
    //loop thru
    foreach ($makerData as $maker) {
        $firstName = esc_sql($maker['first_name']);
        $lastName  = esc_sql($maker['last_name']);
        $email     = $maker['email'];
        $bio       = htmlentities($maker['bio'], ENT_QUOTES);
        $social    = htmlentities($maker['social'], ENT_QUOTES);
        $photo     = htmlentities($maker['photo']);
        $website   = htmlentities($maker['website']);
        $social    = htmlentities($maker['social'], ENT_QUOTES);
        $age_range = esc_sql($maker['age_range']);
        
        // maker db 
        /*  GUID
        * If this maker is already in the DB - pull the maker_id, else let's create one
        */
        $results = $wpdb->get_results("SELECT wp_mf_dir_maker.maker_id
                                        FROM wp_mf_dir_maker
                                        right outer join wp_mf_dir_maker_to_entry
                                            on wp_mf_dir_maker_to_entry.maker_id=wp_mf_dir_maker.maker_id 
                                            and blog_id=".$blog_id." 
                                            and entry_id=".$entryID." and maker_type='".$maker['role']."'
                                        where email = '".$email."'");
        
        $guid = ($wpdb->num_rows != 0 ? $guid = $results[0]->maker_id : createGUID($blog_id.$entryID.$maker['role']));
        
      
        $wp_mf_makersql = "INSERT INTO `wp_mf_dir_maker`"
                    . " (email, first_name, last_name, bio, social, photo, website, age_range, maker_id, last_change_date) "
                    . '  VALUES ("' . $email . '","' . $firstName . '","' . $lastName . '","' . $bio . '","' . $social . '",'
                    . '          "' . $photo . '","' . $website . '","' . $age_range . '","' . $guid . '", now())'
                    . '  ON DUPLICATE KEY UPDATE maker_id="' . $guid . '", last_change_date=now()';        

        //only update non blank fields
        $wp_mf_makersql .= ($firstName != '' ? ', first_name = "' . $firstName . '"' : ''); //first name
        $wp_mf_makersql .= ($lastName != '' ? ', last_name  = "' . $lastName . '"' : ''); //last name
        $wp_mf_makersql .= ($bio != '' ? ', bio        = "' . $bio . '"' : ''); //bio
        $wp_mf_makersql .= ($social != '' ? ', social      = "' . $social . '"' : ''); //social
        $wp_mf_makersql .= ($photo != '' ? ', photo      = "' . $photo . '"' : ''); //photo
        $wp_mf_makersql .= ($website != '' ? ', website    = "' . $website . '"' : ''); //website
        $wp_mf_makersql .= ($age_range != '' ? ', age_range  = "' . $age_range . '"' : ''); //age_range
            
        $wpdb->query($wp_mf_makersql);
        //echo $wp_mf_makersql.'<br/>';
        //build maker to entry table
        //  (key is on maker_id, entry_id and maker_type.  if record already exists, no update is needed)
        $wp_mf_maker_to_entity = 
            "INSERT INTO wp_mf_dir_maker_to_entry (maker_id, blog_id, entry_id, maker_type) "
                . ' VALUES ("' . $guid . '",'.$blog_id.',' . $entryID . ',"' . $maker['role'] . '")  '
                . ' ON DUPLICATE KEY UPDATE maker_id="' . $guid . '";';           
        
        //die();
        $wpdb->query($wp_mf_maker_to_entity);
    }
} //end function

//function to build the maker data table to update the wp_mf_dir_maker table
function buildMakerData($lead, $form_id) {    
    global $wpdb;    
    $entry_id = $lead['id'];

    /* set entry information */        
    $main_category =  (isset($lead['320']) ? get_CPT_name($lead['320']) : '');    
    $all_categories = array();

    //Categories (loop through all fields to find the categories)
    foreach ($lead as $leadKey => $leadValue) {
        if (trim($leadValue != '')) {
            //4 additional categories
            $pos = strpos($leadKey, '321');
            if ($pos !== false) {
                $all_categories[] = get_CPT_name($leadValue);
            }
        }
        $pos = strpos($leadKey, '304.');
        if ($pos !== false) {
            if ($leadValue == 'no-public-view')
                return false;
        }
    }

    if($main_category=='' && is_array($all_categories) && !empty($all_categories)) {            
        $main_category = $all_categories[0];
    }

    //verify we only have unique categories
    $all_categories = array_unique($all_categories);

    //faire information   
    global $faire_year; global $faire;    
        
    $project_name = (isset($lead['151']) && trim($lead['151']) != '' ? $lead['151'] : '');    
    $status = (isset($lead['303']) ? $lead['303'] : '');    

    $project_photo = (isset($lead['22']) ? $lead['22'] : '');    
    //find out if there is an override image for this page
    $overrideImg = findOverride($entry_id, 'mtm');
    if ($overrideImg != '')
        $project_photo = $overrideImg;
    
    //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery            
    if(isset($lead['878']) && $lead['878']!=''){
        $project_gallery = json_decode($lead['878']);
        
        if(is_array($project_gallery) && !empty($project_gallery)){
            $project_photo = $project_gallery[0];        
        }        
    }    
    
    /* Entry Array */
    $entryArray = 
        array('entry_id'=>$entry_id, 
            'form_id' => $form_id, 
            'title' => htmlentities($project_name, ENT_QUOTES),  
            'type' => '',    
            'public_desc' => (isset($lead['16']) ? htmlentities($lead['16'], ENT_QUOTES) : ''),
            'project_photo' => $project_photo,            
            'main_category' => $main_category,    
            'category'      => $all_categories,            
            'project_video' => (isset($lead['32']) ? $lead['32'] : ''),
            'inspiration'   => (isset($lead['287']) ? htmlentities($lead['287'], ENT_QUOTES) : ''),            
            'website'       => (isset($lead['27']) ? $lead['27'] : ''),
            'social'       => (isset($lead['828']) ? $lead['828'] : ''),
            'state' => (isset($lead['101.4']) ? $lead['101.4'] : ''), //contact state
            'country' => (isset($lead['101.6']) ? $lead['101.6'] : ''), //contact country
            'faire_name' => $faire,
            'faire_year'    => $faire_year,
            'status' => $status,
            'link'   => get_bloginfo('url').'/maker/entry/'.$entry_id
        );
    /*
     * Build Maker Array
     */

    $makerArray = array();
    //Set Contact Information
    $makerArray[] = array(
        'first_name' => (isset($lead['96.3']) ? $lead['96.3'] : ''),
        'last_name' => (isset($lead['96.6']) ? $lead['96.6'] : ''),
        'bio' => '',
        'email' => (isset($lead['98']) ? $lead['98'] : ''),        
        'social' => '',
        'photo' => '',
        'website' => '',        
        'age_range' => '',        
        'role' => 'contact'
    );

    // First Check if this is a group or one or more makers    
    $isGroup = (isset($lead['105']) && strpos($lead['105'], 'group') !== false ? true : false);                        

    if($isGroup){
        $makerArray[] = array(
            'first_name'    => (isset($lead['109']) ? $lead['109'] : ''),
            'last_name'     => '',
            'bio'           => (isset($lead['110']) ? $lead['110'] : ''),
            'email'         => (isset($lead['98']) ? $lead['98'] : ''), //contact email
            'social'        => '',                                    
            'photo'         => (isset($lead['111']) ? $lead['111'] : ''),
            'website'       => (isset($lead['112']) ? $lead['112'] : ''),
            'age_range'     => (isset($lead['309']) ? $lead['309'] : ''),
            'role'          => 'group'
        );
    }else{ //one or more makers        
        //Maker 1        
        $email = (isset($lead['161'])&&$lead['161']!='' ? $lead['161']:$entry_id.'-maker1@make.co');
        //if email is set, set maker info
        if($email!=''){
            $social = (isset($lead['821']) ? $lead['821'] : (isset($lead['201']) ? $lead['201'] : ''));
            $makerArray[] = array(
                'first_name'    => (isset($lead['160.3']) ? $lead['160.3'] : ''),
                'last_name'     => (isset($lead['160.6']) ? $lead['160.6'] : ''),
                'bio'           => (isset($lead['234']) ? $lead['234'] : ''),
                'email'         => $email,
                'photo'         => (isset($lead['217']) ? $lead['217'] : ''),
                'website'       => (isset($lead['209']) ? $lead['209'] : ''),
                'social'        => $social,                                                    
                'age_range'     => (isset($lead['310']) ? $lead['310'] : ''),
                'role'          => 'maker1'
            );            
        }else{
            if(isset($lead['160.3']) && $lead['160.3']!=''){
                echo 'error!! maker 1 name is set but email is not for entry ' .$entry_id.'<br/>';                
            }
        }

        //Maker 2
        $email = (isset($lead['162']) ? $lead['162'] :$entry_id.'-maker2@make.co');
        
        //if email is set, set maker info
        if($email!=''){
            $social = (isset($lead['822']) ? $lead['822'] : (isset($lead['208']) ? $lead['208'] : ''));
            $makerArray[] = array(
                'first_name'    => (isset($lead['158.3']) ? $lead['158.3'] : ''),
                'last_name'     => (isset($lead['158.6']) ? $lead['158.6'] : ''),
                'bio'           => (isset($lead['258']) ? $lead['258'] : ''),
                'email'         => $email,
                'photo'         => (isset($lead['224']) ? $lead['224'] : ''),
                'website'       => (isset($lead['216']) ? $lead['216'] : ''),
                'social'        => $social,                                                    
                'age_range'     => (isset($lead['311']) ? $lead['311'] : ''),
                'role'          => 'maker2'
            );            
        }else{
            if(isset($lead['158.3']) && $lead['158.3'] !=''){ 
                echo 'error!! maker 2 name is set but email is not for entry ' .$entry_id.'<br/>';                               
            }
        }

        //Maker 3
        $email = (isset($lead['167']) ? $lead['167'] :$entry_id.'-maker3@make.co');
                
        //if email is set, set maker info
        if($email!=''){
            $social = (isset($lead['823']) ? $lead['823'] : (isset($lead['207']) ? $lead['207'] : ''));
            
            $makerArray[] = array(
                'first_name'    => (isset($lead['155.3']) ? $lead['155.3'] : ''),
                'last_name'     => (isset($lead['155.6']) ? $lead['155.6'] : ''),
                'bio'           => (isset($lead['259']) ? $lead['259'] : ''),
                'email'         => $email,
                'photo'         => (isset($lead['223']) ? $lead['223'] : ''),
                'website'       => (isset($lead['215']) ? $lead['215'] : ''),
                'social'        => $social,                                                    
                'age_range'     => (isset($lead['312']) ? $lead['312'] : ''),
                'role'          => 'maker3'
            );  
        }else{
            if(isset($lead['155.3']) && $lead['155.3']!=''){
                echo 'error!! maker 3 name is set but email is not for entry ' .$entry_id.'<br/>';
            }
        }    

        //Maker 4
        $email = (isset($lead['166']) ? $lead['166'] :$entry_id.'-maker4@make.co');
                
        //if email is set, set maker info
        if($email!=''){
            $social = (isset($lead['824']) ? $lead['824'] : (isset($lead['206']) ? $lead['206'] : ''));
            $makerArray[] = array(
                'first_name'    => (isset($lead['156.3']) ? $lead['156.3'] : ''),
                'last_name'     => (isset($lead['156.6']) ? $lead['156.6'] : ''),
                'bio'           => (isset($lead['260']) ? $lead['260'] : ''),
                'email'         => $email,
                'photo'         => (isset($lead['222']) ? $lead['222'] : ''),
                'website'       => (isset($lead['214']) ? $lead['214'] : ''),
                'social'        => $social,                                                    
                'age_range'     => (isset($lead['313']) ? $lead['313'] : ''),
                'role'          => 'maker4'
            );               
        }else{
            if(isset($lead['156.3']) && $lead['156.3']!=''){
                echo 'error!! maker 4 name is set but email is not for entry ' .$entry_id.'<br/>';
            }
        }

        //Maker 5
        $email = (isset($lead['165']) ? $lead['165'] :$entry_id.'-maker5@make.co');
                
        //if email is set, set maker info
        if($email!=''){
            $social = (isset($lead['825']) ? $lead['825'] : (isset($lead['205']) ? $lead['205'] : ''));
            $makerArray[] = array(
                'first_name'    => (isset($lead['157.3']) ? $lead['157.3'] : ''),
                'last_name'     => (isset($lead['157.6']) ? $lead['157.6'] : ''),
                'bio'           => (isset($lead['261']) ? $lead['261'] : ''),
                'email'         => $email,
                'photo'         => (isset($lead['220']) ? $lead['220'] : ''),
                'website'       => (isset($lead['213']) ? $lead['213'] : ''),
                'social'        => $social,                                                    
                'age_range'     => (isset($lead['314']) ? $lead['314'] : ''),
                'role'          => 'maker5'
            );               
        }else{
            if(isset($lead['157.3']) && $lead['157.3']!=''){
                echo 'error!! maker 5 name is set but email is not for entry ' .$entry_id.'<br/>';
            }
        }

        //Maker 6
        $email = (isset($lead['164']) ? $lead['164'] :$entry_id.'-maker6@make.co');
                
        //if email is set, set maker info
        if($email!=''){
            $social = (isset($lead['827']) ? $lead['827'] : (isset($lead['204']) ? $lead['204'] : ''));
            $makerArray[] = array(
                'first_name'    => (isset($lead['159.3']) ? $lead['159.3'] : ''),
                'last_name'     => (isset($lead['159.6']) ? $lead['159.6'] : ''),
                'bio'           => (isset($lead['262']) ? $lead['262'] : ''),
                'email'         => $email,
                'photo'         => (isset($lead['221']) ? $lead['221'] : ''),
                'website'       => (isset($lead['211']) ? $lead['211'] : ''),
                'social'        => $social,                                                    
                'age_range'     => (isset($lead['315']) ? $lead['315'] : ''),
                'role'          => 'maker6'
            );               
        }else{
            if(isset($lead['159.3']) && $lead['159.3']!=''){
                echo 'error!! maker 6 name is set but email is not for entry ' .$entry_id.'<br/>';
            }
        }        

        //Maker 7
        $email = (isset($lead['163']) ? $lead['163'] :$entry_id.'-maker7@make.co');
                
        //if email is set, set maker info
        if($email!=''){
            $social = (isset($lead['826']) ? $lead['826'] : (isset($lead['203']) ? $lead['203'] : ''));
            $makerArray[] = array(
                'first_name'    => (isset($lead['154.3']) ? $lead['154.3'] : ''),
                'last_name'     => (isset($lead['154.6']) ? $lead['154.6'] : ''),
                'bio'           => (isset($lead['263']) ? $lead['263'] : ''),
                'email'         => $email,
                'photo'         => (isset($lead['219']) ? $lead['219'] : ''),
                'website'       => (isset($lead['212']) ? $lead['212'] : ''),
                'social'        => $social,                                                    
                'age_range'     => (isset($lead['316']) ? $lead['316'] : ''),
                'role'          => 'maker7'
            );               
        }else{
            if(isset($lead['154.3']) && $lead['154.3']!=''){
                echo 'error!! maker 7 name is set but email is not for entry ' .$entry_id.'<br/>';
            }
        }                
    }   

    $return = array('maker' => $makerArray, 'entry' => $entryArray);
    return $return;
}