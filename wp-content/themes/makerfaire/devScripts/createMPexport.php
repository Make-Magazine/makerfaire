<?php

include 'db_connect.php';
//check that the request is valid
/*
  $auth = (isset($_GET['auth'])?$_GET['auth']:'');
  if($auth==''){
  exit();
  }
  //create a crypt key to pass to entriesExport.php to avoid outside from accessing
  $date  = date('mdY');
  $crypt = crypt($date, AUTH_SALT);
  if($auth != $crypt){
  exit();
  } */
$form = (isset($_GET['formID']) ? $_GET['formID'] : '');
if ($form == '') {
    die('You must submit a form ID to export. Example - ?formID=123');
}

// output headers so that the file is downloaded rather than displayed
header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="exportForm' . $form . '.csv"');

// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');

$fieldHeaders = array('title', 'path', 'created', 'teaser', 'image', 'difficulty', 'duration', 'visibility', 'Video', 'ah-ha', 'uh-oh', 'story', 'howto', 'Maker Category', 'tools', 'materials', 'boardsKits', 'resources', 'team', 'collaborators', 'Public');


//entry data
$sql = "SELECT wp_gf_entry.id as entry_id, wp_gf_entry.date_created, 
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='303')as status,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='151')as title,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='319')as teaser,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='22')as photo,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='32')as video,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='16')as short_desc,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='287')as inspire,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='27')as website,
            (select group_concat(meta_value) 
                from wp_gf_entry_meta 
                where wp_gf_entry_meta.entry_id = wp_gf_entry.id and 
                (meta_key like '%320%'or meta_key like '%321%') )as categories,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='160')as maker1_name,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='443')as maker1_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='158')as maker2_name,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='444')as maker2_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='155')as maker3_name,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='445')as maker3_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='156')as maker4_name,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='446')as maker4_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='157')as maker5_name,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='447')as maker5_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='159')as maker6_name,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='448')as maker6_role,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='154')as maker6_name,
            (select meta_value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key ='449')as maker6_role,
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
    if ($entry['status'] == 'Accepted') {
        $entry_id = $entry['entry_id'];
        $team = createTeam($entry);
        $collaborators = array();
        if($entry['maker1_email']!='')  $collaborators[$entry['maker1_email']];
        if($entry['maker2_email']!='')  $collaborators[$entry['maker2_email']];
        if($entry['maker3_email']!='')  $collaborators[$entry['maker3_email']];
        if($entry['maker4_email']!='')  $collaborators[$entry['maker4_email']];
        if($entry['maker5_email']!='')  $collaborators[$entry['maker5_email']];
        if($entry['maker6_email']!='')  $collaborators[$entry['maker6_email']];
        if($entry['maker7_email']!='')  $collaborators[$entry['maker7_email']];
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
            'category' => 'Primary and Secondary Category (Field 320 and Field 321)',
            'tools' => '',
            'materials' => '',
            'boardsKits' => '',
            'resources' => '',
            'team' => $team,
            'collaborators' => implode(",",$collaborators),
            'Public' => 'Yes');
    }
    
    /*
    $fieldNum = (string) $entry['meta_key'];
    //field 302 and 320 is stored as category number, use cross reference to find text value
    if ($fieldNum == '320' || strpos($fieldNum, '321.') !== false || strpos($fieldNum, '302.') !== false) {
        $value = get_CPT_name($entry['value']);
    } else {
        $value = $entry['value'];
    }
    $value = htmlspecialchars_decode($value);
    $entryData[$entry['entry_id']][$fieldNum] = $value;*/
}

// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');

// send the column headers
fputcsv($file, $fieldHeaders);

//send the data
foreach ($entryData as $entryvalue) {
    $output = array();    
    fputcsv($file, $entryvalue);
}

exit();

function cmp($a, $b) {
    return $a["id"] - $b["id"];
}

