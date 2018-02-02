<?php
/**
 * Template Name: Entry
 *
 * @version 2.0
 */

global $wp_query;
$entryId   = $wp_query->query_vars['e_id'];
$editEntry = $wp_query->query_vars['edit_slug'];
$entry     = GFAPI::get_entry($entryId);

$sharing_cards = new mf_sharing_cards();

//entry not found
if(isset($entry->errors)){
  $form_id = '';
  $formType = '';
  $entry=array();
  $faire = '';
}else{
  //find out which faire this entry is for to set the 'look for more makers link'
  $form_id = $entry['form_id'];
  $form = GFAPI::get_form($form_id);
  $formType = $form['form_type'];
  //build an array of field information
  foreach($form['fields'] as $field){
    $fieldID = $field->id;
    $fieldData[$fieldID] = $field;
  }

  //if the form was submitted
  $submit = filter_input(INPUT_POST, 'edit_entry_page');
  if($submit != ''){
    entryPageSubmit($entryId);
    $entry         = GFAPI::get_entry($entryId);
  }

  $faire =$slug=$faireID=$show_sched=$faireShort = $faire_end='';
  if($form_id!=''){
    $formSQL = "select replace(lower(faire_name),' ','-') as faire_name, faire, id,show_sched,start_dt, end_dt, url_path, faire_map, program_guide "
            . " from wp_mf_faire where FIND_IN_SET ($form_id, wp_mf_faire.form_ids)> 0";

    $results =  $wpdb->get_row( $formSQL );
    if($wpdb->num_rows > 0){
      $faire          = $slug = $results->faire_name;
      $faireShort     = $results->faire;
      $faireID        = $results->id;
      $show_sched     = $results->show_sched;
      $faire_start    = $results->start_dt;
      $faire_end      = $results->end_dt;
      $url_sub_path   = $results->url_path;
      $faire_map      = $results->faire_map;
      $program_guide  = $results->program_guide;
    }
  }

  //get makers info
  $makers = getMakerInfo($entry);

  $groupname  = (isset($entry['109']) ? $entry['109']:'');
  $groupphoto = (isset($entry['111']) ? $entry['111']:'');
  $groupbio   = (isset($entry['110']) ? $entry['110']:'');

  // One maker
  // A list of makers (7 max)
  // A group or association
  $displayType = (isset($entry['105']) ? $entry['105']:'');

  $isGroup = $isList = $isSingle = false;
  $isGroup =(strpos($displayType, 'group') !== false);
  $isList =(strpos($displayType, 'list') !== false);
  $isSingle =(strpos($displayType, 'One') !== false);


  $project_name = (isset($entry['151']) ? $entry['151'] : '');  //Change Project Name
  $project_photo = (isset($entry['22']) ? legacy_get_fit_remote_image_url($entry['22'],750,500) : '');
  $project_short = (isset($entry['16']) ? $entry['16']: '');    // Description
  $project_website = (isset($entry['27']) ? $entry['27']: '');  //Website
  $project_video = (isset($entry['32']) ? $entry['32']:'');     //Video
  $project_title = (isset($entry['151'])?(string)$entry['151']:''); //Title
  $project_title  = preg_replace('/\v+|\\\[rn]/','<br/>',$project_title);
}

//set sharing card data
if(is_array($entry) && isset($entry['status']) && $entry['status']=='active' && isset($entry[303]) && $entry[303]=='Accepted'){
  $sharing_cards->project_short = $project_short;
  $sharing_cards->project_photo = $project_photo;
  $sharing_cards->project_title = $project_title;
}else{
  $sharing_cards->project_title = 'Invalid Entry';
  $sharing_cards->project_photo = '';
  $sharing_cards->project_short = '';
}

//Url
global $wp;
$canonical_url = home_url( $wp->request ) . '/' ;
$sharing_cards->canonical_url = $canonical_url;

$sharing_cards->set_values();
get_header();


/* Lets check if we are coming from the MAT tool -
 * if we are, and user is logged in and has access to this record
 *   Display edit functionality
 */
$makerEdit = false;
if($editEntry=='edit'){
  //check if loggest in user has access to this entry
  $current_user = wp_get_current_user();

  //require_once our model
  require_once( get_template_directory().'/models/maker.php' );

  //instantiate the model
  $maker   = new maker($current_user->user_email);
  if($maker->check_entry_access($entry)){
    $makerEdit =  true;
  }
}

 //check if this entry has won any awards
$ribbons = checkForRibbons(0,$entryId);

