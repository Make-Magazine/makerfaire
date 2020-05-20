<?php
include 'db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$form = '';
if (!isset($_POST['formID'])) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
        </head>
        <body>
            <form method="post" enctype="multipart/form-data">
                What form would you like to export?<br/>
                <input type="text" name="formID" />

                Note: This only works for form 245                
                Export File:<br/>
                <input type="radio" id="male" name="exportType" value="entries">
                <label for="entries">Entries</label><br>
                <input type="radio" id="makers" name="exportType" value="makers">
                <label for="makers">Makers</label><br>                
                <br>
                <input type="submit" value="Export" name="submit">
            </form>
        </body>
    </html>

    <?php
} else {
    //form id must be passed
    $form = (isset($_POST['formID']) ? $_POST['formID'] : '245');
    
    $exportType = (isset($_POST['exportType']) ? $_POST['exportType'] : 'entries');
    if ($form == '') {
        die('form is empty');
    }
    
    if ($exportType == 'makers') {
        $CSVData = exportMFMakers($form);
    } else {
        $CSVData = exportEntries($form);
    }
    
    // output headers so that the file is downloaded rather than displayed
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="exportForm' . $form . '-' . $exportType . '.csv"');

    // do not cache the file
    header('Pragma: no-cache');
    header('Expires: 0');

    // create a file pointer connected to the output stream
    $file = fopen('php://output', 'w');

    // send the column headers
    fputcsv($file, $CSVData['fieldHeaders']);

    //send the data
    foreach ($CSVData['entryData'] as $entryvalue) {
        $output = array();
        fputcsv($file, $entryvalue);
    }

    exit();
}

function exportMFMakers($form) {
    GLOBAL $mysqli;
    $CSVData = array();

    $CSVData['fieldHeaders'] = array('Username', 'Photo', 'Email', 'Full Name', 'Bio', 'Location', 'Interests', 'Role', 'Last login', 'Active');
    //entry data
    $sql = "SELECT wp_gf_entry.id as entry_id, wp_gf_entry.date_created, 
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='303')as status,  
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='369.3'),
                \", \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='369.4'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='369.5'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='369.6')
            ) as maker1_location,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='370.3'),
                \", \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='370.4'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='370.5'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='370.6')
            ) as maker2_location,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='371.3'),
                \", \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='371.4'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='371.5'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='371.6')
            ) as maker3_location,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='372.3'),
                \", \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='372.4'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='372.5'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='372.6')
            ) as maker4_location,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='373.3'),
                \", \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='373.4'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='373.5'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='373.6')
            ) as maker5_location,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='374.3'),
                \", \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='374.4'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='374.5'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='374.6')
            ) as maker6_location,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='375.3'),
                \", \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='375.4'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='375.5'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='375.6')
            ) as maker7_location,            
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='160.3'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='160.6')
                ) as maker1_name,            
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='158.3'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='158.6')
                ) as maker2_name,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='155.3'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='155.6')
                ) as maker3_name,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='156.3'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='156.6')
                ) as maker4_name,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='157.3'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='157.6')
                ) as maker5_name,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='159.3'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='159.6')
                ) as maker6_name,
            concat(
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='154.3'),
                \" \",
                (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='154.6')
                ) as maker7_name,                
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='161')as maker1_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='162')as maker2_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='167')as maker3_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='166')as maker4_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='165')as maker5_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='164')as maker6_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='163')as maker7_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='217')as maker1_photo,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='224')as maker2_photo,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='223')as maker3_photo,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='222')as maker4_photo,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='220')as maker5_photo,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='221')as maker6_photo,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='219')as maker7_photo,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='234')as maker1_bio,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='258')as maker2_bio,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='259')as maker3_bio,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='260')as maker4_bio,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='261')as maker5_bio,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='262')as maker6_bio,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='263')as maker7_bio
            
        from wp_gf_entry
        where wp_gf_entry.form_id = $form
        and wp_gf_entry.status='active'";

    //loop thru entry data
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
    $entryData = array();
    foreach ($entries as $entry) {
        if ($entry['status'] == 'Accepted') {
            for ($i = 1; $i <= 7; $i++) {
                $makerName = 'maker' . $i . '_name';
                $makerEmail = 'maker' . $i . '_email';
                $makerPhoto = 'maker' . $i . '_photo';
                $makerBio = 'maker' . $i . '_bio';
                $makerLocation = 'maker' . $i . '_location';
                if ($entry[$makerEmail] != '') {
                    $CSVData['entryData'][] = array('Username' => '',
                        'Photo' => $entry[$makerPhoto],
                        'Email' => $entry[$makerEmail],
                        'Full Name' => $entry[$makerName],
                        'Bio' => $entry[$makerBio],
                        'Location' => $entry[$makerLocation],
                        'Interests' => '',
                        'Role' => 'Maker',
                        'Last login' => '',
                        'Active' => 'Yes');
                }
            }
        }
    }

    return $CSVData;
}

