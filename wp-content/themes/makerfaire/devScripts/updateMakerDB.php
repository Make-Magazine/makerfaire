<?php
include '../../../../wp-load.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$formFilter = (isset($_GET['form']) ? $_GET['form'] : '');
$page = (isset($_GET['page']) ? $_GET['page'] : 1);
$limit = (isset($_GET['limit']) ? $_GET['limit'] : 30);
$offset = ($page != 1 ? (($page - 1) * $limit) : 0);
$yearFilter = (isset($_GET['year']) ? $_GET['year'] : '');

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body>

    <?php
    global $wpdb;

    $blogID = 9999; //we will use blog id of 9999 for flagship    
    $formtable = "wp_gf_form";
    $metatable = 'wp_gf_form_meta';

    //find all forms for this blog that are not trashed
    $formSQL = "SELECT title, formtable.date_created, form_meta.display_meta, form_meta.form_id "
        . " FROM " . $formtable . " formtable "
        . " left outer join " . $metatable . " form_meta on formtable.id=form_meta.form_id "
        . " WHERE is_trash=0 " . ($formFilter != '' ? ' and id=' . $formFilter : '')
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
        } else {
            $faire = get_bloginfo('name');
            $start_dt = 'unknown';
        }


        $fieldData = '';
        $output = true;
        $json = json_decode($formrow['display_meta']);
        $formType = (isset($json->form_type) ? $json->form_type : '');

        //only process Call for Makers forms                        
        if ($formType != 'Master') {
            //echo 'invalid form type<br/>';
            //do not write these records
            continue;
        }

        echo 'Form ' . $formrow['title'] . ' FormID=' . $form_id . ' FormType=' . $formType  . ' Faire Start= ' . $start_dt . '<br/>';

        $entries = GFAPI::get_entries($form_id, array('status' => 'active'), null, array('offset' => 0, 'page_size' => 999));

        if (count($entries) > 999) {
            echo 'WARNING!! More than 999 entries found. Total found = ' . count($entries) . '<br/>';
            die('mission aborted');
        }
        echo '&emsp;found ' . count($entries) . ' entries<br/>';

        $written_count = 0;
        $entry_write_count = 0;
        //loop through entries
        foreach ($entries as $entry) {
            $exhibit_type = array();
            foreach ($entry as $key => $value) {
                if (isset($key) && strpos($key, '339.') === 0) {
                    if (isset($value) && $value != '') {
                        if (stripos($value, 'sponsor') !== false) {
                            $exhibit_type[$key] = 'Exhibit';
                        } else {
                            $exhibit_type[$key] = $value;
                        }
                    }
                }
            }

            //$exhibit_type = array_unique($exhibit_type);

            //do not write Show Management and invalid exhibit types
            if (in_array('Show Management', $exhibit_type) || in_array('Not Sure Yet', $exhibit_type) || in_array('Other', $exhibit_type)) {
                //do not write these records
                //echo 'Invalid Exhibit Type - '.implode("|",$exhibit_type) . '<br/>';                    
                continue;
            }

            //faire year is based on the date the entry was created
            $datetime = new DateTime($entry['date_created']);
            $faire_month = $datetime->format('m');
            $faire_year = $datetime->format('Y');

            if ($faire_month >= 11) {
                $faire_year++;
            }

            //only pull entries for provided year
            if ($yearFilter != '' && $faire_year != $yearFilter) {
                //echo '$faire_year is '.$faire_year.'<Br/>';
                continue;
            }

            //if status is not set, chances are this is not a valid cfm entry
            if (isset($entry['303'])) {
                $written_count = $written_count + 1;
                updateMakerTables((array) $entry, $form_id, $blogID);
            } else {
                //echo 'no status set????<br/>';
                //var_dump($entry);
                die();
            }
        }
        echo '&emsp;wrote ' . $written_count . ' entries<br/>';
        echo '&emsp;wrote ' . $entry_write_count . ' entries<br/>';

        // die('check entries');            
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
    global $entry_write_count;

    $entryID = $entry['id'];
    //echo 'adding entry id '.$entryID.'<br/>'; 

    //build Maker and Entry Data Array
    $data = buildMakerData($entry, $form_id);

    //if this maker is marked as do not display, exit
    if (!$data) {
        //echo 'no data found for '.$entry['id'].'<br/>';
        //var_dump($entry);
        //die();
        return;
    }
    $makerData  = $data['maker'];
    $entryData  = $data['entry'];

    $categories = (is_array($entryData['category']) ? implode('|', $entryData['category']) : '');

    if ($entryData['title'] == '') {
        echo 'Danger Will Robinson!! No Project Title set. Abort! Abort!! ';
        //echo 'blog id = '.$blog_id.", entry id= " . $entryID . ", form id = " . $entryData['form_id'] .'<br/>';        
        return;
    }

    /*
     * Update Entry Table - wp_mf_dir_entry
     * Fields - blog_id, entry_id, form_id, title, type, public_desc, project_photo, main_category, category, project_video, inspiration, website, social, state, country, faire_name, faire_year, status, last_change_date,      
     */
    $wp_mf_entitysql = "insert into wp_mf_dir_entry (blog_id, entry_id, form_id, title, type, public_desc, project_photo, main_category, category, project_video, inspiration, website, social, state, country, faire_name, faire_year, status, entry_link, last_change_date) "
        . " VALUES (" . $blog_id . ", " . $entryID . ", " . $entryData['form_id'] . ", "
        . " '" . $entryData['title']            . "', "
        . " '" . $entryData['type']             . "', "
        . " '" . $entryData['public_desc']      . "', "
        . " '" . $entryData['project_photo']    . "', "
        . " '" . $entryData['main_category']    . "', "
        . " '" . $categories                    . "', "
        . " '" . $entryData['project_video']    . "', "
        . " '" . $entryData['inspiration']      . "', "
        . " '" . $entryData['website']          . "', "
        . " '" . $entryData['social']           . "', "
        . " '" . $entryData['state']            . "', "
        . " '" . $entryData['country']          . "', "
        . " '" . $entryData['faire_name']       . "', "
        . " '" . $entryData['faire_year']       . "', "
        . " '" . $entryData['status']           . "', "
        . " '" . $entryData['link']             . "', now()) "
        . " ON DUPLICATE KEY UPDATE title           = '" . $entryData['title']          . "', "
        . "                         type            = '" . $entryData['type']           . "', "
        . "                         public_desc     = '" . $entryData['public_desc']    . "', "
        . "                         project_photo   = '" . $entryData['project_photo']  . "', "
        . "                         main_category   = '" . $entryData['main_category']  . "', "
        . "                         category        = '" . $categories                  . "', "
        . "                         project_video   = '" . $entryData['project_video']  . "', "
        . "                         inspiration     = '" . $entryData['inspiration']    . "', "
        . "                         website         = '" . $entryData['website']        . "', "
        . "                         social          = '" . $entryData['social']         . "', "
        . "                         state           = '" . $entryData['state']          . "', "
        . "                         country         = '" . $entryData['country']        . "', "
        . "                         faire_name      = '" . $entryData['faire_name']     . "', "
        . "                         faire_year      = '" . $entryData['faire_year']     . "', "
        . "                         status          = '" . $entryData['status']         . "', "
        . "                         entry_link      = '" . $entryData['link']           . "', "
        . "                         last_change_date    = now()";

    $wpdb->query($wp_mf_entitysql);
    $entry_write_count = $entry_write_count + 1;
    //echo $entryID.'|'.$entryData['form_id'].'|'.$blog_id.'<br/>';

    //echo $wp_mf_entitysql.'<br/>';
    /*  Update Maker Table - wp_mf_dir_maker table
        Fields: email, first_name, last_name, bio, social, photo, website, maker_id, last_change_date
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

        // maker db 
        /*  GUID
        * If this maker is already in the DB - pull the maker_id, else let's create one
        */
        $results = $wpdb->get_results("SELECT wp_mf_dir_maker.maker_id
                                        FROM wp_mf_dir_maker
                                        right outer join wp_mf_dir_maker_to_entry
                                            on wp_mf_dir_maker_to_entry.maker_id=wp_mf_dir_maker.maker_id 
                                            and blog_id=" . $blog_id . " 
                                            and entry_id=" . $entryID . " and maker_type='" . $maker['role'] . "'
                                        where email = '" . $email . "'");

        $guid = ($wpdb->num_rows != 0 ? $guid = $results[0]->maker_id : createGUID($blog_id . $entryID . $maker['role']));


        $wp_mf_makersql = "INSERT INTO `wp_mf_dir_maker`"
            . " (email, first_name, last_name, bio, social, photo, website, maker_id, last_change_date) "
            . '  VALUES ("' . $email . '","' . $firstName . '","' . $lastName . '","' . $bio . '","' . $social . '",'
            . '          "' . $photo . '","' . $website . '", "' . $guid . '", now())'
            . '  ON DUPLICATE KEY UPDATE maker_id="' . $guid . '", last_change_date=now()';

        //only update non blank fields
        $wp_mf_makersql .= ($firstName != '' ? ', first_name = "' . $firstName . '"' : ''); //first name
        $wp_mf_makersql .= ($lastName != '' ? ', last_name  = "' . $lastName . '"' : ''); //last name
        $wp_mf_makersql .= ($bio != '' ? ', bio        = "' . $bio . '"' : ''); //bio
        $wp_mf_makersql .= ($social != '' ? ', social      = "' . $social . '"' : ''); //social
        $wp_mf_makersql .= ($photo != '' ? ', photo      = "' . $photo . '"' : ''); //photo
        $wp_mf_makersql .= ($website != '' ? ', website    = "' . $website . '"' : ''); //website        

        $wpdb->query($wp_mf_makersql);
        //echo $wp_mf_makersql.'<br/>';
        //build maker to entry table
        //  (key is on maker_id, entry_id and maker_type.  if record already exists, no update is needed)
        $wp_mf_maker_to_entity =
            "INSERT INTO wp_mf_dir_maker_to_entry (maker_id, blog_id, entry_id, maker_type) "
            . ' VALUES ("' . $guid . '",' . $blog_id . ',' . $entryID . ',"' . $maker['role'] . '")  '
            . ' ON DUPLICATE KEY UPDATE maker_id="' . $guid . '";';

        //die();
        $wpdb->query($wp_mf_maker_to_entity);
    }
} //end function

