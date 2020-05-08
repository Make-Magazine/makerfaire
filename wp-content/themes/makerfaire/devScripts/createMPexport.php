<?php
include 'db_connect.php';
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
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
                <br/>

                Export File:<br/>
                <input type="radio" id="male" name="exportType" value="entries">
                <label for="entries">Entries</label><br>
                <input type="radio" id="makers" name="exportType" value="makers">
                <label for="makers">Makers</label><br>
                <br/>
                <input type="submit" value="Export" name="submit">
            </form>
        </body>
    </html>

    <?php
} else {
    //form id must be passed
    $form = (isset($_POST['formID']) ? $_POST['formID'] : '');
    $exportType = (isset($_POST['exportType']) ? $_POST['exportType'] : 'entries');
    if ($form == '') {
        die();
    }
    
    if ($exportType == 'makers') {
        $CSVData = exportMFMakers($form);
    } else {
        $CSVData = exportEntries($form);
    }

    // output headers so that the file is downloaded rather than displayed
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="exportForm' . $form . '-'.$exportType.'.csv"');

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
    //define category xref
    $catXref = array(
        20637 => '3D Printing and Imaging',
        20638 => '3D Printing and Imaging',
        20639 => 'Energy & Sustainability',
        20640 => 'Arduino',
        20641 => 'Art & Sculpture',
        21319 => 'Emerging Tech',
        21318 => 'Emerging Tech',
        20642 => 'Bikes',
        20643 => 'Teachers',
        21328 => 'Energy & Sustainability',
        20644 => 'Computers & Mobile',
        21297 => 'Cosplay',
        20645 => 'Crafting',
        20646 => 'Drones',
        20647 => 'Teachers',
        20649 => 'Other Boards',
        20650 => 'Teachers',
        20651 => 'Teachers',
        20652 => 'Wearables',
        20653 => 'Planes',
        20654 => 'Food & Beverage',
        20655 => 'Fun & Games',
        20656 => 'Gaming',
        20657 => 'Teachers',
        20658 => 'Health & Biohacking',
        20659 => 'Connected Home',
        21233 => 'Internet of Things',
        20660 => 'Students',
        20661 => 'Students',
        20662 => 'Art & Sculpture',
        20663 => 'Teachers',
        20664 => 'Teachers',
        20665 => 'Other Boards',
        20666 => 'Music',
        20667 => 'Computers & Mobile',
        20668 => 'Photography & Video',
        20669 => 'Raspberry Pi',
        20670 => 'Robotics',
        20671 => 'Rockets',
        20672 => 'Teachers',
        21321 => 'Space',
        20673 => 'Energy & Sustainability',
        20648 => 'Cars',
        21235 => 'Emerging Tech',
        20674 => 'Wearables',
        20675 => 'Woodworking',
        20676 => 'Students',
    );

    $CSVData['fieldHeaders'] = array('title', 'path', 'created', 'teaser', 'image', 'difficulty', 'duration', 'visibility', 'Video', 'ah-ha', 'uh-oh', 'story', 'howto', 'Maker Category', 'tools', 'materials', 'boardsKits', 'resources', 'team', 'owner', 'Public');

    //entry data
    $sql = "SELECT wp_gf_entry.id as entry_id, wp_gf_entry.date_created, 
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='303')as status,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='151')as title,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='319')as teaser,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='22')as image,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='32')as video,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='16')as short_desc,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='287')as inspire,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='27')as website,
            (select group_concat(meta_value) 
                from wp_gf_entry_meta 
                where wp_gf_entry_meta.entry_id = wp_gf_entry.id and 
                (meta_key like '%320%'or meta_key like '%321%') )as categories,
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
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='443')as maker1_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='444')as maker2_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='445')as maker3_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='446')as maker4_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='447')as maker5_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='448')as maker6_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='449')as maker7_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='161')as maker1_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='162')as maker2_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='167')as maker3_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='166')as maker4_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='165')as maker5_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='164')as maker6_email,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='163')as maker7_email
        from wp_gf_entry
        where wp_gf_entry.form_id = $form
        and wp_gf_entry.status='active'";

//loop thru entry data
    $entries = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
    $entryData = array();
    foreach ($entries as $entry) {
        if ($entry['status'] == 'Accepted' &&$entry['maker1_email']!='') {
            $entry_id = $entry['entry_id'];
            //build html for team field
            $team = createTeam($entry);

            //gt categories
            $entry_cat = (string) $entry['categories'];
            $categories = explode(',', $entry_cat); //place comma separated list into an array
            //get unique values only
            $categories = array_unique($categories);
            $output_category = array();
            ;
            foreach ($categories as $category) {
                if (isset($catXref[$category])) {
                    $value = $catXref[$category];
                } else {
                    die('missing category id ' . $category);
                    $value = htmlspecialchars_decode(get_CPT_name($category));
                }
                $output_category[] = $value;
            }

            $collaborators = array();
            if ($entry['maker1_email'] != '')
                $collaborators[] = $entry['maker1_email'];
            if ($entry['maker2_email'] != '')
                $collaborators[] = $entry['maker2_email'];
            if ($entry['maker3_email'] != '')
                $collaborators[] = $entry['maker3_email'];
            if ($entry['maker4_email'] != '')
                $collaborators[] = $entry['maker4_email'];
            if ($entry['maker5_email'] != '')
                $collaborators[] = $entry['maker5_email'];
            if ($entry['maker6_email'] != '')
                $collaborators[] = $entry['maker6_email'];
            if ($entry['maker7_email'] != '')
                $collaborators[] = $entry['maker7_email'];


            $entryData[] = array('title' => $entry['title'],
                'path' => 'https://makerfaire.com/maker/entry/' . $entry_id . '/',
                'created' => $entry['date_created'],
                'teaser' => ($entry['teaser'] != '' ? 'At my exhibit, you can learn how to ' . $entry['teaser'] : ''),
                'image' => $entry['image'],
                'difficulty' => '',
                'duration' => '',
                'visibility' => 'Public',
                'video' => $entry['video'],
                'ahha' => '',
                'uhoh' => '',
                'story' => '<div><div>' . $entry['short_desc'] . '</div>' .
                ($entry['inspire'] != '' ? '<div>Project Inspiration: ' . $entry['inspire'] . '</div>' : '') .
                ($entry['website'] != '' ? '<div><a href="' . $entry['website'] . '">Project Website</a></div>' : '') .
                '</div>',
                'howto' => '',
                'category' => implode(",", $output_category),
                'tools' => '',
                'materials' => '',
                'boardsKits' => '',
                'resources' => '',
                'team' => $team,
                'collaborators' => $entry['maker1_email'],
                'Public' => 'Yes');
        }
    }
    $CSVData['entryData'] = $entryData;
    return $CSVData;
}

function cmp($a, $b) {
    return $a["id"] - $b["id"];
}

function createTeam($entry) {
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