function createTeam($entry) {    
    $output = 
    "<div class='field-collection-view clearfix view-mode-full'>
        <div class='entity entity-field-collection-item field-collection-item-field-maker-memberships clearfix' typeof=''>";
    if(isset($entry['maker1_name'])&&$entry['maker1_name']!=''){
        $output .= "<div class='content'>
            <div class='field field-name-field-team-member field-type-entityreference field-label-above'>
                <div class='field-label'>Team member name:</div>
                <div class='field-items'>
                    <div class='field-item even'>".$entry['maker1_name']."</div>                        
                </div>                    
            </div>
            <div class='field field-name-field-membership-role field-type-text field-label-above'>
                <div class='field-label'>What role did this person play on the project?:</div>
                <div class='field-items'>
                    <div class='field-item even'>".(isset($entry['maker1_role'])&&$entry['maker1_role']!=''?$entry['maker1_role']:'Maker 1')."</div>                        
                </div>
            </div>  
        </div>";
    }
    //Maker 2
    if(isset($entry['maker2_name'])&&$entry['maker2_name']!=''){
        $output .= "<div class='content'>
            <div class='field field-name-field-team-member field-type-entityreference field-label-above'>
                <div class='field-label'>Team member name:</div>
                <div class='field-items'>
                    <div class='field-item even'>".$entry['maker2_name']."</div>                        
                </div>                    
            </div>
            <div class='field field-name-field-membership-role field-type-text field-label-above'>
                <div class='field-label'>What role did this person play on the project?:</div>
                <div class='field-items'>
                    <div class='field-item even'>".(isset($entry['maker2_role'])&&$entry['maker2_role']!=''?$entry['maker2_role']:'Maker 2')."</div>                        
                </div>
            </div>  
        </div>";
    }    
    //Maker 3
    if(isset($entry['maker3_name'])&&$entry['maker3_name']!=''){
        $output .= "<div class='content'>
            <div class='field field-name-field-team-member field-type-entityreference field-label-above'>
                <div class='field-label'>Team member name:</div>
                <div class='field-items'>
                    <div class='field-item even'>".$entry['maker3_name']."</div>                        
                </div>                    
            </div>
            <div class='field field-name-field-membership-role field-type-text field-label-above'>
                <div class='field-label'>What role did this person play on the project?:</div>
                <div class='field-items'>
                    <div class='field-item even'>".(isset($entry['maker3_role'])&&$entry['maker3_role']!=''?$entry['maker3_role']:'Maker 3')."</div>                        
                </div>
            </div>  
        </div>";
    }        
    //Maker 4
    if(isset($entry['maker4_name'])&&$entry['maker4_name']!=''){
        $output .= "<div class='content'>
            <div class='field field-name-field-team-member field-type-entityreference field-label-above'>
                <div class='field-label'>Team member name:</div>
                <div class='field-items'>
                    <div class='field-item even'>".$entry['maker4_name']."</div>                        
                </div>                    
            </div>
            <div class='field field-name-field-membership-role field-type-text field-label-above'>
                <div class='field-label'>What role did this person play on the project?:</div>
                <div class='field-items'>
                    <div class='field-item even'>".(isset($entry['maker4_role'])&&$entry['maker4_role']!=''?$entry['maker4_role']:'Maker 4')."</div>                        
                </div>
            </div>  
        </div>";
    }            
    //Maker 5
    if(isset($entry['maker5_name'])&&$entry['maker5_name']!=''){
        $output .= "<div class='content'>
            <div class='field field-name-field-team-member field-type-entityreference field-label-above'>
                <div class='field-label'>Team member name:</div>
                <div class='field-items'>
                    <div class='field-item even'>".$entry['maker5_name']."</div>                        
                </div>                    
            </div>
            <div class='field field-name-field-membership-role field-type-text field-label-above'>
                <div class='field-label'>What role did this person play on the project?:</div>
                <div class='field-items'>
                    <div class='field-item even'>".(isset($entry['maker5_role'])&&$entry['maker5_role']!=''?$entry['maker5_role']:'Maker 5')."</div>                        
                </div>
            </div>  
        </div>";
    }            
    //Maker 6
    if(isset($entry['maker6_name'])&&$entry['maker6_name']!=''){
        $output .= "<div class='content'>
            <div class='field field-name-field-team-member field-type-entityreference field-label-above'>
                <div class='field-label'>Team member name:</div>
                <div class='field-items'>
                    <div class='field-item even'>".$entry['maker6_name']."</div>                        
                </div>                    
            </div>
            <div class='field field-name-field-membership-role field-type-text field-label-above'>
                <div class='field-label'>What role did this person play on the project?:</div>
                <div class='field-items'>
                    <div class='field-item even'>".(isset($entry['maker6_role'])&&$entry['maker6_role']!=''?$entry['maker6_role']:'Maker 6')."</div>                        
                </div>
            </div>  
        </div>";
    }                
    //Maker 7
    if(isset($entry['maker7_name'])&&$entry['maker7_name']!=''){
        $output .= "<div class='content'>
            <div class='field field-name-field-team-member field-type-entityreference field-label-above'>
                <div class='field-label'>Team member name:</div>
                <div class='field-items'>
                    <div class='field-item even'>".$entry['maker7_name']."</div>                        
                </div>                    
            </div>
            <div class='field field-name-field-membership-role field-type-text field-label-above'>
                <div class='field-label'>What role did this person play on the project?:</div>
                <div class='field-items'>
                    <div class='field-item even'>".(isset($entry['maker7_role'])&&$entry['maker7_role']!=''?$entry['maker7_role']:'Maker 7')."</div>                        
                </div>
            </div>  
        </div>";
    }                
    $output .= "
        </div>
    </div>";
   return $output;
}