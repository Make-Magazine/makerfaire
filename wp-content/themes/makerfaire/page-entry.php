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
    $backMsg = '<i class="fa fa-angle-left fa-lg" aria-hidden="true"></i> Back to Your Maker Admin Tool';
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
  if($makerEdit){
    $website =  'Website: <div id="website" class="mfEdit">'. $project_website.'</div>';
  }else{
    $website =  '<a href="' . $project_website . '" class="btn btn-cyan" target="_blank">Project Website</a>';
  }
}

// Project Inline video
$video = '';
if (!empty($project_video)) {
  if($makerEdit) {
    $video = 'Video: <span id="video" class="mfEdit">'. $project_video.'</span>';
  } else {
    $dispVideo = str_replace('//vimeo.com','//player.vimeo.com/video',$project_video);
    //youtube has two type of url formats we need to look for and change
    $videoID = parse_yturl($dispVideo);
    if($videoID!=''){
      $dispVideo = 'https://www.youtube.com/embed/'.$videoID;
    }
    $video =  '<div class="entry-video">
                <div class="embed-youtube">
                  <iframe src="' . $dispVideo . '" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                </div>
              </div>';
  }
}

//decide if display maker info
$dispMakerInfo = true;
if($formType=='Sponsor' || $formType == 'Startup Sponsor'){
  $dispMakerInfo = false;
}


//build the html for the page
/* JS used only if the person visiting this page can edit the information on it */

if($makerEdit) {
  ?>
  <script type="text/javascript" src="/wp-content/themes/makerfaire/MAT/jeditable/jquery.jeditable.js"></script>
  <script type="text/javascript" src="/wp-content/themes/makerfaire/MAT/jeditable/jquery.jeditable.autogrow.js"></script>
  <script type="text/javascript" src="/wp-content/themes/makerfaire/MAT/jeditable/jquery.jeditable.ajaxupload.js"></script>

  <script type="text/javascript" src="/wp-content/themes/makerfaire/MAT/js/jquery.autogrow.js"></script>
  <script type="text/javascript" src="/wp-content/themes/makerfaire/MAT/js/jquery.ajaxfileupload.js"></script>
  <script type="text/javascript" src="/wp-content/themes/makerfaire/MAT/js/jeditable-main.js"></script>
  <?php
}
  ?>

<div class="clear"></div>