//set the 'backlink' text and link (only set on valid entries)
if($faire!=''){
  $url = parse_url(wp_get_referer()); //getting the referring URL
  $url['path'] = rtrim($url['path'], "/"); //remove any trailing slashes
  $path = explode("/", $url['path']); // splitting the path
  $slug = end($path); // get the value of the last element

  if($slug=='schedule'){
    $backlink = wp_get_referer();
    $backMsg = '<i class="fa fa-angle-left fa-lg" aria-hidden="true"></i> Back to the Schedule';
  }else{
    $backlink = "/".$url_sub_path."/meet-the-makers/";
    $backMsg = '<i class="fa fa-angle-left fa-lg" aria-hidden="true"></i> Look for More Makers';
  }

  //overwrite the backlink to send makers back to MAT if $makerEdit = true
  if($makerEdit){
    $backlink = "/manage-entries/";
    $backMsg = '<i class="fa fa-angle-left fa-lg" aria-hidden="true"></i> Back to Your Maker Faire Portal';
  }
}

//decide if we should display this entry
$validEntry = false;
if(is_array($entry) &&
    isset($entry['status']) && $entry['status']=='active' &&
    isset($entry[303]) && $entry[303]=='Accepted'){
  $validEntry = true; //display the entry
}

//check flags
foreach($entry as $key=>$field ) {
  $pos = strpos($key, '304.');
  if ($pos !== false) {
    if($field=='no-public-view')  $validEntry = false;
  }
}

// Website button
$website = '';


if (!empty($project_website)) {
  $website =  '<a href="' . $project_website . '" class="btn btn-cyan" target="_blank">Project Website</a>';
}


// Project Inline video
$video = '';

if (!empty($project_video)) {
  $dispVideo = str_replace('//vimeo.com','//player.vimeo.com/video',$project_video);
  //youtube has two type of url formats we need to look for and change
  $videoID = parse_yturl($dispVideo);
  if($videoID!=false){
    $dispVideo = 'https://www.youtube.com/embed/'.$videoID;
  }
  $video = '<div class="entry-video">
              <div class="embed-youtube">
                <iframe src="' . $dispVideo . '" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
              </div>
            </div>';
}

//decide if display maker info
$dispMakerInfo = true;
if($formType=='Sponsor' || $formType == 'Startup Sponsor'){
  $dispMakerInfo = false;
}


//build the html for the page
/* JS used only if the person visiting this page can edit the information on it */
?>
<div class="clear"></div>

<div class="container entry-page">
  <div class="row">
    <div class="content col-xs-12 entry-page-mobie-flex">
      <div class="backlink"><a href="<?php echo $backlink;?>"><?php echo $backMsg;?></a></div>
      <?php
      if($makerEdit){ ?>
        <div class="makerEditHead">
          <input type="hidden" id="entry_id" value="<?php echo $entryId;?>" />
          <a class="pull-left" target="_blank" href="/maker-sign/<?php echo $entryId?>/<?php echo $faireShort;?>/">
            <i class="fa fa-file-image-o" aria-hidden="true"></i>View Your Maker Sign
          </a>
          <button class="pull-right" type="button" onClick="showEdit()">Edit Public information</button>
        </div>
      <?php
      }
      if($validEntry) {
        //display the normal entry public information page
        include TEMPLATEPATH.'/pages/page-entry-view.php';
        if($makerEdit) {
          //use the edit entry public info page
          include TEMPLATEPATH.'/pages/page-entry-edit.php';
        }
      } else { //entry is not active
        echo '<h2>Invalid entry</h2>';
      }
      ?>
    </div><!--col-xs-12-->
  </div><!--row-->
</div><!--container-->

 <?php get_footer();