//function to build the maker data table to update the wp_mf_dir_maker table
function buildMakerData($entry, $form_id) {
    $entry_id = $entry['id'];

    /* set entry information */
    $main_category =  (isset($entry['320']) ? get_CPT_name($entry['320']) : '');
    $all_categories = array();

    //Categories (loop through all fields to find the categories)
    foreach ($entry as $entryKey => $entryValue) {
        if (trim($entryValue != '')) {
            //4 additional categories
            $pos = isset($entryKey) ? strpos($entryKey, '321') : false;
            if ($pos !== false) {
                $all_categories[] = get_CPT_name($entryValue);
            }
        }
        $pos = isset($entryKey) ? strpos($entryKey, '304.') : false;
        if ($pos !== false) {
            if ($entryValue == 'no-public-view') {
                //echo 'no-public-view set for entry '.$entry_id.'<br/>';
                return false;
            }
        }
    }

    if ($main_category == '' && is_array($all_categories) && !empty($all_categories)) {
        $main_category = $all_categories[0];
    }

    //verify we only have unique categories
    $all_categories = array_unique($all_categories);

    //faire information   
    global $faire_year;
    global $faire;

    $project_name = (isset($entry['151']) && trim($entry['151']) != '' ? $entry['151'] : '');
    $status = (isset($entry['303']) ? $entry['303'] : '');

    $project_photo = (isset($entry['22']) ? $entry['22'] : '');
    //for BA24, the single photo was changed to a multi image which messed things up a bit
    $photo = json_decode($project_photo);
    if (is_array($photo)) {
        $project_photo = $photo[0];
    }

    // this returns an array of image urls from the additional images field
    $project_gallery = (isset($entry['878']) ? json_decode($entry['878']) : '');

    //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery
    if ($project_photo == '' && is_array($project_gallery) && !empty($project_gallery)) {
        $project_photo = $project_gallery[0];
    }

    global $exhibit_type;
    /* Entry Array */
    $entryArray =
        array(
            'entry_id'    => $entry_id,
            'form_id'       => $form_id,
            'title'         => htmlentities($project_name, ENT_QUOTES),
            'type'          => implode("|", $exhibit_type),
            'public_desc'   => (isset($entry['16']) ? htmlentities(addslashes($entry['16']), ENT_QUOTES) : ''),
            'project_photo' => $project_photo,
            'main_category' => $main_category,
            'category'      => $all_categories,
            'project_video' => (isset($entry['32']) ? $entry['32'] : ''),
            'inspiration'   => (isset($entry['287']) ? htmlentities(addslashes($entry['287']), ENT_QUOTES) : ''),
            'website'       => (isset($entry['27']) ? $entry['27'] : ''),
            'social'        => (isset($entry['906']) ? addslashes($entry['906']) : ''),
            'state'         => (isset($entry['101.4']) ? addslashes($entry['101.4']) : ''), //contact state
            'country'       => (isset($entry['101.6']) ? addslashes($entry['101.6']) : ''), //contact country
            'faire_name'    => $faire,
            'faire_year'    => $faire_year,
            'status'        => $status,
            'link'          => get_bloginfo('url') . '/maker/entry/' . $entry_id
        );

    /*
     * Build Maker Array
     */

    $makerArray = array();

    // Maker or Group
    $displayType = (isset($entry['105']) ? $entry['105'] : '');

    $isGroup = false;
    $isGroup = (stripos($displayType, 'group') !== false || stripos($displayType, 'team') !== false ? true : false);


    //pull group or maker info
    if ($isGroup) {
        $groupname  = (isset($entry['109']) ? $entry['109'] : '');
        $groupphoto = '';        
        if (isset($entry['111']) && $entry['111'] != '') {
            $groupphoto = $entry['111'];
        } elseif (isset($entry['217']) && $entry['217'] != '') {
            $groupphoto = $entry['217'];
        }

        //for BA24, the single photo was changed to a multi image which messed things up a bit
        $photo = json_decode($groupphoto);
        if (is_array($photo) && !empty($photo)) {
            $groupphoto = $photo[0];
        }

        $groupbio = (isset($entry['110']) ? $entry['110'] : '');
        $groupsocial = isset($entry['828']) ? $entry['828'] : '';
        $groupwebsite = isset($entry['112']) ? $entry['112'] : '';
        $makerArray = array(array(
            'first_name' => $groupname,
            'last_name'  => null,
            'bio'       => preg_replace('/\\\\["\']/', '"', $groupbio),
            'email'     => (isset($entry['98']) ? $entry['98'] : ''),
            'photo'     => $groupphoto,
            'social'    => $groupsocial,
            'website'   => $groupwebsite,
            'role'      => 'group'
        ));
    } else {
        // deal with the maker photo possibly being a multi image
        $makerphoto = (isset($entry['217']) && $entry['217'] != '') ? $entry['217'] : "";
        $photo      = json_decode($makerphoto);
        if (is_array($photo) && !empty($photo)) {
            $makerphoto = $photo[0];
        }

        $makerArray = array(array(
            'first_name' => (isset($entry['96.3']) ? $entry['96.3'] : ''),
            'last_name'  => (isset($entry['96.6']) ? $entry['96.6'] : ''),
            'bio'       => (isset($entry['234']) ? $entry['234'] : ''),
            'email'     => (isset($entry['98']) ? $entry['98'] : ''),
            'photo'     => $makerphoto,
            'social'    => isset($entry['821']) ? $entry['821'] : '',
            'website'   => (isset($entry['209']) ? $entry['209'] : ''),
            'role'          => 'maker'
        ));
    }        

    $return = array('maker' => $makerArray, 'entry' => $entryArray);
    return $return;
}