function exportEntries($form) {
    GLOBAL $mysqli;
    $CSVData = array();

    $catXref = array(21344 => array('3D Printing and Imaging', ''),
        21386 => array('3D Printing and Imaging', 'Additive Manufacturing'),
        21346 => array('Arduino', ''),
        21387 => array('Teachers', ''),
        21349 => array('CAD', ''),
        21351 => array('CNC & Machining', ''),
        21352 => array('Computers & Mobile', ''),
        21388 => array('Health and Bio Hacking', 'Distributed Manufacturing'),
        21358 => array('Emerging Tech', ''),
        21359 => array('Energy & Sustainability', ''),
        21339 => array('Health and Bio Hacking', 'Face Masks'),
        21338 => array('Health and Bio Hacking', 'Face Shields'),
        21366 => array('Health & Biohacking', ''),
        21367 => array('Internet of Things', ''),
        21368 => array('Laser Cutting', ''),
        21389 => array('health and Bio Hacking', ''),
        21372 => array('Other Boards', ''),
        21390 => array('Health and Bio Hacking', 'Process/Distributed Networks'),
        21343 => array('Health and Bio Hacking', 'Protective Suits and Gowns'),
        21376 => array('Raspberry Pi', ''),
        21391 => array('Energy & Sustainability', 'Renewable Energy'),
        21340 => array('Health and Bio Hacking', 'Respirators'),
        21377 => array('Robotics', ''),
        21341 => array('Health and Bio Hacking', 'Sanitation and Sterilization'),
        21392 => array('Energy & Sustainability', 'Sustainable Living'),
        21393 => array('Health and Bio Hacking', ''),
        21342 => array('Health and Bio Hacking', 'Ventilators'),
        21383 => array('Wearables', 'Textiles'),
        21394 => array('Emerging Tech', ''),
        21372 => array('Other Boards', '')
    );

    $CSVData['fieldHeaders'] = array('title', 'created', 'image', 'description', 'owner', 'team', 'categories', 'tags', 'postBody1', 'postBody2', 'postBody3', 'postVideo1', 'postVideo2', 'postBody4', 'status', 'format', 'entryID');
    //entry data
    $sql = "SELECT wp_gf_entry.id as entry_id, wp_gf_entry.date_created, 
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='303' limit 1)as status,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='151' limit 1)as title,            
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='22' limit 1)as image,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='16' limit 1)as description,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='98' limit 1)as owner,    
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='32' limit 1)as video,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='386' limit 1)as video2,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='287' limit 1)as problems_solve,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='123' limit 1)as challenges,                        
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='27' limit 1)as website,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='387' limit 1)as format,
            (select group_concat(meta_value) 
                from wp_gf_entry_meta 
                where wp_gf_entry_meta.entry_id = wp_gf_entry.id and 
                (meta_key like '%321%') )as categories        
        from wp_gf_entry
        where wp_gf_entry.form_id = $form
        and wp_gf_entry.status='active'";

    //loop thru entry data
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
    $entryData = array();
    foreach ($entries as $entry) {
        if(trim($entry['format'])=='Online Exhibit'){
        //if ($entry['status'] == 'Accepted') {
            $entry_id = $entry['entry_id'];
            //build html for team field
            //$team = getMakerInfoNested($entry);
            $team="";
            //gt categories
            $entry_cat = (string) $entry['categories'];
            $categories = explode(',', $entry_cat); //place comma separated list into an array
            //get unique values only
            $categories = array_unique($categories);
            $output_category = array();
            $output_tags = array();
            foreach ($categories as $category) {
                if ($category != '') {
                    if (isset($catXref[$category])) {
                        $catValue = $catXref[$category][0];
                        $tagValue = $catXref[$category][1];
                    } else {
                        die('missing category id ' . $category);
                        $value = htmlspecialchars_decode(get_CPT_name($category));
                        $tagValue = '';
                    }
                    $output_category[] = $catValue;
                    $output_tags[] = $tagValue;
                }
            }
            $output_category = array_unique($output_category);
            $output_tags = array_unique($output_tags);
            
            $collaborators = array();

            $entryData[] = array('title' => $entry['title'],
                'created' => date("l, F n Y - H:i", strtotime($entry['date_created'])),
                'image' => $entry['image'],
                'description' => $entry['website'],
                'owner' => $entry['owner'],
                'team' => $team,
                'categories' => implode(",", $output_category),
                'tags' => implode(",", $output_tags),
                'postBody1' => $entry['description'],
                'postBody2' => $entry['problems_solve'],
                'postBody3' => $entry['challenges'],
                'postVideo1' => $entry['video'],
                'postVideo2' => $entry['video2'],
                'postBody4' => '',
                'status'=>$entry['status'],
                'format'=>$entry['format'],
                'entryID'=>$entry_id);
        }
    }

    $CSVData['entryData'] = $entryData;
    return $CSVData;
}