function display_entry_schedule($entry_id) {
  global $wpdb; global $faireID; global $faire; global $show_sched; global $backMsg; global $url_sub_path;
  global $faire_map; global $program_guide;

  if(!$show_sched){
    return;
  }
  $backlink = "/".$url_sub_path."/meet-the-makers/";

  $faire_url = "/$faire";

  $sql = "select location.entry_id, area.area, subarea.subarea, subarea.nicename, location.location, schedule.start_dt, schedule.end_dt
            from  wp_mf_location location
            join  wp_mf_faire_subarea subarea
                            ON  location.subarea_id = subarea.ID
            join wp_mf_faire_area area
                            ON subarea.area_id = area.ID
            left join wp_mf_schedule schedule
                    on location.ID = schedule.location_id
             where location.entry_id=$entry_id"
          . " group by area, subarea, location, schedule.start_dt"
          . " order by schedule.start_dt";
  $results = $wpdb->get_results($sql);

  if($wpdb->num_rows > 0){
    ?>
    <span class="faireTitle">
      <span class="faireLabel">Live at</span>
      <br/>
      <a href="<?= $backlink ?>">
        <div class="faireName"><?php echo ucwords(str_replace('-',' ', $faire));?></div>
      </a>
    </span>
    <div id="entry-schedule">
      <?php // TBD - dynamically set these links and images ?>
      <div class="faireActions">

        <?php if($faire_map!='') { ?>
        <a class="flagship-icon-link" href="<?php echo $faire_map;?>">
          <span class="fa-stack fa-lg">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-map-marker fa-stack-1x fa-inverse"></i>
          </span>
          <h4>Event Map</h4>
        </a>
        <?php } ?>

        <a class="flagship-icon-link" href="/<?php echo $url_sub_path;?>/schedule/">
          <span class="fa-stack fa-lg">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-calendar fa-stack-1x fa-inverse"></i>
          </span>
          <h4>View full schedule</h4>
        </a>

        <?php if($program_guide != '') { ?>
        <a class="flagship-icon-link" href="<?php echo $program_guide;?>">
          <span class="fa-stack fa-lg">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-download fa-stack-1x fa-inverse"></i>
          </span>
          <h4>Download the program guide</h4>
        </a>
        <?php } ?>
      </div>

      <div class="clearfix"></div>
      <div class="row padbottom">
        <?php

        $prev_start_dt = NULL;
        $prev_location = NULL;
        foreach($results as $row){
          if(!is_null($row->start_dt)){
            $start_dt   = strtotime( $row->start_dt);
            $end_dt     = strtotime($row->end_dt);
            $current_start_dt = date("l, F j",$start_dt);
            $current_location = $row->area.' in '.($row->nicename!=''?$row->nicename:$row->subarea);

            if($prev_start_dt==NULL){
              echo '<div class="entry-date-time col-sm-4">';
            }
            if ($prev_start_dt != $current_start_dt){
              //This is not the first new date
              if ($prev_start_dt != NULL){
                echo '</div><div class="entry-date-time col-sm-4">';
              }
              echo '<h5>'.$current_start_dt.'</h5>';
              $prev_start_dt = $current_start_dt;
              $prev_location = null;
            }
            // this is a new location
            if ($prev_location != $current_location){
              $prev_location = $current_location;
              echo '<p><small class="text-muted">LOCATION:</small> '.$current_location.'</p>';
            }
            echo ' <p><small class="text-muted">TIME:</small> '. date("g:i a",$start_dt).' - '.date("g:i a",$end_dt).'</p>';
          }else{
              global $faire_start; global $faire_end;
              echo '<div class="entry-date-time col-sm-12">';


              $faire_start = strtotime($faire_start);
              $faire_end   = strtotime($faire_end);
              $dateRange   = progDateRange($faire_start, $faire_end);

              //tbd change this to be dynamically populated
              echo '<h5>'.natural_language_join($dateRange).': '.date("F j",$faire_start).'-' . date("j",$faire_end).'</h5>';
              echo '<p><small class="text-muted">LOCATION:</small> '.$row->area.' in '.($row->nicename!=''?$row->nicename:$row->subarea).'</p>';
              echo '</div>';
          }
        }
        ?>
          </div><!-- close final col-sm-4-->
        </div><!-- end row-->
    </div><!-- End entry-schedule-->
    <?php
  }
}

/* This function is used to display grouped entries and links*/
function display_groupEntries($entryID){
  global $wpdb;global $faireID; global $faire;
  $return = '';

  $sql = "select * from wp_rg_lead_rel where parentID=".$entryID." or childID=".$entryID;
  $results = $wpdb->get_results($sql);
  if($wpdb->num_rows > 0){
    if($results[0]->parentID==$entryID){
        $title = 'Exhibits in this group:';
        $type = 'parent';
      }else{
        $title = 'Part of a group:';
        $type = 'child';
      }
    $return .= $title.'<br/>';
    $return .= '<div class="row">';
    foreach($results as $row){
      $link_entryID = ($type=='parent'?$row->childID:$row->parentID);
      $entry = GFAPI::get_entry($link_entryID);
      //Title
      $project_title = (string)$entry['151'];
      $project_title  = preg_replace('/\v+|\\\[rn]/','<br/>',$project_title);
      $return .= '<div class="col-md-4 col-sm-6">';
      $return .= '<a href="/maker/entry/'.$link_entryID.'">'.$project_title.'</a></div>';
    }
    $return .= '</div>';
  }
  echo $return;
}