<div class="container entry-page">
  <div class="row">
    <div class="content col-xs-12 entry-page-mobie-flex">
        <div class="backlink"><a href="<?php echo $backlink;?>"><?php echo $backMsg;?></a></div>
        <?php
        if($makerEdit){?>
          <div class="makerEditHead">
            <input type="hidden" id="entry_id" value="<?php echo $entryId;?>" />
            <a target="_blank" href="/maker-sign/<?php echo $entryId?>/<?php echo $faireShort;?>/">
              <i class="fa fa-file-image-o" aria-hidden="true"></i>View Your Maker Sign
            </a>
            <br/>
            To modify your public information, click on the section you'd like to change below.
          </div>
        <?php
        }
      if($validEntry) {
        //display schedule/location information if there is any (do not display schedule if maker edit)
        if (!$makerEdit && !empty(display_entry_schedule($entryId))) {
          display_entry_schedule($entryId);
        }
        ?>
        <!-- Project Title and ribbons -->
        <div class="page-header">
          <h1>
            <span id="project_title" class="<?php echo ($makerEdit?'mfEdit':'')?>"><?php echo $project_title; ?></span>

            <?php echo $ribbons;?>
          </h1>
        </div>

        <!-- Project Image -->
        <p class="<?php echo ($makerEdit?'mfEditUpload':'')?>" id="proj_img" title="Click to upload...">
          <img class="img-responsive dispPhoto" src="<?php echo $project_photo; ?>" />
        </p>

        <!-- Project Short Description -->
        <p id="project_short" class="lead <?php echo ($makerEdit?' mfEdit_area':'')?>"><?php echo nl2br(make_clickable($project_short)); ?></p>

        <?php
        echo $website;  //project Website
        echo $video;    //project Video
        ?>

        <!-- Maker Info -->
        <div class="entry-page-maker-info">
          <?php
          if($dispMakerInfo) { ?>
            <div class="page-header">
              <h2><?php echo ($isGroup ? 'Group' : $isList ? 'Makers':'Maker');?></h2>
            </div>

            <?php
            if ($isGroup) {
              echo '<div class="row padbottom">
                      <div class="col-sm-3 '. ($makerEdit?'mfEditUpload':'').'" id="groupphoto" title="Click to upload...">
                        <div class="entry-page-maker-img">' .
                          (!empty($groupphoto) ? '<img class="img-responsive" src="' . legacy_get_fit_remote_image_url($groupphoto,400,400) . '" alt="Maker group photo" />' : '<img class="img-responsive" src="' . get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg" alt="Maker group placeholder photo" />') . '
                        </div>
                      </div>
                      <div class="col-sm-9 col-lg-7">
                        <h3 class="text-capitalize '. ($makerEdit?'mfEdit ':'').'" id="groupname">' . $groupname . '</h3>
                        <p class="'. ($makerEdit?'mfEdit_area':'').'" id="groupbio">' . make_clickable($groupbio) . '</p>
                      </div>
                    </div>';
            } else {
              foreach($makers as $key=>$maker) {
                if($maker['firstname'] !=''){
                  echo '<div class="row padbottom">
                          <div class="col-sm-3 '. ($makerEdit?'mfEditUpload':'').'" id="maker'.$key.'img" title="Click to upload...">
                            <div class="entry-page-maker-img">' .
                              (!empty($maker['photo']) ? '<img class="img-responsive" src="' . legacy_get_fit_remote_image_url($maker['photo'],400,400) . '" alt="Maker photo" />' : '<img class="img-responsive" src="' . get_stylesheet_directory_uri() . '/images/maker-placeholder.jpg" alt="Maker placeholder photo" />') .'
                            </div>
                          </div>
                          <div class="col-sm-9 col-lg-7">
                            <h3>
                              <span class="text-capitalize '. ($makerEdit?'mfEdit':'').'" id="maker'.$key.'fname">'.$maker['firstname'] . '</span>
                              <span class="text-capitalize '. ($makerEdit?'mfEdit':'').'" id="maker'.$key.'lname">'.$maker['lastname'] . '</span>
                            </h3>
                            <p class="'. ($makerEdit?'mfEdit_area':'').'" id="maker'.$key.'bio">' . make_clickable($maker['bio']) . '</p>
                          </div>
                        </div>';
                }
              }
            }
          } ?>
        </div>
        <?php
        echo display_groupEntries($entryId);
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
                            ON subarea.area_id = area.ID and area.faire_id = $faireID
            left join wp_mf_schedule schedule
                    on location.ID = schedule.location_id
             where location.entry_id=$entry_id"
          . " group by area, subarea, location";
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

      <div class="entry-date-time">
        <?php
        foreach($results as $row){
          if(!is_null($row->start_dt)){
            $start_dt   = strtotime( $row->start_dt);
            $end_dt     = strtotime($row->end_dt);
            echo '<h5>'.date("l, F j",$start_dt).'</h5>'
              . ' <p><small class="text-muted">TIME:</small> '. date("g:i a",$start_dt).' - '.date("g:i a",$end_dt).'</p>';
          }else{
            global $faire_start; global $faire_end;

            $faire_start = strtotime($faire_start);
            $faire_end   = strtotime($faire_end);

            //tbd change this to be dynamically populated
            echo '<h5>Friday, Saturday and Sunday: '.date("F j",$faire_start).'-' . date("j",$faire_end).'</h5>';
          }
          echo '<p><small class="text-muted">LOCATION:</small> '.$row->area.' in '.($row->nicename!=''?$row->nicename:$row->subarea).'</p>';

        }
        ?>
      </div>
    </div>
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
?>