function cmp($a, $b) {
    return $a["id"] - $b["id"];
}

//return makers info
function getMakerInfo($entry) {
    $makers = array();
    if (isset($entry['gpnf_entry_parent'])&&$entry['gpnf_entry_parent']!='') { //is this a nested form with parent information
        //pull maker information from nested form        
        $makers = getMakerInfoNested($entry);
    } else {
        //pull information from legacy        
        $makers = getMakerInfoLegacy($entry);
    }
    
    return $makers;
}

function createTeam($entry) {
    //format owner@email.com, Team Lead, contributor@email.com, Designer
    $output = "";
    for ($i = 1; $i <= 7; $i++) {
        $makerName = 'maker' . $i . '_name';
        $makerRole = 'maker' . $i . '_role';
        if ($entry[$makerName] != '') {
            if ($output != '')
                $output .= ',';
            $output .= teamInnerContent($entry[$makerName], $entry[$makerRole]);
        }
    }

    return $output;
}

function teamInnerContent($makerName, $makerRole) {
    $output = '';
    $output = '<div class="field-collection-view clearfix view-mode-full">
    <div class="entity entity-field-collection-item field-collection-item-field-maker-memberships clearfix" about="/field-collection/field-maker-memberships/24344" typeof="">
        <div class="content">
            <div class="field field-name-field-team-member field-type-entityreference field-label-above">
                <div class="field-label">Team member name: </div>
                <div class="field-items">
                    <div class="field-item even">' . $makerName . '</div>                
                </div>            
            </div>
            <div class="field field-name-field-membership-role field-type-text field-label-above">
                <div class="field-label">What role did this person play on the project?: </div>
                <div class="field-items">
                    <div class="field-item even">' . $makerRole . '</div>
                </div>
            </div>  
        </div>
    </div>
    <ul class="field-collection-view-links">
        <li class="delete first"><a href="/field-collection/field-maker-memberships/24344/delete?destination=project-data-html.csv">Delete</a></li>
        <li class="edit last"><a href="/field-collection/field-maker-memberships/24344/edit?destination=project-data-html.csv">Edit</a></li>
    </ul>
</div>';
    return $output;
}

function getMakerInfoNested($entry) {
    //format owner@email.com, Team Lead, contributor@email.com, Designer    
    global $entryId;
    $entry = GFAPI::get_entry($entryId);
    //get parent information
    $parent_entry_ID = $entry['gpnf_entry_parent'];
    $parent_entry = GFAPI::get_entry($parent_entry_ID);
    if (is_wp_error($parent_entry)) {
        echo 'there is an error';
        var_dump($parent_entry);
    } else {
        //get maker information    
        $makers = array();

        $child_entryID_array = explode(",", $parent_entry[852]);

        foreach ($child_entryID_array as $child_entryID) {
            if ($child_entryID != $entryId) { //no need to process the entry we are looking at
                $child_entry = GFAPI::get_entry($child_entryID);

                if (!is_wp_error($child_entry) && $child_entry['form_id'] == 246) {                
                    $makers[] = array($child_entry['161'], $child_entry['443']);
                }
            }
        }
    }
   
    return $makers;
}