//return makers info
function getMakerInfo($entry) {
  $makers = array();
  if (isset($entry['160.3']))
    $makers[1] = array('firstname' => $entry['160.3'], 'lastname' => $entry['160.6'],
                      'bio'       => (isset($entry['234']) ? $entry['234']: ''),
                      'photo'     => (isset($entry['217']) ? $entry['217'] : '')
                );
  if (isset($entry['158.3']))
    $makers[2] = array('firstname' => $entry['158.3'], 'lastname' => $entry['158.6'],
                      'bio'       => (isset($entry['258']) ? $entry['258'] : ''),
                      'photo'     => (isset($entry['224']) ? $entry['224'] : '')
                );
  if (isset($entry['155.3']))
      $makers[3] = array('firstname' => $entry['155.3'], 'lastname' => $entry['155.6'],
                      'bio'         => (isset($entry['259']) ? $entry['259'] : ''),
                      'photo'       => (isset($entry['223']) ? $entry['223'] : '')
                );
  if (isset($entry['156.3']))
      $makers[4] = array('firstname' => $entry['156.3'], 'lastname' => $entry['156.6'],
                      'bio'         => (isset($entry['260']) ? $entry['260'] : ''),
                      'photo'       => (isset($entry['222']) ? $entry['222'] : '')
                  );
  if (isset($entry['157.3']))
      $makers[5] = array('firstname' => $entry['157.3'], 'lastname' => $entry['157.6'],
                      'bio'         => (isset($entry['261']) ? $entry['261'] : ''),
                      'photo'       => (isset($entry['220']) ? $entry['220'] : '')
                  );
  if (isset($entry['159.3']))
      $makers[6] = array('firstname' => $entry['159.3'], 'lastname' => $entry['159.6'],
                      'bio'         => (isset($entry['262']) ? $entry['262'] : ''),
                      'photo'       => (isset($entry['221']) ? $entry['221'] : '')
                  );
  if (isset($entry['154.3']))
      $makers[7] = array('firstname' => $entry['154.3'], 'lastname' => $entry['154.6'],
                      'bio'         => (isset($entry['263']) ? $entry['263'] : ''),
                      'photo'       => (isset($entry['219']) ? $entry['219'] : '')
                  );
  return $makers;
}

function progDateRange($faire_start, $faire_end) {
    $dates = array();
    $flexDate = $faire_start;
    while ($flexDate <= $faire_end){
      $dates[] = date("l",$flexDate);
      $flexDate = strtotime('+1 day', $flexDate);
    }
    return $dates;
}

function natural_language_join(array $list, $conjunction = 'and') {
  $last = array_pop($list);
  if ($list) {
    return implode(', ', $list) . ' ' . $conjunction . ' ' . $last;
  }
  return $last;
}

function entryPageSubmit($entryId) {
  //get submitted data
  $form_id  = filter_input(INPUT_POST, 'form_id',FILTER_SANITIZE_NUMBER_INT);
  $form     = GFAPI::get_form($form_id);

  foreach($_POST as $inputField=>$value){
    $pos = strpos($inputField, 'input_');
    if ($pos !== false) {
      $fieldID = str_replace ( 'input_', '' , $inputField);
      $fieldID = str_replace ( '_', '.' , $fieldID);

      updateFieldValue($fieldID,$value,$entryId);
    }
  }

  //update maker table information
  GFRMTHELPER::updateMakerTables($entryId);

}

function updateFieldValue($fieldID,$newValue,$entryId) {
  global $fieldData; global $entry; global $form;
  $fieldInfo = $fieldData[(int) $fieldID];

  $fieldLabel = $fieldInfo['label'];

  //set who is updating the record
  $current_user = wp_get_current_user();
  $user = $current_user->ID;

  $form_id = $entry['form_id'];
  $chgRPTins = array();

  $entry_id = $entry['id'];

  if($fieldInfo->type=='fileupload'){
    $field = GFFormsModel::get_field( $form, $fieldNum );
    $input_name = 'input_' . str_replace( '.', '_', $fieldID );

    //validate uploaded file
    $field->validate( $_FILES[$input_name], $form);
    if($field->failed_validation){
      echo $field->validation_message;
      echo 'failed';
      return;
    }
    $newValue = $field->upload_file($form_id, $_FILES[$input_name]);

    //trigger cron job to correct image orientation if needed
    triggerCronImg($entry, $form) ;

    //trigger update
    gf_do_action( array( 'gform_after_update_entry', $form_id), $form, $entry_id, $entry );

    //for security reason, we force to remove all uploaded file
    //@unlink($_FILES['value']);
  }else{
    $fieldValue = (isset($entry[$fieldID])?$entry[$fieldID]:'');
  }

  //update field and change report if needed
  if($fieldValue != $newValue){
    GFAPI::update_entry_field( $entry_id, $fieldID, $newValue);
    $chgRPTins[] = RMTchangeArray($user, $entryId, $form_id, $fieldID, $fieldValue, $newValue, $fieldLabel);
    updateChangeRPT($chgRPTins);
    $entry[$fieldID] = $newValue;
  }
}